<?php
if(!function_exists('is_admin')) { include("include.php"); }

if(is_admin()) { 
   $smarty->assign("zonelist", 
	sql_query("SELECT id, name, serial, updated, valid " .
		  "FROM zones " .
		  "ORDER BY name " .
		  limit())
	    );
	pages("SELECT id " .
	      "FROM zones"
	);
}       
else {  
   $smarty->assign("zonelist", 
	sql_query("SELECT id, name, serial, updated, valid " .
		  "FROM zones " . 
		  "WHERE owner = $userid " . 
		  "ORDER BY name " .
		  limit())
	);
	pages("SELECT id " .
	      "FROM zones " .
	      "WHERE owner = $userid"
	);
}       
$smarty->assign("pagetitle", "Zones");
$smarty->assign("template", "zoneread.tpl");
$smarty->assign("help", help("zoneread"));
$smarty->assign("menu_button", menu_buttons());
$smarty->assign("page_root", "./zonelist.php?");
$smarty->display("main.tpl");

?>
