<?
   
/*
  $Id: new_attributes.php 
  
   New Attribute Manager v4b, Author: Mike G.
  
  Updates for New Attribute Manager v.5.0 and multilanguage support by: Kiril Nedelchev - kikoleppard
  kikoleppard@hotmail.bg
  
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
  
  require('new_attributes_config.php');
  require('includes/application_top.php');

// >>> BEGIN REGISTER_GLOBALS
  // These variables are accessed directly rather than through $HTTP_GET_VARS or $_GET later in this script
  link_get_variable('current_product_id');
  link_get_variable('dest_product_id');
  link_get_variable('dest_category_id');
// <<< END REGISTER_GLOBALS
  if (isset($HTTP_GET_VARS['current_product_id'])) $current_product_id = $HTTP_GET_VARS['current_product_id'];

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_NEW_ATTRIBUTE_MANAGER);
  $adminImages = "includes/languages/english/images/buttons/";
  $backLink = $HTTP_SERVER_VARS['REQUEST_METHOD']=='POST'?'<a href="'.tep_href_link('new_attributes.php',"current_product_id=$current_product_id").'">':"<a href=\"javascript:history.back()\">";
 
  if ( $cPathID && $action == "change" )
  {
        require('new_attributes_change.php');

        tep_redirect( './' . FILENAME_CATEGORIES . '?cPath=' . $cPathID . '&pID=' . $current_product_id );

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
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
     <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>

<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
    
<?
function findTitle( $current_product_id, $languageFilter )
{
  $query = "SELECT * FROM products_description where language_id = '$languageFilter' AND products_id = '$current_product_id'";

  $result = mysql_query($query) or die(mysql_error());

  $matches = mysql_num_rows($result);

  if ($matches) {

  while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
                                                          	
        $productName = $line['products_name'];
        
  }
  
  return $productName;
  
  } else { return HEADING_ERROR; }
  
}

function attribRedirect( $cPath )
{

 return '<SCRIPT LANGUAGE="JavaScript"> window.location="./configure.php?cPath=' . $cPath . '"; </script>';
 
}

if ($action == 'select' || isset($HTTP_POST_VARS['edit_x'])) {

  $pageTitle = HEADING_TITLE_VAL_PRODUCT . findTitle( $current_product_id, $languageFilter );
  require('new_attributes_include.php');

} elseif ($action == 'change' || isset($HTTP_POST_VARS['copy_product_x']) || isset($HTTP_POST_VARS['copy_category_x'])) {

  $pageTitle = HEADING_UPDATE;
  require('new_attributes_change.php');
  require('new_attributes_select.php');

} else {

  $pageTitle = HEADING_TITLE_VAL;
  require('new_attributes_select.php');

}
?>
    </td>
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
