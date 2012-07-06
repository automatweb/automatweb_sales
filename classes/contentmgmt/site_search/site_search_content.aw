<?php
// site_search_content.aw - Saidi sisu otsing
/*

@classinfo relationmgr=yes

@default table=objects
@default field=meta
@default method=serialize

@default group=general

	@property search_static type=checkbox ch_value=1
	@caption Otsing staatilisse koopiasse

	@property search_live type=checkbox ch_value=1
	@caption Otsing aktiivsest saidist

	@property multi_groups type=checkbox ch_value=1
	@caption Otsimisel saab kasutada mitut gruppi

	@property per_page type=textbox size=5
	@caption Mitu tulemust lehel

	@property show_admin_if type=checkbox ch_value=1
	@caption Veebis keeruline otsimisliides

	@property max_num_results type=textbox size=5
	@caption Maksimaalne tulemuste arv

	@property min_s_len type=textbox size=5
	@caption Minimaalne otsingus&otilde;na pikkus

	@property max_s_len type=textbox size=5
	@caption Maksimaalne otsingus&otilde;na pikkus

@default group=keywords

	@property do_keyword_search type=checkbox field=meta method=serialize ch_value=1
	@caption Otsing m&auml;rks&otilde;nadest

	@property keyword_search_classes type=select multiple=1 field=meta method=serialize
	@caption Klassid

@default group=searchgroups

	@property default_order type=select
	@caption Vaikimisi sorteeritakse

	@property default_search_opt type=select
	@caption Vaikimisi otsingu t&uuml;&uuml;p

	@property grpcfg type=table
	@caption Otsingugruppide konfigureerimine

@default group=static

	@property reledit type=releditor reltype=RELTYPE_REPEATER use_form=emb rel_id=first
	@caption Seos

	@property static_gen_link type=text store=no
	@caption Staatilise genereerimise link


@groupinfo activity caption=Aktiivsus

	@property activity type=table group=activity no_caption=1
	@caption Aktiivsus


@default group=search_simple

	@property str type=textbox
	@caption Pealkiri/Sisu

	property s_title type=textbox
	caption Pealkiri

	property date_from type=date_select
	caption Alates

	property date_to type=date_select
	caption Kuni

	@property s_opt type=select
	@caption Leia

	property s_seatch_word_part type=checkbox ch_value=1
	caption Otsi s&otilde;naosa

	property s_group type=select
	caption Asukoht

	@property s_limit type=select
	@caption Mitu tulemust maksimaalselt

	@property search type=submit
	@caption Otsi

	@property results type=text no_caption=1
	@caption Tulemused

@default group=search_complex

	@property c_srch_els type=callback callback=callback_get_complex_els
	@caption Komplekotsingu elemendid

	@property c_search type=submit
	@caption Otsi

	@property c_results type=text no_caption=1
	@caption Tulemused


@reltype REPEATER value=1 clid=CL_RECURRENCE
@caption kordus staatilise koopia genereerimiseks

@reltype SEARCH_GRP value=2 clid=CL_SITE_SEARCH_CONTENT_GRP,CL_EVENT_SEARCH,CL_SHOP_PRODUCT_SEARCH,CL_SITE_SEARCH_CONTENT_GRP_HTML,CL_SITE_SEARCH_CONTENT_GRP_FS,CL_CRM_DB_SEARCH,CL_SITE_SEARCH_CONTENT_GRP_MULTISITE,CL_FORUM_V2
@caption otsingu grupp

@reltype CPLX_EL_CTR value=3 clid=CL_FORM_CONTROLLER
@caption kompleksotsingu elementide kontroller

@reltype CPLX_RES_CTR value=4 clid=CL_FORM_CONTROLLER
@caption kompleksotsingu tulemuste kontroller

@groupinfo static caption="Staatiline otsing"
@groupinfo keywords caption="M&auml;rks&otilde;nade j&auml;rgi otsing"
@groupinfo searchgroups caption="Otsingu grupid"
@groupinfo search caption="Otsi" submit_method=get
	@groupinfo search_simple caption="Lihtne otsing" submit_method=get parent=search
	@groupinfo search_complex caption="Detailotsing" submit_method=get parent=search

*/

define("S_ORD_TIME", 1);
define("S_ORD_TITLE", 2);
define("S_ORD_CONTENT", 3);
define("S_ORD_TIME_ASC", 4);
define("S_ORD_MATCH", 5);
define("S_ORD_POPULARITY", 6);

define("S_OPT_ANY_WORD", 1);
define("S_OPT_ALL_WORDS", 2);
define("S_OPT_PHRASE", 3);
define("S_OPT_WORD_PART", 4);

class site_search_content extends class_base
{
	function site_search_content()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/site_search/site_search_content",
			"clid" => CL_SITE_SEARCH_CONTENT
		));
		$this->site_id = aw_ini_get("site_id");

		$this->search_opts = array(
			S_OPT_ANY_WORD => t("&uuml;ksk&otilde;ik milline s&otilde;nadest (v&otilde;i)"),
			S_OPT_ALL_WORDS => t("koos k&otilde;igi s&otilde;nadega (ja)"),
			S_OPT_PHRASE => t("t&auml;pne fraas"),
			S_OPT_WORD_PART => t("s&otilde;naosa")
		);

		$this->limit_opts = array(
			"0" => t("K&otilde;ik"),
			"20" => "20",
			"50" => "50",
			"100" => "100"
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = class_base::PROP_OK;
		switch($prop["name"])
		{
			case "default_order":
				$prop["options"] = array(
					S_ORD_TIME => t("Muutmise kuup&auml;eva j&auml;rgi"),
					S_ORD_TITLE => t("Pealkirja j&auml;rgi"),
					S_ORD_CONTENT => t("Sisu j&auml;rgi"),
					S_ORD_MATCH => t("T&auml;psuse j&auml;rgi"),
					S_ORD_POPULARITY => t("Populaarsuse j&auml;rgi"),

				);
				break;

			case "static_gen_link":
				$prop['value'] = html::href(array(
					"url" => $this->mk_my_orb("generate_static", array("id" => $arr["obj_inst"]->id())),
					"caption" => t("uuenda staatiline koopia")
				));
				break;

			case "grpcfg":
				$this->do_grpcfg_table($arr);
				break;

			case "keyword_search_classes":
				foreach (aw_ini_get("classes") as $key => $class)
				{
					if(!empty($class["alias"]))
					{
						$options[$key] = $class["name"];
					}
				}
				asort($options);
				$prop["options"] = $options;
				break;

			case "default_search_opt":
				$prop["options"] = array("" => "") + $this->search_opts;
				break;

			case "activity":
				$this->mk_activity_table($arr);
				break;

			case "date_from":
			case "date_to":
				if ($arr["request"][$prop["name"]]["year"] > 0)
				{
					if ($arr["request"][$prop["name"]]["day"] == "---")
					{
						$arr["request"][$prop["name"]]["day"] = $prop["name"] == "date_from" ? 1 : 31;
					}

					if ($arr["request"][$prop["name"]]["month"] == "---")
					{
						$arr["request"][$prop["name"]]["month"] = $prop["name"] == "date_from" ? 1 : 12;
					}
				}

				$prop["year_to"] = 1990;
				$prop["year_from"] = date("Y");
				if (empty($arr["request"][$prop["name"]]))
				{
					$prop["value"] = -1;
					return class_base::PROP_OK;
				}
				$prop["value"] = $arr["request"][$prop["name"]];
				break;

			case "s_opt":
				$prop["options"] = $this->search_opts;
				if (empty($arr["request"]["s_opt"]))
				{
					$prop["value"] = $arr["obj_inst"]->prop("default_search_opt");
				}
				else
				{
					$prop["value"] = $arr["request"]["s_opt"];
				}
				break;

			case "s_limit":
				$prop["options"] = array(
					"20" => "20",
					"50" => "50",
					"100" => "100",
					"500" => "500",
				);
				$prop["value"] = isset($arr["request"]["s_limit"]) ? $arr["request"]["s_limit"] : 100;
				break;

			case "s_group":
				$ol = new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_SEARCH_GRP")));
				$prop["options"] = array("" => t("K&otilde;ikjalt")) + $ol->names();

			case "str":
			case "s_title":
			case "s_seatch_word_part":
				$prop["value"] = isset($arr["request"][$prop["name"]]) ? $arr["request"][$prop["name"]] : "";
				break;

			case "results":
				if (empty($arr["request"]["search"]))
				{
					return class_base::PROP_IGNORE;
				}
				if ($arr["obj_inst"]->prop("min_s_len") &&
					strlen($arr["request"]["str"]) < $arr["obj_inst"]->prop("min_s_len") &&
					$arr["request"]["str"] != "")
				{
					$prop["error"] = sprintf(t("Otsingus&otilde;na pikkus peab olema v&auml;hemalt %s t&auml;hem&auml;rki!"), $arr["obj_inst"]->prop("min_s_len"));
					return class_base::PROP_FATAL_ERROR;
				}
				if ($arr["obj_inst"]->prop("max_s_len") &&
					strlen($arr["request"]["str"]) > $arr["obj_inst"]->prop("max_s_len"))
				{
					$prop["error"] = sprintf(t("Otsingus&otilde;na peab olema l&uuml;hem kui %s t&auml;hem&auml;rki!"), $arr["obj_inst"]->prop("max_s_len"));
					return class_base::PROP_FATAL_ERROR;
				}
				$this->_search_results($arr);
				break;

			case "c_results":
				$o = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_CPLX_RES_CTR");
				if ($o && $arr["request"]["MAX_FILE_SIZE"])
				{
					$i = $o->instance();
					$i->eval_controller_ref($o->id(), $arr, $arr["prop"], $arr["prop"]);
				}
				break;
		}

		return $retval;
	}

	function do_grpcfg_table($arr)
	{
		$o = $arr["obj_inst"];
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "ord",
			"caption" => t("J&auml;rjekord"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "act",
			"caption" => t("Vaikimisi"),
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));

		$t->define_field(array(
			"name" => "sorder",
			"caption" => t("Vaikimisi sorteeritakse"),
		));

		$t->define_field(array(
			"name" => "caption",
			"caption" => t("Pealkiri"),
		));

		$conns = $o->connections_from(array(
			"type" => "RELTYPE_SEARCH_GRP",
		));

		$meta = $o->meta();
		$def = $o->meta("default_grp");

		$multi = $o->prop("multi_groups");

		$propname = $arr["prop"]["name"];

		$sort_opts = array(
			0 => t("--vali--"),
			S_ORD_TIME => t("Kuup&auml;eva j&auml;rgi (uuem enne)"),
			S_ORD_TIME_ASC => t("Kuup&auml;eva j&auml;rgi (vanem enne)"),
			S_ORD_TITLE => t("Pealkirja j&auml;rgi"),
			S_ORD_CONTENT => t("Sisu j&auml;rgi"),
			S_ORD_MATCH => t("T&auml;psuse j&auml;rgi"),
			S_ORD_POPULARITY => t("Populaarsuse j&auml;rgi")
		);

		foreach($conns as $conn)
		{
			$cid = $conn->prop("to");
			if ($multi)
			{
				$act = html::checkbox(array(
					"name" => "defaultgrp[${cid}]",
					"value" => $cid,
					"checked" => ($def[$cid] == $cid),
				));
			}
			else
			{
				$act = html::radiobutton(array(
					"name" => "defaultgrp",
					"value" => $cid,
					"checked" => ($def == $cid),
				));
			};

			$c_o = obj($cid);
			$t->define_data(array(
				"name" => html::get_change_url($cid, array("return_url" => get_ru()), $conn->prop("to.name")),
				"act" => $act,
				"sorder" => html::select(array(
					"name" => "${propname}[sorder][${cid}]",
					"options" => $sort_opts,
					"value" => isset($meta["grpcfg"]["sorder"][$cid]) ? $meta["grpcfg"]["sorder"][$cid] : ""
				)),
				"caption" => html::textbox(array(
					"name" => "${propname}[caption][${cid}]",
					"size" => 20,
					"value" => isset($meta["grpcfg"]["caption"][$cid]) ? $meta["grpcfg"]["caption"][$cid] : ""
				)),
				"ord" => html::textbox(array(
					"size" => 5,
					"name" => "${propname}[ord][${cid}]",
					"value" => $c_o->ord()
				))
			));
		}
	}

	function set_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = class_base::PROP_OK;
		$o = $arr["obj_inst"];
		switch($prop["name"])
		{
			case "grpcfg":
				$o->set_meta("grpcfg",$prop["value"]);
				$o->set_meta("default_grp",$arr["request"]["defaultgrp"]);
				foreach(safe_array($arr["request"]["grpcfg"]["ord"]) as $grp_id => $ord)
				{
					if (is_oid($grp_id) && $this->can("view", $grp_id))
					{
						$o = obj($grp_id);
						if ($o->ord() != $ord)
						{
							$o->set_ord($ord);
							$o->save();
						}
					}
				}
				break;

			case "static_gen_repeater":
				// set it to scheduler
				$sc = new scheduler();
				if ($prop["value"])
				{
					$sc->add(array(
						"event" => $this->mk_my_orb("generate_static", array("id" => $arr["obj_inst"]->id())),
						"rep_id" => $prop["value"]
					));
				}
				else
				{
					$sc->remove(array(
						"event" => $this->mk_my_orb("generate_static", array("id" => $arr["obj_inst"]->id())),
					));
				}
				break;

			case "reledit":
				$this->add_scheduler = true;
				break;

			case "activity":
				$ol = new object_list(array(
					"class_id" => CL_SITE_SEARCH_CONTENT
				));
				for ($o = $ol->begin(); !$ol->end(); $o = $ol->next())
				{
					if ($o->flag(OBJ_FLAG_IS_SELECTED) && $o->id() != $arr["request"]["active"])
					{
						$o->set_flag(OBJ_FLAG_IS_SELECTED, false);
						$o->save();
					}
					else
					if ($o->id() == $arr["request"]["active"] && !$o->flag(OBJ_FLAG_IS_SELECTED))
					{
						$o->set_flag(OBJ_FLAG_IS_SELECTED, true);
						$o->save();
					}
				}
				break;
		}
		return $retval;
	}

	function mk_activity_table($arr)
	{
		$table = $arr["prop"]["vcl_inst"];
		$table->parse_xml_def("activity_list");

		$pl = new object_list(array(
			"class_id" => CL_SITE_SEARCH_CONTENT
		));
		for($o = $pl->begin(); !$pl->end(); $o = $pl->next())
		{
			$actcheck = checked($o->flag(OBJ_FLAG_IS_SELECTED));
			$act_html = "<input type='radio' name='active' $actcheck value='".$o->id()."'>";
			$row = $o->arr();
			$row["active"] = $act_html;
			$table->define_data($row);
		};
	}

	function callback_post_save($arr)
	{
		if ($this->add_scheduler)
		{
			$o = $arr["obj_inst"];
			$recur_conns = $o->connections_from(array(
				"type" => "RELTYPE_REPEATER",
			));
			if (sizeof($recur_conns) > 0)
			{
				$rec = reset($recur_conns);
				$recur_obj_id = $rec->prop("to");
				$rec = new recurrence();
				$stamp = $rec->get_next_event(array(
					"id" => $recur_obj_id
				));
				// set it to scheduler
				$sc = new scheduler();
				$sc->add(array(
					"event" => $this->mk_my_orb("generate_static", array(
						"id" => $arr["obj_inst"]->id(),
						"stamp" => $stamp
					)),
					"time" => $stamp,
				));
			};
		};

	}

	////
	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($args = array())
	{
		extract($args);
		return $this->show(array("id" => $alias["target"]));
	}

	function get_groups($obj)
	{
		$ret = array();
		$co = $obj->connections_from(array(
			"type" => 2 //RELTYPE_SEARCH_GRP
		));
		foreach($co as $c)
		{
			$c_o = $c->to();
			$ret[] = array(
				"oid" => $c_o->id(),
				"name" => $c_o->name(),
				"jrk" => $c_o->ord()
			);
		}

		usort($ret, create_function('$a,$b', 'if ($a["jrk"] == $b["jrk"]) { return 0;}else if ($a["jrk"] > $b["jrk"]) { return 1;}else{return -1;}'));

		$rret = array();
		foreach($ret as $v)
		{
			$rret[$v["oid"]] = $v["name"];
		}

		return $rret;
	}

	function _init_trans()
	{
		if (isset($GLOBALS["lc_search_conf"]["LC_SEARCH_CONF_S_OPT_ANY_WORD"]))
		{
			$this->search_opts[S_OPT_ANY_WORD] = $GLOBALS["lc_search_conf"]["LC_SEARCH_CONF_S_OPT_ANY_WORD"];
		}

		if (isset($GLOBALS["lc_search_conf"]["LC_SEARCH_CONF_S_OPT_ALL_WORDS"]))
		{
			$this->search_opts[S_OPT_ALL_WORDS] = $GLOBALS["lc_search_conf"]["LC_SEARCH_CONF_S_OPT_ALL_WORDS"];
		}

		if (isset($GLOBALS["lc_search_conf"]["LC_SEARCH_CONF_S_OPT_PHRASE"]))
		{
			$this->search_opts[S_OPT_PHRASE] = $GLOBALS["lc_search_conf"]["LC_SEARCH_CONF_S_OPT_PHRASE"];
		}

		if (isset($GLOBALS["lc_search_conf"]["LC_SEARCH_CONF_LIMIT_ALL"]))
		{
			$this->limit_opts[0] = $GLOBALS["lc_search_conf"]["LC_SEARCH_CONF_LIMIT_ALL"];
		}
	}

	////
	// !this shows the object. not strictly necessary, but you'll probably need it, it is used by parse_alias
	function show($arr)
	{
		$arr["str"] = str_replace("'", "", $arr["str"]);
		extract($arr);
		$ob = new object($id);
		if ($ob->prop("show_admin_if") == 1)
		{
			return $this->_show_admin_if($arr);
		}
		$this->read_template("search.tpl");
		lc_site_load("search_conf", $this);


		$sectors_list = new object_list();
		$conns = $ob->connections_from(array(
			"type" => "RELTYPE_SEARCH_GRP",
		));
		foreach($conns as $conn)
		{
			$cid = obj($conn->prop("to"));
			if($cid->class_id() == CL_CRM_DB_SEARCH)
			{
				break;
			}
		}

		// Asukohad. CL_CRM_DB_SEARCH kasutab
		if(isset($cid) && is_object($cid) && $cid->class_id() == CL_CRM_DB_SEARCH)
		{
			$locs = array(
				"county",
				"city"
			);

			foreach($locs as $loc)
			{
				$LOC = strtoupper($loc);

				if($this->is_template($LOC."_OPTION"))
				{
					$ol = new object_list(array(
						"class_id" => constant("CL_CRM_".$LOC),
						new obj_predicate_sort(array(
							"jrk" => "ASC",
							"name" => "ASC"
						)),
					));
					$OPTION = "";
					foreach($ol->arr() as $o)
					{
						$this->vars(array(
							$loc."_value" => $o->id(),
							$loc."_caption" => $o->trans_get_val("name"),
							"selected" => $arr[$loc] == $o->id() ? "selected=\"selected\"" : "",
						));
						$OPTION .= $this->parse($LOC."_OPTION");
					}
					$this->vars(array(
						$LOC."_OPTION" => $OPTION,
					));
				}
			}
		}

		//paneb default v44rtused
		aw_session_set("active_section", $arr["field"]);
		$sec_opt = "";
		$parent = 0;

		if($cid && $cid->class_id() == CL_CRM_DB_SEARCH)
		{
			$sector_parents = new object_list(array(
				"oid" => $cid->prop("dir_tegevusala"),
				"sort_by" => "objects.jrk,objects.name",
			));
			$sectors_list = new object_list();

			foreach($sector_parents->ids() as $parent)
			{
				$sectors_list->add(new object_list(array(
					"parent" => $parent,
					"sort_by" => "objects.jrk,objects.name",
					"class_id" => CL_CRM_SECTOR,
				)));
			}

			foreach($sectors_list->arr() as $sec)
			{
				$selected = "";
				if($_SESSION["active_section"] == $sec->id())
				{
					$selected = "selected";
				}
				$this->vars(array(
					"sec_value" => $sec->id(),
					"sec_name" => strtoupper($sec->trans_get_val("name")),
					"selected" => $selected,
				));
				$sec_opt.= $this->parse("SEC_OPTION");

				//vahepeal kataloogid ka ritta
				$child_list = new object_list(array(
					"parent" => $sec->id(),
					"sort_by" => "objects.jrk,objects.name",
					"class_id" => CL_CRM_SECTOR,
				));
				foreach($child_list->arr() as $child)
				{
					$selected = "";
					if($_SESSION["active_section"] == $child->id())
					{
						$selected = "selected";
					}
					$this->vars(array(
						"sec_value" => $child->id(),
						"sec_name" => "--".$child->trans_get_val("name"),
						"selected" => $selected,
					));
					$sec_opt.= $this->parse("SEC_OPTION");
				}
			}
		}
		$keywords_by_parent = $kd = $keywords = array();

		if ($cid->is_a(CL_CRM_DB_SEARCH))
		{
			$keywords = $keywords_by_parent[0] = explode("," , $cid->prop("keywords"));
			$kd = $cid->prop("keywords2");
		}

		if(sizeof($kd))
		{
			$kw_odl = new object_data_list(
				array(
					"class_id" => CL_KEYWORD,
					"parent" => $kd,
					"sort_by" => "objects.jrk, objects.name"
				),
				array(
					CL_KEYWORD => array("name, parent"),
				)
			);
			foreach($kw_odl->arr() as $kw_oid => $kw_data)
			{
				$keywords[$kw_oid] = $kw_data["name"];
				$keywords_by_parent[$kw_data["parent"]][$kw_oid] = $kw_data["name"];
			}
			if($cid->keywords_by_folder && count($keywords_by_parent) > 0)
			{
				$kwp_ol = new object_list(array(
					"class_id" => menu_obj::CLID,
					"oid" => array_keys($keywords_by_parent),
					"sort_by" => "objects.jrk ASC"
				));
				$keyword_parents = $kwp_ol->names();
			}
			else
			{
				$keywords_by_parent[0] = $keywords;
			}
			$keyword_parents[0] = t("V&otilde;tmes&otilde;nad");
		}

		$key_opt = "";
		$key_opt_row = "";
		if($cid->is_property("keywords_in_row") and $cid->prop("keywords_in_row"))
		{
			$in_row = $cid->prop("keywords_in_row");
			$kw_cnt = 0;
			foreach($keyword_parents as $kwp_id => $kwp_name)
			{
				$keywords = $keywords_by_parent[$kwp_id];
				foreach($keywords as $key)
				{
					if(!trim(strtolower($key)))
					{
						continue;
					}
					$selected = "";
					if($arr["keyword"][strtolower(trim($key))])
					{
						$selected = "checked";
					}
					$this->vars(array(
						"key_value" => trim(strtolower($key)),
						"checked" => $selected,
					));
					$key_opt.= $this->parse("KEY_OPTION");
					$kw_cnt++;
					if($kw_cnt == $in_row)
					{
						$this->vars(array(
							"KEY_OPTION" => $key_opt,
						));
						$key_opt_row.= $this->parse("KEY_OPTION_ROW");
						$kw_cnt = 0;
						$key_opt = "";
					}
				}
				//see siis viimaste yksikute jaoks
				if($kw_cnt > 0)
				{
					$this->vars(array(
						"KEY_OPTION" => $key_opt,
					));
					$key_opt_row.= $this->parse("KEY_OPTION_ROW");
					$kw_cnt = 0;
					$key_opt = "";
				}
			}
		}
		else
		{
			foreach($keywords as $key)
			{
				if(!trim(strtolower($key))) continue;
				$selected = "";
				if($arr["keyword"][trim(strtolower($key))])
				{
					$selected = "checked";
				}
				$this->vars(array(
					"key_value" => trim(strtolower($key)),
					"checked" => $selected,
				));
				$key_opt.= $this->parse("KEY_OPTION");
			}
		}


		$this->_init_trans();

		$gr = $this->get_groups($ob);
		if (empty($group))
		{
			$group = $ob->meta("default_grp");
		}
		$s_gr = "";
		foreach($gr as $gid => $gname)
		{
			if (count($gr) == 1 && $gid == $ob->meta("default_grp"))
			{
				// no show group selecta if there is only one group and it is selected
				break;
			}
			$this->vars(array(
				"group" => $gid,
				"name" => $gname,
				"checked" => checked($group == $gid || isset($group[$gid])),
				"selected" => selected($group == $gid)
			));
			$s_gr .= $this->parse("GROUP");
		}

		$de = new date_edit();
		$de->configure(array(
			"day" => 1,
			"month" => 1,
			"year" => 1,
		));

		$sect = aw_global_get("section");
		if (aw_ini_get("user_interface.full_content_trans"))
		{
			$sect = aw_global_get("ct_lang_lc")."/".$sect;
		}
		$this->vars(array(
			"SEC_OPTION" => $sec_opt,
			"KEY_OPTION" => $key_opt,
			"KEY_OPTION_ROW" => $key_opt_row,
			"GROUP" => $s_gr,
			"reforb" => $this->mk_reforb("do_search", array("id" => $id, "no_reforb" => 1, "section" => $sect)),
			"str" => htmlspecialchars((isset($str) ? $str : "")),
			"str_opts" => $this->picker($opts["str"], $this->search_opts),
			"date_from" => $de->gen_edit_form("s_date[from]", $date["from"], date("Y")-3, date("Y"), true),
			"date_to" => $de->gen_edit_form("s_date[to]", $date["to"], date("Y")-3, date("Y"), true),
			"limit_opts" => $this->picker((isset($opts["limit"]) ? $opts["limit"] : null), $this->limit_opts)
		));

		return $this->parse();
	}

	/** this will get called via scheduler to generate the static content to search from
		@attrib name=generate_static params=name nologin=1
		@param id required
		@returns

		@comment
		parameters:
		id - required, id of the search object
	**/
	function generate_static($arr)
	{
		extract($arr);
		$log = fopen("/export/aw/automatweb/new/files/sexp_log.txt", "a");
		fwrite($log, "export started at ".date("d.m.Y H:i:s")." \n");

		// if we have a scheduler for this thing, then add the next time to the scheduler
		$o = obj($id);
		$rep = $o->get_first_obj_by_reltype("RELTYPE_REPEATER");
		if (is_object($rep))
		{
			$rec = new recurrence();
			$stamp = $rec->get_next_event(array(
				"id" => $rep->id(),
				"time" => time()+600
			));
			// set it to scheduler
			$sc = new scheduler();
			$sc->add(array(
				"event" => $this->mk_my_orb("generate_static", array(
					"id" => $arr["id"],
					"stamp" => $stamp
				)),
				"time" => $stamp,
			));
			fwrite($log, "add event ad time ".date("d.m.Y H:i", $stamp)."\n");
		}
		fclose($log);
		// these funcs must write data to a db table (static_content), with structure like this:
		// id, content, url, title, modified, section, lang_id, created_by
		// optional fields - url, section, lang_id, set to NULL if not available
		// if NULL, ignored in searches
		// id - md5 hash of the url, used in identifying whether we have the entry already or not
		// created_by - the crawler's id that created the entry, used when deleting removed files.

		// here we can add crawlers for different things. right now, only live site crawler
		$this->do_crawl_live_site($arr);

	}

	function do_crawl_live_site($arr)
	{
		// right. now we will have to crawl the site and write all the info to a database table
		// we use export_lite class for this.
		$ex = new export_lite();
		$ex->do_crawl();
	}

	////
	// !searches through static_content db table and returns results
	// params:
	//	str - string to search
	//	menus - the menus to search under
	function fetch_static_search_results($arr)
	{
		// rewrite fucked-up letters
		// IE
		/*$arr["str"] = str_replace(chr(0x9a), "&#0352;", $arr["str"]);
		$arr["str"] = str_replace(chr(0x8a), "&#0352;", $arr["str"]);
		$arr["str"] = str_replace("%9A", "&#0352;", $arr["str"]);
		$arr["str"] = str_replace("%8A", "&#0352;", $arr["str"]);

		// mozilla
		$arr["str"] = str_replace(chr(0xa8), "&#0352;", $arr["str"]);
		$arr["str"] = str_replace("%A8", "&#0352;", $arr["str"]);
		$arr["str"] = str_replace(chr(0xa6), "&#0352;", $arr["str"]);
		$arr["str"] = str_replace("%A6", "&#0352;", $arr["str"]);
		*/
		$arr["str"] = mb_strtolower($arr["str"], languages::USER_CHARSET);
		$str2 = mb_strtoupper($arr["str"], languages::USER_CHARSET);
		$this->quote($str2);
		extract($arr);
		$this->quote($str);

		// init variables
		$ret = array();
		$lim = $fulltext = $sections = $lang_id = $site_id = $date_s = $title_s = $content_s = $ob = "";

		// $ams = new aw_array($menus);
		//XXX: $menus was missing. Assuming it comes from $arr
		$ams = isset($arr["menus"]) ? new aw_array($arr["menus"]) : new aw_array();


		if ($ams->count())
		{
			$sections = " AND section IN (".$ams->to_sql().")";
		}

		if (empty($arr["no_lang_id"]))
		{
			$lang_id = " AND lang_id = '".aw_global_get("lang_id")."'";
		}

		if (!empty($arr["site_id"]))
		{
			$sit_awa = new aw_array($arr["site_id"]);
			$site_id = " AND site_id IN (".$sit_awa->to_sql().")";
		}

		if (aw_ini_get("site_search_content.has_fulltext_index") == 1)
		{
			$fts = "MATCH(title,content) AGAINST('\"$str\"')";
			$fulltext = ", ".$fts;
			$ob = " ORDER BY {$fts} DESC ";
		}

		$date = array();
		if ($arr["date"]["from"] > 1)
		{
			$date[] = "modified >= ".$arr["date"]["from"];
			$ob = " ORDER BY modified DESC ";
		}

		if ($arr["date"]["to"] > 1)
		{
			$date[] = "modified <= ".$arr["date"]["to"];
			$ob = " ORDER BY modified DESC ";
		}

		if (count($date))
		{
			$date_s = " AND (".join(" AND ", $date)." OR modified < 100 )";
		}

		if (!empty($arr["s_title"]))
		{
			$title_s = " AND ".$this->_get_sstring($arr["s_title"], $opts["str"], "title",true, $arr["s_seatch_word_part"]);
		}

		if ($arr["opts"]["limit"] > 0)
		{
			$lim = " LIMIT ".((int)$arr["opts"]["limit"]);
		}

		$content_s = $this->_get_sstring($str, $opts["str"], "content",true,$arr["s_seatch_word_part"]);
		$content_s2 = $this->_get_sstring($str2, $opts["str"], "content",true,$arr["s_seatch_word_part"]);
		if ($content_s == "" && $title_s == "" && $sections == "" && $lang_id == "" && $date_s == "" && $site_id == "")
		{
			return array();
		}
		$content_s = " ({$content_s} OR {$content_s2}) ";
		$sql = "
			SELECT
				url,
				title,
				modified,
				content,
				site_id
				{$fulltext}
			FROM
				static_content
			WHERE
				{$content_s} {$title_s}
				{$sections} {$lang_id} {$date_s} {$site_id} {$ob} {$lim}
		";
		$this->db_query($sql);
		while ($row = $this->db_next())
		{
			$ret[] = array(
				"url" => $row["url"],
				"title" => $row["title"],
				"modified" => $row["modified"],
				"content" => $row["content"],
				"match" => $row[$fts],
				"site_id" => $row["site_id"]
			);
		}

		// go over results and unique by title, but sorting the ones by the latest date
		$tmp = array();
		foreach($ret as $entry)
		{
			$tmp[$entry["title"]][] = $entry;
		}
		$res = array();
		foreach($tmp as $title => $dat)
		{
			usort($dat, create_function('$a,$b', 'return $a["modified"] == $b["modified"] ? 0 : ($a["modified"] > $b["modified"] ? 1 : -1);'));
			$res[] = $dat[0];
		}

		// go over res and throw out things that do not contain the search string
		$ret = array(); //$res;
		foreach($res as $i)
		{
			if ($opts["str"] == S_OPT_ANY_WORD || $opts["str"] == S_OPT_ALL_WORDS || strpos(mb_strtolower($i["content"], languages::USER_CHARSET), mb_strtolower($str, languages::USER_CHARSET)) !== false)
			{
				$ret[] = $i;
			}
		}
		return $ret;
	}

	////
	// !searches through the live site database and returns results. just documents
	// it does not even try to be clever - if you want to search everything, then use static search
	// params:
	//	str - string to search
	//	menus - the menus to search under
	function fetch_live_search_results($arr)
	{
		extract($arr);

		$ids = isset($arr["ids"]) ? $arr["ids"] : ""; ///XXX: teadmata otstarbega muutuja
		$ret = array();

		$ams = new aw_array($menus);

		$mod = $mod2 = "";

		if ($arr["date"]["from"] > 0)
		{
			$mod = "AND ((d.tm > 1 AND d.tm >= ".$arr["date"]["from"].") OR (d.tm < 1 AND o.modified >= ".$arr["date"]["from"]."))";
		}

		if ($arr["date"]["to"] > 0)
		{
			$mod2 = "AND ((d.tm > 1 AND d.tm < ".$arr["date"]["to"].") OR (d.tm < 1 AND o.modified < ".$arr["date"]["to"]."))";
		}

		$lim = "";
		if ($arr["opts"]["limit"] > 0)
		{
			$lim = " LIMIT ".((int)$arr["opts"]["limit"]);
		}

		$stat = " o.status = 2 AND ";
		if ($arr["opts"]["search_notactive"] == 1 || (is_array($arr["opts"]["str"]) && $arr["opts"]["str"]["search_notactive"] == 1))
		{
			$stat = " o.status > 0 AND ";
		}

		$lid_s = " o.lang_id = '".aw_global_get("lang_id")."' AND ";
		$opts["str"] = mb_strtolower($opts["str"], languages::USER_CHARSET);
		$this->quote($str);
		$joiner = "LEFT JOIN documents d ON o.brother_of = d.docid";
		if (aw_ini_get("user_interface.full_content_trans") &&
	                aw_ini_get("languages.default") != aw_global_get("ct_lang_id"))
		{
			$joiner = "LEFT JOIN doc_ct_content d ON d.oid = o.brother_of AND d.lang_id = ".aw_global_get("ct_lang_id");
			$lid_s = " d.lang_id = '".aw_global_get("lang_id")."' AND ";
			$stat = " o.status > 0 AND ";
		}

		$kw_limiter = "";
		if ($this->can("view", $arr["obj"]->prop("search_only_kws")) || (is_array($arr["obj"]->prop("search_only_kws")) && count($arr["obj"]->prop("search_only_kws"))))
		{
			$c = new connection();
			$cs = $c->find(array(
				"from.class_id" => doc_obj::CLID,
				"type" => "RELTYPE_KEYWORD",
				"to" => $arr["obj"]->prop("search_only_kws")
			));
			$lm_dids = new aw_array();
			foreach($cs as $con_entry)
			{
				$lm_dids->set($con_entry["from"]);
			}
			$kw_limiter  = " AND o.oid IN (".$lm_dids->to_sql().") ";
		}

		$sql = "
			SELECT
				o.oid as docid,
				d.title as title,
				o.modified as modified,
				d.lead as lead,
				d.content as content,
				d.tm as tm,
				d.modified as doc_modified,
				o.site_id as site_id,
				d.user1 as user1,
				d.user4 as user4
			 FROM
				objects o
				{$joiner}
			WHERE
				(
					".$this->_get_sstring($str, $opts["str"], "d.content")." OR
					".$this->_get_sstring($str, $opts["str"], "d.title")." OR
					".$this->_get_sstring($str, $opts["str"], "d.lead")." OR
					".$this->_get_sstring($str, $opts["str"], "d.author")." OR
					".$this->_get_sstring($str, $opts["str"], "d.photos")." OR
					".$this->_get_sstring($str, $opts["str"], "d.dcache")."
				) AND
				o.parent IN (".$ams->to_sql().") AND
				{$stat}
				{$ids}
				o.lang_id = '".aw_global_get("lang_id")."' AND
				d.no_search != 1 AND
				o.class_id IN (".doc_obj::CLID.",".CL_BROTHER_DOCUMENT.",".CL_PERIODIC_SECTION.")
				{$kw_limiter}
				{$mod}
				{$mod2}
				{$lim}
		";

		$this->db_query($sql);

		while ($row = $this->db_next())
		{
			if (!object_loader::can("view", $row["docid"]))
			{
				continue;
			}

			$this->save_handle();

			$doco = obj($row["docid"]);
			if (aw_ini_get("user_interface.full_content_trans") && aw_global_get("ct_lang_id") != aw_ini_get("languages.default"))
			{
				$stat = $doco->meta("trans_".aw_global_get("ct_lang_id")."_status") == 1 ? STAT_ACTIVE : STAT_NOTACTIVE;
			}
			else
			{
				$stat = $doco->status();
			}

			$this->restore_handle();

			if (!($arr["opts"]["search_notactive"] == 1 || (is_array($arr["opts"]["str"]) && $arr["opts"]["str"]["search_notactive"] == 1)) && $stat < STAT_ACTIVE)
			{
				continue;
			}

			$ret[] = array(
				"url" => $this->get_doc_url($row),
				"title" => $row["title"],
				"modified" => $row["doc_modified"],
				"content" => $row["content"],
				"lead" => $row["lead"],
				"tm" => $row["tm"],
				"doc_modified" => $row["doc_modified"],
				"user1" => $row["user1"],
				"user4" => $row["user4"],
				"docid" => $row["docid"],
				"target" => ($row["site_id"] != $this->site_id ? "target=\"_blank\"" : ""),
				"site_id" => $row["site_id"]
			);
		}

		if($arr["obj"]->prop("do_keyword_search"))
		{
			$keyresults = $this->search_keywords($str, $menus, $arr["obj"], $date);
			if($ret && $keyresults)
			{
				foreach($keyresults as $k => $v)
				{
					$ret[] = $v;
				}
			}
			else
			if($keyresults)
			{
				$ret = $keyresults;
			}
		}

		return $ret;
	}

	////
	// !merges two result sets together and returns the merged set. results are merged based on titles
	function merge_result_sets($orig, $add)
	{
		$lut = array();
		foreach($orig as $i)
		{
			$lut[strtolower(trim(strip_tags($i["title"])))] = 1;
		}

		$ret = $orig;
		foreach($add as $item)
		{
			if (!isset($lut[strtolower(trim(strip_tags($item["title"])))]))
			{
				$ret[] = $item;
			}
		}

		return $ret;
	}

	function search_keywords($str, $menus, $obj, $date)
	{
		$keyword_list = new object_list(array(
			"class_id" => CL_KEYWORD,
			"name" => "%$str%",
			"lang_id" => AW_REQUEST_CT_LANG_ID
		));
		//If keyword not found, no point to process it futher
		if($keyword_list->count() == 0)
		{
			return;
		}

		$classes = $obj->prop("keyword_search_classes");
		if (!is_array($classes) || count($classes) == 0)
		{
			return;
		}
		$keyword_to_file_conns = new connection();

		$keyword_to_file_conns = $keyword_to_file_conns->find(array(
			"from" => $keyword_list->ids(),
			"to.class_id" => $classes,
		));
		//arr($keyword_to_file_conns);
		if(!$keyword_to_file_conns)
		{
			//return;
		}

		$obj2kw = array();
		foreach($keyword_to_file_conns as $conn)
		{
			$ids_list[] = $conn["to"];
			$obj2kw[$conn["to"]][$conn["from"]] = $conn["from"];
		}

		//List of files oids
		//$ids_list[]

		if (count($ids_list))
		{
			$aliased_docs_conns = new connection();
			$aliased_docs_conns = $aliased_docs_conns->find(array(
				"to" => $ids_list,
				"from.class_id" => doc_obj::CLID,
			));

			foreach ($aliased_docs_conns as $conn)
			{
				$doc_ids[] = $conn["from"];
			}
		}
		// also, find all docs that are connected to keyword directly
		$c = new connection();
		$kw_to_doc_conns = $c->find(array(
			"from.class_id" => doc_obj::CLID,
			"type" => "RELTYPE_KEYWORD",
			"to" => $keyword_list->ids()
		));
		foreach($kw_to_doc_conns as $con)
		{
			$doc_ids[] = $con["from"];
		}

		if(!$doc_ids || !count($doc_ids))
		{
			return;
		}
		$filtr = array(
			"oid" => $doc_ids,
			"parent" => $menus,
		);
		$ol = new object_list($filtr);

		$ret = array();
		foreach ($ol->arr() as $obj)
		{
			if ($date["from"] > 1 && $obj->modified() < $date["from"])
			{
				continue;
			}
			if ($date["to"] > 1 && $obj->modified() > $date["to"])
			{
				continue;
			}
			$ret[] = array(
				"url" => aw_ini_get("baseurl").$obj->id(),
				"title" => $obj->name(),
				"modified" => $obj->modified(),
				"content" => $obj->prop("content"),
				"lead" => $obj->prop("lead"),
				"tm" => $obj->prop("tm"),
				"doc_modified" => $obj->prop("doc_modified"),
				"keywords" => $obj2kw[$obj->id()]
			);
		}
		return $ret;
	}


	////
	// !returns an array of results matching the search
	// params:
	//	obj - object instance of the search object
	//	str - the search string
	//	group - the group to search from
	//  opts - search options
	function fetch_search_results($arr)
	{
		extract($arr);
		$g = new site_search_content_grp();
		// sealt tulevad ainult menyyd .. aga ma pean diilima ka teiste asjadega
		// see koostab nimekirja parentitest ehk asjadest, KUST ma otsima pean
		// aga mul on vaja mingeid callbacke, et saaks otsida ka mujalt
		$ms = $g->get_menus(array("id" => $group));
		// how do I differentiate here?
		$opts["limit"] = $arr["obj"]->prop("max_num_results");
		$ret = array();
		if (1 == $obj->prop("search_static"))
		{
			$ret = $this->fetch_static_search_results(array(
				"menus" => $ms,
				"str" => $str,
				"opts" => $opts,
				"date" => $date,
				"field" => $field,
				"keyword" => $keyword,
				"area" => $area,
			));
		}

		if (1 == $obj->prop("search_live"))
		{
			$go = obj($group);
			// $opts["search_notactive"] = $go->prop("search_notactive"); //FIXME: search_notactive propertyt pole olemas

			$ret = $this->merge_result_sets($ret, $this->fetch_live_search_results(array(
				"menus" => $ms,
				"str" => $str,
				"obj" => $arr["obj"],
				"opts" => $opts,
				"date" => $date,
				"field" => $field,
				"keyword" => $keyword,
				"area" => $area
			)));
			if(strlen($str) != strlen(htmlentities($str)))
			{
				$ret = $this->merge_result_sets($ret, $this->fetch_live_search_results(array(
					"menus" => $ms,
					"str" => htmlentities($str),
					"obj" => $arr["obj"],
					"opts" => $opts,
					"date" => $date,
					"field" => $field,
					"keyword" => $keyword,
					"area" => $area
				)));
			}
		}
		// make sure we only get unique titles in results
		$_ret = array();
		foreach($ret as $d)
		{
			$_ret[mb_strtoupper($d["title"], aw_global_get("charset"))] = $d;
		}

		return $_ret;
	}

	function _sort_title($a, $b)
	{
		return strcmp(mb_strtolower($a["title"]), mb_strtolower($b["title"]));
	}

	function _sort_time($a, $b)
	{
		$af = $a["doc_modified"] > 1 ? "doc_modified" : "modified";
		$bf = $b["doc_modified"] > 1 ? "doc_modified" : "modified";

		if ($a[$af] == $b[$bf])
		{
        	return 0;
		}
		return ($a[$af] > $b[$bf]) ? -1 : 1;
	}

	function _sort_time_asc($a, $b)
	{
		$af = $a["tm"] > 1 ? "tm" : "modified";
		$bf = $b["tm"] > 1 ? "tm" : "modified";

		return $a[$af] - $b[$bf];
	}

	function _sort_content($a, $b)
	{
		return strcmp($a["content"], $b["content"]);
	}

	function _sort_popularity($a, $b)
	{
		return $this->_pops[$b["docid"]] - $this->_pops[$a["docid"]];
	}

	////
	// !sorts the search results
	// params:
	//	results - array of search results, must be reference
	//	sort_by - the order to sort by
	function sort_results($arr)
	{
		switch($arr["sort_by"])
		{
			case S_ORD_TITLE:
				usort($arr["results"], array($this, "_sort_title"));
				break;

			case S_ORD_CONTENT:
				usort($arr["results"], array($this, "_sort_content"));
				break;

			case S_ORD_TIME_ASC:
				usort($arr["results"], array($this, "_sort_time_asc"));
				break;

			case S_ORD_POPULARITY:
				// init popularity table
				$stats = new document_statistics();
				$this->_pops = $stats->get_all_doc_stats();
				usort($arr["results"], array($this, "_sort_popularity"));
				break;

			case S_ORD_TIME:
			default:
				usort($arr["results"], array($this, "_sort_time"));
				break;
		}
	}

	////
	// !displays sorting links in the currently loaded search results template
	// parameters:
	//	params - array of parameters to use to make the sort link
	//	cur_page - the currently selected page
	function display_sorting_links($arr)
	{
		extract($arr);

		$params["page"] = $arr["cur_page"];

		$params1 = $params2 = $params3 = $params;
		$params1["sort_by"] = S_ORD_TIME;
		$params2["sort_by"] = S_ORD_TITLE;
		$params3["sort_by"] = S_ORD_CONTENT;

		$this->vars(array(
			"sort_modified" => aw_url_change_var("sort_by", S_ORD_TIME),
			"sort_title" => aw_url_change_var("sort_by", S_ORD_TITLE),
			"sort_content" => aw_url_change_var("sort_by", S_ORD_CONTENT),
			"sort_popularity" => aw_url_change_var("sort_by", S_ORD_POPULARITY),
		));

		$so_mod = "";
		if ($params["sort_by"] == S_ORD_TIME)
		{
			$so_mod = $this->parse("SORT_MODIFIED_SEL");
		}
		else
		{
			$so_mod = $this->parse("SORT_MODIFIED");
		}

		$so_title = "";
		if ($params["sort_by"] == S_ORD_TITLE)
		{
			$so_title = $this->parse("SORT_TITLE_SEL");
		}
		else
		{
			$so_title = $this->parse("SORT_TITLE");
		}

		$so_ct = "";
		if ($params["sort_by"] == S_ORD_CONTENT)
		{
			$so_ct = $this->parse("SORT_CONTENT_SEL");
		}
		else
		{
			$so_ct = $this->parse("SORT_CONTENT");
		}

		$so_pl = "";
		if ($params["sort_by"] == S_ORD_POPULARITY)
		{
			$so_pl = $this->parse("SORT_POPULARITY_SEL");
		}
		else
		{
			$so_pl = $this->parse("SORT_POPULARITY");
		}
		$this->vars(array(
			"SORT_MODIFIED" => $so_mod,
			"SORT_MODIFIED_SEL" => "",
			"SORT_CONTENT" => $so_ct,
			"SORT_CONTENT_SEL" => "",
			"SORT_TITLE" => $so_title,
			"SORT_TITLE_SEL" => "",
			"SORT_POPULARITY" => $so_pl,
			"SORT_POPULARITY_SEL" => "",
		));
	}

	////
	// !displays pageselector - list of pages and next/back buttons, assumes that a template with the subs is loaded
	// parameters:
	//	num_results - the number of total results
	//	cur_page - the current page in the results
	//	per_page - number of results per page
	//	params - search params, to make the next page link from
	function display_pageselector($arr)
	{
		$page = $arr["cur_page"];
		$cnt = $arr["num_results"];
		$per_page = $arr["per_page"];
		$params = $arr["params"];

		$num_pages = ceil(($cnt / $per_page));
		$page = (int)$page;
		$pg = "";
		$prev = "";
		$nxt = "";

		for ($i=0; $i < $num_pages; $i++)
		{
			$params["page"] = $i;
			if (true || $arr["link_type"] == 1)
			{
				$link = aw_url_change_var("page", $i);
			}
			else
			{
				$link = $this->mk_my_orb("do_search", $params);
			}
			$this->vars(array(
				"page" => str_replace("&", "&amp;", $link),
				"page_from" => ($i*$per_page)+1,
				"page_to" => min(($i+1)*$per_page,$cnt)
			));
			if ((int)$i == (int)$page)
			{
				$pg.=$this->parse("SEL_PAGE");
			}
			else
			{
				$pg.=$this->parse("PAGE");
			}
		}
		$this->vars(array(
			"prev" => str_replace("&", "&amp;", aw_url_change_var("page", (string)max((int)$page-1,0)))
		));

		$this->vars(array(
			"next" => str_replace("&", "&amp;", aw_url_change_var("page", min((int)$page+1,$num_pages-1)))
		));
		if ($page > 0)
		{
			$prev = $this->parse("PREVIOUS");
		}

		if (((int)$page) < (((int)$num_pages)-1))
		{
			$nxt = $this->parse("NEXT");
		}
		$this->vars(array(
			"PREVIOUS" => $prev,
			"NEXT" => $nxt,
			"PAGE" => $pg,
			"SEL_PAGE" => ""
		));
		$this->vars(array(
			"PAGESELECTOR" => $this->parse("PAGESELECTOR"),
			"count" => $cnt
		));

		$this->display_sorting_links(array(
			"cur_page" => $arr["cur_page"],
			"params" => $arr["params"]
		));
	}

	function _get_content($ct)
	{
		return "";
		$co = trim(strip_tags($ct));
		$co = substr($co,strpos($co,"\n"));
		$co = trim($co);
		$co = preg_replace("/#(.*)#/","",substr($co,0,strpos($co,"\n")));
		return $co;
	}

	////
	// !displays results on the selected page, assumes template is already loaded
	// parameters:
	//	results - array of all the results
	//	page - the current page
	//	per_page - number of results to show
	function display_result_page($arr)
	{
		extract($arr);

		// calc the offsets in the array
		$from = $page * $per_page;
		$to = ($page+1) * $per_page;
		$res = "";

		$tr = array();
		foreach($results as $result)
		{
			$tr[] = $result;
		}
		$results = $tr;

		$si = __get_site_instance();
		$di = new doc_display();

		for ($i = $from; $i < $to; $i++)
		{
			if (!isset($results[$i]))
			{
				continue;
			}
			if ($si && method_exists($si, "parse_document"))
			{
				$si->parse_document($results[$i]);
			}
			$results[$i]["url"] = preg_replace("/\&set_lang_id=\d+/imsU", "", str_replace("/index.aw?section=", "/", $results[$i]["url"]));

			if ($results[$i]["site_id"] > 0 && $results[$i]["site_id"] != aw_ini_get("site_id") && $this->can("view", $results[$i]["docid"]))
			{
				$do = obj($results[$i]["docid"]);
				$parent = $do->parent();
				$sp = array();
				$sp[] = $do->id();
				while ($parent)
				{
					// list all brothers that are with the correct site id and if found then
					// make path from that
					$ol = new object_list(array(
						"brother_of" => $parent,
					));
					if ($ol->count())
					{
						$bo = obj($ol->begin());
						foreach(array_reverse($bo->path()) as $pi)
						{
							$sp[] = $pi->id();
						}
						$url = aw_ini_get("baseurl")."?section=".$results[$i]["docid"]."&path=".join(",", array_reverse($sp));
						$results[$i]["url"] = $url;
					}
					$sp[] = $parent;
					$do = obj($parent);
					$parent = $do->parent();
				}
			}
			$tm = ($results[$i]["doc_modified"] ? $results[$i]["doc_modified"] : $results[$i]["modified"]);
			if ($tm > 300)
			{
				$md = date("d.m.Y", $tm);
			}
			else
			{
				$md = "";
			}
			if ($this->can("view", $results[$i]["docid"]))
			{
				$doc_text = strip_tags(str_replace(">", "> ", $di->get_document_text(array(), obj($results[$i]["docid"]))));
			}
			else
			{
				$doc_text = strip_tags(str_replace(">", "> ", $results[$i]["lead"]." ".$results[$i]["content"]));
			}
			$doc_text = preg_replace("/#(\w+?)(\d+?)(v|k|p|)#/i","",$doc_text);
			$this->vars(array(
				"link" => $results[$i]["url"],
				"title" => parse_obj_name($results[$i]["title"]),
				"title_high" => $this->_get_content_high($results[$i]["title"], $_GET["str"]),
				"modified" => $md,
				"content" => $this->_get_content($results[$i]["content"]),
				"content_high" => $this->_get_content_high($doc_text, $_GET["str"]),
				"lead" => preg_replace("/#(.*)#/","",$results[$i]["lead"]),
				"tm" => ($results[$i]["tm"] != "" ? $results[$i]["tm"] : date("d.m.Y", $results[$i]["modified"])),
				"user1" => $results[$i]["user1"],
				"target" => $results[$i]["target"]
			));
			$res .= $this->parse("MATCH");
		}

		$this->vars(array(
			"MATCH" => $res
		));
	}

	////
	// !generates the html for search results
	// parameters:
	//	sort_by - how to sort the results
	//	results - array of results to display
	//	str - the search string
	//	page - the page of the result set to display
	//	per_page - number of results to show per page,
	//	params - the parameters to use to make the next/prev page links
	function display_results($arr)
	{
		extract($arr);

		lc_site_load("search_conf", $this);

		if (count($results) < 1 && empty($multigroups))
		{
			$this->read_template("no_results.tpl");
			$this->vars(array(
				"str" => $str
			));
			return $this->parse();
		}

		$this->vars(array(
			"groupname" => isset($arr["groupname"]) ? $arr["groupname"] : "",
		));

		$this->read_template("search_results.tpl");

		$this->sort_results(array(
			"results" => &$results,
			"sort_by" => $sort_by,
			"grp_by_kw" => isset($arr["group_by_kw"]) ? $arr["group_by_kw"] : null
		));

		// .. and sort order links as well
		$this->display_pageselector(array(
			"num_results" => count($results),
			"cur_page" => $page,
			"per_page" => $per_page,
			"params" => $params
		));

		$this->display_result_page(array(
			"results" => $results,
			"page" => $page,
			"per_page" => $per_page
		));

		$this->vars(array(
		 	"GROUP_SEPARATOR" => $this->parse("GROUP_SEPARATOR")
		));

		$ret =  $this->parse();
		return $ret;
	}

	////
	// !sets the default values to $arr
	function set_defaults($arr)
	{
		$o = obj($arr["id"]);

		if (empty($arr["group"]))
		{
			$arr["group"] = $o->meta("default_grp");
		}

		if (empty($arr["sort_by"]))
		{
			$arr["sort_by"] = $o->meta("default_order");
		}

		if (!$arr["sort_by"])
		{
			$arr["sort_by"] = S_ORD_TIME;
		}

		if (empty($arr["page"]))
		{
			$arr["page"] = 0;
		}

		if (!isset($arr["s_date"])) $arr["s_date"] = array();
		if (!isset($arr["str"])) $arr["str"] = "";
		if (!isset($arr["field"])) $arr["field"] = "";
		if (!isset($arr["keyword"])) $arr["keyword"] = "";
		if (!isset($arr["area"])) $arr["area"] = "";

		if (empty($arr["opts"]["str"]))
		{
			$arr["opts"]["str"] = $o->prop("default_search_opt") ? $o->prop("default_search_opt") : S_OPT_PHRASE;
		}

		if (isset($arr["s_date"]["from"]))
		{
			$arr["date"]["from"] = date_edit::get_timestamp($arr["s_date"]["from"]);
		}

		if (isset($arr["s_date"]["to"]))
		{
			$arr["date"]["to"] = date_edit::get_timestamp($arr["s_date"]["to"]);
		}

		if (!isset($arr["date"]["from"]) or $arr["date"]["from"] < 1)
		{
			$arr["date"]["from"] = -1;
		}

		if (!isset($arr["date"]["to"]) or $arr["date"]["to"] < 1)
		{
			$arr["date"]["to"] = -1;
		}

		return $arr;
	}

	/**
		@attrib name=do_search_if params=name nologin="1" all_args="1"
	**/
	function do_search_if($arr)
	{
		return $this->show(array("id" => $arr["id"]));
	}

	/**

		@attrib name=do_search params=name nologin=1

		@param id optional
		@param group optional
		@param page optional
		@param str optional
		@param sort_by optional
		@param opts optional
		@param s_date optional
		@param field optional
		@param area optional
		@param county optional
		@param city optional
		@param keyword optional

		@returns


		@comment

	**/
	function do_search($arr)
	{
		if (!is_oid($arr["id"]))
		{
			// see if we got a default
			$ol = new object_list(array(
				"class_id" => CL_SITE_SEARCH_CONTENT,
				"flags" => array("mask" => OBJ_FLAG_IS_SELECTED, "flags" => OBJ_FLAG_IS_SELECTED)
			));
			if ($ol->count())
			{
				$o = $ol->begin();
				$arr["id"] = $o->id();
			}
		}
		error::view_check($arr["id"]);
		extract($this->set_defaults($arr));
		$o = obj($id);

		// redisplay the search form
		$ret = $this->show(array(
			"id" => $id,
			"str" => $str,
			"group" => $group,
			"opts" => $opts,
			"date" => $date,
			"field" => $field,
			"keyword" => $keyword,
			"area" => $area,
			"county" => isset($county) ? $county : "",
			"city" => isset($city) ? $city : "",
		));
		$results = array();

		// seda peab siis kuidagi filtreerima ka .. et ta ei hakkas mul igasugu ikaldust n2itama
		if ($str != "" || $area || (is_array($keyword) && sizeof($keyword)) || is_oid($field))
		{
			if (1 == $o->prop("multi_groups"))
			{
				$conns = $o->connections_from(array(
					"type" => "RELTYPE_SEARCH_GRP",
				));
				if (!is_array($group))
				{
					$group = array($group => $group);
				}
				$grpcfg = $o->meta("grpcfg");
				$has_res = false;
				foreach($conns as $_idx => $conn)
				{
					if (count($group) > 0 && !isset($group[$conn->prop("to")]))
					{
						continue;
					}

					$cid = $conn->prop("to");
					if ($conn->prop("to.class_id") == CL_EVENT_SEARCH)
					{
						$t = get_instance(CL_EVENT_SEARCH);

						$clid = $o->class_id();
						$so = $conn->to();
						$results = $t->get_search_results(array(
							"id" => $so->id(),
							"str" => $str,
						));
					}
					elseif ($conn->prop("to.class_id") == CL_SITE_SEARCH_CONTENT_GRP)
					{
						if($str != "")
						{
							$results = $this->fetch_search_results(array(
								"obj" => $o,
								"str" => $str,
								"group" => $cid,
								"opts" => $opts,
								"date" => $date,
								"field" => $field,
								"keyword" => $keyword,
								"area" => $area,
							));
						}
					}
					else
					{
						$i = get_instance($conn->prop("to.class_id"));
						$results = $i->scs_get_search_results(array(
							"obj" => $o,
							"str" => $str,
							"group" => $cid,
							"opts" => $opts,
							"date" => $date,
							"field" => $field,
							"keyword" => $keyword,
							"area" => $area,
						));
					}
					$results_arr[$_idx] = $results;
					if (count($results))
					{
						$has_res = true;
					}
				}

				uasort($conns, array($this, "__grps"));

				foreach($conns as $_idx => $conn)
				{
					if (count($group) > 0 && !isset($group[$conn->prop("to")]))
					{
						continue;
					}
					$results = $results_arr[$_idx];
					$cid = $conn->prop("to");

					$grp_sort_by = $sort_by;
					if (!empty($grpcfg["sorder"][$cid]) && empty($sort_by))
					{
						$grp_sort_by = $grpcfg["sorder"][$cid];
					};

					$i = get_instance($conn->prop("to.class_id"));
					if (method_exists($i, "scs_display_search_results"))
					{
						$ret .= $i->scs_display_search_results(array(
							"results" => $results,
							"group" => $cid,
							"str" => $str
						));
					}
					else
					{
						if($str != "")
						{
							$tmp = $this->display_results(array(
								"groupname" => $grpcfg["caption"][$conn->prop("to")],
								"results" => $results,
								"obj" => $o,
								"str" => $str,
								"group" => reset($group),
								"sort_by" => $grp_sort_by,
								"str" => $str,
								"per_page" => ($o->meta("per_page") ? $o->meta("per_page") : 20),
								"params" => array(
									"id" => $id,
									"str" => $str,
									"sort_by" => $sort_by,
									"group" => reset($group),
									"section" => aw_global_get("section"),
									"sdate" => $s_date,
									"opts" => $opts,
								),
								"page" => is_array($page) ? $page[$conn->prop("to")] : $page,
								"multigroups" => $has_res
							));
							$tmp = str_replace("page=", "page[".$conn->prop("to")."]=", $tmp);
							$ret .= $tmp;
						}
					}

					$search = true;
					if (!$has_res)
					{
						break;
					}
				}
			}
			else
			{
				if($str != "")
				{
					$results = $this->fetch_search_results(array(
						"obj" => $o,
						"str" => $str,
						"group" => $group,
						"opts" => $opts,
						"date" => $date,
						"field" => $field,
						"keyword" => $keyword,
						"area" => $area
					));
				}

				$grp_sort_by = $sort_by;
				//XXX: $cid pole siin defineeritud, teadmata funkstionaalsus. parandada v6i kaotada
/*				if (!empty($grpcfg["sorder"][$cid]))
				{
					$grp_sort_by = $sort_by;
					if (!empty($grpcfg["sorder"][$cid]))
					{
						$grp_sort_by = $grpcfg["sorder"][$cid];
					}
				}
*/
				$ret .= $this->display_results(array(
					// "groupname" => $grpcfg["caption"][$group],//XXX: ajutiselt ka see v2lja sest grpcfg pole
					"results" => $results,
					"obj" => $o,
					"str" => $str,
					"group" => $group,
					"sort_by" => $grp_sort_by,
					"str" => $str,
					"per_page" => ($o->meta("per_page") ? $o->meta("per_page") : 20),
					"params" => array(
						"id" => $id,
						"str" => $str,
						"sort_by" => $sort_by,
						"group" => $group,
						"section" => aw_global_get("section"),
						"s_date" => $s_date,
						"opts" => $opts,
					),
					"page" => $page
				));
			}
		}

		return $ret;
	}

	/** this makes an url for document, taking into account the site id and making urls from that
	**/
	function get_doc_url($row)
	{
		if ($row["site_id"] != $this->site_id)
		{
			// get url from site list
			static $sl;
			if (!is_object($sl))
			{
				$sl = get_instance(CL_INSTALL_SITE_LIST);
			}
			return $sl->get_url_for_site($row["site_id"])."/".$row["docid"];
		}
		else
		{
			return aw_ini_get("baseurl").$row["docid"];
		}
	}

	function _get_sstring($str, $opt, $field, $static = false, $word_part = false)
	{
		if ($str == "")
		{
			return "1=1";
		}
		$words = explode(" ", $str);
		if ((aw_ini_get("site_search_content.has_fulltext_index") == 1) && $static)
		{
			$fld = $field;
			if ($fld == "content")
			{
				$fld = "title,content";
			}
			switch($opt)
			{
				case S_OPT_ANY_WORD:
					// rewrite string

					$str2 = $str;
					if ($word_part)
					{
						$str2 = str_replace(" ", "* ", trim($str));
						$str2.= "*";
					}
					$content_s = " MATCH($fld) AGAINST ('$str2' IN BOOLEAN MODE) ";
					break;

				case S_OPT_ALL_WORDS:
					if ($word_part)
					{
						$tmp = array();
						foreach($words as $word)
						{
							$tmp[] = $word."*";
						}
						$words = $tmp;
					}
					$content_s = "( ".join(" AND ", map("MATCH($fld) AGAINST ('%s' IN BOOLEAN MODE)", $words))." ) ";
					break;

				case S_OPT_PHRASE:
					if ($word_part)
					{
						$str .= "*";
					}
					$content_s = " MATCH($fld) AGAINST('\"$str\"'  IN BOOLEAN MODE) ";
					break;

				case S_OPT_WORD_PART:
					$str2 = str_replace(" ", "* ", trim($str));
					$str2.= "*";
					$content_s = " MATCH($fld) AGAINST ('$str2' IN BOOLEAN MODE) ";
					break;
			}
		}
		else
		{
			switch($opt)
			{
				case S_OPT_ANY_WORD:
					$content_s = "(".join(" OR ", map($field." like '%%%s%%'", $words)).")";
					break;

				case S_OPT_ALL_WORDS:
					$content_s = "(".join(" AND ", map($field." like '%%%s%%'", $words)).")";
					break;

				case S_OPT_PHRASE:
				default:
					$content_s = $field." like '%".$str."%'";
					break;
			}
		}
		return $content_s;
	}

	function on_site_init(&$dbi, &$site, &$ini_opts, &$log, &$osi_vars)
	{
		$conv = new converters();
		$conv->dc = $dbi->dc;

		// connect rootmenu to search grp
		$grp = obj($osi_vars["search_grp"]);
		$rootmenu = obj($ini_opts["frontpage"]);

		$grp->connect(array(
			"to" => $rootmenu->id(),
			"reltype" => 1
		));
		$grp->set_meta("section_include_submenus", array($rootmenu->id() => $rootmenu->id()));
		$grp->save();

		// connect search grp to search
		$s = obj($osi_vars["search_obj"]);
		$s->connect(array(
			"to" => $grp->id(),
			"reltype" => 2
		));

		// set opts
		$s->set_meta("default_grp", $grp->id());
		$s->save();

	}

	function __grps($a, $b)
	{
		return ($a->prop("to.jrk") == $b->prop("to.jrk")) ? 0 : $a->prop("to.jrk") > $b->prop("to.jrk") ? 1 : -1;
	}

	function _init_s_res_t($t)
	{
		$t->define_field(array(
			"name" => "loc",
			"caption" => t("&nbsp;"),
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "link",
			"caption" => t("&nbsp;"),
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "match",
			"caption" => t("Mitu korda sisaldab"),
			"align" => "center",
			"numeric" => 1,
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "title",
			"caption" => t("Pealkiri"),
			"align" => "center",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "mod",
			"caption" => t("Muudetud"),
			"align" => "center",
			"type" => "time",
			"format" => "d.m.Y H:i",
			"numeric" => 1,
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "cont",
			"caption" => t("Sisu"),
			"align" => "center"
		));
	}

	function _search_results($arr)
	{
		if (!is_admin())
		{
			return $this->_search_results_site($arr);
		}

		$t = new aw_table(array("layout" => "generic"));

		$this->_init_s_res_t($t);

		$arr["request"]["s_date"] = array(
			"from" => isset($arr["request"]["date_from"]) ? $arr["request"]["date_from"] : "",
			"to" => isset($arr["request"]["date_to"]) ? $arr["request"]["date_to"] : ""
		);

		// get search results
		$settings = $this->set_defaults($arr["request"]);
		$res = $this->get_multi_search_results($settings);

		$max_match = 0;
		foreach($res as $entry)
		{
			$max_match = max($max_match, $entry["match"]);
		}

		// show in table
		foreach($res as $entry)
		{
			// url, title, modified, content
			$nm = $entry["title"];
			$pi = pathinfo($nm);
			if (empty($pi["extension"]) || strlen($pi["extension"]) > 4)
			{
				$nm .= ".html";
			}
			$num_reps = $this->_get_num_reps($settings["str"], $settings["s_opt"], $entry["content"]);
			if (!$this->can("view", $entry["site_id"]))
			{
				$so = obj();
			}
			else
			{
				$so = obj($entry["site_id"]);
			}
			$t->define_data(array(
				"loc" => $so->prop("short_name"),
				"link" => html::img(array(
					"url" => icons::get_icon_url(CL_FILE, $entry["url"]),
				)),
				"match" => $num_reps, //((int)(($entry["match"] / $max_match) * 100))."%",
				"title" => html::href(array(
					"url" => $entry["url"],
					"caption" => parse_obj_name($entry["title"]),
					"target" => "_blank"
				)),
				"mod" => $entry["modified"],
				"cont" => $this->_get_content_high($entry["content"], $settings["str"])
			));
		}
		$t->set_default_sortby("match");
		$t->set_default_sorder("desc");
		$t->pageselector_string = sprintf(t("Leiti %s dokumenti"), count($res));
		$t->sort_by();
		$arr["prop"]["value"] = $t->draw();
	}

	function _search_results_site($arr)
	{
		$this->read_template("site_results.tpl");

		$arr["request"]["s_date"] = array(
			"from" => $arr["request"]["date_from"],
			"to" => $arr["request"]["date_to"]
		);

		// get search results
		$settings = $this->set_defaults($arr["request"]);
		if (empty($settings["s_group"]))
		{
			$ol = new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_SEARCH_GRP")));
			$settings["s_group"] = $ol->ids();
		}
		$res = $this->get_multi_search_results($settings);
		if (count($res) == 0)
		{
			$arr["prop"]["value"] = $this->parse("NO_RESULTS");
			return ;
		}
		$from = $_GET["page"] * 20;
		$to = ($_GET["page"]+1) * 20;

		$sort_by = $_GET["sort_by"];
		if (!$sort_by)
		{
			$s_lut = array(
				S_ORD_TIME => "modified",
				S_ORD_TIME_ASC => "modified",
				S_ORD_MATCH => "num_reps",
				S_ORD_TITLE => "title",
				S_ORD_CONTENT => "title",
				S_ORD_POPULARITY => "pop"
			);
			$sort_by = $s_lut[$arr["obj_inst"]->prop("default_order")];
		}

		foreach($res as $idx => $entry)
		{
			$res[$idx]["num_reps"] = $this->_get_num_reps($settings["str"], $settings["s_opt"], $entry["content"]);
		}

		$this->__sort_by = $sort_by;
		usort($res, array($this, "__sby"));

		$num = -1;
		foreach($res as $entry)
		{
			$num++;
			if ($num >= $from && $num < $to)
			{
				// url, title, modified, content
				$nm = $entry["url"];
				$pi = pathinfo($nm);
				if ($pi["extension"] == "" || strlen($pi["extension"]) > 4)
				{
					$nm .= ".html";
				}
				$so = obj($entry["site_id"]);
				$loc = "<a href='javascript:void(0)' alt='".$so->name()."' title='".$so->name()."'>".$so->prop("short_name")."</a>";
				$this->vars(array(
					"icon" => $loc,
					"loc" => $loc,
					"link" => html::img(array(
						"url" => icons::get_icon_url(CL_FILE, $nm),
					)),
					"match" => $entry["num_reps"], //((int)(($entry["match"] / $max_match) * 100))."%",
					"title" => html::href(array(
						"url" => $entry["url"],
						"caption" => "<span class=\"sres\">".parse_obj_name($entry["title"])."</span>",
						"target" => "_blank",
					)),
					"mod" => $entry["modified"],
					"cont" => $this->_get_content_high($entry["content"], $settings["str"])
				));
				$str .= $this->parse("RESULT");
			}
		}

		$sts = array(
			"num_reps" => t("t&auml;psuse"),
			"modified" => t("kuup&auml;eva alusel"),
//			"title" => t("pealkirja alusel")
		);
		$sstr = array();
		foreach($sts as $var => $nm)
		{
			if ($sort_by == $var)
			{
				$sstr[] = $nm;
			}
			else
			{
				$sstr[] = html::href(array(
					"url" => aw_url_change_var("sort_by", $var),
					"caption" => $nm
				));
			}
		}

		$this->vars(array(
			"RESULT" => $str,
			"res_cnt" => count($res),
			"sort_by" => join(" | ", $sstr)
		));

		$this->display_pageselector(array(
			"num_results" => count($res),
			"cur_page" => $_GET["page"],
			"per_page" => 20,
			"params" => $_GET,
			"link_type" => 1,
		));

		// sorting links

		$arr["prop"]["value"] = $this->parse();
	}

	function __sby($a, $b)
	{
		$v1 = $a[$this->__sort_by];
		$v2 = $b[$this->__sort_by];

		if ($this->__sort_by === "title")
		{
			return $v1 == $v2 ? 0 : ($v1 > $v2 ? 1 : -1);
		}
		else
		{
			return $v1 == $v2 ? 0 : ($v1 > $v2 ? -1 : 1);
		}
	}

	function get_multi_search_results($arr)
	{
		if (empty($arr["str"]) && empty($arr["s_title"]))
		{
			return array();
		}

		$o = obj($arr["id"]);
		$fetch = array();
		$arr = $arr + array(
			"str" => "",
			"s_title" => "",
			"date" => "",
			"s_group" => "",
			"s_seatch_word_part" => "",
			"opts" => array()
		);

/*
/// FIXME! ajutiselt v2lja komm. sest ei tea mis on $statics
		foreach($o->connections_from(array("type" => "RELTYPE_SEARCH_GRP")) as $c)
		{
			$grp = $c->to();
			if (in_array($grp->class_id(), $statics))//FIXME: $statics defineerimata. mis see on?
			{
				$fetch[] = $grp;
			}
		}
*/

		if (empty($arr["s_limit"]) || ($o->prop("max_num_results") && $arr["s_limit"] > $o->prop("max_num_results")))
		{
			$arr["s_limit"] = $o->prop("max_num_results");
		}

		$arr["opts"]["str"] = $arr["s_opt"];
		$arr["opts"]["limit"] = $arr["s_limit"];

		$res = $this->fetch_static_search_results(array(
			"str" => $arr["str"],
			"opts" => $arr["opts"],
			"date" => $arr["date"],
			"s_title" => $arr["s_title"],
			"no_lang_id" => true,
			"site_id" => $arr["s_group"],
			"s_seatch_word_part" => $arr["s_seatch_word_part"]
		));

		return $res;
	}

	function _get_content_high($c, $str)
	{
		$c = preg_replace("/\s+/", " ", $c);
		// try to find complete string first, then any word
		if (($_pos = strpos(strtolower($c), strtolower($str))) !== false)
		{
			$str = substr($c, $_pos, strlen($str)); // get correct-case version
			return $this->_hgl($c, $str, $_pos);
		}

		// split by word and try for each
		$words = explode(" ", $str);
		$hl = "";
		foreach($words as $widx => $word)
		{
			if (($_pos = strpos(strtolower($c), strtolower($word))) !== false)
			{
				$str = substr($c, $_pos, strlen($word)); // get correct-case version
				$hl = $this->_hgl($c, $word, $_pos, $words);
				for(; $widx < count($words); $widx++)
				{
					$nw = $words[$widx];
					if (($_pos = strpos(strtolower($hl), strtolower($nw))) !== false)
					{
						$str = substr($hl, $_pos, strlen($nw)); // get correct-case version

						$hl = substr($hl, 0, $_pos)."<span class=\"match\">".$str."</span>".substr($hl, $_pos + strlen($nw));
					}
				}
			}
		}

		if ($hl != "")
		{
			return $hl;
		}
		$c_tmp = trim($c);
		$pos = strlen($c_tmp) > 200 ? strpos($c_tmp, " ", 200) : 0;
		if (!$pos)
		{
			return $c;
		}
		return substr(trim($c), 0, $pos);
	}

	function _hgl($c, $str, $_pos, $other = array())
	{
		// find first space 200 chars before
		$begin = $_pos-200;
		if ($begin > 0)
		{
			while($c{$begin} !== " " && $begin > 0)
			{
				$begin--;
			}
		}

		// find first space 200 chars after
		$end = $begin + 400 + strlen($str);

		if ($begin < 0)
		{
			$end += (-$begin);
			$begin = 0;
		}

		$clen = strlen($c);
		while ($end < $clen && $c{$end} != " ")
		{
			$end++;
		}

		// show
		$c =  substr($c, $begin, ($end - $begin));

		$c = str_replace($str, "<span class=\"match\">".$str."</span>", $c);

		if (count($other))
		{
			foreach($other as $word)
			{
				$c = str_replace($word, "<nb>".$word."</span>", $c);
			}
		}
		return $c; // 7 - strlen("<b></b>")
	}

	function _get_num_reps($str, $opt, $content)
	{
		$res = 0;
		switch($opt)
		{
			case S_OPT_ANY_WORD:
			case S_OPT_ALL_WORDS:
				$words = explode(" ", $str);
				$ct = strtolower($content);
				foreach($words as $word)
				{
					$res += substr_count($ct, strtolower($word));
				}
				return $res;

			case S_OPT_PHRASE:
			default:
				return substr_count(strtolower($content), strtolower($str));
		}
	}

	function callback_mod_tab($arr)
	{
		if ($arr["id"] === "search_complex")
		{
			$o = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_CPLX_EL_CTR");
			if (!$o)
			{
				return false;
			}
		}
		return true;
	}

	function callback_get_complex_els($arr)
	{
		$o = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_CPLX_EL_CTR");
		if (!$o)
		{
			return false;
		}

		$i = $o->instance();

		return $i->eval_controller($o->id(), $arr["obj_inst"]);
	}

	function _show_admin_if($arr)
	{
		$o = obj($arr["id"]);

		$arr["group"] = $_GET["group"];
		if ($arr["group"] == "")
		{
			$arr["group"] = "search_simple";
		}
		$props = $this->_get_aif_grp_props($o, $arr["group"]);

		$els = $this->_draw_aif_grp_props($o, $props, $arr["group"]);
		return $this->_draw_aif_tabs($o, $els, $arr["group"]);
	}

	function _get_aif_grp_props($o, $grp)
	{
		$props = array();
		$ap = $o->get_property_list();
		foreach($ap as $pn => $pd)
		{
			if ($pd["group"] == $grp)
			{
				$props[$pn] = $pd;
			}
		}
		return $props;
	}

	function _draw_aif_grp_props($o, $props, $group)
	{
		$rd = new site_search_content(); //FIXME: milleks iseenda instants???
		$rd->init_class_base();
		$rd->request = $_GET;
		$els = $rd->parse_properties(array(
			"properties" => $props,
			"name_prefix" => "",
			"obj_inst" => $o
		));

		$htmlc = new htmlclient();
		$htmlc->start_output(array(
			"handler" => "index"
		));
		foreach($els as $pn => $pd)
		{
			$htmlc->add_property($pd);
		}
		$htmlc->finish_output(array(
			"method" => "GET",
			"action" => "do_search_if",
			"data" => array(
				"orb_class" => "site_search_content",
				"id" => $o->id(),
				"section" => aw_global_get("section"),
				"group" => $group
			),
			"no_insert_reforb" => true
		));

		return $htmlc->get_result(array(
		));
	}

	function _draw_aif_tabs($o, $html, $grp)
	{
		$tp = new tabpanel();
		$tp->add_tab(array(
			"active" => $grp == "search_simple",
			"caption" => t("Lihtne otsing"),
			"link" => aw_ini_get("baseurl")."index".AW_FILE_EXT."?section=".aw_global_get("section")."&group=search_simple"
		));
		$ctr_o = $o->get_first_obj_by_reltype("RELTYPE_CPLX_EL_CTR");
		if ($ctr_o)
		{
			$tp->add_tab(array(
				"active" => $grp != "search_simple",
				"caption" => t("Detailotsing"),
				"link" => aw_ini_get("baseurl")."index".AW_FILE_EXT."?section=".aw_global_get("section")."&group=search_complex"
			));
		}
		return $tp->get_tabpanel(array(
			"content" => $html
		));
	}

	/** Adds a single aw object to the static search index
		@attrib api=1 params=name
		@param oid required type=oid
			the object to add to the index - currently only CL_FILE type objects are supported
	**/
	function add_single_object_to_index($arr)
	{
		if (!acl_base::can("view", $arr["oid"]))
		{
			return false;
		}
		$o = obj($arr["oid"]);
		if ($o->class_id() != CL_FILE)
		{
			return false;
		}
		$i = new site_search_content_grp_html();
		$i->add_single_url_to_index(file::get_url($o->id(), $o->name()));
	}
}
