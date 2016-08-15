# Quickbooks Import QBI
# Contribution for osC
# (c) 2005 by Adam Liberman
# Released under the GNU General Public License
#
# full db install v 2.10
#
# rev May 8, 2005
# --------------------------------------------------------

# Set last line to new version number!
#
# Table structure for table `qbi_config`
#

DROP TABLE IF EXISTS qbi_config;
CREATE TABLE qbi_config (
  qbi_config_id smallint(5) unsigned NOT NULL auto_increment,
  qbi_config_ver decimal(6,2) unsigned NOT NULL default '0.00',
  qbi_qb_ver smallint(5) unsigned NOT NULL default '2003',
  qbi_dl_iif tinyint(2) unsigned NOT NULL default '1',
  qbi_prod_rows smallint(5) unsigned NOT NULL default '5',
  qbi_log tinyint(2) unsigned NOT NULL default '0',
  qbi_status_update tinyint(2) unsigned NOT NULL default '0',
  qbi_cc_status_select tinyint(3) unsigned NOT NULL default '1',
  qbi_mo_status_select tinyint(3) unsigned NOT NULL default '1',
  qbi_email_send tinyint(2) unsigned NOT NULL default '0',
  qbi_cc_clear tinyint(2) unsigned NOT NULL default '0',
  orders_status_import int(11) NOT NULL default '1',
  orders_docnum varchar(36) NOT NULL default '%I',
  orders_ponum varchar(36) NOT NULL default '%I',
  cust_nameb varchar(41) NOT NULL default '%C10W-%I',
  cust_namer varchar(41) NOT NULL default '%L10W-%I',
  cust_limit int(10) unsigned NOT NULL default '0',
  cust_type varchar(48) NOT NULL default '',
  cust_state tinyint(2) unsigned NOT NULL default '1',
  cust_country tinyint(2) unsigned NOT NULL default '0',
  cust_compcon tinyint(2) unsigned NOT NULL default '1',
  cust_phone tinyint(2) unsigned NOT NULL default '0',
  invoice_acct varchar(30) NOT NULL default 'Accounts Receivable',
  invoice_salesacct varchar(30) NOT NULL default 'Undeposited Funds',
  invoice_toprint tinyint(2) unsigned NOT NULL default '1',
  invoice_pmt tinyint(2) unsigned NOT NULL default '0',
  invoice_termscc varchar(30) NOT NULL default '',
  invoice_terms varchar(30) NOT NULL default '',
  invoice_rep varchar(41) NOT NULL default '',
  invoice_fob varchar(13) NOT NULL default '',
  invoice_comments tinyint(2) unsigned NOT NULL default '1',
  invoice_message varchar(128) NOT NULL default '',
  invoice_memo varchar(128) NOT NULL default '',
  item_acct varchar(30) NOT NULL default '',
  item_asset_acct varchar(30) NOT NULL default 'Inventory Asset',
  item_class varchar(30) NOT NULL default '',
  item_cog_acct varchar(30) NOT NULL default 'Cost of Goods Sold',
  item_osc_lang tinyint(2) unsigned NOT NULL default '0',
  item_match_inv tinyint(2) unsigned NOT NULL default '1',
  item_match_noninv tinyint(2) unsigned NOT NULL default '0',
  item_match_serv tinyint(2) unsigned NOT NULL default '0',
  item_default tinyint(2) unsigned NOT NULL default '0',
  item_default_name varchar(40) NOT NULL default '',
  item_import_type tinyint(2) unsigned NOT NULL default '0',
  item_active tinyint(2) unsigned NOT NULL default '0',
  ship_acct varchar(30) NOT NULL default '',
  ship_name varchar(30) NOT NULL default '',
  ship_desc varchar(36) NOT NULL default '',
  ship_class varchar(30) NOT NULL default '',
  ship_tax tinyint(2) unsigned NOT NULL default '0',
  tax_on tinyint(2) unsigned NOT NULL default '0',
  tax_lookup tinyint(2) unsigned NOT NULL default '0',
  tax_name varchar(30) NOT NULL default '',
  tax_agency varchar(30) NOT NULL default '',
  tax_rate float NOT NULL default '0',
  pmts_memo varchar(128) NOT NULL default '',
  prods_sort tinyint(2) unsigned NOT NULL default '0',
  prods_width smallint(5) unsigned NOT NULL default '48',
  qbi_config_active tinyint(2) NOT NULL default '0',
  qbi_config_added timestamp(14) NOT NULL,
  PRIMARY KEY  (qbi_config_id)
) TYPE=MyISAM;
# --------------------------------------------------------

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
# Table structure for table `qbi_groups`
#

DROP TABLE IF EXISTS qbi_groups;
CREATE TABLE qbi_groups (
  qbi_groups_id int(10) unsigned NOT NULL auto_increment,
  qbi_groups_refnum int(10) unsigned NOT NULL default '0',
  qbi_groups_name varchar(40) NOT NULL default '',
  qbi_groups_desc varchar(128) NOT NULL default '',
  qbi_groups_toprint tinyint(2) unsigned NOT NULL default '0',
  added timestamp(14) NOT NULL,
  PRIMARY KEY  (qbi_groups_id),
  KEY qbi_groups_refnum (qbi_groups_refnum)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `qbi_groups_items`
#

DROP TABLE IF EXISTS qbi_groups_items;
CREATE TABLE qbi_groups_items (
  qbi_groups_items_id int(10) unsigned NOT NULL auto_increment,
  qbi_groups_refnum int(10) unsigned NOT NULL default '0',
  qbi_items_refnum int(10) unsigned NOT NULL default '0',
  qbi_groups_items_quan float unsigned NOT NULL default '0',
  added timestamp(14) NOT NULL,
  PRIMARY KEY  (qbi_groups_items_id),
  KEY qbi_groups_ref (qbi_groups_refnum),
  KEY qbi_items_refnum (qbi_items_refnum)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `qbi_items`
#

DROP TABLE IF EXISTS qbi_items;
CREATE TABLE qbi_items (
  qbi_items_id int(10) unsigned NOT NULL auto_increment,
  qbi_items_refnum int(10) unsigned NOT NULL default '0',
  qbi_items_name varchar(40) NOT NULL default '',
  qbi_items_desc varchar(128) NOT NULL default '',
  qbi_items_accnt varchar(40) NOT NULL default '',
  qbi_items_price float unsigned NOT NULL default '0',
  qbi_items_type varchar(16) NOT NULL default '',
  added timestamp(14) NOT NULL,
  PRIMARY KEY  (qbi_items_id),
  KEY qbi_items_refnum (qbi_items_refnum),
  KEY qbi_items_name (qbi_items_name),
  KEY qbi_items_desc (qbi_items_desc),
  KEY qbi_items_type (qbi_items_type)
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
# Table structure for table `qbi_products_items`
#

DROP TABLE IF EXISTS qbi_products_items;
CREATE TABLE qbi_products_items (
  qbi_products_items_id int(10) unsigned NOT NULL auto_increment,
  products_id int(10) unsigned NOT NULL default '0',
  products_options_values_id int(10) unsigned NOT NULL default '0',
  qbi_groupsitems_refnum int(10) unsigned NOT NULL default '0',
  added timestamp(14) NOT NULL,
  PRIMARY KEY  (qbi_products_items_id),
  KEY products_id (products_id),
  KEY qbi_groupsitems_refnum (qbi_groupsitems_refnum),
  KEY products_options_values_id (products_options_values_id)
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

#
# Table structure for table `qbi_shipqb`
#

DROP TABLE IF EXISTS qbi_shipqb;
CREATE TABLE qbi_shipqb (
  qbi_shipqb_id int(10) unsigned NOT NULL auto_increment,
  qbi_shipqb_refnum int(10) unsigned NOT NULL default '0',
  qbi_shipqb_name varchar(16) NOT NULL default '',
  qbi_shipqb_hidden tinyint(2) unsigned NOT NULL default '0',
  added timestamp(14) NOT NULL,
  PRIMARY KEY  (qbi_shipqb_id),
  KEY qbi_shipqb_refnum (qbi_shipqb_refnum),
  KEY qbi_shipqb_name (qbi_shipqb_name),
  KEY qbi_shipqb_hidden (qbi_shipqb_hidden)
) TYPE=MyISAM;

#
# Alter existing tables
#

ALTER TABLE `orders` ADD `qbi_imported` TINYINT(2) UNSIGNED DEFAULT '2' NOT NULL;
ALTER TABLE `orders` CHANGE `qbi_imported` `qbi_imported` TINYINT(2) UNSIGNED DEFAULT '0' NOT NULL;
ALTER TABLE `orders` ADD INDEX (`qbi_imported`);

ALTER TABLE `products` ADD `qbi_imported` TINYINT(2) UNSIGNED DEFAULT '2' NOT NULL;
ALTER TABLE `products` CHANGE `qbi_imported` `qbi_imported` TINYINT(2) UNSIGNED DEFAULT '0' NOT NULL;
ALTER TABLE `products` ADD INDEX (`qbi_imported`);

#
# Dumping data for table `qbi_config`
#

INSERT INTO `qbi_config` SET qbi_config_ver=2.10;
