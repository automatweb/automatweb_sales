<?php

class crm_phone_obj extends _int_object
{
	/**
		@param id required type=oid,array(oid)
	**/
	function get_persons($arr)
	{
		$ret = new object_list;

		// The phone might be connected to the person via work relation.
		$cs = connection::find(array(
			"from" => array(),
			"to" => $arr["id"],
			"type" => "RELTYPE_PHONE",
			"from.class_id" => CL_CRM_PERSON_WORK_RELATION,
		));
		if(count($cs) > 0)
		{
			$wrids = array();
			foreach($cs as $c)
			{
				$wrids[] = $c["from"];
			}
			$cs = connection::find(array(
				"from" => array(),
				"to" => $wrids,
				"from.class_id" => CL_CRM_PERSON,
			));
			foreach($cs as $c)
			{
				$ret->add($c["from"]);
			}
		}
		$cs = connection::find(array(
			"from" => array(),
			"to" => $arr["id"],
			"type" => "RELTYPE_PHONE",
			"from.class_id" => CL_CRM_PERSON,
		));
		foreach($cs as $c)
		{
			$ret->add($c["from"]);
		}

		return $ret;
	}

	function prop($k)
	{
		if($k === "is_public" && is_numeric(parent::prop("conn_id")))
		{
			try
			{
				$c = new connection();
				$c->load(parent::prop("conn_id"));
				return $c->prop("data");
			}
			catch (Exception $e)
			{
				return parent::prop($k);
			}
		}
		return parent::prop($k);
	}

	public function set_name($v)
	{
		parent::set_prop("clean_number", preg_replace("/[^0-9]/", "", $v));
		return parent::set_name(trim($v));
	}

	public function awobj_set_name($name)
	{
		return $this->set_name($name);
	}

	public function awobj_set_is_public($v)
	{
		if(is_numeric(parent::prop("conn_id")))
		{
			try
			{
				$c = new connection();
				$c->load(parent::prop("conn_id"));
				$c->change(array(
					"data" => $v,
				));
			}
			catch (Exception $e)
			{
			}
		}

		return parent::prop("is_public");
	}

/*
	function save()
	{
		$oid = parent::id();

		$conn_id = parent::prop("conn_id");
		// This is not supposed to be saved.
		parent::set_prop("conn_id", NULL);
		$conn_ids = isset($conn_id) ? (is_array($conn_id) ? $conn_id : array($conn_id)) : array();

		// New
		if(!is_oid($oid))
		{
			return $this->parent_save($conn_ids);
		}

		// If no connections remain with the old phone obj, there's no point in keeping it. So we'll just change the current one.
		if(count($this->conns_remain_unchanged($conn_ids)) == 0)
		{
			return $this->parent_save($conn_ids);
		}

		// Getting the current name..
		$q = oql::compile_query("SELECT name FROM CL_CRM_PHONE WHERE CL_CRM_PHONE.oid = %u");
		$r = oql::execute_query($q, $oid);

		$nname = parent::prop("name");
		$cname = $r[$oid]["name"];

		$old_obj = obj($oid);

		if($nname !== $cname)
		{
			$ol = new object_list(array(
				"class_id" => CL_CRM_PHONE,
				"name" => $nname,
				"lang_id" => array(),
				"site_id" => array(),
				"limit" => 1,
			));
			if($ol->count() > 0)
			{
				$phoid = reset($ol->ids());
			}
			else
			{
				$pho = obj(parent::save_new());
				$pho->name = $nname;
				$phoid = $pho->save();
			}

			if(count($conn_ids) > 0)
			{
				foreach($conn_ids as $conn_id)
				{
					if(!is_numeric($conn_id))
					{
						continue;
					}
					try
					{
						$c = new connection();
						$c->load($conn_id);
						$c->change(array(
							"from" => $c->prop("from") == $oid ? $phoid : $c->prop("from"),
							"to" => $c->prop("to") == $oid ? $phoid : $c->prop("to"),
						));
					}
					catch (Exception $e)
					{
					}
				}
			}
			parent::load($phoid);
			return $phoid;
		}
		return parent::save();
	}
*/
	private function conns_remain_unchanged($conns)
	{
		$r = array();
		foreach(parent::connections_from(array()) as $c)
		{
			if(!in_array($c->id(), $conns))
			{
				$r[] = $c;
			}
		}
		foreach(parent::connections_to(array()) as $c)
		{
			if(!in_array($c->id(), $conns))
			{
				$r[] = $c;
			}
		}
		return $r;
	}

	private function parent_save($conn_ids)
	{
		$ol = new object_list(array(
			"class_id" => CL_CRM_PHONE,
			"name" => parent::prop("name"),
			"lang_id" => array(),
			"site_id" => array(),
			"limit" => 1,
		));
		if($ol->count() > 0)
		{
			$id = reset($ol->ids());
			// If it's connected to anything, we have to change to connections also!
			if(is_oid(parent::id()))
			{
				foreach($conn_ids as $conn_id)
				{
					try
					{
						$c = new connection();
						$c->load($conn_id);
						$c->change(array(
							"from" => $c->prop("from") == parent::id() ? $id : $c->prop("from"),
							"to" => $c->prop("to") == parent::id() ? $id : $c->prop("to"),
						));
					}
					catch (Exception $e)
					{
					}
				}
			}
			parent::load($id);
			return $id;
		}
		else
		{
			return parent::save();
		}
	}
}

?>
