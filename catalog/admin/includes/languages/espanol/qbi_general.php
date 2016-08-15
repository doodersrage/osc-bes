<?php
/*
$Id: qbi_general.php,v 2.10 2005/05/08 al Exp $
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

define('HEADING_TITLE', 'Importar Quickbooks QBI');

// Menu
define('QBI_MENU_CREATE', 'Hacer archivo iif');
define('QBI_MENU_PRODUCTS', 'Arreglar Productos');
define('QBI_MENU_SHIP', 'Arreglar Metidos de Envio');
define('QBI_MENU_PRODUCTSMATCH', 'Corresponder Productos');
define('QBI_MENU_SHIPMATCH', 'Corresponder Metidos de Envio');
define('QBI_MENU_CONFIG', 'Configurar');
define('QBI_MENU_UTILITIES', 'Herramientas');

// Menu (new)
define('MENU_1', 'Hacer iif');
define('MENU_2', 'Arreglar');
define('MENU_2A', 'Productos');
define('MENU_2B', 'Descuentos/Cargos');
define('MENU_2C', 'Metidos de Envio');
define('MENU_2D', 'Pago');
define('MENU_3', 'Corresponder');
define('MENU_3A', 'Productos');
define('MENU_3B', 'Descuentos/Cargos');
define('MENU_3C', 'Metidos de Envio');
define('MENU_3D', 'Pago');
define('MENU_4', 'Configurar');
define('MENU_5', 'Sobre');

// Setup files
define('SETUP_FILE_FOUND1', 'Archivo iif');
define('SETUP_FILE_FOUND2', ' encontrado. Importar ahora?');
define('SETUP_FILE_MISSING', 'Archivo iif no encontrado.');
define('SETUP_FILE_BUTTON', 'Importar Archivo iif');
define('SETUP_SUCCESS', 'Actualización exitoso!');
define('SETUP_NAME', 'Nombre');
define('SETUP_DESC', 'Descripción');
define('SETUP_ACCT', 'Cuenta');
define('SETUP_ACTION', 'Acción');
define('SETUP_NO_CHANGE', 'No cambiado');
define('SETUP_UPDATED', 'Actualizado');
define('SETUP_ADDED', 'Agregado');

// Match
define('MATCH_BUTTON', 'Actualizar Correspondencias En Este Página');
define('MATCH_PAGE', 'Página de resultados:');
define('MATCH_PREV', 'Previo');
define('MATCH_NEXT', 'Próxiomo');
define('MATCH_SUCCESS', 'Correspondencias actualizadas.');
define('MATCH_OSC', 'osCommerce');
define('MATCH_QB', 'Quickbooks');

// Warnings
define('WARN_CONFIGURE', 'Tiene que configurar QB Import antes de uso.');
define('WARN_CONFIGURE_LINK', 'Configurar QB Import ahora.');

// Errors
define('ERROR_DIRECTORY_NOT_WRITEABLE', 'Error: No puedo escribir en este directorio. Asigner permisos de escritura en: %s');
define('ERROR_DIRECTORY_DOES_NOT_EXIST', 'Error: No existe el directorio: %s');
?>