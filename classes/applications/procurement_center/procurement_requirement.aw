<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/procurement_center/procurement_requirement.aw,v 1.9 2007/12/06 14:33:50 kristo Exp $
// procurement_requirement.aw - N&otilde;ue 
/*

@classinfo syslog_type=ST_PROCUREMENT_REQUIREMENT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@tableinfo procuremnent_requirements index=aw_oid master_index=brother_of master_table=objects

@default table=objects
@default group=general

	@property pri type=select table=procuremnent_requirements field=aw_pri
	@caption Prioriteet

	@property desc type=textarea rows=20 cols=50 table=procuremnent_requirements field=aw_desc
	@caption Kirjeldus

	@property req_co type=relpicker reltype=RELTYPE_CO table=procuremnent_requirements field=aw_req_co
	@caption Tellija organisatsioon

	@property req_p type=relpicker reltype=RELTYPE_P table=procuremnent_requirements field=aw_req_p
	@caption Tellija isik

	@property project type=relpicker reltype=RELTYPE_PROJECT table=procuremnent_requirements field=aw_req_proj
	@caption Projekt

	@property state type=select table=procuremnent_requirements field=aw_req_state
	@caption Staatus

	@property planned_time type=date_select table=procuremnent_requirements field=aw_planned_time
	@caption Planeeritud t&auml;htaeg

	@property process type=relpicker reltype=RELTYPE_PROCESS table=procuremnent_requirements field=aw_process
	@caption Protsess

	@property budget type=textbox table=procuremnent_requirements field=aw_budget
	@caption Eelarve

@default group=offers

	@property offer_tb type=toolbar store=no no_caption=1
	@property offer_t type=table store=no no_caption=1

@default group=comments

	@property comments type=comments 

@default group=bugs

	@property bugs_toolbar type=toolbar store=no no_caption=1
	@property bugs_table type=table store=no no_caption=1

@groupinfo offers caption="Lahendused n&otilde;udele"
@groupinfo comments caption="Kommentaarid"
@groupinfo bugs caption="Arendus&uuml;lesanded"

@reltype CO value=1 clid=CL_CRM_COMPANY
@caption Organisatsioon

@reltype P value=2 clid=CL_CRM_PERSON
@caption Isik

@reltype PROJECT value=3 clid=CL_PROJECT
@caption Projekt

@reltype PROCESS value=4 clid=CL_PROCUREMENT_PROCESS
@caption Protsess

@reltype BUG value=5 clid=CL_BUG
@caption Arendus&uuml;lesanne

*/

class procurement_requirement extends class_base
{
	const AW_CLID = 1067;

	function procurement_requirement()
	{
		$this->init(array(
			"tpldir" => "applications/procurement_center/procurement_requirement",
			"clid" => CL_PROCUREMENT_REQUIREMENT
		));
		$this->model = get_instance("applications/procurement_center/procurements_model");
		$this->statuses = array(
			0 => t("Avatud"),
			1 => t("Spetsifitseerimisel"),
			2 => t("&Uuml;le antud"),
			3 => t("Testimisel")
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "state":
				$prop["options"] = $this->get_status_list();
				break;

			case "pri":
				$prop["options"] = array("" => t("--vali--")) + $this->get_priority_list($arr["obj_inst"]);
				break;

			case "offer_t":
				$this->_offer_t($arr);
				break;

			case "req_co":
				if (!$prop["value"])
				{
					$cc = get_current_company();
					$prop["value"] = $cc->id();
				}
				if (!isset($prop["options"][$prop["value"]]) && $prop["value"])
				{
					$po = obj($prop["value"]);
					$prop["options"][$prop["value"]] = $po->name();
				}
				break;

			case "req_p":
				if (!$prop["value"])
				{
					$cc = get_current_person();
					$prop["value"] = $cc->id();
				}
				if (!isset($prop["options"][$prop["value"]]) && $prop["value"])
				{
					$po = obj($prop["value"]);
					$prop["options"][$prop["value"]] = $po->name();
				}
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
			case "offer_t":
				$arr["obj_inst"]->set_meta("assessments", $arr["request"]["ass"]);
				$arr["obj_inst"]->set_meta("suitable", $arr["request"]["suitable"]);
				$arr["obj_inst"]->set_meta("nonsuitable", $arr["request"]["nonsuitable"]);
				break;
		}
		return $retval;
	}	

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "" && $t == "procuremnent_requirements")
		{
			$this->db_query("CREATE TABLE procuremnent_requirements (aw_oid int primary key, aw_desc text)");
			return true;
		}

		switch($f)
		{
			case "aw_pri":
			case "aw_planned_time":
			case "aw_req_proj":
			case "aw_req_co":
			case "aw_req_state":
			case "aw_process":
			case "aw_req_p":
				$this->db_add_col($t, array("name" => $f, "type" => "int"));
				return true;

			case "aw_budget":
				$this->db_add_col($t, array("name" => $f, "type" => "double"));
				return true;
		}
	}

	function _init_offer_t(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "solution",
			"caption" => t("Lahendus"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "offerer",
			"caption" => t("Pakkuja"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "default",
			"caption" => t("Eelistatud"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "assessment",
			"caption" => t("Hinne"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "suitable",
			"caption" => t("Sobiv"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "nonsuitable",
			"caption" => t("L&auml;bir&auml;&auml;kimistele"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "price",
			"caption" => t("Hind"),
			"align" => "center",
			"numeric" => 1,
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "price_pm",
			"caption" => t("+/-"),
			"align" => "center",
			"numeric" => 1,
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "tti",
			"caption" => t("Tundide arv"),
			"align" => "center",
			"numeric" => 1,
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "tti_pm",
			"caption" => t("+/-"),
			"align" => "center",
			"numeric" => 1,
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "readyness",
			"caption" => t("Valmidus"),
			"align" => "center",
			"sortable" => 1
		));
	}

	function _offer_t($arr)
	{	
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_offer_t($t);

		$data = $arr["obj_inst"]->meta("defaults");
		$ass = $arr["obj_inst"]->meta("assessments");
		$suitable = $arr["obj_inst"]->meta("suitable");
		$nonsuitable = $arr["obj_inst"]->meta("nonsuitable");
		$default = $data[$arr["obj_inst"]->id()];
		$ol = new object_list(array(
			"class_id" => CL_PROCUREMENT_REQUIREMENT_SOLUTION,
			"requirement" => $arr["obj_inst"]->id(),
			"lang_id" => array(),
			"site_id" => array()
		));
		$po = get_instance(CL_PROCUREMENT_REQUIREMENT_SOLUTION);
		foreach($ol->arr() as $o)
		{
			$ass_sum += $ass[$o->id()];
			$price_sum += $o->prop("price");
			$tti_sum += $o->prop("time_to_install");
			$ass_cnt++;
		}

		foreach($ol->arr() as $o)
		{
			$prdiff = $o->prop("price") - ($price_sum / $ass_cnt);
			$prp = (100 * $prdiff) / ($price_sum / $ass_cnt);
			$prp = $prp > 0 ? "+".number_format($prp, 1) : number_format($prp, 1);

			$hrdiff = $o->prop("time_to_install") - ($tti_sum / $ass_cnt);
			$hrp = (100 * $hrdiff) / ($tti_sum / $ass_cnt);
			$hrp = $hrp > 0 ? "+".number_format($hrp, 1) : number_format($hrp, 1);
			$t->define_data(array(
				"name" => $o->name(),
				"solution" => $o->prop("solution"),
				"offerer" => html::obj_change_url($o->prop("offerer_co"))." ".html::obj_change_url($o->prop("offerer_p")),
				"default" => ($default == $o->id() ? t("X") : t("")),
				"assessment" => html::textbox(array(
					"name" => "ass[".$o->id()."]",
					"value" => $ass[$o->id()],
					"size" => 5
				)),
				"suitable" => html::checkbox(array(
					"name" => "suitable[".$o->id()."]",
					"value" => 1,
					"checked" => $suitable[$o->id()]
				)),
				"nonsuitable" => html::checkbox(array(
					"name" => "nonsuitable[".$o->id()."]",
					"value" => 1,
					"checked" => $nonsuitable[$o->id()]
				)),
				"price" => number_format($o->prop("price"), 2),
				"price_pm" => $prp."%",
				"tti" => $o->prop("time_to_install"),
				"tti_pm" => $hrp."%",
				"readyness" => $po->readyness_states[$o->prop("readyness")],
				"oid" => $o->id()
			));
		}
		$t->sort_by();
		$t->set_sortable(false);

		$t->define_data(array(
			"name" => t("<b>Keskmine:</b>"),
			"assessment" => number_format($ass_sum / $ass_cnt, 2),
			"price" => number_format($price_sum / $ass_cnt, 2),
			"tti" => number_format($tti_sum / $ass_cnt, 2),
		));
		
	}

	function get_avg_solution_score($req)
	{
		$ass = $req->meta("assessments");
		$ol = new object_list(array(
			"class_id" => CL_PROCUREMENT_REQUIREMENT_SOLUTION,
			"requirement" => $req->id(),
			"lang_id" => array(),
			"site_id" => array()
		));
		$po = get_instance(CL_PROCUREMENT_REQUIREMENT_SOLUTION);
		foreach($ol->arr() as $o)
		{
			$ass_sum += $ass[$o->id()];
			$ass_cnt++;
		}
		return $ass_sum / $ass_cnt;
	}

	function get_status_list()
	{
		return $this->statuses;
	}

	function get_priority_list($o)
	{
		if (!is_oid($o->id()) && $this->can("view", $arr["request"]["parent"]))
		{
			return $this->model->get_pris_from_procurement($this->model->get_procurement_from_requirement(obj($_GET["parent"])));
		}
		else
		{
			$rv = array();
			if (is_oid($o->id()))
			{
				$rv = $this->model->get_pris_for_requirement($o);
			}
			if (count($rv) == 0)
			{
				$rv = $this->make_keys(range(1, 10));
			}
			return $rv;
		}
	}

	function _get_bugs_toolbar($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "add_bug",
			"tooltip" => t("Lisa"),
			"url" => html::get_new_url(CL_BUG, $arr["obj_inst"]->id(), array(
				"return_url" => get_ru(),
				"alias_to" => $arr["obj_inst"]->id(),
				"reltype" => 5,
				"from_req" => $arr["obj_inst"]->id()
			)),
			"img" => "new.gif",
		));
		$tb->add_button(array(
			"name" => "delete",
			"tooltip" => t("Kustuta"),
			"img" => "delete.gif",
			"action" => "delete",
			"confirm" => t("Oled kindel, et soovid bugi kustutada?"),
		));
	}

	function _get_bugs_table($arr)
	{
		$ol = new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_BUG")));
		$t =& $arr["prop"]["vcl_inst"];
		$bt = get_instance(CL_BUG_TRACKER);
		$bt->_init_bug_list_tbl_no_edit($t);
		$bt->populate_bug_list_table_from_list($t, $ol, array("bt" => $arr["obj_inst"]));
	}

	function _get_offer_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_new_button(array(CL_PROCUREMENT_REQUIREMENT_SOLUTION), $arr["obj_inst"]->id(), null, array("set_requirement" => $arr["obj_inst"]->id()));
		$tb->add_delete_button();
	}
}
?>
