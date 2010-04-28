<?php
/*
@classinfo  maintainer=markop
*/
class crm_data extends class_base
{
	function crm_data()
	{
		$this->init();
	}
	
	///////////// BILLS

	/**
		@comment
			co - object of company to return bills for
			filter - array of bill filters
				keys:
					monthly - 1/0 - to return only monthly bills
					bill_no - bill number to search by
					bill_date_range - array("from" => time, "to" => time)
					state - 0 - being created, 1 - sent, 2 - paid
					client_mgr - client manager for bill customer, text
					customer - customer for bill, text
			returns an object_list of bills found
	**/
	function get_bills_by_co($co, $filter = NULL)
	{
		$of = array(
			"class_id" => CL_CRM_BILL,
			"lang_id" => array(),
			"site_id" => array(),
/*			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"parent" => $co->id(),
					"CRM_BILL.impl" => $co->id(),
				)
			)),*/
		);

		if ($filter !== NULL)
		{
			error::raise_if(!is_array($filter), array(
				"id" => "ERR_CRM_PARAM",
				"msg" => sprintf(t("crm_data::get_bills_by_co(): second parameter must be an array, if set!"))
			));

			if (isset($filter["bill_no"]))
			{
				$of["bill_no"] = $filter["bill_no"];
			}
			if (isset($filter["project_mgr"]))
			{
				$of["RELTYPE_PROJECT.proj_mgr"] = $filter["project_mgr"];
			}
			if (isset($filter["monthly"]))
			{
				$of["monthly_bill"] = $filter["monthly"];
			}
			if (isset($filter["bill_date_range"]))
			{
				$r = $filter["bill_date_range"];

				if ($r["from"] > 100 && $r["to"] > 100)
				{
					$of["bill_date"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $r["from"], $r["to"]);
				}
				else
				if ($r["from"] > 100)
				{
					$of["bill_date"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $r["from"]);
				}
				else
				if ($r["to"] > 100)
				{
					$of["bill_date"] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $r["to"]);
				}
			}
			if (isset($filter["state"]) && $filter["state"] != -1)
			{
				$of["CL_CRM_BILL.state"] = $filter["state"];
			}
			if (isset($filter["on_demand"]) && $filter["on_demand"] != -1)
			{
				$of["CL_CRM_BILL.on_demand"] = $filter["on_demand"];
			}
			$of2 = $of;

			if (!empty($filter["client_mgr"]))
			{
				if(is_oid($filter["client_mgr"]))
				{
					$ft = new object_list_filter(array(
						"logic" => "OR",
						"conditions" => array(
							"CL_CRM_BILL.customer(CL_CRM_COMPANY).client_manager" => $filter["client_mgr"],
							"CL_CRM_BILL.customer(CL_CRM_PERSON).client_manager" => $filter["client_mgr"],
							"CL_CRM_BILL.customer.RELTYPE_BUYER(CL_CRM_COMPANY_CUSTOMER_DATA)" => $filter["client_mgr"],
						)
					));
					$of[] = $ft;
					$of2[] = $ft;
				}
				else
				{
					$relist = new object_list(array(
						"class_id" => CL_CRM_COMPANY_ROLE_ENTRY,
						"CL_CRM_COMPANY_ROLE_ENTRY.person.name" => map("%%%s%%", explode(",", $filter["client_mgr"]))
					));
	
					$relist3 = new object_list(array(
						"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
						"CL_CRM_COMPANY_CUSTOMER_DATA.client_manager.name" => map("%%%s%%", explode(",", $filter["client_mgr"]))
					));
					$relist2 = new object_list(array(
						"class_id" => CL_CRM_COMPANY,
						"CL_CRM_COMPANY.client_manager.name" => map("%%%s%%", explode(",", $filter["client_mgr"]))
					));
					$relist -> add($relist3);
					$rs = array();
					foreach($relist->arr() as $o)
					{
						$rs[] = $o->prop("buyer");
					}
					$rs = $rs + $relist2->ids();
					$ft = new object_list_filter(array(
						"logic" => "OR",
						"conditions" => array(
	//						"CL_CRM_BILL.customer(CL_CRM_COMPANY).client_manager.name" => map("%%%s%%", explode(",", $filter["client_mgr"])),
							"CL_CRM_BILL.customer" => $rs,
							"CL_CRM_BILL.customer(CL_CRM_PERSON).client_manager.name" => map("%%%s%%", explode(",", $filter["client_mgr"])),
						)
					));
					$of[] = $ft;
					$of2[] = $ft;
				}
			}

			if (isset($filter["customer"]))
			{
				if(is_oid($filter["customer"]))
				{
					$of["CL_CRM_BILL.customer"] = $filter["customer"];
					$of2["CL_CRM_BILL.customer"] = $filter["customer"];
				}
				else
				{
					if(strlen($filter["customer"]) == 1)//yhet2helistest otsingust vaid esimese t2he j2rgi
					{
						$ft = new object_list_filter(array(
							"logic" => "OR",
							"conditions" => array(
								"CL_CRM_BILL.customer(CL_CRM_COMPANY).name" => $filter["customer"]."%",
								"CL_CRM_BILL.customer(CL_CRM_PERSON).name" => $filter["customer"]."%",
								"CL_CRM_BILL.customer_name" => $filter["customer"]."%",
							)
						));
					}
					else
					{
						$ft = new object_list_filter(array(
							"logic" => "OR",
							"conditions" => array(
								"CL_CRM_BILL.customer(CL_CRM_COMPANY).name" => "%".$filter["customer"]."%",
								"CL_CRM_BILL.customer(CL_CRM_PERSON).name" => "%".$filter["customer"]."%",
								"CL_CRM_BILL.customer_name" => "%".$filter["customer"]."%",
							)
						));
					}
				}
				$of[] = $ft;
				$of2[] = $ft;

			}
		}
		$ret =  new object_list($of);
		if (isset($of2))
		{
			$ret->add(new object_list($of2));
		}
		return $ret;
	}

	///////////////// customers

	/** returns customers for company 
	**/
	function get_customers_for_company($co)
	{
		$ret = array();
		$this->_int_req_get_cust_co($co, $ret);

		return $ret;
	}

	function _int_req_get_cust_co($co, &$ret)
	{
		foreach($co->connections_from(array("type" => "RELTYPE_CUSTOMER")) as $c)
		{
			$ret[$c->prop("to")] = $c->prop("to");
		}
		
		foreach($co->connections_from(array("type" => "RELTYPE_CATEGORY")) as $c)
		{
			$this->_int_req_get_cust_co($c->to(), $ret);
		}
	}

	//////////////////////////////////////////// company

	/** returns sections from the given company 
	**/
	function get_section_picker_from_company($co)
	{
		$ret = array();
		$this->req_level = -1;
		$this->_req_get_sect_picker($co, $ret);
		return $ret;
	}

	function _req_get_sect_picker($o, &$ret)
	{
		$this->req_level++;
		foreach($o->connections_from(array("type" => "RELTYPE_SECTION")) as $c)
		{
			$ret[$c->prop("to")] = str_repeat("&nbsp;&nbsp;&nbsp;", $this->req_level).$c->prop("to.name");
			$this->_req_get_sect_picker($c->to(), $ret);
		}
		$this->req_level--;
	}

	////////////////////////////////////////// current person

	function get_current_section()
	{
		$u = new user();
		$p = obj($u->get_current_person());

		if (!is_oid($p->id()))
		{
			return null;
		}
		$cs = $p->connections_to(array("from.class_id" => CL_CRM_SECTION));
		$c = reset($cs);
		if (!$c)
		{
			return NULL;
		}

		return $c->prop("from");
	}

	function get_current_profession()
	{
		$u = new user();
		$p = obj($u->get_current_person());
	
		if (!is_oid($p->id()))
		{
			return null;
		}
		$cs = $p->connections_from(array("to.class_id" => CL_CRM_PROFESSION));
		$c = reset($cs);
		if (!$c)
		{
			return NULL;
		}

		return $c->prop("to");
	}


	//////////// people
	function get_employee_picker($co = NULL, $add_empty = false, $important_only = true)
	{
		if ($co === NULL)
		{
			$u = new user();
			$cco_id = $u->get_current_company();
			if (!$this->can("view", $cco_id))
			{
				return array();
			}
			$co = obj($cco_id);
		}
		$i = get_instance(CL_CRM_COMPANY);
		return $i->get_employee_picker($co, $add_empty, $important_only);
	}
}
?>
