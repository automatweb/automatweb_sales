<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/groupware/project_strat_goal_eval_ws.aw,v 1.4 2007/12/06 14:33:32 kristo Exp $
// project_strat_goal_eval_ws.aw - Projekti eesm&auml;rkide hindamislaud 
/*

@classinfo syslog_type=ST_PROJECT_STRAT_GOAL_EVAL_WS relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop
@tableinfo aw_project_strat_goal_eval_ws index=aw_oid master_index=brother_of master_table=objects

@default table=objects
@default group=general

	@property eval_dl type=date_select table=aw_project_strat_goal_eval_ws field=aw_eval_dl
	@caption Hindamise t&auml;htaeg

@default group=eval

	@property strat_a type=table no_caption=1 store=no

@default group=strat_res_avg

	@property strat_res type=table no_caption=1 store=no

@default group=strat_res_tree

	@layout srt_hb type=hbox 

		@property srt type=treeview parent=srt_hb store=no no_caption=1
		@property srt_tbl type=table parent=srt_hb store=no no_caption=1

@default group=strat_res_wt

	@property strat_wt type=table no_caption=1 store=no

@groupinfo strat_res_wt caption="Hindajad" 

@groupinfo eval caption="Hindamine"
@groupinfo strat_res caption="Hindamise tulemused"
	@groupinfo strat_res_tree caption="Hindajad" parent=strat_res submit=no
	@groupinfo strat_res_avg caption="Keskmised hinded" parent=strat_res submit=no
*/

class project_strat_goal_eval_ws extends class_base
{
	const AW_CLID = 1088;

	function project_strat_goal_eval_ws()
	{
		$this->init(array(
			"tpldir" => "applications/groupware/project_strat_goal_eval_ws",
			"clid" => CL_PROJECT_STRAT_GOAL_EVAL_WS
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "strat_a":
				$this->_strat_a($arr);
				break;

			case "strat_res":
				$this->_strat_res($arr);
				break;

			case "srt":
				$this->_srt($arr);
				break;

			case "srt_tbl":
				$this->_srt_tbl($arr);
				break;

			case "strat_wt":
				$this->_strat_wt($arr);
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
			case "strat_a":
				$this->_save_strat_a($arr);
				break;

			case "strat_wt":
				$this->_save_strat_wt($arr);
				break;
		}
		return $retval;
	}	

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
		$arr["project"] = $_GET["project"];
	}


	function callback_post_save($arr)
	{
		if($arr["new"] == 1 && is_oid($arr["request"]["project"]) && $this->can("view" , $arr["request"]["project"]))
		{
			$project = obj($arr["request"]["project"]);
			$project->connect(array("to" => $arr["id"], "reltype" => "STRAT_EVAL"));
		}
	}


	function _init_strat_a_tbl(&$t, $o)
	{
		$t->define_field(array(
			"name" => "task",
			"caption" => t("Toimetus"),
			"align" => "right"
		));
		// add all strats to table
		foreach($o->connections_from(array("type" => "RELTYPE_STRAT")) as $c)
		{
			$t->define_field(array(
				"name" => $c->prop("to"),
				"caption" => $c->prop("to.name"),
				"align" => "center",
				"sortable" => 1,
				"numeric" => 1
			));
		}
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

		$pi = get_instance(CL_PROJECT);
		$conns = $arr["obj_inst"]->connections_to(array("from.class_id" => CL_PROJECT));
		$c = reset($conns);
		$proj  = $c->from();
		$se = $this->_get_strat_eval($arr["obj_inst"]);
		$data = $se->meta("grid");
		$this->_init_strat_a_tbl($t, $proj);

		$strats = array();
		foreach($proj->connections_from(array("type" => "RELTYPE_STRAT")) as $c)
		{
			$strats[$c->prop("to")] = $c->prop("to");
		}

		foreach($pi->get_events_for_project(array("project_id" => $proj->id())) as $evid)
		{
			$ar = array(
				"task" => html::obj_change_url($evid),
			);
			foreach($strats as $strat)
			{
				$ar[$strat] = html::textbox(array(
					"name" => "a[$evid][$strat]",
					"value" => $data[$evid][$strat],
					"size" => 3
				));
			}
			$t->define_data($ar);
		}
		$t->set_sortable(false);
	}

	function _init_strat_res_tbl(&$t, $o, $sums)
	{
		$t->define_field(array(
			"name" => "task",
			"caption" => t("Toimetus"),
			"align" => "right"
		));
		// add all strats to table
		arsort($sums);
		//foreach($o->connections_from(array("type" => "RELTYPE_STRAT")) as $c)
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

	function _strat_res($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];

		$pi = get_instance(CL_PROJECT);
		$conns = $arr["obj_inst"]->connections_to(array("from.class_id" => CL_PROJECT));
		$c = reset($conns);
		$proj  = $c->from();


		$strats = array();
		foreach($proj->connections_from(array("type" => "RELTYPE_STRAT")) as $c)
		{
			$strats[$c->prop("to")] = $c->prop("to");
		}

		// get all eval objs
		$ol = new object_list(array(
			"class_id" => CL_PROJECT_STRAT_EVALUATION,
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
					$wt = !empty($wts[$o->prop("evaluator")]) ? $wts[$o->prop("evaluator")]/100.0 : 1;
					$data[$evid][$strat] += $eval * $wt;
				}
			}
		}
		$sbs = array();
		$sums = array();
		foreach($pi->get_events_for_project(array("project_id" => $proj->id())) as $evid)
		{
			$ar = array(
				"task" => html::obj_change_url($evid),
			);
			$sum = 0;
			foreach($strats as $strat)
			{
				$ar[$strat] = number_format($data[$evid][$strat] / $ol->count(), 2);
				$sum += $ar[$strat];
				$sbs[$strat] += $ar[$strat];
				$sums[$strat] += $ar[$strat];
			}
			$ar["sum"] = number_format($sum, 2);
			$t->define_data($ar);
		}
		$this->_init_strat_res_tbl($t, $proj, $sums);

		$t->sort_by();
		$t->set_sortable(false);
		$sbs["task"] = t("<b>Summa</b>");
		$t->define_data($sbs);

	}
	
	function _get_strat_eval($p)
	{
		$pp = get_current_person();
		$ol = new object_list(array(
			"class_id" => CL_PROJECT_STRAT_EVALUATION,
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
			$o->set_class_id(CL_PROJECT_STRAT_EVALUATION);
			$o->set_name(sprintf(t("Hinnang projektile %s"), $p->name()));
			$o->set_prop("proj", $p->id());
			$o->set_prop("evaluator" , $pp->id());
			$o->save();
			return $o;
		}
	}

	function _save_strat_a($arr)
	{
		// see if there is an eval for this person already, if not, create it , if it is, update it
		$se = $this->_get_strat_eval($arr["obj_inst"]);
		$se->set_meta("grid", $arr["request"]["a"]);
		$se->save();
	}

	function _srt($arr)
	{
		$tv =& $arr["prop"]["vcl_inst"];
		// add all evaluators
		$ol = new object_list(array(
			"class_id" => CL_PROJECT_STRAT_EVALUATION,
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

		$pi = get_instance(CL_PROJECT);
		$conns = $arr["obj_inst"]->connections_to(array("from.class_id" => CL_PROJECT));
		$c = reset($conns);
		$proj  = $c->from();

		$this->_init_strat_a_tbl($t, $proj);

		$strats = array();
		foreach($proj->connections_from(array("type" => "RELTYPE_STRAT")) as $c)
		{
			$strats[$c->prop("to")] = $c->prop("to");
		}

		// get all eval objs
		$ol = new object_list(array(
			"class_id" => CL_PROJECT_STRAT_EVALUATION,
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
					$data[$evid][$strat] += $eval;
				}
			}
		}
		foreach($pi->get_events_for_project(array("project_id" => $proj->id())) as $evid)
		{
			$ar = array(
				"task" => html::obj_change_url($evid),
			);
			foreach($strats as $strat)
			{
				$ar[$strat] = number_format($data[$evid][$strat] / $ol->count(), 2);
			}
			$t->define_data($ar);
		}
		$t->sort_by();
		$t->set_sortable(false);
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
	}

	function _strat_wt($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_strat_wt_t($t);

		$pi = get_instance(CL_PROJECT);
		$conns = $arr["obj_inst"]->connections_to(array("from.class_id" => CL_PROJECT));
		$c = reset($conns);
		$proj  = $c->from();

		// get project team
		$team = new object_list($proj->connections_from(array("type" => "RELTYPE_PARTICIPANT")));

		$wts = $arr["obj_inst"]->meta("wts");
		$evals = $arr["obj_inst"]->meta("evals");
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
				))
			));
		}
	}

	function _save_strat_wt($arr)
	{
		$arr["obj_inst"]->set_meta("wts", $arr["request"]["wts"]);
		$arr["obj_inst"]->set_meta("evals", $arr["request"]["is"]);
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_project_strat_goal_eval_ws (aw_oid int primary key, aw_eval_dl int)");
			return true;
		}
	}
}
?>
