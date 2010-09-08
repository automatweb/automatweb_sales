<?php

////// DEPRECATED use class constants instead
define("BILL_SUM", 1);
define("BILL_SUM_WO_TAX", 2);
define("BILL_SUM_TAX", 3);
define("BILL_AMT", 4);
////// END DEPRECATED

class crm_bill_obj extends _int_object
{
	const BILL_SUM = 1;
	const BILL_SUM_WO_TAX = 2;
	const BILL_SUM_TAX = 3;
	const BILL_AMT = 4;

	const STATUS_DRAFT = 0;
	const STATUS_READY = 8;
	const STATUS_VERIFIED = 7;
	const STATUS_SENT = 1;
	const STATUS_PAID = 2;
	const STATUS_RECEIVED = 3;
	const STATUS_PARTIALLY_RECEIVED = 6;
	const STATUS_CREDIT = 4;
	const STATUS_CREDIT_MADE = 5;
	const STATUS_DISCARDED = -5;
	const STATUS_OFFER = 16;

	private static $status_names = array();

	/** Returns list of bill status names
	@attrib api=1 params=pos
	@param status type=int
		Status constant value to get name for, one of crm_bill_obj::STATUS_*
	@returns array
		Format option value => human readable name, if $status parameter set, array with one element returned and empty array when that status not found.
	**/
	public static function status_names($status = null)
	{
		if (empty(self::$status_names))
		{
			self::$status_names = array(
				self::STATUS_DRAFT => t("Koostamisel"),
				self::STATUS_READY => t("Koostatud"),
				self::STATUS_VERIFIED => t("Kinnitatud"),
				self::STATUS_SENT => t("Saadetud"),
				self::STATUS_PAID => t("Makstud"),
				self::STATUS_RECEIVED => t("Laekunud"),
				self::STATUS_PARTIALLY_RECEIVED => t("Osaliselt laekunud"),
				self::STATUS_CREDIT => t("Kreeditarve"),
				self::STATUS_CREDIT_MADE => t("Tehtud kreeditarve"),
				self::STATUS_DISCARDED => t("Maha kantud"),
				self::STATUS_OFFER => t("Pakkumus")
			);
		}

		if (isset($status))
		{
			if (isset(self::$status_names[$status]))
			{
				$status_names = array($status => self::$status_names[$status]);
			}
			else
			{
				$status_names = array();
			}
		}
		else
		{
			$status_names = self::$status_names;
		}

		return $status_names;
	}

	function set_prop($name,$value)
	{
		switch($name)
		{
			case "customer":
				if($value != $this->prop("customer"))
				{
					$this->set_prop("customer_name" , "");
				}
				break;
			case "bill_no":
				$this->set_name(t("Arve nr")." ".$value);
				break;
			case "state":
				if($value != $this->prop("state"))
				{
					$prev_state = self::status_names($this->prop("state"));
					$prev_state = reset($prev_state);
					$new_state = self::status_names($value);
					$new_state = reset($new_state);
					$_SESSION["bill_change_comments"][] = t("Staatus") .": {$prev_state} => {$new_state}";
				}
				break;
			case "bill_due_date":
				break;
			case "project":
				$old = $new = "";
				if (is_array($this->prop("project")))
				{
					foreach($this->prop("project") as $old_id)
					{
						$old.= " ".get_name($old_id);
					}
				}

				if (is_array($value))
				{
					foreach($value as $new_id)
					{
						$new.= " ".get_name($new_id);
					}
				}

				if(!is_array($this->prop($name)) || !is_array($value) ||  array_sum($value) != array_sum($this->prop($name)))
				{
					$_SESSION["bill_change_comments"][] = $GLOBALS["properties"][CL_CRM_BILL][$name]["caption"] ." : " .$old. " => " .$new;
				}
				break;

			default :
				if($value != $this->prop($name))
				{
					$_SESSION["bill_change_comments"][] = $GLOBALS["properties"][CL_CRM_BILL][$name]["caption"] ." : " .$this->prop($name). " => " .$value;
				}
				break;
		}
		parent::set_prop($name,$value);
	}

	function save($exclusive = false, $previous_state = null)
	{
		if(!is_oid($this->id()))
		{
			if(!$this->prop("bill_accounting_date"))
			{
				if($this->prop("bill_date"))
				{
					$this->set_prop("bill_accounting_date" , $this->prop("bill_date"));
				}
				else
				{
					$this->set_prop("bill_accounting_date" , time());
				}
			}

			unset($_SESSION["bill_change_comments"]);
		}
		$this->set_prop("sum", $this->_calc_sum());

		$rv = parent::save($exclusive, $previous_state);

		if(isset($_SESSION["bill_change_comments"]) && is_array($_SESSION["bill_change_comments"]))
		{
			$this->add_comment(join("<br>\n" , $_SESSION["bill_change_comments"]));
			unset($_SESSION["bill_change_comments"]);
		}
		return $rv;
	}

	private function _calc_sum()
	{
		$bill_inst = get_instance(CL_CRM_BILL);
		$rows = $this->get_bill_rows_data();
		$sum = 0;
		foreach($rows as $row)
		{
			$sum+= $row["sum"];
		}

		if ($this->prop("disc") > 0)
		{
			$sum -= $sum * ($this->prop("disc") / 100.0);
		}

		return $this->round_sum($sum);
	}

	private function round_sum($sum)
	{
		$u = get_instance(CL_USER);
		$co = $u->get_current_company();
		$co = obj($co);
		if(is_object($co) && $co->prop("round"))
		{
			$round = (double)$co->prop("round");
			$min_stuff = $sum/$round - ($sum/$round - (int)($sum/$round));
			$min_diff = $sum - $min_stuff*$round;
			$max_diff = ($sum - ($min_stuff + 1) * $round)*-1;
			if($max_diff > $min_diff) $sum = $min_stuff*$round;
			else $sum = ($min_stuff+1)*$round;
		}
		 return $sum;
	}

	function get_bill_print_popup_menu()
	{
		$bill_inst = get_instance(CL_CRM_BILL);
		$pop = get_instance("vcl/popup_menu");
		$pop->begin_menu("bill_".$this->id());
		$pop->add_item(Array(
			"text" => t("Prindi arve"),
			"link" => "#",
			"oncl" => "onClick='window.open(\"".$bill_inst->mk_my_orb("change", array("openprintdialog" => 1,"id" => $this->id(), "group" => "preview"), CL_CRM_BILL)."\",\"billprint\",\"width=100,height=100\");'"
		));
		$pop->add_item(Array(
			"text" => t("Prindi arve lisa"),
			"link" => "#",
			"oncl" => "onClick='window.open(\"".$bill_inst->mk_my_orb("change", array("openprintdialog" => 1,"id" => $this->id(), "group" => "preview_add"), CL_CRM_BILL)."\",\"billprintadd\",\"width=100,height=100\");'"
		));
		$pop->add_item(array(
			"text" => t("Prindi arve koos lisaga"),
			"link" => "#",
			"oncl" => "onClick='window.open(\"".$bill_inst->mk_my_orb("change", array("openprintdialog_b" => 1,"id" => $this->id(), "group" => "preview"), CL_CRM_BILL)."\",\"billprintadd\",\"width=100,height=100\");'"
		));
		return $pop->get_menu();
	}

	/** returns bill currency id
		@attrib api=1
		@returns oid
	**/
	function get_bill_currency_id()
	{
		if(!is_oid($this->id()))
		{
			return;
		}

		if($this->prop("currency"))
		{
			return $this->prop("currency");
		}
		$currency = null;
		if($this->prop("customer.currency"))
		{
			$currency = $this->prop("customer.currency");
		}
		if(!$currency && $cust = $this->get_bill_customer())
		{
			$customer = obj($cust);
			$currency =  $customer->prop("currency");
		}
		if(!$currency)
		{
			$co_stat_inst = get_instance("applications/crm/crm_company_stats_impl");
			$currency = $co_stat_inst->get_company_currency();
		}
		$this->set_prop("currency" , $currency);
		$this->save();
		return $currency;
	}

	/** returns bill currency name
		@attrib api=1
		@returns string
	**/
	function get_bill_currency_name()
	{
		if($currency = $this->get_bill_currency_id())
		{
			return get_name($currency);
		}
/*
		if($this->prop("customer.currency"))
		{
			$company_curr = $this->prop("customer.currency");
		}
		else
		{
			if($cust = $this->get_bill_customer())
			{
				$customer = obj($cust);
				$company_curr = $customer->prop("currency");
			}
			else
			{
				$co_stat_inst = get_instance("applications/crm/crm_company_stats_impl");
				$company_curr = $co_stat_inst->get_company_currency();
			}
		}
		if(is_oid($company_curr) && $this->can("view" , $company_curr))
		{
			$cu_o = obj($company_curr);
			return $cu_o->name();
		}*/
		return "EEK";
	}


	/**
		@attrib api=1 all_args=1
	@param payment optional type=oid
		payment id you want to ignore
	@returns string error
	@comment
		returns sum not paid for bill
	**/
	public function get_bill_needs_payment($arr = null)
	{

		$payment = empty($arr["payment"]) ? "" : $arr["payment"];
		$bill_sum = $this->get_bill_sum();
		$sum = 0;
		foreach($this->connections_from(array("type" => "RELTYPE_PAYMENT")) as $conn)
		{
			$p = $conn->to();//echo $p->id();
			if($payment && $payment == $p->id())
			{
				if(($bill_sum - $sum) > $p->prop("sum")) // kui arve summa - juba makstud summa on suurem kui antud laekumine , siis tagastaks selle sama laekumise summa, sest rohkem vtta ju pole
				{
					return $p->prop("sum");
				}
				break;
			}
			$sum = $sum + $p->get_free_sum($this->id());
		}
		if($bill_sum < $sum)
		{
			$sum = $bill_sum;
		}
		return $bill_sum - $sum;
	}

	/** Adds payment in the given amount to the bill
		@attrib api=1 params=pos

		@param sum optional type=double
			The sum the payment was for. defaults to the entire sum of the bill

		@param tm optional type=int
			Time for the payment. defaults to current time

		@returns
			oid of the payment object
	**/
	function add_payment($sum = 0, $tm = null)
	{
		if ($tm === null)
		{
			$tm = time();
		}
		if(!$sum)
		{
			$sum = $this->get_bill_sum(self::BILL_SUM) - $this->prop("partial_recieved");
		}
		$p = new object();
		$p-> set_parent($this->id());
		$p-> set_name($this->name() . " " . t("laekumine"));
		$p-> set_class_id(CL_CRM_BILL_PAYMENT);
		$p->set_prop("customer" , $this->prop("customer"));
		$p-> set_prop("date", $tm);
		$p->save();

		$p->add_bill(array(
			"sum" => $sum,
			"o" => $this,
		));
		return $p->id();
	}

	function get_bill_payments_data()
	{
		$data = array();
		foreach($this->connections_from(array("type" => "RELTYPE_PAYMENT")) as $conn)
		{
			$p = $conn->to();
			$data[$p->id()]["currency"] = $p->get_currency_name();
			$bill_sums = $p->meta("sum_for_bill");
			$data[$p->id()]["sum"] = isset($bill_sums[$this->id()]) ? $bill_sums[$this->id()] : 0;
			$data[$p->id()]["total_sum"] = $p->prop("sum");
			$data[$p->id()]["date"] = $p->prop("date");
		}

		return $data;
	}

	function get_payments_sum()
	{
		$sum = 0;
		foreach($this->connections_from(array("type" => "RELTYPE_PAYMENT")) as $conn)
		{
			$p = $conn->to();
			$data[$p->id()]["currency"] = $p->get_currency_name();
			$bill_sums = $p->meta("sum_for_bill");
			$sum = $sum + $bill_sums[$this->id()];
		}
		return $sum;
	}

	function get_last_payment_date()
	{
		$date = 0;
		foreach($this->connections_from(array("type" => "RELTYPE_PAYMENT")) as $conn)
		{
			$p = $conn->to();
			if($p->prop("date") > $date)
			{
				$date = $p->prop("date");
			}
		}
		return $date;
	}

	/** Returns bill customer id
		@attrib api=1
		@returns
			oid of the customer
	**/
	function get_bill_customer()
	{
		// New
		if(!is_oid($this->id()))
		{
			return "";
		}

		$id = "";

		if (is_oid($this->prop("customer")) && $GLOBALS["object_loader"]->cache->can("view", $this->prop("customer")))
		{
			return $this->prop("customer");
		}
		else
		{
			foreach($this->connections_from(array("type" => "RELTYPE_CUST")) as $conn)
			{
				$id = $conn->prop("to");
			}
		}

		if(!$id)
		{
			foreach($this->connections_from(array("type" => "RELTYPE_TASK")) as $conn)
			{
				$p = $conn->to();
				$id = $p->prop("customer");
			}
		}

		if($id)
		{
			$this->set_prop("customer" , $id);
			$this->save();
		}
		return $id;
	}

	/** Adds bug comments to bill
		@attrib api=1 params=pos
		@param bugcomments required type=array
			array(bug comment id, bug comment 2 id , ...)
		@returns
			bill oid
	**/
	public function add_bug_comments($bugcomments)
	{
		$data = array();
		foreach($bugcomments as $comment)
		{
			if(!$GLOBALS["object_loader"]->cache->can("view" , $comment))
			{
				continue;
			}
			$o = obj($comment);
			$data[$o->parent()][$o->parent()][] = $o;
//			$data[mktime(0,0,0,date("m",$o->created()),date("d",$o->created()),date("Y",$o->created()))][$o->parent()][] = $o;
		}

		ksort($data);
		foreach($data as $day => $day_array)
		{
			foreach($day_array as $bug => $bug_comments)
			{
				$b = obj($bug);
				if(!($error = $this->check_if_has_other_customers($b->prop("customer"))))
				{
					$this->add_bug_row($bug_comments);
				}
				else
				{
					print $error;
				}
			}
		}

		return $this->id();
	}

	/** Adds bug comments to bill... every comment to single row
		@attrib api=1 params=pos
		@param bugcomments required type=array
			array(bug comment id, bug comment 2 id , ...)
		@returns
			bill oid
	**/
	public function add_bug_comments_single_rows($bugcomments)
	{
		$data = array();
		foreach($bugcomments as $comment)
		{
			if(!$GLOBALS["object_loader"]->cache->can("view" , $comment))
			{
				continue;
			}
			$o = obj($comment);
			$data[$o->created()][$o->parent()][] = $o;
		}

		ksort($data);
		foreach($data as $day => $day_array)
		{
			foreach($day_array as $bug => $bug_comments)
			{
				$b = obj($bug);
				if(!$this->check_if_has_other_customers($b->prop("customer")))
				{
					$this->add_bug_row($bug_comments);
				}
			}
		}

		return $this->id();
	}

	private function add_bill_comment_data($data)
	{
		$_SESSION["bill_change_comments"][] = $data;
	}


	/** Adds bug comments to bill row
		@attrib api=1 params=pos
		@param bugcomments required type=array
			array(bug comment id, bug comment 2 id , ...)
		@returns
			bill row id
	**/
	public function add_bug_row($bugcomments)
	{
		$row = new object();
		$row->set_class_id(CL_CRM_BILL_ROW);
		$row->set_name(t("Arve rida"));
		$row->set_parent($this->id());
		$row->save();

		$this->add_bill_comment_data(t("lisati rida idga ".$row->id()));

		$people = array();
		$amt = $price = $date = "";
		$u = get_instance(CL_USER);

		foreach($bugcomments as $c)
		{
			$comment = obj($c);
			$person = $u->get_person_for_uid($comment->createdby());
			if(is_object($person))
			{
				$people[$person->id()] = $person->id();
			}
			$amt+= $comment->bill_hours();
			if($err = $this->connect_bug_comment($comment->id()) || $err2 = $row->connect_bug_comment($comment->id()))
			{
				arr($err);
				arr($err2);
			}
			$comment_date = $comment->prop("date");
			if(!($date > 0) || $date < $comment_date)
			{
				$date = $comment_date;
			}
			$row->connect(array(
				"to" => $comment->id(),
				"type" => "RELTYPE_TASK_ROW"
			));
		}

	//	$amt = ((int)(($amt * 4)+1)) / 4;//ymardab yles 0.25listeni

		$row->set_prop("amt", $amt);
		$row->set_prop("price", $this->convert_to_bill_currency($price));
		$row->set_prop("unit", t("tund"));
		$row->set_prop("people", $people);

//		$br->set_prop("has_tax", $row["has_tax"]); ?????????????

		if(is_object($comment))
		{
			if($comment->prop("parent.class_id") == CL_BUG)
			{
				$row->set_prop("price", $this->convert_to_bill_currency($comment->prop("parent.hr_price")));
			}
			foreach($comment->connections_from(array("type" => "RELTYPE_PROJECT")) as $c)
			{
				$bill = obj($row->parent());
				$bill->set_project($c->prop("to"));
				$row->set_comment($c->prop("to.name"));
			}
			$row->set_prop("date", date("d.m.Y", $date));
			$row->set_name($comment->prop("parent.name"));
		}
		else
		{
			$row->set_prop("date", date("d.m.Y", time()));
		}
		$row->save();
		$this->connect(array(
			"to" => $row->id(),
			"type" => "RELTYPE_ROW"
		));
		return $row->id();
	}

	/** checks if bill has other customers...
		@attrib api=1
		@param customer type=oid
		@returns string/int
			error, if true, if not, then 0
	**/
	public function check_if_has_other_customers($customer)
	{
		if(!is_oid($customer))
		{
			return 0;
		}
		if(!$this->prop("customer"))
		{
			return 0;
		}
		if($customer != $this->prop("customer"))
		{
			if($GLOBALS["object_loader"]->cache->can("view",$customer))
			{
				$new_name = get_name($customer);

			}
			if($GLOBALS["object_loader"]->cache->can("view",$this->prop("customer")))
			{
				$old_name = get_name($this->prop("customer"));
			}

			return "on teised kliendid...\n<br>Arvel oli ".$old_name." , taheti lisada ".$new_name;
		}
		return 0;
	}


	/** connects bill to a bug comment
		@attrib api=1
		@returns
			error string if unsuccessful
	**/
	public function connect_bug_comment($c)
	{
		if(!is_oid($c))
		{
			return t("Pole piisavalt p&auml;dev id");
		}
		$obj = obj($c);
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
		$bug->connect(array("to"=> $this->id(), "type" => "RELTYPE_BILL"));

		$obj ->set_prop("bill_id" , $this->id());
		$obj->save();
		return 0;
	}

	public function set_impl()
	{
		if(!$this->prop("impl"))
		{
			$u = get_instance(CL_USER);
			$this->set_prop("impl", $u->get_current_company());
			$this->save();
		}
	}

	/** sets project
		@attrib api=1 params=pos
		@param project required type=oid
			project object id
	**/
	public function set_project($project)
	{
		$this->connect(array(
			"to" => $project,
			"type" => "RELTYPE_PROJECT",
		));
		$_SESSION["bill_change_comments"][] = t("Projekt")." => " .get_name($project);
		$this->save();
	}

	/** sets customer
		@attrib api=1
		@param cust optional type=oid
			customer object id
		@param tasks optional type=array
			tasks or task rows of other expenses, array(id, id2, ..)
		@param bugs optional type=array
			bug comments , array(id, id2, ..)
		@returns string/int
			error, if true, if not, then 0
	**/
	public function set_customer($arr)
	{
		if ($GLOBALS["object_loader"]->cache->can("view" , $arr["cust"]))
		{
			$cust = obj();
			$this->set_prop("customer", $arr["cust"]);
		}
		elseif(is_array($arr["tasks"]) && sizeof(is_array($arr["tasks"])))
		{
			$c_r_t = $arr["tasks"];
			if (is_array($c_r_t))
			{
				$c_r_t = reset($c_r_t);
			}
			$c_r_t_o = obj($c_r_t);
			if (($c_r_t_o->class_id() == CL_TASK_ROW) || ($c_r_t_o->class_id() == CL_CRM_EXPENSE))
			{
				$t_conns = $c_r_t_o->connections_to(array("from.class_id" => CL_TASK));
				$t_conn = reset($t_conns);
				if ($t_conn)
				{
					$c_r_t_o = $t_conn->from();
				}
			}
			$this->set_prop("customer", $c_r_t_o->prop("customer"));
			if(!$c_r_t_o->prop("customer"))
			{
				$cust = $c_r_t_o->get_first_obj_by_reltype("RELTYPE_CUSTOMER");
				if(is_object($cust))
				{
					$this->set_prop("customer", $cust->id());
				}
			}
		}

		//kui eelmiseid ei olnud v6i nad ei m6junud
		if((!(is_array($arr["tasks"]) || $GLOBALS["object_loader"]->cache->can("view" , $arr["cust"])) || (!$GLOBALS["object_loader"]->cache->can("view" , $this->prop("customer")))) && is_array($arr["bugs"]) && sizeof($arr["bugs"]))
		{
			foreach($arr["bugs"] as $bugc)
			{
				$c = obj($bugc);
				if(($c->class_id() == CL_BUG_COMMENT || $c->class_id() == CL_TASK_ROW)&& $GLOBALS["object_loader"]->cache->can("view" , $c->prop("parent.customer")))
				{
					$this->set_prop("customer" , $c->prop("parent.customer"));
					break;
				}
			}
		}

		$this->save();
		return $this->prop("customer");
	}


	/** adds new row to bill
		@attrib api=1 params=pos
		@returns
	**/
	public function add_row($id = null)
	{
		if(is_oid($id))
		{
			$br = obj($id);
		}
		else
		{
			$br = obj();
			$br->set_class_id(CL_CRM_BILL_ROW);
			$br->set_prop("date", date("d.m.Y", time()));
			if($this->set_crm_settings() && $this->crm_settings->prop("bill_default_unit"))
			{
				$br->set_prop("unit" , $this->crm_settings->prop("bill_default_unit"));
			}
		}
		$br->set_parent($this->id());

		$br->save();
		$this->add_bill_comment_data(t("Lisati rida ID-ga"." ".$br->id()));
		$this->connect(array(
			"to" => $br->id(),
			"type" => "RELTYPE_ROW"
		));
		return $br;
	}

	/** adds rows to bill
		@attrib api=1 params=name
		@param objects optional type=array
			object ids (tasks, meetings, bugs, calls, task rows , bill rows etc.)
		@returns
	**/
	public function add_rows($arr)
	{
		$seti = get_instance(CL_CRM_SETTINGS);
		$co_inst = get_instance(CL_CRM_COMPANY);
		$sts = $seti->get_current_settings();
		define("DEFAULT_TAX", 0.18);
		$bug_rows = array();
		$task_rows = array();
		$tasks = array();
		foreach(safe_array($arr["objects"]) as $id)
		{
			$work = obj($id);
			switch($work->class_id())
			{
				case CL_CRM_BILL_ROW:
					$ex_bill = obj($work->parent());
					$ex_bill->disconnect(array(
						"from" => $work->id(),
					));
					$this->add_row($work->id());
					if(sizeof($work->task_rows()))
					{
						$br_task_rows = new object_list();
						$br_task_rows -> add($work->task_rows());

						foreach($br_task_rows->arr() as $br_task_row)
						{
							$br_task_row->set_prop("bill_id" , $br_task_row->parent());
							if(is_oid($br_task_row->prop("task")))
							{
								$br_task_object = obj($br_task_row->prop("task"));
								$br_task_object->connect(array(
									"to" => $br_task_row->parent(),
									"type" => "RELTYPE_BILL"
								));
							}
						}
					}
					break;
				case CL_BUG:
					$bug_row_ol = $work->get_billable_comments();
					foreach($bug_row_ol->ids() as $id)
					{
						$bug_rows[$id] = $id;
					}
					$tasks[] = $work->id();
					break;
				case CL_CRM_MEETING:
				case CL_CRM_CALL:
				case CL_TASK:
					if($work->prop("deal_price"))
					{
						$agreement = $this->get_agreement_price();
						if(!is_array($agreement))
						{
							$agreement = array();
						}
						$tax = DEFAULT_TAX;
						$deal_name = $work->name();
						$prod = "";
						if ($sts)
						{
							if(is_oid($sts->prop("bill_def_prod")) && $GLOBALS["object_loader"]->cache->can("view",$sts->prop("bill_def_prod")))
							{
								$prod_obj = obj($sts->prop("bill_def_prod"));
								$prod = $sts->prop("bill_def_prod");
								$deal_name = $prod_obj->comment();
								$tr = obj($prod_obj->prop("tax_rate"));
								if (time() >= $tr->prop("act_from") && time() < $tr->prop("act_to"))
								{
									$tax = $tr->prop("tax_amt")/100.0;
								}
							}
						}

						$price = $work->prop("deal_price");
						if($work->prop("deal_has_tax"))
						{
							$price = $price / (1 + $tax);
						}
						$price = $this->convert_to_bill_currency($price , $work->prop("currency"));
						$agreement[] = array(
							"unit" => $work->prop("deal_unit"),
							"price" => $price,
							"amt" => $work->prop("deal_amount"),
							"name" => $deal_name,
							"prod" => $prod,
							"comment" => $deal_name,
							"has_tax" => $work->prop("deal_has_tax"),
						);
						$this->set_meta("agreement_price" , $agreement);
						$this->save();
						$work->set_prop("send_bill" , 0);
						$work->save();
					//ridadele ikkagi arve kylge
						foreach($work->connections_from(array("type" => "RELTYPE_ROW")) as $c)
						{
							$row = $c->to();
							if (!$row->prop("bill_id") && $row->prop("on_bill"))
							{
								$row->set_prop("bill_id", $bill->id());
								$row->save();
							}
						}
						$work->set_billable_oe_bill_id($this->id());

						$tasks[] = $work->id();
					}
					break;
				case CL_TASK_ROW:
					if($work->prop("task.class_id") == CL_BUG)
					{
						$task_rows[$work->task_id()][$work->id()] = $work->id();
//						$bug_rows[] = $work->id();
					}
					else
					{
						$task_rows[$work->task_id()][$work->id()] = $work->id();
					}
					$tasks[] = $work->task_id();
					break;
				case CL_CRM_EXPENSE:
					$expense = $work;
					$filt_by_row = $expense->id();
					// get task from row
					$conns = $expense->connections_to(array("from.class_id" => CL_TASK,"type" => "RELTYPE_EXPENSE"));
					$c = reset($conns);
					if ($c)
					{
						$tasks[] =  $c->prop("from");
					}

					$br = $this->add_row();
					$br->set_prop("comment", $expense->name());
					$br->set_prop("amt", 1);
					$br->set_prop("people", $expense->prop("who"));
					$br->set_prop("is_oe", 1);
					$date = $expense->prop("date");
					$br->set_prop("date", date("d.m.Y", mktime(0,0,0, $date["month"], $date["day"], $date["year"])));
					// get default prod
					if ($sts)
					{
						$br->set_prop("prod", $sts->prop("bill_def_prod"));
					}

					$sum = $co_inst->convert_to_company_currency(array(
						"sum" => $expense->prop("cost"),
						"o" => $expense,
						"company_curr" => $this->prop("customer.currency"),
					));

					$br->set_prop("price", $sum);
					$br->save();

					$expense->set_prop("bill_id", $this->id());
					$expense->save();

					$br->connect(array(
						"to" => $expense->id(),
						"type" => "RELTYPE_EXPENSE"
					));
					break;
			}
		}

		foreach($tasks as $task)
		{
			$this->connect(array(
				"to" => $task,
				"reltype" => "RELTYPE_TASK"
			));
			$task_object = obj($task);
			$task_object->connect(array(
				"to"=> $this->id(),
				"type" => "RELTYPE_BILL"
			));
		}

		foreach($task_rows as $task => $rows)
		{
			$task_o = obj($task);
			foreach($rows as $row)
			{
				$row = obj($row);
				$row->set_prop("bill_id" , $this->id());
				$row->save();
				foreach($row->connections_from(array("type" => "RELTYPE_PROJECT")) as $c)
				{
					$this->set_project($c->prop("to"));
				}
				$br = $this->add_row();
				$br->set_comment($task_o->name());
				$br->set_prop("name", $row->prop("content"));
				$br->set_prop("amt", $row->prop("time_to_cust"));
//				$br->set_prop("prod", $row["prod"]);
				$br->set_prop("price", $this->convert_to_bill_currency($task_o->prop("hr_price") , $task_o->prop("currency")));
				$br->set_prop("unit", t("tund"));

				//see peaks 2kki seadistattav olema hoopis et kas paneb automaatselt?
				$br->set_prop("has_tax", 1);
				$br->set_prop("tax", $br->get_row_tax(1));


				$br->set_prop("date", date("d.m.Y", $row->prop("date")));
				$br->set_prop("people", $row->prop("impl"));
				// get default prod

				if ($sts)
				{
					$br->set_prop("prod", $sts->prop("bill_def_prod"));
				}
				$br->save();
				$br->connect(array(
					"to" => $task,
					"type" => "RELTYPE_TASK"
				));
				$br->connect(array(
					"to" => $row->id(),
					"type" => "RELTYPE_TASK_ROW"
				));
				$this->connect(array(
					"to" => $br->id(),
					"type" => "RELTYPE_ROW"
				));
			}
		}

		//koondatavad bugide read siia vaid
		if(sizeof($bug_rows))
		{
			foreach($bug_rows as $key => $id)
			{
				foreach($task_rows as $task_id => $row_ids)
				{
					if(is_array($row_ids) &&  in_array($id , $row_ids))
					{
						unset($bug_rows[$key]);
					}
				}
			}
			$this->add_bug_comments($bug_rows);
		}


//------ send bill vaja maha saada, kui k6ik on arvele l2inud
// 			if(!$task_rows_to_bill_count[$task]) $task_rows_to_bill_count[$task] = 0;
// 			$task_rows_to_bill_count[$task] ++;
// 			if($task_rows_to_bill_count[$task] == $_POST["count"][$task])
// 			{
// 				$task_o->set_prop("send_bill", 0);
// 				$task_o->save();
// 			}
		return $this->id();
	}

	private function convert_to_bill_currency($sum, $from=null)
	{
		if(!is_oid($from))
		{
			if(!$this->company_currency)
			{
				$u = get_instance(CL_USER);
				$co = obj($u->get_current_company());
				$this->company_currency = $co->prop("currency");
			}
		}
		$from = $this->company_currency;
		$bcurrency = $this->get_bill_currency_id();

		if($bcurrency && $from && $from != $bcurrency)
		{
			$curr_inst = get_instance(CL_CURRENCY);
			$price = $curr_inst->convert(array(
				"from" => $from,
				"to" => $bcurrency,
				"sum" => $sum,
				"date" =>  $this->prop("bill_date"),
			));
			return $price;
		}
		return $sum;
	}

	/** if the bill has an impl and customer, then check if they have a customer relation and if so, then get the due days from that
		@attrib api=1
	**/
	public function set_due_date()
	{
		if (is_oid($this->prop("customer")) && is_oid($this->prop("impl")))
		{
			$cust_rel_list = new object_list(array(
				"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
				"lang_id" => array(),
				"site_id" => array(),
				"buyer" => $this->prop("customer"),
				"seller" => $this->prop("impl")
			));
			if ($cust_rel_list->count())
			{
				$cust_rel = $cust_rel_list->begin();
				$this->set_prop("bill_due_date_days", $cust_rel->prop("bill_due_date_days"));
			}

			if(!$this->prop("bill_due_date_days"))
			{
				$this->set_prop("bill_due_date_days", $this->prop("customer.bill_due_days"));
			}

			$bt = time();
			$this->set_prop("bill_due_date",
				mktime(3,3,3, date("m", $bt), date("d", $bt) + $this->prop("bill_due_date_days"), date("Y", $bt))
			);
			$this->save();
		}
	}

	/** returns bill sum
		@attrib api=1
	**/
	public function get_sum()
	{
		$agreement = $this->get_agreement_price();
		if($agreement["sum"] && $agreement["price"] && strlen($agreement["name"]) > 0) return $agreement["sum"];
		if($agreement[0]["sum"] && $agreement[0]["price"] && strlen($agreement[0]["name"]) > 0)
		{
			$sum = 0;
			foreach($agreement as $a)
			{
				$sum+= $a["sum"];
			}
			return $sum;
		}
		return $this->prop("sum");
	}

	/** returns bill sum without other expenses
		@attrib api=1
	**/
	function get_sum_wo_exp()
	{
		$agreement = $this->meta("agreement_price");
		if($agreement["sum"] && $agreement["price"] && strlen($agreement["name"]) > 0) return $agreement["sum"];
		if($agreement[0]["sum"] && $agreement[0]["price"] && strlen($agreement[0]["name"]) > 0)
		{
			$sum = 0;
			foreach($agreement as $a)
			{
				$sum+= $a["sum"];
			}
			return $sum;
		}
		$rows = $this->get_bill_rows_data();
		$sum = 0;
		foreach($rows as $row)
		{
			if(!$row["is_oe"]) $sum+= $row["sum"];
		}
		return $sum;
	}

	/** returns task object list
		@attrib api=1
	**/
	public function bill_tasks()
	{
		$filter = array();
		$filter["class_id"] = CL_TASK;
		$filter["CL_TASK.RELTYPE_BILL"] = $this->id();
		$ol = new object_list($filter);
		foreach($this->connections_from(array("type" => "RELTYPE_TASK")) as $c)
		{
			$ol->add($c->prop("to"));
		}
		return $ol;
	}

	/** returns task rows data
		@attrib api=1
	**/
	public function bill_task_rows_data()
	{
		$rows_filter = $this->bill_task_rows_filter();
		$rowsres = array(
			CL_TASK_ROW => array(
				"task",
				"time_real",
				"impl",
				"time_to_cust",
				"content",
				"date"
			),
		);
		$rows_arr = new object_data_list($rows_filter , $rowsres);
		return $rows_arr->list_data;
	}

	/** returns task rows
		@attrib api=1
		@returns object list
	**/
	public function bill_task_rows()
	{
		$rows_filter = $this->bill_task_rows_filter();
		$ol = new object_list($rows_filter);
		return $ol;
	}

	private function bill_task_rows_filter()
	{
		$filter = array();
		$filter["class_id"] = CL_TASK_ROW;
		$filter["CL_TASK_ROW.RELTYPE_BILL"] = $this->id();
		return $filter;
	}

	/** returns bill rows
		@attrib api=1
		@returns object list
	**/
	public function get_bill_rows()
	{
		$ol = new object_list($this->get_bill_rows_filter());
		return $ol;
	}

	private function get_bill_rows_filter()
	{
		$filter = array();
		$filter["class_id"] = CL_CRM_BILL_ROW;
		$filter["site_id"] = array();
		$filter["lang_id"] = array();
		$filter["CL_CRM_BILL_ROW.RELTYPE_ROW(CL_CRM_BILL)"] = $this->id();
		$filter["writeoff"] = new obj_predicate_not(1);
		return $filter;
	}

	/** returns bill rows data using object data list
		@attrib api=1
	**/
	public function get_bill_rows_dat()
	{
		$filter = $this->get_bill_rows_filter();
		$rowsres = array(
			CL_CRM_BILL_ROW => array(
				"task_row","prod","price","amt","has_tax","tax"
			),
		);
		$rows_arr = new object_data_list($filter , $rowsres);
		return $rows_arr->list_data;
	}

	/** calculates bill writeoff rows sum without tax
		@attrib api=1
		@returns double
	**/
	public function get_writeoffs_sum()
	{
		$filter = array();
		$filter["class_id"] = CL_CRM_BILL_ROW;
		$filter["site_id"] = array();
		$filter["lang_id"] = array();
		$filter["CL_CRM_BILL_ROW.RELTYPE_ROW(CL_CRM_BILL)"] = $this->id();
		$filter["writeoff"] = 1;

		$rowsres = array(
			CL_CRM_BILL_ROW => array(
				"price","amt"
			),
		);
		$rows_arr = new object_data_list($filter , $rowsres);
		$sum = 0;
		foreach($rows_arr->list_data as $id => $data)
		{
			$sum+= $data["price"]*$data["amt"];
		}
		return $sum;
	}


	/** calculates bill sum
		@attrib api=1 params=pos
		@param type type=int
			BILL_SUM_TAX - bill tax sum , BILL_SUM_WO_TAX - bill sum without tax , BILL_AMT - bill hours
		@returns int
	**/
	public function get_bill_sum($type = self::BILL_SUM)
	{
		if(!is_oid($this->id()))
		{
			return 0;
		}

		$rs = "";
		$sum_wo_tax = $tot_amt = $tot_cur_sum = 0;
		$tax = 0;
		$sum = 0;

		$agreement_price = $this->get_agreement_price();
		if(is_array($agreement_price) && isset($agreement_price[0]["price"]) && strlen($agreement_price[0]["name"]) > 0)
		{
			$rows = $agreement_price;
		}
		elseif(is_array($agreement_price) && isset($agreement_price["price"]) && strlen($agreement_price["name"]) > 0)
		{
			$rows = array($agreement_price);
		}
		else
		{
			$rows = $this->get_bill_rows_dat();
		}

		foreach($rows as $row)
		{
			$cur_tax = 0;
			$cur_sum = 0;
			$cur_pr = 0;
			$row["sum"] = $row["price"] * $row["amt"];
			if ($GLOBALS["object_loader"]->cache->can("view", $row["prod"]))
			{
				$set = false;
				// get tax from prod
				$prod = obj($row["prod"]);
				if ($GLOBALS["object_loader"]->cache->can("view", $prod->prop("tax_rate")))
				{
					$tr = obj($prod->prop("tax_rate"));

					if (time() >= $tr->prop("act_from") && time() < $tr->prop("act_to"))
					{
						$cur_sum = $row["sum"];
						$cur_tax = ($row["sum"] * ($tr->prop("tax_amt")/100.0));
						$cur_pr = $row["price"];
						$set = true;
					}
				}

				if (!$set)
				{
					// no tax
					$cur_sum = $row["sum"];
					$cur_tax = 0;
					$cur_pr = $row["price"];
				}
			}
			elseif (!empty($row["tax"]))
			{
				// tax needs to be added
				$cur_tax = ($row["sum"] * ($row["tax"]/100.0));
				$cur_sum = $row["sum"];
				$cur_pr = $row["price"];
			}
			else
			{
				// tax does not need to be added, tax free it seems
				$cur_sum = $row["sum"];
				$cur_tax = 0;
				$cur_pr = $row["price"];
			}

			$sum_wo_tax += $cur_sum;
			$tax += $cur_tax;
			$sum += ($cur_tax+$cur_sum);
			$tot_amt += $row["amt"];
			$tot_cur_sum += $cur_sum;
		}

		switch($type)
		{
			case self::BILL_SUM_TAX:
				return $tax;

			case self::BILL_SUM_WO_TAX:
				return $sum_wo_tax;

			case self::BILL_AMT:
				return $tot_amt;
		}
		return $sum;
	}

	//selle asemel kasuta get_bill_rows_dat funktsiooni... iganenud on
	/** returns bill rows data
		@attrib api=1
	**/
	public function get_bill_rows_data()
	{
		$inf = array();

		$cons = $this->connections_from(array("type" => "RELTYPE_ROW"));
		foreach($cons as $c)
		{
			$row = $c->to();
			if($row->prop("writeoff"))
			{
				continue;
			}
			$kmk = "";
			if ($GLOBALS["object_loader"]->cache->can("view", $row->prop("prod")))
			{
				$prod = obj($row->prop("prod"));
				if ($GLOBALS["object_loader"]->cache->can("view", $prod->prop("tax_rate")))
				{
					$tr = obj($prod->prop("tax_rate"));
					$kmk = $tr->prop("code");
				}
			}

			$ppl = array();
			foreach((array)$row->prop("people") as $p_id)
			{
				if ($GLOBALS["object_loader"]->cache->can("view", $p_id))
				{
					$ppl[$p_id] = $p_id;
				}
			}

			$rd = array(
				"amt" => $row->prop("amt"),
				"prod" => $row->prop("prod"),
				"name" => $row->prop("desc"),
				"comment" => $row->prop("comment"),
				"price" => $row->prop("price"),
				"sum" => $row->prop("amt") * $row->prop("price"),
				"km_code" => $kmk,
				"unit" => $row->prop("unit"),
				"jrk" => $row->meta("jrk"),
				"id" => $row->id(),
				"is_oe" => $row->prop("is_oe"),
				"has_tax" => $row->prop("has_tax"),
				"tax" => $row->get_row_tax(),
				"date" => $row->prop("date"),
				"persons" => $ppl,
				"has_task_row" => $row->has_task_row(),
				"task_rows" => $row->task_rows(),
			);

			$inf[] = $rd;
		}
		usort($inf, array($this, "__br_sort"));
		return $inf;
	}

	private function __br_sort($a, $b)
	{
		$a_date = $a["date"];
		$b_date = $b["date"];
		list($a_d, $a_m, $a_y) = explode(".", $a_date);
		list($b_d, $b_m, $b_y) = explode(".", $b_date);
		$a_tm = mktime(0,0,0, $a_m, $a_d, $a_y);
		$b_tm = mktime(0,0,0, $b_m, $b_d, $b_y);
		if(!(($a["is_oe"] - $b["is_oe"]) == 0))
		{
			return $a["is_oe"]- $b["is_oe"];
		}
		return  $a["jrk"] < $b["jrk"] ? -1 :
			($a["jrk"] > $b["jrk"] ? 1:
				($a_tm >  $b_tm ? 1:
					($a_tm == $b_tm ? ($a["id"] > $b["id"] ? 1 : -1): -1)
				)
			);
	}

	/** returns bill project id's
		@attrib api=1
		@returns array
	**/
	public function get_project_ids()
	{
		$ret = array();
		foreach($this->connections_from(array("type" => "RELTYPE_PROJECT")) as $c)
		{
			$ret[] = $c->prop("to");
		}
		return $ret;
	}

	/** returns bill project leaders
		@attrib api=1
		@returns object list
	**/
	public function project_leaders()
	{
		$ol = new object_list();
		$ol->add($this->get_project_ids());
		$leaders = new object_list();
		foreach($ol->arr() as $o)
		{
			if(is_oid($o->prop("proj_mgr")))
			{
				$leaders->add($o->prop("proj_mgr"));
			}
		}
		return $leaders;
	}

	/** returns bill project leader names
		@attrib api=1
		@returns array
	**/
	public function project_leader_names()
	{
		$ret = array();
		$ol = new object_list();
		$ol->add($this->get_project_ids());
		foreach($ol->arr() as $o)
		{
			if(is_oid($o->prop("proj_mgr")))
			{
				$ret[$o->prop("proj_mgr")] = $o->prop("proj_mgr.name");
			}
		}
		return $ret;
	}


	/** disconnects tasks from bill and bill rows
		@attrib api=1 params=pos
		@param tasks required type=array
			object ids (tasks, meetings, bugs, calls, task rows etc.)
	**/
	public function remove_tasks($tasks)
	{
		$cons = $this->connections_from(array("type" => "RELTYPE_ROW"));
		foreach($cons as $c)
		{
			$row = $c->to();
			foreach($tasks as $task)
			{
				if($row->is_connected_to(array("to" => $task)))
				{
					$row->disconnect(array(
						"from" => $task,
					));
				}
			}
		}
		foreach($tasks as $task)
		{
			$o = obj($task);
			switch($o->class_id())
			{
				case CL_CRM_MEETING:
				case CL_CRM_CALL:
				case CL_TASK:
					$o->set_prop("bill_no" , "");
					$o->save();
					break;
				case CL_TASK_ROW:
					$o->set_prop("bill_id" , "");
					$o->save();
					break;
			}
			if($this->is_connected_to(array("to" => $o->id())))
			{
				$this->disconnect(array(
					"from" => $o->id(),
				));
			}
			if($o->is_connected_to(array("to" => $this->id())))
			{
				$o->disconnect(array(
					"from" => $this->id(),
				));
			}
		}
	}

	public function add_bills($list)
	{
		$tasks = new object_list();
		$task_rows = new object_list();
		$bill_rows = new object_list();
		$customer = array($this->get_bill_customer() => $this->get_bill_customer());
		foreach($list as $bill_id)
		{
			$bill = obj($bill_id);
			$bill_rows->add($bill->get_bill_rows());
			$tasks->add($bill->bill_tasks());
			$task_rows->add($bill->bill_task_rows());
			$customer[$bill->get_bill_customer()] = $bill->get_bill_customer();
		}
		if(!(sizeof($customer) > 1)) //kui erinevad kliendid, siis ignoreerib t2ielikult
		{
			foreach($bill_rows->arr() as $bill_row)
			{
				$this->connect(array(
					"to" => $bill_row->id(),
					"type" => "RELTYPE_ROW"
				));
				$bill_row->set_parent($this->id());
				$bill_row->save();
			}

			foreach($task_rows->arr() as $task_row)
			{
				$task_row->set_prop("bill_id" , $this->id());
				$task_row->save();
			}

			foreach($tasks->arr() as $task)
			{
				$task->connect(array(
					"to" => $this->id(),
					"type" => "RELTYPE_BILL"
				));
				$this->connect(array(
					"to" => $task->id(),
					"type" => "RELTYPE_TASK"
				));
			}

			$this->save();
			$delete_bills = new object_list();
			$delete_bills->add($list);
			$delete_bills->delete();

			return $this->id();
		}
		else
		{
			return null;
		}
	}

	public function get_comments_text()
	{
		get_instance("vcl/table");
		$t = new vcl_table();
		$t->define_field(array(
			"name" => "choose",
			"width" => 10,
//			"caption" => t("Kommentaar"),
		));
		$t->define_field(array(
			"name" => "time",
			"width" => 100,
//			"caption" => t("Kommentaar"),
		));
		$t->define_field(array(
			"name" => "user",
			"width" => 100,
//			"caption" => t("Kommentaar"),
		));
		$t->define_field(array(
			"name" => "text",
//			"caption" => t("Kulunud aeg"),
		));

		$ret = array();
		$ol = $this->get_comments();
		foreach($ol->arr() as $o)
		{
			$radio = html::radiobutton(array(
				"name" => "set_important_comment",
				"value" => $o->id(),
				"checked" => $this->meta("important_comment") ==  $o->id() ? 1 : 0,

				"onclick" => "
				el = document.getElementsByName(this.name);
				var x = 0;
				while(x < el.length)
				{
					if(el[x].value != this.value)
					{
						el[x].accessKey=0;
					}
					x++;
				}
				if(this.accessKey == 1)
				{
					this.checked=0;
					this.accessKey=0;
				}
				else
				{
					this.accessKey=1;
				}
				"
			));
			$t->define_data(array(
				"choose" => $radio,
				"user" => $o->prop("createdby"),
				"time" => date("d.m.Y H:i" , $o->created()),
				"text" =>  $o->comment(),
			));
		}
		return $t->draw();
		return join("<br>" , $ret);
	}

	public function get_comments()
	{
		$ol = new object_list(array(
			"class_id" => CL_CRM_COMMENT,
			"parent" => $this->id(),
			"site_id" => array(),
			"lang_id" => array(),
			"sort_by" => "objects.created desc",
		));
		return $ol;
	}

	public function add_comment($comment)
	{
		$o = new object();
		$o->set_class_id(CL_CRM_COMMENT);
		$o->set_parent($this->id());
		$o->set_name(t("Kommentaar objektile ").$this->name());
		$o->set_comment($comment);
		$o->save();
		return $o->id();
	}

/**
	@attrib api=1 params=pos
	@returns array
		Indexed by email address, values are in the format "person name <email address>"
	@errors
**/
	public function get_bcc()
	{
		$ret = array();
		$bill_targets = $this->meta("bill_targets");
		$ol = new object_list();

		if($this->project_leaders())
		{
			$ol->add($this->project_leaders());
		}

		foreach($ol->arr() as $mail_person)
		{
			if(!(is_array($bill_targets) && sizeof($bill_targets) && !$bill_targets[$mail_person->id()]))
			{
				$ret[$mail_person->get_mail($this->prop("customer"))] = $mail_person->name() . " <" . $mail_person->get_mail($this->prop("customer")) . ">";
			}
		}

		if($this->set_crm_settings() && $this->crm_settings->prop("bill_mail_to"))
		{
			$ret[$this->crm_settings->prop("bill_mail_to")] = $this->crm_settings->prop("bill_mail_to");
		}

		if (aw_global_get("uid_oid") != "")
		{
			$user_inst = new user();
			$u = obj(aw_global_get("uid_oid"));
			$person = obj($user_inst->get_current_person());
			$ret[$u->get_user_mail_address()] = $person->name() . " <" . $u->get_user_mail_address() . ">";
		}

		return $ret;
	}

	public function get_mail_targets()
	{
		$res = array();
/*
		if($this->set_crm_settings() && $this->crm_settings->prop("bill_mail_to"))
		{
			$res[$this->crm_settings->prop("bill_mail_to")] = $this->crm_settings->prop("bill_mail_to");
		}
*/
		$bill_targets = $this->meta("bill_targets");
		$bill_t_names = $this->meta("bill_t_names");
		if($this->prop("bill_mail_to"))
		{
			if(is_array($bill_t_names) && sizeof($bill_t_names) && $bill_t_names[0])
			{
				$res[$this->prop("bill_mail_to")] = $bill_t_names[0] . " <" . $this->prop("bill_mail_to") . ">";
			}
			else
			{
				$res[$this->prop("bill_mail_to")] = $this->prop("bill_mail_to");
			}
		}

		$ol = new object_list();
		if($this->get_customer_data("bill_person"))
		{
			$ol->add($this->get_customer_data("bill_person"));
		}
		foreach($ol->arr() as $mail_person)
		{
 			if(!(is_array($bill_targets) && sizeof($bill_targets) && !$bill_targets[$mail_person->id()]))
			{
				$name = $mail_person->name();
				if(is_array($bill_t_names) && sizeof($bill_t_names) && $bill_t_names[$mail_person->id()])
				{
					$name = $bill_t_names[$mail_person->id()];
				}
				$res[$mail_person->get_mail($this->prop("customer"))]  = $name . " <" . $mail_person->get_mail($this->prop("customer")) . ">";
			}
		}

		foreach($this->get_cust_mails() as $id => $mail)
		{
			if(!(is_array($bill_targets) && sizeof($bill_targets) && !$bill_targets[$id]))
			{
				$name = $this->get_customer_name();
				if(is_array($bill_t_names) && sizeof($bill_t_names) && $bill_t_names[$id])
				{
					$name = $bill_t_names[$id];
				}
				$res[$mail] = $name . " <" . $mail . ">";
			}
		}
		return $res;
	}

	public function get_mail_persons()
	{
		$ol = new object_list();
		foreach($this->connections_from(array("type" => "RELTYPE_RECIEVER")) as $c)
		{
			$ol->add($c->prop("to"));
		}
		if($this->get_customer_data("bill_person"))
		{
			$ol->add($this->get_customer_data("bill_person"));
		}
		if($this->project_leaders())
		{
			$ol->add($this->project_leaders());
		}
		return $ol;
	}

	public function get_cust_mails()
	{
		if(!is_oid($this->prop("customer")))
		{
			return array();
		}
		$cust = obj($this->prop("customer"));
		if($cust->class_id() == CL_CRM_PERSON)
		{
			$mails = $cust->emails();
		}
		else
		{
			$mails = $cust->get_mails(array());
		}
		$ret = array();
		foreach($mails->arr() as $mail)
		{
			if($mail->prop("mail"))
			{
				$default_mail = $mail;
				if($mail->prop("contact_type") == 1)
				{
					$ret[$mail->id()]= $mail->prop("mail");
				}
			}
		}
		if(!sizeof($ret) && is_object($default_mail))
		{
			$ret[$default_mail->id()]= $default_mail->prop("mail");
		}
		return $ret;
	}

	private function get_sender_signature()
	{
		$ret = array();
		$u = get_instance(CL_USER);
		$p = obj($u->get_current_person());
		$ret[]= $p->name();
		$names = $p->get_profession_names();
		$ret[]= reset($names);
		$names = $p->get_companies()->names();
		$ret[]= reset($names);
		$ret[]= $p->get_phone();
		$ret[]= $p->get_mail();
		return join("<br />\n" , $ret);
	}

	public function get_mail_from()
	{
		$ret = "";
		if($this->prop("bill_mail_from"))
		{
			$ret = $this->prop("bill_mail_from");
		}
		else
		{
			if($this->set_crm_settings() && $this->crm_settings->prop("bill_mail_from"))
			{
				$ret = $this->crm_settings->prop("bill_mail_from");
			}
		}

		if(!$ret)
		{
			$u = obj(aw_global_get("uid_oid"));
			$ret = $u->get_user_mail_address();
		}

		return $ret;
	}

	public function get_mail_from_name()
	{
		$ret = aw_global_get("uid");
		$u = get_instance(CL_USER);
		$p = obj($u->get_current_person());
		if(is_object($p))
		{
			$ret = $p->name();
		}
		if($this->prop("bill_mail_from_name"))
		{
			$ret = $this->prop("bill_mail_from_name");
		}
		else
		{
			if($this->set_crm_settings() && $this->crm_settings->prop("bill_mail_from_name"))
			{
				$ret = $this->crm_settings->prop("bill_mail_from_name");
			}
		}



		return $ret;
	}

	public function get_mail_subject()
	{
		$contact_person = $this->get_contact_person();
		$contact_person = trim($this->prop("ctp_text")) ? trim($this->prop("ctp_text")) : $contact_person->name();

		$replace = array(
			"#type#" => $this->prop("state") == self::STATUS_OFFER ? t("pakkumuse") : t("arve"),
			"#type2#" => $this->prop("state") == self::STATUS_OFFER ? t("Pakkumus") : t("Arve"),
			"#bill_no#" => $this->prop("bill_no"),
			"#customer_name#" => $this->get_customer_name(),
			"#contact_person#" => $contact_person,
			"#signature#" => $this->get_sender_signature(),
		);
		$subject = "";
		if($this->prop("bill_mail_subj"))
		{
			$subject = $this->prop("bill_mail_subj");
		}
		elseif($this->set_crm_settings() && $this->crm_settings->prop("bill_mail_subj"))
		{
			$subject = $this->crm_settings->prop("bill_mail_subj");
		}
		foreach($replace as $key => $val)
		{
			$subject = str_replace($key , $val , $subject);
		}
		return $subject;
	}

	public function get_customer_name()
	{
		if($this->prop("customer_name"))
		{
			return $this->prop("customer_name");
		}
		else
		{
			return $this->prop("customer.name");
		}
	}

	private function get_bill_target_name()
	{
		$ret = "";
//		if($this->prop("bill_rec_name"))
//		{
//			$ret = $this->prop("bill_rec_name");
//		}
//		else
//		{
			$ret = $this->get_customer_name();
//		}
		return $ret;
	}

	public function get_mail_body()
	{
		$contact_person = $this->get_contact_person();
		$contact_person = trim($this->prop("ctp_text")) ? trim($this->prop("ctp_text")) : $contact_person->name();

		$replace = array(
			"#type#" => $this->prop("state") == self::STATUS_OFFER ? t("pakkumuse") : t("arve"),
			"#type2#" => $this->prop("state") == self::STATUS_OFFER ? t("Pakkumus") : t("Arve"),
			"#bill_no#" => $this->prop("bill_no"),
			"#customer_name#" => $this->get_bill_target_name(),
			"#contact_person#" => $contact_person,
			"#signature#" => $this->get_sender_signature(),
		);

		$content = "";
		if($this->prop("bill_mail_ct"))
		{
			$content = $this->prop("bill_mail_ct");
		}
		elseif($this->set_crm_settings() && $this->crm_settings->prop("bill_mail_ct"))
		{
			$content = $this->crm_settings->prop("bill_mail_ct");
		}

		foreach($replace as $key => $val)
		{
			$content = str_replace($key , $val , $content);
		}

		return $content;
	}

	// Can't be private (nor protected). Called in crm_bill::_bill_targets()! -kaarel 21.07.2009
	public function set_crm_settings()
	{
		if(!isset($this->crm_settings) || !is_oid($this->crm_settings))
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

	/** returns sent mail objects
		@attrib api=1
		@returns object list
	**/
	public function get_sent_mails()
	{
		$ol = new object_list(array(
			"class_id" => CL_MESSAGE,
			"site_id" => array(),
			"lang_id" => array(),
			"parent" => $this->id(),
		));
		return $ol;
	}

	private function get_pdf_add()
	{
		$inst = get_instance(CL_CRM_BILL);
		return $inst->show_add(array(
			"id" => $this->id(),
			"pdf" => 1,
			"return" => 1,
		));
	}

	private function get_pdf()
	{
		$inst = get_instance(CL_CRM_BILL);
		return $inst->show(array(
			"id" => $this->id(),
			"pdf" => 1,
			"return" => 1,
		));
	}

	private function get_reminder_pdf()
	{
		$inst = get_instance(CL_CRM_BILL);
		return $inst->show(array(
			"id" => $this->id(),
			"pdf" => 1,
			"return" => 1,
			"reminder" => 1
		));
	}

	public function make_preview_pdf()
	{
		$f = new file();
		$id = $f->create_file_from_string(array(
			"parent" => $this->id(),
			"content" => $this->get_pdf(),
			"name" => ($this->prop("state") == self::STATUS_OFFER ? t("pakkumus_nr") : t("arve_nr")). "_".$this->prop("bill_no").".pdf",
			"type" => "application/pdf"
		));

		return obj($id);
	}

	public function make_reminder_pdf()
	{
                $f = get_instance(CL_FILE);
		$id = $f->create_file_from_string(array(
			"parent" => $this->id(),
			"content" => $this-> get_reminder_pdf(),
			"name" => t("Arve_nr"). "_".$this->prop("bill_no")."_".t("meeldetuletus").".pdf",
			"type" => "application/pdf"
		));

		return obj($id);
	}

	public function make_add_pdf()
	{
                $f = get_instance(CL_FILE);
		$id = $f->create_file_from_string(array(
			"parent" => $this->id(),
			"content" => $this->get_pdf_add(),
			"name" => ($this->prop("state") == self::STATUS_OFFER ? t("pakkumuse_nr") : t("arve_nr")). "_".$this->prop("bill_no")."_".t("aruanne").".pdf",
			"type" => "application/pdf"
		));
		return obj($id);
	}

	/** sends bill pdf to lots of people
		@attrib api=1
	**/
	public function send_bill($preview = null, $add = null)
	{
		$to = $this->get_mail_targets();
		$copies_to = $this->get_bcc();
		$subject = $this->get_mail_subject();
		$from = $this->get_mail_from();
		$from_name = $this->get_mail_from_name();
		$body = $this->get_mail_body();
		$att_comment = "";

		$mail = new object();
		$mail->set_class_id(CL_MESSAGE);
		$mail->set_parent($this->id());
		$mail->set_name(t("saadetud arve")." ".$this->prop("bill_no")." ".t("kliendile")." ".$this->get_customer_name());
		$mail->save();


		$awm = new aw_mail();
		$awm->create_message(array(
			"froma" => $from,
			"fromn" => $from_name,
			"subject" => $subject,
			"to" => implode("," , $to),
			"body" => $body,
			"bcc" => implode(",", $copies_to),
		));

		$mimeregistry = get_instance("core/aw_mime_types");

		//$to_o = $this->make_preview_pdf();
		$to_o = obj($preview);
		$to_o ->set_parent($mail->id());
		$to_o->save();
		$success_file1 = $awm->fattach(array(
			"path" => $to_o->prop("file"),
			"contenttype"=> $mimeregistry->type_for_file($to_o->name()),
			"name" => $to_o->name(),
		));
		$att_comment.= html::href(array(
			"caption" => html::img(array(
				"url" => aw_ini_get("baseurl")."/automatweb/images/icons/pdf_upload.gif",
				"border" => 0,
			)).$to_o->name(),
			"url" => $to_o->get_url(),
		));


		if($add)
		{
		//	$to_o = $this->make_add_pdf();
			$to_o = obj($add);
			$to_o ->set_parent($mail->id());
			$to_o->save();
			$success_file2 = $awm->fattach(array(
				"path" => $to_o->prop("file"),
				"contenttype"=> $mimeregistry->type_for_file($to_o->name()),
				"name" => $to_o->name(),
			));
			$att_comment.= html::href(array(
				"caption" => html::img(array(
					"url" => aw_ini_get("baseurl")."/automatweb/images/icons/pdf_upload.gif",
					"border" => 0,
				)).$to_o->name(),
				"url" => $to_o->get_url(),
			));
		}

		$awm->htmlbodyattach(array(
			"data" => $body
		));

		if (false !== $success_file1)
		{
			$success = $awm->gen_mail();
		}

		$info = "";
		if($success)
		{
			$info .= t("Arve saadetud aadressidele:")."<br />";
			$info .= htmlspecialchars(join (", " , $to));
			$info .= "<br /><br />" . t("koopia aadressidele:")."<br />";
			$info .= htmlspecialchars(join (", " , $copies_to));
			echo $info;
		}
		else
		{
			exit(t("Arve saatmine eba&otilde;nnestus."));
		}

		$att = array($preview);
		if($add)
		{
			$att[] = $add;
		}
		$mail->set_prop("attachments" , $att);
		$mail->set_prop("customer" , $this->prop("customer"));
		$mail->set_prop("message" , $body);
		$mail->set_prop("html_mail" , 1);
		$mail->set_prop("mfrom_name" , $from_name);
		$mail->set_prop("mto" , join (", " , $to));
		$mail->set_prop("bcc" , join (", " , $copies_to));

		$bill_targets = $this->meta("bill_targets");

		foreach($this->get_cust_mails() as $id => $mail_addr)
		{
			if(!(is_array($bill_targets) && sizeof($bill_targets) && !$bill_targets[$id]))
			{
				$mail->connect(array(
					"to" => $id,
					"type" => "RELTYPE_TO_MAIL_ADDRESS"
				));
			}
		}

		$ol = new object_list();
		if($this->project_leaders())
		{
			$ol->add($this->project_leaders());
		}

		if($ol->count())
		{
			foreach($ol->arr() as $o)
			{
				if(!(is_array($bill_targets) && sizeof($bill_targets) && !$bill_targets[$o->id()]))
				{
					if($id = $o->get_mail_id($this->prop("impl")))
					{
						$mail->connect(array(
							"to" => $id,
							"type" => "RELTYPE_TO_MAIL_ADDRESS"
						));
					}
				}
			}
		}

		$ol = new object_list();
		if($this->get_customer_data("bill_person"))
		{
			$ol->add($this->get_customer_data("bill_person"));
		}

		if($ol->count())
		{
			foreach($ol->arr() as $o)
			{
				if(!(is_array($bill_targets) && sizeof($bill_targets) && !$bill_targets[$o->id()]))
				{
					if($id = $o->get_mail_id())
					{
						$mail->connect(array(
							"to" => $id,
							"type" => "RELTYPE_TO_MAIL_ADDRESS"
						));
					}
				}
			}
		}

		if($from)
		{
			$ol = new object_list(array(
				"class_id" => CL_ML_MEMBER,
				"mail" => $from
			));
			if($ol->count())
			{
				$o = $ol->begin();
				$mail->set_prop("mfrom" , $o->id());
			}
		}
		$mto = array();
		if(sizeof($mto))
		{
			$mail->set_prop("mto_relpicker" , $mto);
		}
		$mail->save();

		$comment = sprintf(t("%s saatis arve nr %s; summa %s; kuup&auml;ev: %s; kellaaeg: %s; aadressidele: %s; tekst: %s; lisatud failid: %s. "), aw_global_get("uid"), $this->prop("bill_no") , $this->prop("sum") , date("d.m.Y") , date("H:i") , htmlspecialchars(join (", " , $to)), $body, $att_comment);
		$this->add_comment($comment);

		$state = (int) $this->prop("state");
		if ( false
			or self::STATUS_DRAFT === $state
			or self::STATUS_READY === $state
			or self::STATUS_VERIFIED === $state
			or self::STATUS_DISCARDED === $state
		)
		{
			$this->set_prop("state" , 1);
			$this->save();
		}
	}

	/** shows if bill has rows with no price or amount
		@attrib api=1
		@returns boolean
			true if has rows with no price or no amount
	**/
	public function has_not_initialized_rows()
	{
		$ol = new object_list(array(
			"class_id" => CL_CRM_BILL,
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_CRM_BILL.RELTYPE_ROW.amt" => 0,
					"CL_CRM_BILL.RELTYPE_ROW.price" => 0,
				)
			)),
			"oid" => $this->id(),
			"CL_CRM_BILL.RELTYPE_ROW.writeoff" => new obj_predicate_not(1),
		));
		return 0;//niikauaks kuni aru saab miks see filter alati 1 annab
		return $ol->count();
	}

	/**
		@attrib api=1
		@returns array
			array(task => array(bill_row_1 , bill_row_2))
	**/
	public function task_row_2_bill_rows()
	{
		$res = array();
		$data = $this->get_bill_rows_dat();
		foreach($data as $dat)
		{
			foreach($dat["task_row"] as $tr)
			{
				$res[$tr][$dat["oid"]] = $dat["oid"];
			}
		}
		return $res;
	}

	function get_writeoff_rows_data()
	{
		if(!is_oid($this->id()))
		{
			return;
		}
		$inf = array();
		$cons = $this->connections_from(array("type" => "RELTYPE_ROW"));
		foreach($cons as $c)
		{
			$row = $c->to();
			if(!$row->prop("writeoff"))
			{
				continue;
			}
			$kmk = "";
			if ($GLOBALS["object_loader"]->cache->can("view", $row->prop("prod")))
			{
				$prod = obj($row->prop("prod"));
				if ($GLOBALS["object_loader"]->cache->can("view", $prod->prop("tax_rate")))
				{
					$tr = obj($prod->prop("tax_rate"));
					$kmk = $tr->prop("code");
				}
			}

			$ppl = array();
			foreach((array)$row->prop("people") as $p_id)
			{
				if ($GLOBALS["object_loader"]->cache->can("view", $p_id))
				{
					$ppl[$p_id] = $p_id;
				}
			}
			$rd = array(
				"amt" => $row->prop("amt"),
				"prod" => $row->prop("prod"),
				"name" => $row->prop("desc"),
				"comment" => $row->prop("comment"),
				"price" => $row->prop("price"),
				"sum" => str_replace(",", ".", $row->prop("amt")) * str_replace(",", ".", $row->prop("price")),
				"km_code" => $kmk,
				"unit" => $row->prop("unit"),
				"jrk" => $row->meta("jrk"),
				"id" => $row->id(),
				"is_oe" => $row->prop("is_oe"),
				"has_tax" => $row->prop("has_tax"),
				"tax" => $row->get_row_tax(),
				"date" => $row->prop("date"),
				"id" => $row->id(),
				"persons" => $ppl,
				"has_task_row" => $row->has_task_row(),
			);
			$rd["orderer"] = $row->get_orderer_person_name();
			$rd["task_row_id"] = $row->get_task_row_or_bug_id();


			$inf[] = $rd;
		}
		usort($inf, array(&$this, "__br_sort"));
		return $inf;
	}

	public function get_payment_over_date()
	{
		$time = $this->prop("bill_recieved");
		if(!($time > 1))
		{
			$time = time();
		}
		$res = (int)(($time - $this->prop("bill_due_date")) / (3600*24));
		if($res > 0) return $res;
		return null;
	}

	private function set_implementor()
	{
		if(is_oid($this->prop("impl")))
		{
			$this->implementor_object = obj($this->prop("impl"));
		}
	}

	public function get_customer_data($prop)
	{
		$this->set_implementor();
		if($this->implementor_object)
		{
			$cust_rel_list = new object_list(array(
				"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
				"lang_id" => array(),
				"site_id" => array(),
				"buyer" => $this->prop("customer"),
				"seller" => $this->prop("impl")
			));
			if ($cust_rel_list->count())
			{
				$cust_rel = $cust_rel_list->begin();
				return $cust_rel->prop($prop);
			}
		}
		return null;
	}

	 /** returns bill customer phone
		@attrib api=1
		@returns string
	**/
	public function get_customer_phone()
	{
		$phone = $this->get_customer_data("phone");
		if($GLOBALS["object_loader"]->cache->can("view" , ($phone)))
		{
			return get_name($phone);

		}
		if($GLOBALS["object_loader"]->cache->can("view" , $this->prop("customer")))
		{
			$customer_object = obj($this->prop("customer"));
			return implode(", ", $customer_object->get_phones());
		}
	}

	public function get_quality_options()
	{
		$ret = array();
		$u = get_instance(CL_USER);
		$co = $u->get_current_company();
		if(is_oid($co))
		{
			$co = obj($co);
			$menu = $co->get_first_obj_by_reltype("RELTYPE_QUALITY_MENU");
			if(is_object($menu))
			{
				$ol = new object_list(array(
					"class_id" => CL_MENU,
					"parent" => $menu->id(),
				));
				return $ol->names();
			}

		}
		return $ret;
	}

	public function __rsorter($a, $b)
	{
		$a_date = $a->prop("date");
		$b_date = $b->prop("date");
		list($a_d, $a_m, $a_y) = explode(".", $a_date);
		list($b_d, $b_m, $b_y) = explode(".", $b_date);
		$a_tm = mktime(0,0,0, $a_m, $a_d, $a_y);
		$b_tm = mktime(0,0,0, $b_m, $b_d, $b_y);
		if(!(($a->prop("is_oe") - $b->prop("is_oe")) == 0))
		{
			return $a->prop("is_oe")- $b->prop("is_oe");
		}
		if ($a->comment() != $b->comment())
		{
			return strcmp($a->comment(), $b->comment());
		}
		return $a_tm > $b_tm ? 1:
			($a_tm == $b_tm ?
				($a->id() > $b->id() ? 1 : -1): -1);

	}

	public function reorder_rows()
	{
		$rows = $this->get_bill_rows();//arr($rows);
		$rows->sort_by_cb(array($this, "__rsorter"));
		$count = 0;//arr($rows);
		foreach($rows->arr() as $row)
		{
			$row->set_meta("jrk", $count*10);
			$row->save();
			$count++;
		}
	}

	/** returns bill product selection
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


	/** returns bill unit selection
		@attrib api=1
		@returns array
	**/
	public function get_unit_selection()
	{
		// get prords from co
		$filter = array(
			"class_id" => CL_UNIT,
			"lang_id" => array(),
			"site_id" => array(),
		);

		$t = new object_data_list(
			$filter,
			array(
				CL_UNIT => array(
					new obj_sql_func(OBJ_SQL_UNIQUE, "name", "objects.name"),
				)
			)
		);

		$names = $t->get_element_from_all("name");

		foreach($names as $id => $name)
		{
			if($name)
			{
				$prods[$this->get_unit_id($name)] = $name;
			}
		}
		return $prods;
	}

	private function get_unit_id($name)
	{
		$ol = new object_list(array(
			"class_id" => CL_UNIT,
			"lang_id" => array(),
			"site_id" => array(),
			"name" => $name,
		));
		$ids = $ol->ids();
		if(sizeof($ids))
		{
			return reset($ids);
		}
		else
		{
			return  null;
		}
	}

	/** makes new bill using this bill data
		@attrib api=1
		@param rows optional type=array
			row ids - cut/paste
		@returns oid
			new bill id
	**/
	public function form_new_bill($rows = array())
	{
		$nb = new object();
		$nb->set_parent($this->parent());
		$nb->set_class_id(CL_CRM_BILL);
		$nb->save();
		$save_props = array(
			"impl",
			"bill_date",
			"bill_due_date_days",
			"bill_due_date",
			"bill_recieved",
			"payment_mode",
			"state",
			"currency",
			"disc",
			"language",
			"on_demand",
			"mail_notify",
			"approved",
			"bill_trans_date",
			"signers",
			"customer_name",
			"customer",
			"customer_code",
			"customer_address",
			"ctp_text",
			"warehouse",
			"price_list",
			"transfer_method",
			"transfer_condition",
			"selling_order",
			"transfer_address",
			"show_oe_add",
			"project",
		);
		foreach($save_props as $prop)
		{
			$nb->set_prop($prop , $this->prop($prop));
		}
		$nb->save();
		$nb->add_rows(array("objects" => $rows));//arr($this->mk_my_orb("change", array("id" => $nb->id()), CL_CRM_BILL));
		return $nb->id();
	}

	/** returns bill agreement price
		@attrib api=1
		@returns array
	**/
	public function get_agreement_price()
	{
		if($this->use_agreement_price())
		{
			return $this->meta("agreement_price");
		}
		else
		{
			return null;
		}
	}

	public function use_agreement_price()
	{
		if(!$this->set_crm_settings() || !$this->crm_settings->prop("bill_no_agreement_price"))
		{
			return 1;
		}
		return 0;
	}

	function move_rows_to_dn($arr)
	{
		$o = obj();
		$o->set_class_id(CL_SHOP_DELIVERY_NOTE);
		foreach($arr["sel_rows"] as $tmp => $oid)
		{
			$row = obj($oid);
			if(!$row->meta("dno"))
			{
				$rows[$row->prop("prod")] = array(
					"amount" => $row->prop("amt"),
					"unit" => is_oid($row->prop("unit")) ? $row->prop("unit") : null,
					"price" => $row->prop("price"),
				);
			}
		}
		if($arr["dno"] == "new")
		{
			$dno = $o->create_dn(sprintf(t("%s saateleht #%s"), $this->name(), count($this->connections_from(array(
				"type" => "RELTYPE_DELIVERY_NOTE"
			))) + 1), $arr["id"], array(
				"from_warehouse" => $this->prop("warehouse"),
				"customer" => $this->prop("customer"),
				"impl" => $this->prop("impl"),
				"currency" => $this->prop("currency"),
				"rows" => $rows,
			));
			$this->connect(array(
				"to" => $dno->id(),
				"type" => "RELTYPE_DELIVERY_NOTE",
			));
			$dno->connect(array(
				"to" => $arr["id"],
				"type" => "RELTYPE_BILL",
			));
		}
		elseif($GLOBALS["object_loader"]->cache->can("view", $arr["dno"]))
		{
			$dno = obj($arr["dno"]);
			foreach($rows as $prod => $row)
			{
				$row["prod"] = $prod;
				$row["dno"] = $dno;
				$o->create_dn_row($row);
			}
		}
		foreach($arr["sel_rows"] as $tmp => $oid)
		{
			$row = obj($oid);
			$row->set_meta("dno", $dno->id());
			$row->save();
		}
	}

	function awobj_get_warehouse()
	{
		if($set = parent::prop("warehouse"))
		{
			return $set;
		}
		$impl = $this->prop("impl");
		if(is_oid($this->id()) && ($GLOBALS["object_loader"]->cache->can("view", $impl)))
		{
			$conn = obj($impl)->connections_to(array(
				"from.class_id" => CL_SHOP_WAREHOUSE_CONFIG,
				"type" => "RELTYPE_MANAGER_CO",
			));
			$ids = array();
			foreach($conn as $c)
			{
				$ids[] = $c->prop("from");
			}
			$ol = new object_list(array(
				"class_id" => CL_SHOP_WAREHOUSE,
				"conf" => $ids,
				"site_id" => array(),
				"lang_id" => array(),
			));
			if($ol->count() == 1)
			{
				return $ol->begin()->id();
			}
		}
		return null;
	}


	public function get_bill_cust_data_object()
	{
		if(isset($this->cust_data_object))
		{
			return $this->cust_data_object;
		}
		$cust_rel_list = new object_list(array(
			"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
			"lang_id" => array(),
			"site_id" => array(),
			"buyer" => $this->prop("customer"),
			"seller" => $this->prop("impl")
		));
		$this->cust_data_object = $cust_rel_list->begin();
		return $this->cust_data_object;
	}

	public function get_bill_recieved_money($payment=0)
	{
		$bill_sum = $this->get_bill_sum();
		$needed = $this->get_bill_needs_payment();
		if($payment)
		{
			$needed_wtp = $this->get_bill_needs_payment(array("payment" => $payment));
			$payment = obj($payment);
			$free_sum = $payment->get_free_sum($this->id());
			exit_function("bill::get_bill_recieved_money");
			return min($free_sum , $needed_wtp);
		}

		return $this->posValue($bill_sum - $needed);
	}

	private function posValue($nr)
	{
		if($nr < 0) return 0;
		else return $nr;
	}

	public function get_customer_address($prop = "")
	{
		if(!$this->prop("customer_name") || !$this->prop("customer_address"))
		{
			if($GLOBALS["object_loader"]->cache->can("view" , $this->prop("customer")))
			{
				$cust_obj = obj($this->prop("customer"));
				if($cust_obj->class_id() == CL_CRM_COMPANY)
				{
					$a = "contact";
				}
				else
				{
					$a = "address";
				}
			}
			else
			{
				return "";
			}
		}


		if(!$prop)
		{
			if($this->prop("customer_name"))
			{
				return $this->prop("customer_address");
			}
			else
			{
				return $cust_obj->prop($a.".name");
			}
		}

		if($this->prop("customer_name"))
		{
			$cust_addr = $this->meta("customer_addr");
			return $cust_addr[$prop];
		}
		else
		{
			switch($prop)
			{
				case "street":
					return $cust_obj->prop($a.".aadress");
				break;
				case "index":
					return $cust_obj->prop($a.".postiindeks");
				break;
				case "country":
					return $cust_obj->prop($a.".riik.name");
				break;
				case "county":
					return $cust_obj->prop($a.".maakond.name");
				break;
				case "city":
					return $cust_obj->prop($a.".linn.name");
				break;
				case "country_en":
					if($cust_obj->prop($a.".riik.name_en")) return $cust_obj->prop($a.".riik.name_en");
					else return $cust_obj->prop($a.".riik.name");
				break;
				return "";
			}
		}
	}

	function get_customer_code()
	{
		if($this->prop("customer_name"))
		{
			return $this->prop("customer_code");
		}
		else
		{
			return $this->prop("customer.code");
		}
	}

	public function get_payment_id()
	{
		foreach($this->connections_from(array("type" => "RELTYPE_PAYMENT")) as $conn)
		{
			return $conn->prop("to");
		}
		return null;
	}

	 /** returns bill id
		@attrib api=1 all_args=1
	@param no required type=int
		bill no.
	@returns int
		bill id
	**/
	public static function get_bill_id($arr)
	{
		$bills = new object_list(array(
			"class_id" => CL_CRM_BILL,
			"lang_id" => array(),
			"bill_no" => $arr["no"],
		));
		if(sizeof($bills->count()))
		{
			$ids = $bills->ids();
			return reset($ids);
		}
	}

	 /** returns bill overdue charge
		@attrib api=1
	@returns double
		penalty %
	**/
	public function get_overdue_charge()
	{
		$bpct = $this->prop("overdue_charge");
		if (!$bpct)
		{
			$cust_data = $this->get_bill_cust_data_object();
			if(is_object($cust_data) && $cust_data->prop("bill_penalty_pct"))
			{
				return $cust_data->prop("bill_penalty_pct");
			}
			$bpct = $this->prop("customer.bill_penalty_pct");
			if (!$bpct)
			{
				$bpct = $this->prop("impl.bill_penalty_pct");
			}
		}
		return $bpct;
	}

	 /** makes overdue bill
		@attrib api=1
		@returns oid
			new bill id
	**/
	public function make_overdue_bill()
	{
		if($this->prop("state") != self::STATUS_RECEIVED)
		{
			$_SESSION["bill_error"] = t("Viivisarvet koostatakse ainult laekunud arvete kohta");
			return null;
		}

		if(!($this->get_overdue_charge() > 0))
		{
			$_SESSION["bill_error"] = t("Viivise m&auml;&auml;r peab olema > 0");
			return null;
		}

		$nb = new object();
		$nb->set_parent($this->parent());
		$nb->set_class_id(CL_CRM_BILL);
		$nb->save();
		$save_props = array(
			"impl",
			"bill_due_date_days",
			"currency",
			"disc",
			"language",
			"on_demand",
			"mail_notify",
			"customer_name",
			"customer",
			"customer_code",
			"customer_address",
			"ctp_text",
			"warehouse",
			"price_list",
			"transfer_method",
			"transfer_condition",
			"transfer_address",
			"project",
		);
		foreach($save_props as $prop)
		{
			$nb->set_prop($prop , $this->prop($prop));
		}

		$nb->set_prop("is_overdue_bill" , 1);
		$nb->set_prop("bill_date" , time());
		$nb->set_prop("bill_accounting_date" , time());

		$nb->set_prop("state" , self::STATUS_DRAFT);
		$nb->set_name(t("Viivsarve arvele")." ".$this->prop("bill_no"));
		$nb->save();
		$row = $nb->add_row();
		if($this->set_crm_settings() && $this->crm_settings->prop("bill_default_duedate_unit"))
		{
			$row->set_prop("unit" , $this->crm_settings->prop("bill_default_duedate_unit"));
		}
		$days = ($this->prop("bill_recieved") - $this->prop("bill_date") - 3600*24*$this->prop("bill_due_date_days")) / (3600*24);
		$sum = number_format(((double)$this->get_sum() * (double)$this->get_overdue_charge())/100 , 2);
		$row->set_prop("amt", $days);
		$row-> set_prop("price" , $sum);
		$row->save();

		return $nb->id();
	}

	 /** returns text added to bill
		@attrib api=1
		@returns string
	**/
	public function get_bill_text()
	{
		if($this->prop("bill_text"))
		{
			return $this->prop("bill_text");
		}
		if($this->set_crm_settings() && $this->crm_settings->prop("bill_text"))
		{
			return  $this->crm_settings->prop("bill_text");
		}
	}

	 /** returns unit name
		@attrib api=1 params=pos
		@returns string
	**/
	public function get_unit_name($unit)
	{
		if($GLOBALS["object_loader"]->cache->can("view", $unit))
		{
			$uo = obj($unit);
			$u_trans = $uo->meta("translations");
			if($this->can("view", $this->prop("language")))
			{
				$unit_name = $u_trans[obj($this->prop("language"))->prop("db_lang_id")]["unit_code"];
			}
			if(!$unit_name)
			{
				$unit_name = $uo->prop("unit_code");
			}
		}
		else
		{
			$unit_name = $unit;
		}
		return $unit_name;
	}

	//igast info siia hiljem
	public function get_data()
	{
		$data = array();
		$project_manager_names = $this->project_leader_names();
		$data["impl_rep"] = reset($project_manager_names);
		return $data;

	}

	 /** returns person object who made this
		@attrib api=1
		@returns object
	**/
	public function get_the_person_who_made_this_fucking_thing()
	{
		if(is_oid($this->prop("assembler")))
		{
			try
			{
				return obj($this->prop("assembler"));
			}
			catch (awex_obj $e)
			{
			}
		}
		$creator_user = $this->createdby();
		$user_instance = new user();
		$person = $user_instance->get_person_for_uid($creator_user);
		return $person;
	}

	 /** returns bill customer contact person object
		@attrib api=1
		@returns object
	**/
	public function get_contact_person()
	{
		$contact_person = null;
		if($GLOBALS["object_loader"]->cache->can("view" , $this->prop("customer")))
		{
			$ord = obj($this->prop("customer"));
			try
			{
				$contact_person = obj($ord->prop("firmajuht"), array(), CL_CRM_PERSON);
			}
			catch (awex_obj $e)
			{
			}

			if (!$contact_person or !$contact_person->is_saved())
			{
				try
				{
					$contact_person = obj($ord->prop("contact_person"), array(), CL_CRM_PERSON);
				}
				catch (awex_obj $e)
				{
				}
			}

			if ($contact_person and !$contact_person->is_saved())
			{
				$contact_person = null;
			}
		}
		return $contact_person;
	}

	public function get_my_hours()
	{
		$c = 0;
		$p = get_current_person();
		if(is_object($p))
		{
			$data = $this->bill_task_rows_data();
			foreach($data as $d)
			{
				if(in_array($p->id() , $d["impl"])) $c+=$d["time_real"];
			}
		//	var_dump($data);
		}
		return $c;
	}
}

?>
