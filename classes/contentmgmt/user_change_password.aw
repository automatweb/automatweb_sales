<?php
// $Header: /home/cvs/automatweb_dev/classes/contentmgmt/user_change_password.aw,v 1.4 2008/03/12 21:23:21 kristo Exp $
// user_change_password.aw - Kasutaja parooli muutmine 
/*

@classinfo syslog_type=ST_USER_CHANGE_PASSWORD relationmgr=yes no_comment=1 no_status=1 maintainer=kristo

@default table=objects
@default group=general

*/

class user_change_password extends class_base
{
	function user_change_password()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/user_change_password",
			"clid" => CL_USER_CHANGE_PASSWORD
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
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

	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"reforb" => $this->mk_reforb("save_data", array(
				"return_url" => post_ru()
			)),
		));
		return $this->parse();
	}

	/** saves password change

		@attrib name=save_data

	**/
	function save_data($arr)
	{
		$arr["id"] = aw_global_get("uid");
		extract($arr);

		if ($arr["pwd"] != $arr["pwd2"])
		{
			aw_session_set("chpwe", t("Paroolid ei ole samad!"));
			return $return_url;
		}

		if (!is_valid("password",$pwd))
		{
			aw_session_set("chpwe", "Uus parool sisaldab lubamatuid märke");
			return $return_url;
		}

		if ($arr["pwd"] != "")
		{
			$u = get_instance("users");
			$u->save(array(
				"uid" => $arr["id"], 
				"password" => $arr["pwd"],
			));
		}

		$this->_log(ST_USERS, SA_CHANGE_PWD, $arr['id']);

		return $arr["return_url"];
	}
}
?>
