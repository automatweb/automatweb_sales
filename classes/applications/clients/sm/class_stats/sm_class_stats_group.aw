<?php
/*
@classinfo syslog_type=ST_SM_CLASS_STATS_GROUP relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_sm_class_stats_group master_index=brother_of master_table=objects index=aw_oid

@default table=aw_sm_class_stats_group
@default group=general

@property class_list type=select multiple=1 size=20 table=objects field=meta method=serialize
@caption Valitud klassid
*/

class sm_class_stats_group extends class_base
{
	function sm_class_stats_group()
	{
		$this->init(array(
			"tpldir" => "applications/clients/sm/class_stats/sm_class_stats_group",
			"clid" => CL_SM_CLASS_STATS_GROUP
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
			$this->db_query("CREATE TABLE aw_sm_class_stats_group(aw_oid int primary key)");
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

	function _get_class_list($arr)
	{
		if (!empty($arr["request"]["sel"]))
		{
			$arr["prop"]["value"] = $arr["request"]["sel"];
		}
		$arr["prop"]["options"] = get_class_picker();
	}
}

?>
