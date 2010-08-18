<?php

class aw_http_response extends aw_resource
{
	protected $headers = array(); // http headers, array( header_name => value, ...)

	/**
	@attrib api=1 params=pos
	@returns void
		Sends http headers and content to user agent.
	**/
	public function send()
	{
		if (!headers_sent())
		{
			header("Content-Type: text/html; charset=" . aw_global_get("charset"), true);

			if (empty($this->headers) and empty($this->data))
			{ // 404 - resource not found
				$protocol = automatweb::$request->protocol()->name();
				header("{$protocol} 404 " . t("Resource not available"));
				$this->set_data(t("Resource not available"));
			}
			else
			{
				foreach ($this->headers as $name => $value)
				{
					header($name . ": " . $value, true);
				}

				header("Last-Modified: " . date("r", $this->last_modified), true);
			}
		}
		else
		{
			trigger_error("Headers were already sent", E_USER_WARNING);
		}

		if (aw_ini_get("content.compress"))
		{
			ob_start( 'ob_gzhandler' );
		}

		parent::send();
	}

	public function sysmsg($message)
	{
		echo nl2br(htmlspecialchars($message));
		flush();
	}
}

?>
