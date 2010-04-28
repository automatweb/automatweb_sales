<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_CRM_CASH_COW relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop
@tableinfo aw_crm_cash_cow master_index=brother_of master_table=objects index=aw_oid

@default table=aw_crm_cash_cow
@default group=general

@property product type=relpicker reltype=RELTYPE_PRODUCT
@caption Artikkel

@property unit type=relpicker reltype=RELTYPE_UNIT
@caption &Uuml;hik

@property amount type=textbox
@caption Kogus

@property unit_price type=textbox
@caption &Uuml;hiku hind

@property sum type=text
@caption Summa



@property ready type=textbox
@caption Projektiosa valmidustase

@property incoming_income type=textbox
@caption Viittulu



@reltype EXPENSE value=1 clid=CL_CRM_EXPENSE_SPOT
@caption Kulukoht

@reltype PRODUCT value=2 clid=CL_SHOP_PRODUCT
@caption Artikkel

@reltype UNIT value=3 clid=CL_UNIT
@caption &Uuml;hik

*/

class crm_cash_cow extends class_base
{
	const AW_CLID = 1538;

	function crm_cash_cow()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_cash_cow",
			"clid" => CL_CRM_CASH_COW
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		//	$this->db_query("DROP TABLE aw_crm_cash_cow");

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
			$this->db_query("CREATE TABLE aw_crm_cash_cow(aw_oid int primary key, product int, unit int, unit_price double, sum double, amount double)");
			return true;
		}

		switch($f)
		{
			case "product":
			case "unit":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
			case "unit_price":
			case "sum":
			case "amount":
			case "ready":
			case "incoming_income":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "double"
				));
				return true;
		}
	}
}

?>
