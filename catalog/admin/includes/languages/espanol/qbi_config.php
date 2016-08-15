<?php
/*
$Id: qbi_config.php,v 2.10 2005/05/08 al Exp $
Language file: Spanish

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

// Messages
define('CONFIG_SUCCESS', 'Configuración exitoso');
define('CONFIG_QBI_VER', 'QBI versión');
define('CONFIG_SET_OPT', 'El base de datos ha sido actualizado. Por favor, verifica la configuración.');
define('CONFIG_SET_OPT2', 'Para continuar, tiene que haga click en "Submit" aunque no hubiera cambiado nada.');
// Button
define('CONFIG_SUBMIT', 'Actualizar');
// Form
// Sections
define('CONFIG_SEC_QBI', 'QBI');
define('CONFIG_SEC_ORDERS', 'Pedidos');
define('CONFIG_SEC_CUST', 'Clientes');
define('CONFIG_SEC_INV', 'Facturas');
define('CONFIG_SEC_ITEM', 'Productos');
define('CONFIG_SEC_SHIP', 'Envío');
define('CONFIG_SEC_TAX', 'Impuestos');
define('CONFIG_SEC_PMTS', 'Pagos');
define('CONFIG_SEC_PRODS', 'Productos de QB');
// QB Import Options
define('QBI_QB_VER_L', 'Versión de Quickbooks');
  define('QBI_QB_VER_1999', '1999-2000');
  define('QBI_QB_VER_2001', '2001-2002');
  define('QBI_QB_VER_2003', '2003 en adelante');
define('QBI_DL_IIF_L', 'Descargar archivo iif');
define('QBI_PROD_ROWS_L', 'Filas desplegadas de productos');
define('PRODS_SORT_L', 'Orden del dropdown de productos');
  define('PRODS_SORT_NAME', 'Nombre');
  define('PRODS_SORT_DESC', 'Descripción');
define('PRODS_WIDTH_L', 'Ancho del dropdown de productos');
define('QBI_LOG_L', 'Hacer archivo diagnostico');
// Orders
define ('ORDERS_STATUS_IMPORT_L', 'Importar pedidos con el estado');
  define ('CONFIG_STATUS_ANY', 'todos');
define ('QBI_STATUS_UPDATE_L', 'Poner al día el estado');
define ('QBI_CC_STATUS_SELECT_L', 'Cambiar el estado (tarjetas) a');
define ('QBI_MO_STATUS_SELECT_L', 'Cambiar el estado (cheques) a');
define ('QBI_EMAIL_SEND_L', 'Enviar email del nuevo estado');
define ('QBI_CC_CLEAR_L', 'Borar numero de tarjeta de credito');
// Customers
define('CUST_NAMEB_L', 'Numero de cliente (negocios)');
define('CUST_NAMER_L', 'Numero de cliente (domicilios)');
define('CUST_LIMIT_L', 'Limite de cliente');
define('CUST_TYPE_L', 'Tipo de cliente');
define('CUST_STATE_L', 'Usar codigos para los estados');
define('CUST_COUNTRY_L', 'Incluir paíz local');
define('CUST_COMPCON_L', 'Incluir companía y contacto');
define('CUST_PHONE_L', 'Importar fax como Teléfono Alt');
// Invoices
define('INVOICE_ACCT_L', 'Cuenta de factura');
define('INVOICE_SALESACCT_L', 'Cuenta de recibos de venta');
define('ORDERS_DOCNUM_L', 'Numero de factura');
define('ORDERS_PONUM_L', '\'PO Number\' en factura y recibo');
define('INVOICE_TOPRINT_L', 'Imprimir factura');
define('INVOICE_TERMSCC_L', 'Condiciones, pagado en linea');
define('INVOICE_TERMS_L', 'Condiciones, no prepagado');
define('INVOICE_REP_L', 'Representante');
define('INVOICE_FOB_L', 'Factura fob');
define('INVOICE_COMMENTS_L', 'Incluir comentarios del cliente');
define('INVOICE_MESSAGE_L', 'Mensaje para el cliente');
define('INVOICE_MEMO_L', 'Notas de factura');
// Items
define('ITEM_ACCT_L', 'Cuenta de ingresos de productos');
define('ITEM_ASSET_ACCT_L', 'Cuenta de activos de productos');
define('ITEM_CLASS_L', 'Clases de productos');
define('ITEM_COG_ACCT_L', 'Cuenta de COGS');
define('ITEM_OSC_LANG_L', 'Lenguaje de descripción');
  define('ITEM_LANG_DEF', 'Valor por defecto');
  define('ITEM_LANG_CUST', 'Del comprador');
define('ITEM_MATCH_L', 'Tipos de correspondencias');
  define('ITEM_MATCH_INV_L', 'Inventorio');
  define('ITEM_MATCH_NONINV_L', 'No inventorio');
  define('ITEM_MATCH_SERV_L', 'Servicios');
define('ITEM_DEFAULT_L', 'Usar producto por omisión');
  define('ITEM_DEFAULT_NAME_L', 'Nombre por omisión');
define('ITEM_IMPORT_TYPE_L', 'Tipo para exportación');
  define('ITEM_IMPORT_INV', 'Inventorio');
  define('ITEM_IMPORT_NONINV', 'No inventorio');
  define('ITEM_IMPORT_SERV', 'Servicios');
define('ITEM_ACTIVE_L', 'Solo exportar productos activos');
// Shipping
define('SHIP_NAME_L', 'Nombre de cargo de envío');
define('SHIP_DESC_L', 'Descripción de cargo de envío');
define('SHIP_ACCT_L', 'Cuenta de cargo de envío');
define('SHIP_CLASS_L', 'Clase de envío');
define('SHIP_TAX_L', 'Shipping taxable');
// Taxes
define('TAX_ON_L', 'Impuestos activados');
define('TAX_NAME_L', 'Nombre de impuesto');
define('TAX_AGENCY_L', 'Agencia de impuestos');
define('TAX_RATE_L', 'Tasa de impuestos');
define('TAX_LOOKUP_L', 'Usar mesa de nombres de impuestos');
// Payments
define('INVOICE_PMT_L', 'Importar pagos');
  define('INVOICE_PMT_NONE', 'No');
  define('INVOICE_PMT_PMT', 'Como Factura y Pago');
  define('INVOICE_PMT_SR', 'Como Recibo de Venta');
define('PMTS_MEMO_L', 'Notas de pago');

// Field comments
// QB Import Options
define('QBI_IMPORT_PMTS_C', '');
define('QBI_QB_VER_C', '');
define('QBI_DL_IIF_C', '');
define('QBI_PROD_ROWS_C', '');
define('PRODS_SORT_C', '');
define('PRODS_WIDTH_C', '');
define('QBI_LOG_C', '');
// Orders
define ('ORDERS_STATUS_IMPORT_C', '');
define('QBI_STATUS_UPDATE_C', '');
define('QBI_CC_STATUS_SELECT_C', '');
define('QBI_MO_STATUS_SELECT_C', '');
define('QBI_EMAIL_SEND_C', '');
define('QBI_CC_CLEAR_C', '');
// Customers
define('CUST_NAMEB_C', 'Vea la guía de usuarios.');
define('CUST_NAMER_C', 'Vea la guía de usuarios.');
define('CUST_LIMIT_C', '0 significa no límite.');
define('CUST_TYPE_C', '');
define('CUST_STATE_C', '');
define('CUST_COUNTRY_C', '');
define('CUST_COMPCON_C', '');
define('CUST_PHONE_C', '');
// Invoices
define('INVOICE_ACCT_C', '');
define('INVOICE_SALESACCT_C', '');
define('INVOICE_TOPRINT_C', '');
define('ORDERS_DOCNUM_C', '%I=Numero del pedido en osC');
define('ORDERS_PONUM_C', '%I=Numero del pedido en osC');
define('INVOICE_TERMSCC_C', '');
define('INVOICE_TERMS_C', '');
define('INVOICE_REP_C', '');
define('INVOICE_FOB_C', '');
define('INVOICE_COMMENTS_C', '');
define('INVOICE_MESSAGE_C', '');
define('INVOICE_MEMO_C', '');
// Items
define('ITEM_ACCT_C', '');
define('ITEM_ASSET_ACCT_C', '');
define('ITEM_CLASS_C', '');
define('ITEM_COG_ACCT_C', '');
define('ITEM_OSC_LANG_C', '');
define('ITEM_MATCH_C', '');
  define('ITEM_MATCH_INV_C', '');
  define('ITEM_MATCH_NONINV_C', '');
  define('ITEM_MATCH_SERV_C', '');
define('ITEM_DEFAULT_C', '');
  define('ITEM_DEFAULT_NAME_C', '');
define('ITEM_IMPORT_TYPE_C', '');
define('ITEM_ACTIVE_C', '');
// Shipping
define('SHIP_NAME_C', '');
define('SHIP_DESC_C', '');
define('SHIP_ACCT_C', '');
define('SHIP_CLASS_C', '');
define('SHIP_TAX_C', '');
// Taxes
define('TAX_ON_C', '');
define('TAX_NAME_C', '');
define('TAX_AGENCY_C', '');
define('TAX_RATE_C', '%');
define('TAX_LOOKUP_C', 'No está listo');
// Payments
define('INVOICE_PMT_C', '');
define('PMTS_MEMO_C', '');
?>