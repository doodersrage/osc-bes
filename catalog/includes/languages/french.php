<?php
/*
  $Id: french.php,v 1.1 2002/09/07 17:16:05 jpcivade Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

// look in your $PATH_LOCALE/locale directory for available locales
// or type locale -a on the server.
// Examples:
// on RedHat try 'en_US'
// on FreeBSD try 'en_US.ISO_8859-1'
// on Windows try 'en', or 'English'
// ############# Added French local ###############
// @setlocale(LC_TIME, 'fr_FR.ISO_8859-1');
 setlocale(LC_TIME, 'french');
// setlocale('LC_TIME', 'fr_FR.ISO_8859-1'); // serveur NUX
// setlocale('LC_TIME', 'fr'); // Serveur Win32
// setlocale("LC_TIME", "fr");
// ############# End Added French local ###############

define('DATE_FORMAT_SHORT', '%d/%m/%Y');  // this is used for strftime()
define('DATE_FORMAT_LONG', '%A %d %B, %Y'); // this is used for strftime()
define('DATE_FORMAT', 'd/m/Y');
define('DATE_TIME_FORMAT', DATE_FORMAT_SHORT . ' %H:%M:%S');

////
// Return date in raw format
// $date should be in format mm/dd/yyyy
// raw date is in format YYYYMMDD, or DDMMYYYY
function tep_date_raw($date, $reverse = false) {
  if ($reverse) {
    return substr($date, 0, 2) . substr($date, 3, 2) . substr($date, 6, 4);
  } else {
    return substr($date, 6, 4) . substr($date, 3, 2) . substr($date, 0, 2);
  }
}

// ############# Added #########
// if USE_DEFAULT_LANGUAGE_CURRENCY is true, use the following currency, instead of the applications default currency (used when changing language)
define('LANGUAGE_CURRENCY', 'EUR');
// ######## End Added ############

// Global entries for the <html> tag
define('HTML_PARAMS','dir="LTR" lang="fr"');

// charset for web pages and courriels
define('CHARSET', 'iso-8859-1');

// ################# added  ##############
// page title
define('TITLE', 'Bienvenue dans la boutique '.STORE_NAME.' ');

// CCGV
define('BOX_INFORMATION_GV', 'FAQ des ch&egrave;ques cadeaux');
define('VOUCHER_BALANCE', 'Soldes en ch&egrave;que');
define('BOX_HEADING_GIFT_VOUCHER', 'Compte des ch&egrave;ques cadeaux'); 
define('GV_FAQ', 'FAQ des ch&egrave;ques cadeaux');
define('ERROR_REDEEMED_AMOUNT', 'Votre chèque est validé dans votre panier : ');
define('ERROR_NO_REDEEM_CODE', 'Vous n\'avez pas entr&eacute; de code d\'&eacute;change.');  
define('ERROR_NO_INVALID_REDEEM_GV', 'Code \'ch&egrave;que cadeau\' non valide'); 
define('TABLE_HEADING_CREDIT', 'Cr&eacute;dits en cours');
define('GV_HAS_VOUCHERA', 'Vous avez des provisions sur votre compte \'ch&egrave;ques cadeaux\'. Si vous le d&eacute;sirez <br>
                         vous pouvez envoyer ces fonds par <a class="pageResults" href="');
       
define('GV_HAS_VOUCHERB', '"><b>email</b></a> &agrave; un(e) ami(e)'); 
define('ENTRY_AMOUNT_CHECK_ERROR', 'Vous n\'avez pas assez d\'argent pour envoyer un ch&egrave;que de ce montant.'); 
define('BOX_SEND_TO_FRIEND', 'Envoyer un ch&egrave;que cadeau &agrave; un(e) ami(e)');

define('VOUCHER_REDEEMED', 'Ch&egrave;que &agrave; valider');
define('CART_COUPON', 'Coupon :');
define('CART_COUPON_INFO', 'Plus de renseignements');
// ############## End Added ##################

// header text in includes/header.php
define('HEADER_TITLE_CREATE_ACCOUNT', 'Créer un compte');
define('HEADER_TITLE_MY_ACCOUNT', 'Mon compte');
define('HEADER_TITLE_CART_CONTENTS', 'Voir panier');
define('HEADER_TITLE_CHECKOUT', 'Commander');
define('HEADER_TITLE_CONTACT_US', 'Contactez Nous');
define('HEADER_TITLE_TOP', 'Accueil');
define('HEADER_TITLE_CATALOG', ''. STORE_NAME .'');
define('HEADER_TITLE_LOGOFF', 'Deconnexion');
define('HEADER_TITLE_LOGIN', 'S\'identifier');


// footer text in includes/footer.php
define('FOOTER_TEXT_REQUESTS_SINCE', 'requêtes depuis le');
// define('FOOTER_TEXT_BODY', 'Copyright &copy; - All rights reserved ');

// text for gender
define('MALE', 'Mr.');
define('FEMALE', 'Mme.');
define('MALE_ADDRESS', 'Mr.');
define('FEMALE_ADDRESS', 'Mme.');

// text for date of birth example
define('DOB_FORMAT_STRING', 'jj/mm/aaaa');


// categories box text in includes/boxes/categories.php
define('BOX_HEADING_CATEGORIES', 'Catégories');

// manufacturers box text in includes/boxes/manufacturers.php
define('BOX_HEADING_MANUFACTURERS', 'Fabricants');
define('BOX_MANUFACTURERS_SELECT_ONE', 'Selectionner:');

// whats_new box text in includes/boxes/whats_new.php
define('BOX_HEADING_WHATS_NEW', 'Nouveautés');


// quick_find box text in includes/boxes/quick_find.php3
define('BOX_HEADING_SEARCH', 'Rechercher');
define('BOX_SEARCH_TEXT', 'Recherche rapide');
define('BOX_SEARCH_ADVANCED_SEARCH', 'Recherche avançée');

// specials box text in includes/boxes/specials.php
define('BOX_HEADING_SPECIALS', 'Promotions');

// reviews box text in includes/boxes/reviews.php
define('BOX_HEADING_REVIEWS', 'Commentaires');
define('BOX_REVIEWS_WRITE_REVIEW', 'Ecrire un commentaire pour ce produit!');
define('BOX_REVIEWS_NO_REVIEWS', 'Il n\'y a pas encore de commentaire');
define('BOX_REVIEWS_TEXT_OF_5_STARS', '%s sur 5 Etoiles!');

// shopping_cart box text in includes/boxes/shopping_cart.php
define('BOX_HEADING_SHOPPING_CART', 'Mon panier');
define('BOX_SHOPPING_CART_EMPTY', 'Aucun articles');

// order_history box text in includes/boxes/order_history.php
define('BOX_HEADING_CUSTOMER_ORDERS', 'Historique commandes');

// best_sellers box text in includes/boxes/best_sellers.php
define('BOX_HEADING_BESTSELLERS', 'Meilleures ventes');
define('BOX_HEADING_BESTSELLERS_IN', 'Meilleures ventes dans <br>&nbsp;&nbsp;');

// notifications box text in includes/boxes/products_notifications.php
define('BOX_HEADING_NOTIFICATIONS', 'Surveillance Produit');
define('BOX_NOTIFICATIONS_NOTIFY', 'M\'informer d\'un changement <b>%s</b>');
define('BOX_NOTIFICATIONS_NOTIFY_REMOVE', 'Ne pas m\'informer d\'un changement <b>%s</b>');

// manufacturer box text
define('BOX_HEADING_MANUFACTURER_INFO', 'Information fabricant');
define('BOX_MANUFACTURER_INFO_HOMEPAGE', 'Page d\'accueil de %s');
define('BOX_MANUFACTURER_INFO_OTHER_PRODUCTS', 'Autres articles');

// languages box text in includes/boxes/languages.php
define('BOX_HEADING_LANGUAGES', 'Langues');

// currencies box text in includes/boxes/currencies.php
define('BOX_HEADING_CURRENCIES', 'Devises');

// information box text in includes/boxes/information.php
define('BOX_HEADING_INFORMATION', 'Informations');
define('BOX_INFORMATION_PRIVACY', 'Politique de Confidentialité');
define('BOX_INFORMATION_CONDITIONS', 'Conditions Générales');
define('BOX_INFORMATION_SHIPPING', 'Expéditions et retours');
define('BOX_INFORMATION_CONTACT', 'Contactez-nous');

// tell a friend box text in includes/boxes/tell_a_friend.php
define('BOX_HEADING_TELL_A_FRIEND', 'Faire connaître');
define('BOX_TELL_A_FRIEND_TEXT', 'Envoyer cet article &agrave; un ami(e).');


// checkout procedure text
define('CHECKOUT_BAR_CART_CONTENTS', 'Contenu du Panier');
define('CHECKOUT_BAR_DELIVERY_ADDRESS', 'Adresse de Livraison');
define('CHECKOUT_BAR_PAYMENT_METHOD', 'Méthode de Paiement');
define('CHECKOUT_BAR_CONFIRMATION', '<b>Confirmation</b>');
define('CHECKOUT_BAR_FINISHED', 'Fin!');
define('CHECKOUT_BAR_DELIVERY', 'Information Destinataire');
define('CHECKOUT_BAR_PAYMENT', 'Information Paiement');

// pull down default text
define('PULL_DOWN_DEFAULT', '-- Votre choix? --');
define('PLEASE_SELECT', 'Merci de sélectionner');
define('TYPE_BELOW', 'Ecrire ci-dessous');

// javascript messages
define('JS_ERROR', 'Des erreurs sont survenues durant le traitement de votre formulaire.\n\nVeuillez effectuer les corrections suivantes :\n\n');

define('JS_REVIEW_TEXT', '* Le \'commentaire\' que vous avez rentré doit avoir au moins ' . REVIEW_TEXT_MIN_LENGTH . ' caractères.\n');
define('JS_REVIEW_RATING', '* Vous devez mettre une appréciation pour cet article.\n');

define('JS_ERROR_NO_PAYMENT_MODULE_SELECTED', '* Veuillez choisir une méthode de paiement pour votre commande.\n');

define('JS_ERROR_SUBMITTED', 'Ce formulaire a été déjà soumis. Veuillez appuyer sur Ok et attendez jusqu\'à ce que le traitement soit fini.');

define('ERROR_NO_PAYMENT_MODULE_SELECTED', 'Veuillez choisir une m&eacute;thode de paiement pour votre commande.');

define('CATEGORY_PERSONAL', 'Vous');
define('CATEGORY_COMPANY', 'Société');
define('CATEGORY_ADDRESS', 'Votre adresse');
define('CATEGORY_CONTACT', 'Vos informations personnelles');
define('CATEGORY_OPTIONS', 'Options');
define('CATEGORY_PASSWORD', 'Votre mot de passe');

define('ENTRY_COMPANY', 'Société:');
define('ENTRY_COMPANY_ERROR', ' <small><font color="#FF0000">min ' . ENTRY_COMPANY_LENGTH . ' chars</font></small>');
define('ENTRY_COMPANY_TEXT', ' <small><font color="#0000bb">requis</font></small>');
define('ENTRY_GENDER', 'Civilités:');
define('ENTRY_GENDER_ERROR', 'Sélectionner un des champs Civilités');
define('ENTRY_GENDER_TEXT', '*');
define('ENTRY_FIRST_NAME', 'Prénom:');
define('ENTRY_FIRST_NAME_ERROR', 'Votre prénom doit contenir un minimum de ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' caractères.');
define('ENTRY_FIRST_NAME_TEXT', '*');
define('ENTRY_LAST_NAME', 'Nom :');
define('ENTRY_LAST_NAME_ERROR', 'Votre nom doit contenir un minimum de ' . ENTRY_LAST_NAME_MIN_LENGTH . ' caractères.');
define('ENTRY_LAST_NAME_TEXT', '*');
define('ENTRY_DATE_OF_BIRTH', 'Date de naissance :');
define('ENTRY_DATE_OF_BIRTH_ERROR', 'Votre date de naissance doit avoir ce format : JJ/MM/AAAA (ex. 21/05/1970)');
define('ENTRY_DATE_OF_BIRTH_TEXT', '* (ex. 21/05/1970)');
define('ENTRY_EMAIL_ADDRESS', 'Adresse du Courriel:');
define('ENTRY_EMAIL_ADDRESS_ERROR', 'Votre courriel doit contenir un minimum de ' . ENTRY_EMAIL_ADDRESS_MIN_LENGTH . ' caractères.');
define('ENTRY_EMAIL_ADDRESS_CHECK_ERROR', 'Votre courriel ne semble pas &ecirc;tre valide - veuillez effectuer toutes les corrections n&eacute;cessaires.');
define('ENTRY_EMAIL_ADDRESS_ERROR_EXISTS', 'Votre courriel est d&eacute;j&agrave; enregistr&eacute;e sur notre site - Veuillez ouvrir une session avec ce courriel ou cr&eacute;ez un compte avec une adresse diff&eacute;rente.');
define('ENTRY_EMAIL_ADDRESS_TEXT', '*');
define('ENTRY_STREET_ADDRESS', 'Adresse :');
define('ENTRY_STREET_ADDRESS_ERROR', 'Votre adresse doit contenir un minimum de ' . ENTRY_STREET_ADDRESS_MIN_LENGTH . ' caractères.');
define('ENTRY_STREET_ADDRESS_TEXT', '*');
define('ENTRY_SUBURB', 'Adresse 2:');
define('ENTRY_SUBURB_ERROR', '');
define('ENTRY_SUBURB_TEXT', '');
define('ENTRY_POST_CODE', 'Code postal :');
define('ENTRY_POST_CODE_ERROR', 'Votre code postal doit contenir un minimum de ' . ENTRY_POSTCODE_MIN_LENGTH . ' caractères.');
define('ENTRY_POST_CODE_TEXT', '*');
define('ENTRY_CITY', 'Ville: ');
define('ENTRY_CITY_ERROR', 'Votre ville doit contenir un minimum de ' . ENTRY_CITY_MIN_LENGTH . ' caractères.');
define('ENTRY_CITY_TEXT', '*');
define('ENTRY_STATE', 'Etat/Département :');
define('ENTRY_STATE_ERROR', 'Votre état doit contenir un minimum de ' . ENTRY_STATE_MIN_LENGTH . ' caractères.');
define('ENTRY_STATE_ERROR_SELECT', 'Sélectionner un autre Etat ou Département.');
define('ENTRY_STATE_TEXT', ' *');
define('ENTRY_COUNTRY', 'Pays:');
define('ENTRY_COUNTRY_ERROR', 'Veuillez choisir un pays à partir de la liste déroulante.');
define('ENTRY_COUNTRY_TEXT', '*');
define('ENTRY_TELEPHONE_NUMBER', 'Numéro de Téléphone:');
define('ENTRY_TELEPHONE_NUMBER_ERROR', 'Votre numéro de téléphone doit contenir un minimum de ' . ENTRY_TELEPHONE_MIN_LENGTH . ' caractères.');
define('ENTRY_TELEPHONE_NUMBER_TEXT', '*');
define('ENTRY_FAX_NUMBER', 'Numéro de Fax:');
define('ENTRY_FAX_NUMBER_ERROR', '');
define('ENTRY_FAX_NUMBER_TEXT', '');
define('ENTRY_NEWSLETTER', 'Lettre d\'information:');
define('ENTRY_NEWSLETTER_TEXT', '');
define('ENTRY_NEWSLETTER_YES', 'S\'abonner');
define('ENTRY_NEWSLETTER_NO', 'Ne pas s\'abonner');
define('ENTRY_NEWSLETTER_ERROR', '');
define('ENTRY_PASSWORD', 'Mot de passe:');
define('ENTRY_PASSWORD_ERROR', 'Votre mot de passe doit contenir un minimum de ' . ENTRY_PASSWORD_MIN_LENGTH . ' caractères.');
define('ENTRY_PASSWORD_ERROR_NOT_MATCHING', 'Le mot de passe de confirmation doit être identique à votre mot de passe.');
define('ENTRY_PASSWORD_TEXT', '*');
define('ENTRY_PASSWORD_CONFIRMATION', 'Confirmation:');
define('ENTRY_PASSWORD_CONFIRMATION_TEXT', '*');
define('ENTRY_PASSWORD_CURRENT', 'Mot de passe actuel :');
define('ENTRY_PASSWORD_CURRENT_TEXT', '*');
define('ENTRY_PASSWORD_CURRENT_ERROR', 'Votre mot de passe doit contenir un minimum de ' . ENTRY_PASSWORD_MIN_LENGTH . ' caractères.');
define('ENTRY_PASSWORD_NEW', 'Nouveau mot de passe :');
define('ENTRY_PASSWORD_NEW_TEXT', '*');
define('ENTRY_PASSWORD_NEW_ERROR', 'Votre nouveau mot de passe doit contenir un minimum de ' . ENTRY_PASSWORD_MIN_LENGTH . ' caractères.');
define('ENTRY_PASSWORD_NEW_ERROR_NOT_MATCHING', 'Le mot de passe de confirmation doit être identique à votre nouveau mot de passe.');
define('ENTRY_PASSWORD_ERROR_NOT_MATCHING', 'The Password Confirmation must match your Password.');
define('ENTRY_PASSWORD_TEXT', '*');
define('ENTRY_PASSWORD_CURRENT','Ancien mot de passe');

define('PASSWORD_HIDDEN', '--CACHE--');

define('FORM_REQUIRED_INFORMATION', '* Information requise');

// constants for use in tep_prev_next_display function
define('TEXT_RESULT_PAGE', 'Résultat:');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS', 'Affichage de <b>%d</b> à <b>%d</b> (sur <b>%d</b> produits)');
define('TEXT_DISPLAY_NUMBER_OF_ORDERS', 'Affichage de <b>%d</b> à <b>%d</b> (sur <b>%d</b> commandes)');
define('TEXT_DISPLAY_NUMBER_OF_REVIEWS', 'Affichage de <b>%d</b> à <b>%d</b> (sur <b>%d</b> Impressions)');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS_NEW', 'Affichage de <b>%d</b> à <b>%d</b> (sur <b>%d</b> nouveaux produits)');
define('TEXT_DISPLAY_NUMBER_OF_SPECIALS', 'Affichage de <b>%d</b> à <b>%d</b> (sur <b>%d</b> promotions)');

define('PREVNEXT_TITLE_FIRST_PAGE', 'Première Page');
define('PREVNEXT_TITLE_PREVIOUS_PAGE', 'Page Précédente');
define('PREVNEXT_TITLE_NEXT_PAGE', 'Page Suivante');
define('PREVNEXT_TITLE_LAST_PAGE', 'Dernière Page');
define('PREVNEXT_TITLE_PAGE_NO', 'Page %d');
define('PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE', 'Ensemble précédent de %d Pages');
define('PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE', 'Ensemble suivant de %d Pages');
define('PREVNEXT_BUTTON_FIRST', '&lt;&lt;Premier');
define('PREVNEXT_BUTTON_PREV', '[&lt;&lt;&nbsp;Préc]');
define('PREVNEXT_BUTTON_NEXT', '[Suiv&nbsp;&gt;&gt;]');
define('PREVNEXT_BUTTON_LAST', 'Dernier&gt;&gt;');

define('IMAGE_BUTTON_ADD_ADDRESS', 'Ajouter adresse');
define('IMAGE_BUTTON_ADDRESS_BOOK', 'Carnet d\'adresses');
define('IMAGE_BUTTON_BACK', 'Retour ');
define('IMAGE_BUTTON_BUY_NOW', 'Acheter : ');
define('IMAGE_BUTTON_CHANGE_ADDRESS', 'Changez l\'adresse');
define('IMAGE_BUTTON_CHECKOUT', 'Commander');
define('IMAGE_BUTTON_CONFIRM_ORDER', 'Confirmer la commande');
define('IMAGE_BUTTON_CONTINUE', 'Continuer');
define('IMAGE_BUTTON_CONTINUE_SHOPPING', 'Continuer vos achats');
define('IMAGE_BUTTON_DELETE', 'Supprimer');
define('IMAGE_BUTTON_EDIT_ACCOUNT', 'Editer Compte');
define('IMAGE_BUTTON_HISTORY', 'Historique des commandes');
define('IMAGE_BUTTON_LOGIN', 'Identifiant');
define('IMAGE_BUTTON_IN_CART', 'Ajouter au panier : ');
define('IMAGE_BUTTON_NOTIFICATIONS', 'Surveillance Produit');
define('IMAGE_BUTTON_QUICK_FIND', 'Recherche rapide');
define('IMAGE_BUTTON_REMOVE_NOTIFICATIONS', 'Supprimer les surveillances produits');
define('IMAGE_BUTTON_REVIEWS', 'Impressions sur : ');
define('IMAGE_BUTTON_SEARCH', 'Rechercher');
define('IMAGE_BUTTON_SHIPPING_OPTIONS', 'Options d\'expédition');
define('IMAGE_BUTTON_TELL_A_FRIEND', 'Envoyer à un ami');
define('IMAGE_BUTTON_UPDATE', 'Mise à jour');
define('IMAGE_BUTTON_UPDATE_CART', 'Mise à jour du panier');
define('IMAGE_BUTTON_WRITE_REVIEW', 'Ecrire un commentaire : ');

define('SMALL_IMAGE_BUTTON_DELETE', 'Supprimer');
define('SMALL_IMAGE_BUTTON_EDIT', 'Editer');
define('SMALL_IMAGE_BUTTON_VIEW', 'Afficher');

define('ICON_ARROW_RIGHT', 'Plus');
define('ICON_CART', 'Panier');
define('ICON_ERROR', 'Erreur');
define('ICON_SUCCESS', 'Succès');
define('ICON_WARNING', 'Attention');

define('TEXT_GREETING_PERSONAL', 'Bienvenue <span class="greetUser">%s!</span> Voudriez vous voir <a href="%s"><u>les nouveautés</u></a> disponibles?');
define('TEXT_GREETING_PERSONAL_RELOGON', '<small>Si vous n\'êtes pas %s, merci de vous <a href="%s"><u>reconnecter</u></a> avec votre identifiant et mot de passe.</small>');
define('TEXT_GREETING_GUEST', 'Bienvenue <span class="greetUser">Madame, Monsieur</span>. Voulez vous vous <a href="%s"><u>identifier</u></a> ? Préférez vous <a href="%s"><u>créer un compte</u></a> ?');

define('TEXT_SORT_PRODUCTS', 'Trier les produits ');
define('TEXT_DESCENDINGLY', 'descendant');
define('TEXT_ASCENDINGLY', 'ascendant');
define('TEXT_BY', ' par ');

define('TEXT_REVIEW_BY', 'par %s');
define('TEXT_REVIEW_SUITE', '<i>(Lire la suite)</i>');
define('TEXT_REVIEW_WORD_COUNT', '%s mots');
define('TEXT_REVIEW_RATING', 'Classement: %s [%s]');
define('TEXT_REVIEW_DATE_ADDED', 'Date d\'ajout: %s');
define('TEXT_NO_REVIEWS', 'Il n\'y a pas encore de commentaire sur ce produit.');

define('TEXT_NO_NEW_PRODUCTS', 'Il n\'y a pour le moment aucun nouveau produit.');

define('TEXT_UNKNOWN_TAX_RATE', 'Taxe inconnue');

define('TEXT_REQUIRED', '<span class="errorText">Requis</span>');
define('ERROR_TEP_MAIL', '<font face="Verdana, Arial" size="2" color="#ff0000"><b><small>TEP ERREUR:</small> Impossible d\'envoyer le courriel au travers du serveur SMTP spécifié. Vérifiez le fichier PHP.INI et corrigez le nom du serveur SMTP si nécessaire.</b></font>');
define('WARNING_INSTALL_DIRECTORY_EXISTS', 'Attention, le répertoire d\'installation existe à :' . dirname($HTTP_SERVER_VARS['SCRIPT_FILENAME']) . '/install. Merci de supprimer ce répertoire pour des raisons de sécurité.');
define('WARNING_CONFIG_FILE_WRITEABLE', 'Warning: I am able to write to the configuration file: ' . dirname($HTTP_SERVER_VARS['SCRIPT_FILENAME']) . '/includes/configure.php. Ceci est un risque potentiel - merci de mettre les bonnes permissions sur ce fichier.');
define('WARNING_SESSION_DIRECTORY_NON_EXISTENT', 'Attention: Le répertoire de session n\'existe pas: ' . tep_session_save_path() . '. Les sessions ne fonctionneront pas tant que ce répertoire n\'aura pas été créé.');
define('WARNING_SESSION_DIRECTORY_NOT_WRITEABLE', 'Attention: Il est impossible d\'écrire dans le répertoire de sessions: ' . tep_session_save_path() . '. Celles-ci ne fonctionneront pas tant que les permissions n\'auront pas été corrigées.');
define('WARNING_SESSION_DIRECTORY_NON_EXISTENT', 'Attention : Le r&eacute;pertoire de session n\'existe pas : ' . tep_session_save_path() . '. Les sessions ne fonctionneront pas tant que ce r&eacute;pertoire n\'aura pas &eacute;t&eacute; cr&eacute;&eacute;.');
define('WARNING_SESSION_DIRECTORY_NOT_WRITEABLE', 'Attention : Il est impossible d\'&eacute;crire dans le r&eacute;pertoire de sessions ' . tep_session_save_path() . '. Les sessions ne fonctionneront pas tant que les permissions n\'auront pas &eacute;t&eacute; corrig&eacute;es.');
define('WARNING_SESSION_AUTO_START', 'Attention : session.auto_start est actif - d&eacute;sactiver cette fonctionnalit&eacute; dans php.ini et red&eacute;marrer le serveur.');
define('WARNING_DOWNLOAD_DIRECTORY_NON_EXISTENT', 'Attention : Le r&eacute;pertoire de t&eacute;l&eacute;chargement n\'existe pas : ' . DIR_FS_DOWNLOAD . '. Le t&eacute;l&eacute;chargement de produits ne fonctionnera qu\'avec un r&eacute;pertoire valide.');

define('TEXT_CCVAL_ERROR_INVALID_DATE', 'La date d\'expiration entrée pour cette carte de crédit n\'est pas valide. Veuillez vérifier la date et réessayez.');
define('TEXT_CCVAL_ERROR_INVALID_NUMBER', 'Le numéro entrée pour cette carte de crédit n\'est pas valide. Veuillez vérifier le numéro et réessayez.');
define('TEXT_CCVAL_ERROR_UNKNOWN_CARD', 'Le code à 4 chiffres que vous avez entré est : %s. Si ce code est correct, nous n\'acceptons pas ce type de carte crédit. S\'il est erroné veuillez réessayer.');

/*
  The following copyright announcement can only be
  appropriately modified or removed if the layout of
  the site theme has been modified to distinguish
  itself from the default osCommerce-copyrighted
  theme.

  For more information please read the following
  Frequently Asked Questions entry on the osCommerce
  support site:

  http://www.oscommerce.com/community.php/faq,26/q,50

  Please leave this comment intact together with the
  following copyright announcement.
*/
// define('FOOTER_TEXT_BODY', 'Copyright &copy; 2003 <a href="http://www.oscommerce.com" target="_blank">osCommerce</a><br>Powered by <a href="http://www.oscommerce.com" target="_blank">osCommerce</a><br><font color="gray">Traduction par Delaballe</font>');
?>