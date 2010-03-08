<?php
// localization constans for users.aw

// messages
define("USR_LOGGED_IN","Logis sisse");
define("USR_LOGGED_OUT","Logis välja");


// errors
define("E_USR_UID_TOO_SHORT","Liiga lühike kasutajanimi, uid=%s, pass=%s");
define("E_USR_EXISTS","Kasutaja '%s' on juba olemas");
define("E_USR_PASS_TOO_SHORT","Liiga lühike parool, uid=%s, pass=%s");
define("E_USR_USER_UNKNOWN","Tundmatu kasutaja, uid=%s, pass=%s");
define("E_USR_WRONG_PASS","Vale parool, uid=%s, pass=%s");

define("E_USR_DYN_GROUP_UPDATE","can't update dynamic group, the search form for users does not specify the entry form for users as a search target!");

global $lc_users;

$lc_users["LC_JF_USERNAME"] = "User name:";
$lc_users["LC_JF_EMAIL"] = "E-mail:";
$lc_users["LC_JF_PASSWORD"] = "Password:";
$lc_users["LC_JF_PASSWORD2"] = "Password 2x:";
$lc_users["LC_JF_PASSWORD2"] = "Password 2x:";
$lc_users["LC_JF_NEXT"] = "Next";

$lc_users["LC_LOGS"] = "Logimisi";
$lc_users["LC_ONLINE"] = "Sisse logitud";
$lc_users["LC_LAST_LOGIN"] = "Viimati";
$lc_users["LC_ACTIONS"] = "Tegevused";

$lc_users["LC_PAGE"] = "Lehek&uuml;lg";

$lc_users["LC_CHANGE"] = "Muuda";
$lc_users["LC_PROPERTIES"] = "M&auml;&auml;rangud";
$lc_users["LC_CHANGE_PWD"] = "Muuda parooli";
$lc_users["LC_DELETE"] = "Kustuta";

?>
