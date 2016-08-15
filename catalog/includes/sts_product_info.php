<?php
/*
$Id: sts_product_info.php,v 1.3 2004/02/05 09:36:00 jhtalk Exp jhtalk $

osCommerce, Open Source E-Commerce Solutions
http://www.oscommerce.com

Copyright (c) 2003 osCommerce

Released under the GNU General Public License
*/

/* 

  Simple Template System (STS) - Copyright (c) 2004 Brian Gallagher - brian@diamondsea.com

*/

// This program is designed to build template variables for the product_info.php page template
// This code was modified from product_info.php

// Start the "Add to Cart" form
$template['startform'] = tep_draw_form('cart_quantity', tep_href_link(FILENAME_PRODUCT_INFO, tep_get_all_get_params(array('action')) . 'action=' . (empty($product_info['products_affiliate_url'])?'add_product':'buy_now_aff')));
// Add the hidden form variable for the Product_ID
$template['startform'] .= tep_draw_hidden_field('products_id', $product_info['products_id']);
$template['endform'] = "</form>";

// commented out by splautz since it dulicates same sql call in products_info.php
// Get product information from products_id parameter
// $product_info_query = tep_db_query("select p.products_id, pd.products_name, pd.products_description, pd.products_img_alt, p.products_model, p.products_quantity, p.products_image, pd.products_url, pd.products_affiliate_url, p.products_price, p.products_tax_class_id, p.products_date_added, p.products_date_available, p.manufacturers_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "' and pd.products_id = p.products_id and pd.language_id = '" . (int)$languages_id . "'");
// $product_info = tep_db_fetch_array($product_info_query);
if ($product_info['products_price'] > 0) {
$template['regularprice'] = $currencies->display_price($product_info['products_price'], tep_get_tax_rate($product_info['products_tax_class_id']));
} else {
$template['regularprice'] = '';
}

$template['productname'] = $product_info['products_name'];
if (tep_not_null($product_info['products_model'])) {
  $template['productmodel'] =  $product_info['products_model'];
} else $template['productmodel'] = '';
$template['productid'] =  $product_info['products_id'];

if (tep_not_null($product_info['products_image'])) {
  $template['imagesmall'] = tep_image(DIR_WS_IMAGES . $product_info['products_image'], addslashes($product_info['products_img_alt']?$product_info['products_img_alt']:$product_info['products_name']), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"');
  $template['imagelisting'] = tep_image(DIR_WS_IMAGES . $product_info['products_image'], addslashes($product_info['products_img_alt']?$product_info['products_img_alt']:$product_info['products_name']), LISTING_IMAGE_WIDTH, LISTING_IMAGE_HEIGHT, 'hspace="5" vspace="5"');
  $template['imagelarge'] = tep_image(DIR_WS_IMAGES . $product_info['products_image'], addslashes($product_info['products_img_alt']?$product_info['products_img_alt']:$product_info['products_name']), '','','');
  // $template['imagelarge'] = tep_image(DIR_WS_IMAGES . $products['products_image'], $products['products_name']);
}

$template['availability'] = ($product_info['products_quantity'] == 0)?'Out of stock':'In Stock';
$template['productinfo'] = $product_info['products_info'];
$template['productdesc'] = $product_info['products_description']; 

// START: Extra Fields Contribution v2.0b - mintpeel display fix
$extra_fields_query = tep_db_query("
   SELECT pef.products_extra_fields_id as id, pef.products_extra_fields_status as status, pef.products_extra_fields_name as name, ptf.products_extra_fields_value as value
   FROM ". TABLE_PRODUCTS_EXTRA_FIELDS ." pef
   LEFT JOIN  ". TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS ." ptf
   ON ptf.products_extra_fields_id=pef.products_extra_fields_id
   WHERE ptf.products_id=". (int)$products_id ." and ptf.products_extra_fields_value<>'' and (pef.languages_id='0' or pef.languages_id='".$languages_id."')
   ORDER BY products_extra_fields_order");
$template['extrafields'] = '<table border="0" cellspacing="0" cellpadding="0">';
while ($extra_fields = tep_db_fetch_array($extra_fields_query)) {
  if (! $extra_fields['status'])  // show only enabled extra field
    continue;
  $template['extrafields'] .= '<tr><td class="main" align="left" vallign="middle">'.$extra_fields['name'].': '.$extra_fields['value'].'</td></tr>';
  $template['extrafieldsname'.$extra_fields['id']] = $extra_fields['name'];
  $template['extrafieldsvalue'.$extra_fields['id']] = $extra_fields['value'];
}
$template['extrafields'] .= '</table>';
// END: Extra Fields Contribution - mintpeel display fix

$template['customizeheader'] = TEXT_PRODUCT_CUSTOMIZE;
$template['quantity'] = TEXT_QUANTITY . '&nbsp;' . tep_draw_input_field('cart_quantity', 1, 'size=3');
$template['options'] = '';
$template['specialprice'] = '';
$sts_block_name = 'cart';
require(STS_START_CAPTURE);
// DANIEL: begin - show cart products
// update-20051113
      $cart_products = "select distinct op.pop_id, op.pop_products_id_slave, pd.products_name, pd.products_img_alt, p.products_model, p.products_price, p.products_tax_class_id, p.products_image, if(s.status, s.specials_new_products_price, p.products_price) as specials_price from ". TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id, " . TABLE_PRODUCTS_OPTIONS_PRODUCTS . " op, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " pc, " . TABLE_CATEGORIES . " c WHERE op.pop_products_id_slave = pd.products_id and op.pop_cart = '1' and pd.products_id = p.products_id and p.products_id = pc.products_id and pc.categories_id = c.categories_id and c.categories_status = '1' and p.products_status = '1' and pd.language_id = '" . (int)$languages_id . "' and op.pop_products_id_master = '".$HTTP_GET_VARS['products_id']."' order by COALESCE(op.pop_order_id,10000), op.pop_id";
      $cart_products_query = tep_db_query($cart_products);
      echo '<table class="productlisting" width="100%" border="0" cellspacing="0" cellpadding="2">' . "\n";
      $i = 0;
      while (!$i || $cart_products_value = tep_db_fetch_array($cart_products_query)) {
        if (!$i) {
          $cart_products_value = $product_info;
          $products_id_slave = tep_get_uprid($HTTP_GET_VARS['products_id'], $attributes=NULL); // required to pick up attributes
          if (!strlen($cart_products_value['specials_price'] = tep_get_products_special_price($products_id_slave)))
            $cart_products_value['specials_price'] = $product_info['products_price'];
          else {
            $template['specialprice'] = $currencies->display_price($cart_products_value['specials_price'], tep_get_tax_rate($product_info['products_tax_class_id']));
          }
        } else $products_id_slave = ($cart_products_value['pop_products_id_slave']);

        // build price
        $products_price_slave = $currencies->display_price($cart_products_value['products_price'], tep_get_tax_rate($cart_products_value['products_tax_class_id']));
        if ($cart_products_value['specials_price'] < $cart_products_value['products_price']) $products_price_slave = '<s>' . $products_price_slave . '</s> <span class="productSpecialPrice">' . $currencies->display_price($cart_products_value['specials_price'], tep_get_tax_rate($cart_products_value['products_tax_class_id'])) . '</span>';

        echo ($i?'<tr>':'<tr class="infoBoxContents">');
        // show thumb image if Enabled
        if (MODULE_RELATED_PRODUCTS_CART_SHOW_THUMBS != 'False') {
          $text .= '<td align="center" class="main"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $products_id_slave) . '">' . tep_image(DIR_WS_IMAGES . $cart_products_value['products_image'], $cart_products_value['products_img_alt']?$cart_products_value['products_img_alt']:$cart_products_value['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"').'</a></td>';
        }
        echo '<td align="left" class="smallText"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $products_id_slave) . '">'.$cart_products_value['products_name'].'</a>&nbsp;&nbsp;&nbsp;</td>';
        echo '<td align="left" class="main">' . $cart_products_value['products_model'] . '&nbsp;&nbsp;&nbsp;</td>';
        echo '<td align="center" class="smallText">' . $products_price_slave . '&nbsp;&nbsp;&nbsp;</td>';
        echo '<td align="right" class="main">' . tep_draw_hidden_field('products_id['.$i.']', (int)$products_id_slave) . 'Quantity:</td>';
        echo '<td align="left" class="main">' . tep_draw_input_field('cart_quantity['.$i.']', (!$i?'1':'0'), 'size="4"') . '</td>';

        // build attributes
        $products_options_name_query = tep_db_query("select distinct popt.products_options_id, popt.products_options_name, popt.products_options_type, popt.products_options_length, popt.products_options_comment from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib where patrib.products_id='" . (int)$products_id_slave . "' and patrib.options_id = popt.products_options_id and popt.language_id = '" . (int)$languages_id . "' order by COALESCE(popt.products_options_sort_order,10000), popt.products_options_id");
        if (tep_db_num_rows($products_options_name_query)) {
          echo ($i?'<tr>':'<tr class="infoBoxContents">') . '<td colspan="5" align="left">';
          build_options('<table border="0" cellspacing="0" cellpadding="2">');
          if (!$i) $template['options'] .= '<tr><td class="main" colspan="2">' . TEXT_PRODUCT_OPTIONS . '</td></tr>';
          while ($products_options_name = tep_db_fetch_array($products_options_name_query)) {
            $dvalue = '';
            if (!$i) {
              if (isset($cart->contents[$products_id_slave])) $cartobj = &$cart;
              elseif (isset($wishList->contents[$products_id_slave])) $cartobj = &$wishList;
              else $cartobj = NULL;
              if (isset($cartobj)) {
		        if (isset($cartobj->contents[$products_id_slave]['attributes'][$products_options_name['products_options_id']])) {
                  $dvalue = $cartobj->contents[$products_id_slave]['attributes'][$products_options_name['products_options_id']];
                  if ($dvalue == PRODUCTS_OPTIONS_VALUE_TEXT_ID) $dvalue = $cartobj->contents[$products_id_slave]['attributes_values'][$products_options_name['products_options_id']];
                }
              } else {
                if (isset($attributes[$products_options_name['products_options_id']])) $dvalue = stripslashes($attributes[$products_options_name['products_options_id']]);
                elseif (isset($attributes[TEXT_PREFIX.$products_options_name['products_options_id']])) $dvalue = stripslashes($attributes[TEXT_PREFIX.$products_options_name['products_options_id']]);
              }
            }
            //clr 030714 add case statement to check option type
            switch ($products_options_name['products_options_type']) {
              case PRODUCTS_OPTIONS_TYPE_TEXT:
                //CLR 030714 Add logic for text option
                $products_attribs_query = tep_db_query("select distinct patrib.options_values_price, patrib.price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " patrib where patrib.products_id='" . (int)$products_id_slave . "' and patrib.options_id = '" . $products_options_name['products_options_id'] . "' order by COALESCE(patrib.products_attributes_sort_order,10000), patrib.products_attributes_id");
                $products_attribs_array = tep_db_fetch_array($products_attribs_query);
                build_options('<tr><td class="main" valign="top">' . $products_options_name['products_options_name'] . ':</td><td class="main">');
                echo tep_draw_input_field('id['.$i.']['.TEXT_PREFIX.$products_options_name['products_options_id'].']', $dvalue, ($products_options_name['products_options_length'] ? 'size="' . $products_options_name['products_options_length'] .'" maxlength="' . $products_options_name['products_options_length'] . '"' : ''));
                if (!$i) $template['options'] .= tep_draw_input_field('id['.TEXT_PREFIX.$products_options_name['products_options_id'].']', $dvalue, 'size="' . $products_options_name['products_options_length'] .'" maxlength="' . $products_options_name['products_options_length'] . '"');
                build_options('&nbsp;' . $products_options_name['products_options_comment']);
                // PHPMOM.COM AAP
                if (($products_attribs_array['options_values_price'] != '0')||(($products_attribs_array['options_values_price'] == '0')&&($products_attribs_array['price_prefix'] == ''))) {
                // if ($products_attribs_array['options_values_price'] != '0') {
                  build_options(' (' . $products_attribs_array['price_prefix'] . $currencies->display_price($products_attribs_array['options_values_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) .')');
                }
                build_options('</td></tr>');
                break;
              case PRODUCTS_OPTIONS_TYPE_TEXTAREA:
                //CLR 030714 Add logic for text option
                $products_attribs_query = tep_db_query("select distinct patrib.options_values_price, patrib.price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " patrib where patrib.products_id='" . (int)$products_id_slave . "' and patrib.options_id = '" . $products_options_name['products_options_id'] . "' order by COALESCE(patrib.products_attributes_sort_order,10000), patrib.products_attributes_id");
                $products_attribs_array = tep_db_fetch_array($products_attribs_query);
                build_options('<tr><td class="main" valign="top">' . $products_options_name['products_options_name'] . ':</td><td class="main">');
                echo tep_draw_textarea_field('id['.$i.']['.TEXT_PREFIX.$products_options_name['products_options_id'].']', 'soft', 64, (tep_not_null($products_options_name['products_options_length'])?ceil($products_options_name['products_options_length']/64):''), $dvalue);
                if (!$i) $template['options'] .= tep_draw_textarea_field('id['.TEXT_PREFIX.$products_options_name['products_options_id'].']', 'soft', 64, (tep_not_null($products_options_name['products_options_length'])?ceil($products_options_name['products_options_length']/64):''), $dvalue);
                build_options('<br>' . $products_options_name['products_options_comment']);
                // PHPMOM.COM AAP
                if (($products_attribs_array['options_values_price'] != '0')||(($products_attribs_array['options_values_price'] == '0')&&($products_attribs_array['price_prefix'] == ''))) {
                // if ($products_attribs_array['options_values_price'] != '0') {
                  build_options(' (' . $products_attribs_array['price_prefix'] . $currencies->display_price($products_attribs_array['options_values_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) .')');
                }
                build_options('</td></tr>');
                break;
              case PRODUCTS_OPTIONS_TYPE_RADIO:
                build_options('<tr><td class="main" valign="top">' . $products_options_name['products_options_name'] . ':<br>' . $products_options_name['products_options_comment'] . '</td><td class="main"><table>');
                //CLR 030714 Add logic for radio buttons
                $products_options_query = tep_db_query("select pov.products_options_values_id, pov.products_options_values_name, pa.options_values_price, pa.price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov where pa.products_id = '" . (int)$products_id_slave . "' and pa.options_id = '" . $products_options_name['products_options_id'] . "' and pa.options_values_id = pov.products_options_values_id and pov.language_id = '" . (int)$languages_id . "' order by COALESCE(pa.products_attributes_sort_order,10000), pa.products_attributes_id");
                $checked = true;
                while ($products_options_array = tep_db_fetch_array($products_options_query)) {
                  build_options('<tr><td class="main">');
                  echo tep_draw_radio_field('id['.$i.'][' . $products_options_name['products_options_id'] . ']', $products_options_array['products_options_values_id'], (is_numeric($dvalue)?($dvalue == $products_options_array['products_options_values_id']):$checked));
                  if (!$i) $template['options'] .= tep_draw_radio_field('id[' . $products_options_name['products_options_id'] . ']', $products_options_array['products_options_values_id'], (is_numeric($dvalue)?($dvalue == $products_options_array['products_options_values_id']):$checked));
                  $checked = false;
                  build_options($products_options_array['products_options_values_name']);
                  // PHPMOM.COM AAP
                  if (($products_options_array['options_values_price'] != '0')||(($products_options_array['options_values_price'] == '0')&&($products_options_array['price_prefix'] == ''))) {
                  // if ($products_options_array['options_values_price'] != '0') {
                    build_options(' (' . $products_options_array['price_prefix'] . $currencies->display_price($products_options_array['options_values_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) .')&nbsp');
                  }
                  build_options('</tr></td>');
                }
                build_options('</table></td></tr>');
                break;
              case PRODUCTS_OPTIONS_TYPE_CHECKBOX:
                //CLR 030714 Add logic for checkboxes
                $products_attribs_query = tep_db_query("select distinct patrib.options_values_price, patrib.price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " patrib where patrib.products_id='" . (int)$products_id_slave . "' and patrib.options_id = '" . $products_options_name['products_options_id'] . "' order by COALESCE(patrib.products_attributes_sort_order,10000), patrib.products_attributes_id");
                $products_attribs_array = tep_db_fetch_array($products_attribs_query);
                build_options('<tr><td class="main" valign="top">' . $products_options_name['products_options_name'] . ':</td><td class="main">');
                echo tep_draw_checkbox_field('id['.$i.'][' . $products_options_name['products_options_id'] . ']', $products_attribs_array['options_values_id'], (is_numeric($dvalue)?($dvalue==$products_attribs_array['options_values_id']):false));
                if (!$i) $template['options'] .= tep_draw_checkbox_field('id[' . $products_options_name['products_options_id'] . ']', $products_attribs_array['options_values_id'], (is_numeric($dvalue)?($dvalue==$products_attribs_array['options_values_id']):false));
                build_options('&nbsp;' . $products_options_name['products_options_comment']);
                // PHPMOM.COM AAP
                if (($products_attribs_array['options_values_price'] != '0')||(($products_attribs_array['options_values_price'] == '0')&&($products_attribs_array['price_prefix'] == ''))) {
                // if ($products_attribs_array['options_values_price'] != '0') {
                  build_options(' (' . $products_attribs_array['price_prefix'] . $currencies->display_price($products_attribs_array['options_values_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) .')&nbsp');
                }
                build_options('</td></tr>');
                break;
              default:
                //clr 030714 default is select list
                //clr 030714 reset selected_attribute variable
                $selected_attribute = false;
                $products_options_array = array();
                $products_options_query = tep_db_query("select pov.products_options_values_id, pov.products_options_values_name, pa.options_values_price, pa.price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov where pa.products_id = '" . (int)$products_id_slave . "' and pa.options_id = '" . $products_options_name['products_options_id'] . "' and pa.options_values_id = pov.products_options_values_id and pov.language_id = '" . (int)$languages_id . "' order by COALESCE(pa.products_attributes_sort_order,10000), pa.products_attributes_id");
                while ($products_options = tep_db_fetch_array($products_options_query)) {
                  $products_options_array[] = array('id' => $products_options['products_options_values_id'], 'text' => $products_options['products_options_values_name']);
                  // PHPMOM.COM AAP
                  if (($products_options['options_values_price'] != '0')||(($products_options['options_values_price'] == '0')&&($products_options['price_prefix'] == ''))) {
                   if ($products_options['options_values_price'] != '0') {
                    $products_options_array[sizeof($products_options_array)-1]['text'] .= ' (' . $products_options['price_prefix'] . $currencies->display_price($products_options['options_values_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) .') ';
                  }}
                }
                build_options('<tr><td class="main" valign="top">' . $products_options_name['products_options_name'] . ':</td><td class="main">');
                echo tep_draw_pull_down_menu('id['.$i.'][' . $products_options_name['products_options_id'] . ']', $products_options_array, (is_numeric($dvalue)?$dvalue:false));
                if (!$i) $template['options'] .= tep_draw_pull_down_menu('id[' . $products_options_name['products_options_id'] . ']', $products_options_array, (is_numeric($dvalue)?$dvalue:false));
                build_options(' ' . $products_options_name['products_options_comment']);
                build_options('</td></tr>');
            }  //clr 030714 end switch
          } //clr 030714 end while
          build_options('</table>');
          echo '</td></tr>';
        } //clr 030714 end if

        echo '</tr>' . "\n";
        $i++;
      }
      echo '</table>' . "\n";
//DANIEL: end
require(STS_STOP_CAPTURE);
$template['cart'] = $sts_block['cart'];

// See if there are any reviews
$reviews_query = tep_db_query("select count(*) as count from " . TABLE_REVIEWS . " where products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "' and approved = 1");
$reviews = tep_db_fetch_array($reviews_query);
$reviews_query_average = tep_db_query("select (avg(reviews_rating)) as average_rating from " . TABLE_REVIEWS . " where products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "' and approved = 1");
$reviews_average = tep_db_fetch_array($reviews_query_average);
$reveiws_stars = $reviews_average['average_rating'];
$reveiws_rating = number_format($reveiws_stars,0);

if ($reviews['count'] > 0) {
$template['reviews'] = TEXT_REVIEW_AVERAGE . ' ' . '<a href="' . HTTP_SERVER . '/catalog/product_reviews.php?products_id=' . $product_info['products_id'] . '">' . tep_image(DIR_WS_IMAGES . 'stars_' . $reveiws_rating . '.gif') . ' ' . ' (' . $reviews['count'] . ' ' . TEXT_CURRENT_REVIEWS . ')'. '</a>';
$template['reviewsurl'] = HTTP_SERVER . '/catalog/product_reviews.php?products_id=' . $product_info['products_id'];
$template['reviewsbutton'] = '<a href="'.$template['reviewsurl'].'">'.tep_image_button('button_reviews.gif', IMAGE_BUTTON_REVIEWS).'</a>';
} else {
$template['reviews'] = TEXT_REVIEW_NONE . ' ' . '<a href="'. HTTP_SERVER . '/catalog/product_reviews_write.php?products_id=' . $product_info['products_id'] . '">' .  TEXT_REVIEW_INVITE . '</a>';
$template['reviewsbutton'] = '';
$template['reviewsurl'] = tep_href_link(FILENAME_PRODUCT_REVIEWS, tep_get_all_get_params(),'NONSSL',true,false);
} 


/*if ($reviews['count'] > 0) {
$template['reviews'] = TEXT_REVIEW_AVERAGE . ' ' . '<a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS, tep_get_all_get_params()) . '">' . tep_image(DIR_WS_IMAGES . 'stars_' . $reveiws_rating . '.gif') . ' ' . ' (' . $reviews['count'] . ' ' . TEXT_CURRENT_REVIEWS . ')'. '</a>';
$template['reviewsurl'] = tep_href_link(FILENAME_PRODUCT_REVIEWS, tep_get_all_get_params());
$template['reviewsbutton'] = '<a href="'.$template['reviewsurl'].'">'.tep_image_button('button_reviews.gif',IMAGE_BUTTON_REVIEWS).'</a>';
} else {
$template['reviews'] = TEXT_REVIEW_NONE . ' ' . '<a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS_WRITE,tep_get_all_get_params()) . '">' .  TEXT_REVIEW_INVITE . '</a>';
$template['reviewsbutton'] = '';
$template['reviewsurl'] = tep_href_link(FILENAME_PRODUCT_REVIEWS, tep_get_all_get_params(),'NONSSL',true,false);
} 
*/
// Wishlist
//$template['addtowishlistbutton'] = tep_draw_hidden_field('wish', '0');  //Wishlist
//$template['addtowishlistbutton'] .= tep_image_submit('button_wishlist.gif', IMAGE_BUTTON_ADD_WISHLIST, 'onClick="wish.value=1; submit();"');
$template['addtowishlistbutton'] = tep_image_submit('button_wishlist.gif', IMAGE_BUTTON_ADD_WISHLIST, 'name="wishlist" value="wishlist"');
$template['addtocartbutton'] = empty($product_info['products_affiliate_url'])?tep_image_submit('button_in_cart.gif', IMAGE_BUTTON_IN_CART):tep_image_submit('affiliate_buy_now.gif', IMAGE_BUTTON_BUY_NOW);

// See if there is a product URL
if (tep_not_null($product_info['products_url'])) {
  $template['moreinfolabel'] = TEXT_MORE_INFORMATION;
  $template['moreinfourl'] = tep_href_link(FILENAME_REDIRECT, 'action=url&goto=' . urlencode($product_info['products_url']), 'NONSSL', true, false); 
} else {
  $template['moreinfolabel'] = '';
  $template['moreinfourl'] = '';
}

$template['moreinfolabel'] = str_replace('%s', $template['moreinfourl'], $template['moreinfolabel']);

// See if product is not yet available
if ($product_info['products_date_available'] > date('Y-m-d H:i:s')) {
  $template['productdatelabel'] = TEXT_DATE_AVAILABLE;
  $template['productdate'] = tep_date_long($product_info['products_date_available']);
} else {
  $template['productdatelabel'] = TEXT_DATE_ADDED;
  $template['productdate'] = tep_date_long($product_info['products_date_added']); 
}

// Strip out %s values
$template['productdatelabel'] = str_replace('%s.', '', $template['productdatelabel']);

// See if any "Also Purchased" items
// I suspect that this won't work yet
if ((USE_CACHE == 'true') && empty($SID)) {
  $template['alsopurchased'] = tep_cache_also_purchased(3600);
} else {
  ob_start();
  include(DIR_WS_MODULES . FILENAME_ALSO_PURCHASED_PRODUCTS);
  $template['alsopurchased'] = ob_get_contents();
  ob_end_clean();
}

function build_options($text) {
  global $i, $template;

  echo $text;
  if (!$i) $template['options'] .= $text;
}
?>
