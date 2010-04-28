<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/crm/transport_management/crm_transport_management_carriage_order.aw,v 1.5 2007/12/06 14:33:24 kristo Exp $
// carriage_order.aw - Veotellimus 
/*

@classinfo syslog_type=ST_CARRIAGE_ORDER relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=dragut

@tableinfo crm_transport_management_carriage_order index=oid master_table=objects master_index=oid

@default table=objects
@default group=general

	@property date type=date_select table=crm_transport_management_carriage_order
	@caption Koostamise kuup&auml;ev

	@property location type=textbox table=crm_transport_management_carriage_order
	@caption Koostamise koht

	@property orderer type=relpicker reltype=RELTYPE_ORDERER table=crm_transport_management_carriage_order
	@caption Tellija

	@property dispatcher type=relpicker reltype=RELTYPE_DISPATCHER table=crm_transport_management_carriage_order
	@caption Ekspediitor

	@property deadline type=date_select table=crm_transport_management_carriage_order
	@caption T&auml;htaeg

	@property carriage_order_status type=select store=no 
	@caption Staatus

	@property sender type=relpicker reltype=RELTYPE_SENDER table=crm_transport_management_carriage_order
	@caption Saatja

	@property receiver type=relpicker reltype=RELTYPE_RECEIVER table=crm_transport_management_carriage_order
	@caption Saaja

	@layout unloading_location_frame type=hbox

		@property unloading_location type=relpicker reltype=RELTYPE_UNLOADING_LOCATION table=crm_transport_management_carriage_order parent=unloading_location_frame captionside=top
		@caption Mahalaadimiskoht

		@property unloading_note type=textarea table=crm_transport_management_carriage_order parent=unloading_location_frame captionside=top
		@caption M&auml;rkus

	@layout loading_location_frame type=hbox

		@property loading_location type=relpicker reltype=RELTYPE_LOADING_LOCATION table=crm_transport_management_carriage_order parent=loading_location_frame captionside=top
		@caption Pealelaadimiskoht

		@property loading_note type=textarea table=crm_transport_management_carriage_order parent=loading_location_frame captionside=top
		@caption M&auml;rkus

	@property added_documents type=textarea table=crm_transport_management_carriage_order
	@caption Lisatud dokumendid

	@property transporter type=relpicker reltype=RELTYPE_TRANSPORTER table=crm_transport_management_carriage_order
	@caption Vedaja

	@property next_transporter type=relpicker reltype=RELTYPE_NEXT_TRANSPORTER table=crm_transport_management_carriage_order
	@caption J&auml;rgmine vedaja

	@property transporter_note type=textarea table=crm_transport_management_carriage_order
	@caption Vedaja m&auml;rkused

	@property carriage type=relpicker reltype=RELTYPE_CARRIAGE table=crm_transport_management_carriage_order
	@caption Veo nr.

	@property truck type=text store=no
	@captio Auto nr.

	@property trailer type=text store=no
	@captio Haagise nr.

	@property driver type=text store=no
	@captio Juht

@groupinfo cargo_data caption="Veose andmed"
@default group=cargo_data

	@property marking type=textarea table=crm_transport_management_carriage_order
	@caption Markeering

	@property places_count type=textbox table=crm_transport_management_carriage_order
	@caption Kohtade arv

	@property packing_method type=textbox table=crm_transport_management_carriage_order
	@caption Pakkimisviis

	@property merchandise_name type=textbox table=crm_transport_management_carriage_order
	@caption Kauba nimetus

	@property cargo_class type=textbox table=crm_transport_management_carriage_order
	@caption Klass

	@property nr type=textbox table=crm_transport_management_carriage_order
	@caption Number

	@property cmr_char type=textbox table=crm_transport_management_carriage_order
	@caption T&auml;ht

	@property adr type=textbox table=crm_transport_management_carriage_order
	@caption ADR

	@property measure_unit type=textbox table=crm_transport_management_carriage_order
	@caption M&otilde;&otilde;t&uuml;hik

	@property gross_weight type=textbox table=crm_transport_management_carriage_order
	@caption Brutokaal, kg

	@property capacity type=textbox table=crm_transport_management_carriage_order
	@caption Mahtuvus, m<sup>3</sup>

	@property receiver_instructions type=textbox table=crm_transport_management_carriage_order
	@caption Saaja juhised

	@property sender_special_notes type=textbox table=crm_transport_management_carriage_order
	@caption Saatja erim&auml;rkused

@groupinfo payment_data caption="Makse andmed"
@default group=payment_data

	@property payment_table type=table
	@caption Makse tabel

	@property payment_condition type=chooser table=crm_transport_management_carriage_order
	@caption Maksetingimused

@reltype ORDERER value=1 clid=CL_CRM_COMPANY
@caption Tellija

@reltype SENDER value=2 clid=CL_CRM_COMPANY,CL_CRM_PERSON
@caption Saatja

@reltype RECEIVER value=3 clid=CL_CRM_COMPANY,CL_CRM_PERSON
@caption Saaja

@reltype RECEIVER value=4 clid=CL_CRM_ADDRESS
@caption Mahalaadimiskoht

@reltype UNLOADING_LOCATION value=5 clid=CL_CRM_ADDRESS
@caption Mahalaadimiskoht

@reltype LOADING_LOCATION value=6 clid=CL_CRM_ADDRESS
@caption Mahalaadimiskoht

@reltype TRANSPORTER value=7 clid=CL_CRM_COMPANY,CL_CRM_PERSON
@caption Vedaja

@reltype NEXT_TRANSPORTER value=8 clid=CL_CRM_COMPANY,CL_CRM_PERSON
@caption J&auml;rgmine vedaja

@reltype CARRIAGE value=9 clid=CL_CRM_TRANSPORT_MANAGEMENT_CARRIAGE
@caption Vedu
*/

define('CARRIAGE_ORDER_STATUS_NEW', 1);
define('CARRIAGE_ORDER_STATUS_PLANNED', 2);
define('CARRIAGE_ORDER_STATUS_ON_THE_ROAD', 3);
define('CARRIAGE_ORDER_STATUS_OVER_DEADLINE', 4);
define('CARRIAGE_ORDER_STATUS_CANCELED', 5);
define('CARRIAGE_ORDER_STATUS_COMPLETED', 6);
define('CARRIAGE_ORDER_STATUS_ARCHIVED', 7);

define('PAYMENT_CONDITION_NOFRANKO', 1);
define('PAYMENT_CONDITION_FRANKO', 2);

class crm_transport_management_carriage_order extends class_base
{
	const AW_CLID = 1082;


	var $carriage_order_status = array();
	var $payment_condition = array();

	function crm_transport_management_carriage_order()
	{
		$this->init(array(
			"tpldir" => "applications/crm/transport_management/crm_transport_management_carriage_order",
			"clid" => CL_CRM_TRANSPORT_MANAGEMENT_CARRIAGE_ORDER
		));

		$this->carriage_order_status = array(
			CARRIAGE_ORDER_STATUS_NEW => t('Uus'),
			CARRIAGE_ORDER_STATUS_PLANNED => t('Planeeritud'),
			CARRIAGE_ORDER_STATUS_ON_THE_ROAD => t('Hetkel vedamisel'),
			CARRIAGE_ORDER_STATUS_OVER_DEADLINE => t('&Uuml;le t&auml;htaja'),
			CARRIAGE_ORDER_STATUS_CANCELED => t('Katkestatud'),
			CARRIAGE_ORDER_STATUS_COMPLETED => t('Valmis'),
			CARRIAGE_ORDER_STATUS_ARCHIVED => t('Arhiveeritud')
		);

		$this->payment_condition = array(
			PAYMENT_CONDITION_FRANKO => t('FRANKO'),
			PAYMENT_CONDITION_NOFRANKO => t('NOFRANKO')
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case 'carriage_order_status':
				$prop['options'] = $this->carriage_order_status;
				break;
			case 'payment_condition':
				$prop['options'] = $this->payment_condition;
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

	function callback_mod_reforb($arr)
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

	function _get_payment_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);


		$t->define_field(array(
			'name' => 'for_payment',
			'caption' => t('Kuulub maksmisele')
		));
		$t->define_field(array(
			'name' => 'sender',
			'caption' => t('Saatja')
		));
		$t->define_field(array(
			'name' => 'currency',
			'caption' => t('Valuuta')
		));
		$t->define_field(array(
			'name' => 'receiver',
			'caption' => t('Kauba saaja')
		));

		$rows = array(
			'carriage_price' => t('Veohind'),
			'discount' => t('Allahindlus'),	
			'balance' => t('Saldo'),
			'extra_charge' => t('Juurdehindlus'),
			'others' => t('Teised')
		);

		foreach ($rows as $key => $value)
		{
			$t->define_data(array(
				'for_payment' => $value,
				'sender' => html::textbox(array(
					'name' => 'payment['.$key.'][sender]'
				)),
				'currency' => html::textbox(array(
					'name' => 'payment['.$key.'][currency]'
				)),
				'receiver' => html::textbox(array(
					'name' => 'payment['.$key.'][receiver]'
				)),
			));
		}
		return PROP_OK;
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
			case 'date':
			case 'orderer':
			case 'dispatcher':
			case 'deadline':
			case 'sender':
			case 'receiver':
			case 'unloading_location':
			case 'loading_location':
			case 'transporter':
			case 'next_transporter':
			case 'carriage':
			case 'payment_condition':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'int'
				));
                                return true;

			case 'location':
			case 'places_count':
			case 'packing_method':
			case 'merchandise_name':
			case 'cargo_class':
			case 'nr':
			case 'cmr_char':
			case 'adr':
			case 'measure_unit':
			case 'gross_weight':
			case 'capacity':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'varchar(255)'
				));
                                return true;

			case 'unloading_note':
			case 'loading_note':
			case 'added_documents':
			case 'transporter_note':
			case 'marking':
			case 'receiver_instructions':
			case 'sender_special_notes':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'text'
				));
                                return true;
                }

		return false;
	}

}
?>
