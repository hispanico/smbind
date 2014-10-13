CREATE TABLE records (
  id int auto_increment NOT NULL UNIQUE,
  zone int NOT NULL default 0,
  host varchar(255) NOT NULL,
  type varchar(255) NOT NULL,
  pri int NOT NULL default 0,
  destination varchar(255) NOT NULL,
  valid varchar(255) NOT NULL default 'unknown'
);

CREATE TABLE users (
  id int auto_increment NOT NULL UNIQUE,
  username varchar(255) NOT NULL,
  password varchar(255) NOT NULL,
  admin varchar(255) NOT NULL default 'no'
);

INSERT INTO users VALUES (1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'yes');

CREATE TABLE zones (
  id int auto_increment NOT NULL UNIQUE,
  name varchar(255) NOT NULL,
  pri_dns varchar(255),
  sec_dns varchar(255),
  serial int NOT NULL default 0000000000,
  refresh int NOT NULL default 604800,
  retry int NOT NULL default 86400,
  expire int NOT NULL default 2419200,
  ttl int NOT NULL default 604800,
  valid varchar(255) NOT NULL default 'unknown',
  owner int NOT NULL default 1,
  updated varchar(255) NOT NULL default 'yes'
);

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
INSERT INTO options VALUES ('prins','normal','ns1.domain.tld');
INSERT INTO options VALUES ('secns','normal','ns2.domain.tld');
INSERT INTO options VALUES ('hostmaster','normal','hostmaster.domain.tld');

