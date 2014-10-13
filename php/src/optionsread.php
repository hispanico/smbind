<?php
if(!function_exists("is_admin")) { include("include.php"); }

if(is_admin()) {
	$records = sql_query("SELECT prefkey, prefval " .
			     "FROM options " .
			     "WHERE preftype = 'record'" .
			     "ORDER by prefkey"
		   );
	for($x = 0, $y = 0, $i = 0; $i < count($records); $y++, $i++) {
		if($y == 4) {
			$x++;
			$y = 0;
		}
		$recordarray[$x][$y] = $records[$i];
	}

	$options = sql_query("SELECT prefkey, prefval " .
			     "FROM options " .
			     "WHERE preftype = 'normal'" .
			     "ORDER by prefkey"
		   );

	$smarty->assign("records", $recordarray);
	$smarty->assign("options", $options);
	$smarty->assign("pagetitle", "Options");
	$smarty->assign("template", "optionsread.tpl");
	$smarty->assign("help", help("optionsread"));
	$smarty->assign("menu_button", menu_buttons());
	$smarty->display("main.tpl");
}
else {
	// The user is not an administrator.
	notadmin($smarty);
}

?>
