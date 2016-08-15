<?php
/*
$Id: sts_display_output.php,v 1.2 2004/02/05 05:57:12 jhtalk Exp $

osCommerce, Open Source E-Commerce Solutions
http://www.oscommerce.com

Copyright (c) 2003 osCommerce

Released under the GNU General Public License
*/

/* 

  Simple Template System (STS) - Copyright (c) 2004 Brian Gallagher - brian@diamondsea.com

*/

// Used for debugging, please don't change
$sts_version = "2.01";
$sts_osc_version = PROJECT_VERSION;
$sts_osc_version_required = "osCommerce 2.2-MS2";

// echo "<!-- Page layout by Simple Template System (STS) v$sts_version on $sts_osc_version - http://www.diamondsea.com/sts/ -->\n";

// Perform OSC version checking
if ($sts_osc_version != $sts_osc_version_required) {
  echo "STS was designed to work with OSC version [$sts_osc_version_required].  This is version [$sts_osc_version].\n";
}

$template['debug'] = $template['debug'].''; // Define as blank if not already defined

/////////////////////////////////////////////
// SELECT HOW TO DISPLAY THE OUTPUT
/////////////////////////////////////////////
$display_template_output = 1;
$display_normal_output = 0;
$display_debugging_output = 0;
$display_version_output = 0;

// Override if we need to show a pop-up window
// $scriptname = $_SERVER['PHP_SELF'];
$scriptname = $PHP_SELF;  // modified by splautz to work with se friendly url's
// Returns file name without path nor parameters
$scriptbasename = substr($scriptname, strrpos($scriptname, '/') + 1);
$scriptname1 = strstr($scriptname, "popup");
$scriptname2 = strstr($scriptname, "info_shopping_cart");
// If script name contains "popup" then turn off templates and display the normal output
// This is required to prevent display of standard page elements (header, footer, etc) from the template and allow javascript code to run properly
if ($scriptname1 != false || $scriptname2 != false) {
$display_normal_output = 1;
$display_template_output = 0;
}


/////////////////////////////////////////////
// Allow the ability to turn on/off settings from the URL
// Set values to 0 or 1 as needed
/////////////////////////////////////////////

// Allow Template output control from the URL
if ($HTTP_GET_VARS['sts_template'] != "") {
$display_template_output = $HTTP_GET_VARS['sts_template'];
}
 
// Allow Normal output control from the URL
if ($HTTP_GET_VARS['sts_normal'] != "") {
$display_normal_output = $HTTP_GET_VARS['sts_normal'];
}

// Allow Debugging control from the URL
if ($HTTP_GET_VARS['sts_debug'] != "") {
$display_debugging_output = $HTTP_GET_VARS['sts_debug'];
}

// Allow Version control from the URL
if ($HTTP_GET_VARS['sts_version'] != "") {
$display_version_output = $HTTP_GET_VARS['sts_version'];
}

// Print out version number if needed
if ($display_version_output == 1 or $display_debugging_output == 1) {
print "STS_VERSION=[$sts_version]\n";
print "OSC_VERSION=[$sts_osc_version]\n";
}

// Start with the default template
$sts_template_file = STS_DEFAULT_TEMPLATE;

if ($scriptbasename != "product_info.php" || $product_check['total'] > 0) {
  // See if there is a custom template file for the currently running script
  $sts_check_file = STS_TEMPLATE_DIR . $scriptbasename . ".html";
  if (file_exists($sts_check_file)) {
    // Use it
    $sts_template_file = $sts_check_file;
  } 
}

// Are we in the index.php script?  If so, what is our Category Path (cPath)?
if ($scriptbasename == "index.php") {
  // If no cPath defined, default to 0 (the home page)
  if ($cPath == "") {
	$sts_cpath = 0; 
  } else {
        $sts_cpath = $cPath;
  }

  while ($sts_cpath != "")
  {
    // Look for category-subcategory-specific template file like "index.php_1_17.html"
    $sts_check_file = STS_TEMPLATE_DIR . "index.php_$sts_cpath.html";

    if (file_exists($sts_check_file)) {
      // Use it
      $sts_template_file = $sts_check_file;
      break;
    } 
    $sts_cpath = substr($sts_cpath, 0, (strrpos($sts_cpath, "_")));
  }

}

// Open Template file and read into a variable
if (! file_exists($sts_template_file)) {
  echo "Template file doesn't exist: [$sts_template_file]";
} // else {
//   echo "<!-- Using Template File [$sts_template_file] -->\n";
// }

if (! $fh = fopen($sts_template_file, 'r')) {
echo "Can't open Template file: [$sts_template_file]";
}

$template_html = fread($fh, filesize($sts_template_file));
fclose($fh);


/////////////////////////////////////////////
////// if product_info.php load data
/////////////////////////////////////////////
if ($scriptbasename == 'product_info.php') {
  require(STS_PRODUCT_INFO);
}

/////////////////////////////////////////////
////// Capture <title> and <meta> tags
/////////////////////////////////////////////

// STS: ADD: Support for WebMakers.com's Header Tag Controller contribution
  // Capture the output
  require(STS_START_CAPTURE);

  // BOF: WebMakers.com Changed: Header Tag Controller v1.0
  // Replaced by header_tags.php
  if ( file_exists(DIR_WS_INCLUDES . 'header_tags.php') ) {
//  require(DIR_WS_FUNCTIONS . 'clean_html_comments.php');
//  require(DIR_WS_FUNCTIONS . 'header_tags.php');
    require(DIR_WS_INCLUDES . 'header_tags.php');
  } else {
    echo '<meta http-equiv="Content-Type" content="text/html; charset=' . CHARSET . '">' . "\n";
    echo '<title>' . TITLE . '</title>' . "\n";
  }
  // EOF: WebMakers.com Changed: Header Tag Controller v1.0

  $sts_block_name = 'headertags';
  require(STS_STOP_CAPTURE);

// STS: EOADD: Support for WebMakers.com's Header Tag Controller contribution

/////////////////////////////////////////////
////// Run any user code needed
/////////////////////////////////////////////
require(STS_USER_CODE);

/////////////////////////////////////////////
////// Set up template variables
/////////////////////////////////////////////

// added by splautz for price ranges
  $template['pranges'] = '';
  if ($category_depth == 'products') $search_cat = $current_category_id;
  if (isset($search_cat)) {
    $prange = isset($HTTP_GET_VARS['prange'])?$HTTP_GET_VARS['prange']:'';
    $categories_query = tep_db_query("select c.categories_pranges, cd.categories_name from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = '" . (int)$search_cat . "' and cd.categories_id = '" . (int)$search_cat . "' and cd.language_id = '" . (int)$languages_id . "'");
    if (tep_db_num_rows($categories_query) > 0) {
      $categories = tep_db_fetch_array($categories_query);
      $pranges = preg_split("/\D+/",$categories['categories_pranges'],-1,PREG_SPLIT_NO_EMPTY);
    }
    if (isset($pranges) && sizeof($pranges)) {
      $dranges = array();
      sort($pranges);
      for($i=0,$ic=sizeof($pranges); $i<$ic; $i++) {
        if ($i) $dranges[] = array('id' => $pranges[$i-1].'-'.$pranges[$i], 'text' => '$'.$pranges[$i-1].' - $'.$pranges[$i]);
        else $dranges[] = array('id' => '-'.$pranges[$i], 'text' => '$'.$pranges[$i].' & Under');
      }
      $dranges[] = array('id' => $pranges[$i-1].'-', 'text' => '$'.$pranges[$i-1].' & Over');
      $sts_block_name = 'pranges';
      require(STS_START_CAPTURE);
      echo "\n<!-- Start Search by Price Range -->\n";
      $pranges_string = '<div id="pranges">'.sprintf(TEXT_PRANGES, $categories['categories_name']) . '<br><table border="0" cellpadding="5" cellspacing="0"><tr><td class="smallText">';
      for($i=0,$ic=sizeof($dranges); $i<$ic; $i++) {
        if ($prange == $dranges[$i]['id']) $dranges[$i]['text'] = '<b>'.$dranges[$i]['text'].'</b>';
        $pranges_string .= '<a href="'.tep_href_link(FILENAME_ADVANCED_SEARCH_RESULT, 'categories_id='.$search_cat.'&prange='.$dranges[$i]['id'], 'NONSSL', true, false).'" class="link">'.$dranges[$i]['text'].'</a><br>';
      }
	  $pranges_string .= "</td></tr></table></div>\n";
      $info_box_contents = array();
      $info_box_contents[] = array('text' => BOX_HEADING_PRANGES);
      new infoBoxHeading($info_box_contents, true, false);
      $info_box_contents = array();
      $info_box_contents[] = array('text' => $pranges_string);
      new infoBox($info_box_contents);
      echo "<!-- End Search by Price Range -->\n";
      require(STS_STOP_CAPTURE);
      $template['pranges'] = $sts_block['pranges'];
    }
  }

  $template['sid'] =  tep_session_name() . '=' . tep_session_id();

  // Strip out <title> variable
  $template['title'] = str_between($sts_block['headertags'], "<title>", "</title>");

  // Load up the <head> content that we need to link up everything correctly.  Append to anything that may have been set in sts_user_code.php
  // $template['headcontent'] = $template['headcontent'].'<meta http-equiv="Content-Type" content="text/html; charset=' . CHARSET . '">' . "\n"; 
  $template['headcontent'] = $sts_block['headertags'];
  $template['headcontent'] .= '<base href="' . (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG . '">' . "\n";
  // $template['headcontent'] = $template['headcontent'].'<link rel="stylesheet" type="text/css" href="stylesheet.css">' . "\n";
  $template['headcontent'] .= get_javascript($sts_block['applicationtop2header'],'get_javascript(applicationtop2header)');
  $template['headcontent'] .= $lng->link_string;

  // Note: These values lifted from the stock /catalog/includes/header.php script's HTML
  // catalogurl: url to catalog's home page
  // catalog: link to catalog's home page
  $template['urlcataloglogo'] = tep_href_link(FILENAME_DEFAULT);

  $template['myaccountlogo'] = '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . tep_image(DIR_WS_IMAGES . 'header_account.gif', HEADER_TITLE_MY_ACCOUNT) . '</a>';
  $template['urlmyaccountlogo'] = tep_href_link(FILENAME_ACCOUNT, '', 'SSL');

  $template['cartlogo'] = '<a href="' . tep_href_link(FILENAME_SHOPPING_CART) . '">' . tep_image(DIR_WS_IMAGES . 'header_cart.gif', HEADER_TITLE_CART_CONTENTS) . '</a>';
  $template['urlcartlogo'] = tep_href_link(FILENAME_SHOPPING_CART);

  $template['checkoutlogo'] = '<a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL') . '">' . tep_image(DIR_WS_IMAGES . 'header_checkout.gif', HEADER_TITLE_CHECKOUT) . '</a>';
  $template['urlcheckoutlogo'] = tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL');

  $template['breadcrumbs'] = $breadcrumb->trail(' &raquo; ');

  if (($bcnt=count($breadcrumb->_trail)-2) >= 0)
    $template['back'] = '<a href="' . $breadcrumb->_trail[$bcnt]['link'] . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>';
  else $template['back'] = '';

  if (tep_session_is_registered('customer_id')) {
    $template['myaccount'] = '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '" class="headerNavigation">' . HEADER_TITLE_MY_ACCOUNT . '</a>';
    $template['urlmyaccount'] = tep_href_link(FILENAME_ACCOUNT, '', 'SSL');
    $template['logoff'] = '<a href="' . tep_href_link(FILENAME_LOGOFF, '', 'SSL')  . '" class="headerNavigation">' . HEADER_TITLE_LOGOFF . '</a>';
    $template['urllogoff'] = tep_href_link(FILENAME_LOGOFF, '', 'SSL');
    $template['myaccountlogoff'] = $template['myaccount'] . " | " . $template['logoff'];
  } else {
    $template['myaccount'] = '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '" class="headerNavigation">' . HEADER_TITLE_MY_ACCOUNT . '</a>';
    $template['urlmyaccount'] = tep_href_link(FILENAME_ACCOUNT, '', 'SSL');
    $template['logoff'] = '';
    $template['urllogoff'] = '';
    $template['myaccountlogoff'] = $template['myaccount'];
  }

  $template['cartcontents']    = '<a href="' . tep_href_link(FILENAME_SHOPPING_CART) . '" class="headerNavigation">' . HEADER_TITLE_CART_CONTENTS . '</a>';
  $template['urlcartcontents'] = tep_href_link(FILENAME_SHOPPING_CART);

  $template['checkout'] = '<a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL') . '" class="headerNavigation">' . HEADER_TITLE_CHECKOUT . '</a>';
  $template['urlcheckout'] = tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL');
// Wishlist
  $template['wishlistcontents'] = '<a href="' . tep_href_link(FILENAME_WISHLIST) . '" class="headerNavigation">' . HEADER_TITLE_WISHLIST_CONTENTS . '</a>';
  $template['urlwishlistcontents'] = tep_href_link(FILENAME_WISHLIST);


/////////////////////////////////////////////
////// Create custom boxes
/////////////////////////////////////////////
//added for dhtml menu
  $template['dhmenu'] = strip_unwanted_tags($sts_block['dhmenu'], 'dhmenu');

  $template['categorybox'] = strip_unwanted_tags($sts_block['categorybox'], 'categorybox');
  $template['manufacturerbox'] = strip_unwanted_tags($sts_block['manufacturerbox'], 'manufacturerbox');
  $template['whatsnewbox'] = strip_unwanted_tags($sts_block['whatsnewbox'], 'whatsnewbox');
  $template['searchbox'] = strip_unwanted_tags($sts_block['searchbox'], 'searchbox');
  $template['informationbox'] = strip_unwanted_tags($sts_block['informationbox'], 'informationbox');
  $template['cartbox'] = strip_unwanted_tags($sts_block['cartbox'], 'cartbox');
  $template['maninfobox'] = strip_unwanted_tags($sts_block['maninfobox'], 'maninfobox');
  $template['orderhistorybox'] = strip_unwanted_tags($sts_block['orderhistorybox'], 'orderhistorybox');
  $template['bestsellersbox'] = strip_unwanted_tags($sts_block['bestsellersbox'], 'bestsellersbox');
  $template['specialfriendbox'] = strip_unwanted_tags($sts_block['specialfriendbox'], 'specialfriendbox');
  $template['reviewsbox'] = strip_unwanted_tags($sts_block['reviewsbox'], 'reviewsbox');
  $template['languagebox'] = strip_unwanted_tags($sts_block['languagebox'], 'languagebox');
  $template['currenciesbox'] = strip_unwanted_tags($sts_block['currenciesbox'], 'currenciesbox');
// Wishlist
  $template['wishlistbox'] = strip_unwanted_tags($sts_block['wishlistbox'], 'wishlistbox');
  $template['testimonialsbox'] = strip_unwanted_tags($sts_block['testimonialsbox'], 'testimonialsbox');
  $template['content'] = strip_content_tags($sts_block['columnleft2columnright'], 'content');
  $template['headermsgs'] = $sts_block['headermsgstack'] . $sts_block['headermsg'];
// Prepend any header msgs to content (comment out if desired)
  $template['content'] = $template['headermsgs'] . $template['content'];

  $template['date'] = strftime(DATE_FORMAT_LONG);
  $template['year'] = date('Y');
  $template['numrequests'] = $counter_now . ' ' . FOOTER_TEXT_REQUESTS_SINCE . ' ' . $counter_startdate_formatted;
  $template['counter'] = $sts_block['counter'];
  $template['footer'] = $sts_block['footer'];
  $template['banner'] = $sts_block['banner'];

/* Removed by splautz for performance increase
Use $FILENAME_PAGES?pages_id=x where x is the page ID for pages which are not defined as a forward.  For forwarded pages use the FILENAME
constant of the destination page as defined in includes/filenames.php.
// information pages
    $page_query = tep_db_query("SELECT pd.pages_name, p.pages_status, p.pages_id, p.pages_forward from " . TABLE_PAGES . " p, " . TABLE_PAGES_DESCRIPTION . " pd WHERE p.pages_id = pd.pages_id and p.pages_id > 1 and pd.language_id = '" . (int)$languages_id . "'");
    while($page = tep_db_fetch_array($page_query)){
      if ($page['pages_status'] == '1') {
        if(tep_not_null($page["pages_forward"])) {
          $page_forward = explode('?', $page["pages_forward"]);
          $template['infourl' . $page['pages_id']] = tep_href_link($page_forward[0],isset($page_forward[1])?$page_forward[1]:'');
        } else $template['infourl' . $page['pages_id']] = tep_href_link(FILENAME_PAGES, 'pages_id='.$page['pages_id']);
        $template['infoname' . $page['pages_id']] = $page['pages_name'];
      } else {
        $template['infourl' . $page['pages_id']] =  '';
        $template['infoname' . $page['pages_id']] = '';
      }
    }
*/

/* Removed by splautz for performance increase
Use $FILENAME_DEFAULT?cPath=x where x is the category ID.
/////////////////////////////////////////////
////// Get Categories
/////////////////////////////////////////////
$get_categories_description_query = tep_db_query("SELECT categories_id, categories_name FROM " . TABLE_CATEGORIES_DESCRIPTION);
// Loop through each category (in each language) and create template variables for each name and path
while ($categories_description = tep_db_fetch_array($get_categories_description_query)) {
      $cPath_new = tep_get_path($categories_description['categories_id']);
      $path = substr($cPath_new, 6); // Strip off the "cPath=" from string

      $catname = $categories_description['categories_name'];
      $catname = str_replace(" ", "_", $catname); // Replace Spaces in Category Name with Underscores

      $template["cat_" . $catname] = tep_href_link(FILENAME_DEFAULT, $cPath_new);
      $template["urlcat_" . $catname] = tep_href_link(FILENAME_DEFAULT, $cPath_new);
      $template["cat_" . $path] = tep_href_link(FILENAME_DEFAULT, $cPath_new);
      $template["urlcat_" . $path] = tep_href_link(FILENAME_DEFAULT, $cPath_new);

      // print "<b>template[" . $categories_description['categories_name'] . "]=" . $template[$categories_description['categories_name']] . "<br>template[" . $path . "]=" . $template[$path] . "</b>";
}
*/

/////////////////////////////////////////////
////// Display Template HTML
/////////////////////////////////////////////

// First pass
  // Sort array by string length, so that longer strings are replaced first
  uksort($template, "sortbykeylength");

  // Manually replace the <!--$headcontent--> if present
  $template_html = str_replace('<!--$headcontent-->', $template['headcontent'], $template_html);

  // Automatically replace all the other template variables
  foreach ($template as $key=>$value) {
    $template_html = str_replace('$' . $key, $value, $template_html);
  }

// Second pass
  // Automatically replace any nested template variables
  foreach ($template as $key=>$value) {
    $template_html = str_replace('$' . $key, $value, $template_html);
  }

// Third pass
  $template2 = array();
  // Scan for any filename variables found (only required on final pass since URL's don't contain template vars)
  if (preg_match_all('/\$(filename_\w+)(?:\?([\w\=\&\-]*))?/is',$template_html,$matches,PREG_SET_ORDER)) {
    foreach($matches as $match)
      if (defined(strtoupper($match[1])) && !isset($template2[substr($match[0],1)])) $template2[substr($match[0],1)] = tep_href_link(constant(strtoupper($match[1])),isset($match[2])?$match[2]:'');
  }
  // Sort array by string length, so that longer strings are replaced first
  uksort($template2, "sortbykeylength");

  // Automatically replace any FILENAME template variables
  foreach ($template2 as $key=>$value) {
    $template_html = str_replace('$' . $key, $value, $template_html);
  }

  $template = array_merge($template, $template2);

  if ($display_template_output == 1) {
    echo $template_html;
  }


/////////////////////////////////////////////
////// Display HTML
/////////////////////////////////////////////
 if ($display_normal_output == 1) {
  echo $sts_block['applicationtop2header'];
  echo $sts_block['headermsgstack'];
  echo $sts_block['header'];
  echo $sts_block['headermsg'];

  echo $sts_block['header2columnleft'];

  // print column_left stuff
  echo $sts_block['categorybox'];
  echo $sts_block['manufacturerbox'];
  echo $sts_block['whatsnewbox'];
  echo $sts_block['searchbox'];
  echo $sts_block['informationbox'];

  echo $sts_block['columnleft2columnright'];

  // print column_right stuff
  echo $sts_block['cartbox'];
  echo $sts_block['maninfobox'];
  echo $sts_block['orderhistorybox'];
  echo $sts_block['bestsellersbox'];
  echo $sts_block['specialfriendbox'];
  echo $sts_block['reviewsbox'];
  echo $sts_block['languagebox'];
  echo $sts_block['currenciesbox'];

  echo $sts_block['columnright2footer'];

  // print footer
  echo $sts_block['content'];
  echo $sts_block['counter'];
  echo $sts_block['footer'];
  echo $sts_block['banner'];
 }
/////////////////////////////////////////////
////// End Display HTML
/////////////////////////////////////////////

 if ($display_debugging_output == 1) {
  // Print Debugging Info
  print "\n<pre><hr>\n";
  print "STS_VERSION=[" . $sts_version . "]<br>\n";
  print "OSC_VERSION=[$sts_osc_version]\n";
  print "STS_TEMPLATE=[" . $sts_template_file . "]<hr>\n";
  // Replace $variable names in $sts_block_html_* with variables from the $template array
  foreach ($sts_block as $key=>$value) {
    print "<b>\$sts_block['$key']</b><hr>" . htmlspecialchars($value) . "<hr>\n";
  }

  foreach ($template as $key=>$value) {
    print "<b>\$template['$key']</b><hr>" . htmlspecialchars($value) . "<hr>\n";
  }

 }

 if ($display_normal_output == 1) {
  echo $sts_block['footer2applicationbottom'];
 }

// STRIP_UNWANTED_TAGS() - Remove leading and trailing <tr><td> from strings
function strip_unwanted_tags($tmpstr, $commentlabel) {
  // Now lets remove the <tr><td> that the require puts in front of the tableBox
  $tablestart = strpos($tmpstr, "<table");
  $tdstart = strpos($tmpstr, "<td>");
  // If empty, return nothing
  if ($tablestart === false && $tdstart === false) {
  	return  "\n<!-- start $commentlabel //-->\n$tmpstr\n<!-- end $commentlabel //-->\n";
  }
  if ($tablestart !== false && ($tdstart === false || $tablestart < $tdstart)) {
    $tablefirst = true;
    $tmpstr = substr($tmpstr, $tablestart); // strip off stuff before <table>
  } else {
    $tablefirst = false;
    $tmpstr = substr($tmpstr, $tdstart+4); // strip off stuff before and including <td>
  }

  // Now lets remove the </td></tr> at the end of the tableBox output
  // strrpos only works for chars, not strings, so we'll cheat and reverse the string and then use strpos
  $tmpstr = strrev($tmpstr);

  if ($tablefirst) {
    $tableend = strpos($tmpstr, strrev("</table>"), 1);
    $tmpstr = substr($tmpstr, $tableend);  // strip off stuff after </table>
  } else {
    $tdend = strpos($tmpstr, strrev("</td>"), 1);
    $tmpstr = substr($tmpstr, $tdend+5);  // strip off stuff after and including </td>
  }

  // Now let's un-reverse it
  $tmpstr = strrev($tmpstr);

  // print "<hr>After cleaning tmpstr:" . strlen($tmpstr) . ": FULL=[".  htmlspecialchars($tmpstr) . "]<hr>\n";
  return  "\n<!-- start $commentlabel //-->\n$tmpstr\n<!-- end $commentlabel //-->\n";
}


// STRIP_CONTENT_TAGS() - Remove text before "body_text" and after "body_text_eof"
function strip_content_tags($tmpstr, $commentlabel) {
  // Now lets remove the <tr><td> that the require puts in front of the tableBox
  $tablestart = strpos($tmpstr, "<table");
  $formstart = strpos($tmpstr, "<form");

  // If there is a <form> tag before the <table> tag, keep it
  if ($formstart !== false and $formstart < $tablestart) {
     $tablestart = $formstart;
     $formfirst = true;
  }

  // If empty, return nothing
  if ($tablestart < 1) {
        return  "\n<!-- start $commentlabel //-->\n$tmpstr\n<!-- end $commentlabel //-->\n";
  }
  
  $tmpstr = substr($tmpstr, $tablestart); // strip off stuff before <table>

  // Now lets remove the </td></tr> at the end of the tableBox output
  // strrpos only works for chars, not strings, so we'll cheat and reverse the string and then use strpos
  $tmpstr = strrev($tmpstr);

  if ($formfirst == true) {
    $tableend = strpos($tmpstr, strrev("</form>"), 1);
  } else {
    $tableend = strpos($tmpstr, strrev("</table>"), 1);
  } 

  $tmpstr = substr($tmpstr, $tableend);  // strip off stuff after <!-- body_text_eof //-->

  // Now let's un-reverse it
  $tmpstr = strrev($tmpstr);

  // print "<hr>After cleaning tmpstr:" . strlen($tmpstr) . ": FULL=[".  htmlspecialchars($tmpstr) . "]<hr>\n";
  return  "\n<!-- start $commentlabel //-->\n$tmpstr\n<!-- end $commentlabel //-->\n";
}


function get_javascript($tmpstr, $commentlabel) {
  // Now lets remove the <tr><td> that the require puts in front of the tableBox
  $tablestart = strpos($tmpstr, "<script");

  // If empty, return nothing
  if ($tablestart === false) {
// 	return  "\n<!-- start $commentlabel //-->\n\n<!-- end $commentlabel //-->\n";
    return '';
  }

  $tmpstr = substr($tmpstr, $tablestart); // strip off stuff before <table>

  // Now lets remove the </td></tr> at the end of the tableBox output
  // strrpos only works for chars, not strings, so we'll cheat and reverse the string and then use strpos
  $tmpstr = strrev($tmpstr);

  $tableend = strpos($tmpstr, strrev("</script>"), 1);
  $tmpstr = substr($tmpstr, $tableend);  // strip off stuff after </table>

  // Now let's un-reverse it
  $tmpstr = strrev($tmpstr);

  // print "<hr>After cleaning tmpstr:" . strlen($tmpstr) . ": FULL=[".  htmlspecialchars($tmpstr) . "]<hr>\n";
//  return  "\n<!-- start $commentlabel //-->\n$tmpstr\n<!-- end $commentlabel //-->\n";
  return $tmpstr;
}

// Return the value between $startstr and $endstr in $tmpstr
function str_between($tmpstr, $startstr, $endstr) {
  $startpos = strpos($tmpstr, $startstr);

  // If empty, return nothing
  if ($startpos === false) {
        return  "";
  }

  $tmpstr = substr($tmpstr, $startpos + strlen($startstr)); // strip off stuff before $start

  // Now lets remove the </td></tr> at the end of the tableBox output
  // strrpos only works for chars, not strings, so we'll cheat and reverse the string and then use strpos
  $tmpstr = strrev($tmpstr);

  $endpos = strpos($tmpstr, strrev($endstr), 1);

  $tmpstr = substr($tmpstr, $endpos + strlen($endstr));  // strip off stuff after </table>

  // Now let's un-reverse it
  $tmpstr = strrev($tmpstr);

  return  $tmpstr;
}

function sortbykeylength($a,$b) {
  $alen = strlen($a);
  $blen = strlen($b);
  if ($alen == $blen) $r = 0;
  if ($alen < $blen) $r = 1;
  if ($alen > $blen) $r = -1;
  return $r;
}

?>
