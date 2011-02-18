<?php

// Organization's partner/customer category
// Categories are stored under organization object with object parent relation.
// Subcategories have main categories as their parent

class crm_category_obj extends _int_object
{
	const CLID = 483;

	const TYPE_GENERIC = 1;
	const TYPE_BUYER = 2;
	const TYPE_SELLER = 3;

	private static $type_names = array(); // a cache type variable

	/** Returns list of category type options
	@attrib api=1 params=pos
	@param type type=int default=NULL
		Type constant value to get name for, one of crm_category_obj::TYPE_*
	@returns array
		Format option value => human readable name, if type parameter set,
		array with one element returned and empty array when that type not found.
	**/
	public static function type_names($type = null)
	{
		if (empty(self::$type_names))
		{
			self::$type_names = array(
				self::TYPE_GENERIC => t("&Uuml;ldine kliendikategooria"),
				self::TYPE_BUYER => t("Ostjate kategooria"),
				self::TYPE_SELLER => t("M&uuml;&uuml;jate kategooria")
			);
		}

		if (isset($type))
		{
			if (isset(self::$type_names[$type]))
			{
				$type_names = array($type => self::$type_names[$type]);
			}
			else
			{
				$type_names = array();
			}
		}
		else
		{
			$type_names = self::$type_names;
		}

		return $type_names;
	}

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

	public function awobj_set_category_type($type)
	{
		if (!self::type_names($type))
		{
			throw new awex_obj_type("Given category type " . var_export($type, true) . " is not a valid category type");
		}

		$this->set_prop("category_type", $type);
	}
}
