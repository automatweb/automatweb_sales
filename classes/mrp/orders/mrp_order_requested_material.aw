<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_MRP_ORDER_REQUESTED_MATERIAL relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_mrp_order_requested_material master_index=brother_of master_table=objects index=aw_oid

@default table=aw_mrp_order_requested_material
@default group=general

@property material type=relpicker reltype=RELTYPE_MATERIAL field=aw_material
@caption Materjal

@property amount type=textbox size=10 field=aw_amount
@caption Kogus

@property pages_with_this type=textbox size=10 field=aw_pages_with_this
@caption Lehti selle materjaliga

@property for_covers type=checkbox ch_value=1 field=aw_for_covers
@caption Kaante jaoks

@property connected_job type=relpicker reltype=RELTYPE_MRP_JOB field=aw_connected_job
@caption Materjaliga seotud t&ouml;&ouml;

@property resource type=relpicker reltype=RELTYPE_RESOURCE field=aw_resource
@caption Ressurss

@reltype MRP_JOB value=1 clid=CL_MRP_JOB
@caption Materjaliga seotud t&ouml;&ouml;

@reltype RESOURCE value=2 clid=CL_MRP_RESOURCE
@caption Ressurss
*/

class mrp_order_requested_material extends class_base
{
	const AW_CLID = 1536;

	function mrp_order_requested_material()
	{
		$this->init(array(
			"tpldir" => "mrp/orders/mrp_order_requested_material",
			"clid" => CL_MRP_ORDER_REQUESTED_MATERIAL
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
			$this->db_query("CREATE TABLE aw_mrp_order_requested_material(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_material":
			case "aw_for_covers":
			case "aw_pages_with_this":
			case "aw_connected_job":
			case "aw_resource":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;

			case "aw_amount":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "double"
				));
				return true;
		}
	}
}

?>
