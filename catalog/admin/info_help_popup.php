<?php
  require('includes/application_top.php');
  $languages = tep_get_languages();

  $info_tag = (isset($_REQUEST['info_tag']) ? $_REQUEST['info_tag'] : '');
  $info_help_query = tep_db_query("select info_text from " . TABLE_INFO_HELP . " where info_tag = '" . tep_db_prepare_input($info_tag) . "' and language_id = '" . (int)$languages_id . "'");
  if ($info_help = tep_db_fetch_array($info_help_query)) $info_text = $info_help['info_text'];
  else $info_text = '';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title><?php echo "Help for $info_tag"; ?></title>
</head>
<body><p class="smallText">
<?php echo htmlspecialchars($info_text); ?>
</p></body>
</html>
