<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/groupware/project_analysis_ws.aw,v 1.6 2009/02/07 17:10:34 robert Exp $
// project_analysis_ws.aw - Projekti anal&uuml;&uuml;si t&ouml;&ouml;laud 
/*

@classinfo syslog_type=ST_PROJECT_ANALYSIS_WS relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@default table=objects
@default group=general


@tableinfo aw_project_analysis_ws index=aw_oid master_index=brother_of master_table=objects

	@property eval_dl type=date_select table=aw_project_analysis_ws field=aw_eval_dl
	@caption Hindamise t&auml;htaeg

	@property eval_req type=checkbox ch_value=1 field=meta method=serialize
	@caption Hindamine kohustuslik

	@property sum_average type=checkbox ch_value=1 field=meta method=serialize
	@caption Summaks v&otilde;etakse keskmine

@default group=eval

	@property strat_a type=table no_caption=1 store=no

@default group=strat_res_avg

	@property strat_res_p_weight type=checkbox ch_value=1 store=no
	@caption Hindajate kaal

	@property strat_res_c_weight type=checkbox ch_value=1 store=no
	@caption Tulpade kaal

	@property strat_res type=table no_caption=1 store=no

@default group=strat_res_tree

	@layout srt_hb type=hbox width=20%:80%

		@layout srt_tree type=vbox closeable=1 area_caption=Hindajad parent=srt_hb
			@property srt type=treeview parent=srt_tree store=no no_caption=1

		@layout srt_table type=vbox parent=srt_hb
			@property srt_tbl type=table parent=srt_table store=no no_caption=1

@default group=strat_res_wt

	@property strat_wt_tb type=toolbar no_caption=1
	@property strat_wt type=table no_caption=1 store=no

@default group=cols_cols

	@property cols_tb type=toolbar no_caption=1 no_comment=1
	@property cols_table type=table no_caption=1 no_comment=1

@default group=cols_settings
	
	@property cols_s_table type=table no_caption=1 no_comment=1

@default group=rows

	@property rows_tb type=toolbar no_caption=1

	@property rows_top type=text subtitle=1 store=no

	@property rows_table type=table no_caption=1 no_comment=1

	@property rows_search type=text subtitle=1 store=no

	@property rows_search_what type=chooser
	@caption Lisa

	@property rows_search_str type=textbox
	@caption Otsingus&otilde;na

	@property rows_search_tbl type=table no_caption=1

@groupinfo strat_res_wt caption="Hindajad" 
@groupinfo cols caption="Tulbad" submit=no
@groupinfo cols_cols caption="Tulbad" submit=no parent=cols
@groupinfo cols_settings caption="Seaded" parent=cols
@groupinfo rows caption="Read"


@groupinfo eval caption="Hindamine"
@groupinfo strat_res caption="Hindamise tulemused"
	@groupinfo strat_res_tree caption="Hindajad" parent=strat_res submit=no
	@groupinfo strat_res_avg caption="Keskmised hinded" parent=strat_res


@reltype COL value=1 clid=CL_PROJECT_ANALYSIS_COL
@caption Tulp

@reltype ROW value=2 clid=CL_PROJECT_ANALYSIS_ROW
@caption Rida

@reltype PARTICIPANT value=3 clid=CL_CRM_PERSON
@caption Hindaja
*/

class project_analysis_ws extends class_base
{
	const AW_CLID = 1110;

	function project_analysis_ws()
	{
		$this->init(array(
			"tpldir" => "applications/groupware/project_analysis_ws",
			"clid" => CL_PROJECT_ANALYSIS_WS
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "strat_wt":
				$this->_strat_wt($arr);
				break;

			case "strat_wt_tb":
				$this->_strat_wt_tb($arr);
				break;

			case "cols_tb":
				$this->_cols_tb($arr);
				break;

			case "cols_table":
				$this->_cols_table($arr);
				break;

			case "rows_tb":
				$this->_rows_tb($arr);
				break;

			case "rows_table":
				$this->_rows_table($arr);
				break;

			case "strat_a":
				$this->_strat_a($arr);
				break;

			case "srt":
				$this->_srt($arr);
				break;

			case "srt_tbl":
				$this->_srt_tbl($arr);
				break;

			case "strat_res":
				$this->_strat_res($arr);
				break;

			case "strat_res_c_weight":
			case "strat_res_p_weight":
				$prop["value"] = 1;
				if(!$arr["request"][$prop["name"]] && $arr["request"]["strat_res_subm"])
				{
					$prop["value"] = 0;
				}
				break;

			case "rows_top":
				$prop["value"] = t("Read");
				break;

			case "rows_search":
				$prop["value"] = t("Ridade lisamine");
				break;

			case "rows_search_what":
				$prop["options"] = array(
					1 => t("Alamobjekte"),
					2 => t("Seostest"),
				);
				$prop["value"] = 1;
			case "rows_search_str":
				$prop["value"] = $arr["request"][$prop["name"]];
				break;

			case "rows_search_tbl":
				$this->_rows_search($arr);
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
			case "strat_wt":
				$this->_save_strat_wt($arr);
				break;

			case "strat_a":
				$retval = $this->_save_strat_a($arr);
				break;

			case "rows_table":
				$this->_save_rows_table($arr);
				break;

			case "rows_search_tbl":
				$this->_save_rows_search($arr);
				break;
		}
		return $retval;
	}	

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
		$arr["project"] = $_GET["project"];
		if($arr["group"] == "strat_res_wt")
		{
			$arr["add_p"] = 0;
		}
		if($arr["group"] == "rows")
		{
			$arr["add_r"] = 0;
		}
		if($arr["group"] == "strat_res_avg")
		{
			$arr["strat_res_subm"] = 1;
		}
		if($sw = $_GET["rows_search_what"])
		{
			$arr["rows_search_what"] = $sw;
		}
	}

	function callback_mod_retval($arr)
	{
		if($arr["request"]["strat_res_subm"])
		{
			$arr["args"]["strat_res_subm"] = $arr["request"]["strat_res_subm"];
			$arr["args"]["strat_res_c_weight"] = $arr["request"]["strat_res_c_weight"];
			$arr["args"]["strat_res_p_weight"] = $arr["request"]["strat_res_p_weight"];
		}
		if(aw_global_get("paws_rem_rows_search"))
		{
			aw_session_del("paws_rem_rows_search");
		}
		elseif($arr["request"]["group"] == "rows")
		{
			$arr["args"]["rows_search_what"] = $arr["request"]["rows_search_what"];
			$arr["args"]["rows_search_str"] = $arr["request"]["rows_search_str"];
		}
	}

	function callback_post_save($arr)
	{
		if($arr["new"] == 1 && is_oid($arr["request"]["project"]) && $this->can("view" , $arr["request"]["project"]))
		{
			$project = obj($arr["request"]["project"]);
			$project->connect(array("to" => $arr["id"], "reltype" => "ANALYSIS_WS"));
		}
	}

	function _strat_wt_tb($arr)
	{
		$tb = &$arr["prop"]["vcl_inst"];
		
		$tb->add_search_button(array(
			"pn" => "add_p",
			"multiple" => 1,
			"clid" => CL_CRM_PERSON,
		));
	}

	function _init_strat_wt_t(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Meeskonna liige"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "is",
			"caption" => t("Hindaja?"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "wt",
			"caption" => t("Hindaja hinde kaal protsentides"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "deadline",
			"caption" => t("Hindamise t&auml;htaeg"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "required",
			"caption" => t("Hindamine kohustuslik"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "remind",
			"caption" => t("Meeldetuletuse aeg (p&auml;eva)"),
			"align" => "center",
		));
	}

	function _strat_wt($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_strat_wt_t($t);

		$conns = $arr["obj_inst"]->connections_from(array("type" => "RELTYPE_PARTICIPANT"));
		// get project team
		$team = new object_list($conns);

		$wts = $arr["obj_inst"]->meta("wts");
		$evals = $arr["obj_inst"]->meta("evals");
		$ep = $arr["obj_inst"]->meta("eval_p");
		$def_d = $arr["obj_inst"]->prop("eval_dl");
		$def_r = $arr["obj_inst"]->prop("eval_req");
		foreach($team->arr() as $o)
		{
			if ($o->class_id() != CL_CRM_PERSON)
			{
				continue;
			}
			$t->define_data(array(
				"name" => html::obj_change_url($o->id()),
				"wt" => html::textbox(array(
					"name" => "wts[".$o->id()."]",
					"size" => 5,
					"value" => $wts[$o->id()]
				)),
				"is" => html::checkbox(array(
					"name" => "is[".$o->id()."]",
					"value" => 1,
					"checked" => $evals[$o->id()]
				)),
				"deadline" => html::date_select(array(
					"name" => "eval_p[".$o->id()."][deadline]",
					"value" => ($d = $ep[$o->id()]["deadline"]) ? $d : $def_d,
				)),
				"required" => html::checkbox(array(
					"name" => "eval_p[".$o->id()."][required]",
					"value" => 1,
					"checked" => (isset($ep[$o->id()])) ? $ep[$o->id()]["required"] : $def_r,
				)),
				"remind" => html::textbox(array(
					"name" => "eval_p[".$o->id()."][remind]",
					"value" => ($d = $ep[$o->id()]["remind"]) ? $d : 0,
					"size" => 3
				)),
			));
		}
	}

	function _save_strat_wt($arr)
	{
		$arr["obj_inst"]->set_meta("wts", $arr["request"]["wts"]);
		$arr["obj_inst"]->set_meta("evals", $arr["request"]["is"]);
		$arr["obj_inst"]->set_meta("eval_p", $arr["request"]["eval_p"]);
		if($ps = $arr["request"]["add_p"])
		{
			foreach(explode(",", $ps) as $p)
			{
				$arr["obj_inst"]->connect(array(
					"type" => "RELTYPE_PARTICIPANT",
					"to" => $p,
				));
			}
		}
		$sch = get_instance("core/scheduler");
		$pi = get_instance(CL_CRM_PERSON);
		foreach($arr["request"]["eval_p"] as $pid => $data)
		{
			$url = $this->mk_my_orb("send_notification_msg", array(
				"pid" => $pid,
				"id" => $arr["obj_inst"]->id(),
			));
			$sch->remove($url);
			if($arr["request"]["is"][$pid] && date_edit::get_timestamp($data["deadline"]) > 0 && $data["remind"] > 0 && $pi->has_user(obj($pid)))
			{
				$sch->add(array(
					"event" => $url,
					"time" => date_edit::get_timestamp($data["deadline"]) - $data["remind"] * 24 * 60 * 60,
				));
			}
		}
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_project_analysis_ws (aw_oid int primary key, aw_eval_dl int)");
			return true;
		}
	}

	function _cols_tb($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$t->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Tulp"),
			"url" => html::get_new_url(CL_PROJECT_ANALYSIS_COL, $arr["obj_inst"]->id(), array("return_url" => get_ru(), "alias_to" => $arr["obj_inst"]->id(), "reltype" => 1))
		));
		$t->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"action" => "del_goals",
			"tooltip" => t("Kustuta"),
		));
	}

	function _init_cols_table(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Tulba nimi"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "ord",
			"caption" => t("J&auml;rjekord"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "grp_name",
			"caption" => t("Grupi nimi"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "priority",
			"caption" => t("Prioriteet"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "weight",
			"caption" => t("Kaal"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "createdby",
			"caption" => t("Looja nimi"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "created",
			"caption" => t("Kuup&auml;ev"),
			"align" => "center",
			"sortable" => 1,
			"type" => "time",
			"format" => "d.m.Y H:i",
			"numeric" => 1
		));
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));
	}

	function _cols_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_cols_table($t);

		$u = new user();
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_COL")) as $c)
		{
			$st = $c->to();
			$p = $u->get_person_for_uid($st->createdby());
			$t->define_data(array(
				"name" => html::obj_change_url($c->to()),
				"createdby" => $p->name(),
				"created" => $st->created(),
				"ord" => $st->ord(),
				"oid" => $c->prop("to"),
				"grp_name" => $st->prop("group_name"),
				"priority" => $st->prop("priority"),
				"weight" => $st->prop("weight"),
				"ord" => $st->prop("ord")
			));
		}
	}

	function _init_cols_s_table(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "in_sum",
			"caption" => t("Arvestatakse summas"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "scale",
			"caption" => t("Hindamise skaala"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "type",
			"caption" => t("Hindamise t&uuml;&uuml;p"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "scale_names",
			"caption" => t("Hinnete nimed"),
			"align" => "center",
		));
	}

	function _get_cols_s_table($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$this->_init_cols_s_table($t);
	
		$data = $arr["obj_inst"]->meta("col_settings");

		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_COL")) as $c)
		{
			if(!strlen($data["scale_from"][$c->prop("to")]) || !$data["scale_to"][$c->prop("to")] || $data["scale_from"][$c->prop("to")] >= $data["scale_to"][$c->prop("to")])
			{
				$sn = t("Skaala on m&auml;&auml;ramata");
			}
			else
			{
				$scale_opts = array("" => t("--vali--"));
				for($i = $data["scale_from"][$c->prop("to")]; $i <= $data["scale_to"][$c->prop("to")]; $i++)
				{
					$scale_opts[$i] = $i;
				}
				if(count($scale_opts) < 103)
				{
					$sn = t("Lisa")."<br />".html::select(array(
						"name" => "add_label[".$c->prop("to")."][num]",
						"options" => $scale_opts,
					)).html::textbox(array(
						"name" => "add_label[".$c->prop("to")."][name]",
						"size" => 10,
					));
					if(count($data["scale_names"][$c->prop("to")]))
					{
						$sn .= "<br />".t("Kustuta")."<br />";
					}
					ksort($data["scale_names"][$c->prop("to")]);
					foreach($data["scale_names"][$c->prop("to")] as $num => $name)
					{
						$sn .= html::checkbox(array(
							"name" => "del_label[".$c->prop("to")."][".$num."]",
							"ch_value" => 1,
						)).$num.": ".$name."<br />";
					}
				}
				else
				{
					$sn = t("Skaala on liiga suur");
				}
			}
			$t->define_data(array(
				"name" => html::obj_change_url($c->to()),
				"in_sum" => html::checkbox(array(
					"checked" => $data["not_in_sum"][$c->prop("to")] ? 0 : 1,
					"ch_value" => 1,
					"name" => "col_settings[not_in_sum][".$c->prop("to")."]",
				)),
				"scale" => html::textbox(array(
					"name" => "col_settings[scale_from][".$c->prop("to")."]",
					"value" => $data["scale_from"][$c->prop("to")],
					"size" => 4,
				))." ".t("kuni")." ".html::textbox(array(
					"name" => "col_settings[scale_to][".$c->prop("to")."]",
					"value" => $data["scale_to"][$c->prop("to")],
					"size" => 4,
				)),
				"type" => html::select(array(
					"name" => "col_settings[type][".$c->prop("to")."]",
					"value" => $data["type"][$c->prop("to")],
					"options" => array(
						1 => t("Tekstikast"),
						2 => t("Valik"),
					),
				)),
				"scale_names" => $sn,
			));
		}
	}

	function _set_cols_s_table($arr)
	{
		$old_data = $arr["obj_inst"]->meta("col_settings");
		$not_in_sum = array();
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_COL")) as $c)
		{
			if(!$arr["request"]["col_settings"]["not_in_sum"][$c->prop("to")])
			{
				$not_in_sum[$c->prop("to")] = 1;
			}
			else
			{
				unset($not_in_sum[$c->prop("to")]);
			}
		}
		$data = $arr["request"]["col_settings"];
		$data["not_in_sum"] = $not_in_sum;
		$data["scale_names"] = $old_data["scale_names"];
		foreach($arr["request"]["add_label"] as $id => $dat)
		{
			if(strlen($dat["num"]) && $dat["name"])
			{
				$data["scale_names"][$id][$dat["num"]] = $dat["name"];
			}
		}
		foreach($arr["request"]["del_label"] as $id => $dat)
		{
			foreach($dat as $num => $tmp)
			{
				unset($data["scale_names"][$id][$num]);
			}
		}
		$arr["obj_inst"]->set_meta("col_settings", $data);
		$arr["obj_inst"]->save();
	}

	/**

		@attrib name=del_goals

	**/
	function del_goals($arr)
	{
		if (is_array($arr["sel"]) && count($arr["sel"]))
		{
			$ol = new object_list(array("oid" => $arr["sel"]));
			$ol->delete();
		}

		return $arr["post_ru"];
	}

	function _rows_tb($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$t->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Tulp"),
			"url" => html::get_new_url(CL_PROJECT_ANALYSIS_ROW, $arr["obj_inst"]->id(), array("return_url" => get_ru(), "alias_to" => $arr["obj_inst"]->id(), "reltype" => 2))
		));
		
		$t->add_search_button(array(
			"pn" => "add_r",
			"multiple" => 1,
			"clid" => array(),
		));

		$t->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"action" => "del_goals",
			"tooltip" => t("Kustuta"),
		));
	}

	function _init_rows_table(&$t)
	{
		$t->define_field(array(
			"name" => "show",
			"align" => "center",
			"caption" => t("Kasutusel"),
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Rea nimi"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "grp_name",
			"caption" => t("Rea kirjeldus"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "createdby",
			"caption" => t("Looja nimi"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "created",
			"caption" => t("Kuup&auml;ev"),
			"align" => "center",
			"sortable" => 1,
			"type" => "time",
			"format" => "d.m.Y H:i",
			"numeric" => 1
		));
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));
	}

	function _rows_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_rows_table($t);
		$no_use = $arr["obj_inst"]->meta("rows_no_use");
		$u = new user();
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_ROW")) as $c)
		{
			$st = $c->to();
			$p = $u->get_person_for_uid($st->createdby());
			$no = $st;
			if($o = $st->get_first_obj_by_reltype("RELTYPE_OBJECT"))
			{
				$no = $o;
			}
			$t->define_data(array(
				"show" => html::checkbox(array(
					"checked" => $no_use[$st->id()] ? 0 : 1,
					"ch_value" => 1,
					"name" => "show[".$st->id()."]",
				)),
				"name" => html::obj_change_url($st, $no->name()),
				"createdby" => $p->name(),
				"created" => $st->created(),
				"ord" => $st->ord(),
				"oid" => $c->prop("to"),
				"grp_name" => $st->comment()
			));
		}
	}

	function _save_rows_table($arr)
	{
		$no_use = $arr["obj_inst"]->meta("rows_no_use");
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_ROW")) as $c)
		{
			if(!$arr["request"]["show"][$c->prop("to")])
			{
				$no_use[$c->prop("to")] = 1;
			}
			else
			{
				unset($no_use[$c->prop("to")]);
			}
		}
		$arr["obj_inst"]->set_meta("rows_no_use", $no_use);
		$arr["obj_inst"]->save();

		if($oids = $arr["request"]["add_r"])
		{
			foreach(explode(",", $oids) as $oid)
			{
				$this->_add_row($oid, $arr);
			}
		}
	}

	function _add_row($oid, $arr)
	{
		$o = obj($oid);
		if($o->class_id() != CL_PROJECT_ANALYSIS_ROW)
		{
			$c = new connection();
			$chk = $c->find(array(
				"from.class_id" => CL_PROJECT_ANALYSIS_ROW,
				"to" => $oid,
				"type" => "RELTYPE_OBJECT",
			));
			if(!count($chk))
			{
				$o = obj();
				$o->set_class_id(CL_PROJECT_ANALYSIS_ROW);
				$o->set_parent($arr["obj_inst"]->id());
				$o->set_name(sprintf(t("%s rida"), $arr["obj_inst"]->name()));
				$o->save();
				$o->connect(array(
					"to" => $oid,
					"type" => "RELTYPE_OBJECT",
				));
			}
			else
			{
				$conn = reset($chk);
				$o = obj($conn["from"]);
			}
		}
		$arr["obj_inst"]->connect(array(
			"to" => $o->id(),
			"type" => "RELTYPE_ROW",
		));
	}

	function _init_rows_search($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "icon",
			"align" => "center",
			"caption" => t("T&uuml;&uuml;p"),
		));
		$t->define_field(array(
			"name" => "id",
			"align" => "center",
			"caption" => t("OID"),
		));
		$t->define_field(array(
			"name" => "name",
			"align" => "center",
			"caption" => t("Nimi"),
		));
		$t->define_field(array(
			"name" => "modifiedby",
			"align" => "center",
			"caption" => t("Muutja"),
		));
		$t->define_field(array(
			"name" => "modified",
			"align" => "center",
			"caption" => t("Muudeti"),
			"type" => "time",
			"format" => "d.m.Y, H:i",
		));
		if($arr["request"]["rows_search_what"] == 2)
		{
			$t->define_field(array(
				"name" => "reltypes",
				"align" => "center",
				"caption" => t("Seoset&uuml;&uuml;bid"),
			));
		}
		$t->define_chooser(array(
			"name" => "s_sel",
			"field" => "oid",
		));
	}

	function _rows_search($arr)
	{
		if(!$arr["request"]["rows_search_what"] || !$arr["request"]["rows_search_str"])
		{
			return;
		}
		$this->_init_rows_search(&$arr);
		$t = &$arr["prop"]["vcl_inst"];
		$ol = new object_list(array(
			"site_id" => array(),
			"lang_id" => array(),
			"name" => "%".$arr["request"]["rows_search_str"],
		));
		foreach($ol->arr() as $o)
		{
			$relinfo = $o->get_relinfo();
			$option = array(0 => t("--vali--"));
			$options = array();
			foreach($relinfo as $rt => $data)
			{
				$options[$data["value"]] = $data["caption"];
			}
			natsort($options);
			$options = $option + $options;
			$t->define_data(array(
				"icon" => html::img(array(
					"url" => icons::get_icon_url($o),
				)),
				"oid" => $o->id(),
				"name" => $o->name(),
				"modified" => $o->modified,
				"modifiedby" => $o->modifiedby(),
				"reltypes" => html::select(array(
					"name" => "reltype[".$o->id()."]",
					"options" => $options,
				)),
				"id" => $o->id()
			));
		}
	}

	function _save_rows_search($arr)
	{
		if($sw = $arr["request"]["rows_search_what"])
		{
			foreach($arr["request"]["s_sel"] as $oid)
			{
				if($sw == 1)
				{
					$ol = new object_list(array(
						"parent" => $oid,
						"site_id" => array(),
						"lang_id" => array(),
					));
					foreach($ol->arr() as $oid2 => $o)
					{
						$this->_add_row($oid2, $arr);
					}
				}
				elseif($type = $arr["request"]["reltype"][$oid])
				{
					$conn = obj($oid)->connections_from(array(
						"type" => $type,
					));
					foreach($conn as $c)
					{
						$this->_add_row($c->prop("to"), $arr);
					}
				}
				$set = 1;
			}
		}
		if($set)
		{
			aw_session_set("paws_rem_rows_search", 1);
		}
	}

	function _init_strat_a_tbl(&$t, $o)
	{
		$t->define_field(array(
			"name" => "task",
			"caption" => t(""),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "task_comm",
			"caption" => t(""),
			"align" => "center"
		));
		$grps = array();
		foreach($o->connections_from(array("type" => "RELTYPE_COL", "sort_by" => "to.jrk")) as $c)
		{
			$r = $c->to();
			$grp[$r->prop("group_name")] = $r->prop("group_name");
		}

		foreach($grp as $gn)
		{
			$t->define_field(array(
				"name" => $gn,
				"caption" => $gn,
				"align" => "center",
				"sortable" => 1,
				"numeric" => 1,
			));
		}		

		foreach($o->connections_from(array("type" => "RELTYPE_COL", "sort_by" => "to.jrk")) as $c)
		{
			$r = $c->to();
			$t->define_field(array(
				"name" => $c->prop("to"),
				"caption" => $c->prop("to.name"),
				"align" => "center",
				"sortable" => 1,
				"numeric" => 1,
				"parent" => $r->prop("group_name")
			));
		}
	}

	function _init_strat_a_tbl_r(&$t, $o)
	{
		$t->define_field(array(
			"name" => "task",
			"caption" => t(""),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "task_comm",
			"caption" => t(""),
			"align" => "center"
		));
		$grps = array();
		foreach($o->connections_from(array("type" => "RELTYPE_COL", "sort_by" => "to.jrk")) as $c)
		{
			$r = $c->to();
			$grp[$r->prop("group_name")] = $r->prop("group_name");
		}

		foreach($grp as $gn)
		{
			$t->define_field(array(
				"name" => $gn,
				"caption" => $gn,
				"align" => "center",
				"sortable" => 1,
				"numeric" => 1,
			));
		}		

		foreach($o->connections_from(array("type" => "RELTYPE_COL", "sort_by" => "to.jrk")) as $c)
		{
			$r = $c->to();
			$t->define_field(array(
				"name" => $c->prop("to"),
				"caption" => $c->prop("to.name"),
				"align" => "center",
				"sortable" => 1,
				"numeric" => 1,
				"parent" => $r->prop("group_name")
			));
		}

		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1
		));
	}

	function _strat_a($arr)
	{
		$evs = $arr["obj_inst"]->meta("evals");
		$cp = get_current_person();
		if (!isset($evs[$cp->id()]))
		{
			$arr["prop"]["type"] = "text";
			$arr["prop"]["value"] = t("Teie ei ole m&auml;&auml;ratud hindajaks!");
			return;
		}
		$t =& $arr["prop"]["vcl_inst"];

		$se = $this->_get_strat_eval($arr["obj_inst"]);
		$data = $se->meta("grid");
		$this->_init_strat_a_tbl($t, $arr["obj_inst"]);

		$strats = array();
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_COL", "sort_by" => "to.jrk")) as $c)
		{
			$strats[$c->prop("to")] = $c->prop("to");
		}
		
		$col_data = $arr["obj_inst"]->meta("col_settings");

		$no_use = $arr["obj_inst"]->meta("rows_no_use");
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_ROW", "sort_by" => "to.jrk")) as $c)
		{
			$row = $c->to();
			if($no_use[$row->id()])
			{
				continue;
			}
			$no = $row;
			if($o = $row->get_first_obj_by_reltype("RELTYPE_OBJECT"))
			{
				$no = $o;
			}
			$ar = array(
				"task" => html::obj_change_url($no),
				"task_comm" => $row->comment()
			);
			foreach($strats as $strat)
			{
				if($col_data["type"][$strat] == 2 && strlen($col_data["scale_from"][$strat]) && $col_data["scale_to"][$strat] && $col_data["scale_from"][$strat] < $col_data["scale_to"][$strat])
				{
					$opts = array("" => t("--vali--"));
					for($i = $col_data["scale_from"][$strat]; $i <= $col_data["scale_to"][$strat]; $i++)
					{
						$add = "";
						if($name = $col_data["scale_names"][$strat][$i])
						{
							$add = sprintf(" (%s)", $name);
						}
						$opts[$i] = $i.$add;
					}
					$val = html::select(array(
						"name" => "a[".$row->id()."][$strat]",
						"value" => $data[$row->id()][$strat],
						"options" => $opts,
					));
				}
				else
				{
					$val = html::textbox(array(
						"name" => "a[".$row->id()."][$strat]",
						"value" => $data[$row->id()][$strat],
						"size" => 3
					));
				}
				$ar[$strat] = $val;
			}
			$t->define_data($ar);
		}
		$t->set_sortable(false);
	}

	function _get_strat_eval($p)
	{
		$pp = get_current_person();
		$ol = new object_list(array(
			"class_id" => CL_PROJECT_ANALYSIS_ENTRY,
			"lang_id" => array(),
			"site_id" => array(),
			"proj" => $p->id(),
			"evaluator" => $pp->id()
		));
		if ($ol->count())
		{
			return $ol->begin();
		}
		else
		{
			$o = obj();
			$o->set_parent($p->id());
			$o->set_class_id(CL_PROJECT_ANALYSIS_ENTRY);
			$o->set_name(sprintf(t("Hinnang projektile %s"), $p->name()));
			$o->set_prop("proj", $p->id());
			$o->set_prop("evaluator" , $pp->id());
			$o->save();
			return $o;
		}
	}

	function _save_strat_a(&$arr)
	{
		$settings = $arr["obj_inst"]->meta("col_settings");
		$errors = array();
		foreach($arr["request"]["a"] as $row => $coldata)
		{
			foreach($coldata as $col => $data)
			{
				if($data && ($data > $settings["scale_to"][$col] || $data < $settings["scale_from"][$col]))
				{
					unset($arr["request"]["a"][$row][$col]);
					$error = sprintf(t("%s hindamise skaala on %s - %s"), obj($col)->name(), $settings["scale_from"][$col], $settings["scale_to"][$col]);
					$errors[$error] = $error;
				}
			}
		}
		// see if there is an eval for this person already, if not, create it , if it is, update it
		$se = $this->_get_strat_eval($arr["obj_inst"]);
		$se->set_meta("grid", $arr["request"]["a"]);
		$se->save();
		if(count($errors))
		{
			$arr["prop"]["error"] = implode("<br>", $errors);
			return PROP_ERROR;
		}
	}

	function _srt($arr)
	{
		$tv =& $arr["prop"]["vcl_inst"];
		// add all evaluators
		$ol = new object_list(array(
			"class_id" => CL_PROJECT_ANALYSIS_ENTRY,
			"lang_id" => array(),
			"site_id" => array(),
			"proj" => $arr["obj_inst"]->id(),
		));
		foreach($ol->arr() as $o)
		{
			$tv->add_item(0, array(
				"name" => $o->prop("evaluator.name"),
				"id" => $o->prop("evaluator"),
				"url" => aw_url_change_var("evalr", $o->prop("evaluator"))
			));
		}
	}

	function _srt_tbl($arr)
	{
		if (!$arr["request"]["evalr"])
		{
			return;
		}
		$t =& $arr["prop"]["vcl_inst"];

		$this->_init_strat_a_tbl_r($t, $arr["obj_inst"]);

		$strats = array();
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_COL")) as $c)
		{
			$strats[$c->prop("to")] = $c->prop("to");
		}

		// get all eval objs
		$ol = new object_list(array(
			"class_id" => CL_PROJECT_ANALYSIS_ENTRY,
			"lang_id" => array(),
			"site_id" => array(),
			"proj" => $arr["obj_inst"]->id(),
			"evaluator" => $arr["request"]["evalr"]
		));
		$data = array();
		foreach($ol->arr() as $o)
		{
			$g = safe_array($o->meta("grid"));
			foreach($g as $evid => $d)
			{
				foreach($d as $strat => $eval)
				{
					$so = obj($strat);
					$data[$evid][$strat] += ($eval * ($so->prop("weight") ? $so->prop("weight") : 1) * ($so->prop("priority") ? $so->prop("priority") : 1));
				}
			}
		}
		$no_use = $arr["obj_inst"]->meta("rows_no_use");
		$settings = $arr["obj_inst"]->meta("col_settings");
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_ROW")) as $c)
		{
			$row = $c->to();
			if($no_use[$row->id()])
			{
				continue;
			}
			$no = $row;
			if($o = $row->get_first_obj_by_reltype("RELTYPE_OBJECT"))
			{
				$no = $o;
			}
			$ar = array(
				"task" => html::obj_change_url($no),
				"task_comm" => $row->comment()
			);
			$sum = 0;
			$count = 0;
			foreach($strats as $strat)
			{
				$ar[$strat] = number_format($data[$row->id()][$strat] / $ol->count(), 2);
				if(!$settings["not_in_sum"][$strat])
				{
					$sum += $ar[$strat];
					$count++;
				}
			}
			if($arr["obj_inst"]->prop("sum_average"))
			{
				$sum /= $count;
			}
			$ar["sum"] = number_format($sum, 2);
			$t->define_data($ar);
		}
	}

	function _strat_res($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];

		$strats = array();
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_COL", "sort_by" => "to.jrk")) as $c)
		{
			$strats[$c->prop("to")] = $c->prop("to");
		}

		// get all eval objs
		$ol = new object_list(array(
			"class_id" => CL_PROJECT_ANALYSIS_ENTRY,
			"lang_id" => array(),
			"site_id" => array(),
			"proj" => $arr["obj_inst"]->id(),
		));
		$wts = $arr["obj_inst"]->meta("wts");
		$data = array();
		foreach($ol->arr() as $o)
		{
			$g = safe_array($o->meta("grid"));
			foreach($g as $evid => $d)
			{
				foreach($d as $strat => $eval)
				{
					$so = obj($strat);
					$wt = (!empty($wts[$o->prop("evaluator")]) && !$arr["request"]["strat_res_p_weight"]) ? $wts[$o->prop("evaluator")]/100.0 : 1;
					$cwt = ($so->prop("weight") && ($arr["request"]["strat_res_c_weight"] || !$arr["request"]["strat_res_subm"])) ? $so->prop("weight") : 1;
					$cpt = ($so->prop("priority") && ($arr["request"]["strat_res_c_weight"] || !$arr["request"]["strat_res_subm"])) ? $so->prop("priority") : 1;
					$data[$evid][$strat] += $eval * $wt * $cwt * $cpt;
				}
			}
		}

		$sbs = array();
		$sums = array();
		$no_use = $arr["obj_inst"]->meta("rows_no_use");
		$settings = $arr["obj_inst"]->meta("col_settings");
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_ROW", "sort_by" => "to.jrk")) as $c)
		{
			$row = $c->to();
			if($no_use[$row->id()])
			{
				continue;
			}
			$no = $row;
			if($o = $row->get_first_obj_by_reltype("RELTYPE_OBJECT"))
			{
				$no = $o;
			}
			$ar = array(
				"task" => html::obj_change_url($no),
				"task_comm" => $row->comment()
			);
			$sum = 0;
			$count = 0;
			foreach($strats as $strat)
			{
				$ar[$strat] = number_format($data[$row->id()][$strat] / $ol->count(), 2);
				$sbs[$strat] += $ar[$strat];
				$sums[$strat] += $ar[$strat];
				if(!$settings["not_in_sum"][$strat])
				{
					$sum += $ar[$strat];
					$count++;
				}
			}
			if($arr["obj_inst"]->prop("sum_average"))
			{
				$sum /= $count;
			}
			$ar["sum"] = number_format($sum, 2);
			$t->define_data($ar);
		}
		$this->_init_strat_res_tbl($t, $arr["obj_inst"], $sums);

		$sbs["task"] = t("<b>Summa</b>");
		//$t->define_data($sbs);
	}

	function _init_strat_res_tbl(&$t, $o, $sums)
	{
		$t->define_field(array(
			"name" => "task",
			"caption" => t(""),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "task_comm",
			"caption" => t(""),
			"align" => "center"
		));
		// add all strats to table
		//arsort($sums);
		foreach($sums as $strat_id => $sum)
		{
			$s = obj($strat_id);
			$t->define_field(array(
				"name" => $s->id(),
				"caption" => $s->name(),
				"align" => "center",
				"numeric" => 1,
				"sortable" => 1
			));
		}

		$t->define_field(array(
			"name" => "sum",
			"caption" => t("<b>Summa</b>"),
			"align" => "center",
			"numeric" => 1,
			"sortable" => 1
		));
	}

	/**
	@attrib name=send_notification_msg all_args=1
	**/
	function send_notification_msg($arr)
	{
		$url = $this->mk_my_orb("change", array(
			"id" => $arr["id"],
			"group" => "eval"
		), CL_PROJECT_ANALYSIS_WS, true);
		$msg = sprintf(t("Teil on kohustus anda oma hinnang anal&uuml;&uuml;sit&ouml;&ouml;laual. Hindamiseks klikake <a href=\"%s\">siia</a>."), $url);
		send_aw_message(array(
			"uid" => get_instance(CL_CRM_PERSON)->has_user(obj($arr["pid"]))->name(),
			"msg" => $msg,
			"url" => $url
		));
	}
}
?>
