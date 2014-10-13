<html>
<html>
<head>
    <title>smbind configtest</title>
</head>
<body>
<h1>smbind configtest</h1>

<p>This script will try to check some aspects of your smbind configuration
and point you to errors where ever it can find them. You need to edit <tt>config.php</tt>
and read the <tt>INSTALL</tt> file first before you run this script.</p>

<?php

function fileinfo($file) {
	if(file_exists($file)) {
		$user = exec("id -un");
		$getpwuid = posix_getpwuid(fileowner($file));
		$owner = $getpwuid['name'];

		$members = "";
		$getgrgid = posix_getgrgid(filegroup($file));
		$getpwuid = posix_getpwuid($getgrgid['gid']);
		if(isset($getpwuid['name'])) {
			$members .= $getpwuid['name'] . ", ";
		}
		foreach($getgrgid['members'] as $value) {
			$members .= "$value, ";
		}
		$members = rtrim($members, ", ");
		$group = $getgrgid['name'];

		if(fileperms($file) & 0x0100) {
			$ownerperm = "read ";
		}
		if(fileperms($file) & 0x0080) {
			$ownerperm .= "write ";
		}
		if(fileperms($file) & 0x0040) {
			$ownerperm .= "execute ";
		}
		if(!isset($ownerperm)) {
			$ownerperm = "no";
		}
		if(fileperms($file) & 0x0020) {
			$groupperm = "read ";
		}
		if(fileperms($file) & 0x0010) {
			$groupperm .= "write ";
		}
		if(fileperms($file) & 0x0008) {
			$groupperm .= "execute ";
		}
		if(!isset($groupperm)) {
			$groupperm = "no";
		}
		if(fileperms($file) & 0x0004) {
			$otherperm = "read ";
		}
		if(fileperms($file) & 0x0002) {
			$otherperm .= "write ";
		}
		if(fileperms($file) & 0x0001) {
			$otherperm .= "execute ";
		}
		if(!isset($otherperm)) {
			$otherperm = "no";
		}
		return  "The webserver is running as <b>$user</b>.<br>" .
			"The owner <b>$owner</b> has <b>$ownerperm</b> permissions to $file.<br>" .
			"The group <b>$group</b> has <b>$groupperm</b> permissions to $file.<br>" .
			"The group <b>$group</b> has the following members: $members.<br>" .
			"Any other account has <b>$otherperm</b> permissions to $file.<br>";
	}
	else {
		return "File does not exist: $file<br>";
	}
}

print "Testing config.php...";
$handle = fopen("../config.php", "r")
	or die("Could not read config.php.<br><br>" . fileinfo("../config.php"));
fclose($handle);
print "OK<br>";
include("../config.php");

print "Testing PEAR DB...";
$handle = fopen($_CONF['peardb_path'] . "/DB.php", "r")
	or die ("Could not read PEAR DB. Check " . $_CONF['peardb_path'] . " for DB.php.");
fclose($handle);
print "OK<br>";

print "Testing Smarty...";
$handle = fopen($_CONF['smarty_path'] . "/Smarty.class.php", "r")
	or die ("Could not read Smarty. Check " . $_CONF['smarty_path'] . " for Smarty.class.php.");
fclose($handle);
print "OK<br>";

print "Testing templates_c...";
$handle = fopen("../templates_c/test", "w")
	or die ("Could not write to templates_c.<br><br>" . fileinfo("../templates_c"));
fclose($handle);
unlink("../templates_c/test");
print "OK<br>";

print "Testing path...";
$handle = fopen($_CONF['path'] . "test", "w")
	or die ("Could not write to " . $_CONF['path'] . ".<br><br>" . fileinfo($_CONF['path']));
fclose($handle);
unlink($_CONF['path'] . "test");
print "OK<br>";

print "Testing conf directory...";
file_exists(dirname($_CONF['conf']))
	or die ("The " . dirname($_CONF['conf']) . " directory does not exist.");
print "OK<br>";

print "Testing conf file...";
$handle = fopen($_CONF['conf'], "a")
	or die ("Could not write to " . $_CONF['conf'] . ".<br><br>" . fileinfo(dirname($_CONF['conf'])));
fclose($handle);
print "OK<br>";

print "Testing named-checkconf...";
file_exists($_CONF['namedcheckconf'])
	or die ("Could not find " . $_CONF['namedcheckconf'] . ". Please make sure that it is installed.");
is_executable($_CONF['namedcheckconf'])
	or die ("Could not execute " . $_CONF['namedcheckconf'] . ".<br><br>" . fileinfo($_CONF['namedcheckconf']));
print "OK<br>";

print "Testing named-checkzone...";
file_exists($_CONF['namedcheckzone'])
	or die ("Could not find " . $_CONF['namedcheckzone'] . ". Please make sure that it is installed.");
is_executable($_CONF['namedcheckzone'])
	or die ("Could not execute " . $_CONF['namedcheckzone'] . ".<br><br>" . fileinfo($_CONF['namedcheckzone']));
print "OK<br>";

print "Testing rndc...";
file_exists($_CONF['rndc'])
	or die ("Could not find " . $_CONF['rndc'] . ". Please make sure that it is installed.");
print "OK<br>";

print "Testing (guess) /etc/rndc.conf...";
if(file_exists("/etc/rndc.conf")) {
	if($handle = fopen("/etc/rndc.conf", "r")) {
		print "OK<br>";
		fclose($handle);
	}
	else {
		print("/etc/rndc.conf exists but could not be opened.<br><br>" . fileinfo("/etc/rndc.conf"));
	}
}
else {
	print "not found.<br>";
}

print "Testing (guess) /etc/rndc.key...";
if(file_exists("/etc/rndc.key")) {
	if($handle = fopen("/etc/rndc.key", "r")) {
		print "OK<br>";
		fclose($handle);
	}
	else {	
		print("/etc/rndc.key exists but could not be opened.<br><br>" . fileinfo("/etc/rndc.key"));
	}
}
else {
	print "not found.<br>";
}

print "Testing (guess) connection to localhost:953...";
if($handle = fsockopen("localhost", 953, $errorno, $errorstr, 5)) {
	print "OK<br>";
	fclose($handle);
}
else {
	print "error. Could not connect to localhost:953: $errorstr($errorno)<br>" .
	      "Either <b>named isn't running</b> or rndc is configured on an alternate port.<br>";
}

print "Testing rndc execution...";
if($out = exec($_CONF['rndc'] . " status")) {
	print "OK<br>$out<br>";
}
else {
	$out = exec($_CONF['rndc'] . " status 2>&1");
	die("Could not run rndc as " . exec("id -un") . ". " .
	       "Please make sure that " . exec("id -un") . " is a member of the group that runs named, " .
	       "and that all rndc config files and keys are readable by " . exec("id -un") . ".<br><br>" .
	       "output was: $out");
}

print "Testing database connection...";
include("DB.php");
$dsn = $_CONF['db_type'] . "://" . $_CONF['db_user'] . ":" . $_CONF['db_pass'] .
	"@" . $_CONF['db_host'] . "/" . $_CONF['db_db'];
$dbconnect = DB::connect($dsn);
if(DB::isError($dbconnect)) { 
	die("Database error: " . DB::errorMessage($dbconnect)); 
}
print "OK<br>";

print "Testing database SELECT from zones table...";
$res = $dbconnect->query("SELECT * FROM zones");
if(PEAR::isError($res)) {
		die($res->getMessage());
}
print "OK ";

print "Testing database SELECT from users table...";
$res = $dbconnect->query("SELECT * FROM users");
if(PEAR::isError($res)) {
		die($res->getMessage());
}
print "OK ";

print "Testing database SELECT from records table...";
$res = $dbconnect->query("SELECT * FROM records");
if(PEAR::isError($res)) {
		die($res->getMessage());
}
print "OK<br>";

print "Testing database INSERT into zones table...";
$res = $dbconnect->query("INSERT INTO zones (name) VALUES ('test567')");
if(PEAR::isError($res)) {
		die($res->getMessage());
}
print "OK ";

print "Testing database INSERT into users table...";
$res = $dbconnect->query("INSERT INTO users (username, password) VALUES ('test567', md5('test567'))");
if(PEAR::isError($res)) {
		die($res->getMessage());
}
print "OK ";

print "Testing database INSERT into records table...";
$res = $dbconnect->query("INSERT INTO records (host,type,destination) VALUES ('test567','','')");
if(PEAR::isError($res)) {
		die($res->getMessage());
}
print "OK<br>";

print "Testing database UPDATE zones table...";
$res = $dbconnect->query("UPDATE zones SET name='test890' WHERE name='test567'");
if(PEAR::isError($res)) {
		die($res->getMessage());
}
print "OK ";

print "Testing database UPDATE users table...";
$res = $dbconnect->query("UPDATE users SET username='test890' WHERE username='test567'");
if(PEAR::isError($res)) {
		die($res->getMessage());
}
print "OK ";

print "Testing database UPDATE records table...";
$res = $dbconnect->query("UPDATE records SET host='test890' WHERE host='test567'");
if(PEAR::isError($res)) {
		die($res->getMessage());
}
print "OK<br>";

print "Testing database DELETE FROM zones table...";
$res = $dbconnect->query("DELETE FROM zones WHERE name='test890'");
if(PEAR::isError($res)) {
		die($res->getMessage());
}
print "OK ";

print "Testing database DELETE FROM users table...";
$res = $dbconnect->query("DELETE FROM users WHERE username='test890'");
if(PEAR::isError($res)) {
		die($res->getMessage());
}
print "OK ";

print "Testing database DELETE FROM records table...";
$res = $dbconnect->query("DELETE FROM records WHERE host='test890'");
if(PEAR::isError($res)) {
		die($res->getMessage());
}
print "OK<br>";
$dbconnect->disconnect();


print "<br>";
print "Congratulations, your setup looks good. Please remember to add the following line to your named.conf:";
print "<br><tt>include \"" . $_CONF['conf'] . "\";</tt><br>";

?>
</html>
