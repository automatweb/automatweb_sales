<?php
// mrp_schedule.aw - Ressursiplaneerija

require_once "mrp_header.aw";

class mrp_schedule extends db_connector
{
	# time() at the moment of starting scheduling (int)
	protected $scheduling_time;

	# how many seconds from time() to start schedule, should be at least scheduler's maximum execution time (int)
	protected $schedule_start = 300;

	# how many seconds from time() to start including unscheduled/not done/not started jobs in scheduling (int)
	protected $min_planning_jobstart = 300;

	# day end for the time when scheduling takes place. (timestamp)
	protected $scheduling_day_end;

	# years (float)
	protected $schedule_length = 2;

	# shortest time to partially do a job. seconds (int)
	protected $least_reasonable_joblength = 900;

	# for each resource and its threads. resource_tag1 => array (start_range1 => array (0 => array(start1, length1), ...), ...), ....
	protected $reserved_times = array ();

	# scheduled jobs : resource_id1 => array (id1 => array (starttime1, length1), ...), ...
	protected $job_schedule = array ();

	protected $project_schedule = array ();
	protected $schedulable_resources = array ();
	protected $workspace_id;
	protected $jobs_table = "mrp_job";

	# array (res_id => array (), ...)
	protected $resource_data = array ();

	# scheduler parameters
	protected $use_default_parameters = false;

	protected $parameter_due_date_overdue_slope = 0.5;
	protected $parameter_due_date_overdue_intercept = 10;
	protected $parameter_due_date_decay = 0.05;
	protected $parameter_due_date_intercept = 0.1;
	protected $parameter_priority_slope = 0.8;

	# importance of job start/job length in weighing available times for parallel threads (float)
	protected $parameter_start_priority = 1;
	protected $parameter_length_priority = 1;

	# ...
	protected $parameter_plan_materials = true;
	# END scheduler parameters


//!!! vbl teha ainult yks planeeritud t88de array ja seega siin absoluutsed ajad, mitte rel.
	protected $range_scale = array (//!!! tihedamalt, kogu planeeritava perioodi peale need piirkonnad teha vbl automaatselt.
		0,
		86400,
		172800,
		259200,
		345600,
		432000,
		518400,
		604800,
		864000,
		1209600,
		1814400,
		3456000,
		6048000,
		12096000,
	);
	# farthest ends of currently allocated jobs in $reserved_times for all ranges of $range_scale
	protected $range_ends = array ();

	protected $timings = array ();

	# resource unavailable times for range of time, output of get_unavailable_periods function (start => end)
	protected $unavailable_times = array ();

	# indicates whether initialization has been done for get_unavailable_periods
	protected $initialized = false;

	# method name to use for saving schedule data.
	// var $save_method = "save_direct";
	protected $save_method = "save_fileload";

	private $mrpdbg = false;

	function __construct()
	{
		$this->init();
	}

	protected function initialize ($arr)
	{
		$workspace = new object($arr["mrp_workspace"]);
		$this->workspace_id = $workspace->id ();
		$this->scheduling_time = time ();

		### get parameters
		if (!$this->use_default_parameters)
		{
			$schedule_length = $workspace->prop ("parameter_schedule_length");
			if (strlen(trim($schedule_length)))
			{
				$this->schedule_length = aw_math_calc::string2float($schedule_length);
			}

			$schedule_start = $workspace->prop ("parameter_schedule_start");
			if (strlen(trim($schedule_start)))
			{
				$this->schedule_start = (int) $schedule_start;
			}

			$min_planning_jobstart = $workspace->prop ("parameter_min_planning_jobstart");
			if (strlen(trim($min_planning_jobstart)))
			{
				$this->min_planning_jobstart = (int) $min_planning_jobstart;
			}

			### define timerange scale
			$range_scale = $workspace->prop ("parameter_timescale");
			$range_scale = explode (",", $range_scale);

			if (count($range_scale))
			{
				$range_scale_unit = (int) $workspace->prop ("parameter_timescale_unit");

				foreach ($range_scale as $key => $value)
				{
					$range_scale[$key] = ceil ($value * $range_scale_unit);
				}

				if (reset ($range_scale))
				{
					### prepend range starting at 0
					array_unshift ($range_scale, 0);
				}

				sort ($range_scale, SORT_NUMERIC);
				$this->range_scale = $range_scale;
			}

			### get combined_priority parameters
			$parameter_due_date_overdue_slope = $workspace->prop ("parameter_due_date_overdue_slope");
			if (strlen(trim($parameter_due_date_overdue_slope)))
			{
				$this->parameter_due_date_overdue_slope = aw_math_calc::string2float($parameter_due_date_overdue_slope);
			}

			$parameter_due_date_overdue_intercept = $workspace->prop ("parameter_due_date_overdue_intercept");
			if (strlen(trim($parameter_due_date_overdue_intercept)))
			{
				$this->parameter_due_date_overdue_intercept = aw_math_calc::string2float($parameter_due_date_overdue_intercept);
			}

			$parameter_due_date_decay = $workspace->prop ("parameter_due_date_decay");
			if (strlen(trim($parameter_due_date_decay)))
			{
				$this->parameter_due_date_decay = aw_math_calc::string2float($parameter_due_date_decay);
			}

			$parameter_due_date_intercept = $workspace->prop ("parameter_due_date_intercept");
			if (strlen(trim($parameter_due_date_intercept)))
			{
				$this->parameter_due_date_intercept = aw_math_calc::string2float($parameter_due_date_intercept);
			}

			$parameter_priority_slope = $workspace->prop ("parameter_priority_slope");
			if (strlen(trim($parameter_priority_slope)))
			{
				$this->parameter_priority_slope = aw_math_calc::string2float($parameter_priority_slope);
			}


			### ...
			$parameter_start_priority = $workspace->prop ("parameter_start_priority");
			if (strlen(trim($parameter_start_priority)))
			{
				$this->parameter_start_priority = abs(aw_math_calc::string2float($schedule_length));
			}

			$parameter_length_priority = $workspace->prop ("parameter_length_priority");
			if (strlen(trim($parameter_length_priority)))
			{
				$this->parameter_length_priority = abs(aw_math_calc::string2float($parameter_length_priority));
			}

			$this->parameter_plan_materials = (bool) $workspace->prop ("parameter_plan_materials");
		}

		$this->schedule_length = $this->schedule_length * 31536000;
		define ("MRP_INF", $this->schedule_length * 10);
		$this->schedule_start = $this->scheduling_time + $this->schedule_start;
		$this->min_planning_jobstart = $this->scheduling_time + $this->min_planning_jobstart;
		$this->scheduling_day_end = mktime (23, 59, 59, date ("m", $this->scheduling_time), date ("d", $this->scheduling_time), date("Y", $this->scheduling_time));

		### get schedulable resources
		#### shcedulable resource types
		$applicable_types = array (
			mrp_resource_obj::TYPE_SCHEDULABLE,
			mrp_resource_obj::TYPE_SUBCONTRACTOR
		);

		$resources_folder = $workspace->prop ("resources_folder");
		$resource_tree = new object_tree (array (
			"parent" => $resources_folder,
			"class_id" => array (CL_MRP_RESOURCE, CL_MENU),
			"type" => $applicable_types,
		));
		$resource_list = $resource_tree->to_list ();
		$resource_list->filter (array (
			"class_id" => CL_MRP_RESOURCE,
		));

		for ($resource = $resource_list->begin (); !$resource_list->end (); $resource = $resource_list->next ())
		{
			$this->schedulable_resources[] = $resource->id ();
		}
	}

/**
    @attrib name=create api=1
	@param mrp_workspace required type=oid
	@param mrp_force_replan optional type=int
	@errors
		throws awex_mrp_schedule_workspace if workspace not defined
		throws awex_mrp_schedule_lock when failed to lock system for scheduling
**/
	public function create ($arr)
	{
// /* dbg */ list($micro,$sec) = split(" ",microtime());
// /* dbg */ $ts_s = $sec + $micro;

		$workspace_id = (int) $arr["mrp_workspace"];
		$not_win32 = ("win32" !== aw_ini_get("server.platform"));
		$workspace = new object($workspace_id);
		$sem_id = 0;

		if (CL_MRP_WORKSPACE != $workspace->class_id())
		{
			throw new awex_mrp_schedule_workspace("Workspace not defined");
		}

		// Ee, lock ei t88ta vist
		if ($not_win32)
		{
			### get and acquire semaphore for given workspace
			$sem_id = sem_get($workspace_id, 1, 0666, 1);

			if ($sem_id === false)
			{
				throw new awex_mrp_schedule_lock("Lock init failed");
			}

			if (!sem_acquire($sem_id))
			{
				if (!sem_remove($sem_id))
				{
					if ($_GET["show_errors"] == 1) {echo sprintf (t("error@%s"), __LINE__) . MRP_NEWLINE; flush ();}
					// error::raise(array(
						// "msg" => t("Planeerimisluku lukustamiseta kustutamine eba&otilde;nnestus!"),
						// "fatal" => false,
						// "show" => false,
					// ));
				}

				if ($_GET["show_errors"] == 1) {echo sprintf (t("error@%s"), __LINE__) . MRP_NEWLINE; flush ();}
				// error::raise(array(
					// "msg" => t("Planeerimiseks lukustamine eba&otilde;nnestus!"),
					// "fatal" => true,
					// "show" => true,
				// ));//!!! vaadata uurida miks ikkagi aegajalt ei saada seda semafori k2tte.
				throw new awex_mrp_schedule_lock("Lock acquiring failed");
			}
		}

		### start scheduling only if input data has been altered
		if ( $workspace->prop("rescheduling_needed") or !empty($arr["mrp_force_replan"]) )
		{
			### set scheduling not needed, and start scheduling
			$workspace->set_prop("rescheduling_needed", 0);
			$workspace->save();
		}
		elseif ($not_win32)
		{
	  		### Release&remove semaphore. Stop, no rescheduling needed
			if (!isset($sem_id) || !sem_release($sem_id))
			{
				if (isset($_GET["show_errors"]) && $_GET["show_errors"] == 1) {echo sprintf (t("error@%s"), __LINE__) . MRP_NEWLINE; flush ();}
				// error::raise(array(
					// "msg" => t("Planeerimisluku avamine eba&otilde;nnestus!"),
					// "fatal" => false,
					// "show" => false,
				// ));
			}

			if (!isset($sem_id) || !sem_remove($sem_id))
			{
				if (!empty($_GET["show_errors"])) {echo sprintf (t("error@%s"), __LINE__) . MRP_NEWLINE; flush ();}
				// error::raise(array(
					// "msg" => t("Planeerimisluku kustutamine eba&otilde;nnestus!"),
					// "fatal" => false,
					// "show" => false,
				// ));
			}

			return;
		}
		else
		{
			return;
		}

// /* timing */ timing ("initialize", "start");


		$this->initialize ($arr);
		$jobs_folder = $workspace->prop ("jobs_folder");


// /* timing */ timing ("initialize", "end");
// /* timing */ timing ("get used resources", "start");


		### get used resources
		$resources = array ();
		$resource_tree = new object_tree (array (
			"parent" => $workspace->prop ("resources_folder"),
			"class_id" => array (CL_MENU, CL_MRP_RESOURCE)
		));
		$list = $resource_tree->to_list ();
		$list->filter (array (
			"class_id" => CL_MRP_RESOURCE
		));
		$resources = $list->ids ();


// /* timing */ timing ("get used resources", "end");
// /* timing */ timing ("init_resource_data", "start");


		$this->init_resource_data ($resources);


// /* timing */ timing ("init_resource_data", "end");
// /* timing */ timing ("initiate resource timetables", "start");
// /* dbg */  $res = 6672;
// /* dbg */  $arr = ($this->get_closest_unavailable_period($res, (14*3600)));
// /* dbg */  echo date (MRP_DATE_FORMAT, ($this->schedule_start+$arr[0]))."|".$arr[1];
// /* dbg */  arr ($this->resource_data[$res]);
// /* dbg */  exit;

		### initiate resource reserved times index
		if ($resources)
		{
			foreach ($resources as $resource_id)
			{
				$threads = $this->resource_data[$resource_id]["threads"];

				if (is_oid ($resource_id) and in_array ($resource_id, $this->schedulable_resources))
				{
					while ($threads--)
					{
						$resource_tag = $resource_id . "-" . $threads;
						$this->range_ends[$resource_tag] = $this->range_scale;

						foreach ($this->range_scale as $key => $start)
						{
							$this->reserved_times[$resource_tag][$key] = array ();
						}
					}
				}
			}
		}

// /* timing */ timing ("initiate resource timetables", "end");

		if ($this->parameter_plan_materials)
		{
			// material requirements plan
			$material_requirements = array(); //!!! format: product_id1 => array(date1 => required amount, ...), ... ???

// /* timing */ timing ("read material data", "start");
			// read all materials' order to delivery time times and other necessary data
			$material_data = array(); // format: product_id1 => array("delivery_time" => delivery_time1_seconds, "available_amount" => currently_available_amount_in_default_units), ...
			// available amount will decrease as higher priority jobs subtract their requirements as schedule planning progresses

			$purchasing_manager = new object($workspace->prop ("purchasing_manager"));

			if (CL_SHOP_PURCHASE_MANAGER_WORKSPACE != $purchasing_manager->class_id())
			{
				throw new awex_mrp_schedule_purchasemgr("Purchase manager not defined");
			}

			$warehouses = $purchasing_manager->get_warehouse_ids();

			if (count($warehouses))
			{
				$warehouses = implode(",", $warehouses);
				$this->db_query (
				"SELECT spp.product as product,spp.days as days,swa.amount as amount " .
				"FROM " .
					"aw_shop_product_purveyance spp" .
					"LEFT JOIN objects o1 ON o1.oid = spp.aw_oid " .
					"LEFT JOIN aw_shop_warehouse_amount swa ON spp.product = swa.product " .
					"LEFT JOIN objects o2 ON o2.oid = swa.aw_oid " .
				"WHERE " .
					"o1.status > 0 AND " . // purveyance not deleted
					"o2.status > 0 AND " . // amount not deleted
					"swa.is_default=1 AND " . // storage states only in default units
					"spp.warehouse IN ({$warehouses})" . // only from applicable warehouses
				"");

				### initiate array
				while ($acquisition_terms = $this->db_next ())
				{
					$material_data[$acquisition_terms["product"]] = array(
						"delivery_time" => $acquisition_terms["days"] * 86400,
						"available_amount" => $acquisition_terms["amount"]
					);
				}
			}
// /* timing */ timing ("read material data", "end");
		}

		### get inprogress jobs
		$applicable_states = array (
			mrp_job_obj::STATE_PAUSED,
			mrp_job_obj::STATE_SHIFT_CHANGE,
			mrp_job_obj::STATE_INPROGRESS
		);

		$list = new object_list (array (
			"class_id" => CL_MRP_JOB,
			"state" => $applicable_states,
			"parent" => $jobs_folder,
		));
		$inprogress_jobs = $list->arr ();

// /* timing */ timing ("get all projects from db & initiate project array", "start");


		### get all projects from db
		### schedulable project states
		$applicable_states = array (
			mrp_case_obj::STATE_PLANNED,
			mrp_case_obj::STATE_INPROGRESS,
		);

		if (!is_oid($workspace->prop ("projects_folder")))
		{
			return;
		}
		$this->db_query (
		"SELECT mrp_case.* " .
		"FROM " .
			"mrp_case " .
			"LEFT JOIN objects ON objects.oid = mrp_case.oid " .
		"WHERE " .
			"mrp_case.state IN (" . implode (",", $applicable_states) . ") AND " .
			"objects.status > 0 AND " .
			"objects.parent = " . $workspace->prop ("projects_folder") .
		"");
		$projects = array ();

		### initiate project array
		while ($project = $this->db_next ())
		{
			$projects[$project["oid"]] = array (
				"jobs" => array (),
				"starttime" => $project["starttime"],
				"progress" => $project["progress"],
				"due_date" => $project["due_date"],
				"customer_priority" => $project["customer_priority"],
				"order_quantity" => $project["order_quantity"],
				"project_priority" => $project["project_priority"],
				"state" => $project["state"]
			);
		}

// /* timing */ timing ("get all projects from db & initiate project array", "end");
// /* timing */ timing ("get all jobs from db", "start");


		### get all jobs from db
		### job states
		$applicable_states = array (
			mrp_job_obj::STATE_PLANNED,
			mrp_job_obj::STATE_NEW,
			mrp_job_obj::STATE_ABORTED
		);

		if ($this->parameter_plan_materials)
		{
			$this->db_query (
			"SELECT job.*, o.meta " .
			"FROM " .
				$this->jobs_table . " as job " .
				"LEFT JOIN objects o ON o.oid = job.oid " .
			"WHERE " .
				"job.state IN (" . implode (",", $applicable_states) . ") AND " .
				"job.project > 0 AND " .
				"o.status > 0 AND " .
				"o.parent = " . $jobs_folder . " AND " .
				"job.resource > 0 " .
			"");
		}
		else
		{ // used materials data not read.
			$this->db_query (
			"SELECT job.* " .
			"FROM " .
				$this->jobs_table . " as job " .
				"LEFT JOIN objects o ON o.oid = job.oid " .
			"WHERE " .
				"job.state IN (" . implode (",", $applicable_states) . ") AND " .
				"job.project > 0 AND " .
				"o.status > 0 AND " .
				"o.parent = " . $jobs_folder . " AND " .
				"job.resource > 0 " .
			"");
		}

// /* timing */ timing ("get all jobs from db", "end");
// /* timing */ timing ("distribute jobs to projects", "start");


		### distribute jobs to projects & initiate successor indices
		$starttime_index = array ();
		$successor_index = array ();

		while ($job = $this->db_next())
		{
			/* kiiruse huvides seda kontrolli praegu ei tehta. sellest v6ib mingitel juhtudel jama olla. !!!vaadata altpoolt kas kuskil vaja yldse midagi mis eeldab vaatamis6iguse olemasolu t88le.
			//!!! [15:16] <terryf> see onyks asi, mis seal fiksimist vajab jah
			if (!$this->can("view", $job["oid"]))  //!!! kui taas sisse lylitada siis tuleb panna evtendima acl_base-i
			{
				// echo t(sprintf ("Esines t&ouml;&ouml; (id: %s), mis pole kasutajale n&auml;htav. Planeerimine ei toimu adekvaatselt.", $job["oid"]));
				continue;
			}
			*/

			if (array_key_exists ($job["project"], $projects))
			{
				$projects[$job["project"]]["jobs"][$job["exec_order"]] = $job;
				$prerequisites = explode (",", $job["prerequisites"]);

				foreach ($prerequisites as $prerequisite)
				{
					if ($prerequisite !== "")
					{
						$successor_index[$prerequisite][] = $job["oid"];
					}
				}
			}
		}

// /* timing */ timing ("distribute jobs to projects", "end");
// /* timing */ timing ("insert inprogress jobs", "start");

		### insert inprogress jobs' remaining lengths to resource reserved times
		foreach ($inprogress_jobs as $job)
		{
			$planned_length = $job->prop("planned_length");
			$length = $planned_length ? (int) ($job->prop("length") * ((($job->prop("started") + $planned_length) - $this->schedule_start) / $planned_length)) : 0;

			if ($length > 0)
			{
				$this->currently_processed_job = $job->id ();
				list ($scheduled_start, $scheduled_length) = $this->reserve_time ($job->prop("resource"), $this->schedule_start, $length);

				if (isset ($scheduled_start))
				{
					### modify earliest starttime for unscheduled jobs next in workflow
					if (is_array ($successor_index[$job->id ()]))
					{
						foreach ($successor_index[$job->id ()] as $successor_id)
						{
							$tmp = ($scheduled_start + $scheduled_length + $job->prop("post_buffer"));

							if (!isset($starttime_index[$successor_id]) or $tmp > $starttime_index[$successor_id])
							{
								$starttime_index[$successor_id] = $tmp;
							}
						}
					}
				}
				else
				{
					echo sprintf (t("Viga tegemisel oleva t&ouml;&ouml; (id: %s) planeerimisel: sobivat algusaega ei leitud, t&ouml;&ouml;d ei planeeritud."), $job["oid"]) . MRP_NEWLINE;
				}

				### set planned finishing date for project
				$planned_date = $scheduled_start + $scheduled_length;

				if (!isset($this->project_schedule[$job->prop("project")]) or $planned_date > $this->project_schedule[$job->prop("project")][0])
				{
					$this->project_schedule[$job->prop("project")] = array ($planned_date, mrp_case_obj::STATE_INPROGRESS);
				}
			}
		}

		$inprogress_jobs = null;

// /* timing */ timing ("insert inprogress jobs", "end");
// /* timing */ timing ("sort jobs in projects", "start");

		### sort jobs in all projects
		foreach ($projects as $project_id => $project)
		{
			ksort ($projects[$project_id]["jobs"]);
		}


// /* timing */ timing ("sort jobs in projects", "end");
// /* timing */ timing ("sort projects", "start");


		### sort projects for scheduling by priority
		uasort ($projects, array ($this, "project_priority_comparison"));

// /* dbg */ arr($projects);exit;
// /* timing */ timing ("sort projects", "end");
// /* timing */ timing ("schedule jobs total", "start");

		### states for planning jobs
		$applicable_planning_states = array (
			mrp_job_obj::STATE_PLANNED,
			mrp_job_obj::STATE_NEW
		);

		### states for reserving job time and length
		$applicable_timereserve_states = array (
			mrp_job_obj::STATE_ABORTED
		);

		### schedule jobs in all projects
		// actual scheduling takes place here
		foreach ($projects as $project_id => $project)
		{
			if (count ($project["jobs"]))
			{
// /* dbg */ if ($project_id == 7700) {
// /* dbg */ $this->mrpdbg=1;
// /* dbg */ exit;
// /* dbg */ }
// /* timing */ timing ("one project total", "start");

				$project_start = $projects[$project_id]["starttime"];
				$project_progress = $projects[$project_id]["progress"];

				### schedule project jobs
				foreach ($project["jobs"] as $key => $job)
				{

// /* timing */ timing ("one job total", "start");
// /* timing */ timing ("reserve time & modify earliest start", "start");
// /* dbg */ if ((!empty($_GET["mrp_dbg_job"]) and $job["oid"] == $_GET["mrp_dbg_job"]) or (!empty($_GET["mrp_dbg_resource"]) and $job["oid"] == $_GET["mrp_dbg_resource"])) {
// /* dbg */ $this->mrpdbg=1;
// /* dbg */ echo "<hr><h2>Job oid: {$job["oid"]}</h2><hr>";
// /* dbg */ exit;
// /* dbg */ }

					$material_delivery_time = 0;
					if ($this->parameter_plan_materials)
					{ // get max material acquisition time
						$meta = utf_unserialize($job["meta"]);
						foreach ($meta["used_materials"] as $product_id)
						{
							$material_delivery_time = max($material_data[$product_id]["delivery_time"], $material_delivery_time);
						}
						$material_delivery_time += $this->schedule_start;
					}

					$this->currently_processed_job = (int) $job["oid"];
					$successor_starttime = isset($starttime_index[$job["oid"]]) ? $starttime_index[$job["oid"]] : 0;
					$minstart = max ($project_start, $project_progress, $this->schedule_start, $successor_starttime, $job["minstart"]);
					// $minstart = $job["pre_buffer"] + $minstart;


// /* dbg */ if ($this->mrpdbg) {
// /* dbg */ echo "minstart-". date (MRP_DATE_FORMAT,$minstart )." | length - ". $job["length"]/3600 ."h <br>";
// /* dbg */ arr ($job);
// /* dbg */ echo "minplan jobstart: " . date (MRP_DATE_FORMAT,$this->min_planning_jobstart) . "<br>";
// /* dbg */ echo "sched start: " . date (MRP_DATE_FORMAT,$this->schedule_start) . "<br>";
// /* dbg */ }

					$scheduled_start = $scheduled_length = NULL;

					if ( in_array ($job["state"], $applicable_planning_states) and in_array ($job["resource"], $this->schedulable_resources) and (($job["starttime"] >= $this->min_planning_jobstart) or ($job["starttime"] < $this->schedule_start) or !$job["starttime"]) and ($job["length"] > 0))
					{
						### (re)schedule job next in line
						list ($scheduled_start, $scheduled_length) = $this->reserve_time ($job["resource"], $minstart, $job["length"]);
						$this->job_schedule[$job["oid"]] = array ($scheduled_start, $scheduled_length, $job["state"]);

					}
					elseif (in_array ($job["state"], $applicable_timereserve_states) and in_array ($job["resource"], $this->schedulable_resources))
					{
						### postpone next jobs by job length
						$remaining_length = ($job["remaining_length"] > 0) ? $job["remaining_length"] : $job["length"];
						list ($scheduled_start, $scheduled_length) = $this->reserve_time ($job["resource"], $minstart, $remaining_length);
						$this->job_schedule[$job["oid"]] = array ($scheduled_start, $scheduled_length, $job["state"]);
					}
					elseif ( (!$job["length"]) and in_array ($job["state"], $applicable_planning_states) and in_array ($job["resource"], $this->schedulable_resources) )
					{
						### postpone next jobs by zero length job start
						$scheduled_start = $minstart;
						$scheduled_length = 0;
						$this->job_schedule[$job["oid"]] = array ($scheduled_start, $scheduled_length, $job["state"]);
					}
					else
					{
						continue;
					}

// /* dbg */ if ($this->mrpdbg) {
// /* dbg */ echo "rsrv time ret: ". date (MRP_DATE_FORMAT,$scheduled_start )." | length - ". $scheduled_length/3600 ."h <br>";
// /* dbg */ }
// /* timing */ timing ("reserve time & modify earliest start", "end");
// /* timing */ timing ("modify starttimes for next jobs in wf", "start");

					if (isset ($scheduled_start))
					{
						### modify earliest starttime for unscheduled jobs next in workflow
						if (isset ($successor_index[$job["oid"]]))
						{
							foreach ($successor_index[$job["oid"]] as $successor_id)
							{
								$parallel_part_time_est = // time that is estimated to take to finish producing/processing job items after continuing project on workflow's next resource
								$job["planned_length"] * 1 -
								($job["batch_size"]*$job["min_batches_to_continue_wf"])/($project["order_quantity"]*$job["component_quantity"]); // parallelly/sequentially processable item count quotient

								// time when currently scheduled job allows next job in workflow to start
								$tmp = $scheduled_start + $scheduled_length + $job["post_buffer"] - $parallel_part_time_est;

								//  ...
								if (!isset($starttime_index[$successor_id]) or $tmp > $starttime_index[$successor_id])
								{
									$starttime_index[$successor_id] = $tmp;
								}
							}
						}
					}
					else
					{
						echo sprintf (t("Viga t&ouml;&ouml; (id: %s) planeerimisel: sobivat algusaega ei leitud, t&ouml;&ouml;d ei planeeritud."), $job["oid"]) . MRP_NEWLINE;
					}

// /* timing */ timing ("modify starttimes for next jobs in wf", "end");

					### set planned finishing date for project
					$planned_date = $scheduled_start + $scheduled_length;
					if (!isset($this->project_schedule[$project_id]) or $planned_date > $this->project_schedule[$project_id][0])
					{
						$this->project_schedule[$project_id] = array ($planned_date, $project["state"]);
					}

// /* timing */ timing ("one job total", "end");
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* dbg */ if ($this->mrpdbg){
// /* dbg */ echo MRP_NEWLINE . "<i>END DBG</i>" . MRP_NEWLINE . MRP_NEWLINE;
// /* dbg */ $this->mrpdbg=0;
// /* dbg */ }
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------

				}
// /* timing */ timing ("one project total", "end");
			}
		}

// /* timing */ timing ("schedule jobs total", "end");
// /* dbg */ echo "<hr>";

		$this->save ();

  		if ($not_win32)
		{
			### Release&remove semaphore
			if (!sem_release($sem_id))
			{
				if ($_GET["show_errors"] == 1) {echo sprintf (t("error@%s"), __LINE__) . MRP_NEWLINE; flush ();}
				// error::raise(array(
					// "msg" => t("Planeerimisluku avamine peale planeerimist eba&otilde;nnestus!"),
					// "fatal" => false,
					// "show" => false,
				// ));
			}

			if (!sem_remove($sem_id))
			{
				if ($_GET["show_errors"] == 1) {echo sprintf (t("error@%s"), __LINE__) . MRP_NEWLINE; flush ();}
				// error::raise(array(
					// "msg" => t("Planeerimisluku kustutamine peale planeerimist eba&otilde;nnestus!"),
					// "fatal" => false,
					// "show" => false,
				// ));
				//!!! esineb sageli millegip2rast.
			}
		}

// /* dbg */ list($micro,$sec) = split(" ",microtime());
// /* dbg */ $ts_e = $sec + $micro;
// /* dbg */ $GLOBALS["timings"]["planning_time"] = $ts_s - $ts_e;
 // /* timing */ timing ();
// /* dbg */ exit;
	}

	function compute_due_date ()
	{
		$this->create ();
	}

	protected function save ()
	{
		call_user_func (array (&$this, $this->save_method));
	}

/* --------------------------  PRIVATE METHODS ----------------------------- */

	protected function save_direct ()
	{
		if (is_array ($this->project_schedule) and is_array ($this->job_schedule))
		{
			foreach ($this->project_schedule as $project_id => $project_data)
			{
					$project = obj ($project_id);
					$project->schedule($project_data[0]);

// /* dbg */ if ($_GET["mrp_dbg"]) {
// /* dbg */ echo "proj-" . $project_id . ": [" . date (MRP_DATE_FORMAT, $date) . "]<br>";
// /* dbg */ }
			}

			foreach ($this->job_schedule as $job_id => $job_data)
			{
					$job = obj ($job_id);
					$job->schedule($job_data[0], $job_data[1]);

// /* dbg */ if ($_GET["mrp_dbg"]) {
// /* dbg */ echo "job-" . $job_id . ": [" . date (MRP_DATE_FORMAT, $job_data[0]) . "] - [" . date (MRP_DATE_FORMAT, $job_data[0]+$job_data[1]) . "]<br>";
// /* dbg */ }
			}
		}
	}

	protected function save_fileload () // changes directly mrp_job_obj internal format data
	{
// /* timing */ timing ("save schedule data", "start");
		$win32 = ("win32" === aw_ini_get("server.platform"));

		if (count($this->project_schedule) or count($this->job_schedule))
		{
// /* timing */ timing ("save schedule data - projects", "start");
			$tmpname = tempnam(aw_ini_get("server.tmpdir"), "aw_mrpschedule_");
			$tmp = fopen ($tmpname, "w");

			foreach ($this->project_schedule as $project_id => $project_data)
			{
				if ($project_data[1] == mrp_case_obj::STATE_NEW)
				{
// /* timing */ timing ("save schedule data - projects - save new state", "start");
					$project = obj ($project_id);
					$project->schedule($project_data[0]);
// /* timing */ timing ("save schedule data - projects - save new state", "end");
				}
				else
				{
// /* timing */ timing ("save schedule data - projects - write datafile record", "start");
					fwrite ($tmp, "{$project_id}\t{$project_data[0]}\n");
// /* timing */ timing ("save schedule data - projects - write datafile record", "end");
				}

// /* dbg */ if ($_GET["mrp_dbg"]) {
// /* dbg */ echo "proj-" . $project_id . ": [" . date (MRP_DATE_FORMAT, $project_data[0]) . "]<br>";
// /* dbg */ }
			}

			fclose($tmp);
			chmod($tmpname, 0666);

			if ($win32)
			{
				$tmpname = str_replace("\\", "/", $tmpname);
			}

// /* timing */ timing ("save schedule data - projects", "end");
// /* timing */ timing ("save schedule data - load projectdata to DB", "start");
// $tmpname = str_replace("\\", "/", $tmpname);
			### load local file into db. LOCAL is slower but used because dbserver might be on another machine. Subject to change if speed is primary concern.
			// $query = "LOAD DATA LOCAL INFILE '{$tmpname}' REPLACE INTO TABLE `mrp_case_schedule`";
			$query = "LOAD DATA INFILE '{$tmpname}' REPLACE INTO TABLE `mrp_case_schedule`";
			// $query = "LOAD DATA LOCAL INFILE '{$tmpname}' REPLACE INTO TABLE mrp_case_schedule FIELDS TERMINATED BY ',' LINES TERMINATED BY '\n' (oid,planned_length,starttime)";
			// $query = "LOAD DATA INFILE '{$tmpname}' REPLACE INTO TABLE mrp_case_schedule";
			// $query = "LOAD DATA INFILE '{$tmpname}' REPLACE INTO TABLE mrp_case_schedule FIELDS TERMINATED BY ',' LINES TERMINATED BY '\n' (oid,planned_length,starttime)";
			$db_retval = $this->db_query ($query);

// /* timing */ timing ("save schedule data - load projectdata to DB", "end");

			if (!$db_retval)
			{
				error::raise(array(
					"msg" => t("Viga projektide planeeritud aegade salvestamisel. ") . $this->db_last_error,
					"fatal" => false,
					"show" => true,
				));
			}

			unlink($tmpname);


// /* timing */ timing ("save schedule data - jobs", "start");
			$tmpname = tempnam(aw_ini_get("server.tmpdir"), "mrpschedule");
			$tmp = fopen ($tmpname, "w");

			foreach ($this->job_schedule as $job_id => $job_data)
			{
				if (mrp_job_obj::STATE_NEW == $job_data[2])
				{
// /* timing */ timing ("save schedule data - jobs - save new state", "start");
					$job = obj ($job_id);
					$job->schedule($job_data[0], $job_data[1]);
// /* timing */ timing ("save schedule data - jobs - save new state", "end");
				}
				else
				{
// /* timing */ timing ("save schedule data - jobs - write datafile record", "start");
					fwrite ($tmp, "{$job_id}\t{$job_data[1]}\t{$job_data[0]}\n");
// /* timing */ timing ("save schedule data - jobs - write datafile record", "end");
				}

// /* dbg */ if ($_GET["mrp_dbg"]) {
// /* dbg */ echo "job-" . $job_id . ": [" . date (MRP_DATE_FORMAT, $job_data[0]) . "] - [" . date (MRP_DATE_FORMAT, $job_data[0]+$job_data[1]) . "]<br>";
// /* dbg */ }
			}

// /* timing */ timing ("save schedule data - jobs", "end");
// /* timing */ timing ("save schedule data - load jobdata to DB", "start");
			fclose($tmp);
			chmod($tmpname, 0666);

			if ($win32)
			{
				$tmpname = str_replace("\\", "/", $tmpname);
			}

			### load local file into db. LOCAL is slower but used because dbserver might be on another machine. Subject to change if speed is primary concern.
			// $query = "LOAD DATA LOCAL INFILE '{$tmpname}' REPLACE INTO TABLE mrp_schedule";
			$query = "LOAD DATA INFILE '{$tmpname}' REPLACE INTO TABLE `mrp_schedule`";
			// $query = "LOAD DATA LOCAL INFILE '{$tmpname}' REPLACE INTO TABLE mrp_schedule FIELDS TERMINATED BY ',' LINES TERMINATED BY '\n' (oid,planned_length,starttime)";
			// $query = "LOAD DATA INFILE '{$tmpname}' REPLACE INTO TABLE mrp_schedule";
			// $query = "LOAD DATA INFILE '{$tmpname}' REPLACE INTO TABLE mrp_schedule FIELDS TERMINATED BY ',' LINES TERMINATED BY '\n' (oid,planned_length,starttime)";
			$db_retval = $this->db_query ($query);

// /* timing */ timing ("save schedule data - load jobdata to DB", "end");

			if (!$db_retval)
			{
				if ($_GET["show_errors"] == 1) {echo sprintf (t("error@%s. db error: %s<br>"), __LINE__, $this->db_last_error); flush ();}
				error::raise(array(
					"msg" => t("Viga t&ouml;&ouml;de planeeritud aegade salvestamisel. ") . $this->db_last_error,
					"fatal" => false,
					"show" => true,
				));
			}

			unlink($tmpname);
		}
// /* timing */ timing ("save schedule data", "end");
	}

	function project_priority_comparison ($project1, $project2)
	{
// /* timing */ timing ("project_priority_comparison", "start");

		$due_date1 = $project1["due_date"] - $this->schedule_start;
		$due_date2 = $project2["due_date"] - $this->schedule_start;
		$project_priority1 = $project1["project_priority"];
		$project_priority2 = $project2["project_priority"];
		// $length1 = $project1["project_length"];
		// $length2 = $project2["project_length"];

		### function
		$value1 = $this->combined_priority ($due_date1, $project_priority1);
		$value2 = $this->combined_priority ($due_date2, $project_priority2);//!!! selle peaks siit mujale v6ibolla viima kui seda mitu korda samade param-tega tehakse.

		### return result
		if ($value1 > $value2)
		{
			$result = -1;
		}
		elseif ($value1 < $value2)
		{
			$result = 1;
		}
		else
		{
			$result = 0;
		}

// /* timing */ timing ("project_priority_comparison", "end");

		return $result;
	}

	function combined_priority ($x, $y)
	{
// /* timing */ timing ("combined_priority", "start");

		if ($x <= 0)
		{
			$value = (((-1*$this->parameter_due_date_overdue_slope)*$x) + $this->parameter_due_date_overdue_intercept) + ($this->parameter_priority_slope*$y);
		}
		else
		{
			if ((($x*$this->parameter_due_date_decay) + $this->parameter_due_date_intercept) == 0)
			{
				echo $x . "-" . $this->parameter_due_date_decay . "-" . $this->parameter_due_date_intercept;
			}

			$value = (1/(($x*$this->parameter_due_date_decay) + $this->parameter_due_date_intercept)) + ($this->parameter_priority_slope*$y);
		}

// /* timing */ timing ("combined_priority", "end");

		return $value;
	}

	protected function reserve_time ($resource_id, $start, $length)
	{
// /* timing */ timing ("reserve_time", "start");
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* dbg */ if ($this->mrpdbg){
// /* dbg */ echo "<h4>reserve_time</h4>";
// /* dbg */ }
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------

		$threads = $this->resource_data[$resource_id]["threads"];
		$available_times = array ();
		#### convert to relative time (for $reserved_times, $range_scale, ...)
		$start = ($start > $this->schedule_start) ? ($start - $this->schedule_start) : 0;

		while ($threads--)
		{
			$resource_tag = $resource_id . "-" . $threads;
			$tmp = $this->get_available_time ($resource_tag, $start, $length);
			$available_times[$resource_tag] = $tmp;


// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* dbg */ if ($this->mrpdbg){
// /* dbg */ echo "thread:" . $threads. " restag:" . $resource_tag. " avail. time for this tag (start, length, timerange):";
// /* dbg */ arr ($available_times[$resource_tag]);
// /* dbg */ echo "reserved times this tag: ";
// /* dbg */ arr ($this->reserved_times[$resource_tag]);
// /* dbg */ }
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------

		}

		### select thread with minimal start&length
		$weight = NULL;
		$selected_resource_tag = NULL;
		$reserved_time = NULL;
		$reserved_length = NULL;
		$reserved_time_range = NULL;

		foreach ($available_times as $resource_tag => $available_time)
		{
			if (is_array ($available_time))
			{
				list ($start, $length, $time_range) = $available_time;
				$new_weight = ($start * $this->parameter_start_priority + $length * $this->parameter_length_priority) / 2;

				if (!isset ($weight) or ($new_weight < $weight))
				{
					$selected_resource_tag = $resource_tag;
					$weight = $new_weight;

					$reserved_time = $start;
					$reserved_length = $length;
					$reserved_time_range = $time_range;
				}

// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* dbg */ if ($this->mrpdbg){
// /* dbg */ echo "new_weight: " . $new_weight . MRP_NEWLINE;
// /* dbg */ echo "weight: " . $weight . MRP_NEWLINE;
// /* dbg */ echo "potential reserved_time: " . $reserved_time . MRP_NEWLINE . MRP_NEWLINE;
// /* dbg */ }
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
			}
		}

// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* dbg */ if ($this->mrpdbg){
// /* dbg */ echo "selected_resource_tag: " . $selected_resource_tag . MRP_NEWLINE;
// /* dbg */ }
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------

		if (!isset ($reserved_time))
		{
			echo sprintf (t("Sobivat aega ei leidunud tervest kalendrist! resource-tag: %s, job: %s"), $resource_tag, $this->currently_processed_job) . MRP_NEWLINE;
		}
		else
		{
			### reserve time
			#### see if unavailable times have shifted start beyond originally selected timerange, find correct timerange
			while (isset ($this->range_scale[$reserved_time_range + 1]) and ($this->range_scale[$reserved_time_range + 1] <= $reserved_time))
			{
				$reserved_time_range++;
			}

			$this->reserved_times[$selected_resource_tag][$reserved_time_range][] = array($reserved_time, $reserved_length);

			### sort changed range by starttimes
			try
			{
				usort($this->reserved_times[$selected_resource_tag][$reserved_time_range], "mrp_schedule_reserved_times_sorter");
			}
			catch (Exception $e)
			{
				echo sprintf (t("Samale algusajale on mitu t88d reserveeritud! resource-tag: %s, job: %s"), $selected_resource_tag, $this->currently_processed_job) . MRP_NEWLINE;
			}

			### update max. reach of selected and sequent timeranges (job may reach over next range(s)).
			$reserved_end = ($reserved_time + $length);
			$i = $reserved_time_range;

			while (isset ($this->range_ends[$selected_resource_tag][$i]) and $this->range_ends[$selected_resource_tag][$i] < $reserved_end)
			{
				$this->range_ends[$selected_resource_tag][$i] = $reserved_end;
				$i++;
			}

			### convert back to real time
			$reserved_time += $this->schedule_start;

// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* dbg */ if ($this->mrpdbg){
// /* dbg */ echo "reserved times after this reservation: ";
// /* dbg */ arr ($this->reserved_times[$selected_resource_tag]);
// /* dbg */ echo "reserved time: " . $reserved_time . " [" . date (MRP_DATE_FORMAT, $reserved_time) . "]" . MRP_NEWLINE;
// /* dbg */ }
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* timing */ timing ("reserve_time", "end");

			return array ($reserved_time, $length);
		}
// /* timing */ timing ("reserve_time", "end");
	}

	protected function get_next_range_first_job ($resource_tag, $time_range)
	{
// /* timing */ timing ("get_next_range_first_job", "start");
// /* dbg */ if ($this->mrpdbg){
// /* dbg */ echo "<h4>get_next_range_first_job</h4>";
// /* dbg */ }

		$i = 1;
		$start2 = MRP_INF;

		while (isset ($this->reserved_times[$resource_tag][$time_range + $i]) and ($start2 === MRP_INF))
		{
			if (count($this->reserved_times[$resource_tag][$time_range + $i]))
			{
				$start2 = $this->reserved_times[$resource_tag][$time_range + $i][0][0]; // first element starttime value in this range for this resource thread
			}

			$i++;
		}

// /* dbg */ if ($this->mrpdbg){
// /* dbg */ echo "found next_range_first_job  start2: ".$start2 . " in timerange: " . ($time_range+$i-1) . MRP_NEWLINE;
// /* dbg */ }
// /* timing */ timing ("get_next_range_first_job", "end");

		return $start2;
	}

	protected function add_unavailable_times ($resource_id, $reserved_time, $length, $start2)
	{
// /* timing */ timing ("add_unavailable_times", "start");
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* dbg */ if ($this->mrpdbg){
// /* dbg */ echo "<h4>add_unavailable_times</h4>";
// /* dbg */ }
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------

		$unavailable_start = $unavailable_length = NULL;
		list ($unavailable_start, $unavailable_length) = $this->get_closest_unavailable_period ($resource_id, $reserved_time);


// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* dbg */ if ($this->mrpdbg){
// /* dbg */ echo "reservedtime: " . date (MRP_DATE_FORMAT, $this->schedule_start + $reserved_time) . MRP_NEWLINE;
// /* dbg */ echo "1st unavail: " . date (MRP_DATE_FORMAT, $this->schedule_start + $unavailable_start) ."-". date (MRP_DATE_FORMAT, $this->schedule_start + $unavailable_start + $unavailable_length) . MRP_NEWLINE;
// /* dbg */ $dbg_time = $unavailable_start + $unavailable_length;
// /* dbg */ }
// /* dbg */ echo date (MRP_DATE_FORMAT, $this->schedule_start + $unavailable_start) ."-". date (MRP_DATE_FORMAT, $this->schedule_start + $unavailable_start + $unavailable_length)."<br>";
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------


		### check if unavailable time starts before reserved time ends
		if (isset ($unavailable_start) and (($reserved_time + $length) > $unavailable_start))
		{
			### check if reserved starttime is in an unavailable period & make starttime correction, shifting it to the end of that unavail. period
			if ( ($reserved_time < ($unavailable_start + $unavailable_length)) and ($reserved_time >= $unavailable_start) )
			{
				### check whether with moved starttime it still fits before next already scheduled job's starting time
				if (($unavailable_start + $unavailable_length + $length) <= $start2)
				{
					$reserved_time = $unavailable_start + $unavailable_length;
					list ($unavailable_start, $unavailable_length) = $this->get_closest_unavailable_period ($resource_id, $reserved_time);


// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* dbg */ if ($this->mrpdbg){
// /* dbg */ echo "2nd unavail: " . date (MRP_DATE_FORMAT, $this->schedule_start + $unavailable_start) ."-". date (MRP_DATE_FORMAT, $this->schedule_start + $unavailable_start + $unavailable_length) . MRP_NEWLINE;
// /* dbg */ $dbg_time = $unavailable_start + $unavailable_length;
// /* dbg */ }
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------


				}
				else
				{


// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* dbg */ if ($this->mrpdbg){
// /* dbg */ echo "moved starttime doesn't fit before next job. " . date (MRP_DATE_FORMAT, $this->schedule_start + $unavailable_start) ."-". date (MRP_DATE_FORMAT, $this->schedule_start + $unavailable_start + $unavailable_length) . MRP_NEWLINE;
// /* dbg */ }
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* timing */ timing ("add_unavailable_times", "end");

					return;
				}
			}

// /* timing */ timing ("add_unavailable_times - insert unavailable periods to job length", "start");

			### check if reserved time covers unavailable periods & make length correction if job fits in slices else start over
			$i_dbg1 = 0;

			while ( isset ($unavailable_start) and (($reserved_time + $length) > $unavailable_start) )
			{

// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* dbg */ if ($this->mrpdbg){
// /* dbg */ echo "cycle start unavail: " . date (MRP_DATE_FORMAT, $this->schedule_start + $unavailable_start) ."-". date (MRP_DATE_FORMAT, $this->schedule_start + $unavailable_start + $unavailable_length)." | len: " . $unavailable_length/3600 . " | resp to time: " . date (MRP_DATE_FORMAT, $this->schedule_start + $dbg_time) . "<br>";
// /* dbg */ $dbg_time = $unavailable_start + $unavailable_length;
// /* dbg */ }
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------

				if ($i_dbg1++ == 1000)
				{
					if ($_GET["show_errors"] == 1) {echo sprintf (t("error@%s. res: %s, job: %s<br>"), __LINE__, $resource_id, $this->currently_processed_job); flush ();}
					// error::raise(array(
						// "msg" => sprintf (t("Unavailable times covered by reserved exceeded reasonable limit (%s). Resource %s, job %s"), $i_dbg1, $resource_id, $this->currently_processed_job),
						// "fatal" => false,
						// "show" => false,
					// ));
					echo "viga@" . __LINE__ . MRP_NEWLINE;
// /* timing */ timing ("add_unavailable_times", "end");

					return;
				}


				### check if with added unavailable period length, job still fits before next job
				if (($reserved_time + $length + $unavailable_length) > $start2)
				{
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* dbg */ if ($this->mrpdbg){
// /* dbg */ echo "cycle didnt fit: true<br>";
// /* dbg */ }
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* timing */ timing ("add_unavailable_times", "end");

					return;
				}
				else
				{
					$length += $unavailable_length;
					list ($unavailable_start, $unavailable_length) = $this->get_closest_unavailable_period ($resource_id, ($unavailable_start + $unavailable_length));


// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* dbg */ if ($this->mrpdbg){
// /* dbg */ echo "cycle end unavail: " . date (MRP_DATE_FORMAT, $this->schedule_start + $unavailable_start) ."-". date (MRP_DATE_FORMAT, $this->schedule_start + $unavailable_start + $unavailable_length)." resp to time: " . date (MRP_DATE_FORMAT, $this->schedule_start + $dbg_time) . "<br>";
// /* dbg */ echo "cycle end length: " . $length/3600 . "h<br>";
// /* dbg */ echo "cycle end reserved_time+length: " . date (MRP_DATE_FORMAT, $this->schedule_start + $reserved_time + $length) . "<br>";
// /* dbg */ }
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
				}
			}
// /* timing */ timing ("add_unavailable_times - insert unavailable periods to job length", "end");
		}
// /* timing */ timing ("add_unavailable_times", "end");

		return array ($reserved_time, $length);
	}

	protected function get_available_time ($resource_tag, $start, $length)
	{
// /* timing */ timing ("get_available_time", "start");
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* dbg */ if ($this->mrpdbg){
// /* dbg */ echo "<h4>get_available_time</h4>";
// /* dbg */ }
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------

		list ($resource_id, $thread) = sscanf ($resource_tag, "%u-%u");

		### find range for given starttime
		$reserved_time = $reserved_length = NULL;
		$time_range = $this->find_range ($start);

		### get place for job
		while (isset ($this->reserved_times[$resource_tag][$time_range]) and !isset ($reserved_time))
		{ ### find free space with right length/start

// /* timing */ timing ("get_available_time - get_snug_slot", "start");
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* dbg */ if ($this->mrpdbg){
// /* dbg */ echo "<h5>get_snug_slot</h5>";
// /* dbg */ echo "timerange:" . $time_range ."<br>"; $time_range_dbg = 0;
// /* dbg */ while ($time_range_dbg < ($time_range + 1)) {
// /* dbg */ echo "<hr> timerangedbg:" .  $time_range_dbg . "<br>";
// /* dbg */ foreach ($this->reserved_times[$resource_tag][$time_range_dbg] as $time_dbg) {
// /* dbg */ echo "start1:". date (MRP_DATE_FORMAT, $this->schedule_start + $time_dbg[0]) . " len:" . $time_dbg[1] . " end:" . date (MRP_DATE_FORMAT, $this->schedule_start + $time_dbg[0] + $time_dbg[1]) . "<br>";	}  $time_range_dbg++; }
// /* dbg */ arr ($this->reserved_times[$resource_tag]);
// /* dbg */ }
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------

			### get max reach of previous timerange
			$prev_range_end = (0 === $time_range) ? 0 : $this->range_ends[$resource_tag][($time_range - 1)];//!!! kui esimene range siis oli -1 indeks, vbl panna range_ends arraysse ka -1 kohta midagi?

			if (count ($this->reserved_times[$resource_tag][$time_range]))
			{ ### timerange has already reserved times
				### check if there's space for the job between prev. range end and this range first job (first job by reset because ksort just done)
				$start2 = $this->reserved_times[$resource_tag][$time_range][0][0];
				$d = ($start < ($prev_range_end)) ? 0 : ($start - ($prev_range_end));

				if ( (($prev_range_end + $length + $d) <= $start2) and ($start2  >= ($start + $length)) )
				{
					$start1 = $prev_range_end;

// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* dbg */ if ($this->mrpdbg){
// /* dbg */ echo "A -- start1:". date (MRP_DATE_FORMAT, $this->schedule_start + $start1)." - length1:0 - start:". date (MRP_DATE_FORMAT, $this->schedule_start + $start) ."-start2:". date (MRP_DATE_FORMAT, $this->schedule_start + $start2)  . MRP_NEWLINE;
// /* dbg */ echo "A -- start1:". $start1." - length1:0 - start:". $start ."-start2:".$start2  . MRP_NEWLINE;
// /* dbg */ }
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------

					$reserved_time = ($start1 >= $start) ? $start1 : $start;
					list ($reserved_time, $reserved_length) = $this->add_unavailable_times ($resource_id, $reserved_time, $length, $start2);
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* dbg */ if ($this->mrpdbg){ echo "A -- suitable slot found in start range nr {$time_range}. at range beginning." . MRP_NEWLINE; }
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------

				}

				if (!isset ($reserved_time))
				{ ### space between prev. range end and this range first job not found, search for times after first already reserved job in this range
					### go through reserved times in current timerange to find place for job being scheduled
					$time_range_contents_tmp = $this->reserved_times[$resource_tag][$time_range];
					$i = 0;
					while (isset($time_range_contents_tmp[$i]))
					{
						$start1 = $time_range_contents_tmp[$i][0];
						$length1 = $time_range_contents_tmp[$i][1];

						### get next reserved time start -- start2
						if (isset($time_range_contents_tmp[$i+1][0]))
						{ #### look for it in the same timerange, after current reserved time (start1)
							$start2 = $time_range_contents_tmp[$i+1][0];
						}
						else
						{ #### look for it in the sequent timeranges
							$start2 = $this->get_next_range_first_job ($resource_tag, $time_range);
						}

						$end1 = $start1 + $length1;
						$d = ($start < $end1) ? 0 : ($start - $end1);

						### check if requested space is available between start1 & start2
						if ( (($end1 + $length + $d) <= $start2) and ($start2  >= ($start + $length)) )
						{
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* dbg */ if ($this->mrpdbg){
// /* dbg */ echo "B -- start1:". date (MRP_DATE_FORMAT, $this->schedule_start + $start1)." - length1:".$length1." - start:". date (MRP_DATE_FORMAT, $this->schedule_start + $start) ." - start2:". date (MRP_DATE_FORMAT, $this->schedule_start + $start2) . MRP_NEWLINE;
// /* dbg */ echo "B -- start1:". $start1." - length1:".$length1." - start:". $start ." - start2:".$start2 . MRP_NEWLINE;
// /* dbg */ }
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------

							$reserved_time = (($end1) >= $start) ? ($end1) : $start;
							list ($reserved_time, $reserved_length) = $this->add_unavailable_times ($resource_id, $reserved_time, $length, $start2);

// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* dbg */ if ($this->mrpdbg){ echo "B -- suitable slot found in start range nr {$time_range}. among reserved times" . MRP_NEWLINE; }
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------

							if (isset ($reserved_time) and isset ($reserved_length))
							{
								break;
							}
						}

						++$i;
					}
				}
			}
			else
			{ ### no times reserved yet
				if ($this->range_scale[$time_range] > $prev_range_end)
				{
					$start1 = $this->range_scale[$time_range];

// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* dbg */ if ($this->mrpdbg){ echo "C -- start1 from range_scale." . MRP_NEWLINE; }
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------

				}
				else
				{ ### prev range contains job that reaches beyond this range start
					$start1 = $prev_range_end;

// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* dbg */ if ($this->mrpdbg){ echo "C -- start1 from prev_range_end." . MRP_NEWLINE; }
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------

				}

				$start2 = $this->get_next_range_first_job ($resource_tag, $time_range);
				$d = ($start < $start1) ? 0 : ($start - $start1);

// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* dbg */ if ($this->mrpdbg){
// /* dbg */ echo "C -- start1:". date (MRP_DATE_FORMAT, $this->schedule_start + $start1)." - length1:0 - start:". date (MRP_DATE_FORMAT, $this->schedule_start + $start) ."-start2:". date (MRP_DATE_FORMAT, $this->schedule_start + $start2) . MRP_NEWLINE;
// /* dbg */ echo "C -- start1:". $start1." - length1:0 - start:". $start ."-start2:".$start2 . MRP_NEWLINE;
// /* dbg */ }
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------

				### check if requested space is available between start1 & start2
				if ( (($start1 + $length + $d) <= $start2) and ($start2  >= ($start + $length)) )
				{
					$reserved_time = ($start1 > $start) ? $start1 : $start;
					list ($reserved_time, $reserved_length) = $this->add_unavailable_times ($resource_id, $reserved_time, $length, $start2);

// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* dbg */ if ($this->mrpdbg){ echo "C -- suitable slot found in start range nr {$time_range}. from empty range" . MRP_NEWLINE; }
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------

				}
			}

// /* timing */ timing ("get_available_time - get_snug_slot", "end");
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* dbg */ if ($this->mrpdbg){
// /* dbg */ if (!isset ($reserved_time)){ echo "suitable slot not found in this start range (range nr: {$time_range})" . MRP_NEWLINE; }
// /* dbg */ }
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------

			$time_range++;
		}

// /* timing */ timing ("get_available_time", "end");

		### return planned starttime
		$time_range--;

		if (!isset ($reserved_time) or !isset ($reserved_length))
		{
			### place after last range last job. assume reserved times array has been traversed and $time_range points to last range
			$last_range_last_element_index = count($this->reserved_times[$resource_tag][$time_range]) - 1;

			if (isset($this->reserved_times[$resource_tag][$time_range][$last_range_last_element_index]))
			{ // after last reserved time
				$start1 = $this->reserved_times[$resource_tag][$time_range][$last_range_last_element_index][0];
				$length1 = $this->reserved_times[$resource_tag][$time_range][$last_range_last_element_index][1];
				$reserved_time = $start1 + $length1;
			}
			else
			{ // no reserved times yet in the last range
				$reserved_time = $this->range_scale[$time_range];
			}

			list ($reserved_time, $reserved_length) = $this->add_unavailable_times ($resource_id, $reserved_time, $length, MRP_INF);
		}

		return array ($reserved_time, $reserved_length, $time_range);
	}

	protected function find_range ($starttime)
	{
// /* timing */ timing ("find_range", "start");
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* dbg */ if ($this->mrpdbg){
// /* dbg */ echo "<h5>find_range</h5>";
// /* dbg */ }
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------

		$low = 0;
		$high = count ($this->range_scale) - 1;
		$i_dbg = 0;
		$mid = 0;

		while ($low <= $high)
		{
			$mid = (int) floor (($low + $high) / 2);
			$next = isset ($this->range_scale[$mid + 1]) ? $this->range_scale[$mid + 1] : ($this->schedule_length + 1);

			if ( ($starttime >= $this->range_scale[$mid]) and ($starttime < $next) )
			{

// /* timing */ timing ("find_range", "end");
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* dbg */ if ($this->mrpdbg){
// /* dbg */ echo "found range [{$mid}] resp. to starttime [{$starttime}]" . MRP_NEWLINE;
// /* dbg */ }
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------

				break;
			}
			else
			{
				if ($starttime < $this->range_scale[$mid])
				{
					$high = $mid - 1;
				}
				else
				{
					$low = $mid + 1;
				}
			}

			if ($i_dbg++ == (count ($this->range_scale) * 3))
			{
				if ($_GET["show_errors"] == 1) {echo sprintf (t("error@%s. job: %s<br>"), __LINE__, $this->currently_processed_job); flush ();}
				// error::raise(array(
					// "msg" => sprintf (t("Timerange search exceeded reasonable limit (%s) of cycles. Job %s"), $i_dbg2, $this->currently_processed_job),
					// "fatal" => false,
					// "show" => false,
				// ));
				echo "viga@" . __LINE__ . MRP_NEWLINE;
				break;
			}
		}

		return $mid;
// /* timing */ timing ("find_range", "end");
	}

	// param $resource_id
	// param $time - int unix timestamp time for which to get the closest unavailable period
	// returns resource's unavailable period that has a start closest to $time. array($period_start, $period_length)
	protected function get_closest_unavailable_period ($resource_id, $time)
	{
// /* timing */ timing ("get_closest_unavailable_period", "start");
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* dbg */ if ($this->mrpdbg){
// /* dbg */ echo "<h4>get_closest_unavailable_period</h4>";
// /* dbg */ }
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------

		### convert to real time
		$time += $this->schedule_start;
		$start = $end = NULL;

		### ...
		list ($start, $end) = $this->_get_closest_unavailable_period ($resource_id, $time);

// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* dbg */ if ($this->mrpdbg){
// /* dbg */ echo "closestper1: ". date (MRP_DATE_FORMAT, $start). "-" .date (MRP_DATE_FORMAT, $end) . " | resp to: " .date (MRP_DATE_FORMAT, ($time)) . MRP_NEWLINE;
// /* dbg */ }
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------

		if (isset ($start))
		{
			### find if period ends before another starts
			$i_dbg = 0;
			$period_start = $period_end = NULL;

			do
			{
				list ($period_start, $period_end) = $this->_get_closest_unavailable_period ($resource_id, $end);

// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* dbg */ if ($this->mrpdbg){
// /* dbg */ echo "closestper cycle: ". date (MRP_DATE_FORMAT, $period_start). "-" .date (MRP_DATE_FORMAT, $period_end) . " | resp to: " .date (MRP_DATE_FORMAT, ($end)) . MRP_NEWLINE;
// /* dbg */ }
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------

				if (isset ($period_start))
				{
					if ($period_start <= $end)
					{
						$end = $period_end;
					}
					else
					{
						$period_start = NULL;
					}
				}

				if ($i_dbg++ == 10000)
				{
					//!!! siia j6utakse t6en2oliselt siis kui kogu aeg on ressurss kinni, tykkide kaupa.
					// if ($_GET["show_errors"] == 1) {echo sprintf (t("error@%s. res: %s, job %s<br>"), __LINE__, $resource_id, $this->currently_processed_job); flush ();}
					// error::raise(array(
						// "msg" => sprintf (t("Ressursil id-ga %s pole piirangu ulatuses vabu aegu. V&otilde;imalik on ka viga v&otilde;i etten&auml;gematu seadistus ressursi t&ouml;&ouml;aegades. T&ouml;&ouml; id: %s"), $resource_id, $this->currently_processed_job),
						// "fatal" => false,
						// "show" => false,
					// ));
					echo sprintf (t("Ressursil id-ga %s pole piirangu ulatuses vabu aegu. V&otilde;imalik on ka viga v&otilde;i etten&auml;gematu seadistus ressursi t&ouml;&ouml;aegades. T&ouml;&ouml; id: %s"), $resource_id, $this->currently_processed_job) . MRP_NEWLINE;
					flush ();
					return;
				}
			}
			while (isset ($period_start));

			### convert back to relative time & return
			$period_start = ($start > $this->schedule_start) ? ($start - $this->schedule_start) : 0;
			$period_length = ($start > $this->schedule_start) ? ($end - $start) : ($end - $this->schedule_start);

// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* dbg */ if ($this->mrpdbg){
// /* dbg */ echo "closestper ret: ". date (MRP_DATE_FORMAT, $start). "-" .date (MRP_DATE_FORMAT, ($start+$period_length)) . " | resp to: " .date (MRP_DATE_FORMAT, ($time)) . "<br>";
// /* dbg */ }
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* timing */ timing ("get_closest_unavailable_period", "end");

			return array ($period_start, $period_length);
		}
// /* timing */ timing ("get_closest_unavailable_period", "end");
	}

	## returns $start and $length of next unavailable period after $time. if $time is in an unavail. period, that period's data is returned.
	## $start >= $time < ($start+$length)
	protected function _get_closest_unavailable_period ($resource_id, $time)
	{
// /* timing */ timing ("_get_closest_unavailable_period", "start");
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* dbg */ if ($this->mrpdbg){
// /* dbg */ echo "<h5>_get_closest_unavailable_period</h5>";
// /* dbg */ echo "timeforclosestper:". date (MRP_DATE_FORMAT, $time) . MRP_NEWLINE;
// /* dbg */ }
// /* dbg */ if ((time() - $this->scheduling_time) > 25){
// /* dbg */ echo "<br>time:". date (MRP_DATE_FORMAT, $time) . " res:".$resource_id." t:".$this->scheduling_time."<br>";
// /* dbg */ }
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------

		$closest_periods = array ();

		### get dateinfo
		$day_start = mktime (0, 0, 0, date ("m", $time), date ("d", $time), date("Y", $time));

		### get closest global buffer
		if ($this->resource_data[$resource_id]["global_buffer"] > 0)
		{
			if ($time <= $this->scheduling_day_end)
			{
				$global_buffer_start = $day_start + 86400 + (86400 - $this->resource_data[$resource_id]["global_buffer"]);
			}
			else
			{
				$global_buffer_start = $day_start + (86400 - $this->resource_data[$resource_id]["global_buffer"]);
			}

			$closest_periods[$global_buffer_start] = $global_buffer_start + $this->resource_data[$resource_id]["global_buffer"];
		}

		### get recurrences
		foreach ($this->resource_data[$resource_id]["recurrence_definitions"] as $recurrence)
		{
			if (($recurrence["max_span"] > $time) and ($recurrence["start"] <= $time))
			{ #### only process recurrences that may be relevant to $time -- that may end after $time or start before

// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* dbg */ if ($this->mrpdbg) {
// /* dbg */ echo "recstart: " . date (MRP_DATE_FORMAT, $recurrence["start"]) . " | recinterval: " .  $recurrence["interval"] .  " | reclength: " .  $recurrence["length"] / 3600 . "h | rectime: " .  $recurrence["time"] / 3600 . "h" . MRP_NEWLINE;
// /* dbg */ arr ($recurrence);
// /* dbg */ }
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------

				### make dst corrections
				// $nodst_day_start = $recurrence["start"] + floor (($time - $recurrence["start"]) / $recurrence["interval"]) * $recurrence["interval"];
				$nodst_day_start = $recurrence["start"] + (($time - $recurrence["start"]) - (($time - $recurrence["start"]) % $recurrence["interval"]));
				$nodst_day_hour = (int) date ("H", $nodst_day_start);

				if ($nodst_day_hour === 0)
				{
					$dst_day_start = $nodst_day_start;
				}
				else
				{
					if ($nodst_day_hour < 13)
					{
						$dst_error = $nodst_day_hour;
						$dst_day_start = $nodst_day_start - $dst_error*3600;
					}
					else
					{
						$dst_error = 24 - $nodst_day_hour;
						$dst_day_start = $nodst_day_start + $dst_error*3600;
					}
				}

				### ...
				$recurrence_start = $dst_day_start + $recurrence["time"];
				$recurrence_end = $recurrence_start + $recurrence["length"];


// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* dbg */ if ($this->mrpdbg){
// /* dbg */ echo " rectime:". ($recurrence["time"] / 3600) . " h" . MRP_NEWLINE;
// /* dbg */ echo " recdaystart: nodst - ". date (MRP_DATE_FORMAT, $nodst_day_start) . " | dst - ". date (MRP_DATE_FORMAT, $dst_day_start) . MRP_NEWLINE;
// /* dbg */ echo " recperiod:". date (MRP_DATE_FORMAT, $recurrence_start) ."-". date (MRP_DATE_FORMAT, $recurrence_end) . MRP_NEWLINE;
// /* dbg */ echo " closestper rec: ". date (MRP_DATE_FORMAT, $recurrence_start). "-" .date (MRP_DATE_FORMAT, ($recurrence_end)) . " | resp to: " .date (MRP_DATE_FORMAT, ($time)) . MRP_NEWLINE;
// /* dbg */ }
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------

				while ($recurrence_start > $time)
				{
					$recurrence_start -= $recurrence["interval"];
					$recurrence_end -= $recurrence["interval"];
				}

				while ($recurrence_end <= $time)
				{
					$recurrence_start += $recurrence["interval"];
					$recurrence_end += $recurrence["interval"];
				}

				if (($recurrence_end > $recurrence["start"]) and ($recurrence_start < $recurrence["end"]) and (!isset($closest_periods[$recurrence_start]) or $recurrence_end > $closest_periods[$recurrence_start]))
				{
					$closest_periods[$recurrence_start] = $recurrence_end;
				}
			}
		}

// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* dbg */ if ($this->mrpdbg){
// /* dbg */ echo "closest_periods before:";
// /* dbg */ arr ($closest_periods);
// /* dbg */ }
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------

		### add separate unavailable periods
		foreach ($this->resource_data[$resource_id]["unavailable_periods"] as $period_start => $period_end)
		{
			if ($period_end > ifset($closest_periods, $period_start))
			{
				$closest_periods[$period_start] = $period_end;
			}
		}

// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* dbg */ if ($this->mrpdbg){
// /* dbg */ echo "closest_periods after:";
// /* dbg */ arr ($closest_periods);
// /* dbg */ }
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------

		$start = $end = NULL;

		### combine buffer, recurrence & period
		if (!empty ($closest_periods))
		{
// /* timing */ timing ("find_combined_range", "start");

			ksort ($closest_periods, SORT_NUMERIC);

			if (end ($closest_periods) > $time)
			{
// /* timing */ timing ("find_combined_range - combine_ranges", "start");

				$prev_end = NULL;
				$combined_ranges = array ();

				foreach ($closest_periods as $range_start => $range_end)
				{
					if (($range_start <= $prev_end) and isset ($prev_end))
					{
						if ($range_end > $prev_end)
						{
							$prev_end = $range_end;
						}

						$combined_ranges[$prev_start] = $prev_end;
					}
					else
					{
						$combined_ranges[$range_start] = $range_end;
						$prev_start = $range_start;
						$prev_end = $range_end;
					}
				}

				$closest_periods = $combined_ranges;

// /* timing */ timing ("find_combined_range - combine_ranges", "end");

				foreach ($closest_periods as $range_start => $range_end)
				{
					if ($range_end > $time)
					{
						$start = $range_start;
						$end = $range_end;
						break;
					}
				}
			}

			if ($start >= $end)
			{
				$start = $end = NULL;
			}

// /* timing */ timing ("find_combined_range", "end");
		}

// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* dbg */ if ($this->mrpdbg){
// /* dbg */ echo "combined unavail: t - ".date (MRP_DATE_FORMAT, $time)." [".date (MRP_DATE_FORMAT, $start)." - ".date (MRP_DATE_FORMAT, $end) . "]<br>";
// /* dbg */ }
// /* dbg */ //-------------------------------------------------------------------------------------------------------------------------------------------
// /* timing */ timing ("_get_closest_unavailable_period", "end");

		if (isset ($start) and isset ($end))
		{
			return array ($start, $end);
		}
	}

	protected function init_resource_data ($resources)
	{
		foreach ($resources as $resource_id)
		{
			$resource = new object($resource_id);
			$thread_data = $resource->prop ("thread_data");
			$threads = count ($thread_data) ? count ($thread_data) : 1;

			$this->resource_data[$resource_id]["global_buffer"] = $resource->prop ("global_buffer");
			$this->resource_data[$resource_id]["threads"] = $threads;
			$i = $resource->instance ();
			$this->resource_data[$resource_id]["unavailable_periods"] = $i->get_unavailable_periods ($resource, $this->schedule_start, ($this->schedule_start + $this->schedule_length));
			$this->resource_data[$resource_id]["recurrence_definitions"] = $i->get_recurrent_unavailable_periods ($resource, $this->schedule_start, ($this->schedule_start + $this->schedule_length));
		}
	}

	// @param mrp_resource required type=int
	// @param mrp_start required type=int
	// @param mrp_length required type=int
	public function get_unavailable_periods_for_range ($arr)
	{
		$resource_id = $arr["mrp_resource"];
		$resource = obj ($resource_id);
		$workspace = $resource->prop("workspace");

		if (!$this->initialized)
		{
			$this->scheduling_time = time ();
			$this->schedule_start = $arr["mrp_start"];
			$this->schedule_length = $arr["mrp_length"];
			$this->scheduling_day_end = mktime (23, 59, 59, date ("m", $this->scheduling_time), date ("d", $this->scheduling_time), date("Y", $this->scheduling_time));

			$resources_folder = $workspace->prop ("resources_folder");
			$resource_tree = new object_tree (array (
				"parent" => $resources_folder,
				"class_id" => array (CL_MRP_RESOURCE,CL_MENU),
			));

			$resource_list = $resource_tree->to_list ();
			$resources = array();
			foreach($resource_list->arr() as $resource)
			{
				if ($resource->class_id() == CL_MRP_RESOURCE && $resource->prop("type") != mrp_resource_obj::TYPE_NOT_SCHEDULABLE)
				{
					$resources[] = $resource->id();
				}
			}
			$this->init_resource_data ($resources);
			$this->initialized = true;
		}

		$this->unavailable_times = array();
		$pointer = 0;
		$i_dbg = 0;

		while ($pointer <= $this->schedule_length)
		{
			list ($unavailable_start, $unavailable_length) = $this->get_closest_unavailable_period ($resource_id, $pointer);

			if ($unavailable_length <= 0)
			{
				return $this->unavailable_times;
			}

			$pointer = $unavailable_start + $unavailable_length + 1;
			$unavailable_start = $this->schedule_start + $unavailable_start;
			$unavailable_end = $unavailable_start + $unavailable_length;

			if (!isset($this->unavailable_times[$unavailable_start]) or $unavailable_end > $this->unavailable_times[$unavailable_start])
			{
				$this->unavailable_times[$unavailable_start] = $unavailable_end;
			}

			if (5000 == $i_dbg++)
			{
				// error::raise(array(
					// "msg" => sprintf (t("Search for unavailable times for range exceeded reasonable limit (%s) of cycles. Resource %s"), $i_dbg3, $resource_id),
					// "fatal" => false,
					// "show" => false,
				// ));
				echo "viga@" . __LINE__ . MRP_NEWLINE;
				break;
			}
		}

		return $this->unavailable_times;
	}

	public static function safe_settype_float ($value) // DEPRECATED
	{ return aw_math_calc::string2float($value); }
}

function timing ($name = NULL, $action = "show")
{
	if (1 == $_GET["show_timings"])
	{
		static $timings = array ();
		list ($msec, $sec) = explode (" ", microtime ());
		$time = ((float) $msec + (float) $sec);

		switch ($action)
		{
			case "start":
				$timings[$name]["start"] = $time;
				return;

			case "end":
				if ($timings[$name]["start"])
				{
					$tmp = ($time - $timings[$name]["start"]);
					$timings[$name]["sum"] += $tmp;
					$timings[$name]["count"]++;

					if ($tmp > $timings[$name]["max"])
					{
						$timings[$name]["max"] = $tmp;
					}

					$timings[$name]["start"] = 0;
				}
				return;

			case "show":
				ksort ($timings);
				echo "<table width='95%' border='1' cellspacing='0' cellpadding='2' style='font-size: 10px;'>\n";
				echo "<tr><td><b>Name</b> <td><b>Average</b> <td><b>Max</b> <td><b>Count</b> <td><b>Total</b>\n";

				foreach ($timings as $name => $timing)
				{
					$avg = ($timing["sum"] / $timing["count"]);
					echo "<tr><td>{$name}<td>{$avg}<td>{$timing["max"]}<td>{$timing["count"]}<td>{$timing["sum"]}\n";
				}

				echo "</table>\n";
				return;
		}
	}
}

function mrp_schedule_reserved_times_sorter($a, $b)
{
	if ($a[0] === $b[0])
	{
		throw new aw_exception("Same starttime for two jobs");
	}
	return ($a[0] < $b[0]) ? -1 : 1;
}

/** Generic schedule exception **/
class awex_mrp_schedule extends awex_mrp {}

/** Workspace error **/
class awex_mrp_schedule_workspace extends awex_mrp_schedule {}

/** Purchase manager error **/
class awex_mrp_schedule_purchasemgr extends awex_mrp_schedule {}

/** Schedule data save error **/
class awex_mrp_schedule_save extends awex_mrp_schedule {}

/** Scheduling semaphore lock failure **/
class awex_mrp_schedule_lock extends awex_mrp_schedule {}
