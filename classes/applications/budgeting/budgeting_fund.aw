<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/budgeting/budgeting_fund.aw,v 1.3 2007/12/06 14:32:51 kristo Exp $
// budgeting_fund.aw - Eelarvestamise fond 
/*

@classinfo syslog_type=ST_BUDGETING_FUND relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@tableinfo aw_budgeting_fund master_table=objects master_index=brother_of index=aw_oid
@tableinfo aw_account_balances master_index=oid master_table=objects index=aw_oid

@default table=aw_budgeting_fund
@default group=general

	@property balance type=text table=aw_account_balances field=aw_balance
	@caption Saldo



*/

class budgeting_fund extends class_base
{
	function budgeting_fund()
	{
		$this->init(array(
			"tpldir" => "applications/budgeting/budgeting_fund",
			"clid" => CL_BUDGETING_FUND
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

	function do_db_upgrade($t,$f)
	{
		if ("aw_account_balances" == $tbl)
		{
			$i = get_instance(CL_CRM_CATEGORY);
			return $i->do_db_upgrade($t, $f);
		}
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_budgeting_fund (aw_oid int primary key, aw_owner int,aw_balance double)");
		}
	}
}
?>
