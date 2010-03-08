<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/survey/survey_manager.aw,v 1.11 2008/03/12 21:23:17 kristo Exp $
// survey_manager.aw - Ankeetide haldur 
/*

@classinfo syslog_type=ST_SURVEY_MANAGER relationmgr=yes maintainer=kristo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property survey_folder type=relpicker reltype=RELTYPE_SURVEY_FOLDER
@caption Millisesse kataloogi täidetud ankeedid salvestada?

@property use_cfgform type=relpicker reltype=RELTYPE_SURVEY_CFGFORM
@caption Millist seadete vormi kasutada?

@property redirect_to type=relpicker reltype=RELTYPE_REDIRECT_TO
@caption Kuhu ümber suunata

@property users_only type=checkbox ch_value=1 
@caption Täita saavad ainult registreeritud kasutajad

@property survey_name type=chooser multiple=1 orient=vertical
@caption Ankeedi nimi

@default group=survey_list
@property survey_toolbar type=toolbar no_caption=1
@caption Nimekirja toolbar

@property survey_list type=table store=no no_caption=1
@caption Täidetud ankeedid

@default group=survey_search 

@property search_form type=callback callback=callback_search_form store=no 
@caption Otsi

@property search_results_toolbar type=toolbar no_caption=1
@caption Otsingutulemuste toolbar

@property search_results type=table store=no no_caption=1
@caption Tulemus

@groupinfo survey_list caption="Nimekiri" submit=no
@groupinfo survey_search caption="Otsing" 

@reltype SURVEY_FOLDER value=1 clid=CL_MENU
@caption Kataloog

@reltype SURVEY_CFGFORM value=2 clid=CL_CFGFORM
@caption Seadete vorm

@reltype REDIRECT_TO value=3 clid=CL_DOCUMENT
@caption Kuhu peale täitmist suunata

*/

class survey_manager extends class_base
{
	function survey_manager()
	{
		$this->init(array(
			"clid" => CL_SURVEY_MANAGER
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "survey_list":
				$this->make_survey_list($arr);
				break;
			
			case "search_results":
				$retval = $this->make_search_results($arr);
				break;

			case "survey_toolbar":
				$this->make_survey_toolbar($arr);
				break;
			
			case "search_results_toolbar":
				$retval = $this->search_results_toolbar($arr);
				break;

			case "survey_name":
				$o = $arr["obj_inst"];
				$cform_id = $o->prop("use_cfgform");
				if (is_oid($cform_id))
				{
					$t = get_instance(CL_CFGFORM);
					$props = $t->get_props_from_cfgform(array("id" => $cform_id));
					$opts = array();
					foreach($props as $key => $val)
					{
						if ($val["type"] == "textbox")
						{
							$opts[$key] = $val["caption"];
						};
					};
					$prop["options"] = $opts;
				}
				else
				{
					$retval = PROP_IGNORE;
				};
				break;

		};
		return $retval;
	}

	function make_survey_toolbar($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$o = $arr["obj_inst"];
		$t->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta valitud ankeedid"),
			"action" => "delete_surveys",
			"confirm" => t("Kustutada valitud ankeedid?"),
		));

	}
	
	function search_results_toolbar($arr)
	{
		if (sizeof($this->search_data) == 0)
		{
			return PROP_IGNORE;
		};
		$t = &$arr["prop"]["vcl_inst"];
		$t->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta valitud ankeedid"),
			"action" => "delete_surveys",
			"confirm" => t("Kustutada valitud ankeedid?"),
		));
		$t->add_separator();
		$t->add_button(array(
			"name" => "export_csv",
			"img" => "ftype_xls.gif",
			"tooltip" => t("Ekspordi ankeedid"),
			"url" => aw_url_change_var(array(
				"format" => "csv",
				)),
		));
	}

	function callback_pre_edit($arr)
	{
		if (!empty($arr["request"]["search_form"]))
		{
			$this->search_data = $arr["request"]["search_form"];
		}
		else
		{
			$this->search_data = array();
		};
	}

	function callback_search_form($arr)
	{
		$o = $arr["obj_inst"];
		$cform_id = $o->prop("use_cfgform");
		$use_props = $all_props = array();
		if (is_oid($cform_id) && $this->can("view", $cform_id))
		{
			$cf = get_instance(CL_CFGFORM);
			$use_props = $cf->get_props_from_cfgform(array("id" => $cform_id));
			$cfgu = get_instance("cfg/cfgutils");
			$all_props = $cfgu->load_properties(array(
				"clid" => CL_SURVEY,
			));
		};
		$rv = array();
		$pname = $arr["prop"]["name"];
		foreach($use_props as $key => $val)
		{
			$type = $all_props[$key]["type"];
			$caption = $use_props[$key]["caption"];
			if ($type == "textbox")
			{
				$rv[$pname . $key] = array(
					"type" => "textbox",
					"name" => "${pname}[${key}]",
					"caption" => $caption,
					"value" => $this->search_data[$key],
				);	
			};
		};
		$rv[$pname . "sbt"] = array(
			"type" => "submit",
			"name" => $pname . "sbt",
			"caption" => t("Otsi"),
		);	
		return $rv;

	}

	function make_survey_list($arr)
	{
		$use_props = $this->configure_survey_table($arr);

		$t = &$arr["prop"]["vcl_inst"];

		$ol_args = array(
			"parent" => $arr["obj_inst"]->prop("survey_folder"),
			"class_id" => CL_SURVEY,
		);

		$ol = new object_list($ol_args);


		$return_url = get_ru();
		foreach($ol->arr() as $survey)
		{
			$id = $survey->id();
			$rowdata = array(
				"name" => $survey->name(),
				"created" => $survey->created(),
				"id" => $id,
				"modified" => $survey->modified(),
				"edit" => html::href(array(
					"url" => $this->mk_my_orb("change",array("id" => $id,"return_url" => $return_url),CL_SURVEY),
					"caption" => t("Vaata"),
				)),
			);

			$rowdata = $rowdata + $survey->properties();
			$t->define_data($rowdata);
		};

	}
	
	function make_search_results($arr)
	{
		if (sizeof($this->search_data) == 0)
		{
			return PROP_IGNORE;
		};

		$use_props = $this->configure_survey_table($arr);

		$t = &$arr["prop"]["vcl_inst"];

		$ol_args = array(
			"parent" => $arr["obj_inst"]->prop("survey_folder"),
			"class_id" => CL_SURVEY,
		);

		foreach($this->search_data as $key => $val)
		{
			// do not someone overwrite something important like a parent for example
			if ($use_props[$key])
			{
				$ol_args[$key] = "%" . $val . "%";
			};
		};

		$ol = new object_list($ol_args);

		$rv = "";
		$csv = $arr["request"]["format"] == "csv";

		$return_url = get_ru();

		foreach($ol->arr() as $survey)
		{
			$id = $survey->id();
			if ($csv)
			{
				$first = true;
				foreach($use_props as $key => $val)
				{
					if (!$first)
						$rv .= ",";
					$first = false;
					$rv .= $survey->prop($key);
				};
				$rv .= "\n";
			}
			else
			{
				$rowdata = array(
					"name" => $survey->name(),
					"created" => $survey->created(),
					"id" => $id,
					"modified" => $survey->modified(),
					"edit" => html::href(array(
						"url" => $this->mk_my_orb("change",array("id" => $id,"return_url" => $return_url),CL_SURVEY),
						"caption" => t("Vaata"),
					)),
				);

				$rowdata = $rowdata + $survey->properties();
				$t->define_data($rowdata);
			};
		};

		if ($csv)
		{
			header("Content-type: application/csv");
			$name = preg_replace("/\s/","_",$this->cf_obj->name()) . date("-d-m-Y");
			header("Content-Disposition: filename=" . $name . ".csv");
			die($rv);
		};


	}

	function callback_mod_retval($arr)
	{
		$args = &$arr["args"];
		if (!empty($arr["request"]["search_form"]))
		{
			$args["search_form"] = $arr["request"]["search_form"];
		};
	}


	/*
	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{

		}
		return $retval;
	}	
	*/

	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	function show($arr)
	{
		$o = new object($arr["id"]);
		$t = get_instance(CL_SURVEY);

		if (1 == $o->prop("users_only"))
		{
			if ("" == aw_global_get("uid"))
			{
				return "";
			};
			// try to figure out whether this user has a filled survey
			$users = get_instance("users");
			$user = new object(aw_global_get("uid_oid"));

			$conns = $user->connections_to(array(
				"from.class_id" => CL_SURVEY,
			));

			if (sizeof($conns) == 1)
			{
				$first = reset($conns);
				$survey_id = $first->prop("from");
				return $t->new_change(array(
					"action" => "change",
					"id" => $survey_id,
					"extraids" => array("redirect_to" => $o->prop("redirect_to")),
				));
			};
		}

		$cform_id = $o->prop("use_cfgform");
		$use_props = $all_props = array();
		$cb_values = aw_global_get("cb_values");
		if (is_oid($cform_id) && $this->can("view", $cform_id))
		{
			$cform = new object($cform_id);
			$use_props = $cform->meta("cfg_proplist");
			$cfgu = get_instance("cfg/cfgutils");
			$all_props = $cfgu->load_properties(array(
				"clid" => CL_SURVEY,
			));
			unset($use_props["needs_translation"]);
			unset($use_props["is_translated"]);

			$htmlc = get_instance("cfg/htmlclient",array("template" => "webform.tpl"));
			$htmlc->start_output();
			foreach($use_props as $key => $val)
			{
				$htmlc->add_property(array(
					"name" => $key,
					"caption" => $val["caption"],
					"value" => $cb_values[$key]["value"],
					"type" => $all_props[$key]["type"],
					"error" => $cb_values[$key]["error"],
				));
			}

			aw_session_del("cb_values");

			$htmlc->finish_output(array("data" => array(
				"class" => get_class($this),
				"action" => "process_survey",
				"survey_id" => $o->id(),
				"section" => aw_global_get("section"),
				),
			));

			$html = $htmlc->get_result(array(
				"form_only" => 1
			));	

		}
		else
		{
			$html = "böö";
		};
		return $html;
	}

	/** the same table is used by different properties
	**/
	function configure_survey_table(&$arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$o = $arr["obj_inst"];
		$cform_id = $o->prop("use_cfgform");
		$use_props = $all_props = array();
		if (is_oid($cform_id) && $this->can("view", $cform_id))
		{
			$cf = get_instance(CL_CFGFORM);
			$use_props = $cf->get_props_from_cfgform(array("id" => $cform_id));
			$this->cf_obj = new object($cform_id);
			$cfgu = get_instance("cfg/cfgutils");
			$all_props = $cfgu->load_properties(array(
				"clid" => CL_SURVEY,
			));
		};
		$t->define_chooser(array(
			"name" => "sel",
			"caption" => t("id"),
			"field" => "id",
		));

		// show name only if no cfgform is being used
		//if (sizeof($use_props) == 0)
		//{
			$t->define_field(array(
				"name" => "name",
				"caption" => t("Nimi"),
				"sortable" => 1,
			));
		//};

		$xprops = array();

		foreach($use_props as $key => $val)
		{
			$type = $all_props[$key]["type"];
			$caption = $use_props[$key]["caption"];
			if ($type == "textbox")
			{
				$t->define_field(array(
					"name" => $key,
					"caption" => $caption,
					"sortable" => 1,
				));
				$xprops[$key] = $key;
			};
		};
		
		$t->define_field(array(
			"name" => "remote_host",
			"caption" => t("Host"),
		));
		
		$t->define_field(array(
			"name" => "created",
			"caption" => t("Loodud"),
			"type" => "time",
			"format" => "H:i d-M-y",
			"sortable" => 1,
		));


		$t->define_field(array(
			"name" => "edit",
			"caption" => t("Vaata"),
			"align" => "center",
		));

		$t->set_default_sortby("created");
		$t->set_default_sorder("desc");

		return $xprops;
	}

	/**
		@attrib name=process_survey nologin=1 all_args=1
	**/
	function process_survey($arr)
	{
		$o = new object($arr["survey_id"]);
		$cform_id = $o->prop("use_cfgform");
		$survey_data = array();
		/*if (is_oid($cform_id) && $this->can("view", $cform_id))
		{
		}
		else
		{
			return false;
		};
		*/
		$cform = new object($cform_id);
		$use_props = $cform->meta("cfg_proplist");
		foreach($use_props as $key => $val)
		{
			if (isset($arr[$key]))
			{
				$survey_data[$key] = $arr[$key];	
			};
		};

		$survey_data["parent"] = $o->prop("survey_folder");
		$survey_data["cfgform"] = $cform_id;

		$name_fields = $o->prop("survey_name");
		if (!is_array($name_fields))
		{
			$name_fields = array();
		};
		$name_parts = array();
		foreach($name_fields as $name_field)
		{
			$name_parts[] = trim($survey_data[$name_field]);
		};
		$survey_name = join("-",$name_parts);


		$survey_data["return"] = "id";

		$t = get_instance(CL_SURVEY);
		// right then, now I need to access error information
		$survey_id = $t->submit($survey_data);

		if (is_array($t->cb_values) && sizeof($t->cb_values) > 0)
		{
			aw_session_set("no_cache", 1);
			return $this->cfg["baseurl"] . "/" . $arr["section"];
		}
		else
		{
			$survey_obj = new object($survey_id);
			$survey_obj->set_name($survey_name);
			$survey_obj->set_prop("remote_host",gethostbyaddr(get_ip()));
			$survey_obj->save();
			return $this->cfg["baseurl"] . "/" . $o->prop("redirect_to");

		};



	}

	/**
		@attrib name=delete_surveys 
	**/	
	function delete_surveys($arr)
	{
		$o = new object($arr["id"]);
		$ol = new object_list(array(
			"parent" => $o->prop("survey_folder"),
			"class_id" => CL_SURVEY,
		));
		foreach($ol->arr() as $o)
		{
			if ($arr["sel"][$o->id()])
			{
				$o->delete();
			};
		};
		return $this->finish_action($arr);
	}


}
?>
