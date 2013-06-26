<?php

require_once AW_DIR . "lib/exceptions" . AW_FILE_EXT;

if (version_compare(PHP_VERSION, '5.3.0', '<'))
{
	// define missing constants as E_USER_NOTICE
	define("E_DEPRECATED", 1024);
	define("E_USER_DEPRECATED", 1024);
}

class aw_errorhandler
{
	private static $non_fatal_errors = array(
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

	private static $error_severity_names = array(
		E_ERROR => "ERROR",
		E_WARNING => "WARNING",
		E_PARSE => "PARSE ERROR",
		E_NOTICE => "NOTICE",
		E_CORE_ERROR => "CORE ERROR",
		E_CORE_WARNING => "CORE WARNING",
		E_COMPILE_ERROR => "COMPILE ERROR",
		E_COMPILE_WARNING => "COMPILE WARNING",
		E_USER_ERROR => "USER ERROR",
		E_USER_WARNING => "USER WARNING",
		E_USER_NOTICE => "USER NOTICE",
		E_STRICT => "STRICT STANDARDS NOTICE",
		E_RECOVERABLE_ERROR => "RECOVERABLE ERROR",
		E_DEPRECATED => "DEPRECATION NOTICE",
		E_USER_DEPRECATED => "USER DEPRECATION NOTICE"
	);

	private static $current_exception_handler = "handle_exception";

	public static function set_exception_handler($method)
	{
		if ("handle_exception" !== $method and "handle_exception_dbg" !== $method)
		{
			throw new aw_exception("Invalid exception handler method name '{$method}' given");
		}

		set_exception_handler(array("aw_errorhandler", $method));
		self::$current_exception_handler = $method;
	}

	// Returns true if the error is to be ignored
	// Please add a reason to every "return true;"!
	private static function ignore_error($errno, $errstr, $errfile, $errline)
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
		} elseif (E_WARNING === $errno and "fsockopen()" === substr($errstr, 0, 11) and $errline === 39) {
			/*
				-kaarel 17.06.2013
			*/
			return true;
		}

		return false;
	}

	// all errors and exceptions are channeled to exception handlers
	public static function handle_exception($e)
	{
		if ($e instanceof aw_lock_exception and "object" === $e->object_class)
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

			header ("Location: {$url}");
			exit;
		}
		else
		{
			try
			{
				error::raise(array(
					"id" => get_class($e),
					"msg" => $e->getMessage(),
					"fatal" => true,
					"show" => false,
					"exception" => $e
				));
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

	public static function handle_exception_dbg($e)
	{
		try
		{
			self::display_exception($e);
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

	public static function handle_error($errno, $errstr, $errfile, $errline)
	{
		if(!self::ignore_error($errno, $errstr, $errfile, $errline))
		{
			// generate and throw exception when fatal or significant error occurs. ignore all other errors
			if (
				!isset(self::$non_fatal_errors[$errno]) or // throw exception on fatal errors
				(E_WARNING === $errno and (
					strpos($errstr, "No such file or directory") !== false // file path exception
						//TODO: konverteerida filepath awex_bad_filepath-iks vms.
				)) or
				(E_NOTICE === $errno and (
					strpos($errstr, "Detected an illegal character in input string") !== false // iconv exception
				))
			)
			{
				$e = new ErrorException($errstr, 0, $errno, $errfile, $errline);
				throw $e;
			}
			elseif (ini_get("display_errors"))
			{
				$e = new ErrorException($errstr, 0, $errno, $errfile, $errline);
				self::display_handled_exception($e);
			}
			elseif (E_USER_WARNING === $errno)
			{
				//TODO: meili saatmiseks. teha see ymber
				error::raise(array(
					"id" => "USER WARNING",
					"msg" => $errstr,
					"fatal" => false,
					"show" => false
				));
			}
			elseif (aw_ini_get("errors.log_to"))
			{ // this error logging works only after ini cfg loaded
				$error_type = isset(self::$error_severity_names[$errno]) ? self::$error_severity_names[$errno] : "UNKNOWN ERROR";
				$bt = dbg::sbt();
				$time = gmdate("Y M d H:i:s");
				$uid = aw_global_get("uid");
				$url = automatweb::$request->get_uri()->get();
				$url = addcslashes($url, "'");
				$ip = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : "";
				$user_agent = addcslashes(isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : "", "'");
				$msg = <<<ENDMSG

================================================
{$error_type}: {$errstr}
LOCATION: {$errfile}:{$errline}
BACKTRACE: {$bt}
DATA: Time {$time}; User '{$uid}'; URL '{$url}'; IP '{$ip}'; Agent '{$user_agent}'


ENDMSG;

				$logged = error_log($msg, 3, aw_ini_get("errors.log_to"));
				if (!$logged)
				{
					trigger_error(sprintf("Couldn't write to error log '%s'", aw_ini_get("errors.log_to")), E_USER_WARNING);
				}
			}
		}

		return true;
	}

	private static function display_exception($e)
	{
		$file = $e->getFile();
		$line = $e->getLine();
		$trace = dbg::process_backtrace($e->getTrace(), -1, true, true, true);

		echo "<h3>Exception</h3>\n";
		echo "<em>Class:</em> " . get_class($e) . "<br />\n";
		echo "<em>Message:</em> " . $e->getMessage() . "<br />\n";
		if ($e instanceof ErrorException) echo "<em>Severity:</em> " . (isset(self::$error_severity_names[$e->getSeverity()]) ? self::$error_severity_names[$e->getSeverity()] : "UNKNOWN ERROR") . "<br />\n";
		echo "<em>Code:</em> " . $e->getCode() . "<br />\n";
		echo "<em>File:</em> " . $file . "<br />\n";
		echo "<em>Line:</em> " . $line . "<br />\n";
		echo "<em>Stack trace:</em> <br />\n";
		echo $trace;

		if ($e instanceof aw_exception)
		{
			$fwd = $e->get_forwarded_exceptions();
			if (count($fwd))
			{
				echo "<br />\n<em>Forwarded exceptions:</em> <br />\n";
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
	}

	private static function display_handled_exception($e)
	{
		if ($e instanceof ErrorException)
		{ // display non-fatal error information
			$errno = $e->getSeverity();
			$errstr = $e->getMessage();
			$errfile = $e->getFile();
			$errline = $e->getLine();
			$err = isset(self::$error_severity_names[$errno]) ? self::$error_severity_names[$errno] : "UNKNOWN ERROR";
			echo "<p>[{$err}] <strong>{$errstr}</strong> in {$errfile} on line {$errline}</p>\n\n"; //TODO: aw_response objekti ja sealt footerite kaudu templatesse
			if (false !== strpos($errstr, "EXPLAIN"))
			{
				echo dbg::process_backtrace(debug_backtrace(), -1, true);
			}
		}
		else
		{
			self::display_exception($e);
		}

		flush();
		if (ob_get_status())
		{
			ob_flush();
		}
	}

	private static function notify_by_email($e)
	{
		if ($e instanceof Exception)
		{
			$msg = "[" . get_class($e) . "] " . $e->getMessage();
			$msg .= "\nFile: " . $e->getFile();
			$msg .= "\nLine: " . $e->getLine();
			$msg .= "\nBacktrace:" . str_replace("#", "\n#", $e->getTraceAsString());
		}
		elseif (function_exists("debug_backtrace"))
		{
			$msg = "[UNKNOWN ERROR] " . $e;
			$msg .= dbg::process_backtrace(debug_backtrace());
		}

		$subj = str_replace(array("http://", "https://"), "", aw_ini_get("baseurl"));

		$content = "\nVeateade: " . htmlspecialchars_decode(strip_tags($msg));
		$content.= "\nPHP_SELF: ".aw_global_get("PHP_SELF");
		$content.= "\nlang_id: ".aw_global_get("lang_id");
		$content.= "\nuid: ".aw_global_get("uid");
		$content.= "\nsection: ".aw_global_get("section");
		$content.= "\nurl: " . aw_ini_get("baseurl") . aw_global_get("REQUEST_URI");
		$content.= "\nreferer: " . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "");
		$content.= "\nis_rpc_call: " . (int) aw_global_get("__is_rpc_call");
		$content.= "\nrpc_call_type: " . aw_global_get("__rpc_call_type");

		if (isset($_GET["password"]))
		{
			$_GET["password"] = "***";
		}

		if (isset($_POST["password"]))
		{
			$_POST["password"] = "***";
		}

		$content .= "\n\n\$_GET:\n";
		$content .= print_r($_GET, true);
		$content .= "\n\n\$_POST:\n";
		$content .= print_r($_POST, true);
		$content .= "\n\n\$_COOKIE:\n";
		$content .= print_r($_COOKIE, true);
		$content .= "\n\n\$_SERVER:\n\n";
		$content .= print_r($_SERVER, true);

		// try to find the user's email;
		$head = "";
		if ($uid = aw_global_get("uid"))
		{
			$uso = obj(aw_global_get("uid_oid"));
			$eml = $uso->prop("email");
			if (!$eml)
			{
				$eml = "automatweb@automatweb.com";
			}
			$head="From: {$uid} <{$eml}>\n";
		}
		else
		{
			$head="From: automatweb@automatweb.com\n";
		}

		if (substr(ifset($_REQUEST, "class"), 0, 4) === "http" || substr(ifset($_REQUEST, "entry_id"), 0, 4) === "http")
		{
			$send_mail = false;
		}

		if (isset($_SERVER["REQUEST_METHOD"]) and $_SERVER["REQUEST_METHOD"] === "OPTIONS")
		{
			$send_mail = false;
		}

		if (isset($_SERVER["REDIRECT_REQUEST_METHOD"]) and $_SERVER["REDIRECT_REQUEST_METHOD"] === "PROPFIND")
		{
			$send_mail = false;
		}

		$mh = md5($content);
		if (isset($_SESSION["aw_errorhandler"]["last_mail"]) and $_SESSION["aw_errorhandler"]["last_mail"] === $mh and isset($_SESSION["aw_errorhandler"]["last_mail_time"]) and $_SESSION["aw_errorhandler"]["last_mail_time"] > (time() - 60))
		{
			$send_mail = false;
		}

		if ($send_mail)
		{
			if (aw_ini_get("errors.send_to"))
			{
				$bug_receiver = aw_ini_get("errors.send_to");
			}

			send_mail($bug_receiver, $subj, $content, $head);
			$_SESSION["aw_errorhandler"]["last_mail"] = $mh;
			$_SESSION["aw_errorhandler"]["last_mail_time"] = time();
		}
	}
}

