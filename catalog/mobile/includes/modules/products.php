<!-- products //-->
<table id="products" width="100%">
	<tr>
		<td>
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td>
<?php
  require(DIR_MOBILE_CLASSES . 'split_page_results_ajax.php');
  $listing_split = new splitPageResultsAjax($listing_sql, 12, 'p.products_id');
  $listing_split->generateJS();
?>
		<table id="productsGrid" class="productsGrid"  width="100%">
<!--  ajax_results_begining -->
			<tr>
<?php
	$num_of_columns = 3;
	$row = 0;
  	$col = 0;
    $listing_query = tep_db_query($listing_split->sql_query);
    while ($listing = tep_db_fetch_array($listing_query)) {
		if ($col >= $num_of_columns) {
			$col = 0;
			echo "</tr><tr>";
		} 
		
    	$path = '<a href="' . tep_mobile_link(FILENAME_PRODUCT_INFO, ($cPath ? 'cPath=' . $cPath . '&' : '') . 'products_id=' . $listing['products_id']) . '">';
    	$img = tep_image(DIR_WS_IMAGES . $listing['products_image'], $listing['products_name'], MOBILE_IMAGE_WIDTH, MOBILE_IMAGE_HEIGHT);

    	if (tep_not_null($listing['specials_new_products_price'])) {
	        $price = '<span class="productSpecialPrice">' . $currencies->display_price($listing['specials_new_products_price'], tep_get_tax_rate($listing['products_tax_class_id'])) . '</span>';
        } else {
            $price = $currencies->display_price($listing['products_price'], tep_get_tax_rate($listing['products_tax_class_id']));
        }
        $buy_button = '<a href="' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=buy_now&products_id=' . $listing['products_id']) . '">' . tep_mobile_button(IMAGE_BUTTON_BUY_NOW) . '</a>&nbsp;';
        
?>			
				<td class="productsGrid" align="center" valign="top" width="33%">
					<table class='productCell'>
					<tr><td colspan="2"><?php echo $path . $img . '</a>'; ?></td></tr>
					<tr><td colspan="2"><?php echo $path . $listing['products_name'] . '</a>'; ?></td></tr>
					<tr><td><?php echo $price; ?></td></tr>
<!-- 				<tr><td><?php echo $buy_button; ?></td></tr> -->
					</table>
				</td>
<?php	
		$col++;
      } 
	  while ($col % $num_of_columns != 0) {
			$col++;
			echo '<td class="productsGrid">&nbsp;</td>';
	  } 
      
?>
			</tr>
<!--  ajax_results_ending -->
		</table>
<?php
  if ($listing_split->number_of_rows > 0 ) {
?>
<table border="0" width="100%" cellspacing="0" cellpadding="0" id="splitPageResultsAjaxTable">
  <tr  class="categories">
<?php  if(AJAX_ENABLED) {?>
<script language="javascript"><!--
document.write('<?php echo $listing_split->display_ajax_msg(); ?>');
--></script>
<noscript>
    <td class="smallText"><?php echo $listing_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
    <td class="smallText" align="right"><?php echo TEXT_RESULT_PAGE . ' ' . $listing_split->display_links(MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
</noscript>
<?php  } else { ?>
    <td class="smallText"><?php echo $listing_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
    <td class="smallText" align="right"><?php echo TEXT_RESULT_PAGE . ' ' . $listing_split->display_links(MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
<?php  }?>
  </tr>
</table>
<?php
  }
?>
        </td>
      </tr>
    </table>
<!-- products_eof //-->
