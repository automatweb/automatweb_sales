<?php

class crm_employee_search extends aw_object_search
{
	const EMPLOYMENT_STATUS_ACTIVE = 2;
	const EMPLOYMENT_STATUS_FORMER = 3;
	const EMPLOYMENT_STATUS_PROSPECTIVE = 4;
	const EMPLOYMENT_STATUS_ALL = 5;

	const EMPLOYMENT_GENDER_ALL = 0;
	const EMPLOYMENT_GENDER_M = 1;
	const EMPLOYMENT_GENDER_W = 2;

	const PARAM_NAME = 1;
	const PARAM_EMPLOYER = 2;
	const PARAM_ADDRESS = 3;
	const PARAM_PHONE = 4;
	const PARAM_NONE = 5;
	const PARAM_SORT = 6;
	const PARAM_EMPLOYED_FROM = 7;
	const PARAM_EMPLOYED_UNTIL = 8;
	const PARAM_EMPLOYMENT_STATUS = 9;
	const PARAM_SECTION = 10;
	const PARAM_PROFESSION = 11;
	const PARAM_EMAIL = 12;
	const PARAM_GENDER = 13;
	const PARAM_AGE = 14;
	const PARAM_COUNTY = 15;
	const PARAM_CITY = 16;
	const PARAM_INDEX = 17;


	private $p_name = false;
	private $p_employer = false;
	private $p_address = false;
	private $p_phone = false;
	private $p_section = false;
	private $p_profession = false;
	private $p_employment_status = false;
	private $p_email = false;
	private $p_gender = false;
	private $p_ageto = false;
	private $p_agefrom = false;
	private $p_county = false;
	private $p_city = false;
	private $p_index = false;


	private $p_citizenship = false;
	private $p_personal_code = false;

	

	private $sort_order;
	private $search_method = "obj";

	private static $employment_status_values = array(
		self::EMPLOYMENT_STATUS_ALL,
		self::EMPLOYMENT_STATUS_ACTIVE,
		self::EMPLOYMENT_STATUS_FORMER,
		self::EMPLOYMENT_STATUS_PROSPECTIVE
	);

	private static $gender_values = array(
		self::EMPLOYMENT_GENDER_ALL,
		self::EMPLOYMENT_GENDER_M,
		self::EMPLOYMENT_GENDER_W
	);

	public static $sort_modes = array(
		"employee.name-asc",
		"employee.name-desc",
		"created-asc",
		"created-desc"
	);

	/** Returns translated options array for employment status parameter
		@attrib api=1 params=pos
		@returns array
			option value => human readable translated name
		@errors none
	**/
	public static function get_employment_status_options()
	{
		return array(
			self::EMPLOYMENT_STATUS_ACTIVE => t("Aktiivsed"),
			self::EMPLOYMENT_STATUS_FORMER => t("Endised"),
			self::EMPLOYMENT_STATUS_PROSPECTIVE => t("T&ouml;&ouml;leasuvad"),
			self::EMPLOYMENT_STATUS_ALL => t("K&otilde;ik")
		);
	}

	public static function get_employment_gender_options()
	{
		return array(
			self::EMPLOYMENT_GENDER_M => t("Mees"),
			self::EMPLOYMENT_GENDER_W => t("Naine"),
			self::EMPLOYMENT_GENDER_ALL => t("K&otilde;ik")
		);
	}
	



	// /** Counts objects satisfying filter constraints
		// @attrib api=1 params=pos
		// @returns int
			// Count of objects
		// @errors
	// **/
	// public function count()
	// {
		// return (int) $this->get_obj_count();
	// }

	public function set_sort_order($id)
	{
		if (!in_array($id, self::$sort_modes))
		{
			$e = new awex_param_type_employee_search("Invalid sort order '$id'", self::PARAM_SORT);
			throw $e->ui_msg(t("Antud v&auml;&auml;rtus on j&auml;rjestusparameetriks sobimatu"));
		}

		$method = "sort_" . $this->select_search_method();
		list($order, $direction) = explode("-", $id);
		return $this->$method($order, $direction);
	}

	/** Retrieves object id-s for employees matching search criteria specified.
		@attrib api=1 params=pos
		@comment
			This is where actual search is executed
		@returns array
			Array with person oids as keys and array of work relation ids as values
			array(
				69 => array(55, 66),
				32 => array(34),
				...
			)
		@errors
	**/
	public function get_oids()
	{
		return $this->search_obj_data();
	}

	//TODO: teha, s6ltub objlisti greedy p2ringu valmidusest
	// /** Retrieves object list of employees matching search criteria specified. Limits result if requested.
		// @attrib api=1 params=pos
		// @param limit type=obj_predicate_limit default=null
		// @comment
			// This is where actual search is executed
		// @returns array object_list
		// @errors
	// **/
	// public function get_list(obj_predicate_limit $limit = null)
	// {
		// return $this->search_obj_list($limit);
	// }

	private function select_search_method()
	{
		return $this->search_method;
	}

	private function _set_employer(object $employer)
	{
		if (!$employer->is_a(crm_company_obj::CLID))
		{
			$e = new awex_param_type_employee_search("Invalid value '" . var_export($employer, true) . "' for employer parameter", self::PARAM_EMPLOYER);
			throw $e->ui_msg(t("Antud v&auml;&auml;rtus on t&ouml;&ouml;andja otsinguparameetriks sobimatu"));
		}

		$this->p_employer = $employer->id();
	}

	private function _set_section(object $section = null)
	{
		if (null === $section)
		{
			$this->p_section = new obj_predicate_compare(obj_predicate_compare::NULL);
		}
		elseif (!$section->is_a(crm_section_obj::CLID))
		{
			$e = new awex_param_type_employee_search("Invalid value '" . var_export($section, true) . "' for section parameter", self::PARAM_SECTION);
			throw $e->ui_msg(t("Antud v&auml;&auml;rtus on &uuml;ksuse otsinguparameetriks sobimatu"));
		}
		else
		{
			$this->p_section = $section->id();
		}
	}

	private function _set_profession(object $profession = null)
	{
		if (null === $profession)
		{
			$this->p_profession = new obj_predicate_compare(obj_predicate_compare::NULL);
		}
		elseif (!$profession->is_a(crm_profession_obj::CLID))
		{
			$e = new awex_param_type_employee_search("Invalid value '" . var_export($profession, true) . "' for profession parameter", self::PARAM_PROFESSION);
			throw $e->ui_msg(t("Antud v&auml;&auml;rtus on ameti otsinguparameetriks sobimatu"));
		}
		else
		{
			$this->p_profession = $profession->id();
		}
	}

	private function _set_name($value)
	{
		if (empty($value) or !is_string($value) or strlen($value) < 2)
		{
			$e = new awex_param_type_employee_search("Invalid value '" . var_export($value, true) . "' for name parameter", self::PARAM_NAME);
			throw $e->ui_msg(t("Antud v&auml;&auml;rtus on t&ouml;&ouml;taja nime otsinguparameetriks sobimatu"));
		}

		$this->p_name = self::prepare_search_words($value);
	}

	private function _set_employment_status($value)
	{
		if (empty($value) or !is_int($value) or !in_array($value, self::$employment_status_values))
		{
			$e = new awex_param_type_employee_search("Invalid value '" . var_export($value, true) . "' for employment_status parameter", self::PARAM_EMPLOYMENT_STATUS);
			throw $e->ui_msg(t("Antud v&auml;&auml;rtus ei kuulu kehtivate t&ouml;&ouml;suhte staatuse hulka"));
		}

		$this->p_employment_status = $value;
	}

	private function _set_agefrom($value)
	{
		if (empty($value))
		{
			$e = new awex_param_type_employee_search("Invalid value '" . var_export($value, true) . "' for employment_status parameter", self::PARAM_AGE);
			throw $e->ui_msg(t("Antud v&auml;&auml;rtus ei kuulu v&otilde;imalike vanuste hulka"));
		}

		$this->p_agefrom = $value;
	}

	private function _set_ageto($value)
	{
		if (empty($value))
		{
			$e = new awex_param_type_employee_search("Invalid value '" . var_export($value, true) . "' for employment_status parameter", self::PARAM_AGE);
			throw $e->ui_msg(t("Antud v&auml;&auml;rtus ei  v&otilde;imalike vanuste hulka"));
		}

		$this->p_ageto = $value;
	}




	private function _set_gender($value)
	{
		if (empty($value) or !in_array($value, self::$gender_values))
		{
			$e = new awex_param_type_employee_search("Invalid value '" . var_export($value, true) . "' for gender parameter", self::PARAM_GENDER);
			throw $e->ui_msg(t("Antud v&auml;&auml;rtus ei kuulu kehtivate sugude hulka"));
		}

		$this->p_gender = $value;
	}

	private function _set_citizenship($value)
	{
		$this->p_citizenship = self::prepare_search_words($value);
	}

	private function _set_personal_code($value)
	{
		$this->p_personal_code = self::prepare_search_words($value);
	}

	private function _set_address($value)
	{
		if (empty($value) or !is_string($value) or strlen($value) < 2)
		{
			$e = new awex_param_type_employee_search("Invalid value '" . var_export($value, true) . "' for address parameter", self::PARAM_ADDRESS);
			throw $e->ui_msg(t("Antud v&auml;&auml;rtus on aadressi otsinguparameetriks sobimatu"));
		}

		$this->p_address = self::prepare_multiple_search_words($value);

	}

	private function _set_county($value)
	{
		if (empty($value) or !is_string($value) or strlen($value) < 1)
		{
			$e = new awex_param_type_employee_search("Invalid value '" . var_export($value, true) . "' for address parameter", self::PARAM_COUNTY);
			throw $e->ui_msg(t("Antud v&auml;&auml;rtus on maakonna otsinguparameetriks sobimatu"));
		}

		$this->p_county = self::prepare_multiple_search_words($value);
	}

	private function _set_city($value)
	{
		if (empty($value) or !is_string($value) or strlen($value) < 1)
		{
			$e = new awex_param_type_employee_search("Invalid value '" . var_export($value, true) . "' for address parameter", self::PARAM_CITY);
			throw $e->ui_msg(t("Antud v&auml;&auml;rtus on linna otsinguparameetriks sobimatu"));
		}

		$this->p_city = self::prepare_multiple_search_words($value);
	}

	private function _set_index($value)
	{
		if (empty($value) or strlen($value) < 1)
		{
			$e = new awex_param_type_employee_search("Invalid value '" . var_export($value, true) . "' for address parameter", self::PARAM_INDEX);
			throw $e->ui_msg(t("Antud v&auml;&auml;rtus on postiindeksi otsinguparameetriks sobimatu"));
		}
		$this->p_index = self::prepare_multiple_search_words($value);
	}

	private function _set_email($value)
	{
		if (empty($value) or !is_string($value) or strlen($value) < 0)
		{
			$e = new awex_param_type_employee_search("Invalid value '" . var_export($value, true) . "' for e-mail parameter", self::PARAM_EMAIL);
			throw $e->ui_msg(t("Antud v&auml;&auml;rtus on e-posti otsinguparameetriks sobimatu"));
		}

		$this->p_email = self::prepare_search_words($value);
	}

	private function _set_phone($value)
	{
		if (empty($value) or !is_numeric($value) or $value < 0)
		{
			$e = new awex_param_type_employee_search("Invalid value '" . var_export($value, true) . "' for phone parameter", self::PARAM_PHONE);
			throw $e->ui_msg(t("Antud v&auml;&auml;rtus on telefoninumbri otsinguparameetriks sobimatu"));
		}

		settype($value, "int");
		$this->p_phone = "{$value}%";
	}

	private function sort_obj($order, $direction)
	{
		$sort_dir = ($direction === "asc") ? obj_predicate_sort::ASC : obj_predicate_sort::DESC;

		$sortable_fields = array(
			"name" => array(obj_predicate_sort::ASC, "CL_CRM_COMPANY_CUSTOMER_DATA.buyer.name"),
			"created" => array(obj_predicate_sort::ASC, "created")
		);
		$sort_by = $sortable_fields[$order][1];

		$this->sort_order = new obj_predicate_sort(array($sort_by => $sort_dir));
	}

	private function search_obj_list()
	{
		$result = new object_list();
		$filter = $this->_get_obj_filter();

		// execute query
		$result = new object_list($filter, array("object_id_property" => "employee"));

		return $result;
	}

	private function search_obj_data()
	{
		$filter = $this->_get_obj_filter();

		// execute query
		$result = new object_data_list(
			$filter,
			array(
				crm_person_work_relation_obj::CLID => array("employee", "start", "end")
				// crm_person_work_relation_obj::CLID => array("employee" => "oid")//FIXME: kui nii panna, siis acl annab errori:
				/*
				Uncaught exception: awex_obj_data_integrity
				Message: Error in object hierarchy, count exceeded (340163, 340163)
				File: E:\htdocs\aw\automatweb_sales_dev\classes\core\obj\acl_base.aw
				Line: 551

				objekt on iseenda parent -- parent ja cur_oid on samad, mingi reference kuskil, ning asendatakse ainult oid, mitte parent?
				*/
			)
		);

		$result = $result->arr();
		$oids = array();
		$active = self::EMPLOYMENT_STATUS_ACTIVE === $this->p_employment_status;
		$former = self::EMPLOYMENT_STATUS_FORMER === $this->p_employment_status;
		$prospective = self::EMPLOYMENT_STATUS_PROSPECTIVE === $this->p_employment_status;
		$status_search = ($former or $active or $prospective);
		$active_status_check = $subtracting_status_check = array();
		foreach ($result as $oid => $data)
		{
			if ($status_search)
			{
				$time = time();
				if ( // active employees (employees who have at least one active work relation)
					($data["start"] < $time and (!$data["end"] or $data["end"] > $time)) or
					(!$data["start"] and !$data["end"]) // work relations with no start or end specified are considered active
				)
				{
					$active_status_check[$data["employee"]] = true;
				}

				if ($former and $data["start"] > $time) // exclude employees who have future work rels
				{
					$subtracting_status_check[$data["employee"]] = true;
				}
				elseif ($prospective and $data["end"] < $time) // exclude employees who have ended work rels
				{
					$subtracting_status_check[$data["employee"]] = true;
				}
			}

			$oids[$data["employee"]][] = $oid;
		}

		// filter out by employment status
		if ($status_search)
		{
			if ($active)
			{
				$oids = array_intersect_key($oids, $active_status_check);
			}
			else
			{
				$oids = array_diff_key($oids, $active_status_check, $subtracting_status_check);
			}
		}

		// check employees access because odl checks only workrels
		foreach ($oids as $oid => $data)
		{
			if (!object_loader::can("view", $oid))
			{
				$oids[$oid] = null;
				unset($oids[$oid]);
			}
		}

		return $oids;
	}

	private function get_obj_count()
	{ //TODO: group by employee
		$filter = $this->_get_obj_filter();
		$result = new object_data_list(
			array_merge($filter),
			array(
				crm_person_work_relation_obj::CLID => array(new obj_sql_func(obj_sql_func::COUNT, "count" , "*"))
			)
		);
		$result = $result->arr();
		$result = reset($result);
		$result = $result["count"];
		return $result;
	}

	private function _get_obj_filter()
	{
		$filter = array("class_id" => crm_person_work_relation_obj::CLID);

		// employer constraint
		if ($this->p_employer)
		{
			$filter["employer"] = $this->p_employer;
		}

		// section constraint
		if ($this->p_section)
		{
			$filter["company_section"] = $this->p_section;
		}

		// profession constraint
		if ($this->p_profession)
		{
			$filter["profession"] = $this->p_profession;
		}

		// search params
		if (!empty($this->p_name))
		{
			$filter["CL_CRM_PERSON_WORK_RELATION.employee(CL_CRM_PERSON).name"] = "{$this->p_name}";
		}

		if (!empty($this->p_phone))
		{
			$filter["CL_CRM_PERSON_WORK_RELATION.employee(CL_CRM_PERSON).RELTYPE_PHONE.name"] = "{$this->p_phone}";
		}

		if (!empty($this->p_email))
		{
			$filter["CL_CRM_PERSON_WORK_RELATION.employee(CL_CRM_PERSON).RELTYPE_EMAIL.mail"] = "{$this->p_email}";
		}

		if (!empty($this->p_createdby))
		{
			$filter["createdby"] = $this->p_createdby;
		}

		if (!empty($this->p_address))
		{
		/*	$filter["CL_CRM_PERSON_WORK_RELATION.employee(CL_CRM_PERSON).RELTYPE_ADDRESS_ALT.name"] = "{$this->p_address}";
*/
			$filter[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_CRM_PERSON_WORK_RELATION.employee(CL_CRM_PERSON).RELTYPE_ADDRESS_ALT.name" => "{$this->p_address}",	"CL_CRM_PERSON_WORK_RELATION.employee(CL_CRM_PERSON).RELTYPE_ADDRESS.name" => $this->p_address,
				)));
		}

		if (!empty($this->p_county))
		{
			$filter[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_CRM_PERSON_WORK_RELATION.employee(CL_CRM_PERSON).RELTYPE_ADDRESS_ALT.name" => "{$this->p_county}",	"CL_CRM_PERSON_WORK_RELATION.employee(CL_CRM_PERSON).RELTYPE_ADDRESS.maakond.name" => $this->p_county,
				)));
		}

		if (!empty($this->p_city))
		{
			$filter[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_CRM_PERSON_WORK_RELATION.employee(CL_CRM_PERSON).RELTYPE_ADDRESS_ALT.name" => "{$this->p_city}",	"CL_CRM_PERSON_WORK_RELATION.employee(CL_CRM_PERSON).RELTYPE_ADDRESS.linn.name" => $this->p_city,
			)));
		}

		if (!empty($this->p_index))
		{
			$filter[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_CRM_PERSON_WORK_RELATION.employee(CL_CRM_PERSON).RELTYPE_ADDRESS_ALT.postal_code" => "{$this->p_index}",	"CL_CRM_PERSON_WORK_RELATION.employee(CL_CRM_PERSON).RELTYPE_ADDRESS.postiindeks" => $this->p_index,
			)));
		}


		if (!empty($this->p_gender))
		{
			$filter["CL_CRM_PERSON_WORK_RELATION.employee(CL_CRM_PERSON).gender"] = "{$this->p_gender}";
		}

		if (!empty($this->p_personal_code))
		{
			$filter["CL_CRM_PERSON_WORK_RELATION.employee(CL_CRM_PERSON).personal_id"] = "{$this->p_personal_code}";
		}

		if (!empty($this->p_citizenship))
		{
			$filter["CL_CRM_PERSON_WORK_RELATION.employee(CL_CRM_PERSON).RELTYPE_CITIZENSHIP.country.name"] = "{$this->p_citizenship}";
		}

		if (!empty($this->p_agefrom) || !empty($this->p_ageto))
		{
			$year = date("Y");
			if(!empty($this->p_agefrom) && !empty($this->p_ageto))
			{
				$start = mktime(0,0,0,date("m"),date("d"),$year - $this->p_ageto);
				$end = mktime(0,0,0,date("m"),date("d"),$year - $this->p_agefrom);
				$filter["CL_CRM_PERSON_WORK_RELATION.employee(CL_CRM_PERSON).birth_date"] = 
				new obj_predicate_compare(obj_predicate_compare::BETWEEN, $start, $end);
			}
			elseif(!empty($this->p_agefrom))
			{
				$end = mktime(0,0,0,date("m"),date("d"),$year - $this->p_agefrom);
				$filter["CL_CRM_PERSON_WORK_RELATION.employee(CL_CRM_PERSON).birth_date"] = new obj_predicate_compare(obj_predicate_compare::LESS, $end);
			}
			else
			{
				$start = mktime(0,0,0,date("m"),date("d"),$year - $this->p_ageto);
				$filter["CL_CRM_PERSON_WORK_RELATION.employee(CL_CRM_PERSON).birth_date"] = new obj_predicate_compare(obj_predicate_compare::GREATER, $start);
			}
		}


/* TODO: kirjutada p2ring, mis kysiks isikuid, kel on ainult aktiivsed, ainult l6ppend, ... t88suhted.*/
		if (self::EMPLOYMENT_STATUS_ACTIVE === $this->p_employment_status)
		{
			// set filter to get work relations with this moment's time falling between start and end property values
			// work relation end after this moment's time
			$filter[] = new object_list_filter(array(
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
			));
			// work relation start before this moment's time
			$filter[] = new object_list_filter(array(
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
			));
		}
		elseif (self::EMPLOYMENT_STATUS_FORMER === $this->p_employment_status)
		{
			// work relation end is set and before this moment's time
			$filter["end"] = new obj_predicate_compare(obj_predicate_compare::BETWEEN, 1, time());
		}
		elseif (self::EMPLOYMENT_STATUS_PROSPECTIVE === $this->p_employment_status)
		{
			// work relation start is set and after this moment's time
			$filter["start"] = new obj_predicate_compare(obj_predicate_compare::GREATER, time());
		}

		// sorting
		if ($this->sort_order)
		{
			$filter[] = $this->sort_order;
		}
//arr($filter);
		return $filter;
	}

	public function __set($name, $value)
	{
		$setter = "_set_{$name}";

		if (!method_exists($this, $setter))
		{
			throw new awex_param("Invalid parameter '$name'");
		}

		$this->$setter($value);
	}

	private function prepare_multiple_search_words($string)
	{
		if (false === strpos($string, ","))
		{
			$words = self::prepare_search_words($string);
		}
		else
		{
			$arr = explode(",", $string);
			foreach($arr as $a)
			{
				$words[] =trim($a);
			}
		}
		return $words;
	}

	// takes space separated user input "AND" search string, returns words separated by "%"
	private function prepare_search_words($string)
	{
		if (false === strpos($string, "%"))
		{
			$words = explode(" ", $string);
			$words = array_unique($words);
			$parsed = array();
			foreach ($words as $word)
			{
				$word = trim($word);
				if (strlen($word))
				{
					$parsed[] = addslashes($word);
				}
			}
			$words = "%" . implode("%", $parsed) . "%";
		}
		else
		{
			$words = addslashes($string);
		}
		return $words;
	}
}

class awex_param_type_employee_search extends awex_param_type
{
	public $default_human_readable_error_message = "";

	/**
		@attrib api=1 params=pos
		@param msg type=string
			Human readable translated message about parameter error
		@returns string
	**/
	public function ui_msg($msg)
	{
		$this->default_human_readable_error_message = $msg;
		return $this;
	}
}
