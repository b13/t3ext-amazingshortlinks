#
# Modify the table structure for core table 'sys_domain'
#
CREATE TABLE sys_domain (
	tx_amazingshortlinks_enable int(1) DEFAULT '0' NOT NULL
);

#
# Create the table structure for table 'tx_amazingshortlinks_domain_model_link'
#
CREATE TABLE tx_amazingshortlinks_domain_model_link (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	createdon int(11) DEFAULT '0' NOT NULL,
	createdby int(11) DEFAULT '0' NOT NULL,
	lastupdated int(11) DEFAULT '0' NOT NULL,

	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,

	domainrecord int(11) DEFAULT '0' NOT NULL,
	shortpath tinytext,
	destination text

	PRIMARY KEY (uid),
	KEY index_parent (pid),
	KEY index_query (pid,hidden,deleted,domainrecord)
);

#
# Create the table structure for table
# 'tx_amazingshortlinks_domain_model_log'
#
CREATE TABLE tx_amazingshortlinks_domain_model_log (
	uid int(11) NOT NULL auto_increment,
	creationdate int(11) DEFAULT '0' NOT NULL,

	ipaddress tinytext,
	useragent tinytext,
	referer tinytext,
	shortlink int(11) default '0' NOT NULL,
	query tinytext

	PRIMARY KEY (uid),

);