<?php
/*
  $Id: ot_multiple_orders.php,v 1.15 2003/02/07 22:01:57 dgw_ Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  class ot_multiple_orders {
    var $title, $output;

    function ot_multiple_orders() {
      $this->code = 'ot_multiple_orders';
      $this->title = MODULE_ORDER_MULTIPLE_ORDERS_TOTAL_TITLE;
      $this->description = MODULE_ORDER_MULTIPLE_ORDERS_TOTAL_DESCRIPTION;
      $this->enabled = ((MODULE_ORDER_MULTIPLE_ORDERS_TOTAL_STATUS == 'true') ? true : false);
      $this->sort_order = MODULE_ORDER_MULTIPLE_ORDERS_TOTAL_SORT_ORDER;

      $this->output = array();
    }

    function process() {
      global $order, $currencies, $customer_id, $payment_method, $delete; 
    			
			if(($order->info['payment_method'] <> MODULE_PAYMENT_MULTIPLE_ORDERS_TEXT_TITLE)and(MODULE_PAYMENT_MULTIPLE_ORDERS_STATUS == true)){
        
				$total_query = tep_db_query("select sum(ot.value) as total from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS_TOTAL . " ot on (o.orders_id = ot.orders_id) where o.customers_id = '" . $customer_id . "' and o.paid_order = '0' and ot.class='ot_total'");
        $total = tep_db_fetch_array($total_query);

        $order->info['total'] += $total['total'];
			
        $this->output[] = array('title' => MODULE_ORDER_MULTIPLE_REMAINING,
                                'text' => $currencies->format($total['total'], true, $order->info['currency'], $order->info['currency_value']),
                                'value' => $total['total']);
			}												
    }


    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_MULTIPLE_ORDERS_TOTAL_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
      }

      return $this->_check;
    }

    function keys() {
      return array('MODULE_ORDER_MULTIPLE_ORDERS_TOTAL_STATUS', 'MODULE_ORDER_MULTIPLE_ORDERS_TOTAL_SORT_ORDER');
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Display remaining orders to pay', 'MODULE_ORDER_MULTIPLE_ORDERS_TOTAL_STATUS', 'true', 'Do you want to display the order shipping cost?', '6', '1','tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ORDER_MULTIPLE_ORDERS_TOTAL_SORT_ORDER', '2', 'Sort order of display.', '6', '2', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }
  }
?>
