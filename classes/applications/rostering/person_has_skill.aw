<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/rostering/person_has_skill.aw,v 1.3 2008/05/14 15:42:23 markop Exp $
// person_has_skill.aw - Oskuse kehtivus 
/*

@classinfo syslog_type=ST_PERSON_HAS_SKILL relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@tableinfo aw_person_has_skill index=aw_oid master_table=objects master_index=brother_of

@default table=objects
@default group=general

	@property skill type=relpicker reltype=RELTYPE_SKILL automatic=1 table=aw_person_has_skill field=aw_skill
	@caption P&auml;devus

	@property level type=select table=aw_person_has_skill field=aw_skill_level
	@caption Tase

	@property hour_price type=textbox table=aw_person_has_skill field=aw_hour_price
	@caption P&auml;devuse kasutamise tunnihind

	@property skill_acquired type=date_select table=aw_person_has_skill field=aw_skill_acquired
	@caption Omandatud

	@property skill_lost type=text store=no
	@caption Kaotatud

@reltype SKILL value=1 clid=CL_PERSON_SKILL
@caption P&auml;devus
*/

class person_has_skill extends class_base
{
	function person_has_skill()
	{
		$this->init(array(
			"tpldir" => "applications/rostering/person_has_skill",
			"clid" => CL_PERSON_HAS_SKILL
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "level":
				$prop["options"] = array(0,1,2,3,4,5,6,7,8,9);
			break;
			case "skill":
				if(!empty($arr["request"]["skill"]))
				{
					$prop["value"] = $arr["request"]["skill"];
				}
		//		arr($arr);
				break;
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
			$this->db_query("CREATE TABLE aw_person_has_skill (aw_oid int primary key, aw_skill int, aw_skill_acquired int)");
			return true;
		}
		switch($f)
		{
			case "aw_skill_level":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
			case "aw_hour_price":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "double"
				));
				return true;
		}
	}
}
?>
