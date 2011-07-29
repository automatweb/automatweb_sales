<?php

class _int_obj_container_base
{
	function save()
	{
		return $this->foreach_o(array(
			"func" => "save",
			"save" => false
		));
	}

	function delete($full = null)
	{
		if ($full !== null)
		{
			return $this->foreach_o(array(
				"func" => "delete",
				"params" => array($full),
				"save" => false
			));
		}
		else
		{
			$cnt = $this->foreach_o(array(
				"func" => "delete",
				"save" => false
			));
		}
		$this->_int_init_empty();
		return $cnt;
	}

	///////////////////////////////////
	// object setter functions       //
	///////////////////////////////////

	function set_parent($param)
	{
		return $this->_int_setter("set_parent", $param);
	}

	function set_name($param)
	{
		return $this->_int_setter("set_name", $param);
	}

	function set_class($param)
	{
		return $this->_int_setter("set_class", $param);
	}

	function set_status($param)
	{
		return $this->_int_setter("set_status", $param);
	}

	function set_lang($param)
	{
		return $this->_int_setter("set_lang", $param);
	}

	function set_lang_id($param)
	{
		return $this->_int_setter("set_lang_id", $param);
	}

	function set_comment($param)
	{
		return $this->_int_setter("set_comment", $param);
	}

	function set_ord($param)
	{
		return $this->_int_setter("set_ord", $param);
	}

	function set_alias($param)
	{
		return $this->_int_setter("set_alias", $param);
	}

	function set_period($param)
	{
		return $this->_int_setter("set_period", $param);
	}

	function set_periodic($param)
	{
		return $this->_int_setter("set_periodic", $param);
	}

	function set_site_id($param)
	{
		return $this->_int_setter("set_site_id", $param);
	}

	function set_subclass($param)
	{
		return $this->_int_setter("set_subclass", $param);
	}

	function set_flags($param)
	{
		return $this->_int_setter("set_flags", $param);
	}

	function set_flag($flag, $val)
	{
		return $this->_int_setter("set_flag", array($flag, $val));
	}

	function set_meta($key, $val)
	{
		return $this->_int_setter("set_meta", array($key, $val));
	}

	function set_prop($prop, $val)
	{
		return $this->_int_setter("set_prop", array($prop, $val));
	}

	function merge($param)
	{
		return $this->_int_setter("merge", $param);
	}

	function merge_prop($param)
	{
		return $this->_int_setter("merge_prop", $param);
	}

	function set_implicit_save($param)
	{
		return $this->_int_setter("set_implicit_save", $param);
	}

	function _int_setter($fun, $param = NULL)
	{
		if ($param != NULL)
		{
			return $this->foreach_o(array(
				"func" => $fun,
				"params" => $param,
				"save" => true
			));
		}
		else
		{
			return $this->foreach_o(array(
				"func" => $fun,
				"save" => true
			));
		}
	}
}
