<?php
if(!function_exists("is_admin")) { include("include.php"); }

$_SESSION = array();
session_destroy();
$smarty->assign("menu_button", array());
$smarty->assign("pagetitle", "Logout");
$smarty->assign("template", "logout.tpl");
$smarty->assign("help", help("logout"));
$smarty->display("main.tpl");
?>
