<?php
/*
  $Id: catalog.php,v 1.21 2003/07/09 01:18:53 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/
?>
<!-- catalog //-->
          <tr>
            <td>
<?php
  $heading = array();
  $contents = array();

  $heading[] = array('text'  => BOX_HEADING_CATALOG,
                     'link'  => tep_href_link(FILENAME_CATEGORIES, 'selected_box=catalog'));

  if ($selected_box == 'catalog') {
    $contents[] = array('text'  => '<a href="' . tep_href_link(FILENAME_CATEGORIES, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_CATALOG_CATEGORIES_PRODUCTS . '</a><br>' .
// KIKOLEPPARD for multilanguage support Line Added New Atrributes Manager
                       	           '<a href="' . tep_href_link('new_attributes.php', '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_CATALOG_CATEGORIES_ATTRIBUTE_MANAGER . '</a><br>' .
// KIKOLEPPARD for multilanguage support Line Added New Atrributes Manager
                                   '<a href="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_CATALOG_CATEGORIES_PRODUCTS_ATTRIBUTES . '</a><br>' .
//DANIEL:begin
                                   '<a href="' . tep_href_link(FILENAME_RELATED_PRODUCTS, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_CATALOG_CATEGORIES_RELATED_PRODUCTS . '</a><br>' .
//DANIEL:end
                                   '<a href="' . tep_href_link(FILENAME_MANUFACTURERS, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_CATALOG_MANUFACTURERS . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_REVIEWS, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_CATALOG_REVIEWS . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_SPECIALS, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_CATALOG_SPECIALS . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_PRODUCTS_EXPECTED, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_CATALOG_PRODUCTS_EXPECTED . '</a><br>' .
// added by splautz for image page
                                   '<a href="' . tep_href_link(FILENAME_IMAGES, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_CATALOG_IMAGES . '</a><br>' .
// START: Product Extra Fields
                                   '<a href="' . tep_href_link(FILENAME_PRODUCTS_EXTRA_FIELDS, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_CATALOG_PRODUCTS_EXTRA_FIELDS . '</a><br>' .
// END: Product Extra Fields
                                   '<hr size="1">' .
// Google XML SiteMaps Admin
                                   '<a href="' . tep_href_link(FILENAME_GOOGLESITEMAP, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_CATALOG_GOOGLESITEMAP . '</a><br>' .
// Store Feeds
                                   '<a href="' . tep_href_link('feeders.php', '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_CATALOG_STORE_FEEDS . '</a><br>' .
// Easy populate									
                                   '<a href="' . tep_href_link(FILENAME_IMP_EXP_CATALOG, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_CATALOG_IMP_EXP . '</a><br>' .
// END Easy populate
// QBI
                                   '<a href="' . tep_href_link(FILENAME_QBI, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_CATALOG_QBI . '</a>');
  }

  $box = new box;
  echo $box->menuBox($heading, $contents);
?>
            </td>
          </tr>
<!-- catalog_eof //-->