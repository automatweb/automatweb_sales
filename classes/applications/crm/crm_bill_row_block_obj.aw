<?php

/** Invoice row block is a presentational helper object used to group rows and add properties to those groups (name, description, etc.) **/
class crm_bill_row_block_obj extends _int_object
{
	const CLID = 1781;

	/** Returns rows in this block
		@attrib api=1 params=pos
		@comment
		@returns object_list
		@errors
	**/
	public function get_rows()
	{
		$rows = new object_list($this->connections_from(array("type" => "RELTYPE_CHILD", "to.class_id" => crm_bill_row_obj::CLID)));
		return $rows;
	}

	/** Adds an invoice row to this block
		@attrib api=1 params=pos
		@param row type=CL_CRM_BILL_ROW
		@returns void
		@errors
			throws awex_obj_type if $row is invalid
	**/
	public function add_row(object $row)
	{
		if (!$row->is_a(crm_bill_row_obj::CLID))
		{
			throw new awex_obj_type("Invalid row object " . var_export($row, true) . " with class: " . $row->class_id());
		}

		$this->connect(array(
			"to" => $row,
			"type" => "RELTYPE_CHILD"
		));
	}

	/** Removes an invoice row from this block
		@attrib api=1 params=pos
		@param row type=CL_CRM_BILL_ROW
		@returns void
		@errors
			throws awex_obj_type if $row is invalid
	**/
	public function remove_row(object $row)
	{
		if (!$row->is_a(crm_bill_row_obj::CLID))
		{
			throw new awex_obj_type("Invalid row object " . var_export($row, true) . " with class: " . $row->class_id());
		}

		$remove = $this->is_connected_to(array(
			"to" => $row,
			"type" => "RELTYPE_CHILD"
		));

		if ($remove)
		{
			$this->disconnect(array(
				"from" => $row
			));
		}
	}
}
