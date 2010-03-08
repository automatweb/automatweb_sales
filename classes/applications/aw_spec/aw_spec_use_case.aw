<?php
/*
@classinfo syslog_type=ST_AW_SPEC_USE_CASE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo allow_rte=2
@tableinfo aw_aw_spec_use_case master_index=brother_of master_table=objects index=aw_oid

@default table=aw_aw_spec_use_case
@default group=general

	@property desc type=textarea rows=40 cols=80 field=aw_desc richtext=1
	@caption Kirjeldus
*/

class aw_spec_use_case extends class_base
{
	function aw_spec_use_case()
	{
		$this->init(array(
			"tpldir" => "applications/aw_spec/aw_spec_use_case",
			"clid" => CL_AW_SPEC_USE_CASE
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
			$this->db_query("CREATE TABLE aw_aw_spec_use_case(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_desc":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "mediumtext"
				));
				return true;
		}
	}
}

?>
