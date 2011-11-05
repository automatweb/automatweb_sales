<?php
if (!defined("AW_CONST_INC"))
{


define("AW_CONST_INC", 1);
// 1:42 PM 8/3/2008 - const.aw now contains only parts of old startup script that are to be moved to new appropriate files or deleted. const.aw file to be removed eventually.

//UnWasted - set_magic_quotes_runtime is deprecated since php 5.3
if (version_compare(PHP_VERSION, '5.3.0', '<')) set_magic_quotes_runtime(0);

foreach ($GLOBALS["cfg"] as $key => $value)
{
	if (!is_array($value))
	{
		$GLOBALS["cfg__default__short"][$key] = $value;
	}
}

function get_time()
{
	list($micro,$sec) = explode(" ",microtime());
	return ((float)$sec + (float)$micro);
}
define ("AW_SHORT_PROCESS", 1);
define ("AW_LONG_PROCESS", 2);
$section = null;

if (get_magic_quotes_gpc() && !defined("GPC_HANDLER"))
{
	function stripslashes_deep($value)
	{
		$value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
		return $value;
	}

	$_POST = array_map('stripslashes_deep', $_POST);
	$_GET = array_map('stripslashes_deep', $_GET);
	$_COOKIE = array_map('stripslashes_deep', $_COOKIE);
	define("GPC_HANDLER", 1);
}

$pi = "";

$PATH_INFO = isset($_SERVER["PATH_INFO"]) ? $_SERVER["PATH_INFO"] : null;
$QUERY_STRING = isset($_SERVER["QUERY_STRING"]) ? $_SERVER["QUERY_STRING"] : null;
$REQUEST_URI = isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : null;

$PATH_INFO = isset($PATH_INFO) ? preg_replace("|\?automatweb=[^&]*|","", $PATH_INFO) : "";
$QUERY_STRING = isset($QUERY_STRING) ? preg_replace("|\?automatweb=[^&]*|","", $QUERY_STRING) : "";

if (($QUERY_STRING == "" && $PATH_INFO == "") && $REQUEST_URI != "")
{
        $QUERY_STRING = $REQUEST_URI;
        $QUERY_STRING = str_replace("xmlrpc.aw", "", str_replace("index.aw", "", str_replace("orb.aw", "", str_replace("login.aw", "", str_replace("reforb.aw", "", $QUERY_STRING)))));
}

if (strlen($PATH_INFO) > 1)
{
	$pi = $PATH_INFO;
}

if (strlen($QUERY_STRING) > 1)
{
	$pi .= "?".$QUERY_STRING;
}

$pi = trim($pi);

if (substr($pi, 0, 12) === "/class=image")
{
	$pi = substr(str_replace("/", "&", str_replace("?", "&", $pi)), 1);
	parse_str($pi, $_GET);
	extract($_GET);
}

if (substr($pi, 0, 11) === "/class=file")
{
	$pi = substr(str_replace("/", "&", str_replace("?", "&", $pi)), 1);
	parse_str($pi, $_GET);
	extract($_GET);
}
elseif (substr($pi, 0, 15) === "/class=flv_file")
{
	$pi = substr(str_replace("/", "&", str_replace("?", "&", $pi)), 1);
	parse_str($pi, $_GET);
	extract($_GET);
}
else
{
	$_SERVER["REQUEST_URI"] = isset($_SERVER['REQUEST_URI']) ? preg_replace("|\?automatweb=[^&]*|","", $_SERVER["REQUEST_URI"]) : "";
	$pi = preg_replace("|\?automatweb=[^&]*|ims", "", $pi);
	if ($pi)
	{
		if (($_pos = strpos($pi, "section=")) === false)
		{
			// ok, we need to check if section is followed by = then it is not really the section but
			// for instance index.aw/set_lang_id=1
			// we check for that like this:
			// if there are no / or ? chars before = then we don't prepend

			$qpos = strpos($pi, "?");
			$slpos = strpos($pi, "/");
			$eqpos = strpos($pi, "=");
			$qpos = $qpos ? $qpos : 20000000;
			$slpos = $slpos ? $slpos : 20000000;

			if (!$eqpos || ($eqpos > $qpos || $slpos > $qpos))
			{
				// if no section is in url, we assume that it is the first part of the url and so prepend section = to it
				$pi = str_replace("?", "&", "section=".substr($pi, 1));
			}
		}

		// support for links like http://bla/index.aw?291?lcb=117 ?424242?view=3&date=20
		// this is a quick fix for a specific problem on june 22th 2010 with opera.ee site
		// might have been a configuration error, for increase of tolerance in that case then
		if (preg_match("/^\\?([0-9]+)\\?/", $pi, $section_info))
		{
			$section = $section_info[1];
		}

		if (($_pos = strpos($pi, "section=")) !== false)
		{
			// this here adds support for links like http://bla/index.aw/section=291/lcb=117
			$t_pi = substr($pi, $_pos+strlen("section="));
			if (($_eqp = strpos($t_pi, "="))!== false)
			{
				$t_pi = substr($t_pi, 0, $_eqp);
				$_tpos1 = strpos($t_pi, "?");
				$_tpos2 = strpos($t_pi, "&");
				if ($_tpos1 !== false || $_tpos2 !== false)
				{
					// if the thing contains ? or & , then section is the part before it
					if ($_tpos1 === false)
					{
						$_tpos = $_tpos2;
					}
					else
					if ($_tpos2 === false)
					{
						$_tpos = $_tpos1;
					}
					else
					{
						$_tpos = min($_tpos1, $_tpos2);
					}
					$section = substr($t_pi, 0, $_tpos);
				}
				else
				{
					// if not, then te section is the part upto the last /
					$_lslp = strrpos($t_pi, "/");
					if ($_lslp !== false)
					{
						$section = substr($t_pi, 0, $_lslp);
					}
					else
					{
						$section = $t_pi;
					}
				}
			}
			else
			{
				$section = $t_pi;
			}
		}
	}

	if (aw_ini_get("menuedit.language_in_url"))
	{
		$section = substr(strstr($section, "/"), 1);
	}
}

$GLOBALS["section"] = $section;

$ext = "aw";  // filename extension

if (empty($LC))
{
	$LC="et";
	aw_global_set("LC", $LC);
}

// stat function fields
define("FILE_SIZE",7);
define("FILE_MODIFIED",9);

// please use $row[OID] instead of row["oid"] everywhere you can,
// because "oid" is a reserved word in postgres (and probably others)
// and we really-really want to port AW to other databases ASAP
define("OID","oid");



/////////// DEPRECATED. use menu_obj::TYPE_... constants instead
// mix 69? well mulle meeldib see number :-P
define("MN_CLIENT",69);
// sisurubriik
define("MN_CONTENT",70);
// adminni ylemine menyy
define("MN_ADMIN1",71);
// promo kast
define("MN_PROMO_BOX",73);
// kodukataloog
define("MN_HOME_FOLDER",74);
// kodukataloogi alla tehtud kataloog, et sharetud katalooge olex lihtsam n2idata
define("MN_HOME_FOLDER_SUB",75);
// formi element, mis on samas ka menyy
define("MN_FORM_ELEMENT",76);
// public method
define("MN_PMETHOD",77);
///////// END DEPRECATED



// formide tyybid
define("FTYPE_ENTRY",1);
define("FTYPE_SEARCH",2);
define("FTYPE_FILTER_SEARCH",4);
define("FTYPE_CONFIG",5);

// formide alamtyybid
// subtype voiks bitmask olla tegelikult
define("FSUBTYPE_JOIN",1);

// kas seda vormi saab kasutada eventite sisestamiseks
// mingisse kalendrisse?
define("FSUBTYPE_EV_ENTRY",2);

// kas seda vormi saab kasutada vormi baasil e-maili
// actionite tegemiseks?
define("FSUBTYPE_EMAIL_ACTION",4);

// kas seda vormi kasutatakse kalendri ajavahemike defineerimiseks?
define("FSUBTYPE_CAL_CONF",8);

// kui see on otsinguvorm, siis kas otsingutulemusi filtreeritakse
// l2bi kalendri?
define("FSUBTYPE_CAL_SEARCH",16);

// like CAL_CONF, but data is entered directly
define("FSUBTYPE_CAL_CONF2",32);

// sum of all form & calendar settings, used to figure out
// whether a form has any relation to a calendar
define("FORM_USES_CALENDAR",58);

// object flags - bitmask
define("OBJ_FLAGS_ALL", (1 << 30)-1);	// this has all the flags checked, so you can build masks, by negating this

define("OBJ_HAS_CALENDAR",1 << 0);
// this will be set for objects that need to be translated
define("OBJ_NEEDS_TRANSLATION",1 << 1);
// this will be set for objects whose translation has been checked/confirmed
define("OBJ_IS_TRANSLATED",1 << 2);
// this will be used for objects with calendar functionality
define("OBJ_IS_DONE",1 << 3);
// if you need to select an active object from a bunch of objects, then this flag marks the active object
define("OBJ_FLAG_IS_SELECTED", 1 << 4);
// this says that the object is part of the auto-object translation. in addition to this it can have the NEEDS_TRANSLATION ot IS_TRANSLATED
define("OBJ_HAS_TRANSLATION", 1 << 5);
// this says that the object used to be a calendar vacancy
define("OBJ_WAS_VACANCY", 1 << 6);

// objektide subclassid - objects.subclass sees juusimiseks

// for CL_BROTHER_DOCUMENT
define("SC_BROTHER_DOC_KEYWORD", 1);	// kui dokumendi vend on tehtud t2nu menuu keywordile

// always-defined reltypes
define("RELTYPE_BROTHER", 10000);
define("RELTYPE_ACL", 10001);

//Date formats
define("LC_DATE_FORMAT_SHORT", 1); // For example: 20.06.88 or 05.12.98
define("LC_DATE_FORMAT_SHORT_FULLYEAR", 2); // For example: 20.06.1999 or 05.12.1998
define("LC_DATE_FORMAT_LONG", 3); // For example: 20. juuni 99
define("LC_DATE_FORMAT_LONG_FULLYEAR", 4); // For example: 20. juuni 1999

// project statuses
define("PROJ_IN_PROGRESS", 1);
define("PROJ_DONE", 2);

function ifset(&$item_orig)
{
	$i = 0;
	$count = func_num_args();
	$item =& $item_orig;
	for (; $i < $count-1; $i++)
	{
		$key = func_get_arg($i+1);
		if (is_array($item) && isset($item[$key]))
		{
			$item =& $item[$key];
		}
		else if (is_object($item) && isset($item->$key))
		{
			$item =& $item->$key;
		}
		else
		{
			return null;
		}
	}
	return $item;
}

function aw_config_init_class($that)
{
	$class = get_class($that);
	$that->cfg = array_merge((isset($GLOBALS["cfg"][$class]) ? $GLOBALS["cfg"][$class] : array()),$GLOBALS["cfg__default__short"]);
	$that->cfg["acl"] = $GLOBALS["cfg"]["acl"];
	$that->cfg["config"] = $GLOBALS["cfg"]["config"];
}

// loads localization variables from the site's $site_basedir
function lc_site_load($file, $obj)
{
	if (aw_ini_get("user_interface.full_content_trans") === "1")
	{
		$LC = aw_global_get("ct_lang_lc");
	}
	else
	{
		$LC = aw_global_get("LC");
	}

	if (empty($LC))
	{
		$LC = "et";
	}

	$fname_site = aw_ini_get("site_basedir")."lang/{$LC}/{$file}" . AW_FILE_EXT;
	$fname_default = AW_DIR . "lang/{$LC}/{$file}" . AW_FILE_EXT;
	if (is_readable($fname_site))
	{
		include_once $fname_site;
	}
	elseif (is_readable($fname_default))
	{
		include_once $fname_default;
	}

	if ($obj instanceof aw_template)
	{
		// kui objekt anti kaasa, siis loeme tema template sisse muutuja $lc_$file
		$var = "lc_{$file}";
		if (isset($$var) and is_array($$var))
		{
			$obj->vars($$var);
		}
	}
}

// nyyd on voimalik laadida ka mitu librat yhe calliga
// a la classload("users","groups","someothershit");
//
// kurat. j6le n6me. nimelt siit inkluuditud asjad ei satu ju globaalsesse skoopi,
// niiet ei tasu imestada kui muutujaid faili sees 2kki pole :P
// a nuh, muud varianti ka pole - terryf
function classload($args)
{
	$arg_list = func_get_args();
	while(list(,$lib) = each($arg_list))
	{
		// let's not allow including ../../../etc/passwd :)
		$default_lib = $lib = $olib = str_replace(".","", $lib);

		//klassile pakihalduse teemalise versiooni
		//$lib muutuja on paketihalduse versiooni nimega, $default_lib ilma - default v22rtuseks
		if(function_exists("get_class_version"))
		{
			$lib = get_class_version($lib);
		}

		try
		{
			$cl_id = aw_ini_get("class_lut.".basename($lib));
		}
		catch (Exception $e)
		{
		}

		if (isset($cl_id) and isset($GLOBALS["cfg"]["classes"][$cl_id]["site_class"]) and $GLOBALS["cfg"]["classes"][$cl_id]["site_class"] == 1)
		{
			$lib = $GLOBALS["cfg"]["site_basedir"]."classes/".basename($lib).".".$GLOBALS["cfg"]["ext"];
		}
		elseif (substr($lib,0,13) === "designedclass")
		{
			$lib = basename($lib);
			$lib = $GLOBALS["cfg"]["site_basedir"]."files/classes/".$lib.".".$GLOBALS["cfg"]["ext"];
		}
		else
		{
			$lib = $GLOBALS["cfg"]["classdir"].$lib.".".$GLOBALS["cfg"]["ext"];

			if (isset($GLOBALS['cfg']['user_interface']["default_language"]) && ($adm_ui_lc = $GLOBALS["cfg"]["user_interface"]["default_language"]) != "")
			{
				$trans_fn = $GLOBALS["cfg"]["basedir"]."lang/trans/$adm_ui_lc/aw/".basename($lib);
				if (is_readable($trans_fn))
				{
					require_once($trans_fn);
				}
				else
				{
					$trans_fn = $GLOBALS["cfg"]["basedir"]."lang/trans/$adm_ui_lc/aw/".basename($default_lib);
					if (is_readable($trans_fn))
					{
						require_once($trans_fn);
					}
				}
			}
		}

		if (is_readable($lib))
		{
			include_once($lib);
		}
		else
		{
			if (empty($olib))
			{
				throw new aw_exception("Can't load class when no name given.");
			}

			// try to handle it with class_index and autoload
			class_index::load_class(basename($olib));
		}
	}
}

function get_instance($class, $args = array(), $errors = true)
{
	if (empty($class))
	{
		throw new aw_exception("Can't load class when no name given.");
	}

	if (!empty($GLOBALS["TRACE_INSTANCE"]))
	{
		echo "get_instance $class from ".dbg::short_backtrace()." <br>";
	}

	$site = $designed = false;
	if (is_numeric($class))
	{
		if (!aw_ini_isset("classes.{$class}"))
		{
			$designed = true;
		}
		else
		{
			$class = aw_ini_get("classes.{$class}.file");
		}
	}

	try
	{
		$cl_id = aw_ini_get("class_lut.".basename($class));
		$site = aw_ini_isset("classes." . $cl_id . ".site_class");
	}
	catch (Exception $e)
	{
		$site = false;
	}

	if (substr($class,0,13) === "designedclass")
	{
		$designed = true;
	}

	$lib = basename($class);
	$rs = "";
	$clid = (isset($GLOBALS['cfg']['class_lut']) && isset($GLOBALS["cfg"]["class_lut"][$lib])) ? $GLOBALS["cfg"]["class_lut"][$lib] : 0;
	if (isset($GLOBALS['cfg']['classes'][$clid]))
	{
		$clinf = $GLOBALS['cfg']['classes'][$clid];
		$rs = isset($clinf["is_remoted"]) ? $clinf["is_remoted"] : null;
	};
	// check if the class is remoted. if it is, then create proxy class instance, not real class instance
	if ($rs != "")
	{
		if ($rs != $GLOBALS["cfg"]["baseurl"])
		{
			$proxy_file = $GLOBALS["cfg"]["basedir"]."classes/core/proxy_classes/".$lib.".aw";
			$proxy_class = "__aw_proxy_".$lib;
			include_once($proxy_file);
			return new $proxy_class($rs);
		}
	}

	if ($site)
	{
		$classdir = aw_ini_get("site_basedir")."classes/";
	}
	else if ($designed)
	{
		$classdir = aw_ini_get("site_basedir")."files/classes/";
		$class = basename($class);
		$lib = $GLOBALS["gen_class_name"];
	}
	else
	{
		$classdir = aw_ini_get("classdir");
	}

	$replaced = str_replace(".","", $class);
	//klassile pakihalduse teemalise versiooni

	if (!file_exists($classdir.$replaced.AW_FILE_EXT))
	{
		class_index::load_class(basename($class));
	}

	if(function_exists("get_class_version"))
	{
		$replaced = get_class_version($replaced);
	}

	$_fn = $classdir.$replaced.AW_FILE_EXT;

	if (is_readable($_fn) && !class_exists($lib))
	{
		require_once($_fn);
	}

	// also load translations
	if (isset($GLOBALS["cfg"]["user_interface"]["default_language"]) && ($adm_ui_lc = $GLOBALS["cfg"]["user_interface"]["default_language"]) != "")
	{
		$trans_fn = $GLOBALS["cfg"]["basedir"]."lang/trans/$adm_ui_lc/aw/".basename($class).AW_FILE_EXT;

		if (is_readable($trans_fn))
		{
			require_once($trans_fn);
		}
	}

	if (class_exists($lib))
	{
		if (sizeof($args) > 0)
		{
			$instance = new $lib($args);
		}
		else
		{
			$instance = new $lib();
		}
	}
	else
	{
		$instance = false;
	}

	// now register default members - we do this here, because they might have changed
	// from the last time that the instance was created
	$members = aw_cache_get("__aw_default_class_members", $lib);
	if (is_array($members))
	{
		foreach($members as $k => $v)
		{
			$instance->$k = $v;
		}
	}

	if (aw_global_get("__is_install") && method_exists($instance, "init"))
	{
		$instance->init();
	}

	return $instance;
}

////
// !A neat little functional programming function
function not($arg)
{
	return !$arg;
}

function load_vcl($lib)
{
	if (isset($GLOBALS['cfg']['user_interface']) && ($adm_ui_lc = $GLOBALS["cfg"]["user_interface"]["default_language"]) != "")
	{
		$trans_fn = AW_DIR."lang/trans/{$adm_ui_lc}/aw/".basename($lib).AW_FILE_EXT;
		if (is_readable($trans_fn))
		{
			require_once($trans_fn);
		}
	}

	$fn = AW_DIR."classes/vcl/{$lib}".AW_FILE_EXT;
	if (is_readable($fn))
	{
		include_once($fn);
	}
}


////
// !here we initialize the stuff that we couldn't initialize in parse_config, cause there are no services
// available in parse_config, but now most of them are.
function aw_startup()
{
	// reset aw_cache_* function globals
	$GLOBALS["__aw_cache"] = array();

	// check multi-lang frontpage
	if (is_array(aw_ini_get("frontpage")))
	{
		$tmp = aw_ini_get("frontpage");
		$GLOBALS["cfg"]["ini_frontpage"] = $tmp;
		$GLOBALS["cfg"]["frontpage"] = $tmp[aw_global_get("lang_id")];
	}

	$p = new period();
	$p->request_startup();

	// this check reduces the startup memory usage for not logged in users by a whopping 1.3MB! --duke
	//
	// the check was if user is logged on. now we need to do this all the time, because public users are acl controlled now.
	$u = new users();
	$u->request_startup();

	if (!is_array(aw_global_get("gidlist")))
	{
		aw_global_set("gidlist", array());
		aw_global_set("gidlist_pri", array());
	}

	aw_global_set("aw_init_done", 1);

	$m = new menuedit();
	$m->request_startup();
	/* TODO: vaadata kas vaja ning kas vaja paremini teostada
	__init_aw_session_track();
	*/
}

////
// !called just before the very end
function aw_shutdown()
{
	/*
	//TODO: vaadata yle kas vaja ning kas muuta teostust.
	//this messenger thingie goes here
	$i = get_instance("file");
	if(isset($_SESSION["current_user_has_messenger"]) and $i->can("view", $_SESSION["current_user_has_messenger"]) and $i->can("view", $_SESSION["uid_oid"]))
	{
		$cur_usr = new object($_SESSION["uid_oid"]);
		if (((time() - $_SESSION["current_user_last_m_check"]) > (5 * 60)) && $cur_usr->prop("notify") == 1)
		{
			$drv_inst = get_instance("protocols/mail/imap");
			$drv_inst->set_opt("use_mailbox", "INBOX");

			$inst = new object($_SESSION["current_user_has_messenger"]);
			$conns = $inst->connections_from(array("type" => "RELTYPE_MAIL_SOURCE"));
			list(,$_sdat) = each($conns);
			$sdat = new object($_sdat->to());

			$drv_inst->connect_server(array("obj_inst" => $_sdat->to()));
			$emails = $drv_inst->get_folder_contents(array(
				"from" => 0,
				"to" => "*",
			));

			foreach($emails as $mail_id => $data)
			{
				if($data["seen"] == 0)
				{
					$new[] = $data["fromn"];
				}
			}

			$count = count($new);
			$new = join(", ", $new);
			if(strlen($new))
			{
				$sisu = sprintf(t("Sul on %s lugemata kirja! (saatjad: %s)"), $count, $new);
				$_SESSION["aw_session_track"]["aw"]["do_message"] = $sisu;
			}

			$_SESSION["current_user_last_m_check"] = time();
		}

	}
	// end of that messenger new mail notifiaction crap
	*/
}

function __get_site_instance()
{
	static $__site_instance;
	if (!is_object($__site_instance))
	{
		$fname = aw_ini_get("site_basedir")."public/site".AW_FILE_EXT;
		if (is_readable($fname))
		{
			include_once($fname);
		}
		else
		{
			$fname = aw_ini_get("site_basedir")."htdocs/site".AW_FILE_EXT;
			if (is_readable($fname))
			{
				include_once($fname);
			}
		}

		if (class_exists("site", false))
		{
			$__site_instance = new site();
		}
		else
		{
			$__site_instance = new site_base();
		}
	}
	return $__site_instance;
}

// DEPRECATED profiling functions
function enter_function($name,$args = array()){}
function exit_function($name,$ret = ""){}

function aw_set_exec_time($c_type)
{
	if ($c_type == AW_LONG_PROCESS)
	{
		set_time_limit( aw_ini_get("core.long_process_exec_time") );
	}
	if ($c_type == AW_SHORT_PROCESS)
	{
		set_time_limit( aw_ini_get("core.default_exec_time") );
	}
}

function __init_aw_session_track()
{return;
	if ($_SERVER["REQUEST_METHOD"] != "GET")
	{
		return;
	}
	if (!empty($_SESSION["aw_session_track"]["aw"]["do_redir"]))
	{
		$tmp = $_SESSION["aw_session_track"]["aw"]["do_redir"];
		$_SESSION["aw_session_track"]["aw"]["do_redir"] = "";
		header("Location: ".$tmp);
		die();
	}

	if (!empty($_SESSION["aw_session_track"]["aw"]["do_message"]))
	{
		$tmp = $_SESSION["aw_session_track"]["aw"]["do_message"];
		$_SESSION["aw_session_track"]["aw"]["do_message"] = "";
		echo "<script language=\"javascript\">alert(\"".$tmp."\");</script>";
	}

	// add session tracking options
	$_SESSION["aw_session_track"] = array(
		"server" => array(
			"ip" => isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : null,
			"referer" => isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : null,
			"ru" => $_SERVER["REQUEST_URI"],
			"site" => $_SERVER["HTTP_HOST"],
		),
		"aw" => array(
			"site_id" => aw_ini_get("site_id"),
			"lang_id" => aw_global_get("lang_id"),
			"uid" => aw_global_get("uid"),
			"timestamp" => time()
		)
	);
}

function call_fatal_handler($str)
{
	if (function_exists($GLOBALS["fatal_error_handler"]))
	{
		$GLOBALS["fatal_error_handler"]($str);
	}
}

function incl_f($lib) { return; } //DEPRECATED

}
