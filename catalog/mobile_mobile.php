<?php
require_once('mobile/includes/application_top.php');

// calculate category path
$redirect = false;
if (isset($cPath) && tep_not_null($cPath)) {
	$product_module_name = 'products';
	// use product_listing for "traditional" OSCommerce look
	// $product_module_name = 'products_listing';
	$categories_products_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_TO_CATEGORIES . " where categories_id = '" . (int)$current_category_id . "'");
	$cateqories_products = tep_db_fetch_array($categories_products_query);
	if ($cateqories_products['total'] > 0) {
		$redirect = true; // display products
	} else {
		$category_parent_query = tep_db_query("select count(*) as total from " . TABLE_CATEGORIES . " where parent_id = '" . (int)$current_category_id . "'");
		$category_parent = tep_db_fetch_array($category_parent_query);
		if ($category_parent['total'] == 0) {
			$redirect = true; // display products
		}
	}
}

if($redirect)
	tep_redirect(tep_mobile_link(FILENAME_PRODUCTS, tep_get_all_get_params()));

$list = array();
$parent_id = (tep_not_null($cPath) == true) ? (int)$cPath : 0;
$categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.parent_id = " . $parent_id . " and c.categories_id = cd.categories_id and cd.language_id='" . (int)$languages_id . $path_cond . "' order by sort_order, cd.categories_name");
while ($categories = tep_db_fetch_array($categories_query))  {
	$list[] = $categories;
}

require(DIR_MOBILE_INCLUDES . 'header.php');
require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_DEFAULT);

?>
<!-- categories //-->
<table  class="categories" width="100%" cellpadding="0" cellspacing="0">
<?php
	foreach ($list as $item ) {
		$path = tep_mobile_link(FILENAME_MOBILE, 'cPath=' . $item['categories_id']);
		$img = strlen($item['categories_image']) > 0 ? tep_image(DIR_WS_IMAGES . $item['categories_image'], $item['categories_name'], 30,30) : ' ';
		print tep_mobile_selection($path, array($img, $item['categories_name']));
		
	}
?>
</table>
<!-- categories_eof //-->
<?php require(DIR_MOBILE_INCLUDES . 'footer.php'); ?>
