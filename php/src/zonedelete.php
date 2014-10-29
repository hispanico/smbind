<?php
if(!function_exists("is_admin")) { include("include.php"); }

if(is_admin()) {
	if(filter("num", $_GET['i'])) {
		$smarty->assign("pagetitle", "Delete zone");
		$res = $dbconnect->query("SELECT name " .
					 "FROM zones " .
					 "WHERE id = " . $_GET['i']
				   );
		is_error($res);
		// get the name of the zone, to delete zonefile
		$zonename = current($res->fetchRow(0));
		$smarty->assign("zone", $zonename);
		$res = $dbconnect->query("DELETE FROM zones " .
					 "WHERE id = " . $_GET['i']
				   );
		is_error($res);
		$res = $dbconnect->query("UPDATE zones " .
					 "SET updated = 'yes' " .
					 "WHERE id IN (SELECT id FROM zones LIMIT 1)"
				   );
		is_error($res);
		$res = $dbconnect->query("DELETE FROM records " .
					 "WHERE zone = " . $_GET['i']
				   );
		is_error($res);
		// delete zonefile
		// FIXME: zonefile is always delete, even rights are 600 and file is ownwd by root
		if (!unlink($_CONF['path'] . preg_replace("/\.$/", "",$zonename))) {
		    $smarty->assign("pagetitle", "Ooops!");
		    $smarty->assign("reason", reason("filenotdelete"));
		    $smarty->assign("template", "accessdenied.tpl");
		    $smarty->assign("help", help("accessdenied"));
		    $smarty->assign("menu_button", menu_buttons());
		    $smarty->display("main.tpl");
		    die();
	}
	else {  // Bad input from user.
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

?>
