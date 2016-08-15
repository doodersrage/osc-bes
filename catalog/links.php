<?php
/*
  $Id: links.php,v 1.07 2005/5/28 Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

// define our link functions
  require(DIR_WS_FUNCTIONS . 'links.php');

  $linkSearch = '';
  $linkFound = false;
  $action_search = (isset($_POST['action_search']) ? $_POST['action_search'] : '');
  if (tep_not_null($action_search))
  {
     $linkSearch = $_POST['links_search'];
     $link_query = tep_db_query("select l.links_id, l.links_title, lc.links_id, lc.link_categories_id from " . TABLE_LINKS_DESCRIPTION . " l, " . TABLE_LINKS_TO_LINK_CATEGORIES . " lc where l.links_id = lc.links_id AND links_title LIKE '%" . $linkSearch . "%' and language_id = '" . (int)$languages_id . "'");
     if (tep_db_num_rows($link_query) > 0)
       $linkFound = true;
  } 
	  
// calculate link category path
  if (isset($HTTP_GET_VARS['lPath'])) {
    $lPath = $HTTP_GET_VARS['lPath'];
    $current_category_id = $lPath;
    $display_mode = 'links';
  } elseif (isset($HTTP_GET_VARS['links_id'])) {
    $lPath = tep_get_link_path($HTTP_GET_VARS['links_id']);
  } else {
    $lPath = '';
    $display_mode = 'categories';
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_LINKS);

  // links breadcrumb
  $link_categories_query = tep_db_query("select link_categories_name from " . TABLE_LINK_CATEGORIES_DESCRIPTION . " where link_categories_id = '" . (int)$lPath . "'");
  $link_categories_value = tep_db_fetch_array($link_categories_query);

  if ($display_mode == 'links') {
    $breadcrumb->add(NAVBAR_TITLE, FILENAME_LINKS);
    $breadcrumb->add($link_categories_value['link_categories_name'], FILENAME_LINKS . '?lPath=' . $lPath);
  } else {
    $breadcrumb->add(NAVBAR_TITLE, FILENAME_LINKS);
  }

  /****** DIsplay Feature Links ******/  
  if (LINKS_FEATURED_LINK == 'True')
  {
    /****** Find the Featured links for the main links page ******/
    $openMode = (LINKS_OPEN_NEW_PAGE == 'True') ? 'blank' : 'self';
    $link_featured = '';
    $link_featured_query = tep_db_query("select l.links_id, l.links_url, l.links_image_url, ld.links_title, ld.links_description, lf.expires_date from " . TABLE_LINKS . " l, " . TABLE_LINKS_DESCRIPTION . " ld, " . TABLE_LINKS_FEATURED . " lf where l.links_id = ld.links_id AND ld.links_id = lf.links_id AND lf.expires_date >= now() order by RAND()" );
   
    if (tep_db_num_rows($link_featured_query) > 0) {
      $lf = tep_db_fetch_array($link_featured_query);
      
      if (LINKS_TITLES_AS_LINKS == 'True')
        $link_featured = $lf['links_title'];
      else
        $link_featured = '<a href="' . tep_get_links_url($lf['links_id']) . '" target="_' . $openMode . '">' . $lf['links_title'] . '</a>';

      if (LINK_LIST_IMAGE > 0)
      { 
        if (tep_not_null($lf['links_image_url'])) { 
          $link_featured .= '<br><a href="' . tep_get_links_url($lf['links_id']) .  '" target="_' . $openMode . '">' . tep_links_image($lf['links_image_url'], $lf['links_title'], LINKS_IMAGE_WIDTH, LINKS_IMAGE_HEIGHT) . '</a>';
        } else {
          $link_featured .= '<br><a href="' . tep_get_links_url($lf['links_id']) .  '" target="_' . $openMode . '">' . tep_image(DIR_WS_IMAGES . 'pixel_trans.gif', $lf['links_title'], LINKS_IMAGE_WIDTH, LINKS_IMAGE_HEIGHT, 'style="border: 3px double black"') . '</a>';
        }
      } 
      if (LINK_LIST_DESCRIPTION > 0)
       $link_featured .= '<br>' . $lf['links_description'];
    }     
    
    /****** Find the Featured Links for the category page ******/
    $link_featured_cat = '';
    $link_featured_cat_query = tep_db_query("select l.links_id, l.links_url, l.links_image_url, ld.links_title, ld.links_description, lf.expires_date from " . TABLE_LINKS . " l, " . TABLE_LINKS_DESCRIPTION . " ld, " . TABLE_LINKS_FEATURED . " lf, " . TABLE_LINKS_TO_LINK_CATEGORIES . " l2lc where l.links_id = ld.links_id AND ld.links_id = lf.links_id AND l.links_id = l2lc.links_id AND lf.expires_date >= now() AND l2lc.link_categories_id = '" . (int)$current_category_id . "' order by RAND()" );
 
    if (tep_db_num_rows($link_featured_cat_query) > 0) {
      $lf = tep_db_fetch_array($link_featured_cat_query);
      
      if (LINKS_TITLES_AS_LINKS == 'True')
        $link_featured_cat = $lf['links_title'];
      else
        $link_featured_cat = '<a href="' . tep_get_links_url($lf['links_id']) . '" target="_' . $openMode . '">' . $lf['links_title'] . '</a>';

      if (LINK_LIST_IMAGE > 0)
      { 
        if (tep_not_null($lf['links_image_url'])) { 
          $link_featured_cat .= '<br><a href="' . tep_get_links_url($lf['links_id']) .  '" target="_' . $openMode . '">' . tep_links_image($lf['links_image_url'], $lf['links_title'], LINKS_IMAGE_WIDTH, LINKS_IMAGE_HEIGHT) . '</a>';
        } else {
          $link_featured_cat .= '<br><a href="' . tep_get_links_url($lf['links_id']) .  '" target="_' . $openMode . '">' . tep_image(DIR_WS_IMAGES . 'pixel_trans.gif', $lf['links_title'], LINKS_IMAGE_WIDTH, LINKS_IMAGE_HEIGHT, 'style="border: 3px double black"') . '</a>';
        }
      } 
      if (LINK_LIST_DESCRIPTION > 0)
       $link_featured_cat .= '<br>' . $lf['links_description'];
    } 
  }
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>"> 
<title><?php echo TITLE; ?></title>
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<link rel="stylesheet" type="text/css" href="stylesheet.css">
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
<?php
  if ($display_mode == 'categories') {
?>
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_image(DIR_WS_IMAGES . 'table_background_default.gif', HEADING_TITLE, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td><?php echo TEXT_MAIN_CATEGORIES; ?></td>
      </tr>       
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <?php if (tep_not_null($link_featured)) { ?>
      <tr>
       <td class="linkFeatured"><table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
         <td align="center"><?php echo TEXT_FEATURED_HEADING; ?></td>
        </tr>
        <tr>
         <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
        </tr>        
        <tr>
         <td align="center"><?php echo $link_featured; ?></td>
        </tr> 
       </table></td>
      </tr>  
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>                
      <?php } ?>    
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
<?php
    $categories_query = tep_db_query("select lc.link_categories_id, lcd.link_categories_name, lcd.link_categories_description, lc.link_categories_image from " . TABLE_LINK_CATEGORIES . " lc, " . TABLE_LINK_CATEGORIES_DESCRIPTION . " lcd where lc.link_categories_id = lcd.link_categories_id and lc.link_categories_status = '1' and lcd.language_id = '" . (int)$languages_id . "' order by lcd.link_categories_name");

    $number_of_categories = tep_db_num_rows($categories_query);

    if ($number_of_categories > 0) {
      $rows = 0;
      while ($categories = tep_db_fetch_array($categories_query)) {
        $rows++;
        $lPath_new = 'lPath=' . $categories['link_categories_id'];
        $width = (int)(100 / MAX_DISPLAY_CATEGORIES_PER_ROW) . '%';

        echo '                <td align="center" class="smallText" width="' . $width . '" valign="top"><a href="' . tep_href_link(FILENAME_LINKS, $lPath_new) . '">';
   
        if (SHOW_LINKS_CATEGORIES_IMAGE == 'True') {
          if (tep_not_null($categories['link_categories_image'])) {
            echo tep_links_image(DIR_WS_IMAGES . $categories['link_categories_image'], $categories['link_categories_name'], SUBCATEGORY_IMAGE_WIDTH, SUBCATEGORY_IMAGE_HEIGHT) . '<br>';
          } else {
            echo tep_image(DIR_WS_IMAGES . 'pixel_trans.gif', $categories['link_categories_name'], SUBCATEGORY_IMAGE_WIDTH, SUBCATEGORY_IMAGE_HEIGHT, 'style="border: 3px double black"') . '<br>';
          }
        }

        $categories_count_query = tep_db_query("select link_categories_id from " . TABLE_LINKS_TO_LINK_CATEGORIES . " where link_categories_id = " . $categories['link_categories_id']);
        $linkCount = tep_db_num_rows($categories_count_query);
        echo '<br><b><u>' . $categories['link_categories_name'] . '</b></u></a>&nbsp;' . '(' . $linkCount . ')<br>' . $categories['link_categories_description'] . '</td>' . "\n";
        if ((($rows / MAX_DISPLAY_CATEGORIES_PER_ROW) == floor($rows / MAX_DISPLAY_CATEGORIES_PER_ROW)) && ($rows != $number_of_categories)) {
          echo '              </tr>' . "\n";
          echo '              <tr>' . "\n";
        }
      }
    } else {
?>
                <td><?php new infoBox(array(array('text' => TEXT_NO_CATEGORIES))); ?></td>
<?php
    }
?>
              </tr>
            </table></td>
          </tr>          
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
          <tr>
            <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
              <tr class="infoBoxContents">
                <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                    <td align="right" align="center"><?php echo tep_draw_form('check_links', tep_href_link(FILENAME_LINKS, '', 'NONSSL'), 'post', 'onSubmit="return true;" onReset="return true"') . tep_draw_hidden_field('action_search', 'process'); ?>
                    Search <?php echo tep_draw_input_field('links_search', '', 'maxlength="255", size="30"', false) . tep_image_submit('button_quick_find.gif', SEARCH) ; ?> 
    	     	 	     </form></td>
                    <td class="main" align="right"><?php echo '<a href="' . tep_href_link(FILENAME_LINKS_SUBMIT, tep_get_all_get_params()) . '">' . tep_image_button('button_submit_link.gif', IMAGE_BUTTON_SUBMIT_LINK) . '</a>'; ?></td>
                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>          
          <tr>
           <td><?php include(DIR_WS_MODULES . FILENAME_LINK_SEARCH); ?></td>
          </tr>         
        </table></td>
      </tr>
    </table></td>
<?php
  } elseif ($display_mode == 'links') {
// create column list
    $define_list = array('LINK_LIST_TITLE' => LINK_LIST_TITLE,
                         'LINK_LIST_URL' => LINK_LIST_URL,
                         'LINK_LIST_IMAGE' => LINK_LIST_IMAGE,
                         'LINK_LIST_DESCRIPTION' => LINK_LIST_DESCRIPTION, 
                         'LINK_LIST_COUNT' => LINK_LIST_COUNT);

    asort($define_list);

    $column_list = array();
    reset($define_list);
    while (list($key, $value) = each($define_list)) {
      if ($value > 0) $column_list[] = $key;
    }

    $select_column_list = '';

    for ($i=0, $n=sizeof($column_list); $i<$n; $i++) {
      switch ($column_list[$i]) {
        case 'LINK_LIST_TITLE':
          $select_column_list .= 'ld.links_title, ';
          break;
        case 'LINK_LIST_URL':
          $select_column_list .= 'l.links_url, ';
          break;
        case 'LINK_LIST_IMAGE':
          $select_column_list .= 'l.links_image_url, ';
          break;
        case 'LINK_LIST_DESCRIPTION':
          $select_column_list .= 'ld.links_description, ';
          break;
        case 'LINK_LIST_COUNT':
          $select_column_list .= 'l.links_clicked, ';
          break;
      }
    }

// show the links in a given category
// We show them all

  $listing_sql = "select " . $select_column_list . " l.links_id, ld.language_id from " . TABLE_LINKS_DESCRIPTION . " ld, " . TABLE_LINKS . " l, " . TABLE_LINKS_TO_LINK_CATEGORIES . " l2lc where l.links_status = '2' and l.links_id = l2lc.links_id and ld.links_id = l2lc.links_id and l2lc.link_categories_id = '" . (int)$current_category_id . "'";
  $lang_set = false;
  
  $langsort_query = tep_db_query("select languages_id, sort_order from " . TABLE_LANGUAGES . " where sort_order = " . $languages_id . " limit 1");
  $langsort = tep_db_fetch_array($langsort_query);
     
  $useAll = false;
  
  if ($langsort['languages_id'] == '1')
  {
    if(LINKS_DISPLAY_ENGLISH == 'True')
    {
       $listing_sql .= " and ( ld.language_id = '" . $langsort['languages_id'] . "";
       $lang_set = true;
    }  
    else
    {
      $useAll = true;
    }
  }
  
  if ($langsort['languages_id'] == '2')
  {
    if(LINKS_DISPLAY_GERMAN == 'True')
    {
      if ($lang_set)
        $listing_sql .= "' xor ld.language_id = '" . $langsort['languages_id'] . "";
      else
      {
        $listing_sql .= " and ( ld.language_id = '" . $langsort['languages_id'] . "";
        $lang_set = true;
      }
    }
    else
    {
      $useAll = true;
    }
  }  
   
  if ($langsort['languages_id'] == '3')
  {
    if(LINKS_DISPLAY_SPANISH == 'True')
    {
      if ($lang_set)
        $listing_sql .= "' xor ld.language_id = '" . $langsort['languages_id'] . "";
      else
      {
        $listing_sql .= " and ( ld.language_id = '" . $langsort['languages_id'] . "";
        $lang_set = true;
      }
    }
    else
    {
      $useAll = true;
    }
  } 

  if ($langsort['languages_id'] == '4')
  {
    if (LINKS_DISPLAY_FRENCH == 'True')
    {
      if ($lang_set)
        $listing_sql .= "' xor ld.language_id = '" . $langsort['languages_id'] . "";
      else
      {
        $listing_sql .= " and ( ld.language_id = '" . $langsort['languages_id'] . "";
        $lang_set = true;
      }
    }
    else
    {
      $useAll = true;
    }
  }
       
  if ($lang_set)
    $listing_sql .= "' xor ld.language_id = '99')";
  else if ($useAll)
    $listing_sql .= " and ld.language_id = '99'";  
  else
    $listing_sql .= "";

    if ( (!isset($HTTP_GET_VARS['sort'])) || (!ereg('[1-8][ad]', $HTTP_GET_VARS['sort'])) || (substr($HTTP_GET_VARS['sort'], 0, 1) > sizeof($column_list)) ) {
      for ($i=0, $n=sizeof($column_list); $i<$n; $i++) {
        if ($column_list[$i] == 'LINK_LIST_TITLE') {
          $HTTP_GET_VARS['sort'] = $i+1 . 'a';
          $listing_sql .= " order by ld.links_title";
          break;
        }
      }
    } else {
      $sort_col = substr($HTTP_GET_VARS['sort'], 0 , 1);
      $sort_order = substr($HTTP_GET_VARS['sort'], 1);
      $listing_sql .= ' order by ';
      switch ($column_list[$sort_col-1]) {
        case 'LINK_LIST_TITLE':
          $listing_sql .= "ld.links_title " . ($sort_order == 'd' ? 'desc' : '');
          break;
        case 'LINK_LIST_URL':
          $listing_sql .= "l.links_url " . ($sort_order == 'd' ? 'desc' : '') . ", ld.links_title";
          break;
        case 'LINK_LIST_IMAGE':
          $listing_sql .= "ld.links_title";
          break;
        case 'LINK_LIST_DESCRIPTION':
          $listing_sql .= "ld.links_description " . ($sort_order == 'd' ? 'desc' : '') . ", ld.links_title";
          break;
        case 'LINK_LIST_COUNT':
          $listing_sql .= "l.links_clicked " . ($sort_order == 'd' ? 'desc' : '') . ", ld.links_title";
          break;
      }
    }
?>
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
<?php
// Get the right image for the top-right ;-)
    $image = 'table_background_list.gif';
    if ($current_category_id) {
      $image_query = tep_db_query("select link_categories_image from " . TABLE_LINK_CATEGORIES . " where link_categories_id = '" . (int)$current_category_id . "'");
      $image_value = tep_db_fetch_array($image_query);

      if (tep_not_null($image_value['link_categories_image'])) {
        $image = $image_value['link_categories_image'];
      }
    }
?>
            <td align="right"><?php echo tep_links_image(DIR_WS_IMAGES . $image, HEADING_TITLE, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td><?php echo sprintf(TEXT_MAIN_LINKS, $link_categories_value['link_categories_name']); ?></td>        
      </tr>      
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <?php if (tep_not_null($link_featured_cat)) { ?>
      <tr>
       <td class="linkFeatured"><table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
         <td align="center"><?php echo TEXT_FEATURED_HEADING; ?></td>
        </tr>
        <tr>
         <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
        </tr>        
        <tr>
         <td align="center"><?php echo $link_featured_cat; ?></td>
        </tr> 
       </table></td>
      </tr>  
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>                
      <?php } ?>         
      <tr>
        <td>
        <?php if (LINKS_DISPLAY_FORMAT_STANDARD == 'True')
          include(DIR_WS_MODULES . FILENAME_LINK_LISTING);
         else
          include(DIR_WS_MODULES . 'link_listing_vertical.php');
        ?>
        </td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
               <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td class="main" align="left"><?php echo '<a href="' . tep_href_link(FILENAME_LINKS, '') . '">' . tep_image_button('button_categories.gif', 'Link Categories') . '</a>'; ?></td>
                <td align="right" align="center"><?php echo tep_draw_form('check_links', tep_href_link(FILENAME_LINKS, 'lPath=1', 'NONSSL'), 'post', 'onSubmit="return true;" onReset="return true"') . tep_draw_hidden_field('action_search', 'process'); ?>
                Search <?php echo tep_draw_input_field('links_search', '', 'maxlength="255", size="30"', false) . tep_image_submit('button_quick_find.gif', SEARCH); ?> 
    	     	 	 </form></td>
                <td class="main" align="right"><?php echo '<a href="' . tep_href_link(FILENAME_LINKS_SUBMIT, tep_get_all_get_params()) . '">' . tep_image_button('button_submit_link.gif', IMAGE_BUTTON_SUBMIT_LINK) . '</a>'; ?></td>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
       <td><?php include(DIR_WS_MODULES . FILENAME_LINK_SEARCH); ?></td>
      </tr> 
    </table></td>
<?php
  }
?>
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
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
