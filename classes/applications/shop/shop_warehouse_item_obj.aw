<?php

abstract class shop_warehouse_item_obj extends aw_product_obj
{
	const CLID = 1797;

	/**	Returns an object list of purveyances for this object
		@attrib api=1 params=pos
		@param warehouse optional type=int/array
			The OID(s) of warehouse(s) purveyance is queried for
	**/
	public function get_purveyances($warehouse = null)
	{
		return new object_list(array(
			"class_id" => CL_SHOP_PRODUCT_PURVEYANCE,
			"object" => $this->id(),
			"warehouse" => $warehouse,
		));
	}

	/**	Returns the warehouse object that the product is accessed through
		@attrib api=1
	**/
	public function get_current_warehouse()
	{
		//	TODO: Do some heuristics with return_url or smth to actually return "the warehouse object that the product is accessed through".
		return $this->get_first_obj_by_reltype("RELTYPE_WAREHOUSE");
	}

}

/** Generic shop_warehouse_item_obj exception **/
class awex_shop_warehouse_item_obj extends awex_obj {}
