<?php
/*
  $Id: filenames.php,v 1.1 2003/06/20 00:18:30 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

// define the filenames used in the project
  define('FILENAME_KEYWORDS', 'stats_keywords.php');
  define('FILENAME_BACKUP', 'backup.php');
  define('FILENAME_BANNER_MANAGER', 'banner_manager.php');
  define('FILENAME_BANNER_STATISTICS', 'banner_statistics.php');
  define('FILENAME_CACHE', 'cache.php');
  define('FILENAME_CATALOG_ACCOUNT_HISTORY_INFO', 'account_history_info.php');
  define('FILENAME_CATEGORIES', 'categories.php');
  define('FILENAME_CONFIGURATION', 'configuration.php');
  define('FILENAME_COUNTRIES', 'countries.php');
  define('FILENAME_CURRENCIES', 'currencies.php');
  define('FILENAME_CUSTOMERS', 'customers.php');
  define('FILENAME_DEFAULT', 'index.php');
  define('FILENAME_DEFINE_LANGUAGE', 'define_language.php');
  define('FILENAME_FILE_MANAGER', 'file_manager.php');
  define('FILENAME_GEO_ZONES', 'geo_zones.php');
  define('FILENAME_HEADER_TAGS_CONTROLLER', 'header_tags_controller.php');
  define('FILENAME_HEADER_TAGS_EDIT', 'header_tags_edit.php');
  define('FILENAME_HEADER_TAGS_FILL_TAGS', 'header_tags_fill_tags.php');
  define('FILENAME_LANGUAGES', 'languages.php');
  define('FILENAME_MAIL', 'mail.php');
  define('FILENAME_MANUFACTURERS', 'manufacturers.php');
  define('FILENAME_MODULES', 'modules.php');
  define('FILENAME_NEWSLETTERS', 'newsletters.php');
  define('FILENAME_ORDERS', 'orders.php');
  define('FILENAME_ORDERS_INVOICE', 'invoice.php');
  define('FILENAME_ORDERS_PACKINGSLIP', 'packingslip.php');
  define('FILENAME_ORDERS_STATUS', 'orders_status.php');
  define('FILENAME_POPUP_IMAGE', 'popup_image.php');
  define('FILENAME_PRODUCTS_ATTRIBUTES', 'products_attributes.php');
// ########## Ajout/edite commande et compte client ##########
// Create Order & customers
  define('FILENAME_CREATE_ACCOUNT', 'create_account.php');
  define('FILENAME_CREATE_ACCOUNT_PROCESS', 'create_account_process.php');
  define('FILENAME_CREATE_ACCOUNT_SUCCESS', 'create_account_success.php');
  define('FILENAME_CREATE_ORDER_PROCESS', 'create_order_process.php');
  define('FILENAME_CREATE_ORDER', 'create_order.php');
  define('FILENAME_EDIT_ORDERS', 'edit_orders.php'); 
// ########## END - Ajout/edite commande et compte client ##########
//DANIEL:begin
  define('FILENAME_RELATED_PRODUCTS', 'products_options.php');
//DANIEL:end
  define('FILENAME_PRODUCTS_EXPECTED', 'products_expected.php');
  define('FILENAME_REVIEWS', 'reviews.php');
  define('FILENAME_SERVER_INFO', 'server_info.php');
  define('FILENAME_SHIPPING_MODULES', 'shipping_modules.php');
  define('FILENAME_SPECIALS', 'specials.php');
  define('FILENAME_STATS_CUSTOMERS', 'stats_customers.php');
  define('FILENAME_STATS_PRODUCTS_PURCHASED', 'stats_products_purchased.php');
  define('FILENAME_STATS_PRODUCTS_VIEWED', 'stats_products_viewed.php');
  define('FILENAME_TAX_CLASSES', 'tax_classes.php');
  define('FILENAME_TAX_RATES', 'tax_rates.php');
  define('FILENAME_WHOS_ONLINE', 'whos_online.php');
  define('FILENAME_INFO_HELP', 'info_help.php');  // added by splautz for help info
  define('FILENAME_INFO_HELP_POPUP', 'info_help_popup.php');  // added by splautz for help info
  define('FILENAME_ZONES', 'zones.php');
  define('FILENAME_STATS_PRODUCTS', 'stats_products.php');
  define('FILENAME_PAGES', 'pages.php');
//begin PayPal_Shopping_Cart_IPN
  define('FILENAME_PAYPAL', 'paypal.php');
//end PayPal_Shopping_Cart_IPN
  define('FILENAME_IMAGES', 'images.php');  // added by splautz
// Google XML SiteMaps Admin
  define('FILENAME_GOOGLESITEMAP', 'googlesitemap.php');
// Export orders to CSV
   define('FILENAME_EXPORT_ORDERS_CSV', 'export_orders_csv.php');
// End Export orders to csv
// Easy polulate //
  define('FILENAME_IMP_EXP_CATALOG', 'easypopulate.php'); 
// END
// START: Product Extra Fields
  define('FILENAME_PRODUCTS_EXTRA_FIELDS', 'product_extra_fields.php');
// END: Product Extra Fields
  define('FILENAME_STATS_CREDITS', 'stats_credits.php');
// VJ Links Manager v1.00 begin
  define('FILENAME_LINKS', 'links.php');
  define('FILENAME_LINK_CATEGORIES', 'link_categories.php');
  define('FILENAME_LINKS_CONTACT', 'links_contact.php');
  define('FILENAME_LINKS_CHECK', 'links_check.php');
  define('FILENAME_LINKS_FEATURED', 'links_featured.php');
  define('FILENAME_LINKS_STATUS', 'links_status.php'); 
// VJ Links Manager v1.00 end
  define('FILENAME_TESTIMONIALS_MANAGER', 'testimonials_manager.php');
// QBI
  define('FILENAME_QBI', 'qbi_create.php');
// Customer Export
  define('FILENAME_CUSTOMER_EXPORT', 'customer_export.php');
//KIKOLEPPARD New attribute manager start
  define('FILENAME_NEW_ATTRIBUTE_MANAGER', 'new_attribute_manager.php');
//KIKOLEPPARD New attribute manager end
// BOF Export Orders to CSV
  define('FILENAME_EXPORTORDERS', 'exportorders.php');
// EOF Export Orders to CSV
// order editor
  define('FILENAME_ORDERS_EDIT', 'edit_orders.php');
  define('FILENAME_ORDERS_EDIT_ADD_PRODUCT', 'edit_orders_add_product.php');
  define('FILENAME_ORDERS_EDIT_AJAX', 'edit_orders_ajax.php');
   // end order editor
?>
