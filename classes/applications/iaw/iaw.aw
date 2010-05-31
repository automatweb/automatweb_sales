<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_IAW relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=hannes
@tableinfo aw_iaw master_index=brother_of master_table=objects index=aw_oid

@default group=first
@default table=objects
@default store=no

@layout content type=hbox

@property layout type=table no_caption=1


@groupinfo first caption="Esimene" submit_method=get save=no
*/

class iaw extends class_base
{
	const AW_CLID = 1451;

	function iaw()
	{
		$this->init(array(
			"tpldir" => "applications/iaw",
			"clid" => CL_IAW
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "content":
				$prop["value"] = "tere";
				break;
			case "layout":
				$this->read_template("default/layout.tpl");
				$prop["value"] = $this->parse();
				break;
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

	function callback_mod_tab($arr)
	{
		if ($arr["id"] != "first")
		{
			return false;
		}
		return true;
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
			//$this->db_query("CREATE TABLE aw_iaw(aw_oid int primary_key)");
			return true;
		}

		switch($f)
		{
			case "":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => ""
				));
				return true;
		}
	}
}

?>
