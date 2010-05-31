<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_SHOP_PAYMENT_TYPE_CONDITIONS relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=instrumental
@tableinfo aw_shop_payment_type_conditions master_index=brother_of master_table=objects index=aw_oid

@default table=aw_shop_payment_type_conditions
@default group=general

	@property payment_type type=hidden field=aw_payment_type

	@property row type=hidden field=aw_row

	@property col type=hidden field=aw_col

	@property currency type=hidden field=aw_currency

	@property min_amt type=textbox field=aw_min_amt
	@caption J&auml;relmaksu miinumumsumma

	@property ignore_min_amt type=checkbox field=aw_ignore_min_amt
	@caption J&auml;relmaksu miinumumsummat ei arvestata, ainult informatiivne

	@property max_amt type=textbox field=aw_max_amt
	@caption J&auml;relmaksu maksimumsumma (0 - piiramata)

	@property ignore_max_amt type=checkbox field=aw_ignore_max_amt
	@caption J&auml;relmaksu maksimumsummat ei arvestata, ainult informatiivne

	@property min_payment type=textbox field=aw_min_payment
	@caption &Uuml;he makse miinimumsumma

	@property ignore_min_payment type=checkbox field=aw_ignore_min_payment
	@caption &Uuml;he makse miinumumsummat ei arvestata, ainult informatiivne

	@property prepayment_interest type=textbox field=aw_prepayment_interest
	@caption Esmase sissemakse protsent

	@property yearly_interest type=textbox field=aw_yearly_interest
	@caption Aastaintressi protsent

	@property period_min type=textbox field=aw_period_min
	@caption Minimaalne järelmaksuperioodi pikkus kuudes

	@property period_max type=textbox field=aw_period_max
	@caption Maksimaalne järelmaksuperioodi pikkus kuudes

	@property period_step type=textbox field=aw_period_step
	@caption Järelmaksuperioodi samm

*/

class shop_payment_type_conditions extends class_base
{
	const AW_CLID = 1559;

	function shop_payment_type_conditions()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_payment_type_conditions",
			"clid" => CL_SHOP_PAYMENT_TYPE_CONDITIONS
		));
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_shop_payment_type_conditions(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_valid_to":
			case "aw_valid_from":
			case "aw_payment_type":
			case "aw_row":
			case "aw_col":
			case "aw_currency":
			case "aw_ignore_min_amt":
			case "aw_ignore_max_amt":
			case "aw_ignore_min_payment":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;

			case "aw_min_amt":
			case "aw_max_amt":
			case "aw_min_payment":
			case "aw_prepayment_interest":
			case "aw_yearly_interest":
			case "aw_period_min":
			case "aw_period_max":
			case "aw_period_step":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "decimal(25,5)"
				));
				return true;
		}
	}

	/**
		@attrib name=calculate_rent params=name nologin=1 

		@param id required type=int acl=view

		@param sum required type=float

		@param period required type=int

		@param precision optional type=int default=2
	**/
	public function calculate_rent($arr)
	{
		die(json_encode(obj($arr["id"])->calculate_rent($arr["sum"], $arr["period"], isset($arr["precision"]) ? $arr["precision"] : 2)));
	}
}

?>
