<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/unittest/tester.aw,v 1.3 2008/01/31 13:50:23 kristo Exp $

/*
@classinfo  maintainer=kristo

@default group=general
@default form=tester

@property tests type=text
@caption Tulemused

@forminfo tester onsubmit=do_nothing

*/

class tester extends class_base
{
	function tester()
	{
		$this->init(array(
			"tpldir" => "applications/tester",
		));
	}

	/** does the tests
		
		@attrib name=show default="1" params=name
		
	**/
	function show($arr)
	{
		$arr["form"] = "tester";
		return $this->change($arr);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		switch($prop["name"])
		{
			case "tests":
				$this->do_tests($arr);
				break;
		};
		return PROP_OK;
	}

	function do_tests($arr)
	{
		$rv = "ah, vana perse";
		$arr["prop"]["value"] = $rv;


	}

	/** saves changes 
		
		@attrib name=do_nothing params=name 

	**/
	function do_nothing($arr)
	{
		extract($arr);
		$this->handshake(array(
			"host" => "duke.dev.struktuur.ee",
		));
		$this->login(array(
			"host" => "duke.dev.struktuur.ee",
			"uid" => "duke",
			"password" => "sajoob",
		));

		$dat = $this->do_send_request(array(
			"req" => "/orb.aw?class=testrunner&script=localetest",
			"host" => $_SERVER["HTTP_HOST"],
		));
		list($headers,$data) = explode("\r\n\r\n",$dat);
		$this->logout(array(
			"host" => $_SERVER["HTTP_HOST"],
		));
		//print "<h1>hdr</h1>";
		//arr($headers);
		//print "<h1>dat</h1>";
		//arr($data);
		$p = xml_parser_create();
		xml_parse_into_struct($p, $data, $vals, $index);
		xml_parser_free($p);
		$results = array();
		$ikeys = array("RESULT","NAME","DESCRIPTION","COMMENT");
		foreach($ikeys as $ikey)
		{
			if (is_array($index[$ikey]))
			{
				foreach($index[$ikey] as $key => $tag_index)
				{
					$results[$key][$ikey] = $vals[$tag_index]["value"];
				};
			};
		};
		load_vcl("table");
		$t = new vcl_table();
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));
		$t->define_field(array(
			"name" => "desc",
			"caption" => t("Kirjeldus"),
		));
		$t->define_field(array(
			"name" => "result",
			"caption" => t("Tulemus"),
		));
		$t->define_field(array(
			"name" => "comment",
			"caption" => t("Kommentaar"),
		));
		foreach($results as $result)
		{
			$res = $result["RESULT"];
			$color = $res == "FAIL" ? "red" : "green";
			$t->define_data(array(
				"name" => $result["NAME"],
				"desc" => $result["DESCRIPTION"],
				"result" => "<font color='$color'><b>" . $result["RESULT"] . "</b></font>",
				"comment" => $result["COMMENT"],
			));
		};
		return $t->draw();
	}


	function activate($args = array())
	{
		extract($args);

		$o = obj($id);
		
		$rl = get_instance("remote_login");
		list($server, $this->cookie) = $rl->login_from_obj($o->prop("login"));

		print "<pre>";
		print "Sending requests now\n";

		$query = $o->meta("urls");
		if (is_array($query))
		{
			foreach($query as $key => $val)
			{
				if (strlen($val) > 0)
				{
					print "Sending $val";
					flush();
					$this->send_request(array(
						"host" => $server,
						"req" => $val,
					));
					print "-------------------------------------------------------\n";
					flush();
				};
			};
		};

		print "All requests sent\n";
		print "</pre>";
			
		$this->logout(array(
			"host" => $server,
		));
		exit;
	}


	function handshake($args = array())
	{
		extract($args);
		$socket = get_instance("protocols/socket");
		$socket->open(array(
			"host" => $host,
			"port" => 80,
		));
		
		if ($args["sessid"] != "")
		{
			$this->cookie = $args["sessid"];
			return;
		}

		$op = "HEAD / HTTP/1.0\r\n";
		$op .= "Host: $host\r\n";
		$op .= "Cache-control: no-cache\r\n";
		$op .= "Pragma: no-cache\r\n";
		$op .= "\r\n";

		if ($this->debug)
		{
			print "<pre>";
			print "Acquiring session\n";
			flush();
		};

		$socket->write($op);

		$ipd="";
		
		while($data = $socket->read())
		{
			$ipd .= $data;
		};

		if (preg_match("/automatweb=(\w+?);/",$ipd,$matches))
		{
			$cookie = $matches[1];
		};

		$this->cookie = $cookie;

		if ($this->debug)
		{
			print "Got session, ID is $cookie\n";
			print "</pre>";
			flush();
		};
	}

	function login($args = array())
	{
		extract($args);
		$cookie = $this->cookie;
		$socket = get_instance("protocols/socket");
		$socket->open(array(
			"host" => $host,
			"port" => 80,
		));
		$ext = $this->cfg["ext"];
		
		$request = "uid=$uid&password=$password&class=users&action=login";

		$op = "POST /reforb.".$ext." HTTP/1.0\r\n";
		$op .= "Host: $host\r\n";
		$op .= "Cookie: automatweb=$cookie\r\n";
		$op .= "Keep-Alive: 5\r\n";
		$op .= "Cache-control: no-cache\r\n";
		$op .= "Pragma: no-cache\r\n";
		$op .= "Referer: http://$host/login.".$ext."\r\n";
		$op .= "Content-type: application/x-www-form-urlencoded\r\n";
		$op .= "Content-Length: " . strlen($request) . "\r\n\r\n";
		if ($this->debug)
		{
			print "<pre>";
			print "Logging in\n";
		};
		$socket->write($op);
		$socket->write($request);
	
		$ipd = "";
		while($data = $socket->read())
		{
			$ipd .= $data;
		};
		$this->socket = $socket;
		list($headers,$data) = explode("\r\n\r\n",$ipd);
		if ($this->debug)
		{
			print "Succeeded? Server returned $data\n";
			print "</pre>";
			flush();
		};
	}

	function do_send_request($arr)
	{
		extract($arr);

		$cookie = $this->cookie;
		$socket = get_instance("protocols/socket");
		$socket->open(array(
			"host" => $host,
			"port" => 80,
		));
		$op = "GET $req HTTP/1.0\r\n";
		$op .= "Host: $host\r\n";
		$op .= "Cache-control: no-cache\r\n";
		$op .= "Pragma: no-cache\r\n";
		$op .= "Cookie: automatweb=$cookie\r\n\r\n";
		/*
		print "sending request $req <br />\n";
		print "<pre>";
		print htmlspecialchars($op);
		print "</pre>";
		flush();
		*/
		$socket->write($op);
		$ipd = "";
		while($data = $socket->read())
		{
			$ipd .= $data;
		};
		$socket->close();

		return $ipd;
	}

	function send_request($args = array())
	{
		extract($args);

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
			
		//preg_match("/HTTP\/1.1 (\d+?) (w+?)/",$ipd,$matches);
		//print $matches[1] . $matches[2];
		//print "\n";
		//print "server returned: $ipd\n";
		flush();
	}



	function logout($args = array())
	{
		extract($args);
		$cookie = $this->cookie;
		$socket = get_instance("protocols/socket");
		$socket->open(array(
			"host" => $host,
			"port" => 80,
		));
		$op = "GET /index.".$this->cfg["ext"]."?class=users&action=logout HTTP/1.0\r\n";
		$op .= "Host: $host\r\n";
		$op .= "Cache-control: no-cache\r\n";
		$op .= "Pragma: no-cache\r\n";
		$op .= "Cookie: automatweb=$cookie\r\n\r\n";

		if ($this->debug)
		{
			print "<pre>";
			print t("Logging out").":<br />";
		};
		$socket->write($op);
		
		while($data = $socket->read())
		{
			$ipd .= $data;
		};

		list($headers,$data) = explode("\r\n\r\n",$ipd);
		if ($this->debug)
		{
			print t("Succeeded? Server returned")." $data\n";
			print "</pre>";
			flush();
		};
	}
};
?>
