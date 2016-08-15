<?php
/*
  $Id: product_listing.php,v 1.44 2003/06/09 22:49:59 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  if (defined('MODULE_HEADING_PRODUCT_LISTING')) {
    $info_box_contents = array();
    $info_box_contents[] = array('text' => MODULE_HEADING_PRODUCT_LISTING);
    new infoBoxHeading($info_box_contents, true, true);
  }

if (isset($pw_mispell)){ //added for search enhancements mod
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
<tr><td><?php echo $pw_string; ?></td></tr>
</table>
<?php
 } //end added search enhancements mod
  $listing_split = new splitPageResults($listing_sql, MAX_DISPLAY_SEARCH_RESULTS, 'p.products_id');
// fix counted products

  if ($listing_split->number_of_rows > 0) {
    if ((PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3')) {
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td class="smallText"><?php echo $listing_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
    <td class="smallText" align="right"><?php echo TEXT_RESULT_PAGE . ' ' . $listing_split->display_links(MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
  </tr>
</table>
<?php
    }

    $list_box_contents = array();

    for ($col=0, $n=sizeof($column_list); $col<$n; $col++) {
 // added by splautz for subtitle under product name
      if ($column_list[$col] == 'PRODUCT_LIST_'.($subtitle_name=strtoupper(PRODUCT_LIST_NAME_SUBTITLE))) continue;

      switch ($column_list[$col]) {
        case 'PRODUCT_LIST_MODEL':
          $lc_text = TABLE_HEADING_MODEL;
          $lc_align = '';
          break;
        case 'PRODUCT_LIST_NAME':
          $lc_text = TABLE_HEADING_PRODUCTS;
          $lc_align = '';
          break;
        case 'PRODUCT_LIST_INFO':
          $lc_text = TABLE_HEADING_INFO;
          $lc_align = '';
          break;
        case 'PRODUCT_LIST_MANUFACTURER':
          $lc_text = TABLE_HEADING_MANUFACTURER;
          $lc_align = '';
          break;
        case 'PRODUCT_LIST_PRICE':
          $lc_text = TABLE_HEADING_PRICE;
          $lc_align = 'right';
          break;
        case 'PRODUCT_LIST_QUANTITY':
          $lc_text = TABLE_HEADING_QUANTITY;
          $lc_align = 'right';
          break;
        case 'PRODUCT_LIST_WEIGHT':
          $lc_text = TABLE_HEADING_WEIGHT;
          $lc_align = 'right';
          break;
        case 'PRODUCT_LIST_IMAGE':
          $lc_text = TABLE_HEADING_IMAGE;
          $lc_align = 'center';
          break;
        case 'PRODUCT_LIST_BUY_NOW':
          $lc_text = TABLE_HEADING_BUY_NOW;
          $lc_align = 'center';
          break;
      }

      if ( ($column_list[$col] != 'PRODUCT_LIST_BUY_NOW') && ($column_list[$col] != 'PRODUCT_LIST_IMAGE') ) {
        $lc_text = tep_create_sort_heading($HTTP_GET_VARS['sort'], $col+1, $lc_text);
      }

      $list_box_contents[0][] = array('align' => $lc_align,
                                      'params' => 'class="productListing-heading"',
                                      'text' => '&nbsp;' . $lc_text . '&nbsp;');
    }

    $rows = 0;
    $listing_query = tep_db_query($listing_split->sql_query);
    while ($listing = tep_db_fetch_array($listing_query)) {
      $rows++;

      if (($rows/2) == floor($rows/2)) {
        $list_box_contents[] = array('params' => 'class="productListing-even"');
      } else {
        $list_box_contents[] = array('params' => 'class="productListing-odd"');
      }

// added by splautz to remove manufacturers_id from url (and breadcrumb too, as a result)
      if (($connection == 'NONSSL') && (SEARCH_ENGINE_FRIENDLY_URLS == 'true'))
        $manufacturers_param = 'manufacturers_id=' . $HTTP_GET_VARS['manufacturers_id'] . '&';
      else $manufacturers_param = '';

      $cur_row = sizeof($list_box_contents) - 1;
 // added by splautz for subtitle under product name
      $subtitle=''; $subtitle_col='';

      for ($col=0, $n=sizeof($column_list); $col<$n; $col++) {
        $lc_align = '';

        switch ($column_list[$col]) {
          case 'PRODUCT_LIST_MODEL':
            $lc_align = '';
            $lc_text = '&nbsp;' . $listing['products_model'] . '&nbsp;';
            break;
          case 'PRODUCT_LIST_NAME':
            $lc_align = '';
            if (isset($HTTP_GET_VARS['manufacturers_id'])) {
              $lc_text = '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, $manufacturers_param . 'products_id=' . $listing['products_id']) . '">' . $listing['products_name'] . '</a>';
            } else {
              $lc_text = '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, ($cPath ? 'cPath=' . $cPath . '&' : '') . 'products_id=' . $listing['products_id']) . '">' . $listing['products_name'] . '</a>';
            }
            $subtitle_col = count($list_box_contents[$cur_row])-1; // added by splautz for subtitle under product name
            break;
          case 'PRODUCT_LIST_INFO':
            $lc_align = '';
            $lc_text = $listing['products_info'] . '&nbsp;';
            break;
          case 'PRODUCT_LIST_MANUFACTURER':
            $lc_align = '';
            $lc_text = '&nbsp;<a href="' . tep_href_link(FILENAME_DEFAULT, 'manufacturers_id=' . $listing['manufacturers_id']) . '">' . $listing['manufacturers_name'] . '</a>&nbsp;';
            break;
          case 'PRODUCT_LIST_PRICE':
            $lc_align = 'right';
            if (tep_not_null($listing['specials_new_products_price'])) {
              $lc_text = '&nbsp;<s>' .  $currencies->display_price($listing['products_price'], tep_get_tax_rate($listing['products_tax_class_id'])) . '</s>  <span class="productSpecialPrice">' . $currencies->display_price($listing['specials_new_products_price'], tep_get_tax_rate($listing['products_tax_class_id'])) . '</span>&nbsp;';
            } else {
			if ($listing['products_price'] > 0) {
              $lc_text = '&nbsp;' . $currencies->display_price($listing['products_price'], tep_get_tax_rate($listing['products_tax_class_id'])) . '&nbsp;';
			  } else {
			  $lc_text = '&nbsp;';
			  }
            }
            break;
          case 'PRODUCT_LIST_QUANTITY':
            $lc_align = 'right';
            $lc_text = '&nbsp;' . $listing['products_quantity'] . '&nbsp;';
            break;
          case 'PRODUCT_LIST_WEIGHT':
            $lc_align = 'right';
            $lc_text = '&nbsp;' . $listing['products_weight'] . '&nbsp;';
            break;
          case 'PRODUCT_LIST_IMAGE':
            $lc_align = 'center';
            if (isset($HTTP_GET_VARS['manufacturers_id'])) {
              $lc_text = '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, $manufacturers_param . 'products_id=' . $listing['products_id']) . '">' . tep_image(DIR_WS_IMAGES . $listing['products_image'], $listing['products_img_alt']?$listing['products_img_alt']:$listing['products_name'], LISTING_IMAGE_WIDTH, LISTING_IMAGE_HEIGHT, 'hspace="5" vspace="5"') . '</a>';
            } else {
              $lc_text = '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, ($cPath ? 'cPath=' . $cPath . '&' : '') . 'products_id=' . $listing['products_id']) . '">' . tep_image(DIR_WS_IMAGES . $listing['products_image'], $listing['products_img_alt']?$listing['products_img_alt']:$listing['products_name'], LISTING_IMAGE_WIDTH, LISTING_IMAGE_HEIGHT, 'hspace="5" vspace="5"') . '</a>';
            }
            break;
          case 'PRODUCT_LIST_BUY_NOW':
            $lc_align = 'center';
            $lc_text = '<a href="' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=buy_now' . (!empty($listing['products_affiliate_url'])?'_aff':'') . '&products_id=' . $listing['products_id']) . '">' . tep_image_button('button_buy_now.gif', IMAGE_BUTTON_BUY_NOW) . '</a>&nbsp;';
            break;
        }
 // added by splautz for subtitle under product name
        if ($column_list[$col] == 'PRODUCT_LIST_'.$subtitle_name && $subtitle_name != 'NAME') $subtitle = '<br><table border="0" cellpadding="0" cellspacing="0" width="100%"><tbody><tr><td class="productListing-subtitle" align="'.$lc_align.'">'.$lc_text.'</td></tr></tbody></table>';
        else

        $list_box_contents[$cur_row][] = array('align' => $lc_align,
                                               'params' => 'class="productListing-data"',
                                               'text'  => $lc_text);
      }
 // added by splautz for subtitle under product name
      if (is_numeric($subtitle_col) && tep_not_null($subtitle)) $list_box_contents[$cur_row][$subtitle_col]['text'] .= $subtitle;

    }

    new productListingBox($list_box_contents);

    if ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3')) {
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td class="smallText"><?php echo $listing_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
    <td class="smallText" align="right"><?php echo TEXT_RESULT_PAGE . ' ' . $listing_split->display_links(MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
  </tr>
</table>
<?php
    }
  } elseif (TEXT_NO_PRODUCTS) {
    $list_box_contents = array();

    $list_box_contents[0] = array('params' => 'class="productListing-odd"');
    $list_box_contents[0][] = array('params' => 'class="productListing-data"',
                                   'text' => TEXT_NO_PRODUCTS);

    new productListingBox($list_box_contents);
  }
?>
