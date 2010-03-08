<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/procurement_center/procurement_priority.aw,v 1.2 2007/12/06 14:33:50 kristo Exp $
// procurement_priority.aw - Hanke prioriteet 
/*

@classinfo syslog_type=ST_PROCUREMENT_PRIORITY relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@tableinfo aw_procurement_priorities index=aw_oid master_index=brother_of master_table=objects

@default table=aw_procurement_priorities
@default group=general

	@property pri type=textbox size=5 field=aw_pri
	@caption Prioriteet
*/

class procurement_priority extends class_base
{
	function procurement_priority()
	{
		$this->init(array(
			"tpldir" => "applications/procurement_center/procurement_priority",
			"clid" => CL_PROCUREMENT_PRIORITY
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
			$this->db_query("CREATE TABLE aw_procurement_priorities (aw_oid int primary key, aw_pri double)");
			return true;
		}
	}
}
?>
