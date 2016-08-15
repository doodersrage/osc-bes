<?php
/*
  $Id: all_products.php,v 3.0 2004/02/21 by Ingo (info@gamephisto.de)

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  include(DIR_WS_LANGUAGES . $language . '/' . FILENAME_SITEMAP);

  $breadcrumb->add(HEADING_TITLE, tep_href_link(FILENAME_SITEMAP, '', 'NONSSL'));
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo HEADING_TITLE; ?></title>
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<link rel="stylesheet" type="text/css" href="stylesheet.css">
<style type="text/css" media="screen">
<!--
h1 {font-family: Verdana, Arial, sans-serif; font-size: 10px; font-weight: bold; }
h2 {font-family: Verdana, Arial, sans-serif; font-size: 20px; font-weight: bold; }
h1, h2{margin-bottom:0px; margin-top:0px; line-height: 1em;}
-->
</style>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<!-- body //-->
<table border="0" width="100%" cellspacing="3" cellpadding="3">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><h2><?php echo HEADING_TITLE; ?></h2></td>
       </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
$catLinks = ''; $scatLinks = ''; $manLinks = ''; $prodLinks = ''; $pageLinks = '';
$categories_query = tep_db_query("SELECT c.categories_id, c.parent_id, cd.categories_name FROM " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd WHERE c.categories_id = cd.categories_id AND c.categories_status = '1' AND cd.language_id = '" . (int)$languages_id . "' ORDER BY COALESCE(c.sort_order,1000), cd.categories_name");
while($categories = tep_db_fetch_array($categories_query)) {
  $link = '<a href="' . tep_href_link(FILENAME_DEFAULT, 'cPath=' . $categories['categories_id']) . '">' . $categories['categories_name'] . '</a><br>';
  if ($categories['parent_id']) $scatLinks .= $link;
  else $catLinks .= $link;
}

// Use this to list only manufacturers that have active products
// $manufacturers_query = tep_db_query("select distinct m.manufacturers_id, m.manufacturers_name from " . TABLE_MANUFACTURERS . " m left join " . TABLE_PRODUCTS . " p on m.manufacturers_id = p.manufacturers_id left join " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c on p.products_id = p2c.products_id left join " . TABLE_CATEGORIES . " c on p2c.categories_id = c.categories_id where m.manufacturers_status = '1' and c.categories_status = '1' and p.products_status = '1' order by COALESCE(m.sort_order,10000), m.manufacturers_name");
// Use this to list all active manufacturers
$manufacturers_query = tep_db_query("select m.manufacturers_id, m.manufacturers_name from " . TABLE_MANUFACTURERS . " m where m.manufacturers_status = '1' order by COALESCE(m.sort_order,10000), m.manufacturers_name");
while($manufacturers = tep_db_fetch_array($manufacturers_query)) {
  $manLinks .= '<a href="' . tep_href_link(FILENAME_DEFAULT, 'manufacturers_id=' . $manufacturers['manufacturers_id']) . '">' . $manufacturers['manufacturers_name'] . '</a><br>';
}

$products_query = tep_db_query("SELECT distinct p.products_id, pd.products_name FROM " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " pc, " . TABLE_CATEGORIES . " c WHERE p.products_id = pd.products_id AND p.products_id = pc.products_id AND pc.categories_id = c.categories_id AND c.categories_status = '1' AND p.products_status = '1' AND pd.language_id = '" . (int)$languages_id . "' ORDER BY p.products_id");
while($products = tep_db_fetch_array($products_query)) {
  $prodLinks .= '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $products['products_id']) . '">' . $products['products_name'] . '</a><br>';
}

$page_query = tep_db_query("SELECT pd.pages_name, p.pages_id, p.pages_forward from " . TABLE_PAGES . " p, " . TABLE_PAGES_DESCRIPTION . " pd WHERE p.pages_id = pd.pages_id and p.pages_status = '1' and pd.language_id = '" . (int)$languages_id . "' ORDER BY COALESCE(p.sort_order,1000), p.pages_id");
while($page = tep_db_fetch_array($page_query)){
  if(tep_not_null($page["pages_forward"])) {
    $page_forward = explode('?', $page["pages_forward"]);
    $pageLinks .= '<a href="' . tep_href_link($page_forward[0],isset($page_forward[1])?$page_forward[1]:'') . '">' . $page["pages_name"] . '</a><br>';
  } elseif($page["pages_id"]!=1)
    $pageLinks .= '<a href="' . tep_href_link(FILENAME_PAGES, 'pages_id='.$page["pages_id"]) . '">' . $page["pages_name"] . '</a><br>';
}
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="3" cellpadding="0"><tr><td width="50%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
<?php
if ($catLinks) {
?>
          <tr>
            <td><h3><?php echo CATEGORIES_HEADING; ?></h3></td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
          <tr>
            <td class="smallText"><?php echo $catLinks; ?></td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
<?php
}
if ($scatLinks) {
?>
          <tr>
            <td><h3><?php echo SUBCATEGORIES_HEADING; ?></h3></td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
          <tr>
            <td class="smallText"><?php echo $scatLinks; ?></td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
<?php
}
if ($manLinks) {
?>
          <tr>
            <td><h3><?php echo MANUFACTURERS_HEADING; ?></h3></td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
          <tr>
            <td class="smallText"><?php echo $manLinks; ?></td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
<?php
}
if ($pageLinks || (defined('RESOURCES_LINK') && RESOURCES_LINK)) {
?>
          <tr>
            <td><h3><?php echo PAGES_HEADING; ?></h3></td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
<?php
  if ($pageLinks) {
?>
          <tr>
            <td class="smallText"><?php echo $pageLinks; ?></td>
          </tr>
<?php
  }
  if (defined('RESOURCES_LINK') && RESOURCES_LINK) {
?>
          <tr>
            <td width="33%" colspan="2" class="smallText"><a href="<?php echo RESOURCES_LINK; ?>"><?php echo RESOURCES_TEXT; ?></a></td>
          </tr>
<?php
  }
}
?>
        </table></td><td width="50%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
<?php
if ($prodLinks) {
?>
          <tr>
            <td><h3><?php echo PRODUCTS_HEADING; ?></h3></td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
          <tr>
            <td class="smallText"><?php echo $prodLinks; ?></td>
          </tr>
<?php
}
?>
        </table></td></tr></table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td align="right" class="main"><br><?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT, '', 'NONSSL') . '">' . tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></td>
      </tr>
    </table></td>
<!-- body_text_eof //-->
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
<!-- right_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
<!-- right_navigation_eof //-->
    </table></td>
  </tr>
</table>
<!-- body_eof //-->
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
<!-- You are not the only one, John! -->