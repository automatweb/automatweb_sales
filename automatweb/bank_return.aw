<?php

require_once("const.aw");

ini_set("session.save_handler", "files");
session_name("automatweb");
session_start();
unset($_SESSION["nliug"]);

if (is_array($_SESSION["auth_redir_post"]))
{
	$_POST = $HTTP_POST_VARS = $_SESSION["auth_redir_post"];
	extract($_POST);
	$REQUEST_METHOD = "POST";
}

if ($_GET["set_ui_lang"] != "")
{
	$_SESSION["user_adm_ui_lc"] = $_GET["set_ui_lang"];
}

// you cannot aw_startup() here, it _will_ break things
// reset aw_cache_* function globals
$GLOBALS["__aw_cache"] = array();

$u = new users;
$u->request_startup();

if ($set_ct_lang_id)
{
	$_SESSION["ct_lang_id"] = $set_ct_lang_id;
	$l = get_instance("languages");
	$_SESSION["ct_lang_lc"] = $l->get_langid($set_ct_lang_id);
	aw_global_set("ct_lang_lc", $_SESSION["ct_lang_lc"]);
	aw_global_set("ct_lang_id", $_SESSION["ct_lang_id"]);
}

$LC = aw_global_get("LC");

include(aw_ini_get("basedir")."/lang/" . $LC . "/errors.".aw_ini_get("ext"));
include(aw_ini_get("basedir")."/lang/" . $LC . "/common.".aw_ini_get("ext"));

$sf = new aw_template;
$sf->db_init();
$sf->tpl_init("automatweb");

//siit hakkab siis alles pangast tuleva infoga tegelemine
$_SESSION["bank_return"]["data"] = null;
foreach ($_POST as $key => $val)
{
	$_SESSION["bank_return"]["data"][$key] = $val;
}

if($_POST["VK_REF"])
{
	$id = substr($_POST["VK_REF"], 0, -1);
}
if($_GET["ecuno"])
{
	$id = substr($_GET["ecuno"], 0, -1);
}
foreach ($_GET as $key => $val)
{
	$_SESSION["bank_return"]["data"][$key] = $val;
}
//see siis automaatse tagasituleku puhul pangast, miskip'rast teeb hansa get meetodika selle
if($_SESSION["bank_return"]["data"]["VK_REF"])
{
	$id = substr($_SESSION["bank_return"]["data"]["VK_REF"] ,0 , -1 );
}
if($_SESSION["bank_return"]["data"]["SOLOPMT_RETURN_REF"])
{
	$id = substr($_SESSION["bank_return"]["data"]["SOLOPMT_RETURN_REF"] ,0 , -1 );
}

//logimine
$log = date("d/m/Y H:i : ",time());
$bi = get_instance(CL_BANK_PAYMENT);
$_SESSION["bank_return"]["data"]["timestamp"] = time();
$_SESSION["bank_return"]["data"]["ip"] = $_SERVER['REMOTE_ADDR'];
$_SESSION["bank_return"]["data"]["good"] = $bi->check_response();

//	arr($_POST); arr($_GET);arr($_SESSION);
//foreach($_SESSION["bank_return"]["data"] as $key => $val)
//{
//	$log.= $key." = ".$val.", ";
//}
$log.="\n";
$myFile = $site_dir."/bank_log.txt";
$fh = fopen($myFile, 'a');
fwrite($fh, serialize($_SESSION["bank_return"]["data"])."\n");
fclose($fh);

//esimene on hansapanga, EYP, sampo ja krediidipanga positiivne vastus, teine nordea(yksk6ik milline.. et negatiivne peaks mujale minema)... kolmas krediitkaardikeskuse
	if($_SESSION["bank_return"]["data"]["VK_SERVICE"] == 1101  || $_POST["VK_SERVICE"] == 1101 || $_GET["SOLOPMT_RETURN_PAID"] ||  ($_GET["action"] == "afb" && $_GET["respcode"] == "000")
	|| ($_SESSION["bank_return"]["data"]["action"] == "afb" && $_SESSION["bank_return"]["data"]["respcode"] == "000")
	)
{
	$url = $_SESSION["bank_payment"]["url"];
	if(!$url)
	{
		if(!$bi->can("view" , $id))
		{
			die("Kui sa seda n&auml;ed, siis l&auml;ks midagi pahasti");
		}
		$obj = obj($id);
		$inst = $obj->instance();
		$inst->bank_return(array("id" => $obj->id()));
	}
}
else
{
	$url = $_SESSION["bank_payment"]["cancel"];
	if($bi->can("view" , $id))
	{
		$obj = obj($id);
		$inst = $obj->instance();
		if (method_exists($inst,"bank_fail"))
		{
			$inst->bank_fail(array(
				"id" => $obj->id(),
				"url" => $url,
			));
		}
	}
}
/*
if(!$url)
{
	$obj = obj($id);
	$inst = $obj->instance();
	$inst->bank_return(array("id" => $obj->id()));
}
*/
header("Location:".$url);
die();
