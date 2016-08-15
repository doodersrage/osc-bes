<?php
/*
  $Id: po.php,v 1.2 2002/12/10 12:00:00 olby $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

  class po {
    var $code, $title, $description, $enabled;

// class constructor
    function po() {
      global $order;
      $this->code = 'po';
      $this->title = MODULE_PAYMENT_PO_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_PO_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_PAYMENT_PO_SORT_ORDER;
      $this->enabled = ((MODULE_PAYMENT_PO_STATUS == 'True') ? true : false);
      $this->email_footer = MODULE_PAYMENT_PO_TEXT_EMAIL_FOOTER;
    }

// class methods
    function javascript_validation()
		{
      $validation_string = 'if (payment_value == "' . $this->code . '") {' . "\n" .
                           '  var purchase_order_number = document.checkout_payment.purchase_order_number.value;' . "\n" .
                           '  if (purchase_order_number.length == 0) {' . "\n" .
                           '    error_message = error_message + "' . MODULE_PAYMENT_PO_TEXT_JS_PO_NUMBER . '";' . "\n" .
                           '    error = 1;' . "\n" .
                           '  }' . "\n" .
                           '}' . "\n";
      return $validation_string;
    }

    function selection()
		{
      $selection = array('id' => $this->code,
                         'module' => $this->title,
                         'fields' => array(array('title' => MODULE_PAYMENT_PO_TEXT_PO_NUMBER,
                                                 'field' => tep_draw_input_field('purchase_order_number'))));

      return $selection;
    }

    function pre_confirmation_check()
		{
      return false;
    }

    function confirmation()
		{
      global $HTTP_POST_VARS;

      $confirmation = array('title' => $this->title,
                            'fields' => array(array('title' => MODULE_PAYMENT_PO_TEXT_PO_NUMBER,
                                                    'field' => $HTTP_POST_VARS['purchase_order_number'])));

      return $confirmation;
    }

    function process_button() {
      global $HTTP_POST_VARS;

      $process_button_string = tep_draw_hidden_field('purchase_order_number', $HTTP_POST_VARS['purchase_order_number']);

      return $process_button_string;
    }

    function before_process() {
      return false;
    }

    function after_process() {
      return false;
    }

    function get_error() {
      return false;
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_PO_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Allow Purchase Orders', 'MODULE_PAYMENT_PO_STATUS', 'True', 'Do you want to accept Purchase Orders?', '6', '6',  'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_PAYMENT_PO_SORT_ORDER', '0', 'Sort order of display.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_PAYMENT_PO_STATUS', 'MODULE_PAYMENT_PO_SORT_ORDER');
    }
  }
?>
