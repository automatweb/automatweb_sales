<?php
/*
@classinfo  maintainer=robert
*/
class bt_req_impl extends core
{
	function bt_req_impl()
	{
		$this->init();
	}

	function _get_reqs_tb($arr)
	{
		$pt = $arr["request"]["tf"] ? $arr["request"]["tf"] : $arr["obj_inst"]->id();
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_menu_button(array(
			"name" => "add_req",
			"tooltip" => t("Lisa"),
			"img" => "new.gif",
		));
		$tb->add_menu_item(array(
			"parent" => "add_req",
			"text" => t("Kategooria"),
			"link" => html::get_new_url(CL_REQUIREMENT_CATEGORY, $pt, array(
				"return_url" => get_ru(),
			))
		));
		$tb->add_menu_item(array(
			"parent" => "add_req",
			"text" => t("N&otilde;ue"),
			"link" => html::get_new_url(CL_PROCUREMENT_REQUIREMENT, $pt, array(
				"return_url" => get_ru(),
			))
		));
		$tb->add_button(array(
			"name" => "delete",
			"tooltip" => t("Kustuta"),
			"img" => "delete.gif",
			"action" => "delete",
			"confirm" => t("Oled kindel, et soovid n&otilde;udeid kustutada?"),
		));
		$tb->add_separator();
		$tb->add_button(array(
			"name" => "cut",
			"tooltip" => t("L&otilde;ika"),
			"img" => "cut.gif",
			"action" => "cut_b",
		));
		if (is_array($_SESSION["bt"]["cut_bugs"]) && count($_SESSION["bt"]["cut_bugs"]))
		{
			$tb->add_button(array(
				"name" => "paste",
				"tooltip" => t("Kleebi"),
				"img	" => "paste.gif",
				"action" => "paste_b",
			));
		}
		$tb->add_separator();
		$tb->add_button(array(
			"name" => "export",
			"tooltip" => t("Ekspordi"),
			"img" => "export.gif",
			"action" => "export_req",
		));
	}
	
	function _get_reqs_tree($arr)
	{
		classload("core/icons");
		$arr["prop"]["vcl_inst"] = treeview::tree_from_objects(array(
			"tree_opts" => array(
				"type" => TREE_DHTML, 
				"persist_state" => true,
				"tree_id" => "bt_reqs",
			),
			"root_item" => $arr["obj_inst"],
			"ot" => new object_tree(array(
				"parent" => $arr["obj_inst"]->id(),
				"lang_id" => array(),
				"site_id" => array(),
				"class_id" => CL_REQUIREMENT_CATEGORY
			)),
			"var" => "tf",
			"icon" => icons::get_icon_url(CL_MENU)
		));
	}

	function _init_reqs_table(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "createdby",
			"caption" => t("Looja"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "created",
			"caption" => t("Loodud"),
			"align" => "center",
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y H:i",
		));
		
		$t->define_chooser(array(
			"field" => "id",
			"name" => "sel",
		));
	}

	function _get_reqs_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		//$this->_init_reqs_table($t);

		$pt = $arr["request"]["tf"] ? $arr["request"]["tf"] : $arr["obj_inst"]->id();
		$pto = obj($pt);
		$ol = new object_list(array(
			"class_id" => CL_PROCUREMENT_REQUIREMENT,
			"lang_id" => array(),
			"site_id" => array(),
			"parent" => $pt
		));
		$t->table_from_ol($ol, array("name", "created", "pri", "req_co", "req_p", "project", "process", "planned_time"), CL_PROCUREMENT_REQUIREMENT);
		$t->set_caption(sprintf(t("N&otilde;uded kategoorias %s"), $pto->name()));
	}

	function _get_reqs_p_tree($arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_PROCUREMENT_REQUIREMENT,
			"lang_id" => array(),
			"site_id" => array(),
		));
		// get all projects from those
		$p2req = array();
		$ps2req = array();
		$pp2req = array();
		foreach($ol->arr() as $r)
		{
			if ($this->can("view", $r->prop("project")))
			{
				$p2req[(int)$r->prop("project")] ++;
				$ps2req[(int)$r->prop("project")][$r->prop("state")]++;
				$pp2req[(int)$r->prop("project")][$r->prop("pri")]++;
			}
		}
		$p2req[(int)null]++;
		$p2req[(int)null]--;
		$t =& $arr["prop"]["vcl_inst"];
		$i = get_instance(CL_PROCUREMENT_REQUIREMENT);
		foreach($p2req as $proj => $cnt)
		{
			/*if (!is_oid($proj))
			{
				continue;
			}*/
			$po = obj($proj);
			$nm = (is_oid($proj) ? $po->name() : t("Muud t&ouml;&ouml;d"))." ($cnt)";
			if ($arr["request"]["proj"] == $proj && !$arr["request"]["state"] && !$arr["request"]["pri"] )
			{
				$nm = "<b>".$nm."</b>";
			}
			$t->add_item(0, array(
				"id" => "p_".$proj,
				"parent" => 0,
				"name" => $nm,
				"url" => aw_url_change_var(array(
					"proj" => $proj,
					"state" => null,
					"pri" => null
				))
			));

			$t->add_item("p_".$proj, array(
				"id" => $proj."_states",
				"parent" => $proj,
				"name" => t("Staatused"),
				"url" => aw_url_change_var(array(
					"proj" => $proj,
					"state" => null,
					"pri" => null
				))
			));


			foreach($i->get_status_list() as $s_id => $s_nm)
			{
				if ($arr["request"]["proj"] == $proj && $arr["request"]["state"] === "s_".$s_id)
				{
					$s_nm = "<b>".$s_nm."</b>";
				}
				$t->add_item($proj."_states", array(
					"id" => $proj."_state_".$s_id,
					"parent" => $proj."_states",
					"name" => $s_nm." (".(int)$ps2req[$proj][$s_id].")",
					"url" => aw_url_change_var(array(
						"proj" => $proj,
						"state" => "s_".$s_id,
						"pri" => null
					))
				));
			}


			$t->add_item("p_".$proj, array(
				"id" => $proj."_pris",
				"parent" => "p_".$proj,
				"name" => t("Prioriteedid"),
				"url" => aw_url_change_var(array(
					"proj" => $proj,
					"state" => null,
					"pri" => null
				))
			));


			foreach($i->get_priority_list(obj()) as $s_id => $s_nm)
			{
				if ($arr["request"]["proj"] == $proj && $arr["request"]["pri"] == $s_id)
				{
					$s_nm = "<b>".$s_nm."</b>";
				}
				$t->add_item($proj."_pris", array(
					"id" => $proj."_pri_".$s_id,
					"parent" => $proj."_pris",
					"name" => $s_nm." (".(int)$pp2req[$proj][$s_id].")",
					"url" => aw_url_change_var(array(
						"proj" => $proj,
						"state" => null,
						"pri" => $s_id
					))
				));
			}
		}
	}

	function _get_reqs_p_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		//$this->_init_reqs_table($t);

		$f = array(
			"class_id" => CL_PROCUREMENT_REQUIREMENT,
			"lang_id" => array(),
			"site_id" => array(),
		);
		$po = obj();
		if (!$arr["request"]["proj"])
		{
			$f["project"] = new obj_predicate_compare(OBJ_COMP_LESS, 1);
		}
		else
		{
			$f["project"] = $arr["request"]["proj"];
			$po = obj($f["project"]);
		}

		if ($arr["request"]["state"])
		{
			$f["state"] = str_replace("s_", "", $arr["request"]["state"]);
		}

		if ($arr["request"]["pri"])
		{
			$f["pri"] = $arr["request"]["pri"];
		}

		$ol = new object_list($f);
		$t->table_from_ol($ol, array("name", "created", "pri", "req_co", "req_p", "project", "process", "planned_time"), CL_PROCUREMENT_REQUIREMENT);
		$t->set_caption(sprintf(t("N&otilde;uded projektis %s"), $po->name()));
	}

	function _get_reqs_c_tree($arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_PROCUREMENT_REQUIREMENT,
			"lang_id" => array(),
			"site_id" => array(),
		));
		// get all cos from those
		$c2req = array();
		$c2empl = array();
		foreach($ol->arr() as $r)
		{
			$c2req[(int)$r->prop("req_co")] ++;
			$c2empl[(int)$r->prop("req_co")][$r->prop("req_p")]++;
		}
		$t =& $arr["prop"]["vcl_inst"];
		$i = get_instance(CL_PROCUREMENT_REQUIREMENT);
		foreach($c2req as $co => $cnt)
		{
			if (!is_oid($co))
			{
				continue;
			}
			$po = obj($co);
			$nm = $po->name()." ($cnt)";
			if ($arr["request"]["co"] == $co && !$arr["request"]["empl"])
			{
				$nm = "<b>".$nm."</b>";
			}
			$t->add_item(0, array(
				"id" => "p_".$co,
				"parent" => 0,
				"name" => $nm,
				"url" => aw_url_change_var(array(
					"co" => $co,
					"empl" => null,
				))
			));

			foreach(safe_array($c2empl[$co]) as $p_id => $p_cnt)
			{
				$p_obj = obj($p_id);
				$nm = $p_obj->name()." ($p_cnt)";
				if ($arr["request"]["empl"] == $p_id)
				{
					$nm = "<b>".$nm."</b>";
				}
				$t->add_item("p_".$co, array(
					"id" => "c_".$p_id,
					"parent" => "p_".$co,
					"name" => $nm,
					"url" => aw_url_change_var(array(
						"co" => $co,
						"empl" => $p_id,
					))
				));
			}
		}
	}

	function _get_reqs_c_table($arr)
	{
		if (!$arr["request"]["empl"] && !$arr["request"]["co"])
		{
			return;
		}
		$t =& $arr["prop"]["vcl_inst"];
		//$this->_init_reqs_table($t);

		$f = array(
			"class_id" => CL_PROCUREMENT_REQUIREMENT,
			"lang_id" => array(),
			"site_id" => array(),
		);
		if ($arr["request"]["empl"])
		{
			$f["req_p"] = $arr["request"]["empl"];
		}

		$coo = obj();
		if ($arr["request"]["co"])
		{
			$f["req_co"] = $arr["request"]["co"];
			$coo = obj($f["req_co"]);
		}

		$ol = new object_list($f);
		$t->table_from_ol($ol, array("name", "created", "pri", "req_co", "req_p", "project", "process", "planned_time"), CL_PROCUREMENT_REQUIREMENT);
		$t->set_caption(sprintf(t("N&otilde;uded organisatsioonile %s"), $coo->name()));
	}
}
