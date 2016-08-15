<?php
/*
  $Id: html_output.php,v 1.56 2003/07/09 01:15:48 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

////
// The HTML href link wrapper function
  function tep_href_link($page = '', $parameters = '', $connection = 'NONSSL', $add_session_id = true, $search_engine_safe = true) {
    global $request_type, $session_started, $SID, $languages_id, $column_list, $lng;

    if (!tep_not_null($page)) {
      die('</td></tr></table></td></tr></table><br><br><font color="#ff0000"><b>Error!</b></font><br><br><b>Unable to determine the page link!<br><br>');
    }

// added by splautz for custom seo urls
    $surls_page = false; $surls_script = $page; $no_surls = false;
    if ( ($connection == 'NONSSL') && (SEARCH_ENGINE_FRIENDLY_URLS == 'true') && ($search_engine_safe == true) && !preg_match("/(action=[^\&]*)(?:\&|$)/s",$parameters,$templist) ) {
      if (preg_match("/sort=(([0-8])[ad])(?:\&|$)/s",$parameters,$templist,PREG_OFFSET_CAPTURE)) {
        if ($templist[2][0] != '0' || $templist[2][0].'d' == $templist[1][0]) $no_surls = true;
        else $parameters = substr_replace($parameters,'',$templist[0][1],strlen($templist[0][0]));
      }
      if (preg_match("/currency=(\w+)(?:\&|$)/s",$parameters,$templist,PREG_OFFSET_CAPTURE)) $no_surls = true;
      if (!$no_surls) {
        $original_parameters = $parameters;
        $surls_lid = $languages_id;
        if (preg_match("/language=(\w+)(?:\&|$)/s",$parameters,$templist,PREG_OFFSET_CAPTURE)) {
          if (!is_numeric($templist[1][0]) || $surls_lid != $templist[1][0]) {
            if ( is_numeric($templist[1][0]) ) {
              foreach( $lng->catalog_languages as $code => $catalog_language ) {
                if ( $templist[1][0] == $catalog_language['id'] ) {
                  $surls_lid = $catalog_language['id'];
                  break;
                }
              }
            } elseif ( isset($lng->catalog_languages[$templist[1][0]]) )
              $surls_lid = $lng->catalog_languages[$templist[1][0]]['id'];
          }
          $parameters = substr_replace($parameters,'',$templist[0][1],strlen($templist[0][0]));
        }
        if (preg_match("/(products_id=\d+)(?:\&|$)/s",$parameters,$templist,PREG_OFFSET_CAPTURE)
         || ($page == FILENAME_PAGES && preg_match("/(pages_id=\d+)(?:\&|$)/s",$parameters,$templist,PREG_OFFSET_CAPTURE))
         || ($page == FILENAME_DEFAULT && preg_match("/(manufacturers_id=\d+)(?:\&|$)/s",$parameters,$templist,PREG_OFFSET_CAPTURE))) {
          if ($surls_page = tep_get_surls_page($page, $templist[1][0], $surls_lid, $surls_script))
            $parameters = substr_replace($parameters,'',$templist[0][1],strlen($templist[0][0]));
        }
        if (preg_match("/cPath=(?:\d+_)*(\d+)(?:\&|$)/s",$parameters,$templist,PREG_OFFSET_CAPTURE)
         && ($surls_page !== false || ($page == FILENAME_DEFAULT && $surls_page = tep_get_surls_page($page, 'cPath='.$templist[1][0], $surls_lid, $surls_script))))
          $parameters = substr_replace($parameters,'',$templist[0][1],strlen($templist[0][0]));
        elseif ($surls_page === false) $surls_page = tep_get_script_surls_name($page, $surls_lid, $surls_id);
        if ($surls_page) {
          if (preg_match("/(page|reviews_id|testimonial_id)=(\d+)(?:\&|$)/s",$parameters,$templist,PREG_OFFSET_CAPTURE)) {
            $parameters = substr_replace($parameters,'',$templist[0][1],strlen($templist[0][0]));
            if ($templist[1][0] != 'page' || $templist[2][0] != '1') $page = substr_replace($page, '-' . $templist[2][0], strrpos($page,'.'), 0);
          }
          if ($surls_script == $page) $page = $surls_page;
          else $page = $surls_page . $page;
        } else {
          $parameters = $original_parameters;
          if ($page == FILENAME_DEFAULT && (!$parameters || $parameters == '&')) $page = '';
        }
      }
    }
    if (substr(trim($parameters),-1)=='&') $parameters = substr(trim($parameters),0,strlen(trim($parameters))-1);

    if ($connection == 'NONSSL') {
      $link = HTTP_SERVER . (($surls_page || !$page)?'/':DIR_WS_HTTP_CATALOG);
    } elseif ($connection == 'SSL') {
      if (ENABLE_SSL == true) {
        $link = HTTPS_SERVER . DIR_WS_HTTPS_CATALOG;
      } else {
        $link = HTTP_SERVER . DIR_WS_HTTP_CATALOG;
      }
    } else {
      die('</td></tr></table></td></tr></table><br><br><font color="#ff0000"><b>Error!</b></font><br><br><b>Unable to determine connection method on a link!<br><br>Known methods: NONSSL SSL</b><br><br>');
    }

    if (tep_not_null($parameters)) {
      $link .= $page . '?' . tep_output_string($parameters);
      $separator = '&';
    } else {
      $link .= $page;
      $separator = '?';
    }

    while ( (substr($link, -1) == '&') || (substr($link, -1) == '?') ) $link = substr($link, 0, -1);

// Add the session ID when moving from different HTTP and HTTPS servers, or when SID is defined
// - modified by splautz to allow passing of sessionid during different domain HTTP/HTTPS transfers when force cookie use is true 
//  if ( ($add_session_id == true) && ($session_started == true) && (SESSION_FORCE_COOKIE_USE == 'False') ) {
//    if (tep_not_null($SID)) {
    if ( ($add_session_id == true) && ($session_started == true) ) {
      if (tep_not_null($SID) && (SESSION_FORCE_COOKIE_USE == 'False')) {
        $_sid = $SID;
      } elseif ( ( ($request_type == 'NONSSL') && ($connection == 'SSL') && (ENABLE_SSL == true) ) || ( ($request_type == 'SSL') && ($connection == 'NONSSL') ) ) {
        if (HTTP_COOKIE_DOMAIN != HTTPS_COOKIE_DOMAIN) {
          $_sid = tep_session_name() . '=' . tep_session_id();
        }
      }
    }

//	replaced by splautz in favor of above seo url coding
//
//    if ( (SEARCH_ENGINE_FRIENDLY_URLS == 'true') && ($search_engine_safe == true) ) {
//      while (strstr($link, '&&')) $link = str_replace('&&', '&', $link);
//
//      $link = str_replace('?', '/', $link);
//      $link = str_replace('&', '/', $link);
//      $link = str_replace('=', '/', $link);
//
//      $separator = '?';
//    }

    if (isset($_sid)) {
      $link .= $separator . tep_output_string($_sid); // Update 051113
    }

    return $link;
  }

////
// The HTML image wrapper function
// Update-20060817 (alternate fix)
  function tep_image($src, $alt = '', $width = '', $height = '', $parameters = '') {
    if ( (empty($src) || ($src == DIR_WS_IMAGES)) && (IMAGE_REQUIRED == 'false') ) {
      $src = DIR_WS_IMAGES . 'pixel_trans.gif';  // return false;  modified by splautz to resolve spacing issues
    }
// added for automatic thumbnail
    if ($src == DIR_WS_IMAGES . 'pixel_black.gif' || $src == DIR_WS_IMAGES . 'pixel_trans.gif' || $src == DIR_WS_IMAGES . 'pixel_silver.gif' ) $spix = true;
	else $spix = false;
    if ($image_size = @getimagesize($src)) {
      if ( $width==='100%' ) $width = $image_size[0];
      if ( $height==='100%' ) $height = $image_size[1];
      if (!$spix && CONFIG_CALCULATE_IMAGE_SIZE == 'true') {
        if (empty($width) && empty($height)) {
          $width = $image_size[0];
          $height = $image_size[1];
        } elseif (empty($width) && tep_not_null($height)) {
          $ratio = $height / $image_size[1];
          $width = ceil($image_size[0] * $ratio);
        } elseif (tep_not_null($width) && empty($height)) {
          $ratio = $width / $image_size[0];
          $height = ceil($image_size[1] * $ratio);
        }
		if (($image_size[1]/$height) > ($image_size[0]/$width)){
		  $width=ceil(($image_size[0]/$image_size[1])* $height);
		} else {
		  $height=ceil($width/($image_size[0]/$image_size[1]));
		}
      }
    } elseif (IMAGE_REQUIRED == 'false') {
// modified by splautz to resolve spacing issues
      // return '';
      $src = DIR_WS_IMAGES . 'pixel_trans.gif';
      $spix = true;
    }
    if ($spix) {
      if (empty($width)) $width = 1;
      if (empty($height)) $height = 1;

    } else $src=thumbimage(DIR_FS_CATALOG . $src, $width, $height, 0, 0);

// alt is added to the img tag even if it is null to prevent browsers from outputting
// the image filename as default
    $image = '<img src="' . tep_output_string($src) . '" border="0" alt="' . tep_output_string($alt) . '"';

    if (tep_not_null($alt)) {
      $image .= ' title="' . tep_output_string($alt) . '"';
    }

    if (tep_not_null($width) && tep_not_null($height)) {
      $image .= ' width="' . tep_output_string($width) . '" height="' . tep_output_string($height) . '"';
    }

    if (tep_not_null($parameters)) $image .= ' ' . $parameters;

    $image .= '>';

    return $image;
  }

////
// The HTML form submit button wrapper function
// Outputs a button in the selected language
  function tep_image_submit($image, $alt = '', $parameters = '') {
    global $language;

// added by splautz for button templates & css buttons
    $exceptions = array('button_quick_find.gif', 'button_tell_a_friend.gif', 'image_enlarge.gif', 'button_ppcheckout.gif');
    $template =  in_array($image,$exceptions)?'':trim(BUTTON_TEMPLATE);
    $imagepath = DIR_WS_LANGUAGES . $language . '/images/buttons/' . (tep_not_null($template)&&strtolower($template)!='css'?$template.'/':'') . $image;
    if ((strtolower($template) == 'css' && tep_not_null($alt)) || !file_exists($imagepath)) {
      if (preg_match("/name\s*\=\s*([\x22\x27])(.*?)\\1/is",$parameters,$match) || preg_match("/(name)\s*\=\s*(\S*)/is",$parameters,$match)) $name = ' name="' . $match[2] . '_x"';
      else $name = '';
      $image_submit = '<span class="cssbutton"><input class="cssbuttonsubmit" type="submit" value="' .$alt . '"' . $name . '></span>';
    } else {
// end add

    $image_submit = '<input type="image" src="' . tep_output_string($imagepath) . '" alt="' . tep_output_string($alt) . '"';  // modified by splautz for button templates

    if (tep_not_null($alt)) $image_submit .= ' title=" ' . tep_output_string($alt) . ' "';

    if (tep_not_null($parameters)) $image_submit .= ' ' . $parameters;

    $image_submit .= '>';

    }  // added by splautz for button templates & css buttons

    return $image_submit;
  }

////
// Output a function button in the selected language
  function tep_image_button($image, $alt = '', $parameters = '') {
    global $language;

// added by splautz for button templates & css buttons
    $exceptions = array('button_quick_find.gif', 'button_tell_a_friend.gif', 'image_enlarge.gif', 'button_ppcheckout.gif');
    $template =  in_array($image,$exceptions)?'':trim(BUTTON_TEMPLATE);
    $imagepath = DIR_WS_LANGUAGES . $language . '/images/buttons/' . (tep_not_null($template)&&strtolower($template)!='css'?$template.'/':'') . $image;
    if ((strtolower($template) == 'css' && tep_not_null($alt)) || !file_exists($imagepath))
      return '<span class="cssbutton">&nbsp;' . $alt . '&nbsp;</span>';
    else
// end add

    return tep_image($imagepath, $alt, '', '', $parameters);  // modified by splautz for button templates
  }

////
// Output a separator either through whitespace, or with an image
  function tep_draw_separator($image = 'pixel_black.gif', $width = '100%', $height = '1') {
    return tep_image(DIR_WS_IMAGES . $image, '', $width, $height);
  }

////
// Output a form
  function tep_draw_form($name, $action, $method = 'post', $parameters = '') {
    $form = '<form name="' . tep_output_string($name) . '" action="' . tep_output_string($action) . '" method="' . tep_output_string($method) . '"';

    if (tep_not_null($parameters)) $form .= ' ' . $parameters;

    $form .= '>';

    return $form;
  }

////
// Output a form input field
  function tep_draw_input_field($name, $value = '', $parameters = '', $type = 'text', $reinsert_value = true) {
    $field = '<input type="' . tep_output_string($type) . '" name="' . tep_output_string($name) . '"';

    if ( (isset($GLOBALS[$name])) && ($reinsert_value == true) ) {
      $field .= ' value="' . tep_output_string(stripslashes($GLOBALS[$name])) . '"';
    } elseif (tep_not_null($value)) {
      $field .= ' value="' . tep_output_string($value) . '"';
    }

    if (tep_not_null($parameters)) $field .= ' ' . $parameters;

    $field .= '>';

    return $field;
  }

////
// Output a form password field
  function tep_draw_password_field($name, $value = '', $parameters = 'maxlength="40"') {
    return tep_draw_input_field($name, $value, $parameters, 'password', false);
  }

////
// Output a selection field - alias function for tep_draw_checkbox_field() and tep_draw_radio_field()
  function tep_draw_selection_field($name, $type, $value = '', $checked = false, $parameters = '') {
    $selection = '<input type="' . tep_output_string($type) . '" name="' . tep_output_string($name) . '"';

    if (tep_not_null($value)) $selection .= ' value="' . tep_output_string($value) . '"';

    if ( ($checked == true) || ( isset($GLOBALS[$name]) && is_string($GLOBALS[$name]) && ( ($GLOBALS[$name] == 'on') || (isset($value) && (stripslashes($GLOBALS[$name]) == $value)) ) ) ) {
      $selection .= ' CHECKED';
    }

    if (tep_not_null($parameters)) $selection .= ' ' . $parameters;

    $selection .= '>';

    return $selection;
  }

////
// Output a form checkbox field
  function tep_draw_checkbox_field($name, $value = '', $checked = false, $parameters = '') {
    return tep_draw_selection_field($name, 'checkbox', $value, $checked, $parameters);
  }

////
// Output a form radio field
  function tep_draw_radio_field($name, $value = '', $checked = false, $parameters = '') {
    return tep_draw_selection_field($name, 'radio', $value, $checked, $parameters);
  }

////
// Output a form textarea field
  function tep_draw_textarea_field($name, $wrap, $width, $height, $text = '', $parameters = '', $reinsert_value = true) {
    $field = '<textarea name="' . tep_output_string($name) . '" wrap="' . tep_output_string($wrap) . '" cols="' . tep_output_string($width) . '" rows="' . tep_output_string($height) . '"';

    if (tep_not_null($parameters)) $field .= ' ' . $parameters;

    $field .= '>';

 // Update 051113
    if ( (isset($GLOBALS[$name])) && ($reinsert_value == true) ) {
      $field .= tep_output_string_protected(stripslashes($GLOBALS[$name]));
    } elseif (tep_not_null($text)) {
      $field .= tep_output_string_protected($text);
    }

    $field .= '</textarea>';

    return $field;
  }

////
// Output a form hidden field
  function tep_draw_hidden_field($name, $value = '', $parameters = '') {
    $field = '<input type="hidden" name="' . tep_output_string($name) . '"';

    if (tep_not_null($value)) {
      $field .= ' value="' . tep_output_string($value) . '"';
    } elseif (isset($GLOBALS[$name])) {
      $field .= ' value="' . tep_output_string(stripslashes($GLOBALS[$name])) . '"';
    }

    if (tep_not_null($parameters)) $field .= ' ' . $parameters;

    $field .= '>';

    return $field;
  }

////
// Hide form elements
  function tep_hide_session_id() {
    global $session_started, $SID;

    if (($session_started == true) && (SESSION_FORCE_COOKIE_USE == 'False') && tep_not_null($SID)) {
      return tep_draw_hidden_field(tep_session_name(), tep_session_id());
    }
  }

////
// Output a form pull down menu
  function tep_draw_pull_down_menu($name, $values, $default = '', $parameters = '', $required = false) {
    $field = '<select name="' . tep_output_string($name) . '"';

    if (tep_not_null($parameters)) $field .= ' ' . $parameters;

    $field .= '>';

    if (empty($default) && isset($GLOBALS[$name])) $default = stripslashes($GLOBALS[$name]);

    for ($i=0, $n=sizeof($values); $i<$n; $i++) {
      $field .= '<option value="' . tep_output_string($values[$i]['id']) . '"';
      if ($default == $values[$i]['id']) {
        $field .= ' SELECTED';
      }

      $field .= '>' . tep_output_string($values[$i]['text'], array('"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;')) . '</option>';
    }
    $field .= '</select>';

    if ($required == true) $field .= TEXT_FIELD_REQUIRED;

    return $field;
  }

////
// Creates a pull-down list of countries
// modified by splautz to support limiting by geo_zones
  function tep_get_country_list($name, $selected = '', $parameters = '', $geo_zone = '') {
    $countries_array = array(array('id' => '', 'text' => PULL_DOWN_DEFAULT));
    $countries = tep_get_countries('', false, $geo_zone);

    for ($i=0, $n=sizeof($countries); $i<$n; $i++) {
      $countries_array[] = array('id' => $countries[$i]['countries_id'], 'text' => $countries[$i]['countries_name']);
    }

    return tep_draw_pull_down_menu($name, $countries_array, $selected, $parameters);
  }

function thumbimage ($image, $x, $y, $aspectratio, $resize){

          /*##############################################
	      #          - Thumbnail-Script v1.3 -           #
	      # Generating thumbnails 'on-the-fly' with PHP  #
	      #                                              #
	      #  (c) by Christian Lamine, FlashDreams OHG    #
	      #          http://www.flashdreams.de/          #
	      #                                              #
	      #       Modified by http://www.tse.at          #
	      #       Modified by lars@iwer.de               #
	      #                                              #
	      # This script may be freely used, distributed  #
	      # and modified without any charge as long as   #
	      # this copyright information is included.      #
	      #                                              #
	      # Any commercial selling of this script is     #
	      # forbidden.                                   #
	      #                                              #
	      # The author is not responsible for possible   #
	      # damage which may result from the application #
	      # of this script, neither direct nor indirect. #
	      # Use at your own risk!                        #
    	  ##############################################*/

//     error_reporting(0);
     // Define RGB Color Value for background matte color
     // Example: white is r=255, b=255, g=255; black is r=0, b=0, g=0; red is r=255, b=0, g=0;
     $r = 255; // Red color value (0-255)
     $g = 255; // Green color value (0-255)
     $b = 255; // Blue color value (0-255)

     $imagedir = dirname($image).'/';
     $cachedir = $imagedir . DIRNAME_IMAGECACHE;
     $webdir = substr($imagedir, strlen(DIR_FS_CATALOG));

     $types = array (1 => "gif", "jpeg", "png", "swf", "psd", "wbmp");
	 $not_supported_formats = array ("BMP"); // Write in capital Letters!!

     if (!(!isset ($x) || ereg ('^[0-9]{1,}$', $x, $regs)) &&
       (!isset ($y) || ereg ('^[0-9]{1,}$', $y, $regs)) &&
       (isset ($x) || isset ($y))) return $webdir . basename($image); // DIE ('Fehlende(r) oder ungültige(r) Größenparameter!');

     !isset ($resize) || !ereg ('^[0|1]$', $resize, $regs)
          ? $resize = 0
          : $resize;

     !isset ($aspectratio) || !ereg ('^[0|1]$', $aspectratio, $regs)
          ? isset ($x) && isset ($y)
                 ? $aspectratio = 1
                 : $aspectratio = 0
          : $aspectratio;

     !isset ($image)
          ? DIE ('Es wurde kein Bild angegeben!')
          : !file_exists($image)
               ? DIE ('Die angegebene Datei konnte nicht auf dem Server gefunden werden!')
               : false;

     $imagedata = getimagesize($image);
     $imagemdate = filemtime($image);

     !$imagedata[2] || $imagedata[2] == 4 || $imagedata[2] == 5
          ? DIE ('Bei der angegebenen Datei handelt es sich nicht um ein Bild!')
          : false;

     if (!(imagetypes() & @constant("IMG_" . strtoupper($types[$imagedata[2]]))) || (in_array(strtoupper(array_pop(explode('.', basename($image)))),$not_supported_formats))) {
       return $image = $webdir . DIRNAME_IMAGECACHE . basename($image);  // display blank image so we know there's a problem
     }

     if (empty($x)) $x = floor ($y * $imagedata[0] / $imagedata[1]);


     if (empty($y)) $y = floor ($x * $imagedata[1] / $imagedata[0]);

     if ($aspectratio && isset ($x) && isset ($y)) {
		if ((($imagedata[1]/$y) > ($imagedata[0]/$x) )){
			 $x=ceil(($imagedata[0]/$imagedata[1])* $y);
		} else {
			 $y=ceil($x/($imagedata[0]/$imagedata[1]));
		}
     }

     $makethumb = false;
     $iscache = false;
     if ($x < 1000 && $y < 1000 && (($imagedata[0] > $x || $imagedata[1] > $y) || (($imagedata[0] < $x || $imagedata[1] < $y) && $resize))) {
       $bimage = explode('.', basename($image));
	   array_splice($bimage,count($bimage)-1,0,sprintf("%03s",$x) . sprintf("%03s",$y));
	   $thumbfile =  implode('.',$bimage);
       umask(0);
       if (!is_dir ($cachedir) && !mkdir ($cachedir, 0777)) return $webdir . basename($image);
       else @chmod($cachedir, 0777);  // system ("chmod 0777 ".$cachedir);

       if (file_exists ($cachedir.$thumbfile)) {
            $thumbdata = getimagesize ($cachedir.$thumbfile);
            $thumbmdate = filemtime($cachedir.$thumbfile);
            if ($thumbdata[0] == $x && $thumbdata[1] == $y && $thumbmdate > $imagemdate) $iscached = true;
       }
       if (!$iscached) $makethumb = true;
     }

     if ($makethumb) {
          $img = @call_user_func("imagecreatefrom".$types[$imagedata[2]], $image);
	      if (function_exists("imagecreatetruecolor") && ($thumb = imagecreatetruecolor ($x, $y))) $truecolor = true;
          else {
               $truecolor = false;
               $thumb = imagecreate ($x, $y);
          }
          $th_bg_color = imagecolorallocate($thumb, $r, $g, $b);
          imagefill($thumb, 0, 0, $th_bg_color);
          imagecolortransparent($thumb, $th_bg_color);
          if ($truecolor) @imagecopyresampled ($thumb, $img, 0, 0, 0, 0, $x, $y, $imagedata[0], $imagedata[1]);
          else @imagecopyresized ($thumb, $img, 0, 0, 0, 0, $x, $y, $imagedata[0], $imagedata[1]);
          call_user_func("image".$types[$imagedata[2]], $thumb, $cachedir.$thumbfile);
          @imagedestroy ($img);
          imagedestroy ($thumb);
          $out = $webdir . DIRNAME_IMAGECACHE . $thumbfile;
     } else {
          $iscached
               ? $out = $webdir . DIRNAME_IMAGECACHE . $thumbfile
               : $out = $webdir . basename($image);
     }

     return $out;

}
?>
