<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_study_organisation_publication_category master_index=brother_of master_table=objects index=aw_oid

@default table=aw_study_organisation_publication_category
@default group=general

	@property ord type=textbox size=4 table=objects field=jrk
	@caption Jrk

*/

class study_organisation_publication_category extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/study_organisation/study_organisation_publication_category",
			"clid" => CL_STUDY_ORGANISATION_PUBLICATION_CATEGORY
		));
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_study_organisation_publication_category" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_study_organisation_publication_category` (
				  `aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`aw_oid`)
				)");
				$r = true;
			}
			elseif ("" === $field)
			{
				$this->db_add_col("aw_study_organisation_publication_category", array(
					"name" => "",
					"type" => ""
				));
				$r = true;
			}
		}

		return $r;
	}
}
