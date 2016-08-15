<?php
  require('includes/application_top.php');

// added by splautz for WYSIWYG compatibility
  $brInfo = tep_get_browser();
  $wysiwyg = ($brInfo['browser'] == 'MSIE' ? $brInfo['version'] : 0) >= 5.5 ? true : false;

  $action = (isset($HTTP_GET_VARS['action']) ? $HTTP_GET_VARS['action'] : '');

  if (tep_not_null($action)) {
    switch($action){
      case 'setflag':
        if ( ($HTTP_GET_VARS['flag'] == '0') || ($HTTP_GET_VARS['flag'] == '1') ) {
          if (isset($HTTP_GET_VARS['pID'])) {
            tep_db_query("update " . TABLE_PAGES . " set pages_status='".$HTTP_GET_VARS['flag']."' where pages_id='".$HTTP_GET_VARS['pID']."'");
          }
          if (USE_CACHE == 'true') {
            tep_reset_cache_block('pages');
          }
        }

        tep_redirect(tep_href_link(FILENAME_PAGES, 'pID=' . $HTTP_GET_VARS['pID']));
        break;
      case 'insert_page':
      case 'update_page':
        if (isset($HTTP_POST_VARS['edit_x']) || isset($HTTP_POST_VARS['edit_y'])) {
          $action = 'new_page';
        } else {
          if (isset($HTTP_GET_VARS['pID'])) $pages_id = tep_db_prepare_input($HTTP_GET_VARS['pID']);

          $sort_order = tep_db_prepare_input($HTTP_POST_VARS['sort_order']);
          if (!is_numeric($sort_order)) $sort_order = 'null';

          $sql_data_array = array('pages_status' => tep_db_prepare_input($HTTP_POST_VARS['pages_status']),
                                  'sort_order' => $sort_order,
                                  'pages_forward' => tep_db_prepare_input($HTTP_POST_VARS['pages_forward']));

// modified by splautz to ensure proper image management
          if (isset($HTTP_POST_VARS['pages_image']) && tep_not_null($HTTP_POST_VARS['pages_image']) && ($HTTP_POST_VARS['pages_image'] != 'none')) {
            $pages_image = tep_db_prepare_input($HTTP_POST_VARS['pages_image']);
          } else $pages_image = '';
          $sql_data_array['pages_image'] = $pages_image;
          if ($action == 'update_page') tep_remove_image($pages_id,'g',$pages_image);

          if ($action == 'insert_page') {
            tep_db_perform(TABLE_PAGES, $sql_data_array);
            $pages_id = tep_db_insert_id();
          }
          elseif ($action == 'update_page') {
            tep_db_perform(TABLE_PAGES, $sql_data_array, 'update', "pages_id = '" . (int)$pages_id . "'");
          }

          $languages = tep_get_languages();
          for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
            $language_id = $languages[$i]['id'];

            // added by splautz to ensure TABLE_SEO_SURLS is updated
            if (isset($HTTP_POST_VARS['pages_surls_name'][$language_id])) $surls_name=trim($HTTP_POST_VARS['pages_surls_name'][$language_id]);
            if (isset($HTTP_POST_VARS['pages_surls_id'][$language_id]) && is_numeric($surls_id=$HTTP_POST_VARS['pages_surls_id'][$language_id])) {
              if ($surls_name) {
                if (tep_check_dup_surl(true, $surls_id, tep_db_prepare_input($surls_name))) $surls_name = tep_get_pages_surls_name($pages_id, $language_id);
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
              if (tep_check_dup_surl(true, '', tep_db_prepare_input($surls_name), 'pages.php', 'pages_id=' . (int)$pages_id, $language_id)) {  // dup exists
                $surls_name = '';
                $surls_id = NULL;
              } else {  // dup not found, ok to insert
                $sql_data_array = array('surls_name' => tep_db_prepare_input($surls_name),
                  'surls_script' => 'pages.php',
                  'surls_param' => 'pages_id=' . (int)$pages_id,
                  'language_id' => $language_id);
                tep_db_perform(TABLE_SEO_URLS, $sql_data_array);
                $surls_id = tep_db_insert_id();
                $surls_updated = true;
              }
		    } else $surls_id = NULL;

            //HTC BOC
            $sql_data_array = array('pages_name' => tep_db_prepare_input($HTTP_POST_VARS['pages_name'][$language_id]),
                                    'pages_intro' => tep_db_prepare_input($HTTP_POST_VARS['pages_intro'][$language_id]),
                                    'pages_body' => tep_db_prepare_input($HTTP_POST_VARS['pages_body'][$language_id]),
                                    'pages_body2' => tep_db_prepare_input($HTTP_POST_VARS['pages_body2'][$language_id]),
                                    'pages_img_alt' => tep_db_prepare_input($HTTP_POST_VARS['pages_img_alt'][$language_id]),
                                    'pages_head_title_tag' => tep_db_prepare_input($HTTP_POST_VARS['pages_head_title_tag'][$language_id]),
                                    'pages_head_desc_tag' => tep_db_prepare_input($HTTP_POST_VARS['pages_head_desc_tag'][$language_id]),
                                    'pages_head_keywords_tag' => tep_db_prepare_input($HTTP_POST_VARS['pages_head_keywords_tag'][$language_id]),   
                                    'pages_surls_id' => ($surls_id===NULL)?'null':tep_db_prepare_input($surls_id),   
                                    'pages_h1' => tep_db_prepare_input($HTTP_POST_VARS['pages_h1'][$language_id]));
           //HTC EOC

            if ($action == 'insert_page') {
              $insert_sql_data = array('pages_id' => $pages_id,
                                       'language_id' => $language_id);

              $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

              tep_db_perform(TABLE_PAGES_DESCRIPTION, $sql_data_array);
            }
            elseif ($action == 'update_page') {
              tep_db_perform(TABLE_PAGES_DESCRIPTION, $sql_data_array, 'update', "pages_id = '" . (int)$pages_id . "' and language_id = '" . (int)$language_id . "'");
            }
          }

// modified by splautz to ensure proper image management
//        if ($pages_image = new upload('pages_image', DIR_FS_CATALOG_IMAGES)) {
//          tep_db_query("update " . TABLE_PAGES . " set pages_image = '" . tep_db_input($pages_image->filename) . "' where pages_id = '" . (int)$pages_id . "'");
//        }

          if (USE_CACHE == 'true') {
            tep_reset_cache_block('pages');
          }

          tep_redirect(tep_href_link(FILENAME_PAGES, 'pID=' . $pages_id));
        }
        break;

      case 'delete_page_confirm':
        if (isset($HTTP_POST_VARS['pages_id'])) {
          $pages_id = tep_db_prepare_input($HTTP_POST_VARS['pages_id']);
          if($pages_id!=1 && $pages_id!=2){

            // added by splautz to delete image
            tep_remove_all_images($pages_id,'g');

            // added by splautz to delete surl entries
            $pages_query = tep_db_query("select pd.pages_surls_id from " . TABLE_PAGES_DESCRIPTION . " pd where pd.pages_id = '" . (int)$pages_id . "' and pd.pages_surls_id > 0");
            while($pages = tep_db_fetch_array($pages_query)) {
              tep_remove_surl($pages['pages_surls_id']);
              $surls_updated = true;
            }

            tep_db_query("delete from " . TABLE_PAGES . " where pages_id = '" . (int)$pages_id . "'");
            tep_db_query("delete from " . TABLE_PAGES_DESCRIPTION . " where pages_id = '" . (int)$pages_id . "'");
          }
        }

        if (USE_CACHE == 'true') {
          tep_reset_cache_block('pages');
        }

        tep_redirect(tep_href_link(FILENAME_PAGES));
        break;
      case 'new_page_preview':
// modified by splautz to ensure proper image management
        if (!$wysiwyg || HTML_AREA_WYSIWYG_DISABLE_IMAGE == 'Disable') {
          $pages_image = new upload('pages_image');
          $pages_image->set_destination(DIR_FS_CATALOG_IMAGES);
          if ($pages_image->parse() && $pages_image->save()) {
            $pages_image_name = $pages_image->filename;
          } else {
            $pages_image_name = stripslashes(isset($HTTP_POST_VARS['pages_previous_image']) ? $HTTP_POST_VARS['pages_previous_image'] : '');
          }
        } else {
          if (isset($HTTP_POST_VARS['pages_image']) && tep_not_null($HTTP_POST_VARS['pages_image']) && ($HTTP_POST_VARS['pages_image'] != 'none')) {
            $pages_image_name = $HTTP_POST_VARS['pages_image'];
          } else {
            $pages_image_name = '';
          }
        }
        break;
    }
  }

// check if the catalog image directory exists
  if (is_dir(DIR_FS_CATALOG_IMAGES)) {
    if (!is_writeable(DIR_FS_CATALOG_IMAGES)) $messageStack->add(ERROR_CATALOG_IMAGE_DIRECTORY_NOT_WRITEABLE, 'error');
  } else {
    $messageStack->add(ERROR_CATALOG_IMAGE_DIRECTORY_DOES_NOT_EXIST, 'error');
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
        _script_name = "<?php echo ((HTML_AREA_WYSIWYG_BASIC_PAGE == 'Basic') ? 'editor_basic.js' : 'editor_advanced.js'); ?>";  // script name of editor to use
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
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();" >
<div id="spiffycalendar" class="text"></div>
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
  if ($action == 'new_page') {
    $parameters = array('pages_name' => '',
                        'pages_intro' => '',
                        'pages_body' => '',
                        'pages_body2' => '',
                        'pages_img_alt' => '',
                        'pages_head_title_tag' => '',
                        'pages_head_desc_tag' => '',
                        'pages_head_keyword_tag' => '',
                        'pages_surls_id' => '',
                        'pages_surls_name' => '',
                        'pages_h1' => '',
                        'pages_id' => '',
                        'pages_image' => '',
                        'pages_status' => '',
                        'sort_order' => '',
                        'pages_forward' => '');

    $pInfo = new objectInfo($parameters);
    $languages = tep_get_languages();

    if (isset($HTTP_GET_VARS['pID']) && empty($HTTP_POST_VARS)) {
      $pages_id = tep_db_prepare_input($HTTP_GET_VARS['pID']);
      $page_query = tep_db_query("select p.pages_id, p.pages_image, p.pages_status, p.sort_order, p.pages_forward from " . TABLE_PAGES . " p where p.pages_id = '" . (int)$pages_id . "'");
      $page = tep_db_fetch_array($page_query);
      $pInfo->objectInfo($page);
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        $page_query = tep_db_query("select pd.pages_name, pd.pages_intro, pd.pages_body, pd.pages_body2, pd.pages_img_alt, pd.pages_head_title_tag, pd.pages_head_desc_tag, pd.pages_head_keywords_tag, pd.pages_surls_id, pd.pages_h1 from " . TABLE_PAGES_DESCRIPTION . " pd where pd.pages_id = '" . $pInfo->pages_id . "' and pd.language_id = '" . $languages[$i]['id'] . "'");
        $page = tep_db_fetch_array($page_query);
		$pages_name[$languages[$i]['id']] = $page['pages_name'];
        $pages_intro[$languages[$i]['id']] = $page['pages_intro'];
        $pages_body[$languages[$i]['id']] = $page['pages_body'];
        $pages_body2[$languages[$i]['id']] = $page['pages_body2'];
        $pages_img_alt[$languages[$i]['id']] = $page['pages_img_alt'];
        $pages_head_title_tag[$languages[$i]['id']] = $page['pages_head_title_tag'];
        $pages_head_desc_tag[$languages[$i]['id']] = $page['pages_head_desc_tag'];
        $pages_head_keywords_tag[$languages[$i]['id']] = $page['pages_head_keywords_tag'];
        $pages_surls_id[$languages[$i]['id']] = $page['pages_surls_id'];
        $pages_surls_name[$languages[$i]['id']] = tep_get_pages_surls_name($pInfo->pages_id, $languages[$i]['id']);
        $pages_h1[$languages[$i]['id']] = $page['pages_h1'];
      }
    } elseif (tep_not_null($HTTP_POST_VARS)) {
      $http_post = tep_db_prepare_input($HTTP_POST_VARS);
      $pInfo->objectInfo($http_post);
      $pages_name = $http_post['pages_name'];
      $pages_intro = $http_post['pages_intro'];
      $pages_body = $http_post['pages_body'];
      $pages_body2 = $http_post['pages_body2'];
      $pages_img_alt = $http_post['pages_img_alt'];
      $pages_head_title_tag = $http_post['pages_head_title_tag'];
      $pages_head_desc_tag = $http_post['pages_head_desc_tag'];
      $pages_head_keywords_tag = $http_post['pages_head_keywords_tag'];
      $pages_surls_id = $http_post['pages_surls_id'];
      $pages_surls_name = $http_post['pages_surls_name'];
      $pages_h1 = $http_post['pages_h1'];
    }

    if (!isset($pInfo->pages_status)) $pInfo->pages_status = '1';
    switch ($pInfo->pages_status) {
      case '0': $in_status = false; $out_status = true; break;
      case '1':
      default: $in_status = true; $out_status = false;
    }
?>
    <?php echo tep_draw_form('new_page', FILENAME_PAGES, 'action=new_page_preview' . (isset($HTTP_GET_VARS['pID']) ? '&pID=' . $HTTP_GET_VARS['pID'] : ''), 'post', 'enctype="multipart/form-data"'); ?>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php if (isset($pages_name[$languages_id])) echo $pages_name[$languages_id]; else echo sprintf(TEXT_NEW_PAGE); ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main"><?php tep_echo_help('TEXT_PAGES_STATUS'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_radio_field('pages_status', '1', $in_status) . '&nbsp;' . TEXT_PAGE_ACTIVE . '&nbsp;' . tep_draw_radio_field('pages_status', '0', $out_status) . '&nbsp;' . TEXT_PAGE_NOT_ACTIVE; ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php tep_echo_help('TEXT_PAGES_SORT_ORDER'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('sort_order', $pInfo->sort_order, 'size="3" maxlength="3"'); ?></td>
          </tr>
<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php if ($i == 0) tep_echo_help('TEXT_PAGES_NAME'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_input_field('pages_name[' . $languages[$i]['id'] . ']', $value=(isset($pages_name[$languages[$i]['id']]) ? $pages_name[$languages[$i]['id']] : tep_get_pages_name($pInfo->pages_id, $languages[$i]['id'])), 'onKeyUp="CountInput(this);"');
                echo "<br>".spellcount_link('new_page','pages_name[' . $languages[$i]['id'] . ']',strlen($value)); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
    }
    if (!isset($pages_id) || $pages_id > 1) {
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php tep_echo_help('TEXT_PAGES_FORWARD'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('pages_forward', $pInfo->pages_forward); ?></td>
          </tr>
<?php
    }
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('TEXT_PAGES_INTRO'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_textarea_field('pages_intro[' . $languages[$i]['id'] . ']', 'soft', '60', '15', (isset($pages_intro[$languages[$i]['id']]) ? $pages_intro[$languages[$i]['id']] : tep_get_pages_intro($pInfo->pages_id, $languages[$i]['id'])));
                echo "<br>".spellcount_link('new_page','pages_intro[' . $languages[$i]['id'] . ']'); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
    }
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('TEXT_PAGES_BODY'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_textarea_field('pages_body[' . $languages[$i]['id'] . ']', 'soft', '60', '15', (isset($pages_body[$languages[$i]['id']]) ? $pages_body[$languages[$i]['id']] : tep_get_pages_body($pInfo->pages_id, $languages[$i]['id'])));
                echo "<br>".spellcount_link('new_page','pages_body[' . $languages[$i]['id'] . ']'); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
    }
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('TEXT_PAGES_BODY2'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_textarea_field('pages_body2[' . $languages[$i]['id'] . ']', 'soft', '60', '15', (isset($pages_body2[$languages[$i]['id']]) ? $pages_body2[$languages[$i]['id']] : tep_get_pages_body2($pInfo->pages_id, $languages[$i]['id'])));
                echo "<br>".spellcount_link('new_page','pages_body2[' . $languages[$i]['id'] . ']'); ?></td>
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
            <td class="main"><?php tep_echo_help('TEXT_PAGES_IMAGE'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
		        <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15'); ?>&nbsp;</td>
                <td class="main">
                <?php if (!$wysiwyg || HTML_AREA_WYSIWYG_DISABLE_IMAGE == 'Disable') {
                  echo tep_draw_file_field('pages_image');
                  if (isset($pInfo->pages_image)) echo '<br>' . $pInfo->pages_image;
                  echo tep_draw_hidden_field('pages_previous_image', $pInfo->pages_image);
                } else echo tep_draw_textarea_field('pages_image', 'soft', '30', '1', $pInfo->pages_image); ?>
                </td>
              </tr>
            </table></td>
          </tr>
<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('TEXT_PAGES_IMG_ALT'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_textarea_field('pages_img_alt[' . $languages[$i]['id'] . ']', 'soft', '60', '4', $value=(isset($pages_img_alt[$languages[$i]['id']]) ? $pages_img_alt[$languages[$i]['id']] : tep_get_pages_img_alt($pInfo->pages_id, $languages[$i]['id'])), 'onKeyUp="CountInput(this);"');
                echo "<br>".spellcount_link('new_page','pages_img_alt[' . $languages[$i]['id'] . ']',strlen($value)); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
    }
  if (!tep_not_null($pInfo->pages_forward)) {
?>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td colspan="2" class="main"><hr><?php echo TEXT_PAGE_METTA_INFO; ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr> 
<?php
	if (!isset($pages_id) || $pages_id > 1) for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('TEXT_PAGES_NAME_URL'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_input_field('pages_surls_name[' . $languages[$i]['id'] . ']', $value=(isset($pages_surls_name[$languages[$i]['id']]) ? $pages_surls_name[$languages[$i]['id']] : tep_get_pages_surls_name($pInfo->pages_id, $languages[$i]['id'])), 'size="70" onKeyUp="CountInput(this);"');
                echo "<br>".spellcount_link('new_page','pages_surls_name[' . $languages[$i]['id'] . ']',strlen($value));
				echo tep_draw_hidden_field('pages_surls_id[' . $languages[$i]['id'] . ']', htmlspecialchars($pages_surls_id[$languages[$i]['id']])); ?></td>
              </tr>
            </table></td>
          </tr>
<?php          
    }
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('TEXT_PAGES_HEAD_TITLE'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_input_field('pages_head_title_tag[' . $languages[$i]['id'] . ']', $value=(isset($pages_head_title_tag[$languages[$i]['id']]) ? $pages_head_title_tag[$languages[$i]['id']] : tep_get_pages_head_title_tag($pInfo->pages_id, $languages[$i]['id'])), 'size="70" onKeyUp="CountInput(this);"');
                echo "<br>".spellcount_link('new_page','pages_head_title_tag[' . $languages[$i]['id'] . ']',strlen($value)); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
    }
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>          
          <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('TEXT_PAGES_HEAD_DESCRIPTION'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_textarea_field('pages_head_desc_tag[' . $languages[$i]['id'] . ']', 'soft', '70', '5', $value=(isset($pages_head_desc_tag[$languages[$i]['id']]) ? $pages_head_desc_tag[$languages[$i]['id']] : tep_get_pages_head_desc_tag($pInfo->pages_id, $languages[$i]['id'])), 'onKeyUp="CountInput(this);"');
                echo "<br>".spellcount_link('new_page','pages_head_desc_tag[' . $languages[$i]['id'] . ']',strlen($value)); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
    }
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>          
          <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('TEXT_PAGES_HEAD_KEYWORDS'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_textarea_field('pages_head_keywords_tag[' . $languages[$i]['id'] . ']', 'soft', '70', '2', $value=(isset($pages_head_keywords_tag[$languages[$i]['id']]) ? $pages_head_keywords_tag[$languages[$i]['id']] : tep_get_pages_head_keywords_tag($pInfo->pages_id, $languages[$i]['id'])), 'onKeyUp="CountInput(this);"');
                echo "<br>".spellcount_link('new_page','pages_head_keywords_tag[' . $languages[$i]['id'] . ']',strlen($value)); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
    }
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>          
          <tr>
            <td class="main" valign="top"><?php if ($i == 0) tep_echo_help('TEXT_PAGES_H1'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_textarea_field('pages_h1[' . $languages[$i]['id'] . ']', 'soft', '70', '2', $value=(isset($pages_h1[$languages[$i]['id']]) ? $pages_h1[$languages[$i]['id']] : tep_get_pages_h1($pInfo->pages_id, $languages[$i]['id'])), 'onKeyUp="CountInput(this);"');
                echo "<br>".spellcount_link('new_page','pages_h1[' . $languages[$i]['id'] . ']',strlen($value)); ?></td>
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
<?php
  }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td class="main" align="center"><?php echo tep_image_submit('button_preview.gif', IMAGE_PREVIEW) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_PAGES, (isset($HTTP_GET_VARS['pID']) ? '&pID=' . $HTTP_GET_VARS['pID'] : '')) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>'; ?></td>
      </tr>
    </table></form>
<?php
//MaxiDVD Added WYSIWYG HTML Area Box + Admin Function v1.7 - 2.2 MS2 Products Description HTML - </form>
   if ($wysiwyg && (HTML_AREA_WYSIWYG_DISABLE_PAGE != 'Disable' || HTML_AREA_WYSIWYG_DISABLE_IMAGE != 'Disable')) { ?>
            <script language="JavaScript1.2" defer>
             var config = new Object();  // create new config object
             config.debug = <?php echo HTML_AREA_WYSIWYG_DEBUG; ?>;
    <?php if (HTML_AREA_WYSIWYG_DISABLE_PAGE != 'Disable') { ?>
             config.width = "<?php echo PAGE_EDITOR_WYSIWYG_WIDTH; ?>px";
             config.height = "<?php echo PAGE_EDITOR_WYSIWYG_HEIGHT; ?>px";
             config.bodyStyle = 'background-color: <?php echo HTML_AREA_WYSIWYG_BG_COLOUR; ?>; font-family: "<?php echo HTML_AREA_WYSIWYG_FONT_TYPE; ?>"; color: <?php echo HTML_AREA_WYSIWYG_FONT_COLOUR; ?>; font-size: <?php echo HTML_AREA_WYSIWYG_FONT_SIZE; ?>pt;';
             config.stylesheet = '<?php echo HTTP_CATALOG_SERVER . DIR_WS_CATALOG . 'stylesheet.css'; ?>';
          <?php for ($i = 0, $n = sizeof($languages); $i < $n; $i++) { ?>
             editor_generate('pages_intro[<?php echo $languages[$i]['id']; ?>]',config);
             editor_generate('pages_body[<?php echo $languages[$i]['id']; ?>]',config);
             editor_generate('pages_body2[<?php echo $languages[$i]['id']; ?>]',config);
          <?php }
          }
          if (HTML_AREA_WYSIWYG_DISABLE_IMAGE != 'Disable') { ?>
             config.width  = "<?php echo IMAGE_EDITOR_WYSIWYG_WIDTH; ?>px";
             config.height = "<?php echo IMAGE_EDITOR_WYSIWYG_HEIGHT; ?>px";
             config.bodyStyle = 'background-color: white; font-family: Arial; color: black; font-size: 12px;';
             config.stylesheet = null;
             config.toolbar = [ ["InsertImageURL"] ];
             config.OscImageRoot = '<?php echo trim(HTTP_SERVER . DIR_WS_CATALOG_IMAGES); ?>';
             editor_generate('pages_image',config);
    <?php } ?>
            </script>
<?php }
  } elseif ($action == 'new_page_preview') {
    $languages = tep_get_languages();
    if (tep_not_null($HTTP_POST_VARS)) {
      $http_post = tep_db_prepare_input($HTTP_POST_VARS);
      $pInfo = new objectInfo($http_post);
      $pages_name = $http_post['pages_name'];
      $pages_intro = $http_post['pages_intro'];
      $pages_body = $http_post['pages_body'];
      $pages_body2 = $http_post['pages_body2'];
      $pages_img_alt = $http_post['pages_img_alt'];
      $pages_head_title_tag = $http_post['pages_head_title_tag'];
      $pages_head_desc_tag = $http_post['pages_head_desc_tag'];
      $pages_head_keywords_tag = $http_post['pages_head_keywords_tag'];
      $pages_surls_id = $http_post['pages_surls_id'];
      $pages_surls_name = $http_post['pages_surls_name'];
      $pages_h1 = $http_post['pages_h1'];
    } else {
      $pages_id = tep_db_prepare_input($HTTP_GET_VARS['pID']);
      $page_query = tep_db_query("select p.pages_id, p.pages_image, p.pages_status, p.sort_order, p.pages_forward from " . TABLE_PAGES . " p where p.pages_id = '" . (int)$pages_id . "'");
      $page = tep_db_fetch_array($page_query);
      $pInfo = new objectInfo($page);
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        $page_query = tep_db_query("select pd.pages_name, pd.pages_intro, pd.pages_body, pd.pages_body2, pd.pages_img_alt, pd.pages_head_title_tag, pd.pages_head_desc_tag, pd.pages_head_keywords_tag, pd.pages_surls_id, pd.pages_h1 from " . TABLE_PAGES_DESCRIPTION . " pd where pd.pages_id = '" . $pInfo->pages_id . "' and pd.language_id = '" . $languages[$i]['id'] . "'");
        $page = tep_db_fetch_array($page_query);
		$pages_name[$languages[$i]['id']] = $page['pages_name'];
        $pages_intro[$languages[$i]['id']] = $page['pages_intro'];
        $pages_body[$languages[$i]['id']] = $page['pages_body'];
        $pages_body2[$languages[$i]['id']] = $page['pages_body2'];
        $pages_img_alt[$languages[$i]['id']] = $page['pages_img_alt'];
        $pages_head_title_tag[$languages[$i]['id']] = $page['pages_head_title_tag'];
        $pages_head_desc_tag[$languages[$i]['id']] = $page['pages_head_desc_tag'];
        $pages_head_keywords_tag[$languages[$i]['id']] = $page['pages_head_keywords_tag'];
        $pages_surls_id[$languages[$i]['id']] = $page['pages_surls_id'];
        $pages_surls_name[$languages[$i]['id']] = tep_get_pages_surls_name($pInfo->pages_id, $languages[$i]['id']);
        $pages_h1[$languages[$i]['id']] = $page['pages_h1'];
      }
      $pages_image_name = $pInfo->pages_image;
    }

    $form_action = (isset($HTTP_GET_VARS['pID'])) ? 'update_page' : 'insert_page';

    echo tep_draw_form($form_action, FILENAME_PAGES, 'action=' . $form_action . (isset($HTTP_GET_VARS['pID']) ? '&pID=' . $HTTP_GET_VARS['pID'] : ''), 'post', 'enctype="multipart/form-data"');

    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
      if (isset($HTTP_GET_VARS['read']) && ($HTTP_GET_VARS['read'] == 'only')) {
        $pInfo->pages_name = tep_get_pages_name($pInfo->pages_id, $languages[$i]['id']);
        $pInfo->pages_intro= tep_get_pages_intro($pInfo->pages_id, $languages[$i]['id']);
        $pInfo->pages_body = tep_get_pages_body($pInfo->pages_id, $languages[$i]['id']);
        $pInfo->pages_body2 = tep_get_pages_body2($pInfo->pages_id, $languages[$i]['id']);
        $pInfo->pages_img_alt = tep_get_pages_img_alt($pInfo->pages_id, $languages[$i]['id']);
      } else {
        $pInfo->pages_name = $pages_name[$languages[$i]['id']];
        $pInfo->pages_intro = $pages_intro[$languages[$i]['id']];
        $pInfo->pages_body = $pages_body[$languages[$i]['id']];
        $pInfo->pages_body2 = $pages_body2[$languages[$i]['id']];
        $pInfo->pages_img_alt = $pages_img_alt[$languages[$i]['id']];
      }
?>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . $pInfo->pages_name; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td class="pageContent" valign="top"><?php echo tep_image(DIR_WS_CATALOG_IMAGES . $pages_image_name, $pInfo->pages_img_alt?$pInfo->pages_img_alt:$pInfo->pages_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'align="right" hspace="5" vspace="5"');
        if (tep_not_null($pInfo->pages_intro)) echo $pInfo->pages_intro;
        else echo "<h2>" . $pInfo->pages_name . "</h2>"; ?>
        </td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td class="pageContent"><?php echo $pInfo->pages_body; ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td class="pageContent"><?php echo $pInfo->pages_body2; ?></td>
      </tr>
<?php
    }
    if (isset($HTTP_GET_VARS['read']) && ($HTTP_GET_VARS['read'] == 'only')) {
      if (isset($HTTP_GET_VARS['origin'])) {
        $pos_params = strpos($HTTP_GET_VARS['origin'], '?', 0);
        if ($pos_params != false) {
          $back_url = substr($HTTP_GET_VARS['origin'], 0, $pos_params);
          $back_url_params = substr($HTTP_GET_VARS['origin'], $pos_params + 1);
        } else {
          $back_url = $HTTP_GET_VARS['origin'];
          $back_url_params = '';
        }
      } else {
        $back_url = FILENAME_PAGES;
        $back_url_params = 'pID=' . $pInfo->pages_id;
      }
?>
      <tr>
        <td align="right"><?php echo '<a href="' . tep_href_link($back_url, $back_url_params, 'NONSSL') . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td>
      </tr>
<?php
    } else {
?>
      <tr>
        <td align="right" class="smallText">
<?php
/* Re-Post all POST'ed variables */
      reset($HTTP_POST_VARS);
      while (list($key, $value) = each($HTTP_POST_VARS)) {
        if (!is_array($HTTP_POST_VARS[$key])) {
          echo tep_draw_hidden_field($key, htmlspecialchars(tep_db_prepare_input($value)));
        }
      }
      $languages = tep_get_languages();
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        echo tep_draw_hidden_field('pages_name[' . $languages[$i]['id'] . ']', htmlspecialchars($pages_name[$languages[$i]['id']]));
        echo tep_draw_hidden_field('pages_intro[' . $languages[$i]['id'] . ']', htmlspecialchars($pages_intro[$languages[$i]['id']]));
        echo tep_draw_hidden_field('pages_body[' . $languages[$i]['id'] . ']', htmlspecialchars($pages_body[$languages[$i]['id']]));
        echo tep_draw_hidden_field('pages_body2[' . $languages[$i]['id'] . ']', htmlspecialchars($pages_body2[$languages[$i]['id']]));
        echo tep_draw_hidden_field('pages_img_alt[' . $languages[$i]['id'] . ']', htmlspecialchars($pages_img_alt[$languages[$i]['id']]));
        echo tep_draw_hidden_field('pages_head_title_tag[' . $languages[$i]['id'] . ']', htmlspecialchars($pages_head_title_tag[$languages[$i]['id']]));
        echo tep_draw_hidden_field('pages_head_desc_tag[' . $languages[$i]['id'] . ']', htmlspecialchars($pages_head_desc_tag[$languages[$i]['id']]));
        echo tep_draw_hidden_field('pages_head_keywords_tag[' . $languages[$i]['id'] . ']', htmlspecialchars($pages_head_keywords_tag[$languages[$i]['id']]));
        echo tep_draw_hidden_field('pages_surls_id[' . $languages[$i]['id'] . ']', htmlspecialchars($pages_surls_id[$languages[$i]['id']]));
        echo tep_draw_hidden_field('pages_surls_name[' . $languages[$i]['id'] . ']', htmlspecialchars($pages_surls_name[$languages[$i]['id']]));
        echo tep_draw_hidden_field('pages_h1[' . $languages[$i]['id'] . ']', htmlspecialchars($pages_h1[$languages[$i]['id']]));
      }
      echo tep_draw_hidden_field('pages_image', stripslashes($pages_image_name));

      echo tep_image_submit('button_back.gif', IMAGE_BACK, 'name="edit"') . '&nbsp;&nbsp;';

      if (isset($HTTP_GET_VARS['pID'])) {
        echo tep_image_submit('button_update.gif', IMAGE_UPDATE);
      } else {
        echo tep_image_submit('button_insert.gif', IMAGE_INSERT);
      }
      echo '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_PAGES, (isset($HTTP_GET_VARS['pID']) ? 'pID=' . $HTTP_GET_VARS['pID'] : '')) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>';
?>
        </td>
      </tr>
    </table></form>
<?php
    }
  } else {
?>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_PAGE_ID; ?></td>
                <td class="dataTableHeadingContent" width="100%" align="left"><?php echo TABLE_HEADING_PAGE_NAME; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_SORT; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_PAGE_STATUS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
    $pages_count = 0;
    $pages_query = tep_db_query("select p.pages_id, p.pages_image, p.pages_status, p.sort_order, p.pages_forward, pd.pages_name, pd.pages_img_alt  from " . TABLE_PAGES . " p, " . TABLE_PAGES_DESCRIPTION . " pd where p.pages_id = pd.pages_id and pd.language_id = '" . (int)$languages_id . "' order by COALESCE(p.sort_order,1000), p.pages_id");
    while ($pages = tep_db_fetch_array($pages_query)) {
      $pages_count++;
      $rows++;

      if ( (!isset($HTTP_GET_VARS['pID']) || (isset($HTTP_GET_VARS['pID']) && ($HTTP_GET_VARS['pID'] == $pages['pages_id']))) && !isset($pInfo) && (substr($action, 0, 3) != 'new')) {
        $pInfo = new objectInfo($pages);
      }

      if (isset($pInfo) && is_object($pInfo) && ($pages['pages_id'] == $pInfo->pages_id) ) {
        echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_PAGES, 'pID=' . $pages['pages_id'] . '&action=new_page_preview&read=only') . '\'">' . "\n";
      } else {
        echo '              <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_PAGES, 'pID=' . $pages['pages_id']) . '\'">' . "\n";
      }
?>
                <td class="dataTableContent"><?php echo '<a href="' . tep_href_link(FILENAME_PAGES, 'pID=' . $pages['pages_id'] . '&action=new_page_preview&read=only') . '">' . tep_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW) . '</a></td><td class="dataTableContent" align="center">&nbsp;&nbsp;' . $pages['pages_id'] . '&nbsp;&nbsp;</td><td class="dataTableContent" align="left">' . $pages['pages_name'] . '<td class="dataTableContent" align="right">&nbsp;&nbsp;' . $pages['sort_order'] . '&nbsp;&nbsp;'; ?></td>
                <td class="dataTableContent" align="center">
<?php
      if ($pages['pages_status'] == '1') {
        echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_PAGES, 'action=setflag&flag=0&pID=' . $pages['pages_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
      } else {
        echo '<a href="' . tep_href_link(FILENAME_PAGES, 'action=setflag&flag=1&pID=' . $pages['pages_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
      }
?>
                </td>
                <td class="dataTableContent" align="right"><?php if (isset($pInfo) && is_object($pInfo) && ($pages['pages_id'] == $pInfo->pages_id)) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link(FILENAME_PAGES, 'pID=' . $pages['pages_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    }
?>
              <tr>
                <td colspan="4"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText"><?php echo TEXT_PAGES . '&nbsp;' . $pages_count; ?></td>
                    <td align="right" class="smallText"><?php echo '<a href="' . tep_href_link(FILENAME_PAGES, 'action=new_page') . '">' . tep_image_button('button_new_page.gif', IMAGE_NEW_PAGE) . '</a>'; ?>&nbsp;</td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
<?php
    $heading = array();
    $contents = array();
    switch ($action) {
      case 'delete_page':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_PAGE . '</b>');

        $contents = array('form' => tep_draw_form('pages', FILENAME_PAGES, 'action=delete_page_confirm') . tep_draw_hidden_field('pages_id', $pInfo->pages_id));
        $contents[] = array('text' => TEXT_DELETE_PAGE_INTRO);
        $contents[] = array('text' => '<br><b>' . $pInfo->pages_name . '</b>');
        $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(FILENAME_PAGES, 'pID=' . $pInfo->pages_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
        break;
      default:
        if (isset($pInfo) && is_object($pInfo)) { // page info box contents
          $heading[] = array('text' => '<b>' . tep_get_pages_name($pInfo->pages_id, $languages_id) . '</b>');

          $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_PAGES, 'pID=' . $pInfo->pages_id . '&action=new_page') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . tep_href_link(FILENAME_PAGES, 'pID=' . $pInfo->pages_id . '&action=delete_page') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a>');
          $contents[] = array('text' => '<br>' . TEXT_PAGES_NAME . ' ' . $pInfo->pages_name);
          if (tep_not_null($pInfo->pages_forward)) $contents[] = array('text' => TEXT_PAGES_FORWARD . ' ' . $pInfo->pages_forward);
          $contents[] = array('text' => '<br>' . tep_info_image($pInfo->pages_image, $pInfo->pages_img_alt?$pInfo->pages_img_alt:$pInfo->pages_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '<br>' . $pInfo->pages_image);
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
  }
?>

    </td>
<!-- body_text_eof //-->
  </tr>
</table>
<FORM name="hidden_form" method="POST" action="spellcheck.php?init=yes" target="WIN">
<INPUT type="hidden" name="form_name" value="">
<INPUT type="hidden" name="field_name" value="">
<INPUT type="hidden" name="first_time_text" value="">
</FORM>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>