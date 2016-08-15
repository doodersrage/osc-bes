<?php
/*
  $Id: feeders.php,v 1.6 2003/06/30 13:13:49 dgw_ Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
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
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading">Store Feeds</td>
            <td class="pageHeading" align="right"><img src="images/pixel_trans.gif" width="110" height="48"></td>
          </tr>
        </table>
          <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="main">
            <tr>
              <td>
              Froogle: <?php echo '<a href="' . tep_href_link('froogle.php') . '" class="splitPageLink" target=_blank">Froogle</a>'; ?>
              </td>
            </tr>
            <tr>
              <td>
              Yahoo: <?php echo '<a href="' . tep_href_link('yahoo.php') . '" class="splitPageLink" target=_blank">Yahoo</a>'; ?>
              </td>
            </tr>
            <tr>
              <td>
              BizRate: <?php echo '<a href="' . tep_href_link('bizrate.php') . '" class="splitPageLink" target=_blank>Bizrate</a>'; ?>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>