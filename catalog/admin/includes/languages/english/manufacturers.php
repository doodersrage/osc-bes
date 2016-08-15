<?php
/*
  $Id: manufacturers.php,v 1.10 2002/08/19 01:58:58 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('HEADING_TITLE', 'Manufacturers');

define('TABLE_HEADING_ID', 'ID');  // added by splautz
define('TABLE_HEADING_MANUFACTURERS', 'Manufacturers');
define('TABLE_HEADING_ACTION', 'Action');
define('TABLE_HEADING_STATUS', 'Status');  // added by splautz
define('TABLE_HEADING_SORT', 'Sort');  // added by splautz

define('TEXT_HEADING_NEW_MANUFACTURER', 'New Manufacturer');
define('TEXT_HEADING_EDIT_MANUFACTURER', 'Edit Manufacturer');
define('TEXT_HEADING_DELETE_MANUFACTURER', 'Delete Manufacturer');

define('TEXT_MANUFACTURERS', 'Manufacturers:');
define('TEXT_DATE_ADDED', 'Date Added:');
define('TEXT_LAST_MODIFIED', 'Last Modified:');
define('TEXT_PRODUCTS', 'Products:');
define('TEXT_IMAGE_NONEXISTENT', 'IMAGE DOES NOT EXIST');

define('TEXT_NEW_INTRO', 'Please fill out the following information for the new manufacturer');
define('TEXT_EDIT_INTRO', 'Please make any necessary changes');

define('TEXT_MANUFACTURERS_NAME', 'Manufacturers Name:');
define('TEXT_MANUFACTURERS_SORT_ORDER', 'Sort Order:');  // added by splautz
define('TEXT_MANUFACTURERS_STATUS', 'Status:');  // added by splautz
define('TEXT_MANUFACTURERS_IMAGE', 'Manufacturers Image:');
define('TEXT_MANUFACTURERS_URL', 'Manufacturers URL:');
// added by splautz
define('TEXT_MANUFACTURERS_IMG_ALT', 'Manufacturers Img Alt Text:');
define('TEXT_MANUFACTURERS_INTRO', 'Manufacturers Title/Intro Text:');
define('TEXT_MANUFACTURERS_BODY', 'Manufacturers Body Text:');
define('TEXT_MANUFACTURERS_BODY2', 'Manufacturers Footer Text:');
define('TEXT_MANUFACTURERS_METTA_INFO', '<b>Meta Tag/SEO Information</b>');
define('TEXT_MANUFACTURERS_HEAD_TITLE', 'Manufacturer Header Title:');
define('TEXT_MANUFACTURERS_HEAD_DESCRIPTION', 'Manufacturer Header Desc:');
define('TEXT_MANUFACTURERS_HEAD_KEYWORDS', 'Manufacturer Header Keywords:');
define('TEXT_MANUFACTURERS_NAME_URL', 'Manufacturer URL Name:');
define('TEXT_MANUFACTURERS_H1', 'Manufacturer H1 Line:');

define('TEXT_DELETE_INTRO', 'Are you sure you want to delete this manufacturer?');
define('TEXT_DELETE_IMAGE', 'Delete manufacturers image?');
define('TEXT_DELETE_PRODUCTS', 'Delete products from this manufacturer? (including product reviews, products on special, upcoming products)');
define('TEXT_DELETE_WARNING_PRODUCTS', '<b>WARNING:</b> There are %s products still linked to this manufacturer!');

define('ERROR_DIRECTORY_NOT_WRITEABLE', 'Error: I can not write to this directory. Please set the right user permissions on: %s');
define('ERROR_DIRECTORY_DOES_NOT_EXIST', 'Error: Directory does not exist: %s');
?>