<?php
/*
  $Id: products_images.php, ver 2.0 10/31/2005 Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

// added by splautz for WYSIWYG compatibility
  $brInfo = tep_get_browser();
  $wysiwyg = ($brInfo['browser'] == 'MSIE' ? $brInfo['version'] : 0) >= 5.5 ? true : false;

  $groups = array('p' => 'products','c' => 'categories','m' => 'manufacturers','g' => 'pages');
  $languages = tep_get_languages();

  $action = (isset($_REQUEST['action']) ? $_REQUEST['action'] : '');
  $image_page = tep_db_prepare_input(isset($_REQUEST['image_page']) ? $_REQUEST['image_page'] : '1');
  if (!is_numeric($image_page) || $image_page == '0') $image_page = '1';
  $group_id_view = tep_db_prepare_input(isset($_REQUEST['group_id_view']) ? $_REQUEST['group_id_view'] : '');
  $image_group = tep_db_prepare_input(isset($_REQUEST['image_group']) ? $_REQUEST['image_group'] : 'p');
  $images_id = tep_db_prepare_input(isset($_REQUEST['images_id']) ? $_REQUEST['images_id'] : '');
  $group_id = tep_db_prepare_input(isset($_REQUEST['group_id']) ? $_REQUEST['group_id'] : $group_id_view);
  $sort_order = tep_db_prepare_input(isset($_REQUEST['sort_order']) ? $_REQUEST['sort_order'] : '');
  $images_alt = tep_db_prepare_input(isset($_REQUEST['images_alt']) ? $_REQUEST['images_alt'] : array());
  $images_name = '';
  if (!$wysiwyg || HTML_AREA_WYSIWYG_DISABLE_IMAGE == 'Disable') {
    if ($images_upload = new upload('images_name', DIR_FS_CATALOG_IMAGES)) $images_name = $images_upload->filename;
  } elseif (isset($HTTP_POST_VARS['images_name']) && tep_not_null($HTTP_POST_VARS['images_name']) && ($HTTP_POST_VARS['images_name'] != 'none')) {
    $images_name = tep_db_prepare_input($HTTP_POST_VARS['images_name']);
  }

  if (tep_not_null($action)) {
    if (!is_numeric($sort_order)) $sort_order = "null";
    else $sort_order = "'$sort_order'";
    $page_info = 'image_page='.$image_page.'&group_id_view='.($action=='Insert'?$group_id:$group_id_view).'&image_group='.$image_group;

    switch ($action) {
      case 'Insert':
        if (is_numeric($group_id) && tep_not_null($images_name)) {
          tep_db_query("insert into " . TABLE_IMAGES . " values ('', '" . (int)$group_id . "', " . $sort_order . ", '" . tep_db_input($images_name) . "', '" . $image_group . "')");
          $images_id = tep_db_insert_id();
          for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
            tep_db_query("insert into " . TABLE_IMAGES_DESCRIPTION . " values ('" . (int)$images_id . "', '" . $languages[$i]['id'] . "', '" . tep_db_input(isset($images_alt[$languages[$i]['id']]) ? $images_alt[$languages[$i]['id']] : '') . "')");
          }
        }
        tep_redirect(tep_href_link(FILENAME_IMAGES, $page_info.'&group_id='.$group_id));
        break;
      case 'Update':
        if (is_numeric($images_id) && is_numeric($group_id)) {
          if (tep_not_null($images_name)) {
            tep_remove_image($images_id,$image_group,$images_name,true);
			$update_image = "images_image = '" . tep_db_input($images_name) . "', ";
          } else $update_image = '';
          tep_db_query("update " . TABLE_IMAGES . " set group_id = '" . (int)$group_id . "', sort_order = " . $sort_order . ", " . $update_image . "group_type = '" . $image_group . "' where images_id = '" . (int)$images_id . "'");
          for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
            tep_db_query("update " . TABLE_IMAGES_DESCRIPTION . " set images_alt = '" . tep_db_input(isset($images_alt[$languages[$i]['id']]) ? $images_alt[$languages[$i]['id']] : '') . "' where images_id = '" . (int)$images_id . "' and language_id = '" . $languages[$i]['id'] . "'");
          }
        }
        tep_redirect(tep_href_link(FILENAME_IMAGES, $page_info));
        break;
       case 'Delete':
        if (is_numeric($images_id)) {
          tep_remove_image($images_id,$image_group,'',true);
          tep_db_query("delete from " . TABLE_IMAGES . " where images_id = '" . (int)$images_id . "'");
          tep_db_query("delete from " . TABLE_IMAGES_DESCRIPTION . " where images_id = '" . (int)$images_id . "'");
        }
        tep_redirect(tep_href_link(FILENAME_IMAGES, $page_info));
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
<script language="javascript" src="includes/javascript/spellcheck.js"></script>
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

<!-- images //-->
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading">&nbsp;<?php echo HEADING_TITLE_ATRIB; ?>&nbsp;</td>
            <td>&nbsp;<?php echo tep_image(DIR_WS_IMAGES . 'pixel_trans.gif', '', '1', '53'); ?>&nbsp;</td>
            <td><form name="formview"><select name="group_id_view" onChange="return formview.submit();">
<?php
  echo '<option name="Show All '.ucfirst($groups[$image_group]).'" value="">Show All '.ucfirst($groups[$image_group]).'</option>';
  if ($image_group == 'm') $group_query_str = "select g." . $groups[$image_group] . "_id as id, g." . $groups[$image_group] . "_name as name from " . constant('TABLE_'.strtoupper($groups[$image_group])) . " g order by g." . $groups[$image_group] . "_name";
  else $group_query_str = "select g." . $groups[$image_group] . "_id as id, d." . $groups[$image_group] . "_name as name from " . constant('TABLE_'.strtoupper($groups[$image_group])) . " g, " . constant('TABLE_'.strtoupper($groups[$image_group]).'_DESCRIPTION') . " d where d." . $groups[$image_group] . "_id = g." . $groups[$image_group] . "_id and d.language_id = '" . $languages_id . "' order by d." . $groups[$image_group] . "_name";
  $group_query = tep_db_query($group_query_str);
  while ($group_values = tep_db_fetch_array($group_query)) {
    if ($group_id_view == $group_values['id']) {
      echo '<option name="' . $group_values['name'] . '" value="' . $group_values['id'] . '" SELECTED>' . $group_values['name'] . '</option>';
    } else {
      echo '<option name="' . $group_values['name'] . '" value="' . $group_values['id'] . '">' . $group_values['name'] . '</option>';
    }
  }
?>
            </select>&nbsp;&nbsp;<select name="image_group" onChange="this.form.group_id_view.value=''; return formview.submit();">
<?php
  foreach($groups as $tag => $group) {
    echo '              <option name="'.ucfirst($group).'" value="'.$tag.'"';
    if ($image_group == $tag) echo ' SELECTED';
    echo '>'.ucfirst($group)."</option>\n";
  }
?>
            </select></form>&nbsp;</td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><form name="images" action="<?php echo tep_href_link(FILENAME_IMAGES); ?>" method="post" enctype="multipart/form-data"><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td colspan="7" class="smallText">
<?php
  $per_page = 20;

  if ($image_group == 'm') $images = "select i.*, id.*, d." . $groups[$image_group] . "_name as name from " . TABLE_IMAGES . " i left join " . TABLE_IMAGES_DESCRIPTION . " id on i.images_id = id.images_id left join " . constant('TABLE_'.strtoupper($groups[$image_group])) . " d on d." . $groups[$image_group] . "_id = i.group_id where i.group_type = '$image_group' and id.language_id = '" . (int)$languages_id . "'";
  else $images = "select i.*, id.*, d." . $groups[$image_group] . "_name as name from " . TABLE_IMAGES . " i left join " . TABLE_IMAGES_DESCRIPTION . " id on i.images_id = id.images_id left join " . constant('TABLE_'.strtoupper($groups[$image_group]).'_DESCRIPTION') . " d on d." . $groups[$image_group] . "_id = i.group_id where i.group_type = '$image_group' and d.language_id = id.language_id and id.language_id = '" . (int)$languages_id . "'";
  if (is_numeric($group_id_view)) $images .= " and d." . $groups[$image_group] . "_id='" . (int)$group_id_view . "'";
  $images .= " order by name, i.group_id, COALESCE(i.sort_order,10000), i.images_id";

  $image_query = tep_db_query($images);
  $num_rows = tep_db_num_rows($image_query);

  if ($num_rows <= $per_page) {
     $num_pages = 1;
  } else if (($num_rows % $per_page) == 0) {
     $num_pages = ($num_rows / $per_page);
  } else {
     $num_pages = ($num_rows / $per_page) + 1;
  }
  $num_pages = (int) $num_pages;

  if ($image_page > $num_pages) $image_page = $num_pages;
  $prev_image_page = $image_page - 1;
  $next_image_page = $image_page + 1;
  $image_page_start = ($per_page * $image_page) - $per_page;

  $images = $images . " LIMIT $image_page_start, $per_page";

  // Previous
  if ($prev_image_page) {
    echo '<a href="' . tep_href_link(FILENAME_IMAGES, 'image_page=' . $prev_image_page . '&image_group=' . $image_group) . '"> &lt;&lt; </a> | ';
  }

  for ($i = 1; $i <= $num_pages; $i++) {
    if ($i != $image_page) {
      echo '<a href="' . tep_href_link(FILENAME_IMAGES, 'image_page=' . $i . '&image_group=' . $image_group) . '">' . $i . '</a> | ';
    } else {
      echo '<b><font color="red">' . $i . '</font></b> | ';
    }
  }

  // Next
  if ($image_page != $num_pages) {
    echo '<a href="' . tep_href_link(FILENAME_IMAGES, 'image_page=' . $next_image_page . '&image_group=' . $image_group) . '"> &gt;&gt; </a>';
  }
?>
            </td>
          </tr>
          <tr>
            <td colspan="7"><?php echo tep_black_line(); ?></td>
          </tr>
          <tr class="dataTableHeadingRow">
            <td class="dataTableHeadingContent">&nbsp;<?php echo TABLE_HEADING_ID; ?>&nbsp;</td>
            <td class="dataTableHeadingContent">&nbsp;<?php echo ucfirst($groups[$image_group]); ?>&nbsp;</td>
            <td class="dataTableHeadingContent" align="center">&nbsp;<?php echo TABLE_HEADING_IMAGE; ?>&nbsp;</td>
            <td class="dataTableHeadingContent" align="center">&nbsp;<?php echo TABLE_HEADING_ALT; ?>&nbsp;</td>
            <td class="dataTableHeadingContent" align="center">&nbsp;<?php echo TABLE_HEADING_ORDER; ?>&nbsp;</td>
            <td class="dataTableHeadingContent" align="center">&nbsp;<?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
          </tr>
          <tr>
            <td colspan="7"><?php echo tep_black_line(); ?></td>
          </tr>
<?php
  $next_id = 1;
  $rows = 0;
  $images = tep_db_query($images);
  while ($images_values = tep_db_fetch_array($images)) {
?>
          <tr class="<?php echo (floor($rows/2) == ($rows/2) ? 'attributes-even' : 'attributes-odd'); ?>">
<?php
    if (($action == 'update_image') && ($images_id == $images_values['images_id'])) {
?>
            <td class="smallText" valign="top">&nbsp;<?php echo $images_values['images_id']; ?><input type="hidden" name="images_id" value="<?php echo $images_values['images_id']; ?>">&nbsp;</td>
            <td class="smallText" valign="top">&nbsp;<select name="group_id">
<?php
      $group_query = tep_db_query($group_query_str);
      while ($group_values = tep_db_fetch_array($group_query)) {
        if ($images_values['group_id'] == $group_values['id']) {
          echo '<option name="' . $group_values['name'] . '" value="' . $group_values['id'] . '" SELECTED>' . $group_values['name'] . '</option>';
        } else {
          echo '<option name="' . $group_values['name'] . '" value="' . $group_values['id'] . '">' . $group_values['name'] . '</option>';
        }
      }
?>
            </select>&nbsp;</td>
            <td class="smallText" align="center" valign="top">
<?php
      $images_alt = array();
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        $images_values2_query = tep_db_query("select images_alt from " . TABLE_IMAGES_DESCRIPTION . " where images_id = '" . $images_values['images_id'] . "' and language_id = '" . $languages[$i]['id'] . "'");
        $images_values2 = tep_db_fetch_array($images_values2_query);
        $images_alt[$languages[$i]['id']] = $images_values2['images_alt'];
      }
      if (!$wysiwyg || HTML_AREA_WYSIWYG_DISABLE_IMAGE == 'Disable') echo '&nbsp;'.tep_draw_file_field('images_name').'&nbsp;<br>&nbsp;' . $images_values['images_image'] . '&nbsp;';
      else echo tep_draw_textarea_field('images_name', 'soft', '30', '1', $images_values['images_image'], '', false);
//    echo '<br>&nbsp;' . $images_values['images_image'] . '&nbsp;<br>&nbsp;' . tep_image(DIR_WS_CATALOG_IMAGES . $images_values['images_image'], $images_values['images_alt']?$images_values['images_alt']:$images_values['name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"') . '&nbsp;';
?>
            </td>
            <td class="smallText" valign="top">
<?php
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        if ($i) echo '<br>';
?>
            <table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
				<td class="main"><?php echo tep_draw_textarea_field('images_alt[' . $languages[$i]['id'] . ']', 'soft', '40', '3', $value=$images_alt[$languages[$i]['id']], 'onKeyUp="CountInput(this);"');
                echo "<br>".spellcount_link('images','images_alt[' . $languages[$i]['id'] . ']',strlen($value)); ?></td>
              </tr>
            </table><?php
      }
?>
            </td>
            <td align="center" class="smallText" valign="top">&nbsp;<input type="text" name="sort_order" value="<?php echo $images_values['sort_order']; ?>" size="6">&nbsp;</td>
            <td align="center" class="smallText" valign="top">&nbsp;&nbsp;<input type="submit" name="action" value="Update">&nbsp;<input type="submit" name="action" value="Cancel">&nbsp;</td>
<?php
    } elseif (($action == 'delete_image') && ($images_id == $images_values['images_id'])) {
?>
            <td class="smallText" valign="top">&nbsp;<b><?php echo $images_values['images_id']; ?></b>&nbsp;</td>
            <td class="smallText" valign="top">&nbsp;<b><?php echo $images_values['name']; ?></b>&nbsp;</td>
            <td class="smallText" align="center" valign="top">&nbsp;<b><?php echo $images_values['images_image'] . '</b>&nbsp;<br>&nbsp;' . tep_image(DIR_WS_CATALOG_IMAGES . $images_values['images_image'], $images_values['images_alt']?$images_values['images_alt']:$images_values['name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"'); ?>&nbsp;</td>
            <td class="smallText" valign="top">&nbsp;<b><?php echo htmlspecialchars($images_values['images_alt']); ?></b>&nbsp;</td>
            <td class="smallText" align="center" valign="top">&nbsp;<b><?php echo $images_values['sort_order']; ?></b>&nbsp;</td>
            <td align="center" class="smallText" valign="top">&nbsp;<b><?php echo '<a href="' . tep_href_link(FILENAME_IMAGES, 'action=Delete&images_id='.$images_id.'&image_page='.$image_page.'&group_id_view='.$group_id_view.'&image_group='.$image_group, 'NONSSL') . '">'; ?><?php echo tep_image_button('button_confirm.gif', IMAGE_CONFIRM); ?></a>&nbsp;&nbsp;<?php echo '<a href="' . tep_href_link(FILENAME_IMAGES, 'image_page='.$image_page.'&group_id_view='.$group_id_view.'&image_group='.$image_group, 'NONSSL') . '">'; ?><?php echo tep_image_button('button_cancel.gif', IMAGE_CANCEL); ?></a>&nbsp;</b></td>
<?php
    } else {
// DANIEL: basic browse table list
?>
            <td class="smallText" valign="top">&nbsp;<?php echo $images_values['images_id']; ?>&nbsp;</td>
            <td class="smallText" valign="top">&nbsp;<?php echo $images_values['name']; ?>&nbsp;</td>
            <td class="smallText" align="center" valign="top">&nbsp;<?php echo $images_values['images_image']; if ($group_id_view) echo '&nbsp;<br>&nbsp;' . tep_image(DIR_WS_CATALOG_IMAGES . $images_values['images_image'], $images_values['images_alt']?$images_values['images_alt']:$images_values['name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"'); ?>&nbsp;</td>
            <td class="smallText" valign="top">&nbsp;<?php echo htmlspecialchars($images_values['images_alt']); ?>&nbsp;</td>
            <td class="smallText" align="center" valign="top">&nbsp;<?php echo $images_values['sort_order']; ?>&nbsp;</td>
            <td align="center" class="smallText" valign="top">&nbsp;<?php echo '<a href="' . tep_href_link(FILENAME_IMAGES, 'action=update_image&images_id='.$images_values['images_id'].'&image_page='.$image_page.'&group_id_view='.$group_id_view.'&image_group='.$image_group, 'NONSSL') . '">'; ?><?php echo tep_image_button('button_edit.gif', IMAGE_UPDATE); ?></a>&nbsp;&nbsp;<?php echo '<a href="' . tep_href_link(FILENAME_IMAGES, 'action=delete_image&images_id='.$images_values['images_id'].'&image_page='.$image_page.'&group_id_view='.$group_id_view.'&image_group='.$image_group, 'NONSSL') , '">'; ?><?php echo tep_image_button('button_delete.gif', IMAGE_DELETE); ?></a>&nbsp;</td>
<?php
    }
    $max_images_id_query = tep_db_query("select max(images_id) + 1 as next_id from " . TABLE_IMAGES);
    $max_images_id_values = tep_db_fetch_array($max_images_id_query);
    $next_id = $max_images_id_values['next_id'];
?>
          </tr>
<?php
    $rows++;
  }
  if ($action != 'update_image') {
// DANIEL: bottom line with INSERT BUTTON
?>
          <tr>
            <td colspan="7"><?php echo tep_black_line(); ?></td>
          </tr>
          <tr class="<?php echo (floor($rows/2) == ($rows/2) ? 'attributes-even' : 'attributes-odd'); ?>">
            <td class="smallText" valign="top">&nbsp;<?php echo $next_id; ?>&nbsp;</td>
      	    <td class="smallText" valign="top">&nbsp;<select name="group_id">
<?php
    if (!is_numeric($group_id)) echo '<option name="blank" value="" SELECTED></option>';
    $group_query = tep_db_query($group_query_str);
    while ($group_values = tep_db_fetch_array($group_query)) {
        if ($group_id == $group_values['id']) {
              echo '<option name="' . $group_values['name'] . '" value="' . $group_values['id'] . '" SELECTED>' . $group_values['name'] . '</option>';
        } else {
              echo '<option name="' . $group_values['name'] . '" value="' . $group_values['id'] . '">' . $group_values['name'] . '</option>';
        }
    }
?>
            </select>&nbsp;</td>
            <td class="smallText" valign="top">
            <?php if (!$wysiwyg || HTML_AREA_WYSIWYG_DISABLE_IMAGE == 'Disable') echo '&nbsp;'.tep_draw_file_field('images_name').'&nbsp;';
                  else echo tep_draw_textarea_field('images_name', 'soft', '30', '1', '', '', false); ?>
            </td>
            <td class="smallText" valign="top">
<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
      if ($i) echo '<br>';
?>
            <table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
				<td class="main"><?php echo tep_draw_textarea_field('images_alt[' . $languages[$i]['id'] . ']', 'soft', '40', '3', '', 'onKeyUp="CountInput(this);"');
                echo "<br>".spellcount_link('images','images_alt[' . $languages[$i]['id'] . ']',0); ?></td>
              </tr>
            </table><?php
    }
?>
            </td>
            <td class="smallText" align="center" valign="top">&nbsp;<input type="text" name="sort_order" size="4" maxlength="4">&nbsp;</td>
            <td align="center" class="smallText" valign="top">&nbsp;&nbsp;<input type="submit" name="action" value="Insert">&nbsp;</td>
          </tr>
<?php
  }
?>
          <tr>
            <td colspan="7"><?php echo tep_black_line(); ?></td>
          </tr>
        </table>
<input type="hidden" name="image_page" value="<?php echo $image_page; ?>">
<input type="hidden" name="group_id_view" value="<?php echo $group_id_view; ?>">
<input type="hidden" name="image_group" value="<?php echo $image_group; ?>">
        </form>
<?php
//MaxiDVD Added WYSIWYG HTML Area Box + Admin Function v1.7 - 2.2 MS2 Products Description HTML - </form>
   if ($wysiwyg && HTML_AREA_WYSIWYG_DISABLE_IMAGE != 'Disable') { ?>
            <script language="JavaScript1.2" defer>
             var config = new Object();  // create new config object
             config.debug = <?php echo HTML_AREA_WYSIWYG_DEBUG; ?>;
             config.width  = "<?php echo IMAGE_EDITOR_WYSIWYG_WIDTH; ?>px";
             config.height = "<?php echo IMAGE_EDITOR_WYSIWYG_HEIGHT; ?>px";
             config.bodyStyle = 'background-color: white; font-family: Arial; color: black; font-size: 12px;';
             config.stylesheet = null;
             config.toolbar = [ ["InsertImageURL"] ];
             config.OscImageRoot = '<?php echo trim(HTTP_SERVER . DIR_WS_CATALOG_IMAGES); ?>';
             editor_generate('images_name',config);
            </script>
<?php } ?>
<FORM name="hidden_form" method="POST" action="spellcheck.php?init=yes" target="WIN">
<INPUT type="hidden" name="form_name" value="">
<INPUT type="hidden" name="field_name" value="">
<INPUT type="hidden" name="first_time_text" value="">
</FORM></td>
      </tr>
    </table></td>
<!-- images_eof //-->
  </tr>
</table>
<!-- body_text_eof //-->
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
