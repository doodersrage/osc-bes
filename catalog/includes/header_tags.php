<?php
/*
  /catalog/includes/header_tags.php
  WebMakers.com Added: Header Tags Generator v2.0
  Add META TAGS and Modify TITLE

  NOTE: Globally replace all fields in products table with current product name just to get things started:
  In phpMyAdmin use: UPDATE products_description set PRODUCTS_HEAD_TITLE_TAG = PRODUCTS_NAME

  Shoppe Enhancement Controller - Copyright (c) 2003 WebMakers.com
  Linda McGrath - osCommerce@WebMakers.com
*/

require(DIR_WS_LANGUAGES . $language . '/' . 'header_tags.php');
$head_desc_tag_all = stripslashes(HEAD_DESC_TAG_ALL);
$head_key_tag_all = stripslashes(HEAD_KEY_TAG_ALL);
$head_title_tag_all = stripslashes(HEAD_TITLE_TAG_ALL);
$head_h1_tag_all = stripslashes(HEAD_H1_TAG_ALL);
if (!strlen($head_title_tag_all) && defined('TITLE')) $head_title_tag_all = TITLE;
$tags_array = array('desc' => '', 'keywords' => '', 'title' => '', 'h1' => '');

$sname = $_SERVER['PHP_SELF'] or $PHP_SELF;
if (basename($sname) == FILENAME_DEFAULT) $sname = 'DEFAULT';
else $sname = strtoupper(basename($sname,'.php'));

if (defined("HTTA_{$sname}_ON")) {
  $htta_string = constant("HEAD_TITLE_TAG_$sname")?stripslashes(constant("HEAD_TITLE_TAG_$sname")).' - ':'';
  $htda_string = constant("HEAD_DESC_TAG_$sname")?stripslashes(constant("HEAD_DESC_TAG_$sname")).' ':'';
  $htka_string = constant("HEAD_KEY_TAG_$sname")?stripslashes(constant("HEAD_KEY_TAG_$sname")).',':'';
  $h1_string = constant("HEAD_H1_TAG_$sname")?stripslashes(constant("HEAD_H1_TAG_$sname")):'';

// Define specific settings per page:

// PAGES.PHP (or home page if $pages_id=1)
  if ($pages_id) {
    $tags_array['title'] = !empty($pages['pages_head_title_tag'])?$pages['pages_head_title_tag'].' - ':($pages_id>1&&HTTA_CAT_PAGES_ON=='1'&&!empty($pages['pages_name'])?clean_html_comments($pages['pages_name']).' - ':'').$htta_string;
    $tags_array['desc'] = !empty($pages['pages_head_desc_tag'])?$pages['pages_head_desc_tag'].' ':($pages_id>1&&HTTA_CAT_PAGES_ON=='1'&&strlen($pages['pages_body'].$pages['pages_body2'])?($dtxt=substr(strip_tags($pages['pages_body'].' '.$pages['pages_body2']),0,200)).(strlen($dtxt)>=200?'...':'').' ':'').$htda_string;
    $tags_array['keywords'] = !empty($pages['pages_head_keywords_tag'])?$pages['pages_head_keywords_tag'].',':($pages_id>1&&HTTA_CAT_PAGES_ON=='1'&&!empty($pages['pages_name'])?clean_html_comments($pages['pages_name']).',':'').$htka_string;
    $tags_array['h1'] = !empty($pages['pages_h1'])?$pages['pages_h1']:$h1_string;
// INDEX.PHP
  } elseif ($sname == 'DEFAULT' && isset($HTTP_GET_VARS['manufacturers_id'])) {
    $the_manufacturer_query = tep_db_query("select m.manufacturers_name, mi.manufacturers_htc_title_tag, mi.manufacturers_htc_desc_tag, mi.manufacturers_htc_keywords_tag, mi.manufacturers_h1 from " . TABLE_MANUFACTURERS . " m, " . TABLE_MANUFACTURERS_INFO . " mi where m.manufacturers_id = mi.manufacturers_id and mi.languages_id = '" . (int)$languages_id . "' and m.manufacturers_id = '" . (int)$HTTP_GET_VARS['manufacturers_id'] . "'");
    $the_manufacturer = tep_db_fetch_array($the_manufacturer_query);
    $tags_array['title'] = strlen($the_manufacturer['manufacturers_htc_title_tag'])?$the_manufacturer['manufacturers_htc_title_tag'].' - ':(HTTA_CAT_DEFAULT_ON=='1'&&strlen($the_manufacturer['manufacturers_name'])?clean_html_comments($the_manufacturer['manufacturers_name']).' - ':'').$htta_string;
    $tags_array['desc'] = strlen($the_manufacturer['manufacturers_htc_desc_tag'])?$the_manufacturer['manufacturers_htc_desc_tag'].' ':(HTTA_CAT_DEFAULT_ON=='1'&&strlen($the_manufacturer['manufacturers_body'].$the_manufacturer['manufacturers_body2'])?($dtxt=substr(strip_tags($the_manufacturer['manufacturers_body'].' '.$the_manufacturer['manufacturers_body2']),0,200)).(strlen($dtxt)>=200?'...':'').' ':'').$htda_string;
    $tags_array['keywords'] = strlen($the_manufacturer['manufacturers_htc_keywords_tag'])?$the_manufacturer['manufacturers_htc_keywords_tag'].',':(HTTA_CAT_DEFAULT_ON=='1'&&strlen($the_manufacturer['manufacturers_name'])?clean_html_comments($the_manufacturer['manufacturers_name']).',':'').$htka_string;
    $tags_array['h1'] = strlen($the_manufacturer['manufacturers_h1'])?$the_manufacturer['manufacturers_h1']:$h1_string;
  } elseif ($sname == 'DEFAULT' && $category_depth != 'top') {
    $the_category_query = tep_db_query("select categories_name, categories_htc_title_tag, categories_htc_desc_tag, categories_htc_keywords_tag, categories_h1 from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . (int)$current_category_id . "' and language_id = '" . (int)$languages_id . "'");
    $the_category = tep_db_fetch_array($the_category_query);
    $tags_array['title'] = strlen($the_category['categories_htc_title_tag'])?$the_category['categories_htc_title_tag'].' - ':(HTTA_CAT_DEFAULT_ON=='1'&&strlen($the_category['categories_name'])?clean_html_comments($the_category['categories_name']).' - ':'').$htta_string;
    $tags_array['desc'] = strlen($the_category['categories_htc_desc_tag'])?$the_category['categories_htc_desc_tag'].' ':(HTTA_CAT_DEFAULT_ON=='1'&&strlen($the_category['categories_body'].$the_category['categories_body2'])?($dtxt=substr(strip_tags($the_category['categories_body'].' '.$the_category['categories_body2']),0,200)).(strlen($dtxt)>=200?'...':'').' ':'').$htda_string;
    $tags_array['keywords'] = strlen($the_category['categories_htc_keywords_tag'])?$the_category['categories_htc_keywords_tag'].',':(HTTA_CAT_DEFAULT_ON=='1'&&strlen($the_category['categories_name'])?clean_html_comments($the_category['categories_name']).',':'').$htka_string;
    $tags_array['h1'] = strlen($the_category['categories_h1'])?$the_category['categories_h1']:$h1_string;
// PRODUCT_INFO.PHP
  } elseif ( $sname == 'PRODUCT_INFO' ) {
//    $the_product_info_query = tep_db_query("select p.products_id, pd.products_name, pd.products_description, pd.products_head_title_tag, pd.products_head_keywords_tag, pd.products_head_desc_tag, p.products_model, p.products_quantity, p.products_image, pd.products_url, p.products_price, p.products_tax_class_id, p.products_date_added, p.products_date_available, p.manufacturers_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = '" . $HTTP_GET_VARS['products_id'] . "' and pd.products_id = '" . $HTTP_GET_VARS['products_id'] . "'");
    $the_product_info_query = tep_db_query("select pd.products_name, pd.products_info, pd.products_description, pd.products_head_title_tag, pd.products_head_keywords_tag, pd.products_head_desc_tag, pd.products_h1 from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and p.products_id = '" . $HTTP_GET_VARS['products_id'] . "'" . " and pd.language_id ='" .  $languages_id . "'");
    $the_product_info = tep_db_fetch_array($the_product_info_query);
    $tags_array['title'] = strlen($the_product_info['products_head_title_tag'])?$the_product_info['products_head_title_tag'].' - ':(HTTA_CAT_PRODUCT_INFO_ON=='1'&&strlen($the_product_info['products_name'])?clean_html_comments($the_product_info['products_name']).' - ':'').$htta_string;
    $tags_array['desc'] = strlen($the_product_info['products_head_desc_tag'])?$the_product_info['products_head_desc_tag'].' ':(HTTA_CAT_PRODUCT_INFO_ON=='1'&&strlen($the_product_info['products_info'].$the_product_info['products_description'])?($dtxt=substr(strip_tags($the_product_info['products_info'].' '.$the_product_info['products_description']),0,200)).(strlen($dtxt)>=200?'...':'').' ':'').$htda_string;
    $tags_array['keywords'] = strlen($the_product_info['products_head_keywords_tag'])?$the_product_info['products_head_keywords_tag'].',':(HTTA_CAT_PRODUCT_INFO_ON=='1'&&strlen($the_product_info['products_name'])?clean_html_comments($the_product_info['products_name']).',':'').$htka_string;
    $tags_array['h1'] = strlen($the_product_info['products_h1'])?$the_product_info['products_h1']:$h1_string;
// ALL OTHER PAGES NOT DEFINED ABOVE
  } else {
    $tags_array['title'] = $htta_string;
    $tags_array['desc'] = $htda_string;
    $tags_array['keywords'] = $htka_string;
    $tags_array['h1'] = $h1_string;
  }

  if ($tags_array['title'] === '' || constant("HTTA_{$sname}_ON")=='1') $tags_array['title'] .= $head_title_tag_all . ' - ';
  $tags_array['title'] = substr($tags_array['title'],0,-3);
  if ($tags_array['desc'] === '' || constant("HTDA_{$sname}_ON")=='1') $tags_array['desc'] .= $head_desc_tag_all . ' ';
  $tags_array['desc'] = substr($tags_array['desc'],0,-1);
  if ($tags_array['keywords'] === '' || constant("HTKA_{$sname}_ON")=='1') $tags_array['keywords'] .= $head_key_tag_all . ',';
  $tags_array['keywords'] = substr($tags_array['keywords'],0,-1);
  if ($tags_array['h1'] === '') $tags_array['h1'] = $head_h1_tag_all;

} else {
  $tags_array['desc'] = $head_desc_tag_all;
  $tags_array['keywords'] = $head_key_tag_all;
  $tags_array['title'] = $head_title_tag_all;
  $tags_array['h1'] = $head_h1_tag_all;
}

if (!strlen($tags_array['title'])) $tags_array['title'] = STORE_NAME;  // default to store name if blank

// added by splautz to append page number to title
if (!isset($head_title_page)) {
  $head_title_page = '';
  if (defined('HEAD_TITLE_PAGE') && strlen(HEAD_TITLE_PAGE)) {
    if (isset($HTTP_GET_VARS['page'])) $page_num = $HTTP_GET_VARS['page'];
    elseif (isset($HTTP_POST_VARS['page'])) $page_num = $HTTP_POST_VARS['page'];
    else $page_num = '1';
    if ($page_num > 1) $head_title_page = sprintf(HEAD_TITLE_PAGE, $page_num);
  }
}

echo '<title>' . htmlspecialchars($tags_array['title'].$head_title_page) . '</title>' . "\n";
echo '<meta name="Description" content="' . htmlspecialchars($tags_array['desc']) . '">' . "\n";
echo '<meta name="Keywords" content="' . htmlspecialchars($tags_array['keywords']) . '">' . "\n";
echo '<meta http-equiv="Content-Type" content="text/html; charset=' . CHARSET  . '">'."\n";
// echo '  <META NAME="Reply-to" CONTENT="' . HEAD_REPLY_TAG_ALL . '">' . "\n";

// echo '<!-- EOF: Generated Meta Tags -->' . "\n";
?>
