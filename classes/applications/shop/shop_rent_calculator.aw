<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_SHOP_RENT_CALCULATOR relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=instrumental
@tableinfo aw_shop_rent_calculator master_index=brother_of master_table=objects index=aw_oid

@default table=aw_shop_rent_calculator
@default group=general

@property payment_type type=relpicker reltype=RELTYPE_PAYMENT_TYPE store=connect
@caption Makseviis

#### RELTYPES

@reltype PAYMENT_TYPE value=1 clid=CL_SHOP_PAYMENT_TYPE
@caption Makseviis

*/

class shop_rent_calculator extends class_base
{
	const AW_CLID = 1557;

	function shop_rent_calculator()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_rent_calculator",
			"clid" => CL_SHOP_RENT_CALCULATOR
		));
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	public function show($arr)
	{
		load_javascript("applications/shop/rent_calculator.js");
		$this->read_template("show.tpl");

		$o = new object($arr["id"]);
		$payment_type = obj($o->prop("payment_type"));

		$rent = array_merge(array("rent_period" => "", "sum_core" => ""), safe_array(aw_global_get("rent_calculator")));
		$rent_conditions = obj($payment_type->valid_conditions(array(
			"sum" => $rent["sum_core"],
			"currency" => 354831,
		)));

		if($payment_type->is_a(CL_SHOP_PAYMENT_TYPE))
		{
			$RENT_PERIOD_OPTION = "";
			if($rent_conditions->is_a(CL_SHOP_PAYMENT_TYPE_CONDITIONS))
			{
				foreach($rent_conditions->rent_periods() as $rent_period)
				{
					$this->vars(array(
						"rent_period_value" => $rent_period,
					));
					$RENT_PERIOD_OPTION .= $this->parse("RENT_PERIOD_OPTION".($rent_period == $rent["rent_period"] ? "_SELECTED" : ""));
				}
			}

			$this->vars(array(
				"id" => $o->id(),
				"sum_core" => ifset($rent, "sum_core"),
				"sum_rent" => ifset($rent, "sum_rent"),
				"rent_period" => ifset($rent, "rent_period"),
				"prepayment" => ifset($rent, "prepayment"),
				"single_payment" => ifset($rent, "single_payment"),
				"RENT_PERIOD_OPTION" => $RENT_PERIOD_OPTION,
				"RENT_PERIOD_OPTION_SELECTED" => "",
			));

			if(isset($rent["error"]))
			{
				$this->vars(array(
					"error" => $rent["error"],
				));
				$this->vars(array(
					"ERROR" => $this->parse("ERROR"),
				));
				unset($rent["error"]);
				aw_session_set("rent_calculator", $rent);
			}
			elseif(isset($rent["sum_rent"]))
			{
				if(isset($rent["warning"]))
				{
					$WARNING = "";
					foreach($rent["warning"] as $warning)
					{
						$this->vars(array(
							"warning" => $warning,
						));
						$WARNING .= $this->parse("WARNING");
					}
					$this->vars(array(
						"WARNING" => $WARNING,
					));
				}
				$this->vars(array(
					"RESULT" => $this->parse("RESULT"),
				));
			}
		}

		return $this->parse();
	}

	/**
		@attrib name=calculate params=name nologin=1

		@param id required type=int acl=view

		@param post_ru optional type=string

		@param rent_period optional type=int

		@param sum_core optional type=float
	**/
	public function calculate($arr)
	{
		$o = obj($arr["id"]);
		$payment_type = obj($o->prop("payment_type"));
		$rent = array();

		if($payment_type->is_a(CL_SHOP_PAYMENT_TYPE))
		{
			$rent_conditions = obj($payment_type->valid_conditions(array(
				"sum" => isset($arr["sum_core"]) ? aw_math_calc::string2float($arr["sum_core"]) : 0,
				"currency" => 354831,
			)));
			if($rent_conditions->is_a(CL_SHOP_PAYMENT_TYPE_CONDITIONS))
			{
				$rent = $rent_conditions->calculate_rent(ifset($arr["sum_core"]), ifset($arr["rent_period"]));
			}
		}

		$rent = array_merge($rent, array(
			"sum_core" => ifset($arr["sum_core"]),
			"rent_period" => ifset($arr["rent_period"]),
		));
		aw_session_set("rent_calculator", $rent);

		return !empty($arr["post_ru"]) ? $arr["post_ru"] : $_SERVER["HTTP_REFERER"];
	}

	/**
		@attrib name=get_rent_periods api=1 nologin=1
		@param id required type=int acl=view
			OID of rent calculator object
		@param sum optional type=float
		@param format optional type=string
			[json]
	**/
	public function get_rent_periods($arr)
	{
		$o = obj($arr["id"]);
		$payment_type = obj($o->prop("payment_type"));
		$ret = array();

		if($payment_type->is_a(CL_SHOP_PAYMENT_TYPE))
		{
			$rent_conditions = obj($payment_type->valid_conditions(array(
				"sum" => isset($arr["sum"]) ? aw_math_calc::string2float($arr["sum"]) : 0,
				"currency" => 354831,
			)));
			if($rent_conditions->is_a(CL_SHOP_PAYMENT_TYPE_CONDITIONS))
			{
				$ret = $rent_conditions->rent_periods();
			}
		}

		if($arr["format"] === "json")
		{
			die(json_encode($ret));
		}
	}
}

?>
