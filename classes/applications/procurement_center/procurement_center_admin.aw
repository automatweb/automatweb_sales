<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/procurement_center/procurement_center_admin.aw,v 1.3 2007/12/06 14:33:50 kristo Exp $
// procurement_center_admin.aw - Hangete administreerimiskeskkond 
/*

@classinfo syslog_type=ST_PROCUREMENT_CENTER_ADMIN relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@default table=objects

@default group=general

	@property ord_folder type=relpicker reltype=RELTYPE_FOLDER field=meta method=serialize
	@caption Hankijate kataloog

	@property impl_folder type=relpicker reltype=RELTYPE_FOLDER field=meta method=serialize
	@caption Pakkujate kataloog

	@property manager_co type=relpicker reltype=RELTYPE_MANAGER_CO field=meta method=serialize
	@caption Haldaja firma

@default group=orderers

	@property ord_tb type=toolbar no_caption=1 store=no
	
	@layout ord type=hbox width=20%:80%

		@property ord_tree type=treeview store=no no_caption=1 parent=ord
		@property ord_table type=table store=no no_caption=1 parent=ord

@default group=impls

	@property impl_tb type=toolbar no_caption=1 store=no
	
	@layout impl type=hbox width=20%:80%

		@property impl_tree type=treeview store=no no_caption=1 parent=impl
		@property impl_table type=table store=no no_caption=1 parent=impl

@groupinfo orderers caption="Hankijad" submit=no
@groupinfo impls caption="Pakkujad" submit=no

@reltype FOLDER value=1 clid=CL_MENU
@caption Kataloog

@reltype MANAGER_CO value=2 clid=CL_CRM_COMPANY
@caption Haldaja firma
*/

class procurement_center_admin extends class_base
{
	const AW_CLID = 1071;

	function procurement_center_admin()
	{
		$this->init(array(
			"tpldir" => "applications/procurement_center/procurement_center_admin",
			"clid" => CL_PROCUREMENT_CENTER_ADMIN
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "ord_tb":
				$this->_ord_tb($arr);
				break;

			case "ord_tree":
				$this->_ord_tree($arr);
				break;

			case "ord_table":
				$this->_ord_table($arr);
				break;

			case "impl_tb":
				$this->_impl_tb($arr);
				break;

			case "impl_tree":
				$this->_impl_tree($arr);
				break;

			case "impl_table":
				$this->_impl_table($arr);
				break;
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "manager_co":
				if ($this->can("view", $prop["value"]))
				{
					$new_obj = obj($prop["value"]);
					$new_obj->set_prop("do_create_users", 1);
					$new_obj->save();
					// define an user redirect url for the company group
					$co_grp = $new_obj->get_first_obj_by_reltype("RELTYPE_GROUP");

					$cfg = get_instance("config");		
					$es = $cfg->get_simple_config("login_grp_redirect");
					$this->dequote(&$es);
					$lg = aw_unserialize($es);
					$lg[$co_grp->prop("gid")]["pri"] = 1000000;
					$lg[$co_grp->prop("gid")]["url"] = html::get_change_url($arr["obj_inst"]->id(), array("group" => "p"));

					$ss = aw_serialize($lg, SERIALIZE_XML);
					$this->quote(&$ss);
					$cfg->set_simple_config("login_grp_redirect", $ss);
				}
				break;
		}
		return $retval;
	}	

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function _ord_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_menu_button(array(
			'name'=>'add_item',
			'tooltip'=> t('Uus')
		));

		$parent = $arr["request"]["p_id"] ? $arr["request"]["p_id"] : $arr["obj_inst"]->prop("ord_folder");

		$tb->add_menu_item(array(
			'parent'=>'add_item',
			'text'=> t('Kataloog'),
			'link'=> html::get_new_url(CL_MENU, $parent, array("return_url" => get_ru()))
		));

		$tb->add_menu_item(array(
			'parent'=>'add_item',
			'text'=> t('Hankija'),
			'link'=> html::get_new_url(CL_CRM_COMPANY, $parent, array(
				"return_url" => get_ru(),
				"pseh" => aw_register_ps_event_handler(
						CL_PROCUREMENT_CENTER_ADMIN, 
						"handle_ord_submit", 
						array("id" => $arr["obj_inst"]->id()),
						CL_CRM_COMPANY
				)
			))
		));

		$tb->add_button(array(
			'name' => 'del',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta valitud hankijad'),
			'action' => 'delete_cos',
			'confirm' => t("Kas oled kindel et soovid valitud hankijad kustudada?")
		));
	}

	function _ord_tree($arr)
	{
		classload("core/icons");
		$arr["prop"]["vcl_inst"] = treeview::tree_from_objects(array(
			"tree_opts" => array(
				"type" => TREE_DHTML, 
				"persist_state" => true,
				"tree_id" => "procurement_center_admin_ord",
			),
			"root_item" => obj($arr["obj_inst"]->prop("ord_folder")),
			"ot" => new object_tree(array(
				"class_id" => array(CL_MENU),
				"parent" => $arr["obj_inst"]->prop("ord_folder"),
				"lang_id" => array(),
				"site_id" => array()
			)),
			"var" => "p_id",
			"icon" => icons::get_icon_url(CL_MENU)
		));
	}

	function _init_p_tbl(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "center",
			"caption" => t("T&ouml;&ouml;laud"),
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
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y H:i"
		));
		$t->define_field(array(
			"name" => "modifiedby",
			"caption" => t("Muutja"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "modified",
			"caption" => t("Muudetud"),
			"align" => "center",
			"sortable" => 1,
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y H:i"
		));
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));
	}

	function _ord_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_p_tbl($t);

		$parent = $arr["request"]["p_id"] ? $arr["request"]["p_id"] : $arr["obj_inst"]->prop("ord_folder");
		$ccs = new object_list(array(
			"class_id" => CL_PROCUREMENT_CENTER,
			"lang_id" => array(),
			"site_id" => array()
		));
		$cd = array();
		foreach($ccs->arr() as $cc)
		{
			$co = $cc->get_first_obj_by_reltype("RELTYPE_MANAGER_CO");
			if ($co)
			{
				$cd[$co->id()] = $cc;
			}
		}

		$ol = new object_list(array(
			"class_id" => array(CL_MENU, CL_CRM_COMPANY),
			"parent" => $parent,
			"lang_id" => array(),
			"site_id" => array()
		));
		foreach($ol->arr() as $o)
		{
			$t->define_data(array(
				"name" => html::obj_change_url($o),
				"center" => html::obj_change_url($cd[$o->id()]),
				"createdby" => $o->createdby(),
				"created" => $o->created(),
				"modifiedby" => $o->modifiedby(),
				"modified" => $o->modified(),
				"oid" => $o->id()
			));
		}
	}

	function _impl_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_menu_button(array(
			'name'=>'add_item',
			'tooltip'=> t('Uus')
		));

		$parent = $arr["request"]["p_id"] ? $arr["request"]["p_id"] : $arr["obj_inst"]->prop("impl_folder");

		$tb->add_menu_item(array(
			'parent'=>'add_item',
			'text'=> t('Kataloog'),
			'link'=> html::get_new_url(CL_MENU, $parent, array("return_url" => get_ru()))
		));

		$tb->add_menu_item(array(
			'parent'=>'add_item',
			'text'=> t('Pakkuja'),
			'link'=> html::get_new_url(CL_CRM_COMPANY, $parent, array(
				"return_url" => get_ru(),
				"pseh" => aw_register_ps_event_handler(
						CL_PROCUREMENT_CENTER_ADMIN, 
						"handle_impl_submit", 
						array("id" => $arr["obj_inst"]->id()),
						CL_CRM_COMPANY
				)
			))
		));

		$tb->add_button(array(
			'name' => 'del',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta valitud pakkujad'),
			'action' => 'delete_cos',
			'confirm' => t("Kas oled kindel et soovid valitud pakkujad kustudada?")
		));
	}

	function _impl_tree($arr)
	{
		classload("core/icons");
		$arr["prop"]["vcl_inst"] = treeview::tree_from_objects(array(
			"tree_opts" => array(
				"type" => TREE_DHTML, 
				"persist_state" => true,
				"tree_id" => "procurement_center_admin_impl",
			),
			"root_item" => obj($arr["obj_inst"]->prop("impl_folder")),
			"ot" => new object_tree(array(
				"class_id" => array(CL_MENU),
				"parent" => $arr["obj_inst"]->prop("impl_folder"),
				"lang_id" => array(),
				"site_id" => array()
			)),
			"var" => "p_id",
			"icon" => icons::get_icon_url(CL_MENU)
		));
	}

	function _init_impl_tbl(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "center",
			"caption" => t("T&ouml;&ouml;laud"),
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
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y H:i"
		));
		$t->define_field(array(
			"name" => "modifiedby",
			"caption" => t("Muutja"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "modified",
			"caption" => t("Muudetud"),
			"align" => "center",
			"sortable" => 1,
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y H:i"
		));
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));
	}

	function _impl_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_impl_tbl($t);

		$parent = $arr["request"]["p_id"] ? $arr["request"]["p_id"] : $arr["obj_inst"]->prop("impl_folder");
		$ccs = new object_list(array(
			"class_id" => CL_PROCUREMENT_IMPLEMENTOR_CENTER,
			"lang_id" => array(),
			"site_id" => array()
		));
		$cd = array();
		foreach($ccs->arr() as $cc)
		{
			$co = $cc->get_first_obj_by_reltype("RELTYPE_MANAGER_CO");
			if ($co)
			{
				$cd[$co->id()] = $cc;
			}
		}

		$ol = new object_list(array(
			"class_id" => array(CL_FOLDER, CL_CRM_COMPANY),
			"parent" => $parent,
			"lang_id" => array(),
			"site_id" => array()
		));
		foreach($ol->arr() as $o)
		{
			$t->define_data(array(
				"name" => html::obj_change_url($o),
				"center" => html::obj_change_url($cd[$o->id()]),
				"createdby" => $o->createdby(),
				"created" => $o->created(),
				"modifiedby" => $o->modifiedby(),
				"modified" => $o->modified(),
				"oid" => $o->id()
			));
		}
	}

	/**
		@attrib name=delete_cos
	**/
	function delete_cos($arr)
	{
		object_list::iterate_list($arr["sel"], "delete");
		return $arr["post_ru"];
	}

	function handle_ord_submit($new_obj, $arr)
	{
		// so here we need to set a bunch of stuff for the company to work right
		// people are users, groups and stuff
		$new_obj->set_prop("do_create_users", 1);
		$new_obj->save();

		// apply the group creator
			// seems it is applied automatically

		// create a procurement center for it
		$pc = obj();
		$pc->set_parent($new_obj->id());
		$pc->set_class_id(CL_PROCUREMENT_CENTER);
		$pc->set_name(sprintf(t("%s hankekeskkond"), $new_obj->name()));
		$pc->save();
		$pc->connect(array(
			"to" => $new_obj->id(),
			"type" => "RELTYPE_MANAGER_CO"
		));

		// define an user redirect url for the company group
		$co_grp = $new_obj->get_first_obj_by_reltype("RELTYPE_GROUP");

		$cfg = get_instance("config");		
		$es = $cfg->get_simple_config("login_grp_redirect");
		$this->dequote(&$es);
		$lg = aw_unserialize($es);
		$lg[$co_grp->prop("gid")]["pri"] = 1000000;
		$lg[$co_grp->prop("gid")]["url"] = html::get_change_url($pc->id(), array("group" => "p"));

		$ss = aw_serialize($lg, SERIALIZE_XML);
		$this->quote(&$ss);
		$cfg->set_simple_config("login_grp_redirect", $ss);
	}

	function handle_impl_submit($new_obj, $arr)
	{
		// so here we need to set a bunch of stuff for the company to work right
		// people are users, groups and stuff
		$new_obj->set_prop("do_create_users", 1);
		$new_obj->save();

		// apply the group creator
			// seems it is applied automatically

		// create a procurement center for it
		$pc = obj();
		$pc->set_parent($new_obj->id());
		$pc->set_class_id(CL_PROCUREMENT_IMPLEMENTOR_CENTER);
		$pc->set_name(sprintf(t("%s pakkumiste keskkond"), $new_obj->name()));
		$pc->save();
		$pc->connect(array(
			"to" => $new_obj->id(),
			"type" => "RELTYPE_MANAGER_CO"
		));

		// define an user redirect url for the company group
		$co_grp = $new_obj->get_first_obj_by_reltype("RELTYPE_GROUP");

		$cfg = get_instance("config");		
		$es = $cfg->get_simple_config("login_grp_redirect");
		$this->dequote(&$es);
		$lg = aw_unserialize($es);
		$lg[$co_grp->prop("gid")]["pri"] = 1000000;
		$lg[$co_grp->prop("gid")]["url"] = html::get_change_url($pc->id(), array("group" => "p"));

		$ss = aw_serialize($lg, SERIALIZE_XML);
		$this->quote(&$ss);
		$cfg->set_simple_config("login_grp_redirect", $ss);
	}
}
?>
