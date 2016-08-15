<?php

/*

  $Id: shopping_cart.php,v 1.73 2003/06/09 23:03:56 hpdl Exp $



  osCommerce, Open Source E-Commerce Solutions

  http://www.oscommerce.com



  Copyright (c) 2003 osCommerce



  Released under the GNU General Public License

*/



  require("includes/application_top.php");

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_SHOPPING_CART);



  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_SHOPPING_CART));



// added by splautz for quote system

  require(DIR_WS_CLASSES . 'http_client.php');

  require(DIR_WS_CLASSES . 'shipping.php');



  $getshipping = false;

  if (defined(SHIPPING_INSTANT_QUOTES) && SHIPPING_INSTANT_QUOTES == 'True') {

    $shipping_instant_quotes = true;

    if (tep_not_null($HTTP_POST_VARS['getshipping'])||tep_not_null($HTTP_POST_VARS['getshipping2'])) $getshipping = true;

  } else $shipping_instant_quotes = false;



  class order_shipping_quote {

    var $delivery;



    function order_shipping_quote($postcode, $city = '', $country_id = STORE_COUNTRY, $zone_id = 0) {

      $this->delivery = array();



      $pc_query = tep_db_query("select city, zone_id from " . TABLE_POSTCODES . " where postcode = '" . $postcode . "' and country_id = '" . (int)$country_id . "'");

      if ($pc = tep_db_fetch_array($pc_query)) {

        $city = $pc['city'];

        if (ACCOUNT_STATE == 'true' && !$zone_id) $zone_id = $pc['zone_id'];

      }



      $shipping_address_query = tep_db_query("select countries_name, countries_iso_code_2, countries_iso_code_3 from " . TABLE_COUNTRIES . " where countries_id = '" . (int)$country_id . "'");

      $shipping_address = tep_db_fetch_array($shipping_address_query);

      $this->delivery = array('postcode' => $postcode,

                              'city' => $city,

                              'zone_id' => $zone_id,

                              'country' => array('id' => $country_id, 'title' => $shipping_address['countries_name'], 'iso_code_2' => $shipping_address['countries_iso_code_2'], 'iso_code_3' => $shipping_address['countries_iso_code_3']),

                              'country_id' => $country_id);

    }

  }



  $lookup_shipping = (SHIPPING_INSTANT_QUOTES_REQ_DEST!='True');

  $dest_city = '';

  if (tep_not_null($HTTP_POST_VARS['postcode']) && tep_not_null($HTTP_POST_VARS['country'])) {

    $dest_pcode = tep_db_prepare_input(str_replace(' ', '', $HTTP_POST_VARS['postcode']));

    $dest_ccode = $HTTP_POST_VARS['country'];

    $dest_zcode = '0';

  } elseif (tep_session_is_registered('shippingQuotes') && isset($shippingQuotes['last_postcode'])) {

      $dest_pcode = $shippingQuotes['last_postcode'];

      $dest_ccode = $shippingQuotes['last_country_id'];

      $dest_zcode = $shippingQuotes['last_zone_id'];

  } elseif (tep_session_is_registered('customer_id')) {

    $address_query = tep_db_query("select entry_postcode, entry_country_id, entry_zone_id from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int)$customer_id . "' and address_book_id = '" . (int)$customer_default_address_id . "'");

    if ($address = tep_db_fetch_array($address_query)) {;

      $dest_pcode = $address['entry_postcode'];

      $dest_city = $address['entry_city'];

      $dest_ccode = $address['entry_country_id'];

      $dest_zcode = $address['entry_zone_id'];

    }

  }



  if (isset($dest_pcode)) {

    if ($dest_ccode == '223') $dest_pcode = substr($dest_pcode, 0, 5);

    $dest_code = $dest_ccode.':'.$dest_pcode;

    if ($getshipping || (tep_session_is_registered('shippingQuotes') && $shippingQuotes['cartID'] == $cart->cartID && isset($shippingQuotes['quotes'][$dest_code]))) {

      $lookup_shipping = true;

      $shippingQuotes['last_postcode'] = $dest_pcode;

      $shippingQuotes['last_country_id'] = $dest_ccode;

      $shippingQuotes['last_zone_id'] = $dest_zcode;

      if (tep_session_is_registered('customer_id')) {

        $save_customer_country_id = $customer_country_id;

        $save_customer_zone_id = $customer_zone_id;

      }

      $customer_country_id = $dest_ccode;

      $customer_zone_id = $dest_zcode;

    }

  } else {

    $dest_pcode = '';

    $dest_ccode = STORE_COUNTRY;

    $dest_zcode = '0';

    if ($getshipping) $messageStack->add('header', QUOTE_PCODE_ERROR, 'error');

  }



// end add for quote system

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

      <tr>

        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">

          <tr>

            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>

            <td class="pageHeading" align="right"><?php echo tep_image(DIR_WS_IMAGES . 'table_background_cart.gif', HEADING_TITLE, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>

          </tr>

        </table></td>

      </tr>

      <tr>

        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>

      </tr>

<?php

  if ($cart->count_contents() > 0) {

?>

      <?php echo tep_draw_form('cart_quantity', tep_href_link(FILENAME_SHOPPING_CART, 'action=update_product')); ?>

      <tr>

        <td>

<?php

    $info_box_contents = array();

    $info_box_contents[0][] = array('align' => 'center',

                                    'params' => 'class="productListing-heading"',

                                    'text' => TABLE_HEADING_REMOVE);



    $info_box_contents[0][] = array('params' => 'colspan="2" class="productListing-heading"', // modified by splautz to keep attributes aligned

                                    'text' => TABLE_HEADING_PRODUCTS);



    $info_box_contents[0][] = array('align' => 'center',

                                    'params' => 'class="productListing-heading"',

                                    'text' => TABLE_HEADING_QUANTITY);



    $info_box_contents[0][] = array('align' => 'right',

                                    'params' => 'class="productListing-heading"',

                                    'text' => TABLE_HEADING_TOTAL);



    $any_out_of_stock = 0;

    $products = $cart->get_products();

    for ($i=0, $n=sizeof($products); $i<$n; $i++) {

// Push all attributes information in an array

      if (isset($products[$i]['attributes']) && is_array($products[$i]['attributes'])) {

        while (list($option, $value) = each($products[$i]['attributes'])) {

          //clr 030714 move hidden field to if statement below

          //echo tep_draw_hidden_field('id[' . $products[$i]['id'] . '][' . $option . ']', $value);

          // Update-20060817

          $attributes = tep_db_query("select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix

                                      from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa

                                      where pa.products_id = '" . (int)$products[$i]['id'] . "'

                                       and pa.options_id = '" . (int)$option . "'

                                       and pa.options_id = popt.products_options_id

                                       and pa.options_values_id = '" . (int)$value . "'

                                       and pa.options_values_id = poval.products_options_values_id

                                       and popt.language_id = '" . (int)$languages_id . "'

                                       and poval.language_id = '" . (int)$languages_id . "'");

          $attributes_values = tep_db_fetch_array($attributes);



          //clr 030714 determine if attribute is a text attribute and assign to $attr_value temporarily

          if ($value == PRODUCTS_OPTIONS_VALUE_TEXT_ID) {

            echo tep_draw_hidden_field('id[' . $products[$i]['id'] . '][' . TEXT_PREFIX . $option . ']',  $products[$i]['attributes_values'][$option]);

            $attr_value = $products[$i]['attributes_values'][$option];

          } else {

            echo tep_draw_hidden_field('id[' . $products[$i]['id'] . '][' . $option . ']', $value);

            $attr_value = $attributes_values['products_options_values_name'];

          }



          $products[$i][$option]['products_options_name'] = $attributes_values['products_options_name'];

          $products[$i][$option]['options_values_id'] = $value;

          //$products[$i][$option]['products_options_values_name'] = $attributes_values['products_options_values_name'];

          //clr 030714 assign $attr_value

          $products[$i][$option]['products_options_values_name'] = $attr_value;

          $products[$i][$option]['options_values_price'] = $attributes_values['options_values_price'];

          $products[$i][$option]['price_prefix'] = $attributes_values['price_prefix'];

        }

      }

    }



    for ($i=0, $n=sizeof($products); $i<$n; $i++) {

      if (($i/2) == floor($i/2)) {

        $info_box_contents[] = array('params' => 'class="productListing-even"');

      } else {

        $info_box_contents[] = array('params' => 'class="productListing-odd"');

      }



      $cur_row = sizeof($info_box_contents) - 1;



      $info_box_contents[$cur_row][] = array('align' => 'center',

                                             'params' => 'class="productListing-data" valign="top"',

                                             'text' => tep_draw_checkbox_field('cart_delete[]', $products[$i]['id']));



// modified by splautz to keep attributes aligned

      $products_image = '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $products[$i]['id']) . '">' . tep_image(DIR_WS_IMAGES . $products[$i]['image'], $products[$i]['img_alt']?$products[$i]['img_alt']:$products[$i]['name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="8" vspace="4" align="left"') . '</a>';

      $products_name = '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $products[$i]['id']) . '"><b>' . $products[$i]['name'] . '</b></a>';



      if (STOCK_CHECK == 'true') {

        $stock_check = tep_check_stock($products[$i]['id'], $products[$i]['quantity']);

        if (tep_not_null($stock_check)) {

          $any_out_of_stock = 1;



          $products_name .= $stock_check;

        }

      }



      if (isset($products[$i]['attributes']) && is_array($products[$i]['attributes'])) {

        reset($products[$i]['attributes']);

        while (list($option, $value) = each($products[$i]['attributes'])) {

          $products_name .= '<br><small><i> - ' . $products[$i][$option]['products_options_name'] . ' ' . $products[$i][$option]['products_options_values_name'] . '</i></small>';

        }

      }



      $products_name .= '<br><br>Model: '.$products[$i]['model'];



// added by splautz to keep attributes aligned

      $info_box_contents[$cur_row][] = array('params' => 'class="productListing-data" valign="top" width="1%"',

                                             'text' => $products_image);



      $info_box_contents[$cur_row][] = array('align' => 'left', // modified by splautz to keep attributes aligned

                                             'params' => 'class="productListing-data" valign="top"',

                                             'text' => $products_name);



      $info_box_contents[$cur_row][] = array('align' => 'center',

                                             'params' => 'class="productListing-data" valign="top"',

                                             'text' => tep_draw_input_field('cart_quantity[]', $products[$i]['quantity'], 'size="4"') . tep_draw_hidden_field('products_id[]', $products[$i]['id']));



      $info_box_contents[$cur_row][] = array('align' => 'right',

                                             'params' => 'class="productListing-data" valign="top"',

                                             'text' => '<b>' . $currencies->display_price($products[$i]['final_price'], tep_get_tax_rate($products[$i]['tax_class_id']), $products[$i]['quantity']) . '</b>');

    }



    new productListingBox($info_box_contents);

?>

        </td>

      </tr>

      <tr>

        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>

      </tr>

      <tr>

        <td align="right" class="main"><b><?php echo SUB_TITLE_SUB_TOTAL; ?> <?php echo $currencies->format($cart->show_total()); ?></b></td>

      </tr>

<?php

    if ($any_out_of_stock == 1) {

      if (STOCK_ALLOW_CHECKOUT == 'true') {

?>

      <tr>

        <td class="stockWarning" align="center"><br><?php echo OUT_OF_STOCK_CAN_CHECKOUT; ?></td>

      </tr>

<?php

      } else {

?>

      <tr>

        <td class="stockWarning" align="center"><br><?php echo OUT_OF_STOCK_CANT_CHECKOUT; ?></td>

      </tr>

<?php

      }

    }

?>

      <tr>

        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>

      </tr>

      <tr>

        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">

          <tr class="infoBoxContents">

            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">

              <tr>

                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

                <td class="main"><?php echo tep_image_submit('button_update_cart.gif', IMAGE_BUTTON_UPDATE_CART); ?></td>

<?php

    $back = sizeof($navigation->path)-2;

    if (isset($navigation->path[$back])) {

?>

                <td class="main"><?php echo '<a href="' . tep_href_link($navigation->path[$back]['page'], tep_array_to_string($navigation->path[$back]['get'], array('action')), $navigation->path[$back]['mode']) . '">' . tep_image_button('button_continue_shopping.gif', IMAGE_BUTTON_CONTINUE_SHOPPING) . '</a>'; ?></td>

<?php

    }

?>

                <td align="right" class="main"><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL') . '">' . tep_image_button('button_checkout.gif', IMAGE_BUTTON_CHECKOUT) . '</a>'; ?></td>

                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

              </tr>

            </table></td>

          </tr>

        </table></td>

      </tr>

      </form>

<?php

    if(MODULE_PAYMENT_PAYPAL_EC_STATUS == 'True' ||  MODULE_PAYMENT_PAYPAL_WPP_STATUS == 'True') {

?>

    <tr><td>

     <form method="post" name="form" action="paypal_wpp/ec/process.php">
       <input type="hidden" name="express" value="1"> 

        <input type="hidden" name="amount" value="<?=$cart->show_total();?>"> 

      </form>

      </td></tr>



<?php

   }



// added by splautz for shipping quotes 

    $order = new order_shipping_quote($dest_pcode, $dest_city, $dest_ccode, $dest_zcode);

    $shipping_modules = new shipping;

    if ($shipping_instant_quotes && !tep_count_shipping_modules()) $shipping_instant_quotes = false;



    if ($shipping_instant_quotes) {

?>

      <tr>

        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '25'); ?></td>

      </tr>

      <?php echo tep_draw_form('quote', tep_href_link(FILENAME_SHOPPING_CART)); ?>

      <tr>

        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">

          <tr class="infoBoxContents">

            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php

      if ($shipping_instant_quotes) {

        if ($lookup_shipping) {

          $total_weight = $cart->show_weight();

          $total_count = $cart->count_contents();

// modified by splautz to include freight shipping

          $stype = array('SHIPPING','FREIGHT');

          for($i = 0; $i < 2; $i++) {

            $free_shipping[$i] = false;

            if ( defined('MODULE_ORDER_TOTAL_'.$stype[$i].'_FREE_SHIPPING') && (constant('MODULE_ORDER_TOTAL_'.$stype[$i].'_FREE_SHIPPING') == 'true') ) {

              $pass = false;

              switch (constant('MODULE_ORDER_TOTAL_'.$stype[$i].'_DESTINATION')) {

                case 'national':

                  if ($order->delivery['country_id'] == STORE_COUNTRY) $pass = true;

                  break;

                case 'international':

                  if ($order->delivery['country_id'] != STORE_COUNTRY) $pass = true;

                  break;

                case 'both':

                  $pass = true;

                  break;

              }

              if ( ($pass == true) && ($cart->total >= constant('MODULE_ORDER_TOTAL_'.$stype[$i].'_FREE_SHIPPING_OVER')) ) {

                $free_shipping[$i] = true;

                include(DIR_WS_LANGUAGES . $language . '/modules/order_total/ot_'.strtolower($stype[$i]).'.php');

              }

            }

          }

// end freight shipping modification



// get all available shipping quotes

          $squotes = $shipping_modules->quote();



          foreach($squotes as $freight => $quotes) {

            $num_items = $cart->count_contents($freight);

?>

              <tr>

                <td colspan="3"><table border="0" width="100%" cellspacing="0" cellpadding="2">

                  <tr>

                    <td class="main"><b><?php echo constant('TABLE_HEADING_'.$stype[$freight].'_QUOTE').' ('.$num_items.' item'.($num_items==1?'':'s').')'; ?></b></td>

                  </tr>

                </table></td>

              </tr>

<?php

            if ($free_shipping[$freight] == true) {

?>

              <tr>

                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

                <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="2">

                  <tr>

                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

                    <td class="main"><b><?php echo constant('FREE_'.$stype[$freight].'_TITLE'); ?></b>&nbsp;</td>

                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

                  </tr>

                  <tr class="moduleRow">

                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

                    <td class="main" width="100%"><?php echo sprintf(constant('FREE_'.$stype[$freight].'_DESCRIPTION'), $currencies->format(constant('MODULE_ORDER_TOTAL_'.$stype[$freight].'_FREE_SHIPPING_OVER'))); ?></td>

                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

                  </tr>

                </table></td>

                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 

              </tr>

<?php

            } else {

              for ($i=0, $n=sizeof($quotes); $i<$n; $i++) {

?>

              <tr>

                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

                <td><table border="0" width="100%" cellspacing="0" cellpadding="2">

                  <tr>

                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

                    <td class="main" colspan="2"><b><?php echo $quotes[$i]['module']; ?></b>&nbsp;<?php if (isset($quotes[$i]['icon']) && tep_not_null($quotes[$i]['icon'])) { echo $quotes[$i]['icon']; } ?></td>

                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

                  </tr>

<?php

                if (isset($quotes[$i]['error'])) {

?>

                  <tr>

                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

                    <td class="main" colspan="2"><?php echo $quotes[$i]['error']; ?></td>

                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

                  </tr>

<?php

                } else {

                  for ($j=0, $n2=sizeof($quotes[$i]['methods']); $j<$n2; $j++) {

?>

                  <tr class="moduleRow">

                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

                    <td class="main" width="75%"><?php echo $quotes[$i]['methods'][$j]['title']; ?></td>

                    <td class="main"><?php echo (is_numeric($quotes[$i]['methods'][$j]['cost']))?$currencies->format(tep_add_tax($quotes[$i]['methods'][$j]['cost'], (isset($quotes[$i]['tax']) ? $quotes[$i]['tax'] : 0))):$quotes[$i]['methods'][$j]['cost']; ?></td>

                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

                  </tr>

<?php

                  }

                }

?>

                </table></td>

                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 

              </tr>

<?php

              }

            }

          }

?>

              <tr>

                <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '25'); ?></td>

              </tr>

<?php

        }

        if (SHIPPING_INSTANT_QUOTES_REQ_DEST=='True') {

?>

              <tr>

                <td colspan="3"><table border="0" width="100%" cellspacing="0" cellpadding="2">

                  <tr>

                    <td class="main"><?php echo TITLE_SHIPPING_QUOTE; ?></td>

                  </tr>

                </table></td>

              </tr>

              <tr>

                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

                <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="2">

                  <tr>

                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

                    <td class="main" valign="top"><?php echo ENTRY_POST_CODE; ?></td><td class="main"><?php echo tep_draw_input_field('postcode', empty($dest_pcode)?'':$dest_pcode, '', 'text', false); ?></td>

                    <td align="right" valign="top" class="main" width="40%"><input type="submit" name="getshipping" onClick="document.getElementById('wait').style.display='block'; this.disabled=true; this.form.getshipping2.value='y'; this.form.submit();" value="<?php echo BUTTON_TEXT_SHIPPING_QUOTE ?>"><input type="hidden" name="getshipping2" value=""></td>

                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

                  </tr>

                  <tr>

                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

					<td class="main" valign="top"><?php echo ENTRY_COUNTRY; ?></td><td class="main"><?php echo tep_get_country_list('country', empty($dest_ccode)?STORE_COUNTRY:$dest_ccode, '', SHIPPING_QUOTE_ZONE); ?><br>&nbsp;</td>

                    <td align="center" valign="top" class="main"><div ID="wait" style="color:red; display:none;">Please allow up to 60 secs<br>to obtain quote.</div></td>

                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

                  </tr>

                </table></td>

                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 

              </tr>

<?php

        }

      }

?>

            </table></td>

          </tr>

        </table></td>

      </tr>

      </form>

<?php

    }

// end add for shipping quotes

  } else {  // begin empty cart

?>

      <tr>

        <td align="center" class="main"><?php new infoBox(array(array('text' => TEXT_CART_EMPTY))); ?></td>

      </tr>

      <tr>

        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '25'); ?></td>

      </tr>

      <tr>

        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">

          <tr class="infoBoxContents">

            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">

              <tr>

                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

                <td align="right" class="main"><?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT) . '">' . tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></td>

                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

              </tr>

            </table></td>

          </tr>

        </table></td>

      </tr>

<?php

  }

?>

    </table></td>

<?php

// added by splautz for shipping quotes

  if ($lookup_shipping) {

    if (tep_session_is_registered('customer_id')) {

      $customer_country_id = $save_customer_country_id;

      $customer_zone_id = $save_customer_zone_id;

    } else {

      unset($customer_country_id);

      unset($customer_zone_id);

    }

  }

?>

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

