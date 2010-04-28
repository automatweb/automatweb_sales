<?php

namespace automatweb;

class aw_http_request extends aw_request
{
	private $_uri; // request uri aw_uri object if available, empty aw_uri object if not. read-only
	private $_method = "GET";

	public function __construct($autoload = false)
	{
		parent::__construct($autoload);
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
			catch (\Exception $e)
			{
				$this->_uri = new aw_uri();
			}
		}

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
			$this->_uri->set_arg($this->args);
		}
		catch (\Exception $e)
		{
			if (is_a($e, "awex_uri_type"))
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
			else
			{
				throw $e;
			}
		}
	}
}

?>
