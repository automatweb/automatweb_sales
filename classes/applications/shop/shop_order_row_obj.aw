<?php

class shop_order_row_obj extends _int_object
{
	const CLID = 1415;

	public function awobj_get_purveyance_company_section()
	{
		return $this->__awobj_get("purveyance_company_section");
	}

	public function awobj_get_buyer_rep()
	{
		return $this->__awobj_get("buyer_rep");
	}

	public function awobj_get_planned_date()
	{
		return $this->__awobj_get("planned_date");
	}

	public function awobj_get_planned_time()
	{
		return $this->__awobj_get("planned_time");
	}

	private function __awobj_get($prop)
	{
		$v = $this->prop($prop);
		if (!is_oid($v) and is_oid($this->prop("order")))
		{
			$order = obj($this->prop("order"), array(), shop_sell_order_obj::CLID);
			return $order->prop($prop);
		}

		return $v;
	}
}
