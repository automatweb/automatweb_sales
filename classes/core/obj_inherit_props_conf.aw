<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/core/obj_inherit_props_conf.aw,v 1.6 2008/01/31 13:52:49 kristo Exp $
// obj_inherit_props_conf.aw - Objekti omaduste p&auml;rimine 
/*

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_SAVE, CL_OBJ_INHERIT_PROPS_CONF, on_save_conf)


@classinfo syslog_type=ST_OBJ_INHERIT_PROPS_CONF relationmgr=yes no_comment=1 no_status=1 maintainer=kristo

@default table=objects
@default group=general
@default field=meta

@groupinfo donor caption="Doonorklass" parent=general 
@groupinfo destination caption="Sihtklass" parent=general 

@property name type=textbox table=objects field=name group=donor
@caption Nimi


@property inherit_from type=table store=no no_caption=1 group=donor
@caption Vali doonorid

@property inherit_to type=table no_caption=1 group=destination
@caption Vali sihtobjektid


@groupinfo ihf caption="P&auml;rimine"
@groupinfo ihf_donor caption="Doonorklass" parent=ihf
@groupinfo ihf_destination caption="Sihtklass" parent=ihf

@layout ihf_donor_main type=hbox group=ihf_donor width=30%:70%
@layout ihf_donor_tree type=vbox parent=ihf_donor_main group=ihf_donor
@layout ihf_donor_table type=vbox parent=ihf_donor_main group=ihf_donor

@property ihf_donor_tree type=treeview store=no group=ihf_donor no_caption=1 parent=ihf_donor_tree
@property ihf_donor_tbl type=table store=no group=ihf_donor no_caption=1 parent=ihf_donor_table

@layout ihf_destination_main type=hbox group=ihf_destination width=30%:70%
@layout ihf_destination_tree type=vbox parent=ihf_destination_main group=ihf_destination
@layout ihf_destination_table type=vbox parent=ihf_destination_main group=ihf_destination

@property ihf_destination_tree type=treeview store=no group=ihf_destination no_caption=1 parent=ihf_destination_tree
@property ihf_destination_tbl type=table store=no group=ihf_destination no_caption=1 parent=ihf_destination_table


@groupinfo if_ov caption="Tingimused" submit=no

@property if_ov_toolbar type=toolbar store=no no_caption=1 group=if_ov
@property if_ov_table type=table store=no no_caption=1 group=if_ov


@reltype INHERIT_FROM value=1  clid=CL_MENU
@caption p&auml;ritav objekt

@reltype INHERIT_TO value=2  clid=CL_MENU,CL_SWOT_THREAT
@caption kirjutatav objekt

*/

class obj_inherit_props_conf extends class_base
{
	const AW_CLID = 834;

	function obj_inherit_props_conf()
	{
		$this->init(array(
			"tpldir" => "core/obj_inherit_props_conf",
			"clid" => CL_OBJ_INHERIT_PROPS_CONF
		));
	}

	function get_property($arr)
	{
		$this->tree_sel = $arr["request"]["tree_sel"];
		$this->tree_sel_right = $arr["request"]["tree_sel_right"];
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "if_ov_toolbar":
				$prop["toolbar"]->add_button(array(
					"name" => "del",
					"img" => "delete.gif",
					"tooltip" => t("Kustuta valitud"),
					"url" => "javascript:document.changeform.submit()"
				));
				break;

			case "ihf_donor_tree":
				$this->do_ihf_donor_tree($arr);
				break;

			case "ihf_donor_tbl":
				$this->do_ihf_donor_table($arr);
				break;

			case "ihf_destination_tree":
				$this->do_ihf_destination_tree($arr);
				break;

			case "ihf_destination_tbl":
				$this->do_ihf_destination_table($arr);
				break;

			case "if_ov_table":
				$this->do_if_ov_table($arr);
				break;

			case "inherit_from":
				$this->do_inherit_from($arr);
				break;

			case "inherit_to":
				$this->do_inherit_to($arr);
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
			case "ihf_donor_tbl":
				$cur_wd = $arr["obj_inst"]->meta("use_inherit_from_props");
				foreach(safe_array($arr["request"]["orig_wd"]) as $obj_id => $data)
				{
					foreach($data as $prop_k => $prop_v)
					{
						$cur_wd[$obj_id][$prop_k] = $arr["request"]["wd"][$obj_id][$prop_k];
					}
				}
				$arr["obj_inst"]->set_meta("use_inherit_from_props", $cur_wd);
				break;

			case "ihf_destination_tbl":
				$cur_wd = $arr["obj_inst"]->meta("use_inherit_to_props");
				foreach(safe_array($arr["request"]["orig_wd"]) as $obj_id => $data)
				{
					foreach($data as $prop_k => $prop_v)
					{
						$cur_wd[$obj_id][$prop_k] = $arr["request"]["wd"][$obj_id][$prop_k];
					}
				}
				$arr["obj_inst"]->set_meta("use_inherit_to_props", $cur_wd);
				break;

			case "if_ov_table":
				$cur_wd = $arr["obj_inst"]->meta("wd");
				$del = safe_array($arr["request"]["del_ifs"]);
				foreach($del as $from_prop)
				{
					unset($cur_wd[$from_prop]);
				}
				$arr["obj_inst"]->set_meta("wd", $cur_wd);
				break;

			case "inherit_from":
				$arr["obj_inst"]->set_meta("inherit_from_objs", $arr["request"]["use_data"]);
				break;

			case "inherit_to":
				$arr["obj_inst"]->set_meta("inherit_to_objs", $arr["request"]["use_data"]);
				break;
		}
		return $retval;
	}	

	function on_save_conf($arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_OBJ_INHERIT_PROPS_CONF,
			"lang_id" => array(),
			"site_id" => array()
		));

		$data = array();
		foreach($ol->arr() as $o)
		{
			if ($o->prop("inherit_from") && $o->prop("inherit_to_class"))
			{
				$wd = safe_array($o->meta("wd"));
				$tmp = array();

				foreach($wd as $from_prop => $to_prop)
				{
					if ($from_prop != "" && $to_prop != "")
					{
						
						$tmp[] = array(
							"from_prop" => $from_prop,
							"to_class" => $o->prop("inherit_to_class"),
							"to_prop" => $to_prop,
							"only_to_objs" => $this->make_keys($o->prop("inherit_to_objs"))
						);
					}
				}

				$data[$o->prop("inherit_from")] = $tmp;
			}
		}

		/*$this->put_file(array(
			"file" => aw_ini_get("site_basedir")."/files/obj_inherit_props.conf",
			"content" => aw_serialize($data)
		));*/
	}

	function do_ihf_destination_tree($arr)
	{
		// read properties		
		$ifos = $this->get_inherit_to_objs($arr["obj_inst"]);
		$this->_make_tree_from_ifos($ifos, $arr);
	}

	function _make_tree_from_ifos($ifos, $arr)
	{
		$tree =& $arr["prop"]["vcl_inst"];
		foreach($ifos as $ifo)
		{
			list($sel_o, $sel_g) = explode("|", $arr["request"]["tree_sel"]);

			$nm = $ifo->name();
			if ($sel_o == $ifo->id())
			{
				$nm = "<b>".$nm."</b>";
			}
			$tree->add_item(0, array(
				"id" => $ifo->id(),
				"name" => $nm,
				"url" => aw_url_change_var("tree_sel", $ifo->id()),
			));

			$cu = get_instance("cfg/cfgutils");
			$props = $cu->load_properties(array(
				"clid" => $ifo->class_id()
			));

			$grps = $cu->groupinfo;
			foreach($grps as $gn => $gd)
			{
				$cpat = $gd["caption"];

				if (($sel_g == $gn || $grps[$sel_g]["parent"] == $gn) && $sel_o == $ifo->id())
				{
					$cpat = "<b>".$cpat."</b>";
				}
				$tree->add_item(($gd["parent"] != "" ? $ifo->id()."grp_".$gd["parent"] : $ifo->id()), array(
					"id" => $ifo->id()."grp_".$gn,
					"name" => $cpat,
					"url" => aw_url_change_var("tree_sel", $ifo->id()."|".$gn),
				));
			}
		}
	}

	function do_ihf_donor_tree($arr)
	{
		// read properties		
		$ifos = $this->get_inherit_from_objs($arr["obj_inst"]);
		$this->_make_tree_from_ifos($ifos, $arr);
	}

	function _init_ihf_table(&$t)
	{
		$t->define_field(array(
			"name" => "prop",
			"caption" => t("Omadus")
		));
		$t->define_field(array(
			"name" => "type",
			"caption" => t("Omaduse t&uuml;&uuml;p"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "write_to",
			"caption" => t("Kasuta"),
			"align" => "center"
		));
	}

	function do_ihf_donor_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_ihf_table($t);

		if (!$arr["request"]["tree_sel"])
		{
			return;
		}

		list($sel_o, $sel_g) = explode("|", $arr["request"]["tree_sel"]);
		$sel_obj = obj($sel_o);

		$wd = $arr["obj_inst"]->meta("use_inherit_from_props");		

		// read properties		
		$cu = get_instance("cfg/cfgutils");
		$props = $cu->load_properties(array(
			"clid" => $sel_obj->class_id()
		));
		foreach($props as $pn => $pd)
		{
			if ($sel_g == $pd["group"])
			{
				$t->define_data(array(
					"prop" => $pd["caption"],
					"type" => $pd["type"],
					"write_to" => html::checkbox(array(
						"checked" => ($wd[$sel_obj->id()][$pn] == $pn),
						"value" => $pn,
						"name" => "wd[".$sel_obj->id()."][$pn]"
					)).html::hidden(array(
						"name" => "orig_wd[".$sel_obj->id()."][$pn]",
						"value" => 1
					))
				));
			}
		}
		$t->set_sortable(false);
	}

	function do_ihf_destination_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_ihf_table($t);

		if (!$arr["request"]["tree_sel"])
		{
			return;
		}

		list($sel_o, $sel_g) = explode("|", $arr["request"]["tree_sel"]);
		$sel_obj = obj($sel_o);

		$wd = $arr["obj_inst"]->meta("use_inherit_to_props");		

		$selectable_props = $this->get_inherit_from_sel_props($arr["obj_inst"]);

		// read properties		
		$cu = get_instance("cfg/cfgutils");
		$props = $cu->load_properties(array(
			"clid" => $sel_obj->class_id()
		));
		foreach($props as $pn => $pd)
		{
			if ($sel_g == $pd["group"])
			{
				$t->define_data(array(
					"prop" => $pd["caption"],
					"type" => $pd["type"],
					"write_to" => html::select(array(
						"selected" => ($wd[$sel_obj->id()][$pn]),
						"options" => $selectable_props,
						"name" => "wd[".$sel_obj->id()."][$pn]"
					)).html::hidden(array(
						"name" => "orig_wd[".$sel_obj->id()."][$pn]",
						"value" => 1
					))
				));
			}
		}
		$t->set_sortable(false);
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["tree_sel"] = $this->tree_sel;
		$arr["tree_sel_right"] = $this->tree_sel_right;
	}

	function callback_mod_retval($arr)
	{
		$arr["args"]["tree_sel"] = $arr["request"]["tree_sel"];
		$arr["args"]["tree_sel_right"] = $arr["request"]["tree_sel_right"];
	}

	function _init_if_ov_table(&$t)
	{
		$t->define_field(array(
			"name" => "from_class",
			"caption" => t("Mis klassist"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "from_prop",
			"caption" => t("Mis omadusest"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "to_class",
			"caption" => t("Mis klassi"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "to_prop",
			"caption" => t("Mis omadusse"),
			"align" => "center"
		));

		$t->define_chooser(array(
			"field" => "from_prop",
			"name" => "del_ifs"
		));
	}

	function do_if_ov_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_if_ov_table($t);

		$wd = safe_array($arr["obj_inst"]->meta("wd"));

		$cld = aw_ini_get("classes");
		$if = obj($arr["obj_inst"]->prop("inherit_from"));
		$from_nm = $cld[$if->class_id()]["name"];
		$to_nm = $cld[$arr["obj_inst"]->prop("inherit_to_class")]["name"];

		foreach($wd as $from_prop => $to_prop)
		{
			if ($from_prop != "" && $to_prop != "")
			{
				$t->define_data(array(
					"from_class" => $from_nm,
					"from_prop" => $from_prop,
					"to_prop" => $to_prop,
					"to_class" => $to_nm
				));
			}
		}
		$t->set_sortable(false);
	}

	function _init_inherit_from(&$t, $fn = "_get_inherit_fromto_use")
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "class_id",
			"caption" => t("Klass"),
			"sortable" => 1,
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
			"caption" => t("Muutmise aeg"),
			"sortable" => 1,
			"align" => "center",
			"numeric" => 1,
			"type" => "time",
			"format" => "d.m.Y H:i"
		));

		$t->define_field(array(
			"name" => "use",
			"caption" => t("Kasuta"),
			"align" => "center",
			"callback" => array(&$this, $fn),
			"callb_pass_row" => true
		));
	}

	function do_inherit_from($arr)
	{
		if (!is_oid($arr["obj_inst"]->id()))
		{
			return;
		}
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_inherit_from($t);

		$this->use_data = $arr["obj_inst"]->meta("inherit_from_objs");
		$t->data_from_ol(new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_INHERIT_FROM"))), array("change_col" => "name"));
	}

	function do_inherit_to($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_inherit_from($t);

		$this->use_data = $arr["obj_inst"]->meta("inherit_to_objs");
		$t->data_from_ol(new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_INHERIT_TO"))), array("change_col" => "name"));
	}

	function _get_inherit_fromto_use($arr)
	{
		return html::checkbox(array(
			"name" => "use_data[".$arr["oid"]."]",
			"value" => $arr["oid"],
			"checked" => checked($this->use_data[$arr["oid"]] == $arr["oid"])
		));
	}

	function get_inherit_from_objs($o)
	{
		$ret = array();
		$ol = new object_list($o->connections_from(array("type" => "RELTYPE_INHERIT_FROM")));
		$ud = $o->meta("inherit_from_objs");
		foreach($ol->arr() as $o)
		{
			if ($ud[$o->id()] == $o->id())
			{
				$ret[] = $o;
			}
		}

		return $ret;
	}

	function get_inherit_to_objs($o)
	{
		$ret = array();
		$ol = new object_list($o->connections_from(array("type" => "RELTYPE_INHERIT_TO")));
		$ud = $o->meta("inherit_to_objs");
		foreach($ol->arr() as $o)
		{
			if ($ud[$o->id()] == $o->id())
			{
				$ret[] = $o;
			}
		}

		return $ret;
	}

	function get_inherit_from_sel_props($o)
	{
		$ret = array("--" => t("--Vali--"));

		$wd = $o->meta("use_inherit_from_props");

		$inherit_from_objs = $this->get_inherit_from_objs($o);
		foreach($inherit_from_objs as $o)
		{
			$cu = get_instance("cfg/cfgutils");
			$props = $cu->load_properties(array(
				"clid" => $o->class_id()
			));
			foreach($props as $pn => $pd)
			{
				if ($pd["caption"] != "" && $pd["store"] != "no" && $wd[$o->id()][$pn] == $pn)
				{
					$ret[$o->id().".".$pn] = $o->name()."::".$pd["caption"];
				}
			}
		}

		return $ret;
	}
}
?>
