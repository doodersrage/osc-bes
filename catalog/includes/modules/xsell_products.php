
<?php
/* 
$Id: xsell_products.php, v1  2002/09/11

osCommerce, Open Source E-Commerce Solutions 
<http://www.oscommerce.com> 

Copyright (c) 2002 osCommerce 

Released under the GNU General Public License 
*/ 

if ($HTTP_GET_VARS['products_id']) { 
// ################" Added Enable / Disable Catgories ###################
// $xsell_query = tep_db_query("select distinct p.products_id, p.products_image, pd.products_name from " . TABLE_PRODUCTS_XSELL . " xp, " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where xp.products_id = '" . $HTTP_GET_VARS['products_id'] . "' and xp.xsell_id = p.products_id and p.products_id = pd.products_id and pd.language_id = '" . $languages_id . "' and p.products_status = '1' order by xp.products_id asc limit " . MAX_DISPLAY_ALSO_PURCHASED); 
$xsell_query = tep_db_query("select distinct p.products_id, p.products_image, pd.products_img_alt, pd.products_name from " . TABLE_PRODUCTS_XSELL . " xp, " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_CATEGORIES . " c, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where c.categories_status='1' and p.products_id = p2c.products_id and p2c.categories_id = c.categories_id and xp.products_id = '" . $HTTP_GET_VARS['products_id'] . "' and xp.xsell_id = p.products_id and p.products_id = pd.products_id and pd.language_id = '" . $languages_id . "' and p.products_status = '1' order by xp.products_id asc limit " . MAX_DISPLAY_ALSO_PURCHASED); 
// ################" End Added Enable / Disable Catgories ###################


$num_products_xsell = tep_db_num_rows($xsell_query); 
if ($num_products_xsell >= MIN_DISPLAY_ALSO_PURCHASED) { 
?> 
<!-- xsell_products //-->
<?php
      $info_box_contents = array();
      $info_box_contents[] = array('align' => 'left', 'text' => TEXT_XSELL_PRODUCTS);
      new contentBoxHeading($info_box_contents);

      $row = 0;
      $col = 0;
      $info_box_contents = array();
      while ($xsell = tep_db_fetch_array($xsell_query)) {
        $xsell['products_name'] = tep_get_products_name($xsell['products_id']);
        $info_box_contents[$row][$col] = array('align' => 'center',
                                               'params' => 'class="smallText" width="33%" valign="top"',
                                               'text' => '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $xsell['products_id']) . '">' . tep_image(DIR_WS_IMAGES . $xsell['products_image'], $xsell['products_img_alt']?$xsell['products_img_alt']:$xsell['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a><br><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $xsell['products_id']) . '">' . $xsell['products_name'] . '</a>');
        $col ++;
        if ($col > 2) {
          $col = 0;
          $row ++;
        }
      }
      new contentBox($info_box_contents);
?>
<!-- xsell_products_eof //-->




<?php
    }
  }
?>
