<?php

namespace automatweb;


class crm_expense_spot_obj extends _int_object
{
	const AW_CLID = 1539;

	/** Returns expense spot rows
		@attrib api=1
		@returns object list
	**/
	public function get_rows()
	{
		$ol = new object_list();
		foreach($this->connections_from(array("type" => "RELTYPE_ROW")) as $c)
		{
			$ol->add($c->prop("to"));
		}
		return $ol;
	}

	/** Add new expense spot row
		@attrib api=1
	**/
	public function add_row()
	{
		$o = new object();
		$o->set_class_id(CL_CRM_EXPENSE_SPOT_ROW);
		$o->set_parent($this->id());
		$o->set_name($this->name()." ".t("rida"));
		$o->save();
		$this->connect(array(
			"to" => $o->id(),
			"type" => "RELTYPE_ROW"
		));
		return $o->id();
	}

	/** gets expense amount
		@attrib api=1
	**/
	public function get_amount()
	{
		if($this->prop("amount"))
		{
			return $this->prop("amount");
		}
		else
		{
			$amt = 0;
			$rows = $this->get_rows();
			foreach($rows->arr() as $row)
			{
				$amt+= $row->prop("amount");
			}
			return $amt;
		}

	}

	/** gets expense sum
		@attrib api=1
	**/
	public function get_sum()
	{
		$sum = 0;
		$rows = $this->get_rows();
		foreach($rows->arr() as $row)
		{
			$sum+= $row->prop("amount")*$row->prop("unit_price");
		}
		return $sum;
	}

	/** gets expense assigned amount
		@attrib api=1
	**/
	public function get_assigned_amount()
	{
		$amt = 0;
		$rows = $this->get_rows();
		foreach($rows->arr() as $row)
		{
			$amt+= $row->prop("amount");
		}
		return $amt;
	}
	

}

?>
