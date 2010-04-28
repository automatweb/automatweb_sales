<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/crm/transport_management/crm_transport_management_car_type.aw,v 1.3 2007/12/06 14:33:24 kristo Exp $
// crm_transport_management_car_type.aw - Automark 
/*

@classinfo syslog_type=ST_CRM_TRANSPORT_MANAGEMENT_CAR_TYPE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=dragut

@tableinfo crm_transport_management_car_type index=oid master_table=objects master_index=oid

@default table=objects
@default group=general

@property type type=select table=crm_transport_management_car_type
@caption T&uuml;&uuml;p

*/

define('CAR_TYPE_PASSANGER_CAR', 1);
define('CAR_TYPE_TRUCK', 2);
define('CAR_TYPE_SUV', 3);

class crm_transport_management_car_type extends class_base
{
	const AW_CLID = 1087;

	
	var $car_types = array();

	function crm_transport_management_car_type()
	{
		$this->init(array(
			"tpldir" => "applications/crm/transport_management/crm_transport_management_car_type",
			"clid" => CL_CRM_TRANSPORT_MANAGEMENT_CAR_TYPE
		));

		$this->car_types = array(
			CAR_TYPE_PASSANGER_CAR => t('S&otilde;iduauto'),
			CAR_TYPE_TRUCK => t('Veoauto'),
			CAR_TYPE_SUV => t('Maastur')
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "type":
				$prop['options'] = $this->car_types;
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

	function do_db_upgrade($table, $field, $query, $error)
	{
		if (empty($field))
		{
			$this->db_query('CREATE TABLE '.$table.' (oid INT PRIMARY KEY NOT NULL)');
			return true;
		}

		switch ($field)
		{
			case 'type':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'int'
				));
                                return true;
                }

		return false;
	}

}
?>
