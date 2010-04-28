<?php

namespace automatweb;

/** Debug print variable values
	@attrib api=1 params=pos

	@param arr required type=mixed
		The value to output

	@param die optional type=bool
		If set to true, script execution is stopped after outputting the value, defaults to false

	@param see_html optional type=bool
		If set to true, the value displayed is fed through htmlspecialchars, so you can see the html tags in yer browser

	@comment
		Use this to output any value to the user in a pretty way, basically wraps print_r. The value is printed directly to the browser, not returned. Does nothing when in automatweb::MODE_PRODUCTION mode

**/
function arr($arr, $die=false, $see_html=false)
{
	if (isset(automatweb::$instance) and automatweb::MODE_PRODUCTION === automatweb::$instance->mode())
	{
		return;
	}

	echo "<hr/>\n";
	$tmp = '';
	ob_start();
	print_r($arr);
	$tmp = ob_get_contents();
	ob_end_clean();
	echo "<pre style=\"text-align: left;\">\n" . ($see_html ? htmlspecialchars($tmp) : $tmp) . "</pre>\n<hr/>";

	if ($die)
	{
		exit;
	}
}

// DEPRECATED
class debug
{
	/**
		@attrib name=syntaxcheck
	**/
	public static function syntaxcheck()
	{
		// include all the class files
		$clf = aw_ini_get("classdir");
		self::_req_inc($clf);
	}

	private static function _req_inc($fld)
	{
		if ($dir = opendir($fld))
		{
			while (($file = readdir($dir)) !== false)
			{
				if (!($file == "." || $file == ".." || $file == "CVS" || $file == "fastcall_base.aw" || $file == "contact.aw" || $file == "pop3.aw" || $file == "translation.aw"))
				{
					if (!preg_match("/\.\#/",$file))
					{
						$fn = $fld."/".$file;
						if (is_dir($fn))
						{
							echo "recursing into $fn <br />\n";
							self::_req_inc($fn);
						}
						else
						{
							echo "including $fn <br />\n";
							include_once($fn);
						}
					}
				}
			}
			closedir($dir);
		}
	}
}
// END DEPRECATED

/**
debugging helper methods
**/
class dbg
{
	/** dumps the parameter to a human-readable (x)html string and returns the string
		@attrib api=1 params=pos

		@param data required type=mixed
			The value to dump

		@returns
			string with human-readable representation of the given value. Useful for debugging. Wrapper for var_dump()

		@examples
			$s = array(1, 2);
			echo dbg::dump($s);
	**/
	public static function dump($data)
	{
		ob_start();
		print "<pre>";
		var_dump($data);
		print "</pre>";
		$ret = ob_get_contents();
		ob_end_clean();
		return $ret;
	}

	/** prints detailed information about string contents
		@attrib api=1 params=pos

		@param str required type=string
			The string to dump

		@comment
			Echoes the full string and then for each character, it's character code and position in the string.
			Useful for debugging character set problems.
	**/
	static function str_dbg($str)
	{
		echo "str = $str <br>";
		for($i = 0; $i < strlen($str); $i++)
		{
			echo "at pos $i: ".$str{$i}." nr = ".ord($str{$i})." <br>";
		}
		echo "---<br>";
	}

	/** prints the given message to the user, if $GLOBALS["DEBUG"] is set
		@attrib api=1 params=pos

		@param msg required type=string
			The message to print
	**/
	static function p($msg)
	{
		if (aw_global_get("DEBUG") == 1)
		{
			echo $msg."<br />\n";
		}
	}

	/** prints the given message to the user, if a cookie with the name debug1 is set
		@attrib api=1 params=pos

		@param msg required type=string
			The message to print

		@comment
			Useful for printing debug data, so that just you can see it. cookiemonster class can be used for setting cookies
	**/
	static function p1($msg)
	{
		if (!empty($_COOKIE["debug1"]))
		{
			arr($msg);
		}
	}

	/** prints the given message to the user, if a cookie with the name debug2 is set
		@attrib api=1 params=pos

		@param msg required type=string
			The message to print

		@comment
			Useful for printing debug data, so that just you can see it. cookiemonster class can be used for setting cookies
	**/
	static function p2($msg)
	{
		if (!empty($_COOKIE["debug2"]))
		{
			echo $msg."<br />\n";
		}
	}

	/** prints the given message to the user, if a cookie with the name debug3 is set
		@attrib api=1 params=pos

		@param msg required type=string
			The message to print

		@comment
			Useful for printing debug data, so that just you can see it. cookiemonster class can be used for setting cookies
	**/
	static function p3($msg)
	{
		if (!empty($_COOKIE["debug3"]))
		{
			echo $msg."<br />\n";
		}
	}

	/** prints the given message to the user, if a cookie with the name debug4 is set
		@attrib api=1 params=pos

		@param msg required type=string
			The message to print

		@comment
			Useful for printing debug data, so that just you can see it. cookiemonster class can be used for setting cookies
	**/
	static function p4($msg)
	{
		if (!empty($_COOKIE["debug4"]))
		{
			echo $msg."<br />\n";
		}
	}

	/** prints the given message to the user, if a cookie with the name debug5 is set
		@attrib api=1 params=pos

		@param msg required type=string
			The message to print

		@comment
			Useful for printing debug data, so that just you can see it. cookiemonster class can be used for setting cookies
	**/
	static function p5($msg)
	{
		if (!empty($_COOKIE["debug5"]))
		{
			print "<pre>";
			var_dump($msg);
			print "</pre>";
		}
	}

	/** formats a given backtrace to a human-readable (x)html format
		@attrib api=1 params=pos

		@param bt required type=array
			The backtrace data to process

		@param skip type=int default=-1
			Number of levels to skip from the end

		@param show_long_args type=bool default=false
			Truncate long argument values or not

		@returns
			html with the backtrace formatted to usr-readable format

		@examples
			echo process_backtrace(debug_backtrace());
	**/
	public static function process_backtrace($bt, $skip = -1, $show_long_args = false)
	{
		$msg = "<h1>Backtrace:</h1>\n";
		for ($i = count($bt)-1; $i > $skip; $i--)
		{
			$msg .= "<p style=\"font-family: Verdana, Arial, Helvetica, sans-serif;\">\n<big>* </big>";
			if (!empty($bt[$i+1]["class"]))
			{
				$fnm = "method <code>".$bt[$i+1]["class"]."::".$bt[$i+1]["function"]."</code>";
			}
			elseif (!empty($bt[$i+1]["function"]))
			{
				$fnm = "function <code>".$bt[$i+1]["function"]."</code>";
			}
			else
			{
				$fnm = "file <code>".$bt[$i]["file"] . "</code>";
			}

			$line = isset($bt[$i]["line"]) ? $bt[$i]["line"] : "(unknown)";
			$msg .= $fnm." on line {$line} called <br />\n";

			if (!empty($bt[$i]["class"]))
			{
				$fnm2 = "method <code>".$bt[$i]["class"]."::".$bt[$i]["function"]."</code>";
			}
			elseif (!empty($bt[$i]["function"]))
			{
				$fnm2 = "function <code>".$bt[$i]["function"]."</code>";
			}
			else
			{
				$fnm2 = "file ".$bt[$i]["file"];
			}

			$msg .= $fnm2." with arguments ";

			$awa = new aw_array($bt[$i]["args"]);
			$str = array();
			foreach($awa->get() as $e)
			{
				if (is_object($e))
				{
					if ($show_long_args)
					{
						$e = var_export($e, true);
					}
					else
					{
						$e = "Object ".get_class($e);
					}

					$str[] = $e;
				}
				elseif(is_array($e))
				{
					if ($show_long_args)
					{
						$e = var_export($e, true);
					}
					else
					{
						$e = "Array (".count($e).")";
					}

					$str[] = $e;
				}
				else
				{
					if (!$show_long_args and strlen($e) > 200)
					{
						$e = substr($e, 0, 100)."...".substr($e, -100);
					}
					$str[] = "".$e;
				}
			}

			$file = isset($bt[$i]["file"]) ? $bt[$i]["file"] : "(unknown)";
			$msg .= "<small>(".htmlentities(join(",", $str)).") file: {$file}</small>";
			$msg .= "</p>\n\n";
		}
		return $msg;
	}

	/** Prints the current backtrace in human readable xhtml format
		@attrib api=1
		@returns void
	**/
	public static function bt()
	{
		if (automatweb::MODE_PRODUCTION !== automatweb::$instance->mode())
		{
			echo self::process_backtrace(debug_backtrace(), 0);
		}
	}

	/** Returns the file name and line of last caller in stack
		@attrib api=1
		@returns string
	**/
	public static function call_point_str()
	{
		$bt = debug_backtrace();
		$str = $bt[1]["file"] . " : " . $bt[1]["line"];
		return $str;
	}

	/** formats a one-line user-readable string from the current backtrace
		@attrib api=1

		@returns
			One-line string with a human-readable backtrace
	**/
	static function short_backtrace()
	{
		$msg = "";
		if (function_exists("debug_backtrace"))
		{
			$bt = debug_backtrace();
			for ($i = count($bt); $i >= 0; $i--)
			{
				$fnm = "";

				if (isset($bt[$i+1]))
				{
					if (!empty($bt[$i+1]["class"]))
					{
						$fnm = $bt[$i+1]["class"]."::".$bt[$i+1]["function"];
					}
					elseif (!empty($bt[$i+1]["function"]) and ($bt[$i+1]["function"] !== "include"))
					{
						$fnm = $bt[$i+1]["function"];
					}
				}

				if (isset($bt[$i]))
				{
				}

				$msg .= $fnm . (isset($bt[$i]["line"]) ? (":" . $bt[$i]["line"]) : "") ."->";
			}
		}

		return $msg;
	}

	/** prints the results of a database query
		@attrib api=1 params=pos

		@param q required type=string
			The sql query to perform
	**/
	static function q($q)
	{
		$first = true;
		$db = new db_connector();
		$db->init();
		$db->db_query($q);
		while ($row = $db->db_next())
		{
			echo "********** Row nr ".++$cnt." *****************\n";
			foreach($row as $k => $v)
			{
				echo "$k: $v\n";
			}
		}
		echo "\n";
	}

	/** Checks php syntax for all aw class files in automatweb classes directory
		@attrib name=syntaxcheck
	**/
	public static function syntaxcheck()
	{
		// include all the class files
		$clf = aw_ini_get("classdir");
		self::_req_inc($clf);
	}

	private static function _req_inc($fld)
	{
		if ($dir = opendir($fld))
		{
			while (($file = readdir($dir)) !== false)
			{
				if (
					AW_FILE_EXT === substr($file, -strlen(AW_FILE_EXT)) and
					!($file === "fastcall_base.aw" || $file === "contact.aw" || $file === "pop3.aw" || $file === "translation.aw") and
					!preg_match("/\.\#/",$file) // exclude CVS backup files
				)
				{
					$fn = $fld."/".$file;
					if (is_dir($fn))
					{
						echo "recursing into $fn <br />\n";
						self::_req_inc($fn);
					}
					else
					{
						echo "including $fn <br />\n";
						include_once($fn);
					}
				}
			}
			closedir($dir);
		}
	}
}


?>
