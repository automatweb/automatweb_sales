<?php
/*
@classinfo maintainer=markop
*/
class project_req_impl extends class_base
{
	function project_req_impl($arr)
	{	
		$this->init();
	}

	function _get_req_tb($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$t->add_menu_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Lisa")
		));

		$t->add_menu_item(array(
			"name" => "new_task",
			"parent" => "new",
			"link" => html::get_new_url(
				CL_TASK, 
				is_oid($arr["request"]["tf"]) ? $arr["request"]["tf"] : $arr["obj_inst"]->id(), 
				array(
					"return_url" => get_ru(),
					"alias_to_org" => $arr["obj_inst"]->prop("orderer"),
					"set_proj" => $arr["obj_inst"]->id()
				)
			),
			"text" => t("Toimetus"),
		));

		$t->add_menu_item(array(
			"name" => "new_call",
			"parent" => "new",
			"link" => html::get_new_url(
				CL_CRM_CALL, 
				is_oid($arr["request"]["tf"]) ? $arr["request"]["tf"] : $arr["obj_inst"]->id(), 
				array(
					"return_url" => get_ru(),
					"alias_to_org" => $arr["obj_inst"]->prop("orderer"),
					"set_proj" => $arr["obj_inst"]->id()
				)
			),
			"text" => t("K&otilde;ne"),
		));

		$t->add_menu_item(array(
			"name" => "new_call",
			"parent" => "new",
			"link" => html::get_new_url(
				CL_CRM_MEETING, 
				is_oid($arr["request"]["tf"]) ? $arr["request"]["tf"] : $arr["obj_inst"]->id(), 
				array(
					"return_url" => get_ru(),
					"alias_to_org" => $arr["obj_inst"]->prop("orderer"),
					"set_proj" => $arr["obj_inst"]->id()
				)
			),
			"text" => t("Kohtumine"),
		));

		$t->add_menu_item(array(
			"name" => "new_bug",
			"parent" => "new",
			"link" => html::get_new_url(
				CL_BUG, 
				is_oid($arr["request"]["tf"]) ? $arr["request"]["tf"] : $arr["obj_inst"]->id(), 
				array(
					"return_url" => get_ru(),
					"alias_to_org" => $arr["obj_inst"]->prop("orderer"),
					"set_proj" => $arr["obj_inst"]->id()
				)
			),
			"text" => t("Arendus&uuml;lesanne"),
		));

		$proc = $this->get_proc($arr["obj_inst"]);
		$t->add_menu_item(array(
			"name" => "new_req",
			"parent" => "new",
			"link" => html::get_new_url(
				CL_PROCUREMENT_REQUIREMENT, 
				is_oid($arr["request"]["tf"]) ? $arr["request"]["tf"] : ($proc ? $proc : $arr["obj_inst"]->id()), 
				array(
					"return_url" => get_ru(),
					"alias_to_org" => $arr["obj_inst"]->prop("orderer"),
					"set_proj" => $arr["obj_inst"]->id()
				)
			),
			"text" => t("N&otilde;ue"),
		));

		$t->add_menu_item(array(
			"name" => "new_cat",
			"parent" => "new",
			"link" => html::get_new_url(
				CL_MENU, 
				is_oid($arr["request"]["tf"]) ? $arr["request"]["tf"] : $arr["obj_inst"]->id(), 
				array(
					"return_url" => get_ru(),
					"alias_to_org" => $arr["obj_inst"]->prop("orderer"),
					"set_proj" => $arr["obj_inst"]->id()
				)
			),
			"text" => t("Kategooria"),
		));

		$t->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"action" => "del_goals",
			"tooltip" => t("Kustuta"),
		));
		
		$t->add_separator();
		$t->add_button(array(
			"name" => "export",
			"tooltip" => t("Ekspordi"),
			"img" => "export.gif",
			"action" => "export_req",
		));
	}

	function _get_req_tree_process($arr)
	{
		$proc = $this->get_proc($arr["obj_inst"]);
		if (!$proc)
		{
			$proc = $arr["obj_inst"]->id();
		}
		
		// get all procurements and list them in the tree by process
		$ot = new object_tree(array(
			"class_id" => array(CL_MENU, CL_PROCUREMENT_REQUIREMENT),
			"parent" => $proc,
			"lang_id" => array(),
			"site_id" => array()
		));
		$processes = array();
		$ol = $ot->to_list();
		foreach($ol->arr() as $o)
		{
			$processes[$o->prop("process")]++;
		}

		$t =& $arr["prop"]["vcl_inst"];
		foreach($processes as $proc => $cnt)
		{
			if (!is_oid($proc))
			{
				continue;
			}
			$po = obj($proc);
			$nm = $po->name()." ($cnt)";
			if ($arr["request"]["proc"] == $proc)
			{
				$nm = "<b>".$nm."</b>";
			}
			$t->add_item(0, array(
				"id" => "p_".$proc,
				"parent" => 0,
				"name" => $nm,
				"url" => aw_url_change_var(array(
					"proc" => $proc,
					"empl" => null,
				))
			));
		}
	}

	function _get_req_tree($arr)
	{
		if ($arr["request"]["group"] == "req_process")
		{
			return $this->_get_req_tree_process($arr);
		}
		$proc = $this->get_proc($arr["obj_inst"]);
		if (!$proc)
		{
			$proc = $arr["obj_inst"]->id();
		}
		
		$arr["prop"]["vcl_inst"] = treeview::tree_from_objects(array(
			"tree_opts" => array(
				"type" => TREE_DHTML, 
				"persist_state" => true,
				"tree_id" => "procurement_center",
			),
			"root_item" => obj($proc),
			"ot" => new object_tree(array(
				"class_id" => array(CL_MENU, CL_PROCUREMENT_REQUIREMENT),
				"parent" => $proc,
				"lang_id" => array(),
				"site_id" => array()
			)),
			"var" => "tf"
		));
	}

	function _init_req_tbl(&$t)
	{	
		$t->set_caption( "<b>". t("N&otilde;uded") . "</b>");
		$t->define_field(array(
			"name" => "icon",
			"width" => 1,
			"caption" => t(""),
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "createdby",
			"caption" => t("Looja"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "created",
			"caption" => t("Loodud"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
			"type" => "time",
			"format" => "d.m.Y H:i"
		));
		$t->define_field(array(
			"name" => "parts",
			"caption" => t("Osalejad"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	function _get_req_tbl($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_req_tbl($t);

		if (!$arr["request"]["tf"] && !$arr["request"]["proc"])
		{	
			return;
		}

		if ($arr["request"]["proc"])
		{
			$ol = new object_list(array(
				"process" => $arr["request"]["proc"],
				"class_id" => array(CL_PROCUREMENT_REQUIREMENT),
			));
		}
		else
		{
			$ol = new object_list(array(
				"parent" => $arr["request"]["tf"],
				"class_id" => array(CL_BUG,CL_TASK,CL_CRM_CALL,CL_CRM_MEETING, CL_PROCUREMENT_REQUIREMENT, CL_MENU)
			));
		}
		
		$u = get_instance(CL_USER);
		foreach($ol->arr() as $o)
		{
			$o = $o->get_original();
			$p = $u->get_person_for_uid($o->createdby());
			$parts = array();
			switch($o->class_id())
			{
				case CL_TASK:
					$conns = $o->connections_to(array(
						'type' => array(10, 8),//CRM_PERSON.RELTYPE_PERSON_TASK==10
					));
					foreach($conns as $conn)
					{
						$parts[] = html::obj_change_url($conn->from());
					}
					break;

				case CL_CRM_CALL:
					$conns = $o->connections_to(array(
						'type' => "RELTYPE_PERSON_CALL",
						"from.class_id" => CL_CRM_PERSON
					));
					foreach($conns as $conn)
					{
						$parts[] = html::obj_change_url($conn->from());
					}
					break;

				case CL_CRM_MEETING:
					$conns = $o->connections_to(array(
						'type' => "RELTYPE_PERSON_MEETING",
						"from.class_id" => CL_CRM_PERSON
					));
					foreach($conns as $conn)
					{
						$parts[] = html::obj_change_url($conn->from());
					}
					break;

				case CL_BUG:
					$parts[] = html::obj_change_url($o->prop("who"));
					foreach(safe_array($o->prop("monitors")) as $mon)
					{
						$parts[] = html::obj_change_url($mon);
					}
					break;
			}
			$t->define_data(array(
				"icon" => icons::get_icon($o),
				"name" => html::obj_change_url($o),
				"createdby" => $p->name(),
				"created" => $o->created(),
				"parts" => join(", ", $parts),
				"oid" => $o->id()
			));
		}
	}

	function get_proc($o)
	{
		static $lut;
		if (!is_array($lut))
		{
			$lut = array();
		}
		if (!isset($lut[$o->id()]))
		{
			$ol = new object_list(array(
				"class_id" => CL_PROCUREMENT,
				"proj" => $o->id()
			));
			if ($ol->count())
			{
				$p = $ol->begin();
			}
			else
			{
				$p = obj();
			}
			$lut[$o->id()] = $p->id();
		}
		return $lut[$o->id()];
	}
}
?>
