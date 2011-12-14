<?php

// Thanks to Kartic Krishnamurthy <kaygee@netset.com> for ideas and sample code
// mail.aw - Sending and parsing mail. MIME compatible

// ideaalis peaks see edaspidi toetama ka teisi mailisaatmismeetodeid
// peale PHP mail funktsiooni

define('X_MAILER',"AW Mail 2.0");
define('WARNING', 'This is a MIME encoded message');
define('OCTET', 'application/octet-stream');
define('TEXT', 'text/plain');
define('HTML', 'text/html');
define('INLINE', 'inline');
define('ATTACH', 'attachment');
define('CRLF', "\n");
define('BASE64', 'base64');

class aw_mail
{
	const X_MAILER = "AW Mail 2.0";
	const WARNING = "This is a MIME encoded message";
	const OCTET = "application/octet-stream";
	const TEXT = "text/plain";
	const HTML = "text/html";
	const INLINE = "inline";
	const ATTACH = "attachment";
	const CRLF = "\n";
	const BASE64 = "base64";

	var $boundary;

	var $message; // siin hoiame teate erinevaid osasid
	var $headers; // headerid

	private $mimeparts = array(); // siin hoiame teate MIME osasid
	private $is_multipart_html = false;
	private $body_replacements;
	private $charset = languages::USER_CHARSET;
	private $method = "phpmail"; // phpmail | mimemessage
	private $mimemessage; // mimemessage class email object

	////
	// !Konstruktor
	// argumendid
	// method(string) - mis meetodi abil meili saadame?
	public function aw_mail($args = array())
	{
		return $this->clean($args);
	}

	/**Resets the object
	@attrib api=1 params=name
	@param method optional type=string
		By default php mail function
	@example ${gen_mail}
	@comment
		Resets the mail object
	**/
	public function clean($args = array())
	{
		$this->message = array();
		$this->headers = array();
		$this->mimeparts = array();
		$this->mimeparts[] = "";
		// by default php mail funktsioon
		$this->set_send_method(empty($args["method"]) ? "phpmail" : $args["method"]);
	}

	/**
		@attrib api=1 params=pos
		@param method_name type=string
			phpmail|mimemessage|mimemessage_smtp
		@comment
		@returns
		@errors
			throws awex_awmail_method if method not supported
	**/
	public function set_send_method($method_name)
	{
		if ("phpmail" === $method_name)
		{
		}
		elseif ("mimemessage" === $method_name)
		{
			require(AW_DIR . "addons/email/mimemessage/email_message.php");
			$this->mimemessage = new email_message_class();
		}
		elseif ("mimemessage_smtp" === $method_name)
		{
			require(AW_DIR . "addons/email/mimemessage/email_message.php");
			require(AW_DIR . "addons/email/mimemessage/smtp_message.php");
			require(AW_DIR . "addons/email/smtpclass/smtp.php");
			$this->mimemessage = new email_message_class();
		}
		else
		{
			throw new awex_awmail_method("Send method '{$method_name}' not supported");
		}

		$this->method = $method_name;
	}

	/**
	@attrib api=1 params=name
	@param part required type=string
		number of part
	@return array/decoded MIME part
	**/
	function get_part($args = array())
	{
		extract($args);
		$block = array();
		if ($part === "body")
		{
			$block["headers"] = $this->headers;
			$block["body"] = $this->body;
		}
		else
		{
			$partnum = $part - 1;
			if (!is_array($this->mimeparts[$part]))
			{
				return false;
			}
			$block = $this->mimeparts[$part];
		}

		switch($block["headers"]["Content-Transfer-Encoding"])
		{
			case "base64":
				$content = base64_decode($block["body"]);
				break;

			case "doubleROT13":
				// $content = your_decoding_function_call_here
				break;

			default:
				$content = $block["body"];
		}

		$block["body"] = $content;
		return $block;
	}

	/**
	@attrib api=1 params=name
	@param data required type=string
		body of the message
	@return int/the number of attaches found

	@comments
		Parses a MIME formatted data block (e.g. email message)
	**/
	function parse_message($args = array())
	{
		// First we pass the whole message through our parser
		$res = $this->_parse_block(array(
					"data" => $args["data"],
					));
		$this->headers = $res["headers"];

		// Do we have a multipart message?
		if (preg_match("/^multipart\/mixed/i",$this->headers["Content-Type"]))
		{
			if (!$this->headers["Boundary"])
			{
				preg_match("/boundary=(.*)$/i",$this->headers["Content-Type"],$matches);
				$this->headers["Boundary"] = $matches[1];
			};
			// feel free to consult the RFC to understand this
			$separator = "--" . $this->headers["Boundary"];

			$msg_parts = explode($separator,$res["body"]);
			$count = sizeof($msg_parts);
			// we should always get at least 4 parts and it is safe to ignore first and last, since
			// they do not contain anything of importance
			// second will contain the body of the message, and starting from the third are the attaches

			for ($i = 1; $i <= ($count - 2); $i++)
			{
				$block = $this->_parse_block(array(
						"data" => $msg_parts[$i],
						));
				$headers2 = $block["headers"];

				// kui see on esimene blokk, siis jarelikult on meil tegemist tekstiga, ja seda pole vaja
				// mime-partiks teha
				if ($i == 1)
				{
					$xheaders = array_merge($this->headers,$headers2);
					$this->headers = $xheaders;
					$this->body = $block["body"];
					$this->mimeparts[0] = array(
								"headers" => $xheaders,
								"body" => $block["body"],
							);
				}
				else
				{
					$this->mimeparts[] = $block;
				}
			}
			$this->nparts = $count - 3;
		}
		else
		{
			// nope, it was a single-part message.
			$this->mimeparts[0] = $res;
			$this->body = $res["body"];
			$this->nparts = 0;
		}
		return $this->nparts;
	}

	////
	// !Mime parser for internal use
	// @param data type=string
	//	body of the message
	private function _parse_block($args = array())
	{
		extract($args);
		$in_headers = true;

		$_headers = array();
		$headers = array();
		$body = "";

		// strip the whitespace from the beginning
		$data = preg_replace("/^\s+?/","",$data);

		// I'm not sure whether this is correct either.
		$data = preg_replace("/\r/","",$data);

		// split the data to individual lines
		// actually, I don't like this one not a single bit, but since I do not know the internals
		// of PHP very well, I don't know whether parsing the string until the next linefeed
		// is found, is very effective. So right now, I'll leave it as it is.
		$data = trim($data);

		$lines = preg_split("/\n/",$data);

		$i = 0;

		foreach($lines as $num => $line)
		{
			// If we find an empty line, then we have all the headers and can continue with
			if ((preg_match("/^$/",$line)) && ($in_headers))
			{
				$in_headers = false;
			}

			if ($in_headers)
			{
				// If the line starts with whitespace, then we will add it to the last header
				if (preg_match("/^\s/",$line))
				{
					$last = array_pop($_headers);
					$last .= " " . trim($line);
					array_push($_headers,$last);
				}
				// otherwise we just add it to the end of the headers array
				else
				{
					array_push($_headers,$line);
				}
			}
			// when we get here, then this means that we have reached the body of the message.
			// no further actions, we just fill the $body variable.
			else
			{
				$body .= $line . "\r\n";
			}
		}

		// Now we will fetch all other more or less useful data out of the headers and store it in separate
		// variables

		$content_type = $boundary = $content_name = $content_encoding = "";

		foreach($_headers as $line)
		{
			if (preg_match("/^Content-Type: (.*); (.*)$/i",$line,$matches))
			{
				$headers["Content-Type"] = $matches[1];
				if (preg_match("/boundary=\"(.*)\"/i",$matches[2],$bmatch))
				{
					$headers["Boundary"] = $bmatch[1];
				}
				elseif (preg_match("/boundary=(.*)/i",$matches[2],$bmatch))
				{
					$headers["Boundary"] = $bmatch[1];
				};

				if (preg_match("/name=\"(.*)\"/i",$matches[2],$nmatch))
				{
					$headers["Content-Name"] = $nmatch[1];
				};

				if (preg_match("/name=(.*)/i",$matches[2],$nmatch))
				{
					$headers["Content-Name"] = $nmatch[1];
				};
			}
			elseif (preg_match("/^Date: (.*)$/i",$line,$mt))
			{
				$headers["Date"] = $mt[1];
			}
			else
			{
				preg_match("/^(.+?): (.*)$/",$line,$matches);
				$headers[$matches[1]] = $matches[2];
			}
		}

		$result = array(
			"headers" => $headers,
			"body" => $body,
			);

		return $result;
	}

	/**
	@attrib api=1 params=name
	@param froma type=string default=""
		sender's address
	@param fromn type=string default=""
		sender's name
	@param to type=string
		mail to (adresses)
	@param cc type=string default=""
		mail to (adresses)
	@param bcc type=string default=""
		mail to (adresses)
	@param subject type=string default=""
		mail subject
	@param headers type=array default=array()
		additional headers
	@param body type=string default=""
		mail body
	@param X-Mailer type=string default=self::X_MAILER
		mail version
	@example ${gen_mail}
	@comments
		Creates a new message
	**/
	function create_message($args)
	{
		if (empty($args["to"]))
		{
			throw new awex_awmail_param("Recipient address must be specified.");
		}

		$defaults = array(
			"froma" => "",
			"fromn" => "",
			"cc" => "",
			"bcc" => "",
			"subject" => "",
			"headers" => array(),
			"body" => "",
			"X-Mailer" => self::X_MAILER
		);
		$args = $args + $defaults;

		if (!empty($args["body"]))
		{
			$this->body = $args["body"];
		}

		$this->subject = $args["subject"];

		// get from address
		$this->from = empty($args["froma"]) ? $this->get_default_from_address() : $args["froma"];
		if (!empty($args["fromn"]))
		{
			$from = sprintf("%s <%s>", $args["fromn"], $this->from);
		}
		else
		{
			$from = $this->from;
		}
		$args["from"] = $from;

		//
		if ("mimemessage" === $this->method)
		{
			// create mimemessage object
			$this->_mimemessage_create($args);
		}
		else
		{
			// parse headers
			$this->headers["From"] = $from;
			$this->headers["To"] = $args["to"];
			$this->headers["Cc"] = $args["cc"];
			$this->headers["Bcc"] = $args["bcc"];
			$this->headers["Subject"] = $args["subject"];
			$this->headers["X-Mailer"] = $args["X-Mailer"];
		}
	}

	private function _mimemessage_create($args)
	{
		$this->mimemessage->default_charset = $this->charset;
		$this->mimemessage->mailer = $args["X-Mailer"];
		$this->mimemessage->SetHeader("To", $args["to"]);

		if ($args["cc"])
		{
			$this->mimemessage->SetHeader("Cc", $args["cc"]);
		}

		if ($args["bcc"])
		{
			$this->mimemessage->SetHeader("Bcc", $args["bcc"]);
		}

		if ($args["from"])
		{
			$this->mimemessage->SetHeader("From", $args["from"]);
		}

		$this->mimemessage->SetEncodedHeader("Subject", $args["subject"]);

		foreach ($args["headers"] as $name => $value)
		{
			$this->mimemessage->SetHeader($name, $value);
		}

		if (aw_ini_get("mail.return_path"))
		{
			$return_path = aw_ini_get("mail.return_path");
			if(defined("PHP_OS") and strcmp(substr(PHP_OS, 0, 3), "WIN"))
			{
				$this->mimemessage->SetHeader("Return-Path", $return_path);
			}
			$this->mimemessage->SetHeader("Errors-To", $return_path);
		}
	}

	/**Attaches an object to our message
	@attrib params=name
	@param data required type=string
		random data
	@param description optional type=string
		Content-Description
	@param encoding optional type=string default="base64"
		text encoding
	@param contenttype optional type=string default="application/octet-stream"
		specifies the HTTP content type for the response.
	@param disp optional type=string
		Content-Disposition
	@return false, if $data is empty
		else number of mimeparts
	**/
	public function attach($args = array())
	{
		extract($args);
		if (empty($data))
		{
			return false;
		}

		if (empty($contenttype))
		{
			$contenttype = self::OCTET;
		}

		if (empty($encoding))
		{
			$encoding = self::BASE64;
		}

		if ($encoding == self::BASE64)
		{
			$emsg = base64_encode($data);
			$emsg = chunk_split($emsg,76,self::CRLF);
		}
		else
		{
			$emsg = $data;
		}

		$emsg = trim($emsg);


		if (preg_match("!^".self::TEXT."!i", $contenttype) && !preg_match("!;charset=!i", $contenttype))
		{
			$contenttype .= ";" . self::CRLF . " charset=".$this->charset;
		}

		if (!empty($args["body"]))
		{
			if ($description)
			{
				$this->headers["Content-Description"] = $description;
			}

			if ($disp)
			{
				// $this->headers["Content-Disposition"] = $disp;
			}

			$pref = "Content-Type: text/plain; charset={$this->charset}" . self::CRLF;
			$pref .= "Content-Transfer-Encoding: 8bit";


			//$this->headers["Content-Type"] = $contenttype;
			$this->headers["Content-Transfer-Encoding"] = $encoding;
			$this->mimeparts[0] = $pref . self::CRLF . $emsg;
		}
		else
		{
			$msg = sprintf("Content-Type: %sContent-Transfer-Encoding: %s%s%s%s",
						$contenttype.self::CRLF,
						$encoding.self::CRLF,
						(($description) ? "Content-Description: $description".self::CRLF:""),
						(($disp) ? "Content-Disposition: $disp".self::CRLF:""),
						self::CRLF.$emsg.self::CRLF);
			$this->mimeparts[] = $msg;
		}

		return sizeof($this->mimeparts);
	}

	private function gen_htmlbody($body, $heads = "")
	{
		return ((substr($body,0,6)=="<html>") || (strpos($body, "<!DOCTYPE") !== false))?$body:
			"<html><head><title></title>".$heads."</head><body>$body</body></html>";
	}

	/**
	@attrib api=1 params=name
	@param data type=string
		html data
	@param heads type=string default=""
		stuff inside html <head> tag
	@example ${gen_mail}
	@comments
		Defines a html body part
	@errors
		throws awex_awmail_param if data parameter empty
	**/
	public function htmlbodyattach($args)
	{
		if (empty($args["data"]))
		{
			throw new awex_awmail_param("Empty html data parameter");
		}

		$args = $args + array("data" => "", "heads" => "");
		$data = $args["data"];
		$html = $this->gen_htmlbody($data, $args["heads"]);

		if ("mimemessage" === $this->method)
		{
			// add html part
			$html_part = 0;
			$this->mimemessage->CreateQuotedPrintableHTMLPart($html, "", $html_part);

			// add alternative text part
			$text_part = 0;
			$this->mimemessage->CreateQuotedPrintableTextPart($this->mimemessage->WrapText($this->body), "", $text_part);

			// set both parts as a multipart mail message
			// the text part should appear first in the parts array because the e-mail programs that support displaying more sophisticated message parts will pick the last part in the message that is supported
			$parts = array($text_part, $html_part);
			$this->mimemessage->AddAlternativeMultipart($parts);

			// set to 1, indicate that plain text body is not to be added again
			$this->is_multipart_html = true;
		}
		else
		{
			// nii, kuidas seda siis teha?
			// tuleb teha juurde yx mime part, mille Content-Type: multipart/alternative;
			// sinna sisse paneme vana message body ja eraldatult uue html body.
			$boundary='AW'.chr(rand(65,91)).'--'.md5(uniqid(rand()));
			$atc="Content-Type: multipart/alternative;".self::CRLF." boundary=\"$boundary\"".self::CRLF.self::CRLF;

			$plain = strtr($this->body, array("<br />"=>"\r\n","<br />"=>"\r\n","</p>"=>"\r\n","</p>"=>"\r\n"));
			$plain = $this->strip_html($plain);

			$atc.= "--".$boundary. self::CRLF;
			$atc.="Content-Type: text/plain; charset=" . $this->charset . self::CRLF;
			$atc.="Content-Transfer-Encoding: 8bit".self::CRLF.self::CRLF.$plain.self::CRLF.self::CRLF;
			$atc.="--".$boundary.self::CRLF;

			$data = str_replace("\\\"","\"", $data);
			$atc.="Content-Type: text/html; charset=" . $this->charset . self::CRLF;
			$atc.="Content-Transfer-Encoding: 8bit".self::CRLF.self::CRLF.$html.self::CRLF.self::CRLF;

			$atc .= "--".$boundary."--".self::CRLF;

			// see peab kindlalt olema esimene 2tt2ts.
			$this->mimeparts=array_merge(array($atc), $this->mimeparts);
			unset($this->body);
		}
	}

	/**
	@attrib api=1 params=pos
	@param erc optional type=string
		data , where you want to replace stuff
	@comments
		replace html stuff with not html stuff
	**/
	function strip_html($src)
	{
		$search = array (	"'<script[^>]*?>.*?</script>'si",  // Strip out javascript
					"'<[\/\!]*?[^<>]*?>'si",           // Strip out html tags
					"'&(quot|#34);'i",                 // Replace html entities
					"'&(amp|#38);'i",
					"'&(lt|#60);'i",
					"'&(gt|#62);'i",
					"'&(nbsp|#160);'i",
					"'&(iexcl|#161);'i",
					"'&(cent|#162);'i",
					"'&(pound|#163);'i",
					"'&(copy|#169);'i",
					"'&#(\d+);'e");                    // evaluate as php

		$replace = array (	"",
					"",
					"\"",
					"&",
					"<",
					">",
					" ",
					chr(161),
					chr(162),
					chr(163),
					chr(169),
					"chr(\\1)");

		$text = preg_replace ($search, $replace, $src);
		return $text;
	}

	/**
	@attrib api=1 params=pos
	@param args optional type=array
		array("stuff you need to replace" => "stuff to replace with",)
	@comment
		its useful to do before you generate mail
	**/
	function body_replace($args = array())
	{
		$this->body_replacements = $args;
	}

	/**
	@attrib api=1 params=name
	@param path type=string
		path to file
	@param content type=string default=""
		if set, path is ignored
	@param description type=string default=""
		Content-Description
	@param contenttype type=string default=self::OCTET
		file content type
	@param encoding type=string default=self::BASE64
		file encoding
	@param disp type=string default=""
		content-disposition
	@param name type=string default=""
		Used as file name, if content is set, this must be set too
	@example ${gen_mail}
	@return int/number of mimeparts
	@comment
		Attaches a file to the message
	@errors
		throws awex_awmail_param if path and content parameters empty
		throws awex_awmail_attach if file attaching fails and mimemessage send method used
	**/
	function fattach($args = array())
	{
		if (empty($args["path"]) and empty($args["content"]))
		{
			throw new awex_awmail_param("Both path and content parameters missing");
		}

		$args = $args + array(
			"path" => "",
			"content" => "",
			"description" => "",
			"contenttype" => self::OCTET,
			"encoding" => self::BASE64,
			"disp" => "attachment",
			"name" => ""
		);

		$path = $args["path"];
		$description = $args["description"];
		$contenttype = $args["contenttype"];
		$encoding = (substr($contenttype,0,4) === "text") ? "8bit" : $args["encoding"];
		$name = $args["name"];

		if (strlen($args["content"]) === 0)
		{
			// read the fscking file
			$fp = fopen($path, "rb");
			if (!$fp)
			{
				return false; // fail
			}

			if (!$name)
			{
				$name = basename($path);
			}

			$data = fread($fp, filesize($path));
		}
		else
		{
			$data = $args["content"];

			if (!$name)
			{
				static $i = 1;
				$name = "attached_file_{$i}";
				$i++;
			}
		}

		if ("mimemessage" === $this->method)
		{
			$params = array(
				"Data" => $data,
				"Name" => $name,
				"Content-Type" => $contenttype,
				"Disposition" => $args["disp"]
			);
			$error = $this->mimemessage->AddFilePart($params);

			if ($error)
			{
				throw new awex_awmail_attach($error);
			}
			else
			{
				$this->mimeparts[] = "";
				$part_count = count($this->mimeparts);
			}
		}
		else
		{
			$contenttype .= ";" . self::CRLF . " name=\"".$name . "\"";
			$part_count = $this->attach(array(
				"data" => $data,
				"description" => $description,
				"contenttype" => $contenttype,
				"encoding" => $encoding,
				"disp" => $args["disp"]
			));
		}

		return $part_count;
	}

	//// Genereerib message_id headeri
	private function gen_message_id()
	{
		$id = '<AW' . chr(rand(65,91)) . chr(rand(65,91)) . md5(uniqid(rand())) . "@automatweb>";
		return $id;
	}

	private function build_message($args = array())
	{
		$msg = "";
		if ($this->boundary)
		{
			$boundary = $this->boundary;
		}
		else
		{
			$boundary = 'AW'.chr(rand(65,91)).'------'.md5(uniqid(rand()));
		}

		$nparts = sizeof($this->mimeparts);

		// we have more than one attach
		if (is_array($this->mimeparts) && ($nparts > 1))
		{
			$this->headers["MIME-Version"] = "1.0";
			$this->headers["Content-Type"] = "multipart/mixed;" . self::CRLF . " boundary=\"$boundary\"";
			$this->headers["Content-Transfer-Encoding"] = "8bit";
			if (!empty($c_desc))
			{
				$this->headers["Content-Description"] = $c_desc;
			}
			$warning = self::WARNING.self::CRLF;

			// Since we are here, it means we do have attachments => body must become
			// and attachment too.
			if (!empty($this->body))
			{
				$this->attach(array(
					"data" => $this->body,
					"body" => 1,
					"contenttype" => self::TEXT,
					"encoding" => "8bit"
				));
			}

			// Now create the MIME parts of the email!
			for ($i=0; $i < $nparts; $i++)
			{
				if (!empty($this->mimeparts[$i]))
				{
					$msg .= self::CRLF."--".$boundary.self::CRLF.$this->mimeparts[$i].self::CRLF;
				}
			}

			$msg .= "--".$boundary."--".self::CRLF;
			$msg = $warning.$msg;
		}
		else
		{
			if (!empty($this->body))
			{
				$msg = $this->body;
			}
		}

		return $msg;
	}

	/**
	@attrib api=1 params=pos
	@param name required type=string
		Headers name
	@param value required type=string
		Headers value
	@comment
		Sets a header
	**/
	public function set_header($name,$value)
	{
		$this->headers[$name] = $value;

		if ("mimemessage" === $this->method)
		{
			$this->mimemessage->SetHeader($name, $value);
		}
	}

	/** Sends this mail message
		@attrib api=1 params=pos
		@comment
		@returns
		@errors
			throws awex_awmail_send if message sending fails
	**/
	public function send()
	{
		if ("phpmail" === $this->method)
		{
			$success = $this->gen_mail();
			if (!$success)
			{
				throw new awex_awmail_send("gen_mail() failed. ");
			}
		}
		elseif ("mimemessage" === $this->method)
		{
			$this->_mimemessage_send();
		}
	}

	private function _mimemessage_send()
	{
		if (!$this->is_multipart_html)
		{
			// add e-mail body text
			$this->mimemessage->AddQuotedPrintableTextPart($this->mimemessage->WrapText($this->body));
		}

		// Since the sender is AutomatWeb application server, sender header field must be specified separately
		// Sender address domain must have rDNS record pointing to sending server. To get through spam filters
		$this->mimemessage->SetHeader("Sender", $this->get_default_from_address());

		$error = $this->mimemessage->Send();
		if ($error)
		{
			throw new awex_awmail_send("Mail sending with mimemessage method failed: {$error}");
		}
	}

	//public use is DEPRECATED
	/**
	@attrib api=1
	@example
		$awm = new aw_mail();
		$awm->create_message(array(
			"froma" => $msg["mailfrom"],
			"subject" => $msg["subject"],
			"To" => $msg["target"],
			"body" => $message,
		if ($is_html)
		{
			$awm->htmlbodyattach(array(
				"data" => $message,
			));
		};
		$mimeregistry = get_instance("core/aw_mime_types");
		$awm->fattach(array(
			"path" => $object->prop("file"),
			"contenttype"=> $mimeregistry->type_for_file($object->name()),
			"name" => $object->name(),
		));
		$awm->gen_mail();
		$awm->clean();
	@comment
		Builds and sends mail message
		$this->headers["To"] must be set
	**/
	// public use is DEPRECATED
	function gen_mail()
	{
		$headers = "";
		$arguments = "";

		$email = $this->build_message();
		$to = $this->headers["To"];
		$subject = $this->headers["Subject"];

		if (empty($this->headers["Content-Type"]))
		{
			$ll = new languages();
			$this->set_header("Content-Type","text/plain; charset=\"". $this->charset ."\"");
		}

		unset($this->headers["To"]);//XXX: milleks?
		// why is this here? it will screw up sending to mailinglists - only the first mail will get the subject
		unset($this->headers["Subject"]);//XXX: milleks?

		// unique message id
		$this->set_header("Message-Id", $this->gen_message_id());

		// Since the sender is AutomatWeb application server, sender header field must be specified separately
		// Sender address domain must have rDNS record pointing to sending server. To get through spam filters
		$this->set_header("Sender", $this->get_default_from_address());

		// add reply-to
		if (empty($this->headers["Reply-To"]))
		{
			$this->set_header("Reply-To", $this->from);
		}

		// add date as required by RFC 5322
		if (empty($this->headers["Date"]))
		{
			$this->set_header("Date", date("D, j M Y H:i:s O"));
		}

		// add user specified headers
		foreach($this->headers as $name => $value)
		{
			if ($value)
			{
				$headers .= sprintf("%s: %s%s",$name,$value,self::CRLF);
			}
		}

		if (is_array($this->body_replacements))
		{
			foreach($this->body_replacements as $key => $val)
			{
				$email = str_replace($key,$val,$email);
			}
		}
		$this->bodytext = $email;

		return send_mail($to, $subject, $email, $headers, $arguments);
	}

	private function get_default_from_address()
	{
		// get server fqdn
		if (empty($_SERVER["SERVER_ADDR"]))
		{
			$base_url = new aw_uri(aw_ini_get("baseurl"));
			$host = $base_url->get_host();
		}
		else
		{
			$host = gethostbyaddr($_SERVER["SERVER_ADDR"]);
		}

		// get user name
		if (!empty($_ENV["APACHE_RUN_USER"]))
		{
			$user = $_ENV["APACHE_RUN_USER"];
		}
		else
		{
			$user = get_current_user();
		}

		$user = $user ? $user : "automatweb";
		$from_address = "{$user}@{$host}";
		return $from_address;
	}
}

/** Generic aw_mail exception **/
class awex_awmail extends aw_exception {}

/** Send method error **/
class awex_awmail_method extends awex_awmail {}

/** Parameter error **/
class awex_awmail_param extends awex_awmail {}

/** Attachment error **/
class awex_awmail_attach extends awex_awmail {}

/** Sending failed **/
class awex_awmail_send extends awex_awmail {}
