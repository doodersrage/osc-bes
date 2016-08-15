<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
         "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?=TITLE?></title>
<meta name="viewport"
	content="width=320; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
<style type="text/css" media="screen">
@import "includes/iphone.css";
</style>
</head>

<body>
<!-- header //-->
<div id="header">
<!-- <table width="100%">
  <tr>
    <td id="headerLogo"><?php echo '<a href="' . tep_mobile_link(FILENAME_DEFAULT) . '">' . tep_image(DIR_WS_IMAGES . 'store_logo.png', STORE_NAME, 0,20) . '</a>'; ?></td>
    <td align="right" class="midText"><?php if(sizeof($cart->contents) > 0) echo '<a href="' . tep_mobile_link(FILENAME_SHOPPING_CART) . '">' . BOX_HEADING_SHOPPING_CART . '</a>'; ?></td>
  </tr>
</table> -->
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="headerNavigation">
  <tr class="headerNavigation">
	  <td>
	  <table cellspacing="1" cellpadding="0" width="100%">
	  	<tr>
		    <td class="headerNavigation" width="20%"><div class="headerNavigationSel"><a href="<?php echo tep_mobile_link(FILENAME_DEFAULT);?>"><?php echo tep_image(DIR_MOBILE_IMAGES."shop_sel.png") . "<br>" . TEXT_SHOP; ?></a></div></td>
		    <td class="headerNavigation" width="20%"><div class="headerNavigation"><a href="<?php echo tep_mobile_link(FILENAME_SEARCH);?>"><?php echo tep_image(DIR_MOBILE_IMAGES."search.png") . "<br>" . IMAGE_BUTTON_SEARCH; ?></a></div></td>
		    <td class="headerNavigation" width="20%"><div class="headerNavigation"><a href="<?php echo tep_mobile_link(FILENAME_ACCOUNT);?>"><?php echo tep_image(DIR_MOBILE_IMAGES."settings.png") . "<br>" . TEXT_ACCOUNT; ?></a></div></td>
		    <td class="headerNavigation" width="20%"><div class="headerNavigation"><a href="<?php echo tep_mobile_link(FILENAME_ABOUT);?>"><?php echo tep_image(DIR_MOBILE_IMAGES."about.png") . "<br>" . TEXT_ABOUT; ?></a></div></td>
		    <td class="headerNavigation" width="20%"><div class="headerNavigation"><a href="<?php echo tep_mobile_link(FILENAME_LANGUAGES);?>"><?php echo tep_image(DIR_MOBILE_IMAGES."language.png") . "<br>" . BOX_HEADING_LANGUAGES; ?></a></div></td>
	  	</tr>
	  </table>
	  </td>
<!--   	  <td align="right" valign="middle"><a href="<?php echo tep_mobile_link(FILENAME_LANGUAGES);?>"><?php echo  ($HTTP_GET_VARS['module'] == 'languages') ? "&nbsp;" : $languages_icon; ?></a></td> -->
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

