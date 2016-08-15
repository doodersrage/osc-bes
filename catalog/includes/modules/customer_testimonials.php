<?php
/*
  $Id: customer_testimonials.php,v 1.3 2003/12/08 Exp $

  The Exchange Project - Community Made Shopping!
  http://www.theexchangeproject.org

  Copyright (c) 2000,2001 The Exchange Project

  Released under the GNU General Public License
  Contributed by http://www.seen-online.co.uk
*/
?>
<table border="0" cellspacing="0" cellpadding="2" width="100%">
<?php
  if (sizeof($testimonial_array) == '0') {
?>
  <tr>
    <td class="main"><?php echo TEXT_NO_TESTIMONIALS; ?></td>
  </tr>
<?php
  } else {
    for($i=0; $i<sizeof($testimonial_array); $i++) {
?>
  <tr>
    <td valign="top" class="main">
    <?php
    $info_box_contents = array();
    $info_box_contents[] = array('align' => 'left', 'text' => 'Testimonial by <b>' . $testimonial_array[$i]['author']);
    new contentBoxHeading($info_box_contents);

    if ($testimonial_id != '') {
          $testimonials_list = '<p><a href="'. tep_href_link(FILENAME_CUSTOMER_TESTIMONIALS, '', 'NONSSL'). '">'. TEXT_LINK_TESTIMONIALS. '</a>';
    }
    else {
        $testimonials_list = '';
    }

    $info_box_contents = array();
    if ($testimonial_array[$i]['url_title'] == '') {
        $testimonial_array[$i]['url_title'] = TEXT_LINK_TO_PAGE;
    }

    if (empty($testimonial_array[$i]['url'])) {
    $info_box_contents[][] = array('align' => 'left',
                                   'params' => 'class="smallText" width="100%" valign="top"',
                                   'text' => $testimonial_array[$i]['testimonial']. $testimonials_list);
    } elseif (substr($testimonial_array[$i]['url'], 0, 7) == 'mailto:') {
    $info_box_contents[][] = array('align' => 'left',
                                   'params' => 'class="smallText" width="100%" valign="top"',
                                   'text' => $testimonial_array[$i]['testimonial'].'<br><br><a target="_blank" href="'. $testimonial_array[$i]['url'] . '"><b>'. substr($testimonial_array[$i]['url'], 7). '</b></a>'. $testimonials_list);
    } else {
    $info_box_contents[][] = array('align' => 'left',
                                   'params' => 'class="smallText" width="100%" valign="top"',
                                   'text' => $testimonial_array[$i]['testimonial'].'<br><br><a target="_blank" href="'. $testimonial_array[$i]['url'] . '"><b>'. $testimonial_array[$i]['url_title']. '</b></a>'. $testimonials_list);
    }
    new contentBox($info_box_contents);
    ?>
    </td>
  </tr>
<?php
      if (($i+1) != sizeof($testimonial_array)) {
?>
  <tr>
    <td colspan="2" class="main">&nbsp;</td>
  </tr>
<?php
      }
    }
  }
?>
</table>
