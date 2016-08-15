<?php
/*
  $Id: new_products.php,v 1.34 2003/06/09 22:49:58 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
  $pihp = array(); $ibc = array(); $ibc2 = array(); $ibcCount = 0;

  if ( (!isset($new_products_category_id)) || ($new_products_category_id == '0') ) {
    $pihp = preg_split("/\D+/",PRODUCTS_ON_HOME_PAGE,-1,PREG_SPLIT_NO_EMPTY);
    foreach($pihp as $products_id) {
      $new_products_query = tep_db_query("select p.products_id, p.products_image, p.products_tax_class_id, p.products_price, if(s.status, s.specials_new_products_price, p.products_price) as specials_price from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c where c.categories_status='1' and p.products_id = p2c.products_id and p2c.categories_id = c.categories_id and products_status = '1' and p.products_id = '" . $products_id . "' and p.products_specials > 0");
      if ($new_products = tep_db_fetch_array($new_products_query)) {
        $ibc[] = $new_products;
        $ibcCount++;
      }
    }
// ######################## Added Enable / Disable Categorie ################
//    $new_products_query = tep_db_query("select p.products_id, p.products_image, p.products_tax_class_id, if(s.status, s.specials_new_products_price, p.products_price) as products_price from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id where products_status = '1' order by p.products_date_added desc limit " . MAX_DISPLAY_NEW_PRODUCTS);
    $new_products_query = tep_db_query("select distinct p.products_id, p.products_image, p.products_tax_class_id, p.products_price, if(s.status, s.specials_new_products_price, p.products_price) as specials_price from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c where c.categories_status='1' and p.products_specials > 0 and p.products_id = p2c.products_id and p2c.categories_id = c.categories_id and products_status = '1' order by p.products_date_added desc limit " . MAX_DISPLAY_NEW_PRODUCTS);
// ######################## End Added Enable / Disable Categorie ################
  } else {
    $dpids_category_query = tep_db_query("select categories_dpids from " . TABLE_CATEGORIES . " where categories_id = '" . (int)$new_products_category_id . "'");
    if (($dpids_category = tep_db_fetch_array($dpids_category_query)) && $dpids_category['categories_dpids']) {
      $dpids = preg_split("/\D+/",$dpids_category['categories_dpids'],-1,PREG_SPLIT_NO_EMPTY);
      foreach($dpids as $products_id) {
        $new_products_query = tep_db_query("select p.products_id, p.products_image, p.products_tax_class_id, p.products_price, if(s.status, s.specials_new_products_price, p.products_price) as specials_price from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c where c.categories_status='1' and p.products_specials > 0 and p.products_id = p2c.products_id and p2c.categories_id = c.categories_id and p.products_status = '1' and p.products_id = '" . $products_id . "'");
        if ($new_products = tep_db_fetch_array($new_products_query)) {
          $ibc[] = $new_products;
          $ibcCount++;
        }
      }
    }
// ######################## Added Enable / Disable Categorie ################
//    $new_products_query = tep_db_query("select distinct p.products_id, p.products_image, p.products_tax_class_id, if(s.status, s.specials_new_products_price, p.products_price) as products_price from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c where p.products_id = p2c.products_id and p2c.categories_id = c.categories_id and c.parent_id = '" . (int)$new_products_category_id . "' and p.products_status = '1' order by p.products_date_added desc limit " . MAX_DISPLAY_NEW_PRODUCTS);
    $new_products_query = tep_db_query("select distinct p.products_id, p.products_image, p.products_tax_class_id, p.products_price, if(s.status, s.specials_new_products_price, p.products_price) as specials_price from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c where c.categories_status='1' and p.products_id = p2c.products_id and p.products_specials > 0 and p2c.categories_id = c.categories_id and c.parent_id = '" . (int)$new_products_category_id . "' and p.products_status = '1' order by p.products_date_added desc limit " . MAX_DISPLAY_NEW_PRODUCTS);
// ######################## End Added Enable / Disable Categorie ################
  }

  while ($ibcCount < MAX_DISPLAY_NEW_PRODUCTS && $new_products = tep_db_fetch_array($new_products_query)) {
    if (!in_array($new_products['products_id'],$pihp)) {
      $ibc2[] = $new_products;
      $ibcCount++;
    }
  }
  shuffle($ibc2);
  $ibc = array_merge($ibc,$ibc2);
  $row = 0; $col = 0;
  $info_box_contents = array();
  foreach($ibc as $new_products) {
    $new_products['products_info'] = tep_get_products_info($new_products['products_id']);
    $new_products['products_name'] = tep_get_products_name($new_products['products_id']);
    $new_products['products_img_alt'] = tep_get_products_img_alt($new_products['products_id']);

    $price = $currencies->display_price($new_products['products_price'], tep_get_tax_rate($new_products['products_tax_class_id']));
    if ($new_products['specials_price'] < $new_products['products_price']) $price = '<s>' . $price . '</s>&nbsp;<span class="productSpecialPrice">' . $currencies->display_price($new_products['specials_price'], tep_get_tax_rate($new_products['products_tax_class_id'])) . '</span>';

    $text = '<tr><td class="smallText" align="center"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $new_products['products_id']) . '"><b>' . $new_products['products_name'] . '</b></a></td></tr>';
    $text .= '<tr><td align="center" valign="middle"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $new_products['products_id']) . '">' . tep_image(DIR_WS_IMAGES . $new_products['products_image'], $new_products['products_img_alt']?$new_products['products_img_alt']:$new_products['products_name'], LISTING_IMAGE_WIDTH, LISTING_IMAGE_HEIGHT,'vspace="5"') . '</a></td></tr>';
    $text .= '<tr><td class="smallText" align="left" valign="bottom">' . $new_products['products_info'] . '</td></tr>';
    $text .= '<tr><td class="smallText" align="right">' . $price . '</td></tr>';
    $text = '<table border="0" width="100%" cellspacing="0" cellpadding="2">' . $text . '</table>';

    $info_box_contents[$row][$col] = array('align' => 'center',
                                           'params' => 'class="newProductBox" width="'.floor(100/MAX_DISPLAY_NEW_PRODUCTS_PER_ROW).'%" valign="top"',
                                           'text' => $text);
    $col ++;
    if ($col == MAX_DISPLAY_NEW_PRODUCTS_PER_ROW) {
      $col = 0;
      $row ++;
    }
  }

  if (count($info_box_contents)) {
?>
<!-- new_products //-->
      <tr>
        <td><?php
    new contentBox($info_box_contents);
?>


        </td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<!-- new_products_eof //-->
<?php
  }
?>
