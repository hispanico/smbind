CREATE TABLE records (
  id int auto_increment NOT NULL AUTO_INCREMENT,
  zone int NOT NULL default 0,
  host varchar(255) NOT NULL,
  ttl int default null,
  type varchar(16) NOT NULL,
  pri int NOT NULL default 0,
  num1 int NOT NULL default 0,
  num2 int NOT NULL default 0,
  num3 int NOT NULL default 0,
  destination varchar(255) NOT NULL,
  txt text default null,
  valid enum('unknown', 'yes', 'no') not null default 'unknown',
  primary key(id)
) ROW_FORMAT=COMPRESSED;

CREATE TABLE users (
  id int auto_increment NOT NULL AUTO_INCREMENT,
  username varchar(255) NOT NULL,
  password varchar(255) NOT NULL,
  admin enum('yes', 'no') not null default 'no',
  primary key(id)
);

INSERT INTO users VALUES (1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'yes');

CREATE TABLE zones (
  id int auto_increment NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL,
  pri_dns varchar(255),
  sec_dns varchar(255),
  ter_dns varchar(255),
  ns_ttl int NOT NULL DEFAULT 86400,
  serial int NOT NULL default 0000000000,
  refresh int NOT NULL default 604800,
  retry int NOT NULL default 86400,
  expire int NOT NULL default 2419200,
  ttl int NOT NULL default 604800,
  nttl int NOT NULL DEFAULT 600,
  valid enum('unknown', 'yes', 'no') not null default 'unknown',
  owner int NOT NULL default 1,
  updated enum('yes', 'no') not null default 'yes',
  deleted enum('yes','no') NOT NULL DEFAULT 'no',
  comment varchar(40) DEFAULT NULL,
  notes text,
  primary key(id)
) ROW_FORMAT=COMPRESSED;

CREATE TABLE options (
  prefkey varchar(255) NOT NULL UNIQUE,
  preftype varchar(255) NOT NULL default '',
  prefval varchar(255) default NULL
);

INSERT INTO options VALUES ('A','record','on');
INSERT INTO options VALUES ('NS','record','on');
INSERT INTO options VALUES ('CNAME','record','on');
INSERT INTO options VALUES ('PTR','record','on');
INSERT INTO options VALUES ('MX','record','on');
INSERT INTO options VALUES ('AAAA','record','on');
INSERT INTO options VALUES ('WKS','record','off');
INSERT INTO options VALUES ('HINFO','record','off');
INSERT INTO options VALUES ('MINFO','record','off');
INSERT INTO options VALUES ('TXT','record','on');
INSERT INTO options VALUES ('RP','record','off');
INSERT INTO options VALUES ('AFSDB','record','off');
INSERT INTO options VALUES ('X25','record','off');
INSERT INTO options VALUES ('ISDN','record','off');
INSERT INTO options VALUES ('RT','record','off');
INSERT INTO options VALUES ('NSAP','record','off');
INSERT INTO options VALUES ('NSAP-PTR','record','off');
INSERT INTO options VALUES ('SIG','record','off');
INSERT INTO options VALUES ('KEY','record','off');
INSERT INTO options VALUES ('PX','record','off');
INSERT INTO options VALUES ('GPOS','record','off');
INSERT INTO options VALUES ('LOC','record','off');
INSERT INTO options VALUES ('NXT','record','off');
INSERT INTO options VALUES ('EID','record','off');
INSERT INTO options VALUES ('NIMLOC','record','off');
INSERT INTO options VALUES ('SRV','record','on');
INSERT INTO options VALUES ('ATMA','record','off');
INSERT INTO options VALUES ('NAPTR','record','off');
INSERT INTO options VALUES ('KX','record','off');
INSERT INTO options VALUES ('CERT','record','off');
INSERT INTO options VALUES ('A6','record','off');
INSERT INTO options VALUES ('DNAME','record','off');
INSERT INTO options VALUES ('SINK','record','off');
INSERT INTO options VALUES ('OPT','record','off');
INSERT INTO options VALUES ('APL','record','off');
INSERT INTO options VALUES ('DS','record','off');
INSERT INTO options VALUES ('SSHFP','record','off');
INSERT INTO options VALUES ('RRSIG','record','off');
INSERT INTO options VALUES ('NSEC','record','off');
INSERT INTO options VALUES ('DNSKEY','record','off');
INSERT INTO options VALUES ('TKEY','record','off');
INSERT INTO options VALUES ('TSIG','record','off');
INSERT INTO options VALUES ('IXFR','record','off');
INSERT INTO options VALUES ('AXFR','record','off');
INSERT INTO options VALUES ('MAILB','record','off');
INSERT INTO options VALUES ('500_prins','normal','ns1.domain.tld');
INSERT INTO options VALUES ('501_secns','normal','ns2.domain.tld');
INSERT INTO options VALUES ('502_terns','normal','ns3.domain.tld');
insert into options values ('509_nsttl', 'normal', '86400');
INSERT INTO options VALUES ('510_hostmaster','normal','hostmaster.domain.tld');
INSERT INTO options VALUES ('650_range','normal','100');

create table flags (
  flagname varchar(40) not null unique,
  flagvalue varchar(40) not null default ''
);
insert into flags (flagname, flagvalue) values ('rebuild_zones', '0');
