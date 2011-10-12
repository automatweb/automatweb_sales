<?php
/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@tableinfo aw_ext_system_entries index=aw_oid master_table=objects master_index=brother_of

@default table=aw_ext_system_entries
@default group=general

	@property ext_sys_id type=relpicker reltype=RELTYPE_EXTSYS field=aw_ext_sys_id
	@caption Siduss&uuml;steem

	@property obj type=relpicker reltype=RELTYPE_OBJ field=aw_obj
	@caption AW Objekt

	@property value type=textbox field=aw_value
	@caption V&auml;&auml;rtus

@reltype EXTSYS value=1 clid=CL_EXTERNAL_SYSTEM
@caption Siduss&uuml;steem

@reltype OBJ value=2
@caption AW Objekt

*/

class external_system_entry extends class_base
{
	function external_system_entry()
	{
		$this->init(array(
			"tpldir" => "common/external/external_system_entry",
			"clid" => CL_EXTERNAL_SYSTEM_ENTRY
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
			$this->db_query("CREATE TABLE aw_ext_system_entries (aw_oid int primary key, aw_ext_sys_id int, aw_obj int, aw_value varchar(100))");
			return true;
		}
	}
}
?>
