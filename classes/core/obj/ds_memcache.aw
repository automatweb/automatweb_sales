<?php

namespace automatweb;

class _int_obj_ds_memcache extends _int_obj_ds_decorator
{
	private static $memcache = NULL;

	function __construct($contained)
	{
		parent::_int_obj_ds_decorator($contained);

		if (null === self::$memcache)
		{
			$memcache = new Memcache;
			$success = $memcache->connect('localhost', 11211);

			if (!$success)
			{
				throw new awex_obj_memcache_connect("Couldn't connect to memcache host");
			}

			self::$memcache = $memcache;
		}
	}

	function get_objdata($oid, $param = array())
	{
		if (
			!self::$memcache->get("storage_object_data") or
			!empty($GLOBALS["object2version"][$oid]) && $GLOBALS["object2version"][$oid] !== "_act" or
			// check if it is in the cache
			isset($this->contained->read_properties_data_cache[$oid]) or
			!empty($GLOBALS["__obj_sys_opts"]["no_cache"])
		)
		{
			return $this->contained->get_objdata($oid, $param);
		}

		$key = "objdata-{$oid}";
		$ret = self::$memcache->get($key);

		if (!is_array($ret))
		{
			$ret = $this->contained->get_objdata($oid, $param);
			if (!obj_get_opt("no_cache"))
			{
				$success = self::$memcache->set($key, $ret);

				if (!$success)
				{
					throw new awex_obj_memcache_set("Couldn't cache object with id '{$oid}'");
				}

				$success = self::$memcache->set("storage_object_data", 1);
				if (!$success)
				{
					throw new awex_obj_memcache_set("Couldn't set object data cache readable'");
				}
			}
		}

		return $ret;
	}

	function read_properties($arr)
	{
		$oid = $arr["objdata"]["oid"];
		if (
			!self::$memcache->get("storage_object_data") or
			(!empty($GLOBALS["object2version"][$oid]) && $GLOBALS["object2version"][$oid] != "_act") or
			!empty($GLOBALS["__obj_sys_opts"]["no_cache"])
		)
		{
			return $this->contained->read_properties($arr);
		}

		// check if it is in the cache
		if (isset($this->contained->read_properties_data_cache[$oid]))
		{
			return $this->contained->get_objdata($oid);
		}

		$key = "properties-{$oid}";
		$ret = self::$memcache->get($key);

		if (!is_array($ret))
		{
			$ret = $this->contained->read_properties($arr);
			if (!obj_get_opt("no_cache"))
			{
				$success = self::$memcache->set($key, $ret);
				if (!$success)
				{
					throw new awex_obj_memcache_set("Couldn't cache property data for object with id '{$oid}'");
				}

				$success = self::$memcache->set("storage_object_data", 1);
				if (!$success)
				{
					throw new awex_obj_memcache_set("Couldn't set object data cache readable'");
				}
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
			$success = self::$memcache->set("storage_search", 0);
			if (!$success)
			{
				throw new awex_obj_memcache_set("Couldn't clear search cache");
			}
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
			$success = self::$memcache->set("storage_search", 0);
			if (!$success)
			{
				throw new awex_obj_memcache_set("Couldn't clear search cache");
			}
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
				"brother_of" => $oid
			));
			$tarr[$oid] = 1;
			$char = array_keys($tarr);

			$failed_object_data = array();
			$failed_properties = array();
			foreach($char as $obj_id)
			{
				self::$memcache->delete("objdata-{$obj_id}") or ($failed_object_data[] = $obj_id);
				self::$memcache->delete("properties-{$obj_id}") or ($failed_properties[] = $obj_id);
			}

			if (count($failed_object_data))
			{
				foreach ($failed_object_data as $obj_id)
				{
					$str = self::$memcache->get("objdata-{$obj_id}");
					if (false !== $str)
					{
						throw new awex_obj_memcache_set("Couldn't clear object data for object '{$obj_id}'");
					}
				}
			}

			if (count($failed_properties))
			{
				foreach ($failed_properties as $obj_id)
				{
					$str = self::$memcache->get("properties-{$obj_id}");
					if (false !== $str)
					{
						throw new awex_obj_memcache_set("Couldn't clear properties data for object '{$obj_id}'");
					}
				}
			}

			$success = self::$memcache->set("storage_search", 0);
			if (!$success)
			{
				throw new awex_obj_memcache_set("Couldn't clear search cache");
			}
		}

		if ($propagate)
		{
			$this->contained->save_properties_cache_update($oid);
		}
	}

	function read_connection($id)
	{
		if (
			!self::$memcache->get("storage_object_data") or
			!empty($GLOBALS["__obj_sys_opts"]["no_cache"])
		)
		{
			return $this->contained->read_connection($id);
		}

		$key = "connection-{$id}";
		$ret = self::$memcache->get($key);

		if (!is_array($ret))
		{
			$ret = $this->contained->read_connection($id);
			if (!obj_get_opt("no_cache"))
			{
				$success = self::$memcache->set($key, $ret);
				if (!$success)
				{
					throw new awex_obj_memcache_set("Couldn't cache connection data for object with id '{$id}'");
				}

				$success = self::$memcache->set("storage_object_data", 1);
				if (!$success)
				{
					throw new awex_obj_memcache_set("Couldn't set object data cache readable'");
				}
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
				$success = self::$memcache->delete("connection-{$oid}");
				if (!$success and false !== self::$memcache->get("connection-{$oid}"))
				{
					throw new awex_obj_memcache_set("Couldn't clear connection cache for '{$oid}'");
				}
			}
		}

		if (!obj_get_opt("no_cache"))
		{
			$success = self::$memcache->set("storage_search", 0);
			if (!$success)
			{
				throw new awex_obj_memcache_set("Couldn't clear search cache");
			}
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
			$success = self::$memcache->delete("connection-{$oid}");
			if (!$success and false !== self::$memcache->get("connection-{$oid}"))
			{
				throw new awex_obj_memcache_set("Couldn't clear connection cache for '{$oid}'");
			}

			$success = self::$memcache->set("storage_search", 0);
			if (!$success)
			{
				throw new awex_obj_memcache_set("Couldn't clear search cache");
			}
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
		if (
			!self::$memcache->get("storage_search") or
			!empty($GLOBALS["__obj_sys_opts"]["no_cache"])
		)
		{
			return $this->contained->find_connections($arr);
		}

		$query_hash = md5(serialize($arr));
		$key = "conn_find-{$query_hash}";
		$ret = self::$memcache->get($key);

		if (!is_array($ret))
		{
			$ret = $this->contained->find_connections($arr);
			if (!obj_get_opt("no_cache"))
			{
				$success = self::$memcache->set($key, $ret);
				if (!$success)
				{
					throw new awex_obj_memcache_set("Couldn't cache connection search data (" . var_export($arr, true) . ")");
				}

				$success = self::$memcache->set("storage_search", 1);
				if (!$success)
				{
					throw new awex_obj_memcache_set("Couldn't set search cache readable'");
				}
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
			$success = self::$memcache->set("storage_search", 0);
			if (!$success)
			{
				throw new awex_obj_memcache_set("Couldn't clear search cache");
			}

			$success = self::$memcache->delete("objdata-{$oid}");
			if (!$success and false !== self::$memcache->get("objdata-{$oid}"))
			{
				throw new awex_obj_memcache_set("Couldn't clear object data cache for '{$oid}'");
			}

			$success = self::$memcache->delete("properties-{$oid}");
			if (!$success and false !== self::$memcache->get("properties-{$oid}"))
			{
				throw new awex_obj_memcache_set("Couldn't clear properties cache for '{$oid}'");
			}
		}

		if ($propagate)
		{
			$this->contained->delete_object_cache_update($oid);
		}
	}

	function search($params, $to_fetch = NULL)
	{
		if (
			!self::$memcache->get("storage_search") or
			!empty($GLOBALS["__obj_sys_opts"]["no_cache"])
		)
		{
			return $this->contained->search($params, $to_fetch);
		}

		$query_hash = md5(serialize($params).serialize($to_fetch));
		$key = "obj_find-{$query_hash}";
		$ret = self::$memcache->get($key);

		if (!is_array($ret))
		{
			$ret = $this->contained->search($params, $to_fetch);
			if (!obj_get_opt("no_cache"))
			{
				$success = self::$memcache->set($key, $ret);
				if (!$success)
				{
					throw new awex_obj_memcache_set("Couldn't cache connection search data (" . var_export($params, true) . ")");
				}

				$success = self::$memcache->set("storage_search", 1);
				if (!$success)
				{
					throw new awex_obj_memcache_set("Couldn't set search cache readable'");
				}
			}
		}

		return $ret;
	}

	function fetch_list($param)
	{
		if (
			!self::$memcache->get("storage_search") or
			!empty($GLOBALS["__obj_sys_opts"]["no_cache"])
		)
		{
			return $this->contained->fetch_list($param);
		}

		$query_hash = md5(serialize($param));
		$key = "obj_fetch-{$query_hash}";
		$ret = self::$memcache->get($key);

		if (!is_array($ret))
		{
			$ret = $this->contained->fetch_list($param);
			if (!obj_get_opt("no_cache"))
			{
				$success = self::$memcache->set($key, $ret);
				if (!$success)
				{
					throw new awex_obj_memcache_set("Couldn't cache connection search data (" . var_export($param, true) . ")");
				}

				$success = self::$memcache->set("storage_search", 1);
				if (!$success)
				{
					throw new awex_obj_memcache_set("Couldn't set search cache readable'");
				}
			}
		}
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
			$success = self::$memcache->set("storage_search", 0);
			if (!$success)
			{
				throw new awex_obj_memcache_set("Couldn't clear search cache");
			}

			$success = self::$memcache->set("storage_object_data", 0);
			if (!$success)
			{
				throw new awex_obj_memcache_set("Couldn't clear object data cache");
			}
		}

		if ($propagate)
		{
			$this->contained->originalize_cache_update($oid);
		}
	}
}

/** Generic memcache error **/
class awex_obj_memcache extends awex_obj {}

/** Memcache host connection error **/
class awex_obj_memcache_connect extends awex_obj_memcache {}

/** Caching attempt failure **/
class awex_obj_memcache_set extends awex_obj_memcache {}

?>
