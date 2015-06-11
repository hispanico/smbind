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

// Slaves
$_CONF['conf_slave'] = "/etc/smbind/smbind-slave.conf"; #create locally at this path, and push to this remote path
$_CONF['slaves'] = array(
	// master address (e.g. this host):
	'10.10.20.1' => array(
		// slaves pulling from this master
		'172.16.10.1',
		'172.16.10.2',
		'172.16.20.10',
	),
);
$_CONF['slave_ssh_key'] = '/etc/smbind/id_ed25519'; #ssh private key used to connect to slaves
$_CONF['slave_user'] = 'smbind'; #ssh user on slave

// BIND utilities.
$_CONF['namedcheckconf'] = "/usr/sbin/named-checkconf";
$_CONF['namedcheckzone'] = "/usr/sbin/named-checkzone";
$_CONF['rndc'] 	  	 = "/usr/sbin/rndc";
?>
