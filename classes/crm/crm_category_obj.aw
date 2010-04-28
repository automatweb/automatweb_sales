<?php

namespace automatweb;

//maintainer=markop  
class crm_category_obj extends _int_object
{
	const AW_CLID = 483;

	public function get_category_orgs()
	{
		$ol = new object_list();
		$conns = $this->connections_from(array("type" => "RELTYPE_CUSTOMER"));
		foreach($conns as $conn)
		{
			$ol->add($conn->prop("to"));
		}
		return ($ol);
	}

	public function get_category_customers()
	{
		$ol = new object_list();
		$conns = $this->connections_from(array("type" => "RELTYPE_CUSTOMER"));
		foreach($conns as $conn)
		{
			$ol->add($conn->prop("to"));
		}
		return ($ol);
	}

}
?>
