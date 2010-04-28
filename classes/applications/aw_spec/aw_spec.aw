<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_AW_SPEC relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo allow_rte=2

@default table=objects
@default group=general

@default group=spec

	@property spec_tb type=toolbar no_caption=1 store=no
	
	@layout v_split type=hbox width=20%:80%
		
		@layout tree_border type=vbox parent=v_split area_caption=Sisukord closeable=1

			@property spec_tree type=treeview store=no no_caption=1 parent=tree_border

		@layout table_border type=vbox parent=v_split 

			@layout toolbar_border type=vbox parent=table_border area_caption=Toolbari&nbsp;nupud closeable=1

				@property enter_toolbar type=textbox store=no no_caption=1 parent=toolbar_border size=80

			@property class_list type=table store=no no_caption=1 parent=v_split parent=table_border
			@property relation_list type=table store=no no_caption=1 parent=v_split parent=table_border
		
		@layout spec_border type=vbox parent=v_split area_caption=Sisu closeable=1

			@property spec_editor type=textarea rows=30 cols=80 richtext=1 store=no no_caption=1 parent=spec_border richtext=1

		
@default group=spec_view

	@property view_tb type=toolbar no_caption=1 store=no
	@property view_ct type=text no_caption=1 store=no

@default group=spec_versions

	@property version_tb type=toolbar no_caption=1 store=no
	@property version_table type=table no_caption=1 store=no

@groupinfo spec caption="Koosta" 
@groupinfo spec_view caption="&Uuml;levaade" submit=no
@groupinfo spec_versions caption="Versioonid" submit=no

@reltype VERSION value=1 clid=CL_AW_SPEC_VERSION
@caption Versioon
*/

class aw_spec extends class_base
{
	const AW_CLID = 1418;

	function aw_spec()
	{
		$this->init(array(
			"tpldir" => "applications/aw_spec/aw_spec",
			"clid" => CL_AW_SPEC
		));

		$this->tree_struct = array(
			array(0, "intro", t("Sissejuhatus"), "spec_editor"),
				array("intro", "intro_overview", t("&Uuml;levaade"), "spec_editor"),
				array("intro", "intro_whom", t("Eesm&auml;rgid"), "spec_editor"),
				array("intro", "intro_why", t("&Auml;riline taust"), "spec_editor"),
				array("intro", "intro_scope", t("Ulatus, Skoop"), "spec_editor"),
				array("intro", "intro_users", t("Kasutajate kirjeldus"), "spec_editor"),
				array("intro", "intro_competitors", t("Konkurendid"), "spec_editor"),

			array(0, "pred", t("Ulatus"), "spec_editor"),
				array("pred", "pred_pred", t("Eeldused"), "spec_editor"),
				array("pred", "pred_dep", t("S&otilde;ltuvused"), "spec_editor"),
				array("pred", "pred_constraints", t("Piirangud"), "spec_editor"),

			array(0, "demands", t("N&otilde;uded"), "spec_editor"),
				array("demands", "demands_business", t("&Auml;rin&otilde;uded"), "spec_editor"),
				array("demands", "demands_func", t("Funktsionaalsed n&otilde;uded"), "spec_editor"),
				array("demands", "demands_log_data", t("Loogilised andmete n&otilde;uded"), "spec_editor"),
				array("demands", "demands_users", t("Kasutajan&otilde;uded"), "spec_editor"),
				array("demands", "demands_infomgmt", t("Infohalduse n&otilde;uded"), "spec_editor"),
				array("demands", "demands_server", t("N&otilde;uded serverile"), "spec_editor"),
				array("demands", "demands_usability", t("K&auml;ideldavuse n&otilde;uded"), "spec_editor"),
				array("demands", "demands_other", t("Muud n&otilde;uded"), "spec_editor"),


			array(0, "conf", t("Seadistatavus"), "spec_editor"),
				array("conf", "conf_what", t("Mida seadistada"), "spec_editor"),
				array("conf", "intro_how", t("Kuidas"), "spec_editor"),
				array("conf", "intro_who", t("Kes"), "spec_editor"),

			array(0, "classes", t("Klassid"), "spec_editor"),
				array("classes", "classes_who", t("Kellele"), "spec_editor"),
				array("classes", "classe_why", t("Miks"), "spec_editor"),
				array("classes", "classes_classes", t("Klasside nimekiri"), "class_list"),
				array("classes", "classes_ui", t("Kasutajaliides"), "spec_editor"),
				array("classes", "classes_msgs", t("Teated ja vead"), "spec_editor"),
			array(0, "prev", t("Eeskujud"), "spec_editor"),
			array(0, "bl", t("&Auml;riloogika"), "spec_editor"),
				array("bl", "classes_ucase", t("Kasutajalood"), "spec_editor"),
				array("bl", "classes_principles", t("S&uuml;steemi toimimisp&otilde;him&otilde;tted"), "spec_editor"),
				array("bl", "classes_examples", t("N&auml;ited"), "spec_editor"),
			array(0, "sol", t("Lahendus"), "spec_editor"),
				array("sol", "sol_gen", t("&Uuml;ldine"), "spec_editor"),
				array("sol", "sol_api", t("API Meetodid"), "spec_editor"),
				array("sol", "sol_int", t("S&uuml;steemiintegratsioon"), "spec_editor"),
		);
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
		$arr["disp"] = $_GET["disp"];
		$arr["disp2"] = $_GET["disp2"];
	}

	function callback_mod_retval($arr)
	{
		$arr["args"]["disp"] = $arr["request"]["disp"];
		$arr["args"]["disp2"] = $arr["request"]["disp2"];
	}

	function _get_spec_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_delete_button();
		$tb->add_separator();	
		$tb->add_cut_button(array("var" => "aw_spec_cut"));
		$tb->add_paste_button(array("var" => "aw_spec_cut", "folder_var" => "disp2"));
	}

	function _get_spec_tree($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		
		$disp = $this->_get_disp($arr);

		foreach($this->tree_struct as $item)
		{
			$t->add_item($item[0], array(
				"id" => $item[1],
				"url" => aw_url_change_var("disp2", null, aw_url_change_var("disp", $item[1])),
				"name" => $disp == $item[1] ? "<b>".$item[2]."</b>" : $item[2]
			));

			$mn = "_get_".$item[3]."_tree_items";
			if (method_exists($this, $mn))
			{
				$this->$mn($t, $arr["obj_inst"], $item[1]);
			}
		}
	}

	function _init_class_list_table($t)
	{
		$t->define_field(array(
			"name" => "jrk",
			"caption" => t("Jrk"),
		));
		$t->define_field(array(
			"name" => "class_pri",
			"caption" => t("Prioriteet"),
		));
		$t->define_field(array(
			"name" => "class_name",
			"caption" => t("Klassi nimi"),
		));
		$t->define_field(array(
			"name" => "class_desc",
			"caption" => t("Klassi kirjeldus"),
		));
		$t->define_field(array(
			"name" => "change",
			"caption" => t("Muuda"),
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	function _get_class_list($arr)
	{
		if (!$this->_is_visible($arr))
		{
			return PROP_IGNORE;
		}

		if (substr($arr["request"]["disp2"], -strlen("_rels")) == "_rels")
		{
			list($_t) = explode("_", $arr["request"]["disp2"]);
			$o = obj($_t);
			return $o->instance()->get_embed_rels($o, $arr);
		}
		else
		if ($this->can("view", $arr["request"]["disp2"]))
		{
			$o = obj($arr["request"]["disp2"]);
			return $o->instance()->get_embed_prop($o, $arr);
		}

		$t = $arr["prop"]["vcl_inst"];
		$this->_init_class_list_table($t);
		
		$data = $arr["obj_inst"]->spec_class_list();
		$data[-1] = obj();
		$data[-2] = obj();
		$data[-3] = obj();
		$data[-4] = obj();
		$data[-5] = obj();
		foreach($data as $idx => $dr)
		{
			$t->define_data(array(
				"class_desc" => html::textarea(array(
					"name" => "class_list[".$idx."][class_desc]",
					"value" => $dr->prop("desc"),
					"rows" => 5,
					"cols" => 60
				)),
				"class_name" => html::textbox(array(
					"name" => "class_list[".$idx."][class_name]",
					"value" => $dr->name(),
				)),
				"class_pri" => html::select(array(
					"name" => "class_list[".$idx."][pri]",
					"value" => $dr->prop("pri"),
					"options" => aw_spec_obj::get_priority_options()
				)),
				"jrk" => html::textbox(array(
					"name" => "class_list[".$idx."][jrk]",
					"value" => $dr->ord(),
					"size" => 5
				)),
				"sort_jrk" => is_oid($dr->id()) ? $dr->ord() : 1000000000,
				"change" => is_oid($dr->id()) ? html::href(array(
					"url" => html::get_change_url($dr->id(), array("return_url" => get_ru())),
					"caption" => t("Muuda")
				)) : "",
				"oid" => $dr->id()
			));
		}
		$t->set_default_sortby("sort_jrk");
		$t->set_numeric_field("sort_jrk");
		$t->set_caption(t("Sisesta klassid"));
	}

	function _set_class_list($arr)
	{
		if (!$this->_is_visible($arr))
		{
			return PROP_IGNORE;
		}

		if (substr($arr["request"]["disp2"], -strlen("_rels")) == "_rels")
		{
			list($_t) = explode("_", $arr["request"]["disp2"]);
			$o = obj($_t);
			return $o->instance()->set_embed_rels($o, $arr);
		}
		else
		if ($this->can("view", $arr["request"]["disp2"]))
		{
			$o = obj($arr["request"]["disp2"]);
			return $o->instance()->set_embed_prop($o, $arr);
		}

		$arr["obj_inst"]->set_spec_class_list($arr["request"]["class_list"]);
	}

	function _get_spec_editor($arr)
	{
		if (!$this->_is_visible($arr))
		{
			return PROP_IGNORE;
		}

		$arr["prop"]["value"] = $arr["obj_inst"]->meta($this->_get_disp($arr));
	}

	function _set_spec_editor($arr)
	{
		if (!$this->_is_visible($arr))
		{
			return PROP_IGNORE;
		}
		$arr["obj_inst"]->set_meta($this->_get_disp($arr), $arr["request"]["spec_editor"]);
	}

	private function _get_disp($arr)
	{
		$rv = $arr["request"]["disp"];
		if (!$rv)
		{
			foreach($this->tree_struct as $item)
			{
				if (isset($item[3]))
				{
					return $item[1];
				}
			}
		}
		return $rv;
	}

	private function _get_row_data($arr)
	{
		$disp = $this->_get_disp($arr);
		foreach($this->tree_struct as $item)
		{
			if ($item[1] == $disp)
			{
				return $item;
			}
		}
		throw new aw_exception("no row found for disp $disp!");
	}

	private function _is_visible($arr)
	{
		$rd = $this->_get_row_data($arr);
		if ($rd[3] != $arr["prop"]["name"])
		{
			return false;
		}
		return true;
	}

	function _init_relation_list_table($t)
	{
		$t->define_field(array(
			"name" => "jrk",
			"caption" => t("Jrk"),
		));
		$t->define_field(array(
			"name" => "rel_from",
			"caption" => t("Seos kust"),
		));
		$t->define_field(array(
			"name" => "rel_name",
			"caption" => t("Seose nimi"),
		));
		$t->define_field(array(
			"name" => "rel_to",
			"caption" => t("Seos kuhu"),
		));
	}

	function _get_relation_list($arr)
	{
		if (!$this->_is_visible($arr))
		{
			return PROP_IGNORE;
		}
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_relation_list_table($t);

		$class_picker = $this->get_class_picker($arr["obj_inst"]);		

		$data = $arr["obj_inst"]->spec_relation_list();
		$data[-1] = obj();
		$data[-2] = obj();
		$data[-3] = obj();
		$data[-4] = obj();
		$data[-5] = obj();
		foreach($data as $idx => $dr)
		{
			$t->define_data(array(
				"rel_from" => html::select(array(
					"name" => "rel_data[".$idx."][rel_from]",
					"value" => $dr->prop("rel_from"),
					"options" => $class_picker
				)),
				"rel_name" => html::textbox(array(
					"name" => "rel_data[".$idx."][rel_name]",
					"value" => $dr->name(),
				)),
				"rel_to" => html::select(array(
					"name" => "rel_data[".$idx."][rel_to]",
					"value" => $dr->prop("rel_to"),
					"options" => $class_picker
				)),
				"jrk" => html::textbox(array(
					"name" => "rel_data[".$idx."][jrk]",
					"value" => $dr->ord(),
					"size" => 5
				)),
				"sort_jrk" => is_oid($dr->id()) ? $dr->ord() : 1000000000
			));
		}
		$t->set_default_sortby("sort_jrk");
		$t->set_numeric_field("sort_jrk");
	}

	function _set_relation_list($arr)
	{
		if (!$this->_is_visible($arr))
		{
			return PROP_IGNORE;
		}
		$arr["obj_inst"]->set_spec_relation_list($arr["request"]["rel_data"]);
	}

	/** Returns a list of classes
		@attrib api=1 params=pos
		
		@param o required type=cl_aw_spec
			The spec to read clases from 

		@returns
			array { class_id => class_name } for all classes in the spec and the system
	**/
	public static function get_class_picker($o)
	{
		$clss = aw_ini_get("classes");
		$rv = array("" => t("--vali--"));
		foreach($o->spec_class_list() as $idx => $row)
		{
			$rv["new_".$idx] = $row->name();
		}
		$rv["sep"] = "-----------";
		foreach($clss as $clid => $cle)
		{
			$rv[$clid] = $cle["name"];
		}
		return $rv;
	}

	function _get_view_tb($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->add_button(array(
			"name" => "pdf",
			"url" => $this->mk_my_orb("get_pdf", array("id" => $arr["obj_inst"]->id())),
			"image" => "pdf.gif",
			"tooltip" => t("PDF")
		));
	}

	/**
		@attrib name=get_pdf
		@param id required
	**/
	function get_pdf($arr)
	{
		$str = $this->_get_overview(obj($arr["id"]));
		header("Content-type: application/pdf");
		die(get_instance("html2pdf")->convert(array("source" => $str)));
	}

	function _get_overview($o, $parent = 0, $prnt_num = "")
	{
		$str = "";
		$num = 1;
		foreach($this->tree_struct as $val)
		{
			if (!isset($val[3]) || $val[0] !== $parent)
			{
				continue;
			}
			$np = ($prnt_num != "" ? $prnt_num."." : "").$num;

			$fn = "_get_ovr_".$val[3];
			$tmp = $this->$fn($o, $val, $np);
			if ($tmp === false)
			{
				continue;
			}

			$str .= "<b>$np ".$val[2]."</b><br>";
			$str .= $tmp;
			$str .= "<br><br>";

			$str .= $this->_get_overview($o, $val[1], $np);
			$num++;
		}

		return $str;
	}

	function _get_view_ct($arr)
	{
		$arr["prop"]["value"] = $this->_get_overview($arr["obj_inst"]);
	}

	private function _get_ovr_spec_overview($o, $val)
	{
		return null;
	}

	private function _get_ovr_spec_editor($o, $val)
	{
		$content = nl2br($o->meta($val[1]));
		get_instance("alias_parser")->parse_oo_aliases($o->id(), $content);
		return $content;
//		return nl2br($o->meta($val[1]));
	}

	private function _get_ovr_class_list($o, $val, $prnt_num)
	{
		$str = "<br><br>";
		$num = 0;
		foreach($o->spec_class_list() as $row)
		{
			$np = self::format_chapter_num($prnt_num, ++$num);

			$str .= self::format_doc_entry(
				$np, 
				sprintf(t("Klass: %s"), $row->name()),
				nl2br($row->prop("desc"))
			);

			if (($val = $row->instance()->get_overview($row, $t, $np)) !== null)
			{
				$str .= $val;
			}
		}
		return $str;
	}

	private function _get_ovr_relation_list($o, $val, $prnt_num)
	{
		$str = "<br><br>";
		$class_picker = $this->get_class_picker($o);		

		$num = 0;
		foreach($o->spec_relation_list() as $row)
		{
			$str .= self::format_doc_entry(
				self::format_chapter_num($prnt_num, ++$num),
				sprintf(t("Seos: %s"), $row->name),
				sprintf(t("Klassist %s klassi %s"), $class_picker[$row->rel_from], $class_picker[$row->rel_to])
			);
		}
		return $str;
	}

	private function _get_class_list_tree_items($tree, $o, $pt)
	{
		foreach($o->spec_class_list() as $cl_oid => $cl)
		{
			$id = $pt."_".$cl_oid;
			$tree->add_item($pt, array(
				"id" => $id,
				"url" => aw_url_change_var("disp", "classes_classes", aw_url_change_var("disp2", $cl_oid)),
				"name" => $_GET["disp2"] == $cl_oid ? "<b>".$cl->name()."</b>" : $cl->name(),
				"iconurl" => aw_ini_get("baseurl")."/automatweb/images/aw06/favicon.ico"
			));

			$t = obj($cl_oid);
			if (method_exists($t->instance(), "get_tree_items"))
			{
				$t->instance()->get_tree_items($tree, $t, $id);
			}

			$tree->add_item($id, array(
				"id" => $id."_rels",
				"url" => aw_url_change_var("disp", "classes_classes", aw_url_change_var("disp2", $cl_oid."_rels")),
				"name" => $_GET["disp2"] == $cl_oid."_rels" ? "<b>".sprintf(t("%s seosed"), $cl->name())."</b>" : sprintf(t("%s seosed"), $cl->name()),
				"iconurl" => aw_ini_get("baseurl")."/automatweb/images/nool1.gif"
			));
		}
	}

	/** Formats the chapter number from parent and current entry
		@attrib api=1 params=pos

		@param parent_num required type=string
			The parent chapter number

		@param num required type=string
			The current level item number
	
		@returns
			Combined chapter number 
	**/
	public static function format_chapter_num($parent_num, $num)
	{
		return ($parent_num != "" ? $parent_num."." : "").$num;
	}

	/** Formats document chapter
		@attrib api=1 params=pos

		@param num required type=string
			The chapter number

		@param title required type=string
			The chapter title

		@param content required type=string
			The chapter content

		@returns
			Properly formatted chapter to concanenate to the current document
	**/
	public static function format_doc_entry($num, $title, $content)
	{
		$str = "";
		$str .= "<b>$num ".$title."</b><br>";
		$str .= $content;
		$str .= "<br><br>";
		return $str;
	}

	function _get_version_table($arr)
	{
		$arr["prop"]["vcl_inst"]->table_from_ol(
			new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_VERSION"))),
			array("name"),
			CL_AW_SPEC_VERSION
		);
	}

	function _get_version_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "version",
			"img" => "save.gif",
			"action" => "create_new_version",
			"tooltip" => t("Salvesta uus versioon")
		));
		$tb->add_delete_button();
	}

	/**
		@attrib name=create_new_version
	**/
	function create_new_version($arr)
	{
		obj($arr["id"])->save_new_version();
		return $arr["post_ru"];
	}

	function _get_enter_toolbar($arr)
	{
		if (empty($arr["request"]["disp2"]))
		{
			return PROP_IGNORE;
		}
		$d2 = obj($arr["request"]["disp2"]);
		switch($d2->class_id)
		{
			case CL_AW_SPEC_CLASS:
				return PROP_IGNORE;
		}

		$d = $this->_get_disp($arr);
		$arr["prop"]["value"] = $arr["obj_inst"]->meta("tb_".$d);
	}

	function _set_enter_toolbar($arr)
	{
		$d = $this->_get_disp($arr);
		$arr["obj_inst"]->set_meta("tb_".$d, $arr["prop"]["value"]);
	}
}

?>
