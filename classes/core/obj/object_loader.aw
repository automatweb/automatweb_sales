<?php
/*

this message will get called whenever an object is saved and given the class_id as the message type parameter
and the object's id as the "oid" parameter
EMIT_MESSAGE(MSG_STORAGE_SAVE)

this message will get called whenever a new object is created and given class_id as the message type parameter
and the object's id as the "oid" parameter
EMIT_MESSAGE(MSG_STORAGE_NEW)

*/

/**
Class that provides global object system
Contains knowledge about and interface to used data sources
All methods should be static
**/
class object_loader
{
	private static $instance = false;

	public static function instance()
	{
		if (false === self::$instance)
		{
			$GLOBALS["objects"] = array();
			$GLOBALS["properties"] = array();
			$GLOBALS["tableinfo"] = array();
			$GLOBALS["of2prop"] = array();
			$GLOBALS["__obj_sys_opts"] = array(
				"no_cache" => false
			);
			self::$instance = new _int_object_loader();
		}

		return self::$instance;
	}

	/** Tells if user can perform operation on object
		@attrib api=1 params=pos
		@param operation_id type=string
		@param object_id type=oid
		@param user_oid type=oid default=NULL
			Defaults to current user if not specified
		@comment
		@returns bool
		@errors none
	**/
	public static function can($operation_id, $object_id, $user_oid = null)
	{
		return false === self::$instance ? false : self::$instance->ds->can($operation_id, $object_id, $user_oid);
	}

	/**
		@attrib api=1 params=pos
		@param name type=string
		@comment
		@returns mixed
		@errors
			throws awex_obj_param if option by $name doesn't exist
	**/
	public static function opt($name)
	{
		if (!isset($GLOBALS["__obj_sys_opts"][$name]))
		{
			throw new awex_obj_param("Object system option '{$name}' doesn't exist");
		}

		return $GLOBALS["__obj_sys_opts"][$name];
	}
}

class _int_object_loader extends core
{
	// private variables, only object system classes can use these
	var $ds; 					// data source
	var $object_member_funcs;	// names of all object class member functions
	var $cfgu;					// cfgutilities instance
	var $cache;					// cache class instance
	var $__aw_acl_cache;		// acl memory cache

	private static $tmp_id_count = 0;
	private $registered = false;
	public $obj_inherit_props_conf = array();

	function _int_object_loader()
	{
		$this->init();

		$this->__aw_acl_cache = array();

		$this->all_ot_flds = array_flip(array(
			"parent", "name", "class_id",
			"modified", "created", "status", "lang_id",
			"comment", "modifiedby", "jrk",
			"period", "alias", "periodic",
			"site_id", "metadata",
			"subclass", "flags", "brother_of"
		));

		// init the datasource from the ini file setting
		$datasources = aw_ini_get("objects.default_datasource");
		if ($datasources == "")
		{
			// default to simple mysql
			$datasources = "mysql";
		}

		$dss = array_reverse(explode(",", $datasources));

		$clname = "_int_obj_ds_".$dss[0];
		// the first is the db specific ds, that does not contain anything
		$this->ds = new $clname();

		for ($i = 1; $i < count($dss); $i++)
		{
			$clname = "_int_obj_ds_".$dss[$i];
			$this->ds = new $clname($this->ds);
		}

		$this->object_member_funcs = get_class_methods("object");
		$this->cfgu = new cfgutils();
		$this->cache = new cache();

		$fn = aw_ini_get("site_basedir")."/files/obj_inherit_props.conf";
		if (file_exists($fn) && is_readable($fn))
		{
			$f = fopen($fn, "r");
			if ($f)
			{
				$this->obj_inherit_props_conf = safe_array(aw_unserialize(fread($f, filesize($fn)), false, true));
				fclose($f);
			}
		}
	}

	private function oid_for_alias($alias)
	{
		if (substr($alias,-1) === "/")
		{
			$alias = substr($alias,0,-1);
		}

		// if the site defines recursive aliases, then use those
		if (aw_ini_get("menuedit.recursive_aliases"))
		{
			// split the string at "/"
			// and find each alias part under the previous
			$parts = explode("/", $alias);

			$part = array_shift($parts);

			$parent = $this->ds->get_oid_by_alias(array(
				"alias" => $part,
				"site_id" => aw_ini_get("site_id")
			));

			foreach($parts as $part)
			{
				$parent = $this->ds->get_oid_by_alias(array(
					"alias" => $part,
					"site_id" => aw_ini_get("site_id"),
					"parent" => $parent
				));
			}
			return $parent;
		}
		else
		// else just try to match the whole string
		{
			return $this->ds->get_oid_by_alias(array(
				"alias" => $alias,
				"site_id" => aw_ini_get("site_id")
			));
		}
	}

	// returns oid in param, no list!
	public function param_to_oid($param)
	{
		if (is_oid($param))
		{
			list($param) = explode(":", $param);
			return $param;
		}
		elseif (is_string($param))
		{
			$oid = $this->oid_for_alias($param);
			if (!$oid)
			{
				throw new awex_oid("Invalid object alias '{$param}'");
			}
			return $oid;
		}
		elseif ($param instanceof aw_oid)
		{
			return $param->get_string();
		}
		elseif ($param instanceof object || $param instanceof _int_object)
		{
			return $param->id();
		}

		throw new awex_oid("Invalid object parameter " . var_export($param, true));
	}

	// returns array of oids in param
	public function param_to_oid_list($param)
	{
		if (is_array($param))
		{
			$res = array();
			foreach($param as $item)
			{
				$res[] = $this->param_to_oid($item);
			}
			return $res;
		}
		elseif ($param instanceof object_list)
		{
			return $param->ids();
		}
		elseif ($param instanceof object_tree)
		{
			return $param->ids();
		}
		else
		{
			return array($this->param_to_oid($param));
		}

		throw new awex_oid("Invalid object parameter " . var_export($param, true));
	}

	////
	// !returns temp id for new object
	public function load_new_object($objdata = array(), $constructor_args = array())
	{
		// get tmp oid
		if (!isset($objdata["oid"]))
		{ // tmp oid for completely new object
			$cnt = ++self::$tmp_id_count;
			$oid = "new_object_temp_id_{$cnt}";
		}
		else
		{
			$oid = $objdata["oid"];
			unset($objdata["oid"]); // _int_object built to not know its own oid until saved. when this changes, remove.
		}

 		// determine class
		if (isset($objdata["class_id"]) and is_class_id($class = $objdata["class_id"]) and aw_ini_isset("classes.{$class}.object_override"))
 		{
			$class = basename(aw_ini_get("classes.{$class}.object_override"));
 		}
 		else
 		{
			$class = "_int_object";
 		}

		$GLOBALS["objects"][$oid] = new $class($objdata, $constructor_args);
		return $oid;
	}

	public function load($oid, $constructor_args = array())
	{
		if (!is_oid($oid))
		{
			throw new awex_obj_type("Parameter ('{$oid}') is not an object id");
		}

		if (isset($GLOBALS["objects"][$oid]))
		{
			$ob = $GLOBALS["objects"][$oid];
		}

		if (!isset($ob) || !is_object($ob))
		{
			// check access rights to object
			///TODO: access should be checked separately from loading
			if (!$this->ds->can("view", $oid))
			{
				$e = new awex_obj_acl("No view access object with id '{$oid}'.");
				$e->awobj_id = $oid;
				throw $e;
			}

			// load object data
			if (isset($GLOBALS["__obj_sys_objd_memc"][$oid]))
			{
				$objdata = $GLOBALS["__obj_sys_objd_memc"][$oid];
				$GLOBALS["__obj_sys_objd_memc"][$oid] = null;
				unset($GLOBALS["__obj_sys_objd_memc"][$oid]);
			}
			else
			{
				$objdata = $this->ds->get_objdata($oid);
			}

			// get class
			$class = aw_ini_isset("classes.{$objdata["class_id"]}.object_override") ? basename(aw_ini_get("classes.{$objdata["class_id"]}.object_override")) : "_int_object";
			$objdata["__obj_load_parameter"] = $oid;

			$ref = new $class($objdata, $constructor_args);
			if ($ref->id() === NULL)
			{
				throw new awex_obj_na("No object with id '{$oid}'");
			}
			else
			{
				if (!isset($GLOBALS["objects"][$oid]))
				{
					$GLOBALS["objects"][$oid] = $ref;
				}
			}
		}

		// also remove the entry from acl memc cause we dont need it no more
		if (isset($GLOBALS["__obj_sys_acl_memc"][$oid]))
		{
			$GLOBALS["__obj_sys_acl_memc"][$oid] = null;
			unset($GLOBALS["__obj_sys_acl_memc"][$oid]);
		}

		return $GLOBALS["objects"][$oid]->id();
	}

	public function save($oid, $exclusive = false, $previous_state = null)
	{
		if (!is_object($GLOBALS["objects"][$oid]))
		{
			error::raise(array(
				"id" => "ERR_OBJECT",
				"msg" => sprintf(t("object_loader::save(%s): no object with oid %s exists in the global list"), $oid, $oid)
			));
			return;
		}

		$t_oid = $GLOBALS["objects"][$oid]->save($exclusive, $previous_state);
		$msg_params = array(
			"oid" => $t_oid
		);

		if ($t_oid != $oid)
		{
			// relocate the object in the global list
			$GLOBALS["objects"][$t_oid] = $GLOBALS["objects"][$oid];
			$GLOBALS["objects"][$oid] = $GLOBALS["objects"][$t_oid];
			post_message_with_param("MSG_STORAGE_NEW", $GLOBALS["objects"][$t_oid]->class_id(), $msg_params);
		}
		// return the new value, so that the pointers go to the right place.
		//
		// the problem that there might be other pointers to the previous object should not arise,
		// because you probably will not be able to acquire pointers to temp-objects.
		// probably.
		// well, here's to hoping it won't happen!

		post_message_with_param("MSG_STORAGE_SAVE", $GLOBALS["objects"][$t_oid]->class_id(), $msg_params);

		static $lastmod_set;
		if (!$lastmod_set)
		{
			if (aw_ini_get("site_show.objlastmod_only_menu"))
			{
				if ($GLOBALS["objects"][$t_oid]->class_id() == CL_MENU)
				{
					// write the current time as last modification time of any object.
					cache::file_set("objlastmod", time());
				}
			}
			else
			{
				// write the current time as last modification time of any object.
				cache::file_set("objlastmod", time());
			}
			$lastmod_set = 1;
		}

		return $t_oid;
	}

	public function save_new($oid)
	{
		if (!is_object($GLOBALS["objects"][$oid]))
		{
			error::raise(array(
				"id" => "ERR_OBJECT",
				"msg" => sprintf(t("object_loader::save_new(%s): no object with oid %s exists in the global list"), $oid, $oid)
			));
			return;
		}

		// right. here we need to make a copy BEFORE calling save_new, because
		// otherwise the previous object will get it's oid ovewritten
		$t_o = $GLOBALS["objects"][$oid];
		$t_oid = $t_o->save_new();

		// copy the object to the new place
		$GLOBALS["objects"][$t_oid] = $t_o;
		cache::file_set("objlastmod", time());

		$params = array(
			"oid" => $t_oid
		);
		post_message_with_param("MSG_STORAGE_SAVE", $GLOBALS["objects"][$t_oid]->class_id(), $params);

		return $t_oid;
	}

	// returns true/false based on whether the parameter is the object class member function
	function is_object_member_fun($func)
	{
		return in_array($func, $this->object_member_funcs);
	}

	// load properties - arr[file] , arr[clid]
	public function load_properties($arr)
	{
		if ($arr["file"] === "document" || $arr["file"] === "document_brother")
		{
			$arr["file"] = "doc";
		}

		// if system is set, then no captions/translations/etc will be loaded,
		// since storage really doesn't care. so why should property loader?

		$arr["system"] = 1;

		// cfgu->load_properties is expensive, so we cache the results.
		// why here? because it does a lot more than just load properties
		// and it's a bit tricky to cache all that information there --duke
		//
		// removed the caching from here, it is done in object::_int_load_properties
		// it won't call this function if an object of the same class id has been loaded before
		// so doing the caching here is just wasting memory now.
		// - terryf
		$cls = aw_ini_get("classes");
		if (!isset($cls[$arr["clid"]]) && empty($arr["file"]))
		{
			return array(array(), array(), array(), array(), array());
		}
		$props = $this->cfgu->load_properties($arr);
		$rv = array($props, $this->cfgu->tableinfo, $this->cfgu->relinfo, $this->cfgu->classinfo, $this->cfgu->groupinfo);
		return $rv;
	}

	/** switches the object_loader's default database connection to $new_conn

		@attrib params=pos

		@param new_conn required

		new conn is the new database connection to set the datasource to
		returns the old connection
	**/
	public function switch_db_connection($new_conn)
	{
		// ok, we need to find the real connection.
		// iterate over the ds chain until we hit the last one.
		// that should be the final database ds
		$ds = $this->ds;
		while (is_object($ds->contained))
		{
			$ds = $ds->contained;
		}

		if (!is_object($ds))
		{
			error::raise(array(
				"id" => "ERR_NO_DS",
				"msg" => sprintf(t("object_loader::switch_db_connection(%s): could not find root connection!"), $new_conn)
			));
			return;
		}

		$old = $ds->dc[$ds->default_cid];
		$ds->dc[$ds->default_cid] = $new_conn;
		$this->dc[$ds->default_cid] = $new_conn;
		return $old;
	}

	function _log($new, $oid, $name, $clid = NULL)
	{
		if ($clid === NULL)
		{
			$tmpo = obj($oid);
			// get object's class info
			$clid = $tmpo->class_id();
		}

		if ($clid == 7)
		{
			$type = "ST_DOCUMENT";
		}
		elseif (!empty($GLOBALS["classinfo"][$clid]["syslog_type"]["text"]))
		{
			$type = $GLOBALS["classinfo"][$clid]["syslog_type"]["text"];
		}
		else
		{
			$type = 10000;
		}
	}

	/**
		@attrib api=1 params=pos
		@param type type=string
			Relation type name label
		@param class_id type=int
			AW class id to look for the relation in
		@comment
		@returns int
			Relation type integer id
		@errors
	**/
	public static function resolve_reltype($type, $class_id)
	{
		if (is_array($type))
		{
			$res = array();
			foreach($type as $ot)
			{
				if (!is_numeric($ot) && substr($ot, 0, 7) === "RELTYPE")
				{
					$res[] = self::_resolve_single_rt($ot, $class_id);
				}
				else
				{
					$res[] = $ot;
				}
			}
			return $res;
		}
		elseif (!is_numeric($type) && substr($type, 0, 7) === "RELTYPE")
		{
			return self::_resolve_single_rt($type, $class_id);
		}
		return $type;
	}

	private static function _resolve_single_rt($type, $class_id)
	{
		// it is "RELTYPE_FOO"
		// resolve it to numeric
		if (empty($GLOBALS["relinfo"][$class_id][$type]["value"]))
		{
			return -1; // won't match anything
		}
		return $GLOBALS["relinfo"][$class_id][$type]["value"];
	}

	function handle_cache_update($oid, $site_id, $type)
	{
		if (!$this->registered)
		{
			register_shutdown_function(array($this, "on_shutdown_update_cache"));
			$this->cache_handlers = array();
			$this->registered = true;
		}

		// register cache update to sites using same object system with different cache location
		try
		{
			$repl = aw_ini_get("object_cache.replicate_sites");
		}
		catch (Exception $e)
		{
			$repl = null;
		}

		if ($repl and is_array($repl) or $site_id != aw_ini_get("site_id"))
		{
			$this->cache_handlers[$site_id][$oid][$type] = $type;
		}
	}

	function on_shutdown_update_cache()
	{
		// go over all the registered cache updates and if they are for another site, then propagate them to that one
		$sl = new site_list();
		$f = fopen(aw_ini_get("site_basedir")."/files/updlog.txt", "a");

		try // shutdown functions can't throw exceptions
		{
			$repl = aw_ini_get("object_cache.replicate_sites");
			$cur_sid = aw_ini_get("site_id");
		}
		catch (Exception $e)
		{
			$repl = null;
			$cur_sid = null;
		}

		foreach($this->cache_handlers as $site_id => $data)
		{
			if (is_array($repl))
			{
				foreach($repl as $url)
				{
					$this->_do_repl_call($url, $data, $f, $site_id);
				}
				continue;
			}
			elseif ($site_id == $cur_sid)
			{
				continue;
			}
			else
			{
				$url = $sl->get_url_for_site($site_id);
			}
			$this->_do_repl_call($url, $data, $f, $site_id);
		}
		fclose($f);
	}

	function _do_repl_call($url, $data, $f, $site_id)
	{
		fwrite($f, "call {$site_id} => ".dbg::dump($data)." from site {$url}\n\n");
		fflush($f);
		if ($url != "")
		{
			aw_global_set("__from_raise_error", 1);
			$this->do_orb_method_call(array(
				"server" => $url,
				"method" => "xmlrpc",
				"class" => "object_cache_updater",
				"action" => "handle_remote_update",
				"params" => array(
					"data" => $data
				),
				"no_errors" => 1	// sites may not respond or be password protected or whatever and the user does not need to see that
			));
			aw_global_set("__from_raise_error", 0);
		}
	}

	function handle_no_cache_clear()
	{
		cache::file_clear_pt("html");
		cache::file_clear_pt("acl");
		cache::file_clear_pt("menu_area_cache");
		cache::file_clear_pt("storage_search");
		cache::file_clear_pt("storage_object_data");
	}

	public function set___aw_acl_cache($oid = NULL, $v = array())
	{
		if(isset($oid))
		{
			$this->__aw_acl_cache[$oid] = $v;
		}
		else
		{
			$this->__aw_acl_cache = $v;
		}
	}
}

