<?php

//!!! db ja obj otsingutes tehakse erinevalt addslashes. teha korda

	/**
	@attrib api=1 params=pos
	@param limit type=obj_predicate_limit default=NULL
		Limit determines result type
	@param customer_params type=array default=array()
		Constraining parameters for calls to return. Associative array containing any or all of following elements:
			"name" - customer name substring
			"salesman" - salesperson oid assigned to associated customer
			"last_caller" - person oid who made the last call made to associated customer
			"last_call_result" - result of last call made to associated customer. one of crm_call_obj::RESULT_... constants
			"address" - freeform address search string (if specified then length must be greater than 1)
			"phone" - one of customer's phone numbers is or starts with given numeric string
			"status" - customer relation sales state. one of crm_company_customer_data_obj::SALESSTATE_... constants
	@param order type=string default="name ASC"
		Result ordering options:
			name ASC|DESC - customer name
			last_call_time ASC|DESC - order by time of last call made to the customer associated with the call
			last_call_maker ASC|DESC
			last_call_result ASC|DESC
			calls_made ASC|DESC - total nr of calls made to customer
			lead_source ASC|DESC - contact aquisition source
			salesman ASC|DESC - salesman assigned to customer in this sales application
			Example: "salesman DESC" orders results alphabetically by salesman name in descending direction
			Default direction is ASC

	@returns object_list/int
		If limit is not specified then this method returns the number of calls to be made. If limit given then object_list of these calls

	@errors
		throws awex_obj_type on argument type errors
	**/
	// public function get_contacts_data(obj_predicate_limit $limit = null, $customer_params = array(), $order = "name ASC")



class crm_sales_contacts_search
{
	const PARAM_METHOD = 1;
	const PARAM_NAME = 2;
	const PARAM_SALESMAN = 3;
	const PARAM_LEAD_SOURCE = 4;
	const PARAM_CALLS = 5;
	const PARAM_STATUS = 6;
	const PARAM_ADDRESS = 7;
	const PARAM_PHONE = 8;
	const PARAM_SELLER = 9;
	const PARAM_NONE = 10;
	const PARAM_SORT = 11;
	const PARAM_BUYER = 12;
	const PARAM_CATEGORY = 13;

	private $p_seller;
	private $p_buyer;
	private $p_category;
	private $p_reg_nr;
	private $p_name;
	private $p_salesman;
	private $p_lead_source;
	private $p_calls;
	private $p_status;
	private $p_address;
	private $p_phone;

	private $sort_order;
	private $additional_joins = "";
	private $search_method;

	public static $sort_modes = array(
		"name-asc",
		"name-desc",
		"last_call_time-asc",
		"last_call_time-desc",
		"last_call_maker-asc",
		"last_call_maker-desc",
		"last_call_result-asc",
		"last_call_result-desc",
		"calls_made-asc",
		"calls_made-desc",
		"lead_source-asc",
		"lead_source-desc",
		"salesman-asc",
		"salesman-desc"
	);

	public function __set($name, $value)
	{
		$setter = "_set_{$name}";

		if (!method_exists($this, $setter))
		{
			throw new awex_crm_contacts_search_param("Invalid parameter '$name'", self::PARAM_METHOD);
		}

		$this->$setter($value);
		$this->search_method = null;
	}

	public function count()
	{
		$method = "search_" . $this->select_search_method();
		// limit = false means count
		return $this->$method(false);
	}

	public function set_sort_order($id)
	{
		if (!in_array($id, self::$sort_modes))
		{
			throw new awex_crm_contacts_search_param("Invalid sort order '$id'", self::PARAM_SORT);
		}

		$method = "sort_" . $this->select_search_method();
		list($order, $direction) = explode("-", $id);
		return $this->$method($order, $direction);
	}

	/** Retrieves object id-s for customer relations matching search criteria specified. Limits result if requested.
		@attrib api=1 params=pos
		@param limit type=obj_predicate_limit default=null
		@comment
			This is where actual search is executed
		@returns array
			Array with cr oids as values
		@errors
	**/
	public function get_customer_relation_oids(obj_predicate_limit $limit = null)
	{
		$method = "search_" . $this->select_search_method();
		return $this->$method($limit);
	}

	private function select_search_method()
	{
		if (!$this->search_method)
		{
			if (
				(!empty($this->p_address) xor !empty($this->p_phone))
				and empty($this->p_name)
				and empty($this->p_salesman)
				and empty($this->p_lead_source)
				and empty($this->p_calls)
			)
			{
				$this->search_method = "db";
			}
			else
			{
				$this->search_method = "obj";
			}
		}

		return $this->search_method;
	}

	private function _set_seller(object $seller)
	{
		if (!$seller->is_a(CL_CRM_COMPANY)) //!!! todo: kontroll implements interface seller_interface vms.
		{
			throw new awex_crm_contacts_search_param("Invalid value '" . var_export($seller, true) . "' for seller parameter", self::PARAM_SELLER);
		}

		$this->p_seller = $seller->id();
	}

	private function _set_buyer(object $buyer)
	{
		if (!$buyer->is_a(CL_CRM_COMPANY) and !$buyer->is_a(CL_CRM_PERSON)) //!!! todo: kontroll implements interface buyer_interface vms.
		{
			throw new awex_crm_contacts_search_param("Invalid value '" . var_export($buyer, true) . "' for buyer parameter", self::PARAM_BUYER);
		}

		$this->p_buyer = $buyer->id();
	}

	private function _set_category(object $category)
	{
		if (!$category->is_a(CL_CRM_CATEGORY))
		{
			throw new awex_crm_contacts_search_param("Invalid value '" . var_export($category, true) . "' for category parameter", self::PARAM_CATEGORY);
		}

		$this->p_category = $category->id();
	}

	private function _set_reg_nr($nr)
	{
		$this->p_reg_nr = $nr;
	}

	private function _set_name($value)
	{
		if (empty($value) or !is_string($value) or strlen($value) < 2)
		{
			throw new awex_crm_contacts_search_param("Invalid value '" . var_export($value, true) . "' for name parameter", self::PARAM_NAME);
		}

		$this->p_name = self::prepare_search_words($value);
	}

	private function _set_salesman($value)
	{
		settype($value, "int");
		if (!is_oid($value))
		{
			throw new awex_crm_contacts_search_param("Invalid value '" . var_export($value, true) . "' for salesman parameter", self::PARAM_SALESMAN);
		}

		$this->p_salesman = $value;
	}

	private function _set_lead_source($value)
	{
		if (empty($value) or !is_string($value) or strlen($value) < 2)
		{
			throw new awex_crm_contacts_search_param("Invalid value '" . var_export($value, true) . "' for lead source parameter", self::PARAM_LEAD_SOURCE);
		}

		$this->p_lead_source = self::prepare_search_words($value);
	}

	private function _set_calls(obj_predicate_compare $constraint)
	{
		if ($constraint->get_operator() === obj_predicate_compare::EQUAL)
		{
			$constraint = $constraint->get_comparison_value1();
		}

		$this->p_calls = $constraint;
	}

	private function _set_status($value)
	{
		if (!is_int($value) or !crm_company_customer_data_obj::sales_state_names($value))
		{
			throw new awex_crm_contacts_search_param("Invalid value '" . var_export($value, true) . "' for status parameter", self::PARAM_STATUS);
		}

		$this->p_status = $value;
	}

	private function _set_address($value)
	{
		if (empty($value) or !is_string($value) or strlen($value) < 2)
		{
			throw new awex_crm_contacts_search_param("Invalid value '" . var_export($value, true) . "' for address parameter", self::PARAM_ADDRESS);
		}

		$this->p_address = self::prepare_search_words($value);
	}

	private function _set_phone($value)
	{
		if (empty($value) or !is_numeric($value) or $value < 0)
		{
			throw new awex_crm_contacts_search_param("Invalid value '" . var_export($value, true) . "' for phone parameter", self::PARAM_PHONE);
		}

		settype($value, "int");
		$this->p_phone = "{$value}%";
	}

	private function sort_db($order, $sort_direction)
	{
		$additional_joins = "";

		if ("name" === $order)
		{
			$order_by = "ORDER BY customer_objects.name {$sort_direction}";
		}
		elseif ("last_call_time" === $order)
		{
			$additional_joins .= "LEFT JOIN planner last_call on last_call.id=aw_crm_customer_data.aw_sales_last_call ";
			$order_by = "ORDER BY last_call.real_start {$sort_direction}";
		}
		elseif ("last_call_maker" === $order)
		{
			$additional_joins .= "LEFT JOIN planner last_call on last_call.id=aw_crm_customer_data.aw_sales_last_call ";
			$order_by = "ORDER BY last_call.real_maker {$sort_direction}";
		}
		elseif ("last_call_result" === $order)
		{
			$additional_joins .= "LEFT JOIN planner last_call on last_call.id=aw_crm_customer_data.aw_sales_last_call ";
			$order_by = "ORDER BY last_call.result {$sort_direction}";
		}
		elseif ("calls_made" === $order)
		{
			$order_by = "ORDER BY aw_crm_customer_data.aw_sales_calls_made {$sort_direction}";
		}
		elseif ("salesman" === $order)
		{
			$additional_joins .= "LEFT JOIN objects salesman_objects on salesman_objects.oid=aw_crm_customer_data.aw_salesman ";
			$order_by = "ORDER BY salesman_objects.name {$sort_direction}";
		}
		elseif ("lead_source" === $order)
		{
			$additional_joins .= "LEFT JOIN objects lead_source_objects on lead_source_objects.oid=aw_crm_customer_data.aw_lead_source ";
			$order_by = "ORDER BY lead_source_objects.name {$sort_direction}";
		}

		$this->additional_joins = $additional_joins;
		$this->sort_order = $order_by;
	}

	private function sort_obj($order, $direction)
	{
		$sort_dir = ($direction === "asc") ? obj_predicate_sort::ASC : obj_predicate_sort::DESC;

		$sortable_fields = array(
			"name" => array(obj_predicate_sort::ASC, "CL_CRM_COMPANY_CUSTOMER_DATA.buyer.name"),
			"lead_source" => array(obj_predicate_sort::ASC, "CL_CRM_COMPANY_CUSTOMER_DATA.sales_lead_source.name"),
			"last_call_time" => array(obj_predicate_sort::ASC, "CL_CRM_COMPANY_CUSTOMER_DATA.sales_last_call(CL_CRM_CALL).real_start"),
			"last_call_result" => array(obj_predicate_sort::ASC, "CL_CRM_COMPANY_CUSTOMER_DATA.sales_last_call(CL_CRM_CALL).result"),
			"last_call_maker" => array(obj_predicate_sort::ASC, "CL_CRM_COMPANY_CUSTOMER_DATA.sales_last_call(CL_CRM_CALL).real_maker"),
			"calls_made" => array(obj_predicate_sort::ASC, "sales_calls_made"),
			"salesman" => array(obj_predicate_sort::ASC, "CL_CRM_COMPANY_CUSTOMER_DATA.salesman.name")
		);
		$sort_by = $sortable_fields[$order][1];

		$this->sort_order = new obj_predicate_sort(array($sort_by => $sort_dir));
	}

	private function search_obj($limit)
	{
		$result = array();
		$filter = array("class_id" => CL_CRM_COMPANY_CUSTOMER_DATA);

		if ($this->p_seller and $this->p_buyer)
		{ // search relations where $seller OR $buyer
			$filter[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array (
					"CL_CRM_COMPANY_CUSTOMER_DATA.seller" => $this->p_seller ,
					"CL_CRM_COMPANY_CUSTOMER_DATA.buyer" => $this->p_buyer
				)
			));
		}
		else
		{
			// seller constraint
			if ($this->p_seller)
			{
				$filter["seller"] = $this->p_seller;
			}

			// buyer constraint
			if ($this->p_buyer)
			{
				$filter["buyer"] = $this->p_buyer;
			}
		}

		// category constraint
		if ($this->p_category)
		{
			$filter["CL_CRM_COMPANY_CUSTOMER_DATA.RELTYPE_CATEGORY"] = $this->p_category;
		}

		// company registration number constraint
		if ($this->p_reg_nr)
		{
			$filter[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array (
					"CL_CRM_COMPANY_CUSTOMER_DATA.buyer(CL_CRM_COMPANY).reg_nr" => "{$this->p_reg_nr}",
					"CL_CRM_COMPANY_CUSTOMER_DATA.buyer(CL_CRM_PERSON).reg_nr" => "{$this->p_reg_nr}"
				)
			));
		}

		// search params
		if (!empty($this->p_name))
		{
			$filter[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array (
					"CL_CRM_COMPANY_CUSTOMER_DATA.buyer(CL_CRM_COMPANY).name" => "{$this->p_name}",
					"CL_CRM_COMPANY_CUSTOMER_DATA.buyer(CL_CRM_PERSON).name" => "{$this->p_name}"
				)
			));
		}

		if (!empty($this->p_phone))
		{
			$filter[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array (
					"CL_CRM_COMPANY_CUSTOMER_DATA.buyer(CL_CRM_COMPANY).RELTYPE_PHONE.name" => "{$this->p_phone}",
					"CL_CRM_COMPANY_CUSTOMER_DATA.buyer(CL_CRM_PERSON).RELTYPE_PHONE.name" => "{$this->p_phone}"
				)
			));
		}

		if (!empty($this->p_salesman))
		{
			$filter["salesman"] = $this->p_salesman;
		}

		if (!empty($this->p_calls))
		{
			$filter["sales_calls_made"] = $this->p_calls;
		}

		if (!empty($this->p_lead_source))
		{
			$filter[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array (
					"CL_CRM_COMPANY_CUSTOMER_DATA.sales_lead_source(CL_CRM_COMPANY).name" => "{$this->p_lead_source}",
					"CL_CRM_COMPANY_CUSTOMER_DATA.sales_lead_source(CL_CRM_PERSON).name" => "{$this->p_lead_source}"
				)
			));
		}

		if (!empty($this->p_address))
		{
			$filter[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array (
					"CL_CRM_COMPANY_CUSTOMER_DATA.buyer(CL_CRM_COMPANY).RELTYPE_ADDRESS_ALT.name" => "{$this->p_address}",
					"CL_CRM_COMPANY_CUSTOMER_DATA.buyer(CL_CRM_PERSON).RELTYPE_ADDRESS_ALT.name" => "{$this->p_address}"
				)
			));
		}

		if (!empty($this->p_status))
		{
			$filter["sales_state"] = $this->p_status;
		}

		// pagination and limit
		if (false === $limit)
		{
			$result = new object_data_list(
				array_merge($filter),
				array(
					CL_CRM_COMPANY_CUSTOMER_DATA => array(new obj_sql_func(obj_sql_func::COUNT, "count" , "*"))
				)
			);
			$result = $result->arr();
			$result = reset($result);
			$result = $result["count"];
		}
		else
		{
			if ($limit instanceof obj_predicate_limit)
			{
				$filter[] = $limit;
			}

			if ($this->sort_order)
			{
				$filter[] = $this->sort_order;
			}

			$result = new object_data_list(
				$filter,
				array(
					CL_CRM_COMPANY_CUSTOMER_DATA => array("oid")
				)
			);
			$result = $result->arr();
		}

		return $result;
	}

	private function search_db($limit)
	{
		$result = array();
		$seller = $this->p_seller;
		$address_clid = CL_ADDRESS;
		$phone_clid = CL_CRM_PHONE;
		$crm_person_clid = CL_CRM_PERSON;
		$crm_company_clid = CL_CRM_COMPANY;
		$cro_clid = CL_CRM_COMPANY_CUSTOMER_DATA;
		$db = new db_connector();
		$db->init();
		$real_duration_constraint = $order_by = $limit_str = $group_by = "";
		$additional_joins = $this->additional_joins;


		// result format and limit
		if (false === $limit)
		{
			$select = "count(cro_objects.oid) as count";
		}
		else
		{
			if ($limit instanceof obj_predicate_limit)
			{
				$limit_start = $limit->get_from();
				$limit_count = $limit->get_per_page();
				$limit_str = "LIMIT {$limit_start},{$limit_count}";
			}

			$select = "cro_objects.oid as oid";
			$group_by = "GROUP BY cro_objects.oid";

			if ($this->sort_order)
			{
				$order_by = $this->sort_order;
			}
		}

		// customer status constraint
		if (empty($this->p_status))
		{
			$customer_status_constraint = "";
		}
		else
		{
			$customer_status_constraint = "aw_crm_customer_data.`aw_sales_status`= {$this->p_status} AND";
		}

		 // special case for address search
		if (!empty($this->p_address))
		{
			$q = <<<EOQ
SELECT
	{$select}

FROM
	objects address_objects
	INNER JOIN aliases address_aliases on address_aliases.target = address_objects.oid
	INNER JOIN objects customer_objects on customer_objects.oid = address_aliases.source
	INNER JOIN aw_crm_customer_data on aw_crm_customer_data.aw_buyer = customer_objects.oid
	{$additional_joins}
	INNER JOIN objects cro_objects on cro_objects.oid = aw_crm_customer_data.aw_oid

WHERE
	address_objects.`class_id`={$address_clid} AND
	address_objects.`name` LIKE '{$this->p_address}' AND
	(customer_objects.`class_id`={$crm_person_clid} OR customer_objects.`class_id`={$crm_company_clid}) AND
	cro_objects.`class_id`={$cro_clid} AND
	{$customer_status_constraint}
	aw_crm_customer_data.`aw_seller`={$seller} AND
	address_objects.`status` > 0 AND
	customer_objects.`status` > 0 AND
	cro_objects.`status` > 0

{$group_by}
{$order_by}
{$limit_str};
EOQ;
		}
		 // special case for phone number search
		elseif (!empty($this->p_phone))
		{
			$q = <<<EOQ
SELECT
	{$select}

FROM
	objects phone_objects
	INNER JOIN aliases phone_aliases on phone_aliases.target = phone_objects.oid
	INNER JOIN objects customer_objects on customer_objects.oid = phone_aliases.source
	INNER JOIN aw_crm_customer_data on aw_crm_customer_data.aw_buyer = customer_objects.oid
	{$additional_joins}
	INNER JOIN objects cro_objects on cro_objects.oid = aw_crm_customer_data.aw_oid

WHERE
	phone_objects.`class_id`={$phone_clid} AND
	phone_objects.`name` LIKE '{$this->p_phone}' AND
	(customer_objects.`class_id`={$crm_person_clid} OR customer_objects.`class_id`={$crm_company_clid}) AND
	cro_objects.`class_id`={$cro_clid} AND
	{$customer_status_constraint}
	aw_crm_customer_data.`aw_seller`= {$seller} AND
	(phone_aliases.`reltype` = 13 OR phone_aliases.`reltype` = 17) AND
	phone_objects.`status` > 0 AND
	customer_objects.`status` > 0 AND
	cro_objects.`status` > 0

{$group_by}
{$order_by}
{$limit_str};
EOQ;
		}
		else
		{
			throw new awex_crm_contacts_search_param("No parameters specified", self::PARAM_NONE);
		}

		// parse database result
		if (false === $limit)
		{
			$result = $db->db_fetch_field($q, "count");
		}
		else
		{
			$result = $db->db_fetch_array($q);
		}

		return $result;
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

class awex_crm_contacts_search extends awex_crm {}
class awex_crm_contacts_search_param extends awex_crm_contacts_search {}
