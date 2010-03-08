<?php
// $Header: /home/cvs/automatweb_dev/classes/contentmgmt/login_box.aw,v 1.5 2008/10/08 08:05:44 kristo Exp $
// login_box.aw - Sisselogimiskast 
/*

@classinfo syslog_type=ST_LOGIN_BOX relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@default table=objects
@default group=general

	@property template type=select field=meta method=serialize
	@caption Kujundusmall

	@property redir type=textbox field=meta method=serialize
	@caption Kuhu suunata p&auml;rast logimist
*/

class login_box extends class_base
{
	function login_box()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/login_box",
			"clid" => CL_LOGIN_BOX
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "template":
				$tm = get_instance("templatemgr");
				$prop["options"] = $tm->template_picker(array("folder" => "contentmgmt/login_box"));
				break;
		};
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

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function show($arr)
	{
		if (aw_global_get("uid") != "")
		{
			return "";
		}
		if (($port = aw_ini_get("auth.display_over_ssl_port")) > 0)
		{
			if (!$_SERVER["HTTPS"])
			{
				$bits = parse_url(aw_ini_get("baseurl"));
				header("Location: https://".$bits["host"].":".$port.aw_global_get("REQUEST_URI"));
				die();
			}
		}

		$o = obj($arr["id"]);
		$tpl = $o->prop("template") != "" ? $o->prop("template") : "login.tpl";
		$this->read_template($tpl);
		$args = array();
		if ($o->prop("redir") != "")
		{
			$args["return"] = $o->prop("redir");
		}

		$this->vars(array(
			"reforb" => $this->mk_reforb("login", $args, "users")
		));
		if (aw_global_get("uid") == "")
		{
			$this->vars(array(
				"login" => $this->parse("login")
			));
		}
		else
		{
			$this->vars(array(
				"logged" => $this->parse("logged")
			));
		}
		return $this->parse();
	}
}
?>
