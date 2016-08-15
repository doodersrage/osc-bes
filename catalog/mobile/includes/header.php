<?php if(isset($HTTP_GET_VARS['ajax']) == false) { ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
         "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?=TITLE?></title>
<meta name="viewport"
	content="width=320; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
<style type="text/css" media="screen">
@import "<?php echo DIR_MOBILE_INCLUDES; ?>iphone.css";
</style>
</head>

<body>
<!-- header //-->
<div id="header">
<table width="100%" class="logo">
  <tr>
    <td id="headerLogo"><?php echo '<a href="' . tep_mobile_link(FILENAME_DEFAULT) . '">' . tep_image(DIR_WS_IMAGES . 'store_logo.png', STORE_NAME, 0,20) . '</a>'; ?></td>
    <td align="right" class="midText"><?php if(sizeof($cart->contents) > 0) echo '<a href="' . tep_mobile_link(FILENAME_SHOPPING_CART) . '">' . BOX_HEADING_SHOPPING_CART . '</a>'; ?></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="headerNavigation">
  <tr class="headerNavigation">
    <td class="headerNavigation" id="headerShop"      onclick="location.href='<?php echo tep_mobile_link(FILENAME_DEFAULT);?>'"><a href="<?php echo tep_mobile_link(FILENAME_DEFAULT);?>"><?php echo TEXT_SHOP; ?></a></td>
    <td class="headerNavigation" id="headerSearch"    onclick="location.href='<?php echo tep_mobile_link(FILENAME_SEARCH);?>'"><a href="<?php echo tep_mobile_link(FILENAME_SEARCH);?>"><?php echo IMAGE_BUTTON_SEARCH; ?></a></td>
    <td class="headerNavigation" id="headerAccount"   onclick="location.href='<?php echo tep_mobile_link(FILENAME_ACCOUNT);?>'"><a href="<?php echo tep_mobile_link(FILENAME_ACCOUNT);?>"><?php echo TEXT_ACCOUNT; ?></a></td>
    <td class="headerNavigation" id="headerAbout"     onclick="location.href='<?php echo tep_mobile_link(FILENAME_ABOUT);?>'"><a href="<?php echo tep_mobile_link(FILENAME_ABOUT);?>"><?php echo TEXT_ABOUT; ?></a></td>
	<td class="headerNavigation" id="headerLanguage"  onclick="location.href='<?php echo tep_mobile_link(FILENAME_LANGUAGES);?>'"><a href="<?php echo tep_mobile_link(FILENAME_LANGUAGES);?>"><?php echo  BOX_HEADING_LANGUAGES; ?></a></td>	
<!-- <td class="headerNavigation" id="headerLanguage" onclick="location.href='<?php echo tep_mobile_link(FILENAME_DEFAULT);?>'"><a href="<?php echo tep_mobile_link(FILENAME_LANGUAGES);?>"><?php echo  ($HTTP_GET_VARS['module'] == 'languages') ? "&nbsp;" : $languages_icon; ?></a></td> -->	
  </tr>
</table>
</div>
<!-- header_eof //-->
<!-- error msg -->
<div id="errorMsg">
<?php
  if (isset($HTTP_GET_VARS['error_message']) && tep_not_null($HTTP_GET_VARS['error_message']))
	echo htmlspecialchars(stripslashes(urldecode($HTTP_GET_VARS['error_message'])));
?>
</div>
<!-- error msg -->
<div id="mainBody">
<?php } 
    if(sizeof($breadcrumb->_trail) > 0)
		$headerTitleText = $breadcrumb->_trail[sizeof($breadcrumb->_trail) - 1]['title'];
?>
