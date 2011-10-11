<?php

$uid = "";	// for the extra paranoid
unset($_SESSION["nliug"]);

if (isset($_SESSION["auth_redir_post"]) && is_array($_SESSION["auth_redir_post"]))
{
	$_POST = $HTTP_POST_VARS = $_SESSION["auth_redir_post"];
	$REQUEST_METHOD = "POST";
}


if (!empty($_GET["set_ui_lang"]))
{
	$_SESSION["user_adm_ui_lc"] = $_GET["set_ui_lang"];
}

// you cannot aw_startup() here, it _will_ break things
// reset aw_cache_* function globals
$GLOBALS["__aw_cache"] = array();

$u = new users();
$u->request_startup();

if (!empty($set_ct_lang_id))
{
	$_SESSION["ct_lang_id"] = $set_ct_lang_id;
	$_SESSION["ct_lang_lc"] = languages::get_langid($set_ct_lang_id);
	aw_global_set("ct_lang_lc", $_SESSION["ct_lang_lc"]);
	aw_global_set("ct_lang_id", $_SESSION["ct_lang_id"]);
}

$sf = new aw_template();

/* XXX: v2lja v6etud j6udluse t6stmiseks
TODO: vaadata kas vaja ning kas vaja teostust muuta
register_shutdown_function("log_pv", $GLOBALS["awt"]->timers["__global"]["started"]);
__init_aw_session_track();
*/

$sf->db_init();
$sf->tpl_init("automatweb");

/* XXX: v2lja v6etud j6udluse t6stmiseks
TODO: vaadata kas vaja ning kas vaja teostust muuta
if (!empty($_GET["id"]) || !empty($_GET["parent"]))
{
	$sc = new site_cache();
	$sc->ip_access(array("force_sect" => !empty($_GET["parent"]) ? $_GET["parent"] : $_GET["id"]));
}
*/
