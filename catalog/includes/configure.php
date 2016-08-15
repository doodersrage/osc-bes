<?php
/*
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

// Define the webserver and path parameters
// * DIR_FS_* = Filesystem directories (local/physical)
// * DIR_WS_* = Webserver directories (virtual/URL)
  define('HTTP_SERVER', 'http://www.boatequipmentsuperstore.com'); // eg, http://localhost - should not be empty for productive servers
  define('HTTPS_SERVER', 'https://www.boatequipmentsuperstore.com'); // eg, https://localhost - should not be empty for productive servers
  define('ENABLE_SSL', true); // secure webserver for checkout procedure?
  define('HTTP_COOKIE_DOMAIN', 'boatequipmentsuperstore.com');
  define('HTTPS_COOKIE_DOMAIN', 'boatequipmentsuperstore.com');
  define('HTTP_COOKIE_PATH', '/');
  define('HTTPS_COOKIE_PATH', '');
  define('DIR_WS_HTTP_CATALOG', '/catalog/');
  define('DIR_WS_HTTPS_CATALOG', '/catalog/');
  define('DIR_WS_IMAGES', 'images/');
  define('DIR_WS_ICONS', DIR_WS_IMAGES . 'icons/');
  define('DIR_WS_INCLUDES', 'includes/');
  define('DIR_WS_BOXES', DIR_WS_INCLUDES . 'boxes/');
  define('DIR_WS_FUNCTIONS', DIR_WS_INCLUDES . 'functions/');
  define('DIR_WS_CLASSES', DIR_WS_INCLUDES . 'classes/');
  define('DIR_WS_MODULES', DIR_WS_INCLUDES . 'modules/');
  define('DIR_WS_LANGUAGES', DIR_WS_INCLUDES . 'languages/');

  define('DIR_WS_DOWNLOAD_PUBLIC', 'pub/');
  define('DIR_FS_CATALOG', '/var/www/bes/catalog/');
  define('DIR_FS_DOWNLOAD', DIR_FS_CATALOG . 'download/');
  define('DIR_FS_DOWNLOAD_PUBLIC', DIR_FS_CATALOG . 'pub/');
  define('DIRNAME_IMAGECACHE', 'cache/');  // added by splautz

// define our database connection
  define('DB_SERVER', 'localhost'); // eg, localhost - should not be empty for productive servers
  define('DB_SERVER_USERNAME', 'bes_dbadmin');
  define('DB_SERVER_PASSWORD', 'bespass');
  define('DB_DATABASE', 'bes_osc');
  define('USE_PCONNECT', 'false'); // use persistent connections?
  define('STORE_SESSIONS', 'mysql'); // leave empty '' for default handler or set to 'mysql'

  define('RESOURCES_LINK', HTTP_SERVER . '/site-resources/');

// STS: ADD: Define Simple Template System files
  define('STS_START_CAPTURE', DIR_WS_INCLUDES . 'sts_start_capture.php');
  define('STS_STOP_CAPTURE', DIR_WS_INCLUDES . 'sts_stop_capture.php'); 
  define('STS_RESTART_CAPTURE', DIR_WS_INCLUDES . 'sts_restart_capture.php');
  define('STS_TEMPLATE_DIR', DIR_WS_INCLUDES . 'sts_templates/');
  define('STS_DEFAULT_TEMPLATE', DIR_WS_INCLUDES . 'sts_template.html'); 
  define('STS_DISPLAY_OUTPUT', DIR_WS_INCLUDES . 'sts_display_output.php');
  define('STS_USER_CODE', DIR_WS_INCLUDES . 'sts_user_code.php');
  define('STS_PRODUCT_INFO', DIR_WS_INCLUDES . 'sts_product_info.php');
// STS: EOADD

// added for template invoicing
  define('EMAIL_INVOICE_DIR', 'email_invoice/');
  define('INVOICE_TEMPLATE_DIR', 'templates/');
// end add for template invoicing

// CLR 020605 defines needed for Product Option Type feature.
  define('PRODUCTS_OPTIONS_TYPE_SELECT', 0);
  define('PRODUCTS_OPTIONS_TYPE_TEXT', 1);
  define('PRODUCTS_OPTIONS_TYPE_RADIO', 2);
  define('PRODUCTS_OPTIONS_TYPE_CHECKBOX', 3);
  define('PRODUCTS_OPTIONS_TYPE_TEXTAREA', 4);
  define('TEXT_PREFIX', 'txt_');
  define('PRODUCTS_OPTIONS_VALUE_TEXT_ID', 0);  //Must match id for user defined "TEXT" value in db table TABLE_PRODUCTS_OPTIONS_VALUES
?>
