<?php

class crm_customers_websearch_obj extends _int_object
{
	const CLID = 1825;
	
	const TYPE_SELLER = 0;
	const TYPE_BUYER = 1;
	
	const SEARCH_TYPE_OR = 1;
	const SEARCH_TYPE_AND = 2;
	
	public function awobj_get_skill_search_type()
	{
		$value = parent::prop("skill_search_type");
		if ($value === null)
		{
			$value = crm_customers_websearch_obj::SEARCH_TYPE_OR;
		}
		return $value;
	}
	
	public function get_customer_categories()
	{
		$categories = new object_list();
		if (object_loader::can("", $this->prop("owner")))
		{
			$owner =  obj($this->prop("owner"));
			if ($owner->is_a(crm_company_obj::CLID))
			{
				$categories = $owner->get_customer_categories(object_loader::can("", $this->prop("root_category")) ? obj($this->prop("root_category")) : null);
			}
		}
		return $categories;
	}
	
	public function get_customers()
	{
		$types = $this->prop("type");
		if (!object_loader::can("", $this->prop("owner")) || empty($types))
		{
			return new object_list();
		}
		
		$owner = obj($this->prop("owner"));
		
		$filter = array(
			"class_id" => crm_company_customer_data_obj::CLID,
		);
		
		if (automatweb::$request->arg_isset("category"))
		{
			$filter["CL_CRM_COMPANY_CUSTOMER_DATA.RELTYPE_CATEGORY"] = array();
			foreach ((array)automatweb::$request->arg("category") as $category)
			{
				if (object_loader::can("", $category))
				{
					$root_category = obj($category, null, crm_category_obj::CLID);
					$filter["CL_CRM_COMPANY_CUSTOMER_DATA.RELTYPE_CATEGORY"][] = $root_category->id;
					$filter["CL_CRM_COMPANY_CUSTOMER_DATA.RELTYPE_CATEGORY"] = array_merge($filter["CL_CRM_COMPANY_CUSTOMER_DATA.RELTYPE_CATEGORY"], $owner->get_customer_categories($root_category)->ids());
				}
			}
		}
		if (empty($filter["CL_CRM_COMPANY_CUSTOMER_DATA.RELTYPE_CATEGORY"]) && $this->prop("root_category") && object_loader::can("", $this->prop("root_category")))
		{
			$root_category = obj($this->prop("root_category"), null, crm_category_obj::CLID);
			$filter["CL_CRM_COMPANY_CUSTOMER_DATA.RELTYPE_CATEGORY"] = array_merge(array($root_category->id), $owner->get_customer_categories($root_category)->ids());
		}

		$conditions = array(
			"class_id" => crm_company_customer_data_obj::CLID,
			"{this}" => $owner->id,
		);

		if (automatweb::$request->arg_isset("name") && automatweb::$request->arg("name"))
		{
			$conditions["{that}.name"] = sprintf("%%%s%%", automatweb::$request->arg("name"));
		}

		if (automatweb::$request->arg_isset("skill") && automatweb::$request->arg("skill"))
		{
			if ($this->awobj_get_skill_search_type() == self::SEARCH_TYPE_AND)
			{
				$employers = null;
				foreach((array)automatweb::$request->arg("skill") as $skill)
				{	
					$work_relations = new object_data_list(
						array(
							"class_id" => crm_person_work_relation_obj::CLID,
							"employee.RELTYPE_HAS_SKILL.skill" => $skill,
						),
						array(
							crm_person_work_relation_obj::CLID => array("employer"),
						)
					);
					$employers = $employers === null ? $work_relations->get_element_from_all("employer") : array_intersect($employers, $work_relations->get_element_from_all("employer"));
				}
			}
			else
			{
				$work_relations = new object_data_list(
					array(
						"class_id" => crm_person_work_relation_obj::CLID,
						"employee.RELTYPE_HAS_SKILL.skill" => automatweb::$request->arg("skill"),
					),
					array(
						crm_person_work_relation_obj::CLID => array("employer"),
					)
				);
				$employers = $work_relations->get_element_from_all("employer");
			}
			$conditions["{that}"] = !empty($employers) ? $employers : -1;
		}

		$filter[] = new object_list_filter(array(
			"logic" => "OR",
			"conditions" => array(
				new object_list_filter(array(
					"logic" => "AND",
					"conditions" => in_array(self::TYPE_BUYER, $types) ? $this->__conditions($conditions, self::TYPE_BUYER) : array()
				)),
				new object_list_filter(array(
					"logic" => "AND",
					"conditions" => in_array(self::TYPE_SELLER, $types) ? $this->__conditions($conditions, self::TYPE_SELLER) : array()
				)),
			)
		));
		
		return new object_list($filter);
	}
	
	private function __conditions($__conditions, $type = self::TYPE_BUYER)
	{
		$conditions = array();
		foreach($__conditions as $key => $value)
		{
			$conditions[str_replace(array("{this}", "{that}"), $type == self::TYPE_SELLER ? array("buyer", "seller") : array("seller", "buyer"), $key)] = $value;
		}
		return $conditions;
	}
}

/** Generic crm_customers_webview_obj exception **/
class awex_crm_customers_websearch_obj extends awex_obj {}
