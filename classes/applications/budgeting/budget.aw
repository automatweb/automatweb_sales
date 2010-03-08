<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/budgeting/budget.aw,v 1.11 2008/05/15 15:29:53 markop Exp $
// budget.aw - Eelarve 
/*

@classinfo syslog_type=ST_BUDGET relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@tableinfo aw_budgets index=aw_oid master_table=objects master_index=brother_of

@default table=aw_budgets
@default group=general

	@property project type=relpicker reltype=RELTYPE_PROJECT field=aw_project
	@caption Project

	@property scenario type=relpicker reltype=RELTYPE_SCENARIO field=aw_scenario
	@caption Stsenaarium

	@property start type=date_select field=aw_date_start default=-1
	@caption Alates

	@property end type=date_select field=aw_date_end default=-1
	@caption Kuni

	@property owner type=relpicker reltype=RELTYPE_OWNER field=aw_owner 
	@caption Vastutaja


@default group=m1,m2,m3

	@property total type=textbox size=5
	@caption Kogusumma

	@property m type=table store=no no_caption=1

	@property desc type=text store=no 
	@caption Hetkel projekti kasum

@groupinfo m caption="Raha"
	@groupinfo m1 caption="Voovaade" parent=m
	@groupinfo m2 caption="Grupivaade" parent=m
	@groupinfo m3 caption="T&uuml;&uuml;bivaade" parent=m

@groupinfo ex caption="V&auml;listamine"

	@property ex_table type=table store=no no_caption=1 group=ex

@reltype PROJECT value=1 clid=CL_PROJECT
@caption Projekt

@reltype SCENARIO value=2 clid=CL_BUDGETING_SCENARIO
@caption Stsenaarium

@reltype OWNER value=3 clid=CL_CRM_PERSON
@caption Vastutaja
*/

class budget extends class_base
{
	function budget()
	{
		$this->init(array(
			"tpldir" => "applications/budgeting/budget",
			"clid" => CL_BUDGET
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}
		
	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_budgets (aw_oid int primary key, aw_project int)");
			return true;
		}

		switch($f)
		{
			case "total":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "double"
				));
				return true;
				break;

			case "aw_scenario":
			case "aw_date_start":
			case "aw_date_end":
			case "aw_owner":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
				break;
		}
	}

	function _set_m($arr)
	{
		$arr["obj_inst"]->set_meta("mod_tax_pct", $arr["request"]["p"]);
	}

	function _init_m_t(&$t, $arr)
	{
		if ($arr["request"]["group"] != "m2")
		{
			$t->define_field(array(
				"name" => "tax_grp",
				"caption" => t("Maksugrupp"),
				"align" => "center"
			));
		}
		$t->define_field(array(
			"name" => "tax",
			"caption" => t("Maks"),
			"align" => "left",
			"sortable" => 1,
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "tax_pri",
			"caption" => t("Prioriteet"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "tax_pct",
			"caption" => t("Maksu protsent"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "mod_tax_pct",
			"caption" => t("Muuda protsenti"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "tot_amt",
			"caption" => t("Maksu summa"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "forward",
			"caption" => t("Edasi kandub"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1
		));
	}

	function _get_m($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_m_t($t, $arr);

		// get all taxes that go away above the project
		// then all tasks in the project
		// then let the user play with numbers
		$m = get_instance("applications/budgeting/budgeting_model");
		$above = $arr["obj_inst"]->get_taxes_data();
		$amt = $arr["obj_inst"]->prop("total");
		$p = $arr["obj_inst"]->meta("mod_tax_pct");
		$clss = aw_ini_get("classes");
		$ex = $arr["obj_inst"]->meta("ex");
		foreach($above as $tax_data)
		{
			$tax = $tax_data["tax"];
			$t_pct = $tax->prop("amount");
			if (!empty($p[$tax->id()]))
			{
				$t_pct = $p[$tax->id()];
				$tax_amt = ($t_pct / 100.0) * $amt;
			}
			else
			{
				$tax_amt = $tax->calculate_amount_to_transfer($tax_data["account"] , $amt);
			}

//			$tax_amt = ($t_pct / 100.0) * $amt;
			$amt -= $tax_amt;
			$add = "";
			if ($tax->prop("max_deviation") > 0)
			{
				$add .= sprintf(t("<br>(Maksimaalne erinevus %s)"), $tax->prop("max_deviation"));
			}
			$gn = $tax->prop("tax_grp.name");
			if ($gn == "")
			{
				$gn = t("Muud maksud");
			}
			$t->define_data(array(
				"tax_grp" => $gn,
				"tax" => "&nbsp;&nbsp;&nbsp;&nbsp;".html::obj_change_url($tax),
				"tax_pct" => $tax->prop("amount"),
				"tot_amt" => number_format($tax_amt, 2),
				"forward" => number_format($amt, 2),
				"mod_tax_pct" => html::textbox(array(
					"name" => "p[".$tax->id()."]",
					"value" => $p[$tax->id()],
					"size" => 5
				)).$add,
				"tax_pri" => $tax->prop("pri"),
				"acct_type" => parse_obj_name($clss[$tax->prop("to_acct.class_id")]["name"]),
				"rown" => ++$rown
			));
		}

		$ol = $arr["obj_inst"]->get_tasks();
		// divide the rest of the money between tasks until it runs uut
		foreach($ol->arr() as $task)
		{
			$hr_price = $task->prop("hr_price");
			$tax_amt = $task->prop("num_hrs_guess") * $hr_price;
			$amt -= $tax_amt;
			$t->define_data(array(
				"tax_grp" => t("Muud maksud"),
				"tax" => "&nbsp;&nbsp;&nbsp;&nbsp;".html::obj_change_url($task),
				"tax_pct" => "",
				"tot_amt" => number_format($tax_amt, 2),
				"forward" => number_format($amt, 2),
				"acct_type" => parse_obj_name($clss[$task->class_id()]["name"]),
				"rown" => ++$rown
			));
		}

		$ol = $arr["obj_inst"]->get_meetings();
		// divide the rest of the money between tasks until it runs uut
		foreach($ol->arr() as $task)
		{
			$hr_price = $task->prop("hr_price");
			$tax_amt = $task->prop("num_hrs_guess") * $hr_price;
			$amt -= $tax_amt;
			$t->define_data(array(
				"tax_grp" => t("Muud maksud"),
				"tax" => "&nbsp;&nbsp;&nbsp;&nbsp;".html::obj_change_url($task),
				"tax_pct" => "",
				"tot_amt" => number_format($tax_amt, 2),
				"forward" => number_format($amt, 2),
				"acct_type" => parse_obj_name($clss[$task->class_id()]["name"]),
				"rown" => ++$rown
			));
		}

		$ol = $arr["obj_inst"]->get_calls();
		// divide the rest of the money between tasks until it runs uut
		foreach($ol->arr() as $task)
		{
			$hr_price = $task->prop("hr_price");
			$tax_amt = $task->prop("num_hrs_guess") * $hr_price;
			$amt -= $tax_amt;
			$t->define_data(array(
				"tax_grp" => t("Muud maksud"),
				"tax" => "&nbsp;&nbsp;&nbsp;&nbsp;".html::obj_change_url($task),
				"tax_pct" => "",
				"tot_amt" => number_format($tax_amt, 2),
				"forward" => number_format($amt, 2),
				"acct_type" => parse_obj_name($clss[$task->class_id()]["name"]),
				"rown" => ++$rown
			));
		}
		
		$ol = $arr["obj_inst"]->get_bugs();
		foreach($ol->arr() as $bug)
		{
			$hr_price = $bug->prop("skill_used.hour_price");
			$tax_amt = $bug->prop("num_hrs_guess") * $hr_price;
			$amt -= $tax_amt;
			$t->define_data(array(
				"tax_grp" => t("Muud maksud"),
				"tax" => "&nbsp;&nbsp;&nbsp;&nbsp;".html::obj_change_url($bug),
				"tax_pct" => "",
				"tot_amt" => number_format($tax_amt, 2),
				"forward" => number_format($amt, 2),
				"acct_type" => parse_obj_name($clss[$bug->class_id()]["name"]),
				"rown" => ++$rown
			));
		}
		$ol = $arr["obj_inst"]->get_products();
		foreach($ol->arr() as $product)
		{
			$tax_amt = $product->prop("price");
			$amt -= $tax_amt;
			$t->define_data(array(
				"tax_grp" => t("Muud maksud"),
				"tax" => "&nbsp;&nbsp;&nbsp;&nbsp;".html::obj_change_url($product),
				"tax_pct" => "",
				"tot_amt" => number_format($tax_amt, 2),
				"forward" => number_format($amt, 2),
				"acct_type" => parse_obj_name($clss[$product->class_id()]["name"]),
				"rown" => ++$rown
			));
		}

		if ($arr["request"]["group"] == "m2")
		{
			$t->set_rgroupby(array("tax_grp" => "tax_grp"));
		}
		else
		if ($arr["request"]["group"] == "m3")
		{
			$t->set_rgroupby(array("acct_type" => "acct_type"));
		}
		$t->set_default_sortby("rown");
//		$t->sort_by();
		$t->set_sortable(false);
	}

	function _get_desc($arr)
	{
		$m = get_instance("applications/budgeting/budgeting_model");
		$path = $m->get_transfer_path_from_proj($arr["obj_inst"]->get_project_object());
		$above = $arr["obj_inst"]->get_taxes_data();
		$amt = $arr["obj_inst"]->prop("total");
		$p = $arr["obj_inst"]->meta("mod_tax_pct");
		$ex = $arr["obj_inst"]->meta("ex");

		$p_string = "pappi saab selliseid kohti maksustades: ";
		foreach($path as $pa)
		{
			$p_string .= " , ".html::get_change_url($pa->id() , array() , $pa->name());
		}

		foreach($above as $tax_data)
		{
			$tax = $tax_data["tax"];
			if (!empty($p[$tax->id()]))
			{
				$t_pct = $p[$tax->id()];
				$tax_amt = ($t_pct / 100.0) * $amt;
			}
			else
			{
				$tax_amt = $tax->calculate_amount_to_transfer($tax_data["account"] , $amt);
			}

			$amt -= $tax_amt;
		}

		// divide the rest of the money between tasks until it runs uut
		$ol = $arr["obj_inst"]->get_tasks();
		foreach($ol->arr() as $task)
		{
			$hr_price = $task->prop("hr_price");
			$tax_amt = $task->prop("num_hrs_guess") * $hr_price;
			$amt -= $tax_amt;
		}

		$ol = $arr["obj_inst"]->get_meetings();
		foreach($ol->arr() as $task)
		{
			$hr_price = $task->prop("hr_price");
			$tax_amt = $task->prop("num_hrs_guess") * $hr_price;
			$amt -= $tax_amt;
		}

		$ol = $arr["obj_inst"]->get_calls();
		foreach($ol->arr() as $task)
		{
			$hr_price = $task->prop("hr_price");
			$tax_amt = $task->prop("num_hrs_guess") * $hr_price;
			$amt -= $tax_amt;
		}
		
		$ol = $arr["obj_inst"]->get_bugs();
		foreach($ol->arr() as $bug)
		{
			$tax_amt = $bug->prop("num_hrs_guess") * $bug->prop("skill_used.hour_price");
			$amt -= $tax_amt;
		}
		$ol = $arr["obj_inst"]->get_products();
		foreach($ol->arr() as $product)
		{
			$tax_amt = $product->prop("price");
			$amt -= $tax_amt;
		}
		$arr["prop"]["value"] = number_format($amt, 2).
		" <br>".t("Projekti maksumus: ").number_format($arr["obj_inst"]->get_budget_sum(), 2).
		" <br>".$p_string;


		;
	}

	function _get_ex_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$m = get_instance("applications/budgeting/budgeting_model");
		if(!$project = $arr["obj_inst"]->get_project_object())
		{
			arr(t("Projekt valimata"));
		}
		$ol = new object_list();
		$ol -> add($project->get_tasks());
		$ol -> add($project->get_bugs());
		$ol -> add($project->get_meetings());
		$ol -> add($project->get_calls());
		$ol -> add($project->get_products());

		foreach($m->get_all_taxes_above_project(obj($arr["obj_inst"]->prop("project"))) as $tax)
		{
			$ol->add($tax);
		}
		
		$t->table_from_ol(
			$ol,
			array("name", "comment", "to_acct", "amount", "pri", "max_deviation_minus", "max_deviation_plus", "tax_grp"),
			CL_BUDGETING_TAX
		);
		$t->remove_chooser();

		$t->define_field(array(
			"name" => "ex",
			"caption" => t("V&auml;lista"),
			"align" => "center"
		));
		$ex = $arr["obj_inst"]->meta("ex");
		foreach($t->get_data() as $idx => $row)
		{
			$row["ex"] = html::checkbox(array(
				"name" => "ex[".$row["oid"]."]",
				"value" => 1,
				"checked" => $ex[$row["oid"]] == 1
			));
			$t->set_data($idx, $row);
		}
	}

	function _set_ex_table($arr)
	{
		$arr["obj_inst"]->set_meta("ex", $arr["request"]["ex"]);
	}
}
?>
