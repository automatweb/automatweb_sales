<?php
/*
@classinfo syslog_type=ST_PERSON_SKILL_MANAGER relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop
@tableinfo aw_person_skill_manager master_index=brother_of master_table=objects index=aw_oid

@default table=aw_person_skill_manager
@default group=general

@property company type=relpicker reltype=RELTYPE_COMPANY table=objects field=meta method=serialize
@caption Organisatsioon

@groupinfo skills caption="Oskused"
@default group=skills
	@property skills_tb type=toolbar no_caption=1 store=no 

	@layout skills_layout type=hbox width=20%:80%
		@layout skills_tree_l type=vbox parent=skills_layout closeable=1 area_caption=Oskuste&nbsp;puu
			@property skills_tree type=treeview store=no parent=skills_tree_l no_caption=1
		@layout skills_table_l type=vbox parent=skills_layout closeable=1 area_caption=Oskuste&nbsp;tabel
			@property skills_tbl type=table store=no parent=skills_table_l no_caption=1

@groupinfo workers caption="T&ouml;&ouml;tajad"
@default group=workers
	@property workers_tb type=toolbar no_caption=1 store=no 

	@layout workers_layout type=hbox width=20%:80%
		@layout workers_tree_l type=vbox parent=workers_layout closeable=1 area_caption=T&ouml;&ouml;tajate&nbsp;puu
			@property workers_tree type=treeview store=no parent=workers_tree_l no_caption=1
		@layout workers_table_l type=vbox parent=workers_layout closeable=1 area_caption=T&ouml;&ouml;tajate&nbsp;tabel
			@property workers_tbl type=table store=no parent=workers_table_l no_caption=1

@reltype COMPANY value=2 clid=CL_CRM_COMPANY
@caption Tasemed

*/

class person_skill_manager extends class_base
{
	function person_skill_manager()
	{
		$this->init(array(
			"tpldir" => "applications/crm/person_skill_manager",
			"clid" => CL_PERSON_SKILL_MANAGER
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
		}

		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
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

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_person_skill_manager(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => ""
				));
				return true;
		}
	}

	function _get_skills_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"url" => html::get_new_url(CL_PERSON_SKILL, $arr["request"]["skill"]?$arr["request"]["skill"]:$arr["obj_inst"]->id(), array("return_url" => get_ru())),
		));
		$tb->add_delete_button();
		$tb->add_save_button();
	}

	function _get_workers_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$btn = array(
			"name" => "new",
			"img" => "new.gif",
			"url" => "javascript:alert('".t("Valige isik kellele oskus lisada!")."');",
		);
		$parent =  $arr["request"]["parent"];
		if(!is_oid($parent))
		{
			$a = explode("_" , $parent);
			$parent = $a[0];
			$section = $a[1];
		}
		if($this->can("view" ,$parent))
		{
			$p = obj($parent);
			if($p->class_id() == CL_CRM_PERSON)
			{
				$btn["url"] = html::get_new_url(
					CL_PERSON_HAS_SKILL,
					$parent?$parent:$arr["obj_inst"]->id(),
					 array("alias_to" => $parent, "reltype" => 53, "return_url" => get_ru())
				);
			}
			
		}

		$tb->add_button($btn);
		$tb->add_delete_button();
		$tb->add_save_button();
	}


	function _get_skills_tree($arr)
	{
		$tree = &$arr['prop']['vcl_inst'];
		$skills = $arr["obj_inst"]->get_all_skills();

		$skill = reset($skills->arr());

		if(!is_object($skill))
		{
			return;
		}

		
		$tree =&$arr["prop"]["vcl_inst"];
		$tree->start_tree(array (
			"type" => TREE_DHTML,
			"branch" => 1,
			"has_root" => 1,
			"tree_id" => "person_skills_tree",
			"persist_state" => 1,
			"root_name" => t("Oskused"),
			"root_url" => "#",
			"get_branch_func" => $this->mk_my_orb("skills_tree_leaf",array(
				"clid" => $arr["clid"],
				"group" => $arr["request"]["group"],
				"oid" => $arr["obj_inst"]->id(),
				"set_retu" => get_ru(),
				"parent" => " ",
			)),
		));

		$skills_arr = array();
		foreach($skills->arr() as $skill)
		{
			if($skill->prop("parent.class_id") == CL_PERSON_SKILL)
			{
				$skills_arr[$skill->parent()][] = $skill;
			}
			else
			{
				$skills_arr[0][] = $skill;
			}
		}

		foreach($skills_arr[0] as $skill)
		{
			$tree->add_item(0, array(
				"id" => $skill->id(),
				"name" => $skill->name(),
				"url" => aw_url_change_var(array(
					"skill" =>  $skill->id(),
				)),
			));
			if(is_array($skills_arr[$skill->id()]))
			{
				$tree->add_item($skill->id(), array(
					"id" => 123,
					"name" => $skill->name(),
					"url" => aw_url_change_var(array(
						"skill" =>  $skill->id(),
					)),
				));
			}
		}
		
	}

	/**
		@attrib name=skills_tree_leaf all_args=1
	**/
	function skills_tree_leaf($arr)
	{
		extract($arr);
		$tree = get_instance("vcl/treeview");
		$tree->start_tree(array (
			"type" => TREE_DHTML,
			"branch" => 1,
			"tree_id" => "offers_tree_".$arr["parent"],
//			"persist_state" => 1,
		));

		$ol = new object_list(array(
			"class_id" => CL_PERSON_SKILL,
			"lang_id" => array(),
			"site_id" => array(),
			"parent" => $arr["parent"],
		));

		$ol2 = new object_list(array(
			"class_id" => CL_PERSON_SKILL,
			"lang_id" => array(),
			"site_id" => array(),
			"parent" => $ol->ids(),
		));

		$parents = array();
		foreach($ol2->arr() as $child)
		{
			$parents[$child->parent()] = $child->parent();
		}

		foreach($ol->arr() as $o)
		{
			$tree->add_item(0, array(
				"id" => $o->id(),
				"name" => $o->name(),
				"url" => aw_url_change_var("skill" , $o->id(),$set_retu),
			));
			if($parents[ $o->id()])
			{
				$tree->add_item($o->id(), array(
					"id" => $o->id()."t",
					"name" => "t",
				));
			}
		}
		die($tree->finalize_tree());
	}

	function _get_workers_tree($arr)
	{
		if (!$this->can("view", $arr["obj_inst"]->prop("company")))
		{
			die("Tubade kaust on valimata, palun valige see <a href='/automatweb/orb.aw?class=person_skill_manager&action=change&id=".$arr["obj_inst"]->id()."'>siit</a>");
		}

		$org = obj($arr["obj_inst"]->prop("company"));

		$tree = &$arr['prop']['vcl_inst'];
		$node_id = 0;
//		$i = get_instance(CL_CRM_COMPANY);
		$tree->start_tree(array (
			"type" => TREE_DHTML,
			"branch" => 1,
			"has_root" => 1,
			"tree_id" => "comp_workers_tree",
			"persist_state" => 1,
			"root_name" => $org->name(),
			"root_url" => "#",
			"var" => "op",
			"get_branch_func" => $this->mk_my_orb("workers_tree_leaf",array(
				"clid" => $arr["clid"],
				"group" => $arr["request"]["group"],
				"oid" => $arr["obj_inst"]->id(),
				"set_retu" => get_ru(),
				"op" => $arr["request"]["parent"],
				"parent" => " ",
			)),
		));
		
		$sections = $org->get_root_sections();
		
		foreach($sections->arr() as $section)
		{
			if($section->has_sections() || $section->has_workers())
			{
				$lid = $section->id();
				$lid_name = $section->name();
				if ($_GET["parent"] == $lid)
				{
					$lid_name = "<b>".$lid_name."</b>";
				}

				$tree->add_item(0, array(
					"id" => $lid,
					"name" => $lid_name,
					"url" => aw_url_change_var("parent", $section->id(),$arr["set_retu"]),
				));
				$tree->add_item($section->id(), array(
					"id" => $section->id().'_sub',
					"name" => " ",
				));
			}
		}


/*		$i->active_node = (int)$arr['request']['unit'];
		if(is_oid($arr['request']['cat']))
		{
			$i->active_node = $arr['request']['cat'];
		}
		$i->generate_tree(array(
			'tree_inst' => &$tree_inst,
			'obj_inst' => $org,
			'node_id' => &$node_id,
			'conn_type' => 'RELTYPE_SECTION',
			'attrib' => 'unit',
			'leafs' => true,
			'show_people' =>1 ,
		));

		$nm = t("K&otilde;ik t&ouml;&ouml;tajad");
		$tree_inst->add_item(0, array(
			"id" => CRM_ALL_PERSONS_CAT,
			"name" => $arr["request"]["cat"] == CRM_ALL_PERSONS_CAT ? "<b>".$nm."</b>" : $nm,
			"url" => aw_url_change_var(array(
				"cat" =>  CRM_ALL_PERSONS_CAT,
				"unit" =>  NULL,
			))
		));

		if ($_SESSION["crm"]["people_view"] == "edit")
		{
			
			$tree_inst->set_root_name($arr["obj_inst"]->name());
			$tree_inst->set_root_icon(icons::get_icon_url(CL_CRM_COMPANY));
			$tree_inst->set_root_url(aw_url_change_var("cat", NULL, aw_url_change_var("unit", NULL)));
		}*/
	}

	/**
		@attrib name=workers_tree_leaf all_args=1
	**/
	function workers_tree_leaf($arr)
	{
		extract($arr);
		if(!is_oid($parent))
		{
			$a = explode("_" , $parent);
			$parent = $a[0];
//			$arr["section"] = $a[1];
		}
		if(!$this->can("view" , $parent))
		{
			die();
		}
		$p = obj($parent);

		if($p->class_id() == CL_CRM_SECTION)
		{
			return $this->_section_leaf($arr);
		}
		elseif($p->class_id() == CL_CRM_PROFESSION)
		{
			return $this->_profession_leaf($arr);
		}
		die();
	}

	private function _section_leaf($arr)
	{
		extract($arr);
		if(!is_oid($parent))
		{
			$a = explode("_" , $parent);
			$parent = $a[0];
			$section = $a[1];
		}
		$p = obj($parent);
		$tree = get_instance("vcl/treeview");
		$tree->start_tree(array (
			"type" => TREE_DHTML,
			"branch" => 1,
			"tree_id" => "workers_tree_".$arr["parent"],
			"var" => "parent",
		));
		
		$secs = $p->get_sections();
		$profs = $p->get_professions();
		$workers = $p->get_workers_grp_profession();
		foreach($secs->arr() as $section)
		{
//			if($section->has_sections() || $section->has_workers())
//			{

				$lid = $section->id()."_".$p->id();
				$lid_name = $section->name();
				if ($_GET["op"] == $lid)
				{
					$lid_name = "<b>".$lid_name."</b>";
				}

				$tree->add_item(0, array(
					"id" => $lid,
					"name" => $lid_name,
					"url" => aw_url_change_var("parent", $lid,$arr["set_retu"])
				));
				$tree->add_item($section->id(), array(
					"id" => $section->id().'_sub',
					"name" => " ",
				));
//			}
		}
		foreach($profs->arr() as $prof)
		{
			if($workers[$prof->id()])
			{
				$lid = $prof->id()."_".$p->id();
				$lid_name = $prof->name();
				if ($_GET["op"] == $lid)
				{
					$lid_name = "<b>".$lid_name."</b>";
				}
				$tree->add_item(0, array(
					"id" => $lid,
					"name" => $lid_name,
					'iconurl' =>' images/scl.gif',
					"url" => aw_url_change_var("parent", $lid,$arr["set_retu"])
				));
				$tree->add_item($prof->id()."_".$p->id(), array(
					"id" => $prof->id().'_sub',
					"name" => " ",
				));
			}
		}
		foreach($workers[0] as $worker_id)
		{
			if($this->can("view" , $worker_id))
			{
				$worker = obj($worker_id);
				$lid = $worker->id()."_".$p->id();
				$lid_name = $worker->name();
				if ($_GET["op"] == $lid)
				{
					$lid_name = "<b>".$lid_name."</b>";
				}
				$tree->add_item(0, array(
					"id" => $lid,
					"name" => $lid_name ,
					'iconurl' => icons::get_icon_url($worker->class_id()),
					"url" => aw_url_change_var("parent", $lid,$arr["set_retu"])
				));
			}
		}

		die($tree->finalize_tree());
	}

	private function _profession_leaf($arr)
	{
		extract($arr);
		if(!is_oid($parent))
		{
			$a = explode("_" , $parent);
			$parent = $a[0];
			$section = $a[1];
		}
		$p = obj($parent);
		$tree = get_instance("vcl/treeview");
		$tree->start_tree(array (
			"type" => TREE_DHTML,
			"branch" => 1,
			"tree_id" => "workers_tree_".$arr["parent"],
			"var" => "parent",
		));

		$workers = $p->get_workers_for_section($section);
		foreach($workers->arr() as $worker)
		{
			$lid = $worker->id()."_".$p->id();
			$lid_name = $worker->name();
			if ($_GET["op"] == $lid)
			{
				$lid_name = "<b>".$lid_name."</b>";
			}
			$tree->add_item(0, array(
				"id" => $lid,
				"name" => $lid_name,
				'iconurl' => icons::get_icon_url($worker->class_id()),
				"url" => aw_url_change_var("parent", $lid,$arr["set_retu"])
			));
		}

		die($tree->finalize_tree());
		$parents = array();
	}

	function _get_workers_tbl($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
//		$t->define_field(array(
//			"name" => "name",
//			"caption" => t("Nimi"),
//			"sortable" => 1,
//		));
		$t->define_field(array(
			"name" => "skill",
			"caption" => t("P&auml;devus"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "level",
			"caption" => t("Tase"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "hour_price",
			"caption" => t("P&auml;devuse kasutamise tunnihind"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "acquired",
			"caption" => t("Omandatud"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "lost",
			"caption" => t("kaotatud"),
			"sortable" => 1,
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
		$sol = $arr["obj_inst"]->get_all_skills();
		$parent =  $arr["request"]["parent"];
		if(!is_oid($parent))
		{
			$a = explode("_" , $parent);
			$parent = $a[0];
			$section = $a[1];
		}
		if($this->can("view",$parent))
		{
			$cat = obj($parent);
			$caption = html::get_change_url($parent , array("return_url" => get_ru()), $cat->name());
			if($this->can("view" , $section))
			{
				$sec_obj = obj($section);
				$caption = html::get_change_url($section , array("return_url" => get_ru()), $sec_obj->name()) . " > " . $caption;
			}
			$t->set_header($caption);
			if($cat->class_id() == CL_CRM_PERSON)
			{
				$skills = $cat->get_skills();
				foreach($skills->arr() as $skill)
				{
					$t->define_data(array(
						"name" => html::get_change_url($skill->id(),array("return_url" => get_ru()),$skill->name()),
						"oid" => $skill->id(),
						"skill" =>  html::select(array("name" => "skills[".$skill->id()."][skill]" , "value" => $skill->prop("skill"), "options" => $sol->names())),//html::get_change_url($skill->prop("skill"),array("return_url" => get_ru()),$skill->prop("skill.name")),
						"level" =>  html::select(array("name" => "skills[".$skill->id()."][level]" , "value" => $skill->prop("level"), "options" => array(0,1,2,3,4,5,6,7,8,9))),//html::get_change_url($skill->prop("skill"),array("return_url" => get_ru()),$skill->prop("skill.name")),
						"hour_price" => html::textbox(array("name" => "skills[".$skill->id()."][hour_price]" , "value" => $skill->prop("hour_price"), "size" => 4)),
						"acquired" => html::date_select(array("name" => "skills[".$skill->id()."][skill_acquired]" , "value" => $skill->prop("skill_acquired"))),//$skill->prop("skill_acquired")?date("d.m.Y", $skill->prop("skill_acquired")):"",
						"lost" => $skill->prop("skill_lost")?date("d.m.Y", $skill->prop("skill_lost")):"",
					));
				}
			}
			
		}
	}


	function _get_skills_tbl($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "short",
			"caption" => t("L&uuml;hend"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "hours",
			"caption" => t("Tunde n&auml;dalas, mis on p&auml;devuse hoidmiseks vajalikud"),
			"sortable" => 1,
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
/*		$t->define_field(array(
			"name" => "comment",
			"caption" => t("Kommentaar"),
			"sortable" => 1,
		));
*/
		$tf = $arr["request"]["skill"];
		if(!$tf)
		{
			$ol = $arr["obj_inst"]->get_root_skills();
		}
		else
		{
			$ol = $arr["obj_inst"]->get_all_skills($tf);
		}
		foreach($ol->arr() as $o)
		{
			$t->define_data(array(
				"name" => html::get_change_url($o->id(),array("returl_url" => get_ru()),$o->name()). html::hidden(array("name" => "skill[".$o->id()."][id]" , "value" => $o->id())),
				"oid" => $o->id(),
				"short" => html::textbox(array("name" => "skill[".$o->id()."][short]" , "value" => $o->prop("short_name"))),
				"hours" => html::textbox(array("name" => "skill[".$o->id()."][hours]" , "value" => $o->prop("hrs_per_week_to_keep"))),
			));
		}
	}

	function _set_skills_tbl($arr)
	{
		foreach($arr["request"]["skill"] as $id => $data)
		{
			if(!$this->can("view" , $id))
			{
				continue;
			}
			$o = obj($id);
			$o->set_prop("short_name" ,$data["short"]);
			$o->set_prop("hrs_per_week_to_keep" ,$data["hours"]);
			$o->save();
		}
		$p = "<script name= javascript>location.href='".$arr["request"]["post_ru"]."';</script>";
		die($p);
	}

	
	function _set_workers_tbl($arr)
	{
		foreach($arr["request"]["skills"] as $id => $data)
		{
			if(!$this->can("view" , $id))
			{
				continue;
			}
			$o = obj($id);
			$o->set_prop("skill" ,$data["skill"]);
			$o->set_prop("level" ,$data["level"]);
			$o->set_prop("skill_acquired" ,date_edit::get_timestamp($data["skill_acquired"]));
			$o->set_prop("hour_price" ,$data["hour_price"]);
			$o->save();
		}
		$p = "<script name= javascript>location.href='".$arr["request"]["post_ru"]."';</script>";
		die($p);
	}
}

?>
