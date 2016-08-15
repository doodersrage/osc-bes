<?php
require_once('mobile/includes/application_top.php');
require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_DEFAULT);
require(DIR_MOBILE_INCLUDES . 'header.php');
$headerTitle->write(STORE_NAME);
if(AJAX_ENABLED && $curl_installed)
	include(DIR_MOBILE_CLASSES . 'about_js.php');
?>
<!-- about //-->
<div id="iphone_content_body">
<div id="iphone_content" style="position: absolute; width: 100%">
<table width="100%" cellpadding="0" cellspacing="0"  class="categories">
<?php 
  $page_query = tep_db_query("select pd.pages_name, p.pages_id, p.pages_forward from " . TABLE_PAGES . " p, " . TABLE_PAGES_DESCRIPTION . " pd where p.pages_id = pd.pages_id and p.pages_status = '1' and pd.language_id = '" . (int)$languages_id . "' order by COALESCE(p.sort_order,1000), p.pages_id");

  $page_menu_text = '';
  while($page = tep_db_fetch_array($page_query)){
    if(tep_not_null($page["pages_forward"])) {
//      $page_forward = explode('?', $page["pages_forward"]);
//      echo tep_mobile_selection(tep_mobile_link($page_forward[0],isset($page_forward[1])?$page_forward[1]:''), $page["pages_name"]));
    } elseif($page["pages_id"]!=1)
      echo tep_mobile_selection(tep_mobile_link('mobile_pages.php?pages_id='.$page["pages_id"]), array($page["pages_name"]));
  }
//	echo tep_mobile_selection(tep_mobile_link(FILENAME_CONDITIONS), array("Terms Of Service"));
//	echo tep_mobile_selection(tep_mobile_link(FILENAME_SHIPPING), array(BOX_INFORMATION_SHIPPING));
//	echo tep_mobile_selection(tep_mobile_link(FILENAME_PRIVACY), array("Returns and Refunds"));
//	echo tep_mobile_selection(tep_mobile_link(FILENAME_CONTACT_US), array(BOX_INFORMATION_CONTACT));
?>
	</table>
</div>
</div>
<!-- about_eof //-->
<?php require(DIR_MOBILE_INCLUDES . 'footer.php'); ?>
