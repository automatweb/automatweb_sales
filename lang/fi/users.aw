<?php
// localization constans for users.aw

// messages
define("USR_LOGGED_IN","Logged in");
define("USR_LOGGED_OUT","Logged out");


// errors
define("E_USR_UID_TOO_SHORT","Username too short, uid=%s, pass=%s");
define("E_USR_PASS_TOO_SHORT","Password too short, uid=%s, pass=%s");
define("E_USR_USER_UNKNOWN","Unknown user, uid=%s, pass=%s");
define("E_USR_WRONG_PASS","Wrong password, uid=%s, pass=%s");

define("E_USR_DYN_GROUP_UPDATE","can't update dynamic group, the search form for users does not specify the entry form for users as a search target!");

global $lc_users;

$lc_users["LC_JF_USERNAME"] = "User name:";
$lc_users["LC_JF_EMAIL"] = "E-mail:";
$lc_users["LC_JF_PASSWORD"] = "Password:";
$lc_users["LC_JF_PASSWORD2"] = "Password 2x:";
$lc_users["LC_JF_PASSWORD2"] = "Password 2x:";
$lc_users["LC_JF_NEXT"] = "Next";

$lc_users["LC_LOGS"] = "Logs";
$lc_users["LC_ONLINE"] = "Online";
$lc_users["LC_LAST_LOGIN"] = "Last login";
$lc_users["LC_ACTIONS"] = "Actions";

$lc_users["LC_PAGE"] = "Page";

$lc_users["LC_CHANGE"] = "Change";
$lc_users["LC_PROPERTIES"] = "Properties";
$lc_users["LC_CHANGE_PWD"] = "Change password";
$lc_users["LC_DELETE"] = "Delete";

?>
