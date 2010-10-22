<?php

/*
HANDLE_MESSAGE(MSG_USER_LOGIN, on_login)
*/

class site_list_updater extends aw_template
{
	function site_list_updater()
	{
		$this->init();
	}

	/**
		@attrib api=1

		@param uid required
	**/
	function on_login($arr)
	{
		// if the remote connection is turned off, then bail out
		if(aw_ini_get("server.no_remote_conn") == 1)
		{
			return;
		}

		// check if there has been an update in the last 24 hours
		// if so, do nothing
		if ($this->_get_last_update_time() > (time() - (3600*24)))
		{
			return;
		}
		// no go to background
		$url = str_replace("/automatweb", "", $this->mk_my_orb("bg_do_update", array("uid" => $arr["uid"]), "site_list_updater", false, true, "&", false));

		try
		{
			get_instance("protocols/file/http")->get($url);
		}
		catch (awex_socket $e)
		{
		}
	}

	/**
		@attrib name=ping nologin="1"
	**/
	function ping($arr)
	{
		die("pong");
	}

	/**
		@attrib name=bg_do_update nologin="1"
		@param uid optional
	**/
	function bg_do_update($arr)
	{
		// go to bg.
		// let the user continue with their business
		ignore_user_abort(true);
		header("Content-Type: image/gif");
		header("Content-Length: 43");
		header("Connection: close");
		echo base64_decode("R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==")."\n"; // an encoded 1x1 px empty gif image
		flush();

		$this->_set_last_update_time();
		// update this site's info in the site list
		// check if we have a session key for this site
		if (!($key = $this->_get_session_key()) || $key === "Array")
		{
			// if not, request a session key from the site list server
			$key = $this->_init_session_key();
		}

		// the idea behind the session key is that the first time
		// any communication between the site and the register happens
		// the session key is created in both databases
		// after that it is used to encrypt all communications, but it iself
		// is of course never passed between servers, so that if the attacker
		// misses the session key he can not make any modifications to the register
		// it still is vulnerable during the session key creation, but...

		// get the new info about the site
		$data = $this->_collect();

		// encrypt it
		$data = $this->_encrypt(aw_serialize($data, SERIALIZE_XML), $key);

		// send it to the register
		$this->_do_update($data);
		exit; // TODO: tmp solution to headers sent error later from aw_response. send headers through aw_response
	}

	function _get_last_update_time()
	{
		return $this->get_cval("site_list_last_update".aw_ini_get("site_id"));
	}

	function _set_last_update_time()
	{
		$this->set_cval("site_list_last_update".aw_ini_get("site_id"), time());
	}

	function _get_session_key()
	{
		return $this->get_cval("site_list_xtea_session_key".aw_ini_get("site_id"));
	}

	function _init_session_key()
	{
		$key = $this->do_orb_method_call(array(
			"class" => "site_list",
			"action" => "create_session_key",
			"method" => "xmlrpc",
			"server" => "http://register.automatweb.com",
			"params" => array(
				"site_id" => aw_ini_get("site_id")
			),
			"no_errors" => true
		));
		$this->set_cval("site_list_xtea_session_key".aw_ini_get("site_id"), $key);
		return $key;
	}

	function _collect()
	{
		return array(
			"id" => aw_ini_get("site_id"),
			"baseurl" => aw_ini_get("baseurl"),
			"site_basedir" => aw_ini_get("site_basedir"),
			"code" => aw_ini_get("basedir"),
			"uid" => aw_global_get("uid"),
			"used_class_list" => $this->_get_used_class_list()
		);
	}

	function _get_used_class_list()
	{
		$this->db_query("SELECT distinct(class_id) as c FROM objects WHERE site_id = ".aw_ini_get("site_id")." AND status > 0");
		$rv = array();
		while ($row = $this->db_next())
		{
			$rv[] = $row["c"];
		}
		return join(",", $rv);
	}

	function _encrypt($data, $key)
	{
		$i = get_instance("protocols/crypt/xtea");
		return $i->encrypt($data, $key);
	}

	function _do_update($data)
	{
		$res = $this->do_orb_method_call(array(
			"class" => "site_list",
			"action" => "do_auto_update",
			"method" => "xmlrpc",
			"server" => "http://register.automatweb.com",
			"params" => array(
				"site_id" => aw_ini_get("site_id"),
				"data" => base64_encode($data)
			),
			"no_errors" => 1
		));
		if ($res)
		{
			$this->_set_last_update_time();
		}
	}
}
