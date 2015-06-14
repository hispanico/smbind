<?php
if(!function_exists("is_admin")) { include("include.php"); }

$rebuildq = sql_query("SELECT flagvalue FROM flags WHERE flagname = 'rebuild_zones'");
$rebuild = $rebuildq[0]['flagvalue'];
$zone_error = false;

$zoneres = $dbconnect->query("SELECT * FROM zones WHERE updated = 'yes'");
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
	$nsttl = ctype_digit($_CONF['ns_ttl']) ? "\t".$_CONF['ns_ttl'] : '';
	if ($zone['pri_dns'] != '') {
		$out .= '@' . $nsttl ."\tNS\t\t" . $zone['pri_dns'] . ".\n";
	}
	if ($zone['sec_dns'] != '') {
		$out .= '@' . $nsttl ."\tNS\t\t" . $zone['sec_dns'] . ".\n";
	}
	if ($zone['ter_dns'] != '') {
		$out .= '@' . $nsttl ."\tNS\t\t" . $zone['ter_dns'] . ".\n";
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
	$zones =& $dbconnect->getAll("SELECT name FROM zones ORDER BY name", null, DB_FETCHMODE_ORDERED);
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
			system('ssh -oConnectTimeout=4 -i ' . $_CONF['slave_ssh_key'] . ' ' .
				$_CONF['slave_user'].'@'.$slave.' "cat > '. $_CONF['conf_slave'] .'; /usr/sbin/rndc reconfig" < '.$_CONF['conf_slave'], $exit);
			if ($exit == 0) {
				$slave_message .= "Updated DNS Slave $slave<br>";
			} else {
				$slave_message .= "ERROR: Updating DNS Slave $slave has failed<br>";
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

$smarty->assign("bad", bad_records($userid));
$smarty->assign("output", $output);
$smarty->assign("slave", $slave_message);
$smarty->assign("pagetitle", "Commit changes");
$smarty->assign("template", "commit.tpl");
$smarty->assign("help", help("commit"));
$smarty->assign("menu_button", menu_buttons());
$smarty->display("main.tpl");
?>
