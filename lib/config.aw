<?php

/** Returns aw configuration setting(s) identified by $var.
	@attrib api=1
	@param var required type=string
	@returns mixed
		Configuration setting value. string or array or recursively same.
	@errors
		Throws awex_cfg_key if no such setting
**/
function aw_ini_get($var)
{
	$path = explode(".", $var);

	if ("" === $path[0])
	{
		throw new awex_cfg_key("Invalid key");
	}
	else
	{
		foreach ($path as $index)
		{
			if (isset($val[$index]))
			{
				$val = $val[$index];
			}
			elseif(!isset($val) and isset($GLOBALS["cfg"][$index]))
			{
				$val = $GLOBALS["cfg"][$index];
			}
			else
			{
				$e = new awex_cfg_key("Invalid key '" . $var . "'");
				$e->awcfg_key = $var;
				throw $e;
			}
		}
	}

	return $val;
}

/** Determines if configuration setting is set.
	@attrib api=1
	@param var required type=string
	@returns boolean
**/
function aw_ini_isset($var)
{
	$path = explode(".", $var);
	$path = implode('"]["', $path); //XXX: v6ibolla replace kiirem?
	return eval('return !empty($GLOBALS["cfg"]["' . $path . '"]);');
}


/** Alters or sets a configuration setting value in runtime environment.
	@attrib api=1
	@param var required type=string
		Name of configuration setting to change
	@param value required type=string
		New value. Value can also be an array of strings.
	@returns void
**/
// saving not yet implemented
function aw_ini_set($var, $value, $save = false)
{
	$setting = "\$GLOBALS['cfg']['" . str_replace(".", "']['", $var) . "'] = " . var_export($value, true) . ";";
	eval($setting);

	if (false === strpos($var, "."))
	{
		$setting = "\$GLOBALS['cfg']['__default__short']['" . str_replace(".", "']['", $var) . "'] = " . var_export($value, true) . ";";
		eval($setting);
	}

	// if ($save)
	// {
	// }
}

/** return global variable value
	@attrib api=1 params=pos

	@param var required type=string
		The variable whose value you want to fetch

	@returns
		The aw global variable value for the given variable if set.

	@comment
		This fetches values from the request, cookie and session and those set from aw_global_set.
		this function replaces php's GLOBAL - it keeps global variables in a global object instance
		why is this? well, because then they can't be set from the url, overriding the default values
		and causing potential security problems
**/
function aw_global_get($var)
{
	return isset($GLOBALS["__aw_globals"][$var]) ? $GLOBALS["__aw_globals"][$var] : null;
}

/** Sets an aw global variable value
	@attrib api=1 params=pos

	@param var required type=string
		The name of the variable to set the value for

	@param val required type=string
		The value of the variable to set

	@comment
		Values set by this can be fetched by aw_global_get()
**/
function aw_global_set($var,$val)
{
	$GLOBALS["__aw_globals"][$var] = $val;
}

/** Parses an aw standard ini format file $file to php associative array.
	@param file required type=string
		Configuration file full path
	@param return optional type=boolean
		New value. Value can also be an array of strings.
	@returns void|array
		If $return parameter TRUE, returns result array
**/
function parse_config($file, $return = false)
{
	$fd = file($file);
	$config = array();
	$autoloaded_variables = array(
		"site_basedir" => 1,
		"site_public_root_dir" => 1,
		"basedir" => 1,
		"baseurl" => 1
	);

	foreach($fd as $linenum => $line)
	{
		// parse line
		if (strlen(trim($line)) and $line{0} !== "#") // exclude comments and empty lines
		{
			// config option format is variable = value. variable is class1. ... .classN.
			$data = explode("=", $line, 2);

			if (2 === count($data))
			{ // process regular variable
				$var = str_replace(array('["','"]',"['","']","[","]"), array(".","",".", "",".", ""), trim($data[0]));//!!! should be deprecated and only '.' notation used. kept here for back compatibility.
				$value = trim($data[1]);

				// now, replace all variables in varvalue
				try
				{
					$value = preg_replace('/\$\{(.*)\}/e', "aw_ini_get(\"\\1\")",$value);
				}
				catch (awex_cfg_key $e)
				{
					$e = new awex_cfg_file("Failed to parse configuration file '" . $file . "' on line " . ($linenum + 1) . ". Invalid key '" . $e->awcfg_key . "'.");
					$e->awcfg_file = $file;
					$e->awcfg_line = $linenum + 1;
					throw $e;
				}

				// add setting
				if (!isset($autoloaded_variables[$var]) or "" === $autoloaded_variables[$var])
				{
					if ($return)
					{
						$config[] = $var . "=" . $value;
					}
					else
					{
						$setting_index = explode(".", $var);

						// for loading cfg here
						$setting_path = "\$GLOBALS['cfg']";

						foreach ($setting_index as $key => $index)
						{
							$setting_path .= "['" . $index . "']";

							if (isset($setting_index[$key + 1]) and eval("return (isset(" . $setting_path . ") and !is_array(" . $setting_path . "));"))
							{
								eval($setting_path . " = array();");
							}
						}

						// for caching
						$setting_path = "\$config";

						foreach ($setting_index as $key => $index)
						{
							$setting_path .= "['" . $index . "']";

							if (isset($setting_index[$key + 1]) and eval("return (isset(" . $setting_path . ") and !is_array(" . $setting_path . "));"))
							{
								eval($setting_path . " = array();");
							}
						}

						$str = str_replace(".", "']['", $var) . "'] = " . var_export($value, true) . ";";

						// load setting here
						$setting = "\$GLOBALS['cfg']['" . $str;
						eval($setting);

						// store for caching
						$setting = "\$config['" . $str;
						eval($setting);
					}
				}
			}
			elseif ("include" === substr(trim($line), 0, 7))
			{ // process config file include
				$line = preg_replace('/\$\{(.*)\}/e',"aw_ini_get(\"\\1\")",$line);
				$ifile = trim(substr($line, 7));

				if (!is_readable($ifile))
				{
					$e = new awex_cfg_file("Failed to open include file '" . $ifile . "' on line " . ($linenum + 1) . " in file '" . $file . "'.");
					$e->awcfg_file = $file;
					$e->awcfg_line = $linenum + 1;
					throw $e;
				}

				if ($return)
				{
					$config = array_merge($config, parse_config($ifile, true));
				}
				else
				{
					$config = array_union_recursive(parse_config($ifile), $config);
				}
			}
		}
	}

	return $config;
}

/**
	@param files required type=array
		Configuration files full paths
	@param cache_file optional type=string
		If specified, parsed configuration will be saved to cache in $cache_file and read from there if it already exists and is up to date.
	@comment
		Loads aw configuration files $files to system memory. if $cache_file not up to date or not readable. Caches result to $cache_file
		Some automatic settings depend on from where (file, class, method) this function is called
	@returns void
**/
function load_config ($files = array(), $cache_file = null)
{
	if (empty($files) or !is_array($files))
	{
		throw new awex_cfg_file("Configuration files not specified.");
	}

	// init "db"
	if (!isset($GLOBALS["cfg"]))
	{
		$GLOBALS["cfg"] = array();
	}

	// determine directories
	//TODO: mitte lugeda iga kord, viia installerisse
	$GLOBALS["cfg"]["basedir"] = AW_DIR;

//TODO: if DOCUMENT_ROOT not available, then site dir set to aw framework code directory files subdir. may not be a good solution. check for errors
//TODO: allolev on ebakindel heuristika v6ibolla
	// get directory depending on caller
	// str_replace is for windows style paths
	list($class, $method, $line, $file) = get_caller();
	if ("automatweb" === $class and ("start" === $method or "load_config_files" === $method))
	{
		list($class, $method, $line, $file) = get_caller(1);
		$site_public_root_dir = str_replace(DIRECTORY_SEPARATOR, "/", realpath(dirname($file))) . "/";
		$site_basedir = str_replace(DIRECTORY_SEPARATOR, "/", realpath(dirname($file) . "/../")) . "/";
		// $site_basedir = empty($_SERVER["DOCUMENT_ROOT"]) ? AW_DIR . "files/" : str_replace(DIRECTORY_SEPARATOR, "/", realpath($_SERVER["DOCUMENT_ROOT"]."/../")) . "/";
	}
	else
	{
		//TODO
	}

	$GLOBALS["cfg"]["site_basedir"] = $site_basedir;
	$GLOBALS["cfg"]["site_public_root_dir"] = $site_public_root_dir;

	//selle peab ikka igaltpoolt uuesti saama, muidu ei saa sisev6rgust ja mujalt ligi
	if (empty($GLOBALS["cfg"]["no_update_baseurl"]) and isset($_SERVER["HTTP_HOST"]))
	{
		$baseurl = "http://" . $_SERVER["HTTP_HOST"] . "/";
		$GLOBALS["cfg"]["baseurl"] = $baseurl;
	}

	// determine cache state
	$cache_success = true;
	if ($cache_file and file_exists($cache_file))
	{
		// check the modification date of each of the config files
		$cache_timestamp = filemtime($cache_file);
		foreach($files as $file)
		{
			if (filemtime($file) >= $cache_timestamp)
			{
				$cache_success = false;
			}
		}
	}
	else
	{
		$cache_success = false;
	}

	// try read from cache
	if ($cache_success)
	{
		$cfg = file_get_contents($cache_file);

		if (false === $cfg)
		{
			throw new awex_cfg_file("Configuration cache file not readable.");
		}

		$cfg = unserialize($cfg);

		// check if cache file is for the same files and in same order as are those requested to be loadad
		if (isset($cfg["__aw_ini_cache_meta_cached_files"]))
		{
			if ($cfg["__aw_ini_cache_meta_cached_files"] !== $files)
			{
				$cache_success = false;
				unset($cfg["__aw_ini_cache_meta_cached_files"]);
			}
		}

		if (!is_array($cfg))
		{
			throw new awex_cfg_file("Configuration cache file corrupt.");
		}

		$GLOBALS["cfg"] = array_union_recursive($cfg, $GLOBALS["cfg"]);
		$cache_success = true;
	}

	// load from file
	if (!$cache_success)
	{
		$cfg = array();

		foreach($files as $file)
		{
			$cfg = array_union_recursive(parse_config($file), $cfg);
		}

		// and write to cache if file is specified
		if (!empty($cache_file))
		{
			if (!is_dir(dirname($cache_file)))
			{
				mkdir(dirname($cache_file), 0660);
			}

			if (!is_writable(dirname($cache_file)))
			{
				$success = chmod($cache_file, 0770);
				if (!$success)
				{
					throw new awex_cfg_file("Mode change failed for cache file directory.");
				}

				if (!is_writable(dirname($cache_file)))
				{
					chmod($cache_file, 0777);
				}

				if (!is_writable(dirname($cache_file)))
				{
					throw new awex_cfg_file("No permissions for cache file directory.");
				}
			}

			if (file_exists($cache_file) and !is_writable($cache_file))
			{
				$success = chmod($cache_file, 0660);
				if (!$success)
				{
					throw new awex_cfg_file("Mode change failed for cache file.");
				}

				if (!is_writable($cache_file))
				{
					chmod($cache_file, 0666);
				}

				if (!is_writable($cache_file))
				{
					throw new awex_cfg_file("No permissions for cache file.");
				}
			}

			$cfg["__aw_ini_cache_meta_cached_files"] = $files;
			$cfg = serialize($cfg);
			$bytes = file_put_contents($cache_file, $cfg, LOCK_EX);

			if ($bytes !== strlen($cfg))
			{
				throw new awex_cfg_file("Failed to write configuration file cache.");
			}
		}

		// apply configuration settings
/*TODO: t88le panna mingi variant
		// write lock type to lock class file
		// (for that copy locker class file contents to cache, replacing what it extends)
		$locker_reflection = new ReflectionClass("aw_locker_template");
		$set_locker_type = aw_ini_get("aw_locker.type");
		if ("aw_locker_{$set_locker_type}" !== $locker_reflection->getExtensionName())
		{
			$locker_contents = file_get_contents($locker_reflection->getFileName());
			$locker_contents = str_replace("class aw_locker extends aw_locker_none\n{", "class aw_locker extends aw_locker_none\n{", $locker_contents);
			$locker_contents = preg_replace("/class aw_locker extends aw_locker_[A-z_]+(\n|\r|\r\n){/imU", "class aw_locker extends aw_locker_{$set_locker_type}\n{", $locker_contents);
			class_index::create_local_class("aw_locker", $locker_contents);
		}
		*/
	}

	aw_global_set("lang_id", languages::get_active());

	// only load those definitions if fastcall is not set
	if (empty($_REQUEST["fastcall"]))
	{
//TODO: /* defining class id-s as global constants is to be taken out of use */
		if (!empty($GLOBALS["cfg"]["classes"]))
		{
			// and here do the defs for classes
			foreach($GLOBALS["cfg"]["classes"] as $clid => $cld)
			{
				if (isset($cld["def"]) and !defined($cld["def"]))
				{
					define($cld["def"], $clid);
					if (isset($cld["file"]))
					{
						$bnf = basename($cld["file"]);
						if (!isset($GLOBALS["cfg"]["class_lut"][$bnf]))
						{
							$GLOBALS["cfg"]["class_lut"][$bnf] = $clid;
						}
					}
				}
			}

			//TODO: doc erand kaotada
			// special case for doc
			$GLOBALS["cfg"]["class_lut"]["doc"] = 7;
		}
	}
}


/// I think we should just use the PHP superglobal $GLOBALS for storing
// those variables instead of messing with our own objects. Empty it
// first and then put variables we need into it.

// well. our own stuff kinda.. I dunno, feels better. but yeah, it also feels a lot slower.
// and yeah. we shouldn't need these before aw_startup() and we could init it in there.. - terryf

// .. and now they are.
function _aw_global_init()
{
	// reset aw_global_* function globals
	$GLOBALS["__aw_globals"] = array();
	$lang_cookie_name = languages::get_cookie_name();

	// import CGI spec variables and apache variables

	// but we must do this in a certain order - first the global vars, then the session vars and then the server vars
	// why? well, then you can't override server vars from the url.

	// known variables - these can be modified by the user and are not to be trusted, so we get them first
	$impvars = array(
		"lang_id",
		"DEBUG",
		"no_menus",
		"section",
		"class",
		"action",
		"fastcall",
		"reforb",
		"set_lang_id",
		"admin_lang",
		"admin_lang_lc",
		"LC",
		"period",
		"oid",
		"print",
		"sortby",
		"sort_order",
		"cal",
		"date",
		"project",
		"view"
	);

	foreach($impvars as $k)
	{
		if (isset($GLOBALS[$k]))
		{
			aw_global_set($k, $GLOBALS[$k]);
		}
		elseif (isset($_REQUEST[$k]))
		{
			aw_global_set($k,$_REQUEST[$k]);
		}
	}

	// server vars - these can be trusted pretty well, so we do these last
	$server = array("SERVER_SOFTWARE", "SERVER_NAME", "GATEWAY_INTERFACE", "SERVER_PROTOCOL", "SERVER_PORT","REQUEST_METHOD",  "PATH_TRANSLATED","SCRIPT_NAME", "QUERY_STRING", "REMOTE_ADDR", "REMOTE_HOST", "HTTP_ACCEPT","HTTP_ACCEPT_CHARSET", "HTTP_ACCEPT_ENCODING", "HTTP_ACCEPT_LANGUAGE", "HTTP_CONNECTION", "HTTP_HOST", "HTTP_REFERER", "HTTP_USER_AGENT","REMOTE_PORT","SCRIPT_FILENAME", "SERVER_ADMIN", "SERVER_PORT", "SERVER_SIGNATURE", "PATH_TRANSLATED", "SCRIPT_NAME", "REQUEST_URI", "PHP_SELF", "DOCUMENT_ROOT", "PATH_INFO", "SERVER_ADDR", "HTTP_X_FORWARDED_FOR");

	// why don't we just use $_SERVER where needed?
	foreach($server as $var)
	{
		aw_global_set($var, isset($_SERVER[$var]) ? $_SERVER[$var] : null);
	}

	if (isset($_COOKIE[$lang_cookie_name]) && !isset($_SESSION[$lang_cookie_name]))
	{
		aw_global_set("lang_id", $_COOKIE[$lang_cookie_name]);
	}

	if (isset($_REQUEST))
	{
		aw_global_set("request", $_REQUEST);
	}

	$GLOBALS["__aw_globals_inited"] = true;
}

/** Generic configuration error condition **/
class awex_cfg extends aw_exception {}

/** Indicates invalid configuration setting key **/
class awex_cfg_key extends awex_cfg
{
	public $awcfg_key;
}

/** Configuration file filesystem errors. Not found, not readable, etc. **/
class awex_cfg_file extends awex_cfg
{
	public $awcfg_file;
	public $awcfg_line;
}
