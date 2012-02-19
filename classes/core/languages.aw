<?php

/**
languages core module class
manages information about languages defined in system
manages languages related system state

**/

class languages extends aw_core_module implements orb_public_interface
{
	const USER_CHARSET = "UTF-8"; // charset for all user data in automatweb
	const CODE_CHARSET = "us-ascii"; // charset for automatweb program code files

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
	private static $constructed = false;
	private static $languages_data = array();
	private static $languages_metadata = array(
		"enabled_languages_count" => 0
	);
	private static $_cache_key = "languages-cache-site_id-"; // internal cache file name/key

	private static $acceptlang2lid_lut = array(
		"et" => self::LC_EST,
		"en" => self::LC_ENG,
		"es" => self::LC_SPA,
		"de" => self::LC_DEU,
		"fi" => self::LC_FIN,
		"fr" => self::LC_FRA,
		"lt" => self::LC_LIT,
		"lv" => self::LC_LAV,
		"ru" => self::LC_RUS,
		"sv" => self::LC_SWE
	);

	private static $lid2acceptlang_lut = array(
		self::LC_EST => "et",
		self::LC_ENG => "en",
		self::LC_DEU => "de",
		self::LC_SPA => "es",
		self::LC_FIN => "fi",
		self::LC_FRA => "fr",
		self::LC_LIT => "lt",
		self::LC_LAV => "lv",
		self::LC_RUS => "ru",
		self::LC_SWE => "sv"
	);

	private static $lid2lc_lut = array(
		self::LC_EST => "est",
		self::LC_ENG => "eng",
		self::LC_DEU => "deu",
		self::LC_SPA => "spa",
		self::LC_FIN => "fin",
		self::LC_FRA => "fr",
		self::LC_LIT => "lit",
		self::LC_LAV => "lav",
		self::LC_RUS => "rus",
		self::LC_SWE => "swe"
	);

	private static $lc2lid_lut = array(
		"est" => self::LC_EST,
		"eng" => self::LC_ENG,
		"deu" => self::LC_DEU,
		"spa" => self::LC_SPA,
		"fin" => self::LC_FIN,
		"fra" => self::LC_FRA,
		"lit" => self::LC_LIT,
		"lav" => self::LC_LAV,
		"rus" => self::LC_RUS,
		"swe" => self::LC_SWE
	);

	private static $lid2locale_lut = array(
		self::LC_EST => array("et_EE", "et"),
		self::LC_ENG => "en",
		self::LC_DEU => array("de_DE@euro", "de_DE", "de", "ge"),
		self::LC_SPA => "es",
		self::LC_FIN => "fi",
		self::LC_FRA => "fr",
		self::LC_LIT => "lt",
		self::LC_LAV => "lv",
		self::LC_RUS => "ru",
		self::LC_SWE => "sv"
	);

	public static function construct()
	{
		if (!self::$constructed)
		{
			self::init_cache();
			self::$constructed = true;
		}
	}


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

	/** Sets active content language and redirects if requested.
		@attrib name=set_active nologin=1
		@param id required type=int
			Language id to set
		@param return_url optional type=string
			URL to redirect back to
		@comment
			Public interface to set_active api method
			Redirects to baseurl if given return url is invalid

		@returns void
	**/
	public static function set_active($arr)
	{
		$r = self::set_active_ct_lang($arr["id"]);

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

	/**
		@attrib api=1 params=pos
		@param lid type=int
			AW language id
		@param disregard_status type=bool default=FALSE
			Set language active even if it isn't active
		@comment
		@returns
		@errors
	**/
	public static function set_active_ct_lang($lid, $disregard_status = false)
	{
		$lid = (int) $lid;

		if (!self::is_valid($lid))
		{
			return 0;
		}

		$l = self::fetch($lid);
		if ($l["status"] != object::STAT_ACTIVE && !$disregard_status)
		{
			return 0;
		}

		if (!object_loader::can("", $l["oid"]))
		{
			return 0;
		}

		// save selected language to session and cookie
		$cookie_name = self::get_ct_cookie_name();
		aw_session::set($cookie_name, $lid);
		aw_cookie::set($cookie_name, $lid, aw_ini_get("languages.ct_cookie_lifetime"));

		return $lid;
	}

	/**
		@attrib api=1 params=pos
		@param lid type=int
			AW language id
		@param disregard_status type=bool default=FALSE
			Set language active even if it isn't active
		@comment
		@returns
		@errors
	**/
	public static function set_active_ui_lang($lid, $disregard_status = false)
	{
		$lid = (int) $lid;

		if (!self::is_valid($lid))
		{
			return 0;
		}

		$l = self::fetch($lid);
		if ($l["status"] != object::STAT_ACTIVE && !$disregard_status)
		{
			return 0;
		}

		if (!object_loader::can("", $l["oid"]))
		{
			return 0;
		}

		// save selected language to session and cookie
		$cookie_name = self::get_ui_cookie_name();
		aw_session::set($cookie_name, $lid);
		aw_cookie::set($cookie_name, $lid, aw_ini_get("languages.ui_cookie_lifetime"));

		return $lid;
	}

	public static function get_active() { return self::get_active_ui_lang_id(); }

	/** Return currently active user interface AW language id
		@attrib api=1 params=pos
		@comment
		@returns int
		@errors none
	**/
	public static function get_active_ui_lang_id()
	{
		defined("AW_REQUEST_UI_LANG_ID") and $lang_id = AW_REQUEST_UI_LANG_ID
		or $lang_id = aw_session::get(self::get_ui_cookie_name())
		or $lang_id = aw_cookie::get(self::get_ui_cookie_name())
		;

		if (!self::is_valid($lang_id))
		{
			$lang_id = 0;
		}

		return (int) $lang_id;
	}

	/** Return currently active content AW language id
		@attrib api=1 params=pos
		@comment
		@returns int
		@errors none
	**/
	public static function get_active_ct_lang_id()
	{
		defined("AW_REQUEST_CT_LANG_ID") and $lang_id = AW_REQUEST_CT_LANG_ID
		or $lang_id = aw_global_get("lang_id")
		or $lang_id = aw_session::get(self::get_ct_cookie_name())
		or $lang_id = aw_cookie::get(self::get_ct_cookie_name())
		;

		if (!self::is_valid($lang_id))
		{
			$lang_id = 0;
		}

		return (int) $lang_id;
	}

	//DEPRECATED. utf-8 is now default and only charset used
	public static function get_charset($id = AW_REQUEST_CT_LANG_ID) { return languages::USER_CHARSET; }

	/** Checks if language id is a valid AW lid
		@attrib api=1 params=pos
		@param lid type=int
			AW language id.
		@returns bool
		@errors none
	**/
	public static function is_valid($lid)
	{
		return isset(self::$lid2lc_lut[$lid]);
	}

	/**
		@attrib api=1 params=pos
		@param lid type=int
			AW language id.
		@returns string
			ISO_639-3 language code for aw language id. Empty string if id not found
		@errors none
	**/
	public static function lid2lc($lid)
	{
		return isset(self::$lid2lc_lut[$lid]) ? self::$lid2lc_lut[$lid] : "";
	}

	/**
		@attrib api=1 params=pos
		@param lid type=int
			AW language id.
		@param lang_id type=int default=AW_REQUEST_UI_LANG_ID
			Language in which to get the name
		@returns string
			Language name. Empty string if id not found. Default value (most likely in Estonian) if translation not found
		@errors none
	**/
	public static function lid2name($lid, $lang_id = AW_REQUEST_UI_LANG_ID)
	{
		try
		{
			$language = language_obj::get_by_lid($lid);
			$name = $language->trans_get_val("name", $lang_id);
		}
		catch (Exception $e)
		{
			$name = aw_ini_isset("languages.list.{$lid}") ? aw_ini_get("languages.list.{$lid}.name") : "";
		}

		return $name;
	}

	/**
		@attrib api=1 params=pos
		@param lc type=int
			ISO_639-3 language code
		@returns int
			AW language id for ISO_639-3 language code. 0 if language code not found
		@errors none
	**/
	public static function lc2lid($lc)
	{
		return isset(self::$lc2lid_lut[$lc]) ? self::$lc2lid_lut[$lc] : 0;
	}

	/**
		@attrib api=1 params=pos
		@param lid type=int
			Language id
		@returns string
			Default two letter acceptlang language code for language id. Empty string if id not found
		@errors none
	**/
	public static function lid2acceptlang($lid)
	{
		return isset(self::$lid2acceptlang_lut[$lid]) ? self::$lid2acceptlang_lut[$lid] : "";
	}

	/**
		@attrib api=1 params=pos
		@param acceptlang type=string
			Accept-lang language string code
		@returns int
			AW language id for language code. 0 if id not found
		@errors none
	**/
	public static function acceptlang2lid($acceptlang)
	{
		return isset(self::$lid2acceptlang_lut[$acceptlang]) ? self::$lid2acceptlang_lut[$acceptlang] : "";
	}

	/**
		@attrib api=1 params=pos
		@param lid type=int
			Language id
		@returns string
			Two letter acceptlang language code that is defined in database or default if not. Empty string if id not found
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

	//DEPRECATED. use get_code_for_id() or lid2acceptlang()
	public static function get_langid($id = -1)
	{ trigger_error("get_langid() is deprecated. use get_code_for_id() or lid2acceptlang(). Called from " . get_caller_str(), E_USER_DEPRECATED); if ($id == -1)  { $id = aw_global_get("lang_id"); }$a = self::fetch($id); return $a["acceptlang"]; }

	/** Finds the aw language id for an acceptlang language code (en, et, ...)
		@attrib api=1 params=pos
		@param lc required type=string
			The code to find the language id for
		@returns int
			0 if no language for the code is defined in the system, language id (not language object id)
		@errors none
	**/
	public static function get_id_for_code($lc)
	{
		return isset(self::$acceptlang2lid_lut[$lc]) ? self::$acceptlang2lid_lut[$lc] : 0;
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
			$cf_name = self::$_cache_key . aw_ini_get("site_id");
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
				while ($row = object_loader::ds()->db_next() and isset($row["aw_lid"]))
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
				$dbi->db_query("INSERT INTO languages(id, name, status, acceptlang, modified, modifiedby) values('$lid','$ldat[name]',$status,'$ldat[acceptlang]','".time()."','".$site['site_obj']['default_user']."')");//FIXME
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

	/** Returns AutomatWeb numeric code for ISO_639-3 three-letter language code
		@attrib api=1 params=pos
		@param code type=string
		@comment
		@returns int
			Three to six digit encoded language code
		@errors
	**/
	public static final function get_aw_lid($code)
	{
		$code = strtolower($code);
		$alphabet = "abcdefghijklmnopqrstuvwxyz";
		return (int) strpos($alphabet, $code{0}) + 1 . strpos($alphabet, $code{1}) + 1 . strpos($alphabet, $code{2}) + 1;
	}

	/** Returns content language cookie/session variable name
		@attrib api=1 params=pos
		@returns string
		@errors none
	**/
	public static function get_ct_cookie_name()
	{
		$site_id = aw_ini_get("site_id");
		return "1" === $site_id ? "__aw-languages_ct_lid" : "__aw-languages_site-{$site_id}_ct_lid";
	}

	/** Returns user interface language cookie/session variable name
		@attrib api=1 params=pos
		@returns string
		@errors none
	**/
	public static function get_ui_cookie_name()
	{
		$site_id = aw_ini_get("site_id");
		return "1" === $site_id ? "__aw-languages_ui_lid" : "__aw-languages_site-{$site_id}_ui_lid";
	}
}

/** Generic language module exception **/
class awex_lang extends aw_exception {}

/** Language or language data not available **/
class awex_lang_na extends awex_lang {}

