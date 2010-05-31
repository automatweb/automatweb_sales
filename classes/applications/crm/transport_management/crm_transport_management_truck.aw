<?php

namespace automatweb;

// crm_transport_management_truck.aw - Veoauto
/*

@classinfo syslog_type=ST_CRM_TRANSPORT_MANAGEMENT_TRUCK relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=dragut

@tableinfo crm_transport_management_truck index=oid master_table=objects master_index=oid

@default table=objects
@default group=general

	@property nr type=textbox table=crm_transport_management_truck
	@caption Auto nr.

	@property car_type type=relpicker reltype=RELTYPE_CAR_TYPE table=crm_transport_management_truck
	@caption Mark

	@property model type=textbox table=crm_transport_management_truck
	@caption Mudel

	@property bodytype type=select table=crm_transport_management_truck
	@caption Keret&uuml;&uuml;p

	@property color type=textbox table=crm_transport_management_truck
	@caption V&auml;rvus

	@property year type=textbox table=crm_transport_management_truck
	@caption V&auml;ljalaskeaasta

	@property price type=textbox table=crm_transport_management_truck
	@caption Hind

	@property transmission type=textbox table=crm_transport_management_truck
	@caption K&auml;igukast

	@property driving_axle type=select table=crm_transport_management_truck
	@caption Vedav sild

	@property transit type=textbox table=crm_transport_management_truck
	@caption L&auml;bis&otilde;it

@groupinfo technical_data caption="Tehnilised andmed"
@default group=technical_data

	@property superstructure type=select table=crm_transport_management_truck
	@caption Pealisehitus

	@property length type=textbox table=crm_transport_management_truck
	@caption Pikkus

	@property width type=textbox table=crm_transport_management_truck
	@caption Laius

	@property height type=textbox table=crm_transport_management_truck
	@caption K&otilde;rgus

	@property deadweight type=textbox table=crm_transport_management_truck
	@caption Kandev&otilde;ime

	@property full_weight type=textbox table=crm_transport_management_truck
	@caption T&auml;ismass

	@property fuel_tank_capacity type=textbox table=crm_transport_management_truck
	@caption K&uuml;tusepaagi maht

	@property fuel_type type=textbox table=crm_transport_management_truck
	@caption K&uuml;tus

	@property fuel_consumption type=textbox table=crm_transport_management_truck
	@caption K&uuml;tusekulu

	@property engine_power type=textbox table=crm_transport_management_truck
	@caption Mootori v&otilde;imsus (hj)

@groupinfo accessory caption="Lisavarustus"
@default group=accessory

	@property refrigerator type=checkbox ch_value=1 table=crm_transport_management_truck
	@caption K&uuml;lmutusseade

	@property conditioner type=checkbox ch_value=1 table=crm_transport_management_truck
	@caption Konditsioneer

@groupinfo drivers caption="Juhid"
@default group=drivers

	@property drivers_table type=table
	@caption Juhtide tabel

@groupinfo costs caption="Kulud"
@default group=costs

	@property costs_table type=table no_caption=1
	@caption Kulude tabel

@reltype CAR_TYPE value=1 clid=CL_CRM_TRANSPORT_MANAGEMENT_CAR_TYPE
@caption Auto mark

*/

define('TRUCK_TYPE_SADDLE', 1);
define('TRUCK_TYPE_RIGID', 2);

define('BODYTYPE_PLATFORM', 1);
define('BODYTYPE_VAN', 2);
define('BODYTYPE_DUMPER', 3);
define('BODYTYPE_TARPAULIN', 4);
define('BODYTYPE_TANKER', 5);
define('BODYTYPE_CONTAINER_TRANSPORTER', 6);
define('BODYTYPE_OTHER', 7);

define('DRIVING_AXLE_FRONT', 1);
define('DRIVING_AXLE_BACK', 2);
define('DRIVING_AXLE_4_WHEELS', 3);

class crm_transport_management_truck extends class_base
{
	const AW_CLID = 1085;


	var $truck_types = array();
	var $bodytypes = array();
	var $driving_axle = array();

	function crm_transport_management_truck()
	{
		$this->init(array(
			"tpldir" => "applications/crm/transport_management/crm_transport_management_truck",
			"clid" => CL_CRM_TRANSPORT_MANAGEMENT_TRUCK
		));

		$this->truck_types = array(
			TRUCK_TYPE_SADLE => t('Sadul'),
			TRUCK_TYPE_RIGID => t('Veok')
		);

		$this->bodytypes = array(
			BODYTYPE_PLATFORM => t('Madel'),
			BODYTYPE_VAN => t('Furgoon'),
			BODYTYPE_DUMPER => t('Kallur'),
			BODYTYPE_TARPAULIN => t('Tent'),
			BODYTYPE_TANKER => t('Tsistern'),
			BODYTYPE_CONTAINER_TRANSPORTER => t('Konteinerveok'),
			BODYTYPE_OTHER => t('Muu')
		);

		$this->driving_axle = array(
			DRIVING_AXLE_FRONT => t('Esivedu'),
			DRIVING_AXLE_BACK => t('Tagavedu'),
			DRIVING_AXLE_4_WHEELS => t('Nelivedu')
		);

	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case 'bodytype':
				$prop['options'] = $this->truck_types;
				break;
			case 'driving_axle':
				$prop['options'] = $this->driving_axle;
				break;
			case 'superstructure':
				$prop['options'] = $this->bodytypes;
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

	function _get_drivers_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->define_field(array(
			'name' => 'name',
			'caption' => t('Nimi')
		));
	}

	function _get_costs_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->define_field(array(
			'name' => 'nr',
			'caption' => t('Nr'),
			'align' => 'center',
			'width' => '5%'
		));
		$t->define_field(array(
			'name' => 'date',
			'caption' => t('Kuup&auml;ev')
		));
		$t->define_field(array(
			'name' => 'name',
			'caption' => t('Nimetus')
		));
		$t->define_field(array(
			'name' => 'sum',
			'caption' => t('Summa')
		));

		$costs = $arr['obj_inst']->meta('costs');
		$counter = 0;
		$total_sum = 0;

		foreach (safe_array($costs) as $cost)
		{
			$t->define_data(array(
				'nr' => $counter,
				'date' => html::textbox(array(
					'name' => 'costs['.$counter.'][date]',
					'value' => $cost['date']
				)),
				'name' => html::textbox(array(
					'name' => 'costs['.$counter.'][name]',
					'value' => $cost['name']
				)),
				'sum' => html::textbox(array(
					'name' => 'costs['.$counter.'][sum]',
					'value' => $cost['sum']
				)),
			));
			$total_sum += (int)$cost['sum'];
			$counter++;
		}

		$t->define_data(array(
			'nr' => '---',
			'date' => '',
			'name' => '',
			'sum' => sprintf(t('Summa: %s'), $total_sum)
		));

		for ( $i = 0; $i < 10; $i++ )
		{
			$t->define_data(array(
				'nr' => $counter,
				'date' => html::textbox(array(
					'name' => 'costs['.$counter.'][date]'
				)),
				'name' => html::textbox(array(
					'name' => 'costs['.$counter.'][name]'
				)),
				'sum' => html::textbox(array(
					'name' => 'costs['.$counter.'][sum]'
				)),
			));
			$counter++;
		}

	}

	function _set_costs_table($arr)
	{
		$costs = $arr['request']['costs'];
		$valid_costs = array();
		foreach ($costs as $cost)
		{
			foreach ($cost as $value)
			{
				// if there is at least one field filled, then lets save the row:
				if (!empty($value))
				{
					$cost['sum'] = (int)$cost['sum'];
					$valid_costs[] = $cost;
					break;
				}
			}

		}


		$arr['obj_inst']->set_meta('costs', $valid_costs);
		$arr['obj_inst']->save();
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		if (empty($field))
		{
			$this->db_query('CREATE TABLE '.$table.' (oid INT PRIMARY KEY NOT NULL)');
			return true;
		}

		switch ($field)
		{
			case 'car_type':
			case 'bodytype':
			case 'driving_axle':
			case 'superstructure':
			case 'refrigerator':
			case 'conditioner':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'int'
				));
                                return true;
			case 'nr':
			case 'model':
			case 'color':
			case 'year':
			case 'price':
			case 'transmission':
			case 'transit':
			case 'length':
			case 'width':
			case 'height':
			case 'deadweight':
			case 'full_weight':
			case 'fuel_tank_capacity':
			case 'fuel_type':
			case 'fuel_consumption':
			case 'engine_power':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'varchar(255)'
				));
				return true;
                }

		return false;
	}
}
?>
