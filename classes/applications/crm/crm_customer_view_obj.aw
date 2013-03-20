<?php

class crm_customer_view_obj extends _int_object
{
	const CLID = 1808;
	
	private $customers_table_fields;
	
	public function get_customers_table_fields()
	{
		if (!isset($this->customers_table_fields))
		{
			$this->customers_table_fields = array(
				"name" => array
				(
					"ord" => 100,
					"active" => true,
					"name" => "name",
					"caption" => t("Kliendi nimi"),
					"original_caption" => t("Kliendi nimi"),
					"sortable" => true,
					"chgbgcolor" => "cutcopied",
				),
				"address" => array
				(
					"ord" => 200,
					"active" => true,
					"name" => "address",
					"caption" => t("Aadress ja &uuml;ldkontaktid"),
					"original_caption" => t("Aadress ja &uuml;ldkontaktid"),
					"sortable" => false,
					"chgbgcolor" => "cutcopied",
				),
				"customer_rel_order" => array
				(
					"ord" => 300,
					"active" => true,
					"name" => "customer_rel_order",
					"caption" => t("Suhte suund"),
					"original_caption" => t("Suhte suund"),
					"sortable" => true,
					"chgbgcolor" => "cutcopied",
				),
				"buyer_people" => array
				(
					"ord" => 400,
					"active" => true,
					"name" => "buyer_people",
					"caption" => t("Ostja isikud"),
					"original_caption" => t("Ostja isikud"),
					"sortable" => true,
					"chgbgcolor" => "cutcopied",
				),
				"seller_people" => array
				(
					"ord" => 500,
					"active" => true,
					"name" => "seller_people",
					"caption" => t("M&uuml;&uuml;ja isikud"),
					"original_caption" => t("M&uuml;&uuml;ja isikud"),
					"sortable" => true,
					"chgbgcolor" => "cutcopied",
				),
				"pop" => array
				(
					"ord" => 600,
					"active" => true,
					"name" => "pop",
					"caption" => t("Valikud"),
					"original_caption" => t("Valikud"),
					"sortable" => false,
					"chgbgcolor" => "cutcopied",
				),
			);
			
			$custom_table_fields = $this->meta("configuration_customers_table");
			if (!empty($custom_table_fields) && is_array($custom_table_fields))
			{
				foreach ($custom_table_fields as $field_name => $field)
				{
					if (isset($this->customers_table_fields[$field_name]))
					{
						$this->customers_table_fields[$field_name] = array_merge($this->customers_table_fields[$field_name], $field);
					}
				}
			}
			
			uasort($this->customers_table_fields, array($this, "__customers_table_field_compare"));
		}

		return $this->customers_table_fields;
	}

	private function __customers_table_field_compare ($a, $b)
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
  
	public function get_customer_categories_hierarchy()
	{
		$owner = obj($this->prop("company"), null, crm_company_obj::CLID);
		return $owner->get_customer_categories_hierarchy();
	}
	
	public function get_customer_categories_for_filter()
	{
		$configurations = $this->meta("configuration_customers_filter_customer_category");
		
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
			"organization" => $this->prop("company"),
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
			"customer_category",
			"state",
		);
		
		$filter = array();
		foreach($properties as $property)
		{
			$filter[$property] = $this->default_filter_for_property("customers_filter_{$property}");
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
		
		switch ($property)
		{
			case "customers_filter_customer_category":
				$default_filter = array();
				foreach($configurations as $id => $configuration)
				{
					if((!empty($configuration["use_in_filter"]) or !empty($configurations[$configuration["parent"]]["use_subcategories_in_filter"])) and !empty($configuration["checked_by_default"]))
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
}

/** Generic crm_customer_view_obj exception **/
class awex_crm_customer_view_obj extends awex_obj {}
