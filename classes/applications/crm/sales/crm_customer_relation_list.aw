<?php

/*
@classinfo maintainer=voldemar
*/

class crm_customer_relation_list extends object_list
{
	public function __construct($param = array())
	{
		$this->filter($param);
	}

	public function filter($param)
	{
		$param["class_id"] = array(CL_CRM_COMPANY_CUSTOMER_DATA);

		$application = automatweb::$request->get_application();
		if ($application->is_a(CL_CRM_SALES))
		{ // special properties only if in sales application
			$organisation_oid = $application->prop("owner")->id();
			$param["seller"] = $organisation_oid;

			// role specific constraints
			$role = automatweb::$request->get_application()->get_current_user_role();
			switch ($role)
			{
				case crm_sales_obj::ROLE_GENERIC:
					break;

				case crm_sales_obj::ROLE_DATA_ENTRY_CLERK:
					$param = array();
					break;

				case crm_sales_obj::ROLE_TELEMARKETING_SALESMAN:
					break;

				case crm_sales_obj::ROLE_TELEMARKETING_MANAGER:
					break;

				case crm_sales_obj::ROLE_SALESMAN:
					break;

				case crm_sales_obj::ROLE_MANAGER:
					break;
			}
		}

		return parent::filter($param);
	}

	protected function _int_add_to_list($oid_arr)
	{
		foreach($oid_arr as $oid)
		{
			$o = new object($oid);
			if ($o->is_a(CL_CRM_COMPANY_CUSTOMER_DATA))
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
