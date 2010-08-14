<?php

/*
@classinfo maintainer=voldemar
*/
class aw_request
{
	// const DEFAULT_CLASS = "admin_if";
	// const DEFAULT_ACTION = "change";

	private $_args = array(); // Request parameters. Associative array of argument name/value pairs. read-only
	private $_class; // requested class. aw_class.class_name
	private $_action; // requested class action. one of aw_class.actions
	private $_is_fastcall = false; // boolean
	private $_application; // object
	private static $_application_classes = array( //!!! tmp. teha n2iteks interface-ga. implements application
		"crm_sales",
		"crm_company",
		"realestate_manager",
		"mrp_workspace",
		"admin_if",//!!! esialgu tekitab tyli, vaja m6elda kuidas mitme aknaga s2ilitada aplikatsiooni. variandid window id, klassi aplikatsioonide nimekirjad ja kui mitu siis kysitakse kasutajalt
		// "aw_object_search",///!!!! vt admin_if
		"bug_tracker",
		"events_manager"
	);
	private $_protocol; // protocol object

	public function __construct($autoload = false)
	{
		if ($autoload)
		{
			// load current/active request
			$this->parse_args();
		}
	}

	/** Determines request type, arguments and loads them returning the specific request object
	@attrib api=1 params=pos
	@returns aw_request object
	**/
	public static function autoload()
	{
		// determine request type and create instance
		if (!empty($_SERVER["SERVER_PROTOCOL"]) and substr_count(strtolower($_SERVER["SERVER_PROTOCOL"]), "http") > 0)
		{ //!!! check if SERVER_PROTOCOL always set and 'http' when http request. A rumoured case that empty when https on some specific server/machine.
			$request = new aw_http_request(true);
		}
		else
		{
			$request = new aw_request(true);
		}

		return $request;
	}

	/**
	@attrib api=1 params=pos
	@param name required type=string
		Argument name to get value for.
	@returns var
		Request argument value or NULL if argument not defined
	**/
	public function arg($name)
	{
		if (isset($this->_args[$name]))
		{
			return $this->_args[$name];
		}
		else
		{
			return null;
		}
	}

	/**
	@attrib api=1 params=pos
	@param name required type=string
		Argument name to find.
	@returns bool
	**/
	public function arg_isset($name)
	{
		return isset($this->_args[$name]);
	}

	/**
	@attrib api=1 params=pos
	@returns array
		Request arguments. argument_name => argument_value pairs
	**/
	public function get_args()
	{
		return $this->_args;
	}

	/**
	@attrib api=1 params=pos
	@param arg required type=string,array
		Arguments to set. String argument name or associative array of argument name/value pairs or array of argument names to set value for.
	@param val optional type=mixed
		Argument value. If $arg is array, then it is handled as array of argument names and this value will be set for all of those. Required in that case and when $arg is argument name.
	@returns void
	@comment
		Sets argument values.
	@throws
		awex_request_param when $arg parameter is not valid.
		awex_request_param with code 2 when $arg parameter is string argument name and no second argument given.
	**/
	public function set_arg($arg)
	{
		$sa = (2 === func_num_args());

		if (is_array($arg) and count($arg))
		{
			if ($sa)
			{
				$args = array();
				$val = func_get_arg(1);
				foreach ($arg as $name)
				{
					if (!is_string($name) or !strlen($name))
					{
						throw new awex_request_param("Invalid parameter value '" . var_export($name, true) . "'. Argument name expected.");
					}

					$args[$name] = $val;
				}

				$this->_args = $args + $this->_args;
			}
			else
			{
				$this->_args = $arg + $this->_args;
			}
		}
		elseif (is_string($arg) and strlen($arg))
		{
			if ($sa)
			{
				$this->_args[$arg] = func_get_arg(1);
			}
			else
			{
				throw new awex_request_param("No value specified to set '{$arg}'.", 2);
			}
		}
		else
		{
			throw new awex_request_param("Invalid parameter value '" . var_export($arg, true) . "'. Array or argument name expected.");
		}

		$this->parse_args();
	}

	/**
	@attrib api=1 params=pos
	@param name optional type=string,array
		Name(s) of request argument(s) to unset.
	@returns array
		Argument names that weren't set in the first place.
	@comment
		Unsets request argument(s). If no arguments given, unsets all request arguments.
	**/
	public function unset_arg()
	{
		$not_found_args = array();
		if (func_num_args())
		{
			$name = func_get_arg(0);

			if (is_array($name))
			{
				foreach ($name as $arg)
				{
					if (isset($this->_args[$arg]))
					{
						unset($this->_args[$arg]);
					}
					else
					{
						$not_found_args[] = $arg;
					}
				}
			}
			else
			{
				if (!isset($this->_args[$name]))
				{
					unset($this->_args[$name]);
				}
				else
				{
					$not_found_args[] = $name;
				}
			}
		}
		else
		{
			$this->_args = array();
		}

		$this->parse_args();
	}

	/**
	@attrib api=1 params=pos
	@returns object
		Currently active application object
	**/
	public function get_application()
	{
		if (!is_object($this->_application))
		{
			if (in_array($this->_class, self::$_application_classes)) //!!! tmp solution
			{
				if (isset($this->_args["id"]) and is_oid($this->_args["id"]))
				{
					$application = new object($this->_args["id"]);
					aw_session_set("aw_request_application_object_oid", $application->id());
				}
				elseif ("admin_if" === $this->_class)
				{
					$core = new core();
					$id = admin_if::find_admin_if_id();
					$application = new object($id);
					aw_session_set("aw_request_application_object_oid", $application->id());
				}
				elseif (aw_ini_isset("class_lut." . $this->_class))
				{
					$clid = aw_ini_get("class_lut." . $this->_class);
					$application = obj(null, array(), $clid);
				}
				else
				{
					$application = new object(); //!!! mis on default?
				}
			}
			elseif (is_oid(aw_global_get("aw_request_application_object_oid")))
			{
				$application = new object(aw_global_get("aw_request_application_object_oid"));
			}
			else
			{
				$application = new object(); //!!! mis on default?
			}

			$this->_application = $application;
		}

		return $this->_application;
	}

	/**
	@attrib api=1 params=pos
	@returns boolean
	**/
	public function is_fastcall()
	{
		return $this->_is_fastcall;
	}

	/** Current request protocol
	@attrib api=1 params=pos
	@returns object
	**/
	public function protocol()
	{
		return $this->_protocol;
	}

	public function type() // DEPRECATED
	{ return get_class($this) === "aw_http_request" ? "http" : "";	}

	/**
	@attrib api=1 params=pos
	@returns string
		Requested class name
	**/
	public function class_name()
	{
		return $this->_class;
	}

	/**
	@attrib api=1 params=pos
	@returns string
		Requested class action/public method name
	**/
	public function action()
	{
		return $this->_action;
	}

	private function _set_args($args)
	{
		if (!is_array($args))
		{
			throw new awex_request_param("Invalid type. Arguments are an array");
		}

		$this->_args = $args;
	}

	private function _set_protocol($protocol)
	{
		if (!is_object($protocol))
		{
			throw new awex_request_param("Invalid argument. Protocol must be an object");
		}

		if (!in_array("protocol_interface", class_implements($protocol)))
		{
			throw new awex_request_param("Invalid argument. Protocol object must have protocol interface");
		}

		$this->_protocol = $protocol;
	}

	public function parse_args()
	{ //!!! "restore previous application" on vaja ka teaostada, sest n2iteks kui k2iakse teises applicationis ja minnakse tagasi eelmisest avatud allobjektile, on application vale
		if (!empty($this->_args["fastcall"]))
		{
			$this->_is_fastcall = true;
			aw_global_set("no_db_connection", 1);
		}
		// no name validation because requests can be formed and sent to other servers where different classes, methods, etc. defined
		// $this->_class = empty($this->_args["class"]) ? self::DEFAULT_CLASS : $this->_args["class"];
		// $this->_action = empty($this->_args["action"]) ? self::DEFAULT_ACTION : $this->_args["action"];
		$this->_class = empty($this->_args["class"]) ? "" : $this->_args["class"];
		$this->_action = empty($this->_args["action"]) ? "" : $this->_args["action"];
	}

	public function __isset($name)
	{
		$name = "_{$name}";
		return isset($this->$name);
	}

	public function __get($name)
	{
		$name = "_{$name}";
		return $this->$name;
	}

	public function __set($name, $value)
	{
		$setter_name = "_set_{$name}";
		if (method_exists($this, $setter_name))
		{
			$this->$setter_name($value);
		}
	}

	public function __unset($name)
	{
		$unsetter_name = "_unset_{$name}";
		if (method_exists($this, $unsetter_name))
		{
			$this->$unsetter_name();
		}
	}
}

/** Generic aw_request class unexpected condition indicator **/
class awex_request extends aw_exception {}

/** Requested entity not available **/
class awex_request_na extends awex_request {}

/** Invalid parameter **/
class awex_request_param extends awex_request {}

?>
