<?php
require_once('mobile/includes/application_top.php');

// if the customer is not logged on, redirect them to the login page
  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_mobile_link(FILENAME_LOGIN, '', 'SSL'));
  }

// if there is nothing in the customers cart, redirect them to the shopping cart page
  if ($cart->count_contents() < 1) {
    tep_redirect(tep_mobile_link(FILENAME_SHOPPING_CART));
  }

// needs to be included earlier to set the success message in the messageStack
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CHECKOUT_PAYMENT_ADDRESS);

  $error = false;
  $process = false;
  if (isset($HTTP_POST_VARS['action']) && ($HTTP_POST_VARS['action'] == 'submit')) {
// process a new billing address
    if (tep_not_null($HTTP_POST_VARS['firstname']) && tep_not_null($HTTP_POST_VARS['lastname']) && tep_not_null($HTTP_POST_VARS['street_address'])) {
      $process = true;

      if (ACCOUNT_GENDER == 'true') $gender = tep_db_prepare_input($HTTP_POST_VARS['gender']);
      if (ACCOUNT_COMPANY == 'true') $company = tep_db_prepare_input($HTTP_POST_VARS['company']);
      $firstname = tep_db_prepare_input($HTTP_POST_VARS['firstname']);
      $lastname = tep_db_prepare_input($HTTP_POST_VARS['lastname']);
      $street_address = tep_db_prepare_input($HTTP_POST_VARS['street_address']);
      if (ACCOUNT_SUBURB == 'true') $suburb = tep_db_prepare_input($HTTP_POST_VARS['suburb']);
      $postcode = tep_db_prepare_input($HTTP_POST_VARS['postcode']);
      $city = tep_db_prepare_input($HTTP_POST_VARS['city']);
      $country = tep_db_prepare_input($HTTP_POST_VARS['country']);
      if (ACCOUNT_STATE == 'true') {
        if (isset($HTTP_POST_VARS['zone_id'])) {
          $zone_id = tep_db_prepare_input($HTTP_POST_VARS['zone_id']);
        } else {
          $zone_id = false;
        }
        $state = tep_db_prepare_input($HTTP_POST_VARS['state']);
      }

      if (ACCOUNT_GENDER == 'true') {
        if ( ($gender != 'm') && ($gender != 'f') ) {
          $error = true;

          $messageStack->add('checkout_address', ENTRY_GENDER_ERROR);
        }
      }

      if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
        $error = true;

        $messageStack->add('checkout_address', ENTRY_FIRST_NAME_ERROR);
      }

      if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
        $error = true;

        $messageStack->add('checkout_address', ENTRY_LAST_NAME_ERROR);
      }

      if (strlen($street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) {
        $error = true;

        $messageStack->add('checkout_address', ENTRY_STREET_ADDRESS_ERROR);
      }

      if (strlen($postcode) < ENTRY_POSTCODE_MIN_LENGTH) {
        $error = true;

        $messageStack->add('checkout_address', ENTRY_POST_CODE_ERROR);
      }

      if (strlen($city) < ENTRY_CITY_MIN_LENGTH) {
        $error = true;

        $messageStack->add('checkout_address', ENTRY_CITY_ERROR);
      }

      if (ACCOUNT_STATE == 'true') {
        $zone_id = 0;
        $check_query = tep_db_query("select count(*) as total from " . TABLE_ZONES . " where zone_country_id = '" . (int)$country . "'");
        $check = tep_db_fetch_array($check_query);
        $entry_state_has_zones = ($check['total'] > 0);
        if ($entry_state_has_zones == true) {
          $zone_query = tep_db_query("select distinct zone_id from " . TABLE_ZONES . " where zone_country_id = '" . (int)$country . "' and (zone_name = '" . tep_db_input($state) . "' or zone_code = '" . tep_db_input($state) . "')");
          if (tep_db_num_rows($zone_query) == 1) {
            $zone = tep_db_fetch_array($zone_query);
            $zone_id = $zone['zone_id'];
          } else {
            $error = true;

            $messageStack->add('checkout_address', ENTRY_STATE_ERROR_SELECT);
          }
        } else {
          if (strlen($state) < ENTRY_STATE_MIN_LENGTH) {
            $error = true;

            $messageStack->add('checkout_address', ENTRY_STATE_ERROR);
          }
        }
      }

      if ( (is_numeric($country) == false) || ($country < 1) ) {
        $error = true;

        $messageStack->add('checkout_address', ENTRY_COUNTRY_ERROR);
      }

      if ($error == false) {
        $sql_data_array = array('customers_id' => $customer_id,
                                'entry_firstname' => $firstname,
                                'entry_lastname' => $lastname,
                                'entry_street_address' => $street_address,
                                'entry_postcode' => $postcode,
                                'entry_city' => $city,
                                'entry_country_id' => $country);

        if (ACCOUNT_GENDER == 'true') $sql_data_array['entry_gender'] = $gender;
        if (ACCOUNT_COMPANY == 'true') $sql_data_array['entry_company'] = $company;
        if (ACCOUNT_SUBURB == 'true') $sql_data_array['entry_suburb'] = $suburb;
        if (ACCOUNT_STATE == 'true') {
          if ($zone_id > 0) {
            $sql_data_array['entry_zone_id'] = $zone_id;
            $sql_data_array['entry_state'] = '';
          } else {
            $sql_data_array['entry_zone_id'] = '0';
            $sql_data_array['entry_state'] = $state;
          }
        }

        if (!tep_session_is_registered('billto')) tep_session_register('billto');

        tep_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array);

        $billto = tep_db_insert_id();

        if (tep_session_is_registered('payment')) tep_session_unregister('payment');

        tep_redirect(tep_mobile_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
      }
// process the selected billing destination
    } elseif (isset($HTTP_POST_VARS['address'])) {
      $reset_payment = false;
      if (tep_session_is_registered('billto')) {
        if ($billto != $HTTP_POST_VARS['address']) {
          if (tep_session_is_registered('payment')) {
            $reset_payment = true;
          }
        }
      } else {
        tep_session_register('billto');
      }

      $billto = $HTTP_POST_VARS['address'];

      $check_address_query = tep_db_query("select count(*) as total from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . $customer_id . "' and address_book_id = '" . $billto . "'");
      $check_address = tep_db_fetch_array($check_address_query);

      if ($check_address['total'] == '1') {
        if ($reset_payment == true) tep_session_unregister('payment');
        tep_redirect(tep_mobile_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
      } else {
        tep_session_unregister('billto');
      }
// no addresses to select from - customer decided to keep the current assigned address
    } else {
      if (!tep_session_is_registered('billto')) tep_session_register('billto');
      $billto = $customer_default_address_id;

      tep_redirect(tep_mobile_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
    }
  }

// if no billing destination address was selected, use their own address as default
  if (!tep_session_is_registered('billto')) {
    $billto = $customer_default_address_id;
  }

  $breadcrumb->add(NAVBAR_TITLE_1, tep_mobile_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_mobile_link(FILENAME_CHECKOUT_PAYMENT_ADDRESS, '', 'SSL'));

  $addresses_count = tep_count_customer_address_book_entries();
require(DIR_MOBILE_INCLUDES . 'header.php');
	$headerTitle->write();
?>
<?php echo tep_draw_form('checkout_address', tep_mobile_link(FILENAME_CHECKOUT_PAYMENT_ADDRESS, '', 'SSL'), 'post', 'onSubmit="return check_form_optional(checkout_address);"'); ?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<?php
  if ($messageStack->size('checkout_address') > 0) {
?>
      <tr>
        <td><?php echo $messageStack->output('checkout_address'); ?></td>
      </tr>
      <tr>
        <td class="main"><hr class="separator"></td>
      </tr>
<?php
  }

  if ($process == false) {
?>
      <tr><td>
      	<table border="0" cellspacing="0" cellpadding="2">
	      <tr>
	        <td class="main"><b><?php echo TABLE_HEADING_PAYMENT_ADDRESS; ?></b></td>
	      </tr>
	      <tr>
	        <td class="smallText"><?php echo TEXT_SELECTED_PAYMENT_DESTINATION; ?></td>
	      </tr>
	      <tr>
	        <td class="main"><?php echo tep_address_label($customer_id, $billto, true, ' ', '<br>'); ?></td>
	      </tr>
	  </table>
      </td></tr>
      <tr>
        <td><hr class="separator"></td>
      </tr>
<?php
    if ($addresses_count > 1) {
?>
      <tr><td>
      	<table border="0" cellspacing="0" cellpadding="2">
	      <tr>
			<td class="main"><b><?php echo TABLE_HEADING_ADDRESS_BOOK_ENTRIES; ?></b></td>
	      </tr>
	      <tr>
			<td class="smallText"><?php echo TEXT_SELECT_OTHER_PAYMENT_DESTINATION; ?></td>
	      </tr>
	      <tr>
      		<td>
      			<table border="0" cellspacing="0" cellpadding="2">
<?php
      $radio_buttons = 0;

      $addresses_query = tep_db_query("select address_book_id, entry_firstname as firstname, entry_lastname as lastname, entry_company as company, entry_street_address as street_address, entry_suburb as suburb, entry_city as city, entry_postcode as postcode, entry_state as state, entry_zone_id as zone_id, entry_country_id as country_id from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int)$customer_id . "'");
      while ($addresses = tep_db_fetch_array($addresses_query)) {
        $format_id = tep_get_address_format_id($addresses['country_id']);
?>
					<tr class="moduleRow">
						<td class="main" align="right"><?php echo tep_draw_radio_field('address', $addresses['address_book_id'], ($addresses['address_book_id'] == $billto)); ?></td>
                    	<td class="main"><?php echo tep_address_format($format_id, $addresses, true, ' ', ', '); ?></td>
                	</tr>
<?php
        $radio_buttons++;
      }
?>
                </table>
            </td>
          </tr>
      	</table>
      </td></tr>
      <tr>
        <td><hr class="separator"></td>
      </tr>
<?php
    }
  }

  if ($addresses_count < MAX_ADDRESS_BOOK_ENTRIES) {
?>
      <tr>
      	<td>
        	<table border="0" width="100%" cellspacing="0" cellpadding="2">
	          <tr>
	            <td class="main" colspan="2"><b><?php echo TABLE_HEADING_NEW_PAYMENT_ADDRESS; ?></b></td>
    	      </tr>
	          <tr>
	            <td class="smallText" colspan="2"><?php echo TEXT_CREATE_NEW_PAYMENT_ADDRESS; ?></td>
    	      </tr>
			  <tr>
              	<td><?php require(DIR_WS_MODULES . 'checkout_new_address.php'); ?></td>
              </tr>
	        </table>
	    </td>
      </tr>
      <tr>
        <td><hr class="separator"></td>
      </tr>
<?php
  }
?>
      <tr>
		<td class="main"><?php echo tep_draw_hidden_field('action', 'submit') . tep_mobile_button(IMAGE_BUTTON_CONTINUE); ?></td>
      </tr>
    </table>
</form>
<?php require(DIR_MOBILE_INCLUDES . 'footer.php'); ?>
