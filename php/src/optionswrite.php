<?php
if(!function_exists("is_admin")) { include("include.php"); }

if(is_admin()) {
	foreach($_POST as $value) {
		if(!filter("alphanum", $value, "yes")) {
			// Bad input from user.
			$smarty->assign("pagetitle", "Ooops!");
			$smarty->assign("template", "accessdenied.tpl");
			$smarty->assign("help", help("accessdenied"));
			$smarty->assign("menu_button", menu_buttons());
			$smarty->display("main.tpl");
			die();
		}
	}

	$res = $dbconnect->query("UPDATE options " .
				 "SET prefval = 'off' " .
				 "WHERE preftype = 'record'"
			   );
	is_error($res);

	foreach($_POST as $key => $value) {
		$res = $dbconnect->query("UPDATE options " .
					 "SET prefval = '" . $value . "' " .
					 "WHERE prefkey = '" . $key . "'"
				   );
	}
	is_error($res);
}
else {
	// The user is not an administrator.
	notadmin($smarty);
}

?>
