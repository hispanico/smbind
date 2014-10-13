<?php
if(!function_exists("is_admin")) { include("include.php"); }

if(is_admin()) {
	if((filter("num", $_GET['i'])) &&
	   (filter("alphanum", $_POST['admin'])) &&
	   (filter("alphanum", $_POST['password_one'])) &&
	   (filter("alphanum", $_POST['confirm_password']))) {
		$smarty->assign("pagetitle", "Viewing user");
		if(($_POST['password_one']) &&
		   ($_POST['password_one'] == $_POST['confirm_password'])) {
			$res = $dbconnect->query("UPDATE users " .
						 "SET " .
						 "password = '" . md5($_POST['password_one']) . "', " .
						 "admin = '" . $_POST['admin'] . "' " .
						 "WHERE id = " . $_GET['i']
					  );
			is_error($res);
			}
		elseif($_POST['password_one'] != $_POST['confirm_password']) {
				$smarty->assign("reason", reason("pwtwo"));
				$smarty->assign("template", "accessdenied.tpl");
				$smarty->assign("help", help("accessdenied"));
				$smarty->assign("menu_button", menu_buttons());
				$smarty->display("main.tpl");
				die();
		}
		$res = $dbconnect->query("UPDATE users " .
					 "SET admin = '" . $_POST['admin'] . "' " .
					 "WHERE id = " . $_GET['i']
				   );
		is_error($res);
		$res = $dbconnect->query("SELECT username FROM users WHERE id = " . $_GET['i']);
		is_error($res);
	}
	else {
		// Bad input from user.
		$smarty->assign("pagetitle", "Ooops!");
		$smarty->assign("template", "accessdenied.tpl");
		$smarty->assign("help", help("accessdenied"));
		$smarty->assign("menu_button", menu_buttons());
		$smarty->display("main.tpl");
		die();		}
}
else {
	// The user is not an administrator.
	notadmin($smarty);
}

?>
