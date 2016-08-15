<?php
/*
  $Id: shopping_cart.php,v 1.35 2003/06/25 21:14:33 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  class shoppingCart {
    var $contents, $total, $weight, $cartID, $content_type, $tax;  // $tax added by splautz to tally tax totals
    var $shipments;  // added by splautz for support of freight shipping

    function shoppingCart() {
      $this->reset();
    }

    function restore_contents($reset_basket = false) {  // reset_basket added by splautz to perform cart to basket save only
// ############Added CCGV Contribution ##########
      global $customer_id, $gv_id, $REMOTE_ADDR;
//      global $customer_id;
// ############ End Added CCGV Contribution ##########

      if (!tep_session_is_registered('customer_id')) return false;

// added by splautz for reset_basket
      if ($reset_basket) {
        tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . (int)$customer_id . "'");
        tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where customers_id = '" . (int)$customer_id . "'");
      }

// insert current cart contents in database
      if (is_array($this->contents)) {
        reset($this->contents);
        while (list($products_id, ) = each($this->contents)) {
          $qty = $this->contents[$products_id]['qty'];
          $product_query = tep_db_query("select products_id from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . (int)$customer_id . "' and products_id = '" . tep_db_input($products_id) . "'");
          if (!tep_db_num_rows($product_query)) {
            tep_db_query("insert into " . TABLE_CUSTOMERS_BASKET . " (customers_id, products_id, customers_basket_quantity, customers_basket_date_added) values ('" . (int)$customer_id . "', '" . tep_db_input($products_id) . "', '" . $qty . "', '" . date('Ymd') . "')");
            if (isset($this->contents[$products_id]['attributes'])) {
              reset($this->contents[$products_id]['attributes']);
              while (list($option, $value) = each($this->contents[$products_id]['attributes'])) {
                //clr 031714 udate query to include attribute value. This is needed for text attributes.
                $attr_value = $this->contents[$products_id]['attributes_values'][$option];
                tep_db_query("insert into " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " (customers_id, products_id, products_options_id, products_options_value_id, products_options_value_text) values ('" . (int)$customer_id . "', '" . tep_db_input($products_id) . "', '" . (int)$option . "', '" . (int)$value . "', '" . tep_db_input($attr_value) . "')");
                // tep_db_query("insert into " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " (customers_id, products_id, products_options_id, products_options_value_id) values ('" . (int)$customer_id . "', '" . tep_db_input($products_id) . "', '" . (int)$option . "', '" . (int)$value . "')");
              }
            }
          } else {
            tep_db_query("update " . TABLE_CUSTOMERS_BASKET . " set customers_basket_quantity = '" . $qty . "' where customers_id = '" . (int)$customer_id . "' and products_id = '" . tep_db_input($products_id) . "'");
          }
        }
// ############ Added CCGV Contribution ##########
        if (tep_session_is_registered('gv_id')) {
          $gv_query = tep_db_query("insert into  " . TABLE_COUPON_REDEEM_TRACK . " (coupon_id, customer_id, redeem_date, redeem_ip) values ('" . $gv_id . "', '" . (int)$customer_id . "', now(),'" . $REMOTE_ADDR . "')");
          $gv_update = tep_db_query("update " . TABLE_COUPONS . " set coupon_active = 'N' where coupon_id = '" . $gv_id . "'");
          tep_gv_account_update($customer_id, $gv_id);
          tep_session_unregister('gv_id');
        }
// ############ End Added CCGV Contribution ##########
      }

      if (!$reset_basket) { // added by splautz for reset_basket
// reset per-session cart contents, but not the database contents
      $this->reset(false);

      $products_query = tep_db_query("select products_id, customers_basket_quantity from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . (int)$customer_id . "'");
      while ($products = tep_db_fetch_array($products_query)) {
        $this->contents[$products['products_id']] = array('qty' => $products['customers_basket_quantity']);
// attributes
        //CLR 020606 update query to pull attribute value_text. This is needed for text attributes.
        $attributes_query = tep_db_query("select products_options_id, products_options_value_id, products_options_value_text from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where customers_id = '" . (int)$customer_id . "' and products_id = '" . tep_db_input($products['products_id']) . "'");
        // $attributes_query = tep_db_query("select products_options_id, products_options_value_id from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where customers_id = '" . (int)$customer_id . "' and products_id = '" . tep_db_input($products['products_id']) . "'");
        while ($attributes = tep_db_fetch_array($attributes_query)) {
          $this->contents[$products['products_id']]['attributes'][$attributes['products_options_id']] = $attributes['products_options_value_id'];
          //CLR 020606 if text attribute, then set additional information
          if ($attributes['products_options_value_id'] == PRODUCTS_OPTIONS_VALUE_TEXT_ID)
            $this->contents[$products['products_id']]['attributes_values'][$attributes['products_options_id']] = $attributes['products_options_value_text'];
        }
      }

      $this->cleanup();
      } // added by splautz for reset_basket
    }

    function reset($reset_database = false) {
      global $customer_id;

      $this->contents = array();
      $this->total = 0;
      $this->weight = 0;
      $this->tax = 0;  // added by splautz to tally tax totals
      $this->shipments = array(array(),array());  // added by splautz for support of freight shipping
      $this->content_type = false;

      if (tep_session_is_registered('customer_id') && ($reset_database == true)) {
        tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . (int)$customer_id . "'");
        tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where customers_id = '" . (int)$customer_id . "'");
      }

      unset($this->cartID);
      if (tep_session_is_registered('cartID')) tep_session_unregister('cartID');
    }

    function add_cart($products_id, $qty = '1', $attributes = '', $notify = true) {
      global $new_products_id_in_cart, $customer_id;
// added by splautz for better handling of cartID
      $updateID = false;

// modified by splautz to allow notify of multiple products
// update-20051113
      $products_id_string = tep_get_uprid($products_id, $attributes);
      $products_id = tep_get_prid($products_id_string);

      if (is_numeric($products_id) && is_numeric($qty)) {
        $check_product_query = tep_db_query("select products_status from " . TABLE_PRODUCTS . " where products_id = '" . (int)$products_id . "'");
        $check_product = tep_db_fetch_array($check_product_query);

        if (($check_product !== false) && ($check_product['products_status'] == '1')) {
          if ($notify == true) {
            if (!tep_session_is_registered('new_products_id_in_cart')) {
              $new_products_id_in_cart = array();
              tep_session_register('new_products_id_in_cart');
            }
            if (!in_array($products_id_string, $new_products_id_in_cart)) $new_products_id_in_cart[] = $products_id_string;
          }

          if ($this->in_cart($products_id_string)) {
            $updateID = $this->update_quantity($products_id, $qty, $attributes, $products_id_string);  // modified by splautz for better handling of cartID
          } else {
// added by splautz for better handling of cartID
//          $this->contents[] = array($products_id);  splautz => I'm unsure what this was for (removed in update-20051113)
            $updateID = true;

            $this->contents[$products_id_string] = array('qty' => $qty);
// insert into database
            if (tep_session_is_registered('customer_id')) tep_db_query("insert into " . TABLE_CUSTOMERS_BASKET . " (customers_id, products_id, customers_basket_quantity, customers_basket_date_added) values ('" . (int)$customer_id . "', '" . tep_db_input($products_id_string) . "', '" . $qty . "', '" . date('Ymd') . "')");

            if (is_array($attributes)) {
              reset($attributes);
              while (list($option, $value) = each($attributes)) {
                //CLR 020606 check if input was from text box.  If so, store additional attribute information
                //CLR 020708 check if text input is blank, if so do not add to attribute lists
                //CLR 030228 add htmlspecialchars processing.  This handles quotes and other special chars in the user input.
                $attr_value = NULL;
                $blank_value = FALSE;
                if (strstr($option, TEXT_PREFIX)) {
                  if (trim($value) == NULL)
                  {
                    $blank_value = TRUE;
                  } else {
                    $option = substr($option, strlen(TEXT_PREFIX));
                    $attr_value = htmlspecialchars(stripslashes($value), ENT_QUOTES);
                    $value = PRODUCTS_OPTIONS_VALUE_TEXT_ID;
                    $this->contents[$products_id_string]['attributes_values'][$option] = $attr_value;
                  }
                }

                if (!$blank_value)
                {
                  $this->contents[$products_id_string]['attributes'][$option] = $value;
// insert into database
                //CLR 020606 update db insert to include attribute value_text. This is needed for text attributes.
                //CLR 030228 add tep_db_input() processing
                  if (tep_session_is_registered('customer_id')) tep_db_query("insert into " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " (customers_id, products_id, products_options_id, products_options_value_id, products_options_value_text) values ('" . (int)$customer_id . "', '" . tep_db_input($products_id_string) . "', '" . (int)$option . "', '" . (int)$value . "', '" . tep_db_input($attr_value) . "')");
                // if (tep_session_is_registered('customer_id')) tep_db_query("insert into " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " (customers_id, products_id, products_options_id, products_options_value_id) values ('" . (int)$customer_id . "', '" . tep_db_input($products_id_string) . "', '" . (int)$option . "', '" . (int)$value . "')");
                }
              }
            }
          }
// added by splautz for better handling of cartID
          if ($updateID) $this->cleanup();
        }
      }
    }

// modified by splautz to pass $products_id_string
//  function update_quantity($products_id, $quantity = '', $attributes = '') {
    function update_quantity($products_id, $quantity = '', $attributes = '', $products_id_string = NULL) {
      global $customer_id;
// update-20051113
      if (!$products_id_string) $products_id_string = tep_get_uprid($products_id, $attributes);
      $products_id = tep_get_prid($products_id_string);

//    if (empty($quantity)) return true; // nothing needs to be updated if theres no quantity, so we return true..
      if (is_numeric($products_id) && isset($this->contents[$products_id_string]) && is_numeric($quantity) && $quantity >= 0) { // modified by splautz to handle 0

// added by splautz for better handling of cartID
        $updateID = false;
        if ($this->contents[$products_id_string]['qty'] != $quantity) {
          $updateID = true;
//        $this->contents[$products_id_string] = array('qty' => $quantity);
          $this->contents[$products_id_string]['qty'] = $quantity;
          if (!$quantity) return true; // added by splautz to handle 0
// update database
          if (tep_session_is_registered('customer_id')) tep_db_query("update " . TABLE_CUSTOMERS_BASKET . " set customers_basket_quantity = '" . $quantity . "' where customers_id = '" . (int)$customer_id . "' and products_id = '" . tep_db_input($products_id_string) . "'");
        }

        if (is_array($attributes)) {
          reset($attributes);
          while (list($option, $value) = each($attributes)) {
            //CLR 020606 check if input was from text box.  If so, store additional attribute information
            //CLR 030108 check if text input is blank, if so do not update attribute lists
            //CLR 030228 add htmlspecialchars processing.  This handles quotes and other special chars in the user input.
            $attr_value = NULL;
            $blank_value = FALSE;
            if (strstr($option, TEXT_PREFIX)) {
              if (trim($value) == NULL)
              {
                $blank_value = TRUE;
              } else {
                $option = substr($option, strlen(TEXT_PREFIX));
                $attr_value = htmlspecialchars(stripslashes($value), ENT_QUOTES);
                $value = PRODUCTS_OPTIONS_VALUE_TEXT_ID;
                if ($this->contents[$products_id_string]['attributes_values'][$option] != $attr_value)
                  $this->contents[$products_id_string]['attributes_values'][$option] = $attr_value;
                else $blank_value = TRUE; // added by splautz to prevent update if text value is the same
              }
            }

            if (!$blank_value) {
// added by splautz for better handling of cartID
              if (tep_not_null($attr_value) || $this->contents[$products_id_string]['attributes'][$option] != $value) {
                $updateID = true;
                $this->contents[$products_id_string]['attributes'][$option] = $value;
// update database
                //CLR 020606 update db insert to include attribute value_text. This is needed for text attributes.
                //CLR 030228 add tep_db_input() processing
                if (tep_session_is_registered('customer_id')) tep_db_query("update " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " set products_options_value_id = '" . (int)$value . "', products_options_value_text = '" . tep_db_input($attr_value) . "' where customers_id = '" . (int)$customer_id . "' and products_id = '" . tep_db_input($products_id_string) . "' and products_options_id = '" . (int)$option . "'");
                // if (tep_session_is_registered('customer_id')) tep_db_query("update " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " set products_options_value_id = '" . (int)$value . "' where customers_id = '" . (int)$customer_id . "' and products_id = '" . tep_db_input($products_id_string) . "' and products_options_id = '" . (int)$option . "'");
              }

            }
          }
        }
// added by splautz for better handling of cartID
        return $updateID;

      } else return false;
    }

    function cleanup() {
      global $customer_id;

      reset($this->contents);
      while (list($key,) = each($this->contents)) {
        if ($this->contents[$key]['qty'] < 1) {
          unset($this->contents[$key]);
// remove from database
          if (tep_session_is_registered('customer_id')) {
            tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . (int)$customer_id . "' and products_id = '" . tep_db_input($key) . "'");
            tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where customers_id = '" . (int)$customer_id . "' and products_id = '" . tep_db_input($key) . "'");
          }
        }
      }

// assign a temporary unique ID to the order contents to prevent hack attempts during the checkout procedure
      $this->cartID = $this->generate_cart_id();
    }

/* -1 = non-freight excluding free items; 0 = non-freight; 1 = freight; 2 = all;  */
    function count_contents($freight = 2) {  // get total number of items in cart 
      $total_items = 0;
// modified by splautz to support freight shipping
      if (is_array($this->contents)) {
        switch ($freight) {
          case -1:
          case 0:
          case 1:
            foreach($this->shipments[0] as $orig_code => $shipment)
              $total_items += ($freight < 0)?$shipment['count2']:$shipment['count'];
            if ($freight < 1) break;
            else $total_items *= -1;
          default:
            reset($this->contents);
            while (list($products_id, ) = each($this->contents)) {
              $total_items += $this->get_quantity($products_id);
            }
        }
      }

      return $total_items;
    }

    function get_quantity($products_id) {
      if (isset($this->contents[$products_id])) {
        return $this->contents[$products_id]['qty'];
      } else {
        return 0;
      }
    }

    function in_cart($products_id) {
      if (isset($this->contents[$products_id])) {
        return true;
      } else {
        return false;
      }
    }

    function remove($products_id) {
      global $customer_id;

      //CLR 030228 add call tep_get_uprid to correctly format product ids containing quotes
      $products_id = tep_get_uprid($products_id, $attributes);

      unset($this->contents[$products_id]);
// remove from database
      if (tep_session_is_registered('customer_id')) {
        tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . (int)$customer_id . "' and products_id = '" . tep_db_input($products_id) . "'");
        tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where customers_id = '" . (int)$customer_id . "' and products_id = '" . tep_db_input($products_id) . "'");
      }

// assign a temporary unique ID to the order contents to prevent hack attempts during the checkout procedure
      $this->cartID = $this->generate_cart_id();
    }

    function remove_all() {
      $this->reset();
    }

    function get_product_id_list() {
      $product_id_list = '';
      if (is_array($this->contents)) {
        reset($this->contents);
        while (list($products_id, ) = each($this->contents)) {
          $product_id_list .= ', ' . $products_id;
        }
      }

      return substr($product_id_list, 2);
    }

    function calculate() {
// ############ Added CCGV Contribution ##########
      $this->total_virtual = 0; // CCGV Contribution
// ############ End Added CCGV Contribution ##########
      $this->total = 0;
      $this->weight = 0;
      $this->tax = 0;  // added by splautz to tally tax totals
      $this->shipments = array(array(),array());  // added by splautz for support of freight shipping
      if (!is_array($this->contents)) return 0;

      reset($this->contents);
      while (list($products_id, ) = each($this->contents)) {
        $qty = $this->contents[$products_id]['qty'];

// products price
// modified by splautz for freight shipping
//      $product_query = tep_db_query("select products_id, products_price, products_tax_class_id, products_weight from " . TABLE_PRODUCTS . " where products_id = '" . (int)$products_id . "'");
        $product_query = tep_db_query("select products_id, products_price, products_tax_class_id, products_weight, products_free_shipping, products_freight_class, products_origin_postcode from " . TABLE_PRODUCTS . " where products_id = '" . (int)$products_id . "'");

        if ($product = tep_db_fetch_array($product_query)) {
// ############ Added CCGV Contribution ##########
          $no_count = 1;
          $gv_query = tep_db_query("select products_model from " . TABLE_PRODUCTS . " where products_id = '" . (int)$products_id . "'");
          $gv_result = tep_db_fetch_array($gv_query);
          if (ereg('^GIFT', $gv_result['products_model'])) {
            $no_count = 0;
          }
// ############ End Added CCGV Contribution ##########
          $prid = $product['products_id'];
          $products_tax = tep_get_tax_rate($product['products_tax_class_id']);
          $products_price = $product['products_price'];
          $products_weight = $product['products_weight'];
// added by splautz for freight shipping
          $products_free_shipping = $product['products_free_shipping'];
          $products_freight_class = preg_split("/\D+/",$product['products_freight_class'],-1,PREG_SPLIT_NO_EMPTY);
          $products_origin_postcode = $product['products_origin_postcode'];

          $specials_query = tep_db_query("select specials_new_products_price from " . TABLE_SPECIALS . " where products_id = '" . (int)$prid . "' and status = '1'");
          if (tep_db_num_rows ($specials_query)) {
            $specials = tep_db_fetch_array($specials_query);
            $products_price = $specials['specials_new_products_price'];
          }

// ############ Added CCGV Contribution ##########
          $this->total_virtual += tep_add_tax($products_price, $products_tax) * $qty * $no_count;// ICW CREDIT CLASS;
          $this->weight_virtual += ($qty * $products_weight) * $no_count;// ICW CREDIT CLASS;
// ############ End Added CCGV Contribution ##########
          $this->total += tep_add_tax($products_price, $products_tax) * $qty;
          $this->weight += ($qty * $products_weight);
// added by splautz for support of freight shipping
          $orig_code = tep_get_location_code($products_origin_postcode);
          $weight = $qty * (is_numeric($products_weight)?$products_weight:0);
          if ((sizeof($products_freight_class) && is_numeric($products_freight_class[0])) || $products_weight > 150) {
            $pfclass = in_array($products_freight_class[0],array(50,55,60,65,70,77,85,92,100,110,125,150,175,200,250,300,400,500))?$products_freight_class[0]:SHIPPING_FREIGHT_CLASS;
            $this->shipments[1][$orig_code][$pfclass]['count'] += $qty * (tep_not_null($products_freight_class[1])?$products_freight_class[1]:1);
            $this->shipments[1][$orig_code][$pfclass]['weight'] += $weight;
            if (!$products_free_shipping) {
              $this->shipments[1][$orig_code][$pfclass]['count2'] += $qty * (tep_not_null($products_freight_class[1])?$products_freight_class[1]:1);
              $this->shipments[1][$orig_code][$pfclass]['weight2'] += $weight;
            }
          } else {
            $this->shipments[0][$orig_code]['count'] += $qty;
            $this->shipments[0][$orig_code]['weight'] += $weight;
            if (!$products_free_shipping) {
              $this->shipments[0][$orig_code]['weight2'] += $weight;
              $this->shipments[0][$orig_code]['count2'] += $qty;
            }
          }

// added by splautz to tally tax totals
          if ($products_tax > 0) $this->tax += $qty * tep_calculate_tax($products_price, $products_tax);

// phpmom.com advanced attribute price
          if (isset($this->contents[$products_id]['attributes'])) {
            reset($this->contents[$products_id]['attributes']);
            while (list($option, $value) = each($this->contents[$products_id]['attributes'])) {
              $attribute_price_query = tep_db_query("select options_values_price, price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id = '" . (int)$prid . "' and options_id = '" . (int)$option . "' and options_values_id = '" . (int)$value . "'");
              $attribute_price = tep_db_fetch_array($attribute_price_query);
              if ($attribute_price['price_prefix'] == '+') {
                $this->total += $qty * tep_add_tax($attribute_price['options_values_price'], $products_tax);
// added by splautz to tally tax totals
                if ($products_tax > 0) $this->tax += $qty * tep_calculate_tax($attribute_price['options_values_price'], $products_tax);
              } elseif ($attribute_price['price_prefix'] == '-') {
                $this->total -= $qty * tep_add_tax($attribute_price['options_values_price'], $products_tax);
// added by splautz to tally tax totals
                if ($products_tax > 0) $this->tax -= $qty * tep_calculate_tax($attribute_price['options_values_price'], $products_tax);
              } else {
         //comment where you see //'0' if want '0' value
             // if ($attribute_price['options_values_price'] != '0') { //'0'
                  $this->total += $qty * (tep_add_tax($attribute_price['options_values_price'], $products_tax) - tep_add_tax($products_price, $products_tax));
// added by splautz to tally tax totals
                  if ($products_tax > 0) $this->tax += $qty * (tep_calculate_tax($attribute_price['options_values_price'], $products_tax) - tep_calculate_tax($products_price, $products_tax));
             // }//'0'
              }
            }
          }
        }
      }
    }

// This appears obsolete after applying advanced attributes contribution
// It would need to be rewritten and the actual price obtained to get an accurate attribute total
    function attributes_price($products_id) {
      $attributes_price = 0;

      if (isset($this->contents[$products_id]['attributes'])) {
        reset($this->contents[$products_id]['attributes']);
        while (list($option, $value) = each($this->contents[$products_id]['attributes'])) {
          $attribute_price_query = tep_db_query("select options_values_price, price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id = '" . (int)$products_id . "' and options_id = '" . (int)$option . "' and options_values_id = '" . (int)$value . "'");
          $attribute_price = tep_db_fetch_array($attribute_price_query);
          if ($attribute_price['price_prefix'] == '+') {
            $attributes_price += $attribute_price['options_values_price'];
          } elseif ($attribute_price['price_prefix'] == '-') {
            $attributes_price -= $attribute_price['options_values_price'];
          }
        }
      }

      return $attributes_price;
    }

    function get_products() {
      global $languages_id;

      if (!is_array($this->contents)) return false;

      $products_array = array();
      reset($this->contents);
      while (list($products_id, ) = each($this->contents)) {
        $products_query = tep_db_query("select p.products_id, pd.products_name, p.products_model, p.products_image, pd.products_img_alt, p.products_price, p.products_weight, p.products_tax_class_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = '" . (int)$products_id . "' and pd.products_id = p.products_id and pd.language_id = '" . (int)$languages_id . "'");
        if ($products = tep_db_fetch_array($products_query)) {
          $prid = $products['products_id'];
          $products_price = $products['products_price'];

          $specials_query = tep_db_query("select specials_new_products_price from " . TABLE_SPECIALS . " where products_id = '" . (int)$prid . "' and status = '1'");
          if (tep_db_num_rows($specials_query)) {
            $specials = tep_db_fetch_array($specials_query);
            $products_price = $specials['specials_new_products_price'];
          }

         //BOF PHPMOM.COM AAP//hadir
          $attributes_price = 0;
          if (isset($this->contents[$products_id]['attributes'])) {
            reset($this->contents[$products_id]['attributes']);
            while (list($option, $value) = each($this->contents[$products_id]['attributes'])) {
              $attribute_price_query = tep_db_query("select options_values_price, price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id = '" . (int)$products_id . "' and options_id = '" . (int)$option . "' and options_values_id = '" . (int)$value . "'");
              $attribute_price = tep_db_fetch_array($attribute_price_query);
              if ($attribute_price['price_prefix'] == '+') {
                $attributes_price += $attribute_price['options_values_price'];
              } elseif ($attribute_price['price_prefix'] == '-') {
                $attributes_price -= $attribute_price['options_values_price'];
              } else $attributes_price += ($attribute_price['options_values_price']-$products_price);
            }
          }
          //EOF PHPMOM.COM AAP//hadir

          //clr 030714 update $products_array to include attribute value_text. This is needed for text attributes.
          $products_array[] = array('id' => $products_id,
                                    'name' => $products['products_name'],
                                    'model' => $products['products_model'],
                                    'image' => $products['products_image'],
                                    'img_alt' => $products['products_img_alt'],
                                    'price' => $products_price,
                                    'quantity' => $this->contents[$products_id]['qty'],
                                    'weight' => $products['products_weight'],
//                                  'final_price' => ($products_price + $this->attributes_price($products_id)),
                                    'final_price' => ($products_price + $attributes_price),
                                    'attributes_price' => $attributes_price, //phpmom.com//aap
                                    'tax_class_id' => $products['products_tax_class_id'],
                                    'attributes' => (isset($this->contents[$products_id]['attributes']) ? $this->contents[$products_id]['attributes'] : ''),
                                    'attributes_values' => (isset($this->contents[$products_id]['attributes_values']) ? $this->contents[$products_id]['attributes_values'] : ''));
        }
      }

      return $products_array;
    }

    function show_total() {
      $this->calculate();

      return $this->total;
    }

    function show_weight() {
      $this->calculate();

      return $this->weight;
    }
// ############ Added CCGV Contribution ##########
    function show_total_virtual() {
      $this->calculate();

      return $this->total_virtual;
    }

    function show_weight_virtual() {
      $this->calculate();

      return $this->weight_virtual;
    }
// ############ End Added CCGV Contribution ##########

    function generate_cart_id($length = 5) {
      return tep_create_random_value($length, 'digits');
    }

    function get_content_type() {
      $this->content_type = false;

      if ( (DOWNLOAD_ENABLED == 'true') && ($this->count_contents() > 0) ) {
        reset($this->contents);
        while (list($products_id, ) = each($this->contents)) {
          if (isset($this->contents[$products_id]['attributes'])) {
            reset($this->contents[$products_id]['attributes']);
            while (list(, $value) = each($this->contents[$products_id]['attributes'])) {
              $virtual_check_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad where pa.products_id = '" . (int)$products_id . "' and pa.options_values_id = '" . (int)$value . "' and pa.products_attributes_id = pad.products_attributes_id");
              $virtual_check = tep_db_fetch_array($virtual_check_query);

              if ($virtual_check['total'] > 0) {
                switch ($this->content_type) {
                  case 'physical':
                    $this->content_type = 'mixed';

                    return $this->content_type;
                    break;
                  default:
                    $this->content_type = 'virtual';
                    break;
                }
              } else {
                switch ($this->content_type) {
                  case 'virtual':
                    $this->content_type = 'mixed';

                    return $this->content_type;
                    break;
                  default:
                    $this->content_type = 'physical';
                    break;
                }
              }
            }
// ############ Added CCGV Contribution ##########
          } elseif ($this->show_weight() == 0) {
            reset($this->contents);
            while (list($products_id, ) = each($this->contents)) {
              $virtual_check_query = tep_db_query("select products_weight from " . TABLE_PRODUCTS . " where products_id = '" . $products_id . "'");
              $virtual_check = tep_db_fetch_array($virtual_check_query);
              if ($virtual_check['products_weight'] == 0) {
                switch ($this->content_type) {
                  case 'physical':
                    $this->content_type = 'mixed';

                    return $this->content_type;
                    break;
                  default:
                    $this->content_type = 'virtual';
                    break;
                }
              } else {
                switch ($this->content_type) {
                  case 'virtual':
                    $this->content_type = 'mixed';

                    return $this->content_type;
                    break;
                  default:
                    $this->content_type = 'physical';
                    break;
                }
              }
            }
// ############ End Added CCGV Contribution ##########
          } else {
            switch ($this->content_type) {
              case 'virtual':
                $this->content_type = 'mixed';

                return $this->content_type;
                break;
              default:
                $this->content_type = 'physical';
                break;
            }
          }
        }
      } else {
        $this->content_type = 'physical';
      }

      return $this->content_type;
    }

    function unserialize($broken) {
      for(reset($broken);$kv=each($broken);) {
        $key=$kv['key'];
        if (gettype($this->$key)!="user function")
        $this->$key=$kv['value'];
      }
    }

// ############ Added CCGV Contribution ##########
   // amend count_contents to show nil contents for shipping
   // as we don't want to quote for 'virtual' item
   // GLOBAL CONSTANTS if NO_COUNT_ZERO_WEIGHT is true then we don't count any product with a weight
   // which is less than or equal to MINIMUM_WEIGHT
   // otherwise we just don't count gift certificates

    function count_contents_virtual() {  // get total number of items in cart disregard gift vouchers
      $total_items = 0;
      if (is_array($this->contents)) {
        reset($this->contents);
        while (list($products_id, ) = each($this->contents)) {
          $no_count = false;
          $gv_query = tep_db_query("select products_model from " . TABLE_PRODUCTS . " where products_id = '" . $products_id . "'");
          $gv_result = tep_db_fetch_array($gv_query);
          if (ereg('^GIFT', $gv_result['products_model'])) {
            $no_count=true;
          }
          if (NO_COUNT_ZERO_WEIGHT == 1) {
            $gv_query = tep_db_query("select products_weight from " . TABLE_PRODUCTS . " where products_id = '" . tep_get_prid($products_id) . "'");
            $gv_result=tep_db_fetch_array($gv_query);
            if ($gv_result['products_weight']<=MINIMUM_WEIGHT) {
              $no_count=true;
            }
          }
          if (!$no_count) $total_items += $this->get_quantity($products_id);
        }
      }
      return $total_items;
    }
// ############ End Added CCGV Contribution ##########
  }
?>
