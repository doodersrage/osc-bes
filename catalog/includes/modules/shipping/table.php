<?php
/*
  $Id: table.php,v 1.27 2003/02/05 22:41:52 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  class table {
    var $code, $title, $description, $icon, $enabled, $numberWays=3;  // modified by splautz for multiple table methods

// class constructor
    function table() {
      global $order;

      $this->code = 'table';
      $this->title = MODULE_SHIPPING_TABLE_TEXT_TITLE;
      $this->description = MODULE_SHIPPING_TABLE_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_SHIPPING_TABLE_SORT_ORDER;
      $this->icon = '';
      $this->tax_class = MODULE_SHIPPING_TABLE_TAX_CLASS;
      $this->enabled = ((MODULE_SHIPPING_TABLE_STATUS == 'True') ? true : false);

      if ( ($this->enabled == true) && ((int)MODULE_SHIPPING_TABLE_ZONE > 0) ) {
        $check_flag = false;
        $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_SHIPPING_TABLE_ZONE . "' and zone_country_id = '" . $order->delivery['country']['id'] . "' order by zone_id");
        while ($check = tep_db_fetch_array($check_query)) {
          if ($check['zone_id'] < 1) {
            $check_flag = true;
            break;
          } elseif ($check['zone_id'] == $order->delivery['zone_id']) {
            $check_flag = true;
            break;
          }
        }

        if ($check_flag == false) {
          $this->enabled = false;
        }
      }
    }

// class methods
    function quote($method = '') {
      global $order, $cart, $shipping_weight_x, $shipping_num_boxes_x;

      if (MODULE_SHIPPING_TABLE_MODE == 'price') {
        $order_total = $cart->show_total();
      } else {
        $order_total = $shipping_weight_x[1];
      }

      $this->quotes = array('id' => $this->code,
                            'module' => MODULE_SHIPPING_TABLE_TEXT_TITLE,
                            'methods' => array());

  // added by splautz for multiple table methods
      for($i=1; $i<=$this->numberWays; $i++) if (!$method || $method == $this->code.$i) {
        $shipping = -1;

  // modified by splautz for multiple table methods
        $table_cost = split("[:,]" , constant('MODULE_SHIPPING_TABLE_COST'.($i>1?$i:'')));
        $size = sizeof($table_cost);
        for ($ii=0, $n=$size; $ii<$n; $ii+=2) {
          if ($order_total <= $table_cost[$ii]) {
            $shipping = $table_cost[$ii+1];
            break;
          }
        }
        if ($shipping == -1) break;

        if (MODULE_SHIPPING_TABLE_MODE == 'weight') {
          $shipping = $shipping * $shipping_num_boxes_x[1];
        }

        $this->quotes['methods'][] = array('id' => $this->code.$i,  // modified by splautz for multiple table methods
                                           'title' => constant('MODULE_SHIPPING_TABLE_TEXT_WAY'.($i>1?$i:'')),  // modified by splautz for multiple table methods
                                           'cost' => $shipping + ($shipping_num_boxes_x[1]?MODULE_SHIPPING_TABLE_HANDLING:0));  // modified by splautz for free shipping

      }  // added by splautz for multiple table methods

      if ($this->tax_class > 0) {
        $this->quotes['tax'] = tep_get_tax_rate($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
      }

      if (tep_not_null($this->icon)) $this->quotes['icon'] = tep_image($this->icon, $this->title);

      return $this->quotes;
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_SHIPPING_TABLE_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Enable Table Method', 'MODULE_SHIPPING_TABLE_STATUS', 'True', 'Do you want to offer table rate shipping?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Table Method', 'MODULE_SHIPPING_TABLE_MODE', 'weight', 'The shipping cost is based on the order total or the total weight of the items ordered.', '6', '0', 'tep_cfg_select_option(array(\'weight\', \'price\'), ', now())");
      for($i=1; $i<=$this->numberWays; $i++) {  // modified by splautz for multiple table methods
        tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Shipping Table $i', 'MODULE_SHIPPING_TABLE_COST".($i>1?$i:'')."', '".($i==1?'25:8.50,50:5.50,10000:0.00':'')."', 'Enter shipping cost table $i.".($i==1?' The shipping cost is based on the total cost or weight of items. Example: 25:8.50,50:5.50,etc.. Up to 25 charge 8.50, from there to 50 charge 5.50, etc.':'')."', '6', '0', now())");
      }
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Handling Fee', 'MODULE_SHIPPING_TABLE_HANDLING', '0', 'Handling fee for this shipping method.', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Tax Class', 'MODULE_SHIPPING_TABLE_TAX_CLASS', '0', 'Use the following tax class on the shipping fee.', '6', '0', 'tep_get_tax_class_title', 'tep_cfg_pull_down_tax_classes(', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Shipping Zone', 'MODULE_SHIPPING_TABLE_ZONE', '0', 'If a zone is selected, only enable this shipping method for that zone.', '6', '0', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_SHIPPING_TABLE_SORT_ORDER', '0', 'Sort order of display.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {  // modified by splautz for multiple table methods
      $keys = array('MODULE_SHIPPING_TABLE_STATUS', 'MODULE_SHIPPING_TABLE_MODE');
      for($i=1; $i<=$this->numberWays; $i++) {
        $keys[] = 'MODULE_SHIPPING_TABLE_COST'.($i>1?$i:'');
      }
      $keys = array_merge($keys, array('MODULE_SHIPPING_TABLE_HANDLING', 'MODULE_SHIPPING_TABLE_TAX_CLASS', 'MODULE_SHIPPING_TABLE_ZONE', 'MODULE_SHIPPING_TABLE_SORT_ORDER'));
      return $keys;
    }
  }
?>