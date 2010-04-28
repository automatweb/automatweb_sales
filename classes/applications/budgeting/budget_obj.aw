<?php

namespace automatweb;

/*

@classinfo maintainer=markop

*/
class budget_obj extends _int_object
{
	const AW_CLID = 1213;

	function get_budget_sum()
	{
		$project = $this->prop("project");
		if(!is_oid($project))
		{
			arr("projekt valimata");
			return 0;
		}
		$project = obj($project);
		$m = get_instance("applications/budgeting/budgeting_model");
		$ex = $this->meta("ex");
		$modified = $this->meta("mod_tax_pct");

		//arvutab arvatava omahinna
		$tasks = $project->get_tasks();
		$bugs = $project->get_bugs();
		$calls = $project->get_calls();
		$meetings = $project->get_meetings();
		$products = $project->get_products();
		$sum = 0;
		foreach($tasks->arr() as $task)
		{
			$sum += $task->sum_guess();
		}
		foreach($products->arr() as $product)
		{
			$sum += $product->prop("price");
		}
		foreach($calls->arr() as $task)
		{
			$sum += 0;
		}
		foreach($meetings->arr() as $task)
		{
			$sum += 0;
		}
		foreach($bugs->arr() as $task)
		{
			$sum += $task->sum_guess();
		}

		//lisab maksud
		$taxes = $m->get_project_taxes_data($project);
		$taxes = array_reverse($taxes);
		foreach($taxes as $tax)
		{
			$tax_obj = $tax["tax"];
			if ($ex[$tax_obj->id()] == 1)
			{
				continue;
			}
			if (!empty($modified[$tax_obj->id()]))
			{
				$t_pct = $modified[$tax_obj->id()];
				$sum =  100 * $sum / (100 - $t_pct);
			}
			else
			{
				$sum = $tax_obj->calculate_amount_needed($tax["account"] , $sum);//arr($sum); arr("---------------------------");
			}
		}
			
		return $sum;
	}

	//mitu korda t6en2oliselt on seda j2rjest vaja
	function get_project_object()
	{
		if(!is_object($this->project_object))
		{
			if(is_oid($this->prop("project")))
			{
				$this->project_object = obj($this->prop("project"));
			}
		}
		return $this->project_object;
	}

	function get_ex()
	{
		return $this->meta("ex");
	}

	function get_tasks()
	{
		$ol = new object_list();
		$project = $this->get_project_object();
		if(!$project)
		{
			return $ol;
		}
		$ex = $this->get_ex();
		$filter = array(
			"lang_id" => array(),
			"site_id" => array(),
			"class_id" => CL_TASK,
			"CL_TASK.RELTYPE_PROJECT" => $project->id(),
			"oid" => new obj_predicate_not(array_keys($ex)),
		);
		$ol = new object_list($filter);
		return $ol;
	}

	function get_bugs()
	{
		$ol = new object_list();
		$project = $this->get_project_object();
		if(!$project)
		{
			return $ol;
		}
		$ex = $this->get_ex();
		$filter = array(
			"lang_id" => array(),
			"site_id" => array(),
			"class_id" => CL_BUG,
			"project" => $project->id(),
			"oid" => new obj_predicate_not(array_keys($ex)),
		);
		$ol = new object_list($filter);
		return $ol;
	}

	function get_calls()
	{
		$ol = new object_list();
		$project = $this->get_project_object();
		if(!$project)
		{
			return $ol;
		}
		$ex = $this->get_ex();
		$filter = array(
			"lang_id" => array(),
			"site_id" => array(),
			"class_id" => CL_CRM_CALL,
			"CL_CRM_CALL.RELTYPE_PROJECT" => $project->id(),
			"oid" => new obj_predicate_not(array_keys($ex)),
		);
		$ol = new object_list($filter);
		return $ol;
	}

	function get_meetings()
	{
		$ol = new object_list();
		$project = $this->get_project_object();
		if(!$project)
		{
			return $ol;
		}
		$ex = $this->get_ex();
		$filter = array(
			"lang_id" => array(),
			"site_id" => array(),
			"class_id" => CL_CRM_MEETING,
			"CL_CRM_MEETING.RELTYPE_PROJECT" => $project->id(),
			"oid" => new obj_predicate_not(array_keys($ex)),
		);
		$ol = new object_list($filter);
		return $ol;
	}
	
	function get_products()
	{
		$ol = new object_list();
		$project = $this->get_project_object();
		if(!$project)
		{
			return $ol;
		}
		$ex = $this->get_ex();
		$ol = $project->get_products();
		foreach($ol->arr() as $o)
		{
			if ($ex[$o->id()] == 1)
			{
				$ol->remove($o->id());
			}
		}
		return $ol;
	}

	function get_taxes_data()
	{
		$ret = array();
		$project = $this->get_project_object();
		if(!$project)
		{
			return $ret;
		}
		$ex = $this->get_ex();
		$m = get_instance("applications/budgeting/budgeting_model");
		$ret = $m->get_project_taxes_data($project);
		foreach($ret as $key => $data)
		{
			if ($ex[$data["tax"] -> id()] == 1)
			{
				unset($ret[$key]);
			}
		}
		return $ret;
	}

}
?>
