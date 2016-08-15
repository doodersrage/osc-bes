<?php
/*
  $Id: sts_stop_capture.php,v 1.1 2003/09/22 05:16:09 jhtalk Exp $

osCommerce, Open Source E-Commerce Solutions
http://www.oscommerce.com

Copyright (c) 2003 osCommerce

Released under the GNU General Public License
*/

/*

  Simple Template System (STS) - Copyright (c) 2004 Brian Gallagher - brian@diamondsea.com

*/

  // Store captured output to $sts_capture
  $sts_block[$sts_block_name] = ob_get_contents();
  ob_end_clean(); // Clear out the capture buffer

?>
