<?php
if(!function_exists("is_admin")) { include("include.php"); }

if(is_admin())  {
	$smarty->assign("pagetitle", "New zone");
	$smarty->assign("userlist", sql_query("SELECT id, username " .
					      "FROM users " .
					      "ORDER BY username")
				    );
	$smarty->assign("current_user", $userid);
	$smarty->assign("pri_dns", $_CONF['pri_dns']);
	$smarty->assign("sec_dns", $_CONF['sec_dns']);
	$smarty->assign("template", "newzone.tpl");
	$smarty->assign("help", help("newzone"));
	$smarty->assign("menu_button", menu_buttons());
	$smarty->display("main.tpl");
}
else {
	// The user is not an administrator.
	notadmin($smarty);
}
?>
