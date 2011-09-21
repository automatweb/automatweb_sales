<?php

class languages implements request_startup, orb_public_interface
{
	const CACHE_KEY = "languages-cache-site_id-"; // internal cache file name/key

	// language code constant names correspond to ISO_639-3 codes
	// values can be calculated by languages::get_aw_lc()
	const LC_EST = 51920;
	const LC_ENG = 5147;
	const LC_RUS = 182119;
	const LC_DEU = 4521;
	const LC_FIN = 6914;
	const LC_LAV = 12122;
	const LC_LIT = 12920;
	const LC_SPA = 19161;
	const LC_FRA = 6181;
	const LC_SWE = 19235;


	private $req;
	private static $languages_data = array();
	private static $languages_metadata = array(
		"enabled_languages_count" => 0
	);
	private static $lc_lut = array(
		"et" => self::LC_EST,
		"en" => self::LC_ENG,
		"es" => self::LC_SPA,
		"fi" => self::LC_FIN,
		"fr" => self::LC_FRA,
		"lt" => self::LC_LIT,
		"lv" => self::LC_LAV,
		"ru" => self::LC_RUS,
		"sv" => self::LC_SWE
	);
	private static $lid_lut = array(
		self::LC_EST => "et",
		self::LC_ENG => "en",
		self::LC_SPA => "es",
		self::LC_FIN => "fi",
		self::LC_FRA => "fr",
		self::LC_LIT => "lt",
		self::LC_LAV => "lv",
		self::LC_RUS => "ru",
		self::LC_SWE => "sv"
	);


	/** Sets orb request to be processed by this object
		@attrib api=1 params=pos
		@param request type=aw_request
		@returns void
	**/
	public function set_request(aw_request $request)
	{
		$this->req = $request;
	}

	/** Returns language data array for given id parameter
		@attrib api=1 params=pos
		@param id type=int
			Language automatweb id
		@param no_cache type=bool default=FALSE
		@returns array
		@errors
			throws awex_lang_na if no language data for $id found
	**/
	public static function fetch($id, $no_cache = false)
	{
		if ($no_cache)
		{
			object_loader::ds()->quote($id);
			$language_data = object_loader::ds()->db_fetch_row("SELECT * FROM languages l JOIN objects o ON o.oid=l.oid WHERE l.aw_lid = '{$id}' AND o.status > 0");

			if ($language_data)
			{
				$language_data["meta"] = aw_unserialize($language_data["meta"]);
				$language_data["id"] = $language_data["aw_lid"]; // BC
			}
			elseif (aw_ini_isset("languages.list.{$id}"))
			{
				$language_data = self::_fetch_default($id);
			}
			else
			{
				throw new awex_lang_na("Language '{$id}' not found in database or default settings");
			}
		}
		elseif (isset(self::$languages_data[$id]))
		{
			$language_data = self::$languages_data[$id];
		}
		elseif (aw_ini_isset("languages.list.{$id}"))
		{
			$language_data = self::_fetch_default($id);
		}
		else
		{
			throw new awex_lang_na("Language '{$id}' not found");
		}

		return $language_data;
	}

	// get lang data from aw.ini
	private static function _fetch_default($id)
	{
		$language_data = aw_ini_get("languages.list.{$id}");
		$language_data = array(
			"oid" => 0,
			"aw_lid" => $id,
			"status" => object::STAT_NOTACTIVE,
			"modified" => 0,
			"modifiedby" => "",
			"site_id" => 0,
			"show_not_logged" => 1,
			"show_logged" => 1,
			"show_others" => 1,
			"in_use" => 0,
			"name" => $language_data["name"],
			"acceptlang" => $language_data["acceptlang"],
			"charset" => $language_data["charset"],
			"lang_code" => $language_data["lc"]
		);
		return $language_data;
	}

	/** returns a list of available languages
		@attrib api=1 params=name

		@param all_data type=bool default=FALSE
			If set to true, returns all data about the language, else just the name, defaults to false

		@param ignore_status type=bool default=FALSE
			If set to true, returns all languages, even the ones marked as not active, defaults to false

		@param addempty type=bool default=FALSE
			If set to true, the first element in the returned array is an empty one, this is for using it as listbox options, defaults to false

		@param key type=string default="aw_lid"
			The field to use as the array index, defaults to "aw_lid"

		@param set_for_user type=bool default=FALSE
			If set to true, only the languages that are selected from the user config are returned, defaults to false

		@returns
			list of languages as an array { key => language_data }

	**/
	public static function get_list($arr = array())
	{
		if (!empty($arr["addempty"]))
		{
			$ret = array("0" => "");
		}
		else
		{
			$ret = array();
		}

		$use_key = isset($arr["key"]) ? $arr["key"] : "aw_lid";

		$dat = self::listall(isset($arr["ignore_status"]) ? $arr["ignore_status"] : false);
		foreach($dat as $ldat)
		{
			if (!empty($arr["set_for_user"]))
			{
				$uo = obj(aw_global_get("uid_oid"));
				$tr_ls = $uo->prop("target_lang");
				if (is_array($tr_ls) && count($tr_ls) && !$tr_ls[$ldat["aw_lid"]])
				{
					continue;
				}
			}

			if (!(is_oid($ldat["oid"]) && !object_loader::can("view", $ldat["oid"])))
			{
				if (!empty($arr["all_data"]))
				{
					$ret[$ldat[$use_key]] = $ldat;
				}
				else
				{
					$ret[$ldat[$use_key]] = $ldat["name"];
				}
			}
		}
		return $ret;
	}

	static function list_logged()
	{
		$ret = array();
		foreach(self::$languages_data as $row)
		{
			if (ifset($row, "show_logged") == 1)
			{
				$ret[$row["aw_lid"]] = $row;
			}
		}
		return $ret;
	}

	static function list_not_logged()
	{
		$ret = array();
		foreach(self::$languages_data as $row)
		{
			if ($row["show_not_logged"] == 1)
			{
				$ret[$row["aw_lid"]] = $row;
			}
		}
		return $ret;
	}

	static function list_others()
	{
		$ret = array();
		foreach(self::$languages_data as $row)
		{
			if ($row["show_others"] == 1)
			{
				$ret[$row["aw_lid"]] = $row;
			}
		}
		return $ret;
	}

	/** Lists languages that are in use to be translated to
		@attrib api=1 params=pos
		@returns object_list(CL_LANGUAGE)
	**/
	public static function list_translate_targets()
	{
		$list = new object_list(array(
			"class_id" => CL_LANGUAGE,
			"show_logged" => 1
		));
		return $list;
	}

	static function listall($ignore_status = false)
	{
		if (!$ignore_status)
		{
			$ret = array();
			$ret = self::list_logged();
			if(sizeof($ret))
			{
				return $ret;
			}
			foreach(self::$languages_data as $row)
			{
				if ($row["status"] == 2)
				{
					$ret[$row["aw_lid"]] = $row;
				}
			}
			return $ret;
		}
		else
		{
			return self::$languages_data;
		}
	}

	/** Sets active language and redirects if requested.
		@attrib name=set_active
		@param id required type=int
			Language id to set
		@param return_url optional type=string
			URL to redirect back to
		@comment
			Public interface to set_active api method
			Redirects to baseurl if given return url is invalid

		@returns void
	**/
	public static function _set_active($arr)
	{
		$r = self::set_active($arr["id"]);

		if ($r !== (int) $arr["id"])
		{
			class_base::show_error_text("Keele vahetamine eba&otilde;nnestus.");
		}

		if (!empty($arr["return_url"]))
		{
			try
			{
				aw_redirect(new aw_uri($arr["return_url"]));
			}
			catch (Exception $e)
			{
				aw_redirect(aw_ini_get("baseurl"));
			}
		}
	}

	/** Sets the active language to $id
		@attrib api=1 params=pos
		@param id type=int
			Language id (aw_lang_id property)
		@param force_act type=bool default=FALSE
			If TRUE and user has logged in then requested language will be set active regardless of its object status (excluding 'deleted')
		@comment
		@returns int
			returns set language id or 0 if failed
		@errors none
	**/
	public static function set_active($id, $force_act = false)
	{
		$id = (int) $id;

		if (!aw_ini_isset("languages.list.{$id}.lc"))
		{
			return 0;
		}

		$l = self::fetch($id);
		if (($l["status"] != object::STAT_ACTIVE && !aw_global_get("uid")) && !$force_act)
		{
			return 0;
		}

		if (is_oid($l["oid"]) && !object_loader::can("", $l["oid"]))
		{
			return 0;
		}

		$q = "SELECT acceptlang, charset FROM languages l JOIN objects o ON o.oid=l.oid WHERE l.aw_lid = '{$id}' AND o.status > 0";
		object_loader::ds()->db_query($q);
		$row = object_loader::ds()->db_next();
		if ($row)
		{
			aw_session::set("LC", $row["acceptlang"]);
			aw_global_set("LC",$row["acceptlang"]);
			aw_session::set("ct_lang_lc", $row["acceptlang"]);
			aw_global_set("ct_lang_lc",$row["acceptlang"]);
			aw_global_set("charset",$row["charset"]);
		}

		// save selected language to globals, session, cookie and user preferences
		aw_global_set("lang_id", $id);

		$cookie_name = self::get_cookie_name();
		aw_session::set($cookie_name, $id);
		aw_cookie::set($cookie_name, $id, aw_ini_get("languages.cookie_lifetime"));

		$uid = aw_global_get("uid");
		if (!empty($uid))
		{
			object_loader::ds()->quote($uid);
			object_loader::ds()->db_query("UPDATE users SET lang_id = '{$id}' WHERE uid = '{$uid}'");
		}

		//...
		if (!is_admin())
		{
			// read the language from active lang
			if (aw_ini_get("user_interface.use_site_lang"))
			{
				if (aw_ini_get("user_interface.full_content_trans"))
				{
					$_tmp = aw_global_get("ct_lang_lc");
				}
				else
				{
					$_tmp = aw_global_get("LC");
				}

				aw_ini_set("user_interface.default_language", $_tmp);
			}
		}
		return $id;
	}

	/** Return active language id
		@attrib api=1 params=pos
		@comment
		@returns int
		@errors none
	**/
	public static function get_active()
	{
		$lang_id = aw_global_get("lang_id") or $lang_id = aw_session::get(self::get_cookie_name()) or $lang_id = aw_cookie::get(self::get_cookie_name());
		return (int) $lang_id;
	}

	////
	// !this tries to figure out the balance between the user's language preferences and the
	// languages that are available. this will only return active languages.
	private static function _find_best()
	{
		$langs = array();
		$def = 0;
		foreach(self::$languages_data as $row)
		{
			if ($row["status"] == 2 && (!is_oid($row["oid"]) || object_loader::can("view", $row["oid"])))
			{
				$langs[$row["acceptlang"]] = $row["aw_lid"];
				if (!$def)
				{
					// pick the first active one from the list in case no matches exist for browser settings
					$def = $row["aw_lid"];
				}
			}
		}

		// get all the user's preferences from the browser
		$larr = explode(",",aw_global_get("HTTP_ACCEPT_LANGUAGE"));
		reset($larr);
		while (list(,$v) = each($larr))
		{
			$la = substr($v,0,strcspn($v,"-; "));
			if (!empty($langs[$la]))
			{
				// and accept the first match, nobody uses the really fancy features anyway :P
				return $langs[$la];
			}
		}

		// if there were no matches then just pick the first one
		if ($def)
		{
			return $def;
		}

		// if no languages are active, then get the first one.
		if (count(self::$languages_data))
		{
			foreach (self::$languages_data as $row)
			{
				return $row["aw_lid"];
			}
		}

		// if there are no languages defined in the site, we are fucked anyway, so just return a reasonable number
		return self::LC_EST;
	}

	/**
		@attrib api=1 params=pos
		@param id type=int default=0
			Language id to get charset for. If not specified, current language charset returned
		@comment
		@returns string
		@errors
			throws awex_lang_na if no language data for $id found
	**/
	public static function get_charset($id = 0)
	{
		if (!$id)
		{
			$id = aw_global_get("lang_id");
		}
		$a = self::fetch($id);
		return $a["charset"];
	}

	/**
		@attrib api=1 params=pos
		@param lid type=int default=0
			Language id. Default means current language
		@returns string
			Default language code for language id. Empty string if id not found
		@errors none
	**/
	public static function get_default_code_for_id($lid)
	{
		return isset(self::$lid_lut[$lid]) ? self::$lid_lut[$lid] : "";
	}

	/**
		@attrib api=1 params=pos
		@param lid type=int
			Language id
		@returns string
			Language code. Empty string if id not found
		@errors none
	**/
	public static function get_code_for_id($lid)
	{
		try
		{
			$l = self::fetch($lid);
			$lc = $l["acceptlang"];
		}
		catch (Exception $e)
		{
			$lc = "";
		}
		return $lc;
	}

	//DEPRECATED. use get_code_for_id() or get_default_code_for_id()
	public static function get_langid($id = -1)
	{ trigger_error("get_langid() is deprecated. use get_code_for_id() or get_default_code_for_id(). Called from " . get_caller_str(1), E_USER_DEPRECATED); if ($id == -1)  { $id = aw_global_get("lang_id"); }$a = self::fetch($id); return $a["acceptlang"]; }

	/** Finds the language id for a language code (en, et, ...)
		@attrib api=1 params=pos

		@param lc required type=string
			The code to find the language id for

		@returns int
			0 if no language for the code is defined in the system, language id (not language object id)

		@errors none
	**/
	public static function get_id_for_code($lc)
	{
		return isset(self::$lc_lut[$lc]) ? self::$lc_lut[$lc] : 0;
	}

	/** Counts active languages
		@attrib api=1 params=pos
		@returns int
		@errors none
	**/
	public static function count()
	{
		return self::$languages_metadata["enabled_languages_count"];
	}

	////
	// !this reads all the languages in the site to aw language cache, all the functions in this file use that
	static function init_cache($force_read = false)//XXX: language klass kasutab, pole private
	{
		if ($force_read || !($_it = aw_global_get("lang_cache_init")))
		{
			$cf_name = self::CACHE_KEY . aw_ini_get("site_id");
			$meta_cf_name = $cf_name . "-meta";

			// now try the file cache thingie - maybe it's faster :) I mean, yeah, ok,
			// this doesn't exactly take much time anyway, but still, can't be bad, can it?

			// if the file cache exists and this is not an update, then read from that
			if (!$force_read && ($languages_data = cache::file_get($cf_name)) && ($languages_metadata = cache::file_get($meta_cf_name)))
			{
				self::$languages_data = aw_unserialize($languages_data);
				self::$languages_metadata = aw_unserialize($languages_metadata);
			}
			else
			{
				// we must re-read from the db and write the cache
				object_loader::ds()->db_query("SELECT l.*, o.comment as comment FROM languages l JOIN objects o ON l.oid = o.oid WHERE o.status > 0 GROUP BY o.oid ORDER BY o.jrk");
				$c = 0;
				while ($row = object_loader::ds()->db_next())
				{
					$row["meta"] = aw_unserialize($row["meta"]);
					$row["id"] = $row["aw_lid"]; // BC
					//the following if was in the form of if(true || .....), so i guess there was
					//a reason for that, i checked on eures the values of the variables, and the
					//if without the true seems to work too, if anything goes wrong, i can always
					//write it back in, this why i'm writing this comment :)
					if (trim($row["site_id"]) == "" || in_array(aw_ini_get("site_id"), explode(",", trim($row["site_id"]))))
					{
						self::$languages_data[$row["aw_lid"]] = $row;
						if ($row["status"] == object::STAT_ACTIVE)
						{
							++self::$languages_metadata["enabled_languages_count"];
						}
					}
				}

				cache::file_set($cf_name, aw_serialize(self::$languages_data));
				cache::file_set($meta_cf_name, aw_serialize(self::$languages_metadata));
			}

			aw_global_set("lang_cache_init",1);
		}
	}

	////
	// !this will get called once in the beginning of the page, so that the class can initialize itself nicely
	public function request_startup()
	{
		self::init_cache();
		$lang_id = aw_global_get("lang_id");

		// if we explicitly request language change, we get that, except if the language is not active
		// and we are not logged in
		if (($sl = aw_global_get("set_lang_id")))
		{
			// if language has not changed, don't waste time re-setting it
			if ($sl != $lang_id)
			{
				if (($_l = self::set_active($sl)))
				{
					$lang_id = $_l;
				}
				// if request to change language is denied
				// then we sould remain with the old one, methinks
			}
		}

		if (aw_ini_get("menuedit.language_in_url"))
		{
			list($lang) = explode("/", aw_global_get("section"));
			if (strlen($lang) == 2 && aw_global_get("ct_lang_lc") != $lang)
			{
				// set lang from url
				$lang_id =  self::get_id_for_code($lang);
				aw_session::set("ct_lang_id", $lang_id);
				aw_session::set("ct_lang_lc", $lang);
				aw_global_set("ct_lang_lc", $lang);
				aw_global_set("ct_lang_id", $lang_id);
				setcookie("ct_lang_id", $lang_id, time() + 3600, "/");
				setcookie("ct_lang_lc", $lang, time() + 3600, "/");
			}
		}

		if (!aw_global_get("ct_lang_id") && aw_ini_get("user_interface.full_content_trans") && ($ct_lc = aw_ini_get("user_interface.default_language")))
		{
			if (!empty($_COOKIE["ct_lang_id"]))
			{
				$ct_id = $_COOKIE["ct_lang_id"];
				$ct_lc = $_COOKIE["ct_lang_lc"];
			}
			else
			{
				$ct_id = self::get_id_for_code($ct_lc);
			}

			aw_session::set("ct_lang_lc", $ct_lc);
			aw_session::set("ct_lang_id", $ct_id);
			aw_global_set("ct_lang_lc", $ct_lc);
			aw_global_set("ct_lang_id", $ct_id);
		}

		if (!$lang_id && aw_ini_get("languages.default"))
		{
			$lang_id = aw_ini_get("languages.default");
			try
			{
				$la = self::fetch($lang_id);
			}
			catch (awex_lang_na $e)
			{
				$la = self::fetch($lang_id, true);
			}
			self::set_active($lang_id, true);
		}
		else
		{
			// if at this point no language is active, then we must select one
			if (!$lang_id)
			{
				// try to find one by looking at the preferences the user has set in his/her browser
				$lang_id = self::_find_best();
				// since find_best() pulls just about every trick in the book to try and find a
				// suitable lang_id, we will just force it to be set active, since we can't do better anyway
				self::set_active($lang_id, true);
				$la = self::fetch($lang_id);
			}
			else
			{
				// if a language is active, we must check if perhaps someone kas de-activated it in the mean time
				$la = self::fetch($lang_id, true);
				if (!($la["status"] == 2 || ($la["status"] == 1 && aw_global_get("uid") != "")) || (is_oid($la["oid"]) && !object_loader::can("", $la["oid"])))
				{
					// if so, try to come up with a better one.
					$lang_id = self::_find_best();
					self::set_active($lang_id, true);
					$la = self::fetch($lang_id);
				}
			}
		}

		// assign the correct language so we can find translations
		$LC = $la["acceptlang"];
		if ($LC == "")
		{
			$LC = "et";
		}

		aw_global_set("LC", $LC);

           // if parallel trans is on, then read charset from trans lang
		if (aw_ini_get("user_interface.full_content_trans") && aw_global_get("ct_lang_id") != $lang_id)
		{
			$t_la = self::fetch(aw_global_get("ct_lang_id"));
			aw_global_set("charset", $t_la["charset"]);
		}
		else
		{
			aw_global_set("charset", $la["charset"]);
		}

		// oh yeah, we should only overwrite admin_lang_lc if it is not set already!
		aw_global_set("admin_lang_lc", $LC);

		aw_global_set("lang_oid", $la["oid"]);
		// and we should be all done. if after this, lang_id will still be not set I won't be able to write the
		// code that fixes it anyway.

		// also, if we are in the site, not admin
		// set the ui language to the active language
		if (!is_admin())
		{
			// read the language from active lang
			if (!empty($GLOBALS["cfg"]["user_interface"]["use_site_lang"]))
			{
				if (aw_ini_get("user_interface.full_content_trans"))
				{
					$_tmp = aw_global_get("ct_lang_lc");
				}
				else
				{
					$_tmp = aw_global_get("LC");
				}

				aw_ini_set("user_interface.default_language", $_tmp);
			}
		}

		// define global constants
		//TODO: aw globalsis pole neid vaja, sealt v2lja, kasutada ainult konstante
		define("AW_LANGUAGES_DEFAULT_UI_LID", aw_global_get("lang_id"));
		define("AW_LANGUAGES_DEFAULT_UI_LC", aw_global_get("LC"));
		define("AW_LANGUAGES_DEFAULT_CT_LID", aw_global_get("lang_id"));
		define("AW_LANGUAGES_DEFAULT_CT_LC", aw_global_get("LC"));
	}

	static function on_site_init($dbi, $site, &$ini_opts, &$log)
	{
		// no need to add languages if we are to use an existing database
		if (!$site['site_obj']['use_existing_database'])
		{
			foreach(aw_ini_get("languages.list") as $lid => $ldat)
			{
				$status = 1;
				if ($lid == 1)
				{
					$status = 2;
				}
				$dbi->db_query("INSERT INTO languages(id, name, charset, status, acceptlang, modified, modifiedby) values('$lid','$ldat[name]','$ldat[charset]',$status,'$ldat[acceptlang]','".time()."','".$site['site_obj']['default_user']."')");//FIXME
				$log->add_line(array(//FIXME
					"uid" => aw_global_get("uid"),
					"msg" => t("Lisas keele"),
					"comment" => $ldat["name"],
					"result" => "OK"
				));
			}
		}
	}

	/**
		@attrib name=get_trans_lc
	**/
	function get_trans_lc($arr)
	{
		die($_SESSION["user_adm_ui_lc"] != "" ? $_SESSION["user_adm_ui_lc"] : "et");
	}

	/** Returns AutomatWeb numeric code for ISO 639-3 three-letter language code
		@attrib api=1 params=pos
		@param code type=string
		@comment
		@returns int
			Three to six digit encoded language code
		@errors
	**/
	public static function get_aw_lid($code)
	{
		$code = strtolower($code);
		$alphabet = "abcdefghijklmnopqrstuvwxyz";
		return (int) strpos($alphabet, $code{0}) + 1 . strpos($alphabet, $code{1}) + 1 . strpos($alphabet, $code{2}) + 1;
	}

	/** Returns language cookie/session variable name
		@attrib api=1 params=pos
		@returns string
		@errors none
	**/
	public static function get_cookie_name()
	{
		$site_id = aw_ini_get("site_id");
		return "1" === $site_id ? "lang_id" : "lang_id_{$site_id}";
	}
}

/** Generic language module exception **/
class awex_lang extends aw_exception {}

/** Language or language data not available **/
class awex_lang_na extends awex_lang {}
