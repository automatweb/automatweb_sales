<?php

class aw_http_response extends aw_resource
{
	protected $status = http::STATUS_OK;
	protected $status_message = "200 OK";
	protected $charset = "UTF-8";
	protected $content_type = "text/html";
	protected $compression = "";

	/**
	@attrib api=1 params=pos
	@returns void
		Sends http headers and content to user agent.
	**/
	public function send()
	{
		$this->send_headers();

		if ($this->compression or aw_ini_get("content.compress"))
		{
			ob_start("ob_gzhandler");
		}

		parent::send();
	}

	/**
	@attrib api=1 params=pos
	@returns void
		Sends http headers
	**/
	public function send_headers()
	{
		if (headers_sent())
		{
			trigger_error("Headers were already sent. AutomatWeb headers can't be sent.", E_USER_WARNING);
		}
		else
		{
			$status = automatweb::$request->protocol()->name() . " " . $this->status_message;
			header($status, true);
			header("Content-Type: {$this->content_type}; charset={$this->charset}", true);

			if ($this->last_modified)
			{
				header("Last-Modified: " . date("r", $this->last_modified), true);
			}
		}
	}

	/**
		@attrib api=1 params=pos
		@param name type=string
			Desired content type name
		@comment
		@returns void
		@errors
	**/
	public function set_content_type($name)
	{
		$this->content_type = $name;
	}

	/**
		@attrib api=1 params=pos
		@param name type=string
			Desired encoding name
		@comment
		@returns void
		@errors
	**/
	public function set_charset($name)
	{
		$this->charset = $name;
	}

	/**
		@attrib api=1 params=pos
		@param code type=int
			Desired http status code. One of http::STATUS_... constants
		@param message type=string
			Custom status message.
		@comment
		@returns void
		@errors
			throws awex_param if code is not valid
	**/
	public function set_status($code, $message = "")
	{
		$this->status_message = http::get_status_string($code);
		$this->status = (int) $code;

		if ($message)
		{
			$this->status_message = $message;
		}
	}

	/** Set binary string file contents to be sent to client as attachment (browser 'save as' dialog)
		@attrib api=1 params=pos
		@param contents type=string
		@param file_name type=string
		@param type type=string default="application/octet-stream"
		@param encoding type=string default=""
		@comment
		@returns void
		@errors
	**/
	public function set_send_file_contents($contents, $file_name, $type = "application/octet-stream", $encoding = "")
	{
		ob_clean();

		// IE needs specific headers
		$browser_info = get_browser();
		$browser_is_ie = "IE" === $browser_info->browser;
		if ($browser_is_ie)
		{
			$encoding = mb_detect_encoding($file_name);

			if (false === $encoding)
			{ // encoding not detected, force convert to ascii with html entities
				$file_name_ascii = "";
				foreach (str_split($file_name) as $char)
				{
					$file_name_ascii .= "&#" . ord($char) . ";";
				}
				$file_name = $file_name_ascii;
			}
			else
			{ // convert file name to utf8
				$file_name = iconv($encoding, "UTF-8//TRANSLIT", $file_name);
			}

			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Pragma: public");
		}
		else
		{
			header("Pragma: no-cache");
		}

		if ($encoding)
		{
			$encoding = "; charset={$encoding}";
		}

		header("Content-Type: {$type}{$encoding}");
		header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT" ); // set expiry to avoid caching
		header("Content-Disposition: attachment; filename=\"{$file_name}\"");
		header("Content-Disposition-type: attachment");
		header("Content-Transfer-Encoding: binary");

		exit($contents);//TODO: ... yle vaadata
	}

	public function add_data($data)
	{
		throw new awex_not_available("add_data() is not available for HTTP responses.");
	}

	public function sysmsg($message)
	{
		echo nl2br(htmlspecialchars($message)) . "<br />\n";
		flush();
	}

	public function __toString()
	{
		// if data is an xml/xhtml/... document then match content charset to that document's charset automatically
		if (isset($this->data[0]) and $this->data[0] instanceof aw_xml_document)
		{
			$this->set_charset($this->data[0]->encoding());
		}
		return parent::__toString();
	}
}
