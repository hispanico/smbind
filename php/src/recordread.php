<?php
if(!function_exists("is_admin")) { include("include.php"); }

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
					  "ORDER BY host, type, pri, destination " .
					  limit())
				);
			pages("SELECT id " .
			      "FROM records " .
			      "WHERE zone = " . $_GET['i']
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
			$smarty->assign("page_root", "./record.php?i=" . $_GET['i'] . "&amp;");
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
