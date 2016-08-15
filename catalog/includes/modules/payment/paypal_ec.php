<?php

class paypal_ec {

var $code, $title, $description, $enabled,$payment_action; 

// class constructor
function paypal_ec() {

global $order;

$this->code = 'paypal_ec';
$this->title = MODULE_PAYMENT_PAYPAL_EC_TEXT_TITLE;
$this->description = MODULE_PAYMENT_PAYPAL_EC_TEXT_DESCRIPTION;
$this->sort_order = MODULE_PAYMENT_PAYPAL_EC_SORT_ORDER;
$this->enabled = ((MODULE_PAYMENT_PAYPAL_EC_STATUS == 'True') ? true : false);

if ((int)MODULE_PAYMENT_PAYPAL_PAYPAL_EC_ORDER_STATUS_ID > 0) {
$this->order_status = MODULE_PAYMENT_PAYPAL_EC_ORDER_STATUS_ID;
}

if (is_object($order)) $this->update_status();


}







function update_status() {
global $order;

if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_PAYPAL_EC_ZONE > 0) ) {
$check_flag = false;
$check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_PAYPAL_EC_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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





//javascript validation not required, showing image only 
function javascript_validation() {
return false;
}




//get express checkout image
function selection() {
return array('id' => $this->code,
'module' => MODULE_PAYMENT_PAYPAL_EC_IMAGE_DESCRIPTION);
}





function pre_confirmation_check() {


global $order, $billto, $customer_id, $languages_id, $paypal_token, $pp_payer_id;



//set locale
$language_code = "US";
$languages_query = tep_db_query("select code from " . TABLE_LANGUAGES . " where languages_id = ".$languages_id);

if(tep_db_num_rows($languages_query) > 0) {
$languages = tep_db_fetch_array($languages_query);

switch ($languages['code']) {

case 'en':
$language_code = 'US';
break;

case 'de':
$language_code = 'DE';
break;

case 'fr':
$language_code = 'FR';
break;

case 'it':
$language_code = 'IT';
break;

case 'ja':
$language_code = 'JP';
break;

default:
break;
}
}




}

function confirmation() {
return false;
}

function process_button() {
return "";
}

function before_process() {


global $order,$paypal_token,$pp_token,$pp_payer_id;


ob_start(); 

//check to see if express checkout was used early on in the order process
if(!(tep_session_is_registered('paypal_token') && tep_session_is_registered('pp_payer_id'))) { $_REQUEST['express']=1;  } else {  $_REQUEST['express']=3; }

$_REQUEST['amount']=number_format($order->info['total'], 2);

//wpp configuration file
require_once('paypal_wpp/includes/config_ec.inc.php'); 

//wpp library file
require_once('paypal_wpp/includes/lib.inc.php'); 

//wpp processing file 
require_once('paypal_wpp/includes/upc_direct_paypal.php'); 




switch($_REQUEST[express]) { 

case 1: 

$paypal_token=$upc_results['Token']; 


tep_redirect("$paypal[express_checkout_url]?cmd=_express-checkout&token=$paypal_token","SSL");


break; 

case 3:

switch($upc_results["Ack"]) { 

case "Success": //successful response received


//check to see what action to take

$this->trans_id = $upc_results['TransactionID']; 
$this->payment_status = $upc_results['PaymentStatus']; 
$this->payment_type = $upc_results['PaymentType']; 

switch ($this->payment_status) {

case 'Completed': 
break;


case 'Pending':

$this->pending_reason = $upc_results['PendingReason'];

$order->info['order_status'] = 1;
break;

default:

$upc_results['detail']=$upc_results['Token']; 

tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode(MODULE_PAYMENT_PAYPAL_EC_TEXT_DECLINED_MESSAGE), 'SSL', true, false));

break;




}

//unregister session variables only if the order is successful
tep_session_unregister('paypal_token');
tep_session_unregister('pp_token');
tep_session_unregister('pp_payer_id');


break; 


case "Failure": //transaction error 

//redirect user and display error code and message from the gateway
tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode("($upc_results[ErrorCode]) $upc_results[LongMessage]"), 'SSL', true, false));

break;


default: //transaction error or warning

//redirect user and display general processing error
tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode(MODULE_PAYMENT_PAYPAL_EC_TEXT_PROCESS_ERROR), 'SSL', true, false));



break; 

}

break; 

}





}

function after_process() {
global $insert_id;
tep_db_query("update ".TABLE_ORDERS_STATUS_HISTORY. " set comments = concat(if(trim(comments) != '', concat(trim(comments), '\n'), ''), 'Transaction ID: ".$this->trans_id."\nPayment Type: ".$this->payment_type."\nPayment Status: ".$this->payment_status.(isset($this->pending_reason) ? '\nPending Reason:'.$this->pending_reason : '')."') where orders_id = ". $insert_id);
}




function get_error() {
global $HTTP_GET_VARS;

$error = array('title' => MODULE_PAYMENT_PAYPAL_EC_TEXT_ERROR,
'error' => stripslashes(urldecode($HTTP_GET_VARS['error'])));

return $error;
}



function check() {
if (!isset($this->_check)) {
$check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_PAYPAL_EC_STATUS'");
$this->_check = tep_db_num_rows($check_query);
}
return $this->_check;
}



function install() {
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


function remove() {
tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
}

function keys() {
return array(
'MODULE_PAYMENT_PAYPAL_EC_STATUS', 
'MODULE_PAYMENT_PAYPAL_EC_ZONE', 
'MODULE_PAYMENT_PAYPAL_EC_ORDER_STATUS_ID', 
'MODULE_PAYMENT_PAYPAL_EC_SORT_ORDER', 
'MODULE_PAYMENT_PAYPAL_EC_GATEWAY_SERVER', 
'MODULE_PAYMENT_PAYPAL_EC_CERT_FILE', 
'MODULE_PAYMENT_PAYPAL_EC_USERNAME', 
'MODULE_PAYMENT_PAYPAL_EC_PASSWORD', 
'MODULE_PAYMENT_PAYPAL_EC_BN',
'MODULE_PAYMENT_PAYPAL_EC_PAYMENT_ACTION',
'MODULE_PAYMENT_PAYPAL_EC_IPN_URL',
'MODULE_PAYMENT_PAYPAL_EC_USE_LIB_CURL',
'MODULE_PAYMENT_PAYPAL_EC_CURL_PATH'
);

}
}
?>
