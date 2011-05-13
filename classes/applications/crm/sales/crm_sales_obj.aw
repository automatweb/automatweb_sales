<?php

class crm_sales_obj extends _int_object implements application_interface
{
	// sales application users are always given a role
	// roles are determined by binding crm_profession objects to crm_sales_obj through role_profession_* properties
	// role for a user is thereby defined by their profession
	const ROLE_GENERIC = 10; // anyone
	const ROLE_DATA_ENTRY_CLERK = 20; // only data entry
	const ROLE_TELEMARKETING_SALESMAN = 30; // data entry and calls assigned to user
	const ROLE_TELEMARKETING_MANAGER = 40; // previous two roles + assigning calls(viewing and editing any contact), viewing and editing presentationg
	const ROLE_SALESMAN = 50; // only user's own presentations
	const ROLE_SALES_MANAGER = 70; // presentations
	const ROLE_MANAGER = 60; // all views and rights

	private static $role_names = array();
	private static $current_user_role;
	public static $role_ids = array(
		self::ROLE_GENERIC => "generic",
		self::ROLE_DATA_ENTRY_CLERK => "data_entry_clerk",
		self::ROLE_TELEMARKETING_SALESMAN => "telemarketing_salesman",
		self::ROLE_TELEMARKETING_MANAGER => "telemarketing_manager",
		self::ROLE_SALESMAN => "salesman",
		self::ROLE_SALES_MANAGER => "sales_manager",
		self::ROLE_MANAGER => "manager"
	);

	private static $roles_with_call_edit_privilege = array(
		crm_sales_obj::ROLE_TELEMARKETING_MANAGER,
		crm_sales_obj::ROLE_MANAGER
	);
	private static $roles_with_presentation_edit_privilege = array(
		crm_sales_obj::ROLE_TELEMARKETING_MANAGER,
		crm_sales_obj::ROLE_MANAGER
	);
	private static $roles_with_offer_edit_privilege = array(
		self::ROLE_SALES_MANAGER,
		self::ROLE_MANAGER
	);
	private static $roles_with_all_tasks_privilege = array(
		self::ROLE_TELEMARKETING_MANAGER,
		self::ROLE_SALES_MANAGER,
		self::ROLE_MANAGER
	);

	// used by create_call()
	private static $tm_resource_cache = 0;
	private static $existing_calls_filter_cache = array();
	//

	/** Returns object list of contacts visible to current user, in applicable order
	@attrib api=1
	@param start optional type=int
	@returns object_list
	**/
	public function get_contacts($start = 0, $end = -1)
	{
		// get current user role
		$role = $this->get_current_user_role();

		if (self::ROLE_TELEMARKETING_SALESMAN === $role)
		{
			$contacts;
		}
	}

	/** Returns list of role options
	@attrib api=1 params=pos
	@param role type=int
		Role id constant value to get name for, one of crm_sales_obj::ROLE_*
	@returns array
		Format option value => human readable name, if $role parameter set, array with one element returned and empty array when that role not found.
	**/
	public static function role_names($role = null)
	{
		if (0 === count($this->role_names))
		{
			self::$role_names = array(
				self::ROLE_GENERIC => t("&Uuml;ldine"),
				self::ROLE_DATA_ENTRY_CLERK => t("Andmesisestaja"),
				self::ROLE_TELEMARKETING_SALESMAN => t("Telemarketingit&ouml;&ouml;taja"),
				self::ROLE_TELEMARKETING_MANAGER => t("Telemarketingi juht"),
				self::ROLE_SALESMAN => t("M&uuml;&uuml;giesindaja"),
				self::ROLE_SALES_MANAGER => t("M&uuml;&uuml;gijuht"),
				self::ROLE_MANAGER => t("Juht")
			);
		}

		if (isset($role))
		{
			if (isset(self::$role_names[$role]))
			{
				$role_names = array($result => self::$role_names[$role]);
			}
			else
			{
				$role_names = array();
			}
			return $role_names;
		}
		else
		{
			return self::$role_names;
		}
	}

	public function save($exclusive = false, $previous_state = null)
	{
		if (!is_oid($this->prop("owner")))
		{
			throw new awex_crm_sales_owner("Owner not defined, can't save");
		}

		return parent::save($exclusive, $previous_state);
	}

	public function awobj_get_owner()
	{
		return new object(parent::prop("owner"));
	}

	public function awobj_set_owner(object $owner)
	{
		if (!$owner->is_a(CL_CRM_COMPANY))
		{
			throw new awex_crm_sales_owner("Owner must be CL_CRM_COMPANY object. Object with class id '".$owner->class_id()."' given");
		}

		return parent::set_prop("owner", $owner->id());
	}

	/** Returns current user role id in this sales application
	@attrib api=1
	@returns int
		one of ROLE_... constants
	@comment
		If someone fills many roles, the role with least/lowest access priority will be returned.
		Role priority order is (lowest first):
			ROLE_GENERIC
			ROLE_DATA_ENTRY_CLERK
			ROLE_TELEMARKETING_SALESMAN
			ROLE_TELEMARKETING_MANAGER
			ROLE_SALESMAN
			ROLE_SALES_MANAGER
			ROLE_MANAGER
	**/
	public function get_current_user_role()
	{
		if (isset(self::$current_user_role))
		{
			$role = self::$current_user_role;
		}
		else
		{
			$role = self::ROLE_GENERIC;
			$current_person = get_current_person();
			$professions = $current_person->get_profession_selection($this->prop("owner"));
			if (count($professions))
			{
				reset($professions);
				$profession = key($professions);
				switch ($profession)
				{
					case $this->prop("role_profession_manager"):
						$role = self::ROLE_MANAGER;
						break;
					case $this->prop("role_profession_salesman"):
						$role = self::ROLE_SALESMAN;
						break;
					case $this->prop("role_profession_sales_manager"):
						$role = self::ROLE_SALES_MANAGER;
						break;
					case $this->prop("role_profession_telemarketing_manager"):
						$role = self::ROLE_TELEMARKETING_MANAGER;
						break;
					case $this->prop("role_profession_telemarketing_salesman"):
						$role = self::ROLE_TELEMARKETING_SALESMAN;
						break;
					case $this->prop("role_profession_data_entry_clerk"):
						$role = self::ROLE_DATA_ENTRY_CLERK;
						break;
				}
			}
			self::$current_user_role = $role;
		}
		return $role;
	}

	/** Returns true if role is allowed to perform given action
	@attrib api=1 params=pos
	@param action type=string set=call_edit,presentation_edit,all_tasks
	@param role type=int default=NULL
		One of ROLE_... constants. If not specified, current user role used
	@returns bool
	**/
	public function has_privilege($action, $role = null)
	{
		$has = false;

		if (!isset($role))
		{
			$role = self::get_current_user_role();
		}

		if ("call_edit" === $action)
		{
			$has = in_array($role, self::$roles_with_call_edit_privilege, true);
		}
		elseif ("presentation_edit" === $action)
		{
			$has = in_array($role, self::$roles_with_presentation_edit_privilege, true);
		}
		elseif ("offer_edit" === $action)
		{
			$has = in_array($role, self::$roles_with_offer_edit_privilege, true);
		}
		elseif ("all_tasks" === $action)
		{
			$has = in_array($role, self::$roles_with_all_tasks_privilege, true);
		}

		return $has;
	}

	/** Adds a contact to this sales application
	@attrib api=1 params=pos
	@param customer_relation type=CL_CRM_COMPANY_CUSTOMER_DATA
		Customer relation defining the contact to add and its relation to sales application owner
	@comment
		Saves customer_relation object
	@returns void
	**/
	public function add_contact(object $customer_relation)
	{
		$customer_relation->set_prop("sales_state", crm_company_customer_data_obj::SALESSTATE_NEW);
		$customer_relation->save();
		$case = $customer_relation->get_sales_case(true);
		$case->plan();
	}

	/** Creates a phone call task
	@attrib api=1 params=pos

	@param to type=CL_CRM_COMPANY_CUSTOMER_DATA
	@param time type=int default=0
		UNIX timestamp. Default means an unscheduled call is created
	@param prerequisite_tasks type=object_list default=null
		Tasks (CL_TASK and extensions) that the created call will be the result task for and have it as prerequisites.
	@param check_existing type=bool default=false
		Check if an active call to that customer already exists, don't create new call if it does

	@returns CL_CRM_CALL
		Created call object or when $check_existing TRUE newest existing active call if found
	**/
	public function create_call(object $customer_relation, $time = 0, object_list $prerequisite_tasks = null, $check_existing = false)
	{
		// uses only customer relation prop avoid additional connection objects
		// customer prop is left empty as redundant
		$calls_folder = $this->prop("calls_folder");
		if (!is_oid($calls_folder))
		{
			throw new awex_crm_sales_folder("Calls folder not defined");
		}

		$call = null;
		if ($check_existing)
		{
			$filter = array(
				"class_id" => CL_CRM_CALL,
				"real_start" => new obj_predicate_compare(obj_predicate_compare::LESS, 1),
				"parent" => $calls_folder,
				"customer_relation" => $customer_relation->id(),
				new obj_predicate_sort(array("created" => obj_predicate_sort::DESC)),
				new obj_predicate_limit(1)
			);

			$existing_active_calls = new object_list($filter);
			$call = $existing_active_calls->begin();
		}

		if (null === $call)
		{
			$call = obj(null, array(), CL_CRM_CALL);
			$call->set_parent($calls_folder);
			$call->set_name(sprintf(aw_html_entity_decode(t("K&otilde;ne %s kliendile %s")), $this->get_calls_count($customer_relation) + 1, $customer_relation->prop("buyer.name")));
			$call->set_prop("customer_relation", $customer_relation->id());
			$this->set_call_time($call, $time);

			if (0 === self::$tm_resource_cache)
			{
				$company = $this->awobj_get_owner();
				$profession = new object($this->prop("role_profession_telemarketing_salesman"), array(), CL_CRM_PROFESSION);
				$human_resources_manager = mrp_workspace_obj::get_hr_manager($company);
				$tm_resource = $human_resources_manager->get_profession_resource($company, $profession);
			}
			else
			{
				$tm_resource = self::$tm_resource_cache;
			}

			$call->schedule($tm_resource, $prerequisite_tasks); // also saves call
		}

		return $call;
	}

	/** Creates phone calls to a list of customers
	@attrib api=1 params=pos
	@param customer_relations_filter type=array
		object_list/object_data_list filter for retreiving customer relation objects data
	@returns array
		Returns array with two elements: number of new calls created and number of contacts processed
	**/
	public function create_calls($customer_relation_oids)
	{
		/* dbg */ echo "|";flush ();
		$time = time();

		// find calls folder
		$calls_folder = $this->prop("calls_folder");
		if (!is_oid($calls_folder))
		{
			throw new awex_crm_sales_folder("Calls folder not defined");
		}

		// load telemarketing salesman resource
		$company = $this->awobj_get_owner();
		$profession = new object($this->prop("role_profession_telemarketing_salesman"), array(), CL_CRM_PROFESSION);
		$human_resources_manager = mrp_workspace_obj::get_hr_manager($company);
		self::$tm_resource_cache = $human_resources_manager->get_profession_resource($company, $profession);

		//
		$new_calls_created = 0;
		$existing_calls_found = 0;
		$count = count ($customer_relation_oids);

		/* dbg */ echo "|";flush ();

		if ($count)
		{
			/* dbg */ $tick = $count >= 100 ? round ($count/100) : 1;
			/* dbg */ $tick_size = $count >= 100 ? 1 : round (100/$count);
			/* dbg */ $bar_size = $count >= 100 ? round ($count/$tick) : $count*$tick_size;
			/* dbg */ $tick_i = 0;

			 // load active calls for requested customer relations, if found
			$active_calls_data = new object_data_list(
				array(
					"class_id" => CL_CRM_CALL,
					"real_start" => new obj_predicate_compare(obj_predicate_compare::LESS, 1),
					"parent" => $calls_folder,
					"customer_relation" => $customer_relation_oids
				),
				array(
					CL_CRM_CALL => "customer_relation"
				)
			);
			$active_calls_data = $active_calls_data->arr();
			$local_memory_limit = 0.95 * aw_bytes_string_to_int(ini_get("memory_limit"));

			/* dbg */ echo "|";flush ();

			$status = 0;
			foreach ($customer_relation_oids as $cro_oid)
			{
				if (memory_get_usage() > $local_memory_limit)
				{
					$status = 1;
					break;
				}

				if (!in_array($cro_oid, $active_calls_data))
				{
					$customer_relation = new object($cro_oid);
					$call = $this->create_call($customer_relation, 0, null, false);

					if ($call->modified() < $time)
					{
						++$existing_calls_found;
					}
					else
					{
						++$new_calls_created;
					}
				}
				else
				{
					++$existing_calls_found;
				}

				/* dbg */ if (!($tick_i%$tick))
				/* dbg */ {
					/* dbg */ echo str_repeat ("|", $tick_size);flush ();
				/* dbg */ }
				/* dbg */ $tick_i++;
			}
		}

		self::$tm_resource_cache = 0;
		return array($new_calls_created, $existing_calls_found, $status);
	}

	/** Creates a presentation task
	@attrib api=1 params=pos

	@param to type=CL_CRM_COMPANY_CUSTOMER_DATA
	@param time type=int default=0
		UNIX timestamp. Default means an unscheduled presentation is created
	@param prerequisite_tasks type=object_list default=null
		Tasks (CL_TASK and extensions) that the created presentation will be the result task for and have it as prerequisites.
	@param check_existing type=bool default=false
		Check if an active presentation to that customer already exists, don't create new presentation if it does

	@returns CL_CRM_PRESENTATION
		Created presentation object or when $check_existing TRUE newest existing active presentation if found
	**/
	public function create_presentation(object $customer_relation, $time = 0, object_list $prerequisite_tasks = null, $check_existing = false)
	{
		// uses only customer relation prop avoid additional connection objects
		// customer prop is left empty as redundant
		$presentations_folder = $this->prop("presentations_folder");
		if (!is_oid($presentations_folder))
		{
			throw new awex_crm_sales_folder("Presentations folder not defined");
		}

		if ($check_existing)
		{
			$existing_active_presentations = new object_list(array(
				"class_id" => CL_CRM_PRESENTATION,
				"customer_relation" => $customer_relation->id(),
				"real_start" => new obj_predicate_compare(obj_predicate_compare::LESS, 1),
				"parent" => $presentations_folder,
				new obj_predicate_sort(array("created" => obj_predicate_sort::DESC)),
				new obj_predicate_limit(1)
			));

			if ($existing_active_presentations->count())
			{
				$presentation = $existing_active_presentations->begin();
			}
		}

		if (!isset($presentation))
		{
			$presentation_nr = $this->get_presentations_count($customer_relation) + 1;
			$presentation = obj(null, array(), CL_CRM_PRESENTATION);
			$presentation->set_parent($presentations_folder);
			$presentation->set_name(sprintf(t("Esitlus %s kliendile %s"), $presentation_nr, $customer_relation->prop("buyer.name")));
			$presentation->set_prop("customer_relation", $customer_relation->id());
			$this->set_presentation_time($presentation, $time);
			$company = $this->awobj_get_owner();
			$profession = new object($this->prop("role_profession_telemarketing_salesman"), array(), CL_CRM_PROFESSION);
			$human_resources_manager = mrp_workspace_obj::get_hr_manager($company);
			$resource = $human_resources_manager->get_profession_resource($company, $profession);
			$presentation->schedule($resource, $prerequisite_tasks);
		}

		return $presentation;
	}

	/** Makes a phone call.
	@attrib api=1 params=pos
	@param call type=CL_CRM_CALL
	@returns void
	**/
	public function make_call(object $call)
	{
	}

	/** Ends a phone call made in this application
	@attrib api=1 params=pos
	@param call type=CL_CRM_CALL
	@returns void
	**/
	public function end_call(object $call)
	{
		$this->process_call_result($call);
	}

	/** Makes a presentation.
	@attrib api=1 params=pos
	@param presentation type=CL_CRM_PRESENTATION
	@returns void
	**/
	public function make_presentation(object $presentation)
	{
	}

	/** Ends a presentation made in this application and processes presentation result
	@attrib api=1 params=pos
	@param presentation type=CL_CRM_PRESENTATION
	@returns void
	**/
	public function end_presentation(object $presentation)
	{
		$this->process_presentation_result($presentation);
	}

	/** Cancels a presentation made in this application and processes presentation result
	@attrib api=1 params=pos
	@param presentation type=CL_CRM_PRESENTATION
	@returns void
	**/
	public function cancel_presentation(object $presentation)
	{
		$this->process_presentation_result($presentation);
	}

	/**
	@attrib api=1 params=pos
	@param call type=CL_CRM_CALL
	@returns void
	@errors
		throws awex_crm_sales_call
	**/
	public function process_call_result(object $call)
	{
		if (!is_oid($call->prop("customer_relation")))
		{
			throw new awex_crm_sales_call("Customer relation not defined");
		}

		$result = (int) $call->prop("result");

		// cache call data in cro
		$customer_relation = new object($call->prop("customer_relation"));
		$customer_relation->set_prop("sales_last_call", $call->id());
		$customer_relation->set_prop("sales_calls_made", $customer_relation->prop("sales_calls_made") + 1);

		if ($result === crm_call_obj::RESULT_CALL)
		{
			$caller = new object($call->prop("real_maker"));
			if (!$caller->is_a(CL_CRM_PERSON))
			{
				throw new awex_crm_sales_call("Call maker not defined");
			}

			$new_call_time =  $call->prop("new_call_date");
			$new_call = $this->set_result_call_for_ended_task($call, $new_call_time);
			$company = $this->awobj_get_owner();
			$caller_resource = mrp_workspace_obj::get_person_resource($company, $caller);
			$new_call->schedule($caller_resource); // schedule the new call to the person who made the call resulting to it
			$customer_relation->set_prop("sales_state", crm_company_customer_data_obj::SALESSTATE_NEWCALL);
		}
		elseif ($result === crm_call_obj::RESULT_NOANSWER)
		{
			$new_call_time =  time() + $this->prop("call_result_noanswer_recall_time");
			$new_call = $this->set_result_call_for_ended_task($call, $new_call_time);
		}
		elseif ($result === crm_call_obj::RESULT_BUSY)
		{
			$new_call_time =  time() + $this->prop("call_result_busy_recall_time");
			$new_call = $this->set_result_call_for_ended_task($call, $new_call_time);
		}
		elseif ($result === crm_call_obj::RESULT_OUTOFSERVICE)
		{
			$new_call_time =  time() + $this->prop("call_result_outofservice_recall_time");
			$new_call = $this->set_result_call_for_ended_task($call, $new_call_time);
		}
		elseif ($result === crm_call_obj::RESULT_VOICEMAIL)
		{
			$new_call_time =  time() + $this->prop("call_result_busy_recall_time");
			$new_call = $this->set_result_call_for_ended_task($call, $new_call_time);
		}
		elseif (
			$result === crm_call_obj::RESULT_HUNGUP or
			$result === crm_call_obj::RESULT_DISCONNECTED
		)
		{
			// create immediate new call
			$new_call_time =  time();
			$new_call = $this->set_result_call_for_ended_task($call, $new_call_time);
		}
		elseif ($result === crm_call_obj::RESULT_NEWNUMBER)
		{
			// replace old nr. and create immediate new call
			$new_call_time = time();
			$new_call = $this->set_result_call_for_ended_task($call, $new_call_time);
		}
		elseif ($result === crm_call_obj::RESULT_LANG)
		{
			// change customer's preferred language and create immediate new call
			$customer = new object($customer_relation->prop("buyer"));
			if ($customer->is_a(CL_CRM_PERSON))
			{
				$customer->set_prop("mlang", $call->prop("preferred_language"));
			}
			elseif ($customer->is_a(CL_CRM_COMPANY))
			{
				$customer->set_prop("language", $call->prop("preferred_language"));
			}
			$customer->save();
			$new_call_time = time();
			$new_call = $this->set_result_call_for_ended_task($call, $new_call_time);
		}
		elseif ($result === crm_call_obj::RESULT_NOTINTERESTED)
		{
			$new_call_time = time() + (86400 * 120); ///!!!! tmp. teha call_result_notinterested_recall_time prop-ga
			$new_call = $this->set_result_call_for_ended_task($call, $new_call_time);

			// set customer case on hold
			$customer_relation->set_prop("sales_state", crm_company_customer_data_obj::SALESSTATE_ONHOLD);
			$case = $customer_relation->get_sales_case(false);
			$case->set_on_hold();
		}
		elseif ($result === crm_call_obj::RESULT_REFUSED)
		{
			// set customer case on hold
			$customer_relation->set_prop("sales_state", crm_company_customer_data_obj::SALESSTATE_REFUSED);
			$case = $customer_relation->get_sales_case(false);
			$case->set_on_hold();
		}
		elseif ($result === crm_call_obj::RESULT_UNSUITABLE)
		{
			// set customer case on hold
			$customer_relation->set_prop("sales_state", crm_company_customer_data_obj::SALESSTATE_UNSUITABLE);
			$case = $customer_relation->get_sales_case(false);
			$case->set_on_hold();
		}
		elseif ($result === crm_call_obj::RESULT_PRESENTATION)
		{
			$presentation = $this->set_result_presentation_for_ended_task($call, 0);
			$customer_relation->set_prop("sales_state", crm_company_customer_data_obj::SALESSTATE_PRESENTATION);
		}
		else
		{ // result not defined
		}

		$call->save();
		$customer_relation->save();
	}

	// used in process_ ... _result() which save ended_task and cro afterwards
	// i.e. ended task and customer relation object need to be saved after calling this method
	private function set_result_call_for_ended_task(object $ended_task, $new_call_time)
	{
		$result_task_oid = $ended_task->prop("result_task");
		$result_task = false;

		if ($result_task_oid)
		{
			try
			{
				$result_task = new object($result_task_oid);
			}
			catch (awex_obj_na $e)
			{
			}
		}

		if ($result_task)
		{
			if ($result_task->is_a(CL_CRM_CALL))
			{ // call already created
				$this->set_call_time($result_task, $new_call_time);
				$result_task->save();
			}
			else
			{ // some other object is set as result, clear and create new instead
				$result_task->delete();
				$result_task = false;
			}
		}

		if (!$result_task)
		{ // create call
			$customer_relation = new object($ended_task->prop("customer_relation"));
			$prerequisites = new object_list();
			$prerequisites->add($ended_task);
			$result_task = $this->create_call($customer_relation, $new_call_time, $prerequisites);
			$ended_task->set_prop("result_task", $result_task->id());
		}

		return $result_task;
	}

	// used in process_ ... _result() which save ended_task and cro afterwards
	// i.e. ended task and customer relation object need to be saved after calling this method
	private function set_result_presentation_for_ended_task(object $ended_task, $new_presentation_time)
	{
		$result_task_oid = $ended_task->prop("result_task");
		$result_task = false;

		if ($result_task_oid)
		{
			try
			{
				$result_task = new object($result_task_oid);
			}
			catch (awex_obj_na $e)
			{
			}
		}

		if ($result_task)
		{ // presentation already created
			if ($result_task->is_a(CL_CRM_PRESENTATION))
			{
				$this->set_presentation_time($result_task, $new_presentation_time);
				$result_task->save();
			}
			else
			{ // some other object is set as result, clear and create new instead
				$result_task->delete();
				$result_task = false;
			}
		}

		if (!$result_task)
		{ // create presentation
			$customer_relation = new object($ended_task->prop("customer_relation"));
			$prerequisites = new object_list();
			$prerequisites->add($ended_task);
			$result_task = $this->create_presentation($customer_relation, $new_presentation_time, $prerequisites);
			$customer_relation->set_prop("sales_state", crm_company_customer_data_obj::SALESSTATE_PRESENTATION);
			$ended_task->set_prop("result_task", $result_task->id());
		}

		return $result_task;
	}

	/**
	@attrib api=1 params=pos
	@param customer_relation type=CL_CRM_COMPANY_CUSTOMER_DATA
	@param result type=int default=0
		One of crm_call::RESULT_... constants. If set, only calls with that result are counted. Has no effect when $result_count_mode parameter set to "all"
	@param result_count_mode type=string default="any"
		Applicable values:
		"any" counts any calls with $result. Has effect when $result parameter set
		"last_consecutive" counts last consecutive calls with $result, if any. Has effect when $result parameter set
		"all" counts calls with any result, excludes calls with no result defined
	@returns int
		Number of calls made in this sales application to customer considering given parameters
	**/
	public function get_calls_count(object $customer_relation, $result = 0, $result_count_mode = "any")
	{
		$filter = array(
			"class_id" => CL_CRM_CALL,
			"customer_relation" => $customer_relation->id()
		);

		if ($result)
		{
			$filter["result"] = $result;
		}
		elseif ("all" === $result_count_mode)
		{
			$filter["result"] = new obj_predicate_compare(OBJ_COMP_GREATER, 0);
		}

		$calls = new object_list($filter);
		return $calls->count();
	}

	/**
	@attrib api=1 params=pos
	@param customer_relation type=CL_CRM_COMPANY_CUSTOMER_DATA
	@param result type=int default=0
		One of crm_call::RESULT_... constants. If set, only presentations with that result are counted
	@param result_count_mode type=string default="any"
		Has effect when $result parameter set. Applicable values:
		"any" counts any presentations with $result.
	@returns int
		Number of presentations in this sales application to customer considering given parameters
	**/
	public function get_presentations_count(object $customer_relation, $result = 0, $result_count_mode = "any")
	{
		$filter = array(
			"class_id" => CL_CRM_PRESENTATION,
			"customer_relation" => $customer_relation->id()
		);

		if ($result)
		{
			$filter["result"] = $result;
		}

		$presentations = new object_list($filter);
		return $presentations->count();
	}

	/**
	@attrib api=1 params=pos
	@param customer_relation type=CL_CRM_COMPANY_CUSTOMER_DATA
	@returns CL_CRM_CALL
		Last call made to customer
	**/
	public function get_last_call_made(object $customer_relation)
	{
		$calls_made = new object_list(array(
			"class_id" => CL_CRM_CALL,
			"customer_relation" => $customer_relation->id(),
			"real_duration" => new obj_predicate_compare(OBJ_COMP_GREATER, 0),
			new obj_predicate_limit(1),
			new obj_predicate_sort(array("real_start" => "desc"))
		));
		return $calls_made->begin();
	}

	/** Processes presentation result. If result changes then previous result's implications are undone
	@attrib api=1 params=pos
	@param presentation type=CL_CRM_PRESENTATION
	@returns void
	@errors
		throws awex_crm_sales when customer relation is not defined for the presentation to process result for
	**/
	public function process_presentation_result(object $presentation)
	{
		$result = (int) $presentation->prop("result");

		try
		{
			$customer_relation = obj($presentation->prop("customer_relation"), array(), CL_CRM_COMPANY_CUSTOMER_DATA);
		}
		catch (Exception $e)
		{
			throw new awex_crm_sales("Customer relation not defined for presentation with id '" . $presentation->id() . "'");
		}

		if (in_array($result, crm_presentation_obj::$presentation_done_results))
		{ // cache presentations count in cro
			$customer_relation->set_prop("sales_presentations_made", $customer_relation->prop("sales_presentations_made") + 1);
		}

		if (
			crm_presentation_obj::RESULT_CANCEL_PRESENTER === $result or
			crm_presentation_obj::RESULT_CANCEL_CUSTOMER === $result
		)
		{
			// immediately call back to check why presentation didn't take place
			$new_call_time =  time();
			$new_call = $this->set_result_call_for_ended_task($presentation, $new_call_time);
			$customer_relation->set_prop("sales_state", crm_company_customer_data_obj::SALESSTATE_NEWCALL);
		}
		elseif (crm_presentation_obj::RESULT_DONE_NONE === $result)
		{
			// call back after a while to check if maybe interested then
			$new_call_time =  time() + 180*86400; //!!! normaalseks 6kuud default
			$new_call = $this->set_result_call_for_ended_task($presentation, $new_call_time);
			$customer_relation->set_prop("sales_state", crm_company_customer_data_obj::SALESSTATE_ONHOLD);

			// set customer case on hold
			$case = $customer_relation->get_sales_case(false);
			$case->set_on_hold();
		}
		elseif (
			crm_presentation_obj::RESULT_DONE_NEW_PRESENTATION === $result or
			crm_presentation_obj::RESULT_CANCEL_NEW_PRESENTATION === $result
		)
		{
			$new_presentation = $this->set_result_presentation_for_ended_task($presentation, 0);
			$customer_relation->set_prop("sales_state", crm_company_customer_data_obj::SALESSTATE_PRESENTATION);
		}
		elseif (crm_presentation_obj::RESULT_DONE_SALE === $result)
		{
			$customer_relation->set_prop("sales_state", crm_company_customer_data_obj::SALESSTATE_SALE);
		}
		elseif (
			crm_presentation_obj::RESULT_DONE_NEW_CALL === $result or
			crm_presentation_obj::RESULT_CANCEL_NEW_CALL === $result
		)
		{
			$new_call = $this->set_result_call_for_ended_task($presentation, 0);
		}
		elseif (crm_presentation_obj::RESULT_CANCEL === $result)
		{
		}
		elseif (
			crm_presentation_obj::RESULT_CANCEL_REFUSED === $result or
			crm_presentation_obj::RESULT_DONE_REFUSED === $result
		)
		{
			$customer_relation->set_prop("sales_state", crm_company_customer_data_obj::SALESSTATE_REFUSED);

			// set customer case on hold
			$case = $customer_relation->get_sales_case(false);
			$case->set_on_hold();
		}
		else
		{ // result not defined
		}

		$customer_relation->save();
	}

	public function get_offer_templates()
	{
		static $ol;

		if(!isset($ol))
		{
			$ol = new object_list(array(
				"class_id" => CL_CRM_OFFER_TEMPLATE,
				"offer(CL_CRM_OFFER).parent" => $this->prop("offers_folder"),
			));
		}

		return $ol;
	}

	public function get_cfgform_for_object(object $object)
	{
		$clid = $object->class_id();
		$role = $this->get_current_user_role();
		$cfgform = null;
		if (CL_CRM_SALES == $clid)
		{
			$cfgform_oid = $this->prop("cfgf_main_" . self::$role_ids[$role]);
			if (is_oid($cfgform_oid))
			{
				$cfgform = new object($cfgform_oid);
			}
		}
		elseif (CL_CRM_CALL == $clid)
		{
			$cfgform_oid = $this->prop("cfgf_call_" . self::$role_ids[$role]);
			if (is_oid($cfgform_oid))
			{
				$cfgform = new object($cfgform_oid);
			}
		}
		elseif (CL_CRM_PRESENTATION == $clid)
		{
			$cfgform_oid = $this->prop("cfgf_presentation_" . self::$role_ids[$role]);
			if (is_oid($cfgform_oid))
			{
				$cfgform = new object($cfgform_oid);
			}
		}
		return $cfgform;
	}

	private function set_call_time(object $call, $time)
	{
		if ($time > 1)
		{
			$call->set_prop("start1", $time);
			$call->set_prop("deadline", $time + 900); //!!! normaalseks
			$call->set_prop("end", $time + $this->prop("avg_call_duration_est"));
		}
	}

	private function set_presentation_time(object $presentation, $time)
	{//!!! saab muuta ainult kuni mingi tingimuseni -- myygimehe plaani koostamiseni vms.
		if ($time > 1)
		{
			$presentation->set_prop("start1", $time);
			$presentation->set_prop("end", $time + $this->prop("avg_presentation_duration_est"));
		}
	}

	private function get_customer_case(object $customer_relation, $create = false)
	{
		return $customer_relation->get_sales_case($create);
	}

	/**
	@attrib api=1 params=pos
	@param limit type=obj_predicate_limit default=NULL
		Limit determines result type
	@param customer_params type=array default=array()
		Constraining parameters for calls to return. Associative array containing any or all of following elements:
			"name" - customer name substring
			"salesman" - salesperson oid assigned to associated customer
			"last_caller" - person oid who made the last call made to associated customer
			"last_call_result" - result of last call made to associated customer. one of crm_call_obj::RESULT_... constants
			"address" - freeform address search string (if specified then length must be greater than 1)
			"phone" - one of customer's phone numbers is or starts with given numeric string
			"status" - customer relation sales state. one of crm_company_customer_data_obj::SALESSTATE_... constants
	@param order type=string default="current"
		Result ordering options:
			current - ordered by deadline with in progress calls at front
			name ASC|DESC - customer name
			last_call_time ASC|DESC - order by time of last call made to the customer associated with the call
			last_call_maker ASC|DESC
			last_call_result ASC|DESC
			calls_made ASC|DESC - total nr of calls made to customer
			deadline ASC|DESC - planned deadline
			salesman ASC|DESC - salesman assigned to customer in this sales application
			Example: "salesman DESC" orders results alphabetically by salesman name in descending direction
			Default direction is ASC

	@returns object_list/int
		If limit is not specified then this method returns the number of calls to be made. If limit given then object_list of these calls

	@errors
		throws awex_obj_type on argument type errors
	**/
	public function get_current_calls_to_make(obj_predicate_limit $limit = null, $customer_params = array(), $order = "current")
	{
		$role = $this->get_current_user_role();
		$calls_folder = $this->prop("calls_folder");
		$seller = $this->awobj_get_owner()->id();
		$address_clid = CL_ADDRESS;
		$phone_clid = CL_CRM_PHONE;
		$crm_call_clid = CL_CRM_CALL;
		$crm_person_clid = CL_CRM_PERSON;
		$crm_company_clid = CL_CRM_COMPANY;
		$sales_state_unsuitable = crm_company_customer_data_obj::SALESSTATE_UNSUITABLE;
		$i = $this->instance();
		$real_duration_constraint = $order_by = $limit_str = $group_by = $additional_joins = "";


		// result format and limit
		if (null === $limit)
		{
			// count only
			$select = "count(call_objects.oid) as count";
		}
		else
		{
			$select = "call_objects.oid as oid";
			$limit_start = $limit->get_from();
			$limit_count = $limit->get_per_page();
			$limit_str = "LIMIT {$limit_start},{$limit_count}";
			$group_by = "GROUP BY call_objects.oid";

			// results' order
			$order = explode (" ", $order);
			$order = $order[0];
			$sort_direction = (isset($order[1]) and "DESC" === $order[1]) ? "DESC" : "ASC";

			if ("current" === $order)
			{
				$order_by = <<<END
ORDER BY
	IF((planner.`real_start` > 1), 0, 1),
	planner.`real_start` ASC ,
	IF((planner.`deadline` > 10000), 0, 1),
	planner.`deadline` ASC
END;
			}
			elseif ("name" === $order)
			{
				$additional_joins .= "LEFT JOIN objects customer_objects on customer_objects.oid=aw_crm_customer_data.aw_buyer\n";
				$order_by = "ORDER BY customer_objects.name {$sort_direction}";
			}
			elseif ("last_call_time" === $order)
			{
				$additional_joins .= "LEFT JOIN planner last_call on last_call.id=aw_crm_customer_data.aw_sales_last_call\n";
				$order_by = "ORDER BY last_call.real_start {$sort_direction}";
			}
			elseif ("last_call_maker" === $order)
			{
				$additional_joins .= "LEFT JOIN planner last_call on last_call.id=aw_crm_customer_data.aw_sales_last_call\n";
				$order_by = "ORDER BY last_call.real_maker {$sort_direction}";
			}
			elseif ("last_call_result" === $order)
			{
				$additional_joins .= "LEFT JOIN planner last_call on last_call.id=aw_crm_customer_data.aw_sales_last_call\n";
				$order_by = "ORDER BY last_call.result {$sort_direction}";
			}
			elseif ("deadline" === $order)
			{
				$order_by = "ORDER BY 	IF((planner.`deadline` > 10000), 1, 0), planner.deadline {$sort_direction}";
			}
			elseif ("calls_made" === $order)
			{
				$order_by = "ORDER BY aw_crm_customer_data.aw_sales_calls_made {$sort_direction}";
			}
			elseif ("salesman" === $order)
			{
				$additional_joins .= "LEFT JOIN objects salesman_objects on salesman_objects.oid=aw_crm_customer_data.aw_salesman\n";
				$order_by = "ORDER BY salesman_objects.name {$sort_direction}";
			}
			else
			{
				throw new awex_obj_type("Invalid order '$order'.");
			}
		}

		// role specific parameters
		switch ($role)
		{
			case crm_sales_obj::ROLE_GENERIC:
				break;

			case crm_sales_obj::ROLE_DATA_ENTRY_CLERK:
				break;

			case crm_sales_obj::ROLE_TELEMARKETING_SALESMAN:
				$real_duration_constraint = "planner.`real_duration` < 1  AND";
				break;

			case crm_sales_obj::ROLE_TELEMARKETING_MANAGER:
				break;

			case crm_sales_obj::ROLE_SALESMAN:
				break;

			case crm_sales_obj::ROLE_MANAGER:
				break;
		}


		if (count($customer_params) === 0)
		{ // special case for default list
			$real_duration_constraint = "planner.`real_duration` < 1  AND";
			$q = <<<EOQ
SELECT
	{$select}

FROM
	aw_crm_customer_data
	INNER JOIN planner on planner.customer_relation = aw_crm_customer_data.aw_oid
	{$additional_joins}
	INNER JOIN objects call_objects on call_objects.oid = planner.id

WHERE
	call_objects.`class_id`={$crm_call_clid} AND
	call_objects.`parent`={$calls_folder} AND
	{$real_duration_constraint}
	aw_crm_customer_data.`aw_seller`={$seller} AND
	aw_crm_customer_data.`aw_sales_status`!={$sales_state_unsuitable} AND
	call_objects.`status` > 0

{$group_by}
{$order_by}
{$limit_str};
EOQ;
		}
		elseif (!empty($customer_params["address"]))
		{ // special case for address search
			if (strlen($customer_params["address"]) < 2)
			{
				throw new awex_obj_type("Address search string must be longer than one character.");
			}

			if (empty($customer_params["status"]))
			{
				$customer_status_constraint = "";
			}
			else
			{
				$status = (int) $customer_params["status"];
				$customer_status_constraint = "aw_crm_customer_data.`aw_sales_status`= {$status} AND";
			}

			if (empty($customer_params["phone"]))
			{
				$phone_constraint = "";
			}
			else
			{
				$phone_string = addslashes($customer_params["phone"]);
				$phone_constraint = "phone_objects.name LIKE '{$phone_string}%' AND";
				$additional_joins .= "INNER JOIN aliases phone_aliases on phone_aliases.source = customer_objects.oid\n";
				$additional_joins .= "INNER JOIN objects phone_objects on phone_aliases.target = phone_objects.oid\n";
				$additional_joins .= "LEFT JOIN planner phone_data on phone_objects.oid = planner.id\n";
			}

			$address_string = addslashes($customer_params["address"]);
			$q = <<<EOQ
SELECT
	{$select}

FROM
	objects address_objects
	INNER JOIN aliases address_aliases on address_aliases.target = address_objects.oid
	INNER JOIN objects customer_objects on customer_objects.oid = address_aliases.source
	INNER JOIN aw_crm_customer_data on aw_crm_customer_data.aw_buyer = customer_objects.oid
	INNER JOIN planner on planner.customer_relation = aw_crm_customer_data.aw_oid
	{$additional_joins}
	INNER JOIN objects call_objects on call_objects.oid = planner.id

WHERE
	address_objects.`class_id`={$address_clid} AND
	address_objects.`name` LIKE '{$address_string}' AND
	(customer_objects.`class_id`={$crm_person_clid} OR customer_objects.`class_id`={$crm_company_clid}) AND
	call_objects.`class_id`={$crm_call_clid} AND
	call_objects.`parent`={$calls_folder} AND
	{$phone_constraint}
	{$customer_status_constraint}
	{$real_duration_constraint}
	aw_crm_customer_data.`aw_seller`={$seller} AND
	address_objects.`status` > 0 AND
	customer_objects.`status` > 0 AND
	call_objects.`status` > 0

{$group_by}
{$order_by}
{$limit_str};
EOQ;
		}
		elseif (!empty($customer_params["phone"]))
		{ // special case for phone search
			if (strlen($customer_params["phone"]) < 2)
			{ // if searching by phone number only then this requirement
				throw new awex_obj_type("Phone search string must be longer than one character.");
			}

			if (empty($customer_params["status"]))
			{
				$customer_status_constraint = "";
			}
			else
			{
				$status = (int) $customer_params["status"];
				$customer_status_constraint = "aw_crm_customer_data.`aw_sales_status`= {$status} AND";
			}

			$phone_string = addslashes($customer_params["phone"]);
			$q = <<<EOQ
SELECT
	{$select}

FROM
	objects phone_objects
	INNER JOIN aliases phone_aliases on phone_aliases.target = phone_objects.oid
	INNER JOIN objects customer_objects on customer_objects.oid = phone_aliases.source
	INNER JOIN aw_crm_customer_data on aw_crm_customer_data.aw_buyer = customer_objects.oid
	INNER JOIN planner on planner.customer_relation = aw_crm_customer_data.aw_oid
	{$additional_joins}
	INNER JOIN objects call_objects on call_objects.oid = planner.id

WHERE
	phone_objects.`class_id`={$phone_clid} AND
	phone_objects.`name` LIKE '{$phone_string}%' AND
	(customer_objects.`class_id`={$crm_person_clid} OR customer_objects.`class_id`={$crm_company_clid}) AND
	call_objects.`class_id`={$crm_call_clid} AND
	call_objects.`parent`={$calls_folder} AND
	{$customer_status_constraint}
	{$real_duration_constraint}
	aw_crm_customer_data.`aw_seller`={$seller} AND
	(phone_aliases.`reltype` = 13 OR phone_aliases.`reltype` = 17) AND
	phone_objects.`status` > 0 AND
	customer_objects.`status` > 0 AND
	call_objects.`status` > 0

{$group_by}
{$order_by}
{$limit_str};
EOQ;
		}
		else
		{
			return (null === $limit) ? 0 : new object_list();
		}
		// generic case

		// parse database result
		if (null === $limit)
		{
			$calls_count = $i->db_fetch_field($q, "count");
			$calls = $calls_count;
		}
		else
		{
			$call_oids = $i->db_fetch_array($q);
			$calls = new object_list();
			foreach ($call_oids as $key => $oid_data)
			{
				try
				{
					$call = new object($oid_data["oid"]);
					$calls->add($call);
				}
				catch (aw_lock_exception $e)
				{
				}
			}
		}
		return $calls;
	}

	public function get_contract_list()
	{
		$ol = is_oid($this->id()) ? new object_list(array(
			"class_id" => CL_CRM_DEAL,
			"parent" => $this->prop("contracts_folder"),
		)) : new object_list();
		return $ol;
	}

	/**
	@attrib api=1 params=name

	@param category optional type=int/array/string
		The OIDs of the category of price components to be returned. If 'undefined' given, price components without category set will be returned. May give multiple categories.

	@returns object_list
		Returns an object list of all price component objects defined for current crm_sales object. If current crm_sales object is not yet saved empty object list is returned.
	**/
	public function get_price_component_list($additional_predicates = array())
	{
		$predicates = array(
			"class_id" => CL_CRM_SALES_PRICE_COMPONENT,
			"application" => $this->id(),
			"type" => new obj_predicate_not(crm_sales_price_component_obj::TYPE_NET_VALUE),
			new obj_predicate_sort(array(
				"name" => "ASC"
			)),
		);
		
		foreach ($additional_predicates as $predicate_key => $predicate)
		{
			switch ($predicate_key)
			{
				case "category":
					$predicate = (array)$predicate;
					if (in_array("undefined", $predicate))
					{
						$predicates[] = new object_list_filter(array(
							"logic" => "OR",
							"conditions" => array(
								"category" => $predicate,
								new object_list_filter(array(
									"logic" => "OR",
									"conditions" => array(
										"category" => new obj_predicate_compare(obj_predicate_compare::NULL),
									)
								)),
								new object_list_filter(array(
									"logic" => "OR",
									"conditions" => array(
										"category" => 0,
									)
								)),
							),
						));
					}
					else
					{
						$predicates[$predicate_key] = $predicate;
					}
					break;

				default:
					$predicates[$predicate_key] = $predicate;
			}
		}

		$ol = is_oid($this->id()) ? new object_list($predicates) : new object_list();
		return $ol;
	}

	/**
		@attrib api=1

		@returns object_list
	**/
	public function get_price_component_category_list()
	{
		$ol = is_oid($this->id()) ? new object_list(array(
			"class_id" => CL_CRM_SALES_PRICE_COMPONENT_CATEGORY,
			"parent" => $this->prop("price_component_categories_folder"),
		)) : new object_list();
		return $ol;
	}

	/**	Returns list of price component and price component categories to be shown as a separate column in the offers statistics view.
		@attrib api=1
		@returns int[]
	**/
	public function get_price_components_and_categories_shown_in_statistics()
	{
		$ret = $this->meta("show_in_statistics");
		return is_array($ret) ? $ret : array();
	}

	/**
		@attrib api=1 params=pos
		@param ids required type=int[]
	**/
	public function set_price_components_and_categories_shown_in_statistics($ids)
	{
		$this->set_meta("show_in_statistics", $ids);
	}
}

/** Generic sales application exception **/
class awex_crm_sales extends awex_crm {}

/** Application owner company error **/
class awex_crm_sales_owner extends awex_crm_sales {}

/** Error with handling sales resource manager **/
class awex_crm_sales_resmgr extends awex_crm_sales {}

/** Error with handling sales case **/
class awex_crm_sales_case extends awex_crm_sales {}

/** Expected folder not found or invalid **/
class awex_crm_sales_folder extends awex_crm_sales {}

/** Call error **/
class awex_crm_sales_call extends awex_crm_sales {}

/** Task type is not what expected **/
class awex_crm_sales_task_type extends awex_crm_sales {}
