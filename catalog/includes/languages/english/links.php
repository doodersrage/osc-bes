<?php
/*
  $Id: links.php,v 1.00 2003/10/03 Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

define('NAVBAR_TITLE', 'Links');

define('TEXT_MAIN_CATEGORIES', 'Welcome to our link exchange program.');
define('TEXT_MAIN_LINKS', 'Below is our list of links for the %s category.');
define('TEXT_FEATURED_HEADING', 'Featured Link');

if ($display_mode == 'links') {
  define('HEADING_TITLE', 'Links');
  define('TABLE_HEADING_LINKS_IMAGE', '');
  define('TABLE_HEADING_LINKS_TITLE', 'Title');
  define('TABLE_HEADING_LINKS_URL', 'URL');
  define('TABLE_HEADING_LINKS_DESCRIPTION', 'Description');
  define('TABLE_HEADING_LINKS_COUNT', 'Clicks');
  define('TEXT_NO_LINKS', 'There are no links to list in this category.');
} elseif ($display_mode == 'categories') {
  define('HEADING_TITLE', 'Link Categories');
  define('TEXT_NO_CATEGORIES', 'There are no link categories to list yet.');
  define('TEXT_FIND_LINK', 'If you cannot find the link you are looking for, please try the
  search function below.');
}

// VJ todo - move to common language file
define('TEXT_DISPLAY_NUMBER_OF_LINKS', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> links)');

define('IMAGE_BUTTON_SUBMIT_LINK', 'Submit Link');
?>
