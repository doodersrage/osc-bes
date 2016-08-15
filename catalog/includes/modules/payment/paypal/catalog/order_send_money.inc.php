<?php
/*
  $Id: order_send_money.inc.php,v 1.1.1.1 2004/09/22 13:45:13 devosc Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  DevosC, Developing open source Code
  http://www.devosc.com

  Copyright (c) 2003 osCommerce
  Copyright (c) 2004 DevosC.com

  Released under the GNU General Public License
*/

  if(strtolower($order->info['payment_method']) == 'paypal' && $order->info['orders_status_id'] == MODULE_PAYMENT_PAYPAL_PROCESSING_STATUS_ID && MODULE_PAYMENT_PAYPAL_INVOICE_REQUIRED == 'True' ) {
    include_once(DIR_WS_MODULES . 'payment/paypal.php');
    $paypal = new PayPal();
    echo '<div style="float:right">' . "\n";
    echo tep_draw_form('paypal', $paypal->form_paypal_url, 'post');
    echo $paypal->sendMoneyFields($order,$HTTP_GET_VARS['order_id'])."\n";
    echo tep_image_submit('button_confirm_order.gif', IMAGE_BUTTON_CONFIRM_ORDER) . '</form>' . "\n";
    echo '</div>' . "\n";
  }
?>
