<?php

class crm_company_bills_impl extends class_base
{
	private $show_bill_balance = false;

	function crm_company_bills_impl()
	{
		$this->init();
	}

	function _init_bill_proj_list_t($t, $custs)
	{
		$t->define_field(array(
			"caption" => t("Ava"),
			"name" => "open",
			"align" => "center",
			"sortable" => 1,
			"valign" => "top"
		));

		$t->define_field(array(
			"caption" => t("Projekt"),
			"name" => "name",
			"align" => "center",
			"sortable" => 1,
			"valign" => "top"
		));

		$t->define_field(array(
			"caption" => t("Klient"),
			"name" => "cust",
			"align" => "center",
			"sortable" => 1,
			"valign" => "top",
			"filter" => $custs
		));

		$t->define_field(array(
			"caption" => t("Summa"),
			"name" => "sum",
			"align" => "right",
			"sortable" => 1,
			"valign" => "top",
			"width" => "50%"
		));
	}

	function _get_bill_proj_list($arr)
	{
		if ($arr["request"]["proj"] || $arr["request"]["cust"])
		{
			return PROP_IGNORE;
		}

	//-----------------------konvertimise algoritm
/*	$cnt = 0;

	if(aw_global_get("uid") == "marko"){
		$tasks = new object_list(array(
			"class_id" => CL_TASK,
			"lang_id" => array(),
			"site_id" => array(),
			"brother_of" => new obj_predicate_prop("id"),
		));
		arr($tasks->count());
		foreach($tasks->arr() as $task)
		{
			foreach($task->get_all_rows() as $row_id)
			{$row = obj($row_id);
				if($row->prop("task")) continue;
				$cnt++;

				print "rida id=".$row_id." nimi=".$row->name()." saab taski id=".$task->id()." nimega ".$task->name()."<br>\n";
				$row->set_prop("task", $task->id());
				$row->save();
			}
		}
		arr($cnt);
	}
*/

/*
		$bc = new object_list(array(
			"class_id" => CL_BUG_COMMENT,
	//		"bug" => new obj_predicate_compare(OBJ_COMP_LESS, 1),
	//		"is_done" => 1,
			"created" => new obj_predicate_compare(OBJ_COMP_GREATER,  1204351200),
			"lang_id" => array(),
//			"brother_of" => new obj_predicate_prop("id"),
		));
*//*
		$org_arr = new object_data_list(
		array(
			"class_id" => CL_BUG_COMMENT,
	//		"bug" => new obj_predicate_compare(OBJ_COMP_LESS, 1),
	//		"is_done" => 1,
			"created" => new obj_predicate_compare(OBJ_COMP_GREATER,   1199167200),
			"lang_id" => array(),
//			"brother_of" => new obj_predicate_prop("id"),
		),
			array
			(
				CL_BUG_COMMENT => array(
					"oid" => "oid",
					"name" => "name",
					"bug" => "bug",
				)
			)
		);


*/


//---------------------kokkuleppehinna konvertimise algoritm

/*			$this->db_add_col("planner", array(
					"name" => "deal_has_tax",
					"type" => "int"
				));
				$this->db_add_col("planner", array(
					"name" => "deal_unit",
					"type" => "varchar(31)"
				));
				$this->db_add_col("planner", array(
					"name" => "deal_amount",
					"type" => "double"
				));
				$this->db_add_col("planner", array(
					"name" => "deal_price",
					"type" => "double"
				));*/
/*		$all_tasks = new object_list(array(
			"class_id" => array(CL_TASK, CL_CRM_MEETING,CL_CRM_CALL),
	//		"send_bill" => 1,
	//		"is_done" => 1,
			"lang_id" => array(),
			"brother_of" => new obj_predicate_prop("id"),
		));
		foreach($all_tasks->arr() as $row)
		{
			if($row->meta("deal_price"))
			{
				$row->set_prop("deal_price" , $row->meta("deal_price"));
				$row->set_prop("deal_amount" , $row->meta("deal_amount"));
				$row->set_prop("deal_unit" , $row->meta("deal_unit"));
				$row->set_prop("deal_unit" , $row->meta("deal_has_tax"));
			$row->save();
			}
		}*/
/*
if(aw_global_get("uid") == "marko")
{
aw_set_exec_time(AW_LONG_PROCESS);
ini_set("memory_limit", "800M");
	$all_tasks = new object_list(array(
		"class_id" => CL_TASK_ROW,
		"lang_id" => array(),
		"name" => "konverditudbugikommentaarist",
//		"limit" => 100,
	));
	$u = get_instance(CL_USER);

	arr($all_tasks->count());flush();
	foreach($all_tasks->arr() as $bug_comment)
	{
		foreach($bug_comment->connections_from(array("type" => 2,)) as $delconnection)
		{
			$delconnection->delete();
		}
		$person = $u->get_person_for_uid($bug_comment->createdby());
		$bug_comment->set_prop("impl",$person->id());
		$bug_comment->save();
	}
}
*/

/*
if(aw_global_get("uid") == "marko")
{
aw_set_exec_time(AW_LONG_PROCESS);
ini_set("memory_limit", "800M");
//bugi kommentaaride toimetuse ridadeks konvertimise algoritm
		$all_tasks = new object_list(array(
			"class_id" => CL_BUG_COMMENT,
			"lang_id" => array(),
 //			"limit" => 1,
			"created" => new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, 1212296400),
		));
//$all_tasks = new object_list();
//$all_tasks->add(221302);
arr($all_tasks->count());flush();
$x = 0;
		foreach($all_tasks->arr() as $bug_comment)
		{//arr($bug_comment->properties()); die();
			$tr = obj();
			$tr->set_class_id(1050);
			$tr->set_parent($bug_comment->parent());
			$tr->set_name("konverditudbugikommentaarist");
			$tr->save();

			$tr->set_prop("date" , $bug_comment->created());
			$tr->set_prop("done" , 1);
			$asd = array("acldata" , "meta","subclass" , "flags","site_id", "lang_id" , "alias" , "visible" , "jrk" , "last" , "hits" , "oid" , "status" , "name" ,"brother_of" , "parent", "class_id","metadata", "period", "created", "modified", "periodic", "createdby", "modifiedby");
			foreach($bug_comment->properties() as $prop => $val)
			{
				if(in_array($prop , $asd))continue;
				$tr->set_prop($prop , $val);
			}

			$bug = obj($bug_comment->parent());
			if(is_oid($bug->id()))
			$bug->connect(array(
				"to" => $tr->id(),
				"type" => "RELTYPE_COMMENT",
			));
			$tr->save();
			$this->db_query("UPDATE objects set createdby='".$bug_comment->createdby()."' , created='".$bug_comment->created()."'  WHERE oid=".$tr->id());
print $x."<br>";flush();
	$bug_comment->delete();
$x++;
//arr($tr);
		}

}
*/

		$t = $arr["prop"]["vcl_inst"];
		$this->get_time_between($arr["request"]);

		$format = t('%s maksmata t&ouml;&ouml;d');
		//$t->set_caption(sprintf($format, $arr['obj_inst']->name()));
		$this->sum2proj = array();
		$this->deal_tasks_for_project = array();
		$this->rows_for_project = array();
		$this->bugs_for_project = array();
		$this->task_hour_prices = array();
		$this->tasks_hours = array();

//--------------------------kokkuleppehinnaga taskid
		$deal_task_ol = new object_list(array(
			"class_id" => array(CL_TASK, CL_CRM_MEETING,CL_CRM_CALL),
			"send_bill" => 1,
			"lang_id" => array(),
			"brother_of" => new obj_predicate_prop("id"),
			"deal_price" => new obj_predicate_compare(OBJ_COMP_GREATER, 0),
		));
		$this->deal_tasks = $deal_task_ol->ids();
		foreach($deal_task_ol->arr() as $row)
		{
			foreach($row->connections_from(array(
				"type" => "RELTYPE_PROJECT"
			)) as $c)
			{
				$this->deal_tasks_for_project[$c->prop("to")][] = $row->id();
				$this->sum2proj[$c->prop("to")] += $row->prop("deal_price");
			}
		}

//--------------list all task rows that are not billed yet
		$rows_filter = array(
			"class_id" => CL_TASK_ROW,
			"bill_id" => new obj_predicate_compare(OBJ_COMP_EQUAL, ''),
			"on_bill" => 1,
			"done" => 1,
//			new object_list_filter(array(
//				"logic" => "OR",
//				"conditions" => array(
//					"CL_TASK_ROW.task(CL_TASK).send_bill" => 1,
//					"CL_TASK_ROW.task(CL_BUG).send_bill" => 1,
//					"CL_TASK_ROW.task(CL_CRM_MEETING).send_bill" => 1,
//					"CL_TASK_ROW.task(CL_CRM_CALL).send_bill" => 1,
//				)
//			)),
			"date" => new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $this->search_start, $this->search_end),
		);
		$rowsres = array(
			CL_TASK_ROW => array(
				"task" => "task",
				"project" => "project",
				"time_to_cust" => "time_to_cust",
				"time_real" => "time_real",
				"task.class_id" => "type",
			),
		);
		$rows_arr = new object_data_list($rows_filter , $rowsres);

		foreach($rows_arr->list_data as $bcs)
		{
			if($bcs["time_to_cust"])
			{
				$this->tasks_hours[$bcs["task"]]+= $bcs["time_to_cust"];
			}
			else
			{
				$this->tasks_hours[$bcs["task"]]+= $bcs["time_real"];
			}
			if(!is_array($bcs["project"]))
			{
				$bcs["project"] = array($bcs["project"]);
			}
			foreach($bcs["project"] as $project)
			{
				if($bcs["type"] == CL_BUG)
				{
					$this->bugs_for_project[$project][$bcs["task"]] = $bcs["task"];
					$this->bug_comments[$bcs["task"]][] = $bcs["oid"];
				}
				else
				{
					$this->rows_for_project[$project][] = $bcs["oid"];
				}
			}
		}

//----------------ridadele hinnad
		$tasks_data = array();
		$tasks_filter = array(
			"class_id" => array(CL_TASK),
			"oid" => array_keys($this->tasks_hours)
		);
		$tasksres = array(
			CL_TASK => array(
				"hr_price" => "hr_price",
				"project" => "project",
			),
		);
		$tasks_arr = new object_data_list($tasks_filter , $tasksres);
		$tasks_data = $tasks_arr->list_data;

		$tasks_filter = array(
			"class_id" => array(CL_BUG),
			"oid" => array_keys($this->tasks_hours)
		);
		$tasksres = array(
			CL_BUG => array(
				"hr_price" => "hr_price",
				"project" => "project",
			),
		);
		$tasks_arr = new object_data_list($tasks_filter , $tasksres);
		$tasks_data+= $tasks_arr->list_data;

		$tasks_filter = array(
			"class_id" => array(CL_CRM_MEETING),
			"oid" => array_keys($this->tasks_hours)
		);
		$tasksres = array(
			CL_CRM_MEETING => array(
				"hr_price" => "hr_price",
				"project" => "project"
			)
		);
		$tasks_arr= new object_data_list($tasks_filter , $tasksres);
		$tasks_data+= $tasks_arr->list_data;

		$tasks_filter = array(
			"class_id" => array(CL_CRM_CALL),
			"oid" => array_keys($this->tasks_hours)
		);
		$tasksres = array(
			CL_CRM_CALL => array(
				"hr_price" => "hr_price",
				"project" => "project"
			)
		);
		$tasks_arr = new object_data_list($tasks_filter , $tasksres);
		$tasks_data+=$tasks_arr->list_data;

		foreach($tasks_data as $bcs)
		{
			$this->task_hour_prices[$bcs["oid"]] = $bcs["hr_price"];
			if(!is_array($bcs["project"]))
			{
				$bcs["project"] = array($bcs["project"]);
			}
			foreach($bcs["project"] as $project)
			{
				$this->sum2proj[$project]+= $this->tasks_hours[$bcs["oid"]] * $bcs["hr_price"];
			}
		}

//--------------------------muud kulud---------
		$this->expenses_for_project = array();
		$other_expenses = new object_list(array(
			"class_id" => CL_CRM_EXPENSE,
			"bill_id" => '',
			"RELTYPE_TASK.send_bill" => 1,
		));

		foreach($other_expenses->arr() as $row)
		{
			$task = $row->prop("task");
			foreach($task->connections_from(array(
				"type" => "RELTYPE_PROJECT"
			)) as $c)
			{
				$this->expenses_for_project[$c->prop("to")][] = $row->id();
				$this->sum2proj[$c->prop("to")] += $row->prop("cost");
			}
		}

		$custs = array();
		foreach($this->sum2proj as $p => $sum)
		{
			if (!$this->can("view", $p))
			{
				continue;
			}
			$po = obj($p);
			$ord = $po->get_orderer();
			$ord_name = "";
			if($this->can("view" , $ord))
			{
				$orderer = obj($ord);
				$ord_name = $orderer->name();
			}

			$lister = "<span id='cust".$po->id()."' style='display: none;'>";

			$table = new vcl_table;
			$table->name = "cust".$po->id();
			$params = array(
				"request" => array("proj" => $po->id(), "cust" => $ord),
				"prop" => array(
					"vcl_inst" => &$table
				)
			);
			$this->_get_bill_task_list($params);

			$lister .= $table->draw();
			$lister .= "</span>";
			$dat[] = array(
				"name" => html::obj_change_url($po),
				"open" => html::href(array(
					"url" => "#", //aw_url_change_var("proj", $p),
					"onClick" => "el=document.getElementById(\"cust".$po->id()."\"); if (navigator.userAgent.toLowerCase().indexOf(\"msie\")>=0){if (el.style.display == \"block\") { d = \"none\";} else { d = \"block\";} } else { if (el.style.display == \"table-row\") {  d = \"none\"; } else {d = \"table-row\";} }  el.style.display=d;",
					"caption" => t("Ava")
				)),
				"cust" => html::obj_change_url($ord),
				"sum" => number_format($this->sum2proj[$p], 2).$lister
,				"cust_name" => $ord_name,
			);
			if ($this->can("view", $ord))
			{
				$ordo = obj($ord);
				$custs[] = $ordo->name();
			}
		}
		sort($custs);
		$this->_init_bill_proj_list_t($t, array_unique($custs));
		foreach($dat as $dr)
		{
			$t->define_data($dr);
		}

		$t->set_default_sortby("cust_name");
		return;

		// get all open tasks
		$i = get_instance(CL_CRM_COMPANY);
		//$proj = $i->get_my_projects();
		$proj_i = get_instance(CL_PROJECT);
		$ol = new object_list(array(
			"class_id" => CL_PROJECT,
			"site_id" => array(),
			"lang_id" => array()
		));

		foreach($ol->ids() as $p)
		{
			$events = $proj_i->get_events(array(
				"id" => $p,
				"range" => array(
					"start" => 1,
					"end" => time() + 24*3600*365*10
				)
			));
			if (!count($events))
			{
				continue;
			}
			$evt_ol = new object_list(array(
				"class_id" => CL_TASK,
				"oid" => array_keys($events),
				"bill_no" => new obj_predicate_compare(OBJ_COMP_EQUAL, ""),
				"send_bill" => 1
			));
			if (!$evt_ol->count())
			{
				continue;
			}
			$sum = 0;
			$task_i = get_instance(CL_TASK);
			$has_rows = false;
			foreach($evt_ol->arr() as $evt)
			{
				if (!$evt->prop("send_bill"))
				{
					continue;
				}
				$rows = $task_i->get_task_bill_rows($evt);
				if (!count($rows))
				{
					continue;
				}
				foreach($rows as $row)
				{
					if (!$row["bill_id"])
					{
						$has_rows = true;
						$sum += $row["sum"];
					}
				}
			}

			if (!$has_rows)
			{
				continue;
			}
			$po = obj($p);
			$t->define_data(array(
				"name" => html::obj_change_url($po),
				"open" => html::href(array(
					"url" => aw_url_change_var("proj", $p),
					"caption" => t("Ava")
				)),
				"cust" => html::obj_change_url(reset($po->prop("orderer"))),
				"sum" => number_format($sum, 2)
			));
		}
	}

	function _init_bill_task_list_t(&$t, $proj)
	{
		$t->define_field(array(
			"caption" => t("Juhtumi nimi"),
			"name" => "name",
			"align" => "center",
			"sortable" => 1
		));

		$t->define_field(array(
			"caption" => t("Tunde"),
			"name" => "hrs",
			"align" => "right",
			"sortable" => 1
		));

		$t->define_field(array(
			"caption" => t("Tunni hind"),
			"name" => "hr_price",
			"align" => "right",
			"sortable" => 1
		));

		$t->define_field(array(
			"caption" => t("Summa"),
			"name" => "sum",
			"align" => "right",
			"sortable" => 1
		));

		$t->define_field(array(
			"caption" => t("Arvele m&auml;&auml;ramise kuup&auml;ev"),
			"name" => "set_date",
			"align" => "right",
			"sortable" => 1,
			"type" => "time",
			"format" => "d.m.Y"
		));

		$t->define_field(array(
			"name" => "count",
			"type" => "hidden",
		));

		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel".$proj
		));
	}

	function _get_bill_task_list($arr)
	{
		if (!$arr["request"]["proj"] && !$arr["request"]["cust"])
		{
			return PROP_IGNORE;
		}
		$t = $arr["prop"]["vcl_inst"];
		$t->unset_filter();
		$this->_init_bill_task_list_t($t, $arr["request"]["proj"]);
		$rows = new object_list();
		// list all task rows that are not billed yet
/*		$rows = new object_list(array(
			"class_id" => array(CL_TASK_ROW,CL_CRM_MEETING,CL_CRM_EXPENSE),
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
/*					new object_list_filter(array(
						"logic" => "AND",
						"conditions" => array(
							"class_id" => CL_TASK_ROW,
							"bill_id" => new obj_predicate_compare(OBJ_COMP_EQUAL, ''),
							"on_bill" => 1,
							"done" => 1,
						)
					)),
					new object_list_filter(array(
						"logic" => "AND",
						"conditions" => array(
							"class_id" => CL_CRM_MEETING,
							"send_bill" => 1,
							"bill_no" => new obj_predicate_compare(OBJ_COMP_EQUAL, ''),
							"flags" => array("mask" => OBJ_IS_DONE, "flags" => OBJ_IS_DONE)
						)
					)),
					new object_list_filter(array(
						"logic" => "AND",
						"conditions" => array(
							"class_id" => CL_CRM_EXPENSE,
	//						"on_bill" => 1,
	//						"bill_no" => new obj_predicate_compare(OBJ_COMP_EQUAL, ''),
//							"flags" => array("mask" => OBJ_IS_DONE, "flags" => OBJ_IS_DONE)
						)
					))
				)
			))
		));*/
		//kokkuleppehinna jaoks
		//see ka optimaalsemaks vaja tegelt
/*		$all_tasks = new object_list(array(
			"class_id" => CL_TASK,
			"send_bill" => 1,
	//		"is_done" => 1,
			"project" => $arr["request"]["proj"],
			"brother_of" => new obj_predicate_prop("id"),
		));*/

		$tasks = new object_list();
		$sum2task = array();
		$hr2task = array();
		$task2row = array();
//		$deal_tasks = array();
//		$possible_task_rows = $possible_expenses = array();

		foreach($this->deal_tasks_for_project[$arr["request"]["proj"]] as $deal_task)
		{
			$row = obj($deal_task);
			$rows->add($row->get_all_expenses());
			$t->define_data(array(
				"oid" => $row->id(),
				"name" => $row->name(),
				"sum" => $row->prop("deal_price").t("(Kokkuleppehind)").($row->prop("deal_has_tax") ? t("KMga") : ""),
				"set_date" => $row->prop("to_bill_date"),
			));
			$sum2task[$row->id()] += $row->prop("deal_price");
			$hr2task[$row->id()] += $row->prop("deal_amt");
		}
		if(isset($this->expenses_for_project[$arr["request"]["proj"]]) && sizeof($this->expenses_for_project[$arr["request"]["proj"]]))
		{
			$rows->add($this->expenses_for_project[$arr["request"]["proj"]]);
		}
		if(isset($this->rows_for_project[$arr["request"]["proj"]]) && sizeof($this->rows_for_project[$arr["request"]["proj"]]))
		{
			$rows->add($this->rows_for_project[$arr["request"]["proj"]]);
		}


		$co_stat_inst = get_instance("applications/crm/crm_company_stats_impl");
		foreach($rows-> arr() as $row)
		{
			if($row->class_id() == CL_CRM_EXPENSE)
			{
				$date = $row->prop("date");
				$t->define_data(array(
					"oid" => $row->id(),
					"name" => $ro->name(),
					"sum" => number_format(str_replace(",", ".", $row->prop("cost")),2),
					"set_date" => mktime(0,0,0, $date["month"], $date["day"], $date["year"]),
				));
			}
			else
			{
				if(!in_array($row->prop("task"), $this->deal_tasks))
				{
					$t->define_data(array(
						"oid" => $row->id(),
						"name" => $row->prop("content"),
						"hrs" => $co_stat_inst->hours_format($row->prop("time_to_cust")),
						"hr_price" => number_format($this->task_hour_prices[$row->prop("task")],2),
						"sum" => number_format(str_replace(",", ".", $row->prop("time_to_cust")) * $this->task_hour_prices[$row->prop("task")],2),
						"set_date" => $row->prop("to_bill_date"),
						"count" => html::hidden(array("name" => "count[".$row->prop("task")."]" , "value" => count($rs))),//mis jama see on?
					));
				}
			}
		}


/*
		if ($rows->count())
		{
			$c = new connection();
			$t2row = $c->find(array(
				"from.class_id" => CL_TASK,
				"to" => $rows->ids(),
				"type" => "RELTYPE_ROW"
			));
			foreach($t2row as $conn)
			{
				$task = obj($conn["from"]);
				$row = obj($conn["to"]);

				if ($task->prop("project") == $arr["request"]["proj"])
				{
					if(!in_array($task->id(), $this->deal_tasks))
					{
						$task2row[$task->id()][] = $row->id();
						$sum2task[$task->id()] += str_replace(",", ".", $row->prop("time_to_cust")) * $task->prop("hr_price");
						$hr2task[$task->id()] += str_replace(",", ".", $row->prop("time_to_cust"));
						$tasks->add($conn["from"]);
					}
				}
			}
		}

		if ($rows->count())
		{
			$c = new connection();
			$t2row = $c->find(array(
				"to.class_id" => CL_CRM_EXPENSE,
				"to" => $rows->ids(),
			));
			foreach($t2row as $conn)
			{
				$row = obj($conn["to"]);
				if($this->can("view" , $row->prop("bill_id")))
				{
					 continue;
				}
				if($row->prop("date") < $this->search_start || $row->prop("date") > $this->search_end)
				{
					continue;
				}

				$task = obj($conn["from"]);
				if ($task->prop("project") == $arr["request"]["proj"])
				{
					if(!in_array($task->id(), $this->deal_tasks))
					{
						$task2row[$task->id()][] = $row->id();
						$sum2task[$task->id()] += str_replace(",", ".", $row->prop("cost"));
						$tasks->add($conn["from"]);
					}
				}
			}
		}*/

/*
		foreach($tasks->ids() as $id)
		{
			$rs = $task2row[$id];
			if (count($rs))
			{
				foreach($rs as $row_id)
				{
					$ro = obj($row_id);
				//	$sel_ = 0;
				//	if(in_array($_SESSION["task_sel"],$row_id)) $sel_ =1;
					if($ro->class_id() == CL_CRM_EXPENSE)
					{
						$date = $ro->prop("date");
						$t->define_data(array(
							"oid" => $row_id,
							"name" => $ro->name(),
							"sum" => number_format(str_replace(",", ".", $ro->prop("cost")),2),
							"set_date" => mktime(0,0,0, $date["month"], $date["day"], $date["year"]),
						));
						continue;
					}
					$t->define_data(array(
						"oid" => $row_id,
						"name" => $ro->prop("content"),
						"hrs" => number_format(str_replace(",", ".", $ro->prop("time_to_cust")), 3),
						"hr_price" => number_format($this->task_hour_prices[$id],2),
						"sum" => number_format(str_replace(",", ".", $ro->prop("time_to_cust")) * $this->task_hour_prices[$id],2),
						"set_date" => $ro->prop("to_bill_date"),
						"count" => html::hidden(array("name" => "count[".$id."]" , "value" => count($rs))),
					));
				}
			}
			else
			{
				$t->define_data(array(
					"name" => html::obj_change_url($id),
					"oid" => $id,
					"hrs" => number_format($hr2task[$id], 3),
					"hr_price" => number_format($o->prop("hr_price"),2),
					"sum" => number_format($sum2task[$id],2)
				));
			}
		}*/
/*
		// list all meetings that are not billed yet
		$meetings = new object_list(array(
			"class_id" => CL_CRM_MEETING,
			"send_bill" => 1,
			"bill_no" => new obj_predicate_compare(OBJ_COMP_EQUAL, ''),
			"is_done" => 1,
			"project" => $arr["request"]["proj"],
		));
		foreach($meetings->arr() as $row)
		{
			$projs[$row->prop("project")] = $row->prop("project");
			$sum2proj[$row->prop("project")] += str_replace(",", ".", $row->prop("time_to_cust")) * $row->prop("hr_price");
		}
*/
		foreach($projs as $p)
		{
			$po = obj($p);
			$ord = $po->prop("orderer");
			$t->define_data(array(
				"name" => html::obj_change_url($po),
				"open" => html::href(array(
					"url" => aw_url_change_var("proj", $p),
					"caption" => t("Ava")
				)),
				"cust" => html::obj_change_url(reset($ord)),
				"sum" => number_format($sum2proj[$p], 2)
			));
		}
		$t->set_default_sorder("asc");
		$t->set_default_sortby("set_date");
		$t->sort_by();

		if(isset($this->bugs_for_project[$arr["request"]["proj"]]) && sizeof($this->bugs_for_project[$arr["request"]["proj"]]))
		{
			foreach($this->bugs_for_project[$arr["request"]["proj"]] as $id)
			{
				if (isset($this->bug_comments[$id]))
				{
					$bt = $this->tasks_hours[$id];
					$bug = obj($id);
					$hr_price = $bug->get_hour_price();
					$t->define_data(array(
						"name" => html::obj_change_url($bug)." ".html::href(array("caption" => t("(kommentaarid)") ,
						"url" => "javascript:aw_popup_scroll('".$this->mk_my_orb(
							"create_bill_bug_popup", array(
								"openprintdialog" => 1,
								"id" => $bug->id(),
								"start" => $this->search_start,
								"end" => $this->search_end,
						))."','".t("Bugide kommentaarid")."',800,600)")),
						"oid" => $bug->id(),
						"hrs" =>  $bt,
						"hr_price" => $hr_price,
						"sum" => number_format(($bt * $hr_price), 2)
					));
				}
			}
		}

		return;
		if ($arr["request"]["cust"])
		{
			$i = get_instance(CL_CRM_COMPANY);
			$arr["request"]["proj"] = $i->get_projects_for_customer(obj($arr["request"]["cust"]));
		}
		$proj_i = get_instance(CL_PROJECT);
		$events = array();
		$awa = new aw_array($arr["request"]["proj"]);
		foreach($awa->get() as $p)
		{
			$events += $proj_i->get_events(array(
				"id" => $p,
				"range" => array(
					"start" => 1,
					"end" => time() + 24*3600*365*10
				)
			));
		}

		$task_i = get_instance(CL_TASK);
		foreach($events as $evt)
		{
			$o = obj($evt["id"]);
			if ($o->prop("send_bill"))
			{
				if ($o->prop("bill_no") == "")
				{
					$sum = 0;
					$hrs = 0;
					// get task rows and calc sum from those
					$rows = $task_i->get_task_bill_rows($o);
					foreach($rows as $row)
					{
						if (!$row["bill_id"] && $row["is_done"])
						{
							$sum += $row["sum"];
							$hrs += $row["amt"];
						}
					}

					if ($sum > 0)
					{
						$t->define_data(array(
							"name" => html::get_change_url($o->id(), array("return_url" => get_ru()), parse_obj_name($o->name())),
							"oid" => $o->id(),
							"hrs" => $hrs,
							"hr_price" => number_format($o->prop("hr_price"),2),
							"sum" => number_format($sum,2)
						));
					}
				}
			}
		}
	}

	function _get_bill_tb($arr)
	{
		$_SESSION["create_bill_ru"] = get_ru();
		$tb =& $arr["prop"]["vcl_inst"];

		$tb->add_button(array(
			'name' => 'new',
			'img' => 'new.gif',
			'tooltip' => t('Lisa'),
			'url' => html::get_new_url(CL_CRM_BILL, $arr["obj_inst"]->id(), array("return_url" => get_ru()))
		));

		//if ($arr["request"]["proj"])
		//{
			$tb->add_button(array(
				"name" => "create_bill",
				"img" => "save.gif",
				"tooltip" => t("Koosta arve"),
				"url" => "
					javascript:var ansa = confirm('" . t("Kas koondada arendusulesannete kommentaarid?") . "');
					var asd = document.getElementsByName('bunch_bugs');
					if (ansa)
					{
						asd[0].value=1;
					}
					else
					{
						asd[0].value=0;
					}
					submit_changeform('create_bill');
				",
			//	"action" => "create_bill"
			));
		//}
		$tb->add_button(array(
			"name" => "search_bill",
			"img" => "search.gif",
			"tooltip" => t("Otsi"),
	//		"action" => "search_bill"
			"url" => "javascript:aw_popup_scroll('".$this->mk_my_orb("search_bill", array("openprintdialog" => 1,))."','Otsing',550,500)",
		));
	}

	/**
		@attrib name=search_bill
	**/
	function search_bill($arr)
	{
		if($_GET["sel"])
		{
			echo t("Valitud t&ouml;&ouml;d on teostatud erinevatele klientidele!");
			classload("vcl/table");
			$t = new aw_table(array(
				"layout" => "generic"
			));
			$t->define_field(array(
				"name" => "bill",
				"caption" => t("Arve"),
				"sortable" => 1,
			));
			$t->define_field(array(
			"name" => "customer",
			"sortable" => 1,
			"caption" => t("Klient")
			));
			$t->define_field(array(
				"name" => "select_this",
				"caption" => t("Vali"),
			));
			$t->set_default_sortby("name");

			$filter["lang_id"] = array();
			$filter["site_id"] = array();
			$filter["class_id"] = CL_CRM_BILL;
			$filter["state"] = 0;


			$ol = new object_list($filter);

			for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
			{
				$customer = "";
				if(is_oid($o->prop("customer")) && $this->can("view" , $o->prop("customer")))
				{
					$customer_obj = obj($o->prop("customer"));
					$customer = $customer_obj->name();
				}
				$dat = array(
					"bill" => html::obj_change_url($o),
					"customer" => $customer,
					"select_this" => html::href(array(
						"url" => $this->mk_my_orb("search_bill", array("bill_id" => $o->id(),)),
						"caption" => t("Vali see"),
					)),
				);
				$t->define_data($dat);
			}
			$t->sort_by();
			return $t->draw();

		}
		if($_GET["bill_id"])
		{
			$_SESSION["bill_id"] = $_GET["bill_id"];
			die("
				<html><body><script language='javascript'>
					javascript:var ansa = confirm('" . t("Kas koondada arendusulesannete kommentaarid?") . "');
					var asd = window.opener.document.getElementsByName('bunch_bugs');
					if (ansa)
					{
						asd[0].value=1;
					}
					else
					{
						asd[0].value=0;
					}
					window.opener.submit_changeform('create_bill');
					window.close();
				</script></body></html>
			");
		}
		classload("vcl/table");
		$t = new aw_table(array(
			"layout" => "generic"
		));
		$t->define_field(array(
			"name" => "oid",
			"caption" => t("OID"),
			"sortable" => 1,
		));
			$t->define_field(array(
			"name" => "name",
			"sortable" => 1,
			"caption" => t("Nimi")
		));
			$t->define_field(array(
			"name" => "parent",
			"sortable" => 1,
			"caption" => t("Asukoht")
		));
			$t->define_field(array(
			"name" => "modifiedby",
			"sortable" => 1,
			"caption" => t("Muutja")
		));
		$t->define_field(array(
			"name" => "modified",
			"caption" => t("Muudetud"),
			"sortable" => 1,
			"format" => "d.m.Y H:i",
			"type" => "time"
		));
		$t->define_field(array(
			"name" => "select_this",
			"caption" => t("Vali"),
		));
		$t->set_default_sortby("name");

		$filter["lang_id"] = array();
		$filter["site_id"] = array();
		$filter["class_id"] = CL_CRM_BILL;
		$filter["state"] = 0;
		$ol = new object_list($filter);
		for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
		{
			$dat = array(
				"oid" => $o->id(),
				"name" => html::obj_change_url($o),
				"parent" => $o->path_str(array("max_len" => 3)),
				"modifiedby" => $o->modifiedby(),
				"modified" => $o->modified(),
				"select_this" => html::href(array(
					"url" => $this->mk_my_orb("search_bill", array("bill_id" => $o->id(),)),
					"caption" => t("Vali see"),
				)),
			);
			$t->define_data($dat);
		}

		$t->sort_by();
		return $t->draw();
	}


	function _get_mails_list($arr)
	{
		$t = $arr["prop"]["vcl_inst"] = new vcl_table();
		$this->_init_mails_list_t($t, $arr["request"]);

		$stats = new crm_company_stats_impl();
		$bills = $stats->search_bills(array("stats_s_bill_state" => 1));

		$sum = $tol = $deadline = $mhpa = $custs= array();

		$stuff = explode("_" , $arr["request"]["st"]);
		foreach($bills->arr() as $o)
		{
			$sum[$o->id()] = $o->prop("sum");
			$custs[$o->id()] = $o->prop("customer");
			$deadline[$o->id()] = $o->prop("bill_date") + $o->prop("bill_due_date_days")*3600*24;
			$mhpa[$o->id()] = $o->get_payment_over_date();
			switch($stuff[1])
			{
				case "sent":
					if($mhpa[$o->id()])
					{
						$bills->remove($o->id());
					}
				break;
				case "overdate":
					if(!$mhpa[$o->id()])
					{
						$bills->remove($o->id());
					}
					$tol[$o->id()] = $arr["obj_inst"]->get_customer_prop($o->prop("customer"), "bill_tolerance");
					if($mhpa[$o->id()] > $tol[$o->id()])
					{
						$bills->remove($o->id());
					}
				break;
				case "overtolerance":
					if(!$mhpa[$o->id()])
					{
						$bills->remove($o->id());
					}
					if(!($mhpa[$o->id()] > $tol[$o->id()]))
					{
						$bills->remove($o->id());
					}
				break;
				case "all":
				break;
			}
		}

		$mails = $this->get_bill_mails(array("bills" => $bills->ids()));
		$user_inst = get_instance(CL_USER);

		$stats = new crm_company_stats_impl();
		foreach($mails as $mail)
		{
			$addr = array();//explode("," , htmlspecialchars($mail["mto"]));
			if(is_array($mail["mto_relpicker"]) && sizeof($mail["mto_relpicker"]))
			{
				$ol = new object_list();
				$ol->add($mail["mto_relpicker"]);
				foreach($ol->arr() as $o)
				{
					$filter = array(
						"class_id" => CL_CRM_PERSON_WORK_RELATION,
						"lang_id" => array(),
						"site_id" => array(),
						"CL_CRM_PERSON_WORK_RELATION.RELTYPE_EMAIL" => $o->id(),
					);
					$ol2 = new object_list($filter);
					if($ol2->count())
					{
						$ol3 = new object_list(array(
							"class_id" => CL_CRM_PERSON,
							"site_id" => array(),
							"lang_id" => array(),
							new object_list_filter(array(
								"logic" => "OR",
								"conditions" => array(
									"CL_CRM_PERSON.RELTYPE_ORG_RELATION" => $ol2->ids(),
									"CL_CRM_PERSON.RELTYPE_PREVIOUS_JOB" => $ol2->ids(),
									"CL_CRM_PERSON.RELTYPE_CURRENT_JOB" => $ol2->ids(),
								)
							)),

						));
						$target = reset($ol3->arr());

					}
					else
					{
						$conns = $o->connections_to(array(
							"from.class_id" => CL_CRM_PERSON,
							"type" => 11,
						));
						if(sizeof($conns))
						{
							foreach($conns as $conn)
							{
								$target = $conn->from();
							}
						}
						else
						{
							$conns = $o->connections_to(array(
								"from.class_id" => CL_CRM_COMPANY,
								"type" => 15,
							));
							if(sizeof($conns))
							{
								foreach($conns as $conn)
								{
									$target = $conn->from();
								}
							}
						}
					}
					if($target->class_id() == CL_CRM_COMPANY)
					{
						$addr[] = $target->name()." , ".join(", ",$target->get_phones()).", ".$o->prop("mail");
					}
					else
					{
						$phones = $target->phones();
						$addr[] = $target->name()." , ". $target->company_name(). " , ".join(", ",$phones->names()).", ".join(", ",$target->get_profession_names()).", ".$o->prop("mail");
					}
				}
			}


			$user = $mail["createdby"];
			$person = $user_inst->get_person_for_uid($user);
			$data = array();
			$data["sender"] = $stats->js_obj_url($person->id(), $person->name());
			$data["customer"] = $stats->js_obj_url($custs[$mail["parent"]], get_name($custs[$mail["parent"]]));

			$data["to"] = join("<br>" , $addr);

			$data["sum"] = $stats->js_obj_url($mail["parent"], $sum[$mail["parent"]]);
			$data["payment_over_date"] = $mhpa[$mail["parent"]];
			$data["bill_due_date"] = date("d.m.Y" , $deadline[$mail["parent"]]);
			$data["date"] = date("d.m.Y H:i" , $mail["created"]);
			$t->define_data($data);
		}
	}

	function _init_mails_list_t($t, $r)
	{//saatja nimi (NB!!! mitte kasutada kasutajanime!!!!!), Saaja isikute nimed, asutused, telefon laual ja mobiil, ametinimetus, meilidaadressid; arve summa, arve laekumise t2htaeg; arve staatus.
		$t->define_field(array(
			"name" => "sender",
			"caption" => t("Saatja nimi"),
			"sortable" => 1,
			"chgbgcolor" => "color",
		));
		$t->define_field(array(
			"name" => "date",
			"caption" => t("Saadetud"),
			"chgbgcolor" => "color",
		));
		$t->define_field(array(
			"name" => "customer",
			"caption" => t("Klient"),
			"chgbgcolor" => "color",
		));
		$t->define_field(array(
			"name" => "to",
			"caption" => t("Saajad"),
			"chgbgcolor" => "color",
		));
		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa"),
			"sortable" => 1,
			"numeric" => 1,
			"align" => "right",
			"chgbgcolor" => "color",
		));
		$t->define_field(array(
			"name" => "bill_due_date",
			"caption" => t("T&auml;htaeg"),
			"numeric" => 1,
			"sortable" => 1,
			"chgbgcolor" => "color",
		));
		$t->define_field(array(
			"name" => "payment_over_date",
			"caption" => t("<a href='javascript:void(0)' alt='Maksega hilinenud p&auml;evade arv' title='Maksega hilinenud p&auml;evade arv'>HPA</a>"),
			"align" => "center",
			"chgbgcolor" => "color",
		));
	}

	function _init_bills_list_t($t, $r)
	{
		$t->define_field(array(
			"name" => "bill_no",
			"caption" => t("Number"),
			"sortable" => 1,
			"numeric" => 1,
			"chgbgcolor" => "color",
		));

		if ($r["group"] === "bills_monthly")
		{
			$t->define_field(array(
				"name" => "create_new",
				"caption" => t("Loo uus"),
				"sortable" => 1,
				"numeric" => 1,
			"chgbgcolor" => "color",
			));
		}

		$t->define_field(array(
			"name" => "bill_date",
			"caption" => t("Kuup&auml;ev"),
			"type" => "time",
			"format" => "d.m.Y",
			"numeric" => 1,
			"sortable" => 1,
			"chgbgcolor" => "color",
		));
		$t->define_field(array(
			"name" => "bill_due_date",
			"caption" => t("Makset&auml;htaeg"),
			"type" => "time",
			"format" => "d.m.Y",
			"numeric" => 1,
			"sortable" => 1,
			"chgbgcolor" => "color",
		));
		$t->define_field(array(
			"name" => "payment_over_date",
			"caption" => t("<a href='javascript:void(0)' alt='Maksega hilinenud p&auml;evade arv' title='Maksega hilinenud p&auml;evade arv'>MHPA</a>"),
			"align" => "center",
			"chgbgcolor" => "color",
		));
/*
		$t->define_field(array(
			"name" => "payment_date",
			"caption" => t("Laekumiskuup&auml;ev"),
			"type" => "time",
			"format" => "d.m.Y",
			"numeric" => 1,
			"sortable" => 1
		));
*/
		$t->define_field(array(
			"name" => "customer",
			"caption" => t("Klient"),
			"sortable" => 1,
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"name" => "client_manager",
			"caption" => t("Kliendihaldur"),
			"sortable" => 1,
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"name" => "project_leader",
			"caption" => t("Projektijuht"),
			"sortable" => 1,
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa"),
			"sortable" => 1,
			"numeric" => 1,
			"align" => "right",
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"name" => "tax",
			"caption" => t("K&auml;ibemaks"),
			"sortable" => 1,
			"numeric" => 1,
			"align" => "right",
			"chgbgcolor" => "color",
		));

		if($this->show_bill_balance)
		{
			$t->define_field(array(
				"name" => "balance",
				"caption" => t("Arve saldo"),
				"sortable" => 1,
				"numeric" => 1,
				"align" => "right",
			"chgbgcolor" => "color",
			));
		}

/*
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
*/
		if ($r["group"] !== "bills_monthly")
		{
			$t->define_field(array(
				"name" => "state",
				"caption" => t("Staatus"),
				"sortable" => 1,
			"chgbgcolor" => "color",
			));
		}
		$t->define_field(array(
			"name" => "print",
			"caption" => t("Prindi"),
			"sortable" => 1,
			"chgbgcolor" => "color",
		));
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel",
			"chgbgcolor" => "color",
		));
	}

	private function get_range($val)
	{
		switch($val)
		{
			case "period_last_last":
				$filt["bill_date_range"] = array(
					"from" => mktime(0,0,0, date("m")-2, 0, date("Y")),
					"to" => mktime(0,0,0, date("m")-1, 0, date("Y")),
				);
			break;
			case "period_last":
				$filt["bill_date_range"] = array(
					"from" => mktime(0,0,0, date("m")-1, 0, date("Y")),
					"to" => mktime(0,0,0, date("m"), 0, date("Y")),
				);
			break;
			case "period_current":
				$filt["bill_date_range"] = array(
					"from" => mktime(0,0,0, date("m"), 0, date("Y")),
					"to" => mktime(0,0,0, date("m")+1, 0, date("Y")),
				);
			break;
			case "period_next":
				$filt["bill_date_range"] = array(
					"from" => mktime(0,0,0, date("m")+1, 0, date("Y")),
					"to" => mktime(0,0,0, date("m")+2, 0, date("Y")),
				);
			break;
			case "period_year":
				$filt["bill_date_range"] = array(
					"from" => mktime(0,0,0, 1, 1, date("Y")),
					"to" => mktime(0,0,0, 1, 1, date("Y")+1),
				);
			break;
			case "period_lastyear":
				$filt["bill_date_range"] = array(
					"from" => mktime(0,0,0, 1, 1, date("Y")-1),
					"to" => mktime(0,0,0,1 , 1, date("Y")),
				);
			break;
			default :return null;
		}
		return $filt["bill_date_range"];
	}

	function _get_bills_list($arr)
	{
		if(!isset($arr["request"]["st"]))
		{
			if(is_object($current_co = get_current_company()) && $current_co->id() != $arr["obj_inst"]->id())
			{
				$arr["request"]["st"] = "cust_".$arr["obj_inst"]->id();
			}
		}

		if(!empty($arr["request"]["show_bill_balance"]))
		{
			$this->show_bill_balance = 1;
		}

		if(empty($arr["request"]["bill_s_with_tax"]))
		{
			$tax_add = 2;
		}
		else
		{
			$tax_add = $arr["request"]["bill_s_with_tax"];
		}
		$cg = isset($arr["request"]["currency_grouping"]) ? $arr["request"]["currency_grouping"] : null;

		$t = $arr["prop"]["vcl_inst"];
		if(!empty($_GET["get_all_customers_without_client_relation"]))
		{
			$t = $arr["obj_inst"]->get_all_customers_without_client_relation();
			return 1;
		}

		$this->_init_bills_list_t($t, $arr["request"]);

		$d = new crm_data();
		$bill_i = new crm_bill();
		$curr_inst = new currency();
		$co_stat_inst = new crm_company_stats_impl();
		$pop = new popup_menu();

		if ($arr["request"]["group"] === "bills_monthly")
		{
			$bills = $d->get_bills_by_co($arr["obj_inst"], array("monthly" => 1));
			$format = t('%s kuuarved');
		}
		else
		{
			$filt = array();
			if(!empty($arr["request"]["st"]) || !empty($arr["request"]["timespan"]) || !empty($arr["request"]["bill_status"]))
			{
				$stuff = !empty($arr["request"]["st"]) ? explode("_" , $arr["request"]["st"]) : array();
				if (isset($stuff[0]))
				{
					switch($stuff[0])
					{
						case "prman":
							$filt["project_mgr"] = $stuff[1];
							break;
						case "mails":
							return $this->_get_mails_list($arr);
							break;
						case "custman":
							$filt["client_mgr"] = $stuff[1];
							break;
						case "cust":
							$filt["customer"] = $stuff[1];
							break;

						default:
							if($arr["request"]["st"] > 1)
							{
								$filt["state"] = $arr["request"]["st"]-10;
							}
					}
				}

//				if(isset($stuff[2]))
//				{
//					$filt["state"] = $stuff[2];
//				}



				if(!empty($arr["request"]["bill_status"]))
				{
					$filt["state"] = $arr["request"]["bill_status"] - 10;
				}

				if(!empty($arr["request"]["timespan"]))
				{
					$filt["bill_date_range"] = $this->get_range($arr["request"]["timespan"]);
				}
				else
				{
					$filt["bill_date_range"] = array(
						"from" => mktime(0,0,0, date("m"), 0, date("Y")),
						"to" => mktime(0,0,0, date("m")+1, 0, date("Y")),
					);
				}
			}
			elseif (empty($arr["request"]["bill_s_from"]))
			{
				// init default search opts
				//$u = get_instance(CL_USER);
				//$p = obj($u->get_current_person());
				//$filt["client_mgr"] = $p->name();
				$filt["bill_date_range"] = array(
					"from" => mktime(0,0,0, date("m"), 0, date("Y")),
					"to" => mktime(0,0,0, date("m")+1, 0, date("Y")),
				);
//				$filt["state"] = "0";
			}
			else
			{
				if (!empty($arr["request"]["bill_s_cust"]))
				{
					$filt["customer"] = "%".$arr["request"]["bill_s_cust"]."%";
				}

				if($arr["request"]["bill_s_bill_no"] && $arr["request"]["bill_s_bill_to"])
				{
					$filt["bill_no"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $arr["request"]["bill_s_bill_no"] , $arr["request"]["bill_s_bill_to"], "int");
//					$filt["bill_no"] = "%".$arr["request"]["bill_s_bill_no"]."%";
				}
				elseif($arr["request"]["bill_s_bill_no"])
				{
//					$filt["bill_no"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $arr["request"]["bill_s_bill_no"], "","int");
					$filt["bill_no"] = $arr["request"]["bill_s_bill_no"];
				}
				elseif($arr["request"]["bill_s_bill_to"])
				{
//					$filt["bill_no"] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $arr["request"]["bill_s_bill_to"], "","int");
					$filt["bill_no"] = $arr["request"]["bill_s_bill_to"];
				}

				$filt["bill_date_range"] = array(
					"from" => date_edit::get_timestamp($arr["request"]["bill_s_from"]),
					"to" => date_edit::get_day_end_timestamp($arr["request"]["bill_s_to"])
				);

				if ($arr["request"]["bill_s_client_mgr"] != "")
				{
					$filt["client_mgr"] = "%".$arr["request"]["bill_s_client_mgr"]."%";
				}

				if($arr["request"]["bill_s_status"] == -6)
				{
					$filt["on_demand"] = 1;
				}
				else
				{
					$filt["state"] = $arr["request"]["bill_s_status"];
				}
			}

			$bills = $d->get_bills_by_co($arr["obj_inst"], $filt);

			$format = t('%s arved');
		}

		//$t->set_caption(sprintf($format, $arr['obj_inst']->name()));

		$company_curr = $co_stat_inst->get_company_currency();

		if (isset($arr["request"]["export_hr"]) and $arr["request"]["export_hr"] > 0)
		{
			if (is_array($arr["request"]["bi"]) && count($arr["request"]["bi"]))
			{
				$bills = new object_list();
				$bills->add($arr["request"]["bi"]);
			}
			$this->_do_export_hr($bills, $arr, $arr["request"]["export_hr"]);
		}

		$sum_in_curr = $bal_in_curr = array();
		$balance = $sum = $tax = 0;
		foreach($bills->arr() as $bill)
		{
			$cust = "";
			$cm = "";
			$payments_total = 0;
			if (is_oid($customer_id = $bill->get_bill_customer()))
			{
				$tmp = obj($customer_id);
				$cust = $tmp->name() ?  html::get_change_url($tmp->id(), array("return_url" => get_ru()), ($tmp->prop("short_name") ? $tmp->prop("short_name") : $tmp->name()) , $tmp->name()) : "";
				$cm = html::obj_change_url($tmp->prop("client_manager"));
			}
			if ($arr["request"]["group"] == "bills_search")
			{
				$state = $bill_i->states[$bill->prop("state")];
			}
			else
			{
				$state = html::select(array(
					"options" => $bill_i->states,
					"selected" => $bill->prop("state"),
					"name" => "bill_states[".$bill->id()."]",
					"width" => 100,
				));
			}


			$cursum = $own_currency_sum = $bill->get_bill_sum(2);//$bill_i->get_bill_sum($bill,$tax_add);
			$curid = $bill->get_bill_currency_id();
			$cur_name = $bill->get_bill_currency_name();
			if($company_curr && $curid && ($company_curr != $curid))
			{
				$own_currency_sum  = $co_stat_inst->convert_to_company_currency(array(
					"sum" =>  $cursum,
					"o" => $bill,
				));
			}
			if($cg)//kliendi valuutas
			{
				$sum_str = number_format($cursum, 2)." ".$cur_name;
				$sum_in_curr[$cur_name] += $cursum;
			}
			else//oma organisatsiooni valuutas
			{
				$sum_str = number_format($own_currency_sum, 2);
			}


			$curtax = $own_currency_tax = $bill->get_bill_sum(3);//$bill_i->get_bill_sum($bill,$tax_add);
			if($company_curr && $curid && ($company_curr != $curid))
			{
				$own_currency_tax  = $co_stat_inst->convert_to_company_currency(array(
					"sum" =>  $curtax,
					"o" => $bill,
				));
			}

			if($cg)//kliendi valuutas
			{
				$tax_str = number_format($curtax, 2)." ".$cur_name;
				$tax_in_curr[$cur_name] += $curtax;
			}
			else//oma organisatsiooni valuutas
			{
				$tax_str = number_format($own_currency_tax, 2);
			}


			$pop->begin_menu("bill_".$bill->id());
			$pop->add_item(Array(
				"text" => t("Prindi arve"),
				"link" => "#",
				"onclick" => "window.open(\"".$this->mk_my_orb("change", array("openprintdialog" => 1,"id" => $bill->id(), "group" => "preview"), CL_CRM_BILL)."\",\"billprint\",\"width=100,height=100\");"
			));
			$pop->add_item(Array(
				"text" => t("Prindi arve lisa"),
				"link" => "#",
				"onclick" => "window.open(\"".$this->mk_my_orb("change", array("openprintdialog" => 1,"id" => $bill->id(), "group" => "preview_add"), CL_CRM_BILL)."\",\"billprintadd\",\"width=100,height=100\");"
			));
			$pop->add_item(array(
				"text" => t("Prindi arve koos lisaga"),
				"link" => "#",
				"onclick" => "window.open(\"".$this->mk_my_orb("change", array("openprintdialog_b" => 1,"id" => $bill->id(), "group" => "preview"), CL_CRM_BILL)."\",\"billprintadd\",\"width=100,height=100\");"
			));
			$partial = "";
			if($bill->prop("state") == 3 && $bill->prop("partial_recieved") && $bill->prop("partial_recieved") < $cursum)
			{
				$partial = '<br>'.t("osaliselt");
			}
			$bill_data = array(
				"bill_no" => html::get_change_url($bill->id(), array("return_url" => get_ru()), parse_obj_name($bill->prop("bill_no"))),
				"create_new" => html::href(array(
					"url" => $this->mk_my_orb("create_new_monthly_bill", array(
						"id" => $bill->id(),
						"co" => $arr["obj_inst"]->id(),
						"post_ru" => get_ru()
						), CL_CRM_COMPANY),
					"caption" => t("Loo uus")
				)),
				"bill_date" => $bill->prop("bill_date"),
				"bill_due_date" => $bill->prop("bill_due_date"),
				"customer" => $cust,
				"state" => $state.$partial,
				"sum" => $sum_str,
				"client_manager" => $cm,
				"oid" => $bill->id(),
				"print" => $pop->get_menu(),
				"tax" => $tax_str,
			);

			if($bill->prop("state") == 1)
			{
				$bill_data["payment_over_date"] = $bill->get_payment_over_date();
				$tolerance = $arr["obj_inst"]->get_customer_prop($bill->prop("customer"), "bill_tolerance");
				if($bill_data["payment_over_date"] > $tolerance)
				{
					$bill_data["color"] = "#FF9999";
				}
			}

/*
			//laekunud summa
			if($payments_sum = $bill->get_payments_sum())
			{
				$bill_data["paid"] = number_format($payments_sum,2);
			}

			//hilinenud
			if(($bill->prop("state") == 1 || $bill->prop("state") == 6 || $bill->prop("state") == -6) && $bill->prop("bill_due_date") < time())
			{
				$bill_data["late"] = (int)((time() - $bill->prop("bill_due_date")) / (3600*24));
			}

			//laekumiskuup2ev
			if($payment_date = $bill->get_last_payment_date())
			{
				$bill_data["payment_date"] = $payment_date;
			}
*/
			$project_leaders = $bill->project_leaders();
			if($project_leaders->count())
			{
				$pl_array = array();
				foreach($project_leaders->arr() as $pl)
				{
					$pl_array[] = html::href(array(
						"caption" => $pl->name(),
						"url" => html::obj_change_url($pl , array())
					));
				}
				$bill_data["project_leader"] = join("<br>" , $pl_array);
			}

			if(!empty($arr["request"]["show_bill_balance"]))
			{
				$curr_balance = $bill->get_bill_needs_payment();
				if($company_curr && $curid && ($company_curr != $curid))
				{

					$total_balance = $own_currency_sum;
					foreach($bill->connections_from(array("type" => "RELTYPE_PAYMENT")) as $conn)
					{
						$p = $conn->to();
						if($p->prop("currency_rate") && $p->prop("currency_rate") != 1)
						{
							$total_balance -= $p->get_free_sum($bill->id()) / $p->prop("currency_rate");
						}
						else
						{
							$total_balance -= $curr_inst->convert(array(
								"from" => $curid,
								"to" => $company_curr,
								"sum" => $p->get_free_sum($bill->id()),
								"date" =>  $p->prop("date"),
							));
						}
					}
				}
				else
				{
					$total_balance = $curr_balance;
				}

				if($cg)
				{
					$bill_data["balance"] = number_format($curr_balance, 2)." ". $bill->get_bill_currency_name();
					$bal_in_curr[$cur_name] += $curr_balance;
				}
				else
				{
					$bill_data["balance"] = number_format($total_balance, 2);
				}
				$balance += $total_balance;
//				$bill_data["balance"] = number_format($bill_data["balance"], 2);
			}

			$t->define_data($bill_data);
			// number_format here to round the number the same way in the add, so the sum is correct
			$sum+= number_format($own_currency_sum,2,".", "");
			$tax+= number_format($own_currency_tax,2,".", "");
		}

		$t->set_default_sorder("desc");
		$t->set_default_sortby("bill_no");
		$t->sort_by();
		$t->set_sortable(false);

		$final_dat = array(
			"bill_no" => t("<b>Summa</b>")
		);
		if($cg)
		{
			foreach($sum_in_curr as $cur_name => $amount)
			{
				$final_dat["sum"] .= "<b>".number_format($amount, 2)." ".$cur_name."</b><br>";
				if($arr["request"]["show_bill_balance"])
				{
					$final_dat["balance"] .= "<b>".number_format($bal_in_curr[$cur_name], 2)." ".$cur_name."</b><br>";
				}
			}
			foreach($tax_in_curr as $cur_name => $amount)
			{
				$final_dat["tax"] .= "<b>".number_format($amount, 2)." ".$cur_name."</b><br>";
			}
			$co_currency_name = "";
			if($this->can("view" , $company_curr))
			{
				$company_curr_obj = obj($company_curr);
				$co_currency_name = $company_curr_obj->name();
			}
			$final_dat["sum"] .= "<b>Kokku: ".number_format($sum, 2).$co_currency_name."</b><br>";
			$final_dat["tax"] .= "<b>Kokku: ".number_format($tax, 2).$co_currency_name."</b><br>";
			if($arr["request"]["show_bill_balance"])
			{
				$final_dat["balance"] .= "<b>Kokku: ".number_format($balance, 2).$co_currency_name."</b><br>";
			}
		}
		else
		{
			$final_dat["tax"] = "<b>".number_format($tax, 2)."</b>";
			$final_dat["sum"] = "<b>".number_format($sum, 2)."</b>";
			if(!empty($arr["request"]["show_bill_balance"]))
			{
				$final_dat["balance"] .= "<b>".number_format($balance, 2)."</b><br>";
			}
		}
		$t->define_data($final_dat);
	}

	function _get_bill_s_with_tax($arr)
	{
		$arr["prop"]["options"] = array(
			0 => t("K&auml;ibemaksuta"),
			1 => t("K&auml;ibemaksuga"),
		);
		if(empty($arr["request"]["bill_s_with_tax"]))
		{
			$arr["prop"]["value"] = 1;
		}
		else
		{
			$arr["prop"]["value"] = $arr["request"]["bill_s_with_tax"];
		}
		return PROP_OK;
	}

	function _get_bill_s_client_mgr($arr)
	{
		$v = empty($arr["request"]["bill_s_client_mgr"]) ? null : $arr["request"]["bill_s_client_mgr"];

		$tt = t("Kustuta");
		$arr["prop"]["value"] = html::textbox(array(
			"name" => "bill_s_client_mgr",
			"value" => $v,
			"size" => 25
		))."<a href='javascript:void(0)' onClick='document.changeform.bill_s_client_mgr.value=\"\"' title=\"$tt\" alt=\"$tt\"><img title=\"$tt\" alt=\"$tt\" src='".aw_ini_get("baseurl")."/automatweb/images/icons/delete.gif' border=0></a>";
		return PROP_OK;
	}

	function _get_bill_s_status($arr)
	{
		$b = new crm_bill();
		$arr["prop"]["options"] = array("-1" => "") + $b->states + array("-6" => t("Sissen&otilde;udmisel"));
		if (empty($arr["request"]["bill_s_from"]))
		{
			$arr["prop"]["value"] = -1;
		}
		else
		{
			$arr["prop"]["value"] = $arr["request"]["bill_s_status"];
		}
	}

	function _get_billable_start($arr)
	{
		$arr["prop"]["value"] = empty($arr["request"]["billable_start"]) ? mktime(0,0,0, (date("m" , time()) - 1),1 ,date("Y" , time())) : $arr["request"]["billable_start"];
	}

	function _get_billable_end($arr)
	{
		$arr["prop"]["value"] = empty($arr["request"]["billable_end"]) ? mktime(-1,0,0, date("m" , time()),1 ,date("Y" , time())) : $arr["request"]["billable_end"];
	}

	function _get_bills_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			'name' => 'new',
			'img' => 'new.gif',
			'tooltip' => t('Lisa'),
			'url' => html::get_new_url(CL_CRM_BILL, $arr["obj_inst"]->id(), array("return_url" => get_ru()))
		));

		$tb->add_button(array(
			'name' => 'save',
			'img' => 'save.gif',
			'tooltip' => t('Salvesta'),
			'action' => 'save_bill_list',
		));
		$tb->add_button(array(
			'name' => 'del',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta valitud arved'),
			"confirm" => t("Oled kindel et soovid valitud arved kustutada?"),
			'action' => 'delete_bills',
		));

		$tb->add_separator();

		$tb->add_menu_button(array(
			'name'=>'export',
			'tooltip'=> t('Ekspordi'),
			"img" => "export.gif"
		));

		$last_bno = $arr["obj_inst"]->meta("last_exp_no");

		$tb->add_menu_item(array(
			'parent'=>'export',
			'text' => t("Hansa raama (ridadega)"),
			'link' => "#",
			"onClick" => "v=prompt('" . t("Sisesta arve number?") . "','$last_bno'); if (v != null) { window.location='".aw_url_change_var("export_hr", 1)."&exp_bno='+v;} else { return false; }"
		));

		$tb->add_menu_item(array(
			"parent" => "export",
			"text" => t("Hansa raama (koondatud)"),
			'link' => "#",
			"onClick" => "v=prompt('" . t("Sisesta arve number?") . "','$last_bno'); if (v != null) { window.location='".aw_url_change_var("export_hr", 2)."&exp_bno='+v;} else { return false; }"
		));

		$tb->add_button(array(
			'name' => 'commix',
			'img' => 'merge_left.png',
			'tooltip' => t('Koonda arved'),
			'action' => 'commix',
		));

/*
		$tb->add_button(array(
			"name" => "add_payment",
			"img" => "create_bill.jpg",
			"tooltip" => t("Lisa laekumine"),
			"action" => "add_payment"
		));*/
	}

	function _get_bills_mon_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			'name' => 'save',
			'img' => 'save.gif',
			'tooltip' => t('Salvesta'),
			'action' => 'create_new_monthly_bill',
		));
		$tb->add_button(array(
			'name' => 'del',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta valitud arved'),
			"confirm" => t("Oled kindel et soovid valitud arved kustutada?"),
			'action' => 'delete_bills',
		));
	}

	function _get_bs_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "create_bill",
			"tooltip" => t("Loo arve"),
			"img" => "save.gif",
			"action" => "create_bill"
		));

	}

	function _do_export_hr($bills, $arr, $type = 1)
	{
		$u = get_instance(CL_USER);
		$i = get_instance(CL_CRM_BILL);
		$p = obj($u->get_current_person());
		$co = obj($u->get_current_company());
		$fn = trim(mb_strtoupper($p->prop("firstname")));

		$ct = array();

		$renumber = false;
		if ($_GET["exp_bno"] > 0)
		{
			$renumber = true;
			$bno = $_GET["exp_bno"];
		}
		$min = time() + 24*3600*10;
		$max = 0;
		foreach($bills->arr() as $b)
		{

			$agreement_price = $b->meta("agreement_price");
			if ($renumber)
			{
				$b->set_prop("bill_no", $bno);
				$b->set_name(sprintf(t("Arve nr %s"), $bno));
				// change bill numbers for all tasks that this bill is to
				$ol = new object_list(array(
					"class_id" => CL_TASK,
					"bill_no" => $b->prop("bill_no"),
					"lang_id" => array(),
					"site_id" => array()
				));
				foreach($ol->arr() as $task)
				{
					$task->set_prop("bill_no", $bno);
					$task->save();
				}
				$b->save();
				$bno++;
			}

			$tmp = array();
			foreach((array)$b->prop("signers") as $signer_id)
			{
				if ($this->can("view", $signer_id))
				{
					$signer_o = obj($signer_id);
					$tmp[] = $signer_o->prop("comment");
				}
			}
			$rfn = join(",", $tmp);

			if ($rfn == "" && $this->can("view", $b->prop("customer")))
			{
				$cc = get_instance(CL_CRM_COMPANY);
				$crel = $cc->get_cust_rel(obj($b->prop("customer")));
				if ($crel)
				{
					if ($this->can("view", $crel->prop("client_manager")))
					{
						$clm = obj($crel->prop("client_manager"));
						$rfn = $clm->prop("comment");
					}
				}
				else
				if ($this->can("view", $b->prop("customer.client_manager")))
				{
					$clm = obj($b->prop("customer.client_manager"));
					$rfn = $clm->prop("comment");
				}
			}

			if ($rfn == "")
			{
				//$rfn = $fn;
				$rfn = $p->prop("comment");
			}
			$rfn = str_replace("\n", "", str_replace("\r", "", trim($rfn)));
			$penalty = "0,00";
			if ($this->can("view", $b->prop("customer")))
			{
				$cust = obj($b->prop("customer"));
				if($cust->prop("bill_penalty_pct")) $penalty = str_replace("." , "," , $cust->prop("bill_penalty_pct"));
				else $penalty = str_replace("." , "," , $co->prop("bill_penalty_pct"));
			}

			if($b->prop("bill_trans_date")>1)
			{
				$date = date("d.m.Y", $b->prop("bill_trans_date"));
			}
			else
			{
				$date = date("d.m.Y", $b->prop("bill_date"));
			}
			$min = min($min, $b->prop("bill_date"));
			$max = max($max, $b->prop("bill_date"));
			$payment_mode = $b->prop("payment_mode");

			// bill info row
			$brow = array();
			$brow[] = $b->prop("bill_no");				// arve nr
			$brow[] = date("d.m.Y", $b->prop("bill_date"));		// arve kuup
			$brow[] = date("d.m.Y", $b->prop("bill_due_date"));	// tasumistahtaeg
			$brow[] = 0;						// 0 (teadmata - vaikevaartus 0)
			$brow[] = 1;						// 1 (teadmata -vaikevaartus 1)
			$brow[] = $b->prop("bill_due_date_days"); 		// 7(tasumistingimuse kood - vordusta hetkel paevade arvuga)
			$brow[] = 7;						// 7(tasumistingimus)
			$brow[] = "";
			$brow[] = "";
			$brow[] = "";
			$brow[] = "";
			//$brow[] = "";
			$brow[] = 0;						// 0 (teadmata - vaikevaartus 0)
			$brow[] = $penalty;					// 0,00 (teadmata - vaikevaartus 0,00) viivis
			$brow[] = "";
			$brow[] = $payment_mode?$payment_mode:1;//1;		// 1 (teadmata - vaikev22rtus 1)
			$brow[] = "";
			$brow[] = $rfn;						// OBJEKT (kasutaja eesnimi suurte tahtedega, nt TEDDI)
			$brow[] = "";
			$brow[] = 0;						//  0 (teadmata - vaikevaartus 0)
			$i = get_instance(CL_CRM_BILL);
			$cur = $b->get_bill_currency_name();
			$brow[] = "";
			$brow[] = "";
			$brow[] = "";
			$brow[] = ($cur ? $cur : t("EEK"));			// EEK (valuuta)
			$brow[] = $cur == "EUR" ? "15,64664" : "";
			$brow[] = $date;					// arve kuupaev//////////////
			$brow[] = 0;						// (teadmata - vaikevaartus 0)
			$brow[] = "";
			$brow[] = "";
			$brow[] = $cur == "EUR" ? "1" : "";
			if (true || $cur == "EEK")
			{
				$brow[] = "";
				$brow[] = "";
			}
			else
			{
				$brow[] = "15,65";					// (EURO kurss)
				$brow[] = "1,00";					// (kursi suhe, vaikevaartus 1,00)
			}
			$brow[] = "";
			$brow[] = "";
			$ct[] = join("\t", $brow);

			// customer info row
			$custr = array();
			if ($this->can("view", $b->prop("customer")))
			{
				$cust = obj($b->prop("customer"));

				$custr[] = str_replace("\n", "", str_replace("\r", "", trim($cust->comment())));	// kliendi kood hansaraamas
				$custr[] = str_replace("\n", "", str_replace("\r", "", trim($i->get_customer_name($b->id()))))." ".str_replace("\n", "", str_replace("\r", "", trim($cust->prop("ettevotlusvorm.shortname"))));	// kliendi kood hansaraamas

				/*
				if($cust->class_id() == CL_CRM_PERSON)
				{
					$custr[] = $cust->prop_str("address");
					$custr[] = $cust->prop("address.postiindeks")." ".$cust->prop("address.riik.name");
				}
				else
				{
					$custr[] = $cust->prop_str("contact");
					$custr[] = $cust->prop("contact.postiindeks")." ".$cust->prop("contact.riik.name");
				}
				*/
				$custr[] = $i->get_customer_address($b->id());
				$custr[] = $i->get_customer_address($b->id() , "index")." ".$i->get_customer_address($b->id() , "country");

				$cust_code = str_replace("\n", "", str_replace("\r", "", trim($i->get_customer_code($b->id()))));
				list($cm) = explode(" ", $cust->prop_str("client_manager"));
				$cm = mb_strtoupper($cm);
			}
			else
			{
				$custr[] = "";
				$custr[] = "";
				$custr[] = "";
				$custr[] = "";
			}
			$ct[] = join("\t", $custr)."\t\t\t\t\t";
			$ct[] = join("\t", array("", "", "", ""));	// esindajad

			// payment row
			$pr = array();
			$pr[] = "0,00";	// (teadmata - vaikevaartus 0,00)
//			$pr[] = str_replace(".", ",", round($i->get_bill_sum($b,crm_bill_obj::BILL_SUM_WO_TAX)*2.0+0.049,1)/2.0);		// 33492,03 (summa kaibemaksuta)
			$pr[] = str_replace(".", ",", $i->get_bill_sum($b,crm_bill_obj::BILL_SUM_WO_TAX));		// 33492,03 (summa kaibemaksuta)

			$pr[] = "";
			$pr[] = str_replace(".", ",", round($i->get_bill_sum($b,crm_bill_obj::BILL_SUM_TAX)*2.0+0.049,1)/2.0);		// 6028,57 (kaibemaks)
			$pr[] = str_replace(".", ",", round($i->get_bill_sum($b,crm_bill_obj::BILL_SUM)*2.0+0.049,1)/2.0);		// 39520,60 (Summa koos kaibemaksuga)
			$pr[] = "";
			$pr[] = "";
			$pr[] = "";
			$pr[] = "";
			$pr[] = "";
			$pr[] = "0,00";	//(teadmata - vaikevaartus 0,00)
			$pr[] = "";
			$pr[] = "";
			$pr[] = "";
			$pr[] = "";
			$pr[] = "";
			$pr[] = "";
			$pr[] = "";
			$pr[] = "";
			$pr[] = "";
			$pr[] = "0,00"; //(teadmata - vaikevaartus 0,00)
			$pr[] = "";	//LADU (voib ka tyhjusega asendada)
			$pr[] = "";
			$pr[] = "";
			$pr[] = "";	// 90000 (teadmata, voib ka tyhjusega asendada)
			$pr[] = "";	// 00014 (teadmata, voib ka tyhjusega asendada)
			$pr[] = "";
			$pr[] = "0";	// (teadmata - vaikevaartus 0)
			$pr[] = "";

			$sum = round(str_replace(",", ".", $i->get_bill_sum($b,crm_bill_obj::BILL_SUM))*2.0+0.049,1)/2.0;
			$pr[] = str_replace(".", ",", $sum);	//39520,60 (Summa koos kaibemaksuga)
			$pr[] = "";
			$pr[] = "";
			$pr[] = "";
			$pr[] = str_replace(".", ",", $i->get_bill_sum($b,crm_bill_obj::BILL_SUM_WO_TAX));		// 33492,03 (summa kaibemaksuta)
			$pr[] = "0";	// (teadmata - vaikevaartus 0)
			$pr[] = "0";	//  (teadmata - vaikevaartus 0)
			$pr[] = "";
			$pr[] = "";
			$pr[] = "";
			$pr[] = "";
			$pr[] = "0";	// (teadmata - vaikevaartus 0)
			$pr[] = "";
			$pr[] = "0";	// 0(teadmata - vaikevaartus 0)
			$pr[] = "0";	// (teadmata - vaikevaartus 0)
			$pr[] = "";
			$pr[] =	"0";	// (teadmata - vaikevaartus 0)
			$pr[] = "";
			//$pr[] = str_replace(".", ",", $i->get_bill_sum($b, crm_bill_obj::BILL_AMT)); //77,00 (kogus kokku)
			$pr[] = "";
			$pr[] = "";	// (teadmata - vaikevaartus 0,00)
			$pr[] = "";	// (teadmata - vaikevaartus 0,00)
			$pr[] = "";		// (teadmata - vaikevaartus 0)
			$pr[] = "0";
			$pr[] = "";	//(teadmata - vaikevaartus 0)
			$pr[] = "0";	//(teadmata - vaikevaartus 0)
			$pr[] = "0";
			$pr[] = ""; //(teadmata - vaikevaartus 0)
			$pr[] = "";
			$ct[] = join("\t", $pr);

			$rows = $i->get_bill_rows($b);
			//kui eksisteerib kokkuleppe hind, siis votab selle ridade asemele

			if($agreement_price[0]["price"] && strlen($agreement_price[0]["name"]) > 0)
			{
				$rows = $agreement_price;
			}
			if($agreement_price["price"] && strlen($agreement_price["name"]) > 0)
			{
				$rows = array(0 => array(
					"amt" => $agreement_price["amt"],
					"date" => $agreement_price["date"],
					"unit" => $agreement_price["unit"],
					"price" => $agreement_price["price"],
					"has_tax" => $agreement_price["has_tax"],
					"comment" => $agreement_price["name"],
					"sum" => $agreement_price["sum"],

				));
			}
			if ($type == 1)
			{
			foreach($rows as $idx => $row)
			{
				$ri = array();
				$ri[] = "1";	// (teadmata, vaikevaartus 1))
				//$ri[] = $idx;	// TEST (artikli kood)
				//$ri[] = $row["code"];
				$code = "";
				$acct = "";
				if ($this->can("view", $row["prod"]))
				{
					$prod = obj($row["prod"]);
					$code = $prod->name();
					$acct = $prod->prop("tax_rate.acct");
				}
				$ri[] = $code;
				$ri[] = $row["amt"];	//33 (kogus)
				$dd = trim($row["name"]);
				if ($dd == "")
				{
					$dd = trim($row["comment"]);
				}
				$dd_bits = $this->split_by_word($dd);
				$ri[] = $dd_bits[0];	// testartikkel (toimetuse rea sisu)
				$ri[] = str_replace(".", ",", $row["price"]);	// 555,00 (yhiku hind)
//				$sum = round(str_replace(",", ".", $row["sum"])*2.0+0.049,1)/2.0;
				$sum = str_replace(",", ".", $row["sum"]);
				$ri[] = str_replace(".", ",",$sum);	// 16300,35 (rea summa km-ta)
				$ri[] = str_replace(".", ",", $b->prop("disc")); //11,0 (ale%)
				$ri[] = $acct;		// (konto)
				$ri[] = $this->_get_bill_row_obj_hr($row,$b); // isik siia
				$ri[] = "";
				$ri[] = "";
				$ri[] = str_replace(".", ",", $sum);	// 16300,35 (rea summa km-ta)
				$ri[] = "";
				//$ri[] = "1";	// (kaibemaksukood)
				$ri[] = $row["km_code"];
				$ri[] = "";
				$ri[] = "";
				$ri[] = "";
				$ri[] = "";
				$ri[] = "";
				$ri[] = "";
				$ri[] = "";
				$ri[] = "";
				$ri[] = "";
				$ri[] = $row["unit"];	//TK (yhik)


				$ct[] = join("\t", $ri);
				for($i = 1; $i < count($dd_bits); $i++)
				{
					$ri = array();
					$ri[] = "1";
					$ri[] = "";
					$ri[] = "";
					$ri[] = $dd_bits[$i];
					$ri[] = "";
					$ri[] = "";
					$ri[] = "";
					$ri[] = "";
					$ri[] = "";
					$ri[] = "";
					$ri[] = "";
					$ri[] = "";
					$ri[] = "";
					$ri[] = "";
					$ri[] = "";
					$ri[] = "";
					$ri[] = "";
					$ri[] = "";
					$ri[] = "";
					$ri[] = "";
					$ri[] = "";
					$ri[] = "";
					$ri[] = "";
					$ri[] = "";
					$ct[] = join("\t", $ri);
				}
			}
			}
			else
			{
				$code = $amt = $price = $sum = 0;
				foreach($rows as $idx => $row)
				{
					$code = $row["code"];
					$amt += str_replace(",", ".", $row["amt"]);
					$sum += str_replace(",", ".", $row["sum"]);
				}

				$price = $sum / $amt;
				$sum = round($sum*2.0+0.049,1)/2.0;
				$ri = array();
				$ri[] = "1";
				$ri[] = $code;
				$ri[] = $amt;
				$ri[] = $this->nice_trim($b->prop("notes"));
				$ri[] = number_format($price);
				$ri[] = str_replace(".", ",", $sum);
				$ri[] = str_replace(".", ",", $b->prop("disc"));
				$ri[] = 3100;
				$ri[] = "";
				$ri[] = "";
				$ri[] = "";
				$ri[] = str_replace(".", ",", $sum);
				$ri[] = "";
				$ri[] = "1";
				$ri[] = "";
				$ri[] = "";
				$ri[] = "";
				$ri[] = "";
				$ri[] = "";
				$ri[] = "";
				$ri[] = "";
				$ri[] = "";
				$ri[] = "";
				$ri[] = $b->prop("gen_unit");
				$ct[] = join("\t", $ri);
			}
			$ct[] = ""; // next bill
		}
		if ($renumber)
		{
			$co = obj($_GET["id"]);
			$co->set_meta("last_exp_no", $bno);
			$co->save();
		}
		header("Content-type: text/plain");
		header('Content-Disposition: attachment; filename="arved.txt"');
		echo "format	\r\n";
		echo "1\t44\t1\t\r\n";
		echo "\r\n";
//		echo "sysformat	\r\n";
//		echo "1	1	1	1	.	,	 	\r\n";
//		echo "\n";
		echo "commentstring	\r\n";
		$co = get_current_company();
		$co_n = $co->name();
		$from = date("d.m.Y", $min);
		$to = date("d.m.Y", $max);
		echo "$co_n $from - $to\r\n";
		echo "\r\n";
		echo "fakt1	\r\n";

		die(join("\r\n", $ct));
	}

	function _get_bill_row_obj_hr($row, $b)
	{
		$ret = "";
		$comments = array();
		if (count($row["persons"]) == 0 && $this->can("view", $b->prop("customer")))
		{
			$cc = new crm_company();
			$crel = $cc->get_cust_rel(obj($b->prop("customer")));
			if (!$crel)
			{
				return "";
			}
			//return mb_strtoupper($crel->prop("client_manager.firstname"), aw_global_get("charset")
			$ret = $crel->prop("comment");
		}
		else
		{
			if(!count($row["persons"])) return "";
			$list = new object_list(array(
				"oid" => $row["persons"],
				"lang_id" => array(),
				"site_id" => array()
			));
			foreach($list->arr() as $person)
			{
				$comments[] = $person->prop("comment");
			}
			$ret = join(", " , $comments);
		}
		return $ret;
		//mb_strtoupper(join(", ", $list->names()), aw_global_get("charset"));
	}

	function nice_trim($s, $len = 250)
	{
		if (strlen($s) > $len)
		{
			return substr($s, 0, strrpos(substr($s, 0, $len), " "));
		}
		return $s;
	}

	function get_billable_bugs($r)
	{

/*
		//viimase 2 kuu bugid muudab 2ra
		$ol = new object_list(array(
			"class_id" => CL_BUG_COMMENT,
			"created" => new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, (time() - 3600*24*60)),
		));
		arr(sizeof($ol->ids()));
		foreach($ol->arr() as $o)
		{
			$o->save();
		}
*/



//-----------------------------------------siia ka toimetuse read, kui tegemiseks l2heb
		$ol = new object_list();
/*		$bc_filt = array(
			"class_id" => array(CL_BUG_COMMENT),
			"lang_id" => array(),
			"site_id" => array(),
			"CL_BUG_COMMENT.bug.send_bill" => 1,
			//no ei t88ta see.....
//			"CL_BUG_COMMENT.bug.send_bill" => 1,
		);
*/
		$bc_filt = array(
			"class_id" => array(CL_TASK_ROW),
			"lang_id" => array(),
			"site_id" => array(),
			"task.class_id" => CL_BUG,
			"task.send_bill"=> 1
		);

		if ($this->search_start && $this->search_end)
		{
			$bc_filt["created"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $this->search_start, $this->search_end);
		}
		elseif($this->search_start)
		{
			$bc_filt["created"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $this->search_start);
		}
		elseif ($this->search_end)
		{
			$bc_filt["created"] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $this->search_end);
		}

		$bc_ol = new object_list($bc_filt);
		$this->bug_comments = array();

		foreach($bc_ol->arr() as $bc)
		{
			if(!$this->can("view" , $bc->prop("bill")))//selle v6ib 2ra kaotada kui filtri t88le saab
			{
				$this->bug_comments[$bc->parent()][] = $bc;
			}
		}

		$ol->add(array_keys($this->bug_comments));
		return $ol;
	}


	function split_by_word($str, $len = 50)
	{
		$ret = array();
		do {
			$tmp = $this->nice_trim($str, 50);
			$ret[] = $tmp;
			$str = trim(substr($str, strlen($tmp)));
		} while ($str != "");
		return $ret;
	}

	private function add_bill_comments_to_session($sel)
	{
		if(!$_SESSION["ccbc_bug_comments"])
		{
			$_SESSION["ccbc_bug_comments"] = array();
		}
		elseif(sizeof($_SESSION["ccbc_bug_comments"]))
		{
			$sess_bug = obj(reset($_SESSION["ccbc_bug_comments"]));
			$sess_cust = $sess_bug->prop("parent.customer");
			$new_bug = obj(reset($sel));
			if($new_bug->prop("parent.class_id") == CL_BUG)
			{
				$new_cust = $new_bug->prop("parent.customer");
				if($new_cust != $sess_cust)
				{
					$_SESSION["ccbc_bug_comments"] = array();
				}
			}
		}

		$_SESSION["ccbc_bug_comments"] = $_SESSION["ccbc_bug_comments"] + $sel;

	}

	/**
		@attrib name=create_bill_bug_popup api=1 params=name all_args=1
	**/
	function create_bill_bug_popup($arr)
	{
		if(is_array($arr["sel"]))
		{
			$this->add_bill_comments_to_session($arr["sel"]);

			$ret = 	"<script type='text/javascript'>window.close();</script>";
			die($ret);
		}

		if(!$this->can("view" , $arr["id"]))
		{
			die(t("Bugi id puudu..."));
		}

		$stats = get_instance("applications/crm/crm_company_stats_impl");
		$bug = obj($arr["id"]);

		$comments = $bug->get_billable_comments(array(
				"end" => $arr["end"],
				"start" => $arr["start"],
		));
		if(!sizeof($comments->ids()))
		{
			die(t("Valitud bugil pole arvele minevaid kommentaare..."));
		}

		$t = new vcl_table();


//Tulpadeks: esimesed 300 m2rki igast kommentaarist (klikitav), kommentaarile kulunud aeg ning nende taga on m2rkeruut kommentaari valimiseks (vaikimisi tsekitud).


		$t->define_field(array(
			"name" => "comment",
			"caption" => t("Kommentaar"),
		));

		$t->define_field(array(
			"name" => "time",
			"caption" => t("Kulunud aeg"),
		));

		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));

		foreach($comments->arr() as $comment)
		{
			$capt = substr($comment->prop("content"), 0 , 300);
			$t->define_data(array(
				"comment" => html::href(array(
					"caption" => $capt ? $capt : t("..."),
					"url" => html::obj_change_url($comment , array()))),
				"time" => $stats->hours_format($comment->prop("add_wh")),
				"oid" => $comment->id(),
			));
		}

		$h = new htmlclient();
		$h->start_output();

		$table_prop = array("type" => "text" , "no_caption" => "1" , "value" =>$t->draw());

		$h->add_property($table_prop);

		$h->put_submit(array());
		$h->finish_output(array(
			"method" => "GET",
			"action" => "create_bill_bug_popup",
			"data" => array(
				"orb_class" => "crm_company_bills_impl",
				"id" => $_GET["id"]
			),
			"sbt_caption" => t("Arvele")
		));
		$html = $h->get_result();
/*
		$content = $this->_get_pop_s_res_t($arr);
		$content .= html::submit(array(
			"value" => t("Vali")
		));
		$content .= $this->mk_reforb("save_pop_s_res", array("id" => $_GET["id"]));


		$html .= html::form(array(
			"method" => "POST",
			"action" => "orb.aw",
			"content" => $content
		));
*/
		return $html;

	}

	private function get_time_between($r)
	{
		if(empty($r["billable_start"]))
		{
			$r["billable_start"] = mktime(0,0,0, (date("m" , time()) - 1),1 ,date("Y" , time()));
		}
		else
		{
			$r["billable_start"] = date_edit::get_timestamp($r["billable_start"]);
		}

		if(empty($r["billable_end"]))
		{
			$r["billable_end"] = mktime(-1,0,0, date("m" , time()),1 ,date("Y" , time()));
		}
		else
		{
			$r["billable_end"] = date_edit::get_timestamp($r["billable_end"] );
		}

		if($r["billable_start"] == -1)
		{
			$r["billable_start"] = 0;//et aegade algusest
		}
		if($r["billable_end"] == -1)
		{
			 $r["billable_end"] = 991154552400;//suht suva suur number
		}
		$this->search_start = $r["billable_start"];
		$this->search_end = $r["billable_end"];
		return;
	}


	function _get_bills_stats_tree($arr)
	{
		$tv = $arr["prop"]["vcl_inst"];
		$var = "bill_status";
		$tv->set_selected_item(isset($arr["request"][$var]) ? $arr["request"][$var] : "all_stats");

		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"persist_state" => true,
			"tree_id" => "proj_bills_stats_tree",
		));

		$filter = $this->_get_bills_filter($arr);
		unset($filter["state"]);

		$t = new object_data_list(
			$filter,
			array(
				CL_CRM_BILL => array(
					"state"
				),
			)
		);

		$count = array();
		foreach($t->list_data as $data)
		{
			if (!isset($count[$data["state"]]))
			{
				$count[$data["state"]] = 1;
			}
			else
			{
				++$count[$data["state"]];
			}
		}

		$tv->add_item(0,array(
			"name" => t("K&otilde;ik staatused").(array_sum($count)? "(".array_sum($count).")" : ""),
			"id" => "all_stats",
			"url" => aw_url_change_var($var, null),
		));
		$bills_inst = get_instance(CL_CRM_BILL);

		foreach($bills_inst->states as $id => $caption)
		{
			$tv->add_item("all_stats", array(
				"id" => "".($id+10)."",
				"name" => $caption.(empty($count[$id]) ? "" : " (".$count[$id].")"),
				"url" => aw_url_change_var(array(
					$var => ($id+10),
				)),
				"iconurl" => icons::get_icon_url(CL_CRM_BILL),
			));
		}
	}

	function _get_bills_filter($arr)
	{
		$filter = array(
			"class_id" => CL_CRM_BILL,
			"lang_id" => array(),
			"site_id" => array(),
		);

		$stuff = empty($arr["request"]["st"]) ?  array("default") : explode("_" , $arr["request"]["st"]);
		switch($stuff[0])
		{
			case "prman":
				$filter["RELTYPE_PROJECT.proj_mgr"] = $stuff[1];
				break;
			case "custman":
				$filter["customer.client_mgr"] = $stuff[1];
				break;
			case "cust":
				$filter["customer"] = $stuff[1];
				break;
			default:
				if(isset($arr["request"]["st"]) and $arr["request"]["st"] > 1)
				{
					$filter["state"] = $arr["request"]["st"]-10;
				}
		}

		if(!empty($arr["request"]["bill_status"]))
		{
			$filter["state"] = $arr["request"]["bill_status"] - 10;
		}

		if(!empty($arr["request"]["timespan"]))
		{
			$bill_date_range = $this->get_range($arr["request"]["timespan"]);
		}
		else
		{
			$bill_date_range = array(
				"from" => mktime(0,0,0, date("m"), 0, date("Y")),
				"to" => mktime(0,0,0, date("m")+1, 0, date("Y")),
			);
		}

		if(is_array($bill_date_range))
		{
			$filter["bill_date"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $bill_date_range["from"], $bill_date_range["to"]);
		}
		return $filter;
	}

	function _get_bills_time_tree($arr)
	{
		$tv = $arr["prop"]["vcl_inst"];
		$var = "timespan";
		$tv->set_selected_item(isset($arr["request"][$var]) ? $arr["request"][$var] : "period_current");

		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"persist_state" => true,
			"tree_id" => "proj_bills_time_tree",
		));

		$tv->add_item(0,array(
			"name" => t("K&otilde;ik ajavahemikud"),
			"id" => "all_time",
			"url" => aw_url_change_var($var, "all_time"),
		));

		$branches = array(
//			"all" => t("K&otilde;ik"),
			"period_last_last" => t("&Uuml;leelmine kuu"),
			"period_last" => t("Eelmine kuu"),
			"period_current" => t("K&auml;esolev kuu"),
			"period_next" => t("J&auml;rgmine kuu"),
			"period_year" => t("K&auml;esolev aasta"),
			"period_lastyear" => t("Eelmine aasta"),
		);

		foreach($branches as $id => $caption)
		{
			$tv->add_item("all_time", array(
				"id" => $id,
				"name" => $caption,
				"url" => aw_url_change_var(array(
					$var => $id,
				)),
			));
		}
	}

	function _get_bills_tree($arr)
	{
		$tv = $arr["prop"]["vcl_inst"];
		$var = "st";

		if(!isset($_GET[$var]))
		{
			if(is_object($current_co = get_current_company()) && $current_co->id() != $arr["obj_inst"]->id())
			{
				$_GET[$var] = "cust_".$arr["obj_inst"]->id();
			}
			else
			{
				$_GET[$var] = 10;
			}
		}

		$timespan = isset($arr["request"]["timespan"]) ? $arr["request"]["timespan"] : null;
		$bill_status = isset($arr["request"]["bill_status"]) ? $arr["request"]["bill_status"] : null;
		$bills_data = $this->all_bills_data($timespan, $bill_status - 10);

		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"persist_state" => true,
			"tree_id" => "proj_bills_tree",
		));

		$tv->set_selected_item(isset($arr["request"][$var]) ? $arr["request"][$var] : "all_bills");

		$tv->add_item(0,array(
			"name" => t("K&otilde;ik"),
			"id" => "all_bills",
			"url" => aw_url_change_var($var, null),
		));

		$bills_inst = new crm_bill();
		$states = $bills_inst->states;


		$tv->add_item(0,array(
			"name" => t("Saadetud arved"),
			"id" => "sent_bills",
//			"url" => aw_url_change_var($var, $stat_id+10),
		));

		$sent_bills_props = array(
			"sent" => t("Saadetud ja laekumata"),
			"overdate" => t("Laekumata &uuml;le makset&auml;htaja"),
			"overtolerance" => t("Laekumata &uuml;le tolerantsi"),
			"all" => t("Laekumata kokku"),
		);
/*
b)
(alla kuvab kliendtide nimekirja, kus kliendi nime j2rel sulus on vastav arvete arv)
c)
(vt. punkt b)
d)
*/
/*		$bill_mails = $this->get_bill_mails(array(
			"state" => 1,
		));
*/
		foreach($sent_bills_props as $id => $name)
		{
			if(!$name)
			{
				continue;
			}
			if (isset($_GET[$var]) && $_GET[$var] == "mails_".$id)
			{
				$name = "<b>".$name."</b>";
			}
			$tv->add_item("sent_bills",array(
				"name" => $name,
				"id" => "mails".$id,
				"iconurl" => icons::get_icon_url(CL_MESSAGE),
				"url" => aw_url_change_var($var, "mails_".$id),
			));
		}

		$tv->add_item(0,array(
			"name" => t("Projektijuht"),
			"id" => "pr_mgr",
//			"url" => aw_url_change_var($var, $stat_id+10),
		));

		foreach($this->all_project_managers()->names() as $id => $name)
		{
			if(!$name)
			{
				continue;
			}

			$bills_data = $this->all_bills_data($id);
			$pm_statuses = array();

			foreach($bills_data as $bd)
			{
				if (!isset($pm_statuses[$bd["state"]]))
				{
					$pm_statuses[$bd["state"]] = 0;
				}

				++$pm_statuses[$bd["state"]];
			}

			if(array_sum($pm_statuses))
			{
				$name = $name." (".array_sum($pm_statuses).")";
			}
			if (isset($_GET[$var]) && $_GET[$var] === "prman_".$id)
			{
				$name = "<b>".$name."</b>";
			}

			$tv->add_item("pr_mgr",array(
				"name" => $name,
				"id" => "prman".$id,
				"iconurl" => icons::get_icon_url(CL_CRM_PERSON),
				"url" => aw_url_change_var($var, "prman_".$id),
			));
/*
			if(sizeof($pm_statuses))
			{
				foreach($pm_statuses as $status => $st_count)
				{
					$name = $states[$status]." (".$st_count.")";
					if (isset($_GET[$var]) && $_GET[$var] == "prman_".$id."_".$status)
					{
						$name = "<b>".$name."</b>";
					}

					$tv->add_item("prman".$id,array(
						"name" => $name,
						"id" => "prman_".$id."_".$status,
						"iconurl" => icons::get_icon_url(CL_CRM_BILL),
						"url" => aw_url_change_var($var, "prman_".$id."_".$status),
					));
				}
			}*/
		}

		$tv->add_item(0,array(
			"name" => t("Kliendihaldur"),
			"id" => "cust_mgr",
//			"url" => aw_url_change_var($var, $stat_id+10),
		));

		foreach($this->all_client_managers()->names() as $id => $name)
		{
			if(!$name)
			{
				continue;
			}
			if (isset($_GET[$var]) && $_GET[$var] == "custman_".$id)
			{
				$name = "<b>".$name."</b>";
			}
			$tv->add_item("cust_mgr",array(
				"name" => $name,
				"id" => "custman".$id,
				"iconurl" => icons::get_icon_url(CL_CRM_PERSON),
				"url" => aw_url_change_var($var, "custman_".$id),
			));
		}

		$tv->add_item(0,array(
			"name" => t("&Uuml;ksus"),
			"id" => "unit",
//			"url" => aw_url_change_var($var, $stat_id+10),
		));

		$units = $arr["obj_inst"]->get_sections();
		$unames = $units->names();
		asort($unames);
		foreach($unames as $id => $name)
		{
			if(!$name)
			{
				continue;
			}
			if (isset($_GET[$var]) && $_GET[$var] == "unit_".$id)
			{
				$name = "<b>".$name."</b>";
			}
			$tv->add_item("unit",array(
				"name" => $name,
				"id" => "custman".$id,
				"iconurl" => icons::get_icon_url(CL_CRM_SECTION),
				"url" => aw_url_change_var($var, "unit_".$id),
			));
		}

		$tv->add_item(0,array(
			"name" => t("Klient"),
			"id" => "cust",
//			"url" => aw_url_change_var($var, $stat_id+10),
		));
		$customers_by_1_letter = array();
		$customer_names = $this->all_bill_customers()->names();
		$cust_name_sort = array();
		foreach($customer_names as $customer_id => $customer_name)
		{
			$cust_name_sort[$customer_id] = strtolower($customer_name);

		}
		asort($cust_name_sort);//arr($customer_names);
		foreach($cust_name_sort as $customer_id => $customer_n)
		{
			$customer_name = $customer_names[$customer_id];//*/
//		asort($customer_names , SORT_STRING);
//		foreach($customer_names as $customer_id => $customer_name)
//		{
			if(!$customer_name)
			{
				continue;
			}
			$customers_by_1_letter[substr($customer_name,0,1)][$customer_id] = $customer_name;
		}

		foreach($customers_by_1_letter as $letter1 => $customers)
		{
			$name = $letter1 ." (".sizeof($customers).")";
			if (isset($_GET[$var]) && $_GET[$var] == "cust_".$letter1)
			{
				$name = "<b>".$name."</b>";
			}
			$tv->add_item("cust",array(
				"name" => $name,
				"id" => "cust".$letter1,
			//	"iconurl" => icons::get_icon_url(CL_CRM_COMPANY),
				"url" => aw_url_change_var($var, "cust_".$letter1),
			));

			foreach($customers as $id => $name)
			{
				if (isset($_GET[$var]) && $_GET[$var] == "cust_".$id)
				{
					$name = "<b>".$name."</b>";
				}
				$tv->add_item("cust".$letter1,array(
					"name" => $name,
					"id" => "cust".$id,
					"iconurl" => icons::get_icon_url(CL_CRM_COMPANY),
					"url" => aw_url_change_var($var, "cust_".$id),
				));
			}
		}
/*
		$tv->add_item(0,array(
			"name" => t("Periood"),
			"id" => "period",
//			"url" => aw_url_change_var($var, $stat_id+10),
		));

		$state = t("Eelmine kuu");
 		$bills_data = $this->all_bills_data("period_last");
 		$pm_statuses = array();
 		foreach($bills_data as $bd)
 		{
 			$pm_statuses[$bd["state"]] ++;
 		}
 		if(array_sum($pm_statuses))
 		{
 			$state = $state." (".array_sum($pm_statuses).")";
 		}
		if (isset($_GET[$var]) && $_GET[$var] == "period_last") $state = "<b>".$state."</b>";
		$tv->add_item("period",array(
			"name" => $state,
			"id" => "period_last",
			"url" => aw_url_change_var($var, "period_last"),
		));
 		if(sizeof($pm_statuses))
 		{
 			foreach($pm_statuses as $status => $st_count)
 			{
 				$name = $states[$status]." (".$st_count.")";
 				if (isset($_GET[$var]) && $_GET[$var] == "period_last_".$status)
 				{
 					$name = "<b>".$name."</b>";
 				}
 				$tv->add_item("period_last",array(
 					"name" => $name,
 					"id" => "period_last_".$status,
 					"iconurl" => icons::get_icon_url(CL_CRM_BILL),
 					"url" => aw_url_change_var($var, "period_last_".$status),
 				));
 			}
 		}

		$state = t("Jooksev kuu");
		$bills_data = $this->all_bills_data("period_current");
 		$pm_statuses = array();
 		foreach($bills_data as $bd)
 		{
 			$pm_statuses[$bd["state"]] ++;
 		}
 		if(array_sum($pm_statuses))
 		{
 			$state = $state." (".array_sum($pm_statuses).")";
 		}
		if (isset($_GET[$var]) && $_GET[$var] == "period_current") $state = "<b>".$state."</b>";
		$tv->add_item("period",array(
			"name" => $state,
			"id" => "period_current",
			"url" => aw_url_change_var($var, "period_current"),
		));
 		if(sizeof($pm_statuses))
 		{
 			foreach($pm_statuses as $status => $st_count)
 			{
 				$name = $states[$status]." (".$st_count.")";
 				if (isset($_GET[$var]) && $_GET[$var] == "period_current_".$status)
 				{
 					$name = "<b>".$name."</b>";
 				}
 				$tv->add_item("period_current",array(
 					"name" => $name,
 					"id" => "period_current_".$status,
 					"iconurl" => icons::get_icon_url(CL_CRM_BILL),
 					"url" => aw_url_change_var($var, "period_current_".$status),
 				));
 			}
 		}


		$state = t("J&auml;rgmine kuu");
		$bills_data = $this->all_bills_data("period_next");
 		$pm_statuses = array();
 		foreach($bills_data as $bd)
 		{
 			$pm_statuses[$bd["state"]] ++;
 		}
 		if(array_sum($pm_statuses))
 		{
 			$state = $state." (".array_sum($pm_statuses).")";
 		}
		if (isset($_GET[$var]) && $_GET[$var] == "period_next") $state = "<b>".$state."</b>";
		$tv->add_item("period",array(
			"name" => $state,
			"id" => "period_next",
			"url" => aw_url_change_var($var, "period_next"),
		));
 		if(sizeof($pm_statuses))
 		{
 			foreach($pm_statuses as $status => $st_count)
 			{
 				$name = $states[$status]." (".$st_count.")";
 				if (isset($_GET[$var]) && $_GET[$var] == "period_next_".$status)
 				{
 					$name = "<b>".$name."</b>";
 				}
 				$tv->add_item("period_next",array(
 					"name" => $name,
 					"id" => "period_next_".$status,
 					"iconurl" => icons::get_icon_url(CL_CRM_BILL),
 					"url" => aw_url_change_var($var, "period_next_".$status),
 				));
 			}
 		}


		$state = t("K&otilde;ik perioodid");
		$bills_data = $this->all_bills_data();
 		$pm_statuses = array();
 		foreach($bills_data as $bd)
 		{
 			$pm_statuses[$bd["state"]] ++;
 		}
 		if(array_sum($pm_statuses))
 		{
 			$state = $state." (".array_sum($pm_statuses).")";
 		}
		if (isset($_GET[$var]) && $_GET[$var] == "period_all") $state = "<b>".$state."</b>";
		$tv->add_item("period",array(
			"name" => $state,
			"id" => "period_all",
			"url" => aw_url_change_var($var, "period_all"),
		));
 		if(sizeof($pm_statuses))
 		{
 			foreach($pm_statuses as $status => $st_count)
 			{
 				$name = $states[$status]." (".$st_count.")";
 				if (isset($_GET[$var]) && $_GET[$var] == "period_all_".$status)
 				{
 					$name = "<b>".$name."</b>";
 				}
 				$tv->add_item("period_all",array(
 					"name" => $name,
 					"id" => "period_all_".$status,
 					"iconurl" => icons::get_icon_url(CL_CRM_BILL),
 					"url" => aw_url_change_var($var, "period_all_".$status),
 				));
 			}
 		}*/
	}

	public function all_client_managers()
	{
		$filter = array(
			"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
			"CL_CRM_COMPANY_CUSTOMER_DATA.client_manager" =>  new obj_predicate_compare(OBJ_COMP_GREATER, 0),
		);

		$t = new object_data_list(
			$filter,
			array(
				CL_CRM_COMPANY_CUSTOMER_DATA =>  array(new obj_sql_func(OBJ_SQL_UNIQUE, "client_manager", "aw_crm_customer_data.aw_client_manager"))
			)
		);

		$ol = new object_list();
		$ol->add($t->get_element_from_all("client_manager"));
		return $ol;
	}

	private function all_units()
	{
		$filter = array(
			"class_id" => CL_CRM_SECTION,
		);

		$ol = new object_list($filter);
		return $ol;
	}

	private function all_bills_data($filt = null , $stat = null)
	{
		$filter = array(
			"class_id" => CL_CRM_BILL
		);

		if(is_oid($filt))
		{
			$filter["CL_CRM_BILL.RELTYPE_PROJECT.proj_mgr"] = $filt;
		}
		elseif($filt === "period_last")
		{
			$filter["bill_date"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, mktime(0,0,0, date("m")-1, 0, date("Y")), mktime(0,0,0, date("m"), 0, date("Y")));
		}
		elseif($filt === "period_current")
		{
			$filter["bill_date"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, mktime(0,0,0, date("m"), 0, date("Y")), mktime(0,0,0, date("m")+1, 0, date("Y")));
		}
		elseif($filt === "period_next")
		{
			$filter["bill_date"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, mktime(0,0,0, date("m")+1, 0, date("Y")), mktime(0,0,0, date("m")+2, 0, date("Y")));
		}

		if($stat != "")
		{
			$filter["state"] = $stat;
		}

		$t = new object_data_list(
			$filter,
			array(
				CL_CRM_BILL => array(
					"impl","state","customer"
				),
			)
		);
		return $t->list_data;
	}

	private function get_bill_mails($arr)
	{
		if(!$arr["bills"] || !is_array($arr["bills"]) || !sizeof($arr["bills"]))
		{
			return array();
		}
		$filter = array(
			"class_id" => CL_MESSAGE,
			"site_id" => array(),
			"lang_id" => array(),
			"parent" => $arr["bills"],
		);

		$t = new object_data_list(
			$filter,
			array(
				CL_MESSAGE => array(
					"parent","createdby","mto","created","mto_relpicker"
				),
			)
		);
		return $t->list_data;
	}

	public function all_project_managers()
	{
		$filter = array(
			"class_id" => CL_PROJECT,
			"CL_PROJECT.proj_mgr" =>  new obj_predicate_compare(OBJ_COMP_GREATER, 0),
		);

		$t = new object_data_list(
			$filter,
			array(
				CL_PROJECT=>  array(new obj_sql_func(OBJ_SQL_UNIQUE, "project_manager", "aw_projects.aw_proj_mgr"))
			)
		);

		$ol = new object_list();
		$ol->add($t->get_element_from_all("project_manager"));
		return $ol;

	}


	private function all_bill_customers()
	{
		$filter = array(
			"class_id" => CL_CRM_BILL,
			"CL_CRM_BILL.customer" =>  new obj_predicate_compare(OBJ_COMP_GREATER, 0),
		);

		$t = new object_data_list(
			$filter,
			array(
				CL_CRM_BILL => array(new obj_sql_func(OBJ_SQL_UNIQUE, "customer", "aw_crm_bill.aw_customer"))
			)
		);

		$ol = new object_list();
		$ol->add($t->get_element_from_all("customer"));
		return $ol;
	}

	function _get_quality_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];

		if($arr["request"]["tf"])
		{
			$parent = $arr["request"]["tf"];
		}

		if(!$parent)
		{
			$menu = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_QUALITY_MENU");
			$parent = $menu->id();
		}
		if(!$parent)
		{
			$parent = $arr["obj_inst"]->id();
		}

		$tb->add_button(array(
			'name' => 'new',
			'img' => 'new.gif',
			'tooltip' => t('Lisa'),
			'url' => html::get_new_url(CL_MENU, $parent, array("return_url" => get_ru()))
		));
	}

	function _get_quality_tree($arr)
	{
		$tv =& $arr["prop"]["vcl_inst"];
		$var = "st";
		classload("core/icons");

		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"persist_state" => true,
			"tree_id" => "quality_bills_tree",
		));

		$bills_inst = get_instance(CL_CRM_BILL);
		$states = $bills_inst->states;



		$menu = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_QUALITY_MENU");
		if(is_object($menu))
		{
			$tv->add_item(0,array(
				"name" => t("T&uuml;&uuml;bid"),
				"id" => $menu->id(),
				"url" => aw_url_change_var($var, $menu->id()),
			));

			$ol = new object_list(array(
				"class_id" => CL_MENU,
				"parent" => $menu->id(),
			));
			foreach($ol->names() as $id => $name)
			{
				if($arr["request"][$var] == $id)
				{
					$name = "<b>".$name."</b>";
				}
				$tv->add_item($menu->id(),array(
					"name" => $name,
					"id" => $id,
					"url" => aw_url_change_var($var, $id),
				));
			}
		}
	}

	function _get_quality_list($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];

		$t->define_field(array(
			"caption" => t("Nimi"),
			"name" => "name",
			"align" => "center",
			"sortable" => 1,
		));
/*		$t->define_field(array(
			"caption" => t("Arve"),
			"name" => "bill",
			"align" => "center",
			"sortable" => 1,
		));*/
		$t->define_field(array(
			"caption" => t("Sisu"),
			"name" => "content",
			"align" => "center",
			"sortable" => 1,
		));

		if(is_oid($arr["request"]["st"]))
		{
			$ol = new object_list(array(
				"parent" => $arr["request"]["st"],
			));
			foreach($ol->arr() as $o)
			{
				$t->define_data(array(
					"name" => $o->comment(),
					"content" => $o->name(),
//					"bill" => html::href(array(
//						"caption" => $o->prop("parent.bill_no"),
//						"url" => html::obj_change_url($o->parent() , array())
//					)),
				));
			}

		}

	}

}
