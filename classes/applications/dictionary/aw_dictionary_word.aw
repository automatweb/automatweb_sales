<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_aw_dictionary_word master_index=brother_of master_table=objects index=aw_oid

@default table=aw_aw_dictionary_word
@default group=general

*/

class aw_dictionary_word extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/dictionary/aw_dictionary_word",
			"clid" => CL_AW_DICTIONARY_WORD
		));
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_aw_dictionary_word" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_aw_dictionary_word` (
				  `aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`aw_oid`)
				)");
				$r = true;
			}
			elseif ("" === $field)
			{
				$this->db_add_col("aw_aw_dictionary_word", array(
					"name" => "",
					"type" => ""
				));
				$r = true;
			}
		}

		return $r;
	}
}
