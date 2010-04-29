<?php

/*
@classinfo maintainer=voldemar
*/

class crm_task_list extends object_list
{
	private $user_role = crm_sales_obj::ROLE_GENERIC;

	private $task_classes = array(//!!! teha get_class_label() v6i uurida kas on olemas juba
		CL_TASK => "CL_TASK",
		CL_CRM_CALL => "CL_CRM_CALL",
		CL_CRM_PRESENTATION => "CL_CRM_PRESENTATION"
	);

	public function __construct($param = array(), $task_list_param = array())
	{
		$this->filter($param, $task_list_param);
	}

	/**
	@attrib api=1 params=pos
	@param param type=array default=array()
		Standard object_list parameters
	@param task_list_param type=array default=array()
		Task list specific parameters array. Parameters are as follows:

		own_tasks_only type=bool default=false
			List only tasks where current user is a participant
	**/
	public function filter($param, $task_list_param = array())
	{
		if (!isset($param["class_id"]) or (is_array($param["class_id"]) and count(array_diff($param["class_id"], array_keys($this->task_classes))) > 0) or (is_class_id($param["class_id"]) and !isset($this->task_classes[$param["class_id"]])) or (!is_array($param["class_id"]) and !is_class_id($param["class_id"]))) // clid isn't set or is set but some or all clids are inapplicable
		{
			$param["class_id"] = array_keys($this->task_classes);
		}
		else
		{
			$param["class_id"] = (array) $param["class_id"];
		}

		$application = automatweb::$request->get_application();

		if ($application->is_a(CL_CRM_SALES))
		{ // special properties only if in sales application
			$organisation_oid = $application->prop("owner")->id();
			$role = automatweb::$request->get_application()->get_current_user_role();
			$seller_param = array();

			foreach ($param["class_id"] as $clid)
			{
				$clid_label = $this->task_classes[$clid];
				$seller_param["{$clid_label}.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).seller"] = $organisation_oid;
			}

			$param[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => $seller_param
			));

			// own_tasks_only parameter or salesperson is current user's role
			// salespersons see only their own tasks
			if (!empty($task_list_param["own_tasks_only"]))
			{
				$participant_param1 = array();
				$participant_param2 = array();
				$current_person = get_current_person();

				foreach ($param["class_id"] as $clid)
				{
					$clid_label = $this->task_classes[$clid];
					$participant_param1["{$clid_label}.RELTYPE_ROW.impl"] = $current_person->id();
					$participant_param2["{$clid_label}.RELTYPE_ROW.primary"] = 1;
				}

				$param[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => $participant_param1
				));
				$param[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => $participant_param2
				));
			}

			// role specific constraints
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
			if (isset($this->task_classes[$o->class_id()]))
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
