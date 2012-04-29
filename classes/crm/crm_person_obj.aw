<?php

class crm_person_obj extends _int_object implements crm_customer_interface, crm_sales_price_component_interface, crm_offer_row_interface
{
	const CLID = 145;

	const GENDER_MALE = 1;
	const GENDER_FEMALE = 2;

	protected $all_jobs;
	protected $current_jobs;
	private static $company_id_cache;
	private static $gender_options = array();
	private static $_email_type_lut = array(
		"" => null,
		"all" => null,
		"general" => ml_member_obj::TYPE_GENERIC,
		"invoice" => ml_member_obj::TYPE_INVOICE
	);


	//	Written solely for testing purposes!
	public function get_units()
	{
		$ol = new object_list(array(
			"class_id" => CL_UNIT,
			"status" => object::STAT_ACTIVE,
		));
		return $ol;
	}

	/**
		@attrib api=1 params=pos
		@param value type=int
			If specified, option name for that value is returned
		@comment
			If $value is not among valid options, empty string is returned
		@returns array|string
			All options or the one corresponding to $value
		@errors none
	**/
	public static function gender_options($value = null)
	{
		if (empty(self::$gender_options))
		{
			self::$gender_options = array(
				self::GENDER_MALE => t("Mees"),
				self::GENDER_FEMALE => t("Naine")
			);
		}

		if (null === $value)
		{
			$r = self::$gender_options;
		}
		elseif (isset(self::$gender_options[$value]))
		{
			$r = self::$gender_options[$value];
		}
		else
		{
			$r = "";
		}

		return $r;
	}

	public function awobj_set_is_quickmessenger_enabled($value)
	{
		if (1 === $value and 1 === $this->prop("is_quickmessenger_enabled"))
		{
			// delete old box and its messages
		}
		elseif (1 === $value and 0 === $this->prop("is_quickmessenger_enabled"))
		{
			// create&connect box
		}
	}

	public function awobj_get_username()
	{
		$user = $this->get_user();
		return $user->prop("uid");
	}

	/** Returns login user for person or NULL if not found
		@attrib api=1 params=pos
		@returns CL_USER|NULL
	**/
	public function get_user()
	{
		$this->require_state("saved");

		$users = new object_list(array(
			"class_id" => CL_USER,
			"person" => $this->brother_of()
		));

		if ($users->count())
		{
			$user = $users->begin()->get_original();
			$oids = array_unique($users->brother_ofs());
			if (1 !== count($oids))
			{
				trigger_error(sprintf("Person %s has more than one user (%s)", $this->brother_of(), implode(", ", $oids)), E_USER_WARNING);
			}
		}
		else
		{
			$user = null;
		}

		return $user;
	}

	/** Sets login user for person
		@attrib api=1 params=pos
		@param user type=CL_USER
		@returns void
		@errors
			throws awex_obj_state_new if this object not saved
			throws awex_obj_type if $user of wrong class
	**/
	public function set_user(object $user)
	{
		$this->require_state("saved");

		if (CL_USER != $user->class_id())
		{
			throw new awex_obj_type(sprintf("Invalid type (%s) user object (%s) given for person (%s)"), $user->class_id(), $user->id(), $this->id());
		}

		if (!$user->is_saved())
		{
			throw new awex_obj_state_new(sprintf("Unsaved user object (%s) given for person (%s)"), $user->id(), $this->id());
		}

		$user->set_prop("person", $this->id());
		$user->save();
		cache::file_clear_pt("storage_object_data");
		cache::file_clear_pt("storage_search");
		cache::file_clear_pt("acl");
	}

	// a DEPRECATED and potentially harmful method.
	function set_rank($v)
	{
		throw new Exception("crm_person_obj::set_rank() may not be used");

		// It won't work with new object, so we need to save it first.
		if(!is_oid($this->id())) { 	$this->save(); } $o = obj($this->id()); $org_rel = $o->get_first_obj_by_reltype("RELTYPE_CURRENT_JOB"); if (!$org_rel) {$org_rel = obj(); $org_rel->set_class_id(CL_CRM_PERSON_WORK_RELATION); $org_rel->set_parent($o->id()); $org_rel->save(); $o->connect(array( "to" => $org_rel->id(), "type" => "RELTYPE_CURRENT_JOB", )); } $sp = $org_rel->set_prop("profession", $v); $org_rel->save(); return $sp;
	}

	// DEPRECATED!
	function get_rank($org = null)
	{
		// It won't work with new object, so we need to check the oid.
		if(!is_oid($this->id())) return false;

		$rank = null;
		$this->set_current_jobs();
		foreach($this->current_jobs->arr() as $current_job)
		{
 			$rank = $current_job->prop("profession");
			if(!$org || $org == $current_job->prop("employer"))
			{
				break;
			}
		}
		return $rank;
	}


	/** returns person profession selection. List of professions in $co/$units currently held by this person
		@attrib api=1
		@param co required type=oid
			company id
		@param units optional type=array
			unit object ids
		@returns array
			profession id => name pairs
	**/
	public function get_profession_selection($co, $units = array())
	{
		$this->set_current_jobs();
		$professions = array();
		foreach($this->current_jobs->arr() as $o)
		{
			if(sizeof($units) && !in_array($o->prop("unit") , $units))//kui pole ette antud yksustes j2tab vahele
			{
				continue;
			}
			if((!$co || $co == $o->prop("employer")) && $o->prop("profession.name"))
			{
				$professions[$o->prop("profession")] = $o->prop("profession.name");
			}
		}
		return $professions;
	}

	/** Returns list of professions for this person's work relations
		@attrib api=1 params=pos
		@param organization type=CL_CRM_COMPANY default=NULL
		@returns object_list
		@errors
			throws awex_obj_type when organization parameter object is not of correct type
	**/
	public function get_professions(object $organization = null)
	{
		return crm_person_work_relation_obj::find_professions($this->ref(), $organization);
	}

	function set_name($v)
	{
		$v = htmlspecialchars($v);
		return parent::set_name($v);
	}

	function set_prop($k, $v, $set_into_meta = true)
	{
		$html_allowed = array();
		if(!in_array($k, $html_allowed) && !is_array($v))
		{
			$v = htmlspecialchars($v);
		}

		switch($k)
		{
			case "rank":
				return $this->set_rank($v);

			case "work_contact":
				return $this->set_work_contact($v);

			case "org_section":
				return $this->set_org_section($v);

			case "fake_email":
				return $this->set_fake_email($v, $set_into_meta);

			case "fake_phone":
			case "fake_skype":
			case "fake_mobile":
			case "fake_fax":
				return $this->set_fake_phone($k, $v, $set_into_meta);

			case "fake_address_country":
			case "fake_address_county":
			case "fake_address_city":
			case "fake_address_country_relp":
			case "fake_address_county_relp":
			case "fake_address_city_relp":
			case "fake_address_postal_code":
			case "fake_address_address":
			case "fake_address_address2":
				return $this->set_fake_address_prop($k, $v, $set_into_meta);
		}
		return parent::set_prop($k, $v);
	}

	function prop($k)
	{
		switch($k)
		{
			case "birthday":
				$val = parent::prop("birth_date");
				if($val > 0)
				{
					return date("Y-m-d" , $val);
				}

				return parent::prop("birthday");

			case "birth_date":
				$val = parent::prop("birth_date");
				if($val > 0)
				{
					return $val;
				}
				elseif(parent::prop("birthday") && parent::prop("birthday") != -1)
				{
					$bd_tm = explode("-", parent::prop("birthday"));
					$bd_tm = mktime(0, 0, 0, $bd_tm[1], $bd_tm[2], $bd_tm[0]);
					return $bd_tm;
				}
				return parent::prop($k);
			case "fake_email":
				return parent::prop("email.mail");

			case "fake_phone":
			case "fake_skype":
			case "fake_mobile":
			case "fake_fax":
				return $this->get_prop_phone($k);
				return parent::prop("phone.name");

			case "fake_address_country":
				return parent::prop("address.riik.name");

			case "fake_address_county":
				return parent::prop("address.maakond.name");

			case "fake_address_city":
				return parent::prop("address.linn.name");

			case "fake_address_country_relp":
				return parent::prop("address.riik");

			case "fake_address_county_relp":
				return parent::prop("address.maakond");

			case "fake_address_city_relp":
				return parent::prop("address.linn");

			case "fake_address_postal_code":
				return parent::prop("address.postiindeks");

			case "fake_address_address":
				return parent::prop("address.aadress");

			case "fake_address_address2":
				return parent::prop("address.aadress2");

			case "work_contact":
				return $this->find_work_contact();

			case "rank":
				return $this->get_rank();

			case "org_section":
				return $this->get_org_section();
		}

		if($k === "title" && parent::prop($k) === 0)
		{
			return 3;
		}
		return parent::prop($k);
	}

	function get_prop_phone($type, $return_oid = false)
	{
		if($type === "fake_phone" && object_loader::can("", $this->prop("phone")))
		{
			return $return_oid ? $this->prop("phone") : $this->prop("phone.name");
		}
		elseif($type === "fake_fax")
		{
			$args = array(
				"class_id" => CL_CRM_PHONE,
				"CL_CRM_PHONE.RELTYPE_FAX(CL_CRM_PERSON).oid" => $this->id(),
				"limit" => 1,
				"type" => "fax",
			);
		}
		else
		{
			$args = array(
				"class_id" => CL_CRM_PHONE,
				"CL_CRM_PHONE.RELTYPE_PHONE(CL_CRM_PERSON).oid" => $this->id(),
				"limit" => 1,
				"type" => new obj_predicate_not(array("mobile", "fax", "skype")),
			);
			if(in_array(substr($type, 5), array_keys(get_instance("crm_phone")->phone_types)))
			{
				$args["type"] = substr($type, 5);
			}
		}
		$ol = new object_list($args);
		$names = $ol->names();
		$name = reset($names);
		return $return_oid ? key($names) : $name;
	}

	function find_work_contact()
	{
		$this->set_current_jobs();
		$org_rel = $this->current_jobs->begin();
		if (!$org_rel)
		{
			return false;
		}
		return $org_rel->prop("employer");
	}

	// a DEPRECATED and potentially harmful method
	function set_work_contact($v)
	{
		throw new Exception("crm_person_obj::set_work_contact() may not be used");
		/*
		// It won't work with new object, so we need to save it first.
		if(!is_oid($this->id()))
		{
			$this->save();
		}

		$o = obj($this->id());
		$org_rel = $o->get_first_obj_by_reltype("RELTYPE_CURRENT_JOB");
		if (!$org_rel)
		{
			$org_rel = obj();
			$org_rel->set_class_id(CL_CRM_PERSON_WORK_RELATION);
			$org_rel->set_parent($o->id());
			$org_rel->save();
			$o->connect(array(
				"to" => $org_rel->id(),
				"type" => "RELTYPE_CURRENT_JOB",
			));
		}
		$sp = $org_rel->set_prop("org", $v);
		$org_rel->save();
		return $sp;
		*/
	}

	// a DEPRECATED and potentially harmful method
	function set_org_section($v)
	{
		throw new Exception("crm_person_obj::set_org_section() may not be used");

		/*
		// It won't work with new object, so we need to save it first.
		if(!is_oid($this->id()))
		{
			$this->save();
		}

		$o = obj($this->id());
		$org_rel = $o->get_first_obj_by_reltype("RELTYPE_CURRENT_JOB");
		if (!$org_rel)
		{
			$org_rel = obj();
			$org_rel->set_class_id(CL_CRM_PERSON_WORK_RELATION);
			$org_rel->set_parent($o->id());
			$org_rel->save();
			$o->connect(array(
				"to" => $org_rel->id(),
				"type" => "RELTYPE_CURRENT_JOB",
			));
		}
		$sp = $org_rel->set_prop("company_section", $v);
		$org_rel->save();
		return $sp;
		*/
	}

	function get_org_section()
	{
		$this->set_current_jobs();
		$org_rel = $this->current_jobs->begin();
		if (!$org_rel)
		{
			return false;
		}
		return $org_rel->prop("company_section");
	}

	function add_person_to_list($arr)
	{
		$o = obj($arr["id"]);
		$o->connect(array(
			"to" => $arr["list_id"],
			"reltype" => "RELTYPE_CATEGORY",
		));
	}

	function get_applications($arr = array())
	{
		$this->prms($arr);

		/*
		// Gimme a reason why this won't work!?
		return new object_list(array(
			"class_id" => CL_PERSONNEL_MANAGEMENT_JOB_OFFER,
			"parent" => $arr["parent"],
			"status" => $arr["status"],
			"CL_PERSONNEL_MANAGEMENT_JOB_OFFER.RELTYPE_CANDIDATE.RELTYPE_PERSON" => parent::id(),
		));
		*/
		$ret = new object_list();

		$conns = connection::find(array(
			"to" => parent::id(),
			"from.class_id" => CL_PERSONNEL_MANAGEMENT_CANDIDATE,
			"type" => "RELTYPE_PERSON"
		));
		foreach($conns as $conn)
		{
			$ids[] = $conn["from"];
		}

		if(count($ids) == 0)
		{
			return $ret;
		}

		$conns = connection::find(array(
			"to" => $ids,
			"from.class_id" => CL_PERSONNEL_MANAGEMENT_JOB_OFFER,
			"type" => "RELTYPE_CANDIDATE"
		));

		$pm = get_instance(CL_PERSONNEL_MANAGEMENT);
		foreach($conns as $conn)
		{
			$from = obj($conn["from"]);
			if((in_array($conn["from.status"], $arr["status"]) || count($arr["status"]) == 0) && (in_array($conn["from.parent"], $arr["parent"]) || count($arr["parent"]) == 0) && $pm->check_special_acl_for_obj($from))
			{
				$ret->add($conn["from"]);
			}
		}

		return $ret;
	}

	function prms(&$arr)
	{
		$arr["parent"] = !isset($arr["parent"]) ? array() : $arr["parent"];
		if(!is_array($arr["parent"]))
		{
			$arr["parent"] = array($arr["parent"]);
		}
		$arr["status"] = !isset($arr["status"]) ? array() : $arr["status"];
		if(!is_array($arr["status"]))
		{
			$arr["status"] = array($arr["status"]);
		}
		$arr["childs"] = !isset($arr["childs"]) ? true : $arr["childs"];

		if($arr["childs"] && (!is_array($arr["parent"]) || count($arr["parent"]) > 0))
		{
			$pars = $arr["parent"];
			foreach($pars as $par)
			{
				$ot = new object_tree(array(
					"class_id" => CL_MENU,
					"status" => $arr["status"],
					"parent" => $par,
				));
				foreach($ot->ids() as $oid)
				{
					$arr["parent"][] = $oid;
				}
			}
		}
	}

	function get_age()
	{
		// Implement better syntaxt check here!
		if(strlen(parent::prop("birthday")) != 10)
			return false;

		$date_bits = explode("-", parent::prop("birthday"));
		$age = date("Y") - $date_bits[0];
		if(date("m") < $date_bits[1] || date("m") == $date_bits[1] && date("d") < $date_bits[2])
		{
			$age--;
		}

		return ($age < 0) ? false : $age;
	}

	function phones($arr = array())
	{
		if(!is_array($arr))
		{
			$arr["type"] = $arr;
		}
		extract($arr);
		// $type, $id, $return_as_odl

		$return_as_odl = isset($arr["return_as_odl"]) ? (bool) $arr["return_as_odl"] : false;
		$id = empty($arr["id"]) ? parent::id() : (int) $arr["id"];

		$prms = array(
			"class_id" => CL_CRM_PHONE,
			"status" => array(),
			"parent" => array(),
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_CRM_PHONE.RELTYPE_PHONE(CL_CRM_PERSON)" => $id,
					"CL_CRM_PHONE.RELTYPE_PHONE(CL_CRM_PERSON_WORK_RELATION).RELTYPE_CURRENT_JOB(CL_CRM_PERSON)" => $id,
				),
			)),
		);

		if(isset($type))
		{
			$prms["CL_CRM_PHONE.type"] = $type;
		}

		if($return_as_odl)
		{
			$ret = new object_data_list($prms, array(
				CL_CRM_PHONE => array("oid", "name", "type"),
			));
		}
		else
		{
			$ret = new object_list($prms);
		}
		return $ret;
	}

	function emails($arr = array())
	{
		extract($arr);
		// $type, $id, $return_as_odl

		$return_as_odl = isset($arr["return_as_odl"]) ? (bool) $arr["return_as_odl"] : false;
		$id = empty($arr["id"]) ? parent::id() : (int) $arr["id"];

		$prms = array(
			"class_id" => CL_ML_MEMBER,
			"status" => array(),
			"parent" => array(),
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_ML_MEMBER.RELTYPE_EMAIL(CL_CRM_PERSON)" => $id,
					"CL_ML_MEMBER.RELTYPE_EMAIL(CL_CRM_PERSON_WORK_RELATION).RELTYPE_CURRENT_JOB(CL_CRM_PERSON)" => $id,
				),
			)),
		);

		if($return_as_odl)
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

	/**
		@attrib api=1 params=pos
		@param type type=string default="" set=""|"all"|"invoice"|"general"
		@returns object_list(CL_ML_MEMBER)
		@errors
			throws awex_obj_state_new
	**/
	public function get_email_addresses($type = "")
	{
		$this->require_state("saved");
		$type = isset(self::$_email_type_lut[$type]) ? self::$_email_type_lut[$type] : null;
		$list = new object_list(array(
			"class_id" => ml_member_obj::CLID,
			"CL_ML_MEMBER.RELTYPE_EMAIL(CL_CRM_PERSON)" => $this->id(),
			"contact_type" => $type
		));
		return $list;
	}

	/**
		@attrib api=1 params=pos
		@returns CL_ML_MEMBER|NULL
		@errors
			throws awex_obj_state_new
	**/
	public function get_email_address()
	{
		$this->require_state("saved");
		$email = $this->get_first_obj_by_reltype("RELTYPE_EMAIL");
		return $email;
	}

	function get_skills()
	{
		$ol = new object_list();
		foreach($this->connections_from(array("type" => "RELTYPE_HAS_SKILL")) as $conn)
		{
			$ol ->add($conn->prop("to"));
		}
		return $ol;
	}

	function get_skill_names()
	{
		$ol = new object_list();
		foreach($this->connections_from(array("type" => "RELTYPE_HAS_SKILL")) as $conn)
		{
			$ol ->add($conn->prop("to"));
		}
		$ret = array();
		foreach($ol->arr() as $o)
		{
			$ret[$o->id()] = $o->prop("skill.name");
		}
		return $ret;
	}

	function has_tasks()
	{
		$c = new connection();
		$cs = $c->find(array(
			"from" => $this->id(),
			"from.class_id" => CL_CRM_PERSON,
			"type" => "RELTYPE_PERSON_TASK",
		));
		if(sizeof($cs))
		{
			return 1;
		}
		return 0;
	}

	function has_meetings()
	{
		$c = new connection();
		$cs = $c->find(array(
			"from" => $this->id(),
			"from.class_id" => CL_CRM_PERSON,
			"type" => "RELTYPE_PERSON_MEETING",
		));
		if(sizeof($cs))
		{
			return 1;
		}
		return 0;
	}

	function has_calls()
	{
		$c = new connection();
		$cs = $c->find(array(
			"from" => $this->id(),
			"from.class_id" => CL_CRM_PERSON,
			"type" => "RELTYPE_PERSON_CALL",
		));
		if(sizeof($cs))
		{
			return 1;
		}
		return 0;
	}

	function has_ovrv_offers()
	{
		$filt = array(
			"class_id" => CL_CRM_DOCUMENT_ACTION,
			"actor" => $this->id(),
		);

		$ol = new object_list($filt);
		if(sizeof($ol->ids()))
		{
			return 1;
		}
		return 0;
	}

	function has_bugs()
	{
		$c = new connection();
		$cs = $c->find(array(
			"to" => $this->id(),
			"from.class_id" => CL_BUG,
			"type" => "RELTYPE_MONITOR",
			"bug_status" => array(1,2,10,11),
		));
		if(sizeof($cs))
		{
			return 1;
		}
		return 0;
	}

	function has_projects()
	{
		$col = new object_list(array(
			"class_id" => CL_CRM_COMPANY,
			"CL_PROJECT.RELTYPE_PARTICIPANT" => $this->id()
		));
		return (bool) $col->count();
	}

	function is_cust_mgr()
	{
		return false; // FIXME tundmatu sisuga asi. v6i siis vana systeem.
		$col = new object_list(array(
			"class_id" => CL_CRM_COMPANY,
			"client_manager" => $this->id()
		));
		return (bool) $col->count();
	}

	/** sets the default email adress content or creates it if needed **/
	private function set_fake_email($mail, $set_into_meta = true)
	{
		$n = false;
		if (object_loader::can("", $this->prop("email")))
		{
			$this->set_meta("tmp_fake_email", $mail);
			$this->set_meta("sim_fake_email", 1);
		}
		else
		{
			$n = false;
			if (object_loader::can("", $this->prop("email")))
			{
				$eo = obj($this->prop("email"));
			}
			else
			{
				$eo = obj();
				$eo->set_class_id(CL_ML_MEMBER);
				$eo->set_parent($this->id());
				$eo->set_prop("name", $this->name());
				$n = true;
			}

			$eo->set_prop("mail", $mail);
			$eo->save();

			if ($n)
			{
				$this->set_prop("email", $eo->id());
				$this->connect(array(
					"type" => "RELTYPE_EMAIL",
					"to" => $eo->id()
				));
			}
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
			$n = false;

			$id = $this->get_prop_phone($type, true);
			if (object_loader::can("", $id))
			{
				$eo = obj($id);
			}
			else
			{
				$eo = obj();
				$eo->set_class_id(CL_CRM_PHONE);
				$eo->set_parent($this->id());
				$n = true;
			}

			$type = in_array(substr($type, 5), array_keys(get_instance("crm_phone")->phone_types)) ? substr($type, 5) : "home";
			$eo->set_prop("type", $type);
			$eo->set_name($phone);
			$eo->save();

			if ($n)
			{
				if($type === "fake_phone")
				{
					$this->set_prop("phone", $eo->id());
				}
				$this->connect(array(
					"type" => $type !== "fake_fax" ? "RELTYPE_PHONE" : "RELTYPE_FAX",
					"to" => $eo->id()
				));
			}
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
			$n = false;
			if (object_loader::can("", $this->prop("address")))
			{
				$eo = obj($this->prop("address"));
			}
			else
			{
				$eo = obj();
				$eo->set_class_id(CL_CRM_ADDRESS);
				$eo->set_parent($this->id());
				$n = true;
			}

			switch($k)
			{
				case "fake_address_county":
				case "fake_address_city":
				case "fake_address_country":
					if(object_loader::can("", $v))
					{
						$eo->set_prop($pmap[$k], $v);
					}
					else
					{
						$this->_adr_set_via_rel($eo, $pmap[$k], $v);
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

			if ($n)
			{
				$this->set_prop("address", $eo->id());
			}
		}
	}

	private function _adr_set_via_rel($o, $prop, $val)
	{
		if (object_loader::can("", $o->prop($prop)))
		{
			$ro = obj($o->prop($prop));
		}
		else
		{
			$pl = $o->get_property_list();
			$rl = $o->get_relinfo();

			$ro = obj();
			$ro->set_class_id($rl[$pl[$prop]["reltype"]]["clid"][0]);
			$ro->set_parent(is_oid($o->id()) ? $o->id() : $o->parent());
		}
		$ro->set_name($val);
		$ro->save();

		$o->set_prop($prop, $ro->id());
	}

	/**
	@attrib name=get_sections api=1
	**/
	function get_sections()
	{
		$ol = new object_list();
		$this->set_current_jobs();
		foreach($this->current_jobs->arr() as $to)
		{
			if ($to->prop("company_section"))
			{
				$ol->add($to->prop("company_section"));
			}
		}
		return $ol;
	}

	/**
	@attrib name=get_sections api=1
	**/
	public function get_section_id($co)
	{
		$this->set_current_jobs();
		foreach($this->current_jobs->arr() as $o)
		{
			if((!$co || $co == $o->prop("employer")) && $o->prop("company_section"))
			{
				return $o->prop("company_section");
			}
		}
		return null;
	}

	/**
	@attrib name=get_companies api=1
	**/
	function get_companies()
	{
		$ol = new object_list();
		$this->set_current_jobs();
		foreach($this->current_jobs->arr() as $to)
		{
			if ($to->prop("employer"))
			{
				$ol->add($to->prop("employer"));
			}
		}

		return $ol;
	}

	/** returns person image tag
		@attrib api=1
	**/
	public function get_image_tag()
	{
		$imgo = $this->get_first_obj_by_reltype("RELTYPE_PICTURE");
		$img = "";
		if ($imgo)
		{
			$img_i = $imgo->instance();
			$img = $img_i->make_img_tag_wl($imgo->id(),"","",array("width" => 60));
		}
		return $img;
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

	/** returns one phone number
		@attrib api=1 params=pos
		@param co type=oid optional
			company object id
		@param sect type=oid optional
			section object id
		@param type type=string optional
			phone number type, possible options:"work", "home" , "short", "mobile", "fax", "skype", "extension"
	**/
	public function get_phone($co = null , $sect = null, $type = null)
	{
		$this->set_current_jobs();
		foreach($this->current_jobs->arr() as $current_job)
		{
			if($co && $current_job->prop("employer") != $co)
			{
				continue;
			}

			if($sect && $current_job->prop("company_section") != $sect)
			{
				continue;
			}

			$phone = $current_job->get_first_obj_by_reltype("RELTYPE_PHONE");
			if(is_object($phone) && (!$type || $type == $phone->prop("type")))
			{
				return $phone->name();
			}
		}

		foreach(parent::connections_from(array("type" => "RELTYPE_PHONE")) as $cn)
		{
			if(!$type)
			{
				return $cn->prop("to.name");
			}
			else
			{
				$phone = $cn->to();
				if($type == $phone->prop("type"))
				{
					return $cn->prop("to.name");
				}
			}
		}
		return "";
	}

	/** returns one e-mail address
		@attrib api=1
	**/
	public function get_mail($co = null , $sect = null)
	{
		$this->set_current_jobs();
		foreach($this->current_jobs->arr() as $current_job)
		{
			if($co && $current_job->prop("org") != $co)
			{
				continue;
			}

			if($sect && $current_job->prop("company_section") != $sect)
			{
				continue;
			}

			$mail = $current_job->get_first_obj_by_reltype("RELTYPE_EMAIL");
			if(is_object($mail))
			{
				return $mail->prop("mail");
			}
		}

		foreach(parent::connections_from(array("type" => "RELTYPE_EMAIL")) as $cn)
		{
			$mail = $cn->to();
			return $mail->prop("mail");
		}
		return "";
	}

	/** returns one e-mail id
		@attrib api=1
	**/
	public function get_mail_id($co = null , $sect = null)
	{
		$this->set_current_jobs();
		foreach($this->current_jobs->arr() as $current_job)
		{
			if($co && $current_job->prop("org") != $co)
			{
				continue;
			}

			if($sect && $current_job->prop("company_section") != $sect)
			{
				continue;
			}

			$mail = $current_job->get_first_obj_by_reltype("RELTYPE_EMAIL");
			if(is_object($mail))
			{
				return $mail->id();
			}
		}

		foreach(parent::connections_from(array("type" => "RELTYPE_EMAIL")) as $cn)
		{
			return $cn->prop("to");
		}
		return "";
	}

	/** returns e-mail address for bill
		@attrib api=1
	**/
	public function get_bill_mail()
	{
		$mails = $this->emails();
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
		$mails = $this->emails(array());
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

	/** returns one e-mail address link
		@attrib api=1
	**/
	public function get_mail_tag($co = null , $sect = null)
	{
		$mail = $this->get_mail($co , $sect);
		if(is_email($mail))
		{
			return html::href(array(
					"url" => "mailto:" . $mail,
					"caption" => $mail
				));
		}
		return "";
	}

	/** returns all e-mail address links
		@attrib api=1
	**/
	public function get_all_mail_tags($co = null , $sect = null)
	{
		$mails = $this->emails();
		$ret = array();
		foreach($mails->arr() as $mail)
		{
			if(is_email($mail->prop("mail")))
			{
				$ret[]= html::href(array(
					"url" => "mailto:" . $mail->prop("mail"),
					"caption" => $mail->prop("mail")
				));
			}
		}
		return $ret;
	}

	/** returns all organisations
		@attrib api=1
		@param active optional type=bool
			if set, returns only active work relation organisations
		@return array
			array(id => name , ...)
	**/
	public function get_org_selection($arr = array())
	{
		$sel = array();
		if(!empty($arr["active"]))
		{
			$this->set_current_jobs();
			foreach($this->current_jobs->arr() as $job)
			{
				if($job->prop("org"))
				{
					$sel[$job->prop("org")] = $job->prop("org.name");
				}
			}
			return $sel;
		}

		$this->set_all_jobs();
		foreach($this->all_jobs->arr() as $job)
		{
			if($job->prop("org"))
			{
				$sel[$job->prop("org")] = $job->prop("org.name");
			}
		}

		return $sel;
	}

	/** returns one work relation id
		@attrib api=1
		@param company optional type=oid
		@param section optional type=oid
		@param profession optional type=oid
		@return id
			work relation object id
	**/
	public function get_work_relation_id($arr)
	{
		$this->set_current_jobs();
		foreach($this->current_jobs->arr() as $job)
		{
			if(!empty($arr["section"]) && $job->prop("company_section") != $arr["section"])
			{
				continue;
			}
			if(!empty($arr["profession"]) && $job->prop("profession") != $arr["profession"])
			{
				continue;
			}
			if($arr["company"] && $job->prop("employer") != $arr["company"])
			{
				continue;
			}
			return $job->id();
		}
		return null;
	}

	/** returns all current work relations
		@attrib api=1
		@return object list
			work relations object list
	**/
	public function get_active_work_relations($arr = array())
	{
		$this->set_current_jobs();
		return $this->current_jobs;
	}

	/** returns one company
		@attrib api=1
		@return object
			company object
	**/
	public function company()
	{
		static $company;
		if (!$company)
		{
			$co_id = $this->company_id();
			if(is_oid($co_id))
			{
				$company = obj($co_id);
			}
		}
		return $company;
	}

	/** returns one company name where person works
		@attrib api=1
		@return string
			company name
	**/
	public function company_name()
	{
		$co_id = $this->company_id();
		if(is_oid($co_id))
		{
			$obj = obj($co_id);
			return $obj->name();
		}
		return "";
	}

	//returns all old work relations
	public function get_old_work_relations()
	{
		return crm_person_work_relation_obj::find(obj($this->id()), null, null, null, crm_person_work_relation_obj::STATE_ENDED);
	}

	//returns all person customer relations
	public function get_customer_relations()
	{
		$filter = array("class_id" => crm_company_customer_data_obj::CLID);
		$filter[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array (
					"CL_CRM_COMPANY_CUSTOMER_DATA.seller" => $this->id() ,
					"CL_CRM_COMPANY_CUSTOMER_DATA.buyer" => $this->id(),
				)
		));
		return new object_list($filter);
	}

	function get_education_list()
	{
		$ol = new object_list(array(
			"class_id" => CL_CRM_PERSON_EDUCATION,
			"person" => $this->id(),
		));
		return $ol;
	}

	function get_drivers_licenses()
	{
		$ol = new object_list(array(
			"class_id" => CL_CRM_PERSON_DRIVERS_LICENSE,
			"person" => $this->id(),
		));
		return $ol;
	}


	function get_add_education_list()
	{
		$ol = new object_list();
		foreach($this->connections_from(array("class_id" => "CL_CRM_PERSON_ADD_EDUCATION")) as $conn)
		{
			$ol ->add($conn->prop("to"));
		}
		return $ol;
	}

	/** returns object id of first company found where person is currently employed or has some other active relation to
		@attrib api=1
		@returns oid
			company id
	**/
	public function company_id()
	{
		if (!$this->is_saved())
		{
			return 0;
		}
		elseif (null === self::$company_id_cache[$this->id()])
		{
			$this_persons_work_rels = crm_person_work_relation_obj::find($this->ref());
			if ($this_persons_work_rels->count())
			{
				$id = $this_persons_work_rels->begin()->prop("employer");
			}
			else
			{
				$id = 0;
			}

			self::$company_id_cache[$this->id()] = $id;
		}

		return self::$company_id_cache[$this->id()];
	}

	/** Gets organisations with which this person has a relation to
	*	@attrib api=1
	*	@param active type=bool default=TRUE
	*		Return only organisations with active relations to
	*	@returns array
	*		array of crm_company object ids
	*	@errors
	*		throws awex_obj_state_new
	*/
	public function get_organization_ids($active = true)
	{
		if (!$this->is_saved())
		{
			throw new awex_obj_state_new("Object not saved yet");
		}

		$organisation_ids = array();
		$list = new object_data_list(array(
				"class_id" => CL_CRM_PERSON_WORK_RELATION,
				"employee" => $this->id()
			),
			array(
				CL_CRM_PERSON_WORK_RELATION => array("employer")
			)
		);

		foreach ($list->arr() as $data)
		{
			$organisation_ids[] = $data["employer"];
		}

		return $organisation_ids;
	}

	/** return all current work relation orgs
		@attrib api=1
		@return object list
	**/
	public function get_all_orgs()
	{
		$ol = new object_list();
		$this->set_current_jobs();
		foreach($this->current_jobs->arr() as $conn)
		{
			if($conn->prop("employer"))
			{
				$ol->add($conn->prop("employer"));
			}
		}
		return $ol;
	}

	/** return all current work relation company ids
		@attrib api=1
		@return array()
	**/
	public function get_all_org_ids()
	{
		$ol = $this->get_all_orgs();
		return $ol->ids();
	}

	public function set_current_jobs()
	{
		if(!$this->current_jobs)
		{
			if (!$this->is_saved())
			{
				return;
			}

			$this->current_jobs = crm_person_work_relation_obj::find($this->ref());
		}
	}

	public function set_all_jobs()
	{
		if(!$this->all_jobs)
		{
			if (!$this->is_saved())
			{
				return;
			}

			$this->all_jobs = new object_list(array(
				"class_id" => CL_CRM_PERSON_WORK_RELATION,
				"employee" => $this->id()
			));
		}
	}

	/** returns person section names
		@attrib api=1 params=pos
		@param co optional type=oid
			company id
		@param sec optional type=array
			section object ids
		@return array
			section names
	**/
	public function get_section_names($co = null, $sec = array())
	{
		$this->set_current_jobs();
		$sections = array();
		foreach($this->current_jobs->arr() as $o)
		{
			if(sizeof($sec) && !in_array($o->prop("company_section") , $sec))//kui pole ette antud yksustes j2tab vahele
			{
				continue;
			}
			if((!$co || $co == $o->prop("employer")) && $o->prop("company_section.name"))
			{
				$sections[$o->prop("company_section")] = $o->prop("company_section.name");
			}
		}

		return $sections;
	}

	/** returns person section name
		@attrib api=1 params=pos
		@param co optional type=oid
			company id
		@returns array
			section names
	**/
	public function get_section_name($co = null, $sec = array())
	{
		return reset($this->get_section_names($co, $sec));
	}

	/** returns profession names of person's active work relations
		@attrib api=1
		@param employer optional type=CL_CRM_COMPANY
			employer
		@param sections optional type=object_list
			if professions in specific sections desired
		@returns array
			profession names
	**/
	public function get_profession_names(object $employer = null, object_list $sections = null)
	{
		$professions = crm_person_work_relation_obj::find_professions($this->ref(), $employer);
		$profession_names = array();

		if($professions->count())
		{
			$profession = $professions->begin();

			do
			{
				$profession_names[] = $profession->name();
			}
			while ($profession = $professions->next());
		}

		return $profession_names;
	}

	/** adds new work relation
		@attrib api=1 params=name
		@param org optional type=oid default=current company id
			Company id
		@param section optional type=oid
			Section id
		@param profession optional type=oid
			Profession id
		@param room optional type=oid
		@param start optional type=int default=time()
			Work relation start timestamp
		@param end optional type=int
			Work relation start timestamp
		@param tasks optional type=sting
			Work tasks
		@param salary optional type=int
			Work salary per month
		@param salary_currency optional type=int
			Work salary currency
		@returns oid
			Work relation object id
	**/
	public function add_work_relation($arr = array())
	{
		$wr = new object();
		$wr->set_parent($this->id());
		$wr->set_name($this->name() . " ".t("work relation"));
		$wr->set_class_id(CL_CRM_PERSON_WORK_RELATION);
		$wr->save();

		if(empty($arr["org"]))
		{
			$company = get_current_company();
			$arr["org"] = $company->id();
		}

		$arr["employer"] = $arr["org"];
		unset($arr["org"]);

		if(empty($arr["start"]))
		{
			$arr["start"] = time();
		}

		foreach($arr as $key => $val)
		{
			if($wr->is_property($key))
			{
				$wr->set_prop($key , $val);
			}
		}
		$wr->save();
		$this->current_jobs = null;
		return $wr->id();
	}

	/** finishes work relation
		@attrib api=1 params=name
		@param id optional type=oid
			Work relation id
		@param org optional type=oid
			Company id
		@param section optional type=oid
			Section id
		@param profession optional type=oid
			Profession id
		@returns oid/0
			oid, if successful
	**/
	public function finish_work_relation($arr = array())
	{
		if(!empty($arr["id"]))
		{
			$wr = obj($wr);
		}
		else
		{
			$wr_id = $this->get_work_relation_id(array(
				"company" => $arr["org"],
				"section" => $arr["section"],
				"profession" => $arr["profession"]
			));
			if(is_oid($wr_id))
			{
				$wr = obj($wr_id);
			}
		}

		if(isset($wr) and is_object($wr))
		{
			$wr->finish();

		}
		else
		{
			return 0;
		}
	}

	/** Gets all persons marked as important
		@attrib api=1 params=pos
		@param company optional type=oid
			Company id
		@returns object list
	**/
	public function get_important_persons($company = null)
	{
		$ol = new object_list();
		if(is_oid($company))
		{
			$co = obj($company);
			$persons = $co->get_employees("all");
			$ps = $persons->ids();
		}

		//FIXME: kahtlane tegevus siin, loetakse k6ik t88tajad ...
		foreach($this->connections_from(array("type" => "RELTYPE_IMPORTANT_PERSON")) as $c)
		{
			if(is_array($ps) && !in_array($c->prop("to"), $ps))
			{
				continue;
			}
			$ol->add($c->prop("to"));
		}

		return $ol;
	}

	public function on_connect_to_meeting($arr)
	{
		$conn = $arr["connection"];
		if($conn->prop("from.class_id") == CL_CRM_PERSON && $conn->prop("reltype") == 8)	// RELTYPE_PERSON_MEETING
		{
			return $this->event_notifications($arr, "meeting");
		}
	}

	public function on_connect_to_task($arr)
	{
		$conn = $arr["connection"];
		if($conn->prop("from.class_id") == CL_CRM_PERSON && $conn->prop("reltype") == 10)	// RELTYPE_PERSON_TASK
		{
			return $this->event_notifications($arr, "task");
		}
	}

	public function event_notifications($arr, $type, $modified = false)
	{
		$conn = $arr["connection"];
		$person = $conn->from();
		$user = $person->instance()->has_user($person);
		if($user !== false)
		{
			if((int)$user->prop("nfy_".$type) === 1 || $modified !== true && (int)$user->prop("nfy_".$type) === 2)
			{
				$email = $person->email;
				if ($person->instance()->can("view" , $email))
				{
					$email_obj = new object($email);
					$addr = $email_obj->prop("mail");
					if (is_email($addr))
					{
						$this->send_nfy_mail($addr, $conn->to(), $modified);
					}
				}
			}
		}
	}

	public function send_nfy_mail($addr, $o, $modified = false)
	{
		$type = $o->class_id() == CL_TASK ? t("toimetuse") : t("kohtumise");
		$type_modified = $o->class_id() == CL_TASK ? t("toimetust") : t("kohtumist");

		$subject = sprintf(t("Teid on lisatud %s '%s' osalejaks"), $type, $o->name());
		if($modified === true)
		{
			$subject = sprintf(t("%s muutis %s '%s'"), aw_global_get("uid"), $type_modified, $o->name());
		}

		$msg = t("Link: ").get_instance($o->class_id())->mk_my_orb("change", array("id" => $o->id()))."\n\n";

		$msg .= t("Pealkiri: ").parse_obj_name($o->name())."\n";
		$msg .= t("Algus: ").get_lc_date($o->start1, LC_DATE_FORMAT_LONG_FULLYEAR)." ".date("H:i", $o->start1)."\n";
		$msg .= sprintf(t("L%spp: "), html_entity_decode("&otilde;")).get_lc_date($o->start1, LC_DATE_FORMAT_LONG_FULLYEAR)." ".date("H:i", $o->end)."\n";
		$msg .= strlen(trim($o->comment)) > 0 ? t("Kommentaar: ").$o->comment."\n" : "";
		$msg .= strlen(trim($o->content)) > 0 ? t("Sisu: ").$o->content."\n" : "";
		if(strcmp($msg, $o->meta("sent_mail_content_".$addr)) != 0)
		{
			$o->set_meta("sent_mail_content_".$addr, $msg);
			$o->save();
			send_mail($addr, $subject, $msg, "From: notifications@".str_replace(array("http://", "http://www."), "", aw_ini_get("baseurl")));
		}
	}


	/** returns customer relation creator
		@attrib api=1
		@returns string
	**/
	public function get_cust_rel_creator_name()
	{
		$o = $this->find_customer_relation();
		if(is_object($o))
		{
			return $o->prop("cust_contract_creator.name");
		}
		return "";
	}

	/** returns customer relation object
		@attrib api=1 params=pos
		@param my_co optional
		@param crea_if_not_exists optional
			if no customer relation object, makes one
		@returns object
	**/
	public function find_customer_relation($my_co_id = null, $crea_if_not_exists = false)
	{
		if ($my_co_id === null)
		{
			$my_co_id = user::get_current_company();
		}
		elseif (is_object($my_co_id))
		{
			$my_co_id = $my_co_id->id();
		}

		if (!is_oid($my_co_id))
		{
			return;
		}

		static $gcr_cache;
		if (!is_array($gcr_cache))
		{
			$gcr_cache = array();
		}
		if (isset($gcr_cache[$this->id()][$crea_if_not_exists][$my_co_id]))
		{
			return $gcr_cache[$this->id()][$crea_if_not_exists][$my_co_id];
		}

		$ol = new object_list(array(
			"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
			"buyer" => $this->id(),
			"seller" => $my_co_id
		));
		if ($ol->count())
		{
			$gcr_cache[$this->id()][$crea_if_not_exists][$my_co_id] = $ol->begin();
			return $ol->begin();
		}
		else
		if ($crea_if_not_exists)
		{
			$my_co = obj($my_co_id);
			$o = obj();
			$o->set_class_id(CL_CRM_COMPANY_CUSTOMER_DATA);
			$o->set_name(t("Kliendisuhe ").$my_co->name()." => ".$this->name());
			$o->set_parent($my_co_id);
			$o->set_prop("seller", $my_co_id);
			$o->set_prop("buyer", $this->id());
			$o->save();
			$gcr_cache[$this->id()][$crea_if_not_exists][$this->id()] = $o;
			return $o;
		}
	}

	/** returns person sections selection
		@attrib api=1
		@returns array
	**/
	public function get_section_selection()
	{
		$sects = $this->get_sections();
		$ret = $sects->names();
		return $ret;
	}

	/**
		@attrib name=handle_show_cnt api=1 params=name
		@param action type=string
		@param id type=OID

	**/
	public static function handle_show_cnt($arr)
	{
		extract($arr);

		$show_cnt_conf = personnel_management_obj::get_show_cnt_conf();
		$usr = new user();
		$u = $usr->get_current_user();
		$g = isset($show_cnt_conf[CL_CRM_PERSON][$action]["groups"]) ? $show_cnt_conf[CL_CRM_PERSON][$action]["groups"] : null;
		if($usr->is_group_member($u, $g) && is_oid($id))
		{
			$o = obj($id);
			$o->show_cnt = $o->show_cnt + 1;
			$o->save();
		}
	}

	/** returns all projects where person is customer
		@attrib api=1
		@returns object list
	**/
	public function get_projects_as_customer($arr = array())
	{
		$ol = new object_list(array(
			"class_id" => CL_PROJECT,
			"CL_PROJECT.RELTYPE_ORDERER" => $this->id()
		));
		return $ol;
	}

	/** returns all mail objects sent to person
		@attrib api=1
		@returns object list
	**/
	public function get_recieved_mails($arr = array())
	{
		$adress_objects = $this->emails();
		$options = array();
		$mails = new object_list();

		$filter = array(
			"class_id" => CL_MESSAGE,
			"CL_MESSAGE.RELTYPE_TO_MAIL_ADDRESS" => $adress_objects->ids()
		);

		if(!empty($arr["subject"]))
		{
			$filter["name"] = "%".$arr["subject"]."%";
		}

		if($arr["customer"])
		{
			$filter["CL_MESSAGE.RELTYPE_CUSTOMER.name"] = "%".$arr["customer"]."%";
		}

		$mails->add(new object_list($filter));
		return $mails;
	}

	/** returns persons company property value
		@attrib api=1 params=pos
		@param prop required type=string
			company property name
		@returns object list
	**/
	public function company_property($prop)
	{
		if($co = $this->company())
		{
			return $co->prop($prop);
		}
		return null;
	}

	public function save($check_state = false)
	{
		$this->set_name($this->prop("firstname") . " " . $this->prop("lastname") . (strlen($this->prop("previous_lastname")) < 1 ? "" : " (" . $this->prop("previous_lastname") . ")"));

		$fakes = array(
			"email", "phone", "skype", "mobile", "fax", "address_country", "address_country_relp", "address_county", "address_county_relp", "address_city", "address_city_relp", "address_postal_code", "address_address", "address_address2"
		);

		if(!is_oid($this->id()))
		{
			parent::save($check_state);
		}

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
		$r =  parent::save($check_state);
		return $r;
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
			$address_id = parent::prop("address");
			if (object_loader::can("", $address_id))
			{
				$address_str = obj($address_id)->name();
			}
		}
		return $address_str;
	}


	/** Sets phone number for person
		@attrib api=1 params=pos
		@param phone type=string
			phone number
		@param type type=string default=mobile
			phone number
		@param save type=bool default=TRUE
			Whether to save this object after setting
	**/
	public function set_phone($phone, $type="mobile", $save = true)
	{
		$eo = obj();
		$eo->set_class_id(CL_CRM_PHONE);
		$eo->set_parent($this->id());
		$eo->set_name($phone);
		$eo->set_prop("type", $type);
		$eo->save();

		$this->set_prop("phone", $eo->id());
		if ($save)
		{
			$this->save();
		}
		$this->connect(array(
			"type" => "RELTYPE_PHONE",
			"to" => $eo->id()
		));
	}

	/** Sets e-mail address for person
		@attrib api=1 params=pos
		@param mail type=string
			e-mail address
		@param save type=bool default=TRUE
			Whether to save this object after setting
	**/
	public function set_email($mail, $save = true)
	{
		$eo = obj();
		$eo->set_class_id(CL_ML_MEMBER);
		$eo->set_parent($this->id());
		$eo->set_name($mail);
		$eo->set_prop("mail" , $mail);
		$eo->save();

		$this->set_prop("email", $eo->id());
		if ($save)
		{
			$this->save();
		}
		$this->connect(array(
			"type" => "RELTYPE_EMAIL",
			"to" => $eo->id()
		));
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

	private function _get_sell_orders($arr = array())
	{
		$filter = array(
			"class_id" => CL_SHOP_SELL_ORDER,
			"purchaser" => $this->id()
		);
		$ol = new object_list($filter);
		return $ol;
	}


	/** Returns all kind of variables useful for this person
		@attrib api=1
		@return array
	**/
	public function get_data()
	{
		$data = array();
		foreach($this->properties() as $prop => $val)
		{
			$data[$prop] = $this->prop_str($prop);
		}
		return $data;
	}

	public function prop_str($k, $is_oid = NULL)
	{
		if ($k === "gender")
		{
			return $this->gender_options((int) parent::prop_str($k, false));
		}

		return parent::prop_str($k, $is_oid);
	}

	/**	Returns the the object in JSON
		@attrib api=1
	**/
	public function json()
	{
		$data = array(
			"id" => $this->id(),
			"name" => $this->name(),
			"birthday" => $this->prop("birthday"),
		);

		$json = new json();
		return $json->encode($data, aw_global_get("charset"));
	}
}
