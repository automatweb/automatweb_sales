<?php

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
	// elseif (isset(automatweb::$instance) and automatweb::MODE_DBG_CONSOLE === automatweb::$instance->mode())
	// {
	// }
	else
	{
		echo "<hr/>\n";
		$tmp = '';
		ob_start();
		print_r($arr);
		$tmp = ob_get_contents();
		ob_end_clean();
		echo "<pre style=\"text-align: left;\">\n" . ($see_html ? htmlspecialchars($tmp) : $tmp) . "</pre>\n<hr/>";
	}

	if ($die)
	{
		exit;
	}
}

/** Debug helpers **/
class dbg
{
	/** dumps the parameter to a human-readable string and returns the string
		@attrib api=1 params=pos

		@param data required type=mixed
			The value to dump

		@returns
			string with human-readable representation of the given value. Useful for debugging. Wrapper for var_dump()

		@examples
			$s = array(1, 2);
			echo dbg::dump($s);
	**/
	static function dump($data)
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

	/** formats a given backtrace to a human-readable format
		@attrib api=1 params=pos

		@param bt type=array
			The backtrace data to process

		@param skip type=int default=-1
			Number of levels to skip from the end

		@param show_long_args type=bool default=FALSE
			Truncate long argument values or not

		@param html type=bool default=TRUE
			Html or plain text output

		@param last_call_first type=bool default=FALSE
			Calls order, if true, last call info shown first

		@returns
			html/text with the backtrace formatted to usr-readable format

		@examples
			echo process_backtrace(debug_backtrace());
	**/
	public static function process_backtrace($bt, $skip = -1, $show_long_args = false, $html = true, $last_call_first = false)
	{
		$msg = array();
		for ($i = count($bt)-1; $i > $skip; $i--)
		{
			$msg_item = "";
			if (!empty($bt[$i+1]["class"]))
			{
				$fnm = "method <b>".$bt[$i+1]["class"]."::".$bt[$i+1]["function"]."</b>";
			}
			elseif (!empty($bt[$i+1]["function"]))
			{
				$fnm = "function <b>".$bt[$i+1]["function"]."</b>";
			}
			else
			{
				$fnm = "file ".$bt[$i]["file"];
			}

			$line = isset($bt[$i]["line"]) ? $bt[$i]["line"] : "(unknown)";
			$msg_item .= $fnm." on line {$line} called <br>\n";

			if (!empty($bt[$i]["class"]))
			{
				$fnm2 = "method <b>".$bt[$i]["class"]."::".$bt[$i]["function"]."</b>";
			}
			elseif (!empty($bt[$i]["function"]))
			{
				$fnm2 = "function <b>".$bt[$i]["function"]."</b>";
			}
			else
			{
				$fnm2 = "file ".$bt[$i]["file"];
			}

			$msg_item .= $fnm2." with arguments ";

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
			$str = $html ? htmlentities(implode(",", $str)) : implode(",", $str);
			$msg_item .= "<font size=\"-1\">({$str}) file: {$file}</font>";
			$msg_item .= " <br><br>\n\n";
			$msg[] = $msg_item;
		}

		if ($last_call_first)
		{
			$msg = array_reverse($msg);
		}

		if ($html)
		{
			$msg = implode("", $msg);
			$msg = "<br><br> Backtrace: \n\n<br><br>{$msg}";
		}
		else
		{
			$msg = implode("", $msg);
			$msg = strip_tags($msg);
			$msg = "Backtrace:\n\n{$msg}";
		}

		return $msg;
	}

	/** Prints/returns html formatted detailed debug backtrace to client
		@attrib api=1 params=pos
		@param return type=bool default=FALSE
			echo or return
		@param html type=bool default=TRUE
			html or plain text
		@returns void
	**/
	public static function bt($return = false, $html = true)
	{
		$bt = self::process_backtrace(debug_backtrace(), -1, true, $html, true);
		if ($return)
		{
			return $bt;
		}
		else
		{
			echo $bt;
		}
	}

	/** Prints html formatted detailed debug backtrace to client and terminates whole request (php exit)
		@attrib api=1 params=pos
		@returns void
	**/
	public static function btx()
	{
		exit (self::process_backtrace(debug_backtrace(), -1, true, true, true));
	}

	/** formats a one-line user-readable string from the current backtrace
		@attrib api=1

		@returns
			One-line string with a human-readable backtrace
		@comment
			Excludes all include construct calls
	**/
	public static function sbt()
	{
		$msg = array();
		if (function_exists("debug_backtrace"))
		{
			$include_constructs = array("include", "include_once", "require", "require_once");
			$bt = debug_backtrace();
			for ($i = count($bt); $i >= 0; $i--)
			{
				if (!empty($bt[$i]))
				{
					$fnm = "";
					if (isset($bt[$i+1]))
					{
						if (!empty($bt[$i+1]["class"]))
						{
							$fnm = $bt[$i+1]["class"]."::".$bt[$i+1]["function"];
						}
						elseif (!empty($bt[$i+1]["function"]) and !in_array($bt[$i+1]["function"], $include_constructs))
						{
							$fnm = $bt[$i+1]["function"];
						}
					}

					if ($fnm)
					{
						$msg[] = $fnm . (isset($bt[$i]["line"]) ? (":" . $bt[$i]["line"]) : "");
					}
				}
			}
		}

		return implode(" > ", $msg);
	}

	/** prints the results of a database query
		@attrib api=1 params=pos

		@param q type=string
			The sql query to perform
	**/
	public static function q($q)
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

	/** Prints message followed by variable dump of $value in html, inserts a colon between and quotes around value
		@attrib api=1 params=pos
		@param value type=mixed default=NULL
			Value to print. If message not specified, multiline detailed info printed
		@param msg type=string default=""
			Message to describe event/value. If message isn't specified then info about the variable $value is displayed instead
		@param level type=int default=0
			Output indenting level. Has no meaning if $msg not specified
		@returns void
		@errors none
	**/
	public static function d($value = null, $msg = "", $level = 0)
	{
		if ($msg)
		{
			echo str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;", $level) . $msg .": '". var_export($value, true) . "'<br />\n";
		}
		else
		{
			ob_start();
			debug_zval_dump($value);
			$msg = ob_get_clean();
			echo nl2br(str_replace(" ", "&nbsp;", $msg)) . "<br />\n<br />\n";
		}
		flush();
	}

	/** Prints and exits. See method d() documentation
		@attrib api=1 params=pos
		@param value type=mixed default=NULL
		@param msg type=string default=""
		@param level type=int default=0
		@returns void
		@errors none
	**/
	public static function dx($value = null, $msg = "", $level = 0)
	{
		self::d($value, $msg, $level);
		exit;
	}

	public static function log($msg = "-")
	{
		static $logfile = false;
		if (!$logfile)
		{
			$logfile = fopen(AW_DIR . "files/debug.log", "a");
		}
		$msg = date("M d Y H:i:s") . "\t" .  get_caller() . "\t" . $msg . "\n";
		fwrite($logfile, $msg);
	}
}

