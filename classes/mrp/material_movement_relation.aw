<?php
/*
@classinfo syslog_type=ST_MATERIAL_MOVEMENT_RELATION relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=robert
@tableinfo aw_material_movement_relation master_index=brother_of master_table=objects index=aw_oid

@default table=aw_material_movement_relation
@default group=general

@property job type=relpicker reltype=RELTYPE_JOB
@caption Tegevus

@property dn type=relpicker reltype=RELTYPE_DN
@caption Saateleht

@reltype JOB value=1 clid=CL_MRP_JOB
@caption Tegevus

@reltype DN value=2 clid=CL_SHOP_DELIVERY_NOTE
@caption Saateleht
*/

class material_movement_relation extends class_base
{
	function material_movement_relation()
	{
		$this->init(array(
			"tpldir" => "mrp/material_movement_relation",
			"clid" => CL_MATERIAL_MOVEMENT_RELATION
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
		}

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

	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_material_movement_relation(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "dn":
			case "job":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}
}

?>
