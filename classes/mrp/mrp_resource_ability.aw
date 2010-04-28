<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_MRP_RESOURCE_ABILITY relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_mrp_resource_ability master_index=brother_of master_table=objects index=aw_oid

@default table=aw_mrp_resource_ability
@default group=general

	@property act_from type=date_select field=aw_act_from
	@caption Kehtib alates

	@property act_to type=date_select field=aw_act_to
	@caption Kehtib kuni

	@property format type=relpicker reltype=RELTYPE_FORMAT field=aw_format
	@caption Formaat

	@property ability_per_hr type=textbox size=5 field=aw_ability_per_hr
	@caption J&otilde;udlus tunnis

@reltype FORMAT value=1 clid=CL_MRP_ORDER_PRINT_FORMAT
@caption Formaat

*/

class mrp_resource_ability extends class_base
{
	const AW_CLID = 1551;

	function mrp_resource_ability()
	{
		$this->init(array(
			"tpldir" => "mrp/mrp_resource_ability",
			"clid" => CL_MRP_RESOURCE_ABILITY
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
			$this->db_query("CREATE TABLE aw_mrp_resource_ability(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_act_from":
			case "aw_act_to":
			case "aw_format":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;

			case "aw_ability_per_hr":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "double"
				));
				return true;
		}
	}
}

?>
