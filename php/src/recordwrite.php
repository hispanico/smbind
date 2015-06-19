<?php
if(!function_exists("is_admin")) { include("include.php"); }

if((filter("num", $_GET['i'])) &&
   (filter("num", $_POST['total'])) &&
   (filter("num", $_POST['refresh'])) &&
   (filter("num", $_POST['retry'])) &&
   (filter("num", $_POST['expire'])) &&
   (filter("num", $_POST['ttl'])) &&
   (filter("num", $_POST['nttl'])) &&
   (filter("num", $_POST['ns_ttl'])) &&
   (filter("alphanum", $_POST['pri_dns'])) &&
   (filter("alphanum", $_POST['sec_dns'])) &&
   (filter("alphanum", $_POST['ter_dns']))) {
	if(!owner($_GET['i']) || $_GET['i'] !== $_POST['zoneid']) {
		// The user doesn't own this zone.
		$smarty->assign("pagetitle", "Ooops!");
		$smarty->assign("reason", reason("notown"));
		$smarty->assign("template", "accessdenied.tpl");
		$smarty->assign("help", help("accessdenied"));
		$smarty->assign("menu_button", menu_buttons());
		$smarty->display("main.tpl");
		die();
	}
	$total = $_POST['total'];
	if($total != 0) {
		for($x = 0; $x < $total; $x++) {
			if(! strlen($_POST['host'][$x])) {
				$_POST['host'][$x] = "@";
			}
			if(! strlen($_POST['destination'][$x])) {
				$_POST['destination'][$x] = "@";
			}
			$destination = preg_replace("/\.$/", "", $_POST['destination'][$x]);
			$res = $dbconnect->query("UPDATE records " .
						 "SET host = ?, " .
							 "ttl = ?, " .
						     "type = ?, " .
						     "pri = ?, " .
						     "num1 = ?, " .
						     "num2 = ?, " .
						     "num3 = ?, " .
						     "destination = ?, " .
						     "txt = ?, " .
						     "valid = 'unknown' " .
						 "WHERE id = ? " .
						 "AND zone = ?",
						 array($_POST['host'][$x],
							strlen($_POST['rttl'][$x] > 0) ? $_POST['rttl'][$x] : null,
							$_POST['type'][$x], $_POST['pri'][$x],
							$_POST['num1'][$x],
							$_POST['num2'][$x],
							$_POST['num3'][$x],
							$destination,
							$_POST['type'][$x] == 'TXT' ? $_POST['txt'][$x] : null,
							$_POST['host_id'][$x], $_GET['i']
						)
					   );
			is_error($res);
			if(isset($_POST['delete'][$x])) {
				$res = $dbconnect->query("DELETE FROM records " .
							 "WHERE id = " . $_POST['host_id'][$x] . " " .
							 "AND zone = " . $_GET['i']
							);
			is_error($res);
			}
		}
	}
	if(($_POST['newhost']) || ($_POST['newdestination'])) {
		if(! strlen($_POST['newhost'])) {
			$_POST['newhost'] = "@";
		}
		if(! strlen($_POST['newdestination'])) {
			$_POST['newdestination'] = "@";
		}
		$res = $dbconnect->query("INSERT INTO records " .
			"(zone, host, ttl, type, destination, txt) " .
			"VALUES(?,?,?,?,?,?)",
			array($_POST['zoneid'], $_POST['newhost'],
				 strlen($_POST['newttl'] > 0) ? $_POST['newttl'] : null,
				 $_POST['newtype'],
				 $_POST['newtype'] == 'TXT' ? '' : preg_replace("/\.$/", "", $_POST['newdestination']),
				 $_POST['newtype'] == 'TXT' ? $_POST['newdestination'] : null)
			);
		is_error($res);
	
	}
	$res = $dbconnect->query("SELECT serial " .
				 "FROM zones " .
				 "WHERE id = " . $_POST['zoneid']
				);
	is_error($res);

	// Serial fixes
	$old_serial = current($res->fetchRow(0));
	$serial = date("Ymd00");
	if($serial <= $old_serial) {
		$serial = $old_serial + 1;
	}

	$res = $dbconnect->query("UPDATE zones " .
				 "SET updated = 'yes', " .
				     "refresh = ?, " .
				     "retry = ?, " .
				     "expire = ?, " .
				     "ttl = ?, " .
				     "nttl = ?, " .
				     "pri_dns = ?, " .
				     "sec_dns = ?, " .
				     "ter_dns = ?, " .
				     "ns_ttl = ?, " .
				     "serial = ?, " .
				     "comment = ?, " .
				     "notes = ? " .
				 "WHERE id = ?",
				 array($_POST['refresh'],
					$_POST['retry'],
					$_POST['expire'],
					$_POST['ttl'],
					$_POST['nttl'],
					preg_replace("/\.$/", "", $_POST['pri_dns']),
					preg_replace("/\.$/", "", $_POST['sec_dns']),
					preg_replace("/\.$/", "", $_POST['ter_dns']),
					$_POST['ns_ttl'],
					$serial,
					$_POST['comment'],
					$_POST['notes'],
					$_GET['i']
				)
			);
	is_error($res);
	
	if (is_admin()) {
		if(isset($_POST['owner'])) {
			$res = $dbconnect->query("UPDATE zones set owner = ? where id = ?",
				array($_POST['owner'], $_GET['i'])
			);
			is_error($res);
		}
	}
}

?>
