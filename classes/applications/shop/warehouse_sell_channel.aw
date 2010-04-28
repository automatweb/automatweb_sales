<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_WAREHOUSE_SELL_CHANNEL relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=smeedia
@tableinfo aw_warehouse_sell_channel master_index=brother_of master_table=objects index=aw_oid

@default table=aw_warehouse_sell_channel
@default group=general

*/

class warehouse_sell_channel extends class_base
{
	const AW_CLID = 1682;

	function warehouse_sell_channel()
	{
		$this->init(array(
			"tpldir" => "applications/shop/warehouse_sell_channel",
			"clid" => CL_WAREHOUSE_SELL_CHANNEL
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
			$this->db_query("CREATE TABLE aw_warehouse_sell_channel(aw_oid int primary key)");
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
