<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/rostering/rostering_schedule.aw,v 1.8 2008/10/16 15:08:16 markop Exp $
// rostering_schedule.aw - Rostering graafik 
/*

@classinfo syslog_type=ST_ROSTERING_SCHEDULE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@tableinfo aw_rostering_schedule index=aw_oid master_index=brother_of master_table=objects

@default table=aw_rostering_schedule
@default group=general

	@property final type=checkbox ch_value=1 field=aw_final 
	@caption Kinnitatud
	@comment Kui graafik on kinnitatud, siis graafikut enam muuta ei saa!

	@property g_start type=date_select field=aw_g_start default=-1
	@caption Algus

	@property g_end type=date_select field=aw_g_end default=-1
	@caption L&otilde;pp

	@property g_scenario type=relpicker display=radio automatic=1 field=aw_g_scenario reltype=RELTYPE_SCENARIO
	@caption Stsenaarium

	@property g_unit type=relpicker automatic=1 field=aw_g_unit reltype=RELTYPE_UNIT multiple=1 store=connect
	@caption &Uuml;ksus

	@property g_wp type=relpicker automatic=1 reltype=RELTYPE_WORKBENCH field=aw_g_wp
	@caption T&ouml;&ouml;laud

@default group=admin_act

	@property admin_act_tb type=toolbar no_caption=1 store=no
	
	@property admin_act_cal type=calendar no_caption=1 store=no

@default group=op_act

	@layout opa type=hbox

		@property op_act_cal type=text no_caption=1 store=no parent=opa

		@property op_act_problems type=text store=no parent=opa no_caption=1
		@caption Vali t&ouml;&ouml;taja

@default group=skills_g

	@property skg_tbl type=table no_caption=1 store=no

@default group=shifts_g_1

	@property shifts_g_tbl type=table no_caption=1 store=no

@default group=shifts_g_2

	@property shifts_g2_tbl type=table no_caption=1 store=no

@default group=day_g

	@property day_g_tbl type=table no_caption=1 store=no

@default group=mn_g

	@property mn_g_tbl type=table no_caption=1 store=no

@default group=work_hrs

	@property work_hrs type=table store=no no_caption=1


@groupinfo act caption="Tegevused"
	@groupinfo admin_act caption="Administratiivsed tegevused" submit=no parent=act
	@groupinfo op_act caption="Operatiivsed tegevused" parent=act

@groupinfo skills_g caption="P&auml;devused" 
@groupinfo shifts_g caption="Vahetused" submit=no
	@groupinfo shifts_g_1 caption="T&ouml;&ouml;postid ja vahetused" parent=shifts_g submit=no
	@groupinfo shifts_g_2 caption="Vahetused ja t&ouml;&ouml;postid" parent=shifts_g submit=no

@groupinfo day_g caption="P&auml;eva vaade" submit=no
@groupinfo mn_g caption="Kuu vaade" submit=no
@groupinfo work_hrs caption="T&ouml;&ouml;aruanded" 

@reltype SCENARIO value=1 clid=CL_ROSTERING_SCENARIO
@caption Stsenaarium

@reltype WORKBENCH value=2 clid=CL_ROSTERING_WORKBENCH
@caption T&ouml;&ouml;laud

@reltype UNIT value=3 clid=CL_CRM_SECTION
@caption &Uuml;ksus

*/

class rostering_schedule extends class_base
{
	function rostering_schedule()
	{
		$this->init(array(
			"tpldir" => "applications/rostering/rostering_schedule",
			"clid" => CL_ROSTERING_SCHEDULE
		));
		classload("core/date/date_calc");
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "day_g_tbl":
				$this->_day_g_tbl($arr);
				break;

			case "shifts_g_tbl":
				$this->_shifts_g_tbl($arr);
				break;

			case "shifts_g2_tbl":
				$this->_shifts_g2_tbl($arr);
				break;

			case "admin_act_tb":
				$this->_admin_act_tb($arr);
				break;

			case "admin_act_cal":
				$this->_admin_act_cal($arr);
				break;

			case "op_act_cal":
				$this->_op_act_cal($arr);
				break;

			case "op_act_problems":
				return $this->_op_act_problems($arr);

			case "skg_tbl":
				$this->_skg_tbl($arr);
				break;

			case "g_wp":
				if ($arr["request"]["wp"])
				{
					$prop["value"] = $arr["request"]["wp"];
				}
				break;

			case "mn_g_tbl":
				$this->_mn_g_tbl($arr);
				break;

			case "g_scenario":
				unset($prop["options"][0]);
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

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	function _admin_act_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_menu_button(array(
			"name" => "new",
		));
	
		$o = obj($arr["obj_inst"]->prop("g_wp"));
		$clss = aw_ini_get("classes");
		foreach(array(CL_TASK, CL_CRM_MEETING) as $clid)
		{
			$tb->add_menu_item(array(
				"parent" => "new",
				"text" => $clss[$clid]["name"],
				"link" => html::get_new_url($clid, $o->id(), array("return_url" => get_ru()))
			));
		}
	}

	function _admin_act_cal($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];

		$arr["prop"]["vcl_inst"]->configure(array(
			"overview_func" => array(&$this,"get_overview"),
		));

		if (!$arr["request"]["viewtype"])
		{
			$arr["request"]["viewtype"] = "month";
		}

		$range = $arr["prop"]["vcl_inst"]->get_range(array(
			"date" => $arr["request"]["date"],
			"viewtype" => !empty($arr["request"]["viewtype"]) ? $arr["request"]["viewtype"] : $arr["prop"]["viewtype"],
		));

		$start = $range["start"];
		$end = $range["end"];

		$o = obj($arr["obj_inst"]->prop("g_wp"));
		$events = new object_list(array(
			"parent" => $o->id(),
			"class_id" => array(CL_TASK, CL_CRM_MEETING),
			"lang_id" => array(),
			"site_id" => array()
		));
		
		foreach($events->arr() as $o)
		{
			$icon = icons::get_icon_url($o);
			$t->add_item(array(
				"timestamp" => $o->prop("start1"),
				"item_start" => ($o->class_id() == CL_CRM_MEETING ? $o->prop("start1") : NULL),
				"item_end" => ($o->class_id() == CL_CRM_MEETING ? $o->prop("end") : NULL),
				"data" => array(
					"name" => $o->name(),
					"link" => html::get_change_url($o->id(), array("return_url" => get_ru())),
					"modifiedby" => $o->prop("modifiedby"),
					"icon" => $icon,
					'comment' => $o->comment(),
				),
			));
		}
	}

	function _op_act_cal($arr)
	{
		classload("core/date/date_calc");
		$m = get_instance("applications/rostering/rostering_model");		
		$start = get_week_start();
		$end = get_week_start()+24*7*3600;
		if ($arr["request"]["rostering_chart_start"])
		{
			$start = $arr["request"]["rostering_chart_start"];
		}
		$columns = $arr["request"]["rostering_chart_length"] ? $arr["request"]["rostering_chart_length"] : 7;
		if ($arr["request"]["rostering_chart_length"])
		{
			if ($arr["request"]["rostering_chart_length"] == "mon")
			{
				$start = $this->get_week_start($start);
				$end = mktime(0,0,0, date("m", $start)+1, date("d", $start),date("Y", $start));
				$end = $this->get_week_start($end)+7*3600*24;
				$i = date("W", $start);
				$e = date("W", $end)-1;
				$columns = $e - $i;
			}
			else
			{
				$end = $start + 24*$arr["request"]["rostering_chart_length"]*3600;
			}
		}

		$subs = 24;
		if ($columns > 1)
		{
			$subs = 3;
		}
		if ($arr["request"]["rostering_chart_length"])
		{
			$subs = 7;
		}
			
		$chart = get_instance("vcl/gantt_chart");
		$chart->configure_chart (array (
			"chart_id" => "person_wh",
			"style" => "aw",
			"subdivisions" => $subs,
			"timespans" => $subs,
			"timespan_range" => $arr["request"]["rostering_chart_length"] == "mon" ? 7*3600*24 : 3600*24,
			"start" => $start,
			"end" => $end,
			"width" => 850,
			"row_height" => 12,
			"columns" => $columns,
			"row_dfn" => t("T&ouml;&ouml;post"),
			"column_length" => $arr["request"]["rostering_chart_length"] == "mon" ? 7*3600*24 : 3600*24
		));

		if ($arr["request"]["rostering_chart_length"] == "mon")
		{
			$nc = 1;
			while ($i <= $e)
			{
				$chart->define_column(array(
					"col" => $nc++,
					"title" => $i,
					"uri" => "#"
				));
				$i++;
			}
		}
		else
		{
			$i = 0;
			$days = array ("P", "E", "T", "K", "N", "R", "L");
			while ($i < $columns)
			{
				$day_start = ($start + ($i * 86400));
				$day = date ("w", $day_start);
				$date = date ("j/m/Y", $day_start);
				$uri = aw_url_change_var ("rostering_chart_length", 1);
				$uri = aw_url_change_var ("rostering_chart_start", $day_start, $uri);
				$chart->define_column (array (
					"col" => ($i + 1),
					"title" => $days[$day] . " - " . $date,
					"uri" => $uri,
				));
				$i++;
			}
		}

		$o = obj($arr["obj_inst"]->prop("g_wp"));

		$co = get_instance(CL_CRM_COMPANY);
		$ppl = $co->get_employee_picker(obj($o->prop("owner")));
		static $wtid;
		$wpl2p = array();
		$wpl2sched = array();
		foreach($ppl as $p_id => $p_n)
		{
			$work_times = $m->get_schedule_for_person(obj($p_id), $start, $end);
			foreach($work_times as $wt_item)
			{
				$bar = array (
					"id" => ++$wtid,
					"row" => $wt_item["workplace"],
					"start" => $wt_item["start"],
					"length" => $wt_item["end"] - $wt_item["start"],
					"title" => $p_n.": ".date("d.m.Y H:i", $wt_item["start"])." - ".date("d.m.Y H:i", $wt_item["end"]),
					"uri" => aw_url_change_var(array(
						"problem_id" => null,
						"wt_id" =>  $wtid,
						"wt_from" => $wt_item["start"],
						"wt_to" => $wt_item["end"]
					))
				);

				$chart->add_bar ($bar);
				$wpl2p[$wt_item["workplace"]][$p_id] = $bar;
				$bar["row"] = $wt_item["workplace"]."_".$p_id;
				$chart->add_bar ($bar);
				$wpl2sched[$wt_item["workplace"]][$wt_item["start"]] = $wt_item["end"];
			}
		}

		foreach($wpl2sched as $wpl_id => $data)
		{
			$tm = $start;
			ksort($data);
			reset($data);
			while ($tm < $end)
			{
				list($_from, $_to) = each($data);
				if (!$_from)
				{
					$_from = $end;
					$_to = $end;
				}
				$bar = array (
					"id" => ++$wtid,
					"row" => $wpl_id,
					"start" => $tm+1,
					"length" => ($_from - $tm)-2,
					"title" => sprintf(t("Planeerimata : %s - %s"), date("d.m.Y H:i", $tm), date("d.m.Y H:i", $_from)),
					"uri" => aw_url_change_var(array(
						"problem_id" => $wtid,
						"wt_if" => null
					)),
					"colour" => "red"
				);
				$chart->add_bar ($bar);
				$tm = $_to;
			}
		}


		$ol = new object_list(array(
			"class_id" => CL_ROSTERING_WORKPLACE,
			"lang_id" => array(),
			"site_id" => array()
		));
		foreach($ol->arr() as $wpl)
		{
			//html::get_change_url($wpl->id(), array("return_url" => get_ru()))
			$bar = array (
				"name" => $wpl->id(),
				"title" => "<b>".$wpl->name()."</b>",
				"uri" => aw_url_change_var("show_p", $wpl->id())
			);
			$chart->add_row ($bar);

			// get ppl for workpost
			foreach($wpl2p[$wpl->id()] as $p_id => $sets)
			{
				$po= obj($p_id);
				$chart->add_row (array (
					"name" => $wpl->id()."_".$p_id,
					"title" => " -&gt; ".$po->name(),
					"uri" => html::get_change_url($p_id, array("return_url" => get_ru()))
				));
			}
		}


		$arr["prop"]["value"] = '<div id="tablebox">
		    <div class="pais">
			<div class="caption">'.$this->create_chart_navigation($arr).'</div>
			<div class="navigaator">
			</div>
		    </div>
		    <div class="sisu">
		    <!-- SUB: GRID_TABLEBOX_ITEM -->
			'.$chart->draw_chart().'
		    <!-- END SUB: GRID_TABLEBOX_ITEM -->
		    </div>
		    <div>
		    </div>	
		</div>';	
	}

    // @attrib name=get_time_days_away
	// @param days required type=int
	// @param direction optional type=int
	// @param time optional
	// @returns UNIX timestamp for time of day start $days away from day start of $time
	// @comment DST safe if cumulated error doesn't exceed 12h. If $direction is negative, time is computed for days back otherwise days to.
	function get_time_days_away ($days, $time = false, $direction = 1)
	{
		if (false === $time)
		{
			$time = time ();
		}

		$time_daystart = mktime (0, 0, 0, date ("m", $time), date ("d", $time), date("Y", $time));
		$day_start = ($direction < 0) ? ($time_daystart - $days*86400) : ($time_daystart + $days*86400);
		$nodst_hour = (int) date ("H", $day_start);

		if ($nodst_hour)
		{
			if ($nodst_hour < 13)
			{
				$dst_error = $nodst_hour;
				$day_start = $day_start - $dst_error*3600;
			}
			else
			{
				$dst_error = 24 - $nodst_hour;
				$day_start = $day_start + $dst_error*3600;
			}
		}

		return $day_start;
	}

	function get_week_start ($time = false) //!!! somewhat dst safe (safe if error doesn't exceed 12h)
	{
		if (!$time)
		{
			$time = time ();
		}

		$date = getdate ($time);
		$wday = $date["wday"] ? ($date["wday"] - 1) : 6;
		$week_start = $time - ($wday * 86400 + $date["hours"] * 3600 + $date["minutes"] * 60 + $date["seconds"]);
		$nodst_hour = (int) date ("H", $week_start);

		if ($nodst_hour)
		{
			if ($nodst_hour < 13)
			{
				$dst_error = $nodst_hour;
				$week_start = $week_start - $dst_error*3600;
			}
			else
			{
				$dst_error = 24 - $nodst_hour;
				$week_start = $week_start + $dst_error*3600;
			}
		}

		return $week_start;
	}

	function create_chart_navigation ($arr)
	{
		$start = (int) ($arr["request"]["rostering_chart_start"] ? $arr["request"]["rostering_chart_start"] : time ());
		$columns = (int) ($arr["request"]["rostering_chart_length"] ? $arr["request"]["rostering_chart_length"] : 7);
		$start = ($columns == 7) ? $this->get_week_start ($start) : $start;
		$period_length = $columns * 86400;
		$length_nav = array ();
		$start_nav = array ();

		for ($days = 1; $days < 8; $days++)
		{
			if ($columns == $days)
			{
				$length_nav[] = $days;
			}
			else
			{
				$length_nav[] = html::href (array (
					"caption" => $days,
					"url" => aw_url_change_var ("rostering_chart_length", $days),
				));
			}
		}

		$start_nav[] = html::href (array (
			"caption" => t("<<"),
			"title" => t("5 tagasi"),
			"url" => aw_url_change_var ("rostering_chart_start", ($this->get_time_days_away (5*$columns, $start, -1))),
		));
		$start_nav[] = html::href (array (
			"caption" => t("Eelmine"),
			"url" => aw_url_change_var ("rostering_chart_start", ($this->get_time_days_away ($columns, $start, -1))),
		));
		$start_nav[] = html::href (array (
			"caption" => t("T&auml;na"),
			"url" => aw_url_change_var ("rostering_chart_start", $this->get_week_start ()),
		));
		$start_nav[] = html::href (array (
			"caption" => t("J&auml;rgmine"),
			"url" => aw_url_change_var ("rostering_chart_start", ($this->get_time_days_away ($columns, $start))),
		));
		$start_nav[] = html::href (array (
			"caption" => t(">>"),
			"title" => t("5 edasi"),
			"url" => aw_url_change_var ("rostering_chart_start", ($this->get_time_days_away (5*$columns, $start))),
		));

		$navigation = sprintf(t('&nbsp;&nbsp;Periood: %s &nbsp;&nbsp;P&auml;evi perioodis: %s'), implode (" ", $start_nav) ,implode (" ", $length_nav));

		if (is_oid ($arr["request"]["rostering_hilight"]))
		{
			$project = obj ($arr["request"]["rostering_hilight"]);
			$deselect = html::href (array (
				"caption" => t("Kaota valik"),
				"url" => aw_url_change_var ("rostering_hilight", ""),
			));
			$change_url = html::obj_change_url ($project);
			$navigation .= t(' &nbsp;&nbsp;Valitud projekt: ') . $change_url . ' (' . $deselect . ')';
		}

		$navigation .= "&nbsp;&nbsp;&nbsp;".html::href(array(
			"url" => aw_url_change_var("rostering_chart_length", "mon"),
			"caption" => t("Kuuvaade")
		));

		return $navigation;
	}

	function _op_act_problems($arr)
	{
		if (!$arr["request"]["problem_id"] && !$arr["request"]["wt_id"])
		{
			return PROP_IGNORE;
		}

		$o = obj($arr["obj_inst"]->prop("g_wp"));

		$co = get_instance(CL_CRM_COMPANY);
		$ppl = $co->get_employee_picker(obj($o->prop("owner")));

		if ($arr["request"]["problem_id"])
		{
			// list some sort of problems
			$rv = t("Vali t&ouml;&ouml;taja: <br>");
			foreach($ppl as $p_id => $p_nm)
			{
				$rv .= html::radiobutton(array(
					"name" => "select_p",
					"value" => $p_id,
				))." ".html::obj_change_url($p_id)."<br>";
				$rv .= "&nbsp;&nbsp;&nbsp; teises vahetuses <br>";
				$rv .= "&nbsp;&nbsp;&nbsp; t&ouml;&ouml;tunnid &uuml;letatud <br>";
			}
		}
		else
		{
			$rv = t("Vali uus t&ouml;&ouml;aeg: <br>");
			$rv .= t("Alates:<br> ").html::datetime_select(array(
				"name" => "from",
				"value" => $arr["request"]["wt_from"]
			))." <br>";
			$rv .= t("Kuni:<br> ").html::datetime_select(array(
				"name" => "to",
				"value" => $arr["request"]["wt_to"]
			))." <br>";
			$rv .= t("K&auml;sitsi m&auml;&auml;ratud:<br> ").html::checkbox(array(
				"name" => "manual_set",
				"ch_value" => 1,
				"checked" => true
			))." <br>";
		}
		$arr["prop"]["value"] = $rv;
		return PROP_OK;
	}

	function _init_skg_tbl(&$t, &$wpl2skill, $o)
	{
		// get all units from the graph and for each unit, check if it has workplaces set.
		// if it does, then only add those. 
		// if not, then add all of them
		$ol = new object_list();
		foreach(safe_array($o->prop("g_unit")) as $unit)
		{
			$uo = obj($unit);
			if (count(safe_array($uo->prop("wpls"))))
			{
				foreach(safe_array($uo->prop("wpls")) as $wpl)
				{
					$ol->add($wpl);
				}
			}
			else
			{
				$ol->add(new object_list(array(
					"class_id" => CL_ROSTERING_WORKPLACE,
					"lang_id" => array(),
					"site_id" => array()
				)));
			}
		}
		$t->define_field(array(
			"name" => "empl",
			"caption" => t("T&ouml;&ouml;taja"),
			"align" => "left"
		));
		foreach($ol->arr() as $o)
		{
			$t->define_field(array(
				"name" => $o->id(),
				"caption" => $o->name(),
				"align" => "center"
			));
			foreach($o->connections_from(array("type" => "RELTYPE_SKILL")) as $c)
			{
				$skill_id = $c->prop("to");
				$skill = obj($skill_id);
				$t->define_field(array(
					"name" => $skill_id,
					"parent" => $o->id(),
					"caption" => $skill->name(),
					"align" => "center"
				));
				$wpl2skill[$o->id()][$skill_id] = $skill_id;
			}
		}
	}

	function _skg_tbl($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_skg_tbl($t, $wpl2skill, $arr["obj_inst"]);

		$o = obj($arr["obj_inst"]->prop("g_wp"));
		$co = obj($o->prop("owner"));
		$co_i = $co->instance();
		$empl = $co_i->get_employee_picker($co);

		$sel_units = new aw_array($arr["obj_inst"]->prop("g_unit"));
		$sects = $this->get_all_org_sections($co, $t, $empl, $sel_units);
		$t->set_sortable(false);
	}

	function _init_day_g_tbl(&$t, $o)
	{
		$t->define_field(array(
			"name" => "hr",
			"caption" => t("Aeg"),
			"align" => "left"
		));

		$unit_list = new aw_array($o->prop("g_unit"));
		$wpls = array();
		foreach($unit_list->get() as $unit_id)
		{
			if ($this->can("view", $unit_id))
			{
				$uo = obj($unit_id);
				foreach(safe_array($uo->prop("wpls")) as $wpl_id)
				{
					$wpls[$wpl_id] = $wpl_id;
				}
			}

		}
		
		$wpl_list = new object_list(array(
			"class_id" => CL_ROSTERING_WORKPLACE,
			"site_id" => array(),
			"lang_id" => array()
		));
		foreach($wpl_list->arr() as $wpl)
		{
			if (count($wpls) && !$wpls[$wpl->id()])
			{
				continue;
			}
			$t->define_field(array(
				"name" => $wpl->id(),
				"caption" => $wpl->name(),
				"align" => "center",
				"rowspan" => "rowspan".$wpl->id()
			));
		}
	}

	function _day_g_tbl($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_day_g_tbl($t, $arr["obj_inst"]);

		$m = get_instance("applications/rostering/rostering_model");
		$o = obj($arr["obj_inst"]->prop("g_wp"));
		$co = obj($o->prop("owner"));
		$co_i = $co->instance();
		$ppl = $co_i->get_employee_picker($co);

		$wt = array();
		foreach($ppl as $p_id => $p_nm)
		{
			$wts = $m->get_schedule_for_person(obj($p_id), get_week_start(), get_week_start() + 24 * 7 * 3600);
			foreach($wts as $wtm)
			{
				$wtm["person_id"] = $p_id;
				$wt[date("H", $wtm["start"])][] = $wtm;
			}
		}

		$shift_list = new object_list(array(
			"class_id" => CL_ROSTERING_SHIFT,
			"site_id" => array(),
			"lang_id" => array(),
		));
		$shift_list->sort_by(array("prop" => "start_time"));
		foreach($shift_list->arr() as $shift)
		{
			$t->define_data(array(
				"hr" => html::obj_change_url($shift)
			));
			list($endt) = explode(":", $shift->prop("end_time"));
			list($st) = explode(":", $shift->prop("start_time"));
			if ($endt < $st)
			{
				$endt = 24;
			}
			for($i = $st; $i < $endt; $i++)
			{
				$d = array(
					"hr" => sprintf("&nbsp;&nbsp;&nbsp;&nbsp;%02d:00 - %02d:00", $i, $i+1)
				);
				$tmp = $wt[date("H", get_day_start() + $i * 3600)];
				foreach($tmp as $idx => $item)
				{
					$d[$item["workplace"]] = html::obj_change_url($item["person_id"]);
					$rs = 0;
					for($a = $i; $a < $endt; $a++)
					{
						$tmp2 = $wt[date("H", get_day_start() + $a * 3600)];
						foreach($tmp2 as $idx => $tmp_item)
						{
							if ($tmp_item["workplace"] == $item["workplace"] && $tmp_item["person_id"] == $item["person_id"])
							{
								$rs++;
							}
							else
							if ($tmp_item["workplace"] == $item["workplace"] && $tmp_item["person_id"] != $item["person_id"])
							{
								break;
							}
						}
					}
					$d["rowspan".$item["workplace"]] = $rs;
				}
				$t->define_data($d);
				
			}
		}
		$t->set_sortable(false);
	}

	function _init_shifts_g_tbl(&$t)
	{
		$t->define_field(array(
			"name" => "shift",
			"caption" => t("Vahetus"),
			"align" => "left"
		));
		$ws = get_week_start();
		for($i = 0; $i < 7; $i++)
		{
			$t->define_field(array(
				"name" => "d".$i,
				"caption" => date("d.m.Y", $ws + $i * 24 * 3600),
				"align" => "center"
			));
		}
	}

	function _shifts_g_tbl($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_shifts_g_tbl($t);

		$m = get_instance("applications/rostering/rostering_model");
		$o = obj($arr["obj_inst"]->prop("g_wp"));
		$co = obj($o->prop("owner"));
		$co_i = $co->instance();
		$ppl = $co_i->get_employee_picker($co);

		$wt = array();
		foreach($ppl as $p_id => $p_nm)
		{
			$wts = $m->get_schedule_for_person(obj($p_id), get_week_start(), get_week_start() + 24 * 7 * 3600);
			foreach($wts as $wtm)
			{
				$wt[$wtm["workplace"]][date("d.m.Y", $wtm["start"])][$p_id] = $wtm;
			}
		}
		$shift_list = new object_list(array(
			"class_id" => CL_ROSTERING_SHIFT,
			"site_id" => array(),
			"lang_id" => array()
		));
		$wpl2shift = array();
		$start = get_week_start();
		$unit_list = new aw_array($arr["obj_inst"]->prop("g_unit"));
		$wpls = array();
		foreach($unit_list->get() as $unit_id)
		{
			if ($this->can("view", $unit_id))
			{
				$uo = obj($unit_id);
				foreach(safe_array($uo->prop("wpls")) as $wpl_id)
				{
					$wpls[$wpl_id] = $wpl_id;
				}
			}

		}

		foreach($shift_list->arr() as $shift)
		{
			foreach($shift->connections_from(array("type" => "RELTYPE_WORKPLACE")) as $c)	
			{
				$wpl = $c->to();
				$wpl2shift[$wpl->id()][] = $shift;
			}
		}

		foreach($wpl2shift as $wpl_id => $shifts)
		{
			if (count($wpls) && !$wpls[$wpl_id])
			{
				continue;
			}
			$t->define_data(array(
				"shift" => html::obj_change_url($wpl_id)
			));
			foreach($shifts as $shift)
			{
				$d = array(
					"shift" => "&nbsp;&nbsp;&nbsp;&nbsp;".html::obj_change_url($shift)
				);

				for ($i = 0; $i < 7; $i++)
				{
					$tm = date("d.m.Y", $start + $i * 24 * 3600);
					$wpl_data = $wt[$wpl_id][$tm];
					list($p_id) = each($wpl_data);
					if (!$p_id)
					{
						$d["d".$i] = t("Puudu inimene");
					}
					else
					{
						$d["d".$i] = html::obj_change_url($p_id);
					}
				}
				$t->define_data($d);
			}
		}
		$t->set_sortable(false);
	}

	function _shifts_g2_tbl($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_shifts_g_tbl($t);

		$m = get_instance("applications/rostering/rostering_model");
		$o = obj($arr["obj_inst"]->prop("g_wp"));
		$co = obj($o->prop("owner"));
		$co_i = $co->instance();
		$ppl = $co_i->get_employee_picker($co);

		$wt = array();
		foreach($ppl as $p_id => $p_nm)
		{
			$wts = $m->get_schedule_for_person(obj($p_id), get_week_start(), get_week_start() + 24 * 7 * 3600);
			foreach($wts as $wtm)
			{
				$wt[$wtm["workplace"]][date("d.m.Y", $wtm["start"])][$p_id] = $wtm;
			}
		}
		$shift_list = new object_list(array(
			"class_id" => CL_ROSTERING_SHIFT,
			"site_id" => array(),
			"lang_id" => array()
		));
		$wpl2shift = array();
		$start = get_week_start();

		$unit_list = new aw_array($arr["obj_inst"]->prop("g_unit"));
		$wpls = array();
		foreach($unit_list->get() as $unit_id)
		{
			if ($this->can("view", $unit_id))
			{
				$uo = obj($unit_id);
				foreach(safe_array($uo->prop("wpls")) as $wpl_id)
				{
					$wpls[$wpl_id] = $wpl_id;
				}
			}

		}

		foreach($shift_list->arr() as $shift)
		{
			$t->define_data(array(
				"shift" => html::obj_change_url($shift)
			));
			foreach($shift->connections_from(array("type" => "RELTYPE_WORKPLACE")) as $c)
			{
				$wpl = $c->to();
				if (count($wpls) && !$wpls[$wpl->id()])
				{
					continue;
				}
				$d = array(
					"shift" => "&nbsp;&nbsp;&nbsp;&nbsp;".html::obj_change_url($wpl)
				);

				for ($i = 0; $i < 7; $i++)
				{
					$tm = date("d.m.Y", $start + $i * 24 * 3600);
					$wpl_data = $wt[$wpl->id()][$tm];
					list($p_id) = each($wpl_data);
					if (!$p_id)
					{
						$d["d".$i] = t("Puudu inimene");
					}
					else
					{
						$d["d".$i] = html::obj_change_url($p_id);
					}
				}
				$t->define_data($d);
				
			}
		}

		$t->set_sortable(false);
	}


	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_rostering_schedule (aw_oid int primary key, aw_final int, aw_g_start int, aw_g_end int, aw_g_wp int, aw_g_scenario int)");
			return true;
		}

		switch($f)
		{
			case "aw_g_unit":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}

	function get_all_org_sections($obj, &$t, $empl, $units = null)
	{
		static $retval, $level;
		$level++;
		if ($units === null)
		{
			$tmp = $obj->connections_from(array("type" => "RELTYPE_SECTION"));
			$units = array();
			foreach($tmp as $c)
			{
				$units[] = $c->prop("to");
			}
		}
		else
		{
			$units = $units->get();
		}
		foreach ($units as $unit_id)
		{
			$section_obj = obj($unit_id);
			$retval[$obj->id()][] = $section_obj->id();
			$t->define_data(array(
				"empl" => str_repeat("&nbsp;", $level*3).$section_obj->name()
			));

			// get all employees for this
			$workers = $section_obj->get_workers();
			foreach($workers->arr() as $emplo)
			{
				$d = array(
					"empl" => str_repeat("&nbsp;", ($level+1)*3).html::obj_change_url($emplo)
				);
				// read the skills each person has
				foreach($emplo->connections_from(array("type" => "RELTYPE_HAS_SKILL")) as $c)
				{
					$hs = $c->to();
					$d[$hs->prop("skill")] = "x";
				}
				$t->define_data($d);
			}

			$this->get_all_org_sections($section_obj, $t, $empl);

		}
		$level--;
		return $retval;
	}

	function _init_mn_g_t(&$t, $g)
	{
		$t->define_field(array(
			"name" => "num",
			"caption" => "N",
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "p",
			"caption" => t("Isik"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "s",
			"caption" => t("P&auml;devused"),
			"align" => "center"
		));

		$unit_list = new aw_array($g->prop("g_unit"));
		$skill_list = array();
		$wpls = array();
		foreach($unit_list->get() as $unit_id)
		{
			if ($this->can("view", $unit_id))
			{
				$uo = obj($unit_id);
				foreach(safe_array($uo->prop("wpls")) as $wpl_id)
				{
					//$wpls[$wpl_id] = $wpl_id;
					if ($this->can("view", $wpl_id))
					{
						$wplo = obj($wpl_id);
						foreach(safe_array($wplo->prop("skills")) as $skill_id)
						{
							$skill_list[$skill_id] = $skill_id;
						}
					}
				}
			}

		}

		$ol = new object_list(array(
			"class_id" => CL_PERSON_SKILL,
			"lang_id" => array(),
			"site_id" => array(),
			"oid" => $skill_list
		));
		foreach($ol->arr() as $o)
		{
			$t->define_field(array(
				"name" => "skill_".$o->id(),
				"caption" => $o->prop("short_name"),
				"align" => "center",
				"parent" => "s"
			));
		}

		$tm = $g->prop("g_start");
		while ($tm < $g->prop("g_end"))
		{
			$t->define_field(array(
				"name" => "tm".date("d.m.Y", $tm),
				"caption" => sprintf("%02d", date("d", $tm)),
				"align" => "center",
				"parent" => "m"
			));
			$tm += 24 * 3600;
		}
		$t->define_field(array(
			"name" => "m",
			"caption" => date("d.m.Y", $g->prop("g_start"))." - ".date("d.m.Y", $g->prop("g_end")),
			"align" => "center"
		));
	}

	function _mn_g_tbl($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_mn_g_t($t, $arr["obj_inst"]);

		$skills = new object_list(array(
			"class_id" => CL_PERSON_SKILL,
			"lang_id" => array(),
			"site_id" => array()
		));

		$m = get_instance("applications/rostering/rostering_model");		

		$o = obj($arr["obj_inst"]->prop("g_wp"));

		$co = get_instance(CL_CRM_COMPANY);
		$unit_list = new aw_array($arr["obj_inst"]->prop("g_unit"));
		if (count($unit_list->get()))
		{
			$ppl = array();
			foreach($unit_list->get() as $unit_id)
			{
				if ($this->can("view", $unit_id))
				{
					$ppl += $co->get_employee_picker(obj($unit_id));
				}
			}
		}
		else
		{
			$ppl = $co->get_employee_picker(obj($o->prop("owner")));
		}
		foreach($ppl as $p_id => $p_nm)
		{
			$d = array(
				"p" => html::obj_change_url($p_id),
				"num" => ++$num
			);

			$p_obj = obj($p_id);
			$p_skills = $p_obj->connections_from(array(
				"type" => "RELTYPE_HAS_SKILL"
			));
			foreach($p_skills as $c)
			{
				$has_skill = $c->to();
				$d["skill_".$has_skill->prop("skill")] = html::href(array(
					"caption" => "X",
					"url" => html::get_change_url($has_skill->id(), array("return_url" => get_ru()))
				));
			}

			$work_times = $m->get_schedule_for_person($p_obj, $arr["obj_inst"]->prop("g_start"), $arr["obj_inst"]->prop("g_end"));
			foreach($work_times as $wt_item)
			{
				$shift = obj($wt_item["shift"]);
				$d["tm".date("d.m.Y", $wt_item["start"])] = html::href(array(
					"caption" => $shift->prop("short_name"),
					"url" => html::get_change_url($shift->id(), array("return_url" => get_ru()))
				));
			}
			$t->define_data($d);
		}
		$t->set_sortable(false);
	}

	function _get_work_hrs($arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_ROSTERING_WORK_ENTRY,
			"lang_id" => array(),
			"site_id" => array(),
			"graph" => $arr["obj_inst"]->id()
		));
		$arr["prop"]["vcl_inst"]->table_from_ol(
			$ol, 
			array(
				"name"
			), 
			CL_ROSTERING_WORK_ENTRY
		);
	}
}
?>
