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
  define('HTTP_CATALOG_SERVER', 'http://www.boatequipmentsuperstore.com');
  define('HTTPS_CATALOG_SERVER', '');
  define('ENABLE_SSL_CATALOG', 'false'); // secure webserver for catalog module
  define('DIR_FS_DOCUMENT_ROOT', '/var/www/bes/catalog/'); // where the pages are located on the server
  define('DIR_WS_ADMIN', '/catalog/admin/'); // absolute path required
  define('DIR_FS_ADMIN', '/var/www/bes/catalog/admin/'); // absolute path required
  define('DIR_WS_CATALOG', '/catalog/'); // absolute path required
  define('DIR_FS_CATALOG', '/var/www/bes/catalog/'); // absolute path required
  define('DIR_WS_IMAGES', 'images/');
  define('DIR_WS_ICONS', DIR_WS_IMAGES . 'icons/');
  define('DIR_WS_CATALOG_IMAGES', DIR_WS_CATALOG . 'images/');
  define('DIR_WS_INCLUDES', 'includes/');
  define('DIR_WS_BOXES', DIR_WS_INCLUDES . 'boxes/');
  define('DIR_WS_FUNCTIONS', DIR_WS_INCLUDES . 'functions/');
  define('DIR_WS_CLASSES', DIR_WS_INCLUDES . 'classes/');
  define('DIR_WS_MODULES', DIR_WS_INCLUDES . 'modules/');
  define('DIR_WS_LANGUAGES', DIR_WS_INCLUDES . 'languages/');
  define('DIR_WS_CATALOG_LANGUAGES', DIR_WS_CATALOG . 'includes/languages/');
  define('DIR_FS_CATALOG_LANGUAGES', DIR_FS_CATALOG . 'includes/languages/');
  define('DIR_FS_CATALOG_IMAGES', DIR_FS_CATALOG . 'images/');
  define('DIR_FS_CATALOG_MODULES', DIR_FS_CATALOG . 'includes/modules/');
  define('DIR_FS_BACKUP', DIR_FS_ADMIN . 'backups/');
  define('DIRNAME_IMAGECACHE', 'cache/');  // added by splautz

// define our database connection
  define('DB_SERVER', 'localhost'); // eg, localhost - should not be empty for productive servers
  define('DB_SERVER_USERNAME', 'bes_dbadmin');
  define('DB_SERVER_PASSWORD', 'bespass');
  define('DB_DATABASE', 'bes_osc');
  define('USE_PCONNECT', 'false'); // use persisstent connections?
  define('STORE_SESSIONS', 'mysql'); // leave empty '' for default handler or set to 'mysql'

// added by splautz to define Product Inventory category ID
  define('PRODUCT_INVENTORY_CATID', 1);  // usually 1 unless conflict during import

// added by splautz in case needed for rewrite rules
  define('SURLS_SCRIPT', HTTP_SERVER.'/cgi-bin/accupdate.cgi');  // custom script to run after surl updates; leave empty if not needed
  define('SURLS_TIMEOUT', 10);  // number seconds to wait for script to run
  define('SURLS_CURL', 'FOPEN');  // FOPEN = use fopen method; CURL = use php internal curl; or specify a path to command line curl
?>
