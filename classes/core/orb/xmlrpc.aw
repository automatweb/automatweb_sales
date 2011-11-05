<?php

class xmlrpc extends aw_template
{
	var $allowed = array("I4","BOOLEAN","STRING", "DOUBLE","DATETIME.ISO8601","BASE64", "STRUCT", "ARRAY");

	function xmlrpc()
	{
		$this->init("");
	}

	////
	// !sends the request to the remote server, retrieves the response and decodes it
	function do_request($arr)
	{
		$xml = $this->make_request_xml($arr);
		if (aw_global_get("xmlrpc_dbg"))
		{
			// /*~AWdbg*/ echo "sending request = <pre>", htmlspecialchars($xml),"</pre> <br />";
		}

		$this->no_errors = !empty($arr["no_errors"]);
		$resp = $this->send_request(array(
			"server" => $arr["remote_host"],
			"port" => 80,
			// well, it would be nice to get rid of that separate file you see
			"handler" => "/xmlrpc.aw",
			"request" => $xml,
			"session" => $arr["remote_session"]
		));
		if (aw_global_get("xmlrpc_dbg"))
		{
			// /*~AWdbg*/ echo "got response = <pre>", htmlspecialchars($resp),"</pre> <br />";
		}
		$rv = $this->decode_response($resp);
		return $rv;
	}

	////
	// !creates the xml for the request.
	function make_request_xml($arr)
	{
		extract($arr);
		$xml  = "<?xml version=\"1.0\"?>\n";
		$xml .= "<methodCall>\n";
		$xml .= "\t<methodName>".$class."::".$action."</methodName>\n";

		if (isset($params) && is_array($params) && count($params))
		{
			$xml .= "\t<params>\n";
			$xml .= "\t\t<struct>\n";

			foreach($params as $name => $value)
			{
				$xml .= "\t\t\t<member>\n";
				$xml .= "\t\t\t\t<name>$name</name>\n";
				if (is_array($value))
				{
					$value = aw_serialize($value, SERIALIZE_XMLRPC);
				}
				$xml .= "\t\t\t\t<value>$value</value>\n";
				$xml .= "\t\t\t</member>\n";
			}

			$xml .= "\t\t</struct>\n";
			$xml .= "\t</params>\n";
		}

		$xml .= "</methodCall>\n";
		return $xml;
	}

	function decode_response($xml)
	{
		if (aw_global_get("xmlrpc_dbg"))
		{
			// /*~AWdbg*/ echo "resp xml = <pre>", htmlspecialchars($xml),"</pre> <br />---------------------------<br/>";
		}
		$result = array();
		$parser = xml_parser_create();
		xml_parse_into_struct($parser, $xml, $this->vals, $tags);
		$err = xml_get_error_code($parser);
		if ($err)
		{
			if ($this->no_errors)
			{
				return false;
			}
			else
			{
				$lines = explode("\n", $xml);
				// echo htmlspecialchars($lines[xml_get_current_line_number($parser)-1])." <br>";
				$this->raise_error("ERR_XML_PARSER_ERROR",sprintf(t("Viga XML-RPC p2ringu vastuse dekodeerimisel: %s on line %s! %s"), xml_error_string($err),xml_get_current_line_number($parser), $xml), true,false);
			}
		}
		xml_parser_free($parser);

		foreach($this->vals as $k => $v)
		{
			$this->vals[$k]["value"] = isset($v["value"]) ? $v["value"] : "";
		}

		reset($this->vals);
		list(, $tmp) = each($this->vals);
//		echo "expect methodresponse open got $tmp[tag] $tmp[type] <br />";
		list(, $is_err) = each($this->vals);
		if ($is_err["tag"] === "FAULT")
		{
//			echo "in fault: got $tmp[tag] $tmp[type] <br />";
			list(, $tmp) = each($this->vals);	// value open
//			echo "in fault: expect value open got $tmp[tag] $tmp[type] <br />";
			list(, $tmp) = each($this->vals);	// struct open
//			echo "in fault: expect struct open got $tmp[tag] $tmp[type] <br />";

			// faultcode member
			list(, $tmp) = each($this->vals);	// member open
//			echo "in fault: expect member open got $tmp[tag] $tmp[type] <br />";
			list(, $faultcode_name_v) = each($this->vals);	// name complete
//			echo "in fault: expect name complete got $faultcode_name_v[tag] $faultcode_name_v[type] <br />";
			list(, $tmp) = each($this->vals);	// value open
//			echo "in fault: expect value open got $tmp[tag] $tmp[type] <br />";
			// chomp value
			$faultcode = $this->_proc_unser_data();
			list(, $tmp) = each($this->vals);	// value close
//			echo "in fault: expect value close got $tmp[tag] $tmp[type] <br />";
			list(, $tmp) = each($this->vals);	// member close
//			echo "in fault: expect member close got $tmp[tag] $tmp[type] <br />";

			// faultstring
			list(, $tmp) = each($this->vals);	// member open
//			echo "in fault: expect member open got $tmp[tag] $tmp[type] <br />";
			list(, $faultstring_name) = each($this->vals);	// name complete
//			echo "in fault: expect name complete got $faultstring_name[tag] $faultstring_name[type] <br />";
			list(, $tmp) = each($this->vals);	// value open
//			echo "in fault: expect value open got $tmp[tag] $tmp[type] <br />";
			// chomp value
			$faultstring = $this->_proc_unser_data();
			list(, $tmp) = each($this->vals);	// value close
//			echo "in fault: expect value close got $tmp[tag] $tmp[type] <br />";
			list(, $tmp) = each($this->vals);	// member close
//			echo "in fault: expect member close got $tmp[tag] $tmp[type] <br />";

			list(, $tmp) = each($this->vals);	// struct close
//			echo "in fault: expect member close got $tmp[tag] $tmp[type] <br />";
			list(, $tmp) = each($this->vals);	// value close
//			echo "in fault: expect value close got $tmp[tag] $tmp[type] <br />";
			list(, $tmp) = each($this->vals);	// value close
//			echo "in fault: expect fault close got $tmp[tag] $tmp[type] <br />";
			list(, $tmp) = each($this->vals);	// value close
//			echo "in fault: expect methodresponse close got $tmp[tag] $tmp[type] <br />";
			$errs = aw_ini_get("errors");
			$faultcodestr = $errs[$faultcode][def];
			$this->raise_error(ERR_XMLRPC_FAULT, "Got remote error!<br /><br /> code: $faultcodestr ($faultcode)<br /> string: $faultstring", true, false);
		}
		else
		{
//			echo "in resp: expect params open got $is_err[tag] $is_err[type] <br />";
			list(, $tmp) = each($this->vals);	// param open
//			echo "in resp: expect param open got $tmp[tag] $tmp[type] <br />";
			list(, $tmp) = each($this->vals);	// value open
//			echo "in resp: expect value open got $tmp[tag] $tmp[type] <br />";

			$retval = $this->_proc_unser_data();

			list(, $tmp) = each($this->vals);	// value close
//			echo "in resp: expect value close got $tmp[tag] $tmp[type] <br />";
			list(, $tmp) = each($this->vals);	// param close
//			echo "in resp: expect param close got $tmp[tag] $tmp[type] <br />";
			list(, $tmp) = each($this->vals);	// params close
//			echo "in resp: expect params close got $tmp[tag] $tmp[type] <br />";

			list(, $tmp) = each($this->vals);	// value close
//			echo "in resp: expect methodresponse close got $tmp[tag] $tmp[type] <br />";

			return $retval;
		}
		return false;
	}

	////
	// !Sends an RPC query to a server and returns the results
	// arguments:
	// server(string)
	// port(int)
	// handler(string) - millise skriptile andmed POST-ida
	// request(text) - xml request to send
	// session - the session id to send
	function send_request($args = array())
	{
		extract($args);
		if (substr($server,0,7) === "http://" || substr($server,0,8) === "https://")
		{
			$server = substr($server,7);
		}

		$fp = fsockopen($server, $port, $this->errno, $this->errstr,5);
		$op = "POST $handler HTTP/1.0\r\n";
		$op .= "User-Agent: AutomatWeb\r\n";
		$op .= "Host: $server\r\n";
		$op .= "Content-Type: text/xml\r\n";
		if ($session != "")
		{
			$op.="Cookie: automatweb=$session\r\n";
		}
		$op .= "Content-Length: " . strlen($request) . "\r\n\r\n";
		$op .= $request;

		if (!fputs($fp, $op, strlen($op)))
		{
			$this->errstr="Write error";
			return 0;
		}
		$ipd = "";
		while($data=fread($fp, 32768))
		{
			$ipd.=$data;
		}

		fclose($fp);
		list($headers, $data) = explode("\r\n\r\n", $ipd);
		return $data;
	}

	////
	// !decodes an xmlrpc request - request data comes from global scope
	// returns array with members:
	// class - the class of the request
	// action - the action of the request
	// params - array of parameters for request
	function decode_request($arr = array())
	{
		if (empty($arr["xml"]) and !empty($GLOBALS["HTTP_RAW_POST_DATA"]))
		{
			$xml = $GLOBALS["HTTP_RAW_POST_DATA"];
		}
		elseif (!empty($arr["xml"]))
		{
			$xml = $arr["xml"];
		}
		else
		{
			return "";//TODO: mida siin?
		}

		$result = array();

		$parser = xml_parser_create();
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
		xml_parse_into_struct($parser, $xml, $values, $tags);
		xml_parser_free($parser);

		aw_global_set("__is_rpc_call", true);
		aw_global_set("__rpc_call_type", "xmlrpc");
		$this->i = 0;
		$res = $this->req_decode_xml($values);
		return $res;
	}

	function req_decode_xml($values)
	{
		$result = array();
		$name = "";
		$in_value = false;

		$continue = $this->i < sizeof($values);
		while ($continue)
		{
			$token = $values[$this->i++];

			if (($token["tag"] === "methodName") && ($token["type"] === "complete"))
			{
				if (strpos($token["value"],"::"))
				{
					list($result["class"], $result["action"]) = explode("::", $token["value"]);
				}
				else
				{
					list($result["class"], $result["action"]) = explode(".", $token["value"]);
				};
			};

			if ($in_value && ($token["type"] === "complete") )
			{
				$result["params"][$name] = $token["value"];
				$in_value = false;
			}

			if ($in_value && ($token["tag"] == "struct") )
			{
				$in_value = false;
				$tmp = $this->req_decode_xml($values);
				if (empty($name))
				{
					$result["params"] = $tmp["params"];
				}
				else
				{
					$result["params"][$name] = $tmp["params"];
				}
			}

			if (($token["tag"] === "base64") && ($token["type"] === "complete"))
			{
				$result["params"][$name] = base64_decode($token["value"]);
			}

			if (($token["tag"] === "struct") && ($token["type"] === "close"))
			{
				return $result;
			}

			if ($token["tag"] === "member")
			{
				if ($token["type"] === "open")
				{
					//print "w00p!";
				}
			}

			if (($token["tag"] === "name") && ($token["type"] === "complete"))
			{
				$name = $token["value"];
			}

			if (($token["tag"] === "value") && ($token["type"] === "complete"))
			{
				$result["params"][$name] = $token["value"];
			}

			if (($token["tag"] === "value") && ($token["type"] === "open"))
			{
				$in_value = true;
			}

			$continue = $this->i < sizeof($values);
		}

		return $result;
	}

	function encode_return_data($dat)
	{
		$xml  = "<?xml version=\"1.0\" encoding=\"".languages::USER_CHARSET."\"?>\n";
		$xml .= "<methodResponse>\n";
		$xml .= "\t<params>\n";
		$xml .= "\t\t<param>\n";
		$dat = aw_serialize($dat, SERIALIZE_XMLRPC);
		$xml .= "\t\t\t<value>\n".$dat."\n</value>\n";
		$xml .= "\t\t</param>\n";
		$xml .= "\t</params>\n";
		$xml .= "</methodResponse>\n";
		return $xml;
	}

	function handle_error($code, $msg)
	{
		$xml  = "<?xml version=\"1.0\"?>\n";
		$xml .= "<methodResponse>\n";
		$xml .= "\t<fault>\n";
		$xml .= "\t\t<value>\n";
		$xml .= aw_serialize(array("faultCode" => $code, "faultString" => $msg),SERIALIZE_XMLRPC);
		$xml .= "\t\t</value>\n";
		$xml .= "\t</fault>\n";
		$xml .= "</methodResponse>\n";
		automatweb::$result->set_data($xml);
		automatweb::http_exit(http::STATUS_BAD_REQUEST);
	}

	function xmlrpc_serialize($val, $level = 0)
	{
		$pre = "";
		$pad = str_repeat("  ", $level);
		// From PHP manual:
		// ---------------------------------------------------------------------------------------
		// Never use gettype() to test for a certain type, since the returned string may
		// be subject to change in a future version. In addition, it is slow too, as it involves
		// string comparision.
		// ---------------------------------------------------------------------------------------
		// Instead, use the is_* functions.
		// ---------------------------------------------------------------------------------------

		// how do I encode binaries, I think I need to make them objects
		if (is_bool($val))
		{
			$pre .= $pad.'<boolean>'.($val === true ? 1 : 0)."</boolean>\n";
		}
		elseif (is_integer($val))
		{
			$pre .= $pad."<i4>$val</i4>\n";
		}
		elseif (is_float($val))
		{
			$pre .= $pad."<double>$val</double>\n";
		}
		elseif (is_string($val))
		{
			$val = str_replace("<", "&lt;", $val);
			$val = str_replace("&", "&amp;", $val);
			$val = trim($val);
			$pre .= $pad."<string>$val</string>\n";
		}
		elseif (is_array($val))
		{
			if ( 1 == sizeof($val) && !empty($val["base64"]))
			{
				$pre .= $pad."<base64>".base64_encode($val["base64"])."</base64>\n";
			}
			else
			{
				$pre .= $pad."<struct>\n";
				$level++;
				foreach($val as $k => $v)
				{
					$pre .= $pad."  <member>\n";
					$level++;
					$pre .= $pad."    <name>".$k."</name>\n";
					$pre .= $pad."    <value>\n";
					$pre .= $this->xmlrpc_serialize($v, $level+1);
					$pre .= $pad."    </value>\n";
					$pre .= $pad."  </member>\n";
					$level--;
				}
				$pre .= $pad."</struct>\n";
				$level --;
			};
		}
		else
		{
			// ignore all unknown types
			$pre .= $pad."<string></string>\n";
		};

		return $pre;
	}

	function xmlrpc_unserialize($str)
	{
		$pars = xml_parser_create();
		$this->vals = array();
		$index = array();
		xml_parse_into_struct($pars, $str, $this->vals, $index);
		xml_parser_free($pars);

		reset($this->vals);
		return $this->_proc_unser_data();
	}

	function _proc_unser_data()
	{
		while ($v = $this->_expect(false,true))
		{
			switch ($v["tag"])
			{
				case "I4":
					return (int)$v["value"];
					break;

				case "BOOLEAN":
					return (bool)$v["value"];
					break;

				case "STRING":
					$str = $v["value"];
					// collect all cdata as well
					do {
						list(, $tmp) = each($this->vals);
						$cont = false;
						if ($tmp["type"] === "cdata" && $tmp["tag"] === "STRING")
						{
							$str.=$tmp["value"];
							$cont = true;
						}
					} while ($cont);
					prev($this->vals);
					return str_replace("&lt;", "<", $str);
					break;

				case "DOUBLE":
					return (double)$v["value"];
					break;

				case "DATETIME.ISO8601":
					return strtotime($v["value"]);
					break;

				case "BASE64":
					return base64_decode($v["value"]);
					break;

				case "STRUCT":
					$ret = array();
					$this->_expect("MEMBER");// open member
					$is_mem = true;
					do {
						$name_v = $this->_expect("NAME");// name complete
						$key = $name_v["value"];
						$this->_expect("VALUE");  // value open
						$value = $this->_proc_unser_data();		 // eat value value
						$this->_expect("VALUE");  // value close
						$this->_expect("MEMBER"); // member close
						$mem_o = $this->_expect(); // try member open
						if ($mem_o["tag"] !== "MEMBER")
						{
							// we just ate struct close tag, so no need to rewind
							$is_mem = false;
						}
						$ret[$key] = $value;
					} while ($is_mem);
					return $ret;
					break;

				case "ARRAY":
					$ret = array();
					each($this->vals); // open data
					$in_ar = true;
					do {
						list(, $tmp) = each($this->vals); // try open value
						if ($tmp["tag"] === "VALUE")
						{
							// got data close, that means end of array
							$in_ar = false;
						}
						else
						{
							// chomp value
							$ret[] = $this->_proc_unser_data();
							each($this->vals); // close value
						}
					} while ($in_ar);
					each($this->vals); // close array
					return $ret;
					break;
			}
		}
	}

	////
	// !skips rows in $this->vals until it finds a non-whitespace tag and returns it
	// if $tag is specified, complains if the tag does not match
	function _expect($tag = false, $return_cdata = false)
	{
		do
		{
			list($k, $tmp) = each($this->vals);
			$is_sp = $tmp["type"] === "cdata" && trim($tmp["value"]) == "";
			if ($return_cdata)
			{
				$is_sp = $is_sp && in_array($tmp["tag"], $this->allowed);
			}
		}
		while ($is_sp);

		if ($tag && $tmp["tag"] != $tag)
		{
			// /*~AWdbg*/ echo "error! _expected $tag, got $tmp[tag] ,k = $k tmp = <pre>",var_dump($tmp),"</pre>";
			die();
		}
		return $tmp;
	}
}
