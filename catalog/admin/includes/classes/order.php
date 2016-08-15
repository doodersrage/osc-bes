<?php
/*
  $Id: order.php,v 1.7 2003/06/20 16:23:08 hpdl Exp $
  Modified for Order Editor 2.5

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  class order {
    var $info, $totals, $products, $customer, $delivery;

    function order($order_id) {
      $this->info = array();
      $this->totals = array();
      $this->products = array();
      $this->customer = array();
      $this->delivery = array();

      $this->query($order_id);
    }
//Begin Order Editor modifications
    function query($order_id) {
//begin PayPal_Shopping_Cart_IPN
//    $order_query = tep_db_query("select customers_name, customers_company, customers_street_address, customers_suburb, customers_city, customers_postcode, customers_state, customers_country, customers_telephone, customers_email_address, customers_address_format_id, delivery_name, delivery_company, delivery_street_address, delivery_suburb, delivery_city, delivery_postcode, delivery_state, delivery_country, delivery_address_format_id, billing_name, billing_company, billing_street_address, billing_suburb, billing_city, billing_postcode, billing_state, billing_country, billing_address_format_id, payment_method, cc_type, cc_owner, cc_number, cc_expires, currency, currency_value, date_purchased, orders_status, last_modified, customers_id, payment_id from " . TABLE_ORDERS . " where orders_id = '" . (int)$order_id . "'");
//end PayPal_Shopping_Cart_IPN
// purchaseorders_1_4 start
 // Added purchase_order_number to query.
//    $order_query = tep_db_query("select customers_name, customers_company, customers_street_address, customers_suburb, customers_city, customers_postcode, customers_state, customers_country, customers_telephone, customers_email_address, customers_address_format_id, delivery_name, delivery_company, delivery_street_address, delivery_suburb, delivery_city, delivery_postcode, delivery_state, delivery_country, delivery_address_format_id, billing_name, billing_company, billing_street_address, billing_suburb, billing_city, billing_postcode, billing_state, billing_country, billing_address_format_id, payment_method, cc_type, cc_owner, cc_number, cc_expires, currency, currency_value, purchase_order_number, date_purchased, orders_status, last_modified from " . TABLE_ORDERS . " where orders_id = '" . tep_db_input($order_id) . "'");
//purchaseorders_1_4 end
      $order_query = tep_db_query("select * from " . TABLE_ORDERS . " where orders_id = '" . (int)$order_id . "'");
      $order = tep_db_fetch_array($order_query);

//    $totals_query = tep_db_query("select title, text from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . (int)$order_id . "' order by sort_order");
      $totals_query = tep_db_query("select * from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . (int)$order_id . "' order by sort_order");
      while ($totals = tep_db_fetch_array($totals_query)) {
        $this->totals[] = array(
        'title' => $totals['title'], 
        'text' => $totals['text'], 
        'class' => $totals['class'], 
        'value' => $totals['value'],
        'sort_order' => $totals['sort_order'], 
        'orders_total_id' => $totals['orders_total_id']);
      }

      $this->info = array('currency' => $order['currency'],
                          'currency_value' => $order['currency_value'],
                          'payment_method' => $order['payment_method'],
                          'cc_type' => $order['cc_type'],
                          'cc_owner' => $order['cc_owner'],
                          'cc_number' => $order['cc_number'],
                          'cc_expires' => $order['cc_expires'],
// purchaseorders_1_4 start
                          'purchase_order_number' => $order['purchase_order_number'],
// purchaseorders_1_4 end
                          'shipping_tax' => $order['shipping_tax'],
                          'date_purchased' => $order['date_purchased'],
//begin PayPal_Shopping_Cart_IPN
                          'payment_id' => $order['payment_id'],
//end PayPal_Shopping_Cart_IPN
                          'orders_status' => $order['orders_status'],
                          'last_modified' => $order['last_modified']);

      $this->customer = array('name' => $order['customers_name'],
//begin PayPal_Shopping_Cart_IPN
                              'id' => $order['customers_id'],
//end PayPal_Shopping_Cart_IPN
                              'company' => $order['customers_company'],
                              'street_address' => $order['customers_street_address'],
                              'suburb' => $order['customers_suburb'],
                              'city' => $order['customers_city'],
                              'postcode' => $order['customers_postcode'],
                              'state' => $order['customers_state'],
                              'country' => $order['customers_country'],
                              'format_id' => $order['customers_address_format_id'],
                              'telephone' => $order['customers_telephone'],
                              'email_address' => $order['customers_email_address']);

      $this->delivery = array('name' => $order['delivery_name'],
                              'company' => $order['delivery_company'],
                              'street_address' => $order['delivery_street_address'],
                              'suburb' => $order['delivery_suburb'],
                              'city' => $order['delivery_city'],
                              'postcode' => $order['delivery_postcode'],
                              'state' => $order['delivery_state'],
                              'country' => $order['delivery_country'],
                              'format_id' => $order['delivery_address_format_id']);

      $this->billing = array('name' => $order['billing_name'],
                             'company' => $order['billing_company'],
                             'street_address' => $order['billing_street_address'],
                             'suburb' => $order['billing_suburb'],
                             'city' => $order['billing_city'],
                             'postcode' => $order['billing_postcode'],
                             'state' => $order['billing_state'],
                             'country' => $order['billing_country'],
                             'format_id' => $order['billing_address_format_id']);

      $countryid = tep_get_country_id($this->delivery["country"]);
      $zoneid = tep_get_zone_id($countryid, $this->delivery["state"]);

      $index = 0;
//begin PayPal_Shopping_Cart_IPN
//    $orders_products_query = tep_db_query("select orders_products_id, products_name, products_model, products_price, products_tax, products_quantity, final_price, products_id from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . (int)$order_id . "'");
//end PayPal_Shopping_Cart_IPN
      $orders_products_query = tep_db_query("SELECT op.products_id, op.orders_products_id, op.products_name, op.products_model, op.products_price, op.products_tax, op.products_quantity, op.final_price, p.products_tax_class_id, p.products_weight FROM " . TABLE_ORDERS_PRODUCTS . " op LEFT JOIN " . TABLE_PRODUCTS . " p ON op.products_id = p.products_id WHERE orders_id = '" . (int)$order_id . "'");
      while ($orders_products = tep_db_fetch_array($orders_products_query)) {
        $this->products[$index] = array('qty' => $orders_products['products_quantity'],
//begin PayPal_Shopping_Cart_IPN
                                        'id' => $orders_products['products_id'],
                                        'orders_products_id' => $orders_products['orders_products_id'],
//end PayPal_Shopping_Cart_IPN
                                        'name' => $orders_products['products_name'],
                                        'model' => $orders_products['products_model'],
                                        'tax' => $orders_products['products_tax'],
                                        'tax_description' => tep_get_tax_description($orders_products['products_tax_class_id'], $countryid, $zoneid),
                                        'price' => $orders_products['products_price'],
                                        'final_price' => $orders_products['final_price'],
                                        'weight' => $orders_products['products_weight'],
                                        'orders_products_id' => $orders_products['orders_products_id']);

        $subindex = 0;
//begin PayPal_Shopping_Cart_IPN
//      $attributes_query = tep_db_query("select products_options, products_options_values, options_values_price, price_prefix, products_options_id, products_options_values_id from " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " where orders_id = '" . (int)$order_id . "' and orders_products_id = '" . (int)$orders_products['orders_products_id'] . "'");
//end PayPal_Shopping_Cart_IPN
        $attributes_query = tep_db_query("select * from " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " where orders_id = '" . (int)$order_id . "' and orders_products_id = '" . (int)$orders_products['orders_products_id'] . "'");
        if (tep_db_num_rows($attributes_query)) {
          while ($attributes = tep_db_fetch_array($attributes_query)) {
            $this->products[$index]['attributes'][$subindex] = array('option' => $attributes['products_options'],
//begin PayPal_Shopping_Cart_IPN
                                                                     'option_id' => $attributes['products_options_id'],
                                                                     'value_id' => $attributes['products_options_values_id'],
//end PayPal_Shopping_Cart_IPN
                                                                     'value' => $attributes['products_options_values'],
                                                                     'prefix' => $attributes['price_prefix'],
                                                                     'price' => $attributes['options_values_price'],
                                                                     'orders_products_attributes_id' => $attributes['orders_products_attributes_id']);

            $subindex++;
          }
        }
        $index++;
      }
    }
  }
  //end Order Editor
?>