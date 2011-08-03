<?php

class task_object extends _int_object
{
	const CLID = 244;

	protected $_no_display;

	function get_prop($pn)
	{
		switch($pn)
		{
			case "time_real":
				if($this->class_id() == CL_TASK)
				{
					$pn = "num_hrs_real";
				}
				break;
			case "time_to_cust":
				if($this->class_id() == CL_TASK)
				{
					$pn = "num_hrs_to_cust";
				}
				break;
			case "time_guess":
				if($this->class_id() == CL_TASK)
				{
					$pn = "num_hrs_guess";
				}
				break;
		}

		$ret =  parent::get_prop($pn);
		return $ret;
	}

	function set_name($name)
	{
		$rows = $this->get_all_primary_rows();
		foreach($rows->arr() as $row)
		{
			$row->update_name();
		}
		return parent::set_name($name);
	}

	function get_all_primary_rows()
	{
		if(!is_oid($this->id()))
		{
			return new object_list();
		}
		$rows = new object_list(array(
			"class_id" =>  CL_TASK_ROW,
			"lang_id" => array(),
			"site_id" => array(),
			"primary" => 1,
			"task" => $this->id(),
		));
		return $rows;
	}

	public function save($check_state = false)
	{
		if (!is_oid($this->id()))
		{
			if ($this->is_property("send_bill") and 0 !== $this->prop("send_bill"))
			{
				$this->set_prop("send_bill", 1);
			}
		}
		$res =  parent::save($check_state);

		$this->update_all_rows();
		return $res;
	}

	private function update_all_rows()
	{
		$comments = new object_list();
		$comments->add($this->get_all_rows());
		foreach($comments->arr() as $comment)
		{
			$comment->save();
		}
	}

	//millegi p2rast m6nes olukorras on vendade kustutamisega probleeme ja annab errorit... seega teeb selle enne 2ra
	public function delete($full_delete = false)
	{
		list($tmp) = $GLOBALS["object_loader"]->ds->search(array(
			"brother_of" => $this->id()
		));
		$todelete = array_keys($tmp);
		foreach($todelete as $id)
		{
			if($id != $this->id())
			{
				$brother = obj($id);
				$brother->delete();
			}
		}

		$r = parent::delete($full_delete);

		if (is_oid($this->prop("hr_schedule_job")))
		{
			$job = new object($this->prop("hr_schedule_job"));
			$job->delete($full_delete);
		}

		return $r;
	}

	function _init_override_object()
	{
	}

	function sum_guess()
	{
		$sum = 0;
		if($this->prop("num_hrs_guess"))
		{
			$sum = $this->get_hr_price()*$this->prop("num_hrs_guess");
		}
		return $sum;
	}

	function get_hr_price()
	{
		if ($this->prop("hr_price"))
		{
			return $this->prop("hr_price");
		}
		$conns = $this->connections_to(array());
		foreach($conns as $conn)
		{
			if($conn->prop('from.class_id')==CL_CRM_PERSON)
			{
				$pers = $conn->from();
				// get profession
				$rank = $pers->prop("rank");
				if (is_oid($rank) && $this->can("view", $rank))
				{
					$rank = obj($rank);
					if($rank->prop("hr_price"))
					{
						//salvestada, et m6nes teises vaates teisi arve ei n2itaks
						$this->set_prop("hr_price" , $rank->prop("hr_price"));
						$this->save();
						return $rank->prop("hr_price");
					}
				}
			}
		}
		return 0;
	}

	/** returns all row object ids
		@attrib api=1
		@returns array
	**/
	public function get_all_rows()
	{
		$ret = array();
		$conns = $this->connections_from(array(
			"type" => "RELTYPE_ROW",
		));
		foreach($conns as $con)
		{
			$ret[] = $con->prop("to");
		}
		return $ret;
	}

	/** sets task "real time" to rows "real time" sum
		@attrib api=1
	**/
	public function update_hours()
	{
		$hours = $this->get_row_hours();
		$this->set_prop("time_real", $hours);
		$this->save();
		$GLOBALS["do_not_change_task_real_time"] = 1;//nii ei saa yle salvestada vana v22rtuse klassi vaates
	}

	/** sets task "time to customer" to rows "time to customer" sum
		@attrib api=1
	**/
	public function update_cust_hours()
	{
		$hours = $this->get_row_cust_hours();
		$this->set_prop("time_to_cust", $hours);
		$this->save();
		$GLOBALS["do_not_change_task_cust_time"] = 1;
	}

	/** returns all rows time to customer sum
		@attrib api=1
		@returns int
			row hours sum
	**/
	public function get_row_cust_hours()
	{
		$hours = 0;
		foreach($this->get_rows_data() as $bcs)
		{
			$hours+= $bcs["time_to_cust"];
		}
		return $hours;
	}

	/** returns all rows real time sum
		@attrib api=1
		@returns int
			row hours sum
	**/
	public function get_row_hours()
	{
		$hours = 0;
		foreach($this->get_rows_data() as $bcs)
		{
			$hours+= $bcs["time_real"];
		}
		return $hours;
	}

	/** returns all rows data
		@attrib api=1
		@returns array
			row object
	**/
	public function get_rows_data()
	{
		$filter = array(
			"class_id" => CL_TASK_ROW,
			"task" => $this->id(),
			"lang_id" => array()
		);
		$req = array
		(
			CL_TASK_ROW => array(
				 "time_real" => "time_real",
				"time_to_cust" => "time_to_cust"
			)
		);
		$row_arr = new object_data_list($filter , $req);
		return $row_arr->arr();
	}

	/** makes new task row
		@attrib api=1 params=pos
		@param person optional type=id default=current_person
		@returns object
			row object
	**/
	public function add_row($person = null)
	{
		if (empty($person))
		{
			$person = get_current_person();
			$person = $person->id();
		}
		$new_row = new object();
		$new_row->set_class_id(CL_TASK_ROW);
		$new_row->set_parent($this->id());
		$new_row->set_prop("task" , $this->id());
		$new_row->set_prop("impl" , $person);

		$time = $this->prop("end");
		if(!($time > 0))
		{
			$time = $this->prop("start1");
		}
		if(!($time > 0))
		{
			$time =  time();
		}
		$new_row->set_prop("date" , $time);

		$new_row->save();
		$this->connect(array(
			"to" => $new_row->id(),
			"type" => "RELTYPE_ROW"
		));
		return $new_row;
	}

	function get_all_expenses()
	{
		$ret = array();
		$conns = $this->connections_from(array(
			"type" => "RELTYPE_EXPENSE",
		));
		foreach($conns as $con)
		{
			$ret[] = $con->prop("to");
		}
		return $ret;
	}

	/** returns all billable expenses
		@attrib api=1
		@returns object list
	**/
	function get_billable_expenses()
	{
		$ret = new object_list();
		$conns = $this->connections_from(array(
			"type" => "RELTYPE_EXPENSE",
		));
		$ti = get_instance(CL_TASK);
		foreach($conns as $con)
		{
			$o = $con->to();
			if(!$ti->can("view" , $o->prop("bill_id")))
			{
				$ret->add($o->id());
			}
		}
		return $ret;
	}

	/** sets bill_id prop to all billable expenses
		@attrib api=1
		@param bill_id required type=oid
		@returns boolean
			1 if successful, else 0
	**/
	function set_billable_oe_bill_id($bill_id)
	{
		if(!is_oid($bill_id))
		{
			return 0;
		}
		$billable_oe = $this->get_billable_expenses();
		foreach($billable_oe->arr() as $boe)
		{
			$boe->set_prop("bill_id" , $bill_id);
			$boe->save();
		}
		return 1;
	}

	/** returns task client manager oid
		@attrib api=1
		@returns oid
	**/
	function get_client_mgr()
	{
		return $this->prop("customer.client_manager");
	}

	/** returns task client manager name
		@attrib api=1
		@returns string
	**/
	function get_client_mgr_name()
	{
		return $this->prop("customer.client_manager.name");
	}

	/** user can check the send_bill checkbox or not
		@attrib api=1
		@returns boolean
			true , if can, false if not
	**/
	function if_can_set_billable()
	{
		$seti = get_instance(CL_CRM_SETTINGS);
		$sts = $seti->get_current_settings();
		if ($sts && $sts->prop("billable_only_by_mrg"))
		{
			$u = get_instance(CL_USER);
			$p = $u->get_current_person();
			if($this -> get_client_mgr() == $p)
			{
				return true;
			}
			return false;
		}
		else
		{
			return true;
		}
	}

	/** returns task primary row for person
		@attrib api=1 params=pos
		@param person type=oid default=current_person
			person object id
		@returns object
			row object
	**/
	public function get_primary_row_for_person($person = null)
	{
		if (empty($person))
		{
			$person = get_current_person();
			$person = $person->id();
		}

		$ol = new object_list(array(
			"class_id" =>  CL_TASK_ROW,
			"lang_id" => array(),
			"impl" => $person,
			"site_id" => array(),
			"primary" => 1,
			"task" => $this->id(),
		));
		return $ol->begin();
	}

	public function set_primary_row($data)
	{
		if(!$data["person"])
		{
			$u = get_instance(CL_USER);
			$data["person"] = $u->get_current_person();
		}

		$row = $this->get_primary_row_for_person($data["person"]);
		if(!$row)
		{
			$person = obj($data["person"]);
			$row = $this->add_row($data["person"]);
			$name = $this->name()." ".($person->name() ? $person->name() : "")." ".t("tegevus");
			$row->set_name($name);
			$row->set_prop("content" , $name);
			$row->set_prop("impl" , $data["person"]);
			$row->set_prop("primary" , 1);
			$row->set_prop("done" , 1);
		}
		foreach($data as $prop => $value)
		{
			if($row->is_property($prop))
			{
				$row->set_prop($prop , $value);
			}
		}
		$row->save();
		return $row->id();
	}

	public function set_party($data)
	{
		if(!$data["participant"])
		{
			$u = get_instance(CL_USER);
			$data["participant"] = $u->get_current_person();
		}
		$row = $this->get_party_obj($data["participant"]);
		if(!$row)
		{
			$row = $this->add_party($data["participant"]);
		}
		foreach($data as $prop => $value)
		{
			if($row->is_property($prop))
			{
				$row->set_prop($prop , $value);
			}
		}
		$row->save();
	}

	private function add_party($part)
	{
		$p = obj($part);
		$new_row = new object();
		$new_row->set_class_id(CL_CRM_PARTY);
		$new_row->set_parent($this->id());
		$new_row->set_name($p->name()." ".$this->name()." ".t("osalus"));
		$new_row->set_prop("task" , $this->id());
		$new_row->set_prop("participant" , $part);
		$new_row->save();
		return $new_row;
	}

	public function has_work_time()
	{
		$u = get_instance(CL_USER);
		if(!is_oid($person = $u->get_current_person()))
		{
			return null;
		}
		$row = $this->get_primary_row_for_person($person);
		if(is_object($row) && $row->prop("time_real"))
		{
			return 1;
		}
		return null;
	}

	/** returns party object for participant
		@attrib api=1 params=pos
		@param part required type=oid
		@returns object
	**/
	public function get_party_obj($part)
	{
		if(!is_oid($part)) return null;
		$ol = new object_list(array(
			"class_id" =>  CL_CRM_PARTY,
			"lang_id" => array(),
			"participant" => $part,
			"site_id" => array(),
			"task" => $this->id(),
			"limit" => 1,
		));
		return $ol->begin();
	}


	/** returns spent time for project
		@attrib api=1 params=pos
		@param project required type=oid
			project id
		@param time optional type=double
			full time spent
		@returns double
	**/
	public function get_time_for_project($project, $time)
	{
		if(!$time)
		{
			;
		}
		if($party = $this->get_party_obj($project))
		{
			if($party->prop("hours"))
			{
				return $party->prop("hours");
			}
			if($party->prop("percentage"))
			{
				return ($party->prop("percentage") * $time) / 100;
			}
		}
		else
		{
			return $time;
		}

	}

	/** returns task lifespan
		@attrib api=1 params=name
		@returns int
	**/
	public function get_lifespan($arr)
	{
		// calculate timestamp
		$i_created = $this->created();
		if ($this->prop("bug_status") == BUG_CLOSED)
		{
			$o_bug_comments = new object_list(array(
				"class_id" => array(CL_TASK_ROW,CL_BUG_COMMENT),
				"lang_id" => array(),
				"site_id" => array(),
				"parent" => $this->id(),
				"sort_by" => "objects.created"
			));

			$i_lifespan = end($o_bug_comments->arr())->created() - $i_created;
		}
		else
		{
			$i_lifespan = time() - $i_created;
		}

		// format output
		$i_lifespan_hours = $i_lifespan/3600;
		if ($i_lifespan_hours<=24)
		{
			if ($arr["only_days"])
			{
				if ($arr["without_string_prefix"])
				{
					$s_out = round($i_lifespan_hours/24);
				}
				else
				{
					$s_out = ($i_temp = round($i_lifespan_hours/24))==1 ? $i_temp." ".t("tund") : $i_temp." ".t("tundi");
				}
			}
			else
			{
				if ($arr["without_string_prefix"])
				{
					$s_out = round($i_lifespan_hours);
				}
				else
				{
					$s_out = ($i_temp = round($i_lifespan_hours))==1 ? $i_temp." ".t("tund") : $i_temp." ".t("tundi");
				}
			}
		}
		else
		{
			if ($arr["without_string_prefix"])
			{
				$s_out = round($i_lifespan_hours/24);
			}
			else
			{
				$s_out = ($i_temp = round($i_lifespan_hours/24))==1 ? $i_temp." ".t("p&auml;ev") : $i_temp." ".t("p&auml;eva");
			}
		}

		return $s_out;
	}

	/** returns bug participants object list
		@attrib api=1
	**/
	public function get_participants()
	{
		$ol = new object_list();

		$rows = new object_list(array(
			"class_id" =>  CL_TASK_ROW,
			"lang_id" => array(),
			"site_id" => array(),
			"primary" => 1,
			"task" => $this->id(),
		));
		foreach($rows->arr() as $row)
		{
			$ol->add($row->prop("impl"));
		}
		//kunagi peaks edasise 2ra kustutama
		$types = array(10, 8);
		if ($this->class_id() == CL_CRM_CALL)
		{
			$types = 9;
		}
		if ($this->class_id() == CL_CRM_MEETING)
		{
			$types = 8;
		}

		foreach($this->connections_to(array("type" => $types)) as $c)
		{
			$ol->add($c->prop("from"));
		}
		return $ol;
	}



	/** returns task projects
		@attrib api=1
		@return object list
	**/
	public function get_projects()
	{
		$ol = new object_list(array(
			"class_id" =>  CL_CRM_PARTY,
			"lang_id" => array(),
			"participant.class_id" => CL_PROJECT,
			"site_id" => array(),
			"task" => $this->id(),
			"limit" => 1,
		));
		$projects = new object_list();
		foreach($ol->arr() as $party)
		{
			$projects->add($party->prop("project"));
		}

		$conns = $this->connections_from(array(
			"type" => "RELTYPE_PROJECT",
		));
		foreach($conns as $con)
		{
			$projects->add($con->prop("to"));
		}

		return $projects;
	}

	/** returns task project ids
		@attrib api=1
		@return array
	**/
	public function get_project_ids()
	{
		$ol = new object_list(array(
			"class_id" =>  CL_CRM_PARTY,
			"lang_id" => array(),
			"participant.class_id" => CL_PROJECT,
			"site_id" => array(),
			"task" => $this->id(),
			"limit" => 1,
		));
		$projects = array();
		foreach($ol->arr() as $party)
		{
			$projects[] = $party->prop("project");
		}

		$conns = $this->connections_from(array(
			"type" => "RELTYPE_PROJECT",
		));
		foreach($conns as $con)
		{
			$projects[] = $con->prop("to");
		}

		return $projects;
	}

	/** returns task orderers
		@attrib api=1
		@return object list
	**/
	public function get_orderers()
	{
		$ol = new object_list();
		if(is_oid($this->id()))
		{
			$conns = $this->connections_from(array(
				"type" => "RELTYPE_CUSTOMER",
			));
			foreach($conns as $con)
			{
				$ol->add($con->prop("to"));
			}
		}

		return $ol;
	}

	/** returns task orderer ids
		@attrib api=1
		@return array
	**/
	public function get_orderer_ids()
	{
		$ol = array();
		if(is_oid($this->id()))
		{
			$conns = $this->connections_from(array(
				"type" => "RELTYPE_CUSTOMER",
			));
			foreach($conns as $con)
			{
				$ol[] = $con->prop("to");
			}
		}

		return $ol;
	}

	/** creates new bug object with task info
		@attrib api=1
		@return oid
			new bug object id
	**/
	public function create_bug()
	{
		$o = new object();
		$o->set_class_id(CL_BUG);
		$o->set_parent($this->id());
		$o->set_name($this->name());
		foreach($this->get_orderers()->arr() as $orderer)
		{
			if($orderer->class_id() == CL_CRM_COMPANY)
			{
				$o->set_prop("customer" , $orderer->id());
			}
		}
		$u = get_instance(CL_USER);
		$o->set_prop("bug_status" , BUG_OPEN);
		$o->set_prop("deadline" , (time() + 3600*24*360));
		$o->set_prop("who" , $u->get_current_person());
		$o->set_prop("bug_content" , $this->prop("content"));
		$o->save();

		return $o->id();
	}

	/** returns the number of created bugs connected to this task
		@attrib api=1
		@return int
			number of bugs
	**/
	public function bug_created()
	{
		$ol = new object_list(array(
			"class_id" => CL_BUG,
			"parent" => $this->id(),
		));

		return $ol->count();
	}

	/** adds customer
		@attrib api=1 params=pos
		@param customer required type=oid
			customer id
		@return oid
			customer id
	**/
	public function add_customer($customer)
	{
		if (is_oid($customer))
		{
			$this->connect(array(
				"to" => $customer,
				"type" => "RELTYPE_CUSTOMER"
			));
			$this->set_prop("customer" , $customer);
			$this->save();
		}
		return $customer;
	}

	/** adds project
		@attrib api=1 params=pos
		@param project required type=oid
			project id
		@return oid
			project id
	**/
	public function add_project($project)
	{
		if (is_oid($project))
		{
			$this->connect(array(
				"to" => $project,
				"type" => "RELTYPE_PROJECT"
			));
		}
		return $customer;
	}

	public function set_participant_data($data)
	{
		if(!($row = $this->get_primary_row_for_person($data["person"])))
		{
			$this->add_participant($data["person"]);
		}
		return $this->set_primary_row($data);
	}

	/** adds participant
		@attrib api=1 params=pos
		@param person optional type=oid
			person id
	**/
	public function add_participant($person = null)
	{
		$pl = get_instance(CL_PLANNER);
		if($person)
		{
			$p = obj($person);
		}
		else
		{
			$u = get_instance(CL_USER);
			$p = obj($u->get_current_person());
		}

		$p->connect(array(
			"to" => $this->id(),
			"reltype" => "RELTYPE_PERSON_TASK"
		));

		// also add to their calendar
		if (($cal = $pl->get_calendar_for_person($p)))
		{
			$pl->add_event_to_calendar(obj($cal), $this);
		}

		$data = array(
			"person" => $p->id(),
		);
		return $this->set_primary_row($data);
	}

	/** Tells if person is a participant in this task
		@attrib api=1 params=pos
		@param person type=CL_CRM_PERSON
	**/
	public function has_participant(object $person)
	{
		return $person->is_connected_to(array("to" => $this->id(), "reltype" => "RELTYPE_PERSON_TASK"));
	}
}
