<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_aw_dictionary master_index=brother_of master_table=objects index=aw_oid

@default table=aw_aw_dictionary
@default group=general

*/

class aw_dictionary extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/dictionary/aw_dictionary",
			"clid" => CL_AW_DICTIONARY
		));
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_aw_dictionary" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_aw_dictionary` (
				  `aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`aw_oid`)
				)");
				$r = true;
			}
			elseif ("" === $field)
			{
				$this->db_add_col("aw_aw_dictionary", array(
					"name" => "",
					"type" => ""
				));
				$r = true;
			}
		}

		return $r;
	}
}
