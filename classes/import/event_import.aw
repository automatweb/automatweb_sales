<?php
// event_import.aw - S&uuml;ndmuste import
/*

@classinfo syslog_type=ST_EVENT_IMPORT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kaarel

@default table=objects
@default group=general

	@property events_manager type=relpicker reltype=RELTYPE_EVENTS_MANAGER field=meta method=serialize
	@caption S&uuml;ndmuste halduse keskkond
	@comment S&uuml;ndmuste halduse keskkond, kus kirjeldatakse s&uuml;ndmuste, valdkondade, toimumiskohtade, korraldajate lugemis- ja kirjutamiskaustad jmt

	@property event_form type=relpicker reltype=RELTYPE_EVENT_FORM field=meta method=serialize
	@caption S&uuml;ndmuse vorm

	@property xml_sources type=relpicker reltype=RELTYPE_XML_SOURCE field=meta method=serialize multiple=1
	@caption XML allikad:
	@comment XML v&auml;ljundi allikad, mida kasutatakse importimisel

	@property json_sources type=relpicker reltype=RELTYPE_JSON_SOURCE field=meta method=serialize multiple=1
	@caption JSON allikad:
	@comment JSON v&auml;ljundi allikad, mida kasutatakse importimisel

	@property original_lang type=select field=meta method=serialize
	@caption Keel
	@comment Imporditavate objektide keel

	@property no_event_time_objs type=checkbox ch_value=1 default=0 field=meta method=serialize
	@caption Iga toimumisaeg eraldi s&uuml;ndmuseks

	@property translatable_fields type=select multiple=1 field=meta method=serialize
	@caption V&auml;ljad, mida v&otilde;imalusel t&otilde;lgitakse

	@property last_import_text type=text store=no
	@caption Viimane import
	@comment Viimase impordi l&otilde;ppemise aeg

	@property next_import_text type=text store=no
	@caption J&auml;rgmine automaatne import
	@comment J&auml;rgmise automaatse alguse aeg

	@property import_events_all type=checkbox ch_value=1 field=meta method=serialize
	@caption Impordi s&uuml;ndmused algusest

	@property past_length type=textbox field=meta method=serialize size=5
	@caption Imporditavate p&auml;evade arv (tagasi)
	@comment Mitu p&auml;eva alates t&auml;nasest tagasi imporditakse

	@property future_length type=textbox field=meta method=serialize size=5
	@caption Imporditavate p&auml;evade arv (edasi)
	@comment Mitu p&auml;eva alates t&auml;nasest edasi imporditakse

	@property import_events type=text store=no
	@caption Impordi s&uuml;ndmused
	@comment Link s&uuml;ndmuste importimiseks

@groupinfo xml_config caption="XML seaded"
@default group=xml_config

	@property xml_config_table type=table no_caption=1

@groupinfo recurrence_config caption="Automaatne import"
@default group=recurrence_config

	@property recurrence type=relpicker reltype=RELTYPE_RECURRENCE field=meta method=serialize
	@caption Kordus

	@property auto_import_user type=textbox field=meta method=serialize
	@caption Kasutaja
	@comment Kasutajanimi, kelle &otilde;igustes automaatne import teostatakse

	@property auto_import_passwd type=password field=meta method=serialize
	@caption Parool
	@comment Kasutaja parool, kelle &otilde;igustes automaatne import teostatakse

#@groupinfo import_log caption="Logi" submit=no
#@default group=import_log

#	@property import_log_toolbar type=toolbar no_caption=1

#	@property import_log_table type=table
#	@caption Muudetud s&uuml;ndmuste logi

#@groupinfo import_log_conf caption="Logi seaded"
@groupinfo import_log_conf caption="Muudatused"
@default group=import_log_conf

		@property import_log_conf_table type=table no_caption=1

		@property import_log_conf_time_table type=table no_caption=1

		@property import_log_conf_location_table type=table no_caption=1

		@property import_log_conf_sector_table type=table no_caption=1

#@groupinfo import_log_exeptions caption="Logi erandid"
#@default group=import_log_exeptions

#	@property import_log_exeptions_table type=table no_caption=1

@reltype XML_SOURCE value=1 clid=CL_XML_SOURCE
@caption XML allikas

@reltype RECURRENCE value=5 clid=CL_RECURRENCE
@caption Kordus

@reltype EVENT_FORM value=10 clid=CL_CFGFORM
@caption S&uuml;ndmuse vorm

@reltype JSON_SOURCE value=15 clid=CL_JSON_SOURCE
@caption JSON allikas

@reltype EVENTS_MANAGER value=20 clid=CL_EVENTS_MANAGER
@caption S&uuml;ndmuste halduse keskkond
*/

class event_import extends class_base
{
	function event_import()
	{
		// change this to the folder under the templates folder, where this classes templates will be,
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "import/event_import",
			"clid" => CL_EVENT_IMPORT
		));
	}

	//////
	// class_base classes usually need those, uncomment them if you want to use them
	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "last_import_text":
				$last_import = $arr['obj_inst']->meta("last_import");
				$prop['value'] = (empty($last_import)) ? "0" : date("d-M-y / H:i", $last_import);
				break;

			case "next_import_text":
				$next_import = $this->activate_next_auto_import(array(
					"object" => $arr['obj_inst'],
				));
				$prop['value'] = (empty($next_import)) ? "0" : date("d-M-y / H:i", $next_import);
				break;

			case "original_lang":
				$lg = get_instance("languages");
				$prop["options"] = $lg->get_list();
				break;

			case "import_events":
				$message = t("Alates viimasest impordist");
				if ($arr['obj_inst']->prop("import_events_all"))
				{
					$message = t("K&otilde;ik s&uuml;ndmused");
				}
				$prop['value'] = html::href(array(
					"caption" => sprintf(t("Impordi s&uuml;ndmused (%s)"), $message),
					"url" => $this->mk_my_orb("import_events", array(
							"id" => $arr['obj_inst']->id(),
						)),
					"title" => sprintf(t("Impordi Kultuuriakna s&uuml;ndmused (%s)"), $message),
				));
				break;
		};
		return $retval;
	}

	function _get_import_log_toolbar($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->add_button(array(
			"name" => "make_changes",
			"tooltip" => t("Tee muudatused"),
			"img" => "save.gif",
			"action" => "make_changes",
		));
		$t->add_button(array(
			"name" => "auto_change",
			"tooltip" => t("Muuda selle s&uuml;ndmuse seda v&auml;lja automaatselt edaspidi"),
			"img" => "class_244_done.gif",
			"action" => "auto_change",
		));
		$t->add_button(array(
			"name" => "ignore",
			"tooltip" => t("Ignoreeri selle v&auml;lja muudatusi edaspidi"),
			"img" => "class_244.gif",
			"action" => "ignore",
		));
		$t->add_delete_button();
	}

	function _get_translatable_fields($arr)
	{

		// getting properties from cfgform
		$event_form_oid = $arr["obj_inst"]->prop("event_form");
		if (!is_oid($event_form_oid))
		{
			return;
		}
		$event_form_obj = obj($event_form_oid);
		$event_form_inst = $event_form_obj->instance();
		$props = $event_form_inst->get_props_from_cfgform(array(
			"id" => $event_form_obj->id(),
		));
		foreach ($props as $value)
		{
			if (empty($value['caption']))
			{
				$value['caption'] = $value['name'];
			}
			$options[$value['name']] = $value['caption'];
		}

		$arr["prop"]["options"] = $options;
	}

	function _get_import_log_table($arr)
	{
		// NOT IN USE
		$t = &$arr["prop"]["vcl_inst"];

		// Let's describe the table
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
			"caption" => t("Vali"),
		));
		$t->define_field(array(
			"name" => "obj",
			"caption" => t("S&uuml;ndmus"),
		));
		$t->define_field(array(
			"name" => "field",
			"caption" => t("V&auml;li"),
		));
		$t->define_field(array(
			"name" => "content_old",
			"caption" => t("Vana sisu"),
		));
		$t->define_field(array(
			"name" => "content_new",
			"caption" => t("Uus sisu"),
		));
		$t->define_field(array(
			"name" => "timestamp",
			"caption" => t("Aeg"),
		));

		// Gathering the data.
		$ol = new object_list(array(
			"parent" => array(),
			"class_id" => CL_IMPORT_LOG,
		));
		foreach($ol->arr() as $log)
		{
			$log_parent_id = $log->parent();
			$log_parent_obj = obj($log_parent_id);
			$all_vals = $log_parent_obj->meta("translations");
			$t->define_data(array(
				"obj" => $log_parent_obj->name(),
				"field" => $log->prop("field") . (($log->meta("trans_lang") != "") ? " (" . $log->meta("trans_lang") . ")" : ""),
				"content_old" => ($log->meta("trans_lang") == "") ? $log_parent_obj->prop($log->prop("field")) : $all_vals[$imp_lang_id][$log->prop("field")],
				"content_new" => $log->prop("content"),
				"timestamp" => date("d-M-y / H:i:s", $log->prop("timestamp")),
				"oid" => $log->id(),
			));
		}
	}

	function subt_subt($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$saved_subtag_table = $arr["saved_subtag_table"];
		$saved_arguement_table = $arr["saved_arguement_table"];
		$saved_xml_conf = $arr['obj_inst']->meta("xml_conf");
		$saved_xml_conf_time = $arr['obj_inst']->meta("xml_conf_time");
		$saved_xml_conf_time_format = $arr['obj_inst']->meta("xml_conf_time_format");
		$saved_xml_conf_place = $arr['obj_inst']->meta("xml_conf_place");
		$saved_xml_conf_category = $arr['obj_inst']->meta("xml_conf_category");

		$date_sel = array("start", "start_date", "start_time", "end", "end_date", "end_time");

		$subtags = $saved_subtag_table[$arr["parent_tag_name"]];
		$subtags = str_replace(" ", "", $subtags);
		$subtags = explode(",", $subtags);
		foreach($subtags as $subtag)
		{
			if(!empty($subtag))
			{
				$t_ptn = $arr["parent_tag_name"];
				$t_ptc = $arr["parent_tag_caption"];
				if($arr["parent_tag_name"] != "root")
				{
					$arr["parent_tag_name"] .= "_".$subtag;
					$arr["parent_tag_caption"] .= " -> ".$subtag;
				}
				else
				{
					$arr["parent_tag_name"] = $subtag;
					$arr["parent_tag_caption"] = $subtag;
				}

				$form_field = html::select(array(
					"name" => "xml_conf[".$arr["parent_tag_source_id"]."_".$arr["parent_tag_name"]."]",
					"options" => $arr["options"],
					"selected" => $saved_xml_conf[$arr["parent_tag_source_id"]."_".$arr["parent_tag_name"]],
				));
				$time_field = html::select(array(
					"name" => "xml_conf_time[".$arr["parent_tag_source_id"]."_".$arr["parent_tag_name"]."]",
					"options" => array(
						"do_not_save_into_db" => t("-- Ei salvestata --"),
						"id" => t("ID allikas"),
						"name" => t("Nimi"),
						"comment" => t("Kommentaar"),
						"start" => t("Algab"),
						"start_date" => t("Algab (ainult kuup&auml;ev)"),
						"start_time" => t("Algab (ainult kellaaeg)"),
						"end" => t("L&otilde;peb"),
						"end_date" => t("L&otilde;peb (ainult kuup&auml;ev)"),
						"end_time" => t("L&otilde;peb (ainult kellaaeg)"),
						"place_id" => t("Toimumiskoha ID"),
//						"place_name" => t("Toimumiskoha nimi"),
					),
					"selected" => $saved_xml_conf_time[$arr["parent_tag_source_id"]."_".$arr["parent_tag_name"]],
				));
				$place_field = html::select(array(
					"name" => "xml_conf_place[".$arr["parent_tag_source_id"]."_".$arr["parent_tag_name"]."]",
					"options" => array(
						"do_not_save_into_db" => t("-- Ei salvestata --"),
						"id" => t("ID allikas"),
						"name" => t("Nimi"),
						"comment" => t("Kirjeldus"),
					),
					"selected" => $saved_xml_conf_place[$arr["parent_tag_source_id"]."_".$arr["parent_tag_name"]],
				));
				$category_field = html::select(array(
					"name" => "xml_conf_category[".$arr["parent_tag_source_id"]."_".$arr["parent_tag_name"]."]",
					"options" => array(
						"do_not_save_into_db" => t("-- Ei salvestata --"),
						"id_multiple" => t("ID allikas"),	// V6ib olla ka mitu, komaga eraldatud ID-d
						"tegevusala" => t("Tegevusala nimetus"),
						"comment" => t("Kirjeldus"),
					),
					"selected" => $saved_xml_conf_category[$arr["parent_tag_source_id"]."_".$arr["parent_tag_name"]],
				));
				if(in_array($saved_xml_conf_time[$arr["parent_tag_source_id"]."_".$arr["parent_tag_name"]], $date_sel))
				{
					$time_format = html::textbox(array(
						"name" => "xml_conf_time_format[".$arr["parent_tag_source_id"]."_".$arr["parent_tag_name"]."]",
						"size" => 15,
						"value" => $saved_xml_conf_time_format[$arr["parent_tag_source_id"]."_".$arr["parent_tag_name"]],
					));
				}
				else
				{
					$time_format = "";
				}
				$t->define_data(array(
					"xml_source" => $arr["parent_tag_source"],
					"xml_field" => $arr["parent_tag_caption"],
					"form_field" => $form_field,
					"time_field" => $time_field,
					"place_field" => $place_field,
					"category_field" => $category_field,
					"time_format" => $time_format,
				));
				$subt_args = $saved_arguement_table[$arr["parent_tag_name"]];
				$subt_args = str_replace(" ", "", $subt_args);
				$subt_args = explode(",", $subt_args);

				foreach($subt_args as $subt_arg)
				{
					if(!empty($subt_arg))
					{
						$form_field = html::select(array(
							"name" => "xml_conf[".$arr["parent_tag_source_id"]."_".$arr["parent_tag_name"]."_args".$subt_arg."]",
							"options" => $arr["options"],
							"selected" => $saved_xml_conf[$arr["parent_tag_source_id"]."_".$arr["parent_tag_name"]."_args".$subt_arg],
						));
						$time_field = html::select(array(
							"name" => "xml_conf_time[".$arr["parent_tag_source_id"]."_".$arr["parent_tag_name"]."_args".$subt_arg."]",
							"options" => array(
								"do_not_save_into_db" => "-- Ei salvestata --",
								"id" => t("ID allikas"),
								"name" => t("Nimi"),
								"comment" => t("Kommentaar"),
								"start" => t("Algab"),
								"start_date" => t("Algab (ainult kuup&auml;ev)"),
								"start_time" => t("Algab (ainult kellaaeg)"),
								"end" => t("L&otilde;peb"),
								"end_date" => t("L&otilde;peb (ainult kuup&auml;ev)"),
								"end_time" => t("L&otilde;peb (ainult kellaaeg)"),
								"place_id" => t("Toimumiskoha ID"),
//								"place_name" => t("Toimumiskoha nimi"),
							),
							"selected" => $saved_xml_conf_time[$arr["parent_tag_source_id"]."_".$arr["parent_tag_name"]."_args".$subt_arg],
						));
						$place_field = html::select(array(
							"name" => "xml_conf_place[".$arr["parent_tag_source_id"]."_".$arr["parent_tag_name"]."_args".$subt_arg."]",
							"options" => array(
								"do_not_save_into_db" => t("-- Ei salvestata --"),
								"id" => t("ID allikas"),
								"name" => t("Nimi"),
								"comment" => t("Kirjeldus"),
							),
							"selected" => $saved_xml_conf_place[$arr["parent_tag_source_id"]."_".$arr["parent_tag_name"]."_args".$subt_arg],
						));
						$category_field = html::select(array(
							"name" => "xml_conf_category[".$arr["parent_tag_source_id"]."_".$arr["parent_tag_name"]."_args".$subt_arg."]",
							"options" => array(
								"do_not_save_into_db" => t("-- Ei salvestata --"),
								"id_multiple" => t("ID allikas"),	// V6ib olla ka mitu, komaga eraldatud ID-d
								"tegevusala" => t("Tegevusala nimetus"),
								"comment" => t("Kirjeldus"),
							),
							"selected" => $saved_xml_conf_category[$arr["parent_tag_source_id"]."_".$arr["parent_tag_name"]."_args".$subt_arg],
						));
						if(in_array($saved_xml_conf_time[$arr["parent_tag_source_id"]."_".$arr["parent_tag_name"]."_args".$subt_arg], $date_sel))
						{
							$time_format = html::textbox(array(
								"name" => "xml_conf_time_format[".$arr["parent_tag_source_id"]."_".$arr["parent_tag_name"]."_args".$subt_arg."]",
								"size" => 15,
								"value" => $saved_xml_conf_time_format[$arr["parent_tag_source_id"]."_".$arr["parent_tag_name"]."_args".$subt_arg],
							));
						}
						else
						{
							$time_format = "";
						}
						$t->define_data(array(
							"xml_source" => $arr["parent_tag_source"],
							"xml_field" => $arr["parent_tag_caption"]." (".$subt_arg.")",
							"form_field" => $form_field,
							"time_field" => $time_field,
							"place_field" => $place_field,
							"category_field" => $category_field,
							"time_format" => $time_format,
						));
					}
				}

				$this->subt_subt($arr);
				$arr["parent_tag_name"] = $t_ptn;
				$arr["parent_tag_caption"] = $t_ptc;
			}
		}
	}

	function _get_xml_config_table($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->set_sortable(false);
		$t->define_field(array(
			"name" => "xml_source",
			"caption" => t("XML allikas"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "xml_field",
			"caption" => t("XML v&auml;li"),
		));
		$t->define_field(array(
			"name" => "form_field",
			"caption" => t("S&uuml;ndmuste vormi v&auml;li"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "time_field",
			"caption" => t("Toimumisaja vormi v&auml;li"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "place_field",
			"caption" => t("Toimumiskoha vormi v&auml;li"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "category_field",
			"caption" => t("Valdkonna vormi v&auml;li"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "time_format",
			"caption" => t("Kuup&auml;eva formaat<br>(vaikimisi Unix timestamp)"),
			"align" => "center",
		));

		// getting properties from cfgform
		$event_form_oid = $arr["obj_inst"]->prop("event_form");
		if (!is_oid($event_form_oid))
		{
			return;
		}
		$event_form_obj = obj($event_form_oid);
		$event_form_inst = $event_form_obj->instance();
		$props = $event_form_inst->get_props_from_cfgform(array(
			"id" => $event_form_obj->id(),
		));
		$options = array("do_not_save_into_db" => "-- Ei salvestata --");
		foreach ($props as $value)
		{
			if (empty($value['caption']))
			{
				$value['caption'] = $value['name'];
			}
			$options[$value['name']] = $value['caption'];
		}
		$arr["options"] = $options;

		// get saved xml configuration data
		$saved_xml_conf = $arr['obj_inst']->meta("xml_conf");

		// getting the xml tags from the sources
		$o = obj($arr["request"]["id"]);
		$conns_to_xml_sources = $o->connections_from(array(
			"type" => "RELTYPE_XML_SOURCE",
		));

		foreach($conns_to_xml_sources as $conn_to_xml_source)
		{
			$xml_source = obj($conn_to_xml_source->prop("to"));

			$saved_subtag_table = $xml_source->meta("subtag_table");
			$saved_arguement_table = $xml_source->meta("arguement_table");

			$arr["parent_tag_source"] = $xml_source->name();
			$arr["parent_tag_source_id"] = $xml_source->id();
			$arr["parent_tag_name"] = "root";
			$arr["parent_tag_caption"] = "root";
			$arr["saved_arguement_table"] = $saved_arguement_table;
			$arr["saved_subtag_table"] = $saved_subtag_table;
			$this->subt_subt($arr);
		}
	}

	function _get_import_log_conf_table($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];

		$t->define_field(array(
			"name" => "form_field",
			"caption" => t("S&uuml;ndmuse vormi v&auml;li"),
		));
		$t->define_field(array(
			"name" => "action_source",
			"caption" => t("Allikas on muudetud (AWs mitte)"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "action_aw",
			"caption" => t("AWs on s&uuml;ndmust muudetud (v&otilde;ib-olla ka allikas)"),
			"align" => "center"
		));

		$event_form = obj($arr["obj_inst"]->prop("event_form"));
		$event_form_inst = $event_form->instance();
		$props = $event_form_inst->get_props_from_cfgform(array(
			"id" => $event_form->id(),
		));

		$options = array(
//			"log" => t("Muudatus allikas logitakse"),
			"aut" => t("Muudatus allikas viiakse automaatselt l&auml;bi ka AWs"),
			"ign" => t("Muudatust allikas ignoreeritakse"),
		);

		$saved_conf_source = $arr["obj_inst"]->meta("log_conf_source");
		$saved_conf_aw = $arr["obj_inst"]->meta("log_conf_aw");

		foreach($props as $prop)
		{
			$prop["caption"] = (empty($prop["caption"])) ? $prop["name"] : $prop["caption"];
			$t->define_data(array(
				"form_field" => $prop["caption"],
				"action_source" => html::select(array(
					"name" => "log_conf_source[".$prop["name"]."]",
					"options" => $options,
					"selected" => $saved_conf_source[$prop["name"]],
				)),
				"action_aw" => html::select(array(
					"name" => "log_conf_aw[".$prop["name"]."]",
					"options" => $options,
					"selected" => $saved_conf_aw[$prop["name"]],
				)),
			));
		}
	}

	function _get_import_log_conf_sector_table($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];

		$t->define_field(array(
			"name" => "form_field",
			"caption" => t("Valdkonna vormi v&auml;li"),
		));
		$t->define_field(array(
			"name" => "action_source",
			"caption" => t("Allikas on muudetud (AWs mitte)"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "action_aw",
			"caption" => t("AWs on s&uuml;ndmust muudetud (v&otilde;ib-olla ka allikas)"),
			"align" => "center"
		));

		$props = array(
			array(
				"name" => "tegevusala",
				"caption" => t("Tegevusala nimetus"),
			),
			array(
				"name" => "comment",
				"caption" => t("Kirjeldus"),
			),
		);

		$options = array(
//			"log" => t("Muudatus allikas logitakse"),
			"aut" => t("Muudatus allikas viiakse automaatselt l&auml;bi ka AWs"),
			"ign" => t("Muudatust allikas ignoreeritakse"),
		);

		$saved_conf_source = $arr["obj_inst"]->meta("log_conf_sector_source");
		$saved_conf_aw = $arr["obj_inst"]->meta("log_conf_sector_aw");

		foreach($props as $prop)
		{
			$prop["caption"] = (empty($prop["caption"])) ? $prop["name"] : $prop["caption"];
			$t->define_data(array(
				"form_field" => $prop["caption"],
				"action_source" => html::select(array(
					"name" => "log_conf_sector_source[".$prop["name"]."]",
					"options" => $options,
					"selected" => $saved_conf_source[$prop["name"]],
				)),
				"action_aw" => html::select(array(
					"name" => "log_conf_sector_aw[".$prop["name"]."]",
					"options" => $options,
					"selected" => $saved_conf_aw[$prop["name"]],
				)),
			));
		}
	}

	function _get_import_log_conf_location_table($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];

		$t->define_field(array(
			"name" => "form_field",
			"caption" => t("Toimumiskoha vormi v&auml;li"),
		));
		$t->define_field(array(
			"name" => "action_source",
			"caption" => t("Allikas on muudetud (AWs mitte)"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "action_aw",
			"caption" => t("AWs on s&uuml;ndmust muudetud (v&otilde;ib-olla ka allikas)"),
			"align" => "center"
		));

		$props = array(
			array(
				"name" => "name",
				"caption" => t("Nimi"),
			),
			array(
				"name" => "comment",
				"caption" => t("Kirjeldus"),
			),
		);

		$options = array(
//			"log" => t("Muudatus allikas logitakse"),
			"aut" => t("Muudatus allikas viiakse automaatselt l&auml;bi ka AWs"),
			"ign" => t("Muudatust allikas ignoreeritakse"),
		);

		$saved_conf_source = $arr["obj_inst"]->meta("log_conf_location_source");
		$saved_conf_aw = $arr["obj_inst"]->meta("log_conf_location_aw");

		foreach($props as $prop)
		{
			$prop["caption"] = (empty($prop["caption"])) ? $prop["name"] : $prop["caption"];
			$t->define_data(array(
				"form_field" => $prop["caption"],
				"action_source" => html::select(array(
					"name" => "log_conf_location_source[".$prop["name"]."]",
					"options" => $options,
					"selected" => $saved_conf_source[$prop["name"]],
				)),
				"action_aw" => html::select(array(
					"name" => "log_conf_location_aw[".$prop["name"]."]",
					"options" => $options,
					"selected" => $saved_conf_aw[$prop["name"]],
				)),
			));
		}
	}

	function _get_import_log_conf_time_table($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];

		$t->define_field(array(
			"name" => "form_field",
			"caption" => t("Toimumisaja vormi v&auml;li"),
		));
		$t->define_field(array(
			"name" => "action_source",
			"caption" => t("Allikas on muudetud (AWs mitte)"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "action_aw",
			"caption" => t("AWs on s&uuml;ndmust muudetud (v&otilde;ib-olla ka allikas)"),
			"align" => "center"
		));

		$props = array(
			array(
				"name" => "name",
				"caption" => t("Nimi"),
			),
			array(
				"name" => "comment",
				"caption" => t("Kommentaar"),
			),
			array(
				"name" => "start",
				"caption" => t("Algab"),
			),
			array(
				"name" => "end",
				"caption" => t("L&otilde;peb"),
			),
			array(
				"name" => "place_id",
				"caption" => t("Toimumiskoha ID"),
			),
		);

		$options = array(
//			"log" => t("Muudatus allikas logitakse"),
			"aut" => t("Muudatus allikas viiakse automaatselt l&auml;bi ka AWs"),
			"ign" => t("Muudatust allikas ignoreeritakse"),
		);

		$saved_conf_source = $arr["obj_inst"]->meta("log_conf_time_source");
		$saved_conf_aw = $arr["obj_inst"]->meta("log_conf_time_aw");

		foreach($props as $prop)
		{
			$prop["caption"] = (empty($prop["caption"])) ? $prop["name"] : $prop["caption"];
			$t->define_data(array(
				"form_field" => $prop["caption"],
				"action_source" => html::select(array(
					"name" => "log_conf_time_source[".$prop["name"]."]",
					"options" => $options,
					"selected" => $saved_conf_source[$prop["name"]],
				)),
				"action_aw" => html::select(array(
					"name" => "log_conf_time_aw[".$prop["name"]."]",
					"options" => $options,
					"selected" => $saved_conf_aw[$prop["name"]],
				)),
			));
		}
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			// save data from xml configuration table
			case "xml_config_table":
				if (!empty($arr['request']['xml_conf']))
				{
					$arr['obj_inst']->set_meta("xml_conf", $arr['request']['xml_conf']);
				}
				if (!empty($arr['request']['xml_conf_time']))
				{
					$arr['obj_inst']->set_meta("xml_conf_time", $arr['request']['xml_conf_time']);
				}
				if (!empty($arr['request']['xml_conf_time_format']))
				{
					$arr['obj_inst']->set_meta("xml_conf_time_format", $arr['request']['xml_conf_time_format']);
				}
				if (!empty($arr['request']['xml_conf_place']))
				{
					$arr['obj_inst']->set_meta("xml_conf_place", $arr['request']['xml_conf_place']);
				}
				if (!empty($arr['request']['xml_conf_category']))
				{
					$arr['obj_inst']->set_meta("xml_conf_category", $arr['request']['xml_conf_category']);
				}
				break;

			case "category_config_table":
				if(!empty($arr["request"]["cat_conf"]))
				{
					$arr["obj_inst"]->set_meta("cat_conf", $arr["request"]["cat_conf"]);
				}
				break;

			case "import_log_conf_table":
				if(!empty($arr["request"]["log_conf_source"]))
				{
					$arr["obj_inst"]->set_meta("log_conf_source", $arr["request"]["log_conf_source"]);
				}
				if(!empty($arr["request"]["log_conf_aw"]))
				{
					$arr["obj_inst"]->set_meta("log_conf_aw", $arr["request"]["log_conf_aw"]);
				}
				break;

			case "import_log_conf_sector_table":
				if(!empty($arr["request"]["log_conf_sector_source"]))
				{
					$arr["obj_inst"]->set_meta("log_conf_sector_source", $arr["request"]["log_conf_sector_source"]);
				}
				if(!empty($arr["request"]["log_conf_sector_aw"]))
				{
					$arr["obj_inst"]->set_meta("log_conf_sector_aw", $arr["request"]["log_conf_sector_aw"]);
				}
				break;

			case "import_log_conf_time_table":
				if(!empty($arr["request"]["log_conf_time_source"]))
				{
					$arr["obj_inst"]->set_meta("log_conf_time_source", $arr["request"]["log_conf_time_source"]);
				}
				if(!empty($arr["request"]["log_conf_time_aw"]))
				{
					$arr["obj_inst"]->set_meta("log_conf_time_aw", $arr["request"]["log_conf_time_aw"]);
				}
				break;

			case "import_log_conf_location_table":
				if(!empty($arr["request"]["log_conf_location_source"]))
				{
					$arr["obj_inst"]->set_meta("log_conf_location_source", $arr["request"]["log_conf_location_source"]);
				}
				if(!empty($arr["request"]["log_conf_location_aw"]))
				{
					$arr["obj_inst"]->set_meta("log_conf_location_aw", $arr["request"]["log_conf_location_aw"]);
				}
				break;
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

	////
	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	////
	// !this shows the object. not strictly necessary, but you'll probably need it, it is used by parse_alias
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	function it_is_int($s)
	{
		for($a = 0; $a < strlen($s); $a++)
		{
			if($s[$a] != "0" && $s[$a] != "1" && $s[$a] != "2" && $s[$a] != "3" && $s[$a] != "4" && $s[$a] != "5" && $s[$a] != "6" && $s[$a] != "7" && $s[$a] != "8" && $s[$a] != "9")
			{
				return false;
			}
		}
		return true;
	}

	/////////////
	// Parameters:
	// - UD_xml_source_id: (int) the id of an XML source
	// - UD_start_timestamp: (int) seconds since the Unix Epoch (January 1 1970 00:00:00 GMT).
	//
	function load_xml_content($arr)
	{
		if (!is_oid($arr['UD_xml_source_id']))
		{
			return false;
		}
		$source = obj($arr['UD_xml_source_id']);
		$xml_file_url = $source->prop("url");

		if (empty($xml_file_url))
		{
			return false;
		}

		// The url might me something like http://....?par=var
		$url_params = (strstr($xml_file_url, "?") != "") ? "&" : "?";

		$start_timestamp = $source->prop("start_timestamp");
		$start_timestamp_unix = $source->prop("start_timestamp_unix");
		$end_timestamp = $source->prop("end_timestamp");
		$end_timestamp_unix = $source->prop("end_timestamp_unix");

		// Language
		$url_params .= (!empty($arr["UD_lang_param"]) && !empty($arr["UD_lang_value"])) ? $arr["UD_lang_param"] . "=" . $arr["UD_lang_value"] . "&" : "";

		// Start timestamp
		$url_params .= (!empty($start_timestamp) && !empty($arr["UD_start_timestamp"])) ? $start_timestamp . "=" . $arr["UD_start_timestamp"] . "&" : "";
		if($arr["UD_past_length"] > 0)
		{
			$url_params .= (!empty($start_timestamp_unix) && !empty($arr["UD_start_timestamp_unix"])) ? $start_timestamp_unix . "=" . (time() - $arr["UD_past_length"] * 24 * 3600) . "&" : "";
		}
		else
		{
			$url_params .= (!empty($start_timestamp_unix) && !empty($arr["UD_start_timestamp_unix"])) ? $start_timestamp_unix . "=" . $arr["UD_start_timestamp_unix"] . "&" : "";
		}
//		$url_params .= (!empty($start_timestamp_unix) && !empty($arr["UD_start_timestamp_unix"])) ? $start_timestamp_unix . "=" . mktime(15, 0, 0, 12, 14, 2007) . "&" : "";

		// End timestamp
		$url_params .= (!empty($end_timestamp)) ? $end_timestamp . "=" . date("YmdHis", strtotime("+".$arr["UD_future_length"]." days")) . "&" : "";
		$url_params .= (!empty($end_timestamp_unix)) ? $end_timestamp_unix . "=" . (time() + (3600*24*$arr["UD_future_length"])) . "&" : "";
//		$url_params .= (!empty($end_timestamp_unix)) ? $end_timestamp_unix . "=" . mktime(1, 0, 0, 12, 14, 2007) . "&" : "";

		$category = $source->prop("category");
		$url_params .= (!empty($category) && !empty($arr["UD_category"])) ? $category . "=" . $arr["UD_category"] . "&" : "";

		// The last ? or & is always unnecessary
		$url_params = substr($url_params, 0, strlen($url_params) - 1);

		print "<br> &nbsp; &nbsp; - URL used: ". $xml_file_url.$url_params . "<br><br>";
		$f = fopen($xml_file_url.$url_params, "r");
		if ($f === false)
		{
			return false;
		}
		while (!feof($f))
		{
			$xml_file_content .= fread($f, 4096);
		}
		fclose($f);

		if($source->prop("encoding") && $source->prop("encoding") != "UTF-8")
			$xml_file_content = iconv($source->prop("encoding"), "UTF-8", $xml_file_content);

		$xml_file_content = str_replace("&", "&amp;", $xml_file_content);
		$xml_file_content = str_replace("", "'", $xml_file_content);
		$xml_file_content = ereg_replace("<!--.*-->", "", $xml_file_content);

		return parse_xml_def(array(
			"xml" => $xml_file_content,
		));
	}

	/**
		@attrib name=ignore
	**/
	function ignore($arr)
	{
		$o = obj($arr["id"]);
		// The field where the data of ignored fields is saved
		if (!is_array($arr["sel"]) && is_array($arr["check"]))
		{
			$arr["sel"] = $arr["check"];
		}
		foreach(safe_array($arr["sel"]) as $ign_obj)
		{
			$obj = obj($ign_obj);

			$event = obj($obj->parent());
			$ignore_fields = $event->meta("igno_fields");
			$ignore_fields .= "," . $obj->prop("field");
			$event->set_meta("igno_fields", $ignore_fields);
			$event->save();

			$obj->delete();
		}
		return  $arr["post_ru"];
	}

	/**
		@attrib name=auto_change
	**/
	function auto_change($arr)
	{
		$o = obj($arr["id"]);
		// The field where the data of automatically changed fields is saved
		if (!is_array($arr["sel"]) && is_array($arr["check"]))
		{
			$arr["sel"] = $arr["check"];
		}
		foreach(safe_array($arr["sel"]) as $auto_obj)
		{
			$obj = obj($auto_obj);

			$event = obj($obj->parent());
			$auto_fields = $event->meta("auto_fields");
			$auto_fields .= "," . $obj->prop("field");
			if($obj->meta("trans_lang") == "en")
			{
				$all_vals = $event->meta("translations");
				$all_vals[$imp_lang_id][$obj->prop("field")] = $obj->prop("content");
				$event->set_meta("translations", $all_vals);
			}
			else
			{
				$event->set_prop($obj->prop("field"), $obj->prop("content"));
			}
			$event->set_meta("auto_fields", $auto_fields);
			$event->save();

			$obj->delete();
		}
		return  $arr["post_ru"];
	}

	/**
		@attrib name=make_changes
	**/
	function make_changes($arr)
	{
		$o = obj($arr["id"]);
		if (!is_array($arr["sel"]) && is_array($arr["check"]))
		{
			$arr["sel"] = $arr["check"];
		}
		foreach(safe_array($arr["sel"]) as $cha_obj)
		{
			$obj = obj($cha_obj);

			$event = obj($obj->parent());
			if($obj->meta("trans_lang") == "en")
			{
				$all_vals = $event->meta("translations");
				$all_vals[$imp_lang_id][$obj->prop("field")] = $obj->prop("content");
				$event->set_meta("translations", $all_vals);
			}
			else
			{
				$event->set_prop($obj->prop("field"), $obj->prop("content"));
			}
			$event->save();

			$obj->delete();
		}
		return  $arr["post_ru"];
	}

	function parse_part_of_date($format, $attr_value, $event_time_data, $time_i, $mode)
	{
		$prefix = ($mode < 4) ? "start_" : "end_";
		if(empty($format))
		{
			if($mode % 3 != 2)
			{
				$event_time_data[$time_i][$prefix."hour"] = date("H", $attr_value);
				$event_time_data[$time_i][$prefix."min"] = date("i", $attr_value);
				$event_time_data[$time_i][$prefix."sec"] = date("s", $attr_value);
			}

			if($mode % 3 != 0)
			{
				$event_time_data[$time_i][$prefix."year"] = date("Y", $attr_value);
				$event_time_data[$time_i][$prefix."mon"] = date("m", $attr_value);
				$event_time_data[$time_i][$prefix."day"] = date("d", $attr_value);
			}
		}
		else
		{
			if($mode % 3 != 2)
			{
				if(!(substr($attr_value, strpos($format, "hh")) === false || strpos($format, "hh") === false))
				{
					$event_time_data[$time_i][$prefix."hour"] = substr($attr_value, strpos($format, "hh"), 2);
				}
				if(!(substr($attr_value, strpos($format, "mm")) === false || strpos($format, "mm") === false))
				{
					$event_time_data[$time_i][$prefix."min"] = substr($attr_value, strpos($format, "mm"), 2);
				}
				if(!(substr($attr_value, strpos($format, "ss")) === false || strpos($format, "ss") === false))
				{
					$event_time_data[$time_i][$prefix."sec"] = substr($attr_value, strpos($format, "ss"), 2);
				}
			}

			if($mode % 3 != 0)
			{
				if(!(substr($attr_value, strpos($format, "aaaa")) === false || strpos($format, "aaaa") === false))
				{
					$event_time_data[$time_i][$prefix."year"] = substr($attr_value, strpos($format, "aaaa"), 4);
				}
				if(!(substr($attr_value, strpos($format, "kk")) === false || strpos($format, "kk") === false))
				{
					$event_time_data[$time_i][$prefix."mon"] = substr($attr_value, strpos($format, "kk"), 2);
				}
				if(!(substr($attr_value, strpos($format, "pp")) === false || strpos($format, "pp") === false))
				{
					$event_time_data[$time_i][$prefix."day"] = substr($attr_value, strpos($format, "pp"), 2);
				}
			}
		}
	}

	function handle_date($event_time_data, $time_i, $saved_xml_conf_time, $saved_xml_conf_time_format, $xml_source, $curtag, $attr, $attr_value)
	{
		/*
		arr($event_time_data);
		arr($time_i);
		arr($saved_xml_conf_time);
		arr($xml_source);
		arr($curtag);
		arr($attr);
		arr($attr_value);
		/**/
		if(strlen($attr) > 0)
			$postfix = "_args".$attr;
		else
			$postfix = "";

		if(!empty($event_time_data[$time_i][$saved_xml_conf_time[$xml_source->id()."_".$curtag.$postfix]]))
		{
			$time_i++;
			$event_time_data[$time_i] = array(
				"start_hour" => 0,
				"start_min" => 0,
				"start_sec" => 0,
				"start_year" => 0,
				"start_mon" => 0,
				"start_day" => 0,
				"end_hour" => 0,
				"end_min" => 0,
				"end_sec" => 0,
				"end_year" => 0,
				"end_mon" => 0,
				"end_day" => 0,
			);
		}

		$format = $saved_xml_conf_time_format[$xml_source->id()."_".$curtag.$postfix];
//		arr($format);

		switch($saved_xml_conf_time[$xml_source->id()."_".$curtag.$postfix])
		{
			case "start":
				$this->parse_part_of_date(&$format, &$attr_value, &$event_time_data, &$time_i, 1);
				break;

			case "start_date":
				$this->parse_part_of_date(&$format, &$attr_value, &$event_time_data, &$time_i, 2);
				break;

			case "start_time":
				// Not a nice way to handle this problem.
				$vl = str_replace(", ", ",", $attr_value);
				$vl = explode(",", $vl);
				$c = 0;
				foreach($vl as $attr_value)
				{
					$this->parse_part_of_date(&$format, &$attr_value, &$event_time_data, $time_i + $c, 3);
					$c++;
				}
				break;

			case "end":
				$this->parse_part_of_date(&$format, &$attr_value, &$event_time_data, &$time_i, 4);
				break;

			case "end_date":
				$this->parse_part_of_date(&$format, &$attr_value, &$event_time_data, &$time_i, 5);
				break;

			case "end_time":
				$this->parse_part_of_date(&$format, &$attr_value, &$event_time_data, &$time_i, 6);
				break;
		}
		$event_time_data[$time_i][$saved_xml_conf_time[$xml_source->id()."_".$curtag.$postfix]] = trim($attr_value, " \t\n\r\0");
	}

	function make_piletimaailm_links($v, $o, $pmlinks)
	{
		// I find it the easiest way to treat spaces and line breaks equally.
		$v = str_replace("\n", " ", $v);
		foreach(explode(" ", $v) as $s)
		{
			// These characters are punctuation marks rather than a part of the piletimaailm.com url.
			$s = trim($s, ",.)(:;?!-");
			if(strpos($s, "piletimaailm.com") === false)
				continue;

			// We don't want any e-mail adresses here.
			if(ereg("[[:alnum:]]+@[[:alnum:]]+\.[[:alnum:]]+", $s))
				continue;

			// $pmlinks is an array of piletimaailm.com links already connected to the object.
			if(!in_array($s, $pmlinks))
			{
				$link = new object;
				$link->set_class_id(CL_EXTLINK);
				$link->set_parent($o->id());
				$link->set_prop("name", $s);
				$link->set_prop("url", $s);
				$link->save();
				$pmlinks[$link->id()] = $s;
				$o->connect(array(
					"to" => $link->id(),
					"reltype" => "RELTYPE_URL",
				));
			}
		}
	}

//	function process_import_data($arr, $imps, $o, $xml_source_id, $class_id, $dirs, $import_orig = true)
	function process_import_data($arr, $imps, $o, $xml_source_id, $class_id, $dirs, $imp_lang_id)
	{
		/*
		arr($imps["event"]);
		exit;
		*/
		$li = $o->prop("original_lang");
		$import_orig = ($li == $imp_lang_id);
		$lg = get_instance("languages");
		$lg_cfg = $lg->cfg;
		$lg_list = $lg_cfg["list"];
		$lg = $lg->get_list();

		$orig_val = (!$import_orig) ? $imp_lang_id."_orig_val_" : "orig_val_";

		$saved_xml_conf = $o->meta("xml_conf");
		$saved_xml_conf_time = $o->meta("xml_conf_time");
		$saved_xml_conf_time_format = $o->meta("xml_conf_time_format");
		$saved_xml_conf_place = $o->meta("xml_conf_place");
		$saved_xml_conf_category = $o->meta("xml_conf_category");
		$translatable_fields = $o->prop("translatable_fields");
		$last_import = $o->meta("last_import");
		// What do we do when some of the events have been updated/changed? Here's the conf for those situations:
		$saved_conf_source = $o->meta("log_conf_source");
		$saved_conf_aw = $o->meta("log_conf_aw");
		$saved_conf_source_location = $o->meta("log_conf_source");
		$saved_conf_aw_location = $o->meta("log_conf_aw");
		$saved_conf_source_sector = $o->meta("log_conf_source");
		$saved_conf_aw_sector = $o->meta("log_conf_aw");
		$no_event_time_objs = $o->no_event_time_objs;

		$xml_source = obj($xml_source_id);
		if("tijolatie" == $xml_source->prop("tag_lang") && !$import_orig)
			return false;

		print " &nbsp; - <strong>[STARTED]</strong> " . $xml_source->name() . " [".t(strtoupper($lg[$imp_lang_id]))."]" . "<br>";
		flush();

		// <  POSSIBLE ERRORS  >

		if(!is_oid($xml_source->prop("external_system_event")))
		{
			die(t("External system for events not set!"));
		}

		if(!is_oid($xml_source->prop("external_system_location")))
		{
			die(t("External system for locations not set!"));
		}

		if(!is_oid($xml_source->prop("external_system_location")))
		{
			die(t("External system for categories (aka sectors) not set!"));
		}

		if(!is_oid($xml_source->prop("external_system_event_time")))
		{
			die(t("External system for event times not set!"));
		}

		// </  POSSIBLE ERRORS  >

		// The tag for event, so we can track when an event is coplete.
		$tag_event = $xml_source->prop("tag_event");
		// The tag for the event id, so we can have something to identify it by.
		$tag_id = $xml_source->prop("tag_id");
		$tag_public_event = $xml_source->prop("tag_public_event");
		$val_public_event = $xml_source->prop("val_public_event");
		$tag_delete_event = $xml_source->prop("tag_delete_event");
		$val_delete_event = $xml_source->prop("val_delete_event");
		$tag_delete_time = $xml_source->prop("tag_delete_time");
		$val_delete_time = $xml_source->prop("val_delete_time");
		// The tag and parameter for language, so we know how to ask for translations of events (locations and sectors)
		$tag_lang = $xml_source->prop("tag_lang");
		$param_lang = $xml_source->prop("language");
		// Languages we can get the translations in.
		$saved_language_table = $xml_source->meta("language_table");
		// The IDs of external systems. We need those to check for objects previously imported.
		$ext_sys_event = $xml_source->prop("external_system_event");
		$ext_sys_location = $xml_source->prop("external_system_location");
		$ext_sys_sector = $xml_source->prop("external_system_sector");
		$ext_sys_event_time = $xml_source->prop("external_system_event_time");
		// The names of external systems. We need those to check for objects previously imported.
		$ext_sys_event_obj = obj($xml_source->prop("external_system_event"));
		$ext_sys_event_name = $ext_sys_event_obj->name();
		$ext_sys_location_obj = obj($xml_source->prop("external_system_location"));
		$ext_sys_location_name = $ext_sys_location_obj->name();
		$ext_sys_sector_obj = obj($xml_source->prop("external_system_sector"));
		$ext_sys_sector_name = $ext_sys_sector_obj->name();
		$ext_sys_event_time_obj = obj($xml_source->prop("external_system_event_time"));
		$ext_sys_event_time_name = $ext_sys_event_time_obj->name();

		// We need to know if the event time is deleted when we start saving the data. So that's how.
		$i_conf = 0;
		foreach($tag_delete_time as $tag_delete_time_e)
		{
			$saved_xml_conf_time[$xml_source->id()."_".$tag_delete_time_e] = "delete_".$i_conf;
			$i_conf++;
		}
		$i_conf = 0;
		foreach($tag_delete_event as $tag_delete_event_e)
		{
			// If the event is deleted we skip it. = iteidwsi.
			$saved_xml_conf[$xml_source->id()."_".$tag_delete_event_e] = "iteidwsi_".$i_conf;
			$i_conf++;
		}
		$i_conf = 0;
		foreach($tag_public_event as $tag_public_event_e)
		{
			// If the event is not public we skip it. = iteinpwsi.
			$saved_xml_conf[$xml_source->id()."_".$tag_public_event_e] = "iteinpwsi_".$i_conf;
			$i_conf++;
		}
		// We need to save the data about the language somewhere. (So far only used for V2lisministeeriumi kultuurikalender)
		if($tag_lang != "tlidbup" && $tag_lang != "tijolatie")
		{
			// There are more than one languages and we save 'em here.
			$saved_xml_conf[$xml_source->id()."_".$tag_lang] = "tamtolawseh";
		}

		$saved_lvl_table = $xml_source->meta("level_table");
		foreach($saved_lvl_table as $lvl_nr => $lvl_val)
		{
			$lvl_val = str_replace(" ", "", $lvl_val);
			if(strlen($lvl_val) == 0)
				continue;
			$lvl_vals = explode(",", $lvl_val);
			foreach($lvl_vals as $lvl_single_val)
			{
				$saved_lvl_conf[$lvl_single_val] = $lvl_nr;
			}
		}


		// <  LOAD XML CONTENT  >

		$load_xml_content_params = array(
			"UD_xml_source_id" => $xml_source->id(),
			"UD_start_timestamp" => "".date("YmdHis", $o->meta("last_import")),
			"UD_start_timestamp_unix" => $o->meta("last_import"),
			"UD_future_length" => $o->prop("future_length"),
			"UD_past_length" => $o->prop("past_length"),
		);

		$load_xml_content_params["UD_lang_param"] = $param_lang;
		$load_xml_content_params["UD_lang_value"] = $saved_language_table[$imp_lang_id];

		// In case we have to import all the events...
		if($o->prop("import_events_all"))
		{
			$load_xml_content_params["UD_start_timestamp"] = "19700101000000";
			$load_xml_content_params["UD_start_timestamp_unix"] = 1;
		}
		$xml_content = $this->load_xml_content($load_xml_content_params);

		// </  LOAD XML CONTENT  >

		if ($xml_content === false)
		{
			print " &nbsp; &nbsp; - <strong>Could not get XML data!</strong><br>";
		}

		foreach($xml_content[0] as $v)
		{
			// I have to solve this problem. What if someone uses <event_0>, <event_1> and so on..
			// Start hacking.
			if(strlen(str_replace("#", "", $tag_event)) != strlen($tag_event))
			{
				$curtag = "";
				for($a = 0; $a <= $v["level"] - 1; $a++)
				{
					if(!empty($curtag))
					{
						$curtag .= "_";
					}
					$curtag .= $xml_tag_levels[$a];
				}
				if(strlen($curtag."_".$v["tag"]) > strpos($tag_event, "#"))
				{
					if($this->it_is_int(substr($curtag."_".$v["tag"], strpos($tag_event, "#"))) || substr($curtag."_".$v["tag"], strpos($tag_event, "#")) == "0")
					{
						$v["tag"] = substr($curtag."_".$v["tag"], strlen($curtag."_"), strpos($tag_event, "#") - strlen($curtag."_"))."#";
					}
				}
			}
			// End on hacking.

			$xml_tag_levels[$v["level"]] = $v["tag"];

			// We put together the name for the current tag, including its parent tags
			$curtag = "";
			for($a = 0; $a <= $v["level"]; $a++)
			{
				if(!empty($curtag))
				{
					$curtag .= "_";
				}
				$curtag .= $xml_tag_levels[$a];
			}

			if($curtag == $tag_event && $v["type"] == "open")
			{	// In case we start collecting information for new event, we unset all the previous data.
				$event_data = array();
				unset($event_id);

				$event_time_data = array();
				$event_place_data = array();
				$event_category_data = array();
				$time_i = 0;
				$place_i = 0;
				$category_i = 0;
				$event_time_data[$time_i] = array(
					"start_hour" => 0,
					"start_min" => 0,
					"start_sec" => 0,
					"start_year" => 0,
					"start_mon" => 0,
					"start_day" => 0,
					"end_hour" => 0,
					"end_min" => 0,
					"end_sec" => 0,
					"end_year" => 0,
					"end_mon" => 0,
					"end_day" => 0,
				);
			}

			if(!($curtag == $tag_event && $v["type"] == "close"))
			{
				$cft = $saved_xml_conf_time[$xml_source->id()."_".$curtag];
				$cfp = $saved_xml_conf_place[$xml_source->id()."_".$curtag];
				$cfc = $saved_xml_conf_category[$xml_source->id()."_".$curtag];
				if(!empty($saved_xml_conf[$xml_source->id()."_".$curtag]) && $saved_xml_conf[$xml_source->id()."_".$curtag] != "do_not_save_into_db")
				{
					$event_data[$saved_xml_conf[$xml_source->id()."_".$curtag]] = trim($v["value"], " \t\n\r\0");
				}
				if(!empty($cfp) && $cfp != "do_not_save_into_db")
				{
					if(!empty($event_place_data[$place_i][$cfp]))
					{
						$place_i++;
					}
					$event_place_data[$place_i][$cfp] = trim($v["value"], " \t\n\r\0");
				}
				if(!empty($cfc) && $cfc != "do_not_save_into_db")
				{
					if(!empty($event_category_data[$category_i][$cfc]))
					{
						$category_i++;
					}
					$event_category_data[$category_i][$cfc] = trim($v["value"], " \t\n\r\0");
				}
				if(!empty($cft) && $cft != "do_not_save_into_db")
				{
					$this->handle_date(&$event_time_data, &$time_i, &$saved_xml_conf_time, &$saved_xml_conf_time_format, &$xml_source, &$curtag, "", &$v["value"]);
				}
				if($curtag == $tag_id)
				{
					$event_id = trim($v["value"], " \t\n\r\0");
				}
				if(!empty($v["attributes"]))
				{
					foreach($v["attributes"] as $attr => $attr_value)
					{
						if(strlen($attr) > 0)
							$postfix = "_args".$attr;
						else
							$postfix = "";
						$attr_value = trim($attr_value, " \t\n\r\0");

						if(!empty($saved_xml_conf[$xml_source->id()."_".$curtag.$postfix]) && $saved_xml_conf[$xml_source->id()."_".$curtag.$postfix] != "do_not_save_into_db")
						{
							$event_data[$saved_xml_conf[$xml_source->id()."_".$curtag.$postfix]] = $attr_value;
						}
						if(!empty($saved_xml_conf_place[$xml_source->id()."_".$curtag.$postfix]) && $saved_xml_conf_place[$xml_source->id()."_".$curtag.$postfix] != "do_not_save_into_db")
						{
							if(!empty($event_place_data[$place_i][$saved_xml_conf_place[$xml_source->id()."_".$curtag.$postfix]]))
							{
								$place_i++;
							}
							$event_place_data[$place_i][$saved_xml_conf_place[$xml_source->id()."_".$curtag.$postfix]] = $attr_value;
						}
						if(!empty($saved_xml_conf_category[$xml_source->id()."_".$curtag.$postfix]) && $saved_xml_conf_category[$xml_source->id()."_".$curtag.$postfix] != "do_not_save_into_db")
						{
							if(!empty($event_category_data[$category_i][$saved_xml_conf_category[$xml_source->id()."_".$curtag.$postfix]]))
							{
								$category_i++;
							}
							$event_category_data[$category_i][$saved_xml_conf_category[$xml_source->id()."_".$curtag.$postfix]] = $attr_value;
						}
						if(!empty($saved_xml_conf_time[$xml_source->id()."_".$curtag.$postfix]) && $saved_xml_conf_time[$xml_source->id()."_".$curtag.$postfix] != "do_not_save_into_db")
						{
							$this->handle_date(&$event_time_data, &$time_i, &$saved_xml_conf_time, &$saved_xml_conf_time_format, &$xml_source, &$curtag, &$attr, &$attr_value);
						}
						if($curtag.$postfix == $tag_id)
						{
							$event_id = trim($attr_value, " \t\n\r\0");
						}
					}
				}
			}
			else
			{
				if(empty($event_id))
				{
					print " &nbsp; &nbsp; - ";
					print " - No ID. Skipped.<br>";
				}
				print " &nbsp; &nbsp; - ";
				// If the event is not public, we skip it.
				$not_published = false;
				for($i_conf = 0; isset($event_data["iteinpwsi_".$i_conf]); $i_conf++)
				{
					if($val_public_event == $event_data["iteinpwsi"])
					{
						$not_published = true;
						print $val_public_event." == ".$event_data["iteinpwsi"]."<br>";
					}
					unset($event_data["iteinpwsi_".$i_conf]);
				}
				$deleted = false;
				for($i_conf = 0; isset($event_data["iteidwsi_".$i_conf]); $i_conf++)
				{
					if($val_delete_event == $event_data["iteidwsi_".$i_conf])
					{
						$deleted = true;
					}
					unset($event_data["iteidwsi_".$i_conf]);
				}
				if($not_published)
				{
					print "NOT PUBLIC! ".$event_data["name"];
					if(array_key_exists($event_id, $imps["event"][$ext_sys_event]))
					{
						$event_obj = new object($imps["event"][$ext_sys_event][$event_id]);
						$event_obj->set_prop("published", 0);
						$event_obj->save();
						print " - Property(published) = 0.<br>";
					}
					else
					{
						print " - No such event on our side. Skipped.<br>";
						continue;
					}
				}
				if($deleted)
				{
					print "DELETED! ".$event_data["name"];
					if(array_key_exists($event_id, $imps["event"][$ext_sys_event]))
					{
						$event_obj = new object($imps["event"][$ext_sys_event][$event_id]);
						$event_obj->delete();
						print " - Deleted.<br>";
						continue;
					}
					else
					{
						print " - No such event on our side. Skipped.<br>";
						continue;
					}
				}
				if(isset($event_data["tamtolawseh"]))
				{
					// We only import the languages we're told to import with $imp_lang_id. ;-)
					if($event_data["tamtolawseh"] != $saved_language_table[$imp_lang_id])
					{
						continue;
//						$import_orig = false;
					}
					unset($event_data["tamtolawseh"]);
				}

				// saving the event data
				if(!array_key_exists($event_id, $imps["event"][$ext_sys_event]))
				{ // new event
					$all_vals[$imp_lang_id] = array();

					print "<strong>[ new ][".$lg[$imp_lang_id]."] </strong>";
					$event_obj = new object;
					if(!$import_orig)
					{
						$event_obj->set_meta("trans_".$imp_lang_id."_status", 0);
					}
					$event_obj->set_lang_id($li);
					$event_obj->set_class_id($class_id);
					$event_obj->set_parent($dirs["event"]);		// Kaust, kuhu syndmusi kirjutatakse (event_manager)
					// By default new events are not public.
					$event_obj->set_prop("published", 0);
					// I might need to connect the event object. So I have to save it here. Hopefully it won't cause major impact on cache size.
					$event_obj->save();
					$pmlinks = array();
					if(!empty($event_data["level"]))
					{
						if(array_key_exists($event_data["level"], $saved_lvl_conf))
						{
							$event_obj->set_prop("level", $saved_lvl_conf[$event_data["level"]]);
						}
						unset($event_data["level"]);
					}
					foreach($event_data as $key => $value)
					{
						if(!empty($key) && !empty($value))
						{
							if(!(strpos($value, "piletimaailm.com") === false))
							{
								$this->make_piletimaailm_links($value, &$event_obj, &$pmlinks);
							}

							//$value = $this->remove_crap($value, $imp_lang_id);
							$value = htmlspecialchars($value);
							$value = iconv("UTF-8", $lg_list[$imp_lang_id]["charset"]."//IGNORE", $value);
							if($import_orig)
							{
								// Importing the original content
//								print "Importing the original content -> ".$key."<br>";
								$event_obj->set_prop($key, $value);
								$event_obj->set_status(STAT_ACTIVE);
								$event_obj->set_meta($imp_lang_id."_orig_val_".$key, $imp_lang_id."_orig_val_");
							}
							else
							{
								// Importing translated content
//								print "Importing translated content -> ".$key."<br>";
								if(in_array($key, $translatable_fields))
								{
									$all_vals[$imp_lang_id][$key] = $value;
									$event_obj->set_meta("trans_".$imp_lang_id."_status", 1);
								}
								$event_obj->set_meta("orig_val_".$key, "orig_val_");
							}
							$event_obj->set_meta($orig_val.$key, $value);
						}
					}
					if(!$import_orig)
					{
//						print "Saving translations<br>";
						$event_obj->set_meta("translations", $all_vals);
					}
					$event_obj->set_meta("pmlinks", $pmlinks);
					$event_obj->save();
					print $event_data["name"].", ID - ".$event_obj->id()." [saved]<br>";
					flush();
					// We make a record of the event we just imported so we won't make a duplicate.
					$extent_obj = new object;
					$extent_obj->set_lang_id($li);
					$extent_obj->set_class_id(CL_EXTERNAL_SYSTEM_ENTRY);
					$extent_obj->set_parent($ext_sys_event);
					$extent_obj->set_name(sprintf(t("Siduss&uuml;steemi %s sisestus objektile %s"), $ext_sys_event_name, $event_obj->name()));
					$extent_obj->set_prop("ext_sys_id", $ext_sys_event);
					$extent_obj->set_prop("obj", $event_obj->id());
					$extent_obj->set_prop("value", $event_id);
					$extent_obj->save();
					$extent_obj->connect(array(
						"type" => "OBJ",
						"to" => $event_obj->id(),
					));
					$extent_obj->connect(array(
						"type" => "EXTSYS",
						"to" => $ext_sys_event,
					));
					$imps["event"][$ext_sys_event][$event_id] = $event_obj->id();
				}
				else
				{ // existing event
					$event_obj = new object($imps["event"][$ext_sys_event][$event_id]);
					print "[ --- ][".$lg[$imp_lang_id]."] ".$event_data["name"].", ID - ".$event_obj->id()."<br>";

					// Don't wanna lose any translations alreay imported or entered.
					$all_vals = $event_obj->meta("translations");

					$change_igno = $event_obj->meta("igno_fields");
					$change_igno = str_replace(" ", "", $change_igno);
					$change_igno = explode(",", $change_igno);

					$change_auto = $event_obj->meta("auto_fields");
					$change_auto = str_replace(" ", "", $change_auto);
					$change_auto = explode(",", $change_auto);
					$pmlinks = $event_obj->meta("pmlinks");

					foreach($event_data as $key => $value)
					{
						if(strpos($value, "piletimaailm.com") !== false)
						{
							$this->make_piletimaailm_links($value, &$event_obj, &$pmlinks);
						}

						//$value = $this->remove_crap($value, $imp_lang_id);
						$value = htmlspecialchars($value);
						$value = iconv("UTF-8", $lg_list[$imp_lang_id]["charset"]."//IGNORE", $value);
						if(!empty($key))
						{
							// If the content of the field has been modified manually, we use the $saved_conf_aw array.
							if($event_obj->meta($orig_val.$key) == $event_obj->prop($key) || $event_obj->meta($orig_val.$key) == $orig_val)
								$jumper = &$saved_conf_source;
							else
								$jumper = &$saved_conf_aw;

							// Check for any updated fields
							if(($import_orig && $event_obj->prop($key) != $value) || (!$import_orig && $all_vals[$imp_lang_id][$key] != $value))
							{
								// $jumper[$key] == "log"    is NOT IN USE
								// So it's not up-to-date!
								if($jumper[$key] == "log")
								{
									if(in_array($key, $change_auto) || !($o->prop("cb_log_changes")))
									{
										$event_obj->set_prop($key, $value);
										$event_obj->set_meta($orig_val.$key, $value);
										$event_obj->save();
										print " &nbsp; &nbsp; &nbsp; -";
										print "- property: ".$key." [changed]<br>";
									}
									else if(in_array($key, $change_igno))
									{
										print " &nbsp; &nbsp; &nbsp; -";
										print "- property: ".$key." [change ignored]<br>";
									}
									else
									{
										$log = new object;
										$log->set_lang_id($li);
										$log->set_parent($event_obj->id());
										$log->set_class_id(CL_IMPORT_LOG);
										$log->set_prop("name", "");
										$log->set_prop("field", $key);
										$log->set_prop("content", $value);
										$log->set_prop("timestamp", time());
										$log->save();
										print " &nbsp; &nbsp; &nbsp; -";
										print "- property: ".$key." [change logged]<br>";
									}
								}
								elseif($jumper[$key] == "aut")
								{
									if($import_orig)
									{
										//print "Importing original content -> ".$key."<br>";
										$event_obj->set_prop($key, $value);
										if(!empty($value))
										{
											$event_obj->set_status(STAT_ACTIVE);
										}
									}
									else
									{
										//print "Importing translated content -> ".$key."<br>";
										if(in_array($key, $translatable_fields))
										{
											$all_vals[$imp_lang_id][$key] = $value;
											$event_obj->set_meta("translations", $all_vals);
											if(!empty($value))
											{
												$event_obj->set_meta("trans_".$imp_lang_id."_status", 1);
											}
										}

									}
									// We update the original value field so next time it's changed we'll know, if it was changed in the source or in AW.
									$event_obj->set_meta($orig_val.$key, $value);
									$event_obj->save();
//									print $event_obj->name()."<br>";
									print " &nbsp; &nbsp; &nbsp; -";
									print "- property: ".$key." [changed]<br>";
								}
								else
								{
									print " &nbsp; &nbsp; &nbsp; -";
									print "- property: ".$key." [change ignored]<br>";
								}
							}
						}
					}
					$event_obj->set_meta("pmlinks", $pmlinks);
					$event_obj->save();
				}
				flush();
//				arr($event_obj->meta("translations"));

				//saving the event place data
				foreach($event_place_data as $event_place)
				{
//					if(!empty($event_place["id"]) || !empty($event_place["name"]))
					if(!empty($event_place["id"]))
					{
						if(array_key_exists($event_place["id"], $imps["location"][$ext_sys_location]))
						{
							$place_obj = obj($imps["location"][$ext_sys_location][$event_place["id"]]);
							$new = false;

							$all_vals = $place_obj->meta("translations");
						}
						else
						{
							$place_obj = new object;
							$place_obj->set_lang_id($li);
							$place_obj->set_class_id(CL_SCM_LOCATION);
							$place_obj->set_parent($dirs["location"]);
							print "<b>";
							$new = true;

							$all_vals = array();
						}
						$place_props = array("name", "comment");
						foreach($place_props as $place_prop)
						{
							if(!empty($event_place[$place_prop]))
							{
								// If the content of the field has been modified manually, we use the $saved_conf_aw array.
								if($place_obj->meta($orig_val.$place_prop) == $place_obj->prop($place_prop) || $place_obj->meta($orig_val.$place_prop) == $orig_val)
									$jumper = &$saved_conf_source_location;
								else
									$jumper = &$saved_conf_aw_location;

								// If this event was previously imported and we need to ignore the change, so be it.
								if($jumper[$place_prop] == "ign" && !$new)
									continue;

								if($import_orig)
								{
									$place_obj->set_prop($place_prop, $event_place[$place_prop]);
									if($new)
										$place_obj->set_meta($imp_lang_id."_orig_val_".$place_prop, $imp_lang_id."_orig_val_");
								}
								else
								{
									$all_vals[$imp_lang_id][$place_prop] = $event_place[$place_prop];
									if($new)
										$place_obj->set_meta("orig_val_".$place_prop, "orig_val_");
								}
								$place_obj->set_meta($orig_val.$place_prop, $event_place[$place_prop]);
							}
						}
						if(!$import_orig)
						{
							$place_obj->set_meta("translations", $all_vals);
						}
						$place_obj->save();
						print " &nbsp; &nbsp; &nbsp; -";
						print "- event location: ".$event_place["id"]." - ".$event_place["tegevusala"]." [saved]<br></b>";
						if($new)
						{
							// We make a record of the location we just imported so we won't make a duplicate.
							$extent_obj = new object;
							$extent_obj->set_lang_id($li);
							$extent_obj->set_class_id(CL_EXTERNAL_SYSTEM_ENTRY);
							$extent_obj->set_parent($ext_sys_location);
							$extent_obj->set_name(sprintf(t("Siduss&uuml;steemi %s sisestus objektile %s"), $ext_sys_location_name, $place_obj->name()));
							$extent_obj->set_prop("ext_sys_id", $ext_sys_location);
							$extent_obj->set_prop("obj", $place_obj->id());
							$extent_obj->set_prop("value", $event_place["id"]);
							$extent_obj->save();
							$extent_obj->connect(array(
								"type" => "OBJ",
								"to" => $place_obj->id(),
							));
							$extent_obj->connect(array(
								"type" => "EXTSYS",
								"to" => $ext_sys_location,
							));
							$imps["location"][$ext_sys_location][$event_place["id"]] = $place_obj->id();
						}
					}
				}

				//saving the event category (sector) data
				foreach($event_category_data as $event_category)
				{
					if(!empty($event_category["id_multiple"]))
					{
						$event_cat_ids = explode(",", $event_category["id_multiple"]);
						foreach($event_cat_ids as $event_category["id"])
						{
							if(empty($event_category["id"]))
								continue;
							if(array_key_exists($event_category["id"], $imps["sector"][$ext_sys_sector]))
							{
								$category_obj = obj($imps["sector"][$ext_sys_sector][$event_category["id"]]);
								$new = false;
								$new_event = false;

								$all_vals = $category_obj->meta("translations");
							}
							else
							{
								$category_obj = new object;
								$category_obj->set_lang_id($li);
								$category_obj->set_class_id(CL_CRM_SECTOR);
								$category_obj->set_parent($dirs["sector"]);
								$category_obj->set_status(STAT_ACTIVE);
								$category_obj->set_meta("EN_orig_val_sector", $imp_lang_id."_orig_val_");
								$category_obj->set_meta("orig_val_sector", "orig_val_");
								print "<b>";
								$new = true;
								$new_event = true;

								$all_vals = array();
							}
							// If we don't have a name for the sector, we use the external ID as a name.
							empty($event_category["tegevusala"]) ? $event_category["id"] : $event_category["tegevusala"];
							$event_category["name"] = $event_category["tegevusala"];

							$cat_props = array("tegevusala", "comment", "name");
							foreach($cat_props as $cat_prop)
							{
								if(!empty($event_category[$cat_prop]))
								{
									// If the content of the field has been modified manually, we use the $saved_conf_aw array.
									if($category_obj->meta($orig_val.$cat_prop) == $category_obj->prop($cat_prop) || $category_obj->meta($orig_val.$cat_prop) == $orig_val)
										$jumper = &$saved_conf_source_sector;
									else
										$jumper = &$saved_conf_aw_sector;

									// If this category was previously imported and we need to ignore the change, so be it.
									if($jumper[$cat_prop] == "ign" && !$new)
										continue;

									if($import_orig)
									{
										if($cat_prop == "name")
										{
											$category_obj->set_name($event_category[$cat_prop]);
										}
										else
										{
											$category_obj->set_prop($cat_prop, $event_category[$cat_prop]);
										}
										if($new)
											$category_obj->meta($imp_lang_id."_orig_val_".$cat_prop, $imp_lang_id."_orig_val_");
									}
									else
									{
										$all_vals[$imp_lang_id][$cat_prop] = $event_category[$cat_prop];
										if($new)
											$category_obj->meta("orig_val_".$cat_prop, "orig_val_");
									}
									$category_obj->set_meta($orig_val.$cat_prop, $event_category[$cat_prop]);
								}
							}
							if($import_orig)
								$category_obj->set_meta("translations", $all_vals);
							$category_obj->save();
							print " &nbsp; &nbsp; &nbsp; -";
							print "- event category (aka section): ".$event_category["id"]." - ".$event_category["tegevusala"]." [saved]";

							$sector_prop_val = $event_obj->prop("sector");
							$sector_orig_val = $event_obj->meta($orig_val."sector");

							if($sector_prop_val == $sector_orig_val || $sector_orig_val == $orig_val)
								$jumper = &$saved_conf_source_sector;
							else
								$jumper = &$saved_conf_aw_sector;

							if(!in_array($category_obj->id(), $sector_prop_val) && $jumper["sector"] != "ign" || $new_event)
							{
								$event_obj->connect(array(
									"to" => $category_obj->id(),
									"type" => "RELTYPE_SECTOR",
								));
								$sector_prop_val[$category_obj->id()] = $category_obj->id();
								$event_obj->set_prop("sector", $sector_prop_val);
								$event_obj->set_meta($orig_val."sector", $sector_prop_val);
								$event_obj->save();
								print "[connected]";
							}
							print "<br></b>";

							if($new)
							{
								// We make a record of the category (aka sector) we just imported so we won't make a duplicate.
								$extent_obj = new object;
								$extent_obj->set_lang_id($li);
								$extent_obj->set_class_id(CL_EXTERNAL_SYSTEM_ENTRY);
								$extent_obj->set_parent($ext_sys_sector);
								$extent_obj->set_name(sprintf(t("Siduss&uuml;steemi %s sisestus objektile %s"), $ext_sys_sector_name, $category_obj->name()));
								$extent_obj->set_prop("ext_sys_id", $ext_sys_sector);
								$extent_obj->set_prop("obj", $category_obj->id());
								$extent_obj->set_prop("value", $event_category["id"]);
								$extent_obj->save();
								$extent_obj->connect(array(
									"type" => "OBJ",
									"to" => $category_obj->id(),
								));
								$extent_obj->connect(array(
									"type" => "EXTSYS",
									"to" => $ext_sys_sector,
								));
								$imps["sector"][$ext_sys_sector][$event_category["id"]] = $category_obj->id();
							}
						}
					}
				}

				$times_done = array();
				$event_time_count = 0;
				foreach($event_time_data as $event_time)
				{
					$event_time_count++;
					$deleted = false;
					for($i_conf = 0; isset($event_time["delete_".$i_conf]); $i_conf++)
					{
						if($val_delete_time == $event_time["delete_".$i_conf])
						{
							$deleted = true;
						}
					}
					if($deleted)
					{
						print " &nbsp; &nbsp; &nbsp; -";
						print "DELETED EVENT TIME! ".$event_time["name"];
						if(array_key_exists($event_time["id"], $imps["time"][$ext_sys_event_time]))
						{
							$time_obj = obj($imps["time"][$ext_sys_event_time][$event_time["id"]]);
							$time_obj->delete();
							print " - deleted.<br>";
						}
						else
						{
							print " - No such event time on our side. Skipped.<br>";
						}
						continue;
					}
					$start_postfixes = array("start_mon", "start_day", "start_year");
					foreach($start_postfixes as $start_postfix)
					{
						if(empty($event_time[$start_postfix]))
							$event_time[$start_postfix] = $tmp[$start_postfix];
						else
							$tmp[$start_postfix] = $event_time[$start_postfix];
					}

					if($event_time["start_day"] != 0 && $event_time["start_mon"] != 0)
					{
						if($no_event_time_objs)
						{
							$this->event_times_into_events(array(
								"event_time" => &$event_time,
								"event_obj" => &$event_obj,
								"event_time_count" => &$event_time_count,
								"imps" => &$imps,
								"ext_sys_event" => &$ext_sys_event,
								"ext_sys_event_name" => &$ext_sys_event_name,
								"li" => &$li,
								"event_id" => &$event_id,
								"times_done" => &$times_done,
							));
							continue;
						}
						if(array_key_exists($event_time["id"], $imps["time"][$ext_sys_event_time]))
						{
							$time_obj = obj($imps["time"][$ext_sys_event_time][$event_time["id"]]);
							$new = false;

							$all_vals = $time_obj->meta("translations");
						}
						else
						{
							$time_obj = new object;
							$time_obj->set_lang_id($li);
							$time_obj->set_class_id(CL_EVENT_TIME);
							$time_obj->set_parent($dirs["event"]);
							print "<b>";
							$new = true;

							$all_vals = array();
						}
						$tmp["start"] = 0;
						$tmp["end"] = 0;
						foreach($event_time as $key => $value)
						{
							if($key == "name" && $value != "")
							{
								$tmp["name"] = $value;
							}

							if($key == "comment" && $value != "")
							{
								if($import_orig)
									$time_obj->set_comment($value);
								else
									$all_vals[$imp_lang_id]["comment"] = $value;
							}

							if($key == "location_id" && $value != "")
							{
								$tmp["location_id"] = $value;
							}
						}

						if($tmp["location_id"] != "")
						{
							// connect to a location
							if(!array_key_exists($tmp["location_id"], $imps["location"][$ext_sys_location]))
							{	// That should never happen if the place is specified in the source.
								print "<br><h1>MISSING LOCATION</h1><br><br>";
								/*
								$loc_obj = new object();
								$loc_obj->set_parent($arr["id"]);
								$loc_obj->set_class_id(CL_SCM_LOCATION);
								$loc_obj->set_prop("name", $event_time["location_name"]);
								$loc_obj->set_meta("orig_ids", array($xml_source_id => $tmp["location_id"]));
								$loc_obj->save();

								$imps["location"][$xml_source_id][$tmp["location_id"]] = $loc_obj->id();
								*/
							}
							else
							{
								$time_obj->connect(array(
									"to" => $imps["location"][$ext_sys_location][$tmp["location_id"]],
									"type" => "RELTYPE_LOCATION",
								));
							}
						}

						if($event_time["end_year"] < 1970)
							$event_time["end_year"] = date("Y");
						$tmp["end"] = mktime(
							$event_time["end_hour"],
							$event_time["end_min"],
							$event_time["end_sec"],
							$event_time["end_mon"],
							$event_time["end_day"],
							$event_time["end_year"]
						);

						if($event_time["start_year"] < 1970)
							$event_time["start_year"] = date("Y");
						$tmp["start"] = mktime(
							$event_time["start_hour"],
							$event_time["start_min"],
							$event_time["start_sec"],
							$event_time["start_mon"],
							$event_time["start_day"],
							$event_time["start_year"]
						);

						if(in_array($tmp["start"], $times_done))
						{
							print " &nbsp; &nbsp; &nbsp; -";
							print "- event time: ".date("d-m-Y / H:i", $tmp["start"])." already imported <b>[SKIPPED]<br></b>";
							continue;
						}

						$tmp["end"] = ($tmp["end"] > $tmp["start"]) ? $tmp["end"] : $tmp["start"];

						if($import_orig)
							$time_obj->set_prop("name", $tmp["name"]);
						else
							$all_vals[$imp_lang_id]["name"] = $tmp["name"];

						$time_obj->set_prop("start", $tmp["start"]);
						$time_obj->set_prop("end", $tmp["end"]);

						if(!$import_orig)
							$time_obj->set_meta("translations", $all_vals);

						$time_obj->save();

						if($new)
						{
							// We make a record of the event time we just imported so we won't make a duplicate.
							$extent_obj = new object;
							$extent_obj->set_lang_id($li);
							$extent_obj->set_class_id(CL_EXTERNAL_SYSTEM_ENTRY);
							$extent_obj->set_parent($ext_sys_event_time);
							$extent_obj->set_name(sprintf(t("Siduss&uuml;steemi %s sisestus objektile %s"), $ext_sys_event_time_name, $time_obj->name()));
							$extent_obj->set_prop("ext_sys_id", $ext_sys_event_time);
							$extent_obj->set_prop("obj", $time_obj->id());
							$extent_obj->set_prop("value", $event_time["id"]);
							$extent_obj->save();
							$extent_obj->connect(array(
								"type" => "OBJ",
								"to" => $time_obj->id(),
							));
							$extent_obj->connect(array(
								"type" => "EXTSYS",
								"to" => $ext_sys_event_time,
							));
							$imps["event"][$ext_sys_event_time][$event_time["id"]] = $time_obj->id();
						}
						print " &nbsp; &nbsp; &nbsp; -";
						print "- event time: ".date("d-m-Y / H:i", $tmp["start"])." - ".date("d-m-Y / H:i", $tmp["end"])." [saved]<br></b>";
						$event_obj->connect(array(
							"to" => $time_obj->id(),
							"type" => "RELTYPE_EVENT_TIME",
						));
						$times_done[sizeof($times_done)] = $tmp["start"];
					}
				}
				$tmp = array();
			}
		}

		print " &nbsp; - <strong>[ENDED]</strong> " . $xml_source->name() . "<br><br>";
		flush();
	}

	function event_times_into_events($arr)
	{
		extract($arr);

		if($event_time["end_year"] < 1970)
		{
			$event_time["end_year"] = date("Y");
		}
		$tmp["end"] = mktime(
			$event_time["end_hour"],
			$event_time["end_min"],
			$event_time["end_sec"],
			$event_time["end_mon"],
			$event_time["end_day"],
			$event_time["end_year"]
		);
		if($event_time["start_year"] < 1970)
		{
			$event_time["start_year"] = date("Y");
		}
		$tmp["start"] = mktime(
			$event_time["start_hour"],
			$event_time["start_min"],
			$event_time["start_sec"],
			$event_time["start_mon"],
			$event_time["start_day"],
			$event_time["start_year"]
		);
		$tmp["end"] = ($tmp["end"] > $tmp["start"]) ? $tmp["end"] : $tmp["start"];


		if(in_array($tmp["start"], $times_done))
		{
			print " &nbsp; &nbsp; &nbsp; -";
			print "- event time: ".date("d-m-Y / H:i", $tmp["start"])." already imported <b>[SKIPPED]<br></b>";
			return;
		}
		else
		{
			$times_done[] = $tmp["start"];
		}

		if(empty($event_time["id"]))
		{
			$event_time["id"] = $event_id."_".$tmp["start"];
		}

		if(array_key_exists($event_time["id"], $imps["event"][$ext_sys_event]))
		{
			print " &nbsp; &nbsp; &nbsp; -";
			print "- event time: ".date("d-m-Y / H:i", $tmp["start"])." imported as event and copied translations from the original event. Existing event. Ext ID - ".$event_time["id"]." and AW ID - ".$imps["event"][$ext_sys_event][$event_time["id"]]."<br>";
			$o = obj($imps["event"][$ext_sys_event][$event_time["id"]]);
			$o->set_meta("translations", $event_obj->meta("translations"));
		}
		else
		if($event_time_count > 1)
		{
			print " &nbsp; &nbsp; &nbsp; -";
			print "- event time: ".date("d-m-Y / H:i", $tmp["start"])." imported as <b>NEW EVENT</b><br>";
			$o = obj($event_obj->save_new());
			// We make a record of the event we just imported so we won't make a duplicate.
			$extent_obj = new object;
			$extent_obj->set_lang_id($li);
			$extent_obj->set_class_id(CL_EXTERNAL_SYSTEM_ENTRY);
			$extent_obj->set_parent($ext_sys_event);
			$extent_obj->set_name(sprintf(t("Siduss&uuml;steemi %s sisestus objektile %s"), $ext_sys_event_name, $o->name()));
			$extent_obj->set_prop("ext_sys_id", $ext_sys_event);
			$extent_obj->set_prop("obj", $o->id());
			$extent_obj->set_prop("value", $event_time["id"]);
			$extent_obj->save();
			$extent_obj->connect(array(
				"type" => "OBJ",
				"to" => $event_obj->id(),
			));
			$extent_obj->connect(array(
				"type" => "EXTSYS",
				"to" => $ext_sys_event,
			));
			$imps["event"][$ext_sys_event][$event_time["id"]] = $o->id();
		}
		else
		{
			print " &nbsp; &nbsp; &nbsp; -";
			print "- event time: ".date("d-m-Y / H:i", $tmp["start"])." added to the currect event object<br>";
			$o = $event_obj;
		}
		$o->start1 = $tmp["start"];
		$o->end = $tmp["end"];
		$o->save();

		if(array_key_exists($event_time["place_id"], $imps["location"][$ext_sys_location]))
		{
			$o->connect(array(
				"to" => $imps["location"][$ext_sys_location][$event_time["place_id"]],
				"type" => "RELTYPE_LOCATION",
			));
			$o->location = $imps["location"][$ext_sys_location][$event_time["place_id"]];
			$o->save();
			print " &nbsp; &nbsp; &nbsp; -";
			print "- connected to location. ID - ".$imps["location"][$ext_sys_location][$event_time["place_id"]]."<br>";
		}
	}

	/**
		@attrib name=import_events nologin=1
		@param id required type=int acl=view
	**/
	function import_events($arr)
	{
		aw_set_exec_time(AW_LONG_PROCESS);
		ini_set("memory_limit", "800M");
		/*
		// LOADS OF PAIN IN THE A**
		$f = fopen("http://www.kultura.lv/rssfeed.php?lng=ru&s=1195768800&e=1195853400", "r");
		fread($f, 4000);
		$s = fread($f, 4000);
		fclose($f);
		$all_vals[3]["name"] = iconv("UTF-8", "iso-8859-5", $s);
		$o = new object(78457);
		$o->set_meta("translations", $all_vals);
		$o->save();
		exit;
		/*
		$o = new object(73740);
//		arr($o->meta("translations"));
		arr($o->meta());
		exit;
		/**/
		/*
		$o = obj($arr['id']);

		$ol = new object_list(array(
			"class_id" => CL_CALENDAR_EVENT,
//			"limit" => "0,1000",
			"lang_id" => $o->prop("original_lang"),
		));
		$ol->delete(true);
		$ol = new object_list(array(
			"class_id" => CL_CRM_SECTOR,
//			"limit" => "0,100",
			"lang_id" => $o->prop("original_lang"),
		));
		$ol->delete(true);
		$ol = new object_list(array(
			"class_id" => CL_SCM_LOCATION,
//			"limit" => "0,100",
			"lang_id" => $o->prop("original_lang"),
		));
		$ol->delete(true);
		$ol = new object_list(array(
			"class_id" => CL_EVENT_TIME,
//			"limit" => "0,100",
			"lang_id" => $o->prop("original_lang"),
		));
		$ol->delete(true);
		$ol = new object_list(array(
			"class_id" => CL_EXTERNAL_SYSTEM_ENTRY,
//			"limit" => "0,100",
			"lang_id" => $o->prop("original_lang"),
		));
		$ol->delete(true);
		exit;
		/**/
		if (!$this->can("view", $arr['id']))
		{
			die(t("You don't have view access to import object!"));
		}

		$o = obj($arr['id']);
		$li = $o->prop("original_lang");

		$events_manager_id = $o->prop("events_manager");

		if(!is_oid($events_manager_id))
		{
			die(t("Events manager not set!"));
		}

		if (!$this->can("view", $events_manager_id))
		{
			die(t("You don't have view access to events manager object!"));
		}

		$events_manager_obj = obj($events_manager_id);

		$dir_event = $events_manager_obj->prop("event_menu");
		if(!is_oid($dir_event))
		{
			die(t("Events manager: Directory for events not set!"));
		}

		$dir_place = $events_manager_obj->prop("places_menu");
		if(!is_oid($dir_place))
		{
			die(t("Events manager: Directory for places not set!"));
		}

		$dir_organizer = $events_manager_obj->prop("organiser_menu");
		if(!is_oid($dir_organizer))
		{
			die(t("Events manager: Directory for organizers not set!"));
		}

		$dir_category = $events_manager_obj->prop("sector_menu");
		if(!is_oid($dir_category))
		{
			die(t("Events manager: Directory for categories not set!"));
		}

		$dirs = array(
			"event" => &$dir_event,
			"location" => &$dir_place,
			"organizer" => &$dir_organizer,
			"sector" => &$dir_category,
		);

		$event_form_id = $o->prop("event_form");
		if(!is_oid($event_form_id))
		{
			die(t("Event form not set!"));
		}

		if (!$this->can("view", $event_form_id))
		{
			die(t("You don't have view access to eventform object!"));
		}

		$event_form_obj = obj($event_form_id);

		$class_id = $event_form_obj->prop("subclass");

		// <  GATHERING PREVIOUSLY IMPORTED DATA  >
		// Gathering the IDs of events, locations, categories (aka sectors) and event times already imported...

		$imported_events = array();
		$locations = array();
		$categories = array();
		$imported_times = array();

		$imps = array(
			"event" => &$imported_events,
			"location" => &$locations,
			"sector" => &$categories,
			"time" => &$imported_times,
		);

		$impd_objs_arr = array(
			"event_times" => array(
				"parent" => $dir_event,
				"class_id" => CL_EVENT_TIME,
				"array_var" => &$imported_times,
			),
			"events" => array(
				"parent" => $dir_event,
				"class_id" => CL_CALENDAR_EVENT,
				"array_var" => &$imported_events,
			),
			"locations" => array(
				"parent" => $dir_place,
				"class_id" => CL_SCM_LOCATION,
				"array_var" => &$locations,
			),
			"categories" => array(
				"parent" => $dir_category,
				"class_id" => CL_CRM_SECTOR,
				"array_var" => &$categories,
			),
		);

		foreach($impd_objs_arr as $name => $impd_objs)
		{
			print "Gettin object_list for ".$name.".<br>";
			flush();
			$ol = new object_list(array(
				"lang_id" => $li,
				"parent" => $impd_objs["parent"],
				"class_id" => $impd_objs["class_id"],
			));
			//arr($ol->ids());
			print "Getting object_data_list for external system entries.<br>";;
			flush();

			$extents = new object_data_list(
				array(
					"class_id" => CL_EXTERNAL_SYSTEM_ENTRY,
					"obj" => $ol->ids(),
					"lang_id" => $li,
					"site_id" => array(),
					"parent" => array(),
				),
				array(
					CL_EXTERNAL_SYSTEM_ENTRY => array(
						"value" => "value",
						"obj" => "obj",
						"ext_sys_id" => "ext_sys_id",
					),
				)
			);
			//arr($extents->arr());
			//exit;
			print "Making object_data_list into suitable array.<br>";
			flush();

			foreach($extents->arr() as $ext)
			{
				$ext_ids_arr = str_replace(" ", "", $ext["value"]);
				// The external IDs are separated with commas
				$ext_ids = explode(",", $ext_ids_arr);
				foreach($ext_ids as $ext_id)
				{
					// We don't wanna get an error saying "object::load(44650): no view access for object 44650!"
					if($this->can("view", $ext["obj"]) && is_oid($ext["obj"]))
						$impd_objs["array_var"][$ext["ext_sys_id"]][$ext_id] = $ext["obj"];
				}
			}
		}
		//exit;
		// </  GATHERING PREVIOUSLY IMPORTED DATA  >

		$conns_to_xl_sources = $o->connections_from(array(
			"type" => "RELTYPE_XML_SOURCE",
		));

		print "<br><strong>..:: ".strtoupper($o->name())." EVENTS IMPORT STARTED ::..<br><br></strong>";
		flush();

		foreach($conns_to_xl_sources as $conn_to_xl_source)
		{
			$xml_source_id = $conn_to_xl_source->prop("to");
			$xml_source = obj($xml_source_id);

			$imp_langs = $xml_source->prop("available_langs");
			foreach($imp_langs as $imp_lang_id)
			{
				$this->process_import_data(&$arr, &$imps, &$o, &$xml_source_id, &$class_id, &$dirs, $imp_lang_id);
			}
			/*
			// We try to import the Estonian data of events.
			$this->process_import_data(&$arr, &$imps, &$o, &$xml_source_id, &$class_id, &$dirs);
			// We try to import the English translations.
			$this->process_import_data(&$arr, &$imps, &$o, &$xml_source_id, &$class_id, &$dirs, false);
			*/
		}



		print "<strong>..:: ".strtoupper($o->name())." EVENTS IMPORT ENDED ::..<br><br></strong>";
		flush();

		$o->set_meta("last_import", time());
		$o->save();

		print "FLUSHING CACHE<br><br>";
		flush();
		$cache = get_instance("cache");
		$cache->full_flush();

//		$this->activate_next_auto_import($arr);

		return $this->mk_my_orb("change", array("id" => $o->id()), $o->class_id());
	}

	// this function checks if there is a recurrence object configured
	// to otv_ds_kultuuriaken import
	// if it is then put it in scheduler
	//
	// returns the timestamp of next import
	function activate_next_auto_import($arr)
	{
		$o = $arr['object'];
                if (is_oid($o->prop("recurrence")))
                {
                        $auto_import_user = $o->prop("auto_import_user");
                        $auto_import_passwd = $o->prop("auto_import_passwd");
                        if ($auto_import_user != "" && $auto_import_passwd != "")
                        {

                                $recurrence_inst = get_instance(CL_RECURRENCE);
                                $next = $recurrence_inst->get_next_event(array(
                                        "id" => $o->prop("recurrence")
                                ));
                                if ($next)
                                {
                                        // add to scheduler
                                        $sc = get_instance("scheduler");
                                        $sc->add(array(
                                                "event" => $this->mk_my_orb("import_events", array("id" => $o->id())),
                                                "time" => $next,
                                                "uid" => $auto_import_user,
                                                "password" => $auto_import_passwd,
                                        ));
                                }
                        }
                }

		return $next;

	}
}
?>
