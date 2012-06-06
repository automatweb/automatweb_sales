<?php
/*
// so what does this class do? Simpel answer - it allows us to choose different templates
// for showing calendars.

// also, all view related functions from CL_PLANNER will move over here

@classinfo relationmgr=yes no_status=1

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property use_template type=select
@caption V&auml;limus

@property num_next_events type=textbox size=5
@caption Mitu j&auml;rgmist

@property default_view type=select
@caption Vaade

@property search_form type=relpicker reltype=RELTYPE_SEARCH
@caption Otsinguvorm

@property sub_event_template type=textbox size=5
@caption S&uuml;ndmuse kujundusp&otilde;hi

@property week_event_template type=textbox size=5
@caption S&uuml;ndmuse n&auml;dala kujundusp&otilde;hi

@property default_urlobj type=relpicker reltype=RELTYPE_DEFAULT_URLOBJ store=connect
@caption Default lingi objekt

@property use_event_time_objs type=checkbox ch_value=1
@caption Kasuta toimumisaja objekte

@property show_days_with_events type=checkbox ch_value=1
@caption N&auml;ita ainult s&uuml;ndmustega p&auml;evi

@property sort_day_events_before type=checkbox ch_value=1
@caption Sorteeri &uuml;he p&auml;evased ettepoole

@property show_event_content type=checkbox ch_value=1
@caption N&auml;ita kalendrivaates kohe s&uuml;ndmuse sisu

@property show_event_days type=checkbox ch_value=1
@caption N&auml;ita s&uuml;ndmust k&otilde;ikidel p&auml;evadel

@property actives_only type=checkbox ch_value=1
@caption N&auml;ita ainult aktiivseid s&uuml;ndmusi

@property show_active_trans type=checkbox ch_value=1
@caption N&auml;ita mitteaktiivseid s&uuml;ndmusi, mille t&otilde;lge on aktiivne

@property fix_links type=checkbox ch_value=1
@caption "Eelmine"-"J&auml;rgmine" lingid viivad s&uuml;ndmusega p&auml;evale

@property monyear_2in1 type=checkbox ch_value=1
@caption Kuu ja aasta &uuml;he selecti sees

@groupinfo style caption=Stiilid
@default group=style

@property minical_day_with_events type=relpicker reltype=RELTYPE_STYLE
@caption S&uuml;ndmustega p&auml;ev

@property minical_day_without_events type=relpicker reltype=RELTYPE_STYLE
@caption Ilma s&uuml;ndmusteta p&auml;ev

@property minical_day_today type=relpicker reltype=RELTYPE_STYLE
@caption T&auml;nane p&auml;ev

@property minical_day_active type=relpicker reltype=RELTYPE_STYLE
@caption Aktiivne p&auml;ev

@property minical_day_deactive type=relpicker reltype=RELTYPE_STYLE
@caption Deaktiivne p&auml;ev

@property minical_title type=relpicker reltype=RELTYPE_STYLE
@caption Pealkiri

@property minical_background type=relpicker reltype=RELTYPE_STYLE
@caption Taust

@property table_header type=relpicker reltype=RELTYPE_STYLE
@caption Tulemuste tabeli pealkiri

@property table_frow type=relpicker reltype=RELTYPE_STYLE
@caption Tulemuste tabeli esimene rida

@property table_srow type=relpicker reltype=RELTYPE_STYLE
@caption Tulemuste tabeli teine rida

@groupinfo ftresults caption="Tulemuste seadistamine"
@default group=ftresults

@property month_navigator type=checkbox ch_value=1 default=1
@caption Kuu navigaator

@property result_table type=table
@caption Tulemuste tabel

@groupinfo show_events caption=S&uuml;ndmused submit=no
@default group=show_events

@property show_events type=calendar no_caption=1
@caption S&uuml;ndmused

@reltype EVENT_SOURCE value=1 clid=CL_PLANNER,CL_DOCUMENT_ARCHIVE,CL_PROJECT
@caption V&otilde;ta s&uuml;ndmusi

@reltype OUTPUT value=2 clid=CL_RELATION,CL_DOCUMENT
@caption v&auml;ljund

@reltype STYLE value=3 clid=CL_CSS
@caption Stiil

@reltype SEARCH value=4 clid=CL_EVENT_SEARCH
@caption Otsing

@reltype EVENT_VALIDATOR value=5 clid=CL_FORM_CONTROLLER
@caption S&uuml;ndmuste n&auml;itamise kontroller

# Feel free to add class_id's here!
@reltype DEFAULT_URLOBJ value=6 clid=CL_DOCUMENT,CL_MENU
@caption Default lingi objekt
*/


class calendar_view extends class_base
{
	function calendar_view()
	{
		$this->init(array(
			"tpldir" => "calendar/calendar_view",
			"clid" => CL_CALENDAR_VIEW
		));
		$this->event_entry_classes = array(CL_CALENDAR_EVENT,CL_STAGING,CL_CRM_MEETING,CL_TASK,CL_CRM_CALL,CL_PARTY,CL_COMICS);
		lc_site_load("calendar_view", $this);
	}

	function get_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "use_template":
				$data["options"] = array(
					"intranet1.tpl" => t("Kuuvaade & n&auml;dala s&uuml;ndmused"),
					"month" => t("Kuukalender"),
					"futureevents" => t("Algavad s&uuml;ndmused"),
					"weekview" => t("N&auml;dala vaade"),
					"last_events" => t("J&auml;rgmised s&uuml;ndmused"),
					"grouped" => t("Grupeeri allika j&auml;rgi"),
					"relative" => t("&Uuml;levaade"),
				);
				break;

			case "show_events":
				$this->gen_calendar_contents($arr);
				break;

			case "default_view":
				$data["options"] = array(
					"" => "",
					"day" => t("p&auml;ev"),
					"week" => t("n&auml;dal"),
					"month" => t("kuu"),
					"last" => t("J&auml;rgmised"),
					"relative" => t("&Uuml;levaade"),
				);
				break;

			case "num_next_events":
				if ($arr["obj_inst"]->prop("use_template") != "last_events" && $arr["obj_inst"]->prop("use_template") != "relative")
				{
					return PROP_IGNORE;
				}
				break;
			case "result_table":
				$retval = $this->gen_result_table($arr);
				break;
		}
		return $retval;
	}

	// now to the meat .. I have to generate a list of events from our sources
	// but first I have to get the calendar to work

	function set_property($arr = array())
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "result_table":
				$arr["obj_inst"]->set_meta("result_table", $arr["request"]["result_table"]);
				break;
		}
		return $retval;
	}

	function gen_result_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$o = $arr["obj_inst"];
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));

		$t->define_field(array(
			"name" => "caption",
			"caption" => t("Pealkiri"),
		));

		$t->define_field(array(
			"name" => "active",
			"caption" => t("Aktiivne"),
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "ord",
			"caption" => t("Jrk"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "props",
			"caption" => t("Seaded"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "sep",
			"caption" => t("V&auml;ljade eraldaja"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "fields",
			"caption" => t("Lisav&auml;ljad"),
		));

		$oldvals = $o->meta("result_table");

		$tc = get_instance(CL_CFGFORM);
		$cform_obj = new object($this->cfgform_id);
		$use_output = $cform_obj->prop("use_output");

		$prop_output = $arr["obj_inst"]->prop("use_output");
		if(is_oid($prop_output))
		{
			$use_output = $prop_output;
		}
		elseif (!is_oid($use_output))
		{
			$arr["prop"]["error"] = t("V&auml;ljundvorm on valimata");
			return PROP_ERROR;
		}

		$pname = $arr["prop"]["name"];


		$props = $tc->get_props_from_cfgform(array("id" => $use_output));

		$props["name"]["name"] = "name";
		$names = array();
		foreach($props as $prz)
		{
			$names[$prz["name"]] = $prz["name"];
		}
		foreach($props as $prop)
		{
			$sname = $prop["name"];
			$prps = array(
				"caption" => html::textbox(array(
					"name" => "${pname}[${sname}][caption]",
					"value" => empty($oldvals[$sname]["caption"]) ? $prop["caption"] : $oldvals[$sname]["caption"],
					"size" => 20,
				)),
				"sep" => html::textbox(array(
					"name" => "${pname}[${sname}][sep]",
					"value" => $oldvals[$sname]["sep"],
					"size" => 2,
				)),
				"name" => $prop["name"],
				"active" => html::checkbox(array(
					"name" => "${pname}[${sname}][active]",
					"value" => 1,
					"checked" => ($oldvals[$sname]["active"] == 1),
				)),
				"ord" => html::textbox(array(
					"name" => "${pname}[${sname}][ord]",
					"value" => $oldvals[$sname]["ord"],
					"size" => 2,
				)),
			);
			if($prop["type"] == "date_select" || $prop["type"] == "datetime_select")
			{
				$prps["props"] = html::textbox(array(
					"name" => "${pname}[${sname}][props]",
					"value" => $oldvals[$sname]["props"],
					"size" => 15,
				));
			}
			$nums = count($oldvals[$sname]["fields"]);
			foreach(safe_array($oldvals[$sname]["fields"]) as $k => $v)
			{
				if(empty($v))
				{
					$nums--;
				}
			}
			for($i = 0; $i <= $nums; $i++)
			{
				$prps["fields"] .= html::select(array(
					"name" => "${pname}[${sname}][fields][$i]",
					"options" => array(0 => "-- vali --") + $names,
					"value" => $oldvals[$sname]["fields"][$i],
				))."<br />";
			}
			$t->define_data($prps);
		}
		$t->set_sortable(false);
	}

	function gen_calendar_contents($arr)
	{
		$args = array();

		$arr["prop"]["vcl_inst"]->configure(array(
			//"tasklist_func" => array(&$this,"get_tasklist"),
			"overview_func" => array($this,"get_overview"),
			"overview_range" => 1,
			"full_weeks" => 1,
		));
		$range = $arr["prop"]["vcl_inst"]->get_range(array(
			"date" => $arr["request"]["date"],
			"viewtype" => $arr["request"]["viewtype"] ? $arr["request"]["viewtype"] : $viewtype,
		));
		$this->obj_inst = $arr["obj_inst"];
		$this->_export_events(array(
			"obj_inst" => $this->obj_inst,
			"cal_inst" => $arr["prop"]["vcl_inst"],
			"range" => $range,
		));
	}

	function _get_output_doc($obj)
	{
		$out_conns = $obj->connections_from(array(
			"type" => "RELTYPE_OUTPUT",
		));

		$target_doc = false;

		if (sizeof($out_conns) > 0)
		{
			$first = reset($out_conns);
			$al_id = $first->prop("to");
			$al_obj = new object($al_id);
			if ($al_obj->class_id() != CL_RELATION)
			{
				$target_doc = $al_obj->id();
			}
			else
			{
				// XXX: how do accomplish this with storage?
				$q = "SELECT source,target FROM aliases WHERE relobj_id = '$al_id'";
				$row = $this->db_fetch_row($q);
				$target_doc = $row["source"];
			}
		}

		return $target_doc;

	}

	function get_overview($arr)
	{

		$target_doc = $this->target_doc;
		// now for each of those bloody things I need to figure out the date range as well
		$conns = $this->obj_inst->connections_from(array(
			"type" => "RELTYPE_EVENT_SOURCE",
		));

		$overview = array();

		$item = array();
		/*
		if (!empty($target_doc))
		{
			$item["url"] = aw_ini_get("baseurl") . "/" . $target_doc;
		}
		*/


		foreach ($conns as $conn)
		{
			$to_o = $conn->to();
			// so, how do I get events from that calendar now?
			// no matter HOW, that function needs to accept range arguments
			$clid = $to_o->class_id();
			if ($clid == CL_PLANNER)
			{
				$pl = get_instance(CL_PLANNER);
				$overview = $pl->get_event_list(array(
					"id" => $to_o->id(),
					"start" => $arr["start"],
					"end" => $arr["end"],
				));
			}

			if ($clid == CL_DOCUMENT_ARCHIVE)
			{
				$da = get_instance(CL_DOCUMENT_ARCHIVE);
				$overview = $da->get_days_with_events(array(
					"id" => $to_o->id(),
					"start" => $arr["start"],
					"end" => $arr["end"],
				));
			}

			if ($clid == CL_PROJECT)
			{
				$pr = get_instance(CL_PROJECT);
				$overview = $pr->get_event_overview(array(
					"id" => $to_o->id(),
					"start" => $arr["start"],
					"end" => $arr["end"],
				));


			}
			foreach($overview as $event)
			{
				if($this->can("view", $this->obj_inst->default_urlobj))
				{
					$url_data = parse_url(urldecode($this->vars["current_url"]));
					$item["url"] = aw_url_change_var(array("viewtype" => "day"), false, obj_link($this->obj_inst->default_urlobj)."?".$url_data["query"]);
				}
				$item["timestamp"] = $event["start"];
				$item["event_oid"] = $event["id"];
				//$item["url"] = $event["url"];
				if (!empty($item["url"]))
				{
					$item["url"] = aw_url_change_var("date",date("d-m-Y",$event["start"]),$item["url"]);
				}
				if($this->obj_inst->use_event_time_objs)
				{
					foreach(connection::find(array("from" => $event["id"], "from.class_id" => CL_CALENDAR_EVENT, "type" => "RELTYPE_EVENT_TIMES")) as $et_conn)
					{
						$event_time_obj = obj($et_conn["to"]);

						if (!empty($item["url"]))
						{
							$item["url"] = aw_url_change_var("date",date("d-m-Y",$event_time_obj->start), $item["url"]);
						}

						$tmp = $item;
						$tmp["start"] = $tmp["timestamp"] = get_day_start($event_time_obj->start);
						$tmp["end"] = $event_time_obj->end;
						$rv[$tmp["start"]] = $tmp;

						// Event time can also be on several days.
						if ($event_time_obj->end > $event_time_obj->start && date("dmY", $event_time_obj->start) != date("dmY", $event_time_obj->end))
						{
							for ($i = $tmp["timestamp"] + 86400; $i <= $event_time_obj->end; $i = $i + 86400)
							{
								$tmp = $item;
								$tmp["start"] = $tmp["timestamp"] = $i;
								$tmp["end"] = $event_time_obj->end;
								$rv[$i] = $tmp;
							}
						}
					}
				}
				else
				{
					$rv[$event["start"]] = $item;
					if ($event["end"] > $event["start"] && date("dmY", $event["start"]) != date("dmY", $event["end"]))
					{
						$item["timestamp"] = get_day_start($item["timestamp"]) + 86400;
						for ($i = $item["timestamp"]; $i <= $event["end"]; $i = $i + 86400)
						{
							if (!empty($item["url"]))
							{
								$item["url"] = aw_url_change_var("date",date("d-m-Y",$tmp["timestamp"]), $item["url"]);
							}
							$tmp = $item;
							$tmp["start"] = $tmp["timestamp"] = $i;
							$rv[$i] = $tmp;
						}
					}
				}
			}
		}
		// now, I need a document which will carry the news articles
		return $rv;
	}

	////
	// !Scans events source connections and exports them to the calendar component
	function _export_events($arr)
	{
		$multi_e = $arr["obj_inst"]->prop("show_event_days");
		// alright .. this function needs to accept an object id from which to ask events
		$range = $arr["range"];
		$arr["cal_inst"]->vars_safe($this->vars);
		if (is_oid($arr["oid"]))
		{
			$obj = new object($arr["oid"]);
			$cal_inst = $arr["cal_inst"];
			$first_image = $cal_inst->has_feature("first_image");
			$project_media = $cal_inst->has_feature("project_media");
			$events = $this->get_events_from_object(array(
				"obj_inst" => $obj,
				"range" => $range,
				"status" => $arr["status"],
				"first_image" => $first_image,
				"project_media" => $project_media,
			));
			foreach($events as $event)
			{
				$data = $event;
				$evt_obj = new object($event["id"]);
				//$data = $evt_obj->properties() + $data;
				$data = $data + $evt_obj->properties();
				$data["icon"] = "event_icon_url";
				$varx = array(
					"item_start" => $event["start"],
					"timestamp" => $event["start"],
					"data" => $data,
				);
				if(!empty($multi_e))
				{
					$varx["item_end"] = $event["end"];
				}
				$arr["cal_inst"]->add_item($varx);
			}

		}
		else
		{
			$conns = $arr["obj_inst"]->connections_from(array(
				"type" => "RELTYPE_EVENT_SOURCE",
			));
			$cal_inst = $arr["cal_inst"];
			$first_image = $cal_inst->has_feature("first_image");
			$project_media = $cal_inst->has_feature("project_media");

			$event_validator = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_EVENT_VALIDATOR");
			$done_oids = array();
			$langid = aw_ini_get("user_interface.full_content_trans") ? aw_global_get("ct_lang_id") : aw_global_get("lang_id");
			foreach ($conns as $conn)
			{
				$to_o = $conn->to();
				//$to_o->properties();
				$events = $this->get_events_from_object(array(
					"obj_inst" => $to_o,
					"range" => $range,
					"first_image" => $first_image,
					"project_media" => $project_media,
					"status" => $arr["status"],
					"show_active_trans" => $arr["show_active_trans"],
				));
				foreach($events as $event)
				{
					$data = $event;
					$evt_obj = new object($event["id"]);
					if (isset($done_oids[$evt_obj->brother_of()]))
					{
						continue;
					}
					$done_oids[$evt_obj->brother_of()] = 1;

				//	$data = $evt_obj->properties() + $data;
				//	$data = $data + $evt_obj->properties();
					if($evt_obj->lang_id() != $langid)
					{
						foreach(array_keys($evt_obj->properties()) as $p)
						{
							$data[$p] = $evt_obj->trans_get_val($p);
						}
					}
					else
					{
						$data = $data + $evt_obj->properties();
					}
					$data["name"] = $evt_obj->trans_get_val("name");
					$data["icon"] = "event_icon_url";
					$varx = array(
						"item_start" => $event["start"],
						"timestamp" => $event["start"],
						"data" => $data,
					);
					if(!empty($multi_e))
					{
						$varx["item_end"] = $event["end"];
					}

					if ($event_validator)
					{
						$i = $event_validator->instance();
						if (!$i->eval_controller($event_validator->id(), $evt_obj))
						{
							continue;
						}
					}
					$arr["cal_inst"]->add_item($varx);
				}

			}
		}
	}

	function __sort_events_by_time($el1, $el2)
	{
		if($this->day_start > $el1["start1"] && $this->day_start < $el2["start1"]) return 1;
		if($this->day_start < $el1["start1"] && $this->day_start > $el2["start1"]) return -1;
		return (int)($el1["start1"] - $el2["start1"]);
	}

	function __sort_events_by_jrk($el1, $el2)
	{
		if(!($this->can("view" , $el1) && $this->can("view" , $el2)))
		{
			return $el1 - $el2;
		}

		if(aw_global_get("uid") == "kix"){

		$e1 = obj($el1);
		$e2 = obj($el2);
		$e1_len = $e1->prop("end") - $e1->prop("start1");
		$e2_len = $e2->prop("end") - $e2->prop("start1");
		arr($e2->prop("end"));arr($e2->prop("start1"));arr($e2);
		if($e1_len > 86400 && $e2_len <= 86400) return -1;
		if($e1_len <= 86400 && $e2_len > 86400) return 1;
		if($e1_len <= 86400 && $e2_len <= 86400)
		{
			return $e1->prop("start1") - $e2->prop("start1");
		}

		$p1 = obj($e1->parent());
		$p2 = obj($e2->parent());
		return $p1->prop("jrk") - $p2->prop("jrk");

		//arr($day);
// 		if()
//
// 		if()
		}


		return (int)($el1 - $el2);
	}

	// common interface for getting events out of any class that can contain events
	// probably should not even be in this class
	function get_events_from_object($arr)
	{
		$events = array();
		$o = $arr["obj_inst"];
		$range = $arr["range"];
		$clid = $o->class_id();
		switch($clid)
		{
			case CL_PLANNER:
				$pl = get_instance("applications/calendar/planner_model");
				$events = $pl->_init_event_source(array(
					"id" => $o->id(),
					"type" => $range["viewtype"],
					"flatlist" => 1,
					"date" => date("d-m-Y",$range["timestamp"]),
					"range" => $range,
					"status" => $arr["status"],
					"show_active_trans" => $arr["show_active_trans"],
				));
				if ($range["viewtype"] == "last_events")
				{
					foreach($events as $key => $val)
					{
						if ($val["start"] < $range["start"])
						{
							unset($events[$key]);
						}
					}
				}
				$this->day_start = $arr["range"]["start"];
			//	if(aw_global_get("uid") == "kix") {arr($events);}
				uasort($events, array($this, "__sort_events_by_time"));
			//	if(aw_global_get("uid") == "kix") {arr($events);arr($num);}
				if (is_numeric($range["limit_events"]))
				{
					$num = $range["limit_events"];
					$count = count($events);
					// this does not work, because PLANNER does not yet have a method
					// to returns events from a specified range
					$events = array_slice($events, 0, $num);
				}
				break;

			case CL_DOCUMENT_ARCHIVE:
				$da = get_instance(CL_DOCUMENT_ARCHIVE);
				$events = $da->get_events(array(
					"id" => $o->id(),
					"range" => $range,
					"status" => $arr["status"],
				));
				break;

			case CL_PROJECT:
				$pr = get_instance(CL_PROJECT);
				$events = $pr->get_events(array(
					"id" => $o->id(),
					"range" => $range,
					"status" => $arr["status"],
					"first_image" => $arr["first_image"],
					"project_media" => $arr["project_media"],
				));
				break;
		}

		return $events;
	}

	function get_first_event($arr)
	{
		extract($arr);
		if(!$sources)
		{
			$sources = $this->get_event_sources($o);
		}
		$obj = new object_list(array(
			"parent" => $sources,
			"class_id" => $this->event_entry_classes,
			//new object_list_filter(array("non_filter_classes" => CL_CALENDAR_EVENT)),
			//"brother_of" => new obj_predicate_prop("id"),
			"status" => $status,
			"start1" => new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, time()),
			"sort_by" => "planner.start DESC",
			"limit" => 1,
		));
		return $obj->begin();
	}

	function get_event_sources($o)
	{
		$sources = array();
		switch($o->class_id())
		{
			case CL_PLANNER:
				$pl = get_instance("applications/calendar/planner_model");
				$sources = $pl->get_event_sources($o->id());
				break;

			case CL_DOCUMENT_ARCHIVE:
				$da = get_instance(CL_DOCUMENT_ARCHIVE);
				$sources = $da->get_event_sources($o->id());
				break;

			case CL_PROJECT:
				$pr = get_instance(CL_PROJECT);
				$sources = $pr->get_event_sources($o->id());
				break;
		}
		return $sources;
	}

	////
	//!
	function parse_alias($arr = array())
	{
		if ($arr["obj_inst"])
		{
			$this->obj_inst = $arr["obj_inst"];
		}
		else
		{
			$this->obj_inst = new object($arr["alias"]["target"]);
		}

		if ($this->obj_inst)
		{
			$this->target_doc = $this->_get_output_doc($this->obj_inst);
		}

		// figure out correct tpldir
		$tpldir = "calendar/calendar_view";
		$use_template = isset($arr["use_template"]) ? $arr["use_template"] : $this->obj_inst->prop("use_template");

		$use2dir = array(
			"month" => "calendar/calendar_view/month",
			"weekview" => "calendar/calendar_view/week",
			"year" => "calendar/calendar_view/year",
			"day" => "calendar/calendar_view/day",
			"last_events" => "calendar/calendar_view/last_events",
			// BUG: this should be possible in ALL views
			"grouped" => "calendar/calendar_view/day",
			"relative" => "calendar/calendar_view/relative"
		);

		if ($use2dir[$use_template])
		{
			$tpldir = $use2dir[$use_template];
		}

		// oookey .. I need a way to query the calendar whether it has to show images?
		$vcal = new vcalendar(array(
			"tpldir" => $tpldir,
		));

		if ($arr["event_template"])
		{
			$args["event_template"] = $arr["event_template"];
		}

		$args = array(
			"container_template" => "intranet1.tpl",
		);

		if ("futureevents" != $use_template)
		{
			$args["overview_func"] = array($this,"get_overview");
			$args["overview_range"] = 1;
		}
		if($use_template == "relative")
		{
			$args["overview_range"] = 3;
		}


		if (1 == $this->obj_inst->prop("show_days_with_events"))
		{
			$args["show_days_with_events"] = 1;
		}

		if ($arr["skip_empty"])
		{
			$args["skip_empty"] = $arr["skip_empty"];
		}

		// this parse_alias is also being invoked directly from the project class,
		// without an alias anywhere. I want to show full months if this is an alias
		// and this it's done

		if (!empty($arr["matches"]))
		{
			$arr["full_weeks"] = 1;
		}

		if ($arr["full_weeks"])
		{
			$args["full_weeks"] = $arr["full_weeks"];
		}

		if (is_oid($this->target_doc))
		{
			$args["target_section"] = $this->target_doc;
		}
		else
		{
			$args["target_section"] = aw_global_get("section");
		}
		$status = array(object::STAT_ACTIVE, object::STAT_NOTACTIVE);
		if($this->obj_inst->prop("actives_only") == 1)
		{
			$status = array(object::STAT_ACTIVE);
			$args["active_only"] = 1;
		}
		if($this->obj_inst->prop("fix_links") == 1)
		{
			$args["fix_links"] = 1;
		}
		$vcal->obj_status = $status;
		$vcal->configure($args);


		$viewtype = $this->obj_inst->prop("default_view");
		if ($viewtype == "")
		{
			$viewtype = "week";
		}
		if ($arr["viewtype"])
		{
			$viewtype = $arr["viewtype"];
		}

		if ($_GET["viewtype"])
		{
			$viewtype = $_GET["viewtype"];
			if ($use2dir[$viewtype])
			{
				$tpldir = $use2dir[$viewtype];
			}
		}

		$text = "";
		if (is_oid($_GET["event_id"]) && $this->can("view",$_GET["event_id"]))
		{
			$o = obj($_GET["event_id"]);
			$i = $o->instance();
			$text = $i->request_execute($o);
			$viewtype = "day";
		}

		$range_p = array(
			"viewtype" => $viewtype,
			"date" => aw_global_get("date"),
		);
		$range_p["limit_events"] = $vcal->limit_events = $this->obj_inst->prop("num_next_events");
		if ($use_template == "last_events")
		{
			$range_p["viewtype"] = "last_events";
			$range_p["type"] = "last_events";
		}
		$conns = $this->obj_inst->connections_from(array(
			"type" => "RELTYPE_EVENT_SOURCE",
		));
		$range = $vcal->get_range($range_p);
		if($this->obj_inst->prop("show_event_content") == 1)
		{
			$ec = array();
			foreach($conns as $conn)
			{
				$to_o = $conn->to();
				$sources = $this->get_event_sources($to_o);
				if($_GET["date"] || $range_p["limit_events"] > 1)
				{
					$events = $this->get_events_from_object(array(
						"obj_inst" => $to_o,
						"range" => $range,
						"status" => $status,
					));
					if($first = reset($events))
					{
						$first = obj($first["id"]);
						$start1 = $first->prop("start1");
					}
				}
				else
				{
					$first = $this->get_first_event(array(
						"obj" => $to_o,
						"sources" => $sources,
						"status" => $status,
					));
					if ($first)
					{
						$f = $first->properties() + array("id" => $first->id());
						$events = array($f);
						$start1 = $first->prop("start1");
					}
				}
				$vcal->items = array();
				foreach($events as $event)
				{
					if(!$vcal->first_event)
					{
						$vcal->first_event = $event;
					}
					$event = obj($event["id"]);
					$i = $event->instance();
					$text .= $i->request_execute($event);
				}
				if ($event)
				{
					$vcal->last_event = $event->properties() + array("id" => $event->id());
				}
			}
			$viewtype = "day";
			$this->read_template($use_template."/intranet1.tpl");
			if($this->is_template("RANDOM"))
			{
				$objs = new object_list(array(
					"parent" => $sources,
					"class_id" => $this->event_entry_classes,
					"brother_of" => new obj_predicate_prop("id"),
					//"oid" => new obj_predicate_not($first->id()),
					"status" => $status,
					"limit" => 10,
				));
				$ids = $objs->ids();
				shuffle($ids);
				foreach($ids as $id)
				{
					$obj = obj($id);
					$this->vars_safe(array(
						"link" => aw_url_change_var(array("date" => date("d-m-Y", $obj->prop("start1")))),
					));
					$random .= $this->parse("RANDOM");
				}
				$vcal->random = $random;
			}
			if($this->is_template("YEARS"))
			{
				$objs1 = new object_list(array(
					"parent" => $sources,
					"class_id" => $this->event_entry_classes,
					"status" => $status,
					"brother_of" => new obj_predicate_prop("id"),
					"limit" => 1,
					new object_list_filter(array("non_filter_classes" => CL_COMICS)),
					"sort_by" => "planner.start ASC",
				));
				$obj = $objs->begin();
				if ($obj)
				{
					$start = $obj->prop("start1");
				}
				$objs = new object_list(array(
					"parent" => $sources,
					"class_id" => $this->event_entry_classes,
					"status" => $status,
					"brother_of" => new obj_predicate_prop("id"),
					"limit" => 1,
					new object_list_filter(array("non_filter_classes" => CL_COMICS)),
					"sort_by" => "planner.start DESC",
				));
				$obj = $objs->begin();
				if ($obj)
				{
					$end = $obj->prop("start1");
				}
				$range = range(date("Y", $start), date("Y", $end));
				foreach($range as $year)
				{
					$objs = new object_list(array(
						"parent" => $sources,
						"class_id" => $this->event_entry_classes,
						"status" => $status,
						"brother_of" => new obj_predicate_prop("id"),
						"start1" => new obj_predicate_compare(obj_predicate_compare::GREATER_OR_EQ, mktime(0, 0, 1, 1, 1, $year)),
						"limit" => 1,
					));
					$obj = $objs->begin();
					if ($obj)
					{
						$this->vars_safe(array(
							"link" => aw_url_change_var(array("date" => date("d-m-Y", $obj->prop("start1")))),
							"name" => $year,
						));
					}
					$years .= $this->parse("YEARS");
				}
				$vcal->years = $years;
			}
		}
		else
		{
			$sources = array();
			foreach($conns as $conn)
			{
				$to_o = $conn->to();
				$sources = $sources + $this->get_event_sources($to_o);
			}
		}

		$vcal->event_entry_classes = $this->event_entry_classes;
		$vcal->event_sources = $sources;
		if ($arr["start_from"])
		{
			// this is used by project to limit the year view to start from the current month
			$range["start"] = $arr["start_from"];
		}

		// this cycle creates the "big" calendar, minicalendar is done in the
		// get_overview callback

		// ookei .. ma saan template k2est kysida kas mul on vaja grupeerida asju?
		$exp_args = array(
			"obj_inst" => $this->obj_inst,
			"cal_inst" => $vcal,
			"range" => $range,
			"status" => isset($arr["status"]) ? $arr["status"] : $status,
		);
		if($this->obj_inst->prop("show_active_trans"))
		{
			$exp_args["show_active_trans"] = true;
		}

		if ("grouped" === $use_template && "day" === $viewtype)
		{
			// use_template on see mida kasutatakse yhe konkreetse syndmuse joonistamiseks
			$args["container_template"] = "grouped.tpl";
			//$arr["event_template"] = "groupitem.tpl";
		}

		$vcal->init_output(array("event_template" => $arr["event_template"]));
		$exp_args["limit_events"] = $this->obj_inst->prop("num_next_events");
		if ($arr["obj_inst"])
		{
			$exp_args["oid"] = $arr["obj_inst"]->id();
		}
		$this->_export_events($exp_args);
		classload("layout/active_page_data");
		$style = array();
		// export all defined styles to the active page and the
		// current template
		$style_props = array(
			"minical_day_with_events",
			"minical_day_without_events",
			"minical_day_today",
			"minical_day_active",
			"minical_day_deactive",
			"minical_title",
			"minical_background",
		);

		// day viewd peab saama kuidagi grupeerida

		$props = $this->obj_inst->properties();
		foreach($style_props as $style_prop)
		{
			$prop_value = $props[$style_prop];
			if (0 != $prop_value)
			{
				active_page_data::add_site_css_style($prop_value);
				static $stylex;
				if (!$stylex)
				{
					$stylex = get_instance(CL_STYLE);
				}
				$style[$style_prop] = $stylex->get_style_name($prop_value);
			}
		}

		$args = array(
			"style" => $style,
		);


		if ($this->obj_inst->prop("use_template") ===  "weekview")
		{
			$args["tpl"] = "week.tpl";
		}

		if (!empty($text))
		{
			$args["text"] = $text;
		}

		$rv = $vcal->get_html($args);
		if ("grouped" == $use_template)
		{
			$conns = $this->obj_inst->connections_from(array(
				"type" => "RELTYPE_EVENT_SOURCE",
			));

			$rv = "";

			$rv = html::href(array(
				"url" => aw_url_change_var("date",$range["prev"]),
				"caption" => t("&lt;&lt;"),
			));

			$rv .= " &nbsp; " . aw_locale::get_lc_date($range["start"],6) . "&nbsp; ";

			$rv .= html::href(array(
				"url" => aw_url_change_var("date",$range["next"]),
				"caption" => t("&gt;&gt;"),
			));

			$event_validator = $this->obj_inst->get_first_obj_by_reltype("RELTYPE_EVENT_VALIDATOR");
			$rv .= html::linebreak();
			foreach ($conns as $conn)
			{
				$to_o = $conn->to();

				$events = $this->get_events_from_object(array(
					"obj_inst" => $to_o,
					"range" => $range,
				));
	//			$vcal->events_sorted = 1;
				$vcal->items = array();
				foreach($events as $event)
				{
					$data = $event;
					$evt_obj = new object($event["id"]);
					if ($this->obj_inst->prop("actives_only") && $evt_obj->status() != object::STAT_ACTIVE)
					{
						continue;
					}
					$data = $evt_obj->properties() + $data;
					$data["name"] = $evt_obj->name();
					$data["icon"] = "event_icon_url";
					if ($event_validator)
                                        {
                                                $i = $event_validator->instance();
                                                if (!$i->eval_controller($event_validator->id(), $evt_obj))
                                                {
                                                        continue;
                                                }
                                        }

					$vcal->add_item(array(
						"item_start" => $event["start"],
						"item_end" => $event["end"],
						"data" => $data,
					));
				}
				if (sizeof($events) > 0)
				{
					$rv .= $vcal->draw_day(array("caption" => $to_o->name()));
				}
			}

		}
		return $rv;
	}

}
