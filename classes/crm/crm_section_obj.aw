<?php

class crm_section_obj extends _int_object
{
	const CLID = 321;

	function get_job_offers()
	{
		$r = new object_list;
		foreach($this->connections_to(array("from.class_id" => CL_PERSONNEL_MANAGEMENT_JOB_OFFER, "type" => "RELTYPE_SECTION")) as $conn)
		{
			$r->add($conn->prop("from"));
		}
		return $r;
	}

	//old function... for people without work relation
	//for only people with work relation, use get_workers() instead
	function get_employees($no_subs = 0)
	{
		$ol = new object_list();
		//getting all the workers for the $obj
		$conns = $this->connections_from(array(
			"type" => "RELTYPE_WORKERS",
		));
		foreach($conns as $conn)
		{
			$ol->add($conn->prop('to'));
		}

		//getting all the sections
		if(!$no_subs)
		{
			$conns = $this->connections_from(array(
				"type" => "RELTYPE_SECTION",
			));
			foreach($conns as $conn)
			{
				$section = $conn->to();
				foreach($section->get_employees()->ids() as $oid)
				{
					$ol->add($oid);
				}
			}
		}
		return $ol;
	}

	function get_workers_grp_profession()
	{
		$ret = array();
		$rel_list = new object_list(array(
			"class_id" => CL_CRM_PERSON_WORK_RELATION,
			"site_id" => array(),
			"lang_id" => array(),
			"CL_CRM_PERSON_WORK_RELATION.RELTYPE_SECTION" => $this->id(),
		));

		if(sizeof($rel_list->ids()))
		{
			foreach($rel_list->arr() as $rel)
			{
				$person_list = new object_list(array(
					"class_id" => CL_CRM_PERSON,
					"site_id" => array(),
					"class_id" => array(),
					"CL_CRM_PERSON.RELTYPE_CURRENT_JOB" => $rel->id(),
				));
				$person = reset($person_list->ids());
				if($rel->prop("profession"))
				{
					$ret[$rel->prop("profession")][] = $person;
				}
				else
				{
					$ret[0][] = $person;
				}
			}
			return $ret;
		}

		return $ret;
	}

	/** Returns section workers
		@attrib api=1 params=name
		@param co optional type=oid
			company id
		@return object list
			person object list
	**/
	function get_workers($arr = array())
	{
		$filter = array(
			"class_id" => CL_CRM_PERSON_WORK_RELATION,
			"site_id" => array(),
			"lang_id" => array(),
			"company_section" => $this->id(),
/*			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					new object_list_filter(array(
						"logic" => "AND",
						"conditions" => array(
							"start" => new obj_predicate_compare(obj_predicate_compare::LESS, 1)
						)
					)),
					new object_list_filter(array(
						"logic" => "AND",
						"conditions" => array(
							"start" => new obj_predicate_compare(obj_predicate_compare::LESS, time())
						)
					))
				)
			)),
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					new object_list_filter(array(
						"logic" => "AND",
						"conditions" => array(
							"end" => new obj_predicate_compare(obj_predicate_compare::LESS, 1)
						)
					)),
					new object_list_filter(array(
						"logic" => "AND",
						"conditions" => array(
							"end" => new obj_predicate_compare(obj_predicate_compare::GREATER, time())
						)
					))
				)
			)),*/

		);

		if(empty($arr["state"]))
		{
			$arr["state"] = "active";
		}
		switch($arr["state"])
		{
			case "former":
				$filter["state"] = array(crm_person_work_relation_obj::STATE_ENDED);
				break;
			case "all":
				$filter["state"] = array();
			break;
			case "active":
			default:
				$filter["state"] = array(crm_person_work_relation_obj::STATE_ACTIVE, crm_person_work_relation_obj::STATE_UNDEFINED);
				break;
		}
		$rel_list = new object_list($filter);

		if($rel_list->count())
		{
			$ids = array();
			foreach($rel_list->arr() as $rel)
			{
				$ids[] = $rel->prop("employee");
			}
			if(sizeof($ids))
			{
				$ol = new object_list(array(
					"oid" => $ids
				));
			}
			else
			{
				$ol = new object_list();
			}
		}
		else
		{
			$ol = new object_list();
		}

		//vana asja toimimiseks
/*		if(empty($arr["co"]) || !is_object($co = obj($arr["co"])) || !$co->prop("use_only_wr_workers"))
		{
			$ol->add($this->get_employees(1));
		}
*/

		return $ol;
	}

//2kki optimeerib... seep2rast selline lisa funktsioon
	/** Returns company worker selection
		@attrib api=1 params=name
		@param active optional type=bool
			if set, returns only active workers
		@return object list
			person object list
	**/
	public function get_worker_selection($arr = array())
	{
		$workers = $this->get_workers($arr);

		return $workers->names();

	}

	function get_sections()
	{
		$ol = new object_list();
		$ol->add($this->id());
		foreach($this->connections_from(array(
			"type" => "RELTYPE_SECTION",
			"sort_by_num" => "to.jrk"
		)) as $conn)
		{
			$ol->add($conn->prop("to"));
			$parent = $conn->to();
			$ol->add($parent->get_sections());
		}
		return $ol;
	}

	function has_sections()
	{
		$sub = $this->get_first_obj_by_reltype("RELTYPE_SECTION");
		if(is_object($sub))
		{
			return 1;
		}
		return 0;
	}

	function has_workers()
	{
		$sub = $this->get_first_obj_by_reltype("RELTYPE_WORKERS");
		if(is_object($sub))
		{
			return 1;
		}

		$rels = $this->get_work_relations(array("limit" => 1));
		if(sizeof($rels->ids()))
		{
			return 1;
		}

		return 0;
	}

	/** Returns company work relations
		@attrib api=1 params=name
		@param limit optional type=int
		@return object list
			section work relations object list
	**/
	public function get_work_relations($arr = array())
	{
		$filter = array(
			"class_id" => CL_CRM_PERSON_WORK_RELATION,
			"section" => $this->id()
		);
		if($arr["limit"])
		{
			$filter["limit"] = 1;
		}
		$ol = new object_list($filter);
		return $ol;
	}

	/** Returns list of professions in this section
		@attrib api=1 params=name
		@return object_list
	**/
	public function get_professions()
	{
		$ol = new object_list();
		$connections = $this->connections_from(array(
			"type" => "RELTYPE_PROFESSIONS",
			"sort_by_num" => "to.jrk"
		));
		foreach($connections as $conn)
		{
			$ol->add($conn->prop("to"));
		}
		return $ol;
	}

	function get_students($arr)
	{
		$ids = isset($arr["id"]) ? $arr["id"] : parent::id();
		$prms = array(
			"class_id" => CL_CRM_PERSON,
			"lang_id" => array(),
			"site_id" => array(),
			"CL_CRM_PERSON.RELTYPE_EDUCATION.RELTYPE_FACULTY" => $ids
		);
		$prms = array_merge($prms, $arr["prms"]);
		$props = is_array($arr["props"]) ? $arr["props"] : array(
			CL_CRM_PERSON => array("oid", "name"),
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

	/** Returns default address as a string
		@attrib api=1
	**/
	public function get_address_string()
	{
		$address_str = "";
		$address = $this->get_first_obj_by_reltype("RELTYPE_LOCATION");

		if ($address)
		{
			$address_str = $address->name();
		}
		else
		{
			$address_id = parent::prop("contact");
			if (acl_base::can("", $address_id))
			{
				$address_str = obj($address_id)->name();
			}
		}
		return $address_str;
	}
}
