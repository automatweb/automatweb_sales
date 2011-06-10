<?php

class mrp_order_requested_material_obj extends _int_object
{
	const CLID = 1536;

	function delete($full_delete = false)
	{
		if (is_oid($this->prop("connected_job")))
		{
			obj($this->prop("connected_job"))->delete();
		}
		return parent::delete($full_delete);
	}

	private function _get_order()
	{
		$conns = $this->connections_to(array("from.class_id" => CL_MRP_ORDER_PRINT));
		if (!count($conns))
		{
			return null;
		}
		$con = reset($conns);
		return $con->from();
	}

	public function get_material_price()
	{
		$cur_list = get_instance(CL_CURRENCY)->get_list(RET_NAME);
		$material = obj($this->prop("material"));

		foreach($cur_list as $cur_id => $cur_name)
		{
			$pr_tmp = ($material->price_get_by_currency(obj($cur_id)));
			$price_fin[] = number_format($pr_tmp * $this->prop("amount"), 2)." ".$cur_name;
		}
		return join(" ", $price_fin);
	}
}

?>
