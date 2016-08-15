<?php

/*

  $Id: checkout_process.php,v 1.26 2002/11/01 04:22:05 hpdl Exp $



  osCommerce, Open Source E-Commerce Solutions

  http://www.oscommerce.com



  Copyright (c) 2002 osCommerce



  Released under the GNU General Public License

*/



define('EMAIL_TEXT_SUBJECT', 'Order Process');

define('EMAIL_TEXT_ORDER_NUMBER', 'Order Number:');

define('EMAIL_TEXT_INVOICE_URL', 'Detailed Invoice:');

define('EMAIL_TEXT_DATE_ORDERED', 'Date Ordered:');

define('EMAIL_TEXT_PRODUCTS', 'Products');

define('EMAIL_TEXT_SUBTOTAL', 'Sub-Total:');

define('EMAIL_TEXT_TAX', 'Tax:        ');

define('EMAIL_TEXT_SHIPPING', 'Shipping: ');

define('EMAIL_TEXT_TOTAL', 'Total:    ');

define('EMAIL_TEXT_DELIVERY_ADDRESS', 'Delivery Address');

define('EMAIL_TEXT_BILLING_ADDRESS', 'Billing Address');

define('EMAIL_TEXT_PAYMENT_METHOD', 'Payment Method');

// purchaseorders_1_4 start

define('EMAIL_TEXT_PURCHASE_ORDER_NUMBER',      'Purchase Order No: ');

// purchaseorders_1_4 end



define('EMAIL_SEPARATOR', '------------------------------------------------------');

define('TEXT_EMAIL_VIA', 'via');



// multiple orders

define('TEXT_MULTIPLE_ORDER_HEADER_SUCCESS', 'Your unpaid order was submitted. Please modify cart as needed and checkout again to submit next order.');



define('MODULE_PAYMENT_RFQ2_TEXT_EMAIL_FOOTER', 'Based upon the weight of your order, and the best shipping method available to your location, we will provide you with a shipping cost quote. <br><br> Upon receiving your quote, you may choose to process your order. If so, you will need to contact us with your payment information.  Your order will not ship until the full payment is received.');

?>