<?php
/*
  $Id: header_tags.php,v 1.6 2005/04/10 14:07:36 hpdl Exp $
  Created by Jack York from http://www.oscommerce-solution.com
  
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
define('HEADING_TITLE_CONTROLLER', 'Header Tags Controller');
define('HEADING_TITLE_EDIT_TAGS', 'Header Tags - Edit Tags');
define('HEADING_TITLE_FILL_TAGS', 'Header Tags - Fill Tags');
define('TEXT_INFORMATION_ADD_PAGE', '<b>Add a New Page</b> - This option adds the code for a page into the file mentioned 
above. Note that it does not add an actual page. To add a page, enter the name of the file, with or without the .php extension..');
define('TEXT_INFORMATION_DELETE_PAGE', '<b>Delete a New Page</b> - This option will remove the code for a page from the
above file.'); 
define('TEXT_INFORMATION_CHECK_PAGES', '<b>Check Missing Pages</b> - This option allows you to check which files in your
shop do not have an entry in the above file. Note that not all pages should have an entry. For example,
any page that will use SSL like Login or Create Account. To view the pages, click Update and then select the drop down list.'); 

define('TEXT_PAGE_TAGS', 'In order for Header Tags to display information on a page, an entry for that
page must be made into the includes/languages/english/header_tags.php file (where english would be the language you are using).
 The options on this page will allow you to add, delete and check the code in this file.');
define('TEXT_EDIT_TAGS', 'The main purpose of Header Tags is to give each of the pages in your shop a 
unique title and meta tags. The default settings will not do your shop any good and need to 
be changed on this page. Change them to use the main keywords you have chosen to use for your shop. 
The individual sections are named after the page they belong to. Some pages are used for displaying one 
of a number of items, such as categories & manufacturers (index), products (product_info) & informational 
screens (pages). You can define tags specific for each item in the product catalog or page editor area. 
Any tags defined for index, product_info, & pages here will only be used as a default in case the more 
specific tag information defined elsewhere is empty.');
define('TEXT_FILL_TAGS', 'This option allows you to fill in the meta tags added by
Header Tags. Select the appropriate setting for both the categories and products tags
and then click Update. If you select the Fill Only Empty Tags, then tags already
filled in will not be overwritten. If the Fill products meta description with Products Description option is
chosen, then the meta description tag will be filled with the products description (including short description).
 If a number is entered into the length box, the description will be truncated to that length.');

// header_tags_controller.php & header_tags_edit.php
define('HEADING_TITLE_CONTROLLER_EXPLAIN', '(Explain)');
define('HEADING_TITLE_CONTROLLER_TITLE', 'Title:');
define('HEADING_TITLE_CONTROLLER_DESCRIPTION', 'Description:');
define('HEADING_TITLE_CONTROLLER_KEYWORDS', 'Keyword(s):');
define('HEADING_TITLE_CONTROLLER_H1', 'H1 Line:');
define('HEADING_TITLE_CONTROLLER_SURL', 'URL Name:');
define('HEADING_TITLE_CONTROLLER_PAGENAME', 'Page Name:');
define('HEADING_TITLE_CONTROLLER_PAGENAME_ERROR', 'Page name is already entered -> ');
define('HEADING_TITLE_CONTROLLER_PAGENAME_INVALID_ERROR', 'Page name is invalid -> ');
define('HEADING_TITLE_CONTROLLER_NO_DELETE_ERROR', 'Deleting %s is not allowed');

// header_tags_edit.php
define('HEADING_TITLE_CONTROLLER_DEFAULT_TITLE', 'Default Title:');
define('HEADING_TITLE_CONTROLLER_DEFAULT_DESCRIPTION', 'Default Description:');
define('HEADING_TITLE_CONTROLLER_DEFAULT_KEYWORDS', 'Default Keyword(s):');
define('HEADING_TITLE_CONTROLLER_DEFAULT_H1', 'Default H1 Line:');
// header_tags_fill_tags.php
define('HEADING_TITLE_CONTROLLER_CATEGORIES', 'CATEGORIES');
define('HEADING_TITLE_CONTROLLER_MANUFACTURERS', 'MANUFACTURERS');
define('HEADING_TITLE_CONTROLLER_PRODUCTS', 'PRODUCTS');
define('HEADING_TITLE_CONTROLLER_SKIPALL', 'Skip all tags');
define('HEADING_TITLE_CONTROLLER_FILLONLY', 'Fill only empty tags');
define('HEADING_TITLE_CONTROLLER_FILLALL', 'Fill all tags');
define('HEADING_TITLE_CONTROLLER_CLEARALL', 'Clear all tags');
?>
