<?php
/*
  $Id: links_setup.php,v 1.00 2003/10/02 Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $nameContribution = 'Header Tags Controller';
  
  $stage_one_query_raw = "select products_name, products_description, products_id, language_id from products_description";
  $stage_one_query = tep_db_query($stage_one_query_raw);
  while ($stage_one = tep_db_fetch_array($stage_one_query)) {
    tep_db_query("update products_description set products_head_title_tag='".addslashes($stage_one['products_name'])."', products_head_desc_tag = '". addslashes(strip_tags($stage_one['products_description']))."', products_head_keywords_tag =  '" . addslashes($stage_one['products_name']) . "' where products_id = '" . $stage_one['products_id'] . "' and language_id='".$stage_one['language_id']."'");
  }

  $languages = tep_get_languages();
  for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
    $categories_query_raw = "select categories_id, categories_name, language_id from categories_description where language_id='". (int)$languages[$i]['id']."'";
    $categories_query = tep_db_query($categories_query_raw);
    if (tep_db_num_rows( $categories_query) > 0) {
      while ($categories = tep_db_fetch_array($categories_query)) {
        tep_db_query("update categories_description set categories_htc_title_tag='".addslashes($categories['categories_name'])."', categories_htc_desc_tag = '". addslashes($categories['categories_name'])."', categories_htc_keywords_tag = '". addslashes($categories['categories_name']) . "' where categories_id = '" . $categories['categories_id']."'");
      }
    }
  }
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table class="okvir" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td>
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
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo $nameContribution . ' Fill Tags Setup'; ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td class="main">
         <?php 
           if ($db_error == false) {
             echo $nameContribution . ' tags successfully updated!!!';
           } else {
             echo 'Error encountered during ' . $nameContribution . ' database update.';
           }
         ?>
        </td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '20'); ?></td>
      </tr>
      <tr>
       <td><a target="_blank" href="http://www.oscommerce-solution.com"> Prepared by osCommerce-Solution.com</a></td>
      </tr>
    </table></td>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
