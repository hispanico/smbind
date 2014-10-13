<?php
// Include paths.
$_CONF['smarty_path']	= "/usr/share/smarty";
$_CONF['peardb_path']	= "/usr/share/pear";

// Database DSN.
$_CONF['db_type']	= "mysql"; // mysql for MySQL, pgsql for PostgreSQL
$_CONF['db_user']	= "smbind";
$_CONF['db_pass']	= "";
$_CONF['db_host']	= "localhost";
$_CONF['db_db']		= "smbind";

// Zone data paths (normal).
$_CONF['path'] 		= "/var/named/";
$_CONF['conf']		= "/etc/smbind/smbind.conf"; # Include this file in named.conf.

// Zone data paths (chroot).
#$_CONF['path']		= "/var/named/chroot/var/named/";
#$_CONF['conf']		= "/var/named/chroot/etc/smbind/smbind.conf"; # Include this file in named.conf.
                                                                                                               
// BIND utilities. 
$_CONF['namedcheckconf'] = "/usr/sbin/named-checkconf"; 
$_CONF['namedcheckzone'] = "/usr/sbin/named-checkzone"; 
$_CONF['rndc'] 	  	 = "/usr/sbin/rndc";
?>
