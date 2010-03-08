<?php
// realestate_housepart.aw - Majaosa
/*

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_NEW, CL_REALESTATE_HOUSEPART, on_create)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_DELETE, CL_REALESTATE_PROPERTY, on_delete)

@classinfo syslog_type=ST_REALESTATE_HOUSEPART relationmgr=yes no_comment=1 no_status=1 trans=1 maintainer=voldemar
@extends applications/realestate_management/realestate_property

@tableinfo realestate_property index=oid master_table=objects master_index=oid

@default table=objects
@default group=grp_main
		@property location_description type=classificator table=realestate_property
		@caption Paiknemine

		@property transaction_sqmeter_price type=textbox field=meta method=serialize
		@caption Ruutmeetri hind

		@property transaction_price_total type=text field=meta method=serialize
		@caption Hoone hind kokku

		@layout box1 type=vbox
		@caption Vahendustasu
		@property transaction_broker_fee type=textbox field=meta method=serialize no_caption=1 parent=box1
		@property transaction_broker_fee_type type=select field=meta method=serialize no_caption=1 parent=box1

		@property transaction_selling_price type=text field=meta method=serialize
		@caption M&uuml;&uuml;gihind

		@property transaction_rent_sqmeter type=textbox field=meta method=serialize
		@caption Ruutmeetri kuu&uuml;&uuml;r

		@property transaction_rent_total type=text field=meta method=serialize
		@caption Kuu&uuml;&uuml;r

		@property estate_price_sqmeter type=textbox field=meta method=serialize
		@caption Krundi ruutmeetri hind

		@property estate_price_total type=text field=meta method=serialize
		@caption Krundi hind kokku

		@property fee_payer type=classificator table=realestate_property
		@caption Maakleritasu tasub

		@property available_from type=date_select field=meta method=serialize default=-1
		@caption Objekti vabastamine

		@property montlhy_expenses type=textbox field=meta method=serialize
		@caption Kommunaalmaksete suurus


@default group=grp_detailed
	@property childtitle100 type=text store=no subtitle=1
	@caption Krunt
		@property property_area type=textbox field=meta method=serialize
		@caption Krundi suurus

		@property legal_status type=classificator table=realestate_property
		@caption Omandivorm

		@property ownership_type type=classificator table=realestate_property
		@caption Varaomand

		@property land_use type=classificator table=realestate_property
		@caption Sihtotstarve

	@property childtitle110 type=text store=no subtitle=1
	@caption Krundi kommunikatsioonid
		@layout box7 type=vbox
		@caption Elekter
		@property communications_electricity type=classificator table=realestate_property no_caption=1 parent=box7
		@property communications_electricity_additional type=textbox field=meta method=serialize no_caption=1 parent=box7

		@layout box8 type=vbox
		@caption Veevarustus
		@property communications_water type=classificator table=realestate_property no_caption=1 parent=box8
		@property communications_water_additional type=textbox field=meta method=serialize no_caption=1 parent=box8

		@layout box9 type=vbox
		@caption Kanalisatsioon
		@property communications_sewerage type=classificator table=realestate_property no_caption=1 parent=box9
		@property communications_sewerage_additional type=textbox field=meta method=serialize no_caption=1 parent=box9

		@property communications_additional type=textarea rows=5 cols=74 field=meta method=serialize
		@caption Lisainfo kommunikatsioonide kohta

	@property childtitle120 type=text store=no subtitle=1
	@caption Maja
		@property year_built type=select field=meta method=serialize
		@caption Ehitusaasta

		@property architect type=textbox field=meta method=serialize
		@caption Arhitekt

		@property building_type type=classificator table=realestate_property
		@caption Hoone t&uuml;&uuml;p

		@property quality_class type=classificator table=realestate_property
		@caption Kvaliteediklass

		@property technical_condition type=classificator table=realestate_property
		@caption Tehniline seisukord

		@property finishing type=classificator table=realestate_property
		@caption Siseviimistlus

		@property total_floor_area type=textbox table=realestate_property
		@caption &uuml;ldpind

		@property usable_area type=textbox field=meta method=serialize
		@caption Kasulik pind

		@property living_area type=textbox field=meta method=serialize
		@caption Eluruumide pind

		@property number_of_storeys type=textbox datatype=int field=meta method=serialize
		@caption Korruseid

	@property childtitle1 type=text store=no subtitle=1
	@caption Ruumid
		@property number_of_rooms type=textbox datatype=int table=realestate_property
		@caption Tubade arv

		@property number_of_bedrooms type=textbox datatype=int field=meta method=serialize
		@caption Magamistubasid

		@property number_of_bathrooms type=textbox datatype=int field=meta method=serialize
		@caption Vannitubasid

		@property number_of_restrooms type=textbox datatype=int field=meta method=serialize
		@caption Tualettruume

		@property number_of_showers type=textbox datatype=int field=meta method=serialize
		@caption Dushiruume

		@property has_wardrobe type=checkbox ch_value=1 field=meta method=serialize
		@caption Garderoob

		@property has_separate_wc type=checkbox ch_value=1 field=meta method=serialize
		@caption WC ja vannituba eraldi

		@property has_garage type=checkbox ch_value=1 field=meta method=serialize
		@caption Garaaz

		@layout box6 type=vbox
		@caption Saunad
		@property has_sauna type=checkbox ch_value=1 field=meta method=serialize parent=box6
		@caption Saun
		@property saunas type=textbox field=meta method=serialize parent=box6
		@caption arv

		@property has_balcony type=checkbox ch_value=1 field=meta method=serialize
		@caption R&otilde;du

		@property has_cellar type=checkbox ch_value=1 field=meta method=serialize
		@caption Kelder

		@property has_attic type=checkbox ch_value=1 field=meta method=serialize
		@caption P&ouml;&ouml;ning

		@property has_fireplace_hall type=checkbox ch_value=1 field=meta method=serialize
		@caption Kaminaruum

		@property has_pool type=checkbox ch_value=1 field=meta method=serialize
		@caption Bassein

		@property has_terrace type=checkbox ch_value=1 field=meta method=serialize
		@caption Terrass

		@property floor_plan_description type=textarea rows=5 cols=74 field=meta method=serialize
		@caption Ruumilahenduse kirjeldus

	@property childtitle2 type=text store=no subtitle=1
	@caption K&uuml;te
		@property has_central_heating type=checkbox ch_value=1 field=meta method=serialize
		@caption Keskk&uuml;te

		@property has_electric_heating type=checkbox ch_value=1 field=meta method=serialize
		@caption Elektrik&uuml;te

		@property has_gas_heating type=checkbox ch_value=1 field=meta method=serialize
		@caption Gaasik&uuml;te

		@property has_wood_heating type=checkbox ch_value=1 field=meta method=serialize
		@caption Ahjuk&uuml;te

		@property has_fireplace_heating type=checkbox ch_value=1 field=meta method=serialize
		@caption Kaminak&uuml;te

		@property has_soil_heating type=checkbox ch_value=1 field=meta method=serialize
		@caption Maak&uuml;te

		@property has_air_heating type=checkbox ch_value=1 field=meta method=serialize
		@caption &otilde;hkk&uuml;te

		@property has_oil_heating type=checkbox ch_value=1 field=meta method=serialize
		@caption &otilde;lik&uuml;te

	@property childtitle3 type=text store=no subtitle=1
	@caption Kommunikatsioonid
		@property has_alarm_installed type=checkbox ch_value=1 field=meta method=serialize
		@caption Signalisatsioon

		@property has_industrial_voltage type=checkbox ch_value=1 field=meta method=serialize
		@caption T&ouml;&ouml;stusvool

		@property has_local_sewerage type=checkbox ch_value=1 field=meta method=serialize
		@caption Lokaalne kanalisatsioon

		@property has_central_sewerage type=checkbox ch_value=1 field=meta method=serialize
		@caption Tsentraalne kanalisatsioon

		@property has_cable_tv type=checkbox ch_value=1
		@caption Kaabel TV

		@property has_phone type=checkbox ch_value=1 field=meta method=serialize
		@caption Telefon

		@property has_central_cold_water type=checkbox ch_value=1 field=meta method=serialize
		@caption Tsentraalne k&uuml;lmaveevarustus

		@property has_central_hot_water type=checkbox ch_value=1 field=meta method=serialize
		@caption Tsentraalne soojaveevarustus

		@property electricity_manf_type type=classificator table=realestate_property
		@caption Elektrienergia

		@property electricity_meter_type type=classificator table=realestate_property
		@caption Elektriarvesti

		@property plumbing_condition type=classificator table=realestate_property
		@caption Torustik/p&uuml;stikud

		@property has_new_radiators type=checkbox ch_value=1 field=meta method=serialize
		@caption Uued radiaatorid

	@property childtitle4 type=text store=no subtitle=1
	@caption Sisustus
		@property has_tv type=checkbox ch_value=1 field=meta method=serialize
		@caption Televiisor

		@property has_shower type=checkbox ch_value=1 field=meta method=serialize
		@caption Dush

		@property has_bath type=checkbox ch_value=1 field=meta method=serialize
		@caption Vann

		@property has_boiler type=checkbox ch_value=1 field=meta method=serialize
		@caption Boiler

		@property has_refrigerator type=checkbox ch_value=1 field=meta method=serialize
		@caption K&uuml;lmik

		@property has_washing_machine type=checkbox ch_value=1 field=meta method=serialize
		@caption Pesumasin

		@property has_furniture type=checkbox ch_value=1 field=meta method=serialize
		@caption M&ouml;&ouml;bel

		@property has_furniture_option type=checkbox ch_value=1 field=meta method=serialize
		@caption M&ouml;&ouml;bli v&otilde;imalus

	@property childtitle5 type=text store=no subtitle=1
	@caption Viimistlus ja ehitus
		@property condition type=classificator table=realestate_property
		@caption Valmidustase

		@property quality_class type=classificator field=meta method=serialize
		@caption Kvaliteediklass

		@property bearing_walls type=classificator table=realestate_property
		@caption Kandeseinad

		@property interior_walls type=classificator table=realestate_property
		@caption Siseseinad

		@property interior_ceilings type=classificator table=realestate_property
		@caption Vahelaed

		@property foundation_type type=classificator table=realestate_property
		@caption Vundament

		@property exterior_finishing type=classificator table=realestate_property
		@caption V&auml;lisviimistlus

		@property roof_type type=classificator table=realestate_property
		@caption Katus

	@property childtitle600 type=text store=no subtitle=1
	@caption Siseviimistlus
		@property kitchen_type type=classificator table=realestate_property
		@caption K&ouml;&ouml;k

		@layout box4 type=vbox
		@caption K&ouml;&ouml;giseinad
		@property kitchen_walls type=classificator table=realestate_property no_caption=1 parent=box4
		@property kitchen_walls_description type=textbox field=meta method=serialize no_caption=1 parent=box4

		@property kitchen_floor type=classificator table=realestate_property
		@caption K&ouml;&ouml;gip&otilde;rand

		@property stove_type type=classificator table=realestate_property
		@caption Pliit

		@property kitchen_furniture_option type=classificator table=realestate_property
		@caption M&uuml;&uuml;gis

		@layout box5 type=vbox
		@caption Toaseinad
		@property room_walls type=classificator table=realestate_property no_caption=1 parent=box5
		@property room_walls_description type=textbox field=meta method=serialize no_caption=1 parent=box5

		@property room_floors type=classificator table=realestate_property
		@caption Toap&otilde;rand

		@property lavatories_condition type=classificator table=realestate_property
		@caption Sanruumid

		@property lavatory_equipment_condition type=classificator table=realestate_property
		@caption Santehnika

		@property windows_type type=classificator table=realestate_property
		@caption Aknad

		@layout box3 type=vbox
		@caption Siseuksed
		@property doors_condition type=classificator table=realestate_property no_caption=1 parent=box3
		@property doors_condition_description type=textbox field=meta method=serialize no_caption=1 parent=box3

		@property has_security_door type=checkbox ch_value=1 field=meta method=serialize
		@caption Turvauks

		@layout box2 type=vbox
		@caption Parkett
		@property has_parquet type=checkbox ch_value=1 field=meta method=serialize parent=box2
		@caption Parkett
		@property parquet_type type=classificator table=realestate_property no_caption=1 parent=box2
		@property parquet_type_other type=textbox field=meta method=serialize parent=box2
		@caption Muu

		@property finishing_condition type=classificator table=realestate_property
		@caption Valmidus


*/

classload("applications/realestate_management/realestate_property");

class realestate_housepart extends realestate_property
{
	function realestate_housepart()
	{
		$this->init(array(
			"tpldir" => "applications/realestate_management/realestate_housepart",
			"clid" => CL_REALESTATE_HOUSEPART
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
		$this_object =& $arr["obj_inst"];

		switch($prop["name"])
		{
			case "transaction_price_total":
			case "transaction_sqmeter_price":
			case "transaction_selling_price":
			case "transaction_rent_total":
			case "estate_price_total":
			case "estate_price_sqmeter":
			case "transaction_rent_sqmeter":
			case "montlhy_expenses":
			case "transaction_broker_fee":
				$prop["value"] = number_format ($prop["value"], REALESTATE_NF_DEC_PRICE, REALESTATE_NF_POINT, REALESTATE_NF_SEP);
				break;

			case "living_area":
			case "usable_area":
			case "property_area":
			case "total_floor_area":
				$prop["value"] = number_format ($prop["value"], REALESTATE_NF_DEC, REALESTATE_NF_POINT, REALESTATE_NF_SEP);
				break;

			case "transaction_broker_fee_type":
				$prop["options"] = array (
					"1" => t("Lisandub objekti hinnale"),
					"2" => t("Sisaldub objekti hinnas"),
				);
				break;

			case "year_built":
				$empty = array ("" => "");
				$centuries = range (19,11);
				$years = range (date ("Y"), 1901);

				foreach ($years as $year)
				{
					$options[$year] = $year;
				}

				foreach ($centuries as $century)
				{
					$options[($century - 1)*100] = sprintf (t("%s saj."), $century);
				}

				$prop["options"] = $options;
				break;

			case "number_of_rooms":
				$prop["options"] = range (1, 30);
				break;

			case "number_of_bedrooms":
				$prop["options"] = range (1, 9);
				break;

			case "number_of_bathrooms":
				$prop["options"] = range (1, 5);
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
			case "transaction_sqmeter_price":
			case "transaction_selling_price":
			case "estate_price_sqmeter":
			case "transaction_rent_sqmeter":
			case "living_area":
			case "usable_area":
			case "property_area":
			case "montlhy_expenses":
			case "total_floor_area":
			case "transaction_broker_fee":
				$prop["value"] = safe_settype_float ($prop["value"]);
				break;

			case "transaction_price_total":
				$prop["value"] = safe_settype_float ($arr["request"]["transaction_sqmeter_price"]) * safe_settype_float ($arr["request"]["total_floor_area"]);
				break;

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

			case "transaction_rent_total":
				$prop["value"] = safe_settype_float ($arr["request"]["transaction_rent_sqmeter"]) * safe_settype_float ($arr["request"]["total_floor_area"]);
				break;

			case "estate_price_total":
				$prop["value"] = safe_settype_float ($arr["request"]["estate_price_sqmeter"]) * safe_settype_float ($arr["request"]["property_area"]);
				break;

			case "legal_status":
				if (empty ($prop["value"]))
				{
					$prop["error"] = t("Kohustuslik v&auml;li");
					return PROP_ERROR;
				}
				break;

			case "year_built":
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
