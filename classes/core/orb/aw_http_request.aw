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

	public function lang()
	{
		if (!$this->_client_lang_parsed)
		{
			$this->_get_client_language();
			$this->_client_lang_parsed = true;
		}

		return $this->_lang_id;
	}

	private function _get_client_language()
	{
		$lid = 0;
		/// try url if settings direct
		/// language from url. usu. in the form http://myserver/eng/somedocument...
		if (aw_ini_get("menuedit.language_in_url"))
		{
			$lang = explode("/", $this->_uri->get_path());
			$lc0 = $lang[0];
			$lc1 = $lang[1];

			!empty($lc0) and
			"automatweb" !== $lc0 and
			aw_ini_isset("menuedit.language_table.{$lc0}") and
			$lid = languages::lc2lid(aw_ini_get("menuedit.language_table.{$lc0}"))
			or
			!empty($lc1) and
			"automatweb" !== $lc1 and
			aw_ini_isset("menuedit.language_table.{$lc1}") and
			$lid = languages::lc2lid(aw_ini_get("menuedit.language_table.{$lc1}"))
			;
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

		$this->set_lang($lid);
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
		if (!empty($_SERVER["REQUEST_URI"]))
		{
			try
			{
				$this->_uri = new aw_uri($_SERVER["REQUEST_URI"]);

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

		// load language
		$this->_autoload_language();

		// parse special automatweb request variables
		$AW_GET_VARS = array();
		$pi = "";
		$PATH_INFO = "";
		$QUERY_STRING = "";
		$REQUEST_URI = "";
		$PATH_INFO = "";

		if (!empty($_SERVER["PATH_INFO"]))
		{
			$PATH_INFO = preg_replace("|\?automatweb=[^&]*|","", $_SERVER["PATH_INFO"]);
		}

		if (!empty($_SERVER["QUERY_STRING"]))
		{
			$QUERY_STRING = preg_replace("|\?automatweb=[^&]*|","", $_SERVER["QUERY_STRING"]);
		}

		if (!empty($_SERVER["REQUEST_URI"]))
		{
			$REQUEST_URI = $_SERVER["REQUEST_URI"];
		}

		if (empty($QUERY_STRING) and empty($PATH_INFO) and !empty($REQUEST_URI))
		{
			$QUERY_STRING = str_replace(array("xmlrpc.aw", "index.aw", "orb.aw", "login.aw", "reforb.aw"), "", $REQUEST_URI);
		}

		if (strlen($PATH_INFO) > 0)
		{
			$pi = $PATH_INFO;
		}

		if (strlen($QUERY_STRING) > 0)
		{
			$pi .= "?".$QUERY_STRING;
		}

		$REQUEST_URI = preg_replace("|\?automatweb=[^&]*|","", $REQUEST_URI);
		$pi = preg_replace("|\?automatweb=[^&]*|ims", "", $pi);

		if ($pi)
		{
			// if $pi contains & or =
			if (preg_match("/[&|=]/",$pi))
			{
				// expand and import PATH_INFO
				// replace ? and / with & in $pi and output the result to AW_GET_VARS
				parse_str(str_replace("?","&",str_replace("/","&",$pi)),$AW_GET_VARS);
			}
		}

		$this->args = $this->args + $AW_GET_VARS;

		// parse arguments
		$this->parse_args();
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
