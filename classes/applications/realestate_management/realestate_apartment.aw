<?php
/*

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_NEW, CL_REALESTATE_APARTMENT, on_create)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_DELETE, CL_REALESTATE_PROPERTY, on_delete)

@classinfo syslog_type=ST_REALESTATE_APARTMENT relationmgr=yes no_comment=1 no_status=1 trans=1 maintainer=voldemar
@extends applications/realestate_management/realestate_property

@tableinfo realestate_property index=oid master_table=objects master_index=oid

@default table=objects

@default group=advertisement_data
	@property show_apartment_number type=checkbox ch_value=1 field=meta method=serialize
	@caption Näita korteri numbrit

	@layout box1 type=vbox
	@caption Vahendustasu

		@property transaction_broker_fee type=textbox field=meta method=serialize no_caption=1 parent=box1

		@property transaction_broker_fee_type type=select field=meta method=serialize no_caption=1 parent=box1

	@property fee_payer type=classificator table=realestate_property
	@caption Maakleritasu tasub

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

	@property available_from type=date_select field=meta method=serialize default=-1
	@caption Objekti vabastamine

@default group=grp_main
	@groupinfo temp caption="põhimõtteliselt prügikast"
	@default group=temp

	@groupinfo apartment_info parent=grp_detailed caption="Korteri üldinfo"
	@default group=apartment_info

		@property floor type=textbox datatype=int field=meta method=serialize
		@caption Korrus

		@property number_of_rooms type=textbox datatype=int table=realestate_property
		@caption Tubade arv

		@property total_floor_area type=textbox table=realestate_property
		@caption &Uuml;ldpind


		@property heatable_area type=textbox field=meta method=serialize
		@caption K&ouml;etav pind

		@property ownership_type type=classificator table=realestate_property
		@caption Varaomand

		@property condition type=classificator table=realestate_property
		@caption Valmidus

		@property montlhy_expenses type=textbox field=meta method=serialize
		@caption Kommunaalmaksete suurus

	@groupinfo house_and_property parent=grp_detailed caption="Krunt ja maja"
	@default group=house_and_property

		@property number_of_storeys type=textbox datatype=int field=meta method=serialize group=apartment_info
		@caption Korruseid

		@property year_built type=select field=meta method=serialize
		@caption Ehitusaasta

		@property architect type=textbox field=meta method=serialize
		@caption Arhitekt

		@property location_description type=classificator table=realestate_property
		@caption Paiknemine

		@property building_type type=classificator table=realestate_property
		@caption Hoone t&uuml;&uuml;p

		@property has_lift type=checkbox ch_value=1 field=meta method=serialize
		@caption Lift

		@property has_cellar type=checkbox ch_value=1 field=meta method=serialize
		@caption Kelder

		@property has_parking_spot type=checkbox ch_value=1 field=meta method=serialize
		@caption Parkimiskoht

		@layout box8 type=vbox
		@caption Garaazh

		@property has_garage type=checkbox ch_value=1 field=meta method=serialize parent=box8

		@property number_of_garages type=textbox datatype=int field=meta method=serialize parent=box8
		@caption Arv

		@property heating_type type=textbox field=heating_type table=realestate_property
		@caption K&uuml;tte t&uuml;&uuml;p

		@property building_society_state type=classificator table=realestate_property
		@caption &uuml;histu

		@property facade_condition type=classificator table=realestate_property
		@caption Fassaad

		@property has_hallway_locked type=checkbox ch_value=1 field=meta method=serialize
		@caption Trepikoda lukus

		@property hallway_condition type=classificator table=realestate_property
		@caption Trepikoja seisukord

		@property roof_condition type=classificator table=realestate_property
		@caption Katuse seisund

		@property plumbing_condition type=classificator table=realestate_property
		@caption Torustik/p&uuml;stikud

		@property has_new_radiators type=checkbox ch_value=1 field=meta method=serialize
		@caption Uued radiaatorid


		@property property_area type=textbox field=meta method=serialize
		@caption Krundi suurus

		@property privatization type=classificator table=realestate_property
		@caption Maa erastamine

		@property building_additional_info type=textarea rows=5 cols=74 field=meta method=serialize
		@caption Lisainfo maja kohta

	@groupinfo kitchen parent=grp_detailed caption="K&ouml;&ouml;k"
	@default group=kitchen

		@property kitchen_area type=textbox field=meta method=serialize
		@caption K&ouml;&ouml;gi pindala

		@property kitchen_type type=classificator table=realestate_property
		@caption K&ouml;&ouml;k

		@property kitchen_furniture_option type=classificator table=realestate_property
		@caption M&uuml;&uuml;gis

		@property kitchen_furniture_condition type=classificator table=realestate_property
		@caption M&ouml;&ouml;bel

		@property kitchenware_condition type=classificator table=realestate_property
		@caption K&ouml;&ouml;gitehnika

		@property stove_type type=classificator table=realestate_property
		@caption Pliit

		@layout box4 type=vbox
		@caption Köögiseinad
		@property kitchen_walls type=classificator table=realestate_property no_caption=1 parent=box4
		@property kitchen_walls_description type=textbox field=meta method=serialize no_caption=1 parent=box4

		@property kitchen_floor type=classificator table=realestate_property
		@caption K&ouml;&ouml;gip&otilde;rand

		@property kitchen_furniture type=classificator table=realestate_property
		@caption Sissej&auml;&auml;v k&ouml;&ouml;gim&ouml;&ouml;bel/tehnika

	@groupinfo rooms parent=grp_detailed caption="Toad"
	@default group=rooms
//kordab
		@property number_of_rooms type=textbox datatype=int table=realestate_property
		@caption Tubade arv

		@property apartment_situation type=classificator table=realestate_property
		@caption Paigutus

		@property room_sizes type=textarea field=meta method=serialize
		@caption Tubade suurused

		@property number_of_bedrooms type=textbox datatype=int field=meta method=serialize
		@caption Magamistubasid

		@property has_wardrobe type=checkbox ch_value=1 field=meta method=serialize
		@caption Garderoob

		@layout box2 type=vbox
		@caption R&otilde;dud
		@property has_balcony type=checkbox ch_value=1 field=meta method=serialize parent=box2
		@caption R&otilde;du
		@property number_of_balconies type=textbox datatype=int field=meta method=serialize parent=box2
		@caption Arv

		@property has_terrace type=checkbox ch_value=1 field=meta method=serialize
		@caption Terrass

		@property view type=textbox field=meta method=serialize
		@caption Vaade

		@property rooms_additional type=textarea rows=5 cols=74 field=meta method=serialize
		@caption Lisainfo tubade kohta


		@property childtitle60 type=text store=no subtitle=1
		@caption Viimistlus

			@property rooms_condition type=textbox table=realestate_property field=rooms_condition
			@caption Seisukord

			@property windows_type type=classificator table=realestate_property
			@caption Aknad

			@layout box3 type=vbox
			@caption Siseuksed
			@property doors_condition type=classificator table=realestate_property no_caption=1 parent=box3
			@property doors_condition_description type=textbox field=meta method=serialize no_caption=1 parent=box3

			@layout box5 type=vbox
			@caption Toaseinad
			@property room_walls type=classificator table=realestate_property no_caption=1 parent=box5
			@property room_walls_description type=textbox field=meta method=serialize no_caption=1 parent=box5

			@property room_floors type=classificator table=realestate_property
			@caption Toap&otilde;rand

			@layout box9 type=vbox
			@caption Parkett
			@property has_parquet type=checkbox ch_value=1 field=meta method=serialize parent=box9
			@caption Parkett
			@property parquet_type type=classificator table=realestate_property no_caption=1 parent=box9
			@property parquet_type_other type=textbox field=meta method=serialize parent=box9
			@caption Muu

			@property ceilings type=textbox field=meta method=serialize
			@caption Laed

			@property quality_class type=classificator field=meta method=serialize
			@caption Kvaliteediklass

			@property has_security_door type=checkbox ch_value=1 field=meta method=serialize
			@caption Turvauks

	@property childtitle3 type=text store=no subtitle=1
	@caption Kommunikatsioonid
		@property has_alarm_installed type=checkbox ch_value=1 field=meta method=serialize
		@caption Signalisatsioon

		@property has_cable_tv type=checkbox ch_value=1 field=meta method=serialize
		@caption Kaabel TV

		@property has_phone type=checkbox ch_value=1 field=meta method=serialize
		@caption Telefon

		@property has_internet type=checkbox ch_value=1 field=meta method=serialize
		@caption Internet

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

	@property childtitle4 type=text store=no subtitle=1
	@caption Sisustus
		@property has_fireplace type=checkbox ch_value=1 field=meta method=serialize
		@caption Kamin

		@property has_tv type=checkbox ch_value=1 field=meta method=serialize
		@caption Televiisor

		@property has_refrigerator type=checkbox ch_value=1 field=meta method=serialize
		@caption K&uuml;lmik

		@property has_dishwasher type=checkbox ch_value=1 field=meta method=serialize
		@caption N&otilde;udepesumasin

		@property has_furniture type=checkbox ch_value=1 field=meta method=serialize
		@caption M&ouml;&ouml;bel

		@property has_furniture_option type=checkbox ch_value=1 field=meta method=serialize
		@caption M&ouml;&ouml;bli v&otilde;imalus

		@property has_stove type=checkbox ch_value=1 field=meta method=serialize
		@caption Pliit

	@property childtitle9 type=text store=no subtitle=1
	@caption Seadmed
		@property has_intercom type=checkbox ch_value=1 field=meta method=serialize
		@caption Fonolukk

		@property has_code_lock type=checkbox ch_value=1 field=meta method=serialize
		@caption Koodlukk

		@layout box7 type=vbox
		@caption Elektriarvesti
		@property electricity_meter_type type=classificator table=realestate_property no_caption=1 parent=box7
		@property electricity_meter_param type=textbox field=meta method=serialize no_caption=1 parent=box7
		@caption kW/A


	@groupinfo san_rooms parent=grp_detailed caption="Sanitaarruumid"
	@default group=san_rooms

		@property number_of_bathrooms type=textbox datatype=int field=meta method=serialize
		@caption Vannitubasid

		@property has_separate_wc type=checkbox ch_value=1 field=meta method=serialize
		@caption WC ja vannituba eraldi

		@property bathroom_has_window type=checkbox ch_value=1 field=meta method=serialize
		@caption Aken

		@property bathroom_has_floor_heating type=checkbox ch_value=1 field=meta method=serialize
		@caption Põrandaküte

		@property has_sauna type=checkbox ch_value=1 field=meta method=serialize
		@caption Saun

		@property lavatories_condition type=classificator table=realestate_property
		@caption Sanruumid

		@property is_plated type=checkbox ch_value=1 field=meta method=serialize
		@caption Plaaditud

		@property lavatory_equipment_condition type=classificator table=realestate_property
		@caption Santehnika

		@property has_shower type=checkbox ch_value=1 field=meta method=serialize
		@caption Dush

		@layout box6 type=vbox
		@caption Vann
		@property has_bath type=checkbox ch_value=1 field=meta method=serialize parent=box6
		@caption Vann
		@property bath_additional type=textbox field=meta method=serialize no_caption=1 parent=box6

		@property has_boiler type=checkbox ch_value=1 field=meta method=serialize
		@caption Boiler

		@property has_water_meters type=checkbox ch_value=1 field=meta method=serialize
		@caption Veem&otilde;&otilde;tjad

		@property has_washing_machine type=checkbox ch_value=1 field=meta method=serialize
		@caption Pesumasin

	@groupinfo temp caption="Muu"
	@default group=temp

//see on nagu miski teine omandi asi ka olemas... ei tea miks
		@property legal_status type=classificator table=realestate_property
		@caption Omandivorm


		@property is_middle_floor type=hidden table=realestate_property
		@caption Pole esimene ega viimane korrus

*/

classload("applications/realestate_management/realestate_property");

class realestate_apartment extends realestate_property
{
	function realestate_apartment()
	{
		$this->init(array(
			"tpldir" => "applications/realestate_management/realestate_property",
			"clid" => CL_REALESTATE_APARTMENT
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
			case "transaction_selling_price":
			case "transaction_price_total":
			case "estate_price_sqmeter":
			case "transaction_rent_sqmeter":
			case "montlhy_expenses":
			case "transaction_rent_total":
			case "estate_price_total":
			case "transaction_broker_fee":
				$prop["value"] = number_format ($prop["value"], REALESTATE_NF_DEC_PRICE, REALESTATE_NF_POINT, REALESTATE_NF_SEP);
				break;

			case "property_area":
			case "heatable_area":
			case "kitchen_area":
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
/*
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
				$prop["value"] = (int)($prop["value"]);
				$prop["options"] = range (1, 50);
			//	arr($prop["value"]);
				break;
*/		}

		return $retval;
	}

	function set_property($arr = array())
	{
		$retval = PROP_OK;
		$retval = parent::set_property ($arr);
		$prop = &$arr["prop"];

		switch($prop["name"])
		{
			case "estate_price_sqmeter":
			case "transaction_rent_sqmeter":
			case "property_area":
			case "montlhy_expenses":
			case "heatable_area":
			case "kitchen_area":
			case "transaction_broker_fee":
				$prop["value"] = safe_settype_float ($prop["value"]);
				break;

			case "transaction_price_total":
				$prop["value"] = safe_settype_float ($arr["request"]["transaction_sqmeter_price"]) * safe_settype_float ($arr["request"]["total_floor_area"]);
				break;

			case "transaction_rent_total":
				$prop["value"] = safe_settype_float ($arr["request"]["transaction_rent_sqmeter"]) * safe_settype_float ($arr["request"]["total_floor_area"]);
				break;

			case "estate_price_total":
				$prop["value"] = safe_settype_float ($arr["request"]["estate_price_sqmeter"]) * safe_settype_float ($arr["request"]["property_area"]);
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

	function callback_pre_save ($arr)
	{
		if (method_exists (parent, "callback_pre_save"))
		{
			parent::callback_pre_save ($arr);
		}

		$this_object =& $arr["obj_inst"];

		if ($arr["request"]["number_of_storeys"] < $arr["request"]["floor"])
		{
			echo t("Viga andmetes: korruste arv korrusest v&auml;iksem. ");
		}

		### set middle floor property
		$is_middle_floor = 0;

		if (
			isset ($arr["request"]["number_of_storeys"]) and
			isset ($arr["request"]["floor"]) and
			($arr["request"]["number_of_storeys"] - $arr["request"]["floor"]) and
			($arr["request"]["floor"] != 1) and
			($arr["request"]["number_of_storeys"] > 2)
		)
		{
			$is_middle_floor = 1;
		}

		$this_object->set_prop ("is_middle_floor", $is_middle_floor);
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
