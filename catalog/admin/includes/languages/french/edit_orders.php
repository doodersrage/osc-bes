<?php
/*
  $Id: edit_orders.php,v 2.5 2006/06/15 14:13:44 ams Exp $
  french
  
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
  Translated by lordbdp

  Copyright (c) 2006 osCommerce
  
  Released under the GNU General Public License
*/

define('HEADING_TITLE', 'Modification de commande');
define('HEADING_TITLE_NUMBER', 'N°');
define('HEADING_TITLE_DATE', 'en date du');
define('HEADING_SUBTITLE', 'Modifiez tous les champs souhait&eacute;s puis cliquez sur le bouton "Mise &agrave; jour" ci-dessous.');
define('HEADING_TITLE_STATUS', 'Statut');
define('ADDING_TITLE', 'Ajouter un produit');

define('HINT_UPDATE_TO_CC', '_____________Placez le mode de paiement sur ');
//ENTRY_CREDIT_CARD should be whatever is saved in your db as the payment method
//when your customer pays by Credit Card
define('ENTRY_CREDIT_CARD', 'Carte de crédit');
define('HINT_UPDATE_TO_CC2', ' et les autres champs seront affichés automatiquement. Des champs de CB sont cachés si n importe quelle autre méthode de paiement est choisie.');
define('HINT_PRODUCTS_PRICES', 'Le calcul du prix et du poids est donné à la volée, mais vous devez faire la mise à jour afin de sauver les modifications. La qté peut validée à 0 ou en valeur négative si besoin. Si vous voulez supprimer un produit, cochez la case Effacer puis faites une mise à jour. Les champs de poids ne sont pas modifiables.');
define('HINT_SHIPPING_ADDRESS', 'Si l adresse d expédition est changée, ceci peut modifier la zone de taxes du bon de commande. Vous devrez appuyer sur le bouton de mise à jour pour recalculer correctement des totaux des taxes dans ce cas-ci.');
define('HINT_TOTALS', 'Vous êtes libre de donner des escomptes en ajoutant des valeurs négatives. Tout champs avec une valeur à 0 sera supprimer lors de la mise à jour (sauf les Frais d envoi). Seuls les Frais d envoi, ainsi que les champs libre sous Sous-Total (un pour le texte + un autre pour la valeur) peuvent être modifiés.');
define('HINT_PRESS_UPDATE', 'Cliquez sur le bouton "Mise &agrave; jour" pour enregistrer toutes vos modifications.');
define('HINT_BASE_PRICE', 'Le prix (de base) est le prix du produit avant ajout des attributs de produits (ex: le prix catalogue de l article).');
define('HINT_PRICE_EXCL', 'Le prix HT est le prix de base avec les attributs attachés');
define('HINT_PRICE_INCL', 'Le prix TTC est le prix HT avec la TVA');
define('HINT_TOTAL_EXCL', 'Le total HT est le prix HT selon la qté choisie');
define('HINT_TOTAL_INCL', 'Le prix total TTC est le prix selon la qté choisie et la TVA');

define('TABLE_HEADING_COMMENTS', 'Commentaires');
define('TABLE_HEADING_STATUS', 'Statut de la commande');
define('TABLE_HEADING_QUANTITY', 'Qté');
define('TABLE_HEADING_PRODUCTS_MODEL', 'Modèle');
define('TABLE_HEADING_PRODUCTS_WEIGHT', 'Poids');
define('TABLE_HEADING_PRODUCTS', 'Produits');
define('TABLE_HEADING_TAX', 'Taxes');
define('TABLE_HEADING_BASE_PRICE', 'Prix (de base)');
define('TABLE_HEADING_UNIT_PRICE', 'Prix HT');
define('TABLE_HEADING_UNIT_PRICE_TAXED', 'Prix TTC');
define('TABLE_HEADING_TOTAL_PRICE', 'Total HT');
define('TABLE_HEADING_TOTAL_PRICE_TAXED', 'Total TTC');
define('TABLE_HEADING_TOTAL_MODULE', 'R&eacute;capitulatif de la commande');
define('TABLE_HEADING_TOTAL_AMOUNT', 'Montant');
define('TABLE_HEADING_TOTAL_WEIGHT', 'Total Poids: ');
define('TABLE_HEADING_DELETE', 'Effacer');
define('TABLE_HEADING_SHIPPING_TAX', 'Frais d\'envoi: ');

define('TABLE_HEADING_CUSTOMER_NOTIFIED', 'Client avisé');
define('TABLE_HEADING_DATE_ADDED', 'Date de modification');

define('ENTRY_CUSTOMER_NAME', 'Nom');
define('ENTRY_CUSTOMER_COMPANY', 'Société');
define('ENTRY_CUSTOMER_ADDRESS', 'Adresse');
define('ENTRY_CUSTOMER_SUBURB', 'Banlieue');
define('ENTRY_CUSTOMER_PHONE', 'T&eacute;l&eacute;phone');
define('ENTRY_CUSTOMER_CITY', 'Ville');
define('ENTRY_CUSTOMER_STATE', 'D&eacute;partement');
define('ENTRY_CUSTOMER_POSTCODE', 'Code postal');
define('ENTRY_CUSTOMER_COUNTRY', 'Pays');
define('ENTRY_CUSTOMER_EMAIL', 'E-Mail');
define('ENTRY_ADDRESS', 'Adresse');

define('ENTRY_SHIPPING_ADDRESS', 'Adresse de livraison:');
define('ENTRY_BILLING_ADDRESS', 'Adresse de facturation:');
define('ENTRY_PAYMENT_METHOD', 'Moyen de paiement:');
define('ENTRY_CREDIT_CARD_TYPE', 'Type de carte de crédit:');
define('ENTRY_CREDIT_CARD_OWNER', 'Proporiétaire de la carte:');
define('ENTRY_CREDIT_CARD_NUMBER', 'Numéro de la carte:');
define('ENTRY_CREDIT_CARD_EXPIRES', 'Expiration de la carte:');
define('ENTRY_SUB_TOTAL', 'Sous-Total:');

//do not put a colon (" : ") in the definition of ENTRY_TAX
//ie entry should be 'Tax' NOT 'Tax:'
define('ENTRY_TAX', 'Taxes:');

define('ENTRY_TOTAL', 'Total:');
define('ENTRY_STATUS', 'Statut:');
define('ENTRY_NOTIFY_CUSTOMER', 'Notifier le client:');
define('ENTRY_NOTIFY_COMMENTS', 'Commentaires:');

define('TEXT_NO_ORDER_HISTORY', 'Pas d\'historique disponible');

define('EMAIL_SEPARATOR', '------------------------------------------------------');
define('EMAIL_TEXT_SUBJECT', 'Mise à jour de votre commande');
define('EMAIL_TEXT_ORDER_NUMBER', 'Commande n°:');
define('EMAIL_TEXT_INVOICE_URL', 'Facture détaillée:');
define('EMAIL_TEXT_DATE_ORDERED', 'Date de commande:');
define('EMAIL_TEXT_STATUS_UPDATE', 'Votre commande a été mise à jour au statut suivant.' . "\n\n" . 'Nouveau statut: %s' . "\n\n" . 'Répondez à cette email, dans le cas où vous désirez obtenir plus d\'informations.' . "\n");
define('EMAIL_TEXT_STATUS_UPDATE2', 'Si vous avez des questions, n\'h&eacute;sitez pas &agrave; nous contacter.' . "\n\n" . 'Cordialement. Le Service Commercial ' . STORE_NAME . "\n");
define('EMAIL_TEXT_COMMENTS_UPDATE', 'Les commentaires pour votre commande sont' . "\n\n%s\n\n");

define('ERROR_ORDER_DOES_NOT_EXIST', 'Erreur: aucune commande existante');
define('SUCCESS_ORDER_UPDATED', 'Succès: la commande a bien été mise à jour.');

define('ADDPRODUCT_TEXT_CATEGORY_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_SELECT_PRODUCT', 'Choisissez un article');
define('ADDPRODUCT_TEXT_PRODUCT_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_SELECT_OPTIONS', 'Choisissez une option');
define('ADDPRODUCT_TEXT_OPTIONS_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_OPTIONS_NOTEXIST', 'Pas d\'options: sauté..');
define('ADDPRODUCT_TEXT_CONFIRM_QUANTITY', 'Qté.');
define('ADDPRODUCT_TEXT_CONFIRM_ADDNOW', 'Ajouter maintenant');
define('ADDPRODUCT_TEXT_STEP', 'Etape n°');
define('ADDPRODUCT_TEXT_STEP1', ' &laquo; Choisir une cat&eacute;gorie. ');
define('ADDPRODUCT_TEXT_STEP2', ' &laquo; Choisir un article. ');
define('ADDPRODUCT_TEXT_STEP3', ' &laquo; Choisir une option. ');

define('MENUE_TITLE_CUSTOMER', '1. Informations Clients');
define('MENUE_TITLE_PAYMENT', '2. Moyen de paiement');
define('MENUE_TITLE_ORDER', '3. Produits Command&eacute;s');
define('MENUE_TITLE_TOTAL', '4. Remise, Frais de Port et Total');
define('MENUE_TITLE_STATUS', '5. Status et Notification');
define('MENUE_TITLE_UPDATE', '6. Mise &agrave; jour');

?>