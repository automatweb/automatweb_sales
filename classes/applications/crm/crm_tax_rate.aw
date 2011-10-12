<?php

// crm_tax_rate.aw - Maksum&auml;&auml;r
/*

@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_crm_tax_rate index=aw_oid master_table=objects master_index=brother_of
@default table=aw_crm_tax_rate

@default group=general

	@property tax_type type=select field=aw_tax_type
	@caption Maksu liik

	@property tax_amt type=textbox size=5 field=aw_tax_amt
	@caption Maksum&auml;&auml;r (%)

	@property act_from type=date_select field=aw_act_from
	@caption Kehtib alates

	@property act_to type=date_select field=aw_act_to
	@caption Kehtib kuni

	@property code type=textbox field=aw_code
	@caption K&auml;ibemaksukood

	@property acct type=textbox field=aw_acct_no
	@caption Kontonumber

*/

class crm_tax_rate extends class_base
{
	function crm_tax_rate()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_tax_rate",
			"clid" => CL_CRM_TAX_RATE
		));

		$this->types = array(
			"km" => t("K&auml;ibemaks"),
			"yk_tm" => t("&Uuml;ksikisiku tulumaks"),
			"co_tm" => t("Ettev&otilde;tte tulumaks"),
			"sots" => t("Sotsiaalmaks"),
			"tkm" => t("T&ouml;&ouml;tuskindlustusmaks")
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "tax_type":
				$prop["options"] = $this->types;
				break;
		};
		return $retval;
	}

	function do_db_upgrade($table, $field, $q, $err)
	{
		if ($table == "aw_crm_tax_rate" && $field == "aw_acct_no")
		{
			$this->db_query("alter table aw_crm_tax_rate add aw_acct_no varchar(100)");
			return true;
		}
	}
}
