<?php
/*
 $Id: packingslip.php,v 6.1 2005/06/05 00:37:30 PopTheTop Exp $

 osCommerce, Open Source E-Commerce Solutions
 http://www.oscommerce.com

 Copyright (c) 2003 osCommerce

 Released under the GNU General Public License
*/

 require('includes/application_top.php');

 require(DIR_WS_CLASSES . 'currencies.php');
 $currencies = new currencies();

 $oID = tep_db_prepare_input($HTTP_GET_VARS['oID']);
 $orders_query = tep_db_query("select orders_id from " . TABLE_ORDERS . " where orders_id = '" . (int)$oID . "'");

 include(DIR_WS_CLASSES . 'order.php');
 $order = new order($oID);
 $date = date('d M, Y');
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo STORE_NAME; ?> <?php echo TITLE_PACKING; ?> <?php echo $oID; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">

</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">

<!-- body_text //-->

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td style="padding: 10px 0px 0px 10px; vertical-align: top;" align="left"><FONT FACE="Verdana" SIZE="2" COLOR="#006699"><strong><?php echo INVOICE_NUMBER; ?> <?php echo substr($order->info['date_purchased'],2,2).'-'.str_pad($oID,7,'0',STR_PAD_LEFT); ?><br><?php echo date("M j, Y"); ?></strong></font><br><br><span class="pageHeadingSM"><FONT FACE="Verdana" SIZE="1" COLOR="#006699"><strong><?php echo nl2br(STORE_NAME_ADDRESS); ?></strong></font></span></td>
    <td style="padding: 10px 10px 0px 0px; vertical-align: top;" align="right"><?php echo tep_image(INVOICE_IMAGE, INVOICE_IMAGE_ALT_TEXT, INVOICE_IMAGE_WIDTH, INVOICE_IMAGE_HEIGHT); ?></td>
  </tr>
<tr>
   <td colspan="2">
 <table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
   <TD>
   <table width="100%" border="0" cellspacing="0" cellpadding="2">
         <tr>
     <td colspan="4">
     <table width="100%" border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td width="10%"><hr size="2"></td>
            <td align="center" nowrap class="pageHeading"><em><b><?php echo TITLE_PACKING; ?></b></em></td>
            <td width="100%"><hr size="2"></td>
          </tr>
         </table>
         </td>
      </tr>
      <tr>
       <td colspan="4"><?php echo tep_draw_separator('pixel_trans.gif', '100', '5'); ?></td>
      </tr>
        <tr>
             <td width="3"> </td>
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
                       <td align="left" valign="top"><b><?php echo ENTRY_SOLD_TO; ?></b></td>
                     </tr>
                     <tr>
                       <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo tep_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
                     </tr>
                     <tr>
                       <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo tep_address_format($order->customer['format_id'], $order->customer, 1, '', '<br>&nbsp;&nbsp;&nbsp;&nbsp;'); ?></td>
                     </tr>
                     <tr>
                       <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                     </tr>
                     <tr>
                       <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $order->customer['telephone']; ?></td>
                     </tr>
                     <tr>
                       <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $order->customer['email_address']; ?></td>
                     </tr>
                     <tr>
                       <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '7'); ?></td>
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
             <td width="45"> </td>
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
                       <td align="left" valign="top"><b><?php echo ENTRY_SHIP_TO; ?></b></td>
                     </tr>
                     <tr>
                       <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo tep_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
                     </tr>
                     <tr>
                       <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo tep_address_format($order->delivery['format_id'], $order->delivery, 1, '', '<br>&nbsp;&nbsp;&nbsp;&nbsp;'); ?></td>
                     </tr>
                     <tr>
                       <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                     </tr>
                     <tr>
                       <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                     </tr>
                     <tr>
                       <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                     </tr>
                     <tr>
                       <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '7'); ?></td>
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
   <TD COLSPAN="2"><?php echo tep_draw_separator('pixel_trans.gif', '100', '15'); ?></td>
   </tr>
   <tr>
   <TD COLSPAN="2">
           <table width="100%" border="0" cellpadding="0" cellspacing="0">
           <tr>
             <td width="9"> </td>
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
                <td width="33%">&nbsp;<b><?php echo ORDER_NUMBER; ?></b> <?php echo tep_db_input($oID); ?></td>
                <td width="33%">&nbsp;<b><?php echo ORDER_DATE; ?> </b><?php echo tep_date_short($order->info['date_purchased']); ?></td>
                <td>&nbsp;<b><?php echo ENTRY_PAYMENT_METHOD; ?></b>&nbsp;<?php echo $order->info['payment_method']; ?></td>
             </tr>
               <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '7'); ?></td>
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
   <TD COLSPAN="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '15'); ?></td>
   </tr>
   <tr>
      <TD COLSPAN="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
     </tr>
     <tr>
   <TD COLSPAN="2">
   <table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr class="dataTableHeadingRow">
           <td class="dataTableHeadingContent" colspan="1"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
           <td WIDTH="80" class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></td>
        </tr>
<?php
   for ($i = 0, $n = sizeof($order->products); $i < $n; $i++) {
    echo '      <tr class="dataTableRow">' . "\n" .
         '        <td class="dataTableContent" valign="top" align="left">' . $order->products[$i]['qty'] . '&nbsp;x'. $order->products[$i]['name'] ;

     if (isset($order->products[$i]['attributes']) && (sizeof($order->products[$i]['attributes']) > 0)) {
       for ($j=0, $k=sizeof($order->products[$i]['attributes']); $j<$k; $j++) {
         echo '<br><nobr><small>&nbsp;<i> - ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'];
         echo '</i></small></nobr>';
       }
     }

     echo '          </td>' . "\n" .
          '          <td class="dataTableContent" valign="top">' . $order->products[$i]['model'] . '</td>' . "\n" .
          '         </tr>' . "\n";
   }
?>
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
				<TD><p>Return Policy: Returns must be approved with a RMA# which can be obtained by phoning 423-487-4583 or by email, info@boatequipmentsuperstore.com. When obtaining an RMA#, the appropriate return mailing address will be provided. Returns received without an RMA# will not be refunded.  Items returned damaged, not in sellable condition, or over 15 days from delivery date will be refused. Returns must include original packaging and all accessories. Customer will be refunded the amount of the order minus our cost for shipping (this includes our shipping cost on orders where free shipping is offered to customer on orders over $125), and customer will be responsible for shipping cost of returned item. No returns on carburetors and electrical parts.</p></td>
		  </tr>
		  <tr>
				<TD><?php echo tep_draw_separator('pixel_trans.gif', '100%', '20'); ?></td>
		  </tr>
</table>
<!-- body_text_eof //-->
<P>&nbsp;</P>
<P>&nbsp;</P>
<CENTER><FONT FACE="Verdana" SIZE="2" COLOR="#006699"><strong><?php echo STORE_NAME; ?><BR><?php echo STORE_URL_ADDRESS; ?></strong></font></CENTER>
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>