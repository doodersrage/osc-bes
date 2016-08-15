//german language file for Step-by-step-orders
//to be added in:
//.../catalog/admin/includes/languages/german.php

// pull down default text
define('PULL_DOWN_DEFAULT', 'Bitte auswählen');
define('TYPE_BELOW', 'Bitte eingeben');

define('JS_ERROR', 'Es sind Fehler bei der Verarbeitung ihrer Daten aufgetreten!\nBitte korrigieren Sie die folgenden Fehler:\n\n');

define('JS_GENDER', '* \'Anrede\' muss ausgewählt sein.\n');
define('JS_FIRST_NAME', '* Der \'Vorname\' muss mindestens ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' Buchstaben lang sein.\n');
define('JS_LAST_NAME', '* Der \'Nachname\' muss mindestens' . ENTRY_LAST_NAME_MIN_LENGTH . ' Buchstaben lang sein.\n');
define('JS_DOB', '* Das \'Geburtsdatum\' muss im Format xx/xx/xxxx (Monat/Tag/Jahr) stehen.\n');
define('JS_EMAIL_ADDRESS', '* Die \'E-Mail Addresse\' muss mindestens ' . ENTRY_EMAIL_ADDRESS_MIN_LENGTH . ' Buchstaben lang sein.\n');
define('JS_ADDRESS', '* Die \'Strasse\' muss mindestens ' . ENTRY_STREET_ADDRESS_MIN_LENGTH . ' Buchstaben lang sein..\n');
define('JS_POST_CODE', '* Die \'Postleitzahl\' muss mindestens ' . ENTRY_POSTCODE_MIN_LENGTH . ' Buchstaben lang sein..\n');
define('JS_CITY', '* Der \'Ortsteil\' muss mindestens ' . ENTRY_CITY_MIN_LENGTH . ' Buchstaben lang sein..\n');
define('JS_STATE', '* \'Bundesland\' muss ausgewählt sein.\n');
define('JS_STATE_SELECT', '-- Bitte auswählen --');
define('JS_ZONE', '* Bitte wählen Sie ein \'Bundesland\' aus der Liste.\n');
define('JS_COUNTRY', '* Bitte wählen Sie ein \'Land\' aus.\n');
define('JS_TELEPHONE', '* Die \'Telefonnummer\' muss mindestens ' . ENTRY_TELEPHONE_MIN_LENGTH . ' Zeichen lang sein.\n');
define('JS_PASSWORD', '* \'Passwort\' und \'Passwortbestätigung\' müssen gleich sein und mindestens ' . ENTRY_PASSWORD_MIN_LENGTH . ' Zeichen lang sein.\n');

define('CATEGORY_COMPANY', 'Firma');
define('CATEGORY_PERSONAL', 'Persönliche Angaben');
define('CATEGORY_ADDRESS', 'Addresse');
define('CATEGORY_CONTACT', 'Kontaktinformation');
define('CATEGORY_OPTIONS', 'Optionen');
define('CATEGORY_PASSWORD', 'Passwort');
define('CATEGORY_CORRECT', 'Wenn dies der richtige Kunde ist, drücken Sie unten auf \'Bestätigen\'.');
define('ENTRY_CUSTOMERS_ID', 'ID:');
define('ENTRY_CUSTOMERS_ID_TEXT', '&nbsp;<small><font color="#AABBDD">benötigt</font></small>');
define('ENTRY_COMPANY', 'Firmenname:');
define('ENTRY_COMPANY_ERROR', '');
define('ENTRY_COMPANY_TEXT', '');
define('ENTRY_GENDER', 'Anrede:');
define('ENTRY_GENDER_ERROR', '&nbsp;<small><font color="#AABBDD">benötigt</font></small>');
define('ENTRY_GENDER_TEXT', '&nbsp;<small><font color="#AABBDD">benötigt</font></small>');
define('ENTRY_FIRST_NAME', 'Vorname:');
define('ENTRY_FIRST_NAME_ERROR', '&nbsp;<small><font color="#FF0000">min ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' Zeichen</font></small>');
define('ENTRY_FIRST_NAME_TEXT', '&nbsp;<small><font color="#AABBDD">benötigt</font></small>');
define('ENTRY_LAST_NAME', 'Nachname:');
define('ENTRY_LAST_NAME_ERROR', '&nbsp;<small><font color="#FF0000">min ' . ENTRY_LAST_NAME_MIN_LENGTH . ' Zeichen</font></small>');
define('ENTRY_LAST_NAME_TEXT', '&nbsp;<small><font color="#AABBDD">benötigt</font></small>');
define('ENTRY_DATE_OF_BIRTH', 'Geburtsdatum:');
define('ENTRY_DATE_OF_BIRTH_ERROR', '&nbsp;<small><font color="#FF0000">(z.B. 05/21/1970)</font></small>');
define('ENTRY_DATE_OF_BIRTH_TEXT', '&nbsp;<small>(z.B. 05/21/1970) <font color="#AABBDD">benötigt</font></small>');
define('ENTRY_EMAIL_ADDRESS', 'E-Mail Addresse:');
define('ENTRY_EMAIL_ADDRESS_ERROR', '&nbsp;<small><font color="#FF0000">min ' . ENTRY_EMAIL_ADDRESS_MIN_LENGTH . ' Zeichen</font></small>');
define('ENTRY_EMAIL_ADDRESS_CHECK_ERROR', '&nbsp;<small><font color="#FF0000">Die E-Mail Adresse scheint ungültig zu sein!</font></small>');
define('ENTRY_EMAIL_ADDRESS_ERROR_EXISTS', '&nbsp;<small><font color="#FF0000">Die E-Mail Adresse ist bereits vergeben!</font></small>');
define('ENTRY_EMAIL_ADDRESS_TEXT', '&nbsp;<small><font color="#AABBDD">benötigt</font></small>');
define('ENTRY_STREET_ADDRESS', 'Strasse:');
define('ENTRY_STREET_ADDRESS_ERROR', '&nbsp;<small><font color="#FF0000">min ' . ENTRY_STREET_ADDRESS_MIN_LENGTH . ' Zeichen</font></small>');
define('ENTRY_STREET_ADDRESS_TEXT', '&nbsp;<small><font color="#AABBDD">benötigt</font></small>');
define('ENTRY_SUBURB', 'Ortsteil:');
define('ENTRY_SUBURB_ERROR', '');
define('ENTRY_SUBURB_TEXT', '');
define('ENTRY_POST_CODE', 'Postleitzahl:');
define('ENTRY_POST_CODE_ERROR', '&nbsp;<small><font color="#FF0000">min ' . ENTRY_POSTCODE_MIN_LENGTH . ' Zeichen</font></small>');
define('ENTRY_POST_CODE_TEXT', '&nbsp;<small><font color="#AABBDD">benötigt</font></small>');
define('ENTRY_CITY', 'Ort:');
define('ENTRY_CITY_ERROR', '&nbsp;<small><font color="#FF0000">min ' . ENTRY_CITY_MIN_LENGTH . ' Zeichen</font></small>');
define('ENTRY_CITY_TEXT', '&nbsp;<small><font color="#AABBDD">benötigt</font></small>');
define('ENTRY_STATE', 'Bundesland:');
define('ENTRY_STATE_ERROR', '&nbsp;<small><font color="#FF0000">benötigt</font></small>');
define('ENTRY_STATE_TEXT', '&nbsp;<small><font color="#AABBDD">benötigt</font></small>');
define('ENTRY_COUNTRY', 'Land:');
define('ENTRY_COUNTRY_ERROR', '');
define('ENTRY_COUNTRY_TEXT', '&nbsp;<small><font color="#AABBDD">benötigt</font></small>');
define('ENTRY_TELEPHONE_NUMBER', 'Telefon:');
define('ENTRY_TELEPHONE_NUMBER_ERROR', '&nbsp;<small><font color="#FF0000">min ' . ENTRY_TELEPHONE_MIN_LENGTH . ' Zeichen</font></small>');
define('ENTRY_TELEPHONE_NUMBER_TEXT', '&nbsp;<small><font color="#AABBDD">benötigt</font></small>');
define('ENTRY_FAX_NUMBER', 'Fax :');
define('ENTRY_FAX_NUMBER_ERROR', '');
define('ENTRY_FAX_NUMBER_TEXT', '');
define('ENTRY_NEWSLETTER', 'Newsletter:');
define('ENTRY_NEWSLETTER_TEXT', '');
define('ENTRY_NEWSLETTER_YES', 'abonniert');
define('ENTRY_NEWSLETTER_NO', 'nicht abonniert');
define('ENTRY_NEWSLETTER_ERROR', '');
define('ENTRY_PASSWORD', 'Passwort:');
define('ENTRY_PASSWORD_CONFIRMATION', 'Passwort Bestätigung:');
define('ENTRY_PASSWORD_CONFIRMATION_TEXT', '&nbsp;<small><font color="#AABBDD">benötigt</font></small>');
define('ENTRY_PASSWORD_ERROR', '&nbsp;<small><font color="#FF0000">min ' . ENTRY_PASSWORD_MIN_LENGTH . ' Zeichen</font></small>');
define('ENTRY_PASSWORD_TEXT', '&nbsp;<small><font color="#AABBDD">benötigt</font></small>');
define('PASSWORD_HIDDEN', '--HIDDEN--');

define('BOX_HEADING_MANUAL_ORDER', 'Bestellung erstellen');
define('BOX_MANUAL_ORDER_CREATE_ACCOUNT', 'Kunde anlegen');
define('BOX_MANUAL_ORDER_CREATE_ORDER', 'Bestellung erstellen');