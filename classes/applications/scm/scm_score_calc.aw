<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/scm/scm_score_calc.aw,v 1.11 2007/12/06 14:34:06 kristo Exp $
// scm_score_calc.aw - Punktis&uuml;steem 
/*

@classinfo syslog_type=ST_SCM_SCORE_CALC relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=tarvo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property score_calculator type=select editonly=1
@caption Vali koodialus

@property active_calc type=chooser orient=vertical editonly=1
@caption Aktiivne kood

@groupinfo algorithm caption="Algoritm"
	@default group=algorithm

	@property max_points type=textbox size=5
	@caption Maksimumpunktid

	@property points_step type=textbox size=5
	@caption Samm

	@property points_exception type=textbox size=20
	@caption Erandid

	@property points_others type=textbox size=5
	@caption &Uuml;lej&auml;&auml;nud v&otilde;istlejad

	@property cntr_formula type=releditor reltype=RELTYPE_CFGCONTROLLER props=formula
	@caption Kood

@groupinfo manual_points caption="Kohapunktid"
	@default group=manual_points

	@property man_count type=textbox size=5
	@caption Punkte saavad x esimest

	@property man_points type=text
	@caption Punktid

@reltype CFGCONTROLLER value=1 clid=CL_CFGCONTROLLER
@caption Kontroller

*/

class scm_score_calc extends class_base
{
	const AW_CLID = 1097;

	function scm_score_calc()
	{
		$this->init(array(
			"tpldir" => "applications/scm/scm_score_calc",
			"clid" => CL_SCM_SCORE_CALC
		));
		$this->_set_data();
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- get_property --//
			case "active_calc":
				$prop["options"] = array(
					"man_code" => "Kood",
					"man_alg" => "Algoritm",
					"man_pt" => "M&auml;&auml;ratud punktid",
				);
			break;
			case "cntr_formula":
				$c = $arr["obj_inst"]->get_first_conn_by_reltype("RELTYPE_CFGCONTROLLER");
				if(!$c)
				{
					$this->_gen_new_cntr(&$arr);
					$c = $arr["obj_inst"]->get_first_conn_by_reltype("RELTYPE_CFGCONTROLLER");
				}
				$prop["rel_id"] = $c->id();
			break;
			case "score_calculator":
				foreach($algorithms = $this->algorithm_list() as $fun_name => $caption)
				{
					$prop["options"][$fun_name] = $caption;
				}
			break;

			case "man_count":
				
			break;

			case "man_points":
				$count = $arr["obj_inst"]->prop("man_count");
				$points = aw_unserialize($arr["obj_inst"]->prop("man_points"));
				for($i = 1; $i <= $count; $i++)
				{
					$textbox = html::textbox(array(
						"name" => "point[".$i."]",
						"size" => "5",
						"value" => $points[$i],
					));
					$html .= sprintf(t("Koht nr %s:"), $i). $textbox."<br/>";
				}
				$html .= t("&Uuml;lej&auml;&auml;nud:").html::textbox(array(
					"name" => "point[0]",
					"size" => "5",
					"value" => $points[0],
				));
				$prop["value"] = $html;
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
			//-- set_property --//
			case "score_calculator":
				if($arr["obj_inst"]->prop("score_calculator") != $prop["value"])
				{
					$o = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_CFGCONTROLLER");
					$arr["obj_inst"]->set_prop("score_calculator", $prop["value"]);
					$arr["obj_inst"]->save();
					$this->_set_code_to_cntr(&$arr, &$o);
				}
			break;

			case "man_points":
				$points = aw_serialize($arr["request"]["point"], SERIALIZE_NATIVE);
				$arr["obj_inst"]->set_prop("man_points", $points);
			break;
		}
		return $retval;
	}	

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}
	
	function callback_post_save($arr)
	{
		// make a new controller and connect it if necessary
		$c = $arr["obj_inst"]->get_first_conn_by_reltype("RELTYPE_CFGCONTROLLER");
		if(!$c)
		{
			$this->_gen_new_cntr(&$arr);
		}
	}
	
	function _set_code_to_cntr($arr, $cntr)
	{
		$cntr->set_prop("formula", $this->_codes($arr["obj_inst"]->prop("score_calculator")));
		$cntr->save();
	}

	function _gen_new_cntr($arr)
	{
		$cntr = obj();
		$cntr->set_parent($arr["obj_inst"]->id());
		$cntr->set_class_id(CL_CFGCONTROLLER);
		$cntr->set_name($arr["obj_inst"]->name()." / kontroller");
		$id = $cntr->save_new();
		if($arr["obj_inst"]->prop("score_calculator"))
		{
			$this->_set_code_to_cntr(&$arr, &$cntr);
		}
		$arr["obj_inst"]->connect(array(
			"to" => $id,
			"type" => 1,
		));
	}

	function algorithm_list()
	{
		return $this->data;
	}

	function get_score_calcs()
	{
		$list = new object_list(array(
			"class_id" => CL_SCM_SCORE_CALC,
		));
		return $list->arr();
	}

	/* algoritmide funktsioonid */

	function _code($place, $score_calc)
	{
		$ctr = $score_calc->get_first_obj_by_reltype("RELTYPE_CFGCONTROLLER");
		$object_to_run_on = obj();
		$ctr_instance = get_instance(CL_CFGCONTROLLER);
		$prop = array();
		$return = $ctr_instance->check_property(
			$ctr->id(),
			$object_to_run_on->id(),
			$prop,
			$_GET,
			$place,
			$object_to_run_on
		);
		return $return;
	}

	function _alg($place, $score_calc)
	{
		$excep = $score_calc->prop("points_exception");
		$excep_split = split(",", $excep);
		if(in_array(($place-1), array_keys($excep_split)))
		{
			return $excep_split[$place-1];
		}
		$max = $score_calc->prop("max_points");
		$step = $score_calc->prop("points_step");
		$other = $score_calc->prop("points_others");
		$pt = (($s = ($max - (($place - 1) * $step))) > 0)?$s:$other;
		//echo $place."/";
		return $pt;
	}

	function _points($place, $score_calc)
	{
		$num = $score_calc->prop("man_count");
		$pts = aw_unserialize($score_calc->prop("man_points"));
		$place = ($place > $num)?0:$place;
		return $pts[$place];
	}

	function _codes($code)
	{
$codes["_first_three_step_five"] = 
"\$point = 15;
\$step = 4;
\$retval = ((\$s = (\$point - ((\$entry - 1) * \$step))) > 0)?\$s:0;
";

$codes["_first_five_for_breath"] = 
"\$point = 10;
\$step = 2;
\$retval = ((\$s = (\$point - ((\$entry - 1) * \$step))) > 0)?\$s:0;
";

$codes["_first_three_for_shootout"] = 
"\$point = 5;
\$step = 2;
\$retval = ((\$s = (\$point - ((\$entry - 1) * \$step))) > 0)?\$s:0;
";
		return $codes[$code];
	}
	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

//-- methods --//

	function _set_data()
	{
		$this->data = array(
			"_first_five_for_breath" => t("Esimesed 5 (10p alates, astmega 2)"),
			"_first_three_step_five" => t("Esimesed 3 (15p alates, astmega 4)"),
			"_first_three_for_shootout" => t("Esimesed 3 (5p alates, astmega 2)"),
		);

	}

	function get_score_calc($arr = array())
	{
		$obj = obj($arr["score_calc"]);
		$u= strlen($s = $obj->prop("score_calculator"))?$s:false;
		return $u;
	}

	/**
		@param data required type=array
		@param score_calc required type=oid
		@param competition required type=oid
		@comment
			sorts results
	**/
	function calc_results($arr)
	{
		// at first.. we must sort the array accordingly to result_type.sort
		// then, we loop over the results starting from first place.. and ask points for each place for its function
		$event_inst = get_instance(CL_SCM_EVENT);
		$competition_inst = get_instance(CL_SCM_COMPETITION);
		$res_type_inst = get_instance(CL_SCM_RESULT_TYPE);

		$res_type = $competition_inst->get_result_type(array(
			"competition" => $arr["competition"],
		));
		$sorted = $res_type_inst->sort_results(array(
			"data" => $arr["data"],
			"result_type" => $res_type,
		));
		$arr["score_calc"] = call_user_method("prop", obj($arr["competition"]), "scm_score_calc");
		$score_calc_obj = obj($arr["score_calc"]);
		$active = $score_calc_obj->prop("active_calc");
		switch($active)
		{
			case "man_code":
				$fun = "_code";
				break;
			case "man_alg":
				$fun = "_alg";
				break;
			case "man_pt":
				$fun = "_points";
				break;
			default:
				$fun = NULL;
				break;
		}

		foreach($sorted as $id => $place)
		{
			$ret[$id] = array(
				"place" => $place,
				"points" => $this->$fun($place, &$score_calc_obj),
			);
		}
		return $ret;
	}
}
?>
