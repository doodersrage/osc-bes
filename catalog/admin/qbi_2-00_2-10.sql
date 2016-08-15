# Quickbooks Import QBI
# Contribution for osC
# (c) 2005 by Adam Liberman
# Released under the GNU General Public License
#
# version upgrade 2.02 to 2.10
#
# rev May 8, 2005
# --------------------------------------------------------

# Set last line to new version number!

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