<?php

/*
  $Id: new_attributes_select.php 
  
   New Attribute Manager v4b, Author: Mike G.
  
  Updates for New Attribute Manager v.5.0 and multilanguage support by: Kiril Nedelchev - kikoleppard
  kikoleppard@hotmail.bg
  
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

?>
<TR>
  <TD class="pageHeading" colspan="3"><?php echo $pageTitle; ?></TD>
</TR>
<FORM ACTION="<?php echo $PHP_SELF; ?>" NAME="SELECT_PRODUCT" METHOD="POST">
<TR>
  <TD class="main"><BR><B><?php echo HEADING_SELECT; ?><BR></TD>
</TR>
<TR>
  <TD class="main">
  <SELECT NAME="current_product_id">
<?php

$query = "SELECT * FROM products_description where products_id LIKE '%' AND language_id = '$languageFilter' ORDER BY products_name ASC";

$result = mysql_query($query) or die(mysql_error());

$matches = mysql_num_rows($result);

if ($matches) {

   while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
                                                           	
        $title = $line['products_name'];
        $product_id = $line['products_id'];
        
        echo "<OPTION VALUE=\"" . $product_id . "\"" . ($product_id==$current_product_id?" SELECTED":"") . ">" . $title;
        
   }
} else { echo HEADING_NO_PRODUCTS; }
?>
  </SELECT>&nbsp;<input type="image" name="edit" src="<?php echo $adminImages . 'button_edit.gif'; ?>">
  </TD>
</TR>
<TR>
  <TD class="main">&nbsp;</TD>
</TR>
<TR>
  <TD class="main"><BR><B><?php echo HEADING_SELECT_PROD_COPY; ?><BR></TD>
</TR>
<TR>
  <TD class="main">
  <SELECT NAME="dest_product_id">
<?php

$query = "SELECT * FROM products_description where products_id LIKE '%' AND language_id = '$languageFilter' ORDER BY products_name ASC";

$result = mysql_query($query) or die(mysql_error());

$matches = mysql_num_rows($result);

if ($matches) {

   while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
                                                           	
        $title = $line['products_name'];
        $product_id = $line['products_id'];
        
        echo "<OPTION VALUE=\"" . $product_id . "\"" . ($product_id==$dest_product_id?" SELECTED":"") . ">" . $title;
        
   }
} else { echo HEADING_NO_PRODUCTS; }
?>
  </SELECT>&nbsp;<input type="image" name="copy_product" src="<?php echo $adminImages . 'button_copy.gif'; ?>" onClick="return confirm('<?php echo WARNING_TEXT_COPY; ?>')">
  </TD>
</TR>
<TR>
  <TD class="main"><BR><B><?php echo HEADING_SELECT_CAT_COPY; ?><BR></TD>
</TR>
<TR>
  <TD class="main">
<?php
    $cat_tree = tep_get_category_tree();
    echo tep_draw_pull_down_menu('dest_category_id', $cat_tree, $dest_category_id);
?>
  &nbsp;<input type="image" name="copy_category" src="<?php echo $adminImages . 'button_copy.gif'; ?>" onClick="return confirm('<?php echo WARNING_TEXT_COPY; ?>')">
  </TD>
</TR>
</FORM>