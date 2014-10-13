<?php
if(!function_exists("is_admin")) { include("include.php"); }

if(is_admin()) {
	if((filter("alphanum", $_POST['username_one'], "no")) &&
	   (filter("alphanum", $_POST['password_one'], "no")) &&
	   (filter("alphanum", $_POST['confirm_password'], "no"))) {
		$res = $dbconnect->query("SELECT id " .
					 "FROM users " .
					 "WHERE username = '" . $_POST['username_one'] . "'"
				   );
		is_error($res);
		if($res->numRows() == 0) {
			if($_POST['password_one'] == $_POST['confirm_password']) {
			   $res = $dbconnect->query("INSERT INTO users " .
							"(username, password, admin) ".
						    "VALUES(" .
							"'" . $_POST['username_one'] . "', " .
							"'" . md5($_POST['password_one']) . "', " .
							"'" . $_POST['admin'] . "')"
					      );
				is_error($res);
			}
			else {  // Password doesn't match.
				$smarty->assign("pagetitle", "Ooops!");
				$smarty->assign("reason", reason("pwtwo"));
				$smarty->assign("template", "accessdenied.tpl");
				$smarty->assign("help", help("accessdenied"));
				$smarty->assign("menu_button", menu_buttons());
				$smarty->display("main.tpl");
				die();
			}
		}
		else {  // The user exists in the db.
			$smarty->assign("pagetitle", "Ooops!");
			$smarty->assign("reason", reason("existuser"));
			$smarty->assign("template", "accessdenied.tpl");
			$smarty->assign("help", help("accessdenied"));
			$smarty->assign("menu_button", menu_buttons());
			$smarty->display("main.tpl");
			die();
		}
	}
	else {  // Bad input from user.
		if(! $_POST['password_one']) {
				$smarty->assign("reason", reason("nopassword"));
		}
		elseif(! $_POST['username_one']) {
			$smarty->assign("reason", reason("nousername"));
		}
		$smarty->assign("pagetitle", "Ooops!");
		$smarty->assign("template", "accessdenied.tpl");
		$smarty->assign("help", help("accessdenied"));
		$smarty->assign("menu_button", menu_buttons());
		$smarty->display("main.tpl");
		die();
	}
}
else {
	// The user is not an administrator.
	notadmin($smarty);
}

?>
