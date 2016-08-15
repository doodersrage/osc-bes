<?php
/*
  $Id: customer_testimonials.php,v 1.3 2003/12/08 Exp $

  The Exchange Project - Community Made Shopping!
  http://www.theexchangeproject.org

  Copyright (c) 2000,2001 The Exchange Project

  Released under the GNU General Public License
  Contributed by http://www.seen-online.co.uk
*/

  require('includes/application_top.php');

// added by splautz for surls
  if (isset($HTTP_GET_VARS['page']) && tep_not_null($HTTP_GET_VARS['page'])) {
    $HTTP_GET_VARS['testimonial_id'] = $HTTP_GET_VARS['page'];
    $testimonial_id = $HTTP_GET_VARS['page'];
    unset($HTTP_GET_VARS['page']);
  } elseif (isset($HTTP_GET_VARS['testimonial_id']) && tep_not_null($HTTP_GET_VARS['testimonial_id'])) $testimonial_id = $HTTP_GET_VARS['testimonial_id'];
  else $testimonial_id = '';

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CUSTOMER_TESTIMONIALS);

// added by splautz to append review number to title
  if ($testimonial_id != '' && defined('HEAD_TITLE_TESTIMONIALS') && strlen(HEAD_TITLE_TESTIMONIALS)) $head_title_page = sprintf(HEAD_TITLE_TESTIMONIALS, $testimonial_id);

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_CUSTOMER_TESTIMONIALS, tep_get_all_get_params(array('testimonial_id'))));
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
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading" colspan="2"><?php echo HEADING_TITLE; ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td><br><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main">
            <?php
            if ($testimonial_id != '') {
                $full_testimonial = tep_db_query("select * FROM " . TABLE_CUSTOMER_TESTIMONIALS . " WHERE testimonials_id = '" . (int)$testimonial_id . "'");
            }
            else {
                $full_testimonial = tep_db_query("select * FROM " . TABLE_CUSTOMER_TESTIMONIALS . " WHERE status = '1'");
// Use this to randomize list
//              $full_testimonial = tep_db_query("select * FROM " . TABLE_CUSTOMER_TESTIMONIALS . " WHERE status = '1' order by rand()");
            }
            while ($testimonials = tep_db_fetch_array($full_testimonial)) {
                $testimonial_array[] = array('id' => $testimonials['testimonials_id'],
                                             'author' => $testimonials['testimonials_name'],
                                             'testimonial' => $testimonials['testimonials_html_text'],
                                             'word_count' => tep_word_count($testimonials['testimonials_html_text'], ' '),
                                             'url' => $testimonials['testimonials_url'],
                                             'url_title' => $testimonials['testimonials_url_title']);
                }
            require(DIR_WS_MODULES  . 'customer_testimonials.php');
            ?>
            </td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td align="right" class="main"><br><?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT, '', 'NONSSL') . '">' . tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></td>
      </tr>
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