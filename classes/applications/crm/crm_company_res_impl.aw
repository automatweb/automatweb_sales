<?php
/*
@classinfo maintainer=markop
*/
class crm_company_res_impl extends class_base
{
	function crm_company_res_impl()
	{
		$this->init();
	}

	function _get_res_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];

		$tb->add_menu_button(array(
			'name'=>'add_item',
			'tooltip'=> t('Uus')
		));

		$parent = is_oid($arr["request"]["tf"]) ? $arr["request"]["tf"] : $this->_get_res_parent($arr["obj_inst"]);
		$tb->add_menu_item(array(
			'parent'=>'add_item',
			'text' => t('Kategooria'),
			"url" => html::get_new_url(CL_MENU, $parent, array("return_url" => get_ru()))
		));
		
		$tb->add_menu_item(array(
			'parent'=>'add_item',
			'text' => t('Ressurss'),
			"url" => html::get_new_url(
				CL_MRP_RESOURCE, 
				$parent, 
				array(
					"return_url" => get_ru(),
					"mrp_workspace" => $this->_get_res_mgr($arr["obj_inst"]),
					"mrp_parent" => $parent
				)
			)
		));

		$tb->add_separator();

		$tb->add_button(array(
			'name' => 'res_cut',
			'img' => 'cut.gif',
			'tooltip' => t('L&otilde;ika'),
			'action' => 'res_cut',
		));

		if (is_array($_SESSION["co_res_cut"]) && count($_SESSION["co_res_cut"]))
		{
			$tb->add_button(array(
				'name' => 'res_paste',
				'img' => 'paste.gif',
				'tooltip' => t('Kleebi'),
				'action' => 'res_paste',
			));
		}

		$tb->add_separator();

		$tb->add_button(array(
			'name' => 'res_delete',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta'),
			'action' => 'res_delete',
		));
	}

	function _get_res_tree($arr)
	{
		$ri = obj($this->_get_res_parent($arr["obj_inst"]));
		classload("core/icons");
		$arr["prop"]["vcl_inst"] = treeview::tree_from_objects(array(
			"tree_opts" => array(
				"type" => TREE_DHTML, 
				"persist_state" => true,
				"tree_id" => "co_res_t",
			),
			"root_item" => $ri,
			"ot" => new object_tree(array(
				"class_id" => CL_MENU,
				"parent" => $ri,
			)),
			"var" => "tf",
			"icon" => icons::get_icon_url(CL_MENU)
		));
	}

	function _init_res_t(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "cal",
			"caption" => t("Kalender"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "createdby",
			"caption" => t("Looja"),
			"sortable" => 1,
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "created",
			"caption" => t("Loodud"),
			"sortable" => 1,
			"align" => "center",
			"format" => "d.m.Y H:i",
			"type" => "time",
			"numeric" => 1
		));

		$t->define_field(array(
			"name" => "modifiedby",
			"caption" => t("Muutja"),
			"sortable" => 1,
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "modified",
			"caption" => t("Muudetud"),
			"sortable" => 1,
			"align" => "center",
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y H:i"
		));

		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));
	}

	function _get_res_tbl($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_res_t($t);

		$format = t('%s ressursid');
		$t->set_caption(sprintf($format, $arr['obj_inst']->name()));

		$parent = is_oid($arr["request"]["tf"]) ? $arr["request"]["tf"] : $this->_get_res_parent($arr["obj_inst"]);

		$ol = new object_list(array(
			"class_id" => CL_MRP_RESOURCE,
			"parent" => $parent
		));
		//$t->data_from_ol($ol, array("change_col" => "name"));
		foreach($ol->arr() as $o)
		{
			$t->define_data(array(
				"name" => html::obj_change_url($o),
				"cal" => html::get_change_url($o->id(), array("group" => "grp_resource_schedule", "return_url" => get_ru()), t("Kalender")),
				"createdby" => $o->createdby(),
				"created" => $o->created(),
				"modifiedby" => $o->modifiedby(),
				"modified" => $o->modified(),
				"oid" => $o->id()
			));
		}
	}

	function _get_res_parent($co)
	{
		$o = $co->get_first_obj_by_reltype("RELTYPE_RESOURCES_FOLDER");
		if (!$o)
		{
			$o = obj();
			$o->set_class_id(CL_MENU);
			$o->set_parent($co->id());
			$o->set_name(sprintf(t("%s ressursid"), $co->name()));
			$o->save();
			$co->connect(array(
				"to" => $o->id(),
				"type" => "RELTYPE_RESOURCES_FOLDER"
			));
		}
		return $o->id();
	}

	function _get_res_mgr($co)
	{
		$o = $co->get_first_obj_by_reltype("RELTYPE_RESOURCE_MGR");
		if (!$o)
		{
			$o = obj();
			$o->set_class_id(CL_MRP_WORKSPACE);
			$o->set_parent($co->id());
			$o->set_name(sprintf(t("%s ressursihalduskeskkond"), $co->name()));
			$o->save();
			$co->connect(array(
				"to" => $o->id(),
				"type" => "RELTYPE_RESOURCE_MGR"
			));
		}
		return $o->id();
	}
}
?>
