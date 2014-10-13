<?php
if(!function_exists("is_admin")) { include("include.php"); }

	if((filter("alphanum", $_POST['password_old'], "no")) &&
	   (filter("alphanum", $_POST['password_one'], "no")) &&
	   (filter("alphanum", $_POST['confirm_password'], "no"))) {
		$res = $dbconnect->query("SELECT id " .
					 "FROM users " .
					 "WHERE username = '" . $_SESSION['username'] . "'" .
					 "AND password = '" . md5($_POST['password_old']) . "'"
				   );
		is_error($res);
		if($res->numRows() != 0) {
			if($_POST['password_one'] == $_POST['confirm_password']) {
			   $res = $dbconnect->query("UPDATE users " .
						    "SET " .
							"password = '" . md5($_POST['password_one']) . "' " .
						    "WHERE id = " . $userid
					      );
			   is_error($res);
			   $smarty->assign("pagetitle", "Change password");
			   $smarty->assign("template", "savepass.tpl");
			   $smarty->assign("help", help("savepass"));
			   $smarty->assign("menu_button", menu_buttons());
			   $smarty->display("main.tpl");
			}
			else {
				// Password doesn't match.
				$smarty->assign("reason", reason("pwtwo"));
				$smarty->assign("template", "accessdenied.tpl");
				$smarty->assign("help", help("accessdenied"));
				$smarty->assign("menu_button", menu_buttons());
				$smarty->display("main.tpl");
				die();
			}
		}
		else {
			$smarty->assign("reason", reason("pwone"));
			$smarty->assign("template", "accessdenied.tpl");
			$smarty->assign("help", help("accessdenied"));
			$smarty->assign("menu_button", menu_buttons());
			$smarty->display("main.tpl");
			die();
		}
	}
	else {  // Bad input from user.
		$smarty->assign("pagetitle", "Ooops!");
		$smarty->assign("template", "accessdenied.tpl");
		$smarty->assign("help", help("accessdenied"));
		$smarty->assign("menu_button", menu_buttons());
		$smarty->display("main.tpl");
		die();
	}
?>
