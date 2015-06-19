<?php
if(!function_exists("is_admin")) { include("include.php"); }

$rebuildq = sql_query("SELECT flagvalue FROM flags WHERE flagname = 'rebuild_zones'");
$rebuild = $rebuildq[0]['flagvalue'];
$zone_error = false;

$zoneres = $dbconnect->query("SELECT * FROM zones WHERE updated = 'yes' AND deleted != 'yes'");
is_error($zoneres);

while($zone = $zoneres->fetchrow(DB_FETCHMODE_ASSOC)) {
	$recordres = $dbconnect->query("SELECT * FROM records " .
				"WHERE zone = " . $zone['id'] . " AND valid != 'no' " .
				"ORDER BY host, type, pri, destination");
	is_error($recordres);
	$out =
"\$TTL   " . $zone['ttl'] . "
@       IN      SOA     " . $zone['pri_dns'] . ". " . $_CONF['hostmaster'] . ". (
			" . $zone['serial'] . " \t; Serial
			" . $zone['refresh'] . " \t\t; Refresh
			" . $zone['retry'] . " \t\t; Retry
			" . $zone['expire'] . " \t; Expire
			" . $zone['nttl'] . ")\t\t; Negative Cache TTL
;\n" ;
	if ($zone['pri_dns'] != '') {
		$out .= "@\t" . $zone['ns_ttl'] ."\tNS\t\t" . $zone['pri_dns'] . ".\n";
	}
	if ($zone['sec_dns'] != '') {
		$out .= "@\t" . $zone['ns_ttl'] ."\tNS\t\t" . $zone['sec_dns'] . ".\n";
	}
	if ($zone['ter_dns'] != '') {
		$out .= "@\t" . $zone['ns_ttl'] ."\tNS\t\t" . $zone['ter_dns'] . ".\n";
	}
	$fd = fopen($_CONF['path'] . preg_replace('/\//','-',$zone['name']), "w")
		or die("Cannot open: " . $_CONF['path'] . preg_replace('/\//','-',$zone['name']));
	fwrite($fd, $out);
	fclose($fd);

	while($record = $recordres->fetchrow(DB_FETCHMODE_ASSOC)) {
		if($record['type'] == "MX" || $record['type'] == "SRV") {
			$pri = $record['pri'];
		}
		else {
			$pri = "";
		}
		if ($record['type'] == 'SRV') {
			$pri .= ' '.$record['num1'].' '.$record['num2'].' ';
		}
		if(
			($record['type'] == "NS" ||
			 $record['type'] == "PTR" ||
			 $record['type'] == "CNAME" ||
			 $record['type'] == "MX" ||
			 $record['type'] == "SRV") &&
			($record['destination'] != "@")) 	{
			$destination = $record['destination'] . ".";
		}
		elseif($record['type'] == "TXT") {
			$txt = preg_replace('/\\\/', '\\\\\\\\', $record['txt']);
			$txt = preg_replace('/"/', '\\"', $txt);
			$txt = implode("\"\n\t\t\t\t\t\"", str_split($txt, 255));
			$destination = "(\"" . $txt . "\")";
		}
		else {
			$destination = $record['destination'];
		}
		$out = $record['host'] . "\t" . (is_null($record['ttl']) ? '' : $record['ttl']) .
			"\t" . $record['type'] . "\t" . $pri . "\t" . $destination . "\n";
		$fd = fopen($_CONF['path'] . preg_replace('/\//','-',$zone['name']), "a");
		fwrite($fd, $out);
		fclose($fd);
		$testres = $dbconnect->query("UPDATE records SET valid = 'yes' " .
					"WHERE id = " . $record['id']);
		is_error($testres);
	}
	$cmd = $_CONF['namedcheckzone'] . " " . $zone['name'] . " " . $_CONF['path'] .
		preg_replace('/\//','-',$zone['name']);
	exec($cmd, $output, $exit);
	if ($exit == 0) {
		$updateres = $dbconnect->query("UPDATE zones SET updated = 'no', valid = 'yes' " .
						"WHERE id = " . $zone['id']);
		is_error($updateres);
		if (!$rebuild) {
			$cmd = $_CONF['rndc'] . " reload " . $zone['name'] . "> /dev/null";
			system($cmd, $exit);
			if ($exit != 0) { die("$cmd : exit status " . $exit); }
		}
	}
	else {
		$zone_error = true;
		$updateres = $dbconnect->query("UPDATE zones SET updated = 'yes', valid = 'no' " .
						"WHERE id = " . $zone['id']);
		is_error($updateres);
	}
}

$slave_message = '';
if ($rebuild && !$zone_error) {
	$zones =& $dbconnect->getAll("SELECT name FROM zones WHERE deleted != 'yes' ORDER BY name", null, DB_FETCHMODE_ORDERED);
	is_error($zones);

	$fd = fopen($_CONF['conf'],"w");
	foreach ($zones as $zone) {
		fwrite($fd, "zone \"" . $zone[0] . "\" {
			type master;
			file \"" . $_CONF['path'] . preg_replace('/\//','-',$zone[0]) . "\";
		};\n\n");
	}
	fclose($fd);

	$cmd = $_CONF['namedcheckconf'] . " " . $_CONF['conf'] . " > /dev/null";
	system($cmd, $exit);
	if ($exit != 0) { die($_CONF['namedcheckconf'] . " exit status " . $exit); }

	#$dbconnect->disconnect();

	$cmd = $_CONF['rndc'] . " reload > /dev/null";
	system($cmd, $exit);
	if ($exit != 0) { die($_CONF['rndc'] . " exit status " . $exit); }

	$slave_message = '';
	$slaves_rebuilt = true;
	foreach ($_CONF['slaves'] as $master_addr => $slaves) {
		$fd = fopen($_CONF['conf_slave'],"w");
		foreach ($zones as $zone) {
			fwrite($fd, "zone \"" . $zone[0] . "\" {\n" .
				"\ttype slave;\n" .
				"\tfile \"" . $_CONF['path'] . preg_replace('/\//','-',$zone[0]) . "\";\n" .
				"\tmasters { $master_addr; };\n" .
				"};\n"
			);
		}
		fwrite($fd, $cout);
		fclose($fd);
		foreach ($slaves as $slave) {
			system($_CONF['ssh'] .
				' -oConnectTimeout=' . $_CONF['ssh_connect_timeout'] .
				' -i ' . $_CONF['slave_ssh_key'] . ' ' .
				$_CONF['slave_user'].'@'.$slave.' "cat > '. $_CONF['conf_slave'] .'; /usr/sbin/rndc reconfig" < '.$_CONF['conf_slave'], $exit);
			if ($exit == 0) {
				$slave_message .= "Updated DNS Slave $slave<br>\n";
			} else {
				$slave_message .= "ERROR: Updating DNS Slave $slave has failed<br>\n";
				$slaves_rebuilt = false;
			}
		}
	}
	if ($slaves_rebuilt) {
		$rebuildres = $dbconnect->query('update flags set flagvalue=0 where flagname="rebuild_zones"');
		is_error($rebuildres);
	}
}
if ($zone_error && $rebuild) {
	$slave_message = 'WARNING: Slave update skipped due to zone errors';
}

$zones =& $dbconnect->getAll("SELECT name FROM zones WHERE deleted = 'yes' ORDER BY name", null, DB_FETCHMODE_ORDERED);
is_error($zones);
foreach ($zones as $zone) {
	$zone_fname = $_CONF['path'] . preg_replace('/\//','-',$zone[0]);
	if (file_exists($zone_fname) && !unlink($zone_fname)) {
		$slave_message .= "WARNING: Deleting local zone file for " . $zone[0] . " has failed.<br>\n";
	}
}

function delete_zone_files_from_slave($slave, $zones, &$slave_output) {
	global $_CONF;
	$descriptorspec = array(
	   0 => array("pipe", "r"),
	   1 => array("pipe", "w"),
	   2 => array("pipe", "w")
	);

	$pipes = null;

	$timeout = $_CONF['ssh_activity_timeout'];

	$process = proc_open($_CONF['ssh'] .
		' -oConnectTimeout=' . $_CONF['ssh_connect_timeout'] .
		' -i ' . $_CONF['slave_ssh_key'] . ' ' .
		$_CONF['slave_user'].'@'.$slave. ' "xargs -0 rm -f"', $descriptorspec, $pipes);
	if (is_resource($process)) {
		foreach($pipes as &$pipe) {
			stream_set_blocking($pipe, 0);
		}
		$stdout = '';
		$stderr = '';
		$stdout_eof = false;
		$stderr_eof = false;
		$stdin_eof = false;
		$last_actvity = time();
		$error = false;
		while (1) {
			$read = array();
			if (!$stdout_eof) $read['stdout'] = $pipes[1];
			if (!$stderr_eof) $read['stderr'] = $pipes[2];
			$write = $stdin_eof ? null : array($pipes[0]);
			$expect = null;
			if (false === ($num_changed_streams = stream_select($read, $write, $except, 2))) {
				$error = true;
				break;
			} else {
				if (isset($read['stdout'])) {
					$buf = stream_get_contents($read['stdout']);
					if ($buf === false) {
						$stdout_eof = true;
						$error = true;
					} elseif ($buf === '') {
						$stdout_eof = true;
						fclose($read['stdout']);
					} else {
						$last_actvity = time();
						$stdout .= $buf;
					}
				}
				if (isset($read['stderr'])) {
					$buf = stream_get_contents($read['stderr']);
					if ($buf === false) {
						$stderr_eof = true;
						$error = true;
					} elseif ($buf === '') {
						$stderr_eof = true;
						fclose($read['stderr']);
					} else {
						$last_actvity = time();
						$stderr .= $buf;
					}
				}
				if (isset($write[0])) {
					$zone = array_shift($zones);
					if ($zone === null) {
						fclose($write[0]);
						$stdin_eof = true;
					} else {
						$wrtstr = $_CONF['path'] . preg_replace('/\//','-',$zone[0]) . "\0";
						$wrlen = strlen($wrtstr);
						$written = fwrite($write[0], $wrtstr);
						if ($written === false) {
							$stdin_eof = true;
							$error = true;
						} elseif ($written !== $wrlen) {
							$stdin_eof = true;
							$error = true;
						} else {
							$last_actvity = time();
						}
					}
				}
			}
			if ($stdout_eof && $stderr_eof && $stdin_eof) {
				break;
			}
			if ((time() - $last_actvity) > $timeout) {
				foreach($pipes as &$pipe) {
					if (is_resource($pipe)) fclose($pipe);
				}
				proc_terminate($process);
				break;
			}
		} //while

		// if ($error) {
			foreach($pipes as &$pipe) {
				if (is_resource($pipe)) fclose($pipe);
			}
		// }

		$return_value = proc_close($process);
		$slave_output = array($return_value, $stdout, $stderr);
		if ($return_value || $error) {
			return false;
		} else {
			return true;
		}
	} else {
		return false;
	}
}

if (count($zones)) {
	$zoneres = $dbconnect->query("SELECT id FROM zones WHERE deleted = 'yes'");
	is_error($zoneres);
	while($zone = $zoneres->fetchrow(DB_FETCHMODE_ORDERED)) {
		$res = $dbconnect->query("DELETE FROM records WHERE zone = ?",
			array($zone[0]));
		is_error($res);
	}

	$res = $dbconnect->query("DELETE FROM zones WHERE deleted = 'yes'");
	is_error($res);
	foreach ($_CONF['slaves'] as $master_addr => $slaves) {
		foreach ($slaves as $slave) {
			$slave_output = null;
			$slave_result = delete_zone_files_from_slave($slave, $zones, $slave_output);
			if (!$slave_result) {
				$slave_message .= "Error deleting zone files on slave $slave.<br>\n".
					"Exit code: " . $slave_output[0] . "<br>\n".
					"STDOUT: " . $slave_output[1] . "<br>\n".
					"STDERR: " . $slave_output[2] . "<br>\n";
			}
		}
	}
}

$smarty->assign("bad", bad_records($userid));
$smarty->assign("output", $output);
$smarty->assign("slave", $slave_message);
$smarty->assign("pagetitle", "Commit changes");
$smarty->assign("template", "commit.tpl");
$smarty->assign("help", help("commit"));
$smarty->assign("menu_button", menu_buttons());
$smarty->display("main.tpl");
?>
