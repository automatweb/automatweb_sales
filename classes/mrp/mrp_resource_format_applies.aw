<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_MRP_RESOURCE_FORMAT_APPLIES relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_mrp_resource_format_applies master_index=brother_of master_table=objects index=aw_oid

@default table=aw_mrp_resource_format_applies
@default group=general

@property resource type=relpicker reltype=RELTYPE_RESOURCE field=aw_resource
@caption Resurss

@property format type=relpicker reltype=RELTYPE_FORMAT field=aw_format
@caption Formaat

@reltype RESOURCE value=1 clid=CL_MRP_RESOURCE
@caption Resurss

@reltype FORMAT value=2 clid=CL_MRP_ORDER_PRINT_FORMAT
@caption Formaat

*/

class mrp_resource_format_applies extends class_base
{
	const AW_CLID = 1549;

	function mrp_resource_format_applies()
	{
		$this->init(array(
			"tpldir" => "mrp/mrp_resource_format_applies",
			"clid" => CL_MRP_RESOURCE_FORMAT_APPLIES
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
			$this->db_query("CREATE TABLE aw_mrp_resource_format_applies(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_resource":
			case "aw_format":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}
}

?>
