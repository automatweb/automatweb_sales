<?php
/*
@classinfo syslog_type=ST_CLASSIFICATOR relationmgr=yes maintainer=kristo

@default table=objects
@default group=general

@property comment type=textarea cols=50 rows=5 field=comment
@caption Kommentaar

@default field=meta
@default method=serialize

@property folders type=relpicker reltype=RELTYPE_FOLDER multiple=1
@caption Kus kehtib

@property clids type=select multiple=1
@caption Klassid millele kehtib

@reltype FOLDER value=1 clid=CL_MENU
@caption hallatav kataloog

*/

class classificator extends class_base
{
	function classificator()
	{
		$this->init(array(
			'clid' => CL_CLASSIFICATOR
		));
	}

	function get_property(&$arr)
	{
		$prop =& $arr["prop"];
		if ($prop['name'] == "clids")
		{
			$prop['options'] = get_class_picker();
		}

		return PROP_OK;
	}

	function init_vcl_property($arr)
	{
		$prop = &$arr["property"];

		if ($arr["view"])
		{
			$this->view = 1;
		}

		if(isset($prop["recursive"]) && $prop["recursive"] == 1)
		{
			$this->recursive = 1;
		}

		$ch_args = array(
			"clid" => $arr["clid"],
			"name" => $prop["name"],
			"obj_inst" => $arr["obj_inst"],
		);

		if(isset($arr["sort_by"]) && $arr["sort_by"])
		{
			$ch_args["sort_by"] = $arr["sort_by"];
		}
		if(isset($prop["sort_by"]) && $prop["sort_by"])
		{
			$ch_args["sort_by"] = $prop["sort_by"];
		}
		if(isset($arr["object_type_id"]) && $arr["object_type_id"])
		{
			$ch_args["object_type_id"] = $arr["object_type_id"];
		}
		if (isset($prop["object_type_id"]) && is_oid($prop["object_type_id"]))
		{
			$ch_args["object_type_id"] = $prop["object_type_id"];
		}
		if(isset($prop["sort_callback"]))
		{
			$ch_args["sort_callback"] = $prop["sort_callback"];
		}

		list($achoices,$name,$use_type,$default_value,$choices) = $this->get_choices($ch_args);

		$selected = false;
		$connections = array();

		if ($prop["store"] == "connect")
		{
			if (is_object($arr["obj_inst"]) && is_oid($arr["obj_inst"]->id()))
			{
				$conns = $arr["obj_inst"]->connections_from(array(
					"type" => $prop["reltype"],
				));

				foreach($conns as $conn)
				{
					$selected = $conn->prop("to");
					$connections[$selected] = $selected;
				}
			}
			else
			{
				// try to figure out values from some place else
				$connections = ifset($prop, "value");
			}

			if (empty($prop["value"]))
			{
				if ($use_type == "checkboxes" || ($use_type == "select" && $prop["multiple"] == 1) || $use_type == "mselect")
				{
					$prop["value"] = $connections;
				}
				else
				{
					$prop["value"] = $selected;
				}
			}
		}

		if (!empty ($default_value) and empty ($prop["value"]))
		{
			$prop["default"] = $default_value;
		}

		if (empty($use_type))
		{
			$use_type = ifset($prop, "mode");
		}

		if ($this->view)
		{
			$use_type = "view";
		}

		switch($use_type)
		{
			case "checkboxes":
				$prop["type"] = "chooser";
				$prop["multiple"] = 1;
				$prop["options"] = $choices["list_names"];
				break;

			case "radiobuttons":
				$prop["type"] = "chooser";
				$prop["options"] = $choices["list_names"];
				break;

			case "mselect":
				$prop["type"] = "select";
				$prop["multiple"] = 1;
				$prop["options"] = $choices["list_names"];
				break;

			case "view":
				$prop["options"] = $choices["list_names"];

				break;

			default:
				$prop["type"] = "select";
				if(is_array($choices))
				{
					$prop["options"] = array("" => "") + $choices["list_names"];
				}
		};

		return array($prop["name"] => $prop);
		// well, that was pretty easy. Now I need a way to tell the bloody classificator, that
		// it should use connections instead of field. And what could be easier than doing
		// it where the classificator is defined. ajee!
	}

	// this gives the actual value to property in action=view
	// and we'll make it so, so easy...
	function get_vcl_property($arr)
	{
		$vals = array();
		$prop = &$arr["property"];
		$options = safe_array($prop["options"]);
		$values = is_array($prop["value"]) ? $prop["value"] : array($prop["value"]);
		foreach($options as $key => $opt)
		{
			if(in_array($key, $values))
			{
				$vals[$key] = $opt;
			}
		}
		$arr["property"]["value"] = implode(", ", $vals);

	}

	/** returns a list of classificator objects for the given property
		@attrib api=1

		@param object_type_id optional type=int
			The oid of the object type object from what to read the classificatrs from

		@param clid optional type=int
			The class id to return the classificators for. Either this or object_type_id must be specified

		@param name required type=string
			Name of the property to return the classificators for

		@param sort_by optional type=string
			The database field to sort the returned classificators by, defaults to objects.jrk

		@param sort_callback optional type=string
			The callback to be used in usort() function for the objects.

		@errors
			none

		@returns
			array:
				0 => object_list of classificator objects
				1 => classificator manager name
				2 => type of the classificator element (mselect, select, checkboxes, radiobuttons)
				3 => default classificator
				4 => array(
					"list" => oid's of the classificator objects
					"list_names" => array(oid => name) pairs for classificators
				)

		@examples
			$cl = get_instance(CL_CLASSIFICATOR);
			$opts = $cl->get_choices(array(
				"clid" => CL_REGISTER_DATA,
				"name" => "uservar1"
			));
			echo dbg::dump($opts[4]["list_names"]); // prints the options for the given property
	**/
	function get_choices($arr)
	{
		// needs clid
		// needs $property name

		$ot = get_instance(CL_OBJECT_TYPE);
		if (isset($arr["object_type_id"]))
		{
			$ff = $arr["object_type_id"];
		}
		else
		{
			$ff = $ot->get_obj_for_class(array(
				"clid" => $arr["clid"],
			));
		}

		//if (is_object($arr["obj_inst"]) && is_oid($arr["obj_inst"]->id()))
		if (isset($arr["obj_inst"]) && is_object($arr["obj_inst"]) && is_oid($arr["obj_inst"]->meta("object_type")))
		{
			$custom_ff = $arr["obj_inst"]->meta("object_type");
			if ($this->can("view", $custom_ff))
			{
				$ff = $custom_ff;
			}
		}

		if (!is_oid($ff))
		{
			return false;
		}

		$oft = new object($ff);
		$clf = $oft->meta("classificator");
		$clf_default = $oft->meta("clf_default");
		$default_value = isset($clf_default[$arr["name"]]) ? $clf_default[$arr["name"]] : "";

		$name = $arr["name"];
		// if name is formatted like userdata[uservar1], convert it to just uservar1
		if (false !== strpos($name,"["))
		{
			$name = substr($name,strpos($name,"[")+1,-1);
		};

		$clf_type = $oft->meta("clf_type");
		$use_type = $clf_type[$name];

		// XXX: implement some error checking
		if(!$this->can("view", $clf[$name]))
		{
			return false;
		}
		$ofto = new object($clf[$name]);
		$vars = array(
			"parent" => $clf[$name],
			"class_id" => CL_META,
			"lang_id" => array(),
			"site_id" => array(),
		);

		if(isset($arr["sort_by"]))
		{
			$vars["sort_by"] = $arr["sort_by"];
		}
		else
		{
			$vars["sort_by"] = "objects.jrk";
		}

		if(isset($this->recursive) && $this->recursive == 1)
		{
			$asd = new object_tree($vars);
			$olx = $asd->to_list();
		}
		else
		{
			$olx = new object_list($vars);
		}
		$langid = aw_global_get("lang_id");
		if (!is_admin() && aw_ini_get("user_interface.full_content_trans"))
		{
			$langid = aw_global_get("ct_lang_id");
		}
		if(isset($arr["sort_callback"]))
		{
			list($sc_clid, $sc_fn) = explode("::", $arr["sort_callback"]);
			if(!isset($sc_fn))
			{
				$sc_fn = $sc_clid;
				$sc_clid = $arr["clid"];
			}
			else
			{
				$sc_clid = constant($sc_clid);
			}
			$objs = $olx->arr();
			enter_function("uasort");
			uasort($objs, array(get_instance($sc_clid), "cmp_function"));
			exit_function("uasort");
			foreach($objs as $o)
			{
				$olx_ids[] = $o->id();
				$olx_names[$o->id()] = $o->name();
			}
			$ret = array(
				"list" => $olx_ids,
				"list_names" => $olx_names,
			);
		}
		else
		{
			$ret = array(
				"list" => $olx->ids(),
				"list_names" => $olx->names(),
			);
		}
		$metamgr_obj = new object($ofto->parent());
		$transyes = $metamgr_obj->prop("transyes");
		if ($transyes)
		{
			$objs = isset($arr["sort_callback"]) ? $objs : $olx->arr();
			foreach($objs as $o)
			{
				$obj_id = $o->id();
				$obj_meta = $o->meta("tolge");
				if($name = $o->trans_get_val("name"))
				{
					$ret["list_names"][$obj_id] = $name;
				}
			}
		}

		return array($olx, $ofto->name(), $use_type, $default_value, $ret);
	}

	function process_vcl_property($arr)
	{
		$property = $arr["prop"];

		if ($property["store"] != "connect")
		{
			return false;
		};
		$items = new aw_array($property["value"]);
		$connections = array();

		if (is_oid($arr["obj_inst"]->id()))
		{
			// first I need a list of old connections.
			$oldconns = $arr["obj_inst"]->connections_from(array(
				"type" => $property["reltype"],
			));
			foreach($oldconns as $conn)
			{
				$connections[$conn->prop("to")] = $conn->prop("to");
			};
		};

		list(,,,,$choices) = $this->get_choices(array(
			"clid" => $arr["clid"],
			"name" => $property["name"],
			"obj_inst" => $arr["obj_inst"],
		));

		$ids = $this->make_keys($choices["list"]);
		// I need to list the choices
//		echo $choices;

		foreach($items->get() as $item)
		{
			// skip invalid items
			if (empty($ids[$item]))
			{
				continue;
			}

			if (is_oid($item))
			{
				// create the connection if it didn't exist
				if (empty($connections[$item]))
				{
					//print "connecting to $item with type " . constant($property["reltype"]) . "<br>";
					$arr["obj_inst"]->connect(array(
						"to" => $item,
						"reltype" => constant($property["reltype"]),
					));
				}
				unset($connections[$item]);
			}
		}

		//print "1 = <br>";
		//arr($connections);

		if (sizeof($connections) > 0)
		{
			foreach($connections as $to_remove)
			{
				//print "disconnecting from $to_remove<br>";
				$arr["obj_inst"]->disconnect(array(
					"from" => $to_remove,
				));
			};
		};

		// XXX: would be nice if connect would recognize symbolic reltypes
		// and this belongs to some place else, don't you think so?
	}

	/** returns a list of classificator names for the given property
		@attrib api=1 params=name

		@param object_type optional type=int
			The oid of the object type object from what to read the classificatrs from

		@param object_inst optional type=object
			If set, the object type is read from the objects metadata

		@param clid optional type=int
			The class id to return the classificators for. Either this or object_type_id must be specified

		@param name required type=string
			Name of the property to return the classificators for

		@errors
			none

		@returns
			array { classificator_oid => classificator_name }
	**/
	function get_options_for($arr)
	{
		if (empty($arr["name"]))
		{
			return false;
		};

		if (empty($arr["clid"]))
		{
			return false;
		};

		$cfgu = get_instance("cfg/cfgutils");

		$props = $cfgu->load_properties(array(
			"clid" => $arr["clid"],
			"filter" => array("name" => $arr["name"]),
		));

		if (is_oid($arr["object_type"]))
		{
			$active_object_id = $arr["object_type"];
		}
		else
		{
			$ot = get_instance(CL_OBJECT_TYPE);
			$active_object_id = $ot->get_obj_for_class(array(
				"clid" => $arr["clid"],
			));

			if (is_object($arr["obj_inst"]) && is_oid($arr["obj_inst"]->id()))
			{
				$custom_ff = $arr["obj_inst"]->meta("object_type");
				if (is_oid($custom_ff))
				{
					$active_object_id = $custom_ff;
				};
			};
		}

		$c_obj = new object($active_object_id);
		$clinf = $c_obj->meta("classificator");

		$items = new object_list(array(
			"parent" => $clinf[$arr["name"]],
			"class_id" => CL_META,
			"lang_id" => array(),
			"site_id" => array(),
			"sort_by" => "objects.jrk"
		));
		return $items->names();
	}
}
?>
