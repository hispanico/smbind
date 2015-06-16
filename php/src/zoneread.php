<?php
if(!function_exists('is_admin')) { include("include.php"); }

$where_clause='';
if (isset($_GET['zone_search']) && preg_match('/^[a-z0-9._-]+$/i', $_GET['zone_search'])) {
	$where_clause = "name LIKE '%" . $_GET['zone_search'] . "%' ";
}
if(is_admin()) { 
   $where_clause ? ($where_clause = 'WHERE ' . $where_clause) : 1;
   $smarty->assign("zonelist", 
	sql_query("SELECT id, name, serial, comment, updated, valid " .
		  "FROM zones " . $where_clause .
		  "ORDER BY name " .
		  limit())
	    );
	pages("SELECT id " .
	      "FROM zones " . $where_clause
	);
}
else {
   $where_clause ? ($where_clause = 'AND ' . $where_clause) : 1;
   $smarty->assign("zonelist", 
	sql_query("SELECT id, name, serial, comment updated, valid " .
		  "FROM zones " . 
		  "WHERE owner = $userid " . $where_clause .
		  "ORDER BY name " .
		  limit())
	);
	pages("SELECT id " .
	      "FROM zones " .
	      "WHERE owner = $userid " . $where_clause
	);
}
$smarty->assign("pagetitle", "Zones");
$smarty->assign("template", "zoneread.tpl");
$smarty->assign("help", help("zoneread"));
$smarty->assign("menu_button", menu_buttons());
if (isset($_GET['zone_search'])) {
  $smarty->assign("zone_search", $_GET['zone_search']);
  $page_root="?zone_search=". $_GET['zone_search'] . '&';
} else {
  $page_root="?";
}
$smarty->assign("page_root", $page_root);
$smarty->display("main.tpl");

?>
