<?php

class order_management_obj extends management_base_obj
{
	const CLID = 1816;
	
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
		
		foreach (array("customer_category", "order_source", "state") as $filter_type)
		{		
			if (isset($filter[$filter_type]) && is_array($filter[$filter_type]))
			{
				foreach ($filter[$filter_type] as $key => $value)
				{
					if (!is_oid($value))
					{
						unset($filter[$filter_type][$key]);
					}
				}
			}
		}

		return new object_list(array(
			"class_id" => mrp_case_obj::CLID,
			"name" => isset($filter["name"]) ? "%".$filter["name"]."%" : null,
			"customer_relation.seller" => $this->prop("owner"),
			"customer_relation.RELTYPE_CATEGORY" => !empty($filter["customer_category"]) ? $filter["customer_category"] : null,
			"customer_relation.buyer.name" => !empty($filter["customer_name"]) ? "%".$filter["customer_name"]."%" : null,
			"order_source" => !empty($filter["order_source"]) ? $filter["order_source"] : null,
			"state" => !empty($filter["state"]) ? $filter["state"] : null,
			// FIXME: Create a separate date field!
			"created" => $date_filter
		));
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
		$configurations = $this->meta("configuration_{$property}");
		if (empty($configurations))
		{
			return null;
		}
		
		$default_filter = array();
		foreach($configurations as $id => $configuration)
		{
			if(!empty($configuration["checked_by_default"]))
			{
				$default_filter[$id] = $id;
			}
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
				"state" => array
				(
					"ord" => 35,
					"active" => true,
					"name" => "state",
					"caption" => t("Staatus"),
					"original_caption" => t("Staatus"),
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
}

/** Generic order_management_obj exception **/
class awex_order_management_obj extends awex_management_base_obj {}
