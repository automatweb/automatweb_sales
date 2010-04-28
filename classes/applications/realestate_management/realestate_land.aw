<?php

namespace automatweb;
// realestate_land.aw - Maatükk
/*

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_NEW, CL_REALESTATE_LAND, on_create)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_DELETE, CL_REALESTATE_PROPERTY, on_delete)

@classinfo syslog_type=ST_REALESTATE_LAND relationmgr=yes no_comment=1 no_status=1 trans=1 maintainer=voldemar
@extends applications/realestate_management/realestate_property

@tableinfo realestate_property index=oid master_table=objects master_index=oid

@default table=objects
@default group=grp_detailed
	@property legal_status type=classificator table=realestate_property
	@caption Omandivorm

	@property property_area type=textbox field=meta method=serialize
	@caption Krundi suurus

	@layout box1 type=vbox
	@caption Vahendustasu
	@property transaction_broker_fee type=textbox field=meta method=serialize no_caption=1 parent=box1
	@property transaction_broker_fee_type type=select field=meta method=serialize no_caption=1 parent=box1

	@property estate_price_sqmeter type=textbox field=meta method=serialize
	@caption Krundi ruutmeetri hind

	@property estate_price_total type=text field=meta method=serialize
	@caption Krundi hind kokku

	@property distance_from_tallinn type=textbox field=meta method=serialize
	@caption Kaugus Tallinnast (km)

	@property land_use type=classificator table=realestate_property
	@caption Sihtotstarve

	@property land_use_2 type=classificator table=realestate_property
	@caption Sihtotstarve veel

	@property is_changeable type=checkbox ch_value=1 field=meta method=serialize
	@caption Otstarbe muutmine v&otilde;imalik

	@property has_electricity type=checkbox ch_value=1 field=meta method=serialize
	@caption Elekter

	@property has_sewerage type=checkbox ch_value=1 field=meta method=serialize
	@caption Kanalisatsioon

	@property has_water type=checkbox ch_value=1 field=meta method=serialize
	@caption Vesi

	@property has_zoning_ordinance type=checkbox ch_value=1 field=meta method=serialize
	@caption Detailplaneering

*/

classload("applications/realestate_management/realestate_property");

class realestate_land extends realestate_property
{
	const AW_CLID = 945;

	function realestate_land()
	{
		$this->init(array(
			"tpldir" => "applications/realestate_management/realestate_property",
			"clid" => CL_REALESTATE_LAND
		));
	}

	function callback_on_load ($arr)
	{
		parent::callback_on_load ($arr);
	}

	function get_property($arr)
	{
		parent::get_property ($arr);
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		$this_object =& $arr["obj_inst"];

		switch($prop["name"])
		{
			case "transaction_broker_fee":
			case "estate_price_sqmeter":
			case "estate_price_total":
			case "transaction_selling_price":
				$prop["value"] = number_format ($prop["value"], REALESTATE_NF_DEC_PRICE, REALESTATE_NF_POINT, REALESTATE_NF_SEP);
				break;

			case "transaction_broker_fee_type":
				$prop["options"] = array (
					"1" => t("Lisandub objekti hinnale"),
					"2" => t("Sisaldub objekti hinnas"),
				);
				break;
		}

		return $retval;
	}

	function set_property($arr = array())
	{
		parent::set_property ($arr);
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "transaction_selling_price":
				switch ($arr["request"]["transaction_broker_fee_type"])
				{
					case "1":
						$value = safe_settype_float ($arr["request"]["transaction_broker_fee"]) + safe_settype_float ($arr["request"]["transaction_price_total"]);
						break;

					case "2":
						$value = safe_settype_float ($arr["request"]["transaction_price_total"]);
						break;
				}

				$prop["value"] = $value;
				break;

			case "estate_price_total":
				$prop["value"] = safe_settype_float ($arr["request"]["estate_price_sqmeter"]) * safe_settype_float ($arr["request"]["property_area"]);
				break;

			case "transaction_broker_fee":
			case "estate_price_sqmeter":
				$prop["value"] = safe_settype_float ($prop["value"]);
				break;

			case "legal_status":
				if (empty ($prop["value"]))
				{
					$prop["error"] = t("Kohustuslik v&auml;li");
					return PROP_ERROR;
				}
				break;
		}

		return $retval;
	}

	function callback_mod_reforb($arr)
	{
		parent::callback_mod_reforb ($arr);
		$arr["post_ru"] = post_ru();
	}

	function callback_post_save ($arr)
	{
		parent::callback_post_save ($arr);
	}

	function request_execute ($o)
	{
		return parent::request_execute ($o);
	}

	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	function on_create ($arr)
	{
		parent::on_create ($arr);
	}

	// @attrib name=export_xml
	// @param id required type=int
	// @param no_declaration optional
	function export_xml ($arr)
	{
		return parent::export_xml ($arr);
	}

/**
	@attrib name=pictures_view nologin=1
	@param id required type=int
**/
	function pictures_view ($arr)
	{
		echo parent::pictures_view ($arr);
		exit;
	}

/**
	@attrib name=print nologin=1
	@param id required type=int
	@param contact_type required
	@param show_pictures optional
	@param view_type optional
	@param return_url optional
**/
	function print_view ($arr)
	{
		return parent::print_view ($arr);
	}

	// @attrib name=view
	// @param id required type=int
	// @param view_type required
	// @param return_url optional
	function view ($arr)
	{
		return parent::view ($arr);
	}

	// @attrib name=get_property_data
	// @param id required type=int
	function get_property_data ($arr)
	{
		return parent::get_property_data ($arr);
	}
}

?>
