<?php
/*
  $Id: manufacturers.php,v 1.55 2003/06/29 22:50:52 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License

  modified by splautz for sort & status
*/

  require('includes/application_top.php');

// added by splautz for WYSIWYG compatibility
  $brInfo = tep_get_browser();
  $wysiwyg = ($brInfo['browser'] == 'MSIE' ? $brInfo['version'] : 0) >= 5.5 ? true : false;

  $action = (isset($HTTP_GET_VARS['action']) ? $HTTP_GET_VARS['action'] : '');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'setflag':
        if ( ($HTTP_GET_VARS['flag'] == '0') || ($HTTP_GET_VARS['flag'] == '1') ) {
          if (isset($HTTP_GET_VARS['mID'])) {
            tep_db_query("update " . TABLE_MANUFACTURERS . " set manufacturers_status='".$HTTP_GET_VARS['flag']."' where manufacturers_id='".$HTTP_GET_VARS['mID']."'");
          }
          if (USE_CACHE == 'true') {
            tep_reset_cache_block('manufacturers');
          }
        }

        tep_redirect(tep_href_link(FILENAME_MANUFACTURERS, (isset($HTTP_GET_VARS['page']) ? 'page=' . $HTTP_GET_VARS['page'] . '&' : '') . 'mID=' . $HTTP_GET_VARS['mID']));
        break;
      case 'insert':
      case 'save':
        if (isset($HTTP_GET_VARS['mID'])) $manufacturers_id = tep_db_prepare_input($HTTP_GET_VARS['mID']);
        $manufacturers_name = tep_db_prepare_input($HTTP_POST_VARS['manufacturers_name']);
        $sort_order = tep_db_prepare_input($HTTP_POST_VARS['sort_order']);
        if (!is_numeric($sort_order)) $sort_order = 'null';
        $manufacturers_status = tep_db_prepare_input($HTTP_POST_VARS['manufacturers_status']);

        $sql_data_array = array('manufacturers_name' => $manufacturers_name,
                                'sort_order' => $sort_order,
                                'manufacturers_status' => $manufacturers_status);

// modified by splautz to ensure proper image management
        if (!$wysiwyg || HTML_AREA_WYSIWYG_DISABLE_IMAGE == 'Disable') {
          if ($manufacturers_image = new upload('manufacturers_image', DIR_FS_CATALOG_IMAGES)) {
            $sql_data_array['manufacturers_image'] = $manufacturers_image->filename;
            if ($action == 'save') tep_remove_image($manufacturers_id,'m',$manufacturers_image->filename);
          }
        } else {
          if (isset($HTTP_POST_VARS['manufacturers_image']) && tep_not_null($HTTP_POST_VARS['manufacturers_image']) && ($HTTP_POST_VARS['manufacturers_image'] != 'none')) {
            $manufacturers_image = tep_db_prepare_input($HTTP_POST_VARS['manufacturers_image']);
          } else $manufacturers_image = '';
          $sql_data_array['manufacturers_image'] = $manufacturers_image;
          if ($action == 'save') tep_remove_image($manufacturers_id,'m',$manufacturers_image);
        }

        if ($action == 'insert') {
          $insert_sql_data = array('date_added' => 'now()');

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          tep_db_perform(TABLE_MANUFACTURERS, $sql_data_array);
          $manufacturers_id = tep_db_insert_id();
        } elseif ($action == 'save') {
          $update_sql_data = array('last_modified' => 'now()');

          $sql_data_array = array_merge($sql_data_array, $update_sql_data);

          tep_db_perform(TABLE_MANUFACTURERS, $sql_data_array, 'update', "manufacturers_id = '" . (int)$manufacturers_id . "'");
        }

// modified by splautz to ensure proper image management
//      if ($manufacturers_image = new upload('manufacturers_image', DIR_FS_CATALOG_IMAGES)) {
//        tep_db_query("update " . TABLE_MANUFACTURERS . " set manufacturers_image = '" . $manufacturers_image->filename . "' where manufacturers_id = '" . (int)$manufacturers_id . "'");
//      }

        $languages = tep_get_languages();
        for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
//        $manufacturers_url_array = $HTTP_POST_VARS['manufacturers_url'];  // removed by splautz - defined below
          $language_id = $languages[$i]['id'];

//        $sql_data_array = array('manufacturers_url' => tep_db_prepare_input($manufacturers_url_array[$language_id]));  // removed by splautz - defined below

          // added by splautz to ensure TABLE_SEO_SURLS is updated
          if (isset($HTTP_POST_VARS['manufacturers_surls_name'][$language_id])) $surls_name=trim($HTTP_POST_VARS['manufacturers_surls_name'][$language_id]);
          if (isset($HTTP_POST_VARS['manufacturers_surls_id'][$language_id]) && is_numeric($surls_id=$HTTP_POST_VARS['manufacturers_surls_id'][$language_id])) {
            if ($surls_name) {
              if (tep_check_dup_surl(true, $surls_id, tep_db_prepare_input($surls_name))) $surls_name = tep_get_manufacturer_surls_name($manufacturers_id, $language_id);
              else {
                $sql_data_array = array('surls_name' => tep_db_prepare_input($surls_name));
                tep_db_perform(TABLE_SEO_URLS, $sql_data_array, 'update', "surls_id = '" . (int)$surls_id . "'");
                $surls_updated = true;
              }
			} else {
              tep_remove_surl($surls_id);
              $surls_id = NULL;
              $surls_updated = true;
            }
		  } elseif ($surls_name) {
            if (tep_check_dup_surl(true, '', tep_db_prepare_input($surls_name), 'index.php', 'manufacturers_id=' . (int)$manufacturers_id, $language_id)) {  // dup exists
              $surls_name = '';
              $surls_id = NULL;
            } else {  // dup not found, ok to insert
              $sql_data_array = array('surls_name' => tep_db_prepare_input($surls_name),
                'surls_script' => 'index.php',
                'surls_param' => 'manufacturers_id=' . (int)$manufacturers_id,
                'language_id' => $language_id);
              tep_db_perform(TABLE_SEO_URLS, $sql_data_array);
              $surls_id = tep_db_insert_id();
              $surls_updated = true;
            }
		  } else $surls_id = NULL;

         //HTC BOC
          $sql_data_array = array('manufacturers_url' => tep_db_prepare_input($HTTP_POST_VARS['manufacturers_url'][$language_id]),
           'manufacturers_htc_title_tag' => tep_db_prepare_input($HTTP_POST_VARS['manufacturers_htc_title_tag'][$language_id]),
           'manufacturers_htc_desc_tag' => tep_db_prepare_input($HTTP_POST_VARS['manufacturers_htc_desc_tag'][$language_id]),
           'manufacturers_htc_keywords_tag' => tep_db_prepare_input($HTTP_POST_VARS['manufacturers_htc_keywords_tag'][$language_id]),
           'manufacturers_h1' => tep_db_prepare_input($HTTP_POST_VARS['manufacturers_h1'][$language_id]),
           'manufacturers_surls_id' => ($surls_id===NULL)?'null':tep_db_prepare_input($surls_id),
           'manufacturers_htc_description' => tep_db_prepare_input($HTTP_POST_VARS['manufacturers_htc_description'][$language_id]),
           'manufacturers_body' => tep_db_prepare_input($HTTP_POST_VARS['manufacturers_body'][$language_id]),
           'manufacturers_body2' => tep_db_prepare_input($HTTP_POST_VARS['manufacturers_body2'][$language_id]),
           'manufacturers_img_alt' => tep_db_prepare_input($HTTP_POST_VARS['manufacturers_img_alt'][$language_id]));
          //HTC EOC 

          if ($action == 'insert') {
            $insert_sql_data = array('manufacturers_id' => $manufacturers_id,
                                     'languages_id' => $language_id);

            $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

            tep_db_perform(TABLE_MANUFACTURERS_INFO, $sql_data_array);
          } elseif ($action == 'save') {
            tep_db_perform(TABLE_MANUFACTURERS_INFO, $sql_data_array, 'update', "manufacturers_id = '" . (int)$manufacturers_id . "' and languages_id = '" . (int)$language_id . "'");
          }
        }

        if (USE_CACHE == 'true') {
          tep_reset_cache_block('manufacturers');
        }

        tep_redirect(tep_href_link(FILENAME_MANUFACTURERS, (isset($HTTP_GET_VARS['page']) ? 'page=' . $HTTP_GET_VARS['page'] . '&' : '') . 'mID=' . $manufacturers_id));
        break;
      case 'deleteconfirm':
        $manufacturers_id = tep_db_prepare_input($HTTP_GET_VARS['mID']);

        // added by splautz to delete surl entries
        $manufacturers_query = tep_db_query("select mi.manufacturers_surls_id from " . TABLE_MANUFACTURERS_INFO . " mi where mi.manufacturers_id = '" . (int)$manufacturers_id . "' and mi.manufacturers_surls_id > 0");
        while($manufacturers = tep_db_fetch_array($manufacturers_query)) {
          tep_remove_surl($manufacturers['manufacturers_surls_id']);
          $surls_updated = true;
        }

        if (isset($HTTP_POST_VARS['delete_image']) && ($HTTP_POST_VARS['delete_image'] == 'on')) {
// modified by splautz for proper image management
//        $manufacturer_query = tep_db_query("select manufacturers_image from " . TABLE_MANUFACTURERS . " where manufacturers_id = '" . (int)$manufacturers_id . "'");
//        $manufacturer = tep_db_fetch_array($manufacturer_query);
//
//        $image_location = DIR_FS_DOCUMENT_ROOT . DIR_WS_CATALOG_IMAGES . $manufacturer['manufacturers_image'];
//
//        if (file_exists($image_location)) @unlink($image_location);
          tep_remove_all_images($manufacturers_id,'m');
        }

        tep_db_query("delete from " . TABLE_MANUFACTURERS . " where manufacturers_id = '" . (int)$manufacturers_id . "'");
        tep_db_query("delete from " . TABLE_MANUFACTURERS_INFO . " where manufacturers_id = '" . (int)$manufacturers_id . "'");

        if (isset($HTTP_POST_VARS['delete_products']) && ($HTTP_POST_VARS['delete_products'] == 'on')) {
          $products_query = tep_db_query("select products_id from " . TABLE_PRODUCTS . " where manufacturers_id = '" . (int)$manufacturers_id . "'");
          while ($products = tep_db_fetch_array($products_query)) {
            tep_remove_product($products['products_id']);
          }
        } else {
          tep_db_query("update " . TABLE_PRODUCTS . " set manufacturers_id = '' where manufacturers_id = '" . (int)$manufacturers_id . "'");
        }

        if (USE_CACHE == 'true') {
          tep_reset_cache_block('manufacturers');
        }

        tep_redirect(tep_href_link(FILENAME_MANUFACTURERS, 'page=' . $HTTP_GET_VARS['page']));
        break;
    }
  }
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<?php if ($wysiwyg) { ?>
        <script language="Javascript1.2"><!-- // load htmlarea
// MaxiDVD Added WYSIWYG HTML Area Box + Admin Function v1.7 - 2.2 MS2 Products Description HTML - Head
        _editor_url = "<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_ADMIN; ?>htmlarea/";  // URL to htmlarea files
        _script_name = "<?php echo ((HTML_AREA_WYSIWYG_BASIC_PD == 'Basic') ? 'editor_basic.js' : 'editor_advanced.js'); ?>";  // script name of editor to use
         document.write('<scr' + 'ipt src="' +_editor_url+_script_name+ '"');
         document.write(' language="Javascript1.2"></scr' + 'ipt>');
// --></script>
<?php } ?>
<script language="javascript" src="includes/general.js"></script>
<script language="javascript" src="includes/javascript/spellcheck.js"></script>
<script language="javascript"><!--
function popupWindow(url,x,y) {
  window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width='+x+',height='+y+',screenX=150,screenY=150,top=150,left=150')
}
//--></script>
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
    <td width="100%" valign="top">
<?php
// begin new/edit manufacturer
  if ($action == 'new' || $action == 'edit') {
    $languages = tep_get_languages();
    if ($action == 'edit' && isset($HTTP_GET_VARS['mID'])) {
      $manufacturers_query = tep_db_query("select m.manufacturers_id, m.manufacturers_name, m.manufacturers_image, m.date_added, m.last_modified, m.sort_order, m.manufacturers_status from " . TABLE_MANUFACTURERS . " m where m.manufacturers_id = '" . (int)$HTTP_GET_VARS['mID'] . "'");
      $manufacturers = tep_db_fetch_array($manufacturers_query);
      $mInfo = new objectInfo($manufacturers);
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        $manufacturer_query = tep_db_query("select mi.manufacturers_url, mi.manufacturers_htc_title_tag, mi.manufacturers_htc_desc_tag, mi.manufacturers_htc_keywords_tag, mi.manufacturers_h1, mi.manufacturers_surls_id, mi.manufacturers_img_alt, mi.manufacturers_htc_description, mi.manufacturers_body, mi.manufacturers_body2 from " . TABLE_MANUFACTURERS_INFO . " mi where mi.manufacturers_id = '" . (int)$mInfo->manufacturers_id . "' and mi.languages_id = '" . (int)$languages[$i]['id'] . "'");
        $manufacturer = tep_db_fetch_array($manufacturer_query);
        $manufacturers_url[$languages[$i]['id']] = $manufacturer['manufacturers_url'];
        $manufacturers_htc_title_tag[$languages[$i]['id']] = $manufacturer['manufacturers_htc_title_tag'];
        $manufacturers_htc_desc_tag[$languages[$i]['id']] = $manufacturer['manufacturers_htc_desc_tag'];
        $manufacturers_htc_keywords_tag[$languages[$i]['id']] = $manufacturer['manufacturers_htc_keywords_tag'];
        $manufacturers_h1[$languages[$i]['id']] = $manufacturer['manufacturers_h1'];
        $manufacturers_surls_id[$languages[$i]['id']] = $manufacturer['manufacturers_surls_id'];
        $manufacturers_surls_name[$languages[$i]['id']] = tep_get_manufacturer_surls_name($mInfo->manufacturers_id, $languages[$i]['id']);
        $manufacturers_img_alt[$languages[$i]['id']] = $manufacturer['manufacturers_img_alt'];
        $manufacturers_htc_description[$languages[$i]['id']] = $manufacturer['manufacturers_htc_description'];
        $manufacturers_body[$languages[$i]['id']] = $manufacturer['manufacturers_body'];
        $manufacturers_body2[$languages[$i]['id']] = $manufacturer['manufacturers_body2'];
      }
    } else $mInfo = NULL;
    if ($mInfo) echo tep_draw_form($formname='manufacturers', FILENAME_MANUFACTURERS, 'action=save&' . (isset($HTTP_GET_VARS['page']) ? 'page=' . $HTTP_GET_VARS['page'] : ''), 'post', 'enctype="multipart/form-data"') . tep_draw_hidden_field('manufacturers_id', $mInfo->manufacturers_id);
    else echo tep_draw_form($formname='newmanufacturer', FILENAME_MANUFACTURERS, 'action=insert&' . (isset($HTTP_GET_VARS['page']) ? 'page=' . $HTTP_GET_VARS['page'] : ''), 'post', 'enctype="multipart/form-data"');
?>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php if ($mInfo) echo TEXT_HEADING_EDIT_MANUFACTURER; else echo TEXT_HEADING_NEW_MANUFACTURER; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td><?php if ($mInfo) echo TEXT_EDIT_INTRO; else echo TEXT_NEW_INTRO; ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main"><?php tep_echo_help('TEXT_MANUFACTURERS_NAME'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('manufacturers_name', $value=(isset($mInfo->manufacturers_name) ? $mInfo->manufacturers_name : ''), 'onKeyUp="CountInput(this);"');
            echo "<br>".spellcount_link($formname,'manufacturers_name',strlen($value)); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php tep_echo_help('TEXT_MANUFACTURERS_SORT_ORDER'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('sort_order', (isset($mInfo->sort_order) ? $mInfo->sort_order : ''), 'size="3" maxlength="3"'); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php tep_echo_help('TEXT_MANUFACTURERS_STATUS'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('manufacturers_status', (isset($mInfo->manufacturers_status) ? $mInfo->manufacturers_status : '1'), 'size="2"') . '&nbsp;1=Enabled 0=Disabled'; ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php tep_echo_help('TEXT_MANUFACTURERS_IMAGE'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
		        <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15'); ?>&nbsp;</td>
                <td class="main">
                <?php if (!$wysiwyg || HTML_AREA_WYSIWYG_DISABLE_IMAGE == 'Disable') {
                  echo tep_draw_file_field('manufacturers_image');
                  if (isset($mInfo->manufacturers_image)) echo '<br>' . $mInfo->manufacturers_image;
                } else echo tep_draw_textarea_field('manufacturers_image', 'soft', '30', '1', $mInfo->manufacturers_image); ?>
                </td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
         <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('TEXT_MANUFACTURERS_IMG_ALT'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_textarea_field('manufacturers_img_alt[' . $languages[$i]['id'] . ']', 'soft', '70', '2', $value=(isset($manufacturers_img_alt[$languages[$i]['id']]) ? $manufacturers_img_alt[$languages[$i]['id']] : ''), 'onKeyUp="CountInput(this);"');
                echo "<br>".spellcount_link($formname,'manufacturers_img_alt[' . $languages[$i]['id'] . ']',strlen($value)); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
    }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
         <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('TEXT_MANUFACTURERS_URL'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_input_field('manufacturers_url[' . $languages[$i]['id'] . ']', (isset($manufacturers_url[$languages[$i]['id']]) ? $manufacturers_url[$languages[$i]['id']] : '')); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
    }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
         <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('TEXT_MANUFACTURERS_INTRO'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_textarea_field('manufacturers_htc_description[' . $languages[$i]['id'] . ']', 'soft', '70', '15', (isset($manufacturers_htc_description[$languages[$i]['id']]) ? $manufacturers_htc_description[$languages[$i]['id']] : ''));
                echo "<br>".spellcount_link($formname,'manufacturers_htc_description[' . $languages[$i]['id'] . ']'); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
    }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
         <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('TEXT_MANUFACTURERS_BODY'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_textarea_field('manufacturers_body[' . $languages[$i]['id'] . ']', 'soft', '70', '15', (isset($manufacturers_body[$languages[$i]['id']]) ? $manufacturers_body[$languages[$i]['id']] : ''));
                echo "<br>".spellcount_link($formname,'manufacturers_body[' . $languages[$i]['id'] . ']'); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
    }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
         <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('TEXT_MANUFACTURERS_BODY2'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_textarea_field('manufacturers_body2[' . $languages[$i]['id'] . ']', 'soft', '70', '15', (isset($manufacturers_body2[$languages[$i]['id']]) ? $manufacturers_body2[$languages[$i]['id']] : ''));
                echo "<br>".spellcount_link($formname,'manufacturers_body2[' . $languages[$i]['id'] . ']'); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
    }
?>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td colspan="2" class="main"><hr><?php echo TEXT_MANUFACTURERS_METTA_INFO; ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr> 
<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('TEXT_MANUFACTURERS_NAME_URL'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
				<td class="main"><?php echo tep_draw_input_field('manufacturers_surls_name[' . $languages[$i]['id'] . ']', $value=(isset($manufacturers_surls_name[$languages[$i]['id']]) ? $manufacturers_surls_name[$languages[$i]['id']] : ''), 'size="70" onKeyUp="CountInput(this);"');
                echo "<br>".spellcount_link($formname,'manufacturers_surls_name[' . $languages[$i]['id'] . ']',strlen($value));
				echo tep_draw_hidden_field('manufacturers_surls_id[' . $languages[$i]['id'] . ']', htmlspecialchars($manufacturers_surls_id[$languages[$i]['id']])); ?></td>
              </tr>
            </table></td>
          </tr>
<?php          
    }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('TEXT_MANUFACTURERS_HEAD_TITLE'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_input_field('manufacturers_htc_title_tag[' . $languages[$i]['id'] . ']', $value=(isset($manufacturers_htc_title_tag[$languages[$i]['id']]) ? $manufacturers_htc_title_tag[$languages[$i]['id']] : ''), 'size="70" onKeyUp="CountInput(this);"');
                echo "<br>".spellcount_link($formname,'manufacturers_htc_title_tag[' . $languages[$i]['id'] . ']',strlen($value)); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
    }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('TEXT_MANUFACTURERS_HEAD_DESCRIPTION'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_textarea_field('manufacturers_htc_desc_tag[' . $languages[$i]['id'] . ']', 'soft', '70', '5', $value=(isset($manufacturers_htc_desc_tag[$languages[$i]['id']]) ? $manufacturers_htc_desc_tag[$languages[$i]['id']] : ''), 'onKeyUp="CountInput(this);"');
                echo "<br>".spellcount_link($formname,'manufacturers_htc_desc_tag[' . $languages[$i]['id'] . ']',strlen($value)); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
    }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('TEXT_MANUFACTURERS_HEAD_KEYWORDS'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_textarea_field('manufacturers_htc_keywords_tag[' . $languages[$i]['id'] . ']', 'soft', '70', '2', $value=(isset($manufacturers_htc_keywords_tag[$languages[$i]['id']]) ? $manufacturers_htc_keywords_tag[$languages[$i]['id']] : ''), 'onKeyUp="CountInput(this);"');
                echo "<br>".spellcount_link($formname,'manufacturers_htc_keywords_tag[' . $languages[$i]['id'] . ']',strlen($value)); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
    }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('TEXT_MANUFACTURERS_H1'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_textarea_field('manufacturers_h1[' . $languages[$i]['id'] . ']', 'soft', '70', '2', $value=(isset($manufacturers_h1[$languages[$i]['id']]) ? $manufacturers_h1[$languages[$i]['id']] : ''), 'onKeyUp="CountInput(this);"');
                echo "<br>".spellcount_link($formname,'manufacturers_h1[' . $languages[$i]['id'] . ']',strlen($value)); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
    }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td colspan="2" class="main"><hr></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td class="main" align="center">
<?php
    if ($mInfo) echo tep_image_submit('button_save.gif', IMAGE_SAVE) . ' <a href="' . tep_href_link(FILENAME_MANUFACTURERS, (isset($HTTP_GET_VARS['page']) ? 'page=' . $HTTP_GET_VARS['page'] . '&' : '') . 'mID=' . $mInfo->manufacturers_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>';
    else echo tep_image_submit('button_save.gif', IMAGE_SAVE) . ' <a href="' . tep_href_link(FILENAME_MANUFACTURERS, (isset($HTTP_GET_VARS['page']) ? 'page=' . $HTTP_GET_VARS['page'] : '')) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>'; ?>
        </td>
      </tr>
    </table></form>
<?php
//MaxiDVD Added WYSIWYG HTML Area Box + Admin Function v1.7 - 2.2 MS2 Products Description HTML - </form>
   if ($wysiwyg && (HTML_AREA_WYSIWYG_DISABLE != 'Disable' || HTML_AREA_WYSIWYG_DISABLE_IMAGE != 'Disable')) { ?>
            <script language="JavaScript1.2" defer>
             var config = new Object();  // create new config object
             config.debug = <?php echo HTML_AREA_WYSIWYG_DEBUG; ?>;
    <?php if (HTML_AREA_WYSIWYG_DISABLE != 'Disable') { ?>
             config.width = "<?php echo HTML_AREA_WYSIWYG_WIDTH; ?>px";
             config.height = "<?php echo HTML_AREA_WYSIWYG_HEIGHT; ?>px";
             config.bodyStyle = 'background-color: <?php echo HTML_AREA_WYSIWYG_BG_COLOUR; ?>; font-family: "<?php echo HTML_AREA_WYSIWYG_FONT_TYPE; ?>"; color: <?php echo HTML_AREA_WYSIWYG_FONT_COLOUR; ?>; font-size: <?php echo HTML_AREA_WYSIWYG_FONT_SIZE; ?>pt;';
             config.stylesheet = '<?php echo HTTP_CATALOG_SERVER . DIR_WS_CATALOG . 'stylesheet.css'; ?>';
          <?php for ($i = 0, $n = sizeof($languages); $i < $n; $i++) { ?>
             editor_generate('manufacturers_htc_description[<?php echo $languages[$i]['id']; ?>]',config);
             editor_generate('manufacturers_body[<?php echo $languages[$i]['id']; ?>]',config);
             editor_generate('manufacturers_body2[<?php echo $languages[$i]['id']; ?>]',config);
          <?php }
          }
          if (HTML_AREA_WYSIWYG_DISABLE_IMAGE != 'Disable') { ?>
             config.width  = "<?php echo IMAGE_EDITOR_WYSIWYG_WIDTH; ?>px";
             config.height = "<?php echo IMAGE_EDITOR_WYSIWYG_HEIGHT; ?>px";
             config.bodyStyle = 'background-color: white; font-family: Arial; color: black; font-size: 12px;';
             config.stylesheet = null;
             config.toolbar = [ ["InsertImageURL"] ];
             config.OscImageRoot = '<?php echo trim(HTTP_SERVER . DIR_WS_CATALOG_IMAGES); ?>';
             editor_generate('manufacturers_image',config);
    <?php } ?>
            </script>
<?php }
// end new/edit manufacturer
  } else {
?>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_ID; ?></td>
                <td class="dataTableHeadingContent" width="100%" align="left"><?php echo TABLE_HEADING_MANUFACTURERS; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_SORT; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_STATUS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
//BOC HTC
//  $manufacturers_query_raw = "select manufacturers_id, manufacturers_name, manufacturers_image, date_added, last_modified from " . TABLE_MANUFACTURERS . " order by manufacturers_name";
  $manufacturers_query_raw = "select m.manufacturers_id, m.manufacturers_name, m.sort_order, m.manufacturers_status, m.manufacturers_image, m.date_added, m.last_modified, mi.manufacturers_htc_title_tag, mi.manufacturers_img_alt from " . TABLE_MANUFACTURERS . " m LEFT JOIN " . TABLE_MANUFACTURERS_INFO . " mi on m.manufacturers_id = mi.manufacturers_id where mi.languages_id = '" . (int)$languages_id . "' order by COALESCE(m.sort_order,10000), m.manufacturers_name";
//EOC HTC
  $manufacturers_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_SEARCH_RESULTS, $manufacturers_query_raw, $manufacturers_query_numrows);
  $manufacturers_query = tep_db_query($manufacturers_query_raw);
  while ($manufacturers = tep_db_fetch_array($manufacturers_query)) {
    if ((!isset($HTTP_GET_VARS['mID']) || (isset($HTTP_GET_VARS['mID']) && ($HTTP_GET_VARS['mID'] == $manufacturers['manufacturers_id']))) && !isset($mInfo) && (substr($action, 0, 3) != 'new')) {
      $manufacturer_products_query = tep_db_query("select count(*) as products_count from " . TABLE_PRODUCTS . " where manufacturers_id = '" . (int)$manufacturers['manufacturers_id'] . "'");
      $manufacturer_products = tep_db_fetch_array($manufacturer_products_query);

      $mInfo_array = array_merge($manufacturers, $manufacturer_products);
      $mInfo = new objectInfo($mInfo_array);
    }

    if (isset($mInfo) && is_object($mInfo) && ($manufacturers['manufacturers_id'] == $mInfo->manufacturers_id)) {
      echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_MANUFACTURERS, 'page=' . $HTTP_GET_VARS['page'] . '&mID=' . $manufacturers['manufacturers_id'] . '&action=edit') . '\'">' . "\n";
    } else {
      echo '              <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_MANUFACTURERS, 'page=' . $HTTP_GET_VARS['page'] . '&mID=' . $manufacturers['manufacturers_id']) . '\'">' . "\n";
    }
?>
                <td class="dataTableContent" align="center">&nbsp;&nbsp;<?php echo $manufacturers['manufacturers_id']; ?>&nbsp;&nbsp;</td>
                <td class="dataTableContent" width="100%" align="left"><?php echo $manufacturers['manufacturers_name']; ?></td>
				<td class="dataTableContent" align="center">&nbsp;&nbsp;<?php echo $manufacturers['sort_order']; ?>&nbsp;&nbsp;</td>
                <td class="dataTableContent" align="center">
<?php
      if ($manufacturers['manufacturers_status'] == '1') {
        echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_MANUFACTURERS, 'action=setflag&flag=0&mID=' . $manufacturers['manufacturers_id'] . '&page=' . $HTTP_GET_VARS['page']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
      } else {
        echo '<a href="' . tep_href_link(FILENAME_MANUFACTURERS, 'action=setflag&flag=1&mID=' . $manufacturers['manufacturers_id'] . '&page=' . $HTTP_GET_VARS['page']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
      }
?></td>
                <td class="dataTableContent" align="right"><?php if (isset($mInfo) && is_object($mInfo) && ($manufacturers['manufacturers_id'] == $mInfo->manufacturers_id)) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif'); } else { echo '<a href="' . tep_href_link(FILENAME_MANUFACTURERS, 'page=' . $HTTP_GET_VARS['page'] . '&mID=' . $manufacturers['manufacturers_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="5"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $manufacturers_split->display_count($manufacturers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_MANUFACTURERS); ?></td>
                    <td class="smallText" align="right"><?php echo $manufacturers_split->display_links($manufacturers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page']); ?></td>
                  </tr>
                </table></td>
              </tr>
<?php
  if (empty($action)) {
?>
              <tr>
                <td align="right" colspan="5" class="smallText"><?php echo '<a href="' . tep_href_link(FILENAME_MANUFACTURERS, 'page=' . $HTTP_GET_VARS['page'] . '&mID=' . $mInfo->manufacturers_id . '&action=new') . '">' . tep_image_button('button_insert.gif', IMAGE_INSERT) . '</a>'; ?></td>
              </tr>
<?php
  }
?>
            </table></td>
<?php
  $heading = array();
  $contents = array();

  switch ($action) {
/* code moved by splautz to put on separate page
    case 'new':
      $heading[] = array('text' => '<b>' . TEXT_HEADING_NEW_MANUFACTURER . '</b>');

      $contents = array('form' => tep_draw_form('manufacturers', FILENAME_MANUFACTURERS, 'action=insert', 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => TEXT_NEW_INTRO);
      $contents[] = array('text' => '<br>' . TEXT_MANUFACTURERS_NAME . '<br>' . tep_draw_input_field('manufacturers_name'));
      $contents[] = array('text' => '<br>' . TEXT_MANUFACTURERS_IMAGE . '<br>' . tep_draw_file_field('manufacturers_image'));
      $contents[] = array('text' => '<br>' . TEXT_MANUFACTURERS_SORT_ORDER . '<br>' . tep_draw_input_field('sort_order'));
      $contents[] = array('text' => '<br>' . TEXT_MANUFACTURERS_STATUS . '<br>' . tep_draw_input_field('manufacturers_status', '1', 'size="2"') . '&nbsp;1=Enabled 0=Disabled');

      $manufacturer_inputs_string = '';
      $languages = tep_get_languages();
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        $manufacturer_inputs_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('manufacturers_url[' . $languages[$i]['id'] . ']');
      }

      $contents[] = array('text' => '<br>' . TEXT_MANUFACTURERS_URL . $manufacturer_inputs_string);
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_save.gif', IMAGE_SAVE) . ' <a href="' . tep_href_link(FILENAME_MANUFACTURERS, 'page=' . $HTTP_GET_VARS['page'] . '&mID=' . $HTTP_GET_VARS['mID']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    case 'edit':
      $heading[] = array('text' => '<b>' . TEXT_HEADING_EDIT_MANUFACTURER . '</b>');

      $contents = array('form' => tep_draw_form('manufacturers', FILENAME_MANUFACTURERS, 'page=' . $HTTP_GET_VARS['page'] . '&mID=' . $mInfo->manufacturers_id . '&action=save', 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => TEXT_EDIT_INTRO);
      $contents[] = array('text' => '<br>' . TEXT_MANUFACTURERS_NAME . '<br>' . tep_draw_input_field('manufacturers_name', $mInfo->manufacturers_name));
      $contents[] = array('text' => '<br>' . TEXT_MANUFACTURERS_IMAGE . '<br>' . tep_draw_file_field('manufacturers_image') . '<br>' . $mInfo->manufacturers_image);
      $contents[] = array('text' => '<br>' . TEXT_MANUFACTURERS_SORT_ORDER . '<br>' . tep_draw_input_field('sort_order', $mInfo->sort_order));
      $contents[] = array('text' => '<br>' . TEXT_MANUFACTURERS_STATUS . '<br>' . tep_draw_input_field('manufacturers_status', $mInfo->manufacturers_status, 'size="2"') . '&nbsp;1=Enabled 0=Disabled');

      $manufacturer_inputs_string = '';
      $languages = tep_get_languages();
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        $manufacturer_inputs_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('manufacturers_url[' . $languages[$i]['id'] . ']', tep_get_manufacturer_url($mInfo->manufacturers_id, $languages[$i]['id']));
      }

      $contents[] = array('text' => '<br>' . TEXT_MANUFACTURERS_URL . $manufacturer_inputs_string);
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_save.gif', IMAGE_SAVE) . ' <a href="' . tep_href_link(FILENAME_MANUFACTURERS, 'page=' . $HTTP_GET_VARS['page'] . '&mID=' . $mInfo->manufacturers_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
*/
    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_HEADING_DELETE_MANUFACTURER . '</b>');

      $contents = array('form' => tep_draw_form('manufacturers', FILENAME_MANUFACTURERS, 'page=' . $HTTP_GET_VARS['page'] . '&mID=' . $mInfo->manufacturers_id . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_DELETE_INTRO);
      $contents[] = array('text' => '<br><b>' . $mInfo->manufacturers_name . '</b>');
      $contents[] = array('text' => '<br>' . tep_draw_checkbox_field('delete_image', '', true) . ' ' . TEXT_DELETE_IMAGE);

      if ($mInfo->products_count > 0) {
        $contents[] = array('text' => '<br>' . tep_draw_checkbox_field('delete_products') . ' ' . TEXT_DELETE_PRODUCTS);
        $contents[] = array('text' => '<br>' . sprintf(TEXT_DELETE_WARNING_PRODUCTS, $mInfo->products_count));
      }

      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(FILENAME_MANUFACTURERS, 'page=' . $HTTP_GET_VARS['page'] . '&mID=' . $mInfo->manufacturers_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    default:
      if (isset($mInfo) && is_object($mInfo)) {
        $heading[] = array('text' => '<b>' . $mInfo->manufacturers_name . '</b>');

        $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_MANUFACTURERS, 'page=' . $HTTP_GET_VARS['page'] . '&mID=' . $mInfo->manufacturers_id . '&action=edit') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . tep_href_link(FILENAME_MANUFACTURERS, 'page=' . $HTTP_GET_VARS['page'] . '&mID=' . $mInfo->manufacturers_id . '&action=delete') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a>');
        $contents[] = array('text' => '<br>' . TEXT_DATE_ADDED . ' ' . tep_date_short($mInfo->date_added));
        if (tep_not_null($mInfo->last_modified)) $contents[] = array('text' => TEXT_LAST_MODIFIED . ' ' . tep_date_short($mInfo->last_modified));
        $contents[] = array('text' => '<br>' . tep_info_image($mInfo->manufacturers_image, $mInfo->manufacturers_img_alt?$mInfo->manufacturers_img_alt:$mInfo->manufacturers_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT));
        $contents[] = array('text' => '<br>' . TEXT_PRODUCTS . ' ' . $mInfo->products_count);
      }
      break;
  }

  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '            <td width="25%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }
?>
          </tr>
        </table></td>
      </tr>
    </table>
<?php
  } // needed for new/edit manufacturer page
?>
    </td>
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