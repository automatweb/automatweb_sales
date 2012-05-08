<?php

////// DEPRECATED use class constants instead
define("BILL_SUM", 1);
define("BILL_SUM_WO_TAX", 2);
define("BILL_SUM_TAX", 3);
define("BILL_AMT", 4);
////// END DEPRECATED

//TODO: vastata kysimusele, kas customer property v6ib olla t2idetud ilma customer_relation-ita

class crm_bill_obj extends _int_object
{
	const CLID = 1009;

	const BILL_SUM = 1;
	const BILL_SUM_WO_TAX = 2;
	const BILL_SUM_TAX = 3;
	const BILL_AMT = 4;
	const BILL_SUM_WO_DISCOUNT = 5;
	const BILL_SUM_TAX_WO_DISCOUNT = 6;
	const BILL_SUM_WO_TAX_WO_DISCOUNT = 7;

	const STATUS_DRAFT = 0;
	const STATUS_READY = 8;
	const STATUS_VERIFIED = 7;
	const STATUS_SENT = 1;
	const STATUS_DELIVERED = 32;
	const STATUS_PAID = 2;
	const STATUS_RECEIVED = 3;
	const STATUS_PARTIALLY_RECEIVED = 6;
	const STATUS_CREDIT = 4;
	const STATUS_CREDIT_MADE = 5;
	const STATUS_DISCARDED = -5;//TODO: milleks see peab negatiivne olema
	const STATUS_OFFER = 16;

	const DELIVERY_EMAIL = 2;
	const DELIVERY_MAIL = 4;
	const DELIVERY_WEB = 8;

	const ROW_ORDER_INCREMENT = 100;

	private static $_states_disabling_accounting_data_edit = array(
		self::STATUS_SENT,
		self::STATUS_DELIVERED,
		self::STATUS_PAID,
		self::STATUS_RECEIVED,
		self::STATUS_PARTIALLY_RECEIVED
	);

	private static $status_names = array();
	private static $delivery_method_names = array();
	private $implementor_object = false;
	private $cust_data_object = null;
	private $clear_pdf_cache = true; // set false to instruct save() not to clear pdf files cache for one subsequent call
	private $_accounting_data_disabled = false;

	public static $accounting_data_properties = array(
		"bill_no",
		"bill_due_date_days",
		"sum",
		"bill_trans_date",
		"overdue_charge",
		"disc",
		"customer",
		"customer_relation",
		"currency",
		"bill_date",
		"bill_accounting_date"
	);

	public static $customer_address_properties = array(
		"street" => "street",
		"index" => "index",
		"city" => "city",
		"county" => "county",
		"country" => "country",
		"country_en" => "country_en"
	);

	public function __construct($objdata = array())
	{
		$r = parent::__construct($objdata);
		if (in_array($this->prop("state"), self::$_states_disabling_accounting_data_edit))
		{
			$this->_accounting_data_disabled = true;
		}
		return $r;
	}

	/** Tells if invoice state allows changing properties that are used in corporate accounting
		If invoice has been sent to customer then no changes may be made locally
		@attrib api=1 params=pos
		@returns bool
		@errors none
	**/
	public function can_edit_accounting_data()
	{
		return !$this->_accounting_data_disabled;
	}

	/** Returns list of delivery method values with names
	@attrib api=1 params=pos
	@param value type=int
		Delivery method constant value to get name for, one of crm_bill_obj::DELIVERY_*
	@returns array|string
		Format option value => human readable name.
		If $value parameter set, corresponding method name string returned and empty string when that delivery method not found.
	**/
	public static function delivery_method_names($value = null)
	{
		if (empty(self::$delivery_method_names))
		{
			self::$delivery_method_names = array(
				self::DELIVERY_EMAIL => t("E-post"),
				self::DELIVERY_MAIL => t("Post"),
				self::DELIVERY_WEB => t("Serverip&auml;ring")
			);
		}

		if (isset($value))
		{
			if (isset(self::$delivery_method_names[$value]))
			{
				$names = self::$delivery_method_names[$value];
			}
			else
			{
				$names = "";
			}
		}
		else
		{
			$names = self::$delivery_method_names;
		}

		return $names;
	}

	/** Returns list of bill status names
	@attrib api=1 params=pos
	@param value type=int
		Status constant value to get name for, one of crm_bill_obj::STATUS_*
	@returns array|string
		Format option value => human readable name.
		If $value parameter set, corresponding status name string returned and empty string when that status not found.
	**/
	public static function status_names($value = null)
	{
		if (empty(self::$status_names))
		{
			self::$status_names = array(
				self::STATUS_DRAFT => t("Koostamisel"),
				self::STATUS_READY => t("Koostatud"),
				self::STATUS_VERIFIED => t("Kinnitatud"),
				self::STATUS_SENT => t("Saadetud"),
				self::STATUS_DELIVERED => t("K&auml;ttetoimetatud"),
				self::STATUS_PAID => t("Makstud"),
				self::STATUS_RECEIVED => t("Laekunud"),
				self::STATUS_PARTIALLY_RECEIVED => t("Osaliselt laekunud"),
				self::STATUS_CREDIT => t("Kreeditarve"),
				self::STATUS_CREDIT_MADE => t("Tehtud kreeditarve"),
				self::STATUS_DISCARDED => t("Maha kantud"),
				self::STATUS_OFFER => t("Pakkumus")
			);
		}

		if (isset($value))
		{
			if (isset(self::$status_names[$value]))
			{
				$status_names = self::$status_names[$value];
			}
			else
			{
				$status_names = "";
			}
		}
		else
		{
			$status_names = self::$status_names;
		}

		return $status_names;
	}

	public function handle_row_save(object $row)
	{
		$this->save();
	}

	public function set_prop($name,$value)
	{
		if (in_array($name, self::$accounting_data_properties) and $this->_accounting_data_disabled)
		{
			throw new awex_crm_bill_state("Can't change accounting data for an invoice already sent");
		}

		switch($name)
		{
			case "bill_no":
				$this->set_name(t("Arve nr")." ".$value);
				break;

			case "state":
				if($value != $this->prop("state"))
				{
					$prev_state = self::status_names($this->prop("state"));
					$new_state = self::status_names($value);
					$_SESSION["bill_change_comments"][] = t("Staatus") .": {$prev_state} => {$new_state}";
				}
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
					$_SESSION["bill_change_comments"][] = (empty($GLOBALS["properties"][CL_CRM_BILL][$name]["caption"]) ? $name : $GLOBALS["properties"][CL_CRM_BILL][$name]["caption"]) ." : " .$this->prop($name). " => " .$value;
				}
				break;
		}
		parent::set_prop($name,$value);
	}

	public function awobj_set_customer_relation($cr_oid)
	{
		$this->set_prop("customer_relation", $cr_oid);
		if (object_loader::can("", $cr_oid))
		{
			$cro = obj($cr_oid, array(), crm_company_customer_data_obj::CLID);
			$this->load_customer_data();
		}
	}

	public function save($check_state = false)
	{
		if(!$this->is_saved())
		{ // set defaults
			// set bill dates to current if not specified
			$time = time();
			if(!$this->prop("bill_date"))
			{
				$this->set_prop("bill_date" , $time);
			}

			if(!$this->prop("bill_accounting_date"))
			{
				$this->set_prop("bill_accounting_date" , $time);
			}

			unset($_SESSION["bill_change_comments"]);
		}

		if ($this->can_edit_accounting_data())
		{
			// update due date according to bill date
			$bt = $this->prop("bill_accounting_date");
			if ($bt)
			{
				$this->set_prop("bill_due_date",
					mktime(0, 0, 1, date("m", $bt), date("d", $bt) + $this->prop("bill_due_date_days"), date("Y", $bt))
				);
			}

			// update sum
			$this->set_prop("sum", $this->_calc_sum());
		}

		// delete temporary pdf files
		if ($this->clear_pdf_cache)
		{
			$this->_clear_pdf_cache();
		}
		else
		{
			$this->clear_pdf_cache = true;
		}

		$rv = parent::save($check_state); //XXX: miks siin vaja awdisableacl ?(taketis nii tehtud)

		///FIXME: doesn't belong here (voldemar 12 nov 2010)
		if(isset($_SESSION["bill_change_comments"]) && is_array($_SESSION["bill_change_comments"]))
		{
			$this->add_comment(join(html::linebreak(), $_SESSION["bill_change_comments"]));
			unset($_SESSION["bill_change_comments"]);
		}
		///

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
		$bill_inst = get_instance(CL_CRM_BILL);//FIXME: ...
		$pop = new popup_menu();
		$pop->begin_menu("bill_".$this->id());
		$pop->add_item(Array(
			"text" => t("Prindi arve"),
			"link" => "#",
			"onclick" => "window.open(\"".$bill_inst->mk_my_orb("change", array("openprintdialog" => 1,"id" => $this->id(), "group" => "preview"), CL_CRM_BILL)."\",\"billprint\",\"width=100,height=100\");"
		));
		$pop->add_item(Array(
			"text" => t("Prindi arve lisa"),
			"link" => "#",
			"onclick" => "window.open(\"".$bill_inst->mk_my_orb("change", array("openprintdialog" => 1,"id" => $this->id(), "group" => "preview_add"), CL_CRM_BILL)."\",\"billprintadd\",\"width=100,height=100\");"
		));
		$pop->add_item(array(
			"text" => t("Prindi arve koos lisaga"),
			"link" => "#",
			"onclick" => "window.open(\"".$bill_inst->mk_my_orb("change", array("openprintdialog_b" => 1,"id" => $this->id(), "group" => "preview"), CL_CRM_BILL)."\",\"billprintadd\",\"width=100,height=100\");"
		));
		return $pop->get_menu();
	}

	/** returns bill currency id
		@attrib api=1 obj_save=1
		@returns oid
	**/
	public function get_bill_currency_id()
	{
		if(!is_oid($this->id()))
		{
			return;
		}

		if(object_loader::can("", $this->prop("currency")))
		{
			return $this->prop("currency");
		}

		$currency = null;
		if(object_loader::can("", $this->prop("customer.currency")))
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
			$co_stat_inst = new crm_company_stats_impl();
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
		else
		{
			return "EUR";
			//TODO: http://www.currency-iso.org/dl_iso_table_a1.xml
		}
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
			$p = $conn->to();
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
		$p->set_parent($this->id());
		$p->set_name($this->name() . " " . t("laekumine"));
		$p->set_class_id(CL_CRM_BILL_PAYMENT);
		$p->set_prop("customer" , $this->prop("customer"));
		$p->set_prop("date", $tm);
		$p->save();

		$p->add_bill(array(
			"sum" => $sum,
			"o" => $this->ref(),
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
		@attrib api=1 obj_save=1
		@returns int
			oid of the customer
	**/
	function get_bill_customer()
	{
		return object_loader::can("", $this->prop("customer_relation.buyer")) ? $this->prop("customer_relation.buyer") : 0;
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
			if(!object_loader::can("view" , $comment))
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
			if(!object_loader::can("view" , $comment))
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
		$row->set_class_id(crm_bill_row_obj::CLID);
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

//XXX: Mida see asi teeb ja milleks on???
	// checks if bill has other customers...
		// @attrib api=1
		// @param customer type=oid
		// @returns string/int
			// error, if true, if not, then 0
	public function check_if_has_other_customers($customer)
	{
		automatweb::http_exit(http::STATUS_NOT_IMPLEMENTED);
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
			if(object_loader::can("view",$customer))
			{
				$new_name = get_name($customer);

			}
			if(object_loader::can("view",$this->prop("customer")))
			{
				$old_name = get_name($this->prop("customer"));
			}

			return "on teised kliendid...\n<br>Arvel oli ".$old_name." , taheti lisada ".$new_name;
		}
		return 0;
	}


	// /** connects bill to a bug comment
		// @attrib api=1
		// @returns
			// error string if unsuccessful
	// **/
	public function connect_bug_comment($c)
	{
		automatweb::http_exit(http::STATUS_NOT_IMPLEMENTED);
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

	/** Retrieves and sets implementor from environment
		@attrib api=1 params=pos obj_save=1
		@returns void
	**/
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
		@attrib api=1 params=pos obj_save=1
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

	// /** sets customer
		// @attrib api=1 obj_save=1
		// @param cust optional type=oid
			// customer object id
		// @param tasks optional type=array
			// tasks or task rows of other expenses, array(id, id2, ..)
		// @param bugs optional type=array
			// bug comments , array(id, id2, ..)
		// @returns string/int
			// error, if true, if not, then 0
	// **/
	public function set_customer($arr)
	{
		automatweb::http_exit(http::STATUS_NOT_IMPLEMENTED);
		if (object_loader::can("view" , $arr["cust"]))
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
		if((!(is_array($arr["tasks"]) || object_loader::can("view" , $arr["cust"])) || (!object_loader::can("view" , $this->prop("customer")))) && is_array($arr["bugs"]) && sizeof($arr["bugs"]))
		{
			foreach($arr["bugs"] as $bugc)
			{
				$c = obj($bugc);
				if(($c->class_id() == CL_BUG_COMMENT || $c->class_id() == CL_TASK_ROW)&& object_loader::can("view" , $c->prop("parent.customer")))
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
			$br->set_class_id(crm_bill_row_obj::CLID);
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

	/** adds new row group to bill
		@attrib api=1 params=pos obj_save=1
		@returns CL_CRM_BILL_ROW_GROUP
	**/
	public function add_row_group()
	{
		$row_block_counter = (int) $this->meta("row_block_counter") + 1;
		$br = obj(null, array(), crm_bill_row_group_obj::CLID);
		$br->set_parent($this->id());
		$br->set_name(sprintf(t("Blokk %s"), $row_block_counter));
		$br->save();
		$this->connect(array(
			"to" => $br->id(),
			"type" => "RELTYPE_ROW_GROUP"
		));
		$this->set_meta("row_block_counter", $row_block_counter);
		$this->save();
		return $br;
	}

	/** Returns list of invoice row group objects in this invoice
		@attrib api=1 params=pos
		@comment
		@returns object_list
		@errors
	**/
	public function get_row_groups()
	{
		$list = new object_list($this->connections_from(array("type" => "RELTYPE_ROW_GROUP")));
		return $list;
	}

	/** adds rows to bill
		@attrib api=1 params=name obj_save=1
		@param objects optional type=array
			object ids (tasks, meetings, bugs, calls, task rows , bill rows etc.)
		@returns
	**/
	public function add_rows($arr)
	{
		$co_inst = new crm_company();
		$sts = crm_settings_obj::get_active_instance();
		define("DEFAULT_TAX", 0.18);//TODO: correct this
		$bug_rows = array();
		$task_rows = array();
		$tasks = array();
		foreach(safe_array($arr["objects"]) as $id)
		{
			$work = obj($id);
			switch($work->class_id())
			{
				case crm_bill_row_obj::CLID:
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
							if(is_oid($sts->prop("bill_def_prod")) && object_loader::can("view",$sts->prop("bill_def_prod")))
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
			$sum+= $row["sum"];
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
		return $rows_arr->arr();
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
		if ($this->is_saved())
		{
			$filter = array();
			$filter["class_id"] = crm_bill_row_obj::CLID;
			$filter["CL_CRM_BILL_ROW.RELTYPE_ROW(CL_CRM_BILL)"] = $this->id();
			$filter["writeoff"] = new obj_predicate_not(1);
			$filter[] = new obj_predicate_sort(array("jrk" => "asc"));
		}
		else
		{
			$filter = null;
		}
		return $filter;
	}

	/** Returns bill rows data
		@attrib api=1 params=pos
		@returns array
			array(
				bill_obj1_id => array(
					[oid] => ...
					[name] => ...
					[name_group_comment] => ...
					[parent] => ...
					[brother_of] => ...
					[status] => ...
					[class_id] => ...
					[acldata] => ...
					[task_row] => Array(...)
					[prod] => ...
					[price] => ...
					[amt] => ...
					[has_tax] => ...
					[tax] => ...
					[sum] => ...
					[jrk] => ...
				),
				bill_obj2_id...
			)
	**/
	public function get_bill_rows_dat($translated = false)
	{
		$filter = $this->get_bill_rows_filter();
		$rowsres = array(
			crm_bill_row_obj::CLID => array(
				"task_row", "prod", "price", "amt", "has_tax", "tax", "name_group_comment", "jrk"
			),
		);
		$rows_arr = new object_data_list($filter , $rowsres);
		return $rows_arr->arr();
	}

	/** calculates bill writeoff rows sum without tax
		@attrib api=1
		@returns double
	**/
	public function get_writeoffs_sum()
	{
		$filter = array();
		$filter["class_id"] = crm_bill_row_obj::CLID;
		$filter["CL_CRM_BILL_ROW.RELTYPE_ROW(CL_CRM_BILL)"] = $this->id();
		$filter["writeoff"] = 1;

		$rowsres = array(
			crm_bill_row_obj::CLID => array(
				"price","amt"
			),
		);
		$rows_arr = new object_data_list($filter , $rowsres);
		$sum = 0;
		foreach($rows_arr->arr() as $id => $data)
		{
			$sum+= $data["price"]*$data["amt"];
		}
		return $sum;
	}


	/** Calculates bill sum
		@attrib api=1 params=pos
		@param type type=int
			BILL_SUM total sum with taxes added
			BILL_SUM_WO_TAX - sum without tax(es)
			BILL_SUM_TAX - tax part
			BILL_SUM_WO_DISCOUNT
			BILL_SUM_TAX_WO_DISCOUNT
			BILL_SUM_WO_TAX_WO_DISCOUNT
		@returns float
	**/
	public function get_bill_sum($type = self::BILL_SUM)
	{
		if(!$this->is_saved())
		{
			return (float) 0;
		}

		$rs = "";
		$sum_wo_tax = $tot_amt = $tot_cur_sum = $tax = $sum = 0;

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
			if (object_loader::can("view", $row["prod"]))
			{
				$set = false;
				// get tax from prod
				$prod = obj($row["prod"]);
				if (object_loader::can("", $prod->prop("tax_rate")))
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

		// determine discount
		if (
			self::BILL_SUM_WO_DISCOUNT === $type or
			self::BILL_SUM_TAX_WO_DISCOUNT === $type or
			self::BILL_SUM_WO_TAX_WO_DISCOUNT === $type
		)
		{
			$discount_factor = 1;
		}
		else
		{
			$discount_factor = 1 - ($this->prop("disc") / 100);
		}

		// select desired output
		if (self::BILL_SUM_TAX === $type or self::BILL_SUM_TAX_WO_DISCOUNT === $type)
		{
			$sum = $tax;
		}
		elseif (self::BILL_SUM_WO_TAX === $type or self::BILL_SUM_WO_TAX_WO_DISCOUNT === $type)
		{
			$sum = $sum_wo_tax;
		}
		elseif (self::BILL_AMT === $type)
		{
			$sum = $tot_amt;
		}

		return (float) $sum * $discount_factor;
	}

	//selle asemel kasuta get_bill_rows_dat funktsiooni... iganenud on
	/** returns bill rows data
		@attrib api=1
		@param translated type=bool default=FALSE
		@param combine_by type=string default="" set="comment"|""
		@returns array
			array(
				array(
					"amt" => int row item quantity
					"prod" => product
					"name" => row name
					"comment" => string row caption
					"row_title" => string row title
					"name_group_comment" => string grouped rows comment addition from this row
					"desc" => string text row description
					"price" => float row item price
					"sum" => float row sum
					"km_code" => vat code
					"unit" => int unit oid
					"unit_name" => string unit name
					"jrk" => int row order in rows
					"oid" => int object id
					"has_tax" => bool tax
					"tax" => tax rate
					"tax_sum" => vat part sum
					"date" => string row date
					"persons" =>
					"has_task_row" => bool
					"task_rows" =>
					"quantity_str" => string row item quantity with unit
				),
				...
			)
		@errors
	**/
	public function get_bill_rows_data($translated = false, $combine_by = "")
	{
		$rows_data = array();
		$lang_id = $this->prop("language.aw_lang_id");
		$rows = $this->get_bill_rows();

		if($rows->count())
		{
			// go over rows, get raw data and combine/group
			$row = $rows->begin();

			do
			{
				$kmk = "";
				if (object_loader::can("", $row->prop("prod")))
				{
					$prod = obj($row->prop("prod"));
					if (object_loader::can("", $prod->prop("tax_rate")))
					{
						$tr = obj($prod->prop("tax_rate"));
						$kmk = $tr->prop("code");
					}
				}

				$ppl = array();
				foreach((array)$row->prop("people") as $p_id)
				{
					if (object_loader::can("", $p_id))
					{
						$ppl[$p_id] = $p_id;
					}
				}

				$row_sum = $row->prop("amt") * $row->prop("price");
				$tax_sum = $row_sum / 100 * $row->get_row_tax();
				$rd = array(
					"amt" => $row->prop("amt"),
					"prod" => $row->prop("prod"),
					"name" => $translated ? $row->trans_get_val("desc", $lang_id) : $row->prop("desc"),
					"name_group_comment" => nl2br($translated ? $row->trans_get_val("name_group_comment", $lang_id) : $row->prop("name_group_comment")),
					"row_title" => $translated ? $row->trans_get_val("row_title", $lang_id) : $row->prop("row_title"),
					"comment" => $translated ? $row->trans_get_val("comment", $lang_id) : $row->prop("comment"),
					"desc" => nl2br($translated ? $row->trans_get_val("desc", $lang_id) : $row->prop("desc")),
					"price" => $row->prop("price") == (int) $row->prop("price") ? $row->prop("price") : number_format($row->prop("price"), 2, ".", " "),
					"sum" => $row_sum,
					"km_code" => $kmk,
					"unit" => $row->prop("unit"),
					"unit_name" => $row->trans_get_val("unit.name", $lang_id),
					"jrk" => $row->ord(),
					"oid" => $row->id(),
					"has_tax" => $row->prop("has_tax"),
					"tax" => $row->get_row_tax(),
					"tax_sum" => $tax_sum,
					"date" => $row->prop("date"),
					"persons" => $ppl,
					"has_task_row" => $row->has_task_row(),
					"task_rows" => $row->task_rows()
				);

				// group/combine if requested
				if ("comment" === $combine_by)
				{
					// combined key to not combine rows by comment that have different units or other not combinable properties
					/// group/combine if rows have
					$key =
						$rd["comment"] . // same title
						$rd["prod"] . // same product
						$rd["km_code"] .  // same VAT code
						$rd["unit"] . // same quantity unit
						$rd["has_tax"] . // same tax setting
						$rd["tax"] .  // same tax pct
						// $rd["price"] . // same price
						""
					;

					if (isset($rows_data[$key]))
					{
						$rows_data[$key]["amt"] += $rd["amt"];
						$rows_data[$key]["sum"] += $rd["sum"];
						$rows_data[$key]["tax_sum"] += $rd["tax_sum"];
						$rows_data[$key]["name_group_comment"] .= html::linebreak(2) . $rd["name_group_comment"];
						$rows_data[$key]["desc"] .= html::linebreak(2) . $rd["desc"];
					}
					else
					{
						$rows_data[$key] = $rd;
					}
				}
				else
				{
					$rows_data[] = $rd;
				}
			}
			while ($row = $rows->next());

			// iterate over combined and parsed rows once more to
			// convert applicable values to human readable formats
			//TODO: api peaks andma raw data. viia see konverteerimine v2lja, liideseklassi
			foreach ($rows_data as $key => $rd)
			{
				try
				{
					$unit = obj((int) $rd["unit"], array(), unit_obj::CLID);
				}
				catch (Exception $e)
				{
					$unit = obj(null, array(), unit_obj::CLID);
				}

				$quantity = $rd["amt"] == (int) $rd["amt"] ? (int) $rd["amt"] : number_format($rd["amt"], 2, ".", " ");
				$rd["amt"] = $quantity;
				$rd["quantity_str"] = $unit->get_string_for_value($quantity, $lang_id);
				$rd["tax_sum"] = $rd["tax_sum"] == (int) $rd["tax_sum"] ? (int) $rd["tax_sum"] : number_format($rd["tax_sum"], 2, ".", " ");
				$rd["sum"] = $rd["sum"] == (int) $rd["sum"] ? (int) $rd["sum"] : number_format($rd["sum"], 2, ".", " ");
				$rows_data[$key] = $rd;
			}
		}
		usort($rows_data, array($this, "__br_sort"));
		return $rows_data;
	}

	private function __br_sort($a, $b)
	{
		$a_tm = $b_tm = 0;

		if (!empty($a["date"]))
		{
			$a_date = $a["date"];
			list($a_d, $a_m, $a_y) = explode(".", $a_date);
			$a_tm = mktime(0,0,0, $a_m, $a_d, $a_y);
		}

		if (!empty($b["date"]))
		{
			$b_date = $b["date"];
			list($b_d, $b_m, $b_y) = explode(".", $b_date);
			$b_tm = mktime(0,0,0, $b_m, $b_d, $b_y);
		}

		return $a["jrk"] < $b["jrk"] ? -1 :
			($a["jrk"] > $b["jrk"] ? 1:
				($a_tm >  $b_tm ? 1:
					($a_tm == $b_tm ? ($a["oid"] > $b["oid"] ? 1 : -1): -1)
				)
			);
	}

	/** returns bill projects
		@attrib api=1
		@returns object_list(CL_PROJECT)
	**/
	public function get_projects()
	{
		$projects = new object_list(array(
			"class_id" => project_obj::CLID,
			"CL_PROJECT.RELTYPE_PROJECT(CL_CRM_BILL)" => $this->id()
		));
		return $projects;
	}

	/** Returns list of project leaders associated with this invoice
		@attrib api=1
		@returns object_list(CL_CRM_PERSON)
	**/
	public function get_project_leaders()
	{
		$project_leaders = new object_list(array(
			"class_id" => project_obj::CLID,
			"CL_PROJECT.RELTYPE_PROJECT(CL_CRM_BILL)" => $this->id()
		), array("object_id_property" => "proj_mgr"));
		return $project_leaders;
	}

	/** returns bill project leader names
		@attrib api=1
		@returns array
	**/
	public function project_leader_names()
	{
		$names = $this->get_project_leaders()->names();//TODO: odl-i abil?
		return $names;
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
				"text" =>  htmlspecialchars($o->comment(), ENT_COMPAT, languages::USER_CHARSET)
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
			"sort_by" => "objects.created desc"
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

	/** Returns bill email recipients data
		@attrib api=1 params=pos
		@param type type=array default=array()
			Type(s) of recipients to return. Empty/default means all.
			Valid options for array elements:
				'' -- all recipients
				'project_managers' -- people associated with this project as project managers
				'user' -- bill creator and current user
				'default' -- crm default bill recipients
				'customer_general' -- general customer email contacts
				'customer_bill' -- customer bill reception email contacts
				'custom' -- user defined custom recipients
		@returns array
			Associative multidimensional array
				$string_recipient_email_address => array($string_recipient_oid_or_zero, $string_recipient_name)
		@errors
	**/
	public function get_mail_recipients($type = array())
	{
		if (!is_array($type))
		{
			throw new awex_obj_type("Invalid type argument " . var_export($type, true));
		}

		$recipients = array();
		$customer_oid = (int) $this->prop("customer");

		if (!count($type) or in_array("project_managers", $type))
		{
			// add project managers
			$project_managers = $this->get_project_leaders();
			if($project_managers->count())
			{
				$project_manager = $project_managers->begin();

				do
				{
					$email = $project_manager->get_mail($customer_oid);
					if (is_email($email))
					{
						$recipients[$email] = array($project_manager->id(), $project_manager->name());
					}
				}
				while ($project_manager = $project_managers->next());
			}
		}

		if (!count($type) or in_array("user", $type))
		{
			// add current user
			if (aw_global_get("uid_oid"))
			{
				$user_inst = new user();
				$u = obj(aw_global_get("uid_oid"));
				$person = obj($user_inst->get_current_person());
				$email = $u->get_user_mail_address();
				if (is_email($email))
				{
					$recipients[$email] = array($person->id(), $person->name());
				}
			}

			// add invoice creator
			if ($this->prop("assembler"))
			{
				$person = obj($this->prop("assembler"));
				$email = $person->get_mail($customer_oid);
				if (is_email($email))
				{
					$recipients[$email] = array($person->id(), $person->name());
				}
			}
		}

		if (!count($type) or in_array("default", $type))
		{
			if($this->set_crm_settings() and $this->crm_settings->prop("bill_mail_to"))
			{
				$emails = explode(",", $this->crm_settings->prop("bill_mail_to"));
				foreach ($emails as $email)
				{
					$email = trim($email);
					if (is_email($email))
					{
						$recipients[$email] = array(0, "");
					}
				}
			}
		}

		if (!count($type) or in_array("customer_general", $type))
		{
			$name = $this->get_customer_name();
			foreach($this->get_cust_mails() as $email)
			{
				if (is_email($email))
				{
					$recipients[$email] = array($customer_oid, $name);
				}
			}
		}

		if (!count($type) or in_array("customer_bill", $type))
		{
			if(is_email($this->prop("bill_mail_to")))
			{
				$recipients[$this->prop("bill_mail_to")] = array(0, "");
			}

			try
			{
				$cro = new object($this->prop("customer_relation"));
				if ($cro->is_saved())
				{
					// customer invoice e-mails
					$name = $this->get_customer_name();
					$customer_o = obj($customer_oid, array(), crm_company_obj::CLID);
					$invoice_emails = $customer_o->get_email_addresses("invoice");
					if($invoice_emails->count())
					{
						$email = $invoice_emails->begin();

						do
						{
							if (is_email($email->prop("mail")))
							{
								$recipients[$email->prop("mail")]  = array($customer_oid, $name);
							}
						}
						while ($email = $invoice_emails->next());
					}


					// customer relation invoice e-mails
					$bill_person_ol = new object_list($cro->connections_from(array("reltype" => "RELTYPE_BILL_PERSON")));
					if($bill_person_ol->count())
					{
						$person = $bill_person_ol->begin();

						do
						{
							$emails = $person->get_email_addresses("invoice");
							if($emails->count())
							{
								$email = $emails->begin();

								do
								{
									if (is_email($email->prop("mail")))
									{
										$recipients[$email->prop("mail")]  = array($person->id(), $person->name());
									}
								}
								while ($email = $emails->next());
							}
							elseif ($email = $person->get_email_address())
							{
								if (is_email($email->prop("mail")))
								{
									$recipients[$email->prop("mail")]  = array($person->id(), $person->name());
								}
							}
						}
						while ($person = $bill_person_ol->next());
					}
				}
			}
			catch (awex_obj $e)
			{
			}
		}

		if (!count($type) or in_array("custom", $type))
		{
			// manually added recipients
			$custom = $this->get_receivers();
			foreach ($custom as $email => $person_oid)
			{
				if (is_email($email))
				{
					if ($person_oid)
					{
						$person = new object($person_oid);
						$recipients[$email]  = array($person->id(), $person->name());
					}
					else
					{
						$recipients[$email]  = array(0, "");
					}
				}
			}

			// recipients defined by object relation
			$custom = $this->connections_from(array("type" => "RELTYPE_RECEIVER"));
			foreach($custom as $c)
			{
				$person = $c->to();
				$email = $person->get_mail();
				if (is_email($email))
				{
					$recipients[$email]  = array($person->id(), $person->name());
				}
			}
		}

		return $recipients;
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
		$default_mail = null;
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
		return join("\n" , $ret);
	}

	public function get_mail_from_default()
	{
		$ret = "";
		if($this->set_crm_settings() && $this->crm_settings->prop("bill_mail_from"))
		{
			$ret = $this->crm_settings->prop("bill_mail_from");
		}
		else
		{
			$u = obj(aw_global_get("uid_oid"));
			$ret = $u->get_user_mail_address();
		}

		return $ret;
	}

	public function get_mail_from_name_default()
	{
		$ret = "";
		if($this->set_crm_settings() && $this->crm_settings->prop("bill_mail_from_name"))
		{
			$ret = $this->crm_settings->prop("bill_mail_from_name");
		}
		else
		{
			$ret = aw_global_get("uid");
			$u = get_instance(CL_USER);
			$p = $u->get_current_person();

			if($p)
			{
				try
				{
					$p = obj($p, array(), CL_CRM_PERSON);
					$ret = $p->name();
				}
				catch (Exception $e)
				{
				}
			}
		}

		return $ret;
	}

	/** Returns mail subject string
		@attrib api=1 params=pos
		@param parse type=bool default=TRUE
			Whether to return parsed subject with special tags replaced by values or raw string
		@comment
		@returns string
		@errors
	**/
	public function get_mail_subject($parse = true)
	{
		if($this->prop("bill_mail_subj"))
		{
			$subject = $this->prop("bill_mail_subj");
		}
		elseif($this->set_crm_settings() && $this->crm_settings->prop("bill_mail_subj"))
		{
			$subject = $this->crm_settings->prop("bill_mail_subj");
		}
		else
		{
			$subject = "";
		}

		if ($subject and $parse)
		{
			$subject = $this->parse_text_variables($subject);
		}

		return $subject;
	}

	public function get_customer_name()
	{
		if($this->prop("customer_name"))
		{
			$name = $this->prop("customer_name");
		}
		else
		{
			$name = $this->prop("customer_relation.buyer.name");
		}

		return $name;
	}

	/** Returns mail body/contents string
		@attrib api=1 params=pos
		@param parse type=bool default=TRUE
			Whether to return parsed contents with special tags replaced by values or raw string
		@comment
		@returns string
		@errors
	**/
	public function get_mail_body($parse = true)
	{
		if($this->prop("bill_mail_ct"))
		{
			$content = $this->prop("bill_mail_ct");
		}
		elseif($this->set_crm_settings() && $this->crm_settings->prop("bill_mail_ct"))
		{
			$content = $this->crm_settings->prop("bill_mail_ct");
		}
		else
		{
			$content = "";
		}

		if ($content and $parse)
		{
			$content = $this->parse_text_variables($content);
		}

		return $content;
	}

	/** Parses variables in invoice e-mail body or subject text
		@attrib api=1 params=pos
		@param text type=string
			Text to parse variables in
		@comment
			Available variables are
			#type#
			#type2#
			#bill_no#
			#customer_name#
			#contact_person#
			#signature#

		@returns string
		@errors
	**/
	public function parse_text_variables($text)
	{
		$project = $this->get_projects();
		$project =	$project->count() ? $project->begin()->name() : "";
		$replace = array(
			"#type#" => $this->prop("state") == self::STATUS_OFFER ? t("pakkumuse") : t("arve"),
			"#type2#" => $this->prop("state") == self::STATUS_OFFER ? t("Pakkumus") : t("Arve"),
			"#bill_no#" => $this->prop("bill_no"),
			"#customer_name#" => $this->get_customer_name(),
			"#project_name#" => $project,
			"#contact_person#" => $this->get_customer_contact_person_name(),
			"#signature#" => $this->get_sender_signature()
		);

		foreach($replace as $key => $val)
		{
			$text = str_replace($key, $val , $text);
		}

		return $text;
	}

	public static function get_text_variables_legend()
	{
		return '#bill_no# => '.t("Arve number").'
#customer_name# => '.t("Kliendi nimi").'
#project_name# => '.t("Projekti nimi").'
#contact_person# => '.t("Kliendi kontaktisiku nimi").'
#signature# => '.t("Saatja allkiri").'
';
	}

	// Can't be private (nor protected). Called in crm_bill::_bill_targets()! -kaarel 21.07.2009
	public function set_crm_settings()
	{
		if(!isset($this->crm_settings) || !is_oid($this->crm_settings))
		{
			$this->crm_settings = crm_settings_obj::get_active_instance();
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
			"parent" => $this->id()
		));
		return $ol;
	}

	private function _clear_pdf_cache()
	{
		if (object_loader::can("", $this->meta("crm_bill_attachments_tmp_invoice_pdf_oid")))
		{
			$pdf = obj($this->meta("crm_bill_attachments_tmp_invoice_pdf_oid"), array(), CL_FILE);
			$pdf->delete(true);
		}

		if (object_loader::can("", $this->meta("crm_bill_attachments_tmp_reminder_pdf_oid")))
		{
			$pdf = obj($this->meta("crm_bill_attachments_tmp_reminder_pdf_oid"), array(), CL_FILE);
			$pdf->delete(true);
		}

		if (object_loader::can("", $this->meta("crm_bill_attachments_tmp_appendix_pdf_oid")))
		{
			$pdf = obj($this->meta("crm_bill_attachments_tmp_appendix_pdf_oid"), array(), CL_FILE);
			$pdf->delete(true);
		}

		$this->_clear_pdf_file_references_tmp();
	}

	private function _clear_pdf_file_references_tmp()
	{
		$this->set_meta("crm_bill_attachments_tmp_invoice_pdf_oid", "");
		$this->set_meta("crm_bill_attachments_tmp_reminder_pdf_oid", "");
		$this->set_meta("crm_bill_attachments_tmp_appendix_pdf_oid", "");
	}


	/**
		@attrib api=1 params=pos obj_save=1
		@param create type=bool default=TRUE
			Create or get from cache if found
		@returns CL_FILE
		@errors
	**/
	public function get_invoice_pdf($create = true)
	{
		$lang_id = $this->prop("language.aw_lang_id") ? $this->prop("language.aw_lang_id") : languages::LC_EST;
		$invoice_pdf = null;
		if (object_loader::can("", $this->meta("crm_bill_attachments_tmp_invoice_pdf_oid")))
		{
			try
			{
				$invoice_pdf = obj($this->meta("crm_bill_attachments_tmp_invoice_pdf_oid"), array(), CL_FILE);
			}
			catch (awex_obj $e)
			{
			}
		}

		if ($create and !$invoice_pdf)
		{
			// get html content
			$inst = new crm_bill();
			$content = $inst->show(array(
				"id" => $this->id(),
				"pdf" => 1,
				"return" => 1
			));

			// create file
			$f = new file();
			$id = $f->create_file_from_string(array(
				"parent" => $this->id(),
				"content" => $content,
				"name" => sprintf(($this->prop("state") == self::STATUS_OFFER ? t("pakkumus_nr_%s", $lang_id) : t("arve_nr_%s", $lang_id)), $this->prop("bill_no")) . ".pdf",
				"type" => "application/pdf"
			));
			$this->set_meta("crm_bill_attachments_tmp_invoice_pdf_oid", $id);
			$this->clear_pdf_cache = false;
			$this->save();
			$invoice_pdf = obj($id, array(), CL_FILE);
		}

		return $invoice_pdf;
	}

	/**
		@attrib api=1 params=pos obj_save=1
		@param create type=bool default=TRUE
			Create or get from cache if found
		@returns CL_FILE
		@errors
	**/
	public function get_reminder_pdf($create = true)
	{
		$lang_id = $this->prop("language.aw_lang_id") ? $this->prop("language.aw_lang_id") : languages::LC_EST;
		$reminder_pdf = null;
		if (object_loader::can("", $this->meta("crm_bill_attachments_tmp_reminder_pdf_oid")))
		{
			try
			{
				$reminder_pdf = obj($this->meta("crm_bill_attachments_tmp_reminder_pdf_oid"), array(), CL_FILE);
			}
			catch (awex_obj $e)
			{
			}
		}

		if ($create and !$reminder_pdf)
		{
			// get html content
			$inst = new crm_bill();
			$content = $inst->show(array(
				"id" => $this->id(),
				"pdf" => 1,
				"return" => 1,
				"view_type" => "reminder"
			));

			// create file
			$f = new file();
			$id = $f->create_file_from_string(array(
				"parent" => $this->id(),
				"content" => $content,
				"name" => sprintf(t("arve_nr_%s_meeldetuletus", $lang_id), $this->prop("bill_no")) . ".pdf",
				"type" => "application/pdf"
			));
			$this->set_meta("crm_bill_attachments_tmp_reminder_pdf_oid", $id);
			$this->clear_pdf_cache = false;
			$this->save();
			$reminder_pdf = obj($id, array(), CL_FILE);
		}

		return $reminder_pdf;
	}

	/**
		@attrib api=1 params=pos obj_save=1
		@param create type=bool default=TRUE
			Create or get from cache if found
		@returns CL_FILE
		@errors
	**/
	public function get_appendix_pdf($create = true)
	{
		$lang_id = $this->prop("language.aw_lang_id") ? $this->prop("language.aw_lang_id") : languages::LC_EST;
		$appendix_pdf = null;
		if (object_loader::can("", $this->meta("crm_bill_attachments_tmp_appendix_pdf_oid")))
		{
			try
			{
				$appendix_pdf = obj($this->meta("crm_bill_attachments_tmp_appendix_pdf_oid"), array(), CL_FILE);
			}
			catch (awex_obj $e)
			{
			}
		}

		if ($create and !$appendix_pdf)
		{
			// get html content
			$inst = new crm_bill();
			$content = $inst->show(array(
				"id" => $this->id(),
				"pdf" => 1,
				"view_type" => "descriptions",
				"return" => 1
			));

			// create file
			$f = new file();
			$id = $f->create_file_from_string(array(
				"parent" => $this->id(),
				"content" => $content,
				"name" => sprintf(($this->prop("state") == self::STATUS_OFFER ? t("pakkumuse_nr_%s_detailid", $lang_id) : t("arve_nr_%s_seletuskiri", $lang_id)), $this->prop("bill_no")) . ".pdf",
				"type" => "application/pdf"
			));
			$this->set_meta("crm_bill_attachments_tmp_appendix_pdf_oid", $id);
			$this->clear_pdf_cache = false;
			$this->save();
			$appendix_pdf = obj($id, array(), CL_FILE);
		}

		return $appendix_pdf;
	}

	/** Sends invoice document by mail
		@attrib api=1 params=pos obj_save=1
		@param to type=array
			Associative array of email addresses => names to send e-mail to
		@param subject type=string
			E-mail subject
		@param body type=string
			E-mail body text
		@param cc type=array
			Associative array of email addresses => names to send e-mail copy to
		@param bcc type=array
			Associative array of email addresses => names to send e-mail blind copy to
		@param appendix type=bool
			Whether to send invoice appendix describing invoice rows in detail
		@param reminder type=bool
			Whether to send invoice document as a reminder
		@param from type=string default=""
			Sender e-mail address, default means either defined system default or current user e-mail address
		@param from_name type=string default=""
			Sender name, default means either defined system default or current user name
		@comment
		@returns void
		@errors
			throws awex_crm_bill_email if an invalid e-mail address given. awex_crm_bill_email::$email empty if no recipients or the faulty email address if encountered
			throws awex_crm_bill_send if sending e-mail fails
			throws awex_crm_bill_file if file attachment fails
		@qc date=20110127 standard=aw3
	**/
	public function send_by_mail($to, $subject, $body, $cc = array(), $bcc = array(), $appendix = false, $reminder = false, $from = "", $from_name = "")
	{
		if (!count($to) and !count($cc) and !count($bcc))
		{
			throw new awex_crm_bill_email("Can't send mail, no recipients specified");
		}

		// get or create file attachments
		/// main invoice document
		if (!$reminder)
		{ // regular
			$invoice_pdf = $this->get_invoice_pdf();
		}
		else
		{ // sent as a reminder
			$invoice_pdf = $this->get_reminder_pdf();
		}

		if (!is_object($invoice_pdf))
		{
			throw new awex_crm_bill_file("Main invoice file lost or not created. Invoice id " . $this->id());
		}

		/// appendix
		if ($appendix)
		{
			$appendix_pdf = $this->get_appendix_pdf();
		}

		// parse recipients
		foreach ($to as $email_address => $recipient_name)
		{
			if (!is_email($email_address))
			{
				$e = new awex_crm_bill_email("Invalid email address '{$email_address}'. Sending invoice " . $this->id());
				$e->email = $email_address;
				throw $e;
			}

			$to[$email_address] = $recipient_name ? "{$recipient_name} <{$email_address}>" : $email_address;
		}
		$to = implode(",", $to);

		foreach ($cc as $email_address => $recipient_name)
		{
			if (!is_email($email_address))
			{
				$e = new awex_crm_bill_email("Invalid email address '{$email_address}'. Sending invoice " . $this->id());
				$e->email = $email_address;
				throw $e;
			}

			$cc[$email_address] = $recipient_name ? "{$recipient_name} <{$email_address}>" : $email_address;
		}
		$cc = implode(",", $cc);

		foreach ($bcc as $email_address => $recipient_name)
		{
			if (!is_email($email_address))
			{
				$e = new awex_crm_bill_email("Invalid email address '{$email_address}'. Sending invoice " . $this->id());
				$e->email = $email_address;
				throw $e;
			}

			$bcc[$email_address] = $recipient_name ? "{$recipient_name} <{$email_address}>" : $email_address;
		}

		/// add crm default recipients
		$default_recipients = $this->get_mail_recipients(array("default"));
		foreach ($default_recipients as $email_address => $data)
		{
			$bcc[$email_address] = $email_address;
		}

		$bcc = implode(",", $bcc);

		// compose mail
		$from = is_email($from) ? $from : $this->prop("bill_mail_from");
		$from_name = empty($from_name) ? $this->prop("bill_mail_from_name") : $from_name;
		$att_comment = "";

		$awm = new aw_mail();
		$awm->set_send_method("mimemessage");
		$awm->create_message(array(
			"froma" => $from,
			"fromn" => $from_name,
			"subject" => $subject,
			"body" => strip_tags($body),
			"to" => $to,
			"cc" => $cc,
			"bcc" => $bcc
		));
		$awm->set_header("Reply-To", $from);

		/// add attachments
		// add main invoice pdf
		$part_count = $awm->fattach(array(
			"path" => $invoice_pdf->prop("file"),
			"contenttype"=> aw_mime_types::type_for_file($invoice_pdf->name()),
			"name" => $invoice_pdf->name()
		));
		$att_comment .= html::href(array(
			"caption" => html::img(array(
				"url" => aw_ini_get("icons.server")."pdf_upload.gif",
				"border" => 0
			)).$invoice_pdf->name(),
			"url" => $invoice_pdf->get_url()
		));

		if (!$part_count)
		{
			throw new awex_crm_bill_file("Attaching main invoice file (id: " . $invoice_pdf->id() . ") failed. Invoice id " . $this->id());
		}

		// add appendix pdf
		if($appendix)
		{
			$part_count = $awm->fattach(array(
				"path" => $appendix_pdf->prop("file"),
				"contenttype"=> aw_mime_types::type_for_file($appendix_pdf->name()),
				"name" => $appendix_pdf->name(),
			));
			$att_comment .= html::href(array(
				"caption" => html::img(array(
					"url" => aw_ini_get("icons.server")."pdf_upload.gif",
					"border" => 0,
				)) . $appendix_pdf->name(),
				"url" => $appendix_pdf->get_url(),
			));

			if (!$part_count)
			{
				throw new awex_crm_bill_file("Attaching  invoice appendix file (id: " . $appendix_pdf->id() . ") failed. Invoice id " . $this->id());
			}
		}

		// add attachments from additional invoice attachment files (RELTYPE_ATTACHED_FILE)
		$attachments_list = new object_list(array(
			"class_id" => CL_FILE,
			"CL_FILE.RELTYPE_ATTACHED_FILE(CL_CRM_BILL)" => $this->id()
		));

		if($attachments_list->count())
		{
			$file_o = $attachments_list->begin();

			do
			{
				$part_count = $awm->fattach(array(
					"path" => $file_o->prop("file"),
					"contenttype"=> aw_mime_types::type_for_file($file_o->name()),
					"name" => $file_o->name()
				));
				$att_comment .= html::href(array(
					"caption" => $file_o->name(),
					"url" => $file_o->get_url()
				));

				if (!$part_count)
				{
					throw new awex_crm_bill_file("Attaching  invoice additional file (id: " . $file_o->id() . ") failed. Invoice id " . $this->id());
				}
			}
			while ($file_o = $attachments_list->next());
		}

		// add mail html body
		$awm->htmlbodyattach(array(
			"data" => $body
		));

		// send mail
		try
		{
			$awm->send();
		}
		catch (awex_awmail_send $e)
		{
			throw new awex_crm_bill_send ("Sending '".$this->id()."' failed. Mailer error: " . $e->getMessage());
		}

		// write log
		/// mail message object for logging
		$mail = obj(null, array(), CL_MESSAGE);
		$mail->set_parent($this->id());
		$mail->set_name(t("saadetud arve")." ".$this->prop("bill_no")." ".t("kliendile")." ".$this->get_customer_name());
		$mail->save();

		$attachments = array($invoice_pdf->id());
		$invoice_pdf ->set_parent($mail->id());
		$invoice_pdf->save();

		if ($appendix)
		{
			$attachments[] = $appendix_pdf->id();
			$appendix_pdf ->set_parent($mail->id());
			$appendix_pdf->save();
		}

		$mail->set_prop("attachments", $attachments);
		$mail->set_prop("customer", $this->prop("customer"));
		$mail->set_prop("message", $body);
		$mail->set_prop("html_mail", 1);
		$mail->set_prop("mfrom_name", $from_name);
		$mail->set_prop("mto", $to);
		$mail->set_prop("cc", $cc);
		$mail->set_prop("bcc", $bcc);
		$mail->save();

		$comment = html_entity_decode(sprintf(t("Saadetud aadressidele: %s; koopia aadressidele: %s; tekst: %s; lisatud failid: %s."), $to, $cc, $body, $att_comment));
		$this->send(self::DELIVERY_EMAIL, $comment);
	}

	/**
		@attrib api=1 params=pos obj_save=1
		@param delivery_method type=int default=crm_bill_obj::DELIVERY_EMAIL
		@param comment type=string default=""
		@returns void
		@errors
	**/
	public function send($delivery_method = self::DELIVERY_EMAIL, $comment = "")
	{
		$state = (int) $this->prop("state");
		if (
			self::STATUS_DRAFT === $state
			or self::STATUS_READY === $state
			or self::STATUS_VERIFIED === $state
		)
		{
			$this->set_prop("state", self::STATUS_SENT);
		}

		$comment = sprintf(t("%s saatis arve nr. %s. K&auml;ttetoimetamismeetod %s; summa %s; kuup&auml;ev: %s; kellaaeg: %s."), aw_global_get("uid"), $this->prop("bill_no"), $this->delivery_method_names($delivery_method), $this->prop("sum"), date("d.m.Y") , date("H:i")) . ($comment ? " " . $comment : "");
		$this->add_comment($comment);

		// clear attachment file references so they won't be deleted as junk
		// the ones sent are archived
		$this->_clear_pdf_file_references_tmp();

		// save changes to this
		$this->save();
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
			if (object_loader::can("view", $row->prop("prod")))
			{
				$prod = obj($row->prop("prod"));
				if (object_loader::can("view", $prod->prop("tax_rate")))
				{
					$tr = obj($prod->prop("tax_rate"));
					$kmk = $tr->prop("code");
				}
			}

			$ppl = array();
			foreach((array)$row->prop("people") as $p_id)
			{
				if (object_loader::can("view", $p_id))
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
				"jrk" => $row->ord(),
				"id" => $row->id(),
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
		usort($inf, array($this, "__br_sort"));
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
		if(false === $this->implementor_object and is_oid($this->prop("impl")))
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
				"class_id" => crm_company_customer_data::CLID,
				"buyer" => $this->prop("customer"),
				"seller" => $this->implementor_object->id()
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
		if(object_loader::can("view" , ($phone)))
		{
			return get_name($phone);

		}
		if(object_loader::can("view" , $this->prop("customer")))
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
		if ($a->comment() != $b->comment())
		{
			return strcmp($a->comment(), $b->comment());
		}
		return $a_tm > $b_tm ? 1:
			($a_tm == $b_tm ?
				($a->id() > $b->id() ? 1 : -1): -1);

	}

	/** Takes first row in list and merges other rows' data to that
		@attrib api=1 params=pos
		@param rows type=object_list
		@param aggregate_total type=float default=NULL
			Set sum to this disregarding rows sum
		@comment
		@returns void
		@errors
	**/
	public function merge_rows(object_list $rows, $aggregate_total = null)
	{
		if (!$rows->count())
		{
			return;
		}

		$first_row = $rows->begin();

		$first_task_row = $first_row->get_first_obj_by_reltype("RELTYPE_TASK_ROW");
		if(is_object($first_task_row))
		{
			$mtrid = $first_task_row->id();
		}

		if ($aggregate_total !== null)
		{
			$first_row->set_prop("price", $aggregate_total);
		}

		while ($row_o = $rows->next())
		{
			$first_row->set_prop("amt", $first_row->prop("amt") + $row_o->prop("amt"));
			$first_row->set_prop("sum", $first_row->prop("amt") * $first_row->prop("price"));
			$task_row = $row_o->get_first_obj_by_reltype("RELTYPE_TASK_ROW");
			if(is_object($task_row))
			{
				$task_row->set_meta("parent_row" , $mtrid);
				$first_row->connect(array(
					"to" => $task_row->id(),
					"type" => "RELTYPE_TASK_ROW"
				));
				$task_row->save();
			}
			$row_o->delete();
		}

		$first_row->save();
	}


	public function reorder_rows()
	{
		$rows = $this->get_bill_rows();
		if($rows->count())
		{
			$rows->sort_by_cb(array($this, "__rsorter"));
			$count = 1;
			$row = $rows->begin();

			do
			{
				$row->set_ord($count*self::ROW_ORDER_INCREMENT);
				$row->save();
				$count++;
			}
			while ($row = $rows->next());
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
			"class_id" => CL_UNIT
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
			"name" => $name
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
			"customer_address",
			"ctp_text",
			"warehouse",
			"price_list",
			"transfer_method",
			"transfer_condition",
			"selling_order",
			"transfer_address",
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

		if($arr["dno"] === "new")
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
		elseif(object_loader::can("view", $arr["dno"]))
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
		if(is_oid($this->id()) && (object_loader::can("view", $impl)))
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
				"conf" => $ids
			));
			if($ol->count() == 1)
			{
				return $ol->begin()->id();
			}
		}
		return null;
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
			if(object_loader::can("" , $this->prop("customer")))
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
			}
		}
	}

	public function set_customer_address($prop, $value)
	{
		if (!isset(self::$customer_address_properties[$prop]))
		{
			throw new awex_crm_bill_address("Invalid address property '$prop'");
		}

		$cust_addr = $this->meta("customer_addr");
		$cust_addr[$prop] = $value;
		$this->set_meta("customer_addr", $cust_addr);
	}

	public function get_payment_id()
	{
		foreach($this->connections_from(array("type" => "RELTYPE_PAYMENT")) as $conn)
		{
			return $conn->prop("to");
		}
		return null;
	}

	/** Return all bill e-mail receivers as associative array
		@attrib api=1 params=pos
		@comment
		@returns array
			e-mail address => person object id. Person object may be NULL or CL_CRM_PERSON oid
		@errors
	**/
	public function get_receivers()
	{
		$bill_receivers = safe_array($this->meta("bill_receivers"));
		return $bill_receivers;
		//TODO: other receiving emails from elsewhere
	}

	/** Add bill e-mail receiver
		@attrib api=1 params=pos obj_save=1
		@param email type=CL_ML_MEMBER/string
		@param person type=CL_CRM_PERSON default=NULL
		@returns void
		@errors
			throws awex_obj_type if a parameter is invalid
	**/
	public function add_recipient($email, object $person = null)
	{
		if($email instanceof object and !$email->is_a(CL_ML_MEMBER) or !is_email($email))
		{
			throw new awex_obj_type("Invalid e-mail address");
		}

		$bill_receivers = safe_array($this->meta("bill_receivers"));
		$bill_receivers[$email] = null;
		$this->set_meta("bill_receivers", $bill_receivers);
		$this->save();
		//TODO: person, don't store in meta
	}

	/** Remove bill e-mail receiver by email address
		@attrib api=1 params=pos obj_save=1
		@param email type=string
		@returns void
		@errors
			throws awex_obj_prop if given email is not in receivers list
	**/
	public function remove_receiver($email)
	{
		$bill_receivers = safe_array($this->meta("bill_receivers"));
		$bill_receivers[$email] = null;
		$this->set_meta("bill_receivers", $bill_receivers);
		$this->save();
		//TODO
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
			"class_id" => self::CLID,
			"bill_no" => $arr["no"]
		));
		$id = $bills->count() ? (int) $bills->begin()->id() : 0;
		return $id;
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
			$cust_data = $this->prop("");
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
		@errors
			throws awex_obj_type if overdue interest is not set or incorrect (> 0 required)
			throws awex_crm_bill_state if state isn't RECEIVED
	**/
	public function make_overdue_bill()
	{
		if($this->prop("state") != self::STATUS_RECEIVED)
		{
			throw new awex_crm_bill_state("Status must be 'received'");
		}

		if($this->get_overdue_charge() <= 0)
		{
			throw new awex_obj_type("Overdue interest can't be 0 or negative");
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
	public function get_bill_text($translated = false)
	{
		$bill_text = "";

		if ($translated)
		{
			$language_id = $this->prop("language.aw_lang_id");
			$bill_text = $this->trans_get_val("bill_text", $language_id);
		}
		else
		{
			$bill_text = $this->prop("bill_text");
		}

		return $bill_text;
	}

	 /** returns unit name
		@attrib api=1 params=pos
		@returns string
	**/
	public function get_unit_name($unit)
	{
		if(object_loader::can("", $unit))
		{
			$uo = obj($unit);
			if(object_loader::can("", $this->prop("language")))
			{
				$u_trans = $uo->meta("translations");
				$unit_name = $u_trans[obj($this->prop("language"))->prop("aw_lang_id")]["unit_code"];
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
	public function get_creator()
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

	 /** Returns bill customer contact person object
		@attrib api=1
		@returns CL_CRM_PERSON/NULL
			returns NULL if contact person not found
	**/
	public function get_contact_person()
	{
		if(object_loader::can("" , $this->prop("customer_relation")))
		{
			$crel = obj($this->prop("customer_relation"));
			$contact_person = $crel->get_first_obj_by_reltype("RELTYPE_BILL_PERSON");

			if (!$contact_person)
			{
				try
				{
					if ($crel->prop("buyer_contact_person"))
					{
						$contact_person = new object((int) $crel->prop("buyer_contact_person"));
					}
					elseif ($crel->prop("buyer_contact_person2"))
					{
						$contact_person = new object((int) $crel->prop("buyer_contact_person2"));
					}
					elseif ($crel->prop("buyer_contact_person3"))
					{
						$contact_person = new object((int) $crel->prop("buyer_contact_person3"));
					}
				}
				catch (Exception $e)
				{
					$contact_person = null;
				}
			}
		}

		if(!$contact_person and $this->prop("customer_relation.buyer.firmajuht"))
		{
			try
			{
				$contact_person = new object($this->prop("customer_relation.buyer.firmajuht"));
			}
			catch (Exception $e)
			{
				$contact_person = null;
			}
		}

		return $contact_person;
	}

	 /** Returns invoice customer contact person name as it is set in this invoice
		@attrib api=1
		@returns string
	**/
	public function get_customer_contact_person_name()
	{
		$contact_person_name = $this->prop("ctp_text") or
		$contact_person_name = $this->prop("customer_relation.RELTYPE_BILL_PERSON.name") or
		$contact_person_name = $this->prop("customer_relation.buyer_contact_person") or
		$contact_person_name = $this->prop("customer_relation.buyer_contact_person2") or
		$contact_person_name = $this->prop("customer_relation.buyer_contact_person3") or
		$contact_person_name = $this->prop("customer_relation.buyer.firmajuht.name") or
		$contact_person_name = "";
		;
		return $contact_person_name;
	}

		// @attrib api=1 params=pos
		// @return CL_CRM_COMPANY_CUSTOMER_DATA
			// Customer relation object
		// @errors
			// throws awex_crm_bill_customer customer not defined
			// throws awex_crm_bill_implementor if implementor not defined
	//XXX: praegu pole see veel api osa
	public function load_customer_data()
	{
		if (!$this->prop("customer_relation"))
		{
			throw new awex_crm_bill_customer("Customer not defined");
		}

		try
		{
			$customer_relation_o = obj($this->prop("customer_relation"), array(), crm_company_customer_data_obj::CLID);
			$customer_o = $customer_relation_o->get_buyer();
			$this->set_prop("customer", $customer_o->id());

			// load/reload customer data bill properties
			if ($this->set_crm_settings())
			{
				$this->set_prop("bill_due_date_days", $this->crm_settings->prop("bill_default_due_days"));
				$this->set_prop("overdue_charge", $this->crm_settings->prop("bill_default_overdue_interest"));
			}
			else
			{
				$this->set_prop("bill_due_date_days", crm_settings_obj::DEFAULT_BILL_DUE_DAYS);
				$this->set_prop("overdue_charge", crm_settings_obj::DEFAULT_BILL_OVERDUE_INTEREST);
			}

			if (strlen($customer_relation_o->prop("bill_due_date_days")))
			{
				$this->set_prop("bill_due_date_days", $customer_relation_o->prop("bill_due_date_days"));
			}

			if (strlen($customer_relation_o->prop("bill_penalty_pct")))
			{
				$this->set_prop("overdue_charge", $customer_relation_o->prop("bill_penalty_pct"));
			}

			if (strlen($customer_relation_o->prop("discount")))
			{
				$this->set_prop("disc", $customer_relation_o->prop("discount"));
			}

			// load/reload customer address
			$this->set_prop("customer_name", (string) $customer_o->name());
			$customer_addr = array();
			if ($customer_o->class_id() == CL_CRM_COMPANY)
			{
				$this->set_prop("customer_address", (string) $customer_o->prop("contact.name"));
				$this->set_prop("ctp_text", $this->get_customer_contact_person_name());
				$this->set_customer_address("street", (string) $customer_o->prop("contact.aadress"));
				$this->set_customer_address("city", (string) $customer_o->prop("contact.linn.name"));
				$this->set_customer_address("county", (string) $customer_o->prop("contact.maakond.name"));
				$this->set_customer_address("country", (string) $customer_o->prop("contact.riik.name"));
				$this->set_customer_address("country_en", (string) $customer_o->prop("contact.riik.name_en"));
				$this->set_customer_address("index", (string) $customer_o->prop("contact.postiindeks"));
			}
			else
			{
				$this->set_prop("customer_address", (string) $customer_o->prop("address.name"));
				$this->set_prop("ctp_text", (string) $customer_o->name());
				$this->set_customer_address("street", (string) $customer_o->prop("address.aadress"));
				$this->set_customer_address("city", (string) $customer_o->prop("address.linn.name"));
				$this->set_customer_address("county", (string) $customer_o->prop("address.maakond.name"));
				$this->set_customer_address("country", (string) $customer_o->prop("address.riik.name"));
				$this->set_customer_address("country_en", (string) $customer_o->prop("address.riik.name_en"));
				$this->set_customer_address("index", (string) $customer_o->prop("address.postiindeks"));
			}
		}
		catch (Exception $e)
		{
		}
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
		}
		return $c;
	}

	/**
		@attrib api=1 params=pos obj_save=1
		@param template_oid type=oid
		@returns void
		@errors
			throws awex_obj if template object can't be loaded
			throws awex_crm_bill_tpl if template isn't saved or isn't a template
	**/
	public function load_from_template($template_oid)
	{
		// init
		$template = obj($template_oid, array(), self::CLID);

		if (!$template->prop("is_invoice_template") or !$template->is_saved())
		{
			throw new awex_crm_bill_tpl("Template object '{$template_oid}' isn't a template or isn't saved");
		}

		// load
		$ser = new crm_number_series();
		$this->set_prop("bill_no", $ser->find_series_and_get_next(self::CLID, 0, time()));
		$this->set_name(sprintf(t("Arve nr %s"), $this->prop("bill_no")));
		$this->set_parent($template->parent());
		$this->set_prop("bill_date", time());
		$this->set_prop("comment", $template->prop("comment"));
		$this->set_prop("currency", $template->prop("currency"));
		$this->set_prop("time_spent_desc", $template->prop("time_spent_desc"));
		$this->set_prop("bill_due_date_days", $template->prop("bill_due_date_days"));
		$this->set_prop("bill_recieved", -1);
		$this->set_prop("disc", $template->prop("disc"));
		$this->set_prop("language", $template->prop("language"));
		$this->set_prop("impl", $template->prop("impl"));
		$this->set_prop("payment_mode", $template->prop("payment_mode"));
		$this->set_prop("language", $template->prop("language"));
		$this->set_prop("rows_different_pages", $template->prop("rows_different_pages"));
		$this->set_prop("is_overdue_bill", $template->prop("is_overdue_bill"));
		$this->set_prop("customer", $template->prop("customer"));
		$this->set_prop("customer_relation", $template->prop("customer_relation"));
		$this->set_prop("customer_name", $template->prop("customer_name"));
		$this->set_prop("customer_address", $template->prop("customer_address"));
		$this->set_prop("ctp_text", $template->prop("ctp_text"));
		$this->set_prop("bill_mail_from", $template->prop("bill_mail_from"));
		$this->set_prop("bill_mail_from_name", $template->prop("bill_mail_from_name"));
		$this->set_prop("bill_mail_subj", $template->prop("bill_mail_subj"));
		$this->set_prop("bill_mail_ct", $template->prop("bill_mail_ct"));
		$this->set_prop("disc", $template->prop("disc"));
		$this->set_prop("overdue_charge", $template->prop("overdue_charge"));
		$this->set_prop("bill_text", $template->prop("bill_text"));
		$this->set_prop("bill_appendix_comment", $template->prop("bill_appendix_comment"));
		$this->set_prop("reminder_text", $template->prop("reminder_text"));
		$this->set_meta("bill_receivers", safe_array($template->meta("bill_receivers")));
		$this->set_meta("customer_addr", safe_array($template->meta("customer_addr")));

		if (aw_ini_get("user_interface.content_trans"))
		{
			$this->set_meta("translations", safe_array($template->meta("translations")));
			$languages_in_use = languages::list_translate_targets();

			if($languages_in_use->count())
			{
				$o = $languages_in_use->begin();

				do
				{
					$lang_id = $o->prop("aw_lang_id");
					$this->set_meta("trans_{$lang_id}_status", $template->meta("trans_{$lang_id}_status"));
				}
				while ($o = $languages_in_use->next());
			}
		}

		$this->save();


		// connections
		foreach($template->connections_from() as $con)
		{
			if($con->prop("reltype") == 5)//FIXME
			{
				$br = $con->to();
				$nbr = new object();
				$nbr->set_name($br->name());
				$nbr->set_class_id(crm_bill_row_obj::CLID);
				$nbr->set_parent($this->id());
				$nbr->set_ord($br->ord());

				foreach($br->properties() as $prop => $val)
				{
					if($nbr->is_property($prop))
					{
						$nbr->set_prop($prop , $val);
					}
				}

				$nbr->set_meta($br->meta());
				$nbr->save();
				$this->connect(array(
					"to" => $nbr->id(),
					"reltype" => $con->prop("reltype")
				));
			}
			else
			{
				$this->connect(array(
					"to" => $con->prop("to"),
					"reltype" => $con->prop("reltype")
				));
			}
		}
	}
}

// A static "constructor":
crm_bill_obj::$customer_address_properties = array(
	"street" => t("T&auml;nav, maja, korter"),
	"index" => t("Postiindeks"),
	"city" => t("Linn"),
	"county" => t("Maakond"),
	"country" => t("Riik"),
	"country_en" => t("Riik inglise keeles")
);


/** Generic bill exception **/
class awex_crm_bill extends awex_crm {}

/** Customer errors **/
class awex_crm_bill_customer extends awex_crm_bill {}

/** Implementor errors **/
class awex_crm_bill_implementor extends awex_crm_bill {}

/** Address errors **/
class awex_crm_bill_address extends awex_crm_bill {}

/** E-mail address errors **/
class awex_crm_bill_email extends awex_crm_bill
{
	public $email;
}

/** E-mail sending errors **/
class awex_crm_bill_send extends awex_crm_bill {}

/** PDF or other files related errors **/
class awex_crm_bill_file extends awex_crm_bill {}

/** status related errors **/
class awex_crm_bill_state extends awex_crm_bill {}

/** invoice template related errors **/
class awex_crm_bill_tpl extends awex_crm_bill {}
