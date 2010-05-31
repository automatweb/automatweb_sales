<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_SITE_COPY_CLIENT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=smeedia
@tableinfo aw_site_copy_client master_index=brother_of master_table=objects index=aw_oid

@default table=aw_site_copy_client
@default group=general

@property sc_url type=textbox
@caption Serveri URL

*/

class site_copy_client extends class_base
{
	const AW_CLID = 1532;

	function site_copy_client()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/site_copy/site_copy_client",
			"clid" => CL_SITE_COPY_CLIENT
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
		}

		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
		}

		return $retval;
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_site_copy_client(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "sc_url":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "varchar(255)"
				));
				return true;
		}
	}

	public static function get_obj_inst()
	{
		$ol = new object_list(array(
			"class_id" => CL_SITE_COPY_CLIENT,
			"lang_id" => array(),
			"site_id" => array(),
			"limit" => 1,
		));
		return $ol->begin();
	}

	/**
		@attrib name=check_site params=name nologin=1

		@param url required type=string

	**/
	public function check_site($arr)
	{
		$o = self::get_obj_inst();
		if(strlen($arr["url"]))
		{
			$p = parse_url($arr["url"]);
			$pure_url_encoded = urlencode(isset($p["host"]) ? $p["host"] : $p["path"]);
			$u = new aw_uri($o->sc_url."/orb.aw");
			$u->set_arg(array(
				"class" => "site_copy",
				"action" => "check_site",
				"url" => $pure_url_encoded,
			));
			$r = file_get_contents($u->get());
		}
		else
		{
			$r = json_encode(array("msg" => 0));
		}
		die($r);
	}

	/**
		@attrib name=add_site params=name nologin=1

		@param url required type=string

	**/
	function add_site($arr)
	{
		$o = self::get_obj_inst();
		
		$p = parse_url($arr["url"]);
		$pure_url_encoded = urlencode(isset($p["host"]) ? $p["host"] : $p["path"]);

		$email = obj(get_instance(CL_USER)->get_current_person())->prop("email.mail");

		$u = new aw_uri($o->sc_url."/orb.aw");
		$u->set_arg(array(
			"class" => "site_copy",
			"action" => "add_site",
			"cvs" => 1,
			"email" => $email,
			"url" => $pure_url_encoded,
		));
		file($u->get());
	}
}

?>
