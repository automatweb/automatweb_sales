<?php
// $Header: /home/cvs/automatweb_dev/lang/ru/messenger.aw,v 1.2 2005/05/20 08:19:44 kristo Exp $
// messenger.aw - Messengeri lokaliseeringud

// väljad otsinguvormis
global $lc_messenger;

define("MSG_FIELD_FROM","Kellelt");
define("MSG_FIELD_TO","Kellele");
define("MSG_FIELD_SUBJECT","Teema");
define("MSG_FIELD_CONTENT","Sisu");

// otsingutingimuste liitmisele vastavad stringid
define("MSG_CONNECTOR_AND"," ja ");
define("MSG_CONNECTOR_OR", " või ");

define("MSG_SEARCH_REMARK","Märgista need folderid, millest soovid otsida");

define("MSG_INIT","Initsialiseerin Messengeri.<br>");
define("MSG_INIT2","Kliki siia");

define("MSG_TITLE_FOLDERS","Folderid");
define("MSG_TITLE_SEARCH","Otsing");
define("MSG_TITLE_CONFIG","Messengeri konfigureerimine");
define("MSG_TITLE_RULES","Reeglid");

define("MSG_STATUS_CONFIG_SAVED","Konfiguratsioonimuudatused on salvestatud");
define("MSG_STATUS_SIGNATURE_SAVED","Signatuur on salvestatud");
define("MSG_STATUS_SIGNATURE_ADDED","Signatuur on lisatud");
define("MSG_STATUS_ACCOUNT_ADDED","Konto on lisatud");
define("MSG_STATUS_ACCOUNT_SAVED","Konto on salvestatud");

define("MSG_STATUS_MAIL_RECEIVED","Received %d messages from %d accounts");


define("MSG_MAILBOX_OP_WARNING","Ühtegi teadet polnud märgistatud, seega ei tehtud midagi");
define("MSG_MAILBOX_OP_DELETED","%d teadet kustutati");
define("MSG_MAILBOX_OP_MOVED","%d teadet viidi teise folderisse");
define("MSG_MAILBOX_OP_MARKREAD","%d teadet märgiti loetuks");
define("MSG_MAILBOX_OP_MARKNEW","%d teadet märgiti uueks");
define("MSG_MAILBOX_OP_UNKNOWN","Tundmatu operatsioonikood - %s");

define("MSG_ADDR_CHECK_FAILED","Ühtegi korrektset aadressi ei leitud. Kontrollige üle");
define("MSG_FROM_CHECK_FAILED","E-mail address for this account has not been set. Cannot send any messages before you do that");

define("MSG_EDIT_SIGNATURE","Muuda signatuur");
define("MSG_NEW_SIGNATURE","Uus signatuur");

$lc_messenger["LC_MESSENGER_FROM"] = "Kellelt";
$lc_messenger["LC_MESSENGER_SUBJECT"] = "Teema";
$lc_messenger["LC_MESSENGER_TIME"] = "Aeg";
$lc_messenger["LC_MESSENGER_STATUS"] = "Staatus";
$lc_messenger["LC_MESSENGER_NEW_MESSAGE"] = "Uus teade";





?>
