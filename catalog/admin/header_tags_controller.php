<?php
/*
  $Id: header_tags_controller.php,v 1.2 2004/08/07 22:50:52 hpdl Exp $
  header_tags_controller Originally Created by: Jack York
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
 
  require('includes/application_top.php');
  require('includes/functions/header_tags.php');
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_HEADER_TAGS_CONTROLLER);
  $languages = tep_get_languages();

//  $filenameInc = DIR_FS_CATALOG . 'includes/header_tags.php';
  $filenameEng = DIR_FS_CATALOG . DIR_WS_LANGUAGES . $language . '/header_tags.php';

  $action       = (isset($HTTP_POST_VARS['action']) ? $HTTP_POST_VARS['action'] : '');
  $actionDelete = (isset($HTTP_POST_VARS['action_delete']) ? $HTTP_POST_VARS['action_delete'] : '');
  $actionCheck  = (isset($HTTP_POST_VARS['action_check']) ? $HTTP_POST_VARS['action_check'] : '');
  
  if (tep_not_null($action)) 
  {
    $pagename = $_POST['page'];
    if (($pos = strpos($pagename, ".php")) !== FALSE)  //remove .php from page 
       $pagename = substr($pagename, 0, $pos);     //if present

    if (ValidPageName($pagename)) {
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        $filenameLang = DIR_FS_CATALOG . DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/header_tags.php';
        $fp = file($filenameLang);

        $args = array('page'=>'','surl'=>'','title'=>'','desc'=>'','keyword'=>'','h1'=>'','htta'=>0,'htda'=>0,'htka'=>0,'htca'=>0);
        $args['page'] = $pagename;
        if ($languages[$i]['id'] == $languages_id) {
          $args['surl'] = trim($_POST['surl']);
          $args['title'] = $_POST['title'];
          $args['desc'] = $_POST['desc'];
          $args['keyword'] = $_POST['keyword'];
          $args['h1'] = $_POST['h1'];
        }
        $args['htta'] = ($_POST['htta'] == 'on') ? 1 : 0;
        $args['htda'] = ($_POST['htda'] == 'on') ? 1 : 0;
        $args['htka'] = ($_POST['htka'] == 'on') ? 1 : 0;
        $args['htca'] = ($_POST['htca'] == 'on') ? 1 : 0;
  
        $checkOnce = true;
        $lastSection = '';
//    updated by splautz to fix
//      $insertPoint = 0;
        $insertPoint = count($fp) - 1;

        $markPoint = count($fp) - 1; 
    
        if (NotDuplicatePage($fp, $args['page']))
        {
          for ($idx = 0; $idx < count($fp); ++$idx)  //find where to insert the new page
          {     
            if ($checkOnce && strpos($fp[$idx], "// DEFINE TAGS FOR INDIVIDUAL PAGES") === FALSE)
              continue;

            $checkOnce = false;   
            $section = GetSectionName($fp[$idx]);   

            if (! empty($section))
            {
              if (strcasecmp($section, $args['page']) < 0)
              {         
                $lastSection = $section;    
                $markPoint = $idx;       
              }   
              else if (strcasecmp($section, $args['page']) > 0)
              {
// updated by splautz to fix
//               if ($insertPoint == 0)
//                 $insertPoint = $idx;
                $insertPoint = $idx;
              }      
            }
          }
      
//        if ($insertPoint != count($fp))              //backup one line for appearance
//          $insertPoint--;

          // added by splautz to insert surl entry
          if ($args['surl']) {
            if (tep_check_dup_surl(false, '', tep_db_prepare_input($args['surl']), $args['page'] . '.php', '', $languages_id)) {  // dup exists
              $args['surl'] = '';
            } else {  // dup not found, ok to insert
              $sql_data_array = array('surls_name' => tep_db_prepare_input($args['surl']),
                'surls_script' => $args['page'] . '.php',
                'surls_param' => '',
                'language_id' => $languages_id);
              tep_db_perform(TABLE_SEO_URLS, $sql_data_array);
              $surls_updated = true;
            }
          }

          $fileUpper = strtoupper($args['page']);      //prepare the english array
          $engArray = array();
          $engArray['page'] = sprintf("// %s.php\n", $args['page']);  
          $engArray['htta'] = sprintf("define('HTTA_%s_ON','%d');\n", $fileUpper, $args['htta']);
          $engArray['htda'] = sprintf("define('HTDA_%s_ON','%d');\n", $fileUpper, $args['htda']);
          $engArray['htka'] = sprintf("define('HTKA_%s_ON','%d');\n", $fileUpper, $args['htka']);
          if (in_array($args['page'],array('index','product_info','pages'))) $engArray['htca'] = sprintf("define('HTCA_%s_ON','%d');\n", $fileUpper, $args['htca']);
          $engArray['title'] = sprintf("define('HEAD_TITLE_TAG_%s','%s');\n", $fileUpper, $args['title']);
          $engArray['desc'] = sprintf("define('HEAD_DESC_TAG_%s','%s');\n", $fileUpper, $args['desc']);
          $engArray['keyword'] = sprintf("define('HEAD_KEY_TAG_%s','%s');\n", $fileUpper, $args['keyword']);
          $engArray['h1'] = sprintf("define('HEAD_H1_TAG_%s','%s');\n\n", $fileUpper, $args['h1']);

          array_splice($fp, $insertPoint, 0, $engArray);
          WriteHeaderTagsFile($filenameLang, $fp);

      /*********************** INCLUDES SECTION ************************/     
/* // not needed anymore
      $fp = file($filenameInc); 
      $checkOnce = true;
      $insertPoint = 0;
      $markPoint = count($fp) - 1;
      $defaultPos = 0;
      
      for ($idx = 0; $idx < count($fp); ++$idx)  //find where to insert the new page
      {     
         if ($checkOnce && strpos($fp[$idx], "switch (true)") === FALSE)
            continue;
         $checkOnce = false;   
         $section = GetSectionName($fp[$idx]);   
         if (! empty($section))
         {
            if (strcasecmp($section, $args['page']) < 0)
            {         
               $lastSection = $section;    
               $markPoint = $idx;       
            }   
            else if (strcasecmp($section, $args['page']) > 0)
            {
               if ($insertPoint == 0)
                 $insertPoint = (int)$idx;
            }      
         }
         if (strpos($fp[$idx], "default:") !== FALSE)
           $defaultPos = $idx;
      }
      if ($insertPoint == 0)
        $insertPoint = (int)$defaultPos - 1;
      if ($insertPoint != count($fp))              //backup one line for appearance
        $insertPoint--;  
       
      $incArray = array();
      $fileUpper = strtoupper($args['page']);
      $spaces = 10;
      $incArray['page'] = sprintf("\n// %s.php\n", $args['page']);  
      $incArray['case'] = sprintf("  case (strstr(\$_SERVER['PHP_SELF'],FILENAME_%s) or strstr(\$PHP_SELF, FILENAME_%s));\n",$fileUpper, $fileUpper);
      $incArray['line'] = sprintf("    \$tags_array = tep_header_tag_page(HTTA_%s_ON, HEAD_TITLE_TAG_%s, \n%38sHTDA_%s_ON, HEAD_DESC_TAG_%s, \n%38sHTKA_%s_ON, HEAD_KEY_TAG_%s, \n%38sHEAD_H1_TAG_%s );\n   break;\n",$fileUpper, $fileUpper, " ", $fileUpper, $fileUpper, " ", $fileUpper, $fileUpper, " ", $fileUpper);  
   
      array_splice($fp, $insertPoint, 0, $incArray);  
      WriteHeaderTagsFile($filenameInc, $fp);  
*/
        }
        else
        {
          $error = HEADING_TITLE_CONTROLLER_PAGENAME_ERROR . $args['page'] . ' (' . $languages[$i]['name'] . ')';
          $messageStack->add($error);
        }
      }
    }
    else
    {
//      if (! ValidPageName($args['page']))
      $error = HEADING_TITLE_CONTROLLER_PAGENAME_INVALID_ERROR  . $pagename;
      $messageStack->add($error);
    }
  } 
  else if (tep_not_null($actionDelete))
  {
    /******************** Delete the English entries ********************/
    $page_to_delete = $_POST['delete_page'].'.php';

    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
      $filenameLang = DIR_FS_CATALOG . DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/header_tags.php';
      $fp = file($filenameLang);

      // added by splautz to delete surl entry
      tep_remove_script_surl($page_to_delete, $languages[$i]['id']);

      $found = false; 
      $delStart = 0;
      $delStop = 1;
      for ($idx = 0; $idx < count($fp); ++$idx)
      {
        if (! $found && strpos($fp[$idx], $page_to_delete) !== FALSE)
        {
          $delStart = $idx; // + 1;  //adjust for 0 start
          $found = true;
        }
        else if ($found) {
          if (strpos($fp[$idx], "?>") !== FALSE || strpos($fp[$idx], ".php") !== FALSE) break;
          else $delStop++;
        }
      }

      if ($found == true)          //page entry may not be present
      {
        if ($page_to_delete === 'index.php' || $page_to_delete === 'product_info.php')
        {
          $error = sprintf(HEADING_TITLE_CONTROLLER_NO_DELETE_ERROR, $page_to_delete);
          $messageStack->add($error);
        }
        else
        {
//        echo 'delete from '.$languages[$i]['name'].'  '.$delStart. ' for  '.$delStop.'<br>'; 
          array_splice($fp, $delStart, $delStop);
          WriteHeaderTagsFile($filenameLang, $fp);
        }  
      }
    }
    $surls_updated = true;

     /******************** Delete the includes entries *******************/
/* // not needed anymore
     $fp = file($filenameInc);
     $checkOnce = true;
     $found = false; 
     $delStart = 0;
     $delStop = 1;
     
     for ($idx = 0; $idx < count($fp); ++$idx)
     {
        if ($checkOnce && strpos($fp[$idx], "switch") === FALSE)
           continue;
        
        $checkOnce = false;
        if (! $found && (strpos($fp[$idx], $page_to_delete) !== FALSE || strpos($fp[$idx], strtoupper($page_to_delete))) !== FALSE)
        {
            $delStart = $idx; // + 1;  //adjust for 0 start
            $found = true;
        }
        else if ($found && ( strpos($fp[$idx], "ALL OTHER PAGES NOT DEFINED ABOVE") === FALSE && strpos($fp[$idx], ".php") === FALSE))
           $delStop++;
        else if ($found && (strpos($fp[$idx], "ALL OTHER PAGES NOT DEFINED ABOVE") !== FALSE || strpos($fp[$idx], ".php") !== FALSE))
            break;            
     }

     if ($found == true)          //page entry may not be present
     {
        if ($page_to_delete === 'product_info.php')
        {
          $error = sprintf(HEADING_TITLE_CONTROLLER_NO_DELETE_ERROR, $page_to_delete);
          $messageStack->add($error);
        }
        else
        {
          array_splice($fp, $delStart, $delStop);
          WriteHeaderTagsFile($filenameInc, $fp);
        }  
     }   
*/
  }
  else if (tep_not_null($actionCheck)) 
  {
     $filelist = array();
     $newfiles = array();
     $fp = file($filenameEng);
  
     for ($idx = 0; $idx < count($fp); ++$idx) 
     {
        $section = GetSectionName($fp[$idx]);
        if (empty($section) || strpos($section, "header_tags") !== FALSE || strpos($section, "WebMakers") !== FALSE)
           continue;
        $section .= '.php';
        $section = str_replace("-", "_", $section);  //ensure the scoring is the same
        $filelist[] = $section;
     }
 
     if ($handle = opendir(DIR_FS_CATALOG))
     {
        $fp = file($filenameEng); 
        $found = false;
        while (false !== ($file = readdir($handle))) 
        { 
           if (strpos($file, '.php') === FALSE)
              continue;       
 
           if (FileNotUsingHeaderTags($file))
           {
              foreach($filelist as $name) 
              {           
                 $tmp_file = str_replace("-", "_", $file);  //ensure the scoring is the same
                 if (strcasecmp($name, $tmp_file) === 0)
                 {
                    $found = true;
                    break;
                 }
              }   
              if (! $found)
                 $newfiles[] = array('id' => $file, 'text' => $file);
              else
                 $found = false;
           }
        }
        closedir($handle); 
     }
  }
  
  /******************** Update the Delete drop down *******************/
  $deleteArray = array();
  $fp = file($filenameEng);
  $checkOnce = true;
  for ($idx = 0; $idx < count($fp); ++$idx)
  {
     if ($checkOnce && strpos($fp[$idx], "// DEFINE TAGS FOR INDIVIDUAL PAGES") === FALSE)
        continue;
     $checkOnce = false;
     $l = GetSectionName($fp[$idx]);
     if (tep_not_null($l))
       $deleteArray[] = array('id' => $l, 'text' => $l);
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
      <td class="HTC_Head"><?php echo HEADING_TITLE_CONTROLLER; ?></td>
     </tr>
     <tr>
      <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
     <tr>
      <td class="HTC_subHead"><?php echo TEXT_PAGE_TAGS; ?></td>
     </tr>
     <tr>
      <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
     </tr>
 
     <!-- Begin of Header Tags - Add a Page -->
     <tr>
      <td><?php echo tep_black_line(); ?></td>
     </tr>
     <tr>
      <td class="main"><?php echo TEXT_INFORMATION_ADD_PAGE; ?></td>
     </tr>
     
     <tr>
      <td align="right"><?php echo tep_draw_form('header_tags', FILENAME_HEADER_TAGS_CONTROLLER, '', 'post') . tep_draw_hidden_field('action', 'process'); ?></td>
       <tr>
        <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
     
         <tr>
          <td><table border="0" width="100%">
           <tr>
            <td class="smallText" width="10%" style="font-weight: bold;"><?php echo HEADING_TITLE_CONTROLLER_PAGENAME; ?></td>
            <td class="smallText" ><?php echo tep_draw_input_field('page', '', 'maxlength="255", size="30"', false, 'text', false); ?> </td>
           <tr>             
          </table></td>
         </tr>

         <tr>
          <td><table border="0" width="100%">
           <tr>
            <td class="smallText" width="10%" style="font-weight: bold;"><?php echo HEADING_TITLE_CONTROLLER_SURL; ?></td>
            <td class="smallText" ><?php echo tep_draw_input_field('surl', '', 'maxlength="255", size="30"', false, 'text', false); ?> </td>
           <tr>             
          </table></td>
         </tr>
         
         <tr>
          <td><table border="0" width="100%">
           <tr>
            <td class="smallText" width="13%" style="font-weight: bold;">Switches:</td>
            <td class="smallText">HTTA: </td>
            <td align="left"><?php echo tep_draw_checkbox_field('htta', '', FALSE, ''); ?> </td>
            <td class="smallText">HTDA: </td>
            <td ><?php echo tep_draw_checkbox_field('htda', '', FALSE, ''); ?> </td>
            <td class="smallText">HTKA: </td>
            <td ><?php echo tep_draw_checkbox_field('htka', '', FALSE, ''); ?> </td>
            <td class="smallText">HTCA: </td>
            <td ><?php echo tep_draw_checkbox_field('htca', '', FALSE, ''); ?> </td>
            <td width="50%" class="smallText"> <script>document.writeln('<a style="cursor:hand" onclick="javascript:popup=window.open('
                                           + '\'<?php echo tep_href_link('header_tags_popup_help.php'); ?>\',\'popup\','
                                           + '\'scrollbars,resizable,width=520,height=550,left=50,top=50\'); popup.focus(); return false;">'
                                           + '<font color="red"><u><?php echo HEADING_TITLE_CONTROLLER_EXPLAIN; ?></u></font></a>');
            </script> </td>
           </tr>
          </table></td>
         </tr>
         
         <tr>
          <td><table border="0" width="100%">
           <tr>
            <td class="smallText" width="10%" style="font-weight: bold;"><?php echo HEADING_TITLE_CONTROLLER_TITLE; ?></td>
            <td class="smallText" ><?php echo tep_draw_input_field('title', '', 'maxlength="255", size="60"', false); ?> </td>
           <tr> 
           <tr>
            <td class="smallText" style="font-weight: bold;"><?php echo HEADING_TITLE_CONTROLLER_DESCRIPTION; ?></td>
            <td class="smallText" ><?php echo tep_draw_input_field('desc', '', 'maxlength="255", size="60"', false); ?> </td>
           <tr> 
           <tr>
            <td class="smallText" style="font-weight: bold;"><?php echo HEADING_TITLE_CONTROLLER_KEYWORDS; ?></td>
            <td class="smallText" ><?php echo tep_draw_input_field('keyword','', 'maxlength="255", size="60"', false); ?> </td>
           <tr>
           <tr>
            <td class="smallText" style="font-weight: bold;"><?php echo HEADING_TITLE_CONTROLLER_H1; ?></td>
            <td class="smallText" ><?php echo tep_draw_input_field('h1', '', 'maxlength="255", size="60"', false); ?> </td>
           <tr>
          </table></td>
         </tr>
         
       <tr> 
        <td align="center"><?php echo (tep_image_submit('button_insert.gif', IMAGE_INSERT) ) . ' <a href="' . tep_href_link(FILENAME_HEADER_TAGS_CONTROLLER, '') .'">' . '</a>'; ?></td>
       </tr>
       
       <tr>
        <td><?php echo tep_black_line(); ?></td>
       </tr>
       
      </form>
      </td>
     </tr>
     <!-- end of Header Tags - Add a Page-->
  		  
     <!-- Begin of Header Tags - Delete a Page -->
     <tr>
      <td><?php echo tep_black_line(); ?></td>
     </tr>
     <tr>
      <td class="main"><?php echo TEXT_INFORMATION_DELETE_PAGE; ?></td>
     </tr>     
     <tr>
      <td align="right"><?php echo tep_draw_form('header_tags_delete', FILENAME_HEADER_TAGS_CONTROLLER, '', 'post') . tep_draw_hidden_field('action_delete', 'process'); ?></td>
       <tr>
        <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
         <tr>
          <td><table border="0" width="100%">
           <tr>
            <td class="smallText" width="10%" style="font-weight: bold;"><?php echo HEADING_TITLE_CONTROLLER_PAGENAME; ?></td>
            <td align="left"><?php   echo tep_draw_pull_down_menu('delete_page', $deleteArray, '', '', false);?></td>
           <tr>             
          </table></td>
         </tr>        
       <tr> 
        <td align="center"><?php echo (tep_image_submit('button_delete.gif', IMAGE_DELETE) ) . ' <a href="' . tep_href_link(FILENAME_HEADER_TAGS_CONTROLLER, '') .'">' . '</a>'; ?></td>
       </tr>       
       <tr>
        <td><?php echo tep_black_line(); ?></td>
       </tr>      
      </form>
      </td>
     </tr>
     <!-- end of Header Tags - Delete a Page-->  
     
     <!-- Begin of Header Tags - Auto Add Pages -->
     <tr>
      <td><?php echo tep_black_line(); ?></td>
     </tr>
     <tr>
      <td class="main"><?php echo TEXT_INFORMATION_CHECK_PAGES; ?></td>
     </tr>     
     <tr>
      <td align="right"><?php echo tep_draw_form('header_tags_auto', FILENAME_HEADER_TAGS_CONTROLLER, '', 'post') . tep_draw_hidden_field('action_check', 'process'); ?></td>
       <tr>
        <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
          <td><table border="0" width="100%">
           <tr>
            <td class="smallText" width="10%" style="font-weight: bold;"><?php echo HEADING_TITLE_CONTROLLER_PAGENAME; ?></td>
            <td align="left"><?php   echo tep_draw_pull_down_menu('new_files', $newfiles, '', '', false);?></td>
           <tr>             
          </table></td>
         </tr>            
       <tr> 
        <td align="center"><?php echo (tep_image_submit('button_update.gif', IMAGE_UPDATE) ) . ' <a href="' . tep_href_link(FILENAME_HEADER_TAGS_CONTROLLER, '') .'">' . '</a>'; ?></td>
       </tr>       
       <tr>
        <td><?php echo tep_black_line(); ?></td>
       </tr>      
      </form>
      </td>
     </tr>
     <!-- end of Header Tags - Auto Add Pages-->  
	 
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