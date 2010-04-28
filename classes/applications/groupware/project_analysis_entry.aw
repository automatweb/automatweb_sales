<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/groupware/project_analysis_entry.aw,v 1.2 2007/12/06 14:33:32 kristo Exp $
// project_analysis_entry.aw - Projekti anal&uuml;&uuml;si sisestus 
/*

@classinfo syslog_type=ST_PROJECT_ANALYSIS_ENTRY relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@default table=objects
@default group=general

@tableinfo aw_proj_analysis index=aw_oid master_table=objects master_index=brother_of

@default group=general

	@property proj type=relpicker reltype=RELTYPE_PROJECT table=aw_proj_analysis field=aw_proj
	@caption Projekt

	@property evaluator type=relpicker reltype=RELTYPE_EVAL table=aw_proj_analysis field=aw_eval
	@caption Hindaja

@reltype PROJECT value=1 clid=CL_PROJECT
@caption Projekt

@reltype EVAL value=2 clid=CL_CRM_PERSON
@caption Hindaja
*/

class project_analysis_entry extends class_base
{
	const AW_CLID = 1113;

	function project_analysis_entry()
	{
		$this->init(array(
			"tpldir" => "applications/groupware/project_analysis_entry",
			"clid" => CL_PROJECT_ANALYSIS_ENTRY
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_proj_analysis(aw_oid int primary key, aw_proj int, aw_eval int)");
			return true;
		}
	}
}
?>
