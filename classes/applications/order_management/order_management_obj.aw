<?php

class order_management_obj extends management_base_obj
{
	const CLID = 1816;
	
	const FILTER_DATE_CURRENT_DAY = 1;
	const FILTER_DATE_PREVIOUS_DAY = 2;
	const FILTER_DATE_CURRENT_WEEK = 3;
	const FILTER_DATE_PREVIOUS_WEEK = 4;
	const FILTER_DATE_CURRENT_MONTH = 5;
	const FILTER_DATE_PREVIOUS_MONTH = 6;
	const FILTER_DATE_CURRENT_QUARTER = 7;
	const FILTER_DATE_PREVIOUS_QUARTER = 8;
	const FILTER_DATE_CURRENT_YEAR = 9;
	const FILTER_DATE_PREVIOUS_YEAR = 10;
	
	private $orders_table_fields;
 
	public function get_orders($filter = array())
	{	
		if (empty($filter))
		{
			$filter = $this->default_filter();
		}
		
		$date_filter = null;
		if (!empty($filter["date_from"]["date"]) && !empty($filter["date_to"]["date"])) {
			$date_filter = new obj_predicate_compare(obj_predicate_compare::BETWEEN_INCLUDING,
				datepicker::get_timestamp($filter["date_from"]),
				datepicker::get_timestamp($filter["date_to"]) + 24*3600 - 1);
		} elseif (!empty($filter["date_from"]["date"])) {
			$date_filter = new obj_predicate_compare(obj_predicate_compare::GREATER_OR_EQ, datepicker::get_timestamp($filter["date_from"]));
		} elseif (!empty($filter["date_to"]["date"])) {
			$date_filter = new obj_predicate_compare(obj_predicate_compare::LESS_OR_EQ, datepicker::get_timestamp($filter["date_to"]));
		}
		
		foreach (array("customer_category", "order_sources", "state", "order_state") as $filter_type)
		{		
			if (isset($filter[$filter_type]) && is_array($filter[$filter_type]))
			{
				foreach ($filter[$filter_type] as $key => $value)
				{
					if (empty($value))
					{
						unset($filter[$filter_type][$key]);
					}
				}
			}
		}
		
		$object_list_filter = array(
			"class_id" => mrp_case_obj::CLID,
			"name" => isset($filter["name"]) ? "%".$filter["name"]."%" : null,
			"customer_relation.seller" => $this->prop("owner"),
			"customer_relation.RELTYPE_CATEGORY" => !empty($filter["customer_category"]) ? $filter["customer_category"] : null,
			"customer_relation.buyer.name" => !empty($filter["customer_name"]) ? "%".$filter["customer_name"]."%" : null,
			"order_source" => !empty($filter["order_sources"]) ? $filter["order_sources"] : null,
			"state" => !empty($filter["state"]) ? $filter["state"] : null,
			"order_state" => !empty($filter["order_state"]) ? $filter["order_state"] : null,
			// FIXME: Create a separate date field!
			"created" => $date_filter
		);
		
		foreach(array_keys($object_list_filter) as $i)
		{
			if ($object_list_filter[$i] === null)
			{
				unset($object_list_filter[$i]);
			}
		}

		return new object_list($object_list_filter);
	}
  
	public function get_customer_categories()
	{
		$owner = obj($this->prop("owner"), null, crm_company_obj::CLID);
		// TODO: Should we only return certain types?
		return $owner->get_customer_categories();
	}
  
	public function get_customer_categories_hierarchy()
	{
		$owner = obj($this->prop("owner"), null, crm_company_obj::CLID);
		return $owner->get_customer_categories_hierarchy();
	}
	
	public function get_customer_categories_for_filter()
	{
		$configurations = $this->meta("configuration_orders_filter_customer_category");
		
		$use_in_filter = array();
		$use_subcategories_in_filter = array();
		
		foreach($configurations as $id => $configuration)
		{
			if (!empty($configuration["use_in_filter"]))
			{
				$use_in_filter[] = $id;
			}
			if (!empty($configuration["use_subcategories_in_filter"]))
			{
				$use_subcategories_in_filter[] = $id;
			}
		}
		
		if (empty($use_in_filter) && empty($use_subcategories_in_filter))
		{
			return new object_list();
		}
		
		return new object_list(array(
			"class_id" => crm_category_obj::CLID,
			"organization" => $this->prop("owner"),
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"oid" => $use_in_filter,
					"parent_category" => $use_subcategories_in_filter
				),
			))
		));
	}
	
	public function default_filter($property = null)
	{
		return $property === null ? $this->default_filter_for_all() : $this->default_filter_for_property($property);
	}
	
	private function default_filter_for_all()
	{
		$properties = array(
			"sales_channel",
			"time_period",
			"customer_category",
			"order_sources",
			"order_state",
			"state",
			"date_from",
			"date_to",
		);
		
		$filter = array();
		foreach($properties as $property)
		{
			$filter[$property] = $this->default_filter_for_property("orders_filter_{$property}");
		}
		
		return $filter;
	}
	
	private function default_filter_for_property($property)
	{
		if ($property === "orders_filter_date_from" || $property === "orders_filter_date_to")
		{
			$time_period_filter = $this->default_filter_for_property("orders_filter_time_period");
			$date_from = $date_to = null;
			switch(reset($time_period_filter))
			{
				case self::FILTER_DATE_CURRENT_DAY:
					$date_from = mktime(0, 0, 0, date("n"), date("j"), date("Y"));
					$date_to = mktime(23, 59, 59, date("n"), date("j"), date("Y"));
					break;

				case self::FILTER_DATE_PREVIOUS_DAY:
					$date_from = mktime(0, 0, 0, date("n"), date("j") -1, date("Y"));
					$date_to = mktime(23, 59, 59, date("n"), date("j") -1, date("Y"));
					break;

				case self::FILTER_DATE_CURRENT_WEEK:
					$date_from = mktime(0, 0, 0, date("n"), date("j") - date("N") +1, date("Y"));
					$date_to = mktime(23, 59, 59, date("n"), date("j") - date("N") +7, date("Y"));
					break;

				case self::FILTER_DATE_PREVIOUS_WEEK:
					$date_from = mktime(0, 0, 0, date("n"), date("j") - date("N") -6, date("Y"));
					$date_to = mktime(23, 59, 59, date("n"), date("j") - date("N"), date("Y"));
					break;

				case self::FILTER_DATE_CURRENT_MONTH:
					$date_from = mktime(0, 0, 0, date("n"), 1, date("Y"));
					$date_to = mktime(23, 59, 59, date("n") +1, 0, date("Y"));
					break;

				case self::FILTER_DATE_PREVIOUS_MONTH:
					$date_from = mktime(0, 0, 0, date("n") -1, 1, date("Y"));
					$date_to = mktime(23, 59, 59, date("n"), 0, date("Y"));
					break;

				case self::FILTER_DATE_CURRENT_QUARTER:
					$date_from = mktime(0, 0, 0, date("n") - ((date("n") - 1) % 3), 1, date("Y"));
					$date_to = mktime(23, 59, 59, date("n") - ((date("n") - 1) % 3) + 3, 0, date("Y"));
					break;

				case self::FILTER_DATE_PREVIOUS_QUARTER:
					$date_from = mktime(0, 0, 0, date("n") - ((date("n") - 1) % 3) - 3, 1, date("Y"));
					$date_to = mktime(23, 59, 59, date("n") - ((date("n") - 1) % 3), 0, date("Y"));
					break;

				case self::FILTER_DATE_CURRENT_YEAR:
					$date_from = mktime(0, 0, 0, 1, 1, date("Y"));
					$date_to = mktime(23, 59, 59, 12, 31, date("Y"));
					break;

				case self::FILTER_DATE_PREVIOUS_YEAR:
					$date_from = mktime(0, 0, 0, 1, 1, date("Y") - 1);
					$date_to = mktime(23, 59, 59, 12, 31, date("Y") - 1);
					break;
			}
			$property = substr($property, strlen("orders_filter_"));
			return $$property !== null ? array(
				"date" => date("d.m.Y", $$property),
				"time" => date("H:i", $$property)
			) : null;
		}
		
		$configurations = $this->meta("configuration_{$property}");
		if (empty($configurations))
		{
			return null;
		}
		
		switch ($property)
		{
			case "orders_filter_customer_category":
				$default_filter = array();
				foreach($configurations as $id => $configuration)
				{
					if(!empty($configuration["use_in_filter"]) and !empty($configuration["checked_by_default"]))
					{
						$default_filter[$id] = $id;
					}
				}
				break;

			case "orders_filter_order_sources":
				$default_filter = array();
				foreach($configurations as $id => $configuration)
				{
					if(!empty($configuration["checked_by_default"]))
					{
						$default_filter[$id] = $id;
					}
				}
				break;
			
			default:
				$default_filter = $configurations;
		}
		
		return $default_filter;
	}

	public function get_orders_table_fields()
	{
		if (!isset($this->orders_table_fields))
		{
			$this->orders_table_fields = array(
				"name" => array
				(
					"ord" => 10,
					"active" => true,
					"name" => "name",
					"caption" => t("Nimi"),
					"original_caption" => t("Nimi"),
				),
				"customer_name" => array
				(
					"ord" => 20,
					"active" => true,
					"name" => "customer_name",
					"caption" => t("Kliendi nimi"),
					"original_caption" => t("Kliendi nimi"),
				),
				"customer_relation" => array
				(
					"ord" => 30,
					"active" => true,
					"name" => "customer_relation",
					"caption" => t("Kliendisuhe"),
					"original_caption" => t("Kliendisuhe"),
				),
				"customer_manager" => array
				(
					"ord" => 30,
					"active" => true,
					"name" => "customer_manager",
					"caption" => t("Kliendihaldur"),
					"original_caption" => t("Kliendihaldur"),
				),
				"order_state" => array
				(
					"ord" => 37,
					"active" => true,
					"name" => "order_state",
					"caption" => t("Staatus"),
					"original_caption" => t("Staatus"),
				),
				"state" => array
				(
					"ord" => 35,
					"active" => true,
					"name" => "state",
					"caption" => t("Tootmise staatus"),
					"original_caption" => t("Tootmise staatus"),
				),
				"date" => array
				(
					"ord" => 40,
					"active" => true,
					"name" => "date",
					"caption" => t("Kuup&auml;ev"),
					"original_caption" => t("Kuup&auml;ev"),
				),
				"total" => array
				(
					"ord" => 50,
					"active" => true,
					"name" => "total",
					"caption" => t("Summa"),
					"original_caption" => t("Summa"),
				),
				"total_raw" => array
				(
					"ord" => 100,
					"active" => true,
					"name" => "total_raw",
					"caption" => t("Kateteta"),
					"original_caption" => t("Kateteta"),
					"parent" => "total",
				),
				"total_covers" => array
				(
					"ord" => 200,
					"active" => true,
					"name" => "total_covers",
					"caption" => t("Katted"),
					"original_caption" => t("Katted"),
					"parent" => "total",
				),
				"total_total" => array
				(
					"ord" => 300,
					"active" => true,
					"name" => "total_total",
					"caption" => t("Kokku"),
					"original_caption" => t("Kokku"),
					"parent" => "total",
				),
			);
			
			$custom_table_fields = $this->meta("configuration_orders_table");
			if (!empty($custom_table_fields) && is_array($custom_table_fields))
			{
				foreach ($custom_table_fields as $field_name => $field)
				{
					if (isset($this->orders_table_fields[$field_name]))
					{
						$this->orders_table_fields[$field_name] = array_merge($this->orders_table_fields[$field_name], $field);
					}
				}
			}
			
			uasort($this->orders_table_fields, array($this, "__orders_table_field_compare"));
		}

		return $this->orders_table_fields;
	}

	private function __orders_table_field_compare ($a, $b)
	{
		if (empty($a["parent"]) && !empty($b["parent"]))
		{
			return -1;
		}

		if (!empty($a["parent"]) && empty($b["parent"]))
		{
			return 1;
		}

		return $a["ord"] - $b["ord"];
	}
	
	function create_order (object $customer = null)
	{
		$owner = obj($this->prop("owner"), null, crm_company_obj::CLID);
		$mrp_workspace = obj(mrp_workspace_obj::get_hr_manager($owner)->id, array(), mrp_workspace_obj::CLID);
		$customer_relation = null;
		if (is_object($customer) && is_object($owner))
		{
			$customer_relation = $customer->find_customer_relation($owner, true);
		}
		$order = $mrp_workspace->create_case($customer_relation);
		return $order;
	}
	
	function get_order_sources()
	{
		$ol = new object_list(array(
			"class_id" => order_source_obj::CLID,
			"parent" => $this->id(),
		));
		return $ol;
	}
	
	/**
		@attrib api=1

		@returns object_list
	**/
	function get_price_component_category_list()
	{
		return $this->is_saved() ? new object_list(array(
			"class_id" => price_component_category_obj::CLID,
			"parent" => $this->prop("price_component_categories_folder"),
		)) : new object_list();
	}
	
	/**
	@attrib api=1 params=name

	@param category optional type=int/array/string
		The OIDs of the category of price components to be returned. If 'undefined' given, price components without category set will be returned. May give multiple categories.

	@returns object_list
		Returns an object list of all price component objects defined for current crm_sales object. If current crm_sales object is not yet saved empty object list is returned.
	**/
	public function get_price_component_list($additional_predicates = array())
	{
		$predicates = array(
			"class_id" => price_component_obj::CLID,
			"application" => $this->id(),
			"type" => new obj_predicate_not(price_component_obj::TYPE_NET_VALUE),
			new obj_predicate_sort(array(
				"name" => "ASC"
			)),
		);
		
		foreach ($additional_predicates as $predicate_key => $predicate)
		{
			switch ($predicate_key)
			{
				case "category":
					$predicate = (array)$predicate;
					if (in_array("undefined", $predicate))
					{
						$predicates[] = new object_list_filter(array(
							"logic" => "OR",
							"conditions" => array(
								"category" => $predicate,
								new object_list_filter(array(
									"logic" => "OR",
									"conditions" => array(
										"category" => new obj_predicate_compare(obj_predicate_compare::NULL),
									)
								)),
								new object_list_filter(array(
									"logic" => "OR",
									"conditions" => array(
										"category" => 0,
									)
								)),
							),
						));
					}
					else
					{
						$predicates[$predicate_key] = $predicate;
					}
					break;

				default:
					$predicates[$predicate_key] = $predicate;
			}
		}

		$ol = is_oid($this->id()) ? new object_list($predicates) : new object_list();
		return $ol;
	}
	
	function get_email_templates()
	{
		return new object_list(array(
			"class_id" => CL_MESSAGE_TEMPLATE,
			"parent" => $this->prop("email_templates_folder"),
		));
	}
}

/** Generic order_management_obj exception **/
class awex_order_management_obj extends awex_management_base_obj {}
