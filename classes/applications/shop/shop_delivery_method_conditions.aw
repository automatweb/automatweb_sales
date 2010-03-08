<?php
/*
@classinfo syslog_type=ST_SHOP_DELIVERY_METHOD_CONDITIONS relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=instrumental
@tableinfo aw_shop_delivery_method_conditions master_index=brother_of master_table=objects index=aw_oid

@default table=aw_shop_delivery_method_conditions
@default group=general

	## This sucks! I really don't want to use connections here due to speed issues, but I can't see any other way today. -kaarel 14.07.2009
	#property delivery_method type=hidden field=aw_delivery_method
	@property delivery_method type=relpicker reltype=RELTYPE_DELIVERY_METHOD store=connect field=aw_delivery_method
	@caption K&auml;ttetoimetamise viis

	@property row type=hidden field=aw_row
	@caption Rida

	@property col type=hidden field=aw_col
	@caption Veerg

	@property enable type=checkbox ch_value=1 field=aw_enable
	@caption Lubatud

### RELTYPES

@reltype DELIVERY_METHOD value=1 clid=CL_SHOP_DELIVERY_METHOD
@caption K&auml;ttetoimetamise viis

*/

class shop_delivery_method_conditions extends class_base
{
	function shop_delivery_method_conditions()
	{
		$this->init(array(
			"tpldir" => "applications/shop//shop_delivery_method_conditions",
			"clid" => CL_SHOP_DELIVERY_METHOD_CONDITIONS
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
			$this->db_query("CREATE TABLE aw_shop_delivery_method_conditions(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_delivery_method":
			case "aw_row":
			case "aw_col":
			case "aw_enable":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}
}

?>
