<?php
/*
  $Id: stats_products.php,v 1.3 03/07/05 by Bozmium

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

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


<!-- body //-->
<table border="0" width="80%" cellspacing="3" cellpadding="3">
  <tr>
    <td></td>
<!-- body_text //-->
    <td width="80%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="menuboxheading" align="right"><?php echo strftime(DATE_FORMAT_LONG); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr><td>&nbsp;</td></tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
	            <td  class="menuboxheading"><b><?php echo TABLE_HEADING_ID; ?></b></td>
	            <td  class="menuboxheading"><b><?php echo TABLE_HEADING_MANUF; ?></b></td>
	            <td class="menuboxheading"><b><?php echo TABLE_HEADING_MODEL; ?></b></td>
                <td class="menuboxheading"><b><?php echo TABLE_HEADING_PRODUCTS; ?></b></td>       
                <td class="menuboxheading" align="center"><b><?php echo TABLE_HEADING_PRICE; ?>&nbsp;</b></td>
				<td class="menuboxheading" align="center"><b><?php echo TABLE_HEADING_QUANTITY; ?>&nbsp;</b></td>
              </tr>
              <tr>
                <td colspan="6"><hr></td>
              </tr>
<?php
  if ($HTTP_GET_VARS['page'] > 1) $rows = $HTTP_GET_VARS['page'] * 20 - 20;
// update-20051113
  $products_query_raw = "select DISTINCT pd.products_name, p.products_id, p.products_model, products_quantity, p.products_price, p.manufacturers_id, m.manufacturers_id, m.manufacturers_name from " . TABLE_PRODUCTS . " p left join " . TABLE_MANUFACTURERS . " m on p.manufacturers_id = m.manufacturers_id, " . TABLE_LANGUAGES . " l, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = pd.products_id and p.products_id = pd.products_id and l.languages_id = pd.language_id order by m.manufacturers_name, p.products_model ASC";


  $products_query = tep_db_query($products_query_raw);
  while ($products = tep_db_fetch_array($products_query)) {
    $rows++;
    if (strlen($rows) < 2) {
      $rows = '0' . $rows;
    }
?>
              <tr class="menuboxheading">
                <td ><?php echo $products['products_id']; ?></td>
                <td ><?php echo $products['manufacturers_name']; ?></td>
                <td ><?php echo $products['products_model']; ?></td>
                <td ><?php echo $products['products_name']; ?></td>
                <td align="center"><?php echo ($products['products_price']); ?>&nbsp;</td>
				<td align="center"><?php echo ($products['products_quantity']); ?>&nbsp;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="6"><?php echo tep_draw_separator(); ?></td>
              </tr>
            </table></td>
          <tr>
            <td colspan="6"><table border="0" width="100%" cellspacing="0" cellpadding="2">

            </table></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->


</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>