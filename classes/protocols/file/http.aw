<?php

class http implements protocol_interface
{
	const STATUS_CONTINUE = 100;
	const STATUS_SWITCHING_PROTOCOLS = 101;
	const STATUS_OK = 200;
	const STATUS_CREATED = 201;
	const STATUS_ACCEPTED = 202;
	const STATUS_NO_CONTENT = 204;
	const STATUS_RESET_CONTENT = 205;
	const STATUS_PARTIAL_CONTENT = 206;
	const STATUS_MULTIPLE_CHOICES = 300;
	const STATUS_MOVED_PERMANENTLY = 301;
	const STATUS_FOUND = 302; // originally "Moved Temporarily"
	const STATUS_SEE_OTHER = 303;
	const STATUS_NOT_MODIFIED = 304;
	const STATUS_TEMPORARY_REDIRECT = 307;
	const STATUS_RESUME_INCOMPLETE = 308;
	const STATUS_BAD_REQUEST = 400;
	const STATUS_UNAUTHORIZED = 401;
	const STATUS_FORBIDDEN = 403;
	const STATUS_NOT_FOUND = 404;
	const STATUS_METHOD_NOT_ALLOWED = 405;
	const STATUS_NOT_ACCEPTABLE = 406;
	const STATUS_REQUEST_TIMEOUT = 408;
	const STATUS_CONFLICT = 409;
	const STATUS_GONE = 410;
	const STATUS_LENGTH_REQUIRED = 411;
	const STATUS_PRECONDITION_FAILED = 412;
	const STATUS_REQUEST_TOO_LARGE = 413;
	const STATUS_URI_TOO_LONG = 414;
	const STATUS_UNSUPPORTED_MEDIA_TYPE = 415;
	const STATUS_RANGE_NOT_SATISFIABLE = 416;
	const STATUS_EXPECTATION_FAILED = 417;
	const STATUS_TEAPOT = 418;
	const STATUS_SERVER_ERROR = 500;
	const STATUS_NOT_IMPLEMENTED = 501;
	const STATUS_BAD_GATEWAY = 502;
	const STATUS_SERVICE_UNAVAILABLE = 503;
	const STATUS_GATEWAY_TIMEOUT = 504;

	private static $status_strings = array(
		self::STATUS_CONTINUE => "100 Continue",
		self::STATUS_SWITCHING_PROTOCOLS => "101 Switching Protocols",
		self::STATUS_OK => "200 OK",
		self::STATUS_CREATED => "201 Created",
		self::STATUS_ACCEPTED => "202 Accepted",
		self::STATUS_NO_CONTENT => "204 No Content",
		self::STATUS_RESET_CONTENT => "205 Reset Content",
		self::STATUS_PARTIAL_CONTENT => "206 Partial Content",
		self::STATUS_MULTIPLE_CHOICES => "300 Multiple Choices",
		self::STATUS_MOVED_PERMANENTLY => "301 Moved Permanently",
		self::STATUS_FOUND => "302 Found",
		self::STATUS_SEE_OTHER => "303 See Other",
		self::STATUS_NOT_MODIFIED => "304 Not Modified",
		self::STATUS_TEMPORARY_REDIRECT => "307 Temporary Redirect",
		self::STATUS_RESUME_INCOMPLETE => "308 Resume Incomplete",
		self::STATUS_BAD_REQUEST => "400 Bad Request",
		self::STATUS_UNAUTHORIZED => "401 Unauthorized",
		self::STATUS_FORBIDDEN => "403 Forbidden",
		self::STATUS_NOT_FOUND => "404 Not Found",
		self::STATUS_METHOD_NOT_ALLOWED => "405 Method Not Allowed",
		self::STATUS_NOT_ACCEPTABLE => "406 Not Acceptable",
		self::STATUS_REQUEST_TIMEOUT => "408 Request Timeout",
		self::STATUS_CONFLICT => "409 Conflict",
		self::STATUS_GONE => "410 Gone",
		self::STATUS_LENGTH_REQUIRED => "411 Length Required",
		self::STATUS_PRECONDITION_FAILED => "412 Precondition Failed",
		self::STATUS_REQUEST_TOO_LARGE => "413 Request Entity Too Large",
		self::STATUS_URI_TOO_LONG => "414 Request-URI Too Long",
		self::STATUS_UNSUPPORTED_MEDIA_TYPE => "415 Unsupported Media Type",
		self::STATUS_RANGE_NOT_SATISFIABLE => "416 Requested Range Not Satisfiable",
		self::STATUS_EXPECTATION_FAILED => "417 Expectation Failed",
		self::STATUS_TEAPOT => "418 I'm a teapot",
		self::STATUS_SERVER_ERROR => "500 Internal Server Error",
		self::STATUS_NOT_IMPLEMENTED => "501 Not Implemented",
		self::STATUS_BAD_GATEWAY => "502 Bad Gateway",
		self::STATUS_SERVICE_UNAVAILABLE => "503 Service Unavailable",
		self::STATUS_GATEWAY_TIMEOUT => "504 Gateway Timeout",
	);

	var $cookie;
	var $last_request_headers = array();
	var $socket;

	public function name()
	{
		return "HTTP/1.1";
	}

	/**
		@attrib api=1 params=pos
		@param code type=int
		@comment
		@returns string
		@errors
			throws awex_param if invalid code
	**/
	public static function get_status_string($code)
	{
		if (isset(self::$status_strings[$code]))
		{
			return self::$status_strings[$code];
		}
		else
		{
			throw new awex_param("Invalid status code parameter '{$code}'");
		}
	}


	/**
	@attrib api=1 params=pos
	@param url required type=string
		url, with all the data needed for HTTP get request
	@return array
		data returned after request
	@example
		$http = get_instance("protocols/file/http");
		$this->content = $http->get($this->url);
		$this->headers = $http->get_headers();
	@comment
		gets requested data from url
	@errors
		throws awex_socket_timeout
	**/
	function get($url, $sess = null, $cook_name = "automatweb")
	{
		$max_len = 0;
		$data = parse_url($url);

		$host = !empty($data["host"]) ? $data["host"] : substr(aw_ini_get("baseurl"), 0, -1);
		$host = str_replace("http://", "", $host);
		$host = str_replace("https://", "", $host);

		$port = (!empty($data["port"]) ? $data["port"] : 80);
		$fragment = (!empty($data["fragment"])) ? "#".$data["fragment"] : "";

		$y_url = $data["path"].(!empty($data["query"]) ? "?".$data["query"] : "").$fragment;
		if ($y_url == "")
		{
			$y_url = "/";
		}

		$req  = "GET $y_url HTTP/1.0\r\n";
		$req .= "Host: ".$host.($port != 80 ? ":".$port : "")."\r\n";
		$req .= "User-agent: AW-http-fetch\r\n";
		if ($sess !== null)
		{
			$req .= "Cookie: $cook_name=".$sess."\r\n";
		}
		$req .= "\r\n\r\n";
		$socket = new socket(array(
			"host" => $host,
			"port" => $port
		));
		$socket->write($req);
		$ipd = "";
		$data = "";
		$len = null;

		while($data = $socket->read(10000))
		{
			$ipd .= $data;

			if (preg_match("/Content-Length: (\d+)/ims", $data, $mt))
			{
				$max_len = $mt[1];
			}

			$close_on_len = (strpos($data, "Connection: close") !== false);

			list($tmp_headers,$tmp_data) = explode("\r\n\r\n",$ipd,2);
			if ($close_on_len && $max_len && strlen($tmp_data) >= $max_len)
			{
				break;
			}
		}

		list($headers,$data) = explode("\r\n\r\n",$ipd,2);
		$this->last_request_headers = $headers;

		if (strpos($headers, "HTTP/1.1 302 Found") !== false)
		{
			if (preg_match("/Location: (.*)$/imsU", $headers, $mt))
			{
				$loc = trim($mt[1]);

				if ($loc === $url)
				{
					throw new awex_http_redirect("The server '{$host}' responded with an invalid redirection status (requested to redirect '{$url}' to '{$loc}').", 1);
				}

				// make full url from location
				if ($loc{0} === "/")
				{
					$loc = "http://".$host.$loc;
				}
				elseif ($loc{0} === "?")
				{
					$tmp = $url;
					$qpos = strpos($url, "?");
					if ($qpos)
					{
						$tmp = substr($tmp, 0, $qpos);
					}

					$loc = $tmp.$loc;
				}

				if ($loc === $url)
				{
					throw new awex_http_redirect("The server '{$host}' responded with an invalid redirection status (requested to redirect '{$url}' to '{$loc}').", 2);
				}

				$pu = parse_url($loc);
				if (!$pu["scheme"])
				{
					if (substr($url, -1) === "/")
					{
						$loc = $url.$loc;
					}
					else
					{
						$loc = dirname($url)."/".$loc;
					}
				}

				if ($loc === $url)
				{
					throw new awex_http_redirect("The server '{$host}' responded with an invalid redirection status (requested to redirect '{$url}' to '{$loc}').", 3);
				}

				return $this->get($loc, $sess, $cook_name);
			}
		}

		return $data;
	}

	/**
	@attrib api=1
	@return $this->last_request_headers
	@example ${get}
	@comment
		returns last requested headers
	**/
	function get_headers()
	{
		return $this->last_request_headers;
	}

	/**
	@attrib api=1
	@return string/content type
	@comment
		returns content type, if not in headers, returns "text/html"
	**/
	function get_type()
	{
		$headers = explode("\n", $this->last_request_headers);

		$ct = "text/html";
		foreach($headers as $hd)
		{
			if (preg_match("/Content\-Type\: (.*)/", $hd, $mt))
			{
				$ct = $mt[1];
			}
		}

		return $ct;
	}

	/**
	@attrib api=1 params=name
	@param host required type=string
		host URL
	@param sessid optional type=string
		sets cookie value to sessid
	@param silent optional type=bool
		object id
	@example
		$awt = get_instance("protocols/file/http");
		$awt->handshake(array(
			"host" => $url,
			"sessid" => $evnt["sessid"]
		));
		if ($evnt["uid"] && $evnt["password"])
			$awt->login(array(
				"host" => $url,
				"uid" => $evnt["uid"],
				"password" => $evnt["password"]
			));
		else	$awt->login_hash(array(
				"host" => $url,
				"uid" => $evnt["uid"],
				"hash" => $evnt["auth_hash"],
			));
		$req = $awt->do_send_request(array(
			"host" => $url,
			"req" => substr($ev_url,strlen("http://")+strlen($url)))
		);
		$awt->logout(array("host" => $url));

	@return cookie, if sessid is not set
	**/
	function handshake($args = array())
	{
		extract($args);
		if (!isset($args["silent"]))
		{
			$silent = true;
		}
		$socket = new socket();
		if (substr($host,0,7) === "http://")
		{
			$host = substr($host,7);
		};
		$socket->open(array(
			"host" => $host,
			"port" => 80,
		));

		if (!empty($args["sessid"]))
		{
			$this->cookie = $args["sessid"];
			return;
		}
		// what if remote host uses a different "ext"?
		$op = "HEAD /orb.".AW_FILE_EXT."?class=remote_login&action=getcookie HTTP/1.0\r\n";
		$op .= "Host: $host\r\n\r\n";

		if (!$silent)
		{
			print "<pre>";
			print "Acquiring session  \nop = ".htmlentities($op)."\n";
			flush();
		}

		$socket->write($op);

		$ipd="";

		while($data = $socket->read())
		{
			$ipd .= $data;
		}

		if (preg_match("/automatweb=(\w+?);/",$ipd,$matches))
		{
			$cookie = $matches[1];
		}
		else
		{
			$cookie = "";
		}

		$this->cookie = $cookie;

		if (!$silent)
		{
			print "Got session, ID is $cookie\n";
			print "ipd = ".htmlentities($ipd)."</pre>";
			flush();
		}
		return $cookie;
	}

	/**
	@attrib api=1 params=pos
	@param id required type=oid
		object id
	@return array(prop("server") , $this->cookie)
	@comment
		object's props(server, login_uid, login_password, server) must be set
	**/
	function login_from_obj($id)
	{
		$ob = new object($id);
		$this->handshake(array(
			"silent" => true,
			"host" => $ob->prop("server"),
		));

		$this->login(array(
			"host" => $ob->prop("server"),
			"uid" => $ob->prop("login_uid"),
			"password" => $ob->prop("login_password"),
			"silent" => true
		));

		return array($ob->prop("server"),$this->cookie);
	}

	/**
	@attrib api=1 params=name
	@param host required type=string
		Server URL
	@param uid required type=uid
		User id
	@param password required type=string
		password
	@param silent optional type=bool
		if not set, prints out the request
	@example ${handshake}
	@comment
		Sends login request
	**/
	function login($args = array())
	{
		extract($args);
		$cookie = $this->cookie;
		$socket = new socket();
		if (substr($host,0,7) === "http://")
		{
			$host = substr($host,7);
		}
		$socket->open(array(
			"host" => $host,
			"port" => 80,
		));

		$request = "uid=$uid&password=$password&class=users&action=login";

		$op = "GET /orb.".AW_FILE_EXT."?$request HTTP/1.0\r\n";
		$op .= "Host: $host\r\n";
		$op .= "Cookie: automatweb=$cookie\r\n";
		$op .= "Referer: http://$host/login.".AW_FILE_EXT."\r\n\r\n";
		if (!$silent)
		{
			print "<pre>";
			echo "Logging in $host op = ",htmlentities($op),"\n";
		}

		$socket->write($op);
		$ipd = "";
		while($data = $socket->read())
		{
			$ipd .= $data;
		}
		$this->socket = $socket;

		if (!$silent)
		{
			print "Succeeded? Server returned ".htmlentities($ipd)."\n";
			print "</pre>";
			flush();
		}
	}

	/**
	@attrib api=1 params=name
	@param host required type=string
		Server URL
	@param uid required type=uid
		User id
	@param hash required type=oid
		hash value
	@example ${handshake}
	@comment
		Sends login request
	**/
	function login_hash($args = array())
	{
		extract($args);
		if (substr($host,0,7) === "http://")
		{
			$host = substr($host,7);
		}

		$cookie = $this->cookie;
		$socket = new socket();
		if (substr($host,0,7) === "http://")
		{
			$host = substr($host,7);
		};
		$socket->open(array(
			"host" => $host,
			"port" => 80,
		));

		$request = "uid=$uid&hash=$hash&class=users&action=login";
		$op = "GET /orb.".AW_FILE_EXT."?$request HTTP/1.0\r\n";
		$op .= "Host: $host\r\n";
		$op .= "Cookie: automatweb=$cookie\r\n";
		$op .= "Referer: http://$host/login.".AW_FILE_EXT."\r\n\r\n";
		if (!$silent)
		{
			print "<pre>";
			echo "Logging in $host op = ",htmlentities($op),"\n";
		}
		$socket->write($op);
		$ipd = "";
		while($data = $socket->read())
		{
			$ipd .= $data;
		}

		$this->socket = $socket;

		if (!$silent)
		{
			print "Succeeded? Server returned ".htmlentities($ipd)."\n";
			print "</pre>";
			flush();
		}
	}

	/**
	@attrib api=1 params=name
	@param host required type=string
		Server URL
	@param req required type=string
		Request url
	@return string
		data returned after request
	@example ${handshake}
	@comment
		Sends HTTP GET request
	**/
	function do_send_request($arr)
	{
		extract($arr);

		$cookie = $this->cookie;
		$socket = new socket();
		$socket->open(array(
			"host" => $host,
			"port" => 80,
		));
		$op = "GET $req HTTP/1.0\r\n";
		$op .= "Host: $host\r\n";
		$op .= "Cache-control: no-cache\r\n";
		$op .= "Pragma: no-cache\r\n";
		$op .= "Cookie: automatweb=$cookie\r\n\r\n";
		$socket->write($op);
		$ipd = "";
		while($data = $socket->read())
		{
			$ipd .= $data;
		}
		$socket->close();

		return $ipd;
	}

	/**
	@attrib api=1 params=name
	@param host optional type=string
		Server URL
	@param req required type=string
		Request url
	@example ${handshake}
	@comment
		Sends HTTP GET request
	**/
	function send_request($args = array())
	{
		extract($args);

		if (empty($host))
		{
			if (preg_match("/http:\/\/(.*)(\/*)(.*)/",$req, $mt))
			{
				$args["host"] = $mt[1];
				$args["req"] = $mt[3];
				extract($args);
			}
		}

		$ipd = $this->do_send_request($args);

		$fail = false;

		if (preg_match("/AW_ERROR: (.*)\n/",$ipd,$matches))
		{
			print " - <b><font color=red>FAIL</font></b>\n";
			print trim($matches[1]);
			print "\n";
			$fail = true;
			if (preg_match("/X-AW-Error: (\d)/",$ipd,$matches))
			{
				print "Additonaly, error code $matches[1] was detected\n";
			};
		};

		if (not($fail))
		{
			print " - <b><font color=green>SUCCESS</font></b>\n";
		};
		flush();
	}

	/**
	@attrib api=1 params=name
	@param host required type=string
		Server URL
	@return array(headers,data)
	@example ${handshake}
	@comment
		Log's out
	**/
	function logout($args = array())
	{
		extract($args);
		$cookie = $this->cookie;
		$socket = new socket();
		$socket->open(array(
			"host" => $host,
			"port" => 80,
		));
		$op = "GET /index.".aw_ini_get("ext")."?class=users&action=logout HTTP/1.0\r\n";
		$op .= "Host: $host\r\n";
		$op .= "Cache-control: no-cache\r\n";
		$op .= "Pragma: no-cache\r\n";
		$op .= "Cookie: automatweb=$cookie\r\n\r\n";

		$socket->write($op);

		while($data = $socket->read())
		{
			$ipd .= $data;
		};

		list($headers,$data) = explode("\r\n\r\n",$ipd);
		return array($headers, $data);
	}

	/**
	@attrib api=1 params=pos
	@param c required type=string
		cookie
	@comment
		Sets session cookie
	**/
	function set_session_cookie($c)
	{
		$this->cookie = $c;
	}

	/**
	@attrib api=1 params=pos
	@param server required type=string
		Server URL
	@param handler required type=string
		POST handler
	@param params requires type=array
		Request parameters Array("parameter"=>"value")
	@param port optional type=int default=80
		Connection port
	@example
		$http = new http();
		return $http->post_request(
			"https://unet.eyp.ee/cgi-bin/unet3.sh/un3min.r",
			"https://unet.eyp.ee/cgi-bin/unet3.sh/un3min",
			array(
				"VK_SERVICE"	=> "1002",
				"VK_VERSION"	=> "008",
				"VK_SND_ID"	=> $sender_id,
			),
			$port = 80,
		);
	@return string
	@comment
		Initiates a socket connection to the server and generates HTTP POST request
	**/
	function post_request($server, $handler, $params, $port = 80, $sessid = NULL)
	{
		$fp = fsockopen($server, $port, $errno, $errstr, 5);
		$op = "POST $handler HTTP/1.0\r\n";
		$op .= "User-Agent: AutomatWeb\r\n";
		$op .= "Host: $server\r\n";
		if ($sessid)
		{
			$op .= "Cookie: automatweb=$sessid\r\n";
		}
		$op .= "Content-Type: application/x-www-form-urlencoded\r\n";

		foreach($params as $key => $val)
		{
			$request .= urlencode($key) . "=" . urlencode($val) . "&";
		}

		$op .= "Content-Length: " . strlen($request) . "\r\n\r\n";
		$op .= $request;
		fputs($fp, $op, strlen($op));

		while($dat = fread($fp, 100000))
		{
			$str .= $dat;
		}
		fclose($fp);
		return $str;
	}
}

/** Generic http exception **/
class awex_http extends aw_exception {}

/** Redirection errors **/
class awex_http_redirect extends awex_http {}
