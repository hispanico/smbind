<?php
if(!function_exists("is_admin")) { include("include.php"); }

if((filter("num", $_GET['i'])) &&
   (filter("num", $_POST['total'])) &&
   (filter("num", $_POST['refresh'])) &&
   (filter("num", $_POST['retry'])) &&
   (filter("num", $_POST['expire'])) &&
   (filter("num", $_POST['ttl'])) &&
   (filter("alphanum", $_POST['pri_dns'])) &&
   (filter("alphanum", $_POST['sec_dns']))) {
	if(!owner($_GET['i'])) {
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
			if(($_POST['type'][$x] == "MX") && ($_POST['pri'][$x] == 0)) {
				$_POST['pri'][$x] = 10;
			}
			elseif($_POST['type'][$x] != "MX") {
				$_POST['pri'][$x] = 0;
			}
			if(! $_POST['host'][$x]) {
				$_POST['host'][$x] = "@";
			}
			if(! $_POST['destination'][$x]) {
				$_POST['destination'][$x] = "@";
			}
			$res = $dbconnect->query("UPDATE records " .
						 "SET host = '" . $_POST['host'][$x] . "', " .
						     "type = '" . $_POST['type'][$x] . "', " .
						     "pri = " . $_POST['pri'][$x] . ", " .
						     "destination = '" . preg_replace("/\.$/", "", $_POST['destination'][$x]) . "', " .
						     "valid = 'unknown' " .
						 "WHERE id = " . $_POST['host_id'][$x] . " " .
						 "AND zone = " . $_GET['i']
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
		if(! $_POST['newhost']) {
			$_POST['newhost'] = "@";
		}
		elseif(! $_POST['newdestination']) {
			$_POST['newdestination'] = "@";
		}
		if(! $_POST['newtype'] == "MX") {
			$res = $dbconnect->query("INSERT INTO records " .
							"(zone, host, type, destination) " .
						 "VALUES(" . $_POST['zoneid'] . ", '" .
							     $_POST['newhost'] . "', '" .
							     $_POST['newtype'] . "', '" .
							     preg_replace("/\.$/", "", $_POST['newdestination']) . "')"
					   );
		is_error($res);
		}
		else {
			$res = $dbconnect->query("INSERT INTO records " .
							"(zone, host, type, pri, destination) " .
						 "VALUES(" . $_POST['zoneid'] . ", '" .
							     $_POST['newhost'] . "', '" .
							     $_POST['newtype'] . "', " .
							     "10, '" .
							     preg_replace("/\.$/", "", $_POST['newdestination']) . "')"
					   );
		is_error($res);
		}
	
	}
	$res = $dbconnect->query("SELECT serial " .
				 "FROM zones " .
				 "WHERE id = " . $_POST['zoneid']
				);
	is_error($res);

	// Serial fixes
	$old_serial = current($res->fetchRow(0));
	$serial = date("Ymd") . substr($old_serial + 1, -2);
	if($serial < $old_serial) {
		$serial = $old_serial + 1;
	}
	$serial = "serial = '" . $serial . "' ";

	// Owner fixes
	if(isset($_POST['owner'])) {
		$owner = "owner = '" . $_POST['owner'] . "', ";
	}
	else {
		$owner = '';
	}

	$res = $dbconnect->query("UPDATE zones " .
				 "SET updated = 'yes', " .
				     "refresh = " . $_POST['refresh'] . ", " .
				     "retry = " . $_POST['retry'] . ", " .
				     "expire = " . $_POST['expire'] . ", " .
				     "ttl = " . $_POST['ttl'] . ", " .
				     "pri_dns = '" . preg_replace("/\.$/", "", $_POST['pri_dns']) . "', " .
				     "sec_dns = '" . preg_replace("/\.$/", "", $_POST['sec_dns']) . "', " .
				     $owner .
				     $serial .
				 "WHERE id = " . $_GET['i']
				);
	is_error($res);
}

?>
