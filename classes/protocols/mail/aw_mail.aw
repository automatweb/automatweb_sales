<?php

// Thanks to Kartic Krishnamurthy <kaygee@netset.com> for ideas and sample code
// mail.aw - Sending and parsing mail. MIME compatible

// I am not too happy with the structure of this class. Parts of need to be redesigned and rewritten
// badly
// Minu unistus ( :) ) on selline, et kohe peale parsimist, voiks sellesama klassi abil
// teate kohe ka v2lja saata

// ideaalis peaks see edaspidi toetama ka teisi mailisaatmismeetodeid
// peale PHP mail funktsiooni

/*
@classinfo  maintainer=kristo
*/

define('X_MAILER',"AW Mail 2.0");
define('WARNING','This is a MIME encoded message');
define('OCTET','application/octet-stream');
define('TEXT','text/plain');
define('HTML','text/html');
define('INLINE','inline');
define('ATTACH','attachment');
define('CRLF',"\n");
define('BASE64','base64');

class aw_mail
{
	var $bounce;
	var $boundary;
	var $body_replacements;

	var $message; // siin hoiame teate erinevaid osasid
	var $mimeparts; // siin hoiame teate MIME osasid
	var $headers; // headerid
	////
	// !Konstruktor
	// argumendid
	// method(string) - mis meetodi abil meili saadame?
	function aw_mail($args = array())
	{
		$ll = new languages();
		define('CHARSET',$ll->get_charset());
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
	function clean($args = array())
	{
		$this->message = array();
		$this->headers = array();
		$this->mimeparts = array();
		$this->mimeparts[] = "";
		// by default php mail funktsioon
		$this->method = empty($args["method"]) ? "sendmail" : $args["method"];
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
		if ($part == "body")
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
			};
			$block = $this->mimeparts[$part];
		};

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
		};
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
			};
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
	function _parse_block($args = array())
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
			#print "#";
			// If we find an empty line, then we have all the headers and can continue with
			if ((preg_match("/^$/",$line)) && ($in_headers))
			{
				$in_headers = false;
			};

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
				};
			}
			// when we get here, then this means that we have reached the body of the message.
			// no further actions, we just fill the $body variable.
			else
			{
				$body .= $line . "\r\n";
			};
		}; // foreach

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
			};
		};

		$result = array(
			"headers" => $headers,
			"body" => $body,
			);

		return $result;
	}

	/**
	@attrib api=1 params=name
	@param froma optional type=string
		sender's address
	@param fromn optional type=string
		sender's name
	@param to required type=string
		mail to (adresses)
	@param cc optional type=string
		mail to (adresses)
	@param bcc optional type=string
		mail to (adresses)
	@param subject optional type=string
		mail subject
	@param headers required type=array
		additional headers
	@param body optional type=string
		mail body
	@param X-Mailer optional type=string default="AW Mail 2.0"
		mail version
	@example ${gen_mail}
	@comments
		Creates a new message
	**/
	function create_message($args = array())
	{

		if (is_array($args))
		{
			if (!empty($args["body"]))
			{
				$this->body = $args["body"];
				unset($args["body"]);
			}

			if (empty($args["X-Mailer"]))
			{
				$args["X-Mailer"] = X_MAILER;
			};


			$this->from = $args["froma"];
			$this->subject = $args["subject"];

			if (!empty($args["fromn"]))
			{
				$from = sprintf("%s <%s>",$args["fromn"],$args["froma"]);
				unset($args["fromn"]);
				unset($args["froma"]);
			}
			else
			{
				$from = $args["froma"];
				unset($args["froma"]);
			}
			$args["from"] = $from;

			foreach($args as $name => $value)
			{
				$uname = ucfirst($name);
				$this->headers[$uname] = $value;
			}
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
	function attach($args = array())
	{
		extract($args);
		if (empty($data))
		{
			return false;
		}

		if (empty($contenttype))
		{
			$contenttype = OCTET;
		}

		if (empty($encoding))
		{
			$encoding = BASE64;
		}

		if ($encoding == BASE64)
		{
			$emsg = base64_encode($data);
			$emsg = chunk_split($emsg,76,CRLF);
		}
		else
		{
			$emsg = $data;
		}

		$emsg = trim($emsg);


		if (preg_match("!^".TEXT."!i", $contenttype) && !preg_match("!;charset=!i", $contenttype))
		{
			$contenttype .= ";" . CRLF . " charset=".CHARSET ;
		}

		if ($args["body"])
		{
			if ($description)
			{
				$this->headers["Content-Description"] = $description;
			}

			if ($disp)
			{
				$this->headers["Content-Disposition"] = $disp;
			}

			$pref = "Content-Type: text/plain; charset=ISO-8859-1" . CRLF;
			$pref .= "Content-Transfer-Encoding: 8bit";


			//$this->headers["Content-Type"] = $contenttype;
			$this->headers["Content-Transfer-Encoding"] = $encoding;
			$this->mimeparts[0] = $pref . CRLF . $emsg;
		}
		else
		{
			$msg = sprintf("Content-Type: %sContent-Transfer-Encoding: %s%s%s%s",
						$contenttype.CRLF,
						$encoding.CRLF,
						(($description) ? "Content-Description: $description".CRLF:""),
						(($disp) ? "Content-Disposition: $disp".CRLF:""),
						CRLF.$emsg.CRLF);
			$this->mimeparts[] = $msg;
		};

		return sizeof($this->mimeparts);
	}

	// lauri muudetud 01.09.2001 -->
	/**Generates html stuff around html body
	@param body optional type=string
	@return string/html
	**/
	function gen_htmlbody($body, $heads = "")
	{
		return ((substr($body,0,6)=="<html>") || (strpos($body, "<!DOCTYPE") !== false))?$body:
			"<html><head><title></title>".$heads."</head><body>$body</body></html>";
	}

	/**
	@attrib api=1 params=name
	@param data required type=string
		html data
	@param heads optional type=string
		stuff inside html <head> tag
	@example ${gen_mail}
	@comments
		Defines an alternative html body
	**/
	function htmlbodyattach($args=array())
	{
		extract($args);
		// nii, kuidas seda siis teha?
		// tuleb teha juurde yx mime part, mille Content-Type: multipart/alternative;
		// sinna sisse paneme vana message body ja eraldatult uue html body.
		$boundary='AW'.chr(rand(65,91)).'--'.md5(uniqid(rand()));
		$atc="Content-Type: multipart/alternative;".CRLF." boundary=\"$boundary\"".CRLF.CRLF;

		$plain = strtr($this->body,array("<br />"=>"\r\n","<br />"=>"\r\n","</p>"=>"\r\n","</p>"=>"\r\n"));
		$plain = $this->strip_html($plain);

		$atc.= "--".$boundary. CRLF;
		$atc.="Content-Type: text/plain; charset=".CHARSET . CRLF;
		$atc.="Content-Transfer-Encoding: 8bit".CRLF.CRLF.$plain.CRLF.CRLF;
		$atc.="--".$boundary.CRLF;

		$data = str_replace("\\\"","\"",$data);
		$atc.="Content-Type: text/html; charset=".CHARSET . CRLF;
		$atc.="Content-Transfer-Encoding: 8bit".CRLF.CRLF.$this->gen_htmlbody($data, $heads).CRLF.CRLF;

		$atc .= "--".$boundary."--".CRLF;

		// see peab kindlalt olema esimene 2tt2ts.
		$this->mimeparts=array_merge(array($atc),$this->mimeparts);
		unset($this->body);
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
	@param path required type=string
		path to file
	@param content optional type=string
		if set, path is ignored
	@param description optional type=string
		Content-Description
	@param contenttype optional type=string default="application/octet-stream"
		file content type
	@param encoding optional type=string default="base64"
		file encoding
	@param disp optional type=string
		content-disposition
	@param name optional type=string
		string, used as file name, if content is set, this must be set too
	@example ${gen_mail}
	@return int/number of mimeparts
	@comment
		Attaches a file to the message
	**/
	function fattach($args = array())
	{
		extract($args);
		if (!$contenttype)
		{
			$contenttype = OCTET;
		};

		if (substr($contenttype,0,4) == "text")
		{
			$encoding = "8bit";
		};

		if (!$encoding)
		{
			$encoding = BASE64;
		};

		if ($content == "")
		{
			// read the fscking file
			$fp = fopen($path,"rb");
			if (!$fp)
			{
				//print "attach failed<br />";
				return false; // fail
			}

			if (!$name)
			{
				$name = basename($path);
			};
			$data = fread($fp, filesize($path));
		}
		else
		{
			$data = $content;
		}

		$contenttype .= ";" . CRLF . " name=\"".$name . "\"";
		return $this->attach(array(
				"data" => $data,
				"description" => $description,
				"contenttype" => $contenttype,
				"encoding" => $encoding,
				"disp" => $disp,
			));
	}


	//// Genereerib message_id headeri
	function gen_message_id()
	{
		$id = '<AW' . chr(rand(65,91)) . chr(rand(65,91)) . md5(uniqid(rand())) . "@automatweb>";
		return $id;
	}

	function build_message($args = array())
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
			//$c_ver = "MIME-Version: 1.0".CRLF;
			$this->headers["MIME-Version"] = "1.0";
			//$this->headers["Content-Type"] = "multipart/mixed;" . CRLF . " boundary=\"$boundary\"";
			$this->headers["Content-Type"] = "multipart/mixed;" . CRLF . " boundary=\"$boundary\"";
			$this->headers["Content-Transfer-Encoding"] = "8bit";
			if ($c_desc)
			{
				$this->headers["Content-Description"] = $c_desc;
			};
			$warning = WARNING.CRLF;

			// Since we are here, it means we do have attachments => body must become
			// and attachment too.
			if (!empty($this->body)) {
				$this->attach(array(
					"data" => $this->body,
					"body" => 1,
					"contenttype" => TEXT,
					"encoding" => "8bit",
				));
			};

			// Now create the MIME parts of the email!
			for ($i=0; $i < $nparts; $i++)
			{
				if (!empty($this->mimeparts[$i]))
				{
					$msg .= CRLF."--".$boundary.CRLF.$this->mimeparts[$i].CRLF;
				};
			};

			$msg .= "--".$boundary."--".CRLF;
			$msg = $warning.$msg;
		}
		else
		{
			if (!empty($this->body))
			{
				$msg = $this->body;
			};
		};
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
	function set_header($name,$value)
	{
		$this->headers[$name] = $value;
	}

	/**
	@attrib api=1
	@example
		$awm = get_instance("protocols/mail/aw_mail");
		$awm->create_message(array(
			"froma" => $msg["mailfrom"],
			"subject" => $msg["subject"],
			"To" => $msg["target"],
			//"Sender"=>"bounces@struktuur.ee",
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
	function gen_mail()
	{
		$email = "";
		$headers = "";
		$arguments = "";
		if($this->bounce)
		{
			$arguments = "-f".$this->bounce;
		}

		$email .= $this->build_message();
		$to = $this->headers["To"];
		$subject = $this->headers["Subject"];

		if (empty($this->headers["Content-Type"]))
		{
			$ll = new languages();
			$this->set_header("Content-Type","text/plain; charset=\"".$ll->get_charset()."\"");
		}

		unset($this->headers["To"]);
		// why is this here? it will screw up sending to mailinglists - only the first mail will get the subject
		unset($this->headers["Subject"]);
		$this->set_header("Message-Id",$this->gen_message_id());
		//$this->set_header("Sender",$this->headers["From"]);
		foreach($this->headers as $name => $value)
		{
			if ($value)
			{
				$headers .= sprintf("%s: %s%s",$name,$value,CRLF);
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
		return send_mail($to,$subject,$email,$headers,$arguments);
	}
}

?>
