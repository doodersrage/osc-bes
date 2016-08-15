<?php
/*
  $Id: dm_categories.php,v 1.00 2006/05/07 01:13:58 nate_02631 Exp $
	
  Ties the store category menu into the PHP Layers Menu library, allowing display
	of categories as DTHML drop-down or fly-out menus, collapsable tree-style menus
	or horizontal/vertical indented plain menus.

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 Nate Welch http://www.natewelch.com

  Released under the GNU General Public License
*/

// BEGIN Configuration Options

  // Set the value below corresponding to the type of menu you want to render
	// 0 = Horizontal Drop-down; 1 = Vertical Flyout; 2 = Tree Menu;
	// 3 = Plain Horizontal Menu; 4 = Plain Vertical Menu
	// Include the appropriate stylesheet in your store stylesheet, and if rendering
	// types '0' or '1', you must also echo (output) the "menu footer" variable
	// in your store footer as described in the readme (or submenus won't work)
	$menu_type = 1;
	
  // Set to false to display the menu output only. Set to true to display in
	// a regular box. The former is useful for better integrating the menu with your layout.
	$show_dmcats_as_box = true;				
	
  // Set to 'true' to assign TITLE tags to each of the menu's items, 'false' to leave blank
	$menu_use_titles = true;	
	
  // Name of the icon file to be used preceding menu items. Leave blank for no icons.
	// NOTE: Does not apply to plain style menus. Icon should be in the /images directory
	$menu_icon_file = '';
	
	// Width and height of icons used in menus (does not apply to plain menus).
	$menu_icon_width = 16;
	$menu_icon_height = 16;
	
  // Set the graphic to be used for the forward arrow and down arrow images used in 
	// drop-down and fly-out menus. Images must reside in your catalog's /images directory
	$menu_fwdarrowimg  = 'forward-arrow.png';		
  $menu_downarrowimg = 'down-arrow.png';		
	
	// Indicates whether or not to render your entire category list or just the root categories
	// and the currently selected submenu tree. Rendering the full list is useful for dynamic menu
	// generation where you want the user to have instant access to all categories. The other option
	// is the default oSC behaviour, when the subcats aren't available until the parent is clicked,
	// more suitable for plain-style menus 
	$show_full_tree = true;		
	
	// For tree menus, set to true to have only nodes corresponding to the current category path
	// expanded. If set to false, the tree menu will retain expanded/collapse nodes the user has
	// selected (as well as expanding any for categories they've entered)
	$menu_tree_current_path = true;				
	
  // Set the three numerical values below to adjust the offset of submenus in
  // horizontal drop-down and vertical fly-out menus. Values adjust the following (in order)
  // Top Offset: # of pixels from top border of previous menu the submenu appears
  // Right Offset: # of pixels from right border of previous menu the submenu appears
  // Left Offset: # of pixels from left border of previous menu the submenu appears
  // if the submenu pops to left (i.e. if window border is reached).  Negative values are allowed.
  $menu_layer_offset = array (0,4,4);	
	
	// Show icons on tree menus? If set to false, only expand/collapse icons and connecting lines are shown
	$GLOBALS['dm_tree_folder_icons'] = true;

	// This is the HTML that you would like to appear before/after your categories menu if *not*
	// displaying in a standard "box". This is useful for reconciling tables or clearing
	// floats, depending on your layout needs.	For example if not including in a box in the
	// default osC template, you would need opening/closing <tr><td> tags...
	$before_nobox_html = '';
  $after_nobox_html = '';
	
	// Use this option if you have a *lot* of subcategories in a DHTML style menu and your
	// submenus won't fit on the page.  Set $divide_subcats to the max # of subcategories you want
	// to display.  The menu will show a "more..." link and display the remaining subcategories
	// under that selection. Leave at "0" to not divide your subcategories.
	$divide_subcats = 0;	
	
	// The text you want to display to indicate more subcategories are available
	// This can be set a string or to a language constant you define.
	$divide_subcats_text = 'more...';	

// END Configuration Options


// Misc setting to make folder icon clickable to expand tree menu nodes
$GLOBALS['dm_tree_titleclick'] = true;	

// Initialize HTML and info_box class if displaying inside a box
if ($show_dmcats_as_box) {
    echo '<tr><td>';
    $info_box_contents = array();
    $info_box_contents[] = array('text' => BOX_HEADING_CATEGORIES);
    new infoBoxHeading($info_box_contents, true, false);					
}

// Generate the menu data output (uses configuration options above)
$categories_string = tep_make_cat_dmlist();

// Include required libraries based on menu type
require_once 'includes/functions/dynamenu/lib/PHPLIB.php';
require_once 'includes/functions/dynamenu/lib/layersmenu-common.inc.php';

if ($menu_type < 2) { // Setup for DHTML style menus

    ?>
        <script language="JavaScript" type="text/javascript">
            <!--
                <?php require_once 'includes/functions/dynamenu/libjs/layersmenu-browser_detection.js'; ?>
            // -->
        </script>
        <script language="JavaScript" type="text/javascript" src="includes/functions/dynamenu/libjs/layersmenu-library.js"></script>
        <script language="JavaScript" type="text/javascript" src="includes/functions/dynamenu/libjs/layersmenu.js"></script>
    <?php
		
    require_once 'includes/functions/dynamenu/lib/layersmenu.inc.php';
    $mid = new LayersMenu($menu_layer_offset[0],$menu_layer_offset[1],$menu_layer_offset[2],1);

} elseif ($menu_type > 2) { // Setup for plain style menus

    require_once 'includes/functions/dynamenu/lib/plainmenu.inc.php';
    $mid = new PlainMenu();

} else {  // Setup for tree style menus
		
		?>
        <script language="JavaScript" type="text/javascript">
            <!--
                <?php require_once 'includes/functions/dynamenu/libjs/layersmenu-browser_detection.js'; ?>
								
								<?php
								
								   if ($menu_tree_current_path) {
									     echo "\n".'var menu_tree_current_path = true';   		   
									 } else {
									     echo "\n".'var menu_tree_current_path = false'; 									 
									 }
								
								?>
        // -->
        </script>
        <script language="JavaScript" type="text/javascript" src="includes/functions/dynamenu/libjs/layerstreemenu-cookies.js"></script>
    <?php

        require_once 'includes/functions/dynamenu/lib/treemenu.inc.php';
        $mid = new TreeMenu();

}

// Set menu config variables
$mid->setDirroot('./');
$mid->setLibjsdir('./includes/functions/dynamenu/libjs/');

if ($menu_type !=2) {
    $mid->setTpldir('./includes/functions/dynamenu/templates/');
}
		
$mid->setImgdir('./images/');
$mid->setImgwww('images/');
$mid->setIcondir('./images/');
$mid->setIconwww('images/');
$mid->setIconsize($menu_icon_width, $menu_icon_height);

// Generate menus
$mid->setMenuStructureString($categories_string);
$mid->parseStructureForMenu('catmenu');

switch ($menu_type) {
    case 0: // Horizontal drop-down
        $mid->setDownArrowImg($menu_downarrowimg);
        $mid->setForwardArrowImg($menu_fwdarrowimg);
        $mid->setHorizontalMenuTpl('layersmenu-horizontal_menu.ihtml');						
        $mid->setSubMenuTpl('layersmenu-horiz_sub_menu.ihtml');							
			  $mid->newHorizontalMenu('catmenu');	
				$mid->printHeader();
        $categories_menu = $mid->getMenu('catmenu');
				$GLOBALS['dmfooter'] = $mid->getFooter();								
        break;
    case 1:  // Vertical fly-out
        $mid->setDownArrowImg($menu_downarrowimg);
        $mid->setForwardArrowImg($menu_fwdarrowimg);
        $mid->setVerticalMenuTpl('layersmenu-vertical_menu.ihtml');				
        $mid->setSubMenuTpl('layersmenu-vert_sub_menu.ihtml');							
				$mid->newVerticalMenu('catmenu');
				$mid->printHeader();
        $categories_menu = $mid->getMenu('catmenu');
				$GLOBALS['dmfooter'] = $mid->getFooter();												
        break;
    case 2:  // Tree menu
		    $categories_menu = $mid->newTreeMenu('catmenu');
        break;
    case 3:  // Horizontal plain menu
        $mid->setPlainMenuTpl('layersmenu-horizontal_plain_menu.ihtml');		
        $categories_menu = $mid->newHorizontalPlainMenu('catmenu');							
        break;
    case 4:  // Vertical plain menu
        $mid->setPlainMenuTpl('layersmenu-plain_menu.ihtml');		
        $categories_menu = $mid->newPlainMenu('catmenu');						
        break;	 	 
}	


// Output list inside a box if specified, otherwise just output unordered list
if ($show_dmcats_as_box) {
    $info_box_contents = array();
    $info_box_contents[] = array('text' => $categories_menu);
    new infoBox($info_box_contents);
		echo '</td></tr>';	
} else {
		echo $before_nobox_html;	
    echo $categories_menu;
		echo $after_nobox_html;
}

// Create the root category list
function tep_make_cat_dmlist($rootcatid = 0, $maxlevel = 0){

    global $cPath_array, $show_full_tree, $languages_id;
		
    global $idname_for_menu, $cPath_array, $show_full_tree, $languages_id;

    // Modify category query if not fetching all categories (limit to root cats and selected subcat tree)
		if (!$show_full_tree) {
        $parent_query	= 'AND (c.parent_id = "0"';	
				
				if (isset($cPath_array)) {
				
				    $cPath_array_temp = $cPath_array;
				
				    foreach($cPath_array_temp AS $key => $value) {
						    $parent_query	.= ' OR c.parent_id = "'.$value.'"';
						}
						
						unset($cPath_array_temp);
				}	
				
        $parent_query .= ')';				
		} else {
        $parent_query	= '';	
		}		

		$result = tep_db_query('select c.categories_id, cd.categories_name, c.parent_id from ' . TABLE_CATEGORIES . ' c, ' . TABLE_CATEGORIES_DESCRIPTION . ' cd where c.categories_id = cd.categories_id and cd.language_id="' . (int)$languages_id .'" '.$parent_query.'order by sort_order, cd.categories_name');
    
		while ($row = tep_db_fetch_array($result)) {				
        $table[$row['parent_id']][$row['categories_id']] = $row['categories_name'];
    }

    $output .= tep_make_cat_dmbranch($rootcatid, $table, 0, $maxlevel);

    return $output;
}

// Create the branches off the category list
function tep_make_cat_dmbranch($parcat, $table, $level, $maxlevel) {

    global $cPath_array, $menu_use_titles, $menu_icon_file, $divide_subcats, $divide_subcats_text;
		
		$lvl_adjust = 1;
		
    $list = $table[$parcat];
	
    // Build data for menu
		while(list($key,$val) = each($list)){
        
				if (isset($cPath_array) && in_array($key, $cPath_array)) {
            $this_expanded = '1';
            $this_selected = 'dmselected';						
        } else {
            $this_expanded = '';
            $this_selected = '';									
		    }	

        if (!$level) {
				    unset($GLOBALS['cPath_set']);
						$GLOBALS['cPath_set'][0] = $key;
            $cPath_new = 'cPath=' . $key;

        } else {
						$GLOBALS['cPath_set'][$level] = $key;		
            $cPath_new = 'cPath=' . implode("_", array_slice($GLOBALS['cPath_set'], 0, ($level+1)));
						
						$this_subcat_count++;
        }
				
				if ($menu_use_titles) {
				    $this_title = $val;
				} else {
				    $this_title = '';				
				}				

        if (SHOW_COUNTS == 'true') {
            $products_in_category = tep_count_products_in_category($key);
            if ($products_in_category > 0) {
                $val .= '&nbsp;(' . $products_in_category . ')';
            }
        }
				
				// Output for file to be parsed by PHP Layers Menu
				// Each line (terminated by a newline "\n" is a pipe delimited string with the following fields:
				// [dots]|[text]|[link]|[title]|[icon]|[target]|[expanded]
				// dots - number of dots signifies the level of the link '.' root level items, '..' first submenu, etc....
				// text - text for link; title - tooltip for link; icon - icon for link; target - "dmselected" CSS class if item is selected
				// expanded - signifies if the node is expanded or collapsed by default (applies only to tree style menus)
				
				// Add "more" submenu if dividing subcategories
				if ($this_subcat_count > $divide_subcats && $divide_subcats) {
            $output .= str_repeat(".", $level+$lvl_adjust).'|'.$divide_subcats_text.'||'.$this_title.'|'.$menu_icon_file.'|'.$this_selected.'|'.$this_expanded."\n";							 
				    $this_subcat_count = 1;
						$lvl_adjust ++;
				}
				
        $output .= str_repeat(".", $level+$lvl_adjust).'|'.$val.'|'.tep_href_link(FILENAME_DEFAULT, $cPath_new).'|'.$this_title.'|'.$menu_icon_file.'|'.$this_selected.'|'.$this_expanded."\n";							 
				
								
        if ((isset($table[$key])) AND (($maxlevel > $level + 1) OR ($maxlevel == '0'))) {
            $output .= tep_make_cat_dmbranch($key,$table,$level + $lvl_adjust,$maxlevel);
        }
    
		} // End while loop

    return $output;
}	

?>
