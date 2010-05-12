<?php
/*
@classinfo maintainer=voldemar
*/

// get aw directory and file extension
$__FILE__ = __FILE__;//!!! to check if works with zend encoder (__FILE__)
$aw_dir = str_replace("\\", "/", dirname($__FILE__)) . "/";
$aw_dir = str_replace("//", "/", $aw_dir);
define("AW_DIR", $aw_dir);
define("AW_FILE_EXT", substr($__FILE__, strrpos($__FILE__, "automatweb") + 10)); // extension can't be 'automatweb'

// required for Zend
set_include_path(implode(PATH_SEPARATOR, array(
	AW_DIR . "addons/",
	get_include_path(),
)));

// include required libraries
require_once(AW_DIR . "lib/main" . AW_FILE_EXT);

// set required confituration
register_shutdown_function("aw_fatal_error_handler");
ini_set("track_errors", "1");

class automatweb
{
	const MODE_DEFAULT = 1;//!!! get rid of this!
	const MODE_DBG = 2;
	const MODE_PRODUCTION = 4;
	const MODE_REASONABLE = 8;

	private $mode; // current mode
	private $request_loaded = false; // whether request is loaded or only empty initialized
	private $start_time; // float unix timestamp + micro when current aw server instance was started
	private static $instance_data = array(); // aw instance stack
	private static $current_instance_nr = 0;
	private static $default_cfg_loaded = false;

	public $bc = false; // If true, execute through ..._impl classes and other older code. Default false. read-only
	public static $request; // aw_request object of current aw instance. read-only.
	public static $instance; // current aw instance. read-only.
	public static $result; // aw_resource object. result of executing the request

	private function __construct()
	{
		// initialize object lifetime
		$this->start_time = microtime(true);
		$this->mode(self::MODE_PRODUCTION);
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
			automatweb::start();
		}
		catch (Exception $e)
		{
			try
			{
				automatweb::shutdown();
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

		automatweb::$instance->bc();
		$cfg_cache_file = dirname($cfg_file) .  "/pagecache/ini.cache";
		automatweb::$instance->load_config_files(array($cfg_file), $cfg_cache_file);
		$request = aw_request::autoload();
		automatweb::$instance->set_request($request);
		automatweb::$instance->exec();
		echo automatweb::$result->send();
		automatweb::shutdown();
		exit;
	}

	/**
	@attrib api=1 params=pos
	@returns void
	@comment
		Starts a new Automatweb application server instance.
	@errors
		Throws aw_exception if Automatweb already running.
	**/
	public static function start()
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
		$aw = new automatweb();
		$request = new aw_request();
		$result = new aw_resource();
		self::$instance_data[self::$current_instance_nr] = array(
			"instance" => $aw,
			"request" => $request,
			"result" => $result
		);

		self::$instance = $aw;
		self::$request = $request;
		self::$result = $result;
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
		$this->request_loaded = true;
	}

	/**
	@attrib api=1 params=pos
	@param files required type=array
		Configuration files to load.
	@param cache_file required type=string
		Where to write cached version of loaded configuration.
	@returns void
	@comment
		Loads configuration from given files, merging it to default configuration.
	**/
	public function load_config_files($files = array(), $cache_file)
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

		if (!aw_global_get("no_db_connection"))
		{
			$GLOBALS["object_loader"] = new _int_object_loader();
		}
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
		$baseurl = aw_ini_get("baseurl");
		$baseurl .= (("/" === substr($baseurl, -1)) ? "" : "/");
		$request_uri = $_SERVER["REQUEST_URI"];

		if (strpos($request_uri, "/automatweb") === false)
		{
			aw_redirect(new aw_uri(aw_ini_get("baseurl") . "/automatweb/"));
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

				// execute fastcall if requested
				if (isset($vars["fastcall"]) && $vars["fastcall"] == 1)
				{
					classload("fastcall_base");
					$inst = new $class;
					self::$result->set_data($inst->$action($vars));
					return;
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
				$t = new aw_template();
				$t->init("");
				if (!$t->prog_acl_auth("view", "PRG_MENUEDIT"))
				{
					$t->auth_error();
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

						//!!! TMP teha et oleks olemas see LC
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
							orb::check_class_access("admin_if");
							// go to default admin interface
							include(AW_DIR . "automatweb/admin_header" . AW_FILE_EXT);
							$id = admin_if::find_admin_if_id();
							$redirect_url = aw_ini_get("baseurl") . "/automatweb/orb.aw?class=admin_if&action=change&group=o&id={$id}";
						}
					}
					else
					{ // go to main page
						$redirect_url = aw_ini_get("baseurl") . "/automatweb/";
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
		// For quick debugging -kaarel 14.05.2009
		// Hannes will add this to his AW Mozilla add-on, so debugging will be SO much easier!
		if($id !== null && !empty($_COOKIE["manual_automatweb_mode"]))
		{
			$tmp_id = constant("self::MODE_".$_COOKIE["manual_automatweb_mode"]);
			if($tmp_id !== NULL)
			{
				$id = $tmp_id;
			}
		}

		if ((self::MODE_DEFAULT === $id) or (self::MODE_PRODUCTION === $id))
		{
			error_reporting(0);
			ini_set("display_errors", "0");
			ini_set("display_startup_errors", "0");
			set_exception_handler("aw_exception_handler");
			set_error_handler ("aw_error_handler");
			$this->mode = self::MODE_PRODUCTION;
		}
		elseif (self::MODE_DBG === $id)
		{
			error_reporting(E_ALL | E_STRICT);
			ini_set("display_errors", "1");
			ini_set("display_startup_errors", "1");
			ini_set("ignore_repeated_errors", "1");
			ini_set("mysql.trace_mode", "1");
			aw_ini_set("debug_mode", "1");
			set_exception_handler("aw_dbg_exception_handler");
			set_error_handler ("aw_dbg_error_handler");
			$this->mode = $id;
		}
		elseif(self::MODE_REASONABLE === $id)
		{
			error_reporting(E_ALL | E_STRICT);
			ini_set("display_errors", "1");
			ini_set("display_startup_errors", "1");
			ini_set("ignore_repeated_errors", "1");
			aw_ini_set("debug_mode", "1");
			set_exception_handler("aw_dbg_exception_handler");
			set_error_handler ("aw_reasonable_error_handler");
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
		ini_set("session.save_handler", "files");
		session_name("automatweb");
		session_start();
		_aw_global_init();
	}
}

?>
