<?php
/*
  $Id: customers.php,v 1.16 2003/07/09 01:18:53 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/
?>
<!-- customers //-->
          <tr>
            <td>
<?php
  $heading = array();
  $contents = array();

  $heading[] = array('text'  => BOX_HEADING_CUSTOMERS,
                     'link'  => tep_href_link(FILENAME_CUSTOMERS, 'selected_box=customers'));

  if ($selected_box == 'customers') {
    $contents[] = array('text'  => '<a href="' . tep_href_link(FILENAME_CUSTOMERS, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_CUSTOMERS_CUSTOMERS . '</a><br>' .
                '<a href="' . tep_href_link(FILENAME_ORDERS, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_CUSTOMERS_ORDERS . '</a><br>'.
// ########## Ajout/edite commande et compte client ##########
// ATTENTION de remplacer BOX_CUSTOMERS_ORDERS . '</a>');
// par BOX_CUSTOMERS_ORDERS . '</a><br>' .
                '<a href="' . tep_href_link(FILENAME_CREATE_ACCOUNT, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_MANUAL_ORDER_CREATE_ACCOUNT . '</a><br>' .
                '<a href="' . tep_href_link(FILENAME_CREATE_ORDER, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_MANUAL_ORDER_CREATE_ORDER . '</a><br>' .
// ########## END - Ajout/edite commande et compte client ##########
//begin PayPal_Shopping_Cart_IPN
                '<a href="' . tep_href_link(FILENAME_PAYPAL, '', 'NONSSL') . '" class="menuBoxContentLink">'. BOX_CUSTOMERS_PAYPAL .'</a><br>' .
//end PayPal_Shopping_Cart_IPN
               '<a href="' . tep_href_link(FILENAME_CUSTOMER_EXPORT, '', 'NONSSL') . '" class="menuBoxContentLink">' . 'Export Customers' . '</a>');
  }

  $box = new box;
  echo $box->menuBox($heading, $contents);
?>
            </td>
          </tr>
<!-- customers_eof //-->