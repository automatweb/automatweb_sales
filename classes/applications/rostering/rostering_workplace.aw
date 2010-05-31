<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/rostering/rostering_workplace.aw,v 1.4 2007/12/06 14:34:03 kristo Exp $
// rostering_workplace.aw - T&ouml;&ouml;koht 
/*

@classinfo syslog_type=ST_ROSTERING_WORKPLACE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@tableinfo aw_rostering_workplace index=aw_oid master_index=brother_of master_table=objects

@default table=objects
@default group=general

	@property skills type=relpicker multiple=1 store=connect reltype=RELTYPE_SKILL automatic=1
	@caption P&auml;devused

	@property professions type=relpicker store=connect reltype=RELTYPE_PROFESSION automatic=1
	@caption Ametinimetused

	@property address type=relpicker reltype=RELTYPE_ADDRESS table=aw_rostering_workplace field=aw_address
	@caption Aadress

	@property num_empl type=textbox table=aw_rostering_workplace field=aw_num_empl size=5
	@caption Mitu t&ouml;&ouml;tajat korraga

@default group=stats

	@property stats_tbl type=table store=no no_caption=1

@groupinfo stats caption="Statistika"

@reltype SKILL value=1 clid=CL_PERSON_SKILL
@caption P&auml;devus

@reltype ADDRESS value=2 clid=CL_CRM_ADDRESS
@caption Aadress

@reltype PROFESSION value=3 clid=CL_CRM_PROFESSION
@caption Ametinimetus
*/

class rostering_workplace extends class_base
{
	const AW_CLID = 1142;

	function rostering_workplace()
	{
		$this->init(array(
			"tpldir" => "applications/rostering/rostering_workplace",
			"clid" => CL_ROSTERING_WORKPLACE
		));
		classload("core/date/date_calc");
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "stats_tbl":
				$this->_stats_tbl($arr);
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

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_rostering_workplace(aw_oid int primary key, aw_address int)");
			return true;
		}

		switch($f)
		{
			case "aw_num_empl":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}

	function _init_stats_tbl(&$t)
	{
		$t->define_field(array(
			"name" => "person",
			"caption" => t("Isik"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "wh",
			"caption" => t("T&ouml;&ouml;ajad"),
			"align" => "center"
		));
	}

	function _stats_tbl($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_stats_tbl($t);

		$m = get_instance("applications/rostering/rostering_model");
		
		// get workplace
		$conn = reset($arr["obj_inst"]->connections_to(array("from.class_id" => CL_ROSTERING_WORKBENCH)));
		$wp = $conn->from();

		// from that co, persons from there
		$co = get_instance(CL_CRM_COMPANY);
		$ppl = $co->get_employee_picker(obj($wp->prop("owner")));
		foreach($ppl as $p_id => $p_nm)
		{
			$p = obj($p_id);
			$schedule = $m->get_schedule_for_person($p, get_month_start(), get_month_start() + 60*24*3600);
			$whs = "";
			foreach($schedule as $w_item)
			{
				$whs .= date("d.m.Y H:i", $w_item["start"])." - ".date("d.m.Y H:i", $w_item["end"])." <br>";
			}
			$t->define_data(array(
				"person" => html::obj_change_url($p_id),
				"wh" => $whs
			));
		}
	}
}
?>
