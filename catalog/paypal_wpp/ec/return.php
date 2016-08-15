<?php
ob_start(); 

chdir('../../');

require('includes/application_top.php');


if(isset($_GET['token'])) {

//register token in session for later use
if (!tep_session_is_registered('paypal_token')) { tep_session_register('paypal_token'); }

//register payerid
if (!tep_session_is_registered('pp_payer_id')) { tep_session_register('pp_payer_id'); }

//set paypal token variable returned from paypal

$paypal_token = $_GET['token'];

//set paypal action
$_REQUEST['express']=2; 

//include configuration file
require_once('./paypal_wpp/includes/config_ec.inc.php'); 

//include library file
require_once('./paypal_wpp/includes/lib.inc.php'); 

//include processing file 
require_once('./paypal_wpp/includes/upc_direct_paypal.php'); 

$pp_payer_id=$upc_results['PayerID']; 

//verify response from PayPal

switch($upc_results["Ack"]) { 

case "Success": //successful response received

//add shipping information to address book 


//redirect user to shipping checkout
if (tep_session_is_registered('customer_id')) { 

//get country id
  $upc_results['Country']=tep_get_country_by_iso_code($upc_results['Country']); 

//build address entry
  $address_entry = array('customers_id' => $customer_id,
    'entry_company' => $HTTP_POST_VARS['company'],
    'entry_firstname' => $upc_results['FirstName'],
    'entry_lastname' => $upc_results['LastName'],
    'entry_street_address' => $upc_results['Street1'],
    'entry_suburb' => $upc_results['Street2'],
    'entry_postcode' => $upc_results['PostalCode'],
    'entry_city' => $upc_results['CityName'],
    'entry_state' => $upc_results['StateOrProvince'],
    'entry_country_id' => $upc_results['Country']);

  $sendto = tep_insert_address($address_entry);
  if (!tep_session_is_registered('sendto')) { tep_session_register('sendto'); }

  if (tep_session_is_registered('shipping')) { tep_session_unregister('shipping'); }

  tep_redirect(tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL')); 

}

//redirect user to create account
else { 

//register session variables 
if (!tep_session_is_registered('pp_token')) { tep_session_register('pp_token'); }
if (!tep_session_is_registered('pp_payer_status')) { tep_session_register('pp_payer_status'); }
if (!tep_session_is_registered('pp_firstname')) {tep_session_register('pp_firstname'); }
if (!tep_session_is_registered('pp_lastname')) {tep_session_register('pp_lastname'); }
if (!tep_session_is_registered('pp_stree1')) {tep_session_register('pp_street1'); }
if (!tep_session_is_registered('pp_street2')) {tep_session_register('pp_street2'); }
if (!tep_session_is_registered('pp_city')) {tep_session_register('pp_city'); }
if (!tep_session_is_registered('pp_state')) {tep_session_register('pp_state'); }
if (!tep_session_is_registered('pp_zip')) {tep_session_register('pp_zip'); }
if (!tep_session_is_registered('pp_country')) {tep_session_register('pp_country'); }
if (!tep_session_is_registered('pp_phone')) {tep_session_register('pp_phone'); }
if (!tep_session_is_registered('pp_email')) {tep_session_register('pp_email'); }
if (!tep_session_is_registered('pp_business')) {tep_session_register('pp_business'); }

//map response to session variable names
$pp_token=$upc_results['Token']; 
$pp_payer_status=$upc_results['PayerStatus']; 
$pp_firstname=$upc_results['FirstName']; 
$pp_lastname=$upc_results['LastName']; 
$pp_street1=$upc_results['Street1']; 
$pp_street2=$upc_results['Street2']; 
$pp_city=$upc_results['CityName']; 
$pp_state=$upc_results['StateOrProvince']; 
$pp_zip=$upc_results['PostalCode']; 
$pp_country=tep_get_country_by_iso_code($upc_results['Country']);  
$pp_phone=$upc_results['ContactPhone']; 
$pp_email=$upc_results['Payer']; 
$pp_business=$upc_results['PayerBusiness']; 

tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL')); 

}


break; 

case "Failure": //transaction error 

//redirect user and display error code and message from the gateway
tep_redirect(tep_href_link(FILENAME_SHOPPING_CART, 'error_message=' . urlencode("($upc_results[ErrorCode]) $upc_results[LongMessage]"), 'SSL', true, false));

break;


default: //transaction error or warning

//redirect user and display general processing error
tep_redirect(tep_href_link(FILENAME_SHOPPING_CART, 'error_message=' . urlencode(MODULE_PAYMENT_PAYPAL_EC_TEXT_PROCESS_ERROR), 'SSL', true, false));


break; 

}


  }



require('includes/application_bottom.php');
ob_end_flush(); 
?>