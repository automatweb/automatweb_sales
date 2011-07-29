<?php

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

function __autoload($class_name)
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
			debug_print_backtrace();
			exit("Fatal classload error. Tried to load Zend framework class '" . $class_name . "'");//!!! tmp
		}

		return;
	}
	else
	{
		class_index::load_class($class_name);
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

function get_caller($steps_back = 0)
{
	++$steps_back;
	++$steps_back;
	$trace = debug_backtrace();
	$class = empty($trace[$steps_back]["class"]) ? "" : $trace[$steps_back]["class"] . "::";
	$method = empty($trace[$steps_back]["function"]) ? "()" : $trace[$steps_back]["function"] . "()";
	$line = empty($trace[$steps_back]["line"]) ? $trace[$steps_back - 1]["line"] : $trace[$steps_back]["line"];
	$caller = "{$class}{$method} on line {$line}";
	return $caller;
}
