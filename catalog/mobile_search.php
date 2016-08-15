<?php
require_once('mobile/includes/application_top.php');
  $manufacturers_query = tep_db_query("select manufacturers_id, manufacturers_name from " . TABLE_MANUFACTURERS . " order by manufacturers_name");
  $manufacturers_array = array();
  $manufacturers_array[] = array('id' => '', 'text' => PULL_DOWN_DEFAULT);

  while ($manufacturers = tep_db_fetch_array($manufacturers_query)) {
        $manufacturers_name = ((strlen($manufacturers['manufacturers_name']) > MAX_DISPLAY_MANUFACTURER_NAME_LEN) ? substr($manufacturers['manufacturers_name'], 0, MAX_DISPLAY_MANUFACTURER_NAME_LEN) . '..' : $manufacturers['manufacturers_name']);
        $manufacturers_array[] = array('id' => $manufacturers['manufacturers_id'],
                                       'text' => $manufacturers_name);
   }

      $info_box_contents = array();
      $info_box_contents[] = array('form' => tep_draw_form('manufacturers', tep_href_link(FILENAME_DEFAULT, '', 'NONSSL', false), 'get'),
                                   'text' => tep_draw_pull_down_menu('manufacturers_id', $manufacturers_array, (isset($HTTP_GET_VARS['manufacturers_id']) ? $HTTP_GET_VARS['manufacturers_id'] : ''), 'onChange="this.form.submit();" size="' . MAX_MANUFACTURERS_LIST . '" style="width: 100%"') . tep_hide_session_id());
require(DIR_MOBILE_INCLUDES . 'header.php');
$headerTitle->write(IMAGE_BUTTON_SEARCH);
?>
<!-- search //-->
<?php echo tep_draw_form('quick_find', tep_mobile_link(FILENAME_ADVANCED_SEARCH_RESULT, '', 'NONSSL', false)) ?>
	<table width="100%" cellpadding="0" cellspacing="0"  class="categories">
<?php 
	echo tep_mobile_selection(null, array(TEXT_KEYWORDS.':', tep_draw_input_field('keywords', '', 'results="10" style="width:150px;"', 'search')));

	if(sizeof($manufacturers_array) > 1 )
		echo tep_mobile_selection(null, array(BOX_HEADING_MANUFACTURERS.':', tep_draw_pull_down_menu('manufacturers_id', $manufacturers_array, (isset($HTTP_GET_VARS['manufacturers_id']) ? $HTTP_GET_VARS['manufacturers_id'] : ''), 'onChange="this.form.submit();" style="width: 100%"') . tep_hide_session_id()));
?>
	</table>
</form>
<!-- search_eof //-->
<?php require(DIR_MOBILE_INCLUDES . 'footer.php'); ?>
