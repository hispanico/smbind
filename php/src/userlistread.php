<?php
if(!function_exists("is_admin")) { include("include.php"); }

if(is_admin()) {
	$smarty->assign("pagetitle", "Users");
	$smarty->assign("userlist", sql_query("SELECT id, username, admin " .
					      "FROM users " .
					      "ORDER BY username " .
					      limit())
				    );
	pages("SELECT id " .
	      "FROM users"
	);
	$smarty->assign("template", "userlistread.tpl");
	$smarty->assign("help", help("userlistread"));
	$smarty->assign("menu_button", menu_buttons());
	$smarty->assign("page_root", "./userlist.php?");
	$smarty->display("main.tpl");
}
else {
	// The user is not an administrator.
	notadmin($smarty);
}

?>
