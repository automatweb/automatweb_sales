<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_MRP_PRICELIST_ROW relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_mrp_pricelist_row master_index=brother_of master_table=objects index=aw_oid

@default table=aw_mrp_pricelist_row
@default group=general

	@property pricelist type=relpicker reltype=RELTYPE_PRICELIST field=aw_pricelist
	@caption Hinnakiri

	@property resource type=relpicker reltype=RELTYPE_RESOURCE field=aw_resource
	@caption Ressurss

	@property cnt_from type=textbox size=5 field=aw_cnt_from
	@caption Kogus alates

	@property cnt_to type=textbox size=5 field=aw_cnt_to
	@caption Kogus kuni

	@property config_price type=textbox size=5 field=aw_config_price
	@caption Seadistamise hind

	@property item_price type=textbox size=5 field=aw_item_price
	@caption T&uuml;ki hind

	@property row_type type=hidden field=aw_row_type
	@caption Rea t&uuml;&uuml;p

@reltype PRICELIST value=1 clid=CL_MRP_PRICELIST
@caption Hinnakiri

@reltype RESOURCE value=2 clid=CL_MRP_RESOURCE
@caption Ressurss
*/

class mrp_pricelist_row extends class_base
{
	const AW_CLID = 1522;

	function mrp_pricelist_row()
	{
		$this->init(array(
			"tpldir" => "mrp/orders/mrp_pricelist_row",
			"clid" => CL_MRP_PRICELIST_ROW
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
			$this->db_query("CREATE TABLE aw_mrp_pricelist_row(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_pricelist":
			case "aw_resource":
			case "aw_cnt_from":
			case "aw_cnt_to":
			case "aw_row_type":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int",
					"default" => 0
				));
				return true;

			case "aw_config_price":
			case "aw_item_price":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "double"
				));
				return true;
		}
	}
}

?>
