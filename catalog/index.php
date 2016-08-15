<?php
/*
  $Id: index.php,v 1.1 2003/06/11 17:37:59 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

// the following cPath references come from application_top.php
// modified by splautz so top is displayed even if $cPath = 0 and nested is always a category with sub-categories.
  $category_depth = 'top';
  if (isset($cPath) && tep_not_null($cPath) && $cPath != '0') {
    $category_query = tep_db_query("select cd.categories_name, c.categories_image, cd.categories_img_alt, cd.categories_htc_title_tag, cd.categories_htc_description, cd.categories_body, cd.categories_body2 from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = '" . (int)$current_category_id . "' and cd.categories_id = '" . (int)$current_category_id . "' and cd.language_id = '" . (int)$languages_id . "'");
    $category = tep_db_fetch_array($category_query);
    $category_parent_query = tep_db_query("select count(*) as total from " . TABLE_CATEGORIES . " where parent_id = '" . (int)$current_category_id . "' and categories_status = '1'");
    $category_parent = tep_db_fetch_array($category_parent_query);
    if ($category_parent['total'] > 0) {
 // navigate through the categories
      $category_products_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_status = '1' and p.products_id = p2c.products_id and p2c.categories_id = '" . (int)$current_category_id . "'");
      $category_products = tep_db_fetch_array($category_products_query);
      if ($category_products['total'] > 0) $category_depth = 'combined';
      else $category_depth = 'nested';
    } else {
      $category_depth = 'products'; // display products if any
    }
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_DEFAULT);
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>"> 
<title><?php echo TITLE; ?></title>
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<link rel="stylesheet" type="text/css" href="stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="3" cellpadding="3">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
<?php
  if ($category_depth != 'top' || isset($HTTP_GET_VARS['manufacturers_id'])) {
// added by splautz to obtain current page
    if (isset($_REQUEST['page'])) $page = $_REQUEST['page']; else $page = '';
    if (!is_numeric($page) || $page < 2) $page = '';

    $number_of_categories = 0;
    if ($category_depth == 'nested' || $category_depth == 'combined') {
      if (isset($cPath) && strpos('_', $cPath)) {
// check to see if there are deeper categories within the current category
        $category_links = array_reverse($cPath_array);
        for($i=0, $n=sizeof($category_links); $i<$n; $i++) {
// ################## Added Enable Disable Categorie #################
//        $categories_query = tep_db_query("select count(*) as total from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.parent_id = '" . (int)$category_links[$i] . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "'");
          $categories_query = tep_db_query("select count(*) as total from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_status = '1' and c.parent_id = '" . (int)$category_links[$i] . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "'");
          $categories = tep_db_fetch_array($categories_query);
          if ($categories['total'] < 1) {
          // do nothing, go through the loop
          } else {
// ################## Added Enable Disable Categorie #################
//          $categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.parent_id = '" . (int)$category_links[$i] . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' order by sort_order, cd.categories_name");
		    $categories_query = tep_db_query("select c.categories_id, cd.categories_name, cd.categories_img_alt, c.categories_image, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_status = '1' and c.parent_id = '" . (int)$category_links[$i] . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' order by sort_order, cd.categories_name");
// ################## End Added Enable Disable Categorie #################
            break; // we've found the deepest category the customer is in
          }
        }
      } else {
// ################## End Added Enable Disable Categorie #################
//      $categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.parent_id = '" . (int)$current_category_id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' order by sort_order, cd.categories_name");
        $categories_query = tep_db_query("select c.categories_id, cd.categories_name, cd.categories_img_alt, c.categories_image, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_status = '1' and c.parent_id = '" . (int)$current_category_id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' order by sort_order, cd.categories_name");
// ################## End Added Enable Disable Categorie #################
      }
      $number_of_categories = tep_db_num_rows($categories_query);
      $clinkstr = '';
      $cdispstr = '';
      if ($number_of_categories) {
        $rows = 0;
        while ($categories = tep_db_fetch_array($categories_query)) {
          $rows++;
          $cPath_new = tep_get_path($categories['categories_id']);
          $width = (int)(100 / MAX_DISPLAY_CATEGORIES_PER_ROW) . '%';
          $cdispstr .= '                <td align="center" class="smallText" width="' . $width . '" valign="top"><a href="' . tep_href_link(FILENAME_DEFAULT, $cPath_new) . '" class="link">' . tep_image(DIR_WS_IMAGES . $categories['categories_image'], $categories['categories_img_alt']?$categories['categories_img_alt']:$categories['categories_name'], SUBCATEGORY_IMAGE_WIDTH, SUBCATEGORY_IMAGE_HEIGHT) . '<br>' . $categories['categories_name'] . '</a></td>' . "\n";
          if ((($rows / MAX_DISPLAY_CATEGORIES_PER_ROW) == floor($rows / MAX_DISPLAY_CATEGORIES_PER_ROW)) && ($rows != $number_of_categories)) {
            $cdispstr .= '              </tr>' . "\n";
            $cdispstr .= '              <tr>' . "\n";
          }
          if ($clinkstr) $clinkstr .= ', ';
          $clinkstr .= '<a href="' . tep_href_link(FILENAME_DEFAULT, $cPath_new) . '" class="link">' . $categories['categories_name'] . '</a>';
        }
      }
    }

    if (!$page) {
   // Get the right information for the category or manufacturer page
      $image = ''; $ialt = ''; $ititle = ''; $ibody = ''; $ibody2 = '';
      if (isset($HTTP_GET_VARS['manufacturers_id'])) {
        $manufacturer_query = tep_db_query("select m.manufacturers_image, m.manufacturers_name, mi.manufacturers_img_alt, mi.manufacturers_htc_description, mi.manufacturers_body, mi.manufacturers_body2 from " . TABLE_MANUFACTURERS . " m, " . TABLE_MANUFACTURERS_INFO . " mi where m.manufacturers_id = mi.manufacturers_id and mi.languages_id = '" . (int)$languages_id . "' and m.manufacturers_id = '" . (int)$HTTP_GET_VARS['manufacturers_id'] . "'");
        $manufacturer = tep_db_fetch_array($manufacturer_query);
	    $ititle = $manufacturer['manufacturers_htc_description']?$manufacturer['manufacturers_htc_description']:'<h2>'.$manufacturer['manufacturers_name'].'</h2>';
	    $ialt = $manufacturer['manufacturers_img_alt']?$manufacturer['manufacturers_img_alt']:$manufacturer['manufacturers_name'];
        $image = $manufacturer['manufacturers_image'];
        $ibody = $manufacturer['manufacturers_body'];
        $ibody2 = $manufacturer['manufacturers_body2'];
      } elseif ($category) {
        $ititle = $category['categories_htc_description']?$category['categories_htc_description']:'<h2>'.$category['categories_name'].'</h2>';
        $ialt = $category['categories_img_alt']?$category['categories_img_alt']:$category['categories_name'];
        $image = $category['categories_image'];
        $ibody = $category['categories_body'];
        $ibody2 = $category['categories_body2'];
      }
      if (!$image) $image = DIR_WS_IMAGES . 'table_background_list.gif';

?>
      <tr>
        <td class="pageContent" valign="top"><?php echo tep_image(DIR_WS_IMAGES . $image, $ialt, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'align="right"'); ?>
<?php echo $ititle; ?> 
        </td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr><?php
    }
    if (!$page && $ibody) { ?>
      <tr>
        <td class="pageContent" valign="top"><?php echo $ibody; ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr><?php
    } ?>
      <tr><td><div id="emptyDiv"></div></td></tr><?php
    if (!$page && $ibody2) { ?>
      <tr>
        <td class="pageContent" valign="top"><?php echo $ibody2; ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr><?php
    } ?>
      <tr><td><div id="centerDiv"><table border="0" width="100%" cellspacing="0" cellpadding="0"><?php
    if (!$page && $number_of_categories && defined('HEADING_SUBTITLE')) { ?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0"><tr>
        <td class="pageContent" nowrap valign="top"><?php printf(HEADING_SUBTITLE, $category['categories_name']); ?></td><td class="pageContent" width="100%" valign="middle"><?php echo $clinkstr; ?></td>
        </tr></table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr><?php
    }
    if ($category_depth == 'products' || $category_depth == 'combined' || isset($HTTP_GET_VARS['manufacturers_id']) || isset($HTTP_GET_VARS['filter_id'])) {
// create column list
      $define_list = array('PRODUCT_LIST_MODEL' => PRODUCT_LIST_MODEL,
                           'PRODUCT_LIST_NAME' => PRODUCT_LIST_NAME,
                           'PRODUCT_LIST_INFO' => PRODUCT_LIST_INFO,
                           'PRODUCT_LIST_MANUFACTURER' => PRODUCT_LIST_MANUFACTURER,
                           'PRODUCT_LIST_PRICE' => PRODUCT_LIST_PRICE,
                           'PRODUCT_LIST_QUANTITY' => PRODUCT_LIST_QUANTITY,
                           'PRODUCT_LIST_WEIGHT' => PRODUCT_LIST_WEIGHT,
                           'PRODUCT_LIST_IMAGE' => PRODUCT_LIST_IMAGE,
                           'PRODUCT_LIST_BUY_NOW' => PRODUCT_LIST_BUY_NOW);

      asort($define_list);

      $column_list = array();
      reset($define_list);
      while (list($key, $value) = each($define_list)) {
        if ($value > 0) $column_list[] = $key;
      }

      $select_column_list = '';

      for ($i=0, $n=sizeof($column_list); $i<$n; $i++) {
        switch ($column_list[$i]) {
          case 'PRODUCT_LIST_MODEL':
            $select_column_list .= 'p.products_model, ';
            break;
          case 'PRODUCT_LIST_NAME':
            $select_column_list .= 'pd.products_name, ';
            break;
          case 'PRODUCT_LIST_INFO':
            $select_column_list .= 'pd.products_info, ';
            break;
          case 'PRODUCT_LIST_MANUFACTURER':
            $select_column_list .= 'm.manufacturers_name, ';
            break;
          case 'PRODUCT_LIST_QUANTITY':
            $select_column_list .= 'p.products_quantity, ';
            break;
          case 'PRODUCT_LIST_IMAGE':
            $select_column_list .= 'p.products_image, pd.products_img_alt, ';
            break;
          case 'PRODUCT_LIST_WEIGHT':
            $select_column_list .= 'p.products_weight, ';
            break;

// added by splautz for affiliate linking
          case 'PRODUCT_LIST_BUY_NOW':
            $select_column_list .= 'pd.products_affiliate_url, ';
            break;
        }
      }

      $orderby = "COALESCE(p2c.sort_order,10000)";
// show the products of a specified manufacturer
      if (isset($HTTP_GET_VARS['manufacturers_id'])) {
        if (isset($HTTP_GET_VARS['filter_id']) && tep_not_null($HTTP_GET_VARS['filter_id'])) {
// We are asked to show only a specific category
// ################## Added Enable Disable Categorie #################
//      $listing_sql = "select " . $select_column_list . " p.products_id, p.manufacturers_id, p.products_price, p.products_tax_class_id, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_MANUFACTURERS . " m, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id where p.products_status = '1' and p.manufacturers_id = m.manufacturers_id and m.manufacturers_id = '" . (int)$HTTP_GET_VARS['manufacturers_id'] . "' and p.products_id = p2c.products_id and pd.products_id = p2c.products_id and pd.language_id = '" . (int)$languages_id . "' and p2c.categories_id = '" . (int)$HTTP_GET_VARS['filter_id'] . "'";
// update-20051113
          $listing_sql = "select distinct p.products_id, " . $select_column_list . "p.manufacturers_id, p.products_price, p.products_tax_class_id, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_MANUFACTURERS . " m, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c where c.categories_status = '1' and p.products_status = '1' and p.products_id = p2c.products_id and c.categories_id = p2c.categories_id and pd.products_id = p2c.products_id and pd.language_id = '" . (int)$languages_id . "' and p.manufacturers_id = m.manufacturers_id and m.manufacturers_id = '" . (int)$HTTP_GET_VARS['manufacturers_id'] . "' and p2c.categories_id = '" . (int)$HTTP_GET_VARS['filter_id'] . "'";
        } else {
// We show them all
// ################## Added Enable Disable Categorie #################
//      $listing_sql = "select " . $select_column_list . " p.products_id, p.manufacturers_id, p.products_price, p.products_tax_class_id, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_MANUFACTURERS . " m left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id where p.products_status = '1' and pd.products_id = p.products_id and pd.language_id = '" . (int)$languages_id . "' and p.manufacturers_id = m.manufacturers_id and m.manufacturers_id = '" . (int)$HTTP_GET_VARS['manufacturers_id'] . "'";
// update-20051113
          $listing_sql = "select distinct p.products_id, " . $select_column_list . "p.manufacturers_id, p.products_price, p.products_tax_class_id, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_MANUFACTURERS . " m, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c where c.categories_status = '1' and p.products_status = '1' and p.products_id = p2c.products_id and c.categories_id = p2c.categories_id and pd.products_id = p2c.products_id and pd.language_id = '" . (int)$languages_id . "' and p.manufacturers_id = m.manufacturers_id and m.manufacturers_id = '" . (int)$HTTP_GET_VARS['manufacturers_id'] . "'";
          $orderby = "";
        }
      } else {
// show the products in a given categorie
        if (isset($HTTP_GET_VARS['filter_id']) && tep_not_null($HTTP_GET_VARS['filter_id'])) {
// We are asked to show only specific catgeory
// ################## Added Enable Disable Categorie #################
//        $listing_sql = "select " . $select_column_list . " p.products_id, p.manufacturers_id, p.products_price, p.products_tax_class_id, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_MANUFACTURERS . " m, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id where p.products_status = '1' and p.manufacturers_id = m.manufacturers_id and m.manufacturers_id = '" . (int)$HTTP_GET_VARS['filter_id'] . "' and p.products_id = p2c.products_id and pd.products_id = p2c.products_id and pd.language_id = '" . (int)$languages_id . "' and p2c.categories_id = '" . (int)$current_category_id . "'";
// update-20051113
          $listing_sql = "select distinct p.products_id, " . $select_column_list . "p.manufacturers_id, p.products_price, p.products_tax_class_id, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_MANUFACTURERS . " m, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c where c.categories_status = '1' and p.products_status = '1' and p.products_id = p2c.products_id and c.categories_id = p2c.categories_id and pd.products_id = p2c.products_id and pd.language_id = '" . (int)$languages_id . "' and p.manufacturers_id = m.manufacturers_id and m.manufacturers_id = '" . (int)$HTTP_GET_VARS['filter_id'] . "' and p2c.categories_id = '" . (int)$current_category_id . "'";
        } else {
// We show them all
// ################## Added Enable Disable Categorie #################
//        $listing_sql = "select " . $select_column_list . " p.products_id, p.manufacturers_id, p.products_price, p.products_tax_class_id, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price from " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS . " p left join " . TABLE_MANUFACTURERS . " m on p.manufacturers_id = m.manufacturers_id, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id where p.products_status = '1' and p.products_id = p2c.products_id and pd.products_id = p2c.products_id and pd.language_id = '" . (int)$languages_id . "' and p2c.categories_id = '" . (int)$current_category_id . "'";
// update-20051113
          $listing_sql = "select distinct p.products_id, " . $select_column_list . "p.manufacturers_id, p.products_price, p.products_tax_class_id, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price from " . TABLE_PRODUCTS . " p left join " . TABLE_MANUFACTURERS . " m on p.manufacturers_id = m.manufacturers_id left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c where c.categories_status = '1' and p.products_status = '1' and p.products_id = p2c.products_id and c.categories_id = p2c.categories_id and pd.products_id = p2c.products_id and pd.language_id = '" . (int)$languages_id . "' and p2c.categories_id = '" . (int)$current_category_id . "'";
        }
      }

      if ( (!isset($HTTP_GET_VARS['sort'])) || (!ereg('[1-8][ad]', $HTTP_GET_VARS['sort'])) || (substr($HTTP_GET_VARS['sort'], 0, 1) > sizeof($column_list)) ) {

// replaced by splautz for product sort field
//      for ($i=0, $n=sizeof($column_list); $i<$n; $i++) {
//        if ($column_list[$i] == 'PRODUCT_LIST_NAME') {
//          $HTTP_GET_VARS['sort'] = $i+1 . 'a';
//          $listing_sql .= " order by pd.products_name";
//          break;
//        }
//      }
        $HTTP_GET_VARS['sort'] = '0a';
        $listing_sql .= " order by " . ($orderby ? $orderby . ', ' : '') . "pd.products_name";

      } else {
        if ($orderby) $orderby = ', ' . $orderby;
        $sort_col = substr($HTTP_GET_VARS['sort'], 0 , 1);
        $sort_order = substr($HTTP_GET_VARS['sort'], 1);
        $listing_sql .= ' order by ';
        switch ($column_list[$sort_col-1]) {
          case 'PRODUCT_LIST_MODEL':
            $listing_sql .= "p.products_model " . ($sort_order == 'd' ? 'desc' : '') . $orderby . ", pd.products_name";
            break;
          case 'PRODUCT_LIST_NAME':
            $listing_sql .= "pd.products_name " . ($sort_order == 'd' ? 'desc' : '') . $orderby;
            break;
          case 'PRODUCT_LIST_INFO':
            $listing_sql .= "pd.products_info ". ($sort_order == 'd' ? 'desc' : '') . $orderby . ", pd.products_name";
            break;
          case 'PRODUCT_LIST_MANUFACTURER':
            $listing_sql .= "m.manufacturers_name " . ($sort_order == 'd' ? 'desc' : '') . $orderby . ", pd.products_name";
            break;
          case 'PRODUCT_LIST_QUANTITY':
            $listing_sql .= "p.products_quantity " . ($sort_order == 'd' ? 'desc' : '') . $orderby . ", pd.products_name";
            break;
          case 'PRODUCT_LIST_IMAGE':
            $listing_sql .= "pd.products_name" . $orderby;
            break;
          case 'PRODUCT_LIST_WEIGHT':
            $listing_sql .= "p.products_weight " . ($sort_order == 'd' ? 'desc' : '') . $orderby . ", pd.products_name";
            break;
          case 'PRODUCT_LIST_PRICE':
            $listing_sql .= "final_price " . ($sort_order == 'd' ? 'desc' : '') . $orderby . ", pd.products_name";
            break;
        }
      }

// optional Product List Filter
      if (PRODUCT_LIST_FILTER > 0) {
?>
      <tr>
<?php
        if (isset($HTTP_GET_VARS['manufacturers_id'])) {
          $filterlist_sql = "select distinct c.categories_id as id, cd.categories_name as name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where p.products_status = '1' and p.products_id = p2c.products_id and p2c.categories_id = c.categories_id and p2c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' and p.manufacturers_id = '" . (int)$HTTP_GET_VARS['manufacturers_id'] . "' and c.categories_status = '1' order by COALESCE(c.sort_order,1000), cd.categories_name";
        } else {
          $filterlist_sql= "select distinct m.manufacturers_id as id, m.manufacturers_name as name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c, " . TABLE_MANUFACTURERS . " m where p.products_status = '1' and p.manufacturers_id = m.manufacturers_id and p.products_id = p2c.products_id and p2c.categories_id = c.categories_id and p2c.categories_id = '" . (int)$current_category_id . "' and c.categories_status = '1' order by COALESCE(m.sort_order,10000), m.manufacturers_name";
        }
        $filterlist_query = tep_db_query($filterlist_sql);
        if (tep_db_num_rows($filterlist_query) > 1) {
          echo '            <td align="center" class="main">' . tep_draw_form('filter', $form_action?$form_action:tep_href_link(FILENAME_REDIRECT, '', 'NONSSL', false, false), 'get') . tep_draw_hidden_field('action','rewrite') . tep_draw_hidden_field('goto',FILENAME_DEFAULT) . TEXT_SHOW . '&nbsp;';  // modified by splautz for seo urls
          if (isset($HTTP_GET_VARS['manufacturers_id'])) {
            echo tep_draw_hidden_field('manufacturers_id', $HTTP_GET_VARS['manufacturers_id']);
            $options = array(array('id' => '', 'text' => TEXT_ALL_CATEGORIES));
          } else {
            echo tep_draw_hidden_field('cPath', $cPath);
            $options = array(array('id' => '', 'text' => TEXT_ALL_MANUFACTURERS));
          }
          echo tep_draw_hidden_field('sort', $HTTP_GET_VARS['sort']);
          while ($filterlist = tep_db_fetch_array($filterlist_query)) {
            $options[] = array('id' => $filterlist['id'], 'text' => $filterlist['name']);
          }
          echo tep_draw_pull_down_menu('filter_id', $options, (isset($HTTP_GET_VARS['filter_id']) ? $HTTP_GET_VARS['filter_id'] : ''), 'onchange="this.form.submit()"') . tep_hide_session_id();  // modified by splautz to maintain session id
          echo '</form></td>' . "\n";
        }
?>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr><?php
      }
?>
      <tr>
        <td><?php include(DIR_WS_MODULES . FILENAME_PRODUCT_LISTING); ?></td>
      </tr>
      <tr>
        <td><?php if ($listing_split->number_of_rows > 0 || TEXT_NO_PRODUCTS) echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
    } else { // print new products table

          // needed for the new products module shown below
          $new_products_category_id = $current_category_id;
          include(DIR_WS_MODULES . FILENAME_NEW_PRODUCTS);
    }
?>
      </table></div>
<script type="text/javascript">
<!--
document.getElementById('emptyDiv').innerHTML=document.getElementById('centerDiv').innerHTML;
document.getElementById('centerDiv').innerHTML='';
-->
</script>
      </td></tr><?php
    if (!$page && $number_of_categories && defined('HEADING_SUBTITLE2')) { /* commented out to replace table sub-cat listing with line listing.
?>
      <tr>
        <td class="pageContent" valign="top"><?php printf(HEADING_SUBTITLE2, $category['categories_name']); ?>
        </td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
<?php echo $cdispstr; ?>
          </tr></table>
        </td>
      </tr>
<?php */ ?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0"><tr>
        <td class="pageContent" nowrap valign="top"><?php printf(HEADING_SUBTITLE2, $category['categories_name']); ?></td><td class="pageContent" width="100%" valign="middle"><?php echo $clinkstr; ?></td>
        </tr></table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr><?php
    }
  } else { // default page
    $pages_id = 1;
    $pages_query = tep_db_query("select pd.pages_name, pd.pages_intro, pd.pages_body, pd.pages_body2, pd.pages_img_alt, pd.pages_head_title_tag, pd.pages_head_desc_tag, pd.pages_head_keywords_tag, pd.pages_surls_id, pd.pages_h1, p.pages_id, p.pages_image, p.pages_status, p.sort_order from " . TABLE_PAGES . " p, " . TABLE_PAGES_DESCRIPTION . " pd where p.pages_id = '" . (int)$pages_id . "' and p.pages_id = pd.pages_id and pd.language_id = '" . (int)$languages_id . "'");
    $pages = tep_db_fetch_array($pages_query);
    define('NAVBAR_TITLE', $pages['pages_name']);
    define('HEADING_TITLE', $pages['pages_name']);
    if ($pages['pages_status'])
      define('TEXT_MAIN', $pages['pages_body']);
    else
      define('TEXT_MAIN', '');
?>
      <tr>
        <td class="pageContent" valign="top"><?php echo tep_image(DIR_WS_IMAGES . ($pages["pages_image"]?$pages["pages_image"]:'pixel_trans.gif'), $pages["pages_img_alt"]?$pages["pages_img_alt"]:HEADING_TITLE, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'align="right"');
        if (tep_not_null($pages['pages_intro'])) echo $pages['pages_intro'];
        else echo "<h2>" . $pages['pages_name'] . "</h2>"; ?>
        </td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php if ($pages && $pages['pages_body']) { ?>
      <tr>
        <td class="pageContent" valign="top"><?php echo $pages['pages_body']; ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php } ?>
      <tr><td><div id="emptyDiv"></div></td></tr>
<?php if ($pages && $pages['pages_body2']) { ?>
      <tr>
        <td class="pageContent" valign="top"><?php echo $pages['pages_body2']; ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php } ?>
      <tr><td><div id="centerDiv"><table border="0" width="100%" cellspacing="0" cellpadding="0">
<?php
    include(DIR_WS_MODULES . FILENAME_NEW_PRODUCTS);
    include(DIR_WS_MODULES . FILENAME_UPCOMING_PRODUCTS);
?>
      </table></div>
<script type="text/javascript">
<!--
document.getElementById('emptyDiv').innerHTML=document.getElementById('centerDiv').innerHTML;
document.getElementById('centerDiv').innerHTML='';
-->
</script>
      </td></tr>
<?php
  }
?>
    </table></td>
<!-- body_text_eof //-->
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
<!-- right_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
<!-- right_navigation_eof //-->
    </table></td>
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
