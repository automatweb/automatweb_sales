<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/crm/crm_company_role_entry.aw,v 1.4 2008/01/31 13:54:12 kristo Exp $
// crm_company_role_entry.aw - Rolli kirje 
/*

@classinfo syslog_type=ST_CRM_COMPANY_ROLE_ENTRY relationmgr=yes maintainer=markop

@default group=general

@tableinfo aw_crm_company_roles index=aw_oid master_index=brother_of master_table=objects

@property person type=relpicker reltype=RELTYPE_PERSON table=aw_crm_company_roles field=aw_person
@caption Isik

@property role type=relpicker reltype=RELTYPE_ROLE table=aw_crm_company_roles field=aw_role
@caption Roll

@property company type=relpicker reltype=RELTYPE_COMPANY table=aw_crm_company_roles field=aw_company
@caption Organisatsioon

@property client type=relpicker reltype=RELTYPE_COMPANY table=aw_crm_company_roles field=aw_client
@caption Klient

@property unit type=relpicker reltype=RELTYPE_UNIT table=aw_crm_company_roles field=aw_unit
@caption &Uuml;ksus

@property project type=relpicker reltype=RELTYPE_PROJECT table=aw_crm_company_roles field=aw_project
@caption &Uuml;ksus

@reltype PERSON value=1 clid=CL_CRM_PERSON
@caption isik

@reltype ROLE value=2 clid=CL_CRM_PROFESSION
@caption amet

@reltype COMPANY value=3 clid=CL_CRM_COMPANY
@caption organisatsioon

@reltype UNIT value=4 clid=CL_CRM_SECTION
@caption &uuml;ksus

@reltype PROJECT value=5 clid=CL_PROJECT
@caption &uuml;ksus

*/

class crm_company_role_entry extends class_base
{
	const AW_CLID = 864;

	function crm_company_role_entry()
	{
		$this->init(array(
			"tpldir" => "crm/crm_company_role_entry",
			"clid" => CL_CRM_COMPANY_ROLE_ENTRY
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{

		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{

		}
		return $retval;
	}	
}
?>
