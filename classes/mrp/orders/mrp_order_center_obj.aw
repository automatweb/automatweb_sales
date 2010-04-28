<?php

namespace automatweb;


class mrp_order_center_obj extends _int_object
{
	const AW_CLID = 1518;

	/** lists all customer side contact persons for all orders
		@attrib api=1
	**/
	public function get_all_customer_contacts()
	{
		// all orders -> customer -> client_manager unique list
		$odl = new object_data_list(array(
			"class_id" => CL_MRP_ORDER_PRINT,
			"lang_id" => array(),
			"site_id" => array(),
			"workspace" => $this->id()
		),
		array(
			CL_MRP_ORDER_PRINT => array(new obj_sql_func(OBJ_SQL_UNIQUE, "orderer_person", "orderer_person"))
		));
		$tmp = $odl->arr();
		$ol = new object_list(array(
			"oid" => $tmp[0],
			"lang_id" => array(),
			"site_id" => array()
		));
		return $ol->names();
	}

	/** lists all seller side contact persons for all orders
		@attrib api=1
	**/
	public function get_all_seller_contacts()
	{
		$odl = new object_data_list(array(
			"class_id" => CL_MRP_ORDER_PRINT,
			"lang_id" => array(),
			"site_id" => array(),
			"workspace" => $this->id()
		),
		array(
			CL_MRP_ORDER_PRINT => array(new obj_sql_func(OBJ_SQL_UNIQUE, "seler_person", "seller_person"))
		));
		$tmp = $odl->arr();
		$ol = new object_list(array(
			"oid" => $tmp[0],
			"lang_id" => array(),
			"site_id" => array()
		));
		return $ol->names();
	}

	/**
		@attrib api=1
	**/
	public function get_default_pricelist()
	{
		foreach($this->connections_from(array("type" => "RELTYPE_MRP_PRICELIST")) as $c)
		{
			$pl = $c->to();
			if ($pl->act_from < time() && $pl->act_to > time())
			{
				return $pl;
			}
		}
		return null;
	}

	public function get_all_covers()
	{
		$ol = new object_list($this->connections_from(array("type" => "RELTYPE_MRP_COVER", "to.class_id" => CL_MRP_ORDER_COVER, "to.status" => object::STAT_ACTIVE)));
		return $ol->arr();
	}
}

?>
