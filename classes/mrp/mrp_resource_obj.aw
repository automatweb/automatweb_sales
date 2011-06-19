<?php

require_once "mrp_header.aw";

/* A manufacturing resource is a processing unit that converts 'input products' to 'output products' */

class mrp_resource_obj extends _int_object implements crm_sales_price_component_interface, crm_offer_row_interface
{
	const CLID = 825;

	const STATE_AVAILABLE = 10;
	const STATE_RESERVED = 14;
	const STATE_PROCESSING = 11;
	const STATE_OUTOFSERVICE = 12;
	const STATE_INACTIVE = 13;

	const TYPE_SCHEDULABLE = 1;
	const TYPE_NOT_SCHEDULABLE = 2;
	const TYPE_SUBCONTRACTOR = 3;

	protected $threads = array();
	protected $workspace;

	protected static $inactive_states = array(
		self::STATE_INACTIVE,
		self::STATE_OUTOFSERVICE
	);

	private $thread_index = array(); // thread_job_id => treads_array_key

/** Class constructor
	@attrib api=1 params=pos
**/
	public function __construct($objdata)
	{
		parent::__construct($objdata);

		$new = (null === $this->id());
		if ($new)
		{
			### set status
			$this->set_prop ("state", self::STATE_AVAILABLE);
			$this->set_prop ("production_feedback_option_values", array(1));
		}
	}

	//	Written solely for testing purposes!
	public function get_units()
	{
		$ol = new object_list(array(
			"class_id" => CL_UNIT,
			"status" => object::STAT_ACTIVE,
		));
		return $ol;
	}

	public function get_type_options()
	{
		return array (
			self::TYPE_SCHEDULABLE => t("Ressursi kasutust planeeritakse"),
			self::TYPE_NOT_SCHEDULABLE => t("Ressursi kasutust ei planeerita"),
			self::TYPE_SUBCONTRACTOR => t("Ressurss on allhange")
		);
	}

	public function awobj_set_thread_data($max_threads)
	{ //!!! requires exclusive load
		settype($max_threads, "int");
		if ($max_threads < 0)
		{
			throw new awex_obj_type("Can't be negative number of threads");
		}

		$this->load_threads();
		$thread_count = count($this->threads);
		if ($thread_count > $max_threads)
		{ // lose threads
			$no_of_threads_to_delete = $thread_count - $max_threads;
			// make sure that jobs will be stopped on deleted threads
			foreach ($this->threads as $key => $thread)
			{ // remove as many available threads as possible and needed
				if ($no_of_threads_to_delete and $thread->is_available())
				{
					$this->remove_thread($key);
					--$no_of_threads_to_delete;

					if (!$no_of_threads_to_delete)
					{
						break;
					}
				}
			}

			if ($no_of_threads_to_delete)
			{ // iremove among threads that are in use
				foreach ($this->threads as $key => $thread)
				{
					if ($no_of_threads_to_delete)
					{
						$this->remove_thread($key);
						--$no_of_threads_to_delete;
					}
					else
					{
						break;
					}
				}
			}
		}
		elseif ($thread_count < $max_threads)
		{ // add threads
			$no_of_threads_to_add = $max_threads - $thread_count;
			while ($no_of_threads_to_add--)
			{
				$this->add_thread();
			}
		}

		$r = parent::set_prop("thread_data", $this->threads);
		$workspace = $this->awobj_get_workspace();
		$workspace->request_rescheduling();
		return $r;
	}

	private function add_thread()
	{
		$this->threads[] = new mrp_resource_thread();
		$this->set_prop ("state", self::STATE_AVAILABLE);
	}

	private function remove_thread($id)
	{
		$thread = $this->threads[$id];

		if ($thread->is_available())
		{
			$thread->delete();
			unset($this->threads[$id]);
		}
		else
		{
			$thread->delete();
		}
	}

	public function awobj_get_thread_data()
	{
		$this->load_threads();
		return count($this->threads);
	}

/**
	@attrib params=pos api=1
	@returns CL_MRP_WORKSPACE
	@errors
		throws awex_mrp_resource_workspace when workspace couldn't be loaded
**/
	public function awobj_get_workspace()
	{
		if (!$this->workspace)
		{
			$E = false;
			try
			{
				$workspace = new object(parent::prop("workspace"));
				if (!$workspace->is_a(CL_MRP_WORKSPACE))
				{
					if(is_oid($this->id()))
					{
						// try backward compatibility
						$workspace = $this->get_first_obj_by_reltype("RELTYPE_MRP_OWNER");
					}
					// NEW
					else
					{
						$request = aw_request::autoload();
						$workspace = obj($request->arg("mrp_workspace"));
					}

					if ($workspace instanceof object and CL_MRP_WORKSPACE == $workspace->class_id())
					{ // save new format
						$this->awobj_set_workspace($workspace);
						$this->save();
						$wc = $this->connections_from(array("type" => "RELTYPE_MRP_OWNER"));
						foreach ($wc as $c)
						{
							$c->delete();
						}
					}
					else
					{
						throw new awex_mrp_case_workspace("Workspace not defined");
					}
				}
			}
			catch (awex_mrp_case_workspace $e)
			{
				throw $e;
			}
			catch (Exception $E)
			{
			}

			if ($E)
			{
				$e = new awex_mrp_case_workspace("Workspace not defined");
				$e->set_forwarded_exception($E);
				throw $e;
			}
			$this->workspace = $workspace;
		}
		return $this->workspace;
	}

	/**
		@attrib api=1
	**/
	public function get_all_covers_for_resource()
	{
		$ol = new object_list(array(
			"class_id" => CL_MRP_ORDER_COVER,
			"lang_id" => array(),
			"site_id" => array(),
			"status" => object::STAT_ACTIVE,
			"CL_MRP_ORDER_COVER.RELTYPE_APPLIES_RESOURCE" => $this->id()
		));
		return $ol->arr();
	}

/**
	@attrib params=pos api=1
	@param workspace type=CL_MRP_WORKSPACE
	@returns starndard object set_prop return
	@errors
		throws awex_obj_type when workspace parameter is not a workspace object
**/
	public function awobj_set_workspace(object $workspace)
	{
		if (!is_object($workspace) || !$workspace->is_a(CL_MRP_WORKSPACE))
		{
			throw new awex_obj_type("Workspace not a mrp_workspace object");
		}

		$this->workspace = $workspace;
		return parent::set_prop("workspace", $workspace->id());
	}

	public function awobj_set_global_buffer($value)
	{
		$r = parent::set_prop("global_buffer", $value);
		$workspace = $this->awobj_get_workspace();
		$workspace->request_rescheduling();
		return $r;
	}

	/**
	@attrib api=1 params=pos
	@param value required type=array
	@returns array numeric
		Output product count options
	@errors
		throws awex_obj_type when parameter is not array
	**/
	public function awobj_set_production_feedback_option_values($value)
	{
		if(!is_oid($this->id()))	// NEW
		{
			$value = array(1);
		}

		if (!is_array($value))
		{
			throw new awex_obj_type("Array required.");
		}

		$quantities = array();
		foreach ($value as $quantity)
		{
			$quantities[] = (int) $quantity; ///!!! unit-i j2rgi, v6ibolla float kuskil hoopis
		}

		return $this->set_prop("production_feedback_option_values", $value);
	}

	public function awobj_set_state($value)
	{
		throw new awex_obj_readonly("State is a read-only property");
	}

	public function awobj_get_state()
	{
		$state = parent::prop("state");
		if (!in_array($state, self::$inactive_states))
		{
			if ($this->is_available())
			{
				$state = self::STATE_AVAILABLE;
			}
			else
			{
				$state = self::STATE_PROCESSING;
			}
		}
		return $state;
	}

	public function awobj_get_production_feedback_option_values()
	{
		return (array) $this->prop("production_feedback_option_values");
	}

	/**
		@attrib name=get_materials params=name

		@param id required type=int

		@param odl optional type=bool default=false

		@returns object_list/object_data_list of materials (shop_products)

	**/
	public static function get_materials($arr)
	{
		$prms = array(
			"class_id" => CL_SHOP_PRODUCT,
			"lang_id" => array(),
			"site_id" => array(),
			"RELTYPE_PRODUCT(CL_MATERIAL_EXPENSE_CONDITION).resource" => $arr["id"],
		);
		if(empty($arr["odl"]))
		{
			return new object_list($prms);
		}
		else
		{
			return new object_data_list(
				$prms,
				array(
					CL_SHOP_PRODUCT => array("name")
				)
			);
		}
	}

	/**
	@attrib api=1 params=pos
	@returns array of material_expense_condition objects
		Objects that refer to products that can be used as input materials on this resource. Object id-s as index.
	**/
	public function get_possible_materials()
	{
		$ol = new object_list(array(
			"class_id" => CL_MATERIAL_EXPENSE_CONDITION,
			"lang_id" => array(),
			"site_id" => array(),
			"resource" => $this->id()
		));
		return $ol->arr();
	}

	/** Adds a product to input materials of this resource
	@attrib api=1 params=pos
	@param product required type=CL_SHOP_PRODUCT
	@returns void
	@errors
		throws awex_obj_type when $product is not a CL_SHOP_PRODUCT object (exception variable $argument_name contains faulty parameter name)
	**/
	public function add_input_product(object $product)
	{
		if (CL_SHOP_PRODUCT != $product->class_id())
		{
			$e = new awex_obj_type("Wrong type product object.");
			$e->argument_name = "product";
			throw $e;
		}

		$o = obj();
		$o->set_class_id(CL_MATERIAL_EXPENSE_CONDITION);
		$o->set_parent($this->id());
		$o->set_name(sprintf(t("%s kulutingimus %s jaoks"), $product->name(), $this->name()));
		$o->set_prop("resource", $this->id());
		$o->set_prop("product", $product->id());
		$o->save();
	}

	/** Removes a product from input materials of this resource
	@attrib api=1 params=pos
	@param product required type=CL_SHOP_PRODUCT
	@returns void
	@errors
		throws awex_obj_type when $product is not a CL_SHOP_PRODUCT object (exception variable $argument_name contains faulty parameter name)
	**/
	public function remove_input_product(object $product)
	{
		if (CL_SHOP_PRODUCT != $product->class_id())
		{
			$e = new awex_obj_type("Wrong type product object.");
			$e->argument_name = "product";
			throw $e;
		}

		$ol = new object_list(array(
			"class_id" => CL_MATERIAL_EXPENSE_CONDITION,
			"lang_id" => array(),
			"site_id" => array(),
			"resource" => $this->id(),
			"product" => $product->id()
		));
		foreach ($ol->arr() as $o)
		{
			$o->delete();
		}
	}

	/** Calculates and returns fixed unavailable periods effective between points in time specified by $start and $end
	@attrib api=1 params=pos
	@param start required type=int UNIX timestamp
	@param end required type=int UNIX timestamp
	@returns array
		array (
			period1_start_int_unixtimestamp => period1_end_int_unixtimestamp,
			...
		)
	**/
	public function get_unavailable_periods ($start, $end)
	{
// /* dbg */ if ($resource->id () == 6670  ) {
// /* dbg */ $this->mrpdbg=1;
// /* dbg */ }

		$period_start = $start;
		$period_end = $end;
		$unavailable_dates = array ();
		$dates = $this->prop ("unavailable_dates");
		$dates = explode (";", $dates);
		$separators = " ,.:/|-\\";
		$period_start_year = date ("Y", $period_start);
		foreach ($dates as $date)
		{
			$start_day = (int) strtok ($date, $separators);
			$start_mon = (int) strtok ($separators);
			$start_hour = (int) strtok ($separators);
			$start_min = (int) strtok ($separators);
			$end_day = (int) strtok ($separators);
			$end_mon = (int) strtok ($separators);
			$end_hour = (int) strtok ($separators);
			$end_min = (int) strtok ($separators);
			$in_period_range = true;
			$year = $period_start_year;

			while ($in_period_range)
			{
				$start = mktime ($start_hour, $start_min, 0, $start_mon, $start_day, $year);
				$end = mktime ($end_hour, $end_min, 0, $end_mon, $end_day, $year);

				if ($start < $period_end)
				{
					if ($start < $end)
					{
						$unavailable_dates[$start] = isset($unavailable_dates[$start]) ? max($end, $unavailable_dates[$start]) : $end;
					}
				}
				else
				{
					$in_period_range = false;
				}

				$year++;
			}
		}

		foreach ($unavailable_dates as $start => $end)
		{
			if ($end <= $period_start)
			{
				unset ($unavailable_dates[$start]);
			}
		}

// /* dbg */ if ($this->mrpdbg){
// /* dbg */ echo "unavailable_dates:";
// /* dbg */ arr ($unavailable_dates);
// /* dbg */ }

		return $unavailable_dates;
	}

	/** Calculates and returns recurrent unavailable periods effective between points in time specified by $start and $end
	@attrib api=1 params=pos
	@param start required type=int UNIX timestamp
	@param end required type=int UNIX timestamp
	@returns array
		Array of recurrent period (RP) definitions in associative array format:
		array (
			"length" => $length, // int seconds, length of RP
			"start" => $start, // int UNIX timestamp start date of first RP by this definition
			"time" => $time, // int seconds start time of first RP by this definition. Actual RP start is therefore $start+$time
			"end" => $end, // int UNIX timestamp End time of this RP's effectiveness
			"max_span" => $end + $length,
			"interval" => $interval // int seconds Interval between RP-s -- the second period by this definition starts at $start+$time+$interval
		)
	**/
	public function get_recurrent_unavailable_periods($start, $end)
	{
// /* dbg */ if ($this->id () == 6670  ) {
// /* dbg */ $this->mrpdbg=1;
// /* dbg */ }

		### unavailable recurrences
		$recurrent_unavailable_periods = array ();
		$start = mktime (0, 0, 0, date ("m", $start), date ("d", $start), date("Y", $start));
		$end = mktime (0, 0, 0, date ("m", $end), date ("d", $end), date("Y", $end));

		if ($this->prop ("unavailable_weekends"))
		{
			classload("core/date/date_calc");
			$weekend_start = get_week_start($start) + (5 * 86400);
			$weekend_length = 172800;
			$recurrent_unavailable_periods[] = array (
				"length" => $weekend_length,
				"start" => $weekend_start,
				"time" => 0,
				"end" => $end,
				"max_span" => $end + $weekend_length,
				"interval" => 604800,
			);
		}

		foreach ($this->connections_from (array ("type" => "RELTYPE_RECUR")) as $connection)
		{
			$recurrence = $connection->to ();

			if ( !(($recurrence->prop ("start") > $end) or ($recurrence->prop ("end") < $start)) )
			{
				switch ($recurrence->prop ("recur_type"))
				{
					case recurrence::RECUR_DAILY: //day
						$interval = $recurrence->prop ("interval_daily");
						$interval = round (($interval ? $interval : 1) * 86400);
						break;

					case recurrence::RECUR_WEEKLY: //week
						$interval = $recurrence->prop ("interval_weekly");
						$interval = round (($interval ? $interval : 1) * 86400 * 7);
						break;

					case recurrence::RECUR_YEARLY: //year
						$interval = $recurrence->prop ("interval_yearly");
						$interval = round (($interval ? $interval : 1) * 86400 * 365);
						break;

					default:
						continue;
				}

				$recurrence_starttime = $recurrence->prop ("time");
				$recurrence_starttime = explode (":", $recurrence_starttime);
				$recurrence_starttime_hours = $recurrence_starttime[0] ? (int) $recurrence_starttime[0] : 0;
				$recurrence_starttime_minutes = $recurrence_starttime[1] ? (int) $recurrence_starttime[1] : 0;
				$recurrence_starttime = $recurrence_starttime_hours * 3600 + $recurrence_starttime_minutes * 60;

				$length = round (aw_math_calc::string2float($recurrence->prop ("length")) * 3600);
				$time = $recurrence_starttime;

				$recurrent_unavailable_periods[] = array (
					"length" => $length,
					"start" => $recurrence->prop ("start"),
					"time" => $time,
					"end" => $recurrence->prop ("end"),
					"max_span" => $recurrence->prop ("end") + $time + $length,
					"interval" => $interval,
				);
			}
		}


		### add workhours (available recurrences)
		$recurrent_available_periods = array ();

		foreach ($this->connections_from (array ("type" => "RELTYPE_RECUR_WRK")) as $connection)
		{
			$recurrence = $connection->to ();

			if ( !(($recurrence->prop ("start") > $end) or ($recurrence->prop ("end") < $start)) )
			{
				$interval = 86400;
				list ($recurrence_time_hours, $recurrence_time_minutes) = explode (":", $recurrence->prop ("time"), 2);
				$recurrence_time = abs ((int) $recurrence_time_hours) * 3600 + abs ((int) $recurrence_time_minutes) * 60;
				$recurrence_length = round (aw_math_calc::string2float($recurrence->prop ("length")) * 3600);

// /* dbg */ if ($this->mrpdbg){
// /* dbg */ echo "recurrent_available_period time:" . $recurrence_time . "<br>";
// /* dbg */ }

				$recurrent_available_periods[] = array (
					"length" => $recurrence_length,
					"start" => $recurrence->prop ("start"),
					"time" => $recurrence_time,
					"end" => $recurrence->prop ("end"),
					"interval" => $interval,
				);
			}
		}

// /* dbg */ if ($this->mrpdbg){
// /* dbg */ echo "recurrent_available_periods:";
// /* dbg */ arr ($recurrent_available_periods);
// /* dbg */ }

		### transmute recurrently available periods to unavailables
		### throw away erroneous definitions
		foreach ($recurrent_available_periods as $key => $available_period)
		{
			if ( ($available_period["start"] >= $available_period["end"]) or ($available_period["length"] > 86400) or ($available_period["length"] < 1) )
			{
				unset ($recurrent_available_periods[$key]);
			}
		}

// /* dbg */ if ($this->mrpdbg){
// /* dbg */ echo "recurrent_available_periods after errorcheck:";
// /* dbg */ arr ($recurrent_available_periods);
// /* dbg */ exit;
// /* dbg */ }

		### find combinations of available periods
		$combination_breakpoints = array ($start, $end);

		foreach ($recurrent_available_periods as $available_period)
		{
			if (($available_period["start"] > $start) and ($available_period["start"] < $end))
			{
				$combination_breakpoints[] = $available_period["start"];
			}

			if (($available_period["end"] > $start) and ($available_period["end"] < $end))
			{
				$combination_breakpoints[] = $available_period["end"];
			}
		}

		### make unavailable recurrence definitions according to these combinations
		usort ($recurrent_available_periods, array ($this, "sort_recurrences_by_start"));
		sort ($combination_breakpoints, SORT_NUMERIC);
		$interval = 86400;

		foreach ($combination_breakpoints as $bp_key => $breakpoint)
		{
			if (isset ($combination_breakpoints[$bp_key + 1]))
			{
				$combination_start = $breakpoint;
				$combination_end = $combination_breakpoints[$bp_key + 1];
				$combination = array ();

				foreach ($recurrent_available_periods as $available_period)
				{
					if ( ($available_period["start"] <= $combination_start) and ($available_period["end"] >= $combination_end) )
					{
						$combination[] = $available_period;
					}
				}

				usort ($combination, array ($this, "sort_recurrences_by_time"));

				foreach ($combination as $key => $available_period)
				{
					$time = ($available_period["time"] + $available_period["length"]) % $interval;

					if (isset ($combination[$key + 1]))
					{
						$end_time = $combination[$key + 1]["time"];
					}
					else
					{
						$end_time = $combination[0]["time"];
					}

					if ($end_time > $time)
					{
						$length = $end_time - $time;
					}
					else
					{
						$length = $end_time + ($interval - $time);
					}

					$recurrent_unavailable_periods[] = array (
						"length" => $length,
						"start" => $combination_start,
						"end" => $combination_end,
						"time" => $time,
						"max_span" => $recurrence->prop ("end") + $time + $length,
						"interval" => $interval,
					);
				}
			}
		}

// /* dbg */ if ($this->mrpdbg){
// /* dbg */ echo "return recurrent_unavailable_periods:";
// /* dbg */ foreach ($recurrent_unavailable_periods as $key => $value){ echo "<hr>";
// /* dbg */ echo "length: " . ($value["length"]/3600).  "h<br>";
// /* dbg */ echo "start: " . date (MRP_DATE_FORMAT, $value["start"]).  "<br>";
// /* dbg */ echo "end: " . date (MRP_DATE_FORMAT, $value["end"]).  "<br>";
// /* dbg */ echo "time: " . date ("H.i", mktime(0,0,0,1,1,2005) + $value["time"]).  "<br>";
// /* dbg */ echo "interval: " . ($value["interval"]/3600).  "h<br>";}
// /* dbg */ }

		return $recurrent_unavailable_periods;
	}

/** Starts to process job on this resource. Resource must be reserved in advance
    @attrib api=1 params=pos
	@param job required type=CL_MRP_JOB
	@returns void
	@errors
		throws awex_obj_type when $job parameter is not CL_MRP_JOB
		throws awex_mrp_resource_job when given job couldn't be processed
		throws awex_redundant_instruction when given job already being processed
		throws awex_mrp_resource_unavailable
		throws awex_mrp_resource on any other error
**/
// Future development idea:
// @comment
// Multiple threads can process the same job simultaneously if job allows parallel processing
	function start_job (object $job)
	{
		if (!$job->is_a(CL_MRP_JOB))
		{
			throw new awex_obj_type("Wrong type job object (class id is ". $job->class_id() .")");
		}

		if ($this->is_processing($job))
		{
			throw new awex_redundant_instruction("Job (" . $job->id() . ") is already being processed by this resource (" . $this->id() . ")");
		}

		try
		{
			$processing = false;
			$this->ref()->lock(aw_locker::LOCK_WRITE);
			$this->load_threads();
			$processed_jobs = array();
			$job_error = $state_error = $thread_error = $index_error = false;

			if (isset($this->thread_index[$job->id()]) and isset($this->threads[$this->thread_index[$job->id()]]))
			{
				$thread = $this->threads[$this->thread_index[$job->id()]];

				try
				{
					$thread->process($job);
					$processing = true;
				}
				catch (awex_redundant_instruction $e) // probably pointless double checking
				{
					throw $e;
				}
				catch (awex_mrp_resource_state $e)
				{
					// index error. a thread corresponds to job in index but refuses to process it
					// an index error or job was reserved on this thread and thread deleted meanwhile
					// if deleted then propagate error, else try to correct index
					$thread_error = true;
					$index_error = true;
				}
				catch (awex_mrp_resource_job $e)
				{
					// index error. a thread corresponds to job in index but refuses to process it
					// thread is reported to be reserved for this job but is actually free
					$job_error = true;
					$index_error = true;
				}
				catch (awex_mrp_resource_unavailable $e)
				{
					// index error. a thread corresponds to job in index but refuses to process it
					// thread is reported to be reserved for this job but is actually processing or reserved for another
					$processed_jobs += $e->processed_jobs;
					$job_error = true;
					$index_error = true;
				}
				catch (awex_mrp_resource $e)
				{
					// index error. a thread corresponds to job in index but refuses to process it
					// job is correct but state isn't
					$state_error = true;
					$index_error = true;
				}
			}
			else
			{
				// job not found in thread index. might be an unreserved job or index malfunction
				// check each thread
				foreach ($this->threads as $key => $thread)
				{
					try
					{
						$thread->process($job);

						// if process command succeeded then a simple index error was found. a thread was reserved but job wasin't indexed
						$processing = true;
						$this->thread_index[$job->id()] = $key;
						break;
					}
					catch (awex_redundant_instruction $e)
					{
						// found an index error and fixing it. job wasn't in index but was found being processed by a thread
						$this->thread_index[$job->id()] = $key;
						$this->save ();
						$this->ref()->unlock();
						throw $e;
					}
					catch (awex_mrp_resource_job $e)
					{
						// thread is free and not reserved to this job
					}
					catch (awex_mrp_resource_unavailable $e)
					{
						// thread just processes some other job
						$processed_jobs += $e->processed_jobs;
					}
					catch (awex_mrp_resource_state $e)
					{
						// an index error or job was reserved on this thread and thread deleted meanwhile
						// if deleted then propagate error, else try to correct index
						$thread_error = true;
					}
					catch (awex_mrp_resource $e)
					{
						// index error. a thread corresponds to job in index but refuses to process it
						// job is correct but state isn't
						$state_error = true;
					}
				}
			}

			if ($processing)
			{
				$this->save ();
				$this->ref()->unlock();
			}
			else
			{
				if ($thread_error)
				{
					//!!! do
				}

				if ($job_error)
				{
					//!!! do
				}

				if ($state_error)
				{
					//!!! do
				}

				if (!$processing)
				{
					throw new aw_exception("unknown error");
				}
			}
		}
		catch (awex_redundant_instruction $e)
		{
			$this->ref()->unlock();
			throw $e;
		}
		catch (awex_mrp_resource_unavailable $e)
		{
			$this->ref()->unlock();
			throw $e;
		}
		catch (Exception $E)
		{
			$this->ref()->unlock();
			$e = new awex_mrp_resource("Unknown error on resource " . $this->id() . " trying to start job " . $job->id());
			$e->set_forwarded_exception($E);
			throw $e;
		}
	}

/** Stops job on this resource
    @attrib api=1 params=pos
	@param job required type=CL_MRP_JOB
	@errors
		throws awex_obj_type when $job parameter is not CL_MRP_JOB
		throws awex_mrp_resource_job when given job not being processed
		throws awex_mrp_resource on any other error
**/
	function stop_job (object $job)
	{
		if (!$job->is_a(CL_MRP_JOB))
		{
			throw new awex_obj_type("Job (" . $job->id() . ") of wrong type (" . $job->class_id() . "). Tried to stop on '" . $this->id() . "'");
		}

		try
		{
			$this->ref()->lock(aw_locker::LOCK_WRITE);
			$this->load_threads();
			$finish = false;

			if (isset($this->thread_index[$job->id()]) and isset($this->threads[$this->thread_index[$job->id()]]))
			{
				$thread = $this->threads[$this->thread_index[$job->id()]];
				if ($thread->is_processing($job))
				{
					try
					{
						$thread->finish($job);
						$finish = true;
					}
					catch (awex_mrp_resource_job $e) // probably pointless double checking
					{
						// job was reported processed by thread but actually isn't
						// $finish stays false, do integrity repairs etc. later with other operations
					}

					// clear index in any case
					unset($this->thread_index[$job->id()]);
				}
			}

			if (!$finish)
			{
				// job wasn't found in thread index.
				// check thread by thread if any is processing the job to be stopped
				foreach ($this->threads as $thread)
				{
					if ($thread->is_processing($job))
					{
						try
						{
							$thread->finish($job);
						}
						catch (awex_mrp_resource_job $e) // probably pointless double checking
						{
							// job was reported processed by thread but actually isn't
							//!!! repair resource integrity?
						}
					}
				}
			}

			$this->save ();
			$this->ref()->unlock();
		}
		catch (awex_mrp_resource_job $e)
		{
			throw $e;
		}
		catch (Exception $E)
		{
			$this->ref()->unlock();
			$e = new awex_mrp_resource("Unknown error. job (" . $job->id() . "), resource (" . $this->id() . ")");
			$e->set_forwarded_exception($E);
			throw $e;
		}
	}

/** Reserves the resource for given job.
    @attrib api=1 params=pos
	@param job type=CL_MRP_JOB
	@returns void
	@errors
		throws awex_obj_type when $job parameter is not CL_MRP_JOB
		throws awex_mrp_resource_unavailable
		throws awex_mrp_resource on any other error
**/
	public function reserve(object $job)
	{
		if (!$job->is_a(CL_MRP_JOB))
		{
			throw new awex_obj_type("Job (" . $job->id() . ") of wrong type (" . $job->class_id() . "). Tried to reserve on '" . $this->id() . "'");
		}

		$this->ref()->lock(aw_locker::LOCK_WRITE);
		$this->load_threads();

		try
		{
			foreach ($this->threads as $key => $thread)
			{
				if ($thread->is_available())
				{
					$thread->reserve($job);
					$this->thread_index[$job->id()] = $key;
					$this->save();
					$this->ref()->unlock();
					return;
				}
			}
		}
		catch (Exception $E)
		{
			$e = new awex_mrp_resource("Unknown error");
			$e->set_forwarded_exception($E);
			$this->ref()->unlock();
			throw $e;
		}

		$this->ref()->unlock();
		$e = new awex_mrp_resource_unavailable("Resource unavailable. Processing: " . implode(", ", array_keys($this->thread_index)));
		$e->processed_jobs = array_keys($this->thread_index);
		throw $e;
	}

/** Cancel reservation for given job.
    @attrib api=1 params=pos
	@param job type=CL_MRP_JOB
	@returns bool
	@errors
		throws awex_obj_type when $job parameter is not CL_MRP_JOB
**/
	public function cancel_reservation(object $job)
	{
		if (!$job->is_a(CL_MRP_JOB))
		{
			throw new awex_obj_type("Job (" . $job->id() . ") of wrong type (" . $job->class_id() . "). Tried to cancel on '" . $this->id() . "'");
		}

		try
		{
			$this->ref()->lock(aw_locker::LOCK_WRITE);
			$this->load_threads();

			if (isset($this->thread_index[$job->id()]) and isset($this->threads[$this->thread_index[$job->id()]]))
			{ // look in index first
				$this->threads[$this->thread_index[$job->id()]]->cancel_reservation($job);
				unset($this->thread_index[$job->id()]);
			}
			else
			{ // index error, try to cancel reservation on every thread
				foreach ($this->threads as $thread)
				{
					$thread->cancel_reservation($job);
				}
			}

			$this->save();
			$this->ref()->unlock();
		}
		catch (Exception $e)
		{
			$this->ref()->unlock();
			throw $e;
		}
	}

/** Tells if resource is processing given job.
    @attrib api=1 params=pos
	@param job type=CL_MRP_JOB
	@returns bool
	@errors
		throws awex_obj_type when $job parameter is not CL_MRP_JOB
**/
	public function is_processing(object $job)
	{
		if (!$job->is_a(CL_MRP_JOB))
		{
			throw new awex_obj_type("Job (" . $job->id() . ") of wrong type (" . $job->class_id() . "). Tried to check if processing on '" . $this->id() . "'");
		}

		$this->load_threads();
		$is = false;

		// try thread index
		if (isset($this->thread_index[$job->id()]) and isset($this->threads[$this->thread_index[$job->id()]]) and $this->threads[$this->thread_index[$job->id()]]->is_processing($job))
		{
			$is = true;
		}
		else
		{	// try iteration
			foreach ($this->threads as $key => $thread)
			{
				if ($thread->is_processing($job))
				{
					$is = true;
					// mend thread index
					$this->thread_index[$job->id()] = $key;
					break;
				}
			}
		}

		return $is;
	}

/** Tells if resource is available.
    @attrib api=1 params=pos
	@returns int
		Number of available threads
	@errors
		throws awex_mrp_resource on any error
**/
	public function is_available()
	{
		$is = 0;
		if (!in_array(parent::prop("state"), self::$inactive_states))
		{
			$this->load_threads();
			foreach ($this->threads as $thread)
			{
				if ($thread->is_available())
				{
					++$is;
				}
			}
		}
		return $is;
	}

/**
@attrib api=1 params=pos
@comment
	Standard _int_object parameters
@errors
	throws awex_mrp_case_workspace when workspace not found
**/
	public function save($exclusive = false, $previous_state = null)
	{
		$this->load_threads();
		$new = (null === $this->id());
		if ($new)
		{
			$workspace = $this->awobj_get_workspace();
			$resources_folder = $workspace->prop ("resources_folder");
			$this->set_parent ($resources_folder);
		}

		foreach ($this->threads as $key => $thread)
		{
			if ($thread->deleted() and $thread->is_available())
			{
				$job_id = $thread->get_job_id();
				unset($this->thread_index[$job_id]);
				unset($this->threads[$key]);
			}
		}

		$this->set_prop("thread_data", $this->threads);
		parent::set_prop("state", $this->awobj_get_state());
		$r = parent::save($exclusive, $previous_state);
		return $r;
	}

	protected static function sort_recurrences_by_start ($recurrence1, $recurrence2)
	{
		if ($recurrence1["start"] > $recurrence2["start"])
		{
			$result = 1;
		}
		elseif ($recurrence1["start"] < $recurrence2["start"])
		{
			$result = -1;
		}
		else
		{
			$result = 0;
		}

		return $result;
	}

	protected static function sort_recurrences_by_time ($recurrence1, $recurrence2)
	{
		if ($recurrence1["time"] > $recurrence2["time"])
		{
			$result = 1;
		}
		elseif ($recurrence1["time"] < $recurrence2["time"])
		{
			$result = -1;
		}
		else
		{
			$result = 0;
		}

		return $result;
	}

	protected function load_threads()
	{
		if (empty($this->threads))
		{
			$this->threads = $this->prop("thread_data");
			if (empty($this->threads))
			{ // new object probably
				$this->threads = array(new mrp_resource_thread());
			}
			elseif (is_array(reset($this->threads)))
			{ // convert old format thread data
				$old_thread_data = $this->threads;
				$this->threads = array();
				foreach ($old_thread_data as $old_thread)
				{
					$thread = new mrp_resource_thread();
					if (!empty($old_thread["job"]))
					{ // job in work
						try
						{
							$job = obj($old_thread["job"], array(), CL_MRP_JOB, false);
							$thread->process($job);
							$this->thread_index[$job->id()] = count($this->threads);
						}
						catch (Exception $e)
						{
							if ($job)
							{
								unset($this->thread_index[$job->id()]);
							}
						}
					}
					$this->threads[] = $thread;
				}

				if (count($this->thread_index) === count($this->threads))
				{ // a job is in work, set global state
					$this->set_prop("state", self::STATE_PROCESSING);
				}
			}

			// index threads by their job
			foreach ($this->threads as $key => $thread)
			{
				if ($thread->deleted())
				{
					unset($this->threads[$key]);
				}
				else
				{
					$this->thread_index[$thread->get_job_id()] = $key;
				}
			}
		}
	}

	/**
		@attrib name=get_available_hours
		@param from optional type=int default=0
		@param to optional type=int

	**/
	public function get_available_hours($arr = array())
	{
		$from = isset($arr["from"]) ? (int)$arr["from"] : 0;
		$to = isset($arr["to"]) ? (int)$arr["to"] : time();
		$span = $to - $from;

		$ups = $this->get_unavailable_periods($from, $to);
		foreach($ups as $up_s => $up_f)
		{
			$span -= $up_f - $up_s;
		}

		$rups = $this->get_recurrent_unavailable_periods($from, $to);
		foreach($rups as $rup)
		{
			for($i = $rup["start"] + $rup["time"]; $i < $rup["end"]; $i += $rup["interval"])
			{
				$u = $i + $rup["length"] > $rup["end"] ? $rup["end"] - $i : $rup["length"];
				$span -= max(0, $u);
			}
		}

		return $span;
	}

	/**
		@attrib name=get_planned_hours api=1 params=name

		@param from optional type=int default=0

		@param to optional type=int default=time()

		@param id optional type=int/array
			If not set the OID of current object will be used.

		@returns Array of planned hours by resource if parameter id is array, planned hours as int otherwise.

	**/
	public static function get_planned_hours($arr)
	{
		$arr["id"] = !empty($arr["id"]) ? $arr["id"] : $this->id();
		$resource_ids = implode(",", (array)$arr["id"]);
		$from = isset($arr["from"]) ? (int)$arr["from"] : 0;
		$to = isset($arr["to"]) ? (int)$arr["to"] : time();
		$status = implode(",", array(object::STAT_ACTIVE, object::STAT_NOTACTIVE));
		$span = $to - $from;

		$rows = get_instance("mrp_job")->db_fetch_array("
			SELECT
				m.resource as resource_id, SUM(LEAST(m.length, $to - s.starttime, s.starttime + m.length - $from, $span)) as p
			FROM
				objects o
				LEFT JOIN mrp_job m ON o.brother_of = m.oid
				LEFT JOIN mrp_schedule s ON o.brother_of = s.oid
			WHERE
				s.starttime < $to
				AND s.starttime + m.length > $from
				AND o.status IN ({$status})
				AND m.resource IN ({$resource_ids})
			GROUP BY m.resource
		");

		// Initialize
		$p = array();
		foreach((array)$arr["id"] as $resource_id)
		{
			$p[$resource_ids] = 0;
		}

		foreach($rows as $row)
		{
			$p[$row["resource_id"]] = $row["p"];
		}

		return is_array($arr["id"]) ? $p : reset($p);
	}

	/**
		@attrib name=get_operators api=1
	**/
	public function get_operators()
	{
		$odl = new object_data_list(
			array(
				"class_id" => CL_MRP_RESOURCE_OPERATOR,
				new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						"resource" => $this->id(),
						"all_resources" => 1,
					),
				))
			),
			array(
				CL_MRP_RESOURCE_OPERATOR => array("unit", "profession")
			)
		);
		$secs = $odl->get_element_from_all("unit");
		$pros = $odl->get_element_from_all("profession");
		$odl = new object_data_list(
			array(
				"class_id" => CL_MRP_RESOURCE_OPERATOR,
				"unit" => $secs,
				"all_section_resources" => 1,
			),
			array(
				CL_MRP_RESOURCE_OPERATOR => array("profession")
			)
		);
		$pros = array_merge($pros, $odl->get_element_from_all("profession"));

		$ol = new object_list(array(
			"class_id" => CL_CRM_PERSON,
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_CRM_PERSON.RELTYPE_RANK" => $pros,
					"CL_CRM_PERSON.RELTYPE_CURRENT_JOB.RELTYPE_PROFESSION" => $pros,
				),
			)),
			"lang_id" => array(),
			"site_id" => array(),
		));
		return $ol;
	}

	public function get_hours_per_format_amount($format, $amount)
	{
		// find active ability, get from that
		$ability = $this->_get_active_ability($format);
		if (!$ability)
		{
			return 0;
		}
		return $amount / $ability->ability_per_hr;
	}

	private function _get_active_ability($format)
	{
		$ol = new object_list(array(
			"class_id" => CL_MRP_RESOURCE_ABILITY,
			"lang_id" => array(),
			"site_id" => array(),
			"CL_MRP_RESOURCE_ABILITY.RELTYPE_RESOURCE_ABILITY_ENTRY(CL_MRP_RESOURCE)" => $this->id(),
			"act_from" => new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, time()),
			"act_to" => new obj_predicate_compare(OBJ_COMP_GREATER, time()),
			"format" => $format->id()
		));
		if (!$ol->count())
		{
			return null;
		}
		return $ol->begin();
	}

	public function restore_data_integrity()
	{
		$this->load_threads();

		foreach ($this->threads as $id => $thread)
		{
			try
			{
				$job = obj($thread->get_job_id(), array(), CL_MRP_JOB);

				if (!in_array($job->prop("state"), mrp_job_obj::$in_progress_states))
				{ // clear a stuck thread
					$this->remove_thread($id);
					$this->add_thread();
				}
			}
			catch (awex_obj_na $e)
			{
				$this->remove_thread($id);
				$this->add_thread();
			}
			catch (awex_obj_class $e)
			{
				$this->remove_thread($id);
				$this->add_thread();
			}
			catch (awex_obj_acl $e)
			{
				$this->remove_thread($id);
				$this->add_thread();
			}
		}

		$this->save();
	}
}

class mrp_resource_thread
{
	protected $id;
	protected $state = mrp_resource_obj::STATE_AVAILABLE;
	protected $job; // current job oid
	protected $to_be_deleted = false;

	public function reserve(object $job)
	{
		if (!$this->is_available())
		{
			$e = new awex_mrp_resource_unavailable("Resource thread not available. Processing '{$this->job}'.");
			$e->processed_jobs = array($this->job);
			throw $e;
		}

		$this->state = mrp_resource_obj::STATE_RESERVED;
		$this->job = $job->id();
	}

	public function cancel_reservation(object $job)
	{
		if ($this->state === mrp_resource_obj::STATE_RESERVED and $job->id() === $this->job)
		{
			$this->state = mrp_resource_obj::STATE_AVAILABLE;
		}
	}

	public function process(object $job)
	{
		if ($job->id() !== $this->job)
		{
			if ($this->job)
			{
				$e = new awex_mrp_resource_unavailable("Trying to process job with id '" . $job->id() . "' that this thread isn't reserved for. Reserved or processing '{$this->job}'");
				$e->processed_jobs = array($this->job);
				throw $e;
			}
			else
			{
				throw new awex_mrp_resource_job("Trying to process job with id '" . $job->id() . "' that this thread isn't reserved for.");
			}
		}
		elseif ($this->state === mrp_resource_obj::STATE_PROCESSING)
		{
			throw new awex_redundant_instruction("Job with id '" . $job->id() . "' is already being processed.");
		}
		elseif ($this->state !== mrp_resource_obj::STATE_RESERVED)
		{
			throw new awex_mrp_resource("Trying to process job with id '" . $job->id() . "'. Resource state data corrupt. Thread state '{$this->state}'");
		}

		if ($this->to_be_deleted)
		{
			throw new awex_mrp_resource_state("Trying to process job with id '" . $job->id() . "' on a deleted thread.");
		}

		$this->job = $job->id();
		$this->state = mrp_resource_obj::STATE_PROCESSING;
	}

	public function finish(object $job)
	{
		if ($this->state !== mrp_resource_obj::STATE_PROCESSING)
		{
			throw new awex_mrp_resource_state("Trying to finish job with id '" . $job->id() . "'. Unexpected resource thread state '{$this->state}'");
		}

		if ($job->id() !== $this->job)
		{
			throw new awex_mrp_resource_job("Given job with id '" . $job->id() . "' is not being processed on this thread");
		}

		$this->job = null;
		$this->state = mrp_resource_obj::STATE_AVAILABLE;
	}

	public function is_available()
	{
		return !$this->to_be_deleted and $this->state === mrp_resource_obj::STATE_AVAILABLE;
	}

	public function is_processing(object $job)
	{
		return ($this->state === mrp_resource_obj::STATE_PROCESSING and $job->id() === $this->job);
	}

	public function get_job_id()
	{
		return $this->job;
	}

	public function delete()
	{
		$this->to_be_deleted = true;
	}

	public function deleted()
	{
		return $this->to_be_deleted;
	}
}

/** Generic mrp_resource exception **/
class awex_mrp_resource extends awex_mrp {}

/** Resource is unavailable **/
class awex_mrp_resource_unavailable extends awex_mrp_resource
{
	public $processed_jobs = array();
}

/** Resource state error **/
class awex_mrp_resource_state extends awex_mrp_resource {}

/** Job processing errors **/
class awex_mrp_resource_job extends awex_mrp_resource {}

/** Workspace error **/
class awex_mrp_resource_workspace extends awex_mrp_resource {}
