<?php

/*

  $Id: column_left.php,v 1.15 2003/07/01 14:34:54 hpdl Exp $



  osCommerce, Open Source E-Commerce Solutions

  http://www.oscommerce.com



  Copyright (c) 2003 osCommerce



  Released under the GNU General Public License

*/

  // STS: ADD

  $sts_block_name = 'header2columnleft';

  require(STS_RESTART_CAPTURE);

  // STS: EOADD

require(DIR_WS_BOXES . 'search.php');



  // STS: ADD

  $sts_block_name = 'searchbox';

  require(STS_RESTART_CAPTURE);

  // STS: EOADD
 


  if ((USE_CACHE == 'true') && empty($SID)) {

    echo tep_cache_categories_box(MODULE_CACHE_LIFETIME);

  } else {

    include(DIR_WS_BOXES . 'categories.php');

  }



  // STS: ADD

  $sts_block_name = 'categorybox';

  require(STS_RESTART_CAPTURE);

  // STS: EOADD



  if ((USE_CACHE == 'true') && empty($SID)) {

    echo tep_cache_manufacturers_box();

  } else {

    include(DIR_WS_BOXES . 'manufacturers.php');

  }



  // STS: ADD

  $sts_block_name = 'manufacturerbox';

  require(STS_RESTART_CAPTURE);

  // STS: EOADD







 



  require(DIR_WS_BOXES . 'pages.php');

//require(DIR_WS_BOXES . 'information.php');



  // STS: ADD

  $sts_block_name = 'informationbox';

  require(STS_RESTART_CAPTURE);

  // STS: EOADD

  
  require(DIR_WS_BOXES . 'categories-xc.php');

  // STS: ADD

  $sts_block_name = 'dhmenu';

  require(STS_RESTART_CAPTURE);

  // STS: EOADD
?>
