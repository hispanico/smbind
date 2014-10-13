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
		$smarty->assign("zone", current($res->fetchRow(0)));
		$res = $dbconnect->query("DELETE FROM zones " .
					 "WHERE id = " . $_GET['i']
				   );
		is_error($res);
		$res = $dbconnect->query("UPDATE zones " .
					 "SET updated = 'yes'" .
					 "LIMIT 1"
				   );
		is_error($res);
		$res = $dbconnect->query("DELETE FROM records " .
					 "WHERE zone = " . $_GET['i']
				   );
		is_error($res);
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
