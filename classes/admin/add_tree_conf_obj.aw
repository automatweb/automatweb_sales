<?php

class add_tree_conf_obj extends _int_object
{
	const CLID = 118;

	const BEHAVIOUR_RESTRICTIVE = 1;
	const BEHAVIOUR_PERMISSIVE = 2;

	public function awobj_get_behaviour()
	{
		$value = (int) parent::prop("behaviour");
		if (empty($value))
		{
			$value = self::BEHAVIOUR_RESTRICTIVE;
		}
		return $value;
	}

	public function cb_access($class, $method)
	{
	}

	/** Filters and returns aw_ini_get("classes") array. Only classes marked 'visible' in this configuration
		@attrib api=1 params=pos
		@comment
		@returns array
		@errors
	**/
	public function get_visible()
	{
	}


	/** Returns active configuration object or NULL if none found
		@attrib api=1 params=pos
		@comment
		@returns CL_ADD_TREE_CONF|NULL
		@errors
			throws awex_obj_acl if configuration id found but no access
			throws awex_obj_na if configuration id found but object not available
	**/
	public static function get_active_configuration()
	{
		$active_cfg = null;

		// go over groups and for each check if it has the conf
		$cur_max = 0;
		$gidlist_oid = aw_global_get("gidlist_oid");
		if (is_array($gidlist_oid))
		{
			foreach($gidlist_oid as $g_oid)
			{
				try
				{
					$o = obj($g_oid, array(), CL_GROUP);
				}
				catch (Exception $e)
				{
					continue;
				}

				$c = $o->connections_from(array(
					"type" => "RELTYPE_ADD_TREE" /* from core/users/group */
				));

				if (count($c) > 0 && $o->prop("priority") > $cur_max)
				{
					$cur_max = $o->prop("priority");
					$fc = reset($c);
					$active_cfg = $fc->prop("to");
				}
			}
		}

		if (!$active_cfg and is_oid(aw_ini_get("add_tree_conf.default")))
		{
			$active_cfg = aw_ini_get("add_tree_conf.default");
		}

		if ($active_cfg)
		{
			$active_cfg = obj($active_cfg, array(), self::CLID);
		}

		return $active_cfg;
	}
}

