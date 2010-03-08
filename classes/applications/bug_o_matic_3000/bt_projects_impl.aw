<?php
/*
@classinfo  maintainer=robert
*/

class bt_projects_impl extends core
{
	function bt_projects_impl()
	{
		$this->init();
	}

	function _get_proj_tree($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];

		$gbf = $this->mk_my_orb("proj_tree_level", array(
			"set_retu" => get_ru(),
			"id" => $arr["obj_inst"]->id(),
			"filt_value" => $arr["request"]["filt_value"],
			"parent" => " ",
		), CL_BUG_TRACKER);
		$t->start_tree(array(
			"type" => TREE_DHTML,
			"tree_id" => 'bt_proj_tree',
			"persist_state" => true,
			"get_branch_func" => $gbf,
			"has_root" => true,
			"root_icon" => icons::get_icon_url(CL_MENU),
			"root_url" => "#",
			"root_name" => t("Projektid"),
		));

		$conn = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_OWNER",
		));
		if(count($conn))
		{
			$c = reset($conn);
			$owner = $c->to();
		}
		if($owner)
		{
			$add1 = $add2 = "";
			if($arr["request"]["filt_type"] == "category" && !$arr["request"]["filt_value"])
			{
				$add1 = "<strong>";
				$add2 = "</strong>";
			}
			$t->add_item(0, array(
				"id" => 1,
				"name" => $add1.t("Kategooriad").$add2,
				"url" => aw_url_change_var(array(
					"filt_type" => "category",
					"filt_value" => null,
				), false, get_ru()),
			));
			$t->add_item(0, array(
				"id" => 2,
				"name" => t("Kliendid"),
				"url" => "#",
			));
			$t->add_item(0, array(
				"id" => 3,
				"name" => t("Staatused"),
				"url" => "#",
			));
			$t->add_item(0, array(
				"id" => 4,
				"name" => t("Projektijuhid"),
				"url" => "#",
			));
			$t->add_item(0, array(
				"id" => 5,
				"name" => t("Perioodid"),
				"url" => "#",
			));
			$this->__insert_proj_categories($t, $owner->id(), 1, $arr);
			$this->__insert_cust_categories($t, 2, $arr);
			$this->__insert_states($t, 3, $arr);
			$this->__insert_sections($t, 4, $arr);
			$this->__insert_dates($t, 5);
		}
	}

	function proj_tree_level($arr)
	{
		$t = get_instance("vcl/treeview");
		$t->start_tree(array(
			"type" => TREE_DHTML,
			"branch" => 1,
			"tree_id" => "prod_tree",
		));

		switch($arr["parent"])
		{
			case 1:
				$this->__insert_proj_categories($t, $arr["parent"], 0, $arr);
				break;

			case 2:
				$this->__insert_cust_categories($t, 0, $arr);
				break;

			case 3:
				$this->__insert_states($t, 0, $arr);
				break;

			case 4:
				$this->__insert_sections($t, 0, $arr);
				break;

			case 5:
				$this->__insert_dates($t, 0);
				break;

			case 6:
				$this->__insert_years($t, 0, "start");
				break;
			
			case 7:
				$this->__insert_years($t, 0, "end");
				break;

			case 8:
				$this->__insert_years($t, 0, "deadline");
				break;

			default:
				if($this->can("view", $arr["parent"]))
				{
					$o = obj($arr["parent"]);
					switch($o->class_id())
					{
						case CL_CRM_CATEGORY:
							$this->__insert_category_subs($t, $o, 0, $arr);
							break;

						case CL_CRM_SECTION:
							$this->__insert_section_subs($t, $o, 0, $arr);
							break;

						case CL_PROJECT_CATEGORY:
							$this->__insert_proj_categories($t, $arr["parent"], 0, $arr);
							break;
					}
				}
				elseif(strpos($arr["parent"], "y_") !== false)
				{
					$tmp = explode("_", $arr["parent"]);
					$arr["year"] = $tmp[2];
					$this->__insert_months($t, 0, $tmp[1], $arr);
				} 
		}

		die($t->finalize_tree());
	}

	private function __insert_proj_categories(&$t, $oid, $parent, $arr)
	{
		if($oid == 1)
		{
			$o = get_instance(CL_BUG_TRACKER)->_get_owner($arr);
			if($o)
			{
				$oid = $o->id();
			}
		}
		if($this->can("view", $oid))
		{
			$ol = new object_list(array(
				"class_id" => CL_PROJECT_CATEGORY,
				"site_id" => array(),
				"lang_id" => array(),
				"parent" => $oid,
			));
			foreach($ol->arr() as $o)
			{
				$t->add_item($parent, array(
					"id" => $o->id(),
					"name" => $this->__parse_name($o->name(), $o->id(), $arr),
					"url" => aw_url_change_var(array(
						"filt_type" => "category",
						"filt_value" => $o->id(),
					), false, $arr["set_retu"]),
				));
				if($parent == 0)
				{
					$this->__insert_proj_categories($t, $o->id(), $o->id(), $arr);
				}
			}
		}
	}

	function __insert_cust_categories(&$t, $parent, $arr)
	{
		$owner = get_instance(CL_BUG_TRACKER)->_get_owner($arr);
		if($owner)
		{
			$conn = $owner->connections_from(array(
				"type" => "RELTYPE_CATEGORY"
			));
			foreach($conn as $c)
			{
				$t->add_item($parent, array(
					"id" => $c->prop("to"),
					"name" => $c->prop("to.name"),
					"url" => "#",
				));
				if($parent == 0)
				{
					$conn = $c->to()->connections_from(array(
						"type" => "RELTYPE_CATEGORY"
					));
					if(count($conn))
					{
						$t->add_item($c->prop("to"), array());
					}
				}
			}
		}
	}

	function __insert_category_subs(&$t, $o, $parent, $arr)
	{
		$conn = $o->connections_from(array(
			"type" => "RELTYPE_CATEGORY"
		));
		foreach($conn as $c)
		{
			$t->add_item($parent, array(
				"id" => $c->prop("to"),
				"name" => $c->prop("to.name"),
				"url" => "#",
			));
			$this->__insert_category_subs($t, $c->to(), $c->prop("to"), $arr);
		}
		$conn = $o->connections_from(array(
			"type" => "RELTYPE_CUSTOMER",
		));
		foreach($conn as $c)
		{
			$t->add_item($parent, array(
				"id" => $c->prop("to"),
				"name" => $this->__parse_name($c->prop("to.name"), $c->prop("to"), $arr),
				"url" => aw_url_change_var(array(
					"filt_type" => "customer",
					"filt_value" => $c->prop("to"),
				), false, $arr["set_retu"]),
				"iconurl" => icons::get_icon_url(CL_CRM_COMPANY),
			));
		}
	}

	private function __insert_states(&$t, $parent, $arr)
	{
		$i = get_instance(CL_PROJECT);
		foreach($i->states as $id => $state)
		{
			$t->add_item($parent, array(
				"id" => "st".$id,
				"name" => $this->__parse_name($state, $id, $arr),
				"url" => aw_url_change_var(array(
					"filt_type" => "state",
					"filt_value" => $id,
				), false, $arr["set_retu"]),
			));
		}
	}

	function __insert_sections(&$t, $parent, $arr)
	{
		$owner = get_instance(CL_BUG_TRACKER)->_get_owner($arr);
		$odl = new object_data_list(
			array(
				"lang_id" => array(),
				"site_id" => array(),
				"class_id" => CL_PROJECT,
				"proj_mgr" => "%",
			),
			array(
				CL_PROJECT => array(new obj_sql_func(OBJ_SQL_UNIQUE, "proj_mgr", "proj_mgr"))
			)
		);
		$ppl = array();
		foreach($odl->arr() as $o)
		{
			$ppl[] = $o["proj_mgr"];
		}
		$ol = new object_list();
		if(count($ppl))
		{
			$ol = new object_list(array(
				"class_id" => CL_CRM_SECTION,
				"CL_CRM_SECTION.RELTYPE_SECTION(CL_CRM_PERSON_WORK_RELATION).RELTYPE_CURRENT_JOB(CL_CRM_PERSON)" => $ppl,
				"CL_CRM_SECTION.RELTYPE_SECTION(CL_CRM_PERSON_WORK_RELATION).RELTYPE_ORG" => $owner->id(),
				"site_id" => array(),
				"lang_id" => array(),
			));
		}
		foreach($ol->arr() as $o)
		{
			$t->add_item($parent, array(
				"id" => $o->id(),
				"name" => $o->name(),
				"url" => "#",
			));
			if($parent == 0)
			{
				$t->add_item($o->id(), array());
			}
		}
	}

	function __insert_section_subs(&$t, $obj, $parent, $arr)
	{
		$owner = get_instance(CL_BUG_TRACKER)->_get_owner($arr);
		$ol = new object_list(array(
			"class_id" => CL_PROJECT,
			"proj_mgr.RELTYPE_CURRENT_JOB.RELTYPE_SECTION" => $obj->id(),
		));
		$set_ppl = array();
		foreach($ol->arr() as $o)
		{
			$p = $o->prop("proj_mgr");
			if($set_ppl[$p])
			{
				continue;
			}
			$set_ppl[$p] = $p;
			$po = obj($p);
			$t->add_item($parent, array(
				"id" => $po->id(),
				"name" => $arr["inst_id"] ? $po->name() : $this->__parse_name($po->name(), $po->id(), $arr),
				"iconurl" => icons::get_icon_url(CL_CRM_PERSON),
				"url" => $arr["inst_id"] ? "#" : aw_url_change_var(array(
					"filt_type" => "person",
					"filt_value" => $po->id(),
				), false, $arr["set_retu"]),
			));
			if($arr["inst_id"])
			{
				$this->__insert_person_projects($t, $po, $po->id(), $arr);
			}
		}
	}

	function __insert_person_projects($t, $p, $parent, $arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_PROJECT,
			"site_id" => array(),
			"lang_id" => array(),
			"proj_mgr" => $p->id(),
		));
		foreach($ol->arr() as $oid => $o)
		{
			$t->add_item($parent, array(
				"id" => $oid,
				"name" => $this->__parse_name($o->name(), $oid, $arr),
				"iconurl" => icons::get_icon_url(CL_PROJECT),
				"url" => aw_url_change_var(array(
					"filt_type" => "project",
					"filt_value" => $o->id(),
				), false, $arr["set_retu"]),
			));
		}
	}

	private function __insert_dates(&$t, $parent)
	{
		$t->add_item($parent, array(
			"id" => 6,
			"name" => t("Algus"),
			"url" => "#",
		));
		$t->add_item($parent, array(
			"id" => 7,
			"name" => t("L&otilde;pp"),
			"url" => "#",
		));
		$t->add_item($parent, array(
			"id" => 8,
			"name" => t("T&auml;htaeg"),
			"url" => "#",
		));
		if($parent == 0)
		{
			$this->__insert_years($t, 6, "start");
			$this->__insert_years($t, 7, "end");
			$this->__insert_years($t, 8, "deadline");
		}
	}

	private function __insert_years(&$t, $parent, $prop)
	{
		$param = array(
			"class_id" => CL_PROJECT,
			"site_id" => array(),
			"lang_id" => array(),
			"code" => "%",
			"sort_by" => "aw_projects.aw_".$prop." desc",
		);
		$ol = new object_list($param);
		$set_yrs = array();
		foreach($ol->arr() as $o)
		{
			$i = date("Y", $o->prop($prop));
			if($set_yrs[$i])
			{
				continue;
			}
			$t->add_item($parent, array(
				"id" => "y_".$prop."_".$i,
				"name" => $i,
				"url" => "#",
			));
			$set_yrs[$i] = $i;
			if($parent == 0)
			{
				$arr["year"] = $i;
				$this->__insert_months($t, "y_".$prop."_".$i, $prop, $arr);
			}
			if($parent != 0)
			{
				break;
			}
		}
	}

	private function __insert_months(&$t, $parent, $prop, $arr)
	{
		$months = $this->__get_months();
		$arr["filt_prop"] = $prop;
		foreach($months as $id => $month)
		{
			$t->add_item($parent, array(
				"id" => "m_".$id,
				"name" => $this->__parse_name($month, $id."-".$arr["year"], $arr),
				"url" => aw_url_change_var(array(
					"filt_type" => $prop,
					"filt_value" => $id."-".$arr["year"],
				), false, $arr["set_retu"]),
			));
			if($parent != 0)
			{
				break;
			}
		}
	}

	private function __get_months()
	{
		return array(
			1 => t("Jaanuar"),
			2 => t("Veebruar"),
			3 => t("M&auml;rts"),
			4 => t("Aprill"),
			5 => t("Mai"),
			6 => t("Juuni"),
			7 => t("Juuli"),
			8 => t("August"),
			9 => t("September"),
			10 => t("Oktoober"),
			11 => t("November"),
			12 => t("Detsember"),
		);
	}

	function __parse_name($name, $id, $arr, $num = false)
	{
		$add1 = $add2 = "";
		if($arr["filt_value"] == $id)
		{
			$add1 = "<strong>";
			$add2 = "</strong>";
		}
		
		if($num === false)
		{
			$num = 0;
			$filt = array(
				"class_id" => CL_PROJECT,
				"site_id" => array(),
				"lang_id" => array(),
			);
	
			$i = get_instance(CL_PROJECT);
			if($i->states[$id])
			{
				$filt["state"] = $id;
			}
			elseif(strpos($id, "-") !== false && $arr["filt_prop"])
			{
				$tmp = explode("-", $id);
				$start = mktime(0, 0, 0, $tmp[0], 1, $tmp[1]);
				$end = mktime(23, 59, 59, $tmp[0] +1, 0, $tmp[1]);
				$filt[$arr["filt_prop"]] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $start, $end);
			}
			elseif($this->can("view", $id) && !$num)
			{
				$o = obj($id);
				switch($o->class_id())
				{
					case CL_PROJECT_CATEGORY:
						$filt["category"] = $id;
						break;
					case CL_CRM_COMPANY:
						if($arr["inst_id"])
						{
							$filt["class_id"] = CL_BUG;
							$filt["customer"] = $id;
						}
						else
						{
							$filt["orderer"] = $id;
						}
						break;
					case CL_CRM_PERSON:
						$filt["proj_mgr"] = $id;
						if($arr["inst_id"])
						{
							$filt = false;
						}
						break;
					case CL_PROJECT:
						$filt["class_id"] = CL_BUG;
						$filt["project"] = $id;
						break;
				}
			}
		}
		if($filt)
		{
			$ol = new object_list($filt);
			$num = $ol->count();
			$numpart = " (".$num.")";
		}
		elseif($num !== false)
		{
			$numpart = " (".$num.")";
		}
		if(strlen($name) > 24)
		{
			$name = substr($name, 0, 24)."...";
		}
		return $add1.$name.$add2.$numpart;
	}

	function _get_proj_tbl1($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		
		$ol = new object_list();
		
		if ($arr["request"]["do_proj_search"] == 1)
		{
			$u = get_instance(CL_USER);
			$i = get_instance("applications/crm/crm_company_cust_impl");
			$filt = $i->_get_my_proj_search_filt($arr["request"], null, null);
			$ol = new object_list($filt);
			if($pn = $arr["request"]["proj_search_proj_mgr"])
			{
				$p_ol = new object_list(array(
					"class_id" => CL_CRM_PERSON,
					"site_id" => array(),
					"lang_id" => array(),
					"name" => "%".$pn."%",
				));
				$ol = new object_list();
				foreach($p_ol->ids() as $pid)
				{
					$filt["proj_mgr"] = $pid;
					$ol->add(new object_list($filt));
				}
			}
			$t->set_caption(t("Projektide otsingu tulemused"));
		}
		elseif(!$arr["request"]["filt_type"])
		{
			$po = get_current_person();
			$ol = new object_list(array(
				"class_id" => CL_PROJECT,
				"site_id" => array(),
				"lang_id" => array(),
				"proj_mgr" => $po->id(),
			));
			$t->set_caption(sprintf(t("Projektid, milles %s on projektijuht"), $po->name()));
		}
		elseif($arr["request"]["filt_type"] && $arr["request"]["filt_value"])
		{
			switch($arr["request"]["filt_type"])
			{
				case "category":
					$ol = new object_list(array(
						"class_id" => CL_PROJECT,
						"site_id" => array(),
						"lang_id" => array(),
						"category" => $arr["request"]["filt_value"],
					));
					if($this->can("view", $arr["request"]["filt_value"]))
					{
						$t->set_caption(sprintf(t("Projektid kategoorias %s"), obj($arr["request"]["filt_value"])->name()));
					}
					break;

				case "customer":
					$ol = new object_list(array(
						"class_id" => CL_PROJECT,
						"site_id" => array(),
						"lang_id" => array(),
						"orderer" => $arr["request"]["filt_value"],
					));
					if($this->can("view", $arr["request"]["filt_value"]))
					{
						$t->set_caption(sprintf(t("Projektid kliendiga %s"), obj($arr["request"]["filt_value"])->name()));
					}
					break;

				case "state":
					$ol = new object_list(array(
						"class_id" => CL_PROJECT,
						"site_id" => array(),
						"lang_id" => array(),
						"state" => $arr["request"]["filt_value"],
					));
					$i = get_instance(CL_PROJECT);
					$t->set_caption(sprintf(t("Projektid staatusega %s"), $i->states[$arr["request"]["filt_value"]]));
					break;

				case "person":
					$ol = new object_list(array(
						"class_id" => CL_PROJECT,
						"site_id" => array(),
						"lang_id" => array(),
						"proj_mgr" => $arr["request"]["filt_value"],
					));
					if($this->can("view", $arr["request"]["filt_value"]))
					{
						$t->set_caption(sprintf(t("Projektid, milles %s on projektijuht"), obj($arr["request"]["filt_value"])->name()));
					}
					break;

				case "start":
				case "end":
				case "deadline":
					switch($arr["request"]["filt_type"])
					{
						case "start":
							$p = t("algus");
							break;
						case "end":
							$p = t("l&otilde;pp");
							break;
						case "deadline":
							$p = t("t&auml;htaeg");
							break;
					}
					$tmp = explode("-", $arr["request"]["filt_value"]);
					$start = mktime(0, 0, 0, $tmp[0], 1, $tmp[1]);
					$end = mktime(23, 59, 59, $tmp[0] +1, 0, $tmp[1]);
					$ol = new object_list(array(
						"class_id" => CL_PROJECT,
						"site_id" => array(),
						"lang_id" => array(),
						$arr["request"]["filt_type"] => new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $start, $end),
					));
					$months = $this->__get_months();
					$t->set_caption(sprintf(t("Projektid, mille %s on %s %s"), $p, $months[$tmp[0]], $tmp[1]));
					break;
			}
		}
		$i = get_instance("applications/crm/crm_company_cust_impl");

		foreach ($ol->arr() as $project_obj)
		{
			$i->_get_proj_data_row($project_obj, $data);
		}

		$i->do_projects_table_header($t, $data, false, true);
		foreach($data as $row)
		{
			$row["actions"] = html::href(array(
				"url" => $this->mk_my_orb("change", array(
					"id" => $arr["obj_inst"]->id(),
					"group" => "by_prop",
					"filt_type" => "project",
					"filt_value" => $row["oid"],
				), CL_BUG_TRACKER),
				"caption" => t("Tegevused"),
			));
			$t->define_data($row);
		}
	}
	
	function _get_proj_tbl2($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];

		if(!$arr["request"]["filt_type"] && !$arr["request"]["do_proj_search"])
		{
			$po = get_current_person();
			$conn = $po->connections_to(array(
				"from.class_id" => CL_PROJECT,
				"type" => "RELTYPE_PARTICIPANT",
			));
			$t->set_caption(sprintf(t("Projektid, milles %s on osaleja"), $po->name()));
		}
		else
		{
			return PROP_IGNORE;
		}

		$i = get_instance("applications/crm/crm_company_cust_impl");

		$data = array();
		foreach ($conn as $c)
		{
			$i->_get_proj_data_row($c->from(), $data);
		}
		$i->do_projects_table_header($t, $data, false, true);
		foreach($data as $row)
		{
			$row["actions"] = html::href(array(
				"url" => $this->mk_my_orb("change", array(
					"id" => $arr["obj_inst"]->id(),
					"group" => "by_prop",
					"filt_type" => "project",
					"filt_value" => $row["oid"],
				), CL_BUG_TRACKER),
				"caption" => t("Tegevused"),
			));
			$t->define_data($row);
		}
	}

	function _get_proj_search_state($arr)
	{
		$prop = &$arr["prop"];
		$proj_i = get_instance(CL_PROJECT);
		$prop["options"] = array("" => t("K&otilde;ik")) + $proj_i->states;
		$prop["value"] = $arr["request"][$prop["name"]];
	}

	function _get_proj_search_part($arr)
	{
		$tt = t("Kustuta");
		$arr["prop"]["value"] = html::textbox(array(
			"name" => "proj_search_part",
			"value" => $arr["request"][$arr["prop"]["name"]],
			"size" => 15
		))."<a href='javascript:void(0)' title=\"$tt\" alt=\"$tt\" onClick='document.changeform.proj_search_part.value=\"\"'><img title=\"$tt\" alt=\"$tt\" src='".aw_ini_get("baseurl")."/automatweb/images/icons/delete.gif' border=0></a>";
		return PROP_OK;
	}

	function _get_proj_toolbar($arr)
	{
		$tb = &$arr["prop"]["vcl_inst"];

		if($arr["request"]["filt_type"] == "category" && $this->can("add", $arr["request"]["filt_value"]))
		{
			$parent = $arr["request"]["filt_value"];
			$is_cat = true;
		}
		else
		{
			$owner = get_instance(CL_BUG_TRACKER)->_get_owner($arr);
			if(!$owner)
			{
				$arr["prop"]["error"] = t("BT omanik on m&auml;&auml;ramata!");
				return PROP_ERROR;
			}
			$parent = $owner->id();
		}

		$tb->add_new_button(array(CL_PROJECT_CATEGORY), $parent);

		if($is_cat)
		{
			$tb->add_button(array(
				"name" => "cut",
				"action" => "cut_project",
				"img" => "cut.gif",
				"tooltip" => t("L&otilde;ika"),
			));
		}
		$tb->add_button(array(
			"name" => "copy",
			"action" => "copy_project",
			"img" => "copy.gif",
			"tooltip" => t("Kopeeri"),
		));

		if($is_cat && ($_SESSION["bt_proj_cut_clip"] || $_SESSION["bt_proj_copy_clip"]))
		{
			$tb->add_button(array(
				"name" => "paste",
				"action" => "paste_project",
				"img" => "paste.gif",
				"tooltip" => t("Kleebi"),
			));
		}
	}
}
