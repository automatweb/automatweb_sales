<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/crm/crm_bank_account.aw,v 1.5 2007/12/06 14:33:17 kristo Exp $
// crm_bank_account.aw - CRM Pangakonto
/*

@classinfo syslog_type=ST_CRM_BANK_ACCOUNT relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@tableinfo aw_crm_bank_account index=aw_oid master_index=brother_of master_table=objects maintainer=markop

@default table=objects
@default group=general

	@property acct_no type=textbox table=aw_crm_bank_account field=aw_acct_no
	@caption Arve number

	@property iban_code type=textbox table=objects field=meta method=serialize
	@caption IBAN kood

	@property sort_code type=textbox table=objects field=meta method=serialize
	@caption Kodukontori kood

	@property bank type=relpicker reltype=RELTYPE_BANK automatic=1 table=aw_crm_bank_account field=aw_bank
	@caption Pank

	@property currency type=relpicker reltype=RELTYPE_CURRENCY automatic=1 table=aw_crm_bank_account field=aw_currency
	@caption Valuuta

@reltype BANK value=1 clid=CL_CRM_BANK
@caption pank

@reltype CURRENCY value=2 clid=CL_CURRENCY
@caption Valuuta

*/

class crm_bank_account extends class_base
{
	const AW_CLID = 1024;

	function crm_bank_account()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_bank_account",
			"clid" => CL_CRM_BANK_ACCOUNT
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

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function do_db_upgrade($t, $f)
	{
		switch($f)
		{
			case "aw_currency":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}
}
?>
