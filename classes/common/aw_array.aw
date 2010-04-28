<?php

namespace automatweb;

/**
	@comment
	The class is meant to make array handling a bit easier and to avoid statements like
		if (is_array($arr))
		// do_something

	wrapper for arrays - helps to get rid of numerous is_array checks
	in code and reduces the amount of indenting
**/
class aw_array
{
	private $arg = array();

	/** aw_array is a class provided by the AutomatWeb base system and that simplifies php's array management.
		@attrib api=1

		@param arr optional type=array
			if this argument is specified, it is assumed to be a php array that the aw_array object will contain

		@comment
			The constructor, can initialize the class with an array

		@examples
			$arr = new aw_array($arr["request"]["some_array"]);
			foreach($arr->get() as $key => $value)
			{
			...
			}
	**/
	function aw_array($arg = false)
	{
		if (is_array($arg))
		{
			$this->arg = $arg;
		}
		elseif ($arg)
		{
			$this->arg = array($arg);
		}
		else
		{
			$this->arg = array();
		}
		reset($this->arg);
	}

	/** returns the contents of the aw_array as a php array
		@attrib api=1
	**/
	function &get()
	{
		return $this->arg;
	}

	/** returns the value in the array at the position specified by $key
		@attrib api=1

		@examples
			$tmpmatch = new aw_array();
			$this->db_query("SELECT DISTINCT(objects.oid) AS oid FROM objects WHERE $wis ");
			while($match = $this->db_next())
			{
				 $tmpmatch->set($match["oid"]);
			};

			// now we need to make sure that we get only one list member per form entry
			$usedeids = new aw_array();
			$this->db_query("SELECT entry_id, member_id FROM ml_member2form_entry WHERE member_id IN(".$tmpmatch->to_sql().")");
			while ($row = $this->db_next())
			{
				 if (!$usedeids->get_at($row["entry_id"]))
				 {
				  $matches[$id][]=$row["member_id"];
				  $usedeids->set_at($row["entry_id"], true);
				 }
			}

	**/
	function get_at($key)
	{
		return isset($this->arg[$key]) ? $this->arg[$key] : null;
	}

	/** adds the value of $val to the end of the array (does what the php expression $arr[] = $val would do if $arr was a regular php array)
		@attrib api=1

		@examples
			$tmpmatch = new aw_array();
			$this->db_query("SELECT DISTINCT(objects.oid) AS oid FROM objects WHERE $wis ");
			while($match = $this->db_next())
			{
				 $tmpmatch->set($match["oid"]);
			};
	**/
	function set($val)
	{
		$this->arg[] = $val;
	}

	/** sets the value at position $key in the array to $val
		@attrib api=1

		@examples
			$tmpmatch = new aw_array();
			$this->db_query("SELECT DISTINCT(objects.oid) AS oid FROM objects WHERE $wis ");
			while($match = $this->db_next())
			{
				 $tmpmatch->set($match["oid"]);
			};

			// now we need to make sure that we get only one list member per form entry
			$usedeids = new aw_array();
			$this->db_query("SELECT entry_id, member_id FROM ml_member2form_entry WHERE member_id IN(".$tmpmatch->to_sql().")");
			while ($row = $this->db_next())
			{
				 if (!$usedeids->get_at($row["entry_id"]))
				 {
					  $matches[$id][]=$row["member_id"];
					  $usedeids->set_at($row["entry_id"], true);
				 }
			}
	**/
	function set_at($key, $val)
	{
		$this->arg[$key] = $val;
	}

	/** returns the next array(key, value) from the array. Only one iteration per aw_array instance can be active at one time. if there are no more members, false is returned
		@attrib api=1
	**/
	function next()
	{
		return each($this->arg);
	}

	/** resets the internal iterator for the current aw_array instance, after this, next() will return the first key/value pair of the aw_array
		@attrib api=1
	**/
	function reset()
	{
		reset($this->arg);
	}

	/** checks if the given key exists in the current array
		@attrib api=1 params=pos

		@param key required type=string
			The key to check

		@returns
			true if the given key exists in the array, false if not
	**/
	function key_exists($key)
	{
		return isset($this->arg[$key]);
	}

	/** returns the first element in the array
		@attrib api=1
	**/
	function first()
	{
		$this->reset();
		return $this->next();
	}

	/** returns the contents of the aw_array in a form suitable to be inserted into an SQL IN() clause. basically it returns a string that contains all the values of the array separated by commas. if the array is empty, the string NULL will be returned, so that no value will be matched by the resulting SQL statement
		@attrib api=1

		@example
			$tmpmatch = new aw_array();
			$this->db_query("SELECT DISTINCT(objects.oid) AS oid FROM objects WHERE $wis ");
			while($match = $this->db_next())
			{
				 $tmpmatch->set($match["oid"]);
			};

			// now we need to make sure that we get only one list member per form entry
			$usedeids = new aw_array();
			$this->db_query("SELECT entry_id, member_id FROM ml_member2form_entry WHERE member_id IN(".$tmpmatch->to_sql().")");
			while ($row = $this->db_next())
			{
			 if (!$usedeids->get_at($row["entry_id"]))
			 {
			  $matches[$id][]=$row["member_id"];
			  $usedeids->set_at($row["entry_id"], true);
			 }
			}

	**/
	function to_sql()
	{
		$data = array_values($this->arg);
		foreach($data as $k => $v)
		{
			$data[$k] = addslashes($v);
		}

		$str = join(",",map("'%s'", $data));
		if ($str == "")
		{
			return "NULL";
		}
		return $str;
	}

	function count()
	{
		return count($this->arg);
	}
}

?>
