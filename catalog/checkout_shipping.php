<?php
/*
  $Id: checkout_shipping.php,v 1.16 2003/06/09 23:03:53 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  require('includes/classes/http_client.php');

// if the customer is not logged on, redirect them to the login page
  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

// if there is nothing in the customers cart, redirect them to the shopping cart page
  if ($cart->count_contents() < 1) {
    tep_redirect(tep_href_link(FILENAME_SHOPPING_CART));
  }

// if no shipping destination address was selected, use the customers own address as default
  if (!tep_session_is_registered('sendto')) {
    tep_session_register('sendto');
  // PayPal WPP
    if (tep_session_is_registered('pp_payer_id')) {
      $address_entry = array('customers_id' => $customer_id,
        'entry_company' => $pp_business,
        'entry_firstname' => $pp_firstname,
        'entry_lastname' => $pp_lastname,
        'entry_street_address' => $pp_street1,
        'entry_suburb' => $pp_street2,
        'entry_postcode' => $pp_zip,
        'entry_city' => $pp_city,
        'entry_state' => $pp_state,
        'entry_country_id' => $pp_country);
      $sendto = tep_insert_address($address_entry);
    } else $sendto = $customer_default_address_id;
  } else {
// verify the selected shipping address
    $check_address_query = tep_db_query("select count(*) as total from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int)$customer_id . "' and address_book_id = '" . (int)$sendto . "'");
    $check_address = tep_db_fetch_array($check_address_query);

    if ($check_address['total'] != '1') {
      $sendto = $customer_default_address_id;
      if (tep_session_is_registered('shipping')) tep_session_unregister('shipping');
    }
  }

  require(DIR_WS_CLASSES . 'order.php');
  $order = new order;

// register a random ID in the session to check throughout the checkout procedure
// against alterations in the shopping cart contents
  if (!tep_session_is_registered('cartID')) tep_session_register('cartID');
  $cartID = $cart->cartID;

// if the order contains only virtual products, forward the customer to the billing page as
// a shipping address is not needed
// ###### Added CCGV Contribution #########
//  if ($order->content_type == 'virtual') {
  if (($order->content_type == 'virtual') || ($order->content_type == 'virtual_weight') ) {
// ###### End Added CCGV Contribution #########
    if (!tep_session_is_registered('shipping')) tep_session_register('shipping');
    $shipping = false;
    $sendto = false;
    tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
  }

  $total_weight = $cart->show_weight();
  $total_count = $cart->count_contents();

// load all enabled shipping modules
  require(DIR_WS_CLASSES . 'shipping.php');
  $shipping_modules = new shipping;

// modified by splautz to include freight shipping
  $stype = array('SHIPPING','FREIGHT');
  for($i = 0; $i < 2; $i++) {
    $free_shipping[$i] = false;
    if ( defined('MODULE_ORDER_TOTAL_'.$stype[$i].'_FREE_SHIPPING') && (constant('MODULE_ORDER_TOTAL_'.$stype[$i].'_FREE_SHIPPING') == 'true') ) {
      $pass = false;

      switch (constant('MODULE_ORDER_TOTAL_'.$stype[$i].'_DESTINATION')) {
        case 'national':
          if ($order->delivery['country_id'] == STORE_COUNTRY) {
            $pass = true;
          }
          break;
        case 'international':
          if ($order->delivery['country_id'] != STORE_COUNTRY) {
            $pass = true;
          }
          break;
        case 'both':
          $pass = true;
          break;
      }

  // modified by splautz to ensure free shipping total doesn't include tax unless price is displayed with tax
  //  if ( ($pass == true) && ($order->info['total'] >= MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER) ) {
      if ( ($pass == true) && ($order->info['subtotal'] >= constant('MODULE_ORDER_TOTAL_'.$stype[$i].'_FREE_SHIPPING_OVER')) ) {
        $free_shipping[$i] = true;

        include(DIR_WS_LANGUAGES . $language . '/modules/order_total/ot_'.strtolower($stype[$i]).'.php');
      }
    }
  }
// end freight shipping modification

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CHECKOUT_SHIPPING); // moved by splautz

// process the selected shipping method
  if ( isset($HTTP_POST_VARS['action']) && ($HTTP_POST_VARS['action'] == 'process') ) {
    if (!tep_session_is_registered('comments')) tep_session_register('comments');
    if (tep_not_null($HTTP_POST_VARS['comments'])) {
      $comments = tep_db_prepare_input($HTTP_POST_VARS['comments']);
    }

// modified by splautz to include freight shipping
    if (tep_session_is_registered('shipping')) {
      tep_session_unregister('shipping');
    }
    $shipping = false;
    if ( (tep_count_shipping_modules() > 0) || (in_array(true,$free_shipping)) ) {
      for($i = 0; $i < 2; $i++) {
        if ( (isset($HTTP_POST_VARS[strtolower($stype[$i])])) && (strpos($HTTP_POST_VARS[strtolower($stype[$i])], '_')) ) {
          $ship_selection = $HTTP_POST_VARS[strtolower($stype[$i])];
          list($module, $method) = explode('_', $ship_selection);
          if ( is_object($$module) || ($ship_selection == 'free_free') ) {
            if ($ship_selection == 'free_free') {
              $quote[0]['methods'][0]['title'] = constant('FREE_'.$stype[$i].'_TITLE');
              $quote[0]['methods'][0]['cost'] = '0';
            } else {
              $quote = $shipping_modules->quote($method, $module);
            }
            if (!isset($quote[0]['error']) && isset($quote[0]['methods'][0]['title']) && isset($quote[0]['methods'][0]['cost'])) {
              if (!tep_session_is_registered('shipping')) tep_session_register('shipping');
              $shipping[$i] = array('id' => $ship_selection,
                                    'title' => (($free_shipping[$i] == true) ?  $quote[0]['methods'][0]['title'] : $quote[0]['module'] . ' (' . $quote[0]['methods'][0]['title'] . ')'),
                                    'cost' => $quote[0]['methods'][0]['cost']);
            }
          }
        }
      }
    } else {
      tep_session_register('shipping');
      $shipping = false;
      tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
    }
    if (TABLE_HEADING_TERMS && !isset($HTTP_POST_VARS['terms'])) {
      $noterms = true;
      if (ERROR_TERMS) $messageStack->add('header', ERROR_TERMS, 'warning');
    } elseif (tep_session_is_registered('shipping')) tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
// end freight shipping modification

  }

// get all available shipping quotes
// modified by splautz for freight shipping
//$quotes = $shipping_modules->quote();
  $squotes = $shipping_modules->quote();

// if no shipping method has been selected, automatically select the cheapest method.
// if the modules status was changed when none were available, to save on implementing
// a javascript force-selection method, also automatically select the cheapest shipping
// method if more than one module is now enabled
  $cheapest = $shipping_modules->cheapest();
  if ( !tep_session_is_registered('shipping') || ( tep_session_is_registered('shipping') && ($shipping == false) && (tep_count_shipping_modules() > 0) ) ) $shipping = $cheapest;

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>"> 
<title><?php echo TITLE; ?></title>
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<link rel="stylesheet" type="text/css" href="stylesheet.css">
<script language="javascript"><!--
var selected;

function selectRowEffect(object, buttonSelect) {
  if (!selected) {
    if (document.getElementById) {
      selected = document.getElementById('defaultSelected');
    } else {
      selected = document.all['defaultSelected'];
    }
  }

  if (selected) selected.className = 'moduleRow';
  object.className = 'moduleRowSelected';
  selected = object;

// one button is not an array
  if (document.checkout_address.shipping[0]) {
    document.checkout_address.shipping[buttonSelect].checked=true;
  } else {
    document.checkout_address.shipping.checked=true;
  }
}

function rowOverEffect(object) {
  if (object.className == 'moduleRow') object.className = 'moduleRowOver';
}

function rowOutEffect(object) {
  if (object.className == 'moduleRowOver') object.className = 'moduleRow';
}
//--></script>
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
            <td class="pageHeading" align="right"><?php echo tep_image(DIR_WS_IMAGES . 'table_background_delivery.gif', HEADING_TITLE, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main"><b><?php echo TABLE_HEADING_SHIPPING_ADDRESS; ?></b></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                <td class="main" width="50%" valign="top">
<?php 
echo TEXT_CHOOSE_SHIPPING_DESTINATION . '<br><br>'; 
if(tep_session_is_registered('paypal_token')) { 
?>
<form method="post" name="form" action="paypal_wpp/ec/process.php">
<input type="hidden" name="express" value="1"> 
<input type="hidden" name="amount" value="<?=$cart->show_total();?>"> 
<?php echo tep_image_submit('button_change_address.gif', IMAGE_BUTTON_CHANGE_ADDRESS); ?> 
</form>
<?php 
} else {  
  echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING_ADDRESS, '', 'SSL') . '">' . tep_image_button('button_change_address.gif', IMAGE_BUTTON_CHANGE_ADDRESS) . '</a>'; 
} 
?>
                </td>
                <td align="right" width="50%" valign="top"><table border="0" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="main" align="center" valign="top"><?php echo '<b>' . TITLE_SHIPPING_ADDRESS . '</b><br>' . tep_image(DIR_WS_IMAGES . 'arrow_south_east.gif'); ?></td>
                    <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                    <td class="main" valign="top"><?php echo tep_address_label($customer_id, $sendto, true, ' ', '<br>'); ?></td>
                    <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                  </tr>
                </table></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
  echo tep_draw_form('checkout_address', tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL')) . tep_draw_hidden_field('action', 'process');

// modified by splautz for freight shipping
//if (tep_count_shipping_modules() > 0) {
  if (count($squotes) != count($cheapest)) {
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main"><b><?php echo constant('TABLE_HEADING_'.$stype[0].'_METHOD'); ?></b></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td class="main"><?php echo constant('TEXT_'.$stype[0].'_ERROR'); ?></td>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
<?php
    $back = sizeof($navigation->path)-2;
    if (isset($navigation->path[$back])) {
?>
                <td class="main" align="right"><?php echo '<a href="' . tep_href_link($navigation->path[$back]['page'], tep_array_to_string($navigation->path[$back]['get'], array('action')), $navigation->path[$back]['mode']) . '">' . tep_image_button('button_continue_shopping.gif', IMAGE_BUTTON_CONTINUE_SHOPPING) . '</a>'; ?></td>
<?php
    }
?>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
<?php
  } else {
   foreach($squotes as $freight => $quotes) {
    $num_items = $cart->count_contents($freight);
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main"><b><?php echo constant('TABLE_HEADING_'.$stype[$freight].'_METHOD').' ('.$num_items.' item'.($num_items==1?'':'s').')'; ?></b></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
    if (sizeof($quotes) > 1 && sizeof($quotes[0]) > 1) {
?>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td class="main" width="50%" valign="top"><?php echo constant('TEXT_CHOOSE_'.$stype[$freight].'_METHOD'); ?></td>
                <td class="main" width="50%" valign="top" align="right"><?php echo '<b>' . TITLE_PLEASE_SELECT . '</b><br>' . tep_image(DIR_WS_IMAGES . 'arrow_east_south.gif'); ?></td>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
<?php
    } elseif ($free_shipping[$freight] == false) {  // modified by splautz for freight shipping
?>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td class="main" width="100%" colspan="2"><?php echo constant('TEXT_ENTER_'.$stype[$freight].'_INFORMATION'); ?></td>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
<?php
    }

    if ($free_shipping[$freight] == true) {  // modified by splautz for freight shipping
?>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td colspan="2" width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                    <td class="main" colspan="3"><b><?php echo constant('FREE_'.$stype[$freight].'_TITLE'); ?></b>&nbsp;</td>
                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                  </tr>
                  <tr id="defaultSelected" class="moduleRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, 0)">
                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                    <td class="main" width="100%"><?php echo sprintf(constant('FREE_'.$stype[$freight].'_DESCRIPTION'), $currencies->format(constant('MODULE_ORDER_TOTAL_'.$stype[$freight].'_FREE_SHIPPING_OVER'))) . tep_draw_hidden_field(strtolower($stype[$freight]), 'free_free'); ?></td>
                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                  </tr>
                </table></td>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
              </tr>
<?php
    } else {
      $radio_buttons = 0;
      for ($i=0, $n=sizeof($quotes); $i<$n; $i++) {
?>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                    <td class="main" colspan="3"><b><?php echo $quotes[$i]['module']; ?></b>&nbsp;<?php if (isset($quotes[$i]['icon']) && tep_not_null($quotes[$i]['icon'])) { echo $quotes[$i]['icon']; } ?></td>
                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                  </tr>
<?php
        if (isset($quotes[$i]['error'])) {
?>
                  <tr>
                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                    <td class="main" colspan="3"><?php echo $quotes[$i]['error']; ?></td>
                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                  </tr>
<?php
        } else {
          for ($j=0, $n2=sizeof($quotes[$i]['methods']); $j<$n2; $j++) {
            if (!is_numeric($quotes[$i]['methods'][$j]['cost'])) {
?>
                  <tr class="moduleRow">
                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                    <td class="main" width="75%"><?php echo $quotes[$i]['methods'][$j]['title']; ?></td>
                    <td class="main"><?php echo $quotes[$i]['methods'][$j]['cost']; ?></td>
                    <td></td>
                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                  </tr>
<?php
            } else {
// set the radio button to be checked if it is the method chosen
              $checked = (($quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'] == $shipping[$freight]['id']) ? true : false);

              if ( ($checked == true) || ($n == 1 && $n2 == 1) ) {
                echo '                  <tr id="defaultSelected" class="moduleRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, ' . $radio_buttons . ')">' . "\n";
              } else {
                echo '                  <tr class="moduleRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, ' . $radio_buttons . ')">' . "\n";
              }
?>
                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                    <td class="main" width="75%"><?php echo $quotes[$i]['methods'][$j]['title']; ?></td>
<?php
              if ( ($n > 1) || ($n2 > 1) ) {
?>
                    <td class="main"><?php echo $currencies->format(tep_add_tax($quotes[$i]['methods'][$j]['cost'], (isset($quotes[$i]['tax']) ? $quotes[$i]['tax'] : 0))); ?></td>
                    <td class="main" align="right"><?php echo tep_draw_radio_field(strtolower($stype[$freight]), $quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'], $checked); ?></td>
<?php
              } else {
?>
                    <td class="main" align="right" colspan="2"><?php echo $currencies->format(tep_add_tax($quotes[$i]['methods'][$j]['cost'], $quotes[$i]['tax'])) . tep_draw_hidden_field(strtolower($stype[$freight]), $quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id']); ?></td>
<?php
              }
?>
                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                  </tr>
<?php
              $radio_buttons++;
            }
          }
        }
?>
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
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
   }
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main"><b><?php echo TABLE_HEADING_COMMENTS; ?></b></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td><?php echo tep_draw_textarea_field('comments', 'soft', '60', '5'); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
<?php
   if (TABLE_HEADING_TERMS) {
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main" align="right"><?php if ($noterms) echo '<span class="inputRequirement"><b>';
            echo TABLE_HEADING_TERMS.'&nbsp;'.tep_draw_checkbox_field('terms');
            if ($noterms) echo '&nbsp;<-----</b></span>'; ?></td>
          </tr>
        </table></td>
      </tr>
<?php
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
                <td class="main"><?php echo '<b>' . TITLE_CONTINUE_CHECKOUT_PROCEDURE . '</b><br>' . TEXT_CONTINUE_CHECKOUT_PROCEDURE; ?></td>
                <td class="main" align="right"><?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></td>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
<?php
  }
?>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td width="25%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td width="50%" align="right"><?php echo tep_image(DIR_WS_IMAGES . 'checkout_bullet.gif'); ?></td>
                <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
              </tr>
            </table></td>
            <td width="25%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
            <td width="25%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
            <td width="25%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
                <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td align="center" width="25%" class="checkoutBarCurrent"><?php echo CHECKOUT_BAR_DELIVERY; ?></td>
            <td align="center" width="25%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_PAYMENT; ?></td>
            <td align="center" width="25%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_CONFIRMATION; ?></td>
            <td align="center" width="25%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_FINISHED; ?></td>
          </tr>
        </table></td>
      </tr></form>
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
