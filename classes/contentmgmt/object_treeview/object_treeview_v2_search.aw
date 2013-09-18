<?php
// object_treeview_v2_search.aw - Objektinimekirja otsing
/*

@classinfo syslog_type=ST_OBJECT_TREEVIEW_V2_SEARCH relationmgr=yes no_comment=1 no_status=1 maintainer=kristo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property tv type=relpicker reltype=RELTYPE_OTV
@caption Objektinimekiri

@property predicate type=chooser
@caption Otsinguloogika

@groupinfo search_form caption="Koosta vorm"
@default group=search_form

	@property search_fields type=table store=no no_caption=1
	@caption Otsingu v&auml;ljad

@groupinfo search_table caption="Koosta tulemuste tabel"
@default group=search_table

	@property search_tbl_fields type=table store=no no_caption=1
	@caption Tabeli v&auml;ljad

@groupinfo search_show caption="Otsi" submit_method=get
@default group=search_show

	@property search_show type=callback callback=search_gen_els
	@caption Tabeli v&auml;ljad

	@property search_res type=table store=no no_caption=1


@reltype OTV value=1 clid=CL_OBJECT_TREEVIEW_V2
@caption objektinimekiri

@reltype CTR value=2 clid=CL_FORM_CONTROLLER
@caption kontroller

*/

class object_treeview_v2_search extends class_base
{
	function object_treeview_v2_search()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/object_treeview/object_treeview_v2_search",
			"clid" => CL_OBJECT_TREEVIEW_V2_SEARCH
		));

		$this->predicates = array(
			"OR" => t("V&Otilde;I"),
			"AND" => t("JA")
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "predicate":
				$prop["options"] = $this->predicates;
				break;

			case "search_fields":
				$this->_search_fields($arr);
				break;

			case "search_tbl_fields":
				$this->_search_tbl_fields($arr);
				break;

			case "search_res":
				$this->_search_res($arr);
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
			case "search_fields":
				uasort($arr["request"]["dat"], array($this, "_sort_by_ord"));
				$arr["obj_inst"]->set_meta("search_fields", $arr["request"]["dat"]);
				break;

			case "search_tbl_fields":
				uasort($arr["request"]["dat"], array($this, "_sort_by_ord"));
				$arr["obj_inst"]->set_meta("search_tbl_fields", $arr["request"]["dat"]);
				break;
		}
		return $retval;
	}

	function _sort_by_ord($a, $b)
	{
		if (empty($a["ord"]) and !empty($b["ord"]))
		{
			return 1;
		}
		elseif (!empty($a["ord"]) and empty($b["ord"]))
		{
			return -1;
		}
		else
		{
			return ($a["ord"] == $b["ord"]) ? 0 : (($a["ord"] > $b["ord"]) ? 1 : -1);
		}
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function parse_alias($arr = array())
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	function show($arr)
	{
		$table = $html = "";
		$show_table = isset($arr["show_table"]) ? (bool) $arr["show_table"] : true;
		$show_form = isset($arr["show_form"]) ? (bool) $arr["show_form"] : true;
		$ob = new object($arr["id"]);
		$request = array("s" => automatweb::$request->arg("s"));

		if ($show_form)
		{
			$props =  $this->search_gen_els(array(
				"obj_inst" => $ob,
				"request" => $request
			));

			$htmlc = get_instance("cfg/htmlclient");
			$htmlc->start_output();

			if (!empty($arr["extra_args"]))
			{
				foreach($arr["extra_args"] as $name => $value)
				{
					$htmlc->add_property(array(
						"name" => $name,
						"type" => "hidden",
						"value" => $value
					));
				}
			}

			foreach($props as $pn => $pd)
			{
				$htmlc->add_property($pd);
			}

			$htmlc->finish_output();
			$html = $htmlc->get_result(array(
				"raw_output" => 1
			));
		}

		if ($show_table)
		{
			
			$t = new aw_table(array(
				"layout" => "generic"
			));

			$this->_search_res(array(
				"prop" => array(
					"vcl_inst" => &$t
				),
				"obj_inst" => &$ob,
				"request" => $request,
			));

			$table = $t->draw();
		}

		$this->read_template("show.tpl");
		$this->vars(array(
			"form" => $html,
			"section" => aw_global_get("section"),
			"table" => $table
		));
		return $this->parse();
	}

	function _init_search_fields_t(&$t)
	{
		$t->define_field(array(
			"name" => "property",
			"caption" => t("Omadus"),
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "in_form",
			"caption" => t("N&auml;ita vormis"),
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "text",
			"caption" => t("Tekst"),
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "ord",
			"caption" => t("J&auml;rjekord"),
			"align" => "center",
		));
	}

	function _search_fields($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_search_fields_t($t);

		$dat = $arr["obj_inst"]->meta("search_fields");

		$tvi = get_instance(CL_OBJECT_TREEVIEW_V2);

		$cl = $tvi->_get_col_list(array(
			"o" => $this->_get_otv($arr["obj_inst"]),
			"hidden_cols" => true
		));

		$cl["__fulltext"] = t("T&auml;istekstiotsing");

		foreach($cl as $pn => $pc)
		{
			$pc2 = $pc." ( ".$pn." ) ";
			$t->define_data(array(
				"property" => $pc2,
				"in_form" => html::checkbox(array(
					"name" => "dat[$pn][in_form]",
					"value" => 1,
					"checked" => $dat[$pn]["in_form"] == 1
				)),
				"text" => html::textbox(array(
					"name" => "dat[$pn][text]",
					"value" => (isset($dat[$pn]["text"]) ? $dat[$pn]["text"] : $pc)
				)),
				"ord" => html::textbox(array(
					"name" => "dat[$pn][ord]",
					"value" => $dat[$pn]["ord"],
					"size" => 5
				))
			));
		}
	}

	function _init_search_tbl_fields_t(&$t)
	{
		$t->define_field(array(
			"name" => "jrk",
			"caption" => t("J&auml;rjekord"),
			"sortable" => 1,
			"align" => "center",
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "el",
			"caption" => t("Element"),
			"sortable" => 1,
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "visible",
			"caption" => t("Tabelis"),
			"sortable" => 1,
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "sortable",
			"caption" => t("Sorditav"),
			"sortable" => 1,
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "view_col",
			"caption" => t("Vaata tulp"),
			"sortable" => 1,
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "u_name",
			"caption" => t("Tulba pealkiri"),
			"sortable" => 1,
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "ctr",
			"caption" => t("Kontroller"),
			"sortable" => 1,
			"align" => "center"
		));
	}

	function _search_tbl_fields($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_search_tbl_fields_t($t);

		$dat = $arr["obj_inst"]->meta("search_tbl_fields");

		$ctr_ol = new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_CTR")));
		$ctrs = $ctr_ol->names();
		$ctrs[0] = t("--Vali--");

		$tvi = get_instance(CL_OBJECT_TREEVIEW_V2);
		$cl = $tvi->_get_col_list(array(
			"o" => $this->_get_otv($arr["obj_inst"]),
			"hidden_cols" => true
		));
		foreach($cl as $pn => $pc)
		{
			$pc2 = $pc." ( ".$pn." ) ";

			$ctr = html::select(array(
				"name" => "dat[$pn][ctr]",
				"value" => $dat[$pn]["ctr"],
				"options" => $ctrs
			));

			$t->define_data(array(
				"jrk" => html::textbox(array(
					"name" => "dat[$pn][ord]",
					"value" => $dat[$pn]["ord"],
					"size" => 5
				)),
				"el" => $pc2,
				"visible" => html::checkbox(array(
					"name" => "dat[$pn][visible]",
					"value" => 1,
					"checked" => $dat[$pn]["visible"] == 1
				)),
				"sortable" => html::checkbox(array(
					"name" => "dat[$pn][sortable]",
					"value" => 1,
					"checked" => $dat[$pn]["sortable"] == 1
				)),
				"view_col" => html::checkbox(array(
					"name" => "dat[$pn][view_col]",
					"value" => 1,
					"checked" => $dat[$pn]["view_col"] == 1
				)),
				"u_name" => html::textbox(array(
					"name" => "dat[$pn][text]",
					"value" => isset($dat[$pn]["text"]) ? $dat[$pn]["text"] : $pc,
				)),
				"ctr" => ($dat[$pn]["visible"] ? $ctr : "")
			));
		}
	}

	function search_gen_els($arr)
	{
		$ret = array();

		$form_inf = safe_array($arr["obj_inst"]->meta("search_fields"));

		foreach($form_inf as $eln => $eld)
		{
			if (!$eld["in_form"])
			{
				continue;
			}

			$nm = "s[$eln]";
			$ret[$nm] = array(
				"name" => $nm,
				"type" => "textbox",
				"caption" => $eld["text"],
				"value" => $arr["request"]["s"][$eln]
			);
		}

		$ret["s_submit"] = array(
			"name" => "s_submit",
			"type" => "submit",
			"value" => t("Otsi"),
			"caption" => t("Otsi")
		);
		return $ret;
	}

	function _search_res($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->init_search_res_t($t, $arr["obj_inst"]);

		$res = $this->get_search_results($arr["obj_inst"], $arr["request"]);
		foreach($res as $row)
		{
			$row["modifiedby"] = $row["modder"];
			$row["createdby"] = $row["adder"];
			$row["modified"] = date("d.m.Y H:i", $row["mod_date"]);
			$row["created"] = date("d.m.Y H:i", $row["add_date"]);
			$row["size"] = $row["fileSizeKBytes"] . " kb";

			$t->define_data($row);
		}
	}

	function init_search_res_t(&$t, $o)
	{
		$tbl = safe_array($o->meta("search_tbl_fields"));

		foreach($tbl as $pn => $pd)
		{
			if (!$pd["visible"])
			{
				continue;
			}

			$def = array(
				"name" => $pn,
				"caption" => $pd["text"]
			);

			if ($pd["sortable"])
			{
				$def["sortable"] = true;
			}

			// if controller, eval it
			$show = true;
			if (is_oid($pd["ctr"]) && $this->can("view", $pd["ctr"]))
			{
				$fc = get_instance(CL_FORM_CONTROLLER);
				$show = $fc->eval_controller($pd["ctr"], $def);
			}

			if ($show)
			{
				$t->define_field($def);
			}
		}
	}

	function get_search_results($o, $req)
	{
		$otv2_o = $this->_get_otv($o);
		$d_o = obj($otv2_o->prop("ds"));
		$d_inst = $d_o->instance();

		$flt = array();
		foreach(safe_array($req["s"]) as $f => $v)
		{
			$flt[] = array(
				"field" => $f,
				"value" => $v
			);
		}

		if (count($flt) == 0)
		{
			return array();
		}

		$params = array(
			"filters" => array(
				"saved_filters" => new aw_array($flt),
			),
			"sproc_params" => $o->prop("sproc_params"),
			"predicate" => array_key_exists($o->prop("predicate"), $this->predicates) ? $o->prop("predicate") : "OR"
		);
		return $d_inst->get_objects($d_o, NULL, NULL, $params);
	}

	function _get_otv($o)
	{
		if ($this->can("view", $o->prop("tv")))
		{
			return obj($o->prop("tv"));
		}
		return obj();
	}
}
?>
