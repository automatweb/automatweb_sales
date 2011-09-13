<?php

class crm_role_manager extends class_base
{
	function crm_role_manager()
	{
		$this->init("crm/crm_role_manager");
	}

	/**

		@attrib name=change

		@param from_org required type=int acl=view
		@param to_org optional type=int acl=view
		@param to_project optional type=int acl=view

		@param unit optional
		@param cat optional
		@param list optional

	**/
	function change($arr)
	{
		if ($arr["list"])
		{
			// list with selected persons, toolbar with delete button
			$this->read_template("show_list.tpl");
			$this->_get_list($arr);
			$this->_get_list_tb($arr);

			$this->vars(array(
				"reforb" => $this->mk_reforb("submit_list", $arr)
			));
		}
		else
		{
			// tree on left with org structure of from_org
			// table on right with persons
			$this->read_template("show.tpl");
			$this->_get_tree($arr["from_org"], $arr);

			$this->_get_table($arr["from_org"], $arr);

			$this->_get_tb($arr["from_org"], $arr);

			$this->vars(array(
				"reforb" => $this->mk_reforb("submit", $arr)
			));
		}

		return $this->_get_tabs($arr["from_org"], $arr);
	}

	function _get_tree($org, $r)
	{
		$t = get_instance("vcl/treeview");
		$t->start_tree(array(
			"type" => TREE_DHTML,
			"tree_id" => "crm_rlmgr",
			"persist_state" => 1
		));

		$node_id = 0;

		$c = get_instance(CL_CRM_COMPANY);
		$c->active_node = $r["unit"];
		if(is_oid($r['cat']))
		{
			$c->active_node = $arr['request']['cat'];
		}
		$c->generate_tree(array(
			'tree_inst' => &$t,
			'obj_inst' => obj($org),
			'node_id' => &$node_id,
			'conn_type' => 'RELTYPE_SECTION',
			'attrib' => 'unit',
			'leafs' => true,
		));

		$this->vars(array(
			"tree" => $t->finalize_tree()
		));
	}

	function _get_table($org, $r)
	{
		
		$t = new aw_table();

		$filt = array(
			"company" => $r["from_org"],
			"class_id" => CL_CRM_COMPANY_ROLE_ENTRY
		);

		if (!$r["cat"] && !$r["unit"])
		{
			return;
		}
		if ($r["to_project"])
		{
			// get to org from project as orderer
			$proj = obj($r["to_project"]);
			$o = $proj->get_first_obj_by_reltype("RELTYPE_ORDERER");

			$filt["client"] = $o->id();

			$persons = array();
			$rels = array();
			/*
			$ol = new object_list($filt);
			foreach($ol->arr() as $tmp_o)
			{
				$persons_f[$tmp_o->prop("person")] = $tmp_o->prop("person");
			}*/
			// persons_f must contain all project team members
			$pi = get_instance(CL_PROJECT);
			$persons_f = $pi->get_team($proj);


			$filt["project"] = $r["to_project"];
			$ol = new object_list($filt);
			foreach($ol->arr() as $tmp_o)
			{
				$persons[$tmp_o->prop("person")] = $tmp_o->prop("person");
				$rels[$tmp_o->prop("person")] = $tmp_o->id();
			}
		}
		else
		{
			$persons = array();
			$rels = array();
			$ol = new object_list($filt);
			foreach($ol->arr() as $tmp_o)
			{
				$persons[$tmp_o->prop("person")] = $tmp_o->prop("person");
				$rels[$tmp_o->prop("person")] = $tmp_o->id();
			}
		}

		$t->define_field(array(
			"name" => "check",
			"caption" => t("Vali roll"),
			"align" => "center"
		));

		$c = get_instance("applications/crm/crm_company_people_impl");
		$c->_get_human_resources(array(
			"prop" => array(
				"vcl_inst" => &$t
			),
			"request" => $r,
			"obj_inst" => obj($org),
			"person_filter" => $r["to_project"] ? $persons_f : NULL
		));

		// re-arrange the table a bit
		$t->remove_chooser();

		// add column
		$dat = array();
		foreach($t->get_data() as $idx => $row)
		{
			$row["check"] = html::checkbox(array(
				"name" => "check[]",
				"value" => $row["id"],
				"checked" => isset($persons[$row["id"]])
			)).html::hidden(array(
				"name" => "rels[".$row["id"]."]",
				"value" => $rels[$row["id"]]
			)).html::hidden(array(
				"name" => "r2p[".$rels[$row["id"]]."]",
				"value" => $row["id"]
			));
			$t->set_data($idx, $row);
		}

		$this->vars(array(
			"table" => $t->draw()
		));
	}

	function _get_tb($org, $r)
	{
		$t = get_instance("vcl/toolbar");
		$t->add_button(array(
			"name" => "save",
			"tooltip" => t("Anna rollid"),
			"img" => "save.gif",
			"onClick" => "document.rolf.submit()",
			"url" => "#"
		));

		$this->vars(array(
			"toolbar" => $t->get_toolbar()
		));
	}

	/** saves the roles for company=>client=>role

		@attrib name=submit

	**/
	function submit($arr)
	{
		// find all assigned role entries from co to client
		$filt = array(
			"company" => $arr["from_org"],
			"class_id" => CL_CRM_COMPANY_ROLE_ENTRY
		);

		if ($arr["to_project"])
		{
			$filt["project"] = $arr["to_project"];
		}
		else
		{
			$filt["client"] = $arr["to_org"];
		}

		if (is_oid($arr["unit"]))
		{
			$filt["unit"] = $arr["unit"];
		}

		if (is_oid($arr["cat"]))
		{
			$filt["role"] = $arr["cat"];
		}

		$ol = new object_list($filt);

		$names = $ol->names();
		//die(dbg::dump($names).dbg::dump($arr));
		// now, if any of the selected persons were not present, add them
		// the objects will be added under the client org
		foreach(safe_array($arr["check"]) as $p_id)
		{
			if (!isset($names[$arr["rels"][$p_id]]))
			{
				$o = obj();
				$o->set_parent(is_oid($arr["to_project"]) ? $arr["to_project"] : $arr["to_org"]);
				$o->set_class_id(CL_CRM_COMPANY_ROLE_ENTRY);
				$o->set_prop("person", $p_id);
				$o->set_prop("role", $arr["cat"]);
				$o->set_prop("company", $arr["from_org"]);
				$o->set_prop("client", $arr["to_org"]);
				$o->set_prop("unit", $arr["unit"]);
				$o->set_prop("project", $arr["to_project"]);
				$o->save();
			}
		}

		$ck = safe_array($arr["check"]);
		// remove the ones that are in persons and not in check
		foreach(safe_array($arr["rels"]) as $p_id)
		{
			if (!is_oid($p_id))
			{
				continue;
			}
			$o_id = $arr["r2p"][$p_id];
			if (!in_array($o_id, $ck))
			{
				$o = obj($p_id);
				$o->delete();
			}
		}

		return $this->mk_my_orb("change", array(
			"from_org" => $arr["from_org"],
			"to_org" => $arr["to_org"],
			"unit" => $arr["unit"],
			"cat" => $arr["cat"],
			"to_project" => $arr["to_project"]
		));
	}

	function _get_tabs($org, $r)
	{
		$t = get_instance("vcl/tabpanel");

		$t->add_tab(array(
			"active" => !$r["list"],
			"caption" => t("Vali"),
			"link" => aw_url_change_var("list", NULL)
		));

		$t->add_tab(array(
			"active" => $r["list"],
			"caption" => t("Halda"),
			"link" => aw_url_change_var("list", 1)
		));

		return $t->get_tabpanel(array(
			"content" => $this->parse()
		));
	}

	function _get_list_tb($r)
	{
		$t = get_instance("vcl/toolbar");
		$t->add_button(array(
			"name" => "delete",
			"tooltip" => t("Kustuta"),
			"img" => "delete.gif",
			"url" => "javascript:rolf.submit()"
		));

		$this->vars(array(
			"toolbar" => $t->get_toolbar()
		));
	}

	function _init_list_t(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "phone",
			"caption" => t("Telefon"),
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "email",
			"caption" => t("E-post"),
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "unit",
			"caption" => t("&Uuml;ksus"),
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "role",
			"caption" => t("Ametinimetus"),
			"sortable" => 1
		));

		$t->define_chooser(array(
			"field" => "id",
			"name" => "check"
		));
	}

	function _get_list($r)
	{
		
		$t = new aw_table();
		$this->_init_list_t($t);

		$filt = array(
			"class_id" => CL_CRM_COMPANY_ROLE_ENTRY,
			"company" => $r["from_org"],
		);

		if ($r["to_org"])
		{
			$filt["client"] = $r["to_org"];
		}
		else
		{
			$filt["project"] = $r["to_project"];
		}

		$ol = new object_list($filt);
		foreach($ol->arr() as $o)
		{
			$p = obj($o->prop("person"));

			$eml = "";
			if (is_oid($p->prop("email")) && $this->can("view", $p->prop("email")))
			{
				$emo = obj($p->prop("email"));
				$eml = $emo->prop("mail");
			}

			$t->define_data(array(
				"name" => $p->name(),
				"phone" => $p->prop_str("phone"),
				"email" => $eml,
				"unit" => $o->prop_str("unit"),
				"role" => $o->prop_str("role"),
				"id" => $o->id()
			));
		}

		$this->vars(array(
			"table" => $t->draw()
		));
	}

	/**

		@attrib name=submit_list

	**/
	function submit_list($arr)
	{
		foreach(safe_array($arr["check"]) as $oid)
		{
			$o = obj($oid);
			$o->delete();
		}
		return $this->mk_my_orb("change", array(
			"from_org" => $arr["from_org"],
			"to_org" => $arr["to_org"],
			"list" => 1,
			"to_project" => $arr["to_project"]
		));
	}
}
