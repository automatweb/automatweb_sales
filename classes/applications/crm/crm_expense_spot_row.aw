<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_CRM_EXPENSE_SPOT_ROW relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop
@tableinfo aw_crm_expense_spot_row master_index=brother_of master_table=objects index=aw_oid

@default table=aw_crm_expense_spot_row
@default group=general

@property product type=relpicker reltype=RELTYPE_PRODUCT
@caption Artikkel

@property unit type=relpicker reltype=RELTYPE_UNIT
@caption &Uuml;hik

@property amount type=textbox
@caption Kogus

@property unit_price type=textbox
@caption &Uuml;hiku omahind

@property sum type=textbox
@caption Summa/Eelarve

@property supplier type=relpicker reltype=RELTYPE_SUPPLIER
@caption Tarnija/Hankija


@reltype PRODUCT value=1 clid=CL_SHOP_PRODUCT
@caption Toode

@reltype UNIT value=2 clid=CL_UNIT
@caption &Uuml;hik

@reltype SUPPLIER value=3 clid=CL_CRM_COMPANY,CL_CRM_PERSON
@caption Tarnija/Hankija


*/

class crm_expense_spot_row extends class_base
{
	const AW_CLID = 1540;

	function crm_expense_spot_row()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_expense_spot_row",
			"clid" => CL_CRM_EXPENSE_SPOT_ROW
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
			$this->db_query("CREATE TABLE aw_crm_expense_spot_row(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "product":
			case "unit":
			case "supplier":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
			case "unit_price":
			case "sum":
			case "amount":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "double"
				));
				return true;
		}
	}
}

?>
