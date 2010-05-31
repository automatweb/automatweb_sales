<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/contentmgmt/target_audience.aw,v 1.2 2008/01/31 13:52:15 kristo Exp $
// target_audience.aw - Sihtr&uuml;hm 
/*

@classinfo syslog_type=ST_TARGET_AUDIENCE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_target_audience index=aw_oid master_index=brother_of master_table=objects
@default table=aw_target_audience

@default group=general

	@property ugroup type=relpicker reltype=RELTYPE_GROUP field=aw_group automatic=1
	@caption Kasutajagrupp

@reltype GROUP value=1 clid=CL_GROUP
@caption Kasutajagrupp
*/

class target_audience extends class_base
{
	const AW_CLID = 1063;

	function target_audience()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/target_audience",
			"clid" => CL_TARGET_AUDIENCE
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

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_target_audience (aw_oid int primary key, aw_group int)");
			return true;
		}
	}
}
?>
