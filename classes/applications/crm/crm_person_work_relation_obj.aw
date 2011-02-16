<?php

class crm_person_work_relation_obj extends _int_object
{
	const CLID = 1060;

	const STATE_UNDEFINED = 0;
	const STATE_ACTIVE = 2;
	const STATE_ENDED = 3;
	const STATE_NEW = 4;

	private static $state_names = array();

	/** Returns list of state names
	@attrib api=1 params=pos
	@param status type=int
		state constant value to get name for, one of crm_bill_obj::STATE_*
	@returns array
		Format option value => human readable name, if $state parameter set, array with one element returned and empty array when that state not found.
	**/
	public static function state_names($state = null)
	{
		if (empty(self::$state_names))
		{
			self::$state_names = array(
				self::STATE_UNDEFINED => t("M&auml;&auml;ramata"),
				self::STATE_ACTIVE => t("Aktiivne"),
				self::STATE_ENDED => t("L&otilde;petatud")
			);
		}

		if (isset($state))
		{
			if (isset(self::$state_names[$state]))
			{
				$state_names = array($state => self::$state_names[$state]);
			}
			else
			{
				$state_names = array();
			}
		}
		else
		{
			$state_names = self::$state_names;
		}

		return $state_names;
	}

	/** sets mail address to work relation
		@attrib api=1 params=pos
		@param mail required type=string
	**/
	public function set_mail($mail)
	{
		$o = new object();
		$o->set_parent($this->id());
		$o->set_class_id(CL_ML_MEMBER);
		$o->set_name($mail);
		$o->set_prop("mail" , $mail);
		$o->save();

		$conns = $this->connections_from(array("type" => "RELTYPE_EMAIL"));
		foreach($conns as $conn)
		{
			$conn->delete();
		}
		$this->connect(array("to" =>$o->id(), "type" => "RELTYPE_EMAIL"));
		return $o->id();
	}

	/** sets phone to work relation
		@attrib api=1 params=pos
		@param phone required type=string
	**/
	public function set_phone($phone)
	{
		$o = new object();
		$o->set_parent($this->id());
		$o->set_class_id(CL_CRM_PHONE);
		$o->set_name($phone);
		$o->save();

		$conns = $this->connections_from(array("type" => "RELTYPE_PHONE"));
		foreach($conns as $conn)
		{
			$conn->delete();
		}
		//mis kuradi jama see on - m6nikord ei saa just tehtud objekti id'd k2tte
		if($o->id())
		{
			$this->connect(array("to" =>$o->id(), "type" => "RELTYPE_PHONE"));
		}
		return $o->id();
	}

	/** finishes current work relation
		@attrib api=1
	**/
	public function finish()
	{
		$this->set_prop("end" , time());
		$this->save();
	}

	/** Returns work relations for person on given profession
		@attrib api=1 params=pos
		@param person type=CL_CRM_PERSON default=NULL
		@param profession type=CL_CRM_PROFESSION default=NULL
		@param organization type=CL_CRM_COMPANY|array(CL_CRM_COMPANY)|array(int) default=NULL
			Employer organization(s), object, array of objects, or array of object id-s
		@param section type=CL_CRM_SECTION default=NULL
		@param active type=bool default=TRUE
		@return object_list of CL_CRM_PERSON_WORK_RELATION
		@errors
			throws awex_obj_type when a parameter object is not of correct type
	**/
	public static function find(object $person = null, object $profession = null, $organization = null, object $section = null, $active = true)
	{
		$params = array(
			"class_id" => CL_CRM_PERSON_WORK_RELATION,
		);

		if ($person)
		{
			if (!$person->is_a(CL_CRM_PERSON))
			{
				throw new awex_obj_type("Given person parameter (object '".$person->id()."') isn't a person object. Class id is '".$person->class_id()."'");
			}

			$params["employee"] = $person->id();
		}

		if ($organization)
		{
			$employer = array();
			if (is_array($organization))
			{
				foreach ($organization as $org)
				{
					if (is_object($org))
					{
						if (!$org->is_a(CL_CRM_COMPANY))
						{
							throw new awex_obj_type("Given organization parameter (object '".$org->id()."') isn't a company object. Class id is '".$org->class_id()."'");
						}
						$org = $org->id();
					}
					elseif (!is_oid($org))
					{
						throw new awex_obj_type("Given organization parameter (".var_export($organization, true).") contains an invalid object id");
					}
					$employer[] = $org;
				}
			}
			elseif (!$organization instanceof object)
			{
				throw new awex_obj_type("Given organization parameter (".var_export($organization, true).") isn't a company object");
			}
			elseif (!$organization->is_a(CL_CRM_COMPANY))
			{
				throw new awex_obj_type("Given organization parameter (object '".$organization->id()."') isn't a company object. Class id is '".$organization->class_id()."'");
			}
			else
			{
				$employer[] = $organization->id();
			}

			$params["employer"] = $employer;
		}

		if ($profession)
		{
			if (!$profession->is_a(CL_CRM_PROFESSION))
			{
				throw new awex_obj_type("Given profession parameter (object '".$profession->id()."') isn't a profession object. Class id is '".$profession->class_id()."'");
			}

			$params["profession"] = $profession->id();
		}

		if ($section)
		{
			if (!$section->is_a(CL_CRM_SECTION))
			{
				throw new awex_obj_type("Given section parameter (object '".$section->id()."') isn't a section object. Class id is '".$section->class_id()."'");
			}

			$params["company_section"] = $section->id();
		}

		if ($active)
		{
			$params["state"] = self::STATE_ACTIVE;
		}

		$list = new object_list($params);
		return $list;
	}

	public function save($exclusive = false, $previous_state = null)
	{
		// update work relation object name
		if (strlen($this->name()) < 1)
		{
			if ($this->prop("employer") and $this->prop("employee"))
			{
				$this->set_name($this->prop("employer.name") . " => " . $this->prop("employee.name"));
			}
		}

		// update state according to start/end properties
		$state = self::STATE_UNDEFINED;
		$time = time();
		if ($this->prop("start") > 1 and $this->prop("start") < $time and ($this->prop("end") > $time or $this->prop("end") == 0))
		{
			$state = self::STATE_ACTIVE;
		}
		elseif ($this->prop("start") > $time)
		{
			$state = self::STATE_NEW;
		}
		elseif ($this->prop("end") < $time and $this->prop("end") > 1)
		{
			$state = self::STATE_ENDED;
		}
		$this->set_prop("state", $state);

		return parent::save($exclusive, $previous_state);
	}
}
