<?php
/*
  $Id: orders.php,v 1.1 2005/09/03 04:47:37 loic Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

// ########## Ajout/editer commande et compte client ########## //-->
define('TABLE_HEADING_EDIT_ORDERS', 'Modifier la commande');
// ########## END - Ajout/editer commande et compte client ########## //-->

define('HEADING_TITLE', 'Commandes');
define('HEADING_TITLE_SEARCH', 'No de commande :');
define('HEADING_TITLE_STATUS', 'Statut :');

define('TABLE_HEADING_COMMENTS', 'Commentaires');
define('TABLE_HEADING_CUSTOMERS', 'Clients');
define('TABLE_HEADING_ORDER_TOTAL', 'Montant Total');
define('TABLE_HEADING_DATE_PURCHASED', 'Date d\'achat');
define('TABLE_HEADING_STATUS', 'Statut');
define('TABLE_HEADING_ACTION', 'Action');
define('TABLE_HEADING_QUANTITY', 'Qt&eacute;.');
define('TABLE_HEADING_PRODUCTS_MODEL', 'Modèle');
define('TABLE_HEADING_PRODUCTS', 'Produits');
define('TABLE_HEADING_TAX', 'Taxe');
define('TABLE_HEADING_TOTAL', 'Total');
define('TABLE_HEADING_STATUS', 'Statut');
define('TABLE_HEADING_PRICE_EXCLUDING_TAX', 'Prix (ht)');
define('TABLE_HEADING_PRICE_INCLUDING_TAX', 'Prix (ttc)');
define('TABLE_HEADING_TOTAL_EXCLUDING_TAX', 'Total (ht)');
define('TABLE_HEADING_TOTAL_INCLUDING_TAX', 'Total (ttc)');

define('TABLE_HEADING_NEW_VALUE', 'Nouvelle valeur');
define('TABLE_HEADING_OLD_VALUE', 'Ancienne valeur');
define('TABLE_HEADING_CUSTOMER_NOTIFIED', 'Informer le client;');
define('TABLE_HEADING_DATE_ADDED', 'Date d\'ajout');
define('ENTRY_CUSTOMER', 'Client :');
define('ENTRY_SOLD_TO', 'VENDU A :');
define('ENTRY_STREET_ADDRESS', 'Adresse :');
define('ENTRY_SUBURB', 'Complément Adresse :');
define('ENTRY_CITY', 'Ville :');
define('ENTRY_POST_CODE', 'Code Postal :');
define('ENTRY_STATE', 'D&eacutepartement :');
define('ENTRY_COUNTRY', 'Pays :');
define('ENTRY_TELEPHONE', 'Téléphone :');
define('ENTRY_courriel_ADDRESS', 'Adresse E-Mail :');
define('ENTRY_DELIVERY_TO', 'Livré à; :');
define('ENTRY_SHIP_TO', 'Envoyé à; :');
define('ENTRY_SHIPPING_ADDRESS', 'Adresse de livraison :');
define('ENTRY_BILLING_ADDRESS', 'Adresse de facturation :');
define('ENTRY_PAYMENT_METHOD', 'Mode de paiement :');
define('ENTRY_CREDIT_CARD_TYPE', 'Carte de Cr&eacute;dit :');
define('ENTRY_CREDIT_CARD_OWNER', 'Propriétaire de la carte de crédit :');
define('ENTRY_CREDIT_CARD_NUMBER', 'Numéro :');
define('ENTRY_CREDIT_CARD_EXPIRES', 'Date d\'expiration :');
define('ENTRY_SUB_TOTAL', 'Sous-Total :');
define('ENTRY_TAX', 'Taxe :');
define('ENTRY_SHIPPING', 'Livraison :');
define('ENTRY_TOTAL', 'Total :');
define('ENTRY_DATE_PURCHASED', 'Date d\'achat :');
define('ENTRY_STATUS', 'Statut :');
define('ENTRY_DATE_LAST_UPDATED', 'Dernière mise à jour :');
define('ENTRY_NOTIFY_CUSTOMER', 'Informer client :');
define('ENTRY_NOTIFY_COMMENTS', 'Ajouter un commentaire :');
define('ENTRY_PRINTABLE', 'Imprimer la commande');

define('TEXT_INFO_HEADING_DELETE_ORDER', 'Effacer commande');
define('TEXT_INFO_DELETE_INTRO', 'Voulez vous vraiment effacer cette commande ?');
define('TEXT_INFO_RESTOCK_PRODUCT_QUANTITY', 'Remettre les produits en stock');
define('TEXT_DATE_ORDER_CREATED', 'Date Création:');
define('TEXT_DATE_ORDER_LAST_MODIFIED', 'Dernière Modification :');
define('TEXT_INFO_PAYMENT_METHOD', 'Mode de paiement :');

define('TEXT_ALL_ORDERS', 'Toutes les commandes');
define('TEXT_NO_ORDER_HISTORY', 'Aucun historique de commande disponible');

define('EMAIL_SEPARATOR', '------------------------------------------------------');
define('EMAIL_TEXT_SUBJECT', 'Mise à jour de la commande');
define('EMAIL_TEXT_ORDER_NUMBER', 'Nous tenons à vous informer que le statut de votre commande vient d\'être modifiée.' . "\n\n" . 'Numéro de votre commande :');
define('EMAIL_TEXT_INVOICE_URL', "\n" . 'Vous pouvez suivre l\'évolution de votre commande via cette adresse :');
define('EMAIL_TEXT_DATE_ORDERED', "\n" . 'Date de votre commande :');

// ############# Send Order Html ############"
define('EMAIL_TEXT_STATUS_UPDATE',  'L\'état de votre commande a été mis à jour. ' . "<br><li>" . 'Nouvel état :<b> %s </b>' . "</li><br><br>" . 'Merci de répondre à ce courriel pour toute question.   ' . "<br><br>");
define('EMAIL_TEXT_COMMENTS_UPDATE', '<br><br>Le commentaire pour votre commande est : ' . "<br><br>%s<br>");
// ############# end Send Order Html ############"

define('ERROR_ORDER_DOES_NOT_EXIST', 'Erreur : La commande n\'existe pas.');
define('SUCCESS_ORDER_UPDATED', 'Succès : La commande est mise à jour avec succès.');
define('WARNING_ORDER_NOT_UPDATED', 'Attention : Aucune modification n\'a &eacute;t&eacute; effectu&eacute;e. La commande n\'a pas &eacute;t&eacute; mis &agrave; jour.');
?>