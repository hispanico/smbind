<?php
if(!function_exists("is_admin")) { include("include.php"); }

if(is_admin()) {
	if((filter("alphanum", $_POST['name'], "no")) &&
	   (filter("num", $_POST['refresh'], "no")) &&
	   (filter("num", $_POST['retry'], "no")) &&
	   (filter("num", $_POST['expire'], "no")) &&
	   (filter("num", $_POST['ttl'], "no")) &&
	   (filter("num", $_POST['ns_ttl'], "no")) &&
	   (filter("alphanum", $_POST['pri_dns'])) &&
	   (filter("alphanum", $_POST['sec_dns'])) &&
	   (filter("alphanum", $_POST['ter_dns'])) &&
	   (filter("num", $_POST['www'])) &&
	   (filter("num", $_POST['mail'])) &&
	   (filter("num", $_POST['ftp']))) {
		$_POST['name'] = strtolower($_POST['name']);
		$res = $dbconnect->query("SELECT id " .
					 "FROM zones " .
					 "WHERE name = '" . preg_replace("/\.$/", "", $_POST['name']) . "'"
				   );
		is_error($res);
		if($res->numRows() != 0) {
			// The zone exists in the db.
			$smarty->assign("pagetitle", "Ooops!");
			$smarty->assign("reason", reason("existzone"));
			$smarty->assign("template", "accessdenied.tpl");
			$smarty->assign("help", help("accessdenied"));
			$smarty->assign("menu_button", menu_buttons());
			$smarty->display("main.tpl");
			die();
		}
		if(file_exists($_CONF['path'] . "/" . preg_replace("/\.$/", "", $_POST['name']))) {
			$smarty->assign("pagetitle", "Ooops!");
			$smarty->assign("reason", reason("existfile"));
			$smarty->assign("template", "accessdenied.tpl");
			$smarty->assign("help", help("accessdenied"));
			$smarty->assign("menu_button", menu_buttons());
			$smarty->display("main.tpl");
			die();
		}
		if($res->numRows() == 0) {
			$res = $dbconnect->query("INSERT INTO zones " .
							"(name, pri_dns, sec_dns, ter_dns, ns_ttl, " .
							"serial, refresh, retry, " .
							"expire, ttl, owner, " .
							"updated) " .
						 "VALUES (" .
							"'" . preg_replace("/\.$/", "", $_POST['name']) . "', " .
							"'" . preg_replace("/\.$/", "", $_POST['pri_dns']) . "', " .
							"'" . preg_replace("/\.$/", "", $_POST['sec_dns']) . "', " .
							"'" . preg_replace("/\.$/", "", $_POST['ter_dns']) . "', " .
							$_POST['ns_ttl'] . ", " .
							date("Ymd") . "00, " .
							$_POST['refresh'] . ", " .
							$_POST['retry'] . ", " .
							$_POST['expire'] . ", " .
							$_POST['ttl'] . ", " .
							$_POST['owner'] . ", " .
							"'yes')"
					   );
			is_error($res);

			$rebuildres = $dbconnect->query('update flags set flagvalue=1 where flagname="rebuild_zones"');
			is_error($rebuildres);

			$res = $dbconnect->query("SELECT id " .
						 "FROM zones " .
						 "WHERE name = '" . preg_replace("/\.$/", "", $_POST['name']) . "'"
					   );
			is_error($res);
			$new_zone_row = $res->fetchRow();
			$new_zone_id = $new_zone_row[0];

			// Handle default records: Get new zone id
			if(isset($_POST['www']) || isset($_POST['mail']) || isset($_POST['ftp'])) {
				$id = $dbconnect->getone("SELECT id " .
	 						 "FROM zones " .
					 		 "WHERE name = '" . preg_replace("/\.$/", "", $_POST['name']) . "'"
							 );
				is_error($res);
			
				// Handle default records: www
				if($_POST['www'] != '') {
					$res = $dbconnect->query("INSERT INTO records " .
		 				   		 "(zone, host, type, " .
						  		 "pri, destination) " .
						  		 "VALUES (" .
						  		 $id . ", " .
						  		 "'@', " .
						  		 "'A', " .
						 		 "0, " .
						  		 "'" . $_POST['www'] . "')"
								);
					is_error($res);
					$res = $dbconnect->query("INSERT INTO records " .
								 "(zone, host, type, " .
								 "pri, destination) " .
								 "VALUES (" .
								 $id . ", " .
								 "'www', " .
								 "'CNAME', " .
								 "0, " .
								 "'@')"
								);
					is_error($res);
				}

				// Handle default records: mail
				if($_POST['mail'] != '') {
					$res = $dbconnect->query("INSERT INTO records " .
								 "(zone, host, type, " .
								 "pri, destination) " .
								 "VALUES (" .
								 $id . ", " .
								 "'mail', " .
								 "'A', " .
								 "0, " .
								 "'" . $_POST['mail'] . "')"
								);
					is_error($res);
					$res = $dbconnect->query("INSERT INTO records " .
								 "(zone, host, type, " .
								 "pri, destination) " .
								 "VALUES (" .
								 $id . ", " .
								 "'@', " .
								 "'MX', " .
								 "10, " .
								 "'mail." . preg_replace("/\.$/", "", $_POST['name']) . "')"
								);
					is_error($res);
				}

				// Handle default records: ftp
				if($_POST['ftp'] != '') {
					$res = $dbconnect->query("INSERT INTO records " .
								 "(zone, host, type, " .
								 "pri, destination) " .
								 "VALUES (" .
								 $id . ", " .
								 "'ftp', " .
								 "'A', " .
								 "0, " .
								 "'" . $_POST['ftp'] . "')"
								);
					is_error($res);
				}
			}
		}
	}
	else {  // Bad input from user.
		if(! $_POST['name']) {
			$smarty->assign("reason", reason("nozonename"));
		}
		$smarty->assign("pagetitle", "Ooops!");
		$smarty->assign("template", "accessdenied.tpl");
		$smarty->assign("help", help("accessdenied"));
		$smarty->assign("menu_button", menu_buttons());
		$smarty->display("main.tpl");
		die();
	}
}
else {
	// The user is not an administrator.
	notadmin($smarty);
}

if ($new_zone_id) {
	$_GET['i'] = $new_zone_id;
	include("recordread.php");
}

?>
