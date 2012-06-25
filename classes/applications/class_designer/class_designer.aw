<?php

// class_designer.aw - Vormidisainer

// üldine, soovituslik, kohustuslik
// nõude number, ext_id
/*

@classinfo relationmgr=yes
@classinfo no_status=1

@default table=objects
@default group=general_sub

@property name type=textbox rel=1 trans=1
@caption Nimi
@comment Objekti nimi

@property comment type=textbox
@caption Kommentaar
@comment Vabas vormis tekst objekti kohta

@property status type=status trans=1 default=1
@caption Aktiivne
@comment Kas objekt on aktiivne

@property is_registered type=checkbox ch_value=1 field=meta method=serialize
@caption Klass on registreeritud

@property infomsg type=text store=no
@caption Info

@property visualize type=text store=no editonly=1
@caption Eelvaade

@property from_existing_class type=hidden field=meta method=serialize group=general
@caption Olemasoleva klassi p&otilde;hjal

@property from_existing_class_file type=hidden field=meta method=serialize group=general
@caption Olemasoleva klassi fail

property preview type=text store=no
caption

@default group=settings
@property reg_class_id type=text table=aw_class field=class_id
@caption CLID
@comment Kui klass on registreeritud, siis näitab klassile määratud ID-d

@property object_name type=select field=meta method=serialize
@caption Objekti nime omadus
@comment Siit valitud vormi väljast võetakse objektile nimi

@property can_add type=checkbox ch_value=1 table=aw_class
@caption Saab lisada
@comment Kui see märgistada, siis saab klassi lisada rohelise nupu menüü kaudu

@property class_folder type=select table=aw_class
@caption Asukoht lisamismenüüs
@comment Läheb arvesse ainult siis, kui klassi 'saab lisada'

@default field=meta
@default method=serialize

@property relationmgr type=checkbox ch_value=1 default=1
@caption Seostehaldur
@comment Kas klassil on seostehaldur?

@property no_comment type=checkbox ch_value=1
@caption Kommentaari muuta ei saa

@property no_status type=checkbox ch_value=1
@caption Aktiivsust muuta ei saa
@comment Kui see märgistada, siis ei saa klassi aktiivsust muuta, ta on alati aktiivne

@default group=classdef
@property classdef type=text store=no no_caption=1
@caption Klassi definitsioon

@property create_sql type=text store=no no_caption=1 group=create_sql
@caption SQL definitsioon

@property alter_sql type=text store=no no_caption=1 group=alter_sql
@caption SQL definitsioon

@default group=planner
@layout phbox1 type=hbox

@property planner_toolbar type=toolbar no_caption=1 store=no parent=phbox1
@caption Planeerija toolbar

@property el_defs_toolbar type=toolbar store=no group=el_defs no_caption=1
@caption Elementide toolbar

@property int_refs_toolbar type=toolbar store=no group=int_refs no_caption=1
@caption Sisemiste seoste toolbar

@property ext_refs_toolbar type=toolbar store=no group=ext_refs no_caption=1
@caption Väliste seoste toolbar

@layout pvbox1 type=vbox  group=planner,el_defs,int_refs,ext_refs
@layout phbox2 type=hbox parent=pvbox1 width=30%:70% group=planner,el_defs,int_refs,ext_refs

@property planner_tree type=treeview parent=phbox2 no_caption=1 group=planner,el_defs,int_refs,ext_refs
@caption Planeerija puu

@property planner_list type=table no_caption=1 parent=phbox2 no_caption=1
@caption Planeerija elemendid


@property el_defs_table type=table group=el_defs no_caption=1 parent=phbox2
@caption Elementide tabel

@property int_refs_table type=table group=int_refs no_caption=1 parent=phbox2
@caption Sisemiste seoste tabel

@property ext_refs_table type=table group=ext_refs no_caption=1 parent=phbox2
@caption Väliste seoste tabel

@default group=designer

@layout hbox1 type=hbox group=designer

@property designer_toolbar type=toolbar no_caption=1 store=no parent=hbox1
@caption Disaineri toolbar

@layout vbox1 type=vbox group=designer
@layout hbox2 type=hbox group=designer parent=vbox1 width=30%:70%

@property layout_tree type=treeview parent=hbox2 no_caption=1
@caption Grupid

@property element_list type=table no_caption=1 parent=hbox2 no_caption=1
@caption Elemendid

@layout hbox3 type=hbox group=designer

@property helper type=text no_caption=1 parent=hbox3
@property group_parent type=hidden
@property tmp_name type=hidden
@property element_type type=hidden

@default group=relations_sub

	@property relations_mgr type=releditor reltype=RELTYPE_RELATION mode=manager no_caption=1 props=name,r_class_id,value table_fields=name,r_class_id,value
	@caption Seosed

@default group=dev_request

@property temp type=text store=no
@caption temp

@default group=in_search

@property temp1 type=text store=no
@caption temp

@default group=data

@property temp2 type=text store=no
@caption temp

@groupinfo general_sub caption="Üldine" parent=general
@groupinfo settings caption="Seaded" parent=general

//@groupinfo cl_planner caption="Planeerija"
@groupinfo planner caption="Planeerija" submit=no

@groupinfo relations caption="Seosed"
@groupinfo relations_sub caption="Seosed" submit=no parent=relations
@groupinfo int_refs caption="Sisemised seosed" parent=relations submit=no
@groupinfo ext_refs caption="Välised seosed" parent=relations submit=no
@default group=dev_request

@property temp type=text store=no
@caption temp

@default group=in_search

@property temp1 type=text store=no
@caption temp

@default group=data

@property temp2 type=text store=no
@caption temp

@groupinfo general_sub caption="Üldine" parent=general
@groupinfo settings caption="Seaded" parent=general

//@groupinfo cl_planner caption="Planeerija"
@groupinfo planner caption="Planeerija" submit=no

@groupinfo relations caption="Seosed"
@groupinfo relations_sub caption="Seosed" submit=no parent=relations
@groupinfo int_refs caption="Sisemised seosed" parent=relations submit=no
@groupinfo ext_refs caption="Välised seosed" parent=relations submit=no

@groupinfo designer caption="Disainer" submit=no
@groupinfo el_defs caption="Elemendid" submit=no
@groupinfo dev_request caption="Arendusvajadus"
@groupinfo in_search caption="Otsing"
@groupinfo data caption="Andmed"
@groupinfo defs caption="Definitsioonid"
@groupinfo classdef caption="Klassi kirjeldus" submit=no parent=defs
@groupinfo create_sql caption="Create SQL" submit=no parent=defs
@groupinfo alter_sql caption="Alter SQL" submit=no parent=defs

@reltype RELATION value=1 clid=CL_CLASS_DESIGNER_RELATION
@caption seos

@reltype CL_REVIEW value=2 clid=CL_CLASS_DESIGNER
@caption Eelvaade

@tableinfo aw_class index=aw_id master_table=objects master_index=brother_of

*/

class class_designer extends class_base
{
	function class_designer()
	{
		$this->init(array(
			"tpldir" => "applications/class_designer",
			"clid" => CL_CLASS_DESIGNER
		));

		$this->elements = array(
			CL_PROPERTY_TEXTBOX,CL_PROPERTY_CHOOSER,
			CL_PROPERTY_CHECKBOX,CL_PROPERTY_TABLE,
			CL_PROPERTY_TEXTAREA,CL_PROPERTY_SELECT,
			CL_PROPERTY_TREE,CL_PROPERTY_TOOLBAR,CL_PROPERTY
		);

		$this->all_els = $this->elements;
		$this->all_els[] = CL_PROPERTY_GROUP;
		$this->all_els[] = CL_PROPERTY_GRID;

		// list of all elements that can be saved to a table
		$this->saveable = array(CL_PROPERTY_TEXTBOX,CL_PROPERTY_TEXTAREA,CL_PROPERTY_CHOOSER,CL_PROPERTY_CHECKBOX);

		$this->gen_folder = $this->cfg["site_basedir"]."/files/classes";
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["return_url"] = aw_ini_get("baseurl").aw_global_get("REQUEST_URI");
		$arr["group_parent"] = $_GET["group_parent"] ? $_GET["group_parent"] : $arr["id"];
		$arr["register_under"] = $_REQUEST["register_under"];
	}

	function callback_pre_edit($arr)
	{
		if (in_array($arr["request"]["group"],array("designer","planner","cl_planner","int_refs","ext_refs","el_defs")))
		{
			$can_add = array(
				"group" => false,
				"grid" => false,
				"element" => false,
			);

			if (empty($arr["request"]["group_parent"]))
			{
				$group_parent = $arr["obj_inst"]->id();
				// cannot add those to top level
				$can_add["group"] = true;
			}
			else
			{
				$group_parent = $arr["request"]["group_parent"];
				$grp_p = new object($group_parent);
				$grp_clid = $grp_p->class_id();
				if ($grp_clid == CL_PROPERTY_GROUP)
				{
					$can_add["grid"] = true;
					$can_add["group"] = true;
				}
				elseif ($grp_clid == CL_PROPERTY_GRID)
				{
					$can_add["element"] = true;
				};
			};

			$this->can_add = $can_add;
			$this->group_parent = $group_parent;
		};
	}


	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "designer_toolbar":
				$this->create_designer_toolbar($arr);
				break;

			case "planner_toolbar":
				$this->create_planner_toolbar($arr);
				break;

			case "planner_tree":
				$this->create_planner_tree($arr);
				break;

			case "layout_tree":
				$this->create_layout_tree($arr);
				break;

			case "element_list":
				$this->create_element_list($arr);
				break;

			case "helper":
				$this->read_template("helper_functions.tpl");
				$prop["value"] = $this->parse();
				break;

			case "group_parent":
				$prop["value"] = $this->group_parent;
				break;

			case "classdef":
				$this->save_tabledef = 1;
				$prop["value"] = "<pre>" . htmlspecialchars($this->gen_classdef($arr)) . "</pre>";
				break;

			case "create_sql":
				$prop["value"] = "<pre>" . htmlspecialchars($this->gen_create_sql(array("id" => $arr["obj_inst"]->id()))) . "</pre>";
				break;

			case "alter_sql":
				$sql = $this->gen_alter_sql(array("id" => $arr["obj_inst"]->id()));
				if (empty($sql))
				{
					$prop["value"] = t("SQL table is up to date");
				}
				else
				{
					$prop["value"] = "<pre>" . htmlspecialchars($sql) . "</pre>";
				};
				break;

			case "visualize":
				// first check, whether we have an object
				$conns = $arr["obj_inst"]->connections_from(array(
					"type" => "RELTYPE_CL_PREVIEW",
				));
				$reg_class_id = $arr["obj_inst"]->prop("reg_class_id");
				$ol = new object_list(array(
					"parent" => $arr["obj_inst"]->id(),
					"class_id" => $reg_class_id,
				));
				if (sizeof($ol->ids()) > 0)
				{
					$ob = $ol->begin();
					$prop["value"] = html::href(array(
						"url" => $this->mk_my_orb("change",array("class" => $arr["obj_inst"]->id(),"id" => $ob->id())),
						"caption" => t("Eelvaade"),
					));
				}
				else
				{
					$prop["value"] = html::href(array(
						"url" => $this->mk_my_orb("new",array("class" => $arr["obj_inst"]->id(),"parent" => $arr["obj_inst"]->id()),"class_visualizer"),
						"caption" => t("Eelvaade"),
					));


				};
				break;

			case "is_registered":
				if (1 == $prop["value"])
				{
					$prop["type"] = "text";
					$prop["value"] = "<b>Klass on registreeritud!</b>";
				};
				break;

			case "object_name":
				$otree = new object_tree(array(
					"parent" => $arr["obj_inst"],
				));
				$olist = $otree->to_list();
				// not much point in using a checkbox as name is there?
				foreach($olist->arr() as $o)
				{
					if (CL_PROPERTY_TEXTBOX == $o->class_id())
					{
						$prop["options"][$o->id()] = $o->name();
					};

				};
				break;

			case "infomsg":
				$clx = aw_ini_get("classes");
				if (!is_writable($this->gen_folder))
				{
					$prop["value"] = t("Väljundkataloog ei ole kirjutatav!");
				};
				if ($prop["value"] == "")
				{
					return PROP_IGNORE;
				}
				break;

			case "class_folder":
				$prop["options"] = $this->gen_folder_tree();
				if (!$prop["value"])
				{
					$prop["value"] = $arr["obj_inst"]->meta("register_under");
				}
				break;

			case "int_refs_table":
				$this->do_int_refs_table($arr);
				break;

			case "ext_refs_table":
				$this->do_ext_refs_table($arr);
				break;

			case "el_defs_toolbar":
				$this->do_el_defs_toolbar($arr);
				break;

			case "int_refs_toolbar":
				$this->do_int_refs_toolbar($arr);
				break;

			case "ext_refs_toolbar":
				$this->do_ext_refs_toolbar($arr);
				break;

			case "el_defs_table":
				$this->do_el_defs_table($arr);
				break;

			case "planner_list":
				$this->do_planner_list($arr);
				break;

			case "preview":
				if (!$arr["obj_inst"]->prop("reg_class_id"))
				{
					return PROP_IGNORE;
				}
				// find some objects

				$ol = new object_list(array("class_id" => $arr["obj_inst"]->prop("reg_class_id"),"limit" => 2));
				if ($ol->count())
				{
					$o = $ol->begin();
					$prop["value"] = html::get_change_url($o->id(), array("return_url" => get_ru()), t("Eelvaade"));
				}
				else
				{
					$prop["value"] = html::get_new_url($arr["obj_inst"]->prop("reg_class_id"), $arr["obj_inst"]->id(), array("return_url" => get_ru()), t("Eelvaade"));
				}
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
			case "is_registered":
				if (1 == $prop["value"])
				{
					$this->do_register_class($arr);
				}
				else
				{
					$retval = PROP_IGNORE;
				};
				break;

			case "el_defs_table":
				$this->update_el_defs_table($arr);
				break;

		}
		return $retval;
	}

	/** Creates a hierarchy of groups for the planner
	**/
	function create_planner_tree(&$arr)
	{
		$o = $arr["obj_inst"];
		$tree = $arr["prop"]["vcl_inst"];
		$oid = $o->id();
		$tree->add_item(0,array(
			"name" => $o->name(),
			"id" => $oid,
			"is_open" => 1,
			"url" => $this->mk_my_orb("change",array(
				"id" => $oid,
				"group" => $arr["request"]["group"],
			)),
		));

		$el_tree = new object_tree(array(
			"parent" => $oid,
			"class_id" => array(CL_PROPERTY_GROUP,CL_PROPERTY_GRID),
			"lang_id" => array(),
			"site_id" => array(),

		));
		$el_list = $el_tree->to_list();
		$ellist = $el_list->arr();
		$this->__elord = $o->meta("element_ords");
		usort($ellist, array($this, "__ellist_comp"));

		foreach($ellist as $el)
		{
			$clid = $el->class_id();
			$iconurl = "";
			// XXX: use class icons
			$parent = $el->parent();
			if ($clid == CL_PROPERTY_GRID)
			{
				$p_obj = new object($el->parent());
				$parent = $p_obj->parent();
			};
			$el_id = $el->id();
			if ($clid != CL_PROPERTY_GRID)
			{
				$tree->add_item($parent,array(
					"name" => $el->name(),
					"id" => $el_id,
					"url" => $this->mk_my_orb("change",array(
						"id" => $oid,
						"group" => $arr["request"]["group"],
						"group_parent" => $el_id,
					)),
					"iconurl" => $iconurl,
					"is_open" => 1,
				));
			};
		};

		$tree->set_selected_item($this->group_parent);

	}

	/** Creates a hierarchy of groups and grids
	**/
	function create_layout_tree(&$arr)
	{
		$o = $arr["obj_inst"];
		$tree = $arr["prop"]["vcl_inst"];
		$oid = $o->id();
		$tree->add_item(0,array(
			"name" => $o->name(),
			"id" => $oid,
			"is_open" => 1,
			"url" => $this->mk_my_orb("change",array(
				"id" => $oid,
				"group" => $arr["request"]["group"],
			)),
		));

		$el_tree = new object_tree(array(
			"parent" => $oid,
			"class_id" => array(CL_PROPERTY_GROUP,CL_PROPERTY_GRID),
			"lang_id" => array(),
			"site_id" => array(),

		));
		$el_list = $el_tree->to_list();
		$ellist = $el_list->arr();
		$this->__elord = $o->meta("element_ords");
		usort($ellist, array($this, "__ellist_comp"));

		foreach($ellist as $el)
		{
			$clid = $el->class_id();
			$iconurl = "";
			// XXX: use class icons
			if ($clid == CL_PROPERTY_GRID)
			{
				$iconurl = "/automatweb/images/icons/merge_down.png";
			};
			$el_id = $el->id();
			$tree->add_item($el->parent(),array(
				"name" => $el->name(),
				"id" => $el_id,
				"url" => $this->mk_my_orb("change",array(
					"id" => $oid,
					"group" => "designer",
					"group_parent" => $el_id,
				)),
				"iconurl" => $iconurl,
				"is_open" => 1,
			));
		};

		$tree->set_selected_item($this->group_parent);

	}

	/** Planner toolbar
	**/
	function create_planner_toolbar(&$arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_menu_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Uus"),
		));
		// I don't know .. it feels a bit of uncomfortable to create a menu for just 2 elements
		// but hey .. what do I know
		$tb->add_menu_item(array(
			"parent" => "new",
			"name" => "newgrp",
			"link" => $this->mk_my_orb("create_group", array(
				"group_parent" => $this->group_parent,
				"return_url" => get_ru(),
				"id" => $arr["obj_inst"]->id()
			)),
			"text" => t("Grupp"),
		));
		$tb->add_menu_item(array(
			"parent" => "new",
			"name" => "newprop",
			"link" => $this->mk_my_orb("create_element", array(
				"element_type" => CL_PROPERTY,
				"group_parent" => $this->group_parent,
				"return_url" => get_ru(),
				"id" => $arr["obj_inst"]->id()
			)),
			"text" => t("Omadus"),
		));

	}

	/** Helper toolbar to deal with elements
	**/
	function create_designer_toolbar(&$arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_menu_button(array(
			"name" => "new",
			"img" => "new.gif",
		));


		// XXX: siin on vaja pidada tracki selle üle milliseid elemente parajasti lisada saab
		// gridi saab lisada ainult siis kui parentiks on grupp
		// elementi saab lisada ainult siis kui parentiks on grid
		$tb->add_menu_item(array(
			"parent" => "new",
			"text" => t("tab"),
			"link" => $this->mk_my_orb("create_group", array(
				"group_parent" => $this->group_parent,
				"return_url" => get_ru(),
				"id" => $arr["obj_inst"]->id()
			)),
			"disabled" => !$this->can_add["group"],
		));

		$tb->add_menu_item(array(
			"parent" => "new",
			"text" => t("vbox"),
			"link" => $this->mk_my_orb("create_grid", array(
				"group_parent" => $this->group_parent,
				"return_url" => get_ru(),
				"gtype" => 0,
				"id" => $arr["obj_inst"]->id()
			)),
			"disabled" => !$this->can_add["grid"],
		));

		$tb->add_menu_item(array(
			"parent" => "new",
			"text" => t("hbox"),
			"link" => $this->mk_my_orb("create_grid", array(
				"group_parent" => $this->group_parent,
				"return_url" => get_ru(),
				"gtype" => 1,
				"id" => $arr["obj_inst"]->id()
			)),
			"disabled" => !$this->can_add["grid"],
		));

		// siia ma lisan kõik omadused, mis on planeerijast siia paigutatud
		$oid = $arr["obj_inst"]->id();
		$el_tree = new object_tree(array(
			"parent" => $oid,
			//"class_id" => array(CL_PROPERTY_GROUP,CL_PROPERTY_GRID),
			"lang_id" => array(),
			"site_id" => array(),

		));
		$el_list = $el_tree->to_list();
		$clinf = aw_ini_get("classes");

		$used_clids = array();
		/*
		$tb->add_menu_separator(array(
			"parent" => "new"
		));


		$allowed = $this->elements;
		$allowed[] = CL_PROPERTY;

		foreach($el_list->arr() as $el_o)
		{
			$el_clid = $el_o->class_id();

			if ($el_clid == CL_PROPERTY)
			{
				if (is_oid($el_o->prop("property_type")))
				{
					$el_clid = $el_o->prop("property_type");
				};
			};


			if (!in_array($el_clid,$allowed))
			{
				continue;
			};

			if (empty($used_clids[$el_clid]))
			{
				$used_clids[$el_clid] = 1;
				$tb->add_sub_menu(array(
					"parent" => "new",
					"name" => $el_clid,
					"text" => $clinf[$el_clid]["name"],
				));
			};

			$tb->add_menu_item(array(
				"parent" => $el_clid,
				"text" => parse_obj_name($el_o->name()),
				"link" => $this->mk_my_orb("place_element", array(
					"el_id" => $el_o->id(),
					"group_parent" => $this->group_parent,
					"return_url" => get_ru(),
					"id" => $arr["obj_inst"]->id()
				)),
				"disabled" => !$this->can_add["element"],
			));


		};

		*/


		// aga ma tahan ikkagi
		/*
		$clinf = aw_ini_get("classes");
		foreach($this->elements as $element)
		{
			$tb->add_menu_item(array(
				"parent" => "new",
				"text" => $clinf[$element]["name"],
				"link" => $this->mk_my_orb("create_element", array(
					"element_type" => $element,
					"group_parent" => $this->group_parent,
					"return_url" => get_ru(),
					"id" => $arr["obj_inst"]->id()
				)),
				"disabled" => !$this->can_add["element"],
			));
		};
		*/


		$tb->add_separator();

		$tb->add_button(array(
			"name" => "save",
			"action" => "save",
			"tooltip" => t("Save"),
			"action" => "save_elements",
			"img" => "save.gif",
		));

		$tb->add_separator();

		$tb->add_button(array(
			"name" => "cut",
			"action" => "cut",
			"tooltip" => t("Cut"),
			"action" => "cut",
			"img" => "cut.gif",
		));

		if (count(safe_array($_SESSION["cd_cut"])) > 0 && $this->can_add["element"])
		{
			$tb->add_button(array(
				"name" => "paste",
				"action" => "paste",
				"tooltip" => t("Paste"),
				"action" => "paste",
				"img" => "paste.gif",
			));
		}

		$tb->add_button(array(
			"name" => "delete",
			"action" => "delete",
			"tooltip" => t("Delete"),
			"action" => "delete",
			"img" => "delete.gif",
			"confirm" => t("Kustutada valitud objektid?"),
		));

	}

	function create_element_list(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "ordbox",
			"caption" => t("Jrk"),
			"align" => "center",
			"width" => 50,
		));
		$t->define_field(array(
			"name" => "namebox",
			"caption" => t("Nimi"),
			"width" => 200,
		));
		$t->define_field(array(
			"name" => "class_id",
			"caption" => t("Tüüp"),
			"width" => "100",
		));
		$t->define_field(array(
			"name" => "edit",
			"caption" => t("Muuda"),
			"align" => "center",
			"width" => 100,
		));

		$t->define_chooser(array(
			"name" => "sel",
			"field" => "id",
		));

		//$t->set_sortable(false);

		$o = $arr["obj_inst"];

		$elist = new object_list(array(
			"parent" => $this->group_parent,
			"class_id" => $this->all_els
		));

		$clinf = aw_ini_get("classes");

		$elords = $arr["obj_inst"]->meta("element_ords");

		foreach($elist->arr() as $element)
		{
			$el_id = $element->id();
			$el_clid = $element->class_id();
			$elname = $element->name();

			$real_clid = $el_clid;
			$change_id = $el_id;
			if ($el_clid == CL_PROPERTY)
			{
				$real_clid = $element->prop("property_type");
				$real_obj = $element->get_first_obj_by_reltype("RELTYPE_REAL_PROPERTY");
				if ($real_obj)
				{
					$change_id = $real_obj->id();
				};
			};

			$t->define_data(array(
				"namebox" => html::textbox(array(
					"name" => "name[${el_id}]",
					"size" => 30,
					"value" => $elname,
				)),
				"id" => $el_id,
				"class_id" => $clinf[$real_clid]["name"],
				"ord" => $elords[$element->id()],
				"ordbox" => html::textbox(array(
					"name" => "ord[${el_id}]",
					"size" => 2,
					"value" => $elords[$element->id()],
				)),
				"edit" => html::href(array(
					"caption" => t("Muuda"),
					"url" => $this->mk_my_orb("change",array("id" => $change_id, "return_url" => get_ru()),$real_clid),
				)),
			));
		};

		$t->set_numeric_field("ord");
		$t->set_default_sortby("ord");
		$t->sort_by();
	}

	/**
		@attrib name=create_group

		@param id required type=int acl=view
		@param group_parent required
		@param return_url required

	**/
	function create_group($arr)
	{
		//print "inside create_group<br>";
		// group_parent on see mille alla teha
		// tmp_name on uue grupi nimi ... so what could be easier
		$g = new object();
		$g->set_class_id(CL_PROPERTY_GROUP);
		// XX: check whether we are allowed to add groups here
		$g->set_parent($arr["group_parent"]);
		$g->set_status(STAT_ACTIVE);

		$ol = new object_list(array("class_id" => CL_PROPERTY_GROUP, "parent" => $arr["group_parent"]));

		$g->set_name(t("Grupp ").($ol->count()+1));
		$g->save();

		$o = obj($arr["id"]);
		$elo = safe_array($o->meta("element_ords"));
		$elo[$g->id()] = count($elo) ? max(array_values($elo)) + 1 : 1;
		$o->set_meta("element_ords", $elo);
		$o->save();

		return html::get_change_url($g->id(), array("return_url" => ($arr["return_url"])));
	}

	/**
		@attrib name=create_grid

		@param id required type=int acl=view
		@param group_parent required
		@param gtype required
		@param return_url required

	**/
	function create_grid($arr)
	{
		$g = new object();
		$g->set_class_id(CL_PROPERTY_GRID);
		$g->set_parent($arr["group_parent"]);
		// XX: check whether we are allowed to add grids here
		$g->set_status(STAT_ACTIVE);
		$g->set_prop("grid_type",0);

		$ol = new object_list(array("class_id" => CL_PROPERTY_GRID, "parent" => $arr["group_parent"]));

		$g->set_name(t("vbox ").($ol->count() + 1));
		$g->save();

		$o = obj($arr["id"]);
		$elo = safe_array($o->meta("element_ords"));
		$elo[$g->id()] = count($elo) ? max(array_values($elo)) + 1 : 1;
		$o->set_meta("element_ords", $elo);
		$o->save();

		return html::get_change_url($g->id(), array("return_url" => ($arr["return_url"])));
	}

	/**
		@attrib name=create_element

		@param id required type=int acl=view
		@param element_type required
		@param group_parent required
		@param return_url required
	**/
	function create_element($arr)
	{
		// XX: check whether we are allowed to add elements here
		// parent on group_parent
		// class_id on el_id

		// XX: check whether this is allowed class_id
		$e = new object();
		$e->set_class_id($arr["element_type"]);
		$e->set_parent($arr["group_parent"]);
		$e->set_status(STAT_ACTIVE);

		$ol = new object_list(array("class_id" => $arr["element_type"], "parent" => $arr["group_parent"]));
		$cl = aw_ini_get("classes");

		$e->set_name($cl[$arr["element_type"]]["name"]." ".($ol->count() + 1));
		$e->save();

		$o = obj($arr["id"]);
		$elo = safe_array($o->meta("element_ords"));
		$elo[$e->id()] = count($elo) ? max(array_values($elo)) + 1 : 1;
		$o->set_meta("element_ords", $elo);
		$o->save();

		return html::get_change_url($e->id(), array("return_url" => ($arr["return_url"])));
	}

	/**
		@attrib name=place_element
		@param id required type=int
		@param group_parent required type=int
		@param el_id required type=int
		@param return_url optional

	**/
	function place_element($arr)
	{
		// klassi id on muutujas id
		// elemendi id on muutujas el_id
		// koht elemendi jaoks on muutujas group_parent
		$el_o = new object($arr["el_id"]);
		$el_o->set_parent($arr["group_parent"]);
		$el_o->save();
		// aga kuidas ma saan elementi korraga mitmesse kohta paigutada?

		return $arr["return_url"];
	}

	/**
		@attrib name=save_elements
	**/
	function save_elements($arr)
	{
		$o = obj($arr["id"]);
		$ords = safe_array($o->meta("element_ords"));
		foreach(safe_array($arr["ord"]) as $elid => $elord)
		{
			$ords[$elid] = $elord;
		}
		$o->set_meta("element_ords", $ords);
		$o->save();

		return $arr["return_url"];

	}

	function __ellist_comp($a, $b)
	{
		$a_o = $this->__elord[$a->id()];
		$b_o = $this->__elord[$b->id()];

		if ($a_o == $b_o)
		{
			return 0;
		}
		return ($a_o > $b_o ? 1 : -1);
	}

	function visualize_sql($arr)
	{
		$this->save_tabledef = 1;
		$this->gen_tabledef($arr);
		return $this->create;
	}

	function gen_valid_id($src)
	{
		$rv = strtolower(preg_replace("/\s/","_",$src));
		$rv = preg_replace("/\W/","",$rv);
		return $rv;
	}

	function gen_tabledef($arr)
	{
		$els = $this->get_class_elements($arr);
		$cfgu = get_instance("cfg/cfgutils");

		$clid = $arr["obj_inst"]->prop("reg_class_id");
		$sql_table = "class_${clid}_objects";

		$tabledef = array();
		$clinf = aw_ini_get("classes");

		foreach($els as $el)
		{
			$el_clid = $el["class_id"];
			$eltype = strtolower(str_replace("CL_PROPERTY_","",$clinf[$el_clid]["def"]));
			$sys_name = $this->gen_valid_id($el["name"]);
			/* for SQL tables, feel free to override those */
			// XXX: figure out a way to specify field types, lengths and possibly indexes in property definitions
			$field_type = "char";
			$field_length = 255;

			$can_save = in_array($el_clid,$this->saveable);

			if ($el_clid == CL_PROPERTY_CHECKBOX)
			{
				$field_type = "int";
				$field_length = "11";
			};

			if ($can_save)
			{
				// I cannot rely on using property caption as the field name,
				// because captions can be changed, but fields should not
				$tabledef[$sys_name] = array(
					"proptype" => $eltype,
					//"field" => "el_" . $el_id,
					"field_name" => $sys_name,
					"table" => $sql_table,
					// nii .. siit tuleb kuidagi teada saada igale väljale vajalik info suuruse kohta
					"field_type" => $field_type,
					"field_length" => $field_length,
					"el_id" => $el["id"],
				);
			};
		};

		return $tabledef;
	}

	// Generates SQL to alter the table
	// id - designer object id

	// it can return an empty string, in which case the table does not need altering
	function gen_alter_sql($arr)
	{
		$change_table = $arr["change_table"];
		$designer = new object($arr["id"]);
		$tabledef = $this->gen_tabledef(array(
			"obj_inst" => $designer,
			"cl_prop_only" => 1,
		));

		// reg_els contains id => name pairs of all CL_PROPERTY-s for this designer
		$reg_els = $designer->meta("registered_elements");

		$alter = "";

		foreach($tabledef as $el_id => $el_dat)
		{
			$tables[$el_dat["table"]][] = array(
				"field_name" => $el_dat["field_name"],
				// aga kuidagi on vaja kindlaks teha see, mis tüüpi väli teha
				"field_type" => $el_dat["field_type"],
				"field_length" => $el_dat["field_length"],
				"el_id" => $el_dat["el_id"],
			);
		};

		foreach($tables as $name => $fields)
		{
			// kõigepealt vaatame, kas nimetatud tabel on üldse olemas
			$sql = "DESCRIBE `$name`";
			$this->db_query($sql,false);
			$prev_table = array();
			while ($row = $this->db_next())
			{
				list($main,$extras) = explode(" ",$row["Type"]);
				preg_match("/(\w*)\((\d*)/",$main,$matches);
				$prev_table[$row["Field"]] = array(
					"field_type" => $matches[1],
					"field_length" => $matches[2],
					"field_extras" => $extras,
				);
			};

			array_unshift($fields,array(
				"field_name" => "aw_id",
				"field_type" => "bigint",
				"field_length" => 20,
				"field_extras" => "unsigned",
				"key" => "primary",
			));

			$columns = array();

			foreach($fields as $key => $val)
			{
				$field_name = $val["field_name"];
				$field_type = $val["field_type"];
				$field_length = $val["field_length"];
				$el_id = $val["el_id"];
				// if it is not in the existing table and is not registerd either, then it
				// must be a new name
				if (empty($prev_table[$field_name]) && !$reg_els[$el_id])
				{
					// add column
					$alter = " ADD `${field_name}` ${field_type}";
					if (!empty($field_length))
					{
						$alter .= "(" . $field_length . ")";
					};
					$reg_els[$el_id] = $field_name;
					$columns[] = $alter;
				}
				else
				{
					// nüüd peab võrdlema vana ja uut tüüpi

					// kui elementi pole regels sees, siis tuleb nimi ära muuta
					$prev = $prev_table[$field_name];
					if ( 	($prev["field_type"] != $field_type) ||
						($prev["field_length"] != $field_length) ||
						($field_name != $reg_els[$el_id])
					)
					{
						//print "el_id = $el_id<br>";
						//print "field_name = $field_name<br>";
						//var_dump($el_id);
						//var_dump($reg_els[$el_id]);
						$old_name = $field_name;
						if ($reg_els[$el_id])
						{
							$old_name = $reg_els[$el_id];
						};
						$change = " CHANGE `${old_name}` `${field_name}` ${field_type}";
						if (!empty($field_length))
						{
							$change .= "(" . $field_length . ")";
						};

						$columns[] = $change;
						$reg_els[$el_id] = $field_name;
					};
				};

			};

			if (sizeof($columns) > 0)
			{
				$alter = "ALTER TABLE `$name` " . join(",\n",$columns);
				//print "alt = ";
				//print $alter;
			};

			if ($change_table)
			{
				$this->db_query($alter);
				$designer->meta("registered_elements",$reg_els);
				$designer->save();
			};


		};

		return $alter;
	}

	// id - designer object id
	function gen_create_sql($arr)
	{
		$designer = new object($arr["id"]);
		$tabledef = $this->gen_tabledef(array(
			"obj_inst" => $designer,
		));

		foreach($tabledef as $el_id => $el_dat)
		{
			$tables[$el_dat["table"]][] = array(
				"field_name" => $el_dat["field_name"],
				// aga kuidagi on vaja kindlaks teha see, mis tüüpi väli teha
				"field_type" => $el_dat["field_type"],
				"field_length" => $el_dat["field_length"],
			);
		};

		foreach($tables as $name => $fields)
		{
			// kõigepealt vaatame, kas nimetatud tabel on üldse olemas
			$create .= "CREATE TABLE `$name` ( \n";
			array_unshift($fields,array(
				"field_name" => "aw_id",
				"field_type" => "bigint",
				"field_length" => 20,
				"field_extras" => "unsigned",
				"key" => "primary",
			));
			$first = true;

			$add_columns = $change_columns = $drop_columns = array();

			foreach($fields as $key => $val)
			{
				$field_name = $val["field_name"];
				$field_type = $val["field_type"];
				$field_length = $val["field_length"];
				$sql = sprintf("`%s` %s",$val["field_name"],$val["field_type"]);
				if (!empty($val["field_length"]))
				{
					$sql .= "(" . $val["field_length"] . ")";
				};
				if (!empty($val["field_extras"]))
				{
					$sql .= " " . $val["field_extras"];
				};
				$sql_parts[] = $sql;
				if (!empty($val["key"]) && "primary" == $val["key"])
				{
					$keys .= ",\n PRIMARY KEY (`" . $val["field_name"] . "`)";

				};
			};

			$create .= join(",\n",$sql_parts);
			$create .= $keys . ");\n";
		};

		return $create;
	}


	function get_class_elements($arr)
	{
		$c = $arr["obj_inst"];

		$cltree = new object_tree(array(
			"parent" => $c,
		));
		$cl_list = $cltree->to_list();

		$this->__elord = $c->meta("element_ords");
		$ellist = $cl_list->arr();
		usort($ellist, array($this, "__ellist_comp"));

		$rv = array();

		foreach($ellist as $el)
		{
			$el_clid = $el->class_id();
			$el_id = $el->id();
			if (CL_PROPERTY == $el_clid)
			{
				if (!is_oid($el->prop("property_type")))
				{
					continue;
				};

				$el_clid = $el->prop("property_type");
			};
			$name = $el->name();
			// I need a fully qualified name, or there will be unparseable code
			if (empty($name))
			{
				continue;
			};

			if (CL_PROPERTY != $el_clid && $arr["cl_prop_only"])
			{
				continue;
			};

			$props = $el->properties();
			$props["class_id"] = $el_clid;
			$props["id"] = $el_id;

			$rv[] = $props;
		};
		return $rv;
	}

	function gen_classdef($arr)
	{
		$c = $arr["obj_inst"];
		$ellist = $this->get_class_elements($arr);

		if ($c->prop("from_existing_class") == 1)
		{
			return $this->gen_classdef_from_existing($arr);
		}

		$rv = "";
		$grps = "";
		$clinf = aw_ini_get("classes");

		$cfgu = get_instance("cfg/cfgutils");
		$clname = $this->gen_valid_id($c->name());

		$path = aw_ini_get("basedir") . "/install/class_template/classes/base.aw";
		$clsrc = file_get_contents($path);
		$clid = "CL_" . strtoupper($this->gen_valid_id($c->name()));

		$clid = $c->prop("reg_class_id");



		$clsrc = str_replace("__classname",$clname,$clsrc);
		$clsrc = str_replace("__name",$c->name(),$clsrc);
		$clsrc = str_replace("__classdef",$clid,$clsrc);

		$gpblock = $spblock = "";
		$methods = "";

		// sort elements according to order in metadata

		$saver = "";

		$name_prop = $c->prop("object_name");

		$allowed = $this->elements;
		$allowed[] = CL_PROPERTY;

		$this->tabledef = array();

		$sql_table = "class_${clid}_objects";
		$gen_table = false;
		foreach($ellist as $el)
		{
			$el_clid = $el["class_id"];
			$el_id = $el["id"];
			if (in_array($el_clid,$allowed))
			{
				$name = $el["name"];
				$can_save = in_array($el_clid,$this->saveable);
				$parent = new object($el["parent"]);
				$grandparent = new object($parent->parent());
				$sys_name = $this->gen_valid_id($name);
				$group_name = $this->gen_valid_id($grandparent->name());

				if ($grandparent->class_id() == CL_PROPERTY_GRID)
				{
					$grandgrandparent = new object($grandparent->parent());
					$group_name = $this->gen_valid_id($grandgrandparent->name());
				}
				// this is not correct
				$eltype = strtolower(str_replace("CL_PROPERTY_","",$clinf[$el_clid]["def"]));
				if ($eltype == "tree")
				{
					$eltype = "treeview";
				}
				$rv .= "@property ${sys_name} type=${eltype} group=${group_name}";
				if ($parent->class_id() == CL_PROPERTY_GRID)
				{
					$grid_name = $parent->id();
					$rv .= " parent=" . $grid_name;
				};
				if ($grandparent->class_id() == CL_PROPERTY_GRID)
				{
					$grid_name = $this->gen_valid_id($grandparent->name());
					$rv .= " parent=" . $grid_name;
				};

				// and last and foremost .. I need to match old and new names for each property
				$inst = get_instance($el["class_id"]);
				//$el->instance();
				$generate_methods = array();

				if (method_exists($inst,"generate_get_property"))
				{
					$gpdata = $inst->generate_get_property(array(
						"id" => $el_id,
						"name" => $sys_name,
					));
					if (strlen($gpdata["get_property"]) > 0)
					{
						$gpblock .= $gpdata["get_property"];
					};
					if (is_array($gpdata["generate_methods"]))
					{
						$generate_methods = array_merge($generate_methods,$gpdata["generate_methods"]);
					};
			};

				// nii .. midagi peaks nende asjadega ka ette võtma, sest vastutavad klassid peaks
				// ise oma propertydefinitsioonid kirjutama
				if ($el_clid == CL_PROPERTY_CHOOSER)
				{
					if ($el["orient"] == 1)
					{
						$rv .= " orient=vertical";
					};
					if ($el["multiple"] == 1)
					{
						$rv .= " multiple=1";
					};
				};

				if ($el_clid == CL_PROPERTY_TEXTBOX)
				{
					if ($el["size"])
					{
						$rv .= " size=" . $el["size"];
					};
				};

				if ($can_save)
				{
					$gen_table = true;
					$rv .= " table=${sql_table}";
				};

				$rv .= "\n";
				$rv .= "@caption $name\n\n";


				if (sizeof($generate_methods) > 0 && method_exists($inst,"generate_method"))
				{
					foreach($generate_methods as $method_name)
					{
						$methods .= $inst->generate_method(array(
							"id" => $el_id,
							"name" => $method_name,
						));
					};
					//print "additionally generate methods";
					//arr($generate_methods);
				};

				if ($can_save)
				{
					if ($el_id == $name_prop)
					{
						$saver .= "\t\t\$o->set_name(\$arr[\"request\"][\"${sys_name}\"]);\n";
					};
				};

			};
			if ($el_clid == CL_PROPERTY_GROUP)
			{
				$grpid = $this->gen_valid_id($name);
				$grps .= "@groupinfo $grpid caption=\"".($el["caption"] != "" ? $el["caption"] : $name)."\"\n";
			};

			if ($el_clid == CL_PROPERTY_GRID)
			{
				$parent_o = new object($el["parent"]);
				$p_clid = $parent_o->class_id();
				$p_id = $this->gen_valid_id($parent_o->name());
				$group = "";
				$grid_type = ($el["grid_type"] == 0) ? "hbox" : "vbox";
				$el_id = $this->gen_valid_id($el["name"]) . $el["id"];
				if ($p_clid == CL_PROPERTY_GROUP)
				{
					$group = "group=$p_id";
				};
				$rv .= "@layout $el_id type=${grid_type} $group\n";

				// @layout hbox_oc type=hbox group=order_orderer_cos

				//arr($el->properties());
			};
		};

		if ($c->prop("relationmgr") == 1)
		{
			$gpblock .= "\n\t\t\tcase 'relationmgr':\n";
			$gpblock .= "\t\t\t\t\$this->configure_relationmgr(\$arr);\n";
			$gpblock .= "\t\t\t\t\$retval = PROP_OK;\n";
			$gpblock .= "\t\t\t\tbreak;\n";

			$methods .= $this->generate_relationmgr_config($arr["obj_inst"]->id());

			$rv .= $this->generate_reltypes($arr["obj_inst"]->id());

		};

		if ($gen_table)
		{
			$grps .= "@tableinfo $sql_table index=aw_id master_table=objects master_field=brother_of\n";
		};

		$methods .= "\tfunction callback_pre_save(\$arr)\n";
		$methods .= "\t{\n";
		$methods .= "\t\t\$o = \$arr[\"obj_inst\"];\n";
		$methods .= $saver;
		$methods .= "\t}\n";
		$clsrc = str_replace("//-- get_property --//",$gpblock,$clsrc);
		$clsrc = str_replace("//-- set_property --//",$spblock,$clsrc);
		$clsrc = str_replace("/*/* --remove--","",$clsrc);
		$clsrc = str_replace("--remove-- */","",$clsrc);
		$clsrc = str_replace("//-- methods --//",$methods,$clsrc);
		$clsrc = str_replace("@default group=general",$rv . $grps,$clsrc);
		//$this->gen_tabledef(array());
		return $clsrc;
	}

	/** generates code for configuring relation manager
	**/
	function generate_relationmgr_config($id)
	{
		$o = new object($id);
		//$clsid = aw_global_get("class");
		$clsid = $o->id();
		$clso = new object($clsid);
		// iga seostatud objekt annab ühe välja vasakusse selecti
		$conns = $clso->connections_from(array(
			"type" => "RELTYPE_RELATION",
		));
		$classes = aw_ini_get("classes");
		$export_rels = array();
		$export_rel_names = array();
		$rv = "\tfunction configure_relationmgr(\$arr)\n";
		$rv .= "\t{\n";
		foreach($conns as $conn)
		{
			$relobj = $conn->to();
			$rel_id = $relobj->id();
			// iga iga relobj käest saame selle juurde kuuluva parempoolse selecti sisu
			$rclasses = $relobj->prop("r_class_id");
			//$export_rels[$relobj->id()]["capt_new_object"] = "Objekti tüüp";
			foreach($rclasses as $rclass)
			{
				$export_rels[$rel_id][$rclass] = $classes[$rclass]["name"];
				$rv .= "\t\t\$arr['prop']['configured_rels'][$rel_id][$rclass] = '" . $classes[$rclass]["name"] . "';\n";

			};
			$rv .= "\t\t\$arr['prop']['configured_rel_names'][" . $rel_id ."] = '" . $relobj->name() . "';\n";
			//$export_rel_names[$rel_id] = $relobj->name();
		};

		$rv .= "\t}\n\n";
		return $rv;

	}

	function generate_reltypes($id)
	{
		$o = new object($id);
		$clsid = $o->id();
		$clso = new object($clsid);
		// iga seostatud objekt annab ühe välja vasakusse selecti
		$conns = $clso->connections_from(array(
			"type" => "RELTYPE_RELATION",
		));
		$classes = aw_ini_get("classes");
		$export_rels = array();
		$export_rel_names = array();
		$clinf = aw_ini_get("classes");
		foreach($conns as $conn)
		{
			$rv .= "@reltype " . strtoupper($this->_valid_id($conn->prop("to.name"))) . " value=" . $conn->prop("id") . " clid=";
			$to = $conn->to();
			$r_class_ids = $to->prop("r_class_id");
			$cldefs = array();
			foreach($r_class_ids as $r_class_id)
			{
				$cldefs[] = $clinf[$r_class_id]["def"];

			};
			$rv .= join(",",$cldefs);
			$rv .= "\n";
			$rv .= "@caption " . $conn->prop("to.name") . "\n\n";
		}
		return $rv;


	}

	function _valid_id($src)
	{
		$rv = strtolower(preg_replace("/\s/","_",$src));
		$rv = preg_replace("/\W/","",$rv);
		return $rv;


	}

	/**
		@attrib name=delete
	**/
	function delete($arr)
	{
		$sel = $arr["sel"];
		if (is_array($sel))
		{
			foreach($sel as $oid)
			{
				$o = new object($oid);
				$o->delete();
			};
		};
		return $arr["return_url"];
	}

	/** cuts properties

		@attrib name=cut

	**/
	function do_cut($arr)
	{
		$_SESSION["cd_cut"] = safe_array($arr["sel"]);
		return $arr["return_url"];
	}

	/** pastes properties

		@attrib name=paste

	**/
	function do_paste($arr)
	{
		foreach(safe_array($_SESSION["cd_cut"]) as $oid)
		{
			if (is_oid($oid) && $this->can("edit", $oid))
			{
				$o = obj($oid);
				$o->set_parent($arr["group_parent"]);
				$o->save();
			}
		}
		$_SESSION["cd_cut"] = array();
		return $arr["return_url"];
	}

	function gen_folder_tree()
	{
		$folders = aw_ini_get("classfolders");
		$this->by_parent = array();
		foreach($folders as $id => $folderdat)
		{
			$this->by_parent[$folderdat["parent"]][$id] = $folderdat["name"];
		};
		$this->level = -1;
		return $this->_rec_folder_tree(0);
	}

	function _rec_folder_tree($parent)
	{
		$this->level++;
		$rv = array();
		foreach ($this->by_parent[$parent] as $key => $val)
		{
			$spacer = str_repeat("&nbsp;",$this->level*4);
			$rv[$key] = $spacer .= $val;
			if ($this->by_parent[$key])
			{
				$rv = $rv + $this->_rec_folder_tree($key);
			};
		};
		$this->level--;
		return $rv;


	}

	// register class in central register
	function do_register_class($arr)
	{
		$c = $arr["obj_inst"];
		$class_name = $this->_valid_id($c->name());
		$clid = "CL_" . strtoupper($class_name);
		$st = "ST_" . strtoupper($class_name);
		$class = array(
			"def" => $clid,
			"file" => $class_name,
			"syslog_type" => $st,
			"name" => $c->name(),
		);
		$classlist = get_instance("core/class_list");
		$new_clid = $classlist->register_new_class_id(array(
			"data" => $class
		));

		$c->set_prop("reg_class_id",$new_clid);

	}

	function callback_post_save($arr)
	{
		if ($arr["request"]["register_under"])
		{
			$arr["obj_inst"]->set_meta("register_under", $arr["request"]["register_under"]);
			$arr["obj_inst"]->save();
		}

		// I need to put class information into an ini file as well
		//print "generating class file";
		$cldef = $this->gen_classdef(array(
			"obj_inst" => $arr["obj_inst"],
		));

		$fld = $this->gen_folder;
		if (is_writable($fld))
		{
			$this->put_file(array(
				"file" => $fld . "/" . $arr["obj_inst"]->id() . ".aw",
				"content" => $cldef,
			));

			// generate class information for generated classes as well
	                $clist = new object_list(array(
				"class_id" => CL_CLASS_DESIGNER,
			//	"can_add"  => 1,
                	));

			$ini_file = "";

			// def, name, file, can_add, alias, parents
			foreach($clist->arr() as $class_obj)
			{
				$clid = $class_obj->prop("reg_class_id");
				$prefix = "classes[$clid]";

				if ($class_obj->prop("from_existing_class") == 1)
				{
					$ini_file .= $prefix."[generated] = 1\n";
					$ini_file .= $prefix."[file] = ".$class_obj->id()."\n";
					$ini_file .= $prefix."[orig_file] = ".$class_obj->prop("from_existing_class_file")."\n";
					continue;
				}

				$ini_file .= $prefix . "[def] = " . "CL_" . $clid . "\n";
				$ini_file .= $prefix . "[name] = " . $class_obj->name() . "\n";
				$ini_file .= $prefix . "[file] = " . $class_obj->id() . "\n";
				$ini_file .= $prefix . "[parents] = " . $class_obj->prop("class_folder") . "\n";
				// maybe I do not need this .. don't know right now
				$ini_file .= $prefix . "[generated] = 1\n";
				$ini_file .= $prefix . "[can_add] = " . $class_obj->prop("can_add") . "\n\n";
			};

			$this->put_file(array(
				"file" => $fld . "/" . "generated_classes.ini",
				"content" => $ini_file,
			));

		};

		//print "done<br>";

	}

	function do_int_refs_table(&$arr)
	{
		$ot = new object_tree(array(
			"parent" => $arr["obj_inst"]->id(),
		));

		$ol = $ot->to_list();
		$int_refs = array();
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));
		foreach($ol->arr() as $o)
		{
			if ($o->class_id() == CL_PROPERTY && $o->prop("int_ref") == 1)
			{
				$t->define_data(array(
					"id" => $o->prop("id"),
					"name" => $o->name(),
				));
			};
		};
	}

	function do_ext_refs_table(&$arr)
	{
		$ot = new object_tree(array(
			"parent" => $arr["obj_inst"]->id(),
		));

		$ol = $ot->to_list();
		$int_refs = array();
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));
		foreach($ol->arr() as $o)
		{
			if ($o->class_id() == CL_PROPERTY && $o->prop("ext_ref") == 1)
			{
				$t->define_data(array(
					"id" => $o->prop("id"),
					"name" => $o->name(),
				));
			};
		};


	}

	function do_el_defs_toolbar(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->add_button(array(
			"name" => "save",
			"tooltip" => t("Salvesta"),
			"action" => "submit",
			"img" => "save.gif",
		));
	}

	function do_int_refs_toolbar(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->add_button(array(
			"name" => "save",
			"tooltip" => t("Salvesta"),
			"action" => "submit",
			"img" => "save.gif",
		));
	}

	function do_ext_refs_toolbar(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->add_button(array(
			"name" => "save",
			"tooltip" => t("Salvesta"),
			"action" => "submit",
			"img" => "save.gif",
		));
	}

	function do_el_defs_table(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));
		$t->define_field(array(
			"name" => "type",
			"caption" => t("Tüüp"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "int_ref",
			"caption" => t("Sisemine seos"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "ext_ref",
			"caption" => t("Väline seos"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "edit",
			"caption" => t("Muuda"),
			"align" => "center",
		));

		$tree_parent = is_oid($arr["request"]["group_parent"]) ? $arr["request"]["group_parent"] : $arr["obj_inst"]->id();

		$ot = new object_tree(array(
			"parent" => $tree_parent,
		));
		$clinf = aw_ini_get("classes");
		$clist = $this->elements;
		$cldat = array();
		$cldat[0] = t("--vali--");
		foreach($clist as $cl)
		{
			$cldat[$cl] = $clinf[$cl]["name"];
		};

		$ol = $ot->to_list();
		foreach($ol->arr() as $o)
		{
			if ($o->class_id() == CL_PROPERTY)
			{
				$oid = $o->id();
				$t->define_data(array(
					"id" => $oid,
					"name" => $o->name(),
					"type" => html::select(array(
						"name" => "property_type[" . $oid . "]",
						"options" => $cldat,
						"value" => $o->prop("property_type"),
					)),
					"int_ref" => html::checkbox(array(
						"name" => "int_ref[" . $oid . "]",
						"value" => 1,
						"checked" => $o->prop("int_ref"),
					)),
					"ext_ref" => html::checkbox(array(
						"name" => "ext_ref[" . $oid . "]",
						"value" => 1,
						"checked" => $o->prop("ext_ref"),
					)),
					"edit" => html::href(array(
						"url" => $this->mk_my_orb("change",array("id" => $o->id()),CL_PROPERTY),
						"caption" => t("Muuda"),
					)),
				));
			};
		}

	}

	function do_planner_list(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "id",
			"caption" => t("ID"),
		));
		$t->define_field(array(
			"name" => "parent",
			"caption" => t("Parent"),
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));
		$t->define_field(array(
			"name" => "type",
			"caption" => t("Tüüp"),
		));
		$group_parent = is_oid($arr["request"]["group_parent"]) ? $arr["request"]["group_parent"] : $arr["obj_inst"]->id();
		$ot = new object_tree(array(
			"parent" => $group_parent,
		));
		$ol = $ot->to_list();
		$clinf = aw_ini_get("classes");
		foreach($ol->arr() as $o)
		{
			$clid = $o->class_id();
			if ($clid == CL_PROPERTY_GRID || $clid == CL_PROPERTY_GROUP)
			{
				continue;
			};
			$parent_o =  new object($o->parent());

			$t->define_data(array(
				"name" => parse_obj_name($o->name()),
				"id" => $o->id(),
				"parent" => parse_obj_name($parent_o->name()),
				"type" => "", //$clinf[$o->prop("property_type")]["name"], // property property_type puudub -- voldemar 13 juuni 2012
			));
		}
	}

	function update_el_defs_table($arr)
	{
		$proptypes = $arr["request"]["property_type"];
		$int_ref = $arr["request"]["int_ref"];
		$ext_ref = $arr["request"]["ext_ref"];
		if (is_array($proptypes))
		{
			$inst = get_instance(CL_PROPERTY);
			foreach($proptypes as $prop_id => $proptype)
			{
				$po = new object($prop_id);
				$rv = $inst->submit(array(
					"id" => $prop_id,
					"name" => $po->name(),
					"property_type" => $proptype,
					"int_ref" => $int_ref[$prop_id],
					"ext_ref" => $ext_ref[$prop_id],
					"return" => "id",
				));
				//print "finished updating $rv<br>";
			};
		};
		return $arr["request"]["return_url"];
	}

	function gen_classdef_from_existing($arr)
	{
		$cd = $arr["obj_inst"];

		// read in current class
		$clss = aw_ini_get("classes");
		$fn = aw_ini_get("classdir")."/".$clss[$cd->prop("reg_class_id")]["orig_file"].".".aw_ini_get("ext");

		$src = $this->get_file(array("file" => $fn));

		// insert new ones
		$proplist = $this->_get_cd_props_as_list($cd);

		// filter out props that are not created by class_designer
		foreach($proplist->arr() as $prop)
		{
			if (!count($prop->connections_to(array("from.class_id" => CL_PROPERTY))))
			{
				$proplist->remove($prop->id());
			}
		}

		// insert properties defined from class_designer
		// find prop defs end
		$lines = $this->gcdfe_insert_props(explode("\n", $src),$cd, $proplist);

		// insert get_prop / set_prop classdesigner callbacks
			// find get_property start/end
			// insert cd call in the beginning
		list($get_prop, $methods) = $this->generate_get_property_from_property_list($proplist);

		if ($get_prop != "")
		{
			$lines = $this->gcdfe_insert_gp_block($lines, $get_prop);
		}

		// generate methods to the end of the class as usual
		if ($methods != "")
		{
			// find end of class
			// insert new funcs
			$lines = $this->_insert_at_end($lines, $methods);
		}
		$src = join("\n", $lines);

		$orig_name = basename($clss[$cd->prop("reg_class_id")]["orig_file"]);
		return $src;//str_replace("class $orig_name extends class_base", "class ".$cd->name()." extends class_base", $src);
	}

	function gcdfe_insert_props($lines, $cd, $proplist)
	{
		foreach($lines as $num => $line)
		{
			if (strpos($line, "@property") !== false)
			{
				if (strpos($lines[$num+1], "@comment") !== false)
				{
					$last_prop_line = $num+1;
				}
				else
				{
					$last_prop_line = $num+1;
				}

				if (trim($lines[$last_prop_line+1]) == "")
				{
					$last_prop_line++;
				}
			}
		}

		$propstr = explode("\n", $this->generate_property_defs_from_property_list($proplist, $cd));
		$grpstr = explode("\n", $this->generate_group_defs_from_designer($cd));

		$tmp = array();
		foreach($lines as $num => $line)
		{
			if ($num == ($last_prop_line+1))
			{
				foreach($propstr as $pl)
				{
					$tmp[] = $pl;
				}
				foreach($grpstr as $pl)
				{
					$tmp[] = $pl;
				}

			}
			$tmp[] = $line;
		}
		return $tmp;
	}

	function _get_cd_props_as_list($o)
	{
		$cltree = new object_tree(array(
			"parent" => $o,
		));
		return $cltree->to_list();
	}

	function generate_property_defs_from_property_list($list, $cd)
	{
		$allowed = $this->elements;
		$allowed[] = CL_PROPERTY;
		$clinf = aw_ini_get("classes");
		foreach($list->arr() as $el)
		{
			$el_clid = $el->class_id();
			if (CL_PROPERTY == $el_clid)
			{
				if (!is_oid($el->prop("real_property")))
				{
					continue;
				};

				$el_clid = $el->prop("real_property");
			};
			$name = $el->name();
			// I need a fully qualified name, or there will be unparseable code

			if (empty($name))
			{
				continue;
			};
			if (in_array($el_clid,$allowed))
			{
				$parent = new object($el->parent());
				$grandparent = new object($parent->parent());
				$sys_name = $this->_valid_id($name);
				$group_name = $this->_valid_id($grandparent->name());
				if ($grandparent->class_id() == CL_PROPERTY_GRID)
				{
					$grandgrandparent = new object($grandparent->parent());
					$group_name = $this->_valid_id($grandgrandparent->name());
				}
				// this is not correct
				$eltype = strtolower(str_replace("CL_PROPERTY_","",$clinf[$el_clid]["def"]));
				if ($eltype == "tree")
				{
					$eltype = "treeview";
				}
				$rv .= "@property ${sys_name} type=${eltype} group=${group_name}";
				if ($parent->class_id() == CL_PROPERTY_GRID)
				{
					$grid_name = $parent->id();
					$rv .= " parent=" . $grid_name;
				};
				if ($grandparent->class_id() == CL_PROPERTY_GRID)
				{
					$grid_name = $this->_valid_id($grandparent->name());
					if (!($grid_name == "default" && $cd->prop("from_existing_class")))
					{
						$rv .= " parent=" . $grid_name;
					}
				};
				$inst = $el->instance();

				// nii .. midagi peaks nende asjadega ka ette võtma, sest vastutavad klassid peaks
				// ise oma propertydefinitsioonid kirjutama
				if ($el_clid == CL_PROPERTY_CHOOSER)
				{
					if ($el->prop("orient") == 1)
					{
						$rv .= " orient=vertical";
					};
					if ($el->prop("multiple") == 1)
					{
						$rv .= " multiple=1";
					};
				};

				if ($el_clid == CL_PROPERTY_TEXTBOX)
				{
					if ($el->prop("size"))
					{
						$rv .= " size=" . $el->prop("size");
					};
				};
				$rv .= "\n";
				$rv .= "@caption $name\n\n";

			};
			if ($el_clid == CL_PROPERTY_GROUP)
			{
				$grpid = $this->_valid_id($name);
				$grps .= "@groupinfo $grpid caption=\"".($el->prop("caption") != "" ? $el->prop("caption") : $name)."\"\n";
			};

			if ($el_clid == CL_PROPERTY_GRID)
			{
				$parent_o = new object($el->parent());
				$p_clid = $parent_o->class_id();
				$p_id = $this->_valid_id($parent_o->name());
				$group = "";
				$grid_type = ($el->prop("grid_type") == 0) ? "hbox" : "vbox";
				$el_id = $this->_valid_id($el->name());
				$el_id .= $el->id();
				if ($p_clid == CL_PROPERTY_GROUP)
				{
					$group = "group=$p_id";
				};
				$rv .= "@layout $el_id type=${grid_type} $group\n";

				// @layout hbox_oc type=hbox group=order_orderer_cos

				//arr($el->properties());
			};
		};
		return $rv;
	}

	function generate_get_property_from_property_list($list)
	{
		$allowed = $this->elements;
		$allowed[] = CL_PROPERTY;

		$generate_methods = array();

		foreach($list->arr() as $el)
		{
			$el_clid = $el->class_id();
			if (CL_PROPERTY == $el_clid)
			{
				if (!is_oid($el->prop("real_property")))
				{
					continue;
				};

				$el_clid = $el->prop("real_property");
			};
			$name = $el->name();
			// I need a fully qualified name, or there will be unparseable code
			if (empty($name))
			{
				continue;
			};
			if (in_array($el_clid,$allowed))
			{
				$sys_name = $this->_valid_id($name);
				$inst = $el->instance();
				$generate_methods = array();

				if (method_exists($inst,"generate_get_property"))
				{
					$gpdata = $inst->generate_get_property(array(
						"id" => $el->id(),
						"name" => $sys_name,
					));
					if (strlen($gpdata["get_property"]) > 0)
					{
						$gpblock .= $gpdata["get_property"];
					};
					if (is_array($gpdata["generate_methods"]))
					{
						$generate_methods = array_merge($generate_methods,$gpdata["generate_methods"]);
					};
				};

				if (sizeof($generate_methods) > 0 && method_exists($inst,"generate_method"))
				{
					foreach($generate_methods as $method_name)
					{
						$methods .= $inst->generate_method(array(
							"id" => $el_id,
							"name" => $method_name,
						));
					};
					//print "additionally generate methods";
					//arr($generate_methods);
				};

			};
		};

		return array($gpblock, $methods);
	}

	function gcdfe_insert_gp_block($lines, $get_prop)
	{
		// find get_property method from class
		$ret = array();
		$cnt = count($lines);
		for($i = 0; $i < $cnt; $i++)
		{
			$line = $lines[$i];

			if (preg_match("/function\s+get_property/", $line))
			{
				$ret[] = $line;
				$ret[] = $lines[$i+1];
				$ret[] = "\t\t\$this->class_designer_get_property(\$arr);";
				$i++;
				continue;
			}

			if ($i == ($cnt-3))
			{
				$ret[] = "";
				$ret[] = "\tfunction class_designer_get_property(\$arr)";
				$ret[] = "\t{";
				$ret[] = "\t\t\$prop =& \$arr[\"prop\"];";
				$ret[] = "\t\tswitch(\$prop[\"name\"])";
				$ret[] = "\t\t{";
				$ret[] = $get_prop;
				$ret[] = "\t\t}";
				$ret[] = "\t}";
				$ret[] = "";
			}
			$ret[] = $line;
		}
		return $ret;
	}

	function _insert_at_end($lines, $methods)
	{
		// find get_property method from class
		$ret = array();
		$cnt = count($lines);
		for($i = 0; $i < $cnt; $i++)
		{
			$line = $lines[$i];

			if ($i == ($cnt-3))
			{
				foreach(explode("\n", $methods) as $nl)
				{
					$ret[] = $nl;
				}
			}
			$ret[] = $line;
		}
		return $ret;
	}

	function generate_group_defs_from_designer($cd)
	{
		$tree = new object_tree(array(
			"parent" => $cd->id(),
			"class_id" => CL_PROPERTY_GROUP
		));

		$cfgu = get_instance("cfg/cfgutils");

		$list = $tree->to_list();
		$grps = array();
		foreach($list->arr() as $o)
		{
			$grpid = $this->gen_valid_id($o->name());
			$grps[$grpid] = array(
				"caption" => $o->prop("caption") == "" ? $o->name() : $o->prop("caption")
			);
		}

		// get existing groups from the current class
		if ($cd->prop("from_existing_class") == 1)
		{
			$existing_groups = $this->get_groups_from_existing_class($cd);
			foreach($existing_groups as $gn => $gd)
			{
				unset($grps[$gn]);
			}
		}

		// generate
		$ret = "";
		foreach($grps as $gn => $gd)
		{
			$ret .= "@groupinfo $gn caption=\"".$gd["caption"]."\"\n";
		}
		return $ret;
	}

	function get_groups_from_existing_class($cd)
	{
		$anal = get_instance("cfg/propcollector");

		$cb_inf = $anal->parse_file(array(
			"file" => aw_ini_get("classdir")."/class_base.aw"
		));

		$clss = aw_ini_get("classes");
		$old = $clss[$cd->prop("reg_class_id")]["orig_file"];

		$inf = $anal->parse_file(array(
			"file" => aw_ini_get("classdir")."/".$old.".aw"
		));

		$grpi = $cb_inf["properties"]["groupinfo"];
		foreach(safe_array($inf["properties"]["groupinfo"]) as $gn => $gi)
		{
			$grpi[$gn] = $gi;
		}
		return $grpi;
	}
}
