<?php
// $Header: /home/cvs/automatweb_dev/classes/translate/translation_workplace.aw,v 1.9 2008/01/31 13:55:34 kristo Exp $
// translation_workplace.aw - T&otilde;lkimise t&ouml;&ouml;laud 
/*

@classinfo syslog_type=ST_TRANSLATION_WORKPLACE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@default table=objects
@default group=general

@property objtypes type=relpicker multiple=1 field=meta method=serialize reltype=RELTYPE_OBJT
@caption Objektit&uuml;&uuml;bid, mida t&otilde;lkida

@default group=translatable

	@property tr_toolbar type=toolbar no_caption=1 store=no

	@layout tr_ver_split type=hbox width=20%:80%

		@layout tr_left type=vbox parent=tr_ver_split

			@layout tr_left_top type=vbox closeable=1 area_caption=Filtreeri parent=tr_left
	
				@property tr_tree type=treeview store=no no_caption=1 parent=tr_left_top
	
			@layout tr_left_bottom type=vbox parent=tr_left closeable=1 area_caption=Otsing

				@property s_oid type=textbox size=5 store=no parent=tr_left_bottom captionside=top
				@caption Objekti id

				@property s_name type=textbox size=20 store=no parent=tr_left_bottom captionside=top
				@caption Nimi

				@property s_clid type=select store=no parent=tr_left_bottom captionside=top
				@caption T&uuml;&uuml;p

				@property s_sbt type=submit store=no parent=tr_left_bottom no_caption=1
				@caption Otsi

		@property tr_table type=table store=no no_caption=1 parent=tr_ver_split
		

@groupinfo translatable caption="T&otilde;lgitavad objektid"
groupinfo translated caption="T&otilde;lgitud objektid"
groupinfo untrans caption="T&otilde;lkimata objektid"

@reltype OBJT value=1 clid=CL_OBJECT_TYPE
@caption Objektit&uuml;&uuml;p
*/

class translation_workplace extends class_base
{
	function translation_workplace()
	{
		$this->init(array(
			"tpldir" => "translate/translation_workplace",
			"clid" => CL_TRANSLATION_WORKPLACE
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "tr_table":
				$this->_tr_table($arr);
				break;

			case "tr_tree":
				$this->_tr_tree($arr);
				break;

			case "s_clid":
				$this->_s_clid($arr);
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
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function _init_tr_table(&$t)
	{
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
			"type" => "time",
			"format" => "d.m.Y H:i",
			"numeric" => 1,
			"align" => "center",
			"sortable" => 1
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
			"type" => "time",
			"format" => "d.m.Y H:i",
			"numeric" => 1,
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "class_id",
			"caption" => t("T&uuml;&uuml;p"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "trans_status",
			"caption" => t("T&otilde;lke staatus"),
			"numeric" => 1,
			"align" => "center",
			"sortable" => 1
		));

		$l = get_instance("languages");
		$ll = $l->get_list(array("set_for_user" => true));
		// meddle with things until the default language is the first in the table
		uksort($ll, create_function('$a, $b','$ld=aw_ini_get("languages.default");if ($a == $ld) { return -1; }if ($b == $ld) { return 1;} return 0;'));
		foreach($ll as $lid => $lang)
		{
			$t->define_field(array(
				"name" => $lid,
				"caption" => $lang,
				"align" => "center",
				"sortable" => 1,
				"parent" => "trans_status"
			));
			$t->define_field(array(
				"name" => $lang."_state",
				"caption" => t("Aktiivne"),
				"align" => "center",
				"sortable" => 1,
				"parent" => $lid
			));
			$t->define_field(array(
				"name" => $lang."_mod",
				"caption" => t("Muudetud"),
				"align" => "center",
				"sortable" => 1,
				"parent" => $lid,
				"type" => "time",
				"format" => "d.m.Y H:i",
				"numeric" => 1
			));
		}
	}

	function _tr_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_tr_table($t);

		classload("core/date/date_calc");

		$tm = $arr["request"]["tm"];
		$class_id = $arr["request"]["class_id"];
		$parent = $arr["request"]["parent"];
		$clss = aw_ini_get("classes");

		$filt = array(
			"class_id" => array_keys($this->_get_clids($arr["obj_inst"])),
			//"flags" => array("mask" => 2, "flags" => 2),
		);
		$has = false;
		if ($tm)
		{
			$has = true;
			switch($tm)
			{
				case "today":
					$filt["modified"] = new obj_predicate_compare(OBJ_COMP_GREATER, get_day_start());
					$t->set_caption(t("T&auml;na muudetud"));
					break;

				case "yesterday":
					$filt["modified"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, get_day_start()-24*3600, get_day_start());
					$t->set_caption(t("Eile muudetud"));
					break;

				case "week":
					$filt["modified"] = new obj_predicate_compare(OBJ_COMP_GREATER, get_week_start());
					$t->set_caption(t("Sel n&auml;dalal muudetud"));
					break;

				case "untr":
					$t->set_caption(t("T&otilde;lkimata"));
					break;

				case "old":
					$t->set_caption(t("Vanemad"));
					$filt["modified"] = new obj_predicate_compare(OBJ_COMP_LESS, get_week_start());
					break;
			}
		}
		else
		if ($class_id)
		{
			$has = true;
			$filt["class_id"] = $class_id;
			$t->set_caption(sprintf(t("Objektit&uuml;&uuml;p: %s"), $clss[$class_id]["name"]));
		}
		else
		if ($parent)
		{
			$has = true;
			$filt["parent"] = $parent;
			$po = obj($parent);
			$t->set_caption($po->path_str());
		}
		if (!$has)
		{
			return;
		}
		$ol = new object_list($filt);

		$l = get_instance("languages");
		$ll = $l->get_list(array("set_for_user" => true));
		$data = array();
		foreach($ol->arr() as $o)
		{
			$d = array(
				"name" => html::obj_change_url($o),
				"created" => $o->created(),
				"createdby" => $o->createdby(),
				"modified" => $o->modified(),
				"modifiedby" => $o->modifiedby(),
				"class_id" => $clss[$o->class_id()]["name"],
				"trans_status" => $trs
			);
			$has_trans = false;
			foreach($ll as $lid => $lang)
			{
				if ($lid == $o->lang_id())
				{
					$d[$lang."_state"] = $o->status() == STAT_ACTIVE ? t("Jah") : t("Ei");
					$d[$lang."_mod"] = $o->modified();
					$this->_sortby_lang = $lang."_mod";
				}
				else
				{
					$t_state = $o->meta("trans_".$lid."_status") ? t("Jah") : t("Ei");
					$t_mod = $o->meta("trans_".$lid."_modified");
					if ($t_mod < $o->modified() && $t_mod > 100)
					{
						$t_state = html::href(array(
							"url" => $this->mk_my_orb("change", array("id" => $o->id(), "group" => "tlgi"), "doc")."#".$lid,
							"caption" => "<font color='red'>".$t_state."</font>"
						));
					}
					else
					{
						$t_state = html::href(array(
							"url" => $this->mk_my_orb("change", array("id" => $o->id(), "group" => "tlgi"), "doc")."#".$lid,
							"caption" => $t_state
						));
					}
					$d[$lang."_state"] = $t_state;
					$d[$lang."_mod"] = $t_mod;
					if ($t_mod > 100)
					{
						$has_trans = true;
					}
				}
			}

			if (($tm == "untr" || $tm == "old") && $has_trans)
			{
				continue;
			}
			$data[] = $d;
		}

		usort($data, array(&$this, "__t_sort"));
		foreach($data as $d)
		{
			$t->define_data($d);
		}
		$t->set_sortable(false);
	}

	function __t_sort($a, $b)
	{
		// sort any red rows first
		$a_has_red = $this->_has_reds($a);
		$b_has_red = $this->_has_reds($b);

		if ($a_has_red && $b_has_red)
		{
			// sort by date
			return $b[$this->_sortby_lang] - $a[$this->_sortby_lang];
		}
		else
		if ($a_has_red)
		{
			return -1;
		}
		else
		if ($b_has_red)
		{
			return 1;
		}
	}

	function _has_reds($a)
	{
		foreach($a as $v)
		{
			if (strpos($v, "color='red'") !== false)
			{
				return true;
			}
		}
		return false;
	}

	function _tr_tree($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];

		$t->start_tree(array(
			"type" => TREE_DHTML,
			"tree_id" => "tr_tree",
			"persist_state" => 1,
			"get_branch_func" => $this->mk_my_orb("get_node", array("id" => $arr["obj_inst"]->id(), "url" => get_ru(), "parent" => "0")),
			"has_root" => 1,
			"root_name" => $arr["obj_inst"]->name(),
			"root_url" => aw_url_change_var("b_id", null),
		));

		$items = array(
			"parent" => t("Kataloogid"),
			"class_id" => t("Objektit&uuml;&uuml;bid"),
			"modified" => t("Muudetud"),
		);
		$pt = is_array(aw_ini_get("admin_rootmenu2")) ? reset(aw_ini_get("admin_rootmenu2")) : aw_ini_get("admin_rootmenu2");
		$url = aw_url_change_var(array(
			"parent" => $pt,
			"class_id" => null,
			"tm" => null
		));
		foreach($items as $nm => $str)
		{
			$t->add_item(0, array(
				"id" => $nm,
				"name" => $str,
				"url" => $url
			));
			$t->add_item($nm, array(
				"id" => $nm."_opener",
				"name" => $str,
				"url" => $url
			));
		}
	}

	/**
		@attrib name=get_node
		@param id required type=int acl=view
		@param url required
		@param parent required 
	**/
	function get_node($arr)
	{
		$o = obj($arr["id"]);
		$t = get_instance("vcl/treeview");
		$t->start_tree(array(
			"type" => TREE_DHTML,
			"tree_id" => "tr_tree",
			"branch" => 1,
		));
		if ($arr["parent"][1] == "o")
		{
			list(,$arr["parent"]) = explode("_", $arr["parent"]);
		}
		if ($arr["parent"] == "0modified")
		{
			$t->add_item(0, array(
				"id" => "modified_today",
				"name" => t("T&auml;na"),
				"url" => aw_url_change_var(array(
					"parent" => null,
					"class_id" => null,
					"tm" => "today"
				), false, $arr["url"])
			));
			$t->add_item(0, array(
				"id" => "modified_yesterday",
				"name" => t("Eile"),
				"url" => aw_url_change_var(array(
					"parent" => null,
					"class_id" => null,
					"tm" => "yesterday"
				), false, $arr["url"])
			));
			$t->add_item(0, array(
				"id" => "modified_week",
				"name" => t("See n&auml;dal"),
				"url" => aw_url_change_var(array(
					"parent" => null,
					"class_id" => null,
					"tm" => "week"
				), false, $arr["url"])
			));
			$t->add_item(0, array(
				"id" => "modified_untr",
				"name" => t("T&otilde;lkimata"),
				"url" => aw_url_change_var(array(
					"parent" => null,
					"class_id" => null,
					"tm" => "untr"
				), false, $arr["url"])
			));
			$t->add_item(0, array(
				"id" => "modified_old",
				"name" => t("Vanemad"),
				"url" => aw_url_change_var(array(
					"parent" => null,
					"class_id" => null,
					"tm" => "old"
				), false, $arr["url"])
			));
		}
		else
		if ($arr["parent"] == "0class_id")
		{
			$clss = aw_ini_get("classes");
			$clp = array();
			foreach(safe_array($o->prop("objtypes")) as $ot_id)
			{
				$ot = obj($ot_id);
				$clp[$ot->subclass()] = $clss[$ot->subclass()]["name"];
			}

			foreach($clp as $clid => $cln)
			{
				$t->add_item(0, array(
					"id" => "clid_".$clid,
					"name" => $cln,
					"url" => aw_url_change_var(array(
						"parent" => null,
						"class_id" => $clid,
						"tm" => null
					), false, $arr["url"])
				));
			}
		}
		else
		if ($arr["parent"] == "0parent")
		{
			$ol = new object_list(array(
				"parent" => aw_ini_get("admin_rootmenu2"),
				"class_id" => CL_MENU
			));
			$ol2 = new object_list(array(
				"parent" => $ol->ids(),
				"class_id" => CL_MENU
			));
			$has_l2 = array();
			foreach($ol2->arr() as $item)
			{
				$has_l2[$item->parent()] = $item->id();
			}

			foreach($ol->arr() as $item)
			{
				$t->add_item(0, array(
					"id" => "o_".$item->id(),
					"name" => parse_obj_name($item->name()),
					"url" => aw_url_change_var(array(
						"parent" => $item->id(),
						"class_id" => null,
						"tm" => null
					), false, $arr["url"])
				));
				if ($nid = $has_l2[$item->id()])
				{
					$t->add_item("o_".$item->id(), array(
						"id" => "o_".$nid,
						"name" => parse_obj_name($item->name()),
						"url" => aw_url_change_var(array(
							"parent" => $item->id(),
							"class_id" => null,
							"tm" => null
						))
					), false, $arr["url"]);
				}
			}
		}
		else
		if ($this->can("view", (int)$arr["parent"]))
		{
			$ol = new object_list(array(
				"parent" => (int)$arr["parent"],
				"class_id" => CL_MENU
			));
			$ol2 = new object_list(array(
				"parent" => $ol->ids(),
				"class_id" => CL_MENU
			));
			$has_l2 = array();
			foreach($ol2->arr() as $item)
			{
				$has_l2[$item->parent()] = $item->id();
			}

			foreach($ol->arr() as $item)
			{
				$t->add_item(0, array(
					"id" => "o_".$item->id(),
					"name" => parse_obj_name($item->name()),
					"url" => aw_url_change_var(array(
						"parent" => $item->id(),
						"class_id" => null,
						"tm" => null
					), false, $arr["url"])
				));
				if ($nid = $has_l2[$item->id()])
				{
					$t->add_item("o_".$item->id(), array(
						"id" => "o_".$nid,
						"name" => parse_obj_name($item->name()),
						"url" => aw_url_change_var(array(
							"parent" => $item->id(),
							"class_id" => null,
							"tm" => null
						))
					), false, $arr["url"]);
				}
			}
		}

		die($t->finalize_tree());
	}

	function _get_clids($o)
	{
		$clss = aw_ini_get("classes");
		$clp = array();
		foreach(safe_array($o->prop("objtypes")) as $ot_id)
		{
			$ot = obj($ot_id);
			$clp[$ot->subclass()] = $clss[$ot->subclass()]["name"];
		}
		return $clp;
	}

	function _s_clid($arr)
	{
		$clp = $this->_get_clids($arr["obj_inst"]);
		$t = array("" => t("--vali--")) + $clp;
		$arr["prop"]["options"] = $t;
	}
}
?>
