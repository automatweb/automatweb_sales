<?php

class smtp extends aw_template
{
	function smtp()
	{
		$this->db_init();
	}

	function send_message($server, $from, $to, $msg)
	{
		if (!$this->connect($server))
		{
			return false;
		}

		if (!$this->get_status($this->read_response()))
		{
			$this->raise_error(ERR_SMTP_WSERVER,t("smtp: error, something wrong with server"), false);
		}

		$this->send_command("HELO ".aw_global_get("SERVER_NAME"));
		if (!$this->get_status($err = $this->read_response()))
		{
			$this->raise_error(ERR_SMTP_HELO,sprintf(t("smtp: error '%s' after HELO %s"), $err, aw_global_get("SERVER_NAME")), false);
		}

		$this->send_command("MAIL FROM:<$from>");
		if (!$this->get_status($err = $this->read_response()))
		{
			$this->raise_error(ERR_SMTP_MFROM,sprintf(t("smtp: error '%s' after MAIL FROM:<%s>"), $err, $from), false);
		}

		// to can contain multiple addresses
		$to_arr = explode(",", $to);
		$this->send_command("RCPT TO:<".reset($to_arr).">");
		if (!$this->get_status($err = $this->read_response()))
		{
			$this->raise_error(ERR_SMTP_RCPT,sprintf(t("smtp: error '%s' after RCPT TO:<%s>"), $err, $to), false);
		}

		$this->send_command("DATA");
		if (!$this->get_status($err = $this->read_response()))
		{
			$this->raise_error(ERR_SMTP_DATA,sprintf(t("smtp: error '%s' after DATA"), $err), false);
		}

		$larr = explode("\n", $msg);
		reset($larr);
		while (list(,$v) = each($larr))
		{
			$v = str_replace("\x0d", "", $v);	// make damn sure we have no breaks at end of line
			$v = str_replace("\x0a", "", $v);

			if ($v == ".")
				$v = "..";

			$this->send_command($v);
		}
		$this->send_command(".");
		if (!$this->get_status($err = $this->read_response()))
		{
			$this->raise_error(ERR_SMTP_MSG,sprintf(t("smtp: error '%s' after message"), $err), false);
		}

		$this->send_command("QUIT");
		if (!$this->get_status($err = $this->read_response()))
		{
			$this->raise_error(ERR_SMTP_QUIT,sprintf(t("smtp: error '%s' after QUIT"), $err), false);
		}

		return true;
	}

	function connect($server)
	{
		$this->fp = fsockopen($server, 25, &$errno, &$errstr, 20);
		if (!$this->fp)
		{
			$this->raise_error(ERR_SMTP_CONNECT,sprintf(t("smtp: error connecting, %s , %s"), $errno, $errstr),false);
			return false;
		}
		return true;
	}

	function read_response()
	{
		$line = fgets($this->fp, 512);
		return $line;
	}

	function get_status($line)
	{
		$errors = array("500" => 1, "501" => 1, "502" => 1, "503" => 1, "504" => 1, "421" => 1, "221" => 1, "450" => 1, "550" => 1, "451" => 1, "551" => 1, "452" => 1, "552" => 1, "553" => 1, "554" => 1);

		$code = $line+0;
		if ($errors[code])
			return false;

		return true;
	}

	function send_command($cmdstr)
	{
		fputs($this->fp, $cmdstr."\n");
	}
}
