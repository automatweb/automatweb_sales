<?php
/*
@classinfo maintainer=markop
*/
class project_files_impl extends class_base
{
	function project_files_impl()
	{
		$this->init();
		$this->types = array(
			CL_MENU => t("Kataloog"),
			CL_FILE => t("Fail"),
			CL_CRM_MEMO => t("Memo"),
			CL_CRM_DOCUMENT => t("CRM Dokument"),
			CL_CRM_DEAL => t("Leping"),
			CL_CRM_OFFER => t("Pakkumine"),
			CL_PROJECT_STRAT_GOAL_EVAL_WS => t("Eesm&auml;rkide hindamise t&ouml;&ouml;laud"),
			CL_PROJECT_RISK_EVAL_WS => t("Riskide hindamise t&ouml;&ouml;laud"),
			CL_PROJECT_ANALYSIS_WS => t("Anal&uuml;&uuml;si t&ouml;&ouml;laud"),
			CL_DEVELOPMENT_ORDER => t("Arendustellimus")
		);
	}

	function _get_files_tb($arr)
	{
		$pt = $this->_get_files_pt($arr);

		$tb =& $arr["prop"]["vcl_inst"];

		$types = array(
			CL_MENU => t("Kataloog"),
			CL_FILE => t("Fail"),
			CL_CRM_MEMO => t("Memo"),
			CL_CRM_DOCUMENT => t("CRM Dokument"),
			CL_CRM_DEAL => t("Leping"),
			CL_CRM_OFFER => t("Pakkumine"),
			CL_PROJECT_STRAT_GOAL_EVAL_WS => t("Eesm&auml;rkide hindamise t&ouml;&ouml;laud"),
			CL_PROJECT_RISK_EVAL_WS => t("Riskide hindamise t&ouml;&ouml;laud"),
			CL_PROJECT_ANALYSIS_WS => t("Anal&uuml;&uuml;si t&ouml;&ouml;laud"),
			CL_DEVELOPMENT_ORDER => t("Arendustellimus")
		);

		$tb->add_menu_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Uus"),
		));
		if(!is_oid($pt) || !$this->can("view" , $pt))
		{
			$ff = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_FILES_FLD");
			$pt = $ff->id();
		}
		else
		{
			$parent_obj = obj($pt);
			if($parent_obj->class_id() != CL_MENU)
			{
				$ff = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_FILES_FLD");
				$pt = $ff->id();
			}
		}
		foreach($types as $type => $desc)
		{
			$tb->add_menu_item(array(
				"parent" => "new",
				"text" => $desc,
				"link" => html::get_new_url($type, $pt, array("project" => $arr["obj_inst"]->id() , "return_url" => get_ru())),
			));
		}
		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"action" => "del_goals",
			"tooltip" => t("Kustuta"),
		));

		$tb->add_separator();
		$tb->add_button(array(
			"name" => "cut",
			"img" => "cut.gif",
			"action" => "cut_files",
			"tooltip" => t("L&otilde;ika"),
		));

		if (is_array($_SESSION["proj_cut_files"]) && count($_SESSION["proj_cut_files"]) && $arr["request"]["tf"] != "unsorted")
		{
			$tb->add_button(array(
				"name" => "paste",
				"img" => "paste.gif",
				"action" => "paste_files",
				"tooltip" => t("Kleebi"),
			));
		}
	}

	function _get_files_pt($arr)
	{
		if ($this->can("view" , $arr["request"]["tf"]) && !array_key_exists($arr["request"]["tf"] , $this->types))
		{
			$o = obj($arr["request"]["tf"]);
			if($o->class_id() == CL_MENU)
			{
				return $arr["request"]["tf"];
			}
		}
		$ff = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_FILES_FLD");
		if (!$ff)
		{
			$ff = obj();
			$ff->set_class_id(CL_MENU);
			$ff->set_parent($arr["obj_inst"]->id());
			$ff->set_name(sprintf(t("%s failid"), $arr["obj_inst"]->name()));
			$ff->save();
			$arr["obj_inst"]->connect(array(
				"to" => $ff->id(),
				"type" => "RELTYPE_FILES_FLD"
			));
		}
		return $ff->id();
	}

	
	function _get_parent_folders($obj)
	{
		$parents = array();
		if($obj->class_id() == CL_PROJECT)
		{
			$obj = $obj->get_first_obj_by_reltype("RELTYPE_FILES_FLD");
//			$parents[] = $obj->id();
		}
		$ol = new object_list(array(
			"lang_id" => array(),
			"parent" => $obj->id(),
			"class_id" => CL_MENU,
		));
		$parents[] = $obj->id();
		foreach($ol->arr() as $folder)
		{
			$parents = array_merge($parents,$this->_get_parent_folders($folder));
		}
		return $parents;
	}

	function _get_files_tree($arr)
	{
		$otf = $arr["request"]["tf"];
		unset($arr["request"]["tf"]);
		$pt = $this->_get_files_pt($arr);
		classload("core/icons");
		$parent_folders = $this->_get_parent_folders($arr["obj_inst"]);
		$parent_folders[] = $arr["obj_inst"]->id();
		$arr["prop"]["vcl_inst"] = treeview::tree_from_objects(array(
			"tree_opts" => array(
				"type" => TREE_DHTML, 
				"persist_state" => true,
				"tree_id" => "crm_proj_t",
			),
			"root_item" => obj($pt),
			"target_url" => aw_url_change_var("tf", null),
			"ot" => new object_tree(array(
				"class_id" => array(CL_MENU),
				"parent" => $pt,
				"sort_by" => "objects.jrk"
			)),
			"var" => "tf",
			"icon" => icons::get_icon_url(CL_MENU)
		));

		$nm = t("Sorteerimata") ;
		if ($otf == "unsorted")
		{
			$nm = "<b>".$nm."</b>";
		}
		$arr["prop"]["vcl_inst"]->add_item(0, array(
			"id" => "unsorted",
			"name" => $nm,
			"url" => aw_url_change_var("tf", "unsorted"),
		));
		
		$nm = t("T&uuml;&uuml;bi j&auml;rgi");
		if ($otf == "by_type")
		{
			$nm = "<b>".$nm."</b>";
		}
		$arr["prop"]["vcl_inst"]->add_item(0, array(
			"id" => "by_type",
			"name" => $nm,
			"url" => aw_url_change_var("tf", "by_type"),
		));
		
		$types = array(
			CL_FILE => t("Fail"),
			CL_CRM_MEMO => t("Memo"),
			CL_CRM_DOCUMENT => t("CRM Dokument"),
			CL_CRM_DEAL => t("Leping"),
			CL_CRM_OFFER => t("Pakkumine"),
			CL_PROJECT_STRAT_GOAL_EVAL_WS => t("Eesm&auml;rkide hindamise t&ouml;&ouml;laud"),
			CL_PROJECT_RISK_EVAL_WS => t("Riskide hindamise t&ouml;&ouml;laud"),
			CL_PROJECT_ANALYSIS_WS => t("Anal&uuml;&uuml;si t&ouml;&ouml;laud"),
			CL_DEVELOPMENT_ORDER => t("Arendustellimus")
		);
		$pr = $arr["obj_inst"]->id();
		foreach($types as $clid => $capt)
		{
			$filter = array(
				"class_id" => $clid,
				"lang_id" => array(),
			);
			if(in_array($clid ,array(CL_CRM_DOCUMENT,CL_CRM_DEAL,CL_CRM_MEMO,CL_CRM_OFFER,CL_DEVELOPMENT_ORDER)))
			{
				$filter["project"] = $pr;
			}
			if(in_array($clid ,array(CL_FILE,CL_PROJECT_STRAT_GOAL_EVAL_WS,CL_PROJECT_RISK_EVAL_WS,CL_PROJECT_ANALYSIS_WS)))
			{
				$filter["parent"] = $parent_folders;
			}
			$ol = new object_list($filter);
			$nm = $capt." (".sizeof($ol->ids()).")";
			if ($otf == $clid)
			{
				$nm = "<b>".$nm."</b>";
			}
			$arr["prop"]["vcl_inst"]->add_item("by_type", array(
				"id" => $clid,
				"name" => $nm,
				"url" => aw_url_change_var("tf", $clid),
			));
		}
	}

	function _init_files_tbl(&$t)
	{
		$t->define_field(array(
			"caption" => t(""),
			"name" => "icon",
			"align" => "center",
			"sortable" => 0,
			"width" => 1
		));

		$t->define_field(array(
			"caption" => t("Nimi"),
			"name" => "name",
			"align" => "center",
			"sortable" => 1
		));

		$t->define_field(array(
			"caption" => t("Looja"),
			"name" => "createdby",
			"align" => "center",
			"sortable" => 1
		));

		$t->define_field(array(
			"caption" => t("Loodud"),
			"name" => "created",
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
			"type" => "time",
			"format" => "d.m.Y H:i"
		));

		$t->define_field(array(
			"caption" => t("Muudetud"),
			"name" => "modified",
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
			"type" => "time",
			"format" => "d.m.Y H:i"
		));

		$t->define_field(array(
			"caption" => t(""),
			"name" => "pop",
			"align" => "center"
		));

		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));
	}

	function _get_files_table($arr)
	{
		if($arr["request"])
		{
			$pt = $this->_get_files_pt($arr);
		}	
		
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_files_tbl($t);
		$pr = NULL;

		//otsingust
		if(!$arr["request"]["tf"] && ($arr["request"]["files_find_name"] || $arr["request"]["files_find_type"] || $arr["request"]["files_find_comment"]))
		{
			$filter = array(
				"class_id" => array(CL_FILE,CL_CRM_DOCUMENT, CL_CRM_DEAL, CL_CRM_MEMO, CL_CRM_OFFER , CL_PROJECT_STRAT_GOAL_EVAL_WS ,CL_PROJECT_RISK_EVAL_WS ,CL_PROJECT_ANALYSIS_WS,CL_DEVELOPMENT_ORDER),
				"lang_id" => array(),
				"name" => "%".$arr["request"]["files_find_name"]."%",
				
			);
			if($arr["request"]["files_find_comment"])
			{
				$filter["comment"] = "%".$arr["request"]["files_find_comment"]."%";
			}
			if($arr["request"]["files_find_type"])
			{
				$filter["class_id"] = $arr["request"]["files_find_type"];
			}
		}
		//puust ja igaltpoolt mujalt
		else
		{
			if ($arr["request"]["tf"] == "unsorted")
			{
	//			$ot = new object_tree(array("class_id" => CL_MENU, "parent" => $pt, "lang_id" => array(), "site_id" => array()));
	//			$pt = new obj_predicate_not(array($pt, $pt) + $ot->ids());
				$pr = $arr["obj_inst"]->id();
				$pt = new obj_predicate_not($this->_get_parent_folders($arr["obj_inst"]));
			
			}
			$filter = array(
				$filters,
				"parent" => $pt,
	//			"project" => $pr,
				"class_id" => array(CL_FILE,CL_CRM_DOCUMENT, CL_CRM_DEAL, CL_CRM_MEMO, CL_CRM_OFFER , CL_PROJECT_STRAT_GOAL_EVAL_WS ,CL_PROJECT_RISK_EVAL_WS ,CL_PROJECT_ANALYSIS_WS,CL_DEVELOPMENT_ORDER),
				"lang_id" => array(),
			);
			$filter[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
	//				"CL_FILE.project" => $pr, // enne oli r36msalt ka fail sees, kuid ei ole seni faili kuidagi projektiga seostatud.
					"CL_CRM_DOCUMENT.project" => $pr,
					"CL_CRM_DEAL.project" => $pr,
					"CL_CRM_MEMO.project" => $pr,
					"CL_CRM_OFFER.project" => $pr,
				))
			);
	
			if(in_array($arr["request"]["tf"], array(CL_FILE,CL_CRM_MEMO,CL_CRM_DOCUMENT,CL_CRM_DEAL,CL_CRM_OFFER,CL_PROJECT_STRAT_GOAL_EVAL_WS,CL_PROJECT_RISK_EVAL_WS,CL_PROJECT_ANALYSIS_WS,CL_DEVELOPMENT_ORDER)))
			{
				$pr = $arr["obj_inst"]->id();
				$filter = array(
					"class_id" => $arr["request"]["tf"],
					"lang_id" => array(),
				);
				if(in_array($arr["request"]["tf"] ,array(CL_CRM_DOCUMENT,CL_CRM_DEAL,CL_CRM_MEMO,CL_CRM_OFFER,CL_DEVELOPMENT_ORDER)))
				{
					$filter["project"] = $pr;
				}
				if(in_array($arr["request"]["tf"] ,array(CL_FILE,CL_PROJECT_STRAT_GOAL_EVAL_WS,CL_PROJECT_RISK_EVAL_WS,CL_PROJECT_ANALYSIS_WS)))
				{
					$parent_folders = $this->_get_parent_folders($arr["obj_inst"]);
					$filter["parent"] = $parent_folders;
				}
			}
		}

		$ol = new object_list($filter);
		classload("core/icons");
		$clss = aw_ini_get("classes");
		new file();
		foreach($ol->arr() as $o)
		{
			$pm = new popup_menu();
			$pm->begin_menu("sf".$o->id());
			
			if ($o->class_id() == CL_FILE)
			{
				$pm->add_item(array(
					"text" => $o->name(),
					"link" => file::get_url($o->id(), $o->name())
				));
			}
			else
			{
				foreach($o->connections_from(array("type" => "RELTYPE_FILE")) as $c)
				{
					$pm->add_item(array(
						"text" => $c->prop("to.name"),
						"link" => file::get_url($c->prop("to"), $c->prop("to.name"))
					));
				}
			}
			
			$t->define_data(array(
				"icon" => $pm->get_menu(array(
					"icon" => icons::get_icon_url($o)
				)),
				"name" => html::obj_change_url($o),
				"class_id" => $clss[$o->class_id()]["name"],
				"createdby" => $o->createdby(),
				"created" => $o->created(),
				"modifiedby" => $o->modifiedby(),
				"modified" => $o->modified(),
				"oid" => $o->id()
			));
		}

		$t->set_default_sortby("created");
		$t->set_default_sorder("desc");
	}
}

?>
