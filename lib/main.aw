<?php

defined("AW_DIR") or exit;

// load libraries
// required for startup
require_once AW_DIR . "lib/errorhandling" . AW_FILE_EXT;
require_once AW_DIR . "lib/config" . AW_FILE_EXT;
require_once AW_DIR . "classes/core/util/class_index" . AW_FILE_EXT;

// other. later perhaps implement conditional loading.
require_once AW_DIR . "lib/core/obj/object" . AW_FILE_EXT;
require_once AW_DIR . "lib/debug" . AW_FILE_EXT;
require_once AW_DIR . "lib/site_file_index" . AW_FILE_EXT;

spl_autoload_register("zend_autoload");
spl_autoload_register(array("class_index", "load_class"));


function zend_autoload($class_name)
{
	if (0 === strpos($class_name, "Zend_"))
	{
		$class_file = AW_DIR . "addons/" . str_replace("_", "/", $class_name) . ".php";

		if (file_exists($class_file))
		{
			require_once $class_file;
		}
	}
}

function get_include_contents($filename)
{
	if (is_readable($filename))
	{
		ob_start();
		require_once $filename;
		$r = ob_get_clean();
	}
	else
	{
		$r = false;
	}

	return $r;
}

function array_union_recursive(array $array1 = array(), array $array2 = array())
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
	$steps_back_file = $steps_back + 1;
	$steps_back += 2;
	$trace = debug_backtrace();
	$file = empty($trace[$steps_back_file]["file"]) ? "" : $trace[$steps_back_file]["file"];
	$class = empty($trace[$steps_back]["class"]) ? "" : $trace[$steps_back]["class"];
	$method = empty($trace[$steps_back]["function"]) ? "" : $trace[$steps_back]["function"];
	$line = (empty($trace[$steps_back_file]["line"]) ? $trace[$steps_back]["line"] : $trace[$steps_back_file]["line"]);
	return array($class, $method, $line, $file);
}

function get_caller_str($steps_back = 0, $file = false)
{
	++$steps_back;
	list($class, $method, $line, $file_path) = get_caller($steps_back);
	$file = $file ? " in {$file_path}" : "";
	$class = $class ? $class . "::" : "";
	$method .= "()";
	$caller = "{$class}{$method} on line {$line}{$file}";
	return $caller;
}
