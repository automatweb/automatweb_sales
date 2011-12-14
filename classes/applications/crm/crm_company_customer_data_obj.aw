<?php

class crm_company_customer_data_obj extends _int_object
{
	const CLID = 1028;

	const SALESSTATE_NEW = 1;
	const SALESSTATE_LEAD = 2;
	const SALESSTATE_NEWCALL = 3;
	const SALESSTATE_PRESENTATION = 4;
	const SALESSTATE_SALE = 5;
	const SALESSTATE_REFUSED = 6;
	const SALESSTATE_UNSUITABLE = 7;
	const SALESSTATE_ONHOLD = 8;
	// new sales state constants also have to be added to self::$sales_state_names array in self::sales_state_names() method

	const PARTY_NAME_SEPARATOR = " => ";

	public static $customer_class_ids = array(CL_CRM_COMPANY, CL_CRM_PERSON);

	private static $sales_state_names = array();

	public function get_discounts()
	{
		return discount_obj::get_discounts();
	}

	public function awobj_set_sales_state($state)
	{
		$states = self::sales_state_names();
		if (!isset($states[$state]))
		{
			throw new awex_crm_cust_data_sales_state("Not a valid sales state: '{$state}'");
		}
		$this->set_prop("sales_state", $state);
	}

/* TODO: kui korra on osapooled kinnitatud siis muuta enam ei saa
	public function awobj_set_seller($oid)
	{
	}

	public function awobj_set_buyer($oid)
	{
	}
 */

	/** Customer's sales state names or name
	@attrib api=1 params=pos
	@param state optional type=int
		State for which to get name. One of SALESSTATE constant values.
	@comment
	@returns mixed
		Array of constant values (keys) and names (array values) if $state parameter not specified. String name corresponding to that state if $state parameter given. Names are in currently active language. Empty string if invalid state parameter given.
	**/
	public static function sales_state_names($state = null)
	{
		if (empty(self::$sales_state_names))
		{
			self::$sales_state_names = array(
				self::SALESSTATE_NEW => t("Uus"),
				self::SALESSTATE_LEAD => t("Soovitus"),
				self::SALESSTATE_NEWCALL => t("Uus k&otilde;ne"),
				self::SALESSTATE_PRESENTATION => t("Esitlus"),
				self::SALESSTATE_SALE => t("Ostja"),
				self::SALESSTATE_UNSUITABLE => t("Sobimatu kontakt"),
				self::SALESSTATE_REFUSED => t("Keeldund kontaktist"),
				self::SALESSTATE_ONHOLD => t("Arhiveeritud")
			);
		}

		if (!isset($state))
		{
			$names = self::$sales_state_names;
		}
		elseif (is_scalar($state) and isset(self::$sales_state_names[$state]))
		{
			$names = self::$sales_state_names[$state];
		}
		else
		{
			$names = "";
		}

		return $names;
	}

	public function get_sales_case($create = false)
	{
		$resource_mgr = mrp_workspace_obj::get_hr_manager(new object($this->prop("seller")));
		$list = new object_list(array(
			"class_id" => CL_MRP_CASE,
			"workspace" => $resource_mgr->id(),
			"customer_relation" => $this->id()
		));

		if ($list->count() < 1 and $create)
		{
			$case = $resource_mgr->create_case(new object($this->id()));
		}
		elseif ($list->count() > 1)
		{
			trigger_error("More than one sales case associated with customer relation " . $this->id(), E_USER_WARNING);
			$case = $list->begin();
		}
		elseif ($list->count() === 1)
		{
			$case = $list->begin();
		}
		else
		{
			$case = null;
		}

		return $case;
	}

	/** Get seller object
		@attrib api=1 params=pos
		@comment
		@returns CL_CRM_COMPANY|CL_CRM_PERSON|NULL
		@errors
	**/
	public function get_seller()
	{
		if (object_loader::can("", $this->prop("seller")))
		{
			return new object($this->prop("seller"));
		}
	}

	/** Get buyer object
		@attrib api=1 params=pos
		@comment
		@returns CL_CRM_COMPANY|CL_CRM_PERSON|NULL
		@errors
	**/
	public function get_buyer()
	{
		if (object_loader::can("", $this->prop("buyer")))
		{
			return new object($this->prop("buyer"));
		}
	}

	public function get_bills()
	{
		$filter = array(
			"class_id" => CL_CRM_BILL,
			"customer" => $this->prop("buyer"),
			"CL_CRM_BILL.RELTYPE_IMPL" => $this->prop("seller")
		);
		return new object_list($filter);
	}

	public function get_price_lists()
	{
		$filter = array(
			"class_id" => CL_SHOP_PRICE_LIST,
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_SHOP_PRICE_LIST.RELTYPE_ORG" => $this->prop("buyer"),
					"CL_SHOP_PRICE_LIST.RELTYPE_PERSON" => $this->prop("buyer"),
				),
			))
		);
		return new object_list($filter);
	}

	public function get_orders()
	{
		$filter = array(
			"class_id" => CL_SHOP_ORDER,
			"orderer_company" => $this->prop("buyer"),
			"seller_company" => $this->prop("seller"),
		);
		return new object_list($filter);
	}

	public function get_sell_orders()
	{
		$filter = array(
			"class_id" => CL_SHOP_SELL_ORDER,
			"purchaser" => $this->prop("buyer"),
//			"seller_company" => $this->prop("seller"),
		);
		return new object_list($filter);
	}

	public function get_recalls()
	{
		$filter = array(
			"class_id" => CL_SHOP_ORDER
		);
		return new object_list($filter);
	}

	public function get_delivery_notes()
	{
		$filter = array(
			"class_id" => CL_SHOP_DELIVERY_NOTE,
			"customer" => $this->prop("buyer"),
			"impl" => $this->prop("seller")
		);
		return new object_list($filter);
	}

	public function get_locations()
	{
		// USE classes like CL_COUNTRY, CL_COUNTRY_ADMINISTRATIVE_UNIT, CL_COUNTRY_CITY etc
		return new object_list();
	}

	/** Adds a customer category to this customer relation. Customer will be in that category iow.
		@attrib api=1 params=pos
		@param category type=CL_CRM_CATEGORY
		@returns void
		@errors
			awex_obj_state_new if this object not saved
			awex_obj_type if invalid/unsaved category object given
		@qc date=20101027 standard=aw3
	**/
	public function add_category(object $category)
	{
		if (!$this->is_saved())
		{
			throw new awex_obj_state_new();
		}

		if (!$category->is_saved() or !$category->is_a(CL_CRM_CATEGORY))
		{
			throw new awex_obj_type("Invalid category {$category}");
		}

		//TODO: mis toimub kui topelt yritada lisada kategooriat?
		$this->connect(array(
			"to" => $category,
			"reltype" => "RELTYPE_CATEGORY"
		));
	}

	/** Removes a customer category from this customer relation. Customer won't be in that category thereby
		@attrib api=1 params=pos
		@param category type=CL_CRM_CATEGORY
		@returns void
		@errors
			awex_obj_state_new if this object not saved
			awex_obj_type if invalid/unsaved category object given
		@qc date=20110512 standard=aw3
	**/
	public function remove_category(object $category)
	{
		if (!$this->is_saved())
		{
			throw new awex_obj_state_new();
		}

		if (!$category->is_saved() or !$category->is_a(CL_CRM_CATEGORY))
		{
			throw new awex_obj_type("Invalid category {$category}");
		}

		//TODO: mis toimub kui yritada eemaldada kategooriat milles klient pole?
		$this->disconnect(array(
			"from" => $category,
			"reltype" => "RELTYPE_CATEGORY"
		));
	}

	public function get_customer_categories()
	{
		if(is_oid($this->prop("buyer")) && is_oid($this->prop("seller")))
		{
			$ids = obj($this->prop("seller"))->get_all_org_customer_categories();
			arr(array(
				"class_id" => CL_CRM_CATEGORY,
				"oid" => $ids,
				"CL_CRM_CATEGORY.RELTYPE_CUSTOMER" => $this->prop("buyer"),
			));
			return !empty($ids) ? new object_list(array(
				"class_id" => CL_CRM_CATEGORY,
				"oid" => $ids,
				"CL_CRM_CATEGORY.RELTYPE_CUSTOMER" => $this->prop("buyer"),
			)) : new object_list();
		}
		else
		{
			return new object_list();
		}
	}

	public function save($check_state = false)
	{
		if (!$this->prop("sales_state"))
		{
			$this->set_prop("sales_state", self::SALESSTATE_NEW);
		}

		if (self::SALESSTATE_NEW == $this->prop("sales_state") and is_oid($this->prop("sales_lead_source")))
		{
			$this->awobj_set_sales_state(self::SALESSTATE_LEAD);
		}

		if (!$this->is_saved())
		{
			if (!$this->prop("cust_contract_creator"))
			{
				$this->set_prop("cust_contract_creator", aw_global_get("uid_oid"));
			}

			if (!$this->prop("cust_contract_date"))
			{
				$this->set_prop("cust_contract_date", time());
			}
		}

		if (!$this->name() and $this->prop("seller") and $this->prop("buyer"))
		{
			$this->set_name($this->prop_str("seller.name") . crm_company_customer_data_obj::PARTY_NAME_SEPARATOR . $this->prop_str("buyer.name"));
		}

		$r = parent::save($check_state);
	}
}

/** Generic customer relation error **/
class awex_crm_cust_data extends awex_crm {}

/** Unexpected sales state **/
class awex_crm_cust_data_sales_state extends awex_crm_cust_data {}

?>
