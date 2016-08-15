<?php
/*
  $Id: logoff.php,v 1.13 2003/06/05 23:28:24 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_LOGOFF);

  $breadcrumb->add(NAVBAR_TITLE);

  tep_session_unregister('customer_id');
  tep_session_unregister('customer_default_address_id');
  tep_session_unregister('customer_first_name');
  tep_session_unregister('customer_country_id');
  tep_session_unregister('customer_zone_id');
  if (tep_session_is_registered('sendto')) tep_session_unregister('sendto');
  if (tep_session_is_registered('billto')) tep_session_unregister('billto');
  if (tep_session_is_registered('shipping')) tep_session_unregister('shipping');
  if (tep_session_is_registered('payment')) tep_session_unregister('payment');
  if (tep_session_is_registered('comments')) tep_session_unregister('comments');
// PayPal WPP
  if (tep_session_is_registered('paypal_token')) { tep_session_unregister('paypal_token'); }
  if (tep_session_is_registered('pp_payer_id')) { tep_session_unregister('pp_payer_id'); }
  if (tep_session_is_registered('pp_token')) { tep_session_unregister('pp_token'); }
  if (tep_session_is_registered('pp_payer_status')) { tep_session_unregister('pp_payer_status'); }
  if (tep_session_is_registered('pp_firstname')) { tep_session_unregister('pp_firstname'); }
  if (tep_session_is_registered('pp_lastname')) { tep_session_unregister('pp_lastname'); }
  if (tep_session_is_registered('pp_stree1')) { tep_session_unregister('pp_street1'); }
  if (tep_session_is_registered('pp_street2')) { tep_session_unregister('pp_street2'); }
  if (tep_session_is_registered('pp_city')) { tep_session_unregister('pp_city'); }
  if (tep_session_is_registered('pp_state')) { tep_session_unregister('pp_state'); }
  if (tep_session_is_registered('pp_zip')) { tep_session_unregister('pp_zip'); }
  if (tep_session_is_registered('pp_country')) { tep_session_unregister('pp_country'); }
  if (tep_session_is_registered('pp_phone')) { tep_session_unregister('pp_phone'); }
  if (tep_session_is_registered('pp_email')) { tep_session_unregister('pp_email'); }
  if (tep_session_is_registered('pp_business')) { tep_session_unregister('pp_business'); }
// ###### Added CCGV Contribution #########
  tep_session_unregister('gv_id');
  tep_session_unregister('cc_id');
// ###### End Added CCGV Contribution #########

  $cart->reset();
// Wishlist
  $wishList->reset();
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
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td><?php echo tep_image(DIR_WS_IMAGES . 'table_background_man_on_board.gif', HEADING_TITLE); ?></td>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td class="pageHeading" align="center"><?php echo HEADING_TITLE; ?></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo TEXT_MAIN; ?></td>
              </tr>
            </table></td>
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
                <td align="right"><?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT) . '">' . tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></td>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
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