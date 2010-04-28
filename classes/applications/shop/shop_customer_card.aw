<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_SHOP_CUSTOMER_CARD relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_shop_customer_cards master_index=brother_of master_table=objects index=aw_oid

@default table=aw_shop_customer_cards
@default group=general

@property customer type=relpicker reltype=RELTYPE_CUSTOMER field=aw_customer
@caption Klient

@property customer_rep type=relpicker reltype=RELTYPE_CUSTOMER_REP multiple=1 store=connect
@caption Esindajad

@property number type=textbox field=aw_number
@caption Number

@property act_from type=date_select field=aw_from
@caption Kehtivuse algus

@property act_to type=date_select field=aw_to
@caption Kehtivuse l&otilde;pp

@property blocked type=checkbox ch_value=1 field=aw_blocked
@caption Blokeeritud

@property sales_limit type=textbox field=aw_sales_limit
@caption M&uuml;&uuml;gilimiit

@property sales_limit_type type=chooser field=aw_sales_limit_type
@caption M&uuml;&uuml;gilimiidi t&uuml;&uuml;p

@property credit_limit type=chooser field=aw_credit_limit
@caption Krediidilimiit

@property currency type=relpicker reltype=RELTYPE_CURRENCY field=aw_currency automatic=1
@caption Valuuta

@property pricelist type=relpicker reltype=RELTYPE_PRICELIST field=aw_pricelist automatic=1
@caption Hinnakiri

@property discount_pct type=textbox field=aw_discount_pct
@caption Soodustuse %

@property sales_warning type=textbox field=aw_sales_warning
@caption Hoiatus m&uuml;&uuml;gil


@reltype CUSTOMER value=1 clid=CL_CRM_COMPANY,CL_CRM_PERSON
@caption Klient

@reltype CUSTOMER_REP value=2 clid=CL_CRM_PERSON
@caption Kliendi esindaja

@reltype CURRENCY value=3 clid=CL_CURRENCY
@caption Valuuta

@reltype PRICELIST value=4 clid=CL_SHOP_PRICE_LIST
@caption Hinnakiri

*/

class shop_customer_card extends class_base
{
	const AW_CLID = 1442;

	function shop_customer_card()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_customer_card",
			"clid" => CL_SHOP_CUSTOMER_CARD
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

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_shop_customer_cards(aw_oid int primary key, aw_customer int, aw_number varchar(255), aw_from int, aw_to int, aw_blocked int, aw_sales_limit double, aw_sales_limit_type int, aw_credit_limit double, aw_currency int, aw_pricelist int, aw_discount_pct double, aw_sales_warning varchar(255))");
			return true;
		}
	}
}

?>
