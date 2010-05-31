<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/procurement_center/procurement_offer_row.aw,v 1.6 2007/11/23 11:05:13 markop Exp $
// procurement_offer_row.aw - Pakkumise rida 
/*

@classinfo syslog_type=ST_PROCUREMENT_OFFER_ROW relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop


@tableinfo aw_procurement_offer_rows index=aw_oid master_table=objects master_index=brother_of

@default table=objects
@default group=general
@default field=meta

	@property product type=textbox size=10 table=aw_procurement_offer_rows field=aw_product
	@caption Toode

	@property amount type=textbox  table=aw_procurement_offer_rows field=aw_amount
	@caption Kogus

	@property b_amount type=textbox  table=aw_procurement_offer_rows field=aw_b_amount
	@caption Ostetav kogus
	
	@property b_price type=textbox  table=aw_procurement_offer_rows field=aw_b_price
	@caption Ostu hind

	@property unit type=select  table=aw_procurement_offer_rows field=aw_unit
	@caption &Uuml;hik

	@property price type=textbox  table=aw_procurement_offer_rows field=aw_price
	@caption Hind

	@property price_amount type=textbox  table=aw_procurement_offer_rows field=aw_price_amount
	@caption Hind koguse puhul
	
	@property currency type=select  table=aw_procurement_offer_rows field=aw_currency
	@caption Valuuta

	@property shipment type=textbox size=10  table=aw_procurement_offer_rows field=aw_shipment
	@caption Tarneaeg

	@property accept type=checkbox ch_value=1  table=aw_procurement_offer_rows field=aw_accept
	@caption Aktsepteeritud


@reltype OFFER value=1 clid=CL_PROCUREMENT_OFFER
@caption Pakkumine

*/

class procurement_offer_row extends class_base
{
	const AW_CLID = 1127;

	function procurement_offer_row()
	{
		$this->init(array(
			"tpldir" => "applications/procurement_center/procurement_offer_row",
			"clid" => CL_PROCUREMENT_OFFER_ROW
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "unit":
				$unit_list = new object_list(array(
					"class_id" => CL_UNIT
				));
				foreach($unit_list->arr() as $unit)
				{
					$prop["options"][$unit->id()] = $unit->prop("unit_code");
				}
				break;
			
			case "currency":
			
				$unit_opts = array();
				$curr_list = new object_list(array(
					"class_id" => CL_CURRENCY
				));
				$prop["options"] = $curr_list->names();
				break;
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- set_property --//
		}
		return $retval;
	}	

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
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
		if ($t == "aw_procurement_offer_rows" && $f == "")
		{
			$this->db_query("CREATE TABLE aw_procurement_offer_rows (aw_oid int primary key)");
			return true;
		}
		switch($f)
		{
		
			case "aw_amount":
			case "aw_b_amount":
			case "aw_price":
			case "aw_b_price":
			case "aw_price_amount":	
				$this->db_add_col($t, array("name" => $f, "type" => "double"));
				return true;	

			case "aw_shipment":
			case "aw_currency":
			case "aw_accept":
			case "aw_unit":
				$this->db_add_col($t, array("name" => $f, "type" => "int"));
				return true;
			case "aw_product":
				$this->db_add_col($t, array("name" => $f, "type" => "varchar(150)"));
				return true;
		}
	}

//-- methods --//
}
?>
