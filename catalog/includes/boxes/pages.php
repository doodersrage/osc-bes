<!-- pages //-->
          <tr>
            <td>
<?php
  include_once('includes/application_top.php');

  $page_query = tep_db_query("select pd.pages_name, p.pages_id, p.pages_forward from " . TABLE_PAGES . " p, " . TABLE_PAGES_DESCRIPTION . " pd where p.pages_id = pd.pages_id and p.pages_status = '1' and pd.language_id = '" . (int)$languages_id . "' order by COALESCE(p.sort_order,1000), p.pages_id");

  $page_menu_text = '';
  while($page = tep_db_fetch_array($page_query)){
    if(tep_not_null($page["pages_forward"])) {
      $page_forward = explode('?', $page["pages_forward"]);
      $page_menu_text .= '<a href="' . tep_href_link($page_forward[0],isset($page_forward[1])?$page_forward[1]:'') . '">' . $page["pages_name"] . '</a><br>';
    } elseif($page["pages_id"]!=1)
      $page_menu_text .= '<a href="' . tep_href_link(FILENAME_PAGES, 'pages_id='.$page["pages_id"]) . '">' . $page["pages_name"] . '</a><br>';
  }

  $info_box_contents = array();
  $info_box_contents[] = array('align' => 'center',
                               'text'  => BOX_HEADING_PAGES
                              );
  new infoBoxHeading($info_box_contents, false, false);

  $info_box_contents = array();
    $info_box_contents[] = array('align' => 'left',
                                 'text'  => $page_menu_text,
                                );
  new infoBox($info_box_contents);
?>
            </td>
          </tr>
<!-- pages_eof //-->