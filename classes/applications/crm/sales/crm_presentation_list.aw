<?php

/*
@classinfo maintainer=voldemar
*/

class crm_presentation_list extends crm_task_list
{
	public function filter($param, $task_list_param = array())
	{
		$param["class_id"] = CL_CRM_PRESENTATION;

		$application = automatweb::$request->get_application();
		if ($application->is_a(CL_CRM_SALES))
		{ // special properties only if in sales application
			$param["parent"] = $application->prop("presentations_folder");

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
					// tm salespersons see their own presentations
					// $param["createdby"] = aw_global_get("uid");
					// $param["real_start"] = new obj_predicate_compare(obj_predicate_compare::LESS, 1);
					break;

				case crm_sales_obj::ROLE_TELEMARKETING_MANAGER:
					break;

				case crm_sales_obj::ROLE_SALESMAN:
					// salespersons see their own presentations
					$current_person = get_current_person();
					$param["CL_CRM_PRESENTATION.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).salesman"] = $current_person->id();
					break;

				case crm_sales_obj::ROLE_MANAGER:
					break;
			}
		}

		return parent::filter($param, $task_list_param);
	}

	protected function _int_add_to_list($oid_arr)
	{
		foreach($oid_arr as $oid)
		{
			$o = new object($oid);
			if ($o->is_a(CL_CRM_PRESENTATION))
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
