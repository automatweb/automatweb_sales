<?php

class crm_company_stats_impl extends class_base
{
	private $co_currency;

	function crm_company_stats_impl()
	{
		$this->init();

		$this->res_types = array(
			"rows"	=> t("Read"),
			"task" => t("Toimetused")
		);
		if($this->there_are_bugs())
		{
			$this->res_types["bugs"] = t("Arendus&uuml;lesanded");
		}
		$this->res_types["bills"] = t("Arved");
		$this->res_types["proj"] = t("Projektid");
		$this->res_types["cust"] = t("Kliendid");
		$this->res_types["cust_det"] = t("Kliendid - detailvaade");
		$this->res_types["pers"] = t("T&ouml;&ouml;tajad");
		$this->res_types["pers_det"] = t("T&ouml;&ouml;tajad - detailvaade");
	}

	public function _get_stats_annual_reports_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->table_from_ol($arr["obj_inst"]->get_annual_reports(), array("year", "currency", "value_added_tax", "social_security_tax", "assets", "turnover", "profit", "employees", "turnover_per_employee"), crm_company_annual_report_obj::CLID);
	}

	function _get_project_mgr($arr)
	{
		$c = get_instance(CL_CRM_COMPANY);
		$arr["prop"]["options"] = $c->get_employee_picker($arr["obj_inst"], true);
		$arr["prop"]["value"] = $arr["request"]["stats_s_worker_sel"];
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function _get_stats_s_cust_type($arr)
	{
		$arr["prop"]["options"] = array(
			CL_CRM_PERSON => t("Isik"),
			CL_CRM_COMPANY => t("Organisatsioon")
		);
		$arr["prop"]["value"] = $arr["request"][$arr["prop"]["name"]];
	}

	function _get_stats_s_state($arr)
	{
		$arr["prop"]["options"] = array(
			"" => t("K&otilde;ik"),
			"done" => t("Tehtud"),
			"not_done" => t("Tegemata")
		);
		$arr["prop"]["value"] = $arr["request"][$arr["prop"]["name"]];
	}

	function _get_stats_s_bill_state($arr)
	{
		$b = get_instance(CL_CRM_BILL);
		$arr["prop"]["options"] = array(-2 => t("K&otilde;ik"), -3 => t("Arve puudub"),-1 => t("Arve tehtud")) + $b->states + array(-4 => t("&Uuml;le t&auml;htaja")) + array(-6 => t("Sisse n&otilde;udmisel"));
		//unset($arr["prop"]["options"][-5]);
		$arr["prop"]["value"] = $arr["request"][$arr["prop"]["name"]];
	}


	function _get_stats_s_worker_sel($arr)
	{
		$c = get_instance(CL_CRM_COMPANY);
		$arr["prop"]["options"] = $c->get_employee_picker($arr["obj_inst"], true);
		$arr["prop"]["value"] = $arr["request"]["stats_s_worker_sel"];
	}

	function _get_stats_s_area($arr)
	{
		$cl = get_instance(CL_CLASSIFICATOR);
		$arr["prop"]["options"] = array("" => t("--vali--")) + $cl->get_options_for(array(
			"clid" => CL_PROJECT,
			"name" => "proj_type"
		));
		$arr["prop"]["value"] = $arr["request"][$arr["prop"]["name"]];
	}

	function _get_stats_s_res_type($arr)
	{
		$arr["prop"]["options"] = $this->res_types;
		$arr["prop"]["value"] = $arr["request"]["stats_s_res_type"];
	}

	function _init_stats_s_res_t($t, $req)
	{
		if ($req["stats_s_res_type"] === "cust")
		{
			$t->define_field(array(
				"name" => "cust",
				"caption" => t("Klient"),
				"align" => "right"
			));
		}
		else
		if ($req["stats_s_res_type"] === "proj")
		{
			$t->define_field(array(
				"name" => "cust",
				"caption" => t("Klient"),
				"align" => "center"
			));

			$t->define_field(array(
				"name" => "proj",
				"caption" => t("Projekt"),
				"align" => "right"
			));
		}
		else
		if ($req["stats_s_res_type"] === "task")
		{
			$t->define_field(array(
				"name" => "cust",
				"caption" => t("Klient"),
				"align" => "center"
			));

			$t->define_field(array(
				"name" => "proj",
				"caption" => t("Projekt"),
				"align" => "center"
			));

			$t->define_field(array(
				"name" => "task",
				"caption" => t("Toimetus"),
				"align" => "center"
			));

			$t->define_field(array(
				"name" => "person",
				"caption" => t("Isik"),
				"align" => "center"
			));

			$t->define_field(array(
				"name" => "deadline",
				"caption" => t("T&auml;htaeg"),
				"align" => "center",
				"type" => "time"
			));
		}
		else
		{
			$t->define_field(array(
				"name" => "person",
				"caption" => t("Isik"),
				"align" => "right"
			));
		}

		$t->define_field(array(
			"name" => "pt",
			"caption" => t("<a href='javascript:void(0)' alt='Prognoositud tunde' title='Prognoositud tunde'>PT</a>"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "tt",
			"caption" => t("<a href='javascript:void(0)' alt='Tegelikult tunde' title='Tegelikult tunde'>TT</a>"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "kt",
			"caption" => t("<a href='javascript:void(0)' alt='Tunde Kliendile' title='Tunde Kliendile'>KT</a>"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "sum_plus",
			"caption" => t("Vahe (+)"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "sum_minus",
			"caption" => t("Vahe (-)"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa"),
			"align" => "center"
		));

		if ($req["stats_s_res_type"] === "task")
		{
			$t->define_field(array(
				"name" => "payment_over_date",
				"caption" => t("<a href='javascript:void(0)' alt='Maksega hilinenud p&auml;evade arv' title='Maksega hilinenud p&auml;evade arv'>MHPA</a>"),
				"align" => "center",
			));
		}

		if ($req["stats_s_res_type"] !== "pers" && $req["stats_s_res_type"] !== "task")
		{
			$t->define_field(array(
				"name" => "otherexp",
				"caption" => t("Muud kulud"),
				"align" => "center"
			));
		}
	}

	function _get_stats_s_res($arr)
	{
		aw_set_exec_time(AW_LONG_PROCESS);
		if (!$arr["request"]["MAX_FILE_SIZE"])
		{
			return;
		}

		$t = $arr["prop"]["vcl_inst"];
		// get all persons from company
		$u = get_instance(CL_USER);
		$slaves = array();
		$c = new crm_company();
		$c->get_all_workers_for_company($arr["obj_inst"], $slaves);

		if (is_array($arr["request"]["stats_s_worker_sel"]))
		{
			$slaves = $this->make_keys($arr["request"]["stats_s_worker_sel"]);
		}

		if($arr["request"]["stats_s_res_type"] == "bills")//selle laseb enne k2iku, pole igast taske ja buge vaja
		{
			return $this->_get_bills_stats($slaves, $tasks, $arr["request"]);
		}

		// get all tasks based on the search
		$tasks = $this->_get_tasks_from_search($arr["request"]);

		if($arr["request"]["stats_s_res_type"] == "pers_det" ||
			$arr["request"]["stats_s_res_type"] == "bugs" ||
			$arr["request"]["stats_s_res_type"] == "cust_det"
		)//teistele pole buge vaja
		{
			$bugs = $this->_get_bugs_from_search($arr["request"]);
		}
		if($arr["request"]["stats_s_res_type"] == "rows")
		{
			return $this->_get_row_stats($slaves, $tasks,$arr["request"], $arr["prop"]["vcl_inst"]);
		}
		$this->_init_stats_s_res_t($t, $arr["request"]);
		switch ($arr["request"]["stats_s_res_type"])
		{
			default:
			case "cust":
				$data = $this->_get_cust_stats($slaves, $tasks, $arr["request"]);
				break;

			case "proj":
				$data = $this->_get_proj_stats($slaves, $tasks, $arr["request"]);
				break;

			case "pers":
				$data = $this->_get_pers_stats($slaves, $tasks, $arr["request"]);
				break;

			case "task":
				$data = $this->_get_task_stats($slaves, $tasks, $arr["request"]);
				break;

			case "cust_det":
				$data = $this->_get_task_cust_det($slaves, $tasks, $arr["request"], $bugs);
				break;

			case "pers_det":
				return $this->_get_task_pers_det($slaves, $tasks, $arr["request"], $bugs);

			case "rows":
				return $this->_get_row_stats($slaves, $tasks, $arr["request"]);

			case "bugs":
				$data = $this->_get_bug_stats($slaves, $tasks, $arr["request"], $bugs, $t);
				break;
		}

		$sums = array();
		$calc = array("pt", "tt", "kt", "sum", "otherexp", "sum_minus" , "sum_plus" , "twh_str","hob_str");

		foreach($data as $t_d)
		{
			$a = 0;
			foreach($calc as $fld)
			{
				$sums[$fld] += $t_d[$fld];
				$a += $t_d[$fld];
				//punktid komadeks ja komad 2ra kaotada
				$t_d[$fld] = str_replace("," ,"" , $t_d[$fld]);
				$t_d[$fld] = str_replace("." ,"," , $t_d[$fld]);
			}
			if ($a > 0)
			{
				$t->define_data($t_d);
			}
		}
		$t->set_sortable(false);

		//$sums[$t->rowdefs[0]["name"]] = t("Summa"); // oh my, this is nasty
		foreach($sums as $k => $v)
		{
			//punktid komadeks ja komad 2ra kaotada
			$v = str_replace("," ,"" , $v);
			$v = str_replace("." ,"," , $v);
			$sums[$k] = "<b>".$v."</b>";
		}

		$t->define_data($sums);

		if($arr["request"]["return_table"])
		{
			return;
		}

		$sf = new aw_template;
		$sf->db_init();
		$sf->tpl_init("automatweb");
		$sf->read_template("index.tpl");
		$sf->vars(array(
			"content"	=> $t->draw(),
			"uid" => aw_global_get("uid"),
			"charset" => aw_global_get("charset"),
			"MINIFY_JS_AND_CSS" => $sf->parse("MINIFY_JS_AND_CSS")
		));
		die($sf->parse());

	}

	function _init_stats_bugs_t(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Arendus&uuml;lesanne"),
			"align" => "left",
		));
		$t->define_field(array(
			"name" => "date",
			"caption" => t("Kuup&auml;ev"),
			"sortable" => 1,
			"numeric" => 1,
			"type" => "time",
			"format" => "d.m.Y",
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "cust",
			"caption" => t("Klient"),
			"align" => "left",
		));
		$t->define_field(array(
			"name" => "proj",
			"caption" => t("Projekt"),
			"align" => "leftr",
		));
		$t->define_field(array(
			"name" => "project_mrg",
			"caption" => t("Projektijuht"),
			"align" => "left",
		));

		$t->define_field(array(
			"name" => "did",
			"caption" => t("Teostaja"),
			"align" => "left",
		));
		$t->define_field(array(
			"name" => "length",
			"caption" => t("Kestvus"),
			"align" => "right",
		));
		$t->define_field(array(
			"name" => "tk",
			"caption" => html::href(array("url" => "#" , "title" => t("Tunde kliendile") , "caption" => t("TK"))),
			"align" => "right",
		));
		$t->define_field(array(
			"name" => "state",
			"caption" => t("Staatus"),
			"align" => "left",
		));
		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa"),
			"align" => "right",
			"numeric" => 1
		));
	}

	function _get_bug_stats($slaves, $tasks, $req, $bugs, $t = null)
	{

		if($t)
		{
			$t = new vcl_table;
		}
		$this->_init_stats_bugs_t($t);
		$bugs->sort_by(array("prop" => "date","order" => "asc"));

		$bi = get_instance(CL_BUG);
		$statuses = $bi->bug_statuses;
		$cust_sum = 0;
		$names_ol = new object_list();
		foreach($bugs->arr() as $bug)
		{
			if($bug->prop("customer"))$names_ol->add($bug->prop("customer"));
			if($bug->prop("project"))$names_ol->add($bug->prop("project"));
			if($bug->prop("who"))$names_ol->add($bug->prop("who"));
			if($bug->prop("project.proj_mgr"))$names_ol->add($bug->prop("project.proj_mgr"));
		}

		$names = $names_ol->names();

		foreach($bugs->arr() as $bug)
		{
			$hr_price = 0;
			$who = $bug->prop("who");
		//tunnihind ameti j2rgi ei funka vist kuskil
/*			if(is_oid($who) && $this->can("view" , $who))
			{
				$person = obj($who);
				$rank = $person->prop("rank");
				if(is_oid($rank) && $this->can("view" ,$rank))
				{
					$rank_obj = obj($rank);
					$hr_price = $rank_obj->prop("hr_price");
				}

			}
*/
			$hr_price = $bug->prop("hr_price");
			$hrs_real = $last_date = $hrs_cust = 0;
			foreach(safe_array($this->bug_comments[$bug->id()]) as $bc)
			{
				$hrs_real += $bc["time_real"];
				$hrs_cust += $bc["time_customer"];
				$last_date = max($last_date, $bc["date"]);
			}

			$hours_to_customer = $bug->prop("num_hrs_to_cust") ? $bug->prop("num_hrs_to_cust") : $hrs_cust;
			$hours_real = $bug->prop("num_hrs_real") ? $bug->prop("num_hrs_real") : $hrs_real;

			$sum = $hours_to_customer ? $hours_to_customer * $hr_price : $hours_real * $hr_price;

			if($last_date < 32423)
			{
				$last_date = $bug->prop("deadline");
			}
			$t->define_data(array(
				"name" => $this->js_obj_url($bug->id() , $bug->name()),
				"date" => $last_date,
				"cust" => $this->js_obj_url($bug->prop("customer") , $names[$bug->prop("customer")]),//html::obj_change_url($bug->prop("customer")),
				"proj" => $this->js_obj_url($bug->prop("project") , $names[$bug->prop("project")]),//html::obj_change_url($bug->prop("project")),
				"did" => $this->js_obj_url($bug->prop("who") , $names[$bug->prop("who")]),//html::obj_change_url($bug->prop("who")),
				"length" => $this->hours_format($hours_real),//number_format($bug->prop("num_hrs_real"), 3 , ',', '') ,
				"tk" => $this->hours_format($hours_to_customer),//number_format($bug->prop("num_hrs_to_cust"), 2 , ',', ''),
				"state" => $statuses[$bug->prop("bug_status")],
				"bill_state" => $bs,
				"sum" => number_format($sum, 2 , ',', ''),
				"project_mrg" => $this->js_obj_url($bug->prop("project.proj_mgr") , $names[$bug->prop("project.proj_mgr")]),//$bug->prop("project.proj_mgr.name"),
			));
			$l_sum += $hours_real;
			$cust_sum += $hours_to_customer;
			$s_sum += $sum;
		}
		$sf = new aw_template;
		$sf->db_init();
		$sf->tpl_init("automatweb");
		$sf->read_template("index.tpl");

		$t->set_default_sorder("asc");
		$t->set_default_sortby("date");
		$t->sort_by();

		//summa rida
		$t->define_data(array(
				"name" => '<b>'.t("Summa:").'</b>',
				"length" => $this->hours_format($l_sum),//number_format($l_sum, 3 , ',', '') ,
				"sum" => number_format($s_sum, 2 , ',', ''),
				"tk" => $this->hours_format($cust_sum),
			));
		if($req["return_table"])
		{
			return;
		}

		classload("core/util/minify_js_and_css");
		$sf->vars(array(
			"content" => $t->draw(),
			"uid" => aw_global_get("uid"),
			"charset" => aw_global_get("charset"),
			"MINIFY_JS_AND_CSS" => minify_js_and_css::parse_admin_header($sf->parse("MINIFY_JS_AND_CSS"))
		));
		die($sf->parse());
	}

	function hours_format($num)
	{
//		$num = number_format($num, 3 , ',', '');
		$num = round($num , 4);
		$xnum = explode("." , $num);

		if(!isset($xnum[1]) or !($xnum[1] > 0))
		{
			$xnum[1] = "00";
		}
		elseif(!($xnum[1] >= 10))
		{
			$xnum[1] = $xnum[1]."0";
		}
		else
		{
			$xnum[1] = substr($xnum[1] , 0 , 4);
		}
		$num = join("," , $xnum);
		return $num;
	}

	function _get_tasks_from_search($r)
	{
		$this->tasksearchstart = microtime();
		$filt = array(
			"class_id" => CL_TASK,
			"site_id" => array(),
			"lang_id" => array(),
			"brother_of" => new obj_predicate_prop("id")
		);

		if ($r["stats_s_worker"] != "")
		{
			// list all persons that match the name, then get ids of all tasks for that person
			$p_list = new object_list(array(
				"class_id" => CL_CRM_PERSON,
				"name" => "%".$r["stats_s_worker"]."%",
				"lang_id" => array(),
				"site_id" => array()
			));
			$c = new connection();
			$tasks = $c->find(array(
				"from" => $p_list->ids(),
				"from.class_id" => CL_CRM_PERSON,
				"type" => "RELTYPE_PERSON_TASK"
			));
			$_ids = array();
			foreach($tasks as $task)
			{
				$_ids[] = $task["to"];
			}
			$filt["oid"] = $_ids;
		}

		if (is_array($r["stats_s_worker_sel"]) && count($r["stats_s_worker_sel"]) > 0)
		{
			$c = new connection();
			$tasks = $c->find(array(
				"from" => $r["stats_s_worker_sel"],
				"from.class_id" => CL_CRM_PERSON,
				"type" => "RELTYPE_PERSON_TASK"
			));
			$_ids = array();
			foreach($tasks as $task)
			{
				$_ids[] = $task["to"];
			}
			$filt["oid"] = $_ids;
		}
		if ($r["stats_s_cust"] != "")
		{
			$filt["CL_TASK.RELTYPE_CUSTOMER.name"] = map("%%%s%%", explode(",", $r["stats_s_cust"]));
		}

		if ($r["stats_s_cust_type"] != "")
		{
			$filt["CL_TASK.RELTYPE_CUSTOMER.class_id"] = $r["stats_s_cust_type"];
		}
		if ($r["stats_s_area"] != "")
		{
			$filt["CL_TASK.project.RELTYPE_TYPE"] = $r["stats_s_area"];
		}

		if ($r["stats_s_proj"] != "")
		{
			$filt["CL_TASK.project.name"] = map("%%%s%%", explode(",", $r["stats_s_proj"]));
		}

		$r["stats_s_from"] = date_edit::get_timestamp($r["stats_s_from"]);
		$r["stats_s_to"] = date_edit::get_timestamp($r["stats_s_to"]);

		if ($r["stats_s_time_sel"] != "")
		{
			classload("core/date/date_calc");
			switch($r["stats_s_time_sel"])
			{
				case "today":
					$r["stats_s_from"] = time() - (date("H")*3600 + date("i")*60 + date("s"));
					$r["stats_s_to"] = time();
					break;

				case "yesterday":
					$r["stats_s_from"] = time() - ((date("H")*3600 + date("i")*60 + date("s")) + 24*3600);
					$r["stats_s_to"] = time() - (date("H")*3600 + date("i")*60 + date("s"));
					break;

				case "cur_week":
					$r["stats_s_from"] = get_week_start();
					$r["stats_s_to"] = time();
					break;

				case "cur_mon":
					$r["stats_s_from"] = get_month_start();
					$r["stats_s_to"] = time();
					break;

				case "last_mon":
					$r["stats_s_from"] = mktime(0,0,0, date("m")-1, 1, date("Y"));
					$r["stats_s_to"] = get_month_start();
					break;
			}
		}
		if($r["stats_s_from"] == -1)$r["stats_s_from"] = 0;//et aegade algusest
		if($r["stats_s_to"] == -1) $r["stats_s_to"] = 991154552400;//suht suva suur number
		$this->_stats_s_from = $r["stats_s_from"];
		$this->_stats_s_to = $r["stats_s_to"];
		if ($r["stats_s_from"] > 1 && $r["stats_s_to"])
		{
		//	$filt["start1"] = new obj_predicate_compare(OBJ_COMP_BETWEEN, $r["stats_s_from"], $r["stats_s_to"]);
			$filt[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"start1" => new obj_predicate_compare(OBJ_COMP_BETWEEN, $r["stats_s_from"], $r["stats_s_to"]),
					"CL_TASK.RELTYPE_ROW.date" => new obj_predicate_compare(OBJ_COMP_BETWEEN, ($r["stats_s_from"] - 1), ($r["stats_s_to"] + 86399))
				)
			));
		}
		else
		if ($r["stats_s_from"] > 1)
		{
			//$filt["start1"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $r["stats_s_from"]);
			$filt[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"start1" => new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $r["stats_s_from"]-1),
					"CL_TASK.RELTYPE_ROW.date" => new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $r["stats_s_from"]-1)
				)
			));
		}
		else
		if ($r["stats_s_to"] > 1)
		{
			//$filt["start1"] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $r["stats_s_to"]);
			$filt[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"start1" => new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, ($r["stats_s_to"]+ 86399)),
					"CL_TASK.RELTYPE_ROW.date" => new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, ($r["stats_s_to"]+86399))
				)
			));
		}
		$this->start_filt = $filt["start1"];

		if ($r["stats_s_state"] != "")
		{
			$filt["is_done"] = array(
				"mask" => OBJ_IS_DONE,
				"flags" => $r["stats_s_state"] == "done" ? OBJ_IS_DONE : 0
			);
		}

		if ($r["stats_s_bill_state"] == -6)
		{
			$filt["CL_TASK.RELTYPE_BILL.on_demand"] = 1;
		}
		elseif ($r["stats_s_bill_state"] > -1 || $r["stats_s_bill_state"] == -5)
		{
			$filt["CL_TASK.RELTYPE_BILL.state"] = $r["stats_s_bill_state"];
		}
		else
		if ($r["stats_s_bill_state"] == -1)
		{
			$filt["CL_TASK.bill_no"] = new obj_predicate_not();
		}
//		else
//		if ($r["stats_s_bill_state"] == -3)
//		{
//			$filt[] = new object_list_filter(array(
//				"logic" => "OR",
//				"conditions" => array(
//					"CL_TASK.bill_no" => new obj_predicate_not(""),
//					"CL_TASK.RELTYPE_BILL.state" => -5,
//				)
//			));
//		}
		else
		if ($r["stats_s_bill_state"] == -4)
		{
			$rv = oql::compile_query("
			SELECT
				id
			FROM
				CL_TASK
			WHERE
				((CL_TASK.RELTYPE_BILL.state IN (1 , 6) AND CL_TASK.RELTYPE_BILL.bill_date  + CL_TASK.RELTYPE_BILL.bill_due_date_days * 84600)) < ".time());
			$rv2 = oql::execute_query($rv, array("id"));

			if(!is_array($filt["oid"]))
			{
				$filt["oid"] = array_keys($rv2);
			}
			else
			{
				$filt["oid"] = $filt["oid"] + array_keys($rv2);

			}
//			$filt["CL_TASK.RELTYPE_BILL"] = array_keys($rv2);
//			$filt[] = new object_list_filter(array(
//				"logic" => "OR",
//				"conditions" => array(
//					"CL_TASK.RELTYPE_BILL.state" => 1,
//					"CL_TASK.RELTYPE_BILL.state" => 6,
//				)
//			));
//			$filt["CL_TASK.RELTYPE_BILL.state"] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, 1);
		}
		else
		if ($r["stats_s_bill_state"] == -3)
		{
			$filt["CL_TASK.bill_no"] = "";
		}

		if (is_array($filt["oid"]) && count($filt["oid"])== 0)
		{
			return new object_list();
		}
		$ol = new object_list($filt);
		return $ol;
	}


	function _get_bugs_from_search($r)
	{
		enter_function("stats::bug_search");
		$this->bugsearchstart = microtime();
		$filt = array(
			"class_id" => CL_BUG,
			"site_id" => array(),
			"lang_id" => array(),
			"brother_of" => new obj_predicate_prop("id"),
		);

		$bc_filt = array(
			"class_id" => array(CL_TASK_ROW),
			"lang_id" => array(),
			"site_id" => array()
		);

		if(is_oid($r["project_mgr"]))
		{
			$filt["CL_BUG.RELTYPE_PROJECT.proj_mgr"] = $r["project_mgr"];
		}

		if($r["stats_s_bill_state"] == -3)
		{
			$bc_filt["bill_id"] = new obj_predicate_compare(OBJ_COMP_EQUAL, '');
			$filt["CL_BUG.RELTYPE_COMMENT.bill_id"] =  new obj_predicate_compare(OBJ_COMP_EQUAL, '');
		}

		if ($r["stats_s_worker"] != "")
		{
			// list all persons that match the name, then get ids of all tasks for that person
			$p_list = new object_list(array(
				"class_id" => CL_CRM_PERSON,
				"name" => "%".$r["stats_s_worker"]."%",
				"lang_id" => array(),
				"site_id" => array()
			));
			$c = new connection();
			$bugs = $c->find(array(
				"to" => $p_list->ids(),
				"to.class_id" => CL_CRM_PERSON,
				"type" => "RELTYPE_MONITOR"
			));
			$_ids = array();
			foreach($bugs as $bug)
			{
				$_ids[] = $bug["from"];
			}
			$filt["oid"] = $_ids;
		}

		if (is_array($r["stats_s_worker_sel"]) && count($r["stats_s_worker_sel"]) > 0)
		{
			$c = new connection();
			$bugs = $c->find(array(
				"to" => $r["stats_s_worker_sel"],
				"to.class_id" => CL_CRM_PERSON,
				"type" => "1"
			));
			$_ids = array();
			foreach($bugs as $bug)
			{
				$_ids[] = $bug["from"];
			}
			$filt["oid"] = $_ids;
		}

		if ($r["stats_s_cust"] != "")
		{
			$filt["CL_BUG.customer.name"] = map("%%%s%%", explode(",", $r["stats_s_cust"]));
			$bc_filt["CL_TASK_ROW.RELTYPE_CUSTOMER.name"] = map("%%%s%%", explode(",", $r["stats_s_cust"]));
		}

		if ($r["stats_s_cust_type"] != "")
		{
			$filt["CL_BUG.customer.class_id"] = $r["stats_s_cust_type"];
		}
		if ($r["stats_s_area"] != "")
		{
			$filt["CL_BUG.project.RELTYPE_TYPE"] = $r["stats_s_area"];
		}

		if ($r["stats_s_proj"] != "")
		{
			$filt["CL_BUG.project.name"] = map("%%%s%%", explode(",", $r["stats_s_proj"]));
		}

		if($r["between"])
		{
			$bc_filt["date"] =  $r["between"];
			$filt["CL_BUG.RELTYPE_COMMENT.date"] = $r["between"];
		}
		else
		{

			$r["stats_s_from"] = date_edit::get_timestamp($r["stats_s_from"]);
			$r["stats_s_to"] = date_edit::get_timestamp($r["stats_s_to"]);

			if ($r["stats_s_time_sel"] != "")
			{
				classload("core/date/date_calc");
				switch($r["stats_s_time_sel"])
				{
					case "today":
						$r["stats_s_from"] = time() - (date("H")*3600 + date("i")*60 + date("s"));
						$r["stats_s_to"] = time();
						break;

					case "yesterday":
						$r["stats_s_from"] = time() - ((date("H")*3600 + date("i")*60 + date("s")) + 24*3600);
						$r["stats_s_to"] = time() - (date("H")*3600 + date("i")*60 + date("s"));
						break;

					case "cur_week":
						$r["stats_s_from"] = get_week_start();
						$r["stats_s_to"] = time();
						break;

					case "cur_mon":
						$r["stats_s_from"] = get_month_start();
						$r["stats_s_to"] = time();
						break;

					case "last_mon":
						$r["stats_s_from"] = mktime(0,0,0, date("m")-1, 1, date("Y"));
						$r["stats_s_to"] = get_month_start();
						break;
				}
			}
			if($r["stats_s_from"] == -1)
			{
				$r["stats_s_from"] = 0;//et aegade algusest
			}
			if($r["stats_s_to"] > 0)
			{
				$this->_stats_s_to = $r["stats_s_to"];
			}
			$this->_stats_s_from = $r["stats_s_from"];

			if ($r["stats_s_from"] > 0 && $r["stats_s_to"] > 0)
			{
				$bc_filt["date"] = new obj_predicate_compare(OBJ_COMP_BETWEEN, ($r["stats_s_from"] - 1), ($r["stats_s_to"] + 86400));
				$filt["CL_BUG.RELTYPE_COMMENT.date"] = new obj_predicate_compare(OBJ_COMP_BETWEEN, ($r["stats_s_from"] - 1), ($r["stats_s_to"] + 86400));
			}
			elseif ($r["stats_s_from"] > 0)
			{
				$bc_filt["date"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $r["stats_s_from"]);
				$filt["CL_BUG.RELTYPE_COMMENT.date"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $r["stats_s_from"]);
			}
			elseif ($r["stats_s_to"] > 0)
			{
				$bc_filt["date"] = new obj_predicate_compare(OBJ_COMP_LESS, ($r["stats_s_to"] + 86400));
				$filt["CL_BUG.RELTYPE_COMMENT.date"] = new obj_predicate_compare(OBJ_COMP_LESS, ($r["stats_s_to"] + 86400));
			}
		}
		// gather all bug comments in the correct range, so we can count the time only spent in the correct range
//		$bc_ol = new object_list($bc_filt);
		$this->bug_comments = array();

		$rows_data_list = new object_data_list(
			$bc_filt,
			array(CL_TASK_ROW => array(
				"task", "date"
			))
		);
		foreach($rows_data_list->arr() as $bcs)
		{
			$this->bug_comments[$bcs["task"]][] = array(
				"id" => $bcs["oid"],
				"date" => $bcs["date"],
				"time_real" => $bcs["time_real"],
				"time_customer" => $bcs["time_to_cust"]
			);
		}
		$this->start_filt = $filt["start1"];

		if (is_array($filt["oid"]) && count($filt["oid"])== 0)
		{
			exit_function("stats::bug_search");
			return new object_list();
		}
 		$ol = new object_list($filt);
		if($_GET["debug"])
		{
			list($usec, $sec) = explode(" ", $this->bugsearchstart);
			list($usec2, $sec2) = explode(" ", microtime());
			arr("bugide otsing: " . ($sec2 - $sec).",".($usec2 - $usec));
		}
		exit_function("stats::bug_search");
		return $ol;
	}


	function _get_pers_stats($slaves, $tasks, $req)
	{
		// get paricipants
		$c = new connection();
		$ps = $c->find(array(
			"from" => $slaves,
			"from.class_id" => CL_CRM_PERSON,
			"type" => "RELTYPE_PERSON_TASK",
			"to" => $tasks->ids()
		));

		$task2impl = array();
		foreach($ps as $con)
		{
			$task2impl[$con["to"]][] = $con["from"];
		}

		$d = array();
		$task_i = get_instance(CL_TASK);
		foreach($tasks->arr() as $task)
		{
			// if task jas rows, then get data from those
			$rows = $task_i->get_task_bill_rows($task, $req["stats_s_only_billable"]);
			foreach($rows as $row)
			{
				$awa = new aw_array($row["impl"]);
				foreach($awa->get() as $p)
				{
					if (!$this->_in_time($row["date"]))
					{
						continue;
					}
					$diff = $row["amt"] - $row["amt_real"];

					$d[$p]["pt"] += $row["amt_guess"];
					$d[$p]["tt"] += $row["amt_real"];
					$d[$p]["kt"] += $row["amt"];
					$d[$p]["sum_plus"] += $diff > 0 ? $diff : 0;
					$d[$p]["sum_minus"] += $diff < 0 ? -$diff : 0;
					$d[$p]["sum"] += $row["sum"];
				}
			}
		}

		foreach($slaves as $s_id)
		{
			$d[$s_id]["person"] = html::obj_change_url($s_id);
		}

		return $d;
	}

	function _get_cust_stats($slaves, $tasks, $req)
	{
		$d = array();
		$task_i = get_instance(CL_TASK);
		foreach($tasks->arr() as $task)
		{
			$t_id = $task->prop("customer");
			// if task jas rows, then get data from those
			$rows = $task_i->get_task_bill_rows($task, $req["stats_s_only_billable"]);
			foreach($rows as $row)
			{
				if ($row["is_oe"] == 1)
				{
					$d[$t_id]["otherexp"] += $row["sum"];
					continue;
				}
				if (!$this->_in_time($row["date"]))
				{
					continue;
				}
				$diff = $row["amt"] - $row["amt_real"];
				$d[$t_id]["pt"] += $row["amt_guess"];
				$d[$t_id]["tt"] += $row["amt_real"];
				$d[$t_id]["kt"] += $row["amt"];
				$d[$t_id]["sum_plus"] += $diff > 0 ? $diff : 0;
				$d[$t_id]["sum_minus"] += $diff < 0 ? -$diff : 0;
				$d[$t_id]["sum"] += $row["sum"];
			}
		}

		foreach($d as $id => $inf)
		{
			$d[$id]["cust"] = html::obj_change_url($id);
		}
		return $d;
	}

	function _get_proj_stats($slaves, $tasks, $req)
	{
		$d = array();
		$task_i = get_instance(CL_TASK);
		foreach($tasks->arr() as $task)
		{
			$t_id = $task->prop("project");
			$rows = $task_i->get_task_bill_rows($task, $req["stats_s_only_billable"]);

			foreach($rows as $row)
			{
				if ($row["is_oe"] == 1)
				{
					$d[$t_id]["otherexp"] += $row["sum"];
					continue;
				}

				if (!$this->_in_time($row["date"]))
				{
					continue;
				}
				$diff = $row["amt"] - $row["amt_real"];
				$d[$t_id]["pt"] += $row["amt_guess"];
				$d[$t_id]["tt"] += $row["amt_real"];
				$d[$t_id]["kt"] += $row["amt"];
				$d[$t_id]["sum_plus"] += $diff > 0 ? $diff : 0;
				$d[$t_id]["sum_minus"] += $diff < 0 ? -$diff : 0;
				$d[$t_id]["sum"] += $row["sum"];
			}
		}

		foreach($d as $id => $inf)
		{
			$p = obj($id);
			$d[$id]["proj"] = html::obj_change_url($id);
			$ord = $p->prop("orderer");
			if (is_array($ord))
			{
				$ord = reset($ord);
			}
			$d[$id]["cust"] = html::obj_change_url($ord);
		}
		return $d;
	}

	function _init_stats_rows_t(&$t, $req = array())
	{
		$t->define_field(array(
			"name" => "date",
			"caption" => t("Kuup&auml;ev"),
			"sortable" => 1,
			"numeric" => 1,
			"type" => "time",
			"format" => "d.m.Y",
			"align" => "center"
		));
		if(!$req["stats_s_group_by_client"])
		{
			$t->define_field(array(
				"name" => "cust",
				"caption" => t("Klient"),
				"align" => "center",
				"sortable" => 1,
				"callback" =>  array(&$this, "__cust_format"),
				"callb_pass_row" => true,
			));
		}
		if(!$req["stats_s_group_by_project"])
		{
			$t->define_field(array(
				"name" => "proj",
				"caption" => t("Projekt"),
				"align" => "center",
				"sortable" => 1,
				"callback" =>  array(&$this, "__proj_format"),
				"callb_pass_row" => true,
			));
		}
		if(!$req["stats_s_group_by_task"])
		{
			$t->define_field(array(
				"name" => "task",
				"caption" => t("Toimetus"),
				"align" => "center",
				"sortable" => 1,
				"callback" =>  array(&$this, "__task_format"),
				"callb_pass_row" => true,
			));
		}
		$t->define_field(array(
			"name" => "did",
			"caption" => t("Teostaja"),
			"align" => "center",
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "content",
			"caption" => t("Rea sisu"),
			"align" => "center",
			"callback" =>  array(&$this, "__content_format"),
			"callb_pass_row" => true,
		));
		$t->define_field(array(
			"name" => "length",
			"caption" => t("Kestvus"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "length_cus",
			"caption" => t("Kestvus kliendile"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "state",
			"caption" => t("Staatus"),
			"align" => "center",
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa"),
			"align" => "center",
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "bill_nr",
			"caption" => t("Arve nr."),
			"align" => "center",
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "bill_state",
			"caption" => t("Arve staatus"),
			"align" => "center",
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "check",
			"caption" => t("<a href='#' onClick='aw_sel_chb(document.changeform,\"sel\")'>Vali</a>"),
			"align" => "center",
		));
	}

	function __content_format($val)
	{
		return $val["content_val"];
	}

	function __cust_format($val)
	{
		return $val["cust_val"];
	}

	function __proj_format($val)
	{
		return $val["proj_val"];
	}

	function __task_format($val)
	{
		return $val["task_val"];
	}

	function _get_row_stats($slaves, $tasks, $req, $vcl)
	{
		$company_curr = $this->get_company_currency();
		$tb =  new toolbar;
		$co_inst = get_instance("crm/crm_company");

		$u = new user();
		$co = $u->get_current_company();

		$_SESSION["create_bill_ru"] = get_ru();
		$tb->add_button(array(
			"name" => "creab",
			"img" => "new.gif",
			"tooltip" => t("Loo arve"),
//			"action" => "create_bill"
			"url" => "javascript:document.changeform.submit();",
		));
		$tb->add_button(array(
			"name" => "save_rows",
			"img" => "save.gif",
			"tooltip" => t("Salvesta"),
			"action" => "save_rows",
		));

		$t = new vcl_table;
		$this->_init_stats_rows_t($t,$req);
		$d = array();
		$task_i = get_instance(CL_TASK);
		$row_inst = get_instance(CL_TASK_ROW);
		$bill_inst = get_instance(CL_CRM_BILL);
		$time_stats = $this->make_time_stats($req);

		if($req["stats_s_group_by_client"] || $req["stats_s_group_by_project"] || $req["stats_s_group_by_task"])
		{
			$grouping = true;
		}
		$filter = array(
			"class_id" => CL_TASK_ROW,
			"lang_id" => array(),
			"site_id" => array(),
			"impl" => $slaves,
//			"date" => new obj_predicate_compare(OBJ_COMP_BETWEEN, $time_stats["from"]-1, ($time_stats["to"] + 86399)),
		);

		$filter[] = new object_list_filter(array(
			"logic" => "OR",
			"conditions" => array(
				"date" => new obj_predicate_compare(OBJ_COMP_BETWEEN, $time_stats["from"]-1, ($time_stats["to"] + 86399)),
				new object_list_filter(array(
					"logic" => "AND",
					"conditions" => array(
						"date" => new obj_predicate_compare(OBJ_COMP_LESS, 1),
						"created" => new obj_predicate_compare(OBJ_COMP_BETWEEN, $time_stats["from"]-1, ($time_stats["to"] + 86399)),
					)
				))
			)
		));

		if($req["stats_s_worker"])
		{
			$filter["CL_TASK_ROW.impl(CL_CRM_PERSON).name"] = "%".$req["stats_s_worker"]."%";
		}

		//yritab ikka enne miskid valedele klientidele l2inud v2lja saada... muidu paljuks l2heb
		if($req["stats_s_cust"])
		{
			$row_ids = array();
			$task_filter = array(
				"class_id" => CL_TASK,
				"lang_id" => array(),
				"site_id" => array(),
				"CL_TASK.RELTYPE_CUSTOMER.name" => "%".$req["stats_s_cust"]."%",
			);
			$tol = new object_list($task_filter);
			foreach($tol->arr() as $task_object)
			{
				foreach($task_object->connections_from(array("type" => "RELTYPE_ROW")) as $conn)
				{
					$row_ids[$conn->prop("to")] = $conn->prop("to");
				}
			}
			if(sizeof($row_ids))
			{
				$filter["oid"] = $row_ids;
			}
			else
			{
				$filter["oid"] = 0;
			}
		}
ini_set("memory_limit", "1500M");
//die(dbg::dump($filter));
		$ol = new object_list($filter);
		$row2task = array();
		$l_cus = 0;

		$c = new connection();
		foreach($c->find(array("to" => $ol->ids(), "from.class_id" => CL_TASK, "type" => "RELTYPE_ROW")) as $c)
		{
			if ($req["stats_s_cust"] != "")
			{
				$task = obj($c["from"]);
				if (strpos(mb_strtolower($task->prop("customer.name"), aw_global_get("charset")), mb_strtolower($req["stats_s_cust"], aw_global_get("charset"))) === false)
				{
					continue;
				}
			}
			$row2task[$c["to"]] = $c["from"];
		}
		$bi = get_instance(CL_BUG);

		$this->req = $arr["request"];
		$this->r2t = $row2task;

		if($grouping)
		{
			$ol->sort_by_cb(array(&$this, "__row_sorter"));
			$last_cust = $last_task = $last_proj = 0;
		}
		else
		{
//			$ol->sort_by(array("prop" => "date","order" => "asc"));
		}
		foreach($ol->arr() as $o)
		{
			$impl = $check = $bs = $bn = "";
			$b = null;
			$agreement = array();

			$from = $o->connections_from(array(
				"type" => "RELTYPE_IMPL"
			));
			if(is_oid($o->prop("impl")))
			{
				$impl = html::href(array(
					"url" => "javascript:gt_change(".$o->prop("impl").");" ,
					"caption" => $o->prop("impl.name")
				)).'<br>';
			}

			if ($this->can("view", $o->prop("bill_id")))
			{
				$b = obj($o->prop("bill_id"));
				//$bs = sprintf(t("Arve nr %s"), $b->prop("bill_no"));
				$bn = $b->prop("bill_no");
				$bs = $bill_inst->states[$b->prop("state")];
				$agreement = $b->meta("agreement_price");
			//	$bs = html::obj_change_url($o->prop("bill_id"));
			}
			elseif ($o->prop("on_bill"))
			{
				$bs = t("Arvele");
				$check = html::checkbox(array(
					"name" => "sel[]",
					"value" => $o->id()
				));
			}
			else
			{
				$bs = t("Arve puudub");
			}
			//kui pole toimetust, siis on suht jama selle reaga
			$task = obj($row2task[$o->id()]);
			if (!is_oid($task->id()))
			{
				continue;
			}

			//filtreerib osa v2lja vastavalt otsingu parameetritele:
			//klient
			$customer = $task->prop("customer");
			if($req["stats_s_cust_type"] || $req["stats_s_cust"])
			{
				if(is_oid($customer) && $this->can("view" , $customer))
				{
					$customer = obj($customer);
				}
				else continue;
				if($req["stats_s_cust_type"] && $customer->class_id() != $req["stats_s_cust_type"])
				{
					continue;
				}
				if($req["stats_s_cust"] && !(substr_count($customer->name() , $req["stats_s_cust"]) > 0))
				{
					continue;
				}
			}
			//projekt
			$project = $task->prop("project");
			if($req["stats_s_proj"])
			{
				if(is_oid($project) && $this->can("view" , $project)) $project = obj($project);
				else continue;
				if($req["stats_s_proj"] && !(substr_count($project->name() , $req["stats_s_proj"]) > 0)) continue;
			}
			//staatus
			if($req["stats_s_state"] == "done" && $task->prop("is_done") != 8) continue;
			if($req["stats_s_state"] == "not_done" && $task->prop("is_done") == 8) continue;
			//arve

			if($req["stats_s_bill_state"] == -3 && ($this->can("view", $o->prop("bill_id"))))
			{
				continue;
			}
			if($req["stats_s_bill_state"] > -2 || $req["stats_s_bill_state"] == -4 || $req["stats_s_bill_state"] == -5 || $req["stats_s_bill_state"] == -6)
			{
				if(is_oid($o->prop("bill_id")) && $this->can("view" ,$o->prop("bill_id")))
				{
					$bill = obj($o->prop("bill_id"));
				}
				else continue;

			}
			if($req["stats_s_bill_state"] == 0 && ($bill->prop("state") || !$this->can("view" ,$o->prop("bill_id"))))
			{
				continue;
			}
			if(($req["stats_s_bill_state"] > 0 || $req["stats_s_bill_state"] == -5) && ($bill->prop("state") != $req["stats_s_bill_state"]))
			{
				continue;
			}

			if(($req["stats_s_bill_state"] == -6) && (!$bill->prop("on_demand")))
			{
				continue;
			}

			if ($req["stats_s_bill_state"] == -4)
			{
				if(!(($bill->prop("state") == 1) || ($bill->prop("state") == 6) &&  ($bill->prop("bill_date") + $bill->prop("bill_due_date_days")*84600 > time())))				{
					continue;
				}
			}
			if($req["stats_s_bill_state"] == 0 && ($bill->prop("state") || !$this->can("view" ,$o->prop("bill_id"))))
			{
				continue;
			}
			if($req["stats_s_bill_state"] > 0 && ($bill->prop("state") != $req["stats_s_bill_state"]))
			{
				continue;
			}

			//aeg

			//if(($time_stats["to"] + 86399) < $o->prop("date") || $o->prop("date") < $time_stats["from"]) continue;
			$ttc = $o->prop("time_to_cust");
			$time_real = $o->prop("time_real");
			$sum = str_replace(",", ".", $ttc);
			$sum *= str_replace(",", ".", $task->prop("hr_price"));

			//kui on kokkuleppehind kas arvel, v6i kui arvet ei ole, siis toimetusel... tuleb v2he arvutada
			if(is_object($b))
			{
				$br_ol = new object_list(array(
					"class_id" => CL_CRM_BILL_ROW,
					"lang_id" => array(),
					"site_id" => array(),
					"CL_CRM_BILL_ROW.RELTYPE_TASK_ROW" => $o->id(),
				));
				$br = reset($br_ol->arr());
				if((sizeof($agreement) && ($agreement[0]["price"] > 0)) || (!is_object($b) && $task->prop("deal_price")))
				{
					$sum = $row_inst->get_row_ageement_price($o);
				}
				elseif(is_object($br) && !$o->meta("parent_row"))
				{
					$ttc = $br->prop("amt");
					foreach($br->connections_from(array("type" => "RELTYPE_TASK_ROW")) as $c)
					{
						$other_tr = $c->to();
						if($other_tr->meta("parent_row") == $o->id())
						{
							$time_real += $other_tr->prop("time_real");
						}
					}
					$sum = $br->get_sum();//if(aw_global_get("uid") == "Teddi.Rull") arr($sum);
				}
				else
				{
					continue;
				}
			}

/*

			//kokkuleppehinna j2rgi arvest
			if(is_object($b) && sizeof($b->meta("agreement_price")))
			{
				$agreement = $b->meta("agreement_price");
				$agreement_hr_price = $agreement[0]["sum"]/$agreement[0]["amt"];
				$sum = $agreement_hr_price * $o->prop("time_real");
			}//kokkuleppehind toimetusest
			elseif($task->prop("deal_price"))
			{
				$sum = $task_i->get_row_ageement_price($o);
			}

	*/		$sum = $this->convert_to_company_currency(array(
				"sum" => $sum,
				"o" => $task,
				"company_curr" =>  $company_curr
			));

			//teeb vahepealkirjad projektidest, toimetustest ja klientidest
			if($last_cust != $task->prop("customer") && $req["stats_s_group_by_client"])
			{
				if($last_cust != 0)
				{
					$t->define_data(array());
				}
				if($this->can("view" , $task->prop("customer")))
				{
					$cust = obj($task->prop("customer"));
					$t->define_data(array("content_val" => "<h2>".$cust->name(). '</h2>' ));
				}
				$last_cust = $task->prop("customer");
			}

			if($last_proj != $task->prop("project") && $req["stats_s_group_by_project"])
			{
				if($this->can("view" , $task->prop("project")))
				{
					$proj = obj($task->prop("project"));
					$t->define_data(array("content_val" => "<h3>".$proj->name(). '</h3>'));
				}
				$last_proj = $task->prop("project");
			}

			if($last_task != $task->id() && $req["stats_s_group_by_task"])
			{
				$t->define_data(array("content_val" => "<h4>".$task->name().'</h4>'));
				$last_task = $task->id();
			}
					//if(aw_global_get("uid") == "Teddi.Rull") arr(($o->prop("date") > 1)?$o->prop("date"):$o->created());
			$lcust = $this->hours_format($o->prop("time_to_cust"));
			$t->define_data(array(
				"content_val" => $bi->_split_long_words($o->prop("content")),
				"date" => ($o->prop("date") > 1)?$o->prop("date"):$o->created(),
				"cust" => $task->prop("customer.name"),
				"cust_val" => $task->prop("customer") ? html::href(array(
					"url" => "javascript:gt_change(".$task->prop("customer").");" ,
					"caption" => $task->prop("customer.name"),
				)) : "".(($o->prop("date") < 1)?"*":""),

				"proj" => $task->prop("project.name"),//html::obj_change_url($task->prop("project")),
				"proj_val" => $task->prop("project") ? html::href(array(
					"url" => "javascript:gt_change(".$task->prop("project").");" ,
					"caption" => $task->prop("project.name"),
				)) : "",

				"task" => $task->name(),//html::obj_change_url($task),
				"task_val" => html::href(array(
					"url" => "javascript:gt_change(".$task->id().");" ,
					"caption" => $task->name(),
				)),
				"length_cus" => (!is_oid($bn))?html::textbox(array(
						"name" => "rows[".$o->id()."][time_to_cust]",
						"value" => $lcust,
						"size" => 4,
				)).html::hidden(array(
					"name" => "rows[".$o->id()."][time_to_cust_real]",
					"value" => $lcust,
				)):$this->hours_format($ttc),
				"content" => $o->prop("ord"),
//				"content" => $bi->_split_long_words($o->prop("content")),
				"did" => $impl,
				"length" => $this->hours_format($time_real),//number_format($time_real, 3, ',', ''),
				"state" => $o->prop("done") ? t("Tehtud") : t("Tegemata"),
				"bill_state" => $bs,
				"sum" => number_format($sum, 2, ',', ''),
				"check" => $check,
				"bill_nr" => $bn,
			));
			$l_sum += $time_real;
			$s_sum += $sum;
			$l_cus += $ttc;
		}

		if(!$grouping)
		{
			if($_GET["sortby"])
			{
				$t->sort_by(array(
					"field" => $_GET["sortby"],
					"order" => $_GET["sort_order"]
				));
			}
			else
			{
				$t->sort_by(array(
					"field" => array("date" , "content"),
					"order" => array("asc", "asc"),
				));
			}
		}
		$t->set_sortable(false);
		if($req["return_table"])
		{
			return;
		}
		$sf = new aw_template;
		$sf->db_init();
		$sf->tpl_init("automatweb");
		$sf->read_template("index.tpl");

		//summa rida
		$t->define_data(array(
				"content" => '<b>'.t("Summa:").'</b>',
				"length" => $this->hours_format($l_sum),//number_format($l_sum, 3, ',', '') ,
				"sum" => number_format($s_sum, 2, ',', ''),
				"length_cus" => $this->hours_format($l_cus),//number_format($l_cus, 3, ',', ''),
			));
		$sf->vars(array(
			"content" => $t->draw(),
			"uid" => aw_global_get("uid"),
			"charset" => aw_global_get("charset"),
			"MINIFY_JS_AND_CSS" => $sf->parse("MINIFY_JS_AND_CSS")
		));

//		$action = $co_inst->mk_my_orb("create_bill", array("id" => $co));
//		$form_start = "<form action='".$action."' method='POST' name='changeform' enctype='multipart/form-data' >
		$form_start = "<form action='orb.aw' method='POST' name='changeform' enctype='multipart/form-data' >
			<input type='hidden' NAME='MAX_FILE_SIZE' VALUE='100000000'>
			<input type='hidden' NAME='class' VALUE='crm_company'>
			<input type='hidden' NAME='action' VALUE='create_bill'>
			<input type='hidden' NAME='id' VALUE='".$co."'>
			<input type='hidden' NAME='post_ru' VALUE='".post_ru()."'>"
			;
		$form_end = "</form>";
		die($tb->get_toolbar().$form_start.$sf->parse().$form_end.$this->submit_js());
		return ($tb->get_toolbar().$t->draw());
	}

	private function _init_bill_person_rows_t($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Esitatud"),
			"sortable" => 1,
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "payments",
			"caption" => t("Laekunud"),
			"sortable" => 1,
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "pers",
			"caption" => "%",
			"sortable" => 1,
			"numeric" => 1
		));
	}

	private function _init_bill_rows_t($t)
	{
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));

		$t->define_field(array(
			"name" => "date",
			"caption" => t("Kuup&auml;ev"),
			"sortable" => 1,
			"numeric" => 1,
			"type" => "time",
			"format" => "d.m.Y",
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "bill_no",
			"caption" => t("Number"),
			"sortable" => 1,
			"numeric" => 1
		));

		$t->define_field(array(
			"name" => "cust",
			"caption" => t("Klient"),
			"align" => "left",
			"sortable" => 1,
		));

		$t->define_field(array(
			"name" => "state",
			"caption" => t("Staatus"),
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "bill_due_date",
			"caption" => t("Makset&auml;htaeg"),
			"type" => "time",
			"format" => "d.m.Y",
			"numeric" => 1,
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa"),
			"sortable" => 1,
			"numeric" => 1,
			"align" => "right"
		));

		$t->define_field(array(
			"name" => "payment_date",
			"caption" => t("Laekumiskuup&auml;ev"),
			"type" => "time",
			"format" => "d.m.Y",
			"numeric" => 1,
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "paid",
			"caption" => t("Laekunud"),
			"sortable" => 1,
			"numeric" => 1,
			"align" => "right"
		));

		$t->define_field(array(
			"name" => "late",
			"caption" => t("Hilinenud p&auml;evi"),
			"sortable" => 1,
			"numeric" => 1,
			"align" => "right"
		));
	}

	public function search_bills($r)
	{
		$filter = array(
			"class_id" => CL_CRM_BILL
		);

		if(!empty($r["between"]))
		{
			$filter["CL_CRM_BILL.bill_date"] = $r["between"];
		}
		else
		{
			if(isset($r["stats_s_from"]) && is_array($r["stats_s_from"]) || isset($r["stats_s_to"]) && is_array($r["stats_s_to"]))
			{
				$r["stats_s_from"] = date_edit::get_timestamp($r["stats_s_from"]);
				$r["stats_s_to"] = date_edit::get_timestamp($r["stats_s_to"]);
			}

			if (!empty($r["stats_s_time_sel"]))
			{
				classload("core/date/date_calc");
				switch($r["stats_s_time_sel"])
				{
					case "today":
						$r["stats_s_from"] = time() - (date("H")*3600 + date("i")*60 + date("s"));
						$r["stats_s_to"] = time();
						break;

					case "yesterday":
						$r["stats_s_from"] = time() - ((date("H")*3600 + date("i")*60 + date("s")) + 24*3600);
						$r["stats_s_to"] = time() - (date("H")*3600 + date("i")*60 + date("s"));
						break;

					case "cur_week":
						$r["stats_s_from"] = get_week_start();
						$r["stats_s_to"] = time();
						break;

					case "cur_mon":
						$r["stats_s_from"] = get_month_start();
						$r["stats_s_to"] = time();
						break;

					case "last_mon":
						$r["stats_s_from"] = mktime(0,0,0, date("m")-1, 1, date("Y"));
						$r["stats_s_to"] = get_month_start();
						break;
				}
			}

			if(isset($r["stats_s_from"]) && $r["stats_s_from"] == -1)
			{
				$r["stats_s_from"] = 0;//et aegade algusest
			}

			if(isset($r["stats_s_to"]) && $r["stats_s_to"] == -1)
			{
				$r["stats_s_to"] = 991154552400;//suht suva suur number
			}

			if (isset($r["stats_s_from"]) && $r["stats_s_from"] > 1 && !empty($r["stats_s_to"]))
			{
				$filter["CL_CRM_BILL.bill_date"] = new obj_predicate_compare(OBJ_COMP_BETWEEN, ($r["stats_s_from"] - 1), ($r["stats_s_to"] + 86399));
			}
			elseif (isset($r["stats_s_from"]) && $r["stats_s_from"] > 1)
			{
				$filter["CL_CRM_BILL.bill_date"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $r["stats_s_from"]-1);
			}
			elseif (!empty($r["stats_s_to"]))
			{
				$filter["CL_CRM_BILL.bill_date"] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, ($r["stats_s_to"]+ 86399));
			}
		}

		if (!empty($r["stats_s_cust"]))
		{
			$filter[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_CRM_BILL.customer.name" => "%".$r["stats_s_cust"]."%",
					"CL_CRM_BILL.customer_name" => "%".$r["stats_s_cust"]."%",
				)
			));
		}

		if (!empty($r["stats_s_cust_type"]))
		{
			$filter["CL_CRM_BILL.customer.class_id"] = $r["stats_s_cust_type"];
		}

		if (!empty($r["stats_s_proj"]))
		{
			$filter["CL_CRM_BILL.RELTYPE_TASK.project.name"] = "%".$r["stats_s_proj"]."%";
		}

		if (!empty($r["stats_s_state"]))
		{
			if($r["stats_s_state"] === "done")
			{
				$filter["CL_CRM_BILL.RELTYPE_TASK.is_done"] = 1;
			}
			if($r["stats_s_state"] === "not_done")
			{
				$filter["CL_CRM_BILL.RELTYPE_TASK.is_done"] = 0;
			}
		}

		//valdkond
		if (!empty($r["stats_s_area"]))
		{
			$filter["CL_CRM_BILL.RELTYPE_TASK.project.RELTYPE_TYPE"] = $r["stats_s_area"];
		}

		//arve staatus
		if(!empty($r["stats_s_bill_state"]) && ($r["stats_s_bill_state"] >= 0 || $r["stats_s_bill_state"] == -5))
		{
			$filter["CL_CRM_BILL.state"] = $r["stats_s_bill_state"];
		}

		if(isset($r["stats_s_bill_state"]) && $r["stats_s_bill_state"] == -6)//sisse n6udmisel
		{
			$filter["CL_CRM_BILL.on_demand"] = 1;
		}

		if (isset($r["stats_s_bill_state"]) && $r["stats_s_bill_state"] == -4)//yle t2htaja...
		{
			$filter[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					new object_list_filter(array(
						"logic" => "AND",
						"conditions" => array(
							"CL_CRM_BILL.bill_due_date" => new obj_predicate_compare(OBJ_COMP_LESS, time()),
							"CL_CRM_BILL.state" => 1,
						)
					)),
					new object_list_filter(array(
						"logic" => "AND",
						"conditions" => array(
							"CL_CRM_BILL.bill_due_date" => new obj_predicate_compare(OBJ_COMP_LESS, time()),
							"CL_CRM_BILL.state" => 6,
						)
					)),
				)
			));
		}

		//t88taja
		if(isset($r["stats_s_worker_sel"]) and is_array($r["stats_s_worker_sel"]) or !empty($r["stats_s_worker"]))//kliendihalduri j2rgi
		{
			$pf = array("class_id" => CL_CRM_PERSON);

			if(!empty($r["stats_s_worker"]))
			{
				$pf["name"] = "%".$r["stats_s_worker"]."%";
			}

			if(isset($r["stats_s_worker_sel"]) and is_array($r["stats_s_worker_sel"]))
			{
				$pf["oid"] = $r["stats_s_worker_sel"];
			}

			$persons = new object_list($pf);


			$relist = new object_list(array(
				"class_id" => CL_CRM_COMPANY_ROLE_ENTRY,
				"CL_CRM_COMPANY_ROLE_ENTRY.person" => $persons->ids()
			));
			$relist3 = new object_list(array(
				"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
				"CL_CRM_COMPANY_CUSTOMER_DATA.client_manager" => $persons->ids()
			));
			$relist2 = new object_list(array(
				"class_id" => CL_CRM_COMPANY,
				"CL_CRM_COMPANY.client_manager" => $persons->ids()
			));
			$relist -> add($relist3);
			$rs = array();
			foreach($relist->arr() as $o)
			{
				$rs[] = $o->prop("buyer");
			}
			$rs = $rs + $relist2->ids();
			$ft = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
//						"CL_CRM_BILL.customer(CL_CRM_COMPANY).client_manager.name" => map("%%%s%%", explode(",", $filter["client_mgr"])),
					"CL_CRM_BILL.customer" => $rs,
					"CL_CRM_BILL.customer(CL_CRM_PERSON).client_manager" => $persons->ids(),
				)
			));
			$filter[] = $ft;
		}
		return new object_list($filter);
	}

	private function _get_bills_stats($slaves, $tasks, $req, $t = null)
	{
		if(!$t)
		{
			$t = new vcl_table;
		}
		$this->_init_bill_rows_t($t);
		$bills = $this->search_bills($req);

		$company_curr = $this->get_company_currency();
		$bill_inst = get_instance(CL_CRM_BILL);
		$person_payments = array();
		$person_sum = array();
		$curr_sum = array();
		$company_sum = 0;
		$paid_company_sum = 0;
		$payments_curr_sum = array();
		foreach($bills->arr() as $bill)
		{
			$data = array();
			$data["oid"] = $bill->id();
			$data["date"] = $bill->prop("bill_date");
			$data["bill_no"] = html::href(array(
				"url" => "javascript:gt_change(".$bill->id().");" ,
				"caption" => $bill->prop("bill_no"),
			));

			if($bill->prop("customer_name"))
			{
				$data["cust"] = $this->js_obj_url($bill->prop("customer") , $bill->prop("customer_name"));
			}
			else
			{
				$data["cust"] = $this->js_obj_url($bill->prop("customer"), $bill->prop("customer.name"));
			}

			$data["state"] = $bill_inst->states[$bill->prop("state")];
			$data["bill_due_date"] = $bill->prop("bill_due_date");

			//laekunud summa
			if($payments_sum = $bill->get_payments_sum())
			{
				$payments_curr_sum[$bill->get_bill_currency_name()]+= $payments_sum;
				$paid_c = $this->convert_to_company_currency(array("sum" => $payments_sum, "o" => $bill, "company_curr" => $company_curr));
				$paid_company_sum+= $paid_c;

				$data["paid"] = number_format($paid_c,2);//number_format($payments_sum,2)." ".$bill->get_bill_currency_name();
				//$person_payments[$bill->prop("customer.client_manager")] += $payments_sum;
				$person_payments[$bill->prop("customer.client_manager")] += $paid_c;
			}

			//hilinenud
			if(($bill->prop("state") == 1 || $bill->prop("state") == 6 || $bill->prop("state") == -6) && $bill->prop("bill_due_date") < time())
			{
				$data["late"] = (int)((time() - $bill->prop("bill_due_date")) / (3600*24));
			}

			//laekumiskuup2ev
			if($payment_date = $bill->get_last_payment_date())
			{
				$data["payment_date"] = $payment_date;
			}
//			if($_GET["test"])
//			{
				$sum = $bill_inst->get_bill_sum($bill);
				$c_sum = $this->convert_to_company_currency(array("sum" => $sum, "o" => $bill, "company_curr" => $company_curr));

				$company_sum+= $c_sum;

//				$curr_sum[$bill->get_bill_currency_name()]+= $sum;
				$data["sum"] = number_format($c_sum,2);//number_format($sum,2)." ".$bill->get_bill_currency_name();
				$person_sum[$bill->prop("customer.client_manager")] += $c_sum;
//			}
			$t->define_data($data);
		}

		$tmp_all = array();
		$tmp_paid = array();
		foreach($curr_sum as $c => $s)
		{
			$tmp_all[] = $s." ".$c;
		}
		foreach($payments_curr_sum as $c => $s)
		{
			$tmp_paidl[] = $s." ".$c;
		}

		$t->define_data(array(
			"cust" => "<b>".t("Kokku").":</b>",
			"sum" =>  number_format($company_sum,2),//join("<br>" , $tmp_all),
			"paid" => number_format($paid_company_sum,2),//join("<br>" , $tmp_paidl),
		));

		$t->set_sortable(false);

		if($req["return_table"])
		{
			return;
		}


		$sf = new aw_template();
		$sf->db_init();
		$sf->tpl_init("automatweb");
		$sf->read_template("index.tpl");

		$t2 = new vcl_table();
		$this->_init_bill_person_rows_t($t2);
		foreach($person_payments as $person => $val)
		{
			$po = "";
			if($this->can("view" , $person))
			{
				$po = obj($person);
				$t2->define_data(array(
					"name" => $po->name(),
					"payments" => number_format($val,2 , ",", ""),
					"sum" => number_format($person_sum[$person],2 , ",", ""),
					"pers" => number_format((($val/$person_sum[$person]) * 100),2 , ",", ""),
				));
			}
		}
		$t2->define_data(array(
			"name" => "<b>".t("Kokku").":</b>",
			"payments" =>number_format(array_sum($person_payments),2 , ",", ""),
			"sum" => number_format(array_sum($person_sum),2 , ",", ""),
			"pers" => number_format(((array_sum($person_payments)/array_sum($person_sum)) * 100),2 , ",", ""),
		));
		$t2->set_header(t("Laekumine inimeste l&otilde;ikes"));



/*		//summa rida
		$t->define_data(array(
				"content" => '<b>'.t("Summa:").'</b>',
				"length" => $this->hours_format($l_sum),//number_format($l_sum, 3, ',', '') ,
				"sum" => number_format($s_sum, 2, ',', ''),
				"length_cus" => $this->hours_format($l_cus),//number_format($l_cus, 3, ',', ''),
			));
*/
		$sf->vars(array(
			"content" => $t->draw()."<p>".$t2->draw()."</p>",
			"uid" => aw_global_get("uid"),
			"charset" => aw_global_get("charset"),
			"MINIFY_JS_AND_CSS" => $sf->parse("MINIFY_JS_AND_CSS")
		));

//		$action = $co_inst->mk_my_orb("create_bill", array("id" => $co));
//		$form_start = "<form action='".$action."' method='POST' name='changeform' enctype='multipart/form-data' >
		$form_start = "<form action='orb.aw' method='POST' name='changeform' enctype='multipart/form-data' >
			<input type='hidden' NAME='MAX_FILE_SIZE' VALUE='100000000'>
			<input type='hidden' NAME='class' VALUE='crm_company'>
			<input type='hidden' NAME='action' VALUE='add_payment'>
			<input type='hidden' NAME='id' VALUE='".$co."'>"
			;
		$form_end = "</form>";

		die($this->_get_bills_toolbar().$form_start.$sf->parse().$form_end);
		return ($tb->get_toolbar().$t->draw());
	}

	private function _get_bills_toolbar()
	{
		$tb =  new toolbar();
		$tb->add_button(array(
			"name" => "add_payment",
			"img" => "create_bill.jpg",
			"tooltip" => t("Lisa laekumine"),
			"url" => "javascript:document.changeform.submit();"
		));
		return $tb->get_toolbar();
	}

	function submit_js()
	{
		$inst = new crm_company();
		return '<script type="text/javascript">
		doLoad(600000);
			var sURL = unescape(window.location.href);
			function doLoad()
			{
			setTimeout( "refresh()", 600000 );
			}
			function refresh()
			{
				window.location.reload();
			}

		function submit_changeform(action)
		{
			changed = 0;

			if (typeof(aw_submit_handler) != "undefined")
			{
				if (aw_submit_handler() == false)
				{
					document.getElementById(\'button\').disabled=false;
					return false;
				}
			}
			if (typeof action == "string" && action.length>0)
			{
				document.changeform.action.value = action;
			};
			document.changeform.submit();
		}

		function gt_change(id)
		{
			change_url = "'.$inst->mk_my_orb("gt_change").'";
			change_url = change_url+"&id="+id+"&return_url='.urlencode(get_ru()).'";
			NewWindow = window.open(change_url , "_blank");
//			window.location.href = change_url;
		}
		</script>';
	}

	function __row_sorter($a, $b)
	{
		if(!($this->can("view" , $this->r2t[$a->id()]) && $this->can("view" , $this->r2t[$a->id()])))
		{
			return -1;
		}
		$a_task = obj($this->r2t[$a->id()]);
		$b_task = obj($this->r2t[$b->id()]);
		$b_proj = $b_task->prop("project");
		$a_proj = $a_task->prop("project");
		$b_cust = $b_task->prop("customer");
		$a_cust = $a_task->prop("customer");
		if(($a_cust - $b_cust) == 0)
		{
			if(($a_proj - $b_proj) == 0)
			{
				if(($a_task->id() - $b_task->id()) == 0)
				{
					return $a->prop("date") - $b->prop("date");
				}
				else return ($a_task->id() - $b_task->id());
			}
			else
			{
				return $a_proj - $b_proj;
			}
		}
		else
		{
			return $a_cust - $b_cust;
		}
		return $a->prop("date") - $b->prop("date");
	}

	function make_time_stats($r)
	{
		$r["stats_s_from"] = date_edit::get_timestamp($r["stats_s_from"]);
		$r["stats_s_to"] = date_edit::get_timestamp($r["stats_s_to"]);

		if ($r["stats_s_time_sel"] != "")
		{
			classload("core/date/date_calc");
			switch($r["stats_s_time_sel"])
			{
				case "today":
					$r["stats_s_from"] = time() - (date("H")*3600 + date("i")*60 + date("s"));
					$r["stats_s_to"] = time();
					break;

				case "yesterday":
					$r["stats_s_from"] = time() - ((date("H")*3600 + date("i")*60 + date("s")) + 24*3600);
					$r["stats_s_to"] = time() - (date("H")*3600 + date("i")*60 + date("s"));
					break;

				case "cur_week":
					$r["stats_s_from"] = get_week_start();
					$r["stats_s_to"] = time();
					break;

				case "cur_mon":
					$r["stats_s_from"] = get_month_start();
					$r["stats_s_to"] = time();
					break;

				case "last_mon":
					$r["stats_s_from"] = mktime(0,0,0, date("m")-1, 1, date("Y"));
					$r["stats_s_to"] = get_month_start();
					break;
			}
		}
		if($r["stats_s_from"] == -1)$r["stats_s_from"] = 3;//et aegade algusest
		if($r["stats_s_to"] == -1) $r["stats_s_to"] = 991154552400;
		return array("from" => $r["stats_s_from"], "to" => $r["stats_s_to"]);
	}

	function _get_task_stats($slaves, $tasks, $req)
	{
		// get paricipants
		$c = new connection();
		$ps = $c->find(array(
			"from" => $slaves,
			"from.class_id" => CL_CRM_PERSON,
			"type" => "RELTYPE_PERSON_TASK",
			"to" => $tasks->ids()
		));

		$task2impl = array();
		foreach($ps as $con)
		{
			$task2impl[$con["to"]][] = $con["from"];
		}

		$d = array();
		$task_i = get_instance(CL_TASK);
		$company_curr = $this->get_company_currency();
		foreach($tasks->arr() as $task)
		{
			$t_id = $task->id();

			$rows = $task_i->get_task_bill_rows($task, $req["stats_s_only_billable"]);
			foreach($rows as $row)
			{
				if ($row["is_oe"] == 1)
				{
					$d[$t_id]["otherexp"] += $row["sum"];
					continue;
				}

				if (!$this->_in_time($row["date"]))
				{
					continue;
				}

				//miski kokkuleppelise hinna teema.... suht kahtlane
				if(is_oid($row["bill_id"]) && $this->can("view" , $row["bill_id"]))
				{
					$bill_obj = obj($row["bill_id"]);
					if($bill_obj->meta("agreement_price"))
					{
						$agreement = $bill_obj->meta("agreement_price");
						$agreement_hr_price = $agreement["sum"]/$agreement["amt"];
						$row["sum"] = $agreement_hr_price * $row["amt_real"];
					}
				}

				$diff = $row["amt"] - $row["amt_real"];
				$d[$t_id]["pt"] += $row["amt_guess"];
				$d[$t_id]["tt"] += $row["amt_real"];
				$d[$t_id]["kt"] += $row["amt"];
				$d[$t_id]["sum_plus"] += $diff > 0 ? $diff : 0;
				$d[$t_id]["sum_minus"] += $diff < 0 ? -$diff : 0;
				$d[$t_id]["sum"] += $row["sum"];

			}
			$d[$t_id]["sum"] = $this->convert_to_company_currency(array("sum" => $d[$t_id]["sum"], "o" => $task, "company_curr" =>  $company_curr));

			if(is_oid($task->prop("customer")))
			{
				$d[$t_id]["cust"] = html::obj_change_url($task->prop("customer"));
			}
			else
			{
				$cu_a = array();
				$d[$t_id]["proj"] = "";
				foreach($task->connections_from(array("type" => "RELTYPE_CUSTOMER")) as $c)
				{
					$cu_a[].=html::obj_change_url($c->prop("to"));
				}
				$d[$t_id]["cust"] = join(", ",$cu_a);
			}

			if(is_oid($task->prop("project")))
			{
				$d[$t_id]["proj"] = html::obj_change_url($task->prop("project"));
			}
			else
			{
				$pr_a = array();
				$d[$t_id]["proj"] = "";
				foreach($task->connections_from(array("type" => "RELTYPE_PROJECT")) as $c)
				{
					$pr_a[].=html::obj_change_url($c->prop("to"));
				}
				$d[$t_id]["proj"] = join(", ",$pr_a);
			}
			$d[$t_id]["deadline"] = $task->prop("deadline");
			$is = array();
			foreach(safe_array($task2impl[$t_id]) as $p)
			{
				$is[] = html::obj_change_url($p);
			}
			$d[$t_id]["person"] = join(", ", $is);
			$d[$t_id]["task"] = html::obj_change_url($task);

			if ($this->can("view",$task->prop("bill_no")))
			{
				$bill = obj($task->prop("bill_no"));
				if ($bill->prop("bill_recieved") > 100)
				{
					$d[$t_id]["payment_over_date"] = (int)(($bill->prop("bill_recieved") - $bill->prop("bill_due_date")) / (24*3600));
				}
			}
		}
		return $d;
	}
	//id,
	private function get_customer_detailed_stats($arr)
	{
		$str = "";
		$co = obj($arr["id"]);
		$i = get_instance("applications/crm/crm_company_qv_impl");
		$p = array("value" => "");
		$i->_get_qv_cust_inf(array(
			"obj_inst" => $co,
			"prop" => &$p,
			"tasks" => $arr["tasks"],
			"proj" => $arr["proj"],
			"request" => $arr["request"]
		));

		$str .= "<b><font size=5>".$co->name()."</font><br></b><br>";
		$pv = $p["value"]."<br><br>";

		// add table
		$vcl = new vcl_table();
		$p = array(
			"vcl_inst" => $vcl
		);

		$i->_get_qv_t(array(
			"prop" => &$p,
			"obj_inst" => $co,
			"tasks" => $arr["tasks"],
			"proj" => $arr["proj"],
			"request" => $arr["request"]
		));

		if ($this->_stats_s_from < 100)
		{
			$f_d = t("K&otilde;ik");
		}
		else
		{
			$f_d = date("d.m.Y", $this->_stats_s_from);
		}
		if ($this->_stats_s_to < 100)
		{
			$t_d = t("K&otilde;ik");
		}
		else
		{
			$t_d = date("d.m.Y", $this->_stats_s_to);
		}
		$timespan = $f_d." - ".$t_d;
		$str .= "</b>".t("Ajavahemikul")." $timespan<br>";
		$str .=	t("T&ouml;&ouml;tunde kokku")." ".$this->hours_format($i->hrs_total)." <br>";//number_format($i->hrs_total, 3, ',', '')." <br>";
		$str .= t("Arvele l&auml;inud t&ouml;&ouml;tunde")." ".$this->hours_format($i->hrs_on_bill)." <br>";//number_format($i->hrs_on_bill, 3, ',', '')."<br>";
		$str .= t("Tehtud t&ouml;id summas")." ".number_format($i->done_sum, 2, ',', '')."<br>";
		$str .= t("Esitatud arveid summas")." ".number_format($i->bills_sum, 2, ',', '')."<br>";
		$str .= t("Tasutud arveid summas")." ".number_format($i->bills_paid_sum, 2, ',', '')."<br><br>";
		$str .= $pv;
		$str .= $vcl->draw();
		$str .= "<br><br><hr><br>";
		return $str;
	}

	function _get_task_cust_det($slaves, $tasks, $r, $bugs)
	{
		// for all customers in the list of tasks, show overview with all the tasks

		$cust = array();
		$proj = array();
		$task_i = get_instance(CL_TASK);
		foreach($tasks->arr() as $task)
		{
			$cust[$task->prop("customer")] = 1;
			if (($p = $task->prop("project")))
			{
				$proj[$p] = $p;
			}
		}

		foreach($bugs->arr() as $bug)
		{
			$cust[$bug->prop("customer")] = 1;
			if (($p = $bug->prop("project")))
			{
				$proj[$p] = $p;
			}
		}

		$u = get_instance(CL_USER);
		$co = $u->get_current_company();

		$i = get_instance("applications/crm/crm_company_qv_impl");
		$str = "";

		foreach($cust as $id => $tmp)
		{
			if (!is_oid($id) || $id == $co || !$this->can("view" , $id))
			{
				continue;
			}

			$this->custdetstart = microtime();

			$str .= $this->get_customer_detailed_stats(array(
				"tasks" => $tasks,
				"proj" => $proj,
				"request" => $r,
				"id" => $id,
			));

			if($_GET["debug"])
			{
				list($usec, $sec) = explode(" ", $this->custdetstart);
				list($usec2, $sec2) = explode(" ", microtime());
				arr($co->name() . " task_cust_det: " . ($sec2 - $sec).".".substr((1 + $usec2 - $usec) , 2));
			}
			flush();

		}

		if (!count($cust))
		{
			$str = t("<b>Valitud perioodis ei ole &uuml;htegi s&uuml;ndmust!</b>");
		}

		$sf = new aw_template;
		$sf->db_init();
		$sf->tpl_init("automatweb");
		$sf->read_template("index.tpl");
		$sf->vars(array(
			"content"	=> $str,
			"uid" => aw_global_get("uid"),
			"charset" => aw_global_get("charset"),
			"MINIFY_JS_AND_CSS" => $sf->parse("MINIFY_JS_AND_CSS")
		));
		die($sf->parse());
	}


	function _init_ps_det_t($t)
	{
		$t->define_field(array(
			"name" => "icon",
			"caption" => t(""),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "date",
			"caption" => t("Kuup&auml;ev"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimetus"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "cust",
			"caption" => t("Klient"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "parts",
			"caption" => t("Osalejad"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "state",
			"caption" => t("Staatus"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "hrs",
			"caption" => t("Tunde"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa"),
			"align" => "center"
		));
	}

	function _get_task_pers_det($slaves, $tasks, $r, $bugs)
	{
		if ($tasks->count() == 0)
		{
			$str = t("<b>Valitud perioodis ei ole &uuml;htegi s&uuml;ndmust!</b>");
			if ($r["ret"] == 1)
			{
				return $str;
			}
			else
			{
				die($str);
			}
		}
		$task_i = get_instance(CL_TASK);
		// get persons from search
		$f = array(
			"oid" => $slaves,
			"class_id" => CL_CRM_PERSON,
			"name" => "%".$r["stats_s_worker"]."%",
			"lang_id" => array(),
			"site_id" => array()
		);
		$p_list = new object_list($f);
		$c = new connection();
		$ps = $c->find(array(
			"from" => $slaves,
			"from.class_id" => CL_CRM_PERSON,
			"type" => "RELTYPE_PERSON_TASK",
			"to" => $tasks->ids()
		));
		if (count($ps) == 0)
		{
			$str = t("Valitud filtriga s&uuml;ndmusi pole!");
			if ($r["ret"] == 1)
			{
				return $str;
			}
			else
			{
				die($str);
			}
		}

		$task2impl = array();
		foreach($ps as $con)
		{
			$impl2task[$con["from"]][] = $con["to"];
		}
		$str = "";
		$company_curr = $this->get_company_currency();
		if(is_oid($company_curr) && $this->can("view" , $company_curr)) $company_curr_obj = obj($company_curr);
		foreach($p_list->arr() as $p)
		{
			$tot_wh = 0;
			$done_sum = $done_sum_exp = 0;
			$bills_sum = $bills_sum_exp = 0;
			$paid_bills_sum = $payd_bills_sum_exp = 0;
			$bills_paid_sum = $bills_paid_sum_exp = 0;
			$hrs_on_bill = 0;

			if ($p->name() == "")
			{
				continue;
			}

			$str .= "<b><font size=5>".$p->name()."</font><br><br>";
			$t = new vcl_table();
			$this->_init_ps_det_t($t);

			$projs = array();
			// tasks
			if (!is_array($impl2task[$p->id()]) || count($impl2task[$p->id()]) == 0)
			{
				$str .= t("<b>Valitud perioodis ei ole &uuml;htegi s&uuml;ndmust!</b><br><br><br>");
				continue;
			}
			$filt = array(
				"oid" => $impl2task[$p->id()],
				"class_id" => array(CL_CRM_MEETING,CL_CRM_CALL,CL_TASK),
			);
			if ($r["stats_s_cust"] != "")
			{
				$filt["CL_CRM_MEETING.customer.name"] = "%".$r["stats_s_cust"]."%";
			}
			$ol = new object_list($filt);

			// also add call/meeting/offer by range
			$c2 = new connection();
			$os = $c2->find(array(
				"from" => $p->id(),
				"from.class_id" => CL_CRM_PERSON,
				"type" => array("RELTYPE_PERSON_CALL", "RELTYPE_PERSON_MEETING"),
			));
			$osids = array();
			foreach($os as $entry)
			{
				$osids[] = $entry["to"];
			}
			$filt = array(
				"class_id" => array(CL_CRM_MEETING, CL_CRM_CALL, CL_CRM_OFFER),
				"brother_of" => new obj_predicate_prop("id"),
				"oid" => $osids
			);

			$t_i = get_instance(CL_TASK);
			$filt["start1"] = $this->start_filt;
			if ($r["stats_s_cust"] != "")
			{
				$filt["CL_CRM_MEETING.customer.name"] = "%".$r["stats_s_cust"]."%";
			}

			if (count($osids))
			{
				$ol2 = new object_list($filt);
				$ol->add($ol2);
			}

			$ol->sort_by(array("prop" => "start1"));

			$grpd = t("<b>Tegevused</b>");
			$ri = array();

			$sums = array();
			$mx_sb = 0;
			foreach($ol->arr() as $o)
			{
				$task_partipicant = 0;
				if(sizeof($o->prop("participants")) == 1)
				{
					$task_partipicant = reset($o->prop("participants"));
				}

				$parts = array();
				foreach((array)$o->prop("participants") as $_p)
				{
					$parts[] = html::obj_change_url($_p);
				}
				$sum = 0;
				$hrs = 0;
				foreach($t_i->get_task_bill_rows($o, false) as $row)
				{
					if (!$this->_in_time($row["date"]))
					{
						continue;
					}
					//kokkuleppehinna j2rgi
					if(is_oid($row["bill_id"]) && $this->can("view", $row["bill_id"]))
					{
						$bill_obj = obj($row["bill_id"]);
						if($bill_obj->meta("agreement_price"))
						{
							$agreement = $bill_obj->meta("agreement_price");
							$agreement_hr_price = $agreement["sum"] / $agreement["amt"];
							if($agreement["sum"]) $sum = $agreement_hr_price * $row["amt_real"];
						}
					}
					if (isset($row["impl"][$p->id()]))
					{
						$sum += $row["sum"];
						$hrs += $row["amt"];
						$tot_wh += $row["amt"];
						$done_sum += $row["sum"];
					}
				}
				$end = "";
				if ($o->prop("end") > $o->prop("start1"))
				{
					$end = " - ".date("d.m.Y", $o->prop("end"));
				}
				// kui igasugu valuuta v2ljad on t2idetud ja arve on tehtud teises valuutas,... siis tuleks summad ymber arvestada
				$sum = $this->convert_to_company_currency(array("sum" => $sum, "o" => $o, "company_curr" =>  $company_curr));

				$ri[] = array(
					"icon" => icons::get_icon($o),
					"date" => date("d.m.Y", $o->prop("start1")).$end,
					"name" => html::obj_change_url($o),
					"parts" => join(", ", $parts),
					"hrs" => $this->hours_format($hrs),//number_format($hrs, 3, ',', ''),
					"sum" => number_format($sum, 2, ',', ''),
					"grp_desc" => $grpd,
					"grp_num" => 3,
					"state" => $o->prop("is_done") > 0 ? t("Tehtud") : t("T&ouml;&ouml;s"),
					"cust" => html::obj_change_url($o->prop("customer")),
					"sb" => $o->prop("start1")
				);
				$projs[] = $o->prop("project");
				$sums["hrs"] += $hrs;
				$sums["sum"] += $sum;
				$mx_sb = max($mx_sb, $o->prop("start1"));

				$expenses = $t_i->get_task_expenses($o);

				foreach($expenses->arr() as $expense)
				{
					if($expense->prop("who") == $p->id() || $expense->prop("who") == $task_partipicant)
					{
						$done_sum_exp+= $this->convert_to_company_currency(array(
							"sum" => $expense->prop("cost"),
							"o" => $expense,
						));
					}
				}
			}

			usort($ri, create_function('$a,$b', 'return $a["sb"] == $b["sb"] ? 0 : ($a["sb"] > $b["sb"] ? 1 : -1);'));
			foreach($ri as $rii)
			{
				$t->define_data($rii);
			}

			if ($ol->count())
			{
				$t->define_data(array(
					"grp_desc" => $grpd,
					"grp_num" => 4,
					"name" => t("<b>Summa:</b>"),
					"hrs" => $this->hours_format($sums["hrs"]),//number_format($sums["hrs"], 3, ',', ''),
					"sum" => number_format($sums["sum"], 2, ',', ''),
					"sb" => $mx_sb+1
				));
			}

			if ($ol->count() == 0)
			{
				$str = $t->define_data(array(
					"name" => t("<b>Valitud perioodis ei ole &uuml;htegi s&uuml;ndmust!</b>")
				));
			}

			$task_ol = $ol;
			if (!count($projs))
			{
				$ol = new object_list();
			}
			else
			{
				$ol = new object_list(array(
					"class_id" => CL_PROJECT,
					"oid" => $projs,
					"lang_id" => array(),
					"site_id" => array(),
					"sort_by" => "aw_projects.aw_deadline desc",
					"state" => "%",
				));
			}
			$pd = t("<b>Projektid</b>");
			$pi = get_instance(CL_PROJECT);

			$sums = array();
			$mx_sb = 0;
			foreach($ol->arr() as $o)
			{
				$parts = array();
				foreach(array_unique((array)$o->prop("participants")) as $_p)
				{
					$parts[] = html::obj_change_url($_p);
				}
				$sum = 0;
				$hrs = 0;
				// get all tasks for that project and calc sum and hrs
				$t_ol = new object_list(array(
					"class_id" => CL_TASK,
					"lang_id" => array(),
					"site_id" => array(),
					"project" => $o->id(),
					"brother_of" => new obj_predicate_prop("id")
				));
				foreach($t_ol->arr() as $task)
				{
					foreach($t_i->get_task_bill_rows($task, false) as $row)
					{
						if (!$this->_in_time($row["date"]))
						{
							continue;
						}
						if (isset($row["impl"][$p->id()]))
						{
							$sum += $row["sum"];
							$hrs += $row["amt"];
						}
					}
				}

				$datestring = "";
				if ($o->prop("start") > 100)
				{
					$datestring = date("d.m.Y", $o->prop("start"));
					if ($o->prop("end") > 100)
					{
						$datestring .= " - ".date("d.m.Y", $o->prop("end"));
					}
				}

				$t->define_data(array(
					"date" => $datestring,
					"name" => html::obj_change_url($o),
					"parts" => join(", ", $parts),
					"hrs" => $this->hours_format($hrs),//number_format($hrs, 3, ',', ''),
					"sum" => number_format($sum, 2, ',', ''),
					"grp_desc" => $pd,
					"grp_num" => 1,
					"state" => $pi->states[$o->prop("state")],
					"cust" => html::obj_change_url(array_unique($o->prop("orderer")))
				));

				$sums["hrs"] += $hrs;
				$sums["sum"] += $sum;
				//$mx_sb = max($mx_sb, $o->prop("start1"));
			}

			if ($ol->count())
			{
				$t->define_data(array(
					"grp_desc" => $pd,
					"grp_num" => 2,
					"name" => t("<b>Summa:</b>"),
					"hrs" => $this->hours_format($sums["hrs"]),//number_format($sums["hrs"], 3, ',', ''),
					"sum" => number_format($sums["sum"], 2, ',', ''),
					"sb" => $mx_sb+1
				));
			}

			// bills
			$f = array(
				"class_id" => CL_CRM_BILL,
				"lang_id" => array(),
				"site_id" => array(),
				"sort_by" => "aw_crm_bill.aw_due_date desc",
				"bill_no" => "%",
				"CL_CRM_BILL.RELTYPE_TASK" => $task_ol->ids() // only from the task list for this co
			);
			$ol = new object_list($f);
			$bd = t("<span style='font-size: 0px;'>y</span><b>Arved</b>");
			$sums = array();
			$mx_sb = 0;
			foreach($ol->arr() as $o)
			{
				$parts = array();
				foreach((array)$o->prop("participants") as $_p)
				{
					$parts[] = html::obj_change_url($_p);
				}
				$bi = $o->instance();
				$sum = $bi->get_sum($o);
				$rows = $bi->get_bill_rows($o);
				$hrs = 0;
				$my_sum = 0;
				$sum = $this->convert_to_company_currency(array("sum"=>$sum, "o"=>$o, "company_curr"=>$company_curr));
				foreach($rows as $row)
				{
					if ($p_list->count() == 1)
					{
						$row_obj = obj($row["id"]);
						// if the row is connected to a task row and the person has that row, then add to his stats
						$task_row = $row_obj->get_first_obj_by_reltype("RELTYPE_TASK_ROW");
						$my = false;
						if ($task_row)
						{
							$impls = $task_row->prop("impl");
							if (!is_array($impls))
							{
								$impls = array($impls => $impls);
							}
							foreach($impls as $_impl)
							{
								if ($_impl == $p->id())
								{
									$my = true;
								}
							}
						}
						if ($my)
						{
							$my_sum += $row["sum"];
							$my_amt += $row["amt"];
						}

						if($row_obj->prop("is_oe"))
						{

							$exp = $row_obj->get_first_obj_by_reltype("RELTYPE_EXPENSE");
							$task_partipicant = 0;
							if($exp)
							{
								$t_ol = new object_list(array(
									"class_id" => CL_TASK,
									"CL_CRM_TASK.RELTYPE_EXPENSE.id" => $exp->id(),
									"lang_id" => array(),

								));
								$task = reset($t_ol->arr());
								if($task && (sizeof($task->prop("participants")) == 1))
								{
									$task_partipicant = reset($task->prop("participants"));
								}
								if($p->id() == $exp->prop("who") || $exp->prop("who") == $task_partipicant)
								{
									$bills_sum_exp+= $row["sum"];
									$my_sum += $row["sum"];
									if ($o->prop("state") > 1)
									{
										$bills_paid_sum_exp+= 	$this->convert_to_company_currency(array(
											"sum" => $exp->prop("cost"),
											"o" => $exp,
										));
									}
								}
							}
						}
					}
					$hrs += str_replace(",", ".",$row["amt"]);
				}
				$add = "";
				$my_sum = $this->convert_to_company_currency(array("sum"=>$my_sum, "o"=>$o, "company_curr"=>$company_curr));
				if ($my_sum > 0)
				{
					$add .= " (".number_format($my_sum, 2, ',', '').")";
				}
				$my_sum_sum += $my_sum;
				$my_amt_sum += $my_amt;
				//kokkuleppehinna j2rgi
				if($o->meta("agreement_price"))
				{
					$agreement = $o->meta("agreement_price");
	//				$agreement_hr_price = $agreement["sum"] / $agreement["amt"];
					if($agreement["sum"]) $sum = $agreement["sum"];
					if($agreement["amt"]) $hrs = $agreement["amt"];
				}
				$t->define_data(array(
					"date" => $o->prop("bill_date") > 100 ? date("d.m.Y", $o->prop("bill_date")) : "",
					"name" => html::get_change_url($o->id(), array("return_url" => get_ru(), "group" => "preview"), parse_obj_name($o->name())),
					"parts" => "",
					"hrs" => $this->hours_format($hrs),//number_format($hrs, 3, ',', ''),
					"sum" => number_format($sum, 2, ',', '').$add,
					"grp_desc" => $bd,
					"grp_num" => 6,
					"state" => $bi->states[$o->prop("state")],
					"cust" => html::obj_change_url($o->prop("customer"))
				));
				$hrs_on_bill += $hrs;
				$bills_sum += $sum;
				if ($o->prop("state") > 1)
				{
					$bills_paid_sum += $sum;
					$my_bills_paid_sum += $my_sum;
				}
				$sums["hrs"] += $hrs;
				$sums["sum"] += $sum;
			}

			$brc = NULL;
			if ($this->_stats_s_from > 300 && $this->_stats_s_to > 300)
			{
				$brc = new obj_predicate_compare(OBJ_COMP_BETWEEN, $this->_stats_s_from, $this->_stats_s_from);
			}
			else
			if ($this->_stats_s_from > 300 && $this->_stats_s_to < 300)
			{
				$brc = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $this->_stats_s_from);
			}
			else
			if ($this->_stats_s_from < 300 && $this->_stats_s_to > 300)
			{
				$brc = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $this->_stats_s_to);
			}
			$u = get_instance(CL_USER);
			/*$f = array(
				"class_id" => CL_CRM_BILL,
				"lang_id" => array(),
				"site_id" => array(),
				"sort_by" => "aw_crm_bill.aw_due_date desc",
				"bill_no" => "%",
				"bill_recieved" => $brc,
				"state" => 2,
				"CL_CRM_BILL.RELTYPE_TASK" => $task_ol->ids()
			);
			$bb_ol = new object_list($f);
			foreach($bb_ol->arr() as $bb_bill)
			{
				$paid_bills_sum += $bb_bill->prop("sum");
			}*/


			if ($ol->count())
			{
				$t->define_data(array(
					"grp_desc" => $bd,
					"grp_num" => 6,
					"name" => t("<b>Summa:</b>"),
					"hrs" => $this->hours_format($sums["hrs"]),//number_format($sums["hrs"], 3, ',', ''),
					"sum" => number_format($sums["sum"], 2, ',', ''),
					"sb" => $mx_sb+1
				));
			}

			//bugs
			$sums = array();
			$mx_sb = 0;
			foreach($bugs->arr() as $o)
			{
				$bi = $o->instance();
				$sum = 0;
				$person = $o->prop("who");
				$hrs = $o->prop("num_hrs_to_cust");
				if(is_oid($person) && $this->can("view", $person))
				{
					$person_obj = obj($person);
					$rank = $person_obj->prop("rank");
					if(is_oid($rank) && $this->can("view", $rank))
					{
						$rank_obj = obj($rank);
						$sum = $hrs * $rank_obj->prop("hr_price");
					}
				}
				//$sum = $this->convert_to_company_currency(array("sum"=>$sum, "o"=>$o, "company_curr"=>$company_curr));

				$t->define_data(array(
					"date" => $o->prop("deadline") > 100 ? date("d.m.Y", $o->prop("deadline")) : "",
					"name" => html::get_change_url($o->id(), array("return_url" => get_ru(), "group" => "preview"), parse_obj_name($o->name())),
					"parts" => "",
					"hrs" => $this->hours_format($hrs),//number_format($hrs, 3, ',', ''),
					"sum" => number_format($sum, 2, ',', '').$add,
					"grp_desc" => t("<span style='font-size: 0px;'>y</span><b>Arendus&uuml;lesanded</b>"),
					"grp_num" => 7,
					"state" => $bi->bug_statuses[$o->prop("bug_status")],
					"cust" => html::obj_change_url($o->prop("customer"))
				));
				$hrs_on_bill += $hrs;
				$bills_sum += $sum;
				if ($o->prop("state") == 2)
				{
					$bills_paid_sum += $sum;
					$my_bills_paid_sum += $my_sum;
				}
				$sums["hrs"] += $hrs;
				$sums["sum"] += $sum;
			}

			$t->define_data(array(
				"grp_desc" => t("<span style='font-size: 0px;'>y</span><b>Arendus&uuml;lesanded</b>"),
				"grp_num" => 7,
				"name" => t("<b>Summa:</b>"),
				"hrs" => $this->hours_format($sums["hrs"]),//number_format($sums["hrs"], 3, ',', ''),
				"sum" => number_format($sums["sum"], 2, ',', ''),
				"sb" => $mx_sb+1
			));

			$t->sort_by(array(
				"rgroupby" => array("grp_num" => "grp_desc"),
				"sorder" => "asc",
				"field" => array("sb", "grp_num")
			));

			if ($this->_stats_s_from < 100)
			{
				$f_d = t("K&otilde;ik");
			}
			else
			{
				$f_d = date("d.m.Y", $this->_stats_s_from);
			}
			if ($this->_stats_s_to < 100)
			{
				$t_d = t("K&otilde;ik");
			}
			else
			{
				$t_d = date("d.m.Y", $this->_stats_s_to);
			}
			if ($my_sum_sum > 0)
			{
				$bills_sum = $my_sum_sum;
				$bills_paid_sum = $my_bills_paid_sum;
			}
			$timespan = $f_d." - ".$t_d;
			$str .= "</b>".t("Ajavahemikul")." $timespan<br>";
			$str .=	t("T&ouml;&ouml;tunde kokku")." ".$this->hours_format($tot_wh)."<br>";//number_format($tot_wh, 3, ',', '')."<br>";
			$str .= t("Arvele l&auml;inud t&ouml;&ouml;tunde")." ".$this->hours_format($hrs_on_bill)."<br>";//number_format($hrs_on_bill, 3, ',', '')."<br>";
			$str .= t("Tehtud t&ouml;id summas")." ".number_format($done_sum, 2, ',', '')." (".t("sh kulud ").number_format($done_sum_exp, 2, ',', '')."), ".t("neto ").number_format(($done_sum - $done_sum_exp), 2, ',', '')."<br>";
			$str .= t("Esitatud arveid summas")." ".number_format($bills_sum, 2, ',', '')." (".t("sh kulud ").number_format($bills_sum_exp, 2, ',', '')."), ".t("neto ").number_format(($bills_sum - $bills_sum_exp), 2, ',', '')."<br>";
			$str .= t("Tasutud arveid summas")." ".number_format($bills_paid_sum, 2, ',', '')." (".t("sh kulud ").number_format($bills_paid_sum_exp, 2, ',', '')."), ".t("neto ").number_format(($bills_paid_sum - $bills_paid_sum_exp), 2, ',', '')."<br><br>";
			$str .= $t->draw();
			$str .= "<br><br><hr><br>";
		}

		if ($r["ret"] == 1)
		{
			return $str;
		}

		$sf = new aw_template;
		$sf->db_init();
		$sf->tpl_init("automatweb");
		$sf->read_template("index.tpl");
		$sf->vars(array(
			"content"	=> $str,
			"uid" => aw_global_get("uid"),
			"charset" => aw_global_get("charset"),
			"MINIFY_JS_AND_CSS" => $sf->parse("MINIFY_JS_AND_CSS")
		));
		die($sf->parse());
	}

	function check_date($date, $start, $end)
	{
		$start = (mktime(0, 0, 0, $start["month"], $start["day"], $start["year"]));
		$end = (mktime(0, 0, 0, $end["month"], $end["day"], $end["year"]));
		if($date > $start && $date < $end)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function get_company_currency()
	{
		if(!$this->co_currency)
		{
			$u = get_instance(CL_USER);
			$company = get_current_company();
			if ($company)
			{
				$this->co_currency = $company->prop("currency");
			}
		}
		return $this->co_currency;
	}


	/**
	@attrib params=name
	@param sum required type=int
		sum to convert
	@param o required type=object
		this objects sum you want to convert... it works if it is class crm_bill or task or crm_expense
	@param company_curr optional type=oid
		company currency oid
	**/
	function convert_to_company_currency($args)
	{
		extract($args);
		if(empty($company_curr))
		{
			$company_curr = $this->get_company_currency();
		}
		$changed = 0;
		if($o->class_id() == CL_CRM_EXPENSE)
		{
			classload("core/date/date_calc");
			if(!$o->prop("currency"))
			{
				return $sum;
			}
			$object_curr = $o->prop("currency");
			$date = $o->prop("date");
			if(is_array($date))
			{
				$date = date_edit::get_timestamp($date);
			}
		}

		if($o->class_id() == CL_TASK)
		{
			$object_curr = $o->prop("hr_price_currency");
			$date = $o->prop("deadline");
			if(!($date > 0))
			{
				$date = $o->prop("end");
			}
			if(!($date > 0))
			{
				$date = $o->prop("start1");
			}
		}

		if($o->class_id() == CL_CRM_BILL)
		{
			$object_curr = $o->get_bill_currency_id();
			$date = $o->prop("bill_date");
		}

		if($o->class_id() == CL_CRM_BILL_PAYMENT)
		{
			$date = $o->prop("date");
			$object_curr = $o->prop("currency");
		}

		if(
			is_oid($company_curr)
			&& $this->can("view" , $company_curr)
			&& $object_curr
			&& !($object_curr == $company_curr)
			&& is_oid($object_curr)
		)
		{
			$company_curr_obj = obj($company_curr);
			$currency_rates = (array) $company_curr_obj->meta("rates");
			foreach($currency_rates as $rate)
			{
				if($rate["currency"] == $object_curr && $rate["rate"] && $this->check_date($date, $rate["start_date"],$rate["end_date"]))
				{
					$sum = $sum * $rate["rate"];
					$changed = 1;
					continue;
				}
			}

			if(!$changed)//et kui ei saanud vahetuskurssi asutuse valuuta juurest, siis vaatab teisest
			{
				$curr_obj = obj($object_curr);
				$currency_rates = (array) $curr_obj->meta("rates");
				foreach($currency_rates as $rate)
				{
					if($rate["currency"] == $company_curr && $rate["rate"] && $this->check_date($date, $rate["start_date"],$rate["end_date"]))
					{
						$sum = $sum/$rate["rate"];
						$changed = 1;
						continue;
					}
				}
			}
		}
		return $sum;
	}

	function _get_stats_s_from($arr)
	{
		$data =& $arr["prop"];
		if ($arr["request"][$data["name"]]["year"] > 1)
		{
			$data["value"] = $arr["request"][$data["name"]];
		}
		else
		{
			// default to last week
/*			$day = date("w");
			if ($day == 0)
			{
				$day = 6;
			}
			else
			{
				$day--;
			}
*/			$data["value"] = mktime(0,0,0, date("m"), 1, date("Y"));
		}
	}

	function _get_stats_s_time_sel($arr)
	{
		$arr["prop"]["options"] = array(
			"" => t("--vali--"),
			"today" => t("T&auml;na"),
			"yesterday" => t("Eile"),
			"cur_week" => t("Jooksev n&auml;dal"),
			"cur_mon" => t("Jooksev kuu"),
			"last_mon" => t("Eelmine kuu")
		);
		$arr["prop"]["value"] = $arr["request"]["stats_s_time_sel"];
	}

	function _get_stats_stats_time_sel($arr)
	{
		$arr["prop"]["options"] = array(
			"" => t("--vali--"),
//			"today" => t("T&auml;na"),
//			"yesterday" => t("Eile"),
			"next" => t("Plaanis"),
			"cur_week" => t("Jooksev n&auml;dal"),
			"cur_mon" => t("Jooksev kuu"),
			"last_mon" => t("Eelmine kuu"),
			"last_last_mon" => t("&Uuml;leeelmine kuu"),
			"cur_year" => t("Jooksev aasta"),
			"last_year" => t("Eelmine aasta"),
		);
		$arr["prop"]["value"] = isset($arr["request"]["stats_stats_time_sel"]) ? $arr["request"]["stats_stats_time_sel"] : "";
		if(empty($arr["prop"]["value"]) && empty($arr["request"]["stats_stats_from"]) && empty($arr["request"]["stats_stats_to"]))
		{
			$arr["prop"]["value"] = "cur_mon";
		}
	}

	function _get_stats_s_toolbar($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			'name' => 'save',
			'img' => 'save.gif',
			'tooltip' => t('Salvesta aruanne'),
			'action' => 'save_report',
		));
	}

	function _get_stats_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			'name' => 'delete',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta aruandeid'),
			"confirm" => t("Olete kindel et soovite valitud aruanded kustutada?"),
			'action' => 'submit_delete_docs',
		));
	}

	function _init_stats_list_t($t)
	{
		$t->define_field(array(
			"name" => "res_type",
			"caption" => t("T&uuml;&uuml;p"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "cust",
			"caption" => t("Klient"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "proj",
			"caption" => t("Projekt"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "worker",
			"caption" => t("T&ouml;&ouml;taja"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "view",
			"caption" => t("Vaata"),
			"align" => "center"
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	function _get_stats_list($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_stats_list_t($t);

		$format = t('%s salvestatud aruanded');
		$t->set_caption(sprintf($format, $arr['obj_inst']->name()));

		$ol = new object_list(array(
			"class_id" => CL_CRM_REPORT_ENTRY,
			"parent" => $arr["obj_inst"]->id()
		));

		$t->data_from_ol($ol, array("change_col" => "view"));
/*		foreach($t->data as $key => $data)
		{
			$t->data[$key]["res_type"] = $this->res_types[$t->data[$key]["res_type"]];//arr($t->data);
		}
*/	}

	function _in_time($tm)
	{
		if (($this->_stats_s_from < 100 || $tm >= $this->_stats_s_from) && ($tm <= $this->_stats_s_to || $this->_stats_s_to < 100))
		{
			return true;
		}
		return false;
	}

	function _get_undone_orders($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_undone_tbl($t,$cl);
		$xls = $arr["xls"];
		// list orders from order folder

		$ol = $arr["obj_inst"]->get_undone_orders();
		foreach($ol->arr() as $o)
		{
			$t->define_data(array(
				"name" => $o->name(),
				"sum" => $o->get_price(),
			));
		}
		$t->set_caption(t("T&auml;itmata tellimused"));
	}

	function _init_undone_tbl($t,$cl)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));
		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa"),
		));
	}

	function _get_unpaid_bills($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_bills_list_t($t, $arr["request"]);
		$bills = $arr["obj_inst"]->get_unpaid_bills();

		$d = get_instance("applications/crm/crm_data");

		$t->set_caption(t("Maksmata arved"));

		$bill_i = get_instance(CL_CRM_BILL);
		$company_curr = $this->get_company_currency();

		foreach($bills->arr() as $bill)
		{
			$cust = "";
			$cm = "";
			if (is_oid($bill->prop("customer")) && $this->can("view", $bill->prop("customer")))
			{
				$tmp = obj($bill->prop("customer"));
				$cust = html::get_change_url($tmp->id(), array("return_url" => get_ru()), $bill->get_customer_name());
				$cm = html::obj_change_url($tmp->prop("client_manager"));
			}

			$state = $bill_i->states[$bill->prop("state")];

			$cursum = $bill_i->get_bill_sum($bill,$tax_add);

			//paneme ikka oma valuutasse ymber asja
			$curid = $bill->prop("customer.currency");
			if($company_curr && $curid && ($company_curr != $curid))
			{
				$cursum  = $this->convert_to_company_currency(array(
					"sum" =>  $cursum,
					"o" => $bill,
				));
			}
			$partial = "";
			if($bill->prop("state") == 3 && $bill->prop("partial_recieved") && $bill->prop("partial_recieved") < $cursum) $partial = '<br>'.t("osaliselt");

			$t->define_data(array(
				"bill_no" => html::get_change_url($bill->id(), array("return_url" => get_ru()), parse_obj_name($bill->prop("bill_no"))),
				"bill_date" => $bill->prop("bill_date"),
				"bill_due_date" => $bill->prop("bill_due_date"),
				"customer" => $cust,
				"state" => $state.$partial,
				"sum" => number_format($cursum, 2),
				"client_manager" => $cm,
				"oid" => $bill->id(),
				"print" => $bill->get_bill_print_popup_menu(),
			));
			$sum+= $cursum;
		}

		$t->set_default_sorder("desc");
		$t->set_default_sortby("bill_no");
		$t->sort_by();
		$t->set_sortable(false);

		$t->define_data(array(
			"sum" => "<b>".number_format($sum, 2)."</b>",
			"bill_no" => t("<b>Summa</b>")
		));
	}


	function _init_bills_list_t($t, $r)
	{
		$t->define_field(array(
			"name" => "bill_no",
			"caption" => t("Number"),
			"sortable" => 1,
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "bill_date",
			"caption" => t("Kuup&auml;ev"),
			"type" => "time",
			"format" => "d.m.Y",
			"numeric" => 1,
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "bill_due_date",
			"caption" => t("Makset&auml;htaeg"),
			"type" => "time",
			"format" => "d.m.Y",
			"numeric" => 1,
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "client_manager",
			"caption" => t("Kliendihaldur"),
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa"),
			"sortable" => 1,
			"numeric" => 1,
			"align" => "right"
		));

		$t->define_field(array(
			"name" => "state",
			"caption" => t("Staatus"),
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "print",
			"caption" => t("Prindi"),
			"sortable" => 1
		));
//		$t->define_chooser(array(
//			"field" => "oid",
//			"name" => "sel"
//		));
	}

	function there_are_bugs()
	{
		$filter = array(
			"class_id" => CL_BUG,
			"limit" => 1
		);

		$t = new object_data_list(
			$filter,
			array(
				CL_BUG =>  array(new obj_sql_func(OBJ_SQL_COUNT, "cnt" , "*"))
			)
		);

		$bug_data = $t->arr();
		$count = reset($bug_data);
		return $count["cnt"];
	}

	function _get_this_year_cash_flow($arr)
	{
		$company_curr = obj($this->get_company_currency());
		$val =& $arr["prop"]["value"];
		$val = t("K&auml;ive")." ".(date("Y", time()) - 1) . " - " . date("Y", time()). ": ".$arr["obj_inst"]->get_cash_flow()." ".$company_curr->name();
	}

	function _get_stats_stats_tb($arr)
	{
	}

	function _get_stats_tree($arr)
	{
		$tv = $arr["prop"]["vcl_inst"];
		$var = "st";

		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"persist_state" => true,
			"tree_id" => "co_stats_tree",
		));

		$leafs = array(
//			"rows" => t("T&ouml;&ouml;"),
			"tasks" => t("Tegevused"),
			"bills" => t("Arved"),
			"projects" => t("Projektid"),
			"customers" => t("Kliendid"),
			"workers" => t("T&ouml;&ouml;tajad"),
			"project_managers" => t("Projektijuhid")
		);
		foreach($leafs as $key => $leaf_name)
		{
			if (isset($_GET[$var]) && $_GET[$var] == $key)
			{
				$leaf_name = "<b>".$leaf_name."</b>";
			}
			$tv->add_item(0,array(
				"name" => $leaf_name,
				"id" => $key,
				"url" => aw_url_change_var($var, $key),
			));
		}

		$leafs = array(
			"task" => t("Toimetused"),
			"meeting" => t("Kohtumised"),
			"call" => t("K&otilde;ned"),
			"bug" => t("&Uuml;lesanded"),
		);

		foreach($leafs as $key => $leaf_name)
		{
			if (isset($_GET[$var]) && $_GET[$var] == $key)
			{
				$leaf_name = "<b>".$leaf_name."</b>";
			}
			$tv->add_item("tasks",array(
				"name" => $leaf_name,
				"id" => $key,
				"url" => aw_url_change_var($var, $key),
			));
		}

	}

	function _get_stats_stats_search($arr)
	{
	}

	function _get_status_chart($arr)
	{
		if (!empty($arr["request"]["st"]))
		{
			switch($arr["request"]["st"])
			{
				case "task":
				case "meeting":
				case "bug":
				case "customers":
					return false;
					break;

			}
		}

		aw_set_exec_time(AW_LONG_PROCESS);
		if(!empty($arr["request"]["stats_stats_time_sel"]))
		{
			switch($arr["request"]["stats_stats_time_sel"])
			{
				case "next":
					$start = time();
					$end = time() + 86400*1000;
					break;
				case "cur_week":
					$start = date_calc::get_week_start();
					$end = date_calc::get_week_start()+7*86400-1;
					break;
				case "cur_mon":
					$start = date_calc::get_month_start();
					$end = mktime(0,0,0,(date("m") + 1) , 1 , date("Y"))-1;
					break;
				case "last_mon":
					$start = mktime(0,0,0,(date("m") - 1) , 1 , date("Y"));
					$end = date_calc::get_month_start()-1;
					break;
				case "last_last_mon":
					$start = mktime(0,0,0,(date("m") - 2) , 1 , date("Y"));
					$end = mktime(0,0,0,(date("m") - 1) , 1 , date("Y"))-1;
					break;
				case "cur_year":
					$start = date_calc::get_year_start();
					$end = mktime(0,0,0,1,1,(date("Y") + 1))-1;
					break;
				case "last_year":
					$start = mktime(0,0,0,1,1,(date("Y") - 1));
					$end = date_calc::get_year_start()-1;
					break;
			}
		}
		elseif(!empty($arr["request"]["stats_stats_from"]))
		{
			$start = date_edit::get_timestamp($arr["request"]["stats_stats_from"]);
			$end = date_edit::get_timestamp($arr["request"]["stats_stats_to"]);
		}
		else
		{
			$start = date_calc::get_month_start();
			$end = mktime(0,0,0,(date("m") + 1) , date("d") , date("Y"));
		}
		$between = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING,$start, $end);

		$c = $arr["prop"]["vcl_inst"];
		$c->set_type(GCHART_PIE_3D);
		$c->set_size(array(
			"width" => 400,
			"height" => 150,
		));
		$c->add_fill(array(
			"area" => GCHART_FILL_BACKGROUND,
			"type" => GCHART_FILL_SOLID,
			"colors" => array(
				"color" => "e9e9e9",
			),
		));

		$c->set_colors(array(
			"330000","660000","000000","aa0000","aa00cc",
			"000000","003300","006600","009900","00cc00",
			"330099","660099","990099","aa0099","9900cc",
			"000066","000099","0000cc","990000","000033",
		));
		$times = array();
		$labels = array();
		$title = "";

		if (!empty($arr["request"]["st"]))
		{
			switch($arr["request"]["st"])
			{
				case "bills":
					$c->set_size(array(
						"width" => 950,
						"height" => 150,
					));
					$bills = $this->get_bills_data(array("between" => $between));
					foreach($bills as $bill)
					{
						$times[$bill["state"]] ++;
					}
					$bill_inst = get_instance(CL_CRM_BILL);
					foreach($times as $status => $count)
					{
						$labels[] = $bill_inst->states[$status]." (".$count.")";
					}
					$title = t("Arveid staatuste kaupa");
					break;
				case "projects":
					$c->set_size(array(
						"width" => 950,
						"height" => 200,
					));
					$works = $this->get_rows_data(array("between" => $between));
					$tasks = array();
					$works_per_person = array();
					foreach($works as $row)
					{
						$times[reset($row["project"])]+= $row["time_real"];
					}
					arsort($times);
					$count = 0;
					foreach($times as $project => $hours)
					{
						if($count > 18)
						{
							unset($times[$project]);
							continue;
						}
						$labels[] = get_name($project)." (".$hours.")";
						$count++;
					}
					$title = t("T&ouml;&ouml;tunde projketide kaupa");
					break;
				case "customers":
					return false;
					break;
				case "task":
					break;
				case "bug":
					break;
				case "meeting":
					break;
				case "rows":
					break;
				case "call":
					$calls_data = $this->get_calls_data(array(
						"from" => $start,
						"to" => $end
					));
					$calls_in = 0;
					$calls_out = 0;
					$undef = 0;
					foreach($calls_data as $dat)
					{
						if($dat["promoter"] == 0)
						{
							$calls_out++;
						}
						elseif($dat["promoter"] == 1)
						{
							$calls_in++;
						}
						else
						$undef++;

					}
					$times[] = $calls_out;
					$times[] = $calls_in;
					$times[] = $undef;
					$labels[] = t("K&otilde;ned v&auml;lja")." (".$calls_out.")";
					$labels[] = t("K&otilde;ned sisse")." (".$calls_in.")";
					$labels[] = t("M&auml;&auml;ramata")." (".$undef.")";
					$title = t("K&otilde;ned jagatuna sisse tulnuteks ja v&auml;lja l&auml;inuteks").". (".t("Kokku").":".(sizeof($calls_data)).")";

					break;
				case "workers":
					$c->set_colors(array(
						"660000","3300cc",
					));
					if(!$this->stats_rows_data)
					{
						$this->stats_rows_data = $this->get_rows_data(array("between" => $between));
					}

					$tasks = array();
					$works_per_person = array();

					$hours_cust = 0;
					$hours_dev = 0;
					$u = get_instance(CL_USER);
					$company = $u->get_current_company();
					foreach($this->stats_rows_data as $row)
					{
						$hours_cust+= (!reset($row["customer"]) || reset($row["customer"]) == $company ? 0 : $row["time_real"]);
						$hours_dev+= (!reset($row["customer"]) || reset($row["customer"]) == $company ? $row["time_real"] : 0);
					}
					$times["dev"] = $hours_dev;
					$labels[] = t("Arendus")." ".round($hours_dev , 2);
					$times["cust"] = $hours_cust;
					$labels[] = t("Kliendile")." ".round($hours_cust , 2);
					$title = t("T&ouml;&ouml;de jaotus klientide ja oma organisatsiooni vahel").". (".t("Kokku").":".round($hours_cust + $hours_dev , 2).")";
					break;
				default:
					return PROP_IGNORE;
					break;
			}
		}

		$c->set_title(array(
			"text" => $title,
			"color" => "666666",
			"size" => 11,
		));
		$c->add_data($times);
		$c->set_labels($labels);
	}

	function _get_money_chart($arr)
	{
		if (!empty($arr["request"]["st"]))
		{
			switch($arr["request"]["st"])
			{
				case "bills":
				case "projects":
				case "task":
				case "call":
				case "meeting":
				case "bug":
					return false;
					break;
			}
		}

		if(!empty($arr["request"]["stats_stats_time_sel"]))
		{
			switch($arr["request"]["stats_stats_time_sel"])
			{
				case "next":
					$start = time();
					$end = time() + 86400*1000;
					break;
				case "cur_week":
					$start = date_calc::get_week_start();
					$end = date_calc::get_week_start()+7*86400-1;
					break;
				case "cur_mon":
					$start = date_calc::get_month_start();
					$end = mktime(0,0,0,(date("m") + 1) , 1 , date("Y"))-1;
					break;
				case "last_mon":
					$start = mktime(0,0,0,(date("m") - 1) , 1 , date("Y"));
					$end = date_calc::get_month_start()-1;
					break;
				case "last_last_mon":
					$start = mktime(0,0,0,(date("m") - 2) , 1 , date("Y"));
					$end = mktime(0,0,0,(date("m") - 1) , 1 , date("Y"))-1;
					break;
				case "cur_year":
					$start = date_calc::get_year_start();
					$end = mktime(0,0,0,1,1,(date("Y") + 1))-1;
					break;
				case "last_year":
					$start = mktime(0,0,0,1,1,(date("Y") - 1));
					$end = date_calc::get_year_start()-1;
					break;
			}
		}
		elseif(!empty($arr["request"]["stats_stats_from"]))
		{
			$start = date_edit::get_timestamp($arr["request"]["stats_stats_from"]);
			$end = date_edit::get_timestamp($arr["request"]["stats_stats_to"]);
		}
		else
		{
			$start = date_calc::get_month_start();
			$end = mktime(0,0,0,(date("m") + 1) , date("d") , date("Y"));
		}
		$between = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING,$start, $end);

		$c = $arr["prop"]["vcl_inst"];
		$c->set_type(GCHART_BAR_V);
		$c->set_size(array(
			"width" => 550,
			"height" => 150,
		));
		$c->add_fill(array(
			"area" => GCHART_FILL_BACKGROUND,
			"type" => GCHART_FILL_SOLID,
			"colors" => array(
				"color" => "e9e9e9",
			),
		));
		$c->set_bar_sizes(array(
			"width" => 40,
			"bar_spacing" => 2,
//			"bar_group_spacing" => 8,
		));
		$times = array();
		$labels = array();
		$max = 0;
		$title = "";

		if (!empty($arr["request"]["st"]))
		{
			switch($arr["request"]["st"])
			{
				case "bills":
					break;
				case "projects":
					break;
				case "customers":
					$c->set_size(array(
						"width" => 950,
						"height" => 150,
					));
					$payments = $this->get_payments_data(array("between" => $between));

					foreach($payments as $payment)
					{
						$times[$payment["customer"]]+= $payment["sum"];
					}
					arsort($times);
					$max = 0;
					$count = 0;
					$title = t("Raha laekunud klientide kaupa").". (".t("Kokku").":".(array_sum($times)).")";
					foreach($times as $person => $time)
					{
						if($count > 10)
						{
							unset($times[$person]);
							continue;
						}
						$times[$person] = round($times[$person] , 2);
						$p = obj($person);

						$labels[] = is_object($p) ? ($p->prop("short_name") ? $p->prop("short_name") : substr($p->prop("name") , 0 , 10)) : "";
						if($max < $time)
						{
							$max = $time;
						}
						$count++;
					}
					$c->set_bar_sizes(array(
						"width" => 75,
						"bar_spacing" => 2,
			//			"bar_group_spacing" => 8,
					));
					break;
				case "task":
					break;
				case "bug":
					break;
				case "meeting":
					break;
				case "rows":
					break;
				case "call":
					break;
				case "workers":
					if(!$this->stats_rows_data)
					{
						$this->stats_rows_data = $this->get_rows_data(array("between" => $between));
					}

					foreach($this->stats_rows_data as $dat)
					{
						$person = reset($dat["impl"]);
						if($dat["time_real"])
						{
							$times[$person]+=$dat["time_real"];
						}
					}

					arsort($times);
					$max = 0;
					$count = 0;
					$title = t("Kirja l&auml;inud t&ouml;&ouml;tunnid inimeste kaupa").". (".t("Kokku").":".(array_sum($times)).")";
					foreach($times as $person => $time)
					{
						if($count > 11)
						{
							unset($times[$person]);
							continue;
						}
						$times[$person] = round($time , 2);
						$p = obj($person);

						$labels[] = is_object($p) ? $p->prop("firstname") : "";
						if($max < $time)
						{
							$max = $time;
						}
						$count++;
					}
					break;
				default:
					return PROP_IGNORE;
					break;
			}
		}

		$c->add_axis_label(2, $labels);
		$c->add_axis_label(3, $times);

		$c->set_axis(array(
			GCHART_AXIS_LEFT,
			GCHART_AXIS_BOTTOM,
			GCHART_AXIS_BOTTOM,
			GCHART_AXIS_TOP,
		));
		//set some labels
		$c->add_axis_label(0, array("0", round($max/2), round($max)));

		$c->add_axis_style(2, array(
			"color" => "FFFFFF",
			"font" => 8,
			"align" => GCHART_AXIS_TOP,
		));

		//set the range and style for one of them
		$c->add_axis_style(1, array(
			"color" => "ff0000",
			"font" => 11,
			"align" => GCHART_AXIS_BOTTOM,
		));
		$c->set_colors(array(
			"aa2222", "FFFF00","bbbbbb", "aa2222", "FFFF00","bbbbbb", "aa2222", "FFFF00","bbbbbb", "aa2222", "FFFF00",
		));

		$c->set_title(array(
			"text" => $title,
			"color" => "666666",
			"size" => 11,
		));
		$c->add_data($times);
		$c->set_labels($labels);
	}

	function _get_stats_table($arr)
	{
		aw_set_exec_time(AW_LONG_PROCESS);
		if(!empty($arr["request"]["stats_stats_time_sel"]))
		{
			switch($arr["request"]["stats_stats_time_sel"])
			{
				case "next":
					$start = time();
					$end = time() + 86400*1000;
					break;
				case "cur_week":
					$start = date_calc::get_week_start();
					$end = date_calc::get_week_start()+7*86400-1;
					break;
				case "cur_mon":
					$start = date_calc::get_month_start();
					$end = mktime(0,0,0,(date("m") + 1) , 1 , date("Y"))-1;
					break;
				case "last_mon":
					$start = mktime(0,0,0,(date("m") - 1) ,1 , date("Y"));
					$end = date_calc::get_month_start()-1;
					break;
				case "last_last_mon":
					$start = mktime(0,0,0,(date("m") - 2) , 1 , date("Y"));
					$end = mktime(0,0,0,(date("m") - 1) , 1 , date("Y"))-1;
					break;
				case "cur_year":
					$start = date_calc::get_year_start();
					$end = mktime(0,0,0,1,1,(date("Y") + 1))-1;
					break;
				case "last_year":
					$start = mktime(0,0,0,1,1,(date("Y") - 1));
					$end = date_calc::get_year_start()-1;
					break;
			}
		}
		elseif(!empty($arr["request"]["stats_stats_from"]))
		{
			$start = date_edit::get_timestamp($arr["request"]["stats_stats_from"]);
			$end = date_edit::get_timestamp($arr["request"]["stats_stats_to"]);
		}
		else
		{
			$start = date_calc::get_month_start();
			$end = mktime(0,0,0,(date("m") + 1) , 1 , date("Y"))-1;
		}

		$arr["request"]["stats_s_from"] = array("day" => date("d" , $start) , "month" => date("m" , $start) , "year" => date("Y" , $start));
		$arr["request"]["stats_s_to"] = array("day" => date("d" , $end) , "month" => date("m" , $end) , "year" => date("Y" , $end));
		$arr["request"]["return_table"] = 1;
		$arr["request"]["MAX_FILE_SIZE"] = 100000000;
		$arr["request"]["from"] = $start;
		$arr["request"]["to"] = $end;
		$arr["request"]["between"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING,$start, $end);
		$this->_stats_s_from = $start;
		$this->_stats_s_to = $end;

		if (!empty($arr["request"]["st"]))
		{
			switch($arr["request"]["st"])
			{
				case "bills":
					$filt = $arr["request"];
					$this->_get_bills_stats($slaves, $tasks, $filt, $arr["prop"]["vcl_inst"]);
					break;
				case "projects":
	//				$arr["request"]["stats_s_res_type"] = "proj";
	//				$this->_get_stats_s_res($arr);
					$this->_get_stats_projects($arr);
					break;
				case "customers":
	//				$arr["request"]["stats_s_res_type"] = "cust";
	//				$this->_get_stats_s_res($arr);
					$this->_get_stats_customers($arr);
					break;
				case "task":
					$arr["request"]["class_id"] = CL_TASK;
					$this->_get_stats_different_tasks($arr);
					break;
				case "bug":
					$arr["request"]["stats_s_res_type"] = "bugs";
					$this->_get_stats_s_res($arr);
					break;
				case "meeting":
					$arr["request"]["class_id"] = CL_CRM_MEETING;
					$this->_get_stats_different_tasks($arr);
					break;
				case "rows":
					$arr["request"]["stats_s_res_type"] = "rows";
					$this->_get_stats_s_res($arr);
					break;
				case "call":
					$arr["request"]["class_id"] = CL_CRM_CALL;
					$this->_get_stats_different_tasks($arr);
					break;
				case "workers":
					$this->_get_stats_workers($arr);
					break;
				case "project_managers":
					$this->_get_stats_project_managers($arr);
					break;
				default:
					$stuff = explode("_" , $arr["request"]["st"]);
					if($stuff[0] == "customer")
					{
						$str = $this->get_customer_detailed_stats(array(
							"id" => $stuff[1],
							"request" => $arr["request"],
						));
						$arr["prop"]["type"] = "text";
						$arr["prop"]["value"] = $str;
					}
					break;
			}
		}
	}

	private function _init_workers_t($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "left",
			"sortable" => 1,
			"numeric" => 1,
			"sorting_field" => "sort_name",
		));
		$t->define_field(array(
			"name" => "hours",
			"caption" => t("Tunde"),
			"align" => "center"
		));

		$t->define_field(array(
			"parent" => "hours",
			"name" => "pt",
			"caption" => t("<a href='javascript:void(0)' alt='Prognoositud tunde' title='Prognoositud tunde'>PT</a>"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));

		$t->define_field(array(
			"parent" => "hours",
			"name" => "tt",
			"caption" => t("<a href='javascript:void(0)' alt='Tegelikult tunde' title='Tegelikult tunde'>TT</a>"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));

		$t->define_field(array(
			"parent" => "hours",
			"name" => "kt",
			"caption" => t("<a href='javascript:void(0)' alt='Tunde Kliendile' title='Tunde Kliendile'>KT</a>"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));
		$t->define_field(array(
			"parent" => "hours",
			"name" => "at",
			"caption" => t("<a href='javascript:void(0)' alt='Tunde arvel' title='Tunde Arvel'>AT</a>"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));

		$t->define_field(array(
			"name" => "who",
			"caption" => t("Kellele"),
			"align" => "center"
		));
		$t->define_field(array(
			"parent" => "who",
			"name" => "cust",
			"caption" => t("Kliendile"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));
		$t->define_field(array(
			"parent" => "who",
			"name" => "our",
			"caption" => t("Arendus"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));

		$t->define_field(array(
			"name" => "meetings",
			"caption" => t("Kohtumisi"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));
		$t->define_field(array(
			"name" => "calls",
			"caption" => t("K&otilde;nesi"),
			"align" => "center"
		));
		$t->define_field(array(
			"parent" => "calls",
			"name" => "calls_in",
			"caption" => t("Sisse"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));
		$t->define_field(array(
			"parent" => "calls",
			"name" => "calls_out",
			"caption" => t("V&auml;lja"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));

		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));
	}

	private function _init_customers_t($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "left",
			"sortable" => 1,
			"sorting_field" => "sort_name",
		));
		$t->define_field(array(
			"name" => "hours",
			"caption" => t("Tunde"),
			"align" => "center"
		));

		$t->define_field(array(
			"parent" => "hours",
			"name" => "pt",
			"caption" => t("<a href='javascript:void(0)' alt='Prognoositud tunde' title='Prognoositud tunde'>PT</a>"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));

		$t->define_field(array(
			"parent" => "hours",
			"name" => "tt",
			"caption" => t("<a href='javascript:void(0)' alt='Tegelikult tunde' title='Tegelikult tunde'>TT</a>"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));

		$t->define_field(array(
			"parent" => "hours",
			"name" => "kt",
			"caption" => t("<a href='javascript:void(0)' alt='Tunde Kliendile' title='Tunde Kliendile'>KT</a>"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));
		$t->define_field(array(
			"parent" => "hours",
			"name" => "at",
			"caption" => t("<a href='javascript:void(0)' alt='Tunde arvel' title='Tunde Arvel'>AT</a>"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));

		$t->define_field(array(
			"name" => "meetings",
			"caption" => t("Kohtumisi"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));
		$t->define_field(array(
			"name" => "calls",
			"caption" => t("K&otilde;nesi"),
			"align" => "center"
		));
		$t->define_field(array(
			"parent" => "calls",
			"name" => "calls_in",
			"caption" => t("Sisse"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));
		$t->define_field(array(
			"parent" => "calls",
			"name" => "calls_out",
			"caption" => t("V&auml;lja"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));

		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa"),
			"align" => "right"
		));

		$t->define_field(array(
			"parent" => "sum",
			"name" => "bills",
			"caption" => t("Arvel"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));
		$t->define_field(array(
			"parent" => "sum",
			"name" => "payments",
			"caption" => t("Laekunud"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));
		$t->define_field(array(
			"name" => "real_hour_price",
			"caption" => t("Tegelik tunnihind"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));
	}

	private function _get_stats_customers($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$this->_init_customers_t($t);
		$rows = $this->get_rows_data($arr["request"]);
		$bills = $this->get_bills_data($arr["request"]);
		$payments = $this->get_payments_data($arr["request"]);

		$bills_data = $payments_data = array();
		foreach($bills as $bill)
		{
			$bills_data[$bill["customer"]]+= $bill["sum"];
		}

		foreach($payments as $payment)
		{
			$payments_data[$payment["customer"]]+= $payment["sum"];
		}

		$tg = array();
		$tc = array();
		$tr = array();
		$tb = array();
		$meetings = array();
		$calls_in = array();
		$calls_out = array();
		$hours_cust = array();
		$hours_dev = array();
		$sum = array();
		$u = new user();
		$company = $u->get_current_company();
		$name_objects = new object_list();

		foreach($rows as $row)
		{
			$person = reset($row["customer"]);
			if($row["task.class_id"] == CL_CRM_MEETING)
			{
				$meetings[$person][$row["task"]] = $row["task"];
			}
			if($row["task.class_id"] == CL_CRM_CALL)
			{
				if($calls_data[$row["task"]]["promoter"] == 0)
				{
					$calls_out[$person][$row["task"]] = $row["task"];
				}
				else
				{
					$calls_in[$person][$row["task"]] = $row["task"];
				}
			}

			$tr[$person]+= $row["time_real"];
			$tg[$person]+= $row["time_guess"];
			$tc[$person]+= $row["time_to_cust"];

			$hours_cust[$person]+= (reset($row["customer"]) == $company ? 0 : $row["time_real"]);
			$hours_dev[$person]+= (reset($row["customer"]) == $company ? $row["time_real"] : 0);
			if($row["bill_id"])
			{
				$tb[$person]+= $row["time_to_cust"];
			}
			if($person)$name_objects->add($person);
		}

		foreach($tr as $pers => $sum_hours)
		{
			if(!$sum_hours)
			{
				continue;
			}
			$data = array();

			if($this->can("view" , $pers))
			{
				$person = obj($pers);
//				$data["name"] = html::href(array(
//					"url" => "javascript:gt_change(".$pers.");" ,
//					"caption" => $person->name(),
//				));
				$data["name"] = html::href(array(
					"url" => aw_url_change_var("st", "customer_".$pers),
					"caption" => $person->name(),
				));

				$data["sort_name"] = $person->name();
			}
			else
			{
				$data["name"] = $data["sort_name"] = t("(Klient m&auml;&auml;ramata)");
			}
			$data["tt"] = round($tr[$pers] , 2);
			$data["kt"] = $tc[$pers];
			$data["pt"] = $tg[$pers];
			$data["at"] = $tb[$pers];
			$data["meetings"] = is_array($meetings[$pers]) ? sizeof($meetings[$pers]) : "";
			$data["calls_in"] = is_array($calls_in[$pers]) ? sizeof($calls_in[$pers]) : "";
			$data["calls_out"] = is_array($calls_out[$pers]) ? sizeof($calls_out[$pers]) : "";
			$data["bills"] = $bills_data[$pers];
			$data["payments"] = round($payments_data[$pers],2);
			$data["real_hour_price"] = round($data["payments"]/$data["tt"], 2);
			$t->define_data($data);
		}
		$t->set_default_sorder("desc");
		$t->set_default_sortby("real_hour_price");
		$t->sort_by();
		$t->set_sortable(false);
		$t->define_data(array(
			"name" => t("Kokku"),
			"tt" => array_sum($tr),
			"kt" => array_sum($tc),
			"pt" => array_sum($tg),
			"at" => array_sum($tb),
			"our" => array_sum($hours_dev),
			"sum" => array_sum($sum),
			"cust" => array_sum($hours_cust),
			"bills" => array_sum($bills_data),
			"payments" => array_sum($payments_data),
		));
	}

	private function _init_projects_t($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "left",
			"sortable" => 1,
			"sorting_field" => "sort_name",
		));
		$t->define_field(array(
			"name" => "customer",
			"caption" => t("Klient"),
			"align" => "left",
			"sortable" => 1,
//			"sorting_field" => "sort_cust_name",
		));
		$t->define_field(array(
			"name" => "hours",
			"caption" => t("Tunde"),
			"align" => "center"
		));

		$t->define_field(array(
			"parent" => "hours",
			"name" => "pt",
			"caption" => t("<a href='javascript:void(0)' alt='Prognoositud tunde' title='Prognoositud tunde'>PT</a>"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));

		$t->define_field(array(
			"parent" => "hours",
			"name" => "tt",
			"caption" => t("<a href='javascript:void(0)' alt='Tegelikult tunde' title='Tegelikult tunde'>TT</a>"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));

		$t->define_field(array(
			"parent" => "hours",
			"name" => "kt",
			"caption" => t("<a href='javascript:void(0)' alt='Tunde Kliendile' title='Tunde Kliendile'>KT</a>"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));
		$t->define_field(array(
			"parent" => "hours",
			"name" => "at",
			"caption" => t("<a href='javascript:void(0)' alt='Tunde arvel' title='Tunde Arvel'>AT</a>"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));

		$t->define_field(array(
			"name" => "meetings",
			"caption" => t("Kohtumisi"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));
		$t->define_field(array(
			"name" => "calls",
			"caption" => t("K&otilde;nesi"),
			"align" => "center"
		));
		$t->define_field(array(
			"parent" => "calls",
			"name" => "calls_in",
			"caption" => t("Sisse"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));
		$t->define_field(array(
			"parent" => "calls",
			"name" => "calls_out",
			"caption" => t("V&auml;lja"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));
	}

	private function _get_stats_projects($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$this->_init_projects_t($t);
		$rows = $this->get_rows_data($arr["request"]);
		$tg = array();
		$tc = array();
		$tr = array();
		$tb = array();
		$meetings = array();
		$calls_in = array();
		$calls_out = array();
		$hours_cust = array();
		$hours_dev = array();
		$sum = array();
		$name_objects = new object_list();
		$project_ids = array();

		foreach($rows as $row)
		{
			$person = reset($row["project"]);
			if(!$person)
			{
				continue;
			}
			if($row["task.class_id"] == CL_CRM_MEETING)
			{
				$meetings[$person][$row["task"]] = $row["task"];
			}
			if($row["task.class_id"] == CL_CRM_CALL)
			{
				if($calls_data[$row["task"]]["promoter"] == 0)
				{
					$calls_out[$person][$row["task"]] = $row["task"];
				}
				else
				{
					$calls_in[$person][$row["task"]] = $row["task"];
				}
			}

			$tr[$person]+= $row["time_real"];
			$tg[$person]+= $row["time_guess"];
			$tc[$person]+= $row["time_to_cust"];

			$hours_cust[$person]+= (reset($row["customer"]) == $company ? 0 : $row["time_real"]);
			$hours_dev[$person]+= (reset($row["customer"]) == $company ? $row["time_real"] : 0);
			if($row["bill_id"])
			{
				$tb[$person]+= $row["time_to_cust"];
			}
			$project_ids[$person] = $person;
		}

		foreach($project_ids as $project_id)
		{
			if($this->can("view" , $project_id))
			{
				$name_objects->add($project_id);
			}
		}

		foreach($tr as $pers => $sum_hours)
		{
			if(!$sum_hours || !$this->can("view" , $pers))
			{
				continue;
			}
			$person = obj($pers);
			$data = array();
			$data["name"] = html::href(array(
				"url" => "javascript:gt_change(".$pers.");" ,
				"caption" => $person->name(),
			));
			$customers = $person->get_customers();
			$data["customer"] = join(", " , $customers->names());
			$data["sort_name"] = $person->name();
			$data["tt"] = round($tr[$pers] , 2);
			$data["kt"] = $tc[$pers];
			$data["pt"] = $tg[$pers];
			$data["at"] = $tb[$pers];
			$data["meetings"] = is_array($meetings[$pers]) ? sizeof($meetings[$pers]) : "";
			$data["calls_in"] = is_array($calls_in[$pers]) ? sizeof($calls_in[$pers]) : "";
			$data["calls_out"] = is_array($calls_out[$pers]) ? sizeof($calls_out[$pers]) : "";

			$t->define_data($data);
		}
		$t->set_default_sorder("desc");
		$t->set_default_sortby("tt");
		$t->sort_by();
		$t->set_sortable(false);
		$t->define_data(array(
			"name" => t("Kokku"),
			"tt" => array_sum($tr),
			"kt" => array_sum($tc),
			"pt" => array_sum($tg),
			"at" => array_sum($tb),
			"our" => array_sum($hours_dev),
			"sum" => array_sum($sum),
			"cust" => array_sum($hours_cust),
		));
	}

	private function _init_project_managers_t($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "left",
			"sortable" => 1,
			"numeric" => 1,
			"sorting_field" => "sort_name",
		));
		$t->define_field(array(
			"name" => "hours",
			"caption" => t("Kliendile tehtud tunde"),
			"align" => "center"
		));

		$t->define_field(array(
			"parent" => "hours",
			"name" => "tt",
			"caption" => t("<a href='javascript:void(0)' alt='Tegelikult tunde' title='Tegelikult tunde'>TT</a>"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));

		$t->define_field(array(
			"parent" => "hours",
			"name" => "kt",
			"caption" => t("<a href='javascript:void(0)' alt='Tunde Kliendile' title='Tunde Kliendile'>KT</a>"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));
		$t->define_field(array(
			"parent" => "hours",
			"name" => "at",
			"caption" => t("<a href='javascript:void(0)' alt='Tunde arvel' title='Tunde Arvel'>AT</a>"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));


		$t->define_field(array(
			"name" => "bills",
			"caption" => t("Arveid esitatud"),
			"align" => "center"
		));

		$t->define_field(array(
			"parent" => "bills",
			"name" => "bills_no",
			"caption" => t("Arvuliselt"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));

		$t->define_field(array(
			"parent" => "bills",
			"name" => "bills_real_hours",
			"caption" => t("<a href='javascript:void(0)' alt='Tegelikke arvele l&auml;inud t&ouml;&ouml;tunde' title='Tegelikke arvele l&auml;inud t&ouml;&ouml;tunde'>TT</a>"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));

		$t->define_field(array(
			"parent" => "bills",
			"name" => "bills_customer_hours",
			"caption" => t("<a href='javascript:void(0)' alt='Arvele l&auml;inud t&ouml;&ouml;tunde kliendile' title='Arvele l&auml;inud t&ouml;&ouml;tunde kliendile'>TK</a>"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));

		$t->define_field(array(
			"parent" => "bills",
			"name" => "bills_hours",
			"caption" => t("<a href='javascript:void(0)' alt='Tunde Arvel' title='Tunde Arvel'>AT</a>"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));

		$t->define_field(array(
			"parent" => "bills",
			"name" => "bills_sum",
			"caption" => t("Summas"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));

		$t->define_field(array(
			"parent" => "bills",
			"name" => "bills_hp",
			"caption" => t("<a href='javascript:void(0)' alt='Tuletatud tunnihind' title='Tuletatud tunnihind'>TH</a>"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));

		$t->define_field(array(
			"parent" => "bills",
			"name" => "bills_payments",
			"caption" => t("Laekunud"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));

		$t->define_field(array(
			"name" => "meetings",
			"caption" => t("Kohtumisi"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));
		$t->define_field(array(
			"name" => "calls",
			"caption" => t("K&otilde;nesi"),
			"align" => "center"
		));
		$t->define_field(array(
			"parent" => "calls",
			"name" => "calls_in",
			"caption" => t("Sisse"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));
		$t->define_field(array(
			"parent" => "calls",
			"name" => "calls_out",
			"caption" => t("V&auml;lja"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
		));
	}

	private function _get_stats_project_managers($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_project_managers_t($t);
		$t->set_caption(t("Tabel"));//TODO
		$u = get_instance(CL_USER);
		$company = $u->get_current_company();

		if(!$this->stats_rows_data)
		{
			$this->stats_rows_data = $this->get_rows_data($arr["request"]);
		}
		$bill_rows_data = $this->get_bills_rows_data($arr["request"]);
		$br_data = $this->get_bills_rows_by_tr_data($arr["request"]);
		$bill_hours = array();
		$bill_customer_hours = array();
		$bill_real_hours = array();
		foreach($bill_rows_data as $brd)
		{
			$bill_hours[$brd["parent"]]+= $brd["amt"];
		}

		$tr2br = array();
		$bill_rows = array();
		foreach($br_data as $bill_row)
		{
			foreach($bill_row["task_row"] as $trid)
			{
				$tr2br[$trid] = $bill_row["oid"];
			}
		}

		$bills_data = $this->get_bills_data($arr["request"]);
		$calls_data = $this->get_calls_data($arr["request"]);
		$bills_task_rows =  $this->get_rows_data(array(
			"bill_between" => $arr["request"]["between"],
		));

		foreach($bills_task_rows as $btr)
		{
			$bill_real_hours[$btr["bill_id"]]+=$btr["time_real"];
			$bill_customer_hours[$btr["bill_id"]]+=$btr["time_to_cust"];
		}

		$tg = array();
		$tc = array();
		$tr = array();
		$bill_at = array();
		$tb = 0;
		$meetings = array();
		$bills_payments = array();
		$calls_in = array();
		$calls_out = array();
		$bill_no = array();
		$sum = array();
		$name_objects = new object_list();
		$pm_bill_rh = array();
		$pm_bill_ch = array();

		$projects = $project_to_manager = array();
		foreach($bills_data as $bill_data)
		{
			foreach($bill_data["project"] as $bill_project)
			{
				if($this->can("view" , $bill_project))
				{
					$projects[$bill_project] = $bill_project;
				}
			}
		}

		foreach($this->stats_rows_data as $row)
		{
			if($this->can("view" , reset($row["project"])))
			{
				$projects[reset($row["project"])] = reset($row["project"]);
			}
		}

		$projects_ol = new object_list();
		$projects_ol->add($projects);
		foreach($projects_ol->arr() as $project_o)
		{
			if($project_o->prop("proj_mgr"))
			{
				$project_to_manager[$project_o->id()] = $project_o->prop("proj_mgr");
			}
			else
			{
				$project_to_manager[$project_o->id()] = 1;
			}
		}

		foreach($bills_data as $bill_data)
		{
			$bill_proj_mgr = null;
			foreach($bill_data["project"] as $bill_project)
			{
				if($project_to_manager[$bill_project] > 1)
				{
					if($bill_proj_mgr && $bill_proj_mgr != $project_to_manager[$bill_project])
					{
						// arr("erinevad projektijuhid");
						// arr($bill_data["project"]);
					}
					$bill_proj_mgr = $project_to_manager[$bill_project];
				}
			}
			if(!$bill_proj_mgr && (!$bill_data["customer"] || $bill_data["customer"] == $company))
			{
				continue;
			}

			$bills_no[$bill_proj_mgr]++;
			$bills_sum[$bill_proj_mgr]+=$bill_data["sum"];
			if($bill_data["state"] == 3)
			{
				$bills_payments[$bill_proj_mgr]+=$bill_data["sum"];
			}
			$bill_at[$bill_proj_mgr]+= $bill_hours[$bill_data["oid"]];
			$pm_bill_rh[$bill_proj_mgr]+= $bill_real_hours[$bill_data["oid"]];
			$pm_bill_ch[$bill_proj_mgr]+= $bill_customer_hours[$bill_data["oid"]];
		}

		foreach($this->stats_rows_data as $row)
		{
			$customer = null;
			foreach($row["customer"] as $cust)
			{
				if($customer != $company)
				{
					break;
				}
				$customer = $cust;
			}

			if($customer == $company)
			{
				continue;
			}

			$project = reset($row["project"]);
			$manager = $project_to_manager[$project];
			$bill_rows[$manager][$tr2br[$row["oid"]]] = $tr2br[$row["oid"]];
			if($row["task.class_id"] == CL_CRM_MEETING)
			{
				$meetings[$manager][$row["task"]] = $row["task"];
				$meetings["all"][$row["task"]] = $row["task"];
			}
			if($row["task.class_id"] == CL_CRM_CALL)
			{
				if($calls_data[$row["task"]]["promoter"] == 0)
				{
					$calls_out[$manager][$row["task"]] = $row["task"];
					$calls_out["all"][$row["task"]] = $row["task"];
				}
				else
				{
					$calls_in[$manager][$row["task"]] = $row["task"];
					$calls_in["all"][$row["task"]] = $row["task"];
				}
			}
			$tr[$manager]+= $row["time_real"];
			$tg[$manager]+= $row["time_guess"];
			$tc[$manager]+= $row["time_to_cust"];

			if($this->can("view" , $person))
			{
				$name_objects->add($person);
			}
		}

		foreach($tr as $pers => $sum_hours)
		{
			if(!$sum_hours || !$this->can("view" , $pers))
			{
//				continue;
			}
			$data = array();


			$data["at"] = 0;
			foreach($bill_rows[$pers] as $br)
			{
				$data["at"]+= $br_data[$br]["amt"];
			}
			$tb+= $data["at"];
			if($pers == 1)
			{
				$data["name"] = t("(Projektijuht M&auml;&auml;ramata)");
			}
			elseif($this->can("view", $pers))
			{
				$person = obj($pers);
				$data["name"] = html::href(array(
					"url" => "javascript:gt_change(".$pers.");" ,
					"caption" => $person->name(),
				));
			}
			else
			{
				$data["name"] = t("(Projekt m&auml;&auml;ramata)");
			}
			$data["tt"] = round($tr[$pers] , 2);
			$data["kt"] = $tc[$pers];
			$data["pt"] = $tg[$pers];
			$data["meetings"] = is_array($meetings[$pers]) ? sizeof($meetings[$pers]) : "";
			$data["calls_in"] = is_array($calls_in[$pers]) ? sizeof($calls_in[$pers]) : "";
			$data["calls_out"] = is_array($calls_out[$pers]) ? sizeof($calls_out[$pers]) : "";
			$data["sum"] =round($sum[$pers]);
			$diff = $data["kt"] - $data["tt"];
			$data["bills_no"] = $bills_no[$pers];
			$data["bills_sum"] = $bills_sum[$pers];
			$data["bills_payments"] = $bills_payments[$pers];
			$data["bills_hours"] = $bill_at[$pers];
			$data["bills_customer_hours"] = $pm_bill_ch[$pers];
			$data["bills_real_hours"] = $pm_bill_rh[$pers];
			$data["bills_hp"] = round($data["bills_sum"]/$data["bills_real_hours"] , 2);
			$t->define_data($data);
		}
		$t->set_default_sorder("desc");
		$t->set_default_sortby("sum");
		$t->sort_by();
		$t->set_sortable(false);
		$t->define_data(array(
			"name" => t("Kokku"),
			"tt" => round(array_sum($tr), 2),
			"kt" => round(array_sum($tc), 2),
			"pt" => round(array_sum($tg), 2),
			"at" => round($tb , 2),
			"sum" => round(array_sum($sum), 2),
			"meetings" => is_array($meetings["all"]) ? sizeof($meetings["all"]) : "",
			"calls_in" => is_array($calls_in["all"]) ? sizeof($calls_in["all"]) : "",
			"calls_out" => is_array($calls_out["all"]) ? sizeof($calls_out["all"]) : "",
			"bills_no" => array_sum($bills_no),
			"bills_sum" =>  round(array_sum($bills_sum) , 2),
			"bills_payments" =>  round(array_sum($bills_payments) , 2),
		));
	}

	private function _get_stats_workers($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$this->_init_workers_t($t);

		if(!$this->stats_rows_data)
		{
			$this->stats_rows_data = $this->get_rows_data($arr["request"]);
		}

		$bugs_data = $this->get_bugs_data($arr["request"]);
		$calls_data = $this->get_calls_data($arr["request"]);
		$meetings_data = $this->get_meetings_data($arr["request"]);
		$tasks_data = $this->get_tasks_data($arr["request"]);

		$tg = array();
		$tc = array();
		$tr = array();
		$tb = array();
		$meetings = array();
		$calls_in = array();
		$calls_out = array();
		$hours_cust = array();
		$hours_dev = array();
		$sum = array();
		$u = new user();
		$company = $u->get_current_company();
		$name_objects = new object_list();

		foreach($this->stats_rows_data as $row)
		{
			$person = reset($row["impl"]);
			if($row["task.class_id"] == CL_CRM_MEETING)
			{
				$meetings[$person][$row["task"]] = $row["task"];
				$meetings["all"][$row["task"]] = $row["task"];
				$sum[$person]+= $meetings_data[$row["task"]]["hr_price"] * $row["time_to_cust"];
			}
			if($row["task.class_id"] == CL_CRM_CALL)
			{
				if($calls_data[$row["task"]]["promoter"] == 0)
				{
					$calls_out[$person][$row["task"]] = $row["task"];
					$calls_out["all"][$row["task"]] = $row["task"];
				}
				else
				{
					$calls_in[$person][$row["task"]] = $row["task"];
					$calls_in["all"][$row["task"]] = $row["task"];
				}
				$sum[$person]+= $calls_data[$row["task"]]["hr_price"] * $row["time_to_cust"];
			}
			if($row["task.class_id"] == CL_TASK)
			{
				$sum[$person]+= $tasks_data[$row["task"]]["hr_price"] * $row["time_to_cust"];
			}
			else
			{
				$sum[$person]+= $bugs_data[$row["task"]]["hr_price"] * $row["time_to_cust"];
			}
			$tr[$person]+= $row["time_real"];
			$tg[$person]+= $row["time_guess"];
			$tc[$person]+= $row["time_to_cust"];
			$customer = reset($row["customer"]);
			if(!$customer || $customer == $company)
			{
				$hours_dev[$person]+= $row["time_real"];
			}
			else
			{
				$hours_cust[$person]+= $row["time_real"];
			}


		//	$hours_cust[$person]+= ((reset($row["customer"]) == $company || !is_oid(reset($row["customer"])))) ? 0 : $row["time_real"]);
		//	$hours_dev[$person]+= ((reset($row["customer"]) == $company || !is_oid(reset($row["customer"])))) ? $row["time_real"] : 0);
			if($row["bill_id"])
			{
				$tb[$person]+= $row["time_to_cust"];
			}
			if($this->can("view" , $person))
			{
				$name_objects->add($person);
			}
		}

		foreach($tr as $pers => $sum_hours)
		{
			if(!$sum_hours)
			{
				continue;
			}
			$data = array();
			if($this->can("view" , $pers))
			{
				$person = obj($pers);
				$data["name"] = html::href(array(
					"url" => "javascript:gt_change(".$pers.");" ,
					"caption" => $person->name(),
				));
				$data["sort_name"] = $person->name();
			}
			else
			{
				$data["name"] = t("(Nimetu)");
				$data["sort_name"] = t("(Nimetu)");
			}

			$data["tt"] = round($tr[$pers] , 2);
			$data["kt"] = $tc[$pers];
			$data["pt"] = $tg[$pers];
			$data["at"] = $tb[$pers];
			$data["meetings"] = is_array($meetings[$pers]) ? sizeof($meetings[$pers]) : "";
			$data["calls_in"] = is_array($calls_in[$pers]) ? sizeof($calls_in[$pers]) : "";
			$data["calls_out"] = is_array($calls_out[$pers]) ? sizeof($calls_out[$pers]) : "";

			$data["cust"] = $hours_cust[$pers];
			$data["our"] =$hours_dev[$pers];

//			$data["sum"] = $meeting->prop("hr_price") * $data["kt"];
			$data["sum"] =round($sum[$pers]);
			$diff = $data["kt"] - $data["tt"];
			$data["sum_plus"] += $diff > 0 ? $diff : 0;
			$data["sum_minus"] += $diff < 0 ? -$diff : 0;

			$t->define_data($data);
		}
		$t->set_default_sorder("desc");
		$t->set_default_sortby("sum");
		$t->sort_by();
		$t->set_sortable(false);
		$t->define_data(array(
			"name" => t("Kokku"),
			"tt" => round(array_sum($tr), 2),
			"kt" => round(array_sum($tc), 2),
			"pt" => round(array_sum($tg), 2),
			"at" => round(array_sum($tb), 2),
			"our" => round(array_sum($hours_dev), 2),
			"sum" => round(array_sum($sum), 2),
			"cust" => round(array_sum($hours_cust) , 2),
			"meetings" => is_array($meetings["all"]) ? sizeof($meetings["all"]) : "",
			"calls_in" => is_array($calls_in["all"]) ? sizeof($calls_in["all"]) : "",
			"calls_out" => is_array($calls_out["all"]) ? sizeof($calls_out["all"]) : "",
		));
	}

	private function _init_meetings_t($t)
	{
		$t->define_field(array(
			"name" => "id",
			"caption" => t("ID"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "time",
			"caption" => t("Toimumisaeg"),
			"align" => "left"
		));

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "left"
		));

		$t->define_field(array(
			"name" => "cust",
			"caption" => t("Klient"),
			"align" => "left"
		));

		$t->define_field(array(
			"name" => "person",
			"caption" => t("Osalejad"),
			"align" => "left"
		));

		$t->define_field(array(
			"name" => "pt",
			"caption" => t("<a href='javascript:void(0)' alt='Prognoositud tunde' title='Prognoositud tunde'>PT</a>"),
			"align" => "right"
		));

		$t->define_field(array(
			"name" => "tt",
			"caption" => t("<a href='javascript:void(0)' alt='Tegelikult tunde' title='Tegelikult tunde'>TT</a>"),
			"align" => "right"
		));

		$t->define_field(array(
			"name" => "kt",
			"caption" => t("<a href='javascript:void(0)' alt='Tunde Kliendile' title='Tunde Kliendile'>KT</a>"),
			"align" => "right"
		));
/*
		$t->define_field(array(
			"name" => "sum_plus",
			"caption" => t("Vahe (+)"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "sum_minus",
			"caption" => t("Vahe (-)"),
			"align" => "center"
		));
*/
		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa"),
			"align" => "right"
		));
	}

	private function _get_stats_different_tasks($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		if($arr["request"]["class_id"] == CL_CRM_MEETING)
		{
			$meetings = $this->get_meetings($arr["request"]);
		}
		if($arr["request"]["class_id"] == CL_CRM_CALL)
		{
			$meetings = $this->get_calls($arr["request"]);
		}
		if($arr["request"]["class_id"] == CL_TASK)
		{
			$meetings = $this->get_tasks($arr["request"]);
		}

		$this->_init_meetings_t($t);
		$arr["request"]["parent_clid"] = $arr["request"]["class_id"];
		$meeting_rows = $this->get_rows_data($arr["request"]);
		$tg = array();
		$tc = array();
		$tr = array();
		$parts = array();

		$sum = $hours = $hcust = $hguess = 0;

		$name_objects = new object_list();

		foreach($meeting_rows as $row)
		{
			$tr[$row["task"]]+= $row["time_real"];
			$tg[$row["task"]]+= $row["time_guess"];
			$tc[$row["task"]]+= $row["time_to_cust"];
			foreach($row["impl"] as $imp)
			{
				$parts[$row["task"]][$imp] = $imp;
				if($this->can("view" , $imp))
				{
					$name_objects->add($imp);
				}
			}
		}

		foreach($meetings->arr() as $meeting)
		{
			$name_objects->add($meeting->get_orderer_ids());
		}
		$names = $name_objects->names();

		foreach($meetings->arr() as $meeting)
		{
			$impl = $orderers = array();
			foreach($parts[$meeting->id()] as $imp)
			{
				$impl[] = $this->js_obj_url($imp , $names[$imp]);
			}
			foreach($meeting->get_orderer_ids() as $or)
			{
				$orderers[] = html::href(array(
					"url" => "javascript:gt_change(".$or.");" ,
					"caption" => $names[$or],
				));
			}
			$data = array();
			$data["id"] = $meeting->id();
			$data["time"] = date("d.m.Y H:i" , $meeting->prop("start1"))." - ".date("d.m.Y H:i" , $meeting->prop("end"));
			$data["name"] = html::href(array(
				"url" => "javascript:gt_change(".$meeting->id().");" ,
				"caption" => $meeting->name(),
			));
			$data["cust"] = join("<br>" , $orderers);
	//		$data["proj"] = join(" ," , $meeting->get_projects()->names());
			$data["person"] = join("<br>" , $impl);
			$data["tt"] = $tr[$meeting->id()];
			$data["kt"] = $tc[$meeting->id()];
			$data["pt"] = $tg[$meeting->id()];
			$data["sum"] = $meeting->prop("hr_price") * $data["kt"];
			$sum += $data["sum"];
			$diff = $data["kt"] - $data["tt"];
			$data["sum_plus"] += $diff > 0 ? $diff : 0;
			$data["sum_minus"] += $diff < 0 ? -$diff : 0;

			$t->define_data($data);
		}
		$t->set_sortable(false);
		$t->define_data(array(
			"name" => t("Kokku"),
			"tt" => array_sum($tr),
			"kt" => array_sum($tc),
			"pt" => array_sum($tg),
			"sum" => $sum,
		));
	}

	public function js_obj_url($id , $name)
	{
		if(!$id)
		{
			return $name;
		}

		if(!$name)
		{
			$name = "(".t("Nimetu").")";
		}

		return html::href(array(
			"url" => "javascript:gt_change(".$id.");" ,
			"caption" => $name,
		));
	}

 	private function get_rows_data($f = array())
	{
		$bc_filt = array(
			"class_id" => CL_TASK_ROW
		);

		if(!empty($f["between"]))
		{
			$bc_filt["date"] = $f["between"];
		}

		if(!empty($f["parent_clid"]))
		{
			$bc_filt["task.class_id"] = $f["parent_clid"];
		}

		if(!empty($f["bill_between"]))
		{
			$bc_filt["bill_id.bill_date"] = $f["bill_between"];
		}

		if(!empty($f["customer"]))
		{
			$bc_filt["CL_TASK_ROW.RELTYPE_CUSTOMER"] = $f["customer"];
		}

		$rows_data_list = new object_data_list(
			$bc_filt,
			array(CL_TASK_ROW => array(
				"task", "date","time_guess" , "time_real" , "time_to_cust","impl", "bill_id","task.class_id", "customer", "project"
			))
		);
		$cache = $rows_data_list->arr();
		return $cache;
	}

	private function get_meetings_data($arr)
	{
		$filter = $this->get_meetings_filter($arr);
		$rows_data_list = new object_data_list(
			$filter,
			array(CL_CRM_MEETING => array(
				"hr_price"
			))
		);
		return $rows_data_list->arr();
	}

	public function get_meetings($arr)
	{
		$filter = $this->get_meetings_filter($arr);
		$ol = new object_list($filter);
		return $ol;
	}

	public function get_calls($arr)
	{
		$filter = $this->get_calls_filter($arr);
		$ol = new object_list($filter);
		return $ol;
	}

	public function get_bugs($arr)
	{
		$filter = $this->get_bugs_filter($arr);
		return new object_list($filter);
	}

	private function get_bugs_data($arr)
	{
		$filter = $this->get_bugs_filter($arr);
		$rows_data_list = new object_data_list(
			$filter,
			array(CL_BUG => array(
				"hr_price"
			))
		);
		return $rows_data_list->arr();
	}

	public function get_tasks($arr)
	{
		$filter = $this->get_tasks_filter($arr);
		$ol = new object_list($filter);
		return $ol;
	}

	private function get_tasks_data($arr)
	{
		$filter = $this->get_tasks_filter($arr);
		$rows_data_list = new object_data_list(
			$filter,
			array(CL_TASK => array(
				"hr_price"
			))
		);
		return $rows_data_list->arr();
	}

	private function get_calls_data($arr)
	{
		$filter = $this->get_calls_filter($arr);
		$rows_data_list = new object_data_list(
			$filter,
			array(CL_CRM_CALL => array(
				"hr_price", "promoter"
			))
		);
		return $rows_data_list->arr();
	}

	private function get_payments_data($arr)
	{
		$filter = $this->get_payments_filter($arr);
		$rows_data_list = new object_data_list(
			$filter,
			array(CL_CRM_BILL_PAYMENT => array(
				"sum", "customer"
			))
		);
		return $rows_data_list->arr();
	}

	private function get_payments_filter($arr)
	{
		$filter = array();
		$filter["class_id"] = CL_CRM_BILL_PAYMENT;
		$filter["brother_of"] = new obj_predicate_prop("id");
		if (!empty($arr["between"]))
		{
			$filter["date"] = $arr["between"];
		}
		return $filter;
	}

	private function get_bills_data($arr)
	{
		$filter = $this->get_bills_filter($arr);
		$rows_data_list = new object_data_list(
			$filter,
			array(CL_CRM_BILL => array(
				"sum", "customer","state","project"
			))
		);
		return $rows_data_list->arr();
	}

	private function get_bills_rows_data($arr)
	{
		$filter = $this->get_bill_rows_filter($arr);
		$rows_data_list = new object_data_list(
			$filter,
			array(CL_CRM_BILL_ROW => array(
				"amt" , "task_row"
			))
		);
		return $rows_data_list->arr();
	}

	private function get_bill_rows_filter($arr)
	{
		$filter = array();
		$filter["class_id"] = CL_CRM_BILL_ROW;

		if (!empty($arr["between"]))
		{
			$filter["CL_CRM_BILL_ROW.RELTYPE_ROW(CL_CRM_BILL).bill_date"] = $arr["between"];
		}

		return $filter;
	}

	private function get_bills_rows_by_tr_data($arr)
	{
		$filter = $this->get_bill_rows_by_tr_filter($arr);
		$rows_data_list = new object_data_list(
			$filter,
			array(CL_CRM_BILL_ROW => array(
				"amt" , "task_row"
			))
		);
		return $rows_data_list->arr();
	}

	private function get_bill_rows_by_tr_filter($arr)
	{
		$filter = array();
		$filter["class_id"] = CL_CRM_BILL_ROW;

		if (!empty($arr["between"]))
		{
			$filter["CL_CRM_BILL_ROW.RELTYPE_TASK_ROW.date"] = $arr["between"];
		}

		return $filter;
	}

	private function get_bills_filter($arr)
	{
		$filter = array();
		$filter["class_id"] = CL_CRM_BILL;
		$filter["brother_of"] = new obj_predicate_prop("id");

		if (!empty($arr["between"]))
		{
			$filter["bill_date"] = $arr["between"];
		}

		return $filter;
	}

	private function get_bugs_filter($arr)
	{
		$filter = array();
		$filter["class_id"] = CL_BUG;
		$filter["brother_of"] = new obj_predicate_prop("id");

		if (!empty($arr["status"]))
		{
			$filter["bug_status"] = $arr["status"];
		}

		if (!empty($arr["project_manager"]))
		{
			$filter["CL_BUG.RELTYPE_PROJECT.proj_mgr"] = $arr["project_manager"];
		}

		if (!empty($arr["customer"]))
		{
			$filter["customer"] = $arr["customer"];
		}

		if (!empty($arr["deadline"]))
		{
			$arr["done"] = 0;
			$filter["deadline"] = new obj_predicate_compare(OBJ_COMP_LESS, time());

		}

		if (isset($arr["done"]))
		{
			if ($arr["done"] == 1)
			{
				$filter["bug_status"] = array(3,4,5,6,7,8,9);
			}
			elseif ($arr["done"] === 0)
			{
				$filter["bug_status"] = array(1,2,10,11);
			}
		}

		if (!empty($arr["between"]))
		{
			$filter["CL_BUG.RELTYPE_COMMENT.date"] = $arr["between"];
		}

		if (!empty($arr["person"]))
		{
			$filter["who"] = $arr["person"];
		}

		return $filter;
	}

	private function get_tasks_filter($arr)
	{
		$filter = array();
		$filter["class_id"] = CL_TASK;
		$filter["brother_of"] = new obj_predicate_prop("id");

		if (!empty($arr["project_manager"]))
		{
			$filter["CL_TASK.RELTYPE_PROJECT.proj_mgr"] = $arr["project_manager"];
		}

		if (!empty($arr["customer"]))
		{
			$filter["CL_TASK.RELTYPE_CUSTOMER"] = $arr["customer"];
		}

		if (!empty($arr["deadline"]))
		{
			$arr["done"] = 0;
			$filter["deadline"] = new obj_predicate_compare(obj_predicate_compare::LESS, time());

		}

		if (isset($arr["done"]))
		{
			if ($arr["done"] == 1)
			{
				$filter["is_done"] = $arr["done"];
			}
			elseif ($arr["done"] === 0)
			{
				$filter["is_done"] = new obj_predicate_not(1);
			}
		}

		if (!empty($arr["between"]))
		{
			$filter["CL_TASK.RELTYPE_ROW.date"] = $arr["between"];
		}

		if (!empty($arr["person"]))
		{
			$filter["CL_CRM_CALL.RELTYPE_ROW.impl"] = $arr["person"];
		}
		return $filter;
	}

	private function get_meetings_filter($arr)
	{
		$filter = array();
		$filter["class_id"] = CL_CRM_MEETING;
		$filter["brother_of"] = new obj_predicate_prop("id");

		if (!empty($arr["project_manager"]))
		{
			$filter["CL_CRM_MEETING.RELTYPE_PROJECT.proj_mgr"] = $arr["project_manager"];
		}

		if (!empty($arr["customer"]))
		{
			$filter["CL_CRM_MEETING.RELTYPE_CUSTOMER"] = $arr["customer"];
		}

		if (!empty($arr["deadline"]))
		{
			$arr["done"] = 0;
			$filter["deadline"] = new obj_predicate_compare(obj_predicate_compare::LESS, time());
		}

		if (!empty($arr["person"]))
		{
			$filter["CL_CRM_MEETING.RELTYPE_ROW.impl"] = $arr["person"];
		}

		if (isset($arr["done"]))
		{
			if ($arr["done"] == 1)
			{
				$filter["is_done"] = $arr["done"];
			}
			elseif ($arr["done"] === 0)
			{
				$filter["is_done"] = new obj_predicate_not(1);
			}
		}

		if (!empty($arr["from"]))
		{
			$filter[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_CRM_MEETING.start1" => new obj_predicate_compare(obj_predicate_compare::GREATER, $arr["from"]),
					"CL_CRM_MEETING.RELTYPE_ROW.date" => new obj_predicate_compare(obj_predicate_compare::GREATER, $arr["from"]),
				)
			));
		}

		if (!empty($arr["to"]))
		{
			$filter[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_CRM_MEETING.start1" => new obj_predicate_compare(obj_predicate_compare::LESS, $arr["to"]),
					"CL_CRM_MEETING.RELTYPE_ROW.date" => new obj_predicate_compare(obj_predicate_compare::LESS, $arr["to"]),
				)
			));
		}
		return $filter;
	}

	private function get_calls_filter($arr)
	{
		$filter = array();
		$filter["class_id"] = CL_CRM_CALL;
		$filter["brother_of"] = new obj_predicate_prop("id");

		if (!empty($arr["project_manager"]))
		{
			$filter["CL_CRM_CALL.RELTYPE_PROJECT.proj_mgr"] = $arr["project_manager"];
		}

		// if (!empty($arr["client_manager"]))
		// {
			//	$filter["CL_TASK.RELTYPE_CUSTOMER"] = $arr["customer"];
		// }

		if (!empty($arr["customer"]))
		{
			$filter["CL_CRM_CALL.RELTYPE_CUSTOMER"] = $arr["customer"];
		}

		if (!empty($arr["deadline"]))
		{
			$arr["done"] = 0;
			$filter["deadline"] = new obj_predicate_compare(obj_predicate_compare::LESS, time());
		}

		if (isset($arr["done"]))
		{
			if ($arr["done"] == 1)
			{
				$filter["is_done"] = $arr["done"];
			}
			elseif($arr["done"] === 0)
			{
				$filter["is_done"] = new obj_predicate_not(1);
			}
		}

		if (!empty($arr["person"]))
		{
			$filter["CL_CRM_CALL.RELTYPE_ROW.impl"] = $arr["person"];
		}

		if (!empty($arr["from"]))
		{
			$filter[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_CRM_CALL.start1" => new obj_predicate_compare(obj_predicate_compare::GREATER, $arr["from"]),
					"CL_CRM_CALL.RELTYPE_ROW.date" => new obj_predicate_compare(obj_predicate_compare::GREATER, $arr["from"])
				)
			));
		}

		if (!empty($arr["to"]))
		{
			$filter[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_CRM_CALL.start1" => new obj_predicate_compare(obj_predicate_compare::LESS, $arr["to"]),
					"CL_CRM_CALL.RELTYPE_ROW.date" => new obj_predicate_compare(obj_predicate_compare::LESS, $arr["to"])
				)
			));
		}

		return $filter;
	}

}

