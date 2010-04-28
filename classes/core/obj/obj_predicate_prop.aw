<?php

namespace automatweb;

class obj_predicate_prop
{
	var $prop; // the property that this object references
	/**
		@attrib api=1 params=pos
		@param p1 required
		@param p2 optional
		@comment
		Compares two property values.
		If only $p1 is set, then this property is compared for equality. If both $p1 and $p2 are set, then $p1 is the comparison method ( uses the same types as obj_predicate_compare), and $p2 is the property to be compared with.
		@examples
		$filt = array(
			"class_id" => CL_BUG,
			"bug_status" => new obj_predicate_not(4),
			"brother_of" => new obj_predicate_prop("id")
		);
		$ol = new object_list($filt);
		// filters out bug's which bug_status property isn't 4 and which brother_of property equals with id property
		$filt = array(
			"class_id" => CL_BUG,
			"bug_status" => new obj_predicate_not(2),
			"bug_priority" => new obj_predicate_prop(obj_predicate_compare::LESS, "bug_status")
		);
		$ol = new object_list($filt);
		// filters out bug's which bug_status property isn't 2 and which bug_priority property value is less than bug_status property value.


	**/
	function obj_predicate_prop($p1, $p2 = NULL)
	{
		if ($p2 !== NULL)
		{
			$this->prop = $p2;
			$this->compare = $p1;
		}
		else
		{
			$this->prop = $p1;
			$this->compare = obj_predicate_compare::EQUAL;
		}
	}

	function __toString()
	{
		return "obj_predicate_prop(".$this->prop.",".$this->compare.")";
	}
}
?>
