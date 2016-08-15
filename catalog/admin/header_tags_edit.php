<?php
/*
  $Id: header_tags_controller.php,v 1.0 2005/04/08 22:50:52 hpdl Exp $
  Originally Created by: Jack York - http://www.oscommerce-solution.com
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
 
  require('includes/application_top.php');
  require('includes/functions/header_tags.php');
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_HEADER_TAGS_CONTROLLER);

  if (!isset($HTTP_POST_VARS['lng_id'])) $lng_id = $languages_id;
  else $lng_id = $HTTP_POST_VARS['lng_id'];
  $lng_dir = '';
  
  $languages_array = array();
  $languages = tep_get_languages();
  $lng_exists = false;
  for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
    if ($languages[$i]['id'] == $lng_id) $lng_dir = $languages[$i]['directory'];

    $languages_array[] = array('id' => $languages[$i]['id'],
                               'text' => $languages[$i]['name']);
  }
  if (!$lng_dir) {
    $lng_dir = $language;
    $lng_id = $languages_id;
  }

  $filename = DIR_FS_CATALOG. DIR_WS_LANGUAGES . $lng_dir . '/header_tags.php';

  $formActive = false;
  
  /****************** READ IN FORM DATA ******************/
  $action = (isset($HTTP_POST_VARS['action']) ? $HTTP_POST_VARS['action'] : '');
  
  if (tep_not_null($action)) 
  {
      $main['title'] = $_POST['main_title'];  //read in the knowns
      $main['desc'] = $_POST['main_desc'];
      $main['keyword'] = $_POST['main_keyword'];
      $main['h1'] = $_POST['main_h1'];
 
      $formActive = true;
      $args_new = array();
      $c = 0;
      $pageCount = TotalPages($filename);
      for ($t = 0, $c = 0; $t < $pageCount; ++$t, $c += 6) //read in the unknowns
      {
         $args_new['title'][$t] = $_POST[$c];
         $args_new['desc'][$t] = $_POST[$c+1];
         $args_new['keyword'][$t] = $_POST[$c+2];
         $args_new['h1'][$t] = $_POST[$c+3];
         $args_new['surl'][$t] = trim($_POST[$c+4]);
         $args_new['surl_id'][$t] = $_POST[$c+5];        

         $boxID = sprintf("HTTA_%d", $t); 
         $args_new['HTTA'][$t] = $_POST[$boxID];
         $boxID = sprintf("HTDA_%d", $t); 
         $args_new['HTDA'][$t] = $_POST[$boxID];
         $boxID = sprintf("HTKA_%d", $t); 
         $args_new['HTKA'][$t] = $_POST[$boxID];
         $boxID = sprintf("HTCA_%d", $t); 
         $args_new['HTCA'][$t] = $_POST[$boxID];
      }   
  }
  
  /***************** READ IN DISK FILE ******************/
  $m_title = '';
  $m_desc = '';
  $m_keyword = '';
  $m_h1 = '';
  $sections = array();      //used for unknown titles
  $args = array();          //used for unknown titles
  $ctr = 0;                 //used for unknown titles
  $findTitles = false;      //used for unknown titles
  $fp = file($filename);

  for ($idx = 0; $idx < count($fp); ++$idx)
  { 
      if (strpos($fp[$idx], "define('HEAD_TITLE_TAG_ALL'") !== FALSE)
      {
          $m_title = GetMainArgument($fp[$idx], $m_title, $main['title'], $formActive);
      } 
      else if (strpos($fp[$idx], "define('HEAD_DESC_TAG_ALL'") !== FALSE)
      {
          $m_desc = GetMainArgument($fp[$idx], $m_desc, $main['desc'], $formActive);
      } 
      else if (strpos($fp[$idx], "define('HEAD_KEY_TAG_ALL'") !== FALSE)
      {
          $m_keyword = GetMainArgument($fp[$idx], $m_keyword, $main['keyword'], $formActive);             
      } 
      else if (strpos($fp[$idx], "define('HEAD_H1_TAG_ALL'") !== FALSE)
      {
          $m_h1 = GetMainArgument($fp[$idx], $m_h1, $main['h1'], $formActive);             
          $findTitles = true;  //enable next section            
      } 
      else if ($findTitles)
      {
          if (($pos = strpos($fp[$idx], '.php')) !== FALSE) //get the section titles
          {
              $sections['titles'][$ctr] = GetSectionName($fp[$idx]); 

              // added by splautz to update surl
              if ($formActive) {
                $surls_id = $args_new['surl_id'][$ctr];
                $surls_name = $args_new['surl'][$ctr];
                if (is_numeric($surls_id)) {
                  if ($surls_name) {
                    if (tep_check_dup_surl(false, $surls_id, tep_db_prepare_input($surls_name))) $surls_name = tep_get_script_surls_name($sections['titles'][$ctr] . '.php', $lng_id, $surls_id);
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
                  if (tep_check_dup_surl(false, '', tep_db_prepare_input($surls_name), $sections['titles'][$ctr] . '.php', '', $lng_id)) {  // dup exists
                    $surls_name = '';
                    $surls_id = NULL;
                  } else {  // dup not found, ok to insert
                    $sql_data_array = array('surls_name' => tep_db_prepare_input($surls_name),
                     'surls_script' => $sections['titles'][$ctr] . '.php',
                     'surls_param' => '',
                     'language_id' => $lng_id);
                    tep_db_perform(TABLE_SEO_URLS, $sql_data_array);
                    $surls_id = tep_db_insert_id();
                    $surls_updated = true;
                  }
                } else $surls_id = NULL;
              } else $surls_name = tep_get_script_surls_name($sections['titles'][$ctr] . '.php', $lng_id, $surls_id);
              $args['surl'][$ctr] = $surls_name;
              $args['surl_id'][$ctr] = ($surls_id === NULL)?'x':strval($surls_id);

              $ctr++; 
          }
          else                                   //get the rest of the items in this section
          {
              if (! IsComment($fp[$idx])) // && tep_not_null($fp[$idx]))
              {
                  $c = $ctr - 1;
                  if (IsTitleSwitch($fp[$idx]))
                  {
                     $args['title_switch'][$c] = GetSwitchSetting($fp[$idx]);
                     $args['title_switch_name'][$c] = sprintf("HTTA_%d",$c);                     
                     if ($formActive)
                     {
                       $fp[$idx] = ChangeSwitch($fp[$idx], $args_new['HTTA'][$c]);
                       $args['title_switch'][$c] = GetSwitchSetting($fp[$idx]);
                       $args['title_switch_name'][$c] = sprintf("HTTA_%d",$c); 
                     }                      
                  }
                  else if (IsDescriptionSwitch($fp[$idx]))
                  {
                     $args['desc_switch'][$c] = GetSwitchSetting($fp[$idx]);
                     $args['desc_switch_name'][$c] = sprintf("HTDA_%d",$c);  
                     if ($formActive)
                     {
                       $fp[$idx] = ChangeSwitch($fp[$idx], $args_new['HTDA'][$c]);
                       $args['desc_switch'][$c] = GetSwitchSetting($fp[$idx]);
                       $args['desc_switch_name'][$c] = sprintf("HTDA_%d",$c);  
                     } 
                  }
                  if (IsKeywordSwitch($fp[$idx]))
                  {
                     $args['keyword_switch'][$c] = GetSwitchSetting($fp[$idx]);
                     $args['keyword_switch_name'][$c] = sprintf("HTKA_%d",$c);
                     if ($formActive)
                     {
                       $fp[$idx] = ChangeSwitch($fp[$idx], $args_new['HTKA'][$c]);
                       $args['keyword_switch'][$c] = GetSwitchSetting($fp[$idx]);
                       $args['keyword_switch_name'][$c] = sprintf("HTKA_%d",$c);
                     }   
                  }
                  else if (IsCatSwitch($fp[$idx]))
                  {
                     $args['cat_switch'][$c] = GetSwitchSetting($fp[$idx]);
                     $args['cat_switch_name'][$c] = sprintf("HTCA_%d",$c);
                     if ($formActive) {
                       $fp[$idx] = ChangeSwitch($fp[$idx], $args_new['HTCA'][$c]);
                       $args['cat_switch'][$c] = GetSwitchSetting($fp[$idx]);
                       $args['cat_switch_name'][$c] = sprintf("HTCA_%d",$c);
                     }
                  }
                  else if (IsTitleTag($fp[$idx]))
                  {
                     $args['title'][$c] = GetArgument($fp[$idx], $args_new['title'][$c], $formActive);
                  } 
                  else if (IsDescriptionTag($fp[$idx])) 
                  {
                     $args['desc'][$c] = GetArgument($fp[$idx], $args_new['desc'][$c], $formActive);                   
                  }
                  else if (IsKeywordTag($fp[$idx])) 
                  {
                    $args['keyword'][$c] = GetArgument($fp[$idx], $args_new['keyword'][$c], $formActive);
                  }
				  else if (IsH1Tag($fp[$idx])) 
                  {
                    $args['h1'][$c] = GetArgument($fp[$idx], $args_new['h1'][$c], $formActive);
                  }      
              }
          }
      }
  }

  /***************** WRITE THE FILE ******************/
  if ($formActive)
  {      
     WriteHeaderTagsFile($filename, $fp);  
  }

// added by splautz for running external script after surls updates
  tep_surls_update(false);
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<style type="text/css">
td.HTC_Head {color: sienna; font-size: 24px; font-weight: bold; } 
td.HTC_subHead {color: sienna; font-size: 14px; } 
</style> 
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
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
     <tr>
      <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr><?php echo tep_draw_form('lng', FILENAME_HEADER_TAGS_EDIT, '', 'post'); ?>
          <td class="HTC_Head"><?php echo HEADING_TITLE_EDIT_TAGS; ?></td>
          <td align="right"><?php echo tep_draw_separator('pixel_trans.gif', '1', HEADING_IMAGE_HEIGHT); ?></td>
          <td align="right"><?php echo tep_draw_pull_down_menu('lng_id', $languages_array, $lng_id, 'onChange="this.form.submit();"'); ?></td>
        </form></tr>
	  </table></td>
     </tr>
     <tr>
      <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
     <tr>
      <td class="HTC_subHead"><?php echo TEXT_EDIT_TAGS; ?></td>
     </tr>
     <tr>
      <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
     </tr>
     
     <!-- Begin of Header Tags -->
     <tr>
      <td align="right"><?php echo tep_draw_form('header_tags', FILENAME_HEADER_TAGS_EDIT, '', 'post') . tep_draw_hidden_field('action', 'process') . tep_draw_hidden_field('lng_id', $lng_id); ?></td>
       <tr>
        <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
     
         <tr>
          <td class="smallText" width="20%" style="font-weight: bold;"><?php echo HEADING_TITLE_CONTROLLER_DEFAULT_TITLE; ?></td>
          <td class="smallText" ><?php echo tep_draw_input_field('main_title', tep_not_null($m_title) ? $m_title : '', 'maxlength="255", size="60"', false); ?> </td>
         <tr> 
         <tr>
          <td class="smallText" width="20%" style="font-weight: bold;"><?php echo HEADING_TITLE_CONTROLLER_DEFAULT_DESCRIPTION; ?></td>
          <td class="smallText" ><?php echo tep_draw_input_field('main_desc', tep_not_null($m_desc) ? $m_desc : '', 'maxlength="255", size="60"', false); ?> </td>
         <tr> 
         <tr>
          <td class="smallText" width="20%" style="font-weight: bold;"><?php echo HEADING_TITLE_CONTROLLER_DEFAULT_KEYWORDS; ?></td>
          <td class="smallText" ><?php echo tep_draw_input_field('main_keyword', tep_not_null($m_keyword) ? $m_keyword : '', 'maxlength="255", size="60"', false); ?> </td>
         <tr> 
         <tr>
          <td class="smallText" width="20%" style="font-weight: bold;"><?php echo HEADING_TITLE_CONTROLLER_DEFAULT_H1; ?></td>
          <td class="smallText" ><?php echo tep_draw_input_field('main_h1', tep_not_null($m_h1) ? $m_h1 : '', 'maxlength="255", size="60"', false); ?> </td>
         <tr> 
         
         <?php for ($i = 0, $id = 0; $i < count($sections['titles']); ++$i, $id += 6) { ?>
         <tr>
          <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
         </tr>
         
         <tr>
          <td colspan="3" ><table border="0" width="100%">
         <tr>
          <td colspan="3" class="smallText" width="18%" style="font-weight: bold;"><?php echo $sections['titles'][$i]; ?></td>
          <td class="smallText" width="4%">HTTA: </td>
          <td align="left" width="4%"><?php echo tep_draw_checkbox_field($args['title_switch_name'][$i], '', $args['title_switch'][$i], ''); ?> </td>
          <td class="smallText" width="4%">HTDA: </td>
          <td align="left" width="4%"><?php echo tep_draw_checkbox_field($args['desc_switch_name'][$i], '', $args['desc_switch'][$i], ''); ?> </td>
          <td class="smallText" width="4%">HTKA: </td>
          <td align="left" width="4%"><?php echo tep_draw_checkbox_field($args['keyword_switch_name'][$i], '', $args['keyword_switch'][$i], ''); ?> </td>
<?php if (in_array($sections['titles'][$i],array('index','product_info','pages'))) { ?>
          <td class="smallText" width="4%">HTCA: </td>
          <td align="left" width="4%"><?php echo tep_draw_checkbox_field($args['cat_switch_name'][$i], '', $args['cat_switch'][$i], ''); ?> </td>
<?php } else { ?>
          <td class="smallText" width="4%"></td>
          <td align="left" width="4%"><?php echo tep_draw_hidden_field($args['cat_switch_name'][$i], (int)$args['cat_switch'][$i]); ?> </td>
<?php } ?>

          <td width="50%" class="smallText"> <script>document.writeln('<a style="cursor:hand" onclick="javascript:popup=window.open('
                                           + '\'<?php echo tep_href_link('header_tags_popup_help.php'); ?>\',\'popup\','
                                           + '\'scrollbars,resizable,width=520,height=550,left=50,top=50\'); popup.focus(); return false;">'
                                           + '<font color="red"><u><?php echo HEADING_TITLE_CONTROLLER_EXPLAIN; ?></u></font></a>');
         </script> </td>
   
         </tr>
         </table></td>
         </tr>
         
         <tr>
          <td colspan="3" ><table border="0" width="100%">
           <tr>
            <td width="2%">&nbsp;</td>
            <td class="smallText" width="12%"><?php echo HEADING_TITLE_CONTROLLER_TITLE; ?></td>
            <td class="smallText" ><?php echo tep_draw_input_field($id, $args['title'][$i], 'maxlength="255", size="60"', false); ?> </td>
           </tr>
           <tr>
            <td width="2%">&nbsp;</td>
            <td class="smallText" width="12%"><?php echo HEADING_TITLE_CONTROLLER_DESCRIPTION; ?></td>
            <td class="smallText" ><?php echo tep_draw_input_field($id+1, $args['desc'][$i], 'maxlength="255", size="60"', false); ?> </td>
           </tr>
           <tr>
            <td width="2%">&nbsp;</td>
            <td class="smallText" width="12%"><?php echo HEADING_TITLE_CONTROLLER_KEYWORDS; ?></td>
            <td class="smallText" ><?php echo tep_draw_input_field($id+2, $args['keyword'][$i], 'maxlength="255", size="60"', false); ?> </td>
           </tr>
           <tr>
            <td width="2%">&nbsp;</td>
            <td class="smallText" width="12%"><?php echo HEADING_TITLE_CONTROLLER_H1; ?></td>
            <td class="smallText" ><?php echo tep_draw_input_field($id+3, $args['h1'][$i], 'maxlength="255", size="60"', false); ?> </td>
           </tr>
<?php if (!in_array($sections['titles'][$i],array('index','product_info','pages'))) { ?>
           <tr>
            <td width="2%">&nbsp;</td>
            <td class="smallText" width="12%"><?php echo HEADING_TITLE_CONTROLLER_SURL; ?></td>
            <td class="smallText" ><?php echo tep_draw_input_field($id+4, $args['surl'][$i], 'maxlength="255", size="60"', false, 'text', false);
			echo tep_draw_hidden_field($id+5, $args['surl_id'][$i]); ?> </td>
           </tr>
<?php } ?>
          </table></td>
         </tr>
         <?php } ?> 
        </table>
        </td>
       </tr>  
       <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
       </tr>
       <tr> 
        <td align="center"><?php echo (tep_image_submit('button_update.gif', IMAGE_UPDATE) ) . ' <a href="' . tep_href_link(FILENAME_HEADER_TAGS_EDIT, tep_get_all_get_params(array('action'))) .'">' . '</a>'; ?></td>
       </tr>
      </form>
      </td>
     </tr>
     <!-- end of Header Tags -->

         
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