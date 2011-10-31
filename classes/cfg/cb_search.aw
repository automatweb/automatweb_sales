<?php
/*
@classinfo relationmgr=yes no_status=1 no_comment=1

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property root_class type=select
@caption Juurklass

@property root_class_cf type=select
@caption Seadete vorm

@property root_class_ot type=select
@caption Objektit&uuml;&uuml;p

@property next_connection type=select
@caption Where do you want to go from here?

@property view_cf type=select
@caption Vaatamise seadete vorm

@property show_non_cf_fields type=checkbox ch_value=1
@caption N&auml;ita ka v&auml;lju, mis ei ole seadete vormis

@property max_results type=textbox
@caption Tulemuste arv

@groupinfo props caption="V&auml;ljad"

@property choose_fields type=table group=props no_caption=1
@caption Vali omadused

@groupinfo mktbl caption="Koosta tulemuste tabel"
@default group=mktbl

@property sform_tbl type=table store=no no_caption=1


@groupinfo parents caption="Kataloogid"

	@property parents type=table no_caption=1 group=parents
	@caption Kataloogid, ksut otsida

@groupinfo search caption="Otsi" submit_method=get

	@property search type=callback callback=callback_gen_search group=search
	@caption Otsi

	@property sbt type=submit group=search
	@caption Otsi

	@property results type=table group=search no_caption=1
	@caption Tulemused

@groupinfo submit_after caption="Tulemuste salvestamine"

	@property show_submit type=checkbox ch_value=1 group=submit_after
	@caption N&auml;ita salvesta nuppu

	@property submit_button_text type=textbox group=submit_after
	@caption Salvesta nupu tekst

	@property submit_handler_controller type=relpicker reltype=RELTYPE_SUBMIT_CTR group=submit_after
	@caption Salvestamise kontroller

@reltype SYN value=1 clid=CL_CB_SEARCH_SYNONYMS
@caption s&uuml;non&uuml;mid

@reltype PARENT value=2 clid=CL_MENU,CL_CRM_COMPANY
@caption kataloog, kust otsida

@reltype RESULT_CONTROLLER value=3 clid=CL_CFGCONTROLLER
@caption tulemuste kontroller

@reltype ROW_CONTROLLER value=4 clid=CL_CFGCONTROLLER
@caption rea kontroller

@reltype SUBMIT_CTR value=5 clid=CL_CFGCONTROLLER
@caption salvestamise kontroller

@reltype SEARCH_VALID_CTR value=6 clid=CL_CFGCONTROLLER
@caption otsingu valideerimise kontroller

@reltype ADDITIONAL_OBJECT_TYPE value=7 clid=CL_OBJECT_TYPE
@caption lisaks leitav objektit&uuml;&uuml;p

// step 1 - choose a class
// step 2 - choose a connection (might be optional)
// step 3 - choose another class (also optional)
*/

class cb_search extends class_base
{
	/** search content **/
	private $search_data = array();

	/** if search prep already done **/
	private $search_prepared = false;

	/** if prep already done **/
	private $prepared = false;

	/** table data **/
	private $__tdata;

	/** which els are in the result data **/
	private $in_results;

	/** which els are in the form**/
	private $in_form;

	function cb_search()
	{
		$this->init(array(
			"clid" => CL_CB_SEARCH,
			"tpldir" => "cfg/cb_search"
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		$o = $arr["obj_inst"];
		switch($prop["name"])
		{
			case "max_results":
				$prop["value"] = strlen($prop["value"])?$prop["value"]:500;
				break;
			case "root_class":
				$this->make_class_list(&$prop);
				break;

			case "root_class_cf":
			case "view_cf":
				if ($arr["obj_inst"]->prop("root_class"))
				{
					$ol = new object_list(array(
						"class_id" => CL_CFGFORM,
						"subclass" => $arr["obj_inst"]->prop("root_class"),
						"site_id" => array(),
					));
					$prop["options"] = array("" => "") + $ol->names();
				}
				break;

			case "root_class_ot":
				if ($arr["obj_inst"]->prop("root_class"))
				{
					$ol = new object_list(array(
						"class_id" => CL_OBJECT_TYPE,
						"subclass" => $arr["obj_inst"]->prop("root_class"),
						"site_id" => array(),
					));
					$prop["options"] = array("" => "") + $ol->names();
				}
				break;

			case "next_connection":
				return PROP_IGNORE; // just for now
				$cfgx = get_instance("cfg/cfgutils");
				$tmp = $cfgx->load_class_properties(array(
					"clid" => $o->prop("root_class"),
				));
				$relx = new aw_array($cfgx->get_relinfo());
				$choices = array("" => "");
				$clinf = aw_ini_get("classes");
				foreach($relx->get() as $relkey => $relval)
				{
					if (is_numeric($relkey))
					{
						$choices[$relkey] = $relval["caption"] . " - " . $clinf[$relval["clid"][0]]["name"];
					};
				};
				$prop["options"] = $choices;
				break;

			case "choose_fields":
				$this->mk_prop_table($arr);
				break;

			case "results":
				$this->mk_result_table($arr);
				break;

			case "sform_tbl":
				$this->do_sform_tbl_tbl($arr);
				break;

			case "parents":
				$this->do_parents_tbl($arr);
				break;
		};
		return $retval;
	}

	private function _init_prop_table($t)
	{
		$t->define_field(array(
			"name" => "classn",
			"caption" => t("Klass"),
		));
		$t->define_field(array(
			"name" => "property",
			"caption" => t("Omadus"),
		));
		$t->define_field(array(
			"name" => "in_form",
			"caption" => t("N&auml;ita vormis"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "search_mult",
			"caption" => t("Otsing komaga eraldatud"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "search_tb",
			"caption" => t("Tekstikast otsimiseks"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "type",
			"caption" => t("T&uuml;&uuml;p"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "default",
			"caption" => t("Vaikimisi"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "caption",
			"caption" => t("Tekst"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "ord",
			"caption" => t("J&auml;rjekord"),
			"align" => "center",
		));
	}

	private function mk_prop_table($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$o = $arr["obj_inst"];
		$this->_init_prop_table($t);
		$form_dat = $o->meta("form_dat");
		$clist = aw_ini_get("classes");
		list($props, $clid, $relinfo) = $this->get_props_from_obj($o);
		$cll = $clist[$clid]["name"];
		$opts = array(
			"select" => t("Rippmen&uuml;&uuml;"),
			"mselect" => t("Mitmerealine rippmen&uuml;&uuml;"),
			"checkboxes" => t("M&auml;rkeruut"),
			"radiobuttons" => t("Raadionupp"),
		);
		foreach($props as $pn => $item)
		{
			if (!is_array($form_dat[$pn]))
			{
				$form_dat[$pn]["caption"] = $item["caption"];
			}

			$default = html::textbox(array(
				"name" => "form_dat[$clid][$pn][default]",
				"value" => $form_dat[$clid][$pn]["default"],
				"size" => 5
			));
			if ($item["type"] == "classificator")
			{
				$tmp = array($item["name"] => &$item);
				$this->mod_chooser_prop($tmp, $pn, $clid, $o);
				$default = html::select(array(
					"name" => "form_dat[$clid][$pn][default]",
					"options" => array("" => "") + $item["options"],
					"selected" => $form_dat[$clid][$pn]["default"],
					"multiple" => $form_dat[$clid][$pn]["type"] == "checkboxes"
				));
			}

			$row = array(
				"classn" => $cll,
				"property" => $item["caption"]." (".$item["name"].")",
				"in_form" => html::checkbox(array(
					"name" => "form_dat[$clid][$pn][visible]",
					"value" => 1,
					"checked" => ($form_dat[$clid][$pn]["visible"] == 1)
				)),
				"search_mult" => html::checkbox(array(
					"name" => "form_dat[$clid][$pn][search_mult]",
					"value" => 1,
					"checked" => ($form_dat[$clid][$pn]["search_mult"] == 1)
				)),
				"search_tb" => html::checkbox(array(
					"name" => "form_dat[$clid][$pn][search_tb]",
					"value" => 1,
					"checked" => ($form_dat[$clid][$pn]["search_tb"] == 1)
				)),
				"caption" => html::textbox(array(
					"name" => "form_dat[$clid][$pn][caption]",
					"value" => $form_dat[$clid][$pn]["caption"]
				)),
				"ord" => html::textbox(array(
					"name" => "form_dat[$clid][$pn][jrk]",
					"size" => 5,
					"value" => $form_dat[$clid][$pn]["jrk"]
				)),
				"default" => $default
			);
			if($item["type"] == "classificator")
			{
				$row["type"] = html::select(array(
					"name" => "form_dat[$clid][$pn][type]",
					"options" => $opts,
					"value" => $form_dat[$clid][$pn]["type"],
				));
			}
			$t->define_data($row);
		};

		if ($o->prop("next_connection"))
		{
			$relin = $relx[$o->prop("next_connection")];
			$tgt = $relin["clid"][0];

			$tmp = $cfgx->load_class_properties(array(
				"clid" => $tgt,
			));
			$cl2 = $clinf[$tgt]["name"];

			foreach($tmp as $item)
			{
				if ($item["type"] == "textbox" || $item["type"] == "textarea")
				{
					$t->define_data(array(
						"class" => $cl2,
						"property" => $item["caption"] . " / " . $item["name"],
						"xname" => $tgt . "/" . $item["name"],
					));
				};
			};
		}
	}

	private function make_class_list($arr)
	{
		$cl = aw_ini_get("classes");
		$names = array();
		foreach($cl as $clid => $clinf)
		{
			if (!empty($clinf["name"]))
			{
				$names[$clid] = $clinf["name"];
			};
		};
		asort($names);
		$arr["options"] = array("0" => t("--vali--")) + $names;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		$o = $arr["obj_inst"];
		switch($prop["name"])
		{
			case "choose_fields":
				$o->set_meta("form_dat",$arr["request"]["form_dat"]);
				break;

			case "sform_tbl":
				$arr["obj_inst"]->set_meta("tdata", $arr["request"]["tdata"]);
				break;

			case "parents":
				$arr["obj_inst"]->set_meta("parents", $arr["request"]["data"]);
				break;
		}
		return $retval;
	}

	function callback_gen_search($arr)
	{
		// now, get a list of properties in both classes and generate the search form
		// get a list of properties in both classes
		$this->_prepare_form_data($arr);

		$this->_prepare_search($arr);

		$fd = $arr["obj_inst"]->meta("form_dat");

		// would be nice to separate things by blah
		$res = array();
		$vars = array(
			"checkboxes" => array(
				"type" => "chooser",
				"multiple" => 1,
			),
			"radiobuttons" => array(
				"type" => "chooser",
			),
			"mselect" => array(
				"type" => "select",
				"multiple" => 1,
			),
		);
		foreach($this->in_form as $iname => $item)
		{
			$name = $item["name"];
			$item["name"] = "s[" . $item["clid"] . "][" . $item["name"] . "]";
			if ($this->search_data[$item["clid"]][$name])
			{
				$item["value"] = $this->search_data[$item["clid"]][$name];
			}
			else
			{
				$def = $fd[$item["clid"]][$name]["default"];
				if (is_array($def))
				{
					$def = $this->make_keys($def);
				}
				$item["value"] = $def;
			}

			$item["ord"] = $fd[$item["clid"]][$iname]["jrk"];

			if ($item["type"] == "relpicker" || $item["type"] == "relmanager")
			{
				// get all conns from that class with that reltype
				$c = new connection();
				$conns = $c->find(array(
					"from.class_id" => $item["clid"],
					"type" => $item["reltype"]
				));

				$item["options"] = array("" => "");
				foreach($conns as $con)
				{
					$item["options"][$con["to"]] = $con["to.name"];
				}
			}

			if ($this->form_dat[$item["clid"]][$iname]["search_tb"] == 1)
			{
				$item["type"] = "textbox";
			}
			$res[$iname] = $item;

			if ($item["type"] == "classificator")
			{
				if(array_key_exists($fd[$item["clid"]][$iname]["type"], $vars))
				{
					$res[$iname] = $vars[$fd[$item["clid"]][$iname]["type"]] + $res[$iname];
				}
				else
				{
					$res[$iname]["type"] = "select";
				}
				$this->mod_chooser_prop($res, $iname, $item["clid"], $arr["obj_inst"]);
			}
		};

		uasort($res, create_function('$a,$b','if ($a["ord"] == $b["ord"] ) { return 0; } else { return $a["ord"] > $b["ord"] ? 1 : -1; }'));
		return $res;
	}

	private function mod_chooser_prop(&$props, $pn, $clid, $o)
	{
		// since storage can't do this yet, we gots to do sql here :(
		$p =& $props[$pn];
		$opts = array("" => "");
		if ($p["table"] != "" && $p["field"] != "")
		{
			$clsf = get_instance(CL_CLASSIFICATOR);
			$pr = array(
				"name" => $pn,
				"clid" => $clid
			);
			if (is_oid($o->prop("root_class_ot")))
			{
				$pr["object_type"] = $o->prop("root_class_ot");
			}
			$opts = $clsf->get_options_for($pr);
		}
		if($p["type"] == "select")
		{
			$p["options"] = array("" => "") + $opts;
		}
		else
		{
			$p["options"] = $opts;
		}
	}

	private function _prepare_search($arr)
	{
		if ($this->search_prepared)
		{
			return false;
		}
		$this->search_data = array();
		$this->search_prepared = 1;
		foreach($this->in_form as $iname => $item)
		{
			if (is_array($arr["request"]["s"]) && is_array($arr["request"]["s"][$item["clid"]]) &&
				$arr["request"]["s"][$item["clid"]][$item["name"]])
			{
				$val = $arr["request"]["s"][$item["clid"]][$item["name"]];
				$this->search_data[$item["clid"]][$item["name"]] = $val;
			};
		};

	}

	function __proptbl_srt($pa, $pb)
	{
		$a = $this->__tdata[$pa];
		$b = $this->__tdata[$pb];

		if ($a["jrk"] == $b["jrk"])
		{
			return 0;
		}
		return $a["jrk"] > $b["jrk"];
	}

	private function mk_result_table($arr)
	{
		$this->_prepare_form_data($arr);
		$this->_prepare_search($arr);
		$t = &$arr["prop"]["vcl_inst"];
		$ctrl = array();
		foreach($this->in_results as $iname => $item)
		{
			$dat = array(
				"name" => $iname,
				"caption" => $item["caption"],
			);
			if ($this->__tdata[$iname]["sortable"])
			{
				$dat["sortable"] = 1;
			}
			if ($iname == "del_link" || $iname == "change_link")
			{
				$dat["align"] = "center";
			}
			if($item["type"] == "date_select")
			{
				$dat = $dat + array(
					"type" => "time",
					"format" => "d-m-Y",
					"numeric" => 1,
				);
			}
			if(count($item["controllers"]) > 0)
			{
				$ctrl[$iname] = $item["controllers"];
			}

			$t->define_field($dat);
		}
		$add_f = array();
		$view_controller_inst = get_instance(CL_CFG_VIEW_CONTROLLER);
		if(count($ctrl) > 0)
		{
			foreach($ctrl as $key => $v)
			{
				foreach($v as $value)
				{
					$rval = null;
					$view_controller_inst->check_property(&$rval, $value, $this->search_data);
					if(is_array($rval))
					{
						$t->define_field($rval["field"]);
						$add_f[$rval["field"]["name"]] = $value;
					}
				}
			}
		}

		if ($this->__tdata["__defaultsort"])
		{
			$t->set_default_sortby($this->__tdata["__defaultsort"]);
			$t->set_default_sorder("asc");
		}
		else
		{
			$t->set_default_sortby("name");
			$t->set_default_sorder("asc");
		}

		list($f_props) = $this->get_props_from_obj($arr["obj_inst"]);

		$classfps = array();
		foreach($f_props as $f_pn => $f_pd)
		{
			if ($f_pd["type"] == "classificator" || $f_pd["type"] == "relpicker" || $f_pd["type"] == "relmanager")
			{
				$classfps[$f_pn] = $f_pn;
			}
		}
		$price_props = array();
		foreach(safe_array($this->__tdata) as $td_p => $td_d)
		{
			if ($td_d["is_price"])
			{
				$price_props[$td_p] = $td_p;
			}
		}

		$clss = aw_ini_get("classes");
		//$view_controller_inst = get_instance(CL_CFG_VIEW_CONTROLLER);
		$controller_inst = get_instance(CL_CFGCONTROLLER);
		// now do the actual bloody search
		foreach($this->search_data as $clid => $data)
		{
			if (!empty($data))
			{
				$sdata = array();
				$sdata["class_id"] = $clid;
				$sdata[] = new object_list_filter(array("non_filter_classes" => $clid));
				foreach($data as $key => $val)
				{
					if ($key == "per_page")
					{
						continue;
					}
					if ($key == "fts_search")
					{
						$t_cond = array();
						foreach($f_props as $t_pn => $t_pd)
						{
							if ($t_pn == "is_translated" || $t_pn == "needs_translation" || $t_pd["store"] == "no" || $t_pn == "fts_search" || $t_pn == "per_page")
							{
								continue;
							}

							$t_cond[$t_pn] = "%".$val."%";
						}

						$sdata[] = new object_list_filter(array(
							"logic" => "OR",
							"conditions" => $t_cond
						));
						continue;
					}

					if ($this->in_form[$key]["type"] == "classificator" || $this->in_form[$key]["type"] == "relpicker" || $this->in_form[$key]["type"] == "relmanager")
					{
						if ($this->form_dat[$clid][$key]["search_tb"] == 1)
						{
							$s_v = "";
							if ($this->form_dat[$clid][$key]["search_mult"])
							{
								$s_v = map('%s%%', explode(",", $val));
							}
							else
							{
								$s_v = "%".$val."%";
							}

							$sdata[$clss[$clid]["def"].".".$f_props[$key]["reltype"].".name"] = $s_v;
						}
						else
						{
							$sdata[$key] = $val;
						}
					}
					elseif($this->in_form[$key]["type"] == "date_select")
					{
						//$sdata[$key] = new object
					}
					elseif ($this->form_dat[$clid][$key]["search_mult"])
					{
						$sdata[$key] = map('%s%%', explode(",", $val));
					}
					else
					{
						$sdata[$key] = $val . "%";
					}
					if(count($this->in_form[$key]["controllers"]) > 0)
					{
						$this->in_form[$key]["sdata"] = &$sdata;
						foreach($this->in_form[$key]["controllers"] as $value)
						{
							$controller_inst->check_property($value, $args["id"], &$this->in_form[$key], $this->search_data, $val, $arr["obj_inst"]);
						}
					}
				};

				$this->proc_syns_in_sdata($arr["obj_inst"], $sdata);

				if ($GLOBALS["sortby"] != "")
				{
					$sp = $f_props[$GLOBALS["sortby"]];
					$this->quote(&$GLOBALS["sort_order"]);
					$sdata["sort_by"] = $sp["table"].".".$sp["field"]." ".$GLOBALS["sort_order"];
				}
				else
				if ($this->__tdata["__defaultsort"] != "")
				{
					$sp = $f_props[$this->__tdata["__defaultsort"]];
					if ($sp)
					{
						$sdata["sort_by"] = $sp["table"].".".$sp["field"]." ASC ";
					}
				}
				else
				{
					$sdata["sort_by"] = "objects.name ASC ";
				}

				// if there are any criteria for search from folder, add them to the filter
				$this->_add_parent_filter($arr["obj_inst"], $sdata);

				$sdata["limit"] = ($_t = $arr["obj_inst"]->prop("max_results"))?$_t:500;
				$sdata["join_strategy"] = "data";
				$sdata["site_id"] = array();

				$this->add_additional_object_types($arr["obj_inst"], $sdata);

				$olist_cnt = new object_list($sdata);

				if ($data["per_page"])
				{
					$sdata["limit"] = ($arr["request"]["ft_page"] * $data["per_page"]).",".$data["per_page"];
					$t->pageselector_string = $t->draw_text_pageselector(array(
						"d_row_cnt" => $olist_cnt->count(),
						"records_per_page" => $data["per_page"]
					));
				}
				$olist = new object_list($sdata);
				$res_data = $olist->arr();

				if (($c_o = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_RESULT_CONTROLLER")))
				{
					$controller_inst->check_property($c_o->id(), $arr["obj_inst"]->id(), $res_data, $arr["request"], $sdata, &$t);
				}

				if (($c_o = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_ROW_CONTROLLER")))
				{
					$add_f[] = $c_o->id();
				}
				foreach($res_data as $o)
				{
					$row = array();
					foreach($this->in_results as $iname => $item)
					{
						$row[$iname] = create_email_links($o->prop_str($iname));
						if ($f_props[$iname]["type"] == "textarea")
						{
							$row[$iname] = nl2br($row[$iname]);
						}
					}

					foreach($price_props as $p_pn)
					{
						if(is_numeric($row[$p_pn]) && !empty($row[$p_pn]))
						{
							$row[$p_pn] = number_format($row[$p_pn], 2);
						}
					}
					$vparms = array(
						"id" => $o->id(),
						"section" => aw_global_get("section")
					);
					if ($arr["obj_inst"]->prop("view_cf"))
					{
						$vparms["cfgform"] = $arr["obj_inst"]->prop("view_cf");
					}
					$row["view_link"] = html::href(array(
						"url" => $this->mk_my_orb("view", $vparms, $o->class_id()),
						"caption" => $this->in_results["view_link"]["caption"]
					));
					$row["change_link"] = html::href(array(
						"url" => $this->mk_my_orb("change", array("id" => $o->id()), $o->class_id()),
						"caption" => $this->in_results["change_link"]["caption"]
					));
					$row["del_link"] = html::href(array(
						"url" => $this->mk_my_orb("delete_obj", array("id" => $o->id(), "return_url" => get_ru())),
						"caption" => $this->in_results["del_link"]["caption"]
					));
					$row["oid"] = $o->id();
					foreach($add_f as $val)
					{
						$view_controller_inst->check_property(&$row, $val, $this->search_data);
					}

					$t->define_data($row);
				};
			};
		};

		$t->sort_by();
	}

	private function _prepare_form_data($arr)
	{
		if ($this->prepared)
		{
			return false;
		};
		$this->prepared = true;
		$o = $arr["obj_inst"];

		list($props, $clid, $relx) = $this->get_props_from_obj($o);

		$clinf = aw_ini_get("classes");
		$cl1 = $clinf[$clid]["name"];

		$this->form_dat = $o->meta("form_dat");
		$this->tdata = $o->meta("tdata");

		$this->in_form = array();
		$res = array();
		$controllers = array();
		$cf = $o->prop("root_class_cf");
		$prop_cfg = array();
		if(is_oid($cf) && $this->can("view", $cf))
		{
			$obj = obj($cf);
			$controllers = $obj->meta("controllers");
			$view_controllers = $obj->meta("view_controllers");
			$prop_cfg = safe_array($obj->meta("cfg_proplist"));
		}
		foreach(safe_array($this->form_dat[$clid]) as $pn => $pd)
		{
			if (empty($pd["visible"]))
			{
				continue;
			}

			$this->in_form[$pn] = $props[$pn];
			$this->in_form[$pn]["clid"] = $clid;
			if($controllers[$pn])
			{
				$this->in_form[$pn]["controllers"] = is_array($controllers[$pn]) ? $controllers[$pn] : array($controllers[$pn]);
			}
			$this->in_form[$pn] = safe_array($prop_cfg[$pn]) + $this->in_form[$pn];
			$this->in_form[$pn]["caption"] = $pd["caption"];
		};
		$this->__tdata = $o->meta("form_dat");
		uksort($this->in_form, array(&$this, "__proptbl_srt"));

		$this->in_results = array();
		foreach(safe_array($this->tdata) as $pn => $pd)
		{
			if (!$pd["visible"] || !is_array($pd))
			{
				continue;
			}
			$this->in_results[$pn] = $props[$pn];
			$this->in_results[$pn]["clid"] = $clid;
			$this->in_results[$pn]["caption"] = $pd["caption"];
			if($view_controllers[$pn])
			{
				$this->in_results[$pn]["controllers"] = is_array($view_controllers[$pn]) ? $view_controllers[$pn] : array($view_controllers[$pn]);
			}
		}

		$this->__tdata = $o->meta("tdata");
		uksort($this->in_results, array(&$this, "__proptbl_srt"));

		/*
		$relin = $relx[$o->prop("next_connection")];
		$tgt = $relin["clid"][0];

		$tmp = $cfgx->load_class_properties(array(
			"clid" => $tgt,
		));

		foreach($tmp as $iname => $item)
		{
			$xname = $tgt . "/" . $item["name"];
			if ($in_form[$xname])
			{
				$item["clid"] = $tgt;
				$this->in_form[$xname] = $item;
			};
			if ($in_results[$xname])
			{
				$this->in_results[$xname] = $item;
			};
		};


		*/
	}

	private function get_props_from_obj($o, $addt = true)
	{
		// get a list of properties in both classes
		$cfgx = get_instance("cfg/cfgutils");
		$ret = $cfgx->load_class_properties(array(
			"clid" => $o->prop("root_class"),
		));

		if ($o->prop("root_class_cf"))
		{
			if ($o->prop("root_class") == CL_DOCUMENT)
			{
				$class_i = get_instance("doc");
			}
			else
			{
				$class_i = get_instance($o->prop("root_class"));
			}
			$tmp = $class_i->load_from_storage(array(
				"id" => $o->prop("root_class_cf")
			));

			$dat = array();
			foreach($tmp as $pn => $pd)
			{
				$dat[$pn] = $ret[$pn];
				$dat[$pn]["caption"] = $pd["caption"];
			}
			$ret = $dat;

			if ($o->prop("show_non_cf_fields"))
			{
				$cu = get_instance("cfg/cfgutils");
				$ps = $cu->load_properties(array("clid" => $o->prop("root_class")));

				foreach($ps as $pn => $pd)
				{
					if (!isset($ret[$pn]))
					{
						$ret[$pn] = $pd;
					}
				}
			}
			else
			{
				$fd = safe_array($o->meta("form_dat"));
				foreach($fd as $clid => $inf)
				{
					foreach($inf as $pn => $propi)
					{
						if (!empty($propi["visible"]) and !isset($ret[$pn]))
						{
							$cu = get_instance("cfg/cfgutils");
							$ps = $cu->load_properties(array("clid" => $o->prop("root_class")));
							$ret[$pn] = isset($ps[$pn]) ? $ps[$pn] : null;
						}
					}
				}
			}
		}

		if ($addt)
		{
			$ret["fts_search"] = array(
				"type" => "textbox",
				"caption" => t("T&auml;istekstiotsing"),
				"name" => "fts_search"
			);

			$ret["parent"] = array(
				"type" => "folder_select",
				"caption" => t("Kataloog"),
				"name" => "parent"
			);

			$ret["per_page"] = array(
				"type" => "select",
				"caption" => t("Mitu kirjet lehel"),
				"name" => "per_page",
				"options" => array(
					10 => "10",
					25 => "25",
					50 => "50",
					100 => "100",
					250 => "250"
				)
			);
		}

		if ($ret["name"])
		{
			$ret["name"] = $ret["name"] + array(
				"type" => "textbox",
				"name" => "name",
				"table" => "objects",
				"field" => "name",
			);
		}

		return array($ret, $o->prop("root_class"), $cfgx->get_relinfo());
	}

	private function _init_sform_tbl_tbl(&$t)
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
			"name" => "is_price",
			"caption" => t("Hind"),
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
			"name" => "u_name",
			"caption" => t("Tulba pealkiri"),
			"sortable" => 1,
			"align" => "center"
		));
	}

	private function do_sform_tbl_tbl($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_sform_tbl_tbl($t);

		$tdata = $arr["obj_inst"]->meta("tdata");

		// get register
		list($props, $clid, $relinfo) = $this->get_props_from_obj($arr["obj_inst"], false);
		$max_jrk = 0;
		$props["change_link"]["caption"] = t("Muuda");
		$props["view_link"]["caption"] = t("Vaata");
		$props["del_link"]["caption"] = t("Kustuta");
		foreach($props as $pn => $pd)
		{
			if ($pn == "needs_translation" || $pn == "is_translated")
			{
				continue;
			}
			$defs = "";
			if ($tdata[$pn]["sortable"])
			{
				$defs = html::radiobutton(array(
					"name" => "tdata[__defaultsort]",
					"value" => $pn,
					"checked" => ($tdata["__defaultsort"] == $pn)
				));
			}
			$vc = "";
			if ($tdata[$pn]["visible"])
			{
				$vc = html::radiobutton(array(
					"name" => "tdata[__view_col]",
					"value" => $pn,
					"checked" => ($tdata["__view_col"] == $pn)
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
				"is_price" => html::checkbox(array(
					"name" => "tdata[$pn][is_price]",
					"value" => 1,
					"checked" => ($tdata[$pn]["is_price"] == 1)
				)),
				"defaultsort" => $defs,
				"view_col" => $vc,
				"u_name" => html::textbox(array(
					"name" => "tdata[$pn][caption]",
					"value" => ($tdata[$pn]["caption"] == "" ? $pd["caption"] : $tdata[$pn]["caption"])
				)),
			));
		}

		$t->set_default_sortby("jrk");
		$t->sort_by();
	}

	function show($arr)
	{
		aw_session_set("no_cache", 1);
		$ob = new object($arr["id"]);
		$request = array("s" => $GLOBALS["s"]);
		$this->dequote(&$request);
		if ($GLOBALS["search_butt"])
		{
			$request["search_butt"] = $GLOBALS["search_butt"];
		}
		if ($GLOBALS["ft_page"])
		{
			$request["ft_page"] = $GLOBALS["ft_page"];
		}
		list($props, $clid, $relinfo) = $this->get_props_from_obj($ob);

		$props = $this->callback_gen_search(array(
			"obj_inst" => $ob,
			"request" => $request
		));

		$htmlc = get_instance("cfg/htmlclient");
		$htmlc->start_output();
		foreach($props as $pn => $pd)
		{
			$pd2 = unserialize(serialize($pd));
			if ($pd["type"] == "relpicker")
			{
				$pd["type"] = "select";
			}
			$htmlc->add_property($pd);
		}
		$htmlc->add_property(array(
			"name" => "search",
			"caption" => t("Otsi"),
			"type" => "submit",
			"store" => "no"
		));
		$htmlc->finish_output();

		$html = $htmlc->get_result(array(
			"raw_output" => 1
		));

		$show_results = true;
		if (($sv_ctr = $ob->get_first_obj_by_reltype("RELTYPE_SEARCH_VALID_CTR")))
		{
			$ci = $sv_ctr->instance();
			if ($ci->check_property($sv_ctr->id(), $ob->id(), $request, $request, $request, $ob) == PROP_ERROR)
			{
				$show_results = false;
				$errmsg = $sv_ctr->prop("errmsg");
			}
		}

		if ($show_results)
		{

			$t = new aw_table(array(
				"layout" => "generic"
			));
			$this->mk_result_table(array(
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
			"table" => $table,
			"errmsg" => $errmsg
		));

		// if there is a submit handler controller, then show submit button with text
		if ($ob->prop("show_submit") /*&& $request["s"]*/)
		{
			$this->_do_submit($ob);
		}

		if ($show_results)
		{
			if ($_SESSION["cb_search_err"] != "")
			{
				$this->vars(array("errmsg" => $_SESSION["cb_search_err"]));
				$this->vars(array(
					"HAS_ERROR" => $this->parse("HAS_ERROR")
				));
				unset($_SESSION["cb_search_err"]);
			}
			$this->vars(array(
				"NO_ERROR" => $this->parse("NO_ERROR")
			));
		}
		else
		{
			$this->vars(array(
				"HAS_ERROR" => $this->parse("HAS_ERROR"),
				"SUBMIT_BUTTON" => ""
			));
		}

		return $this->parse();
	}

	/** Returns properties for the search form
		@attrib api=1 params=pos

		@param ob required type=cl_cb_search
			The search form object to draw form from

		@returns
			array { property_name => property_data, ... } for each property defined in the search form

	**/
	function get_callback_properties($ob)
	{
		$request = array();
		foreach (array("s", "search_butt", "ft_page") as $key)
		{
			if (!empty($GLOBALS[$key]))
			{
				$request["search_butt"] = $GLOBALS[$key];
			}
		}

		list($props, $clid, $relinfo) = $this->get_props_from_obj($ob);

		$tmp = $this->callback_gen_search(array(
			"obj_inst" => $ob,
			"request" => $request
		));

		return $tmp;
	}

	/** populates the current search result's table
		@attrib api=1 params=name

		@param ob required type=cl_cb_search
			The search object

		@param t required
			vcl/table class instance to populate with the search results

		@param request optional
			Array with request variables
	**/
	function get_search_result_table($arr)
	{
		$t =& $arr["t"];
		$this->mk_result_table(array(
			"prop" => array(
				"vcl_inst" => &$t
			),
			"obj_inst" => &$arr["ob"],
			"request" => $arr["request"],
		));
	}

	/** deletes the given object and returns to return_url

		@attrib name=delete_obj

		@param id required
		@param return_url required
	**/
	function delete_obj($arr)
	{
		$o = obj($arr["id"]);
		$o->delete();
		return $arr["return_url"];
	}

	/**

		sdata is list object list filter parameter
		get the synonyms from rels and insert or clauses

	**/
	private function proc_syns_in_sdata($o, &$sdata)
	{
		$scs = $o->connections_from(array(
			"type" => "RELTYPE_SYN"
		));
		foreach($scs as $c)
		{
			$iter = $sdata;
			$syno = $c->to();
			$syns = safe_array($syno->meta("syns"));
			foreach($iter as $k => $v)
			{
				if (is_object($v))
				{
					// FIXME: implement this for fts search
				}
				else
				if (is_array($v))
				{
					$tmp = array();
					foreach($v as $str)
					{
						$ps = $this->proc_perm_str($o, $str, $syns);
						foreach($ps as $p)
						{
							$tmp[] = $p;
						}
					}
					$v = $tmp;
				}
				else
				{
					$v = $this->proc_perm_str($o, $v, $syns);
				}

				$sdata[$k] = $v;
			}
		}
	}

	private function proc_perm_str($o, $v, $syns)
	{
		// string
		$has_pct = (strpos($v,"%") !== NULL ? true : false);
		$has_pct_first = $v{0} == "%";
		$v = str_replace("%", "", $v);

		$has_syn = false;
		$p_syns = array();
		$varr = array();
		foreach($syns as $synrow)
		{
			if ($synrow != "")
			{
				$synlist = explode(",", $synrow);
				$words = explode(" ",mb_strtolower($v, aw_global_get("charset")));

				foreach($synlist as $syn)
				{
					if (($pos = array_search(mb_strtolower($syn, aw_global_get("charset")), $words)) !== false)
					{
						$has_syn = true;
						$p_syns[$syn] = array("p" => $pos, "l" => $synlist);
						break;
					}
				}
			}
		}

		$res = array($v);
		// make permutations
		if ($has_syn)
		{
			// synonym lists are in $p_syns,
			// current words are in $words
			// must make all permutations of those
			$res = $this->req_do_perms($words, $p_syns);
		}

		if ($has_pct)
		{
			$tmp = array();
			foreach($res as $val)
			{
				if ($has_pct_first)
				{
					$tmp[] = "%".$val."%";
				}
				else
				{
					$tmp[] = $val."%";
				}
			}
			$res = $tmp;
		}
		return $res;
	}

	private function req_do_perms($words, $p_syns)
	{
		// for all syns, make all possibilities of that syn and add to an array
		$res = array($words);
		foreach($p_syns as $wd => $dat)
		{
			$others = $dat["l"];
			$pos = $dat["p"];

			foreach($others as $other)
			{
				foreach($res as $wordlist)
				{
					$tmp = array();
					foreach($wordlist as $idx => $word)
					{
						if ($idx == $pos)
						{
							$word = $other;
						}
						$tmp[] = $word;
					}
					$res[] = $tmp;
				}
			}
		}

		$tmp = array();
		foreach($res as $wl)
		{
			$tmp[] = join(" ", $wl);
		}

		return array_unique($tmp);
	}

	private function _init_parents_tbl(&$t)
	{
		$t->define_field(array(
			"name" => "id",
			"caption" => t("OID"),
			"sortable" => 1,
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "use",
			"caption" => t("Kasuta?"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "subs",
			"caption" => t("K.A. Alamkataloogid?"),
			"align" => "center"
		));
	}

	private function do_parents_tbl($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_parents_tbl($t);

		$data = $arr["obj_inst"]->meta("parents");

		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_PARENT")) as $c)
		{
			$o = $c->to();

			$t->define_data(array(
				"id" => $o->id(),
				"name" => $o->path_str(),
				"use" => html::checkbox(array(
					"name" => "data[".$o->id()."][use]",
					"value" => 1,
					"checked" => ($data[$o->id()]["use"] == 1)
				)),
				"subs" => html::checkbox(array(
					"name" => "data[".$o->id()."][subs]",
					"value" => 1,
					"checked" => ($data[$o->id()]["subs"] == 1)
				)),
			));
		}
		$t->set_default_sortby("name");
		$t->sort_by();
	}

	private function _add_parent_filter($o, &$sdata)
	{
		$pd = safe_array($o->meta("parents"));
		$pft = array();
		foreach($pd as $pid => $dat)
		{
			if (!$dat["use"])
			{
				continue;
			}

			$pft[$pid] = $pid;
			if ($dat["subs"])
			{
				$ot = new object_tree(array(
					"class_id" => CL_MENU,
					"parent" => $pid,
					"status" => array(STAT_NOTACTIVE, STAT_ACTIVE),
				));
				$pft = $pft + $this->make_keys($ot->ids());
			}
		}

		if (count($pft))
		{
			$sdata["parent"] = $pft;
		}
	}

	private function _do_submit($o)
	{
		$this->vars(array(
			"submit_text" => $o->prop("submit_button_text")
		));
		$this->vars(array(
			"SUBMIT_BUTTON" => $this->parse("SUBMIT_BUTTON"),
			"reforb" => $this->mk_reforb("handle_submit", array(
				"id" => $o->id(),
				"ret" => aw_url_change_var("ret", null, post_ru()),
				//"s" => $_REQUEST["s"]
			)),
			"reforb2" => $this->mk_reforb("handle_submit", array(
				"id" => $o->id(),
				"ret" => aw_url_change_var("ret", null, post_ru()),
				"second_form" => 1
			))
		));
	}

	/**
		@attrib name=handle_submit nologin="1"
	**/
	function handle_submit($arr)
	{
		$o = obj($arr["id"]);
		$c = get_instance(CL_CFGCONTROLLER);
		$rv = $c->check_property(
			$o->prop("submit_handler_controller"),
			$o,
			$o,
			$arr,
			array(),
			$o
		);
		return $rv;
	}

	private function add_additional_object_types($o, &$sdata)
	{
		$ots = $o->connections_from(array("type" => "RELTYPE_ADDITIONAL_OBJECT_TYPE"));
		if (count($ots))
		{
			if (!is_array($sdata["class_id"]))
			{
				$sdata["class_id"] = array($sdata["class_id"]);
			}

			foreach($ots as $ot_c)
			{
				$ot = $ot_c->to();
				$sdata["class_id"][] = $ot->prop("type");
			}
		}
	}
}
