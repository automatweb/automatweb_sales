<?php

require_once "mrp_header.aw";

class mrp_case_obj extends _int_object implements crm_sales_price_component_interface, crm_offer_row_interface
{
	const CLID = 828;
	const STATE_NEW = 1; // project hasn't been planned yet
	const STATE_PLANNED = 2; // start time has been planned. project is in schedule
	const STATE_ABORTED = 4; // work was started but then stopped with no knowledge if resumed in future
	const STATE_DONE = 5; // project is completed
	const STATE_LOCKED = 6; //!!!
	const STATE_ONHOLD = 9; //!!! project is not scheduled but ...?
	const STATE_ARCHIVED = 10; // project is done and archived, no active operations performed with project
	const STATE_VIRTUAL_PLANNED = 11; // project is scheduled for assessment purposes but no real operations can be performed (starting, ...)
	const STATE_DELETED = 8; // project is deleted
	const STATE_INPROGRESS = 3; // work is being done

	protected static $mrp_state_names = array(); // array of state => human_readable_name
	protected $workspace; // project owner

	//	Written solely for testing purposes!
	public function get_units()
	{
		$ol = new object_list(array(
			"class_id" => CL_UNIT,
		));
		return $ol;
	}

	/** Class constructor
		@attrib api=1 params=pos
	**/
	function __construct($objdata)
	{
		parent::__construct($objdata);

		$new = (!is_oid($this->id()));
		if ($new)
		{
			### set status
			$this->set_prop ("state", self::STATE_NEW);
		}
	}

	/**
	@attrib api=1 params=pos
	@param state optional type=int
		State for which to get name. One of STATE constant values.
	@comment
	@returns mixed
		Array of constant values (keys) and names (array values) if $state parameter not specified. String name corresponding to that state if $state parameter given. Names are in currently active language. Empty string if invalid state parameter given.
	**/
	public static function get_state_names($state = null)
	{
		if (empty(self::$mrp_state_names))
		{
			self::$mrp_state_names = array(
				self::STATE_NEW => t("Uus"),
				self::STATE_PLANNED => t("Planeeritud"),
				self::STATE_INPROGRESS => t("T&ouml;&ouml;s"),
				self::STATE_ABORTED => t("Katkestatud"),
				self::STATE_DONE => t("Valmis"),
				self::STATE_LOCKED => t("Lukustatud"),
				self::STATE_ONHOLD => t("Ootel"),
				self::STATE_ARCHIVED => t("Arhiveeritud"),
				self::STATE_VIRTUAL_PLANNED => t("Virtuaalselt planeeritud"),
				self::STATE_DELETED => t("Kustutatud")
			);
		}

		if (!isset($state))
		{
			$names = self::$mrp_state_names;
		}
		elseif (is_scalar($state) and isset(self::$mrp_state_names[$state]))
		{
			$names = self::$mrp_state_names[$state];
		}
		else
		{
			$names = "";
		}

		return $names;
	}

	public function awobj_get_project_priority()
	{
		return aw_math_calc::string2float(parent::prop("project_priority"));
	}

	public function awobj_set_trykiarv($value)
	{
		//!!! selle muutmine peab vist vaatama t88d l2bi ja kui on v2hem ekspemlare tehtud kui uus v22rtus siis panema nende staatused 'not done' lisaks, kui t88d on tehtud siis ei saa trykiarvu v2hendada, kui projekt on arhiveeritud (v6i ka valmis?) siis ei saa trykiarvu enam muuta
		return parent::set_prop("trykiarv", $value);
	}

	/**	Create customer data object for workspace owner and project customer if no customer data object for those two exists.
		@attrib api=1 params=pos

		@param oid required type=int acl=view
	**/
	public function awobj_set_customer($oid)
	{
		try
		{
			$ws = $this->awobj_get_workspace();
			if (is_oid($ws->prop("owner")))
			{
				$seller = $ws->prop("owner");
				// Make customer data object if doesn't exist.
				obj($oid)->find_customer_relation(obj($seller), true);
			}
		}
		catch(awex_mrp_case_workspace $E)
		{
			// Damnit!
		}
		return parent::set_prop("customer", $oid);
	}

/**
	@attrib params=pos api=1
	@param workspace type=CL_MRP_WORKSPACE
	@returns starndard object set_prop return
	@errors
		throws awex_mrp_case_type when workspace parameter is not a workspace object
**/
	public function awobj_set_workspace(object $workspace)
	{
		if (!$workspace->is_a(CL_MRP_WORKSPACE))
		{
			throw new awex_mrp_case_type("Workspace not a mrp_workspace object");
		}

		$this->workspace = $workspace;
		return parent::set_prop("workspace", $workspace->id());
	}

	public function awobj_set_state($value)
	{
//		throw new awex_obj_readonly("State is a read-only property");
	}

/**
	@attrib params=pos api=1
	@returns CL_MRP_WORKSPACE
	@errors
		throws awex_mrp_case_workspace when workspace couldn't be loaded
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
					$not_new = (null !== $this->id());
					if ($not_new)
					{
						// try backward compatibility
						$workspace = $this->get_first_obj_by_reltype("RELTYPE_MRP_OWNER");

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
							throw new awex_mrp_case_workspace("Workspace not defined. Stored value: " . var_export($workspace, true));
						}
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
				$e = new awex_mrp_case_workspace("Workspace not defined. Stored value: " . var_export($workspace, true));
				$e->set_forwarded_exception($E);
				throw $e;
			}
			$this->workspace = $workspace;
		}
		return $this->workspace;
	}

	public function awobj_set_order_quantity($value)
	{
		//!!! selle muutmine peab vist vaatama t88d l2bi ja kui on v2hem ekspemlare tehtud kui uus v22rtus siis panema nende staatused 'not done' lisaks, kui t88d on tehtud siis ei saa trykiarvu v2hendada, kui projekt on arhiveeritud (v6i ka valmis?) siis ei saa trykiarvu enam muuta
		settype($value, "int");
		if ($value < 1)
		{
			throw new awex_mrp_case_type("order_quantity can't be 0 or negative.");
		}
		return parent::set_prop("order_quantity", $value);
	}

/**
	@attrib name=get_job_count params=pos api=1
	@returns int
		Number of jobs
**/
	public function get_job_count($state = array(), $resource =  array())
	{
		$params = array ("type" => "RELTYPE_MRP_PROJECT_JOB");
/* juhuks kui see connectionit otside prop param kunagi teostatakse
		if (count($resource))
		{
			$params["to.resource"] = $resource;
		}

 */
		$connections = $this->connections_from ($params);
		return count ($connections);
	}

	/**
		@attrib api=1 params=pos
		@returns array
			Associative array of all case jobs: object id => job object
	**/
	public function get_job_list()
	{
		$ol = new object_list($this->connections_from(array ("type" => "RELTYPE_MRP_PROJECT_JOB", "class_id" => CL_MRP_JOB)));
		return $ol->arr();
	}

	/**
		@attrib api=1
	**/
	public function get_job_names()
	{
		$ol = new object_list($this->connections_from(array ("type" => "RELTYPE_MRP_PROJECT_JOB", "class_id" => CL_MRP_JOB)));
		return $ol->names();
	}

	/** Creates a new job in this project
		@attrib api=1 params=pos
		@param resource type=CL_MRP_RESOURCE default=null
		@returns CL_MRP_JOB
			Created job object
	**/
	public function add_job(object $resource = null)
	{
		$workspace = $this->awobj_get_workspace();

		if ($resource)
		{
			$resource_name = $resource->name();
			$constructor_args = array("resource" => $resource);
		}
		else
		{
			$resource_name = t("ressurss m&auml;&auml;ramata");
			$constructor_args = array();
		}

		if (!($jobs_folder = $workspace->prop ("jobs_folder")))
		{
			throw new awex_mrp_case_workspace("Workspace has no jobs folder");
		}

		// get job number
		$jobs = new object_data_list(
			array(
				"class_id" => CL_MRP_JOB,
				"project" => $this->id()
			),
			array(
				CL_MRP_JOB => array("jrk"),
			)
		);
		$job_nr = 0;
		foreach ($jobs->arr() as $job)
		{
			$job_nr = max($job_nr, $job["jrk"]);
		}
		++$job_nr;

		// create job object
		$job = new object (array (
		   "parent" => $jobs_folder,
		   "class_id" => CL_MRP_JOB
		), $constructor_args);
		$job->set_prop ("exec_order", $job_nr);
		$job->set_prop ("project", $this->id ());
		$job->set_ord($job_nr);
		$job->save ();

		$this->connect (array (
			"to" => $job,
			"reltype" => "RELTYPE_MRP_PROJECT_JOB"
		));

		return $job;
	}

	public function save($exclusive = false, $previous_state = null)
	{
		$new = (null === $this->id());
		if ($new)
		{ //
			$workspace = $this->awobj_get_workspace();
			$projects_folder = $workspace->prop ("projects_folder");
			$this->set_parent ($projects_folder);
		}

		// order_quantity must be set
		if ($this->prop("order_quantity") < 1)
		{
			if (($this->prop("trykiarv") > 1))
			{
				$this->awobj_set_order_quantity($this->prop("trykiarv"));
			}
			else
			{
				$this->awobj_set_order_quantity(1);
			}
		}

		$r = parent::save($exclusive, $previous_state);

		if ($new)
		{
			$case_id = $this->id();
			$this->instance()->db_query("
				INSERT INTO
					mrp_log(
						project_id,job_id,uid,tm,message
					)
					values(
						{$case_id},NULL,'".aw_global_get("uid")."',".time().",'Projekt lisati'
					)
			");
		}

		return $r;
	}

	function delete ($full_delete = false)
	{
		$this->set_prop ("state", self::STATE_DELETED);
		$this->save ();

		### delete project's jobs
		$connections = $this->connections_from (array ("type" => "RELTYPE_MRP_PROJECT_JOB"));

		foreach ($connections as $connection)
		{
			$job = $connection->to ();
			$job->delete ();
		}

		$applicable_planning_states = array(
			self::STATE_INPROGRESS,
			self::STATE_PLANNED
		);

		if (in_array ($this->prop ("state"), $applicable_planning_states))
		{
			### post rescheduling msg
			$workspace = $this->awobj_get_workspace();
			$workspace->request_rescheduling();
		}

		return parent::delete($full_delete);
	}

/** Inserts project to schedule or reschedules it
    @attrib api=1 params=pos
	@param scheduled_date type=int
		UNIX timestamp scheduled finishng time
	@returns void
	@errors
		throws awex_mrp_case_state when current job state doesn't allow planning.
		throws awex_mrp_case on errors.
**/
	function schedule($scheduled_date)
	{
		$applicable_states = array(
			self::STATE_NEW,
			self::STATE_PLANNED
		);
		if (!in_array ($this->prop("state"), $applicable_states))
		{
			throw new awex_mrp_case_state("State must be 'NEW' or 'PLANNED'.");
		}

		try
		{
			$this->set_prop ("planned_date", $scheduled_date);
			$log = false;
			if ($this->prop("state") != self::STATE_PLANNED)
			{
				$this->set_prop ("state", self::STATE_PLANNED);
				// $this->set_prop ("first_planned", time ());
				$log = true;
			}
			$this->save ();

			if ($log)
			{
				$ws = get_instance (CL_MRP_WORKSPACE);
				$ws->mrp_log($this->id(), NULL, "Projekt planeeriti");
			}
		}
		catch (Exception $E)
		{
			$error_message = "Unknown error (" . get_class($e) . "): " . $e->getMessage();
			$e = new awex_mrp_case($error_message);
			$e->set_forwarded_exception($E);
			throw $e;
		}
	}

/** Starts the project. Project must be planned.
    @attrib api=1 params=pos
	@returns void
	@errors
		throws awex_mrp_case_state when current project state doesn't allow starting
**/
	function start ()
	{
		### states for starting a project
		$applicable_states = array (
			self::STATE_PLANNED
		);

		if (!in_array ($this->prop ("state"), $applicable_states))
		{
			throw new awex_mrp_case_state("State for starting a project must be 'PLANNED'");
		}

		### start project
		$this->set_prop ("state", self::STATE_INPROGRESS);
		$this->set_prop ("started", time ());
		$this->save ();

		### log change
		$ws = get_instance (CL_MRP_WORKSPACE);
		$ws->mrp_log ($this->id (), NULL, "Projekt l&auml;ks t&ouml;&ouml;sse");
	}

/** Updates project to job state changes.
    @attrib api=1 params=pos
	@param job type=CL_MRP_JOB
	@returns void
	@errors
		throws awex_mrp_case_state when current project state doesn't allow this state change
		throws awex_mrp_case on other errors
**/
	public function update_progress(object $job)
	{
		$req_project_states_by_job_state = array (
			mrp_job_obj::STATE_INPROGRESS => array(self::STATE_INPROGRESS, self::STATE_PLANNED),
			mrp_job_obj::STATE_ABORTED => array(self::STATE_INPROGRESS, self::STATE_ONHOLD),
			mrp_job_obj::STATE_DONE => array(self::STATE_INPROGRESS, self::STATE_ONHOLD),
			mrp_job_obj::STATE_PAUSED => array(self::STATE_INPROGRESS),
		);

		try
		{
			$state = $this->prop ("state");
			$job_state = $job->prop("state");
			if (!in_array ($state, $req_project_states_by_job_state[$job_state]))
			{
				$job_state_name = mrp_job_obj::get_state_names($job_state);
				$case_state_name = self::get_state_names($state);
				throw new awex_mrp_case_state("Project is not ready for this job. Job state '{$job_state_name}', case state '{$case_state_name}'");
			}

			switch ($job_state)
			{
				case mrp_job_obj::STATE_INPROGRESS: // job was started or resumed from pause or abort
					if (self::STATE_PLANNED == $state and $this->prop("started") < 1) // started
					{
						$prev_progress = $this->prop("progress");
						$this->set_prop ("progress", time() + $job->prop ("planned_length"));
						$this->start();
						return;
					}
					else // resumed
					{
						if (self::STATE_PLANNED == $state)
						{
							$this->set_prop ("state", self::STATE_INPROGRESS);
						}
						$progress = max ($this->prop ("progress"), time ());
					}
					$this->set_prop ("progress", $progress);
					break;

				case mrp_job_obj::STATE_PAUSED: // job was paused
					$progress = max ($this->prop ("progress"), time ());
					$this->set_prop ("progress", $progress);
					break;

				case mrp_job_obj::STATE_DONE: // job was finished
					$this->set_prop ("progress", time());

					### finish project if this was the last job
					$list = new object_list (array (
						"class_id" => CL_MRP_JOB,
						"project" => $this->id (),
						"state" => mrp_job_obj::STATE_DONE
					));
					$done_jobs = $list->count ();

					$list = new object_list (array (
						"class_id" => CL_MRP_JOB,
						"project" => $this->id ()
					));
					$all_jobs = $list->count ();

					if ($done_jobs === $all_jobs)
					{
						### finish project
						$this->finish();
						return;
					}
					break;

				default: // job operation doesn't require any changes in project
					return;
			}

			$this->save();
		}
		catch (awex_mrp_case_state $e)
		{
			throw $e;
		}
		catch (Exception $E)
		{
			if (isset($prev_progress))
			{
				$this->set_prop("progress", $prev_progress);
			}
			$e = new awex_mrp_case("Unknown error: " . $E->getMessage());
			$e->set_forwarded_exception($E);
			throw $e;
		}
	}

/** Finishes the project. Project must be in progress.
    @attrib api=1 params=pos
	@returns void
	@errors
		throws awex_mrp_case_state when current project state doesn't allow finishing
		throws awex_mrp_case_not_completed when some jobs are not done yet
**/
	function finish ()
	{
		### check if all jobs are done
		$job_list = new object_list (array (
			"class_id" => CL_MRP_JOB,
			"project" => $this->id (),
		));
		$all_jobs = $job_list->count ();

		### states for jobs that allow finishing a project
		$applicable_states = array (
			mrp_job_obj::STATE_DONE,
			mrp_job_obj::STATE_ABORTED
		);
		$job_list = new object_list (array (
			"class_id" => CL_MRP_JOB,
			"state" => $applicable_states,
			"project" => $this->id()
		));
		$done_jobs = $job_list->count ();

		if ($done_jobs !== $all_jobs)
		{
			throw new awex_mrp_case_not_completed("Some jobs are not done.");
		}

		### states for finishing a project
		$applicable_states = array (
			self::STATE_INPROGRESS
		);

		if (!in_array ($this->prop ("state"), $applicable_states))
		{
			throw new awex_mrp_case_state("State for finishing a project must be 'INPROGRESS'");
		}

		### finish project
		$this->set_prop("finished", time());
		$this->set_prop ("state", self::STATE_DONE);
		$this->save ();

		### log event
		$ws = get_instance(CL_MRP_WORKSPACE);
		$ws->mrp_log($this->id(), NULL, "Projekt l&otilde;petati");
	}

/** Aborts the project. Project must be in progress.
    @attrib api=1 params=pos
	@returns void
	@errors
		throws awex_mrp_case_state when current project isn't in progress
**/
	function abort ()
	{
		### states for aborting a project
		$applicable_states = array (
			self::STATE_INPROGRESS
		);

		if (!in_array ($this->prop ("state"), $applicable_states))
		{
			throw new awex_mrp_case_state("State must be 'INPROGRESS'");
		}

		### abort project
		$this->set_prop ("state", self::STATE_ABORTED);
		$this->save ();

		### post rescheduling msg
		$workspace = $this->awobj_get_workspace();
		$workspace->request_rescheduling();

		### log event
		$ws = get_instance(CL_MRP_WORKSPACE);
		$ws->mrp_log ($this->id (), NULL, "Projekt katkestati");
	}

/** Archives the project. Project must be done.
    @attrib api=1 params=pos
	@returns void
	@errors
		throws awex_mrp_case_state when current project state doesn't allow archiving
**/
	function archive ()
	{
		### states for archiving a project
		$applicable_states = array(
			self::STATE_DONE
		);

		if (!in_array ($this->prop ("state"), $applicable_states))
		{
			throw new awex_mrp_case_state("State must be 'DONE'");
		}

		### archive project
		$this->set_prop("archived", time());
		$this->set_prop("state", self::STATE_ARCHIVED);
		$this->save();

		### log event
		$ws = get_instance(CL_MRP_WORKSPACE);
		$ws->mrp_log(
			$this->id(),
			NULL,
			"Projekt arhiveeriti"
		);
	}

/** Inserts the project to production schedule. Project must be new, aborted or on hold.
    @attrib api=1 params=pos
	@returns void
	@errors
		throws awex_mrp_case_state when current project state doesn't allow planning
**/
	function plan ()
	{
		### states for planning a project
		$applicable_states = array(
			self::STATE_NEW,
			self::STATE_ABORTED,
			self::STATE_ONHOLD
		);

		if (!in_array ($this->prop ("state"), $applicable_states))
		{
			throw new awex_mrp_case_state("State must be 'NEW', 'ABORTED' or 'ONHOLD'");
		}

		### plan project
		$this->set_prop("state", self::STATE_PLANNED);
		$this->save();

		### post rescheduling msg
		$workspace = $this->awobj_get_workspace();
		$workspace->request_rescheduling();

		### log event
		$ws = get_instance(CL_MRP_WORKSPACE);
		$ws->mrp_log($this->id(), NULL, "Projekt sisestati planeerimisse");
	}

/** Sets the project on hold. Project won't be scheduled but remains active. Project must be planned or in progress.
    @attrib api=1 params=pos
	@returns void
	@errors
		throws awex_mrp_case_state when current project state doesn't allow setting on hold.
**/
	function set_on_hold ()
	{
		### states for taking a project out of schedule
		$applicable_states = array(
			self::STATE_INPROGRESS,
			self::STATE_PLANNED
		);

		if (!in_array ($this->prop ("state"), $applicable_states))
		{
			throw new awex_mrp_case_state("State must be 'INPROGRESS' or 'PLANNED'. Current: " . $this->prop ("state"));
		}

		### set project on hold
		$this->set_prop("state", self::STATE_ONHOLD);
		$this->save();

		### post rescheduling msg
		$workspace = $this->awobj_get_workspace();
		$workspace->request_rescheduling();

		### log event
		$ws = get_instance(CL_MRP_WORKSPACE);
		$ws->mrp_log($this->id(), NULL, "Projekt v&otilde;eti planeerimisest v&auml;lja");
	}

	/**
		@attrib name=save_materials_without_job api=1 params=pos

		@param data required type=data
			The structure of data:
				$data = array(
					[The OID of the shop product object] => array(
						[product] => [The OID of the shop product object],
						[amount] => 1000,
						[planning] => 0,
						[movement] => 0,
						[unit] => [The OID of the unit object],
					)
				)
	**/
	public function save_materials_without_job($arr)
	{
		$ol = $this->get_material_expenses_without_job();

		foreach($ol->arr() as $o)
		{
			$prod = $o->prop("product");
			if(isset($arr[$prod]) && $arr[$prod]["amount"] > 0)
			{
				$o->set_prop("amount", $arr[$prod]["amount"]);
				$o->set_prop("planning", $arr[$prod]["planning"]);
				$o->set_prop("movement", $arr[$prod]["movement"]);
				$o->set_prop("unit", $arr[$prod]["unit"]);
				$o->save();
			}
			else
			{
				$o->delete();
			}

			if(isset($arr[$prod]))
			{
				unset($arr[$prod]);
			}
		}

		foreach($arr as $oid => $exp)
		{
			$product = obj($oid);
			$o = obj();
			$o->set_class_id(CL_MATERIAL_EXPENSE);
			$o->set_parent($this->id());
			$o->set_name(sprintf(t("%s kulu projekti %s jaoks"), $product->name(), $this->name()));
			$o->set_prop("case", $this->id());
			$o->set_prop("amount", $exp["amount"]);
			$o->set_prop("planning", $exp["planning"]);
			$o->set_prop("movement", $exp["movement"]);
			$o->set_prop("unit", $exp["unit"]);
			$o->set_prop("product", $exp["product"]);
			$o->save();
		}
	}

	/**
		@attrib name=get_material_expenses_without_job params=name

		@param id required type=int/array

		@param odl optional type=bool default=false

	**/
	public function get_material_expenses_without_job($arr = array())
	{
		$prms = array(
			"class_id" => CL_MATERIAL_EXPENSE,
			"lang_id" => array(),
			"site_id" => array(),
 			"case" => $this->id(),
			"job" => new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, 0),
 			"product" => new obj_predicate_compare(OBJ_COMP_GREATER, 0),
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
					CL_MATERIAL_EXPENSE => array("product", "product.name" => "product_name", "amount", "unit", "planning", "movement", "job", "job.state")
				)
			);
		}
	}
}

/** Generic mrp_case exception **/
class awex_mrp_case extends awex_mrp {}

/** Project is expected to be completed but isn't **/
class awex_mrp_case_not_completed extends awex_mrp_case {}

/** Type mismatch error **/
class awex_mrp_case_type extends awex_mrp_case {}

/** Project state doesn't allow attempted operation **/
class awex_mrp_case_state extends awex_mrp_case {}

/** Workspace not defined or invalid **/
class awex_mrp_case_workspace extends awex_mrp_case {}
