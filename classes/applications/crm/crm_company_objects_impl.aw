<?php
/*
@classinfo maintainer=markop
*/
class crm_company_objects_impl extends class_base
{
	function crm_company_objects_impl()
	{
		$this->init();
	}

	function _get_objects_listing_tree($arr)
	{
		
		$tree = &$arr["prop"]["vcl_inst"];
		$ot = new object_tree(array(
    		"parent" => $arr["obj_inst"]->id(),
    		"class_id" => CL_MENU
		));
		$ol = $ot->to_list();
		
		foreach ($ol->arr() as $obj)
		{
			if($obj->parent() == $arr["obj_inst"]->id())
			{
				$parent = 0;
			}
			else
			{
				$parent = $obj->parent();
			}
			$tree->add_item($parent, array(
				'id' => $obj->id(),
				'name' => $obj->id()==$arr["request"]["parent"]?"<b>".$obj->name()."</b>":$obj->name(),
				'iconurl' => icons::get_icon_url($obj->class_id()),
				'url' => aw_url_change_var(array('parent' => $obj->id())),
			));
		}
	}
	
	function _get_objects_listing_toolbar($arr)
	{
		$tb = & $arr["prop"]["toolbar"];
				
		$tb->add_menu_button(array(
			'name'=>'add_item',
			'tooltip'=>t('Uus')
		));
				
		//Add classes
		foreach ((aw_ini_get("classes")) as $class_id => $classinfo)
		{
			$parents = split(",",$classinfo["parents"]);
			$newparent = $arr["request"]["parent"]? $arr["request"]["parent"]:$arr["obj_inst"]->id();
			
			if(count($parent) == 0)
			{
				$parents[] = "add_item";
			}
			
			foreach ($parents as $parent)
			{	
				$tb->add_menu_item(array(
					//'disabled' => $classinfo["can_add"]==0?false:true,
					'parent'=> $parent,
					'text'=> $classinfo["name"],
					'url' => html::get_new_url($class_id, $newparent),
				));
			}
		}
		//Add submenus
		foreach ((aw_ini_get("classfolders")) as $key => $menu)
		{
			$tb->add_sub_menu(array(
    			"parent" => $menu["parent"]?$menu["parent"]:'add_item',
    			"name" => $key,
   	 			"text" => $menu["name"],
    		));
		}
		
		$tb->add_button(array(
			'name' => 'del',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta valitud objektid'),
			'action' => 'delete_selected_objects',
		));
		
		$tb->add_button(array(
			'name' => 'cut',
			'img' => 'cut.gif',
			'tooltip' => t('Cut'),
			'action' => 'cut',
		));
		
		if($_SESSION["crm_cut"])
		{
			$tb->add_button(array(
				'name' => 'paste',
				'img' => 'paste.gif',
				'tooltip' => t('Paste'),
				'url' => $this->mk_my_orb("paste", array(
					"parent" => $arr["request"]["parent"],
					"id" => $arr["obj_inst"]->id(),
					"group" => $arr["request"]["group"],
						), CL_CRM_COMPANY),//'paste',
				'disabled' => 'true',
			));
		}
	}

	function do_object_table_header(&$table)
	{
		$table->define_field(array(
			"name" => "icon",
			"width" => 15
		));
			
		$table->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => "1",
		));
		
		$table->define_field(array(
			"name" => "modified",
			"caption" => t("Muudetud"),
			"sortable" => "1",
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.y",
			"align" => "center",
		));
		
		$table->define_field(array(
			"name" => "class_id",
			"caption" => t("Tüüp"),
			"sortable" => "1",
			"callback" => array(&$this, "get_class_name"),
		));
			
		$table->define_chooser(array(
			"name" => "select",
			"field" => "select",
		));
	}

	function get_class_name($id)
	{
		$classes = aw_ini_get("classes"); 
		return $classes[$id]["name"];
	}

	function define_object_table_data(&$arr)
	{
		$classes = aw_ini_get("classes");
		unset($classes[CL_RELATION]);
		$class_ids = array_keys($classes);
		
		$ol = new object_list(array(
			"parent" => $arr["request"]["parent"] ? $arr["request"]["parent"] : $arr["obj_inst"]->id(),
			"class_id" => $class_ids 
		));
		
		$table = &$arr["prop"]["vcl_inst"];
		
		get_instance("core/icons");
		foreach ($ol->arr() as $item)
		{
			$table->define_data(array(
				"class_id" => $item->class_id(),
				"name" => html::href(array(
					"url" => html::get_change_url($item->id()),
					"caption" => $item->name(),
				)),
				"modified" => get_lc_date($item->modified()),
				"select" => $item->id(),
				"icon" => html::img(array(
					"url" => icons::get_icon_url($item->class_id()),
				)),
			));
		}
	}

	function _get_objects_listing_table($arr)
	{
		$table = &$arr["prop"]["vcl_inst"];
		$this->do_object_table_header($table);
		$this->define_object_table_data($arr);
	}

	function _get_stypes_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_new_button(array(CL_CRM_SERVICE_TYPE,CL_CRM_SERVICE_TYPE_CATEGORY), !empty($arr["request"]["stype"]) ? $arr["request"]["stype"] : $arr["obj_inst"]->id(), 67);
		$tb->add_delete_button();
	}

	function _get_stypes_tbl($arr)
	{
		$pt = !empty($arr["request"]["stype"]) ? $arr["request"]["stype"] : $arr["obj_inst"]->id();
		$t =& $arr["prop"]["vcl_inst"];
		$t->table_from_ol(
			new object_list(array(
				"parent" => $pt,
				"class_id" => CL_CRM_SERVICE_TYPE,
				"lang_id" => array(),
				"site_id" => array()
			)),
			array("name", "hr_price"),
			CL_CRM_SERVICE_TYPE
		);
	}

	function _get_stypes_tree($arr)
	{
		
		
		$arr["prop"]["vcl_inst"] = treeview::tree_from_objects(array(
			"tree_opts" => array(
				"type" => TREE_DHTML, 
				"persist_state" => true,
				"tree_id" => "service_types",
			),
			"root_item" => $arr["obj_inst"],
			"ot" => new object_tree(array(
				"class_id" => array(CL_CRM_SERVICE_TYPE_CATEGORY),
				"parent" => $arr["obj_inst"]->id(),
				"lang_id" => array(),
				"site_id" => array()
			)),
			"var" => "stype",
			"icon" => icons::get_icon_url(CL_MENU)
		));
	}
}
?>
