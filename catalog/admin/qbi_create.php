<?php
/*
$Id: qbi_create.php,v 2.10 2005/05/08 al Exp $

Quickbooks Import QBI
contribution for osCommerce
ver 2.10 May 8, 2005
(c) 2005 Adam Liberman
www.libermansound.com
info@libermansound.com
Please use the osC forum for support.
Released under the GNU General Public License

    This file is part of Quickbooks Import QBI.

    Quickbooks Import QBI is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    Quickbooks Import QBI is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Quickbooks Import QBI; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

require('includes/application_top.php');
require(DIR_WS_LANGUAGES . $language . '/qbi_general.php');
require(DIR_WS_LANGUAGES . $language . '/orders.php'); // needed for email text
require(DIR_WS_INCLUDES . 'qbi_version.php');
require(DIR_WS_INCLUDES . 'qbi_definitions.php');
require(DIR_WS_FUNCTIONS . 'qbi_functions.php');
require(DIR_WS_CLASSES . 'qbi_classes.php');
require(DIR_WS_INCLUDES . 'qbi_engine_orders.php');
require(DIR_WS_INCLUDES . 'qbi_engine_cust.php');
require(DIR_WS_INCLUDES . 'qbi_engine_prod.php');
require(DIR_WS_INCLUDES . 'qbi_page_top.php');
require(DIR_WS_INCLUDES . 'qbi_menu_tabs.php');

// Orders
for ($i=0; $i<=2; $i++) {
  $iif_form=new iif_form;
  $iif_form->where_clause("qbi_imported='".$i."'");
  if ($i==0 AND ORDERS_STATUS_IMPORT!=0) $iif_form->where_clause("orders_status='".ORDERS_STATUS_IMPORT."'");
  $iif_form->form_make('orders','orders_id','date_purchased',$i);
  echo $iif_form->form_display();
}

// Products
for ($i=0; $i<=2; $i++) {
  $iif_form=new iif_form;
  $iif_form->where_clause("qbi_imported='".$i."'");
  if (ITEM_ACTIVE==1) $iif_form->where_clause("products_status='1'");
  $iif_form->form_make('products','products_id','products_date_added',$i);
  echo $iif_form->form_display();
}

// Customers
$iif_form=new iif_form;
$iif_form->form_make('customers','customers_id','',2);
echo $iif_form->form_display();

require(DIR_WS_INCLUDES . 'qbi_page_bot.php');
?>