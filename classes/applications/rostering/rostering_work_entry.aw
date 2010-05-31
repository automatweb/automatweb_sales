<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/rostering/rostering_work_entry.aw,v 1.7 2008/10/16 15:08:16 markop Exp $
// rostering_work_entry.aw - T&ouml;&ouml;aegade sisestus 
/*

@classinfo syslog_type=ST_ROSTERING_WORK_ENTRY relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@tableinfo aw_rostering_work_entry index=aw_oid master_index=brother_of master_table=objects

@default table=aw_rostering_work_entry
@default group=general

	@property graph type=relpicker automatic=1 reltype=RELTYPE_GRAPH field=aw_graph
	@caption Graafik

	@property g_wp type=relpicker automatic=1 reltype=RELTYPE_WORKBENCH field=aw_g_wp
	@caption T&ouml;&ouml;laud

	@property g_unit type=relpicker automatic=1 field=aw_unit reltype=RELTYPE_UNIT multiple=1 store=connect
	@caption &Uuml;ksus
	
	@property g_day type=select field=aw_day 
	@caption P&auml;ev
	

@default group=entry

	@property entry_header type=text store=no no_caption=1
	@property entry_t type=table store=no no_caption=1

@groupinfo entry caption="Sisestamine"

@reltype GRAPH value=1 clid=CL_ROSTERING_SCHEDULE
@caption Graafik

@reltype WORKBENCH value=2 clid=CL_ROSTERING_WORKBENCH
@caption T&ouml;&ouml;laud

@reltype UNIT value=3 clid=CL_CRM_SECTION
@caption &Uuml;ksus
*/

class rostering_work_entry extends class_base
{
	const AW_CLID = 1160;

	function rostering_work_entry()
	{
		$this->init(array(
			"tpldir" => "applications/rostering/rostering_work_entry",
			"clid" => CL_ROSTERING_WORK_ENTRY
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "entry_t":
				$this->_entry_t($arr);
				break;

			case "g_wp":
				if ($arr["request"]["wp"])
				{
					$prop["value"] = $arr["request"]["wp"];
				}
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
			case "entry_t":
				$arr["obj_inst"]->set_meta("d", $arr["request"]["d"]);
				break;
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
			$this->db_query("CREATE TABLE aw_rostering_work_entry(aw_oid int primary key, aw_graph int)");
			return true;
		}
		switch($f)
		{
			case "aw_unit":
			case "aw_day":
			case "aw_g_wp":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}

	function _init_entry_t(&$t)
	{
		$t->define_field(array(
			"name" => "def",
			"caption" => t("Planeeritud"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "wpl",
			"caption" => t("T&ouml;&ouml;post"),
			"align" => "center",
			"parent" => "def"
		));
		$t->define_field(array(
			"name" => "person",
			"caption" => t("Isik"),
			"align" => "center",
			"parent" => "def"
		));
		$t->define_field(array(
			"name" => "skill",
			"caption" => t("P&auml;devus"),
			"align" => "center",
			"parent" => "def"
		));
		$t->define_field(array(
			"name" => "hrs",
			"caption" => t("Aeg"),
			"align" => "center",
			"parent" => "def"
		));

		$t->define_field(array(
			"name" => "real",
			"caption" => t("Tegelik"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "correct",
			"caption" => t("Graafik korrektne"),
			"align" => "center",
			"parent" => "real"
		));
		$t->define_field(array(
			"name" => "set_person",
			"caption" => t("T&ouml;&ouml;taja"),
			"align" => "center",
			"parent" => "real"
		));
		$t->define_field(array(
			"name" => "set_hrs_from",
			"caption" => t("Alates"),
			"align" => "center",
			"parent" => "real"
		));
		$t->define_field(array(
			"name" => "set_hrs_to",
			"caption" => t("Kuni"),
			"align" => "center",
			"parent" => "real"
		));
		$t->define_field(array(
			"name" => "pay_type",
			"caption" => t("Tasu liik"),
			"align" => "center",
			"parent" => "real"
		));
	}

	function _entry_t($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_entry_t($t);

		// filter persons by section and graph by day selected
		$wp = obj($arr["obj_inst"]->prop("g_wp"));
		$co = obj($wp->prop("owner"));
		$co_i = $co->instance();

		$gr = obj($arr["obj_inst"]->prop("graph"));

		$ppl = array();
		$units = $gr->prop("g_unit");
		if (is_array($arr["obj_inst"]->prop("g_unit")) && count($arr["obj_inst"]->prop("g_unit")))
		{
			$units = $arr["obj_inst"]->prop("g_unit");
		}

		if (is_array($units) && count($units))
		{
			foreach($units as $unit)
			{
				$u = obj($unit);
				$ppl = $ppl + $u->get_worker_selection();
//				foreach($u->connections_from(array("type" => "RELTYPE_WORKERS")) as $c)
//				{
//					$ppl[$c->prop("to")] = $c->prop("to.name");
//				}
			}
		}
		else
		{
			$ppl = $co_i->get_employee_picker($co);
		}

		// get date range from selected graph
		$gr = obj($arr["obj_inst"]->prop("graph"));
		$start = $arr["obj_inst"]->prop("g_day") ? $arr["obj_inst"]->prop("g_day") : $gr->prop("g_start");
		$end = $start + 24 * 3600;

		$pt = array();
		foreach($wp->connections_from(array("type" => "RELTYPE_PAYMENT_TYPE")) as $c)
		{
			$pt[$c->prop("to")] = $c->prop("to.name");
		}

		$d = $arr["obj_inst"]->meta("d");
		$m = get_instance("applications/rostering/rostering_model");
		foreach($ppl as $p_id => $p_nm)
		{
			$wt = $m->get_schedule_for_person(obj($p_id), $start, $end);
			foreach($wt as $wt_id => $wt_item)
			{
				$t->define_data(array(
					"wpl" => html::obj_change_url($wt_item["workplace"]),
					"person" => html::obj_change_url($p_id),
					"skill" => html::obj_change_url($wt_item["skill"]),
					"hrs" => date("d.m.Y H:i", $wt_item["start"])." - ".date("d.m.Y H:i", $wt_item["end"]),
					"correct" => html::checkbox(array(
						"name" => "d[$wt_id][correct]",
						"value" => 1,
						"checked" => $d[$wt_id]["correct"]
					)),
					"set_person" => html::select(array(
						"name" => "d[$wt_id][person]",
						"options" => array("" => t("--vali--")) + $ppl,
						"value" => $d[$wt_id]["person"]
					)),
					"set_hrs_from" => html::textbox(array(
						"name" => "d[$wt_id][h_from]",
						"size" => 5,
						"value" => $d[$wt_id]["h_from"]
					)),
					"set_hrs_to" => html::textbox(array(
						"name" => "d[$wt_id][h_to]",
						"size" => 5,
						"value" => $d[$wt_id]["h_to"]
					)),
					"pay_type" => html::select(array(
						"name" => "d[$wt_id][pay_type]",
						"options" => array("" => t("--vali--")) + $pt,
						"value" => $d[$wt_id]["pay_type"]
					)),
				));
			}
		}
		$t->set_sortable(false);
	}

	function _get_entry_header($arr)
	{
		return PROP_IGNORE;
		// get date range from selected graph
		$gr = obj($arr["obj_inst"]->prop("graph"));
		$start = $gr->prop("g_start");
		$end = $gr->prop("g_end");
		$date = $arr["request"]["date"] ? $arr["request"]["date"] : $start;

		$dstr = array();
		for($tm = $start; $tm < $end; $tm += (24*3600))
		{
			if ($tm == $date)
			{
				$dstr[] = date("d.m.Y", $tm);
			}
			else
			{
				$dstr[] = html::href(array(
					"url" => aw_url_change_var("date", $tm),
					"caption" => date("d.m.Y", $tm)
				));
			}
		}
		$arr["prop"]["value"] = join(" /  ", $dstr);
	}

	function _get_g_unit($arr)
	{
		if ($this->can("view", $arr["obj_inst"]->prop("graph")))
		{
			$go = obj($arr["obj_inst"]->prop("graph"));
			if (is_array($go->prop("g_unit")) && count($go->prop("g_unit")))
			{
				$arr["prop"]["options"] = array("" => t("--vali--"));
				foreach(safe_array($go->prop("g_unit")) as $unit_id)
				{
					if ($this->can("view", $unit_id))
					{
						$uo = obj($unit_id);
						$arr["prop"]["options"][$unit_id] = $uo->name();
					}
				}
			}
		}
	}

	function _get_g_day($arr)
	{
		if ($this->can("view", $arr["obj_inst"]->prop("graph")))
		{
			$go = obj($arr["obj_inst"]->prop("graph"));
			for($tm = $go->prop("g_start"); $tm < $go->prop("g_end"); $tm += 24*3600)
			{
				$arr["prop"]["options"][$tm] = date("d.m.Y", $tm);
			}
		}
	}
}
?>
