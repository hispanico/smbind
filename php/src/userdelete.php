<?php
if(!function_exists("is_admin")) { include("include.php"); }

if(is_admin()) {
	if(filter("num", $_GET['i'])) {
		if($_GET['i'] != 1) {
			$smarty->assign("pagetitle", "Delete user");
			$res = $dbconnect->query("SELECT username " .
						 "FROM users " .
						 "WHERE id = " . $_GET['i']
						);
			is_error($res);
			$smarty->assign("user", current($res->fetchRow(0)));
			$res = $dbconnect->query("DELETE FROM users " .
						 "WHERE id = " . $_GET['i']
						);
			is_error($res);
			$res = $dbconnect->query("UPDATE zones " .
						 "SET owner = 1 " .
						 "WHERE owner = " . $_GET['i']
						);
			is_error($res);
		}
		else {
			$smarty->assign("pagetitle", "Ooops!");
			$smarty->assign("reason", reason("deleteadmin"));
			$smarty->assign("template", "accessdenied.tpl");
			$smarty->assign("help", help("accessdenied"));
			$smarty->assign("menu_button", menu_buttons());
			$smarty->display("main.tpl");
		}
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
