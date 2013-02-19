<?php

/*
@classinfo maintainer=kaarel
*/

class crm_offer_list extends object_list
{
	protected $rows_loaded = false;

	public static function get_default_filter()
	{
		$filter = array(
			"class_id" => CL_CRM_OFFER,
		);

		$application = automatweb::$request->get_application();
		if ($application->is_a(CL_CRM_SALES))
		{ // special properties only if in sales application
			$filter["parent"] = $application->prop("offers_folder");

			// role specific constraints
			$role = $application->get_current_user_role();
			switch ($role)
			{
				case crm_sales_obj::ROLE_GENERIC:
					break;

				case crm_sales_obj::ROLE_DATA_ENTRY_CLERK:
					break;

				case crm_sales_obj::ROLE_TELEMARKETING_SALESMAN:
					break;

				case crm_sales_obj::ROLE_TELEMARKETING_MANAGER:
					break;

				case crm_sales_obj::ROLE_SALESMAN:
					// salespersons see their own offers
					$current_person = get_current_person();
					$filter["CL_CRM_OFFER.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).salesman"] = $current_person->id();
					break;

				case crm_sales_obj::ROLE_MANAGER:
					break;
			}
		}

		return $filter;
	}

	/**	Loads the rows for all offers in list.
		@attrib api=1
	**/
	public function load_rows()
	{
		if($this->rows_loaded)
		{
			return;
		}

		if($this->count() > 0)
		{
			$ol = new object_list(array(
				"class_id" => CL_CRM_OFFER_ROW,
				"offer" => $this->ids(),
			));

			$rows = array();
			foreach($ol->arr() as $row)
			{
				$offer_id = $row->prop("offer");
				if(!isset($rows[$offer_id]))
				{
					$rows[$offer_id] = array();
				}
				$rows[$offer_id][$row->id()] = $row;
			}

			$offer = $this->begin();
			for (; !$this->end(); $offer = $this->next())
			{
				$offer->set_rows(isset($rows[$offer->id]) ? $rows[$offer->id] : array());
			}
		}

		$this->rows_loaded = true;
	}

	/**	Loads the applied price components for all offers in list. Enables $offer->() to be called.
		@attrib api=1
	**/
	public function load_applied_price_components()
	{
		if(!$this->rows_loaded)
		{
			$this->load_rows();
		}

		if($this->count() > 0)
		{
			$ids = $this->ids();
			$offer = $this->begin();
			for (; !$this->end(); $offer = $this->next())
			{
				$ids = array_merge($ids, array_keys($offer->get_rows()));
			}

			$offer_row_instance = new crm_offer_row();
			$q = sprintf("SELECT * FROM aw_crm_offer_row_price_components WHERE aw_object_id IN (%s)", implode(",", $ids));
			$price_components = $offer_row_instance->db_fetch_array($q);
			
			$applied_price_components = array();
			foreach($price_components as $price_component)
			{
				if (is_oid($price_component["aw_price_component_id"]))
				{
					$applied_price_components[$price_component["aw_object_id"]][$price_component["aw_price_component_id"]] = new price_component_applied(
						new object($price_component["aw_price_component_id"]),
						aw_math_calc::string2float($price_component["aw_value"]),
						aw_math_calc::string2float($price_component["aw_price_change"])
					);
				}
			}

			$offer = $this->begin();
			for (; !$this->end(); $offer = $this->next())
			{
				$offer->set_applied_price_components(isset($applied_price_components[$offer->id]) ? $applied_price_components[$offer->id] : array());
				foreach($offer->get_rows() as $row)
				{
					$row->set_applied_price_components(isset($applied_price_components[$row->id]) ? $applied_price_components[$row->id] : array());
				}
			}
		}

		$this->applied_price_components_loaded = true;
	}

	public function filter($param)
	{
		$param = array_merge(self::get_default_filter(), $param);

		return parent::filter($param);
	}

	protected function _int_add_to_list($oid_arr)
	{
		foreach($oid_arr as $oid)
		{
			/*	Isn't the following line a bit inefficient?
			 *	Say I wanted to add 100 offers at once. Instead of calling obj() 100 times (thus making atleast 100 database queries)
			 *	one could most probably get the required information with just one or a few database queries.
			 *	-kaarel 15.04.2010
			 */
			$o = new object($oid);
			if ($o->is_a(CL_CRM_OFFER))
			{
				$this->list[$oid] = $o;
				$this->list_names[$oid] = $this->list[$oid]->name();
				$this->list_objdata[$oid] = array(
					"brother_of" => $this->list[$oid]->brother_of()
				);
			}
		}
	}
}

?>
