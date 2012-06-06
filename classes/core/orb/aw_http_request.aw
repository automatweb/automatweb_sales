<?php

class aw_http_request extends aw_request
{
	private $_uri; // request uri aw_uri object if available, empty aw_uri object if not. read-only
	private $_method = "GET";
	private $_client_lang_parsed = false; // http request language init flag

	public function __construct($autoload = false)
	{
		$this->protocol = new http();

		if (!$autoload)
		{
			$this->_uri = new aw_uri();
		}
		else
		{
			$this->_autoload();
		}
	}

	public function lang_id()
	{
		if (!$this->_client_lang_parsed)
		{
			$this->_get_client_language();
			$this->_client_lang_parsed = true;
		}

		return parent::lang_id();
	}

	private function _get_client_language()
	{
		$lid = 0;
		/// try url if settings direct
		/// language from url. usu. in the form http://myserver/eng/somedocument...
		if (aw_ini_get("menuedit.language_in_url"))
		{
			$lang = explode(" ", trim(str_replace("/", " ", $this->_uri->get_path())));

			if (!empty($lang[0]))
			{
				if ("automatweb" !== $lang[0])
				{
					$lid = languages::lc2lid($lang[0]);
				}
				elseif (!empty($lang[1]))
				{
					$lid = languages::lc2lid($lang[1]);
				}
			}
		}

		/// try browser acceptlang header
		if (!$lid and !empty($_SERVER["HTTP_ACCEPT_LANGUAGE"]))
		{
			$langs = array();
			// break up string into pieces (languages and q factors)
			preg_match_all("/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i", $_SERVER["HTTP_ACCEPT_LANGUAGE"], $lang_parse);

			if (count($lang_parse[1]))
			{
				// create a list like "en" => 0.8
				$langs = array_combine($lang_parse[1], $lang_parse[4]);

				// set default to 1 for any without q factor
				foreach ($langs as $lang => $val)
				{
					if ($val === "") $langs[$lang] = 1;
				}

				// sort list based on value
				arsort($langs, SORT_NUMERIC);
			}

			/// look through sorted list and use first one that matches an aw language
			foreach ($langs as $lang => $val)
			{
				if ($lid = languages::acceptlang2lid($lang))
				{
					break;
				}
			}
		}

		if ($lid)
		{
			$this->set_lang_id($lid);
		}
	}

	/**
	@attrib api=1 params=pos
	@returns aw_uri
		Request uri if available.
	@throws
		awex_request_na when uri is not available in current request type
	**/
	public function get_uri()
	{
		$this->update_uri();
		return clone $this->_uri;
	}

	/**
	@attrib api=1 params=pos
	@returns string
		Request method name. "POST" | "GET" ...
	**/
	public function get_method()
	{
		return $this->_method;
	}

	private function _autoload()
	{
		if (!empty($_SERVER["REQUEST_METHOD"]))
		{
			$this->_method = $_SERVER["REQUEST_METHOD"];
		}

		// load arguments
		if (!empty($_POST))
		{
			$this->_method = "POST";

			if (!empty($_GET) and count($_GET))
			{ // _GET overwrites _POST. Must be reviewed and this requirement deprecated and code requiring it rewritten.
				$this->args = $_GET + $_POST;
				$_POST = $_GET + $_POST;
			}
			else
			{
				$this->args = $_POST;
			}
		}
		elseif (!empty($_GET))
		{
			$this->args = $_GET;
			$this->_method = "GET";
		}

		// load uri
		/// load host part
		if (!empty($_SERVER["SCRIPT_URI"]))
		{
			$scheme = empty($_SERVER["HTTPS"]) || "off" !== $_SERVER["HTTPS"] ? "http" : "https";// "off" check is for IIS
			$url = substr($_SERVER["SCRIPT_URI"], 0, strrpos($_SERVER["SCRIPT_URI"], "/"));
			$host_only = false;
		}
		elseif (!empty($_SERVER["HTTP_HOST"]))
		{
			$scheme = empty($_SERVER["HTTPS"]) || "off" !== $_SERVER["HTTPS"] ? "http" : "https";// "off" check is for IIS
			$url = "{$scheme}://{$_SERVER["HTTP_HOST"]}/";
			$host_only = true;
		}
		elseif (!empty($_SERVER["SERVER_NAME"]))
		{
			$scheme = empty($_SERVER["HTTPS"]) && "off" !== $_SERVER["HTTPS"] ? "http" : "https"; // "off" check is for IIS
			$url = "{$scheme}://{$_SERVER["SERVER_NAME"]}/";
			$host_only = true;
		}
		else
		{
			$scheme = "";
			$url = aw_ini_get("baseurl");
			$host_only = true;
		}

		/// create uri object
		if (empty($_SERVER["REQUEST_URI"]))
		{
			$this->_uri = new aw_uri($url);
			$request_uri = "";
		}
		else
		{
			$request_uri = $host_only ? $_SERVER["REQUEST_URI"] : "/" . basename($_SERVER["REQUEST_URI"]);

			try
			{
				$url = $url . (1 === strpos($request_uri, "/") ? substr($request_uri, 1) : $request_uri);
				$this->_uri = new aw_uri($url);

				// try to get request variables from uri, assuming that those must prevail over $_GET
				$uri_args = $this->_uri->get_args();
				if (count($uri_args) > 0 and count($uri_args) !== count($_GET))
				{
					$this->args = $uri_args;
				}
			}
			catch (Exception $e)
			{
				$this->_uri = new aw_uri();
			}
		}

		// parse special automatweb request variables
		$AW_GET_VARS = array();
		$pi = isset($_SERVER["PATH_INFO"]) ? $_SERVER["PATH_INFO"] : "";
		$query_string = isset($_SERVER["QUERY_STRING"]) ? $_SERVER["QUERY_STRING"] : "";

		if (!$query_string and !$pi and $request_uri)
		{
			$query_string = str_replace(array("xmlrpc.aw", "index.aw", "orb.aw", "login.aw", "reforb.aw"), "", $request_uri);
		}

		if (strlen($query_string) > 0)
		{
			$pi .= "?{$query_string}";
		}

		if (false !== strpos($pi, "=") and false === strpos($pi, "?"))
		{
			// expand and import PATH_INFO
			// replace ? and / with & in $pi and output the result to AW_GET_VARS
			parse_str(str_replace("/", "&", $pi), $AW_GET_VARS);
		}


		$this->args = $this->args + $AW_GET_VARS;

		if (isset($this->args["automatweb"])) unset($this->args["automatweb"]); // for security//TODO: otsida, miks oli vaja preg_replace-ga automatweb=... urlist v2lja, kas ainult sessioonimuutuja p2rast, mis v6is globalsi kaudu kuskile sattuda?

		// check arguments
		$test_uri = new aw_uri();
		$args = $this->args;
		foreach ($args as $key => $value)
		{
			try
			{
				$test_uri->set_arg($key, $value);
			}
			catch (Exception $e)
			{
				unset($args[$key]);
			}
		}
		$this->args = $args;

		// parse arguments
		$this->parse_args();

		// load system components
		$this->_autoload_data_storage();//TODO: cfg laadimise juurde viia
		$this->_autoload_language();
	}

	private function update_uri()
	{
		$this->_uri->unset_arg();

		try
		{
			if ($this->args)
			{
				$this->_uri->set_arg($this->args);
			}
		}
		catch (Exception $e)
		{
			if ($e instanceof awex_uri_type)
			{
				if (awex_uri_type::RESERVED_CHR === $e->getCode())
				{
					throw new awex_request_na("This request contains arguments that can't be converted to URI argument names.");
				}
				else
				{
					throw new awex_request_na("This request contains argument values that can't be converted to URI arguments.");
				}
			}
			elseif ($e instanceof awex_uri_arg)
			{
				//	$this->args is prolly an empty array, take no action
			}
			else
			{
				throw $e;
			}
		}
	}
}
