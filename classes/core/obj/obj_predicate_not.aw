<?php

class obj_predicate_not
{
	/**
		@attrib api=1 params=pos
		@param data type=int
			Data to be compared

		@comment
			Used in object list filtering property values.

		@examples
			$filt = array(
				"class_id" => CL_BUG,
				"bug_status" => new obj_predicate_not(4),
			);
			$ol = new object_list($filt);

			// generates list of bugs with statuses from anything but 4

	**/
	function obj_predicate_not($data)
	{
		$this->data = $data;
	}

	function __toString()
	{
		return "obj_predicate_not(".$this->data.")";
	}
}
