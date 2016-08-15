<?php
// /catalog/includes/languages/english/header_tags.php
// WebMakers.com Added: Header Tags Generator v2.5.2
// Add META TAGS and Modify TITLE
//
// DEFINITIONS FOR /includes/languages/english/header_tags.php

// Define your email address to appear on all pages
define('HEAD_REPLY_TAG_ALL',STORE_OWNER_EMAIL_ADDRESS);
// Define format of appended text on title that appears on additional pages
define('HEAD_TITLE_PAGE',' - %s');

// For all pages not defined or left blank, and for products not defined
// These are included unless you set the toggle switch in each section below to OFF ( '0' )
// The HEAD_TITLE_TAG_ALL is included AFTER the specific one for the page
// The HEAD_DESC_TAG_ALL is included AFTER the specific one for the page
// The HEAD_KEY_TAG_ALL is included AFTER the specific one for the page
define('HEAD_TITLE_TAG_ALL','$storename');
define('HEAD_DESC_TAG_ALL','$storename');
define('HEAD_KEY_TAG_ALL','$storename');
define('HEAD_H1_TAG_ALL','$storename');

// DEFINE TAGS FOR INDIVIDUAL PAGES

// index.php
define('HTTA_DEFAULT_ON','1'); // Include HEAD_TITLE_TAG_ALL in Title
define('HTKA_DEFAULT_ON','0'); // Include HEAD_KEY_TAG_ALL in Keywords
define('HTDA_DEFAULT_ON','0'); // Include HEAD_DESC_TAG_ALL in Description
define('HTTA_CAT_DEFAULT_ON','1'); //Include category or manufacturers name in Title
define('HEAD_TITLE_TAG_DEFAULT','');
define('HEAD_DESC_TAG_DEFAULT','');
define('HEAD_KEY_TAG_DEFAULT','');
define('HEAD_H1_TAG_DEFAULT','');

// product_info.php - if left blank in products_description table these values will be used
define('HTTA_PRODUCT_INFO_ON','1');
define('HTKA_PRODUCT_INFO_ON','0');
define('HTDA_PRODUCT_INFO_ON','0');
define('HTTA_CAT_PRODUCT_INFO_ON','1');
define('HEAD_TITLE_TAG_PRODUCT_INFO','');
define('HEAD_DESC_TAG_PRODUCT_INFO','');
define('HEAD_KEY_TAG_PRODUCT_INFO','');
define('HEAD_H1_TAG_PRODUCT_INFO','');

// pages.php
define('HTTA_PAGES_ON','1');
define('HTDA_PAGES_ON','0');
define('HTKA_PAGES_ON','0');
define('HTTA_CAT_PAGES_ON','1');
define('HEAD_TITLE_TAG_PAGES','');
define('HEAD_DESC_TAG_PAGES','');
define('HEAD_KEY_TAG_PAGES','');
define('HEAD_H1_TAG_PAGES','');

// products_new.php - whats_new
define('HTTA_WHATS_NEW_ON','1');
define('HTKA_WHATS_NEW_ON','0');
define('HTDA_WHATS_NEW_ON','0');
define('HEAD_TITLE_TAG_WHATS_NEW','New Products');
define('HEAD_DESC_TAG_WHATS_NEW','');
define('HEAD_KEY_TAG_WHATS_NEW','');
define('HEAD_H1_TAG_WHATS_NEW','');

// reviews.php
define('HTTA_REVIEWS_ON','1');
define('HTDA_REVIEWS_ON','0');
define('HTKA_REVIEWS_ON','0');
define('HEAD_TITLE_TAG_REVIEWS','Reviews');
define('HEAD_DESC_TAG_REVIEWS','');
define('HEAD_KEY_TAG_REVIEWS','');
define('HEAD_H1_TAG_REVIEWS','');

// sitemap.php
define('HTTA_SITEMAP_ON','1');
define('HTDA_SITEMAP_ON','0');
define('HTKA_SITEMAP_ON','0');
define('HEAD_TITLE_TAG_SITEMAP','Site Map');
define('HEAD_DESC_TAG_SITEMAP','');
define('HEAD_KEY_TAG_SITEMAP','');
define('HEAD_H1_TAG_SITEMAP','');

// advanced_search.php
define('HTTA_ADVANCED_SEARCH_ON','1');
define('HTDA_ADVANCED_SEARCH_ON','0');
define('HTKA_ADVANCED_SEARCH_ON','0');
define('HEAD_TITLE_TAG_ADVANCED_SEARCH','Search');
define('HEAD_DESC_TAG_ADVANCED_SEARCH','');
define('HEAD_KEY_TAG_ADVANCED_SEARCH','');
define('HEAD_H1_TAG_ADVANCED_SEARCH','');

// contact_us.php
define('HTTA_CONTACT_US_ON','1');
define('HTDA_CONTACT_US_ON','0');
define('HTKA_CONTACT_US_ON','0');
define('HEAD_TITLE_TAG_CONTACT_US','Contact Us');
define('HEAD_DESC_TAG_CONTACT_US','');
define('HEAD_KEY_TAG_CONTACT_US','');
define('HEAD_H1_TAG_CONTACT_US','');

// customer_testimonials.php
define('HTTA_CUSTOMER_TESTIMONIALS_ON','1');
define('HTDA_CUSTOMER_TESTIMONIALS_ON','0');
define('HTKA_CUSTOMER_TESTIMONIALS_ON','0');
define('HEAD_TITLE_TAG_CUSTOMER_TESTIMONIALS','Customer Testimonials');
define('HEAD_DESC_TAG_CUSTOMER_TESTIMONIALS','');
define('HEAD_KEY_TAG_CUSTOMER_TESTIMONIALS','');
define('HEAD_H1_TAG_CUSTOMER_TESTIMONIALS','');

// specials.php
define('HTTA_SPECIALS_ON','1');
define('HTDA_SPECIALS_ON','0');
define('HTKA_SPECIALS_ON','0');
define('HEAD_TITLE_TAG_SPECIALS','Specials');
define('HEAD_DESC_TAG_SPECIALS','');
define('HEAD_KEY_TAG_SPECIALS','');
define('HEAD_H1_TAG_SPECIALS','');

?>
