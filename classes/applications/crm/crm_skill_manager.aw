<?php
// crm_skill_manager.aw - Oskuste haldur

// Copied from metamgr.aw and modified.
/*

@classinfo syslog_type=ST_CRM_SKILL_MANAGER relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=instrumental

@default table=objects
@default group=general

	@property default_lvls type=relpicker reltype=RELTYPE_LEVELS field=meta method=serialize
	@caption Default tasemed

@groupinfo skills caption="Oskused" submit=no
@default group=skills

	@property skills_tlb type=toolbar no_caption=1 store=no 

	@layout skills_layout type=hbox width=20%:80%

		@property skills_tree type=treeview store=no parent=skills_layout no_caption=1
		
		@property skills_tbl type=table store=no parent=skills_layout no_caption=1
		
	@property skill type=hidden store=no 

@reltype LEVELS value=1 clid=CL_META
@caption Tasemed

*/

class crm_skill_manager extends class_base
{
	function crm_skill_manager()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_skill_manager",
			"clid" => CL_CRM_SKILL_MANAGER
		));
	}

	function callback_pre_edit($arr)
	{
		if($arr["request"]["group"] == "skills")
		{
			aw_global_set("output_charset", "utf-8");
		}

		$meta_tree = new object_tree(array(
			"parent" => $arr["obj_inst"]->id(),
			"class_id" => CL_CRM_SKILL,
			"lang_id" => array(),
			"site_id" => array(),
		));
		$olist = $meta_tree->to_list();
		$rw_tree = array();
		for ($o = $olist->begin(); !$olist->end(); $o = $olist->next())
		{
			$rw_tree[$o->parent()][$o->id()] = (int)$o->ord();
		};

		foreach($rw_tree as $parent => $items)
		{
			asort($rw_tree[$parent]);
		};

		$this->rw_tree = $rw_tree;
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "skill":
				$prop["value"] = $arr["request"]["skill"];
				break;
		}

		return $retval;
	}

	function _get_skills_tlb($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->add_save_button();
		$t->add_delete_button();
	}

	function _get_skills_tree($arr)
	{
		$ini_langs = aw_ini_get("languages.list");

		$tree = &$arr["prop"]["vcl_inst"];
		$obj = $arr["obj_inst"];
		$object_name = $obj->name();
		$tree->add_item(0, array(
			// For translation purposes is the charset for this view UTF-8...
			"name" => iconv($ini_langs[$obj->lang_id]["charset"], "UTF-8", $object_name),
			"id" => $obj->id(),
			"url" => $this->mk_my_orb("change", array(
				"id" => $obj->id(),
				"group" => $arr["prop"]["group"],
			)),
		));
		
		foreach($this->rw_tree as $parent => $items)
		{
			foreach($items as $obj_id => $ord)
			{
				$o = new object($obj_id);
				$tree->add_item($o->parent(),array(
					"name" => iconv($ini_langs[$o->lang_id()]["charset"], "UTF-8", $o->name()),
					"id" => $o->id(),
					"url" => aw_url_change_var(array("skill" => $o->id())),
				));
			};
		};

		$tree->set_selected_item($arr["request"]["skill"]);
	}

	
	function _get_skills_tbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "id",
			"caption" => t("ID"),
			"sortable" => 1,
			"align" => "right",
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"callback" => array(&$this, "callb_name"),
			"callb_pass_row" => true,
			"align" => "center",
		));
		$ini_langs = aw_ini_get("languages.list");
		$langs = get_instance("languages")->get_list();
		$olid = $arr["obj_inst"]->lang_id();
		unset($langs[$olid]);
		foreach($langs as $lang_id => $lang)
		{
			$t->define_field(array(
				"name" => $lang_id,
				"caption" => t("Nimi")." (".$lang.")",
				"align" => "center",
			));
		}
		if(!empty($arr["request"]["skill"]))
		{
			$t->define_field(array(
				"name" => "subheading",
				"caption" => t("Vahepealkiri (ei saa siduda isikuga)"),
				"align" => "center",
			));
		}
		else
		{
			$t->define_field(array(
				"name" => "lvl",
				"caption" => t("Saab m&auml;&auml;rata taset"),
				"align" => "center",
			));
			$t->define_field(array(
				"name" => "lvl_meta",
				"caption" => t("Tasemed"),
				"align" => "center",
			));
		}
		$t->define_field(array(
			"name" => "ord",
			"caption" => t("Jrk"),
			"sortable" => 1,
			"callback" => array(&$this, "callb_ord"),
			"callb_pass_row" => true,
			"align" => "center",
		));
		$t->define_chooser(array(
			"field" => "id",
			"name"  => "sel",
		));

		$this->mt_parent = false;

		if (!empty($arr["request"]["skill"]))
		{
			$root_obj = new object($arr["request"]["skill"]);
			$this->mt_parent = $root_obj->id();
		}
		else
		{
			$root_obj = $arr["obj_inst"];
		};

		$olist = new object_list(array(
			"parent" => $root_obj->id(),
			"class_id" => CL_CRM_SKILL,
			"lang_id" => array(),
			"site_id" => array(),
			"sort_by" => "objects.jrk",
		));
		
		$options[0] = t("--vali--");
		foreach($arr["obj_inst"]->connections_from(array("reltype" => "RELTYPE_LEVELS")) as $conn)
		{
			$to = $conn->to();
			$options[$to->id()] = $to->name();
		}

		$new_data = array(
			"id" => "new",
			"is_new" => 1,
			"name" => "",
			"subheading" => html::checkbox(array(
				"name" => "submeta[new][subheading]",
				"value" => 1,
			)),
			"lvl" => html::checkbox(array(
				"name" => "submeta[new][lvl]",
				"value" => 1,
			)),
			"ord" => "",
			"lvl_meta" => html::select(array(
				"name" => "submeta[new][lvl_meta]",
				"options" => $options,
				"value" => $arr["obj_inst"]->prop("default_lvls"),
			)),
		);
		foreach($langs as $lang_id => $lang)
		{
			$new_data[$lang_id] = html::textbox(array(
				"name" => "submeta[new][trans][".$lang_id."]",
				"value" => "",
				"size" => 40,
			));
		}

		$t->define_data($new_data);

		foreach($olist->arr() as $o)
		{
			$id = $o->id();
			$var_name = $o->name();

			$trans = array(
				"is_new" => 0,
				"id" => $id,
				"name" => iconv($ini_langs[$olid]["charset"], "UTF-8", $var_name),
				"subheading" => html::checkbox(array(
					"name" => "submeta[".$id."][subheading]",
					"checked" => $o->prop("subheading"),
				)),
				"lvl" => html::checkbox(array(
					"name" => "submeta[" . $id . "][lvl]",
					"checked" => $o->prop("lvl"),
				)),
				"lvl_meta" => html::select(array(
					"name" => "submeta[" . $id . "][lvl_meta]",
					"options" => $options,
					"value" => $o->prop("lvl_meta"),
				)),
				"ord" => $o->ord(),
			);
			$tr = $o->meta("translations");
			foreach($langs as $lang_id => $lang)
			{
				$trans[$lang_id] = html::textbox(array(
					"name" => "submeta[" . $id . "][trans][".$lang_id."]",
					"value" => iconv($ini_langs[$lang_id]["charset"], "UTF-8", $tr[$lang_id]["name"]),
					"size" => 40,
				));
			}
			$t->define_data($trans);
		};

		if(!empty($arr["request"]["skill"]))
		{
			$other_data = array(
				"id" => "other",
				"name" => iconv($ini_langs[$olid]["charset"], "UTF-8", $root_obj->other),
			);
			$tr = $root_obj->meta("translations");
			foreach($langs as $lang_id => $lang)
			{
				$other_data[$lang_id] = html::textbox(array(
					"name" => "submeta[other][trans][".$lang_id."]",
					"value" => iconv($ini_langs[$lang_id]["charset"], "UTF-8", $tr[$lang_id]["other"]),
					"size" => 40,
				));
			}
			$t->define_data($other_data);
		}

		$t->set_sortable(false);
	}

	function callb_name($arr)
	{
		return html::textbox(array(
			"name" => "submeta[" . $arr["id"] . "][name]",
			"size" => 40,
			"value" => $arr["name"],
		));
	}

	function callb_ord($arr)
	{
		if($arr["id"] == "other")
		{
			return "";
		}
		return html::textbox(array(
			"name" => "submeta[" . $arr["id"] . "][ord]",
			"size" => 4,
			"value" => $arr["ord"],
		));
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "skills_tbl":
				$this->submit_meta($arr);
				break;
		}

		return $retval;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	function submit_meta($arr = array())
	{
		$obj = $arr["obj_inst"];
		$olid = $obj->lang_id();
		$curlid = aw_global_get("lang_id");
		$ini_langs = aw_ini_get("languages.list");
		$new = $arr["request"]["submeta"]["new"];
		if ($new["name"])
		{
			// now I need to create a new object under this object
			$parent = $obj->id();
			if ($arr["request"]["skill"])
			{
				$parent = $arr["request"]["skill"];
				$parent_obj = obj($parent);
				$new["lvl"] = $parent_obj->prop("lvl");
				$new["lvl_meta"] = $parent_obj->prop("lvl_meta");
			}
			$no = new object;
			$no->set_class_id(CL_CRM_SKILL);
			$no->set_status(STAT_ACTIVE);
			$no->set_lang_id($olid);
			$no->set_parent($parent);
			$no->set_name(iconv("UTF-8", $ini_langs[$olid]["charset"], $new["name"]));
			$no->set_prop("subheading", $new["subheading"]);
			$no->set_prop("lvl", $new["lvl"]);
			$no->set_prop("lvl_meta", $new["lvl_meta"]);
			$no->set_ord((int)$new["ord"]);
			$tr = array();
			foreach($new["trans"] as $lang_id => $trans_name)
			{
				$tr[$lang_id]["name"] = iconv("UTF-8", $ini_langs[$lang_id]["charset"], $trans_name);
				$no->set_meta("trans_".$lang_id."_status", 1);
				$no->set_meta("trans_".$lang_id."_modified", time());
			}
			$no->set_meta("translations", $tr);
			$no->save();
		};

		$other = $arr["request"]["submeta"]["other"];
		if($this->can("edit", $arr["request"]["skill"]) && $other["name"])
		{
			$o = obj($arr["request"]["skill"]);
			$t = $o->meta("translations");
			$o->set_prop("other", iconv("UTF-8", $ini_langs[$olid]["charset"], $other["name"]));
			$o->set_prop("other_jrk", $other["ord"]);
			foreach($other["trans"] as $lang_id => $trans_other)
			{
				$t[$lang_id]["other"] = iconv("UTF-8", $ini_langs[$lang_id]["charset"], $trans_other);
				$o->set_meta("trans_".$lang_id."_status", 1);
				$o->set_meta("trans_".$lang_id."_modified", time());
			}
			$o->set_meta("translations", $t);
			$o->save();
		}
		else
		if($this->can("edit", $arr["request"]["skill"]))
		{
			$o = obj($arr["request"]["skill"]);
			$t = $o->meta("translations");
			$o->set_prop("other", "");
			$o->set_prop("other_jrk", 0);
			foreach(array_keys($t) as $lang_id)
			{
				unset($t[$lang_id]["other"]);
				$o->set_meta("trans_".$lang_id."_modified", time());
			}
			$o->set_meta("translations", $t);
			$o->save();
		}

		$submeta = $arr["request"]["submeta"];
		unset($submeta["new"]);
		unset($submeta["other"]);
		if (is_array($submeta))
		{
			foreach($submeta as $skey => $sval)
			{
				$so = new object($skey);
				$so->set_name(iconv("UTF-8", $ini_langs[$olid]["charset"], $sval["name"]));
				$so->set_prop("subheading", $sval["subheading"]);
				if($so->parent() == $arr["obj_inst"]->id())
				{
					$so->set_prop("lvl", $sval["lvl"]);
					$so->set_prop("lvl_meta", $sval["lvl_meta"]);
				}
				else
				{
					$parent_obj = obj($so->parent());
					$so->set_prop("lvl", $parent_obj->prop("lvl"));
					$so->set_prop("lvl_meta", $parent_obj->prop("lvl_meta"));
				}
				$tr = $so->meta("translations");
				foreach($sval["trans"] as $lang_id => $trans_name)
				{
					$tr[$lang_id]["name"] = iconv("UTF-8", $ini_langs[$lang_id]["charset"], $trans_name);
					$so->set_meta("trans_".$lang_id."_status", 1);
					$so->set_meta("trans_".$lang_id."_modified", time());
				}
				$so->set_meta("translations", $tr);
				$so->set_ord($sval["ord"]);
				$so->save();
			};
		};
	}

	function callback_mod_retval($arr)
	{
		if ($arr["request"]["skill"])
		{
			$arr["args"]["skill"] = $arr["request"]["skill"];
		};
	}

	/**
		@attrib name=get_skills

		@param id required type=oid
			The oid of the skill_manager object.
	**/
	function get_skills($arr)
	{
		$o = obj($arr["id"]);

		
		$meta_tree = new object_tree(array(
			"parent" => $o->id(),
			"class_id" => CL_CRM_SKILL,
			"lang_id" => array(),
			"site_id" => array(),
		));
		$olist = $meta_tree->to_list();
		$rw_tree = array();
		for ($o = $olist->begin(); !$olist->end(); $o = $olist->next())
		{
			$rw_tree[$o->parent()][$o->id()]["name"] = $o->name();
			$rw_tree[$o->parent()][$o->id()]["subheading"] = $o->prop("subheading");
			$rw_tree[$o->parent()][$o->id()]["lvl"] = $o->prop("lvl");
			$rw_tree[$o->parent()][$o->id()]["lvl_meta"] = $o->prop("lvl_meta");
		};

		foreach($rw_tree as $parent => $items)
		{
			asort($rw_tree[$parent]);
		};
		return $rw_tree;
	}
}

?>
