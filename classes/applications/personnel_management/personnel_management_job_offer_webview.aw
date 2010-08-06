<?php
// personnel_management_job_offer_webview.aw - T&ouml;&ouml;pakkumised veebis
/*

@classinfo syslog_type=ST_PERSONNEL_MANAGEMENT_JOB_OFFER_WEBVIEW relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=instrumental

@default table=objects
@default group=general

@default field=meta
@default method=serialize

	@property org type=relpicker reltype=RELTYPE_ORG multiple=1 no_sel=1
	@caption Ettev&otilde;te

	@property secs type=relpicker reltype=RELTYPE_SECTION multiple=1 no_edit=1 no_sel=1
	@caption Osakonnad

	@property areas type=relpicker reltype=RELTYPE_AREA multiple=1 automatic=1 no_sel=1
	@caption Piirkonnad

	@property counties type=relpicker reltype=RELTYPE_COUNTY multiple=1 automatic=1 no_sel=1
	@caption Maakonnad

	@property cities type=relpicker reltype=RELTYPE_CITY multiple=1 automatic=1 no_sel=1
	@caption Linnad

@groupinfo display caption="N&auml;itamine"
@default group=display

	@property jo2link type=checkbox
	@caption T&ouml;&ouml;pakkumisel klikkides p&auml;&auml;seb selle detailvaatesse

	@property only_translated_jos type=checkbox
	@caption Kuva ainult aktiivse t&otilde;lkega t&ouml;&ouml;pakkumisi

	@property ord_tbl type=table
	@caption J&auml;rjestamisprintsiibid

	@property grp_rule type=select
	@caption Grupeerimine

	@property grp_ord_tbl type=table
	@caption Gruppide j&auml;rjestamisprintsiibid

	@property grp_rule_loc_area type=checkbox
	@caption Grupeeri piirkonna j&auml;rgi

	@property grp_rule_loc_county type=checkbox
	@caption Grupeeri maakonna j&auml;rgi

	@property grp_rule_loc_city type=checkbox
	@caption Grupeeri linna j&auml;rgi

	@property grp_rule_loc_lvls type=checkbox
	@caption Alumisel tasemel kuvatavat t&ouml;&ouml;pakkumist kuvatakse ka &uuml;lemise taseme grupis

	@property tutorial type=text store=no
	@caption Tutorial

@reltype ORG value=1 clid=CL_CRM_COMPANY
@caption Ettev&otilde;te

@reltype SECTION value=2 clid=CL_CRM_SECTION
@caption Osakond

@reltype COUNTY value=3 clid=CL_CRM_COUNTY
@caption Maakond

@reltype CITY value=4 clid=CL_CRM_CITY
@caption Linn

@reltype AREA value=5 clid=CL_CRM_AREA
@caption Piirkond

*/

class personnel_management_job_offer_webview extends class_base
{
	function personnel_management_job_offer_webview()
	{
		$this->init(array(
			"tpldir" => "applications/personnel_management/personnel_management_job_offer_webview",
			"clid" => CL_PERSONNEL_MANAGEMENT_JOB_OFFER_WEBVIEW
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "grp_rule":
				$prop["options"] = array(
					0 => t("--vali--"),
					1 => t("Asukoha j&auml;rgi"),
					2 => t("Organisatsiooni j&auml;rgi"),
					3 => t("&Uuml;ksuse j&auml;rgi"),
					4 => t("Organisatsiooni ja &uuml;ksuste j&auml;rgi")
				);
				break;

			case "grp_rule_loc_area":
			case "grp_rule_loc_county":
			case "grp_rule_loc_city":
				if($arr["obj_inst"]->grp_rule != 1)		// Asukoha j2rgi
				{
					return PROP_IGNORE;
				}
				break;
		}

		return $retval;
	}

	function _get_grp_ord_tbl($arr)
	{
		if(!$arr["obj_inst"]->grp_rule)
		{
			return PROP_INGORE;
		}

		$t = &$arr["prop"]["vcl_inst"];
		$t->set_sortable(false);
		$t->define_field(array(
			"name" => "priority",
			"caption" => t("Prioriteet"),
			"align" => "right"
		));
		$t->define_field(array(
			"name" => "property",
			"caption" => t("Omadus"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "order",
			"caption" => t("J&auml;rjestus"),
			"align" => "center"
		));
		$ops = array(
			"" => t("--vali--"),
			"name" => t("Grupi nimi"),
			"ord" => t("Grupi jrk"),
		);
		$mi = 0;
		foreach($arr["obj_inst"]->meta("grp_ord_tbl") as $i => $d)
		{
			$t->define_data(array(
				"priority" => $i,
				"property" => html::select(array(
					"name" => "grp_ord_tbl[".$i."][property]",
					"options" => $ops,
					"selected" => $d["property"],
				)),
				"order" => html::select(array(
					"name" => "grp_ord_tbl[".$i."][order]",
					"options" => array("ASC" => t("Kasvav"), "DESC" => t("Kahanev")),
					"selected" => $d["order"],
				)),
			));
			$mi = $i > $mi ? $i : $mi;
		}
		$mi++;
		$t->define_data(array(
			"priority" => $mi,
			"property" => html::select(array(
				"name" => "grp_ord_tbl[".$mi."][property]",
				"options" => $ops,
			)),
			"order" => html::select(array(
				"name" => "grp_ord_tbl[".$mi."][order]",
				"options" => array("" => t("--vali--"), "ASC" => t("Kasvav"), "DESC" => t("Kahanev")),
			)),
		));
		$t->sort_by(array("field" => "priority"));
	}

	function _get_ord_tbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->set_sortable(false);
		$t->define_field(array(
			"name" => "priority",
			"caption" => t("Prioriteet"),
			"align" => "right"
		));
		$t->define_field(array(
			"name" => "property",
			"caption" => t("Omadus"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "order",
			"caption" => t("J&auml;rjestus"),
			"align" => "center"
		));
		$ops = array(
			"" => t("--vali--"),
			"name" => t("T&ouml;&ouml;pakkumise nimi"),
			"profession.ord" => t("Ameti jrk"),
			"profession.name" => t("Ameti nimi"),
			"end" => t("T&auml;htaeg"),
		);
		$mi = 0;
		foreach($arr["obj_inst"]->meta("ord_tbl") as $i => $d)
		{
			$t->define_data(array(
				"priority" => $i,
				"property" => html::select(array(
					"name" => "ord_tbl[".$i."][property]",
					"options" => $ops,
					"selected" => $d["property"],
				)),
				"order" => html::select(array(
					"name" => "ord_tbl[".$i."][order]",
					"options" => array("ASC" => t("Kasvav"), "DESC" => t("Kahanev")),
					"selected" => $d["order"],
				)),
			));
			$mi = $i > $mi ? $i : $mi;
		}
		$mi++;
		$t->define_data(array(
			"priority" => $mi,
			"property" => html::select(array(
				"name" => "ord_tbl[".$mi."][property]",
				"options" => $ops,
			)),
			"order" => html::select(array(
				"name" => "ord_tbl[".$mi."][order]",
				"options" => array("" => t("--vali--"), "ASC" => t("Kasvav"), "DESC" => t("Kahanev")),
			)),
		));
		$t->sort_by(array("field" => "priority"));
	}

	function _get_secs($arr)
	{
		$ops = array();
		$dops = array();
		foreach($arr["obj_inst"]->org as $orgid)
		{
			$org = obj($orgid);
			$secs = get_instance(CL_CRM_COMPANY)->get_all_org_sections($org);
			if(count($secs) > 0)
			{
				// The company as the subheading for the sections.
				$ops[$orgid] = $org->name;
				$dops[] = $orgid;
				foreach($secs as $sec_id)
				{
					$sec = obj($sec_id);
					$ops[$sec_id] = $sec->name;
				}
			}
		}
		$arr["prop"]["options"] = $ops;
		$arr["prop"]["disabled_options"] = $dops;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "ord_tbl":
				foreach($arr["request"]["ord_tbl"] as $k => $v)
				{
					if(!$v["property"] || !$v["order"])
					{
						unset($arr["request"]["ord_tbl"][$k]);
					}
				}
				$arr["obj_inst"]->set_meta("ord_tbl", $arr["request"]["ord_tbl"]);
				break;

			case "grp_ord_tbl":
				foreach($arr["request"]["grp_ord_tbl"] as $k => $v)
				{
					if(!$v["property"] || !$v["order"])
					{
						unset($arr["request"]["grp_ord_tbl"][$k]);
					}
				}
				$arr["obj_inst"]->set_meta("grp_ord_tbl", $arr["request"]["grp_ord_tbl"]);
				break;
		}

		return $retval;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	/**
	@attrib name=show
	**/
	function show($arr)
	{
		$o = obj($arr["id"]);
		$this->skip_untrans = $o->prop("only_translated_jos");
//		$props = array_keys(get_instance(CL_CFGFORM)->get_cfg_proplist(get_instance(CL_CFGFORM)->get_sysdefault(array("clid" => CL_PERSONNEL_MANAGEMENT_JOB_OFFER))));
		$props = array_keys(get_instance(CL_CFGFORM)->get_default_proplist(array("clid" => CL_PERSONNEL_MANAGEMENT_JOB_OFFER)));
		$this->read_template("show.tpl");

		$this->vars(array(
			"captions.job_offer" => t("T&ouml;&ouml;pakkumine"),
			"captions.deadline" => t("T&auml;htaeg"),
		));

		$ol_prms = array(
			"class_id" => CL_PERSONNEL_MANAGEMENT_JOB_OFFER,
			"parent" => array(),
			"status" => object::STAT_ACTIVE,
			"confirmed" => 1,
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"endless" => 1,
					"end" => new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, time() - (24*3600 - 1)),
				),
			)),
			"start" => new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, time()),
		);

		if(is_array($o->org) && count($o->org) > 0)
		{
			// $ol_prms["CL_PERSONNEL_MANAGEMENT_JOB_OFFER.company"] = $o->org;
		}

		if(is_array($o->secs) && count($o->secs) > 0)
		{
			$ol_prms["CL_PERSONNEL_MANAGEMENT_JOB_OFFER.sect"] = $o->secs;
		}

		if(is_array($o->areas) && count($o->areas) > 0)
		{
			$ol_prms["CL_PERSONNEL_MANAGEMENT_JOB_OFFER.loc_area"] = $o->areas;
		}

		if(is_array($o->counties) && count($o->counties) > 0)
		{
			$ol_prms["CL_PERSONNEL_MANAGEMENT_JOB_OFFER.loc_county"] = $o->counties;
		}

		if(is_array($o->cities) && count($o->cities) > 0)
		{
			$ol_prms["CL_PERSONNEL_MANAGEMENT_JOB_OFFER.loc_city"] = $o->cities;
		}

		$ol = new object_list($ol_prms);
		$jos = $ol->arr();

		$this->ord_info = $this->order_ord_info($o->meta("ord_tbl"));
		$this->ordi = 0;
		uasort($jos, array($this, "decide_ord"));

		$this->ord_info = $this->order_ord_info($o->meta("grp_ord_tbl"));

		switch ($o->grp_rule)
		{
			case 1:
				$clids = array(
					"area" => CL_CRM_AREA,
					"county" => CL_CRM_COUNTY,
					"city" => CL_CRM_CITY,
				);
				$caps = array(
					"area" => t("Piirkonnad"),
					"county" => t("Maakonnad"),
					"city" => t("Linnad"),
				);
				$GROUP_LVL1 = "";
				$ol_prms = array(
					"parent" => array(),
					"status" => array(),
					"site_id" => array(),
					"lang_id" => array(),
				);
				$olf_conds = array();
				foreach($clids as $opr => $clid)
				{
					if(!$o->prop("grp_rule_loc_".$opr))
					{
						continue;
					}
					$conditions = array("class_id" => $clid);
					// area => areas, county => counties. Pft! -kaarel
					$prop_val = $o->prop(str_replace("y", "ie", $opr)."s");
					if(is_array($prop_val) && count($prop_val) > 0)
					{
						$conditions["oid"] = $prop_val;
					}
					$olf_conds[] = new object_list_filter(array(
						"logic" => "AND",
						"conditions" => $conditions,
					));
					$denied_by_clid[$clid] = $this->denied_jos(&$o, $opr, &$jos);
				}
				$ol_prms[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => $olf_conds,
				));
				if(count($ol_prms) > 4)
				{
					$ol = new object_list($ol_prms);
					$ol_arr = $ol->arr();

					$GROUP_LVL1 = "";
					uasort($ol_arr, array($this, "decide_ord"));

					foreach($ol_arr as $loc)
					{
						$denied = $denied_by_clid[$loc->class_id()];
						$loc_jos = $loc->get_job_offers(array())->ids();
						$JOB_OFFER = $this->job_offer($jos, $props, $loc_jos, $denied);
						if(empty($JOB_OFFER))
						{
							continue;
						}
						$this->vars(array(
							"JOB_OFFER" => $JOB_OFFER,
							"JOB_OFFERS.HEADER" => $this->job_offers_header($props),
						));
						$this->vars(array(
							"group.lvl2" => $loc->trans_get_val("name"),
							"JOB_OFFERS" => $this->parse("JOB_OFFERS"),
						));
						$GROUP_LVL2 .= $this->parse("GROUP.LVL2");
					}
					$this->vars(array(
						"group.lvl1" => $caps[$ops],
						"JOB_OFFERS" => "",
						"GROUP.LVL2" => $GROUP_LVL2,
					));
					$GROUP_LVL1 .= $this->parse("GROUP.LVL1");
				}
				$this->vars(array(
					"GROUP.LVL1" => $GROUP_LVL1,
				));
				break;

			case 2:
				$ol_prms = array(
					"class_id" => CL_CRM_COMPANY,
					"parent" => array(),
					"status" => array(),
					"site_id" => array(),
					"lang_id" => array(),
				);
				if(is_array($o->org) && count($o->org) > 0)
				{
					$ol_prms["oid"] = $o->org;
				}
				$ol = new object_list($ol_prms);
				$ol_arr = $ol->arr();

				$GROUP_LVL1 = "";
				uasort($ol_arr, array($this, "decide_ord"));

				foreach($ol_arr as $comp)
				{
					$org_jos = $comp->get_job_offers()->ids();
					$JOB_OFFER = $this->job_offer(&$jos, &$props, &$org_jos);
					if(empty($JOB_OFFER))
					{
						continue;
					}
					$this->vars(array(
						"JOB_OFFER" => $JOB_OFFER,
						"JOB_OFFERS.HEADER" => $this->job_offers_header($props),
					));
					$this->vars(array(
						"group.lvl1" => $comp->trans_get_val("name"),
						"JOB_OFFERS" => $this->parse("JOB_OFFERS"),
					));
					$GROUP_LVL1 .= $this->parse("GROUP.LVL1");
				}
				$this->vars(array(
					"GROUP.LVL1" => $GROUP_LVL1,
				));
				break;

			case 3:
				$ol_prms = array(
					"class_id" => CL_CRM_SECTION,
					"parent" => array(),
					"status" => array(),
					"site_id" => array(),
					"lang_id" => array(),
				);
				if(is_array($o->secs) && count($o->secs) > 0)
				{
					$ol_prms["oid"] = $o->secs;
				}
				$ol = new object_list($ol_prms);
				$ol_arr = $ol->arr();

				$GROUP_LVL1 = "";
				uasort($ol_arr, array($this, "decide_ord"));

				foreach($ol_arr as $sec)
				{
					$sec_jos = $sec->get_job_offers()->ids();
					$JOB_OFFER = $this->job_offer(&$jos, &$props, &$sec_jos);
					if(empty($JOB_OFFER))
					{
						continue;
					}
					$this->vars(array(
						"JOB_OFFER" => $JOB_OFFER,
						"JOB_OFFERS.HEADER" => $this->job_offers_header($props),
					));
					$this->vars(array(
						"group.lvl1" => $sec->trans_get_val("name"),
						"JOB_OFFERS" => $this->parse("JOB_OFFERS"),
					));
					$GROUP_LVL1 .= $this->parse("GROUP.LVL1");
				}
				$this->vars(array(
					"GROUP.LVL1" => $GROUP_LVL1,
				));
				break;

			case 4:
				$ol_prms = array(
					"class_id" => CL_CRM_COMPANY,
					"parent" => array(),
					"status" => array(),
					"site_id" => array(),
					"lang_id" => array(),
				);
				if(is_array($o->org) && count($o->org) > 0)
				{
					$ol_prms["oid"] = $o->org;
				}
				$ol = new object_list($ol_prms);
				$ol_arr = $ol->arr();

				$GROUP_LVL1 = "";
				uasort($ol_arr, array($this, "decide_ord"));

				foreach($ol_arr as $comp)
				{
					$secs = get_instance(CL_CRM_COMPANY)->get_all_org_sections($comp);
					$GROUP_LVL2 = "";
					foreach($secs as $sec_id)
					{
						$sec = obj($sec_id);
						$sec_jos = $sec->get_job_offers()->ids();
						$JOB_OFFER = $this->job_offer(&$jos, &$props, &$sec_jos);
						if(empty($JOB_OFFER))
						{
							continue;
						}
						$this->vars(array(
							"JOB_OFFER" => $JOB_OFFER,
							"JOB_OFFERS.HEADER" => $this->job_offers_header($props),
						));
						$this->vars(array(
							"group.lvl2" => $sec->trans_get_val("name"),
							"JOB_OFFERS" => $this->parse("JOB_OFFERS"),
						));
						$GROUP_LVL2 .= $this->parse("GROUP.LVL2");
					}
					$this->vars(array(
						"group.lvl2" => $comp->trans_get_val("name"),
						"GROUP.LVL2" => $GROUP_LVL2,
						"JOB_OFFERS" => "",
					));
					$GROUP_LVL1 .= $this->parse("GROUP.LVL1");
				}
				$this->vars(array(
					"GROUP.LVL1" => $GROUP_LVL1,
				));
				break;

			default:
				$this->vars(array(
					"JOB_OFFERS.HEADER" => $this->job_offers_header(&$props),
					"JOB_OFFER" => $this->job_offer(&$jos, &$props),
				));
				$this->vars(array(
					"JOB_OFFERS" => $this->parse("JOB_OFFERS"),
				));
				$this->vars(array(
					"GROUP.LVL1" => $this->parse("GROUP.LVL1"),
				));
				break;
		}

		return $this->parse();
	}

	private function job_offers_header($props)
	{
		foreach($props as $prop)
		{
			$this->vars(array(
				"JOB_OFFERS.HEADER.".strtoupper($prop) => $this->parse("JOB_OFFERS.HEADER.".strtoupper($prop)),
			));
		}
		return $this->parse("JOB_OFFERS.HEADER");
	}

	private function denied_jos($o, $lt, $jos)
	{
		if($o->grp_rule_loc_lvls || $lt == "city")
		{
			return array();
		}
		if($lt == "area")
		{
			$d = array();
			foreach($jos as $jo)
			{
				if(is_oid($jo->loc_city) && $o->grp_rule_loc_city || is_oid($jo->loc_county) && $o->grp_rule_loc_county)
				{
					$d[] = $jo->id();
				}
			}
			return $d;
		}
		else
		if($lt == "county")
		{
			$d = array();
			foreach($jos as $jo)
			{
				if(is_oid($jo->loc_city) && $o->grp_rule_loc_city)
				{
					$d[] = $jo->id();
				}
			}
			return $d;
		}
	}

	private function job_offer(&$jos, &$props, &$allowed = NULL, &$denied = array())
	{
		$JOB_OFFER = "";
		foreach($jos as $jo)
		{
			if(in_array($jo->id(), $denied) || !in_array($jo->id(), $allowed) && is_array($allowed))
			{
				continue;
			}
			// Maybe I don't wanna see untranslated job offers!??!
			$langid = aw_ini_get("user_interface.full_content_trans") ? aw_global_get("ct_lang_id") : aw_global_get("lang_id");
			if($this->skip_untrans && !$jo->meta("trans_".$langid."_status") && $jo->lang_id() != $langid)
			{
				continue;
			}
			$this->vars(array(
				"job_offer.href" => obj_link($jo->id()),
			));
			foreach($props as $prop)
			{
				$this->vars(array(
					"job_offer.".$prop => $this->proc_prop(&$prop, &$jo),
				));
				$this->vars(array(
					"JOB_OFFER.".strtoupper($prop) => $this->parse("JOB_OFFER.".strtoupper($prop)),
				));
			}
			$JOB_OFFER .= $this->parse("JOB_OFFER");
		}
		return $JOB_OFFER;
	}

	private function proc_prop($p, $o)
	{
		switch($p)
		{
			case "start":
				return get_lc_date($o->prop($p));

			case "end":
				return $o->get_end();

			case "company":
			case "loc_country":
			case "loc_area":
			case "loc_county":
			case "loc_city":
			case "jo_type":
			case "profession":
			case "sect":
				return htmlentities($o->trans_get_val($p.".name"));

			default:
				return htmlentities($o->trans_get_val($p));
		}
	}

	private function order_ord_info($arr)
	{
		$r = array();
		foreach($arr as $pra => $a)
		{
			$i = 0;
			foreach($arr as $prb => $b)
			{
				if($pra > $prb)
				{
					$i++;
				}
			}
			$r[$i] = $a;
		}
		return $r;
	}

	private function decide_ord($o, $o2)
	{
		if($this->ordi > count($this->ord_info))
		{
			$this->ordi = 0;
			return 0;
		}
		$ord_info = $this->ord_info[$this->ordi];

		$ord_decided = false;
		if($ord_info["property"] == "ord")
		{
			$ord_decided = $o->ord() != $o2->ord();
			if(($o->ord() > $o2->ord() && $ord_info["order"] == "ASC") || ($o->ord() < $o2->ord() && $ord_info["order"] == "DESC"))
			{
				$this->ordi = 0;
				return 1;
			}
		}
		else
		if(strlen($ord_info["property"]) > 4 && substr($ord_info["property"], strlen($ord_info["property"]) - 4, 4) == ".ord")
		{
			$o_ = obj($o->prop(substr($ord_info["property"], 0, strlen($ord_info["property"]) - 4)));
			$o2_ = obj($o2->prop(substr($ord_info["property"], 0, strlen($ord_info["property"]) - 4)));

			$ord_decided = $o_->ord() != $o2_->ord();
			if(($o_->ord() > $o2_->ord() && $ord_info["order"] == "ASC") || ($o_->ord() < $o2_->ord() && $ord_info["order"] == "DESC"))
			{
				$this->ordi = 0;
				return 1;
			}
		}
		else
		if(preg_match("/^[-]?([0-9]*\.[0-9]+|[0-9]+)$/", $o->prop($ord_info["property"])) && preg_match("/^[-]?([0-9]*\.[0-9]+|[0-9]+)$/", $o2->prop($ord_info["property"])))
		{
			$ord_decided = $o->prop($ord_info["property"]) != $o2->prop($ord_info["property"]);
			if(($o->prop($ord_info["property"]) > $o2->prop($ord_info["property"]) && $ord_info["order"] == "ASC") || ($o->prop($ord_info["property"]) < $o2->prop($ord_info["property"]) && $ord_info["order"] == "DESC"))
			{
				$this->ordi = 0;
				return 1;
			}
		}
		else
		if((strcasecmp($o->prop($ord_info["property"]), $o2->prop($ord_info["property"])) > 0 && $ord_info["order"] == "ASC") || (strcasecmp($o->prop($ord_info["property"]), $o2->prop($ord_info["property"])) < 0 && $ord_info["order"] == "DESC"))
		{
			$this->ordi = 0;
			return 1;
		}
		if(strcasecmp($o->prop($ord_info["property"]), $o2->prop($ord_info["property"])) == 0 && !$ord_decided)
		{
			$this->ordi++;
			return $this->decide_ord($o, $o2);
		}
	}

	function _get_tutorial($arr)
	{
		$arr["prop"]["value"] = nl2br("Templeiti v&otilde;ib panna suvalise personnel_management_job_offer property. Selleks peab olema {VAR:job_offer.[property_name]}
		Selle v&otilde;ib paigutada &lt;!-- SUB:JOB_OFFER.[PROPERTY_NAME] --&gt; sisse.
		N&auml;iteks:
		&lt;!-- SUB: JOB_OFFER.WEOFFER --&gt;
		{VAR:job_offer.weoffer}
		&lt;!-- END SUB: JOB_OFFER.WEOFFER --&gt;");
	}
}

?>
