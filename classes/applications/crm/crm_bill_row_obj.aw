<?php

class crm_bill_row_obj extends _int_object
{
	const CLID = 1054;

	private $crm_settings;

	function set_prop($p, $v)
	{
		if(is_oid($this->id()) && $v != $this->prop($p) && !(!$v && !$this->prop($p)))
		{
			$this->add_bill_comment_data(t("Rida")." ".$this->id()." ". $GLOBALS["properties"][self::CLID][$p]["caption"] ." : " .$this->prop($p). " => " .$v);
		}

		$rv = parent::set_prop($p, $v);
		return $rv;
	}

	public function awobj_set_price($value)
	{
		return $this->set_prop("price", aw_math_calc::string2float($value));
	}

	public function awobj_set_amt($value)
	{
		return $this->set_prop("amt", aw_math_calc::string2float($value));
	}

	public function awobj_set_sum($value)
	{
		return $this->set_prop("sum", aw_math_calc::string2float($value));
	}

	public function awobj_get_sum()
	{
		if($this->prop("sum"))
		{
			$sum = $this->prop("sum");
		}
		else
		{
			$sum = $this->prop("amt") * $this->prop("price");
		}

		return $sum;
	}

	private function add_bill_comment_data($data)
	{
		$_SESSION["bill_change_comments"][] = $data;
	}

	// DEPRECATED. use this::prop("sum");
	public function get_sum() { return $this->awobj_get_sum(); }

	/** checks if task row is connected to bill row
		@attrib api=1
		@returns boolean
			reservation price
	**/
	function has_task_row()
	{
		foreach($this->connections_from(array("type" => "RELTYPE_TASK_ROW"))as $c)
		{
			return 1;
		}
		return 0;
	}

	/** returns task row ids
		@attrib api=1
		@returns array
			task row ids
	**/
	function task_rows()
	{
		$arr = array();
		foreach($this->connections_from(array("type" => "RELTYPE_TASK_ROW"))as $c)
		{
			$arr[] = $c->prop("to");
		}
		return $arr;
	}


	/** Returns task row or bug connected to this bill row
		@attrib api=1
		@returns oid
			bug or task row id
	**/
	public function get_task_row_or_bug_id()
	{
		foreach($this->connections_from(array("type" => "RELTYPE_TASK_ROW"))as $c)
		{
			return $c->prop("to");
		}
		foreach($this->connections_from(array("type" => "RELTYPE_BUG"))as $c)
		{
			return $c->prop("to");
		}
		foreach($this->connections_from(array("type" => "RELTYPE_TASK"))as $c)
		{
			return $c->prop("to");
		}
		return "";
	}

	/** Returns task row or bug orderer person name
		@attrib api=1
		@returns string
			Person name
	**/
	public function get_orderer_person_name()
	{
		$problem = $this->get_task_row_or_bug_id();
		if($problem)
		{
			$problem = obj($problem);
		}
		else
		{
			return "";
		}
		if($problem->class_id() == CL_BUG)
		{
			if($ret = $problem->prop("customer_person.name"))
			{
				return $ret;
			}
		}
		if($problem->class_id() == CL_TASK_ROW)
		{
			if($ret = $problem->prop("orderer.name"))
			{
				return $ret;
			}
			if($problem->prop("task.customer.class_id") == CL_CRM_PERSON && $ret = $problem->prop("task.customer.name"))
			{
				return $ret;
			}
		}
		return "";
	}

	/** returns bill row bill id
		@attrib api=1
		@returns oid
			bill id
	**/
	function get_bill()
	{
		$bills_list = new object_list(array(
			"class_id" => CL_CRM_BILL,
			"CL_CRM_BILL.RELTYPE_ROW" => $this->id()
		));
		$ids = $bills_list->ids();
		return reset($ids);
	}

	/** returns bill row bill id
		@attrib api=1
		@returns oid
			bill id
	**/
	function get_bill_object()
	{
		$bills_list = new object_list(array(
			"class_id" => CL_CRM_BILL,
			"CL_CRM_BILL.RELTYPE_ROW" => $this->id()
		));
		return $bills_list->begin();
	}

	/** checks if bill has other customers...
		@attrib api=1
		@param customer type=oid
		@returns string/int
			error, if true, if not, then 0
	**/
	function check_if_has_other_customers($customer)
	{
		$bill = $this->get_bill();
		if(!is_oid($bill) || !is_oid($customer))
		{
			return 0;
		}
		$bill = obj($bill);
		if(!$bill->prop("customer"))
		{
			return 0;
		}
		if($customer != $bill->prop("customer"))
		{
			return "on teised kliendid...";
		}
		return 0;
	}

	/** connects bill row to a task row
		@attrib api=1
		@returns
			error string if unsuccessful
	**/
	function connect_task_row($row)
	{
		if(!is_oid($row))
		{
			return t("Pole piisavalt p&auml;dev klassi id");
		}
		$row_obj = obj($row);
		if(!is_oid($row_obj->prop("task")))
		{
			return t("Ridadel pole toimetust m&auml;&auml;ratud");
		}
		$tasko = obj($row_obj->prop("task"));
		$error = $this->check_if_has_other_customers($tasko->prop("project.orderer"));
		if($error)
		{
			return $error;
		}
		$this->connect(array("to"=> $row, "type" => "RELTYPE_TASK_ROW"));
		$this->connect(array("to"=> $row_obj->prop("task"), "type" => "RELTYPE_TASK"));
		$bill = $this->get_bill();
		if(is_oid($bill))
		{
			$billo = obj($bill);
			$billo->connect(array("to"=> $row_obj->prop("task"), "type" => "RELTYPE_TASK"));
		}
		$tasko->connect(array("to"=> $bill, "type" => "RELTYPE_BILL"));
		$row_obj->set_prop("bill_id" , $bill);
		$row_obj->save();
		return 0;
	}

	/** connects bill row to a bug comment
		@attrib api=1 params=pos
		@param id required type=oid
			bug comment object id
		@returns
	**/
	function connect_bug_comment($id)
	{
		if(!is_oid($id))
		{
			return t("Pole piisavalt p&auml;dev klassi id");
		}
		$obj = obj($id);
		$bug = obj($obj->parent());
		if($bug->class_id() != CL_BUG)
		{
			return t("Kommentaaril pole bugi");
		}
		$error = $this->check_if_has_other_customers($bug->prop("customer"));
		if($error)
		{
			return $error;
		}
		$this->connect(array("to"=> $bug->id(), "type" => "RELTYPE_BUG"));

		return 0;
		//tegelt ma ei teagi kas on yldse m6tet rida ka siduma hakata

	}

	/** returns bill row implementors
		@attrib api=1 params=pos
		@returns array
	**/
	function get_person_selection()
	{
		$arr = array();
		foreach($this->connections_from(array("type" => "RELTYPE_PEOPLE")) as $c)
		{
			$arr[$c->prop("to")] = $c->prop("to.name");
		}
		return $arr;
		//tegelt ma ei teagi kas on yldse m6tet rida ka siduma hakata

	}

	/** returns bill row product selection
		@attrib api=1
		@returns array
	**/
	public function get_prod_selection()
	{
		$prods = array();
		$bill = $this->get_bill();
		if(object_loader::can("" , $bill))
		{
			$bill_obj = obj($bill);
			$prods = $bill_obj->get_prod_selection();
		}

		if (!isset($prods[$this->prop("prod")]) && object_loader::can("", $this->prop("prod")))
		{
			$prodo = obj($this->prop("prod"));
			$prods[$this->prop("prod")] = $prodo->name();
		}
		return $prods;
	}

	/** returns bill projects
		@attrib api=1
		@returns array
	**/
	public function get_project_selection()
	{
		$projects = array();
		$bill = $this->get_bill();
		if(object_loader::can("", $bill))
		{
			$bill_obj = obj($bill);
			foreach($bill_obj->connections_from(array("type" => "RELTYPE_PROJECT")) as $c)
			{
				$projects[$c->prop("to")] = $c->prop("to.name");
			}
		}

		if($this->prop("project"))
		{
			$projects[$this->prop("project")] = get_name($this->prop("project"));
		}
		return $projects;
	}

	/** returns bill row unit selection
		@attrib api=1
		@returns array
	**/
	public function get_unit_selection()
	{
		$prods = array();
		$bill = $this->get_bill();
		if(object_loader::can("" , $bill))
		{
			$bill_obj = obj($bill);
			$prods = $bill_obj->get_unit_selection();
		}

		if (!isset($prods[$this->prop("unit")]) && object_loader::cancan("", $this->prop("unit")))
		{
			$prodo = obj($this->prop("unit"));
			$prods[$this->prop("unit")] = $prodo->name();
		}
		asort($prods);
		return $prods;
	}

	public function get_bill_currency_id()
	{
		$bill = $this->get_bill();
		$ret = null;
		if(object_loader::can("" , $bill))
		{
			$bill_obj = obj($bill);
			$ret = $bill_obj->get_bill_currency_id();
		}
		return $ret;
	}

	public function get_bill_date()
	{
		$bill = $this->get_bill();
		$date = null;
		if(object_loader::can("" , $bill))
		{
			$bill_obj = obj($bill);
			$date = $bill_obj->prop("bill_date");
		}
		return $date;
	}

	/** returns bill row tax rate
		@attrib api=1 params=pos
		@param advise optional type=boolean
			true: if tax not set, then returns possible value
		@returns
	**/
	public function get_row_tax($advise = false)
	{
		if($this->prop("tax"))
		{
			return $this->prop("tax");
		}

		if($this->prop("has_tax"))
		{
			if (!object_loader::can("view", $this->prop("prod.tax_rate")))
			{
				return 18;
			}
			else
			{
				return $this->prop("prod.tax_rate.tax_amt");
			}
		}

		if($advise)
		{
			if (object_loader::can("view", $this->prop("prod.tax_rate")))
			{
				return $this->prop("prod.tax_rate.tax_amt");
			}

			return $this->get_default_tax();

		}

		return 0;
	}

	private function set_crm_settings()
	{
		if(!$this->crm_settings)
		{
			$seti = get_instance(CL_CRM_SETTINGS);
			$this->crm_settings = $seti->get_current_settings();
		}
		if($this->crm_settings)
		{
			return 1;
		}
		return 0;
	}

	private function get_default_tax()
	{
		$res = 0;
		if($this->prop("date"))
		{
			$date = $this->prop("date");
		}
		else
		{
			$date = time();
		}
		$this->set_crm_settings();
		if($this->crm_settings)
		{
			foreach($this->crm_settings->connections_from(array("type" => "RELTYPE_TAX_RATE")) as $c)
			{
				$tax = $c->to();
				$res = $tax->prop("tax_amt");
				if($tax-> prop("act_from") <= $date && $tax-> prop("act_to") > $date)
				{
					return $res;
				}
			}
		}
		return $res;
	}

	public function is_writeoff()
	{
		return $this->prop("writeoff");
	}

	/**
		@attrib api=1 params=pos
		@param group type=CL_CRM_BILL_ROW_GROUP default=NULL
			If no group given, row will be removed from the group it's in
		@comment
		@returns void
		@errors
			throws awex_obj_type if $group isn't a row group
	**/
	public function set_group(object $group = null)
	{
		if ($group and !$group->is_a(crm_bill_row_group_obj::CLID))
		{
			throw new awex_obj_type("Invalid row group class" . $group->class_id());
		}

		// remove from old grp
		if ($this->meta("row_group_id"))
		{
			try
			{
				$old_group = new object($this->meta("row_group_id"));
				$old_group->remove_row($this->ref());
			}
			catch (Exception $e)
			{
			}

			$new_group_id = null;
		}

		// set new group
		if ($group)
		{
			$group->add_row($this->ref());
			$new_group_id = $group->id();
		}

		$this->set_meta("row_group_id", $new_group_id);
		$this->save();
	}

	public function save($check_state = false)
	{
		$invoice = $this->get_bill_object();
		$name = $this->comment();
		$name = substr($name, 0, 100) . (strlen($name) > 100 ? "..." : "");
		if ($invoice)
		{
			$name = sprintf(t("Arve nr. %s rida: %s"), $invoice->prop("bill_no"), $name);
		}

		$this->set_name($name);
		$r = parent::save($check_state);

		// update invoice
		if ($invoice)
		{
			$invoice->handle_row_save($this->ref());
		}

		return $r;
	}
}
