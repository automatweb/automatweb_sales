<?php
// $Header: /home/cvs/automatweb_dev/classes/protocols/mail/pop3.aw,v 1.4 2008/01/31 13:55:23 kristo Exp $
// pop3.aw - POP3 login 
/*

@classinfo syslog_type=ST_PROTO_POP3 relationmgr=yes  maintainer=kristo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property server type=textbox 
@caption Server

@property user type=textbox
@caption Kasutaja

@property password type=textbox
@caption Parool

*/

class pop3 extends class_base
{
	function pop3()
	{
		$this->init(array(
			"tpldir" => "protocols/mail/pop3",
			"clid" => CL_PROTO_POP3
		));
	}

	//////
	// class_base classes usually need those, uncomment them if you want to use them

	/*
	function get_property($args)
	{
		$data = &$args["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{

		};
		return $retval;
	}
	*/

	/*
	function set_property($args = array())
	{
		$data = &$args["prop"];
		$retval = PROP_OK;
		switch($data["name"])
                {

		}
		return $retval;
	}	
	*/

	function connect($server)
	{
		$server = trim($server);
		$this->fp = fsockopen($server, 110, &$errno, &$errstr);
		if (!$this->fp)
		{
			$this->raise_error(ERR_POP3_CONNECT,sprintf(t("pop3: error connecting, %s , %s"), $errno, $errstr),true);
			return false;
		}
		//print "connected!<br>";
                return true;
	}

	function read_response()
	{
		$line = fgets($this->fp, 512);
		//echo "<< :$line<br />";
		//flush();
		return $line;
        }

        function send_command($cmdstr)
        {
                fputs($this->fp, $cmdstr."\n");
		//echo ">>> :$cmdstr<br />\n";
		//flush();
        }

	function get_status($line)
	{
		if (strlen($line) > 0)
		{
			if ($line[0] == "+")
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		return false;
	}
	
	/**
	@attrib api=1 params=name
	@param id required type=oid
		POP3 id
	@return array of messages
		FALSE if some error occurs
	@errors 
		ERR_POP3_INVUSER - if the user name is invalid
		ERR_POP3_INVPWD - it the passwort is invalid
		ERR_POP3_STAT - somekind of error after command STAT
		ERR_POP3_UIDL - somekind of error after commant UIDL
		ERR_POP3_RETR - problem with getting message
		ERR_POP3_CONNECT - error with connecting
	@example 
		$pop3 = get_instance("protocols/mail/pop3");
		$messages = $pop3->get_messages($pop3_id);
	@comment 
		Gets messages
	**/
	function get_messages($arr)
	{
		$obj = new object($arr["id"]);
		$server = $obj->prop("server");
		$user = $obj->prop("user");
		$pass = $obj->prop("password");
		if (!$this->connect($server))
		{
			return false;
		}
		$this->read_response();
		$this->send_command("USER $user");
		if (!$this->get_status($this->read_response()))
		{
			$this->raise_error(ERR_POP3_INVUSER,sprintf(t("pop3: Invalid username %s!"), $user),true);
			return false;
		}

		$this->send_command("PASS $pass");
		if (!$this->get_status($this->read_response()))
		{
			$this->raise_error(ERR_POP3_INVPWD,sprintf(t("pop3: Invalid password for user %s!"), $user),true);
			return false;
		}

		$this->send_command("STAT");
		if (!$this->get_status($res = $this->read_response()))
		{
			$this->raise_error(ERR_POP3_STAT,sprintf(t("pop3:  weird error %s after STAT!"), $res),true);
			return false;
		}
		preg_match("/\+OK (.*) (.*)/",$res,$mt);
		$no_msgs = $mt[1];

		$this->send_command("UIDL");
		if (!$this->get_status($res = $this->read_response()))
		{
			$this->raise_error(ERR_POP3_UIDL,sprintf(t("pop3:  weird error %s after UIDL!"), $res),true);
			return false;
		}
		$muidls = array();
		for ($i=1; $i <= $no_msgs; $i++)
		{
			preg_match("/(\d*)\s*(.*)/",$res = $this->read_response(),$br);
			$muidls[$br[1]] = $br[2];
			//                      echo $br[2],"<br />";
		}
		$this->read_response(); // a "." is returned after the list
		$uidls = array();

		$use_callback = false;

		if (is_array($arr["store_callback"]) && sizeof($arr["store_callback"]) == 2)
		{
			$use_callback = true;
			$clinst = &$arr["store_callback"][0];
			$meth = $arr["store_callback"][1];
		};

		$msgs = array();
		for ($i=1; $i <= $no_msgs; $i++)
		{
			// only retrieve messages, that are not already downloaded
			$key = trim($muidls[$i]);
			if (!in_array($key,$uidls))
			//if (isset($uidls[$key]))
			{
				$tmp = $this->get_message($i);
				if ($use_callback)
				{
					$clinst->$meth(array(
						"msg" => $tmp,
						"uniqid" => $muidls[$i],
					));
				};
				$msgs[] = array("msg" => $tmp, "uidl" => $muidls[$i]);
			}
			else
			{
				#echo "message nr $i not downed, uidl = '",$muidls[$i],"<br />";
				#print "idl was not set, therefore we are not skipping the message<br />";
                        };
                        //if ($delete)
                        //      $this->send_command("DELE $i");
                }



		// vaat the biggest and stupidest problem is that, I need to create a list of all message id-s
		// that have not yet been downloaded. .. but perhaps I should just retrieve them all and 
		// throw away things that I do not need.

		$this->send_command("QUIT");
		$this->read_response();
		/*
		print "<pre>";
		print_r($msgs);
		print "</pre>";
		*/
		return ($msgs);
	}

	function get_message($num)
	{
		$this->send_command("RETR $num");
		if (!$this->get_status($res = $this->read_response()))
		{
			$this->raise_error(ERR_POP3_RETR,sprintf(t("pop3: imelik error %s after RETR %s !"), $res, $num),true);
			return false;
		}
		$ret = "";
		$continue = true;
		do {
			$line = $this->read_response();
			if ($line == ".\x0d\x0a")
			{
				$continue = false;
			}
			else
			{
				if ($line[0] == ".")
				{
					$ret.=substr($line,1);
				}
				else
				{
					$ret.=$line;
				}
                        }
		} while ($continue);
		//      echo "message $num follows:<br />\n<pre>$ret</pre>\nBEEP!\n<br />";
		//              flush();
		return $ret;
        }

}
?>
