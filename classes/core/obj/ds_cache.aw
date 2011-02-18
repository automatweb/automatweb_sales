<?php

class _int_obj_ds_cache extends _int_obj_ds_decorator
{
	function _int_obj_ds_cache($contained)
	{
		parent::_int_obj_ds_decorator($contained);
	}

	function get_objdata($oid, $param = array())
	{
		if (!empty($GLOBALS["object2version"][$oid]) && $GLOBALS["object2version"][$oid] !== "_act")
		{
			return $this->contained->get_objdata($oid, $param);
		}

		// check if it is in the cache
		if (isset($this->contained->read_properties_data_cache[$oid]))
		{
			return $this->contained->get_objdata($oid, $param);
		}

		if (!empty($GLOBALS["__obj_sys_opts"]["no_cache"]))
		{
			return $this->contained->get_objdata($oid, $param);
		}

		$c_fn = "objdata-{$oid}";

		$ret = aw_unserialize(cache::file_get_pt_oid(
			"storage_object_data",
			$oid,
			$c_fn
		), false, true);

		if (!is_array($ret))
		{
			$ret = $this->contained->get_objdata($oid, $param);
			if (!obj_get_opt("no_cache"))
			{
				cache::file_set_pt_oid(
					"storage_object_data",
					$oid,
					$c_fn,
					aw_serialize($ret, SERIALIZE_NATIVE)
				);
			}
		}
		return $ret;
	}

	function read_properties($arr)
	{
		$oid = $arr["objdata"]["oid"];
		if ((!empty($GLOBALS["object2version"][$oid]) && $GLOBALS["object2version"][$oid] != "_act"))
		{
			return $this->contained->read_properties($arr);
		}

		// check if it is in the cache
		if (isset($this->contained->read_properties_data_cache[$oid]))
		{
			return $this->contained->get_objdata($oid);
		}

		if (!empty($GLOBALS["__obj_sys_opts"]["no_cache"]))
		{
			return $this->contained->read_properties($arr);
		}

		$c_fn = "properties-{$oid}";

		$ret = aw_unserialize(cache::file_get_pt_oid(
			"storage_object_data",
			$oid,
			$c_fn
		), false, true);

		if (!is_array($ret))
		{
			$ret = $this->contained->read_properties($arr);
			if (!obj_get_opt("no_cache"))
			{
				cache::file_set_pt_oid(
					"storage_object_data",
					$oid,
					$c_fn,
					aw_serialize($ret, SERIALIZE_NATIVE)
				);
			}
		}
		return $ret;
	}

	function create_new_object($arr)
	{
		$id =  $this->contained->create_new_object($arr);

		// after creating a new object, we need to clear storage search cache and html cache
		// but html cache clear is done in ds_mysql, not here
		$this->create_new_object_cache_update(null);

		return $id;
	}

	function create_new_object_cache_update($oid, $propagate = false)
	{
		if (!obj_get_opt("no_cache"))
		{
			cache::file_clear_pt("storage_search");
		}
		if ($propagate)
		{
			$this->contained->create_new_object_cache_update($oid);
		}
	}

	function create_brother($arr)
	{
		$id =  $this->contained->create_brother($arr);
		$this->create_brother_cache_update(null);
		return $id;
	}

	function create_brother_cache_update($oid, $propagate = false)
	{
		// after creating a new object, we need to clear storage search cache and html cache
		// but html cache clear is done in ds_mysql, not here
		if (!obj_get_opt("no_cache"))
		{
			cache::file_clear_pt("storage_search");
		}
		if ($propagate)
		{
			$this->contained->create_brother_cache_update($oid);
		}
	}

	function save_properties($arr)
	{
		$ret = $this->contained->save_properties($arr);
		// fetch brothers and clear cache for all of them
		$this->save_properties_cache_update($arr["objdata"]["brother_of"]);
		return $ret;
	}

	function save_properties_cache_update($oid, $propagate = false)
	{
		if (!obj_get_opt("no_cache"))
		{
			list($tarr) = $this->contained->search(array(
				"brother_of" => $oid,
				"site_id" => array(),
				"lang_id" => array()
			));
			$tarr[$oid] = 1;
			$char = array_keys($tarr);
			foreach($char as $obj_id)
			{
				cache::file_clear_pt_oid_fn("storage_object_data", $obj_id, "objdata-$obj_id");
				cache::file_clear_pt_oid_fn("storage_object_data", $obj_id, "properties-$obj_id");
			}

			cache::file_clear_pt("storage_search");
		}
		if ($propagate)
		{
			$this->contained->save_properties_cache_update($oid);
		}
	}

	function read_connection($id)
	{
		if (!empty($GLOBALS["__obj_sys_opts"]["no_cache"]))
		{
			return $this->contained->read_connection($id);
		}

		$c_fn = "connection-{$id}";

		$ret = aw_unserialize(cache::file_get_pt_oid(
			"storage_object_data",
			$id,
			$c_fn
		), false, true);

		if (!is_array($ret))
		{
			$ret = $this->contained->read_connection($id);
			if (!obj_get_opt("no_cache"))
			{
				cache::file_set_pt_oid(
					"storage_object_data",
					$id,
					$c_fn,
					aw_serialize($ret, SERIALIZE_NATIVE)
				);
			}
		}
		return $ret;
	}

	////
	// !saves connection
	function save_connection($data)
	{
		$ret =  $this->contained->save_connection($data);
		$oid = isset($data["id"]) ? $data["id"] : null;//!!!???
		$this->save_connection_cache_update($oid);
		return $ret;
	}

	function save_connection_cache_update($oid, $propagate = false)
	{
		// here we must clear storage search, because it can contain searches by conn and that connection's cache
		// also html cache, but that gets done one level deeper
		if ($oid)
		{
			if (!obj_get_opt("no_cache"))
			{
				cache::file_clear_pt_oid_fn("storage_object_data", $oid, "connection-{$oid}");
			}
		}

		if (!obj_get_opt("no_cache"))
		{
			cache::file_clear_pt("storage_search");
		}

		if ($propagate)
		{
			$this->contained->save_connection_cache_update($oid);
		}
	}

	////
	// !deletes connection $id
	function delete_connection($id)
	{
		$ret = $this->contained->delete_connection($id);
		$this->delete_connection_cache_update($id);
		return $ret;
	}

	function delete_connection_cache_update($oid, $propagate = false)
	{
		// here we must clear storage search, because it can contain searches by conn and that connection's cache
		// also html cache, but that gets done one level deeper
		if (!obj_get_opt("no_cache"))
		{
			cache::file_clear_pt_oid_fn("storage_object_data", $oid, "connection-{$oid}");
			cache::file_clear_pt("storage_search");
		}
		if ($propagate)
		{
			$this->contained->delete_connection_cache_update($oid);
		}
	}

	////
	// !returns all connections that match filter
	function find_connections($arr)
	{
		if (!empty($GLOBALS["__obj_sys_opts"]["no_cache"]))
		{
			return $this->contained->find_connections($arr);
		}

		$query_hash = md5(serialize($arr));

		$c_fn = "conn_find-{$query_hash}";

		$ret = aw_unserialize(cache::file_get_pt(
			"storage_search",
			$query_hash[0],
			$c_fn
		), false, true);

		if (!is_array($ret))
		{
			$ret = $this->contained->find_connections($arr);
			if (!obj_get_opt("no_cache"))
			{
				cache::file_set_pt(
					"storage_search",
					$query_hash[0],
					$c_fn,
					aw_serialize($ret, SERIALIZE_NATIVE)
				);
			}
		}
		return $ret;
	}


	function delete_object($oid)
	{
		$ret = $this->contained->delete_object($oid);
		aw_cache_flush("__aw_acl_cache");
		$this->delete_object_cache_update($oid);
		return $ret;
	}

	function delete_object_cache_update($oid, $propagate = false)
	{
		// clear lots of caches here: html, acl for oid, storage_search, storage_objdata for $oid
		if (!obj_get_opt("no_cache"))
		{
			cache::file_clear_pt_oid("acl", $oid);
			cache::file_clear_pt("storage_search");
			cache::file_clear_pt_oid_fn("storage_object_data", $oid, "objdata-{$oid}");
			cache::file_clear_pt_oid_fn("storage_object_data", $oid, "properties-{$oid}");
		}
		if ($propagate)
		{
			$this->contained->delete_object_cache_update($oid);
		}
	}

	function search($params, $to_fetch = NULL)
	{
		if (!empty($GLOBALS["__obj_sys_opts"]["no_cache"]))
		{
			return $this->contained->search($params, $to_fetch);
		}

		$tp = $params;
		if (!isset($tp["lang_id"]))
		{
			$tp["lang_id"] = aw_global_get("lang_id");
		}
		if (!isset($tp["site_id"]))
		{
			$tp["site_id"] = aw_ini_get("site_id");
		}
		$query_hash = md5(serialize($tp).serialize($to_fetch));

		$c_fn = "obj_find-{$query_hash}";

		$ret = aw_unserialize(cache::file_get_pt(
			"storage_search",
			$query_hash[0],
			$c_fn
		), false, true);

		if (!is_array($ret))
		{
			$ret = $this->contained->search($params, $to_fetch);
			if (!obj_get_opt("no_cache"))
			{
				cache::file_set_pt(
					"storage_search",
					$query_hash[0],
					$c_fn,
					aw_serialize($ret, SERIALIZE_NATIVE)
				);
			}
		}
		return $ret;
	}

	function fetch_list($param)
	{
		if (!empty($GLOBALS["__obj_sys_opts"]["no_cache"]))
		{
			return $this->contained->fetch_list($param);
		}

		$query_hash = md5(serialize($param));

		$c_fn = "obj_fetch-{$query_hash}";

		$ret = $this->contained->fetch_list($param);
		return $ret;
	}

	function originalize($oid)
	{
		$rv =  $this->contained->originalize($oid);
		$this->originalize_cache_update($oid);
		return $rv;
	}

	function originalize_cache_update($oid, $propagate = false)
	{
		if (!obj_get_opt("no_cache"))
		{
			cache::file_clear_pt("storage_search");
			cache::file_clear_pt("storage_object_data");
		}
		if ($propagate)
		{
			$this->contained->originalize_cache_update($oid);
		}
	}
}
