<?php
/*
@classinfo syslog_type=ST_SM_PROP_STATS_GROUP relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_sm_prop_stats_group master_index=brother_of master_table=objects index=aw_oid

@default table=aw_sm_prop_stats_group
@default group=general

@property prop_list type=table store=no
@caption Valitud omadused

@reltype PROP value=1 clid=CL_AW_CLASS_PROPERTY
@caption Omadus
*/

class sm_prop_stats_group extends class_base
{
	function sm_prop_stats_group()
	{
		$this->init(array(
			"tpldir" => "applications/class_designer/sm_prop_stats_group",
			"clid" => CL_SM_PROP_STATS_GROUP
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

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
		$arr["sel"] = ifset($_GET, "sel");
	}

	function callback_post_save($arr)
	{
		if (is_array($arr["request"]["sel"]))
		{
			foreach($arr["request"]["sel"] as $prop)
			{
				$arr["obj_inst"]->connect(array("to" => $prop, "type" => "RELTYPE_PROP"));
			}
		}
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
			$this->db_query("CREATE TABLE aw_sm_prop_stats_group(aw_oid int primary key)");
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

	function _get_prop_list($arr)
	{
		if (!is_oid($arr["obj_inst"]->id()))
		{
			return PROP_IGNORE;
		}
		$arr["prop"]["vcl_inst"]->table_from_ol(
			new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_PROP"))),
			array("c_class", "p_caption", "p_name"),
			CL_AW_CLASS_PROPERTY
		);
	}
}

?>
