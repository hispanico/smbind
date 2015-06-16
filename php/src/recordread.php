<?php
if(!function_exists("is_admin")) { include("include.php"); }

$wclause='';
if (isset($_GET['rname']) && preg_match('/^[a-z0-9._-]+$/i', $_GET['rname'])) {
  $wclause=" AND ( ( host like '%" . $_GET['rname'] . "%' )" .
    " OR (destination like '%" . $_GET['rname'] . "%' ) ) " ;

}

if(filter("num", $_GET['i'])) {
	if(owner($_GET['i'])) {
		$res = $dbconnect->query("SELECT * " .
			    "FROM zones " .
			    "WHERE id = " . $_GET['i']
				      );
		is_error($res);
		if($res->numRows() != 0) {
			$smarty->assign("zone", $res->fetchRow(DB_FETCHMODE_ASSOC));
			$smarty->assign("pagetitle", "Viewing zone");
			$smarty->assign("record",
				sql_query("SELECT * " .
					  "FROM records " .
					  "WHERE zone = " . $_GET['i'] . " " .
					  $wclause .
					  "ORDER BY host, type, pri, destination " .
					  limit())
				);
			pages("SELECT id " .
			      "FROM records " .
			      "WHERE zone = " . $_GET['i'] .
			      $wclause
			     );
			$smarty->assign("types", $_CONF['parameters']);
			$smarty->assign("userlist",
				sql_query("SELECT id, username " .
					  "FROM users " .
					  "ORDER BY username")
				);
			$smarty->assign("template", "recordread.tpl");
			$smarty->assign("help", help("recordread"));
			$smarty->assign("menu_button", menu_buttons());
			if (isset($_GET['rname'])) {
			  $smarty->assign('rname', $_GET['rname']);
			  $page_root = '?i=' . $_GET['i'] . '&rname=' . $_GET['rname'] . '&';
			} else {
			  $page_root= '?i=' . $_GET['i'] . '&';
			}
			$smarty->assign("page_root", $page_root);
			$smarty->display("main.tpl");
		}
	}
	else {
		// The user doesn't own this zone.
		$smarty->assign("pagetitle", "Ooops!");
		$smarty->assign("reason", reason("notown"));
		$smarty->assign("template", "accessdenied.tpl");
		$smarty->assign("help", help("accessdenied"));
		$smarty->assign("menu_button", menu_buttons());
		$smarty->display("main.tpl");
		die();
	}
}
else {
	// Bad input from user.
	$smarty->assign("pagetitle", "Ooops!");
	$smarty->assign("template", "accessdenied.tpl");
	$smarty->assign("help", help("accessdenied"));
	$smarty->assign("menu_button", menu_buttons());
	$smarty->display("main.tpl");
	die();
}

?>
