<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@extends menu
@tableinfo aw_crm_invoice_folder master_index=brother_of master_table=objects index=aw_oid

@default table=aw_crm_invoice_folder
@default group=general

*/

class crm_invoice_folder extends menu
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_invoice_folder",
			"clid" => crm_invoice_folder_obj::CLID
		));
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_crm_invoice_folder" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_crm_invoice_folder` (
				  `aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`aw_oid`)
				)");
				$r = true;
			}
			elseif ("" === $field)
			{
				$this->db_add_col("aw_crm_invoice_folder", array(
					"name" => "",
					"type" => ""
				));
				$r = true;
			}
		}

		return $r;
	}
}
