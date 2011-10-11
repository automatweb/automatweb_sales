<?php
/*

Class for parsing, editing and constructing URI-s. Type conversion to string is automatic. Currently supports only URL-s.

@examples
$uri = new aw_uri("www.com.net/dev/main.aw?foo=bar");
$uri->set_arg("foo", "foobar");
echo $uri;

Outputs:
www.com.net/dev/main.aw?foo=foobar

*/

class aw_uri
{
	// uri parts
	private $scheme = "";
	private $host = "";
	private $port;
	private $user = "";
	private $pass = "";
	private $path = "";
	private $fragment = "";
	private $args = array(); // values are internally stored urldecoded
	private $string;

	// uri character classes
	public $reserved_chars = array(";", "/", "?", ":", "@",  "&", "=", "+", ",", '$');

	// ...
	protected $updated = false;

	public function __construct($uri = null)
	{
		if (isset($uri))
		{
			$this->set($uri);
		}
	}

	/**
	@attrib api=1
	@returns string
		Returns uri as string
	**/
	public function get()
	{
		if (!$this->updated)
		{
			$this->update_string();
		}

		return $this->string;
	}

	/** Returns only path part.
	@attrib api=1
	@returns string
	**/
	public function get_path()
	{
		return $this->get_string(false, false, false, false, false, true, false, false);
	}

	/**
	@attrib api=1
	@returns string
		Returns uri without query and fragment parts.
	**/
	public function get_base()
	{
		return $this->get_string(true, true, true, true, true, true, false, false);
	}

	/**
	@attrib api=1
	@returns string
		Returns uri without host and scheme.
	**/
	public function get_query()
	{
		$str = $str = $this->get_string(false, false, true, true, true, false, true, true);
		return substr($str, 0, 1) === "/" ? substr($str, 1) : $str;
	}

	/**
	@attrib api=1
	@returns string
		Returns uri as string with xml reserved characters converted to entities
	**/
	public function get_xml()
	{
		return str_replace("&", "&amp;", $this->get());
	}

	/**
	@attrib api=1 params=pos
	@param uri required type=string
		URI to load
	@returns void
	@errors
		Throws awex_uri_arg if $uri is not a URI and can't be loaded.
	**/
	public function set($uri)
	{
		$tmp = parse_url($uri);

		if (false === $tmp)
		{
			throw new awex_uri_arg("Not a URI: '$uri'.");
		}

		if (!empty($tmp["query"]))
		{
			parse_str($tmp["query"], $this->args); //!!! kontrollida kuidas urlencoded asjadega k2itutakse
		}

		if (isset($tmp["scheme"]))
		{
			$this->scheme = $tmp["scheme"];
		}

		if (isset($tmp["host"]))
		{
			$this->host = $tmp["host"];
		}

		if (isset($tmp["port"]))
		{
			$this->port = $tmp["port"];
		}

		if (isset($tmp["user"]))
		{
			$this->user = $tmp["user"];
		}

		if (isset($tmp["pass"]))
		{
			$this->pass = $tmp["pass"];
		}

		if (isset($tmp["path"]))
		{
			$this->path = preg_replace("|/+|", "/", $tmp["path"]);
		}

		if (isset($tmp["fragment"]))
		{
			$this->fragment = $tmp["fragment"];
		}

		$this->string = $uri;
		$this->updated = true;
	}

	/** Sets host part
	@attrib api=1 params=pos
	@param host required type=string
		host name
	@returns void
	@errors
		Throws awex_uri_type if $host is not a valid URI host name.
	**/
	public function set_host($host)
	{
		$this->host = $host;
		$this->updated = false;
	}

	/** Returns host part
	@attrib api=1 params=pos
	@returns string
	**/
	public function get_host()
	{
		return $this->host;
	}

	/** Sets scheme part
	@attrib api=1 params=pos
	@param scheme required type=string
		scheme name
	@returns void
	@errors
		Throws awex_uri_type if $scheme is not a valid URI scheme name.
	**/
	public function set_scheme($scheme)
	{
		$this->scheme = $scheme;
		$this->updated = false;
	}

	/** Returns scheme part
	@attrib api=1 params=pos
	@returns string
	**/
	public function get_scheme()
	{
		return $this->scheme;
	}

	/**
	@attrib api=1 params=pos
	@param name required type=string
		URI query argument/parameter name
	@returns string
		Query argument value. Returns NULL if argument not set.
	**/
	public function arg($name)
	{
		return isset($this->args[$name]) ? (string) $this->args[$name] : null;
	}

	/** Checks if argument is set
	@attrib api=1 params=pos
	@param name required type=string
		URI query argument/parameter name
	@returns bool
	**/
	public function arg_isset($name)
	{
		return isset($this->args[$name]);
	}

	/**
	@attrib api=1 params=pos
	@returns array
		Query argument values. Argument names as keys
	**/
	public function get_args()
	{
		return $this->args;
	}

	/**
	@attrib api=1 params=pos
	@param arg required type=string,array
		URI query arguments to set. String argument name or associative array of argument name/value string pairs or array of argument names to set value for.
	@param val optional type=string
		Argument value. If $arg is array, then it is handled as array of argument names and this value will be set for all of those. Required in that case and when $arg is argument name. Other scalar types are type-cast to string.
	@returns void
	@comment
		Sets query parameter value(s). If value is an object it is attempted to convert to string by __toString method if such exists. If value is an array, it is encoded in bracket format arg_name[array_element_key]=array_element_value (actual uri argument will have urlencoded name and value)
	@throws
		awex_uri_arg when $arg parameter is not valid -- name(s) not string value(s).
		awex_uri_arg when $arg parameter is string argument name and no second argument given.
		awex_uri_type when a query argument value is non-scalar.
		awex_uri_type with code awex_uri_type::RESERVED_CHR when an argument name contains reserved characters.
	**/
	public function set_arg($arg)
	{
		$sa = (2 === func_num_args());
		$args = $this->args;

		if (is_array($arg) and count($arg))
		{
			if ($sa)
			{
				$val = func_get_arg(1);

				foreach ($arg as $name)
				{
					$this->_set_arg($name, $val, $args);
				}
			}
			else
			{
				foreach ($arg as $name => $val)
				{
					$this->_set_arg($name, $val, $args);
				}
			}
		}
		elseif (is_string($arg) and strlen($arg))
		{
			if ($sa)
			{
				$val = func_get_arg(1);
				$this->_set_arg($arg, $val, $args);
			}
			else
			{
				throw new awex_uri_arg("No value specified to set '{$arg}'.");
			}
		}
		else
		{
			throw new awex_uri_arg("Invalid parameter value '" . var_export($arg, true) . "'. Array or argument name expected.");
		}

		$this->args = $args;
		$this->updated = false;
	}

	private function _set_arg($name, $val, &$args)
	{
		if (!is_string($name) or !strlen($name))
		{
			throw new awex_uri_arg("Invalid parameter value '" . var_export($name, true) . "'. Argument name expected.");
		}

		if (str_replace($this->reserved_chars, "a", $name) !== $name)
		{
			throw new awex_uri_type("Reserved character(s) in argument name", awex_uri_type::RESERVED_CHR);
		}

		if (is_object($val) and method_exists($val, "__toString"))
		{
			$val = $val->__toString();
		}
		elseif (is_scalar($val) or is_null($val) or is_array($val))
		{
		}
		else
		{
			throw new awex_uri_type("Tried to assign non-scalar, non-null, non-array value to URI query argument. Conversion attempts failed.");
		}

		$args[$name] = $val;
	}

	/**
	@attrib api=1 params=pos
	@param name optional type=string,array
		Name(s) of URI query argument(s) to unset.
	@returns array
		Argument names that weren't set in the first place.
	@comment
		Unsets argument(s). If no arguments given, unsets all query arguments.
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
					if (isset($this->args[$arg]))
					{
						unset($this->args[$arg]);
					}
					else
					{
						$not_found_args[] = $arg;
					}
				}
			}
			else
			{
				if (isset($this->args[$name]))
				{
					unset($this->args[$name]);
				}
				else
				{
					$not_found_args[] = $name;
				}
			}
		}
		else
		{
			$this->args = array();
		}

		$this->updated = false;
		return $not_found_args;
	}

	/* Rebuilds full string representation */
	private function update_string()
	{
		$this->string = $this->get_string();
		$this->updated = true;
	}

	/* Reads required parts and builds string representation */
	private function get_string($scheme = true, $host = true, $port = true, $user = true, $pass = true, $path = true, $query = true, $fragment = true)
	{
		$uri = "";

		if ($this->host)
		{
			if ($this->scheme and $scheme)
			{
				$uri .= $this->scheme . "://";
			}

			if ($this->user and $this->pass and $user and $pass)
			{
				$uri .= $this->user . ":" . $this->pass . "@";
			}
			elseif ($this->user and $user)
			{
				$uri .= $this->user . "@";
			}

			if ($host)
			{
				$uri .= $this->host;
			}

			if ($this->port and $port)
			{
				$uri .= ":" . $this->port;
			}
		}

		if ($this->path and $path)
		{
			$uri .= $this->path;
		}
		else
		{
			$uri .= "/";
		}

		if (count($this->args) and $query)
		{
			$uri .= "?" . http_build_query($this->args);
		}

		if ($this->fragment and $fragment)
		{
			$uri .= "#" . $this->fragment;
		}

		return $uri;
	}

	public function __toString()
	{
		return $this->get();
	}
}

/* Generic aw_uri class exception */
class awex_uri extends aw_exception {}

/* Generic condition when invalid argument given as method parameter */
class awex_uri_arg extends awex_uri {}

/* Method argument type not what expected */
class awex_uri_type extends awex_uri
{
	const RESERVED_CHR = 1;
}

