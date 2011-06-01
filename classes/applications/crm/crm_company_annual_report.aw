<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_crm_company_annual_report master_index=brother_of master_table=objects index=aw_oid

@default table=aw_crm_company_annual_report
@default group=general

	@property company type=objpicker clid=CL_CRM_COMPANY field=aw_company
	@caption Organisatsioon

	@property year type=textbox field=aw_year
	@caption Aasta

	@property currency type=objpicker field=aw_currency
	@caption Valuuta

	@property value_added_tax type=textbox field=aw_value_added_tax
	@caption K&auml;ibemaks

	@property social_security_tax type=textbox field=aw_social_security_tax
	@caption Sotsiaalmaks

	@property assets type=textbox field=aw_assets
	@caption Varad (bilansimaht)

	@property turnover type=textbox field=aw_turnover
	@caption &Auml;ritulu (tegevustulu)

	@property profit type=textbox field=aw_profit
	@caption Puhaskasum (tulem)

	@property employees type=textbox field=aw_employees
	@caption T&ouml;&ouml;tajaid

	@property turnover_per_employee type=textbox field=aw_turnover_per_employee
	@caption K&auml;ive t&ouml;&ouml;taja kohta

*/

class crm_company_annual_report extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_company_annual_report",
			"clid" => crm_company_annual_report_obj::CLID
		));
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_crm_company_annual_report" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_crm_company_annual_report` (
				  `aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`aw_oid`)
				)");
				$r = true;
			}
			elseif ("aw_company" === $field or "aw_currency" === $field or "aw_employees" === $field)
			{
				$this->db_add_col("aw_crm_company_annual_report", array(
					"name" => $field,
					"type" => "int default 0"
				));
				$r = true;
			}
			elseif ("aw_year" === $field)
			{
				$this->db_add_col("aw_crm_company_annual_report", array(
					"name" => $field,
					"type" => "smallint default 0"
				));
				$r = true;
			}
			elseif (in_array($field, array("aw_value_added_tax", "aw_social_security_tax", "aw_assets", "aw_turnover", "aw_turnover_per_employee", "aw_profit")))
			{
				$this->db_add_col("aw_crm_company_annual_report", array(
					"name" => $field,
					"type" => "decimal(24,4) default 0"
				));
				$r = true;
			}
		}

		return $r;
	}
}
