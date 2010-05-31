<?php

namespace automatweb;
/*

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_NEW, CL_REALESTATE_COMMERCIAL, on_create)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_DELETE, CL_REALESTATE_PROPERTY, on_delete)

@classinfo syslog_type=ST_REALESTATE_COMMERCIAL relationmgr=yes no_comment=1 no_status=1 trans=1 maintainer=voldemar
@extends applications/realestate_management/realestate_property

@tableinfo realestate_property index=oid master_table=objects master_index=oid

@default table=objects
@default group=grp_main
	@property transaction_broker_fee type=textbox datatype=int field=meta method=serialize
	@caption Maakleritasu

	@property transaction_monthly_rent type=textbox datatype=int field=meta method=serialize
	@caption Kuurent

	@property vat_excluded type=checkbox ch_value=1 field=meta method=serialize
	@caption Käibemaks lisandub

	@property transaction_additional_costs type=textbox datatype=int field=meta method=serialize
	@caption Kõrvalkulud EEK/m2

@default group=grp_detailed
	@property usage_purpose type=classificator table=realestate_property
	@caption Kasutusotstarve

	@property condition type=classificator table=realestate_property
	@caption Valmidus

	@property quality_class type=classificator field=meta method=serialize
	@caption Kvaliteediklass

	@property total_floor_area type=textbox table=realestate_property
	@caption Üldpind

	@property number_of_rooms type=textbox datatype=int table=realestate_property
	@caption Ruumide arv

	@property number_of_storeys type=textbox datatype=int field=meta method=serialize
	@caption Korruseid

	@property floor type=textbox datatype=int field=meta method=serialize
	@caption Korrus

	@property property_area type=textbox field=meta method=serialize
	@caption Krundi suurus

	@property has_lift type=checkbox ch_value=1 field=meta method=serialize
	@caption Lift

	@property has_kitchen type=checkbox ch_value=1 field=meta method=serialize
	@caption Köök

	@property room_height type=textbox field=meta method=serialize
	@caption Ruumide kõrgus

	@property door_height type=textbox field=meta method=serialize
	@caption Ukse kõrgus

	@property loading_facilities type=classificator table=realestate_property
	@caption Kauba laadmine

	@property licenced_for type=textbox field=meta method=serialize
	@caption Kasutusload

	@property childtitle1 type=text store=no subtitle=1
	@caption Küte ja ventilatsioon
		@property has_central_heating type=checkbox ch_value=1 field=meta method=serialize
		@caption Keskküte

		@property has_electric_heating type=checkbox ch_value=1 field=meta method=serialize
		@caption Elektriküte

		@property has_gas_heating type=checkbox ch_value=1 field=meta method=serialize
		@caption Gaasiküte

		@property has_oil_heating type=checkbox ch_value=1 field=meta method=serialize
		@caption Õliküte

		@property has_forced_ventilation type=checkbox ch_value=1 field=meta method=serialize
		@caption Sundventilatsioon

		@property has_airconditioning type=checkbox ch_value=1 field=meta method=serialize
		@caption Kliimaseade

		@property has_refrigeration_equipment type=textbox field=meta method=serialize
		@caption Jahutusseadmed

	@property childtitle2 type=text store=no subtitle=1
	@caption Kommunikatsioonid
		@property has_alarm_installed type=checkbox ch_value=1 field=meta method=serialize
		@caption Signalisatsioon

		@property has_industrial_voltage type=checkbox ch_value=1 field=meta method=serialize
		@caption Tööstusvool

		@property has_internet type=checkbox ch_value=1 field=meta method=serialize
		@caption Internet

		@property has_isdn type=checkbox ch_value=1 field=meta method=serialize
		@caption ISDN

		@property number_of_phone_lines type=textbox field=meta method=serialize
		@caption Telefone

	@property childtitle3 type=text store=no subtitle=1
	@caption Sisustus
		@property has_shower type=checkbox ch_value=1 field=meta method=serialize
		@caption Dush

		@property has_refrigerator type=checkbox ch_value=1 field=meta method=serialize
		@caption Külmik

		@property has_furniture type=checkbox ch_value=1 field=meta method=serialize
		@caption Mööbel

		@property has_furniture_option type=checkbox ch_value=1 field=meta method=serialize
		@caption Mööbli võimalus

*/

classload("applications/realestate_management/realestate_property");

class realestate_commercial extends realestate_property
{
	const AW_CLID = 947;

	function realestate_commercial ()
	{
		$this->init(array(
			"tpldir" => "applications/realestate_management/realestate_property",
			"clid" => CL_REALESTATE_COMMERCIAL
		));
	}

	function callback_on_load ($arr)
	{
		parent::callback_on_load ($arr);
	}

	function get_property($arr)
	{
		$retval = PROP_OK;
		$retval = parent::get_property ($arr);
		$prop = &$arr["prop"];

		switch($prop["name"])
		{
			case "transaction_additional_costs":
			case "transaction_monthly_rent":
			case "transaction_broker_fee":
				$prop["value"] = number_format ($prop["value"], REALESTATE_NF_DEC_PRICE, REALESTATE_NF_POINT, REALESTATE_NF_SEP);
				break;

			case "total_floor_area":
			case "property_area":
				$prop["value"] = number_format ($prop["value"], REALESTATE_NF_DEC, REALESTATE_NF_POINT, REALESTATE_NF_SEP);
				break;

			case "number_of_rooms":
				$prop["options"] = range (1, 30);
				break;

			case "number_of_storeys":
				$prop["options"] = range (1, 50);
				break;
		}

		return $retval;
	}

	function set_property($arr = array())
	{
		$retval = PROP_OK;
		$retval = parent::set_property ($arr);
		$prop = &$arr["prop"];

		switch($prop["name"])
		{
			case "total_floor_area":
			case "property_area":
			case "transaction_additional_costs":
			case "transaction_monthly_rent":
			case "transaction_broker_fee":
				$prop["value"] = safe_settype_float ($prop["value"]);
				break;
		}

		return $retval;
	}

	function callback_mod_reforb(&$arr)
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
