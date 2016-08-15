<?php
require_once('mobile/includes/application_top.php');

  $pages_id = $HTTP_GET_VARS["pages_id"];
  $pages_query = tep_db_query("select pd.pages_name, pd.pages_intro, pd.pages_body, pd.pages_body2, pd.pages_img_alt, pd.pages_head_title_tag, pd.pages_head_desc_tag, pd.pages_head_keywords_tag, pd.pages_surls_id, pd.pages_h1, p.pages_id, p.pages_image, p.pages_status, p.sort_order from " . TABLE_PAGES . " p, " . TABLE_PAGES_DESCRIPTION . " pd where p.pages_id = '" . (int)$pages_id . "' and p.pages_id = pd.pages_id and pd.language_id = '" . (int)$languages_id . "'");
  $pages = tep_db_fetch_array($pages_query);
  define('NAVBAR_TITLE', $pages['pages_name']);
  define('HEADING_TITLE', $pages['pages_name']);
  define('TEXT_INFORMATION', $pages['pages_body']);

require(DIR_MOBILE_INCLUDES . 'header.php');
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PRIVACY);
  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_PRIVACY));
	$headerTitle->write();
?>
<!--  ajax_part_begining -->
<table width="100%" border="0" cellpadding="5" cellspacing="0">
  <tr valign="top"><td class="empty"></td><td rowspan="2" class="content_center">
	
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td class="pageContent" valign="top"><?php //echo tep_image(DIR_WS_IMAGES . ($pages["pages_image"]?$pages["pages_image"]:'pixel_trans.gif'), $pages["pages_img_alt"]?$pages["pages_img_alt"]:HEADING_TITLE, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'align="right"');
        if (tep_not_null($pages['pages_intro'])) echo $pages['pages_intro'];
        else echo "<h2>" . $pages['pages_name'] . "</h2>"; ?>
        </td>
      </tr>
<?php if ($pages && $pages['pages_body']) { ?>
      <tr>
        <td class="pageContent" valign="top"><?php echo $pages['pages_body']; ?></td>
      </tr>
<?php } ?>
      
<?php if ($pages && $pages['pages_body2']) { ?>
      <tr>
        <td class="pageContent" valign="top"><?php echo $pages['pages_body2']; ?></td>
      </tr>
<?php } ?>
<tr><td><div id="centerDiv"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<!--      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td align="right"><?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT) . '">' . tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
--></table></div></td></tr>
<script language="javascript">
<!--
document.getElementById('emptyDiv').innerHTML=document.getElementById('centerDiv').innerHTML;
document.getElementById('centerDiv').innerHTML='';
-->
</script>
    </table>
<?php require(DIR_MOBILE_INCLUDES . 'footer.php'); ?>
