<?php
require_once('mobile/includes/application_top.php');
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CONTACT_US);

  $error = false;
  if (isset($HTTP_GET_VARS['action']) && ($HTTP_GET_VARS['action'] == 'send')) {
    $name = tep_db_prepare_input($HTTP_POST_VARS['name']);
    $email_address = tep_db_prepare_input($HTTP_POST_VARS['email']);
    $enquiry = tep_db_prepare_input($HTTP_POST_VARS['enquiry']);

    if (tep_validate_email($email_address)) {
      tep_mail(STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, EMAIL_SUBJECT, $enquiry, $name, $email_address);

      tep_redirect(tep_mobile_link(FILENAME_CONTACT_US, 'action=success'));
    } else {
      $error = true;

      $messageStack->add('contact', ENTRY_EMAIL_ADDRESS_CHECK_ERROR);
    }
  }

  $breadcrumb->add(NAVBAR_TITLE, tep_mobile_link(FILENAME_CONTACT_US));
require(DIR_MOBILE_INCLUDES . 'header.php');
$headerTitle->write();
?>
<!--  ajax_part_begining -->
<?php echo tep_draw_form('contact_us', tep_mobile_link(FILENAME_CONTACT_US, 'action=send')); ?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<?php
  if ($messageStack->size('contact') > 0) {
?>
      <tr>
        <td><?php echo $messageStack->output('contact'); ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '3'); ?></td>
      </tr>
<?php
  }

  if (isset($HTTP_GET_VARS['action']) && ($HTTP_GET_VARS['action'] == 'success')) {
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
                <td ><?php echo '<a href="' . tep_mobile_link(FILENAME_DEFAULT) . '">' . tep_mobile_button(IMAGE_BUTTON_CONTINUE) . '</a>'; ?></td>
          </tr>
        </table></td>
      </tr>
<?php
  } else {
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main"><?php echo ENTRY_NAME; ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo tep_draw_input_field('name'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo ENTRY_EMAIL; ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo tep_draw_input_field('email'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo ENTRY_ENQUIRY; ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo tep_draw_textarea_field('enquiry', 'soft', 30, 5); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_mobile_button(IMAGE_BUTTON_CONTINUE); ?></td>
      </tr>
<?php
  }
?>
    </table>
<!--  ajax_part_ending -->
</form>
<?php require(DIR_MOBILE_INCLUDES . 'footer.php'); ?>
