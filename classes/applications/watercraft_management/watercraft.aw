<?php

// watercraft.aw - Veesõiduk
/*

@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo watercraft index=oid master_table=objects master_index=oid

@default table=objects
@default group=general

	@property deal_type type=select table=watercraft
	@caption Tehingu t&uuml;&uuml;p

	@property watercraft_type type=select table=watercraft
	@caption Aluse t&uuml;&uuml;p

	@property watercraft_type_other type=textbox table=watercraft
	@caption Muu aluset&uuml;&uuml;p

	@property watercraft_accessories type=chooser orient=vertical multiple=1 field=meta method=serialize
	@caption Varustus/tarvikud (hetkel ei salvestu)

	@property manufacturer type=select table=watercraft
	@caption Tootja

	@property manufacturer_other type=textbox table=watercraft
	@caption Tootja

	@property brand type=textbox table=watercraft
	@caption Mark

	@property body_material type=select table=watercraft
	@caption Kerematerjal

	@property body_material_other type=textbox table=watercraft
	@caption Muu kerematerjal

	property location type=relpicker reltype=RELTYPE_LOCATION automatic=1 table=watercraft
	caption Asukoht (praegu v&otilde;etakse vaikimisi k&otilde;ik s&uuml;steemis olevad linnad)

	@property location type=select table=watercraft
	@caption Asukoht

	@property location_other type=textbox table=watercraft
	@caption Muu asukoht

	@property location_precise type=textbox table=watercraft
	@caption T&auml;psem asukoht

	@property condition type=select table=watercraft
	@caption Seisukord

	@property condition_info type=textbox table=watercraft
	@caption Lisainfo seisukorra kohta

	@property seller type=relpicker reltype=RELTYPE_SELLER table=watercraft
	@caption M&uuml;&uuml;ja

	@property contact_name type=textbox table=watercraft
	@caption Kontaktisiku nimi
	@comment Kontaktisiku nimi, juhuks kui kuulutuse kontaktisik on m&uuml;&uuml;jast erinev.

	@property contact_email type=textbox table=watercraft
	@caption Kontaktisiku e-mail
	@comment Kontaktisiku e-mail, juhuks kui kuulutuse kontaktisik on m&uuml;&uuml;jast erinev.

	@property contact_phone type=textbox table=watercraft
	@caption Kontaktisiku telefoninumber
	@comment Kontaktisiku telefoninumber, juhuks kui kuulutuse kontaktisik on m&uuml;&uuml;jast erinev.

	@property price type=textbox table=watercraft
	@caption Hind

	@property visible type=checkbox ch_value=1 table=watercraft
	@caption N&auml;htav

	@property archived type=checkbox ch_value=1 table=watercraft
	@caption Arhiveeritud

@groupinfo images caption="Pildid"
@default group=images

	@property images_toolbar type=toolbar no_caption=1
	@caption Piltide t&ouml;&ouml;riistariba

	@property images_table type=table no_caption=1
	@caption Pildid

@groupinfo parameters caption="Parameetrid"
@default group=parameters

	@property centreboard type=chooser orient=vertical multiple=1 store=no
	@caption Kiil/Svert (hetkel ei salvestu)

	@property length type=textbox table=watercraft
	@caption Pikkus (m)

	@property width type=textbox table=watercraft
	@caption Laius (m)

	@property height type=textbox table=watercraft
	@caption K&otilde;rgus (m)

	@property weight type=textbox table=watercraft
	@caption Raskus (kg)

	@property draught type=textbox table=watercraft
	@caption S&uuml;vis (cm)

	@property creation_year type=select table=watercraft
	@caption Valmistamisaasta

	@property passanger_count type=select table=watercraft
	@caption Reisijaid

	@property sleeper_count type=select table=watercraft
	@caption Magamiskohti

@groupinfo engines caption="Mootor(id)"
@default group=engines

	@property engine_manufacturer type=textbox table=watercraft
	@caption Tootja

	@property engine_model type=textbox table=watercraft
	@caption Mudel

	@property engine_count type=select table=watercraft
	@caption Mootorite arv

	@property engine_type type=select table=watercraft
	@caption T&uuml;&uuml;p

	@property engine_capacity type=textbox table=watercraft
	@caption T&ouml;&ouml;maht (cm3)

	@property fuel_tank type=textbox table=watercraft
	@caption K&uuml;tusepaak (l)

	@property fuel type=select table=watercraft
	@caption K&uuml;tus

	@property engine_power type=textbox table=watercraft
	@caption V&otilde;imsus

	@property engine_cooling type=select table=watercraft
	@caption Jahutus

@groupinfo mast caption="Mast(id)"
@default group=mast

	@property mast_material type=select table=watercraft
	@caption Materjal

	@property mast_material_other type=textbox table=watercraft
	@caption Muu materjal

	@property mast_count type=select table=watercraft
	@caption Mastide arv

@groupinfo sail caption="Purjed"
@default group=sail

	@property sail_table type=table
	@caption Purjed

	@property sail_info type=textarea rows=10 cols=60 table=watercraft
	@caption Lisainfo

@groupinfo additional_equipment caption="Lisavarustus"
@default group=additional_equipment

	property additional_equipment_table type=table no_caption=1
	caption Lisavarustus

			'electricity_110V' => array( 'caption' => t('Elekter 110V'), 'amount' => null ),
	@layout electricity_110V_row type=hbox width=10%:90%
	@caption Elekter 110V

		@property electricity_110V_sel type=checkbox ch_value=1 table=watercraft parent=electricity_110V_row no_caption=1
		caption Olemas

		@property electricity_110V_info type=textbox table=watercraft parent=electricity_110V_row no_caption=1
		@caption Info

			'electricity_220V' => array( 'caption' => t('Elekter 220V'), 'amount' => null ),
	@layout electricity_220V_row type=hbox width=10%:90%
	@caption Elekter 220V

		@property electricity_220V_sel type=checkbox ch_value=1 table=watercraft parent=electricity_220V_row no_caption=1
		caption Olemas

		@property electricity_220V_info type=textbox table=watercraft parent=electricity_220V_row no_caption=1
		@caption Info

			'radio_station' => array( 'caption' => t('Raadiojaam'), 'amount' => null),
	@layout radio_station_row type=hbox width=10%:90%
	@caption Raadiojaam

		@property radio_station_sel type=checkbox ch_value=1 table=watercraft parent=radio_station_row no_caption=1
		caption Olemas

		@property radio_station_info type=textbox table=watercraft parent=radio_station_row no_caption=1
		@caption Info

			'stereo' => array( 'caption' => t('Stereo'), 'amount' => null ),
	@layout stereo_row type=hbox width=10%:90%
	@caption Stereo

		@property stereo_sel type=checkbox ch_value=1 table=watercraft parent=stereo_row no_caption=1
		caption Olemas

		@property stereo_info type=textbox table=watercraft parent=stereo_row no_caption=1
		@caption Info

			'cd' => array( 'caption' => t('CD'), 'amount' => null ),
	@layout cd_row type=hbox width=10%:90%
	@caption CD

		@property cd_sel type=checkbox ch_value=1 table=watercraft parent=cd_row no_caption=1
		caption Olemas

		@property cd_info type=textbox table=watercraft parent=cd_row no_caption=1
		@caption Info

			'waterproof_speakers' => array( 'caption' => t('Veekindlad k&otilde;larid'), 'amount' => null ),
	@layout waterproof_speakers_row type=hbox width=10%:90%
	@caption Veekindlad k&otilde;larid

		@property waterproof_speakers_sel type=checkbox ch_value=1 table=watercraft parent=waterproof_speakers_row no_caption=1
		caption Olemas

		@property waterproof_speakers_info type=textbox table=watercraft parent=waterproof_speakers_row no_caption=1
		@caption Info

			'burglar_alarm' => array( 'caption' => t('Signalisatsioon'), 'amount' => null ),
	@layout burglar_alarm_row type=hbox width=10%:90%
	@caption Signalisatsioon

		@property burglar_alarm_sel type=checkbox ch_value=1 table=watercraft parent=burglar_alarm_row no_caption=1
		caption Olemas

		@property burglar_alarm_info type=textbox table=watercraft parent=burglar_alarm_row no_caption=1
		@caption Info

			'navigation_system' => array( 'caption' => t('Navigatsioonis&uuml;steem'), 'amount' => null ),
	@layout navigation_system_row type=hbox width=10%:90%
	@caption Navigatsioonis&uuml;steem

		@property navigation_system_sel type=checkbox ch_value=1 table=watercraft parent=navigation_system_row no_caption=1
		caption Olemas

		@property navigation_system_info type=textbox table=watercraft parent=navigation_system_row no_caption=1
		@caption Info

			'navigation_lights' => array( 'caption' => t('Navigatsioonituled'), 'amount' => null ),
	@layout navigation_lights_row type=hbox width=10%:90%
	@caption Navigatsioonituled

		@property navigation_lights_sel type=checkbox ch_value=1 table=watercraft parent=navigation_lights_row no_caption=1
		caption Olemas

		@property navigation_lights_info type=textbox table=watercraft parent=navigation_lights_row no_caption=1
		@caption Info

			'trailer' => array( 'caption' => t('Treiler'), 'amount' => null ),
	@layout trailer_row type=hbox width=10%:90%
	@caption Treiler

		@property trailer_sel type=checkbox ch_value=1 table=watercraft parent=trailer_row no_caption=1
		caption Olemas

		@property trailer_info type=textbox table=watercraft parent=trailer_row no_caption=1
		@caption Info

			'toilet' => array( 'caption' => t('Tualett'), 'amount' => null ),
	@layout toilet_row type=hbox width=10%:90%
	@caption Tualett

		@property toilet_sel type=checkbox ch_value=1 table=watercraft parent=toilet_row no_caption=1
		caption Olemas

		@property toilet_info type=textbox table=watercraft parent=toilet_row no_caption=1
		@caption Info

			'shower' => array( 'caption' => t('Dush'), 'amount' => null ),
	@layout shower_row type=hbox width=10%:90%
	@caption Dush

		@property shower_sel type=checkbox ch_value=1 table=watercraft parent=shower_row no_caption=1
		caption Olemas

		@property shower_info type=textbox table=watercraft parent=shower_row no_caption=1
		@caption Info

			'lifejacket' => array( 'caption' => t('P&auml;&auml;stevest'), 'amount' => t('tk') ),
	@layout lifejacket_row type=hbox width=10%:45%:45%
	@caption P&auml;&auml;stevest

		@property lifejacket_sel type=checkbox ch_value=1 table=watercraft parent=lifejacket_row no_caption=1
		caption Olemas

		@property lifejacket_info type=textbox table=watercraft parent=lifejacket_row no_caption=1
		@caption Info

		@property lifejacket_amount type=textbox table=watercraft parent=lifejacket_row
		@caption Kogus (tk)

			'swimming_ladder' => array( 'caption' => t('Ujumisredel'), 'amount' => null ),
	@layout swimming_ladder_row type=hbox width=10%:90%
	@caption Ujumisredel

		@property swimming_ladder_sel type=checkbox ch_value=1 table=watercraft parent=swimming_ladder_row no_caption=1
		caption Olemas

		@property swimming_ladder_info type=textbox table=watercraft parent=swimming_ladder_row no_caption=1
		@caption Info

			'awning' => array( 'caption' => t('Varikatus'), 'amount' => null ),
	@layout awning_row type=hbox width=10%:90%
	@caption Varikatus

		@property awning_sel type=checkbox ch_value=1 table=watercraft parent=awning_row no_caption=1
		caption Olemas

		@property awning_info type=textbox table=watercraft parent=awning_row no_caption=1
		@caption Info

			'kitchen_cooker' => array( 'caption' => t('K&ouml;&ouml;k/Pliit'), 'amount' => null ),
	@layout kitchen_cooker_row type=hbox width=10%:90%
	@caption K&ouml;&ouml;k/Pliit

		@property kitchen_cooker_sel type=checkbox ch_value=1 table=watercraft parent=kitchen_cooker_row no_caption=1
		caption Olemas

		@property kitchen_cooker_info type=textbox table=watercraft parent=kitchen_cooker_row no_caption=1
		@caption Info

			'vendrid' => array( 'caption' => t('Vendrid'), 'amount' => t('tk') ),
	@layout vendrid_row type=hbox width=10%:45%:45%
	@caption Vendrid

		@property vendrid_sel type=checkbox ch_value=1 table=watercraft parent=vendrid_row no_caption=1
		caption Olemas

		@property vendrid_info type=textbox table=watercraft parent=vendrid_row no_caption=1
		@caption Info

		@property vendrid_amount type=textbox table=watercraft parent=vendrid_row
		@caption Kogus (tk)

			'fridge' => array( 'caption' => t('K&uuml;lmkapp'), 'amount' => null ),
	@layout fridge_row type=hbox width=10%:90%
	@caption K&uuml;lmkapp

		@property fridge_sel type=checkbox ch_value=1 table=watercraft parent=fridge_row no_caption=1
		caption Olemas

		@property fridge_info type=textbox table=watercraft parent=fridge_row no_caption=1
		@caption Info

			'anchor' => array( 'caption' => t('Ankur'), 'amount' => null ),
	@layout anchor_row type=hbox width=10%:90%
	@caption Ankur

		@property anchor_sel type=checkbox ch_value=1 table=watercraft parent=anchor_row no_caption=1
		caption Olemas

		@property anchor_info type=textbox table=watercraft parent=anchor_row no_caption=1
		@caption Info

			'oars' => array( 'caption' => t('Aerud'), 'amount' => t('tk') ),
	@layout oars_row type=hbox width=10%:45%:45%
	@caption Aerud

		@property oars_sel type=checkbox ch_value=1 table=watercraft parent=oars_row no_caption=1
		caption Olemas

		@property oars_info type=textbox table=watercraft parent=oars_row no_caption=1
		@caption Info

		@property oars_amount type=textbox table=watercraft parent=oars_row
		@caption Kogus (tk)

			'tv_video' => array( 'caption' => t('TV-video'), 'amount' => null ),
	@layout tv_video_row type=hbox width=10%:90%
	@caption TV-video

		@property tv_video_sel type=checkbox ch_value=1 table=watercraft parent=tv_video_row no_caption=1
		caption Olemas

		@property tv_video_info type=textbox table=watercraft parent=tv_video_row no_caption=1
		@caption Info

			'fuel' => array( 'caption' => t('K&uuml;te'), 'amount' => null ),
	@layout fuel_row type=hbox width=10%:90%
	@caption K&uuml;te

		@property fuel_sel type=checkbox ch_value=1 table=watercraft parent=fuel_row no_caption=1
		caption Olemas

		@property fuel_info type=textbox table=watercraft parent=fuel_row no_caption=1
		@caption Info

			'water_tank' => array( 'caption' => t('Veepaak'), 'amount' => t('liitrit') ),
	@layout water_tank_row type=hbox width=10%:45%:45%
	@caption Veepaak

		@property water_tank_sel type=checkbox ch_value=1 table=watercraft parent=water_tank_row no_caption=1
		caption Olemas

		@property water_tank_info type=textbox table=watercraft parent=water_tank_row no_caption=1
		@caption Info

		@property water_tank_amount type=textbox table=watercraft parent=water_tank_row
		@caption Maht (l)

			'life_boat' => array( 'caption' => t('P&auml;&auml;stepaat'), 'amount' => null),
	@layout life_boat_row type=hbox width=10%:45%:45%
	@caption P&auml;&auml;stepaat

		@property life_boat_sel type=checkbox ch_value=1 table=watercraft parent=life_boat_row no_caption=1
		caption Olemas

		@property life_boat_info type=textbox table=watercraft parent=life_boat_row no_caption=1
		@caption Info

		@property life_boat_amount type=textbox table=watercraft parent=life_boat_row
		@caption Kogus (tk)


	@property additional_equipment_info rows=10 type=textarea table=watercraft
	@caption T&auml;iendav info

@reltype LOCATION value=1 clid=CL_CRM_CITY
@caption Asukoht

@reltype SELLER value=2 clid=CL_CRM_PERSON,CL_CRM_COMPANY
@caption M&uuml;&uuml;ja

*/

define('DEAL_TYPE_SALE', 1);
define('DEAL_TYPE_LEASE', 2);
define('DEAL_TYPE_BUY', 3);

define('WATERCRAFT_TYPE_MOTOR_BOAT', 1);
define('WATERCRAFT_TYPE_SAILING_SHIP', 2);
define('WATERCRAFT_TYPE_DINGHY', 3);
define('WATERCRAFT_TYPE_ROWING_BOAT', 4);
define('WATERCRAFT_TYPE_SCOOTER', 5);
define('WATERCRAFT_TYPE_SAILBOARD', 6);
define('WATERCRAFT_TYPE_CANOE', 7); // deprecated
define('WATERCRAFT_TYPE_FISHING_BOAT', 8); // deprecated
define('WATERCRAFT_TYPE_OTHER', 9);
define('WATERCRAFT_TYPE_ACCESSORIES', 10);

define('ACCESSORIES_ACCESSORY', 1);
define('ACCESSORIES_ENGINE', 2);
define('ACCESSORIES_SAIL', 3);
define('ACCESSORIES_MAST', 4);

define('BODY_MATERIAL_WOOD', 1);
define('BODY_MATERIAL_STEEL', 2);
define('BODY_MATERIAL_ALUMINUM', 3);
define('BODY_MATERIAL_PLASTIC', 4);
define('BODY_MATERIAL_FIBERGLASS', 5);
define('BODY_MATERIAL_OTHER', 6);

define('CONDITION_NEW', 1);
define('CONDITION_GOOD', 2);
define('CONDITION_LITTLE_USED', 3);
define('CONDITION_USED', 4);
define('CONDITION_NEEDS_REPAIR', 5);

define('CENTREBOARD_1','');
define('CENTREBOARD_2','');

define('ENGINE_TYPE_2_TACT', 1);
define('ENGINE_TYPE_4_TACT', 2);

define('FUEL_PETROL', 1);
define('FUEL_DIESEL', 2);

define('ENGINE_COOLING_SEA_WATER', 1);
define('ENGINE_COOLING_FRESH_WATER', 2);

define('MAST_MATERIAL_WOOD', 1);
define('MAST_MATERIAL_ALUMINIUM', 2);
define('MAST_MATERIAL_PLASTIC', 3);
define('MAST_MATERIAL_OTHER', 4);

class watercraft extends class_base
{
	var $deal_type;
	var $watercraft_type;
	var $accessories;
	var $body_material;
	var $condition;
	var $centreboard;
	var $engine_type;
	var $fuel;
	var $engine_cooling;
	var $mast_material;

	function watercraft()
	{
		$this->init(array(
			"tpldir" => "applications/watercraft_management/watercraft",
			"clid" => CL_WATERCRAFT
		));

		$this->deal_type = array(
			DEAL_TYPE_SALE => t('M&uuml;&uuml;k'),
			DEAL_TYPE_LEASE => t('Rent'),
			DEAL_TYPE_BUY => t('Ost')
		);

		$this->watercraft_type = array(
			WATERCRAFT_TYPE_MOTOR_BOAT => t('Kaater'),
			WATERCRAFT_TYPE_SAILING_SHIP => t('Jaht'),
			WATERCRAFT_TYPE_DINGHY => t('Kummipaat'),
			WATERCRAFT_TYPE_ROWING_BOAT => t('Aerupaat'),
			WATERCRAFT_TYPE_SCOOTER => t('Skuuter'),
			WATERCRAFT_TYPE_SAILBOARD => t('Purjelaud'),
			WATERCRAFT_TYPE_OTHER => t('Muu alus'),
			WATERCRAFT_TYPE_ACCESSORIES => t('Varustus')
		);

		$this->accessories = array(
			ACCESSORIES_ACCESSORY => t('Lisavarustus'),
			ACCESSORIES_ENGINE => t('Mootor'),
			ACCESSORIES_SAIL => t('Purjed'),
			ACCESSORIES_MAST => t('Mast')
		);

		$this->body_material = array(
			BODY_MATERIAL_WOOD => t('Puit'),
			BODY_MATERIAL_STEEL => t('Teras'),
			BODY_MATERIAL_ALUMINUM => t('Alumiinium'),
			BODY_MATERIAL_PLASTIC => t('Plastik'),
			BODY_MATERIAL_FIBERGLASS => t('Klaaskiud'),
			BODY_MATERIAL_OTHER => t('Muu')
		);

		$this->condition = array(
			CONDITION_NEW => t('Uus'),
			CONDITION_GOOD => t('Heas korras'),
			CONDITION_LITTLE_USED => t('V&auml;he kasutatud'),
			CONDITION_USED => t('Kasutatud'),
			CONDITION_NEEDS_REPAIR => t('Vajab remonti')
		);

		$this->centreboard = array(
			CENTREBOARD_1 => t('Kiil'),
			CENTREBOARD_2 => t('Svert'),
		);

		$this->engine_type = array(
			ENGINE_TYPE_2_TACT => t('2-taktiline'),
			ENGINE_TYPE_4_TACT => t('4-taktiline')
		);

		$this->fuel = array(
			FUEL_PETROL => t('Bensiin'),
			FUEL_DIESEL => t('Diisel')
		);

		$this->engine_cooling = arraY(
			ENGINE_COOLING_SEA_WATER => t('Merevesi'),
			ENGINE_COOLING_FRESH_WATER => t('Magevesi')
		);

		$this->mast_material = array(
			MAST_MATERIAL_WOOD => t('Puit'),
			MAST_MATERIAL_ALUMINIUM => t('Alumiinium'),
			MAST_MATERIAL_PLASTIC => t('Plastik'),
			MAST_MATERIAL_OTHER => t('Muu materjal'),
		);

		$this->sail_types = array(
			'groot' => t('Groot/suurpuri'),
			'foka_1' => t('Foka&nbsp;1'),
			'foka_2' => t('Foka&nbsp;2'),
			'foka_3' => t('Foka&nbsp;3'),
			'genoa_1' => t('Genoa&nbsp;1'),
			'genoa_2' => t('Genoa&nbsp;2'),
			'genoa_3' => t('Genoa&nbsp;3'),
			'spinnaker' => t('Spinnaker'),
			'gennaker' => t('Gennaker'),
			'stormfoka' => t('Tormifoka'),
		);
		$this->sail_table_fields = array(
			'type' => t('Purje t&uuml;&uuml;p'),
			'area' => t('Pindala'),
			'material' => t('Purje materjal'),
			'age_and_condition' => t('Purje vanus ja seisukord'),
		);
	}


	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case 'deal_type':
				$prop['options'] = $this->deal_type;
				break;
			case 'watercraft_type':
				$prop['options'] = $this->watercraft_type;
				break;
			case 'watercraft_type_other':
				if ($arr['obj_inst']->prop('watercraft_type') != WATERCRAFT_TYPE_OTHER)
				{
					$retval = PROP_IGNORE;
				}
				break;
			case 'watercraft_accessories':
				if ($arr['obj_inst']->prop('watercraft_type') != WATERCRAFT_TYPE_ACCESSORIES)
				{
					$retval = PROP_IGNORE;
				}
				else
				{
					$prop['options'] = $this->accessories;
				}
				break;
			case 'manufacturer':
				$management = get_active(CL_WATERCRAFT_MANAGEMENT);
				$prop['options'][] = t('--Vali--');
				if ( $management !== false )
				{
					$manufacturers = new object_list(array(
						'class_id' => CL_CRM_COMPANY,
						'parent' => $management->prop('manufacturers')
					));
					foreach ($manufacturers->arr() as $id => $manufacturer)
					{
						$prop['options'][$id] = $manufacturer->name();
					}
				}
				break;
			case 'location':
				$management = get_active(CL_WATERCRAFT_MANAGEMENT);
				$prop['options'][] = t('--Vali--');
				if ( $management !== false )
				{
					$locations = new object_list(array(
						'class_id' => CL_CRM_ADDRESS,
						'parent' => $management->prop('locations')
					));
					foreach ($locations->arr() as $id => $location)
					{
						$prop['options'][$id] = $location->name();
					}
				}
				// xxx i don't know if this is necessary here, so i will comment out it now --dragut
				//$prop['options'][-1] = t('Muu asukoht');
				break;
			case 'location_other':
				if ($arr['obj_inst']->prop('location') > 0)
				{
					$retval = PROP_IGNORE;
				}
				break;
			case 'body_material':
				$prop['options'] = $this->body_material;
				break;
			case 'body_material_other':
				if ($arr['obj_inst']->prop('body_material') != BODY_MATERIAL_OTHER)
				{
					$retval = PROP_IGNORE;
				}
				break;
			case 'condition':
				$prop['options'] = $this->condition;
				break;
			case 'centreboard':
				if ($arr['obj_inst']->prop('watercraft_type') != WATERCRAFT_TYPE_SAILING_SHIP)
				{
					$retval = PROP_IGNORE;
				}
				else
				{
					$prop['options'] = $this->centreboard;
				}
				break;
			case 'creation_year':
				$prop['options'] = $this->custom_range(1900, date('Y'));
				if ( empty($prop['value']) )
				{
					$prop['selected'] = '2000';
				}
				break;
			case 'passanger_count':
				$prop['options'] = $this->custom_range(1, 50);
				break;
			case 'sleeper_count':
				$prop['options'] = $this->custom_range(0, 20);
				break;
			case 'engine_count':
				$prop['options'] = $this->custom_range(0, 4);
				break;
			case 'engine_type':
				$prop['options'] = $this->engine_type;
				break;
			case 'fuel':
				$prop['options'] = $this->fuel;
				break;
			case 'engine_cooling':
				$prop['options'] = $this->engine_cooling;
				break;
			case 'mast_material':
				$prop['options'] = $this->mast_material;
				break;
			case 'mast_material_other':
				if ($arr['obj_inst']->prop('mast_material') != MAST_MATERIAL_OTHER)
				{
					$retval = PROP_IGNORE;
				}
				break;
			case 'mast_count':
				$prop['options'] = $this->custom_range(1, 4);
				break;
		};
		return $retval;
	}

	function _get_images_toolbar($arr)
	{
		$t = $arr['prop']['vcl_inst'];

		$t->add_button(array(
			'name' => 'new',
			'img' => 'new.gif',
			'tooltip' => t('Uus Pilt'),
			'url' => $this->mk_my_orb('new', array(
				'parent' => $arr['obj_inst']->id(),
				'return_url' => get_ru()
			), CL_IMAGE),
		));

		$t->add_button(array(
			'name' => 'delete',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta'),
			'action' => '_delete_objects',
			'confirm' => t('Oled kindel et soovid valitud objektid kustutada?')
		));

		return PROP_OK;
	}

	function _get_images_table($arr)
	{
		$t = $arr['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->define_field(array(
			'name' => 'name',
			'caption' => t('Nimi')
		));
		$t->define_field(array(
			'name' => 'select',
			'caption' => t('Vali'),
			'width' => '5%',
			'align' => 'center'
		));

		$images = new object_list(array(
			'class_id' => CL_IMAGE,
			'parent' => $arr['obj_inst']->id()
		));

		foreach ($images->arr() as $id => $image)
		{
			$t->define_data(array(
				'name' => html::href(array(
					'url' => $this->mk_my_orb('change', array(
						'id' => $image->id()
					), CL_IMAGE),
					'caption' => $image->name()
				)),
				'select' => html::checkbox(array(
					'name' => 'selected_ids['.$id.']',
					'value' => $id
				))
			));
		}
		return PROP_OK;
	}

	function _get_sail_table($arr)
	{
		$t = $arr['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->define_field(array(
			'name' => 'type',
			'caption' => $this->sail_table_fields['type'],
			'talign' => 'left',
			'width' => '20px',
		));
		$t->define_field(array(
			'name' => 'area',
			'caption' => $this->sail_table_fields['area']." m<sup>2</sup>",
			'talign' => 'left',
			'width' => '20px',
		));
		$t->define_field(array(
			'name' => 'material',
			'talign' => 'left',
			'caption' => $this->sail_table_fields['material'],
			'width' => '20px',
		));
		$t->define_field(array(
			'name' => 'age_and_condition',
			'caption' => $this->sail_table_fields['age_and_condition'],
			'talign' => 'left',
			'width' => '20px',
		));

		$rows = $this->sail_types;


		$saved_sail_table = $arr['obj_inst']->meta('sail_table');
		// these style = some amount of pixels, are here for marine24 webview.. it didn't fit any other way, and i didn't have any time to do nicer
		foreach ( $rows as $key => $value )
		{
			$t->define_data(array(
				'type' => $value,
				'area' => html::textbox(array(
					'name' => 'sail_table['.$key.'][area]',
					'value' => $saved_sail_table[$key]['area'],
					'style' => 'width:50px;',
				)),
				'material' => html::textbox(array(
					'name' => 'sail_table['.$key.'][material]',
					'value' => $saved_sail_table[$key]['material'],
					'style' => 'width:100px;',
				)),
				'age_and_condition' => html::textbox(array(
					'name' => 'sail_table['.$key.'][age_and_condition]',
					'value' => $saved_sail_table[$key]['age_and_condition'],
					'style' => 'width:100px;',
				)),

			));
		}

		// custom sail types:
		$t->define_data(array(
			'type' => t('Muu&nbsp;puri'),
			'area' => '',
			'material' => '',
			'age_and_condition' => ''
		));

		$t->define_data(array(
			'type' => html::textbox(array(
				'name' => 'sail_table[other_sail][type]',
				'size' => 20,
				'style' => 'width:100px;',
				'value' => $saved_sail_table['other_sail']['type']
			)),
			'area' => html::textbox(array(
				'name' => 'sail_table[other_sail][area]',
				'size' => 20,
				'style' => 'width:50px;',
				'value' => $saved_sail_table['other_sail']['area']
			)),
			'material' => html::textbox(array(
				'name' => 'sail_table[other_sail][material]',
				'size' => 20,
				'style' => 'width:100px;',
				'value' => $saved_sail_table['other_sail']['material']
			)),
			'age_and_condition' => html::textbox(array(
				'name' => 'sail_table[other_sail][age_and_condition]',
				'size' => 20,
				'style' => 'width:100px;',
				'value' => $saved_sail_table['other_sail']['age_and_condition']
			)),
		));
		return PROP_OK;
	}

	function _set_sail_table($arr)
	{
		$arr['obj_inst']->set_meta('sail_table', $arr['request']['sail_table']);
		return PROP_OK;
	}

	function _get_additional_equipment_table($arr)
	{
		$t = $arr['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->define_field(array(
			'name' => 'check',
			'caption' => t('Olemas'),
			'align' => 'center',
			'width' => '5%'
		));
		$t->define_field(array(
			'name' => 'name',
			'caption' => t('Nimetus')
		));
		$t->define_field(array(
			'name' => 'info',
			'caption' => t('Lisainfo')
		));
		$t->define_field(array(
			'name' => 'amount',
			'caption' => t('Kogus')
		));

		$rows = array(
			'electricity_110V' => array( 'caption' => t('Elekter 110V'), 'amount' => null ),
			'electricity_220V' => array( 'caption' => t('Elekter 220V'), 'amount' => null ),
			'radio_station' => array( 'caption' => t('Raadiojaam'), 'amount' => null),
			'stereo' => array( 'caption' => t('Stereo'), 'amount' => null ),
			'cd' => array( 'caption' => t('CD'), 'amount' => null ),
			'waterproof_speakers' => array( 'caption' => t('Veekindlad k&otilde;larid'), 'amount' => null ),
			'burglar_alarm' => array( 'caption' => t('Signalisatsioon'), 'amount' => null ),
			'navigation_system' => array( 'caption' => t('Navigatsioonis&uuml;steem'), 'amount' => null ),
			'navigation_lights' => array( 'caption' => t('Navigatsioonituled'), 'amount' => null ),
			'trailer' => array( 'caption' => t('Treiler'), 'amount' => null ),
			'toilet' => array( 'caption' => t('Tualett'), 'amount' => null ),
			's(hower' => array( 'caption' => t('Dush'), 'amount' => null ),
			'lifejacket' => array( 'caption' => t('P&auml;&auml;stevest'), 'amount' => t('tk') ),
			'swimming_ladder' => array( 'caption' => t('Ujumisredel'), 'amount' => null ),
			'awning' => array( 'caption' => t('Varikatus'), 'amount' => null ),
			'kitchen_cooker' => array( 'caption' => t('K&ouml;&ouml;k/Pliit'), 'amount' => null ),
			'vendrid' => array( 'caption' => t('Vendrid'), 'amount' => t('tk') ),
			'fridge' => array( 'caption' => t('K&uuml;lmkapp'), 'amount' => null ),
			'anchor' => array( 'caption' => t('Ankur'), 'amount' => null ),
			'oars' => array( 'caption' => t('Aerud'), 'amount' => t('tk') ),
			'tv_video' => array( 'caption' => t('TV-video'), 'amount' => null ),
			'fuel' => array( 'caption' => t('K&uuml;te'), 'amount' => null ),
			'water_tank' => array( 'caption' => t('Veepaak'), 'amount' => t('liitrit') ),
			'life_boat' => array( 'caption' => t('P&auml;&auml;stepaat'), 'amount' => null),
		);

		$saved_additional_equipment = $arr['obj_inst']->meta('additional_equipment_table');
		foreach ($rows as $key => $value)
		{
			$amount_str = "";
			if ($value['amount'] !== null)
			{
				$amount_str = html::textbox(array(
					'name' => 'additional_equipment['.$key.'][amount]',
					'value' => $saved_additional_equipment[$key]['amount']
				));
				$amount_str .= $value['amount'];
			}

			$t->define_data(array(
				'check' => html::checkbox(array(
					'name' => 'additional_equipment['.$key.'][check]',
					'value' => 1,
					'checked' => ($saved_additional_equipment[$key]['check'] == 1) ? true : false
				)),
				'name' => $value['caption'],
				'info' => html::textbox(array(
					'name' => 'additional_equipment['.$key.'][info]',
					'value' => $saved_additional_equipment[$key]['info']

				)),
				'amount' => $amount_str
			));
		}
		return PROP_OK;

	}

	function _set_additional_equipment_table($arr)
	{
		$arr['obj_inst']->set_meta('additional_equipment_table', $arr['request']['additional_equipment']);
		return PROP_OK;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function callback_mod_tab($arr)
	{
		$watercraft_type = $arr['obj_inst']->prop('watercraft_type');

		$no_engine = array(
			WATERCRAFT_TYPE_CANOE,
			WATERCRAFT_TYPE_SAILBOARD
		);
		if ( $arr['id'] === 'engines' && in_array($watercraft_type, $no_engine) )
		{
			return false;
		}

		$no_mast = array(
			WATERCRAFT_TYPE_MOTOR_BOAT,
			WATERCRAFT_TYPE_DINGHY,
			WATERCRAFT_TYPE_ROWING_BOAT,
			WATERCRAFT_TYPE_SCOOTER,
			WATERCRAFT_TYPE_CANOE,
		);
		if ( ( $arr['id'] === 'mast' || $arr['id'] === 'sail' ) && in_array($watercraft_type, $no_mast) )
		{
			return false;
		}
		return true;
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$objs = array("manufacturer", "location");
		$templates = array(
			WATERCRAFT_TYPE_MOTOR_BOAT => 'motor_boat.tpl',
			WATERCRAFT_TYPE_SAILING_SHIP => 'sailing_ship.tpl',
			WATERCRAFT_TYPE_DINGHY => 'dinghy.tpl',
			WATERCRAFT_TYPE_ROWING_BOAT => 'rowing_boat.tpl',
			WATERCRAFT_TYPE_SCOOTER => 'scooter.tpl',
			WATERCRAFT_TYPE_SAILBOARD => 'sailboard.tpl',
			WATERCRAFT_TYPE_CANOE => 'canoe.tpl',
			WATERCRAFT_TYPE_FISHING_BOAT => 'fishing_boat.tpl',
			WATERCRAFT_TYPE_OTHER => 'other.tpl',
			WATERCRAFT_TYPE_ACCESSORIES => 'accessories.tpl',
		);

		// read template:

		$watercraft_type = $ob->prop('watercraft_type');
		if ($watercraft_type)
		{
			$this->read_template($templates[$watercraft_type]);
		}
		else
		{
			$this->read_template("show.tpl");
		}

		$vars = array();

		// properties
		$props = $ob->properties();

		// location
		$loc = array();
		if($this->can("view", $props["location"]))
		{
			$o = obj($props["location"]);
			$loc[] = $o->name();
		}

		if($props["location_precise"])
		{
			$loc[] = $props["location_precise"];
		}
		$this->vars(array(
			"watercraft_final_location" => count($loc)?join(", ", $loc):t("-"),
		));

		//  manufacturer
		$man = array();
		if($this->can("view", $props["manufacturer"]))
		{
			$o = obj($props["manufacturer"]);
			$man[] = $o->name();
		}

		if($props["manufacturer_other"])
		{
			$man[] = $props["manufacturer_other"];
		}
		$this->vars(array(
			"watercraft_manufacturer_together" => count($man)?join(" ", $man):t("-"),
		));

		$proplist = $ob->get_property_list();
		foreach ($props as $prop_name => $prop_value)
		{
			if($prop_name == "sail_table")
			{
				// parse table header
				foreach($this->sail_table_fields as $caption)
				{
					$this->vars(array(
						"caption" => "<b>".$caption."</b>",
					));
					$th .= $this->parse("SAIL_TABLE_TH");
				}
				$this->vars(array(
					"SAIL_TABLE_TH" => $th,
				));
				$header_row = $this->parse("SAIL_TABLE_HTR");

				// parse sails
				$prop_value = $ob->meta($prop_name);
				// we loop over sail types
				$has_rows = false;
				foreach($this->sail_types + array("other_sail" => $prop_value["other_sail"]["type"]) as $var => $caption)
				{
					unset($row);
					// a little exception for type column, and then let's parse values
					$prop_value[$var]['type'] = $this->sail_types[$var]?$this->sail_types[$var]:$prop_value[$var]["type"];
					$val_exists = false;
					foreach($this->sail_table_fields as $field_var => $field_caption)
					{

						$value = $prop_value[$var][$field_var]?$prop_value[$var][$field_var]:"-";
						$value .= ($field_var == 'area')?" m<sup>2</sup>":"";
						$value = ($field_var == "type")?"<b>".$value."</b>":$value;
						$this->vars(array(
							"caption" => $value,
						));
						$val_exists = ($prop_value[$var][$field_var] && $field_var != "type")?true:$val_exists;
						$row .= $this->parse("SAIL_TABLE_TD");
					}
					if(!$val_exists)
					{
						continue;
					}
					$has_rows = true;
					$this->vars(array(
						"SAIL_TABLE_TD" => $row,
					));
					$sail_rows .= $this->parse("SAIL_TABLE_TR");
				}
				$this->vars(array(
					"SAIL_TABLE_TR" => $sail_rows,
					"SAIL_TABLE_HTR" => $header_row,
				));
				$vars["WATERCRAFT_SAIL_TABLE"] = $has_rows?$this->parse("WATERCRAFT_SAIL_TABLE"):"";
			}
			elseif (is_array($this->$prop_name))
			{
				$prop_data = $this->$prop_name;
				$vars['watercraft_'.$prop_name] = ($prop_data[$prop_value])?$prop_data[$prop_value]:"-";
			}
			else
			{
				if($proplist[$prop_name]["group"] == "additional_equipment")
				{
					if(substr($prop_name, -4) == "_sel" && $prop_value)
					{
						$prp = substr($prop_name, 0, -4);
						$add_equip[$prp]["sel"] = $prop_value;
					}
					else if(substr($prop_name, -5) == "_info" && strlen($prop_value))
					{
						$prp = substr($prop_name, 0, -5);
						$add_equip[$prp]["info"] = $prop_value;
					}
					else if(substr($prop_name, -7) == "_amount" && strlen($prop_value))
					{
						$prp = substr($prop_name, 0, -7);
						$add_equip[$prp]["amount"] = $prop_value;
					}
				}
				// this hack sucks bigtime .. doesn't it?:)
				if(in_array($prop_name, $objs) && $this->can("view", $prop_value))
				{
					$prop_obj = obj($prop_value);
					$prop_value = $prop_obj->name();
				}
				$vars['watercraft_'.$prop_name] = htmlentities($prop_value?$prop_value:"-");
			}
		}
		$cf = get_instance("cfg/cfgutils");
		$cf->load_properties(array(
			"clid" => CL_WATERCRAFT,
		));
		$li = $cf->get_layoutinfo();
		foreach($add_equip as $prp => $vals)
		{
			$_t = ($vals["amount"]?sprintf("(%s tk)", $vals["amount"]):"").($vals["info"]?sprintf("- %s",$vals["info"]):"");
			if($vals["sel"])
			{
				$joins[] = $li[$prp."_row"]["caption"]." ".$_t;
			}
		}
		$vars["watercraft_comma_separated_additional_equipment"] = ($_t = join(", ", $joins))?$_t:"-";

		// images
		$image_inst = get_instance(CL_IMAGE);
		$images = new object_list(array(
			'class_id' => CL_IMAGE,
			'parent' => $ob->id()
		));
		$images_str = '';
		$first_image_str = '';
		$images_count = 0;
		$first_image = true;
		foreach ($images->arr() as $image_id => $image)
		{
			$image_data = $image_inst->get_image_by_id($image_id);
			$image_url = $image_inst->get_url_by_id($image_id);

			$fl = $image->prop("file");
			if(!empty($fl))
			{
				// rewrite $fl to be correct if site moved
				$fl = basename($fl);
				$fl = aw_ini_get("site_basedir")."files/".$fl{0}."/".$fl;
				$sz = getimagesize($fl);
				$sm_w = $sz[0];
				$sm_h = $sz[1];
			}

			$fl = $image->prop("file2");
			if(!empty($fl))
			{
				// rewrite $fl to be correct if site moved
				$fl = basename($fl);
				$fl = aw_ini_get("site_basedir")."files/".$fl{0}."/".$fl;
				$sz = getimagesize($fl);
				$bg_w = $sz[0];
				$bg_h = $sz[1];
			}

			$this->vars(array(
				'watercraft_image_url' => $image_data["url"],
				//'watercraft_big_image_url' => ($image_data["big_url"])?$image_data["big_url"]:$image_data["url"],
				'watercraft_big_image_url' => $this->mk_my_orb("show_big", array( "id" => $image_id), CL_IMAGE),
				'watercraft_image_name' => $image_data['name'],
				'watercraft_image_tag' => $image_inst->make_img_tag_wl($image_id),
				'watercraft_image_width' => $sm_w,
				'watercraft_image_height' => $sm_h,
				'watercraft_big_image_width' => $image_data["big_url"]?$bg_w:$sm_w,
				'watercraft_big_image_height' => $image_data["big_url"]?$bg_h:$sm_h,
			));
			if ($first_image)
			{
				$first_image_str = $this->parse('WATERCRAFT_FIRST_IMAGE');
				$first_image = false;
			}
			else
			{
				$images_str .= $this->parse('WATERCRAFT_IMAGE');
			}
			$images_count++;
		}

		if(empty($first_image_str))
		{
			$first_image_str = $this->parse('WATERCRAFT_NO_FIRST_IMAGE');
		}
		if (empty($images_str))
		{
			$images_str = $this->parse('WATERCRAFT_NO_IMAGE');
		}

		$vars['WATERCRAFT_IMAGE'] = $images_str;
		$vars['WATERCRAFT_FIRST_IMAGE'] = $first_image_str;
		$vars["watercraft_id"] = $ob->id();
		$vars['images_count'] = $images_count;
		$vars['name'] = $ob->prop('name');
		$vars['return_url'] = $_GET["return_url"]?$_GET["return_url"]:aw_url_change_var(array('watercraft_id' => NULL, 'return_url' => NULL), false, get_ru());

		$vars["seller_name"] = $ob->prop("contact_name");
		$vars["seller_email"] = $ob->prop("contact_email");
		$vars["seller_phone"] = $ob->prop("contact_phone");
		if((!$vars["seller_name"] || !$vars["seller_email"] || !$vars["seller_phone"]) && $this->can("view", $ob->prop("seller")))
		{
			$seller_obj = obj($ob->prop("seller"));
			$vars["seller_name"] = !$vars["seller_name"]?(($_t = $seller_obj->name())?$_t:""):$vars["seller_name"];
			if($seller_obj->class_id() == CL_CRM_PERSON)
			{
				$vars["seller_email"] = !$vars["seller_email"]?(($_t = $seller_obj->prop("email.mail"))?$_t:""):$vars["seller_email"];
				// well, this user1 prop isn't really the brightest idea. But hey, site_join does that, so i'll use it.
				$vars["seller_phone"] = !$vars["seller_phone"]?(($_t = $seller_obj->prop("phone.name"))?$_t:""):$vars["seller_phone"];
			}
			elseif($seller_obj->class_id() == CL_CRM_COMPANY)
			{
				$vars["seller_email"] = !$vars["seller_email"]?$seller_obj->prop("email.mail"):$vars["seller_email"];
				$vars["seller_phone"] = !$vars["seller_phone"]?$seller_obj->prop("phone.name"):$vars["seller_phone"];
			}
		}
		$vars["seller_name"] = $vars["seller_name"]?$vars["seller_name"]:"-";
		$vars["seller_email"] = $vars["seller_email"]?$vars["seller_email"]:"-";
		$vars["seller_phone"] = $vars["seller_phone"]?$vars["seller_phone"]:"-";

		$this->vars($vars);
		return $this->parse();
	}

	function request_execute($o)
	{
		return $this->show(array(
			'id' => $o->id(),
		));
	}

	function custom_range($start, $end)
	{
		$result = array();
		foreach (range($start, $end) as $value)
		{
			$result[$value] = $value;
		}
		return $result;
	}

	/**
		@attrib name=_delete_objects
	**/
	function _delete_objects($arr)
	{

		foreach ($arr['selected_ids'] as $id)
		{
			if (is_oid($id) && $this->can("delete", $id))
			{
				$object = new object($id);
				$object->delete(true);
			}
		}
		return $arr['post_ru'];
	}

	function do_db_upgrade($table, $field, $query, $error)
	{

		if ("watercraft" === $table and empty($field))
		{
			// db table doesn't exist, so lets create it:
			$this->db_query('CREATE TABLE `watercraft` (
				oid INT PRIMARY KEY NOT NULL,

				deal_type int,
				watercraft_type int,
				body_material int,
				location int,
				`condition` int,
				seller int,
				visible int,
				archived int,
				centreboard int,
				creation_year int,
				passanger_count int,
				sleeper_count int,
				engine_count int,
				engine_type int,
				fuel int,
				engine_cooling int,
				mast_material int,
				mast_count int,
				manufacturer int,
				electricity_110V_sel int,
				electricity_220V_sel int,
				radio_station_sel int,
				stereo_sel int,
				cd_sel int,
				waterproof_speakers_sel int,
				burglar_alarm_sel int,
				navigation_system_sel int,
				navigation_lights_sel int,
				trailer_sel int,
				toilet_sel int,
				shower_sel int,
				lifejacket_sel int,
				swimming_ladder_sel int,
				awning_sel int,
				kitchen_cooker_sel int,
				vendrid_sel int,
				fridge_sel int,
				anchor_sel int,
				oars_sel int,
				tv_video_sel int,
				fuel_sel int,
				water_tank_sel int,
				life_boat_sel int,

				`length` double,
				width double,
				height double,
				weight double,
				draught double,
				fuel_tank double,
				engine_power double,
				price int,

				location_precise varchar(255),
				contact_name varchar(255),
				contact_email varchar(255),
				contact_phone varchar(255),
				watercraft_type_other varchar(255),
				body_material_other varchar(255),
				location_other varchar(255),
				condition_info varchar(255),
				brand varchar(255),
				engine_manufacturer varchar(255),
				engine_model varchar(255),
				engine_capacity varchar(255),
				mast_material_other varchar(255),
				electricity_110V_info varchar(255),
				electricity_220V_info varchar(255),
				radio_station_info varchar(255),
				stereo_info varchar(255),
				cd_info varchar(255),
				waterproof_speakers_info varchar(255),
				burglar_alarm_info varchar(255),
				navigation_system_info varchar(255),
				navigation_lights_info varchar(255),
				trailer_info varchar(255),
				toilet_info varchar(255),
				shower_info varchar(255),
				lifejacket_info varchar(255),
				lifejacket_amount varchar(255),
				swimming_ladder_info varchar(255),
				awning_info varchar(255),
				kitchen_cooker_info varchar(255),
				vendrid_info varchar(255),
				vendrid_amount varchar(255),
				fridge_info varchar(255),
				anchor_info varchar(255),
				oars_info varchar(255),
				oars_amount varchar(255),
				tv_video_info varchar(255),
				fuel_info varchar(255),
				water_tank_info varchar(255),
				water_tank_amount varchar(255),
				life_boat_info varchar(255),
				life_boat_amount varchar(255),

				sail_info text,
				additional_equipment_info text
			)');
			return true;
		}

		switch ($field)
		{
			case 'deal_type':
			case 'watercraft_type':
			case 'body_material':
			case 'location':
			case 'condition':
			case 'seller':
			case 'visible':
			case 'archived':
			case 'centreboard':
			case 'creation_year':
			case 'passanger_count':
			case 'sleeper_count':
			case 'engine_count':
			case 'engine_type':
			case 'fuel':
			case 'engine_cooling':
			case 'mast_material':
			case 'mast_count':
			case 'manufacturer':
			case 'electricity_110V_sel':
			case 'electricity_220V_sel':
			case 'radio_station_sel':
			case 'stereo_sel':
			case 'cd_sel':
			case 'waterproof_speakers_sel':
			case 'burglar_alarm_sel':
			case 'navigation_system_sel':
			case 'navigation_lights_sel':
			case 'trailer_sel':
			case 'toilet_sel':
			case 'shower_sel':
			case 'lifejacket_sel':
			case 'swimming_ladder_sel':
			case 'awning_sel':
			case 'kitchen_cooker_sel':
			case 'vendrid_sel':
			case 'fridge_sel':
			case 'anchor_sel':
			case 'oars_sel':
			case 'tv_video_sel':
			case 'fuel_sel':
			case 'water_tank_sel':
			case 'price':
			case 'life_boat_sel':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'int'
				));
				return true;

			case 'length':
			case 'width':
			case 'height':
			case 'weight':
			case 'draught':
			case 'fuel_tank':
			case 'engine_power':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'double'
				));
				return true;

			case 'watercraft_type_other':
			case 'manufacturer_other':
			case 'body_material_other':
			case 'location_other':
			case 'condition_info':
			case 'brand':
			case 'engine_manufacturer':
			case 'engine_model':
			case 'engine_capacity':
			case 'mast_material_other':
			case 'electricity_110V_info':
			case 'electricity_220V_info':
			case 'radio_station_info':
			case 'stereo_info':
			case 'cd_info':
			case 'waterproof_speakers_info':
			case 'burglar_alarm_info':
			case 'navigation_system_info':
			case 'navigation_lights_info':
			case 'trailer_info':
			case 'toilet_info':
			case 'shower_info':
			case 'lifejacket_info':
			case 'lifejacket_amount':
			case 'swimming_ladder_info':
			case 'awning_info':
			case 'kitchen_cooker_info':
			case 'vendrid_info':
			case 'vendrid_amount':
			case 'fridge_info':
			case 'anchor_info':
			case 'oars_info':
			case 'oars_amount':
			case 'tv_video_info':
			case 'fuel_info':
			case 'water_tank_info':
			case 'water_tank_amount':
			case 'life_boat_info':
			case 'life_boat_amount':
			case 'location_precise':
			case 'contact_name':
			case 'contact_email':
			case 'contact_phone':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'varchar(255)'
				));
                                return true;
			case 'sail_info':
			case 'additional_equipment_info':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'text'
				));
                                return true;
		}

		return false;
	}
}
