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
	
	const ORDER_STATE_DRAFT = 1;
	const ORDER_STATE_READY = 2;
	const ORDER_STATE_SENT = 3;
	const ORDER_STATE_CONFIRMED = 4;
	const ORDER_STATE_CANCELLED = 5;

	protected static $mrp_state_names = array(); // array of state => human_readable_name
	protected static $mrp_order_state_names = array(); // array of state => human_readable_name
	protected $workspace; // project owner

	//	Written solely for testing purposes!
	public function get_units()
	{
		$ol = new object_list(array(
			"class_id" => CL_UNIT,
			"status" => object::STAT_ACTIVE,
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

	/**
	@attrib api=1 params=pos
	@param state optional type=int
		State for which to get name. One of mrp_case_obj::ORDER_STATE_ constant values.
	@comment
	@returns mixed
		Array of constant values (keys) and names (array values) if $state parameter not specified. String name corresponding to that status if $state parameter given. Names are in currently active language. Empty string if invalid state parameter given.
	**/
	public static function get_order_state_names($state = null)
	{
		if (empty(self::$mrp_order_state_names))
		{
			self::$mrp_order_state_names = array(
				self::ORDER_STATE_DRAFT => t("Koostamisel"),
				self::ORDER_STATE_READY => t("Koostatud"),
				self::ORDER_STATE_SENT => t("Saadetud"),
				self::ORDER_STATE_CONFIRMED => t("Kinnitatud"),
				self::ORDER_STATE_CANCELLED => t("Tühistatud"),
			);
		}

		if (!isset($state))
		{
			$names = self::$mrp_order_state_names;
		}
		elseif (is_scalar($state) and isset(self::$mrp_order_state_names[$state]))
		{
			$names = self::$mrp_order_state_names[$state];
		}
		else
		{
			$names = "";
		}

		return $names;
	}
	
	public function awobj_get_order_state()
	{
		$state = $this->prop("order_state");
		if (empty($state))
		{
			return self::ORDER_STATE_DRAFT;
		}
		return $state;
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
	
	public function awobj_get_seller()
	{
		if (is_oid($this->prop("customer_relation")))
		{
			$customer_relation = obj($this->prop("customer_relation"), null, crm_company_customer_data_obj::CLID);
			$this->set_prop("seller", $customer_relation->seller);
			
			return $customer_relation->seller;
		}

		return parent::prop("seller");
	}
	
	public function awobj_get_customer()
	{
		if (is_oid($this->prop("customer_relation")))
		{
			$customer_relation = obj($this->prop("customer_relation"), null, crm_company_customer_data_obj::CLID);
			$this->set_prop("customer", $customer_relation->buyer);
			
			return $customer_relation->buyer;
		}

		return null;
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

	public function save($check_state = false)
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

		if (is_oid($seller_id = parent::prop("seller")) && is_oid($buyer_id = parent::prop("customer")))
		{
			try
			{
				$buyer = obj($buyer_id);
				$customer_relation = $buyer->find_customer_relation(obj($seller_id, null, crm_company_obj::CLID));
				if ($customer_relation !== null)
				{
					$this->set_prop("customer_relation", $customer_relation->id());
				}
			}
			catch (Exception $e)
			{
				$this->set_prop("customer_relation", 0);
			}
		}
		else
		{
			$this->set_prop("customer_relation", 0);
		}

		$r = parent::save($check_state);

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

	/** Returns order customer contact person name as it is set in this order
		@attrib api=1
		@returns string
	**/
	public function get_customer_contact_person_name()
	{
		$contact_person_name = $this->prop("customer_relation.RELTYPE_BILL_PERSON.name") or
		$contact_person_name = $this->prop("customer_relation.buyer_contact_person.name") or
		$contact_person_name = $this->prop("customer_relation.buyer_contact_person2.name") or
		$contact_person_name = $this->prop("customer_relation.buyer_contact_person3.name") or
		$contact_person_name = "";
		
		if($contact_person_name === "" and $this->prop("customer_relation.buyer.class_id") === crm_company_obj::CLID)
		{
			$contact_person_name = $this->prop("customer_relation.buyer.firmajuht.name");
		}
		elseif ($contact_person_name === "" and $this->prop("customer_relation.buyer.class_id") === crm_person_obj::CLID)
		{
			$contact_person_name = $this->prop("customer_relation.buyer.name");
		}

		return $contact_person_name;
	}

	/** Returns order customer contact person object
		@attrib api=1
		@returns CL_CRM_PERSON/NULL
			returns NULL if contact person not found
	**/
	public function get_contact_person()
	{
		if(object_loader::can("" , $this->prop("customer_relation")))
		{
			$crel = obj($this->prop("customer_relation"));
			$contact_person = $crel->get_first_obj_by_reltype("RELTYPE_BILL_PERSON");

			if (!$contact_person)
			{
				try
				{
					if ($crel->prop("buyer_contact_person"))
					{
						$contact_person = new object((int) $crel->prop("buyer_contact_person"));
					}
					elseif ($crel->prop("buyer_contact_person2"))
					{
						$contact_person = new object((int) $crel->prop("buyer_contact_person2"));
					}
					elseif ($crel->prop("buyer_contact_person3"))
					{
						$contact_person = new object((int) $crel->prop("buyer_contact_person3"));
					}
				}
				catch (Exception $e)
				{
					$contact_person = null;
				}
			}
		}

		if(!$contact_person)
		{
			if ($this->prop("customer_relation.buyer.class_id") === crm_company_obj::CLID and $this->prop("customer_relation.buyer.firmajuht"))
			{
				try
				{
					$contact_person = new object($this->prop("customer_relation.buyer.firmajuht"));
				}
				catch (Exception $e)
				{
					$contact_person = null;
				}
			}
			elseif ($this->prop("customer_relation.buyer.class_id") === crm_person_obj::CLID)
			{
				$contact_person = new object($this->prop("customer_relation.buyer"));
			}
		}

		return $contact_person;
	}
	
	public function get_currency()
	{
		// FIXME!
		return new object(378726);
	}

	public function get_mail_from_default()
	{
		$u = obj(aw_global_get("uid_oid"));
		return $u->get_user_mail_address();
	}

	public function get_mail_from_name_default()
	{
		$person_oid = user::get_current_person();

		if($person_oid)
		{
			try
			{
				$person = obj($person_oid, array(), CL_CRM_PERSON);
				return $person->name();
			}
			catch (Exception $e)
			{
			}
		}

		return null;
	}
	
	/**
		@attrib api=1 params=pos obj_save=1
		@param create type=bool default=TRUE
			Create or get from cache if found
		@returns CL_FILE
		@errors
	**/
	function get_order_pdf($create = true)
	{
		$lang_id = /*$this->prop("language.aw_lang_id") ? $this->prop("language.aw_lang_id") : */languages::LC_EST;
		$pdf = null;
		if (object_loader::can("", $this->meta("send_mail_attachments_order_pdf_oid")))
		{
			try
			{
				$pdf = obj($this->meta("send_mail_attachments_order_pdf_oid"), array(), CL_FILE);
			}
			catch (awex_obj $e)
			{
			}
		}

		if ($create and !$pdf)
		{
			// get html content
			$inst = new mrp_case();
			$content = $inst->show(array(
				"id" => $this->id(),
				"pdf" => 1,
				"return" => 1
			));

			// create file
			$f = new file();
			$id = $f->create_file_from_string(array(
				"parent" => $this->id(),
				"content" => $content,
				"name" => sprintf(t("tellimus_nr_%s", $lang_id), $this->prop("name")) . ".pdf",
				"type" => "application/pdf"
			));
			$this->set_meta("send_mail_attachments_order_pdf_oid", $id);
			$this->clear_pdf_cache = false;
			$this->save();
			$pdf = obj($id, array(), CL_FILE);
		}

		return $pdf;
	}

	/** Returns order email recipients data
		@attrib api=1 params=pos
		@param type type=array default=array()
			Type(s) of recipients to return. Empty/default means all.
			Valid options for array elements:
				'user' -- order creator and current user
				'default' -- crm default order recipients
				'customer_general' -- general customer email contacts
				'custom' -- user defined custom recipients
		@returns array
			Associative multidimensional array
				$string_recipient_email_address => array($string_recipient_oid_or_zero, $string_recipient_name)
		@errors
	**/
	public function get_mail_recipients($type = array())
	{
		if (!is_array($type))
		{
			throw new awex_obj_type("Invalid type argument " . var_export($type, true));
		}

		$recipients = array();
		$customer_oid = (int) $this->prop("customer");

		if (!count($type) or in_array("user", $type))
		{
			// add current user
			if (aw_global_get("uid_oid"))
			{
				$u = obj(aw_global_get("uid_oid"));
				$person = obj(user::get_current_person());
				$email = $u->get_user_mail_address();
				if (is_email($email))
				{
					$recipients[$email] = array($person->id(), $person->name());
				}
			}
		}

		if (!count($type) or in_array("customer_general", $type))
		{
			$name = $this->get_customer_name();
			foreach($this->get_customer_mails() as $email)
			{
				if (is_email($email))
				{
					$recipients[$email] = array($customer_oid, $name);
				}
			}
		}

		if (!count($type) or in_array("custom", $type))
		{
			// manually added recipients
			$custom = $this->get_receivers();
			foreach ($custom as $email => $person_oid)
			{
				if (is_email($email))
				{
					if ($person_oid)
					{
						$person = new object($person_oid);
						$recipients[$email]  = array($person->id(), $person->name());
					}
					else
					{
						$recipients[$email]  = array(0, "");
					}
				}
			}
		}

		return $recipients;
	}
	
	/** Return all order e-mail receivers as associative array
		@attrib api=1 params=pos
		@comment
		@returns array
			e-mail address => person object id. Person object may be NULL or CL_CRM_PERSON oid
		@errors
	**/
	public function get_receivers()
	{
		return safe_array($this->meta("order_receivers"));
	}

	public function get_customer_name()
	{
		return $this->prop("customer_relation.buyer.name");
	}

	public function get_customer_mails()
	{
		if(!is_oid($this->prop("customer_relation.buyer")))
		{
			return array();
		}

		$customer = obj($this->prop("customer_relation.buyer"));
		if($customer->class_id() == CL_CRM_PERSON)
		{
			$mails = $customer->emails();
		}
		else
		{
			$mails = $customer->get_mails(array());
		}

		$customer_mails = array();
		$default_mail = null;
		foreach($mails->arr() as $mail)
		{
			if($mail->prop("mail"))
			{
				$default_mail = $mail;
				if($mail->prop("contact_type") == 1)
				{
					$customer_mails[$mail->id()]= $mail->prop("mail");
				}
			}
		}

		if(!sizeof($customer_mails) && is_object($default_mail))
		{
			$customer_mails[$default_mail->id()]= $default_mail->prop("mail");
		}
		return $customer_mails;
	}

	/** Returns mail subject string
		@attrib api=1 params=pos
		@param parse type=bool default=TRUE
			Whether to return parsed subject with special tags replaced by values or raw string
		@comment
		@returns string
		@errors
	**/
	public function get_mail_subject($parse = true)
	{
		$subject = "Tellimus nr #order_no#";

		if ($subject and $parse)
		{
			$subject = $this->parse_text_variables($subject);
		}

		return $subject;
	}

	/** Returns mail body/contents string
		@attrib api=1 params=pos
		@param parse type=bool default=TRUE
			Whether to return parsed contents with special tags replaced by values or raw string
		@comment
		@returns string
		@errors
	**/
	public function get_mail_body($parse = true)
	{
		$content = "Lugupeetav #contact_person#,

Saadame Teile tellimuse nr #order_no#.

Parimat,
#signature#";

		if ($content and $parse)
		{
			$content = $this->parse_text_variables($content);
		}

		return $content;
	}

	/** Parses variables in order e-mail body or subject text
		@attrib api=1 params=pos
		@param text type=string
			Text to parse variables in
		@comment
			Available variables are
			#order_no#
			#customer_name#
			#contact_person#
			#signature#

		@returns string
		@errors
	**/
	public function parse_text_variables($text)
	{
		$replace = array(
			"#order_no#" => $this->prop("name"),
			"#customer_name#" => $this->get_customer_name(),
			"#contact_person#" => $this->get_customer_contact_person_name(),
			"#signature#" => $this->get_sender_signature()
		);

		foreach($replace as $key => $val)
		{
			$text = str_replace($key, $val , $text);
		}

		return $text;
	}

	private function get_sender_signature()
	{
		$ret = array();
		$p = obj(user::get_current_person());
		$ret[] = $p->name();
		$names = $p->get_profession_names();
		$ret[] = reset($names);
		$names = $p->get_companies()->names();
		$ret[] = reset($names);
		$ret[] = $p->get_phone();
		$ret[] = $p->get_mail();
		return join("\n" , $ret);
	}

	public static function get_mail_variables_legend()
	{
		return '#order_no# => '.t("Tellimuse number").'
#customer_name# => '.t("Kliendi nimi").'
#contact_person# => '.t("Kliendi kontaktisiku nimi").'
#signature# => '.t("Saatja allkiri").'
';
	}

	/** Sends order document by mail
		@attrib api=1 params=pos obj_save=1
		@param to type=array
			Associative array of email addresses => names to send e-mail to
		@param subject type=string
			E-mail subject
		@param body type=string
			E-mail body text
		@param cc type=array
			Associative array of email addresses => names to send e-mail copy to
		@param bcc type=array
			Associative array of email addresses => names to send e-mail blind copy to
		@param from type=string default=""
			Sender e-mail address, default means either defined system default or current user e-mail address
		@param from_name type=string default=""
			Sender name, default means either defined system default or current user name
		@comment
		@returns void
		@errors
			throws awex_mrp_case_email if an invalid e-mail address given. awex_mrp_case_email::$email empty if no recipients or the faulty email address if encountered
			throws awex_mrp_case_send if sending e-mail fails
			throws awex_mrp_case_file if file attachment fails
	**/
	public function send_by_mail($to, $subject, $body, $cc = array(), $bcc = array(), $from = "", $from_name = "")
	{
		if (!count($to) and !count($cc) and !count($bcc))
		{
			throw new awex_mrp_case_email("Can't send mail, no recipients specified");
		}

		// get or create file attachments
		$pdf = $this->get_order_pdf();

		if (!is_object($pdf))
		{
			throw new awex_mrp_case_file("Main order file lost or not created. Order id " . $this->id());
		}

		// FIXME: Refactor to remove code duplication! Next 3 foreach loops!
		foreach ($to as $email_address => $recipient_name)
		{
			if (!is_email($email_address))
			{
				$e = new awex_mrp_case_email("Invalid email address '{$email_address}'. Sending order " . $this->id());
				$e->email = $email_address;
				throw $e;
			}

			$to[$email_address] = $recipient_name ? "{$recipient_name} <{$email_address}>" : $email_address;
		}
		$to = implode(",", $to);

		foreach ($cc as $email_address => $recipient_name)
		{
			if (!is_email($email_address))
			{
				$e = new awex_mrp_case_email("Invalid email address '{$email_address}'. Sending order " . $this->id());
				$e->email = $email_address;
				throw $e;
			}

			$cc[$email_address] = $recipient_name ? "{$recipient_name} <{$email_address}>" : $email_address;
		}
		$cc = implode(",", $cc);

		foreach ($bcc as $email_address => $recipient_name)
		{
			if (!is_email($email_address))
			{
				$e = new awex_mrp_case_email("Invalid email address '{$email_address}'. Sending order " . $this->id());
				$e->email = $email_address;
				throw $e;
			}

			$bcc[$email_address] = $recipient_name ? "{$recipient_name} <{$email_address}>" : $email_address;
		}

		/// add crm default recipients
		$default_recipients = $this->get_mail_recipients(array("default"));
		foreach ($default_recipients as $email_address => $data)
		{
			$bcc[$email_address] = $email_address;
		}

		$bcc = implode(",", $bcc);

		// compose mail
		$from = is_email($from) ? $from : $this->prop("send_mail_from");
		$from_name = empty($from_name) ? $this->prop("send_mail_from_name") : $from_name;
		$att_comment = "";

		$awm = new aw_mail();
		$awm->set_send_method("mimemessage");
		$awm->create_message(array(
			"froma" => $from,
			"fromn" => $from_name,
			"subject" => $subject,
			"body" => strip_tags($body),
			"to" => $to,
			"cc" => $cc,
			"bcc" => $bcc
		));
		$awm->set_header("Reply-To", $from);

		/// add attachments
		$part_count = $awm->fattach(array(
			"path" => $pdf->prop("file"),
			"contenttype"=> aw_mime_types::type_for_file($pdf->name()),
			"name" => $pdf->name()
		));
		$att_comment .= html::href(array(
			"caption" => html::img(array(
				"url" => aw_ini_get("icons.server")."pdf_upload.gif",
				"border" => 0
			)).$pdf->name(),
			"url" => $pdf->get_url()
		));

		if (!$part_count)
		{
			throw new awex_mrp_case_file("Attaching main order file (id: " . $pdf->id() . ") failed. Order id " . $this->id());
		}

		// add mail html body
		$awm->htmlbodyattach(array(
			"data" => $body
		));

		// send mail
		try
		{
			$awm->send();
		}
		catch (awex_awmail_send $e)
		{
			throw new awex_mrp_case_send ("Sending '".$this->id()."' failed. Mailer error: " . $e->getMessage());
		}

		// write log
		/// mail message object for logging
		$mail = obj(null, array(), CL_MESSAGE);
		$mail->set_parent($this->id());
		$mail->set_name(sprintf(t("Saadetud tellimus %s kliendile %s"), $this->prop("name"), $this->get_customer_name()));
		$mail->save();

		$attachments = array($pdf->id());
		$pdf ->set_parent($mail->id());
		$pdf->save();

		$mail->set_prop("attachments", $attachments);
		$mail->set_prop("customer", $this->prop("customer_relation.buyer"));
		$mail->set_prop("message", $body);
		$mail->set_prop("html_mail", 1);
		$mail->set_prop("mfrom_name", $from_name);
		$mail->set_prop("mto", $to);
		$mail->set_prop("cc", $cc);
		$mail->set_prop("bcc", $bcc);
		$mail->save();

		$this->set_prop("order_state", self::ORDER_STATE_SENT);

		$comment = sprintf(t("Aadressidele: %s\nKoopia aadressidele: %s\nTekst: %s\nLisatud failid: %s."), htmlspecialchars($to), htmlspecialchars($cc), html_entity_decode($body), html_entity_decode($att_comment));
		$ws = new mrp_workspace();
		$ws->mrp_log($this->id(), NULL, t("Tellimus saadeti kliendile"), $comment);

		$this->save();
	}

	/** returns sent mail objects
		@attrib api=1
		@returns object list
	**/
	public function get_sent_mails()
	{
		return new object_list(array(
			"class_id" => CL_MESSAGE,
			"parent" => $this->id()
		));
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

/** E-mail address errors **/
class awex_mrp_case_email extends awex_mrp_case
{
	public $email;
}

/** E-mail sending errors **/
class awex_mrp_case_send extends awex_mrp_case {}

/** PDF or other files related errors **/
class awex_mrp_case_file extends awex_mrp_case {}

