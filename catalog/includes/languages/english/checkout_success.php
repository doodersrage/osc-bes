<?php

/*

  $Id: checkout_success.php,v 1.12 2003/04/15 17:47:42 dgw_ Exp $



  osCommerce, Open Source E-Commerce Solutions

  http://www.oscommerce.com



  Copyright (c) 2002 osCommerce



  Released under the GNU General Public License

*/



define('NAVBAR_TITLE_1', 'Checkout');

define('NAVBAR_TITLE_2', 'Success');



define('HEADING_TITLE', 'Your Order Has Been Processed!');



define('TEXT_SUCCESS', 'You will be receiving an email confirmation shortly.');

define('TEXT_NOTIFY_PRODUCTS', 'Please notify me of updates to the products I have selected below:');

define('TEXT_SEE_ORDERS', 'You can view your order history if you logged in with an account, by going to the <a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL'). '">\'My Account\'</a> page and by clicking on <a href="' . tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL') . '">\'History\'</a>.');

define('TEXT_CONTACT_STORE_OWNER', 'Please <a href="' . tep_href_link(FILENAME_CONTACT_US) . '">contact</a> us with any questions about your order.');

define('TEXT_THANKS_FOR_SHOPPING', 'Thanks for shopping at Boat Equipment Superstore!');



define('TABLE_HEADING_COMMENTS', 'Enter a comment for the order processed');



define('TABLE_HEADING_DOWNLOAD_DATE', 'Expiry date: ');

define('TABLE_HEADING_DOWNLOAD_COUNT', ' downloads remaining');

define('HEADING_DOWNLOAD', 'Download your products here:');

define('FOOTER_DOWNLOAD', 'You can also download your products at a later time at \'%s\'');

?>