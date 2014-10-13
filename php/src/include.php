<?php
session_start();

include("../config.php");
set_include_path(get_include_path() . ":" . $_CONF['smarty_path'] . ":" . $_CONF['peardb_path']);

include("DB.php");
$dsn = $_CONF['db_type'] . "://" . $_CONF['db_user'] . ":" . $_CONF['db_pass'] .
	"@" . $_CONF['db_host'] . "/" . $_CONF['db_db'];
$dbconnect = DB::connect($dsn);
if(DB::isError($dbconnect)) {
	die("Database error: " . DB::errorMessage($dbconnect));
}

$_CONF = sql_config($_CONF, $dbconnect);

include("Smarty.class.php");
$smarty = new Smarty;
$smarty->template_dir = "../templates";
$smarty->compile_dir = "../templates_c";

if(isset($_POST['username']) && isset($_POST['password'])) {
	if((!filter("alphanum", $_POST['username'])) or (!filter("alphanum", $_POST['password']))) {
		die("Username and password must contain only letters and numbers.");
	}
	$_SESSION['username'] = $_POST['username'];
	$_SESSION['password'] = $_POST['password'];
}

if(isset($_SESSION['username']) && isset($_SESSION['password'])) {
	$res = $dbconnect->query("SELECT ID FROM users " .
				"WHERE username = '" . $_SESSION['username'] . 
				"' AND password = '" . md5($_SESSION['password']) . "'"
				);
	if($res->numRows() == 0) {
		login_page($smarty);
	}
}
else {
	login_page($smarty);
}

$userid = current($res->fetchRow(0));

if(isset($_SERVER['PHP_SELF'])) {
	$smarty->assign("menu_current", basename($_SERVER['PHP_SELF']));
}
else {
	$smarty->assign("menu_current", "main");
}

if(is_admin()) {
	$smarty->assign("admin", "yes");
}

function bad_records($userid) {
	$return = array();
	if(is_admin()) {
		$zresult = sql_query("SELECT id, name FROM zones WHERE valid != 'yes';");
		if($zresult) {
			array_push($return, array('id' => $zresult[0]['id'], 'name' => $zresult[0]['name']));
		}
	}
	else {
		$zresult = sql_query("SELECT id, name FROM zones WHERE valid != 'yes' AND owner = " . $userid);
		if($zresult) {
			array_push($return, array('id' => $zresult[0]['id'], 'name' => $zresult[0]['name']));
		}
	}
	return $return;
}

function sql_query($query) {
	global $dbconnect;
	$return_array = array(); 
	$res = $dbconnect->query($query);
	is_error($res);
	while($row = $res->fetchrow(DB_FETCHMODE_ASSOC)) {
		array_push($return_array, $row);
	}
	return $return_array;
}

function filter($type, $str, $empty = "yes") {
	$regex['num'] = "(^[0-9.]*$)";
	$regex['alphanum'] = "(^[A-Za-z0-9._-]*$)";
	if(ereg($regex[$type], $str)) {
		return true;
	}
	elseif(empty($str)) {
		if($empty == "yes") {
			return true;
		}
		elseif($empty == "no") {
			return false;
		}
	}
	else {
		return false;
	}
}

function owner($zone) {
	global $userid; global $dbconnect;
	$res = $dbconnect->query("SELECT ID FROM users WHERE ID = " . $userid . " AND admin = 'yes'");
	if($res->numRows() != 0) {
		return true;
	}
	$res = $dbconnect->query("SELECT ID FROM zones WHERE owner = " . $userid . " AND ID = " . $zone);
	if($res->numRows() != 0) {
		return true;
	}
	return false;
}

function is_admin() {
	global $userid; global $dbconnect;
	$res = $dbconnect->query("SELECT ID FROM users WHERE ID = " . $userid . " AND admin = 'yes'");
	is_error($res);
	if($res->numRows() != 0) {
		return true;
	}
	else {
		return false;
	}
}

function rndc_status($_CONF) {
	$cmd = $_CONF['rndc'] . " status > /dev/null";
	system($cmd, $exit);
	return $exit;
}

function login_page($smarty) {
	$smarty->assign("pagetitle", "Login");
	$smarty->assign("template", "login.tpl");
	$smarty->assign("help", help("login"));
	$smarty->display("main.tpl");
	die();
}

function is_error($resource) {
	if(PEAR::isError($resource)) {
		die($resource->getMessage() . "<br><br>" . $resource->getDebugInfo());
	}
}

function reason($reason) {
	if($reason == "notown") {
		return "You don't own this zone.";
	}
	elseif($reason == "notadmin") {
		return "You are not an administrator.";
	}
	elseif($reason == "pwone") {
		return "The old password is not correct.";
	}
	elseif($reason == "pwtwo") {
		return "The second password doesn't match the first one.";
	}
	elseif($reason == "existzone") {
		return "The zone already exists in the database.";
	}
	elseif($reason == "existfile") {
		return "The zone already exists on the file system.";
	}
	elseif($reason == "existuser") {
		return "The user already exists in the database.";
	}
	elseif($reason == "nopassword") {
		return "That's not much of a password.";
	}
	elseif($reason == "nousername") {
		return "That's not much of a username.";
	}
	elseif($reason == "nozonename") {
		return "That's not much of a zone name.";
	}
	elseif($reason == "deleteadmin") {
		return "You may not delete the default admin user.";
	}
	else {
		return "An unknown error ocurred.";
	}
}

function help($help) {
	if($help == "login") {
		return "Please log in.";
	}
	elseif($help == "mainpage") {
		return "User status and Server status are displayed, " .
		       "along with any zone errors.";
	}
	elseif($help == "zoneread") {
		return "Your zones are displayed. Here you can create a zone, edit a zone, or delete a zone.";
	}
	elseif($help == "newzone") {
		return "Enter your new zone's domain name, name servers and smbind owner.<br><br>" .
		       "This will create a new zone with a SOA and NS record.<br><br>" .
		       "The Web/Mail/FTP IP fields will create these A, CNAME, and MX template records for you. " .
		       "Otherwise, leave them blank.";
	}
	elseif($help == "recordread") {
		return "Here you can modify your zone's SOA record, or add, edit, or delete resource records.";
	}
	elseif($help == "userlistread") {
		return "Here you can add, edit, or delete smbind users.";
	}
	elseif($help == "commit") {
		return "Your zone files are commited to disk, error checked, and reloaded.";
	}
	elseif($help == "optionsread") {
		return "Here you can change options that define how smbind works.";
	}
	elseif($help == "deletezone") {
		return "Please confirm.";
	}
	elseif($help == "deleteuser") {
		return "Please confirm.";
	}
	elseif($help == "newuser") {
		return "Here can you add a new user.";
	}
	elseif($help == "userread") {
		return "Here can you change the user's properties.";
	}
	elseif($help == "chpass") {
		return "Here can you change your password.";
	}
	elseif($help == "savepass") {
		return "Login using your new password.";
	}
	elseif($help == "accessdenied") {
		return "Access denied.";
	}
	else {
		return "";
	}
}

function notadmin($smarty) {
	$smarty->assign("reason", reason("notadmin"));
	$smarty->assign("pagetitle", "Access Denied");
	$smarty->assign("template", "accessdenied.tpl");
	$smarty->assign("help", help("accessdenied"));
	$smarty->assign("menu_button", menu_buttons());
	$smarty->display("main.tpl");
	die();
}

function sql_config($_CONF, $dbconnect) {
	$query = sql_query("SELECT prefval " .
			   "FROM options " .
			   "WHERE prefkey = 'hostmaster' " .
			   "AND preftype = 'normal'"
		);
	$_CONF['hostmaster'] = $query[0]['prefval'];

	$query = sql_query("SELECT prefval " .
			   "FROM options " .
			   "WHERE prefkey = 'prins' " .
			   "AND preftype = 'normal'"
		 );
	$_CONF['pri_dns'] = $query[0]['prefval'];

	$query = sql_query("SELECT prefval " .
			   "FROM options " .
			   "WHERE prefkey = 'secns' " .
			   "AND preftype = 'normal'"
		 );
	$_CONF['sec_dns'] = $query[0]['prefval'];

	$query = sql_query("SELECT prefval " .
			   "FROM options " .
			   "WHERE prefkey = 'range' " .
			   "AND preftype = 'normal'"
		);
	if($query) {
		$_CONF['range'] = $query[0]['prefval'];
	}
	else {
		global $dbconnect;
        	$res = $dbconnect->query("INSERT INTO options " .
					 "(prefkey, preftype, prefval) " .
					 "VALUES ('range', 'normal', '10')"
					);
		is_error($res);
	}

	$query = sql_query("SELECT prefkey " .
			   "FROM options " .
			   "WHERE prefval = 'on' " .
			   "AND preftype = 'record'" .
			   "ORDER BY prefkey"
		 );
	$_CONF['parameters'] = array();
	foreach($query as $record) {
		array_push($_CONF['parameters'], $record['prefkey']);
	}
	$query = sql_query("SELECT DISTINCT type " .
			   "FROM records"
		 );
	foreach($query as $record) {
		array_push($_CONF['parameters'], $record['type']);
	}
	$_CONF['parameters'] = array_unique($_CONF['parameters']);

	return $_CONF;
}

function menu_buttons() {
	global $userid;

	$zresult = sql_query("SELECT id FROM zones WHERE updated = 'yes'");
	if(count($zresult) == 0) {
		$committext = "Commit changes";
	}
	else {
		$committext = "<FONT COLOR=\"#FF0000\">Commit changes</FONT>";
	}

	if(count($zresult) == 0 && bad_records($userid)) {
		$maintext = "<FONT COLOR=\"#FF0000\">Main</FONT>";
	}
	else {
		$maintext = "Main";
	}
	
	return array(
			array("title" => $maintext, "link" => "main.php"),
			array("title" => "Zones", "link" => "zonelist.php"),
			array("title" => "Users", "link" => "userlist.php"),
			array("title" => "Change password", "link" => "chpass.php"),
			array("title" => $committext, "link" => "commit.php"),
			array("title" => "Options", "link" => "options.php"),
			array("title" => "Log out", "link" => "logout.php")
	);
}

function limit() {
	global $_CONF;
	global $smarty;
	global $dbtype;
	if(!isset($_GET['page'])) {
		$_GET['page'] = 1;
	}
	$smarty->assign("current_page", $_GET['page']);

	if($_CONF['range'] > 0) {
		if($_CONF['db_type'] = "mysql") {
			$limit = "LIMIT " .
				(($_GET['page'] * $_CONF['range']) - $_CONF['range']) .
		       		", " .  
		         	$_CONF['range'];
		}
		elseif($_CONF['db_type'] = "psql") {
			$limit = "OFFSET " .
				(($_GET['page'] * $_CONF['range']) - $_CONF['range']) .
				" LIMIT " .
				$_CONF['range'];
		}
		else {
			$limit = "";
		}
	}
	else {
		$limit = "";
	}
	return $limit;
}

function pages($sql) {
	global $_CONF; global $smarty;
	$return = array();
	$result = sql_query($sql);
	if(count($result) > $_CONF['range'] && $_CONF['range'] > 0) {
		for($i = 1; $i <= (ceil((count($result)) / $_CONF['range'])); $i++) {
			array_push($return, $i);
		}
	}
	$smarty->assign("pages", $return);
}

?>
