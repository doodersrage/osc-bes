<?php
/*
$Id: sts_user_code.php,v 1.2 2004/02/05 05:57:21 jhtalk Exp jhtalk $

osCommerce, Open Source E-Commerce Solutions
http://www.oscommerce.com

Copyright (c) 2003 osCommerce

Released under the GNU General Public License
*/

/*

  Simple Template System (STS) - Copyright (c) 2004 Brian Gallagher - brian@diamondsea.com

*/

// PUT USER MODIFIED CODE IN HERE, SUCH AS NEW BOXES, ETC.

// The following code is a sample of how to add new boxes easily.
//  Just uncomment block below and tweak for your needs! 
//  Use as many blocks as you need and just change the block names.

  // $sts_block_name = 'newthingbox';
  // require(STS_START_CAPTURE);
  // require(DIR_WS_BOXES . 'new_thing_box.php');
  // require(STS_STOP_CAPTURE);
  // $template['newthingbox'] = strip_unwanted_tags($sts_block['newthingbox'], 'newthingbox');

    $template['storename'] = defined('STORE_NAME')?STORE_NAME:'';
    $template['cataloglogo'] = '<a href="' . tep_href_link(FILENAME_DEFAULT) . '">' . tep_image(DIR_WS_IMAGES . 'store_logo.gif', STORE_NAME) . '</a>';
    $template['host'] = $HTTP_SERVER_VARS['HTTP_HOST'];
	$template['h1'] = htmlspecialchars($tags_array['h1']);

	if ($category_depth == 'top') $template['sitemap'] = ' | <a href="http://$host/sitemap.html" class="headerNavigation">Site Map</a> | <a href="' . RESOURCES_LINK . '" class="headerNavigation">Resources</a>';
    else $template['sitemap'] = '';

//    $sts_block_name = 'catmenu';
//    require(STS_START_CAPTURE);
//    echo "\n<!-- Start Category Menu -->\n";
//    echo tep_draw_form('goto', tep_href_link(FILENAME_DEFAULT, '', 'NONSSL', false, false), 'get');
//    echo tep_draw_pull_down_menu('cPath', tep_get_category_tree(), $current_category_id, 'onChange="this.form.submit();"');
//    echo "</form>\n";
//    echo "<!-- End Category Menu -->\n";
//    require(STS_STOP_CAPTURE);
//    $template['catmenu'] = $sts_block['catmenu'];
//
//// category css menu
//    $sts_block_name = 'catcssmenu';
//    require(STS_START_CAPTURE);
//    echo "\n<!-- Start Category CSS Menu -->\n";
//	$categories_string = '<div id="menu">' . tep_draw_cat_css_menu() . "</div>\n";
//    $info_box_contents = array();
//    $info_box_contents[] = array('text' => BOX_HEADING_CATEGORIES);
//    new infoBoxHeading($info_box_contents, true, false);
//    $info_box_contents = array();
//    $info_box_contents[] = array('text' => $categories_string);
//    new infoBox($info_box_contents);
//    echo "<!-- End Category CSS Menu -->\n";
//    require(STS_STOP_CAPTURE);
//    $template['catcssmenu'] = $sts_block['catcssmenu'];

// obtain info for affiliate variables
    if (isset($orders_id)) {
      $totals_query = tep_db_query("select value from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . (int)$orders_id . "' and class = 'ot_subtotal'");
      if ($totals = tep_db_fetch_array($totals_query)) {
        $template['orderid'] = $orders_id;
        $template['ordersubtotal'] = number_format($totals['value'],2);
      }
    }

// product_info.php variables
    if ($scriptbasename == 'product_info.php') {
      $manufacturer_query = tep_db_query("select manufacturers_name from " . TABLE_MANUFACTURERS . " where manufacturers_id = '" . (int)$product_info['manufacturers_id'] . "'");
      $manufacturer = tep_db_fetch_array($manufacturer_query);
	  $template['manufacturer'] = $manufacturer['manufacturers_name'];

      $template['displayprice'] = $template['regularprice'];
      if ($template['specialprice']) $template['displayprice'] = '<s>' . $template['displayprice'] . '</s> <span class="productSpecialPrice">' . $template['specialprice'] . '</span>';

      $template['productpopup'] = image_popup(DIR_WS_IMAGES . $product_info['products_image'], $product_info['products_img_alt']?$product_info['products_img_alt']:$product_info['products_name'], PRODUCT_INFO_IMAGE_WIDTH, PRODUCT_INFO_IMAGE_HEIGHT);
      $images_query = tep_db_query("select pd.products_name, i.images_image, id.images_alt from " . TABLE_IMAGES . " i, " . TABLE_IMAGES_DESCRIPTION . " id, " . TABLE_PRODUCTS_DESCRIPTION . " pd where i.images_id = id.images_id and i.group_id = '" . $product_info['products_id'] . "' and i.group_id = pd.products_id and i.group_type = 'p' and id.language_id = '" . (int)$languages_id . "' and id.language_id = pd.language_id order by COALESCE(i.sort_order,10000), i.images_id");
      $popup_count = 0;
      while ($images = tep_db_fetch_array($images_query)) {
        $popup_count++;
        $template['prodpopup' . strval($popup_count)] = image_popup(DIR_WS_IMAGES . $images['images_image'], $images['images_alt']?$images['images_alt']:$images['products_name'], LISTING_IMAGE_WIDTH, LISTING_IMAGE_HEIGHT);
      }
      while($popup_count++ < 10) $template['prodpopup' . strval($popup_count)] = '';

      $sts_block_name = 'related';
      require(STS_START_CAPTURE);
// DANIEL: begin - show related products
// update-20051113
      $related_products = "select distinct op.pop_id, op.pop_products_id_slave, pd.products_name, pd.products_img_alt, p.products_price, p.products_tax_class_id, p.products_image, if(s.status, s.specials_new_products_price, p.products_price) as specials_price from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id, " . TABLE_PRODUCTS_OPTIONS_PRODUCTS . " op, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " pc, " . TABLE_CATEGORIES . " c WHERE op.pop_products_id_slave = pd.products_id and op.pop_cart = '0' and pd.products_id = p.products_id and p.products_id = pc.products_id and pc.categories_id = c.categories_id and c.categories_status = '1' and p.products_status = '1' and pd.language_id = '" . (int)$languages_id . "' and op.pop_products_id_master = '".$HTTP_GET_VARS['products_id']."' order by COALESCE(op.pop_order_id,10000), op.pop_id";
      $related_products_query = tep_db_query($related_products);
      if (mysql_num_rows($related_products_query)>0) {
        $row = 0; $col = 0;
        $info_box_contents = array();
        while ($related_products_value = tep_db_fetch_array($related_products_query)) {
          $products_id_slave = ($related_products_value['pop_products_id_slave']);
          $products_price_slave = $currencies->display_price($related_products_value['products_price'], tep_get_tax_rate($related_products_value['products_tax_class_id']));
          if ($related_products_value['specials_price'] < $related_products_value['products_price']) $products_price_slave = '<s>' . $products_price_slave . '</s>&nbsp;<span class="productSpecialPrice">' . $currencies->display_price($related_products_value['specials_price'], tep_get_tax_rate($related_products_value['products_tax_class_id'])) . '</span>';
          $text = '';
          // show thumb image if Enabled
          if (MODULE_RELATED_PRODUCTS_SHOW_THUMBS!='False') {
            $text .= '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $products_id_slave) . '">' . tep_image(DIR_WS_IMAGES . $related_products_value['products_image'], $related_products_value['products_img_alt']?$related_products_value['products_img_alt']:$related_products_value['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"').'</a>';
          }
          $text .= '<br>&nbsp;<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $products_id_slave) . '">'.$related_products_value['products_name'].'<br>@ '.$products_price_slave.'</a>';
          $info_box_contents[$row][$col] = array('align' => 'center',
                                                 'params' => 'class="newProductBox" width="'.floor(100/MAX_DISPLAY_RELATED_PRODUCTS_PER_ROW).'%" valign="top"',
                                                 'text' => $text);
          $col ++;
          if ($col == MAX_DISPLAY_RELATED_PRODUCTS_PER_ROW) {
            $col = 0;
            $row ++;
          }
        }
        $heading_box_contents = array();
        $heading_box_contents[] = array('text' => TEXT_RELATED_PRODUCTS);
        new contentBoxHeading($heading_box_contents);
        new contentBox($info_box_contents);
      }
//DANIEL: end
      require(STS_STOP_CAPTURE);
      $template['related'] = $sts_block['related'];
    }

/*
// column banners
    column_banners();

// Header Tabs
    $xtabs = array(array(FILENAME_ALLPRODS,'','All Products'),
                  array(FILENAME_ACCOUNT,'',HEADER_TITLE_MY_ACCOUNT),
                  array(FILENAME_SHOPPING_CART,'',HEADER_TITLE_CART_CONTENTS),
                  array(FILENAME_WISHLIST,'',HEADER_TITLE_WISHLIST_CONTENTS),
                  array(FILENAME_ADVANCED_SEARCH,'','Search'),
                  array(FILENAME_PAGES,'pages_id=10','About Us'),
                  array(FILENAME_CONTACT_US,'','Contact Us'),
                  array(FILENAME_PAGES,'pages_id=3','Shipping'));
    $template['headerTabs'] = '<table class="BODY" border="0" cellspacing="0" cellpadding="0"><tr>';
    foreach($xtabs as $xtab) {
      if (basename($PHP_SELF) == $xtab[0]) {
        $tabclass = 'ontab';
        if (tep_not_null($xtab[1])) {
          $chkparams = split('&',$xtab[1]);
          foreach($chkparams as $chkparam) {
            $param = split('=',$chkparam);
            if (count($param)!=2 || !isset($HTTP_GET_VARS[$param[0]]) || $HTTP_GET_VARS[$param[0]] != $param[1]) {
              $tabclass = 'tab';
              break;
            }
          }
        }
      } else $tabclass = 'tab';
      $template['headerTabs'] .= '<td valign="middle" class="tableft"><img src="images/tabs/tab_left.gif" border="0" alt=""></td><td valign="middle" style="background-image:url(images/tabs/tab_middle.gif)"><a href="'.tep_href_link($xtab[0],$xtab[1]).'" class="'.$tabclass.'">'.$xtab[2].'</a></td><td valign="middle" class="tabright"><img src="images/tabs/tab_right.gif" border="0" alt=""></td>';
    }
    $template['headerTabs'] .= '</tr></table>';
*/

  function tep_get_category_tree($parent_id = '0', $spacing = '', $exclude = '', $category_tree_array = '', $include_itself = false) {
    global $languages_id;

    if (!is_array($category_tree_array)) $category_tree_array = array();
    if ( (sizeof($category_tree_array) < 1) && ($exclude != '0') ) $category_tree_array[] = array('id' => '0', 'text' => "Catalog");

    if ($include_itself) {
      $category_query = tep_db_query("select cd.categories_name from " . TABLE_CATEGORIES_DESCRIPTION . " cd where cd.language_id = '" . (int)$languages_id . "' and cd.categories_id = '" . (int)$parent_id . "'");
      $category = tep_db_fetch_array($category_query);
      $category_tree_array[] = array('id' => $parent_id, 'text' => $category['categories_name']);
    }

    $categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_status = '1' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' and c.parent_id = '" . (int)$parent_id . "' order by COALESCE(c.sort_order,1000), cd.categories_name");
    while ($categories = tep_db_fetch_array($categories_query)) {
      if ($exclude != $categories['categories_id']) $category_tree_array[] = array('id' => $categories['categories_id'], 'text' => $spacing . $categories['categories_name']);
      $category_tree_array = tep_get_category_tree($categories['categories_id'], $spacing . '&nbsp;&nbsp;&nbsp;', $exclude, $category_tree_array);
    }

    return $category_tree_array;
  }

  function tep_draw_cat_css_menu($parent_id = '0', $spacing = '', $cPath = '') {
    global $languages_id;

    $out = '';
    $categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_status = '1' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' and c.parent_id = '" . (int)$parent_id . "' order by COALESCE(c.sort_order,1000), cd.categories_name");
    while ($categories = tep_db_fetch_array($categories_query)) {
      $out .= $spacing . '  <li>' . tep_image(DIR_WS_IMAGES . 'infobox/menuarrow.gif', '', 9, 16, 'align="left"') . '<a href="' . tep_href_link(FILENAME_DEFAULT, 'cPath=' . $cPath . $categories['categories_id']) . '">' . $categories['categories_name'] . "</a>";
      if ($mout = tep_draw_cat_css_menu($categories['categories_id'], $spacing . '    ', $cPath . $categories['categories_id'] . '_')) $out .= "$mout$spacing  ";
      $out .= "</li>\n";
    }
    if ($out) $out = "\n$spacing<ul>\n$out$spacing</ul>\n";
    return $out;
  }

  function image_popup($image, $alt, $width, $height) {
    if (tep_not_null($image) && $imagedata = @getimagesize($image)) {
	  
      $ret .= '<a href="' . tep_href_link($image) . '" target="_blank" class="popimg" ><table cellspacing="0" cellpadding="0"><tr><td>' . tep_image($image, $alt, $width, $height, 'hspace="5" vspace="5"') . '</td></tr><tr><td align="center">' . tep_image_button('image_enlarge.gif', TEXT_CLICK_TO_ENLARGE) . '</td></tr></table></a>';
      return $ret;
    } else return '';
  }

// column banners
  function column_banners($banners_group='box',$banners_count=1) {
    global $template;
    $sts_block_name = $banners_group.'_bannersbox';
    $template[$banners_group.'_bannersbox'] = '';
    require(STS_START_CAPTURE);
   	require(DIR_WS_BOXES . 'column_banner.php');
    require(STS_STOP_CAPTURE);
    $template[$banners_group.'_bannersbox'] = strip_unwanted_tags($sts_block[$banners_group.'_bannersbox'], $banners_group.'_bannersbox');
  }
  
  if ( $_SERVER["SERVER_PORT"] == '443' ) { 
$template['analytics'] = '<script src="https://ssl.google-analytics.com/urchin.js" type="text/javascript">
</script>
<script type="text/javascript">
_uacct = "UA-2146623-1";
urchinTracker();
</script>';
} else {
$template['analytics'] = '<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
</script>
<script type="text/javascript">
_uacct = "UA-2146623-1";
urchinTracker();
</script>';

}
  
  $template['dhtmlmenufooter'] = $GLOBALS['dmfooter'];
?>