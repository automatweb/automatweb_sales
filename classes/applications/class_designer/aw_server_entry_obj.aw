<?php

class aw_server_entry_obj extends _int_object
{
	const CLID = 1499;


	function name()
	{
		return $this->prop("name");
	}

	function set_name($p)
	{
		parent::set_name($p);
		return parent::set_prop("name", $p);
	}

	function set_prop($k, $v)
	{
		$rv = parent::set_prop($k, $v);
		if ($k == "name")
		{
			parent::set_name($v);
		}
		else
		if ($k == "server_id")
		{
			if (!$v)
			{
				return parent::set_prop("server_oid", 0);
			}
			// set server oid accordingly
			$ol = new object_list(array(
				"class_id" => CL_AW_SERVER_ENTRY,
				"server_id" => $v,
				"site_id" => array(),
				"lang_id" => array()
			));
			if (!$ol->count())
			{
				parent::set_prop("server_oid", 0);
				parent::set_prop("server_id", 0);
				return $rv;
			}
			$o = $ol->begin();
			parent::set_prop("server_oid", $o->id());
		}
		else
		if ($k == "server_oid")
		{
			if ($this->can("view", $v))
			{
				$o = obj($v);
				parent::set_prop("server_oid", $o->id());
				parent::set_prop("server_id", $o->server_id);
				return $rv;
			}
			else
			{
				parent::set_prop("server_oid", 0);
				parent::set_prop("server_id", 0);
				return $rv;
			}
		}
		return $rv;
	}
}

?>
