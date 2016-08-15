<?php
  require('includes/application_top.php');

  $pages_id = $HTTP_GET_VARS["pages_id"];
  $pages_query = tep_db_query("select pd.pages_name, pd.pages_intro, pd.pages_body, pd.pages_body2, pd.pages_img_alt, pd.pages_head_title_tag, pd.pages_head_desc_tag, pd.pages_head_keywords_tag, pd.pages_surls_id, pd.pages_h1, p.pages_id, p.pages_image, p.pages_status, p.sort_order from " . TABLE_PAGES . " p, " . TABLE_PAGES_DESCRIPTION . " pd where p.pages_id = '" . (int)$pages_id . "' and p.pages_id = pd.pages_id and pd.language_id = '" . (int)$languages_id . "'");
  $pages = tep_db_fetch_array($pages_query);
  define('NAVBAR_TITLE', $pages['pages_name']);
  define('HEADING_TITLE', $pages['pages_name']);
  define('TEXT_INFORMATION', $pages['pages_body']);
  $breadcrumb->add(NAVBAR_TITLE, tep_href_link('pages.php', 'pages_id='.$pages_id, 'NONSSL'));
?>


<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<base href="<?php echo (getenv('HTTPS') == 'on' ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
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
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td class="pageContent" valign="top"><?php echo tep_image(DIR_WS_IMAGES . ($pages["pages_image"]?$pages["pages_image"]:'pixel_trans.gif'), $pages["pages_img_alt"]?$pages["pages_img_alt"]:HEADING_TITLE, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'align="right"');
        if (tep_not_null($pages['pages_intro'])) echo $pages['pages_intro'];
        else echo "<h2>" . $pages['pages_name'] . "</h2>"; ?>
        </td>
      </tr>
<?php if ($pages && $pages['pages_body']) { ?>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td class="pageContent" valign="top"><?php echo $pages['pages_body']; ?></td>
      </tr>
<?php } ?>
      <tr><td><div id="emptyDiv"></div></td></tr>
<?php if ($pages && $pages['pages_body2']) { ?>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td class="pageContent" valign="top"><?php echo $pages['pages_body2']; ?></td>
      </tr>
<?php } ?>
<tr><td><div id="centerDiv"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td align="right"><?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT) . '">' . tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></td>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
</table></div></td></tr>
<script language="javascript">
<!--
document.getElementById('emptyDiv').innerHTML=document.getElementById('centerDiv').innerHTML;
document.getElementById('centerDiv').innerHTML='';
-->
</script>
    </table></td>
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