<?php

/*
@classinfo  maintainer=voldemar
*/

require_once "mrp_header.aw";

class mrp_workspace_obj extends _int_object
{
	// resource manager types
	const MGR_TYPE_MR = 1; // manufacturing resources
	const MGR_TYPE_HR = 2; // human resources

/**
	@attrib params=pos api=1
	@returns void
	@errors
		awex_mrp_ws_schedule if rescheduling request fails for some reason
**/
	public function request_rescheduling()
	{
		try
		{
			$this->set_prop("rescheduling_needed", 1);
			aw_disable_acl();
			$this->save();
			aw_restore_acl();
		}
		catch (Exception $E)
		{
			$e = new awex_mrp_ws_schedule("Rescheduling request failed");
			$e->set_forwarded_exception($E);
			throw $e;
		}
	}

	public function get_all_mrp_customers($arr = array())
	{
		$filter = array(
			"class_id" => CL_MRP_CASE,
			"CL_MRP_CASE.customer" =>  new obj_predicate_compare(OBJ_COMP_GREATER, 0),
		);
		if($arr["name"])
		{
			$filter["CL_MRP_CASE.customer.name"] = $arr["name"]."%";

		}
		$t = new object_data_list(
			$filter,
			array(
				CL_MRP_CASE=>  array(new obj_sql_func(OBJ_SQL_UNIQUE, "customer", "mrp_case.customer"))
			)
		);

		return $t->get_element_from_all("customer");
	}

	public function get_all_mrp_cases_data()
	{
		$filter = array(
			"class_id" => CL_MRP_CASE,
			"CL_MRP_CASE.customer" =>  new obj_predicate_compare(OBJ_COMP_GREATER, 0),
		);

		$t = new object_data_list(
			$filter,
			array(
				CL_MRP_CASE=>  array("customer")
			)
		);
		return $t->list_data;
	}

	public function set_priors($priors = array())
	{
		$c = $this->prop("owner");
		$o = obj($c);
		foreach($priors as $cust => $p)
		{
			$o->set_customer_prop($cust , "priority" , $p);
		}
	}

	/** returns all used warehouses
		@attrib api=1
		@returns object list
	**/
	public function get_warehouses()
	{
		$filter = array("class_id" => CL_SHOP_WAREHOUSE, "lang_id" => array(), "site_id" => array());
		return new object_list($filter);
	}

	/** returns material expense data
		@attrib api=1
		@param product type=oid
		@param category type=oid
			product category object id
		@param from type=int
			expenses from timestamp
		@param to type=oid
			expenses to timestamp
		@param resource type=oid/array
			resource ids
		@returns array
	**/
	public function get_material_expense_data($arr = array())
	{
		$filter = $this->_get_material_expense_filter($arr);
		$t = new object_data_list(
			$filter,
			array(
				CL_MATERIAL_EXPENSE=>  array("amount" , "used_amount" , "unit", "product","job","job.resource")
			)
		);
		return $t->list_data;
	}

	/** returns material expense data
		@attrib api=1
		@param product type=oid
		@param category type=oid
			product category object id
		@param from type=int
			expenses from timestamp
		@param to type=oid
			expenses to timestamp
		@param resource type=oid/array
			resource ids
		@returns object list
	**/
	public function get_material_expenses($arr = array())
	{
		$filter = $this->_get_material_expense_filter($arr);
		return new object_list($filter);
	}

	private function _get_material_expense_filter($arr)
	{
		$filter = array(
			"class_id" => CL_MATERIAL_EXPENSE,
			"site_id" => array(),
			"lang_id" => array()
		);
		if(isset($arr["product"]) && $arr["product"])
		{
			$filter["product"] = $arr["product"];
		}
		if(isset($arr["category"]) && $arr["category"])
		{
			$filter["product.RELTYPE_CATEGORY"] = $arr["category"];
		}

		if(isset($arr["from"]) && $arr["from"] > 0 && isset($arr["to"]) && $arr["to"] > 0)
		{
			$to += 24 * 60 * 60 -1;
			$filter["RELTYPE_JOB.started"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $arr["from"], $arr["to"]);
		}
		elseif(isset($arr["from"]) && $arr["from"] > 0)
		{
			$filter["RELTYPE_JOB.started"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $arr["from"]);
		}
		elseif(isset($arr["to"]) && $arr["to"] > 0)
		{
			$to += 24 * 60 * 60 -1;
			$filter["RELTYPE_JOB.started"] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $arr["to"]);
		}

		if(isset($arr["resource"]))
		{
			$filter["RELTYPE_JOB.resource"] = $arr["resource"];
		}
		if(isset($arr["people"]))
		{
			$filter["RELTYPE_JOB.RELTYPE_PERSON"] = $arr["people"];
		}

		return $filter;
	}

	public function get_menu_resources($resources_folder)
	{
		$resource_tree_filter = array(
			"parent" => $resources_folder,
			"class_id" => array(CL_MENU, CL_MRP_RESOURCE),
			"sort_by" => "objects.jrk",
		);
		$resource_tree = new object_tree($resource_tree_filter);
		$ids = $resource_tree->ids();
		return $ids;
	}

/** Creates a new project in this resource manager workspace
	@attrib params=pos api=1
	@param customer_relation type=CL_CRM_COMPANY_CUSTOMER_DATA default=null
	@returns CL_MRP_CASE
		Created project
**/
	public function create_case(object $customer_relation = null)
	{
		$case = obj(null, array(), CL_MRP_CASE);
		$case->set_parent($this->id());
		$case->set_prop("workspace", new object($this->id()));
		$case->set_prop("purchasing_manager", $this->prop("purchasing_manager"));
		if (is_object($customer_relation))
		{
			$customer = new object($customer_relation->prop("buyer"));
			$case->set_name(sprintf(t("Kliendi %s projekt"), $customer->name()));
			$case->set_prop("customer", $customer->id());//!!! deprecated soon
			$case->set_prop("customer_relation", $customer_relation->id());
		}
		$case->save();

		if (is_object($customer_relation))
		{
			$case->connect(array("to" => $customer, "type" => "RELTYPE_MRP_CUSTOMER"));//!!! deprecated soon
			$case->connect(array("to" => $customer_relation, "type" => "RELTYPE_CUSTOMER_RELATION"));
		}
		return $case;
	}

/** Returns resource for person in given company. Creates one if not found.
	@attrib params=pos api=1
	@param company type=CL_CRM_COMPANY
	@param person type=CL_CRM_PERSON
	@returns CL_MRP_RESOURCE
**/
	public static function get_person_resource(object $company, object $person)
	{
		$self = self::get_hr_manager($company);
		$list = new object_list(array(
			"class_id" => CL_MRP_RESOURCE,
			"workspace" => $self->id(),
			"CL_MRP_RESOURCE.RELTYPE_CONTAINING_OBJECT" => $person->id(),
			"site_id" => array(),
			"lang_id" => array()
		));
		$resource = $list->begin();

		if (!is_object($resource))
		{
			// create person resource
			$parent = $self->prop("resources_folder");//!!! must check parent's status, that it isn't DELETED.
			$resource = obj(null, array(), CL_MRP_RESOURCE);
			$resource->set_parent($parent);
			$resource->set_name(sprintf(t("Ressurss '%s'"), $person->name()));
			$resource->set_prop("workspace", $self);
			$resource->set_prop("thread_data", 1);
			$resource->set_prop("type", mrp_resource_obj::TYPE_SCHEDULABLE);
			aw_disable_acl();
			$resource->save();
			aw_restore_acl();
			$resource->connect(array("to" => $self, "reltype" => "RELTYPE_MRP_OWNER"));
			$resource->connect(array("to" => $person, "reltype" => "RELTYPE_CONTAINING_OBJECT"));
		}

		return $resource;
	}

/** Returns resource for given profession. Creates one if not found.
	@attrib params=pos api=1
	@param company type=CL_CRM_COMPANY
	@param profession type=CL_CRM_PROFESSION
	@returns CL_MRP_RESOURCE
**/
	public static function get_profession_resource(object $company, object $profession)
	{
		$self = self::get_hr_manager($company);
		$list = new object_list(array(
			"class_id" => CL_MRP_RESOURCE,
			"workspace" => $self->id(),
			"CL_MRP_RESOURCE.RELTYPE_CONTAINING_OBJECT" => $profession->id(),
			"site_id" => array(),
			"lang_id" => array()
		));
		$resource = $list->begin();

		if (!is_object($resource))
		{
			// create person resource
			$num_of_profession_employees = $profession->get_workers(null, false)->count();
			$parent = $self->prop("resources_folder");//!!! must check parent's status, that it isn't DELETED.
			$resource = obj(null, array(), CL_MRP_RESOURCE);
			$resource->set_parent($parent);
			$resource->set_name(sprintf(t("Ameti '%s' ressurss"), $profession->name()));
			$resource->set_prop("workspace", $self);
			$resource->set_prop("thread_data", $num_of_profession_employees);
			$resource->set_prop("type", mrp_resource_obj::TYPE_SCHEDULABLE);
			aw_disable_acl();
			$resource->save();
			aw_restore_acl();
			$resource->connect(array("to" => $self, "type" => "RELTYPE_MRP_OWNER"));
			$resource->connect(array("to" => $profession, "type" => "RELTYPE_CONTAINING_OBJECT"));
		}

		return $resource;
	}

/** Returns system's human resource manager workspace applicable for given company. Creates one if not found.
	@attrib params=pos api=1
	@param company type=CL_CRM_COMPANY
	@returns CL_MRP_WORKSPACE
**/
	public static function get_hr_manager(object $company)
	{
		$list = new object_list(array(
			"class_id" => CL_MRP_WORKSPACE,
			"subclass" => self::MGR_TYPE_HR,
			"CL_MRP_WORKSPACE.RELTYPE_MRP_OWNER" => $company->id(),
			"site_id" => array(),
			"lang_id" => array()
		));
		$ws = $list->begin();

		if (!is_object($ws))
		{
			$list = new object_list(array(
				"class_id" => CL_MRP_WORKSPACE,
				"subclass" => self::MGR_TYPE_HR,
				"site_id" => array(),
				"lang_id" => array()
			));
			$ws = $list->begin();

			if (is_object($ws))
			{ // make first found workspace manage this company's human resources too
				$ws->connect(array("to" => $company, "reltype" => "RELTYPE_MRP_OWNER"));
			}
			else
			{ // create human resource manager
				$parent = aw_ini_get("users.root_folder");
				$ws = obj(null, array(), CL_MRP_WORKSPACE);
				$ws->set_parent($parent);
				$ws->set_subclass(self::MGR_TYPE_HR);
				$ws->set_name(t("Systeemi inimressursside halduskeskkond"));
				aw_disable_acl();
				$ws->save();
				aw_restore_acl();
				$ws->connect(array("to" => $company, "reltype" => "RELTYPE_MRP_OWNER"));

				$projects_folder = obj(null, array(), CL_MENU);
				$projects_folder->set_parent($ws->id());
				$projects_folder->set_name(t("Projektid"));
				$projects_folder->save();
				$resources_folder = obj(null, array(), CL_MENU);
				$resources_folder->set_parent($ws->id());
				$resources_folder->set_name(t("Ressursid"));
				$resources_folder->save();
				$jobs_folder = obj(null, array(), CL_MENU);
				$jobs_folder->set_parent($ws->id());
				$jobs_folder->set_name(t("T&ouml;&ouml;d"));
				$jobs_folder->save();
				$ws->set_prop("projects_folder", $projects_folder->id());
				$ws->set_prop("resources_folder", $resources_folder->id());
				$ws->set_prop("jobs_folder", $jobs_folder->id());
				aw_disable_acl();
				$ws->save();
				aw_restore_acl();
			}
		}

		return $ws;
	}
}

/** Generic workspace error **/
class awex_mrp_ws extends awex_mrp {}

/** Generic workspace scheduling operations error **/
class awex_mrp_ws_schedule extends awex_mrp_ws {}


?>
