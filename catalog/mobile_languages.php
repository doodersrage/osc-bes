<?php
require_once('mobile/includes/application_top.php');
require(DIR_MOBILE_INCLUDES . 'header.php');
$PCSITE = DIR_MAIN_HTTP_CATALOG. FILENAME_DEFAULT;
$headerTitle->write(BOX_HEADING_LANGUAGES);
?>
<!-- languages //-->
<table width="100%" cellpadding="0" cellspacing="0"  class="categories">
<?php 
  if (!isset($lng) || (isset($lng) && !is_object($lng))) {
    include(DIR_WS_CLASSES . 'language.php');
    $lng = new language;
  }

  reset($lng->catalog_languages);

  $path = $_SERVER['HTTP_REFERER'] . ((strpos($_SERVER['HTTP_REFERER'],'?') == false) ? '?' : '&') . 'language='; 
  while (list($key, $value) = each($lng->catalog_languages)) {
	echo tep_mobile_selection($path . $key, array(tep_image(DIR_WS_LANGUAGES .  $value['directory'] . '/images/' . $value['image'], $value['name']),$value['name']));
  }
?>
</table>
<!-- languages_eof //-->
<?php require(DIR_MOBILE_INCLUDES . 'footer.php'); ?>
