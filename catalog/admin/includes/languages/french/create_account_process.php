<?php
/*
  $Id: create_account_process.php,v 1 12:01 AM 17/08/2003 frankl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('EMAIL_PASS_1', 'Veuillez prendre note de votre mot de passe ');
define('EMAIL_PASS_2', ' modifiable en ligne en acc�dant � votre nouveau compte.' . "\n\n");

define('NAVBAR_TITLE', 'Cr�ation de compte');
define('HEADING_TITLE', 'Information sur le comte');
define('HEADING_NEW', 'Proc�dure de commande');
define('NAVBAR_NEW_TITLE', 'Proc�dure de commande');

define('EMAIL_SUBJECT', 'Bienvenue chez' . STORE_NAME);
define('EMAIL_GREET_MR', 'Cher Mr. ' . stripslashes($HTTP_POST_VARS['lastname']) . ',' . "\n\n");
define('EMAIL_GREET_MS', 'Ch�re Mme. ' . stripslashes($HTTP_POST_VARS['lastname']) . ',' . "\n\n");
define('EMAIL_GREET_NONE', 'Cher ' . stripslashes($HTTP_POST_VARS['firstname']) . ',' . "\n\n");
define('EMAIL_WELCOME', 'Nous vous souhaitons la bienvenue sur <b>' . STORE_NAME . '</b>.' . "\n\n");
define('EMAIL_TEXT', 'Vous pouvez d�sormais acc�der � nos services suivants :' . "\n\n" . '<li><b>Panier permanent</b>' . "\n" . '----------------' . "\n\n" . '     Tous les articles resteront dans votre panier jusqu\'� ce que vous soldiez votre commande. Vous pouvez � tout moment supprimer les articles de votre choix.' . "\n\n" . '<li><b>Carnet d\'adresses</b>' . "\n" . '-----------------' . "\n\n" . '     Vous pouvez cr�er votre carnet d\'adresses, et nous demander la livraison � toute adresse de votre choix.' . "\n\n" . '<li><b>Historique des commandes</b>' . "\n" . '------------------------' . "\n\n" . '     Vous avez acc�s � l\'historique de vos commandes sur votre compte.' . "\n\n" . '<li><b>Vos suggestions</b>' . "\n" . '---------------' . "\n\n" . '     N\'h�sitez pas � exprimer vos opinions sur le site '.STORE_NAME.'.' . "\n\n\n");
define('EMAIL_CONTACT', 'Pour toute aide sur les services, merci de contacter : ' . STORE_OWNER_EMAIL_ADDRESS . '.' . "\n\n");
define('EMAIL_WARNING', 'Nous vous remercions pour la confiance que vous nous t�moigniez en vous enregistrant comme nouveau client sur le site ' . STORE_NAME . "\n\n" . '<b>Observation :</b> ' . STORE_NAME . ' ne saurait en aucun cas responsable des utilisations qui pourrait �tre effectu�es sur une bo�te email d\'une tierce personne.');
?>