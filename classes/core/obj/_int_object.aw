<?php

/*

this message will get called whenever an object is deleted and the class_id as the message type parameter
and the object's id as the "oid" parameter
EMIT_MESSAGE(MSG_STORAGE_DELETE)

*/

class _int_object
{
	///////////////////////////////////////////
	// private variables

	var $obj = array(
		"properties" => array(),
		"class_id" => null,
		"meta" => null
	);			// actual object data. $this->objdata["__obj_load_parameter"] -- special element for storing constructor parameter
	protected $_create_new_version = 0;
	protected $implicit_save = false;
	protected $no_modify = false;
	protected $obj_sys_flags;
	protected $props_loaded = false;
	protected $props_modified = array();
	protected $ot_modified = array("modified" => 1);
	protected static $global_save_count = 0;
	protected static $cache_off = false;

	/**
		This defines, after how many object saves cache will automatically be turned off for the rest of the request.
		It's here so that the cache will not be cleared a million times pointlessly when you are creating a million objects in one request.
	**/
	const CACHE_OFF_ON_SAVE_COUNT = 3;

	///////////////////////////////////////////
	// public functions

	function __construct($objdata = array())
	{
		$this->obj = $objdata;

		if (isset($objdata["__obj_load_parameter"]))
		{
			$this->_int_load();
		}
	}

	// DEPRECATED! use self::__construct() instead
	// function _int_object($objdata) { $this->__construct($objdata); }

	function save($exclusive = false, $previous_state = null)
	{
		$this->_int_can_save();
		$tmp =  $this->_int_do_save($exclusive, $previous_state);
		return $tmp;
	}

	function save_new()
	{
		if (!is_oid($this->parent()))
		{
			throw new awex_obj_parent("Cannot duplicate object! Parent not set!");
		}

		if (!is_class_id($this->class_id()))
		{
			throw new awex_obj_class("Cannot duplicate object! Class ID not set!");
		}

		$o = new object();
		$o->set_class_id($this->class_id());
		$o->set_parent($this->parent());

		foreach($this->get_property_list() as $pn => $pd)
		{
			$o->set_prop($pn, $this->prop($pn));
		}

		$o->save();

		return $o;
	}

	function set_implicit_save($param)
	{
		$prev = $this->implicit_save;
		$this->implicit_save = $param;
		return $prev;
	}

	function get_implicit_save()
	{
		return ($this->implicit_save ? true : false);
	}

	function arr()
	{
		return $this->obj;
	}

	function delete($full_delete = false)
	{
		if (!$this->obj["oid"])
		{
			error::raise(array(
				"id" => "ERR_NO_OID",
				"msg" => t("object::delete(): no current object loaded")
			));
			return;
		}

		if (!$this->can("delete"))
		{
			$e = new awex_obj_acl("No delete access.");
			$e->awobj_id = $this->obj["oid"];
			throw $e;
		}

		$ret = $this->obj["oid"];

		$this->_int_do_delete($this->obj["oid"], $full_delete);

		return $ret;
	}

	function connect($param)
	{
		if (!is_array($param))
		{
			throw new awex_obj_type("Invalid parameter " . var_export($param, true) . " for connecting");
		}

		if (empty($param["reltype"]) && !empty($param["type"]))
		{
			$param["reltype"] = $param["type"];
		}

		$oids = $GLOBALS["object_loader"]->param_to_oid_list($param["to"]);
		foreach($oids as $oid)
		{
			$to = obj($oid);
			$oid = $to->brother_of();

			// check if a connection to the same object with the same reltype already exists
			// if it does, then don't do it again.
			$cprms = array("to" => $oid);
			if ($param["reltype"])
			{
				if (!is_numeric($param["reltype"]) && substr($param["reltype"], 0, 7) === "RELTYPE")
				{
					// it is "RELTYPE_FOO"
					// resolve it to numeric
					$param["reltype"] = $GLOBALS["relinfo"][$this->obj["class_id"]][$param["reltype"]]["value"];
				}
				$cprms["type"] = $param["reltype"];
			}

			// use a different code path for not saved objects
			if (!isset($this->obj["oid"]) or !is_oid($this->obj["oid"]))
			{
				// object is not saved, therefore we cannot create
				// the actual connection, so remember the data
				// and try to create it _after_ the object is saved
				$this->obj["_create_connections"][] = $param;
			}
			else
			{
				if (isset($this->obj["brother_of"]) && $GLOBALS["object_loader"]->ds->can("view", $this->obj["brother_of"]) &&
					$GLOBALS["object_loader"]->ds->can("view", $oid))
				{
					$c = new connection();
					$param["from"] = $this->obj["brother_of"];
					$param["to"] = $oid;
					$c->change($param);
				}
				else
				{
					error::raise(array(
						"id" => "ERR_ACL",
						"msg" => sprintf(t("object::connect(): no view access for both endpoints (%s and %s)!"), $this->obj["brother_of"], $oid)
					));
					return;
				}
			};
		}
	}

	function disconnect($param)
	{
		if (!is_array($param))
		{
			error::raise(array(
				"id" => "ERR_PARAM",
				"msg" => sprintf(t("object::disconnect(%s): parameter must be an array!"), $param)
			));
			return;
		}
		$oids = $GLOBALS["object_loader"]->param_to_oid_list($param["from"]);
		foreach($oids as $oid)
		{
			$c = new connection();
			$finder = array(
				"from" => $this->obj["brother_of"],
				"from.class_id" => $this->obj["class_id"],
				"to" => $oid,
			);
			if (!empty($param["type"]))
			{
				$finder["type"] = $GLOBALS["object_loader"]->resolve_reltype($param["type"], $this->obj["class_id"]);
			}
			$conn_id = $c->find($finder);
			if (count($conn_id) < 1)
			{
				if($param["errors"] === false)
				{
					return false;
				}
				else
				{
					error::raise(array(
						"id" => "ERR_CONNECTION",
						"msg" => sprintf(t("object::disconnect(): could not find connection to object %s from object %s"), $oid, $this->obj["oid"])
					));
					return;
				}
			}
			reset($conn_id);
			list(, $f_c) = each($conn_id);
			$c->load($f_c["id"]);
			$c->delete();
		}
	}

	function connections_from($param = NULL)
	{
		if (empty($this->obj["oid"]))
		{
			return array();
		}

		if (!isset($param["type"]) && isset($param["reltype"]))
		{
			$param["type"] = $param["reltype"];
		}

		$filter = array(
			"from" => $this->obj["brother_of"]
		);

		if ($param != NULL)
		{
			if (!is_array($param))
			{
				error::raise(array(
					"id" => "ERR_PARAM",
					"msg" => t("object::connections_from(): if argument is present, then argument must be array of filter parameters!")
				));
				return;
			}

			if (isset($param["type"]))
			{
				$param["type"] = $GLOBALS["object_loader"]->resolve_reltype($param["type"], $this->obj["class_id"]);
				if ($param["type"])
				{
					$filter["type"] = $param["type"];
				}
			}

			if (isset($param["to"]))
			{
				$filter["to"] = $GLOBALS["object_loader"]->param_to_oid_list($param["to"]);
			}

			if (isset($param["idx"]))
			{
				$filter["idx"] = $param["idx"];
			}

			foreach($param as $k => $v)
			{
				if (substr($k, 0, 3) == "to.")
				{
					$filter[$k] = $v;
				}
				if (substr($k, 0, 5) == "from.")
				{
					$filter[$k] = $v;
				}
			}

			if (isset($param["class"]))
			{
				$filter["to.class_id"] = $param["class"];
			}
		}

		if (empty($filter["from.class_id"]))
		{
			$filter["from.class_id"] = $this->obj["class_id"];
		}

		$ret = array();
		$cs = $GLOBALS["object_loader"]->ds->find_connections($filter);
		foreach($cs as $c_id => $c_d)
		{
			// set acldata to memcache
			$GLOBALS["__obj_sys_acl_memc"][$c_d["from"]] = array(
				"acldata" => $c_d["from.acldata"],
				"parent" => $c_d["from.parent"]
			);
			$GLOBALS["__obj_sys_acl_memc"][$c_d["to"]] = array(
				"acldata" => $c_d["to.acldata"],
				"parent" => $c_d["to.parent"]
			);
			if ($GLOBALS["object_loader"]->ds->can("view", $c_d["to"]))
			{
				$ret[$c_id] = new connection($c_d);
			}
		}
		if (!empty($param["sort_by"]))
		{
			uasort($ret, create_function('$a,$b', 'return strcasecmp($a->prop("'.$param["sort_by"].'"), $b->prop("'.$param["sort_by"].'"));'));
		}
		if (!empty($param["sort_by_num"]))
		{
			uasort($ret, create_function('$a,$b', 'return ($a->prop("'.$param["sort_by_num"].'") == $b->prop("'.$param["sort_by_num"].'") ? 0 : ($a->prop("'.$param["sort_by_num"].'") > $b->prop("'.$param["sort_by_num"].'") ? 1 : -1 ));'));
		}
		if(isset($param['sort_dir']) && $param['sort_dir'] == 'desc')
		{
			return array_reverse($ret);
		}
		else
		{
			return $ret;
		}
	}

	function connections_to($param = NULL)
	{
		if (empty($this->obj["oid"]))
		{
			return array();
		}

		if (!isset($param["type"]) && isset($param["reltype"]))
		{
			$param["type"] = $param["reltype"];
		}

		$filter = array(
			"to" => $this->obj["brother_of"]
		);

		if ($param != NULL)
		{
			if (!is_array($param))
			{
				error::raise(array(
					"id" => "ERR_PARAM",
					"msg" => t("object::connections_to(): if argument is present, then argument must be array of filter parameters!")
				));
				return;
			}
			if (isset($param["type"]))
			{
				if (!is_numeric($param["type"]) && !is_array($param["type"]) && substr($param["type"], 0, 7) === "RELTYPE" && isset($param["from.class_id"]) && is_class_id($param["from.class_id"]))
				{
					// it is "RELTYPE_FOO"
					// resolve it to numeric
					if (!isset($GLOBALS["relinfo"][$param["from.class_id"]]) || !is_array($GLOBALS["relinfo"][$param["from.class_id"]]))
					{
						// load class def
						self::_int_load_properties($param["from.class_id"]);
					}

					if (empty($GLOBALS["relinfo"][$param["from.class_id"]][$param["type"]]["value"]))
					{
						$param["type"] = -1; // won't match anything
					}
					else
					{
						$param["type"] = $GLOBALS["relinfo"][$param["from.class_id"]][$param["type"]]["value"];
					}
				}

				$filter["type"] = $param["type"];
			}

			if (isset($param["from"]))
			{
				$filter["from"] = $GLOBALS["object_loader"]->param_to_oid_list($param["from"]);
			}

			if (isset($param["idx"]))
			{
				$filter["idx"] = $param["idx"];
			}
			foreach($param as $k => $v)
			{
				if (substr($k, 0, 3) == "to.")
				{
					$filter[$k] = $v;
				}
				if (substr($k, 0, 5) == "from.")
				{
					$filter[$k] = $v;
				}
			}

			if (isset($param["class"]))
			{
				$filter["from.class_id"] = $param["class"];
			}
		}

		$ret = array();
		$cs = $GLOBALS["object_loader"]->ds->find_connections($filter);
		foreach($cs as $c_d)
		{
			// set acldata to memcache
			$GLOBALS["__obj_sys_acl_memc"][$c_d["from"]] = array(
				"acldata" => $c_d["from.acldata"],
				"parent" => $c_d["from.parent"]
			);
			$GLOBALS["__obj_sys_acl_memc"][$c_d["to"]] = array(
				"acldata" => $c_d["to.acldata"],
				"parent" => $c_d["to.parent"]
			);
			if ($GLOBALS["object_loader"]->ds->can("view", $c_d["from"]))
			{
				$ret[] = new connection($c_d);
			}
		}

		if (!empty($param["sort_by"]))
		{
			usort($ret, create_function('$a,$b', 'return strcasecmp($a->prop("'.$param["sort_by"].'"), $b->prop("'.$param["sort_by"].'"));'));
		}
		if (!empty($param["sort_by_num"]))
		{
			uasort($ret, create_function('$a,$b', 'return ($a->prop("'.$param["sort_by_num"].'") == $b->prop("'.$param["sort_by_num"].'") ? 0 : ($a->prop("'.$param["sort_by_num"].'") > $b->prop("'.$param["sort_by_num"].'") ? 1 : -1 ));'));
		}

		if(isset($param['sort_dir']) && $param['sort_dir'] == 'desc')
		{
			return array_reverse($ret);
		}
		else
		{
			return $ret;
		}
	}

	function path($param = NULL)
	{
		if (!is_object($this))
		{
			$o = new object($param);
			return $o->path();
		}

		if (!$this->obj["oid"])
		{
			error::raise(array(
				"id" => "ERR_NO_OBJ",
				"msg" => t("object::path(): no object loaded!")
			));
			return;
		}

		if ($param !== NULL && !is_array($param))
		{
			error::raise(array(
				"id" => "ERR_PARAM",
				"msg" => sprintf(t("object::path(%s): if parameter is specified, it must be an array!"), $param)
			));
			return;
		}

		if (is_array($param) && isset($param["to"]))
		{
			$param["to"] = $GLOBALS["object_loader"]->param_to_oid($param["to"]);
		}

		return $this->_int_path($param);
	}

	function path_str($param = NULL)
	{
		if ($param != NULL)
		{
			if (!is_array($param))
			{
				error::raise(array(
					"id" => "ERR_PARAM",
					"msg" => t("object::path_str(): parameter must be an array if present!")
				));
				return;
			}
		}

		$pt = $this->path($param);
		$i = 0;
		$cnt = count($pt);
       	if (isset($param["max_len"]))
		{
			$i = $cnt - $param["max_len"];
			if (!empty($param["path_only"]))
			{
				$i--;
			}
		}

		$skip = isset($param["start_at"]) && is_oid($param["start_at"]) ? true : false;
		$ret = array();
		for(; $i < $cnt; $i++)
		{
			if (isset($pt[$i]) && is_object($pt[$i]))
			{
				if (isset($param["start_at"]) && is_oid($param["start_at"]) && $pt[$i]->id() == $param["start_at"])
				{
					$skip = false;
				}
				if ($skip)
				{
					continue;
				}

				if (!empty($param["path_only"]) && $pt[$i]->id() == $this->obj["oid"])
				{
					continue;
				}
				$ret[] = $pt[$i]->name();
			}
		}
		$tmp = join(" / ", $ret);
		if ($tmp == "" && empty($param["path_only"]))
		{
			$tmp = $this->name();
		}

		return $tmp;
	}

	function can($param)
	{
		if (empty($this->obj["oid"]))
		{
			error::raise(array(
				"id" => "ERR_ACL",
				"msg" => sprintf(t("object::can(%s): no current object loaded!"),$param)
			));
			return;
		}

		return $this->_int_can($param);
	}

	function init_acl()
	{
		$GLOBALS["object_loader"]->set___aw_acl_cache();
	}

	function is_property($param)
	{
		if (!is_string($param) or empty($param))
		{
			return false;
		}

		if (!is_class_id($this->obj["class_id"]))
		{
			error::raise(array(
				"err" => "ERR_NO_CLASS_ID",
				"msg" => sprintf(t("object::is_property(%s): no class_id for the current object is set!"), $param)
			));
			return;
		}

		return $this->_int_is_property($param);
	}

	function parent()
	{
		return isset($this->obj["parent"]) ? $this->obj["parent"] : null;
	}

	function set_parent($parent)
	{
		$prev = isset($this->obj["parent"]) ? $this->obj["parent"] : null;
		$parent = $GLOBALS["object_loader"]->param_to_oid($parent);

		if (!$parent)
		{
			error::raise(array(
				"id" => "ERR_NO_PARENT",
				"msg" => sprintf(t("object::set_parent(%s): no parent specified!"), $parent)
			));
			return;
		}


		$this->_int_set_of_value("parent", $parent);
		$this->_int_do_implicit_save();

		// also, check parent object and set site_id according to these rules:
		// - if parent is client type menu, then do nothing
		// - else set site_id same as parent's
		if (is_oid($parent) && $GLOBALS["object_loader"]->ds->can("view", $parent))
		{
			if (isset($GLOBALS["objects"][$parent]) && is_object($GLOBALS["objects"][$parent]))
			{
				$objdata = $GLOBALS["objects"][$parent]->obj;
			}
			else
			{
				$objdata = $GLOBALS["object_loader"]->ds->get_objdata($parent, array("no_errors" => true));
			}

			if ($objdata !== NULL)
			{
				$o = obj($parent);
				if (!($o->class_id() == menu_obj::CLID && $o->prop("type") == MN_CLIENT) && is_numeric($o->site_id()))
				{
					$this->set_site_id($o->site_id());
				}
			}
		}

		return $prev;
	}

	function name()
	{
		return isset($this->obj["name"]) ? $this->obj["name"] : null;
	}

	function set_name($param)
	{
		$prev = isset($this->obj["name"]) ? $this->obj["name"] : null;
		$this->_int_set_of_value("name", $param);
		$this->_int_do_implicit_save();
		return $prev;
	}

	function class_id()
	{
		return (int) (isset($this->obj["class_id"]) ? $this->obj["class_id"] : null);
	}

	function set_class_id($param)
	{
		if (!is_class_id($param))
		{
			error::raise(array(
				"id" => "ERR_CLASS_ID",
				"msg" => sprintf(t("object::set_class(%s): specified class id id is not valid!"), $param)
			));
			return;
		}

		$this->_int_set_of_value("class_id", $param);

		// since the class id has changed, we gots to load new properties for the new class type
		$this->_int_load_properties();
		$this->_int_do_implicit_save();
	}

	public function class_title()
	{
		try
		{
			$class_title = aw_ini_get(sprintf("classes.%u.name", $this->class_id()));
		}
		catch(awex_cfg_key $e)
		{
			$class_title = NULL;
		}
		return $class_title;
	}

	function status()
	{
		return isset($this->obj["status"]) ? $this->obj["status"] : null;
	}

	function set_status($param)
	{
		$prev = isset($this->obj["status"]) ? $this->obj["status"] : null;
		settype($param, "int");

		if (object::STAT_ACTIVE === $param)
		{
			$this->_int_set_of_value("status", object::STAT_ACTIVE);
		}
		elseif (object::STAT_NOTACTIVE === $param)
		{
			$this->_int_set_of_value("status", object::STAT_NOTACTIVE);
		}
		elseif (object::STAT_DELETED === $param)
		{
			$this->_int_set_of_value("status", object::STAT_DELETED);
			return $this->delete();
		}
		else
		{
			throw new awex_obj_type("Invalid status parameter '{$param}' for object '{$this->obj["oid"]}'");
		}

		$this->_int_do_implicit_save();
		return $prev;
	}

	function lang()
	{
		if (!isset($this->obj["lang_id"]))
		{
			return NULL;
		}

		// right. once we convert all code to use lang codes (en/et/..) we can make this better. right now, the sucky version.
		$li = new languages();
		return $li->get_langid($this->obj["lang_id"]);
	}

	function lang_id()
	{
		return isset($this->obj["lang_id"]) ? $this->obj["lang_id"] : null;
	}

	function set_lang_id($param)
	{
		$prev = isset($this->obj["lang_id"]) ? $this->obj["lang_id"] : null;

		if (!is_numeric($param))
		{
			error::raise(array(
				"id" => "ERR_LANG_ID",
				"msg" => sprintf(t("object::set_lang_id(%s): lang_id must be integer!"), $param)
			));
			return;

		}
		$this->_int_set_of_value("lang_id", (int)$param);
		$this->_int_do_implicit_save();
		return $prev;

	}

	function set_lang($param)
	{
		$prev = $this->lang();

		$li = new languages();
		$lang_id = $li->get_langid_for_code($param);
		if (!$lang_id)
		{
			$lang_id = aw_global_get("lang_id");
		}

		$this->_int_set_of_value("lang_id", $lang_id);
		$this->_int_do_implicit_save();
		return $prev;
	}

	function comment()
	{
		return isset($this->obj["comment"]) ? $this->obj["comment"] : null;
	}

	function set_comment($param)
	{
		$prev = isset($this->obj["comment"]) ? $this->obj["comment"] : null;
		$this->_int_set_of_value("comment", $param);
		$this->_int_do_implicit_save();
		return $prev;
	}

	function ord()
	{
		return isset($this->obj["jrk"]) ? $this->obj["jrk"] : null;
	}

	function set_ord($param)
	{
		$prev = isset($this->obj["jrk"]) ? $this->obj["jrk"] : null;

		if (!is_numeric($param) && $param != "")
		{
			error::raise(array(
				"id" => "ERR_ORD",
				"msg" => sprintf(t("object::set_ord(%s): order must be integer!"), $param)
			));
			return;
		}

		$this->_int_set_of_value("jrk", (int)$param);
		$this->_int_do_implicit_save();
		return $prev;
	}

	function alias()
	{
		return isset($this->obj["alias"]) ? $this->obj["alias"] : null;
	}

	function set_alias($param)
	{
		$prev = isset($this->obj["alias"]) ? $this->obj["alias"] : null;
		$this->_int_set_of_value("alias", $param);
		$this->_int_do_implicit_save();
		return $prev;
	}

	// id is null until object is saved
	function id()
	{
		return isset($this->obj["oid"]) ? $this->obj["oid"] : null;
	}

	function createdby()
	{
		return isset($this->obj["createdby"]) ? $this->obj["createdby"] : null;
	}

	function created()
	{
		return isset($this->obj["created"]) ? $this->obj["created"] : null;
	}

	function modifiedby()
	{
		return isset($this->obj["modifiedby"]) ? $this->obj["modifiedby"] : null;
	}

	function modified()
	{
		return isset($this->obj["modified"]) ? $this->obj["modified"] : null;
	}

	function period()
	{
		return isset($this->obj["period"]) ? $this->obj["period"] : null;
	}

	function set_period($param)
	{
		$prev = isset($this->obj["period"]) ? $this->obj["period"] : null;

		if (!is_numeric($param) && $param != "")
		{
			error::raise(array(
				"id" => "ERR_PERIOD",
				"msg" => sprintf(t("object::set_period(%s): period must be integer!"), $param)
			));
			return;
		}

		$this->_int_set_of_value("period", (int)$param);
		$this->_int_do_implicit_save();
		return $prev;
	}

	function is_periodic()
	{
		return isset($this->obj["periodic"]) ? $this->obj["periodic"] : null;
	}

	function set_periodic($param)
	{
		$prev = isset($this->obj["periodic"]) ? $this->obj["periodic"] : null;

		if (!(is_numeric($param) || is_bool($param)) && $param != "")
		{
			error::raise(array(
				"id" => "ERR_BOOL",
				"msg" => sprintf(t("object::set_periodic(%s): order must be integer or boolean!"), $param)
			));
			return;
		}

		$this->_int_set_of_value("periodic", ($param ? true : false));
		$this->_int_do_implicit_save();
		return $prev;
	}

	function site_id()
	{
		return isset($this->obj["site_id"]) ? $this->obj["site_id"] : null;
	}

	function set_site_id($param)
	{
		$prev = isset($this->obj["site_id"]) ? $this->obj["site_id"] : null;

		if (!is_numeric($param))
		{
			error::raise(array(
				"id" => "ERR_SITE_ID",
				"msg" => sprintf(t("object::set_site_id(%s): site_id must be integer!"), $param)
			));
			return;
		}

		$this->_int_set_of_value("site_id", (int)$param);
		$this->_int_do_implicit_save();
		return $prev;
	}

	function is_brother()
	{
		if (!isset($this->obj["oid"]))
		{
			return NULL;
		}

		return ($this->obj["oid"] != $this->obj["brother_of"]);
	}

	function has_brother($parent)
	{
		if (!isset($this->obj["oid"]))
		{
			return NULL;
		}
		$args = array(
			"class_id" => $this->obj["class_id"],
			"brother_of" => $this->obj["brother_of"],
			"site_id" => array(),
			"lang_id" => array(),
		);
		if(is_array($parent) || is_oid($parent))
		{
			$args["parent"] = $parent;
		}
		$ol = new object_list($args);
		return reset($ol->ids());
	}

	function brothers()
	{
		if (!isset($this->obj["oid"]))
		{
			return NULL;
		}
		$args = array(
			"class_id" => $this->obj["class_id"],
			"brother_of" => $this->obj["oid"],
			"oid" => new obj_predicate_not($this->obj["oid"]),
			"site_id" => array(),
			"lang_id" => array(),
		);
		$ol = new object_list($args);
		return $ol->ids();
	}

	function get_original()
	{
		$ib = $this->is_brother();
		$cv = $GLOBALS["object_loader"]->ds->can("view", $this->obj["brother_of"]);
		if ($ib && $cv)
		{
			aw_global_set("__from_raise_error", 1);
			$rv =  new object($this->obj["brother_of"]);
			aw_global_set("__from_raise_error", 0);
			if (isset($GLOBALS["aw_is_error"]) && $GLOBALS["aw_is_error"] == 1)
			{
				return $this;
			}
			return $rv;
		}
		return $this;
	}

	function subclass()
	{
		return isset($this->obj["subclass"]) ? $this->obj["subclass"] : null;
	}

	function set_subclass($param)
	{
		$prev = isset($this->obj["subclass"]) ? $this->obj["subclass"] : null;

		if ($param  == "")
		{
			$param = 0;
		}

		if (!is_numeric($param))
		{
			error::raise(array(
				"id" => "ERR_SUBCLASS",
				"msg" => sprintf(t("object::set_subclass(%s): subclass must be integer!"), $param)
			));
			return;
		}

		$this->_int_set_of_value("subclass", (int)$param);
		$this->_int_do_implicit_save();
		return $prev;
	}

	function flags()
	{
		return isset($this->obj["flags"]) ? $this->obj["flags"] : null;
	}

	function set_flags($param)
	{
		$prev = isset($this->obj["flags"]) ? $this->obj["flags"] : null;

		if ($param  == "")
		{
			$param = 0;
		}

		if (!is_numeric($param))
		{
			error::raise(array(
				"id" => "ERR_FLAGS",
				"msg" => sprintf(t("object::set_flags(%s): flags must be integer!"), $param)
			));
			return;
		}

		$this->_int_set_of_value("flags", (int)$param);
		$this->_int_do_implicit_save();
		return $prev;
	}

	function flag($param)
	{
		if (!is_numeric($param))
		{
			error::raise(array(
				"id" => "ERR_FLAG",
				"msg" => sprintf(t("object::flag(%s): flag must be integer!"), $param)
			));
			return;
		}

		if (isset($this->obj["flags"]))
		{
			return $this->obj["flags"] & $param;
		}
		else
		{
			return 0;
		}
	}

	function set_flag($flag, $val)
	{
		if (!is_numeric($flag))
		{
			error::raise(array(
				"id" => "ERR_FLAG",
				"msg" => sprintf(t("object::set_flag(%s, %s): flag must be integer!"), $flag, $val)
			));
			return;
		}
		if (!(is_numeric($val) || is_bool($val)))
		{
			error::raise(array(
				"id" => "ERR_FLAG",
				"msg" => sprintf(t("object::set_flag(%s, %s): value must be integer!"), $flag, $val)
			));
			return;
		}

		$prev = $this->flag($flag);
		$flags = isset($this->obj["flags"]) ? $this->obj["flags"] : 0;

		if ($val)
		{
			// if set flag, then or the current bits with the value
			$value = $flags | $flag;
		}
		else
		{
			$mask = OBJ_FLAGS_ALL ^ $flag;
			$value = $flags & $mask;
		}

		$this->_int_set_of_value("flags", $value);
		$this->_int_do_implicit_save();
		return $prev;
	}

	function meta($param = false)
	{
		// calling this without an argument returns the contents of whole metainfo
		// site_content->build_menu_chain for example needs access to the whole metainfo at once -- duke
		if ($param === false)
		{
			return $this->obj["meta"];
		}
		else
		{
			return isset($this->obj["meta"][$param]) ? $this->obj["meta"][$param] : null;
		}
	}

	function set_meta($key, $value)
	{
		$prev = isset($this->obj["meta"][$key]) ? $this->obj["meta"][$key] : null;
		$this->_int_set_ot_mod("metadata", $prev, $value);
		$this->obj["meta"][$key] = $value;

		$dat = isset($GLOBALS["properties"][$this->obj["class_id"]][$key]) ? $GLOBALS["properties"][$this->obj["class_id"]][$key] : null;

		// if any property is defined for metadata, we gots to sync from object to property
		if (is_array($dat) && $dat["field"] === "meta" && $dat["table"] === "objects")
		{
			$this->_int_set_prop($key, $value);
		}

		$this->_int_do_implicit_save();
		return $prev;
	}

	function get_property_list()
	{
		return $GLOBALS["properties"][$this->obj["class_id"]];
	}

	function get_group_list()
	{
		$clid = isset($this->obj["class_id"]) ? $this->obj["class_id"] : null; //!!! return default kui clid null
		$classes = aw_ini_get("classes");
		$inf = $GLOBALS["object_loader"]->load_properties(array(
			"file" => ($clid == doc_obj::CLID ? "doc" : basename($classes[$clid]["file"])),
			"clid" => $clid

		));
		return $inf[4];
	}

	function get_relinfo()
	{
		return $GLOBALS["relinfo"][$this->obj["class_id"]];
	}

	function get_tableinfo()
	{
		return $GLOBALS["tableinfo"][$this->obj["class_id"]];
	}

	function get_classinfo()
	{
		return $GLOBALS["classinfo"][$this->obj["class_id"]];
	}

	function draft($param)
	{
		$retval = $this->_int_get_draft($param);
		return $retval;
	}

	function set_draft($param, $value)
	{
		$retval = $this->_int_set_draft($param, $value);
		return $retval;
	}

	function prop($param)
	{
		$retval = $this->_int_get_prop($param);
		return $retval;
	}

	function prop_str($param, $is_oid = NULL)
	{
		if (strpos($param, ".") !== false)
		{
			$o = $this;
			$bits = explode(".", $param);
			foreach($bits as $idx => $part)
			{
				$is_rel = false;
				if (substr($part, 0, strlen("RELTYPE")) === "RELTYPE")
				{
					$is_rel = true;
					$prop_dat = array();
					$tmp = $o->get_first_obj_by_reltype($part);
					if ($tmp)
					{
						$cur_v = $tmp->id();
					}
					else
					{
						return null;
					}
				}
				else
				{
					$cur_v = $o->prop($part);
					$prop_dat = $GLOBALS["properties"][$o->class_id()][$part];
				}
				// the true here is because if the user says that this thingie is an oid, then we trust him
				// we check of course, but still. we trust him.
				if (is_array($cur_v) && count($cur_v) == 1)
				{
					$cur_v = reset($cur_v);
				}
				$acl_tmp = $GLOBALS["cfg"]["acl"]["no_check"];
				$GLOBALS["cfg"]["acl"]["no_check"] = 0;
				if (!$GLOBALS["object_loader"]->ds->can("view", $cur_v))
				{
					$GLOBALS["cfg"]["acl"]["no_check"] = $acl_tmp;
					if ($idx == (count($bits)-1))
					{
						return $o->prop_str($part);
					}
					return null;
				}
				$GLOBALS["cfg"]["acl"]["no_check"] = $acl_tmp;
				if ($idx == (count($bits)-1))
				{
					if ($is_rel)
					{
						return $o->prop_str("name");
					}
					else
					{
						return $o->prop_str($part);
					}
				}
				$o = obj($cur_v);
			}
		}
		$pd = isset($GLOBALS["properties"][$this->obj["class_id"]][$param]) ? $GLOBALS["properties"][$this->obj["class_id"]][$param] : null;
		if (!$pd)
		{
			return $this->prop($param);
		}

		$type = $pd["type"];
		if ($is_oid)
		{
			$type = "oid";
		}

		$val = $this->prop($param);
		switch($type)
		{
			// YOU *CAN NOT* convert dates to strings here - it fucks up dates in vcl tables
			case "relmanager":
			case "relpicker":
			case "classificator":
			case "popup_search":
			case "crm_participant_search":
			case "releditor":
				if ($pd["store"] === "connect")
				{
					// we need to list the connections and fetch their names.
					// UNLESS we already got them from wherever. like fetch_full_list conn prop fetch
					if (isset($GLOBALS["read_properties_data_cache_conn"][$this->obj["oid"]]) and is_array($GLOBALS["read_properties_data_cache_conn"][$this->obj["oid"]]))
					{
						$rt = $GLOBALS["relinfo"][$this->obj["class_id"]][$pd["reltype"]]["value"];
						$_tmp = array();
						if (isset($GLOBALS["read_properties_data_cache_conn"][$this->obj["oid"]][$rt]) and is_array($GLOBALS["read_properties_data_cache_conn"][$this->obj["oid"]][$rt]))
						{
							foreach($GLOBALS["read_properties_data_cache_conn"][$this->obj["oid"]][$rt] as $con)
							{
								$_tmp[] = $con["target_name"];
							}
						}
					}
					elseif (is_oid($this->id()))
					{
						$rels = new object_list($this->connections_from(array(
							"type" => $pd["reltype"]
						)));
						$_tmp = $rels->names();
					}

					if (count($_tmp))
					{
						foreach ($_tmp as $key => $value)
						{
							$_tmp[$key] = strlen($value) ? $value : t("[Nimetu]");
						}
						$val = join(", ", $_tmp);
					}
					else
					{
						$val = "";
					}
					break;
				}

			case "objpicker":
			case "oid":
				if (is_oid($val))
				{
					if (object_loader::can("view", $val))
					{
						$tmp = new object($val);
						$val = $tmp->name();
					}
					else
					{
						$val = t("[Puudub ligip&auml;&auml;s]");
					}
				}
				elseif (is_array($val))
				{
					$vals = array();
					foreach($val as $k)
					{
						if (is_oid($k))
						{
							if (object_loader::can("view", $k))
							{
								$tmp = new object($k);
								$tmp = $tmp->name();
								$vals[] = strlen($tmp) ? $tmp : t("[Nimetu]");
							}
							else
							{
								$vals[] = t("[Puudub ligip&auml;&auml;s]");
							}
						}
					}
					$val = join(", ", $vals);
				}
				break;

			case "checkbox":
				$val = $val == $pd["ch_value"] ? t("Jah") : t("Ei");
				break;
		}

		if (empty($val))
		{
			$val = "";
		}

		return $val;
	}

	function set_prop($key, $val)
	{
		if (!$this->_int_is_property($key))
		{
			throw new awex_obj_prop("Property {$key} not defined for current object (id: " . $this->obj["oid"] . ", clid: " . $this->obj["class_id"] . ")");
		}

		$prev = $this->_int_get_prop($key);
		$this->_int_set_prop($key, $val);
		// if this is a relpicker property, create the relation as well
		$propi = $GLOBALS["properties"][$this->obj["class_id"]][$key];
		if (($propi["type"] === "relpicker" ) ||
			($propi["type"] === "releditor" && ($propi["store"] === "connect" || $propi["choose_default"] == 1)) ||
			 $propi["type"] === "relmanager" ||
			($propi["type"] === "classificator" && $propi["store"] === "connect") ||
			($propi["type"] === "popup_search" && $propi["reltype"] != "") ||
			($propi["type"] === "chooser" && $propi["store"] === "connect" || !empty($propi["reltype"]))
		)
		{
			$_rt = $GLOBALS["relinfo"][$this->obj["class_id"]][$propi["reltype"]]["value"];
			if (ifset($propi, "multiple") == 1 || is_array($val))
			{
				$tval = $val;
				if (!is_array($tval))
				{
					$tval = array($tval => $tval);
				}
				// get all old connections
				// remove the ones that are not selected
				if (is_oid($this->id()) && ($propi["type"] !== "relpicker" || $propi["store"] === "connect"))
				{
					foreach($this->connections_from(array("type" => $_rt)) as $c)
					{
						if (!in_array($c->prop("to"), $tval))
						{
							$this->disconnect(array("from" => $c->prop("to"), "type" => $_rt));
						}
					}
				}
				// connect to all selected ones
				foreach(safe_array($tval) as $_idx => $connect_to)
				{
					if (is_oid($connect_to) && $GLOBALS["object_loader"]->ds->can("view", $connect_to))
					{
						$this->connect(array(
							"to" => $connect_to,
							"reltype" => $_rt
						));
					}
					else
					{
						unset($tval[$_idx]);
					}
				}
			}
			else
			{
				// if this has store=connect, then we need to remove other conns
				if (is_oid($this->id()) && $propi["store"] === "connect")
				{
					foreach($this->connections_from(array("type" => $_rt)) as $c)
					{
						if ($c->prop("to") != $val)
						{
							$this->disconnect(array("from" => $c->prop("to"), "type" => $_rt));
						}
					}
				}
				if (is_oid($val) && $GLOBALS["object_loader"]->ds->can("view", $val))
				{
					$this->connect(array(
						"to" => $val,
						"reltype" => $_rt
					));
				}
			}
		}

		// if this is an object field property, sync to object field
		if ($propi["table"] === "objects")
		{
			if ($propi["field"] === "meta")
			{
				$this->_int_set_ot_mod("metadata", ifset($this->obj, "meta", $propi["name"]), $val);
				$this->obj["meta"][$propi["name"]] = $val;
			}
			elseif ($propi["method"] === "bitmask" and isset($this->obj["flags"]))
			{
				// it's flags, sync to that
				$mask = $this->obj["flags"];
				// zero out cur field bits
				$mask = $mask & (~((int)$propi["ch_value"]));
				$mask = $mask | $val;
				$this->_int_set_ot_mod("flags", $this->obj["flags"], $mask);
				$this->obj["flags"] = $mask;
			}
			else
			{
				if ($propi["method"] === "serialize")
				{
					$this->_int_set_ot_mod($propi["field"], $this->obj[$propi["field"]][$propi["name"]], $this->obj["properties"][$key]);
					$this->obj[$propi["field"]][$propi["name"]] = $this->obj["properties"][$key];
				}
				else
				{
					$this->_int_set_ot_mod($propi["field"], ifset($this->obj, $propi["field"]), ifset($this->obj, "properties", $key));
					$this->obj[$propi["field"]] = $this->obj["properties"][$key];
				}
			}
		}
		$this->_int_do_implicit_save();
		return $prev;
	}

	function properties()
	{
		// make sure props are loaded
		$this->_int_get_prop(NULL);

		$ret = $this->obj["properties"];
		$ret["createdby"] = $this->createdby();
		$ret["modifiedby"] = $this->modifiedby();
		$ret["created"] = $this->created();
		$ret["modified"] = $this->modified();
		return $ret;
	}

	function fetch()
	{
		// returns something which resembles the return value of get_object
		// this approach might suck, but it's a awfully big task to convert
		// _everything_ and I'm running out of time
		$this->_int_get_prop(NULL);
		$retval = array();
		if (is_array($this->obj["properties"]))
		{
			foreach($this->obj["properties"] as $k => $v)
			{
				$retval[$k] = $this->trans_get_val($k);
			}
		}

		if (is_array($this->obj))
		{
			foreach($this->obj as $k => $v)
			{
				if ($k === "comment" || $k === "name")
				{
					$retval[$k] = $this->trans_get_val($k);
				}
				else
				if (!isset($retval[$k]))
				{
					$retval[$k] = $v;
				}
			}
		}
		return $retval;
	}

	function last()
	{
		// god damn, no setter for this or we'll never get rid of it!
		return isset($this->obj["last"]) ? $this->obj["last"] : null;
	}

	function brother_of()
	{
		return isset($this->obj["brother_of"]) ? $this->obj["brother_of"] : null;
	}

	function instance()
	{
		$clid = $this->class_id();
		if (!$clid)
		{
			error::raise(array(
				"id" => "ERR_OBJ_INSTANCE",
				"msg" => t("object::instance(): no object loaded or class id not set!")
			));
			return;
		}

		$cl = basename(aw_ini_get("classes." . $clid . ".file"));
		return new $cl();
	}

	function create_brother($parent)
	{
		if (empty($this->obj["oid"]))
		{
			$this->obj["_create_brothers"][] = $parent;
			return;
		}

		if (!is_oid($parent))
		{
			error::raise(array(
				"id" => "ERR_CORE_OID",
				"msg" => sprintf(t("object::create_brother(%s): no parent!"), $parent)
			));
			return;
		}

		// make sure brothers are only created for original objects, no n-level brothers!
		if ($this->obj["brother_of"] != $this->obj["oid"])
		{
			$o = obj($this->obj["brother_of"]);
			return $o->create_brother($parent);
		}

		// check if a brother already exists for this object under
		// the $parent menu.
		$ol = new object_list(array(
			"parent" => $parent,
			"brother_of" => $this->obj["oid"],
			"site_id" => array(),
		));
		if ($ol->count() > 0)
		{
			$tmp = $ol->begin();
			return $tmp->id();
		}

		return $this->_int_create_brother($parent);
	}

	function is_connected_to($param)
	{
		if (count($this->connections_from($param)) > 0)
		{
			return true;
		}
		return false;
	}

	function set_create_new_version()
	{
		$this->_create_new_version = 1;
	}

	function load_version($v)
	{
		$oid = $this->obj["oid"];
		$GLOBALS["object2version"][$oid] = $v;

		// check access rights to object
		if (!$GLOBALS["object_loader"]->ds->can("view", $oid))
		{
			$e = new awex_obj_acl("No view access object with id '{$oid}'.");
			$e->awobj_id = $oid;
			throw $e;
		}

		// load object data
		if (isset($GLOBALS["__obj_sys_objd_memc"][$oid]))
		{
			$objdata = $GLOBALS["__obj_sys_objd_memc"][$oid];
			unset($GLOBALS["__obj_sys_objd_memc"][$oid]);
		}
		else
		{
			$objdata = $GLOBALS["object_loader"]->ds->get_objdata($oid);
		}

		$objdata["__obj_load_parameter"] = $oid;
		$this->obj = $objdata;
		$this->_int_load();
	}

	function set_save_version($v)
	{
		$GLOBALS["object2version"][$this->obj["oid"]] = $v;
	}

	function set_no_modify($arg)
	{
		$this->no_modify = $arg;
	}

	function originalize()
	{
		if (!$this->is_brother())
		{
			return;
		}
		$GLOBALS["object_loader"]->ds->originalize($this->obj["oid"]);
		$GLOBALS["object_loader"]->handle_cache_update($this->id(), $this->site_id(), "originalize");
	}

	function trans_get_val($prop, $lang_id = false, $ignore_status = false)
	{
		// I wanna use object::trans_get_val("foo.name");
		if(strpos($prop, "."))
		{
			$i = strrpos($prop, ".");
			$foo = substr($prop, 0, $i);
			$foo_prop = substr($prop, $i + 1);
			if(is_oid($this->prop($foo)) && $this->can("view", $this->prop($foo)))
			{
				$foo_obj = obj($this->prop($foo));
				return $foo_obj->trans_get_val($foo_prop, $lang_id);
			}
		}

		if (isset($this->obj["oid"]) and $this->obj["oid"] != $this->obj["brother_of"])
		{
			$tmp = $this->get_original();
			if ($tmp->id() != $this->obj["oid"]) // if no view access for original, bro can return the same object
			{
				return $tmp->trans_get_val($prop, $lang_id);
			}
		}

		switch($prop)
		{
			case "name":
				if ($this->class_id() == language_obj::CLID)
				{
					$val = $this->prop("lang_name");
					$prop = "lang_name";
				}
				else
				{
					$val = $this->$prop();
				}
				break;

			case "alias":
				$val = $this->$prop();
				break;

			default:
				$val = $this->prop($prop);
		}

		$trans = false;
		$cur_lid = false;
		if (!empty($GLOBALS["cfg"]["user_interface"]["content_trans"]) && ($cur_lid = aw_global_get("lang_id")) != $this->lang_id())
		{
			$trans = true;
		}

		if ((!empty($GLOBALS["cfg"]["user_interface"]["full_content_trans"]) || !empty($GLOBALS["cfg"]["user_interface"]["trans_classes"][$this->class_id()])) &&
			($cl = aw_global_get($GLOBALS["cfg"]["user_interface"]["full_content_trans"] ? "ct_lang_id" : "lang_id")) != $this->lang_id())
		{
			$trans = true;
			$cur_lid = $cl;
		}

		// If the language id is given, ignore the stuff above.
		if($lang_id !== false && $lang_id != $this->lang_id())
		{
			$trans = true;
			$cur_lid = $lang_id;
		}

		if ($trans and isset($this->obj["meta"]["translations"]))
		{
			$trs = $this->obj["meta"]["translations"];
			if ($prop == "status") // check transl status
			{
				return $this->obj["meta"]["trans_".$cur_lid."_status"] == 1 ? object::STAT_ACTIVE : object::STAT_NOTACTIVE;
			}
			if (isset($trs[$cur_lid]) && ($this->obj["meta"]["trans_".$cur_lid."_status"] == 1 || $ignore_status))
			{
				if ($trs[$cur_lid][$prop] == "")
				{
					return $val;
				}
				$val = $trs[$cur_lid][$prop];
			}
		}
		// No spaces in the end of alias! -kaarel 26.02.2009
		if($prop === "alias")
		{
			$val = trim($val);
		}
		return $val;
	}

	function prop_is_translated($prop)
	{
		$trans = false;
		$cur_lid = false;
		if ($GLOBALS["cfg"]["user_interface"]["content_trans"] == 1 && ($cur_lid = aw_global_get("lang_id")) != $this->lang_id())
		{
			$trans = true;
		}

		if ($GLOBALS["cfg"]["user_interface"]["full_content_trans"] == 1 && ($cl = aw_global_get("ct_lang_id")) != $this->lang_id())
		{
			$trans = true;
			$cur_lid = $cl;
		}

		if ($trans)
		{
			// $trs = $this->obj["meta"]["translations"];
			// if (/*!empty($trs[$cur_lid]) &&*/ @$this->obj["meta"]["trans_".$cur_lid."_status"] == 1)
			if (isset($this->obj["meta"]["trans_".$cur_lid."_status"]) and $this->obj["meta"]["trans_".$cur_lid."_status"] == 1)
			{
				return true;
			}
			return false;
		}
		return true;
	}

	function trans_get_val_str($param)
	{
		if (isset($this->obj["oid"]) and $this->obj["oid"] != $this->obj["brother_of"])
		{
			$tmp = $this->get_original();
			return $tmp->trans_get_val_str($prop);
		}

		$pd = isset($GLOBALS["properties"][$this->obj["class_id"]][$param]) ? $GLOBALS["properties"][$this->obj["class_id"]][$param] : null;
		if (!$pd)
		{
			return $this->trans_get_val($param);
		}

		$type = $pd["type"];
		if ($is_oid)
		{
			$type = "oid";
		}

		$val = $this->trans_get_val($param);
		switch($type)
		{
			// YOU *CAN NOT* convert dates to strings here - it fucks up dates in vcl tables
			case "relmanager":
			case "relpicker":
			case "classificator":
			case "popup_search":
			case "crm_participant_search":
			case "releditor":
				if ($pd["store"] == "connect")
				{
					$rels = new object_list($this->connections_from(array(
						"type" => $pd["reltype"]
					)));
					$_tmp = array();
					foreach($rels->arr() as $rel_o)
					{
						$_tmp[] = $rel_o->trans_get_val("name");
					}
					if (count($_tmp))
					{
						$val = join(", ", $_tmp);
					}
					else
					{
						$val = "";
					}
					break;
				}

			case "oid":
				if (is_oid($val))
				{
					if ($GLOBALS["object_loader"]->ds->can("view", $val))
					{
						$tmp = new object($val);
						$val = $tmp->trans_get_val("name");
					}
					else
					{
						$val = "";
					}
				}
				else
				if (is_array($val))
				{
					$vals = array();
					foreach($val as $k)
					{
						if (is_oid($k))
						{
							if ($GLOBALS["object_loader"]->ds->can("view", $k))
							{
								$tmp = new object($k);
								$vals[] = $tmp->trans_get_val("name");
							}
						}
					}
					$val = join(", ", $vals);
				}
				break;
		}
		if ($val === "0" || $val === 0)
		{
			$val = "";
		}
		return $val;
	}

	public function acl_del($g_oid)
	{
		$group = obj($g_oid);
		$GLOBALS["object_loader"]->remove_acl_group_from_obj($group, $this->obj["oid"]);
		$this->disconnect(array(
			"from" => $group->id(),
			"type" => RELTYPE_ACL
		));
	}

	public function acl_get()
	{
		return $GLOBALS["object_loader"]->get_acl_groups_for_obj($this->obj["oid"]);
	}

	public function acl_set($group, $acl)
	{
		if (!$this->is_connected_to(array("to" => $group->id())))
		{
			$this->connect(array(
				"to" => $group->id(),
				"reltype" => RELTYPE_ACL
			));
		}

		if (!$group->prop("gid"))
		{
			return;
		}

		$GLOBALS["object_loader"]->add_acl_group_to_obj($group->prop("gid"), $this->obj["oid"]);
		$GLOBALS["object_loader"]->save_acl(
			$this->obj["oid"],
			$group->prop("gid"),
			$acl
		);

		$this->init_acl();
	}

	public function get_first_conn_by_reltype($type = NULL)
	{
		$conns = $this->connections_from(array(
			"type" => $type,
		));
		return reset($conns); // reset($empty_arr) gives bool(false)
	}

	public function get_first_obj_by_reltype($type = NULL)
	{
		$conns = $this->connections_from(array(
			"type" => $type,
		));
		if ($first = reset($conns))
		{
			return $first->to();
		}
		return false;
	}

	public function get_xml($options)
	{
		$i = new obj_xml_gen();
		return $i->gen($this->obj["oid"], $options);
	}

	public function from_xml($xml, $parent)
	{
		$i = new obj_xml_gen();
		$oid = $i->unser($xml, $parent);
		return new object($oid);
	}

	public function get_state_id()
	{
		return (int)ifset($this->obj, "mod_cnt");
	}

	public function get_object_data()
	{
		return $this->obj;
	}

	public function is_a($class_id)
	{
		$is = false;
		if (is_class_id($class_id))
		{
			$cl = basename(aw_ini_get("classes.{$class_id}.object_override"));
			if ($class_id == $this->class_id())
			{ // object is of queried class
				$is = true;
			}
			elseif (aw_ini_isset("classes.{$class_id}.object_override") and $this instanceof $cl)
			{ // object extends queried class
				$is = true;
			}
		}
		return $is;
	}

	public function is_saved()
	{
		return isset($this->obj["oid"]) and is_oid($this->obj["oid"]);
	}

	public function implements_interface ($name)
	{
		$interfaces = class_implements($this, true);
		return in_array($name, $interfaces);
	}

	public function has_method ($name)
	{
		return method_exists($this, (string) $name);
	}

	/////////////////////////////////////////////////////////////////
	// private functions

	protected function _int_set_prop_mod($prop, $oldval, $newval)
	{
		$cv1 = $oldval;
		$cv2 = $newval;
		if ($cv1 === "" && $cv2 === "0")
		{
			$cv1 = "0";
		}
		if ($cv1 === "0" && $cv2 === "")
		{
			$cv2 = "0";
		}
		if (is_array($cv1) || is_array($cv2))
		{
			$cv1 = serialize($cv1);
			$cv2 = serialize($cv2);
		}
		// strlen is here, cause otherwise "0011" would == "011" and "11"
		if ($cv1 != $cv2 || strlen($cv1) != strlen($cv2))
		{
			$this->props_modified[$prop] = 1;
		}
	}

	protected function _int_set_ot_mod($fld, $oldval, $newval)
	{
		$cv1 = $oldval;
		$cv2 = $newval;
		if (is_array($cv1) || is_array($cv2))
		{
			$cv1 = serialize($cv1);
			$cv2 = serialize($cv2);
		}
		if ($cv1 != $cv2 && isset($GLOBALS["object_loader"]->all_ot_flds[$fld]))
		{
			$this->ot_modified[$fld] = 1;
		}
	}

	protected function _int_load()
	{
		$oid = $GLOBALS["object_loader"]->param_to_oid($this->obj["__obj_load_parameter"]);
		$this->_int_load_property_values();

		// yeees, this looks weird, BUT it is needed if the loaded object is not actually the one requested
		$GLOBALS["objects"][$oid] = $this;

		if ($oid !== $this->obj["oid"])
		{
			$GLOBALS["objects"][$this->obj["oid"]] = $this;
		}
	}

	function _int_load_properties($cl_id = NULL)
	{
		if (empty($cl_id) and !empty($this->obj["class_id"]))
		{
			$cl_id = $this->obj["class_id"];
		}

		if (isset($GLOBALS["properties"][$cl_id]) && isset($GLOBALS["tableinfo"][$cl_id]) && isset($GLOBALS["of2prop"][$cl_id]))
		{ // class properties already loaded
			return;
		}

		// then get the properties
		if ($cl_id == 29)
		{
			$file = "doc";
		}
		else
		{
			$file = empty($GLOBALS["cfg"]["classes"][$cl_id]["file"]) ? null : basename($GLOBALS["cfg"]["classes"][$cl_id]["file"]);
		}

		list(
				$GLOBALS["properties"][$cl_id],
				$GLOBALS["tableinfo"][$cl_id],
				$GLOBALS["relinfo"][$cl_id],
				$GLOBALS["classinfo"][$cl_id],
			) =
			$GLOBALS["object_loader"]->load_properties(array(
				"file" => $file,
				"clid" => $cl_id
		));

		if (!isset($GLOBALS["properties"][$cl_id]))
		{
			$GLOBALS["properties"][$cl_id] = "";
		}

		if (!isset($GLOBALS["tableinfo"][$cl_id]))
		{
			$GLOBALS["tableinfo"][$cl_id] = "";
		}

		// also make list of properties that belong to object, so we can keep them
		// in sync in $this->obj and properties

		// things in this array can be accessed later with $objref->prop("keyname")
		$GLOBALS["of2prop"][$cl_id] = array(
			"brother_of" => "brother_of",
			"parent" => "parent",
			"class_id" => "class_id",
			"lang_id" => "lang_id",
			"period" => "period",
			"created" => "created",
			"modified" => "modified",
			"periodic" => "periodic",
		);
		foreach($GLOBALS["properties"][$cl_id] as $prop)
		{
			if (!empty($prop["table"]) && $prop['table'] === "objects" && $prop["field"] !== "meta")
			{
				$GLOBALS["of2prop"][$cl_id][$prop['name']] = $prop['name'];
			}
		}
	}

	protected function _int_do_save($exclusive, $previous_state)
	{
		if ($previous_state === null)
		{
			$previous_state = $this->get_state_id();
		}

		// first, update modifier fields
		if (!$this->no_modify)
		{
			$this->_int_set_of_value("modified", time());
			$this->_int_set_of_value("modifiedby", aw_global_get("uid"));
		}

		if (!is_array($GLOBALS["properties"][$this->obj["class_id"]]))
		{
			$this->_int_load_properties();
		}

		$_is_new = false;
		if (empty($this->obj["oid"]))
		{
			$this->_int_init_new();
			$this->_int_do_inherit_new_props();

			// no exclusive when creating
			$this->obj["oid"] = $GLOBALS["object_loader"]->ds->create_new_object(array(
				"objdata" => &$this->obj,
				"properties" => $GLOBALS["properties"][$this->obj["class_id"]],
				"tableinfo" => $GLOBALS["tableinfo"][$this->obj["class_id"]]
			));
			if (empty($this->obj["brother_of"]))
			{
				$this->obj["brother_of"] = $this->obj["oid"];
			}
			$GLOBALS["object_loader"]->handle_cache_update($this->id(), $this->site_id(), "create_new_object");
			$_is_new = true;
		}
		else
		{
			// check if the class specifies that it is versioned and that something has changed
			if (!empty($GLOBALS["classinfo"][$this->obj["class_id"]]["versioned"]) && aw_ini_get("config.object_versioning"))
			{
				if (count($this->ot_modified) > 1 || count($this->props_modified) > 1)
				{
					$GLOBALS["object_loader"]->ds->backup_current_version(array(
						"properties" => $GLOBALS["properties"][$this->obj["class_id"]],
						"tableinfo" => $GLOBALS["tableinfo"][$this->obj["class_id"]],
						"id" => $this->obj["oid"]
					));
				}
			}

			// now, save objdata
			$GLOBALS["object_loader"]->ds->save_properties(array(
				"objdata" => $this->obj,
				"properties" => $GLOBALS["properties"][$this->obj["class_id"]],
				"tableinfo" => $GLOBALS["tableinfo"][$this->obj["class_id"]],
				"propvalues" => $this->obj["properties"],
				"ot_modified" => $this->ot_modified,
				"props_modified" => $this->props_modified,
				"create_new_version" => $this->_create_new_version,
				"exclusive_save" => $exclusive,
				"current_mod_count" => $previous_state
			));
			$GLOBALS["object_loader"]->handle_cache_update($this->brother_of(), $this->site_id(), "save_properties");

			$this->ot_modified = array("modified" => 1);
			$this->props_modified = array();
		}

		// this here is bad, I know, but it is necessary. Why? because if for some of the connections that are created
		// there is a message handler, that reies to load the object just created, it errors out, because it is in a state of flux -
		// it should be in the objects array, but that gets done in the object_loader that called here.
		// so we have to do it here as well to fix the in-between state to the correct one right now
		$GLOBALS["objects"][$this->obj["oid"]] =& $this;

		if (isset($this->obj["_create_connections"]) && is_array($this->obj["_create_connections"]))
		{
			foreach($this->obj["_create_connections"] as $new_conn)
			{
				//$obj = obj($this->obj["oid"]);
				if (is_oid($new_conn["to"]))
				{
					$this->connect($new_conn);
				}
			}
		}

		if (isset($this->obj["_create_brothers"]) && is_array($this->obj["_create_brothers"]))
		{
			foreach($this->obj["_create_brothers"] as $bro_args)
			{
				$this->create_brother($bro_args);
			}
		}

		// obj inherit props impl
		$this->_int_do_obj_inherit_props();

		// if this is a brother object, we should save the original as well
		if ($this->obj["oid"] != $this->obj["brother_of"])
		{
			// first, unload the object
			// of course, we will lose data here if it is modified, but this is a race condition anyway.
			unset($GLOBALS["objects"][$this->obj["brother_of"]]);
			$prev = obj_set_opt("no_cache", 1);
			$original = obj($this->obj["brother_of"]);
			obj_set_opt("no_cache", $prev);
			$original->save();
		}

		// log save
		$GLOBALS["object_loader"]->_log($_is_new, $this->obj["oid"], (string)$this->name(), $this->obj["class_id"]);

		// check cache
		$this->_check_save_cache();

		return $this->obj["oid"];
	}

	protected function _check_save_cache()
	{
		if (self::$global_save_count > self::CACHE_OFF_ON_SAVE_COUNT && !self::$cache_off)
		{
			obj_set_opt("no_cache", 1);
			self::$cache_off = true;
			register_shutdown_function(array(&$GLOBALS["object_loader"], "handle_no_cache_clear"));
		}
		self::$global_save_count++;
	}

	protected function _int_do_inherit_new_props()
	{
		$data = object_loader::instance()->obj_inherit_props_conf;
		if (!is_array($data))
		{
			return;
		}

		foreach($data as $from_oid => $ihd)
		{
			if (is_array($ihd))
			{
				foreach($ihd as $r_ihd)
				{
					if ($r_ihd["to_class"] == $this->obj["class_id"] && (!is_array($r_ihd["only_to_objs"]) || count($r_ihd["only_to_objs"]) == 0))
					{
						if (object_loader::can("edit", $from_oid))
						{
							$orig = obj($from_oid);
							$this->_int_set_prop_mod($r_ihd["to_prop"], $this->obj["properties"][$r_ihd["to_prop"]], $orig->prop($r_ihd["from_prop"]));
							$this->obj["properties"][$r_ihd["to_prop"]] = $orig->prop($r_ihd["from_prop"]);
						}
					}
				}
			}
		}
	}

	protected function _int_do_obj_inherit_props()
	{
		if (isset($GLOBALS["object_loader"]->obj_inherit_props_conf[$this->obj["oid"]]))
		{
			$tmp = safe_array($GLOBALS["object_loader"]->obj_inherit_props_conf[$this->obj["oid"]]);
			foreach($tmp as $ihd)
			{
				$propv = $this->obj["properties"][$ihd["from_prop"]];

				// find all object os correct type
				$filt = array(
					"class_id" => $ihd["to_class"]
				);
				if (is_array($ihd["only_to_objs"]) && count($ihd["only_to_objs"]) > 0)
				{
					$filt["oid"] = $ihd["only_to_objs"];
				}
				$ol = new object_list($filt);
				foreach($ol->arr() as $o)
				{
					$o->set_prop($ihd["to_prop"], $propv);
					$o->save();
				}
			}
		}
	}

	protected function _int_do_implicit_save()
	{
		if ($this->implicit_save)
		{
			$this->save();
		}
	}

	protected function _int_sync_from_objfield_to_prop($ofname, $mod = true)
	{
		// object field changed, sync to properties
		$pn = empty($this->obj["class_id"]) || empty($GLOBALS["of2prop"][$this->obj["class_id"]][$ofname]) ? "" : $GLOBALS["of2prop"][$this->obj["class_id"]][$ofname];
		if ($pn != "")
		{
			if ($mod)
			{
				$this->_int_set_prop_mod(
					$pn,
					isset($this->obj["properties"][$pn]) ? $this->obj["properties"][$pn] : null,
					isset($this->obj[$ofname]) ? $this->obj[$ofname] : null
				);
			}
			$this->obj["properties"][$pn] = isset($this->obj[$ofname]) ? $this->obj[$ofname] : "";
		}
	}

	protected function _int_path($param)
	{
		$ret = array();
		$parent = $this->id();
		$cnt = 0;

		if (!empty($param["full_path"]))
		{
			$rootmenu = array(1);
			$add = false;
		}
		else
		if (is_admin())
		{
			$rootmenu = array(aw_ini_get("admin_rootmenu2"));
			$add = false;
		}
		else
		{
			$rootmenu = array(aw_ini_get("rootmenu"));
			$add = true;
		}

// /* dbg */ if ($GLOBALS["gdg"] == 1)
// /* dbg */ echo "int path enter ".dbg::dump($param)." parent = $parent root = ".dbg::dump($rootmenu)." <br>\n";

		while ($parent && !in_array($parent, $rootmenu))
		{
// /* dbg */ if ($GLOBALS["gdg"] == 1)
// /* dbg */ echo "loop with $parent <br>\n";

			if ($GLOBALS["object_loader"]->ds->can("view", $parent))
			{
				unset($t);
				$__from_raise_error = aw_global_get("__from_raise_error");
				aw_global_set("__from_raise_error", 1);
				try
				{
					$t = new object($parent);
				}
				catch (Exception $e)
				{
					$parent = 0;
					break;
				}
				aw_global_set("__from_raise_error", $__from_raise_error);

				if (isset($param["to"]) && is_oid($param["to"]) && $t->id() == $param["to"])
				{
					$add = false;
					break;
				}

				$ret[] = $t;
				$parent = $t->parent();
			}
			else
			{
				// we break here, because if we don't have view access to an object in the path
				// we can't find it's parent and then we're fucked anyway.
				$parent = 0;
				break;
			}

			$cnt++;

			if ($cnt > 100)
			{
				error::raise(array(
					"id" => "ERR_HIER",
					"msg" => sprintf(t("object::path(%s): error in object hierarchy, infinite loop!"), $this->id())
				));
				return;
			}
		}

// /* dbg */ if ($GLOBALS["gdg"] == 1)
// /* dbg */ echo "int path return ".dbg::dump($ret)." <br>\n";

		if ($add && !aw_global_get("__is_install"))
		{
			$rm = reset($rootmenu);
			if ($GLOBALS["object_loader"]->ds->can("view", $rm))
			{
				$ret[] = obj($rm);
			}
		}

		$ret = array_reverse($ret);
		if (!empty($param["no_self"]))
		{
			array_pop($ret);
		}
		return $ret;
	}

	protected function _int_can($param)
	{
		return object_loader::can($param, $this->obj["oid"]);
	}

	protected function _int_can_save()
	{
		$clid = $this->obj["class_id"];

		if (isset($this->obj["parent"]) and is_array($this->obj["parent"]))
		{
			$this->obj["parent"] = $this->obj["parent"]["oid"];
		}

		// required params - parent and class_id
		if (isset($this->obj["parent"]) and $this->obj["parent"] > 0 and isset($clid) and $clid > 0)
		{
			// acl
			if (!empty($this->obj["oid"]))
			{
				if ($this->_int_can("edit"))
				{
					return true;
				}
				else
				{
					throw new awex_obj_acl(sprintf("No access to edit object '%s'", $this->obj["oid"]));
				}
			}
			else
			{
				if (object_loader::can("add", $this->obj["parent"]))
				{
					return true;
				}
				else
				{
					throw new awex_obj_acl(sprintf("No access to add object under folder '%s' (gidlist = %s)", $this->obj["parent"], join(", ", (array) aw_global_get("gidlist"))));
				}
			}
		}

		throw new awex_obj_acl(sprintf("Object '%s' cannot be saved, needed properties are not set (parent, class_id)", (isset($this->obj["oid"]) ? $this->obj["oid"] : "NULL")));

		// security checks
		if (aw_ini_isset("classes.{$clid}.has_server_access") and aw_ini_get("classes.{$clid}.has_server_access") and aw_ini_get("acl.restrict_server_access"))
		{
			throw new awex_obj_acl(sprintf("Object '%s' cannot be saved, server access restriction enabled", (isset($this->obj["oid"]) ? $this->obj["oid"] : "NULL")));
		}
	}

	protected function _int_set_of_value($ofield, $val)
	{
		$oldval = isset($this->obj[$ofield]) ? $this->obj[$ofield] : null;
		$this->_int_set_ot_mod($ofield, $oldval, $val);
		$this->obj[$ofield] = $val;
		$this->_int_sync_from_objfield_to_prop($ofield);
	}

	protected function _int_init_new()
	{
		$this->_int_set_of_value("created", time());
		$this->_int_set_of_value("createdby", aw_global_get("uid"));
		$this->_int_set_of_value("hits", 0);

		if (empty($this->obj["site_id"]))
		{
			$this->_int_set_of_value("site_id", $GLOBALS["cfg"]["site_id"]);
		}

		// new objects can't be created with deleted status
		if (empty($this->obj["status"]))
		{
			$this->_int_set_of_value("status", object::STAT_NOTACTIVE);
		}

		// default to current lang id
		if (empty($this->obj["lang_id"]))
		{
			$this->_int_set_of_value("lang_id", aw_global_get("lang_id"));
		}

		// set property defaults
	}

	protected function _int_is_property($prop)
	{
		if (!$this->props_loaded and !empty($this->obj["class_id"]))
		{
			$this->_int_load_property_values();
		}
		return isset($GLOBALS["properties"][$this->obj["class_id"]][$prop]) && is_array($GLOBALS["properties"][$this->obj["class_id"]][$prop]);
	}

	protected function _int_do_delete($oid, $full_delete = false)
	{
		// load the object to see of its brother status
		$obj = $GLOBALS["object_loader"]->ds->get_objdata($oid);

		$todelete = array();

		// if this object is a brother to another object, just delete it.
		if ($obj["brother_of"] != $oid)
		{
			$todelete[] = $oid;
		}
		// else, if this is an original object
		else
		{
			// find all of its brothers and delete all of them.
			list($tmp) = $GLOBALS["object_loader"]->ds->search(array(
				"brother_of" => $oid
			));
			$todelete = array_keys($tmp);
		}

		foreach($todelete as $oid)
		{
			if (!$GLOBALS["object_loader"]->ds->can("delete", $oid))
			{
				continue;
			}
			$params = array(
				"oid" => $oid
			);
			post_message_with_param(
				"MSG_STORAGE_DELETE",
				$this->obj["class_id"],
				$params
			);

			$tmpo = obj($oid);
			// get object's class info
			$clid = $tmpo->class_id();
			if ($clid == 7)
			{
				$type = ST_DOCUMENT;
			}
			elseif (isset($GLOBALS["classinfo"][$clid]["syslog_type"]["text"]) and defined($GLOBALS["classinfo"][$clid]["syslog_type"]["text"]))
			{
				$type = defined($GLOBALS["classinfo"][$clid]["syslog_type"]["text"]) ? constant($GLOBALS["classinfo"][$clid]["syslog_type"]["text"]) : NULL;
			}
			else
			{
				$type = 10000;
			}

			$nm = $tmpo->name();

			if ($full_delete)
			{
				$GLOBALS["object_loader"]->ds->final_delete_object($oid);
				$GLOBALS["object_loader"]->cache->_log($type, "SA_FINAL_DELETE", $nm, $oid, false);
			}
			else
			{
				$GLOBALS["object_loader"]->ds->delete_object($oid);
				$GLOBALS["object_loader"]->cache->_log($type, "SA_DELETE", $nm, $oid, false);
				$GLOBALS["object_loader"]->handle_cache_update($tmpo->id(), $tmpo->site_id(), "delete_object");
			}
		}

		// now we need to fetch all objects from the db that are below the objects on $todelete and set them as deleted as well
		$belows = $this->_fetch_to_delete_objects($oid);
		if (count($belows))
		{
			$GLOBALS["object_loader"]->ds->delete_multiple_objects($belows);
		}

		// must clear acl cache for all objects below it
		// but since I think that finding the subobjects and clearing just those will be slower than clearing it all
		// we're gonna clear it all.
		if (!aw_ini_get("acl.use_new_acl"))
		{
			$c = new cache();
			$c->file_clear_pt("acl");
		}
	}

	protected function _int_create_brother($parent)
	{
		$rv =  $GLOBALS["object_loader"]->ds->create_brother(array(
			"objdata" => $this->obj,
			"parent" => $parent
		));
		$GLOBALS["object_loader"]->handle_cache_update($this->id(), $this->site_id(), "create_brother");
		// this here makes sure that the site_id setting is correct for the brother
		$o = obj($rv);
		$o->set_parent($o->parent());
		$o->save();
		return $rv;
	}

	protected function _int_check_draft($param)
	{
		// If no property specified, return NULL
		if(strlen($prop) == 0)
		{
			return false;
		}

		$user_oid = get_instance("user")->get_current_user();
		// If no user is set, return NULL.
		if(!is_oid($user_oid))
		{
			return false;
		}
		return true;
	}

	protected function _int_set_draft($param, $value)
	{
		if($this->_int_check_draft($param))
		{
			return false;
		}

		$user_oid = get_instance("user")->get_current_user();

		$params = array(
			"class_id" => draft_obj::CLID,
			"draft_object" => $this->id(),
			"draft_property" => $prop,
			"draft_user" => $user_oid,
			"limit" => 1
		);
		if(!is_oid($this->id()))
		{
			unset($params["draft_object"]);
			$params["draft_new"] = 1;
		}
		$ol = new object_list($params);

		if($ol->count() > 0)
		{
			$o = $ol->begin();
		}
		else
		{
			$o = obj();
			$o->set_class_id(draft_obj::CLID);
			$o->set_parent($user_oid);
		}

		$o->set_prop("draft_user", $user_oid);
		$o->set_prop("draft_property", $param);
		if(is_oid($this->id()))
		{
			$o->set_prop("draft_object", $this->id());
		}
		else
		{
			$o->set_prop("draft_new", $this->class_id());
		}
		$o->set_prop("draft_content", $value);
		$o->save();

		return true;
	}

	protected function _int_get_draft($prop)
	{
		if($this->_int_check_draft($param))
		{
			return NULL;
		}
		$user_oid = get_instance("user")->get_current_user();

		$params = array(
			"class_id" => draft_obj::CLID,
			"draft_object" => $this->id(),
			"draft_property" => $prop,
			"draft_user" => $user_oid,
			"limit" => 1
		);
		if(!is_oid($this->id()))
		{
			unset($params["draft_object"]);
			$params["draft_new"] = $this->class_id();
		}

		$odl = new object_data_list(
			$params,
			array(
				draft_obj::CLID => array("draft_content"),
			)
		);
		if($odl->count() > 0)
		{
			$o = reset($odl->arr());
			return $o["draft_content"];
		}
		else
		{
			return NULL;
		}
	}

	protected function _int_set_prop($prop, $val)
	{
		if (!$this->props_loaded)
		{
			$this->_int_load_property_values();
		}
		$this->_int_set_prop_mod($prop, ifset($this->obj, "properties", $prop), $val);
		$this->obj["properties"][$prop] = $val;
	}

	protected function _int_get_prop($prop)
	{
		if (!$this->props_loaded)
		{
			if (empty($this->obj["class_id"]))
			{
				return null;
			}
			$this->_int_load_property_values();
		}
		$cur_v = null;
		// if this is a complex thingie, then loopdaloop
		if (strpos($prop, ".") !== false || preg_match("/RELTYPE_(.*)\(CL_(.*)\)/", $prop, $mt))
		{
			$o = $this;
			$bits = explode(".", $prop);
			foreach($bits as $idx => $part)
			{
				if (substr($part, 0, strlen("RELTYPE")) == "RELTYPE")
				{
					if (preg_match("/RELTYPE_(.*)\(CL_(.*)\)/", $part, $mt))
					{
						// relation from class $mt[2] with type $mt[1]
						$c = new connection();
						$finder = array(
							"from.class_id" => constant("CL_".$mt[2]),
							"reltype" => "RELTYPE_".$mt[1],
							"to" => $cur_v ? $cur_v : $this->id()
						);
						$conns = $c->find($finder);
						if (!count($conns))
						{
							return null;
						}
						$con = reset($conns);
						$cur_v = $con["from"];
					}
					else
					{
						$prop_dat = array();
						$tmp = $o->get_first_obj_by_reltype($part);
						if ($tmp)
						{
							$cur_v = $tmp->id();
						}
						else
						{
							return null;
						}
					}
				}
				else
				{
					$cur_v = $o->prop($part);
					$prop_dat = isset($GLOBALS["properties"][$o->class_id()][$part]) ? $GLOBALS["properties"][$o->class_id()][$part] : NULL;
				}
				// the true here is because if the user says that this thingie is an oid, then we trust him
				// we check of course, but still. we trust him.
				if (!$prop_dat && $GLOBALS["object_loader"]->is_object_member_fun($part))
				{
					$cur_v = $o->$part();
					if ($part === "parent")
					{
						$o = obj($cur_v);
					}
				}
				else
				if (true || in_array($prop_dat["type"], array("relpicker", "classificator", "popup_search", "relmanager", "releditor")))
				{
					if (is_array($cur_v) && count($cur_v) == 1)
					{
						$cur_v = reset($cur_v);
					}
					$acl_tmp = $GLOBALS["cfg"]["acl"]["no_check"];
					$GLOBALS["cfg"]["acl"]["no_check"] = 0;
					if (!$GLOBALS["object_loader"]->ds->can("view", $cur_v))
					{
						$GLOBALS["cfg"]["acl"]["no_check"] = $acl_tmp;
						if ($idx == (count($bits)-1))
						{
							return $cur_v;
						}
						return null;
					}
					$GLOBALS["cfg"]["acl"]["no_check"] = $acl_tmp;

					$o = obj($cur_v);
				}
				else
				{
					return $cur_v;
				}
			}
			return $cur_v;
		}

		$pd = false;
		if (isset($GLOBALS["properties"][$this->obj["class_id"]][$prop]))
		{
			$pd = $GLOBALS["properties"][$this->obj["class_id"]][$prop];
		}

		if ($pd && $pd["field"] === "meta" && $pd["table"] === "objects" && isset($this->obj["meta"][$pd["name"]]))
		{
			$this->_scan_warning_possibility($this->obj["meta"][$pd["name"]], $pd);
			return isset($this->obj["meta"][$pd["name"]]) ? $this->obj["meta"][$pd["name"]] : null;
		}

		if (isset($this->obj["properties"][$prop]))
		{
			$this->_scan_warning_possibility($this->obj["properties"][$prop], $pd);
		}

		return isset($this->obj["properties"][$prop]) ? $this->obj["properties"][$prop] : null;
	}

	protected function _scan_warning_possibility($value, $prop = false)
	{
		if(!$prop || !is_array($prop) || !$prop["type"])
		{
			return false;
		}
		$_1 = array("checkbox", "chooser");
		$_2 = array("date_select", "time_select");
		$level_0 = array("checkbox", "text", "releditor", "status", "href", "hidden", "callback");

		if(in_array($prop["type"], $_1) || in_array($prop["type"], $level_0) || $prop["store"] === "no")
		{
			return false;
		}

		if(in_array($prop["type"], $_2) && $value != -1)
		{
			return false;
		}

		if(!empty($value) || is_string($value) && strlen($value))
		{
			return false;
		}

		if(empty($prop["warning"]))
		{
			$prop["warning"] = ($prop["type"] === "relpicker") ? 2 : 1;
		}

		return false;
	}

	protected function _int_load_property_values()
	{
		$this->_int_load_properties();

		if (!isset($this->obj["oid"]) or !is_oid($this->obj["oid"]))
		{
			// do not try to read an empty object
			return;
		}

		$this->obj["properties"] = $GLOBALS["object_loader"]->ds->read_properties(array(
			"properties" => $GLOBALS["properties"][$this->obj["class_id"]],
			"tableinfo" => $GLOBALS["tableinfo"][$this->obj["class_id"]],
			"objdata" => $this->obj,
		));

		foreach(safe_array($GLOBALS["of2prop"][$this->obj["class_id"]]) as $key => $val)
		{
			if (empty($this->obj["properties"][$key]))
			{
				$this->_int_sync_from_objfield_to_prop($key, false);
			}
		}

		$this->props_loaded = true;
	}

	protected function _fetch_to_delete_objects($pt)
	{
		$parents = array($pt);
		$ret = array();
		while (count($parents) > 0)
		{
			list($tmp) = $GLOBALS["object_loader"]->ds->search(array(
				"parent" => $parents,
				"lang_id" => array(),
				"site_id" => array()
			));
			$parents = array();
			foreach($tmp as $idx => $d)
			{
				$ret[] = $idx;
				$parents[] = $idx;
			}
		}
		return $ret;
	}

	function __destruct()
	{
		try
		{
			if (isset($this->obj["oid"]) and aw_locker::is_locked("object", $this->obj["oid"]))
			{
				aw_locker::unlock("object", $this->obj["oid"], aw_locker::BOUNDARY_SERVER);
			}
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}

	// returns object reference container (object class object) to be used by calls to other objects
	protected function ref()
	{
		if (!empty($this->obj["oid"]))
		{ // existing object reference container
			return new object($this->obj["oid"]);
		}
		else
		{ // new object, find referring container
		}
	}

/** Throws an awex_obj_state exception when state isn't what is required
	@attrib api=1 params=pos
	@param state type=string
		State to require from this object. Currently only available option is 'saved'
	@comment
	@returns void
**/
	protected function require_state($state = "saved")
	{
		$required_state = false;

		if ("saved" === $state and $this->is_saved())
		{
			$required_state = true;
		}

		if (!$required_state)
		{
			throw new awex_obj_state();
		}
	}
}
