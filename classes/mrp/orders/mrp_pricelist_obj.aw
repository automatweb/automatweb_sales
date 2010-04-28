<?php

namespace automatweb;


class mrp_pricelist_obj extends _int_object
{
	const AW_CLID = 1521;

	/** Returns array of resources for this price list
		@attrib api=1
	**/
	function get_resource_list($parent = null)
	{	
		if ($parent !== null)
		{
			$ol = new object_list(array(
				"class_id" => CL_MRP_RESOURCE,
				"parent" => $parent,
				"lang_id" => array(),
				"site_id" => array()
			));
			return $ol->arr();
		}

		$conns = $this->connections_to(array("from.class_id" => CL_MRP_ORDER_CENTER));
		$c = reset($conns);
		if (!$c)
		{
			return array();
		}

		$ot = new object_tree(array(
			"parent" => $c->from()->mrp_workspace()->resources_folder,
			"class_id" => array(CL_MRP_RESOURCE,CL_MENU),
			"lang_id" => array(),
			"site_id" => array()
		));
		$rv = array();

		foreach($ot->to_list()->arr() as $item)
		{
			if ($item->class_id() == CL_MRP_RESOURCE)
			{
				$rv[] = $item;
			}
		}
		return $rv;
	}

	function get_ranges_for_resource($res)
	{
		$ol = new object_list(array(
			"class_id" => CL_MRP_PRICELIST_ROW,
			"lang_id" => array(),
			"site_id" => array(),
			"pricelist" => $this->id(),
			"resource" => $res->id(),
			"row_type" => mrp_pricelist_row_obj::ROW_TYPE_AMOUNT
		));
		return $ol->arr();
	}

	function set_ranges_for_resource($res, $d)
	{
		foreach(safe_array($d) as $idx => $row)
		{
			if ($idx == -1)
			{
				$r = obj();
				$r->set_parent($this->id());
				$r->set_class_id(CL_MRP_PRICELIST_ROW);
				$r->set_name(sprintf(t("Hinnakirja %s rida ressursile %s"), $this->name(), $res->name()));
				$r->pricelist = $this->id();
				$r->row_type = mrp_pricelist_row_obj::ROW_TYPE_AMOUNT;
				$r->set_prop("resource", $res->id());
			}
			else
			if ($this->can("view", $idx))
			{
				$r = obj($idx);
			}
			else
			{
				continue;
			}

			if ($row["cnt_from"] < 1 && $row["cnt_to"] < 1)
			{
				if (is_oid($r->id()))
				{
					$r->delete();
				}
				continue;
			}

			$r->item_price = $row["item_price"];
			$r->config_price = $row["config_price"];
			$r->cnt_from = $row["cnt_from"];
			$r->cnt_to = $row["cnt_to"];
			$r->save();
		}
	}

	function get_ranges_for_resource_hr($res)
	{
		$ol = new object_list(array(
			"class_id" => CL_MRP_PRICELIST_ROW,
			"lang_id" => array(),
			"site_id" => array(),
			"pricelist" => $this->id(),
			"resource" => $res->id(),
			"row_type" => mrp_pricelist_row_obj::ROW_TYPE_HOUR
		));
		return $ol->arr();
	}

	function set_ranges_for_resource_hr($res, $d)
	{
		foreach(safe_array($d) as $idx => $row)
		{
			if ($idx == -1)
			{
				$r = obj();
				$r->set_parent($this->id());
				$r->set_class_id(CL_MRP_PRICELIST_ROW);
				$r->set_name(sprintf(t("Hinnakirja %s rida ressursile %s"), $this->name(), $res->name()));
				$r->pricelist = $this->id();
				$r->row_type = mrp_pricelist_row_obj::ROW_TYPE_HOUR;
				$r->set_prop("resource", $res->id());
			}
			else
			if ($this->can("view", $idx))
			{
				$r = obj($idx);
			}
			else
			{
				continue;
			}

			if ($row["cnt_from"] < 1 && $row["cnt_to"] < 1)
			{
				if (is_oid($r->id()))
				{
					$r->delete();
				}
				continue;
			}

			$r->item_price = $row["item_price"];
			$r->config_price = $row["config_price"];
			$r->cnt_from = $row["cnt_from"];
			$r->cnt_to = $row["cnt_to"];
			$r->save();
		}
	}

	function get_price_for_resource_and_amount($resource, $amount)
	{
		$ol = new object_list(array(
			"class_id" => CL_MRP_PRICELIST_ROW,
			"site_id" => array(),
			"lang_id" => array(),
			"pricelist" => $this->id(),
			"row_type" => mrp_pricelist_row_obj::ROW_TYPE_AMOUNT,
			"resource" => $resource->id(),
			"cnt_from" => new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $amount),
			"cnt_to" => new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $amount)
		));

		if (!$ol->count())
		{
			return 0;
		}
		$row = $ol->begin();
		return $row->config_price + ($row->item_price * $amount);
	}

	function get_price_for_resource_and_time($resource, $time)
	{
		$ol = new object_list(array(
			"class_id" => CL_MRP_PRICELIST_ROW,
			"site_id" => array(),
			"lang_id" => array(),
			"pricelist" => $this->id(),
			"row_type" => mrp_pricelist_row_obj::ROW_TYPE_HOUR,
			"resource" => $resource->id(),
			"cnt_from" => new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $time),
			"cnt_to" => new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $time)
		));

		if (!$ol->count())
		{
			return 0;
		}
		$row = $ol->begin();
		return $row->config_price + ($row->item_price * $time);
	}
}

?>
