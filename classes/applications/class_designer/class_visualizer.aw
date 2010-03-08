<?php
/*
@classinfo maintainer=kristo
*/
class class_visualizer extends class_base
{
	function class_visualizer()
	{
		$this->init("");
		$this->tpl_init("class_visualizer");
	}

	/**
		@attrib name=view default=1
		@param id optional type=int
		@param group optional

	**/
	function view($arr)
	{
		$obj_id = $arr["id"];
		if (empty($obj_id) && is_oid(aw_global_get("class")))
		{
			$obj_id = aw_global_get("class");
		};
		$this->cls_id = $obj_id;
		$o = new object($obj_id);
		if ($o->class_id() != CL_CLASS_DESIGNER)
		{
			die(t("I'm so depessed"));
		};
		$tree = new object_tree(array(
			"parent" => $o->id(),
			"class_id" => CL_PROPERTY_GROUP,
		));
		$tlist = $tree->to_list();
		$group = $arr["group"];
		//arr($tlist);
                $cli = get_instance("cfg/htmlclient");
		$cf = get_instance(CL_CLASS_DESIGNER);
		$items = $cf->elements;
		$clinf = aw_ini_get("classes");
		// kui gruppi pole, siis vali esimene
		// XXX: ühendada see algoritm sellega, mis tehakse classbases
		//$group = $arr["group"];
		$groupitems = array();
		//$active_groups = array();
		foreach($tlist->arr() as $xo)
		{
			$parent_obj = new object($xo->parent());
			if ($parent_obj->class_id() == CL_PROPERTY_GROUP)
			{
				$groupitems[$xo->parent()]["items"][] = array(
					"caption" => $xo->name(),
					"id" => $xo->id(),
				);
			}
			else
			{
				if (empty($group))
				{
					$group = $xo->id();
				};
				$groupitems[$o->id()]["items"][] = array(
					"caption" => $xo->name(),
					"id" => $xo->id(),
				);
			};
		};
		
		// URL-ist anti grupp. Kui sellel grupil on lapsi, mille tüübiks on ka grupp, siis me ka näitame neid
	
		if (is_oid($group))
		{
			$active_groups = array($group);
			$children = new object_list(array(
				"parent" => $group,
				"class_id" => CL_PROPERTY_GROUP,
			));
			if ($children->count() > 0)
			{
				$use_group_o = $children->begin();
				$use_group = $use_group_o->id();
				$active_groups[] = $use_group;
			}
			else
			{
				$grp_obj = new object($group);
				$parent_obj = new object($grp_obj->parent());
				if ($parent_obj->class_id() == CL_PROPERTY_GROUP)
				{
					$active_groups[] = $parent_obj->id(); 
				};
				$use_group = $group;
			};
		};

		foreach($groupitems as $key => $dat)
		{
			foreach($dat["items"] as $gd)
			{
				if ($key == $o->id())
				{
					$level = 1;
					$tab_parent = "";
				}
				else
				{
					$level = 2;
					$tab_parent = $key;
				};

				if ($level == 2 && !in_array($tab_parent,$active_groups))
				{
					continue;
				};

				// aga teise taseme grupid lisame ainult siis, kui nad on aktiivsed, eh?

				$cli->add_tab(array(
					"id" => $gd["id"],
					"caption" => $gd["caption"],
					"active" => in_array($gd["id"],$active_groups),
					"parent" => $tab_parent,
					"level" => $level,
					"link" => $this->mk_my_orb("view",array(
						//"id" => $o->id(),
						"group" => $gd["id"],
						"class" => $this->cls_id,
					)),
				));
			};

		};

		$elements = new object_tree(array(
			"parent" => $use_group,
		));

		$elements = $elements->to_list();
		foreach($elements->arr() as $el)
		{
			$clid = $el->class_id();
			if (in_array($clid,$items))
			{
				$eltype = $clinf[$clid]["def"];
				$eltype = strtolower(str_replace("CL_PROPERTY_","",$eltype));
				$propdata = array(
					"name" => $el->name(),
					"caption" => $el->name(),
					"type" => $eltype,
				);
				if ($clid == CL_PROPERTY_CHOOSER)
				{
					$propdata["multiple"] = $el->prop("multiple");
					$propdata["orient"] = $el->prop("orient") == 1 ? "vertical" : "";
					$propdata["options"] = explode("\n",$el->prop("options"));
				}
				else
				{
					$ti = get_instance($clid);
					if (method_exists($ti, "get_visualizer_prop"))
					{
						$ti->get_visualizer_prop($el, $propdata);
					}
				}
				$cli->add_property($propdata);
			};
		};
		/* so what's the use of this thing anyway? -- ahz
		// I need to invoke htmlclient directly
		foreach($tmp as $ta)
		{
			$cli->add_property($ta);
		};
		*/
		$cli->finish_output(array(
			"action" => "submit",
			"data" => array(
				//"id" => $o->id(),
				"group" => $use_group,
				"class" => get_class($this),
				"class" => $this->cls_id,
			),
		));
		$cont = $cli->get_result();

		return $cont;

	}

	/**
		@attrib name=submit
	**/
	function submit($arr)
	{

		// XXX: create a proper list of properties
		$cl_obj = new object($arr["class"]);		
		if (is_oid($arr["id"]))
		{
			$clx = new object($arr["id"]);
		}
		else
		{
			$clx = new object();
			$clx->set_class_id($cl_obj->prop("reg_class_id"));
			$clx->set_parent($arr["parent"]);
			$clx->set_status(STAT_ACTIVE);
		};
		foreach($arr as $key => $val)
		{
			if (is_numeric($key))
			{
				$clx->set_meta($key,$val);
			};
		};

		$clx->set_name($arr[$cl_obj->prop("object_name")]);
		$clx->save();

		$rv = $this->mk_my_orb("change",array("class" => $arr["class"],"group" => $arr["group"],"id" => $clx->id()),$arr["class"]);
		//print $rv;
		return $rv;

	}

	function get_classinfo($arr)
	{
		// check if we need to regenerate the class def
		$ot = new object_tree(array(
			"parent" => $arr["class_id"],
			"lang_id" => array(),
			"site_id" => array()
		));
		$ol = $ot->to_list();
		foreach($ol->arr() as $o)
		{
			$mod = max($mod, $o->modified());
		}

		$fn = aw_ini_get("site_basedir")."/files/classes/".$arr["class_id"].".aw";

		if (filemtime($fn) < $mod)
		{
			$cd = get_instance(CL_CLASS_DESIGNER);
			$cd->callback_post_save(array(
				"obj_inst" => obj($arr["class_id"])
			));
		}

		$anal = get_instance("cfg/propcollector");

		$inf = $anal->parse_file(array(
			"file" => $fn
		));
		return $inf["properties"]["classinfo"];
	}

	function get_class_groups($arr)
	{
		$anal = get_instance("cfg/propcollector");

		$cb_inf = $anal->parse_file(array(
			"file" => aw_ini_get("classdir")."/class_base.aw"
		));

		$inf = $anal->parse_file(array(
			"file" => aw_ini_get("site_basedir")."/files/classes/".$arr["obj_id"].".aw"
		));

		$grpi = $cb_inf["properties"]["groupinfo"];
		foreach(safe_array($inf["properties"]["groupinfo"]) as $gn => $gi)
		{
			$grpi[$gn] = $gi;
		}

		$o = new object($arr["obj_id"]);
		if ($o->prop("relationmgr") == 1)
		{
			$grpi["relationmgr"] = array(
				"caption" => t("Seostehaldur"),
				//"no_form" => 1,
				"submit" => "no",
			);
			if($_REQUEST["srch"] == 1)
			{
				$grpi["relationmgr"]["submit_method"] = "get";
			}
		};
		return $grpi;
	}

	function get_group_properties($arr)
	{
		$anal = get_instance("cfg/propcollector");
		$cb_inf = $anal->parse_file(array(
			"file" => aw_ini_get("classdir")."/class_base.aw"
		));
		$inf = $anal->parse_file(array(
			"file" => aw_ini_get("site_basedir")."/files/classes/".$arr["id"].".aw"
		));
		$grpi = array();
		
		foreach(safe_array($cb_inf["properties"]) as $gn => $gi)
		{
			if (is_numeric($gn))
			{
				$grpi[$gi["name"]] = $gi;
			}
		}

		foreach(safe_array($inf["properties"]) as $gn => $gi)
		{
			if (is_numeric($gn))
			{
				$grpi[$gi["name"]] = $gi;
			}
		}
		return $grpi;
	}

	function get_layouts($arr)
	{
		$anal = get_instance("cfg/propcollector");
		$inf = $anal->parse_file(array(
			"file" => aw_ini_get("site_basedir")."/files/classes/".$arr["class_id"].".aw"
		));
		return $inf["layout"];
	}
	
	/**
		@attrib name=playground
	**/
	function playground($arr)
	{
		$this->read_template("playground.tpl");
		return $this->parse();

	}

	/**
		@attrib name=proc all_args=1
	**/
	function proc($arr)
	{
		$text = $_POST["text"];
		// ja nüüd ei olegi muud vaja, kui sellest asjast propertyte definitsioon genereerida
		$anakin = get_instance("cfg/propcollector");
		$result = $anakin->parse_file(array(
			"data" => $text,
		));

		$objprops = array();

		$htmlc = get_instance("cfg/htmlclient", array("template" => "default.tpl"));
		foreach($result["properties"] as $key => $val)
		{
			if (is_numeric($key))
			{
				$htmlc->add_property($val);
			};
		};

		$htmlc->finish_output(array("submit" => "no"));
		return $htmlc->get_result();

	}

	
};
?>
