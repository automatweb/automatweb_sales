<?php

/*
@classinfo maintainer=voldemar
*/

class crm_call_list extends crm_task_list
{
	public function filter($param, $task_list_param = array())
	{
		$param["class_id"] = CL_CRM_CALL;
		$application = automatweb::$request->get_application();

		if ($application->is_a(CL_CRM_SALES))
		{ // special properties only if in sales application
			$param["parent"] = $application->prop("calls_folder");

			// role specific constraints
			$role = automatweb::$request->get_application()->get_current_user_role();
			switch ($role)
			{
				case crm_sales_obj::ROLE_GENERIC:
					if (empty($param["real_duration"]))
					{
						// $param["real_duration"] = new obj_predicate_compare(OBJ_COMP_LESS, 1);//!!! tmp
					}
					break;

				case crm_sales_obj::ROLE_DATA_ENTRY_CLERK:
					break;

				case crm_sales_obj::ROLE_TELEMARKETING_SALESMAN:
					if (empty($param["real_duration"]))
					{
						$param["real_duration"] = new obj_predicate_compare(OBJ_COMP_LESS, 1);//!!! tmp
					}

					// $current_person = get_current_person();
					// $lang_skills = new object_list($current_person->connections_from(array("reltype" => "RELTYPE_LANGUAGE_SKILL")));
					// if ($lang_skills->count())
					// {
						// $lang_ids = $lang_skills->ids();
						// $param[] = new object_list_filter(array(
							// "logic" => "OR",
							// "conditions" => array (
								// new object_list_filter(array(
									// "logic" => "OR",
									// "conditions" => array (
										// "CL_CRM_CALL.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).buyer(CL_CRM_COMPANY).language" => new obj_predicate_compare(obj_predicate_compare::LESS, 1),
										// "CL_CRM_CALL.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).buyer(CL_CRM_PERSON).mlang" =>  new obj_predicate_compare(obj_predicate_compare::LESS, 1)
									// )
								// )),
								// new object_list_filter(array(
									// "logic" => "OR",
									// "conditions" => array (
										// "CL_CRM_CALL.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).buyer(CL_CRM_COMPANY).language" => $lang_ids,
										// "CL_CRM_CALL.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).buyer(CL_CRM_PERSON).mlang" => $lang_ids
									// )
								// ))
							// )
						// ));
					// }
					break;

				case crm_sales_obj::ROLE_TELEMARKETING_MANAGER:
					break;

				case crm_sales_obj::ROLE_SALESMAN:
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
			if ($o->is_a(CL_CRM_CALL))
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
