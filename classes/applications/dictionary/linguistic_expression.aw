<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_linguistic_expression master_index=brother_of master_table=objects index=aw_oid

@default table=aw_linguistic_expression
@default group=general

*/

class linguistic_expression extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/dictionary/linguistic_expression",
			"clid" => CL_LINGUISTIC_EXPRESSION
		));
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_linguistic_expression" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_linguistic_expression` (
				  `aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`aw_oid`)
				)");
				$r = true;
			}
			elseif ("" === $field)
			{
				$this->db_add_col("aw_linguistic_expression", array(
					"name" => "",
					"type" => ""
				));
				$r = true;
			}
		}

		return $r;
	}
}
