<?php

class project_obj extends _int_object implements crm_sales_price_component_interface, crm_offer_row_interface
{
	const CLID = 239;

	public function awobj_set_participants($pv)
	{
		$set = (array) $this->prop("implementor");
		foreach((array)$this->prop("orderer") as $val)
		{
			$set[$val] = $val;
		}

		foreach($set as $id)
		{
			$this->connect(array(
				"to" => $id,
				"type" => "RELTYPE_PARTICPANT"
			));
			$pv[$id] = $id;
		}

		return $this->set_prop("participants", $pv);
	}

	function save($exclusive = false, $previous_state = null)
	{
		$new = !$this->is_saved();
		$rv = parent::save($exclusive, $previous_state);
		if ($new && !count($this->connections_from(array("type" => "RELTYPE_IMPLEMENTOR"))))
		{
			$c = get_current_company();
			$this->connect(array(
				"to" => $c->id(),
				"type" => "RELTYPE_IMPLEMENTOR"
			));
		}
		return $rv;
	}

	/** Returns all or found people associated with this project (orderers, implementors, participants and their employees if they're organizations)
		@attrib api=1 params=pos
		@param name_search type=string default=""
			Return onlly people whose name contains search string
		@param type type=string default="everyone"
			Role (everyone|orderers|implementors|participants)
		@comment
		@returns object_list
		@errors
	**/
	public function get_people($name_search = "", $type = "everyone")
	{
		// add independent people
		$people = new object_list($this->connections_from(array(
			"type" => array(
				"RELTYPE_ORDERER",
				"RELTYPE_IMPLEMENTOR",
				"RELTYPE_PARTICPANT",
			),
			"to.class_id" => crm_person_obj::CLID
		)));

		// add organized people
		$organizations = array();
		$list = new object_list($this->connections_from(array(
			"type" => array(
				"RELTYPE_ORDERER",
				"RELTYPE_IMPLEMENTOR",
				"RELTYPE_PARTICPANT"
			),
			"to.class_id" => crm_company_obj::CLID
		)));
		$organizations = $list->ids();
		$organizations = array_unique($organizations);

		$work_relations = crm_person_work_relation_obj::find(null, null, $organizations);

		if($work_relations->count())
		{
			$o = $work_relations->begin();

			do
			{
				if (object_loader::can("view", $o->prop("employee")))
				{
					$people->add($o->prop("employee"));
				}
			}
			while ($o = $work_relations->next());
		}

		return $people;
	}


	/** Returns project tasks
		@attrib api=1 params=name
		@param from optional type=int
			Filter date from
		@param to optional type=int
			Filter date to
		@param done optional type=bool
		@returns
			object_list
	**/
	public function get_tasks($arr)
	{
		$arr["clid"] = CL_TASK;
		$filter = $this->_get_tasks_filter($arr);
		$ol = new object_list($filter);
		return $ol;
	}

	/** Returns project meetings
		@attrib api=1 params=name
		@param from optional type=int
			Filter date from
		@param to optional type=int
			Filter date to
		@param done optional type=bool
		@returns
			object_list
	**/
	public function get_meetings($arr)
	{
		$arr["clid"] = CL_CRM_MEETING;
		$filter = $this->_get_tasks_filter($arr);
		$ol = new object_list($filter);
		return $ol;
	}

	/** Returns project calls
		@attrib api=1 params=name
		@param from optional type=int
			Filter date from
		@param to optional type=int
			Filter date to
		@param done optional type=bool
		@returns
			object_list
	**/
	public function get_calls($arr)
	{
		$arr["clid"] = CL_CRM_CALL;
		$filter = $this->_get_tasks_filter($arr);
		$ol = new object_list($filter);
		return $ol;
	}

	/** returns all bugs related to current project
		@attrib api=1 params=name
		@param start optional
			time between start
		@param end optional
			time between end
		@param status optional type=bool
		@param participant optional type=string/oid
		@returns object list
	**/
	public function get_bugs($arr = array())
	{
		$filter = $this->_get_bugs_filter($arr);
		$ol = new object_list($filter);
		return $ol;
	}

	/** returns bugs related to current project
		@attrib api=1 params=name
		@param from optional type=int
			time between start
		@param to optional
			time between end type=int
		@param status optional type=int
			bug status
		@returns object list
	**/
	public function get_bugs_data($arr = array())
	{
		$filter = $this->_get_bugs_filter($arr);
		$bugres = array(
			CL_BUG => array("bug_status"),
		);
		$rows_arr = new object_data_list($filter , $bugres);

		return $rows_arr->list_data;
	}

	private function _get_bugs_filter($arr = array())
	{
		$filter = array(
			"class_id" => CL_BUG,
			"project" => $this->id(),
			"sort_by" => "objects.created desc"
		);
		$from = isset($arr["from"]) ? (int) $arr["from"] : 0;
		$to = isset($arr["to"]) ? (int) $arr["to"] : 0;
		$participant = isset($arr["participant"]) ? $arr["participant"] : "";
		$status = isset($arr["status"]) ? (int) $arr["status"] : 0;

		if ($from && $to)
		{
			$filter["CL_BUG.RELTYPE_COMMENT.created"] = new obj_predicate_compare(OBJ_COMP_BETWEEN, $from-1, $to);
		}
		elseif ($from)
		{
			$filter["CL_BUG.RELTYPE_COMMENT.created"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $from);
		}
		elseif ($to)
		{
			$filter["CL_BUG.RELTYPE_COMMENT.created"] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $to);
		}

		if($participant)
		{
			if(is_oid($participant))
			{
				$filter["CL_BUG.RELTYPE_MONITOR"] = $participant;
			}
			else
			{
				$filter["CL_BUG.RELTYPE_MONITOR.name"] = "%".$participant."%";
			}
		}

		if($status)
		{
			$filter["bug_status"] = $status;
		}

		return $filter;
	}

	/** returns all billable bugs related to current project
		@attrib api=1 params=pos
		@returns object list
	**/
	function get_billable_bugs()
	{
/*		$all_bugs = new object_list(array(
			"lang_id" => array(),
			"site_id" => array(),
			"class_id" => CL_BUG,
			"project" => $this->id(),
			"sort_by" => "objects.created desc",
			"send_bill" => 1,
		));*/
		$bugs = array();
		$bugs_list = new object_list();
		$rows_filter = $this->get_billable_bug_rows_filter();
		$rowsres = array(
			CL_TASK_ROW => array(
				"task" => "task",
			),
		);
		$rows_arr = new object_data_list($rows_filter , $rowsres);
		foreach($rows_arr->list_data as $bcs)
		{
			$bugs[$bcs["task"]] = $bcs["task"];
		}

		$bugs_list->add($bugs);
		return $bugs_list;
	}

	private function get_billable_bug_rows_filter()
	{
		$filter = array(
			"class_id" => CL_TASK_ROW,
			"bill_id" => new obj_predicate_compare(OBJ_COMP_EQUAL, ''),
			"on_bill" => 1,
			"done" => 1,
			"task.class_id" => CL_BUG,
//			"CL_TASK_ROW.task(CL_BUG).send_bill" => 1,
			"CL_TASK_ROW.RELTYPE_PROJECT" => $this->id(),
		);
		return $filter;
	}

	/** Returns an object_list with all bug comments for the project
		@attrib api=1 params=pos

		@param date_from optional type=int
			Filter date from

		@param date_to optional type=int
			Filter date to

		@returns
			object_list instance with the bug comments in it
	**/
	function get_bug_comments($date_from = null, $date_to = null)
	{
		$bug_ol = $this->get_bugs();
		if(!sizeof($bug_ol->ids()))
		{
			return new object_list();
		}
		$filt = array(
			"class_id" => array(CL_BUG_COMMENT,CL_TASK_ROW),
			"parent" => $bug_ol->ids(),
			"lang_id" => array(),
			"site_id" => array(),
		);
		if ($date_from !== null && $date_to !== null)
		{
			$filt["created"] = new obj_predicate_compare(OBJ_COMP_BETWEEN, ($date_from - 1), ($date_to + 1));
		}
		else
		if ($date_from !== null)
		{
			$filt["created"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $date_from);
		}
		else
		if ($date_to !== null)
		{
			$filt["created"] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $date_to);
		}
		$com_ol = new object_list($filt);
		return $com_ol;
	}

	function get_products()
	{
		$ol = new object_list($this->connections_from(array(
				"type" => "RELTYPE_PRODUCT"
		)));
		// = new object_list($filter);
		return $ol;
	}

	/** Returns number of hours spent on the project, group by person and object type (taskt,bug,call,..)
		@attrib api=1 params=pos

		@param person_filter optional type=array
			array { person_id, ... } if array then only the given persons stats are returned if not, then all persons in the project

		@param date_filter optional type=array
			array { from => timestamp, to => timestamp } if set, then data is filtered by the gievn dates

		@returns
			array {
				person_id => array {
					class_id => array {
						paid => hour_count,
						unpaid => hour_count,
						act_type => array {
							cl_crm_activity_stats_type oid => hours_count,
							...
						}
					},
					...
				},
				...
			}
	**/
	function stats_get_by_person($person_filter = null, $date_filter = null)
	{
		$rv = array();

		$this->_stats_insert_crm($rv, $person_filter, $date_filter);
		$this->_stats_insert_bugs($rv, $person_filter, $date_filter);

		return $rv;
	}

	private function _stats_insert_crm(&$rv, $person_filter, $date_filter)
	{
		// classes that take time from projects crm_call crm_meeting task bug
		$filt = array(
			"class_id" => array(CL_CRM_CALL, CL_CRM_MEETING, CL_TASK),
			"project" => $this->id()
		);
		$ol = new object_list($filt);

		$member2o = $this->_stats_get_member_list($ol->ids());
		foreach($ol->arr() as $o)
		{
			switch($o->class_id())
			{
				case CL_CRM_CALL:
				case CL_CRM_MEETING:
					foreach(safe_array($member2o[$o->id()]) as $person)
					{
						$rv[$person][$o->class_id()] = array(
							"paid" => (double)$o->time_to_cust,
							"unpaid" => (double)$o->time_real
						);
					}
					break;

				case CL_TASK:
					foreach(safe_array($member2o[$o->id()]) as $person)
					{
						$rv[$person][$o->class_id()] = array(
							"paid" => (double)$o->num_hrs_to_cust,
							"unpaid" => (double)$o->num_hrs_real
						);
						foreach($o->get_all_rows() as $row_o)
						{
							$rv[$person][$o->class_id()]["paid"] += (double)$o->time_to_cust;
							$rv[$person][$o->class_id()]["unpaid"] += (double)$o->time_real;
						}
					}
					break;
			}
		}
	}

	/** returns task => array { person,...} **/
	private function _stats_get_member_list($ids)
	{
		if (!count($ids))
		{
			return array();
		}
		$c = new connection();
		$rels = $c->find(array(
			"to" => $ids,
			"from.class_id" => CL_CRM_PERSON
		));
		$rv = array();
		foreach($rels as $rel)
		{
			$rv[$rel["to"]][] = $rel["from"];
		}
		return $rv;
	}

	private function _stats_insert_bugs(&$rv, $person_filter, $date_filter)
	{
		$filt_bug = array(
			"class_id" => CL_BUG,
			"project" => $this->id()
		);
		$ol2 = new object_list($filt_bug);
		$bug_ids = $ol2->ids();

		$bug_comments = new object_list(array(
			"class_id" => array(CL_BUG_COMMENT,CL_TASK_ROW),
			"parent" => $bug_ids
		));
		foreach($bug_comments->arr() as $com)
		{
			$person_id = $this->_get_person_from_user($com->createdby());

			$rv[$person_id][CL_BUG]["paid"] += (double)$com->add_wh_cust;
			$rv[$person_id][CL_BUG]["unpaid"] += (double)$com->add_wh;
			$rv[$person_id][CL_BUG]["act_type"][(int)$com->activity_stats_type] += (double)$com->add_wh;
		}
	}

	private function _get_person_from_user($uid)
	{
		static $cache;
		if (!isset($cache[$uid]))
		{
			$cache[$uid] = get_instance(CL_USER)->get_person_for_uid($uid)->id();
		}

		return $cache[$uid];
	}

	/** returns time spent with bugs
		@attrib api=1 params=pos
		@returns double
			spent time in hours
	**/
	function get_bugs_time($start = null, $end = null)
	{
		$sum = 0;
		if(!$start && !$end)
		{
			$ol = $this->get_bugs();
			foreach($ol->arr() as $o)
			{
				$sum += $o->prop("num_hrs_real");
			}
		}
		else
		{
			$comments = $this->get_bug_comments($start , $end);
			foreach($comments->arr() as $com)
			{
				$sum+= (double)$com->prop("add_wh");
			}
		}
		return $sum;
	}


	function get_project_bugs()
	{
		return $this->get_bugs();
	}

	/** Returns orderer id
		@attrib api=1
		@returns oid
			Orderer object id
	**/
	public function get_orderer()
	{
		$orderers = $this->get_customer_ids();
		$orderer = reset($this->get_customer_ids());
		if($orderer && $this->prop("orderer") != $orderer)
		{
			$this->set_prop("orderer" , $orderer);
			$this->save();
		}
		return $orderer;
	}

	/** Returns implementor id
		@attrib api=1
		@returns oid
			Implementor object id
	**/
	public function get_implementor()
	{
		$impl = $this->prop("implementor");
		if (is_array($impl))
		{
			$impl = reset($impl);
		}
		return $impl;
	}

	public function get_customer_ids()
	{
		$ret = array();
		foreach($this->connections_from(array("type" => "RELTYPE_ORDERER")) as $c)
		{
			$ret[$c->prop("to")] = $c->prop("to");
		}
		return $ret;
	}

	/** Returns project customers
		@attrib api=1
		@returns object list
	**/
	public function get_customers()
	{
		$ol = new object_list();
		foreach($this->connections_from(array("type" => "RELTYPE_ORDERER")) as $c)
		{
			$ol->add($c->prop("to"));
		}
		return $ol;
	}

	private function get_bills_filter($status = null)
	{
		$ids = array();
		$task_rows = $this->get_rows_data();
		foreach($task_rows as $tr)
		{
			$ids[$tr["bill_id"]] = $tr["bill_id"];
		}
/*
		$task_rows = new object_list(array(
			"class_id" => CL_TASK_ROW,
			"lang_id" => array(),
			"site_id" => array(),
			"project" => $this->id(),
		));

		foreach($task_rows->arr() as $tr)
		{
			$ids[$tr->prop("bill")] = $tr->prop("bill");
		}
*/
		if(!sizeof($ids))
		{
			$ids[] = 1;
		}

		$filter = array(
			"class_id" => CL_CRM_BILL,
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"oid" => $ids,
					"CL_CRM_BILL.RELTYPE_PROJECT" => $this->id(),
				)
			))
		);

		if(isset($status))
		{
			$filter["state"] = $status;
		}
		return $filter;

	}

	/** Returns project bills
		@attrib api=1
		@param status optional type=int
			Filter date to
		@returns object list
	**/
	public function get_all_customer_bills($arr)
	{
		$projects = array();
		$projects[$this->id()] = $this;
		$params = array();
		if(isset($arr["status"]))
		{
			$params["status"] = $arr["status"];
		}

		$customers = $this->get_customers();

		foreach($customers->arr() as $customer)
		{
			foreach($customer->get_projects_as_customer()->arr() as $project_id => $project)
			{
				$projects[$project_id] = $project;
			}
		}

		$bills = new object_list();
		foreach($projects as $id => $o)
		{
			$bills->add($o->get_bills($params));
		}
		return $bills;
	}

	/** Returns different project bill statuses sum
		@attrib api=1
		@returns array
			array(bill_status => count , ...)
	**/
	public function get_bill_state_count()
	{
		$ret = array();
		$bills = $this->get_bills();
		foreach($bills->arr() as $bill)
		{
			$ret[$bill->prop("state")]++;
		}
		return $ret;
	}

	/** Returns project bills
		@attrib api=1
		@param status optional type=int
			Filter date to
		@returns object list
	**/
	public function get_bills($arr = array())
	{
		$ol = new object_list(
			$this->get_bills_filter($arr["status"])
		);
		return $ol;
	}

	/** Returns project bills sum
		@attrib api=1
		@returns double
	**/
	public function get_bill_sum()
	{
		$bills = $this->get_bills();
		$sum = 0;
		foreach($bills->arr() as $bill)
		{
			$sum += $bill->prop("sum");
		}
		return $sum;
	}

	function get_billable_deal_tasks()
	{
//---------------------------------------------uuele systeemile ka vaja... osalustega projektidele
		$ol = new object_list(array(
			"class_id" => array(CL_TASK, CL_CRM_MEETING,CL_CRM_CALL),
			"send_bill" => 1,
			"brother_of" => new obj_predicate_prop("id"),
			"deal_price" => new obj_predicate_compare(OBJ_COMP_GREATER, 0),
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_TASK.RELTYPE_PROJECT" => $this->id(),
					"CL_CRM_MEETING.RELTYPE_PROJECT" => $this->id(),
					"CL_CRM_CALL.RELTYPE_PROJECT" => $this->id(),

				)
			)),
		));
		return $ol;
	}

	function get_billable_expenses()
	{
/*		$ol = new object_list(array(
			"class_id" => CL_CRM_EXPENSE,
			"send_bill" => 1,
			"lang_id" => array(),
			"brother_of" => new obj_predicate_prop("id"),
			"deal_price" => new obj_predicate_compare(OBJ_COMP_GREATER, 0),
		));*/

		//---------------------------suht vigane on kulude m2rkimine arvele... see yle vaadata
		$ol = new object_list();
		return $ol;
	}

	function get_billable_rows()
	{
		$ol = new object_list(array(
			"class_id" => CL_TASK_ROW,
			"bill_id" => new obj_predicate_compare(OBJ_COMP_EQUAL, ''),
			"on_bill" => 1,
			"done" => 1,
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_TASK_ROW.task(CL_TASK).send_bill" => 1,
//					"CL_TASK_ROW.task.class_id" => CL_BUG,
					"CL_TASK_ROW.task(CL_BUG).send_bill" => 1,
					"CL_TASK_ROW.task(CL_CRM_MEETING).send_bill" => 1,
					"CL_TASK_ROW.task(CL_CRM_CALL).send_bill" => 1,
				)
			)),
			"CL_TASK_ROW.RELTYPE_PROJECT" => $this->id(),
		));
		return $ol;
	}

	/** Returns project billable hours
		@attrib api=1
		@returns double
	**/
	public function get_billable_hours()
	{
		$sum = 0;
		$rows = $this->get_billable_rows();
		foreach($rows->arr() as $row)
		{
			$sum+=$row->prop("time_real");
		}
		return $sum;
	}

	public function get_billed_hours()
	{
		$billed_hours_filter = array(
			"class_id" => CL_TASK_ROW,
			"bill_id" => new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, 1),
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_TASK_ROW.task(CL_TASK).send_bill" => 1,
					"CL_TASK_ROW.task(CL_BUG).send_bill" => 1,
					"CL_TASK_ROW.task(CL_CRM_MEETING).send_bill" => 1,
					"CL_TASK_ROW.task(CL_CRM_CALL).send_bill" => 1,
				)
			)),
			"CL_TASK_ROW.RELTYPE_PROJECT" => $this->id(),
		);
		$rowsres = array(
			CL_TASK_ROW => array(
				"time_real",
				"time_to_cust",
				"time_guess",
			),
		);
		$rows_arr = new object_data_list($billed_hours_filter , $rowsres);
		$sum = 0;
		foreach($rows_arr->list_data as $data)
		{
			$sum+= $data["time_real"];
		}
		return $sum;
	}

	private function all_rows_filter()
	{
		return array(
			"class_id" => CL_TASK_ROW,
//			"bill_id" => new obj_predicate_compare(OBJ_COMP_EQUAL, ''),
//			"done" => 1,
			"CL_TASK_ROW.RELTYPE_PROJECT" => $this->id()
		);
	}

	public function get_rows_data()
	{
		$rows_filter = $this->all_rows_filter();
		$rowsres = array(
			CL_TASK_ROW => array(
				"task",
				"task.class_id",
				"time_real",
				"time_to_cust",
				"time_guess",
				"impl",
				"date",
				"bill_id"
			),
		);
		$rows_arr = new object_data_list($rows_filter , $rowsres);

		return $rows_arr->list_data;
	}

	function get_billable_task_rows()
	{
		$ol = new object_list(array(
			"class_id" => CL_TASK_ROW,
			"bill_id" => new obj_predicate_compare(OBJ_COMP_EQUAL, ''),
			"on_bill" => 1,
			"done" => 1,
			"task.class_id" => array(CL_TASK, CL_CRM_MEETING,CL_CRM_CALL),
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_TASK_ROW.task(CL_TASK).send_bill" => 1,
					"CL_TASK_ROW.task(CL_CRM_MEETING).send_bill" => 1,
					"CL_TASK_ROW.task(CL_CRM_CALL).send_bill" => 1,
				)
			)),
			"CL_TASK_ROW.RELTYPE_PROJECT" => $this->id()
		));
		return $ol;
	}

	/** Adds bill
		@attrib api=1
	**/
	public function add_bill()
	{
		$bill = obj();
		$bill->set_class_id(CL_CRM_BILL);
		$bill->set_parent($this->id());
		$bill->save();

		$ser = get_instance(CL_CRM_NUMBER_SERIES);
		$bno = $ser->find_series_and_get_next(CL_CRM_BILL,0,time());
		if (!$bno)
		{
			$bno = $bill->id();
		}

		$bill->set_prop("bill_no", $bno);
		$bill->set_prop("bill_trans_date", time());
		$bill->set_name(sprintf(t("Arve nr %s"), $bill->prop("bill_no")));

		$bill->set_project($this->id());
		$cust = $this->get_orderer();
		if ($cust)
		{
			$bill->set_prop("customer", $cust);
		}

// 		$impl = $this->get_implementor();
// 		if ($impl)
// 		{
// 			$bill->set_prop("impl", $impl);
// 		}
		$bill->set_impl();
		$bill->set_prop("bill_date", time());
		$bill->set_due_date();

		$bill->save();
		return $bill;
	}

	public function get_goals($parent = null)
	{
/*		$goals = new object_list(array(
			"class_id" => array(CL_TASK,CL_CRM_CALL,CL_CRM_MEETING),
			"project" => $this->id(),
			"predicates" => $parent,
			"brother_of" => new obj_predicate_prop("id")
		));
*/
		$goals = new object_list(array(
			"class_id" => array(CL_TASK,CL_CRM_CALL,CL_CRM_MEETING,CL_BUG),
//			"project" => $arr["obj_inst"]->id(),
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					// "oid" => $ids,
					"CL_TASK.RELTYPE_PROJECT" => $this->id(),
					"CL_CRM_MEETING.RELTYPE_PROJECT" => $this->id(),
					"CL_CRM_CALL.RELTYPE_PROJECT" => $this->id(),
					"CL_BUG.RELTYPE_PROJECT" => $this->id(),
				)
			)),
//			"predicates" => $parent,
			"brother_of" => new obj_predicate_prop("id")
		));

		//kuna nyyd asi peaks toimuma nii et mis omab connectionit, on
//		foreach($this->connections_to(array("type" => 4)) as $c)
//		{
//			$goals->add($c->prop("from"));
//		}
		return $goals;
	}

	/** returns all tasks, meetings and calls data related to current project
		@attrib api=1 params=name
		@returns object list
	**/
	public function get_all_tasks_data($arr = array())
	{
		$filter = $this->_get_tasks_filter($arr);
		$bugres = array(
			CL_TASK => array("end", "is_done"),
		);
		$rows_arr = new object_data_list($filter , $bugres);

		return $rows_arr->list_data;
	}

	private function _get_tasks_filter($arr = array())
	{
		$filter = array(
			//"class_id" => CL_TASK,
			"brother_of" => new obj_predicate_prop("id"),
		);

		$clids = array(CL_TASK => "CL_TASK", CL_CRM_MEETING => "CL_CRM_MEETING" , CL_CRM_CALL => "CL_CRM_CALL");
		if(!empty($arr["clid"]))
		{
			$search_clids = array($arr["clid"]);
		}
		else
		{
			$search_clids = array_keys($clids);
		}

		$filter["class_id"] = $search_clids;

		$project_cond = array();
		foreach($search_clids as $c)
		{
			$project_cond[$clids[$c].".RELTYPE_PROJECT"] = $this->id();
		}
		$filter[] = new object_list_filter(array(
			"logic" => "OR",
			"conditions" => $project_cond,
		));

		$time_filt = false;
		if (isset($arr["from"]) && $arr["from"] > 1 && !empty($arr["to"]))
		{
			$time_filt = new obj_predicate_compare(OBJ_COMP_BETWEEN, $arr["from"], $arr["to"]);
		}
		elseif (isset($arr["from"]) && $arr["from"] > 1)
		{
			$time_filt = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $arr["from"]);
		}
		elseif (isset($arr["to"]) && $arr["to"] > 1)
		{
			$time_filt = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $arr["to"]);
		}

		if(isset($arr["done"]))
		{
			if($arr["done"])
			{
				$filter["is_done"] = 1;
			}
			else
			{
				$filter["is_done"] = new obj_predicate_not(1);
			}
		}

		if($time_filt)
		{
			$time_cond = array();
			foreach($search_clids as $c)
			{
//				$time_cond[$clids[$c].".RELTYPE_ROW.date"] = $time_filt;
				$time_cond[$clids[$c].".start1"] = $time_filt;
				$time_cond[$clids[$c].".end"] = $time_filt;
			}
			$filter[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => $time_cond,
			));
		}

		//kui k6igile osalus, siis toimisk
		if(!empty($arr["participant"]))
		{
			$rows_filter = array("class_id" => CL_TASK_ROW);
			$rows_filter["CL_TASK_ROW.RELTYPE_PROJECT"] = $this->id();
			$rows_filter["task.class_id"] = $search_clids;
			if(is_oid($arr["participant"]))
			{
				$rows_filter["CL_TASK_ROW.impl"] = $arr["participant"];
			}
			else
			{
				$rows_filter["CL_TASK_ROW.impl(CL_CRM_PERSON).name"] = "%".$arr["participant"]."%";
			}

			$task_ids = array();
			$rowsres = array(
				CL_TASK_ROW => array(
					"task" => "task",
				),
			);
			$rows_arr = new object_data_list($rows_filter , $rowsres);
			foreach($rows_arr->list_data as $bcs)
			{
				$task_ids[$bcs["task"]] = $bcs["task"];
			}

			if(sizeof($task_ids))
			{
				$filter["oid"] = $task_ids;
			}
			else
			{
				$filter["oid"] = 1;
			}
		}
		return $filter;
	}

	private function _money_format($sum)
	{
		return number_format($sum, 2);
	}

	public function get_workers_stats()
	{
		$all_data = $this->get_rows_data();
		$bills = $this->get_bills();
		$work_data = array();
		$result = array();

		foreach($bills->arr() as $bill)
		{
			$bill_sum = $bill->get_sum();
			$payments = $bill->get_payments_sum();
			$bill_rows = $bill->get_bill_rows_data();
			$projects = $bill->get_project_ids();

			if(sizeof($projects) < 2)//kui arve yhe projekti kohta
			{
				$result["on_bill"] += $bill_sum;
				$result["payments"] += $payments;
			}

			foreach($bill_rows as $br)
			{
				$people = $br["persons"];
				$paid = 0;
				if($payments)
				{
					$paid = min($payments/$bill_sum , 1);
				}
				if(!sizeof($br["task_rows"]))
				{
					$divide = sizeof($people);
					if(sizeof($people))
					{
						foreach($people as $person)
						{
							$work_data[$person]["on_bill"]+= $this->_money_format($br["sum"]/$divide);
							$work_data[$person]["payments"]+= $this->_money_format(($br["sum"]/$divide) / $paid);
							if(sizeof($projects) > 1)
							{
								$result["on_bill"] += $this->_money_format(($br["sum"]/$divide) / sizeof($projects));
								$result["payments"] += $this->_money_format(($br["sum"]/$divide) / ($paid * sizeof($projects)));
							}

						}
					}
					if(sizeof($projects) > 1)
					{
						$result["on_bill"] += $this->_money_format($br["sum"] / sizeof($projects));
						$result["payments"] += $this->_money_format($br["sum"] / ($paid * sizeof($projects)));
					}
				}
				else
				{
					$br_person_hours = array();
					$br_person_cust_hours = array();
					foreach($br["task_rows"] as $task_row)
					{
						if(!array_key_exists($task_row , $all_data))
						{
							continue;
						}
						$impl = reset($all_data[$task_row]["impl"]);
						$br_person_hours[$impl]+= $all_data[$task_row]["time_real"];
						$br_person_cust_hours[$impl]+= $all_data[$task_row]["time_to_cust"];
					}
					if(sizeof($people))
					{
						foreach($people as $person)
						{
							if(array_sum($br_person_hours))
							{
								$divide = array_sum($br_person_hours) / max(0.01 , $br_person_hours[$person]);
							}
							else
							{
								$divide = array_sum($br_person_cust_hours) / max(0.01 , $br_person_cust_hours[$person]);
							}
							$work_data[$person]["on_bill"]+= $this->_money_format($br["sum"]/$divide);
							$work_data[$person]["payments"]+= $this->_money_format(($br["sum"]/$divide) / $paid);
						}
					}
					if(sizeof($projects) > 1 && sizeof($br_person_hours))
					{
						$result["on_bill"] += $this->_money_format($br["sum"]);
						$result["payments"] += $this->_money_format($br["sum"] / $paid);
					}

				}
			}
		}

		$tasks = $task_prices = array();
		foreach($all_data as $data)
		{
			$tasks[$data["task"]] = $data["task"];
		}

		$tasks_ol = new object_list();
		$tasks_ol->add($tasks);
		foreach($tasks_ol->arr() as $to)
		{
			$task_prices[$to->id()] = $to->prop("hr_price");
		}


		foreach($all_data as $data)
		{
			$person = reset($data["impl"]);
			if(!isset($work_data[$person]))
			{
				$work_data[$person] = array();
			}
			$cust_time = $data["time_to_cust"];// ? $data["time_to_cust"] : $data["time_real"];
			$work_data[$person]["real"] +=$data["time_real"];
			$work_data[$person]["guess"] +=$data["time_guess"];
			$work_data[$person]["cust"] +=$cust_time;
			if($task_prices[$data["task"]])
			{
				$work_data[$person]["sum"] += $data["time_real"] * $task_prices[$data["task"]];
				$result["sum"] += $data["time_real"] * $task_prices[$data["task"]];
				$work_data[$person]["sum_cust"] += $cust_time * $task_prices[$data["task"]];
				$result["sum_cust"] += $cust_time * $task_prices[$data["task"]];
			}
			else
			{
				$work_data[$person]["without"] += $data["time_real"];
 				$result["without"] += $data["time_real"];
			}
 			$result["real"] +=$data["time_real"];
 			$result["guess"] +=$data["time_guess"];
 			$result["cust"] +=$cust_time;

		}

		$work_data["result"] = $result;
		return $work_data;
	}

	public function get_payments_data()
	{
		$data = array();
		$bills = $this->get_bills();
		foreach($bills->arr() as $bill)
		{
			foreach($bill->get_bill_payments_data() as $p_data)
			{
				$data[] = $p_data;
			}
		}
		return $data;
	}

	private function get_project_income()
	{
		$ret = array();
		$payments = $this->get_payments_data();
		foreach($payments as $p)
		{
			$ret[$p["currency"]]+= $p["sum"];
		}
		return $ret;
	}

	/** Returns project income in company currency
		@attrib api=1
		@returns double
	**/
	public function get_project_income_cc()
	{
		$sum = 0;
		$payments = $this->get_payments_data();
		foreach($payments as $p)
		{
			$sum+= $p["total_sum"];
		}
		return $sum;
	}

	/** Returns project work hours sum
		@attrib api=1
		@returns double
	**/
	public function get_real_hours()
	{
		$sum = 0;
		foreach($this->get_rows_data() as $data)
		{
			$sum+= $data["time_real"];
		}
		return $sum;
	}

	/** Returns project income text
		@attrib api=1
		@returns string
			ex: 123 EEK
			    225 EUR
	**/
	public function get_project_income_text()
	{
		$income = $this->get_project_income();
		$ret = array();
		foreach($income as $curr => $sum)
		{
			$ret[] = round($sum , 2)." ".$curr;
		}
		return join(html::linebreak() , $ret);
	}

	/** Returns project planned hours
		@attrib api=1
		@returns double
	**/
	public function get_planned_hours()
	{
		$sum = 0;
		foreach($this->get_rows_data() as $data)
		{
			$sum+= $data["time_guess"];
		}
		return $sum;
	}

	/** Returns project income spots
		@attrib api=1
		@returns object list
	**/
	public function get_cash_cows()
	{
		$ol = new object_list();
		foreach($this->connections_from(array("type" => "RELTYPE_CASH_COW")) as $c)
		{
			$ol->add($c->prop("to"));
		}
		return $ol;
	}

	/** Add new income spot
		@attrib api=1
	**/
	public function add_cash_cow()
	{
		$o = new object();
		$o->set_class_id(CL_CRM_CASH_COW);
		$o->set_parent($this->id());
		$o->set_name($this->name()." ".t("Tulukoht"));
		$o->save();
		$this->connect(array(
			"to" => $o->id(),
			"type" => "RELTYPE_CASH_COW"
		));
		return $o->id();
	}

	public function has_not_guessed_expenses()
	{
		return 1;
	}

	/** returns project product selection
		@attrib api=1
		@returns array
	**/
	public function get_prod_selection()
	{
		$prods = array("" => t("--vali--"));
		// get prords from co
		$u = get_instance(CL_USER);
		$co = obj($u->get_current_company());
		$wh = $co->get_first_obj_by_reltype("RELTYPE_WAREHOUSE");
		if ($wh)
		{
			$wh_i = $wh->instance();
			$pkts = $wh_i->get_packet_list(array(
				"id" => $wh->id()
			));
			foreach($pkts as $pko)
			{
				$prods[$pko->id()] = $pko->name();
			}
		}
		return $prods;
	}

	//	Written solely for testing purposes!
	public function get_units(){$ol = new object_list(array("class_id" => CL_UNIT,));return $ol;}
}
