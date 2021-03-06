<?php
/*
  $Id: wishlist.php,v 3.0  2005/04/20 Dennis Blake
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Released under the GNU General Public License
*/

/*******************************************************************
****** QUERY THE DATABASE FOR THE CUSTOMERS WISHLIST PRODUCTS ******
*******************************************************************/
  require_once(DIR_WS_LANGUAGES . $language . '/' . FILENAME_WISHLIST);


?>
<!-- wishlist //-->
          <tr>
            <td>

<?php
    $info_box_contents = array();
    $info_box_contents[] = array('align' => 'left',
                                 'text'  => BOX_HEADING_CUSTOMER_WISHLIST
                                );
    new infoBoxHeading($info_box_contents, false, false, tep_href_link(FILENAME_WISHLIST, '','NONSSL'));

    $info_box_contents = array();

	if (is_array($wishList->wishID) && !empty($wishList->wishID)) {
	reset($wishList->wishID);

	if (count($wishList->wishID) < MAX_DISPLAY_WISHLIST_BOX) {

		$wishlist_box = '<table>';
		$counter = 1;

/*******************************************************************
*** LOOP THROUGH EACH PRODUCT ID TO DISPLAY IN THE WISHLIST BOX ****
*******************************************************************/

	    while (list($wishlist_id, ) = each($wishList->wishID)) {
		$products_id = tep_get_prid($wishlist_id);

// update-20051113
    	$products_query = tep_db_query("select pd.products_id, pd.products_name, pd.products_description, p.products_image, p.products_price, p.products_tax_class_id, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id, " . TABLE_PRODUCTS_DESCRIPTION . " pd where pd.products_id = '" . $products_id . "' and p.products_id = pd.products_id and pd.language_id = '" . $languages_id . "' order by products_name");
		$products = tep_db_fetch_array($products_query);

		$wishlist_box .= '<tr><td class="infoBoxContents" valign="top">0' . $counter . '.</td>';
		$wishlist_box .= '<td class="infoBoxContents"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $wishlist_id, 'NONSSL') . '">' . $products['products_name'] . '</a></td></tr>';
		
		$counter++;
		}

	$wishlist_box .= '</table>';

	} else {

	$wishlist_box = '<div class="infoBoxContents">' . sprintf(TEXT_WISHLIST_COUNT, count($wishList->wishID)) . '</div>';

	}

  } else {

	$wishlist_box = '<div class="infoBoxContents">' . BOX_WISHLIST_EMPTY . '</div>';

  }

    $info_box_contents[] = array('align' => 'left',
                                 'text'  => $wishlist_box);

    new infoBox($info_box_contents);
?>
            </td>
          </tr>
<!-- wishlist_eof //-->