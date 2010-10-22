<?php

class crm_person_work_relation_obj extends _int_object
{
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
		@param organization type=CL_CRM_COMPANY default=NULL
		@param section type=CL_CRM_SECTION default=NULL
		@param active type=bool default=TRUE
		@return object_list of CL_CRM_PERSON_WORK_RELATION
		@errors
			throws awex_obj_type when a parameter object is not of correct class
	**/
	public static function find(object $person = null, object $profession = null, object $organization = null, object $section = null, $active = true)
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
			if (!$organization->is_a(CL_CRM_COMPANY))
			{
				throw new awex_obj_type("Given organization parameter (object '".$organization->id()."') isn't a company object. Class id is '".$organization->class_id()."'");
			}

			$params["employer"] = $organization->id();
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

			$params["section"] = $section->id();
		}

		if ($active)
		{
			$params[] = new object_list_filter(array( // end is not defined or in the future
				"logic" => "OR",
				"conditions" => array(
					new object_list_filter(array(
						"conditions" => array(
							"end" => new obj_predicate_compare(obj_predicate_compare::GREATER, time())
						)
					)),
					new object_list_filter(array(
						"conditions" => array(
							"end" => new obj_predicate_compare(obj_predicate_compare::LESS, 1)
						)
					)),
				)
			));

			$params[] = new object_list_filter(array( // start is not defined on in the past
				"logic" => "OR",
				"conditions" => array(
					new object_list_filter(array(
						"conditions" => array(
							"start" => new obj_predicate_compare(obj_predicate_compare::LESS, time())
						)
					)),
					new object_list_filter(array(
						"conditions" => array(
							"start" => new obj_predicate_compare(obj_predicate_compare::LESS, 1)
						)
					)),
				)
			));
		}

		$list = new object_list($params);
		return $list;
	}

	public function save($exclusive = false, $previous_state = null)
	{
		if (strlen($this->name()) < 1)
		{
			if ($this->prop("employer") and $this->prop("employee"))
			{
				$this->set_name($this->prop("employer.name") . " => " . $this->prop("employee.name"));
			}
		}
		return parent::save($exclusive, $previous_state);
	}
}
