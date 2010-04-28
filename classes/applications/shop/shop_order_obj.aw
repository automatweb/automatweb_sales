<?php

namespace automatweb;


class shop_order_obj extends _int_object
{
	const AW_CLID = 302;

	function set_prop($name,$value)
	{
		parent::set_prop($name,$value);
	}

	function get_price()
	{
		$d = new aw_array($this->meta("ord_item_data"));
		$sum = 0;
		foreach($d->get() as $id => $prod)
		{
			if(!is_oid($id) || !$this->can("view", $id))
			{
				continue;
			}
			$it = obj($id);
			$inst = $it->instance();
			$price = $inst->get_price($it);
			$prod = new aw_array($prod);
			foreach($prod->get() as $x => $val)
			{
				$sum += $price * $val["items"];
			}
		}
		return number_format($sum, 2);
	}

	function payment_marked()
	{
		return $o->prop("confirmed");
	}
}

?>
