<?php
/*
  $Id: whats_new.php,v 1.31 2003/02/10 22:31:09 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
// ######################## Added Enable / Disable Categorie ################
//  if ($random_product = tep_random_select("select products_id, products_image, products_tax_class_id, products_price from " . TABLE_PRODUCTS . " where products_status = '1' order by products_date_added desc limit " . MAX_RANDOM_SELECT_NEW)) {
   if ($random_product = tep_random_select("select distinct p.products_id, p.products_image, p.products_tax_class_id, p.products_price from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c where p.products_status=1 and p.products_id = p2c.products_id and c.categories_id = p2c.categories_id and c.categories_status=1 order by p.products_date_added desc limit " . MAX_RANDOM_SELECT_NEW)) {
// ######################## End Added Enable / Disable Categorie ################
?>
<!-- whats_new //-->
          <tr>
            <td>
<?php
    $random_product['products_name'] = tep_get_products_name($random_product['products_id']);
    $random_product['specials_new_products_price'] = tep_get_products_special_price($random_product['products_id']);
    $random_product['products_img_alt'] = tep_get_products_img_alt($random_product['products_id']);

    $info_box_contents = array();
    $info_box_contents[] = array('text' => BOX_HEADING_WHATS_NEW);

    new infoBoxHeading($info_box_contents, false, false, tep_href_link(FILENAME_PRODUCTS_NEW));

    if (tep_not_null($random_product['specials_new_products_price'])) {
      $whats_new_price = '<s>' . $currencies->display_price($random_product['products_price'], tep_get_tax_rate($random_product['products_tax_class_id'])) . '</s><br>';
      $whats_new_price .= '<span class="productSpecialPrice">' . $currencies->display_price($random_product['specials_new_products_price'], tep_get_tax_rate($random_product['products_tax_class_id'])) . '</span>';
    } else {
	if ($random_product['products_price'] > 0) {
      $whats_new_price = $currencies->display_price($random_product['products_price'], tep_get_tax_rate($random_product['products_tax_class_id']));
	  } else {
	  $whats_new_price = '';
	  }
    }

    $info_box_contents = array();
    $info_box_contents[] = array('align' => 'center',
                                 'text' => '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $random_product['products_id']) . '">' . tep_image(DIR_WS_IMAGES . $random_product['products_image'], $random_product['products_img_alt']?$random_product['products_img_alt']:$random_product['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'vspace="5"') . '</a><br><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $random_product['products_id']) . '">' . $random_product['products_name'] . '</a><br>' . $whats_new_price);

    new infoBox($info_box_contents);
?>
            </td>
          </tr>
<!-- whats_new_eof //-->
<?php
  }
?>
