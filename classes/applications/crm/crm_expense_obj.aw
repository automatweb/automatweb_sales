<?php

class crm_expense_obj extends _int_object
{
	const CLID = 1144;

	function set_prop($name,$value)
	{
		if ($name == "cost")
		{
			$value = str_replace(",",".",$value);
		}
		parent::set_prop($name,$value);
	}

	public function save($check_state = false)
	{
		if(!$this->prop("task"))
		{
			$this->set_prop("task" , $this->parent());
		}
		parent::save($check_state);
	}

	function get_task()
	{
		$ol = new object_list(array(
			"class_id" => CL_TASK,
			"site_id" => array(),
			"lang_id" => array(),
			"CL_TASK.RELTYPE_EXPENSE" => $this->id(),
		));
		return reset($ol->ids());
	}
	
	function task_name()
	{
		$task =  $this->get_task();
		if($task)
		{
			$o = obj($task);
			return $o->name();
		}
		return t("Toimetus puudub");
	}

	function customer_name()
	{
		$task =  $this->get_task();
		if($task)
		{
			$o = obj($task);
			return $o->prop("customer.name");
		}
		return t("Klient puudub");
	}

/*	function get_customer()
	{
		$task =  $this->get_task();
		if($task)
		{
			$o = obj($task);
			return $o->prop("customer");
		}
		return "";
	}
*/
}

?>
