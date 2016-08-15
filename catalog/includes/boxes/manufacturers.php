<?php
/*
  $Id: manufacturers.php,v 1.1.1.1.2.4 2004/12/10 17:25:00 admin Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
// #################### Added Enable / Disable Categories ##############  // modified by splautz for sort & status
//  $manufacturers_query = tep_db_query("select manufacturers_id, manufacturers_name from " . TABLE_MANUFACTURERS . " order by manufacturers_name");

// Use this to list only manufacturers that have active products
  $manufacturers_query = tep_db_query("select distinct m.manufacturers_id, m.manufacturers_name from " . TABLE_MANUFACTURERS . " m left join " . TABLE_PRODUCTS . " p on m.manufacturers_id = p.manufacturers_id left join " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c on p.products_id = p2c.products_id left join " . TABLE_CATEGORIES . " c on p2c.categories_id = c.categories_id where m.manufacturers_status = '1' and c.categories_status = '1' and p.products_status = '1' order by COALESCE(m.sort_order,10000), m.manufacturers_name");
// Use this to list all active manufacturers
// $manufacturers_query = tep_db_query("select m.manufacturers_id, m.manufacturers_name from " . TABLE_MANUFACTURERS . " m where m.manufacturers_status = '1' order by COALESCE(m.sort_order,10000), m.manufacturers_name");
// #################### End Added Enable / Disable Categories ##############
  if ($number_of_rows = tep_db_num_rows($manufacturers_query)) {
?>
<!-- manufacturers //-->
          <tr>
            <td>
<?php
    $info_box_contents = array();
    $info_box_contents[] = array('text' => BOX_HEADING_MANUFACTURERS);

    new infoBoxHeading($info_box_contents, false, false);

    if ($number_of_rows <= MAX_DISPLAY_MANUFACTURERS_IN_A_LIST) {
// Display a list
      $manufacturers_list = '';
      while ($manufacturers = tep_db_fetch_array($manufacturers_query)) {
        $manufacturers_name = ((strlen($manufacturers['manufacturers_name']) > MAX_DISPLAY_MANUFACTURER_NAME_LEN) ? substr($manufacturers['manufacturers_name'], 0, MAX_DISPLAY_MANUFACTURER_NAME_LEN) . '..' : $manufacturers['manufacturers_name']);
        if (isset($HTTP_GET_VARS['manufacturers_id']) && ($HTTP_GET_VARS['manufacturers_id'] == $manufacturers['manufacturers_id'])) $manufacturers_name = '<b>' . $manufacturers_name .'</b>';
        $manufacturers_list .= '<a href="' . tep_href_link(FILENAME_DEFAULT, 'manufacturers_id=' . $manufacturers['manufacturers_id']) . '">' . $manufacturers_name . '</a><br>';
      }

      $manufacturers_list = substr($manufacturers_list, 0, -4);

      $info_box_contents = array();
      $info_box_contents[] = array('text' => $manufacturers_list);
    } else {
// Display a drop-down
      $manufacturers_array = array();
      if (MAX_MANUFACTURERS_LIST < 2) {
        $manufacturers_array[] = array('id' => '', 'text' => PULL_DOWN_DEFAULT);
      }

      while ($manufacturers = tep_db_fetch_array($manufacturers_query)) {
        $manufacturers_name = ((strlen($manufacturers['manufacturers_name']) > MAX_DISPLAY_MANUFACTURER_NAME_LEN) ? substr($manufacturers['manufacturers_name'], 0, MAX_DISPLAY_MANUFACTURER_NAME_LEN) . '..' : $manufacturers['manufacturers_name']);
        $manufacturers_array[] = array('id' => $manufacturers['manufacturers_id'],
                                       'text' => $manufacturers_name);
      }

      $info_box_contents = array();
// modified by splautz for seo urls
      $info_box_contents[] = array('form' => tep_draw_form('manufacturers', tep_href_link(FILENAME_REDIRECT, '', 'NONSSL', false, false), 'get'),
                                   'text' => tep_draw_hidden_field('action','rewrite').tep_draw_hidden_field('goto',FILENAME_DEFAULT).tep_draw_pull_down_menu('manufacturers_id', $manufacturers_array, (isset($HTTP_GET_VARS['manufacturers_id']) ? $HTTP_GET_VARS['manufacturers_id'] : ''), 'onChange="this.form.submit();" size="' . MAX_MANUFACTURERS_LIST . '" style="width: 100%"') . tep_hide_session_id());

    }

    new infoBox($info_box_contents);
?>
            </td>
          </tr>
<!-- manufacturers_eof //-->
<?php
  }
?>