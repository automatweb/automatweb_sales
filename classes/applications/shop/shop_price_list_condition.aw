<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_SHOP_PRICE_LIST_CONDITION relationmgr=yes no_name=1 no_comment=1 no_status=1 prop_cb=1 maintainer=instrumental
@tableinfo aw_shop_price_list_condition master_index=brother_of master_table=objects index=aw_oid

@default table=aw_shop_price_list_condition
@default group=general

	@property jrk type=textbox size=4 table=objects
	@caption J&auml;rjekorra number

	@property price_list type=hidden field=aw_price_list
	@caption Hinnakiri

	@property currency type=hidden field=aw_currency
	@caption Valuuta

	@property row type=hidden field=aw_row
	@caption Rida

	@property col type=hidden field=aw_col
	@caption Veerg

	@property type type=select field=aw_type
	@caption T&uuml;&uuml;p

	@property value type=textbox size=5 field=aw_value maxlength=30
	@caption V&auml;&auml;rtus

	@property bonus type=textbox size=5 field=aw_bonus maxlength=30
	@caption Boonus

	@property quantities type=textbox size=5 field=aw_quantities maxlength=100
	@caption Kogused

*/

class shop_price_list_condition extends class_base
{
	const AW_CLID = 1573;

	function shop_price_list_condition()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_price_list_condition",
			"clid" => CL_SHOP_PRICE_LIST_CONDITION
		));
	}

	public function _get_type($arr)
	{		
		$options = array(t("Auto"));
		$ol = new object_list(array(
			"class_id" => CL_SHOP_PRICE_LIST_CONDITION_TYPE,
		));
		$options = $options + $ol->names();

		$arr["prop"]["options"] = $options;
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
			$this->db_query("CREATE TABLE aw_shop_price_list_condition(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_price_list":
			case "aw_row":
			case "aw_col":
			case "aw_type":
			case "aw_currency":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;

			case "aw_value":
			case "aw_bonus":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "varchar(30)"
				));
				return true;

			case "aw_quantities":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "varchar(100)"
				));
				return true;
		}
	}
}

?>
