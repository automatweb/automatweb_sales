<?php
/*
@classinfo  maintainer=markop
*/
class project_teams_impl extends class_base
{
	function project_teams_impl()
	{
		$this->init();
	}

/*	function _get_team_team_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];

//		if ($arr["request"]["team"] == "" )
//		{
			$tb->add_button(array(
				"name" => "new",
				"img" => "new.gif",
				"tooltip" => t("Tiim"),
				"url" => $this->mk_my_orb("new", array(
					"parent" => $arr["obj_inst"]->id(), 
					"return_url" => get_ru(),
					"alias_to" => $arr["obj_inst"]->id(),
					"reltype" => 21
				), CL_PROJECT_TEAM)
			));
//		}

		if ($arr["request"]["team"] == "teams" || is_oid($arr["request"]["team"]))
		{
			$tb->add_button(array(
				"name" => "delete",
				"img" => "delete.gif",
				"action" => "del_team_mem",
				"tooltip" => t("Kustuta"),
			));
		}

		$tb->add_separator();

		if ($arr["request"]["team"] != "teams")
		{
			$tb->add_button(array(
				"name" => "copy",
				"img" => "copy.gif",
				"action" => "copy_team_mem",
				"tooltip" => t("Kopeeri"),
			));
		}
		
		if (is_array($_SESSION["proj_team_member_copy"]) && count($_SESSION["proj_team_member_copy"] && is_oid($arr["request"]["team"])))
		{
			$tb->add_button(array(
				"name" => "paste",
				"img" => "paste.gif",
				"action" => "paste_team_mem",
				"tooltip" => t("Kleebi"),
			));
		}
	}
*/

	function _get_team_tb($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$t->add_button(array(
				"name" => "new",
				"img" => "new.gif",
				"tooltip" => t("Uus Meeskond"),
				"url" => $this->mk_my_orb("new", array(
					"parent" => $arr["obj_inst"]->id(), 
					"return_url" => get_ru(),
					"alias_to" => $arr["obj_inst"]->id(),
					"reltype" => 21
				), CL_PROJECT_TEAM)
			));
//		}

/*		if($arr["request"]["team"] != "teams" && !is_oid($arr["request"]["team"]) && $arr["request"]["team"] != "all_parts" || !$arr["request"]["no_search"])
		{
			$t->add_button(array(
				"name" => "save",
				"img" => "save.gif",
				"action" => "add_participants",
				"tooltip" => t("Lisa valitud isikud meeskonda"),
			));
		}
*/
		if ($arr["request"]["team"] == "teams" || is_oid($arr["request"]["team"]) && $arr["request"]["no_search"])
		{
			$t->add_button(array(
				"name" => "delete",
				"img" => "delete.gif",
				"action" => "del_team_mem",
				"tooltip" => t("Kustuta"),
			));
		}

		$t->add_separator();
		if ($arr["request"]["team"] != "teams")
		{
			$t->add_button(array(
				"name" => "copy",
				"img" => "copy.gif",
				"action" => "copy_team_mem",
				"tooltip" => t("Kopeeri"),
			));
		}
		if (is_array($_SESSION["proj_team_member_copy"]) && count($_SESSION["proj_team_member_copy"] && is_oid($arr["request"]["team"])))
		{
			$t->add_button(array(
				"name" => "paste",
				"img" => "paste.gif",
				"action" => "paste_team_mem",
				"tooltip" => t("Kleebi"),
			));
		}
	}

	function _get_team_team_tree($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];

		//default tuleks tabelisse projekti meeskond
		if(!$arr["request"]["team_search_co"] && !$arr["request"]["team_search_person"] && !$arr["request"]["team"])
		{
			$arr["request"]["team"] = "all_parts";
			$arr["request"]["no_search"] = 1;
		}

		
		$nm = t("T&ouml;&ouml;perekonnad");
		if ($arr["request"]["team"] == "")
		{
			$nm = "<b>".$nm."</b>";
		}
		$url = aw_url_change_var("no_search", "1");
		$tb->add_item(0, array(
			"name" => $nm,
			"id" => "teams",
			"url" => aw_url_change_var("team", "teams", $url),
			"iconurl" => icons::get_icon_url(CL_MENU)
		));

		// list all teams from project
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_TEAM")) as $c)
		{
			$nm = $c->prop("to.name");
			if ($arr["request"]["team"] == $c->prop("to"))
			{
				$nm = "<b>".$nm."</b>";
			}
			$tb->add_item("teams", array(
				"name" => $nm,
				"id" => $c->prop("to"),
				"url" => aw_url_change_var("team", $c->prop("to"), $url),
				"iconurl" => icons::get_icon_url(CL_PROJECT_TEAM)
			));
		}
		$nm = t("Projekti meeskond");
		if ($arr["request"]["team"] == "all_parts")
		{
			$nm = "<b>".$nm."</b>";
		}
		$tb->add_item(0, array(
			"name" => $nm,
			"id" => "parts",
			"url" => aw_url_change_var("team", "all_parts", $url),
			"iconurl" => icons::get_icon_url(CL_MENU)
		));
	}

	function _get_team($arr)
	{
		//default tuleks tabelisse projekti meeskond
		if(!$arr["request"]["team_search_co"] && !$arr["request"]["team_search_person"] && !$arr["request"]["team"])
		{
			$arr["request"]["team"] = "all_parts";
			$arr["request"]["no_search"] = 1;
		}
		$t =& $arr["prop"]["vcl_inst"];
		//n2itab vaid meeskondi... juhul kui vajutatakse "Tiimid" peale
		if(($arr["request"]["no_search"]) && $arr["request"]["team"] == "teams")
		{
			$t->set_caption("<b>".t("T&ouml;&ouml;perekonnad")."<b>");
			$this->_init_teams_t($t);
			foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_TEAM")) as $c)
			{
				$t->define_data(array(
					"name" => $c->prop("to.name"),
					"oid" => $c->prop("to"),
				));
			}
			return;
		}
		
		$this->_init_team_t($t);

		if(($arr["request"]["no_search"]) && ($arr["request"]["team"] == "all_parts" || is_oid($arr["request"]["team"])))
		{
			//tabelile pealkiri
			if(($arr["request"]["no_search"]) && ($arr["request"]["team"] =="all_parts")) $t->set_caption("<b>".t("Projekti meeskond")."<b>");
		
			$connectons = array();
			if(is_oid($arr["request"]["team"]))
			{
				$team = obj($arr["request"]["team"]);
				$connections = $team->connections_from(array("type" => "RELTYPE_TEAM_MEMBER"));
				$t->set_caption("<b>".t("T&ouml;&ouml;perekonna")." \"".$team->name()."\" ".t("liikmed")."<b>");
			}
			else $connections = $arr["obj_inst"]->connections_from(array("type" => "RELTYPE_PARTICIPANT"));
			
			$p = get_instance(CL_CRM_PERSON);
			$from = $arr["obj_inst"]->prop("implementor");
			if (is_array($from))
			{
				$from = reset($from);
			}
			$to = $arr["obj_inst"]->prop("orderer");
			if (is_array($to))
			{
				$to = reset($to);
			}
			$ol = new object_list();
			foreach($connections as $c)
			{
				$o = $c->to();
				if ($o->class_id() == CL_CRM_COMPANY)
				{
					continue;
				}

				if ($o->class_id() == CL_USER)
				{
					$i = $o->instance();
					$o = obj($i->get_person_for_user($o));
				}

				$co = $o->get_all_org_ids();
				$co_s = array();
				if (count($co))
				{
					foreach($co as $co_oid)
					{
						$co_s[] = html::obj_change_url(obj($co_oid));
					}
				}
				else
				{
					$empl = $o->company();//$o->get_first_obj_by_reltype("RELTYPE_WORK");
					if ($empl)
					{
						$co_s[] = html::obj_change_url($empl);
					}
				}
		
				$role_url = $this->mk_my_orb("change", array(
					"from_org" => $from,
					"to_org" => $to,
					"to_project" => $arr["obj_inst"]->id()
				), "crm_role_manager");
		
				$ol_2 = new object_list(array(
					"class_id" => CL_CRM_COMPANY_ROLE_ENTRY,
					"lang_id" => array(),
					"site_id" => array(),
					"company" => $from,
					"client" => $to, 
					"project" => $arr["obj_inst"]->id(),
					"person" => $o->id()
				));
				
	
				$rs = array();
				foreach($ol_2->arr() as $role_entry)
				{
					$tmp = html::obj_change_url($role_entry->prop("role"));
					$tmp = html::obj_change_url($role_entry->prop("unit")).($tmp != "" ? " / " : "").$tmp;
					$rs[] = $tmp;
				}
				$t->define_data(array(
					"person" => html::obj_change_url($o),
					"co" => join(", ", $co_s),
					"rank" => html::obj_change_url($o->prop("rank")),
					"phone" => html::obj_change_url($o->prop("phone")),
					"mail" => html::obj_change_url($o->prop("email")),
					"roles" => join("<br>", $rs)."<br>".html::popup(array(
						"url" => $role_url,
						'caption' => t('Rollid'),
						"width" => 800,
						"height" => 600,
						"scrollbars" => "auto"
					)),
					"oid" => $o->id()
				));
				$ol->add($o);
			}
		}
		else
		{
			$t->set_caption("<b>".t("Otsingu tulemused")."<b>");
			//v6imalikud organisatsioonid
			if(substr_count($arr["request"]["team_search_co"], ',') > 0 )
			{
				$arr["request"]["team_search_co"] = explode(',' , $arr["request"]["team_search_co"]);
			}
			$org_ids = array();
			
			if(!is_array($arr["request"]["team_search_co"])) {
				$arr["request"]["team_search_co"] = array($arr["request"]["team_search_co"]);
			}
			
			foreach($arr["request"]["team_search_co"] as $co)
			{
				$org_list = new object_list(array(
					"class_id" => CL_CRM_COMPANY,
					"name" => "%".$co."%",
					"lang_id" => array(),
					"site_id" => array()
				));
				$org_ids = $org_ids+$org_list->ids();
			}
			if (($arr["request"]["team_search_person"] == "" && $arr["request"]["team_search_co"] == "") || (is_array($org_ids) && !sizeof($org_ids)))
			{
				$ol = new object_list();
			}
			else
			{
				$ol = new object_list(array(
					"class_id" => CL_CRM_PERSON,
					"name" => "%".$arr["request"]["team_search_person"]."%",
//					"CL_CRM_PERSON.RELTYPE_WORK.name" => "%".$arr["request"]["team_search_co"]."%",
//					"CL_CRM_PERSON.RELTYPE_WORK.id" => $org_ids,
					"lang_id" => array(),
					"site_id" => array(),
					new object_list_filter(array(
						"logic" => "OR",
						"conditions" => array(
							"CL_CRM_PERSON.RELTYPE_WORK" => $org_ids,
							"CL_CRM_PERSON.CURRENT_JOB.org" => $org_ids,
						))
					),
				));
			}
			//juhuks kui otsitakse mitut isikut komaga eraldatud
			if(substr_count($arr["request"]["team_search_person"], ',') > 0 && !(is_array($org_ids) && !sizeof($org_ids)))
			{
				$arr["request"]["team_search_person"] = explode(',' , $arr["request"]["team_search_person"]);
				foreach($arr["request"]["team_search_person"] as $person)
				{
					$pl = new object_list(array(
						"class_id" => CL_CRM_PERSON,
						"name" => "%".$person."%",
						new object_list_filter(array(
							"logic" => "OR",
							"conditions" => array(
								"CL_CRM_PERSON.RELTYPE_WORK" => $org_ids,
								"CL_CRM_PERSON.CURRENT_JOB.org" => $org_ids,
							))
						),
						"lang_id" => array(),
						"site_id" => array()
					));
					$ol->add($pl);
				}
			}
			
			foreach($ol->arr() as $o)
			{
				$t->define_data(array(
					"person" => html::obj_change_url($o),
					"rank" => html::obj_change_url($o->prop("rank")),
					"phone" => $o->prop("phone.name"),
					"mail" => $o->prop("email.name"),
					"co" => html::obj_change_url($o->company()),
					"oid" => $o->id(),
//					"roles" => join("<br>", $rs)."<br>".html::popup(array(
//						"url" => $role_url,
//						'caption' => t('Rollid'),
//						"width" => 800,
//						"height" => 600,
//						"scrollbars" => "auto"
//					)),
				));
			}
		}
	}


	function _init_team_t(&$t)
	{
		$t->define_field(array(
			"name" => "person",
			"caption" => t("Nimi"),
			"align" => "center",
			"width" => "16%",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "co",
			"caption" => t("Organisatsioon"),
			"align" => "center",
			"width" => "16%",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "rank",
			"caption" => t("Ametinimetus"),
			"align" => "center",
			"width" => "16%",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "phone",
			"caption" => t("Telefon"),
			"align" => "center",
			"width" => "16%",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "mail",
			"caption" => t("E-post"),
			"align" => "center",
			"width" => "16%",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "roles",
			"caption" => t("Rollid"),
			"align" => "center",
			"width" => "16%",
			"sortable" => 1
		));

		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}
	
	function _init_teams_t(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
//			"width" => "16%",
			"sortable" => 1
		));

		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	function _get_team_team_tbl($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$person_list = array();
		if ($arr["request"]["team"] == "all_parts")
		{
			$p = get_instance(CL_PROJECT);
			$person_list = $p->get_team($arr["obj_inst"]);
		}
		else
		if ($this->can("view", $arr["request"]["team"]))
		{
			$to  = obj($arr["request"]["team"]);
			foreach($to->connections_from(array("type" => "RELTYPE_TEAM_MEMBER")) as $c)
			{
				$person_list[$c->prop("to")] = $c->prop("to");
			}
		}
		$co = get_instance(CL_CRM_COMPANY);
		$co->display_persons_table($person_list, &$t);
	}
}
?>
