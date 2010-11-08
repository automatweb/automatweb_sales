<?php

//// DEPRECATED. moved to object class' constants
// object statuses
define("STAT_DELETED", 0);
define("STAT_NOTACTIVE", 1);
define("STAT_ACTIVE", 2);
//// END DEPRECATED

class core extends acl_base
{
	var $errmsg;
	public $raise_error_exception;

	private $use_empty = false;

	/** every class that derives from core, should call this initialization method
		@attrib api=1 params=name

		@param clid optional type=int
			If set, sets the class_id of the class that inherits from core

		@comment
			Initializes databas connection

	**/
	function init($args = false)
	{
		parent::init($args);
		if (is_array($args) && isset($args["clid"]))
		{
			$this->clid = $args["clid"];
		}
	}

	/** deprecated - use config::get_simple_config instead **/
	function get_cval($ckey)
	{
		$q = sprintf("SELECT content FROM config WHERE ckey = '%s'",$ckey);
		return $this->db_fetch_field($q,"content");
	}

	/** deprecated - use config::set_simple_config instead **/
	function set_cval($ckey,$val)
	{
		$ret = $this->db_fetch_row("SELECT content FROM config WHERE ckey = '$ckey'");
		if (!is_array($ret))
		{
			// create key if it does not exist
			$this->db_query("INSERT INTO config(ckey, content, modified, modified_by) VALUES('$ckey','$val',".time().",'".aw_global_get("uid")."')");
		}
		else
		{
			$this->db_query("UPDATE config SET content = '$val', modified = '".time()."', modified_by = '".aw_global_get("uid")."' WHERE ckey = '$ckey' ");
		}
		return $val;
	}

	////
	// !Setter for object
	function set_opt($key,$val)
	{
		$this->$key = $val;
	}

	////
	// !Getter for object
	function get_opt($key)
	{
		return isset($this->$key) ? $this->$key : NULL;
	}

	/** This function writes an entry to the aw syslog table. This is automatically called for most actions, via classbase, but if you perform any special actions in your class, that should be logged, then use this function to log the action.

		@attrib api=1

		@param type required type=int
			Log entry type. The types are defined in the ini file, in the syslog.types array. The type specifies the class in most cases. The prefix for syslog type define's is ST_, for instance ST_DOCUMENT

		@param action required type=int
			Log action type. The actions are defined in the ini file, in the syslog.actions array. The action specifies, what was done - for instance, change/add/delete. The prefix for syslog actions is SA_, for instance SA_ADD

		@param text required type=string
			the text of the log message.

		@param oid optional type=int
			defaults to 0 The object id of the object this action is about.

		@param honor_ini optional type=bool
			if set to true, the disable logging ini setting will be ignored.

		@comment
			The logging can be disabled by the ini setting logging_disabled

		@errors
			none

		@returns
			none

		@examples
			$this->_log(ST_DOCUMENT, SA_ADD, "Added document $name", $docid);
	**/
	function _log($type,$action,$text,$oid = 0,$honor_ini = true, $object_name = null)
	{
		if(aw_ini_get('logging_disabled') && $honor_ini)
		{
			return;
		}

		if (empty($this->dc))
		{
			print "SYSLOG: $text\n";
		}
		else
		{
			$ip = aw_global_get("HTTP_X_FORWARDED_FOR");
			if (!inet::is_ip($ip))
			{
				$ip = aw_global_get("REMOTE_ADDR");
			}
			$t = time();
			$this->quote($text);
			$this->quote($oid);
			$this->quote($type);
			$ref = aw_global_get("HTTP_REFERER");
			$this->quote($ref);
			$session_id = session_id();
			if ($object_name === null)
			{
				$object_name = $this->db_fetch_field("SELECT name FROM objects where oid = '$oid'", "name");
			}
			$this->quote($object_name);
			$mail_id = isset($_GET["mlx"]) ? (int) $_GET["mlx"] : 0;
			$fields = array("tm","uid","type","action","ip","oid","act_id", "referer", "object_name", "session_id", "mail_id");
			$values = array($t,aw_global_get("uid"),$type,$text,$ip,(int)$oid,$action,$ref,$object_name, $session_id, $mail_id);

			if (aw_ini_get("syslog.has_site_id") == 1)
			{
				$fields[] = "site_id";
				$values[] = $this->cfg["site_id"];
			}

			if (aw_ini_get("syslog.has_lang_id") == 1)
			{
				$fields[] = "lang_id";
				$values[] = aw_global_get("lang_id");
			}

			/*
				It seems that mssql doesn't support insert delayd syntax.
				We're on the safe side as long as AWs running on
				MSSQL have used the aw.ini directive logging_disabled.
				Im sure a more permanent fix will surface one day.
			*/
			$q = sprintf("INSERT INTO syslog (%s) VALUES (%s)",join(",",$fields),join(",",map("'%s'",$values)));

			if (!$this->db_query($q))
			{
				echo "q = $q <br>";
				echo ("cannot write to syslog: " . $this->db_last_error["error_string"]);
				send_mail(
					"vead@struktuur.ee",
					"Syslog katki ".aw_ini_get("baseurl"),
					"q = $q / ".$this->db_last_error["error_string"]." \n".dbg::process_backtrace(debug_backtrace())
				);
			};
		}
	}

	/** This writes a line to log file in site directory

		@attrib api=1 name=site_log params=pos

		@param type required type=string
			Line to write into log file

		@comment
			The logging can be disabled or enabled by the ini setting site_logging. Every day has its own log file with date. Log files will be in $sitedir/files/logs directory.

		@errors
			none

		@returns
			true when the string is successfully written into log file
			false on some failure (usually permissions writing/folder existance related)

		@examples
			$this->site_log('foobar');
	**/
	function site_log($string)
	{
		if (aw_ini_get('site_log') != 1)
		{
			return false;
		}

		$site_basedir = aw_ini_get('site_basedir');
		$folder = aw_ini_get('site_basedir').'/files';
		if (!is_dir($folder) || !is_writable($folder))
		{
			return false;
		}
		$folder .= '/logs';
		if (!is_dir($folder))
		{
			mkdir($folder, 0777); // for some reason, this mode thing doesn't work, need to set permissions separately --dragut
			chmod($folder, 0777);
			if (!is_writable($folder))
			{
				return false;
			}
		}

		$filename = $folder.'/log-'.date('Y-m-d').'.log';

		$f = fopen($filename, 'a');
		flock($f, LOCK_EX);
		fwrite($f, $string."\n");
		fclose($f);

		return true;
	}

	/** Converts the given timestamp to text format.
		@attrib api=1

		@param timestamp required
			The unix timestamp to convert to text format.

		@param format optional
			The date format string identifier, from the ini file. These are defined in config.date_formats
	**/
	function time2date($timestamp = "",$format = 0)
	{
		if ($format != 0)
		{
			$dateformats = $this->cfg["config"]["dateformats"];
			$dateformat = $dateformats[$format];
		}
		else
		{
			$dateformat = $this->cfg["config"]["default_dateformat"];
		}

		return ($timestamp) ? date($dateformat,$timestamp) : date($dateformat);
	}

	/** adds a view to an object
		@attrib api=1 params=pos

		@param oid required type=oid
			The object to which to add the view

		@comments
			Writes the number of views to a table, one row per object. Can be used for extremely simplistic statistics.
	**/
	function add_hit($oid)
	{
		if ($oid)
		{
			$this->db_query("UPDATE hits SET hits=hits+1 WHERE oid = $oid");
		};
	}

	/** Signals an error condition, displays it to the user if specified, sends an e-mail and registers error if configured. All relavant variables and a backtrace are displayed. If specified, also halts execution.
		@attrib api=1

		@param err_type required
			The type of the error to throw. Error types are registered in the ini file, errors array.

		@param msg required
			The error message.

		@param fatal optional
			If true, execution is halted after the error is displayed. Defaults to false.

		@param silent optional
			If true, error is not displayed to the user. It is still sent to the list and reported to the error server. Defaults to false.

		@param oid optional
			If set, must contain the oid of the object that the error is about.

	**/
	function raise_error($err_type,$msg, $fatal = false, $silent = false, $oid = 0, $send_mail = true)
	{
		if (!function_exists("aw_global_get"))
		{
			classload("defs");
		}

		if(aw_ini_get('raise_error.no_email'))
		{
			$send_mail = false;
		}

		$GLOBALS["aw_is_error"] = 1;
		$msg = htmlentities($msg);
		if (aw_global_get("__from_raise_error") > 0)
		{
			return false;
		}
		aw_global_set("__from_raise_error",1);
		$this->errmsg[] = $msg;
		$_SESSION["aw_session_track"]["aw"]["last_error_message"] = $msg;

		$orig_msg = $msg;
		$is_rpc_call = aw_global_get("__is_rpc_call");
		$rpc_call_type = aw_global_get("__rpc_call_type");

		// $msg = "Suhtuge veateadetesse rahulikult!  Te ei ole korda saatnud midagi katastroofilist. Ilmselt juhib programm Teie t&auml;helepanu mingile ebat&auml;psusele  andmetes v&otilde;i n&auml;puveale.<br /><br />\n\n".$msg." </b>";
		$msg = $msg." </b>";

		// also attach backtrace and file/line
		if ($this->raise_error_exception instanceof awex_php_generic_error)
		{
			$msg .= "\n<br /><b>File:</b> " . $this->raise_error_exception->errfile;
			$msg .= "\n<br /><b>Line:</b> " . $this->raise_error_exception->errline;
			$msg .= "\n<br /><b>Backtrace:</b>" . str_replace("#", "\n<br /><b>#</b>", $this->raise_error_exception->getTraceAsString()); //!!! backtrace otsida ka 6ige, errori enda oma, mitte errorhandleri oma
		}
		elseif ($this->raise_error_exception instanceof Exception)
		{
			$msg .= "\n<br /><b>File:</b> " . $this->raise_error_exception->getFile();
			$msg .= "\n<br /><b>Line:</b> " . $this->raise_error_exception->getLine();
			$msg .= "\n<br /><b>Backtrace:</b>" . str_replace("#", "\n<br /><b>#</b>", $this->raise_error_exception->getTraceAsString());
		}
		elseif (function_exists("debug_backtrace"))
		{
			$msg .= dbg::process_backtrace(debug_backtrace());
		}


		// meilime veateate listi ka
		$subj = str_replace("http://", "", aw_ini_get("baseurl"));

		if (!$is_rpc_call && !headers_sent())
		{
			header("X-AW-Error: 1");
		}

		$content = "\nVeateade: " . htmlspecialchars_decode(strip_tags($msg));
		$content.= "\nKood: ".$err_type;
		$content.= "\nFatal: " . (int) $fatal;
		$content.= "\nPHP_SELF: ".aw_global_get("PHP_SELF");
		$content.= "\nlang_id: ".aw_global_get("lang_id");
		$content.= "\nuid: ".aw_global_get("uid");
		$content.= "\nsection: ".(isset($_REQUEST["section"]) ? $_REQUEST["section"] : "");
		$content.= "\nurl: " . aw_ini_get("baseurl") . aw_global_get("REQUEST_URI");
		$content.= "\nreferer: " . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "");
		$content.= "\nis_rpc_call: " . (int) $is_rpc_call;
		$content.= "\nrpc_call_type: " . $rpc_call_type;

		if (!empty($_GET["password"]))
		{
			$_GET["password"] = "***";
		}
		if (!empty($_POST["password"]))
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
		if (($uid = aw_global_get("uid")) != "")
		{
			$uso = obj(aw_global_get("uid_oid"));
			$eml = $uso->prop("email");
			if ($eml == "")
			{
				$eml = "automatweb@automatweb.com";
			}
			$head="From: $uid <".$eml.">\n";
		}
		else
		{
			$head="From: automatweb@automatweb.com\n";
		}


		if ($err_type == 30 && strpos($_SERVER["HTTP_USER_AGENT"], "Microsoft-WebDAV-MiniRedir") !== false)
		{
			$send_mail = false;
		}

		if ($err_type == 30)
		{
			if (count($_GET) < 1 && count($_POST) < 1)
			{
				$send_mail = false;
			}
		}

		if ($err_type == 83 && aw_ini_get("site_id") == 543)
		{
			$send_mail = false;
		}

		// ifthe request is too big or b0rked
		if ($err_type == 30 && $_SERVER["REQUEST_METHOD"] === "POST" && count($_POST) == 0)
		{
			$send_mail = false;
		}

		if ($err_type == 31 && substr($_REQUEST["class"], -3) === "...")
		{
			$send_mail = false;
		}

		if ($err_type == 31 && strpos($_REQUEST["class"], "%27") !== false)
		{
			$send_mail= false;
		}

		if ($err_type == 31 && strpos($_REQUEST["class"], "@") !== false)
		{
			$send_mail = false;
		}

		if ($err_type == 30 && !empty($_REQUEST["comment"]))
		{
			$send_mail = false;
		}

		if (substr(ifset($_REQUEST, "class"), 0, 4) === "http" || substr(ifset($_REQUEST, "entry_id"), 0, 4) === "http")
		{
			$send_mail = false;
		}

		if ($err_type === "ERR_ACL" && (!isset($_REQUEST["id"]) or substr($_REQUEST["id"], 0, 4) === "http"))
		{
			$send_mail = false;
		}

		if ($err_type == 110 && strpos($msg, "http:") !== false)
		{
			//die("silly robot");
			//$send_mail = false;
		}

		$tmp = $_REQUEST;
		unset($tmp["MAX_FILE_SIZE"]);
		if ($err_type == 30 && count($tmp) == 0)
		{
			$send_mail = false;
		}

		$si = __get_site_instance();
		if (is_object($si) && method_exists($si,"process_error"))
		{
			$tmp = $si->process_error(array(
				"err_type" => $err_type,
				"content" => $content
			));
			if ($tmp !== null)
			{
				$send_mail = $tmp;
			}
		}

		if (isset($_SERVER["REQUEST_METHOD"]) and $_SERVER["REQUEST_METHOD"] === "OPTIONS")
		{
			$send_mail = false;
		}

		if (isset($_SERVER["REDIRECT_REQUEST_METHOD"]) and $_SERVER["REDIRECT_REQUEST_METHOD"] === "PROPFIND")
		{
			$send_mail = false;
		}

		// if error type is class not defined and get and post are empty, the orb.aw url was requested probably, no need ot send error
		if ($err_type === "ERR_ORB_NOCLASS" && count($_GET) === 0 && count($_POST) === 0)
		{
			$send_mail = false;
		}

		if ($err_type === "ERR_IMAGE_FORMAT")
		{
			$send_mail = false;
		}

		$mh = md5($content);
		if (isset($_SESSION["last_mail"]) and $_SESSION["last_mail"] === $mh and isset($_SESSION["last_mail_time"]) and $_SESSION["last_mail_time"] > (time() - 60))
		{
			$send_mail = false;
		}

		if ($err_type == 31 && (strpos($GLOBALS["class"], "/") !== false || strpos($GLOBALS["class"], "alert") !== false || strpos($GLOBALS["class"], "&") !== false || strpos($GLOBALS["class"], "\\") !== false || strpos($GLOBALS["class"], "%") !== false || strpos($GLOBALS["class"], "§") !== false || strpos($GLOBALS["class"], "=") !== false))
		{
			die("silly robot");
		}

		// kui saidi kaustas on fail spam.txt, siis kontrollitakse enne saatmist kirja sisu failis olevate s6nade vastu; spam.txt sisu on yhes reas kujul: /viagra|v1agra|porn|foo/i
		if ( file_exists(aw_ini_get("site_basedir") . "/spam.txt") && $send_mail)
		{
			$spam = file(aw_ini_get("site_basedir") . "/spam.txt");
			if (preg_match($spam[0], $content))
			{
				$send_mail = false;
			}
		}

		if ($send_mail)
		{
			if (aw_ini_get("errors.send_to"))
			{
				$bug_receiver = aw_ini_get("errors.send_to");
			}
			else
			{
				if (aw_ini_get("client.high_priority") == 1)
				{
					$bug_receiver = "vead-top@struktuur.ee";
				}
				else
				{
					$bug_receiver = "vead@struktuur.ee";
				}

				if (strlen(aw_ini_get("bugmailcc")) > 1)
				{
					$bug_receiver .= ",".aw_ini_get("bugmailcc");
				}
			}

			send_mail($bug_receiver, $subj, $content, $head);
			$_SESSION["last_mail"] = $mh;
			$_SESSION["last_mail_time"] = time();
		}

		// here we replicate the error to the site that logs all errors (usually aw.struktuur.ee)
		// we replicate by POST request, cause this thing can be too long for a GET request

		$class = empty($_REQUEST["class"]) ? "" : $_REQUEST["class"];
		$action = empty($_REQUEST["action"]) ? "" : $_REQUEST["action"];

		//XXX: watchout, on eau the following if block had a "false &&" part in it
		//i just deleted that, but for further testing i'm writing this comment
		//so i could find the place easily
		if (!($class === "bugtrack" && $action === "add_error") && aw_ini_get("config.error_log_site"))
		{
			// kui viga tuli bugi replikeerimisel, siis 2rme satu l6pmatusse tsyklisse
			$socket = get_instance("protocols/socket");
			$socket->open(array(
				"host" => aw_ini_get("config.error_log_site"),
				"port" => 80,
			));

			$req = "class=bugtrack&action=add_error";
			$req.= "&site_url=".urlencode(aw_ini_get("baseurl"));
			$req.= "&err_type=".$err_type;
			$req.= "&err_msg=".urlencode($msg);
			$req.= "&err_uid=".aw_global_get("uid");
			$req.= "&err_content=".urlencode($content);

			$op = "POST http://".aw_ini_get("config.error_log_site")."/reforb.".aw_ini_get("ext")." HTTP/1.0\r\n";
			$op .= "Host: ".aw_ini_get("config.error_log_site")."\r\n";
			$op .= "Content-type: application/x-www-form-urlencoded\r\n";
			$op .= "Content-Length: " . strlen($req) . "\r\n\r\n";
			$socket->write($op);
			$socket->write($req);
			$socket->close();
		}

		if ($silent)
		{
			aw_global_set("__from_raise_error",0);
			return;
		}

		if ($fatal)
		{
			if ($is_rpc_call)
			{
				$driver_inst = get_instance("core/orb/".$rpc_call_type);
				$driver_inst->handle_error($err_type, $orig_msg);
				die();
			}
			else
			{
				//!!! liigutada
				//!!! liigutada p6hierrorhandlerisse, lib/errorhandling.aw-sse
				$co = get_instance("config");
				$la = get_instance("core/languages");
				$ld = $la->fetch(aw_global_get("lang_id"));
				$u = $co->get_simple_config("error_redirect_".$ld["acceptlang"]);
				if (!$u)
				{
					$u = $co->get_simple_config("error_redirect");
				}
				$seu = aw_ini_get("core.show_error_users");
				if ($seu == "")
				{
					$uid_arr = array();
				}
				else
				{
					$uid_arr = explode(",", $seu);
				}
				//!!! END liigutada

				if (automatweb::MODE_PRODUCTION === automatweb::$instance->mode())
				{
					exit;
				}
				else
				{
					flush();
					die("<br /><b>AW_ERROR: $msg</b><br />\n\n<br />");
				}
			}
		}
		aw_global_set("__from_raise_error",0);
	}

	/** Creates orb links
		@attrib api=1

		@comment
			This function is documented in the orb specification.

			the idea is this that it determines itself whether we go through the site (index.aw)
			or the orb (orb.aw) - for the admin interface
			you can force it to point to the admin interface
			this function also handles array arguments!
			crap, I hate this but I gotta do it - shoulda used array arguments or something -
			if $use_orb == 1 then the url will go through orb.aw, not index.aw - which means that it will be shown
			directly, without drawing menus and stuff
	**/
	public function mk_my_orb($fun,$arr=array(),$cl_name="",$force_admin = false,$use_orb = false,$sep = "&",$honor_r_orb = true)
	{
		// resolve to name
		// kui on numeric, siis ma saan class_lut-ist teada tema nime
		if (is_numeric($cl_name))
		{
			$fx = array_search($cl_name,$GLOBALS["cfg"]["class_lut"]);
			if (isset($GLOBALS["cfg"]["classes"][$cl_name]))
			{
				$cl_name = $GLOBALS["cfg"]["classes"][$cl_name]["file"];
			}
		}

		$cl_name = ("" == $cl_name) ? get_class($this) : basename($cl_name);

		// tracked_vars comes from orb->process_request
		$this->orb_values = isset($GLOBALS["tracked_vars"]) ? $GLOBALS["tracked_vars"] : null;

		if (!empty($arr["section"]))
		{
			$this->orb_values["section"] = $arr["section"];
		}

		if (isset($arr["_alias"]) && !empty($arr["section"]))
		{
			$this->orb_values["alias"] = $arr["_alias"];
			unset($arr["_alias"]);
		}
		else
		{
			$this->orb_values["class"] = $cl_name;
		}
		$this->orb_values["action"] = $fun;

		// figure out the request method once.
		static $r_use_orb;
		if (!isset($r_use_orb))
		{
			$r_use_orb = basename($_SERVER["SCRIPT_NAME"],".aw") === "orb";
		}

		if (!$honor_r_orb)
		{
			$r_use_orb = false;
		}

		$in_admin = isset($GLOBALS["cfg"]["in_admin"]) ? (bool) $GLOBALS["cfg"]["in_admin"] : false;

		$ru = null;
		if (isset($arr["return_url"]))
		{
			$ru = $arr["return_url"];
			unset($arr["return_url"]);
		}

		$this->process_orb_args("",$arr);
		$res = aw_ini_get("baseurl") . "/";
		if ($force_admin || $in_admin)
		{
			$res .= "automatweb/";
			$use_orb = true;
		}

		if ($use_orb || $r_use_orb)
		{
			$res .= "orb.aw";
		}

		$res .= ($sep == "/") ? "/" : "?";
		foreach($this->orb_values as $name => $value)
		{
			// lets skip the parameter only when it is empty string --dragut
			if ($value !== '')
			{
				$add = $name."=".$value.$sep;
				if(strlen($res.$add) > 2047)
				{
					$add = substr($add, 0, 2000);
				}
				$res .= $add;
			}
		}

		if ($ru !== null)
		{
			$rv = $res."return_url=".urlencode($ru).$sep;
		}
		else
		{
			$rv = substr($res,0,-strlen($sep));
		}

		$len = strlen($rv);
		if ($len > 2047)
		{
			$rv = substr($rv, 0, 2047);
		}
		return $rv;
	}

	/** creates the necessary hidden elements to put in a form that tell the orb which function to call
		@attrib api=1

		@comment
			This function is documented in the orb specification.
	**/
	function mk_reforb($fun,$arr = array(),$cl_name = "")
	{
		$cl_name = ("" == $cl_name) ? get_class($this) : basename($cl_name);

		// tracked_vars comes from orb->process_request
		$this->orb_values = isset($GLOBALS["tracked_vars"]) ? $GLOBALS["tracked_vars"] : null;
		$this->orb_values["class"] = $cl_name;
		$this->orb_values["action"] = $fun;

		if (empty($arr["no_reforb"]))
		{
			$this->orb_values["reforb"] = 1;
		}

		$this->use_empty = true;

		// flatten is not the correct term!
		$this->process_orb_args("",$arr, false);
		$res = "";
		foreach($this->orb_values as $name => $value)
		{
			$value = str_replace("\"","&amp;",$value);
			$res .= "<input type='hidden' name='$name' value='$value' />\n";
		}
		return $res;
	}

	private function process_orb_args($prefix,$arr, $enc = true)
	{
		foreach($arr as $name => $value)
		{
			if (is_array($value))
			{
				$_tpref = "" == $prefix ? $name : "[".$name."]";
				$this->process_orb_args($prefix.$_tpref,$arr[$name]);
			}
			else
			{
				// commented this out, because it breaks stuff - namely, urls that are created via
				// $this->mk_orb("admin_cell", array("id" => $this->id, "col" => (int)$arr["r_col"], "row" => (int)$arr["r_row"]))
				// where the col and row parameters will be "0"
				// it will not include them.. damned if I know why
				// so, before putting this back, check that
				// - terryf

				// 0 will get included now, "" will not. reforb sets use_empty so
				// that gets everything
				if ((isset($value) && ($value !== "")) || $this->use_empty)
				//{
					if ($enc)
					{
						$value = urlencode($value);
					}
					$this->orb_values[empty($prefix) ? $name : $prefix."[".$name."]"] = $value;
				//};
			}
		}
	}

	/** deprecated - no not use **/
	function mk_orb($fun,$arr, $cl_name = "",$user = "")
	{
		return $this->mk_my_orb($fun,$arr,$cl_name);
	}

	/** Creates a link from the $args array and returns it.

		@param args optional type=array
			An array of key => value pairs. These are inserted as key => value pairs in the result url.

		@param skip_empty optional type=bool
			If true, empty values are not inserted into the result url.
	**/
	function mk_link($args = array(),$skip_empty = false)
	{
		$retval = array();
		foreach($args as $key => $val)
		{
			if ($val)
			{
				$retval[] = "$key=$val";
			}
			elseif (!$skip_empty)
			{
				$retval[] = $key;
			};
		};
		return join("/",$retval);
	}

	////
	// !creates a list of menus above $parent and appends $text and assigns it to the correct variable
	function mk_path($oid,$text = "",$period = 0)
	{
		$path = "";
		$nt = true;
		// check if there is a default text
		$dyc = $this->prog_acl("", "default_yah_ct");

		if (true !== $dyc and strlen($dyc))
		{
			$return_url = automatweb::$request->arg("return_url");
			if ($return_url)
			{
				$text = html::href(array(
					"url" => $return_url,
					"caption" => t("Tagasi"),
				));
			}
			else
			{
				$text = $dyc;
			}
			$nt = false;
		}

		if ($this->can("view", $oid) && $nt)
		{
			$current = new object($oid);
			// path() always return an array
			$chain = array_reverse($current->path());

			if (count($chain) == 0)
			{
				$admrm = aw_ini_get("admin_rootmenu2");
				if (is_array($admrm))
				{
					$admrm = reset($admrm);
				}
				$chain = array(obj($admrm));
			}

			$set = false;

			foreach($chain as $obj)
			{
				$name = $obj->name();
				$name = empty($name) ? t("(nimetu)") : $name;

				if (!$set)
				{
					aw_global_set("site_title_path_obj_name", $name);
					$set = true;
				}

				$path = html::href(array(
					"url" => admin_if::get_link_for_obj($obj->id(),$period),
					"caption" => parse_obj_name(strip_tags($name)),
				)) . " / " . $path;
			}
		}

		if((aw_global_get("output_charset") != null) && (aw_global_get("charset") != aw_global_get("output_charset")))
		{
			$path = iconv(aw_global_get("charset"), aw_global_get("output_charset"), $path);
			$text = iconv(aw_global_get("charset"), aw_global_get("output_charset"), $text);
		}

		$GLOBALS["site_title"] = $path.$text;

		return $path;
	}

	/** Returns the contents of the given file. If file is not found, false is returned.
		@attrib api=1

		@param file required type=string
			The full path of the file whose contents must be returned. Can be http or local.

		@errors
			none

		@examples
			echo $this->get_file(array("file" => aw_ini_get("basedir")."/init.aw"));
	**/
	public static function get_file($arr)
	{
		$retval = "";
		if (empty($arr["file"]))
		{
			throw new aw_exception("No file name");
		}
		elseif (strpos($arr["file"], "http") === 0)
		{
			$url_parsed = parse_url($arr["file"]);
			$host = isset($url_parsed["host"]) ? $url_parsed["host"] : "";
			$path = isset($url_parsed["path"]) ? $url_parsed["path"] : "";
			$port = isset($url_parsed["port"]) ? $url_parsed["port"] : 80;

			if (!empty($url_parsed["query"]))
			{
				$path .= "?".$url_parsed["query"];
			}

			$out = "GET $path HTTP/1.0\r\nHost: $host\r\n\r\n";

			$fp = fsockopen($host, $port, $errno, $errstr, 30);

			fwrite($fp, $out);
			$body = false;
			while (!feof($fp))
			{
				$s = fgets($fp, 1024);
				if ( $body )
				{
					$retval .= $s;
				}
				if ( $s === "\r\n" )
				{
					$body = true;
				}
			}

		    fclose($fp);
		}
		else
		{
			if ( not(is_file($arr["file"])) || not(is_readable($arr["file"])) )
			{
				$retval = false;
			}
			else
			if (!($fh = fopen($arr["file"],"r")))
			{
				$retval = false;
			}
			else
			{
				if (($fs = filesize($arr["file"])) > 0)
				{
					$retval = fread($fh,$fs); // SLURP
				}
				fclose($fh);
			}
		}
		return $retval;
	}

	/** Writes a file.
		@attrib api=1

		@param file required type=string
			The full path of the file to write to.

		@param content requires type=string
			The content of the file.

		@errors
			error is thrown if no file is given or file cannot be written to

		@returns
			true if file was written

		@examplex
			$this->put_file(array(
				"file" => "/www/foo",
				"content" => "allah"
			));
	**/
	public static function put_file($arr)
	{
		if (not($arr["file"]))
		{
			throw new aw_exception("No file name");
		}

		$file = $arr["file"];


		// jama on selles, et "w" modes avamine truncateb olemasoleva faili,
		// ja sellest voib tekkida jamasid... see, et mitu inimest korraga sama
		// faili kirjutavad, peaks olema suht v&auml;ikese t&otilde;en&auml;osusega s&uuml;ndmus, sest
		// &uuml;ldjuhul me kasutame random nimedega faile.
		// "b" is for os-indepence, winblowsil on huvitav omadus isiklikke reavahetusi kasutada
		if (not(($fh = fopen($file,"wb"))))
		{
			throw new aw_exception("File open failed");
		}
		else
		{
			fwrite($fh, $arr["content"]);
			fclose($fh);
		}
		// actually this should return a boolean value and an error message should
		// be stored somewhere inside the class.
		return true;
	}

	/** Retrieves a list of files in a directory
		@attrib api=1

		@param dir required type=string
			The directory on the server whose contents to return.

		@errors
			none
	**/
	function get_directory($args = array())
	{
		$files = array();

		if (empty($args["dir"]))
		{
			return $files;
		}
		else
		{
			$dir = $args["dir"];
		}

		// Directory Handle
		if (is_dir($dir) and $DH = opendir($dir))
		{
			while (false !== ($file = readdir($DH)))
			{
				$fn = $dir . "/" . $file;
				if (is_file($fn))
				{
					$files[$file] = $file;
				}
			}
			closedir($DH);
		}

		return $files;
	}

	/** converts all characters of string $str to their hex representation and returns the resulting string
		@attrib api=1

		@param str required type=string
			The string to convert.

		@errors
			none

		@returns
			the given string, in hex character codes

		@examples
			echo $this->binhex("abx"); // echos 616263
			echo $this->hexbin($this->binhex("abc"));	// echos abc
	**/
	function binhex($str)
	{
		$l = strlen($str);
		$ret = "";
		for ($i=0; $i < $l; $i++)
		{
			$v = ord($str[$i]);
			if ($v < 16)
			{
				$ret.= "0".dechex($v);
			}
			else
			{
				$ret.= dechex($v);
			};
		}
		return $ret;
	}

	/** opposite of binhex, decodes a string of hex numbers to their values and creates a string from them
		@attrib api=1

		@param str required type=string
			The string to convert.

		@errors
			none

		@returns
			the given string, converted back to the original text

		@examples
			echo $this->binhex("abx"); // echos 616263
			echo $this->hexbin($this->binhex("abc"));	// echos abc
	**/
	function hexbin($str)
	{
		$l = strlen($str);
		$ret = "";
		for ($i=0; $i < $l; $i+=2)
		{
			$ret.= chr(hexdec($str[$i].$str[$i+1]));
		};
		return $ret;
	}

	////
	// !loads localization constans and imports them to the current class, vars are assumed to be in array $arr_name
	function lc_load($file, $arr_name, $lang_id = "")
	{
		if (empty($lang_id))
		{
			$admin_lang_lc = aw_global_get("admin_lang_lc");
		}
		else
		{
			$admin_lang_lc = $lang_id;
		}

		if (!$admin_lang_lc)
		{
			$admin_lang_lc = "et"; //!!! kust saab defaulti
		}

		// for better debugging
		$fullpath = AW_DIR."lang/{$admin_lang_lc}/{$file}".AW_FILE_EXT;
		if (!is_readable($fullpath))
		{
			throw new aw_exception("Locale file '{$fullpath}' not readable.");
		}

		require_once($fullpath);

		if (is_array($GLOBALS[$arr_name]))
		{
			$this->vars($GLOBALS[$arr_name]);
		}
	}

	function _get_menu_list_cb($o, $param)
	{
		$this->tt[$o->id()] = $o->path_str();
	}

	////
	// !teeb objektide nimekirja ja tagastab selle arrays, sobiv picker() funxioonile ette andmisex
	// ignore_langmenus = kui sait on mitme keelne ja on const.aw sees on $lang_menus = true kribatud
	// siis kui see parameeter on false siis loetaxe aint aktiivse kelle menyyd

	// empty - kui see on true, siis pannaxe k6ige esimesex arrays tyhi element
	// (see on muiltiple select boxide jaoks abix)

	// rootobj - mis objektist alustame
	function get_menu_list($ignore_langmenus = false,$empty = false,$rootobj = -1, $onlyact = -1, $make_path = true)
	{
		enter_function("core::get_menu_list");

		if ($rootobj == -1)
		{
			$rootobj = cfg_get_admin_rootmenu2();
		}

		$ot = new object_tree(array(
			"class_id" => CL_MENU,
			"parent" => $rootobj,
			"status" => ($onlyact ? object::STAT_ACTIVE : array(object::STAT_NOTACTIVE, object::STAT_ACTIVE)),
			"sort_by" => "objects.parent",
			"lang_id" => array(),
			"site_id" => array(),
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"lang_id" => aw_global_get("lang_id"),
					"type" => MN_CLIENT
				)
			)),
			"sort_by" => "objects.parent, objects.jrk"
		));


		$this->tt = array();
		if ($empty)
		{
			$this->tt[] = "";
		}

		if ($make_path)
		{
			$ot->foreach_cb(array(
				"save" => false,
				"func" => array($this, "_get_menu_list_cb"),
				"param" => ""
			));
		}
		else
		{
			$_tmp = $ot->to_list();
			$_tmp2 = $_tmp->names();
			if (is_array($_tmp2))
			{
				$this->tt = $_tmp2;
			};
		};

		exit_function("core::get_menu_list");
		return $this->tt;
	}

	/** executes an orb function call and returns the data that the function returns

		@attrib api=1

		@param action required type=string
			orb action to exec

		@param class optional type=string
			class for the action - default the current class

		@param params optional type=array
			params to the action

		@param method optional type=string
			the method to use when doing the function call - possible values: local / xmlrpc / (soap - not implemented yet)

		@param server optional type=string
			if doing a rpc call, the server where to connect

		@param login_obj optional type=int
			if we must log in to a server the id of the CL_AW_LOGIN that will be used to login to the server. if this is set, then server will be ignored

		@comment
			Further information can be found in the orb specification
	**/
	function do_orb_method_call($arr)
	{
		extract($arr);

		if (!$arr["class"])
		{
			$arr["class"] = get_class($this);
		}

		$ob = get_instance("core/orb/orb");
		return $ob->do_method_call($arr);
	}

	/** this takes an array and goes through it and makes another array that has as keys the values of the given array and also the velues of the given array
		@attrib api=1

		@param arr required type=array
			The array to convert.

		@examples
			$arr = array("a", "b", "c");
			echo dbg::dump($arr);
			// echos:
			array(3) {
			  [0]=>
			  string(1) "a"
			  [1]=>
			  string(1) "b"
			  [2]=>
			  string(1) "c"
			}
	**/
	function make_keys($arr)
	{
		$ret = array();
		if (is_array($arr))
		{
			foreach($arr as $v)
			{
				$ret[$v] = $v;
			}
		}
		return $ret;
	}

	function parse_alias($args = array())
	{
		if (isset($args["alias"]['target']))
		{
			return $this->show(array('id' => $args["alias"]['target']));
		}
		else
		{
			return '';
		}
	}

	function to_ent($data)
	{
		$chars = array(
			"Ü" => "&Uuml;",
			"ü" => "&uuml;",
			"Õ" => "&Otilde;",
			"õ" => "&otilde;",
			"Ö" => "&Ouml;",
			"ö" => "&ouml;",
			"Ä" => "&Auml;",
			"ä" => "&auml;"
		);
		if(!is_array($data))
		{
			foreach($chars as $char => $ent)
			{
				$data = str_replace($char, $ent, $data);
			}
		}
		else
		{
			foreach($data as $key => $val)
			{
				foreach($chars as $char => $ent)
				{
					$data = str_replace($char, $ent, $data);
				}
			}
		}
		return $data;
	}

	function show($args)
	{
		$obj = obj($args['id']);
		return $obj->name();
	}
}
