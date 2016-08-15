<?php
/*
  $Id: googlesitemap.php admin page,v 1.0 8/11/2005 bhakala@pc-productions.net
  Released under the GNU General Public License
*/

  require('includes/application_top.php');
    
  	function GenerateSubmitURL(){
		$url = urlencode(HTTP_SERVER . DIR_WS_CATALOG . 'sitemapindex.xml');
		return htmlspecialchars(utf8_encode('http://www.google.com/webmasters/sitemaps/ping?sitemap=' . $url));
	} # end function
?>

<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title>Google XML Sitemap Admin</title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
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
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading">Google XML Sitemap Admin </td>
            <td class="pageHeading" align="right"><img src="images/google-sitemaps.gif" width="110" height="48"></td>
          </tr>
        </table>
          <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="main">
            <tr>
              <td width="78%" align="left" valign="top"><p><strong>OVERVIEW:</strong></p>
                <p>This module automatically generates several XML Google compliant site maps for your oscommerce store: a main site map, one for categories, and one for your products.</p>
                <p><strong>INSTRUCTIONS: </strong></p>
                <p><strong><font color="#FF0000">STEP 1:</font></strong> Click <a href="javascript:(void 0)" class="splitPageLink" onClick="window.open('<?php echo $HTTP_SERVER . DIR_WS_CATALOG;?>googlesitemap/index.php','google','resizable=1,statusbar=5,width=600,height=400,top=0,left=50,scrollbars=yes')"><strong>[HERE]</strong></a> to create / update your site map. </p>
                <p>NOTE: Please ensure that you or your web developer has registered with Google SiteMaps, and submitted your initial site map before proceeding to step 2. </p>
                <p><strong><font color="#FF0000">STEP 2:</font></strong> Click <a href="javascript:(void 0)"  onClick="window.open('<?php echo $returned_url = GenerateSubmitURL();?>','google','resizable=1,statusbar=5,width=600,height=400,top=0,left=50,scrollbars=yes')" class="splitPageLink"><strong>[HERE]</strong></a> to PING the google server to notify them of the update to your XML sitemap.</p>
                <p>COMPLETE!</p>
                <p>&nbsp;</p></td>
              <td width="22%" align="right" valign="top"><table width="98%"  border="0" cellpadding="1" cellspacing="0" bgcolor="#E1EEFF">
                <tr>
                  <td align="center" class="smallText"> <strong>What is Google SiteMaps?</strong></td>
                </tr>
                <tr>
                  <td class="smallText"><table width="100%"  border="0" cellpadding="4" cellspacing="0" bgcolor="#F0F8FF">
                    <tr>
                      <td align="left" valign="top" class="smallText"><p>Google SiteMaps allows you to upload an XML sitemap of all of your categories and products directly to google.com for faster indexing. </p>
                        <p>To register or login to your Google account, click <strong><a href="https://www.google.com/webmasters/sitemaps/login" target="_blank" class="splitPageLink">[HERE]</a></strong>.</p>
                        </td>
                    </tr>
                  </table></td>
                </tr>
              </table>
                <p>&nbsp;</p></td>
            </tr>
          </table>
          </td>
      </tr>
      <tr>
        <td></td>
          </tr>       
<!-- body_text_eof //-->
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>