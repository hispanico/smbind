<?php
if(!function_exists("is_admin")) { include("include.php"); }

$smarty->assign("pagetitle", "Change password");
$smarty->assign("template", "chpass.tpl");
$smarty->assign("help", help("chpass"));
$smarty->assign("menu_button", menu_buttons());
$smarty->display("main.tpl");
?>
