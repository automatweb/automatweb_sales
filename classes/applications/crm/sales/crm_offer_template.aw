<?php
/*
@classinfo syslog_type=ST_CRM_OFFER_TEMPLATE relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_crm_offer_template master_index=brother_of master_table=objects index=aw_oid

@default table=aw_crm_offer_template
@default group=general

@property offer type=objpicker class=CL_CRM_OFFER field=aw_offer
@caption Pakkumine

*/

class crm_offer_template extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/crm/sales/crm_offer_template",
			"clid" => CL_CRM_OFFER_TEMPLATE
		));
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

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

?>
