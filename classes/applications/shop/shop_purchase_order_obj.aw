<?php

namespace automatweb;


class shop_purchase_order_obj extends _int_object
{
	const AW_CLID = 1430;

	/** adds an article to the order, params are order_row props **/
	public function add_article($data)
	{
		$or = obj();
		$or->set_class_id(CL_SHOP_ORDER_ROW);
		$or->set_parent($this->id());
		$or->set_name($data["name"]);
		$or->prod_name = $data["name"];
		$or->required = $data["required"];
		$or->amount = $data["amount"];
		$or->real_amount = $data["real_amount"];
		$or->unit = $data["unit"];
		$or->price = $data["price"];
		$or->other_code = $data["other_code"];
		$or->comment = $data["comment"];
		$or->save();
		$this->connect(array(
			"to" => $or->id(),
			"type" => "RELTYPE_ROW"
		));
		return $or;
	}
}

?>
