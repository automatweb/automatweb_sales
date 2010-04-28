<?php

namespace automatweb;

define("OBJ_SQL_UNIQUE", 1);
define("OBJ_SQL_COUNT", 2);
define("OBJ_SQL_MAX", 3);
define("OBJ_SQL_MIN", 4);

class obj_sql_func
{
	const UNIQUE = 1;
	const COUNT = 2;
	const MAX = 3;
	const MIN = 4;

	public $sql_func;
	public $name = "";
	public $params = array();

	function obj_sql_func($func, $name, $params = array())
	{
		$this->sql_func = $func;
		$this->params = $params;
		$this->name = $name;
	}

	function __toString()
	{
		return "obj_sql_func(".$this->sql_func.",".$this->name.",".join($this->params).")";
	}
}
