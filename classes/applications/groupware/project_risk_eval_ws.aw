<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/groupware/project_risk_eval_ws.aw,v 1.4 2007/12/06 14:33:32 kristo Exp $
// project_risk_eval_ws.aw - Riskide hindamise t&ouml;&ouml;laud 
/*

@classinfo syslog_type=ST_PROJECT_RISK_EVAL_WS relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@tableinfo aw_project_risk_eval_ws index=aw_oid master_index=brother_of master_table=objects

@default table=objects
@default group=general

	@property eval_dl type=date_select table=aw_project_risk_eval_ws field=aw_eval_dl
	@caption Hindamise t&auml;htaeg

@default group=risks

	@property risks type=table store=no no_caption=1

@default group=strat_res_avg

	@property strat_res type=table no_caption=1 store=no

@default group=strat_res_tree

	@layout srt_hb type=hbox 

		@property srt type=treeview parent=srt_hb store=no no_caption=1
		@property srt_tbl type=table parent=srt_hb store=no no_caption=1

@groupinfo risks caption="Hindamine"
@groupinfo strat_res caption="Hindamise tulemused"
	@groupinfo strat_res_tree caption="Hindajad" parent=strat_res submit=no
	@groupinfo strat_res_avg caption="Keskmised hinded" parent=strat_res submit=no
*/

class project_risk_eval_ws extends class_base
{
	const AW_CLID = 1089;

	function project_risk_eval_ws()
	{
		$this->init(array(
			"tpldir" => "applications/groupware/project_risk_eval_ws",
			"clid" => CL_PROJECT_RISK_EVAL_WS
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "risks":
				$this->_risks($arr);
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
		};
		return $retval;
	}

	function _init_risks_t(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Risk"),
			"align" => "center"
		));
		$t->define_field(array(		
			"name" => "infl",
			"caption" => t("M&otilde;ju"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "occ",
			"caption" => t("Juhtub"),	
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "tot",
			"caption" => t("Kokku"),	
			"align" => "center",
			"sortable" => 1
		));
	}

	function _risks($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_risks_t($t);

		$p = $this->_get_proj($arr["obj_inst"]);
		$se = $this->_get_eval($arr["obj_inst"]);
		$infl = $se->meta("infl");
		$occ = $se->meta("occ");
		foreach($p->connections_from(array("type" => "RELTYPE_RISK")) as $c)
		{
			$r = $c->to();
			$t->define_data(array(
				"name" => html::obj_change_url($r),
				"infl" => html::textbox(array(
					"name" => "infl[".$r->id()."]",
					"value" => $infl[$r->id()],
					"size" => 5
				)),
				"occ" => html::textbox(array(
					"name" => "occ[".$r->id()."]",
					"value" => $occ[$r->id()],
					"size" => 5
				)),
				"tot" => $occ[$r->id()] + $infl[$r->id()]
			));
		}
	}

	function _get_eval($p)
	{
		$pp = get_current_person();
		$ol = new object_list(array(
			"class_id" => CL_PROJECT_RISK_EVALUATION,
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
			$o->set_class_id(CL_PROJECT_RISK_EVALUATION);
			$o->set_name(sprintf(t("Hinnang projekti %s riskidele"), $p->name()));
			$o->set_prop("proj", $p->id());
			$o->set_prop("evaluator" , $pp->id());
			$o->save();
			return $o;
		}
	}

	function _get_proj($o)
	{
		$conns = $o->connections_to(array("from.class_id" => CL_PROJECT));
		$c = reset($conns);
		return $c->from();
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "risks":
				$this->_save_risks($arr);
				break;
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
		$arr["project"] = $_GET["project"];
	}


	function callback_post_save($arr)
	{
		if($arr["new"] == 1 && is_oid($arr["request"]["project"]) && $this->can("view" , $arr["request"]["project"]))
		{
			$project = obj($arr["request"]["project"]);
			$project->connect(array("to" => $arr["id"], "reltype" => "RISK_EVAL"));
		}
	}
	
	function _save_risks($arr)
	{
		$se = $this->_get_eval($arr["obj_inst"]);
		$se->set_meta("infl", $arr["request"]["infl"]);
		$se->set_meta("occ", $arr["request"]["occ"]);
		$se->save();
		
	}

	function _strat_res($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];

		$proj  = $this->_get_proj($arr["obj_inst"]);
		$this->_init_risks_t($t, $proj);

		// get all eval objs
		$ol = new object_list(array(
			"class_id" => CL_PROJECT_RISK_EVALUATION,
			"lang_id" => array(),
			"site_id" => array(),
			"proj" => $arr["obj_inst"]->id(),
		));
		$data_infl = array();
		$data_occ = array();
		foreach($ol->arr() as $o)
		{
			$g = safe_array($o->meta("infl"));
			$g2 = safe_array($o->meta("occ"));
			foreach($g as $evid => $d)
			{
				$data_infl[$evid] += $g[$evid];
				$data_occ[$evid] += $g2[$evid];
			}
		}
		$sbs = array();
		foreach($proj->connections_from(array("type" => "RELTYPE_RISK")) as $c)
		{
			$infl = $data_infl[$c->prop("to")]/$ol->count();
			$occ = $data_occ[$c->prop("to")];
			$t->define_data(array(
				"name" => html::obj_change_url($c->prop("to")),
				"infl" => number_format($infl,2),
				"occ" => number_format($occ/$ol->count(),2),
				"tot" => number_format(($data_occ[$c->prop("to")] + $data_infl[$c->prop("to")])/$ol->count(),2),
			));
		}
		$t->sort_by();
		$t->set_sortable(false);

	}

	function _srt($arr)
	{
		$tv =& $arr["prop"]["vcl_inst"];
		// add all evaluators
		$ol = new object_list(array(
			"class_id" => CL_PROJECT_RISK_EVALUATION,
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

		$proj  = $this->_get_proj($arr["obj_inst"]);
		$this->_init_risks_t($t, $proj);

		// get all eval objs
		$ol = new object_list(array(
			"class_id" => CL_PROJECT_RISK_EVALUATION,
			"lang_id" => array(),
			"site_id" => array(),
			"proj" => $arr["obj_inst"]->id(),
			"evaluator" => $arr["request"]["evalr"]
		));
		$data_infl = array();
		$data_occ = array();
		foreach($ol->arr() as $o)
		{
			$g = safe_array($o->meta("infl"));
			$g2 = safe_array($o->meta("occ"));
			foreach($g as $evid => $d)
			{
				$data_infl[$evid] += $g[$evid];
				$data_occ[$evid] += $g2[$evid];
			}
		}
		$sbs = array();
		foreach($proj->connections_from(array("type" => "RELTYPE_RISK")) as $c)
		{
			$infl = $data_infl[$c->prop("to")]/$ol->count();
			$occ = $data_occ[$c->prop("to")];
			$t->define_data(array(
				"name" => html::obj_change_url($c->prop("to")),
				"infl" => number_format($infl,2),
				"occ" => number_format($occ/$ol->count(),2),
				"tot" => number_format(($data_occ[$c->prop("to")] + $data_infl[$c->prop("to")])/$ol->count(),2),
			));
		}
		$t->sort_by();
		$t->set_sortable(false);

	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_project_risk_eval_ws (aw_oid int primary key, aw_eval_dl int)");
			return true;
		}
	}
}
?>
