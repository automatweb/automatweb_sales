<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/crm/crm_vacation.aw,v 1.4 2007/12/06 14:33:17 kristo Exp $
// crm_vacation.aw - Puhkus 
/*

@classinfo syslog_type=ST_CRM_VACATION relationmgr=yes maintainer=markop

@default group=general

@default table=planner

@property start1 type=datetime_select field=start 
@caption Algab

@property end type=datetime_select field=end 
@caption L�peb

@property person_ref type=textbox table=external_reference field=ext_id
@caption Viit isikule

@default table=objects
@default field=meta
@default method=serialize

@property duration_days type=textbox 
@caption Kestvus p�evades

@property type type=relpicker reltype=RELTYPE_VACATION_TYPE automatic=1
@caption Puhkuse t��p

@reltype VACATION_TYPE value=1 clid=CL_META
@caption Puhkuse t��p
	
@tableinfo planner index=id master_table=objects master_index=oid
@tableinfo external_reference index=aw_id master_table=objects master_index=oid

*/

class crm_vacation extends class_base
{
	function crm_vacation()
	{
		$this->init(array(
			"clid" => CL_CRM_VACATION
		));
	}

	/*
	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{

		};
		return $retval;
	}
	*/

	/*
	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{

		}
		return $retval;
	}	
	*/

}
?>
