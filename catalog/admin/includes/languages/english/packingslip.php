<?php
/*
  $Id: packingslip.php,v 6.1 2005/06/05 17:41:55 PopTheTop Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

//// START Edit the following defines to your liking ////

// Footing
define('INVOICE_TEXT_THANK_YOU', 'Thank you for shopping at'); // Printed at the bottom of your packingslips
define('STORE_URL_ADDRESS', HTTP_CATALOG_SERVER); // Your web address Printed at the bottom of your packingslips

// Image Info
define('INVOICE_IMAGE', DIR_WS_IMAGES.'header_logo'.(file_exists(DIR_WS_IMAGES.'header_logo.gif')?'.gif':'.jpg')); //Change this to match your logo image and foler it is in
define('INVOICE_IMAGE_WIDTH', ''); // Change this to your logo's width
define('INVOICE_IMAGE_HEIGHT', '50'); // Change this to your logo's height
define('INVOICE_IMAGE_ALT_TEXT', STORE_NAME); // Change this to your logo's ALT text or leave blank

// Product Table Info Headings
define('TABLE_HEADING_PRODUCTS_MODEL', 'Model #'); // Change this to "Model #" or leave it as "SKU #"

//// END Editing the above defines to your liking ////

define('TABLE_HEADING_COMMENTS', 'Comments');
define('TABLE_HEADING_PRODUCTS_MODEL', 'Model');
define('TABLE_HEADING_PRODUCTS', 'Products');

define('ENTRY_SOLD_TO', 'SOLD TO:');
define('ENTRY_SHIP_TO', 'SHIP TO:');
define('ENTRY_PAYMENT_METHOD', 'Payment:');

define('INVOICE_NUMBER', 'Invoice #');
define('TITLE_PACKING', 'Packing Slip');
define('ORDER_NUMBER', 'Order #:');
define('ORDER_DATE', 'Date of Order:');
?>