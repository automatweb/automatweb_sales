<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/crm/building_management/crm_building_management_unit.aw,v 1.4 2007/12/06 14:33:21 kristo Exp $
// crm_building_management_unit.aw - &Uuml;hik 
/*

@classinfo syslog_type=ST_CRM_BUILDING_MANAGEMENT_UNIT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=dragut

@tableinfo crm_building_management_unit index=oid master_table=objects master_index=oid

@default table=objects
@default group=general

@property code type=textbox table=crm_building_management_unit
@caption Kood

*/

class crm_building_management_unit extends class_base
{
	const AW_CLID = 1108;

	function crm_building_management_unit()
	{
		$this->init(array(
			"tpldir" => "applications/crm/building_management/crm_building_management_unit",
			"clid" => CL_CRM_BUILDING_MANAGEMENT_UNIT
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- get_property --//
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
