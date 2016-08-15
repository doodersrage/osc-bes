<?php
/*
  $Id: customer_testimonials.php,v 1.00 2002/12/27 Exp $

  The Exchange Project - Community Made Shopping!
  http://www.theexchangeproject.org

  Copyright (c) 2000,2001 The Exchange Project

  Released under the GNU General Public License
  Contributed by http://www.seen-online.co.uk
*/
?>
<!-- customer testimonials //-->
<?php
  if ($random_testimonial = tep_random_select("select * FROM " . TABLE_CUSTOMER_TESTIMONIALS . " WHERE status = 1 ORDER BY rand() LIMIT 1")) {
?>
          <tr>
            <td>
<?php
    $info_box_contents = array();
    $info_box_contents[] = array('align' => 'left',
                                 'text'  => BOX_HEADING_CUSTOMER_TESTIMONIALS
                                );
    new infoBoxHeading($info_box_contents, false, false, tep_href_link(FILENAME_CUSTOMER_TESTIMONIALS, '', 'NONSSL'));

    $testimonial = substr($random_testimonial['testimonials_html_text'], 0, 100);

    $info_box_contents = array();
    $info_box_contents[] = array('align' => 'left',
                                 'text' => '<a href="' . tep_href_link(FILENAME_CUSTOMER_TESTIMONIALS, 'testimonial_id=' . $random_testimonial['testimonials_id']) . '">' . $testimonial . '...<br>Read more...</a><br><b>'.$random_testimonial['testimonials_name'].'</b>'
                                 );
    new infoBox($info_box_contents);
?>
            </td>
          </tr>
<?php
  }
?>
<!-- customer testimonials_eof //-->
