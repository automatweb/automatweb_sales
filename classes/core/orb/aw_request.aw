<?php

//TODO: need peab defineerima ka siis kui ei kasutata autoloadi!!!
// selleks tuleb _autoload_language viia lang() meetodisse
//_autoload_language jagada kaheks -- requesti parsimine ja automatwebi setup vastavalt saadud keelele, viimane viia automatweb.aw-sse vms
//!!!!!
//TODO:
// datasource ja keelte laadimine automatweb::set_request-i vms
// siin laetakse request, seal t6lgendatakse seda...


/**
defines global constants

AW_REQUEST_UI_LANG_ID - active user interface language AW id
AW_REQUEST_UI_LANG_CODE - active user interface language ISO_639-3 3-letter code

AW_REQUEST_CT_LANG_ID - active content language AW id
AW_REQUEST_CT_LANG_CODE - active content language ISO_639-3 3-letter code

**/
class aw_request
{
	// const DEFAULT_CLASS = "admin_if";
	// const DEFAULT_ACTION = "change";

	private $_args = array(); // Request parameters. Associative array of argument name/value pairs. read-only
	private $_class = ""; // requested class. aw_class.class_name
	private $_class_id = 0; // requested class id. aw_class.class_id
	private $_action = ""; // requested class action. one of aw_class.actions
	private $_is_fastcall = false; // boolean
	private $_application; // object
	private static $_application_classes = array( //!!! tmp. teha n2iteks interface-ga. implements application
		"crm_sales",
		"crm_company",
		"realestate_manager",
		"mrp_workspace",
		"admin_if",//!!! esialgu tekitab tyli, vaja m6elda kuidas mitme aknaga s2ilitada aplikatsiooni. variandid window id, klassi aplikatsioonide nimekirjad ja kui mitu siis kysitakse kasutajalt
		// "aw_object_search",///!!!! vt admin_if
		"bug_tracker",
		"events_manager"
	);
	private $_protocol; // protocol object
	private $_lang_id = 0;

	public function __construct($autoload = false)
	{
		if ($autoload)
		{
			// load current/active request
			$this->parse_args();
			$this->_autoload_data_storage();
			$this->_autoload_language();
		}
	}

	/** Determines request type, arguments and loads them returning the specific request object
	@attrib api=1 params=pos
	@returns aw_request object
	**/
	public static function autoload()
	{
		// determine request type and create instance
		if (!empty($_SERVER["SERVER_PROTOCOL"]) and substr_count(strtolower($_SERVER["SERVER_PROTOCOL"]), "http") > 0)
		{ //!!! check if SERVER_PROTOCOL always set and 'http' when http request. A rumoured case that empty when https on some specific server/machine.
			$request = new aw_http_request(true);
		}
		else
		{
			$request = new aw_request(true);
		}

		return $request;
	}

	protected function _autoload_data_storage()
	{//TODO: viia automatweb.aw-sse set_request meetodisse!!!
		// initiate data source manager
		if (!aw_global_get("no_db_connection"))
		{
			$GLOBALS["object_loader"] = object_loader::instance();
		}
	}

	// - loads both content and ui active language variables from cookie and session
	// - finds best language if not requested or found in cookie or session in any way and set it active
	protected function _autoload_language()
	{
		// determine and set up content language
		// look if set_lang_id request made then if active is defined and finally find best if not
		if ($ct_lid = languages::get_active_ct_lang_id())
		{
			aw_global_set("lang_id", $ct_lid);
		}

		if ($set_lang_id = (int) automatweb::$request->arg("set_lang_id"))
		{
			/// if language has not changed, don't waste time re-setting it
			if ($set_lang_id !== $ct_lid)
			{
				/// if we explicitly request language change, we get that, except if the language is not active
				/// and we are not logged in
				if (!$ct_lid)
				{
					///TODO: startup error
				}
			}
		}

		/// if at this point no language is active, then we must select one
		if (!$ct_lid)
		{
			////
			// try to figure out the balance between the user's language preferences and the
			// languages that are available.
			// places checked:
			// 1 ini languages.default setting
			// 2 request (browser acceptlang etc.)
			// 3 first active language found
			// 4 any defined language
			// 5 a hard coded default
			{
				// try ini
				$ct_lid = languages::lc2lid(aw_ini_get("languages.default"));

				// try request
				if (!$ct_lid)
				{
					$ct_lid = self::lang_id();
				}

				// try to find an active language
				// if no languages are active, then get the first one.
				if (!$ct_lid)
				{
					$languages = languages::listall();
					if (count($languages))
					{
						foreach($languages as $l)
						{
							if ($l["status"] == object::STAT_ACTIVE && object_loader::can("", $l["oid"]))
							{
								$ct_lid = $l["aw_lid"];
								break;
							}
						}

						if (!$ct_lid)
						{
							$l = reset($languages);
							$ct_lid = $l["aw_lid"];
						}
					}
				}

				// if there are no languages defined in the site, we are fucked anyway, so just return a reasonable number
				if (!$ct_lid)
				{
					$ct_lid = languages::LC_EST;
				}
			}

			if (!$ct_lid)
			{
				///TODO: startup error
			}
		}

		$ct_lc = languages::lid2lc($ct_lid);
		$this->set_lang_id($ct_lid);
		// content language determined


		// determine and set up user interface language
		// default to user_interface.default_language ini setting or content language if all else fails
		$ui_lid = languages::get_active_ui_lang_id();
		if (!$ui_lc = languages::lid2lc($ui_lid))
		{
			$ui_lc = aw_ini_get("user_interface.default_language");
			$ui_lid = languages::lc2lid($ui_lc);

			if (!$ui_lid)
			{
				$ui_lc = $ct_lc;
				$ui_lid = $ct_lid;
			}
		}
		// ui language determined


//////////////////////////// milleks need on?
//
$la = languages::fetch($ct_lid);
if (!aw_global_get("ct_lang_id") && aw_ini_get("user_interface.full_content_trans") && ($ct_lc1 = aw_ini_get("user_interface.default_language")))
{
	if (!empty($_COOKIE["ct_lang_id"]))
	{
		$ct_id = $_COOKIE["ct_lang_id"];
		$ct_lc1 = $_COOKIE["ct_lang_lc"];
	}
	else
	{
		$ct_id = languages::get_id_for_code($ct_lc1);
	}

	aw_session::set("ct_lang_lc", $ct_lc1);
	aw_session::set("ct_lang_id", $ct_id);
	aw_global_set("ct_lang_lc", $ct_lc1);
	aw_global_set("ct_lang_id", $ct_id);
}
////////////////////////////


//TODO: get rid of!
$LC = $la["acceptlang"]; if ($LC == "") { $LC = "et"; } aw_global_set("LC", $LC);
aw_global_set("admin_lang_lc", $LC);
aw_global_set("lang_oid", $la["oid"]);
//////////////////////////////



		// putenv('LC_ALL=de_DE');
		// setlocale(LC_ALL, 'de_DE');
		aw_global_set("lang_id", $ct_lid);

		// define global constants
		define("AW_REQUEST_UI_LANG_ID", $ui_lid);
		define("AW_REQUEST_UI_LANG_CODE", $ui_lc);
		define("AW_REQUEST_CT_LANG_ID", $ct_lid);
		define("AW_REQUEST_CT_LANG_CODE", $ct_lc);
	}

	/**
	@attrib api=1 params=pos
	@param name required type=string
		Argument name to get value for.
	@returns var
		Request argument value or NULL if argument not defined
	**/
	public function arg($name)
	{
		if (isset($this->_args[$name]))
		{
			return $this->_args[$name];
		}
		else
		{
			return null;
		}
	}

	/**
	@attrib api=1 params=pos
	@param name required type=string
		Argument name to find.
	@returns bool
	**/
	public function arg_isset($name)
	{
		return isset($this->_args[$name]);
	}

	/**
	@attrib api=1 params=pos
	@returns array
		Request arguments. argument_name => argument_value pairs
	**/
	public function get_args()
	{
		return $this->_args;
	}

	/**
	@attrib api=1 params=pos
	@param arg required type=string,array
		Arguments to set. String argument name or associative array of argument name/value pairs or array of argument names to set value for.
	@param val optional type=mixed
		Argument value. If $arg is array, then it is handled as array of argument names and this value will be set for all of those. Required in that case and when $arg is argument name.
	@returns void
	@comment
		Sets argument values.
	@throws
		awex_request_param when $arg parameter is not valid.
		awex_request_param with code 2 when $arg parameter is string argument name and no second argument given.
	**/
	public function set_arg($arg)
	{
		$sa = (2 === func_num_args());

		if (is_array($arg) and count($arg))
		{
			if ($sa)
			{
				$args = array();
				$val = func_get_arg(1);
				foreach ($arg as $name)
				{
					if (!is_string($name) or !strlen($name))
					{
						throw new awex_request_param("Invalid parameter value '" . var_export($name, true) . "'. Argument name expected.");
					}

					$args[$name] = $val;
				}

				$this->_args = $args + $this->_args;
			}
			else
			{
				$this->_args = $arg + $this->_args;
			}
		}
		elseif (is_string($arg) and strlen($arg))
		{
			if ($sa)
			{
				$this->_args[$arg] = func_get_arg(1);
			}
			else
			{
				throw new awex_request_param("No value specified to set '{$arg}'.", 2);
			}
		}
		else
		{
			throw new awex_request_param("Invalid parameter value '" . var_export($arg, true) . "'. Array or argument name expected.");
		}

		$this->parse_args();
	}

	/**
	@attrib api=1 params=pos
	@param name optional type=string,array
		Name(s) of request argument(s) to unset.
	@returns array
		Argument names that weren't set in the first place.
	@comment
		Unsets request argument(s). If no arguments given, unsets all request arguments.
	**/
	public function unset_arg()
	{
		$not_found_args = array();
		if (func_num_args())
		{
			$name = func_get_arg(0);

			if (is_array($name))
			{
				foreach ($name as $arg)
				{
					if (isset($this->_args[$arg]))
					{
						unset($this->_args[$arg]);
					}
					else
					{
						$not_found_args[] = $arg;
					}
				}
			}
			else
			{
				if (!isset($this->_args[$name]))
				{
					unset($this->_args[$name]);
				}
				else
				{
					$not_found_args[] = $name;
				}
			}
		}
		else
		{
			$this->_args = array();
		}

		$this->parse_args();
	}

	/**
	@attrib api=1 params=pos
	@returns object
		Currently active application object
	**/
	public function get_application()
	{
		if (!is_object($this->_application))
		{
			if (in_array($this->_class, self::$_application_classes)) //!!! tmp solution
			{
				if (isset($this->_args["id"]) and is_oid($this->_args["id"]))
				{
					try
					{
						$application = new object($this->_args["id"]);
						aw_session_set("aw_request_application_object_oid", $application->id());
					}
					catch (Exception $e)
					{
						$application = new object(); //!!! mis on default?
					}
				}
				elseif ("admin_if" === $this->_class)
				{
					$core = new core();
					$id = admin_if::find_admin_if_id();
					$application = new object($id);
					aw_session_set("aw_request_application_object_oid", $application->id());
				}
				elseif (aw_ini_isset("class_lut." . $this->_class))
				{
					$clid = aw_ini_get("class_lut." . $this->_class);
					$application = obj(null, array(), $clid);
				}
				else
				{
					$application = new object(); //!!! mis on default?
				}
			}
			elseif (is_oid(aw_global_get("aw_request_application_object_oid")))
			{
				try
				{
					$application = new object(aw_global_get("aw_request_application_object_oid"));
				}
				catch (Exception $e)
				{
					$application = new object(); //!!! mis on default?
				}
			}
			else
			{
				$application = new object(); //!!! mis on default?
			}

			$this->_application = $application;
		}

		return $this->_application;
	}

	/**
	@attrib api=1 params=pos
	@returns boolean
	**/
	public function is_fastcall()
	{
		return $this->_is_fastcall;
	}

	/** Current request protocol
	@attrib api=1 params=pos
	@returns object
	**/
	public function protocol()
	{
		return $this->_protocol;
	}

	public function type() // DEPRECATED
	{ return get_class($this) === "aw_http_request" ? "http" : "";	}

	/**
	@attrib api=1 params=pos
	@returns string
		Requested class name
	**/
	public function class_name()
	{
		return $this->_class;
	}

	/**
	@attrib api=1 params=pos
	@comment
		Class id is set only if requested class is a registered automatweb class. Otherwise this returns 0.
	@returns int
		Requested class id
	**/
	public function class_id()
	{
		return $this->_class_id;
	}

	/**
	@attrib api=1 params=pos
	@returns string
		Requested class action/public method name
	**/
	public function action()
	{
		return $this->_action;
	}

	/**
	@attrib api=1 params=pos
	@returns string
		Requested language aw id
	**/
	public function lang_id()
	{
		return $this->_lang_id;
	}

	/** Sets content language id
	@attrib api=1 params=pos
	@param lid type=string
		Language aw id
	@returns void
	@errors
		throws awex_param if invalid language id given
	**/
	public function set_lang_id($lid)
	{
		if (!languages::lid2lc($lid))
		{
			throw new awex_param("Invalid language id {$lid}");
		}
		$this->_lang_id = (int) $lid;
	}

	private function _set_args($args)
	{
		if (!is_array($args))
		{
			throw new awex_request_param("Invalid type. Arguments are an array");
		}

		$this->_args = $args;
	}

	private function _set_protocol($protocol)
	{
		if (!is_object($protocol))
		{
			throw new awex_request_param("Invalid argument. Protocol must be an object");
		}

		if (!in_array("protocol_interface", class_implements($protocol)))
		{
			throw new awex_request_param("Invalid argument. Protocol object must have protocol interface");
		}

		$this->_protocol = $protocol;
	}

	public function parse_args()
	{ //!!! "restore previous application" on vaja ka teaostada, sest n2iteks kui k2iakse teises applicationis ja minnakse tagasi eelmisest avatud allobjektile, on application vale
		if (!empty($this->_args["fastcall"]))
		{
			$this->_is_fastcall = true;
			aw_global_set("no_db_connection", 1);
		}
		// no name validation because requests can be formed and sent to other servers where different classes, methods, etc. defined
		// $this->_class = empty($this->_args["class"]) ? self::DEFAULT_CLASS : $this->_args["class"];
		// $this->_action = empty($this->_args["action"]) ? self::DEFAULT_ACTION : $this->_args["action"];
		$this->_action = empty($this->_args["action"]) ? "" : $this->_args["action"];

		if (!empty($this->_args["class"]))
		{
			$this->_class = $this->_args["class"];
		}

		if (aw_ini_isset("class_lut.{$this->_class}"))
		{
			$this->_class_id = aw_ini_get("class_lut.{$this->_class}");
		}
	}

	public function __isset($name)
	{
		$name = "_{$name}";
		return isset($this->$name);
	}

	public function __get($name)
	{
		$name = "_{$name}";
		return $this->$name;
	}

	public function __set($name, $value)
	{
		$setter_name = "_set_{$name}";
		if (method_exists($this, $setter_name))
		{
			$this->$setter_name($value);
		}
	}

	public function __unset($name)
	{
		$unsetter_name = "_unset_{$name}";
		if (method_exists($this, $unsetter_name))
		{
			$this->$unsetter_name();
		}
	}

	private function get_client_ip()
	{
// more headers to check: HTTP_PRAGMA, HTTP_XONNECTION, HTTP_CACHE_INFO, HTTP_XPROXY, HTTP_PROXY, HTTP_PROXY_CONNECTION, HTTP_CLIENT_IP, HTTP_VIA, HTTP_X_COMING_FROM, HTTP_X_FORWARDED_FOR, HTTP_X_FORWARDED, HTTP_COMING_FROM, HTTP_FORWARDED_FOR, HTTP_FORWARDED, ZHTTP_CACHE_CONTROL

/*
function getRealIP($fakeip=false)
{
	$ip = (!empty($_SERVER["HTTP_CLIENT_IP"])) ? (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) ? $_SERVER["HTTP_CLIENT_IP"] : preg_replace(‘/(?:,.*)/’, ", $_SERVER["HTTP_X_FORWARDED_FOR"]):$_SERVER["REMOTE_ADDR"];
	$ip = (!$fakeip) ? $ip:$fakeip;

	// local check class b and c
	$patterns = array(
		"/(192).(168).(\d+).(\d+)/i",
		"/(10).(\d+).(\d+).(\d+)/i"
	);
	foreach($patterns as $pattern)
	{
		if(preg_match($pattern,$ip))
		{
			return "VPN";
		}
	}

	// local check class a
	$parts = explode(".",$ip);
	if($parts[0]==172 && ($parts[1]>15 || $parts[1]<32))
	{
		return "VPN";
	}

	return trim($ip);
}
*/

		if (!empty($_SERVER["HTTP_CLIENT_IP"]))
		{
			$ip = $_SERVER["HTTP_CLIENT_IP"];
		}
		elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
		{
			$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}
		elseif (!empty($_SERVER["REMOTE_ADDR"]))
		{
			$ip = $_SERVER["REMOTE_ADDR"];
		}
		else
		{
			$ip = "";
		}
		return $ip;
	}
}

/** Generic aw_request class unexpected condition indicator **/
class awex_request extends aw_exception {}

/** Requested entity not available **/
class awex_request_na extends awex_request {}

/** Invalid parameter **/
class awex_request_param extends awex_request {}
