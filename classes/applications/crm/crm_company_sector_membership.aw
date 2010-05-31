<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_CRM_COMPANY_SECTOR_MEMBERSHIP relationmgr=yes no_comment=1 prop_cb=1 maintainer=instrumental
#@tableinfo aw_crm_company_sector_membership master_index=brother_of master_table=objects index=aw_oid

#@default table=aw_crm_company_sector_membership
@default group=general

@property company type=relpicker reltype=RELTYPE_COMPANY store=connect
@caption Organisatsioon

@property sector type=relpicker reltype=RELTYPE_SECTOR store=connect
@caption Tegevusala

##

@reltype COMPANY value=1 clid=CL_CRM_COMPANY
@caption Organisatsioon

@reltype SECTOR value=2 clid=CL_CRM_SECTOR
@caption Tegevusala

*/

class crm_company_sector_membership extends class_base
{
	const AW_CLID = 1524;

	function crm_company_sector_membership()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_company_sector_membership",
			"clid" => CL_CRM_COMPANY_SECTOR_MEMBERSHIP
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
		}

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

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_crm_company_sector_membership(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => ""
				));
				return true;
		}
	}
}

?>
