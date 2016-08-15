<?php
require_once('mobile/includes/application_top.php');

    $listing_sql = "select 	p.products_id,  
    						pd.products_name, 
    						p.manufacturers_id, 
    						p.products_price, 
    						p.products_image, 
    						p.products_tax_class_id, 
    						IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, 
    						IF(s.status, s.specials_new_products_price, p.products_price) as final_price from " . 
    						TABLE_PRODUCTS_DESCRIPTION . " pd," .
    						TABLE_PRODUCTS . " p left join " . 
    						TABLE_MANUFACTURERS . " m on p.manufacturers_id = m.manufacturers_id left join " . 
    						TABLE_SPECIALS . " s on p.products_id = s.products_id, " . 
    						TABLE_PRODUCTS_TO_CATEGORIES . " p2c 
    						where p.products_status = '1' 
    						and p.products_id = p2c.products_id 
    						and pd.products_id = p2c.products_id 
    						and pd.language_id = '" . (int)$languages_id . "'";
 
    if (isset($HTTP_GET_VARS['cPath']))
        $listing_sql .= " and p2c.categories_id = '" . (int)$current_category_id . "'";
    if (isset($HTTP_GET_VARS['manufacturers_id'])) 
        $listing_sql .= " and m.manufacturers_id = '" . (int)$HTTP_GET_VARS['manufacturers_id'] . "'";
        
    $listing_sql .= " order by pd.products_name";
    $PCSITE = DIR_MAIN_HTTP_CATALOG . FILENAME_DEFAULT . '?' . tep_get_all_get_params();
    require(DIR_MOBILE_INCLUDES . 'header.php');
	require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_DEFAULT);
	
	$headerTitle->write($breadcrumb->_trail[sizeof($breadcrumb->_trail)-1]['title']);

	require(DIR_MOBILE_MODULES . 'products.php');
	require(DIR_MOBILE_INCLUDES . 'footer.php'); 
?>
