<?php
/*
  $Id: account_history_info.php,v 6.1 2005/06/05 23:03:52 PopTheTop Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

// BEGIN Fancier Order Details & Print Invoice

  require('includes/application_top.php');

  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

  if (!isset($HTTP_GET_VARS['order_id']) || (isset($HTTP_GET_VARS['order_id']) && !is_numeric($HTTP_GET_VARS['order_id']))) {
    tep_redirect(tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));
  }
  
  $customer_info_query = tep_db_query("select customers_id from " . TABLE_ORDERS . " where orders_id = '". (int)$HTTP_GET_VARS['order_id'] . "'");
  $customer_info = tep_db_fetch_array($customer_info_query);
  if ($customer_info['customers_id'] != $customer_id) {
    tep_redirect(tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_ACCOUNT_HISTORY_INFO);

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));
  $breadcrumb->add(sprintf(NAVBAR_TITLE_3, $HTTP_GET_VARS['order_id']), tep_href_link(FILENAME_ACCOUNT_HISTORY_INFO, 'order_id=' . $HTTP_GET_VARS['order_id'], 'SSL'));

  require(DIR_WS_CLASSES . 'order.php');
  $order = new order($HTTP_GET_VARS['order_id']);
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo STORE_NAME; ?> <?php echo HEADING_TITLE; ?> <?php echo $HTTP_GET_VARS['order_id']; ?></title>
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<link rel="stylesheet" type="text/css" href="stylesheet.css">
<script language="javascript">
<!--
function popupPrintReceipt(url) {
  window.open(url,'popupPrintReceipt','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=750')
}
//-->
</script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
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
	  <td style="padding: 10px 0px 0px 10px; vertical-align: top;" align="left" class="order_history_top"><?php echo HEADING_INVOICE_NUMBER; ?> <?php echo substr($order->info['date_purchased'],2,2).'-'.str_pad($HTTP_GET_VARS['order_id'],7,'0',STR_PAD_LEFT); ?><BR><?php echo date("M j, Y", mktime(0,0,0,substr($order->info['date_purchased'],5,2),substr($order->info['date_purchased'],8,2),substr($order->info['date_purchased'],0,4))); ?></td>
	</tr>
	<tr>
   	<td VALIGN="top">
		<table border="0" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<TD>
				<table width="100%" border="0" cellspacing="0" cellpadding="2">
		        <tr>
					 <td colspan="4">
					 <table width="100%" border="0" cellspacing="0" cellpadding="2">
			        <tr>
	      		    <td width="10%"><hr size="2"></td>
			          <td align="center" class="pageHeading" NOWRAP><em><b><?php echo HEADING_TITLE2; ?></b></em></td>
      			    <td width="100%"><hr size="2"></td>
		   	     </tr>
			       </table>
			       </td>
				   </tr>
				   <tr>
				   	<td colspan="4"><?php echo tep_draw_separator('pixel_trans.gif', '100', '5'); ?></td>
				   </tr>
			      <tr>
			      	<td valign="top">
			         <table width="100%" border="0" cellpadding="0" cellspacing="0">
         		     <tr>
		                <td width="11"><img src="images/borders/mainwhite_01.gif" width="11" height="16" alt=""></td>
      		          <td background="images/borders/mainwhite_02.gif"><img src="images/borders/mainwhite_02.gif" width="24" height="16" alt="" ></td>
            		    <td width="19"><img src="images/borders/mainwhite_03.gif" width="19" height="16" alt=""></td>
         		     </tr>
         		     <tr>
		                <td background="images/borders/mainwhite_04.gif"><img src="images/borders/mainwhite_04.gif" width="11" height="21" alt=""></td>
		                <td align="center" style="background-color: white;">
							 	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="main">
            		        <tr>
                  		    <td align="left" valign="top" class="order_infobox_heading"><b><?php echo HEADING_BILLING_ADDRESS; ?></b></td>
		                    </tr>
      		              <tr>
            		          <td class="order_infobox_data">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo tep_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
                  		  </tr>
		                    <tr>
      		                <td class="order_infobox_data">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo tep_address_format($order->customer['format_id'], $order->customer, 1, '', '<br>&nbsp;&nbsp;&nbsp;&nbsp;'); ?></td>
            		        </tr>
                  		  <tr>
		                      <td class="order_infobox_data">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      		              </tr>
            		        <tr>
                  		    <td class="order_infobox_data">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $order->customer['telephone']; ?></td>
		                    </tr>
      		              <tr>
            		          <td class="order_infobox_data">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $order->customer['email_address']; ?></td>
		                    </tr>
                  		  <tr>
		                      <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '7'); ?></td>
      		              </tr>
		                  </table>
							 </td>
		                <td background="images/borders/mainwhite_06.gif"><img src="images/borders/mainwhite_06.gif" width="19" height="21" alt=""></td>
         		     </tr>
         		     <tr>
		                <td><img src="images/borders/mainwhite_07.gif" width="11" height="18" alt=""></td>
      		          <td background="images/borders/mainwhite_08.gif"><img src="images/borders/mainwhite_08.gif" width="24" height="18" alt=""></td>
		                <td><img src="images/borders/mainwhite_09.gif" width="19" height="18" alt=""></td>
         		     </tr>
			         </table>
			         </td>
		            <td width="15"> </td>
			         <td valign="top">
		            <table width="100%" border="0" cellpadding="0" cellspacing="0">
      		        <tr>
            		    <td width="11"><img src="images/borders/mainwhite_01.gif" width="11" height="16" alt=""></td>
		                <td background="images/borders/mainwhite_02.gif"><img src="images/borders/mainwhite_02.gif" width="24" height="16" alt=""></td>
      		          <td width="19"><img src="images/borders/mainwhite_03.gif" width="19" height="16" alt=""></td>
            		  </tr>
		              <tr>
      		          <td background="images/borders/mainwhite_04.gif"><img src="images/borders/mainwhite_04.gif" width="11" height="21" alt=""></td>
            		    <td align="center" bgcolor="#FFFFFF">
								 <table width="100%" border="0" cellpadding="0" cellspacing="0" class="main">
   	   		             <tr>
      	      		         <td align="left" valign="top" class="order_infobox_heading"><b><?php echo HEADING_DELIVERY_ADDRESS; ?></b></td>
		   	                </tr>
      		              <tr>
            		          <td class="order_infobox_data">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo tep_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
                  		  </tr>
		               	    <tr>
	      		               <td class="order_infobox_data">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo tep_address_format($order->delivery['format_id'], $order->delivery, 1, '', '<br>&nbsp;&nbsp;&nbsp;&nbsp;'); ?></td>
   	         		       </tr>
                  		  <tr>
		                      <td class="order_infobox_data">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      		              </tr>
            		        <tr>
                  		    <td class="order_infobox_data">&nbsp;&nbsp;&nbsp;&nbsp;</td>
		                    </tr>
      		              <tr>
            		          <td class="order_infobox_data">&nbsp;&nbsp;&nbsp;&nbsp;</td>
		                    </tr>
                  		  <tr>
		                      <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '7'); ?></td>
      		              </tr>
								 </table>
							 </td>
		                <td background="images/borders/mainwhite_06.gif"><img src="images/borders/mainwhite_06.gif" width="19" height="21" alt=""></td>
      		        </tr>
            		  <tr>
		                <td><img src="images/borders/mainwhite_07.gif" width="11" height="18" alt=""></td>
      		          <td background="images/borders/mainwhite_08.gif"><img src="images/borders/mainwhite_08.gif" width="24" height="18" alt=""></td>
            		    <td><img src="images/borders/mainwhite_09.gif" width="19" height="18" alt=""></td>
		              </tr>
      		      </table>
			         </td>
			      </tr>
			    </table>
				 </TD>
		  </tr>
		  <tr>
				<TD><?php echo tep_draw_separator('pixel_trans.gif', '100', '15'); ?></td>
		  </tr>
		  <tr>
				<TD>
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
  		        <tr>
	             <td width="2"> </td>
                <td>
		          <table width="100%" border="0" cellpadding="0" cellspacing="0">
  				       <tr>
            		   <td width="11"><img src="images/borders/mainwhite_01.gif" width="11" height="16" alt=""></td>
		               <td background="images/borders/mainwhite_02.gif"><img src="images/borders/mainwhite_02.gif" width="24" height="16" alt="" ></td>
      		         <td width="19"><img src="images/borders/mainwhite_03.gif" width="19" height="16" alt=""></td>
  				       </tr>
  				       <tr>
		               <td background="images/borders/mainwhite_04.gif"><img src="images/borders/mainwhite_04.gif" width="11" height="21" alt=""></td>
      		         <td align="center" style="background-color: white;">
							<table width="100%" border="0" cellpadding="0" cellspacing="0" class="main">
							 	<tr>
	   		   		      <td class="order_infobox_data">&nbsp;<b><?php echo HEADING_ORDER_NUMBER; ?>:</b>&nbsp;<?php echo $HTTP_GET_VARS['order_id']; ?><br><?php echo tep_draw_separator('pixel_trans.gif', '100', '6'); ?><br>&nbsp;<b><?php echo HEADING_ORDER_DATE; ?></b>&nbsp;<?php echo tep_date_short($order->info['date_purchased']); ?></td>
    			   		      <td align="right" class="order_infobox_data"><?php
  echo '&nbsp;<b>' . HEADING_PAYMENT_METHOD . '</b>&nbsp;' . $order->info['payment_method'];
  if (tep_not_null($order->info['cc_number'])) {
    echo '&nbsp;('.$order->info['cc_type'].')';
    $cc_card_number_less_middle_digits = substr($order->info['cc_number'], 0, 4) . str_repeat('x', (strlen($order->info['cc_number']) - 8)) . substr($order->info['cc_number'], -4);
    echo '<br>' . tep_draw_separator('pixel_trans.gif', '100%', '6') . '<br>&nbsp;<b>' . ENTRY_PAYMENT_CC_NUMBER . '</b>&nbsp;' . $cc_card_number_less_middle_digits;
  }
  if (tep_not_null($order->info['purchase_order_number'])) {
    echo '<br>' . tep_draw_separator('pixel_trans.gif', '100%', '6') . '<br>&nbsp;<b>' . ENTRY_PURCHASE_ORDER_NUMBER . '</b>&nbsp;' . $order->info['purchase_order_number'];  
  }
?></td>
				   	      </tr>
				            <tr>
		      		      	<td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '7'); ?></td>
		            		</tr>
							</table>
							</td>
  				       	<td background="images/borders/mainwhite_06.gif"><img src="images/borders/mainwhite_06.gif" width="19" height="21" alt=""></td>
  				       </tr>
  				       <tr>
  				       	<td><img src="images/borders/mainwhite_07.gif" width="11" height="18" alt=""></td>
  				       	<td background="images/borders/mainwhite_08.gif"><img src="images/borders/mainwhite_08.gif" width="24" height="18" alt=""></td>
  				       	<td><img src="images/borders/mainwhite_09.gif" width="19" height="18" alt=""></td>
  				       </tr>
  				    </table>
                </td>
  		        </tr>
            </table>
				</td>
		  </tr>
		  <tr>
				<TD><?php echo tep_draw_separator('pixel_trans.gif', '100%', '20'); ?></td>
		  </tr>
  		  <tr>
				<td align="center">
				<table border="0" width="99%" cellspacing="0" cellpadding="2">
      			<tr class="product_infobox_heading_row">
        				<td class="product_infobox_heading" colspan="2">&nbsp;<font color="#000000"><?php echo HEADING_PRODUCTS; ?></font></td>
        				<td WIDTH="80" class="product_infobox_heading"><font color="#000000"><?php echo HEADING_MODEL; ?></font></td>
        				<td WIDTH="80" class="product_infobox_heading" align="right"><font color="#000000"><?php echo HEADING_UNIT; ?></font></td>
        				<TD WIDTH="80" ALIGN="right" CLASS="product_infobox_heading"><font color="#000000"><?php echo HEADING_TOTAL; ?></font>&nbsp;</TD>
      			</tr>
<?php
    for ($i = 0, $n = sizeof($order->products); $i < $n; $i++) {
		echo '      <tr class="product_infobox_data_row">' . "\n" .
		     '        <td class="product_infobox_data" valign="top" align="right">' . $order->products[$i]['qty'] . '&nbsp;x</td>' . "\n" .
		     '        <td class="product_infobox_data" valign="top">' . $order->products[$i]['name'];

      if (isset($order->products[$i]['attributes']) && (($k = sizeof($order->products[$i]['attributes'])) > 0)) {
        for ($j = 0; $j < $k; $j++) {
          echo '<br><nobr><small>&nbsp;<i> - ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'];
          if ($order->products[$i]['attributes'][$j]['price'] != '0') echo ' (' . $order->products[$i]['attributes'][$j]['prefix'] . $currencies->format($order->products[$i]['attributes'][$j]['price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . ')';
          echo '</i></small></nobr>';
        }
      }

      echo '      				</td>' . "\n" .
           '      				<td WIDTH="80" class="product_infobox_data" valign="top">' . $order->products[$i]['model'] . '</td>' . "\n";
      echo '      				<td WIDTH="80" class="product_infobox_data" align="right" valign="top">' . $currencies->format($order->products[$i]['final_price'], true, $order->info['currency'], $order->info['currency_value']) . '</td>' . "\n" .
           '      				<td WIDTH="80" class="product_infobox_data" align="right" valign="top"><b>' . $currencies->format($order->products[$i]['final_price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . '&nbsp;</b></td>' . "\n";
      echo '      			</tr>' . "\n";
    }
?>
  			<tr>
  				<td align="right" colspan="5">
				<table border="0" cellspacing="0" cellpadding="2">
<?php
  for ($i = 0, $n = sizeof($order->totals); $i < $n; $i++) {
    echo '      			<tr>' . "\n" .
         '      				<td align="right" class="smallText">' . $order->totals[$i]['title'] . '</td>' . "\n" .
         '      				<td align="right" class="smallText">' . $order->totals[$i]['text'] . '</td>' . "\n" .
         '      			</tr>' . "\n";
  }
?>
      		</table>
				</td>
      	</tr>
      </table></td>
	</tr>


<?php
  if (DOWNLOAD_ENABLED == 'true') include(DIR_WS_MODULES . 'downloads.php');
?>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '18'); ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '18'); ?></td>
      </tr>
      <tr>
      	<TD>
         <table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td width="2"> </td>
               <td>
		         <table width="100%" border="0" cellpadding="0" cellspacing="0">
  				     <tr>
            	    <td width="11"><img src="images/borders/mainwhite_01.gif" width="11" height="16" alt=""></td>
		             <td background="images/borders/mainwhite_02.gif"><img src="images/borders/mainwhite_02.gif" width="24" height="16" alt="" ></td>
      		       <td width="19"><img src="images/borders/mainwhite_03.gif" width="19" height="16" alt=""></td>
  				     </tr>
  				     <tr>
		             <td background="images/borders/mainwhite_04.gif"><img src="images/borders/mainwhite_04.gif" width="11" height="21" alt=""></td>
      		       <td align="center" style="background-color: white;">
						 <table width="100%" border="0" cellpadding="0" cellspacing="0" class="main">
   	   		       <tr>
      	      	       <td colspan="3" align="left" valign="top" class="order_infobox_heading"><b><?php echo ORDER_STATUS; ?></b></td>
		   	          </tr>
<?php
  $statuses_query = tep_db_query("select os.orders_status_name, osh.date_added, osh.comments from " . TABLE_ORDERS_STATUS . " os, " . TABLE_ORDERS_STATUS_HISTORY . " osh where osh.orders_id = '" . (int)$HTTP_GET_VARS['order_id'] . "' and osh.orders_status_id = os.orders_status_id and os.language_id = '" . (int)$languages_id . "' order by osh.date_added");
  while ($statuses = tep_db_fetch_array($statuses_query)) {
      echo '		   	          <tr>' . "\n";
      echo '            		       <td valign="top" class="product_infobox_data" width="114"><br>&nbsp;&nbsp;&nbsp;&nbsp;<strong>' . tep_date_short($statuses['date_added']) . '</strong></td>' . "\n";
      echo '            		       <td valign="top" class="product_infobox_data" width="85"><br>' . $statuses['orders_status_name'] . '</td>' . "\n";
      echo '            		       <td valign="top" class="product_infobox_data"><br>' . (empty($statuses['comments']) ? '&nbsp;' : nl2br(tep_output_string_protected($statuses['comments']))) . '</td>' . "\n";
      echo '		   	          </tr>' . "\n";
  }
?>
   	   		       <tr>
            		       <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '7'); ?></td>
		   	          </tr>
						 </table>
						 </td>
						 <td background="images/borders/mainwhite_06.gif"><img src="images/borders/mainwhite_06.gif" width="19" height="21" alt=""></td>
  				     </tr>
  				     <tr>
  				       <td><img src="images/borders/mainwhite_07.gif" width="11" height="18" alt=""></td>
  				       <td background="images/borders/mainwhite_08.gif"><img src="images/borders/mainwhite_08.gif" width="24" height="18" alt=""></td>
  				       <td><img src="images/borders/mainwhite_09.gif" width="19" height="18" alt=""></td>
  				     </tr>
		         </table>
		         </td>
				</tr>
   </table></td>
	</tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '25'); ?></td>
      </tr>
		  <tr>
				<TD><p>Return Policy: Returns must be approved with a RMA# which can be obtained by phoning 423-487-4583 or by email, info@boatequipmentsuperstore.com. When obtaining an RMA#, the appropriate return mailing address will be provided. Returns received without an RMA# will not be refunded.  Items returned damaged, not in sellable condition, or over 15 days from delivery date will be refused. Returns must include original packaging and all accessories. Customer will be refunded the amount of the order minus our cost for shipping (this includes our shipping cost on orders where free shipping is offered to customer on orders over $125), and customer will be responsible for shipping cost of returned item. No returns on carburetors and electrical parts.</p></td>
		  </tr>
		  <tr>
				<TD><?php echo tep_draw_separator('pixel_trans.gif', '100%', '20'); ?></td>
		  </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
             <?php /* begin PayPal_Shopping_Cart_IPN */ ?>
                <td>
                  <div style="float:left"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_HISTORY, tep_get_all_get_params(array('order_id')), 'SSL') . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?></div>
                  <?php require(DIR_WS_MODULES . 'payment/paypal/catalog/order_send_money.inc.php'); ?>
                  <div style="float:right"><a href="javascript:popupPrintReceipt('<?php echo tep_href_link(FILENAME_PRINT_INVOICE, tep_get_all_get_params(array('order_id')) . 'order_id=' . $HTTP_GET_VARS['order_id'], 'SSL'); ?>')"><?php echo tep_image_button('button_print_order.gif', IMAGE_BUTTON_PRINT_ORDER) . '</a>'; ?></div>
                </td>
             <?php /* end PayPal_Shopping_Cart_IPN */ ?>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
</td>
  </tr>
</table><!-- body_text_eof //-->
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
<?php
// END Fancier Order Details & Print Invoice
?>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>