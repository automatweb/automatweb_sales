<?php

class crm_call_obj extends task_object implements crm_sales_price_component_interface, crm_offer_row_interface
{
	const CLID = 223;
	const RESULT_CALL = 1;
	const RESULT_PRESENTATION = 2;
	const RESULT_REFUSED = 3;
	const RESULT_NOANSWER = 4;
	const RESULT_BUSY = 5;
	const RESULT_HUNGUP = 6;
	const RESULT_OUTOFSERVICE = 7; // number is out of service at the moment
	const RESULT_INVALIDNR = 8; // number is not used or invalid
	const RESULT_VOICEMAIL = 9; // voicemail or answering machine
	const RESULT_NEWNUMBER = 10; // a redirect or comment from answerer giving a new number to call the contact
	const RESULT_DISCONNECTED = 11; // an error occurred during call, got disconnected, ...
	const RESULT_NOTINTERESTED = 12;
	const RESULT_UNSUITABLE = 13; // contact is unsuitable (a child, someone not applicable to caller's ends)
	const RESULT_LANG = 14; // contact prefers/needs to be addressed in a different language

	const ERR_STATE_ENDED = 1; // call state error code for situations where 'ended' is unexpected
	const ERR_STATE_NEW = 2;// call state error code for situations where 'new' is unexpected

	const DEFAULT_DURATION = 600; // default planned call length, seconds

	private static $read_only_when_done = array(
		"real_start", "start1", "end", "real_duration", "real_maker", "deadline"
	);

	//	Written solely for testing purposes!
	public function get_units()
	{
		$ol = new object_list(array(
			"class_id" => CL_UNIT,
			"status" => object::STAT_ACTIVE,
		));
		return $ol;
	}

	public function set_prop($name, $value)
	{
		if ($this->prop("real_duration") > 0 and in_array($name, self::$read_only_when_done))
		{
			return $this->prop($name);
		}
		else
		{
			return parent::set_prop($name, $value);
		}
	}

	/** Returns list of call result options
	@attrib api=1 params=pos
	@param result type=int
		Result constant value to get name for, one of crm_call_obj::RESULT_*
	@returns array
		Format option value => human readable name, if result parameter set, array with one element returned and empty array when that result not found.
	**/
	public static function result_names($result = null)
	{
		$result_names = array(
			self::RESULT_CALL => t("Uus k&otilde;ne"),
			self::RESULT_PRESENTATION => t("Esitlus"),
			self::RESULT_NOANSWER => t("Ei vasta"),
			self::RESULT_BUSY => t("Kinni"),
			self::RESULT_OUTOFSERVICE => t("Teenindusest v&auml;ljas"),
			self::RESULT_VOICEMAIL => t("K&otilde;nepost/automaatvastaja"),
			self::RESULT_REFUSED => t("Keeldub kontaktist"),
			self::RESULT_NOTINTERESTED => t("Hetkel pole huvitatud"),
			self::RESULT_UNSUITABLE => t("Sobimatu kontakt"),
			self::RESULT_LANG => t("Muu suhtluskeel"),
			self::RESULT_HUNGUP => t("Katkestas k&otilde;ne"),
			self::RESULT_INVALIDNR => t("Vigane number/pole kasutusel"),
			self::RESULT_DISCONNECTED => t("K&otilde;ne katkes"),
			self::RESULT_NEWNUMBER => t("Number muutund")
		);

		if (isset($result))
		{
			if (isset($result_names[$result]))
			{
				$result_names = array($result => $result_names[$result]);
			}
			else
			{
				$result_names = array();
			}
		}

		return $result_names;
	}

	/** Tells if call can be started.
	@attrib api=1 params=pos
	@returns bool
	@errors
		none
	**/
	public function can_start()
	{
		$can_start = ($this->prop("real_start") < 2);
		if (is_oid($this->prop("hr_schedule_job")))
		{
			$job = new object($this->prop("hr_schedule_job"));
			$can_start = ($can_start and $job->can_start(false, true));
		}
		return $can_start;
	}

	/** Tells if call is in progress.
	@attrib api=1 params=pos
	@returns bool
	@errors
		none
	**/
	public function is_in_progress()
	{
		return (($this->prop("real_duration") < 1) and ($this->prop("real_start") > 1));
	}

	/** Returns TRUE if call has ended or is in progress
	@attrib api=1 params=pos
	@returns bool
	@errors
		none
	**/
	public function has_started()
	{
		return ($this->prop("real_start") > 1);
	}

	/** Tells if call is made and ended.
	@attrib api=1 params=pos
	@returns bool
	@errors
		none
	**/
	public function has_ended()
	{
		return (($this->prop("real_duration") > 0) and ($this->prop("real_start") > 1));
	}

	/** Makes a phone call.
	@attrib api=1 params=pos
	@param phone type=CL_CRM_PHONE default=null
		If not set, 'phone' property must be set
	@returns void
	@errors
		throws awex_crm_call_state when call already started
		throws awex_crm_call_job when call job is not defined
		throws awex_mrp_resource_unavailable when maker has another unfinished job
	**/
	public function make(object $phone = null)
	{
		if ($this->prop("real_duration") > 0)
		{
			throw new awex_crm_call_state("Call has already been made");
		}

		if (!is_oid($this->prop("hr_schedule_job")))
		{
			throw new awex_crm_call_job("Call job not defined");
		}

		if ($phone instanceof object and $phone->is_a(CL_CRM_PHONE))
		{
			$this->set_prop("phone", $phone->id());
		}

		$current_person = get_current_person();
		$this->set_prop("real_start", time());
		$this->set_prop("real_maker", $current_person->id());

		$application = automatweb::$request->get_application();
		if ($application->is_a(CL_CRM_SALES))
		{
			$company = $application->prop("owner");
		}
		else
		{
			$company = get_current_company();
		}

		$person_resource = mrp_workspace_obj::get_person_resource($company, $current_person);
		$job = new object($this->prop("hr_schedule_job"));

		if (!($info = $job->can_start(false, true)))
		{
			throw new awex_crm_call_state("Call job can't start");
		}

		$old_resource = $job->prop("resource");
		$job->set_prop("resource", $person_resource->id());
		$job->save();

		try
		{
			$job->start();
		}
		catch (awex_mrp_case_state $e)
		{
			// !!! v6ibolla 'unscheduled call' siis peaks midagi muud m6tlema sest see on normaalse wf osa ja exceptioni kaudu liiga kulukas
			try
			{
				// try to plan customer case and try start again
				$customer_relation = new object($this->prop("customer_relation"));
				$customer_case = $customer_relation->get_sales_case(false);
				$customer_case->plan();
				$job->start();
			}
			catch (Exception $e)
			{
				$job->set_prop("resource", $old_resource);
				$job->save();
				throw $e;
			}
		}
		catch (awex_mrp_job_state $e)
		{
			if (mrp_job_obj::STATE_INPROGRESS != $job->prop("state"))
			{
				// !!! v6ibolla 'unscheduled call' siis peaks midagi muud m6tlema sest see on normaalse wf osa ja exceptioni kaudu liiga kulukas
				try
				{
					// try to plan job and try start again
					$job->plan();
					$job->start();
				}
				catch (awex_mrp_case_state $e)
				{ //XXX: see if possible w/o dbl check for same exception
					try
					{
						// try to plan customer case and try start again
						$customer_relation = new object($this->prop("customer_relation"));
						$customer_case = $customer_relation->get_sales_case(false);
						$customer_case->plan();
						$job->start();
					}
					catch (Exception $e)
					{
						$job->set_prop("resource", $old_resource);
						$job->save();
						throw $e;
					}
				}
				catch (Exception $e)
				{
					$job->set_prop("resource", $old_resource);
					$job->save();
					throw $e;
				}
			}
		}
		catch (awex_mrp_resource_unavailable $e)
		{
			$person_resource->restore_data_integrity();

			try
			{
				$job->start();
			}
			catch (awex_mrp_case_state $e)
			{
				// !!! v6ibolla 'unscheduled call' siis peaks midagi muud m6tlema sest see on normaalse wf osa ja exceptioni kaudu liiga kulukas
				try
				{
					// try to plan customer case and try start again
					$customer_relation = new object($this->prop("customer_relation"));
					$customer_case = $customer_relation->get_sales_case(false);
					$customer_case->plan();
					$job->start();
				}
				catch (Exception $e)
				{
					$job->set_prop("resource", $old_resource);
					$job->save();
					throw $e;
				}
			}
		}

		$this->save();

		if ($application->is_a(CL_CRM_SALES))
		{
			// send call start message to sales application
			$application->make_call(new object($this->id()));
		}
	}

	/** Schedules the phone call.
	@attrib api=1 params=pos
	@param resource type=CL_MRP_RESOURCE
		resource to schedule the call to
	@param prerequisite_tasks type=object_list default=null
		Tasks (CL_TASK and extensions) that the call will have as prerequisites.
	@returns void
	@errors
		throws awex_crm_call_state when call state doesn't allow scheduling
		throws awex_crm_call_cr when call state doesn't allow scheduling
	@comment
		Requires customer_relation to be set. Saves object (calls self::save())
	**/
	public function schedule(object $resource, object_list $prerequisite_tasks = null)
	{
		if ($this->prop("real_duration") > 0 or $this->prop("real_start") > 1)
		{
			throw new awex_crm_call_state("Call has already been made");
		}

		$customer_relation = new object($this->prop("customer_relation"));
		if (!$customer_relation->is_saved())
		{
			throw new awex_crm_call_cr("Customer relation must be defined");
		}

		if (is_oid($this->prop("hr_schedule_job")))
		{
			$job = new object($this->prop("hr_schedule_job"));
			$case = $customer_relation->get_sales_case(false);
		}
		else
		{
			$case = $customer_relation->get_sales_case(true);
			$job = $case->add_job();
			$this->set_prop("hr_schedule_job", $job->id());
		}

		// set tasks that this call is a result to as prerequisites
		if (isset($prerequisite_tasks))
		{
			if ($prerequisite_tasks->count() > 0)
			{
				$prerequisite_jobs = new object_list();
				foreach ($prerequisite_tasks->arr() as $task)
				{
					if (is_oid($task->prop("hr_schedule_job")))
					{
						$prerequisite_jobs->add($task->prop("hr_schedule_job"));
					}
				}

				$job->set_prop("prerequisites", $prerequisite_jobs);
			}
		}

		$job->set_prop("resource", $resource->id());
		$planned_length = $this->prop("end") > $this->prop("start1") ? $this->prop("end") - $this->prop("start1") : 0;
		$job->set_prop("planned_length", $planned_length);
		$job->set_prop("minstart", $this->prop("start1"));
		$time = $this->prop("start1");

		if (mrp_job_obj::STATE_PLANNED == $job->prop("state"))
		{
			$job->save();
		}
		else
		{
			if ($time < time())
			{ // an unscheduled call
				$job->set_on_hold();
			}
			else
			{
				try
				{
					$case->plan();
				}
				catch (awex_mrp_case_state $e)
				{
					if ($case->prop("state") != mrp_case_obj::STATE_INPROGRESS)
					{
						throw $e;
					}
				}

				$job->plan();
			}
		}

		$this->save();
	}

	public function schedule_new(object $resource)
	{
		$customer_relation = new object($this->prop("customer_relation"));
		$case = $customer_relation->get_sales_case(true);
		$job = $case->add_job();
		$this->set_prop("hr_schedule_job", $job->id());
		$job->set_prop("resource", $resource->id());
		$planned_length = $this->prop("end") > $this->prop("start1") ? $this->prop("end") - $this->prop("start1") : 0;
		$job->set_prop("planned_length", $planned_length);
		$job->set_prop("minstart", $this->prop("start1"));
		$time = $this->prop("start1");
		$job->set_on_hold();
		$this->save();
	}

	/** Finishes phone call.
	@attrib api=1 params=pos
	@returns void
	@errors
		throws awex_crm_call_state when call not started or already ended
	**/
	public function end()
	{
		if ($this->prop("real_duration") > 0)
		{
			throw new awex_crm_call_state("Call was already ended", self::ERR_STATE_ENDED);
		}

		if ($this->prop("real_start") < 2)
		{
			throw new awex_crm_call_state("Call not started", self::ERR_STATE_NEW);
		}

		$this->set_prop("real_duration", (time() - $this->prop("real_start")));

		if (is_oid($this->prop("hr_schedule_job")))
		{
			$job = new object($this->prop("hr_schedule_job"));

			try
			{
				$job->done();
			}
			catch (awex_mrp_job_state $e)
			{
				if (mrp_job_obj::STATE_DONE != $job->prop("state"))
				{
					throw $e;
				}
			}
		}

		$this->save();

		$application = automatweb::$request->get_application();
		if ($application->is_a(CL_CRM_SALES))
		{
			// send call end message to sales application
			$application->end_call(new object($this->id()));
		}
	}

	public function save($exclusive = false, $previous_state = null)
	{
		$application = automatweb::$request->get_application();
		if ($application->is_a(CL_CRM_SALES))
		{ // process result. if result requires a task, check if it exists and create if it doesn't. delete old result task if result has changed.
			$result_task_oid = (int) $this->prop("result_task");
			$result = (int) $this->prop("result");
			$result_task = null;

			if ($result_task_oid)
			{
				try
				{
					$result_task = new object($result_task_oid);
				}
				catch (awex_obj_na $e)
				{
					$this->set_prop("result_task", 0);
				}
			}

			if (crm_call_obj::RESULT_PRESENTATION === $result)
			{ // result requires a result task
				if (!$result_task)
				{ // no existing presentation, create
					$customer_relation = obj($this->prop("customer_relation"), array(), CL_CRM_COMPANY_CUSTOMER_DATA);
					$result_task = $application->create_presentation($customer_relation);
					$this->set_prop("result_task", $result_task->id());
				}
				elseif (!$result_task->is_a(CL_CRM_PRESENTATION))
				{
					if (!$result_task->is_a(CL_CRM_CALL) or $result_task->prop("real_start") < 2)
					{ // result task was an unstarted call or some other object
						$result_task->delete();
					}

					$result_task = null;
				}
			}
			elseif ($result_task and $result_task->is_a(CL_CRM_PRESENTATION))
			{ // clear result presentation if call result changed from presentation to something else
				$result_task->delete();
				$this->set_prop("result_task", 0);
			}
		}

		$r = parent::save($exclusive, $previous_state);

		if (is_oid($this->prop("hr_schedule_job")))
		{
			$job = new object($this->prop("hr_schedule_job"));
			$planned_length = $this->prop("end") > $this->prop("start1") ? $this->prop("end") - $this->prop("start1") : 0;
			if ($this->prop("start") != $job->prop("minstart") or $planned_length != $job->prop("planned_length"))
			{
				$job->set_prop("minstart", $this->prop("start1"));
				$job->set_prop("planned_length", $planned_length);
				try
				{
					$job->plan();
				}
				catch (Exception $e)
				{
				}
			}
		}

		return $r;
	}
}

/** Generic call error **/
class awex_crm_call extends awex_obj {}

/** Call status error **/
class awex_crm_call_state extends awex_crm_call {}

/** Call customer relation error **/
class awex_crm_call_cr extends awex_crm_call {}

/** Call job error **/
class awex_crm_call_job extends awex_crm_call {}

?>
