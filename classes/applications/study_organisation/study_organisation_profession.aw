<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_study_organisation_profession master_index=brother_of master_table=objects index=aw_oid

@default table=aw_study_organisation_profession
@default group=general

	@property ord type=textbox size=4 table=objects field=jrk
	@caption Jrk

	@property teaching type=textbox type=textbox field=aw_teaching
	@caption &Otilde;ppetöö (min)

	@property research type=textbox field=aw_research
	@caption Teadustöö (min)

	@property administrating type=textbox field=aw_administrating
	@caption Administratiivtöö

	@property competence type=textbox field=aw_competence
	@caption N&otilde;utav kompetents

	@property load type=textbox field=aw_load
	@caption N&otilde;utav töömaht

*/

class study_organisation_profession extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/study_organisation/study_organisation_profession",
			"clid" => CL_STUDY_ORGANISATION_PROFESSION
		));
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_study_organisation_profession" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_study_organisation_profession` (
				  `aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`aw_oid`)
				)");
				$r = true;
			}
			elseif (in_array($field, array("aw_teaching", "aw_research", "aw_administrating", "aw_competence", "aw_load")))
			{
				$this->db_add_col("aw_study_organisation_profession", array(
					"name" => $field,
					"type" => "smallint unsigned"
				));
				$r = true;
			}
		}

		return $r;
	}
}
