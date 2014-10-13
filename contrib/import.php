<?php
/**
 * Import script for smbind
 *
 * This script tries to read every file in the current directory. If the file looks like a zone file
 * it will be imported into the smbind database. This script has been tested with PHP 5.
 */

/*
    Copyright (C) 2010  Eero Vuojolahti

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


/**
 * PLEASE PUT YOUR settings here
 */
define('DB_SERVER','localhost');
define('DB_USER','smbind');
define('DB_PASSWORD','');
define('DB_NAME','smbind');
define('EMPTY_DB',false); // Turn this to true if you want to empty smbind before processing zone files
define('ZONE_SUFFIX','//'); // Regular expression to remove when trying to create a zone name from a filename

// This would change the name generated from a file "example.org.zone" to be "example.org"
//define('ZONE_SUFFFIX','/.zone$/');

/*
 *
 *
 *** DO NOT EDIT BEYOND THIS UNLESS YOU KNOW WHAT YOU ARE DOING ***
 *
 *
 */

define('COMMENT_PATTERN','/;[^\n]*/i');
define('ORIGIN_PATTERN', '/^\$ORIGIN\s+(.+)\.\s*/msi');
define('SOA_BEGINS_PATTERN', '/^([^\s]+)?(\s+[\d\w]+)?\s+IN\s+SOA\s+/msi');
define('FULL_SOA_PATTERN', '/^([^\s]+)?(\s+[\d\w]+)?\s+IN\s+SOA\s+([-\w\d.]*)\.\s+([-\w\d.]*)\s+\((.*)\)/msi');
define('TIMES_PATTERN', '/\s*(\d+\w?)\s+(\d+\w?)\s+(\d+\w?)\s+(\d+\w?)\s+(\d+\w?)/msi');
define('TXT_PATTERN', '/^\"(.*)\"/msi');
define('MX_PATTERN', '/^(\d+)\s+([^\s].*)/msi');
define('TYPE_PATTERN','(A|A6|AAAA|AFSDB|APL|ATMA|AXFR|CERT|CNAME|DNAME|DNSKEY|DS|EID|GPOS|HINFO|ISDN|IXFR|KEY|KX|LOC|MAILB|MINFO|MX|NAPTR|NIMLOC|NS|NSAP|NSAP-PTR|NSEC|NXT|OPT|PTR|PX|RP|RRSIG|RT|SIG|SINK|SRV|SSHFP|TKEY|TSIG|TXT|WKS|X25)');
define('RECORD_PATTERN', '/^([^\s]+)?(\s+[\d][\d\w]*)?(\s+IN)?\s+'.TYPE_PATTERN.'\s+([^\s].*$)/msi');
define('BIND_TIME_PATTERN','/^(\d+)([smhdw])/');

function parse_zone_file($file, $db_conn) {
	$zone = "";
	$prins = "";
	$record_count = 0;
	$soa_begins = false;
	$soa_found = false;
	$soa_data = "";
	$handle = @fopen($file, "r");

	if ($handle) {
	    while (!feof($handle)) {
		$buffer = fgets($handle);
		$buffer = strip_comments($buffer);
		// SOA must be first record in the zone files! Otherwise records before it will be skipped.
		if ($soa_found === false) {
		        // Lets try to find our domain name
			if (preg_match(ORIGIN_PATTERN, $buffer, $match)) {
				$zone = strtolower($match[1]);
				//echo "\n\n WE GOT A DOMAIN $zone\n\n";
			}

		        // Now we need SOA
			if (preg_match(SOA_BEGINS_PATTERN, $buffer, $match)) {
				$soa_begins = true;
			}
			if ($soa_begins) {
				$soa_data .= $buffer;
			}

			if (preg_match(FULL_SOA_PATTERN, $soa_data, $match)) {
				// SOA found and we can set the primary dns
				$prins = $match[3];
				if ($zone === "" && strlen($match[1]) > 1 && substr($match[1], -1) === ".") {
					// gets domain name from SOA
					$zone = strtolower(substr($match[1], 0, -1));
				} else if ($zone === "") {
					// as a last resort we use the filename as a domain
					$zone = strtolower(preg_replace(ZONE_SUFFIX, '', $file));
				}

				if(preg_match(TIMES_PATTERN, $match[5], $match2)) {
					// serial, refresh, retry, expire and ttl found!
					$soa_found = true;
					$serial = $match2[1];
					$refresh = bind_time_format($match2[2]);
					$retry = bind_time_format($match2[3]);
					$expire = bind_time_format($match2[4]);
					$ttl = bind_time_format($match2[5]);
					//print_r($match);
					//print_r($match2);
					$zone_id = create_zone($zone, $prins, $serial, $refresh, $retry, $expire, $ttl, $db_conn);
					if ($zone_id === false) {
						return "Could not create zone \"$zone\" from file: \"$file\"! Maybe it exists already?\n";
					}
					$record_count++; // First record will be SOA
				} else {
					// SOA was found but we couldn't parse serial, refresh, retry, expire and ttl
					// skipping this domain
					return "Possibly broken SOA! Skipping file: $file\n";
				}
			}
		} else if (preg_match(RECORD_PATTERN, $buffer, $match)) {
			// SOA has been found now we can parse rest of the settings
			$host = $match[1];
			$type = $match[4];
			$destination = $match[5];
			$ret = create_record($zone_id, $zone, $host, $type, $destination, $db_conn);
			//print_r($match);
			if ($ret === false) {
				die("Error while inserting record to DB! zone: $zone");
			}
			$record_count++; // One more record successfully added
		}
	    }
	    fclose($handle);
	    return "parsed: $zone with $record_count records.\n";
	}
}

function create_zone($zone, $prins, $serial, $refresh, $retry, $expire, $ttl, $db_conn) {
	$res = mysql_query( "SELECT id FROM zones WHERE name = '".mysql_real_escape_string($zone)."'");
	if ($row = mysql_fetch_array($res)) {
		// zone already exists
		return false;
	}

	$query = sprintf("INSERT INTO zones(name,pri_dns,serial,refresh,retry,expire,ttl) VALUES('%s', '%s', '%s', '%s', '%s', '%s', '%s')",
		mysql_real_escape_string($zone),
		mysql_real_escape_string($prins),
		mysql_real_escape_string($serial),
		mysql_real_escape_string($refresh),
		mysql_real_escape_string($retry),
		mysql_real_escape_string($expire),
		mysql_real_escape_string($ttl));

	$res = mysql_query($query, $db_conn);

	if($res === false) {
		return false;
	}
	return mysql_insert_id($db_conn);
}

function create_record($zone_id, $zone, $host, $type, $destination, $db_conn) {
	$pri = 0;
	if ($host === "") {
		$host = "@";
	}
	if ($type === "MX") {
		if (preg_match(MX_PATTERN, $destination, $match)) {
			$pri = $match[1];
			$destination = $match[2];
		} else {
			die("Error in MX record! zone: $zone\n");
		}
	}
	$destination = fix_destination($zone, $type, $destination);
	if ($type === "NS" && $host === "@") {
		// Tries to insert current NS as the zones sec_dns if it is still empty.
		check_secdns($zone_id, $destination, $db_conn);
		$query = sprintf("SELECT * FROM zones WHERE id = '%s' AND (pri_dns = '%s' OR sec_dns = '%s')",
				mysql_real_escape_string($zone_id),
				mysql_real_escape_string($destination),
				mysql_real_escape_string($destination));
		$res = mysql_query($query);
		if ($row = mysql_fetch_array($res)) {
			// already primary or secondary ns
			return true;
		}
	}
	$query = sprintf("INSERT INTO records(zone,host,type,pri,destination) VALUES('%s', '%s', '%s', '%s', '%s')",
		mysql_real_escape_string($zone_id),
		mysql_real_escape_string($host),
		mysql_real_escape_string($type),
		mysql_real_escape_string($pri),
		mysql_real_escape_string($destination));

	$res = mysql_query($query, $db_conn);
	if($res === false) {
		return false;
	}
	return true;
}

function fix_destination($zone, $type, $destination) {
	$destination = trim($destination);
	switch ($type) {
	case "NS":
	case "PTR":
	case "CNAME":
	case "MX":
	case "SRV":
		if ($destination !== "@") {
			if (substr($destination, -1) === ".") {
				// removes trailing dot from a fqdn
				$destination = substr($destination, 0, -1);
			} else {
				// appends zone name to form a fqdn
				$destination .= ".".$zone;
			}
		}
	    break;
	case "TXT":
		if (preg_match(TXT_PATTERN, $destination, $match)) {
			// removes quotation marks
			$destination = $match[1];
		} else {
			die("Error in TXT record! zone: $zone\n");
		}
	    break;
	}
	return $destination;
}

function check_secdns ($zone_id, $ns, $db_conn) {
	// If sec_dns is empty, we should try to put first NS record there that is not already in pri_dns.
	$query = sprintf("UPDATE zones SET sec_dns = '%s' WHERE id = '%s' AND ISNULL(sec_dns) AND pri_dns != '%s'",
		mysql_real_escape_string($ns),
		mysql_real_escape_string($zone_id),
		mysql_real_escape_string($ns));
        $res = mysql_query($query, $db_conn);
//	echo "$query \n";
//	echo "Affected rows: ". mysql_affected_rows($db_conn) ."\n";
}

function bind_time_format($value) {
	if (preg_match(BIND_TIME_PATTERN, strtolower($value), $match)) {
		$value = $match[1];
		switch ($match[2]) {
			case "s":
				$multiplier = 1;
        			break;
			case "m":
				$multiplier = 60;
        			break;
			case "h":
				$multiplier = 3600;
        			break;
			case "d":
				$multiplier = 86400;
        			break;
			case "w":
				$multiplier = 604800;
        			break;
		}
		$value = $value*$multiplier;
	}
	return $value;
}

function strip_comments($string) {
	$replacement = '';
	return preg_replace(COMMENT_PATTERN, $replacement, $string);
}

$db_conn = mysql_connect(DB_SERVER, DB_USER, DB_PASSWORD);
if( $db_conn === false )
	die("impossible to connect to DB!");

if( mysql_select_db(DB_NAME) === false )
	die("impossible to select DB!");

if(EMPTY_DB === true) {
	mysql_query('DELETE FROM zones');
	mysql_query('DELETE FROM records');
}

$dir = opendir('.');
while(false !== ($file = readdir($dir))) {
	if(is_file($file)) {
		echo "Reading file: $file ...\n";
		$ret = parse_zone_file($file, $db_conn);
		echo $ret;
	}
}
closedir($dir);
