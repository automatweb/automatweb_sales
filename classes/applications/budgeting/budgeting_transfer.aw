<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/budgeting/budgeting_transfer.aw,v 1.3 2007/12/06 14:32:51 kristo Exp $
// budgeting_transfer.aw - &Uuml;lekanne 
/*

@classinfo syslog_type=ST_BUDGETING_TRANSFER relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@tableinfo aw_budgeting_transfers master_table=objects master_index=brother_of index=aw_oid

@default table=aw_budgeting_transfers
@default group=general

	@property from_acct type=relpicker reltype=RELTYPE_FROM_ACCT field=aw_from_acct
	@caption Kontolt

	@property to_acct type=relpicker reltype=RELTYPE_TO_ACCT field=aw_to_acct
	@caption Kontole

	@property in_project type=relpicker reltype=RELTYPE_IN_PROJECT field=aw_in_project
	@caption Seoses projektiga

	@property amount type=textbox size=5 field=aw_amt
	@caption Summa

	@property when type=datetime_select  field=aw_when
	@caption Millal

	@property do_transfer type=text  store=no no_caption=1
	@caption Millal

@reltype FROM_ACCT value=1 clid=CL_BUDGETING_ACCOUNT,CL_CRM_COMPANY,CL_CRM_PERSON,CL_CRM_CATEGORY,CL_PROJECT,CL_TASK,CL_BUDGETING_FUND,CL_SHOP_PRODUCT
@caption Kontolt

@reltype TO_ACCT value=2 clid=CL_BUDGETING_ACCOUNT,CL_CRM_COMPANY,CL_CRM_PERSON,CL_CRM_CATEGORY,CL_PROJECT,CL_TASK,CL_BUDGETING_FUND,CL_SHOP_PRODUCT
@caption Kontole

@reltype IN_PROJECT value=3 clid=CL_PROJECT
@caption Seoses projektiga
*/

class budgeting_transfer extends class_base
{
	const AW_CLID = 1203;

	function budgeting_transfer()
	{
		$this->init(array(
			"tpldir" => "applications/budgeting/budgeting_transfer",
			"clid" => CL_BUDGETING_TRANSFER
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "do_transfer":
				$prop["value"] = html::href(array(
					"url" => $this->mk_my_orb("apply_transfer", array("id" => $arr["obj_inst"]->id(), "r" => get_ru())),
					"caption" => t("Teosta &uuml;lekanne kohe")
				));
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
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function do_db_upgrade($t,$f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_budgeting_transfers (aw_oid int primary key,aw_from_acct int, aw_to_acct int,aw_amt double,aw_when int)");
		}

		switch($f)
		{
			case "aw_in_project":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}

	/**
		@attrib name=apply_transfer
		@param id required 
		@param r required
	**/
	function apply_transfer($arr)
	{
		// transfer money
		$tf = obj($arr["id"]);
		$m = get_instance("applications/budgeting/budgeting_model");
		$m->set_account_balance(
			obj($tf->prop("to_acct")), 
			$m->get_account_balance(obj($tf->prop("to_acct"))) + $tf->prop("amount")
		);

		$m->apply_taxes_on_money_transfer($tf);
		return $arr["r"];
	}
}
?>
