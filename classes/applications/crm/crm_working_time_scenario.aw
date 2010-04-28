<?php

namespace automatweb;
/*

@classinfo syslog_type=ST_CRM_WORKING_TIME_SCENARIO relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@default table=objects
@default group=general
@property weekdays type=text store=no no_caption=1
@caption N&auml;dalap&auml;evad

@property days type=text store=no no_caption=1
@caption P&auml;evade plaan

@reltype CURRENCY value=1 clid=CL_CURRENCY
@caption valuuta


*/

class crm_working_time_scenario extends class_base
{
	const AW_CLID = 1392;

	function crm_working_time_scenario()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_working_time_scenario",
			"clid" => CL_CRM_WORKING_TIME_SCENARIO
		));
		$this->days = array(t("Esmasp&auml;ev"),t("Teisip&auml;ev"), t("Kolmap&auml;ev"), t("Neljap&auml;ev"),t("Reede"), t("Laup&auml;ev"), t("P&uuml;hap&auml;ev"));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "weekdays":
				if($arr["new"] || $arr["obj_inst"]->get_free_times())
				{
					return PROP_IGNORE;
				}
				$wd = $arr["obj_inst"]->get_weekdays();
				$ft = $arr["obj_inst"]->get_free_times();
				$time = $arr["obj_inst"]->get_time();
				$prop["value"] = "";
				$prop["value"].= html::checkbox(array(
					"name" => "weekdays[0]",
					"caption" => t("Esmasp&auml;ev"),
					"checked" => $wd[0],
				));
				$prop["value"].= html::checkbox(array(
					"name" => "weekdays[1]",
					"caption" => t("Teisip&auml;ev"),
					"checked" => $wd[1],
				));
				$prop["value"].= html::checkbox(array(
					"name" => "weekdays[2]",
					"caption" => t("Kolmap&auml;ev"),
					"checked" => $wd[2],
				));
				$prop["value"].= html::checkbox(array(
					"name" => "weekdays[3]",
					"caption" => t("Neljap&auml;ev"),
					"checked" => $wd[3],
				));
				$prop["value"].= html::checkbox(array(
					"name" => "weekdays[4]",
					"caption" => t("Reede"),
					"checked" => $wd[4],
				));
				$prop["value"].= html::checkbox(array(
					"name" => "weekdays[5]",
					"caption" => t("Laup&auml;ev"),
					"checked" => $wd[5],
				));
				$prop["value"].= html::checkbox(array(
					"name" => "weekdays[6]",
					"caption" => t("P&uuml;hap&auml;ev"),
					"checked" => $wd[6],
				));
				$prop["value"].= "\n<br>".t("Vabu aegu")." ".html::textbox(array(
					"name" => "free_times",
					"caption" => t("Vabu aegu"),
					"size" => 10,
					"value" => $ft,
				));
				$prop["value"].= "\n<br>".t("Alates")." ".html::time_select(array(
					"name" => "start",
	//				"caption" => t("Vabu aegu"),
//					"size" => 10,
					"value" => $time[0],
				));
				$prop["value"].= "\n<br>".t("Kuni")." ".html::time_select(array(
					"name" => "end",
	//				"caption" => t("Vabu aegu"),
//					"size" => 10,
					"value" => $time[1],
				));
				break;
			case "days":
				if($arr["new"] || !$arr["obj_inst"]->get_free_times())
				{
					return PROP_IGNORE;
				}
				$prop["value"] = $this->get_days_table($arr);
				break;
		}

		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "weekdays":
				if(is_array($arr["request"]["weekdays"]))
				{
					$arr["obj_inst"]->set_weekdays($arr["request"]["weekdays"]);
				}
				if($arr["request"]["free_times"])
				{
					$arr["obj_inst"]->set_free_times($arr["request"]["free_times"]);
				}
				if($arr["request"]["start"])
				{
					$arr["obj_inst"]->set_time(array($arr["request"]["start"] , $arr["request"]["end"]));
				}
				break;
			case "days":
				if($arr["request"]["scenario_data"])
				{
					$arr["obj_inst"]->set_scenario_data($arr["request"]["scenario_data"]);
				}
				break;
		}
		return $retval;
	}
	function callback_mod_reforb($arr)
	{
		$arr["add_bill"] = "";	
		$arr["post_ru"] = post_ru();
	}

	function get_days_table($arr)
	{
		$wd = $arr["obj_inst"]->get_weekdays();
		$ft = $arr["obj_inst"]->get_free_times();
		$time = $arr["obj_inst"]->get_time();
		$counttime = ($time[1]["hour"] - $time[0]["hour"]) * 60 + ($time[1]["minute"] - $time[0]["minute"]);
		$t = new vcl_table;
		$x = 1;

		$t->define_field(array(
			"name" => "day",
			"caption" => t("P&auml;ev"),
		));
		$t->define_field(array(
			"name" => "start",
			"caption" => t("Algus"),
		));
		$t->define_field(array(
			"name" => "end",
			"caption" => t("L&otilde;pp"),
		));
		$t->define_field(array(
			"name" => "pause",
			"caption" => t("Suletud"),
			"width" => 50,
		));
		$t->define_field(array(
			"name" => "pause_reason",
			"caption" => t("Suletud p&otilde;hjus"),
			"width" => 300,
		));

		$scenario_data = $arr["obj_inst"]->get_scenario_data();
		if(!$scenario_data)
		{
			foreach($wd as $day => $data)
			{
				$start = $time[0];
				$t->define_data(array(
					"day" => $this->days[$day],
				));
				$x = 0;
				while($x < $ft)
				{
					$end = $start;
					$end["minute"] = $end["minute"] + $counttime/$ft;
					$t->define_data(array(
						"start" => html::time_select(array(
							"name" => "scenario_data[".$day."][".$x."][start]",
							"value" => $start,
							"minute_step" => 10,
						)),
						"end" => html::time_select(array(
							"name" => "scenario_data[".$day."][".$x."][end]",
							"value" => $end,
							"minute_step" => 10,
						)),
						"pause" => html::checkbox(array(
							"name" => "scenario_data[".$day."][".$x."][is_pause]",
						//	"value" => "",
						)),
						"pause_reason" => html::textbox(array(
							"name" => "scenario_data[".$day."][".$x."][pause_reason]",
						//	"value" => "",
						)),
					));
					$start = $end;
					$x++;
				}
			}
		}
		else
		{
			foreach($scenario_data as $day => $data)
			{
				$t->define_data(array(
					"day" => $this->days[$day],
				));
				$x = 0;
				foreach($data as $stuff)
				{
					$t->define_data(array(
						"start" => html::time_select(array(
							"name" => "scenario_data[".$day."][".$x."][start]",
							"value" => $stuff["start"],
						)),
						"end" => html::time_select(array(
							"name" => "scenario_data[".$day."][".$x."][end]",
							"value" => $stuff["end"],
						)),
						"pause" => html::checkbox(array(
							"name" => "scenario_data[".$day."][".$x."][is_pause]",
							"checked" => $stuff["is_pause"]
						)),
						"pause_reason" => html::textbox(array(
							"name" => "scenario_data[".$day."][".$x."][pause_reason]",
							"value" => $stuff["pause_reason"]
						)),
					));
					$x++;
				}
			}
		}
		return $t->draw();
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

	/**
		@attrib name=make_worker_table params=name all_args=1
	**/
	function make_worker_table($arr)
	{
		$ret = "";
		if(is_oid($arr["person"]) && $this->can("view" , $arr["person"]))
		{
			$p = obj($arr["person"]);
			$ret.= $p->name()."<br>\n";
		}

		if(is_array($_POST["bron_times"]))
		{
			$room_inst = get_instance(CL_ROOM);
			foreach($_POST["bron_times"] as $tmstmp => $data)
			{
				if($data["accept"])
				{
					$start = mktime($data["start"]["hour"], $data["start"]["minute"],0,date("m",$tmstmp),date("d",$tmstmp),date("Y",$tmstmp));
					$end = mktime($data["end"]["hour"], $data["end"]["minute"],0,date("m",$tmstmp),date("d",$tmstmp),date("Y",$tmstmp));
					if($room_inst->check_if_available(array(
						"room" => $_POST["room"],
						"start" => $start,
						"end" => $end,
					)))
					{
						
						$bron = $room_inst->make_reservation(array(
							"not_verified" => 1,
							"id" => $_POST["room"],
							"data" => array(
								"start" => $start,
								"end" => $end,
							),
						));
						if(is_oid($bron))
						{
							$bron_object = obj($bron);
							$bron_object->set_prop("people" , $_POST["person"]);
							if($data["is_pause"])
							{
								$bron_object->set_prop("time_closed" , 1);
								$bron_object->set_prop("closed_info" , $data["pause_reason"]);
							}
							$bron_object->save();
						}
					}
//					print "teeb bronni ajale ".date("d.m.Y h:i" , $start)." - ".date("d.m.Y h:i" , $end)."\n<br>";
				}
			}
			//siin objektide tegemine jne
			die("<script type='text/javascript'>
				window.close();
				</script>
			");
		}

		$scenario = obj($arr["scenario"]);
		$scenario->set_room($arr["room"]);
	
		$scenario_data = $scenario->get_scenario_data();
		if(is_oid($arr["scenario"]) && is_oid($arr["person"]))
		{
			$pers = obj($arr["person"]);
			$pers->set_meta("last_used_working_scenario" , $arr["scenario"]);
			$pers->save();
		}

		$start = date_edit::get_timestamp($arr["start"]);
		$end = date_edit::get_timestamp($arr["end"]);
		classload("core/date/date_calc");
		$week_start = get_week_start($start);

		$weeks = number_format(($end - $week_start) / (24*3600*7) , 0);

		classload("vcl/table");
		$t = new vcl_table(array(
			"layout" => "generic",
		));

		$t->define_field(array(
			"name" => "d0",
			"caption" => t("Esmasp&auml;ev"),
			"valign" => "top",
		));
		$t->define_field(array(
			"name" => "d1",
			"caption" => t("Teisip&auml;ev"),
			"valign" => "top",
		));
		$t->define_field(array(
			"name" => "d2",
			"caption" => t("Kolmap&auml;ev"),
			"valign" => "top",
		));
		$t->define_field(array(
			"name" => "d3",
			"caption" => t("Neljap&auml;ev"),
			"valign" => "top",
		));
		$t->define_field(array(
			"name" => "d4",
			"caption" => t("Reede"),
			"valign" => "top",
		));
		$t->define_field(array(
			"name" => "d5",
			"caption" => t("laup&auml;ev"),
			"valign" => "top",
		));
		$t->define_field(array(
			"name" => "d6",
			"caption" => t("P&uuml;hap&auml;ev"),
			"valign" => "top",
		));

		$s = $week_start;
		while($s < $end)
		{
			$data = array();
			$day = 0;
			while($day < 7)
			{
				if($s < $end && $s >= $start)
				{
					$data["d".$day] = date("d.m.Y" , $s) . "<br>". $scenario->get_date_options($s);
				}
				else
				{
					$data["d".$day] = date("d.m.Y" , $s);
				}
				
				$s = $s + 24*3600;
				$day ++;
			}
			$t->define_data($data);
		}
	
		$form =  html::form(array(
			"method" => "POST",
			"content" => $ret.$t->draw().
				html::hidden(array(
					"name" => "person",
					"value" => $arr["person"],
				)).html::hidden(array(
					"name" => "room",
					"value" => $arr["room"],
				)).
				html::submit(array(
					"value" => t("Moodusta eelbroneeringud"),
				)),
		));

		$sf = new aw_template;
		$sf->db_init();
		$sf->tpl_init("automatweb");
		$sf->read_template("index.tpl");
		
		$sf->vars(array(
			"content" => $form,
			"uid" => aw_global_get("uid"),
			"charset" => aw_global_get("charset"),
			"MINIFY_JS_AND_CSS" => $sf->parse("MINIFY_JS_AND_CSS")
		));

		die($sf->parse());

	}

}

?>
