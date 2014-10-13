<?php
if(!function_exists("is_admin")) { include("include.php"); }

if(is_admin()) {
        if(filter("num", $_GET['i'])) {
                $smarty->assign("pagetitle", "Delete user");
                $res = $dbconnect->query("SELECT username " .
                                         "FROM users " .
                                         "WHERE id = " . $_GET['i']
                                   );
                is_error($res);
                $smarty->assign("user", current($res->fetchRow(0)));
                $smarty->assign("userid", $_GET['i']);
                $smarty->assign("template", "deleteuser.tpl");
                $smarty->assign("help", help("deleteuser"));
		$smarty->assign("menu_button", menu_buttons());
                $smarty->display("main.tpl");
        }
        else {  // Bad input from user.
                $smarty->assign("pagetitle", "Ooops!");
                $smarty->assign("template", "accessdenied.tpl");
                $smarty->assign("help", help("accessdenied"));
                $smarty->display("main.tpl");
                die();
        }
}
else {
        // The user is not an administrator.
        notadmin($smarty);
}
?>
