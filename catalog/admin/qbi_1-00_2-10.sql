# Quickbooks Import QBI
# Contribution for osC
# (c) 2005 by Adam Liberman
# Released under the GNU General Public License
#
# version upgrade 1.00 to 2.10
#
# rev May 8, 2005
# --------------------------------------------------------

# Set last line to new version number!


# Changes 1.00 to 2.00

#
# Alter existing tables
#

ALTER TABLE `qbi_config` ADD orders_status_import int(11) NOT NULL default '1';
ALTER TABLE `qbi_config` ADD orders_docnum varchar(36) NOT NULL default '%I';
ALTER TABLE `qbi_config` ADD orders_ponum varchar(36) NOT NULL default '%I';
ALTER TABLE `qbi_config` ADD cust_nameb varchar(41) NOT NULL default '%C10W-%I';
ALTER TABLE `qbi_config` ADD cust_namer varchar(41) NOT NULL default '%L10W-%I';
ALTER TABLE `qbi_config` ADD cust_compcon tinyint(2) unsigned NOT NULL default '1';
ALTER TABLE `qbi_config` ADD invoice_termscc varchar(30) NOT NULL default '';
ALTER TABLE `qbi_config` ADD invoice_pmt tinyint(2) unsigned NOT NULL default '0';
ALTER TABLE `qbi_config` ADD ship_desc varchar(36) NOT NULL default '';
ALTER TABLE `qbi_config` ADD ship_tax tinyint(2) unsigned NOT NULL default '0';
ALTER TABLE `qbi_config` ADD prods_width smallint(5) unsigned NOT NULL default '48';

ALTER TABLE `qbi_config` CHANGE cust_limit cust_limit int(10) unsigned NOT NULL default '0';
ALTER TABLE `qbi_config` CHANGE invoice_rep invoice_rep varchar(41) NOT NULL default '';
ALTER TABLE `qbi_config` CHANGE invoice_fob invoice_fob varchar(13) NOT NULL default '';
ALTER TABLE `qbi_config` CHANGE tax_rate tax_rate float NOT NULL default '0';

ALTER TABLE `qbi_config` DROP invoice_salesrec;
ALTER TABLE `qbi_config` DROP tax_class;
ALTER TABLE `qbi_config` DROP pmts_import;

#
# Table structure for table `qbi_disc`
#

DROP TABLE IF EXISTS qbi_disc;
  CREATE TABLE qbi_disc (
  qbi_disc_id int(10) unsigned NOT NULL auto_increment,
  qbi_disc_refnum int(10) unsigned NOT NULL default '0',
  qbi_disc_name varchar(40) NOT NULL default '',
  qbi_disc_desc varchar(128) NOT NULL default '',
  qbi_disc_accnt varchar(40) NOT NULL default '',
  qbi_disc_price float unsigned NOT NULL default '0',
  qbi_disc_type varchar(16) NOT NULL default '',
  qbi_disc_tax tinyint(2) unsigned NOT NULL default '0',
  added timestamp(14) NOT NULL,
  PRIMARY KEY  (qbi_disc_id),
  KEY qbi_items_refnum (qbi_disc_refnum),
  KEY qbi_items_name (qbi_disc_name),
  KEY qbi_items_desc (qbi_disc_desc),
  KEY qbi_disc_tax (qbi_disc_tax)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `qbi_ot`
#

DROP TABLE IF EXISTS qbi_ot;
CREATE TABLE qbi_ot (
  qbi_ot_id int(10) unsigned NOT NULL auto_increment,
  qbi_ot_mod varchar(48) NOT NULL default '',
  language_id int(10) unsigned NOT NULL default '0',
  qbi_ot_text varchar(48) NOT NULL default '',
  added timestamp(14) NOT NULL,
  PRIMARY KEY  (qbi_ot_id),
  KEY qbi_ot_mod (qbi_ot_mod),
  KEY language_id (language_id),
  KEY qbi_ot_text (qbi_ot_text)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `qbi_ot_disc`
#

DROP TABLE IF EXISTS qbi_ot_disc;
CREATE TABLE qbi_ot_disc (
  qbi_ot_disc_id int(10) unsigned NOT NULL auto_increment,
  qbi_ot_mod varchar(48) NOT NULL default '',
  qbi_disc_refnum int(10) unsigned NOT NULL default '0',
  added timestamp(14) NOT NULL,
  PRIMARY KEY  (qbi_ot_disc_id),
  KEY qbi_ot_id (qbi_ot_mod),
  KEY qbi_disc_id (qbi_disc_refnum)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `qbi_payosc`
#

DROP TABLE IF EXISTS qbi_payosc;
CREATE TABLE qbi_payosc (
  qbi_payosc_id int(10) unsigned NOT NULL auto_increment,
  qbi_payosc_mod varchar(48) NOT NULL default '',
  language_id int(10) unsigned NOT NULL default '0',
  qbi_payosc_text varchar(48) NOT NULL default '',
  added timestamp(14) NOT NULL,
  PRIMARY KEY  (qbi_payosc_id),
  KEY qbi_payosc_file (qbi_payosc_mod),
  KEY language_id (language_id),
  KEY qbi_payosc_text (qbi_payosc_text)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `qbi_payosc_payqb`
#

DROP TABLE IF EXISTS qbi_payosc_payqb;
CREATE TABLE qbi_payosc_payqb (
  qbi_payosc_payqb_id int(10) unsigned NOT NULL auto_increment,
  qbi_payosc_mod varchar(48) NOT NULL default '',
  qbi_payqb_refnum int(10) unsigned NOT NULL default '0',
  added timestamp(14) NOT NULL,
  PRIMARY KEY  (qbi_payosc_payqb_id),
  KEY qbi_payosc_mod (qbi_payosc_mod),
  KEY qbi_payqb_refnum (qbi_payqb_refnum)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `qbi_payqb`
#

DROP TABLE IF EXISTS qbi_payqb;
CREATE TABLE qbi_payqb (
  qbi_payqb_id int(10) unsigned NOT NULL auto_increment,
  qbi_payqb_refnum int(10) unsigned NOT NULL default '0',
  qbi_payqb_name varchar(48) NOT NULL default '',
  qbi_payqb_hidden tinyint(2) unsigned NOT NULL default '0',
  qbi_payqb_type tinyint(2) unsigned NOT NULL default '0',
  added timestamp(14) NOT NULL,
  PRIMARY KEY  (qbi_payqb_id),
  KEY qbi_payqb_refnum (qbi_payqb_refnum),
  KEY qbi_payqb_name (qbi_payqb_name),
  KEY qbi_payqb_hidden (qbi_payqb_hidden),
  KEY qbi_payqb_type (qbi_payqb_type)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `qbi_shiposc`
#

DROP TABLE IF EXISTS qbi_shiposc;
CREATE TABLE qbi_shiposc (
  qbi_shiposc_id int(10) unsigned NOT NULL auto_increment,
  language_id int(10) unsigned NOT NULL default '0',
  qbi_shiposc_car_code varchar(48) NOT NULL default '',
  qbi_shiposc_serv_code varchar(48) NOT NULL default '',
  qbi_shiposc_car_text varchar(48) NOT NULL default '',
  qbi_shiposc_serv_text varchar(48) NOT NULL default '',
  added timestamp(14) NOT NULL,
  PRIMARY KEY  (qbi_shiposc_id),
  KEY language_id (language_id),
  KEY qbi_shiposc_car_code (qbi_shiposc_car_code),
  KEY qbi_shiposc_serv_code (qbi_shiposc_serv_code),
  KEY qbi_shiposc_car_text (qbi_shiposc_car_text),
  KEY qbi_shiposc_serv_text (qbi_shiposc_serv_text)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `qbi_shiposc_shipqb`
#

DROP TABLE IF EXISTS qbi_shiposc_shipqb;
CREATE TABLE qbi_shiposc_shipqb (
  qbi_shiposc_shipqb_refnum int(10) unsigned NOT NULL auto_increment,
  qbi_shiposc_car_code varchar(48) NOT NULL default '',
  qbi_shiposc_serv_code varchar(48) NOT NULL default '',
  qbi_shipqb_refnum int(10) unsigned NOT NULL default '0',
  added timestamp(14) NOT NULL,
  PRIMARY KEY  (qbi_shiposc_shipqb_refnum),
  KEY qbi_shiposc_car_code (qbi_shiposc_car_code),
  KEY qbi_shiposc_serv_code (qbi_shiposc_serv_code),
  KEY qbi_shipqb_refnum (qbi_shipqb_refnum)
) TYPE=MyISAM;
# --------------------------------------------------------


# Changes 1.00 to 1.01

ALTER TABLE `orders` CHANGE `qbi_imported` `qbi_imported` TINYINT(2) UNSIGNED DEFAULT '0' NOT NULL;


# Changes 2.00 to 2.02

# none


# Changes 2.02 to 2.10

ALTER TABLE `qbi_config` ADD qbi_log tinyint(2) unsigned NOT NULL default '0' AFTER `qbi_prod_rows`,
 ADD cust_phone tinyint(2) unsigned NOT NULL default '0' AFTER `cust_compcon`,
 ADD item_match_inv tinyint(2) unsigned NOT NULL default '1' AFTER `item_osc_lang`,
 ADD item_match_noninv tinyint(2) unsigned NOT NULL default '0' AFTER `item_match_inv`,
 ADD item_match_serv tinyint(2) unsigned NOT NULL default '0' AFTER `item_match_noninv`,
 ADD item_default tinyint(2) unsigned NOT NULL default '0' AFTER `item_match_serv`,
 ADD item_default_name varchar(40) NOT NULL default '' AFTER `item_default`,
 ADD item_import_type tinyint(2) unsigned NOT NULL default '0' AFTER `item_default_name`,
 ADD item_active tinyint(2) unsigned NOT NULL default '0' AFTER `item_import_type`;

DELETE FROM `qbi_groups_items`;
ALTER TABLE `qbi_groups_items` DROP INDEX `qbi_groups_id`;
ALTER TABLE `qbi_groups_items` CHANGE `qbi_groups_id` `qbi_groups_refnum` INT(10) UNSIGNED DEFAULT '0' NOT NULL;
ALTER TABLE `qbi_groups_items` ADD INDEX (`qbi_groups_refnum`);

ALTER TABLE `qbi_items` ADD INDEX (`qbi_items_type`);

ALTER TABLE `qbi_shipqb` ADD INDEX (`qbi_shipqb_refnum`);
ALTER TABLE `qbi_shipqb` ADD INDEX (`qbi_shipqb_name`);
ALTER TABLE `qbi_shipqb` ADD INDEX (`qbi_shipqb_hidden`);

ALTER TABLE `products` ADD `qbi_imported` TINYINT(2) UNSIGNED DEFAULT '2' NOT NULL;
ALTER TABLE `products` CHANGE `qbi_imported` `qbi_imported` TINYINT(2) UNSIGNED DEFAULT '0' NOT NULL;
ALTER TABLE `products` ADD INDEX (`qbi_imported`);

DROP TABLE `qbi_taxes`;

#
# Set inactive to force config update
#

UPDATE qbi_config SET qbi_config_active=0;

#
# Set new version number
#

UPDATE qbi_config SET qbi_config_ver=2.10;