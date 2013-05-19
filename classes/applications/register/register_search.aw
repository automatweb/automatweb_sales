<?php

// register_search.aw - Registri otsing
/*

@classinfo syslog_type=ST_REGISTER_SEARCH relationmgr=yes no_status=1 no_comment=1

@default table=objects
@default field=meta
@default method=serialize


@default group=general

	@property register type=relpicker reltype=RELTYPE_REGISTER
	@caption Register, millest otsida

	@property per_page type=textbox size=5
	@caption Mitu kirjet lehel

	@property show_all_in_empty_search type=checkbox ch_value=1
	@caption T&uuml;hi otsing n&auml;itab k&otilde;iki

	@property show_only_act type=checkbox ch_value=1
	@caption N&auml;ita ainult aktiivseid objekte

	@property show_all_right_away type=checkbox ch_value=1
	@caption Otsingus n&auml;idatakse ilma otsimata k&otilde;iki

	@property notfound_text type=textarea rows=5 cols=40
	@caption Mida n&auml;idatakse kui midagi ei leita (%s on otsing)

	@property show_date type=checkbox ch_value=1
	@caption Tulemuste all on kuup&auml;ev

	@property results_from_all_langs type=checkbox ch_value=1
	@caption Tulemused k&otilde;ikidest keeltest


@default group=mkfrm

	@property sform_frm type=table store=no no_caption=1

	@property butt_text type=textbox
	@caption Otsi nupu tekst


@default group=mktbl

	@property sform_tbl type=table store=no no_caption=1


@default group=search

	@property search type=callback store=no callback=callback_get_sform no_caption=1
	@property search_res type=table store=no no_caption=1


@groupinfo mkfrm caption="Koosta otsinguvorm"
@groupinfo mktbl caption="Koosta tulemuste tabel"
@groupinfo search caption="Otsi" submit_method=get


@reltype REGISTER value=1 clid=CL_REGISTER
@caption register millest otsida

@reltype SEARCH_FOLDER value=2 clid=CL_MENU
@caption kaust millest otsida


*/

class register_search extends class_base
{
	function register_search()
	{
		$this->init(array(
			"tpldir" => "applications/register/register_search",
			"clid" => CL_REGISTER_SEARCH
		));

		$this->fts_name = "fulltext_search";
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "sform_frm":
				if (!$arr["obj_inst"]->prop("register"))
				{
					return PROP_IGNORE;
				}
				$this->do_sform_frm_tbl($arr);
				break;

			case "sform_tbl":
				if (!$arr["obj_inst"]->prop("register"))
				{
					return PROP_IGNORE;
				}
				$this->do_sform_tbl_tbl($arr);
				break;

			case "search_res":
				if (!$arr["obj_inst"]->prop("register"))
				{
					return PROP_IGNORE;
				}
				$this->do_search_res_tbl($arr);
				break;
		};

		$this->request = $arr["request"];
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "sform_frm":
				$arr["obj_inst"]->set_meta("fdata", $arr["request"]["fdata"]);
				break;

			case "sform_tbl":
				$arr["obj_inst"]->set_meta("tdata", $arr["request"]["tdata"]);
				break;
		}
		return $retval;
	}

	function _init_sform_frm_tbl($t)
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
			"name" => "searchable",
			"caption" => t("Otsitav"),
			"sortable" => 1,
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "is_num",
			"caption" => t("Numbrite vahemiku otsing"),
			"sortable" => 1,
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "is_chooser",
			"caption" => t("Valik olemasolevatest"),
			"sortable" => 1,
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "el_type",
			"caption" => t("Elemendi t&uuml;&uuml;p"),
			"sortable" => 1,
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "u_name",
			"caption" => t("Elemendi tekst"),
			"sortable" => 1,
			"align" => "center"
		));
	}

	function do_sform_frm_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_sform_frm_tbl($t);

		$fdata = $arr["obj_inst"]->meta("fdata");

		// get register
		$reg = obj($arr["obj_inst"]->prop("register"));
		$props = $this->get_props_from_reg($reg);

		$props[$this->fts_name]["caption"] = t("T&auml;istekstiotsing");
		/*$props["created"]["caption"] = t("Loodud");
		$props["modified"]["caption"] = t("Muudetud");
		$props["createdby"]["caption"] = t("Looja");
		$props["modifiedby"]["caption"] = t("Muutja");*/
		foreach($props as $pn => $pd)
		{
			if (!is_array($fdata[$pn]) || $fdata[$pn]["caption"] == "")
			{
				$fdata[$pn] = array(
					"caption" => $pd["caption"]
				);
			}
			$t->define_data(array(
				"jrk" => html::textbox(array(
					"size" => 5,
					"name" => "fdata[$pn][jrk]",
					"value" => $fdata[$pn]["jrk"]
				)),
				"el" => $pd["caption"],
				"searchable" => html::checkbox(array(
					"name" => "fdata[$pn][searchable]",
					"value" => 1,
					"checked" => ($fdata[$pn]["searchable"] == 1)
				)),
				"is_num" => html::checkbox(array(
					"name" => "fdata[$pn][is_num]",
					"value" => 1,
					"checked" => ($fdata[$pn]["is_num"] == 1)
				)),
				"is_chooser" => html::checkbox(array(
					"name" => "fdata[$pn][is_chooser]",
					"value" => 1,
					"checked" => ($fdata[$pn]["is_chooser"] == 1)
				)),
				"u_name" => html::textbox(array(
					"name" => "fdata[$pn][caption]",
					"value" => $fdata[$pn]["caption"]
				)),
				"el_type" => html::select(array(
					"name" => "fdata[$pn][el_type]",
					"value" => $fdata[$pn]["el_type"],
					"options" => array(
						"" => t("--vormist--"),
						"select" => t("Rippmen&uuml;&uuml;"),
						"multiple_select" => t("Mitmene rippmen&uuml;&uuml;"),
						"checkbox" => t("Valikuruudud"),
						"radiobutton" => t("Raadionupp"),
					)
				)),
			));
		}

		$t->set_default_sortby("jrk");
		$t->sort_by();
	}


	function _init_sform_tbl_tbl($t)
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
			"name" => "defaultsort",
			"caption" => t("Vaikimisi sort"),
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
			"name" => "change_col",
			"caption" => t("Muuda tulp"),
			"sortable" => 1,
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "u_name",
			"caption" => t("Tulba pealkiri"),
			"sortable" => 1,
			"align" => "center"
		));
	}

	function do_sform_tbl_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_sform_tbl_tbl($t);

		$tdata = $arr["obj_inst"]->meta("tdata");

		// get register
		$reg = obj($arr["obj_inst"]->prop("register"));
		$props = $this->get_props_from_reg($reg);
		$max_jrk = 0;
		$props["change_link"]["caption"] = "Muuda";
		$props["view_link"]["caption"] = "Vaata";
		$props["del_link"]["caption"] = "Kustuta";
		foreach($props as $pn => $pd)
		{
			$defs = "";
			if (true || $tdata[$pn]["sortable"])
			{
				$defs = html::radiobutton(array(
					"name" => "tdata[__defaultsort]",
					"value" => $pn,
					"checked" => ($tdata["__defaultsort"] == $pn)
				));
			}
			$vc = "";
			$cc = "";
			if ($tdata[$pn]["visible"])
			{
				$vc = html::radiobutton(array(
					"name" => "tdata[__view_col]",
					"value" => $pn,
					"checked" => ($tdata["__view_col"] == $pn)
				));
				$cc = html::radiobutton(array(
					"name" => "tdata[__change_col]",
					"value" => $pn,
					"checked" => ($tdata["__change_col"] == $pn)
				));
			}
			$t->define_data(array(
				"jrk" => html::textbox(array(
					"size" => 5,
					"name" => "tdata[$pn][jrk]",
					"value" => $tdata[$pn]["jrk"]
				)),
				"el" => $pd["caption"],
				"visible" => html::checkbox(array(
					"name" => "tdata[$pn][visible]",
					"value" => 1,
					"checked" => ($tdata[$pn]["visible"] == 1)
				)),
				"sortable" => html::checkbox(array(
					"name" => "tdata[$pn][sortable]",
					"value" => 1,
					"checked" => ($tdata[$pn]["sortable"] == 1)
				)),
				"defaultsort" => $defs,
				"view_col" => $vc,
				"change_col" => $cc,
				"u_name" => html::textbox(array(
					"name" => "tdata[$pn][caption]",
					"value" => ($tdata[$pn]["caption"] == "" ? $pd["caption"] : $tdata[$pn]["caption"])
				)),
			));
		}

		$t->set_default_sortby("jrk");
		$t->sort_by();
	}

	////
	// !shows the search
	function show($arr)
	{
		aw_global_set("no_cache", 1);
		$ob = new object($arr["id"]);
		$request = array("rsf" => isset($_REQUEST["rsf"]) ? $_REQUEST["rsf"] : null);
		if (!empty($_REQUEST["search_butt"]))
		{
			$request["search_butt"] = $_REQUEST["search_butt"];
		}

		if (empty($request["search_butt"]) && is_array($request["rsf"]) && count($request["rsf"]))
		{
			$request["search_butt"] = 1;
		}

		if (!empty($_REQUEST["ft_page"]))
		{
			$request["ft_page"] = $_REQUEST["ft_page"];
		}

		if ($ob->prop("show_all_right_away"))
		{
			$request["search_butt"] = "vimbledon";
		}


		$props =  $this->get_sform_properties($ob, $request);
		$fdata = $ob->meta("fdata");

		$htmlc = new htmlclient();
		$htmlc->start_output();
		foreach($props as $pn => $pd)
		{
			if (substr($pn, 0, 11) === "rsf_uservar")
			{
				$pd["type"] = "select";
				$pd["multiple"] = 1;
			}

			preg_match("/rsf\[(.*)\]/imsU", $pd["name"], $mt);
			$nn = isset($mt[1]) ? $mt[1] : "";
			if (!empty($fdata[$nn]["el_type"]))
			{
				switch($fdata[$nn]["el_type"])
				{
					case "checkbox":
						$pd["type"] = "chooser";
						$pd["multiple"] = 1;
						break;

					case "radiobutton":
						$pd["type"] = "chooser";
						$pd["multiple"] = 0;
						break;

					case "select":
						$pd["type"] = $fdata[$nn]["el_type"];
						$pd["multiple"] = 0;
						break;

					case "multiple_select":
						$pd["type"] = "select";
						$pd["multiple"] = 1;
						break;
				}
			}

			$htmlc->add_property($pd);
		}
		$htmlc->finish_output();

		$html = $htmlc->get_result(array(
			"raw_output" => 1
		));

		$t = new aw_table(array(
			"layout" => "generic"
		));
		$this->do_search_res_tbl(array(
			"prop" => array(
				"vcl_inst" => $t
			),
			"obj_inst" => $ob,
			"request" => $request,
		));

		if (count($t->data) < 1 && !empty($request["search_butt"]) && $ob->prop("notfound_text"))
		{
			$table = nl2br(sprintf($ob->prop("notfound_text"), $request["rsf"][$this->fts_name]));
		}
		else
		if (!empty($request["search_butt"]))
		{
			$table = $t->draw();
		}
		else
		{
			$table = "";
		}

		if ($ob->prop("show_date") && !empty($request["search_butt"]))
		{
			$table .= html::linebreak().date("d.m.Y H:i:s");
		}

		if (!empty($arr["no_form"]))
		{
			return $html.html::linebreak().$table;
		}

		$this->read_template("show.tpl");
		$this->vars(array(
			"form" => $html,
			"section" => aw_global_get("section"),
			"table" => $table
		));
		return $this->parse();
	}

	function get_props_from_reg($reg)
	{
		$properties = array();
		$awa = new aw_array($reg->prop("data_cfgform"));
		foreach($awa->get() as $cfid)
		{
			if (!is_oid($cfid) || !$this->can("view", $cfid))
			{
				continue;
			}
			$cff = obj($cfid);
			$class_id = $cff->prop("ctype");

			$cfgu = new cfgutils();
			$f_props = $cfgu->load_properties(array(
				"clid" => $class_id
			));

			$class_i = get_instance($class_id);
			$tmp = $class_i->load_from_storage(array(
				"id" => $cff->id()
			));

			foreach(safe_array($tmp) as $k => $v)
			{
				if ($v["name"] !== "needs_translation" && $v["name"] !== "is_translated")
				{
					$properties[$k] = $v;
					$properties[$k]["type"] = $f_props[$k]["type"];
				}
			}
		}

		$properties["created"] = array(
			"caption" => t("Loodud"),
			"type" => "datetime_select",
			"name" => "created"
		);
		$properties["createdby"] = array(
			"caption" => t("Looja"),
			"type" => "textbox",
			"name" => "createdby"
		);
		$properties["modified"] = array(
			"caption" => t("Muudetud"),
			"type" => "datetime_select",
			"name" => "modified"
		);
		$properties["modifiedby"] = array(
			"caption" => t("Muutja"),
			"type" => "textbox",
			"name" => "modifiedby"
		);
		return $properties;
	}

	function get_clid_from_reg($reg)
	{
		$awa = new aw_array($reg->prop("data_cfgform"));
		foreach($awa->get() as $cfid)
		{
			if (!is_oid($cfid) || !$this->can("view", $cfid))
			{
				continue;
			}
			$cff = obj($cfid);
			$class_id = $cff->prop("ctype");
			return $class_id;
		}
	}

	function get_ot_from_reg($reg)
	{
		$awa = new aw_array($reg->prop("data_cfgform"));
		foreach($awa->get() as $cfid)
		{
			if (!is_oid($cfid) || !$this->can("view", $cfid))
			{
				continue;
			}
			$cff = obj($cfid);

			$ot = $cff->connections_to(array(
				"from.class_id" => CL_OBJECT_TYPE,
				"type" => 1
			));
			$ot = reset($ot);
			if($ot)
			{
				return $ot->prop("from");
			}
		}
	}

	function callback_get_sform($arr)
	{
		return $this->get_sform_properties($arr["obj_inst"], $arr["request"]);
	}

	function get_sform_properties($o, $request)
	{
		$reg = obj($o->prop("register"));
		$props = $this->get_props_from_reg($reg);

		$clid = $this->get_clid_from_reg($reg);
		$fdata = $o->meta("fdata");
		$ot = $this->get_ot_from_reg($reg);

		// load props for entire class, cause from cfgform we don't get all dat
		$cfgu = new cfgutils();
		$f_props = $cfgu->load_properties(array(
			"clid" => $clid
		));

		$tmp = array();
		foreach($props as $pn => $pd)
		{
			if (empty($fdata[$pn]["searchable"]))
			{
				continue;
			}

			if ($pd["type"] === "date_select" || $pd["type"] === "datetime_select")
			{
				$de = new date_edit();
				if ($pd["type"] === "datetime_select")
				{
					$de->configure(array(
						"day" => 1,
						"month" => 1,
						"year" => 1,
						"hour" => 1,
						"minute" => 1
					));
				}
				else
				{
					$de->configure(array(
						"day" => 1,
						"month" => 1,
						"year" => 1,
					));
				}
				$ts_from = -1;
				if ($request["rsf"][$pn."_from"])
				{
					$ts_from = date_edit::get_timestamp($request["rsf"][$pn."_from"]);
				}
				$ts_to = -1;
				if ($request["rsf"][$pn."_to"])
				{
					$ts_to = date_edit::get_timestamp($request["rsf"][$pn."_to"]);
				}
				if (!$pd["year_from"])
				{
					$pd["year_from"] = date("Y")-10;
				}
				if (!$pd["year_to"])
				{
					$pd["year_to"] = date("Y")+10;
				}
				$content = 	$de->gen_edit_form("rsf[".$pn."_from]", $ts_from, $pd["year_from"], $pd["year_to"], true)." - ".
							$de->gen_edit_form("rsf[".$pn."_to]", $ts_to, $pd["year_from"], $pd["year_to"], true);

				$tmp[$pn] = array(
					"name" => $pn,
					"type" => "text",
					"caption" => $fdata[$pn]["caption"],
					"value" => $content
				);
			}
			else
			{
				$tmp[$pn] = $pd + (array)$f_props[$pn];
				$tmp[$pn]["value"] = $request["rsf"][$pn];
				$tmp[$pn]["caption"] = $fdata[$pn]["caption"];

				// if is_chooser , make list of all possible options and insert into options.
				if ($fdata[$pn]["is_chooser"] == 1)
				{
					$this->mod_chooser_prop($tmp, $pn, $reg);
				}
			}
		}

		if ($fdata[$this->fts_name]["searchable"] == 1)
		{
			$tmp[$this->fts_name] = array(
				"name" => $this->fts_name,
				"type" => "textbox",
				"caption" => $fdata[$this->fts_name]["caption"],
				"value" => $request["rsf"][$this->fts_name]
			);

			if (aw_ini_get("site_id") == 125)
			{
				$tmp[$this->fts_name]["zee_shaa_helper"] = 1;
			}
		}

		if (!$clid)
		{
			return array();
		}
		$i = get_instance($clid);
		$xp = $i->parse_properties(array(
			"object_type_id" => $ot,
			"properties" => $tmp,
			"name_prefix" => "rsf"
		));

		$xp["search_butt"] = array(
			"name" => "search_butt",
			"caption" => $o->prop("butt_text"),
			"type" => "submit",
			"store" => "no",
		);

		return $xp;
	}

	function __proptbl_srt($pa, $pb)
	{
		$a = isset($this->__tdata[$pa]) ? $this->__tdata[$pa] : array("jrk" => null);
		$b = isset($this->__tdata[$pb]) ? $this->__tdata[$pb] : array("jrk" => null);

		if ($a["jrk"] == $b["jrk"])
		{
			return 0;
		}
		return $a["jrk"] > $b["jrk"];
	}

	function _init_search_res_tbl($t, $o)
	{
		$tdata = $o->meta("tdata");

		$cfgu = new cfgutils();
		$f_props = $cfgu->load_properties(array(
			"clid" => CL_REGISTER_DATA
		));

		// get register
		$reg = obj($o->prop("register"));
		$props = $this->get_props_from_reg($reg);
		$this->__tdata = $tdata;
		uksort($props, array($this, "__proptbl_srt"));

		$np = 0;
		foreach($props as $pn => $pd)
		{
			if (!empty($tdata[$pn]["visible"]))
			{
				$np++;
			}
		}

		foreach($props as $pn => $pd)
		{
			if (!empty($tdata[$pn]["visible"]))
			{
				$fd = array(
					"name" => $pn,
					"caption" => $tdata[$pn]["caption"],
					"sortable" => ifset($tdata, $pn, "sortable"),
					"width" => ((int)(100 / $np))."%"
				);
				if ($f_props[$pn]["type"] === "date_select" || $pd["type"] === "datetime_select")
				{
					$fd["type"] = "time";
					$fd["format"] = "Y-m-d";
					$fd["numeric"] = 1;
				}
				$t->define_field($fd);
			}
		}

		$pnn = array("change_link", "view_link", "del_link");
		foreach($pnn as $pn)
		{
			if ($tdata[$pn]["visible"])
			{
				$t->define_field(array(
					"name" => $pn,
					"caption" => $tdata[$pn]["caption"],
					"sortable" => ifset($tdata, $pn, "sortable"),
					"align" => "center"
				));
			}
		}
	}

	function get_search_results($o, $request, $reg_flds = false)
	{
		// return immediately if nothing is to be done
		if (empty($request["search_butt"]) && !$o->prop("show_all_right_away") && empty($request["MAX_FILE_SIZE"]))
		{
			return array(new object_list(), new object_list());
		}

		$reg = obj($o->prop("register"));
		$reg_i = $reg->instance();

		$props = $this->get_props_from_reg($reg);
		$fdata = $o->meta("fdata");

		if ($reg_flds === false)
		{
			$reg_flds = $reg_i->_get_reg_folders($reg);
			foreach($o->connections_from(array("type" => "RELTYPE_SEARCH_FOLDER")) as $c)
			{
				$reg_flds[] = $c->prop("to");
			}
		}
		else
		{
			$ign = true;
		}

		$filter = array(
			"class_id" => CL_REGISTER_DATA,
			"status" => $o->prop("show_only_act") ? STAT_ACTIVE : array(STAT_ACTIVE, STAT_NOTACTIVE),
		);

		if (empty($ign))
		{
			$filter[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"register_id" => $reg->id(),
					"parent" => $reg_flds
				)
			));
		}

		if ($o->prop("results_from_all_langs"))
		{
			$filter["lang_id"] = array();
		}

		$tmp = obj();
		$tmp->set_class_id(CL_REGISTER_DATA);
		$real_props = $tmp->get_property_list();

		foreach($props as $pn => $pd)
		{
			if (($pd["type"] === "datetime_select" || $pd["type"] === "date_select") && (isset($request["rsf"][$pn."_from"]) || isset($request["rsf"][$pn."_to"])))
			{
				$ts_f = date_edit::get_timestamp($request["rsf"][$pn."_from"]);
				$ts_t = date_edit::get_timestamp($request["rsf"][$pn."_to"]);
				if ($ts_f != -1 && $ts_t != -1)
				{
					$filter[$pn] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $ts_f, $ts_t);
				}
				else
				if ($ts_f != -1)
				{
					$filter[$pn] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $ts_f);
				}
				else
				if ($ts_t != -1)
				{
					$filter[$pn] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $ts_t);
				}
			}
			else
			if (!empty($request["rsf"][$pn]))
			{
				if ($fdata[$pn]["is_chooser"])
				{
					if (!empty($real_props[$pn]["reltype"]))
					{
						$filter["CL_REGISTER_DATA.".$real_props[$pn]["reltype"].".name"] = $request["rsf"][$pn];
					}
					else
					{
						$filter[$pn] = $request["rsf"][$pn];
					}
				}
				else
				if ($fdata[$pn]["is_num"] == 1)
				{
					list($from, $to) = explode("-", trim($request["rsf"][$pn]));
					if (!$from && !$to)
					{
						continue;
					}
					else
					if ($from && !$to)
					{
						$filter[$pn] = $from;
					}
					else
					{
						$filter[$pn] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $from, $to);
					}
				}
				else
				if ($pd["type"] === "classificator")
				{
					$filter[$pn] = $request["rsf"][$pn];
				}
				else
				if (is_array($request["rsf"][$pn]))
				{
					$filter[$pn] = $request["rsf"][$pn];
				}
				else
				{
					$filter[$pn] = "%".$request["rsf"][$pn]."%";
				}
			}
		}
		$cfgu = new cfgutils();
		$f_props = $cfgu->load_properties(array(
			"clid" => CL_REGISTER_DATA
		));

		// if fulltext search
		if ($request["rsf"][$this->fts_name] != "")
		{
			$tmp = array();
			foreach($f_props as $pn => $pd)
			{
				if ($pn === "status" || $pn === "register_id" || $f_props[$pn]["store"] === "no" || $f_props[$pn]["field"] === "meta"
|| $f_props[$pn]["type"] === "submit" || !isset($f_props[$pn]) || $f_props[$pn]["type"] === "date_select")
				{
					continue;
				}

				if ($f_props[$pn]["type"] === "classificator")
				{
				//	$tmp["CL_REGISTER_DATA.".$f_props[$pn]["reltype"].".name"] = "%".$request["rsf"][$this->fts_name]."%";
				}
				else
				{
					$tmp[$pn] = "%".$request["rsf"][$this->fts_name]."%";
				}
			}
			$filter[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => $tmp
			));
		}
		$filter[] = new object_list_filter(array("non_filter_classes" => CL_REGISTER_DATA));
		$tdata = $o->meta("tdata");

		if (!empty($_REQUEST["sortby"]))
		{
			$sp = $f_props[$_REQUEST["sortby"]];
			if ($sp)
			{
				$this->quote($_REQUEST["sort_order"]);
				$filter["sort_by"] = $sp["table"].".".$sp["field"]." ".$_REQUEST["sort_order"];
			}
		}
		else
		if ($tdata["__defaultsort"] != "")
		{
			$sp = $f_props[$tdata["__defaultsort"]];
			if ($sp)
			{
				$filter["sort_by"] = $sp["table"].".".$sp["field"]." ASC ";
			}
		}
		else
		{
			$filter["sort_by"] = "objects.name ASC ";
		}

		$si = __get_site_instance();
		if (method_exists($si, "refine_register_search_filter"))
		{
			$si->refine_register_search_filter($o, $filter);
		}

		if ((!empty($request["search_butt"]) || !empty($request["MAX_FILE_SIZE"])) || $o->prop("show_all_right_away") == 1)
		{
			$ol_cnt = new object_list($filter);
			if (($ppg = $o->prop("per_page")))
			{
				$filter["limit"] = ((isset($request["ft_page"]) ? $request["ft_page"] : 0) * $ppg).",".$ppg;
			}
			$ret = new object_list($filter);
		}
		else
		{
			if ($o->prop("show_all_in_empty_search") && !empty($request["search_butt"]))
			{
				$ol_cnt = new object_list($filter);
				if (($ppg = $o->prop("per_page")))
				{
					$filter["limit"] = ($request["ft_page"] * $ppg).",".$ppg;
				}
				$ret = new object_list($filter);
			}
			else
			{
				$ret = new object_list();
				$ol_cnt = new object_list();
			}
		}

		return array($ret, $ol_cnt);
	}

	function do_search_res_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_search_res_tbl($t, $arr["obj_inst"]);

		$tdata = $arr["obj_inst"]->meta("tdata");

		$reg = obj($arr["obj_inst"]->prop("register"));
		$props = $this->get_props_from_reg($reg);

		$can_change = false;
		$can_delete = false;

		list($ol, $ol_cnt) = $this->get_search_results($arr["obj_inst"], $arr["request"]);

		for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
		{
			$data = array();
			foreach($t->rowdefs as $k => $v)
			{
				if (substr($v["name"], 0, 6) == "userim")
				{
					// link to img
					$imgo = $o->get_first_obj_by_reltype("RELTYPE_IMAGE".substr($v["name"], 6));
					if (is_object($imgo) && $imgo-> class_id() == CL_IMAGE)
					{
						$imgi = $imgo->instance();
						$data[$v["name"]] = $imgi->make_img_tag_wl($imgo->id());
					}
				}
				else
				if (substr($v["name"], 0, 8) == "userfile")
				{
					// link to file
					$fileo = $o->get_first_obj_by_reltype("RELTYPE_FILE".substr($v["name"], 8));
					if ($fileo)
					{
						$filei = $fileo->instance();
						$data[$v["name"]] = html::href(array(
							"url" => $filei->get_url($fileo->id(), $fileo->name()),
							"caption" => $fileo->name()
						));
					}
				}
				else
				if ($v["name"] == "change_link")
				{
					if ($this->can("edit", $o->id()))
					{
						$data[$v["name"]] = html::href(array(
							"url" => $this->mk_my_orb("change", array("section" => aw_global_get("section"), "id" => $o->id()), $o->class_id()),
							"caption" => t("Muuda")
						));
						$can_change = true;
					}
					else
					{
						$data[$v["name"]] = "";
					}
				}
				else
				if ($v["name"] == "view_link")
				{
					$data[$v["name"]] = html::href(array(
						"url" => $this->mk_my_orb("view", array("id" => $o->id(), "section" => aw_global_get("section")), $o->class_id()),
						"caption" => t("Vaata")
					));
				}
				else
				if ($v["name"] == "del_link")
				{
					if ($this->can("delete", $o->id()))
					{
						$delurl = $this->mk_my_orb("delete", array("id" => $o->id(), "return_url" => get_ru()));
						$data[$v["name"]] = html::href(array(
							"url" => "#",
							"onClick" => "if(confirm(\"".t("Kustutada objekt?")."\")){window.location=\"$delurl\";};",
							"caption" => t("Kustuta")
						));
						$can_delete = true;
					}
				}
				else
				{
					$data[$v["name"]] = $o->prop_str($v["name"]);
					if (isset($tdata["__view_col"]) && $tdata["__view_col"] == $v["name"])
					{
						$data[$v["name"]] = html::href(array(
							"url" => $this->mk_my_orb("view", array("section" => aw_global_get("section"), "id" => $o->id()), $o->class_id()),
							"caption" => $data[$v["name"]]
						));
					}
					if (isset($tdata["__change_col"]) && $tdata["__change_col"] == $v["name"] && $this->can("edit", $o->id()))
					{
						$data[$v["name"]] = html::href(array(
							"url" => $this->mk_my_orb("change", array("section" => aw_global_get("section"), "id" => $o->id()), $o->class_id()),
							"caption" => $data[$v["name"]]
						));
						$can_change = true;
					}
				}
			}
			$t->define_data($data);
		}

		if (!$can_change)
		{
			$t->remove_field("change_link");
		}

		if (!$can_delete)
		{
			$t->remove_field("del_link");
		}

		if ($tdata["__defaultsort"] != "")
		{
			$t->set_default_sortby($tdata["__defaultsort"]);
			$t->set_default_sorder("asc");
		}
		else
		{
			$t->set_default_sortby("name");
			$t->set_default_sorder("asc");
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

	function mod_chooser_prop(&$props, $pn, $reg )
	{
		// since storage can't do this yet, we gots to do sql here :(
		$p =& $props[$pn];
		$opts = array("" => "");
		if ($p["table"] != "" && $p["field"] != "")
		{
			// also must filter by register data folder
			$reg_i = $reg->instance();
			$flds = $reg_i->_get_reg_folders($reg);

			// this is an expensive query, so cache the results
			$cfn = "register_".$reg->id()."_search_mod_chooser_p_".$pn;

			if (true || !($res = cache::file_get_ts($cfn, cache::get_objlastmod())))
			{
				if ($p["store"] == "connect")
				{
					$relist = obj();
					$relist->set_class_id(CL_REGISTER_DATA);
					$relist = $relist->get_relinfo();
					$q = "SELECT distinct(t.name) as val FROM aliases a LEFT JOIN objects o ON o.oid = a.source left join objects t on t.oid = a.target WHERE o.class_id = ".CL_REGISTER_DATA." AND o.status > 0 AND a.reltype = ".$relist[$p["reltype"]]["value"];
				}
				else
				{
					$q = "SELECT distinct($p[field]) as val FROM $p[table]
						LEFT JOIN objects ON objects.oid = ".$p["table"].".aw_id WHERE objects.parent IN(".join(",",$flds).") AND objects.status > 0";
				}

				$this->db_query($q);
				while ($row = $this->db_next())
				{
					$opts[$row["val"]] = $row["val"];
				}
				cache::file_set($cfn, aw_serialize($opts));
			}
			else
			{
				$opts = aw_unserialize($res);
			}
		}

		$p["type"] = "select";
		$p["options"] = $opts;
	}

	/**

		@attrib name=delete

		@param id required type=int acl=view;delete
		@param return_url required
	**/
	function delete($arr)
	{
		$o = obj($arr["id"]);
		$o->delete();

		header("Location: ".$arr["return_url"]);
		die();
	}

}
