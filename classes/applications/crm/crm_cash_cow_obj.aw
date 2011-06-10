<?php

class crm_cash_cow_obj extends _int_object
{
	const CLID = 1538;

	/** Returns income expense spots
		@attrib api=1
		@returns object list
	**/
	public function get_expense_spots()
	{
		$ol = new object_list();
		foreach($this->connections_from(array("type" => "RELTYPE_EXPENSE")) as $c)
		{
			$ol->add($c->prop("to"));
		}
		return $ol;
	}

	/** Add new expense spot
		@attrib api=1
	**/
	public function add_expense_spot()
	{
		$o = new object();
		$o->set_class_id(CL_CRM_EXPENSE_SPOT);
		$o->set_parent($this->id());
		$o->set_name($this->name()." ".t("Kulukoht"));
		$o->save();
		$this->connect(array(
			"to" => $o->id(),
			"type" => "RELTYPE_EXPENSE"
		));
		return $o->id();
	}
}

?>
