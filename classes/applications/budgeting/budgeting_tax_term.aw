<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_BUDGETING_TAX_TERM relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop
@tableinfo aw_budgeting_tax_term master_index=brother_of master_table=objects index=aw_oid

@default table=aw_budgeting_tax_term
@default group=general

	@property from_place type=relpicker field=aw_from_place reltype=RELTYPE_FROM_ACCT
	@caption Kust

	@property amount_final type=textbox size=5 field=aw_amt_final
	@caption Summa t&auml;isarv

	@property amount type=textbox size=5 field=aw_amt
	@caption Summa %

	@property max_deviation_minus type=textbox size=5 field=aw_max_deviation_minus
	@caption Maksimaalne projektip&otilde;hine muudatus -

	@property max_deviation_plus type=textbox size=5 field=aw_max_deviation_plus
	@caption Maksimaalne projektip&otilde;hine muudatus +

	@property pri type=textbox size=5 field=aw_pri
	@caption Prioriteet

@reltype FROM_ACCT value=1 clid=CL_BUDGETING_ACCOUNT,CL_BUDGETING_FUND,CL_CRM_CATEGORY,CL_CRM_COMPANY,CL_CRM_PERSON,CL_PROJECT,CL_SHOP_PRODUCT,CL_TASK
@caption Kontolt


*/

class budgeting_tax_term extends class_base
{
	const AW_CLID = 1447;

	function budgeting_tax_term()
	{
		$this->init(array(
			"tpldir" => "applications/budgeting/budgeting_tax_term",
			"clid" => CL_BUDGETING_TAX_TERM
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

	function callback_mod_reforb($arr)
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
			$this->db_query("CREATE TABLE aw_budgeting_tax_term (aw_oid int primary key, aw_from_place int)");
			return true;
		}

		switch($f)
		{
			case "aw_from_place":
			case "aw_pri":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
			case "aw_amt_final":
			case "aw_amt":
			case "aw_max_deviation_minus":
			case "aw_max_deviation_plus":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "double"
				));
				return true;
		}
	}
}
?>
