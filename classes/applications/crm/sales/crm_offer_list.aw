<?php

/*
@classinfo maintainer=kaarel
*/

class crm_offer_list extends object_list
{
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
}

?>
