<?php
/*
  $Id: linkpoint_api.php,v 1.48 2003/04/10 21:42:30 project3000 Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
  Copyright (c) 2003 osCommerce
  Released under the GNU General Public License
*/



      

  class linkpoint_api {
    var $code, $title, $description, $enabled, $cc_type, $transtype, $transmode, $zipcode, $states, $bstate, $sstate;
// class constructor
    function linkpoint_api() {
      global $order;
      $this->code = 'linkpoint_api';
      $this->title = MODULE_PAYMENT_LINKPOINT_API_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_LINKPOINT_API_TEXT_DESCRIPTION;
      $this->enabled = ((MODULE_PAYMENT_LINKPOINT_API_STATUS == 'True') ? true : false);
      $this->sort_order = MODULE_PAYMENT_LINKPOINT_API_SORT_ORDER;
      $this->states = $this->_state_list();

      if ((int)MODULE_PAYMENT_LINKPOINT_API_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_LINKPOINT_API_ORDER_STATUS_ID;
      }

      if (is_object($order)) $this->update_status();

      
    }



// class methods
    function update_status() {
      global $order;
      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_LINKPOINT_API_ZONE > 0) ) {
        $check_flag = false;
        $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_LINKPOINT_API_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
        while ($check = tep_db_fetch_array($check_query)) {
          if ($check['zone_id'] < 1) {
            $check_flag = true;
            break;
          } elseif ($check['zone_id'] == $order->billing['zone_id']) {
            $check_flag = true;
            break;
          }
        }
        if ($check_flag == false) {
          $this->enabled = false;
        }
      }
    }

    function javascript_validation() {
      $js = '  if (payment_value == "' . $this->code . '") {' . "\n" .
            '    var cc_owner = document.checkout_payment.linkpoint_api_cc_owner.value;' . "\n" .
            '    var cc_number = document.checkout_payment.linkpoint_api_cc_number.value;' . "\n" .
            '    if (cc_owner == "" || cc_owner.length < ' . CC_OWNER_MIN_LENGTH . ') {' . "\n" .
            '      error_message = error_message + "' . MODULE_PAYMENT_LINKPOINT_API_TEXT_JS_CC_OWNER . '";' . "\n" .
            '      error = 1;' . "\n" .
            '    }' . "\n" .
            '    if (cc_number == "" || cc_number.length < ' . CC_NUMBER_MIN_LENGTH . ') {' . "\n" .
            '      error_message = error_message + "' . MODULE_PAYMENT_LINKPOINT_API_TEXT_JS_CC_NUMBER . '";' . "\n" .
            '      error = 1;' . "\n" .
            '    }' . "\n" .
            '  }' . "\n";

      return $js;
    }

    function selection() {
      global $order;

      for ($i=1; $i<13; $i++) {
        $expires_month[] = array('id' => sprintf('%02d', $i), 'text' => strftime('%B',mktime(0,0,0,$i,1,2000)));
      }

      $today = getdate(); 
      for ($i=$today['year']; $i < $today['year']+10; $i++) {
        $expires_year[] = array('id' => strftime('%y',mktime(0,0,0,1,1,$i)), 'text' => strftime('%Y',mktime(0,0,0,1,1,$i)));
      }
      $selection = array('id' => $this->code,
                         'module' => $this->title,
                         'fields' => array(array('title' => MODULE_PAYMENT_LINKPOINT_API_TEXT_CREDIT_CARD_OWNER,
                                                 'field' => tep_draw_input_field('linkpoint_api_cc_owner', $order->billing['firstname'] . ' ' . $order->billing['lastname'])),
                                           array('title' => MODULE_PAYMENT_LINKPOINT_API_TEXT_CREDIT_CARD_NUMBER,
                                                 'field' => tep_draw_input_field('linkpoint_api_cc_number')),
        array('title' => MODULE_PAYMENT_LINKPOINT_API_TEXT_CREDIT_CARD_CHECK_VALUE,
                                                 'field' => tep_draw_input_field('linkpoint_api_cc_cvm', '', 'size="4" maxlength="4"') . '&nbsp;&nbsp;(last 3 or 4 digits on back of credit card)'),
                                           array('title' => MODULE_PAYMENT_LINKPOINT_API_TEXT_CREDIT_CARD_EXPIRES,
                                                 'field' => tep_draw_pull_down_menu('linkpoint_api_cc_expires_month', $expires_month) . '&nbsp;' . tep_draw_pull_down_menu('linkpoint_api_cc_expires_year', $expires_year))));
      return $selection;
    }

    function pre_confirmation_check() {
      global $HTTP_POST_VARS;

      include(DIR_WS_CLASSES . 'cc_validation.php');

      $cc_validation = new cc_validation();
      $result = $cc_validation->validate($HTTP_POST_VARS['linkpoint_api_cc_number'], $HTTP_POST_VARS['linkpoint_api_cc_expires_month'], $HTTP_POST_VARS['linkpoint_api_cc_expires_year']);
      $error = '';
      switch ($result) {
        case -1:
          $error = sprintf(TEXT_CCVAL_ERROR_UNKNOWN_CARD, substr($cc_validation->cc_number, 0, 4));
          break;
        case -2:
        case -3:
        case -4:
          $error = TEXT_CCVAL_ERROR_INVALID_DATE;
          break;
        case false:
          $error = TEXT_CCVAL_ERROR_INVALID_NUMBER;
          break;
      }

      if ( ($result == false) || ($result < 1) ) {
        $payment_error_return = 'payment_error=' . $this->code . '&error=' . urlencode($error) . '&linkpoint_api_cc_owner=' . urlencode($HTTP_POST_VARS['linkpoint_api_cc_owner']) . '&linkpoint_api_cc_expires_month=' . $HTTP_POST_VARS['linkpoint_api_cc_expires_month'] . '&linkpoint_api_cc_expires_year=' . $HTTP_POST_VARS['linkpoint_api_cc_expires_year'] . '&linkpoint_api_cc_cvm=' . $HTTP_POST_VARS['linkpoint_api_cc_cvm'];

        tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
      }

      $this->cc_owner = $HTTP_POST_VARS['linkpoint_api_cc_owner'];
      $this->cc_card_type = $cc_validation->cc_type;
      $this->cc_card_number = $cc_validation->cc_number;
      $this->cc_expiry_month = $cc_validation->cc_expiry_month;
      $this->cc_expiry_year = $cc_validation->cc_expiry_year;
      $this->cc_cvm = $HTTP_POST_VARS['linkpoint_api_cc_cvm'];
      $this->cc_nocvm = $HTTP_POST_VARS['linkpoint_api_cc_nocvm'];
    }

    function confirmation() {
      global $HTTP_POST_VARS;

      $confirmation = array('title' => $this->title . ': ' . $this->cc_card_type,
                            'fields' => array(array('title' => MODULE_PAYMENT_LINKPOINT_API_TEXT_CREDIT_CARD_OWNER,
                                                    'field' => $HTTP_POST_VARS['linkpoint_api_cc_owner']),
                                              array('title' => MODULE_PAYMENT_LINKPOINT_API_TEXT_CREDIT_CARD_NUMBER,
                                                    'field' => substr($this->cc_card_number, 0, 4) . str_repeat('X', (strlen($this->cc_card_number) - 8)) . substr($this->cc_card_number, -4)),
                                              array('title' => MODULE_PAYMENT_LINKPOINT_API_TEXT_CREDIT_CARD_EXPIRES,
                                                    'field' => strftime('%B, %Y', mktime(0,0,0,$HTTP_POST_VARS['linkpoint_api_cc_expires_month'], 1, '20' . $HTTP_POST_VARS['linkpoint_api_cc_expires_year'])))));
        
        
      return $confirmation;
    }

    function process_button() {
      global $HTTP_SERVER_VARS, $order, $customer_id;
           
     
     
   
    
     
      $sequence = rand(1, 1000);
      $process_button_string = tep_draw_hidden_field('cc_owner', $this->cc_owner) .
                               tep_draw_hidden_field('cc_expires', $this->cc_expiry_month . substr($this->cc_expiry_year, -2)) .
                               tep_draw_hidden_field('cc_expires_month', $this->cc_expiry_month) .
                               tep_draw_hidden_field('cc_expires_year', substr($this->cc_expiry_year, -2)) .
                               tep_draw_hidden_field('cc_type', $this->cc_card_type) .
                               tep_draw_hidden_field('cc_number', $this->cc_card_number) .
                               tep_draw_hidden_field('userid', $customer_id) .
                               tep_draw_hidden_field('cc_cvv', $this->cc_cvm);
                       
                               
                               if ($order->billing['country']['iso_code_2'] == 'US') {
		 $this->bstate = $this->states[strtoupper($order->billing['state'])];
     if ($this->bstate == '') {
     $this->bstate = $order->billing[state];
     }
     $process_button_string .= tep_draw_hidden_field('bstate', $this->bstate);
     }
     else {
	 $process_button_string .= tep_draw_hidden_field('bstate', $order->billing[state]);
	 }	
	 
	 if ($order->delivery['country']['iso_code_2'] == 'US') {
		 $this->sstate = $this->states[strtoupper($order->delivery['state'])];
     if ($this->sstate == '') {
     $this->sstate = $order->delivery[state];
     }
     $process_button_string .= tep_draw_hidden_field('sstate', $this->sstate);
     }
	 else {
	 $process_button_string .= tep_draw_hidden_field('sstate', $order->delivery[state]);
	 }			
      return $process_button_string;
    }
    

  
    
 function before_process() {
      global $_POST, $_SERVER, $order, $cart, $db, $lp_response_array, $lp_order_id;

      require(DIR_FS_CATALOG . DIR_WS_MODULES . 'payment/linkpoint_api/lphp.php');

		 $order->info['cc_type'] = $_POST['cc_type'];
      $order->info['cc_owner'] = $_POST['cc_owner'];
      $order->info['cc_cvv'] = $_POST['cc_cvv'];

        
        $mylphp = new lphp;

        // Build Info to send to Gateway

	$myorder["host"]       = MODULE_PAYMENT_LINKPOINT_API_SERVER;
    $myorder["port"]       = "1129";
	$myorder["keyfile"]=(DIR_FS_CATALOG. DIR_WS_MODULES . 'payment/linkpoint_api/' . MODULE_PAYMENT_LINKPOINT_API_LOGIN . '.pem');
	$myorder["configfile"] = MODULE_PAYMENT_LINKPOINT_API_LOGIN;        // Store number

	$myorder["ordertype"]  = strtoupper(MODULE_PAYMENT_LINKPOINT_API_AUTHORIZATION_MODE);

      switch (MODULE_PAYMENT_LINKPOINT_API_TRANSACTION_MODE_RESPONSE) {
    case "Live": $myorder["result"] = "LIVE"; break;
    case "Test": $myorder["result"] = "GOOD"; break;
    case "Decline": $myorder["result"] = "DECLINE"; break;
      }

	$myorder["transactionorigin"] = "ECI";           // For credit card retail txns, set to RETAIL, for Mail order/telephone order, set to MOTO, for e-commerce, leave out or set to ECI
//	$myorder["oid"]               = "";  // Order ID number must be unique. If not set, gateway will assign one.
	$myorder["ponumber"]          = "1002";  // Needed for business credit cards
	$myorder["taxexempt"]          = "Y";  // Needed for business credit cards
	$myorder["terminaltype"]      = "UNSPECIFIED";    // Set terminaltype to POS for an electronic cash register or integrated POS system, STANDALONE for a point-of-sale credit card terminal, UNATTENDED for a self-service station, or UNSPECIFIED for e-commerce or other applications
	$myorder["ip"]                = $_SERVER['REMOTE_ADDR'];

//	$myorder["subtotal"]    = $order->info['subtotal'];
//	$myorder["tax"]         = $order->info['tax'];
//	$myorder["shipping"]    = $order->info['shipping_cost'];
	$grantotal = number_format($order->info['total'], 2);
      $myorder["chargetotal"] = str_replace(",", "", $grantotal);



	// CARD INFO
	$myorder["cardnumber"]   = $_POST['cc_number'];
	$myorder["cardexpmonth"] = $_POST['cc_expires_month'];
	$myorder["cardexpyear"]  = $_POST['cc_expires_year'];
	if (empty($_POST['cc_cvv'])) {
	$myorder["cvmindicator"] = "not_provided";
     }
    else {
    $myorder["cvmindicator"] = "provided";
    }
      $myorder["cvmvalue"]  = $_POST['cc_cvv'];

	// BILLING INFO
	$myorder["userid"]   = $_POST['userid'];
	$myorder["name"]     = $order->billing['firstname'] . ' ' . $order->billing['lastname'];
	$myorder["company"]  = $order->billing['company'];
	$myorder["address1"] = $order->billing['street_address'];
	$myorder["address2"] = $order->billing['suburb'];
	$myorder["city"]     = $order->billing['city'];
	$myorder["state"]    = $_POST['bstate'];
	$myorder["country"]  = $order->billing['country']['iso_code_2'];
	$myorder["phone"]    = $order->customer['telephone'];
	$myorder["email"]    = $order->customer['email_address'];
	$myorder["addrnum"]  = $order->billing['street_address'];   // Required for AVS. If not provided, transactions will downgrade.
	$myorder["zip"]      = $order->billing['postcode'];  // Required for AVS. If not provided, transactions will downgrade.

	// SHIPPING INFO
	$myorder["sname"]     = $order->delivery['firstname'] . ' ' . $order->delivery['lastname'];
	$myorder["saddress1"] = $order->delivery['street_address'];
	$myorder["saddress2"] = $order->delivery['suburb'];
	$myorder["scity"]     = $order->delivery['city'];
	$myorder["sstate"]    = $_POST['sstate'];
	$myorder["szip"]      = $order->delivery['postcode'];
	$myorder["scountry"]  = $order->delivery['country']['iso_code_2'];
	

     // $order->products[$i]['price'] + $cart->attributes_price($products[$i]['id']) 
     // if (isset($order->products[$i]['attributes']) && (($k = sizeof ($order->products[$i]['attributes'])) > 0 )) {
     // for ($j = 0; $j < $k; $j++) {    } }
        
// description needs to be limited to 100 chars

 for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {   
    $api = htmlentities($order->products[$i]['name'], ENT_QUOTES, 'UTF-8');
    if (strlen($api) > '100') {
         $descrip = substr($api, 0, 100);
         }
      else {
      $descrip = $api;
      }
    $iprice = number_format($order->products[$i]['price'], 2);
    $items = array (
		'id' 			=> $order->products[$i]['id'],
	    'description' 	=> $descrip,
		'quantity' 		=> $order->products[$i]['qty'],
		'price'		=> str_replace(",", "", $iprice) );

   $myorder["items"][$i] = $items;
 	}
	


	
	// MISC
//	$myorder["comments"] = "Repeat customer. Ship immediately.";
   $myorder["debugging"] = strtolower(MODULE_PAYMENT_LINKPOINT_API_DEBUG);  // for development only - not intended for production use

  // Send transaction.
	$result = $mylphp->curl_process($myorder);  // use curl methods
   

    // - SGS-000001: D:Declined:P:
    //- SGS-005005: Duplicate transaction.
    //	Begin Transaction Status does not = APPROVED
       
    if ($myorder['debugging'] == 'true') {
    exit;
    }
      
       if ($result["r_approved"] != "APPROVED" && strstr($result['r_error'], 'D:Declined')) {
tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . ' - ' . urlencode(MODULE_PAYMENT_LINKPOINT_API_TEXT_DECLINED_MESSAGE), 'SSL', true, false));
}
if ($result["r_approved"] != "APPROVED" && strstr($result['r_error'], 'R:Referral')) {
tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . ' - ' . urlencode(MODULE_PAYMENT_LINKPOINT_API_TEXT_DECLINED_MESSAGE), 'SSL', true, false));
}
if ($result["r_approved"] != "APPROVED" && strstr($result['r_error'], 'Duplicate transaction')) {
tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . ' - ' . urlencode(MODULE_PAYMENT_LINKPOINT_API_TEXT_DUPLICATE_MESSAGE), 'SSL', true, false));
} 
if ($result["r_approved"] != "APPROVED" && strstr($result['r_error'], 'SGS')) {
tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . ' - ' . urlencode($result["r_error"]), 'SSL', true, false));
}
if ($result["r_approved"] != "APPROVED" ) {
tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . ' - ' . urlencode(MODULE_PAYMENT_LINKPOINT_API_TEXT_ERROR_MESSAGE), 'SSL', true, false));
}
      
    
	//	End Transaction Status does not = APPROVED
  }

    function after_process() {
	  return false;
    }
    
      function get_error() {
      global $_GET;

      $error = array('title' => MODULE_PAYMENT_LINKPOINT_API_TEXT_ERROR,
                     'error' => stripslashes(urldecode($_GET['error'])));

      return $error;
      }

    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_LINKPOINT_API_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Linkpoint API module', 'MODULE_PAYMENT_LINKPOINT_API_STATUS', 'True', 'Do you want to accept Linkpoint payments?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Store Number', 'MODULE_PAYMENT_LINKPOINT_API_LOGIN', '000001', 'The 6 or 7 digit store number for LinkPoint. For Yourpay accounts you must enter your 10 digit store number.', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('LinkPoint Transaction Mode', 'MODULE_PAYMENT_LINKPOINT_API_TRANSACTION_MODE_RESPONSE', 'Live', '<strong>Live:</strong> Use for live transactions<br /><strong>Test:</strong> Use for test transactions', '6', '0', 'tep_cfg_select_option(array(\'Live\', \'Test\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Authorization Type', 'MODULE_PAYMENT_LINKPOINT_API_AUTHORIZATION_MODE', 'Preauth', 'Preauth will reserve the funds on the credit card. Sale will immediately charge the card.', '6', '0', 'tep_cfg_select_option(array(\'Preauth\', \'Sale\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('LinkPoint server', 'MODULE_PAYMENT_LINKPOINT_API_SERVER', 'secure.linkpt.net', 'LinkPoint secure server', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Debugging', 'MODULE_PAYMENT_LINKPOINT_API_DEBUG', 'False', 'Only use for troubleshooting errors.', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_LINKPOINT_API_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_LINKPOINT_API_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '2', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_LINKPOINT_API_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
    }
    
    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
     return array( 'MODULE_PAYMENT_LINKPOINT_API_STATUS', 'MODULE_PAYMENT_LINKPOINT_API_LOGIN', 'MODULE_PAYMENT_LINKPOINT_API_TRANSACTION_MODE_RESPONSE', 'MODULE_PAYMENT_LINKPOINT_API_AUTHORIZATION_MODE', 'MODULE_PAYMENT_LINKPOINT_API_SERVER', 'MODULE_PAYMENT_LINKPOINT_API_DEBUG', 'MODULE_PAYMENT_LINKPOINT_API_SORT_ORDER', 'MODULE_PAYMENT_LINKPOINT_API_ZONE', 'MODULE_PAYMENT_LINKPOINT_API_ORDER_STATUS_ID');
         }

 
 function _state_list() {
  $list = array('ALABAMA' => 'AL',
  'ALASKA' => 'AK' ,
  'ARIZONA' => 'AZ' ,
  'ARKANSAS' => 'AR' ,
  'CALIFORNIA' => 'CA' ,
  'COLORADO' => 'CO' ,
  'CONNECTICUT' => 'CT' ,
  'DELAWARE' => 'DE' ,
  'DISTRICT OF COLUMBIA' => 'DC' ,
  'FLORIDA' => 'FL' ,
  'GEORGIA' => 'GA' ,
  'HAWAII' => 'HI' ,
  'IDAHO' => 'ID' ,
  'ILLINOIS' => 'IL' ,
  'INDIANA' => 'IN' ,
  'IOWA' => 'IA' ,
  'KANSAS' => 'KS' ,
  'KENTUCKY' => 'KY' ,
  'LOUISIANA' => 'LA' ,
  'MAINE' => 'ME' ,
  'MARYLAND' => 'MD' ,
  'MASSACHUSETTS' => 'MA' ,
  'MICHIGAN' => 'MI' ,
  'MINNESOTA' => 'MN' ,
  'MISSISSIPPI' => 'MS' ,
  'MISSOURI' => 'MO' ,
  'MONTANA' => 'MT' ,
  'NEBRASKA' => 'NE' ,
  'NEVADA' => 'NV' ,
  'NEW HAMPSHIRE' => 'NH' ,
  'NEW JERSEY' => 'NJ' ,
  'NEW MEXICO' => 'NM' ,
  'NEW YORK' => 'NY' ,
  'NORTH CAROLINA' => 'NC' ,
  'NORTH DAKOTA' => 'ND' ,
  'OHIO' => 'OH' ,
  'OKLAHOMA' => 'OK' ,
  'OREGON' => 'OR' ,
  'PENNSYLVANIA' => 'PA' ,
  'RHODE ISLAND' => 'RI' ,
  'SOUTH CAROLINA' => 'SC' ,
  'SOUTH DAKOTA' => 'SD' ,
  'TENNESSEE' => 'TN' ,
  'TEXAS' => 'TX' ,
  'UTAH' => 'UT' ,
  'VERMONT' => 'VT' ,
  'VIRGINIA' => 'VA' ,
  'WASHINGTON' => 'WA' ,
  'WEST VIRGINIA' => 'WV' ,
  'WISCONSIN' => 'WI' ,
  'WEST VIRGINIA' => 'WV' ,
  'WYOMING' => 'WY');
  return $list;
  }
  }
  
  
  
?>