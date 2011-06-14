<?php

class crm_sales_price_component_obj extends _int_object
{
	const CLID = 1712;

	const TYPE_NET_VALUE = 1;
	const TYPE_UNIT = 2;
	const TYPE_ROW = 3;
	const TYPE_TOTAL = 4;

	protected $applicables;
	protected $applicables_loaded = false;

	protected $restrictions;
	protected $restrictions_loaded = false;

	protected $all_prerequisites;

	/**	Creates and returns price component object, or modifies an existing one if a price component with given application, object and currency already exists.
		@attrib api=1 params=pos

		@param application required type=object
		@param object required type=object
			Object for which the price component is created for
		@param value required type=real
		@param currency optional type=CL_CURRENCY

		@returns CL_CRM_SALES_PRICE_COMPONENT
		@errors
	**/
	public static function create_net_value_price_component(object $application, object $object, $value, object $currency = null)
	{
		if ($currency !== null and !$currency->is_a(currency_obj::CLID))
		{
			throw new awex_crm_sales_price_component_class("Cannot create net value price component, invalid currency object given. Expected CL_CURRENCY, " . $currency->class_id() . " given.");
		}

		$ol = new object_list(array(
			"class_id" => crm_sales_price_component_obj::CLID,
			"type" => self::TYPE_NET_VALUE,
			"application" => $application->id(),
			"applicables" => $object->id(),
		));

		if ($ol->count() > 0)
		{
			$potential_price_component = $ol->begin();
			do
			{
				if ($currency === null or $potential_price_component->is_applicable($currency->id()))
				{
					$price_component = $potential_price_component;
					break;
				}
			}
			while($potential_price_component = $ol->next());
		}

		if (!isset($price_component))
		{
			$name = $currency !== null ? sprintf(t("Rakenduse '%s' juurhind objektile '%s' valuutas '%s'"), $application->name(), $object->name(), $currency->prop("name")) : sprintf(t("Rakenduse '%s' juurhind objektile '%s' ilma valuutata"), $application->name(), $object->name());

			$price_component = obj(null, array(), crm_sales_price_component_obj::CLID);
			$price_component->set_parent($object->id());
			$price_component->set_name($name);
			$price_component->set_prop("type", self::TYPE_NET_VALUE);
			$price_component->set_prop("application", $application->id());
			$price_component->set_prop("applicables", array($object->id(), $currency->id()));
		}
		
		$price_component->set_prop("value", $value);
		$price_component->save();

		return $price_component;
	}

	public function prop_str($k, $is_oid = NULL)
	{
		switch($k)
		{
			case "value":
				return (string)$this->awobj_get_value() . ($this->prop("is_ratio") ? "%" : "");
		}

		return parent::prop_str($k, $is_oid);
	}

	public function awobj_get_prerequisites()
	{
		$prerequisites = parent::prop("prerequisites");
		return !empty($prerequisites) ? $prerequisites : array("net_value");
	}

	public function awobj_get_value()
	{
		// Lose the trailing zeros
		return aw_math_calc::string2float(parent::prop("value"));
	}

	public function awobj_set_value($v)
	{
		$this->set_prop("is_ratio", strpos($v, "%") !== false ? 1 : 0);
		return parent::set_prop("value", aw_math_calc::string2float($v));
	}

	/**	Returns an array of applicable class IDs
		@attrib api=1
		@returns int[]
	**/
	public static function get_applicable_clids()
	{
		$possible_classes = class_index::get_classes_by_interface("crm_sales_price_component_interface");
		$possible_clids = array();
		foreach($possible_classes as $possible_class)
		{
			$constant_name = $possible_class."::CLID";
			if(defined($constant_name))
			{
				$possible_clids[] = constant($constant_name);
			}
		}

		return $possible_clids;
	}

	/**	Returns true if this price component has an applicable object with the given OID
		@attrib api=1 params=pos
		@param applicable_id required type=int
			The OID of the object applicability is queried for.
		@returns boolean
		@errors Throws awex_crm_sales_price_component if this price component is not saved
	**/
	public function is_applicable($applicable_id)
	{
		if(!$this->applicables_loaded)
		{
			try
			{
				$this->load_applicables();
			}
			catch(awex_crm_sales_price_component $e)
			{
				throw $e;
			}
		}

		return in_array($applicable_id, $this->applicables->ids());
	}

	/**
		@attrib api=1 params=pos
		@param applicable_id required type=int
		@returns void
		@errors Throws awex_crm_sales_price_component if this price component is not saved
	**/
	public function add_applicable($applicable_id)
	{
		if(!$this->is_saved())
		{
			throw new awex_crm_sales_price_component("Price component must be saved before applicables can be added!");
		}

		if(!$this->applicables_loaded)
		{
			$this->load_applicables();
		}

		if(!$this->is_applicable($applicable_id))
		{
			$this->connect(array(
				"to" => $applicable_id,
				"type" => "RELTYPE_APPLICABLE",
			));

			$this->applicables->add($applicable_id);
		}
	}

	/**	Returns object list of applicable objects for this price component
		@attrib api=1 params=pos
		@param applicable_id required type=int/int[]
		@returns void
		@errors Throws awex_crm_sales_price_component if this price component is not saved
	**/
	public function remove_applicable($applicable_ids)
	{
		if(!$this->applicables_loaded)
		{
			try
			{
				$this->load_applicables();
			}
			catch(awex_crm_sales_price_component $e)
			{
				throw $e;
			}
		}

		foreach((array)$applicable_ids as $applicable_id)
		{
			if($this->is_applicable($applicable_id))
			{
				$this->disconnect(array(
					"from" => $applicable_id,
					"type" => "RELTYPE_APPLICABLE"
				));
				$this->applicables->remove($applicable_id);
			}
		}
	}

	/**	Returns true if this price component has a restriction object for section/work relation/profession with the given OID
		@attrib api=1 params=pos
		@param oid required type=int
			The OID of the object restriction is queried for.
		@returns boolean
		@errors Throws awex_crm_sales_price_component if this price component is not saved
	**/
	public function is_restricted($oid)
	{
		if(!$this->restrictions_loaded)
		{
			try
			{
				$this->load_restrictions();
			}
			catch(awex_crm_sales_price_component $e)
			{
				throw $e;
			}
		}

		foreach($this->restrictions->arr() as $restriction)
		{
			if($restriction->subject == $oid)
			{
				return true;
			}
		}

		return false;
	}

	/**
		@attrib api=1 params=pos
		@param oid required type=int
			The OID of the object restriction is added for
		@returns void
		@errors Throws awex_crm_sales_price_component if this price component is not saved
	**/
	public function add_restriction($oid)
	{
		if(!$this->is_saved())
		{
			throw new awex_crm_sales_price_component("Price component must be saved before restrictions can be added!");
		}

		if(!$this->restrictions_loaded)
		{
			$this->load_restrictions();
		}

		if(!$this->is_restricted($oid))
		{
			$restriction = obj(NULL, array(), CL_CRM_SALES_PRICE_COMPONENT_RESTRICTION);
			$restriction->set_parent($this->id());
			$restriction->set_prop("subject", $oid);
			$restriction->set_prop("price_component", $this->id());

			$restriction_id = $restriction->save();

			$this->restrictions->add($restriction_id);
		}
	}

	/**
		@attrib api=1 params=pos
		@param restriction_id required type=int/int[]
		@returns void
		@errors Throws awex_crm_sales_price_component if this price component is not saved
	**/
	public function remove_restriction($restriction_ids)
	{
		if(!$this->restrictions_loaded)
		{
			try
			{
				$this->load_restrictions();
			}
			catch(awex_crm_sales_price_component $e)
			{
				throw $e;
			}
		}

		foreach((array)$restriction_ids as $restriction_id)
		{
			$restriction = obj($restriction_id);
			if($restriction->is_a(CL_CRM_SALES_PRICE_COMPONENT_RESTRICTION))
			{
				$restriction->delete();
				$this->restrictions->remove($restriction_id);
			}
		}
	}

	/**	Returns object list of applicable objects for this price component
		@attrib api=1 params=pos
		@returns object_list
		@errors Throws awex_crm_sales_price_component if this price component is not saved
	**/
	public function get_applicables()
	{
		if(!$this->applicables_loaded)
		{
			try
			{
				$this->load_applicables();
			}
			catch(awex_crm_sales_price_component $e)
			{
				throw $e;
			}
		}

		return $this->applicables;
	}

	/**	Returns object list of restriction objects for this price component
		@attrib api=1 params=pos
		@returns object_list
		@errors Throws awex_crm_sales_price_component if this price component is not saved
	**/
	public function get_restrictions()
	{
		if(!$this->restrictions_loaded)
		{
			try
			{
				$this->load_restrictions();
			}
			catch(awex_crm_sales_price_component $e)
			{
				throw $e;
			}
		}

		return $this->restrictions;
	}

	/**	Checks if the given set of prerequisites creates a cycle. Returns true if this is the case, false otherwise
		@attrib api=1 params=pos
		@param oid required type=int
		@param prerequisites required type=int[]
		@returns boolean
	**/
	public static function check_prerequisites_cycle($id, $initial_prerequisites)
	{
		//	This is used to later return the cycle details. Not implemented yet though...
//		$prerequisites_by_oid = array($id => $initial_prerequisites);

		$new_prerequisites = $initial_prerequisites;
		while(count($new_prerequisites) !== 0)
		{
			$odl = new object_data_list(
				array(
					"class_id" => CL_CRM_SALES_PRICE_COMPONENT,
					"oid" => $new_prerequisites,
					"site_id" => array(),
					"lang_id" => array(),
				),
				array(
					CL_CRM_SALES_PRICE_COMPONENT => array("prerequisites")
				)
			);

			$new_prerequisites = array();
			foreach($odl->get_element_from_all("prerequisites") as $price_component_id => $prerequisites)
			{
				if(in_array($id, $prerequisites))
				{
					return true;
				}
//				$prerequisites_by_oid[$price_component_id] = $prerequisites;
				$new_prerequisites += $prerequisites;
			}
		}

		return false;
	}

	/**	Returns an array of all prerequisites recursively for this price component
		@attrib api=1
		@returns int[]
	**/
	public function get_all_prerequisites()
	{
		if(!isset($this->all_prerequisites))
		{
			$this->all_prerequisites = $new_prerequisites = safe_array($this->prop("prerequisites"));
			while(count($new_prerequisites) !== 0)
			{
				$odl = new object_data_list(
					array(
						"class_id" => CL_CRM_SALES_PRICE_COMPONENT,
						"oid" => $new_prerequisites,
						"site_id" => array(),
						"lang_id" => array(),
					),
					array(
						CL_CRM_SALES_PRICE_COMPONENT => array("prerequisites")
					)
				);

				$new_prerequisites = array();
				foreach($odl->get_element_from_all("prerequisites") as $price_component_id => $prerequisites)
				{
					$new_prerequisites += $prerequisites;
				}
				$this->all_prerequisites += $new_prerequisites;
			}
		}

		return $this->all_prerequisites;
	}

	protected function load_restrictions()
	{
		if(!$this->is_saved())
		{
			throw new awex_crm_sales_price_component("Price component must be saved before restrictions can be loaded!");
		}

		$this->restrictions = new object_list(array(
			"class_id" => CL_CRM_SALES_PRICE_COMPONENT_RESTRICTION,
			"price_component" => $this->id(),
		));
		//	This should be integrated into object_list, but object list isn't happy with objpickers.
		foreach($this->restrictions->arr() as $restriction)
		{
			if(!in_array($restriction->prop("subject.status"), array(object::STAT_ACTIVE, object::STAT_NOTACTIVE)))
			{
				$this->restrictions->remove($restriction);
			}
		}
		$this->restrictions_loaded = true;
	}

	protected function load_applicables()
	{
		if(!$this->is_saved())
		{
			throw new awex_crm_sales_price_component("Price component must be saved before applicables can be loaded!");
		}

		$this->applicables = new object_list($this->connections_from(array(
			"type" => "RELTYPE_APPLICABLE",
		)));
		$this->applicables_loaded = true;
	}
}

/** Generic crm sales price component error **/
class awex_crm_sales_price_component extends awex_crm_sales {}

/** Invalid class **/
class awex_crm_sales_price_component_class extends awex_crm_sales {}

?>
