<?php
/*
  $Id: categories.php,v 1.146 2003/07/11 14:40:27 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

// added by splautz for WYSIWYG compatibility
  $brInfo = tep_get_browser();
  $wysiwyg = ($brInfo['browser'] == 'MSIE' ? $brInfo['version'] : 0) >= 5.5 ? true : false;
  $pi_category_id = PRODUCT_INVENTORY_CATID; // added by splautz for Product Inventory

  $action = (isset($HTTP_GET_VARS['action']) ? $HTTP_GET_VARS['action'] : '');
// added by splautz for keeping track of current page
  $page = tep_db_prepare_input(isset($HTTP_GET_VARS['page']) ? $HTTP_GET_VARS['page'] : '1');
  $urlp = (empty($HTTP_GET_VARS['search'])?'cPath='.$cPath:'search='.urlencode($HTTP_GET_VARS['search']));
  $urlpage = $urlp.($page>1?'&page='.$page:'');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'setflag':
        if ( ($HTTP_GET_VARS['flag'] == '0') || ($HTTP_GET_VARS['flag'] == '1') ) {
          if (isset($HTTP_GET_VARS['pID'])) {
            tep_set_product_status($HTTP_GET_VARS['pID'], $HTTP_GET_VARS['flag']);
          }

          if (USE_CACHE == 'true') {
            tep_reset_cache_block('categories');
            tep_reset_cache_block('also_purchased');
          }
        }

        tep_redirect(tep_href_link(FILENAME_CATEGORIES, $urlpage . '&pID=' . $HTTP_GET_VARS['pID']));
        break;
// ####################### Added Categories Enable / Disable ###############
      case 'setflag_cat':
        if ( ($HTTP_GET_VARS['flag'] == '0') || ($HTTP_GET_VARS['flag'] == '1') ) {
          if (isset($HTTP_GET_VARS['cID'])) {
            tep_set_categories_status($HTTP_GET_VARS['cID'], $HTTP_GET_VARS['flag']);
          }

          if (USE_CACHE == 'true') {
            tep_reset_cache_block('categories');
            tep_reset_cache_block('also_purchased');
          }
        }

	tep_redirect(tep_href_link(FILENAME_CATEGORIES, $urlpage . '&cID=' . $HTTP_GET_VARS['cID']));
	break;
// ####################### End Categories Enable / Disable ###############
      case 'insert_category':
      case 'update_category':
        if (isset($HTTP_POST_VARS['categories_id'])) $categories_id = tep_db_prepare_input($HTTP_POST_VARS['categories_id']);
        $sort_order = tep_db_prepare_input($HTTP_POST_VARS['sort_order']);
        if (!is_numeric($sort_order)) $sort_order = 'null';
        $categories_dpids = tep_db_prepare_input($HTTP_POST_VARS['categories_dpids']);
        $categories_pranges = tep_db_prepare_input($HTTP_POST_VARS['categories_pranges']);

// ####################### Added Categories Enable / Disable ###############
//      $sql_data_array = array('sort_order' => $sort_order);
        $categories_status = tep_db_prepare_input($HTTP_POST_VARS['categories_status']);
        $sql_data_array = array('sort_order' => $sort_order, 'categories_dpids' => $categories_dpids, 'categories_pranges' => $categories_pranges, 'categories_status' => $categories_status);
// ####################### End Added Categories Enable / Disable ###############

// modified by splautz to ensure proper image management
        if (!$wysiwyg || HTML_AREA_WYSIWYG_DISABLE_IMAGE == 'Disable') {
          if ($categories_image = new upload('categories_image', DIR_FS_CATALOG_IMAGES)) {
            $sql_data_array['categories_image'] = $categories_image->filename;
            if ($action == 'update_category') tep_remove_image($categories_id,'c',$categories_image->filename);
          }
        } else {
          if (isset($HTTP_POST_VARS['categories_image']) && tep_not_null($HTTP_POST_VARS['categories_image']) && ($HTTP_POST_VARS['categories_image'] != 'none')) {
            $categories_image = tep_db_prepare_input($HTTP_POST_VARS['categories_image']);
          } else $categories_image = '';
          $sql_data_array['categories_image'] = $categories_image;
          if ($action == 'update_category') tep_remove_image($categories_id,'c',$categories_image);
        }

        if ($action == 'insert_category') {
          $insert_sql_data = array('parent_id' => $current_category_id,
                                   'date_added' => 'now()');

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          tep_db_perform(TABLE_CATEGORIES, $sql_data_array);

          $categories_id = tep_db_insert_id();
        } elseif ($action == 'update_category') {
          $update_sql_data = array('last_modified' => 'now()');

          $sql_data_array = array_merge($sql_data_array, $update_sql_data);

          tep_db_perform(TABLE_CATEGORIES, $sql_data_array, 'update', "categories_id = '" . (int)$categories_id . "'");
        }

        $languages = tep_get_languages();
        for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
          $language_id = $languages[$i]['id'];

          // added by splautz to ensure TABLE_SEO_SURLS is updated
         if (isset($HTTP_POST_VARS['categories_surls_name'][$language_id])) $surls_name=str_replace(" ", '-', strtolower(trim($HTTP_POST_VARS['categories_surls_name'][$language_id]))); 
          if (isset($HTTP_POST_VARS['categories_surls_id'][$language_id]) && is_numeric($surls_id=$HTTP_POST_VARS['categories_surls_id'][$language_id])) {
            if ($surls_name) {
              if (tep_check_dup_surl(true, $surls_id, tep_db_prepare_input($surls_name))) $surls_name = tep_get_category_surls_name($categories_id, $language_id);
              else {
                $sql_data_array = array('surls_name' => tep_db_prepare_input($surls_name));
                tep_db_perform(TABLE_SEO_URLS, $sql_data_array, 'update', "surls_id = '" . (int)$surls_id . "'");
                $surls_updated = true;
              }
			} else {
              tep_remove_surl($surls_id);
              $surls_id = NULL;
              $surls_updated = true;
            }
		  } elseif ($surls_name) {
            if (tep_check_dup_surl(true, '', tep_db_prepare_input($surls_name), 'index.php', 'cPath=' . (int)$categories_id, $language_id)) {  // dup exists
              $surls_name = '';
              $surls_id = NULL;
            } else {  // dup not found, ok to insert
              $sql_data_array = array('surls_name' => tep_db_prepare_input($surls_name),
                'surls_script' => 'index.php',
                'surls_param' => 'cPath=' . (int)$categories_id,
                'language_id' => $language_id);
              tep_db_perform(TABLE_SEO_URLS, $sql_data_array);
              $surls_id = tep_db_insert_id();
              $surls_updated = true;
            }
		  } else $surls_id = NULL;

          //HTC BOC
          $sql_data_array = array('categories_name' => tep_db_prepare_input($HTTP_POST_VARS['categories_name'][$language_id]),
           'categories_htc_title_tag' => tep_db_prepare_input($HTTP_POST_VARS['categories_htc_title_tag'][$language_id]),
           'categories_htc_desc_tag' => tep_db_prepare_input($HTTP_POST_VARS['categories_htc_desc_tag'][$language_id]),
           'categories_htc_keywords_tag' => tep_db_prepare_input($HTTP_POST_VARS['categories_htc_keywords_tag'][$language_id]),
           'categories_h1' => tep_db_prepare_input($HTTP_POST_VARS['categories_h1'][$language_id]),
           'categories_surls_id' => ($surls_id===NULL)?'null':tep_db_prepare_input($surls_id),
           'categories_htc_description' => tep_db_prepare_input($HTTP_POST_VARS['categories_htc_description'][$language_id]),
           'categories_body' => tep_db_prepare_input($HTTP_POST_VARS['categories_body'][$language_id]),
           'categories_body2' => tep_db_prepare_input($HTTP_POST_VARS['categories_body2'][$language_id]),
           'categories_img_alt' => tep_db_prepare_input($HTTP_POST_VARS['categories_img_alt'][$language_id]));
          //HTC EOC 
      
          if ($action == 'insert_category') {
            $insert_sql_data = array('categories_id' => $categories_id,
                                     'language_id' => $languages[$i]['id']);

            $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

            tep_db_perform(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array);
          } elseif ($action == 'update_category') {
            tep_db_perform(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array, 'update', "categories_id = '" . (int)$categories_id . "' and language_id = '" . (int)$languages[$i]['id'] . "'");
          }
        }

// modified by splautz to ensure proper image management
//      if ($categories_image = new upload('categories_image', DIR_FS_CATALOG_IMAGES)) {
//        tep_db_query("update " . TABLE_CATEGORIES . " set categories_image = '" . tep_db_input($categories_image->filename) . "' where categories_id = '" . (int)$categories_id . "'");
//      }

        if (USE_CACHE == 'true') {
          tep_reset_cache_block('categories');
          tep_reset_cache_block('also_purchased');
        }

        tep_redirect(tep_href_link(FILENAME_CATEGORIES, $urlpage . '&cID=' . $categories_id));
        break;
      case 'delete_category_confirm':
        if (isset($HTTP_POST_VARS['categories_id'])) {
          $categories_id = tep_db_prepare_input($HTTP_POST_VARS['categories_id']);

          $categories = tep_get_category_tree($categories_id, '', '0', '', true);
          $products = array();
          $products_delete = array();

          for ($i=0, $n=sizeof($categories); $i<$n; $i++) {
            $product_ids_query = tep_db_query("select products_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " where categories_id = '" . (int)$categories[$i]['id'] . "'");

            while ($product_ids = tep_db_fetch_array($product_ids_query)) {
              $products[$product_ids['products_id']]['categories'][] = $categories[$i]['id'];
            }
          }

          reset($products);
          while (list($key, $value) = each($products)) {
            $category_ids = '';

            for ($i=0, $n=sizeof($value['categories']); $i<$n; $i++) {
              $category_ids .= "'" . (int)$value['categories'][$i] . "', ";
            }
            $category_ids = substr($category_ids, 0, -2);

            $check_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . (int)$key . "' and categories_id not in (" . $category_ids . ")");
            $check = tep_db_fetch_array($check_query);
            if ($check['total'] < '1') {
              $products_delete[$key] = $key;
            }
          }

// removing categories can be a lengthy process
          tep_set_time_limit(0);
          for ($i=0, $n=sizeof($categories); $i<$n; $i++) {
            tep_remove_category($categories[$i]['id']);
          }

          reset($products_delete);
          while (list($key) = each($products_delete)) {
            tep_remove_product($key);
          }
        }

        if (USE_CACHE == 'true') {
          tep_reset_cache_block('categories');
          tep_reset_cache_block('also_purchased');
        }

        tep_redirect(tep_href_link(FILENAME_CATEGORIES, $urlpage));
        break;
      case 'delete_product_confirm':
        if (isset($HTTP_POST_VARS['products_id']) && isset($HTTP_POST_VARS['product_categories']) && is_array($HTTP_POST_VARS['product_categories'])) {
          $product_id = tep_db_prepare_input($HTTP_POST_VARS['products_id']);
          $product_categories = $HTTP_POST_VARS['product_categories'];

          for ($i=0, $n=sizeof($product_categories); $i<$n; $i++) {
            tep_db_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . (int)$product_id . "' and categories_id = '" . (int)$product_categories[$i] . "'");
          }

          $product_categories_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . (int)$product_id . "'");
          $product_categories = tep_db_fetch_array($product_categories_query);

          if ($product_categories['total'] == '0') {
            tep_remove_product($product_id);
          }
        }

        if (USE_CACHE == 'true') {
          tep_reset_cache_block('categories');
          tep_reset_cache_block('also_purchased');
        }

        tep_redirect(tep_href_link(FILENAME_CATEGORIES, $urlpage));
        break;
      case 'move_category_confirm':
        if (isset($HTTP_POST_VARS['categories_id']) && ($HTTP_POST_VARS['categories_id'] != $HTTP_POST_VARS['move_to_category_id'])) {
          $categories_id = tep_db_prepare_input($HTTP_POST_VARS['categories_id']);
          $new_parent_id = tep_db_prepare_input($HTTP_POST_VARS['move_to_category_id']);

          $path = explode('_', tep_get_generated_category_path_ids($new_parent_id));

          if (in_array($categories_id, $path)) {
            $messageStack->add_session(ERROR_CANNOT_MOVE_CATEGORY_TO_PARENT, 'error');

            tep_redirect(tep_href_link(FILENAME_CATEGORIES, $urlpage . '&cID=' . $categories_id));
          } else {
            tep_db_query("update " . TABLE_CATEGORIES . " set parent_id = '" . (int)$new_parent_id . "', last_modified = now() where categories_id = '" . (int)$categories_id . "'");

            if (USE_CACHE == 'true') {
              tep_reset_cache_block('categories');
              tep_reset_cache_block('also_purchased');
            }

// modified by splautz to maintain current page
//          tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $new_parent_id . '&cID=' . $categories_id));
            tep_redirect(tep_href_link(FILENAME_CATEGORIES, $urlpage . '&cID=' . $categories_id));
          }
        }

        break;
      case 'move_product_confirm':
        $products_id = tep_db_prepare_input($HTTP_POST_VARS['products_id']);
        $new_parent_id = tep_db_prepare_input($HTTP_POST_VARS['move_to_category_id']);

        $duplicate_check_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . (int)$products_id . "' and categories_id = '" . (int)$new_parent_id . "'");
        $duplicate_check = tep_db_fetch_array($duplicate_check_query);
        if ($duplicate_check['total'] < 1) tep_db_query("update " . TABLE_PRODUCTS_TO_CATEGORIES . " set categories_id = '" . (int)$new_parent_id . "' where products_id = '" . (int)$products_id . "' and categories_id = '" . (int)$current_category_id . "'");

        if (USE_CACHE == 'true') {
          tep_reset_cache_block('categories');
          tep_reset_cache_block('also_purchased');
        }

// modified by splautz to maintain current page
//      tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $new_parent_id . '&pID=' . $products_id));
        tep_redirect(tep_href_link(FILENAME_CATEGORIES, $urlpage . '&pID=' . $products_id));
        break;
      case 'insert_product':
      case 'update_product':
        if (isset($HTTP_POST_VARS['edit_x']) || isset($HTTP_POST_VARS['edit_y'])) {
          $action = 'new_product';
        } else {
          if (isset($HTTP_GET_VARS['pID'])) $products_id = tep_db_prepare_input($HTTP_GET_VARS['pID']);
          $products_date_available = tep_db_prepare_input($HTTP_POST_VARS['products_date_available']);
          $products_date_available = (date('Y-m-d') < $products_date_available) ? $products_date_available : 'null';

if ($HTTP_POST_VARS['products_specials'] > 0) $products_special_value = 1; else $products_special_value = 0;

          $sql_data_array = array('products_quantity' => tep_db_prepare_input($HTTP_POST_VARS['products_quantity']),
                                  'products_model' => tep_db_prepare_input($HTTP_POST_VARS['products_model']),
                                  'products_price' => tep_db_prepare_input($HTTP_POST_VARS['products_price']),
                                  'products_date_available' => $products_date_available,
                                  'products_weight' => tep_db_prepare_input($HTTP_POST_VARS['products_weight']),
                                  'products_free_shipping' => tep_db_prepare_input($HTTP_POST_VARS['products_free_shipping']),
                                  'products_specials' => $products_special_value,
                                  'products_origin_postcode' => tep_db_prepare_input($HTTP_POST_VARS['products_origin_postcode']),
                                  'products_freight_class' => tep_db_prepare_input($HTTP_POST_VARS['products_freight_class']),
                                  'products_sun_class' => tep_db_prepare_input($HTTP_POST_VARS['products_sun_class']),
                                  'products_status' => tep_db_prepare_input($HTTP_POST_VARS['products_status']),
                                  'products_tax_class_id' => tep_db_prepare_input($HTTP_POST_VARS['products_tax_class_id']),
                                  'manufacturers_id' => tep_db_prepare_input($HTTP_POST_VARS['manufacturers_id']));

// modified by splautz to ensure proper image management
          if (isset($HTTP_POST_VARS['products_image']) && tep_not_null($HTTP_POST_VARS['products_image']) && ($HTTP_POST_VARS['products_image'] != 'none')) {
            $products_image = tep_db_prepare_input($HTTP_POST_VARS['products_image']);
          } else $products_image = '';
          $sql_data_array['products_image'] = $products_image;
          if ($action == 'update_product') tep_remove_image($products_id,'p',$products_image);

          if (isset($HTTP_POST_VARS['categories_ids'])) $selected_catids = $HTTP_POST_VARS['categories_ids'];
          else $selected_catids = array(0);
          $insert_catids = array();
          if ($action == 'insert_product') {
            $insert_sql_data = array('products_date_added' => 'now()');

            $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

            tep_db_perform(TABLE_PRODUCTS, $sql_data_array);
            $products_id = tep_db_insert_id();

            $insert_catids = $selected_catids;
// removed by multiple categories
//            tep_db_query("insert into " . TABLE_PRODUCTS_TO_CATEGORIES . " (products_id, categories_id) values ('" . (int)$products_id . "', '" . (int)$current_category_id . "')");
//          } elseif ($action == 'update_product') {
//            $update_sql_data = array('products_last_modified' => 'now()');
//
//            $sql_data_array = array_merge($sql_data_array, $update_sql_data);
//
//            tep_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = '" . (int)$products_id . "'");
//          }
          } elseif ($action == 'update_product') {
            $update_sql_data = array('products_last_modified' => 'now()');

            $sql_data_array = array_merge($sql_data_array, $update_sql_data);

            tep_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = '" . (int)$products_id . "'");

	        #delete unused categories saved in the tables
            $p2c_query = tep_db_query("select categories_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '". (int)$products_id . "'");
            while($p2c=tep_db_fetch_array($p2c_query)) {
              if (in_array($p2c['categories_id'],$selected_catids)) $insert_catids[] = $p2c['categories_id'];
	          else tep_db_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . (int)$products_id . "' and categories_id = '" . $p2c['categories_id'] . "'");	  
            }

            $insert_catids = array_diff($selected_catids,$insert_catids);
          }

          # create loop here to insert rows for multiple categories
          if ($insert_catids)
          {
            foreach ($insert_catids as $categories_id)
              {
		        tep_db_query("insert into " . TABLE_PRODUCTS_TO_CATEGORIES . " (products_id, categories_id, sort_order) values ('" . (int)$products_id . "', '" . (int)$categories_id . "', null)");
              }
	      }

          // added by splautz to ensure sort order is updated
          if ($current_category_id != $pi_category_id) {
            $sort_order = tep_db_prepare_input($HTTP_POST_VARS['sort_order']);
            if (!is_numeric($sort_order)) $sort_order = "null";
            else $sort_order = "'$sort_order'";
            tep_db_query("update " . TABLE_PRODUCTS_TO_CATEGORIES . " set sort_order = " . $sort_order . " where products_id = '" . (int)$products_id . "' and categories_id = '" . (int)$current_category_id . "'");
          }

          // added by splautz to ensure special price is updated
          $specials_query = tep_db_query("select specials_id from " . TABLE_SPECIALS . " where products_id = '" . (int)$products_id . "'");
          if ($specials = tep_db_fetch_array($specials_query)) $specials_id = $specials['specials_id'];
          if ((($specials_status=$HTTP_POST_VARS['specials_status']) === '') || (($specials_price=$HTTP_POST_VARS['specials_price']) === '')) {
            if (isset($specials_id)) tep_db_query("delete from " . TABLE_SPECIALS . " where specials_id = '" . $specials_id . "'");
          } elseif (isset($specials_id)) {
            tep_db_query("update " . TABLE_SPECIALS . " set specials_new_products_price = '" . tep_db_input($specials_price) . "', specials_last_modified = now(), expires_date = '" . tep_db_input($HTTP_POST_VARS['specials_xdate']) . "', status = '" . tep_db_input($specials_status) . "' where specials_id = '" . $specials_id . "'");
          } else {
            tep_db_query("insert into " . TABLE_SPECIALS . " (products_id, specials_new_products_price, specials_date_added, expires_date, status) values ('" . (int)$products_id . "', '" . tep_db_input($specials_price) . "', now(), '" . tep_db_input($HTTP_POST_VARS['specials_xdate']) . "', '" . tep_db_input($specials_status) . "')");
          }

          $languages = tep_get_languages();
          for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
            $language_id = $languages[$i]['id'];

            // added by splautz to ensure TABLE_SEO_SURLS is updated
            if (isset($HTTP_POST_VARS['products_surls_name'][$language_id])) $surls_name=str_replace(" ", '-', strtolower(trim($HTTP_POST_VARS['products_surls_name'][$language_id]))); 
            if (isset($HTTP_POST_VARS['products_surls_id'][$language_id]) && is_numeric($surls_id=$HTTP_POST_VARS['products_surls_id'][$language_id])) {
              if ($surls_name) {
                if (tep_check_dup_surl(true, $surls_id, tep_db_prepare_input($surls_name))) $surls_name = tep_get_products_surls_name($products_id, $language_id);
                else {
                  $sql_data_array = array('surls_name' => tep_db_prepare_input($surls_name));
                  tep_db_perform(TABLE_SEO_URLS, $sql_data_array, 'update', "surls_id = '" . (int)$surls_id . "'");
                  $surls_updated = true;
                }
			  } else {
                tep_remove_surl($surls_id);
		        $surls_id = NULL;
                $surls_updated = true;
              }
		    } elseif ($surls_name) {
              if (tep_check_dup_surl(true, '', tep_db_prepare_input($surls_name), 'product_info.php', 'products_id=' . (int)$products_id, $language_id)) {  // dup exists
                $surls_name = '';
                $surls_id = NULL;
              } else {  // dup not found, ok to insert
                $sql_data_array = array('surls_name' => tep_db_prepare_input($surls_name),
                  'surls_script' => 'product_info.php',
                  'surls_param' => 'products_id=' . (int)$products_id,
                  'language_id' => $language_id);
                tep_db_perform(TABLE_SEO_URLS, $sql_data_array);
                $surls_id = tep_db_insert_id();
                $surls_updated = true;
              }
		    } else $surls_id = NULL;

            //HTC BOC
            $sql_data_array = array('products_name' => tep_db_prepare_input($HTTP_POST_VARS['products_name'][$language_id]),
                                    'products_img_alt' => tep_db_prepare_input($HTTP_POST_VARS['products_img_alt'][$language_id]),
                                    'products_info' => tep_db_prepare_input($HTTP_POST_VARS['products_info'][$language_id]),
                                    'products_description' => tep_db_prepare_input($HTTP_POST_VARS['products_description'][$language_id]),
                                    'products_url' => tep_db_prepare_input($HTTP_POST_VARS['products_url'][$language_id]),
                                    'products_affiliate_url' => tep_db_prepare_input($HTTP_POST_VARS['products_affiliate_url'][$language_id]),
                                    'products_head_title_tag' => tep_db_prepare_input($HTTP_POST_VARS['products_head_title_tag'][$language_id]),
                                    'products_head_desc_tag' => tep_db_prepare_input($HTTP_POST_VARS['products_head_desc_tag'][$language_id]),
                                    'products_head_keywords_tag' => tep_db_prepare_input($HTTP_POST_VARS['products_head_keywords_tag'][$language_id]),   
                                    'products_surls_id' => ($surls_id===NULL)?'null':tep_db_prepare_input($surls_id),   
                                    'products_h1' => tep_db_prepare_input($HTTP_POST_VARS['products_h1'][$language_id]));
           //HTC EOC
                       
            if ($action == 'insert_product') {
              $insert_sql_data = array('products_id' => $products_id,
                                       'language_id' => $language_id);

              $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

              tep_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array);
            } elseif ($action == 'update_product') {
              tep_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, 'update', "products_id = '" . (int)$products_id . "' and language_id = '" . (int)$language_id . "'");
            }
          }

          // START: Extra Fields Contribution
          $extra_fields_query = tep_db_query("SELECT * FROM " . TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS . " WHERE products_id = " . (int)$products_id);
          while ($products_extra_fields = tep_db_fetch_array($extra_fields_query)) {
            $extra_product_entry[$products_extra_fields['products_extra_fields_id']] = $products_extra_fields['products_extra_fields_value'];
          }

          if ($HTTP_POST_VARS['extra_field']) { // Check to see if there are any need to update extra fields.
            foreach ($HTTP_POST_VARS['extra_field'] as $key=>$val) {
              if (isset($extra_product_entry[$key])) { // an entry exists
                if ($val == '') tep_db_query("DELETE FROM " . TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS . " where products_id = " . (int)$products_id . " AND  products_extra_fields_id = " . $key);
                else tep_db_query("UPDATE " . TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS . " SET products_extra_fields_value = '" . tep_db_prepare_input($val) . "' WHERE products_id = " . (int)$products_id . " AND  products_extra_fields_id = " . $key);
              }
              else { // an entry does not exist
                if ($val != '') tep_db_query("INSERT INTO " . TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS . " (products_id, products_extra_fields_id, products_extra_fields_value) VALUES ('" . (int)$products_id . "', '" . $key . "', '" . tep_db_prepare_input($val) . "')");
              }
            }
          } // Check to see if there are any need to update extra fields.
          // END: Extra Fields Contribution

          if (USE_CACHE == 'true') {
            tep_reset_cache_block('categories');
            tep_reset_cache_block('also_purchased');
          }

          tep_redirect(tep_href_link(FILENAME_CATEGORIES, $urlpage . '&pID=' . $products_id));
        }
        break;
      case 'copy_to_confirm':
        if (isset($HTTP_POST_VARS['products_id']) && isset($HTTP_POST_VARS['categories_id'])) {
          $products_id = tep_db_prepare_input($HTTP_POST_VARS['products_id']);
          $categories_id = tep_db_prepare_input($HTTP_POST_VARS['categories_id']);

          if ($HTTP_POST_VARS['copy_as'] == 'link') {
            if ($categories_id != $current_category_id) {
              $check_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . (int)$products_id . "' and categories_id = '" . (int)$categories_id . "'");
              $check = tep_db_fetch_array($check_query);
              if ($check['total'] < '1') {
                tep_db_query("insert into " . TABLE_PRODUCTS_TO_CATEGORIES . " (products_id, categories_id) values ('" . (int)$products_id . "', '" . (int)$categories_id . "')");
              }
            } else {
              $messageStack->add_session(ERROR_CANNOT_LINK_TO_SAME_CATEGORY, 'error');
            }
          } elseif ($HTTP_POST_VARS['copy_as'] == 'duplicate') {
            $product_query = tep_db_query("select products_quantity, products_model, products_image, products_price, products_date_available, products_weight, products_free_shipping, products_specials, products_origin_postcode, products_freight_class, products_sun_class, products_tax_class_id, manufacturers_id from " . TABLE_PRODUCTS . " where products_id = '" . (int)$products_id . "'");
            $product = tep_db_fetch_array($product_query);

// update-20051113
            tep_db_query("insert into " . TABLE_PRODUCTS . " (products_quantity, products_model, products_image, products_price, products_date_added, products_date_available, products_weight, products_specials, products_free_shipping, products_specials, products_origin_postcode, products_freight_class, products_sun_class, products_status, products_tax_class_id, manufacturers_id) values ('" . tep_db_input($product['products_quantity']) . "', '" . tep_db_input($product['products_model']) . "', '" . tep_db_input($product['products_image']) . "', '" . tep_db_input($product['products_price']) . "',  now(), " . (empty($product['products_date_available']) ? "null" : "'" . tep_db_input($product['products_date_available']) . "'") . ", '" . tep_db_input($product['products_weight']) . "', '" . 
             tep_db_input($product['products_free_shipping']) . "', '" . tep_db_input($product['products_specials']) . "', '" . tep_db_input($product['products_origin_postcode']) . "', '" . tep_db_input($product['products_freight_class']) . "', '" . tep_db_input($product['products_sun_class']) . "', '0', '" . (int)$product['products_tax_class_id'] . "', '" . (int)$product['manufacturers_id'] . "')");

            $dup_products_id = tep_db_insert_id();

            //HTC BOC 
            $description_query = tep_db_query("select language_id, products_name, products_img_alt, products_info, products_description, products_head_title_tag, products_head_desc_tag, products_head_keywords_tag, products_url, products_affiliate_url, products_h1 from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$products_id . "'");
            while ($description = tep_db_fetch_array($description_query)) {
              tep_db_query("insert into " . TABLE_PRODUCTS_DESCRIPTION . " (products_id, language_id, products_name, products_img_alt, products_info, products_description, products_head_title_tag, products_head_desc_tag, products_head_keywords_tag, products_url, products_affiliate_url, products_viewed, products_h1) values ('" . (int)$dup_products_id . "', '" . (int)$description['language_id'] . "', '" . tep_db_input($description['products_name']) . "', '" . tep_db_input($description['products_img_alt']) . "', '" . tep_db_input($description['products_info']) . "', '" . tep_db_input($description['products_description']) . "', '" . tep_db_input($description['products_head_title_tag']) . "', '" . tep_db_input($description['products_head_desc_tag']) . "', '" . tep_db_input($description['products_head_keywords_tag']) . "', '" . tep_db_input($description['products_url']) . "', '" . tep_db_input($description['products_affiliate_url']). "', '0', '" . tep_db_input($description['products_h1']) . "')");
            }       
            //HTC EOC 

            tep_db_query("insert into " . TABLE_PRODUCTS_TO_CATEGORIES . " (products_id, categories_id) values ('" . (int)$dup_products_id . "', '" . (int)$categories_id . "')");
            $products_id = $dup_products_id;
          }

          if (USE_CACHE == 'true') {
            tep_reset_cache_block('categories');
            tep_reset_cache_block('also_purchased');
          }
        }

// modified by splautz to maintain current page
//      tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $categories_id . '&pID=' . $products_id));
        tep_redirect(tep_href_link(FILENAME_CATEGORIES, $urlpage . '&pID=' . $products_id));
        break;
      case 'new_product_preview':
// copy image only if modified
// modified by splautz to ensure proper image management
        if (!$wysiwyg || HTML_AREA_WYSIWYG_DISABLE_IMAGE == 'Disable') {
          $products_image = new upload('products_image');
          $products_image->set_destination(DIR_FS_CATALOG_IMAGES);
          if ($products_image->parse() && $products_image->save()) {
            $products_image_name = $products_image->filename;
          } else {
            $products_image_name = stripslashes(isset($HTTP_POST_VARS['products_previous_image']) ? $HTTP_POST_VARS['products_previous_image'] : '');
          }
        } else {
          if (isset($HTTP_POST_VARS['products_image']) && tep_not_null($HTTP_POST_VARS['products_image']) && ($HTTP_POST_VARS['products_image'] != 'none')) {
            $products_image_name = $HTTP_POST_VARS['products_image'];
          } else {
            $products_image_name = '';
          }
        }
        break;
    }
  }

// check if the catalog image directory exists
  if (is_dir(DIR_FS_CATALOG_IMAGES)) {
    if (!is_writeable(DIR_FS_CATALOG_IMAGES)) $messageStack->add(ERROR_CATALOG_IMAGE_DIRECTORY_NOT_WRITEABLE, 'error');
  } else {
    $messageStack->add(ERROR_CATALOG_IMAGE_DIRECTORY_DOES_NOT_EXIST, 'error');
  }
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<?php if ($wysiwyg) { ?>
        <script language="Javascript1.2"><!-- // load htmlarea
// MaxiDVD Added WYSIWYG HTML Area Box + Admin Function v1.7 - 2.2 MS2 Products Description HTML - Head
        _editor_url = "<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_ADMIN; ?>htmlarea/";  // URL to htmlarea files
        _script_name = "<?php echo ((HTML_AREA_WYSIWYG_BASIC_PD == 'Basic') ? 'editor_basic.js' : 'editor_advanced.js'); ?>";  // script name of editor to use
         document.write('<scr' + 'ipt src="' +_editor_url+_script_name+ '"');
         document.write(' language="Javascript1.2"></scr' + 'ipt>');
// --></script>
<?php } ?>
<script language="javascript" src="includes/general.js"></script>
<script language="javascript" src="includes/javascript/spellcheck.js"></script>
<script language="javascript"><!--
function popupWindow(url,x,y) {
  window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width='+x+',height='+y+',screenX=150,screenY=150,top=150,left=150')
}
//--></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">
<div id="spiffycalendar" class="text"></div>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top">
<?php
  if ($action == 'new_product') {
    $parameters = array('products_name' => '',
                       'products_img_alt' => '',
                       'products_info' => '',
                       'products_description' => '',
                       'products_url' => '',
                       'products_affiliate_url' => '',
                       'products_id' => '',
                       'products_quantity' => '',
                       'products_model' => '',
                       'products_image' => '',
                       'products_price' => '',
                       'products_weight' => '',
                       'products_free_shipping' => '',
                       'products_origin_postcode' => '',
                       'products_freight_class' => '',
                       'products_sun_class' => '',
                       'products_date_added' => '',
                       'products_last_modified' => '',
                       'products_date_available' => '',
                       'products_status' => '',
                       'products_tax_class_id' => '',
                       'manufacturers_id' => '');

    $pInfo = new objectInfo($parameters);
    $languages = tep_get_languages();

   //HTC BOC
   if (isset ($HTTP_GET_VARS['pID']) && (!$HTTP_POST_VARS) ) {
// START: Extra Fields Contribution	  
      $products_extra_fields_query = tep_db_query("SELECT * FROM " . TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS . " WHERE products_id=" . (int)$HTTP_GET_VARS['pID']);
      while ($products_extra_fields = tep_db_fetch_array($products_extra_fields_query)) {
        $extra_field[$products_extra_fields['products_extra_fields_id']] = $products_extra_fields['products_extra_fields_value'];
      }
	  $extra_field_array=array('extra_field'=>$extra_field);
	  $pInfo->objectInfo($extra_field_array);
// END: Extra Fields Contribution
      $products_id = tep_db_prepare_input($HTTP_GET_VARS['pID']);
      $product_query = tep_db_query("select p.products_id, p.products_quantity, p.products_model, p.products_image, p.products_price, p.products_weight, p.products_free_shipping, p.products_specials, p.products_origin_postcode, p.products_freight_class, p.products_sun_class, p.products_date_added, p.products_last_modified, date_format(p.products_date_available, '%Y-%m-%d') as products_date_available, p.products_status, p.products_tax_class_id, p.manufacturers_id, s.status as specials_status, s.specials_new_products_price as specials_price, date_format(s.expires_date, '%Y-%m-%d') as specials_xdate, p2c.sort_order from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id left join " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c on p.products_id = p2c.products_id and p2c.categories_id = '" . $current_category_id . "' where p.products_id = '" . (int)$products_id . "'");
      $product = tep_db_fetch_array($product_query);                         
   //HTC EOC 
      $pInfo->objectInfo($product);
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        $product_query = tep_db_query("select pd.products_name, pd.products_img_alt, pd.products_info, pd.products_description, pd.products_head_title_tag, pd.products_head_desc_tag, pd.products_head_keywords_tag, pd.products_url, pd.products_affiliate_url, pd.products_surls_id, pd.products_h1 from " . TABLE_PRODUCTS_DESCRIPTION . " pd where pd.products_id = '" . $pInfo->products_id . "' and pd.language_id = '" . $languages[$i]['id'] . "'");
        $product = tep_db_fetch_array($product_query);
        $products_name[$languages[$i]['id']] = $product['products_name'];
        $products_img_alt[$languages[$i]['id']] = $product['products_img_alt'];
        $products_info[$languages[$i]['id']] = $product['products_info'];
        $products_description[$languages[$i]['id']] = $product['products_description'];
        $products_head_title_tag[$languages[$i]['id']] = $product['products_head_title_tag'];
        $products_head_desc_tag[$languages[$i]['id']] = $product['products_head_desc_tag'];
        $products_head_keywords_tag[$languages[$i]['id']] = $product['products_head_keywords_tag'];
        $products_url[$languages[$i]['id']] = $product['products_url'];
        $products_affiliate_url[$languages[$i]['id']] = $product['products_affiliate_url'];
        $products_surls_id[$languages[$i]['id']] = $product['products_surls_id'];
        $products_surls_name[$languages[$i]['id']] = tep_get_products_surls_name($pInfo->products_id, $languages[$i]['id']);
        $products_h1[$languages[$i]['id']] = $product['products_h1'];
      }
    } elseif (tep_not_null($HTTP_POST_VARS)) {
 // Update 051113 (alternate version of fix)
      $http_post = tep_db_prepare_input($HTTP_POST_VARS);
      $pInfo->objectInfo($http_post);
      $products_name = $http_post['products_name'];
      $products_img_alt = $http_post['products_img_alt'];
      $products_info = $http_post['products_info'];
      $products_description = $http_post['products_description'];
      $products_head_title_tag = $http_post['products_head_title_tag'];
      $products_head_desc_tag = $http_post['products_head_desc_tag'];
      $products_head_keywords_tag = $http_post['products_head_keywords_tag'];
      $products_url = $http_post['products_url'];
      $products_affiliate_url = $http_post['products_affiliate_url'];
      $products_surls_id = $http_post['products_surls_id'];
      $products_surls_name = $http_post['products_surls_name'];
      $products_h1 = $http_post['products_h1'];
    }

    $manufacturers_array = array(array('id' => '', 'text' => TEXT_NONE));
    $manufacturers_query = tep_db_query("select manufacturers_id, manufacturers_name from " . TABLE_MANUFACTURERS . " order by COALESCE(sort_order,10000), manufacturers_name");  // modified by splautz for sort
    while ($manufacturers = tep_db_fetch_array($manufacturers_query)) {
      $manufacturers_array[] = array('id' => $manufacturers['manufacturers_id'],
                                     'text' => $manufacturers['manufacturers_name']);
    }

    # get selected categories and special price
    $categories_array_selected = array(array('id' => ''));
    if (isset($HTTP_GET_VARS['pID'])) {
      $categories_query_selected = tep_db_query("select categories_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . $HTTP_GET_VARS['pID'] . "'");
      while ($categories = tep_db_fetch_array($categories_query_selected)) {
        $categories_array_selected[] = array('id' => $categories['categories_id']);
      }
    } elseif ($current_category_id != $pi_category_id) $categories_array_selected[] = array('id' => $current_category_id);  // added by splautz for product inventory

//    $categories_array = array(array('id' => '', 'text' => TEXT_NONE));
    #Categories list displays only for one languge (Deafault is English)
//    $language_id = 1;
    $categories_array = tep_get_category_tree(0,'',1);

    $tax_class_array = array(array('id' => '0', 'text' => TEXT_NONE));
    $tax_class_query = tep_db_query("select tax_class_id, tax_class_title from " . TABLE_TAX_CLASS . " order by tax_class_title");
    while ($tax_class = tep_db_fetch_array($tax_class_query)) {
      $tax_class_array[] = array('id' => $tax_class['tax_class_id'],
                                 'text' => $tax_class['tax_class_title']);
    }

    if (!isset($pInfo->products_status)) $pInfo->products_status = '1';
    switch ($pInfo->products_status) {
      case '0': $in_status = false; $out_status = true; break;
      case '1':
      default: $in_status = true; $out_status = false;
    }
?>
<link rel="stylesheet" type="text/css" href="includes/javascript/spiffyCal/spiffyCal_v2_1.css">
<script language="JavaScript" src="includes/javascript/spiffyCal/spiffyCal_v2_1.js"></script>
<script language="javascript"><!--
  var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "new_product", "products_date_available","btnDate1","<?php echo $pInfo->products_date_available; ?>",scBTNMODE_CUSTOMBLUE);
  var xdateSpecial = new ctlSpiffyCalendarBox("xdateSpecial", "new_product", "specials_xdate","btnDate2","<?php echo $pInfo->specials_xdate; ?>",scBTNMODE_CUSTOMBLUE);
//--></script>
<script language="javascript"><!--
var tax_rates = new Array();
<?php
    for ($i=0, $n=sizeof($tax_class_array); $i<$n; $i++) {
      if ($tax_class_array[$i]['id'] > 0) {
        echo 'tax_rates["' . $tax_class_array[$i]['id'] . '"] = ' . tep_get_tax_rate_value($tax_class_array[$i]['id']) . ';' . "\n";
      }
    }
?>

function doRound(x, places) {
  return Math.round(x * Math.pow(10, places)) / Math.pow(10, places);
}

function getTaxRate() {
  var selected_value = document.forms["new_product"].products_tax_class_id.selectedIndex;
  var parameterVal = document.forms["new_product"].products_tax_class_id[selected_value].value;

  if ( (parameterVal > 0) && (tax_rates[parameterVal] > 0) ) {
    return tax_rates[parameterVal];
  } else {
    return 0;
  }
}

function updateGross(pricetype) {
  if (pricetype) {
    var taxRate = getTaxRate();
    var grossValue = document.forms["new_product"][pricetype+"_price"].value;

    if (grossValue) {
      if (taxRate > 0) {
        grossValue = grossValue * ((taxRate / 100) + 1);
      }
      document.forms["new_product"][pricetype+"_price_gross"].value = doRound(grossValue, 4);
      if (pricetype == 'specials' && document.forms["new_product"].specials_status.value == '') {
        document.forms["new_product"].specials_status.value = 1;
      }
    } else {
      document.forms["new_product"][pricetype+"_price_gross"].value = '';
      if (pricetype == 'specials') document.forms["new_product"].specials_status.value = '';
    }
  } else {
    updateGross('products');
    updateGross('specials');
  }
}

function updateNet(pricetype) {
  var taxRate = getTaxRate();
  var netValue = document.forms["new_product"][pricetype+"_price_gross"].value;

  if (netValue) {
    if (taxRate > 0) {
      netValue = netValue / ((taxRate / 100) + 1);
    }
    document.forms["new_product"][pricetype+"_price"].value = doRound(netValue, 4);
    if (pricetype == 'specials' && document.forms["new_product"].specials_status.value == '') {
      document.forms["new_product"].specials_status.value = 1;
    }
  } else {
    document.forms["new_product"][pricetype+"_price"].value = '';
    if (pricetype == 'specials') document.forms["new_product"].specials_status.value = '';
  }
}
//--></script>
    <?php echo tep_draw_form('new_product', FILENAME_CATEGORIES, $urlpage . (isset($HTTP_GET_VARS['pID']) ? '&pID=' . $HTTP_GET_VARS['pID'] : '') . '&action=new_product_preview', 'post', 'enctype="multipart/form-data"'); ?>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo sprintf(TEXT_NEW_PRODUCT, tep_output_generated_category_path($current_category_id)); ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" cellspacing="0" cellpadding="2">
<?php
    if ($current_category_id != $pi_category_id) {
?>
          <tr>
            <td class="main"><?php tep_echo_help('TEXT_PRODUCTS_SORT_ORDER'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('sort_order', (isset($pInfo->sort_order) ? $pInfo->sort_order : ''), 'size="4" maxlength="4"'); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php tep_echo_help('TEXT_PRODUCTS_SPECIALS'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_specials', ($pInfo->products_specials), 'size="2"') . '&nbsp;1=Enabled 0=Disabled'; ?></td>
          </tr>
		  <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php
    }
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td class="main"><?php if ($i == 0) tep_echo_help('TEXT_PRODUCTS_NAME'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_input_field('products_name[' . $languages[$i]['id'] . ']', $value=(isset($products_name[$languages[$i]['id']]) ? $products_name[$languages[$i]['id']] : tep_get_products_name($pInfo->products_id, $languages[$i]['id'])), 'onKeyUp="CountInput(this);"');
                echo "<br>".spellcount_link('new_product','products_name[' . $languages[$i]['id'] . ']',strlen($value)); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
    }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php tep_echo_help('TEXT_PRODUCTS_MODEL'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_model', $pInfo->products_model); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php tep_echo_help('TEXT_PRODUCTS_MANUFACTURER'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_pull_down_menu('manufacturers_id', $manufacturers_array, $pInfo->manufacturers_id); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php tep_echo_help('TEXT_CATEGORIES'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_mselect_menu('categories_ids[]', $categories_array, $categories_array_selected, 'size=10'); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr bgcolor="#ebebff">
            <td class="main"><?php tep_echo_help('TEXT_PRODUCTS_TAX_CLASS'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_pull_down_menu('products_tax_class_id', $tax_class_array, $pInfo->products_tax_class_id, 'onchange="updateGross()"'); ?></td>
          </tr>
          <tr bgcolor="#ebebff">
            <td class="main"><?php tep_echo_help('TEXT_PRODUCTS_PRICE_NET'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_price', $pInfo->products_price, "onKeyUp=\"updateGross('products')\""); ?></td>
          </tr>
          <tr bgcolor="#ebebff">
            <td class="main"><?php tep_echo_help('TEXT_PRODUCTS_PRICE_GROSS'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_price_gross', $pInfo->products_price, "onKeyUp=\"updateNet('products')\""); ?></td>
          </tr>
          <tr bgcolor="#ebebff">
            <td class="main"><?php tep_echo_help('TEXT_PRODUCTS_SPECIAL_STATUS'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('specials_status', (isset($pInfo->specials_status) ? $pInfo->specials_status : ''), 'size="2"') . '&nbsp;1=Enabled 0=Disabled'; ?></td>
          </tr>
          <tr bgcolor="#ebebff">
            <td class="main"><?php tep_echo_help('TEXT_PRODUCTS_SPECIAL_PRICE_NET'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('specials_price', $pInfo->specials_price, "onKeyUp=\"updateGross('specials')\""); ?></td>
          </tr>
          <tr bgcolor="#ebebff">
            <td class="main"><?php tep_echo_help('TEXT_PRODUCTS_SPECIAL_PRICE_GROSS'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('specials_price_gross', $pInfo->specials_price_gross, "onKeyUp=\"updateNet('specials')\""); ?></td>
          </tr>
          <tr bgcolor="#ebebff">
            <td class="main"><?php tep_echo_help('TEXT_PRODUCTS_SPECIALS_XDATE'); ?><br><small>(YYYY-MM-DD)</small></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;'; ?><script language="javascript">xdateSpecial.writeControl(); xdateSpecial.dateFormat="yyyy-MM-dd";</script></td>
          </tr>
<script language="javascript"><!--
updateGross();
//--></script>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php tep_echo_help('TEXT_PRODUCTS_STATUS'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_radio_field('products_status', '1', $in_status) . '&nbsp;' . TEXT_PRODUCT_AVAILABLE . '&nbsp;' . tep_draw_radio_field('products_status', '0', $out_status) . '&nbsp;' . TEXT_PRODUCT_NOT_AVAILABLE; ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php tep_echo_help('TEXT_PRODUCTS_DATE_AVAILABLE'); ?><br><small>(YYYY-MM-DD)</small></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;'; ?><script language="javascript">dateAvailable.writeControl(); dateAvailable.dateFormat="yyyy-MM-dd";</script></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php tep_echo_help('TEXT_PRODUCTS_QUANTITY'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_quantity', $pInfo->products_quantity); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php tep_echo_help('TEXT_PRODUCTS_WEIGHT'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_weight', $pInfo->products_weight); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php tep_echo_help('TEXT_PRODUCTS_FREE_SHIPPING'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_free_shipping', ($pInfo->products_free_shipping), 'size="2"') . '&nbsp;1=Enabled 0=Disabled'; ?></td>
          </tr>
          <tr>
            <td class="main"><?php tep_echo_help('TEXT_PRODUCTS_ORIGIN_POSTCODE'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_origin_postcode', $pInfo->products_origin_postcode); ?></td>
          </tr>
<?php
// added by splautz for freight shipping
    if (defined('MODULE_ORDER_TOTAL_FREIGHT_STATUS')) {
?>
          <tr>
            <td class="main"><?php tep_echo_help('TEXT_PRODUCTS_FREIGHT_CLASS'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_freight_class', $pInfo->products_freight_class); ?></td>
          </tr>
<?php
    }
// added by splautz for Sun standard weight class
    if (defined('MODULE_SHIPPING_YELLOW_SUN_ORIG') && MODULE_SHIPPING_YELLOW_SUN_ORIG == 'True') {
      $sclasses[] = array('id' => '', 'text' => TEXT_NONE);
      $sun_class_query = tep_db_query("select id, name, standard_weight from " . TABLE_SUN_CLASS . " order by id");
      while ($sun_class = tep_db_fetch_array($sun_class_query)) {
        $sclasses[] = array('id' => $sun_class['id'], 'text' => $sun_class['name']);
      }
      if (count($sclasses) > 1) {
?>
          <tr>
            <td class="main"><?php tep_echo_help('TEXT_PRODUCTS_SUN_CLASS'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_pull_down_menu('products_sun_class', $sclasses, $pInfo->products_sun_class?$pInfo->products_sun_class:''); ?></td>
          </tr>
<?php
      }
    }
// START: Extra Fields Contribution (chapter 1.4)
      // Sort language by ID  
	  for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
	    $languages_array[$languages[$i]['id']]=$languages[$i];
	  }
      $extra_fields_query = tep_db_query("SELECT * FROM " . TABLE_PRODUCTS_EXTRA_FIELDS . " ORDER BY products_extra_fields_order");
      while ($extra_fields = tep_db_fetch_array($extra_fields_query)) {
	  // Display language icon or blank space
        if ($extra_fields['languages_id']==0) {
	      $m=tep_draw_separator('pixel_trans.gif', '24', '15');
	    } else $m= tep_image(DIR_WS_CATALOG_LANGUAGES . $languages_array[$extra_fields['languages_id']]['directory'] . '/images/' . $languages_array[$extra_fields['languages_id']]['image'], $languages_array[$extra_fields['languages_id']]['name']);
?>
          <tr bgcolor="#ebebff">
            <td class="main"><?php echo $extra_fields['products_extra_fields_name']; ?>:</td>
            <td class="main"><?php echo $m . '&nbsp;' . tep_draw_input_field("extra_field[".$extra_fields['products_extra_fields_id']."]", $pInfo->extra_field[$extra_fields['products_extra_fields_id']]); ?></td>
          </tr>
<?php
}
// END: Extra Fields Contribution
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php tep_echo_help('TEXT_PRODUCTS_IMAGE'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
		        <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15'); ?>&nbsp;</td>
                <td class="main">
                <?php if (!$wysiwyg || HTML_AREA_WYSIWYG_DISABLE_IMAGE == 'Disable') {
                  echo tep_draw_file_field('products_image');
                  if (isset($pInfo->products_image)) echo '<br>' . $pInfo->products_image;
                  echo tep_draw_hidden_field('products_previous_image', $pInfo->products_image);
                } else echo tep_draw_textarea_field('products_image', 'soft', '30', '1', $pInfo->products_image); ?>
                </td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('TEXT_PRODUCTS_IMG_ALT'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
				<td class="main"><?php echo tep_draw_textarea_field('products_img_alt[' . $languages[$i]['id'] . ']', 'soft', '70', '2', $value=(isset($products_img_alt[$languages[$i]['id']]) ? $products_img_alt[$languages[$i]['id']] : tep_get_products_img_alt($pInfo->products_id, $languages[$i]['id'])), 'onKeyUp="CountInput(this);"');
                echo "<br>".spellcount_link('new_product','products_img_alt[' . $languages[$i]['id'] . ']',strlen($value)); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
    }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('TEXT_PRODUCTS_INFO'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_textarea_field('products_info[' . $languages[$i]['id'] . ']', 'soft', '70', '2', (isset($products_info[$languages[$i]['id']]) ? $products_info[$languages[$i]['id']] : tep_get_products_info($pInfo->products_id, $languages[$i]['id'])));
                echo "<br>".spellcount_link('new_product','products_info[' . $languages[$i]['id'] . ']'); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
    }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<!-- HTC BOC //-->
<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('TEXT_PRODUCTS_DESCRIPTION'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_textarea_field('products_description[' . $languages[$i]['id'] . ']', 'soft', '70', '15', (isset($products_description[$languages[$i]['id']]) ? $products_description[$languages[$i]['id']] : tep_get_products_description($pInfo->products_id, $languages[$i]['id'])));
                echo "<br>".spellcount_link('new_product','products_description[' . $languages[$i]['id'] . ']'); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
    }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php
    if (false) {
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {  // blocked by splautz since not needed
?>
          <tr>
            <td class="main"><?php if ($i == 0) { tep_echo_help('TEXT_PRODUCTS_URL'); echo '<br><small>' . TEXT_PRODUCTS_URL_WITHOUT_HTTP . '</small>'; } ?></td>
            <td class="main"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('products_url[' . $languages[$i]['id'] . ']', (isset($products_url[$languages[$i]['id']]) ? $products_url[$languages[$i]['id']] : tep_get_products_url($pInfo->products_id, $languages[$i]['id']))); ?></td>
          </tr>
<?php
      }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php
    }
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td class="main"><?php if ($i == 0) { tep_echo_help('TEXT_PRODUCTS_AFFILIATE_URL'); echo '<br><small>' . TEXT_PRODUCTS_URL_WITHOUT_HTTP . '</small>'; } ?></td>
            <td class="main"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('products_affiliate_url[' . $languages[$i]['id'] . ']', (isset($products_affiliate_url[$languages[$i]['id']]) ? $products_affiliate_url[$languages[$i]['id']] : tep_get_products_affiliate_url($pInfo->products_id, $languages[$i]['id']))); ?></td>
          </tr>
<?php
    }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td colspan="2" class="main"><hr><?php echo TEXT_PRODUCT_METTA_INFO; ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr> 
<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('TEXT_PRODUCTS_NAME_URL'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
				<td class="main"><?php echo tep_draw_input_field('products_surls_name[' . $languages[$i]['id'] . ']', $value=(isset($products_surls_name[$languages[$i]['id']]) ? $products_surls_name[$languages[$i]['id']] : tep_get_products_surls_name($pInfo->products_id, $languages[$i]['id'])), 'size="70" onKeyUp="CountInput(this);"');
                echo "<br>".spellcount_link('new_product','products_surls_name[' . $languages[$i]['id'] . ']',strlen($value));
				echo tep_draw_hidden_field('products_surls_id[' . $languages[$i]['id'] . ']', htmlspecialchars($products_surls_id[$languages[$i]['id']])); ?></td>
              </tr>
            </table></td>
          </tr>
<?php          
    }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('TEXT_PRODUCTS_HEAD_TITLE'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_input_field('products_head_title_tag[' . $languages[$i]['id'] . ']', $value=(isset($products_head_title_tag[$languages[$i]['id']]) ? $products_head_title_tag[$languages[$i]['id']] : tep_get_products_head_title_tag($pInfo->products_id, $languages[$i]['id'])), 'size="70" onKeyUp="CountInput(this);"');
                echo "<br>".spellcount_link('new_product','products_head_title_tag[' . $languages[$i]['id'] . ']',strlen($value)); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
    }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('TEXT_PRODUCTS_HEAD_DESCRIPTION'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_textarea_field('products_head_desc_tag[' . $languages[$i]['id'] . ']', 'soft', '70', '5', $value=(isset($products_head_desc_tag[$languages[$i]['id']]) ? $products_head_desc_tag[$languages[$i]['id']] : tep_get_products_head_desc_tag($pInfo->products_id, $languages[$i]['id'])), 'onKeyUp="CountInput(this);"');
                echo "<br>".spellcount_link('new_product','products_head_desc_tag[' . $languages[$i]['id'] . ']',strlen($value)); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
    }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('TEXT_PRODUCTS_HEAD_KEYWORDS'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_textarea_field('products_head_keywords_tag[' . $languages[$i]['id'] . ']', 'soft', '70', '2', $value=(isset($products_head_keywords_tag[$languages[$i]['id']]) ? $products_head_keywords_tag[$languages[$i]['id']] : tep_get_products_head_keywords_tag($pInfo->products_id, $languages[$i]['id'])), 'onKeyUp="CountInput(this);"');
                echo "<br>".spellcount_link('new_product','products_head_keywords_tag[' . $languages[$i]['id'] . ']',strlen($value)); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
    }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('TEXT_PRODUCTS_H1'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_textarea_field('products_h1[' . $languages[$i]['id'] . ']', 'soft', '70', '2', $value=(isset($products_h1[$languages[$i]['id']]) ? $products_h1[$languages[$i]['id']] : tep_get_products_h1($pInfo->products_id, $languages[$i]['id'])), 'onKeyUp="CountInput(this);"');
                echo "<br>".spellcount_link('new_product','products_h1[' . $languages[$i]['id'] . ']',strlen($value)); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
    }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td colspan="2" class="main"><hr></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td class="main" align="center"><?php echo tep_draw_hidden_field('products_date_added', (tep_not_null($pInfo->products_date_added) ? $pInfo->products_date_added : date('Y-m-d'))) . tep_image_submit('button_preview.gif', IMAGE_PREVIEW) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_CATEGORIES, $urlpage . (isset($HTTP_GET_VARS['pID']) ? '&pID=' . $HTTP_GET_VARS['pID'] : '')) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>'; ?></td>
      </tr>
<!-- HTC EOC //-->
    </table></form>
<?php
//MaxiDVD Added WYSIWYG HTML Area Box + Admin Function v1.7 - 2.2 MS2 Products Description HTML - </form>
   if ($wysiwyg && (HTML_AREA_WYSIWYG_DISABLE != 'Disable' || HTML_AREA_WYSIWYG_DISABLE_IMAGE != 'Disable')) { ?>
            <script language="JavaScript1.2" defer>
             var config = new Object();  // create new config object
             config.debug = <?php echo HTML_AREA_WYSIWYG_DEBUG; ?>;
    <?php if (HTML_AREA_WYSIWYG_DISABLE != 'Disable') { ?>
             config.width = "<?php echo HTML_AREA_WYSIWYG_WIDTH; ?>px";
             config.height = "<?php echo HTML_AREA_WYSIWYG_HEIGHT; ?>px";
             config.bodyStyle = 'background-color: <?php echo HTML_AREA_WYSIWYG_BG_COLOUR; ?>; font-family: "<?php echo HTML_AREA_WYSIWYG_FONT_TYPE; ?>"; color: <?php echo HTML_AREA_WYSIWYG_FONT_COLOUR; ?>; font-size: <?php echo HTML_AREA_WYSIWYG_FONT_SIZE; ?>pt;';
             config.stylesheet = '<?php echo HTTP_CATALOG_SERVER . DIR_WS_CATALOG . 'stylesheet.css'; ?>';
          <?php for ($i = 0, $n = sizeof($languages); $i < $n; $i++) { ?>
             editor_generate('products_description[<?php echo $languages[$i]['id']; ?>]',config);
             editor_generate('products_info[<?php echo $languages[$i]['id']; ?>]',config);
          <?php }
          }
          if (HTML_AREA_WYSIWYG_DISABLE_IMAGE != 'Disable') { ?>
             config.width  = "<?php echo IMAGE_EDITOR_WYSIWYG_WIDTH; ?>px";
             config.height = "<?php echo IMAGE_EDITOR_WYSIWYG_HEIGHT; ?>px";
             config.bodyStyle = 'background-color: white; font-family: Arial; color: black; font-size: 12px;';
             config.stylesheet = null;
             config.toolbar = [ ["InsertImageURL"] ];
             config.OscImageRoot = '<?php echo trim(HTTP_SERVER . DIR_WS_CATALOG_IMAGES); ?>';
             editor_generate('products_image',config);
    <?php } ?>
            </script>
<?php } ?>
<!-- HTC BOC //-->     
<?php
  } elseif ($action == 'new_product_preview') {
    $languages = tep_get_languages();

    if ((isset($HTTP_GET_VARS['read']) && ($HTTP_GET_VARS['read'] == 'only')) || !tep_not_null($HTTP_POST_VARS)) {
      $products_id = tep_db_prepare_input($HTTP_GET_VARS['pID']);
      $product_query = tep_db_query("select p.products_id, p.products_quantity, p.products_model, p.products_image, p.products_price, p.products_weight, p.products_free_shipping, p.products_specials, p.products_origin_postcode, p.products_freight_class, p.products_sun_class, p.products_date_added, p.products_last_modified, date_format(p.products_date_available, '%Y-%m-%d') as products_date_available, p.products_status, p.products_tax_class_id, p.manufacturers_id, s.status as specials_status, s.specials_new_products_price as specials_price, date_format(s.expires_date, '%Y-%m-%d') as specials_xdate, p2c.sort_order from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id left join " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c on p.products_id = p2c.products_id and p2c.categories_id = '" . $current_category_id . "' where p.products_id = '" . (int)$products_id . "'");
      $product = tep_db_fetch_array($product_query);
   //HTC EOC 
      $pInfo = new objectInfo($product);
      $products_image_name = $pInfo->products_image;
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        $product_query = tep_db_query("select pd.products_name, pd.products_img_alt, pd.products_info, pd.products_description, pd.products_head_title_tag, pd.products_head_desc_tag, pd.products_head_keywords_tag, pd.products_url, pd.products_affiliate_url, pd.products_surls_id, pd.products_h1 from " . TABLE_PRODUCTS_DESCRIPTION . " pd where pd.products_id = '" . $pInfo->products_id . "' and pd.language_id = '" . $languages[$i]['id'] . "'");
        $product = tep_db_fetch_array($product_query);
		$products_name[$languages[$i]['id']] = $product['products_name'];
		$products_img_alt[$languages[$i]['id']] = $product['products_img_alt'];
		$products_info[$languages[$i]['id']] = $product['products_info'];
        $products_description[$languages[$i]['id']] = $product['products_description'];
        $products_head_title_tag[$languages[$i]['id']] = $product['products_head_title_tag'];
        $products_head_desc_tag[$languages[$i]['id']] = $product['products_head_desc_tag'];
        $products_head_keywords_tag[$languages[$i]['id']] = $product['products_head_keywords_tag'];
        $products_url[$languages[$i]['id']] = $product['products_url'];
        $products_affiliate_url[$languages[$i]['id']] = $product['products_affiliate_url'];
        $products_surls_id[$languages[$i]['id']] = $product['products_surls_id'];
        $products_surls_name[$languages[$i]['id']] = tep_get_products_surls_name($pInfo->products_id, $languages[$i]['id']);
        $products_h1[$languages[$i]['id']] = $product['products_h1'];
      }
 // HTC EOC
    } else {
      $http_post = tep_db_prepare_input($HTTP_POST_VARS);
      $pInfo = new objectInfo($http_post);
      $products_name = $http_post['products_name'];
      $products_img_alt = $http_post['products_img_alt'];
      $products_info = $http_post['products_info'];
      $products_description = $http_post['products_description'];
      $products_head_title_tag = $http_post['products_head_title_tag'];
      $products_head_desc_tag = $http_post['products_head_desc_tag'];
      $products_head_keywords_tag = $http_post['products_head_keywords_tag'];
      $products_url = $http_post['products_url'];
      $products_affiliate_url = $http_post['products_affiliate_url'];
      $products_surls_id = $http_post['products_surls_id'];
      $products_surls_name = $http_post['products_surls_name'];
      $products_h1 = $http_post['products_h1'];
    }

    $form_action = (isset($HTTP_GET_VARS['pID'])) ? 'update_product' : 'insert_product';

    echo tep_draw_form($form_action, FILENAME_CATEGORIES, $urlpage . (isset($HTTP_GET_VARS['pID']) ? '&pID=' . $HTTP_GET_VARS['pID'] : '') . '&action=' . $form_action, 'post', 'enctype="multipart/form-data"');

	$selected_catids = $HTTP_POST_VARS['categories_ids'];
	if ($selected_catids){
		//create the sql statement
			$product_categories_query= "SELECT categories_id as id, categories_name as text FROM categories_description WHERE ("; 
			$selected_catids_size = count($selected_catids);
			foreach ($selected_catids as $current_category_id)
			{
			$product_categories_query .= "categories_id=".$current_category_id;
			$selected_catids_size--;
			if ($selected_catids_size)
				$product_categories_query .= " or ";
			}
			$product_categories_query .= " ) and language_id=".$languages_id;
		// execute the sql statement
			$product_categories_query_result = tep_db_query($product_categories_query);
	
		$categories_array = array(array('id' => '', 'text' => TEXT_NONE));
		$count=0;
		while ($product_categories = tep_db_fetch_array($product_categories_query_result)){
			$categories_array[$count]['id'] = $product_categories["id"];
			$categories_array[$count]['text'] = $product_categories["text"];
			$count++;
		}
		$selected_catids_size = count($selected_catids);
	}

// added by splautz to build price display
    $price = $currencies->display_price($pInfo->products_price, tep_get_tax_rate($pInfo->products_tax_class_id));
    if (isset($pInfo->specials_price) && $pInfo->specials_price !== '') {
      if (substr($pInfo->specials_price, -1) == '%') {
        $pInfo->specials_price = ($pInfo->products_price - (($pInfo->specials_price / 100) * $pInfo->products_price));
        $HTTP_POST_VARS['specials_price'] = $pInfo->specials_price;
      }
      if ($pInfo->specials_status && $pInfo->specials_price < $pInfo->products_price) $price = '<s>' . $price . '</s>&nbsp;<span class="specialPrice">' . $currencies->display_price($pInfo->specials_price, tep_get_tax_rate($pInfo->products_tax_class_id)) . '</span>';
    }
	?>
	<table border="0" width="100%" cellspacing="0" cellpadding="2">
	<tr>
		<td class="pageHeading">
	<?php if ($selected_catids) { 
		print(TEXT_CATEGORIES."<br>");
		print(tep_draw_mselect_menu('categories_ids[]', $categories_array, $categories_array, 'size='.$selected_catids_size));
	} ?>
		</td>
	</tr>
	</table>
	<?php
    // HTC BOC         
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        $pInfo->products_name = $products_name[$languages[$i]['id']];
        $pInfo->products_img_alt = $products_img_alt[$languages[$i]['id']];
        $pInfo->products_info = $products_info[$languages[$i]['id']];
        $pInfo->products_description = $products_description[$languages[$i]['id']];
        $pInfo->products_head_title_tag = $products_head_title_tag[$languages[$i]['id']];
        $pInfo->products_head_desc_tag = $products_head_desc_tag[$languages[$i]['id']];
        $pInfo->products_head_keywords_tag = $products_head_keywords_tag[$languages[$i]['id']];
        $pInfo->products_url = $products_url[$languages[$i]['id']];
        $pInfo->products_affiliate_url = $products_affiliate_url[$languages[$i]['id']];
        $pInfo->products_surls_id = $products_surls_id[$languages[$i]['id']];
        $pInfo->products_surls_name = $products_surls_name[$languages[$i]['id']];
        $pInfo->products_h1 = $products_h1[$languages[$i]['id']];
    // HTC EOC
	?>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . $pInfo->products_name; ?></td>
            <td class="pageHeading" align="right"><?php echo $price; ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td class="main"><?php echo tep_image(DIR_WS_CATALOG_IMAGES . $products_image_name, $pInfo->products_img_alt?$pInfo->products_img_alt:$pInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'align="right" hspace="5" vspace="5"');
		// START: Extra Fields Contribution (chapter 1.5)
          if ($HTTP_GET_VARS['read'] == 'only') {
            $products_extra_fields_query = tep_db_query("SELECT * FROM " . TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS . " WHERE products_id=" . (int)$HTTP_GET_VARS['pID']);
            while ($products_extra_fields = tep_db_fetch_array($products_extra_fields_query)) {
              $extra_fields_array[$products_extra_fields['products_extra_fields_id']] = $products_extra_fields['products_extra_fields_value'];
            }
          }
          else {
            $extra_fields_array = $HTTP_POST_VARS['extra_field'];
          }

          $extra_fields_names_query = tep_db_query("SELECT * FROM " . TABLE_PRODUCTS_EXTRA_FIELDS. " WHERE languages_id='0' or languages_id='".(int)$languages[$i]['id']."' ORDER BY products_extra_fields_order");
          while ($extra_fields_names = tep_db_fetch_array($extra_fields_names_query)) {
            $extra_field_name[$extra_fields_names['products_extra_fields_id']] = $extra_fields_names['products_extra_fields_name'];
			echo '<B>'.$extra_fields_names['products_extra_fields_name'].':</B>&nbsp;'.stripslashes($extra_fields_array[$extra_fields_names['products_extra_fields_id']]).'<BR>'."\n";
          }		  
        // END: Extra Fields Contribution
          echo "<br />" . $pInfo->products_description;
         ?>
       </td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
          <tr>
            <td class="main"><b><u><?php echo sprintf(TEXT_PRODUCTS_INFO); ?></u></b></td>
            <td class="main" align="right"></td>
          </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td class="main"><?php echo $pInfo->products_info; ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
<?php
      if ($pInfo->products_url) {
?>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td class="main"><?php echo sprintf(TEXT_PRODUCT_MORE_INFORMATION, $pInfo->products_url); ?></td>
      </tr>
<?php
      }
?>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
<?php
      if ($pInfo->products_date_available > date('Y-m-d')) {
?>
      <tr>
        <td align="center" class="smallText"><?php echo sprintf(TEXT_PRODUCT_DATE_AVAILABLE, tep_date_long($pInfo->products_date_available)); ?></td>
      </tr>
<?php
      } else {
?>
      <tr>
        <td align="center" class="smallText"><?php echo sprintf(TEXT_PRODUCT_DATE_ADDED, tep_date_long($pInfo->products_date_added)); ?></td>
      </tr>
<?php
      }
?>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
<?php
    }

    if (isset($HTTP_GET_VARS['read']) && ($HTTP_GET_VARS['read'] == 'only')) {
      if (isset($HTTP_GET_VARS['origin'])) {
        $pos_params = strpos($HTTP_GET_VARS['origin'], '?', 0);
        if ($pos_params != false) {
          $back_url = substr($HTTP_GET_VARS['origin'], 0, $pos_params);
          $back_url_params = substr($HTTP_GET_VARS['origin'], $pos_params + 1);
        } else {
          $back_url = $HTTP_GET_VARS['origin'];
          $back_url_params = '';
        }
      } else {
        $back_url = FILENAME_CATEGORIES;
        $back_url_params = $urlpage . '&pID=' . $pInfo->products_id;
      }
?>
      <tr>
        <td align="right"><?php echo '<a href="' . tep_href_link($back_url, $back_url_params, 'NONSSL') . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td>
      </tr>
<?php
    } else {
?>
      <tr>
        <td align="right" class="smallText">
<?php
/* Re-Post all POST'ed variables */
      reset($HTTP_POST_VARS);
      while (list($key, $value) = each($HTTP_POST_VARS)) {
        if (!is_array($HTTP_POST_VARS[$key])) {
          echo tep_draw_hidden_field($key, htmlspecialchars(tep_db_prepare_input($value)));
        }
      }
      // HTC BOC
      $languages = tep_get_languages();
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        echo tep_draw_hidden_field('products_name[' . $languages[$i]['id'] . ']', htmlspecialchars($products_name[$languages[$i]['id']]));
        echo tep_draw_hidden_field('products_img_alt[' . $languages[$i]['id'] . ']', htmlspecialchars($products_img_alt[$languages[$i]['id']]));
        echo tep_draw_hidden_field('products_info[' . $languages[$i]['id'] . ']', htmlspecialchars($products_info[$languages[$i]['id']]));
        echo tep_draw_hidden_field('products_description[' . $languages[$i]['id'] . ']', htmlspecialchars($products_description[$languages[$i]['id']]));
        echo tep_draw_hidden_field('products_head_title_tag[' . $languages[$i]['id'] . ']', htmlspecialchars($products_head_title_tag[$languages[$i]['id']]));
        echo tep_draw_hidden_field('products_head_desc_tag[' . $languages[$i]['id'] . ']', htmlspecialchars($products_head_desc_tag[$languages[$i]['id']]));
        echo tep_draw_hidden_field('products_head_keywords_tag[' . $languages[$i]['id'] . ']', htmlspecialchars($products_head_keywords_tag[$languages[$i]['id']]));
        echo tep_draw_hidden_field('products_url[' . $languages[$i]['id'] . ']', htmlspecialchars($products_url[$languages[$i]['id']]));
        echo tep_draw_hidden_field('products_affiliate_url[' . $languages[$i]['id'] . ']', htmlspecialchars($products_affiliate_url[$languages[$i]['id']]));
        echo tep_draw_hidden_field('products_surls_id[' . $languages[$i]['id'] . ']', htmlspecialchars($products_surls_id[$languages[$i]['id']]));
        echo tep_draw_hidden_field('products_surls_name[' . $languages[$i]['id'] . ']', htmlspecialchars($products_surls_name[$languages[$i]['id']]));
        echo tep_draw_hidden_field('products_h1[' . $languages[$i]['id'] . ']', htmlspecialchars($products_h1[$languages[$i]['id']]));
      }       
      // HTC EOC
      // START: Extra Fields Contribution
      if ($HTTP_POST_VARS['extra_field']) { // Check to see if there are any need to update extra fields.
        foreach ($HTTP_POST_VARS['extra_field'] as $key=>$val) {
          echo tep_draw_hidden_field('extra_field['.$key.']', stripslashes($val));
        }
      } // Check to see if there are any need to update extra fields.
      // END: Extra Fields Contribution

      echo tep_draw_hidden_field('products_image', $products_image_name);

      echo tep_image_submit('button_back.gif', IMAGE_BACK, 'name="edit"') . '&nbsp;&nbsp;';

      if (isset($HTTP_GET_VARS['pID'])) {
        echo tep_image_submit('button_update.gif', IMAGE_UPDATE);
      } else {
        echo tep_image_submit('button_insert.gif', IMAGE_INSERT);
      }
      echo '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_CATEGORIES, $urlpage . (isset($HTTP_GET_VARS['pID']) ? '&pID=' . $HTTP_GET_VARS['pID'] : '')) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>';
?></td>
      </tr>
    </table></form>
<?php
    }
// begin new/edit category
  } elseif ($action == 'new_category' || $action == 'edit_category') {
    $languages = tep_get_languages();
    if ($action == 'edit_category' && isset($HTTP_GET_VARS['cID'])) {
      $categories_query = tep_db_query("select c.categories_id, c.categories_image, c.parent_id, c.sort_order, c.date_added, c.last_modified, c.categories_status, c.categories_dpids, c.categories_pranges from " . TABLE_CATEGORIES . " c where c.categories_id = '" . (int)$HTTP_GET_VARS['cID'] . "'");
      $categories = tep_db_fetch_array($categories_query);
      $cInfo = new objectInfo($categories);
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        $category_query = tep_db_query("select cd.categories_name, cd.categories_htc_title_tag, cd.categories_htc_desc_tag, cd.categories_htc_keywords_tag, cd.categories_h1, cd.categories_surls_id, cd.categories_htc_description, cd.categories_body, cd.categories_body2, cd.categories_img_alt from " . TABLE_CATEGORIES_DESCRIPTION . " cd where cd.categories_id = '" . (int)$cInfo->categories_id . "' and cd.language_id = '" . (int)$languages[$i]['id'] . "'");
        $category = tep_db_fetch_array($category_query);
        $categories_name[$languages[$i]['id']] = $category['categories_name'];
        $categories_htc_title_tag[$languages[$i]['id']] = $category['categories_htc_title_tag'];
        $categories_htc_desc_tag[$languages[$i]['id']] = $category['categories_htc_desc_tag'];
        $categories_htc_keywords_tag[$languages[$i]['id']] = $category['categories_htc_keywords_tag'];
        $categories_h1[$languages[$i]['id']] = $category['categories_h1'];
        $categories_surls_id[$languages[$i]['id']] = $category['categories_surls_id'];
        $categories_surls_name[$languages[$i]['id']] = tep_get_category_surls_name($cInfo->categories_id, $languages[$i]['id']);
        $categories_htc_description[$languages[$i]['id']] = $category['categories_htc_description'];
        $categories_body[$languages[$i]['id']] = $category['categories_body'];
        $categories_body2[$languages[$i]['id']] = $category['categories_body2'];
        $categories_img_alt[$languages[$i]['id']] = $category['categories_img_alt'];
      }
    } else $cInfo = NULL;
    if ($cInfo) echo tep_draw_form($formname='categories', FILENAME_CATEGORIES, 'action=update_category&' . $urlpage, 'post', 'enctype="multipart/form-data"') . tep_draw_hidden_field('categories_id', $cInfo->categories_id);
    else echo tep_draw_form($formname='newcategory', FILENAME_CATEGORIES, 'action=insert_category&' . $urlpage, 'post', 'enctype="multipart/form-data"');
?>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php if ($cInfo) echo TEXT_INFO_HEADING_EDIT_CATEGORY; else echo TEXT_INFO_HEADING_NEW_CATEGORY; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td><?php if ($cInfo) echo TEXT_EDIT_INTRO; else echo TEXT_NEW_CATEGORY_INTRO; ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" cellspacing="0" cellpadding="2">
<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td class="main"><?php if ($i == 0) tep_echo_help('TEXT_CATEGORIES_NAME'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_input_field('categories_name[' . $languages[$i]['id'] . ']', $value=(isset($categories_name[$languages[$i]['id']]) ? $categories_name[$languages[$i]['id']] : ''), 'onKeyUp="CountInput(this);"');
                echo "<br>".spellcount_link($formname,'categories_name[' . $languages[$i]['id'] . ']',strlen($value)); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
    }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php tep_echo_help('TEXT_CATEGORIES_SORT_ORDER'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('sort_order', (isset($cInfo->sort_order) ? $cInfo->sort_order : ''), 'size="3" maxlength="3"'); ?></td>
          </tr>
<?php
    if (!$cInfo || $cInfo->categories_id != $pi_category_id) {  // added by splautz for product inventory (remove extra category options)
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php tep_echo_help('TEXT_CATEGORIES_STATUS'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('categories_status', (isset($cInfo->categories_status) ? $cInfo->categories_status : '1'), 'size="2"') . '&nbsp;1=Enabled 0=Disabled'; ?></td>
          </tr>
<?php
      if ($cInfo) {
        $category_parent_query = tep_db_query("select count(*) as total from " . TABLE_CATEGORIES . " where parent_id = '" . (int)$cInfo->categories_id . "' and categories_status = '1'");
        $category_parent = tep_db_fetch_array($category_parent_query);
        if ($category_parent['total'] > 0) {
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php tep_echo_help('TEXT_CATEGORIES_DPIDS'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('categories_dpids', (isset($cInfo->categories_dpids) ? $cInfo->categories_dpids : '')); ?></td>
          </tr>
<?php
        } else {
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php tep_echo_help('TEXT_CATEGORIES_PRANGES'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('categories_pranges', (isset($cInfo->categories_pranges) ? $cInfo->categories_pranges : '')); ?></td>
          </tr>
<?php
        }
      }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php tep_echo_help('TEXT_CATEGORIES_IMAGE'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
		        <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15'); ?>&nbsp;</td>
                <td class="main">
                <?php if (!$wysiwyg || HTML_AREA_WYSIWYG_DISABLE_IMAGE == 'Disable') {
                  echo tep_draw_file_field('categories_image');
                  if (isset($cInfo->categories_image)) echo '<br>' . $cInfo->categories_image;
                } else echo tep_draw_textarea_field('categories_image', 'soft', '30', '1', $cInfo->categories_image); ?>
                </td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('TEXT_CATEGORIES_IMG_ALT'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_textarea_field('categories_img_alt[' . $languages[$i]['id'] . ']', 'soft', '70', '2', $value=(isset($categories_img_alt[$languages[$i]['id']]) ? $categories_img_alt[$languages[$i]['id']] : ''), 'onKeyUp="CountInput(this);"');
                echo "<br>".spellcount_link($formname,'categories_img_alt[' . $languages[$i]['id'] . ']',strlen($value)); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
      }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
         <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('TEXT_CATEGORIES_INTRO'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_textarea_field('categories_htc_description[' . $languages[$i]['id'] . ']', 'soft', '70', '15', (isset($categories_htc_description[$languages[$i]['id']]) ? $categories_htc_description[$languages[$i]['id']] : ''));
                echo "<br>".spellcount_link($formname,'categories_htc_description[' . $languages[$i]['id'] . ']'); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
      }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
         <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('TEXT_CATEGORIES_BODY'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_textarea_field('categories_body[' . $languages[$i]['id'] . ']', 'soft', '70', '15', (isset($categories_body[$languages[$i]['id']]) ? $categories_body[$languages[$i]['id']] : ''));
                echo "<br>".spellcount_link($formname,'categories_body[' . $languages[$i]['id'] . ']'); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
      }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
         <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('TEXT_CATEGORIES_BODY2'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_textarea_field('categories_body2[' . $languages[$i]['id'] . ']', 'soft', '70', '15', (isset($categories_body2[$languages[$i]['id']]) ? $categories_body2[$languages[$i]['id']] : ''));
                echo "<br>".spellcount_link($formname,'categories_body2[' . $languages[$i]['id'] . ']'); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
      }
?>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td colspan="2" class="main"><hr><?php echo TEXT_CATEGORIES_METTA_INFO; ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr> 
<?php
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('TEXT_CATEGORIES_NAME_URL'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
				<td class="main"><?php echo tep_draw_input_field('categories_surls_name[' . $languages[$i]['id'] . ']', $value=(isset($categories_surls_name[$languages[$i]['id']]) ? $categories_surls_name[$languages[$i]['id']] : ''), 'size="70" onKeyUp="CountInput(this);"');
                echo "<br>".spellcount_link($formname,'categories_surls_name[' . $languages[$i]['id'] . ']',strlen($value));
				echo tep_draw_hidden_field('categories_surls_id[' . $languages[$i]['id'] . ']', htmlspecialchars($categories_surls_id[$languages[$i]['id']])); ?></td>
              </tr>
            </table></td>
          </tr>
<?php          
      }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('TEXT_CATEGORIES_HEAD_TITLE'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_input_field('categories_htc_title_tag[' . $languages[$i]['id'] . ']', $value=(isset($categories_htc_title_tag[$languages[$i]['id']]) ? $categories_htc_title_tag[$languages[$i]['id']] : ''), 'size="70" onKeyUp="CountInput(this);"');
                echo "<br>".spellcount_link($formname,'categories_htc_title_tag[' . $languages[$i]['id'] . ']',strlen($value)); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
      }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('TEXT_CATEGORIES_HEAD_DESCRIPTION'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_textarea_field('categories_htc_desc_tag[' . $languages[$i]['id'] . ']', 'soft', '70', '5', $value=(isset($categories_htc_desc_tag[$languages[$i]['id']]) ? $categories_htc_desc_tag[$languages[$i]['id']] : ''), 'onKeyUp="CountInput(this);"');
                echo "<br>".spellcount_link($formname,'categories_htc_desc_tag[' . $languages[$i]['id'] . ']',strlen($value)); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
      }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('TEXT_CATEGORIES_HEAD_KEYWORDS'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_textarea_field('categories_htc_keywords_tag[' . $languages[$i]['id'] . ']', 'soft', '70', '2', $value=(isset($categories_htc_keywords_tag[$languages[$i]['id']]) ? $categories_htc_keywords_tag[$languages[$i]['id']] : ''), 'onKeyUp="CountInput(this);"');
                echo "<br>".spellcount_link($formname,'categories_htc_keywords_tag[' . $languages[$i]['id'] . ']',strlen($value)); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
      }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('TEXT_CATEGORIES_H1'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_textarea_field('categories_h1[' . $languages[$i]['id'] . ']', 'soft', '70', '2', $value=(isset($categories_h1[$languages[$i]['id']]) ? $categories_h1[$languages[$i]['id']] : ''), 'onKeyUp="CountInput(this);"');
                echo "<br>".spellcount_link($formname,'categories_h1[' . $languages[$i]['id'] . ']',strlen($value)); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
      }
    }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td colspan="2" class="main"><hr></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td class="main" align="center">
<?php
    if ($cInfo) echo tep_image_submit('button_save.gif', IMAGE_SAVE) . ' <a href="' . tep_href_link(FILENAME_CATEGORIES, $urlpage . '&cID=' . $cInfo->categories_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>';
    else echo tep_image_submit('button_save.gif', IMAGE_SAVE) . ' <a href="' . tep_href_link(FILENAME_CATEGORIES, $urlpage) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>'; ?>
        </td>
      </tr>
    </table></form>
<?php
//MaxiDVD Added WYSIWYG HTML Area Box + Admin Function v1.7 - 2.2 MS2 Products Description HTML - </form>
   if ($wysiwyg && (HTML_AREA_WYSIWYG_DISABLE != 'Disable' || HTML_AREA_WYSIWYG_DISABLE_IMAGE != 'Disable')) { ?>
            <script language="JavaScript1.2" defer>
             var config = new Object();  // create new config object
             config.debug = <?php echo HTML_AREA_WYSIWYG_DEBUG; ?>;
    <?php if (HTML_AREA_WYSIWYG_DISABLE != 'Disable') { ?>
             config.width = "<?php echo HTML_AREA_WYSIWYG_WIDTH; ?>px";
             config.height = "<?php echo HTML_AREA_WYSIWYG_HEIGHT; ?>px";
             config.bodyStyle = 'background-color: <?php echo HTML_AREA_WYSIWYG_BG_COLOUR; ?>; font-family: "<?php echo HTML_AREA_WYSIWYG_FONT_TYPE; ?>"; color: <?php echo HTML_AREA_WYSIWYG_FONT_COLOUR; ?>; font-size: <?php echo HTML_AREA_WYSIWYG_FONT_SIZE; ?>pt;';
             config.stylesheet = '<?php echo HTTP_CATALOG_SERVER . DIR_WS_CATALOG . 'stylesheet.css'; ?>';
          <?php for ($i = 0, $n = sizeof($languages); $i < $n; $i++) { ?>
             editor_generate('categories_htc_description[<?php echo $languages[$i]['id']; ?>]',config);
             editor_generate('categories_body[<?php echo $languages[$i]['id']; ?>]',config);
             editor_generate('categories_body2[<?php echo $languages[$i]['id']; ?>]',config);
          <?php }
          }
          if (HTML_AREA_WYSIWYG_DISABLE_IMAGE != 'Disable') { ?>
             config.width  = "<?php echo IMAGE_EDITOR_WYSIWYG_WIDTH; ?>px";
             config.height = "<?php echo IMAGE_EDITOR_WYSIWYG_HEIGHT; ?>px";
             config.bodyStyle = 'background-color: white; font-family: Arial; color: black; font-size: 12px;';
             config.stylesheet = null;
             config.toolbar = [ ["InsertImageURL"] ];
             config.OscImageRoot = '<?php echo trim(HTTP_SERVER . DIR_WS_CATALOG_IMAGES); ?>';
             editor_generate('categories_image',config);
    <?php } ?>
            </script>
<?php }
// end new/edit category
  } else {
?>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
            <td align="right"><table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td class="smallText" align="right">
<?php
    echo tep_draw_form('search', FILENAME_CATEGORIES, '', 'get');
    echo HEADING_TITLE_SEARCH . ' ' . tep_draw_input_field('search');
    echo '</form>';
?>
                </td>
              </tr>
              <tr>
                <td class="smallText" align="right">
<?php
    echo tep_draw_form('goto', FILENAME_CATEGORIES, '', 'get');
// added by splautz to default to blank on searches
    $cat_tree = tep_get_category_tree();
    if (isset($HTTP_GET_VARS['search'])) array_unshift($cat_tree,array('id'=>'','text'=>''));

    echo HEADING_TITLE_GOTO . ' ' . tep_draw_pull_down_menu('cPath', $cat_tree, $current_category_id, 'onChange="this.form.submit();"');
    echo '</form>';
?>
                </td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_ID; ?></td>
                <td class="dataTableHeadingContent" width="100%" align="left" colspan="2"><?php echo TABLE_HEADING_CATEGORIES_PRODUCTS; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_SORT; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_STATUS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
    $categories_count = 0;
    $rows = 0;
    if (isset($HTTP_GET_VARS['search'])) {
      $search = tep_db_prepare_input($HTTP_GET_VARS['search']);

      // HTC BOC
// #################### Added Categorie Enable / Disable ##################
//      $categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.parent_id, c.sort_order, c.date_added, c.last_modified from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' and cd.categories_name like '%" . tep_db_input($search) . "%' order by c.sort_order, cd.categories_name");
        $categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.parent_id, c.sort_order, c.date_added, c.last_modified, c.categories_dpids, c.categories_pranges, cd.categories_htc_title_tag, cd.categories_htc_desc_tag, cd.categories_htc_keywords_tag, cd.categories_h1, cd.categories_surls_id, cd.categories_htc_description, cd.categories_body, cd.categories_body2, cd.categories_img_alt, c.categories_status from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' and cd.categories_name like '%" . tep_db_input($search) . "%' order by cd.categories_name");
        $categories_count_query = tep_db_query("select count(c.categories_id) from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' and cd.categories_name like '%" . tep_db_input($search) . "%'");  // added by splautz for paging
// #################### End Added Categorie Enable / Disable ##################
    } else {
// #################### Added Categorie Enable / Disable ##################
//      $categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.parent_id, c.sort_order, c.date_added, c.last_modified from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.parent_id = '" . (int)$current_category_id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' order by c.sort_order, cd.categories_name");
      $categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.parent_id, c.sort_order, c.date_added, c.last_modified, c.categories_dpids, c.categories_pranges, cd.categories_htc_title_tag, cd.categories_htc_desc_tag, cd.categories_htc_keywords_tag, cd.categories_h1, cd.categories_surls_id, cd.categories_htc_description, cd.categories_body, cd.categories_body2, cd.categories_img_alt, c.categories_status from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.parent_id = '" . (int)$current_category_id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' order by COALESCE(c.sort_order,1000), cd.categories_name");
      $categories_count_query = tep_db_query("select count(c.categories_id) from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.parent_id = '" . (int)$current_category_id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "'");  // added by splautz for paging
// #################### End Added Categorie Enable / Disable ##################
    // HTC EOC

    }
// added by splautz for paging
    if ($categories = tep_db_fetch_array($categories_count_query)) $categories_count = array_shift($categories);
    if ($page < 2) {

    while ($categories = tep_db_fetch_array($categories_query)) {
// removed by splautz since count is obtained directly
//    $categories_count++;
      $rows++;

// Get parent_id for subcategories if search
// removed by splautz to maintain search page & prevent duplicates
//    if (isset($HTTP_GET_VARS['search'])) $cPath= $categories['parent_id'];

      if ((!isset($HTTP_GET_VARS['cID']) && !isset($HTTP_GET_VARS['pID']) || (isset($HTTP_GET_VARS['cID']) && ($HTTP_GET_VARS['cID'] == $categories['categories_id']))) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {
        $category_childs = array('childs_count' => tep_childs_in_category_count($categories['categories_id']));
        $category_products = array('products_count' => tep_products_in_category_count($categories['categories_id']));

        $cInfo_array = array_merge($categories, $category_childs, $category_products);
        $cInfo = new objectInfo($cInfo_array);
      }

      if (isset($cInfo) && is_object($cInfo) && ($categories['categories_id'] == $cInfo->categories_id) ) {
        echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_CATEGORIES, tep_get_path($categories['categories_id'])) . '\'">' . "\n";
      } else {
        echo '              <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_CATEGORIES, $urlpage . '&cID=' . $categories['categories_id']) . '\'">' . "\n";
      }
?>
                <td class="dataTableContent"><?php echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, tep_get_path($categories['categories_id'])) . '">' . tep_image(DIR_WS_ICONS . 'folder.gif', ICON_FOLDER) . '</a></td><td class="dataTableContent" align="center">&nbsp;&nbsp;' . ($categories['categories_id']==$pi_category_id?'':$categories['categories_id']) . '&nbsp;&nbsp;</td><td class="dataTableContent" align="left"><b>' . $categories['categories_name'] . '</b></td><td class="dataTableContent"></td><td class="dataTableContent" align="right">&nbsp;&nbsp;' . $categories['sort_order'] . '&nbsp;&nbsp;'; ?></td>
                <td class="dataTableContent" align="center">
<?php
      if ($categories['categories_id'] == $pi_category_id) {  // added by splautz for product inventory category (to remove status indicators)
        echo tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10);
      } elseif ($categories['categories_status'] == '1') {
        echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_CATEGORIES, 'action=setflag_cat&flag=0&cID=' . $categories['categories_id'] . '&' . $urlpage) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
      } else {
        echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'action=setflag_cat&flag=1&cID=' . $categories['categories_id'] . '&' . $urlpage) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
      }
?>
                </td>
                <td class="dataTableContent" align="right"><?php if (isset($cInfo) && is_object($cInfo) && ($categories['categories_id'] == $cInfo->categories_id) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, $urlpage . '&cID=' . $categories['categories_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    }
    }  // added by splautz for paging

    $products_count = 0;
    if (isset($HTTP_GET_VARS['search'])) {
// modified by splautz for product id & model search, to prevent duplicates, & for paging
//    $products_query = tep_db_query("select distinct p.products_id, p.products_model, pd.products_name, pd.products_img_alt, p.products_quantity, p.products_image, p.products_price, p.products_date_added, p.products_last_modified, p.products_date_available, p.products_status, p2c.categories_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and p.products_id = p2c.products_id and pd.products_name like '%" . tep_db_input($search) . "%' order by pd.products_name");
      $products_query_str = "select distinct p.products_id, p.products_model, pd.products_name, pd.products_img_alt, p.products_quantity, p.products_image, p.products_price, p.products_date_added, p.products_last_modified, p.products_date_available, p.products_status from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and (pd.products_name like '%" . tep_db_input($search) . "%' or p.products_model like '%" . tep_db_input($search) . "%' or p.products_id = '" . tep_db_input($search) . "') order by pd.products_name";
      $products_count_query = tep_db_query("select count(distinct p.products_id) from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and (pd.products_name like '%" . tep_db_input($search) . "%' or p.products_model like '%" . tep_db_input($search) . "%' or p.products_id = '" . tep_db_input($search) . "')");  // added by splautz for paging
    } elseif ($current_category_id == $pi_category_id) {  // added by splautz for product inventory category
// modified by splautz for paging
//    $products_query = tep_db_query("select p.products_id, p.products_model, pd.products_name, pd.products_img_alt, p.products_quantity, p.products_image, p.products_price, p.products_date_added, p.products_last_modified, p.products_date_available, p.products_status from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' order by pd.products_name");
      $products_query_str = "select p.products_id, p.products_model, pd.products_name, pd.products_img_alt, p.products_quantity, p.products_image, p.products_price, p.products_date_added, p.products_last_modified, p.products_date_available, p.products_status from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' order by pd.products_name";
      $products_count_query = tep_db_query("select count(p.products_id) from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "'");  // added by splautz for paging
    } else {
// modified by splautz for paging
//    $products_query = tep_db_query("select p.products_id, p.products_model, pd.products_name, pd.products_img_alt, p.products_quantity, p.products_image, p.products_price, p.products_date_added, p.products_last_modified, p.products_date_available, p.products_status, p2c.sort_order from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and p.products_id = p2c.products_id and p2c.categories_id = '" . (int)$current_category_id . "' order by COALESCE(p2c.sort_order,10000), pd.products_name");
      $products_query_str = "select p.products_id, p.products_model, pd.products_name, pd.products_img_alt, p.products_quantity, p.products_image, p.products_price, p.products_date_added, p.products_last_modified, p.products_date_available, p.products_status, p2c.sort_order from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and p.products_id = p2c.products_id and p2c.categories_id = '" . (int)$current_category_id . "' order by COALESCE(p2c.sort_order,10000), pd.products_name";
      $products_count_query = tep_db_query("select count(p.products_id) from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and p.products_id = p2c.products_id and p2c.categories_id = '" . (int)$current_category_id . "'");  // added by splautz for paging
    }
// added by splautz for paging
    $per_page = 50;
    if ($products = tep_db_fetch_array($products_count_query)) $products_count = array_shift($products);
    $num_pages = max(1,ceil($products_count/$per_page));
    if ($page > $num_pages) $page = $num_pages;
    $prev_page = $page - 1;
    $next_page = $page + 1;
    $page_start = ($page - 1) * $per_page;
    $products_query = tep_db_query("$products_query_str LIMIT $page_start, $per_page");

    while ($products = tep_db_fetch_array($products_query)) {
// removed by splautz since count is obtained directly
//    $products_count++;
      $rows++;

// Get categories_id for product if search
// removed by splautz to maintain search page & prevent duplicates
//    if (isset($HTTP_GET_VARS['search'])) $cPath = $products['categories_id'];

      if ( (!isset($HTTP_GET_VARS['pID']) && !isset($HTTP_GET_VARS['cID']) || (isset($HTTP_GET_VARS['pID']) && ($HTTP_GET_VARS['pID'] == $products['products_id']))) && !isset($pInfo) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {
// find out the rating average from customer reviews
        $reviews_query = tep_db_query("select (avg(reviews_rating) / 5 * 100) as average_rating from " . TABLE_REVIEWS . " where products_id = '" . (int)$products['products_id'] . "'");
        $reviews = tep_db_fetch_array($reviews_query);
        $pInfo_array = array_merge($products, $reviews);
        $pInfo = new objectInfo($pInfo_array);
      }

      if (isset($pInfo) && is_object($pInfo) && ($products['products_id'] == $pInfo->products_id) ) {
        echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_CATEGORIES, $urlpage . '&pID=' . $products['products_id'] . '&action=new_product_preview&read=only') . '\'">' . "\n";
      } else {
        echo '              <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_CATEGORIES, $urlpage . '&pID=' . $products['products_id']) . '\'">' . "\n";
      }
?>
                <td class="dataTableContent"><?php echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, $urlpage . '&pID=' . $products['products_id'] . '&action=new_product_preview&read=only') . '">' . tep_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW) . '</a></td><td class="dataTableContent" align="center">&nbsp;&nbsp;' . $products['products_id'] . '&nbsp;&nbsp;</td><td class="dataTableContent" align="left">' . $products['products_name'] . '</td><td class="dataTableContent">' . $products['products_model'] . '</td><td class="dataTableContent" align="right">&nbsp;&nbsp;' . $products['sort_order'] . '&nbsp;&nbsp;'; ?></td>
                <td class="dataTableContent" align="center">
<?php
      if ($products['products_status'] == '1') {
        echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_CATEGORIES, 'action=setflag&flag=0&pID=' . $products['products_id'] . '&' . $urlpage) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
      } else {
        echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'action=setflag&flag=1&pID=' . $products['products_id'] . '&' . $urlpage) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
      }
?></td>
                <td class="dataTableContent" align="right"><?php if (isset($pInfo) && is_object($pInfo) && ($products['products_id'] == $pInfo->products_id)) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, $urlpage . '&pID=' . $products['products_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    }

    $cPath_back = '';
    if (sizeof($cPath_array) > 0) {
      for ($i=0, $n=sizeof($cPath_array)-1; $i<$n; $i++) {
        if (empty($cPath_back)) {
          $cPath_back .= $cPath_array[$i];
        } else {
          $cPath_back .= '_' . $cPath_array[$i];
        }
      }
    }

    $cPath_back = (tep_not_null($cPath_back)) ? 'cPath=' . $cPath_back . '&' : '';
?>
              <tr>
                <td colspan="7" class="smallText">
<?php  // added by splautz for paging
  // Previous
    if ($prev_page) {
      echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, $urlp.'&page=' . $prev_page) . '"> &lt;&lt; </a> | ';
    }

    for ($i = 1; $i <= $num_pages; $i++) {
      if ($i != $page) {
        echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, $urlp.'&page=' . $i) . '">' . $i . '</a> | ';
      } else {
        echo '<b><font color="red">' . $i . '</font></b> | ';
      }
    }

  // Next
    if ($page != $num_pages) {
      echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, $urlp.'&page=' . $next_page) . '"> &gt;&gt; </a>';
    }
?>
                </td>
              </tr>
              <tr>
                <td colspan="7"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText"><?php echo TEXT_CATEGORIES . '&nbsp;' . $categories_count . '<br>' . TEXT_PRODUCTS . '&nbsp;' . $products_count; ?></td>
                    <td align="right" class="smallText"><?php if (sizeof($cPath_array) > 0 || isset($HTTP_GET_VARS['search'])) echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, $cPath_back . 'cID=' . $current_category_id) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>&nbsp;';
                      if (!isset($HTTP_GET_VARS['search'])) {
                        if ($current_category_id <> $pi_category_id) echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, $urlpage . '&action=new_category') . '">' . tep_image_button('button_new_category.gif', IMAGE_NEW_CATEGORY) . '</a>&nbsp;';
                        echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, $urlpage . '&action=new_product') . '">' . tep_image_button('button_new_product.gif', IMAGE_NEW_PRODUCT) . '</a>';
                      } ?>&nbsp;</td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
<?php
    $heading = array();
    $contents = array();
    switch ($action) {
/* code moved by splautz to put on separate page
      case 'new_category':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_CATEGORY . '</b>');

        $contents = array('form' => tep_draw_form('newcategory', FILENAME_CATEGORIES, 'action=insert_category&cPath=' . $cPath, 'post', 'enctype="multipart/form-data"'));
        $contents[] = array('text' => TEXT_NEW_CATEGORY_INTRO);

        $category_inputs_string = '';
        $languages = tep_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
          $category_inputs_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('categories_name[' . $languages[$i]['id'] . ']');
          // HTC BOC
          $category_htc_title_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('categories_htc_title_tag[' . $languages[$i]['id'] . ']');
          $category_htc_desc_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('categories_htc_desc_tag[' . $languages[$i]['id'] . ']');
          $category_htc_keywords_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('categories_htc_keywords_tag[' . $languages[$i]['id'] . ']');
          $category_h1_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('categories_h1[' . $languages[$i]['id'] . ']');
          $category_surls_name_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('categories_surls_name[' . $languages[$i]['id'] . ']');
          $category_htc_description_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_textarea_field('categories_htc_description[' . $languages[$i]['id'] . ']', 'soft', 30, 5, '');
          $category_body_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_textarea_field('categories_body[' . $languages[$i]['id'] . ']', 'soft', 30, 5, '');
          $category_body2_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_textarea_field('categories_body2[' . $languages[$i]['id'] . ']', 'soft', 30, 5, '');
          $category_img_alt_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_textarea_field('categories_img_alt[' . $languages[$i]['id'] . ']', 'soft', 30, 5, '');
          // HTC EOC
        }

        $contents[] = array('text' => '<br>' . TEXT_CATEGORIES_NAME . $category_inputs_string);
        $contents[] = array('text' => '<br>' . TEXT_CATEGORIES_IMAGE . '<br>' . tep_draw_file_field('categories_image'));
        $contents[] = array('text' => '<br>' . TEXT_SORT_ORDER . '<br>' . tep_draw_input_field('sort_order', '', 'size="2"'));
        // HTC BOC
		$contents[] = array('text' => '<br>' . 'Category Img Alt Text' . $category_img_alt_string);
		$contents[] = array('text' => '<br>' . 'Category URL Name' . $category_surls_name_string);
        $contents[] = array('text' => '<br>' . 'Category Header Tag Title' . $category_htc_title_string);
        $contents[] = array('text' => '<br>' . 'Category Header Tag Description' . $category_htc_desc_string);
        $contents[] = array('text' => '<br>' . 'Category Header Tag Keywords' . $category_htc_keywords_string);
		$contents[] = array('text' => '<br>' . 'Category H1 Line' . $category_h1_string);
        $contents[] = array('text' => '<br>' . 'Category Title/Intro Text' . $category_htc_description_string);
		$contents[] = array('text' => '<br>' . 'Category Body Text' . $category_body_string);
		$contents[] = array('text' => '<br>' . 'Category Footer Text' . $category_body2_string);
        // HTC EOC
        $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_save.gif', IMAGE_SAVE) . ' <a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
        break;
      case 'edit_category':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_CATEGORY . '</b>');

        $contents = array('form' => tep_draw_form('categories', FILENAME_CATEGORIES, 'action=update_category&cPath=' . $cPath, 'post', 'enctype="multipart/form-data"') . tep_draw_hidden_field('categories_id', $cInfo->categories_id));
        $contents[] = array('text' => TEXT_EDIT_INTRO);

        $category_inputs_string = '';
        $languages = tep_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {

          $categories_query = tep_db_query("select cd.categories_name, cd.categories_htc_title_tag, cd.categories_htc_desc_tag, cd.categories_htc_keywords_tag, cd.categories_h1, cd.categories_surls_id, cd.categories_htc_description, cd.categories_body, cd.categories_body2, cd.categories_img_alt from " . TABLE_CATEGORIES_DESCRIPTION . " cd where cd.categories_id = '" . (int)$cInfo->categories_id . "' and cd.language_id = '" . (int)$languages[$i]['id'] . "'");
          $categories = tep_db_fetch_array($categories_query);

          $category_inputs_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('categories_name[' . $languages[$i]['id'] . ']', $categories['categories_name']);
          // HTC BOC
          $category_htc_title_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('categories_htc_title_tag[' . $languages[$i]['id'] . ']', $categories['categories_htc_title_tag']);
          $category_htc_desc_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('categories_htc_desc_tag[' . $languages[$i]['id'] . ']', $categories['categories_htc_desc_tag']);
          $category_htc_keywords_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('categories_htc_keywords_tag[' . $languages[$i]['id'] . ']', $categories['categories_htc_keywords_tag']);
          $category_h1_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('categories_h1[' . $languages[$i]['id'] . ']', $categories['categories_h1']);
          $category_surls_id_string .= tep_draw_hidden_field('categories_surls_id[' . $languages[$i]['id'] . ']', $categories['categories_surls_id']);
          $category_surls_name_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('categories_surls_name[' . $languages[$i]['id'] . ']', tep_get_category_surls_name($cInfo->categories_id, $languages[$i]['id']));
          $category_htc_description_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_textarea_field('categories_htc_description[' . $languages[$i]['id'] . ']', 'soft', 30, 5, $categories['categories_htc_description']);
          $category_body_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_textarea_field('categories_body[' . $languages[$i]['id'] . ']', 'soft', 30, 5, $categories['categories_body']);
          $category_body2_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_textarea_field('categories_body2[' . $languages[$i]['id'] . ']', 'soft', 30, 5, $categories['categories_body2']);
          $category_img_alt_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_textarea_field('categories_img_alt[' . $languages[$i]['id'] . ']', 'soft', 30, 5, $categories['categories_img_alt']);
          // HTC EOC
        }

        $contents[] = array('text' => '<br>' . TEXT_EDIT_CATEGORIES_NAME . $category_inputs_string);
        $contents[] = array('text' => '<br>' . TEXT_EDIT_SORT_ORDER . '<br>' . tep_draw_input_field('sort_order', $cInfo->sort_order, 'size="2"'));
        if ($cInfo->categories_id != $pi_category_id) {  // added by splautz for product inventory (remove extra category options)
// ###################### Added Categories Enable / Disable #################
          $contents[] = array('text' => '<br>' . TEXT_EDIT_STATUS . '<br>' . tep_draw_input_field('categories_status', $cInfo->categories_status, 'size="2"') . '1=Enabled 0=Disabled');
// ##################### End Added Categories Enable / Disable ###################
          $contents[] = array('text' => '<br>' . tep_image(DIR_WS_CATALOG_IMAGES . $cInfo->categories_image, $cInfo->categories_name) . '<br>' . DIR_WS_CATALOG_IMAGES . '<br><b>' . $cInfo->categories_image . '</b>');
          $contents[] = array('text' => '<br>' . TEXT_EDIT_CATEGORIES_IMAGE . '<br>' . tep_draw_file_field('categories_image'));
        // HTC BOC
		  $contents[] = array('text' => '<br>' . 'Category Img Alt Text' . $category_img_alt_string);
		  $contents[] = array('text' => $category_surls_id_string);
          $contents[] = array('text' => '<br>' . 'Category URL Name' . $category_surls_name_string);
          $contents[] = array('text' => '<br>' . 'Category Header Tag Title' . $category_htc_title_string);
          $contents[] = array('text' => '<br>' . 'Category Header Tag Description' . $category_htc_desc_string);
          $contents[] = array('text' => '<br>' . 'Category Header Tag Keywords' . $category_htc_keywords_string);
          $contents[] = array('text' => '<br>' . 'Category H1 Line' . $category_h1_string);
          $contents[] = array('text' => '<br>' . 'Category Title/Intro Text' . $category_htc_description_string);
          $contents[] = array('text' => '<br>' . 'Category Body Text' . $category_body_string);
          $contents[] = array('text' => '<br>' . 'Category Footer Text' . $category_body2_string);
        // HTC EOC
        }
	    $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_save.gif', IMAGE_SAVE) . ' <a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
        break;
*/
      case 'delete_category':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_CATEGORY . '</b>');

        $contents = array('form' => tep_draw_form('categories', FILENAME_CATEGORIES, 'action=delete_category_confirm&' . $urlpage) . tep_draw_hidden_field('categories_id', $cInfo->categories_id));
        $contents[] = array('text' => TEXT_DELETE_CATEGORY_INTRO);
        $contents[] = array('text' => '<br><b>' . $cInfo->categories_name . '</b>');
        if ($cInfo->childs_count > 0) $contents[] = array('text' => '<br>' . sprintf(TEXT_DELETE_WARNING_CHILDS, $cInfo->childs_count));
        if ($cInfo->products_count > 0) $contents[] = array('text' => '<br>' . sprintf(TEXT_DELETE_WARNING_PRODUCTS, $cInfo->products_count));
        $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(FILENAME_CATEGORIES, $urlpage . '&cID=' . $cInfo->categories_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
        break;
      case 'move_category':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_MOVE_CATEGORY . '</b>');

        $contents = array('form' => tep_draw_form('categories', FILENAME_CATEGORIES, 'action=move_category_confirm&' . $urlpage) . tep_draw_hidden_field('categories_id', $cInfo->categories_id));
        $contents[] = array('text' => sprintf(TEXT_MOVE_CATEGORIES_INTRO, $cInfo->categories_name));
        $contents[] = array('text' => '<br>' . sprintf(TEXT_MOVE, $cInfo->categories_name) . '<br>' . tep_draw_pull_down_menu('move_to_category_id', tep_get_category_tree(0,'',1), $current_category_id));
        $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_move.gif', IMAGE_MOVE) . ' <a href="' . tep_href_link(FILENAME_CATEGORIES, $urlpage . '&cID=' . $cInfo->categories_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
        break;
      case 'delete_product':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_PRODUCT . '</b>');

        $contents = array('form' => tep_draw_form('products', FILENAME_CATEGORIES, 'action=delete_product_confirm&' . $urlpage) . tep_draw_hidden_field('products_id', $pInfo->products_id));
        $contents[] = array('text' => TEXT_DELETE_PRODUCT_INTRO);
        $contents[] = array('text' => '<br><b>' . $pInfo->products_name . '</b>');

        $product_categories_string = '';
        $product_categories = tep_generate_category_path($pInfo->products_id, 'product');
        for ($i = 0, $n = sizeof($product_categories); $i < $n; $i++) {
          $category_path = '';
          for ($j = 0, $k = sizeof($product_categories[$i]); $j < $k; $j++) {
            $category_path .= $product_categories[$i][$j]['text'] . '&nbsp;&gt;&nbsp;';
          }
          $category_path = substr($category_path, 0, -16);
          $product_categories_string .= tep_draw_checkbox_field('product_categories[]', $product_categories[$i][sizeof($product_categories[$i])-1]['id'], true) . '&nbsp;' . $category_path . '<br>';
        }
        $product_categories_string = substr($product_categories_string, 0, -4);

        $contents[] = array('text' => '<br>' . $product_categories_string);
        $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(FILENAME_CATEGORIES, $urlpage . '&pID=' . $pInfo->products_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
        break;
      case 'move_product':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_MOVE_PRODUCT . '</b>');

        $contents = array('form' => tep_draw_form('products', FILENAME_CATEGORIES, 'action=move_product_confirm&' . $urlpage) . tep_draw_hidden_field('products_id', $pInfo->products_id));
        $contents[] = array('text' => sprintf(TEXT_MOVE_PRODUCTS_INTRO, $pInfo->products_name));
        $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENT_CATEGORIES . '<br><b>' . tep_output_generated_category_path($pInfo->products_id, 'product') . '</b>');
        $contents[] = array('text' => '<br>' . sprintf(TEXT_MOVE, $pInfo->products_name) . '<br>' . tep_draw_pull_down_menu('move_to_category_id', tep_get_category_tree(0,'',1), $current_category_id));
        $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_move.gif', IMAGE_MOVE) . ' <a href="' . tep_href_link(FILENAME_CATEGORIES, $urlpage . '&pID=' . $pInfo->products_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
        break;
      case 'copy_to':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_COPY_TO . '</b>');

        $contents = array('form' => tep_draw_form('copy_to', FILENAME_CATEGORIES, 'action=copy_to_confirm&' . $urlpage) . tep_draw_hidden_field('products_id', $pInfo->products_id));
        $contents[] = array('text' => TEXT_INFO_COPY_TO_INTRO);
        $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENT_CATEGORIES . '<br><b>' . tep_output_generated_category_path($pInfo->products_id, 'product') . '</b>');
        $contents[] = array('text' => '<br>' . TEXT_CATEGORIES . '<br>' . tep_draw_pull_down_menu('categories_id', tep_get_category_tree(0,'',1), $current_category_id));
        $contents[] = array('text' => '<br>' . TEXT_HOW_TO_COPY . '<br>' . tep_draw_radio_field('copy_as', 'link', true) . ' ' . TEXT_COPY_AS_LINK . '<br>' . tep_draw_radio_field('copy_as', 'duplicate') . ' ' . TEXT_COPY_AS_DUPLICATE);
        $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_copy.gif', IMAGE_COPY) . ' <a href="' . tep_href_link(FILENAME_CATEGORIES, $urlpage . '&pID=' . $pInfo->products_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
        break;
      default:
        if ($rows > 0) {
          if (isset($cInfo) && is_object($cInfo)) { // category info box contents
            $heading[] = array('text' => '<b>' . $cInfo->categories_name . '</b>');
// modified by splautz for product inventory (remove delete & move options)
            $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_CATEGORIES, $urlpage . '&cID=' . $cInfo->categories_id . '&action=edit_category') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a>' . (($cInfo->categories_id == $pi_category_id)?'':' <a href="' . tep_href_link(FILENAME_CATEGORIES, $urlpage . '&cID=' . $cInfo->categories_id . '&action=delete_category') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a> <a href="' . tep_href_link(FILENAME_CATEGORIES, $urlpage . '&cID=' . $cInfo->categories_id . '&action=move_category') . '">' . tep_image_button('button_move.gif', IMAGE_MOVE) . '</a>'));
            $contents[] = array('text' => '<br>' . TEXT_DATE_ADDED . ' ' . tep_date_short($cInfo->date_added));
            if (tep_not_null($cInfo->last_modified)) $contents[] = array('text' => TEXT_LAST_MODIFIED . ' ' . tep_date_short($cInfo->last_modified));
            $contents[] = array('text' => '<br>' . tep_info_image($cInfo->categories_image, $cInfo->categories_img_alt?$cInfo->categories_img_alt:$cInfo->categories_name, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT) . '<br>' . $cInfo->categories_image);
            $contents[] = array('text' => '<br>' . TEXT_SUBCATEGORIES . ' ' . $cInfo->childs_count . '<br>' . TEXT_PRODUCTS . ' ' . $cInfo->products_count);
          } elseif (isset($pInfo) && is_object($pInfo)) { // product info box contents
            $heading[] = array('text' => '<b>' . tep_get_products_name($pInfo->products_id, $languages_id) . '</b>');
//DANIEL: begin - Related Products button added at the very very end of this line
// modified by splautz for product inventory (remove move option)
            $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_CATEGORIES, $urlpage . '&pID=' . $pInfo->products_id . '&action=new_product') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . tep_href_link(FILENAME_CATEGORIES, $urlpage . '&pID=' . $pInfo->products_id . '&action=delete_product') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a>' . (($current_category_id == $pi_category_id)?'':' <a href="' . tep_href_link(FILENAME_CATEGORIES, $urlpage . '&pID=' . $pInfo->products_id . '&action=move_product') . '">' . tep_image_button('button_move.gif', IMAGE_MOVE) . '</a>') . ' <a href="' . tep_href_link(FILENAME_CATEGORIES, $urlpage . '&pID=' . $pInfo->products_id . '&action=copy_to') . '">' . tep_image_button('button_copy_to.gif', IMAGE_COPY_TO) .
             '</a> <a href="./new_attributes.php?action=select&current_product_id=' . $pInfo->products_id . '&cPath=' . $cPath . '">' .  tep_image_button('button_edit_attributes.gif', IMAGE_EDIT_ATTRIBUTES) .
             '</a> <a href="' . tep_href_link(FILENAME_RELATED_PRODUCTS, 'products_id_view=' . $pInfo->products_id) . '" target="_new">' . tep_image_button('button_related_products.gif', 'Related Products') .
             '</a> <a href="' . tep_href_link(FILENAME_IMAGES, 'group_id_view=' . $pInfo->products_id . '&image_group=p') . '" target="_new">' . tep_image_button('button_images.gif', 'Images') . '</a>');
//DANIEL: end
            $contents[] = array('text' => '<br>' . TEXT_DATE_ADDED . ' ' . tep_date_short($pInfo->products_date_added));
            if (tep_not_null($pInfo->products_last_modified)) $contents[] = array('text' => TEXT_LAST_MODIFIED . ' ' . tep_date_short($pInfo->products_last_modified));
            if (date('Y-m-d') < $pInfo->products_date_available) $contents[] = array('text' => TEXT_DATE_AVAILABLE . ' ' . tep_date_short($pInfo->products_date_available));
            $contents[] = array('text' => '<br>' . tep_info_image($pInfo->products_image, $pInfo->products_img_alt?$pInfo->products_img_alt:$pInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '<br>' . $pInfo->products_image);
            $contents[] = array('text' => '<br>' . TEXT_PRODUCTS_PRICE_INFO . ' ' . $currencies->format($pInfo->products_price) . '<br>' . TEXT_PRODUCTS_QUANTITY_INFO . ' ' . $pInfo->products_quantity);
            $contents[] = array('text' => '<br>' . TEXT_PRODUCTS_AVERAGE_RATING . ' ' . number_format($pInfo->average_rating, 2) . '%');
          }
        } else { // create category/product info
          $heading[] = array('text' => '<b>' . EMPTY_CATEGORY . '</b>');

          $contents[] = array('text' => TEXT_NO_CHILD_CATEGORIES_OR_PRODUCTS);
        }
        break;
    }

    if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
      echo '            <td width="25%" valign="top">' . "\n";

      $box = new box;
      echo $box->infoBox($heading, $contents);

      echo '            </td>' . "\n";
    }
?>
          </tr>
        </table></td>
      </tr>
    </table>
<?php
  }
?>
    </td>
<!-- body_text_eof //-->
  </tr>
</table>
<FORM name="hidden_form" method="POST" action="spellcheck.php?init=yes" target="WIN">
<INPUT type="hidden" name="form_name" value="">
<INPUT type="hidden" name="field_name" value="">
<INPUT type="hidden" name="first_time_text" value="">
</FORM>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>