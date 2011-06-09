<?php
/*
@classinfo syslog_type=ST_CRM_OFFER_TEMPLATE relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@extends applications/crm/sales/crm_offer

@tableinfo aw_crm_offer master_index=brother_of master_table=objects index=aw_oid
@tableinfo aw_crm_offer_template master_index=brother_of master_table=objects index=aw_oid

@default table=aw_crm_offer_template
@default group=general

	@property template_name type=textbox field=name table=objects
	@caption Nimi

	@property offer type=objpicker clid=CL_CRM_OFFER field=aw_offer
	@caption Pakkumus

*/

class crm_offer_template extends crm_offer
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/crm/sales/crm_offer",
			"clid" => crm_offer_template_obj::CLID
		));
	}

	public function _get_offer($arr)
	{
		return PROP_IGNORE;
	}

	public function _set_offer($arr)
	{
		return PROP_IGNORE;
	}

	public function _get_state($arr)
	{
		return PROP_IGNORE;
	}

	public function _get_result($arr)
	{
		return PROP_IGNORE;
	}

	public function _get_save_as_template(&$arr)
	{
		return PROP_IGNORE;
	}

	public function _get_submit_button($arr)
	{
		return PROP_IGNORE;
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_crm_offer" === $table)
		{
			$r = parent::do_db_upgrade($table, $field, $query, $error);
		}

		if ("aw_crm_offer_template" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_crm_offer_template` (
				  `aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`aw_oid`)
				)");
				$r = true;
			}
			elseif ("aw_offer" === $field)
			{
				$this->db_add_col("aw_crm_offer_template", array(
					"name" => $field,
					"type" => "int"
				));
				$r = true;
			}
		}

		return $r;
	}
}

