<?php
// menu_tree.aw - men&uuml;&uuml;puu

/*
	@default table=objects
	@default field=meta
	@default method=serialize
	@default group=general

	@classinfo trans=1
	@property children_only type=checkbox ch_value=1 trans=1 prop_cb=1
	@caption Ainult alammen&uuml;&uuml;d

	@property template type=select trans=1
	@caption Template

	@property num_levels type=select
	@caption Tasemeid

	@property no_unclickable type=checkbox ch_value=1 default=1
	@caption &Auml;ra n&auml;ita mitteklikitavaid men&uuml;&uuml;sid

	@property menus type=select multiple=1 size=15 trans=1
	@caption Men&uuml;&uuml;d

	@property root_menu multiple=1 type=relpicker no_caption=1 reltype=RELTYPE_ROOT_MENU

	@property menu_tb type=toolbar no_caption=1
	@caption Men&uuml;&uuml; toolbar

	@property menu_table type=table no_caption=1
	@caption Men&uuml;&uuml; seoste tabel

	@groupinfo activity caption="Aktiivsus"
		@property default_table type=table no_caption=1 store=no group=activity

	@reltype ROOT_MENU value=1 clid=CL_MENU
	@caption Men&uuml;&uuml;

*/
class menu_tree extends class_base
{
	private $rec_level = 0;
	private $_sfo_level = 0;
	private $sfo_ids = array();
	private $object_list;
	private $strip_tags;

	function menu_tree()
	{
		$this->init(array(
			"clid" => CL_MENU_TREE,
		));
		$this->strip_tags = aw_ini_get("menuedit.strip_tags");
	}

	function get_property($args)
	{
		$data = &$args["prop"];
		$retval = true;
		switch($data["name"])
		{
			case "default_table":
				$this->_get_default_table($args);
			break;
			case "num_levels":
				$data["options"] = array(
					0 => t("K&otilde;ik"),
					1 => 1,
					2 => 2,
					3 => 3,
					4 => 4,
					5 => 5
				);
				break;
			case "root_menu":
				return PROP_IGNORE;
			case "menus":
				//nyydsest peaks seostega seda asja tegema
				//et yleminek oleks valutu :
				if(!is_oid($args["obj_inst"]->id()) || !$data["value"]) return PROP_IGNORE;
				$cs = $args["obj_inst"]->connections_from(array(
					"type" => "RELTYPE_ROOT_MENU",
				));
				if(sizeof($cs))
				{
					return PROP_IGNORE;
				}

				foreach($data["value"] as $m)
				{
					$args["obj_inst"]->connect(array(
						"to" => $m,
						"reltype" => "RELTYPE_ROOT_MENU",
					));
				}

				$ol = new object_list(array(
					"class_id" => menu_obj::CLID,
					"status" => array(STAT_ACTIVE,STAT_NOTACTIVE),
					new object_list_filter(array(
						"logic" => "OR",
						"conditions" => array(
							"lang_id" => aw_global_get("lang_id"),
							"type" => MN_CLIENT
						)
					)),
				));
				$menus = array();
				for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
				{
					$menus[$o->id()] = $o->path_str();
				}
				asort($menus);
				$data["options"] = $menus;
				break;

			case "menu_tb":
				if(!is_oid($args["obj_inst"]->id())) return PROP_IGNORE;
				$this->_get_menu_tb($args);
				break;
			case "menu_table":
				if(!is_oid($args["obj_inst"]->id())) return PROP_IGNORE;
				$this->_get_menu_table($args);
				break;

			case "template":
				$tpldir = aw_ini_get("site_basedir"). "templates/menu_tree/";
				$tpls = $this->get_directory(array(
					"dir" => $tpldir,
				));
				$data["options"] = $tpls;
				break;

		}
		return PROP_OK;
	}

	function set_property($arr)
	{
		$prop =& $arr["prop"];
		switch($prop["name"])
		{
			case "default_table":
				$this->{"_set_".$prop["name"]}($arr);
				break;
		}
		return PROP_OK;
	}

	function _get_default_table(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_header(t("S&uuml;steemi vaikimisi objekt"));
		$t->define_field(array(
			"name" => "select",
			"caption" => t("Vali"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Objekt"),
		));
		$ol = new object_list(array(
			"class_id" => $this->clid
		));
		$active = $this->get_sysdefault();
		foreach($ol->arr() as $oid=>$o)
		{
			$t->define_data(array(
				"select" => html::radiobutton(array(
					"name" => "default",
					"value" => $oid,
					"checked" => ($oid == $active)?1:0,
				)),
				"name" => html::get_change_url($oid, array(), $o->name()),
			));
		}
	}


	function _set_default_table($arr)
	{
		$ol = new object_list(array(
			"class_id" => $this->clid,
			"site_id" => aw_ini_get("site_id")
		));
		foreach ($ol->arr() as $item)
		{
			if ($item->id() != $arr["request"]["default"])
			{
				$item->set_status(STAT_NOTACTIVE);
			}
			else
			{
				$item->set_status(STAT_ACTIVE);
			}
			$item->save();
		}
	}

	function get_sysdefault()
	{
		$active = false;
		$ol = new object_list(array(
			"class_id" => $this->clid,
			"status" => STAT_ACTIVE,
			"site_id" => aw_ini_get("site_id")
		));
		if (sizeof($ol->ids()) > 0)
		{
			$first = $ol->begin();
			$active = $first->id();
		}
		else
		{
			$mt_obj = obj();
			$mt_obj->set_class_id(CL_MENU_TREE);
			$mt_obj->set_parent(aw_ini_get("users.root_folder"));
			$mt_obj->set_status(STAT_ACTIVE);
			$mt_obj->set_name("Menu tree");
			$mt_obj->save();
			$mt_obj->connect(array(
				"to" => aw_ini_get("site_rootmenu"),
				"type" => "RELTYPE_ROOT_MENU",
			));
			$active = $mt_obj->id();
		}
		return $active;
	}


	function parse_alias($args = array())
	{
		extract($args);
		$this->shown = array();

		$obj = obj($alias["target"]);
		$this->mt_obj = $obj;

		$menus = safe_array($obj->meta("menus"));
		$cs = $obj->connections_from(array(
			"type" => "RELTYPE_ROOT_MENU",
		));
		foreach($cs as $c)
		{
			$to = $c->to();
			$menus[] = $to->id();
		}

		if (count($menus) == 0)
		{
			$ol = new object_list();
		}
		else
		{
			$ol = new object_list(array(
				"oid" => $menus,
				"sort_by" => "objects.jrk",
				"class_id" => menu_obj::CLID,
				new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						"site_id" => aw_ini_get("site_id"),
						"lang_id" => aw_global_get("lang_id"),
						"type" => MN_CLIENT
					)
				)),
/*				new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						"site_id" => aw_ini_get("site_id"),
						"brother_of" => new obj_predicate_prop("id")
					)
				))*/
			));
		}
		$menus = $ol->ids();
		$cho = $obj->meta("children_only");
		$this->children_only = !empty($cho) ? true : false;
		$tpl = ($obj->meta("template") ? $obj->meta("template") : "menu_tree.tpl");
		$tpl = str_replace("/","",$tpl);

		$folder_list = array();
		// FIXME: this should use menu cache
		if (is_array($menus))
		{
			$this->tpl_name = "content";
			$this->spacer = "&nbsp";
			$this->sq = 3;
			$this->add_start_from = true;
			$this->read_template("menu_tree/" . $tpl);

			if ($this->is_template("item_L1"))
			{
				// this type of template can have different subtemplates for different levels..
				// good if one needs to use more complex designs..
				// you can also use optional item_Ln_START and item_ln_END subtemplates,
				// if they exist, then first and last item for a level is drawn using
				// those templates - if they exist, then they _need_ to contain variables
				// for items
				$this->layout_mode = 2;
				$this->single_tpl = 0;
			}
			elseif ($this->is_template("START"))
			{
				// this type of template has 3 subs, START, ITEM and END,
				// START and END are simply used to start and finish a level, they
				// cannot contain variables for items
				// typical usage: nested <ul> list
				$this->layout_mode = 3;
				$this->single_tpl = 0;
			}
			else
			{
				// this type of template has a single subtemplate which is used for
				// all levels, items are aligned with spacers
				// name of the subtemplate - "content"
				$this->layout_mode = 1;
				$this->single_tpl = 1;
			}

			foreach($menus as $val)
			{
				$folder_list = array_merge($folder_list,(array)$this->gen_rec_list(array(
						"start_from" => $val,
				)));
				$this->level = 0;
			}
		}
		//$fl = str_replace("&", "&amp;", join("",$folder_list));
		$fl = str_replace(chr(150), "-", join("",$folder_list));
		return $fl;

	}

	function gen_rec_list($args = array())
	{
		extract($args);
		$this->alias_stack = array();
		$this->object_list = array(); // siia satuvad koik need objektid

		$this->start_from = $args["start_from"];

		$this->_gen_rec_list(array($args["start_from"]));
		if ( (sizeof($this->object_list) == 0) && not($this->add_start_from) )
		{
			$retval = false;
		}
		else
		{
			$this->res = "";

			if ($this->add_start_from && $this->can("view", $start_from))
			{
				$_root = obj($start_from);
				if ($_root->status() == STAT_ACTIVE)
				{
					$this->object_list[$_root->parent()][$start_from] = $_root;
				}
			}

			reset($this->object_list);
			$this->level = 0;
			$this->_recurse_object_list(array(
				"parent" => (is_object($_root) ? $_root->parent() : ""),
			));
			if ($this->layout_mode == 1)
			{
				// return the stuff outside the "content" sub as well
				$this->vars(array(
					"content" => $this->res,
				));
				$retval = $this->parse();
			}
			else
			{
				$retval = $this->res;
			}
		}
		return $retval;
	}

	////
	// !Rekursiivne funktsioon, kutsutakse v&auml;lja gen_rec_list seest
	function _gen_rec_list($parents = array())
	{
		$this->save_handle();

		$nsuo = (aw_global_get("uid") == "" && aw_ini_get("menuedit.no_show_users_only"));

		// go over parents and see if they have sfo settings
		foreach($parents as $parent)
		{
			$po = obj($parent);
			if ($this->can("view", $po->prop("submenus_from_obj")))
			{
				$sfo = obj($po->prop("submenus_from_obj"));
				$sfo_i = $sfo->instance();
				$this->_req_sfo($po, $sfo, $sfo_i);
			}
		}

		$hu = $hide_untranslated = aw_ini_get("user_interface.hide_untranslated");
		$filt = array(
			"class_id" => menu_obj::CLID,
			"parent" => $parents,
			"status" => $hide_untranslated && aw_ini_get("languages.default") != aw_global_get("ct_lang_id") ? array(STAT_ACTIVE, STAT_NOTACTIVE) : STAT_ACTIVE,
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"lang_id" => aw_global_get("lang_id"),
					"type" => MN_CLIENT
				)
			)),
			"sort_by" => "objects.jrk"
		);

		if ($this->mt_obj->prop("no_unclickable") == 1)
		{
			$filt["clickable"] = 1;
		}

		if (aw_global_get("uid") == "")
		{
			$filt["users_only"] = 0;
		}
		$ol = new object_list($filt);
		$_parents = array();
		$lid = aw_global_get("ct_lang_id");
		for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
		{
			if ($hide_untranslated && (!$o->prop_is_translated("name") || ($lid == $o->lang_id() && $o->status() == STAT_NOTACTIVE)))
			{
				continue;
			}
			$name = $o->trans_get_val("name");
			if ($this->strip_tags)
			{
				$name = strip_tags($name);
			}
			$can = true;
			if ($nsuo)
			{
				if ($o->meta("users_only") == 1)
				{
					$can = false;
				}
			}

			if ($can)
			{
				$_parents[] = $o->id();
				$this->object_list[$o->parent()][$o->id()] = $o;
				// if this menu has get subs, then incluse those
				if ($this->can("view", $o->prop("submenus_from_obj")))
				{
					$sfo = obj($o->prop("submenus_from_obj"));
					$sfo_i = $sfo->instance();
					$this->_req_sfo($o, $sfo, $sfo_i);
				}
			}
		}

		if (sizeof($_parents) > 0)
		{
			$this->_gen_rec_list($_parents);
		}

		$this->restore_handle();
	}

	function _get_menu_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Men&uuml;&uuml;"),
			"align" => "center",
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
			"width" => "30px",
		));

		$cs = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_ROOT_MENU",
		));
		foreach($cs as $c)
		{
			$to = $c->to();
			$t->define_data(array(
				"name" => $to->path_str(),
				"oid" => $to->id(),
			));
		}
	}

	function _get_menu_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$url = $this->mk_my_orb("do_search", array(
			"pn" => "root_menu",
			"id" => $arr["obj_inst"]->id(),
			"clid" => menu_obj::CLID,
//			"multiple" => 1,
		), "popup_search");

		$tb->add_button(array(
			"name" => "search_menu",
			"tooltip" => t("Lisa men&uuml;&uuml;"),
			"img" => "search.gif",
			//"action" => "search_menus",
			"url" => "javascript:aw_popup_scroll('$url','".t("Otsi")."',550,500)"
		));
		$tb->add_button(array(
			"name" => "remove_menu",
			"tooltip" => t("Eemalda men&uuml;&uuml;"),
			"img" => "delete.gif",
			"action" => "delete_menus",
		));
		return PROP_OK;
	}

        /**
                @attrib name=delete_menus
        **/
        function delete_menus($arr)
        {
		$o = obj($arr["id"]);
		foreach($arr["sel"] as $menu)
		{
			$o->disconnect(array(
				"from" => $menu,
			));
		}
		return html::get_change_url($arr["id"], array(
			"return_url" => $arr["return_url"],
		));
        }

        /**
                @attrib name=search_menus
        **/
        function search_menus($arr)
        {

		$t->add_menu_item(array(
			"parent" => "search",
			"text" => t("Avamisaeg"),

		));
        	$o = obj($arr["id"]);
                return $arr[""];
        }

	function _req_sfo($o, $sfo, $sfo_i)
	{
		$this->_sfo_level++;
		$folders = $sfo_i->get_folders_as_object_list($sfo, $this->_sfo_level, $o);
		foreach($folders->arr() as $fld)
		{
			$this->object_list[$o->id()][$fld->id()] = $fld;
			$this->_req_sfo($fld, $sfo, $sfo_i);
			$this->sfo_ids[$fld->id()] = $sfo;
		}
		$this->_sfo_level--;
	}

	/////
	// !Recurse and print object array
	function _recurse_object_list($args = array())
	{
		if ($args["parent"])
		{
			$parent = $args["parent"];
		}
		else
		{
			$parent = 0;
		}

		$slice = $this->object_list[$parent];
		if (!is_array($slice))
		{
			return false;
		}

		if ($this->mt_obj->prop("num_levels") > 0 && $this->mt_obj->prop("num_levels") < $this->rec_level)
		{
			return;
		}

		$this->rec_level++;
		$ss = new site_show();
		$slicesize = sizeof($slice);
		$slicecounter = 0;
		while(list($k,$v) = each($slice))
		{
			$url = "";
			$this->vars(array(
				"level" => $this->rec_level
			));

			$slicecounter++;
			$id = $v->id();
			$spacer = str_repeat($this->spacer,$this->level * $this->sq);
			$name = $spacer . $v->trans_get_val("name");

			if ($this->single_tpl)
			{
				$tpl = ($this->tpl_name) ? $this->tpl_name : $this->tlist[1][0];
			}
			elseif ($this->layout_mode == 2)
			{
				$tpl = "item_L" . $this->level;
				if ( ($slicecounter == 1) && ($this->is_template($tpl . "_START")) )
				{
					$tpl .= "_START";
				}
				else
				if ( ($slicecounter == $slicesize) && ($this->is_template($tpl . "_END")) )
				{
					$tpl .= "_END";
				}
			}
			elseif ($this->layout_mode == 3)
			{
				if ($v->prop("clickable") != 1 && $this->is_template("ITEM_NOCLICK"))
				{
					$tpl = "ITEM_NOCLICK";
				}
				else
				{
					$tpl = "ITEM";
				}
			}
			else
			{
				$tpl = $this->tlist[$this->level + 1][0];
			};

			$tmpp = $v->path();

			if ($v->alias())
			{
				if (aw_ini_get("menuedit.recursive_aliases") == 0)
				{
					$id = $v->alias();
				}
				else
				{
					$id = join("/",$this->alias_stack);
					$id .= ($id == "" ? "" : "/") . $v->alias();
				}
				$id = aw_ini_get("baseurl").$id;
			}
			else
			{
				$id = aw_ini_get("baseurl").$id;
			}

			if ($v->prop("link"))
			{
				$url = $v->prop("link");
				$id = $url;
			}

			if (!empty($this->sfo_ids[$v->id()]))
			{
				$sfo_i = $this->sfo_ids[$v->id()]->instance();
				$url = $sfo_i->make_menu_link($v, $this->sfo_ids[$v->id()]);
			}
			else
			if (!is_oid($v->prop("submenus_from_obj")))
			{
				$pt = array_reverse($v->path());
				foreach($pt as $p_o)
				{
					if (is_oid($p_o->prop("submenus_from_obj")) && $this->can("view", $p_o->prop("submenus_from_obj")))
					{
						$sfo = $p_o->prop("submenus_from_obj");
						$sfo_o = obj($sfo);
						$sfo_i = $sfo_o->instance();
						$url = $sfo_i->make_menu_link($v, $sfo_o);
						break;
					}
				}
			}

			if ($url == "")
			{
				$url = $ss->make_menu_link($v);
			}

			$item = false;
			$url = str_replace("&", "&amp;", $url);

			if ($this->children_only && $v->id() == $this->start_from)
			{
				// do nothing
			}
			else
			{
				// check if we have already shown this one, so let's not do it again!
				if (!isset($this->shown[$v->id()]))
				{
					$this->vars(array(
						"url" => $url,
						"oid" => $id,
						"name" => parse_obj_name($v->trans_get_val("name")),
						"comment" => $v->trans_get_val("comment"),
						"spacer" => $spacer,
					));
					if($this->is_template($tpl."_SEL") && aw_global_get("section") == $v->id())
					{
						$this->res .= $this->parse($tpl."_SEL");
					}
					else
					{
						$this->res .= $this->parse($tpl);
					}
					$this->shown[$v->id()] = $id;
					$item = true;
				}
			}

			$next_slice = isset($this->object_list[$v->id()]) ? $this->object_list[$v->id()] : null;
			if (is_array($next_slice) && (sizeof($next_slice) > 0))
			{
				if ($v->alias())
				{
					array_push($this->alias_stack,$v->alias());
				}

				if ($this->layout_mode == 3)
				{
					$this->res .= $this->parse("START");
				}

				$this->level++;

				$this->_recurse_object_list(array(
					"parent" => $v->id(),
				));

				$this->level--;

				if ($this->layout_mode == 3)
				{
					$this->res .= $this->parse("END");
				}

				if ($v->alias())
				{
						array_pop($this->alias_stack);
				}
			}

			if ($item && $this->is_template("ITEM_END"))
			{
				$this->res .= $this->parse("ITEM_END");
			}
		}
		$this->rec_level--;
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["root_menu"] = "0";
	}
}
