<?php
require_once('mobile/includes/application_top.php');

// if the customer is not logged on, redirect them to the login page
  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_mobile_link(FILENAME_LOGIN, '', 'SSL'));
  }

// if there is nothing in the customers cart, redirect them to the shopping cart page
  if ($cart->count_contents() < 1) {
    tep_redirect(tep_mobile_link(FILENAME_SHOPPING_CART));
  }

// if no shipping method has been selected, redirect the customer to the shipping method selection page
  if (!tep_session_is_registered('shipping')) {
    tep_redirect(tep_mobile_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
  }

// avoid hack attempts during the checkout procedure by checking the internal cartID
  if (isset($cart->cartID) && tep_session_is_registered('cartID')) {
    if ($cart->cartID != $cartID) {
      tep_redirect(tep_mobile_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
    }
  }

// Stock Check
  if ( (STOCK_CHECK == 'true') && (STOCK_ALLOW_CHECKOUT != 'true') ) {
    $products = $cart->get_products();
    for ($i=0, $n=sizeof($products); $i<$n; $i++) {
      if (tep_check_stock($products[$i]['id'], $products[$i]['quantity'])) {
        tep_redirect(tep_mobile_link(FILENAME_SHOPPING_CART));
        break;
      }
    }
  }

// if no billing destination address was selected, use the customers own address as default
  if (!tep_session_is_registered('billto')) {
    tep_session_register('billto');
    $billto = $customer_default_address_id;
  } else {
// verify the selected billing address
    if ( (is_array($billto) && empty($billto)) || is_numeric($billto) ) {
      $check_address_query = tep_db_query("select count(*) as total from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int)$customer_id . "' and address_book_id = '" . (int)$billto . "'");
      $check_address = tep_db_fetch_array($check_address_query);

      if ($check_address['total'] != '1') {
        $billto = $customer_default_address_id;
        if (tep_session_is_registered('payment')) tep_session_unregister('payment');
      }
    }
  }

  require(DIR_WS_CLASSES . 'order.php');
  $order = new order;

  if (!tep_session_is_registered('comments')) tep_session_register('comments');
  if (isset($HTTP_POST_VARS['comments']) && tep_not_null($HTTP_POST_VARS['comments'])) {
    $comments = tep_db_prepare_input($HTTP_POST_VARS['comments']);
  }

  $total_weight = $cart->show_weight();
  $total_count = $cart->count_contents();

// load all enabled payment modules
  require(DIR_WS_CLASSES . 'payment.php');
  $payment_modules = new payment;

  require(DIR_WS_LANGUAGES . $language . '/checkout_payment.php');

  $breadcrumb->add(NAVBAR_TITLE_1, tep_mobile_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_mobile_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
	
  require(DIR_MOBILE_INCLUDES . 'header.php');
	$headerTitle->write();
?>
<?php echo tep_draw_form('checkout_payment', tep_mobile_link(FILENAME_CHECKOUT_CONFIRMATION, '', 'SSL'), 'post', 'onsubmit="return check_form();"'); ?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr class="categories">
		<td class="main" style="padding: 5px;"><input type="submit" value="<?php echo IMAGE_BUTTON_CONTINUE; ?>"></td>
      </tr>
<?php
  if (isset($HTTP_GET_VARS['payment_error']) && is_object(${$HTTP_GET_VARS['payment_error']}) && ($error = ${$HTTP_GET_VARS['payment_error']}->get_error())) {
?>
      <tr>
            <td class="messageStackError"><b><?php echo tep_output_string_protected($error['title']); ?></b></td>
      </tr>
      <tr class="infoBoxNoticeContents">
            <td class="messageStackError" width="100%" valign="top"><?php echo tep_output_string_protected($error['error']); ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '2'); ?></td>
      </tr>
<?php
  }
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main"><b><?php echo TABLE_HEADING_PAYMENT_METHOD; ?></b></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td>
			<table border="0" cellspacing="0" cellpadding="2">
<?php
  $selection = $payment_modules->selection();

  if (sizeof($selection) > 1) {
?>
              <tr>
                <td class="smallText" valign="top" colspan="10"><?php echo TEXT_SELECT_PAYMENT_METHOD; ?></td>
              </tr>
<?php
  } else {
?>
              <tr>
                <td class="smallText" valign="top" colspan="10"><?php echo TEXT_ENTER_PAYMENT_INFORMATION; ?></td>
              </tr>
<?php
  }

  $radio_buttons = 0;
  for ($i=0, $n=sizeof($selection); $i<$n; $i++) {
?>
<?php
    if ( ($selection[$i]['id'] == $payment) || ($n == 1) ) {
      echo '    <tr id="defaultSelected" class="moduleRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, ' . $radio_buttons . ')">' . "\n";
    } else {
      echo '    <tr class="moduleRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, ' . $radio_buttons . ')">' . "\n";
    }
?>
                <td class="main" align="right">
<?php
    if (sizeof($selection) > 1) {
      echo tep_draw_radio_field('payment', $selection[$i]['id'], ($selection[$i]['id'] == $payment));
    } else {
      echo tep_draw_hidden_field('payment', $selection[$i]['id']);
    }
?>
                </td>
                <td class="main"><b><?php echo $selection[$i]['module']; ?></b></td>
			  </tr>
<?php
    if (isset($selection[$i]['error'])) {
?>
              <tr>
				<td class="main" colspan="4"><?php echo $selection[$i]['error']; ?></td>
              </tr>
<?php
    } elseif (isset($selection[$i]['fields']) && is_array($selection[$i]['fields'])) {
?>
			  <tr>
              	<td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td colspan="4"><table border="0" cellspacing="0" cellpadding="2">
<?php
      for ($j=0, $n2=sizeof($selection[$i]['fields']); $j<$n2; $j++) {
?>
                      <tr>
                        <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                        <td class="main"><?php echo $selection[$i]['fields'][$j]['title']; ?></td>
                        <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                        <td class="main"><?php echo $selection[$i]['fields'][$j]['field']; ?></td>
                        <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                      </tr>
<?php
      }
?>
                    </table></td>
                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                  </tr>
<?php
    }
    $radio_buttons++;
  }
?>
            </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '2'); ?></td>
      </tr>
      <tr>
        <td class="main"><b><?php echo TABLE_HEADING_BILLING_ADDRESS; ?></b></td>
      </tr>
      <tr>
		<td class="main" valign="top"><?php echo tep_address_label($customer_id, $billto, true, ' ', '<br>'); ?></td>
      </tr>
      <tr>
        <td class="smallText"><?php echo '<a href="' . tep_mobile_link(FILENAME_CHECKOUT_PAYMENT_ADDRESS, '', 'SSL') . '">' . IMAGE_BUTTON_CHANGE_ADDRESS . '</a>'; ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '2'); ?></td>
      </tr>
      <tr>
            <td class="main"><b><?php echo TABLE_HEADING_COMMENTS; ?></b></td>
      </tr>
      <tr>
		<td><?php echo tep_draw_textarea_field('comments', 'soft', '30', '3', $comments); ?></td>
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
