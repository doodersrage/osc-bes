<?php
/*
  $Id: email_invoice.php,v 6.1 2005/06/05 18:17:59 dgw_ Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

//// START Edit the following defines to your liking ////

// Footing
define('INVOICE_TEXT_THANK_YOU', 'Thank you for shopping at'); // Printed at the bottom of your invoices
define('STORE_URL_ADDRESS', HTTP_SERVER); // Your web address Printed at the bottom of your invoices

// Image Info
define('INVOICE_IMAGE', DIR_WS_IMAGES.'header_logo'.(file_exists(DIR_WS_IMAGES.'header_logo.gif')?'.gif':'.jpg')); //Change this to match your logo image and foler it is in
define('INVOICE_IMAGE_WIDTH', ''); // Change this to your logo's width
define('INVOICE_IMAGE_HEIGHT', '50'); // Change this to your logo's height
define('INVOICE_IMAGE_ALT_TEXT', STORE_NAME); // Change this to your logo's ALT text or leave blank

// Product Table Info Headings
define('TABLE_HEADING_PRODUCTS_MODEL', 'Model #'); // Change this to "Model #" or leave it as "SKU #"

//// END Editing the above defines to your liking ////

// Misc Invoice Info
define('INVOICE_TEXT_NUMBER_SIGN', '#');
define('INVOICE_TEXT_DASH', '-');
define('INVOICE_TEXT_COLON', ':');

define('INVOICE_TEXT_INVOICE', 'Invoice');
define('INVOICE_TEXT_ORDER', 'Order');
define('INVOICE_TEXT_DATE_OF_ORDER', 'Date of Order');
define('ENTRY_PAYMENT_CC_NUMBER', 'Card Number:');
// purchaseorders_1_4 start
define('ENTRY_PURCHASE_ORDER_NUMBER', 'PO Number:');
// purchaseorders_1_4 end

// Customer Info
define('ENTRY_SOLD_TO', 'SOLD TO:');
define('ENTRY_SHIP_TO', 'SHIP TO:');
define('ENTRY_PAYMENT_METHOD', 'Payment:');

// Product Table Info Headings
define('TABLE_HEADING_PRODUCTS', 'Products');
define('TABLE_HEADING_PRICE_EXCLUDING_TAX', 'Price (ex)');
define('TABLE_HEADING_PRICE_INCLUDING_TAX', 'Price (inc)');
define('TABLE_HEADING_TOTAL_EXCLUDING_TAX', 'Total (ex)');
define('TABLE_HEADING_TOTAL_INCLUDING_TAX', 'Total (inc)');
define('TABLE_HEADING_TAX', 'Tax');
define('TABLE_HEADING_UNIT_PRICE', 'Unit Price');
define('TABLE_HEADING_TOTAL', 'Total');

// Order Total Details Info
define('ENTRY_SUB_TOTAL', 'Sub-Total:');
define('ENTRY_SHIPPING', 'Shipping:');
define('ENTRY_TAX', 'Tax:');
define('ENTRY_TOTAL', 'Total:');

//Order Comments
define('TABLE_HEADING_COMMENTS', 'ORDER COMMENTS:');
define('TABLE_HEADING_DATE_ADDED', 'Date Added');
define('TABLE_HEADING_COMMENT_LEFT', 'Comment Left');
define('INVOICE_TEXT_NO_COMMENT', 'No comments have been left for this order');
?>