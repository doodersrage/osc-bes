<?php
require_once('mobile/includes/application_top.php');

  require(DIR_WS_CLASSES.'http_client.php');

// if the customer is not logged on, redirect them to the login page
  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_mobile_link(FILENAME_LOGIN, '', 'SSL'));
  }

// if there is nothing in the customers cart, redirect them to the shopping cart page
  if ($cart->count_contents() < 1) {
    tep_redirect(tep_mobile_link(FILENAME_SHOPPING_CART));
  }

// if no shipping destination address was selected, use the customers own address as default
  if (!tep_session_is_registered('sendto')) {
    tep_session_register('sendto');
    $sendto = $customer_default_address_id;
  } else {
// verify the selected shipping address
    if ( (is_array($sendto) && empty($sendto)) || is_numeric($sendto) ) {
      $check_address_query = tep_db_query("select count(*) as total from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int)$customer_id . "' and address_book_id = '" . (int)$sendto . "'");
      $check_address = tep_db_fetch_array($check_address_query);

      if ($check_address['total'] != '1') {
        $sendto = $customer_default_address_id;
        if (tep_session_is_registered('shipping')) tep_session_unregister('shipping');
      }
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
  if ($order->content_type == 'virtual') {
    if (!tep_session_is_registered('shipping')) tep_session_register('shipping');
    $shipping = false;
    $sendto = false;
    tep_redirect(tep_mobile_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
  }

  $total_weight = $cart->show_weight();
  $total_count = $cart->count_contents();

// load all enabled shipping modules
  require(DIR_WS_CLASSES . 'shipping.php');
  $shipping_modules = new shipping;

  if ( defined('MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING') && (MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING == 'true') ) {
    $pass = false;

    switch (MODULE_ORDER_TOTAL_SHIPPING_DESTINATION) {
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

    $free_shipping = false;
    if ( ($pass == true) && ($order->info['total'] >= MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER) ) {
      $free_shipping = true;

      include(DIR_WS_LANGUAGES . $language . '/modules/order_total/ot_shipping.php');
    }
  } else {
    $free_shipping = false;
  }

// process the selected shipping method
  if ( isset($HTTP_POST_VARS['action']) && ($HTTP_POST_VARS['action'] == 'process') ) {
    if (!tep_session_is_registered('comments')) tep_session_register('comments');
    if (tep_not_null($HTTP_POST_VARS['comments'])) {
      $comments = tep_db_prepare_input($HTTP_POST_VARS['comments']);
    }

    if (!tep_session_is_registered('shipping')) tep_session_register('shipping');

    if ( (tep_count_shipping_modules() > 0) || ($free_shipping == true) ) {
      if ( (isset($HTTP_POST_VARS['shipping'])) && (strpos($HTTP_POST_VARS['shipping'], '_')) ) {
        $shipping = $HTTP_POST_VARS['shipping'];

        list($module, $method) = explode('_', $shipping);
        if ( is_object($$module) || ($shipping == 'free_free') ) {
          if ($shipping == 'free_free') {
            $quote[0]['methods'][0]['title'] = FREE_SHIPPING_TITLE;
            $quote[0]['methods'][0]['cost'] = '0';
          } else {
            $quote = $shipping_modules->quote($method, $module);
          }
          if (isset($quote['error'])) {
            tep_session_unregister('shipping');
          } else {
            if ( (isset($quote[0]['methods'][0]['title'])) && (isset($quote[0]['methods'][0]['cost'])) ) {
              $shipping = array('id' => $shipping,
                                'title' => (($free_shipping == true) ?  $quote[0]['methods'][0]['title'] : $quote[0]['module'] . ' (' . $quote[0]['methods'][0]['title'] . ')'),
                                'cost' => $quote[0]['methods'][0]['cost']);

              tep_redirect(tep_mobile_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
            }
          }
        } else {
          tep_session_unregister('shipping');
        }
      }
    } else {
      $shipping = false;
                
      tep_redirect(tep_mobile_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
    }    
  }

// get all available shipping quotes
  $quotes = $shipping_modules->quote();

// if no shipping method has been selected, automatically select the cheapest method.
// if the modules status was changed when none were available, to save on implementing
// a javascript force-selection method, also automatically select the cheapest shipping
// method if more than one module is now enabled
  if ( !tep_session_is_registered('shipping') || ( tep_session_is_registered('shipping') && ($shipping == false) && (tep_count_shipping_modules() > 1) ) ) $shipping = $shipping_modules->cheapest();

  require(DIR_WS_LANGUAGES . $language . '/checkout_shipping.php');

  $breadcrumb->add(NAVBAR_TITLE_1, tep_mobile_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_mobile_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));

require(DIR_MOBILE_INCLUDES . 'header.php');
$headerTitle->write();
?>
<?php echo tep_draw_form('checkout_address', tep_mobile_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL')) . tep_draw_hidden_field('action', 'process'); ?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr class="categories">
		<td class="main" style="padding: 5px;"><input type="submit" value="<?php echo IMAGE_BUTTON_CONTINUE; ?>"></td>
      </tr>
<?php
  if (tep_count_shipping_modules() > 0) {
?>
      <tr>
	  	<td class="main"><b><?php echo TABLE_HEADING_SHIPPING_METHOD; ?></b></td>
      </tr>
      <tr>
        <td>
			<table border="0" cellspacing="0" cellpadding="2">
<?php
	if (sizeof($quotes) > 1 && sizeof($quotes[0]) > 1) {
?>
              <tr>
                <td class="smallText" valign="top" colspan="10"><?php echo TEXT_CHOOSE_SHIPPING_METHOD; ?></td>
              </tr>
<?php
    } elseif ($free_shipping == false) {
?>
              <tr>
                <td class="smallText" colspan="10"><?php echo TEXT_ENTER_SHIPPING_INFORMATION; ?></td>
              </tr>
<?php
    }

    if ($free_shipping == true) {
?>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td colspan="2" width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                    <td class="main" colspan="3"><b><?php echo FREE_SHIPPING_TITLE; ?></b>&nbsp;<?php echo $quotes[$i]['icon']; ?></td>
                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                  </tr>
                  <tr id="defaultSelected" class="moduleRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, 0)">
                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                    <td class="main" width="100%"><?php echo sprintf(FREE_SHIPPING_DESCRIPTION, $currencies->format(MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER)) . tep_draw_hidden_field('shipping', 'free_free'); ?></td>
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
                <td class="main" colspan="10"><b><?php echo $quotes[$i]['module']; ?></b>&nbsp;<?php if (isset($quotes[$i]['icon']) && tep_not_null($quotes[$i]['icon'])) { echo $quotes[$i]['icon']; } ?></td>
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
// set the radio button to be checked if it is the method chosen
            $checked = (($quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'] == $shipping['id']) ? true : false);

            if ( ($checked == true) || ($n == 1 && $n2 == 1) ) {
              echo '                  <tr id="defaultSelected" class="moduleRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, ' . $radio_buttons . ')">' . "\n";
            } else {
              echo '                  <tr class="moduleRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, ' . $radio_buttons . ')">' . "\n";
            }

            if ( ($n > 1) || ($n2 > 1) ) {
?>
                    <td class="main" align="right"><?php echo tep_draw_radio_field('shipping', $quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'], $checked); ?></td>
                    <td class="main"><?php echo $currencies->format(tep_add_tax($quotes[$i]['methods'][$j]['cost'], (isset($quotes[$i]['tax']) ? $quotes[$i]['tax'] : 0))); ?></td>
                    <td class="main"><?php echo $quotes[$i]['methods'][$j]['title']; ?></td>
<?php
            } else {
?>
                    <td class="main"><?php echo $currencies->format(tep_add_tax($quotes[$i]['methods'][$j]['cost'], $quotes[$i]['tax'])) . tep_draw_hidden_field('shipping', $quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id']); ?></td>
                    <td class="main"><?php echo $quotes[$i]['methods'][$j]['title']; ?></td>
<?php
            }
?>
                  </tr>
<?php
            $radio_buttons++;
          }
        }
      }
    }
?>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '2'); ?></td>
      </tr>
<?php
  }
?>
      <tr>
		<td class="main"><b><?php echo TABLE_HEADING_SHIPPING_ADDRESS; ?></b></td>
      </tr>
      <tr>
		<td class="main" valign="top"><?php echo tep_address_label($customer_id, $sendto, true, ' ', '<br>'); ?></td>
      </tr>
      <tr>
 		<td class="smallText"><?php echo '<a href="' . tep_mobile_link(FILENAME_CHECKOUT_SHIPPING_ADDRESS, '', 'SSL') . '">' . IMAGE_BUTTON_CHANGE_ADDRESS . '</a>'; ?></td>
	  </tr>		
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '2'); ?></td>
      </tr>
      <tr>
		<td class="main"><b><?php echo TABLE_HEADING_COMMENTS; ?></b></td>
      </tr>
      <tr>
		<td><?php echo tep_draw_textarea_field('comments', 'soft', '30', '3'); ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '2'); ?></td>
      </tr>
      <tr>
		<td class="main"><input type="submit" value="<?php echo IMAGE_BUTTON_CONTINUE; ?>"></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '2'); ?></td>
      </tr>
      <tr><td><table width="100%"  cellspacing="0" cellpadding="0">
<?php echo 	tep_mobile_selection(tep_mobile_link(FILENAME_SHOPPING_CART, '', 'SSL'), array(BOX_HEADING_SHOPPING_CART) ); ?>
	  </table></td></tr>
    </table>
</form>
<?php require(DIR_MOBILE_INCLUDES . 'footer.php'); ?>
