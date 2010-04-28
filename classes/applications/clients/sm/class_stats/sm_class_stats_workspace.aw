<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_SM_CLASS_STATS_WORKSPACE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_sm_class_stats_workspace master_index=brother_of master_table=objects index=aw_oid

@default table=aw_sm_class_stats_workspace
@default group=classes

@property toolbar type=toolbar no_caption=1 store=no

	@layout classes_split type=hbox

		@layout classes_left type=vbox parent=classes_split

			@layout classes_tree type=vbox area_caption=Klasside&nbsp;puu closeable=1 parent=classes_left
	
				@property classes_tree type=treeview no_caption=1 parent=classes_tree store=no

			@layout classes_groups type=vbox area_caption=Klasside&nbsp;grupid closeable=1 parent=classes_left
	
				@property classes_groups type=treeview no_caption=1 parent=classes_groups store=no

		@property classes_list type=table no_caption=1 store=no parent=classes_split

@groupinfo classes caption="Klassid"

*/

class sm_class_stats_workspace extends class_base
{
	const AW_CLID = 1493;

	function sm_class_stats_workspace()
	{
		$this->init(array(
			"tpldir" => "applications/clients/sm/class_stats/sm_class_stats_workspace",
			"clid" => CL_SM_CLASS_STATS_WORKSPACE
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
		}

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

	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_sm_class_stats_workspace(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => ""
				));
				return true;
		}
	}

	function _get_classes_tree($arr)
	{
		$clf = aw_ini_get("classfolders");
		foreach($clf as $id => $data)
		{
			$arr["prop"]["vcl_inst"]->add_item($data["parent"], array(
				"name" => $data["name"],
				"id" => $id,
				"url" => aw_url_change_var("clf", $id)
			));
		}
		if (!empty($arr["request"]["clf"]))
		{
			$arr["prop"]["vcl_inst"]->set_selected_item($arr["request"]["clf"]);
		}
		$arr["prop"]["vcl_inst"]->set_root_name(t("Klassid"));
		$arr["prop"]["vcl_inst"]->set_root_url(aw_url_change_var("clf", null));
	}

	function _get_classes_list($arr)
	{
		$clss = aw_ini_get("classes");
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_classes_list_table($t);
		$this->_filter_class_list($clss, $arr["request"]);

		$sum = array();
		foreach($clss as $class_id => $cld)
		{
			$o = obj();
			$o->set_class_id($class_id);
			$cld["id"] = $class_id;
			$cld["prop_cnt"] = 0;
			$cld["rel_cnt"] = 0;
			$cld["prop_table"] = 0;
			$cld["prop_meta"] = 0;
			foreach($o->get_property_list() as $pn => $pd)
			{
				$cld["prop_cnt"]++;
				if (empty($pd["store"]) || ($pd["store"] != "no" && $pd["store"] != "connect"))
				{
					if (!empty($pd["field"]) && $pd["field"] == "meta")
					{
						$cld["prop_meta"]++;
					}
					else
					{
						$cld["prop_table"]++;
					}
				
				}
			}
			foreach($o->get_relinfo() as $rid => $rdata)
			{
				if (is_numeric($rid))
				{
					$cld["rel_cnt"]++;
				}
			}
			
			foreach($cld as $k => $v)
			{
				if (!isset($sum[$k]))
				{
					$sum[$k] = 0;
				}
				if (is_numeric($v))
				{
					$sum[$k] += $v;
				}
			}
			$t->define_data($cld);
		}
		$t->set_default_sortby("name");
		$t->sort_by();
		$t->set_sortable(false);
		$t->define_data(array(
			"def" => html::strong(t("Summa")),
			"prop_cnt" => html::strong($sum["prop_cnt"]),
			"prop_table" => html::strong($sum["prop_table"]),
			"prop_meta" => html::strong($sum["prop_meta"]),
			"rel_cnt" => html::strong($sum["rel_cnt"]),
			"file" => count($clss)
		));
	}

	private function _filter_class_list(&$clss, $r)
	{
		if (empty($r["clf"]) && empty($r["grp"]))
		{
			return;
		}

		if ($this->can("view", $r["grp"]))
		{
			$o = obj($r["grp"]);
			$p = $o->class_list;
			foreach($clss as $clid => $cld)
			{
				if (!isset($p[$clid]))
				{
					unset($clss[$clid]);
				}
			}
			return;
		}
		// get all folders beneath $r["clf"] and then list all classes for those
		$clfs = array($r["clf"] => $r["clf"]);
		$c = aw_ini_get("classfolders");

		$this->_req_fetch_clfs($c, $r["clf"], $clfs);
		foreach($clss as $clid => $cld)
		{
			$pts = $this->make_keys(explode(",", isset($cld["parents"]) ? $cld["parents"] : ""));
			if (!count(array_intersect($clfs, $pts)))
			{
				unset($clss[$clid]);
			}
		}
	}

	private function _req_fetch_clfs($c, $parent, &$list)
	{
		foreach($c as $id => $dat)
		{
			if ($dat["parent"] == $parent)
			{
				$list[$id] = $id;
				$this->_req_fetch_clfs($c, $id, $list);
			}
		}
	}

	private function _init_classes_list_table(&$t)
	{
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "id"
		));
		$t->define_field(array(
			"name" => "def",
			"caption" => t("ID"),
			"align" => "left",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Name"),
			"align" => "left",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "file",
			"caption" => t("Fail"),
			"align" => "left",
			"numeric" => 1,
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "prop_cnt",
			"caption" => t("Omadusi"),
			"align" => "right",
			"numeric" => 1,
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "rel_cnt",
			"caption" => t("Seoset&uuml;&uuml;pe"),
			"align" => "right",
			"numeric" => 1,
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "prop_table",
			"caption" => t("Omadusi tabelis"),
			"align" => "right",
			"numeric" => 1,
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "prop_meta",
			"caption" => t("Omadusi metadatas"),
			"align" => "right",
			"numeric" => 1,
			"sortable" => 1
		));
	}

	function _get_toolbar($arr)
	{
		$pt = isset($arr["request"]["grp"]) ? $arr["request"]["grp"] : $arr["obj_inst"]->id();
		$arr["prop"]["vcl_inst"]->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"onClick" => "len = document.changeform.elements.length;str  = '';
	for(i = 0; i < len; i++)
	{
		if (document.changeform.elements[i].name.indexOf('sel') != -1 && document.changeform.elements[i].checked)
		{
			str += '&sel['+document.changeform.elements[i].value+']='+document.changeform.elements[i].value;
		}
	}
	
window.location.href='".html::get_new_url(CL_SM_CLASS_STATS_GROUP, $pt)."&'+str;",
			"url" => "#",
			"tooltip" => "new"
		));
	}

	function _get_classes_groups($arr)
	{	
		$arr["prop"]["vcl_inst"] = treeview::tree_from_objects(array(
			"tree_opts" => array(
				"type" => TREE_DHTML,
				"tree_id" => "smc",
				"persist_state" => true,
			),
			"root_item" => $arr["obj_inst"],
			"ot" => new object_tree(array(
				"class_id" => array(CL_SM_CLASS_STATS_GROUP),
				"parent" => $arr["obj_inst"]->id()
			)),
			"var" => "grp"
                ));
		foreach($arr["prop"]["vcl_inst"]->get_item_ids() as $id)
		{
			if ($id == $arr["obj_inst"]->id())
			{
				continue;
			}
			$d = $arr["prop"]["vcl_inst"]->get_item($id);
			$d["name"] .= " ".html::get_change_url($id, array("return_url" => get_ru()), html::img(array("url" => aw_ini_get("baseurl")."/automatweb//images/icons/edit.gif", "border" => "0")));
			$d["name"] .= " ".html::get_change_url($id, array("return_url" => get_ru()), html::img(array("url" => aw_ini_get("baseurl")."/automatweb//images/icons/delete.gif", "border" => "0")));
			$arr["prop"]["vcl_inst"]->set_item($d);
		}
	}
}

?>
