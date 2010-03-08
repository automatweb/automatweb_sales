<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/register/register.aw,v 1.26 2008/06/06 08:07:11 kristo Exp $
// register.aw - Register 
/*
 
@classinfo syslog_type=ST_REGISTER relationmgr=yes no_status=1  maintainer=kristo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property data_cfgform type=relpicker reltype=RELTYPE_CFGFORM multiple=1
@caption Andmete seadete vorm

@property default_cfgform type=checkbox ch_value=1
@caption Tee seadete vorm andmetele default'iks

@property data_return_url type=textbox
@caption Kuhu &uuml;mbersuunata peale andmete salvestamist

@property data_rootmenu type=relpicker reltype=RELTYPE_MENU
@caption Andmete kataloog

@property data_tree_field type=select
@caption Andmete puu struktuuri v&auml;li

@property search_o type=relpicker reltype=RELTYPE_SEARCH
@caption Otsingu konfiguratsioon

@property show_all type=checkbox ch_value=1
@caption Kui pole valitud, n&auml;ita k&otilde;iki sisestusi

@property per_page type=textbox size=5
@caption Mitu kirjet lehel

@property cfgform_name_in_field type=select
@caption Kirje lisamisel pane seadete vormi nimi v&auml;lja


@groupinfo data caption=Andmed
@default group=data

@property data_tb type=toolbar store=no no_caption=1

@layout datalt type=hbox group=data

@property data_tree type=text store=no no_caption=1 parent=datalt
@property data type=table store=no no_caption=1 parent=datalt

@groupinfo mail_send_settings caption="E-maili saatmise seaded"
@default group=mail_send_settings

@property mail_address_to type=relpicker multiple=1 reltype=RELTYPE_MAIL
@caption E-maili aadress, (to)

@property mail_address_from type=relpicker reltype=RELTYPE_MAIL
@caption E-maili aadress, (from)

@property mail_subject type=textbox
@caption Subject

@groupinfo search caption="Otsing" submit_method=get submit=no
@default group=search

@property search type=text store=no no_caption=1

@reltype CFGFORM value=1 clid=CL_CFGFORM
@caption andmete seadete vorm

@reltype MENU value=2 clid=CL_MENU
@caption andmete kataloog

@reltype SEARCH value=3 clid=CL_REGISTER_SEARCH
@caption registri otsing

@reltype MAIL value=4 clid=CL_ML_MEMBER
@caption e-maili aadress
*/

class register extends class_base
{
	function register()
	{
		$this->init(array(
			"tpldir" => "applications/register/register",
			"clid" => CL_REGISTER
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "data_tree_field":
				$prop["options"] = $this->get_chooser_elements($arr["obj_inst"]);
				break;

			case "data_tb":
				$this->do_data_toolbar($arr);
				break;

			case "data":
				$this->do_data_tbl($arr);
				break;

			case "data_tree":
				if (!$arr["obj_inst"]->prop("data_tree_field"))
				{
					return PROP_IGNORE;
				}
				$prop["value"] = $this->get_data_tree($arr["obj_inst"]);
				break;

			case "search":
				if (!$arr["obj_inst"]->prop("search_o"))
				{
					$prop["value"] = t("Otsingu konfiguratsioon valimatta!");
				}
				else
				{
					$s = get_instance(CL_REGISTER_SEARCH);
					$prop["value"] = $s->show(array(
						"id" => $arr["obj_inst"]->prop("search_o"),
						"no_form" => 1
					));
				}
				break;

			case "cfgform_name_in_field":
				$rs = get_instance(CL_REGISTER_SEARCH);
				$ps = $rs->get_props_from_reg($arr["obj_inst"]);
				$prop["options"] = array("" => "");
				foreach($ps as $pn => $pd)
				{
					$prop["options"][$pn] = $pd["caption"];
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
			case "data":
				$awa = new aw_array($arr["request"]["select"]);
				foreach($awa->get() as $k => $v)
				{
					if ($k == $v)
					{
						$o = obj($k);
						$o->delete();
					}
				}
				break;
		}
		return $retval;
	}	

	function do_data_toolbar($arr)
	{
		$tb =& $arr["prop"]["toolbar"];

		$tb->add_menu_button(array(
			"name" => "new",
			"tooltip" => t("Uus")
		));

		$awa = new aw_array($arr["obj_inst"]->prop("data_cfgform"));
		foreach($awa->get() as $cfid)
		{
			$o = obj($cfid);
			$tb->add_menu_item(array(
				"parent" => "new",
				"text" => $o->name(),
				"link" => $this->mk_my_orb("new", array(
					"cfgform" => $o->id(),
					"parent" => $arr["obj_inst"]->prop("data_rootmenu"),
					"return_url" => get_ru(),
					"cfgform" => $cfid,
					"set_register_id" => $arr["obj_inst"]->id(),
					"section" => aw_global_get("section")
				), CL_REGISTER_DATA)
			));
		}
	}

	function _init_data_tbl(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1
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
			"type" => "time",
			"format" => "d.m.Y H:i",
			"align" => "center"
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
			"type" => "time",
			"format" => "d.m.Y H:i",
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "change",
			"caption" => t("Muuda"),
			"align" => "center"
		));

		$t->define_chooser(array(
			"name" => "select",
			"field" => "oid"
		));
	}

	function do_data_tbl($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_data_tbl($t);
		$t->set_sortable(false);
		$filt = array(
			"class_id" => CL_REGISTER_DATA,
			"register_id" => $arr["obj_inst"]->id(),
		);

		if (!$arr["request"]["sortby"])
		{
			$arr["request"]["sortby"] = "objects.created";
		}
		if (!$arr["request"]["sort_order"])
		{
			$arr["request"]["sort_order"] = "desc";
		}

		$this->quote(&$arr["request"]["sortby"]);
		$this->quote(&$arr["request"]["sort_order"]);
		$filt["sort_by"] = $arr["request"]["sortby"]." ".$arr["request"]["sort_order"];

		if (($dtf = $arr["obj_inst"]->prop("data_tree_field")))
		{
			if ($arr["request"]["treefilter"])
			{
				if ($arr["request"]["treefilter"] != "__all__")
				{
					$filt[$dtf] = $arr["request"]["treefilter"];
				}
				$ol_cnt = new object_list($filt);

				if (($ppg = $arr["obj_inst"]->prop("per_page")))
				{
					$filt["limit"] = ($arr["request"]["ft_page"] * $ppg).",".$ppg;
				}
				$ol = new object_list($filt);
			}
			else
			{
				$ol = new object_list();
				$ol_cnt = new object_list();
			}
		}
		else
		{
			if ($arr["obj_inst"]->prop("show_all"))
			{
				$ol_cnt = new object_list($filt);
				if (($ppg = $arr["obj_inst"]->prop("per_page")))
				{
					$filt["limit"] = ($arr["request"]["ft_page"] * $ppg).",".$ppg;
				}
				$ol = new object_list($filt);
			}
			else
			{
				$ol = new object_list();
				$ol_cnt = new object_list();
			}
		}

		for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
		{
			$t->define_data(array(
				"oid" => $o->id(),
				"name" => $o->name(),
				"createdby" => $o->createdby(),
				"created" => $o->created(),
				"modifiedby" => $o->modifiedby(),
				"modified" => $o->modified(),
				"change" => html::href(array(
					"url" => $this->mk_my_orb("change", array(
						"section" => aw_global_get("section"), 
						"id" => $o->id(),
						"return_url" => html::get_change_url($arr["request"]["id"], array(
							"group" => $arr["request"]["group"],
						)),
					), $o->class_id()),
					"caption" => t("Muuda")
				))
			));
		}

		$t->sort_by();

		if ($arr["obj_inst"]->prop("per_page"))
		{
			$t->pageselector_string = $t->draw_text_pageselector(array(
				"d_row_cnt" => $ol_cnt->count(),
				"records_per_page" => $arr["obj_inst"]->prop("per_page")
			));
		}
	}

	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	////
	// !shows the register
	function show($arr)
	{
		if ($GLOBALS["print"] == 1)
		{
			return "";
		}
		$o = obj($arr["id"]);

		$html = "";
		if ($this->can("add", $o->prop("data_rootmenu")))
		{
			$tb = get_instance("vcl/toolbar");
			$this->do_data_toolbar(array(
				"prop" => array(
					"toolbar" => &$tb
				),
				"obj_inst" => $o
			));

			$html =  $tb->get_toolbar();
		}

		if ($o->prop("show_all"))
		{
			classload("vcl/table");
			$t = new aw_table(array(
				"layout" => "generic"
			));
		
			$this->do_data_tbl(array(
				"prop" => array(
					"vcl_inst" => &$t
				),
				"obj_inst" => $o,
				"request" => $GLOBALS
			));
			$html .=  $t->draw();
		}
	
		return $html ;
	}

	function get_chooser_elements($o)
	{
		$rs = get_instance(CL_REGISTER_SEARCH);
		$ps = $rs->get_props_from_reg($o);
		$clid = $rs->get_clid_from_reg($o);

		// load props for entire class, cause from cfgform we don't get all dat
		$cfgu = get_instance("cfg/cfgutils");
		$f_props = $cfgu->load_properties(array(
			"clid" => $clid
		));

		$ret = array("" => "");
		foreach($ps as $pn => $pd)
		{
			if ($f_props[$pn]["type"] == "classificator")
			{
				$ret[$pn] = $pd["caption"];
			}
		}

		return $ret;
	}

	function get_data_tree($o)
	{
		$t = get_instance("vcl/treeview");
		$t->start_tree(array(
			"root_name" => "K&otilde;ik",
			"root_url" => aw_url_change_var("treefilter", "__all__"),
			"has_root" => 1,
			"tree_id" => "register".$o->id(),
			"type" => TREE_DHTML,
			"persist_state" => 1
		));

		// get values from prop
		$clsf = get_instance(CL_CLASSIFICATOR);
		$vals = $clsf->get_options_for(array(
			"name" => $o->prop("data_tree_field"),
			"clid" => CL_REGISTER_DATA
		));
		
		// insert into tree
		foreach($vals as $v_id => $v_name)
		{
			$t->add_item(0, array(
				"id" => $v_id,
				"name" => $v_name,
				"url" => aw_url_change_var("treefilter", $v_id)
			));
		}

		return $t->finalize_tree(array(
		));
	}

	function _get_reg_folders($r)
	{
		$ot = new object_tree(array(
			"parent" => $r->prop("data_rootmenu"),
			"class_id" => CL_MENU
		));
		$tmp = $ot->ids();
		$tmp[] = $r->prop("data_rootmenu");
		return $tmp;
	}
}
?>
