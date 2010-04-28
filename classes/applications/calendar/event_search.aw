<?php

namespace automatweb;
// event_search.aw - Syndmuste otsing
/*

@classinfo syslog_type=ST_EVENT_SEARCH relationmgr=yes maintainer=dragut

@default table=objects
@default group=general
@default field=meta
@default method=serialize

	@property event_cfgform type=relpicker reltype=RELTYPE_EVENT_CFGFORM
	@caption Kasutatav vorm

	@property use_output type=relpicker reltype=RELTYPE_EVENT_SHOW
	@caption N&auml;itamise vorm

	@property show_type type=select field=meta method=serialize
	@caption N&auml;ta vaikimisi s&uuml;ndmusi

	@property target_section type=textbox field=meta method=serialize
	@caption Tulemuste target section

	@property hide_search_form type=checkbox ch_value=1 field=meta method=serialize
	@caption Peida otsinguvorm

	@property dont_search_from_all_sites type=checkbox ch_value=1 field=meta method=serialize
	@caption &Auml;ra otsi k&otilde;igist saitidest

	@property dont_search_from_all_languages type=checkbox ch_value=1 field=meta method=serialize
	@caption &Auml;ra otsi k&otilde;igist keeltest

	@property sort_by_groups type=checkbox ch_value=1 field=meta method=serialize
	@caption Sorteeri gruppide j&auml;rgi
	@comment Gruppideks v&otilde;ivad olla n&auml;iteks Projektid

	@property every_event_just_once type=checkbox ch_value=1 field=meta method=serialize
	@caption Kuva iga s&uuml;ndmust ainult 1 kord

	@property items_per_page type=textbox size=5 field=meta method=serialize
	@caption Mitu s&uuml;ndmust lehel

	@property preview_object type=relpicker reltype=RELTYPE_DOCUMENT
	@caption Eelvaate objekt

@groupinfo ftsearch caption="Otsinguvorm"
@default group=ftsearch

	@property navigator_range type=chooser orient=vertical
	@caption Ajavahemiku navigaator

	@property ftsearch_fields type=chooser multiple=1 orient=vertical
	@caption Vabateksti v&auml;jad

	@property ftsearch_fields2 type=chooser multiple=1 orient=vertical
	@caption Vabateksti v&auml;jad 2


@groupinfo ftform caption="Otsinguvorm seadistamine"
@default group=ftform

	@property ftform type=table no_caption=1
	@caption Vorm


@groupinfo styles caption="Stiilid"
@default group=styles

	@property month_navigator_style type=relpicker reltype=RELTYPE_STYLE
	@caption Kuu navigaatori stiil

	@property week_navigator_style type=relpicker reltype=RELTYPE_STYLE
	@caption N&auml;dala navigaatori stiil

	@property sform_table_style type=relpicker reltype=RELTYPE_STYLE
	@caption Otsinguvormi tabeli stiil

	@property sform_submit_style type=relpicker reltype=RELTYPE_STYLE
	@caption Otsinguvormi nupu stiil


@groupinfo ftresults caption="Tulemuste seadistamine"
@default group=ftresults

	@property result_table type=table
	@caption Tulemuste tabel


@reltype EVENT_CFGFORM value=1 clid=CL_CFGFORM
@caption S&uuml;ndmuse vorm

@reltype EVENT_SOURCE value=3 clid=CL_MENU,CL_PLANNER,CL_PROJECT
@caption S&uuml;ndmuste allikas

@reltype EVENT_SHOW value=4 clid=CL_CFGFORM
@caption N&auml;itamise vorm

@reltype STYLE value=5 clid=CL_CSS
@caption Stiil

@reltype DOCUMENT value=6 clid=CL_DOCUMENT
@caption Dokument
*/

class event_search extends class_base
{
	const AW_CLID = 850;

	var $cfgform_id;
	function event_search()
	{
		$this->init(array(
			"tpldir" => "applications/calendar/event_search",
			"clid" => CL_EVENT_SEARCH,
		));

		$this->fields = array(
			"fulltext",
			"fulltext2",
			"start_date",
			"end_date",
			"project1",
			"project2",
			"active",
			"format",
			"location",
			"level",
			"sector",
			"search_btn"
		);
		lc_site_load("event_search", &$this);
	}

	function callback_pre_edit($arr)
	{
		$o = $arr["obj_inst"];
		$cfgform_id = $o->prop("event_cfgform");
		if (is_oid($cfgform_id) && $this->can("view", $cfgform_id))
		{
			$this->evt_cfgform_id = $cfgform_id;
		};
	}

	function gen_ftsearch_fields($arr)
	{
		if (!$this->evt_cfgform_id)
		{
			return PROP_IGNORE;
		};
		$t = new cfgform();
		$props = $t->get_props_from_cfgform(array("id" => $this->evt_cfgform_id));
		foreach($props as $propname => $propdata)
		{
			if ($propdata["type"] == "textbox" || $propdata["type"] == "textarea")
			{
				$opts[$propname] = $propdata["caption"];
			};
		};
		$arr["prop"]["options"] = $opts;

	}

	function gen_ftform($arr)
	{
		$prop = &$arr["prop"];
		$o = &$arr["obj_inst"];
		$t = &$prop["vcl_inst"];
		$formconfig = $o->meta("formconfig");
		$event_cfgform = $o->prop('event_cfgform');
		if ($this->can('view', $event_cfgform))
		{
			$event_cfgform = new object($event_cfgform);
			$event_class_id = $event_cfgform->prop('ctype');
		}

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));

		$t->define_field(array(
			"name" => "caption",
			"caption" => t("Pealkiri"),
		));

		$t->define_field(array(
			"name" => "settings",
			"caption" => t("Seaded"),
		));

		$t->define_field(array(
			"name" => "active",
			"caption" => t("Aktiivne"),
		));

		$t->set_sortable(false);

		$t->define_data(array(
			"name" => t("Tekstiotsing"),
			"caption" => html::textbox(array(
				"name" => "fulltext[caption]",
				"value" => $formconfig["fulltext"]["caption"] ? $formconfig["fulltext"]["caption"] : ("Tekstiotsing"),
			)),
			"active" => html::checkbox(array(
				"name" => "fulltext[active]",
				"value" => $formconfig["fulltext"]["active"],
				"checked" => $formconfig["fulltext"]["active"],
			)),
		));
		$t->define_data(array(
			"name" => t("Tekstiotsing 2"),
			"caption" => html::textbox(array(
				"name" => "fulltext2[caption]",
				"value" => $formconfig["fulltext2"]["caption"] ? $formconfig["fulltext2"]["caption"] : ("Tekstiotsing 2"),
			)),
			"active" => html::checkbox(array(
				"name" => "fulltext2[active]",
				"value" => $formconfig["fulltext2"]["active"],
				"checked" => $formconfig["fulltext2"]["active"],
			)),
		));

		$date_display_options = array(
			'select' => t('Kuvatakse valikkastid'),
			'one_textbox' => t('Kuvatakse &uuml;ks tekstikast'),
		);
		$t->define_data(array(
			"name" => t("Alguskuup&auml;ev"),
			"caption" => html::textbox(array(
				"name" => "start_date[caption]",
				"value" => $formconfig["start_date"]["caption"] ? $formconfig["start_date"]["caption"] : t("Alguskuup&auml;ev"),
			)),
			"settings" => html::select(array(
				'name' => 'start_date[type]',
				'options' => $date_display_options,
				'selected' => $formconfig['start_date']['type']
			)),
			"active" => html::checkbox(array(
				"name" => "start_date[active]",
				"value" => $formconfig["start_date"]["active"],
				"checked" => $formconfig["start_date"]["active"],

			)),
		));

		$t->define_data(array(
			"name" => t("L&otilde;ppkuup&auml;ev"),
			"caption" => html::textbox(array(
				"name" => "end_date[caption]",
				"value" => $formconfig["end_date"]["caption"] ? $formconfig["end_date"]["caption"] : t("L&otilde;ppkuup&auml;ev"),
			)),
			"settings" => html::select(array(
				'name' => 'end_date[type]',
				'options' => $date_display_options,
				'selected' => $formconfig['end_date']['type']
			)),
			"active" => html::checkbox(array(
				"name" => "end_date[active]",
				"value" => $formconfig["end_date"]["active"],
				"checked" => $formconfig["end_date"]["active"],
			)),
		));

		$prj_conns = $o->connections_from(array(
			"type" => "RELTYPE_EVENT_SOURCE",
		));

		$prj_opts = array("0" => t("--vali--"));

		foreach($prj_conns as $prj_conn)
		{
			$id = $prj_conn->prop("to");
			$name = $prj_conn->prop("to.name");
			$prj_opts[$id] = $name;
		}
		$t->define_data(array(
			"name" => t("Projekt 1"),
			"caption" => html::textbox(array(
				"name" => "project1[caption]",
				"value" => $formconfig["project1"]["caption"] ? $formconfig["project1"]["caption"] : t("Projekt 1"),
			)),
			"settings" => html::select(array(
				"name" => "project1[rootnode]",
				"options" => $prj_opts,
				"multiple" => 1,
				"value" => $formconfig["project1"]["rootnode"],
			)),
			"active" => html::checkbox(array(
				"name" => "project1[active]",
				"value" => $formconfig["project1"]["active"],
				"checked" => $formconfig["project1"]["active"],
			)),
		));

		$t->define_data(array(
			"name" => t("Projekt 2"),
			"caption" => html::textbox(array(
				"name" => "project2[caption]",
				"value" => $formconfig["project2"]["caption"] ? $formconfig["project2"]["caption"] : t("Projekt 2"),
			)),
			"settings" => html::select(array(
				"name" => "project2[rootnode]",
				"options" => $prj_opts,
				"multiple" => 1,
				"value" => $formconfig["project2"]["rootnode"],
			)),
			"active" => html::checkbox(array(
				"name" => "project2[active]",
				"value" => $formconfig["project2"]["active"],
				"checked" => $formconfig["project2"]["active"],
			))
		));

		if ($event_class_id == CL_CALENDAR_EVENT)
		{
			$t->define_data(array(
				"name" => t("Asukoht"),
				"caption" => html::textbox(array(
					"name" => "location[caption]",
					"value" => $formconfig["location"]["caption"] ? $formconfig["location"]["caption"] : t("Asukoht"),
				)),
				"settings" => "",
				"active" => html::checkbox(array(
					"name" => "location[active]",
					"value" => $formconfig["location"]["active"],
					"checked" => $formconfig["location"]["active"],
				))
			));
			$t->define_data(array(
				"name" => t("Tase"),
				"caption" => html::textbox(array(
					"name" => "level[caption]",
					"value" => $formconfig["level"]["caption"] ? $formconfig["level"]["caption"] : t("Tase"),
				)),
				"settings" => "",
				"active" => html::checkbox(array(
					"name" => "level[active]",
					"value" => $formconfig["level"]["active"],
					"checked" => $formconfig["level"]["active"],
				))
			));

			$t->define_data(array(
				"name" => t("Valdkonnad"),
				"caption" => html::textbox(array(
					"name" => "sector[caption]",
					"value" => $formconfig["sector"]["caption"] ? $formconfig["sector"]["caption"] : t("Valdkonnad"),
				)),
				"settings" => "",
				"active" => html::checkbox(array(
					"name" => "sector[active]",
					"value" => $formconfig["sector"]["active"],
					"checked" => $formconfig["sector"]["active"],
				))
			));
		}
		$t->define_data(array(
			"name" => t("Otsi nupp"),
			"caption" => html::textbox(array(
				"name" => "search_btn[caption]",
				"value" => $formconfig["search_btn"]["caption"] ? $formconfig["search_btn"]["caption"] : t("Otsi nupp"),
			)),
			"settings" => "",
			"active" => html::checkbox(array(
				"name" => "search_btn[active]",
				"value" => $formconfig["search_btn"]["active"],
				"checked" => $formconfig["search_btn"]["active"],
			))
		));

	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "every_event_just_once":
				$meta = $arr["obj_inst"]->meta();
				if(!isset($meta["every_event_just_once"]))
				{
					$prop["value"] = 1;
				}
				break;

			case "navigator_range":
				$prop["options"] = array(
					0 => t("Kuu navigaator"),
					1 => t("N&auml;dala navigaator"),
				);
				break;
			case "show_type":
				$prop["options"] = array(
					0 => t("Kuu j&auml;rgi"),
					1 => t("P&auml;eva j&auml;rgi"),
					2 => t("Kuu alates t&auml;nasest"),
				);
				break;
			case "ftsearch_fields":
			case "ftsearch_fields2":
				$this->gen_ftsearch_fields($arr);
				break;

			case "ftform":
				$this->gen_ftform($arr);
				break;

			case "result_table":
				$retval = $this->gen_result_table($arr);
				break;
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		$o = &$arr["obj_inst"];
		switch($prop["name"])
		{
			case "ftform":
				$fdata = array();
				foreach($this->fields as $fname)
				{
					if ($arr["request"][$fname])
					{
						$fdata[$fname] = $arr["request"][$fname];
					}
				}
				$o->set_meta("formconfig", $fdata);
				break;

			case "result_table":
				$o->set_meta("result_table", $arr["request"]["result_table"]);
				break;
		}
		return $retval;
	}

	function parse_alias($arr)
	{
		$args = $_GET;
		$args["id"] = $arr["alias"]["to"];
		return $this->show($args);
	}

	function gen_result_table($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
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
			"name" => "clickable",
			"caption" => t("Klikitav"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "fullview",
			"caption" => t("T&auml;isvaates"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "brs",
			"caption" => t("Reavahetused"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "sepb",
			"caption" => t("Eraldaja enne"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "sepa",
			"caption" => t("Eraldaja p&auml;rast"),
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

		$tc = new cfgform();
		$cform_obj = new object($this->evt_cfgform_id);
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
		};

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
				"clickable" => html::checkbox(array(
					"name" => "${pname}[${sname}][clickable]",
					"value" => 1,
					"checked" => ($oldvals[$sname]["clickable"] == 1),
				)),
				"fullview" => html::checkbox(array(
					"name" => "${pname}[${sname}][fullview]",
					"value" => 1,
					"checked" => ($oldvals[$sname]["fullview"] == 1),
				)),
				"brs" => html::checkbox(array(
					"name" => "${pname}[${sname}][brs]",
					"value" => 1,
					"checked" => ($oldvals[$sname]["brs"] == 1),
				)),
				"sepa" => html::textbox(array(
					"name" => $pname."[$sname][sepa]",
					"value" => $oldvals[$sname]["sepa"],
					"size" => 3,
				)),
				"sepb" => html::textbox(array(
					"name" => $pname."[$sname][sepb]",
					"value" => $oldvals[$sname]["sepb"],
					"size" => 3,
				)),
				"ord" => html::textbox(array(
					"name" => "${pname}[${sname}][ord]",
					"value" => $oldvals[$sname]["ord"],
					"size" => 2,
				)),
			);
			$prps["props"] = html::textarea(array(
				"name" => "${pname}[${sname}][props]",
				"value" => $oldvals[$sname]["props"],
				"rows" => 5,
				"cols" => 15,
			));
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
// if there is still some extra separators needed for added fields, then uncomment these:
// and implement the show part in show function
/*
				$prps["fields"] .= html::textbox(array(
					"name" => "${pname}[${sname}][fields_attributes][$i][sep]",
					"value" => $oldvals[$sname]['fields_attributes'][$i]['sep'],
					"size" => 5,
				));

				$prps["fields"] .= html::textbox(array(
					"name" => "${pname}[${sname}][fields_attributes][$i][sep_before]",
					"value" => $oldvals[$sname]["fields_attributes"][$i]['sep_before'],
					"size" => 5,
				));
*/
				$prps["fields"] .= html::select(array(
					"name" => "${pname}[${sname}][fields][$i]",
					"options" => array(0 => "-- vali --") + $names,
					"value" => $oldvals[$sname]["fields"][$i],
				));
/*
				$prps["fields"] .= html::textbox(array(
					"name" => "${pname}[${sname}][fields_attributes][$i][sep_after]",
					"value" => $oldvals[$sname]["fields_attributes"][$i]['sep_after'],
					"size" => 5,
				));
*/
				$prps["fields"] .= "<br />";


			}
			$t->define_data($prps);
		};
		$t->set_sortable(false);
	}

	function get_search_results($arr)
	{
		// 1. pane kokku object list
		$ob = new object($arr["id"]);
		$formconfig = $ob->meta("formconfig");
		$ft_fields = $ob->prop("ftsearch_fields");
		$all_projects1 = new object_list(array(
			"parent" => array($formconfig["project1"]["rootnode"]),
			"class_id" => array(CL_PROJECT, CL_PLANNER),
		));
		$all_projects2 = new object_list(array(
			"parent" => array($formconfig["project2"]["rootnode"]),
			"class_id" => array(CL_PROJECT, CL_PLANNER),
		));
		$par1 = $all_projects1->ids();
		$par2 = $all_projects2->ids();

		$search = array();
		$search["parent"] = array_merge($par1,$par2);

	       $ft_fields = $ob->meta("ftsearch_fields");
	       $or_parts = array("name" => "%" . $arr["str"] . "%");
	       foreach($ft_fields as $ft_field)
	       {
		       $or_parts[$ft_field] = "%" . $arr["str"] . "%";

	       };
	       $search[] = new object_list_filter(array(
		       "logic" => "OR",
		       "conditions" => $or_parts,
	       ));
		$search["sort_by"] = "planner.start";
		$search["class_id"] = array(CL_CRM_MEETING, CL_CALENDAR_EVENT);
		$start_tm = strtotime("today 0:00");
		$end_tm = strtotime("+30 days", $start_tm);
		$search["CL_CALENDAR_EVENT.start1"] = new obj_predicate_compare(OBJ_COMP_BETWEEN, $start_tm, $end_tm);

		$ol = new object_list($search);
		$ret = array();
		$baseurl = aw_ini_get("baseurl");
		foreach($ol->arr() as $o)
		{
			$orig = $o->get_original();
			$oid = $orig->id();
			$ret[$oid] = array(
				"url" => $baseurl . "/" . $oid,
				"title" => $orig->name(),
				"modified" => $orig->prop("start1"),
			);
		};

		return $ret;


		// 2. tagasta tulemused

	}


	////
	// !this shows the object. not strictly necessary, but you'll probably need it, it is used by parse_alias
	/**
		@attrib name=search nologin="1" all_args="1"
	**/
	function show($arr)
	{
		enter_function("event_search::show");
		$ob = new object($arr["id"]);
		$every_event_just_once = $ob->prop("every_event_just_once");
		if(!$ob->prop("every_event_just_once"))
		{
			$meta = $ob->meta();
			if(!isset($meta["every_event_just_once"]))
			{ // Default value is 1
				$every_event_just_once = 1;
			}
		}
//arr($arr);
		$event_cfgform = $ob->prop('event_cfgform');
		if ($this->can('view', ($event_cfgform)))
		{
			$event_cfgform = new object($event_cfgform);
			$event_class_id = $event_cfgform->prop('ctype');
		}

		$show_search_form = true;
		if ($ob->prop('hide_search_form') == 1)
		{
			$show_search_form = false;
		}

		$htmlc = get_instance("cfg/htmlclient", array("template" => "webform.tpl"));
		$htmlc->start_output();

		$formconfig = $ob->meta("formconfig");

		$do_search = false;
		$search = array();

		// start_date and end_date from URL can be now in format day.month.year as well. --dragut
		if (!is_array($arr['start_date']) && substr_count($arr['start_date'], '.') == 2)
		{
			$parts = explode('.', $arr['start_date']);
			$arr['start_date'] = array(
				'month' => $parts[1],
				'year' => $parts[2],
				'day' => $parts[0]
			);
		}
		if (!is_array($arr['end_date']) && substr_count($arr['end_date'], '.') == 2)
		{
			$parts = explode('.', $arr['end_date']);
			$arr['end_date'] = array(
				'month' => $parts[1],
				'year' => $parts[2],
				'day' => $parts[0]
			);
		}


		load_vcl("date_edit");
		$dt = new date_edit();
		$start_tm = $dt->get_timestamp($arr["start_date"]);
		$end_tm = $dt->get_timestamp($arr["end_date"]);

		// if date is set in the url, then try to use that to specify our range.
		// last condition in this if is probably temporary --dragut
		if (isset($arr["date"]) && substr_count($arr["date"],"-") == 2 && empty($arr['sbt']) )
		{
			list($_d,$_m,$_y) = explode("-",$arr["date"]);
			$start_tm = mktime(0,0,0,$_m,$_d,$_y);
			$end_tm = mktime(23,59,59,$_m,$_d,$_y);
			$arr["start_date"] = array("day" => $_d, "month" => $_m, "year" => $_y);
		}

		$cur_days = cal_days_in_month(CAL_GREGORIAN, date("m"), date("Y"));
		$show_type = $ob->prop("show_type");
		$sd = ($show_type == 1 or $show_type == 2) ? date("d") : 1;
		$ed = ($show_type == 1 or $show_type == 2) ? date("d") : $cur_days;
		if($start_tm == -1)
		{
			$start_tm = mktime(0, 0, 0, date("m"), $sd, date("Y"));
			$arr["start_date"]["month"] = date("m");
			$arr["start_date"]["year"] = date("Y");
			$arr["start_date"]["day"] = 1;
		}
		if($end_tm == -1)
		{
			$md = date("m");
			$yd = date("Y");
			if($show_type == 2)
			{
				$md++;
				if($md > 12)
				{
					$md = 1;
					$yd++;
				}
			}
			$cur_days = cal_days_in_month(CAL_GREGORIAN, $md, $yd);
			$end_tm = mktime(0, 0, 0, $md, $ed, $yd);
			$arr["end_date"]["month"] = $md;
			$arr["end_date"]["year"] = $yd;
			$arr["end_date"]["day"] = $cur_days;
		}
		if($formconfig["fulltext"]["active"] && $show_search_form)
		{
			$htmlc->add_property(array(
				"name" => "fulltext",
				"caption" => $formconfig["fulltext"]["caption"],
				"type" => "textbox",
				"value" => $arr["fulltext"],
			));
		}
		if($formconfig["fulltext2"]["active"] && $show_search_form)
		{
			$htmlc->add_property(array(
				"name" => "fulltext2",
				"caption" => $formconfig["fulltext2"]["caption"],
				"type" => "textbox",
				"value" => $arr["fulltext2"],
			));
		}

		if($formconfig["start_date"]["active"] && $show_search_form)
		{
			switch ($formconfig["start_date"]["type"])
			{
				case "one_textbox":
					$htmlc->add_property(array(
						"name" => "start_date",
						"caption" => $formconfig["start_date"]["caption"],
						"type" => "textbox",
						"value" => date("d.m.Y", $start_tm),
					));
					break;
				default:
					$htmlc->add_property(array(
						"name" => "start_date",
						"caption" => $formconfig["start_date"]["caption"],
						"type" => "date_select",
						"value" => $start_tm,
					));
			}
		}
		if($formconfig["end_date"]["active"] && $show_search_form)
		{
			switch ($formconfig["end_date"]["type"])
			{
				case "one_textbox":
					$htmlc->add_property(array(
						"name" => "end_date",
						"caption" => $formconfig["end_date"]["caption"],
						"type" => "textbox",
						"value" => date("d.m.Y", $end_tm),
					));
					break;
				default:
					$htmlc->add_property(array(
						"name" => "end_date",
						"caption" => $formconfig["end_date"]["caption"],
						"type" => "date_select",
						"value" => $end_tm,
					));
			}
		}
		if ($event_class_id == CL_CALENDAR_EVENT && $show_search_form)
		{
			$htmlc->add_property(array(
				"name" => "end_date",
				"caption" => $formconfig["end_date"]["caption"],
				"type" => "date_select",
				"value" => $end_tm,
				"buttons" => true
			));
			if($formconfig["location"]["active"])
			{
				$htmlc->add_property(array(
					"name" => "location",
					"caption" => $formconfig["location"]["caption"],
					"type" => "textbox",
					"value" => $arr["location"],
				));
			}
			if($formconfig["level"]["active"])
			{
				$cl_calendar_event = get_instance(CL_CALENDAR_EVENT);
				$htmlc->add_property(array(
					"name" => "level",
					"caption" => $formconfig["level"]["caption"],
					"type" => "select",
					"value" => $arr["level"],
					"options" => array('0' => t('Vali')) + $cl_calendar_event->level_options,
				));
			}
			if($formconfig["sector"]["active"])
			{
				$cl_calendar_event = get_instance(CL_CALENDAR_EVENT);
				$htmlc->add_property(array(
					"name" => "sector",
					"caption" => $formconfig["sector"]["caption"],
					"type" => "textbox",
					"value" => $arr["sector"],
				));
			}
		}
		$search_p1 = false;
		$search_p2 = false;
		$p_rn1 = $formconfig["project1"]["rootnode"];
		$p_rn2 = $formconfig["project2"]["rootnode"];

		// dragut starts hacking:
		// so, if $p_rn1 and $p_rn2 are empty, then maybe there are no root node set
		// but maybe still there are some event_sources set, so then we try to use them
		if (empty($p_rn1) && empty($p_rn2))
		{
			$connections_to_event_sources = $ob->connections_from(array(
				"type" => "RELTYPE_EVENT_SOURCE",
			));
			foreach ($connections_to_event_sources as $connection_to_event_source)
			{
				$p_rn1[] = $connection_to_event_source->prop("to");
			}
		}
		// dragut stops hacking

		$p_rn1 = is_array($p_rn1) ? $p_rn1 : array($p_rn1);
		$p_rn2 = is_array($p_rn2) ? $p_rn2 : array($p_rn2);
		foreach($p_rn1 as $pkey => $pval)
		{
			if(!is_oid($pval) || !$this->can("view", $pval))
			{
				unset($p_rn1[$pkey]);
			}
		}
		foreach($p_rn2 as $pkey => $pval)
		{
			if(!is_oid($pval) || !$this->can("view", $pval))
			{
				unset($p_rn2[$pkey]);
			}
		}
		if(count($p_rn1) > 0)
		{
			$prj_ch1 = array();
			$optgnames1 = array();
			$rn1 = array();
			foreach($p_rn1 as $trn1)
			{
				$tmp = obj($trn1);
				$clid = $tmp->class_id();
				if($clid == CL_MENU)
				{
					$prj_cx = $this->_get_project_choices($trn1);
					// if there are projects to choose from, search from them, else assume that it's a event folder
					if(!empty($prj_cx))
					{
						$search_p1 = true;
						$prj_ch1[] = $prj_cx;
						$optgnames1[] = $tmp->name();
					}
					else
					{
						$rn1[] = $tmp->id();
						$event_folders_tree = new object_tree(array(
							'parent' => $tmp->id(),
							'class_id' => CL_MENU
						));
						$rn1 = array_merge($rn1, $event_folders_tree->ids());
					}
				}
				elseif($clid == CL_PLANNER)
				{
					$r = $tmp->prop("event_folder");
					if(is_oid($r) && $this->can("view", $r))
					{
						$rn1[] = $r;
					}
					$search_p1 = true;
					// this goddamn calendar has to manage the
					// events from other calendars and projects aswell.. oh hell..
					$sources = $tmp->connections_from(array(
						"type" => "RELTYPE_EVENT_SOURCE",
					));
					foreach($sources as $source)
					{
						if($source->prop("to.class_id") == CL_PLANNER)
						{
							$_tmp = $source->to();
							$rn1[] = $_tmp->prop("event_folder");
						}
						else
						{
							$rn1[] = $source->prop("to");
						}
						$prj_ch1[0][$source->prop("to")] = $source->prop("to.name");
					}
				}
				elseif($clid == CL_PROJECT)
				{
					$rn1[] = $trn1;
					$sources = $tmp->connections_from(array(
						"type" => "RELTYPE_SUBPROJECT",
					));
					$search_p1 = true;
					foreach($sources as $source)
					{
						$prj_ch1[$source->prop("to")] = $source->prop("to.name");
						$rn1[] = $source->prop("to");
					}
				}
			}
		}
		if(count($p_rn2) > 0)
		{
			$rn2 = array();
			$prj_ch2 = array();
			$optgnames2 = array();
			foreach($p_rn2 as $trn2)
			{
				$tmp = obj($trn2);
				if($tmp->class_id() == CL_MENU)
				{
					$prj_cx = $this->_get_project_choices($trn2);
					if(!empty($prj_cx))
					{
						$optgnames2[] = $tmp->name();
						$search_p2 = true;
						$prj_ch2[] = $prj_cx;
					}
					else
					{
						$rn2[] = $tmp->id();
						$event_folders_tree = new object_tree(array(
							'parent' => $tmp->id(),
							'class_id' => CL_MENU
						));
						$rn2 = array_merge($rn2, $event_folders_tree->ids());
					}
				}
				elseif($tmp->class_id() == CL_PLANNER)
				{
					$r = $tmp->prop("event_folder");
					if(is_oid($r) && $this->can("view", $r))
					{
						$rn2[] = $r;
					}
					$sources = $tmp->connections_from(array(
						"type" => "RELTYPE_EVENT_SOURCE",
					));
					foreach($sources as $source)
					{
						if($source->prop("to.class_id") == CL_PLANNER)
						{
							$_tmp = $source->to();
							$rn2[] = $_tmp->prop("event_folder");
						}
						else
						{
							$rn2[] = $source->prop("to");
						}
					}
				}
				elseif($tmp->class_id() == CL_PROJECT)
				{
					$rn2[] = $trn2;
					$sources = $tmp->connections_from(array(
						"type" => "RELTYPE_SUBPROJECT",
					));
					foreach($sources as $source)
					{
						$rn2[] = $source->prop("to");
					}
				}
			}
		}
		if($search_p1 && $formconfig["project1"]["active"] && $show_search_form)
		{
			$vars = array(
				"name" => "project1",
				"caption" => $formconfig["project1"]["caption"],
				"type" => "select",
				"value" => $arr["project1"],
			);
			if(count($prj_ch1) > 1)
			{
				$vars["options"] = array(0 => t("k&otilde;ik")) + $prj_ch1;
				$vars["optgnames"] = $optgnames1;
				$vars["optgroup"] = $prj_ch1;
			}
			else
			{
				$vars["options"] = array(0 => t("K&otilde;ik")) + (array)reset($prj_ch1);
				//$vars["options"] = array(0 => t("k&otilde;ik")) + $prj_ch1;
			}
			$htmlc->add_property($vars);
		}

		if($search_p2 && $formconfig["project2"]["active"] && $show_search_form)
		{

			$vars = array(
				"name" => "project2",
				"caption" => $formconfig["project2"]["caption"],
				"type" => "select",
				"value" => $arr["project2"],
			);
			if(count($prj_ch2) > 1)
			{
				$vars["options"] = array(0 => t("k&otilde;ik"));
				$vars["optgnames"] = $optgnames2;
				$vars["optgroup"] = $prj_ch2;
			}
			else
			{
				$vars["options"] = array(0 => t("k&otilde;ik")) + reset($prj_ch2);
			}
			$htmlc->add_property($vars);
		}
		if ($show_search_form)
		{
			$htmlc->add_property(array(
				"name" => "sbt",
				"caption" => $formconfig["search_btn"]["caption"] != "" ? $formconfig["search_btn"]["caption"] : t("Otsi"),
				"type" => "submit",
			));
		}
		$do_search = true;
		if ($do_search)
		{
			$search["parent"] = $parx2 = array();
			$search["sort_by"] = "planner.start";


			if ($event_class_id == CL_CALENDAR_EVENT)
			{
				$search['class_id'] = CL_CALENDAR_EVENT;
			}
			else
			{
				$search["class_id"] = array(CL_STAGING, CL_CRM_MEETING, CL_TASK);
			}

			$par1 = array();
			$par2 = array();
			if($search_p1 || $search_p2)
			{
				$par1 = $this->get_parn($ob, $p_rn1);

				$par2 = $this->get_parn($ob, $p_rn2);

				if (is_oid($arr["project1"]))
				{
					$search["parent"][] = $arr["project1"];
				}
				elseif($search_p1)
				{
					$search["parent"] = $par1;
				}
				if (is_oid($arr["project2"]))
				{
					$parx2[] = $arr["project2"];
				}
				elseif($search_p2 || !empty($par2))
				{
					$parx2 = $par2;
				}
			}
			elseif($rn1 || $rn2)
			{
				if($rn1)
				{
					if(is_array($rn1))
					{
						$search["parent"] = array_merge($rn1, $search["parent"]);
					}
					else
					{
						$search["parent"][] = $rn1;
					}
				}
				if($rn2)
				{
					if(is_array($rn2))
					{
						$parx2 = array_merge($rn2, $parx2);
					}
					else
					{
						$parx2[] = $rn2;
					}
				}
			}

			if ($ob->prop('dont_search_from_all_languages') != 1)
			{
				$search["lang_id"] = array();
			}

			if ($ob->prop('dont_search_from_all_sites') != 1)
			{
				$search["site_id"] = array();
			}

			$ft_fields = $ob->prop("ftsearch_fields");
			$ft_fields2 = $ob->prop("ftsearch_fields2");
			if ($arr["fulltext"])
			{
				$or_parts = array();
				foreach(safe_array($ft_fields) as $ft_field)
				{
					$or_parts[$ft_field] = "%" . $arr["fulltext"] . "%";
				}
				$search[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => $or_parts,
				));
			}
			if ($arr["fulltext2"])
			{
				$or_parts = array();
				foreach(safe_array($ft_fields2) as $ft_field)
				{
					$or_parts[$ft_field] = "%" . $arr["fulltext2"] . "%";
				}
				$search[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => $or_parts,
				));
			}

			$ext_search = $search;
			unset($ext_search["id"]);
			$ext_search["limit"] = "0,1";
			$ext_search["CL_CALENDAR_EVENT.utextvar1"] = '%';
			$ext_search["lang_id"] = array();
			$ext_search["site_id"] = array();
			//$ext_search[] = new object_list_filter(array("non_filter_classes" => array(CL_PLANNER)));
			$ext_s_search = $ext_search;
			$ext_e_search = $ext_search;
			$ext_s_search["sort_by"] = "planner.start asc";
			$ext_e_search["sort_by"] = "planner.end desc";
			$ext_s_ol = new object_list($ext_s_search);
			$ext_s_o = $ext_s_ol->begin();
			$ext_e_ol = new object_list($ext_e_search);
			$ext_e_o = $ext_e_ol->begin();

			if($ext_s_o && $ext_s_o->prop("start1") < $start_tm)
			{
				$is_before = true;
			}

			if($ext_e_o && $ext_e_o->prop("end") > $end_tm)
			{
				$is_after = true;
			}

			if ($search['class_id'] == CL_CALENDAR_EVENT)
			{
				if (!empty($arr['org']))
				{
					$search[] = new object_list_filter(array(
						'logic' => 'OR',
						'conditions' => array(
							"CL_CALENDAR_EVENT.RELTYPE_ORGANIZER" => $arr['org'],
							"CL_CALENDAR_EVENT.RELTYPE_LOCATION.owner" => $arr['org'],
						)
					));
				}
				if (!empty($arr['sector']))
				{
					$search['CL_CALENDAR_EVENT.RELTYPE_SECTOR.name'] = '%'.$arr['sector'].'%';
				}
				if (!empty($arr['level']))
				{
					$search['CL_CALENDAR_EVENT.level'] = (int)$arr['level'];
				}

				if (!empty($arr['location']))
				{
					$search[] = new object_list_filter(array(
						'logic' => 'OR',
						'conditions' => array(
							"CL_CALENDAR_EVENT.RELTYPE_LOCATION.address.riik.name" => '%'.$arr['location'].'%',
							"CL_CALENDAR_EVENT.RELTYPE_LOCATION.address.maakond.name" => '%'.$arr['location'].'%',
							"CL_CALENDAR_EVENT.RELTYPE_LOCATION.address.linn.name" => '%'.$arr['location'].'%',
						)
					));
				}

				if (!empty($arr['location_county']))
				{
					$search["CL_CALENDAR_EVENT.RELTYPE_LOCATION.address.maakond"] = $arr['location_county'];
				}

				if (!empty($arr['location_city']))
				{
					$search["CL_CALENDAR_EVENT.RELTYPE_LOCATION.address.linn"] = $arr['location_city'];
				}

				$search[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						new object_list_filter(array(
							"logic" => "AND",
							"conditions" => array(
								"end" => new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $start_tm),
								"start1" => new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, ($end_tm + 86399)),
							),
						)),
						new object_list_filter(array(
							"logic" => "AND",
							"conditions" => array(
								"end" => -1,
								"start1" => new obj_predicate_compare(OBJ_COMP_BETWEEN, $start_tm, ($end_tm + 86399)),
							),
						)),
						new object_list_filter(array(
							"logic" => "AND",
							"conditions" => array(
								"start1" => new obj_predicate_compare(OBJ_COMP_BETWEEN, $start_tm, ($end_tm + 86399)),
							),
						)),
						new object_list_filter(array(
							"logic" => "AND",
							"conditions" => array(
								"CL_CALENDAR_EVENT.RELTYPE_EVENT_TIME.end" => new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $start_tm),
								"CL_CALENDAR_EVENT.RELTYPE_EVENT_TIME.start" => new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, ($end_tm + 86399)),
							),
						)),
						new object_list_filter(array(
							"logic" => "AND",
							"conditions" => array(
								"CL_CALENDAR_EVENT.RELTYPE_EVENT_TIME.end" => -1,
								"CL_CALENDAR_EVENT.RELTYPE_EVENT_TIME.start" => new obj_predicate_compare(OBJ_COMP_BETWEEN, $start_tm, ($end_tm + 86399)),
							),
						)),
						new object_list_filter(array(
							"logic" => "AND",
							"conditions" => array(
								"CL_CALENDAR_EVENT.RELTYPE_EVENT_TIME.start" => new obj_predicate_compare(OBJ_COMP_BETWEEN, $start_tm, ($end_tm + 86399)),
							),
						)),
					),
				));

			}
			else
			{
				$search[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						new object_list_filter(array(
							"logic" => "AND",
							"conditions" => array(
								"end" => new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $start_tm),
								"start1" => new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, ($end_tm + 86399)),
							),
						)),
						new object_list_filter(array(
							"logic" => "AND",
							"conditions" => array(
								"end" => -1,
								"start1" => new obj_predicate_compare(OBJ_COMP_BETWEEN, $start_tm, ($end_tm + 86399)),
							),
						)),
						new object_list_filter(array(
							"logic" => "AND",
							"conditions" => array(
								"start1" => new obj_predicate_compare(OBJ_COMP_BETWEEN, $start_tm, ($end_tm + 86399)),
							),
						)),
					),
				));
			}
			if(is_oid($arr["evt_id"]) && $this->can("view", $arr["evt_id"]))
			{
				$obj = obj($arr["evt_id"]);
				$orig = $obj->get_original();
				$search = array(
					"oid" => $orig->id(),
				);
				if ($ob->prop('dont_search_from_all_sites') != 1)
				{
					$search['site_id'] = array();
				}
				if ($ob->prop('dont_search_from_all_languages') != 1)
				{
					$search['lang_id'] = array();
				}
			}
			$clinf = aw_ini_get("classes");
			$edata = array();
			$ecount = array();
			if (sizeof($search["parent"]) != 0 || $search["oid"])
			{
				if($search["oid"])
				{
					$ol = new object_list($search);
				}
				else
				{
					$ol = new object_list($search);
					$oris = $ol->brother_ofs();

					if($arr["project2"] || !empty($parx2))
					{
						$search2 = $search;
						$search2["parent"] = $parx2;
						$ol = new object_list($search2);
						$oris2 = $ol->brother_ofs();
						$ids = array_intersect($oris2, $oris);
					}
					else
					{
						$ids = $oris;
					}
					if(!empty($ids))
					{
						$ol_params = array(
							"oid" => $ids,
							"class_id" => array(CL_STAGING,CL_CRM_MEETING, CL_CALENDAR_EVENT, CL_TASK),
							"start1" => new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, ($end_tm + 3600*24)),
							"sort_by" => "planner.start",
						);
						if ($ob->prop('dont_search_from_all_sites') != 1)
						{
							$ol_params['site_id'] = array();
						}
						if ($ob->prop('dont_search_from_all_languages') != 1)
						{
							$ol_params['lang_id'] = array();
						}
						$ol = new object_list($ol_params);
						// if the results are divided into pages, then ask only showing results --dragut
						if ( (int)$ob->prop('items_per_page') > 0 )
						{
							// parameteres for pager drawing function:
							$pager_params = array(
								'current_page' => empty($_GET['page']) ? 1 : (int)$_GET['page'],
								'items_per_page' => (int)$ob->prop('items_per_page'),
								'items_count' => $ol->count()
							);

							$current_page = empty($_GET['page']) ? 0 : (int)$_GET['page'] - 1;
							$ol_params['limit'] = ( $current_page * (int)$ob->prop('items_per_page') ).', '.(int)$ob->prop('items_per_page');
							$ol = new object_list($ol_params);
						}
					}
					else
					{
						$ol = new object_list();
					}

				}
				$origs = array();
				foreach($ol->arr() as $res)
				{
					$orig_id = $res->id();
					$origs[] = $orig_id;
					// Event can last for more than one day.
					$s_ts = $res->prop("start1");
					$e_ts = $res->prop("end");
					for($i = $s_ts; strcmp(date("Ymd", $i), date("Ymd", $e_ts)) <= 0; $i += 24*3600)
					{
						if($ecount[$orig_id] && ($_GET["evt_id"] || $every_event_just_once))
						{
							// Kui syndmust p2ritakse OID j2rgi, siis kuvame teda ainult 1 kord.
							// V6i kui syndmust ei taheta rohkem kui 1 kord kuvada.
							break;
						}
						// Kui syndmust p2ritakse OID j2rgi, siis ajalisi piiranguid ei kontrolli.
						if(($i < $start_tm || $i > $end_tm + (24*3600 - 1)) && !isset($_GET["evt_id"]))
						{
							continue;
						}
						$edata[] = array_merge(array(
							"event_id" => $res->id(),
							"event" => $res->name(),
							"project_selector" => "n/a",
							"date" => date("d-m-Y", $i),
						), $res->properties(), array("start1" => $i));
						$ecount[$orig_id]++;
					}
				}
				$this->read_template(($search["oid"] ? "show_event.tpl" : "search_results.tpl"));
				$tabledef = $ob->meta("result_table");
				uasort($tabledef, array($this, "__sort_props_by_ord"));
				// We have to parse the table header after the data, cuz we don't know if we need to add the "Delete" column.
				$parse_table_header = true;
				$add_delete_column = false;
			}
			$blist = array();
			if(!empty($origs))
			{
				$fls = new object_list(array(
					"brother_of" => $origs,
				));
				$blist = $fls->arr();
			}
			$pr1 = $formconfig["project1"]["rootnode"];
			$dats = array();
			foreach($blist as $b_o)
			{
				$par = $b_o->parent();
				if (!is_oid($par) || !$this->can("view", $par))
				{
					continue;
				}
				$orig = $b_o->brother_of();
				if ($ecount[$orig])
				{
					if(!in_array($par, $par2) && !in_array($par, $par1))
					{
						continue;
					}
					$p2 = new object($par);
					$nm = $p2->name();
					if ($p2->class_id() == CL_MENU)
					{
						continue;
					}
					$dats[$orig][$par] = $nm;
				}
			}
			foreach($dats as $key => $val)
			{
				$val = safe_array($val);
				$valz = $val;
				sort($valz);

				$i_arr = $this->find_edata_key(&$edata, $key);
				foreach($i_arr as $i)
				{
					$edata[$i]["projs"] = array_keys($val);
					$edata[$i]["project_selector"] = implode(", ", $valz);
				}
			}
			if(count($prj_ch1) > 1)
			{
				$groups = array();
				$grps = array();
				foreach($prj_ch1 as $gid => $parr)
				{
					$obj = obj(key($parr));
					$parq = obj($obj->parent());
					$grps[$gid] = $parq->name();
					$groups[$gid] = array();
				}
				foreach($edata as $ekey => $eval)
				{
					foreach($prj_ch1 as $ckey => $cval)
					{
						$z = 0;
						foreach($cval as $key => $xz)
						{
							$cval[$key] = $z;
							$z++;
						}
						foreach(safe_array($eval["projs"]) as $dkey)
						{
							foreach($cval as $xkey => $xv)
							{
								if($dkey == $xkey)
								{
									$groups[$ckey][$ekey] = $eval + array("ord" => $xv);
									break;
								}
							}
						}
					}
				}
			}
			else
			{
				$groups = array($edata);
			}
			exit_function("event_search::search_speed");
			$res = "";
			$si = __get_site_instance();
			$has_proc = method_exists($si, "handle_parse_event_field");

			$col_count = 0;
			//teeb enne
			foreach($tabledef as $key => $propdef)
			{
				if(!$propdef["active"])
				{
					continue;
				}
				if($key == "content")
				{
					continue;
				}
				$col_count++;
			}
			$this->vars(array(
				"col_count" => $col_count,
			));


			$aliasmrg = get_instance("alias_parser");
			foreach($groups as $gkey => $edata)
			{
				uasort($edata, array($this, "__sort_props_by_date"));
				if(count($groups) > 1)
				{
					$this->vars(array(
						"block_caption" => $grps[$gkey],
					));
					$res .= $this->parse("BLOCK");
					if ($ob->prop('sort_by_groups') == 1)
					{
						uasort($edata, array($this, "__sort_props_by_proj"));
					}
				}
				foreach($edata as $eval)
				{
					$id = $eval["event_id"];
					$obj = obj($id);
					$cdat = "";
					foreach($tabledef as $sname => $propdef)
					{
						if($sname == "content" || $sname == "delete_link")
						{
							continue;
						}
						if($search["oid"] && !$propdef["fullview"])
						{
							continue;
						}
						if($search["oid"] && (empty($eval[$sname]) || $eval[$sname] == -1))
						{
						//	continue;
						}
						elseif(!$propdef["active"] && !$search["oid"])
						{
							continue;
						}
						$names = array_merge((array)$sname, safe_array($tabledef[$sname]["fields"]));
						$names = $this->make_keys($names);
						$val = array();
						$skip = false;
						foreach($names as $nms)
						{
							if(empty($nms))
							{
								continue;
							}
							$v = create_links(isset($eval[$nms]) ? $eval[$nms] : $eval["meta"][$nms]);
							if ($has_proc)
							{
								$v = $si->handle_parse_event_field($nms, $v);
							}

							if($search["oid"] && (empty($v) || $v == -1))
							{
								$skip = true;
								continue;
							}
							$value = $tabledef[$nms]["props"];
							// if there is something in the controller field set, then lets see what it is
							if (!empty($value))
							{
								// if there is some php code
								if (strpos($value, "#php#") !== false)
								{
									// remove the php code indicators
									$value = str_replace("#php#", "", $value);
									$value = str_replace("#/php#", "", $value);
									// and execute the code
									eval($value);
								}
								else
								{
									// if it isn't a php code, then it might be a date conf string:
									if ($nms == "start1" || $nms == "end")
									{
										$v = date($value, $v);
									}
									else
									{
										// and if it isn't the date case, then maybe i should
										// just give it the value which is in controller?
										// would it make any sense?
										// not so sure about that, so commenting it out now:

									//	$v = $value;
									}
								}
							}
							else
							{
								if(strpos($nms, "image") !== false)
								{
									if(is_oid($v) && $this->can("view", $v))
									{
										$image_inst = get_instance(CL_IMAGE);
										$v = html::img(array(
											'url' => $image_inst->get_url_by_id($v)
										));
									}
								}
								if ($nms == "start1" || $nms == "end")
								{
									if($skip)
									{
										continue;
									}
									// if there is no controller set for date:
									$v = date("d-m-Y", $v);
								}
								if($nms == "name")
								{
									if($obj->prop("udeftb1") != "")
									{
										$v = html::popup(array(
											"url" => $obj->prop("udeftb1"),
											"caption" => $v,
											"target" => "_blank",
											"toolbar" => 1,
											"directories" => 1,
											"status" => 1,
											"location" => 1,
											"resizable" => 1,
											"scrollbars" => 1,
											"menubar" => 1,
										));
									}
								}
								if($tabledef[$nms]["clickable"] == 1 && !$search["oid"])
								{
									if($ob-> prop("preview_object"))
									{
										$parse_url = aw_ini_get("baseurl")."/".$ob-> prop("preview_object")."?evt_id=".$id;
									}
									else
									{
										$parse_url = aw_ini_get("baseurl").aw_url_change_var(array("evt_id" => $id));
									}
									$v = html::href(array(
										"url" => $parse_url,
										"caption" => $v,
									));
								}
								if($tabledef[$nms]["brs"] == 1)
								{
									$v = nl2br($v);
								}

								// this seems to be the right place to execute controller (props)


								if(strpos($v, "#") !== false)
								{
									$aliasmrg->parse_oo_aliases($id, $v);
								}
							}
							$val[] = $tabledef[$nms]["sepb"].$v.$tabledef[$nms]["sepa"];

						}

						if (!$skip)
						{
							$val = implode(" ".$tabledef[$sname]["sep"]." ", $val);
							$this->vars(array(
								"cell" => $val,
								"colcaption" => $propdef["caption"],
							));
							$cdat .= $this->parse("CELL");
							$this->vars(array(
								"CELL" => $cdat,
								$sname => $val
							));
						}
					}
					$nmx = "content";
					$use = false;
					if($search["oid"] && $tabledef["content"]["fullview"])
					{
						$use = true;
					}
					elseif($tabledef["content"]["active"] && !($search["oid"]))
					{
						$use = true;
					}
					$content = "";
					if($use)
					{
						if(!empty($eval["content"]))
						{
							$content = nl2br($eval["content"]);
						}
						elseif(!empty($eval["utextarea1"]))
						{
							$content = nl2br($eval["utextarea1"]);
						}
						if(strpos($content, "#") !== false)
						{
							$aliasmrg->parse_oo_aliases($id, $content);
						}
					}
					$i++;
					$this->vars(array(
						"num" => $i % 2 ? 1 : 2,
					));
					if($use && !empty($content))
					{
						$this->vars(array(
							"fulltext_name" => $tabledef[$nmx]["caption"],
							"fulltext" => $content,
						));
						$fulltext = $this->parse("FULLTEXT");
					}
					else
					{
						$fulltext = "";
					}

					if ($this->is_template('DELETE_EVENT_LINK') && $this->can("delete", $id))
					{
						$this->vars(array(
							'delete_url' => $this->mk_my_orb("delete_event", array(
								'event_id' => $id,
								'return_url' => get_ru()
							), CL_EVENT_SEARCH),
						));
						$delete_url_str = $this->parse('DELETE_EVENT_LINK');
						$add_delete_column = true;
					}
					$this->vars(array(
						"FULLTEXT" => $fulltext,
						"DELETE_EVENT_LINK" => $delete_url_str
					));
					$res .= $this->parse("EVENT");
				}
			}
			if($parse_table_header)
			{
				$cdat = "";
				$col_count = 0;
				$clickable = false;
				if ($this->is_template('DELETE_EVENT_LINK') && $add_delete_column)
				{
					$tabledef['delete_link'] = array(
						'caption' => t('Kustuta'),
						'active' => true
					);
				}
				foreach($tabledef as $key => $propdef)
				{
					if(!$propdef["active"])
					{
						continue;
					}
					if($key == "content")
					{
						continue;
					}
					if($propdef["clickable"])
					{
						$clickable = true;
					}

					$this->vars(array(
						"colcaption" => $propdef["caption"],
					));
					$cdat .= $this->parse("COLHEADER");
					$col_count++;

					$this->vars(array(
						"COLHEADER" => $cdat,
						"col_count" => $col_count,
					));
				}
			}
			//Navigation bar
			$arr = $arr + array("section" => aw_global_get("section"));
			$next_month_args = $arr;
			$prev_month_args = $arr;

			if($next_month_args["start_date"]["month"] == 12)
			{
				$next_month_args["start_date"]["month"] = 1;
				$next_month_args["end_date"]["month"] = 1;

				$next_month_args["start_date"]["year"]++;
				$next_month_args["end_date"]["year"]++;
			}
			else
			{
				$next_month_args["start_date"]["month"]++;
				$next_month_args["end_date"]["month"] = $next_month_args["start_date"]["month"];
			}
			$next_month_args["start_date"]["day"] = 1;
			$next_month_args["end_date"]["day"] = cal_days_in_month(CAL_GREGORIAN, $next_month_args["end_date"]["month"], $next_month_args["end_date"]["year"]);
			$next_month_args["sbt"] = "Otsi";
			if($prev_month_args["start_date"]["month"] == 1)
			{
				$prev_month_args["start_date"]["month"] = 12;
				$prev_month_args["start_date"]["year"]--;

				$prev_month_args["end_date"]["month"] = 12;
				$prev_month_args["end_date"]["year"]--;
			}
			else
			{
				$prev_month_args["start_date"]["month"]--;
				$prev_month_args["end_date"]["month"] = $prev_month_args["start_date"]["month"];
			}

			$prev_month_args["start_date"]["day"] = 1;
			$prev_month_args["end_date"]["day"] = cal_days_in_month(CAL_GREGORIAN, $prev_month_args["end_date"]["month"], $prev_month_args["end_date"]["year"]);
			$prev_month_args["sbt"] = "Otsi";

			$prev_days = $prev_month_args["end_date"]["day"];
			$next_days = $next_month_args["end_date"]["day"];
			$s_date = $arr["start_date"];
			$cur_days = cal_days_in_month(CAL_GREGORIAN, $s_date["month"], $s_date["year"]);
			$t_day = mktime(0, 0, 0, $s_date["month"], 1, $s_date["year"]);
			$day_of_week = date("w", $t_day);
			$offset = $day_of_week - 1 < 0 ? 6 : $day_of_week - 1;
			$weeks = ceil(($cur_days + $offset) / 7);
			for($i = 1; $i <= $weeks; $i++)
			{
				if($offset)
				{
					$start_day = $offset > 0 ? $prev_days - $offset : $t_day;
					$start_month = $offset > 0 ? $prev_month_args["start_date"]["month"] : $s_date["month"];
					$start_year = $offset > 0 ? $prev_month_args["start_date"]["year"] : $s_date["year"];
					$end_day = 7 - $offset;
					$end_year = $s_date["year"];
					$end_month = $s_date["month"];
					unset($offset);
				}
				elseif($i == $weeks)
				{
					$b_days = $end_day + 7;
					$start_day = $end_day + 1;
					$end_month = $b_days > $cur_days ? $next_month_args["start_date"]["month"] : $end_month;
					$end_year = $b_days > $cur_days ? $next_month_args["start_date"]["year"] : $end_year;
					$end_day = $b_days > $cur_days ? $b_days - $cur_days : $b_days;
				}
				else
				{
					$start_day = $end_day + 1;
					$end_day = $end_day + 7;
					$start_month = $s_date["month"];
					$start_year = $s_date["year"];
				}
				$week_args = array(
					"section" => aw_global_get("section"),
					"start_date" => array(
						"day" => $start_day,
						"year" => $start_year,
						"month" => $start_month,
					),
					"end_date" => array(
						"day" => $end_day,
						"year" => $end_year,
						"month" => $end_month,
					),
					"sbt" => "Otsi",
				);
				$this->vars(array(
					"week_url" => str_replace("event_search", "", $this->mk_my_orb("search", $week_args, "event_search")),
					"week_nr" => $i,
				));

				$nx = ($i == $weeks ? "next_weeks_end": "next_weeks").($start_day == $arr["start_date"]["day"] && $end_day == $arr["end_date"]["day"] ? "_b" : "");
				$res_weeks .= $this->parse($nx);
			}

			$this->vars(array(
				"begin_month_name" => aw_locale::get_lc_month($arr["start_date"]["month"]),
				"begin_year" => $arr["start_date"]["year"],
				"prev_month_url" => str_replace("event_search", "", $this->mk_my_orb("search", $prev_month_args)),
				"next_month_url" => str_replace("event_search", "", $this->mk_my_orb("search", $next_month_args)),
				"next_weeks" => $res_weeks,
			));

			if($is_before)
			{
				$pm = $this->parse("PREV_MONTH_URL");
				$this->vars(array(
					"PREV_MONTH_URL" => $pm,
				));
			}

			if($is_after)
			{
				$nm = $this->parse("NEXT_MONTH_URL");
				$this->vars(array(
					"NEXT_MONTH_URL" => $nm,
				));
			}

			$this->vars(array(
				"EVENT" => $res,
				"PAGE" => $this->_compose_pager($pager_params),
			));
			$result = $this->parse();
			$htmlc->add_property(array(
				"name" => "results",
				"type" => "text",
				"no_caption" => 1,
				"value" => $result,
			));
		}

		$htmlc->finish_output(array(
			"data" => array(
				"class" => "",
				"section" => $this->can('view', $ob->prop("target_section")) ? $ob->prop("target_section") : aw_global_get("section"),
				"action" => "search",
				"id" => $ob->id(),
				"alias" => "event_search",
			),
			"method" => "get",
			"form_handler" => aw_ini_get("baseurl")."/".aw_global_get("section"),
			"submit" => "no"
		));

		$html = $htmlc->get_result(array(
			"form_only" => 1
		));
		exit_function("event_search::show");
		return empty($search["oid"]) ? $html : $result;
	}


	////
	// items_count
	// items_per_page
	// current_page
	//
	// returns the pages string
	function _compose_pager($arr)
	{
		$pages = array();
		// how many pages:
		$page_count = ceil($arr['items_count'] / $arr['items_per_page']);
		for ($page = 1; $page <= $page_count; $page++)
		{
			$this->vars(array(
				'page_num' => $page,
				'page_url' => aw_url_change_var('page', $page),
				'next_page_url' => aw_url_change_var('page', $page + 1),
				'prev_page_url' => aw_url_change_var('page', $page - 1)
			));
			if ($page == $arr['current_page'])
			{
				$pages[] = $this->parse('ACTIVE_PAGE');
			}
			else
			{
				$pages[] = $this->parse('PAGE');
			}
		}
		return implode($this->parse('PAGE_SEPARATOR'), $pages);
	}

	function _get_project_choices($parent)
	{
		if(!is_oid($parent) || !$this->can("view", $parent))
		{
			return array();
		}
		$ol = new object_list(array(
			"parent" => $parent,
			"class_id" => array(CL_PROJECT, CL_PLANNER, CL_MENU),
			"sort_by" => "objects.jrk",
			"site_id" => array(),
			"lang_id" => array()
		));
		return $ol->names();

	}

	function __sort_props_by_ord($el1, $el2)
	{
		return (int)($el1["ord"] - $el2["ord"]);
	}

	function __sort_props_by_date($a, $b)
	{
		return (int)$a["start1"] - (int)$b["start1"];
	}

	function __sort_props_by_proj($el1, $el2)
	{
		if((int)($el1["ord"] - $el2["ord"]) == 0)
		{
			return (int)($el1["start1"] - $el2["start1"]);
		}
		else
		{
			return (int)($el1["ord"] - $el2["ord"]);
		}
	}

    /*function get_search_results($arr)
        {
                // 1. pane kokku object list
                $ob = new object($arr["id"]);
                $formconfig = $ob->meta("formconfig");
                $ft_fields = $ob->prop("ftsearch_fields");
                $all_projects1 = new object_list(array(
                        "parent" => array($formconfig["project1"]["rootnode"]),
                        "class_id" => array(CL_PROJECT, CL_PLANNER),
                ));
                $all_projects2 = new object_list(array(
                        "parent" => array($formconfig["project2"]["rootnode"]),
                        "class_id" => array(CL_PROJECT, CL_PLANNER),
                ));
                $par1 = $all_projects1->ids();
                $par2 = $all_projects2->ids();

                $search = array();
                $search["parent"] = array_merge($par1,$par2);

               $ft_fields = $ob->meta("ftsearch_fields");
               $or_parts = array("name" => "%" . $arr["str"] . "%");
               foreach($ft_fields as $ft_field)
               {
                       $or_parts[$ft_field] = "%" . $arr["str"] . "%";

               };
               $search[] = new object_list_filter(array(
                       "logic" => "OR",
                       "conditions" => $or_parts,
               ));
                $search["sort_by"] = "planner.start";
                $search["class_id"] = array(CL_CRM_MEETING, CL_CALENDAR_EVENT);
                $start_tm = strtotime("today 0:00");
                $end_tm = strtotime("+30 days", $start_tm);
                $search["CL_CALENDAR_EVENT.start1"] = new obj_predicate_compare(OBJ_COMP_BETWEEN, $start_tm, $end_tm);
                $ol = new object_list($search);
                $ret = array();
                $baseurl = aw_ini_get("baseurl");
                foreach($ol->arr() as $o)
                {
                        $orig = $o->get_original();
                        $oid = $orig->id();
                        $ret[$oid] = array(
                                "url" => $baseurl . "/" . $oid,
                                "title" => $orig->name(),
                                "modified" => $orig->prop("start1"),
                        );
                };
		return $ret;
	}
*/

	/**
		@attrib name=delete_event params=name

		@param event_id required type=int acl=view;edit
		@param return_url optional type=string
	**/
	function delete_event($arr)
	{
		if ($this->can('delete', $arr['event_id']))
		{
			$o = new object($arr['event_id']);
			$o->delete(true);

			// make the event go away in event_search list --dragut (27.11.2007)
			$cache = get_instance('cache');
			$cache->file_clear_pt('storage_search');
		}

		return $arr['return_url'];
	}

	/**
		@attrib name=convert_evx
	**/
	function convert_evx($arr)
	{
		$sql = "SELECT objects.oid,metadata,planner.utextarea1 AS ua FROM objects,planner  WHERE objects.oid = planner.id AND class_id = 831";
		$this->db_query($sql);
		while($row = $this->db_next())
		{
			if (empty($row["metadata"]))
			{
				continue;
			};
			$old = aw_unserialize($row["metadata"]);
			$old_txt = $old["utextarea1"];
			if (empty($old_txt))
			{
				continue;
			};
			$this->save_handle();
			$sql = "UPDATE planner SET utextarea1 = '$old_txt' WHERE id = '" . $row["oid"] . "'";
			$this->db_query($sql);
			$this->restore_handle();
			print "sql = $sql<br>";
			print "ox = $old_txt<br>";
			print "<pre>";
			print_r($row);
			print "</pre>";
		};
		print "all done<br>";


	}

	private function get_parn($ob, $p_rn)
	{
		$all_projects_filter = array(
			"parent" => $p_rn,
			"class_id" => array(CL_PROJECT, CL_PLANNER, CL_MENU),
		);
		if ($ob->prop('dont_search_from_all_languages') != 1)
		{
			$all_projects_filter['lang_id'] = array();
		}
		if ($ob->prop('dont_search_from_all_sites') != 1)
		{
			$all_projects_filter['site_id'] = array();
		}
		$all_projects = new object_list($all_projects_filter);
		$ids = $all_projects->ids();

		// Don't we need the "major" parents itself also? I think we do.
		//															-kaarel
		$ids = array_merge($ids,$p_rn);

		// Planner's events can be in any folder. Have to get it from it's property.
		$planners = new object_list(array(
			"class_id" => CL_PLANNER,
			"oid" => $ids,
		));
		foreach($planners->arr() as $planner)
		{
			if($this->can("view", $planner->event_folder))
			{
				$ids[] = $planner->event_folder;
			}
		}

		return $ids;
	}

	function find_edata_key($edata, $key)
	{
		$r = array();
		foreach($edata as $i => $data)
		{
			if($data["event_id"] == $key)
			{
				$r[] = $i;
			}
		}
		return $r;
	}
}
?>
