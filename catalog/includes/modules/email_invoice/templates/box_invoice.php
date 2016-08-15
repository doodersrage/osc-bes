<?php
/*
  $Id: box_invoice.php,v 5.5 2005/05/15 00:37:30 PopTheTop Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  class objectInfo {

// class constructor
    function objectInfo($object_array) {
      reset($object_array);
      while (list($key, $value) = each($object_array)) {
        $this->$key = tep_db_prepare_input($value);
      }
    }
  }

?>
<html>
<head>
<title><?php echo STORE_NAME; ?> <?php echo INVOICE_TEXT_INVOICE; ?> <?php echo INVOICE_TEXT_NUMBER_SIGN; ?> <?php echo substr($order->info['date_purchased'],2,2).INVOICE_TEXT_DASH.str_pad($oID,7,'0',STR_PAD_LEFT); ?></title>
<style type="text/css">
.dataTableHeadingRow { background-color: #C9C9C9; }
.dataTableHeadingContent { font-family: Verdana, Arial, sans-serif; font-size: 10px; color: #ffffff; font-weight: bold; }
.dataTableRow { background-color: #F0F1F1; }
.dataTableContent { font-family: Verdana, Arial, sans-serif; font-size: 10px; color: #000000; }
.smallText { font-family: Verdana, Arial, sans-serif; font-size: 10px; }
.smallTextBlue { font-family: Verdana, Arial, sans-serif; font-size: 10px; color: #006699; }
.smallAddressBlue { font-family: Arial; font-size: 9px; color: #006699; }
.main { font-family: Verdana, Arial, sans-serif; font-size: 12px; }
</style>
</head>
<body bgcolor="#FFFFFF" leftmargin=10 topmargin=10>
<!-- START Top Header -->
<table width=100% bgcolor="#FFFFFF" cellpadding="5" style="border-collapse: collapse" bordercolor="#EEEEEE" cellspacing="0" border="3">
	<tr>
		<td width="100%">
			<table width="100%">
				<tr>
					<td style="padding: 5px 0px 0px 5px; vertical-align: top;" align="left" class="smallTextBlue" NOWRAP><strong><?php echo INVOICE_TEXT_INVOICE; ?> <?php echo INVOICE_TEXT_NUMBER_SIGN; ?> <?php echo substr($order->info['date_purchased'],2,2).INVOICE_TEXT_DASH.str_pad($oID,7,'0',STR_PAD_LEFT); ?><BR><?php echo $date; ?></strong></font><br><br><span class="smallAddressBlue"><strong><?php echo nl2br(STORE_NAME_ADDRESS); ?></strong></span></td>
					<td style="padding: 5px 0px 0px 0px; vertical-align: top;" align="right"><?php echo tep_image(INVOICE_IMAGE, INVOICE_IMAGE_ALT_TEXT, INVOICE_IMAGE_WIDTH, INVOICE_IMAGE_HEIGHT, 'hspace="5"'); ?></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<!-- END Top Header -->

<!-- START INVOICE -->
<table width=100% bgcolor="#C9C9C9" cellpadding="2">
  <tr class="dataTableHeadingRow">
    <td>&nbsp;<font face="Arial Black" size="3" color="#FFFFFF"><i>INVOICE</i></font></td>
  </tr>
</table>
<!-- END INVOICE -->

<table width="100%" border="3" cellpadding="5" bordercolor="#EEEEEE" bgcolor="#FFFFFF" style="border-collapse: collapse">
  <tr>
    <td width="50%" valign="top">
<!-- START Billing Info -->
							 	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="main">
            		        <tr>
                  		    <td class="smallTextBlue" align="left" valign="top"><b><?php echo ENTRY_SOLD_TO; ?></b></font></td>
		                    </tr>
      		              <tr>
            		          <td><img src="<?php echo $ei_image_dir; ?>pixel_trans.gif" width="100" height="5" alt=""></td>
                  		  </tr>
		                    <tr>
      		                <td class="smallTextBlue" NOWRAP>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo tep_address_format($order->customer['format_id'], $order->customer, 1, '', '<br>&nbsp;&nbsp;&nbsp;&nbsp;'); ?></font></td>
            		        </tr>
                  		  <tr>
		                      <td>&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo $ei_image_dir; ?>pixel_trans.gif" width="1" height="10" alt=""></td>
      		              </tr>
            		        <tr>
                  		    <td class="smallTextBlue" NOWRAP>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $order->customer['telephone']; ?></font></td>
		                    </tr>
      		              <tr>
            		          <td class="smallTextBlue" NOWRAP>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $order->customer['email_address']; ?></font></td>
		                    </tr>
                  		  <tr>
		                      <td><img src="<?php echo $ei_image_dir; ?>pixel_trans.gif" width="1" height="7" alt=""></td>
      		              </tr>
		                  </table>
<!-- END Billing Info -->
    </td>
    <td width="50%" valign="top">
<!-- START Shipping Info -->
								 <table width="100%" border="0" cellpadding="0" cellspacing="0" class="main">
   	   		             <tr>
      	      		         <td class="smallTextBlue" align="left" valign="top"><b><?php echo ENTRY_SHIP_TO; ?></b></font></td>
		   	                </tr>
      		              <tr>
            		          <td><img src="<?php echo $ei_image_dir; ?>pixel_trans.gif" width="100" height="5" alt=""></td>
                  		  </tr>
		               	    <tr>
	      		               <td class="smallTextBlue" NOWRAP>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo tep_address_format($order->delivery['format_id'], $order->delivery, 1, '', '<br>&nbsp;&nbsp;&nbsp;&nbsp;'); ?></font></td>
   	         		       </tr>
                  		  <tr>
		                      <td>&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo $ei_image_dir; ?>pixel_trans.gif" width="1" height="10" alt=""></td>
      		              </tr>
            		        <tr>
                  		    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
		                    </tr>
      		              <tr>
            		          <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
		                    </tr>
                  		  <tr>
		                      <td><img src="<?php echo $ei_image_dir; ?>pixel_trans.gif" width="1" height="7" alt=""></td>
      		              </tr>
								 </table>
<!-- END Shipping Info -->
		</td>
	</tr>
</table>

<!-- START Product Info -->
<table border="0" width="100%" cellspacing="0" cellpadding="2">
	<tr class="dataTableHeadingRow">
		<td class="dataTableHeadingContent" colspan="2">&nbsp;<font color="#FFFFFF"><?php echo TABLE_HEADING_PRODUCTS; ?></font></td>
		<td WIDTH="80" class="dataTableHeadingContent"><font color="#FFFFFF"><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></font></td>
		<td WIDTH="80" class="dataTableHeadingContent" align="right"><font color="#FFFFFF"><?php echo TABLE_HEADING_UNIT_PRICE; ?></font></td>
		<TD WIDTH="80" ALIGN="right" CLASS="dataTableHeadingContent"><font color="#FFFFFF"><?php echo TABLE_HEADING_TOTAL; ?></font>&nbsp;</TD>
	</tr>
<?php
    for ($i = 0, $n = sizeof($order->products); $i < $n; $i++) {
		echo '	<tr class="dataTableRow">' . "\n" .
		     '		<td class="dataTableContent" valign="top" align="right">' . $order->products[$i]['qty'] . '&nbsp;x</td>' . "\n" .
		     '		<td class="dataTableContent" valign="top" NOWRAP>' . $order->products[$i]['name'];

      if (isset($order->products[$i]['attributes']) && (($k = sizeof($order->products[$i]['attributes'])) > 0)) {
        for ($j = 0; $j < $k; $j++) {
          echo '<br><nobr><small>&nbsp;<i> - ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'];
          if ($order->products[$i]['attributes'][$j]['price'] != '0') echo ' (' . $order->products[$i]['attributes'][$j]['prefix'] . $currencies->format($order->products[$i]['attributes'][$j]['price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . ')';
          echo '</i></small></nobr>';
        }
      }

      echo '		</td>' . "\n" .
           '		<td WIDTH="80" class="dataTableContent" valign="top">' . $order->products[$i]['model'] . '</td>' . "\n";
      echo '		<td WIDTH="80" class="dataTableContent" align="right" valign="top">' . $currencies->format($order->products[$i]['final_price'], true, $order->info['currency'], $order->info['currency_value']) . '</td>' . "\n" .
           '		<td WIDTH="80" class="dataTableContent" align="right" valign="top"><b>' . $currencies->format($order->products[$i]['final_price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . '&nbsp;</b></td>' . "\n";
      echo '	</tr>' . "\n";
    }
?>
	<tr>
		<td align="right" colspan="5">
		<table border="0" cellspacing="0" cellpadding="2">
<?php
  for ($i = 0, $n = sizeof($order->totals); $i < $n; $i++) {
    echo '			<tr>' . "\n" .
         '				<td align="right" class="smallText">' . $order->totals[$i]['title'] . '</td>' . "\n" .
         '				<td align="right" class="smallText">' . $order->totals[$i]['text'] . '</td>' . "\n" .
         '			</tr>' . "\n";
  }
?>
		</table>
		</td>
	</tr>
</table>
<!-- END Product Info -->
<!-- START Customer Thank You and Order Link -->
<table width="100%" border="3" cellpadding="5" bordercolor="#EEEEEE" bgcolor="#FFFFFF" style="border-collapse: collapse">
	<tr>
		<td colspan="2" align="center"><br><font size=2 face="Arial" color="#006699">Thank you for purchasing from <?php echo STORE_NAME; ?><br><?php echo STORE_URL_ADDRESS; ?><br>Please print this invoice for your records</font><br><br></td>
	</tr>
<!-- END Customer Thank You and Order Link -->
</table>

<!-- START Footer -->
<table width=100% bgcolor="#C9C9C9" cellpadding="2">
  <tr class="dataTableHeadingRow">
    <td class="dataTableHeadingContent">&nbsp;<STRONG><FONT COLOR="#ffffff">Copyright © 20<?php echo date("y"); ?> <?php echo STORE_NAME; ?>, All Rights Reserved</FONT></STRONG></td>
  </tr>
</table>
<!-- END Footer -->
</body>
</html>
