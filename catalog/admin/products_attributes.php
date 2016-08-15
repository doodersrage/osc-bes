<?php
/*
  $Id: products_attributes.php,v 1.52 2003/07/10 20:46:01 dgw_ Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  $languages = tep_get_languages();

// >>> BEGIN REGISTER_GLOBALS
    // These variables are accessed directly rather than through $HTTP_GET_VARS or $_GET later in this script
    link_get_variable('option_page');
    link_get_variable('value_page');
    link_get_variable('attribute_page');
    link_get_variable('option_order_by');
    link_get_variable('value_filter');
    link_get_variable('attribute_filter');
// <<< END REGISTER_GLOBALS

  $action = (isset($HTTP_GET_VARS['action']) ? $HTTP_GET_VARS['action'] : '');
// modify action if form was submitted by an option change
  $form_updated = false;
  if (!isset($HTTP_POST_VARS['x'])) {
    if ($action == 'update_product_attribute') {
      $action = 'update_attribute'; $form_updated = true;
    } elseif ($action == 'add_product_attributes') {
      $action = ''; $form_updated = true;
    }
  }

  if (isset($HTTP_POST_VARS['option_order_by'])) {
    $option_order_by = $HTTP_POST_VARS['option_order_by'];
    $HTTP_GET_VARS['option_order_by'] = $option_order_by;
  }
  if (isset($HTTP_POST_VARS['value_filter'])) {
    $value_filter = $HTTP_POST_VARS['value_filter'];
    $HTTP_GET_VARS['value_filter'] = $value_filter;
  }
  if (isset($HTTP_POST_VARS['attribute_filter'])) {
    $attribute_filter = $HTTP_POST_VARS['attribute_filter'];
    $HTTP_GET_VARS['attribute_filter'] = $attribute_filter;
  }

  $page_info = '';
  if (isset($HTTP_GET_VARS['option_page'])) $page_info .= 'option_page=' . $HTTP_GET_VARS['option_page'] . '&';
  if (isset($HTTP_GET_VARS['value_page'])) $page_info .= 'value_page=' . $HTTP_GET_VARS['value_page'] . '&';
  if (isset($HTTP_GET_VARS['attribute_page'])) $page_info .= 'attribute_page=' . $HTTP_GET_VARS['attribute_page'] . '&';
  if (isset($HTTP_GET_VARS['option_order_by'])) $page_info .= 'option_order_by=' . $HTTP_GET_VARS['option_order_by'] . '&';
  else $option_order_by = 'sort';
  if (isset($HTTP_GET_VARS['value_filter'])) $page_info .= 'value_filter=' . $HTTP_GET_VARS['value_filter'] . '&';
  else $value_filter = '0';
  if (isset($HTTP_GET_VARS['attribute_filter'])) $page_info .= 'attribute_filter=' . $HTTP_GET_VARS['attribute_filter'] . '&';
  else $attribute_filter = '0';
  if (tep_not_null($page_info)) {
    $page_info = substr($page_info, 0, -1);
  }

  if (tep_not_null($action)) {
    switch ($action) {
      case 'add_product_options':
        $products_options_id = tep_db_prepare_input($HTTP_POST_VARS['products_options_id']);
        $option_name_array = $HTTP_POST_VARS['option_name'];
        $option_comment_array = $HTTP_POST_VARS['option_comment'];	//clr 030714 update to add option comment to products_option
        $option_type = $HTTP_POST_VARS['option_type'];	//clr 030714 update to add option type to products_option
		$option_length = $HTTP_POST_VARS['option_length'];	//clr 030714 update to add option length to products_option
/* BOF: Attribute Sort/Copy */
        $sort_order = tep_db_prepare_input($HTTP_POST_VARS['options_sort_order']);
        $sort_order = is_numeric($sort_order)?"'".(int)$sort_order."'":'null';
/* EOF: Attribute Sort/Copy */

        for ($i=0, $n=sizeof($languages); $i<$n; $i ++) {
          $option_name = tep_db_prepare_input($option_name_array[$languages[$i]['id']]);
          $option_comment = tep_db_prepare_input($option_comment_array[$languages[$i]['id']]);

// modified for Attribute Sort/Copy
//        tep_db_query("insert into " . TABLE_PRODUCTS_OPTIONS . " (products_options_id, products_options_name, language_id, products_options_type, products_options_length, products_options_comment) values ('" . (int)$products_options_id . "', '" . tep_db_input($option_name) . "', '" . (int)$languages[$i]['id'] . "', '" . $option_type . "', '" . $option_length . "', '" . tep_db_input($option_comment)  . "')");
          tep_db_query("insert into " . TABLE_PRODUCTS_OPTIONS . " (products_options_id, products_options_name, language_id, products_options_type, products_options_length, products_options_comment, products_options_sort_order) values ('" . (int)$products_options_id . "', '" . tep_db_input($option_name) . "', '" . (int)$languages[$i]['id'] . "', '" . $option_type . "', '" . $option_length . "', '" . tep_db_input($option_comment)  . "', $sort_order)");
	      if($option_type != 0 && $option_type != 2){
            tep_db_query("insert into " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " (products_options_id, products_options_values_id) values ('" . (int)$products_options_id . "', '0')");
          }
        }
        tep_redirect(tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info));
        break;
      case 'add_product_option_values':
        $value_name_array = $HTTP_POST_VARS['value_name'];
        $value_id = tep_db_prepare_input($HTTP_POST_VARS['value_id']);
        $option_id = tep_db_prepare_input($HTTP_POST_VARS['option_id']);
/* BOF: Attribute Sort/Copy */
        $sort_order = tep_db_prepare_input($HTTP_POST_VARS['values_sort_order']);
        $sort_order = is_numeric($sort_order)?"'".(int)$sort_order."'":'null';
/* EOF: Attribute Sort/Copy */

        for ($i=0, $n=sizeof($languages); $i<$n; $i ++) {
          $value_name = tep_db_prepare_input($value_name_array[$languages[$i]['id']]);

          tep_db_query("insert into " . TABLE_PRODUCTS_OPTIONS_VALUES . " (products_options_values_id, language_id, products_options_values_name) values ('" . (int)$value_id . "', '" . (int)$languages[$i]['id'] . "', '" . tep_db_input($value_name) . "')");
        }

//      tep_db_query("insert into " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " (products_options_id, products_options_values_id) values ('" . (int)$option_id . "', '" . (int)$value_id . "')");
/* BOF: Attribute Sort/Copy */
        tep_db_query("insert into " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " (products_options_id, products_options_values_id, products_options_values_sort_order) values ('" . (int)$option_id . "', '" . (int)$value_id . "', $sort_order)");
/* EOF: Attribute Sort/Copy */

        tep_redirect(tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info));
        break;
      case 'add_product_attributes':
        $products_id = tep_db_prepare_input($HTTP_POST_VARS['attributes_products_id']);
        $options_id = tep_db_prepare_input($HTTP_POST_VARS['attributes_options_id']);
        $values_id = tep_db_prepare_input($HTTP_POST_VARS['attributes_values_id']);
        $value_price = tep_db_prepare_input($HTTP_POST_VARS['value_price']);
        $price_prefix = tep_db_prepare_input($HTTP_POST_VARS['price_prefix']);

// BOF: WebMakers.com Added: Attribute Sorter
        if (!isset($HTTP_POST_VARS['attributes_sort_order']) || (isset($HTTP_POST_VARS['attributes_sort_order']) && $HTTP_POST_VARS['attributes_sort_order'] == '')) {
        /* sort_order is not set, so find the one from the Option Value */
          $lookup = tep_db_query("select products_options_values_sort_order from " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " WHERE products_options_values_id = '" . $values_id . "'");
          $lookup_res = tep_db_fetch_array($lookup);
          $att_sort_order = $lookup_res['products_options_values_sort_order'];
        } else {
          $att_sort_order = tep_db_prepare_input($HTTP_POST_VARS['attributes_sort_order']);
        }
        $att_sort_order = is_numeric($att_sort_order)?"'".(int)$att_sort_order."'":'null';

        tep_db_query("insert into " . TABLE_PRODUCTS_ATTRIBUTES . " values ('', '" . (int)$products_id . "', '" . (int)$options_id . "', '" . (int)$values_id . "', '" . tep_db_input($value_price) . "', '" . tep_db_input($price_prefix) . "', $att_sort_order)");
// EOF: WebMakers.com Added: Attribute Sorter
//      tep_db_query("insert into " . TABLE_PRODUCTS_ATTRIBUTES . " values ('', '" . (int)$products_id . "', '" . (int)$options_id . "', '" . (int)$values_id . "', '" . tep_db_input($value_price) . "', '" . tep_db_input($price_prefix) . "')");

        if (DOWNLOAD_ENABLED == 'true') {
          $products_attributes_id = tep_db_insert_id();

          $products_attributes_filename = tep_db_prepare_input($HTTP_POST_VARS['products_attributes_filename']);
          $products_attributes_maxdays = tep_db_prepare_input($HTTP_POST_VARS['products_attributes_maxdays']);
          $products_attributes_maxcount = tep_db_prepare_input($HTTP_POST_VARS['products_attributes_maxcount']);

          if (tep_not_null($products_attributes_filename)) {
            tep_db_query("insert into " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " values (" . (int)$products_attributes_id . ", '" . tep_db_input($products_attributes_filename) . "', '" . tep_db_input($products_attributes_maxdays) . "', '" . tep_db_input($products_attributes_maxcount) . "')");
          }
        }

        tep_redirect(tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info));
        break;
      case 'update_option_name':
        $option_name_array = $HTTP_POST_VARS['option_name'];
        $option_comment_array = $HTTP_POST_VARS['option_comment'];	//clr 030714 update to add option comment to products_option
	    $option_type = $HTTP_POST_VARS['option_type'];	//clr 030714 update to add option type to products_option
	    $option_length = $HTTP_POST_VARS['option_length'];	//clr 030714 update to add option length to products_option
        $option_id = tep_db_prepare_input($HTTP_POST_VARS['products_option_id']);
/* BOF: Attribute Sort/Copy */
        $sort_order = tep_db_prepare_input($HTTP_POST_VARS['options_sort_order']);
        $sort_order = is_numeric($sort_order)?"'".(int)$sort_order."'":'null';
/* EOF: Attribute Sort/Copy */

        for ($i=0, $n=sizeof($languages); $i<$n; $i ++) {
          $option_name = tep_db_prepare_input($option_name_array[$languages[$i]['id']]);	
          $option_comment = tep_db_prepare_input($option_comment_array[$languages[$i]['id']]);

// modified for Attribute Sort/Copy
//        tep_db_query("update " . TABLE_PRODUCTS_OPTIONS . " set products_options_name = '" . tep_db_input($option_name) . "', products_options_type = '" . $option_type . "', products_options_length = '" . $option_length . "', products_options_comment = '" . tep_db_input($option_comment) . "' where products_options_id = '" . (int)$option_id . "' and language_id = '" . (int)$languages[$i]['id'] . "'");
          tep_db_query("update " . TABLE_PRODUCTS_OPTIONS . " set products_options_name = '" . tep_db_input($option_name) . "', products_options_type = '" . $option_type . "', products_options_length = '" . $option_length . "', products_options_comment = '" . tep_db_input($option_comment) . "', products_options_sort_order = $sort_order where products_options_id = '" . (int)$option_id . "' and language_id = '" . (int)$languages[$i]['id'] . "'");
        }

        tep_redirect(tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info));
        break;
      case 'update_value':
        $value_name_array = $HTTP_POST_VARS['value_name'];
        $value_id = tep_db_prepare_input($HTTP_POST_VARS['value_id']);
        $option_id = tep_db_prepare_input($HTTP_POST_VARS['option_id']);
/* BOF: Attributes Sort/Copy */
        $sort_order = tep_db_prepare_input($HTTP_POST_VARS['values_sort_order']);
        $sort_order = is_numeric($sort_order)?"'".(int)$sort_order."'":'null';
/* EOF: Attributes Sort/Copy */

        for ($i=0, $n=sizeof($languages); $i<$n; $i ++) {
          $value_name = tep_db_prepare_input($value_name_array[$languages[$i]['id']]);

          tep_db_query("update " . TABLE_PRODUCTS_OPTIONS_VALUES . " set products_options_values_name = '" . tep_db_input($value_name) . "' where products_options_values_id = '" . tep_db_input($value_id) . "' and language_id = '" . (int)$languages[$i]['id'] . "'");
        }

//      tep_db_query("update " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " set products_options_id = '" . (int)$option_id . "'  where products_options_values_id = '" . (int)$value_id . "'");
/* BOF: Attributes Sort/Copy */
        tep_db_query("update " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " set products_options_id = '" . (int)$option_id . "', products_options_values_sort_order = $sort_order where products_options_values_id = '" . (int)$value_id . "'");
/* EOF: Attributes Sort/Copy */


        tep_redirect(tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info));
        break;
      case 'update_product_attribute':
        $products_id = tep_db_prepare_input($HTTP_POST_VARS['attributes_products_id']);
        $options_id = tep_db_prepare_input($HTTP_POST_VARS['attributes_options_id']);
        $values_id = tep_db_prepare_input($HTTP_POST_VARS['attributes_values_id']);
        $value_price = tep_db_prepare_input($HTTP_POST_VARS['value_price']);
        $price_prefix = tep_db_prepare_input($HTTP_POST_VARS['price_prefix']);
        $attribute_id = tep_db_prepare_input($HTTP_POST_VARS['attribute_id']);
// BOF: WebMakers.com Added: Attribute Sorter
        $att_sort_order = tep_db_prepare_input($HTTP_POST_VARS['attributes_sort_order']);
        $att_sort_order = is_numeric($att_sort_order)?"'".(int)$att_sort_order."'":'null';

        tep_db_query("update " . TABLE_PRODUCTS_ATTRIBUTES . " set products_id = '" . (int)$products_id . "', options_id = '" . (int)$options_id . "', options_values_id = '" . (int)$values_id . "', options_values_price = '" . tep_db_input($value_price) . "', price_prefix = '" . tep_db_input($price_prefix) . "', products_attributes_sort_order = $att_sort_order where products_attributes_id = '" . (int)$attribute_id . "'");
// EOF: WebMakers.com Added: Attribute Sorter
//      tep_db_query("update " . TABLE_PRODUCTS_ATTRIBUTES . " set products_id = '" . (int)$products_id . "', options_id = '" . (int)$options_id . "', options_values_id = '" . (int)$values_id . "', options_values_price = '" . tep_db_input($value_price) . "', price_prefix = '" . tep_db_input($price_prefix) . "' where products_attributes_id = '" . (int)$attribute_id . "'");

        if (DOWNLOAD_ENABLED == 'true') {
          $products_attributes_filename = tep_db_prepare_input($HTTP_POST_VARS['products_attributes_filename']);
          $products_attributes_maxdays = tep_db_prepare_input($HTTP_POST_VARS['products_attributes_maxdays']);
          $products_attributes_maxcount = tep_db_prepare_input($HTTP_POST_VARS['products_attributes_maxcount']);

          if (tep_not_null($products_attributes_filename)) {
            tep_db_query("replace into " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " set products_attributes_id = '" . (int)$attribute_id . "', products_attributes_filename = '" . tep_db_input($products_attributes_filename) . "', products_attributes_maxdays = '" . tep_db_input($products_attributes_maxdays) . "', products_attributes_maxcount = '" . tep_db_input($products_attributes_maxcount) . "'");
          }
        }

        tep_redirect(tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info));
        break;
      case 'delete_option':
        $option_id = tep_db_prepare_input($HTTP_GET_VARS['products_option_id']);

        tep_db_query("delete from " . TABLE_PRODUCTS_OPTIONS . " where products_options_id = '" . (int)$option_id . "'");
// added by splautz to ensure TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS is up to date
        tep_db_query("delete from " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " where products_options_id = '" . (int)$option_id . "'");

        tep_redirect(tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info));
        break;
      case 'delete_value':
        $value_id = tep_db_prepare_input($HTTP_GET_VARS['value_id']);

        tep_db_query("delete from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where products_options_values_id = '" . (int)$value_id . "'");
        tep_db_query("delete from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where products_options_values_id = '" . (int)$value_id . "'");
        tep_db_query("delete from " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " where products_options_values_id = '" . (int)$value_id . "'");

        tep_redirect(tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info));
        break;
      case 'delete_attribute':
        $attribute_id = tep_db_prepare_input($HTTP_GET_VARS['attribute_id']);

        tep_db_query("delete from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_attributes_id = '" . (int)$attribute_id . "'");

// added for DOWNLOAD_ENABLED. Always try to remove attributes, even if downloads are no longer enabled
        tep_db_query("delete from " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " where products_attributes_id = '" . (int)$attribute_id . "'");

        tep_redirect(tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info));
        break;
    }
  }

//CLR 030312 add function to draw pulldown list of option types
// Draw a pulldown for Option Types
function draw_optiontype_pulldown($name, $default = '') {
  if (!is_numeric($default)) $select = 0;
  elseif ($default == 0 || $default == 2) $select = 1;
  else $select = 2;

  $values = array();
  if ($select != 2) $values[] = array('id' => 0, 'text' => 'Select');
  if ($select != 1) $values[] = array('id' => 1, 'text' => 'Text');
  if ($select != 2) $values[] = array('id' => 2, 'text' => 'Radio');
  if ($select != 1) $values[] = array('id' => 3, 'text' => 'Checkbox');
  if ($select != 1) $values[] = array('id' => 4, 'text' => 'Textarea');
  return tep_draw_pull_down_menu($name, $values, $default);
}

//CLR 030312 add function to translate type_id to name
// Translate option_type_values to english string
function translate_type_to_name($opt_type) {
  if ($opt_type == 0) return 'Select';
  if ($opt_type == 1) return 'Text';
  if ($opt_type == 2) return 'Radio';
  if ($opt_type == 3) return 'Checkbox';
  if ($opt_type == 4) return 'Textarea';
  return 'Error ' . $opt_type;
}

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
<!-- options and values//-->
      <tr>
        <td width="100%"><table width="100%" border="0" cellspacing="5" cellpadding="0">
          <tr>
            <td valign="top" width="50%"><table width="100%" border="0" cellspacing="0" cellpadding="2">
<!-- options //-->
<?php
  if ($action == 'delete_product_option') { // delete product option
    $options = tep_db_query("select products_options_id, products_options_name from " . TABLE_PRODUCTS_OPTIONS . " where products_options_id = '" . (int)$HTTP_GET_VARS['products_option_id'] . "' and language_id = '" . (int)$languages_id . "'");
    $options_values = tep_db_fetch_array($options);
?>
              <tr>
                <td class="pageHeading">&nbsp;<?php echo $options_values['products_options_name']; ?>&nbsp;</td>
                <td>&nbsp;<?php echo tep_image(DIR_WS_IMAGES . 'pixel_trans.gif', '', '1', '53'); ?>&nbsp;</td>
              </tr>
              <tr>
                <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td colspan="3"><?php echo tep_black_line(); ?></td>
                  </tr>
<?php
    $products = tep_db_query("select p.products_id, pd.products_name, pov.products_options_values_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov, " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_DESCRIPTION . " pd where pd.products_id = p.products_id and pov.language_id = '" . (int)$languages_id . "' and pd.language_id = '" . (int)$languages_id . "' and pa.products_id = p.products_id and pa.options_id='" . (int)$HTTP_GET_VARS['products_option_id'] . "' and pov.products_options_values_id = pa.options_values_id order by pd.products_name");
    if (tep_db_num_rows($products)) {
?>
                  <tr class="dataTableHeadingRow">
                    <td class="dataTableHeadingContent" align="center">&nbsp;<?php echo TABLE_HEADING_ID; ?>&nbsp;</td>
                    <td class="dataTableHeadingContent">&nbsp;<?php echo TABLE_HEADING_PRODUCT; ?>&nbsp;</td>
                    <td class="dataTableHeadingContent">&nbsp;<?php echo TABLE_HEADING_OPT_VALUE; ?>&nbsp;</td>
                  </tr>
                  <tr>
                    <td colspan="3"><?php echo tep_black_line(); ?></td>
                  </tr>
<?php
      $rows = 0;
      while ($products_values = tep_db_fetch_array($products)) {
        $rows++;
?>
                  <tr class="<?php echo (floor($rows/2) == ($rows/2) ? 'attributes-even' : 'attributes-odd'); ?>">
                    <td align="center" class="smallText">&nbsp;<?php echo $products_values['products_id']; ?>&nbsp;</td>
                    <td class="smallText">&nbsp;<?php echo $products_values['products_name']; ?>&nbsp;</td>
                    <td class="smallText">&nbsp;<?php echo $products_values['products_options_values_name']; ?>&nbsp;</td>
                  </tr>
<?php
      }
?>
                  <tr>
                    <td colspan="3"><?php echo tep_black_line(); ?></td>
                  </tr>
                  <tr>
                    <td colspan="3" class="main"><br><?php echo TEXT_WARNING_OF_DELETE; ?></td>
                  </tr>
                  <tr>
                    <td align="right" colspan="3" class="main"><br><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info, 'NONSSL') . '">'; ?><?php echo tep_image_button('button_cancel.gif', ' cancel '); ?></a>&nbsp;</td>
                  </tr>
<?php
    } else {
?>
                  <tr>
                    <td class="main" colspan="3"><br><?php echo TEXT_OK_TO_DELETE; ?></td>
                  </tr>
                  <tr>
                    <td class="main" align="right" colspan="3"><br><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=delete_option&products_option_id='.$HTTP_GET_VARS['products_option_id'].'&'.$page_info, 'NONSSL') . '">'; ?><?php echo tep_image_button('button_delete.gif', ' delete '); ?></a>&nbsp;&nbsp;&nbsp;<?php echo '<a href="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info, 'NONSSL') . '">'; ?><?php echo tep_image_button('button_cancel.gif', ' cancel '); ?></a>&nbsp;</td>
                  </tr>
<?php
    }
?>
                </table></td>
              </tr>
<?php
  } else {
?>
              <tr>
        <td colspan="7"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading" width="100%">&nbsp;<?php echo HEADING_TITLE_OPT; ?>&nbsp;</td>
            <td align="right" class="main" nowrap><form name="option_order_by_form" action="<?php echo tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info, 'NONSSL'); ?>" method="post">
            <?php echo TEXT_OPTION_SORT_BY; ?>&nbsp;<select name="option_order_by" onChange="this.form.submit();">
<?php
    $option_order_by_array = array(array(TEXT_OPTION_SORT,'sort'),array(TEXT_OPTION_ID,'products_options_id'),array(TEXT_OPTION_NAME,'products_options_name'));
    foreach($option_order_by_array as $order_select) {
      echo '<option value="'.$order_select[1].'"'.($order_select[1]==$option_order_by?' SELECTED':'').'>'.$order_select[0].'</option>';
    }
?>            </select></form></td>
            <td>&nbsp;<?php echo tep_image(DIR_WS_IMAGES . 'pixel_trans.gif', '', '1', '53'); ?>&nbsp;</td>
          </tr>
        </table></td>
              </tr>
              <tr>
                <td colspan="3" class="smallText">
<?php
    $per_page = MAX_ROW_LISTS_OPTIONS;
//  $options = "select * from " . TABLE_PRODUCTS_OPTIONS . " where language_id = '" . (int)$languages_id . "' order by " . $option_order_by;
    $options = "select * from " . TABLE_PRODUCTS_OPTIONS . " where language_id = '" . (int)$languages_id . "' order by " . ($option_order_by=='sort'?'COALESCE(products_options_sort_order,10000),products_options_id':$option_order_by);

    if (!isset($option_page)) {
      $option_page = 1;
    }
    $prev_option_page = $option_page - 1;
    $next_option_page = $option_page + 1;

    $option_query = tep_db_query($options);

    $option_page_start = ($per_page * $option_page) - $per_page;
    $num_rows = tep_db_num_rows($option_query);

    if ($num_rows <= $per_page) {
      $num_pages = 1;
    } else if (($num_rows % $per_page) == 0) {
      $num_pages = ($num_rows / $per_page);
    } else {
      $num_pages = ($num_rows / $per_page) + 1;
    }
    $num_pages = (int) $num_pages;

    $options = $options . " LIMIT $option_page_start, $per_page";

    // Previous
    if ($prev_option_page)  {
      echo '<a href="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, "option_page=$prev_option_page&".tep_get_all_get_params(array('action','option_page','option_id','value_id','attribute_id'))) . '"> &lt;&lt; </a> | ';
    }

    for ($i = 1; $i <= $num_pages; $i++) {
      if ($i != $option_page) {
        echo '<a href="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, "option_page=$i&".tep_get_all_get_params(array('action','option_page','option_id','value_id','attribute_id'))) . '">' . $i . '</a> | ';
      } else {
        echo '<b><font color=red>' . $i . '</font></b> | ';
      }
    }

    // Next
    if ($option_page != $num_pages) {
      echo '<a href="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, "option_page=$next_option_page&".tep_get_all_get_params(array('action','option_page','option_id','value_id','attribute_id'))) . '"> &gt;&gt; </a>';
    }
//CLR 030212 - Add column for option type
?>
                </td>
              </tr>
              <tr>
                <td colspan="7"><?php echo tep_black_line(); ?></td>
              </tr>
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent">&nbsp;<?php echo TABLE_HEADING_ID; ?>&nbsp;</td>
                <td class="dataTableHeadingContent">&nbsp;<?php echo TABLE_HEADING_OPT_NAME; ?>&nbsp;</td>
				<td class="dataTableHeadingContent">&nbsp;<?php echo TABLE_HEADING_OPT_TYPE; ?>&nbsp;</td>	<!-- CLR 030212 - Add column for option type //-->
    			<td class="dataTableHeadingContent">&nbsp;<?php echo TABLE_HEADING_OPT_LENGTH; ?>&nbsp;</td>	<!-- CLR 030212 - Add column for option length //-->
                <td class="dataTableHeadingContent">&nbsp;<?php echo TABLE_HEADING_OPT_COMMENT; ?>&nbsp;</td>	<!-- CLR 030212 - Add column for option comment //-->
<?php /* BOF: Attributes sort/copy */ ?>
                <td class="dataTableHeadingContent" align="center">&nbsp;<?php echo TABLE_HEADING_OPT_SORT_ORDER; ?>&nbsp;</td>
<?php /* EOF: Attributes sort/copy */ ?>
                <td class="dataTableHeadingContent" align="center">&nbsp;<?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
              <tr>
                <td colspan="7"><?php echo tep_black_line(); ?></td>
              </tr>
<?php
    $next_id = 1;
    $rows = 0;
    $options = tep_db_query($options);
    while ($options_values = tep_db_fetch_array($options)) {
      $sort_order = $options_values['products_options_sort_order'];
      $rows++;
?>
              <tr class="<?php echo (floor($rows/2) == ($rows/2) ? 'attributes-even' : 'attributes-odd'); ?>">
<?php
      if (($action == 'update_option') && ($HTTP_GET_VARS['products_option_id'] == $options_values['products_options_id'])) {
        echo '<form name="option" action="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=update_option_name&'.$page_info, 'NONSSL') . '" method="post">';
        $inputs = ''; $inputs2 = '';
        for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
          $option_name = tep_db_query("select products_options_name, products_options_length, products_options_comment from " . TABLE_PRODUCTS_OPTIONS . " where products_options_id = '" . $options_values['products_options_id'] . "' and language_id = '" . $languages[$i]['id'] . "'");
          $option_name = tep_db_fetch_array($option_name);
 		  $inputs .= $languages[$i]['code'] . ':&nbsp;<input type="text" name="option_name[' . $languages[$i]['id'] . ']" size="16" value="' . $option_name['products_options_name'] . '">&nbsp;<br>';
 		  $inputs2 .= $languages[$i]['code'] . ':&nbsp;<input type="text" name="option_comment[' . $languages[$i]['id'] . ']" size="16" value="' . $option_name['products_options_comment'] . '">&nbsp;<br>';
        }
//CLR 030212 - Add column for option type
?>
                <td align="center" class="smallText">&nbsp;<?php echo $options_values['products_options_id']; ?><input type="hidden" name="products_option_id" value="<?php echo $options_values['products_options_id']; ?>">&nbsp;</td>
				<td class="smallText" nowrap><?php echo $inputs; ?></td>
				<td class="smallText"><?php echo draw_optiontype_pulldown('option_type', $options_values['products_options_type']); ?></td>	<!-- CLR 030212 - Add column for option type //-->
				<td class="smallText"><?php echo '&nbsp;<input type="text" name="option_length" size="4" value="' . $option_name['products_options_length'] . '">'; ?></td>	<!-- CLR 030212 - Add column for option length //-->
				<td class="smallText" nowrap><?php echo $inputs2; ?></td>
<?php /* BOF: Attributes sort/copy */ ?>
                <td align="center" class="smallText">&nbsp;<input type="text" name="options_sort_order" size="3" value="<?php echo $sort_order; ?>">&nbsp;</td>
<?php /* EOF: Attributes sort/copy */ ?>
                <td align="center" class="smallText">&nbsp;<?php echo tep_image_submit('button_update.gif', IMAGE_UPDATE); ?>&nbsp;<?php echo '<a href="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info, 'NONSSL') . '">'; ?><?php echo tep_image_button('button_cancel.gif', IMAGE_CANCEL); ?></a>&nbsp;</td>
<?php
        echo '</form>' . "\n";
      } else {
//CLR 030212 - Add column for option type
?>
                <td align="center" class="smallText">&nbsp;<?php echo $options_values["products_options_id"]; ?>&nbsp;</td>
                <td class="smallText">&nbsp;<?php echo $options_values["products_options_name"]; ?>&nbsp;</td>
                <td class="smallText">&nbsp;<?php echo translate_type_to_name($options_values["products_options_type"]); ?>&nbsp;</td> <!-- CLR 030212 - Add column for option type //-->
				<td class="smallText">&nbsp;<?php echo $options_values["products_options_length"]; ?>&nbsp;</td>	<!-- CLR 030212 - Add column for option length //-->
				<td class="smallText">&nbsp;<?php echo $options_values["products_options_comment"]; ?>&nbsp;</td>	<!-- CLR 030212 - Add column for option comment //-->
<?php /* BOF: Attributes sort/copy */ ?>
                <td align="center" class="smallText" size="3">&nbsp;<?php echo $sort_order; ?>&nbsp;</td>
<?php /* EOF: Attributes sort/copy */ ?>
                <td align="center" class="smallText">&nbsp;<?php echo '<a href="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=update_option&products_option_id=' . $options_values['products_options_id'] . '&' . $page_info, 'NONSSL') . '">'; ?><?php echo tep_image_button('button_edit.gif', IMAGE_UPDATE); ?></a>&nbsp;&nbsp;<?php echo '<a href="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=delete_product_option&products_option_id=' . $options_values['products_options_id'] . '&' . $page_info, 'NONSSL') , '">'; ?><?php echo tep_image_button('button_delete.gif', IMAGE_DELETE); ?></a>&nbsp;</td>
<?php
      }
?>
              </tr>
<?php
    }
?>
              <tr>
                <td colspan="7"><?php echo tep_black_line(); ?></td>
              </tr>
<?php
    if ($action != 'update_option') {
      $max_options_id_query = tep_db_query("select max(products_options_id) + 1 as next_id from " . TABLE_PRODUCTS_OPTIONS);
      $max_options_id_values = tep_db_fetch_array($max_options_id_query);
      if (is_numeric($max_options_id_values['next_id'])) $next_id = $max_options_id_values['next_id'];
?>
              <tr class="<?php echo (floor($rows/2) == ($rows/2) ? 'attributes-even' : 'attributes-odd'); ?>">
<?php
      echo '<form name="options" action="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=add_product_options&'.$page_info, 'NONSSL') . '" method="post"><input type="hidden" name="products_options_id" value="' . $next_id . '">';
      $inputs = ''; $inputs2 = '';
      for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
		$inputs .= $languages[$i]['code'] . ':&nbsp;<input type="text" name="option_name[' . $languages[$i]['id'] . ']" size="16">&nbsp;<br>';
		$inputs2 .= $languages[$i]['code'] . ':&nbsp;<input type="text" name="option_comment[' . $languages[$i]['id'] . ']" size="16">&nbsp;<br>';
      }
//CLR 030212 - Add column for option type
?>
                <td align="center" class="smallText">&nbsp;<?php echo $next_id; ?>&nbsp;</td>
                <td class="smallText" nowrap><?php echo $inputs; ?></td>
                <td class="smallText"><?php echo draw_optiontype_pulldown('option_type'); ?></td>	<!-- CLR 030212 - Add column for option type //-->
                <td class="smallText"><?php echo '<input type="text" name="option_length" size="4" value="' . $option_name['products_options_length'] . '">'; ?></td>	<!-- CLR 030212 - Add column for option length //-->
                <td class="smallText" nowrap><?php echo $inputs2; ?></td>
<?php /* BOF: Attribute sort/copy */ ?>
                <td class="smallText"><input type="text" name="options_sort_order" size="3"></td> 
<?php /* EOF: Attribute sort/copy */ ?>
                <td align="center" class="smallText">&nbsp;<?php echo tep_image_submit('button_insert.gif', IMAGE_INSERT); ?>&nbsp;</td>
<?php
      echo '</form>';
?>
              </tr>
              <tr>
                <td colspan="7"><?php echo tep_black_line(); ?></td>
              </tr>
<?php
    }
  }
?>
            </table></td>
<!-- options eof //-->
            <td valign="top" width="50%"><table width="100%" border="0" cellspacing="0" cellpadding="2">
<!-- value //-->
<?php
  if ($action == 'delete_option_value') { // delete product option value
    $values = tep_db_query("select products_options_values_id, products_options_values_name from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where products_options_values_id = '" . (int)$HTTP_GET_VARS['value_id'] . "' and language_id = '" . (int)$languages_id . "'");
    $values_values = tep_db_fetch_array($values);
?>
              <tr>
                <td colspan="3" class="pageHeading">&nbsp;<?php echo $values_values['products_options_values_name']; ?>&nbsp;</td>
                <td>&nbsp;<?php echo tep_image(DIR_WS_IMAGES . 'pixel_trans.gif', '', '1', '53'); ?>&nbsp;</td>
              </tr>
              <tr>
                <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td colspan="3"><?php echo tep_black_line(); ?></td>
                  </tr>
<?php
    $products = tep_db_query("select p.products_id, pd.products_name, po.products_options_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_OPTIONS . " po, " . TABLE_PRODUCTS_DESCRIPTION . " pd where pd.products_id = p.products_id and pd.language_id = '" . (int)$languages_id . "' and po.language_id = '" . (int)$languages_id . "' and pa.products_id = p.products_id and pa.options_values_id='" . (int)$HTTP_GET_VARS['value_id'] . "' and po.products_options_id = pa.options_id order by pd.products_name");
    if (tep_db_num_rows($products)) {
?>
                  <tr class="dataTableHeadingRow">
                    <td class="dataTableHeadingContent" align="center">&nbsp;<?php echo TABLE_HEADING_ID; ?>&nbsp;</td>
                    <td class="dataTableHeadingContent">&nbsp;<?php echo TABLE_HEADING_PRODUCT; ?>&nbsp;</td>
                    <td class="dataTableHeadingContent">&nbsp;<?php echo TABLE_HEADING_OPT_NAME; ?>&nbsp;</td>
                  </tr>
                  <tr>
                    <td colspan="3"><?php echo tep_black_line(); ?></td>
                  </tr>
<?php
      while ($products_values = tep_db_fetch_array($products)) {
        $rows++;
?>
                  <tr class="<?php echo (floor($rows/2) == ($rows/2) ? 'attributes-even' : 'attributes-odd'); ?>">
                    <td align="center" class="smallText">&nbsp;<?php echo $products_values['products_id']; ?>&nbsp;</td>
                    <td class="smallText">&nbsp;<?php echo $products_values['products_name']; ?>&nbsp;</td>
                    <td class="smallText">&nbsp;<?php echo $products_values['products_options_name']; ?>&nbsp;</td>
                  </tr>
<?php
      }
?>
                  <tr>
                    <td colspan="3"><?php echo tep_black_line(); ?></td>
                  </tr>
                  <tr>
                    <td class="main" colspan="3"><br><?php echo TEXT_WARNING_OF_DELETE; ?></td>
                  </tr>
                  <tr>
                    <td class="main" align="right" colspan="3"><br><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info, 'NONSSL') . '">'; ?><?php echo tep_image_button('button_cancel.gif', ' cancel '); ?></a>&nbsp;</td>
                  </tr>
<?php
    } else {
?>
                  <tr>
                    <td class="main" colspan="3"><br><?php echo TEXT_OK_TO_DELETE; ?></td>
                  </tr>
                  <tr>
                    <td class="main" align="right" colspan="3"><br><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=delete_value&value_id='.$HTTP_GET_VARS['value_id'].'&'.$page_info, 'NONSSL') . '">'; ?><?php echo tep_image_button('button_delete.gif', ' delete '); ?></a>&nbsp;&nbsp;&nbsp;<?php echo '<a href="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info, 'NONSSL') . '">'; ?><?php echo tep_image_button('button_cancel.gif', ' cancel '); ?></a>&nbsp;</td>
                  </tr>
<?php
    }
?>
              	</table></td>
              </tr>
<?php
  } else {
?>
              <tr>
        <td width="100%" colspan="5"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading" width="100%">&nbsp;<?php echo HEADING_TITLE_VAL; ?>&nbsp;</td>
            <td align="right" class="main" nowrap><form name="value_filter_form" action="<?php echo tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES,  tep_get_all_get_params(array('action','value_page','attribute_page','option_id','value_id','attribute_id')), 'NONSSL'); ?>" method="post">
            <?php echo TEXT_VALUE_FILTER; ?>&nbsp;<select name="value_filter" onChange="this.form.submit();"><option value="0"><?php echo TEXT_VALUE_ALL; ?></option>
<?php
    $options_query = tep_db_query("select products_options_id, products_options_name, products_options_type from " . TABLE_PRODUCTS_OPTIONS . " where language_id = '" . (int)$languages_id . "' order by COALESCE(products_options_sort_order,10000), products_options_id");
    while($options = tep_db_fetch_array($options_query)) {
      echo '<option value="'.$options['products_options_id'].'"'.($options['products_options_id']==(int)$value_filter?' SELECTED':'').'>'.$options['products_options_name'].'</option>';
    }
?>            </select></form></td>
            <td>&nbsp;<?php echo tep_image(DIR_WS_IMAGES . 'pixel_trans.gif', '', '1', '53'); ?>&nbsp;</td>
          </tr>
        </table></td>
              </tr>
              <tr>
                <td colspan="4" class="smallText">
<?php
    $per_page = MAX_ROW_LISTS_OPTIONS;
//  $values = "select pov.products_options_values_id, pov.products_options_values_name, pov2po.products_options_id from " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov left join " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " pov2po on pov.products_options_values_id = pov2po.products_options_values_id where pov.language_id = '" . (int)$languages_id . "' order by pov.products_options_values_id";
    $values = "select pov.products_options_values_id, pov.products_options_values_name, pov2po.products_options_id, pov2po.products_options_values_sort_order from " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov left join " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " pov2po on pov.products_options_values_id = pov2po.products_options_values_id left join " . TABLE_PRODUCTS_OPTIONS . " po on po.products_options_id = pov2po.products_options_id and po.language_id = '" . (int)$languages_id . "' where pov2po.products_options_values_id > '0' " . ((int)$value_filter?"and pov2po.products_options_id = '".(int)$value_filter."' ":'') . "and pov.language_id = '" . (int)$languages_id . "' order by COALESCE(po.products_options_sort_order,10000), po.products_options_id, COALESCE(pov2po.products_options_values_sort_order,10000), pov2po.products_options_values_id";

    if (!isset($value_page)) {
      $value_page = 1;
    }
    $prev_value_page = $value_page - 1;
    $next_value_page = $value_page + 1;

    $value_query = tep_db_query($values);

    $value_page_start = ($per_page * $value_page) - $per_page;
    $num_rows = tep_db_num_rows($value_query);

    if ($num_rows <= $per_page) {
      $num_pages = 1;
    } else if (($num_rows % $per_page) == 0) {
      $num_pages = ($num_rows / $per_page);
    } else {
      $num_pages = ($num_rows / $per_page) + 1;
    }
    $num_pages = (int) $num_pages;

    $values = $values . " LIMIT $value_page_start, $per_page";

    // Previous
    if ($prev_value_page)  {
      echo '<a href="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, "value_page=$prev_value_page&".tep_get_all_get_params(array('action','value_page','option_id','value_id','attribute_id'))) . '"> &lt;&lt; </a> | ';
    }

    for ($i = 1; $i <= $num_pages; $i++) {
      if ($i != $value_page) {
         echo '<a href="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, "value_page=$i&".tep_get_all_get_params(array('action','value_page','option_id','value_id','attribute_id'))) . '">' . $i . '</a> | ';
      } else {
         echo '<b><font color=red>' . $i . '</font></b> | ';
      }
    }

    // Next
    if ($value_page != $num_pages) {
      echo '<a href="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, "value_page=$next_value_page&".tep_get_all_get_params(array('action','value_page','option_id','value_id','attribute_id'))) . '"> &gt;&gt;</a> ';
    }
?>
                </td>
              </tr>
              <tr>
                <td colspan="5"><?php echo tep_black_line(); ?></td>
              </tr>
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent">&nbsp;<?php echo TABLE_HEADING_ID; ?>&nbsp;</td>
                <td class="dataTableHeadingContent">&nbsp;<?php echo TABLE_HEADING_OPT_NAME; ?>&nbsp;</td>
                <td class="dataTableHeadingContent">&nbsp;<?php echo TABLE_HEADING_OPT_VALUE; ?>&nbsp;</td>
<?php /* BOF: Attributes sort/copy */ ?>
                <td class="dataTableHeadingContent" align="center">&nbsp;<?php echo TABLE_HEADING_OPT_SORT_ORDER; ?>&nbsp;</td>
<?php /* EOF: Attributes sort/copy */ ?>
                <td class="dataTableHeadingContent" align="center">&nbsp;<?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
              <tr>
                <td colspan="5"><?php echo tep_black_line(); ?></td>
              </tr>
<?php
    $next_id = 1;
    $rows = 0;
    $values = tep_db_query($values);
    while ($values_values = tep_db_fetch_array($values)) {
      $options_name = tep_options_name($values_values['products_options_id']);
      $values_name = $values_values['products_options_values_name'];
      $sort_order = $values_values['products_options_values_sort_order'];
      $rows++;
?>
              <tr class="<?php echo (floor($rows/2) == ($rows/2) ? 'attributes-even' : 'attributes-odd'); ?>">
<?php
      if (($action == 'update_option_value') && ($HTTP_GET_VARS['value_id'] == $values_values['products_options_values_id'])) {
        echo '<form name="values" action="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=update_value&'.$page_info, 'NONSSL') . '" method="post">';
        $inputs = '';
        for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
          $value_name = tep_db_query("select products_options_values_name from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where products_options_values_id = '" . (int)$values_values['products_options_values_id'] . "' and language_id = '" . (int)$languages[$i]['id'] . "'");
          $value_name = tep_db_fetch_array($value_name);
          $inputs .= $languages[$i]['code'] . ':&nbsp;<input type="text" name="value_name[' . $languages[$i]['id'] . ']" size="15" value="' . $value_name['products_options_values_name'] . '">&nbsp;<br>';
        }
?>
                <td align="center" class="smallText">&nbsp;<?php echo $values_values['products_options_values_id']; ?><input type="hidden" name="value_id" value="<?php echo $values_values['products_options_values_id']; ?>">&nbsp;</td>
                <td align="center" class="smallText">&nbsp;<?php echo "\n"; ?><select name="option_id">
<?php
        $update = 'disabled';
        $options = tep_db_query("select products_options_id, products_options_name from " . TABLE_PRODUCTS_OPTIONS . " where (products_options_type = '0' or products_options_type = '2') and language_id = '" . (int)$languages_id . "' order by COALESCE(products_options_sort_order,10000), products_options_id");
        while ($options_values = tep_db_fetch_array($options)) {
          $update = '';
          echo "\n" . '<option name="' . $options_values['products_options_name'] . '" value="' . $options_values['products_options_id'] . '"';
          if ($values_values['products_options_id'] == $options_values['products_options_id']) {
            echo ' selected';
          }
          echo '>' . $options_values['products_options_name'] . '</option>';
        }
?>
                </select>&nbsp;</td>
                <td class="smallText" nowrap><?php echo $inputs; ?></td>
<?php /* BOF: Attributes sort/copy */ ?>
                <td align="center" class="smallText">&nbsp;<input type="text" name="values_sort_order" size="3" value="<?php echo $sort_order; ?>">&nbsp;</td>
<?php /* EOF: Attributes sort/copy */ ?>
                <td align="center" class="smallText">&nbsp;<?php echo tep_image_submit('button_update.gif', IMAGE_UPDATE, $update); ?>&nbsp;<?php echo '<a href="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info, 'NONSSL') . '">'; ?><?php echo tep_image_button('button_cancel.gif', IMAGE_CANCEL); ?></a>&nbsp;</td>
<?php
        echo '</form>';
      } else {
?>
                <td align="center" class="smallText">&nbsp;<?php echo $values_values["products_options_values_id"]; ?>&nbsp;</td>
                <td align="center" class="smallText">&nbsp;<?php echo $options_name; ?>&nbsp;</td>
                <td class="smallText">&nbsp;<?php echo $values_name; ?>&nbsp;</td>
<?php /* BOF: Attributes sort/copy */ ?>
                <td align="center" class="smallText" size="3">&nbsp;<?php echo $sort_order; ?>&nbsp;</td>
<?php /* EOF: Attributes sort/copy */ ?>
                <td align="center" class="smallText">&nbsp;<?php echo '<a href="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=update_option_value&value_id=' . $values_values['products_options_values_id'] . '&' . $page_info, 'NONSSL') . '">'; ?><?php echo tep_image_button('button_edit.gif', IMAGE_UPDATE); ?></a>&nbsp;&nbsp;<?php echo '<a href="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=delete_option_value&value_id=' . $values_values['products_options_values_id'] . '&' . $page_info, 'NONSSL') , '">'; ?><?php echo tep_image_button('button_delete.gif', IMAGE_DELETE); ?></a>&nbsp;</td>
<?php
      }
?>
              </tr>
<?php
    }
?>
              <tr>
                <td colspan="5"><?php echo tep_black_line(); ?></td>
              </tr>
<?php
    if ($action != 'update_option_value') {
      $max_values_id_query = tep_db_query("select max(products_options_values_id) + 1 as next_id from " . TABLE_PRODUCTS_OPTIONS_VALUES);
      $max_values_id_values = tep_db_fetch_array($max_values_id_query);
      if (is_numeric($max_values_id_values['next_id'])) $next_id = $max_values_id_values['next_id'];
?>
              <tr class="<?php echo (floor($rows/2) == ($rows/2) ? 'attributes-even' : 'attributes-odd'); ?>">
<?php
      echo '<form name="values" action="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=add_product_option_values&'.$page_info, 'NONSSL') . '" method="post">';
?>
                <td align="center" class="smallText">&nbsp;<?php echo $next_id; ?>&nbsp;</td>
                <td align="center" class="smallText">&nbsp;<select name="option_id">
<?php
      $insert = 'disabled';
      $options = tep_db_query("select products_options_id, products_options_name from " . TABLE_PRODUCTS_OPTIONS . " where (products_options_type = '0' or products_options_type = '2') and language_id = '" . $languages_id . "' order by COALESCE(products_options_sort_order,10000), products_options_id");
      while ($options_values = tep_db_fetch_array($options)) {
        $insert = '';
        echo '<option name="' . $options_values['products_options_name'] . '" value="' . $options_values['products_options_id'] . '"' . ($options_values['products_options_id']==(int)$value_filter?' SELECTED':'') . '>' . $options_values['products_options_name'] . '</option>';
      }

      $inputs = '';
      for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
        $inputs .= $languages[$i]['code'] . ':&nbsp;<input type="text" name="value_name[' . $languages[$i]['id'] . ']" size="15">&nbsp;<br>';
      }
?>
                </select>&nbsp;</td>
                <td class="smallText" nowrap><input type="hidden" name="value_id" value="<?php echo $next_id; ?>"><?php echo $inputs; ?></td>
<?php /* BOF: Attribute sort/copy */ ?>
                <td class="smallText"><input type="text" name="values_sort_order" size="3"></td> 
<?php /* EOF: Attribute sort/copy */ ?>
                <td align="center" class="smallText">&nbsp;<?php echo tep_image_submit('button_insert.gif', IMAGE_INSERT, $insert); ?>&nbsp;</td>
<?php
      echo '</form>';
?>
              </tr>
              <tr>
                <td colspan="5"><?php echo tep_black_line(); ?></td>
              </tr>
<?php
    }
  }
?>
            </table></td>
          </tr>
        </table></td>
<!-- option value eof //-->
      </tr>
<!-- products_attributes //-->
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading" width="100%">&nbsp;<?php echo HEADING_TITLE_ATRIB; ?>&nbsp;</td>
            <td align="right" class="main" nowrap><form name="attribute_filter_form" action="<?php echo tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES,  tep_get_all_get_params(array('action','value_page','attribute_page','option_id','value_id','attribute_id')), 'NONSSL'); ?>" method="post">
            <?php echo TEXT_ATTRIBUTE_FILTER; ?>&nbsp;<select name="attribute_filter" onChange="this.form.submit();"><option value="0"><?php echo TEXT_ATTRIBUTE_ALL; ?></option>
<?php
    $products_query = tep_db_query("select p.products_id, pd.products_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where pd.products_id = p.products_id and pd.language_id = '" . $languages_id . "' order by pd.products_name, pd.products_id");
    while ($products = tep_db_fetch_array($products_query)) {
      echo '<option value="'.$products['products_id'].'"'.($products['products_id']==(int)$attribute_filter?' SELECTED':'').'>'.$products['products_name'].'</option>';
    }
?>            </select></form></td>

            <td>&nbsp;<?php echo tep_image(DIR_WS_IMAGES . 'pixel_trans.gif', '', '1', '53'); ?>&nbsp;</td>
          </tr>
        </table></td>
      </tr>
      <tr>
<?php
  if ($action == 'update_attribute') {
    $form_action = 'update_product_attribute';
  } else {
    $form_action = 'add_product_attributes';
  }

  if (!isset($attribute_page)) {
    $attribute_page = 1;
  }
  $prev_attribute_page = $attribute_page - 1;
  $next_attribute_page = $attribute_page + 1;
?>
        <td><form name="attributes" action="<?php echo tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, "action=$form_action&".$page_info); ?>" method="post"><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td colspan="8" class="smallText">
<?php
  $per_page = MAX_ROW_LISTS_OPTIONS;
  $filter = '';
  $filter .= (int)$attribute_filter?" and pa.products_id = '" . (int)$attribute_filter . "'":'';
  $filter .= (int)$value_filter?" and pa.options_id = '" . (int)$value_filter . "'":'';
  $filter = $filter?' where'.substr($filter,4):'';

  $attributes = "select pa.* from " . TABLE_PRODUCTS_ATTRIBUTES . " pa left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on pa.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' left join " . TABLE_PRODUCTS_OPTIONS . " po on po.products_options_id = pa.options_id and po.language_id = '" . (int)$languages_id . "'$filter order by pd.products_name, pd.products_id, COALESCE(po.products_options_sort_order,10000), pa.options_id, COALESCE(pa.products_attributes_sort_order,10000), pa.products_attributes_id";
  $attribute_query = tep_db_query($attributes);

  $attribute_page_start = ($per_page * $attribute_page) - $per_page;
  $num_rows = tep_db_num_rows($attribute_query);

  if ($num_rows <= $per_page) {
     $num_pages = 1;
  } else if (($num_rows % $per_page) == 0) {
     $num_pages = ($num_rows / $per_page);
  } else {
     $num_pages = ($num_rows / $per_page) + 1;
  }
  $num_pages = (int) $num_pages;

  $attributes = $attributes . " LIMIT $attribute_page_start, $per_page";

  // Previous
  if ($prev_attribute_page) {
    echo '<a href="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, "attribute_page=$prev_attribute_page&".tep_get_all_get_params(array('action','attribute_page','option_id','value_id','attribute_id'))) . '"> &lt;&lt; </a> | ';
  }

  for ($i = 1; $i <= $num_pages; $i++) {
    if ($i != $attribute_page) {
      echo '<a href="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, "attribute_page=$i&".tep_get_all_get_params(array('action','attribute_page','option_id','value_id','attribute_id'))) . '">' . $i . '</a> | ';
    } else {
      echo '<b><font color="red">' . $i . '</font></b> | ';
    }
  }

  // Next
  if ($attribute_page != $num_pages) {
    echo '<a href="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, "attribute_page=$next_attribute_page&".tep_get_all_get_params(array('action','attribute_page','option_id','value_id','attribute_id'))) . '"> &gt;&gt; </a>';
  }
?>
            </td>
          </tr>
          <tr>
            <td colspan="8"><?php echo tep_black_line(); ?></td>
          </tr>
          <tr class="dataTableHeadingRow">
            <td class="dataTableHeadingContent">&nbsp;<?php echo TABLE_HEADING_ID; ?>&nbsp;</td>
            <td class="dataTableHeadingContent">&nbsp;<?php echo TABLE_HEADING_PRODUCT; ?>&nbsp;</td>
            <td class="dataTableHeadingContent">&nbsp;<?php echo TABLE_HEADING_OPT_NAME; ?>&nbsp;</td>
            <td class="dataTableHeadingContent">&nbsp;<?php echo TABLE_HEADING_OPT_VALUE; ?>&nbsp;</td>
            <td class="dataTableHeadingContent" align="right">&nbsp;<?php echo TABLE_HEADING_OPT_PRICE; ?>&nbsp;</td>
            <td class="dataTableHeadingContent" align="center">&nbsp;<?php echo TABLE_HEADING_OPT_PRICE_PREFIX; ?>&nbsp;</td>
<?php // BOF: WebMakers.com Added: Heading for new fields ?>
            <td class="dataTableHeadingContent" align="center">&nbsp;<?php echo TABLE_HEADING_OPT_SORT_ORDER; ?>&nbsp;</td>
<?php // EOF: WebMakers.com Added: Heading for new fields ?>
            <td class="dataTableHeadingContent" align="center">&nbsp;<?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
          </tr>
          <tr>
            <td colspan="8"><?php echo tep_black_line(); ?></td>
          </tr>
<?php
  $next_id = 1;
  $attributes = tep_db_query($attributes);
  while ($attributes_values = tep_db_fetch_array($attributes)) {
    $products_name_only = tep_get_products_name($attributes_values['products_id']);
    $options_name = tep_options_name($attributes_values['options_id']);
    $values_name = tep_values_name($attributes_values['options_values_id']);
    $rows++;
?>
          <tr class="<?php echo (floor($rows/2) == ($rows/2) ? 'attributes-even' : 'attributes-odd'); ?>">
<?php
    if (($action == 'update_attribute') && ($HTTP_GET_VARS['attribute_id'] == $attributes_values['products_attributes_id'] || $HTTP_POST_VARS['attribute_id'] == $attributes_values['products_attributes_id'])) {
?>
            <td class="smallText">&nbsp;<?php echo $attributes_values['products_attributes_id']; ?><input type="hidden" name="attribute_id" value="<?php echo $attributes_values['products_attributes_id']; ?>">&nbsp;</td>
            <td class="smallText">&nbsp;<select name="attributes_products_id">
<?php
      $products = tep_db_query("select p.products_id, pd.products_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where pd.products_id = p.products_id and pd.language_id = '" . $languages_id . "' order by pd.products_name, pd.products_id");
      $selected_product = $form_updated?(int)$HTTP_POST_VARS['attributes_products_id']:$attributes_values['products_id'];
      while ($products_values = tep_db_fetch_array($products)) {
        if ($selected_product == $products_values['products_id']) {
          echo "\n" . '<option name="' . $products_values['products_name'] . '" value="' . $products_values['products_id'] . '" SELECTED>' . $products_values['products_name'] . '</option>';
        } else {
          echo "\n" . '<option name="' . $products_values['products_name'] . '" value="' . $products_values['products_id'] . '">' . $products_values['products_name'] . '</option>';
        }
      }
?>
            </select>&nbsp;</td>
            <td class="smallText">&nbsp;<select name="attributes_options_id" onChange="this.form.submit();">
<?php
      $selected = '0';
      $options = tep_db_query("select distinct po.* from " . TABLE_PRODUCTS_OPTIONS . " po inner join " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " pov2po on po.products_options_id = pov2po.products_options_id where po.language_id = '" . $languages_id . "' order by COALESCE(po.products_options_sort_order,10000), po.products_options_id");
      $selected_option = $form_updated?(int)$HTTP_POST_VARS['attributes_options_id']:$attributes_values['options_id'];
      while ($options_values = tep_db_fetch_array($options)) {
        if (!$selected) $selected = $options_values['products_options_id'];
        if ($selected_option == $options_values['products_options_id']) {
          $selected = $options_values['products_options_id'];
          echo "\n" . '<option name="' . $options_values['products_options_name'] . '" value="' . $options_values['products_options_id'] . '" SELECTED>' . $options_values['products_options_name'] . '</option>';
        } else {
          echo "\n" . '<option name="' . $options_values['products_options_name'] . '" value="' . $options_values['products_options_id'] . '">' . $options_values['products_options_name'] . '</option>';
        }
      }
?>
            </select>&nbsp;</td>
            <td class="smallText">&nbsp;<select name="attributes_values_id">
<?php
      $update = 'disabled';
      $values = tep_db_query("select * from " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov left join " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " pov2po on pov.products_options_values_id = pov2po.products_options_values_id where pov.language_id = '" . $languages_id . "' and pov2po.products_options_id = '".(int)$selected."' order by COALESCE(pov2po.products_options_values_sort_order,10000), pov2po.products_options_values_id");
      $selected_value = $form_updated?(int)$HTTP_POST_VARS['attributes_values_id']:$attributes_values['options_values_id'];
      while ($values_values = tep_db_fetch_array($values)) {
        $update = '';
        if ($selected_value == $values_values['products_options_values_id']) {
          echo "\n" . '<option name="' . $values_values['products_options_values_name'] . '" value="' . $values_values['products_options_values_id'] . '" SELECTED>' . $values_values['products_options_values_name'] . '</option>';
        } else {
          echo "\n" . '<option name="' . $values_values['products_options_values_name'] . '" value="' . $values_values['products_options_values_id'] . '">' . $values_values['products_options_values_name'] . '</option>';
        }
      }
?>
            </select>&nbsp;</td>
            <td align="right" class="smallText">&nbsp;<input type="text" name="value_price" value="<?php echo ($form_updated?$HTTP_POST_VARS['value_price']:$attributes_values['options_values_price']); ?>" size="6">&nbsp;</td>
            <td align="center" class="smallText">&nbsp;<input type="text" name="price_prefix" value="<?php echo ($form_updated?$HTTP_POST_VARS['price_prefix']:$attributes_values['price_prefix']); ?>" size="2">&nbsp;</td>
<?php /* BOF: WebMakers.com Added: Attribute Sorter- Edit */ ?>
            <td align="center" class="smallText">&nbsp;<input type="text" name="attributes_sort_order" value="<?php echo ($form_updated?$HTTP_POST_VARS['attributes_sort_order']:$attributes_values['products_attributes_sort_order']); ?>" size="2">&nbsp;</td>
<?php /* EOF: WebMakers.com Added: Attribute Sorter- Edit */ ?>
            <td align="center" class="smallText">&nbsp;<?php echo tep_image_submit('button_update.gif', IMAGE_UPDATE, $update); ?>&nbsp;<?php echo '<a href="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info, 'NONSSL') . '">'; ?><?php echo tep_image_button('button_cancel.gif', IMAGE_CANCEL); ?></a>&nbsp;</td>
<?php
      if (DOWNLOAD_ENABLED == 'true') {
        $download_query_raw ="select products_attributes_filename, products_attributes_maxdays, products_attributes_maxcount
                              from " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . "
                              where products_attributes_id='" . $attributes_values['products_attributes_id'] . "'";
        $download_query = tep_db_query($download_query_raw);
        if (tep_db_num_rows($download_query) > 0) {
          $download = tep_db_fetch_array($download_query);
          $products_attributes_filename = $download['products_attributes_filename'];
          $products_attributes_maxdays  = $download['products_attributes_maxdays'];
          $products_attributes_maxcount = $download['products_attributes_maxcount'];
        }
?>
          <tr class="<?php echo (!($rows % 2)? 'attributes-even' : 'attributes-odd');?>">
            <td>&nbsp;</td>
            <td colspan="5">
              <table>
                <tr class="<?php echo (!($rows % 2)? 'attributes-even' : 'attributes-odd');?>">
                  <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_DOWNLOAD; ?>&nbsp;</td>
                  <td class="smallText"><?php echo TABLE_TEXT_FILENAME; ?></td>
                  <td class="smallText"><?php echo tep_draw_input_field('products_attributes_filename', ($form_updated?$HTTP_POST_VARS['products_attributes_filename']:$products_attributes_filename), 'size="15"', false, 'text', false); ?>&nbsp;</td>
                  <td class="smallText"><?php echo TABLE_TEXT_MAX_DAYS; ?></td>
                  <td class="smallText"><?php echo tep_draw_input_field('products_attributes_maxdays', ($form_updated?$HTTP_POST_VARS['products_attributes_maxdays']:$products_attributes_maxdays), 'size="5"', false, 'text', false); ?>&nbsp;</td>
                  <td class="smallText"><?php echo TABLE_TEXT_MAX_COUNT; ?></td>
                  <td class="smallText"><?php echo tep_draw_input_field('products_attributes_maxcount', ($form_updated?$HTTP_POST_VARS['products_attributes_maxcount']:$products_attributes_maxcount), 'size="5"', false, 'text', false); ?>&nbsp;</td>
                </tr>
              </table>
            </td>
            <td>&nbsp;</td>
          </tr>
<?php
      }
?>
<?php
    } elseif (($action == 'delete_product_attribute') && ($HTTP_GET_VARS['attribute_id'] == $attributes_values['products_attributes_id'])) {
?>
            <td class="smallText">&nbsp;<b><?php echo $attributes_values["products_attributes_id"]; ?></b>&nbsp;</td>
            <td class="smallText">&nbsp;<b><?php echo $products_name_only; ?></b>&nbsp;</td>
            <td class="smallText">&nbsp;<b><?php echo $options_name; ?></b>&nbsp;</td>
            <td class="smallText">&nbsp;<b><?php echo $values_name; ?></b>&nbsp;</td>
            <td align="right" class="smallText">&nbsp;<b><?php echo $attributes_values["options_values_price"]; ?></b>&nbsp;</td>
            <td align="center" class="smallText">&nbsp;<b><?php echo $attributes_values["price_prefix"]; ?></b>&nbsp;</td>
            <td align="center" class="smallText">&nbsp;<b><?php echo $attributes_values["products_attributes_sort_order"]; ?></b>&nbsp;</td>
            <td align="center" class="smallText">&nbsp;<b><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=delete_attribute&attribute_id='.$HTTP_GET_VARS['attribute_id'].'&'.$page_info) . '">'; ?><?php echo tep_image_button('button_confirm.gif', IMAGE_CONFIRM); ?></a>&nbsp;&nbsp;<?php echo '<a href="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info, 'NONSSL') . '">'; ?><?php echo tep_image_button('button_cancel.gif', IMAGE_CANCEL); ?></a>&nbsp;</b></td>
<?php
    } else {
?>
            <td class="smallText">&nbsp;<?php echo $attributes_values["products_attributes_id"]; ?>&nbsp;</td>
            <td class="smallText">&nbsp;<?php echo $products_name_only; ?>&nbsp;</td>
            <td class="smallText">&nbsp;<?php echo $options_name; ?>&nbsp;</td>
            <td class="smallText">&nbsp;<?php echo $values_name; ?>&nbsp;</td>
            <td align="right" class="smallText">&nbsp;<?php echo $attributes_values["options_values_price"]; ?>&nbsp;</td>
            <td align="center" class="smallText">&nbsp;<?php echo $attributes_values["price_prefix"]; ?>&nbsp;</td>
            <td align="center" class="smallText">&nbsp;<?php echo $attributes_values["products_attributes_sort_order"]; ?></td>
            <td align="center" class="smallText">&nbsp;<?php echo '<a href="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=update_attribute&attribute_id=' . $attributes_values['products_attributes_id'] . '&' . $page_info, 'NONSSL') . '">'; ?><?php echo tep_image_button('button_edit.gif', IMAGE_UPDATE); ?></a>&nbsp;&nbsp;<?php echo '<a href="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=delete_product_attribute&attribute_id=' . $attributes_values['products_attributes_id'] . '&' . $page_info, 'NONSSL') , '">'; ?><?php echo tep_image_button('button_delete.gif', IMAGE_DELETE); ?></a>&nbsp;</td>
<?php
    }
?>
          </tr>
<?php
  }
  if ($action != 'update_attribute') {
    $max_attributes_id_query = tep_db_query("select max(products_attributes_id) + 1 as next_id from " . TABLE_PRODUCTS_ATTRIBUTES);
    $max_attributes_id_values = tep_db_fetch_array($max_attributes_id_query);
    if (is_numeric($max_attributes_id_values['next_id'])) $next_id = $max_attributes_id_values['next_id'];
?>
          <tr>
            <td colspan="8"><?php echo tep_black_line(); ?></td>
          </tr>
          <tr class="<?php echo (floor($rows/2) == ($rows/2) ? 'attributes-even' : 'attributes-odd'); ?>">
            <td class="smallText">&nbsp;<?php echo $next_id; ?>&nbsp;</td>
      	    <td class="smallText">&nbsp;<select name="attributes_products_id">
<?php
    $products = tep_db_query("select p.products_id, pd.products_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where pd.products_id = p.products_id and pd.language_id = '" . $languages_id . "' order by pd.products_name, pd.products_id");
    $selected_product = $form_updated?$HTTP_POST_VARS['attributes_products_id']:$attribute_filter;
    while ($products_values = tep_db_fetch_array($products)) {
      echo '<option name="' . $products_values['products_name'] . '" value="' . $products_values['products_id'] . '"'.($products_values['products_id']==(int)$selected_product?' SELECTED':'').'>' . $products_values['products_name'] . '</option>';
    }
?>
            </select>&nbsp;</td>
            <td class="smallText">&nbsp;<select name="attributes_options_id" onChange="this.form.submit();">
<?php
    $selected = '0';
    $options = tep_db_query("select distinct po.* from " . TABLE_PRODUCTS_OPTIONS . " po inner join " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " pov2po on po.products_options_id = pov2po.products_options_id where po.language_id = '" . $languages_id . "' order by COALESCE(po.products_options_sort_order,10000), po.products_options_id");
    $selected_option = $form_updated?$HTTP_POST_VARS['attributes_options_id']:$value_filter;
    while ($options_values = tep_db_fetch_array($options)) {
      if (!$selected || $options_values['products_options_id']==(int)$selected_option) $selected = $options_values['products_options_id'];
      echo '<option name="' . $options_values['products_options_name'] . '" value="' . $options_values['products_options_id'] . '"'.($options_values['products_options_id']==(int)$selected_option?' SELECTED':'').'>' . $options_values['products_options_name'] . '</option>';
    }
?>
            </select>&nbsp;</td>
            <td class="smallText">&nbsp;<select name="attributes_values_id">
<?php
    $insert = 'disabled';
    $values = tep_db_query("select * from " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov left join " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " pov2po on pov.products_options_values_id = pov2po.products_options_values_id where pov.language_id = '" . $languages_id . "' and pov2po.products_options_id = '".(int)$selected."' order by COALESCE(pov2po.products_options_values_sort_order,10000), pov2po.products_options_values_id");
    while ($values_values = tep_db_fetch_array($values)) {
      $insert = '';
      echo '<option name="' . $values_values['products_options_values_name'] . '" value="' . $values_values['products_options_values_id'] . '"'.(($form_updated && $values_values['products_options_values_id']==(int)$HTTP_POST_VARS['attributes_values_id'])?' SELECTED':'').'>' . $values_values['products_options_values_name'] . '</option>';
    }
?>
            </select>&nbsp;</td>
            <td align="right" class="smallText">&nbsp;<input type="text" name="value_price" size="6" value="<?php echo ($form_updated?$HTTP_POST_VARS['value_price']:''); ?>">&nbsp;</td>
            <td align="right" class="smallText">&nbsp;<input type="text" name="price_prefix" size="2" value="<?php echo ($form_updated?$HTTP_POST_VARS['price_prefix']:'+'); ?>">&nbsp;</td>
            <td align="right" class="smallText">&nbsp;<input type="text" name="attributes_sort_order" size="6" value="<?php echo ($form_updated?$HTTP_POST_VARS['attributes_sort_order']:''); ?>">&nbsp;</td>
            <td align="center" class="smallText">&nbsp;<?php echo tep_image_submit('button_insert.gif', IMAGE_INSERT, $insert); ?>&nbsp;</td>
          </tr>
<?php
      if (DOWNLOAD_ENABLED == 'true') {
        $products_attributes_maxdays  = DOWNLOAD_MAX_DAYS;
        $products_attributes_maxcount = DOWNLOAD_MAX_COUNT;
?>
          <tr class="<?php echo (!($rows % 2)? 'attributes-even' : 'attributes-odd');?>">
            <td>&nbsp;</td>
            <td colspan="5">
              <table>
                <tr class="<?php echo (!($rows % 2)? 'attributes-even' : 'attributes-odd');?>">
                  <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_DOWNLOAD; ?>&nbsp;</td>
                  <td class="smallText"><?php echo TABLE_TEXT_FILENAME; ?></td>
                  <td class="smallText"><?php echo tep_draw_input_field('products_attributes_filename', ($form_updated?$HTTP_POST_VARS['products_attributes_filename']:$products_attributes_filename), 'size="15"', false, 'text', false); ?>&nbsp;</td>
                  <td class="smallText"><?php echo TABLE_TEXT_MAX_DAYS; ?></td>
                  <td class="smallText"><?php echo tep_draw_input_field('products_attributes_maxdays', ($form_updated?$HTTP_POST_VARS['products_attributes_maxdays']:$products_attributes_maxdays), 'size="5"', false, 'text', false); ?>&nbsp;</td>
                  <td class="smallText"><?php echo TABLE_TEXT_MAX_COUNT; ?></td>
                  <td class="smallText"><?php echo tep_draw_input_field('products_attributes_maxcount', ($form_updated?$HTTP_POST_VARS['products_attributes_maxcount']:$products_attributes_maxcount), 'size="5"', false, 'text', false); ?>&nbsp;</td>
                </tr>
              </table>
            </td>
            <td>&nbsp;</td>
          </tr>
<?php
      } // end of DOWNLOAD_ENABLED section
?>
<?php
  }
?>
          <tr>
            <td colspan="8"><?php echo tep_black_line(); ?></td>
          </tr>
        </table></form></td>
      </tr>
    </table></td>
<!-- products_attributes_eof //-->
  </tr>
</table>
<!-- body_text_eof //-->
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
