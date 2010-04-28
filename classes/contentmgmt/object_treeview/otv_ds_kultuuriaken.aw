<?php

namespace automatweb;
// otv_ds_kultuuriaken.aw - Import Kultuuriaknast
/*
@classinfo syslog_type=ST_OTV_DS_KULTUURIAKEN relationmgr=yes no_comment=1 no_status=1 maintainer=dragut

@default table=objects
@default group=general

	@property event_form type=relpicker reltype=RELTYPE_EVENT_FORM field=meta method=serialize
	@caption S&uuml;ndmuse vorm
	@comment Kultuuriakna s&uuml;ndmuse lisamise/muutmise vorm

	@property xml_file_url type=textbox size=50 field=meta method=serialize
	@caption XML faili url
	@comment Veebiaadress, kust XML v&auml;ljundi saab

	@property last_import_text type=text store=no
	@caption Viimane import
	@comment Viimase impordi toimumise aeg

	@property next_import_text type=text store=no
	@caption J&auml;rgmine automaatne import
	@comment J&auml;rgmise automaatse impordi toimumise aeg

	@property import_events_all type=checkbox ch_value=1 field=meta method=serialize
	@caption Impordi k&otilde;iik s&uuml;ndmused

	@property import_events type=text store=no
	@caption Impordi s&uuml;ndmused
	@comment Link s&uuml;ndmuste importimiseks

@groupinfo config caption="Seaded"
@default group=config

	@property config_table type=table store=no caption=no
	@caption Seaded

@groupinfo xml_view caption="Vaade XML"
@default group=xml_view

	@property xml_config type=table store=no
	@caption XML vaate seaded

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


@reltype EVENT_FORM value=1 clid=CL_CFGFORM
@caption S&uuml;ndmuse vorm

@reltype PARENT value=2 clid=CL_PROJECT
@caption Parent

@reltype RECURRENCE value=3 clid=CL_RECURRENCE
@caption Kordus
*/

class otv_ds_kultuuriaken extends class_base
{
	const AW_CLID = 890;



	var $xml_fields = array(
		"id",
		"date",
		"date_end",
		"begin",
		"type",
		"importance",
		"name",
		"description",
		"details",
		"place",
		"contact",
		"url",
		"email",
		"telefon",
	);

	function otv_ds_kultuuriaken()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/object_treeview/otv_ds_kultuuriaken",
			"clid" => CL_OTV_DS_KULTUURIAKEN
		));
	}

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
			case "config_table":
				$this->create_config_table($arr);
				break;

			case "xml_config":
				$this->create_xml_config_table($arr);
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
			// save data from configuration table
			case "config_table":
				if (!empty($arr['request']['config_conf']))
				{
					$arr['obj_inst']->set_meta("config_conf", $arr['request']['config_conf']);
				}
				break;

			// save data from xml configuration table
			case "xml_config":
				if (!empty($arr['request']['xml_conf']))
				{
					$arr['obj_inst']->set_meta("xml_conf", $arr['request']['xml_conf']);
				}
				break;

		}
		return $retval;
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

	function create_config_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);
		$t->define_field(array(
			"name" => "parent",
			"caption" => t("Parent"),
		));

		$t->define_field(array(
			"name" => "xml_field_content",
			"caption" => t("XML v&auml;lja sisu")
		));

		// let's get all parents
		$conns_to_parents = $arr['obj_inst']->connections_from(array(
				"type" => "RELTYPE_PARENT",
			));

		$saved_config_conf = $arr['obj_inst']->meta("config_conf");
		foreach($conns_to_parents as $conn_to_parent)
		{
			$t->define_data(array(
				"parent" => $conn_to_parent->prop("to.name"),
				"xml_field_content" => html::textbox(array(
					"name" => "config_conf[".$conn_to_parent->id()."][xml_field_content]",
					"value" => $saved_config_conf[$conn_to_parent->id()]['xml_field_content'],
				)),
			));
		}
	}

	function create_xml_config_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);
		$t->define_field(array(
			"name" => "xml_field",
			"caption" => t("XML v&auml;li"),
		));
		$t->define_field(array(
			"name" => "form_field",
			"caption" => t("Vormi v&auml;li"),
		));


		// getting properties from cfgform
		$event_form_oid = $arr['obj_inst']->prop("event_form");
		if (!is_oid($event_form_oid))
		{
			return;
		}
		$event_form_obj = obj($event_form_oid);
		$event_form_inst = $event_form_obj->instance();
		$props = $event_form_inst->get_props_from_cfgform(array(
				"id" => $event_form_obj->id(),
			));
		$options = array();
		foreach ($props as $value)
		{
			if (empty($value['caption']))
			{
				$value['caption'] = $value['name'];
			}
			$options[$value['name']] = $value['caption'];
		}

		// getting and modifying the xml data to get all <event> element children
		// i think i have to make an array with all those elements which are present under
		// event element, cause it surely is silly to load all the xml data file and only
 		// to get those element names
		// -- so it seems i created the class-wide xml fields array -dragut

		// a really good thing would be, if there is somekind of xml schema or dtd defined,
		// so i have to load only the schema/dtd file to acquire the data structure

	/*
		//// so commenting out that part where i get the field names from the data file
		$xml_content = $this->load_xml_content(array(
			"id" => $arr['obj_inst']->id(),
			"owner" => 0,
			"start" =>0,
		));

		$index_arr = $xml_content[1];
		unset($index_arr['events'], $index_arr['event']);
		$index_arr = array_keys($index_arr);
	*/

		// get saved xml configuration data
		$saved_xml_conf = $arr['obj_inst']->meta("xml_conf");

		// and lets put the data into table:
		foreach($this->xml_fields as $value)
		{

			switch ($value)
			{
				case "begin":
					$form_field = "date + hhmm";
					break;
				case "importance":
					$form_field = "jrk";
					break;
				default:
					$form_field = html::select(array(
						"name" => "xml_conf[".$value."]",
						"options" => $options,
						"selected" => $saved_xml_conf[$value],
					));
			}

			$t->define_data(array(
				"xml_field" => $value,
				"form_field" => $form_field,
			));
		}
	}
	///
	// params
	// - Import Kultuuriaknast oid (required)
	// - owner id (optional - if not set, events from all owners will be returned)
	// - start (optional) last modified timestamp / last import timestamp
	function load_xml_content($arr)
	{
		if (!is_oid($arr['id']))
		{
			return false;
		}
		$o = new object($arr['id']);
		$xml_file_url = $o->prop("xml_file_url");
		if (empty($xml_file_url))
		{
			return false;
		}

		// if there is a better way to compose the url params, then
		// it should be implmented here:
		$url_params = (!empty($arr['owner'])) ? "?owner=".$arr['owner']."&" : "?";
		$url_params = (!empty($arr['start'])) ? $url_params."start=".$arr['start'] : $url_params;
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
	// waiting the day when i can use file_get_contents()
	//	$xml_file_content = file_get_contents($xml_file_url.$url_params);
		return parse_xml_def(array(
			"xml" => $xml_file_content,
		));
	}

	/**
		@attrib name=import_events nologin=1
		@param id required type=int acl=view
	**/
	function import_events($arr)
	{
	//	aw_set_exec_time(AW_LONG_PROCESS);

		if (!$this->can("view", $arr['id']))
		{
			error::raise(array(
				"msg" => t("You don't have view access to import object!"),
			));
		}

		$o = obj($arr['id']);

		$event_form_id = $o->prop("event_form");
		if (!$this->can("view", $event_form_id))
		{
			error::raise(array(
				"msg" => t("You don't have view access to eventform object!"),
			));
		}

		$event_form_obj = obj($event_form_id);

		$class_id = $event_form_obj->prop("subclass");

		$saved_config_conf = $o->meta("config_conf");
		$saved_xml_conf = $o->meta("xml_conf");

		$conns_to_parents = $o->connections_from(array(
			"type" => "RELTYPE_PARENT",
		));

		// checking if i have to import all objects or from the last import
		$last_import = $o->meta("last_import");
		if ($o->prop("import_events_all"))
		{
			$last_import = "";
		}

		foreach ($conns_to_parents as $conn_to_parent)
		{
			$conn_id = $conn_to_parent->id();
			$parent_id = $conn_to_parent->prop("to");

			$load_xml_content_params = array(
				"id" => $arr['id'],
				"owner" => $saved_config_conf[$conn_id]['xml_field_content'],
				"start" => $last_import,
			);

			if (empty($last_import))
			{
				unset($load_xml_params['start']);
			}

			$xml_content = $this->load_xml_content($load_xml_content_params);

			// so, it is possible, that we don't have anything from load_xml_content method
			// in this case it returns false
			if ($xml_content === false)
			{
				echo "<strong>Could not get XML data!</strong><br><br>";
				return $this->mk_my_orb("change", array("id" => $o->id()), $o->class_id());
			}

			$ol = new object_list(array(
				"parent" => $parent_id,
				"class_id" => $class_id,
			));
			echo "parent: ".$conn_to_parent->prop("to")." <strong>".$conn_to_parent->prop('to.name')."</strong> [".$ol->count()."]<br>";
			flush();
			// creating an array of existing objects:
			$imported_events = array();
			if ($ol->count() != 0)
			{
				foreach ($ol->arr() as $obj)
				{
					$imported_events[$obj->prop($saved_xml_conf['id'])] = $obj->id();
				}
			}
			// loop through all the xml data
			foreach ($xml_content[0] as $value)
			{
				if ($value['tag'] == "event" && $value['type'] == "open")
				{
					$tmp_start_date = 0;
					$event_data = array();
				}
				if ($value['tag'] != "events" && $value['tag'] != "event" && $value['type'] == "complete")
				{
					switch($value['tag'])
					{
						case "lmod":
							break;
						case "begin":
							if (!empty($value['value']))
							{
								$event_data[$saved_xml_conf['date']] += (($value['value']{0}.$value['value']{1}) * 3600 + ($value['value']{2}.$value['value']{3}) * 60) - (12 * 3600);
							}
							break;
						case "date":
							$event_data[$saved_xml_conf[$value['tag']]] += $value['value'];
							break;
						case "date_end":
							$event_data[$saved_xml_conf[$value['tag']]] = $value['value'] + (11 * 3600);
							break;
						case "importance":
							$event_data['jrk'] = $value['value'];
							break;
						default:
							$event_data[$saved_xml_conf[$value['tag']]] = $value['value'];
					}
				}
				if ($value['tag'] == "event" && $value['type'] == "close")
				{
					if (empty($imported_events) || !array_key_exists($event_data[$saved_xml_conf['id']], $imported_events))
					{
						$event_obj = new object;
						$event_obj->set_parent($parent_id);
						$event_obj->set_class_id($class_id);
						echo "<strong>[ new ] </strong>";
						flush();
					}
					else
					{
						$event_obj = new object($imported_events[$event_data[$saved_xml_conf['id']]]);
						echo "[ --- ] ";
						flush();
					}

					// setting the objects properties
					// seems that there are no ord/jrk property
					$event_obj->set_ord($event_data['jrk']);
					$event_obj->set_comment($event_data['comment']);
					unset($event_data['jrk']);
					unset($event_data['comment']);
					foreach ($event_data as $k => $v)
					{
						if (!empty($k))
						{
							$event_obj->set_prop($k, $v);
						}
					}
					$event_obj->save();
					echo " ".$event_data['name']." [ saved ]<br>";
					flush();
				}
			}
		}

		echo t(" <br>..:: IMPORT L6PPENUD ::..<br>");
		flush();
		$o->set_meta("last_import", time());

		// ok, here should go this part where next import will be put in scheduler
		// the time of next automatic import comes from recurrence object
		$this->activate_next_auto_import(array(
			"object" => $o,
		));

		$o->save();
		return $this->mk_my_orb("change", array("id" => $o->id()), $o->class_id());
	}

	//// params:
	// object => otv_ds_kultuuriaken object
	//
	// this fn. checks if there is a recurrence object configured
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
