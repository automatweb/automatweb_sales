<?php

class crm_sales_price_component_obj extends _int_object
{
	protected $applicables;
	protected $applicables_loaded = false;

	protected $restrictions;
	protected $restrictions_loaded = false;

	public function prop_str($k, $is_oid = NULL)
	{
		if($k === "value")
		{
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
			$constant_name = $possible_class."::AW_CLID";
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

?>
