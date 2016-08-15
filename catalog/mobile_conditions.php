<?php
require_once('mobile/includes/application_top.php');
require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CONDITIONS);
$breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_CONDITIONS));

require(DIR_MOBILE_INCLUDES . 'header.php');
$headerTitle->write();
?>
<!--  ajax_part_begining -->
<table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td class="main"><?php echo TEXT_INFORMATION; ?></td>
      </tr>
</table>
<!--  ajax_part_ending -->
<?php require(DIR_MOBILE_INCLUDES . 'footer.php'); ?>
