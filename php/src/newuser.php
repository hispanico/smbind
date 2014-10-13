<?php
if(!function_exists("is_admin")) { include("include.php"); }

if(is_admin()) {
	$smarty->assign("pagetitle", "New user");
	$smarty->assign("admin_array", array("yes", "no"));
	$smarty->assign("template", "newuser.tpl");
	$smarty->assign("help", help("newuser"));
	$smarty->assign("menu_button", menu_buttons());
	$smarty->display("main.tpl");
}
else {
	// The user is not an administrator.
	notadmin($smarty);
}
?>
