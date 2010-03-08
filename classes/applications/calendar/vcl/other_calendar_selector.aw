<?php
/*
@classinfo maintainer=kristo
*/
class other_calendar_selector extends aw_template
{
	function other_calendar_selector()
	{
		$this->init(array(
			"tpldir" => "calendar",
		));
	}


	function init_vcl_property($arr)
	{
		$event_obj = $arr["obj_inst"];
		$plrlist = $this->_get_calendars_for_event($arr["obj_inst"]);

		$current_start = $event_obj->prop("start1");

		$this->read_template("others.tpl");

		$tt = $tc = "";

		// XXX: get date from url
		$use_date = aw_global_get("date");
		if (empty($use_date))
		{
			$use_date = date("d-m-Y");
		};
		list($d,$m,$y) = explode("-",$use_date);

		$tm = mktime(0,0,0,$m,$d,$y);

		classload("core/date/date_calc");
		$range = get_date_range(array(
			"time" => $tm,
			"type" => "day",
		));

		$this->vars(array(
			"date" => date("d.m Y",$tm),
			"prevlink" => aw_url_change_var("date",$range["prev"]),
			"nextlink" => aw_url_change_var("date",$range["next"]),
		));

		// XXX: arvestada kalendris m22ratud p2eva alguse ja l6pu aegu
		$day_start = mktime(9,0,0,$m,$d,$y);
		$day_end = mktime(21,0,0,$m,$d,$y);

		$step = 60 * 60; // 1. tund

		$first = true;
		$pl = get_instance(CL_PLANNER);
		foreach($plrlist as $pl_id)
		{
			$pl_obj = new object($pl_id);
			$events = $pl->get_event_list(array(
				"id" => $pl_id,
				"start" => $day_start,
				"end" => $day_end,
			));

			$this->vars(array(
				"calendar_name" => $pl_obj->name(),
			));
			$tt .= $this->parse("one_calendar");

			$cells = "";
			for ($ts = $day_start; $ts <= $day_end; $ts = $ts + $step)
			{
				$evstr = "";
				$free = true;
				foreach($events as $event)
				{
					if (between($event["start"],$ts,$ts+$step-1))
					{
						$ev_obj = new object($event["id"]);
						$ev_obj = $ev_obj->get_original();
						if ($ev_obj->class_id() != CL_CALENDAR_VACANCY)
						{
							$evstr .= $ev_obj->name() . "<br>";
							$free = false;
						};
					};
				};

				$this->vars(array(
					"event" => $evstr,
					"time" => date("H:i",$ts),
					"selector_cell" => "",
					"bgcolor" => $free ? "#CCFFCC" : "#FFCCCC",
				));


				if ($first)
				{
					$this->vars(array(
						"time" => date("H:i",$ts),
						"checked" => checked(between($current_start,$ts,$ts+$step-1)),
						"event_sel_id" => $ts,
					));

					$this->vars(array(
						"selector_cell" => $this->parse("selector_cell"),
					));
				}

				$cells .= $this->parse("cell");

			};

			$this->vars(array(
				"cell" => $cells,
			));

			$tc .= $this->parse("one_calendar_content");

			$first = false;
		};
		
		// 

		$this->vars(array(
			"one_calendar" => $tt,
			"one_calendar_content" => $tc,
		));

		$all_props = array();
		$all_props[$arr["prop"]["name"]] = array(
			"type" => "text",
			"name" => $arr["prop"]["name"],
			"value" => $this->parse(),
			"caption" => $arr["prop"]["caption"],
			"no_caption" => $arr["prop"]["no_caption"],
		);

		return $all_props;
	}

	function process_vcl_property($arr)
	{
		$arr["obj_inst"]->set_prop("start1",$arr["request"]["emb"]["start_time"]);
	}

	private function _get_calendars_for_event($o)
	{
		// get participants then get calendars for them
		$conns = $o->connections_to(array("from.class_id" => CL_CRM_PERSON));
		$pl = get_instance(CL_PLANNER);
		$ppl = array();
		foreach($conns as $conn)
		{
			$u = $pl->get_calendar_for_person($conn->from());
			if ($u)
			{
				$cals[] = $u;
			}
		}
		return $cals;
	}
};
?>
