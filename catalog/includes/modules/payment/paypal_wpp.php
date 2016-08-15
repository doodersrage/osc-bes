<?php

class paypal_wpp {
var $code, $title, $description, $enabled, $payment_action;

// class constructor
function paypal_wpp() {
global $order;

$this->code = 'paypal_wpp';
$this->title = MODULE_PAYMENT_PAYPAL_WPP_TEXT_TITLE;
$this->description = MODULE_PAYMENT_PAYPAL_WPP_TEXT_DESCRIPTION;
$this->sort_order = MODULE_PAYMENT_PAYPAL_WPP_SORT_ORDER;
$this->enabled = ((MODULE_PAYMENT_PAYPAL_WPP_STATUS == 'True') ? true : false);

if ((int)MODULE_PAYMENT_PAYPAL_WPP_ORDER_STATUS_ID > 0) {
$this->order_status = MODULE_PAYMENT_PAYPAL_WPP_ORDER_STATUS_ID;
}

if (is_object($order)) $this->update_status();
}





// class methods
function update_status() {
global $order;

if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_PAYPAL_WPP_ZONE > 0) ) {
$check_flag = false;
$check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_PAYPAL_WPP_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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
'    var paypal_cc_firstname = document.checkout_payment.paypal_cc_firstname.value;' . "\n" .
'    var paypal_cc_lastname = document.checkout_payment.paypal_cc_lastname.value;' . "\n" .
'    var paypal_cc_number = document.checkout_payment.paypal_cc_number.value;' . "\n" .
'    var paypal_cc_cvv2 = document.checkout_payment.paypal_cc_cvv2.value;' . "\n" .
'    if (paypal_cc_firstname == "" || paypal_cc_firstname.length < ' . ENTRY_FIRST_NAME_MIN_LENGTH . ') {' . "\n" .
'      error_message = error_message + "' . MODULE_PAYMENT_PAYPAL_WPP_TEXT_JS_CC_FIRSTNAME . '";' . "\n" .
'      error = 1;' . "\n" .
'    }' . "\n" .
'    if (paypal_cc_lastname == "" || paypal_cc_lastname.length < ' . ENTRY_LAST_NAME_MIN_LENGTH . ') {' . "\n" .
'      error_message = error_message + "' . MODULE_PAYMENT_PAYPAL_WPP_TEXT_JS_CC_LASTNAME . '";' . "\n" .
'      error = 1;' . "\n" .
'    }' . "\n" .
'    if (paypal_cc_number == "" || paypal_cc_number.length < ' . CC_NUMBER_MIN_LENGTH . ') {' . "\n" .
'      error_message = error_message + "' . MODULE_PAYMENT_PAYPAL_WPP_TEXT_JS_CC_NUMBER . '";' . "\n" .
'      error = 1;' . "\n" .
'    }' . "\n" .
'    if (paypal_cc_cvv2.length > 4) {' . "\n" .
'      error_message = error_message + "' . MODULE_PAYMENT_PAYPAL_WPP_TEXT_JS_CC_CVV2 . '";' . "\n" .
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
'module' => MODULE_PAYMENT_PAYPAL_WPP_IMAGE_DESCRIPTION,
'fields' => array(
array('title' => MODULE_PAYMENT_PAYPAL_WPP_TEXT_CREDIT_CARD_FIRSTNAME,
'field' => tep_draw_input_field('paypal_cc_firstname', $order->billing['firstname'])),
array('title' => MODULE_PAYMENT_PAYPAL_WPP_TEXT_CREDIT_CARD_LASTNAME,
'field' => tep_draw_input_field('paypal_cc_lastname', $order->billing['lastname'])),
array('title' => MODULE_PAYMENT_PAYPAL_WPP_TEXT_CREDIT_CARD_NUMBER,
'field' => tep_draw_input_field('paypal_cc_number')),
array('title' => MODULE_PAYMENT_PAYPAL_WPP_TEXT_CREDIT_CARD_CVV2,
'field' => tep_draw_input_field('paypal_cc_cvv2')),
array('title' => MODULE_PAYMENT_PAYPAL_WPP_TEXT_CREDIT_CARD_EXPIRES,
'field' => tep_draw_pull_down_menu('paypal_cc_expires_month', $expires_month) . '&nbsp;' . tep_draw_pull_down_menu('paypal_cc_expires_year', $expires_year))));

return $selection;
}

   



function pre_confirmation_check() {
global $HTTP_POST_VARS, $order, $languages_id;

include(DIR_WS_CLASSES . 'cc_validation.php');

$cc_validation = new cc_validation();
$result = $cc_validation->validate($HTTP_POST_VARS['paypal_cc_number'], $HTTP_POST_VARS['paypal_cc_expires_month'], $HTTP_POST_VARS['paypal_cc_expires_year']);

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
$payment_error_return = 'payment_error=' . $this->code . '&error=' . urlencode($error) . '&cc_owner=' . urlencode($HTTP_POST_VARS['paypal_cc_owner']) . '&cc_expires_month=' . $HTTP_POST_VARS['paypal_cc_expires_month'] . '&cc_expires_year=' . $HTTP_POST_VARS['paypal_cc_expires_year'];

tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
}

switch ($cc_validation->cc_type) {
case 'Master Card':
$this->cc_card_type = 'MasterCard';
break;
case 'American Express':
$this->cc_card_type = 'Amex';
break;
default:
$this->cc_card_type = $cc_validation->cc_type; // allowable: Visa, Discover
break;
}

$this->cc_card_number = $cc_validation->cc_number;
$this->cc_expires_month = $cc_validation->cc_expiry_month;
$this->cc_expires_year = $cc_validation->cc_expiry_year;


}

function confirmation() {
global $HTTP_POST_VARS;

$confirmation = array('title' => $this->title . ': ' . $this->cc_card_type,
'fields' => array(array('title' => MODULE_PAYMENT_PAYPAL_WPP_TEXT_CREDIT_CARD_FIRSTNAME,
'field' => $HTTP_POST_VARS['paypal_cc_firstname']),
array('title' => MODULE_PAYMENT_PAYPAL_WPP_TEXT_CREDIT_CARD_LASTNAME,
'field' => $HTTP_POST_VARS['paypal_cc_lastname']),
array('title' => MODULE_PAYMENT_PAYPAL_WPP_TEXT_CREDIT_CARD_NUMBER,
'field' => substr($this->cc_card_number, 0, 4) . str_repeat('X', (strlen($this->cc_card_number) - 8)) . substr($this->cc_card_number, -4)),
array('title' => MODULE_PAYMENT_PAYPAL_WPP_TEXT_CREDIT_CARD_CVV2,
'field' => $HTTP_POST_VARS['paypal_cc_cvv2']),
array('title' => MODULE_PAYMENT_PAYPAL_WPP_TEXT_CREDIT_CARD_EXPIRES,
'field' => strftime('%B, %Y', mktime(0,0,0,$HTTP_POST_VARS['paypal_cc_expires_month'], 1, '20' . $HTTP_POST_VARS['paypal_cc_expires_year'])))));

return $confirmation;
}




function process_button() {
global $HTTP_POST_VARS;
$process_button_string = tep_draw_hidden_field('paypal_cc_firstname', $HTTP_POST_VARS['paypal_cc_firstname']) .
tep_draw_hidden_field('paypal_cc_lastname', $HTTP_POST_VARS['paypal_cc_lastname']) .
tep_draw_hidden_field('paypal_cc_expires_month', $this->cc_expires_month) .
tep_draw_hidden_field('paypal_cc_expires_year', $this->cc_expires_year) .
tep_draw_hidden_field('paypal_cc_type', $this->cc_card_type) .
tep_draw_hidden_field('paypal_cc_number', $this->cc_card_number) .
tep_draw_hidden_field('paypal_cc_cvv2', $HTTP_POST_VARS['paypal_cc_cvv2']);  


return $process_button_string;
}






function before_process() {
global $HTTP_POST_VARS, $order;


ob_start(); 

$order->info['cc_type'] = $HTTP_POST_VARS['paypal_cc_type'];
$order->info['cc_number'] = substr($HTTP_POST_VARS['paypal_cc_number'], 0, 4) . str_repeat('X', (strlen($HTTP_POST_VARS['paypal_cc_number']) - 8)) . substr($HTTP_POST_VARS['paypal_cc_number'], -4);
$order->info['cc_owner'] = $HTTP_POST_VARS['paypal_cc_firstname']. ' ' .$HTTP_POST_VARS['paypal_cc_lastname'];
$order->info['cc_expires'] = $HTTP_POST_VARS['paypal_cc_expires_year'];
$this->payment_action='4'; 



//wpp configuration file
require_once('paypal_wpp/includes/config_wpp.inc.php'); 

//wpp library file
require_once('paypal_wpp/includes/lib.inc.php'); 

//wpp processing file 
require_once('paypal_wpp/includes/upc_direct_paypal.php'); 


switch($upc_results["Ack"]) { 

case "Success": //successful response received


//check to see what action to take

switch($paypal[action]) {


case 1: //setExpressCheckOut

//redirect user to PayPal to select checkout options
/*
echo "<script language='JavaScript1.3'>"; 
echo "window.location=\"$paypal[express_checkout_url]?cmd=_express-checkout&token=$upc_results[Token]\""; 
echo "</script>"; 
*/
tep_redirect("$paypal[express_checkout_url]?cmd=_express-checkout&token=$upc_results[Token]","SSL");

break;


case 3: //doExpressCheckOut success

include_once('./thankyou_express.php'); 

break; 


case 4: //doDirectPayment success 

$this->trans_id = $upc_results['TransactionID']; 
$this->avs = $upc_results['AVSCode']; 
$this->cvv2 = $upc_results['CVV2Code']; 

break; 

}  

break; 


case "Failure": //transaction error 


//redirect user and display error code and message from the gateway
tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'payment_error=' . $this->code . '&error=' . urlencode("($upc_results[ErrorCode]) $upc_results[LongMessage]"), 'SSL', true, false));


break;


default: //transaction error or warning


//redirect user and display general processing error
tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'payment_error=' . $this->code . '&error=' . urlencode(MODULE_PAYMENT_PAYPAL_WPP_TEXT_PROCESS_ERROR), 'SSL', true, false));


break; 

}


}

function after_process() {
global $insert_id;
tep_db_query("update ".TABLE_ORDERS_STATUS_HISTORY. " set comments = concat(if(trim(comments) != '', concat(trim(comments), '\n'), ''), 'Transaction ID: ".$this->trans_id."\nPayment Type: credit card\nPayment Status: Completed\nAVS Code: ".$this->avs."\nCVV2 Code: ".$this->cvv2."') where orders_id = ".$insert_id);
}




function get_error() {
global $HTTP_GET_VARS;

$error = array('title' => MODULE_PAYMENT_PAYPAL_WPP_TEXT_ERROR,
'error' => stripslashes(urldecode($HTTP_GET_VARS['error'])));

return $error;
}




function check() {
if (!isset($this->_check)) {
$check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_PAYPAL_WPP_STATUS'");
$this->_check = tep_db_num_rows($check_query);
}
return $this->_check;
}


function ec_check() {
$check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_PAYPAL_EC_STATUS'");
if($num_rows = tep_db_num_rows($check_query)) { return true; } else { return false; }
}


 

function install() {

//Direct Pay

tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Website Payments Pro Module', 'MODULE_PAYMENT_PAYPAL_WPP_STATUS', 'True', 'Do you want to accept credit card payments through WebSite Payments Pro?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_PAYPAL_WPP_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0' , now())");
tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_PAYPAL_WPP_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '2', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");
tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_PAYPAL_WPP_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Transaction Mode', 'MODULE_PAYMENT_PAYPAL_WPP_GATEWAY_SERVER', 'Test', 'Select Test for SandBox transactions.', '6', '6', 'tep_cfg_select_option(array(\'Test\',\'Live\'), ', now())");
tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function,  date_added) values ('Enable cURL Library', 'MODULE_PAYMENT_PAYPAL_WPP_USE_LIB_CURL', 'True', 'Set to True if PHP was compiled with libCurl support.', '6', '6','tep_cfg_select_option(array(\'True\', \'False\'),', now())");
tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('cURL Path', 'MODULE_PAYMENT_PAYPAL_WPP_CURL_PATH', '/usr/bin/curl', 'Absolute Path to cURL Program', '6', '6', now())");
tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('API Username', 'MODULE_PAYMENT_PAYPAL_WPP_USERNAME', 'sdk-seller_api1.sdk.com', 'PayPal API Username', '6', '6', now())");
tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('API Password', 'MODULE_PAYMENT_PAYPAL_WPP_PASSWORD', '12345678', 'PayPal API Password', '6', '6', now())");
tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('API Certificate File', 'MODULE_PAYMENT_PAYPAL_WPP_CERT_FILE', 'paypal_wpp/certs/cert_key_pem.txt', 'Enter your API certificate file name and path from catalog/', '6', '6', now())");
tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Payment Action', 'MODULE_PAYMENT_PAYPAL_WPP_PAYMENT_ACTION', 'Sale', 'PayPal Payment Action', '6', '6','tep_cfg_select_option(array(\'Sale\',\'Authorization\'), ', now())");   
tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('PayPal BN', 'MODULE_PAYMENT_PAYPAL_WPP_BN', 'OSCommerce-2.2MS2', 'Your PayPal BN identification code', '6', '6', now())");
tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('IPN Notification URL', 'MODULE_PAYMENT_PAYPAL_WPP_IPN_URL', '', 'Instant Payment Notification URL', '6', '6', now())");   


// Express Checkout


if(!$this->ec_check()) { 

tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable PayPal Express Checkout Module', 'MODULE_PAYMENT_PAYPAL_EC_STATUS', 'True', 'Do you want to accept credit/debit card payments through PayPal Express Checkout?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_PAYPAL_EC_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0' , now())");
tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_PAYPAL_EC_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '2', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");
tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_PAYPAL_EC_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Transaction Mode', 'MODULE_PAYMENT_PAYPAL_EC_GATEWAY_SERVER', 'Test', 'Select Test for SandBox transactions.', '6', '6', 'tep_cfg_select_option(array(\'Test\',\'Live\'), ', now())");
tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function,  date_added) values ('Enable cURL Library', 'MODULE_PAYMENT_PAYPAL_EC_USE_LIB_CURL', 'True', 'Set to True if PHP was compiled with libCurl support.', '6', '6','tep_cfg_select_option(array(\'True\', \'False\'),', now())");
tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('cURL Path', 'MODULE_PAYMENT_PAYPAL_EC_CURL_PATH', '/usr/bin/curl', 'Absolute Path to cURL Program', '6', '6', now())");
tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('API Username', 'MODULE_PAYMENT_PAYPAL_EC_USERNAME', 'sdk-seller_api1.sdk.com', 'PayPal API Username', '6', '6', now())");
tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('API Password', 'MODULE_PAYMENT_PAYPAL_EC_PASSWORD', '12345678', 'PayPal API Password', '6', '6', now())");
tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('API Certificate File', 'MODULE_PAYMENT_PAYPAL_EC_CERT_FILE', 'paypal_wpp/certs/cert_key_pem.txt', 'Enter your API certificate file name and path from catalog/', '6', '6', now())");
tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('PayPal BN', 'MODULE_PAYMENT_PAYPAL_EC_BN', 'OSCommerce-2.2MS2', 'Your PayPal BN identification code', '6', '6', now())");
tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Payment Action', 'MODULE_PAYMENT_PAYPAL_EC_PAYMENT_ACTION', 'Sale', 'PayPal Payment Action', '6', '6','tep_cfg_select_option(array(\'Sale\',\'Authorization\'), ', now())");   
tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('IPN Notification URL', 'MODULE_PAYMENT_PAYPAL_EC_IPN_URL', '', 'Instant Payment Notification URL', '6', '6', now())");   

}


}



function remove() {
tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
}

function keys() {
return array('MODULE_PAYMENT_PAYPAL_WPP_STATUS', 
'MODULE_PAYMENT_PAYPAL_WPP_ZONE', 
'MODULE_PAYMENT_PAYPAL_WPP_ORDER_STATUS_ID', 
'MODULE_PAYMENT_PAYPAL_WPP_SORT_ORDER', 
'MODULE_PAYMENT_PAYPAL_WPP_GATEWAY_SERVER', 
'MODULE_PAYMENT_PAYPAL_WPP_CERT_FILE', 
'MODULE_PAYMENT_PAYPAL_WPP_USERNAME', 
'MODULE_PAYMENT_PAYPAL_WPP_PASSWORD', 
'MODULE_PAYMENT_PAYPAL_WPP_BN',
'MODULE_PAYMENT_PAYPAL_WPP_IPN_URL',
'MODULE_PAYMENT_PAYPAL_WPP_PAYMENT_ACTION',
'MODULE_PAYMENT_PAYPAL_WPP_USE_LIB_CURL',
'MODULE_PAYMENT_PAYPAL_WPP_CURL_PATH'
);
}
}
?>
