<?php

namespace automatweb;
/*
@tableinfo aw_prices index=aw_oid master_table=objects master_index=brother_of 

@classinfo syslog_type=ST_PRICE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@default table=objects
@default group=general

	@property type type=select default=1 table=aw_prices field=aw_type
	@caption Hinna t&uuml;&uuml;p

	@property sum type=textbox default=0 table=aw_prices field=aw_sum
	@caption Summa

	@property currency type=select table=aw_prices field=aw_currency
	@caption Valuuta

	@property date_from type=date_select table=aw_prices field=aw_date_from
	@caption Alates

	@property date_to type=date_select table=aw_prices field=aw_date_to
	@caption Kuni

	@property object type=relpicker reltype=RELTYPE_OBJECT table=aw_prices field=aw_object
	@caption Objekst millele hind m&otildejub

	@property price_prop type=textbox table=aw_prices field=aw_prop
	@caption Hinna omadus, (m&otilde;nel klassil v&otilde;ib olla eri liiki hindu)

@reltype OBJECT value=1
@caption Objekt millele hind m&otilde;jub

*/

class price extends class_base
{
	const AW_CLID = 1366;

	function price()
	{
		$this->init(array(
			"tpldir" => "common/price",
			"clid" => CL_PRICE
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
		};
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
		if ($f == "" && $t == "aw_prices")
		{
			$this->db_query("CREATE TABLE aw_prices(aw_oid int primary key,
				aw_verified int,
				aw_type int,
				aw_sum double,
				aw_currency int,
				aw_date_from int,
				aw_date_to int
			)");
			return true;
		}
		else
		{
			switch($f)
			{
				case "aw_object":
					$this->db_add_col($t, array(
						"name" => $f,
						"type" => "int"
					));
					break;
				case "aw_prop":
					$this->db_add_col($t, array(
						"name" => $f,
						"type" => "VARCHAR(64)"
					));
			}
			return true;
		}
		return false;
	}
}
?>
