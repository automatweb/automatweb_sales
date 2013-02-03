<?php

class join_site_customer_relation_obj extends _int_object
{
	const CLID = 1820;
	
	const TYPE_BUYER = 0;
	const TYPE_SELLER = 1;
	
	public function get_customer_groups()
	{
		$customer_groups_ids = $this->prop("customer_groups");
		$customer_groups = !empty($customer_groups_ids) ? new object_list(array(
			"oid" => $customer_groups_ids,
		)) : new object_list();
		
		return $customer_groups;
	}
}
