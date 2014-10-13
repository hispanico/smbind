<?php
if(!function_exists("is_admin")) { include("include.php"); }

if(is_admin()) {
	$res = $dbconnect->query("SELECT id FROM zones");
	is_error($res);
	$zones = $res->numRows();
	$smarty->assign("admin", "yes");
}
else {
	$res = $dbconnect->query("SELECT id FROM zones " .
	 			 "WHERE owner = " . $userid
			   );
	is_error($res);
	$zones = $res->numRows();
	$smarty->assign("admin", "no");
}
$smarty->assign("pagetitle", "Main");
$smarty->assign("user", $_SESSION['username']);
$smarty->assign("zones", $zones);
$smarty->assign("status", rndc_status($_CONF));
$smarty->assign("bad", bad_records($userid));
$smarty->assign("template", "mainpage.tpl");
$smarty->assign("help", help("mainpage"));
$smarty->assign("menu_button", menu_buttons());
$smarty->display("main.tpl");
?>
