<?php
require_once('mobile/includes/application_top.php');
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_LOGOFF);

  $breadcrumb->add(NAVBAR_TITLE);

  tep_session_unregister('customer_id');
  tep_session_unregister('customer_default_address_id');
  tep_session_unregister('customer_first_name');
  tep_session_unregister('customer_country_id');
  tep_session_unregister('customer_zone_id');
  tep_session_unregister('comments');

  $cart->reset();
require(DIR_MOBILE_INCLUDES . 'header.php');
$headerTitle->write();
?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
		<td class="main"><?php echo TEXT_MAIN; ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '2'); ?></td>
      </tr>
      <tr>
		<td ><?php echo tep_draw_form('cart_quantity', tep_mobile_link(FILENAME_DEFAULT)) . tep_mobile_button(IMAGE_BUTTON_CONTINUE) . '</form>'; ?></td>
	  </tr>
</table>
<?php require(DIR_MOBILE_INCLUDES . 'footer.php'); ?>
