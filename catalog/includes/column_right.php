<?php

/*

  $Id: column_right.php,v 1.17 2003/06/09 22:06:41 hpdl Exp $



  osCommerce, Open Source E-Commerce Solutions

  http://www.oscommerce.com



  Copyright (c) 2003 osCommerce



  Released under the GNU General Public License

*/



  // STS: ADD

  $sts_block_name = 'columnleft2columnright';

  require(STS_RESTART_CAPTURE);

  // STS: EOADD



  require(DIR_WS_BOXES . 'shopping_cart.php');



  // STS: ADD

  $sts_block_name = 'cartbox';

  require(STS_RESTART_CAPTURE);

  // STS: EOADD



// Wishlist

  if($wishList->count_wishlist() != '0') require(DIR_WS_BOXES . 'wishlist.php');



  // STS: ADD

  $sts_block_name = 'wishlistbox';

  require(STS_RESTART_CAPTURE);

  // STS: EOADD



  if (isset($HTTP_GET_VARS['products_id'])) include(DIR_WS_BOXES . 'manufacturer_info.php');



  // STS: ADD

  $sts_block_name = 'maninfobox';

  require(STS_RESTART_CAPTURE);

  // STS: EOADD



  if (tep_session_is_registered('customer_id')) include(DIR_WS_BOXES . 'order_history.php');



  // STS: ADD

  $sts_block_name = 'orderhistorybox';

  require(STS_RESTART_CAPTURE);

  // STS: EOADD



  require(DIR_WS_BOXES . 'whats_new.php');



  // STS: ADD

  $sts_block_name = 'whatsnewbox';

  require(STS_RESTART_CAPTURE);

  // STS: EOADD 

 include(DIR_WS_BOXES . 'customer_testimonials.php');



  // STS: ADD

  $sts_block_name = 'testimonialsbox';

  require(STS_RESTART_CAPTURE);

  // STS: EOADD



  if (isset($HTTP_GET_VARS['products_id'])) {

    if (tep_session_is_registered('customer_id')) {

      $check_query = tep_db_query("select count(*) as count from " . TABLE_CUSTOMERS_INFO . " where customers_info_id = '" . (int)$customer_id . "' and global_product_notifications = '1'");

      $check = tep_db_fetch_array($check_query);

      if ($check['count'] > 0) {

        include(DIR_WS_BOXES . 'best_sellers.php');

      } else {

        include(DIR_WS_BOXES . 'product_notifications.php');

      }

    } else {

      include(DIR_WS_BOXES . 'product_notifications.php');

    }

  } else {

    include(DIR_WS_BOXES . 'best_sellers.php');

  }



  // STS: ADD

  $sts_block_name = 'bestsellersbox';

  require(STS_RESTART_CAPTURE);

  // STS: EOADD



  if (isset($HTTP_GET_VARS['products_id'])) {

    if (basename($PHP_SELF) != FILENAME_TELL_A_FRIEND) include(DIR_WS_BOXES . 'tell_a_friend.php');

  } else {

    include(DIR_WS_BOXES . 'specials.php');

  }



  // STS: ADD

  $sts_block_name = 'specialfriendbox';

  require(STS_RESTART_CAPTURE);

  // STS: EOADD



  require(DIR_WS_BOXES . 'reviews.php');



  // STS: ADD

  $sts_block_name = 'reviewsbox';

  require(STS_RESTART_CAPTURE);

  // STS: EOADD



  if (substr(basename($PHP_SELF), 0, 8) != 'checkout') {

    include(DIR_WS_BOXES . 'languages.php');



    // STS: ADD

    $sts_block_name = 'languagebox';

    require(STS_RESTART_CAPTURE);

    // STS: EOADD



    include(DIR_WS_BOXES . 'currencies.php');



    // STS: ADD

    $sts_block_name = 'currenciesbox';

    require(STS_RESTART_CAPTURE);

    // STS: EOADD



  }

?>

