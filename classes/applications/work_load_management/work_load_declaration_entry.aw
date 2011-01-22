<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_work_load_declaration_entry master_index=brother_of master_table=objects index=aw_oid

@default table=aw_work_load_declaration_entry
@default group=general

	@property user type=objpicker clid=CL_USER field=aw_user
	@caption Kasutaja

*/

class work_load_declaration_entry extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/work_load_management/work_load_declaration_entry",
			"clid" => CL_WORK_LOAD_DECLARATION_ENTRY
		));
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_work_load_declaration_entry" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_work_load_declaration_entry` (
				  `aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`aw_oid`)
				)");
				$r = true;
			}
			elseif ("aw_user" === $field)
			{
				$this->db_add_col("aw_work_load_declaration_entry", array(
					"name" => $field,
					"type" => "int"
				));
				$r = true;
			}
		}

		return $r;
	}
}
