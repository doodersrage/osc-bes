<?php 
/* 
 $Id: allprods.php,v 4.3 2004/01/13 20:28:47 UJP Co. Exp $


 All Products v4.3 MS 2.2 with Images http://www.oscommerce.com/community/contributions,1501

 osCommerce, Open Source E-Commerce Solutions
 http://www.oscommerce.com

 Copyright (c) 2004 osCommerce

 Released under the GNU General Public License
 
*/ 

 require('includes/application_top.php'); 
 include(DIR_WS_LANGUAGES . $language . '/' . FILENAME_ALLPRODS); 

 $breadcrumb->add(HEADING_TITLE, tep_href_link(FILENAME_ALLPRODS, '', 'NONSSL')); 

$firstletter=$HTTP_GET_VARS['fl'];
 if (!$HTTP_GET_VARS['page']){
  $where="and pd.products_name like '$firstletter%'";
 } else {
  $where="and pd.products_name like '$firstletter%'";
 } 


?> 
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN"> 
<html <?php echo HTML_PARAMS; ?>> 
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE ?></title> 
<base href="<?php echo (getenv('HTTPS') == 'on' ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>"> 
<link rel="stylesheet" type="text/css" href="stylesheet.css">
</head> 
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0"> 
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
       <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
         <tr>
           <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
           <td class="pageHeading" align="right"><?php echo tep_image(DIR_WS_IMAGES . 'table_background_products_new.gif', HEADING_TITLE, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
         </tr>
         <tr>
           <td class="main"><?php echo HEADING_SUB_TEXT; ?></td>
         </tr>           
         <tr>
          <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
         </tr>
       </table></td>
     </tr>
     <tr>
       <td align="center" class="smallText"><?php $firstletter_nav=
        '<a href="' . tep_href_link("allprods.php",  'fl=A', 'NONSSL') . '"> A |</A>' . 
        '<a href="' . tep_href_link("allprods.php",  'fl=B', 'NONSSL') . '"> B |</A>' .
        '<a href="' . tep_href_link("allprods.php",  'fl=C', 'NONSSL') . '"> C |</A>' .
        '<a href="' . tep_href_link("allprods.php",  'fl=D', 'NONSSL') . '"> D |</A>' .
        '<a href="' . tep_href_link("allprods.php",  'fl=E', 'NONSSL') . '"> E |</A>' .
        '<a href="' . tep_href_link("allprods.php",  'fl=F', 'NONSSL') . '"> F |</A>' .
        '<a href="' . tep_href_link("allprods.php",  'fl=G', 'NONSSL') . '"> G |</A>' .
        '<a href="' . tep_href_link("allprods.php",  'fl=H', 'NONSSL') . '"> H |</A>' .
        '<a href="' . tep_href_link("allprods.php",  'fl=I', 'NONSSL') . '"> I |</A>' .
        '<a href="' . tep_href_link("allprods.php",  'fl=J', 'NONSSL') . '"> J |</A>' .
        '<a href="' . tep_href_link("allprods.php",  'fl=K', 'NONSSL') . '"> K |</A>' .
        '<a href="' . tep_href_link("allprods.php",  'fl=L', 'NONSSL') . '"> L |</A>' .
        '<a href="' . tep_href_link("allprods.php",  'fl=M', 'NONSSL') . '"> M |</A>' .
        '<a href="' . tep_href_link("allprods.php",  'fl=N', 'NONSSL') . '"> N |</A>' .
        '<a href="' . tep_href_link("allprods.php",  'fl=O', 'NONSSL') . '"> O |</A>' .
        '<a href="' . tep_href_link("allprods.php",  'fl=P', 'NONSSL') . '"> P |</A>' .
        '<a href="' . tep_href_link("allprods.php",  'fl=Q', 'NONSSL') . '"> Q |</A>' .
        '<a href="' . tep_href_link("allprods.php",  'fl=R', 'NONSSL') . '"> R |</A>' .
        '<a href="' . tep_href_link("allprods.php",  'fl=S', 'NONSSL') . '"> S |</A>' .
        '<a href="' . tep_href_link("allprods.php",  'fl=T', 'NONSSL') . '"> T |</A>' .
        '<a href="' . tep_href_link("allprods.php",  'fl=U', 'NONSSL') . '"> U |</A>' .
        '<a href="' . tep_href_link("allprods.php",  'fl=V', 'NONSSL') . '"> V |</A>' .
        '<a href="' . tep_href_link("allprods.php",  'fl=W', 'NONSSL') . '"> W |</A>' .
        '<a href="' . tep_href_link("allprods.php",  'fl=X', 'NONSSL') . '"> X |</A>' .
        '<a href="' . tep_href_link("allprods.php",  'fl=Y', 'NONSSL') . '"> Y |</A>' .
        '<a href="' . tep_href_link("allprods.php",  'fl=Z', 'NONSSL') . '"> Z</A>&nbsp;&nbsp;'   .
        '<a href="' . tep_href_link("allprods.php",  '',     'NONSSL') . '"> FULL</A>';
        
        echo $firstletter_nav; ?></td>
     </tr>
    <tr>
    <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
    </tr>
     <tr>
       <td>
<?php
 // create column list
 $define_list = array('PRODUCT_LIST_MODEL' => PRODUCT_LIST_MODEL,
                      'PRODUCT_LIST_NAME' => PRODUCT_LIST_NAME,
                      'PRODUCT_LIST_INFO' => PRODUCT_LIST_INFO,
                      'PRODUCT_LIST_MANUFACTURER' => PRODUCT_LIST_MANUFACTURER, 
                      'PRODUCT_LIST_PRICE' => PRODUCT_LIST_PRICE,
                      'PRODUCT_LIST_QUANTITY' => PRODUCT_LIST_QUANTITY,
                      'PRODUCT_LIST_WEIGHT' => PRODUCT_LIST_WEIGHT,
                      'PRODUCT_LIST_IMAGE' => PRODUCT_LIST_IMAGE,
                      'PRODUCT_LIST_BUY_NOW' => PRODUCT_LIST_BUY_NOW);
 asort($define_list);

 $column_list = array();
 reset($define_list);
 while (list($column, $value) = each($define_list)) {
   if ($value) $column_list[] = $column; 
 }

 $select_column_list = '';

 for ($col=0, $n=sizeof($column_list); $col<$n; $col++) {
   if ( ($column_list[$col] == 'PRODUCT_LIST_BUY_NOW') || ($column_list[$col] == 'PRODUCT_LIST_NAME') || ($column_list[$col] == 'PRODUCT_LIST_PRICE') ) {
     continue;
   }
 }
 
// $listing_sql = "select distinct p.products_id, p.products_model, pd.products_name, pd.products_info, pd.products_description, p.products_image, p.products_price, p.products_tax_class_id, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, p.products_date_added, m.manufacturers_name from " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_PRODUCTS . " p left join " . TABLE_MANUFACTURERS . " m on p.manufacturers_id = m.manufacturers_id left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on p.products_id = pd.products_id and pd.language_id = '" . $languages_id . "' left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id $where order by pd.products_name";
// update-20051113
$listing_sql = "select distinct p.products_id, p.products_model, pd.products_name, pd.products_info, pd.products_description, p.products_image, p.products_price, p.products_tax_class_id, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, p.products_date_added, m.manufacturers_name from " . TABLE_PRODUCTS . " p left join " . TABLE_MANUFACTURERS . " m on p.manufacturers_id = m.manufacturers_id left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c where c.categories_status = '1' and p.products_status = '1' and p.products_id = p2c.products_id and c.categories_id = p2c.categories_id and pd.products_id = p2c.products_id and pd.language_id = '" . (int)$languages_id . "' $where order by pd.products_name, p.products_model, p.products_id";
if (ALL_PRODUCTS_DISPLAY_MODE == 'true')
 include(DIR_WS_MODULES . 'product_listing.php'); //display in standard format
else
 include(DIR_WS_MODULES . 'allprods.php');
?>
       </td>
     </tr>
   </table></td>
<!-- body_text_eof //-->
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
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>