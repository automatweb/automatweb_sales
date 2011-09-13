<?php
// $Header: /home/cvs/automatweb_dev/classes/workflow/workflow_resource.aw,v 1.6 2008/01/31 13:55:40 kristo Exp $
// workflow_resource.aw - Ressurss 
/*

@classinfo syslog_type=ST_WORKFLOW_RESOURCE relationmgr=yes maintainer=kristo

@default table=objects
@default group=general

@property is_subcontract type=checkbox ch_value=1 field=meta method=serialize
@caption allhange

@groupinfo overview caption="&Uuml;levaade" submit=no

@property overview type=text store=no group=overview no_caption=1 

@groupinfo calendar caption="Kalender" submit=no

@property calendar type=calendar store=no group=calendar no_caption=1 

@reltype EVENT value=1 clid=CL_CRM_MEETING
@caption t&ouml;&ouml;
*/

class workflow_resource extends class_base
{
	function workflow_resource()
	{
		$this->init(array(
			"tpldir" => "workflow/workflow_resource",
			"clid" => CL_WORKFLOW_RESOURCE
		));

		$this->c_p = array(
			"#a0e937",
			"#e9ca37",
			"#1cf31c",
			"#f2ad79",
			"#0ce5dd",
			"#469af1",
			"#d3ccf3",
		);
		reset($this->c_p);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "calendar":
				$this->gen_event_list($arr);
				break;

			case "overview":
				$prop["value"] = $this->get_tx_overview($arr["obj_inst"]);
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

	////
	// !Optionally this also needs to support date range ..
	function gen_event_list($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];

		$t->configure(array(
			"overview_func" => array(&$this,"get_overview"),
		));

		$range = $t->get_range(array(
			"date" => $arr["request"]["date"],
			"viewtype" => $arr["request"]["viewtype"],
		));

		$start = $range["start"];
		$end = $range["end"];
		

		$this->overview = array();

		$lds = $this->get_events_for_resource(array(
			"id" => $arr["obj_inst"]->id(),
		));
		if (sizeof($lds) > 0)
		{
			$ol = new object_list(array(
				"oid" => $lds,
				"sort_by" => "planner.start",
				new object_list_filter(array("non_filter_classes" => CL_CRM_MEETING)),
			));


			$clss = aw_ini_get("classes");
			for($o =& $ol->begin(); !$ol->end(); $o =& $ol->next())
			{
				$clinf = $clss[$o->class_id()];
				$t->add_item(array(
					"timestamp" => $o->prop("start1"),
					"data" => array(
						"name" => " - ".date("H:i", $o->prop("end")).": ".$o->prop("name"),
						"icon" => "", //icons::get_icon_url($o),
						"link" => $this->mk_my_orb("change",array("id" => $o->id()),$clinf["file"]),
					),
				));

				if ($o->prop("start1") > $range["overview_start"])
				{
					$this->overview[$o->prop("start1")] = 1;
				};
			};
		};
	}

	function get_overview($arr = array())
	{
		return $this->overview;
	}
	
	function get_events_for_resource($arr)
	{
		$o = obj($arr["id"]);
		$ret = array();
		foreach($o->connections_from(array("type" => "RELTYPE_EVENT")) as $c)
		{
			$ret[$c->prop("to")] = $c->prop("to");
		}

		return $ret;
	}

	/**

		@comment

			$o = resource object instance
			$length - the length needed 
	**/
	function get_next_avail_time_for_resource($arr)
	{
		$o = $arr["o"];

		// get all events
		$evids = $this->get_events_for_resource(array(
			"id" => $o->id()
		));

		$evs = array();
		foreach($evids as $evid)
		{
			if ($arr["ignore_events"][$evid])
			{
				continue;
			}
			$tmp = obj($evid);
	
			// ignore events with lower priority
			if ($arr["priority"] && $tmp->meta("task_priority") < $arr["priority"])
			{
				continue;
			}

			if ($tmp->prop("start1") > time())
			{
				$evs[$tmp->prop("start1")] = $tmp;
			}
		}

		ksort($evs);

		// search for the first 1h interval
		// how the hell do we do that.
		// hmmm. 
		if (!empty($arr["min_time"]))
		{
			$ts_s = $arr["min_time"];
			$ts_e = $arr["min_time"] + $arr["length"];
		}
		else
		{
			$ts_s = time() + 3600;
			$ts_e = time() + 3600 + $arr["length"];
		}
		
		classload("core/date/date_calc");
		do { 
			// check if there are any events in $ts_s - $ts_e
			$has_events = false;
			foreach($evs as $ev_tm => $ev_o)
			{
				$ev_end_tm = $ev_o->prop("end");
				//echo "ev end tm for event ".$ev_o->id()." = ".date("d.m.Y H:i", $ev_end_tm)." <br>";
				if (timespans_overlap($ev_tm, $ev_end_tm, $ts_s, $ts_e))
				{
					$has_events = true;
					break;
				}
			}

			if ($has_events)
			{
				//echo "checked ".date("d.m.Y H:i", $ts_s)." - ".date("d.m.Y H:i", $ts_e)." found events. <br>";
				// sucks. increment by one minute and try again.
				$ts_s += 60;
				$ts_e += 60;
			}
			else
			{
				//echo "checked ".date("d.m.Y H:i", $ts_s)." - ".date("d.m.Y H:i", $ts_e)." NO events. <br>";
			}

		} while($has_events);

		return $ts_s;
	}

	function get_current_event($o, $ts)
	{
		$evids = $this->get_events_for_resource(array(
			"id" => $o->id()
		));
		foreach($evids as $evid)
		{
			$evo = obj($evid);
			if ($ts >= $evo->prop("start1") && $ts < $evo->prop("end"))
			{
				return $evo;
			}
		}
		return false;
	}

	function get_tx_overview($real_o)
	{
		$this->read_template("overview.tpl");

		// get all resources 
		$resource_list = new object_list();
		$resource_list->add($real_o->id());


		$j_b_r = array();
		for($o = $resource_list->begin(); !$resource_list->end(); $o = $resource_list->next())
		{
			$j_b_r[$o->id()] = array();
			$res_i = $o->instance();
			foreach($res_i->get_events_for_resource(array("id" => $o->id())) as $evid)
			{
				$j_b_r[$o->id()][] = obj($evid);
			}
		}

		$colors = array();

		$res = "";
		$resource_list = new object_list();
		$resource_list->add($real_o->id());

		for($o = $resource_list->begin(); !$resource_list->end(); $o = $resource_list->next())
		{
			$this->vars(array(
				"res_name" => html::href(array(
					"url" => $this->mk_my_orb("change", array("id" => $o->id(), "group" => "calendar"), $o->class_id()),
					"caption" => $o->name(),
				)),
				"r_date" => date("d.m.Y", time())
			));

			$shown = array();
			$hour = "";
			// get events for resource 
			$times = array(); // one entry for each hour
			$res_i = $o->instance();
			$tsp = mktime(0,0,date("m"), date("d"), date("Y"));
			foreach($res_i->get_events_for_resource(array("id" => $o->id())) as $evid)
			{
				$evo = obj($evid);
				if (!isset($colors[$evo->name()]))
				{
					$colors[$evo->name()] = $this->get_rand_color();
				}
				$start = $evo->prop("start1");
				$end = $evo->prop("end");
				$n_hr = (($end - $start) / 3600);
				for($i = 0; $i < $n_hr; $i++)
				{
					$tmp = $start + ($i * 3600);
					// round to the beginning if the hour
					$tmp -= $tmp % 3600;
					$times[$tmp] = $evo;
				}
			}

			for($i = 0; $i < 48; $i++)
			{
				$tsp = time() - (time() % (24*3600)) + ($i*(3600/2));
				$job = $this->get_job_for_time($tsp, $j_b_r[$o->id()]);
				$this->vars(array(
					"time" => date("H:i", $tsp),
					"event" => ($shown[$job] ? "" : $job),
					"color" => ($colors[$job] ? $colors[$job]  : "#FFFFFF"),
				));
				$hour .= $this->parse("HOUR");
				$shown[$job] = true;
			}

			$this->vars(Array(
				"HOUR" => $hour
			));
			$res .= $this->parse("RESOURCE");
		}

		$this->vars(array(
			"RESOURCE" => $res
		));

		return $this->parse();
	}

	function get_rand_color()
	{
		$ret = next($this->c_p);
		if (!$ret)
		{
			$ret = reset($this->c_p);
		}
		return $ret;
	}

	function get_job_for_time($ts, $joblist)
	{
		foreach($joblist as $job)
		{
			if ($ts >= $job->prop("start1") && $ts < $job->prop("end"))
			{
				return $job->name();
			}
		}
		return " - ";
	}
}
?>
