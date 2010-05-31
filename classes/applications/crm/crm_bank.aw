<?php

namespace automatweb;

// crm_bank.aw - CRM Pank
/*

@classinfo syslog_type=ST_CRM_BANK relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_crm_bank index=aw_oid master_index=brother_of master_table=objects

@default table=aw_crm_bank
@default group=general

	@property address type=relpicker reltype=RELTYPE_ADDRESS field=aw_address
	@caption Aadress

@reltype ADDRESS value=1 clid=CL_CRM_ADDRESS
@caption aadress

*/

class crm_bank extends class_base
{
	const AW_CLID = 1023;

	function crm_bank()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_bank",
			"clid" => CL_CRM_BANK
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

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}
}
?>
