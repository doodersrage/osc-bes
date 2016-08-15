<?php
require_once('mobile/includes/application_top.php');
require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PRODUCT_INFO);
require(DIR_MOBILE_INCLUDES . 'header.php');

$product_check_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "' and pd.products_id = p.products_id and pd.language_id = '" . (int)$languages_id . "'");
$product_check = tep_db_fetch_array($product_check_query);

echo tep_draw_form('cart_quantity', tep_mobile_link(FILENAME_PRODUCT_INFO, tep_get_all_get_params(array('action')) . 'action=add_product')); ?>
		<?php
if ($product_check['total'] < 1) {
			?>
		<table border="0" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td><?php new infoBox(array(array('text' => TEXT_PRODUCT_NOT_FOUND))); ?></td>
			</tr>
			<tr>
				<td><?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT) . '">' . tep_mobile_button(IMAGE_BUTTON_CONTINUE) . '</a>'; ?></td>
			</tr>
		</table>
<?php
} else {
	$product_info_query = tep_db_query("select p.products_id, pd.products_name, pd.products_description, p.products_model, p.products_quantity, p.products_image, pd.products_url, p.products_price, p.products_tax_class_id, p.products_date_added, p.products_date_available, p.manufacturers_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "' and pd.products_id = p.products_id and pd.language_id = '" . (int)$languages_id . "'");
	$product_info = tep_db_fetch_array($product_info_query);
  	require(DIR_MOBILE_INCLUDES. "product_header.php");
	
	tep_db_query("update " . TABLE_PRODUCTS_DESCRIPTION . " set products_viewed = products_viewed+1 where products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "' and language_id = '" . (int)$languages_id . "'");
?>
	<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td class="main">
				<p><?php echo stripslashes($product_info['products_description']); ?></p>
				</td>
			</tr>
			<tr>
				<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '2'); ?></td>
			</tr>
			<?php
			$reviews_query = tep_db_query("select count(*) as count from " . TABLE_REVIEWS . " where products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "'");
			$reviews = tep_db_fetch_array($reviews_query);
			if ($reviews['count'] > 0) {
				?>
			<tr>
				<td class="main"><?php echo TEXT_CURRENT_REVIEWS . ' ' . $reviews['count']; ?></td>
			</tr>
			<tr>
				<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '2'); ?></td>
			</tr>
			<?php
}

if (tep_not_null($product_info['products_url'])) {
	?>
			<tr>
				<td class="main"><?php echo sprintf(TEXT_MORE_INFORMATION, tep_href_link(FILENAME_REDIRECT, 'action=url&goto=' . urlencode($product_info['products_url']), 'NONSSL', true, false)); ?></td>
			</tr>
			<tr>
				<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '2'); ?></td>
			</tr>
			<?php
}

if ($product_info['products_date_available'] > date('Y-m-d H:i:s')) {
	?>
			<tr>
				<td align="center" class="smallText"><?php echo sprintf(TEXT_DATE_AVAILABLE, tep_date_long($product_info['products_date_available'])); ?></td>
			</tr>
			<?php
} else {
	?>
			<tr>
				<td align="center" class="smallText"><?php echo sprintf(TEXT_DATE_ADDED, tep_date_long($product_info['products_date_added'])); ?></td>
			</tr>
			<?php
}
?>
			<tr>
				<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '2'); ?></td>
			</tr>
			<tr>
				<td>
<?php
				$path = tep_href_link(FILENAME_PRODUCT_REVIEWS, tep_get_all_get_params());
?>
				<table border="0" width="100%" cellspacing="1" cellpadding="2" class="categories">
					<?php echo tep_mobile_selection(tep_mobile_link(FILENAME_PRODUCT_REVIEWS, tep_get_all_get_params(array('module'))), array(BOX_HEADING_REVIEWS)); ?>
				</table>
				</td>
			</tr>
			<tr>
				<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '2'); ?></td>
			</tr>
			<tr>
				<td><?php
				if ((USE_CACHE == 'true') && empty($SID)) {
					echo tep_cache_also_purchased(3600);
				} else {
					include(DIR_MOBILE_MODULES . FILENAME_ALSO_PURCHASED_PRODUCTS);
				}
}
?></td>
		</tr>
	</table>
</form>
<?php require(DIR_MOBILE_INCLUDES . 'footer.php'); ?>
