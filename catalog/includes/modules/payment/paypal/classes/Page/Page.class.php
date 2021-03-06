<?php
/*
  $Id: Page.class.php,v 1.1.1.1 2004/09/22 13:45:13 devosc Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  DevosC, Developing open source Code
  http://www.devosc.com

  Copyright (c) 2003 osCommerce
  Copyright (c) 2004 DevosC.com

  Released under the GNU General Public License
*/

class PayPal_Page {

  var $baseDirectory;

  var $metaTitle = 'osCommerce: PayPal_Shopping_Cart_IPN';

  // class constructor
  function PayPal_Page() {
  }

  function template() {
    return $this->baseDirectory . 'templates/' . $this->templateName . '.tpl.php';
  }

  function setTemplate($template) {
    $this->templateName = $template;
  }

  function setContentFile($contentFile = '') {
    $this->contentFile = $contentFile;
  }

  function setContentLangaugeFile($base_dir, $lng_dir, $lng_file) {
    if(file_exists($base_dir .'/'. $lng_dir . '/' . $lng_file)) {
      $this->contentFile = $base_dir .'/'. $lng_dir . '/' . $lng_file;
    } elseif (file_exists($base_dir . '/english/' . $lng_file)) {
      $this->contentFile =  $base_dir . '/english/' . $lng_file;
    }
  }

  function setTitle($title = '') {
    $this->pageTitle = $title;
  }

  function setOnLoad($javascript) {
    $this->onLoad = $javascript;
  }

  function setMetaTitle($title) {
    $this->metaTitle = $title;
  }

  function includeLanguageFile($paypal_dir, $lng_dir, $lng_file) {
    $base_dir = $this->baseDirectory . $paypal_dir . '/';
    if(file_exists($base_dir . $lng_dir . '/' . $lng_file)) {
      include_once($base_dir . $lng_dir . '/' . $lng_file);
    } elseif (file_exists($base_dir . 'english/' . $lng_file)) {
      include_once($base_dir . 'english/' . $lng_file);
    }
  }

  function setBaseDirectory($dir) {
    $this->baseDirectory = $dir;
  }

  function setBaseURL($location) {
    $this->baseURL = $location;
  }

  function imagePath($image) {
    return $this->baseURL. 'images/'.$image;
  }

  function image($img,$alt='') {
    return tep_image($this->baseURL.'images/'.$img,$alt);
  }

  function draw_href_link($ppURLText, $ppURLParams = '', $ppURL = FILENAME_PAYPAL, $js = true) {
    //$ppURL = tep_href_link(FILENAME_PAYPAL,'action=details&info='.$ppTxnID);
    $ppURL = tep_href_link($ppURL,$ppURLParams);
    if ($js === true) {
      $ppScriptLink = '<script language="JavaScript"><!--
      document.write("<a style=\"color: #0033cc; text-decoration: none;\" href=\"javascript:openWindow(\''.$ppURL.'\');\" tabindex=\"-1\">'.$ppURLText.'</a>");
      --></script><noscript><a style="color: #0033cc; text-decoration: none;" href="'.$ppURL.'" target="PayPal">'.$ppURLText.'</a></noscript>';
    } else {
      $ppScriptLink = '<a style="color: #0033cc; text-decoration: none;" href="'.$ppURL.'" target="PayPal">'.$ppURLText.'</a>';
    }
    return $ppScriptLink;
  }

  function addJavaScript($filename) {
    $this->javascript[] = $filename;
  }

  function importJavaScript() {
    if(is_array($this->javascript)) {
      $javascript = '';
      $javascriptCount = count($this->javascript);
      for($i=0; $i<$javascriptCount; $i++) {
        $javascript .= '<script language="javascript" src="' . $this->javascript[$i] . '"></script>'."\n";
      }
      return $javascript;
    }
  }

  function addCSS($filename) {
    $this->css[] = $filename;
  }

  function getCSS($filename) {
    $css = '';
    if(function_exists(file_get_contents)) {
      $css = file_get_contents($this->baseDirectory.'templates/css/'.basename($filename).'.css');
    } else {
      $fh = @fopen($this->baseDirectory.'templates/css/'.basename($filename).'.css','rb');
      if ($fh) {
        while (!feof($fh))
          $css .= @fread($fh,1024);
        @fclose($fh);
      }
    }
    return $css;
  }

  function importCSS() {
    $css = "<style type='text/css' media=\"all\">\n";
    if(is_array($this->css)) {
      $cssCount = count($this->css);
      for($i=0; $i<$cssCount; $i++) {
        $css .= "@import url(" . $this->css[$i] . ");\n";
      }
    }
    return $css."</style>\n";
  }

  function copyright() {
  return "<table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"2\">
      <tr><td><br class=\"h10\"/></td></tr>
      <tr><td align=\"center\" class=\"ppfooter\">E-Commerce Engine Copyright &copy; 2000-2004 <a href=\"http://www.oscommerce.com\" class=\"copyright\" target=\"_blank\">osCommerce</a><br/>osCommerce provides no warranty and is redistributable under the <a href=\"http://www.fsf.org/licenses/gpl.txt\" class=\"copyright\" target=\"_blank\">GNU General Public License</a></td></tr>
      <tr><td><br class=\"h10\"/></td></tr><tr><td align=\"center\" class=\"ppfooter\"><a href=\"http://www.oscommerce.com\" target=\"_blank\" class=\"poweredByButton\"><span class=\"poweredBy\">Powered By</span><span class=\"osCommerce\">" . PROJECT_VERSION . "</span></a></td></tr><tr><td><br class=\"h10\"/></td></tr></table>";
  }
}//end class
?>
