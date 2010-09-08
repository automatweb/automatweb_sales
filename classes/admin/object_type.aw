<?php
/*

@classinfo relationmgr=yes

@default table=objects
@default group=general

	@property type type=select field=subclass
	@caption Objektit&uuml;&uuml;p

@default field=meta
@default method=serialize

	@property use_cfgform type=relpicker reltype=RELTYPE_OBJECT_CFGFORM
	@caption Kasuta seadete vormi

	@property configuration type=callback callback=gen_config store=no group=settings
	@caption Klassi konfiguratsioon

	@property default_object type=chooser store=no group=defobj orient=vertical
	@caption Vaikimisi objekt

@groupinfo settings caption="Klassi konfiguratsioon"
@groupinfo defobj caption="Aktiivne objekt"

@reltype OBJECT_CFGFORM value=1 clid=CL_CFGFORM
@caption Seadete vorm

@reltype META_ELEMENTS value=2 clid=CL_META
@caption Muutuja

*/

class object_type extends class_base
{
	function object_type()
	{
		$this->init(array(
			"clid" => CL_OBJECT_TYPE,
		));
	}

	function get_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "type":
				$data["options"] = $this->get_type_picker();
				$old_type = $arr["obj_inst"]->meta("type");
				if (!empty($old_type))
				{
					$data["selected"] = $old_type;
				};
				break;

			case "default_object":
				$ol = new object_list(array(
					"class_id" => CL_OBJECT_TYPE,
					"subclass" => $arr["obj_inst"]->prop("type")
				));
				$data["options"] = $ol->names();
				for ($o = $ol->begin(); !$ol->end(); $o = $ol->next())
				{
					$flg = $o->flag(OBJ_FLAG_IS_SELECTED);
					if ($o->flag(OBJ_FLAG_IS_SELECTED))
					{
						$data["value"] = $o->id();
					}
				}
				break;
		}
		return $retval;
	}

	function set_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "name":
				if ($data["value"] == "" && $arr["request"]["type"])
				{
					$clss = aw_ini_get("classes");
					$data["value"] = $clss[$arr["request"]["type"]]["name"];
				}
				break;

			case "configuration":
				$arr["obj_inst"]->set_meta("classificator",$arr["request"]["classificator"]);
				$arr["obj_inst"]->set_meta("clf_type",$arr["request"]["clf_type"]);
				$arr["obj_inst"]->set_meta("clf_default",$arr["request"]["clf_default"]);
				break;

			case "type":
				$old_type = $arr["obj_inst"]->meta("type");
				if (!empty($old_type))
				{
					$arr["obj_inst"]->set_meta("type","");
				};
				break;

			case "default_object":
				$ol = new object_list(array(
					"class_id" => $this->clid,
					"subclass" => $arr["obj_inst"]->prop("type")
				));

				for ($o = $ol->begin(); !$ol->end(); $o = $ol->next())
				{
					if ($o->flag(OBJ_FLAG_IS_SELECTED) && $o->id() != $data["value"])
					{
						$o->set_flag(OBJ_FLAG_IS_SELECTED, false);
						$o->save();
					}
					elseif ($o->id() == $data["value"] && !$o->flag(OBJ_FLAG_IS_SELECTED))
					{
						$o->set_flag(OBJ_FLAG_IS_SELECTED, true);
						$o->save();
					};
				}
				break;

		};
		return $retval;
	}

	/** Returns the object type object id for the given class_id
		@attrib api=1 params=name

		@param clid required type=class_id
			The class id to find the object type for

		@param general optional type=bool
			If set to true, the object type returned does not have to be set as default, it is randomly selected from the available ones for the given class.

		@returns
			Matching object type object id
	**/
	function get_obj_for_class($arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_OBJECT_TYPE,
			"subclass" => $arr["clid"]
		));
		$rv = false;
		for ($o = $ol->begin(); !$ol->end(); $o = $ol->next())
		{
			if(isset($arr["general"]) && $arr["general"] === true)
			{
				$rv = $o->id();
			}
			else
			{
				$flg = $o->flag(OBJ_FLAG_IS_SELECTED);
				if ($o->flag(OBJ_FLAG_IS_SELECTED))
				{
					$rv = $o->id();
				};
			}
		};
		return $rv;
	}

	function gen_config($arr)
	{
		$obj = $arr["obj_inst"];
		$type = $obj->prop("type");

		$conns = $obj->connections_from(array(
			"type" => "RELTYPE_META_ELEMENTS",
		));

		$opts = array("" => "");
		foreach($conns as $item)
		{
			$opts[$item->prop("to")] = $item->prop("to.name");
		};

		$mx = $obj->meta("classificator");
		$ct = $obj->meta("clf_type");
		$clf_defaults = $obj->meta("clf_default");

		$prop = $arr["prop"];

		// I need a new method -- get_properties_by_type
		// class_base fxt thingie needs it too to retrieve only the toolbar
		$defaults = $this->get_properties_by_type(array(
			"clid" => $obj->prop("type"),
			"type" => "classificator",
		));
		$types = array(
			"" => t("-vali-"),
			"mselect" => t("multiple select"),
			"select" => t("select"),
			"checkboxes" => t("checkboxid"),
			"radiobuttons" => t("radiobuttons"),
		);
		$rv = array();

		foreach($defaults as $key => $val)
		{
			$rv["c".$key] = array(
				"name" => "c".$key,
				"type" => "layout",
				"rtype" => "hbox",
				"caption" => $key,
			);

			$rv[$key] = array(
				"name" => "classificator[" . $key . "]",
				"selected" => $mx[$key],
				"type" => "select",
				"caption" => $key . " " . t("Oks"),
				"options" => $opts,
				"parent" => "c".$key,
			);

			$rv["x".$key] = array(
				"name" => "clf_type[" . $key . "]",
				"type" => "select",
				"caption" => t("T&uuml;&uuml;p"),
				"options" => $types,
				"selected" => $ct[$key],
				"parent" => "c".$key,
			);

			if (isset ($mx[$key]))
			{
				$classificator = get_instance(CL_CLASSIFICATOR);
				$prop_args = array (
					"clid" => $obj->prop("type"),
					"name" => $key,
				);
				list ($options, $name, $use_type) = $classificator->get_choices($prop_args);
				$rv["d".$key] = array(
					"name" => "clf_default[" . $key . "]",
					"caption" => t("Vaikimisi v&auml;limus"),
					"options" => $options ? $options->names() : array(),
					"value" => $clf_defaults[$key],
					"selected" => $clf_defaults[$key],
					"parent" => "c".$key,
				);

				switch ($ct[$key])
				{
					case "mselect":
						$rv["d".$key]["type"] = "select";
						$rv["d".$key]["multiple"] = 1;
						break;

					case "select":
						$rv["d".$key]["type"] = "select";
						break;

					case "checkboxes":
						$rv["d".$key]["type"] = "chooser";
						$rv["d".$key]["multiple"] = 1;
						break;

					case "radiobuttons":
						$rv["d".$key]["type"] = "chooser";
						break;

					default:
						$rv["d".$key]["type"] = "text";
						$rv["d".$key]["value"] = "";
						break;
				}
			}
		}

		return $rv;
	}

	private function get_type_picker()
	{
		$ret = array();
		$tmp = aw_ini_get("classes");
		foreach($tmp as $clid => $cldat)
		{
			if (isset($cldat["name"]))
			{
				$ret[$clid] = $cldat["name"];
			}
		}
		asort($ret);
		$ret = array("__all_objs" => t("K&otilde;ik")) + $ret;
		return $ret;
	}

	/** builds the url for adding a new object, given an object type object id for the class
		@attrib api=1 params=name

		@param id required type=oid
			The object type object id to read the addable class id from

		@param parent required type=oid
			The object to add the new object under

		@param section optional type=oid
			The section to display the new object adding form under

		@returns
			url that displays the new object form for the class specified in the given object type object
	**/
	function get_add_url($arr)
	{
		$o = new object($arr["id"]);

		$tmp = aw_ini_get("classes");
		$clss = $tmp[$o->prop("type")]["file"];
		if ($clss == "document")
		{
			$clss = "doc";
		}
		$rv = $this->mk_my_orb("new", array(
			"parent" => $arr["parent"],
			"period" => aw_global_get("period"),
			"section" => $arr["section"],
			"cfgform" => $o->prop("use_cfgform"),
		),$clss);
		return $rv;
	}

	/** reads the properties from the object type $o and returns them. honors cfgforms
		@attrib api=1 params=pos

		@param o required type=cl_object_type
			The object type object to read the class and cfgform from

		@returns
			array { property name => property data, ... }  containing all properties in the class given in the object type
			or if the object type also has a config form set, then properties are read from that
	**/
	function get_properties($o)
	{
		// get a list of properties in both classes
		$cfgx = new cfgutils();
		$ret = $cfgx->load_properties(array(
			"clid" => $o->subclass(),
		));

		if ($o->prop("use_cfgform"))
		{
			$class_i = get_instance($o->subclass() == CL_DOCUMENT ? "doc" : $o->subclass());
			$tmp = $class_i->load_from_storage(array(
				"id" => $o->prop("use_cfgform")
			));

			$dat = array();
			foreach($tmp as $pn => $pd)
			{
				$dat[$pn] = $ret[$pn];
				$dat[$pn]["caption"] = $pd["caption"];
			}
			$ret = $dat;
		}

		return $ret;
	}

	/**
		@attrib name=get_sysdefault api=1 params=pos

		@param clid required type=class_id
			The class_id the system default object_type is asked for.

	**/
	private function get_default_settings($clid)
	{
		$o = obj(object_type::get_obj_for_class(array(
			"clid" => $clid,
		)));
		return $o->meta();
	}

	/** Returns array of classificator options
		@attrib api=1 params=name

		@param clid required type=class_id
			The class id to get the options for

		@param classificator required type=string
			The property to get options for

		@returns
			false, if no settings are found
			array { cl_meta_oid => name, ... } for all defined options
	**/
	function get_classificator_options($arr)
	{
		$conf = object_type::get_default_settings($arr["clid"]);
		if(!is_oid($conf["classificator"][$arr["classificator"]]))
		{
			return false;
		}

		$ol = new object_list(array(
			"class_id" => CL_META,
			"parent" => $conf["classificator"][$arr["classificator"]],
			"status" => object::STAT_ACTIVE,
			"sort_by" => "jrk",
			"lang_id" => array(),
		));
		//return $ol->names();
		$ops = array();
		foreach($ol->arr() as $o)
		{
			$ops[$o->id()] = $o->trans_get_val("name");
		}
		return $ops;
	}
}
