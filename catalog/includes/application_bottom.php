<?php
/*
  $Id: application_bottom.php,v 1.14 2003/02/10 22:30:41 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  // Store output between footer.php and application_bottom.php
  $sts_block_name = 'footer2applicationbottom';
  require(STS_STOP_CAPTURE);

  // Print everything out
  require(STS_DISPLAY_OUTPUT);

// added by splautz to remember category path for use in tep_get_product_path
  $previous_cPath = $cPath;
  if (!tep_session_is_registered('previous_cPath')) tep_session_register('previous_cPath');

// close session (store variables)
  tep_session_close();

  if (STORE_PAGE_PARSE_TIME == 'true') {
    $time_start = explode(' ', PAGE_PARSE_START_TIME);
    $time_end = explode(' ', microtime());
    $parse_time = number_format(($time_end[1] + $time_end[0] - ($time_start[1] + $time_start[0])), 3);
    error_log(strftime(STORE_PARSE_DATE_TIME_FORMAT) . ' - ' . getenv('REQUEST_URI') . ' (' . $parse_time . 's)' . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);

    if (DISPLAY_PAGE_PARSE_TIME == 'true') {
      echo '<span class="smallText">Parse Time: ' . $parse_time . 's</span>';
    }
  }

  if ( (GZIP_COMPRESSION == 'true') && ($ext_zlib_loaded == true) && ($ini_zlib_output_compression < 1) ) {
    if ( (PHP_VERSION < '4.0.4') && (PHP_VERSION >= '4') ) {
      tep_gzip_output(GZIP_LEVEL);
    }
  }
?>
