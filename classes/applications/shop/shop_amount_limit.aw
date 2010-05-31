<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_SHOP_AMOUNT_LIMIT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=robert
@tableinfo aw_shop_amount_limit master_index=brother_of master_table=objects index=aw_oid

@default table=aw_shop_amount_limit
@default group=general

@property start type=date_select store=no
@caption Algus

@property end type=date_select store=no
@caption L&otilde;pp

@property time type=textbox size=5 store=no
@caption Kellaaeg (tund:minut)

@property length type=textbox size=5 store=no
@caption Pikkus (h)

@property recur_type type=select store=no
@caption Korduse t&uuml;&uuml;p

@property recur_interval type=textbox store=no
@caption Korduse intervall

@property amount type=textbox datatype=int
@caption Kogus

@property unit type=relpicker reltype=RELTYPE_UNIT
@caption &Uuml;hik

@reltype UNIT value=1 clid=CL_UNIT
@caption &Uuml;hik
*/

class shop_amount_limit extends class_base
{
	const AW_CLID = 1530;

	function shop_amount_limit()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_amount_limit",
			"clid" => CL_SHOP_AMOUNT_LIMIT
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "recur_type":
				$data["options"] = array(
					RECUR_DAILY => t("päev"),
					RECUR_WEEKLY => t("nädal"),
					RECUR_MONTHLY => t("kuu"),
					RECUR_YEARLY => t("aasta"),
					RECUR_MINUTELY => t("minut"),
					RECUR_HOURLY => t("tund"),
				);
				break;
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
			$this->db_query("CREATE TABLE aw_shop_amount_limit(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "amount":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "double"
				));
				return true;
			case "unit":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}
}

?>
