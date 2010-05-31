<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/crm/transport_management/crm_transport_management_carriage.aw,v 1.3 2007/12/06 14:33:24 kristo Exp $
// carriage.aw - Vedu 
/*

@classinfo syslog_type=ST_CARRIAGE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=dragut

@tableinfo crm_transport_management_carriage index=oid master_table=objects master_index=oid

@default table=objects
@default group=general

	@property start_date type=date_select table=crm_transport_management_carriage
	@caption Alguskuup&auml;ev

	@property end_date type=date_select table=crm_transport_management_carriage
	@caption L&otilde;pukuup&auml;ev

	@property return_deadline type=date_select table=crm_transport_management_carriage
	@caption Tagasisaabumise t&auml;htaeg

	@property route type=relpicker reltype=RELTYPE_ROUTE table=crm_transport_management_carriage
	@caption Marsruut

	@property start_location type=relpicker reltype=RELTYPE_START_ADDRESS table=crm_transport_management_carriage
	@caption Alguspunkt

	@property end_location type=relpicker reltype=RELTYPE_END_ADDRESS table=crm_transport_management_carriage
	@caption Sihtpunkt

	@property trucks_table type=table 
	@caption Veoautod

	@property trailer type=relpicker reltype=RELTYPE_TRAILER table=crm_transport_management_carriage
	@caption Haagise nr.	

	@property transporter type=relpicker reltype=RELTYPE_TRANSPORTER table=crm_transport_management_carriage
	@caption Vedaja

	@property dispatcher type=relpicker reltype=RELTYPE_DISPATCHER table=crm_transport_management_carriage
	@caption Ekspediitor

	@property code type=textbox table=crm_transport_management_carriage
	@caption Kood (tuletatakse ekspediitori kasutajanimest + autoincremental number)

	@property carriage_status type=select table=crm_transport_management_carriage
	@caption Veo staatus

	@property income type=text store=no
	@caption Tulud

	@property costs type=text store=no
	@caption Kulud

	@property profit type=text store=no
	@caption Kasum

@groupinfo orders caption="Tellimused"
@default group=orders

	@property orders_table type=table
	@caption Tellimuste tabel

@groupinfo costs caption="Kulud"
@default group=costs

	@property costs_table type=table
	@caption Kulude tabel

@groupinfo income caption="Tulud"
@default group=income

	@property incomes_table type=table
	@caption Tulude tabel


@reltype ROUTE value=1 clid=CL_CRM_TRANSPORT_MANAGEMENT_ROUTE
@caption Marsruut

@reltype START_ADDRESS value=2 clid=CL_CRM_ADDRESS
@caption Alguspunkt

@reltype END_ADDRESS value=3 clid=CL_CRM_ADDRESS
@caption Sihtpunkt

@reltype TRUCK value=4 clid=CL_CRM_TRANSPORT_MANAGEMENT_TRUCK
@caption Veoauto

@reltype TRAILER value=5 clid=CL_CRM_TRANSPORT_MANAGEMENT_TRAILER
@caption Haagis

@reltype TRANSPORTER value=6 clid=CL_CRM_COMPANY
@caption Vedaja

@reltype DISPATCHER value=7 clid=CL_CRM_PERSON
@caption Ekspediitor
*/

define('CARRIAGE_STATUS_RECRUIT', 1);
define('CARRIAGE_STATUS_CONFIRMED', 2);
define('CARRIAGE_STATUS_IN_PROGRESS', 3);
define('CARRIAGE_STATUS_COMPLETED', 4);
define('CARRIAGE_STATUS_ARCHIVED', 5);

class crm_transport_management_carriage extends class_base
{
	const AW_CLID = 1083;


	var $carriage_status = array();

	function crm_transport_management_carriage()
	{
		$this->init(array(
			"tpldir" => "applications/crm/transport_management/crm_transport_management_carriage",
			"clid" => CL_CRM_TRANSPORT_MANAGEMENT_CARRIAGE
		));

		$this->carriage_status = array(
			CARRIAGE_STATUS_RECRUIT => t('Komplekteerimisel'),
			CARRIAGE_STATUS_CONFIRMED => t('Kinnitatud'),
			CARRIAGE_STATUS_IN_PROGRESS => t('Hetkel t&ouml;&ouml;s'),
			CARRIAGE_STATUS_COMPLETED => t('Valmis'),
			CARRIAGE_STATUS_ARCHIVED => t('Arhiveeritud')
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case 'carriage_status':
				$prop['options'] = $this->carriage_status;
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
	
	function _get_trucks_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);
		$t->define_field(array(
			'name' => 'truck',
			'caption' => t('Veoauto')
		));

		$t->define_data(array(
			'truck' => 'siia selle veoautode lisamise teeb natuke hilje, n2itab neid seoste p6hjal, ilmselt popup search vaja siis integreerida'
		));
	}

	function _get_orders_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);
		$t->define_field(array(
			'name' => 'order',
			'caption' => t('Tellimus')
		));

		$t->define_data(array(
			'order' => 'Selle veoga seotud tellimused siia [todo]'
		));
	}

	function _get_costs_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);
		$t->define_field(array(
			'name' => 'name',
			'caption' => t('Nimetus')
		));

		$t->define_field(array(
			'name' => 'sum',
			'caption' => t('Summa')
		));

		for ( $i = 0; $i < 10; $i++ )
		{
			$t->define_data(array(
				'name' => html::textbox(array(
					'name' => 'costs['.$i.'][name]',
				)),
				'sum' => html::textbox(array(
					'name' => 'costs['.$i.'][sum]',
				))
			));
		}


	}

	function _get_incomes_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);
		$t->define_field(array(
			'name' => 'client',
			'caption' => t('Kliendi nimi')
		));

		$t->define_field(array(
			'name' => 'sum',
			'caption' => t('Summa')
		));

		$t->define_data(array(
			'client' => 'kliendi nimi',
			'sum' => '150.-'
		));
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
			case 'start_date':
			case 'end_date':
			case 'return_deadline':
			case 'route':
			case 'start_location':
			case 'end_location':
			case 'trailer':
			case 'transporter':
			case 'dispatcher':
			case 'carriage_status':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'int'
				));
                                return true;
			case 'code':
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
