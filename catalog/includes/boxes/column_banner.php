<?php

/*

// WebMakers.com Added - Column Banner

// Created by: Linda McGrath osCOMMERCE@WebMakers.com

// Test at: http://www.thewebmakerscorner.com

*/

  if ($banners = tep_banner_exists('dynamic', isset($banners_group)?$banners_group:'box', isset($banners_count)?$banners_count:1)) {  // number of displayed banners may be increased by changing parameters here
?>
<!-- column_banner //-->
          <tr>
            <td>
<?php
    $info_box_contents = array();
    $info_box_contents[] = array('align' => 'center',
                                 'text'  => BOX_HEADING_COLUMN_BANNER
                                );
    new infoBoxHeading($info_box_contents, false, false);

    foreach($banners as $banner) {  // modified by splautz for displaying of multiple banners
      $info_box_contents = array();
      $info_box_contents[] = array('align' => 'center',
                                   'text'  => tep_display_banner('static', $banner)
                                  );
      new infoBox($info_box_contents);
    }
?>
            </td>
          </tr>
<!-- column_banner_eof //-->
<?php
  }
?>