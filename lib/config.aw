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
//	enter_function("__global::aw_ini_get",array());
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

//	exit_function("__global::aw_ini_get");
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
	$path = implode('"]["', $path); //!!! v6ibolla replace kiirem?
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

	foreach($fd as $linenum => $line)
	{
		// parse line
		if (strlen(trim($line)) and $line{0} != "#") // exclude comments and empty lines
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
					$var = preg_replace('/\$\{(.*)\}/e', "aw_ini_get(\"\\1\")",$var);
				}
				catch (awex_cfg_key $e)
				{
					$e = new awex_cfg_file("Failed to parse configuration file '" . $file . "' on line " . ($linenum + 1) . ". Invalid key '" . $e->awcfg_key . "'.");
					$e->awcfg_file = $file;
					$e->awcfg_line = $linenum + 1;
					throw $e;
				}

				// add setting
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
	@returns void
**/
function load_config ($files = array(), $cache_file = null)
{
	$is_cached = true;

	if (!isset($GLOBALS["cfg"]))
	{
		$GLOBALS["cfg"] = array();
	}

	$GLOBALS["cfg"]["basedir"] = AW_DIR;
	$site_basedir = str_replace("\\", "/", realpath($_SERVER["DOCUMENT_ROOT"]."/../"));
	$GLOBALS["cfg"]["site_basedir"] = $site_basedir;

	// get the modification date on the ini cache
	if (file_exists($cache_file) and is_array($files))
	{
		// check the modification date of each of the config files
		$cache_timestamp = filemtime($cache_file);

		foreach($files as $k => $file)
		{
			if (filemtime($file) >= $cache_timestamp)
			{
				$is_cached = false;
			}
		}
	}
	else
	{
		$is_cached = false;
	}

	// read from cache
	$read_from_cache = false;
	if ($is_cached)
	{
		$cfg = file_get_contents($cache_file);

		if (false === $cfg)
		{
			throw new aw_exception("Configuration cache file not readable.");
		}

		$cfg = unserialize($cfg);

		// check if cache file is for the same files and in same order as are those requested to be loadad
		if (isset($cfg["__aw_ini_cache_meta_cached_files"]))
		{
			if ($cfg["__aw_ini_cache_meta_cached_files"] !== $files)
			{
				$read_from_cache = false;
				unset($cfg["__aw_ini_cache_meta_cached_files"]);
			}
		}

		if (!is_array($cfg))
		{
			throw new aw_exception("Configuration cache file corrupt.");
		}

		$GLOBALS["cfg"] = array_union_recursive($cfg, $GLOBALS["cfg"]);
		$read_from_cache = true;
	}

	//selle peab ikka igaltpoolt uuesti saama, muidu ei saa sisev6rgust ja mujalt ligi
	if (empty($GLOBALS["cfg"]["no_update_baseurl"]) and isset($_SERVER["HTTP_HOST"]))
	{
		$baseurl = "http://" . $_SERVER["HTTP_HOST"];
		$GLOBALS["cfg"]["baseurl"] = $baseurl;
	}

	// load from file
	if (!$read_from_cache)
	{
		$cfg = array();

		foreach($files as $file)
		{
			$cfg = array_union_recursive(parse_config($file), $cfg);
		}

		// and write to cache if file is specified
		if (isset($cache_file))
		{
			if (!is_writable(dirname($cache_file)))
			{
				$success = chmod($cache_file, 0770);
				if (!$success)
				{
					throw new aw_exception("Mode change failed for cache file directory.");
				}

				if (!is_writable(dirname($cache_file)))
				{
					chmod($cache_file, 0777);
				}

				if (!is_writable(dirname($cache_file)))
				{
					throw new aw_exception("No permissions for cache file directory.");
				}
			}

			if (file_exists($cache_file) and !is_writable($cache_file))
			{
				$success = chmod($cache_file, 0660);
				if (!$success)
				{
					throw new aw_exception("Mode change failed for cache file.");
				}

				if (!is_writable($cache_file))
				{
					chmod($cache_file, 0666);
				}

				if (!is_writable($cache_file))
				{
					throw new aw_exception("No permissions for cache file.");
				}
			}

			$cfg["__aw_ini_cache_meta_cached_files"] = $files;
			$cfg = serialize($cfg);
			$bytes = file_put_contents($cache_file, $cfg, LOCK_EX);

			if ($bytes !== strlen($cfg))
			{
				throw new aw_exception("Failed to write configuration file cache.");
			}
		}
	}

	// siin ei saa veel aw_global_get'i kasutada, kuna defsi pole veel laetud
	if (aw_ini_isset("tpldir"))
	{
		aw_ini_set("site_tpldir", aw_ini_get("tpldir"));
	}

	// kui saidi "sees", siis votame templated tolle saidi juurest, ehk siis ei puutu miskit

	// only load those definitions if fastcall is not set. This shouldnt break anything
	// and should save us a little memory. -- duke
	if (empty($_REQUEST["fastcall"]))
	{
/* defining class id-s as global constants is to be taken out of use */
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

			// special case for doc
			$GLOBALS["cfg"]["class_lut"]["doc"] = 7;
		}
/* defining various names as global constants is deprecated
		// and here do the defs for programs
		if (!empty($GLOBALS["cfg"]["programs"]))
		{
			foreach($GLOBALS["cfg"]["programs"] as $prid => $prd)
			{
				if (!defined($prd["def"]))
				{
					define($prd["def"], $prid);
				}
			}
		}

		// and here do the defs for errors
		if (!empty($GLOBALS["cfg"]["errors"]))
		{
			foreach($GLOBALS["cfg"]["errors"] as $erid => $erd)
			{
				if (isset($erd["def"]) and !defined($erd["def"]))
				{
					define($erd["def"], $erid);
				}
			}
		}

		// defines for syslog
		if (!empty($GLOBALS["cfg"]["syslog"]["types"]))
		{
			foreach($GLOBALS["cfg"]["syslog"]["types"] as $stid => $std)
			{
				if (isset($std["def"]) and !defined($std["def"]))
				{
					define($std["def"], $stid);
				}
			}
		}

		// defines for syslog actions
		if (!empty($GLOBALS["cfg"]["syslog"]["actions"]))
		{
			foreach($GLOBALS["cfg"]["syslog"]["actions"] as $said => $sad)
			{
				if (!defined($sad["def"]))
				{
					define($sad["def"], $said);
				}
			}
		}

		// ...
		if (!empty($GLOBALS["cfg"]["translate"]["ids"]))
		{
			foreach($GLOBALS["cfg"]["translate"]["ids"] as $tid => $tdef)
			{
				if (!defined($tdef))
				{
					define($tdef, $tid);
				}
			}
		}
*/
	}
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

?>
