<?php
if(!function_exists("is_admin")) { include("include.php"); }

$zoneres = $dbconnect->query("SELECT * FROM zones WHERE updated = 'yes'");
is_error($zoneres);

while($zone = $zoneres->fetchrow()) {
	$recordres = $dbconnect->query("SELECT * FROM records " . 
				"WHERE zone = " . $zone[0] . " AND valid != 'no' " .
				"ORDER BY host, type, pri, destination");
	is_error($recordres);
	$out = 
"\$TTL   " . $zone[8] . "
@       IN      SOA     " . $zone[2] . ". " . $_CONF['hostmaster'] . ". (
			" . $zone[4] . " \t; Serial
			" . $zone[5] . " \t\t; Refresh
			" . $zone[6] . " \t\t; Retry
			" . $zone[7] . " \t; Expire
			" . $zone[8] . ")\t\t; Negative Cache TTL
;\n" ;
	if ($zone[2] != '') {
		$out .= "@       IN      NS\t\t" . $zone[2] . ".\n";
	}
	if ($zone[3] != '') {
		$out .= "@       IN      NS\t\t" . $zone[3] . ".\n";	
	}
	$fd = fopen($_CONF['path'] . preg_replace('/\//','-',$zone[1]), "w")
		or die("Cannot open: " . $_CONF['path'] . preg_replace('/\//','-',$zone[1]));
	fwrite($fd, $out);
	fclose($fd);

	$rebuild = "yes";

	while($record = $recordres->fetchrow()) {
		if($record[3] == "MX") {
			$pri = $record[4];
		}
		else {
			$pri = "";
		}
		if(
			($record[3] == "NS" || 
			 $record[3] == "PTR" || 
			 $record[3] == "CNAME" || 
			 $record[3] == "MX" || 
			 $record[3] == "SRV") && 
			($record[5] != "@")) 	{
			$destination = $record[5] . ".";
		}
		elseif($record[3] == "TXT") {
			$destination = "\"" . $record[5] . "\"";
		}
		else {
			$destination = $record[5];
		}
		$out = $record[2] . "\tIN\t" . $record[3] . "\t" . $pri . "\t" . $destination . "\n";
		$fd = fopen($_CONF['path'] . preg_replace('/\//','-',$zone[1]), "a");
		fwrite($fd, $out);
		fclose($fd);
		$testres = $dbconnect->query("UPDATE records SET valid = 'yes' " . 
					"WHERE id = " . $record[0]);
		is_error($testres);
	}
	$cmd = $_CONF['namedcheckzone'] . " " . $zone[1] . " " . $_CONF['path'] .
		preg_replace('/\//','-',$zone[1]);
	exec($cmd, $output, $exit);
	if ($exit == 0) {
		$updateres = $dbconnect->query("UPDATE zones SET updated = 'no', valid = 'yes' " . 
						"WHERE id = " . $zone[0]);
		is_error($updateres);
		$rebuild = "yes";
	}      
	else {
		$updateres = $dbconnect->query("UPDATE zones SET updated = 'yes', valid = 'no' " . 
						"WHERE id = " . $zone[0]);
		is_error($updateres);
	}
}

if (isset($rebuild)) {
	$confres = $dbconnect->query("SELECT name FROM zones ORDER BY name");
	is_error($confres);

	$cout = "";
	while($conf = $confres->fetchrow()) {
		$cout .= "zone \"" . $conf[0] . "\" {
			type master;
			file \"" . preg_replace('/\//','-',$conf[0]) . "\";
		};\n\n";
	}
	$fd = fopen($_CONF['conf'],"w");
	fwrite($fd, $cout);
	fclose($fd);

	$cmd = $_CONF['namedcheckconf'] . " " . $_CONF['conf'] . " > /dev/null";
	system($cmd, $exit);
	if ($exit != 0) { die($_CONF['namedcheckconf'] . " exit status " . $exit); }

	#$dbconnect->disconnect();

	$cmd = $_CONF['rndc'] . " reload > /dev/null";
	system($cmd, $exit);
	if ($exit != 0) { die($_CONF['rndc'] . " exit status " . $exit); }
}
$smarty->assign("bad", bad_records($userid));
$smarty->assign("output", $output);
$smarty->assign("pagetitle", "Commit changes");
$smarty->assign("template", "commit.tpl");
$smarty->assign("help", help("commit"));
$smarty->assign("menu_button", menu_buttons());
$smarty->display("main.tpl");
?>
