<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/class_designer/property_tree.aw,v 1.10 2007/12/06 14:33:04 kristo Exp $
// property_tree.aw - Puu komponent 
/*

@classinfo syslog_type=ST_PROPERTY_TREE relationmgr=yes no_status=1 no_comment=1 maintainer=kristo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property data_from type=select
@caption Andmed

@property ct_rels_levels type=textbox editonly=1 size=5
@caption Mitu taset puus

@property no_caption type=checkbox ch_value=1 
@caption Ilma tekstita

@default group=content_user 

	@property ct_toolbar type=toolbar no_caption=1 store=no 

	@layout cu_hbox type=hbox width="20%:80%"

	@property ct_tree type=treeview parent=cu_hbox no_caption=1
	@caption Puu

	@property ct_list type=table parent=cu_hbox no_caption=1
	@caption Tabel

@default group=content_objs

	@property ct_clids type=select multiple=1
	@caption Objektit&uuml;&uuml;bid, mida puus kuvada

@default group=content_rels

	@property ct_rel type=table no_caption=1
	@caption Sisuobjektide seose t&uuml;&uuml;p

@groupinfo content_user caption="Sisu" submit=no
@groupinfo content_objs caption="Sisu" 
@groupinfo content_rels caption="Sisu" 

*/

class property_tree extends class_base
{
	function property_tree()
	{
		$this->init(array(
			"tpldir" => "applications/class_designer/property_tree",
			"clid" => CL_PROPERTY_TREE
		));

		$this->data_from = array(
			"user" => t("Kasutaja poolt sisestatud"),
			"objs" => t("Objektid objektis&uuml;steemist"),
			"rels" => t("Seostatud")
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "ct_toolbar":
				$this->_ct_toolbar($arr);
				break;

			case "ct_tree":
				$this->_ct_tree($arr);
				break;

			case "ct_list":
				$this->_ct_list($arr);
				break;

			case "data_from":
				$prop["options"] = $this->data_from;
				break;

			case "ct_clids":
				$prop["options"] = get_class_picker();
				break;

			case "ct_rels_levels":
				if ($arr["obj_inst"]->prop("data_from") != "rels")
				{
					return PROP_IGNORE;
				}
				break;

			case "ct_rel":
				$this->_ct_rel($arr);
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
			case "ct_rel":
				$this->_save_ct_rel($arr);
				break;
		}
		return $retval;
	}	

	function _ct_tree($arr)
	{
		$ot = new object_tree(array(
			"parent" => $arr["obj_inst"]->id(),
			"class_id" => CL_PROPERTY_TREE_BRANCH
		));

		$arr["prop"]["vcl_inst"] = treeview::tree_from_objects(array(
			"ot" => $ot,
			"var" => "ts",
			"root_item" => $arr["obj_inst"],
			"tree_opts" => array("type" => TREE_DHTML,"persist_state" => true, "tree_id" => "_ct_tree"),
		));
	}

	function _ct_toolbar($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];

		$t->add_button(array(
			"name" => "new",
			"url" => html::get_new_url(CL_PROPERTY_TREE_BRANCH, ($arr["request"]["ts"] ? $arr["request"]["ts"] : $arr["obj_inst"]->id() ), array(
				"return_url" => get_ru()
			)),
			"tooltip" => t("Lisa uus oks"),
			"img" => "new.gif",
		));

		$t->add_button(array(
			"name" => "save",
			"tooltip" => t("Salvesta"),
			"img" => "save.gif",
		));

		$t->add_button(array(
			"name" => "delete",
			"tooltip" => t("Kustuta"),
			"confirm" => t("Oled kindel et soovid valitud objekte kustutada?"),
			"action" => "ct_del",
			"img" => "delete.gif",
		));
	}

	function _init_ct_list_t(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "change",
			"caption" => t("Muuda"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	function _ct_list($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_ct_list_t($t);

		$pt = is_oid($arr["request"]["ts"]) ? $arr["request"]["ts"] : $arr["obj_inst"]->id();
		$ol = new object_list(array(
			"parent" => $pt,
			"class_id" => CL_PROPERTY_TREE_BRANCH
		));
		$t->data_from_ol($ol);
	}

	function get_visualizer_prop($el, &$pd)
	{
		// do the damn tree magic 
		$tv = get_instance(CL_TREEVIEW);

		$tree_opts = array(
			"root_url" => aw_global_get("REQUEST_URI"),	
			"type" => TREE_DHTML,
			"tree_id" => "vist".$el->id(),
			"persist_state" => true,
		);

		$tv->start_tree($tree_opts);

		$ic = get_instance("core/icons");

		$var = "demot[".$el->id()."]";

		$ot = new object_tree(array(
			"parent" => $el->id(),
			"class_id" => CL_PROPERTY_TREE_BRANCH
		));
		$ol = $ot->to_list();
		$i = get_instance(CL_PROPERTY_TREE_BRANCH);
		foreach($ol->arr() as $o)
		{
			$i->get_vis_tree_item($tv, $o, $var, $el);
		}

		$pd["type"] = "tree";
		$pd["value"] = $tv->finalize_tree();
		
		if ($el->prop("no_caption") == 1)
		{
			$pd["no_caption"] = 1;
		}
	}

	function callback_mod_tab($arr)
	{
		switch($arr["obj_inst"]->prop("data_from"))
		{
			case "rels":
				if ($arr["id"] == "content_objs" || $arr["id"] == "content_user")
				{
					return false;
				}
				break;

			case "objs":
				if ($arr["id"] == "content_user" || $arr["id"] == "content_rels")
				{
					return false;
				}
				break;
		
			case "user":
			default:
				if ($arr["id"] == "content_objs" || $arr["id"] == "content_rels")
				{
					return false;
				}
				break;
		}

		return true;
	}

	function callback_mod_reforb($arr)
	{
		$arr["return_url"] = $_SERVER["REQUEST_METHOD"] == "GET" ? post_ru() : $arr["return_url"];
		$arr["ts"] = $_GET["ts"];
	}

	/**

		@attrib name=ct_del

	**/
	function ct_del($arr)
	{
		$sel = safe_array($arr["sel"]);
		if (count($sel))
		{
			$ol = new object_list(array("oid" => $sel));
			$ol->delete();
		}
		return $arr["return_url"];
	}

	function generate_get_property($arr)
	{
		$el = new object($arr["id"]);
		$sys_name = $arr["name"];
		$gpblock = "";
		$gpblock .= "\t\t\tcase \"${sys_name}\":\n";
		$gpblock .= "\t\t\t\t\$this->generate_${sys_name}(\$arr);\n";
		$gpblock .= "\t\t\t\tbreak;\n\n";
		return array(
			"get_property" => $gpblock,
			"generate_methods" => array("generate_${sys_name}"),
		);
	}
	
	function generate_method($arr)
	{
		$obj = new object($arr["id"]);
		switch($obj->prop("data_from"))
		{
			case "rels":
				$ret = $this->_generate_rels_meth($arr);
				break;

			case "objs":
				$ret = $this->_generate_objs_meth($arr);
				break;
		
			case "user":
			default:
				$ret = $this->_generate_user_meth($arr);
				break;
		}
		return $ret;
	}

	function _generate_user_meth($arr)
	{
		$obj = new object($arr["id"]);
		$name = $arr["name"];
		$els = new object_tree(array(
			"parent" => $arr["id"],
			"class_id" => CL_PROPERTY_TREE_BRANCH,
		));
		$els = $els->to_list();

		$var = $name."_tf";

		$rv = "\tfunction $name(\$arr)\n";
		$rv .= "\t{\n";

		$i = get_instance(CL_PROPERTY_TREE_BRANCH);
		foreach($els->arr() as $el)
		{
			$rv .= $i->do_generate_method($obj, $el, $var);
		};

		$rv .= "\t}\n\n";
		return $rv;
	}

	function _generate_objs_meth($arr)
	{
		$obj = new object($arr["id"]);
		$name = $arr["name"];

		$rv = "\tfunction $name(\$arr)\n";
		$rv .= "\t{\n";

		// TODO: objects from below

		$rv .= "\t}\n\n";
		return $rv;
	}

	function _generate_rels_meth($arr)
	{
		$obj = new object($arr["id"]);
		$name = $arr["name"];
		$var = $name."_tf";

		$rv = "\tfunction $name(\$arr)\n";
		$rv .= "\t{\n";

		// begin tree
		$rv .= "\t\t\$t =& \$arr[\"prop\"][\"vcl_inst\"];\n";
		$rv .= "\t\t\$cur_o = \$arr[\"obj_inst\"];\n";
		$rv .= "\t\t\$stack = array();\n";
		$reld = safe_array($obj->meta("ct_rels_dat"));

		// nest foreaches for levels
		for($level = 0; $level < $obj->prop("ct_rels_levels"); $level++)
		{
			$rv .= $this->_i($level)."//level $level \n";
			// start from the cur obj and find the correct related obj
			$pts = explode(".", $reld[$level]);
			$idx = 0;
			if (count($pts) > 1)
			{
				for($idx = 0; $idx < (count($pts)-1); $idx++)
				{
					$rv .= $this->_i($level)."\$cur_o = \$cur_o->get_first_obj_by_reltype(\"".$pts[$idx]."\");\n";
				}
			}

			// now, get connected objects from the last rel and ionsert into tree
			$rv .= $this->_i($level)."foreach(\$cur_o->connections_from(array(\"type\" => \"".$pts[$idx]."\")) as \$c)\n";
			$rv .= $this->_i($level)."{\n";
			$rv .= $this->_i($level+1)."\$item_$level = \$c->to();\n";

			if ($level == 0)
			{
				$rv .= $this->_i($level+1)."\$t->add_item(0, array(\n";
			}
			else
			{
				$rv .= $this->_i($level+1)."\$t->add_item(\$item_".($level-1)."->id(), array(\n";
			}
			$rv .= $this->_i($level+1)."\t\"name\" => \$arr[\"request\"][\"$var\"] == \$item_".$level."->id() ? \"<b>\".\$item_".$level."->name().\"</b>\" : \$item_".$level."->name(),\n";
			$rv .= $this->_i($level+1)."\t\"id\" => \$item_".$level."->id(),\n";
			$rv .= $this->_i($level+1)."\t\"url\" => aw_url_change_var(\"$var\", \$item_".$level."->id()),\n";
			$rv .= $this->_i($level+1)."\t\"iconurl\" => icons::get_icon_url(CL_MENU, \"\"),\n";
			$rv .= $this->_i($level+1)."));\n";
			$rv .= $this->_i($level+1)."\$cur_o = \$c->to();\n";

			//$rv .= $this->_i($level+1)."array_push(\$stack, \$cur_o);\n";

		}

		for($level = $obj->prop("ct_rels_levels")-1; $level > -1 ; $level--)
		{
			//$rv .= $this->_i($level+1)."\$cur_o = array_pop(\$stack);\n";
			$rv .= $this->_i($level)."}\n";
		}

		$rv .= "\t}\n\n";
		return $rv;
	}

	function _init_ct_rel(&$t)
	{
		$t->define_field(array(
			"name" => "level",
			"caption" => t("Tase"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "rel",
			"caption" => t("Seos(ed)"),
			"align" => "center"
		));
	}

	function _get_designer($o)
	{
		$pt = $o->path();
		foreach($pt as $p)
		{
			if ($p->class_id() == CL_CLASS_DESIGNER)
			{
				return $p;
			}
		}
		return NULL;
	}

	function _ct_rel($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_ct_rel($t);

		$reld = safe_array($arr["obj_inst"]->meta("ct_rels_dat"));
		$prop_tbl = get_instance(CL_PROPERTY_TABLE);

		$clss = aw_ini_get("classes");

		$from_clid = 0;
		for($level = 0; $level < $arr["obj_inst"]->prop("ct_rels_levels"); $level++)
		{
			$rels = array();

			$str = $reld[$level];
			if ($str != "")
			{
				$str .= ".";
			}
			foreach(explode(".", $str) as $l_rel)
			{
				if (!$from_clid)
				{
					// read all connections from designer class
					$d = $this->_get_designer($arr["obj_inst"]);
					$relp = array("" => "");
					foreach($d->connections_from(array("type" => "RELTYPE_RELATION")) as $c)
					{
						$rel_o = $c->to();
						$k = "RELTYPE_".strtoupper($rel_o->name());
						$relp[$k] = $rel_o->name();
						if ($l_rel == $k)
						{
							$from_clid = reset($rel_o->prop("r_class_id"));
						}
					}
					$rels[] = $d->name().": ".html::select(array(
						"name" => "rels[$level][]",
						"options" => $relp,
						"value" => $l_rel
					));
				}
				else
				{
					$pp = $prop_tbl->_get_property_picker_from_clid($from_clid);
					$rels[] = $clss[$from_clid]["name"].": ".html::select(array(
						"name" => "rels[$level][]",
						"options" => $pp,
						"value" => $l_rel
					));

					if ($l_rel)
					{
						$cfgu = get_instance("cfg/cfgutils");
						$ps = $cfgu->load_properties(array(
							"clid" => $from_clid
						));
						$r_rels = $cfgu->get_relinfo();
	
						$from_clid = $prop_tbl->_get_rel_class_id_for_prop_or_rel($from_clid, $ps, $r_rels, $l_rel);
					}
				}

			}


			$t->define_data(array(
				"level" => sprintf(t("Tase %s"), $level+1),
				"rel" => join("<br>", $rels)
			));
		}

		$t->set_sortable(false);
	}

	function _save_ct_rel($arr)
	{
		$save = array();

		$rels = safe_array($arr["request"]["rels"]);
		foreach($rels as $level => $parts)
		{
			$rp = array();
			foreach($parts as $part)
			{
				if ($part != "")
				{
					$rp[] = $part;
				}
			}

			if (count($rp))
			{
				$save[$level] = join(".", $rp);
			}
		}

		$arr["obj_inst"]->set_meta("ct_rels_dat", $save);
	}

	function _i($level)
	{
		return "\t\t".str_repeat("\t", $level);
	}
}
?>
