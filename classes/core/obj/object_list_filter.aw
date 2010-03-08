<?php
/*
@classinfo  maintainer=kristo
*/

class object_list_filter
{
	var $filter;

	/** constructs and initializes the filter.
		@attrib api=1

		@param param required type=array
			array(
				logic => OR | AND // conditions are in disjunction or conjunction
				conditions => array(
					// any valid object_list or object_tree parameter terms
				)
			)

		If class-specific object list filter parameters used, then in the object_list/object_tree filter a class_id parameter must be specified and must contain the classes used here.

		@examples
			// creates a tree that contains all folders below object 666 that are folders or active documents or documents under the folder $parent, the tree will contain all folders below it and all objects in those folders
			$ot = new object_tree(array(
				"parent" => 666,
				"class_id" => array(CL_FILE, CL_DOCUMENT),
				new object_list_filter(
					"logic" => "OR",
					"conditions" => array(
						"CL_DOCUMENT.status" => STAT_ACTIVE,
						"CL_DOCUMENT.parent" => $parent,
					)
				)
			));
	**/
	function object_list_filter($param)
	{
		$this->filter = $param;
	}

	function __toString()
	{
		return "object_list_filter(".serialize($this->filter).")";
	}
}

?>
