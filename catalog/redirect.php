<?php
/*
  $Id: redirect.php,v 1.10 2003/06/05 23:31:31 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

/* spam redirect patch
 * ir8@prim8.net
 * 2005/08/24
 *
 * People are using this redirect page to send out their spams.
 * Put in your acceptable redirect sites in $allowed_goto array.  Only domains in 
 * allowed_goto can be used in a action=url goto.
 */

  $allowed_goto = array($HTTP_SERVER_VARS['HTTP_HOST']);

  require('includes/application_top.php');

  switch ($HTTP_GET_VARS['action']) {
    case 'banner':
      $banner_query = tep_db_query("select banners_url from " . TABLE_BANNERS . " where banners_id = '" . (int)$HTTP_GET_VARS['goto'] . "'");
      if (tep_db_num_rows($banner_query)) {
        $banner = tep_db_fetch_array($banner_query);
        tep_update_banner_click_count($HTTP_GET_VARS['goto']);

        tep_redirect($banner['banners_url']);
      }
      break;

    case 'url':
      if (isset($HTTP_GET_VARS['goto']) && tep_not_null($HTTP_GET_VARS['goto'])) {
//      tep_redirect('http://' . $HTTP_GET_VARS['goto']);
        if (($spos=strpos($HTTP_GET_VARS['goto'], '/')) === false) $goto_host = $HTTP_GET_VARS['goto'];
        else $goto_host = substr($HTTP_GET_VARS['goto'], 0, $spos);
 // Update 051113
        $check_query = tep_db_query("select products_url from " . TABLE_PRODUCTS_DESCRIPTION . " where products_url = '" . tep_db_input($HTTP_GET_VARS['goto']) . "' limit 1");
        if (tep_db_num_rows($check_query) || in_array($goto_host, $allowed_goto)) {
          tep_redirect('http://' . $HTTP_GET_VARS['goto']);
        }
      }
      break;

// added by splautz for seo url redirects on category/manufacturer filter (index.php) & in manufacturer box
    case 'rewrite':
      if (isset($HTTP_GET_VARS['goto']) && tep_not_null($HTTP_GET_VARS['goto'])) {
        tep_redirect(tep_href_link($HTTP_GET_VARS['goto'], tep_get_all_get_params(array('action','goto'))));
      }
      break;

// added by splautz for affiliate links
    case 'aff_link':
      if ($HTTP_GET_VARS['products_id']) {
        $product_affiliate_query = tep_db_query("select pd.products_affiliate_url from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "' and pd.products_id = p.products_id and pd.language_id = '" . (int)$languages_id . "'");
        $product_affiliate = tep_db_fetch_array($product_affiliate_query);
        if (!empty($product_affiliate['products_affiliate_url'])) tep_redirect('http://' . $product_affiliate['products_affiliate_url']);
      }
      break;

    case 'manufacturer':
      if (isset($HTTP_GET_VARS['manufacturers_id']) && tep_not_null($HTTP_GET_VARS['manufacturers_id'])) {
        $manufacturer_query = tep_db_query("select manufacturers_url from " . TABLE_MANUFACTURERS_INFO . " where manufacturers_id = '" . (int)$HTTP_GET_VARS['manufacturers_id'] . "' and languages_id = '" . (int)$languages_id . "'");
        if (tep_db_num_rows($manufacturer_query)) {
// url exists in selected language
          $manufacturer = tep_db_fetch_array($manufacturer_query);

          if (tep_not_null($manufacturer['manufacturers_url'])) {
            tep_db_query("update " . TABLE_MANUFACTURERS_INFO . " set url_clicked = url_clicked+1, date_last_click = now() where manufacturers_id = '" . (int)$HTTP_GET_VARS['manufacturers_id'] . "' and languages_id = '" . (int)$languages_id . "'");

            tep_redirect($manufacturer['manufacturers_url']);
          }
        } else {
// no url exists for the selected language, lets use the default language then
          $manufacturer_query = tep_db_query("select mi.languages_id, mi.manufacturers_url from " . TABLE_MANUFACTURERS_INFO . " mi, " . TABLE_LANGUAGES . " l where mi.manufacturers_id = '" . (int)$HTTP_GET_VARS['manufacturers_id'] . "' and mi.languages_id = l.languages_id and l.code = '" . DEFAULT_LANGUAGE . "'");
          if (tep_db_num_rows($manufacturer_query)) {
            $manufacturer = tep_db_fetch_array($manufacturer_query);

            if (tep_not_null($manufacturer['manufacturers_url'])) {
              tep_db_query("update " . TABLE_MANUFACTURERS_INFO . " set url_clicked = url_clicked+1, date_last_click = now() where manufacturers_id = '" . (int)$HTTP_GET_VARS['manufacturers_id'] . "' and languages_id = '" . (int)$manufacturer['languages_id'] . "'");

              tep_redirect($manufacturer['manufacturers_url']);
            }
          }
        }
      }
      break;

// VJ Links Manager v1.00 begin
    case 'links':
      require(DIR_WS_FUNCTIONS . 'links.php');

      $links_query = tep_db_query("select links_url from " . TABLE_LINKS . " where links_id = '" . (int)$HTTP_GET_VARS['goto'] . "'");
      if (tep_db_num_rows($links_query)) {
        $link = tep_db_fetch_array($links_query);
        tep_update_links_click_count($HTTP_GET_VARS['goto']);

        tep_redirect($link['links_url']);
      }
      break;
// VJ Links Manager v1.00 end
  }

  tep_redirect(tep_href_link(FILENAME_DEFAULT));
?>
