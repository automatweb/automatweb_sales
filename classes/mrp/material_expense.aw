<?php
/*
@classinfo syslog_type=ST_MATERIAL_EXPENSE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=robert
@tableinfo aw_material_expense master_index=brother_of master_table=objects index=aw_oid

@default table=aw_material_expense
@default group=general

@property product type=relpicker reltype=RELTYPE_PRODUCT
@caption Materjal

@property job type=relpicker reltype=RELTYPE_JOB
@caption Tegevus

@property case type=relpicker reltype=RELTYPE_CASE field=aw_case
@caption Projekt

@property amount type=textbox datatype=int
@caption Planeeritud kogus

@property used_amount type=textbox
@caption Kulutatud kogus

@property base_amount type=textbox datatype=int
@caption Kogus p&otilde;hi&uuml;hikus

@property unit type=relpicker reltype=RELTYPE_UNIT
@caption &Uuml;hik

@property planning type=select
@caption Planeerimine

@property movement type=select
@caption Materjali liikumine

@reltype PRODUCT value=1 clid=CL_SHOP_PRODUCT
@caption Materjal

@reltype JOB value=2 clid=CL_MRP_JOB
@caption Tegevus

@reltype UNIT value=3 clid=CL_UNIT
@caption &Uuml;hik

@reltype CASE value=4 clid=CL_MRP_JOB
@caption Projekt
*/

class material_expense extends class_base
{
	function material_expense()
	{
		$this->init(array(
			"tpldir" => "mrp/material_expense",
			"clid" => CL_MATERIAL_EXPENSE
		));
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_material_expense(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "product":
			case "job":
			case "aw_case":
			case "unit":
			case "planning":
			case "movement":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
			case "amount":
			case "base_amount":
			case "used_amount":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "char(20)"
				));
				return true;
		}
	}
}

?>
