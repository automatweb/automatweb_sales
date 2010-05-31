<?php

namespace automatweb;
// rostering_account.aw - Eelarvestamise konto
/*

@classinfo syslog_type=ST_ROSTERING_ACCOUNT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@tableinfo aw_budgeting_account master_table=objects master_index=brother_of index=aw_oid
@tableinfo aw_account_balances master_index=oid master_table=objects index=aw_oid

@default table=aw_budgeting_account
@default group=general

	@property owner type=relpicker reltype=RELTYPE_OWNER field=aw_owner
	@caption Omanik

	@property balance type=text table=aw_account_balances field=aw_balance
	@caption Saldo

	@property min_amt type=textbox size=5 field=aw_min_amt
	@caption Kohustuslik varu



@default group=transfers

	@property transfers_tb type=toolbar no_caption=1 store=no
	@property transfers_tbl type=table store=no no_caption=1

@default group=taxes

	@property taxes_tb type=toolbar no_caption=1 store=no
	@property taxes_tbl type=table store=no no_caption=1

@groupinfo transfers caption="Tehingud" submit=no save=no
@groupinfo taxes caption="Maksud" submit=no save=no

@reltype OWNER value=1 clid=CL_CRM_COMPANY,CL_CRM_PERSON,CL_PROJECT,CL_TASK,CL_CRM_SECTOR,CL_BUDGETING_FUND
@caption Omanik


*/

class budgeting_account extends class_base
{
	const AW_CLID = 1202;

	function budgeting_account()
	{
		$this->init(array(
			"tpldir" => "applications/budgeting/budgeting_account",
			"clid" => CL_BUDGETING_ACCOUNT
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

	function _get_transfers_tb($arr)
	{
		$arr["prop"]["vcl_inst"]->add_new_button(array(CL_BUDGETING_TRANSFER), $arr["obj_inst"]->id());
		$arr["prop"]["vcl_inst"]->add_delete_button();
	}

	function _get_transfers_tbl($arr)
	{
		$data = new object_list(array(
			"class_id" => CL_BUDGETING_TRANSFER,
			"lang_id" => array(),
			"site_id" => array(),
			"from_acct" => $arr["obj_inst"]->id(),
		));
		$arr["prop"]["vcl_inst"]->table_from_ol(
			$data,
			array("name", "from_acct", "to_acct", "in_project", "amount", "when"),
			CL_BUDGETING_TRANSFER
		);
	}

	function _get_taxes_tb($arr)
	{
		$arr["prop"]["vcl_inst"]->add_new_button(array(CL_BUDGETING_TAX_FOLDER_RELATION), $arr["obj_inst"]->id() , array(),array("folder" => $arr["obj_inst"]->id()));
		$arr["prop"]["vcl_inst"]->add_delete_button();
	}

	function _get_taxes_tbl($arr)
	{
		$data = $arr["obj_inst"]->get_account_taxes();
		$arr["prop"]["vcl_inst"]->table_from_ol(
			$data,
			array("name", "to_acct", "amount", "pri", "tax_grp"),
			CL_BUDGETING_TAX
		);
	}

	function do_db_upgrade($t,$f)
	{
		if ("aw_account_balances" === $t)
		{
			$i = get_instance(CL_CRM_CATEGORY);
			return $i->do_db_upgrade($t, $f);
		}

		if ($f == "" and "aw_budgeting_account" === $t)
		{
			$this->db_query("CREATE TABLE aw_budgeting_account (aw_oid int primary key, aw_owner int,aw_balance double, aw_min_amt double)");
			return true;
		}

		return false;
	}
}
?>
