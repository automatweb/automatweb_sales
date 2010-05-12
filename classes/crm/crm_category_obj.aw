<?php

// Organization's partner/customer category
// Categories are stored under organization object with object parent relation.
// Subcategories have main categories as their parent


class crm_category_obj extends _int_object
{
	/**
		@attrib api=1 params=pos
		@return object_list
	**/
	public function get_customer_list()
	{
		$ol = new object_list(array(
			"class_id" => crm_company_customer_data_obj::$customer_class_ids,
			"CL_CRM_COMPANY_CUSTOMER_DATA.buyer" => new obj_predicate_prop("oid"),
			"CL_CRM_COMPANY_CUSTOMER_DATA.RELTYPE_CATEGORY" => $this->id()
		));
		return $ol;
	}

	/**
		@attrib api=1 params=pos
		@return object_list
	**/
	public function get_subcategories()
	{ //TODO currently returns only immediate subcategories, need to return all
		if ($this->is_saved())
		{
			$filter = array(
				"class_id" => CL_CRM_CATEGORY,
				"parent_category" => $this->id(),
				"organization" => $this->prop("organization")
			);
		}
		else
		{
			$filter = null;
		}

		$list = new object_list($filter);
		return $list;
	}
}
?>
