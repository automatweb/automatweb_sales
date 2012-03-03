<?php

// get aw directory and file extension
$__FILE__ = __FILE__;//!!! to check if works with zend encoder (__FILE__)
$aw_dir = str_replace(DIRECTORY_SEPARATOR, "/", dirname($__FILE__)) . "/"; // replace to have it work on windows
$aw_dir = str_replace(DIRECTORY_SEPARATOR, "/", $aw_dir);
define("AW_DIR", $aw_dir);
define("AW_FILE_EXT", substr($__FILE__, strrpos($__FILE__, "automatweb") + 10)); // extension can't be 'automatweb'

// required for Zend Framework classes autoloading
set_include_path(implode(PATH_SEPARATOR, array(
	AW_DIR . "addons/",
	get_include_path()
)));

// include required 'kernel' libraries
require_once(AW_DIR . "lib/main" . AW_FILE_EXT);

// set required configuration
set_error_handler (array("aw_errorhandler", "handle_error"));
ini_set("track_errors", "1");
$success = mb_internal_encoding(languages::USER_CHARSET);

if (!$success)
{
	throw new aw_exception("Failed to set default character encoding for multibyte module");
}

/*
//TODO: cli exec option
// service request if script used in executable mode
if (!empty($argv[1]))
{
	$request = aw_request::autoload();
	automatweb::start();
	automatweb::$instance->bc();
	automatweb::$instance->set_request($request);
	automatweb::$instance->exec();
	automatweb::$result->send();
	automatweb::shutdown();
}
*/

class automatweb
{
	const MODE_DBG = 2;
	const MODE_PRODUCTION = 4;
	const MODE_REASONABLE = 8;
	const MODE_DBG_EXTENDED = 16;
	const MODE_DBG_CONSOLE = 32;

	private $mode = 0; // current mode
	private $request_loaded = false; // whether request is loaded or only empty initialized
	private $start_time; // float unix timestamp + micro when current aw server instance was started
	private static $instance_data = array(); // aw instance stack
	private static $current_instance_nr = 0;
	private static $default_cfg_loaded = false;

	public $bc = false; // If true, execute through ..._impl classes and other older code. Default false. read-only
	public static $request; // aw_request object of current aw instance. read-only.
	public static $instance; // current aw instance. read-only.
	public static $result; // aw_resource object. result of executing the request

	private function __construct($mode_id = self::MODE_PRODUCTION)
	{
		// initialize object lifetime
		$this->start_time = microtime(true);
		$this->mode($mode_id);
	}

	/** Shortcut method for running a typical http www request
	@attrib api=1 params=pos
	@param cfg_file required type=string
		Configuration file absolute path. It is expected to be in an automatweb site directory! I.e. a 'pagecache' directory must be found in that same directory.
	@returns void
	@comment
		A common web request execution script. Creates a server instance, autoloads request. Ends php script when done.
	@errors
		Displays critical errors in output. If cfg_file not found, or when a fatal server error occurred.
	**/
	public static function run_simple_web_request_bc($cfg_file)
	{
		if (!is_readable($cfg_file))
		{
			exit("Configuration file not readable.");
		}

		try
		{
			self::start();
		}
		catch (Exception $e)
		{
			try
			{
				self::shutdown();
			}
			catch (Exception $e)
			{
			}

			if (!headers_sent())
			{
				header("HTTP/1.1 500 Server Error");
			}

			echo "Server Error";
		}

		self::$instance->bc();
		$cfg_cache_file = dirname($cfg_file) .  "/pagecache/ini.cache";
		self::$instance->load_config_files(array($cfg_file), $cfg_cache_file);
		$request = aw_request::autoload();
		self::$instance->set_request($request);
		self::$instance->exec();
		self::http_exit();
	}

	/**
	@attrib api=1 params=pos
	@returns void
	@comment
		Starts a new Automatweb application server instance.
	@errors
		Throws aw_exception if Automatweb already running.
	**/
	public static function start($mode_id = self::MODE_PRODUCTION)
	{
		// load default cfg
		if (self::$current_instance_nr)
		{ // store previous configuration
			self::$instance_data[self::$current_instance_nr]["cfg"] = $GLOBALS["cfg"];
		}

		if (!self::$default_cfg_loaded)
		{
			// load default configuration
			load_config(array(AW_DIR . "aw.ini"), AW_DIR . "files/ini.cache.aw");
			self::$default_cfg_loaded = true;
		}

		// start aw
		++self::$current_instance_nr;
		$aw = new automatweb($mode_id);
		_aw_global_init();//TODO: viia aw instantsi sisse ___aw_globals
		aw_cache::setup();//TODO: make aw thread safe

		if (!session_id())
		{
			self::start_session();
		}

		$request = new aw_request(); // autoload can't be used here!
		$result = new aw_resource();
		self::$instance_data[self::$current_instance_nr] = array(
			"instance" => $aw,
			"request" => $request,
			"result" => $result
		);

		self::$instance = $aw;
		self::$request = $request;
		self::$result = $result;

		// For quick debugging -kaarel 14.05.2009
		// Hannes will add this to his AW Mozilla add-on, so debugging will be SO much easier!
		if(!empty($_COOKIE["manual_automatweb_mode"]))
		{
			$mode_id = constant("self::MODE_".$_COOKIE["manual_automatweb_mode"]);
			if($mode_id !== NULL)
			{
				$aw->mode($mode_id);
			}
		}
	}

	private static function start_session()
	{
		if (
			isset($_SERVER["REQUEST_URI"]) and
			false !== strpos($_SERVER["REQUEST_URI"], "class=file") and
			false !== strpos($_SERVER["REQUEST_URI"], "action=preview") and
			isset($_SERVER['HTTP_USER_AGENT']) and
			strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE'))
		{//TODO: paremaks teha, mujale panna, ... . Vajalik, et https-iga IE faili allalaadimised t88taks
			session_cache_limiter("private");
		}

		ini_set("session.save_handler", "files");
		session_name("automatweb");
		session_start();

		if (is_array($_SESSION))
		{
			foreach($_SESSION as $k => $v)
			{
				aw_global_set($k,$v);
			}
		}
		aw_global_set("uid", isset($_SESSION["uid"]) ? $_SESSION["uid"] : "");
	}

	/**
	@attrib api=1 params=pos
	@returns void
	@comment
		Shuts down currently active Automatweb application server instance.
	@errors
		Throws aw_exception if Automatweb not running.
	**/
	public static function shutdown()
	{
		if(!count(self::$instance_data))
		{
			throw new aw_exception("Automatweb not started.");
		}

		// throw away current aw
		array_pop(self::$instance_data);
		--self::$current_instance_nr;

		if(!count(self::$instance_data))
		{ // clean up, restore defaults
			self::$instance = null;
			self::$request = null;
			self::$result = null;
			self::$default_cfg_loaded = false;
		}
		else
		{
			// restore previous aw
			$instance_data = end(self::$instance_data);
			$GLOBALS["cfg"] = $instance_data["cfg"];
			self::$instance = $instance_data["instance"];
			self::$request = $instance_data["request"];
			self::$result = $instance_data["result"];
			self::$instance->mode(self::$instance->mode);
		}
	}

	/** Outputs buffers, shuts down and stops scipt execution
		@attrib api=1 params=pos
		@param status type=int default=http::STATUS_OK
			One of http::STATUS_ constants
		@param message type=string default=""
			Optional status message.
		@comment
		@returns void
		@errors
	**/
	public static function http_exit($status = http::STATUS_OK, $message = "")
	{
		self::$result->set_status($status);
		self::$result->send();
		self::shutdown();
		exit($message);
	}


	/**
	@attrib api=1 params=pos
	@param request required type=aw_request
	@returns void
	@comment
		Sets current/active request in this aw instance.
	**/
	public function set_request(aw_request $request)
	{
		self::$request = $request;
		self::$instance_data[self::$current_instance_nr]["request"] = $request;
		$ct_lid = $request->lang_id();

		if (!$request->is_fastcall())
		{
			$ct_lid = languages::set_active_ct_lang($ct_lid);
		}

		if (!$ct_lid)
		{
			// since just about every trick in the book to try and find a
			// suitable lang_id is exhausted, just force it to be set active
			$ct_lid = languages::set_active_ct_lang($ct_lid, true);
		}

		$this->request_loaded = true;
	}

	/**
	@attrib api=1 params=pos
	@param files type=array
		Configuration files to load.
	@param cache_file type=string default=""
		Where to write cached version of loaded configuration.
	@returns void
	@comment
		Loads configuration from given files, merging it to default configuration.
	**/
	public function load_config_files(array $files, $cache_file = "")
	{
		$keys = array_keys($files, AW_DIR . "aw.ini", true);
		foreach ($keys as $i)
		{
			unset($files[$i]);
		}

		load_config($files, $cache_file);

		// configure settings with values from aw configuration
		date_default_timezone_set(aw_ini_get("date_default_tz"));

		// set mode by config
		$mode = "automatweb::MODE_" . aw_ini_get("config.mode");
		if (defined($mode))
		{
			$mode = constant($mode);
			automatweb::$instance->mode($mode);
		}

		// call core modules constructors
		//TODO: leida 6ige koht sellele
		languages::construct();
	}

	/**
	@attrib api=1 params=pos
	@returns void
	@comment
		Executes (current) request.
	**/
	public function exec()
	{
		if (!$this->request_loaded)
		{ // autoload request
			$request = aw_request::autoload();
			$this->set_request($request);
		}

		if (self::$request instanceof aw_http_request)
		{
			self::$result = new aw_http_response();
			self::$result->set_charset(languages::USER_CHARSET);
		}

		if ($this->bc)
		{ // old execution path. compatibility mode.
			return $this->exec_bc();
		}
		else
		{
			$class = self::$request->class_name();
			$method = self::$request->action();
			$o = new $class(); //!!! validate and pass params?
			$o->$method(); //!!! validate and pass params from request?
		}
	}

	private function exec_bc()
	{
		ignore_user_abort(true);
		ini_set("memory_limit", "512M");
		ini_set("max_execution_time", "2000");
		$request_uri = $_SERVER["REQUEST_URI"];

		if (aw_ini_get("menuedit.require_ssl") and "https" !== self::$request->get_uri()->get_scheme())
		{ // redirect to https
			$redirect_url = self::$request->get_uri();
			$redirect_url->set_scheme("https");
			aw_redirect($redirect_url);
		}

		// execute fastcall if requested
		if (self::$request->is_fastcall())
		{
			require_once(AW_DIR . "lib/fastcall_base" . AW_FILE_EXT);
			if (!class_exists("class_base"))
			{
				throw new aw_exception("Failed to load 'fastcall' module");
			}

			$vars = self::$request->get_args();
			$class = self::$request->class_name();
			$action = self::$request->action();
			$inst = new $class();
			self::$result->set_data($inst->$action($vars));
			exit();
		}
		elseif (strpos($request_uri, "/automatweb") === false)
		{
			aw_global_set("section", menu_obj::get_active_section_id());

			// can't use classload here, cause it will be included from within a function and then all kinds of nasty
			// scoping rules come into action. blech.
			$script = basename($_SERVER["SCRIPT_FILENAME"], AW_FILE_EXT);
			$path = aw_ini_get("classdir") . aw_ini_get("site_impl_dir") . $script . "_impl" . AW_FILE_EXT;
			if (file_exists($path))
			{
				self::$result->set_data(get_include_contents($path));
			}
		}
		else
		{
			aw_ini_set("in_admin", true);
			$vars = self::$request->get_args();

			if (isset($vars["class"]))
			{
				$GLOBALS["__START"] = microtime(true);

				// parse vars
				$class = self::$request->class_name();
				$action = self::$request->action();

				if (empty($class) && !empty($vars["alias"]))
				{
					$class = $vars["alias"];
				}

				include(AW_DIR . "automatweb/admin_header".AW_FILE_EXT);

				if (isset($_SESSION["auth_redir_post"]) && is_array($_SESSION["auth_redir_post"]))
				{
					$vars = $_SESSION["auth_redir_post"];
					$_POST = $_SESSION["auth_redir_post"];
					$class = $vars["class"];
					$action = $vars["action"];

					if (empty($class) && isset($vars["alias"]))
					{
						$class = $vars["alias"];
					}

					unset($_SESSION["auth_redir_post"]);
				}

				// actually, here we should find the program that get's executed somehow and do prog_acl for that.
				// but there seems to be no sure way to do that unfortunately.

				$orb = new orb();
				$orb->process_request(array(
					"class" => $class,
					"action" => $action,
					"vars" => $vars,
					"silent" => false
				));

				$content = $orb->get_data();

				// et kui orb_data on link, siis teeme ymbersuunamise
				// see ei ole muidugi parem lahendus. In fact, see pole yleyldse
				// mingi lahendus
				if ((substr($content,0,5) === "http:" || substr($content,0,6) === "https:" || (isset($vars["reforb"]) && ($vars["reforb"] == 1))) && empty($vars["no_redir"]))
				{
					aw_redirect(new aw_uri($content));
				}

				ob_start();
				include(AW_DIR . "automatweb/admin_footer" . AW_FILE_EXT);
				$footer_return = ob_get_clean();
				self::$result->set_data($str . $footer_return);
			}
			elseif (
				(!empty($_SERVER["PATH_TRANSLATED"]) and file_exists($_SERVER["PATH_TRANSLATED"])) or
				(!empty($_SERVER["SCRIPT_NAME"]) and file_exists(AW_DIR . $_SERVER["SCRIPT_NAME"]))
			)
			{
				$u = new users();
				$u->request_startup();

				if (!acl_base::prog_acl_auth("view", "PRG_MENUEDIT"))
				{
					self::http_exit(http::STATUS_UNAUTHORIZED);
				}
				else
				{
					// no class given but request is valid and legal
					// if user is logged in
					$uid = aw_global_get("uid");
					if ($uid)
					{
						// find user's/group's default redirect
						$users = new users_user();
						$user = obj(aw_global_get("uid_oid"));

						//TODO: TMP teha et oleks olemas see LC
						if (!aw_global_get("LC"))
						{
							aw_global_set("LC", "et");
						}
						// END TMP

						try
						{
							$redirect_url = new aw_uri($users->_find_post_login_url(array(), $uid, $user));

							if (!$redirect_url->arg_isset("class"))
							{
								throw new Exception("No class");
							}
						}
						catch (Exception $e)
						{
							// go to default admin interface
							include(AW_DIR . "automatweb/admin_header" . AW_FILE_EXT);
							$id = admin_if::find_admin_if_id();
							$redirect_url = aw_ini_get("baseurl") . "automatweb/orb.aw?class=admin_if&action=change&group=o&id={$id}";
						}
					}
					else
					{ // go to main page
						$redirect_url = aw_ini_get("baseurl") . "automatweb/";
					}

					aw_redirect(new aw_uri($redirect_url));
				}
			}
			else // a bad request. avoid background calls to admin_if when e.g. a non-existent ordinary file requested (css, images, etc.)
			{
			}
		}
	}

	/**
	@attrib api=1 params=pos
	@returns aw_resource
		Result aw_resource object
	**/
	public function get_result()
	{
		return $this->result;
	}

	/**
	@attrib api=1 params=pos
	@returns void
		Outputs result in the format, by the protocol and through the medium specified in current request.
	**/
	public function output_result()
	{
		$this->result->send();
	}

	public function set_result($value, $buffer = true, $append = false) // DEPRECATED
	{ if ($buffer) { if ($append){ $this->result->set_data($value); } else { $this->result->clear_data(); $this->result->set_data($value); } } elseif (is_string($value)) { echo $value; } }

	/**
	@attrib api=1 params=pos
	@param id optional type=integer
		Configuration mode id. One of automatweb::MODE_... constants.
	@returns void/integer
		Current mode id, if $id parameter not given.
	@comment
		Sets configuration mode or retrieves current mode id.
	**/
	public function mode($id = null)
	{
		if (self::MODE_PRODUCTION === $id)
		{
			if (0 !== $this->mode and self::MODE_PRODUCTION !== $this->mode)
			{
				self::$result->sysmsg("Switching to production mode\n");
			}

			error_reporting(0);
			ini_set("display_errors", "0");
			ini_set("display_startup_errors", "0");
			aw_errorhandler::set_exception_handler("handle_exception");
			$this->mode = self::MODE_PRODUCTION;
		}
		elseif (self::MODE_DBG === $id)
		{
			error_reporting(-1);
			ini_set("display_errors", "1");
			ini_set("display_startup_errors", "1");
			ini_set("ignore_repeated_errors", "1");
			ini_set("mysql.trace_mode", "1");
			aw_ini_set("debug_mode", "1");
			aw_errorhandler::set_exception_handler("handle_exception_dbg");
			$this->mode = $id;
		}
		elseif (self::MODE_DBG_CONSOLE === $id)
		{
			error_reporting(-1);
			ini_set("display_errors", "1");
			ini_set("display_startup_errors", "1");
			ini_set("ignore_repeated_errors", "1");
			ini_set("mysql.trace_mode", "1");
			aw_ini_set("debug_mode", "1");
			aw_errorhandler::set_exception_handler("handle_exception_dbg");
			//TODO: tekitada konsool uude aknasse siin
			$this->mode = $id;
		}
		elseif (self::MODE_DBG_EXTENDED === $id)
		{
			error_reporting(-1);
			ini_set("display_errors", "1");
			ini_set("display_startup_errors", "1");
			ini_set("ignore_repeated_errors", "1");
			ini_set("mysql.trace_mode", "1");
			aw_ini_set("debug_mode", "1");
			aw_global_set("debug.db_query", "1");
			aw_errorhandler::set_exception_handler("handle_exception_dbg");
			$this->mode = $id;
		}
		elseif(self::MODE_REASONABLE === $id)
		{
			error_reporting(E_ALL ^ E_NOTICE);
			ini_set("display_errors", "1");
			ini_set("display_startup_errors", "1");
			ini_set("ignore_repeated_errors", "1");
			aw_ini_set("debug_mode", "1");
			aw_errorhandler::set_exception_handler("handle_exception_dbg");
			$this->mode = $id;
		}
		else
		{
			return $this->mode;
		}
	}

	/**
	@attrib api=1 params=pos
	@returns void
	@comment
		Sets current Automatweb instance to be backward compatible with older requests, scripts and other code and also to execute differently.
	**/
	public function bc()
	{
		$this->bc = true;
		require_once(AW_DIR . "lib/bc" .AW_FILE_EXT);
		include AW_DIR . "const" . AW_FILE_EXT;
		if (self::$result instanceof aw_http_response)
		{
			self::$result->set_charset(languages::USER_CHARSET);
		}
	}
}

