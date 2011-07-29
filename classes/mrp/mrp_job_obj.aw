<?php

require_once "mrp_header.aw";

class mrp_job_obj extends _int_object implements crm_sales_price_component_interface, crm_offer_row_interface
{
	const CLID = 826;

	const PRSN_HNDL_S = 1;
	const PRSN_HNDL_F = 2;
	const PRSN_HNDL_S_OR_F = 3;
	const PRSN_HNDL_S_AND_F = 4;

	const STATE_NEW = 1; // job hasn't been planned yet
	const STATE_PLANNED = 2; // start time has been planned. job is in schedule
	const STATE_ONHOLD = 14; // start time has been planned. job is not in schedule but can be rescheduled. workflow can not continue.
	const STATE_ABORTED = 4; // work was started but then stopped with no knowledge if resumed in future. workflow can not continue.
	const STATE_DONE = 5; // job is completed
	const STATE_LOCKED = 6;
	const STATE_DELETED = 8; // job is deleted
	const STATE_CANCELED = 13; // job is canceled, job is not in schedule. replanning/rescheduling is not available. workflow can continue.

	// states that mark a job that is in progress
	const STATE_PAUSED = 7; // in progress but on pause, resource is reserved
	const STATE_INPROGRESS = 3; // work is being done
	const STATE_SHIFT_CHANGE = 12; // on pause for shift changing, resource is reserved

	protected static $mrp_state_names = array(); // array of state => human_readable_name
	protected $mrp_resource; // CL_MRP_RESOURCE
	protected $mrp_case; // CL_MRP_CASE
	protected $mrp_workspace; // CL_MRP_WORKSPACE
	protected $mrp_state; // job state property cache
	protected $mrp_job_data_loaded = false;
	protected $mrp_job_state_data = array();

	private $pause_state	= self::STATE_PAUSED; // used to discern between pausing and shift change in pause();
	private $change_name = false; // when new resource or project set, job name changes

	public static $in_progress_states = array(
		self::STATE_INPROGRESS,
		self::STATE_PAUSED,
		self::STATE_SHIFT_CHANGE
	);

	private static $planning_states = array(
		self::STATE_INPROGRESS,
		self::STATE_PAUSED,
		self::STATE_PLANNED
	);

	private static $case_states_for_setting_planned = array (
		mrp_case_obj::STATE_PLANNED,
		mrp_case_obj::STATE_ONHOLD,
		mrp_case_obj::STATE_INPROGRESS
	);

	private static $job_states_for_setting_planned = array (
		self::STATE_NEW,
		self::STATE_ONHOLD,
		self::STATE_CANCELED,
		self::STATE_ABORTED
	);

	private static $case_states_for_setting_on_hold = array(
		mrp_case_obj::STATE_NEW,
		mrp_case_obj::STATE_PLANNED,
		mrp_case_obj::STATE_INPROGRESS,
		mrp_case_obj::STATE_ONHOLD
	);

	private static $job_states_for_setting_on_hold = array(
		self::STATE_NEW,
		self::STATE_ABORTED,
		self::STATE_PLANNED
	);

	private static $job_states_for_canceling = array(
		self::STATE_NEW,
		self::STATE_ABORTED,
		self::STATE_ONHOLD,
		self::STATE_PLANNED
	);

	private static $states_for_logging_workers = array(
		self::STATE_INPROGRESS,
		self::STATE_ABORTED,
		self::STATE_DONE,
		self::STATE_PAUSED,
		self::STATE_SHIFT_CHANGE
	);


	/**
	@attrib api=1 params=pos
	@param objdata required type=array
		_int_object standard input
	@param args optional type=array
		"load_data" element - type bool - default FALSE -- fast and memoryconserving mode, TRUE -- load all job data also which is required for some methods (refer to individual documentation).
		"resource" element - type object CL_MRP_RESOURCE - default null -- if set and constructing a new object, defaults will be imported from and 'resource' prop will be set to that resource.
	@errors
		throws awex_obj_acl when no access rights for resource or project.
		throws awex_obj_class when resource or project of invalid class.
		throws awex_mrp_job_data when resource (code 1) or project (code 2) not set.
		throws awex_mrp_job (with forwarded exception) on other errors.
	**/
	public function __construct($objdata, $args = array())
	{
		try
		{
			parent::__construct($objdata);
		}
		catch (Exception $E)
		{
			$error_message = "Unknown error (" . get_class($e) . "): " . $e->getMessage();
			$e = new awex_mrp_job($error_message);
			$e->set_forwarded_exception($E);
			throw $e;
		}

		if (!empty($args["load_data"]))
		{
			$this->load_data();
		}

		$new = (null === $this->id());
		if ($new)
		{
			### set status
			$this->set_prop ("state", self::STATE_NEW);

			if (isset($args["resource"]))
			{
				$resource = $args["resource"];
				if (!($resource instanceof object) or !$resource->is_a(CL_MRP_RESOURCE))
				{
					throw new awex_obj_class("Invalid resource parameter. Not a resource object");
				}

				$this->awobj_set_resource($resource->id());

				// init with resource defaults
				$this->set_prop("pre_buffer", $resource->prop("default_pre_buffer"));
				$this->set_prop("post_buffer", $resource->prop("default_post_buffer"));
				$this->set_prop("min_batches_to_continue_wf", $resource->prop("default_min_batches_to_continue_wf"));
				$this->set_prop("batch_size", $resource->prop("default_batch_size"));
			}
			else
			{
				$this->set_prop("pre_buffer", 0);
				$this->set_prop("post_buffer", 0);
				$this->set_prop("min_batches_to_continue_wf", 0);
				$this->set_prop("batch_size", 1);
			}

			$this->set_prop("component_quantity", 1);
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

	/** Loads and verifies full job data
	@attrib api=1 params=pos
	@returns void
	@errors
		throws awex_obj_acl when no access rights for resource or project.
		throws awex_obj_class when resource or project of invalid class.
		throws awex_mrp_case_workspace when workspace not defined.
		throws awex_mrp_job_data on internal data errors:
			resource (code 1)
			project (code 2)
			prerequisites definition invalid (code 3)
			state couldn't be loaded (code 4)
		throws awex_mrp_job (with forwarded exception) on other errors.
	**/
	public function load_data()
	{
		if ($this->mrp_job_data_loaded)
		{
			return;
		}
		//!!! lock?

		try
		{
			// load resource
			$resource = $this->prop("resource");
			if (!is_oid($resource))
			{
				throw new awex_mrp_job_data("Resource not defined for job " . $this->id(), 1);
			}
			$this->mrp_resource = obj($resource, array(), CL_MRP_RESOURCE);

			// load project
			$project = $this->prop("project");
			if (!is_oid($this->prop("project")))
			{
				throw new awex_mrp_job_data("Project not defined for job " . $this->id(), 2);
			}
			$this->mrp_case = obj($project, array(), CL_MRP_CASE);

			// load workspace
			$this->mrp_workspace = $this->mrp_case->prop("workspace");

			// load properties
			$this->mrp_state = $this->prop("state");
		}
		catch (awex_obj_acl $e)
		{
			throw $e;
		}
		catch (awex_obj_class $e)
		{
			throw $e;
		}
		catch (awex_mrp_case_workspace $e)
		{
			throw $e;
		}
		catch (awex_mrp_job_data $e)
		{
			throw $e;
		}
		catch (Exception $E)
		{
			$error_message = "Unknown error (" . get_class($e) . "): " . $e->getMessage();
			$e = new awex_mrp_job($error_message);
			$e->set_forwarded_exception($E);
			throw $e;
		}

		$this->mrp_job_data_loaded = true;
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
				self::STATE_PAUSED => t("Paus"),
				self::STATE_SHIFT_CHANGE => t("Paus"),
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

	public function set_prop($k, $v)
	{
		if($k === "state")
		{
			if ($v === self::STATE_DONE)
			{
				$this->set_prop("real_length", $this->get_real());
				$this->set_prop("length_deviation", $this->get_deviation());
			}
			$this->mrp_state = $v; // update data cache
		}

		return parent::set_prop($k, $v);
	}

	function save($exclusive = false, $previous_state = null)
	{
		$this->mrp_job_data_loaded = false;
		if ($this->change_name)
		{
			$project = is_oid($this->prop("project")) ? obj($this->prop("project"))->name() : t("Projektita");
			$resource = is_oid($this->prop("resource")) ? obj($this->prop("resource"))->name() : t("Ressursita");
			$this->set_name ($project . " - " . $resource . " - " . $this->ord());
			$this->change_name = false;
		}
		$retval = parent::save($exclusive, $previous_state);
		$this->log_state_change();
		return $retval;
	}

	private function get_deviation()
	{
		return $this->prop("real_length") - $this->prop("length");
	}

	private function get_real()
	{
		$job_id = $this->id();
		$v = $this->instance()->db_fetch_field("
			SELECT
				SUM(aw_job_last_duration) as length_sum
			FROM
				mrp_job_rows
			WHERE
				aw_job_id = '{$job_id}' AND
				aw_job_previous_state = '".self::STATE_INPROGRESS."';",
			"length_sum");
		// If the work is still in progress we have to add the time from last state change until now.
		$i = $this->instance()->db_fetch_row("SELECT aw_job_state, UNIX_TIMESTAMP() - aw_tm as tm FROM mrp_job_rows WHERE aw_job_id = '$job_id' ORDER BY aw_tm DESC LIMIT 1");
		if(isset($i["aw_job_state"]) && $i["aw_job_state"] == self::STATE_INPROGRESS)
		{
			return (int)$v + $i["tm"];
		}
		return (int)$v;
	}

	public function get_resource()
	{
		$this->load_data();
		return $this->mrp_resource;
	}

	public function get_workspace()
	{
		$this->load_data();
		return $this->mrp_workspace;
	}

	/**
	@attrib name=save_materials

	@param amount required type=array
	@param unit required type=array
	@param movement optional type=array
	@param planning optional type=array

	@comment
		Function to be called on a mrp_job object to save its materials
		Parameters are arrays of $product_id => "value" pairs
		eg amount => array( 123 => 12, 124 => 13 ), where 123 & 124 are product ids, 12 & 13 amounts
	**/
	function save_materials($arr)
	{
		$res = obj($this->prop("resource"));
		if($res)
		{
			$conn = $res->connections_to(array(
				"from.class_id" => CL_MATERIAL_EXPENSE_CONDITION
			));
			$conn2 = $this->connections_to(array(
				"from.class_id" => CL_MATERIAL_EXPENSE,
			));
			foreach($conn2 as $c)
			{
				$o = $c->from();
				$prod = $o->prop("product");
				$prods[$prod] = $o->id();
			}

			foreach($conn as $c)
			{
				$prod = $c->from()->prop("product");
				if(!$prod)
				{
					continue;
				}
				$unit = new object($arr["unit"][$prod]);
				$this->set_used_material_assessment(obj($prod), $arr["amount"][$prod], $unit, $arr["movement"][$prod], $arr["planning"][$prod]);
			}

			$conn = $this->connections_to(array(
				"from.class_id" => CL_MATERIAL_MOVEMENT_RELATION,
			));
			foreach($arr["unit"] as $prod => $unit)
			{
				if(!$arr["amount"][$prod])
				{
					continue;
				}
				$data[$prod] = array(
					"unit" => $unit,
					"amount" => $arr["amount"][$prod],
				);
			}
			$dn = obj();
			$dn->set_class_id(CL_SHOP_DELIVERY_NOTE);
			if(!count($conn))
			{
				$o = obj();
				$o->set_class_id(CL_MATERIAL_MOVEMENT_RELATION);
				$o->set_parent($this->id());
				$o->set_name(sprintf(t("Materjali liikumisseos t&ouml;&ouml;ga %s"), $this->name()));
				$o->set_prop("job", $this->id());
				$o->save();
				$dn = obj();
				$dn->set_class_id(CL_SHOP_DELIVERY_NOTE);
				$dno = $dn->create_dn(sprintf(t("%s saateleht"), $this->name()), $o->id(), array("rows" => $data));
				$job = $o->prop("job");
				if($this->can("view", $job))
				{
					$case = obj($job)->get_first_obj_by_reltype("RELTYPE_MRP_PROJECT");
				}
				if($case)
				{
					$dno->set_prop("number", $case->name());
					$wh = $case->prop("warehouse");
				}
				$dno->set_prop("from_warehouse", $wh);
				$dno->save();
				$o->set_prop("dn", $dno->id());
				$o->save();
			}
			else
			{
				foreach($conn as $c)
				{
					$dn = $c->from()->prop("dn");
					if($dn && $this->can("view", $dn))
					{
						$dno = obj($dn);
						$dno->update_dn_rows($data);
					}
				}
			}

			if($this->prop("state") == self::STATE_PLANNED)
			{
				$ws = $res->prop("workspace");
			}
			$conn = array();
			if($ws)
			{
				$conn = $ws->connections_to(array(
					"from.class_id" => CL_SHOP_PURCHASE_MANAGER_WORKSPACE,
				));
			}
			if(count($conn))
			{
				$c = reset($conn);
				$c->from()->update_job_order($this);
			}
		}
	}

	public function get_material_expense_list()
	{
		$ol = new object_list(array(
			"class_id" => CL_MATERIAL_EXPENSE,
			"job" => $this->id()
		));
		$rv = array();
		foreach($ol->arr() as $entry)
		{
			$rv[$entry->prop("product")] = $entry;
		}
		return $rv;
	}

	/** Adds, changes or removes source materials planned to be used by this job
	@attrib api=1 params=pos
	@param product required type=CL_SHOP_PRODUCT
	@param amount required type=int,float
		Quantity how much or how many of $product is assessed to be required for completing this job.
	@param unit optional type=CL_UNIT
		If no unit given, product's default unit used.
	@param movement optional type=int
	@param planning optional type=int
	@returns void
	@comment
		If quantity is 0, the product is removed from this job's planned materials. If product already planned to be used and $quantity is different, it will overwrite previous value.
	@errors
		throws awex_mrp_job_type when $unit or $product argument of wrong class (exception variable $argument_name contains faulty parameter name)
		throws awex_mrp_job_state when job is already done or in progress
	**/
	public function set_used_material_assessment(object $product, $amount, object $unit = null, $movement = null, $planning = null)
	{
		if (CL_SHOP_PRODUCT != $product->class_id())
		{
			$e = new awex_mrp_job_type("Wrong type product object.");
			$e->argument_name = "product";
			throw $e;
		}

		$applicable_states = array(
			self::STATE_NEW,
			self::STATE_PLANNED,
			self::STATE_ABORTED
		);
		if (!in_array($this->prop("state"), $applicable_states))
		{
			throw new awex_mrp_job_state("Can't set assessments for jobs in progress, done and with similar states");
		}

		$conn2 = $this->connections_to(array(
			"from.class_id" => CL_MATERIAL_EXPENSE,
		));
		foreach($conn2 as $c)
		{
			$o = $c->from();
			$prod = $o->prop("product");
			$prods[$prod] = $o->id();
		}
		$used_materials = array();
		$prod = $product->id();
		if(!isset($prods[$prod]) && $amount > 0)
		{
			// add new material
			$o = obj();
			$o->set_class_id(CL_MATERIAL_EXPENSE);
			$o->set_parent($this->id());
			$o->set_name(sprintf(t("%s kulu %s jaoks"), $product->name(), $this->name()));
			$o->set_prop("product", $prod);

			if(is_a($unit, "object"))
			{
				if (CL_UNIT != $unit->class_id())
				{
					$e = new awex_mrp_job_type("Wrong type unit object.");
					$e->argument_name = "unit";
					throw $e;
				}
			}
			else
			{
				$units = $product->instance()->get_units($product);
				if(count($units))
				{
					$unit = new object(reset($units));
				}
				else
				{
					//!!!
				}
			}

			$o->set_prop("unit", $unit->id());
			$o->set_prop("amount", $amount);
			$o->set_prop("job", $this->id());
			if(isset($movement))
			{
				$o->set_prop("movement", $movement);
			}
			if(isset($planning))
			{
				$o->set_prop("planning", $planning);
			}
			$this->set_used_material_base_amount(array(
				"obj" => &$o,
				"product" => $prod,
				"amount" => $amount,
				"unit" => $unit->id(),
			));
			$o->save();
			$used_materials[$prod] = $amount;//!!! in default units
		}
		elseif (isset($prods[$prod]))
		{
			if($amount <= 0)
			{
				// remove material
				$eo = obj($prods[$prod]);
				$eo->delete();
				unset($used_materials[$prod]);
			}
			else
			{
				// change material quantity
				$eo = obj($prods[$prod]);
				$eo->set_prop("unit", $unit->id());
				$eo->set_prop("amount", $amount);
				if(isset($movement))
				{
					$eo->set_prop("movement", $movement);
				}
				if(isset($planning))
				{
					$eo->set_prop("planning", $planning);
				}
				$this->set_used_material_base_amount(array(
					"obj" => &$eo,
					"product" => $prod,
					"amount" => $amount,
					"unit" => $unit->id(),
				));
				$eo->save();
				$used_materials[$prod] = $amount;//!!! in default units
			}
		}
	}

	/** Sets source material amount that was used by this job
	@attrib api=1 params=pos
	@param product required type=CL_SHOP_PRODUCT
	@param amount required type=int,float
		Quantity how much or how many of $product was used by this job.
	@param unit optional type=CL_UNIT
		If no unit given, product's default unit used.
	@returns void
	@errors
		throws awex_mrp_job_type when $unit or $product argument of wrong class (exception variable $argument_name contains faulty parameter name)
		throws awex_mrp_job_state when job is not done or is archived ...
	**/
	public function set_used_material_amount(object $product, $amount, object $unit = null)
	{
		// check for errors
		if (CL_SHOP_PRODUCT != $product->class_id())
		{
			$e = new awex_mrp_job_type("Wrong type product object.");
			$e->argument_name = "product";
			throw $e;
		}

		if (isset($unit) and CL_UNIT != $unit->class_id())
		{
			$e = new awex_mrp_job_type("Wrong type unit object.");
			$e->argument_name = "unit";
			throw $e;
		}
		elseif (!isset($unit))
		{
			$unit = reset($product->get_units());
			// get default unit
		}

		if ($amount < 0)
		{
			$e = new awex_mrp_job_type("Material amount can't be less than 0.");
			$e->argument_name = "amount";
			throw $e;
		}

		$applicable_states = array(
			self::STATE_DONE,
			self::STATE_CANCELED
		);
		if (!in_array($this->prop("state"), $applicable_states))
		{
			throw new awex_mrp_job_state("Invalid state " . var_export($this->prop("state"), true) . " for reporting material expenses");
		}

		// set amount
		$ol = new object_list(array(
			"class_id" => CL_MATERIAL_EXPENSE,
			"job" => $this->id(),
			"product" => $product->id()
		));
//nomaeitea errorit annab see hetkel
//		$ol->set_prop("used_amount", $amount);
//		$ol->save();
		foreach($ol->arr() as $o){$o->set_prop("used_amount", $amount);$o->save();}
	}

/** Sets job ready to be scheduled. Applies to new and aborted jobs
    @attrib api=1 params=pos
	@returns void
	@errors
		throws awex_mrp_job_state when current job state doesn't allow planning.
		throws awex_mrp_case_state if case is not planned
		throws awex_mrp_job on errors.
**/
	function plan()
	{
		$this->load_data();

		if (!in_array ($this->mrp_case->prop("state"), self::$case_states_for_setting_planned))
		{
			throw new awex_mrp_case_state("Invalid case state for planning: '" . $this->mrp_case->prop ("state") . "'");
		}

		if (!in_array ($this->prop("state"), self::$job_states_for_setting_planned))
		{
			throw new awex_mrp_job_state("Invalid job state for planning: '" . $this->prop ("state") . "'");
		}

		try
		{
			$this->set_prop ("state", self::STATE_PLANNED);
			$this->save ();
			$this->state_changed("");
			$this->mrp_workspace->request_rescheduling();
		}
		catch (Exception $E)
		{
			$error_message = "Unknown error (" . get_class($e) . "): " . $e->getMessage();
			$e = new awex_mrp_job($error_message);
			$e->set_forwarded_exception($E);
			throw $e;
		}
	}

/** Inserts job to schedule or reschedules it
    @attrib api=1 params=pos
	@param start type=int
		UNIX timestamp scheduled start time for this job
	@param length type=int
		Eventual job duration in seconds in this schedule position
	@returns void
	@errors
		throws awex_mrp_job_state when current job state doesn't allow planning.
		throws awex_mrp_job on errors.
**/
	function schedule($start, $length)
	{
		$applicable_states = array (
			self::STATE_NEW,
			self::STATE_PLANNED
		);
		if (!in_array ($this->prop("state"), $applicable_states))
		{
			throw new awex_mrp_job_state("State must be 'NEW' or 'PLANNED'.");
		}

		try
		{
			$comment = "";
			$this->set_prop ("starttime", $start);
			$this->set_prop ("planned_length", $length);
			if ($this->prop("state") != self::STATE_PLANNED)
			{
				$this->set_prop ("state", self::STATE_PLANNED);
				// $this->set_prop ("first_planned", time ());
				$comment = "mrp_schedule";
			}

			$this->save ();

			if ($comment)
			{
				$this->state_changed($comment);
			}
		}
		catch (Exception $E)
		{
			$error_message = "Unknown error (" . get_class($e) . "): " . $e->getMessage();
			$e = new awex_mrp_job($error_message);
			$e->set_forwarded_exception($E);
			throw $e;
		}
	}

/** Sets the job on hold. Job won't be scheduled but remains active. Project must be planned or new. Requires load_data()
    @attrib api=1 params=pos
	@returns void
	@errors
		throws awex_mrp_job_state when current job state doesn't allow setting on hold.
		throws awex_mrp_case_state when current project state doesn't allow setting on hold.
		throws awex_mrp_job on errors.
**/
	function set_on_hold()
	{
		$this->load_data();

		if (!in_array ($this->mrp_case->prop("state"), self::$case_states_for_setting_on_hold))
		{
			throw new awex_mrp_case_state("Invalid case state for setting on hold: '" . $this->mrp_case->prop ("state") . "'");
		}

		if (!in_array ($this->prop ("state"), self::$job_states_for_setting_on_hold))
		{
			throw new awex_mrp_job_state("Invalid job state for setting on hold: '" . $this->prop ("state") . "'");
		}

		try
		{
			$this->set_prop ("state", self::STATE_ONHOLD);
			$this->save ();
			$this->state_changed("");
			$this->mrp_workspace->request_rescheduling();
		}
		catch (Exception $E)
		{
			$error_message = "Unknown error (" . get_class($e) . "): " . $e->getMessage();
			$e = new awex_mrp_job($error_message);
			$e->set_forwarded_exception($E);
			throw $e;
		}
	}

/** Cancels a new, planned, on hold or aborted job. Job won't be scheduled but remains active. Project must be planned or new.
    @attrib api=1 params=pos
	@returns void
	@errors
		throws awex_mrp_job_state when current job state doesn't allow canceling.
		throws awex_mrp_job on errors.
**/
	public function cancel()
	{
		$this->load_data();

		if (!in_array ($this->prop ("state"), self::$job_states_for_canceling))
		{
			throw new awex_mrp_job_state("Invalid job state for canceling: '" . $this->prop ("state") . "'");
		}

		try
		{
			$this->set_prop ("state", self::STATE_CANCELED);
			$this->save ();
			$this->state_changed("");
			$this->mrp_workspace->request_rescheduling();
		}
		catch (Exception $E)
		{
			$error_message = "Unknown error (" . get_class($e) . "): " . $e->getMessage();
			$e = new awex_mrp_job($error_message);
			$e->set_forwarded_exception($E);
			throw $e;
		}
	}

/** Starts the job. Job must be planned.
    @attrib api=1 params=pos
	@param comment type=string default=""
		Arbitrary comment text
	@param real_start_time type=int default=NULL
		UNIX timestamp real start time. Default is the time when method is called. Used for reflecting job state changes asynchronously
	@returns void
	@errors
		throws awex_mrp_job_state when current job state doesn't allow starting.
		throws awex_mrp_resource_unavailable if resource couldn't be reserved.
		throws awex_mrp_job_prerequisites when one of job's prerequisite jobs is not done.
		throws awex_mrp_case_state when project isn't ready for this job.
		throws awex_mrp_case on project errors. Indicates that the job was started successfully but couldn't be set started on project. This results in outdated project object data. These errors must be corrected by appropriate methods in project object.
		throws awex_mrp_resource on other resource errors. Job might not have been stopped on resource.
		throws awex_mrp_job on other errors.
**/
	function start ($comment = "", $real_start_time = null)
	{
		//!!!!!!
		//!!! asynkroonse sisestamise jaoks tuleb teha kontroll et aeg mis parameetriks anti pole juba
		//!!! sellel ressursil kinni (t2henduses, et selle hetke kohta on juba muu tegevus kirjas)
		//!!! lisaks peab v6imaldama sellisel juhul ressursi staatust mitte kontrollida can_start-is, sest
		//!!! sisestatakse infot mineviku kohta ja hetke seis pole oluline

		$this->load_data();

		// verify state
		$applicable_states = array (
			self::STATE_PLANNED
		);
		if (!in_array ($this->mrp_state, $applicable_states))
		{
			throw new awex_mrp_job_state("Can't start job with state '{$this->mrp_state}'");
		}

		try
		{
			$saving = false;

			if (empty($real_start_time))
			{
				$real_start_time = time();
			}

			// reserve resource
			$this->mrp_resource->reserve($this->ref()); //!!! kas reserveerida enne kontrolle v6i p2rast?

			### check if prerequisites are done
			if (!$this->job_prerequisites_are_done())
			{
				throw new awex_mrp_job_prerequisites("A prerequisite is not done.");
			}

			// start making changes to data
			$this->save_mrp_state("start");

			### start job
			$this->set_prop ("state", self::STATE_INPROGRESS);
			$this->set_prop ("started", (int)$real_start_time);

			// update job in project
			$this->mrp_case->update_progress($this->ref());

			// start on resource
			$this->mrp_resource->start_job($this->ref());

			### all went well, save
			$saving = true;
			$this->save ();

			### log
			$this->state_changed($comment);
			$this->stats_start();
		}
		catch (awex_mrp_job_prerequisites $e)
		{
			$this->mrp_resource->cancel_reservation($this->ref());
			throw $e;
		}
		catch (awex_mrp_resource_unavailable $e)
		{
			$this->mrp_resource->cancel_reservation($this->ref());
			throw $e;
		}
		catch (awex_mrp_case_state $e)
		{
			$this->mrp_resource->cancel_reservation($this->ref());
			$this->restore_mrp_state("start", $saving);
			throw $e;
		}
		catch (awex_mrp_case $e)
		{
			$this->mrp_resource->cancel_reservation($this->ref());
			$this->restore_mrp_state("start", $saving);
			throw $e;
		}
		catch (awex_mrp_resource $e)
		{
			try
			{
				$this->mrp_resource->stop_job($this->ref());
				$this->restore_mrp_state("start", $saving);
			}
			catch (Exception $E)
			{
				$e->set_forwarded_exception($E);
			}
			throw $e;
		}
		catch (Exception $E)
		{
			$error_message = "Unknown error (" . get_class($E) . "): " . $E->getMessage();
			$e = new awex_mrp_job($error_message);
			$e->set_forwarded_exception($E);

			// restore job state
			try
			{
				$this->restore_mrp_state("start", $saving);
			}
			catch (Exception $E)
			{
				$e->set_forwarded_exception($E);
			}

			### free resource and exit
			$this->mrp_resource->cancel_reservation($this->ref());
			throw $e;
		}
	}

/** Ends the job or sets how much is done if $quantity parameter specified. Job must be in progress.
    @attrib api=1 params=pos
	@param quantity optional type=int,float
		Amount/quantity of items done. If not specified, assumed that whole job done
	@param comment type=string default=""
		Arbitrary comment text
	@param real_length type=int default=NULL
		Real job duration in seconds. Default is the time when method is called minus start time. Used for reflecting job state changes asynchronously
	@returns void
	@errors
		throws awex_mrp_job_state when current job state doesn't allow finishing.
		throws awex_mrp_resource on resource errors. Job might not have been stopped on resource.
		throws awex_mrp_job on other errors.
	**/
	function done ($quantity = null, $comment = "", $real_length = null)
	{
		$this->load_data();

		// verify state
		$applicable_states = array (
			self::STATE_INPROGRESS
		);

		if (in_array ($this->mrp_state, $applicable_states))
		{ // job is in progress, normal execution flow
			try
			{
				if (isset($quantity))
				{ // part of job done
					$this->set_prop ("done", $this->prop("done") + $quantity);
				}
				elseif ($this->prop("done") < 1)
				{ // set whole order quantity done when no specific data entered
					$quantity = $this->mrp_case->prop("order_quantity")*$this->prop("component_quantity");
					$this->set_prop ("done", $quantity);
				}

				// set who did them. set time
				$i = $this->instance();
				$i->db_query("
					INSERT INTO mrp_job_progress
						(aw_job_id, aw_case_id, aw_resource_id, aw_pid_oid, aw_uid_oid, aw_quantity, aw_entry_time)
					VALUES
						('".$this->id()."', '".$this->prop("project")."', '".$this->prop("resource")."', '".get_instance("user")->get_current_person()."', '".aw_global_get("uid_oid")."', '{$quantity}', '".time()."')
				");

				if ($this->prop("done") >= $this->mrp_case->prop("order_quantity")*$this->prop("component_quantity"))
				{ // whole job done
					// free resource
					$this->mrp_resource->stop_job($this->ref());

					### finish job
					$time = time ();
					$this->set_prop ("state", self::STATE_DONE);
					$this->set_prop ("finished", $time);

					### log job change
					$this->state_changed($comment);
					$this->stats_done();
				}

				### save changes
				$this->save();

				### post rescheduling msg
				$this->mrp_workspace->request_rescheduling();

				if (!isset($quantity))
				{
					// update job in project
					$this->mrp_case->update_progress($this->ref());
				}
			}
			catch (awex_mrp_resource $e)
			{
				throw $e;
			}
			catch (awex_mrp_case $e)
			{
			}
			catch (Exception $E)
			{
				$error_message = "Unknown error (" . get_class($E) . "): " . $E->getMessage();
				$e = new awex_mrp_job($error_message);
				$e->set_forwarded_exception($E);
				throw $e;
			}
		}
		elseif ($this->mrp_resource->is_processing($this->ref()))
		{ // job isn't in progress but resource is processing it, presume previous execution flow error and try to stop job on resource. try to correct job data also
			try
			{
				if (null === $real_length)
				{
					$real_length = $this->get_real();
				}

				// free resource
				$this->mrp_resource->restore_data_integrity();
				$this->mrp_resource->stop_job($this->ref());
				$this->set_prop ("state", self::STATE_DONE);
				$this->set_prop("length_deviation", $this->get_deviation());
				$this->set_prop("real_length", $real_length);
				if ($this->prop("finished") < 2)
				{
					$this->set_prop ("finished", time());
				}

				$this->save();
			}
			catch (awex_mrp_resource_job $e)
			{
				$this->mrp_resource->cancel_reservation($this->ref());
				$this->set_prop ("state", self::STATE_DONE);
				$this->set_prop("length_deviation", $this->get_deviation());
				$this->set_prop("real_length", $real_length);
				if ($this->prop("finished") < 2)
				{
					$this->set_prop ("finished", time());
				}

				$this->save();
			}
			catch (Exception $E)
			{
				throw $E;
			}
		}
	}

/** Aborts the job. Job must be paused or in progress.
    @attrib api=1 params=pos
	@returns void
	@errors
		throws awex_mrp_job_state when current job state doesn't allow aborting.
		throws awex_mrp_resource on resource errors. Job might not have been stopped on resource.
		throws awex_mrp_job on other errors.
**/
	function abort ($comment = "")
	{
		$this->load_data();

		$applicable_states = array (
			self::STATE_INPROGRESS,
			self::STATE_SHIFT_CHANGE, //!!! miks pausil olevat t88d peaks saama katkestada?
			self::STATE_PAUSED
		);
		if (!in_array ($this->mrp_state, $applicable_states))
		{
			throw new awex_mrp_job_state("Can't abort job with state '{$this->mrp_state}'");
		}

		try
		{
			### set resource as free
			$this->mrp_resource->stop_job($this->ref());

			### abort job
			$this->set_prop ("state", self::STATE_ABORTED);
			$this->set_prop ("aborted", time());
			$this->save ();

			### post rescheduling msg
			$this->mrp_workspace->request_rescheduling();

			### log event
			$this->state_changed($comment);
			$this->stats_done();

			// update job in project
			$this->mrp_case->update_progress($this->ref());
		}
		catch (awex_mrp_resource $e)
		{
			throw $e;
		}
		catch (awex_mrp_case $e)
		{
		}
		catch (Exception $e)
		{
			$error_message = "Unknown error (" . get_class($e) . "): " . $e->getMessage();
			$e = new awex_mrp_job($error_message);
			$e->set_forwarded_exception($E);
			throw $e;
		}
	}

/** Pauses the job. Job must be in progress.
    @attrib api=1 params=pos
	@returns void
	@errors
		throws awex_mrp_job_state when current job state doesn't allow pausing.
		throws awex_mrp_job on other errors.
**/
	function pause ($comment = "")
	{
		$this->load_data();

		$applicable_states = array (
			self::STATE_INPROGRESS
		);
		if (!in_array ($this->mrp_state, $applicable_states))
		{
			throw new awex_mrp_job_state("Can't pause job with state '{$this->mrp_state}'");
		}

		try
		{
			### pause job
			$this->set_prop ("state", $this->pause_state);
			$this->pause_state = self::STATE_PAUSED;

			### save paused times for job
			$pt = safe_array($this->meta("paused_times"));
			$pt[] = array("start" => time(), "end" => NULL);
			$this->set_meta("paused_times" , $pt);

			### save project&job
			$this->save ();

			### log event
			$this->state_changed($comment);
			$this->stats_done();

			// update job in project
			$this->mrp_case->update_progress($this->ref());
		}
		catch (awex_mrp_case $e)
		{
		}
		catch (Exception $E)
		{
			$error_message = "Unknown error (" . get_class($e) . "): " . $e->getMessage();
			$e = new awex_mrp_job($error_message);
			$e->set_forwarded_exception($E);
			throw $e;
		}
	}

/** Continue a paused job.
    @attrib api=1 params=pos
	@returns void
	@errors
		throws awex_mrp_job_state when current job state doesn't allow continuing.
		throws awex_mrp_job on other errors.
**/
	function scontinue($comment = "")
	{
		$this->load_data();

		$applicable_job_states = array (
			self::STATE_PAUSED,
			self::STATE_SHIFT_CHANGE
		);
		if (!in_array ($this->mrp_state, $applicable_job_states))
		{
			throw new awex_mrp_job_state("Can't continue job with state '{$this->mrp_state}'");
		}

		try
		{
			### continue job
			$this->set_prop ("state", self::STATE_INPROGRESS);

			### save paused times for job
			$pt = safe_array($this->meta("paused_times"));
			$pt[count($pt)-1]["end"] = time();
			$this->set_meta("paused_times" , $pt);

			$this->save ();

			### log event
			$this->state_changed($comment);
			$this->stats_start();

			// update job in project. assuming here that job can be continued till done regardless of project changes meanwhile
			$this->mrp_case->update_progress($this->ref());
		}
		catch (awex_mrp_case $e)
		{
		}
		catch (Exception $E)
		{
			$error_message = "Unknown error (" . get_class($e) . "): " . $e->getMessage();
			$e = new awex_mrp_job($error_message);
			$e->set_forwarded_exception($E);
			throw $e;
		}
	}

/** Resume job after abort
    @attrib api=1 params=pos
	@returns void
	@errors
		throws awex_mrp_job_state when current job state doesn't allow resuming.
		throws awex_mrp_resource_unavailable if resource couldn't be reserved.
		throws awex_mrp_job_prerequisites when one of job's prerequisite jobs is not done.
		throws awex_mrp_case_state when project isn't ready for this job.
		throws awex_mrp_case on project errors. Indicates that the job was started successfully but couldn't be set started on project. This results in outdated project object data. These errors must be corrected by appropriate methods in project object.
		throws awex_mrp_resource on other resource errors. Job might not have been stopped on resource.
		throws awex_mrp_job on other errors.
**/
	function acontinue($comment = "")
	{
		$this->load_data();

		// verify state
		$applicable_states = array (
			self::STATE_ABORTED
		);
		if (!in_array ($this->mrp_state, $applicable_states))
		{
			throw new awex_mrp_job_state("Invalid job state '{$this->mrp_state}' for continuing from abort");
		}

		try
		{
			// reserve resource
			$this->mrp_resource->reserve($this->ref()); //!!! kas reserveerida enne kontrolle v6i p2rast?

			### check if prerequisites are done
			if (!$this->job_prerequisites_are_done())
			{
				throw new awex_mrp_job_prerequisites("A prerequisite is not done.");
			}

			// start making changes to data
			$this->save_mrp_state("start");
			$saving = false;

			### start job
			$this->set_prop ("state", self::STATE_INPROGRESS);

			// update job in project
			$this->mrp_case->update_progress($this->ref());

			// start on resource
			$this->mrp_resource->start_job($this->ref());

			### all went well, save
			$saving = true;
			$this->save ();

			### log
			$this->state_changed($comment);
			$this->stats_start();
		}
		catch (awex_mrp_job_prerequisites $e)
		{
			$this->mrp_resource->cancel_reservation($this->ref());
			throw $e;
		}
		catch (awex_mrp_resource_unavailable $e)
		{
			$this->mrp_resource->cancel_reservation($this->ref());
			throw $e;
		}
		catch (awex_mrp_case_state $e)
		{
			$this->mrp_resource->cancel_reservation($this->ref());
			$this->restore_mrp_state("start", $saving);
			throw $e;
		}
		catch (awex_mrp_case $e)
		{
			$this->mrp_resource->cancel_reservation($this->ref());
			$this->restore_mrp_state("start", $saving);
			throw $e;
		}
		catch (awex_mrp_resource $e)
		{
			$this->mrp_resource->stop_job($this->ref());
			$this->restore_mrp_state("start", $saving);
			throw $e;
		}
		catch (Exception $E)
		{
			$error_message = "Unknown error (" . get_class($E) . "): " . $E->getMessage();
			$e = new awex_mrp_job($error_message);
			$e->set_forwarded_exception($E);

			// restore job state
			$this->restore_mrp_state("start", $saving);

			### free resource and exit
			$this->mrp_resource->cancel_reservation($this->ref());
			throw $e;
		}
	}

/** Pauses the job for shift end. Job must be in progress.
    @attrib api=1 params=pos
	@returns void
	@comment User will be logged out when calling this method.
	@errors
		throws awex_mrp_job_state when current job state doesn't allow pausing.
		throws awex_mrp_job on other errors.
	**/
	function end_shift ($comment = "")
	{
		try
		{
			### pause job
			$this->pause_state = self::STATE_SHIFT_CHANGE;
			$this->pause ($comment);
			### log out user
			$u = get_instance("users");
			$u->logout();
		}
		catch (awex_mrp_job $e)
		{
			throw $e;
		}
		catch (Exception $E)
		{
			$error_message = "Unknown error (" . get_class($e) . "): " . $e->getMessage();
			$e = new awex_mrp_job($error_message);
			$e->set_forwarded_exception($E);
			throw $e;
		}
	}


/**
    @attrib api=1 params=pos
	@returns bool
		Whether all applicable job prerequisite jobs are done
	@errors
		throws awex_mrp_job on any error
**/
	public function job_prerequisites_are_done()
	{
		try
		{
			$applicable_states = array (
				self::STATE_DONE,
				self::STATE_CANCELED,
				self::STATE_DELETED
			);

			$prerequisites = $this->awobj_get_prerequisites()->arr();
			foreach ($prerequisites as $prerequisite)
			{
				if ($prerequisite->status() != object::STAT_DELETED and !in_array ($prerequisite->prop ("state"), $applicable_states))
				{
					return false;
				}
			}
			return true;
		}
		catch (Exception $E)
		{
			$error_message = "Couln't determine prerequisites status. Unknown error (" . get_class($E) . "): " . $E->getMessage();
			$e = new awex_mrp_job($error_message);
			$e->set_forwarded_exception($E);
			throw $e;
		}
	}


/**
    @attrib api=1 params=pos
	@param reserve_start type=bool default=false
		If TRUE then all entities that starting this job depends on, are reserved and waiting for this job to start
	@param return_info type=bool default=false
		If TRUE and job can't be started returns comments why
	@returns bool,string
		Whether starting the job is possible, textual comments in current language if return_info set and can't start
**/
	function can_start ($reserve_start = false, $return_info = false)
	{
		$info = "";

		try
		{
			$this->load_data();
		}
		catch (Exception $e)
		{
			if ($return_info)
			{
				$info .= t("Andmete laadimine ei &otilde;nnestunud.");
				return $info;
			}
			else
			{
				return false;
			}
		}

		### check if project is ready to go on
		$applicable_states = array (
			mrp_case_obj::STATE_INPROGRESS,
			mrp_case_obj::STATE_PLANNED
		);

		if (!in_array ($this->mrp_case->prop ("state"), $applicable_states))
		{
			if ($return_info)
			{
				$info .= t("Projekti staatus sobimatu (" . mrp_case_obj::get_state_names($this->mrp_case->prop ("state")) . "). ");
			}
			else
			{
				return false;
			}
		}

		### check if job can start
		$applicable_states = array (
			self::STATE_PLANNED,
			self::STATE_ABORTED
		);

		if (!in_array ($this->mrp_state, $applicable_states))
		{
			if ($return_info)
			{
				$info .= t("Staatus sobimatu (" . self::get_state_names($this->mrp_state) . "). ");
			}
			else
			{
				return false;
			}
		}

		### check if resource is available
		if (!$reserve_start and !$this->mrp_resource->is_available())
		{
			if ($return_info)
			{
				$info .= t("Ressurss kinni. ");
			}
			else
			{
				return false;
			}
		}

		### check if all prerequisite jobs are done or done in sufficient quantity
		if (!$this->check_prerequisites_allow_continue())
		{
			if ($return_info)
			{
				$info .= t("Eeldust&ouml;&ouml;d tegemata. ");
			}
			else
			{
				return false;
			}
		}

		return $info === "" ? true : $info;
	}

	private function check_prerequisites_allow_continue()
	{
		$allow = false;
		try
		{
			$allow = $this->job_prerequisites_are_done();

			if (!$allow)
			{
				$prerequisites = $this->awobj_get_prerequisites()->arr();
				$allow = true;
				foreach ($prerequisites as $prerequisite)
				{
					if (
						// prerequisite that requires whole order to be done to continue processing workflow, is not done
						$prerequisite->prop("min_batches_to_next_resource") < 1 and
						$prerequisite->prop("state") != self::STATE_DELETED and
						$prerequisite->prop("state") != self::STATE_CANCELED and
						$prerequisite->prop("state") != self::STATE_DONE
							or
						// prereq that has less done than needed to allow continuing
						$prerequisite->prop("state") != self::STATE_DELETED and
						$prerequisite->prop("state") != self::STATE_CANCELED and
						$prerequisite->prop("done") < $prerequisite->prop ("batch_size")*$prerequisite->prop("min_batches_to_continue_wf")
					)
					{
						$allow = false;
						break;
					}
				}
			}
		}
		catch (Exception $e)
		{
			$allow = false;
		}
		return $allow;
	}

	function delete($full_delete = false)
	{
		try
		{
			$this->load_data();
			### job states that require freeing resource
			$applicable_states = array (
				self::STATE_INPROGRESS,
				self::STATE_SHIFT_CHANGE,
				self::STATE_PAUSED
			);

			if (in_array ($this->mrp_state, $applicable_states) and $this->mrp_resource->is_processing($this->ref()))
			{
				### free resource
				$this->mrp_resource->stop_job($this->ref());
				$this->mrp_resource->cancel_reservation($this->ref()); // just in case of any errors
			}

			$this->set_prop ("state", self::STATE_DELETED);
			$this->save ();

			### set successive jobs' prerequisites equal to deleted job's prerequisites
			$list = new object_list (array (
				"class_id" => CL_MRP_JOB,
				"project" => $this->mrp_case->id(),
				"state" => new obj_predicate_not (self::STATE_DELETED)
			));
			$case_jobs = $list->arr ();
			$prerequisites = $this->awobj_get_prerequisites()->ids();

			foreach ($case_jobs as $other_job)
			{
				$other_job_prerequisites = $other_job->prop ("prerequisites")->ids();

				if (in_array ($this->id(), $other_job_prerequisites))
				{
					$successor_prerequisites = array_merge ($other_job_prerequisites, $prerequisites);
					$successor_prerequisites = array_unique ($successor_prerequisites);

					### remove deleted job from prerequisites
					$keys = array_keys ($successor_prerequisites, $this->id ());

					foreach ($keys as $key)
					{
						unset ($successor_prerequisites[$key]);
					}

					### ...
					$other_job->set_prop ("prerequisites", new object_list(array("oid" => $successor_prerequisites)));
					$other_job->save ();
				}
			}

			### correct project's job order
			$i = get_instance(CL_FILE);
			$i->do_orb_method_call (array (
				"action" => "order_jobs",
				"class" => "mrp_case",
				"params" => array (
					"oid" => $this->mrp_case->id ()
				)
			));
//!!! ei saa j2tta workflowd katkiseks
			$applicable_planning_states = array(
				mrp_case_obj::STATE_INPROGRESS,
				mrp_case_obj::STATE_PLANNED
			);

			if (in_array ($this->mrp_case->prop("state"), $applicable_planning_states))
			{
				### post rescheduling msg
				$this->mrp_workspace->request_rescheduling();
			}
		}
		catch (awex_mrp_resource $e)
		{
			throw $e;
		}
		catch (Exception $e)
		{
		}

		return parent::delete($full_delete);
	}

/** Adds a comment to job
    @attrib api=1 params=pos
	@param comment required type=string
	@returns void
**/
	public function add_comment($comment)
	{
		if (strlen(trim($comment)))
		{
			$hist = safe_array($this->meta("change_comment_history"));
			array_unshift($hist, array(
				"tm" => time(),
				"uid" => aw_global_get("uid"),
				"text" => trim($comment)
			));
			$this->set_meta("change_comment_history", $hist);

			$workspace_i = get_instance(CL_MRP_WORKSPACE);
			$workspace_i->mrp_log($this->prop("project"), $this->id(), t("Lisas kommentaari"), $comment);
		}
	}

	private function state_changed($comment)
	{
		$ws = new mrp_workspace();
		$com_txt = "T&ouml;&ouml; ".$this->name()." staatus muudeti ".$this->get_state_names($this->prop("state"));
		$ws->mrp_log($this->prop("project"), $this->id(), $com_txt, $comment);
		$this->add_comment($com_txt." ".$comment); //!!! milleks topeltkommentaar?
	}

	function log_state_change()
	{
		$i = $this->instance();
		$r = $i->db_fetch_row("SELECT aw_pid,aw_tm,aw_job_state FROM mrp_job_rows WHERE aw_job_id = '".$this->id()."' ORDER BY aw_tm DESC, aw_row_id DESC LIMIT 1");
		// Log only if state is changed or new object
		if($r["aw_job_state"] != $this->prop("state") || !$r)
		{
			$current_person_oid = get_current_person()->id();
			$last_duration = isset($r["aw_tm"]) ? time() - $r["aw_tm"] : 0;
			$prev_state = isset($r["aw_job_state"]) ? $r["aw_job_state"] : self::STATE_NEW;
			$i->db_query("
				INSERT INTO mrp_job_rows
					(aw_job_id, aw_case_id, aw_resource_id, aw_uid, aw_uid_oid, aw_previous_pid, aw_pid, aw_job_previous_state, aw_job_state, aw_job_last_duration, aw_tm)
				VALUES
					('".$this->id()."', '".$this->prop("project")."', '".$this->prop("resource")."', '".aw_global_get("uid")."', '".aw_global_get("uid_oid")."', '".$r["aw_pid"]."', '".$current_person_oid."', '".$prev_state."', '".$this->prop("state")."', '".$last_duration."', '".time()."')
			");

			if(in_array($this->prop("state"), self::$states_for_logging_workers))
			{
				$this->connect(array(
					"type" => "RELTYPE_PERSON",
					"to" => $current_person_oid
				));
			}
		}
	}

	private function stats_start()
	{
		$case = $this->prop("project");
		$res = $this->prop("resource");
		$job_id = $this->id();
		$uid = aw_global_get("uid");
		$start = time();
		$p = get_current_person();
		$person_name = $p->name();

		$cnt = $this->instance()->db_fetch_field("SELECT count(*) as cnt FROM mrp_stats
			WHERE
				case_oid = {$case} AND
				resource_oid = {$res} AND
				job_oid = {$job_id} AND
				uid = '{$uid}'",
			"cnt");
		if ($cnt == 0)
		{
			$this->instance()->db_query("INSERT INTO mrp_stats(
				case_oid, resource_oid, job_oid, uid, start, end, length, last_start, person_name
			)
			VALUES(
				{$case}, {$res}, {$job_id}, '{$uid}', {$start}, NULL, 0, {$start}, '{$person_name}'
			)");
		}
		else
		{
			// start after pause
			$this->instance()->db_query("UPDATE mrp_stats SET
				last_start = {$start}
				WHERE
				case_oid = {$case} AND resource_oid = {$res} AND job_oid = {$job_id} AND uid = '{$uid}'"
			);
		}
	}

	private function stats_done()
	{
		$case = $this->prop("project");
		$res = $this->prop("resource");
		$job_id = $this->id();
		$uid = aw_global_get("uid");
		$tm = time();
		$q = "SELECT * FROM mrp_stats WHERE case_oid = {$case} AND resource_oid = {$res} AND job_oid = {$job_id} AND uid = '{$uid}'";
		$row = $this->instance()->db_fetch_row($q);
		if (!$row)
		{
			$fp = fopen(aw_ini_get("site_basedir")."/files/mrp_stats.txt", "a");
			fwrite($fp, date("d.m.Y H:i:s").": stats_done($job_id): no row for $case, $res, $job_id, $uid\n");
			fclose($fp);
			return;
		}
		$this->instance()->db_query("UPDATE mrp_stats SET
			end = {$tm}, length = length + ({$tm} - last_start), last_start = null
			WHERE
			case_oid = {$case} AND resource_oid = {$res} AND job_oid = {$job_id} AND uid = '{$uid}'"
		);
	}

/** Adds a prerequisite job to this job
    @attrib api=1 params=pos
	@param job required type=CL_MRP_JOB
	@returns void
**/
	public function add_prerequisite(object $job)
	{
		$prerequisites_list = $this->awobj_get_prerequisites();
		$prerequisites_list->add($job);
		$this->awobj_set_prerequisites($prerequisites_list);
	}

/** Removes a prerequisite job from this job
    @attrib api=1 params=pos
	@param job required type=CL_MRP_JOB
	@returns void
**/
	public function remove_prerequisite(object $job)
	{
		$prerequisites_list = $this->awobj_get_prerequisites();
		$prerequisites_list->remove($job);
		$this->awobj_set_prerequisites($prerequisites_list);
	}

/** Retrieves list of prerequisite jobs
    @attrib api=1 params=pos
	@returns object_list of CL_MRP_JOB objects
	@errors
		throws awex_mrp_job_data when no permissions to access one of prerequisite jobs or data is corrupt
**/
	public function awobj_get_prerequisites()
	{
		$prerequisites_raw =  (string) parent::prop("prerequisites");
		$prerequisite_oids = empty($prerequisites_raw) ? array() : explode(",", $prerequisites_raw);
		if (count($prerequisite_oids))
		{
			foreach ($prerequisite_oids as $key => $prerequisite_oid)
			{
				if (empty($prerequisite_oid))
				{
					unset($prerequisite_oids[$key]);
				}
			}
			$prerequisites_list = new object_list(array(
				"class_id" => CL_MRP_JOB,
				"oid" => $prerequisite_oids,
				"site_id" => array(),
				"lang_id" => array()
			));

			if ($prerequisites_list->count() !== count($prerequisite_oids))
			{
				// go over the array and let an acl exception be thrown when permissions are missing. otherwise assume that definition is corrupt
				foreach ($prerequisite_oids as $key => $prerequisite_oid)
				{
					if (is_oid($prerequisite_oid))
					{
						$tmp = obj($prerequisite_oid, array(), CL_MRP_JOB);
					}
				}

				$e = new awex_mrp_job_data("Prerequisites definition corrupt");
				$e->awobj_id = $this->id();
				throw $e;
			}
		}
		else
		{
			$prerequisites_list = new object_list();
		}

		return $prerequisites_list;
	}

/** Sest list of jobs as prerequisites for this job
    @attrib api=1 params=pos
	@param prerequisites_list type=object_list
		List of CL_MRP_JOB objects
	@returns void
	@errors
		throws awex_obj_type if object list contains object(s) that are not jobs.
		throws awex_mrp_job_data when project not set (code 2)
		throws awex_mrp_job_prerequisites when one of given prerequisites not among case jobs
**/
	public function awobj_set_prerequisites(object_list $prerequisites_list)
	{ // internal format -- ',job1id,job2id,'
		$prerequisites_raw = "";
		if ($prerequisites_list->count())
		{
			$project = $this->prop("project");
			if (!is_oid($this->prop("project")))
			{
				throw new awex_mrp_job_data("Project not defined", 2);
			}
			$project = obj($project, array(), CL_MRP_CASE);
			$project_jobs = $project->get_job_list();

			foreach ($prerequisites_list->arr() as $prerequisite_job)
			{
				if (CL_MRP_JOB != $prerequisite_job->class_id())
				{
					throw new awex_obj_type("One of given prerequisite objects is not a CL_MRP_JOB object");
				}
				elseif (!isset($project_jobs[$prerequisite_job->id()]))
				{
					throw new awex_mrp_job_prerequisites("One of given prerequisite objects is not among case jobs");
				}
			}
			$prerequisite_oids =  array_unique($prerequisites_list->ids());
			$prerequisites_raw = "," . implode(",", $prerequisite_oids) .  ",";
		}
		parent::set_prop("prerequisites", $prerequisites_raw);
		$this->request_rescheduling();
	}

	public function awobj_set_state($value)
	{
		throw new awex_obj_readonly("State is a read-only property");
	}

	public function awobj_set_length($value)
	{
		$r = parent::set_prop("length", (int) $value);
		$this->request_rescheduling();
		return $r;
	}

	public function awobj_set_post_buffer($value)
	{
		$r = parent::set_prop("post_buffer", (int) $value);
		$this->request_rescheduling();
		return $r;
	}

	public function awobj_set_pre_buffer($value)
	{
		$r = parent::set_prop("pre_buffer", (int) $value);
		$this->request_rescheduling();
		return $r;
	}

	public function awobj_set_minstart($value)
	{
		$r = parent::set_prop("minstart", (int) $value);
		$this->request_rescheduling();
		return $r;
	}

	public function awobj_set_resource($resource_id)
	{
		$this->change_name = true;
		$resource = obj($resource_id, array(), CL_MRP_RESOURCE);
		return parent::set_prop("resource", $resource_id);
	}

	public function awobj_set_project($project_id)
	{
		$this->change_name = true;
		$project = obj($project_id, array(), CL_MRP_CASE);
		$this->connect (array (
			"to" => $project,
			"reltype" => "RELTYPE_MRP_PROJECT"
		));
		return parent::set_prop("project", $project_id);
	}

	protected function request_rescheduling()
	{
		if (in_array ($this->prop ("state"), self::$planning_states))
		{
			$this->get_workspace()->request_rescheduling();
		}
	}

	protected function save_mrp_state($state_id)
	{
		$this->mrp_job_state_data[$state_id] = array(
			"state" => $this->prop("state"),
			"started" => $this->prop("started")
		);
	}

	protected function restore_mrp_state($state_id, $save = false)
	{
		if (!isset($this->mrp_job_state_data[$state_id]))
		{
			throw new awex_mrp_job("No state with this id saved.");
		}

		foreach ($this->mrp_job_state_data[$state_id] as $propname => $value)
		{
			$this->set_prop($propname, $value);
		}

		if ($save)
		{
			$this->save();
		}

		$res = obj($this->prop("resource"));
		if($res)
		{
			$ws = $res->get_first_obj_by_reltype("RELTYPE_MRP_OWNER");
		}
		if($ws && $this->prop("state") == self::STATE_PLANNED)
		{
			$conn = $ws->connections_to(array(
				"from.class_id" => CL_SHOP_PURCHASE_MANAGER_WORKSPACE,
			));
			foreach($conn as $c)
			{
				$c->from()->update_job_order($this);
			}
		}
	}

	private function set_used_material_base_amount($arr)
	{
		$po = obj($arr["product"]);
		$units = $po->instance()->get_units($po);
		$unit = reset($units);
		if($arr["unit"] && $arr["unit"] != $unit)
		{
			$ufi = obj();
			$ufi->set_class_id(CL_SHOP_UNIT_FORMULA);
			$fo = $ufi->get_formula(array(
				"from_unit" => $arr["unit"],
				"to_unit" => $unit,
				"product" => $po,
			));
			if($fo)
			{
				$amt = round($ufi->calc_amount(array(
					"amount" => $arr["amount"],
					"prod" => $po,
					"obj" => $fo,
				)), 3);
			}
		}
		else
		{
			$amt = $arr["amount"];
		}
		$arr["obj"]->set_prop("base_amount", $amt);
	}

	/**
		@attrib name=get_person_work_hours api=1 params=name

		@param from optional type=int
			UNIX timestamp
		@param to optional type=int
			UNIX timestamp
		@param state optional type=int/array
			The state(s) of job to return the hours for
		@param person optional type=int/array
			The OID(s) of crm_person to return the hours for
		@param person_handling optional type=int default=PRSN_HNDL_S
			How to use [person param]?
			PRSN_HNDL_S - [person param] was the one to change the job's state to [state param]
			PRSN_HNDL_F - [person param] was the one to change the job's state from [state param]
			PRSN_HNDL_S_OR_F - PRSN_HNDL_S or PRSN_HNDL_F
			PRSN_HNDL_S_AND_F - PRSN_HNDL_S and PRSN_HNDL_F
		@param job optional type=int/array
			The OID(s) of mrp_job to return the hours fo
		@param by_job optional type=boolean default=false
		@param average optional type=boolean
		@param count optional type=boolean
		@param convert_to_hours optional type=boolean default=true
		@returns Array of work hours by person
		@comment Output format:
			Array
			(
				[[job status]] => Array
				(
					[{person object OID}] => {time in seconds}
				)
				::optional::
				[average] => Array
				(
					[[job status]] => Array
					(
						[{person object OID}] => {time in seconds}
					)
				)
				[count] => Array
				(
					[[job status]] => Array
					(
						[{person object OID}] => {time in seconds}
					)
				)
				::optional::
			)
	**/
	public static function get_person_hours($arr)
	{
		$i = get_instance(CL_MRP_JOB);

		$states = isset($arr["state"]) ? (array)$arr["state"] : array(self::STATE_INPROGRESS, self::STATE_PAUSED);

		// Initialize $data
		$data = array();
		foreach($states as $state)
		{
			$data[$state] = array();
			if(!empty($arr["average"]))
			{
				$data["average"][$state] = array();
			}
			if(!empty($arr["count"]))
			{
				$data["count"][$state] = array();
			}
		}

		$arr["person_handling"] = isset($arr["person_handling"]) ? $arr["person_handling"] : self::PRSN_HNDL_S;
		$arr["person"] = isset($arr["person"]) ? (is_oid($arr["person"]) ? (array)$arr["person"] : safe_array($arr["person"])) : array();

		if($arr["person_handling"] == self::PRSN_HNDL_S_OR_F)
		{
			// First, get the hours the person started
			$persons = count($arr["person"]) > 0 ? "aw_previous_pid IN (".implode(",", $arr["person"]).") AND" : "";
			$q = $i->db_fetch_array(self::something_hours_build_query($arr, "aw_previous_pid", "pid", $persons));
			self::something_hours_insert_data($q, "pid", $data, $arr);

			// Now, get the hours the person finished, but DIDN'T start
			$persons = count($arr["person"]) > 0 ? "aw_pid IN (".implode(",", $arr["person"]).") AND aw_pid != aw_previous_pid AND" : "";
			$q = $i->db_fetch_array(self::something_hours_build_query($arr, "aw_pid", "pid", $persons));
			self::something_hours_insert_data($q, "pid", $data, $arr);
		}
		else
		{
			switch($arr["person_handling"])
			{
				default:
				case self::PRSN_HNDL_S:
					$persons = count($arr["person"]) > 0 ? "aw_previous_pid IN (".implode(",", $arr["person"]).") AND" : "";
					$field = "aw_previous_pid";
					break;

				case self::PRSN_HNDL_F:
					$persons = count($arr["person"]) > 0 ? "aw_pid IN (".implode(",", $arr["person"]).") AND" : "";
					$field = "aw_pid";
					break;

				case self::PRSN_HNDL_S_AND_F:
					$persons = count($arr["person"]) > 0 ? "aw_pid IN (".implode(",", $arr["person"]).") AND aw_pid = aw_previous_pid AND" : "";
					$field = "aw_pid";
					break;
			}

			$q = $i->db_fetch_array(self::something_hours_build_query($arr, $field, "pid", $persons));
			self::something_hours_insert_data($q, "pid", $data, $arr);
		}

		return $data;
	}

	/**
		@attrib name=get_resource_hours api=1 params=name

		@param from optional type=int
			UNIX timestamp
		@param to optional type=int
			UNIX timestamp
		@param state optional type=int/array
			The state(s) of job to return the hours for
		@param resource optional type=int/array
			The OID(s) of mrp_resource to return the hours for
		@param job optional type=int/array
			The OID(s) of mrp_job to return the hours for
		@param average optional type=boolean

		@param count optional type=boolean

		@param convert_to_hours optional type=boolean default=true


		@returns Array of work hours by person or FALSE on failure.

		@comment Output format:
			Array
			(
				[[job status]] => Array
				(
					[{resource object OID}] => {time in seconds}
				)
				::optional::
				[average] => Array
				(
					[[job status]] => Array
					(
						[{resource object OID}] => {time in seconds}
					)
				)
				[count] => Array
				(
					[[job status]] => Array
					(
						[{resource object OID}] => {count}
					)
				)
				::optional::
			)
	**/
	public static function get_resource_hours($arr)
	{
		$i = get_instance(CL_MRP_JOB);

		$states = isset($arr["state"]) ? (array)$arr["state"] : array(self::STATE_INPROGRESS, self::STATE_PAUSED);

		// Initialize $data
		$data = array();
		foreach($states as $state)
		{
			$data[$state] = array();
			if(!empty($arr["average"]))
			{
				$data["average"][$state] = array();
			}
			if(!empty($arr["count"]))
			{
				$data["count"][$state] = array();
			}
		}

		$resources = array();
		if (isset($arr["resource"]))
		{
			if (is_array($arr["resource"]))
			{
				foreach ($arr["resource"] as $resource)
				{
					if (is_oid($resource))
					{
						$resources[] = $resource;
					}
				}
			}
			elseif (is_oid($arr["resource"]))
			{
				$resources = array($arr["resource"]);
			}
		}

		$arr["resource"] = $resources;
		if (count($resources))
		{
			$resources = "aw_resource_id IN (".implode(",", $resources).") AND";
		}
		else
		{
			$resources = "";
		}

		$q = $i->db_fetch_array(self::something_hours_build_query($arr, "aw_resource_id", "resource_id", $resources));
		self::something_hours_insert_data($q, "resource_id", $data, $arr);

		return $data;
	}

	private static function something_hours_build_query($arr, $field, $key, $additional)
	{
		$states = isset($arr["state"]) ? (array)$arr["state"] : array(self::STATE_INPROGRESS, self::STATE_PAUSED);
		$from = (int)(isset($arr["from"]) ? $arr["from"] : 0);
		$to = (int)(isset($arr["to"]) ? $arr["to"] : time());
		$c2h = !isset($arr["convert_to_hours"]) || !empty($arr["convert_to_hours"]) ? "/3600" : "";
		$count = !empty($arr["count"]) ? "COUNT(*) as cnt," : "";
		$average = !empty($arr["average"]) ? "AVG(aw_job_last_duration){$c2h} as avg," : "";

		$arr["job"] = isset($arr["job"]) ? (is_oid($arr["job"]) ? (array)$arr["job"] : safe_array($arr["job"])) : array();
		$jobs = count($arr["job"]) > 0 ? "aw_job_id IN (".implode(",", $arr["job"]).") AND" : "";

		$by_job = empty($arr["by_job"]) ? "" : "aw_job_id,";
		$select_job = empty($arr["by_job"]) ? "" : "aw_job_id as job_id,";

		$query = "
			SELECT
				$select_job
				$count
				$average
				aw_job_previous_state as state,
				SUM(LEAST(aw_tm - $from, aw_job_last_duration, $to - aw_tm + aw_job_last_duration)){$c2h} as hours,
				$field as $key
			FROM
				mrp_job_rows
			WHERE
				$additional
				$jobs
				aw_job_previous_state IN('".implode("','", $states)."') AND
				(aw_tm BETWEEN $from AND {$to} OR aw_tm - aw_job_last_duration < $to AND aw_tm > $from)
			GROUP BY aw_job_previous_state, {$by_job} $field
		";
		return $query;
	}

	private static function something_hours_insert_data($q, $key, &$data, $arr)
	{
		$states = isset($arr["state"]) ? (array)$arr["state"] : array(self::STATE_INPROGRESS, self::STATE_PAUSED);
		$by_job = !empty($arr["by_job"]);

		foreach($q as $d)
		{
			// Hours
			if(isset($data[$d["state"]][$d[$key]]) && !$by_job || $by_job && isset($data[$d["state"]][$d[$key]][$d["job_id"]]))
			{
				$by_job ? $data[$d["state"]][$d[$key]][$d["job_id"]] += $d["hours"] : $data[$d["state"]][$d[$key]] += $d["hours"];
			}
			else
			{
				$by_job ? $data[$d["state"]][$d[$key]][$d["job_id"]] = $d["hours"] : $data[$d["state"]][$d[$key]] = $d["hours"];
			}

			// Average
			if(isset($d["avg"]) && (isset($data["average"][$d["state"]][$d[$key]]) && !$by_job || $by_job && isset($data["average"][$d["state"]][$d[$key]][$d["job_id"]])))
			{
				$by_job ? $data["average"][$d["state"]][$d[$key]][$d["job_id"]] += $d["avg"] : $data["average"][$d["state"]][$d[$key]] += $d["avg"];
			}
			elseif(isset($d["avg"]))
			{
				$by_job ? $data["average"][$d["state"]][$d[$key]][$d["job_id"]] = $d["avg"] : $data["average"][$d["state"]][$d[$key]] = $d["avg"];
			}

			// Count
			if(isset($d["cnt"]) && (isset($data["count"][$d["state"]][$d[$key]]) && !$by_job || $by_job && isset($data["count"][$d["state"]][$d[$key]][$d["job_id"]])))
			{
				$by_job ? $data["count"][$d["state"]][$d[$key]][$d["job_id"]] += $d["cnt"] : $data["count"][$d["state"]][$d[$key]] += $d["cnt"];
			}
			elseif(isset($d["cnt"]))
			{
				$by_job ? $data["count"][$d["state"]][$d[$key]][$d["job_id"]] = $d["cnt"] : $data["count"][$d["state"]][$d[$key]] = $d["cnt"];
			}

			// Initialize others
			foreach($states as $_state)
			{
				if($d["state"] !== $_state && (!isset($data[$_state][$d[$key]])) && !$by_job || $by_job && !isset($data[$_state][$d[$key]][$d["job_id"]]))
				{
					$by_job ? $data[$_state][$d[$key]][$d["job_id"]] = 0 : $data[$_state][$d[$key]] = 0;
					$by_job ? $data["average"][$_state][$d[$key]][$d["job_id"]] = 0 : $data["average"][$_state][$d[$key]] = 0;
					$by_job ? $data["count"][$_state][$d[$key]][$d["job_id"]] = 0 : $data["count"][$_state][$d[$key]] = 0;
				}
			}
		}
	}

	/**
		@attrib name=get_material_expenses params=name

		@param id required type=int/array

		@param odl optional type=bool default=false

	**/
	public static function get_material_expenses($arr)
	{
		$prms = array(
			"class_id" => CL_MATERIAL_EXPENSE,
			"lang_id" => array(),
			"site_id" => array(),
			// The array(-1) is for the case if id param is empty.
 			"job" => array_merge(array(-1), (array)$arr["id"]),
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

	public function get_progress()
	{
		$progress = $this->get_progress_for_job_id(array(
			"job" => $this->id(),
		));
		return reset($progress);
	}

	/**
		@attrib name=get_progress_for_id api=1 params=name

		@param job optional type=int/array

		@param case optional type=int/array

		@param resource optional type=int/array

		@param person optional type=int/array

		@param from optional type=int

		@param to optional type=int

		@param groupby optional type=string/array default=job
			Can be string e.g. "resource" or array e.g. array("resource", "job").
			All possible values: job, case, person.
	**/
	public static function get_progress_for_params($arr)
	{
		$q = "
			SELECT
				%s
			FROM
				mrp_job_progress
			WHERE
				%s
			GROUP BY
				%s
		";

		$select = "SUM(aw_quantity) as quantity";
		if(empty($arr["groupby"]))
		{
			$arr["groupby"] = "aw_job_id";
		}
		$groupby_fields = array();
		foreach((array)$arr["groupby"] as $groupby_element)
		{
			$groupby = empty($groupby) ? "" : $groupby.",";
			switch($groupby_element)
			{
				case "job":
					$groupby .= "aw_job_id";
					$select .= ",aw_job_id";

					$groupby_fields[] = "aw_job_id";
					break;

				case "case":
					$groupby .= "aw_case_id";
					$select .= ",aw_case_id";

					$groupby_fields[] = "aw_case_id";
					break;

				case "person":
					$groupby .= "aw_pid_oid";
					$select .= ",aw_pid_oid";

					$groupby_fields[] = "aw_pid_oid";
					break;

				case "resource":
					$groupby .= "aw_resource_id";
					$select .= ",aw_resource_id";

					$groupby_fields[] = "aw_resource_id";
					break;

				case "month":
					$groupby .= "year(from_unixtime(aw_entry_time)),month(from_unixtime(aw_entry_time))";
					$select .= ",year(from_unixtime(aw_entry_time)),month(from_unixtime(aw_entry_time))";

					$groupby_fields[] = "year(from_unixtime(aw_entry_time))";
					$groupby_fields[] = "month(from_unixtime(aw_entry_time))";
					break;

				case "week":
					$groupby .= "year(from_unixtime(aw_entry_time)),week(from_unixtime(aw_entry_time), 3)";
					$select .= ",year(from_unixtime(aw_entry_time)),week(from_unixtime(aw_entry_time), 3)";

					$groupby_fields[] = "year(from_unixtime(aw_entry_time))";
					$groupby_fields[] = "week(from_unixtime(aw_entry_time), 3)";
					break;
			}
		}

		$where = "";
		// Handle the OIDs
		$possible_fields = array(
			"job" => "aw_job_id",
			"case" => "aw_case_id",
			"resource" => "aw_resource_id",
			"person" => "aw_pid_oid",
		);
		foreach($possible_fields as $possible_field => $table_field)
		{
			if(!empty($arr[$possible_field]))
			{
				if(strlen($where) > 0)
				{
					$where .= " AND ";
				}
				$where .= $table_field." IN (".implode(",", (array)$arr[$possible_field]).")";
			}
		}

		// Handle the timespan
		if(isset($arr["from"]) && isset($arr["to"]))
		{
			if(strlen($where) > 0)
			{
				$where .= " AND ";
			}
			$where .= "aw_entry_time BETWEEN '".$arr["from"]."' AND '".$arr["to"]."'";
		}
		elseif(isset($arr["from"]))
		{
			if(strlen($where) > 0)
			{
				$where .= " AND ";
			}
			$where .= "aw_entry_time >= '".$arr["from"]."'";
		}
		elseif(isset($arr["to"]))
		{
			if(strlen($where) > 0)
			{
				$where .= " AND ";
			}
			$where .= "aw_entry_time <= '".$arr["to"]."'";
		}

		if(empty($where))
		{
			$where = "1=1";
		}

		$job = new mrp_job;
		$rows = $job->db_fetch_array(sprintf($q, $select, $where, $groupby));

		$ret = array();
		foreach($rows as $row)
		{
			eval('$ret[$row["'.implode('"]][$row["', $groupby_fields).'"]] = $row["quantity"];');
		}

		return $ret;
	}

	public function is_finished()
	{
		return (bool) $this->prop("finished");
	}
}

/** Generic job exception **/
class awex_mrp_job extends awex_obj {}

/** Indicates a problem with internal data integrity **/
class awex_mrp_job_data extends awex_mrp_job
{
	/** Informative keyword for debugging or error handling **/
	public $info;
}

/** Indicates a problem with job prerequisite jobs **/
class awex_mrp_job_prerequisites extends awex_mrp_job {}

/** Indicates a type mismatch. A value is of unexpected type **/
class awex_mrp_job_type extends awex_mrp_job {}

/** Job state doesn't allow this operation **/
class awex_mrp_job_state extends awex_mrp_job {}
