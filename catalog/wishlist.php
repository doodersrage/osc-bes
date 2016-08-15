<?php
/*
  $Id: wishlist.php,v 3.0  2005/04/20 Dennis Blake
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_WISHLIST);

/*******************************************************************
****************** ADD PRODUCT TO SHOPPING CART ********************
*******************************************************************/

  if (isset($HTTP_POST_VARS['add_wishprod'])) {
	if(isset($HTTP_POST_VARS['add_prod_x'])) {
		foreach ($HTTP_POST_VARS['add_wishprod'] as $value) {
			$product_id = tep_get_prid($value);
			$cart->add_cart($product_id, $cart->get_quantity(tep_get_uprid($product_id, $HTTP_POST_VARS['id'][$value]))+1, $HTTP_POST_VARS['id'][$value]);
		}
	}
  }


/*******************************************************************
****************** DELETE PRODUCT FROM WISHLIST ********************
*******************************************************************/

  if (isset($HTTP_POST_VARS['add_wishprod'])) {
	if(isset($HTTP_POST_VARS['delete_prod_x'])) {
		foreach ($HTTP_POST_VARS['add_wishprod'] as $value) {
			$wishList->remove($value);
		}
	}
  }


/*******************************************************************
************* EMAIL THE WISHLIST TO MULTIPLE FRIENDS ***************
*******************************************************************/

  if (isset($HTTP_POST_VARS['email_prod_x'])) {

		$errors = false;
		$guest_errors = "";
		$email_errors = "";
		$message_error = "";

		if(strlen($HTTP_POST_VARS['message']) < '1') {
			$error = true;
			$message_error .= "<div class=\"messageStackError\"><img src=\"images/icons/error.gif\" /> " . ERROR_MESSAGE . "</div>";
		}			

  		if(tep_session_is_registered('customer_id')) {
			$customer_query = tep_db_query("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_id = '" . (int)$customer_id . "'");
	  		$customer = tep_db_fetch_array($customer_query);
	
			$from_name = $customer['customers_firstname'] . ' ' . $customer['customers_lastname'];
			$from_email = $customer['customers_email_address'];
			$subject = $customer['customers_firstname'] . ' ' . WISHLIST_EMAIL_SUBJECT;
			$link = HTTP_SERVER . DIR_WS_CATALOG . FILENAME_WISHLIST_PUBLIC . "?public_id=" . $customer_id;
	
		//REPLACE VARIABLES FROM DEFINE
			$arr1 = array('$from_name', '$link');
			$arr2 = array($from_name, $link);
			$replace = str_replace($arr1, $arr2, WISHLIST_EMAIL_LINK);
			$message = tep_db_prepare_input($HTTP_POST_VARS['message']);
			$body = $message . $replace;
		} else {
			if(strlen($_POST['your_name']) < '1') {
				$error = true;
				$guest_errors .= "<div class=\"messageStackError\"><img src=\"images/icons/error.gif\" /> " . ERROR_YOUR_NAME . "</div>";
			}
			if(strlen($_POST['your_email']) < '1') {
				$error = true;
				$guest_errors .= "<div class=\"messageStackError\"><img src=\"images/icons/error.gif\" /> " .ERROR_YOUR_EMAIL . "</div>";
			} elseif(!tep_validate_email($_POST['your_email'])) {
				$error = true;
				$guest_errors .= "<div class=\"messageStackError\"><img src=\"images/icons/error.gif\" /> " . ERROR_VALID_EMAIL . "</div>";
			}

			$from_name = stripslashes($_POST['your_name']);
			$from_email = $_POST['your_email'];
			$subject = $from_name . ' ' . WISHLIST_EMAIL_SUBJECT;
			$message = stripslashes($HTTP_POST_VARS['message']);

			$z = 0;
			$prods = "";
			foreach($HTTP_POST_VARS['prod_name'] as $name) {
				$prods .= stripslashes($name) . "  " . stripslashes($HTTP_POST_VARS['prod_att'][$z]) . "\n" . $HTTP_POST_VARS['prod_link'][$z] . "\n\n";
				$z++;
			}
			$body = $message . "\n\n" . $prods . "\n\n" . WISHLIST_EMAIL_GUEST;
	  	}

		//Check each posted name => email for errors.
		$j = 0;
		foreach($HTTP_POST_VARS['friend'] as $friendx) {
			if($j == 0) {
				if($friend[0] == '' && $email[0] == '') {
					$error = true;
					$email_errors .= "<div class=\"messageStackError\"><img src=\"images/icons/error.gif\" /> " . ERROR_ONE_EMAIL . "</div>";
				}
			}

			if(isset($friendx) && $friendx != '') {
				if(strlen($email[$j]) < '1') {
					$error = true;
					$email_errors .= "<div class=\"messageStackError\"><img src=\"images/icons/error.gif\" /> " . ERROR_ENTER_EMAIL . "</div>";
				} elseif(!tep_validate_email($email[$j])) {
					$error = true;
					$email_errors .= "<div class=\"messageStackError\"><img src=\"images/icons/error.gif\" /> " . ERROR_VALID_EMAIL . "</div>";
				}
			}

			if(isset($email[$j]) && $email[$j] != '') {
				if(strlen($friendx) < '1') {
					$error = true;
					$email_errors .= "<div class=\"messageStackError\"><img src=\"images/icons/error.gif\" /> " . ERROR_ENTER_NAME . "</div>";
				}
			}
			$j++;
		}
		if($error == false) {
			$j = 0;
			foreach($HTTP_POST_VARS['friend'] as $friendx) {
				if($friendx != '') {
					tep_mail($friendx, $email[$j], $subject, '', $from_name, $from_email, $friendx . ",\n\n" . $body);  // modified by splautz to send as text only
				}

			//Clear Values
				$friend[$j] = "";
				$email[$j] = "";
				$message = "";

				$j++;
			}

        	$messageStack->add('wishlist', WISHLIST_SENT, 'success');
		}
  }


 $breadcrumb->add(NAVBAR_TITLE_WISHLIST, tep_href_link(FILENAME_WISHLIST, '', 'SSL'));
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<base href="<?php echo (getenv('HTTPS') == 'on' ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<link rel="stylesheet" type="text/css" href="stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="3" cellpadding="3">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top">
	  <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
      </table>
	</td>
<!-- body_text //-->
    <td width="100%" valign="top"><?php echo tep_draw_form('wishlist_form', tep_href_link(FILENAME_WISHLIST)); ?>
	  <table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td>
		  <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_image(DIR_WS_IMAGES . 'table_background_wishlist.gif', HEADING_TITLE, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
          </table>
		</td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>

<?php
  if ($messageStack->size('wishlist') > 0) {
?>
      <tr>
        <td><?php echo $messageStack->output('wishlist'); ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
  }


if (is_array($wishList->wishID) && !empty($wishList->wishID)) {
	reset($wishList->wishID);

?>
	  <tr>
		<td>
		<table border="0" width="100%" cellspacing="0" cellpadding="3" class="productListing">
		  <tr>
				<td class="productListing-heading"><?php echo BOX_TEXT_IMAGE; ?></td>
				<td class="productListing-heading"><?php echo BOX_TEXT_PRODUCT; ?></td>
				<td class="productListing-heading"><?php echo BOX_TEXT_PRICE; ?></td>
				<td class="productListing-heading" align="center"><?php echo BOX_TEXT_SELECT; ?></td>
		  </tr>

<?php
		$i = 0;
		while (list($wishlist_id, ) = each($wishList->wishID)) {

			$product_id = tep_get_prid($wishlist_id);

// update-20051113
		    $products_query = tep_db_query("select pd.products_id, pd.products_name, pd.products_description, p.products_image, p.products_status, p.products_price, p.products_tax_class_id, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id, " . TABLE_PRODUCTS_DESCRIPTION . " pd where pd.products_id = '" . $product_id . "' and p.products_id = pd.products_id and pd.language_id = '" . $languages_id . "' order by products_name");
			$products = tep_db_fetch_array($products_query);

		      if (($i/2) == floor($i/2)) {
		        $class = "productListing-even";
		      } else {
		        $class = "productListing-odd";
		      }

?>
				  <tr class="<?php echo $class; ?>">
					<td valign="top" class="productListing-data" align="left"><a href="<?php echo tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $wishlist_id, 'NONSSL'); ?>"><?php echo tep_image(DIR_WS_IMAGES . $products['products_image'], $products['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT); ?></a></td>
					<td valign="top" class="productListing-data" align="left" class="main"><b><a href="<?php echo tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $wishlist_id, 'NONSSL'); ?>"><?php echo $products['products_name']; ?></a></b>
					<input type="hidden" name="prod_link[]" value="<?php echo tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $wishlist_id, 'NONSSL'); ?>" />
					<input type="hidden" name="prod_name[]" value="<?php echo $products['products_name']; ?>" />
<?php



/*******************************************************************
******** THIS IS THE WISHLIST CODE FOR PRODUCT ATTRIBUTES  *********
*******************************************************************/

                  $attributes_addon_price = 0;

                  // Now get and populate product attributes
					$att_name = "";
					if (isset($wishList->wishID[$wishlist_id]['attributes'])) {
						while (list($option, $value) = each($wishList->wishID[$wishlist_id]['attributes'])) {
                            //clr 030714 move hidden field to if statement below
                      		//echo tep_draw_hidden_field('id[' . $wishlist_id . '][' . $option . ']', $value);

         					$attributes = tep_db_query("select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix
                                      from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa
                                      where pa.products_id = '" . $wishlist_id . "'
                                       and pa.options_id = '" . $option . "'
                                       and pa.options_id = popt.products_options_id
                                       and pa.options_values_id = '" . $value . "'
                                       and pa.options_values_id = poval.products_options_values_id
                                       and popt.language_id = '" . $languages_id . "'
                                       and poval.language_id = '" . $languages_id . "'");
							$attributes_values = tep_db_fetch_array($attributes);

                            //clr 030714 determine if attribute is a text attribute and assign to $attr_value temporarily
                            if ($value == PRODUCTS_OPTIONS_VALUE_TEXT_ID) {
                              echo tep_draw_hidden_field('id[' . $wishlist_id . '][' . TEXT_PREFIX . $option . ']', $wishList->wishID[$wishlist_id]['attributes_values'][$option]);
                              $attr_value = $wishList->wishID[$wishlist_id]['attributes_values'][$option];
                            } else {
                              echo tep_draw_hidden_field('id[' . $wishlist_id . '][' . $option . ']', $value);
                              $attr_value = $attributes_values['products_options_values_name'];
                            }

                       		if ($attributes_values['price_prefix'] == '+') {
								$attributes_addon_price += $attributes_values['options_values_price'];
                       		} else if($attributes_values['price_prefix'] == '-') {
                         		$attributes_addon_price -= $attributes_values['options_values_price'];
							} else $attributes_addon_price += $attributes_values['options_values_price'] - $products['final_price'];
							$att_name .= " (" . $attributes_values['products_options_name'] . ": " . $attr_value . ") ";
                       		echo '<br /><small><i> ' . $attributes_values['products_options_name'] . ': ' . $attr_value . '</i></small>';
                    	} // end while attributes for product

					}

					echo '<input type="hidden" name="prod_att[]" value="' . $att_name . '" />';

                   	if (tep_not_null($products['specials_new_products_price'])) {
                   		$products_price = '<s>' . $currencies->display_price($products['products_price']+$attributes_addon_price, tep_get_tax_rate($products['products_tax_class_id'])) . '</s> <span class="productSpecialPrice">' . $currencies->display_price($products['specials_new_products_price']+$attributes_addon_price, tep_get_tax_rate($products['products_tax_class_id'])) . '</span>';
                   	} else {
                       	$products_price = $currencies->display_price($products['products_price']+$attributes_addon_price, tep_get_tax_rate($products['products_tax_class_id']));
                    }

/*******************************************************************
******* CHECK TO SEE IF PRODUCT HAS BEEN ADDED TO THEIR CART *******
*******************************************************************/

			if($cart->in_cart($wishlist_id)) {
				echo '<br /><font color="#FF0000"><b>' . TEXT_ITEM_IN_CART . '</b></font>';
			}

/*******************************************************************
********** CHECK TO SEE IF PRODUCT IS NO LONGER AVAILABLE **********
*******************************************************************/

   			if($products['products_status'] == 0) {
   				echo '<br /><font color="#FF0000"><b>' . TEXT_ITEM_NOT_AVAILABLE . '</b></font>';
  			}
	
			$i++;
?>
			</td>
			<td valign="top" class="productListing-data"><?php echo $products_price; ?></td>
			<td valign="top" class="productListing-data" align="center">
<?php

/*******************************************************************
* PREVENT THE ITEM FROM BEING ADDED TO CART IF NO LONGER AVAILABLE *
*******************************************************************/

			if($products['products_status'] != 0) {
				echo tep_draw_checkbox_field('add_wishprod[]',$wishlist_id);
			}
?>
			</td>
		  </tr>

<?php
		}
?>
		</table>
		</td>
	  </tr>
	  <tr>
		<td align="right"><br /><?php echo tep_image_submit('button_delete.gif', 'Delete From Wishlist', 'name="delete_prod" value="delete_prod"') . " " . tep_image_submit('button_in_cart.gif', 'Add to Cart', 'name="add_prod" value="add_prod"'); ?></td>
 	  </tr>
	</table>
<?php

/*******************************************************************
*********** CODE TO SPECIFY HOW MANY EMAILS TO DISPLAY *************
*******************************************************************/


	if(!tep_session_is_registered('customer_id')) {

?>
	<table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
	  <tr>
		<td class="main"><?php echo WISHLIST_EMAIL_TEXT_GUEST; ?></td>
	  </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
	  <tr>
        <td align="center">
			<table border="0" width="400px" cellspacing="0" cellpadding="2">
			  <tr>
				<td class="main" colspan="2"><table cellpadding="2" cellspacing="0">
				  <tr>
					<td colspan="2"><?php echo $guest_errors; ?></td>
				  </tr>
				  <tr>
					<td class="main"><?php echo TEXT_YOUR_NAME; ?></td>
					<td class="main"><?php echo tep_draw_input_field('your_name', $your_name); ?></td>
			  	  </tr>
			  	  <tr>
					<td class="main"><?php echo TEXT_YOUR_EMAIL; ?></td>
					<td class="main"><?php echo tep_draw_input_field('your_email', $your_email); ?></td>
			  	  </tr>
				</table></td>
			  </tr>
		      <tr>
		        <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
		      </tr>
		      <tr>
		        <td colspan="2"><?php echo tep_draw_separator('pixel_black.gif', '100%', '1'); ?></td>
		      </tr>
		      <tr>
		        <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
		      </tr>
<?php 

	} else {

?>

	<table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
	  <tr>
		<td class="main"><?php echo WISHLIST_EMAIL_TEXT; ?></td>
	  </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
	  <tr>
        <td align="center">
			<table border="0" width="400px" cellspacing="0" cellpadding="2">

<?php

	}

?>
			  <tr>
				<td colspan="2"><?php echo $email_errors; ?></td>
			  </tr>
<?php

	$email_counter = 0;
	while($email_counter < DISPLAY_WISHLIST_EMAILS) {
?>
			  <tr>
				<td class="main"><?php echo TEXT_NAME; ?>&nbsp;&nbsp;<?php echo tep_draw_input_field('friend[]', $friend[$email_counter]); ?></td>
				<td class="main"><?php echo TEXT_EMAIL; ?>&nbsp;&nbsp;<?php echo tep_draw_input_field('email[]', $email[$email_counter]); ?></td>
			  </tr>
<?php
	$email_counter++;
	}
?>
			  <tr>
				<td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
			  </tr>
			  <tr>
				<td colspan="2"><?php echo $message_error; ?></td>
			  </tr>
			  <tr>
				<td colspan="2" class="main"><?php echo TEXT_MESSAGE .  tep_draw_textarea_field('message', 'soft', 45, 5); ?></td>
			  </tr>
			  <tr>
				<td colspan="2" align="right"><?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE, 'name="email_prod" value="email_prod"'); ?></td>

			  </tr>
			</table>
		</td>
	  </tr>
	</table>
	</form>
<?php

} else { // Nothing in the customers wishlist

?>
  <tr>
	<td>
	<table border="0" width="100%" cellspacing="0" cellpadding="2">
	  <tr>
		<td><table border="0" width="100%" cellspacing="0" cellpadding="0">
		  <tr>
			<td class="main"><?php echo BOX_TEXT_NO_ITEMS;?></td>
		  </tr>
		</table>
		</td>
	  </tr>
	</table>
	</td>
  </tr>
</table>
</form>

<?php 
}
?>
<!-- customer_wishlist_eof //-->
	</td>

<!-- body_text_eof //-->
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
<!-- right_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
<!-- right_navigation_eof //-->
    </table></td>
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>