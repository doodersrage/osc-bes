<?php 
  if (tep_not_null($product_info['products_model'])) {
    $products_name = $product_info['products_name'] . '<br><span class="smallText">[' . $product_info['products_model'] . ']</span>';
  } else {
    $products_name = $product_info['products_name'];
  }
	if ($new_price = tep_get_products_special_price($product_info['products_id'])) {
		$products_price = '<s>' . $currencies->display_price($product_info['products_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) . '</s> <span class="productSpecialPrice">' . $currencies->display_price($new_price, tep_get_tax_rate($product_info['products_tax_class_id'])) . '</span>';
	} else {
		$products_price = $currencies->display_price($product_info['products_price'], tep_get_tax_rate($product_info['products_tax_class_id']));
	}
  
	$headerTitle->write($product_info['products_name']);
?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
<?php
if (tep_not_null($product_info['products_image'])) {
?>		<td rowspan="4"><?php echo tep_image(DIR_WS_IMAGES . $product_info['products_image'], $product_info['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"'); ?></td>
<?php
  }
?>
      	<td class="pageHeading" valign="top"><?php echo $products_name; ?></td>
      </tr>
      <tr>
       	<td class="medText"><?php echo $products_price; ?></td>
      </tr>
	  <tr>
		<td>
		<?php
		$products_attributes_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib where patrib.products_id='" . (int)$HTTP_GET_VARS['products_id'] . "' and patrib.options_id = popt.products_options_id and popt.language_id = '" . (int)$languages_id . "'");
		$products_attributes = tep_db_fetch_array($products_attributes_query);
		if ($products_attributes['total'] > 0) {
			?>
			<table border="0" cellspacing="0" cellpadding="2">
				<tr>
					<td class="main" colspan="2"><?php echo TEXT_PRODUCT_OPTIONS; ?></td>
				</tr>
					<?php
					$products_options_name_query = tep_db_query("select distinct popt.products_options_id, popt.products_options_name from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib where patrib.products_id='" . (int)$HTTP_GET_VARS['products_id'] . "' and patrib.options_id = popt.products_options_id and popt.language_id = '" . (int)$languages_id . "' order by popt.products_options_name");
					while ($products_options_name = tep_db_fetch_array($products_options_name_query)) {
						$products_options_array = array();
						$products_options_query = tep_db_query("select pov.products_options_values_id, pov.products_options_values_name, pa.options_values_price, pa.price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov where pa.products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "' and pa.options_id = '" . (int)$products_options_name['products_options_id'] . "' and pa.options_values_id = pov.products_options_values_id and pov.language_id = '" . (int)$languages_id . "'");
						while ($products_options = tep_db_fetch_array($products_options_query)) {
							$products_options_array[] = array('id' => $products_options['products_options_values_id'], 'text' => $products_options['products_options_values_name']);
							if ($products_options['options_values_price'] != '0') {
								$products_options_array[sizeof($products_options_array)-1]['text'] .= ' (' . $products_options['price_prefix'] . $currencies->display_price($products_options['options_values_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) .') ';
							}
						}

						if (isset($cart->contents[$HTTP_GET_VARS['products_id']]['attributes'][$products_options_name['products_options_id']])) {
							$selected_attribute = $cart->contents[$HTTP_GET_VARS['products_id']]['attributes'][$products_options_name['products_options_id']];
						} else {
							$selected_attribute = false;
						}
						?>
				<tr>
					<td class="main"><?php echo $products_options_name['products_options_name'] . ':'; ?></td>
					<td class="main" id="productAttribute"><?php echo tep_draw_pull_down_menu('id[' . $products_options_name['products_options_id'] . ']', $products_options_array, $selected_attribute); ?></td>
				</tr>
					<?php
					}
					?>
			</table>
		<?php
		}
		?>
		</td>
  	  </tr>	
	  <tr>
		<td><?php echo tep_draw_hidden_field('products_id', $product_info['products_id']) . tep_draw_hidden_field('quantity', '1') . tep_mobile_button("Add To Cart"); ?></td>
	  </tr>
	  <tr>
		<td colspan="2"><hr class="separator"></td>
	  </tr>
</table>
