<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_EVENT_WEBVIEW relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=instrumental
@tableinfo aw_event_webview master_index=brother_of master_table=objects index=aw_oid

@default table=objects
@default group=general

@property date_start type=date_select field=meta method=serialize
@caption Alates

@property date_end type=date_select field=meta method=serialize
@caption Kuni

@property events_manager type=relpicker reltype=RELTYPE_EVENTS_MANAGER store=connect
@caption S&uuml;ndmuste halduse keskkond

@property display_by type=chooser field=meta method=serialize
@caption Kuvamine

@property published_only type=checkbox ch_value=1 field=meta method=serialize
@caption Ainult avalikud s&uuml;ndmused

@property order_by_time type=select field=meta method=serialize
@caption J&auml;rjestamine aja j&auml;rgi

@reltype EVENTS_MANAGER value=1 clid=CL_EVENTS_MANAGER
@caption S&uuml;ndmuste halduse keskkond

*/

class event_webview extends class_base
{
	const AW_CLID = 1475;

	function event_webview()
	{
		$this->init(array(
			"tpldir" => "applications/calendar/event_webview",
			"clid" => CL_EVENT_WEBVIEW
		));
		lc_site_load("event_webview", &$this);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "display_by":
				$prop["options"] = array(
					"event" => t("S&uuml;ndmuste kaupa"),
					"event_times" => t("Toimumisaegade kaupa"),
				);
				if(!$prop["value"])
				{
					$prop["value"] = "event_times";
				}
				break;

			case "order_by_time":
				$prop["options"] = array(
					"asc" => t("Vanemad eespool"),
					"desc" => t("Uuemad eespool"),
				);
				$prop["value"] = $prop["value"] ? $prop["value"] : "asc";
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
		}

		return $retval;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}
	
	/**
	@attrib name=show all_args=1
	**/
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");

		// ?date=22-05-2008
		if(!empty($_GET["date"]) && preg_match("/^[0-9]{2}-[0-9]{2}-[0-9]{4}$/", $_GET["date"]) && $_GET["viewtype"] == "day")
		{
			list($dd, $dm, $dy) = explode("-", $_GET["date"]);
			$date_s = mktime(0, 0, 0, $dm, $dd, $dy);
			$date_e = $date_s + 24*3600 - 1;
		}

		if(!$this->can("view", $ob->events_manager))
		{
			return $this->parse();
		}

		$em = obj($ob->events_manager);
		$ol_args = array(
			"class_id" => CL_CALENDAR_EVENT,
			"parent" => $em->event_menu_source,
			"lang_id" => array(),
		);
		if($ob->published_only)
		{
			$ol_args["published"] = 1;
		}

		if(!empty($date_s) && !empty($date_e))
		{
			if($ob->display_by === "event_times")
			{
				$ol_args["CL_CALENDAR_EVENT.RELTYPE_EVENT_TIMES.start"] = new obj_predicate_compare(
					OBJ_COMP_BETWEEN_INCLUDING,
					$date_s,
					$date_e
				);
			}
			else
			{
				$ol_args["CL_CALENDAR_EVENT.start1"] = new obj_predicate_compare(
					OBJ_COMP_BETWEEN_INCLUDING,
					$date_s,
					$date_e
				);
			}
		}
		else
		{
			if($ob->date_start)
			{
				if($ob->display_by === "event_times")
				{
					$ol_args["CL_CALENDAR_EVENT.RELTYPE_EVENT_TIMES.start"] = new obj_predicate_compare(
						OBJ_COMP_LESS,
						$ob->date_end
					);
				}
				else
				{
					$ol_args["CL_CALENDAR_EVENT.start1"] = new obj_predicate_compare(
						OBJ_COMP_LESS,
						$ob->date_end
					);
				}
			}
			if($ob->date_end)
			{
				if($ob->display_by === "event_times")
				{
					$ol_args["CL_CALENDAR_EVENT.RELTYPE_EVENT_TIMES.end"] = new obj_predicate_compare(
						OBJ_COMP_GREATER,
						$ob->date_start
					);
				}
				else
				{
					$ol_args["CL_CALENDAR_EVENT.end"] = new obj_predicate_compare(
						OBJ_COMP_GREATER,
						$ob->date_start
					);
				}
			}
		}

//		$ol_args[] = new obj_predicate_sort(array("start1" => ($ob->order_by_time != "desc" ? "asc" : "desc")));
		$events = new object_list($ol_args);

		$EVENT = "";
		$event_data = array();
		$event_time_ids = array();
		$event_time_match_event = array();

		$oid_props = array("relpicker", "classificator");
		$props = get_instance(CL_CFGFORM)->get_default_proplist(array("clid" => CL_CALENDAR_EVENT));

		if($ob->display_by === "event_times" && $events->count() > 0)
		{
			foreach($events->arr() as $event)
			{
				$event_data[$event->id()] = array();
				foreach($props as $k => $p)
				{
					$v = in_array($p["type"], $oid_props) ? $event->trans_get_val($k.".name") : $event->trans_get_val($k);
					$event_data[$event->id()]["event.".$k] = $v;
				}
				$event_data[$event->id()] = array_merge($event_data[$event->id()], array(
					"event.start1" => date("d-m-Y H:i:s", $event->start1),
					"event.start1.date" => get_lc_date($event->start1, LC_DATE_FORMAT_LONG_FULLYEAR),
					"event.start1.time" => date("H:i", $event->start1),
					"event.end" => date("d-m-Y H:i:s", $event->end),
					"event.end.date" => get_lc_date($event->end, LC_DATE_FORMAT_LONG_FULLYEAR),
					"event.end.time" => date("H:i", $event->end),
					"event.AWurl" => obj_link($event->id()),
				));

				foreach($event->connections_from(array("type" => "RELTYPE_EVENT_TIMES")) as $conn)
				{
					$event_time_ids[] = $conn->prop("to");
					$event_time_match_event[$conn->prop("to")] = $conn->prop("from");
				}
			}

			if(count($event_time_ids) > 0)
			{
				$event_times = new object_data_list(array(
					"class_id" => CL_EVENT_TIME,
					"parent" => array(),
					"site_id" => array(),
					"lang_id" => array(),
					"status" => array(),
					"oid" => $event_time_ids,
					new obj_predicate_sort(array("start" => ($ob->order_by_time != "desc" ? "asc" : "desc"))),
				),
				array
				(
					CL_EVENT_TIME => array("oid", "start", "end"),
				));

				foreach($event_times->arr() as $time)
				{
					$to = obj($time["oid"]);
					// The url parameter
					if(!empty($date_s) && !empty($date_e) && ($date_s > $to->start || $date_e < $to->start))
					{
						continue;
					}
					// Settings (considered only id the url parameter is not given)
					if(($ob->date_start && $ob->date_start > $to->end || $ob->date_end && $ob->date_end < $to->start) && (empty($date_s) || empty($date_e)))
					{
						continue;
					}
					$event_data[$event_time_match_event[$to->id()]] = array_merge($event_data[$event_time_match_event[$to->id()]], array(
						"event.start1" => date("d-m-Y H:i:s", $to->start),
						"event.start1.date" => get_lc_date($to->start, LC_DATE_FORMAT_LONG_FULLYEAR),
						"event.start1.time" => date("H:i", $to->start),
						"event.end" => date("d-m-Y H:i:s", $to->end),
						"event.end.date" => get_lc_date($to->end, LC_DATE_FORMAT_LONG_FULLYEAR),
						"event.end.time" => date("H:i", $to->end),
						"event.location" => $to->trans_get_val("location.name"),
						"event.AWurl" => obj_link($event_time_match_event[$to->id()])."?event_time=".$to->id(),
					));
					$this->vars($event_data[$event_time_match_event[$to->id()]]);
					$EVENT .= $this->parse("EVENT");
				}
			}
		}
		else
		{
			foreach($events->arr() as $event)
			{
				$event_data[$event->id()] = array();
				foreach($props as $k => $p)
				{
					$v = in_array($p["type"], $oid_props) ? $event->trans_get_val($k.".name") : $event->trans_get_val($k);
					$this->vars(array(
						"event.".$k => $v,
					));
				}
				$this->vars(array(
					"event.start1" => date("d-m-Y H:i:s", $event->start1),
					"event.start1.date" => get_lc_date($event->start1, LC_DATE_FORMAT_LONG_FULLYEAR),
					"event.start1.time" => date("H:i", $event->start1),
					"event.end" => date("d-m-Y H:i:s", $event->end),
					"event.end.date" => get_lc_date($event->end, LC_DATE_FORMAT_LONG_FULLYEAR),
					"event.end.time" => date("H:i", $event->end),
					"event.AWurl" => obj_link($event->id()),
				));
			}
			$EVENT .= $this->parse("EVENT");
		}
		
		$this->vars(array(
			"EVENT" => $EVENT,
		));
		$this->vars(array(
			"EVENTS" => $this->parse("EVENTS"),
		));

		return $this->parse();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_event_webview(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => ""
				));
				return true;
		}
	}
}

?>
