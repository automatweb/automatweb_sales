<?php

// DEPRECATED! use class constants instead
define("OBJ_COMP_LESS", 1);
define("OBJ_COMP_GREATER", 2);
define("OBJ_COMP_LESS_OR_EQ", 4);
define("OBJ_COMP_GREATER_OR_EQ", 8);
define("OBJ_COMP_BETWEEN", 16);
define("OBJ_COMP_EQUAL", 32);
define("OBJ_COMP_BETWEEN_INCLUDING", 64);
define("OBJ_COMP_NULL", 128);
define("OBJ_COMP_IN_TIMESPAN", 256);
// END DEPRECATED.

class obj_predicate_compare
{
	const LESS = 1;
	const GREATER = 2;
	const LESS_OR_EQ = 4;
	const GREATER_OR_EQ = 8;
	const BETWEEN = 16;
	const EQUAL = 32;
	const BETWEEN_INCLUDING = 64;
	const NULL = 128;
	const IN_TIMESPAN = 256;

	public $comparator;
	public $data;
	public $data2;
	public $type;

	/**
		@attrib api=1 params=pos

		@param comparator required type=int
		Comparator type.
		Available types:
		CONSTANT (integer_assigned to that constant) - action
		obj_predicate_compare::LESS (1) - values less than $data
		obj_predicate_compare::GREATER (2) - values greater than $data
		obj_predicate_compare::LESS_OR_EQ (4) - values less or equal to $data
		obj_predicate_compare::GREATER_OR_EQ (8) - values greater or equal to $data
		obj_predicate_compare::BETWEEN (16) - values between $data and $data2
		obj_predicate_compare::EQUAL (32) - values equal to $data
		obj_predicate_compare::BETWEEN_INCLUDING (64) - values between and $data and $data2, including $data & $data2 themselves
		obj_predicate_compare::NULL (128) - value NULL
		obj_predicate_compare::IN_TIMESPAN (256) - takes two arrays as parameters, first has two entries containing the properties defining the timespan, second is an array containing two elements, defining the searchable timespan

		@param data optional type=string
		data to compare

		@param data2 optional type=string
		data to compare

		@param type optional type=string
		data type

		@comment
		Used in object list filtering property values.
		@examples
		$filt = array(
			"class_id" => CL_BUG,
			"bug_status" => new obj_predicate_compare(obj_predicate_compare::BETWEEN_INCLUDING, 1, 6),
		);
		$ol = new object_list($filt);

		// generates list of bugs with statuses from 1 to 6 (inclucing 1 and 6)
	**/
	public function obj_predicate_compare($comparator, $data = null, $data2 = null, $type = null)
	{
		$self_reflection = new ReflectionClass('obj_predicate_compare');
		$constants = $self_reflection->getConstants();
		settype($comparator, "int");
		if (!in_array($comparator, $constants))
		{
			throw new awex_obj_type("Invalid comparison operator ". var_export($comparator, true));
		}

		$this->comparator = $comparator;
		$this->data = $data;
		$this->data2 = $data2;
		$this->type = $type;
	}

	public function __toString()
	{
		$str = "objcompare-".$this->comparator."-".$this->data."-".$this->data2."-".$this->type;
		return $str;
	}

	public function get_comparison_value1()
	{
		return $this->data;
	}

	public function get_comparison_value2()
	{
		return $this->data2;
	}

	public function get_operator()
	{
		return $this->comparator;
	}
}
