<?php

namespace automatweb;


class crm_company_obj extends _int_object implements crm_customer_interface
{
	const AW_CLID = 129;

	function prop($k)
	{
		if(substr($k, 0, 5) === "fake_" && is_oid($this->id()))
		{
			switch(substr($k, 5))
			{
				case "url":
					return is_oid(parent::prop("url_id")) ? parent::prop("url_id.url") : parent::prop("RELTYPE_URL.url");

				case "email":
					return is_oid(parent::prop("email_id")) ? parent::prop("email_id.mail") : parent::prop("RELTYPE_EMAIL.mail");

				case "skype":
				case "mobile":
				case "fax":
				case "phone":
					return $this->get_prop_phone($k);

				case "address_country":
					return is_oid(parent::prop("contact")) ? parent::prop("contact.riik.name") : parent::prop("RELTYPE_ADDRESS.riik.name");

				case "address_country_relp":
					return is_oid(parent::prop("contact")) ? parent::prop("contact.riik") : parent::prop("RELTYPE_ADDRESS.riik");

				case "address_county":
					return is_oid(parent::prop("contact")) ? parent::prop("contact.maakond.name") : parent::prop("RELTYPE_ADDRESS.maakond.name");

				case "address_county_relp":
					return is_oid(parent::prop("contact")) ? parent::prop("contact.maakond") : parent::prop("RELTYPE_ADDRESS.maakond");

				case "address_city":
					return is_oid(parent::prop("contact")) ? parent::prop("contact.linn.name") : parent::prop("RELTYPE_ADDRESS.linn.name");

				case "address_city_relp":
					return is_oid(parent::prop("contact")) ? parent::prop("contact.linn") : parent::prop("RELTYPE_ADDRESS.linn");

				case "address_postal_code":
					return is_oid(parent::prop("contact")) ? parent::prop("contact.postiindeks") : parent::prop("RELTYPE_ADDRESS.postiindeks");

				case "address_address":
					return is_oid(parent::prop("contact")) ? parent::prop("contact.aadress") : parent::prop("RELTYPE_ADDRESS.aadress");

				case "address_address2":
					return is_oid(parent::prop("contact")) ? parent::prop("contact.aadress2") : parent::prop("RELTYPE_ADDRESS.aadress2");
			}
		}
		return parent::prop($k);
	}

	function get_prop_phone($type, $return_oid = false)
	{
		if($type === "fake_phone" && $this->instance()->can("view", $this->prop("phone_id")))
		{
			return $return_oid ? $this->prop("phone_id") : $this->prop("phone_id.name");
		}
		else
		{
			$args = array(
				"class_id" => CL_CRM_PHONE,
				"CL_CRM_PHONE.RELTYPE_PHONE(CL_CRM_COMPANY).oid" => $this->id(),
				"limit" => 1,
				"type" => new obj_predicate_not(array("mobile", "fax", "skype")),
			);
			if(in_array(substr($type, 5), array_keys(get_instance("crm_phone")->phone_types)))
			{
				$args["type"] = substr($type, 5);
			}
			$ol = new object_list($args);
			$names = $ol->names();
			$name = reset($names);
			return $return_oid ? key($names) : $name;
		}
	}

	function set_prop($name, $value, $set_into_meta = true)
	{
		if($name === "name")
		{
			$value = htmlspecialchars($value);
		}

		if(substr($name, 0, 5) === "fake_")
		{
			switch(substr($name, 5))
			{
				case "url":
					return $this->set_fake_url($value, $set_into_meta);

				case "email":
					return $this->set_fake_email($value, $set_into_meta);

				case "phone":
				case "fax":
				case "mobile":
				case "skype":
					return $this->set_fake_phone($name, $value, $set_into_meta);

				case "address_country":
				case "address_country_relp":
				case "address_county":
				case "address_county_relp":
				case "address_city":
				case "address_city_relp":
				case "address_postal_code":
				case "address_address":
				case "address_address2":
					return $this->set_fake_address_prop($name, $value, $set_into_meta);
			}
		}
		return parent::set_prop($name, $value);
	}

	function set_name($v)
	{
		$v = htmlspecialchars($v);
		return parent::set_name($v);
	}

	public function save($exclusive = false, $previous_state = null)
	{
		if(!is_oid($this->id()))
		{
			parent::save($exclusive, $previous_state);
		}
		$fakes = array(
			"url", "email", "phone", "fax", "mobile", "skype", "address_country", "address_country_relp", "address_county", "address_county_relp", "address_city", "address_city_relp", "address_postal_code", "address_address", "address_address2"
		);
		foreach($fakes as $fake)
		{
			$sim = $this->meta("sim_fake_".$fake);
			if($sim)
			{
				$this->set_meta("sim_fake_".$fake, NULL);
				$this->set_prop("fake_".$fake, $this->meta("tmp_fake_".$fake), false);
				$this->set_meta("tmp_fake_".$fake, NULL);
			}
		}
		return parent::save();
	}

	/** Returns all customer returns to the warehouse
		@attrib api=1
		@return object list
			shop returns object list
	**/
	public function get_warehouse_returns()
	{
		$filter = array(
			"class_id" => CL_SHOP_WAREHOUSE_RETURN,
			"buyer" => $this->id()
		);
		$ol = new object_list($filter);
		return $ol;
	}

	/** Returns all customer sell orders
		@attrib api=1
		@return object list
			orders object list
	**/
	public function get_sell_orders()
	{
		return $this->_get_sell_orders();
	}

	//undone - boolean
	private function _get_sell_orders($arr = array())
	{

		$filter = array(
			"class_id" => CL_SHOP_SELL_ORDER,
			"purchaser" => $this->id()
		);
		$ol = new object_list($filter);
		return $ol;
	}

	/** Returns customer's undone orders
		@attrib api=1
		@return object list
			orders object list
	**/
	public function get_undone_orders()
	{
		$arr = array("undone" => 1);
		return  $this->_get_orders($arr);
	}

	/** Returns customer delivery notes
		@attrib api=1
		@return object list
			delivery note object list
	**/
	public function get_delivery_notes()
	{
		$filter = array(
			"class_id" => CL_SHOP_DELIVERY_NOTE,
			"customer" => $this->id(),
		);
		$ol = new object_list($filter);
		return $ol;
	}


	/** Returns all customer orders
		@attrib api=1
		@return object list
			orders object list
	**/
	public function get_orders()
	{
		return $this->_get_orders();
	}

	//undone - boolean
	private function _get_orders($arr = array())
	{

		$filter = array(
			"class_id" => CL_SHOP_ORDER,
			"orderer_company" => $this->id(),
		);
		$ol = new object_list($filter);
//see ei ole hea, et peab kindlasti ymber tegema, kuid va toodet on igalpool kasutuses, et ei taha hetkel selle muutmisele m6elda
		if($arr["undone"])
		{
			foreach($ol->arr() as $o)
			{
				if($o->meta("order_completed"))
				{
					$ol->remove($o->id());
				}
			}
		}
		return $ol;
	}




	/** Returns customer unpaid bills
		@attrib api=1
		@param states optional type=array
			bill states
		@return object list
			bills object list
	**/
	public function get_unpaid_bills()
	{
		$filter = array("unpaid" => 1);
		return $this->_get_bills($filter);
	}

	/** Returns customer all bills
		@attrib api=1
		@return object list
			bills object list
	**/
	public function get_bills($filter = array())
	{
		return $this->_get_bills($filter);
	}

	//unpaid - bool
	//
	private function _get_bills($arr = array())
	{
		$filter = array(
			"class_id" => CL_CRM_BILL,
			"customer" => $this->id(),
			"site_id" => array(),
			"lang_id" => array(),
		);

		if($arr["unpaid"])
		{
			 $filter["state"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, 1, 2);
		}
		if(is_array($arr["states"]))
		{
			 $filter["state"] = $arr["states"];
		}
		$ol = new object_list($filter);
		return $ol;
	}

	function get_cash_flow($start , $end)
	{
		$filter = array(
			"class_id" => CL_CRM_BILL,
			"customer" => $this->id(),
//			"state" => 2,
			"site_id" => array(),
			"lang_id" => array(),
		);

		if(!$start)
		{
			$start = mktime(0, 0, 0, 0, 0, (date("Y", time())) - 1);
		}

		if ($end > 100)
		{
			$filter["bill_date"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $start, $end);
		}
		else
		{
			$filter["bill_date"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $start);
		}

		$ol = new object_list($filter);
//		return $ol;

		$bill_i = get_instance(CL_CRM_BILL);
		$co_stat_inst = get_instance("applications/crm/crm_company_stats_impl");
		$company_curr = $co_stat_inst->get_company_currency();

		foreach($ol->arr() as $bill)
		{
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
			$sum+= $cursum;
		}

		return number_format($sum , 2);
	}

	// Since the crm_company object is sometimes handled as school...
	function get_students($arr)
	{
		$ret = new object_list;

		// Student is connected to the school via education object.
		$cs = connection::find(array(
			"from" => array(),
			"to" => $arr["id"],
			"type" => "RELTYPE_SCHOOL",
			"from.class_id" => CL_CRM_PERSON_EDUCATION,
		));
		if(count($cs) > 0)
		{
			$schids = array();
			foreach($cs as $c)
			{
				$schids[] = $c["from"];
			}
			$cs = connection::find(array(
				"from" => array(),
				"to" => $schids,
				"from.class_id" => CL_CRM_PERSON,
			));
			foreach($cs as $c)
			{
				$ret->add($c["from"]);
			}
		}

		return $ret;
	}

	function get_educations($arr)
	{
		$ids = isset($arr["id"]) ? $arr["id"] : parent::id();
		$prms = array(
			"class_id" => CL_CRM_PERSON_EDUCATION,
			"lang_id" => array(),
			"site_id" => array(),
			"CL_CRM_PERSON_EDUCATION.RELTYPE_SCHOOL" => $ids
		);
		$prms = array_merge($prms, $arr["prms"]);
		$props = is_array($arr["props"]) ? $arr["props"] : array(
			CL_CRM_PERSON_EDUCATION => array("oid", "name"),
		);

		if($arr["return_as_odl"])
		{
			$r = new object_data_list(
				$prms,
				$props
			);
		}
		else
		{
			$r = new object_list($prms);
		}
		return $r;
	}

	function get_job_offers()
	{
		$r = new object_list;
		foreach($this->connections_to(array("from.class_id" => CL_PERSONNEL_MANAGEMENT_JOB_OFFER, "type" => "RELTYPE_ORG")) as $conn)
		{
			$r->add($conn->prop("from"));
		}
		return $r;
	}


	/** returns people employed by this company
		@attrib api=1
		@param only_active optional type=bool default=true
			if set, returns only active workers
		@param profession optional type=CL_CRM_PROFESSION
			return only people on that profession
		@param section optional type=CL_CRM_SECTION
			return only people in given section
		@returns object_list of CL_CRM_PERSON
		@errors
		throws awex_obj_class when a parameter object class id is not what expected (profession)
	**/
	public function get_employees($only_active = true, object $profession = null, object $section = null)
	{
		if (!$this->is_saved())
		{
			return new object_list();
		}

		$filter = array(
			"class_id" => CL_CRM_PERSON_WORK_RELATION,
			"employer" => $this->id()
		);

		if ($only_active)
		{
			$filter[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					new object_list_filter(array(
						"conditions" => array(
							"end" => new obj_predicate_compare(OBJ_COMP_LESS, 1)
						)
					)),
					new object_list_filter(array(
						"conditions" => array(
							"end" => new obj_predicate_compare(OBJ_COMP_GREATER, time())
						)
					))
				)
			));
		}

		if($profession)
		{
			if (!$profession->is_a(CL_CRM_PROFESSION))
			{
				throw new awex_obj_class("Wrong profession object class " . $profession->class_id());
			}
			$filter["profession"] = $profession->id();
		}

		if($section)
		{
			if (!$section->is_a(CL_CRM_SECTION))
			{
				throw new awex_obj_class("Wrong section object class " . $section->class_id());
			}
			$filter["section"] = $section->id();
		}

		$work_relations_list = new object_data_list(
			$filter,
			array(CL_CRM_PERSON_WORK_RELATION => "employee")
		);

		if ($work_relations_list->count())
		{
			$person_list = new object_list(array("oid" => $work_relations_list->arr()));
		}
		else
		{
			$person_list = new object_list();
		}

		return $person_list;
	}

	/** Returns company work relations
		@attrib api=1 params=name
		@param active optional type=bool
			if set, returns only active employees
		@param employee optional type=oid
			employee in company
		@param profession optional type=oid
			employee profession in company
		@param section optional type=oid
			employee section in company
		@return object list
			employees' work relations object list
	**/
	public function get_work_relations($arr = array())
	{
		$filter = array(
			"class_id" => CL_CRM_PERSON_WORK_RELATION,
			"employer" => $this->id()
		);

		if(!empty($arr["active"]))
		{
			$filter[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					new object_list_filter(array(
						"conditions" => array(
							"end" => new obj_predicate_compare(OBJ_COMP_LESS, 1)
						)
					)),
					new object_list_filter(array(
						"conditions" => array(
							"end" => new obj_predicate_compare(OBJ_COMP_GREATER, time())
						)
					))
				)
			));
		}

		if(!empty($arr["profession"]))
		{
			$filter["profession"] = (int) $arr["profession"];
		}

		if(!empty($arr["section"]))
		{
			$filter["section"] = (int) $arr["section"];
		}

		if(!empty($arr["employee"]))
		{
			$filter["employee"] = (int) $arr["employee"];
		}

		$ol = new object_list($filter);
		return $ol;
	}

	/** Returns company worker selection
		@attrib api=1 params=name
		@param active optional type=bool
			if set, returns only active workers
		@param profession optional type=oid
			worker profession in company
		@return object list
			person object list
	**/
	public function get_worker_selection($arr = array())
	{
		$workers = $this->get_workers($arr);
		$ret = $workers->names();
		asort($ret);
		return $ret;
	}

	/** Returns company workers
		@attrib api=1 params=name
		@param active optional type=bool
			if set, returns only active workers
		@param profession optional type=oid
			worker profession in company
		@param section optional type=oid
			worker section in company
		@returns object_list
			person object list
	**/
	public function get_workers($arr = array())
	{
		$pl = $this->get_employees(
			isset($arr["active"]) ? (bool) $arr["active"] : false,
			empty($arr["profession"]) ? null : obj($arr["profession"]),
			empty($arr["section"]) ? null : obj($arr["section"])
		);
		return $pl;

/// RETAIN DEPRECATED CODE FOR REFERENCE AND RESEARCH
/* 		$rels = $this->get_work_relations($arr);
		$work_relation_ids = $rels->ids();

		if(!sizeof($work_relation_ids))
		{
			$ol =  new object_list();
		}
		else
		{
			$ol =  new object_list();
			$work_relation = $rels->begin();
			do
			{
				$person_connections = $work_relation->connections_to(array(
					"from.class_id" => CL_CRM_PERSON
				));
				$c = count($person_connections);

				if (1 === $c)
				{
					$person_connection = reset($person_connections);
					$person = $person_connection->from();
					$ol->add($person);
				}
				elseif (1 < $c)
				{
					throw new awex_crm_workrel("Too many persons connected to work relation (oid '" . $work_relation->id() . "').");
				}
				else
				{
					throw new awex_crm_workrel("No person corresponds to work relation (oid '" . $work_relation->id() . "').");
				}
			}
			while ($work_relation = $rels->next());
 */
/* much slower than one by one processing through work relation connections
			$filter = array(
				"class_id" => CL_CRM_PERSON,
				"lang_id" => array(),
				"site_id" => array(),
				$filter[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						"CL_CRM_PERSON.RELTYPE_ORG_RELATION" => $work_relation_ids,
						"CL_CRM_PERSON.RELTYPE_PREVIOUS_JOB" => $work_relation_ids,
						"CL_CRM_PERSON.RELTYPE_CURRENT_JOB" => $work_relation_ids
					)
				))
			);
			$ol = new object_list($filter);
 */
/* 		}

		return $ol;
 */	}

	/** Adds worker to company
		@attrib api=1 params=name
		@param worker required type=string/oid
			person name or object id
		@param profession optional type=string/oid
			worker profession in company
		@param section optional type=string/oid
			worker section in company
		@param mail optional type=string
			work e-mail address
		@param phone optional type=string
			work phone
		@param parent optional type=oid
			new person parent
		@return oid
	**/
	public function add_worker_data($arr = array())
	{
		extract($arr);
		if(!$parent)
		{
			$parent = $this->id();
		}
		if(is_oid($worker) && $GLOBALS["object_loader"]->can("view" , $worker))
		{
			$worker = obj($worker);
		}
		else
		{//tegelikult oleks vaja isikukoodi ka vist.. mille j2rgi otsida, et kas tegelt see inimene kuskil 2kki
			$wn = $worker;
			if(!($worker = $this->get_worker_by_name($worker)))
			{
				$worker = new object();
				$worker->set_class_id(CL_CRM_PERSON);
				$worker->set_name($wn);
				$worker->set_parent($parent);
				$worker->save();
			}
		}

		if(!($wrid = $worker->get_work_relation_id(array(
			"company" => $this->id(),
		))))
		{
			$wrid = $worker->add_work_relation(array(
				"org" => $this->id(),
			));
		}

		$wr = obj($wrid);

		if($profession)
		{
			$wr->set_profession($profession);
		}
		if($section)
		{
			$wr->set_section($section);
		}

		if($mail)
		{
			$wr->set_mail($mail);
		}

		if($phone)
		{
			$wr->set_phone($phone);
		}
		return $worker->id();
	}

	private function get_worker_by_name($name)
	{
		$sel = $this->get_worker_selection();
		foreach($sel as $id => $n)
		{
			if($n == $name)
			{
				return obj($id);
			}
		}
		return 0;
	}

	/** Adds new employee
		@attrib api=1 params=name
		@param name optional type=string
			person name
		@param id optional
		@return oid
			person object id
	**/
	function add_employees($data = array())
	{
		if(is_oid($data["id"]))
		{
			$o = obj($data["id"]);
		}
		else
		{
			$o = new object();
			$o->set_name($data["name"] ? $data["name"] : t("(Nimetu)"));
			$o->set_class_id(CL_CRM_PERSON);
			$o->set_parent($this->id());
			$o->save();
		}

		$wr = $this->add_employee(null, $o);
		return $wr->prop("employee");
	}

	/** Adds a new employee, creates a person if none given
		@attrib api=1 params=pos
		@param profession type=CL_CRM_PROFESSION default=null
		@param person type=CL_CRM_PERSON default=null
			Person to add as an employee. If none given, a new person is created
		@returns CL_CRM_PERSON_WORK_RELATION
			Created employment relation object
		@errors
			throws awex_obj_type if given profession or person of wrong type
			throws awex_redundant_instruction if person already is employed in this organization on given profession
	**/
	public function add_employee(object $profession = null, object $person = null)
	{
		if (!$this->is_saved())
		{
			return;
		}

		if ($profession and !$profession->is_a(CL_CRM_PROFESSION))
		{
			throw new awex_obj_type("Given profession (".$profession->id().") of wrong type (".$profession->class_id().") while adding an employee to " . $this->id());
		}

		if ($person)
		{
			if (!$person->is_a(CL_CRM_PERSON))
			{
				throw new awex_obj_type("Given person (".$person->id().") of wrong type (".$person->class_id().") while adding an employee to " . $this->id());
			}

			// to not add employee twice
			// look for existing employment contracts between this organization and person in given profession
			$filter = array(
				"class_id" => CL_CRM_PERSON_WORK_RELATION,
				"employer" => $this->id(),
				"employee" => $person->id()
			);
			// look for any relation if no profession given
			if ($profession)
			{
				$filter["profession"] = $profession->id();
			}

			$list = new object_list($filter);
			if ($list->count())
			{
				throw new awex_redundant_instruction("Person " . $person->id() . " is already employed by organization " . $this->id() . " in profession " . $profession->id());
			}
		}
		else
		{
			$person = new object();
			$person->set_class_id(CL_CRM_PERSON);
			$person->set_parent($this->id());
			$person->set_meta("no_create_user_yet", true);
			$person->save();
		}

		$work_relation = obj(null, array(), CL_CRM_PERSON_WORK_RELATION);
		$work_relation->set_parent($this->id());
		$work_relation->set_prop("employer", $this->id());
		$work_relation->set_prop("employee", $person->id());
		$work_relation->set_prop("start", time());

		if ($profession)
		{
			$work_relation->set_prop("profession", $profession->id());
		}

		try
		{
			// set section if found
			$section_oid = new aw_oid($profession->prop("section"));
			$section = obj($section_oid, array(), CL_CRM_SECTION);
			$work_relation->set_prop("section", $section->id());
		}
		catch (\Exception $e)
		{
		}

		$work_relation->save();
		return $work_relation;
	}

	function get_root_sections()
	{
		$r = new object_list();

		foreach($this->connections_from(array(
			"type" => "RELTYPE_SECTION",
			"sort_by_num" => "to.jrk"
		)) as $conn)
		{
			$r->add($conn->prop("to"));
		}
		return $r;
	}

	function get_sections()
	{
		$r = new object_list();
		foreach($this->connections_from(array("type" => "RELTYPE_SECTION")) as $conn)
		{
			$parent = $conn->to();
			$r->add($parent->get_sections());
		}
		return $r;
	}

	/** Returns a list of task stats types for the company
		@attrib api=1 params=pos

		@returns
			array { type_id => type_desc }

	**/
	function get_activity_stats_types()
	{
		$ol = new object_list($this->connections_from(array("type" => "RELTYPE_ACTIVITY_STATS_TYPE")));
		return $ol->names();
	}

	/** Returns object list of comments for the company.
	@attrib name=get_comments api=1 params=name
	**/
	function get_comments()
	{
		$ol = new object_list();
		$conns = $this->connections_from(array("type" => "RELTYPE_COMMENT"));
		foreach($conns as $conn)
		{
			$ol->add($conn->prop("to"));
		}
		return $ol;
	}

	function get_mail()
	{
		$inst = $this->instance();
		if($inst->can("view" , $this->prop("email_id")))
		{
			$mail = obj($this->prop("email_id"));
		}
		else
		{
			$mail = $this->get_first_obj_by_reltype("RELTYPE_EMAIL");
		}

		if(is_object($mail))
		{
			return $mail->prop("mail");
		}

		return "";
	}

	function get_mails($arr = array("return_as_names" => true))
	{
		extract($arr);
		// $type, $id, $return_as_odl, $return_as_names

		$prms = array(
			"class_id" => CL_ML_MEMBER,
			"status" => array(),
			"parent" => array(),
			"site_id" => array(),
			"lang_id" => array(),
			"CL_ML_MEMBER.RELTYPE_EMAIL(CL_CRM_COMPANY)" => isset($id) ? $id : parent::id(),
		);

		if(isset($return_as_names) && $return_as_names)
		{
			$ret = array();
			$conns = $this->connections_from(array("type" => "RELTYPE_EMAIL"));
			foreach($conns as $conn)
			{
				$ret[]= $conn->prop("to.name");
			}
		}
		elseif(isset($return_as_odl) && $return_as_odl)
		{
			$ret = new object_data_list($prms, array(
				CL_ML_MEMBER => array("oid", "mail"),
			));
		}
		else
		{
			$ret = new object_list($prms);
		}
		return $ret;
	}

	/** returns e-mail address for sending bill
		@attrib api=1
	**/
	public function get_bill_mail()
	{
		$mails = $this->get_mails(array());
		$ret = null;
		foreach($mails->arr() as $mail)
		{
			if($mail->prop("mail"))
			{
				if($mail->prop("contact_type") == 1)
				{
					return $mail->prop("mail");
				}
//				$ret = $mail->prop("mail");
			}
		}

		return $ret;
	}

	/** returns e-mail addresses for sending bill
		@attrib api=1
	**/
	public function get_bill_mails()
	{
		$mails = $this->get_mails(array());
		$ret = array();
		foreach($mails->arr() as $mail)
		{
			if($mail->prop("mail"))
			{
				if($mail->prop("contact_type") == 1)
				{
					$ret[]= $mail->prop("mail");
				}
//				$ret = $mail->prop("mail");
			}
		}

		return $ret;
	}

	public function add_mail($address)
	{
		$mo = new object();
		$mo->set_class_id(CL_ML_MEMBER);
		$mo->set_parent($this->id());
		$mo->set_name($address);
		$mo->set_prop("mail" , $address);
		$mo->save();

		$conns = $this->connections_from(array("type" => "RELTYPE_EMAIL"));
		if(!sizeof($conns))
		{
			$this->set_prop("email_id" , $mo->id());
			$this->save();
		}
		if(is_oid($mo->id()))//mul pole hetkel 6rna aimugi miks see m6nikord tyhja tulemuse annab
		{
			$this->connect(array("to" =>$mo->id(),  "type" => "RELTYPE_EMAIL"));
		}
		return $mo->id();
	}

	public function change_mail($address)
	{
		$conns = $this->connections_from(array("type" => "RELTYPE_EMAIL"));
		foreach($conns as $conn)
		{
			$conn->delete();
		}
		$mid = $this->add_mail($address);
		return $mid;
	}


	public function add_phone($phone)
	{
		$mo = new object();
		$mo->set_class_id(CL_CRM_PHONE);
		$mo->set_parent($this->id());
		$mo->set_name($phone);
		$mo->save();

		$conns = $this->connections_from(array("type" => "RELTYPE_PHONE"));
		if(!sizeof($conns))
		{
			$this->set_prop("phone_id" , $mo->id());
			$this->save();
		}

		$this->connect(array("to" =>$mo->id(),  "type" => "RELTYPE_PHONE"));
		return $mo->id();
	}

	public function change_phone($phone)
	{
		$conns = $this->connections_from(array("type" => "RELTYPE_PHONE"));
		foreach($conns as $conn)
		{
			$conn->delete();
		}
		$mid = $this->add_phone($phone);
		return $mid;
	}


	/** Adds sector
		@attrib api=1 params=name
		@param id optional type=oid
			sector object id
		@param name optional type=string
			sector name
		@param code optional type=string
			sector code
		@param parent optional type=oid
			parent for new sector object
		@return oid
			sector object id
	**/
	public function add_sector($arr)
	{
		$filter = array();
		$filter["class_id"] = CL_CRM_SECTOR;
		$filter["lang_id"] = array();
		$filter["site_id"] = array();
		if($arr["id"])
		{
			$filter["oid"] = $arr["id"];
			$ol = new object_list($filter);
		}
		elseif($arr["code"] || $arr["name"])
		{
			if($arr["code"])
			{
				$filter["kood"] = $arr["code"];
			}
			else
			{
				$filter["name"] = $arr["name"];
			}
			$ol = new object_list($filter);
		}
		else
		{
			$ol = new object_list();
		}
		if(!is_object($o = reset($ol->arr())))
		{
			if(!$arr["parent"])
			{
				$arr["parent"] = $this->id();
			}
			$o = new object();
			$o->set_class_id(CL_CRM_SECTOR);
			$o->set_parent($arr["parent"]);
			$o->set_name($arr["name"]);
			$o->set_prop("kood" , $arr["code"]);
			$o->save();
		}
		$this->connect(array("to" => $o->id(),  "type" => "RELTYPE_TEGEVUSALAD"));
	}

	/** Adds category
		@attrib api=1 params=pos
		@param id optional type=oid
			category object id
	**/
	public function add_category($id)
	{
		$cat = obj($id);
		$cat->connect(array("to" => $this->id(),  "type" => "RELTYPE_CUSTOMER"));
	}

	/** Adds address
		@attrib api=1 params=name
		@param county optional type=string/oid
			county
		@param city optional type=string/oid
			city
		@param address optional type=string
			street/village etc
		@param index optional type=string
		@param parent optional type=oid
			parent for new address object
		@return oid
			address object id
	**/
	public function add_address($arr)
	{
		if(!$arr["parent"])
		{
			$arr["parent"] = $this->id();
		}
		if($arr["use_existing"])
		{
			$o = $this->get_first_obj_by_reltype("RELTYPE_ADDRESS");
		}
		if(!$o)
		{
			$o = new object();
			$o->set_class_id(CL_CRM_ADDRESS);
			$o->set_parent($arr["parent"]);
		}

		$o->set_name($arr["address"]);
		$o->set_prop("aadress" , $arr["address"]);
		$o->set_prop("postiindeks" , $arr["index"]);
		$o->save();

/*		$conns = $this->connections_from(array("type" => "RELTYPE_ADDRESS"));
		if(!sizeof($conns))
		{
			$this->set_prop("address_id" , $o->id());
			$this->save();
		}*/

		$this->connect(array("to" => $o->id(),  "type" => "RELTYPE_ADDRESS"));
		if($arr["county"])
		{
			$o->set_county($arr["county"]);
		}
		if($arr["city"])
		{
			$o->set_city($arr["city"]);
		}
		return $o->id();
	}

	/** Adds address
		@attrib api=1 params=name
		@param county optional type=string/oid
			county
		@param city optional type=string/oid
			city
		@param address optional type=string
			street/village etc
		@param index optional type=string
		@param parent optional type=oid
			parent for new address object
		@return oid
			address object id
	**/
	public function set_address($arr)
	{
		$arr["use_existing"] = 1;
		return $this->add_address($arr);
	}

	public function add_url($address)
	{
		$mo = new object();
		$mo->set_class_id(CL_EXTLINK);
		$mo->set_parent($this->id());
		$mo->set_name($address);
		$mo->set_prop("url" , $address);
		$mo->save();

		$conns = $this->connections_from(array("type" => "RELTYPE_URL"));
		if(!sizeof($conns))
		{
			$this->set_prop("url_id" , $mo->id());
			$this->save();
		}
		$this->connect(array("to" =>$mo->id(), "type" => "RELTYPE_URL"));
		return $mo->id();
	}

	public function change_url($address)
	{
		$conns = $this->connections_from(array("type" => "RELTYPE_URL"));
		foreach($conns as $conn)
		{
			$conn->delete();
		}
		$urlid = $this->add_url($address);
		return $urlid;
	}

	function get_faxes()
	{
		$ret = array();
		$conns = $this->connections_from(array("type" => "RELTYPE_TELEFAX"));
		foreach($conns as $conn)
		{
			$ret[]= $conn->prop("to.name");
		}
		return $ret;
	}

	/** Returns customer's all phone numbers as array
		@attrib api=1 params=pos
	**/
	function get_phones()
	{
		$ret = array();
		$conns = $this->connections_from(array("type" => "RELTYPE_PHONE"));
		foreach($conns as $conn)
		{
			$ret[]= $conn->prop("to.name");
		}
		return $ret;
	}

	/** returns customer relation creator
		@attrib api=1
		@returns string
	**/
	public function get_cust_rel_creator_name()
	{
		$o = $this->get_customer_relation();
		if(is_object($o))
		{
			return $o->prop("cust_contract_creator.name");
		}
		return "";
	}

	/** returns customer relation object
		@attrib api=1 params=pos
		@param my_co type=CL_CRM_COMPANY default=null
			By default current company used
		@param crea_if_not_exists type=bool default=false
			if no customer relation object, makes one
		@returns CL_CRM_COMPANY_CUSTOMER_DATA
	**/
	public function get_customer_relation($my_co = null, $crea_if_not_exists = false)
	{
		if ($my_co === null)
		{
			$my_co = get_current_company();
		}

		if (!is_object($my_co) || !is_oid($my_co->id()))
		{
			throw new awex_crm("Current company not defined");
		}

		if ($this->id() == $my_co->id())
		{
			throw new awex_crm("Company can't be its own customer");
		}

		static $gcr_cache;
		if (!is_array($gcr_cache))
		{
			$gcr_cache = array();
		}

		if (isset($gcr_cache[$this->id()][$crea_if_not_exists][$my_co->id()]))
		{
			return $gcr_cache[$this->id()][$crea_if_not_exists][$my_co->id()];
		}

		$ol = new object_list(array(
			"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
			"buyer" => $this->id(),
			"seller" => $my_co->id()
		));

		if ($ol->count())
		{
			$gcr_cache[$this->id()][$crea_if_not_exists][$my_co->id()] = $ol->begin();
			return $ol->begin();
		}
		elseif ($crea_if_not_exists)
		{
			$o = obj();
			$o->set_class_id(CL_CRM_COMPANY_CUSTOMER_DATA);
			$o->set_name(t("Kliendisuhe ").$my_co->name()." => ".$this->name());
			$o->set_parent($my_co->id());
			$o->set_prop("seller", $my_co->id());
			$o->set_prop("buyer", $this->id());
			$o->save();
			$gcr_cache[$this->id()][$crea_if_not_exists][$this->id()] = $o;
			return $o;
		}
	}

	function faculties($arr = array())
	{
		$r = $this->get_sections();
		if(isset($arr["return_as_odl"]) && $arr["return_as_odl"])
		{
			$ids = $r->count() > 0 ? $r->ids() : -1;
			$arr["props"] = isset($arr["props"]) && is_array($arr["props"]) ? $arr["props"] : array(
				CL_CRM_SECTION => array("oid", "name")
			);
			$r = new object_data_list(
				array(
					"class_id" => CL_CRM_SECTION,
					"oid" => $ids,
					"site_id" => array(),
					"lang_id" => array(),
				),
				$arr["props"]
			);
		}
		return $r;
	}

	/** returns customer relation object
		@attrib api=1 params=pos
		@param form optional
			form oid or name
		@returns object
	**/
	public function set_legal_form($form)
	{
		if(!$form)
		{
			return false;
		}
		if(is_oid($form))
		{
			$form_id = $form;
		}
		else
		{
			$ol = new object_list(array(
				"class_id" => CL_CRM_CORPFORM,
				"site_id" => array(),
				"lang_id" => array(),
				"name" => $form,
			));

			if($ol->count())
			{
				$form_id = reset($ol->ids());
			}
			else
			{
				$o = new object();
				$o->set_class_id(CL_CRM_CORPFORM);
				$o->set_parent($this->parent());
				$o->set_name($form);
				$o->save();
				$form_id = $o->id();
			}
		}

		$this->set_prop("ettevotlusvorm" , $form_id);
		$this->save();
		return $form_id;
	}

	/** sets the default email adress content or creates it if needed **/
	private function set_fake_email($mail, $set_into_meta = true)
	{
		if($set_into_meta === true)
		{
			$this->set_meta("tmp_fake_email", $mail);
			$this->set_meta("sim_fake_email", 1);
		}
		else
		{
			if($GLOBALS["object_loader"]->cache->can("view", $this->prop("email_id")))
			{
				$eo = obj($this->prop("email_id"));
			}
			else
			{
				$eo = $this->get_first_obj_by_reltype("RELTYPE_EMAIL");
				if($eo === false)
				{
					$eo = obj();
					$eo->set_class_id(CL_ML_MEMBER);
					$eo->set_parent($this->id());
					$eo->set_prop("name", $this->name());
				}
			}
			$conns = connection::find(array("from" => $this->id(), "to" => $eo->id(), "from.class_id" => CL_CRM_COMPANY, "reltype" => "RELTYPE_EMAIL"));
			if(count($conns) > 0)
			{
				$eo->conn_id = reset(array_keys($conns));
			}

			$eo->set_prop("mail", $mail);
			aw_disable_acl();
			$eo->save();
			aw_restore_acl();

			$this->set_prop("email_id", $eo->id());
			$this->save();
			$this->connect(array(
				"type" => "RELTYPE_EMAIL",
				"to" => $eo->id()
			));
		}
	}

	/** sets the default email adress content or creates it if needed **/
	private function set_fake_phone($type, $phone, $set_into_meta = true)
	{
		if($set_into_meta === true)
		{
			$this->set_meta("tmp_".$type, $phone);
			$this->set_meta("sim_".$type, 1);
		}
		else
		{
			if(!is_oid($this->id()))
			{
				$this->save();
			}
			$id = $this->get_prop_phone($type, true);
			if ($GLOBALS["object_loader"]->cache->can("view", $id))
			{
				$eo = obj($id);
			}
			else
			{
				$eo = obj();
				$eo->set_class_id(CL_CRM_PHONE);
				$eo->set_parent($this->id());
			}
			$conns = connection::find(array("from" => $this->id(), "to" => $eo->id(), "from.class_id" => CL_CRM_COMPANY, "reltype" => "RELTYPE_PHONE"));
			if(count($conns) > 0)
			{
				$eo->conn_id = reset(array_keys($conns));
			}
			$type = in_array(substr($type, 5), array_keys(get_instance("crm_phone")->phone_types)) ? substr($type, 5) : "work";
			$eo->set_prop("type", $type);
			$eo->set_name($phone);
			aw_disable_acl();
			$eo->save();
			aw_restore_acl();

			if($type === "fake_phone")
			{
				$this->set_prop("phone_id", $eo->id());
			}
			$this->save();
			$this->connect(array(
				"type" => "RELTYPE_PHONE",
				"to" => $eo->id()
			));
		}
	}

	private function set_fake_url($url, $set_into_meta = true)
	{
		if($set_into_meta === true)
		{
			$this->set_meta("tmp_fake_url", $url);
			$this->set_meta("sim_fake_url", 1);
		}
		else
		{
			if ($GLOBALS["object_loader"]->cache->can("view", $this->prop("url_id")))
			{
				$eo = obj($this->prop("url_id"));
			}
			else
			{
				$eo = $this->get_first_obj_by_reltype("RELTYPE_URL");
				if($eo === false)
				{
					$eo = obj();
					$eo->set_class_id(CL_EXTLINK);
					$eo->set_parent($this->id());
				}
			}

			$eo->set_prop("url", $url);
			$eo->save();

			$this->set_prop("url_id", $eo->id());
			$this->save();
			$this->connect(array(
				"type" => "RELTYPE_URL",
				"to" => $eo->id()
			));
		}
	}

	private function set_fake_address_prop($k, $v, $set_into_meta = true)
	{
		if($set_into_meta === true)
		{
			$this->set_meta("tmp_".$k, $v);
			$this->set_meta("sim_".$k, 1);
		}
		else
		{
			$pmap = array(
				"fake_address_country" => "riik",
				"fake_address_country_relp" => "riik",
				"fake_address_county" => "maakond",
				"fake_address_county_relp" => "maakond",
				"fake_address_city" => "linn",
				"fake_address_city_relp" => "linn",
				"fake_address_postal_code" => "postiindeks",
				"fake_address_address" => "aadress",
				"fake_address_address2" => "aadress2"
			);

			if ($GLOBALS["object_loader"]->cache->can("view", $this->prop("contact")))
			{
				$eo = obj($this->prop("contact"));
			}
			else
			{
				$eo = obj();
				$eo->set_class_id(CL_CRM_ADDRESS);
				$eo->set_parent($this->id());
			}

			switch($k)
			{
				case "fake_address_county":
				case "fake_address_city":
				case "fake_address_country":
					if(strlen(trim($v)) > 0)
					{
						$this->_adr_set_via_rel($eo, $pmap[$k], $v);
					}
					else
					{
						$eo->set_prop($pmap[$k], 0);
					}
					break;

				case "fake_address_county_relp":
					$v = is_oid($v) ? $v : 0;
					if ($v)
					{
						$o = obj($v, array(), CL_CRM_COUNTY);
						$eo->connect(array("to" => $o, "type" => "RELTYPE_MAAKOND"));
					}
					$eo->set_prop($pmap[$k], $v);
					break;

				case "fake_address_city_relp":
					$v = is_oid($v) ? $v : 0;
					if ($v)
					{
						$o = obj($v, array(), CL_CRM_CITY);
						$eo->connect(array("to" => $o, "type" => "RELTYPE_LINN"));
					}
					$eo->set_prop($pmap[$k], $v);
					break;

				case "fake_address_country_relp":
					$v = is_oid($v) ? $v : 0;
					if ($v)
					{
						$o = obj($v, array(), CL_CRM_COUNTRY);
						$eo->connect(array("to" => $o, "type" => "RELTYPE_RIIK"));
					}
					$eo->set_prop($pmap[$k], $v);
					break;

				case "fake_address_postal_code":
				case "fake_address_address":
				case "fake_address_address2":
					$eo->set_prop($pmap[$k], $v);
					break;
			}

			$eo->save();

			$this->set_prop("contact", $eo->id());
			$this->save();
		}
	}

	private function _adr_set_via_rel($o, $prop, $val)
	{
		$pl = $o->get_property_list();
		$rl = $o->get_relinfo();

		$ol = new object_list(array(
			"class_id" => $rl[$pl[$prop]["reltype"]]["clid"][0],
			"name" => $val,
			"limit" => 1,
		));
		if ($ol->count() > 0)
		{
			$ro = $ol->begin();
		}
		else
		{
			$ro = obj();
			$ro->set_class_id($rl[$pl[$prop]["reltype"]]["clid"][0]);
			$ro->set_parent(is_oid($o->id()) ? $o->id() : $o->parent());
			$ro->set_name($val);
			$ro->save();
		}

		$o->set_prop($prop, $ro->id());
	}

	/**
		@attrib api=1
		@returns Object list of all customers by customer data objects.
	**/
	public function get_customers_by_customer_data_objs()
	{
		$ol = new object_list(array(
			"class_id" => CL_CRM_COMPANY,
			"lang_id" => array(),
			"site_id" => array(),
			"CL_CRM_COMPANY.RELTYPE_BUYER(CL_CRM_COMPANY_CUSTOMER_DATA).seller" => $this->id(),
			"CL_CRM_COMPANY.RELTYPE_BUYER(CL_CRM_COMPANY_CUSTOMER_DATA).buyer" => new obj_predicate_prop("id"),
		));
		return $ol;
	}


	/** goes through all the relations and returns a set of id
		@attrib api=1
		@returns array
	**/
	public function get_customers_for_company()
	{
		$data = array();
		$impl = array();
		$impl = $this->get_workers()->ids();
		$impl[] = $this->id();
		// also, add all orderers from projects where the company is implementor
		$ol = new object_list(array(
			"class_id" => CL_PROJECT,
			"CL_PROJECT.RELTYPE_IMPLEMENTOR" => $impl,
			"lang_id" => array(),
			"site_id" => array()
		));
		foreach($ol->arr() as $o)
		{
			foreach($o->get_customer_ids() as $ord)
			{
				if ($ord)
				{
					$data[$ord] = $ord;
				}
			}
		}

		$conns = $this->connections_from(array(
			"type" => "RELTYPE_CUSTOMER",
		));
		foreach($conns as $conn)
		{
			$data[$conn->prop('to')] = $conn->prop('to');
		}
		return $data;
	}

	/** returns all projects where company is customer
		@attrib api=1
		@returns object list
	**/
	public function get_projects_as_customer($arr = array())
	{
		$ol = new object_list(array(
			"class_id" => CL_PROJECT,
			"CL_PROJECT.RELTYPE_ORDERER" => $this->id(),
			"lang_id" => array(),
			"site_id" => array()
		));
		return $ol;
	}

	/** adds new project and sets company as customer
		@attrib api=1 params=pos
		@returns oid
	**/
	public function add_project_as_customer($name, $impl = null)
	{
		if(!$impl)
		{
			$co = get_current_company();
			$impl = $co->id();
		}
		$o = new object();
		$o->set_parent($this->id());
		$o->set_class_id(CL_PROJECT);
		$o->set_name($name);
		$o->save();
		$o->connect(array(
			"to" => $this->id(),
			"type" => "RELTYPE_ORDERER"
		));
		$o->set_prop("implementor" , $impl);
		$o->save();
		return $o->id();
	}

	/** adds customer to company
		@attrib api=1 params=pos
		@param customer optional type=oid/string
			form oid
		@returns oid
			customer object id
	**/
	public function add_customer($customer)
	{
		if(is_oid($customer))
		{
			$ol = new object_list(array(
				"class_id" => CL_CRM_COMPANY,
				"oid" => $customer,
				"lang_id" => array(),
				"site_id" => array()
			));
		}
		else
		{
			$ol = new object_list(array(
				"class_id" => CL_PROJECT,
				"name" => $customer,
				"lang_id" => array(),
				"site_id" => array()
			));
		}

		$co = reset($ol->arr());
		if(!$co)
		{
			if(is_oid($co))
			{
				return null;
			}
			$co = new object();
			$co->set_parent($this->id());
			$co->set_class_id(CL_CRM_COMPANY);
			$co->set_name($customer);
			$co->save();
		}

		$rel = $co->get_customer_relation(null, true);
		return $co->id();
	}

	/** returns company section, adds if none exists
		@attrib api=1 params=pos
		@param section optional type=string
			form oid
		@returns oid
			customer object id
	**/
	public function get_section_by_name($section)
	{
		$units = $this->get_sections();
		foreach($units->names() as $key => $name)
		{
			if($name == $section)
			{
				return $key;
			}
		}
		$customer_unit = $this->add_section($section);

		return $customer_unit;
	}

	/** adds new section to company
		@attrib api=1 params=pos
		@param section optional type=string
			form oid
		@returns oid
			customer object id
	**/
	public function add_section($section)
	{
		$sectiono = new object();
		$sectiono -> set_parent($this->id());
		$sectiono -> set_class_id(CL_CRM_SECTION);
		$sectiono -> set_name($section);
		$sectiono -> save();

		$this->connect(array(
			"to" => $sectiono->id(),
			"type" => "RELTYPE_SECTION"
		));
		return $sectiono -> id();
	}

	public function get_customer_prop($co , $prop)
	{
		if($co)
		{

			$o = obj($co);
			$rel = $o->get_customer_relation($this);
			if(!is_object($rel))
			{
				return;
			}
			return $rel->prop($prop);
		}
		else return "";

	}

	public function set_customer_prop($co , $prop,$value = null)
	{
		if($value != null)
		{

			$o = obj($co);
			$rel = $o->get_customer_relation($this , 1);
			$ret = $rel->set_prop($prop , $value);
			$rel->save();
			return $ret;
		}
		else return "";

	}

	/** Returns default address as a string
		@attrib api=1
	**/
	public function get_address_string()
	{
		$address_str = "";
		$address = $this->get_first_obj_by_reltype("RELTYPE_ADDRESS_ALT");

		if ($address)
		{
			$address_str = $address->name();
		}
		else
		{
			$address_id = parent::prop("contact");
			if ($GLOBALS["object_loader"]->cache->can("view", $address_id))
			{
				$address_str = obj($address_id)->name();
			}
		}
		return $address_str;
	}

	/** Returns object list of professions defined in this company
		@attrib api=1
		@returns object_list
	**/
	public function get_company_professions()
	{
		$sections = new object_list($this->connections_from(array(
			"type"=> "RELTYPE_SECTION"
		)));
		$professions = new object_list($this->connections_from(array(
			"type"=> "RELTYPE_PROFESSIONS"
		)));
		foreach ($sections->arr() as $section)
		{
			$professions->add(new object_list($section->connections_from(array(
				"type"=> "RELTYPE_PROFESSIONS"
			))));
		}
		return $professions;
	}

	public function get_all_customer_ids($arr = array())
	{
		$filter = array(
			"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
			"lang_id" => array(),
			"site_id" => array(),
			"seller" => $this->id(),
		);
		if($arr["name"])
		{
			$filter["buyer.name"] = $arr["name"]."%";
		}

		$t = new object_data_list(
			$filter,
			array(
				CL_CRM_COMPANY_CUSTOMER_DATA => array(
					"buyer"
				)
			)
		);

		$customers = $t->get_element_from_all("buyer");
		return $customers;
	}

	public function get_all_org_customer_categories($obj = NULL)
	{
		static $retval = array();
		if($obj === NULL)
		{
			$obj = $this;
		}

		$conns = $obj->connections_from(array(
			"type" => "RELTYPE_CATEGORY",
		));

		foreach($conns as $conn)
		{
			$retval[$conn->prop("to")] = $conn->prop("to");
			$obj = $conn->to();
			$this->get_all_org_customer_categories($obj);
		}
		return $retval;
	}

	/**
		@attrib params=pos
		@param root optional type=int acl=view
			The OID from which to start the customer categories hierarchy
		@param depth optional type=int
			The maximum depth of the customer categories hierarchy. If not set entire hierarchy will be returned.
	**/
	public function get_customer_categories_hierarchy($root = NULL, $depth = NULL)
	{
		$retval = array();
		$o = is_oid($root) && $GLOBALS["object_loader"]->can("view", $root) ? obj($root) : $this;
		foreach($o->connections_from(array("type" => "RELTYPE_CATEGORY")) as $conn)
		{
			$retval[$conn->prop("to")] = $depth === NULL || $depth > 1 ? $this->get_customer_categories_hierarchy($conn->prop("to"), $depth -1) : array();
		}
		return $retval;
	}
}


/** Generic CRM application exception **/
class awex_crm extends awex_obj {}

/** customer relation related error **/
class awex_crm_custrel extends awex_crm {}

/** work relation related error **/
class awex_crm_workrel extends awex_crm {}

?>
