<?php

class crm_presentation_obj extends task_object implements crm_sales_price_component_interface, crm_offer_row_interface
{
	const CLID = 1560;
	const RESULT_CALL = 1; // reserve for future use, check applicable code for integrity when starting to use
	const RESULT_PRESENTATION = 2; // reserve for future use, check applicable code for integrity when starting to use
	const RESULT_SALE = 3;
	const RESULT_MISS = 4;
	const RESULT_REFUSED = 5;
	const RESULT_NONE = 6;
	const RESULT_CANCEL = 7;

	const RESULT_DONE_NEW_CALL= 1;
	const RESULT_DONE_NEW_PRESENTATION = 2;
	const RESULT_DONE_SALE = 3;
	const RESULT_DONE_NONE = 6;
	const RESULT_DONE_REFUSED = 8;
	const RESULT_CANCEL_NEW_CALL = 9;
	const RESULT_CANCEL_NEW_PRESENTATION = 10;
	const RESULT_CANCEL_PRESENTER = 4;
	const RESULT_CANCEL_CUSTOMER = 7;
	const RESULT_CANCEL_REFUSED = 5;

	public static $presentation_done_results = array(
		self::RESULT_DONE_NEW_CALL,
		self::RESULT_DONE_NEW_PRESENTATION,
		self::RESULT_DONE_SALE,
		self::RESULT_DONE_NONE,
		self::RESULT_DONE_REFUSED
	);

	private static $result_names = array();
	private static $save_locked = false;

	private static $read_only_when_done = array(
		// "real_start",
		// "real_duration",
		"start1",
		"end",
		"result",
		"real_maker",
		"deadline"
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
		if ($this->is_done() and in_array($name, self::$read_only_when_done))
		{
			return $this->prop($name);
		}
		else
		{
			return parent::set_prop($name, $value);
		}
	}

	/** Returns list of presentation result options
	@attrib api=1 params=pos
	@param result type=int default=NULL
		Result constant value to get name for, one of crm_presentation_obj::RESULT_*
	@returns array
		Format option value => human readable name, if result parameter set, array with one element returned and empty array when that result not found.
	**/
	public static function result_names($result = null)
	{
		if (empty(self::$result_names))
		{
			self::$result_names = array(
				self::RESULT_CALL => t("Uus k&otilde;ne"), // reserve for future use, check applicable code for integrity when starting to use
				// self::RESULT_PRESENTATION => t("Uus esitlus"), // reserve for future use, check applicable code for integrity when starting to use
				self::RESULT_SALE => t("Toote m&uuml;&uuml;k"),
				self::RESULT_MISS => t("Esitlus j&auml;i &auml;ra"),
				self::RESULT_CANCEL => t("Loobus"),
				self::RESULT_REFUSED => t("Keeldub kontaktist"),
				self::RESULT_NONE => t("Esitlus toimus"),


				self::RESULT_DONE_NEW_CALL => t("Toimus - uus k&otilde;ne"),
				self::RESULT_DONE_NEW_PRESENTATION => t("Toimus - uus esitlus"),
				self::RESULT_DONE_SALE => t("Toimus - toote m&uuml;&uuml;k"),
				self::RESULT_DONE_NONE => t("Toimus - tulemuseta"),
				self::RESULT_DONE_REFUSED => t("Toimus - keeldub kontaktist"),
				self::RESULT_CANCEL_NEW_CALL => t("J&auml;i &auml;ra - uus k&otilde;ne"),
				self::RESULT_CANCEL_NEW_PRESENTATION => t("J&auml;i &auml;ra - uus esitlus"),
				self::RESULT_CANCEL_PRESENTER => t("J&auml;i &auml;ra - esitleja t&otilde;ttu"),
				self::RESULT_CANCEL_CUSTOMER => t("J&auml;i &auml;ra - kliendi t&otilde;ttu"),
				self::RESULT_CANCEL_REFUSED => t("J&auml;i &auml;ra - keeldub kontaktist")
			);
		}

		if (isset($result))
		{
			if (isset(self::$result_names[$result]))
			{
				$result_names = array($result => self::$result_names[$result]);
			}
			else
			{
				$result_names = array();
			}
		}
		else
		{
			$result_names = self::$result_names;
		}

		return $result_names;
	}

	public function awobj_set_start1($value)
	{
		$application = automatweb::$request->get_application();
		if ($application->is_a(CL_CRM_SALES))
		{
			$role = $application->get_current_user_role();
			if (crm_sales_obj::ROLE_SALESMAN === $role and !$this->prop("result") and $value > 0 and $value < time())
			{
				throw new awex_crm_presentation_time("Start cannot be in the past");
			}
		}

		$this->set_prop("start1", $value);
	}

	public function awobj_set_real_start($value)
	{
		if ($value > time())
		{
			throw new awex_crm_presentation_time("Real start cannot be in the future");
		}

		if ($this->prop("real_duration") + $value > time())
		{
			throw new awex_crm_presentation_time("Real end can't be in the future");
		}

		$this->set_prop("real_start", $value);
	}

	public function awobj_set_real_duration($value)
	{
		if ($this->prop("real_start") + $value > time())
		{
			throw new awex_crm_presentation_time("Real end can't be in the future");
		}

		$this->set_prop("real_duration", $value);
	}

	/** Schedules the presentation
	@attrib api=1 params=pos
	@param resource type=CL_MRP_RESOURCE
		resource to schedule the presentation to
	@param prerequisite_tasks type=object_list default=null
		Tasks (CL_TASK and extensions) that the presentation will have as prerequisites.
	@returns void
	@errors
		throws awex_crm_presentation_state when presentation state doesn't allow scheduling
		throws awex_crm_custrel when customer relation not defined
	@comment
		Requires customer_relation to be set. Saves object (calls self::save())
	**/
	public function schedule(object $resource, object_list $prerequisite_tasks = null)
	{
		if ($this->prop("real_duration") > 0 or $this->prop("real_start") > 1 or $this->is_done())
		{
			throw new awex_crm_presentation_state("Can't reschedule an ended presentation.");
		}

		$customer_relation = new object($this->prop("customer_relation"));
		if (!$customer_relation->is_saved())
		{
			throw new awex_crm_custrel("Customer relation must be defined");
		}

		if (is_oid($this->prop("hr_schedule_job")))
		{
			$job = new object($this->prop("hr_schedule_job"));
		}
		else
		{
			$case = $customer_relation->get_sales_case(true);
			$job = $case->add_job();
			$this->set_prop("hr_schedule_job", $job->id());
		}

		// set tasks that this presentation is a result to as prerequisites
		if (isset($prerequisite_tasks) and $prerequisite_tasks->count() > 0)
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

		$job->set_prop("resource", $resource->id());
		parent::save();
		$time = $this->prop("start1");

		if ($time < time())
		{ // an unscheduled presentation
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

	/** Marks the presentation started
	@attrib api=1 params=pos
	@param time type=int default=null
		Start time. Optional for asynchronous user feedback
	@returns void
	@errors
		throws awex_crm_presentation_state when presentation state doesn't allow starting
		throws awex_crm_presentation when presentation job is not defined
		throws awex_mrp_resource_unavailable when presentation job resource is occupied
	**/
	public function start($time = null)
	{
		if ($this->prop("real_start") > 2 or $this->is_done())
		{
			throw new awex_crm_presentation_state("Presentation has already started");
		}

		if (!is_oid($this->prop("hr_schedule_job")))
		{
			throw new awex_crm_presentation("Presentation job not defined");
		}

		$job = new object($this->prop("hr_schedule_job"));

		if (null === $time)
		{
			$time = time();
		}

		$this->awobj_set_real_start($time);

		// set job resource to person resource
		$application = automatweb::$request->get_application();
		if ($application->is_a(CL_CRM_SALES))
		{
			$company = $application->prop("owner");
		}
		else
		{
			$company = get_current_company();
		}

		$customer_relation = new object($this->prop("customer_relation"));
		$sales_person = obj($customer_relation->prop("salesman"), array(), CL_CRM_PERSON);
		$person_resource = mrp_workspace_obj::get_person_resource($company, $sales_person);
		$this->set_prop("real_maker", $sales_person->id());

		$old_resource = $job->prop("resource");
		$job->set_prop("resource", $person_resource->id());
		$job->save();

		//
		try
		{
			$job->start(null, $time);
		}
		catch (awex_mrp_case_state $e)
		{
			// !!! v6ibolla 'unscheduled' siis peaks midagi muud m6tlema sest see on normaalse wf osa ja exceptioni kaudu liiga kulukas
			try
			{
				// try to plan customer case and try start again
				$customer_case = $customer_relation->get_sales_case(false);
				$customer_case->plan();
				$job->start(null, $time);
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
			// !!! v6ibolla 'unscheduled' siis peaks midagi muud m6tlema sest see on normaalse wf osa ja exceptioni kaudu liiga kulukas
			try
			{
				// try to plan job and try start again
				$job->plan();
				$job->start(null, $time);
			}
			catch (Exception $e)
			{
				$job->set_prop("resource", $old_resource);
				$job->save();
				throw $e;
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

		parent::save();

		if ($application->is_a(CL_CRM_SALES))
		{
			// send presentation start message to sales application
			$application->make_presentation($this->ref());
		}
	}

	/** Ends the presentation
	@attrib api=1 params=pos
	@param time type=int default=0
		Ending time. Optional for asynchronous user feedback. Default means current time is used
	@returns void
	@errors
		throws awex_crm_presentation_state when presentation state doesn't allow ending
		throws awex_crm_presentation_results when result not defined or not suitable for ending
		throws awex_crm_presentation when presentation job is not defined
	**/
	public function end($time = 0)
	{
		if ($this->prop("real_duration") > 0)
		{
			throw new awex_crm_presentation_state("Presentation has already ended");
		}

		if ($this->prop("real_start") < 2)
		{
			throw new awex_crm_presentation_state("Presentation hasn't started");
		}

		if (!is_oid($this->prop("hr_schedule_job")))
		{
			throw new awex_crm_presentation("Presentation job not defined");
		}

		if (!in_array($this->prop("result"), self::$presentation_done_results))
		{
			throw new awex_crm_presentation_results("Invalid result '" . $this->prop("result") . "' for ending presentation.");
		}

		$job = new object($this->prop("hr_schedule_job"));

		if (0 === $time)
		{
			$time = time();
		}

		if ($time <= $this->prop("real_start"))
		{
			throw new awex_crm_presentation("End time ({$time}) must be greater than start time (".$this->prop("real_start").")");
		}

		$this->awobj_set_real_duration($time - $this->prop("real_start"));

		//
		$job->done(null, "", $this->prop("real_duration"));

		$application = automatweb::$request->get_application();
		if ($application->is_a(CL_CRM_SALES))
		{
			// send presentation end message to sales application and process presentation result
			$application->end_presentation($this->ref());
		}

		$this->set_meta("processed_result", $this->prop("result"));
		parent::save();
	}

	/** Cancels the presentation
	@attrib api=1 params=pos
	@returns void
	@errors
		throws awex_crm_presentation_results when presentation result not defined or doesn't allow canceling
		throws awex_crm_presentation when presentation job is not defined
	**/
	public function cancel()
	{
		if (!is_oid($this->prop("hr_schedule_job")))
		{
			throw new awex_crm_presentation("Presentation job not defined");
		}

		if (!$this->prop("result") or in_array($this->prop("result"), self::$presentation_done_results))
		{
			throw new awex_crm_presentation_results("Invalid result '" . $this->prop("result") . "' for canceling presentation.");
		}

		$job = new object($this->prop("hr_schedule_job"));
		$job->cancel();

		$this->awobj_set_real_start(0);
		$this->awobj_set_real_duration(0);

		$application = automatweb::$request->get_application();
		if ($application->is_a(CL_CRM_SALES))
		{
			// send presentation end message to sales application and process presentation result
			$application->cancel_presentation($this->ref());
		}

		$this->set_meta("processed_result", $this->prop("result"));
		parent::save();
	}

	public function save($check_state = false)
	{
		if (!$this->is_done())
		{
			// change job times according to changes in presentation times
			if (is_oid($this->prop("hr_schedule_job")))
			{
				try
				{
					$job = obj($this->prop("hr_schedule_job"), array(), CL_MRP_JOB);
					$planned_length = $this->prop("end") > $this->prop("start1") ? $this->prop("end") - $this->prop("start1") : 0;
					if ($this->prop("real_start") < 1 and ($this->prop("start1") != $job->prop("minstart") or $planned_length != $job->prop("planned_length")))
					{
						$job->set_prop("minstart", $this->prop("start1"));
						$job->set_prop("planned_length", $planned_length);
						$job->save();
					}
				}
				catch (awex_obj_na $e)
				{
				}
			}

			if ($result = $this->prop("result"))
			{
				if (in_array($result, self::$presentation_done_results) and ($this->prop("real_duration") < 1 or $this->prop("real_start") < 2))
				{
					throw new awex_crm_presentation_results("real_duration, real_start and result must all be defined when presentation took place.");
				}

				if (is_oid($this->prop("hr_schedule_job")))
				{
					try
					{
						// start and end job if presentation done
						if (in_array($result, self::$presentation_done_results))
						{
							try
							{
								if (!$this->is_saved())
								{
									$r = parent::save($check_state);
								}

								// prepare for simulation nulling temporal data
								$start_time = $this->prop("real_start");
								$end_time = $this->prop("real_start") + $this->prop("real_duration");
								$this->awobj_set_real_start(0);
								$this->awobj_set_real_duration(0);

								// simulate workflow
								$this->start($start_time);// voids exclusive save ///!!! fix
								$this->end($end_time);// voids exclusive save
							}
							catch (Exception $e)
							{
								throw $e;
							}
						}
						else
						{ // cancel presentation
							if (!$this->is_saved())
							{
								$r = parent::save($check_state);
							}

							$this->cancel();// voids exclusive save
						}
					}
					catch (awex_obj_na $e)
					{
					}
				}
			}
		}

		$r = parent::save($check_state);
		return $r;
	}

	/** Checks if presentation is done regardless if it occurred or was canceled
	@attrib api=1 params=pos
	@returns bool
	@comment
		Presentation is considered finished when its result is entered
	**/
	public function is_done()
	{
		return (bool) $this->meta("processed_result");
	}

	/** Checks if presentation is finished
	@attrib api=1 params=pos
	@returns bool
	@comment
		Presentation is considered done when it really occurred and data about that is entered
	**/
	public function is_finished()
	{
		$is_finished = false;

		try
		{
			$job = new object($this->prop("hr_schedule_job"));

			if (
				in_array($this->prop("result"), self::$presentation_done_results) and
				$job->is_finished() and
				$this->prop("real_start") > 1 and
				$this->prop("real_duration") > 1
			)
			{
				$is_finished = true;
			}
		}
		catch (Exception $e)
		{
		}

		return $is_finished;
	}
}

/** Generic presentation error **/
class awex_crm_presentation extends awex_crm {}

/** Presentation results are not defined as expected **/
class awex_crm_presentation_results extends awex_crm_presentation {}

/** Presentation state is not what expected **/
class awex_crm_presentation_state extends awex_crm_presentation {}

/** Presentation's real time or planned time errors **/
class awex_crm_presentation_time extends awex_crm_presentation {}

/** Salesman errors **/
class awex_crm_presentation_salesman extends awex_crm_presentation {}

?>
