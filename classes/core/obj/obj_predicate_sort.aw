<?php

/**
Sets sort mode and direction

Example:

$sort_order = new obj_predicate_sort(array("jrk" => obj_predicate_sort::ASC));

**/

class obj_predicate_sort
{
	const ASC = 1;
	const DESC = 2;

	private $arr = array();
	private $data = array();

	private static $direction_values = array(
		self::ASC => "ASC",
		self::DESC => "DESC",
		"asc" => "ASC",
		"ASC" => "ASC",
		"desc" => "DESC",
		"DESC" => "DESC"
	);

	private static $supported_predicates = array(
		"obj_predicate_compare"
	);

	public function obj_predicate_sort($data)
	{
		if (!is_array($data))
		{
			throw new awex_obj_type("Invalid argument type");
		}

		foreach ($data as $prop => $direction)
		{
			$predicate = false;

			if (is_array($direction))
			{
				$predicate = $direction[0];
				$direction = $direction[1];
			}

			if (!isset(self::$direction_values[$direction]))
			{
				throw new awex_obj("Argument contains invalid sorting direction instruction(s)");
			}

			if ($predicate and (!is_object($predicate) or !in_array(get_class($predicate), self::$supported_predicates)))
			{

			}

			$this->data[] = array(
				"prop" => $prop,
				"direction" => self::$direction_values[$direction],
				"predicate" => $predicate
			);
		}
	}

	public function get_sorter_list()
	{
		return $this->data;
	}

	public function __toString()
	{
		$s ="";
		foreach(safe_array($this->arr) as $prop => $direction)
		{
			$s .= $prop."=>".$direction;
		}
		return $s;
	}
}
