<?php

namespace automatweb;

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

lc_init();

// you cannot aw_startup() here, it _will_ break things
// reset aw_cache_* function globals
$GLOBALS["__aw_cache"] = array();
check_pagecache_folders();

$u = new users();
$u->request_startup();
$l = new languages();
$l->request_startup();

if (!empty($set_ct_lang_id))
{
	$_SESSION["ct_lang_id"] = $set_ct_lang_id;
	$l = new languages();
	$_SESSION["ct_lang_lc"] = $l->get_langid($set_ct_lang_id);
	aw_global_set("ct_lang_lc", $_SESSION["ct_lang_lc"]);
	aw_global_set("ct_lang_id", $_SESSION["ct_lang_id"]);
}

$LC = aw_global_get("LC");
$lc_file = AW_DIR."lang/{$LC}/errors".AW_FILE_EXT;
if (is_readable($lc_file))
{
	include($lc_file);
}

$lc_file = AW_DIR."lang/{$LC}/common".AW_FILE_EXT;
if (is_readable($lc_file))
{
	include($lc_file);
}

$sf = new aw_template();
$sf->db_init();
$sf->tpl_init("automatweb");

lc_load("automatweb");

?>
