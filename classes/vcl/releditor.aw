<?php

/*
	Displays a form for editing one connection
	or alternatively provides an interface to edit
	multiple connections
@classinfo maintainer=kristo
*/

class releditor extends core
{
	var $auto_fields;
	private $loaded_from_cfgform = false;
	private $choose_default = false;

	function releditor()
	{
		$this->init();
	}

	private function init_new_rel_table($arr)
	{
		classload("vcl/table");
		$awt = new vcl_table(array(
			"layout" => "generic",
		));
		$property = $arr["prop"];

		if(!is_object($arr["obj_inst"]))
		{
			if (aw_ini_isset("class_lut.".$arr["request"]["class"]))
			{
				$clid = aw_ini_get("class_lut.".$arr["request"]["class"]);
				$arr["obj_inst"] = obj($arr["request"]["id"], array(), $clid);
			}
			else
			{
				$arr["obj_inst"] = obj($arr["request"]["id"]);
			}
		}

		if (!empty($arr["cb_values"]["edit_data"]))
		{
			$tmp = aw_unserialize($arr["cb_values"]["edit_data"]);
			if (is_array($tmp))
			{
				$this->_init_js_rv_table($awt, $arr["obj_inst"]->class_id(), $property["name"]);
				foreach($tmp  as $idx => $dat_row)
				{
					$this->_insert_js_data_to_table($awt, $property, $dat_row, $arr["obj_inst"]->class_id(), $idx);
				}
				return '<div id="releditor_'.$this->elname.'_table_wrapper">'.$awt->draw()."</div>".html::hidden(array(
					"name" => $property["name"]."_data",
					"value" => $arr["cb_values"]["edit_data"]
				));
			}
		}

		if ($arr["new"])
		{
			return '<div id="releditor_'.$this->elname.'_table_wrapper"></div>';
		}

		$htmlclient = new htmlclient();

		if ($arr["obj_inst"]->class_id())
		{
			$parent_inst = get_instance($arr["obj_inst"]->class_id());
		}

		$parent_property_list = $arr["obj_inst"]->get_property_list();
		$property = $parent_property_list[$property["name"]];
		$tb_fields = empty($property["table_fields"]) ? array() : (array) $property["table_fields"];
		$data = array();

		if(!$arr["new"] && is_object($arr["obj_inst"]) && is_oid($arr["obj_inst"]->id()))
		{
			$conns = $arr["obj_inst"]->connections_from(array(
				"type" => $property["reltype"],
			));
			if (count($conns) == 0)
			{
				return '<div id="releditor_'.$this->elname.'_table_wrapper"></div>';
				return;
			}
			$idx = 1;
			foreach($conns as $conn)
			{
				$c_to = $conn->prop("to");
				$target = $conn->to();

				$clinst = $target->instance();
				$rowdata = array(
					"id" => $c_to,
					"idx" => $idx,
					"parent" => $target->parent(),
					"conn_id" => $conn->id(),
					"name" => $conn->prop("to.name"),
					"_sort_jrk" => $conn->prop("to.jrk"),
					"_sort_name" => $conn->prop("to.name"),
					"delete" => "<a href='javascript:void(0)' name='".$this->elname."_edit_".($idx-1)."'>".t("Muuda")."</a>",
					"_delete" => "<a href='javascript:void(0)' name='".$this->elname."_delete_".($idx-1)."'>".t("Kustuta")."</a>",
				);

				$property_list = $this->all_props;
				$export_props = array();

				foreach($property_list as $_pn => $_pd)
				{
					$data[$idx-1][$_pn] = $target->prop($_pn);

					if (!in_array($_pn,$tb_fields) || (!isset($_pd["show_in_emb_tbl"]) || $_pd["show_in_emb_tbl"] != 1) && isset($arr["prop"]["cfgform_id"]) && is_oid($arr["prop"]["cfgform_id"]))
					{
						continue;
					}

					if(!isset($fields_defined) || !$fields_defined)
					{
						$awt->define_field(array(
							"name" => $_pn,
							"caption" => $property_list[$_pn]["caption"],
						));
					}

					$prop = $_pd;
					$prop["value"] = $target->prop($_pn);
					// now lets call get_property on that beast
					if(method_exists($clinst, "get_property"))
					{
						$test = $clinst->get_property(array(
							"prop" => &$prop,
							"obj_inst" => $target,
							"called_from" => "releditor"
						));
						if (null !== $test && PROP_OK != $test)
						{
							continue;
						};
					}
					if ($_pd["type"] === "chooser" && is_array($prop["options"]))
					{
						$prop["value"] = $prop["options"][$prop["value"]];
					}
					if ($_pd["type"] === "date_select")
					{
						$prop["value"] = date("d.m.Y", $prop["value"]);
					}
					else
					if ($_pd["type"] === "datetime_select")
					{
						$prop["value"] = date("d.m.Y", $prop["value"]);
					}
					if (($_pd["type"] === "relpicker" || $_pd["type"] === "classificator") && $this->can("view", $prop["value"]))
					{
						$_tmp = obj($prop["value"]);
						$prop["value"] = parse_obj_name($_tmp->name());
					}
					else
					if ($_pd["type"] === "select" && is_array($prop["options"]))
					{
						$prop["value"] = $prop["options"][$prop["value"]];
					};
					if(isset($prop["filt_edit_fields"]) && $prop["filt_edit_fields"] == 1)
					{
						if($prop["value"] != "" && $prop["type"] === "textbox")
						{
							$ed_fields[$_pn] = $_pn;
						}
					}
					$get_prop_arr = array();
					foreach($arr as $key => $val)
					{
						$get_prop_arr[$key] = $val;
					}

					$get_prop_arr["called_from"] = "releditor_table";
					$get_prop_arr["prop"] = $prop;
					$get_prop_arr["prop"]["name"] = $this->elname."[".$get_prop_arr["prop"]["name"]."]";
					$parent_inst->get_property($get_prop_arr);
					$prop = $get_prop_arr["prop"];

					if (is_array($prop["value"]))
					{
						$strs = array();
						foreach($prop["value"] as $k => $v)
						{
							$strs[] = $prop["options"][$v];
						}
						$prop["value"] = join(", ", $strs);
					}
					$export_props[$_pn] = $prop["value"];
				}
				$fields_defined = 1;
				$rowdata = $export_props + $rowdata;
				// This one defines the display table data. Just a reminder for myself. - Kaarel

				$awt->define_data($rowdata);
				$idx++;
			}
		}

		$awt->define_field(array(
			"name" => "delete",
			"caption" => t("Muuda"),
			"align" => "center"
		));
		$awt->define_field(array(
			"name" => "_delete",
			"caption" => t("Kustuta"),
			"align" => "center"
		));
		$awt->set_sortable(true);
		$awt->set_numeric_field("_sort_jrk");
		$awt->set_default_sortby(array("_sort_jrk"=>"_sort_jrk", "_sort_name"=>"_sort_name"));
		$awt->sort_by();
		$awt->set_sortable(false);
		return '<div id="releditor_'.$this->elname.'_table_wrapper">'.$awt->draw().html::hidden(array("name" => $this->elname."_data", "value" => htmlspecialchars(serialize($data)))).'</div>';
	}


	private function init_new_manager($arr)
	{
		$visual = $edit_id = "";
		$prop = $arr["prop"];
		$this->elname = $prop["name"];
		$obj = $arr["obj_inst"];
		$obj_inst = $obj;
		$clid = $arr["prop"]["clid"][0];
		if (empty($clid) && is_object($arr["obj_inst"]))
		{
			$relinfo = $arr["obj_inst"]->get_relinfo();
			$clid = $relinfo[$prop["reltype"]]["clid"][0];
		}

		if (!is_oid($arr["obj_inst"]->id()))
		{
			$conns = array();
		}
		else
		{
			$conns = $arr["obj_inst"]->connections_from(array(
				"type" => $arr["prop"]["reltype"],
			));
		}
		$conns_count = sizeof($conns);

		$props = $arr["prop"]["props"];
		if (!is_array($props) && !empty($props))
		{
			$props = array($props);
		}

		$xprops = array();

		if ($clid == 7)
		{
			$use_clid = "doc";
		}
		else
		{
			$use_clid = $clid;
		}

		$t = get_instance($use_clid);
		if ($obj_inst->class_id())
		{
			$parent_inst = get_instance($obj_inst->class_id());
		}

		$t->init_class_base();
		$emb_group = "general";

		$all_props = array();

		// generate a list of all properties. Needed to display edit form
		// and to customize table display in manager mode
		$all_props = $t->load_defaults();
		$act_props = array();
		$use_form = isset($prop["use_form"]) ? $prop["use_form"] : "";

		if (!empty($use_form))
		{
			foreach($all_props as $key => $_prop)
			{
				if (is_array($_prop["form"]) && in_array($use_form,$_prop["form"]))
				{
					$props[$key] = $key;
				}
			}
		}

		$form_type = isset($arr["request"][$this->elname]) ? $arr["request"][$this->elname] : "";
		if (isset($arr["prop"]["always_show_add"]) && $arr["prop"]["always_show_add"] == 1 && !is_oid($edit_id))
		{
			$form_type = "new";
		}
		$this->form_type = $form_type;

		$pcount = sizeof($props);
		foreach($all_props as $key => $_prop)
		{
			if ($all_props[$key] && is_array($props) && in_array($key,$props))
			{
				if (!empty($form_type) || $visual !== "manager")
				{
					if (1 == $pcount and "manager" !== $visual)
					{
						$_prop["caption"] = $prop["caption"];
					}

					//saadab asja get_property'sse
					$act_props[$key] = $_prop;
				}
			}
			$this->all_props[$key] = $_prop;
		}

		if(isset($arr["prop"]["cfgform_id"]) and $this->can("view", $arr["prop"]["cfgform_id"]))
		{
			$cfgform_id = $arr["prop"]["cfgform_id"];
			$cfg = get_instance(CL_CFGFORM);
			$this->cfg_act_props = $cfg->get_cfg_proplist($cfgform_id);
			$act_props = $this->all_props = $all_props = $this->cfg_act_props;
			$this->loaded_from_cfgform = true;
			$cfgform_o = new object($cfgform_id, array(), CL_CFGFORM);
			$layoutinfo = $cfg->get_cfg_layout($cfgform_o);

			if (is_array($layoutinfo))
			{
				$t->layoutinfo = $layoutinfo;
			}
		}
		else
		{
			$cfgform_id = 0;
		}

		if (!empty($prop["choose_default"]))
		{
			$this->choose_default = 1;
		}

		$obj_inst = false;
		if ($form_type !== "new" && is_object($arr["obj_inst"]) &&  is_oid($arr["obj_inst"]->id()))
		{
			if ($edit_id)
			{
				$obj_inst = new object($edit_id, array(), $clid);
			}
			else if (!empty($prop["rel_id"]) && $prop["rel_id"] === "first")
			{
				$o = $arr["obj_inst"];
				if (is_object($o) && is_oid($o->id()))
				{
					$conns = $o->connections_from(array(
						"type" => $prop["reltype"],
					));
					// take the first
					if ($prop["rel_id"] === "first")
					{
						$key = reset($conns);
						if ($key)
						{
							$obj_inst = $key->to();
						};
					}
					else
					if ($conns[$prop["rel_id"]])
					{
						$obj_inst = $conns[$prop["rel_id"]]->to();
					}
				}
			}
		}

		if (is_object($obj_inst) && empty($arr["view"]))
		{
			$act_props["id"] = array(
				"type" => "hidden",
				"name" => "id",
				"value" => $obj_inst->id(),
			);
		}

		if (!$obj_inst)
		{
			$obj_inst = new object(null, array(), $clid);
		}

		// so that the object can access the source object
		if (is_object($arr["obj_inst"]))
		{
			aw_global_set("from_obj", $arr["obj_inst"]->id());
		}

		// maybe I can use the property name itself
		if (isset($arr["cb_values"]) && $arr["cb_values"])
		{
			$t->cb_values = $arr["cb_values"];
		};

		if (empty($arr["prop"]["parent"]))
		{
			$xprops[$prop["name"]."[0]_caption"] = array(
				"type" => "text",
				"value" => empty($prop["caption"]) ? "" : $prop["caption"],
				"subtitle" => 1,
				"store" => "no",
				"name" => $this->elname."_caption",
				"caption" => " ",
	 		);
		}

		$xprops[$prop["name"]."[0]table"] = array(
			"type" => "text",
			"value" => "<a name='".$arr["prop"]["name"]."'></a>".$this->init_new_rel_table($arr),
			"store" => "no",
			"name" => $this->elname."_table",
			"caption" => "",
			"no_caption" => 1,
 		);

		// Adding the cfgform OID allows us to use view controllers. :P
		//													-kaarel
		$t->cfgform_id = $cfgform_id;
		$tmp = $t->parse_properties(array(
			"properties" => $act_props,
			"name_prefix" => $this->elname."[".$conns_count."]",
			"obj_inst" => $obj_inst,
		));
		foreach($tmp as $k => $v)
		{
			$xprops[$k] = $v;
		}

		$xprops[$prop["name"]."_reled_data"] = array(
			"type" => "hidden",
			"value" => $arr["obj_inst"]->class_id()."::".$prop["name"],
			"store" => "no",
			"name" => $prop["name"]."_reled_data",
			"no_caption" => 1,
		);

		$xprops[$prop["name"]."[0]break"] = array(
			"type" => "text",
			"value" => '<br>',
			"store" => "no",
			"name" => $this->elname."_break",
			"caption" => " ",
		);

		if(is_array($arr["prop"]["clid"]))
		{
			$arr["prop"]["clid"] = reset($arr["prop"]["clid"]);
		}
		$xprops[$prop["name"]."[0]add_button"] = array(
			"type" => "text",
			"value" => '
			<input type="submit" value="'.t("Lisa").'" name="'.$prop["name"].'" id="button" onchange="null;set_changed();"/>
			<script>
				jQuery.aw_releditor({
					"releditor_name" : "'.$prop["name"].'",
					"id" : "'.$arr["obj_inst"]->id().'",
					"reltype" : "'.$arr["prop"]["reltype"].'",
					"clid" : "'.$arr["prop"]["clid"].'",
					"use_clid" : "'.$use_clid.'",
					"start_from_index" : "'.$conns_count.'",
					"main_clid" : "'.$obj->class_id().'"
					});
			</script>',
			"store" => "no",
			"name" => $this->elname."_add_button",
			"caption" => " ",//$this->elname."_add_button",
 		);

		$arp = isset($arr["prop"]["parent"]) ? $arr["prop"]["parent"] : "";
		foreach($xprops as $key => $prop)
		{
			$get_prop_arr = $arr;
			$get_prop_arr["prop"] = $prop;
			$get_prop_arr["prop"]["name"] = str_replace("[0]" , "" , $get_prop_arr["prop"]["name"]);
			$get_prop_arr["caller_releditor_name"] = $arr["prop"]["name"];

			if (method_exists($parent_inst, "get_property"))
			{
				$parent_inst->get_property($get_prop_arr);
			}
			$get_prop_arr["prop"]["name"] = $prop["name"];
			$xprops[$key] = $get_prop_arr["prop"];
			if ($arp != "")
			{
				$xprops[$key]["parent"] = $arp;
			}
		}
		return $xprops;
	}

	function init_rel_editor($arr)
	{
		if(ifset($arr, "prop", "mode") === "manager2")
		{
			return $this->init_new_manager($arr);
		}
		// enter_function("init-rel-editor");
		$prop = $arr["prop"];
		$this->elname = $prop["name"];
		$obj = $arr["obj_inst"];
		$obj_inst = $obj;

		$clid = $arr["prop"]["clid"][0];
		if (empty($clid) && is_object($arr["obj_inst"]))
		{
			$relinfo = $arr["obj_inst"]->get_relinfo();
			$clid = $relinfo[$prop["reltype"]]["clid"][0];
		}

		$props = isset($arr["prop"]["props"]) ? $arr["prop"]["props"] : null;
		if (!is_array($props) && !empty($props))
		{
			$props = array($props);
		}

		$xprops = array();
		$errors = false;


		// Automatic fields for manager
		$this->auto_fields = array(
			'class_id' => t("Klassi ID"),
			'class_name' => t("Klass"),
		);


		// manager is a kind of small aliasmgr, it has a table, rows can be clicked
		// 	to edit items, new items can be added, existing ones can be deleted

		// form is a single form, which can be used to edit a single connection. It
		// is also the default
		$visual = isset($prop["mode"]) && $prop["mode"] === "manager" ? "manager" : "form";


		if (!is_array($props) && empty($prop["use_form"]))
		{
			$errors = true;
			$xprops[] = array(
				"type" => "text",
				"caption" => t(" "),
				"error" => sprintf(t("Viga %s definitsioonis (omadused defineerimata!)"), $prop["name"]),
			);
		};

		if (empty($clid))
		{
			$errors = true;
			$xprops[] = array(
				"type" => "text",
				"caption" => t(" "),
				"error" => sprintf(t("Viga %s definitsioonis (seose t&uuml;&uuml;p defineerimata!)"), $prop["name"])
			);
		};

		// now check whether a relation was requested from url
		$edit_id = isset($arr["request"][$this->elname]) ? $arr["request"][$this->elname] : false;

		$found = true;

		$cache_inst = get_instance("cache");

		if ($edit_id !== false && !empty($edit_id) && is_oid($edit_id) && is_oid($arr["obj_inst"]->id()))
		{
			// check whether this connection exists
			$found = false;
			$conns = $arr["obj_inst"]->connections_from(array(
				"type" => $arr["prop"]["reltype"],
			));

			foreach($conns as $conn)
			{
				if ($conn->prop("to") == $edit_id)
				{
					$found = true;
				}
			}
		}

		if (!$found)
		{
			$errors = true;
			$xprops[] = array(
				"type" => "text",
				"caption" => t(" "),
				"value" => t("Seda seost ei saa redigeerida!"),
			);
		};

		if ($errors)
		{
			return $xprops;
		};

		if ($clid == 7)
		{
			$use_clid = "doc";
		}
		else
		{
			$use_clid = $clid;
		}

		$t = get_instance($use_clid);
		$t->init_class_base();
		$emb_group = "general";

		$filter = array(
			"group" => "general",
		);

		if (!empty($prop["use_form"]))
		{
			$filter["form"] = $prop["use_form"];
			$use_form = $prop["use_form"];
		}

		$all_props = array();

		// generate a list of all properties. Needed to display edit form
		// and to customize table display in manager mode
		//$all_props = $t->get_property_group($filter);
		$all_props = $t->load_defaults();
		$this->clid = $use_clid;
		$act_props = array();


		if (!empty($use_form))
		{
			foreach($all_props as $key => $_prop)
			{
				if (isset($_prop["form"]) && is_array($_prop["form"]) && in_array($use_form,$_prop["form"]))
				{
					$props[$key] = $key;
				}
			}
		}

		$form_type = empty($arr["request"][$this->elname]) ? "" : $arr["request"][$this->elname];
		if (isset($arr["prop"]["always_show_add"]) && $arr["prop"]["always_show_add"] == 1 && !is_oid($edit_id))
		{
			$form_type = "new";
		}
		$this->form_type = $form_type;

		#$this->all_props = $act_props;
		$pcount = sizeof($props);

		// act_props needs to contain properties, if
		// 1) visual is form and form_type is empty, if a single relation (rel_id=first) is being edited
		// 2) ....
		foreach($all_props as $key => $_prop)
		{
			if (!empty($arr["cb_values"]["value"][$key]))
			{
				$_prop["value"] = $arr["cb_values"]["value"][$key];
			}

			//if (!empty($use_form) || (is_array($props) && in_array($key,$props)))
			//if ($all_props[$key])
			//if (is_array($props) && in_array($key,$props))
			//if ((!empty($form_type) && $all_props[$key]) || (is_array($props) && in_array($key,$props)))
			//if (!empty($form_type) && $all_props[$key] && is_array($props) && in_array($key,$props))
			if ($all_props[$key] && is_array($props) && in_array($key,$props))
			{
				// if (!empty($form_type) || $visual != "manager")
				if (!empty($form_type) || $visual !== "manager")
				{
					// if (1 == $pcount)// yksiku elemendi caption releditor property captioniga sama
					if (1 == $pcount and "manager" !== $visual)
					{
						$_prop["caption"] = $prop["caption"];
					}
					$act_props[$key] = $_prop;
				}
			}

			if (!empty($arr["cb_values"]["value"][$key]))
			{
				$_prop["value"] = $arr["cb_values"]["value"][$key];
			}
			$this->all_props[$key] = $_prop;
		}

		$this->table_props = $props;

		// "someone" has already used cfgform property, but for what purpose or why, is a big f'ing mystery to me,
		// so i'll just implement something neater

		if(isset($arr["prop"]["cfgform_id"]) and $this->can("view", $arr["prop"]["cfgform_id"]))
		{
			$cfgform_id = $arr["prop"]["cfgform_id"];
			$cfg = get_instance(CL_CFGFORM);
			$this->cfg_act_props = $cfg->get_cfg_proplist($cfgform_id);
			$act_props = $this->all_props = $all_props = $this->cfg_act_props;
			$this->loaded_from_cfgform = true;
			$cfgform_o = new object($cfgform_id);
			$layoutinfo = $cfg->get_cfg_layout($cfgform_o);

			if (is_array($layoutinfo))
			{
				$t->layoutinfo = $layoutinfo;
			}
		}

		// the toolbar should be before the props, because otherwise it
		// would look freakish when adding new or changing -- ahz
		if($visual === "manager" && empty($arr["prop"]["no_toolbar"]))
		{
			// insert the toolbar into property array
			$tbdef = $this->init_rel_toolbar($arr);
			$act_props = array_merge(array($tbdef["name"] => $tbdef), $act_props);
		}

		if (!empty($prop["choose_default"]))
		{
			$this->choose_default = 1;
		}

		if ($visual === "manager")
		{
			// insert the table into property array
			$tabledef = $this->init_rel_table($arr);
			$act_props[$tabledef["name"]] = $tabledef;
		}

		$obj_inst = false;

		if (isset($arr["prop"]["edit_id"]) and $this->can("edit", $arr["prop"]["edit_id"]))
		{
			$edit_id = $arr["prop"]["edit_id"];
		}

		// load the first connection.
		// It should be relatively simple to extend this so that it can load
		// a programmaticaly specified relation

		// need to check whether a existing recurrence thing is specifed, if so, add that
		if ($form_type !== "new" && is_object($arr["obj_inst"]) &&  is_oid($arr["obj_inst"]->id()))
		{
			if ($edit_id)
			{
				$obj_inst = new object($edit_id, array(), $clid);
			}
			elseif (!empty($prop["rel_id"]))
			{
			//else if ($prop["rel_id"] == "first")
			//{
				$o = $arr["obj_inst"];
				if (is_object($o) && is_oid($o->id()))
				{
					$conns = $o->connections_from(array(
						"type" => $prop["reltype"],
					));
					// take the first
					if ($prop["rel_id"] === "first")
					{
						$key = reset($conns);
						if ($key)
						{
							$obj_inst = $key->to();
						};
					}
					else
					if ($conns[$prop["rel_id"]])
					{
						$obj_inst = $conns[$prop["rel_id"]]->to();
					}
				}
			}
		}



		if (is_object($obj_inst) && empty($arr["view"]))
		{
			$act_props["id"] = array(
				"type" => "hidden",
				"name" => "id",
				"value" => $obj_inst->id(),
			);
		}


		if (($visual === "manager" && (is_object($obj_inst) || ($form_type === "new" || (isset($arr["prop"]["always_show_add"]) && $arr["prop"]["always_show_add"] == 1 && !is_oid($edit_id))))))
		//if ($visual == "form" || ($visual == "manager" && (is_object($obj_inst) || $form_type == "new")))
		{
			// I might not want a submit button, eh?
			// exactly my point: i don't want it, so the save button will be on toolbar -- ahz
			/*
			$act_props["sbt"] = array(
				"type" => "submit",
				"name" => "sbt",
				"value" => t("Salvesta"),
			);*/

			if (!empty($arr["prop"]["cfgform"]))
			{
				$act_props["eb_cfgform"] = array(
					"type" => "hidden",
					"name" => "cfgform",
					"value" => $arr["prop"]["cfgform"],
				);
			}
		}

		if (!$obj_inst)
		{
			$obj_inst = new object(null, array(), $clid);
		}

		// so that the object can access the source object
		if (is_object($arr["obj_inst"]))
		{
			aw_global_set("from_obj",$arr["obj_inst"]->id());
		}

		// maybe I can use the property name itself
		if (isset($arr["cb_values"]) && $arr["cb_values"])
		{
			$t->cb_values = $arr["cb_values"];
		};


		// parse_properties fills the thing with values and stuff. And it eats my precious toolbar
		$xprops = $t->parse_properties(array(
			"properties" => $act_props,
			"name_prefix" => $this->elname,
			"obj_inst" => $obj_inst,
		));

		// add this after parse, otherwise the name will be in form propname[elname], and I do not
		// want this
		if ("manager" === $visual)
		{
			$act_name = $prop["name"] . "_action";
			$xprops[$act_name] = array(
				"type" => "hidden",
				"name" => $act_name,
				"id" => $act_name,
				"value" => "",
			);
		};
		foreach($xprops as $pn => $pd)
		{
			preg_match("/.*\[(.*)\]/imsU", $pd["name"], $mt);
			if (!empty($mt[1]) and !empty($arr["cb_values"]["value"][$mt[1]]))
			{
				$xprops[$pn]["value"] = $arr["cb_values"]["value"][$mt[1]];
			}
		}

		if (isset($prop["parent"]) && $prop["parent"] != "")
		{
			$tmp = array();
			foreach($xprops as $pn => $pd)
			{
				$pd["parent"] = $prop["parent"];
				$tmp[$pn] = $pd;
			}
			$xprops = $tmp;
		}

		// exit_function("init-rel-editor");
		return $xprops;
	}

	function init_rel_toolbar($arr)
	{
		if ($arr["request"]["action"] === "view")
		{
			return;
		}
		$createlinks = array();
		$return_url = get_ru();
		// You can set newly created object's parent to be current object
		if (!empty($arr['prop']['override_parent']) && $arr['prop']['override_parent'] === 'this')
		{
			$parent = $arr['obj_inst']->id();
		}
		// Or set any object for the parent
		elseif (!empty($arr['prop']['override_parent']) && is_oid($arr['prop']['override_parent']))
		{
			$parent = $arr['prop']['override_parent'];
		}
		else // Or the default, current objects parent.
		if (is_object($arr["obj_inst"]))
		{
			$parent = $arr['obj_inst']->parent();
		}
		$s_clids = array();
		foreach ($arr['prop']['clid'] as $clid)
		{
			if (!empty($arr["prop"]["direct_links"]))
			{
				$params = array(
					"return_url" => $return_url,
				);
				if (!empty($arr["prop"]["cfgform"]))
				{
					$params["cfgform"] = $arr["prop"]["cfgform"];
				}
				$params["alias_to"] = $arr["obj_inst"]->id();
				$params["reltype"] = $arr["prop"]["reltype"];
				$newurl = html::get_new_url($clid, $parent, $params);
			}
			else
			{
				$newurl = aw_url_change_var(array(
					$this->elname => "new",
				));
			}

			$createlinks[] = array('class' => $clid, 'url' => $newurl);
			$s_clids[] = $clid;
		}

		$tb = get_instance("vcl/toolbar");
		if (count($createlinks) > 1)
		{
			$tb->add_menu_button(array(
				"name" => "new",
				"tooltip" => t("Uus"),
			));
			$clss = aw_ini_get('classes');
			foreach ($createlinks as $i)
			{
				$cn = $clss[$i['class']]['name'];
				$tb->add_menu_item(array(
					'parent' => "new",
					'tooltip' => t("Uus ").$cn,
					'text' => sprintf(t('Lisa %s'),$cn),
					'link' => $i['url'],
				));
			}
		}
		else
		{
			$tb->add_button(array(
				"name" => "new",
				"img" => "new.gif",
				"tooltip" => t("Uus"),
				"url" => $createlinks[0]['url'],
			));
		}

		$confirm_test = t("Kustutada valitud objektid?");
		if (isset($arr['prop']['delete_relations']) && $arr['prop']['delete_relations'])
		{
			$confirm_test = t("Kustutada valitud seosed?");
		}

		$act_input = $this->elname . "_action";

		$tb->add_search_button(array(
			"pn" => "s_reled",
			"multiple" => 1,
			"clid" => $s_clids
		));

		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			// ma pean siia kuidagi mingi triki tegema. Fuck, I hate this :(
			"url" => "javascript:if(confirm('${confirm_test}')){el=document.getElementsByName('${act_input}');el[0].value='delete';document.changeform.submit();};",
			//"action" => "submit_list",
		));

		// because it sucks to have both toolbar and a save button, we'll put the save on toolbar -- ahz
		$tb->add_button(array(
			"name" => "save",
			"img" => "save.gif",
			"tooltip" => t("Salvesta"),
			"action" => "",
		));

		if(isset($arr["prop"]["clone_link"]) and $arr["prop"]["clone_link"] == 1)
		{
			$tb->add_button(array(
				"name" => "clone",
				"img" => "copy.gif",
				"tooltip" => t("Klooni valitud objektid"),
				"url" => "javascript:element = 'check[';len = document.changeform.elements.length;var count = 0;for (i=0; i < len; i++){if (document.changeform.elements[i].checked == true){count++;}}if(count == 1){num=prompt('Mitu objekti kloonida soovid?', '1');document.changeform.releditor_clones.value=num;document.changeform.submit();}else{alert('Sa oled kas liiga vahe voi liiga palju objekte kloonimiseks valinud, proovi uuesti')}",
			));
		}

		$rv = array(
			"name" => $this->elname . "_toolbar",
			"type" => "toolbar",
			"vcl_inst" => $tb,
			"no_caption" => 1,
		);

		return $rv;
	}


	function init_rel_table($arr)
	{
		$awt = new vcl_table(array(
			"layout" => "generic",
		));
		if (!empty($arr["prop"]["table_edit_fields"]))
		{
			$ed_fields = new aw_array($arr["prop"]["table_edit_fields"]);
			$ed_fields = $ed_fields->get();
		};

		if(isset($arr["prop"]["filt_edit_fields"]) and $arr["prop"]["filt_edit_fields"] == 1)
		{
			$ed_fields = array("name" => "name");
		}
		if (!empty($arr["prop"]["props"]))
		{
			$tmp = new aw_array($arr["prop"]["props"]);
			$tb_fields = $tmp->get();
		};

		$fdata = array();
		$conns = array();
		$filt_props = array();
		if (empty($arr["new"]) && $arr["obj_inst"] instanceof object)
		{
			$conns = $arr["obj_inst"]->connections_from(array(
				"type" => $arr["prop"]["reltype"],
			));
			$name = $arr["prop"]["name"];
			$return_url = get_ru();
			foreach($conns as $conn)
			{
				$c_to = $conn->prop("to");
				if (isset($arr["prop"]["direct_links"]) and $arr["prop"]["direct_links"] == 1)
				{
					$url = $this->mk_my_orb("change",array(
						"id" => $c_to,
						"return_url" => $return_url
					),$conn->prop("to.class_id"));
				}
				else
				{
					$url = aw_url_change_var(array($this->elname => $c_to));
				};
				$target = $conn->to();
				$clinst = $target->instance();
				$rowdata = array(
					"id" => $c_to,
					"parent" => $target->parent(),
					"conn_id" => $conn->id(),
					"name" => $conn->prop("to.name"),
					"edit" => html::href(array(
						"caption" => t("Muuda"),
						"url" => $url,
					)),
					"_sort_jrk" => $conn->prop("to.jrk"),
					"_sort_name" => $conn->prop("to.name"),
					"_active" => (isset($arr["request"][$this->elname]) and $arr["request"][$this->elname] == $c_to),
				);
				$export_props = array();
				$clss = null;
				// Some autogenerated fields, for list see line 11 or so
				foreach ($this->auto_fields as $fn => $caption)
				{
					if (in_array($fn, $tb_fields))
					{
						$value = "";
						switch ($fn)
						{
							case 'class_name':
								if (!isset($clss))
								{
									$clss = aw_ini_get('classes');
								}
								$value = $clss[$target->class_id()]['name'];
							break;
							case 'class_id':
								$value = $target->class_id();
							break;
						}
						$export_props[$fn] = $value;
					}
				}

				$property_list = $this->all_props;

				foreach($property_list as $_pn => $_pd)
				{
					if (!in_array($_pn,$tb_fields))
					{
						continue;
					};

					/*
					if (empty($fdata[$_pn]))
					{
						continue;
					};
					*/
					$prop = $_pd;
					$prop["value"] = $target->prop($_pn);
					// now lets call get_property on that beast
					if(method_exists($clinst, "get_property"))
					{
						$test = $clinst->get_property(array(
							"prop" => &$prop,
							"obj_inst" => $target,
							"called_from" => "releditor"
						));
						if (PROP_OK != $test)
						{
							continue;
						};
					}
					// I don't want to display the value of the chooser, but the caption of the value. ;) - Kaarel
					if ($_pd["type"] === "chooser" && is_array($prop["options"]))
					{
						$prop["value"] = $prop["options"][$prop["value"]];
					}

					if ($_pd["type"] === "date_select")
					{
						$prop["value"] = date("d.m.Y", $prop["value"]);
					}
					elseif ($_pd["type"] === "datetime_select")
					{
						$prop["value"] = date("d.m.Y", $prop["value"]);
					}

					if (($_pd["type"] === "relpicker" || $_pd["type"] === "classificator") && $this->can("view", $prop["value"]))
					{
						$_tmp = obj($prop["value"]);
						$prop["value"] = parse_obj_name($_tmp->name());
					}
					elseif ($_pd["type"] === "select")
					{
						$prop["value"] = isset($prop["options"][$prop["value"]]) ? $prop["options"][$prop["value"]] : "";
					}

					if(isset($arr["prop"]["filt_edit_fields"]) and $arr["prop"]["filt_edit_fields"] == 1)
					{
						if($prop["value"] != "" && $prop["type"] === "textbox")
						{
							$ed_fields[$_pn] = $_pn;
						}
					}
					$export_props[$_pn] = $prop["value"];
				}



				$ed_fields["name"] = "name";
				//$export_props = $target->properties();
				if ($ed_fields && ($this->form_type != $target->id()))
				{
					foreach(array_unique($ed_fields) as $ed_field)
					{
						// fucking hackery! :(
						if ($this->all_props[$ed_field]["type"] == "textbox")
						{
							$export_props[$ed_field] = html::textbox(array(
								"name" => "$name" . '[_data][' . $conn->prop("id") . '][' . $ed_field . "]",
								"value" => $export_props[$ed_field],
								"size" => empty($this->all_props[$ed_field]["size"]) ? 15 : $this->all_props[$ed_field]["size"]
							));
						};
					};
				};
				$rowdata = $export_props + $rowdata;
				if ($this->choose_default)
				{
					$rowdata = $rowdata + array(
						"default" => html::radiobutton(array(
							"name" => $arr["prop"]["name"] . '[_default]',
							"value" => $rowdata["id"],
							"checked" => ($arr["prop"]["value"] == $rowdata["id"]),
						)),
					);

				};
				$stuff = $this->get_sub_prop_values(array(
					"prop" => &$prop,
					"obj_inst" => $target,
					"fields" => $this->get_sub_props($tb_fields),
				));
				$rowdata = $rowdata + $stuff;
				// This one defines the display table data. Just a reminder for myself. - Kaarel
				$awt->define_data($rowdata);
			}
		}

		if ($this->choose_default)
		{
			$awt->define_field(array(
				"name" => "default",
				"caption" => t("Vali &uuml;ks"),
				"align" => "center",
				"sortable" => 1
			));
		};

		if(isset($arr["prop"]["filt_edit_fields"]) and $arr["prop"]["filt_edit_fields"] == 1)
		{
			$awt->define_field(array(
				"name" => "id",
				"caption" => t("ID"),
				"sortable" => 1,
				"numeric" => 1,
			));

			foreach($ed_fields as $field)
			{
				$caption = $this->all_props[$field]["caption"];
				if (isset($this->cfg_act_props[$field]["caption"]))
                                {
					$caption = $this->cfg_act_props[$field]["caption"];
				}
				$awt->define_field(array(
					"name" => $field,
					"caption" => $caption,
					"sortable" => 1
				));
			}
		}
		elseif (!empty($arr["prop"]["table_fields"]))
		{
			if (!is_array($arr['prop']['table_fields']))
			{
				$arr['prop']['table_fields'] = array($arr['prop']['table_fields']);
			}
			foreach($arr["prop"]["table_fields"] as $table_field)
			{
				$caption = $table_field;
				if(sizeof(explode("." , $table_field)) > 1)
				{
					$sub_fileds = explode("." , $table_field);
					$reltype = $this->all_props[$sub_fileds[0]]["reltype"];
					$o_ = new object();
					$o_->set_class_id($this->clid);
					$relinfo = $o_->get_relinfo();
					$clid = reset($relinfo[$reltype]["clid"]);
					$o_2 = new object();
					$o_2->set_class_id($clid);
					$prop2 = $o_2->get_property_list();
					$caption = $prop2[$sub_fileds[1]]["caption"];

				}
				if (isset($this->all_props[$table_field]))
				{
					$caption = $this->all_props[$table_field]['caption'];
				}
				else if (isset($this->auto_fields[$table_field]))
				{
					$caption = $this->auto_fields[$table_field];
				}
				else
				if (isset($this->cfg_act_props[$table_field]["caption"]))
				{
					$caption = $this->cfg_act_props[$table_field]["caption"];
				}
				$awt->define_field(array(
					"name" => $table_field,
					"caption" => $caption,
					"sortable" => 1,
				));
				//$fdata[$table_field] = $table_field;
			};
		}
		else
		{
			$awt->define_field(array(
				"name" => "id",
				"caption" => t("ID"),
				"sortable" => 1,
				"numeric" => 1,
			));

			$awt->define_field(array(
				"name" => "name",
				"caption" => t("Nimi"),
				"sortable" => 1
			));
		};

		if ($arr["request"]["action"] !== "view")
		{
			$awt->define_field(array(
				"name" => "edit",
				"caption" => t("Muuda"),
				"align" => "center",
			));

			// aliasmgr uses "check"
			$awt->define_chooser(array(
				"field" => "conn_id",
				"name" => "check",
			));
		}
		// and how do I get values for those?

		// and how do I show the selected row?

		if(isset($arr["prop"]["clone_link"] ) and $arr["prop"]["clone_link"] == 1)
		{
			$awt->set_header('<input type="hidden" name="releditor_clones" id="releditor_clones" value="0" />');
		}
		$awt->set_numeric_field("_sort_jrk");
		$awt->set_sortable(true);
		$awt->set_numeric_field("_sort_jrk");
		$awt->set_default_sortby(array("_sort_jrk"=>"_sort_jrk", "_sort_name"=>"_sort_name"));
		$awt->sort_by();
		$awt->set_sortable(false);

		if (isset($arr["prop"]["caption"]) and strlen($arr["prop"]["caption"]))
		{
			$awt->set_caption($arr["prop"]["caption"]);
		}

		$rv = array(
			"name" => $this->elname . "_table",
			"type" => "table",
			"vcl_inst" => $awt,
			"no_caption" => 1,
		);

		return $rv;
	}

	function get_sub_prop_values($arr)
	{
		$ret = array();
		foreach($arr["fields"] as $field => $data)
		{
			$ret[$field] = $arr["obj_inst"]->prop_str($field);
		}
		return $ret;
	}

	function get_sub_props($table_fields)
	{
		$ret = array();
		foreach($table_fields as $table_field)
		{
			if(sizeof(explode("." , $table_field)) > 1)
			{
				$sub_fileds = explode("." , $table_field);
				$reltype = $this->all_props[$sub_fileds[0]]["reltype"];
				$o_ = new object();
				$o_->set_class_id($this->clid);
				$relinfo = $o_->get_relinfo();
				$clid = reset($relinfo[$reltype]["clid"]);
				$o_2 = new object();
				$o_2->set_class_id($clid);
				$prop2 = $o_2->get_property_list();
				$ret[$table_field] = $prop2[$sub_fileds[1]];;
			}
		}
		return $ret;
	}

	/**
		@attrib name=delo all_args=1 public=1
	**/
	function delo($arr)
	{
		extract($arr);
		$o = obj($id);
		$o->delete();
	}

	function callback_post_save($arr)
	{
		// read the data from the serialized array
		if (isset($arr["request"][$arr["prop"]["name"]."_data"]))
		{
			$dat = unserialize($arr["request"][$arr["prop"]["name"]."_data"]);
			if (!is_array($dat))
			{
				return;
			}

			// for each row in the data, fake a submit to the correct class
			$relinfo = $arr["obj_inst"]->get_relinfo();

			$to_clid = $relinfo[$arr["prop"]["reltype"]]["clid"][0];
			$class_name = basename(aw_ini_get("classes.{$to_clid}.file"));

			$rels = $arr["obj_inst"]->get_property_list();

			$idx2rel = array();
			if (is_oid($arr["obj_inst"]->id()))
			{
				$idx = 0;
				foreach($arr["obj_inst"]->connections_from(array("type" => $arr["prop"]["reltype"])) as $c)
				{
					$idx2rel[$idx++] = $c->prop("to");
				}
			}

			if($arr["request"]["cfgform"])
			{
				$cfgform = new cfgform();
				$cfgproplist = $cfgform->get_cfg_proplist($arr["request"]["cfgform"]);
			}

			foreach($dat as $idx => $row)
			{
				$row["class"] = $class_name;
				$row["action"] = "submit";
				$row["parent"] = $arr["obj_inst"]->id();
				$row["alias_to"] = $arr["obj_inst"]->id();
				$row["alias_to_prop"] = $arr["prop"]["name"];
				$row["reltype"] = $arr["prop"]["reltype"];
				$row["id"] = $idx2rel[$idx];
				$row["cfgform"] = $cfgproplist[$arr["prop"]["name"]]["cfgform_id"];
				$i = get_instance($to_clid);
				$i->submit($row);
			}
		}
	}

	function process_releditor($arr)
	{
		if("no" === $arr["prop"]["store"] or isset($arr["prop"]["mode"]) and $arr["prop"]["mode"] === "manager2")
		{
			return;
		}
		$prop = &$arr["prop"];
		$obj = $arr["obj_inst"];
		$set_default_relation = false;

		$clid = $arr["prop"]["clid"][0];

		if (!empty($arr["prop"]["reltype"]) and isset($arr["request"]["s_reled"]))
		{
			$ps = new popup_search();
			$ps->do_create_rels($obj, $arr["request"]["s_reled"], $arr["prop"]["reltype"]);
		}

		if ($clid == 7)
		{
			$use_clid = "doc";
		}
		else
		{
			$use_clid = $clid;
		}

		if (!isset($prop['delete_relations']))
		{
			$prop['delete_relations'] = '0';
		}

		$act_prop = $prop["name"] . "_action";

		if (isset($arr["request"][$act_prop]) and "delete" === $arr["request"][$act_prop])
		{
			// XXX: this will fail, if there are multiple releditors on one page
			$to_delete = new aw_array($arr["request"]["check"]);
			$delete_default = false;

			foreach($to_delete->get() as $alias_id)
			{
				$c = new connection($alias_id);

				if ("manager" === $prop["mode"] and $c->prop("to") == $obj->prop($prop["name"]))
				{
					$delete_default = true;
				}

				$c->delete();
			}

			if ($delete_default)
			{
				# old default deleted, set first found to be default
				$conns = $obj->connections_from(array(
					"type" => $arr["prop"]["reltype"],
				));
				$first_conn = reset($conns);

				if (is_object($first_conn))
				{
					$obj->set_prop($arr["prop"]["name"], $first_conn->prop("to"));
				}
			}

			return PROP_OK;
		}

		$clinst = get_instance($use_clid);

		$elname = $prop["name"];
		$emb = isset($arr["request"][$elname]) ? $arr["request"][$elname] : array();
		// _data is used to edit multiple connections at once
		unset($emb["_data"]);

		if (isset($emb["_default"]) and is_oid($emb["_default"]))
		{
			$prop["value"] = $emb["_default"];
			$set_default_relation = $emb["_default"];
		}

		unset($emb["_default"]);

		$clinst->init_class_base();
		$emb_group = "general";

		$filter = array(
			"group" => "general",
		);

		if (!empty($prop["use_form"]))
		{
			$filter["form"] = $prop["use_form"];
			$use_form = $prop["use_form"];
		};

		$props = $clinst->load_defaults();

		$propname = $prop["name"];
		$proplist = is_array($prop["props"]) ? $prop["props"] : array($prop["props"]);

		$el_count = 0;

		foreach($props as $item)
		{
			// if that property is in the list of the class properties, then
			// process it
			if (!empty($use_form) || in_array($item["name"],$proplist))
			{
				if ($item["type"] === "fileupload")
				{
					if (!isset($emb[$item["name"]]) or !is_array($emb[$item["name"]]))
					{
						// ot, aga miks need 2 caset siin on?
						$name = $item["name"];
						$_fileinf = $_FILES[$elname];
						$filename = $_fileinf["name"][$name];
						$filetype = $_fileinf["type"][$name];
						$tmpname = $_fileinf["tmp_name"][$name];
						// tundub, et polnud sellist faili, eh?
						if(empty($tmpname) || !is_uploaded_file($tmpname))
						{
						}
						else
						{
							$emb[$name] = array(
								"tmp_name" => $tmpname,
								"type" => $filetype,
								"name" => $filename,
							);
							$el_count++;
						};
					}
					/*
					// ok, wtf is that code supposed to do?
					// - that code is supposed to upload the picture when it's added through another
					// - class = meaning, DO NOT TOUCH THIS OK? -- ahz
					*/
					else
					{
						$tmpname = $emb[$item["name"]]["tmp_name"];
						if (is_uploaded_file($tmpname))
						{
							$emb[$item["name"]]["contents"] = $this->get_file(array(
								"file" => $tmpname,
							));

							$el_count++;
						};
					};

				}
				else
				{
					// this shit takes care of those non-empty select boxes
					if ($emb[$item["name"]] && $item["type"] != "datetime_select" && $item["name"] != "status")
					{
						$el_count++;
					}

					if ($item["type"] === "checkbox" && !$emb[$item["name"]])
					{
						$emb[$item["name"]] = 0;
					}
				}
			}
		}


		// TODO: make it give feedback to the user, if an object can not be added
		if ($el_count > 0)
		{
			$emb["group"] = "general";
			$obj_parent = $obj->parent();
			if (is_oid($prop["obj_parent"]))
			{
				$obj_parent = $prop["obj_parent"];
			};
			if ($prop["override_parent"] === "this")
			{
				$obj_parent = $obj->id();
			}
			$emb["parent"] = $obj_parent;
			$emb["return"] = "id";
			$emb["prefix"] = $elname;

			$reltype = $arr["prop"]["reltype"];

			$emb["cb_existing_props_only"] = 1;


			$obj_id = $clinst->submit($emb);
			// fucking hackery :(
			$cb_values = aw_global_get("cb_values");
			if (is_array($cb_values) && sizeof($cb_values) > 0)
			{
				$errtxt = "";
				foreach($cb_values as $pkey => $pval)
				{
					if (in_array($pkey, $prop["props"]))
					{
						$errtxt .= $pval["error"];
					}
				}

				if (strlen($errtxt) > 0)
				{
					$prop["error"] = $errtxt;
					return PROP_ERROR;
				}
			}


			if ($prop["rel_id"] === "first" && empty($emb["id"]))
			{
				// I need to disconnect, no?
				if (is_oid($obj->id()))
				{
					$old = $obj->connections_from(array(
						"type" => $arr["prop"]["reltype"],
					));

					foreach($old as $conn)
					{
						$obj->disconnect(array(
							"from" => $conn->prop("to"),
						));
					};
				};
			}

			if (is_oid($obj_id))
			{
				if (empty($emb["id"]))
				{
					$obj->connect(array(
						"to" => $obj_id,
						"reltype" => $arr["prop"]["reltype"],
					));

					if (!$obj->prop($arr["prop"]["name"]))
					{
						$set_default_relation = $obj_id;
					}
				};
			};
		}

		if ($set_default_relation)
		{
			$obj->set_prop($arr["prop"]["name"], $set_default_relation);
		}

		// is this save() here really needed?  --dragut
		// it seems that, in some cases it saves an object which has releditor
		// although it shouldn't be saved cause some PROP_FATAL_ERROR appearance.
		// --dragut
	//	$obj->save();

		$things = isset($arr["request"][$elname]["_data"]) ? $arr["request"][$elname]["_data"] : array();
		if (sizeof($things) > 0 && is_oid($obj->id()))
		{
			$conns = $obj->connections_from(array(
				"type" => $arr["prop"]["reltype"],
			));

			foreach($conns as $conn)
			{
				$conn_id = $conn->prop("id");
				if ($things[$conn_id])
				{
					$to_obj = $conn->to();
					foreach($things[$conn_id] as $propname => $propvalue)
					{
						$to_obj->set_prop($propname,$propvalue);
					};
					$to_obj->save();
				}
			}
		}

		$num = isset($arr["request"]["releditor_clones"]) ? (int) $arr["request"]["releditor_clones"] : 0;
		if(!empty($arr["prop"]["clone_link"]) && $num > 0)
		{
			foreach(safe_array($arr["request"]["check"]) as $check)
			{
				$conn = new connection($check);
				$old_obj = $conn->to();
				for($i = 1; $i <= $num; $i++)
				{
					$new_obj = obj($old_obj->save_new());
					$obj->connect(array(
						"to" => $new_obj->id(),
						"reltype" => $arr["prop"]["reltype"],
					));
				}
			}
		}
	}

	function get_html()
	{
		return "here be releditor";
		//return $this->t->draw();
	}

	function callback_mod_reforb($arr)
	{
		$arr["s_reled"] = "0";
	}

	/**
		@attrib name=handle_js_submit all_args=1 nologin=1
	**/
	function handle_js_submit($arr)
	{
		$propn = null;
		foreach($arr as $k => $d)
		{
			if (substr($k, -strlen("_reled_data")) === "_reled_data")
			{
				list($clid, $propn) = explode("::", $d);
				break;
			}
		}

		if ($propn === null)
		{
			die("error, no property data! given: ".dbg::dump($arr));
		}

		$num = reset(array_keys($arr[$propn]));

//		$this->loaded_from_cfgform = is_oid($arr["cfgform"]) && $this->can("view", $arr["cfgform"]);
		$t = new aw_table;
		$this->_init_js_rv_table($t, $clid, $propn, $arr["cfgform"]);

		$cfgproplist = is_oid($arr["cfgform"]) ? get_instance(CL_CFGFORM)->get_cfg_proplist($arr["cfgform"]) : array();
		$cfgcontroller_inst = get_instance(CL_CFGCONTROLLER);
		$prev_dat = safe_array(unserialize(iconv("utf-8", aw_global_get("charset")."//IGNORE", $arr[$propn."_data"])));
		foreach($arr[$propn][$num] as $k => $v)
		{
			if (!is_array($v))
			{
				$arr[$propn][$num][$k] = iconv("utf-8", aw_global_get("charset")."//IGNORE", $v);
			}
		}

		if(is_oid($arr["cfgform"]) && $this->can("view", $arr["cfgform"]))
		{
			$cfgform_i = new cfgform();
			$cfgproplist = $cfgform_i->get_cfg_proplist($arr["cfgform"]);
			$cfgcontroller_inst = get_instance(CL_CFGCONTROLLER);
			$cfgform_o = obj($cfgproplist[$propn]["cfgform_id"]);
			if(is_oid($cfgproplist[$propn]["cfgform_id"]) && $this->can("view", $cfgproplist[$propn]["cfgform_id"]))
			{
				$cfgproplist_ = $cfgform_i->get_cfg_proplist($cfgproplist[$propn]["cfgform_id"]);
			}
			else
			{
				$cfgproplist_ = $cfgform_i->get_default_proplist(array("clid" => $arr["use_clid"]));
			}

			$cfg_cntrl = (array) $cfgform_o->meta("controllers");

			$retval = PROP_OK;
			$err = array();
			foreach($cfg_cntrl as $cntrl_prop => $cntrl_ids)
			{
				if (count($cntrl_ids))
				{
					$controller_inst = $cfgcontroller_inst;
					foreach ($cntrl_ids as $cfg_cntrl_id)
					{
						if (is_oid($cfg_cntrl_id))
						{
							$tmp = array("value" => &$arr[$propn][$num][$cntrl_prop]);
							$retval_ = $controller_inst->check_property($cfg_cntrl_id, $arr["id"], $tmp, $arr[$propn][$num], NULL, obj($arr["id"]));
							$retval = $retval_ != PROP_OK ? $retval_ : $retval;
							if($retval_ != PROP_OK)
							{
								$err[$cntrl_prop][] = htmlentities(str_replace("%caption", $cfgproplist_[$cntrl_prop]["caption"], obj($cfg_cntrl_id)->errmsg));
							}
						}
					}
				}
			}

			if($retval != PROP_OK)
			{
				// Return the errors instead of HTML.
				$js_arr = "var error = {";
				$count = 0;
				foreach($err as $err_prop => $msgs)
				{
					$js_arr .= $count > 0 ? "," : "";
					$msg_str = "";
					foreach($msgs as $msg)
					{
						$msg_str .= strlen($msg_str) > 0 ? "<br />" : "";
						$msg_str .= $msg;
					}
					$js_arr .= $propn."_".$arr["start_from_index"]."__".$err_prop."_:\"".$msg."\"";
					$count++;
				}
				$js_arr .= "};";
				header("Content-type: text/html; charset=".aw_global_get("charset"));
				die($js_arr);
			}
		}

		$prev_dat[$num] = $arr[$propn][$num];
		$cur_prop = $this->_get_js_cur_prop($clid, $propn);

		// if the current object exists then we need to save the change to the connected class immediately
		if (is_oid($arr["id"]))
		{
			$o = obj($arr["id"]);
			$idx2oid = array();
			$idx = 0;
			foreach($o->connections_from(array("type" => $cur_prop["reltype"])) as $c)
			{
				$idx2oid[$idx++] = $c->prop("to");
			}

			$clss = aw_ini_get("classes");

			$row = safe_array($prev_dat[$num]);
			$row["class"] = basename($clss[$this->_get_related_clid($clid, $propn)]["file"]);
			$row["action"] = "submit";
			$row["parent"] = $arr["id"];
			$row["id"] = $idx2oid[$num];
			$row["alias_to"] = $arr["id"];
			$row["alias_to_prop"] = $propn;
			$row["reltype"] = $cur_prop["reltype"];
			$row["cfgform"] = $cfgproplist[$propn]["cfgform_id"];
			$i = get_instance($this->_get_related_clid($clid, $propn));
			$rv = $i->submit($row);
			// So the set_property() and prop() functions could change the value -kaarel 12.03.2009
			foreach(array_keys($prev_dat[$num]) as $k)
			{
				$prev_dat[$num][$k] = $i->obj_inst->prop($k);
			}
		}

		foreach($prev_dat as $idx => $dat_row)
		{
			$this->_insert_js_data_to_table($t, $cur_prop, $dat_row, $clid, $idx, $arr["cfgform"], $err);
		}

		header("Content-type: text/html; charset=".aw_global_get("charset"));
		die($t->draw().html::hidden(array(
			"name" => $propn."_data",
			"value" => htmlspecialchars(serialize($prev_dat))
		)));
	}

	/** returns property data, given class id and property name
	**/
	private function _get_js_cur_prop($clid, $propn)
	{
		$cur_props = $this->_get_props_from_clid($clid);
		return $cur_props[$propn];
	}

	private function _init_js_rv_table($t, $clid, $propn, $cfgform_id = null)
	{
		if ($this->all_props)
		{
			$rel_props = $this->all_props;
		}
		else
		{
			$rel_clid = $this->_get_related_clid($clid, $propn);
			$rel_props = $this->_get_props_from_clid($rel_clid);
		}
		$cur_prop = $this->_get_js_cur_prop($clid, $propn);
		if ($this->can("view", $cfgform_id))
		{
			$cfo = obj($cfgform_id);
			$cf = get_instance(CL_CFGFORM);
			$props = $cf->get_cfg_proplist($cfgform_id);
			$cur_prop = $props[$propn];

			if ($this->can("view", $cur_prop["cfgform_id"]))
			{
				$this->loaded_from_cfgform = true;
				$rel_props = $cf->get_cfg_proplist($cur_prop["cfgform_id"]);
				foreach($rel_props as $pn => $pd)
				{
					if (!$pd["show_in_emb_tbl"])
					{
						unset($rel_props[$pn]);
					}
				}
			}
		}

		if ($this->loaded_from_cfgform)
		{
			$defs = array();
			foreach ($rel_props as $name => $data)
			{
				if ($data["show_in_emb_tbl"] && (!$data["emb_tbl_col_num"] || !isset($defs[$data["emb_tbl_col_num"]])))
				{
					$defs[$data["emb_tbl_col_num"]] = 1;
					$this->_define_table_col_from_prop($t, $data);
				}
			}
		}
		else
		{
			$defs = array();
			$this->sort_relp = $rel_props;

//-------------see sortimine siin, ma ei n2e kuda vajalik oleks, seda enam , et ["ord"] muutujaid ei tule m6istlikke kuskil
//kui tundub ikka vajalik, siis claendat_event'is event_time_edit propertyga peaks ka proovima... seal sordib lihtsalt valeks
//			uasort($cur_prop["table_fields"], array(&$this, "__props_sort"));
			foreach(safe_array($cur_prop["table_fields"]) as $prop_name)
			{
				$data = $rel_props[$prop_name];
				if (isset($rel_props[$prop_name]) && (!$data["emb_tbl_col_num"] || !isset($defs[$data["emb_tbl_col_num"]])))
				{
					$defs[$data["emb_tbl_col_num"]] = 1;
					$this->_define_table_col_from_prop($t, $rel_props[$prop_name]);
				}
			}
		}
		$t->define_field(array(
			"name" => $propn."_change",
			"caption" => t("Muuda"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => $propn."_delete",
			"caption" => t("Kustuta"),
			"align" => "center"
		));
	}

	function __props_sort($a, $b)
	{
		$a = $this->sort_relp[$a];
		$b = $this->sort_relp[$b];
		return $a["ord"] - $b["ord"];
	}

	private function _insert_js_data_to_table($t, $cur_prop, $prop_data, $clid, $idx, $cfgform_id)
	{
		$rel_clid = $this->_get_related_clid($clid, $cur_prop["name"]);
		$rel_props = $this->_get_props_from_clid($rel_clid);
		if ($this->can("view", $cfgform_id))
		{
			$cfo = obj($cfgform_id);
			$cf = get_instance(CL_CFGFORM);
			$props = $cf->get_cfg_proplist($cfgform_id);
			$cur_prop = $props[$cur_prop["name"]];

			if ($this->can("view", $cur_prop["cfgform_id"]))
			{
				$rel_props = $cf->get_cfg_proplist($cur_prop["cfgform_id"]);
				$cur_prop["table_fields"] = array();
				foreach($rel_props as $pn => $pd)
				{
					if (!$pd["show_in_emb_tbl"])
					{
						unset($rel_props[$pn]);
					}
					else
					{
						$cur_prop["table_fields"][] = $pn;
					}
				}
			}
		}

		$d = array(
			"oid" => $idx+1,
			$cur_prop["name"]."_change" => "<a href='javascript:void(0)' name='".$cur_prop["name"]."_edit_".$idx."'>".t("Muuda")."</a>",
			$cur_prop["name"]."_delete" => "<a href='javascript:void(0)' name='".$cur_prop["name"]."_delete_".($idx)."'>".t("Kustuta")."</a>"
		);

		// call get_property for each field in the table as well
		$o = obj();
		$o->set_class_id($rel_clid);
		$i = $o->instance();
		$defs = array();

		foreach(safe_array($cur_prop["table_fields"]) as $prop_name)
		{
			$tc_name = $prop_name;

			$data = $rel_props[$prop_name];
			if (!empty($data["emb_tbl_col_num"]) && isset($defs[$data["emb_tbl_col_num"]]))
			{
				$tc_name = $defs[$data["emb_tbl_col_num"]];
			}
			else
			{
				$defs[$data["emb_tbl_col_num"]] = $tc_name;
			}

			if (($rel_props[$prop_name]["type"] === "date_select" || $rel_props[$prop_name]["type"] === "datetime_select") && is_array($prop_data[$prop_name]))
			{
				$tc_val = date_edit::get_timestamp($prop_data[$prop_name], $rel_props[$prop_name]);
			}
			else
			{
				$tc_val = $prop_data[$prop_name];
			}
			$pv = $rel_props[$prop_name];
			$pv["value"] = $tc_val;

			if ($pv["type"] === "relpicker")
			{
				$ri = get_instance("vcl/relpicker");
				$ri->init_vcl_property(array(
					"obj_inst" => $o,
					"request" => $_POST,
					"property" => &$pv,
					"relinfo" => $o->get_relinfo()
				));
			}
			$args = array(
				"obj_inst" => $o,
				"request" => $_POST,
				"prop" => &$pv
			);
			if (method_exists($i, "_get_".$prop_name))
			{
				$mn = "_get_".$prop_name;
				$i->$mn($args);
			}
			else
			if (method_exists($i, "get_property"))
			{
				$i->get_property($args);
			}
			switch($pv["type"])
			{
				case "relpicker":
				case "classificator":
					if (isset($pv["options"][$pv["value"]]))
					{
						$tc_val = $pv["options"][$pv["value"]];
					}
					else
					if ($this->can("view", $pv["value"]))
					{
						$tmp = obj($pv["value"]);
						$tc_val = parse_obj_name($tmp->name());
					}
					else
					if (is_oid($pv["value"]))
					{
						$tc_val = "";
					}
					else
					if (is_array($pv["value"]))
					{
						$strs = array();
						foreach($pv["value"] as $item)
						{
							if ($this->can("view", $item))
							{
								$tmp = obj($item);
								$strs[] = parse_obj_name($tmp->name());
							}
						}
						$tc_val = join(", ", $strs);
					}
					else
					{
						$tc_val = $pv["value"];
					}
					break;

				case "chooser":
				case "select":
					if (is_array($pv["value"]))
					{
						$tmp = array();
						foreach($pv["value"] as $k => $v)
						{
							$tmp[] = $pv["options"][$v];
						}
						$tc_val = join(", ", $tmp);
					}
					else
					if (is_array($pv["options"]) && isset($pv["options"][$pv["value"]]))
					{
						$tc_val = $pv["options"][$pv["value"]];
					}
					else
					{
						$tc_val = $pv["value"];
					}

					break;

				case "checkbox":
					$tc_val = $pv["value"] == $pv["ch_value"] ? t("Jah") : t("Ei");
					break;

				default:
					$tc_val = $pv["value"];
			}
			if (trim($d[$tc_name]) != "")
			{
				$d[$tc_name] .= ($tc_val != "" ? $data["emb_tbl_col_sep"] : "").$tc_val;
			}
			else
			{
				$d[$tc_name] = $tc_val;
			}
		}
		$t->define_data($d);
	}

	private function _define_table_col_from_prop($t, $pd)
	{
		if (!is_array($pd))
		{
			return;
		}
		$d = array(
			"name" => $pd["name"],
			"caption" => $pd["emb_tbl_caption"] ? $pd["emb_tbl_caption"] : $pd["caption"],
		);
		if ($pd["type"] === "date_select")
		{
			$d["type"] = "time";
			if (is_array($pd["format"]))
			{
				$dmy = array();
				if (in_array("day", $pd["format"]))
				{
					$dmy[] = "d";
				}
				if (in_array("month", $pd["format"]))
				{
					$dmy[] = "m";
				}
				if (in_array("year", $pd["format"]))
				{
					$dmy[] = "Y";
				}
				if (count($dmy) == 0)
				{
					$dmy = "d.m.Y";
				}
				$d["format"] = join(".", $dmy);
			}
			else
			{
				$d["format"] = "d.m.Y";
			}
		}
		else
		if ($pd["type"] === "datetime_select")
		{
			$d["type"] = "time";
			$d["format"] = "d.m.Y H:i:s";
		}
		$t->define_field($d);
	}

	/** returns the first class_id from the relation type for the $from_prop property in class $from_clid
	**/
	private function _get_related_clid($from_clid, $from_prop)
	{
 		$o = obj();
		$o->set_class_id($from_clid);
		$pl = $o->get_property_list();
		$pd = $pl[$from_prop];

		$relinfo = $o->get_relinfo();
		return $relinfo[$pd["reltype"]]["clid"][0];
	}

	/** returns list of properties for class $from_clid
	**/
	private function _get_props_from_clid($from_clid)
	{
 		$o = obj();
		$o->set_class_id($from_clid);
		return $o->get_property_list();
	}

	/**
		@attrib name=js_change_data all_args=1 nologin=1
	**/
	function js_change_data($arr)
	{
		$releditor_name = $arr["releditor_name"];
		$d = unserialize(iconv("utf-8", aw_global_get("charset")."//IGNORE", $arr[$releditor_name."_data"]));
		$idx = $arr["edit_index"];
		$main_clid = $arr["main_clid"];//CL_CALENDAR_EVENT;//CL_CRM_PERSON;

		$pd = $this->_get_js_cur_prop($main_clid, $releditor_name);
		$rel_clid = $this->_get_related_clid($main_clid, $releditor_name);
		$rel_props = $this->_get_props_from_clid($rel_clid);

		$r = array();
		foreach($pd["props"] as $rel_prop_name)
		{
			if ($rel_props[$rel_prop_name]["type"] === "datetime_select")
			{
				if (is_array($d[$idx][$rel_prop_name]))
				{
					$d[$idx][$rel_prop_name] = mktime($d[$idx][$rel_prop_name]["hour"], $d[$idx][$rel_prop_name]["minute"], $d[$idx][$rel_prop_name]["second"], $d[$idx][$rel_prop_name]["month"], $d[$idx][$rel_prop_name]["day"], $d[$idx][$rel_prop_name]["year"]);
				}
				$r[] = "'[$rel_prop_name]': {'day': '".date("d", $d[$idx][$rel_prop_name])."', 'month': '".date("m", $d[$idx][$rel_prop_name])."', 'year': '".date("Y", $d[$idx][$rel_prop_name])."', 'hour':'".date("H", $d[$idx][$rel_prop_name])."', 'minute': '".date("i", $d[$idx][$rel_prop_name])."' }";
			}
			else
			if (is_array($d[$idx][$rel_prop_name]))
			{
				$d2 = array();
				foreach($d[$idx][$rel_prop_name] as $k => $v)
				{
					$d2[] = " '$k' : '$v' ";
				}
				$r[] = "'[$rel_prop_name][]': { ".join(", ", $d2)." } ";
			}
			else
			if($rel_props[$rel_prop_name]["option_is_tuple"] || $rel_props[$rel_prop_name]["type"] === "relpicker")
			{
				$value = is_oid($d[$idx][$rel_prop_name]) ? obj($d[$idx][$rel_prop_name])->name() : $d[$idx][$rel_prop_name];
				$r[] = "'[".$rel_prop_name."]_awAutoCompleteTextbox': '".$value."'";
				$r[] = "'[$rel_prop_name]': '".$d[$idx][$rel_prop_name]."'";
			}
			else
			{
				$r[] = "'[$rel_prop_name]': '".$d[$idx][$rel_prop_name]."'";
			}
		}

		$s_out = "edit_data = {";
		$s_out .= join(",\n", $r);
		$s_out .= " }; ";

		header("Content-type: text/html; charset=".aw_global_get("charset"));
		echo $s_out;
		die();
	}

	/**
		@attrib name=js_get_button_name nologin=1
		@param is_edit optional
	**/
	function js_get_button_name($arr)
	{
		header("Content-type: text/html; charset=".aw_global_get("charset"));
		if ($arr["is_edit"] == 1)
		{
			die(trim(t("Muuda")));
		}
		else
		{
			die(t("Lisa"));
		}
	}

	/**
		@attrib name=js_get_delete_confirmation_text nologin=1
	**/
	function js_get_delete_confirmation_text($arr)
	{
		header("Content-type: text/html; charset=".aw_global_get("charset"));
		die(t("Oled kindel, et soovid kustutada?"));
	}

	/**
		@attrib name=js_delete_rows all_args=1 nologin="1"
	**/
	function js_delete_rows($arr)
	{
		$propn = null;
		foreach($arr as $k => $d)
		{
			if (substr($k, -strlen("_reled_data")) === "_reled_data")
			{
				list($clid, $propn) = explode("::", $d);
				break;
			}
		}
		if ($propn === null)
		{
			die("error, no property data! given: ".dbg::dump($arr));
		}

//		$this->loaded_from_cfgform = is_oid($arr["cfgform"]) && $this->can("view", $arr["cfgform"]);
		$t = new aw_table;
		$this->_init_js_rv_table($t, $clid, $propn, $arr["cfgform"]);

		$prev_dat = safe_array(unserialize(iconv("utf-8", aw_global_get("charset")."//IGNORE", $arr[$propn."_data"])));
		$cur_prop = $this->_get_js_cur_prop($clid, $propn);

		if (is_oid($arr["id"]))
		{
			$o = obj($arr["id"]);
			$idx2oid = array();
			$idx = 0;
			foreach($o->connections_from(array("type" => $cur_prop["reltype"])) as $c)
			{
				$idx2oid[$idx++] = $c->prop("to");
			}
		}
		if (is_oid($arr["id"]) && is_oid($idx2oid[$arr[$propn."_delete_index"]]))
		{
			$o->disconnect(array(
				"from" => $idx2oid[$arr[$propn."_delete_index"]],
				"type" => $cur_prop["reltype"]
			));
		}
		unset($prev_dat[$arr[$propn."_delete_index"]]);

		$tmp = array();
		foreach($prev_dat as $row)
		{
			$tmp[] = $row;
		}
		$prev_dat = $tmp;

		if (count($prev_dat) == 0)
		{
			die(html::hidden(array(
				"name" => $propn."_data",
				"value" => htmlspecialchars(serialize($prev_dat))
			)));
		}

		foreach($prev_dat  as $idx => $dat_row)
		{
			$this->_insert_js_data_to_table($t, $cur_prop, $dat_row, $clid, $idx, $arr["cfgform"]);
		}


		header("Content-type: text/html; charset=".aw_global_get("charset"));
		die($t->draw().html::hidden(array(
			"name" => $propn."_data",
			"value" => htmlspecialchars(serialize($prev_dat))
		)));
	}
}

?>
