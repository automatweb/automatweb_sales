<?php

namespace automatweb;

if (!defined("AW_DIR"))
{
	exit;
}

// load libraries
// required for startup
require_once AW_DIR . "lib/errorhandling" . AW_FILE_EXT;
require_once AW_DIR . "lib/config" . AW_FILE_EXT;
require_once AW_DIR . "classes/core/util/class_index" . AW_FILE_EXT;

// other. later perhaps implement conditional loading.
require_once AW_DIR . "lib/core/obj/object" . AW_FILE_EXT;
require_once AW_DIR . "lib/debug" . AW_FILE_EXT;
require_once AW_DIR . "lib/site_file_index" . AW_FILE_EXT;

function autoload($class_name)
{
	if (0 === strpos($class_name, "Zend"))
	{
		$class_file = AW_DIR . "addons/" . str_replace("_", "/", $class_name) . ".php";

		if (file_exists($class_file))
		{
			require_once $class_file;
		}

		if (!class_exists($class_name, false) and !interface_exists($class_name, false))
		{
			exit("Fatal classload error. Tried to load Zend framework class '" . $class_name . "'");//!!! tmp
		}

		return;
	}

	try
	{
		$class_file = class_index::get_file_by_name($class_name);
		require_once $class_file;
	}
	catch (awex_clidx_double_dfn $e)
	{
		exit ("Class '" . $e->clidx_cl_name . "' redeclared. Fix error in '" . $e->clidx_path1 . "' or '" . $e->clidx_path2 . "'.");//TODO tmp
		//TODO take action -- delete/rename one of the classes or load both or ...
		// $class_file = class_index::get_file_by_name($class_name);
	}
	catch (awex_clidx $e)
	{
		$caller = class_exists("automatweb\\dbg") ? " in " . dbg::call_point_str(1) : "";
		dbg::bt();
		exit("Fatal classload error. " . $e->getMessage() . " Tried to load '" . $class_name . "'" . $caller);//TODO
	}
}

function get_include_contents($filename)
{
	if (is_readable($filename))
	{
		ob_start();
		require_once $filename;
		return ob_get_clean();
	}
}

function array_union_recursive($array1 = array(), $array2 = array())
{
	if (!is_array($array1) or !is_array($array1))
	{
		throw new aw_exception("Invalid argument type.");
	}

	$array = $array1 + $array2;

	foreach ($array1 as $key => $value)
	{
		if (is_array($value) and isset($array2[$key]) and is_array($array2[$key]))
		{
			$array[$key] = array_union_recursive($value, $array2[$key]);
		}
	}

	return $array;
}

?>
