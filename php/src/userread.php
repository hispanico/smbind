<?php
if(!function_exists("is_admin")) { include("include.php"); }

if(is_admin()) {
	if(filter("num", $_GET['i'])) {
		$smarty->assign("pagetitle", "Viewing user");
		$smarty->assign("admin_array", array("yes", "no"));
		$res = $dbconnect->query("SELECT id, username, admin " .
					 "FROM users " .
					 "WHERE id = " . $_GET['i']
				   );
		is_error($res);
		$smarty->assign("user", $res->fetchRow(DB_FETCHMODE_ASSOC));
		$smarty->assign("zonelist", sql_query("SELECT id, name, serial " .
						      "FROM zones " .
						      "WHERE owner = " . $_GET['i'])
					    );
		$smarty->assign("template", "userread.tpl");
		$smarty->assign("help", help("userread"));
		$smarty->assign("menu_button", menu_buttons());
		$smarty->display("main.tpl");
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
