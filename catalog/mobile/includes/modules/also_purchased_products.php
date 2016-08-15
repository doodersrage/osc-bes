<?php
  if (isset($HTTP_GET_VARS['products_id'])) {
    $orders_query = tep_db_query("select p.products_id, p.products_image from " . TABLE_ORDERS_PRODUCTS . " opa, " . TABLE_ORDERS_PRODUCTS . " opb, " . TABLE_ORDERS . " o, " . TABLE_PRODUCTS . " p where opa.products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "' and opa.orders_id = opb.orders_id and opb.products_id != '" . (int)$HTTP_GET_VARS['products_id'] . "' and opb.products_id = p.products_id and opb.orders_id = o.orders_id and p.products_status = '1' group by p.products_id order by o.date_purchased desc limit " . MAX_DISPLAY_ALSO_PURCHASED);
    $num_products_ordered = tep_db_num_rows($orders_query);

    if ($num_products_ordered >= MIN_DISPLAY_ALSO_PURCHASED) {
?>
<!-- also_purchased_products //-->
<table border="0" cellpadding="0" cellspacing="2">
	<tr><td class="pageHeading"><?php echo TEXT_ALSO_PURCHASED_PRODUCTS; ?></td></tr>
</table>
<table id="productsGrid" class="productsGrid"  width="100%">
	<tr>

<?php
      $col = 0;
      $info_box_contents = array();
      while ($orders = tep_db_fetch_array($orders_query)) {
      	if($col > 2) break;
      	
        $orders['products_name'] = tep_get_products_name($orders['products_id']);
      	$col ++;
        $path = '<a href="' . tep_mobile_link(FILENAME_PRODUCT_INFO, 'products_id=' . $orders['products_id']) . '">';
        $img = tep_image(DIR_WS_IMAGES . $orders['products_image'], $orders['products_name'], MOBILE_IMAGE_WIDTH, MOBILE_IMAGE_HEIGHT);
?>
				<td class="productsGrid" align="center" valign="top" width="33%">
					<table class='productCell'>
					<tr><td><?php echo $path . $img . '</a>'; ?></td></tr>
					<tr><td><?php echo $path . $orders['products_name'] . '</a>'; ?></td></tr>
<!-- 				<tr><td><?php echo $price; ?></td></tr>
					<tr><td valign="bottom"><?php echo $buy_button; ?></td></tr> -->					
					</table>
				</td>

<?php       
      }

?></tr>
</table>
<!-- also_purchased_products_eof //-->
<?php
    }
  }
?>
