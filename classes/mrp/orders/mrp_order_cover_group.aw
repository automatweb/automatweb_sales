<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_MRP_ORDER_COVER_GROUP relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_mrp_order_cover_group master_index=brother_of master_table=objects index=aw_oid

@default table=aw_mrp_order_cover_group
@default group=general

@reltype MRP_COVER value=5 clid=CL_MRP_ORDER_COVER,CL_MRP_ORDER_COVER_GROUP
@caption Kate

*/

class mrp_order_cover_group extends class_base
{
	const AW_CLID = 1537;

	function mrp_order_cover_group()
	{
		$this->init(array(
			"tpldir" => "mrp/orders/mrp_order_cover_group",
			"clid" => CL_MRP_ORDER_COVER_GROUP
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
			$this->db_query("CREATE TABLE aw_mrp_order_cover_group(aw_oid int primary key)");
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
