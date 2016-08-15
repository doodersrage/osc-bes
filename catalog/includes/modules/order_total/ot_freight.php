<?php
/*
  $Id: ot_freight.php,v 1.15 2003/02/07 22:01:57 dgw_ Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  class ot_freight {
    var $title, $output;

    function ot_freight() {
      $this->code = 'ot_freight';
      $this->title = MODULE_ORDER_TOTAL_FREIGHT_TITLE;
      $this->description = MODULE_ORDER_TOTAL_FREIGHT_DESCRIPTION;
      $this->enabled = ((MODULE_ORDER_TOTAL_FREIGHT_STATUS == 'true') ? true : false);
      $this->sort_order = MODULE_ORDER_TOTAL_FREIGHT_SORT_ORDER;

      $this->output = array();
    }

    function process() {
      global $order, $currencies;

      if (MODULE_ORDER_TOTAL_FREIGHT_FREE_SHIPPING == 'true') {
        switch (MODULE_ORDER_TOTAL_FREIGHT_DESTINATION) {
          case 'national':
            if ($order->delivery['country_id'] == STORE_COUNTRY) $pass = true; break;
          case 'international':
            if ($order->delivery['country_id'] != STORE_COUNTRY) $pass = true; break;
          case 'both':
            $pass = true; break;
          default:
            $pass = false; break;
        }

// modified by splautz to ensure free freight shipping total doesn't include tax unless price is displayed with tax
//      if ( ($pass == true) && ( ($order->info['total'] - $order->info['shipping_cost']) >= MODULE_ORDER_TOTAL_FREIGHT_FREE_SHIPPING_OVER) ) {
        if ( ($pass == true) && ($order->info['subtotal'] >= MODULE_ORDER_TOTAL_FREIGHT_FREE_SHIPPING_OVER) ) {
          $order->info['freight_method'] = $this->title;
          $order->info['shipping_cost'] -= $order->info['shipping_cost_1'];
          $order->info['total'] -= $order->info['shipping_cost_1'];
          $order->info['shipping_cost_1'] = 0;
        }
      }

      if (tep_not_null($order->info['freight_method'])) {
        $module = substr($GLOBALS['shipping'][1]['id'], 0, strpos($GLOBALS['shipping'][1]['id'], '_'));
        if ($GLOBALS[$module]->tax_class > 0) {
          $freight_tax = tep_get_tax_rate($GLOBALS[$module]->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
          $freight_tax_description = tep_get_tax_description($GLOBALS[$module]->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);

          $order->info['tax'] += tep_calculate_tax($order->info['shipping_cost_1'], $freight_tax);
          $order->info['tax_groups']["$freight_tax_description"] += tep_calculate_tax($order->info['shipping_cost_1'], $freight_tax);
          $order->info['total'] += tep_calculate_tax($order->info['shipping_cost_1'], $freight_tax);

          if (DISPLAY_PRICE_WITH_TAX == 'true') {
            $order->info['shipping_cost_1'] += tep_calculate_tax($order->info['shipping_cost_1'], $freight_tax);
            $order->info['shipping_cost'] += tep_calculate_tax($order->info['shipping_cost_1'], $freight_tax);
          }
        }

        $this->output[] = array('title' => $order->info['freight_method'] . ':',
                                'text' => $currencies->format($order->info['shipping_cost_1'], true, $order->info['currency'], $order->info['currency_value']),
                                'value' => $order->info['shipping_cost_1']);
      }
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_FREIGHT_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
      }

      return $this->_check;
    }

    function keys() {
      return array('MODULE_ORDER_TOTAL_FREIGHT_STATUS', 'MODULE_ORDER_TOTAL_FREIGHT_SORT_ORDER', 'MODULE_ORDER_TOTAL_FREIGHT_FREE_SHIPPING', 'MODULE_ORDER_TOTAL_FREIGHT_FREE_SHIPPING_OVER', 'MODULE_ORDER_TOTAL_FREIGHT_DESTINATION');
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Display Shipping', 'MODULE_ORDER_TOTAL_FREIGHT_STATUS', 'true', 'Do you want to display the freight shipping cost?', '6', '1','tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ORDER_TOTAL_FREIGHT_SORT_ORDER', '2', 'Sort order of display.', '6', '2', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Allow Free Shipping', 'MODULE_ORDER_TOTAL_FREIGHT_FREE_SHIPPING', 'false', 'Do you want to allow free shipping for freight items?', '6', '3', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, date_added) values ('Free Shipping For Orders Over', 'MODULE_ORDER_TOTAL_FREIGHT_FREE_SHIPPING_OVER', '50', 'Provide free freight shipping for orders over the set amount.', '6', '4', 'currencies->format', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Provide Free Shipping For Orders Made', 'MODULE_ORDER_TOTAL_FREIGHT_DESTINATION', 'national', 'Provide free freight shipping for orders sent to the set destination.', '6', '5', 'tep_cfg_select_option(array(\'national\', \'international\', \'both\'), ', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }
  }
?>
