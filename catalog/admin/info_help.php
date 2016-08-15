<?php
/*
  $Id: info_help.php,v 1.17 2005/12/08 15:00:00 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  $languages = tep_get_languages();
  $action = (isset($_REQUEST['x']) ? 'update' : '');
  $info_tag = (isset($_REQUEST['info_tag']) ? $_REQUEST['info_tag'] : '');
  $info_text = (isset($_REQUEST['info_text']) ? $_REQUEST['info_text'] : array());

  if (tep_not_null($action)) {
    switch ($action) {
      case 'update':
        if ($info_tag) {
          for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
            $language_id = $languages[$i]['id'];
            if (isset($info_text[$language_id])) {
              $sql_data_array = array('info_text' => tep_db_prepare_input($info_text[$language_id]));
              tep_db_perform(TABLE_INFO_HELP, $sql_data_array, 'update', "info_tag = '" . tep_db_prepare_input($info_tag) . "' and language_id = '" . (int)$language_id . "'");
            }
          }
        }
        $info_text = tep_db_prepare_input($info_text);
        break;
    }
  } elseif ($info_tag) {
    $info_help_query = tep_db_query("select language_id, info_text from " . TABLE_INFO_HELP . " where info_tag = '" . tep_db_prepare_input($info_tag) . "'");
    while ($info_help = tep_db_fetch_array($info_help_query)) {
      $info_text[$info_help['language_id']] = isset($info_help['info_text'])?$info_help['info_text']:'';
    }
  } else $info_text = array();

  $tags[] = array('id' => '', 'text' => TEXT_NONE);
  $info_help_query = tep_db_query("select distinct info_tag from " . TABLE_INFO_HELP . " order by info_tag");
  while ($info_help = tep_db_fetch_array($info_help_query)) {
    $tags[] = array('id' => $info_help['info_tag'], 'text' => $info_help['info_tag']);
  }
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
<script language="javascript" src="includes/javascript/spellcheck.js"></script>
<script language="javascript"><!--
function popupWindow(url,x,y) {
  window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width='+x+',height='+y+',screenX=150,screenY=150,top=150,left=150')
}
//--></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<div id="spiffycalendar" class="text"></div>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"> 
<?php echo tep_draw_form('info_help', FILENAME_INFO_HELP, '', 'post'); ?>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="main" width="100px"><?php tep_echo_help('INFO_HELP_TAG'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_pull_down_menu('info_tag', $tags, $info_tag?$info_tag:'', 'onChange="return info_help.submit()"'); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++)
      if (isset($info_text[$languages[$i]['id']])) {
?>
          <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('INFO_HELP_TEXT'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_textarea_field('info_text[' . $languages[$i]['id'] . ']', 'soft', '70', '2', $value=$info_text[$languages[$i]['id']], 'onKeyUp="CountInput(this);"');
                echo "<br>".spellcount_link('info_help','info_text[' . $languages[$i]['id'] . ']',strlen($value)); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
      }
?>

        </table></td>
      </tr>
      <tr>
        <td class="main" align="left">
<?php
    if ($info_tag) echo tep_image_submit('button_save.gif', IMAGE_SAVE);
?>
        </td>
      </tr>
    </table></form></td>
<!-- body_text_eof //-->
  </tr>
</table>
<FORM name="hidden_form" method="POST" action="spellcheck.php?init=yes" target="WIN">
<INPUT type="hidden" name="form_name" value="">
<INPUT type="hidden" name="field_name" value="">
<INPUT type="hidden" name="first_time_text" value="">
</FORM>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
