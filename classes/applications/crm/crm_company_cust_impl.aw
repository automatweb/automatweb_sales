<?php
/*
@classinfo  maintainer=markop
*/
class crm_company_cust_impl extends class_base
{
	public $use_group = "";

	function crm_company_cust_impl()
	{
		$this->init();
	}

	function do_projects_table_header(&$table, $data = false, $skip_sel = false, $is_bt= null)
	{
		$table->define_field(array(
			"name" => "project_name",
			"caption" => t("Nimi"),
			"sortable" => 1
		));

		if($is_bt)
		{
			$table->define_field(array(
				"name" => "actions",
				"caption" => t("Tegevused")
			));
		}

		if ($this->use_group === "org_projects_archive")
		{
			$table->define_field(array(
				"name" => "project_code",
				"caption" => t("Kood"),
				"sortable" => 1
			));

			$table->define_field(array(
				"name" => "archive_code",
				"caption" => t("Arhiveerimistunnus"),
				"sortable" => 1
			));
		}

		if (is_array($data))
		{
			$filt = array();
			foreach($data as $row)
			{
				if (trim($row["project_orderer"]) != "")
				{
					foreach(explode(",", strip_tags($row["project_orderer"])) as $ord_pt)
					{
						$filt["project_orderer"][] = trim($ord_pt);
					}
				}
				if (trim($row["project_impl"]) != "")
				{
					foreach(explode(",", strip_tags($row["project_impl"])) as $ord_pt)
					{
						$filt["project_impl"][] = trim($ord_pt);
					}
				}
				$part = strip_tags($row["project_participants"]);
				foreach(explode(",", $part) as $nm)
				{
					$filt["project_participants"][] = trim($nm);
				}
			}
		}

		$filt["project_participants"] = array_unique($filt["project_participants"]);

		$table->define_field(array(
			"name" => "project_orderer",
			"caption" => t("Tellija"),
			"sortable" => 1,
			"filter" => array_unique($filt["project_orderer"])
		));

		$table->define_field(array(
			"name" => "project_impl",
			"caption" => t("Teostaja"),
			"sortable" => 1,
			"filter" => array_unique($filt["project_impl"])
		));

		$table->define_field(array(
			"name" => "project_start",
			"caption" => t("Algus"),
			"sortable" => 1,
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y",
		));

		$table->define_field(array(
			"name" => "project_deadline",
			"caption" => t("T&auml;htaeg"),
			"sortable" => 1,
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y"
		));

		if ($_GET["group"] == "org_projects_archive")
		{
			$table->define_field(array(
				"name" => "project_end",
				"caption" => t("L&otilde;pp"),
				"sortable" => 1,
				"type" => "time",
				"numeric" => 1,
				"format" => "d.m.Y"
			));
		}

		$table->define_field(array(
			"name" => "project_participants",
			"caption" => t("Osalejad"),
			"sortable" => 1,
			"filter" => array_unique($filt["project_participants"])
		));

		if (!$skip_sel)
		{
			$table->define_field(array(
				"name" => "roles",
				"caption" => t("Rollid"),
				"sortable" => 0,
			));

			$table->define_chooser(array(
				"field" => "oid",
				"name" => "sel"
			));
		}
	}

	function _get_my_projects($arr)
	{
		$table = $arr["prop"]["vcl_inst"];

		$i = get_instance(CL_CRM_COMPANY);
		// if this is my co, then list all projects where my co is implementor

		// setting table caption:
		if(isset($arr['request']['pf']))
		{
			$format = t('%s projektid');
		}
		elseif ( isset($arr['request']['proj_search_part']) )
		{
			$participants_name = $arr['request']['proj_search_part'];
			$format = t('%s projektid');
			if ( !empty($participants_name) )
			{
				$format .= t(', milles %s on osaline');
			}
		}
		elseif(get_current_company() && get_current_company()->id() != $arr['obj_inst']->id())
		{
			$format = t('%s projektid');
		}
		else
		{
			$user_obj = get_current_person();
			$participants_name = $user_obj->name();
			$format = t('%s projektid, milles %s on osaline');
		}
		$table->set_caption(sprintf($format, $arr['obj_inst']->name(), $participants_name));

		$my_co = get_current_company();
		if ($my_co->id() == $arr["obj_inst"]->id())
		{
			// get list via search
			if ($arr["request"]["do_proj_search"] != 1)
			{
				$p = get_current_person();
				$arr["request"]["proj_search_part"] = $p->name();
				$arr["request"]["proj_search_state"] = 1;
			}
			$filt = $this->_get_my_proj_search_filt($arr["request"], null);
			$conns_ol = new object_list($filt);
		}
		else
		{
			// else list all projs where the requested co is orderer
			$conns_ol = new object_list(array(
				"class_id" => CL_PROJECT,
				"CL_PROJECT.RELTYPE_ORDERER" => $arr["obj_inst"]->id(),
				"lang_id" => array(),
				"site_id" => array(),
				"state" => $arr["request"]["do_proj_search"] == 1 ? null : 1
			));

			if ($arr["request"]["do_proj_search"] == 1)
			{
				$filt = $this->_get_my_proj_search_filt($arr["request"], $conns_ol->ids());
				if ($filt == -1)
				{
					$conns_ol = new object_list();
				}
				else
				{
					if (!count($filt["oid"]))
					{
						$filt["oid"] = -1;
					}
					$conns_ol = new object_list($filt);
				}
			}
		}

		$data = array();
		foreach ($conns_ol->arr() as $project_obj)
		{
			$this->_get_proj_data_row($project_obj, $data);
		}
		$this->do_projects_table_header($table, $data, isset($arr["prj"]));
		foreach($data as $row)
		{
			$table->define_data($row);
		}
		return PROP_OK;
	}

	function _get_proj_data_row($project_obj, &$data)
	{
		$orderer = $project_obj->get_first_obj_by_reltype("RELTYPE_ORDERER");
		if(is_object($orderer)) $orderer = $orderer->id();
		$roles = $this->_get_role_html(array(
			"from_org" => $arr["request"]["id"],
			"to_org" => $orderer,
			"rc_by_co" => $rc_by_co,
			"to_project" => $project_obj->id()
		));

		if (is_oid($cpi = $project_obj->prop("contact_person_implementor")) && $this->can("view", $cpi))
		{
			$impl = html::get_change_url($cpi, array("return_url" => get_ru()), parse_obj_name($project_obj->prop_str("contact_person_implementor")));
		}
		else
		{
			$impl = $this->_get_linked_names($project_obj->connections_from(array("type" => "RELTYPE_IMPLEMENTOR")));
		}
		$data[] = array(
			"project_name" => html::get_change_url($project_obj->id(), array("return_url" => get_ru()), parse_obj_name($project_obj->name())),
			"project_code" => $project_obj->prop("code"),
			"project_participants"	=> $this->_get_part_names($project_obj->connections_from(array("type" => "RELTYPE_PARTICIPANT"))),
			"project_created" => $project_obj->created(),
			"project_orderer" => $this->_get_linked_names($project_obj->connections_from(array("type" => "RELTYPE_ORDERER"))),
			"project_impl" => $impl,
			"project_deadline" => $project_obj->prop("deadline"),
			"project_start" => $project_obj->prop("start"),
			"project_end" => $project_obj->prop("end"),
			"oid" => $project_obj->id(),
			"roles" => $roles,
		);
	}

	function _get_impl_projects($arr)
	{
		$table = &$arr["prop"]["vcl_inst"];

		// get applicable projects
		$applicable_states = array(
			PROJ_IN_PROGRESS
		);

		$projects = new object_list(array(
			"class_id" => CL_PROJECT,
			"CL_PROJECT.RELTYPE_IMPLEMENTOR" => $arr["obj_inst"]->id(),
			"lang_id" => array(),
			"site_id" => array(),
			"state" => $applicable_states,
		));

		$this->proj_count = $projects->count();
		$this->get_impl_projects_header($table);

		$table->set_default_sortby ("start");
		$table->set_default_sorder ("desc");
		$table->define_pageselector (array (
			"type" => "text",
			"d_row_cnt" => $this->proj_count,
			"records_per_page" => 25,
		));

		$sum_expences = $sum_rec = $sum_due = $sum_budget = 0;
		$users_data = array();
		$cl_users = get_instance("users");
		$cl_user = get_instance (CL_USER);

		// populate table with data
		foreach($projects->arr() as $project)
		{
			$sales_person_uid = $project->createdby();
			$prj_mgr_oid = $project->prop("proj_mgr");

			if (!isset ($users_data["by_uid"][$sales_person_uid]))
			{
				$oid = $cl_users->get_oid_for_uid ($sales_person_uid);
				$user = obj ($oid);
				$oid = $cl_user->get_person_for_user ($user);
				$sales_person = obj ($oid);
				$users_data["by_uid"][$sales_person_uid]["name"] = $sales_person->name();
			}

			if (!isset ($users_data["by_oid"][$prj_mgr_oid]))
			{
				$prj_mgr_o = obj($prj_mgr_oid);
				$users_data["by_oid"][$prj_mgr_oid]["name"] = $prj_mgr_o->name();
			}

			$ol = new object_list($project->connections_from(array("type" => "RELTYPE_ORDERER")));
			$orderers = array();

			if ($ol->count())
			{
				foreach ($ol->arr() as $o)
				{
					$orderers[] = html::get_change_url ($o->id(), array("return_url" => get_ru()), $o->name());
				}

				$orderers =  implode(", ", $orderers);
			}
			else
			{
				$orderers = "";
			}


			$row = array(
				"project" => html::get_change_url ($project->id (), array("return_url" => get_ru()), $project->name()),
				"orderers" => $orderers,
				"start" => $project->prop("start"),
				"deadline" => $project->prop("deadline"),
				"bgcolour_overdue" => ($project->prop("deadline") < time() ? "red" : NULL),
				"phase" => $project->prop("state"),//!!! teha faasid
				"manager" => $users_data["by_oid"][$prj_mgr_oid]["name"],
				"sales_person" => $users_data["by_uid"][$sales_person_uid]["name"],//!!! ajutiselt created_by. aw myygitarkvara veel pole.
				"budget" => $project->prop("proj_price"),
				"outsourcing_expences" => $project->prop("outsourcing_expences"),
				"account_received" => $project->prop("account_received"),
				"account_due" => ($project->prop("proj_price") - $project->prop("account_received")),
				"oid" => $project->id(),
				"actions" => $actions_menu . html::hidden (array(
					"name" => "crm_prjmgr_proj_id[" . $project->id() . "]",
					"value" => $project->id(),
				))
			);
			$row_added = $table->define_data($row);

			if ($row_added)
			{
				$sum_budget += $project->prop ("proj_price");
				$sum_expences += $project->prop ("outsourcing_expences");
				$sum_rec += $project->prop ("account_received");
			}
		}

		### statistics
		$prefix = "<b>" . t("Kokku:") . "</b><br />";

		if ($sum_budget)
		{
			$row = array(
				"budget" => $prefix . number_format ($sum_budget, 2, ',', ' '),
				"outsourcing_expences" => $prefix . number_format ($sum_expences, 2, ',', ' '),
				"account_received" => $prefix . number_format ($sum_rec, 2, ',', ' '),
				"account_due" => $prefix . number_format (($sum_budget - $sum_rec), 2, ',', ' ')
			);
			$table->define_data ($row);
		}

		return PROP_OK;
	}

	function get_impl_projects_header(&$table)
	{
		$table->define_field(array(
			"name" => "project",
			"caption" => t("Nimi"),
			"sortable" => 1
		));
		$table->define_field(array(
			"name" => "orderers",
			"caption" => t("Tellija(d)"),
			"sortable" => 1
		));
		$table->define_field(array(
			"name" => "start",
			"caption" => t("Algus"),
			"sortable" => 1,
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y"
		));
		$table->define_field(array(
			"name" => "deadline",
			"caption" => t("T&auml;htaeg"),
			"sortable" => 1,
			"type" => "time",
			"numeric" => 1,
			"chgbgcolor" => "bgcolour_overdue",
			"format" => "d.m.Y"
		));
		$table->define_field(array(
			"name" => "phase",
			"caption" => t("Faas"),
			"sortable" => 1
		));
		$table->define_field(array(
			"name" => "manager",
			"caption" => t("Projektijuht"),
			"sortable" => 1
		));
		$table->define_field(array(
			"name" => "sales_person",
			"caption" => t("M&uuml;&uuml;ja/looja"),
			"sortable" => 1
		));
		$table->define_field(array(
			"name" => "budget",
			"caption" => t("Hind"),
			"sortable" => 1
		));
		$table->define_field(array(
			"name" => "outsourcing_expences",
			"caption" => t("V&auml;lja"),
			"sortable" => 1
		));
		$table->define_field(array(
			"name" => "account_received",
			"caption" => t("Saadud"),
			"sortable" => 1
		));
		$table->define_field(array(
			"name" => "account_due",
			"caption" => t("Saada"),
			"sortable" => 1
		));
		$table->define_field(array(
			"name" => "actions",
			"caption" => t(""),
		));
		$table->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
		));
	}

	function _get_part_names($conns)
	{
		if (!count($conns))
		{
			return;
		}
		$ol = new object_list($conns);

		$ol2 = new object_list(array(
			"oid" => $ol->ids(),
			"sort_by" => "objects.class_id, objects.name"
		));

		return html::obj_change_url($ol2->ids());
	}

	function _org_table_header($tf)
	{
		$tf->define_field(array(
			"name" => "name",
			"caption" => t("Kliendi nimi"),
			"sortable" => 1
		));

		$tf->define_field(array(
			"name" => "classif1",
			"caption" => t("Asutuse omadused")
		));

		$tf->define_field(array(
			"name" => "address",
			"caption" => t("Aadress")
		));

		$tf->define_field(array(
			"name" => "email",
			"caption" => t("Kontakt"),
			"align" => "center"
		));

		$tf->define_field(array(
			"name" => "url",
			"caption" => t("WWW")
		));

		$tf->define_field(array(
			"name" => "phone",
			"caption" => t('Telefon')
		));

		$tf->define_field(array(
			"name" => "fax",
			"caption" => t('Faks')
		));

		$tf->define_field(array(
			"name" => "ceo",
			"caption" => t("Juht")
		));

		$tf->define_field(array(
			"name" => "rollid",
			"caption" => t("Rollid")
		));

		$tf->define_field(array(
			"name" => "client_manager",
			"caption" => t("Kliendihaldur"),
			"sortable" => 1,
		));

		$tf->define_field(array(
			"name" => "customer_rel_creator",
			"caption" => t("Kliendisuhte looja"),
			"sortable" => 1
		));

		$tf->define_field(array(
			"name" => "pop",
			"caption" => t("&nbsp;")
		));

		$tf->define_chooser(array(
			"field" => "id",
			"name" => "check"
		));
	}


	function _get_role_html($arr)
	{
		extract($arr);
		$role_url = $this->mk_my_orb("change", array(
			"from_org" => $from_org,
			"to_org" => $to_org,
			"to_project" => $to_project
		), "crm_role_manager");

		$roles = array();

		$iter = safe_array($rc_by_co[$to_org]);
		if (!empty($to_project))
		{
			$iter = safe_array($rc_by_co[$to_org][$to_project]);
		}
		foreach($iter as $r_p_id => $r_p_data)
		{
			if ($this->can("view", $r_p_id))
			{
				$r_p_o = obj($r_p_id);
				$roles[] = html::get_change_url($r_p_o->id(), array(), parse_obj_name($r_p_o->name())).": ".join(",", $r_p_data);
			}
		}
		$roles = join("<br>", $roles);

		$roles .= ($roles != "" ? "<br>" : "" ).html::popup(array(
			"url" => $role_url,
			'caption' => t('Rollid'),
			"width" => 800,
			"height" => 600,
			"scrollbars" => "auto"
		));
		return $roles;
	}

	function _get_my_customers_toolbar($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];

		$category = isset($arr['request']['category']) ? $arr['request']['category'] : "";
		$cat = $category;
		if (!$cat)
		{
			$cat = $this->_get_first_cust_cat($arr["obj_inst"]);
			if ($cat)
			{
				$cat = $cat->id();
			}
		}

		$add = 1;
		$stchk = explode('_', $category);
		$lp = array(
			'parent' => $arr['obj_inst']->id(),
			'return_url' => get_ru(),
		);

		if($stchk[0] !== 'st')
		{
			$lp['alias_to'] = $cat;
			$lp['reltype'] = 3; // crm_company.CUSTOMER,
		}

		if ($arr["request"]["group"] === "relorg_b")
		{
			$lp["set_as_is_buyer"] = 1;
			if($stchk[0] == 'st')
			{
				$lp["set_buyer_status"] = $stchk[1];
			}
		}
		elseif ($arr["request"]["group"] === "relorg_s")
		{
			$lp["set_as_is_cust"] = 1;
		}
		elseif($stchk[0] === 'st')
		{
			$add = 0;
		}

		$tb->add_menu_item(array(
			'parent'=> "add_item",
			'text' => t("Organisatsioon"),
			'link' => $this->mk_my_orb('new',$lp,
				'crm_company'
			)
		));

		if($add == 1)
		{
			$tb->add_menu_button(array(
				'name'=>'add_item',
				'tooltip'=> t('Uus')
			));
		}

		if($stchk[0] !== 'st')
		{
			$tb->add_menu_item(array(
				'parent'=> "add_item",
				'text' => t("Erasik"),
				'link' => $this->mk_my_orb('new',array(
						'parent' => $arr['obj_inst']->id(),
						'alias_to' => $cat,
						'reltype' => 3, // crm_company.CUSTOMER,
						'return_url' => get_ru()
					),
					CL_CRM_PERSON
				)
			));


			// add category
			$alias_to = $arr['obj_inst']->id();
			$rt = 30;

			if((int)$category)
			{
				$alias_to = $category;
				$parent = (int)$category;
				$rt = 2;
			}

			$tb->add_menu_item(array(
				'parent'=>'add_item',
				'text' => t('Kategooria'),
				'link' => $this->mk_my_orb('new',array(
						'parent' => $arr['obj_inst']->id(),
						'alias_to' => $alias_to,
						'reltype' => $rt, //RELTYPE_CATEGORY
						'return_url' => get_ru()
					),
					'crm_category'
				)
			));
		}

		$tb->add_separator();

		//delete button
		$tb->add_menu_button(array(
			'name'=>'delete',
			'tooltip'=> t('Kustuta'),
			"img" => "delete.gif"
		));

		$tb->add_menu_item(array(
			'parent'=> "delete",
			'text' => t("Eemalda grupist"),
			"action" => "remove_from_category"
		));

		$tb->add_menu_item(array(
			'parent'=> "delete",
			'text' => t("L&otilde;peta klinedisuhe"),
			"action" => "remove_cust_relations"
		));

		$tb->add_menu_item(array(
			'parent'=> "delete",
			'text' => t("Kustuta l&otilde;plikult"),
			"action" => "submit_delete_ppl"
		));

		//save
		$tb->add_menu_button(array(
			'name'=>'save_as_cust',
			'tooltip'=> t('Salvesta kliendina'),
			"img" => "nool1.gif"
		));

		$seti = new crm_settings();
		$sts = $seti->get_current_settings();
		if ($sts && $sts->prop("send_mail_feature"))
		{
			$tb->add_button(array(
				'name'=>'send_email',
				'tooltip'=> t('Saada kiri'),
				"img" => "mail_send.gif",
				'action' => 'send_mails'
			));
		}
		$link = "#";
		$this->_do_cust_cat_tb_submenus($tb, $link, $arr["obj_inst"], "save_as_cust", "document.changeform.elements.cust_cat.value=%s;submit_changeform('save_as_customer')");
		$this->_do_cust_cat_tb_submenus2($tb);
		$tb->add_separator();

		$c = new popup_menu();
		$c->begin_menu("crm_co_ppl_filt");

		$c->add_item(array(
			"text" => t("T&uuml;hista"),
			"link" => aw_url_change_var("filt_p", null)
		));

		for($i = ord('A'); $i < ord("Z"); $i++)
		{
			$c->add_item(array(
				"text" => chr($i).(!empty($arr["request"]["filt_p"]) and $arr["request"]["filt_p"] === chr($i) ? " ".t("(Valitud)") : "" ),
				"link" => aw_url_change_var("filt_p", chr($i))
			));
		}

		$tb->add_cdata(" ".t("Vali filter:")." ".$c->get_menu().(!empty($arr["request"]["filt_p"]) ? t("Valitud:").$arr["request"]["filt_p"] : "" ));
	}

	function _get_my_customers_listing_tree($arr)
	{
		$tree_inst = $arr['prop']['vcl_inst'];
		$node_id = 0;

		$i = new crm_company();
		$i->active_node = (int)$arr['request']['category'];

		$i->generate_tree(array(
			'tree_inst' => $tree_inst,
			'obj_inst' => $arr['obj_inst'],
			'node_id' => &$node_id,
			'conn_type' => 'RELTYPE_CATEGORY',
			'skip' => array(CL_CRM_COMPANY),
			'attrib' => 'category',
			'leafs' => 'false',
			'style' => 'nodetextbuttonlike',
			"edit_mode" => 1
		));

		//need to delete every category of the tree that the person doesn't
		//have a relation with
		$my_data = array();
		$person = get_current_person();
		$conns = $person->connections_from(array(
			'type' => "RELTYPE_HANDLER",
		));

		foreach($conns as $conn)
		{
			$my_data[$conn->prop('to')] = $conn->prop('to');
		}
		//$this->_clean_up_the_tree(&$tree_inst->items, 0, &$my_data);
	}

	function _clean_up_the_tree($tree_items, $arrkey, $my_data)
	{
		$ret = false;
		foreach($tree_items[$arrkey] as $key=>$value)
		{
			//these are toplevel nodes
			//checking if one has sub_elements
			if(array_key_exists($value['id'], $tree_items))
			{
				//has subelements
				$ret = $this->_clean_up_the_tree($tree_items, $value['id'], &$my_data);
				$keep_it = false;

				foreach($my_data as $key2=>$value2)
				{
					if(in_array($value2, $value['oid']))
					{
						$keep_it = true;
						$ret = true;
					}
				}

				if(!$ret && !$keep_it)
				{
					unset($tree_items[$arrkey][$key]);
				}
			}
			//no sub elements, now if this node isn't useful to me
			//it will get deleted :)
			else
			{
				$keep_it = false;
				foreach($my_data as $key2=>$value2)
				{
					if(in_array($value2, $value['oid']))
					{
						$keep_it = true;
					}
				}
				if(!$keep_it)
				{
					unset($tree_items[$arrkey][$key]);
				}
				return $keep_it;
			}
		}
		return $ret;
	}

	function _get_offers_listing_toolbar($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];

		$tb->add_menu_button(array(
			'name'=>'add_item',
			'tooltip'=> t('Uus')
		));

		$params = array(
			'alias_to'=> $arr['obj_inst']->id(),
			'reltype'=> 9, //RELTYPE_OFFER,
			'org' => $arr['obj_inst']->id(),
			'alias_to_org' => $arr['request']['org_id'],
			"return_url" => get_ru()
		);

		$tb->add_menu_item(array(
				'disabled' => $arr['request']['org_id']? false : true,
				'parent'=>'add_item',
				'text'=>t('Pakkumine'),
				'url' => html::get_new_url(CL_CRM_OFFER, $arr['obj_inst']->id(), $params),
		));

		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"action" => "delete_selected_objects",
			"confirm" => t("Kas oled kindel, et soovid valitud pakkumise(d) kustutada?"),
			"tooltip" => t("Kustuta")
		));
	}

	function _get_offers_listing_tree($arr)
	{
		get_instance("core/icons");

		// list all child rels
		$parents = array();
		$c = new connection();
		foreach($c->find(array("from" => $data, "type" => 7 /* "RELTYPE_CHILD_ORG" */)) as $rel)
		{
			$parents[$rel["to"]] = $rel["from"];
		}

		$tree = $arr["prop"]["vcl_inst"];
		$node_id = (int)$arr["node_id"];

		$tree->start_tree(array(
			"type" => TREE_DHTML,
			"tree_id" => "arhp_t",
			"persist_state" => 1
		));

		$i = get_instance(CL_CRM_COMPANY);
		$i->active_node = (int)$arr['request']['category'];
		$i->tree_uses_oid = true;
		$i->generate_tree(array(
			'tree_inst' => $tree,
			'obj_inst' => $arr["node_id"] > 0 ? obj($arr["node_id"]) : $arr['obj_inst'],
			'node_id' => &$node_id,
			'conn_type' => 'RELTYPE_CATEGORY',
			'attrib' => 'category',
			'leafs' => "do_offer_tree_leafs",
			'style' => 'nodetextbuttonlike',
			'parent2chmap' => $parents
		));
		$tree->set_branch_func($this->mk_my_orb("get_offers_tree_branch",array("co_id" => $arr["obj_inst"]->id()))."&fetch_branch=");
		$tree->tree_type = TREE_DHTML;
		if ($arr["node_id"] && $arr["node_id"] != -1)
		{
			die($tree->finalize_tree(array("rootnode" => $arr["node_id"])));
		}
		$node_id = -1;
		$tree->add_item(0, array(
			'id' => $node_id,
			'name' => t('K&otilde;ik organisatsioonid'),
			'url' => '',
		));
		$tree->set_feature(PERSIST_STATE);

		if ($arr["node_id"] == -1)
		{
			$tree->items = array();
		}
		$all_org_parent = $node_id;

		$data = array();
		$i->get_customers_for_company($arr["obj_inst"], &$data);

		foreach ($data as $customer)
		{
			$obj = obj($customer);
			$pt = $all_org_parent;
			if (isset($parents[$customer]))
			{
				$pt = "ao".$parents[$customer];
			}
			$tree->add_item($pt, array(
				'id' => "ao".$customer,
				'name' => $obj->id()==$arr["request"]["org_id"]?"<b>".$obj->name()."</b>":$obj->name(),
				'iconurl' => icons::get_icon_url($obj->class_id()),
				'url' => aw_url_change_var(array('org_id' => $obj->id())),
			));
		}

		$tree->set_branch_func(
			$this->mk_my_orb(
				"get_offers_tree_branch",
				array(
					"co_id" => $arr["obj_inst"]->id(),
					"real_url" => get_ru(),
					"org_id" => $arr["request"]["org_id"]
				)
			)."&fetch_branch="
		);
		$tree->tree_type = TREE_DHTML;
		if ($arr["node_id"] == -1)
		{
			die($tree->finalize_tree(array("rootnode" => $arr["node_id"])));
		}

		$tree->has_root = true;
	}

	function _get_offers_listing_table($arr)
	{
		$table = $arr["prop"]["vcl_inst"];

		if(!$arr["request"]["org_id"])
		{
			$table->define_field(array(
				"name" => "org",
				"caption" => t("Organisatsioon"),
				"sortable" => "1",
				"align" => "center",
			));
		}

		$table->define_field(array(
			"name" => "offer_name",
			"caption" => t("Nimi"),
			"sortable" => "1",
			"align" => "center",
		));

		$table->define_field(array(
			"name" => "salesman",
			"caption" => t("Koostaja"),
			"sortable" => "1",
			"align" => "center",
		));

		$table->define_field(array(
			"name" => "offer_made",
			"caption" => t("Lisatud"),
			"sortable" => "1",
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.y",
			"align" => "center",
		));

		$table->define_field(array(
			"name" => "offer_sum",
			"caption" => t("Summa"),
			"sortable" => "1",
			"align" => "center",
		));

		$table->define_field(array(
			"name" => "offer_status",
			"caption" => t("Staatus"),
			"sortable" => "1",
			"align" => "center",
		));

		$table->define_chooser(array(
			"name" => "select",
			"field" => "select",
			"caption" => t("X"),
		));

		$offer_inst = get_instance(CL_CRM_OFFER);
		if($arr["request"]["org_id"])
		{
			$offers = &$offer_inst->get_offers_for_company($arr["request"]["org_id"], $arr["obj_inst"]->id());
		}
		else
		{
			$params = array(
				"preformer" => $arr["obj_inst"]->id(),
				"offer_status" => array(0,1,2),
				"class_id" => CL_CRM_OFFER,
			);

			if(is_oid($arr["request"]["category"]))
			{
				$cat = &obj($arr["request"]["category"]);
				$data = array();
				$i = get_instance(CL_CRM_COMPANY);
				$i->get_customers_for_company($cat,&$data,true);
				foreach ($data as $org)
				{
					$offer_obj = $offer_inst->get_offers_for_company($org, $arr["obj_inst"]->id());
					foreach ($offer_obj->arr() as $tmp)
					{
						$ids[] = $tmp->id();
					}
				}
				$params["oid"] = $ids;
				if(count($ids)>0)
				{
					$offers = new object_list($params);
				}
			}
			if(!$arr["request"]["org_id"] && !$arr["request"]["category"])
			{
				$offers = new object_list($params);
			}
		}

		if(is_object($offers))
		{
			if($offers->count() > 0)
			{
				$statuses = array(
					t("Koostamisel"),
					t("Saadetud"),
					t("Esitletud"),
					t("Tagasil&uuml;katud"),
					t("Positiivelt l&otilde;ppenud")
				);
				foreach ($offers->arr() as $offer)
				{
					//Do not list brother offers
					if($offer->is_brother())
					{
						continue;
					}
					$org = obj($offer->prop("orderer"));
					if($this->can("view", $offer->prop("salesman")))
					{
						$salesman = &obj($offer->prop("salesman"));
						$salesmanlink = html::get_change_url($salesman->id(), array(), $salesman->name());
					}
					$table->define_data(array(
						"org" => is_object($org)?html::get_change_url($org->id(), array(), $org->name()):false,
						"salesman" => $salesmanlink,
						"offer_name" => html::get_change_url($offer->id(), array(), $offer->name()),
						"offer_made" => $offer->created(),
						"offer_sum" => $offer->prop("sum"),//$offer_inst->total_sum($offer->id()),
						"select" => $offer->id(),
						"offer_status" => $statuses[$offer->prop("offer_status")],
						"offer_nr_status" => $offer->prop("offer_status"),
					));
					$table->set_default_sortby("offer_made");
					$table->set_default_sorder('desc');
				}
			}
		}
	}

	function _get_projects_listing_tree($arr)
	{
		if (!$arr["request"]["search_all_proj"])
		{
			return PROP_IGNORE;
		}
		return $this->_get_offers_listing_tree($arr);
	}

	function _get_projects_listing_table($arr)
	{
		$table = $arr["prop"]["vcl_inst"];
		$this->do_projects_table_header(&$table);

		// if this is my co, then list all projects where my co is implementor
		$my_co = get_current_company();

		if ( isset($arr['request']['all_proj_search_part']) )
		{
			$format = t('%s projektide arhiiv');
			$participants_name = $arr['request']['all_proj_search_part'];
			if ( !empty($participants_name) )
			{
				$format .= t(', milles %s on osaline');
			}
		}
		else
		{
			$user_obj = get_current_person();
			$participants_name = $user_obj->name();
			$format = t('%s projektid milles %s on osaline');
		}

		$table->set_caption(sprintf($format, $arr['obj_inst']->name(), $participants_name));

		if ($arr["request"]["search_all_proj"] == 1 && $arr["request"]["org_id"])
		{
			$ol = new object_list(array(
				"class_id" => CL_PROJECT,
				"CL_PROJECT.RELTYPE_ORDERER" => $arr["request"]["org_id"],
				"lang_id" => array(),
				"site_id" => array(),
			));
		}
		else
		if ($my_co->id() == $arr["obj_inst"]->id())
		{
			// get list via search
			if ($arr["request"]["aps_sbt"] != 1)
			{
				$p = get_current_person();
				$arr["request"]["all_proj_search_part"] = $p->name();
				$arr["request"]["all_proj_search_state"] = 2;
			}
			$filt = $this->_get_my_proj_search_filt($arr["request"], null, "all_");
			$ol = new object_list($filt);
		}
		else
		{
			// else list all projs where the requested co is orderer
			$ol = new object_list(array(
				"class_id" => CL_PROJECT,
				"CL_PROJECT.RELTYPE_ORDERER" => $arr["obj_inst"]->id(),
				"lang_id" => array(),
				"site_id" => array(),
				"state" => $arr["request"]["aps_sbt"] == 1 ? null : 2
			));
			if ($arr["request"]["aps_sbt"] == 1)
			{
				$filt = $this->_get_my_proj_search_filt($arr["request"], $ol->ids(), "all_");
				$ol = new object_list($filt);
			}
		}

		$rs_by_co = array();
		$role_entry_list = new object_list(array(
			"class_id" => CL_CRM_COMPANY_ROLE_ENTRY,
			"company" => $arr["request"]["id"],
			"client" => $arr["request"]["org_id"],
			"project" => $ol->ids()
		));

		foreach($role_entry_list->arr() as $role_entry)
		{
			$rc_by_co[$role_entry->prop("client")][$role_entry->prop("project")][$role_entry->prop("person")][] = html::get_change_url(
					$arr["request"]["id"],
					array(
						"group" => "contacts2",
						"unit" => $role_entry->prop("unit"),
					),
					parse_obj_name($role_entry->prop_str("unit"))
				)
				."/".
				html::get_change_url(
					$arr["request"]["id"],
					array(
						"group" => "contacts2",
						"cat" => $role_entry->prop("role")
					),
					parse_obj_name($role_entry->prop_str("role"))
				);
		}

		foreach ($ol->arr() as $project)
		{
			$roles = $this->_get_role_html(array(
				"from_org" => $arr["request"]["id"],
				"to_org" => $arr["request"]["org_id"],
				"rc_by_co" => $rc_by_co,
				"to_project" => $project->id()
			));
			$table->define_data(array(
				"project_name" => html::obj_change_url($project),
				"project_code" => $project->prop("code"),
				"archive_code" => $project->prop("archive_code"),
				"project_participants"	=> $this->_get_linked_names($project->connections_from(array("type" => "RELTYPE_PARTICIPANT"))),
				"project_created" => $project->created(),
				"roles" => $roles,
				"project_orderer" => $this->_get_linked_names($project->connections_from(array("type" => "RELTYPE_ORDERER"))),
				"project_impl" => $this->_get_linked_names($project->connections_from(array("type" => "RELTYPE_IMPLEMENTOR"))),
				"project_deadline" => $project->prop("deadline"),
				"project_end" => $project->prop("end"),
				"oid" => $project->id()
			));
		}
		$table->set_numeric_field("archive_code");
		$table->set_default_sortby("project_end");
		$table->set_default_sorder("desc");
	}

	function _get_offers_current_org_id($arr)
	{
		$arr["prop"]["value"] = $arr["request"]["org_id"];
	}

	function _get_org_proj_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];

		$tb->add_menu_button(array(
			"name" => "new",
			"tooltip" => t("Uus")
		));

		$tb->add_menu_item(array(
			'parent' => 'new',
			"text" => t("Projekt teostajana"),
			'link' => html::get_new_url(
				CL_PROJECT,
				$arr["obj_inst"]->id(),
				array(
					"connect_impl" => $arr["obj_inst"]->id(),
					"return_url" => get_ru(),
					"connect_orderer" => $arr["request"]["org_id"],
				)
			),
		));

		$tb->add_menu_item(array(
			'parent' => 'new',
			"text" => t("Projekt tellijana"),
			'link' => html::get_new_url(
				CL_PROJECT,
				$arr["obj_inst"]->id(),
				array(
					"connect_orderer" => $arr["obj_inst"]->id(),
					"return_url" => get_ru(),
					"connect_impl" => $arr["request"]["org_id"],
				)
			),
		));

		$tb->add_menu_item(array(
			'parent' => 'new',
			"text" => t("P&auml;eva raport"),
			'link' => html::get_new_url(
				CL_CRM_DAY_REPORT,
				$arr["obj_inst"]->id(),
				array(
					"alias_to" => $arr["obj_inst"]->id(),
					"reltype" => 39,
					"return_url" => get_ru()
				)
			),
		));

		$pl = get_instance(CL_PLANNER);
		$this->cal_id = $pl->get_calendar_for_user(array(
			"uid" => aw_global_get("uid"),
		));

		$url = $this->mk_my_orb('new',array(
			'alias_to_org' => $arr['obj_inst']->id(),
			'reltype_org' => 13,
			'class' => 'planner',
			'id' => $this->cal_id,
			'group' => 'add_event',
			'clid' => CL_TASK,
			'action' => 'change',
			'title' => t("Toimetus"),
			'parent' => $arr["obj_inst"]->id(),
			'return_url' => get_ru()
		));

		$tb->add_button(array(
			"name" => "add_task_to_proj",
			"img" => 'class_244.gif',
			"tooltip" => t("Lisa toimetus"),
			"action" => "add_task_to_proj"
		));

		$tb->add_separator();

		$tb->add_button(array(
			"name" => "mark_done",
			"img" => 'save.gif',
			"tooltip" => t("M&auml;rgi tehtuks"),
			"action" => "mark_proj_done"
		));

		if ($arr["request"]["group"] == "org_projects")
		{
			$tb->add_button(array(
				"name" => "search",
				"img" => "search.gif",
				"tooltip" => t("Otsi projekte"),
				"url" => aw_url_change_var(array(
					"search_all_proj" => 1,
					"category" => NULL,
					"org_id" => NULL
				))
			));
		}

		$tb->add_button(array(
			"name" => "delete",
			"img" => 'delete.gif',
			"tooltip" => t("Kustuta"),
			"confirm" => t("Oled kindel et soovid valitud projekte kustutada?"),
			"action" => "delete_projs"
		));
	}

	function _get_linked_names($conns)
	{
		$res = array();
		foreach ($conns as $conn)
		{
			$res[] = html::href(array(
				"url" => html::get_change_url($conn->prop("to"), array("return_url" => get_ru())),
				"caption" => $conn->prop("to.name"),
			));
		}
		return join(", ", $res);
	}

	function _get_my_proj_search_filt($ar, $oids, $prefix = "")
	{

		$ret = array(
			"class_id" => CL_PROJECT,
			"lang_id" => array(),
			"site_id" => array(),
			"oid" => $oids
		);


		if($ar["pf"])//kui valik tuleb puust... siis edasi ei otsi ka
		{
			$ar = array("pf" => $ar["pf"]);
			$stuff = explode("_" , $ar["pf"]);
			switch($stuff[0])
			{
				case "prman":
					$ret["proj_mgr"] = $stuff[1];
				break;
				case "custman":
					$ret["CL_PROJECT.RELTYPE_ORDERER.client_manager"] = $stuff[1];
				break;
				case "cust":
					if(is_oid($stuff[1]))
					{
						$ret["CL_PROJECT.RELTYPE_ORDERER"] = $stuff[1];
					}
					else
					{
						$ret["CL_PROJECT.RELTYPE_ORDERER.name"] = $stuff[1]."%";
					}
				break;

			}
		}



		if ($ar[$prefix."proj_search_cust"] != "")
		{
			$ret[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_PROJECT.RELTYPE_IMPLEMENTOR.name" => "%".$ar[$prefix."proj_search_cust"]."%",
					"CL_PROJECT.RELTYPE_ORDERER.name" => "%".$ar[$prefix."proj_search_cust"]."%",
				)
			));
		}

		if ($ar[$prefix."proj_search_part"] != "")
		{
			$ret["CL_PROJECT.RELTYPE_PARTICIPANT.name"] = map("%%%s%%", explode(",", $ar[$prefix."proj_search_part"]));
		}

		if ($ar[$prefix."proj_search_name"] != "")
		{
			$ret["name"] = "%".$ar[$prefix."proj_search_name"]."%";
		}

		if ($ar[$prefix."proj_search_code"] != "")
		{
			$ret["code"] = "%".$ar[$prefix."proj_search_code"]."%";
		}

		if ($ar[$prefix."proj_search_arh_code"] != "")
		{
			$ret["archive_code"] = "%".$ar[$prefix."proj_search_arh_code"]."%";
		}

		if ($ar[$prefix."proj_search_task_name"] != "")
		{
			$ret["CL_PROJECT.RELTYPE_PRJ_EVENT.name"] = "%".$ar[$prefix."proj_search_task_name"]."%";
		}

		$ar[$prefix."proj_search_dl_from"] = date_edit::get_timestamp($ar[$prefix."proj_search_dl_from"]);
		$ar[$prefix."proj_search_dl_to"] = date_edit::get_timestamp($ar[$prefix."proj_search_dl_to"]);

		if ($ar[$prefix."proj_search_dl_from"] > 1 && $ar[$prefix."proj_search_dl_to"] > 1)
		{
			$ret["deadline"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $ar[$prefix."proj_search_dl_from"], $ar[$prefix."proj_search_dl_to"]);
		}
		else
		if ($ar[$prefix."proj_search_dl_from"] > 1)
		{
			$ret["deadline"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $ar[$prefix."proj_search_dl_from"]);
		}
		else
		if ($ar[$prefix."proj_search_dl_to"] > 1)
		{
			$ret["deadline"] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $ar[$prefix."proj_search_dl_to"]);
		}


		$ar[$prefix."proj_search_end_from"] = date_edit::get_timestamp($ar[$prefix."proj_search_end_from"]);
		$ar[$prefix."proj_search_end_to"] = date_edit::get_timestamp($ar[$prefix."proj_search_end_to"]);

		if ($ar[$prefix."proj_search_end_from"] > 1 && $ar[$prefix."proj_search_end_to"] > 1)
		{
			$ret["end"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $ar[$prefix."proj_search_end_from"], $ar[$prefix."proj_search_end_to"]);
		}
		else
		if ($ar[$prefix."proj_search_end_from"] > 1)
		{
			$ret["end"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $ar[$prefix."proj_search_end_from"]);
		}
		else
		if ($ar[$prefix."proj_search_end_to"] > 1)
		{
			$ret["end"] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $ar[$prefix."proj_search_end_to"]);
		}

		if ($ar[$prefix."proj_search_state"])
		{
			$ret["state"] = $ar[$prefix."proj_search_state"];
		}

		if ($ar[$prefix."proj_search_contact_person"])
		{
			$ret["CL_PROJECT.contact_person_implementor.name"] = "%".$ar[$prefix."proj_search_contact_person"]."%";
		}

		if (is_array($ret["oid"]) && count($ret["oid"]) == 0)
		{
			$ret = -1;
		}
		return $ret;
	}

	function _get_org_proj_arh_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "search",
			"img" => "search.gif",
			"tooltip" => t("Otsi projekte"),
			"url" => aw_url_change_var(array(
				"search_all_proj" => 1,
				"category" => NULL,
				"org_id" => NULL
			))
		));
		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta"),
			"action" => "delete_projs",
			"confirm" => t("Oled kindel et soovid valitud projekte kustutada?")
		));
	}

	function _get_first_cust_cat($o)
	{
		$ol = new object_list($o->connections_from(array(
			"type" => "RELTYPE_CATEGORY",
		)));
		$ol->sort_by(array("prop" => "ord"));
		return $ol->begin();
	}

	function _init_report_list_t($t)
	{
		$t->define_field(array(
			"name" => "date",
			"caption" => t("Kuup&auml;ev"),
			"sortable" => 1,
			"type" => "time",
			"format" => "d.m.Y",
			"numeric" => 1,
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "reporter",
			"caption" => t("Esitaja"),
			"sortable" => 1,
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
			"align" => "center"
		));
	}

	function _get_report_list($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_report_list_t($t);

		if ($arr["request"]["group"] == "all_reports")
		{
			$reps = new object_list(array(
				"class_id" => CL_CRM_DAY_REPORT,
				"parent" => $arr["obj_inst"]->id(),
			));

			$format = t('%s k&otilde;ik raportid');
		}
		else
		{
			$user_obj = get_current_person();
			$current_person_oid = $user_obj->id();
			$reps = new object_list(array(
				"class_id" => CL_CRM_DAY_REPORT,
				"parent" => $arr["obj_inst"]->id(),
				"reporter" => $current_person_oid
			));
			$current_person_name = $user_obj->name();
			$format = t('%s raportid, milles on %s osaline');
		}

		$t->set_caption(sprintf($format, $arr['obj_inst']->name(), $current_person_name));

		foreach($reps->arr() as $r)
		{
			$rep = "";
			if ($this->can("view", $r->prop("reporter")))
			{
				$o = obj($r->prop("reporter"));
				$rep = html::get_change_url($o->id(), array("return_url" => get_ru()), $o->name());
			}
			$t->define_data(array(
				"date" => $r->prop("date"),
				"reporter" => $rep,
				"name" => html::get_change_url($r->id(), array("return_url" => get_ru()), $r->name())
			));
		}

		$t->set_default_sortby("date");
		$t->set_default_sorder("desc");
	}

	function _get_all_proj_search_part($arr)
	{
		if (!empty($arr["request"]["search_all_proj"]))
		{
			return PROP_IGNORE;
		}

		if (!empty($arr["request"]["all_proj_search_dl_from"]))
		{
			$p = $u->get_current_person();
			if($p->has_projects())
			{
				$v = $p->name();
			}
		}
		else
		{
			$v = $arr["request"]["all_proj_search_part"];
		}
		$tt = t("Kustuta");
		$arr["prop"]["value"] = html::textbox(array(
			"name" => "all_proj_search_part",
			"value" => $v,
			"size" => 15
		))."<a href='javascript:void(0)' title=\"$tt\" alt=\"$tt\" onClick='document.changeform.all_proj_search_part.value=\"\"'><img title=\"$tt\" alt=\"$tt\" src='".aw_ini_get("baseurl")."/automatweb/images/icons/delete.gif' border=0></a>";
		return PROP_OK;
	}

	function _get_proj_search_part($arr)
	{
		if (empty($arr["request"]["proj_search_dl_from"]))
		{
			$p = get_current_person();
			if($p->has_projects())
			{
				$v = $p->name();
			}
		}
		else
		{
			$v = $arr["request"]["proj_search_part"];
		}
		$tt = t("Kustuta");
		$arr["prop"]["value"] = html::textbox(array(
			"name" => "proj_search_part",
			"value" => $v,
			"size" => 15
		))."<a href='javascript:void(0)' title=\"$tt\" alt=\"$tt\" onClick='document.changeform.proj_search_part.value=\"\"'><img title=\"$tt\" alt=\"$tt\" src='".aw_ini_get("baseurl")."/automatweb/images/icons/delete.gif' border=0></a>";
		return PROP_OK;
	}

	function _get_customer_search_cust_mgr($arr)
	{
		if (empty($arr["request"]["customer_search_submit"]))
		{
			$p = get_current_person();
			if($p->is_cust_mgr())
			{
				$v = $p->name();
			}
			else
			{
				$v = "";
			}
		}
		else
		{
			$v = $arr["request"]["customer_search_cust_mgr"];
		}

		$tt = t("Kustuta");
		$arr["prop"]["value"] = html::textbox(array(
			"name" => "customer_search_cust_mgr",
			"value" => $v,
			"size" => 25
		))."<a href='javascript:void(0)' title=\"$tt\" alt=\"$tt\" onClick='document.changeform.customer_search_cust_mgr.value=\"\"'><img title=\"$tt\" alt=\"$tt\" src='".aw_ini_get("baseurl")."/automatweb/images/icons/delete.gif' border=0></a>";
		return PROP_OK;
	}

	function _get_customer_rel_creator($arr)
	{
		$v = isset($arr["request"]["customer_rel_creator"]) ? $arr["request"]["customer_rel_creator"] : "";
		$arr["prop"]["value"] = html::textbox(array(
			"name" => "customer_rel_creator",
			"value" => $v,
			"size" => 30
		));
		return PROP_OK;
	}

	function _get_customer_search_filter($r, $within = false, $oids)
	{
		$ret = array(
			"class_id" => array(CL_CRM_COMPANY, CL_CRM_PERSON),
		);

		$has_params = false;
		if($within)
		{
			$ret["oid"] = $within;
		}

		if ($r["customer_search_name"] != "")
		{
			$ret["name"] = "%".$r["customer_search_name"]."%";
			$has_params = true;
		}
		else
		{
			$ret["site_id"] = array();
			$ret["lang_id"] = array();
		}
		if ($r["customer_search_reg"] != "")
		{
			$ret["reg_nr"] = "%".$r["customer_search_reg"]."%";
			$has_params = true;
		}

		if ($r["customer_search_worker"] != "")
		{
			$persons = new object_list(array(
				"class_id" => CL_CRM_PERSON,
				"name" => "%".$r["customer_search_worker"]."%"
			));
			$rels = array();
			foreach($persons->arr() as $person)
			{
				foreach($person->connections_from(array(
					"to.class_id" => CL_CRM_PERSON_WORK_RELATION,
				)) as $c)
				{
					$to = $c->to();
					$rels[] = $to->prop("org");
				}
			}

			if(sizeof($rels))
			{
				$ret[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						"CL_CRM_COMPANY.RELTYPE_WORKERS.name" => "%".$r["customer_search_worker"]."%",
						"oid" => $rels,
					)
				));
			}
			else
			{
				$ret["CL_CRM_COMPANY.RELTYPE_WORKERS.name"] = "%".$r["customer_search_worker"]."%";
			}
			$has_params = true;
		}

		if ($r["customer_search_insurance_exp"] != "")
		{
			$ret["CL_CRM_COMPANY.RELTYPE_INSURANCE.insurance_type"] = (int) $r["customer_search_insurance_exp"];
			$ret["CL_CRM_COMPANY.RELTYPE_INSURANCE.expires"] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, time());
			$has_params = true;
		}

		if ($r["customer_search_address"] != "")
		{
			$ret["CL_CRM_COMPANY.contact.name"] = "%".$r["customer_search_address"]."%";
			$has_params = true;
		}

		if ($r["customer_search_city"] != "")
		{
			$ret["CL_CRM_COMPANY.contact.linn.name"] = "%".$r["customer_search_city"]."%";
			$has_params = true;
		}

		if ($r["customer_search_county"] != "")
		{
			$ret["CL_CRM_COMPANY.contact.maakond.name"] = "%".$r["customer_search_county"]."%";
			$has_params = true;
		}

		if ($r["customer_search_ev"] != "")
		{
			$ret["CL_CRM_COMPANY.ettevotlusvorm.name"] = "%".$r["customer_search_ev"]."%";
			$has_params = true;
		}

		if (trim($r["customer_search_keywords"]))
		{
			$has_params = true;
			$keywords= explode(",", $r["customer_search_keywords"]);

			foreach ($keywords as $keyword)
			{
				$keyword = trim($keyword);

				if ($keyword)
				{
					$ret[] = new object_list_filter(array(
						"logic" => "OR",
						"conditions" => array("activity_keywords" => "%," . $keyword . "%")
					));
				}
			}
		}

		if ($r["customer_search_cust_grp"] != "")
		{
			$has_params = true;
			// get all customers for group and stick into oid list
			$sectlist = array();
			$this->_req_get_sects(obj($r["customer_search_cust_grp"]), $sectlist);
			$s_from = array_keys($sectlist);
			$s_from[] = $r["customer_search_cust_grp"];
			$c = new connection();
			$co_conns = $c->find(array(
				"from" => $s_from,
				"from.class_id" => CL_CRM_CATEGORY,
				"type" => "RELTYPE_CUSTOMER"
			));

			$oids = array();
			foreach($co_conns as $co_con)
			{
				$oids[] = $co_con["to"];
			}
			if (count($oids) == 0)
			{
				$oids = -1;
			}
		}

		if (empty($r["customer_search_is_co"]["is_co"]) && !empty($r["customer_search_is_co"]["is_person"]))
		{
			$ret["class_id"] = CL_CRM_PERSON;
			$ret["is_customer"] = 1;
		}
		else
		if (!empty($r["customer_search_is_co"]["is_co"]) && empty($r["customer_search_is_co"]["is_person"]))
		{
			$ret["class_id"] = CL_CRM_COMPANY;
		}
		else
		{
			$ret[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_CRM_PERSON.is_customer" => 1,
					"CL_CRM_COMPANY.reg_nr" => "%" // this is here to match all companies, otherwise we'd just get persons
				)
			));
		}

		if ($r["customer_search_cust_mgr"] != "")
		{
			$has_params = true;
			// seems this should also search from roles. so, get all role entries for that person and collect the cos from those
			$relist = new object_list(array(
				"class_id" => CL_CRM_COMPANY_ROLE_ENTRY,
				"CL_CRM_COMPANY_ROLE_ENTRY.person.name" => map("%%%s%%", explode(",", $r["customer_search_cust_mgr"]))
			));

			$rs = array();
			foreach($relist->arr() as $o)
			{
				$rs = $o->prop("client");
			}

			$ret[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_CRM_COMPANY.client_manager.name" => map("%%%s%%", explode(",", $r["customer_search_cust_mgr"])),
					"CL_CRM_PERSON.client_manager.name" => map("%%%s%%", explode(",", $r["customer_search_cust_mgr"])),
					"oid" => $rs
				)
			));
		}

		if ($r["customer_search_classif1"] != "")
		{
			$oids = array();
			foreach ($r["customer_search_classif1"] as $val)
			{
				if (is_oid($val))
				{
					$oids[] = $val;
				}
				elseif ("NA" === $val)
				{
					$oids[] = new obj_predicate_compare(OBJ_COMP_NULL);
				}
			}

			if (count($oids))
			{
				$ret["CL_CRM_COMPANY.RELTYPE_METAMGR"] = $oids;
				$has_params = true;
			}
		}

		if($r["customer_rel_creator"])
		{
			$co = get_current_company();

			$options = $co->get_employees();
			$persons = new object_list(array(
				"class_id" => CL_CRM_PERSON,
				"name" => "%".$r["customer_rel_creator"]."%",
				"oid" => $options->ids(),
			));

			$cos = array();
			$crel = new object_list(array(
				"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
				"cust_contract_creator" => $persons->ids(),
				"seller" => $co->id(),
			));
			foreach($crel->arr() as $c)
			{
				 $cos[$c->prop("buyer")] = $c->prop("buyer");
			}
			$ret["oid"] = $cos;
		}

		$ret["sort_by"] = "name";
		$ret["limit"] = (100*$_GET["ft_page"]).", 100";
		$ret["sort_by"].= " ".( $_GET["sort_order"] == "desc" ? "DESC" : "ASC");

		return $ret;
	}

	function _get_customer_search_is_co($arr)
	{
		$arr["prop"]["options"] = array(
			"is_co" => t("Organisatsioon"),
			"is_person" => t("Eraisik")
		);
		if (empty($arr["request"]["customer_search_submit"]))
		{
			$arr["prop"]["value"] = array("is_co" => "is_co", "is_person" => "is_person");
		}
		else
		{
			$arr["prop"]["value"] = $arr["request"][$arr["prop"]["name"]];
		}
	}

	function _get_customer_listing_tree($arr)
	{
		$tree_inst = $arr['prop']['vcl_inst'];
		$tree_inst->set_only_one_level_opened(1);

		$node_id = 0;
		$category = isset($arr['request']['category']) ? (int) $arr['request']['category'] : 0;

		if (!empty($arr['request']['category']))
		{
			$f_cat = $this->_get_first_cust_cat($arr["obj_inst"]);
			if ($f_cat)
			{
				$arr['request']['category'] = $f_cat->id();
			}
		}

		$i = new crm_company();
		$i->active_node = $category;
		$i->generate_tree(array(
			'tree_inst' => $tree_inst,
			'obj_inst' => $arr['obj_inst'],
			'node_id' => &$node_id,
			'conn_type' => 'RELTYPE_CATEGORY',
			'skip' => array(CL_CRM_COMPANY),
			'attrib' => 'category',
			'leafs' => false,
			'style' => 'nodetextbuttonlike',
			"edit_mode" => 1,
			"statuses" => 1
		));

		$this->_add_cust_mgr($arr, $tree_inst);
		$this->_add_cust_alpha($arr, $tree_inst);

		return PROP_OK;
	}

	private function _add_cust_mgr($arr, $tree_inst)
	{
		$tree_inst->add_item(0, array(
			"id" => "cmgr",
			"name" => t("Kliendihaldur"),
			"url" => aw_url_change_var("category", null, aw_url_change_var("filt_p", null))
		));
		$ol = new object_data_list(array(
			"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
			"lang_id" => array(),
			"site_id" => array(),
			"seller" => $arr["obj_inst"]->id()
		),
		array(
			CL_CRM_COMPANY_CUSTOMER_DATA => array(new obj_sql_func(OBJ_SQL_UNIQUE, "client_manager", "client_manager"))
		));
		$tmp = $ol->arr();
		$ids = array(-1);
		foreach($tmp as $item)
		{
			if ($this->can("view", $item["client_manager"]))
			{
				$ids[] = $item["client_manager"];
			}
		}
		$ol = new object_list(array(
			"oid" => $ids,
			"lang_id" => array(),
			"site_id" => array()
		));
		$nms = $ol->names();
		foreach($nms as $id => $nm)
		{
			$tree_inst->add_item("cmgr", array(
				"id" => "cmgr_".$id,
				"name" => parse_obj_name($nm),
				"url" => aw_url_change_var("category", null, aw_url_change_var("filt_p", null, aw_url_change_var("cmgr", $id)))
			));
		}

		if (!empty($arr["request"]["cmgr"]))
		{
			$tree_inst->set_selected_item("cmgr_".$arr["request"]["cmgr"]);
		}
	}

	private function _add_cust_alpha($arr, $tree_inst)
	{
		$tree_inst->add_item(0, array(
			"id" => "alpha",
			"name" => t("T&auml;hestik"),
			"url" => aw_url_change_var("category", null)
		));
		for($i = ord("A"); $i < ord("Z"); $i++)
		{
			$tree_inst->add_item("alpha", array(
				"id" => "alpha_".chr($i),
				"name" => chr($i),
				"url" => aw_url_change_var("category", null, aw_url_change_var("filt_p", chr($i)))
			));
		}

		if (!empty($arr["request"]["filt_p"]))
		{
			$tree_inst->set_selected_item("alpha_".$arr["request"]["filt_p"]);
		}
	}

	function _finish_org_tbl($arr, &$orglist)
	{
		$tf = $arr["prop"]["vcl_inst"];
		$org = obj($arr["request"]["id"]);
		$format_s = t("%s kliendid");
		$format_t = t("%s kliendid: %s");

		if(!empty($arr["st"]))
		{
			$status = obj($arr["st"]);
			$oname = $org->name();
			$tf->set_caption($oname.t(" kliendid: ").$status->name());
		}
		else
		{
			$format = (($tmp = $arr["request"]["group"]) == "relorg_t" || $tmp = "relorg")?$format_t:$format_s;
			$tf->set_caption(sprintf($format, $org->name(), $arr["obj_inst"]->name()));
		}
		$this->_org_table_header(&$tf);
		$default_cfg = true;

		$cl_crm_settings = get_instance(CL_CRM_SETTINGS);
		$cl_crm_company = get_instance(CL_CRM_COMPANY);
		if ($o = $cl_crm_settings->get_current_settings())
		{
			$usecase = $cl_crm_company->get_current_usecase($arr);//$arr["obj_inst"] peab olemas olema.
			$cl_crm_settings->apply_table_cfg($o, $usecase, $arr["prop"]["name"], &$tf);
			$visible_fields = $cl_crm_settings->get_visible_fields($o, $usecase, $arr["prop"]["name"]);

			if (!empty($visible_fields))
			{
				$default_cfg = false;
			}
		}

		# some helper data for roles
		if ($default_cfg or in_array("rollid", $visible_fields))
		{
			$rc_by_co = array();
			$role_entry_list = new object_list(array(
				"class_id" => CL_CRM_COMPANY_ROLE_ENTRY,
				"company" => $arr["request"]["id"],
				"client" => $orglist,
				"project" => new obj_predicate_compare(OBJ_COMP_LESS, 1)
			));
			foreach($role_entry_list->arr() as $role_entry)
			{
				$rc_by_co[$role_entry->prop("client")][$role_entry->prop("person")][] = html::get_change_url(
						$arr["request"]["id"],
						array(
							"group" => "contacts2",
							"unit" => $role_entry->prop("unit"),
						),
						parse_obj_name($role_entry->prop_str("unit"))
					)
					."/".
					html::get_change_url(
						$arr["request"]["id"],
						array(
							"group" => "contacts2",
							"cat" => $role_entry->prop("role")
						),
						parse_obj_name($role_entry->prop_str("role"))
					);
			}
		}

		# table contents
		$perpage = 100;
		$page_nr = isset($arr["request"]["ft_page"]) ? (int) $arr["request"]["ft_page"] : 0;
		if($perpage > count($orglist))
		{
			$page_nr = 0;
		}

		foreach($orglist as $org)
		{
			$o = obj($org);

			// aga &uuml;lej&auml;&auml;nud on k&otilde;ik seosed!
			$name = $client_manager = $pm = $vorm = $tegevus = $contact = $juht = $juht_id = $phone = $fax = $url = $mail = $ceo = "";

			if ($this->can("view", $o->prop("ettevotlusvorm")))
			{
				$tmp = new object($o->prop("ettevotlusvorm"));
				$vorm = $tmp->prop('shortname');
			}

			# rollid
			if ($default_cfg or in_array("rollid", $visible_fields))
			{
				$roles = $this->_get_role_html(array(
					"from_org" => $arr["request"]["id"],
					"to_org" => $o->id(),
					"rc_by_co" => $rc_by_co
				));
			}

			if ($o->class_id() == CL_CRM_COMPANY)
			{
				# ceo
				if ($default_cfg or in_array("ceo", $visible_fields))
				{
					$ceo = html::obj_change_url($o->prop("firmajuht"));
				}

				# email
				if (($default_cfg or in_array("email", $visible_fields)) and ($this->can("view", $o->prop("email_id"))))
				{
					$mail_obj = new object($o->prop("email_id"));
					$mail = $mail_obj->prop("mail");
					$mail = empty($mail) ? "" : html::href(array(
						"url" => "mailto:" . $mail,
						"caption" => $mail
					));
				}

				# url
				if (($default_cfg or in_array("url", $visible_fields)) and ($this->can("view", $o->prop("url_id"))))
				{
					$url_o = obj($o->prop("url_id"));
					$url_str = $url_o->name();
					if (strpos($url_str, "http:") !== false && substr($url_str, 0, 3) == "www")
					{
						$url_str = "http://".$url_str;
					}
					$url = html::href(array(
						"url" => $url_str,
						"caption" => $url_str,
						"target" => "_blank"
					));
				}
			}
			else
			{
				$mail = "";
				if (is_oid($o->prop("email")) && $this->can("view", $o->prop("email")))
				{
					$mail_obj = new object($o->prop("email"));
					$mail .= html::href(array(
						"url" => "mailto:" . $mail_obj->prop("mail"),
						"caption" => $mail_obj->prop("mail"),
					));
				};
				if ($this->can("view", $o->prop("url")))
				{
					$urlo = obj($o->prop("url"));
					$ru = $urlo->prop_str("url");
					if (substr($ru, 0, 4) != "http")
					{
						$ru = "http://".$ru;
					}
					$url = html::href(array(
						"url" => $ru,
						"caption" => $urlo->prop_str("url"),
					));
				}
				if ($this->can("view", $o->prop("phone")))
				{
					$urlo = obj($o->prop("phone"));
					$phone = $urlo->name();
				}
			}

			# phone
			if (($default_cfg or in_array("phone", $visible_fields)) and $this->can("view", $o->prop("phone_id")))
			{
				$phone = obj($o->prop("phone_id"));
				$phone = $phone->name();
			}

			# fax
			if (($default_cfg or  in_array("fax", $visible_fields)) and $this->can("view", $o->prop("telefax_id")))
			{
				$fax = obj($o->prop("telefax_id"));
				$fax = $fax->name();
			}

			# client_manager
			if ($default_cfg or in_array("client_manager", $visible_fields))
			{
				$client_manager = html::obj_change_url($o->prop("client_manager"));
			}

			# pop
			if ($default_cfg or in_array("pop", $visible_fields))
			{
				$pm = get_instance("vcl/popup_menu");
				$pm->begin_menu("org".$o->id());
				$pm->add_item(array(
					"text" => t("Vaata"),
					"link" => $this->mk_my_orb("change", array("id" => $o->id(), "return_url" => get_ru(), "group" => "quick_view"), CL_CRM_COMPANY)
				));
				$pm->add_item(array(
					"text" => t("Muuda"),
					"link" => html::get_change_url($o->id(), array("return_url" => get_ru()))
				));
				if ($arr["request"]["category"])
				{
					$pm->add_item(array(
						"text" => t("Eemalda kliendigrupist"),
						"link" => $this->mk_my_orb("remove_from_cust_grp", array(
							"id" => $o->id(),
							"cgrp" => $arr["request"]["category"],
							"post_ru" => get_ru()
						))
					));
				}
				$pm = $pm->get_menu();
			}

			# name
			if ($default_cfg or in_array("name", $visible_fields))
			{
				$name = html::get_change_url($o->id(), array("return_url" => get_ru()), $o->name()." ".$vorm);
			}

			$_url = $this->mk_my_orb("get_cust_contact_table", array("id" => $o->id(), "return_url" => post_ru()));
			$namp = " (<a id='tnr".$o->id()."' href='javascript:void(0)' onClick='co_contact(".$o->id().",\"".$_url."\");'>".t("Kontaktid")."</a>) ";

			$c = $o->connections_from(array(
				"type" => "RELTYPE_METAMGR"
			));

			if (count($c))
			{
				$classif1 = array();
				foreach ($c as $c_o)
				{
					$classif1[] = $c_o->prop("to.name");
				}
				$classif1 = implode(", ", $classif1);
			}
			else
			{
				$classif1 = t("N/A");
			}

			//!!! todo: define and get data only for fields configured to be shown in current crm settings.
			$tf->define_data(array(
				"id" => $o->id(),
				"name" => $name.$namp,
				"classif1" => $classif1,
				"customer_rel_creator" => method_exists($o, "get_cust_rel_creator_name") ? $o->get_cust_rel_creator_name() : "n/a",///!!!! teha korda
				"reg_nr" => $o->prop("reg_nr"),
				"address" => $o->class_id() == CL_CRM_COMPANY ? $o->prop_str("contact") : $o->prop("RELTYPE_ADDRESS.name"),
				"ceo" => $ceo,
				"phone" => $phone,
				"fax" => $fax,
				"url" => $url,
				"email" => $mail,
				'rollid' => $o->class_id() == CL_CRM_CATEGORY ? "" : $roles,
				'client_manager' => $client_manager,
				"pop" => $o->class_id() == CL_CRM_CATEGORY ? "" : $pm,
			));
		}


		$tf->set_default_sortby("name");

		// make pageselector.
		$tf->define_pageselector(array(
			"type"=>"lb",
			"records_per_page"=>100,
			"d_row_cnt" => $org_count,
		));
	}

	function _get_customer($arr, $filter = NULL)
	{
		if (!empty($arr["request"]["customer_search"]))
		{
			return PROP_IGNORE;
		}

		if ($filter)
		{
			$orglist = $this->make_keys($filter);
		}
		else
		{
			if($arr["request"]["group"] !== "relorg_t" && $arr["request"]["group"] !== "relorg")
			{
				$ol2 = new object_list(array(
					"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
					(($this->use_group === "relorg_s")?"buyer":"seller") => $arr["obj_inst"]->id(),
				));
				foreach($ol2->arr() as $oid => $obj)
				{
					$ids[] = $obj->prop(($this->use_group === "relorg_s")?"seller":"buyer");
				}
				$ol2 = $ids;
				//$ol2 = $ol2->ids();
			}
			else
			{
				$ol2 = false;
			}
			// different for customer vs my co.
			$co = get_current_company();
			if ($arr["obj_inst"]->id() == $co->id())
			{
				$filt = $this->_get_customer_search_filter($arr["request"], $ol2);
				$ol = new object_list($filt);
				$orglist = $this->make_keys($ol->ids());
			}
			else
			{
				$filt = $this->_get_customer_search_filter($arr["request"], $ol2);
				$ol = new object_list($filt);
				$orglist = $this->make_keys($ol->ids());
			}

			if(!empty($arr["request"]["filt_p"]))
			{
				$filt["name"] = $arr["request"]["filt_p"].$filt["name"]."%";
			}

			unset($filt["sort_by"]);
			unset($filt["limit"]);

			$t = new object_data_list(
				$filt,
				array(
					CL_CRM_COMPANY =>  array(new obj_sql_func(OBJ_SQL_COUNT,"cnt" , "*"))
				)
			);
			$this->result_count = reset(reset($t->arr()));
		}

		$this->_finish_org_tbl($arr, $orglist);

		if (!empty($arr["request"]["customer_search_print_view"]))
		{
			$sf = new aw_template;
			$sf->db_init();
			$sf->tpl_init("automatweb");
			$sf->read_template("index.tpl");
			$sf->vars(array(
				"content"	=> $arr["prop"]["vcl_inst"]->draw(),
				"uid" => aw_global_get("uid"),
				"charset" => aw_global_get("charset")
			));
			die($sf->parse());
		}

	}

	function _get_my_customers_table($arr)
	{
		if(!empty($arr["request"]["customer_search_submit"]) || !empty($arr["request"]["customer_search_submit_and_change"]))
		{
			$this->_get_customer(&$arr);
		}
		else
		{
			$category = isset($arr['request']['category']) ? $arr['request']['category'] : "";
			$stchk = explode('_', $category);
			if($stchk[0] === 'st')
			{
				$arr["st"] = $stchk[1];
				$status = obj($stchk[1]);
				$all_cust_data = new object_list(array(
					"seller" => $arr['obj_inst']->id(),
					"class_id" => array(CL_CRM_COMPANY_CUSTOMER_DATA)
				));
				$orglist = array();
				foreach($all_cust_data->list as $cd)
				{
					$cust_data = obj($cd);
					$cust_st = $cust_data->connections_from(array(
						"type" => RELTYPE_STATUS,
						"to" => $status
					));
					if(count($cust_st))
					{
						$orglist[$cust_data->prop("buyer")] = $cust_data->prop("buyer");
					}
				}
			}
			else
			{
				//will list the companys from the category
				//if category is selected
				$customer_relations_search = new crm_sales_contacts_search();

				if ("relorg_s" === $this->use_group)
				{ // list sellers
					$customer_relations_search->buyer = $arr['obj_inst'];
				}
				elseif ("relorg_b" === $this->use_group)
				{ // list buyers
					$customer_relations_search->seller = $arr['obj_inst'];
				}
				else
				{
				}

				if (!empty($arr["request"]["filt_p"]))
				{
					$customer_relations_search->name = $arr["request"]["filt_p"] . "%";
				}

				try
				{
					$category = obj($category, array(), CL_CRM_CATEGORY);
					$customer_relations_search->category = $category;
				}
				catch (Exception $e)
				{
					if (!empty($category))
					{
						$this->show_error_text(t("Kategooria parameeter ei vasta n&otilde;uetele"));
					}
				}

				$cro_oids = $customer_relations_search->get_customer_relation_oids(new obj_predicate_limit(50)/* //!!!! tmp */);

				foreach($cro_oids as $key => $cro_oid_data)
				{
					$cro = obj($cro_oid_data["oid"]);
					$customer_oid = $cro->prop("buyer");
					$orglist[$customer_oid] = $customer_oid;
				}
			}
			$this->_finish_org_tbl($arr, $orglist);
		}
	}

	function _do_cust_cat_tb_submenus(&$tb, $link, $p, $p_str, $oncl = null)
	{
		$cnt = 0;
		foreach($p->connections_from(array("type" => "RELTYPE_CATEGORY")) as $c)
		{
			$cnt++;
			$name = $p_str."_".$c->prop("to");
			if ($this->_do_cust_cat_tb_submenus($tb, $link, $c->to(), $name, $oncl) > 0)
			{
				$tb->add_sub_menu(array(
					'parent'=> $p_str,
					"name" => $name,
					'text' => $c->prop("to.name"),
				));
			}
			else
			{
				$parm = array(
					'parent'=>$p_str,
					'text' => $c->prop("to.name"),
					'link' => str_replace(urlencode("%s"), $c->prop("to"), str_replace("%s", $c->prop("to"), $link))
				);
				if ($oncl !== NULL)
				{
					$parm["onClick"] = str_replace(urlencode("%s"), $c->prop("to"), str_replace("%s", $c->prop("to"), $oncl));
					$parm["link"] = "#";
				}
				$tb->add_menu_item($parm);
			}
		}
		return $cnt;
	}

	function _do_cust_cat_tb_submenus2(&$tb)
	{

		$st = get_instance(CL_CRM_COMPANY_STATUS);
		$categories = $st->categories(0);
		$company = get_current_company();
		$link = "document.changeform.elements.cust_cat.value='%s';submit_changeform('save_as_customer')";
		foreach($categories as $id=>$cat)
		{
			$ol = new object_list(array(
				"class_id" => array(CL_CRM_COMPANY_STATUS),
				"category" => $id,
				"parent" => $company->id()

			));
			if(count($ol->ids()))
			{
				$tb->add_sub_menu(array(
					'parent'=> "save_as_cust",
					"name" => $id,
					'text' => $cat,
				));


				foreach($ol->arr() as $o)
				{
					$linkn = str_replace(urlencode("%s"), "status_".$o->id(), str_replace("%s", 'status_'.$o->id(), $link));
					if($this->_do_cust_cat_tb_submenus3($tb, $o->id()))
					{
						$tb->add_sub_menu(array(
							'parent'=> $id,
							"name" => $o->id(),
							'text' => $o->name(),
							'onClick' => $linkn,
							"link" =>"#"
						));
					}
					else
					{
						$tb->add_menu_item(array(
							"parent" => $id,
							"name" => $o->id(),
							"text" => $o->name(),
							'onClick' => $linkn,
							"link" =>"#"
						));
					}
				}
			}
		}
	}

	function _do_cust_cat_tb_submenus3(&$tb, $p)
	{
		$link = "document.changeform.elements.cust_cat.value='%s';submit_changeform('save_as_customer')";
		$ol = new object_list(array(
			"class_id" => array(CL_CRM_COMPANY_STATUS),
			"parent" => $p
		));
		if(count($ol->list))
		{
			foreach($ol->arr() as $o)
			{
				$linkn = str_replace(urlencode("%s"), "status_".$o->id(), str_replace("%s", 'status_'.$o->id(), $link));
				if($this->_do_cust_cat_tb_submenus3($tb, $o->id()))
				{
					$tb->add_sub_menu(array(
						'parent'=> $id,
						"name" => $o->id(),
						'text' => $o->name(),
						"onClick" => $linkn,
						"link" =>"#"
					));
				}
				else
				{
					$tb->add_menu_item(array(
						"parent" => $p,
						"name" => $o->id(),
						"text" => $o->name(),
						"onClick" => $linkn,
						"link" =>"#"
					));
				}
			}
			return 1;
		}
		else
		{
			return 0;
		}
	}

	/**
		@attrib name=get_offers_tree_branch all_args=1
	**/
	function get_offers_tree_branch($arr)
	{
		$tr = get_instance("vcl/treeview");
		$this->_get_offers_listing_tree(array(
			"prop" => array(
				"vcl_inst" => &$tr,
			),
			"obj_inst" => obj($arr["co_id"]),
			"node_id" => $arr["fetch_branch"]
		));
	}

	function _get_customer_search_cust_grp($arr)
	{
		$dat = array();
		$this->_req_get_sects($arr["obj_inst"], $dat);
		$arr["prop"]["options"] = array("" => "") + $dat;
		$arr["prop"]["value"] = $arr["request"]["customer_search_cust_grp"];
	}

	function _get_customer_search_insurance_exp($arr)
	{
		$dat = array();
		$ol = new object_list(array(
			"class_id" => CL_CRM_INSURANCE_TYPE,
			"site_id" => array(),
			"lang_id" => array()
		));
		$dat = $ol->names();
		$arr["prop"]["options"] = array("" => "") + $dat;
		$arr["prop"]["value"] = $arr["request"]["customer_search_insurance_exp"];
	}

	function _req_get_sects($o, &$dat)
	{
		$this->_sect_l ++;
		foreach($o->connections_from(array("type" => "RELTYPE_CATEGORY")) as $c)
		{
			$dat[$c->prop("to")] = str_repeat("&nbsp;&nbsp;&nbsp;", $this->_sect_l) . $c->prop("to.name");
			$this->_req_get_sects($c->to(), $dat);
		}
		$this->_sect_l --;
	}

	/**
		@attrib name=get_cust_contact_table
		@param id required type=int acl=view
		@param return_url optional
	**/
	function get_cust_contact_table($arr)
	{
		$o = obj($arr["id"]);
		// get employees for that company. if any are important, return those, if not, return all
		$hr = get_instance("applications/crm/crm_company_people_impl");

		classload("vcl/table");
		$t = new vcl_table();

		$p = array(
			"vcl_inst" => &$t,
		);
		$hr->_get_human_resources(array(
			"obj_inst" => $o,
			"prop" => &$p,
			"caller_ru" => $arr["return_url"],
			"request" => array(
				"all_if_empty" => true
			)
		));

		$t->sort_by();
		die(iconv(aw_global_get("charset"), "utf-8", $t->draw()));
	}

	/**
		@attrib name=remove_from_cust_grp
		@param id required
		@param cgrp required
		@param post_ru required
	**/
	function remove_from_cust_grp($arr)
	{
		$cg = obj($arr["cgrp"]);
		$cg->disconnect(array("from" => $arr["id"]));
		return $arr["post_ru"];
	}


	private function all_projects_data($filt)
	{
		$filter = array(
			"class_id" => CL_PROJECT,
			"site_id" => array(),
			"lang_id" => array(),
		);

		if(is_oid($filt))
		{
			$filter["proj_mgr"] = $filt;
		}
/*		elseif($filt == "period_last")
		{
			$filter["bill_date"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, mktime(0,0,0, date("m")-1, 0, date("Y")), mktime(0,0,0, date("m"), 0, date("Y")));
		}
		elseif($filt == "period_current")
		{
			$filter["bill_date"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, mktime(0,0,0, date("m"), 0, date("Y")), mktime(0,0,0, date("m")+1, 0, date("Y")));
		}
		elseif($filt == "period_next")
		{
			$filter["bill_date"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, mktime(0,0,0, date("m")+1, 0, date("Y")), mktime(0,0,0, date("m")+2, 0, date("Y")));
		}
// */
		$t = new object_data_list(
			$filter,
			array(
				CL_PROJECT => array(
					"state"
				),
			)
		);
		return $t->list_data;
	}

	private function all_project_customers()
	{
		$filter = array(
			"class_id" => CL_PROJECT,
			"CL_PROJECT.RELTYPE_ORDERER" =>  new obj_predicate_compare(OBJ_COMP_GREATER, 0),
		);

		$t = new object_data_list(
			$filter,
			array(
				CL_PROJECT => array(new obj_sql_func(OBJ_SQL_UNIQUE, "orderer", "aliases___129_9.target"))
			)
		);

		$filter2 = array(
			"class_id" => CL_CRM_PARTY,
			"CL_CRM_PARTY.participant.class_id" => CL_PROJECT,
		);
		$t2 = new object_data_list(
			$filter2,
			array(
				CL_CRM_PARTY => array(new obj_sql_func(OBJ_SQL_UNIQUE, "participant", "objects_1490_participant.oid"))
			)
		);

		$ol = new object_list();
		$ol->add($t->get_element_from_all("orderer"));
		$ol->add($t2->get_element_from_all("participant"));
		return $ol;
	}

	function _get_project_tree($arr)
	{
		$tv =& $arr["prop"]["vcl_inst"];
		$var = "pf";
		$bills_impl = get_instance("applications/crm/crm_company_bills_impl");
		$project_impl = get_instance(CL_PROJECT);
/*		if(!isset($_GET[$var]))
		{
			$_GET[$var] = 10;
		}
		classload("core/icons");
*/
		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"persist_state" => true,
			"tree_id" => "company_projects_tree",
		));

		$tv->add_item(0,array(
			"name" => t("Projektijuht"),
			"id" => "pr_mgr",
//			"url" => aw_url_change_var($var, $stat_id+10),
		));

//$this->states






		foreach($bills_impl->all_project_managers()->names() as $id => $name)
		{
			if(!$name)
			{
				continue;
			}

			$project_data = $this->all_projects_data($id);

			if(sizeof($project_data))
			{
				$name = $name." (".sizeof($project_data).")";
			}
			if (isset($_GET[$var]) && $_GET[$var] == "prman_".$id)
			{
				$name = "<b>".$name."</b>";
			}

			$tv->add_item("pr_mgr",array(
				"name" => $name,
				"id" => "prman".$id,
				"iconurl" => icons::get_icon_url(CL_CRM_PERSON),
				"url" => aw_url_change_var($var, "prman_".$id),
			));
		}

		$tv->add_item(0,array(
			"name" => t("Kliendihaldur"),
			"id" => "cust_mgr",
//			"url" => aw_url_change_var($var, $stat_id+10),
		));

		foreach($bills_impl->all_client_managers()->names() as $id => $name)
		{
			if(!$name)
			{
				continue;
			}
			if (isset($_GET[$var]) && $_GET[$var] == "custman_".$id)
			{
				$name = "<b>".$name."</b>";
			}
			$tv->add_item("cust_mgr",array(
				"name" => $name,
				"id" => "custman".$id,
				"iconurl" => icons::get_icon_url(CL_CRM_PERSON),
				"url" => aw_url_change_var($var, "custman_".$id),
			));

		}

		$tv->add_item(0,array(
			"name" => t("Klient"),
			"id" => "cust",
//			"url" => aw_url_change_var($var, $stat_id+10),
		));
		$customers_by_1_letter = array();
		$customer_names = $this->all_project_customers()->names();
		asort($customer_names);
		foreach($customer_names as $customer_id => $customer_name)
		{
			if(!$customer_name)
			{
				continue;
			}
			$customers_by_1_letter[substr($customer_name,0,1)][$customer_id] = $customer_name;
		}

		foreach($customers_by_1_letter as $letter1 => $customers)
		{
			$name = $letter1 ." (".sizeof($customers).")";
			if (isset($_GET[$var]) && $_GET[$var] == "cust_".$letter1)
			{
				$name = "<b>".$name."</b>";
			}
			$tv->add_item("cust",array(
				"name" => $name,
				"id" => "cust".$letter1,
			//	"iconurl" => icons::get_icon_url(CL_CRM_COMPANY),
				"url" => aw_url_change_var($var, "cust_".$letter1),
			));

			foreach($customers as $id => $name)
			{
				if (isset($_GET[$var]) && $_GET[$var] == "cust_".$id)
				{
					$name = "<b>".$name."</b>";
				}
				$tv->add_item("cust".$letter1,array(
					"name" => $name,
					"id" => "cust".$id,
					"iconurl" => icons::get_icon_url(CL_CRM_COMPANY),
					"url" => aw_url_change_var($var, "cust_".$id),
				));
			}
		}
/*
		$tv->add_item(0,array(
			"name" => t("Periood"),
			"id" => "period",
//			"url" => aw_url_change_var($var, $stat_id+10),
		));

		$state = t("Eelmine kuu");
 		$bills_data = $this->all_bills_data("period_last");
 		$pm_statuses = array();
 		foreach($bills_data as $bd)
 		{
 			$pm_statuses[$bd["state"]] ++;
 		}
 		if(array_sum($pm_statuses))
 		{
 			$state = $state." (".array_sum($pm_statuses).")";
 		}
		if (isset($_GET[$var]) && $_GET[$var] == "period_last") $state = "<b>".$state."</b>";
		$tv->add_item("period",array(
			"name" => $state,
			"id" => "period_last",
			"url" => aw_url_change_var($var, "period_last"),
		));
 		if(sizeof($pm_statuses))
 		{
 			foreach($pm_statuses as $status => $st_count)
 			{
 				$name = $states[$status]." (".$st_count.")";
 				if (isset($_GET[$var]) && $_GET[$var] == "period_last_".$status)
 				{
 					$name = "<b>".$name."</b>";
 				}
 				$tv->add_item("period_last",array(
 					"name" => $name,
 					"id" => "period_last_".$status,
 					"iconurl" => icons::get_icon_url(CL_CRM_BILL),
 					"url" => aw_url_change_var($var, "period_last_".$status),
 				));
 			}
 		}

		$state = t("Jooksev kuu");
		$bills_data = $this->all_bills_data("period_current");
 		$pm_statuses = array();
 		foreach($bills_data as $bd)
 		{
 			$pm_statuses[$bd["state"]] ++;
 		}
 		if(array_sum($pm_statuses))
 		{
 			$state = $state." (".array_sum($pm_statuses).")";
 		}
		if (isset($_GET[$var]) && $_GET[$var] == "period_current") $state = "<b>".$state."</b>";
		$tv->add_item("period",array(
			"name" => $state,
			"id" => "period_current",
			"url" => aw_url_change_var($var, "period_current"),
		));
 		if(sizeof($pm_statuses))
 		{
 			foreach($pm_statuses as $status => $st_count)
 			{
 				$name = $states[$status]." (".$st_count.")";
 				if (isset($_GET[$var]) && $_GET[$var] == "period_current_".$status)
 				{
 					$name = "<b>".$name."</b>";
 				}
 				$tv->add_item("period_current",array(
 					"name" => $name,
 					"id" => "period_current_".$status,
 					"iconurl" => icons::get_icon_url(CL_CRM_BILL),
 					"url" => aw_url_change_var($var, "period_current_".$status),
 				));
 			}
 		}


		$state = t("J&auml;rgmine kuu");
		$bills_data = $this->all_bills_data("period_next");
 		$pm_statuses = array();
 		foreach($bills_data as $bd)
 		{
 			$pm_statuses[$bd["state"]] ++;
 		}
 		if(array_sum($pm_statuses))
 		{
 			$state = $state." (".array_sum($pm_statuses).")";
 		}
		if (isset($_GET[$var]) && $_GET[$var] == "period_next") $state = "<b>".$state."</b>";
		$tv->add_item("period",array(
			"name" => $state,
			"id" => "period_next",
			"url" => aw_url_change_var($var, "period_next"),
		));
 		if(sizeof($pm_statuses))
 		{
 			foreach($pm_statuses as $status => $st_count)
 			{
 				$name = $states[$status]." (".$st_count.")";
 				if (isset($_GET[$var]) && $_GET[$var] == "period_next_".$status)
 				{
 					$name = "<b>".$name."</b>";
 				}
 				$tv->add_item("period_next",array(
 					"name" => $name,
 					"id" => "period_next_".$status,
 					"iconurl" => icons::get_icon_url(CL_CRM_BILL),
 					"url" => aw_url_change_var($var, "period_next_".$status),
 				));
 			}
 		}*/
	}
}
?>
