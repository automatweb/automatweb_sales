<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_crm_company_ownership master_index=brother_of master_table=objects index=aw_oid

@default table=aw_crm_company_ownership
@default group=general

	@property owner type=objpicker clid=CL_CRM_PERSON,CL_CRM_COMPANY field=aw_owner
	@caption Omanik

	@property share_percentage type=textbox field=aw_share_percentage
	@caption Osalusprotsent

*/

class crm_company_ownership extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_company_ownership",
			"clid" => CL_CRM_COMPANY_OWNERSHIP
		));
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_crm_company_ownership" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_crm_company_ownership` (
				  `aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`aw_oid`)
				)");
				$r = true;
			}
			elseif ("aw_owner" === $field)
			{
				$this->db_add_col("aw_crm_company_ownership", array(
					"name" => $field,
					"type" => "int default 0"
				));
				$r = true;
			}
			elseif ("aw_share_percentage" === $field)
			{
				$this->db_add_col("aw_crm_company_ownership", array(
					"name" => $field,
					"type" => "decimal(8,5)"
				));
				$r = true;
			}
		}

		return $r;
	}
}
