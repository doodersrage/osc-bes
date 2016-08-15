<?php
/*
  $Id: osC_invoice.php,v 6.1 2005/06/05 00:37:30 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
?>
<html>
<head>
<title><?php echo STORE_NAME; ?> <?php echo INVOICE_TEXT_INVOICE; ?> <?php echo INVOICE_TEXT_NUMBER_SIGN; ?> <?php echo substr($order->info['date_purchased'],2,2).INVOICE_TEXT_DASH.str_pad($oID,7,'0',STR_PAD_LEFT); ?></title>
</head>
<body>

<strong><?php echo nl2br(STORE_NAME); ?></strong>
<br>
====================================
<br>
<b>Order Number:</b> <?php echo $oID; ?>
<br>
<b>Payment Method:</b> <?php echo $order->info['payment_method']; ?>
<?php
  if (tep_not_null($order->info['purchase_order_number'])) echo '<br>  ' . ENTRY_PURCHASE_ORDER_NUMBER . ' ' . $order->info['purchase_order_number'];
?>
<br>
<b>Date of Order:</b> <?php echo $date; ?>
<br><br>

<strong>Products Ordered:</strong>
<br>
====================================
<br>
<?php
    for ($i = 0, $n = sizeof($order->products); $i < $n; $i++) {
      echo $order->products[$i]['qty'] . '&nbsp;x&nbsp;' . $order->products[$i]['name'] . ' (' . $order->products[$i]['model'] . ') = ' . $currencies->format($order->products[$i]['final_price'], true, $order->info['currency'], $order->info['currency_value']);
      if (isset($order->products[$i]['attributes']) && (($k = sizeof($order->products[$i]['attributes'])) > 0)) {
        for ($j = 0; $j < $k; $j++) {
          echo '<br><nobr><small>&nbsp;&nbsp;&nbsp;<i> - ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'];
          if ($order->products[$i]['attributes'][$j]['price'] != '0') echo ' (' . $order->products[$i]['attributes'][$j]['prefix'] . $currencies->format($order->products[$i]['attributes'][$j]['price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . ')';
          echo '</i></small></nobr>';
        }
      }
    }
?>
<br><br>

<?php
  for ($i = 0, $n = sizeof($order->totals); $i < $n; $i++) {
    echo $order->totals[$i]['title'] . '&nbsp;' . $order->totals[$i]['text'] . '<br>';
  }
?>
<br>

<b>Billing Address</b>
<br>
====================================
<br>
<?php echo tep_address_format($order->customer['format_id'], $order->customer, 1, '', '<br>'); ?>
<br>
<?php echo $order->customer['telephone']; ?>
<br>
<?php echo '<a href="mailto:' . $order->customer['email_address'] . '"><u>' . $order->customer['email_address'] . '</u></a>'; ?>
<br><br>

<b>Shipping Address:</b>
<br>
====================================
<br>
<?php echo tep_address_format($order->delivery['format_id'], $order->delivery, 1, '', '<br>'); ?>

<br>
</body>
</html>