<?php
/*
  $Id: database_tables.php,v 1.1 2003/06/20 00:18:30 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

// define the database table names used in the project
  define('TABLE_ADDRESS_BOOK', 'address_book');
  define('TABLE_ADDRESS_FORMAT', 'address_format');
  define('TABLE_BANNERS', 'banners');
  define('TABLE_BANNERS_HISTORY', 'banners_history');
  define('TABLE_CATEGORIES', 'categories');
  define('TABLE_CATEGORIES_DESCRIPTION', 'categories_description');
  define('TABLE_CONFIGURATION', 'configuration');
  define('TABLE_CONFIGURATION_GROUP', 'configuration_group');
  define('TABLE_COUNTRIES', 'countries');
  define('TABLE_CURRENCIES', 'currencies');
  define('TABLE_CUSTOMERS', 'customers');
  define('TABLE_CUSTOMERS_BASKET', 'customers_basket');
  define('TABLE_CUSTOMERS_BASKET_ATTRIBUTES', 'customers_basket_attributes');
  define('TABLE_CUSTOMERS_INFO', 'customers_info');
  define('TABLE_LANGUAGES', 'languages');
  define('TABLE_MANUFACTURERS', 'manufacturers');
  define('TABLE_MANUFACTURERS_INFO', 'manufacturers_info');
  define('TABLE_NEWSLETTERS', 'newsletters');
  define('TABLE_ORDERS', 'orders');
  define('TABLE_ORDERS_PRODUCTS', 'orders_products');
  define('TABLE_ORDERS_PRODUCTS_ATTRIBUTES', 'orders_products_attributes');
  define('TABLE_ORDERS_PRODUCTS_DOWNLOAD', 'orders_products_download');
  define('TABLE_ORDERS_STATUS', 'orders_status');
  define('TABLE_ORDERS_STATUS_HISTORY', 'orders_status_history');
  define('TABLE_ORDERS_TOTAL', 'orders_total');
  define('TABLE_PRODUCTS', 'products');
  define('TABLE_PRODUCTS_ATTRIBUTES', 'products_attributes');
//DANIEL: begin
  define('TABLE_PRODUCTS_OPTIONS_PRODUCTS', 'products_options_products');
//DANIEL: end
  define('TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD', 'products_attributes_download');
  define('TABLE_PRODUCTS_DESCRIPTION', 'products_description');
  define('TABLE_PRODUCTS_NOTIFICATIONS', 'products_notifications');
  define('TABLE_PRODUCTS_OPTIONS', 'products_options');
  define('TABLE_PRODUCTS_OPTIONS_VALUES', 'products_options_values');
  define('TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS', 'products_options_values_to_products_options');
  define('TABLE_PRODUCTS_TO_CATEGORIES', 'products_to_categories');
  define('TABLE_IMAGES', 'images');  // added by splautz
  define('TABLE_IMAGES_DESCRIPTION', 'images_description');  // added by splautz
  define('TABLE_REVIEWS', 'reviews');
  define('TABLE_REVIEWS_DESCRIPTION', 'reviews_description');
  define('TABLE_SESSIONS', 'sessions');
  define('TABLE_SPECIALS', 'specials');
  define('TABLE_TAX_CLASS', 'tax_class');
  define('TABLE_TAX_RATES', 'tax_rates');
  define('TABLE_GEO_ZONES', 'geo_zones');
  define('TABLE_ZONES_TO_GEO_ZONES', 'zones_to_geo_zones');
  define('TABLE_WHOS_ONLINE', 'whos_online');
  define('TABLE_ZONES', 'zones');
  define('TABLE_POSTCODES', 'postcodes');  // added by splautz
  define('TABLE_SEO_URLS', 'seo_urls');  // added by splautz
  define('TABLE_INFO_HELP', 'info_help');  // added by splautz
  define('TABLE_PAGES', 'pages');
  define('TABLE_PAGES_DESCRIPTION', 'pages_description');
  define('TABLE_SUN_CLASS', 'sun_class');  // added by splautz
  define('TABLE_SUN_RATES', 'sun_rates');  // added by splautz
// Wishlist
  define('TABLE_WISHLIST', 'customers_wishlist');
  define('TABLE_WISHLIST_ATTRIBUTES', 'customers_wishlist_attributes');
// START: Product Extra Fields
  define('TABLE_PRODUCTS_EXTRA_FIELDS', 'products_extra_fields');
  define('TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS', 'products_to_products_extra_fields');
// END: Product Extra Fields
// VJ Links Manager v1.00 begin
  define('TABLE_LINK_CATEGORIES', 'link_categories');
  define('TABLE_LINK_CATEGORIES_DESCRIPTION', 'link_categories_description');
  define('TABLE_LINKS', 'links');
  define('TABLE_LINKS_DESCRIPTION', 'links_description');
  define('TABLE_LINKS_TO_LINK_CATEGORIES', 'links_to_link_categories');
  define('TABLE_LINKS_STATUS', 'links_status');
  define('TABLE_LINKS_CHECK', 'links_check'); 
  define('TABLE_LINKS_FEATURED', 'links_featured');
// VJ Links Manager v1.00 end
  define('TABLE_TESTIMONIALS', 'customer_testimonials');
  define('TABLE_ADMIN_COMMENTS', 'admin_comments');
// QBI
  define('TABLE_QBI_CONFIG', 'qbi_config');
  define('TABLE_QBI_DISC', 'qbi_disc');
  define('TABLE_QBI_GROUPS', 'qbi_groups');
  define('TABLE_QBI_GROUPS_ITEMS', 'qbi_groups_items');
  define('TABLE_QBI_ITEMS', 'qbi_items');
  define('TABLE_QBI_OT', 'qbi_ot');
  define('TABLE_QBI_OT_DISC', 'qbi_ot_disc');
  define('TABLE_QBI_PAYOSC', 'qbi_payosc');
  define('TABLE_QBI_PAYOSC_PAYQB', 'qbi_payosc_payqb');
  define('TABLE_QBI_PAYQB', 'qbi_payqb');
  define('TABLE_QBI_PRODUCTS_ITEMS', 'qbi_products_items');  
  define('TABLE_QBI_SHIPOSC', 'qbi_shiposc');
  define('TABLE_QBI_SHIPQB', 'qbi_shipqb');
  define('TABLE_QBI_SHIPOSC_SHIPQB', 'qbi_shiposc_shipqb');
  define('TABLE_QBI_TAXES', 'qbi_taxes');
?>
