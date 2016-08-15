<?php
require_once('mobile/includes/application_top.php');
  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_mobile_link(FILENAME_LOGIN, '', 'SSL'));
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_ACCOUNT);
  
  $breadcrumb->add(NAVBAR_TITLE, tep_mobile_link(FILENAME_ACCOUNT, '', 'SSL'));
require(DIR_MOBILE_INCLUDES . 'header.php');
$headerTitle->write();
if(AJAX_ENABLED && $curl_installed)
	include(DIR_MOBILE_CLASSES . 'account_js.php');
?>
<!-- account //-->
<div id="iphone_content_body">
<div id="iphone_content" style="position: absolute; width: 100%">
<table width="100%" cellpadding="0" cellspacing="0"  class="categories">
<?php 
	echo tep_mobile_selection(tep_mobile_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'), array(TEXT_MY_ORDERS));
	echo tep_mobile_selection(tep_mobile_link(FILENAME_ACCOUNT_EDIT, '', 'SSL'), array(HEADER_TITLE_MY_ACCOUNT));
	echo tep_mobile_selection(tep_mobile_link(FILENAME_ADDRESS_BOOK), array(IMAGE_BUTTON_ADDRESS_BOOK));
	echo tep_mobile_selection(tep_mobile_link(FILENAME_ACCOUNT_PASSWORD), array(CATEGORY_PASSWORD));
	echo tep_mobile_selection(tep_mobile_link(FILENAME_ACCOUNT_NEWSLETTERS), array(TEXT_NEWSLETTERS));
	echo tep_mobile_selection(tep_mobile_link(FILENAME_ACCOUNT_NOTIFICATIONS), array(BOX_HEADING_NOTIFICATIONS));
	echo tep_mobile_selection(tep_mobile_link(FILENAME_LOGOFF), array(HEADER_TITLE_LOGOFF));
?>
	</table>
</div>
</div>
<!-- account_eof //-->
<?php require(DIR_MOBILE_INCLUDES . 'footer.php'); ?>
