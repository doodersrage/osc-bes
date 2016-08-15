<?php
/*
  $Id: create_account_process.php,v 1 12:01 AM 17/08/2003 frankl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('EMAIL_PASS_1', 'Bitte notiere Dein Passwort: ');
define('EMAIL_PASS_2', ' In Deinem Account kannst Du es dann später ändern.' . "\n\n");

define('NAVBAR_TITLE', 'Account anlegen');
define('HEADING_TITLE', 'Account-Informationen');
define('HEADING_NEW', 'Bestellung');
define('NAVBAR_NEW_TITLE', 'Bestellung');

define('EMAIL_SUBJECT', 'Willkommen bei ' . STORE_NAME);
define('EMAIL_GREET_MR', 'Hallo ' . stripslashes($HTTP_POST_VARS['firstname']) . ',' . "\n\n");
define('EMAIL_GREET_MS', 'Hallo ' . stripslashes($HTTP_POST_VARS['firstname']) . ',' . "\n\n");
define('EMAIL_GREET_NONE', 'Hallo ' . stripslashes($HTTP_POST_VARS['firstname']) . ',' . "\n\n");
define('EMAIL_WELCOME', 'willkommen bei <b>' . STORE_NAME . '</b>.' . "\n\n");
define('EMAIL_TEXT', 'Du kannst nun unseren <b>Online-Service</b> nutzen. Der Service bietet unter anderem:' . "\n\n" . '<li><b>Kundenwarenkorb</b> - Jeder Artikel bleibt registriert bis Du zur Kasse gehst oder die Produkte aus dem Warenkorb entfernst.' . "\n" . '<li><b>Addressbuch</b> - Wir können jetzt die Produkte zu der von Dir ausgesuchten Adresse senden. Der perfekte Weg ein Geburtstagsgeschenk zu versenden.' . "\n" . '<li><b>Vorherige Bestellungen</b> - Du kannst jederzeit Deine vorherigen Bestellungen überprüfen.' . "\n" . '<li><b>Meinungen über Produkte</b> - Teile Deine Meinung zu unseren Produkten mit anderen Kunden.' . "\n\n");
define('EMAIL_CONTACT', 'Falls Du Fragen zu unserem Kunden-Service hast, wende Dich bitte an uns: ' . STORE_OWNER_EMAIL_ADDRESS . '.' . "\n\n");
define('EMAIL_WARNING', '<b>Achtung:</b> Diese eMail-Adresse wurde uns von einem Kunden bekanntgegeben. Falls Du Dich nicht angemeldet hast, sende bitte eine eMail an ' . STORE_OWNER_EMAIL_ADDRESS . '.' . "\n");

?>