<?php

class shop_purchase_manager_workspace_obj extends _int_object
{
	public function get_warehouse_ids()
	{
		$warehouse_ids = array();
		foreach ($this->connections_from(array("type" => "RELTYPE_WAREHOUSE")) as $c)
		{
			$warehouse_ids[] = $c->prop("to");
		}
		return $warehouse_ids;
	}

	/**
		@attrib name=order_products api=1
		
		@param products required type=array
		@param date required type=int
		@param job optional type=oid
	
		@comment
			arr[products] is an array of arrays ( product => oid, unit => oid, amount => int )
			when unit is not defined there, product's default unit is used

		@returns order object that was created
	**/
	public function order_products($arr)
	{
		$o = $this->create_order(array(
			"name" => sprintf("Tellimus %s", date("d.m.Y", $arr["date"])),
			"date" => $arr["date"],
			"job" => $arr["job"],
		));
		foreach($arr["products"] as $product)
		{
			$row = $this->create_order_row($product, $o);
		}
	}

	/**
		@attrib name=order_product api=1

		@param product required type=oid
		@param unit optional type=oid
		@param amount required type=int
		@param date required type=int
		@param job optional type=oid

		@returns order object that was created
	**/
	public function order_product($arr)
	{
		$o = $this->create_order(array(
			"name" => sprintf("%s tellimus %s", obj($arr["product"]->name()), date("d.m.Y", $arr["date"])),
			"date" => $arr["date"],
			"job" => $arr["job"],
		));
		$row = $this->create_order_row($arr, $o);
		return $o;
	}

	private function create_order($arr)
	{
		$o = obj();
		$o->set_class_id(CL_SHOP_SELL_ORDER);
		$o->set_parent($this->id());
		$o->set_name($arr["name"]);
		$o->set_prop("date", $arr["date"]);
		$o->set_prop("order_status", ORDER_STATUS_CONFIRMED);
		if($arr["job"])
		{
			$o->set_prop("job", $arr["job"]);
		}
		$o->save();
		return $o;
	}

	private function create_order_row($arr, $o)
	{		
		$row = obj();
		$row->set_class_id(CL_SHOP_ORDER_ROW);
		$row->set_parent($o->id());
		$row->set_name(sprintf(t("%s rida"), $o->name()));
		$row->set_prop("prod", $arr["product"]);
		$row->set_prop("amount", $arr["amount"]);
		$unit = $arr["unit"];
		if(!$unit)
		{
			$po = obj($arr["product"]);
			$units = $po->instance()->get_units($po);
			$unit = $units[0];
		}
		$row->set_prop("unit", $unit);
		$row->save();
		$o->connect(array(
			"to" => $row,
			"type" => "RELTYPE_ROW",
		));
	}

	/**
	@attrib name=update_order_rows

	@param order required type=object
	@param rows required type=array

	@comment
		Updates order rows' amounts
	**/
	function update_order_rows($order, $rows)
	{
		$conn = $order->connections_from(array(
			"to.class_id" => CL_SHOP_ORDER_ROW,
		));
		foreach($conn as $c)
		{
			$o = $c->to();
			$o->set_prop("amount", $rows[$o->prop("prod")]["amount"]);
			$o->save();
			$upd_prods[$o->prop("prod")] = 1;
		}
		foreach($rows as $row)
		{
			if(!$upd_prods[$row["product"]])
			{
				$this->create_order_row($row, $order);
			}
		}
	}

	/**
	@attrib name=get_order_rows
	
	@param product optional type=int
	@param date optional type=int
	@param job optional type=int
	@param order_type required type=string	
	@param order_status optional type=int

	@comment
		Returns object list of orders rows by specified date and/or product
	**/
	function get_order_rows($arr)
	{
		get_instance(CL_SHOP_PURCHASE_ORDER);
		$params = array(
			"class_id" => CL_SHOP_ORDER_ROW,
			"RELTYPE_ROW(".$arr["order_type"].").class_id" => constant($arr["order_type"]),
			"site_id" => array(),
			"lang_id" => array(),
		);
		if($arr["order_status"])
		{
			$params["RELTYPE_ROW(".$arr["order_type"].").order_status"] = $arr["order_status"];
		}
		if(is_array($arr["date"]))
		{
			$params["RELTYPE_ROW(".$arr["order_type"].").date"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $arr["date"][0], $arr["date"][1]);
		}
		elseif($arr["date"])
		{
			$params["RELTYPE_ROW(".$arr["order_type"].").date"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, time(), $arr["date"]);
		}
		else
		{
			$params["RELTYPE_ROW(".$arr["order_type"].").date"] = new obj_predicate_compare(OBJ_COMP_GREATER, time());
		}
		if($arr["job"])
		{
			$params["RELTYPE_ROW(".$arr["order_type"].").job"] = $arr["job"];
		}
		if($arr["product"])
		{
			$params["prod"] = $arr["product"];
		}
		return new object_list($params);
	}

	/**
	@attrib name=update_orders api=1

	@comment
		updates/creates orders according to mrp_jobs
	**/
	function update_orders()
	{
		//waiting for a storage fix for the next ol
	if(false)
	{
		$job = obj();
		$job->set_class_id(CL_MRP_JOB);

		//find all jobs that don't have an order
		$ol = new object_list(array(
			"class_id" => CL_MRP_JOB,
			"state" => mrp_job_obj::STATE_PLANNED,
			"RELTYPE_JOB(CL_SHOP_SELL_ORDER).oid" => new obj_predicate_compare(OBJ_COMP_NULL),
			//"RELTYPE_MRP_RESOURCE.workspace" => $this->prop("mrp_workspace"),
			"RELTYPE_JOB(CL_MATERIAL_EXPENSE).class_id" => CL_MATERIAL_EXPENSE,
		));
	arr($ol);die();
		$ol2 = new object_list(array(
			"class_id" => CL_MATERIAL_EXPENSE,
			"job" => $ol->ids(),
		));
		$ol2->arr();
		foreach($ol->arr() as $oid => $o)
		{
			$ol2 = new object_list(array(
				"class_id" => CL_MATERIAL_EXPENSE,
				"job" => $oid,
			));
			foreach($ol2->arr() as $rid => $row)
			{
				if($row->prop("base_amount"))
				{
					$prods[$rid] = array(
						"product" => $row->prop("product"),
						"amount" => $row->prop("base_amount"),
					);
				}
			}
			$this->order_products(array(
				"date" => $o->prop("starttime"),
				"job" => $oid,
				"products" => $prods,
			));
		}
	}
		//find all planned jobs' dates
		$odl = new object_data_list(
			array(
				"class_id" => CL_MRP_JOB,
				"RELTYPE_JOB(CL_SHOP_SELL_ORDER).class_id" => CL_SHOP_SELL_ORDER,
				"state" => mrp_job_obj::STATE_PLANNED,
			),
			array(
				CL_MRP_JOB => array("oid", "starttime"),
			)
		);
		$jobs = array();
		foreach($odl->arr() as $job)
		{
			$jobs[] = $job["oid"];
		}

		//get all orders for the loaded jobs
		$odl2 = new object_data_list(
			array(
				"class_id" => CL_SHOP_SELL_ORDER,
				"job" => $jobs,
			),
			array(
				CL_SHOP_SELL_ORDER => array("oid", "job", "date"),
			)
		);
		$orders = array();
		foreach($odl2->arr() as $order)
		{
			$orders[$order["job"]] = array(
				"oid" => $order["oid"],
				"date" => $order["date"],
			);
		}

		//loop over jobs to change their orders' dates if needed
		foreach($odl->arr() as $job)
		{
			if($orders[$job["oid"]] && $orders[$job["oid"]]["date"] != $job["starttime"])
			{
				$o = obj($orders[$job["oid"]]["oid"]);
				$o->set_prop("date", $job["starttime"]);
				$o->save();
			}
		}
	}

	/**
	@attrib name=update_job_orders api=1
	
	@param job required type=object
	
	@comment 
		updates the order for a job
	**/
	function update_job_order($job)
	{
		$ol = new object_list(array(
			"class_id" => CL_MATERIAL_EXPENSE,
			"job" => $job->id(),
		));
		foreach($ol->arr() as $row)
		{
			if($row->prop("base_amount"))
			{
				$prods[$row->prop("product")] = array(
					"product" => $row->prop("product"),
					"amount" => $row->prop("base_amount"),
				);
			}
		}
		if(count($prods))
		{
			$ol = new object_list(array(
				"class_id" => CL_SHOP_SELL_ORDER,
				"job" => $job->id(),
			));
			if($order = $ol->begin())
			{
				$this->update_order_rows($order, $prods);
			}
			else
			{
				$this->order_products(array(
					"date" => $job->prop("starttime"),
					"job" => $job->id(),
					"products" => $prods,
				));
			}
		}
	}
}

?>
