<?php

namespace automatweb;


class mrp_order_obj extends _int_object
{
	const AW_CLID = 1519;

	/**
		@attrib api=1
	**/
	public function get_customer_relation()
	{
		if (!is_oid($this->id()))
		{
			return null;
		}
		if (!$GLOBALS["object_loader"]->cache->can("view", $this->prop("customer")))
		{
			return null;
		}
		$crel = get_instance(CL_CRM_COMPANY)->get_cust_rel(obj($this->id())->customer(), false, obj($this->prop("workspace"))->owner_co());

		if (!$crel)
		{
			return null;
		}
		return $crel;
	}

	/**
		@attrib api=1
	**/
	public function get_case()
	{
		if (is_oid($this->id()) && !$GLOBALS["object_loader"]->cache->can("view", $this->prop("mrp_case")))
		{
			$mc = obj();
			$mc->set_class_id(CL_MRP_CASE);

			if (!$GLOBALS["object_loader"]->cache->can("view", $this->prop("workspace")))
			{
				return null;
			}
			$mc->set_parent(obj($this->prop("workspace"))->mrp_workspace()->projects_folder);
			$mc->set_name($this->prop("name"));
			$mc->set_prop("trykiarv", $this->prop("amount"));
			$mc->set_prop("workspace", obj(obj($this->prop("workspace"))->mrp_workspace));
			$mc->set_prop("order_quantity", 1);
			$mc->save();
			$this->set_prop("mrp_case", $mc->id());
			$this->save();
		}

		if ($GLOBALS["object_loader"]->cache->can("view", $this->prop("mrp_case")))
		{
			return obj($this->prop("mrp_case"));
		}
		return null;
	}

	function set_prop($k, $v)
	{
		if ($k == "customer" && ($case = $this->get_case()))
		{
			$case->set_prop("customer", $v);
			$case->save();
		}

		if ($k == "state" && $v == 4)
		{
			$case = $this->get_case();
			$case->set_prop("state", MRP_STATUS_NEW);
			$case->save();
		}
		return parent::set_prop($k, $v);
	}
}

?>
