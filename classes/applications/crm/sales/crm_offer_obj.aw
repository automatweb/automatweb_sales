<?php

class crm_offer_obj extends crm_offer_price_component_handler
{
	protected $rows;
	protected $price_components;
	protected $price_components_loaded = false;
	protected $row_price_components;
	protected $row_price_components_loaded = array();
	protected $salesman_data;
	protected $all_prerequisites_by_price_component;
	protected static $state_names;

	const STATE_NEW = 0;
	const STATE_SENT = 1;
	const STATE_CONFIRMED = 2; 
	const STATE_CANCELLED = 3;

	public static function state_names($state = null)
	{
		if (0 === count(self::$state_names))
		{
			self::$state_names = array(
				self::STATE_NEW => t("Koostamisel"),
				self::STATE_SENT => t("Saadetud"),
				self::STATE_CONFIRMED => t("Kinnitatud"),
				self::STATE_CANCELLED => t("T&uuml;histatud")
			);
		}

		if (isset($state))
		{
			if (isset(self::$state_names[$state]))
			{
				$state_names = array($state => self::$state_names[$state]);
			}
			else
			{
				$state_names = array();
			}
			return $state_names;
		}
		else
		{
			return self::$state_names;
		}
	}

	public function confirm()
	{
		$this->set_prop("state", self::STATE_CONFIRMED);
		$this->save();
	}

	public function awobj_get_sum()
	{
		return aw_math_calc::string2float(parent::prop("sum"));
	}

	public function save($exclusive = false, $previous_state = null)
	{
		if(is_oid($this->prop("customer")))
		{
			$this->set_customer_relation();
		}

		return parent::save($exclusive, $previous_state);
	}

	public function awobj_get_date()
	{
		$date = parent::prop("date");
		return !empty($date) ? $date : time();
	}

	/**	Returns true if this offer contains the given object, false otherwise
		@attrib api=1 params=pos
		@param object type=object
			The object to be added to the offer
		@returns boolean
		@errors Throws awex_crm_offer if this offer is not saved
	**/
	public function contains_object(object $o)
	{
		if(!isset($this->rows))
		{
			try
			{
				$this->load_rows();
			}
			catch (awex_crm_offer $e)
			{
				throw $e;
			}
		}

		foreach($this->rows as $row)
		{
			if($o->id() == $row->prop("object"))
			{
				return true;
			}
		}

		return false;
	}

	/**
		@attrib api=1
		@param object type=object
			The object to be added to the offer
		@returns void
		@error
			Throws awex_crm_offer if this offer is not saved.
			TODO: Throws awex_crm_offer if the object to be added doesn't implement crm_sales_price_component_interface.
	**/
	public function add_object(object $o)
	{
		if(!$this->is_saved())
		{
			throw new awex_crm_offer("Offer must be saved before rows can be added!");
		}

		$row = obj(NULL, array(), CL_CRM_OFFER_ROW);
		$row->set_parent($this->id());
		$row->set_prop("offer", $this->id());
		$row->set_prop("object", $o->id());
		$row->save();
	}

	/**	Returns array of 
		@attrib api=1
		@returs crm_offer_row_obj[]
		@error Throws awex_crm_offer if this offer is not saved
	**/
	public function get_rows()
	{
		if(!isset($this->rows))
		{
			try
			{
				$this->load_rows();
			}
			catch (awex_crm_offer $e)
			{
				throw $e;
			}
		}

		return $this->rows;
	}

	/**
		@attrib api=1
		@returns object_list
		@errors Throws awex_crm_offer if this offer is not saved
	**/
	public function get_price_components_for_row(object $row)
	{
		if (!$this->price_components_loaded)
		{
			try
			{
				$this->load_price_components();
			}
			catch (awex_crm_offer $e)
			{
				throw $e;
			}
		}

		if (empty($this->row_price_components_loaded[$row->id()]))
		{
			try
			{
				$this->load_price_components_for_row($row);
			}
			catch (awex_crm_offer $e)
			{
				throw $e;
			}
		}

		return $this->row_price_components[$row->id()];
	}

	/**
		@attrib api=1
		@returns object_list
		@errors Throws awex_crm_offer if this offer is not saved
	**/
	public function get_price_components_for_total()
	{
		if (!$this->price_components_loaded)
		{
			try
			{
				$this->load_price_components();
			}
			catch (awex_crm_offer $e)
			{
				throw $e;
			}
		}

		return $this->price_components[crm_sales_price_component_obj::TYPE_TOTAL];
	}

	/**	Returns true if given price component is compulsory for this offer, false otherwise
		@attrib api=1 params=pos
		@param price_component required type=price_component_obj
			The price component the compulsoriness is queried for
		@returns boolean
	**/
	public function price_component_is_compulsory($price_component)
	{
		$compulsory = false;
		
		if(!isset($this->salesman_data))
		{
			try
			{
				$this->load_offer_data_for_price_component();
			}
			catch(awex_crm_offer $e)
			{
				throw $e;
			}
		}

		$min = $max = $price_component->prop("value");
		$value = $price_component->prop("value");

		$priority_of_current_compulsoriness = 0;
		$priorities_of_compulsoriness = array(
			CL_CRM_SECTION => 1,
			CL_CRM_PROFESSION => 2,
			CL_CRM_PERSON_WORK_RELATION => 3,
		);

		foreach($price_component->get_restrictions()->arr() as $restriction)
		{
			if(
				(
					in_array($restriction->prop("subject"), $this->salesman_data["work_relations"]->ids())
					|| in_array($restriction->prop("subject"), $this->salesman_data["professions"]->ids())
					|| in_array($restriction->prop("subject"), $this->salesman_data["sections"]->ids())
				)
				&& $priority_of_current_compulsoriness < $priorities_of_compulsoriness[$restriction->prop("subject.class_id")]
			)
			{
				$priority_of_current_compulsoriness = $priorities_of_compulsoriness[$restriction->prop("subject.class_id")];
				$compulsory = $restriction->prop("compulsory");
			}
		}

		return $compulsory;
	}

	/**	Returns array of lower and upper tolerance of given price component for this offer
		@attrib api=1 params=pos
		@param price_component required type=price_component_obj
			The price component the tolerance is queried for
		@returns array($min, $max)
	**/
	public function get_tolerance_for_price_component($price_component)
	{
		if(!isset($this->salesman_data))
		{
			try
			{
				$this->load_offer_data_for_price_component();
			}
			catch(awex_crm_offer $e)
			{
				throw $e;
			}
		}

		$min = $max = $price_component->prop("value");
		$value = $price_component->prop("value");

		$priority_of_current_tolerance = 0;
		$priorities_of_tolerance = array(
			CL_CRM_SECTION => 1,
			CL_CRM_PROFESSION => 2,
			CL_CRM_PERSON_WORK_RELATION => 3,
		);

		foreach($price_component->get_restrictions()->arr() as $restriction)
		{
			if(
				(
					in_array($restriction->prop("subject"), $this->salesman_data["work_relations"]->ids())
					|| in_array($restriction->prop("subject"), $this->salesman_data["professions"]->ids())
					|| in_array($restriction->prop("subject"), $this->salesman_data["sections"]->ids())
				)
				&& $priority_of_current_tolerance < $priorities_of_tolerance[$restriction->prop("subject.class_id")]
			)
			{
				$priority_of_current_tolerance = $priorities_of_tolerance[$restriction->prop("subject.class_id")];
				$min = $restriction->has_lower_tolerance() ? $restriction->prop("lower_tolerance") * $value / 100 : $value;
				$max = $restriction->has_upper_tolerance() ? $restriction->prop("upper_tolerance") * $value / 100 : $value;
			}
		}

		return array($min, $max);
	}

	public function sort_price_components($a, $b)
	{
		if(in_array($a->id(), $this->all_prerequisites_by_price_component[$b->id()]))
		{
			return -1;
		}
		elseif(in_array($b->id(), $this->all_prerequisites_by_price_component[$a->id()]))
		{
			return 1;
		}
		return 0;
	}

	/**	Loads relevant data to check if price component is compulsory and to find the correct tolerance.
	 *	Relevant data is currently section, work_relation and profession, all of which will be taken from the salesman of the offer.
	**/
	protected function load_offer_data_for_price_component()
	{
		if(!$this->is_saved())
		{
			throw new awex_crm_offer("Offer must be saved before rows can be loaded!");
		}

		//	Offer must always have a salesman!
		if(!is_oid($this->prop("salesman")))
		{
			throw new awex_crm_offer("No salesman defined for this offer!");
		}

		$salesman = obj($this->prop("salesman"));

		$this->salesman_data = array(
			"professions" => $salesman->get_professions(),
			"work_relations" => $salesman->get_active_work_relations(),
			"sections" => $salesman->get_sections(),
		);
	}

	protected function load_price_components_for_row(object $row)
	{
		$odl = new object_data_list(
			array(
				"class_id" => CL_CRM_SALES_PRICE_COMPONENT,
				"type" => array(crm_sales_price_component_obj::TYPE_UNIT, crm_sales_price_component_obj::TYPE_ROW, crm_sales_price_component_obj::TYPE_NET_VALUE),
				"applicables.id" => $row->prop("object"),
//				"application" => automatweb::$request->get_application()->id()
			),
			array(
				CL_CRM_SALES_PRICE_COMPONENT => array("applicables")
			)
		);

		$valid_price_components = array();
		foreach($odl->arr() as $oid => $odata)
		{
			if(true)	//	This is the place to check applicables
			{
				$valid_price_components[] = $oid;
			}
		}

		$ol = new object_list();
		$ol->add($valid_price_components);
		$ol->add($this->price_components[crm_sales_price_component_obj::TYPE_UNIT]);
		$ol->add($this->price_components[crm_sales_price_component_obj::TYPE_ROW]);

		$this->load_all_prerequisites_for_price_component_ol($ol);

		$ol->sort_by_cb(array($this, "sort_price_components"));
		$this->row_price_components[$row->id()] = $ol;

		$this->row_price_components_loaded[$row->id()] = true;
	}

	protected function load_all_prerequisites_for_price_component_ol($ol)
	{
		foreach($ol->arr() as $o)
		{
			if(!isset($this->all_prerequisites_by_price_component[$o->id()]))
			{
				$this->all_prerequisites_by_price_component[$o->id()] = $o->get_all_prerequisites();
			}
		}
	}

	protected function load_price_components()
	{
		/*
		 *	This is the place where we'll load all the price components that are not row specific
		 */

		$this->all_prerequisites_by_price_component = array();

		//	Price components without applicables
		$q = sprintf("
			SELECT o.oid
			FROM objects o LEFT JOIN aliases a ON o.oid = a.source AND a.reltype = %u
			WHERE a.target IS NULL AND o.class_id = %u;", 2 /* RELTYPE_APPLICABLE */, CL_CRM_SALES_PRICE_COMPONENT);

		$price_components_without_applicables = array();
		foreach($this->instance()->db_fetch_array($q) as $row)
		{
			$price_components_without_applicables[] = $row["oid"];
		}		
		if(!empty($price_components_without_applicables))
		{
			$odl = new object_data_list(
				array(
					"class_id" => CL_CRM_SALES_PRICE_COMPONENT,
					"oid" => $price_components_without_applicables,
					"type" => array(crm_sales_price_component_obj::TYPE_UNIT, crm_sales_price_component_obj::TYPE_ROW, crm_sales_price_component_obj::TYPE_TOTAL),
//					"application" => automatweb::$request->get_application()->id()
				),
				array(
					CL_CRM_SALES_PRICE_COMPONENT => array("type"),
				)
			);
			$price_component_ids_by_type = array(
				crm_sales_price_component_obj::TYPE_UNIT => array(),
				crm_sales_price_component_obj::TYPE_ROW => array(),
				crm_sales_price_component_obj::TYPE_TOTAL => array(),
			);
			foreach($odl->arr() as $oid => $odata)
			{
				$price_component_ids_by_type[$odata["type"]][] = $oid;
			}
			$ol = new object_list();
			$ol->add($price_component_ids_by_type[crm_sales_price_component_obj::TYPE_UNIT]);
			$this->price_components[crm_sales_price_component_obj::TYPE_UNIT] = $ol;
			
			$ol = new object_list();
			$ol->add($price_component_ids_by_type[crm_sales_price_component_obj::TYPE_ROW]);
			$this->price_components[crm_sales_price_component_obj::TYPE_ROW] = $ol;
			
			$ol = new object_list();
			$ol->add($price_component_ids_by_type[crm_sales_price_component_obj::TYPE_TOTAL]);
			$this->price_components[crm_sales_price_component_obj::TYPE_TOTAL] = $ol;
		}

		$this->price_components_loaded = true;
	}

	protected function load_rows()
	{
		if(!$this->is_saved())
		{
			throw new awex_crm_offer("Offer must be saved before rows can be loaded!");
		}

		$ol = new object_list(array(
			"class_id" => CL_CRM_OFFER_ROW,
			"offer" => $this->id(),
		));
		$this->rows = $ol->arr();
	}

	protected function set_customer_relation()
	{
		$application = automatweb::$request->get_application();
		if ($application->is_a(CL_CRM_SALES))
		{
			$owner = $application->prop("owner");
			$customer = new object($this->prop("customer"));
			$customer_relation = $customer->get_customer_relation($owner, true);
			if(is_object($customer_relation))
			{
				$customer_relation_id = $customer_relation->id();
				$this->set_prop("customer_relation", $customer_relation_id);
			}
		}
	}
}

/** Generic crm offer error **/
class awex_crm_offer extends awex_crm {}

?>
