<?php

require_once AW_DIR . "lib/exceptions" . AW_FILE_EXT;

if (version_compare(PHP_VERSION, '5.3.0', '<'))
{
	// define missing constants as E_USER_NOTICE
	define("E_DEPRECATED", 1024);
	define("E_USER_DEPRECATED", 1024);
}

// Returns true if the error is to be ignored
// Please add a reason to every "return true;"!
function aw_ignore_error($errno, $errstr, $errfile, $errline, $context)
{
	if(E_STRICT === $errno)
	{
		if($errstr === "Declaration of site_cache::show() should be compatible with that of core::show()")
		{
			/*
				Making either of these compatible with the other one just creates more notices! -kaarel 27.05.2009
				site_cache::show($args = array())
				core::show($args)
			*/
			return true;
		}

		if($errstr === "Declaration of _int_object_loader::_log() should be compatible with that of core::_log()")
		{
			/*
				You make them compatible! -kaarel 27.05.2009
				_int_object_loader::_log($new, $oid, $name, $clid = NULL)
				_log($type, $action, $text, $oid = 0, $honor_ini = true, $object_name = null)
			*/
			return true;
		}
	}
	elseif (E_NOTICE === $errno and "unserialize()" === substr($errstr, 0, 13) and ("/defs.aw" === substr($errfile, -8) or "\\defs.aw" === substr($errfile, -8)))
	{
		/* No way of predetermining if string to be unserialized is valid for that */
		return true;
	}

	return false;
}

// all errors and exceptions are channeled to exception handlers
function aw_exception_handler($e)
{
	$class = get_class($e);

	if ("aw_lock_exception" === $class and "object" === $e->object_class)
	{
		$blocked_object = new object($e->object_id);
		$obj_str = $blocked_object->is_saved() ? aw_ini_get("classes." . $blocked_object->class_id() . "name") . ' "' . $blocked_object->name() . '"'  : $e->object_id;

		class_base::show_error_text(t("Objekti '{$obj_str}' ei saanud lukustada, sest keegi teine kasutas seda. Proovige uuesti."));
		$request_url = aw_ini_get("baseurl") . aw_global_get("REQUEST_URI");
		$referer = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "";

		if ($_GET and aw_global_get("REQUEST_URI"))
		{
			$url = $request_url;
		}
		elseif ($referer)
		{
			$url = $referer;
		}
		else
		{
			$url = aw_ini_get("baseurl");
		}

		header ("Location: $url");
		exit;
	}
	else
	{
		try
		{
			error::raise(array(
				"id" => $class,
				"msg" => $e->getMessage(),
				"fatal" => true,
				"show" => false,
				"exception" => $e
			));
			automatweb::shutdown();
		}
		catch (Exception $e)
		{
			/*	Ajutiselt v2lja. Muidu ilmub pidevalt lehe sappa vastik veateade.
			// exception handler error
			echo get_class($e)." thrown within the exception handler. Message: ".$e->getMessage()." on line ".$e->getLine();
			echo "<br /><b>Stack trace:</b> <br />\n";
			$trace = nl2br($e->getTraceAsString());
			echo $trace;
			//!!! teha redirect vms siia
			*/
		}
	}
}

function aw_dbg_exception_handler($e)
{
	try
	{
		// display error
		if ($e instanceof awex_php_generic_error)
		{
			$file = $e->errfile;
			$line = $e->errline;
			$trace = nl2br($e->getTraceAsString()) . "<br />\n <b>Variable context:</b> <br />\n" . var_export($e->context, true);
		}
		else
		{
			$file = $e->getFile();
			$line = $e->getLine();
			$trace = nl2br($e->getTraceAsString());
		}

		echo "<b>Uncaught exception:</b> " . get_class($e) . "<br />\n";
		echo "<b>Message:</b> " . $e->getMessage() . "<br />\n";
		echo "<b>File:</b> " . $file . "<br />\n";
		echo "<b>Line:</b> " . $line . "<br />\n";
		echo "<b>Stack trace:</b> <br />\n";
		echo $trace;

		if ($e instanceof aw_exception)
		{
			$fwd = $e->get_forwarded_exceptions();
			if (count($fwd))
			{
				echo "<br />\n<b>Forwarded exceptions:</b> <br />\n";
				foreach ($fwd as $fwd_e)
				{
					arr($fwd_e);
				}
			}
		}

		flush();
		if (ob_get_status())
		{
			ob_flush();
		}

		automatweb::shutdown();
	}
	catch (Exception $e)
	{
		echo get_class($e)." thrown within the exception handler. Message: ".$e->getMessage()." on line ".$e->getLine();
		echo "<br /><b>Stack trace:</b> <br />\n";
		$trace = nl2br($e->getTraceAsString());
		echo $trace;
	}
}

function aw_error_handler($errno, $errstr, $errfile, $errline, $context)
{
	// generate and throw exception when fatal error occurs. ignore all other errors
	if (E_USER_WARNING === $errno)
	{
		error::raise(array(
			"id" => "USER WARNING",
			"msg" => $errstr,
			"fatal" => false,
			"show" => false
		));
	}
	elseif (!aw_is_non_fatal_error($errno))
	{
		$class = aw_get_error_exception_class($errno);
		$e = new $class($errstr, $errno);
		$e->errfile = $errfile;
		$e->errline = $errline;
		$e->context = $context;
		throw $e;
	}
	return true;
}

function aw_dbg_error_handler($errno, $errstr, $errfile, $errline, $context)
{
	if(aw_ignore_error($errno, $errstr, $errfile, $errline, $context))
	{
		return true;
	}

	$class = aw_get_error_exception_class($errno);
	if (aw_is_non_fatal_error($errno))
	{ // display non-fatal error information
		$err = strtoupper(substr($class, 9));
		echo "[{$err}] <b>{$errstr}</b> in {$errfile} on line {$errline}<br><br>\n\n"; //!!! aw_response objekti ja sealt footerite kaudu templatesse
		if (false !== strpos($errstr, "EXPLAIN"))
		{
			echo dbg::process_backtrace(debug_backtrace(), -1, true);
		}

		flush();
		if (ob_get_status())
		{
			ob_flush();
		}
	}
	else
	{ // generate and throw exception when fatal error occurs
		$e = new $class($errstr, $errno);
		$e->errfile = $errfile;
		$e->errline = $errline;
		$e->context = $context;
		throw $e;
	}
	return true;
}

function aw_console_error_handler($errno, $errstr, $errfile, $errline, $context)
{
	if(aw_ignore_error($errno, $errstr, $errfile, $errline, $context))
	{
		return true;
	}

	$class = aw_get_error_exception_class($errno);
	if (aw_is_non_fatal_error($errno))
	{ // display non-fatal error information
		$err = strtoupper(substr($class, 9));
		dc_dump("[{$err}] <b>{$errstr}</b> in {$errfile} on line {$errline}<br><br>\n\n");
		if (false !== strpos($errstr, "EXPLAIN"))
		{
			echo dbg::process_backtrace(debug_backtrace(), -1, true);
		}
	}
	else
	{ // generate and throw exception when fatal error occurs
		$e = new $class($errstr, $errno);
		$e->errfile = $errfile;
		$e->errline = $errline;
		$e->context = $context;
		throw $e;
	}
	return true;
}

function aw_reasonable_error_handler($errno, $errstr, $errfile, $errline, $context)
{
	if(aw_ignore_error($errno, $errstr, $errfile, $errline, $context))
	{
		return true;
	}

	static $file_cache = array();
	$current_user_is_maintainer = false;

	if (isset($file_cache[$errfile]))
	{ // file maintainer info cached
		$current_user_is_maintainer = $file_cache[$errfile];
	}
	elseif (is_file($errfile))
	{ // get file maintainer from file
		try
		{
			$uid = aw_global_get("uid"); //!!! kuidas ldapi kasutaja tavalisega samastada?
			$f = file_get_contents($errfile);
			$m = preg_match("/\@classinfo.+maintainer\=([\S]+)\s/U", $f, $md);

			if (1 === $m and $uid === $md[1])
			{
				$current_user_is_maintainer = true;
			}
		}
		catch (Exception $e)
		{
		}

		$file_cache[$errfile] = $current_user_is_maintainer;
	}

	$r = true;
	if ($current_user_is_maintainer or !aw_is_non_fatal_error($errno) or E_WARNING === $errno)
	{
		$r = aw_dbg_error_handler($errno, $errstr, $errfile, $errline, $context);
	}
	return $r;
}

function aw_fatal_error_handler($e = null)
{ // handles fatal errors as exceptions
	try // just in case. to avoid exceptions without stack frame since this is a shutdown function
	{
		if (empty($e))
		{
			$e = error_get_last();
		}

		if (!empty($e) and !aw_is_non_fatal_error($e["type"]))
		{
			// this is to find out the name of current exception handler function
			$current_exception_handler = set_exception_handler("aw_exception_handler");
			set_exception_handler($current_exception_handler);
			//////////////

			// generate exception
			$class = aw_get_error_exception_class($e["type"]);
			$E = new $class($e["message"], $e["type"]);
			$E->errfile = $e["file"];
			$E->errline = $e["line"];
			//////////////

			// handle the exception
			if ($current_exception_handler)
			{
				$current_exception_handler($E);
			}
			//////////////
		}
	}
	catch (Exception $e)
	{
		//!!!
	}
}

function aw_get_error_exception_class($error_type)
{
	static $errors = array(
		E_NOTICE => "awex_php_notice",
		E_STRICT => "awex_php_strict",
		E_WARNING => "awex_php_warning",
		E_USER_NOTICE => "awex_php_user_notice",
		E_USER_WARNING => "awex_php_user_warning",
		E_CORE_WARNING => "awex_php_core_warning",
		E_COMPILE_WARNING => "awex_php_compile_warning",
		E_PARSE => "awex_php_parse",
		E_ERROR => "awex_php_error",
		E_USER_ERROR => "awex_php_user_error",
		E_USER_DEPRECATED => "awex_php_user_notice",
		E_DEPRECATED => "awex_php_notice",
		E_CORE_ERROR => "awex_php_core_error"
	);

	if (isset($errors[$error_type]))
	{
		$class = $errors[$error_type];
	}
	else
	{
		$class = "awex_php_fatal";
	}
	return $class;
}

function aw_is_non_fatal_error($error_type)
{
	static $non_fatal_errors = array(
		E_NOTICE => 1,
		E_STRICT => 1,
		E_DEPRECATED => 1,
		E_WARNING => 1,
		E_USER_NOTICE => 1,
		E_USER_WARNING => 1,
		E_USER_DEPRECATED => 1,
		E_CORE_WARNING => 1,
		E_COMPILE_WARNING => 1
	);
	return isset($non_fatal_errors[$error_type]);
}
