<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_crm_customers_webview master_index=brother_of master_table=objects index=aw_oid

@default table=aw_crm_customers_webview
@default group=general

*/

class crm_customers_webview extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_customers_webview",
			"clid" => crm_customers_webview_obj::CLID
		));
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_crm_customers_webview" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_crm_customers_webview` (
				  `aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`aw_oid`)
				)");
				$r = true;
			}
			elseif ("" === $field)
			{
				$this->db_add_col("aw_crm_customers_webview", array(
					"name" => "",
					"type" => ""
				));
				$r = true;
			}
		}

		return $r;
	}
}
