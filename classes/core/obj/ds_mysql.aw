<?php

//TODO: savehandle/restorehandle poliitika v2lja m6elda.

class _int_obj_ds_mysql extends _int_obj_ds_base
{
	private $last_search_query_string = "";
	private $last_object_table_alias = "";

	function _int_obj_ds_mysql()
	{
		$this->init();
	}

	public function is_deleted($oid)
	{
		$this->quote($oid);
		$this->save_handle();
		$data = $this->db_fetch_row("SELECT oid, status FROM objects WHERE oid = {$oid}");
		$this->restore_handle();
		return (isset($data["status"]) && (int) $data["status"] === object::STAT_DELETED);
	}

	public function is_accessible($oid, $action = "view", $user_oid = 0)
	{
		$user_oid = $user_oid ? $user_oid : aw_global_get("uid_oid");
		$this->quote($oid);
		$this->save_handle();
		$data = $this->db_fetch_row("SELECT acl_data FROM objects WHERE oid = {$oid}");
		$this->restore_handle();
		return (isset($data["status"]) && (int) $data["status"] === object::STAT_DELETED);
	}

	// returns oid for alias
	// parameters:
	//	alias - required
	//	site_id - optional
	//	parent - optional
	function get_oid_by_alias($arr)
	{
		extract($arr);
		$parent = $site_id = "";

		if (isset($arr["parent"]))
		{
			$parent = " AND parent = '".$arr["parent"]."'";
		}

		if (isset($arr["site_id"]))
		{
			$site_id = " AND site_id = '".$arr["site_id"]."'";
		}

		$this->quote($alias);
		$q = sprintf("
			SELECT
				%s
			FROM
				objects
			WHERE
				alias = '%s' AND
				status != 0 %s %s
		", OID, $alias, $site_id,$parent);

		return $this->db_fetch_field($q, OID);
	}

	function get_objdata($oid, $param = array())
	{
		$this->save_handle();
		$oid = (int)$oid;
		if (!empty($GLOBALS["object2version"][$oid]) && $GLOBALS["object2version"][$oid] !== "_act")
		{
			$v = $GLOBALS["object2version"][$oid];
			$ret = $this->db_fetch_row("SELECT * FROM objects WHERE oid = '$oid' AND status != 0");
			$ret2 = $this->db_fetch_row("SELECT o_alias, o_jrk, o_metadata FROM documents_versions WHERE docid = '$oid' AND version_id = '$v'");
			$ret["alias"] = $ret2["o_alias"];
			$ret["jrk"] = $ret2["o_jrk"];
			$ret["metadata"] = $ret2["o_metadata"];
			$rv =  $this->_get_objdata_proc($ret, $param, $oid);
			$this->restore_handle();
			return $rv;
		}

		if (isset($this->read_properties_data_cache[$oid]))
		{
			$td = $this->read_properties_data_cache[$oid];
			if (isset($GLOBALS["read_properties_data_cache_conn"][$oid]))
			{
				$ps = $GLOBALS["properties"][$td["class_id"]];
				$rts = $GLOBALS["relinfo"][$td["class_id"]];

				$cfp_dat = array();
				foreach($GLOBALS["read_properties_data_cache_conn"][$oid] as $_tmp => $d)
				{
					foreach($d as $_tmp2 => $d2)
					{
						// find prop from reltype
						// add to that
						$pn = false;
						foreach($ps as $pid => $pd)
						{
							if ($pd["store"] === "connect" && $rts[$pd["reltype"]]["value"] == $d2["reltype"])
							{
								$v = $d2["target"];
								if (!empty($pd["multiple"]))
								{
									$td[$pid][] = $v;
								}
								else
								{
									$td[$pid] = $v;
								}
								continue;
							}
						}
					}
				}
			}
			$ret = $this->_get_objdata_proc($td, $param, $oid);
		}
		else
		{
			$ret = $this->db_fetch_row("SELECT * FROM objects WHERE oid = '$oid' AND status != 0");
			if ($ret["oid"] != $ret["brother_of"])
			{
				$ret["metadata"] = $this->db_fetch_field("SELECT metadata FROM objects WHERE oid = '".$ret["brother_of"]."'", "metadata");
			}
			$ret = $this->_get_objdata_proc($ret, $param, $oid);
		}
		$this->restore_handle();
		return $ret;
	}

	function _get_objdata_proc($ret, $param, $oid = -1)
	{
		if ($ret === false)
		{
			if (!empty($param["no_errors"]))
			{
				return NULL;
			}
			else
			{
				throw new awex_obj_na("No object with id '$oid'");
			}
		}

		$ret["meta"] = aw_unserialize(
			stripslashes($ret["metadata"]),
				//FIXME: stripslashes added here because some metadata records
				// contain strings "\\'" which cause parse errors when aw_unserialize'd.
				// need to find root cause of this, temporary fix here
			false,
			true
		);

		if (!empty($ret["metadata"]) && $ret["meta"] === NULL)
		{
			$ret["meta"] = aw_unserialize(stripslashes(stripslashes($ret["metadata"])), false, true); //!!! miks siin topelt stripslashes vajalik on?
		}
		//unset($ret["metadata"]);

		if ($ret["brother_of"] == 0)
		{
			$ret["brother_of"] = $ret["oid"];
		}

		// unserialize acldata
		if (isset($ret["acldata"]))
		{
			$ret["acldata"] = aw_unserialize($ret["acldata"], false, true);
		}
		else
		{
			$ret["acldata"] = null;
		}

		// filter it for all current groups

		// or we could join the acl table based on the current user.
		// but we can't do that, cause here we can't do things based on the user
		// then again we could just read all the acl and save that. maybe. you think?

		// crap. descisions, descisions...

		// ok, so try for the store-shit-in-object-table
		return $ret;
	}

	// parameters:
	//	properties - property array
	//	tableinfo - tableinfo from propreader
	//	objdata - result of this::get_objdata
	function read_properties($arr)
	{
		$properties = $arr["properties"];
		$tableinfo = $arr["tableinfo"];
		$objdata = $arr["objdata"];

		if (!empty($GLOBALS["object2version"][$objdata["oid"]]) && $GLOBALS["object2version"][$objdata["oid"]] != "_act")
		{
			$arr["objdata"]["load_version"] = $GLOBALS["object2version"][$objdata["oid"]];
			return $this->load_version_properties($arr);
		}
		$ret = array();
		// then read the properties from the db
		// find all the tables that the properties are in
		$tables = array();
		$tbl2prop = array();
		$objtblprops = array();
		$datagrids = array();
		foreach($properties as $prop => $data)
		{
			if (isset($data["store"]) && $data["store"] === "no")
			{
				continue;
			}

			if (empty($data["table"]))
			{
				$data["table"] = "objects";
			}

			if (isset($data["type"]) and $data["type"] === "datagrid")
			{
				$datagrids[$prop] = $data;
			}
			elseif ($data["table"] !== "objects")
			{
				$tables[$data["table"]] = $data["table"];
				$tbl2prop[$data["table"]][] = $data;
			}
			else
			{
				$objtblprops[] = $data;
			}
		}
		$conn_prop_vals = array();
		$conn_prop_fetch = array();

		// import object table properties in the props array
		foreach($objtblprops as $prop)
		{
			if ($prop["store"] === "connect")
			{
				$_co_reltype = $prop["reltype"];
				$_co_reltype = $GLOBALS["relinfo"][$objdata["class_id"]][$_co_reltype]["value"];

				if ($_co_reltype == "")
				{
					error::raise(array(
						"id" => "ERR_NO_RT",
						"msg" => sprintf(t("ds_mysql::read_properties(): no reltype for prop %s (%s)"), $prop["name"], $prop["reltype"])
					));
				}

				$conn_prop_fetch[$prop["name"]] = $_co_reltype;
			}
			elseif ($prop["method"] === "serialize")
			{
				// metadata is unserialized in read_objprops
				$ret[$prop["name"]] = isset($objdata[$prop['field']]) && isset($objdata[$prop["field"]][$prop["name"]]) ? $objdata[$prop["field"]][$prop["name"]] : "";
			}
			elseif ($prop["method"] === "bitmask")
			{
				$ret[$prop["name"]] = ((int)(isset($objdata[$prop["field"]]) ? $objdata[$prop["field"]] : 0)) & ((int)$prop["ch_value"]);
			}
			else
			{
				$ret[$prop["name"]] = isset($objdata[$prop["field"]]) ? $objdata[$prop["field"]] : null;
			}

			if (isset($prop["datatype"]) && $prop["datatype"] === "int" && $ret[$prop["name"]] == "")
			{
				$ret[$prop["name"]] = "0";
			}
		}

		// fix old broken databases where brother_of may be 0 for non-brother objects
		$object_id = ($objdata["brother_of"] ? $objdata["brother_of"] : $objdata["oid"]);

		foreach($datagrids as $g_pn => $g_prop)
		{
			// fetch all rows from the table
			$q = "SELECT * FROM ".$g_prop["table"]." WHERE ".$tableinfo[$g_prop["table"]]["index"]." = ".$object_id." ORDER BY ".$g_prop["field"];
			$this->db_query($q);
			$val = array();
			while ($row = $this->db_next())
			{
				$val[$row[$g_prop["field"]]] = $row;
			}
			$ret[$g_pn] = $val;
		}

		// do a query for each table
		foreach($tables as $table)
		{
			$fields = array();
			$_got_fields = array();
			foreach($tbl2prop[$table] as $prop)
			{
				if ($prop['field'] === "meta" && $prop["table"] === "objects")
				{
					$prop['field'] = "metadata";
				}

				if ($prop["method"] === "serialize" && $prop["store"] !== "connect")
				{
					if (!array_key_exists($prop["field"], $_got_fields))
					{
						$fields[] = $table.".`".$prop["field"]."` AS `".$prop["field"]."`";
						$_got_fields[$prop["field"]] = true;
					}
				}
				elseif ($prop["store"] === "connect")
				{
					$_co_reltype = $prop["reltype"];
					$_co_reltype = $GLOBALS["relinfo"][$objdata["class_id"]][$_co_reltype]["value"];

					if ($_co_reltype == "")
					{
						error::raise(array(
							"id" => "ERR_NO_RT",
							"msg" => sprintf(t("ds_mysql::read_properties(): no reltype for prop %s (%s)"), $prop["name"], $prop["reltype"])
						));
					}

					$conn_prop_fetch[$prop["name"]] = $_co_reltype;
				}
				elseif (isset($prop['type']) and $prop['type'] === 'range') //!!! mis olukord on see kus prop[type] on tyhi?
				{
					$fields[] = $table.".`".$prop["field"]."_from` AS `".$prop["name"]."_from`";
					$fields[] = $table.".`".$prop["field"]."_to` AS `".$prop["name"]."_to`";
				}
				else
				{
					$fields[] = $table.".`".$prop["field"]."` AS `".$prop["name"]."`";
				}
			}

			if (count($fields) > 0)
			{
				$q = "SELECT ".join(",", $fields)." FROM {$table} WHERE `".$tableinfo[$table]["index"]."` = '".$object_id."'";
				if (isset($this->read_properties_data_cache[$object_id]))
				{
					$data = $this->read_properties_data_cache[$object_id];
				}
				else
				{
					$data = $this->db_fetch_row($q);
				}

				// this saves loads of is_array calls in the generic method
				if (is_array($data))
				{
					foreach($data as $_k => $_v)
					{
						if (is_array($_v))
						{
							foreach($_v as $_k2 => $_v2)
							{
								$data[$_k][$_k2] = stripslashes($_v2);
							}
						}
						else
						{
							$data[$_k] = stripslashes($_v);
						}
					}
					$ret += $data;
				}
				else
				{
					$data = stripslashes($data);
				}

				foreach($tbl2prop[$table] as $prop)
				{
					if ($prop["method"] === "serialize")
					{
						if ($prop['field'] === "meta" && $prop["table"] === "objects")
						{
							$prop['field'] = "metadata";
						}

						$unser = isset($ret[$prop["field"]]) ? aw_unserialize($ret[$prop["field"]], false, true) : null;
						$ret[$prop["name"]] = isset($unser[$prop["name"]]) ? $unser[$prop["name"]] : null;
					}

					if (isset($prop["datatype"]) and $prop["datatype"] === "int" and empty($ret[$prop["name"]]))
					{
						$ret[$prop["name"]] = "0";
					}

					if (isset($prop["type"]) and $prop["type"] === "range")
					{
						$ret[$prop['name']] = array(
							'from' => $ret[$prop['name'].'_from'],
							'to' => $ret[$prop['name'].'_to']
						);
						unset($ret[$prop['name'].'_from'], $ret[$prop['name'].'_to']);

					}
				}
			}
		}

		if (count($conn_prop_fetch))
		{
			$cfp_dat = array();
			if (isset($GLOBALS["read_properties_data_cache_conn"][$object_id]))
			{
				$cfp_dat = array(); //$GLOBALS["read_properties_data_cache_conn"][$object_id];
				foreach($GLOBALS["read_properties_data_cache_conn"][$object_id] as $_tmp => $d)
				{
					foreach($d as $_tmp2 => $d2)
					{
						$cfp_dat[] = $d2;
					}
				}
			}
			else
			{
				$q = "
					SELECT
						target,
						reltype
					FROM
						aliases
					LEFT JOIN objects ON objects.oid = aliases.target
					WHERE
						source = '".$object_id."' AND
						reltype IN (".join(",", map("'%s'", $conn_prop_fetch)).") AND
						objects.status != 0
				";
				$this->db_query($q);
				while ($row = $this->db_next())
				{
					$cfp_dat[] = $row;
				}
			}

			foreach($cfp_dat as $row)
			{
				$prop_name = array_search($row["reltype"], $conn_prop_fetch);
				if (!$prop_name)
				{
					error::raise(array(
						"id" => "ERR_NO_PROP",
						"msg" => sprintf(t("ds_mysql::read_properties(): no prop name for reltype %s in store=connect fetch! q = %s"), $row["reltype"], $q)
					));
				}

				$prop = $properties[$prop_name];
				if (!empty($prop["multiple"]))
				{
					$ret[$prop_name][$row["target"]] = $row["target"];
				}
				else
				{
					if (!isset($ret[$prop_name])) // just the first one
					{
						$ret[$prop_name] = $row["target"];
					}
				}
			}
		}

		return $ret;
	}


	// parameters:
	//	properties - property array
	//	tableinfo - tableinfo from propreader
	//  object_id - array of object id's
	// class_id - class id
	// full - bool, true - read objdata, false - just tables
	private function get_read_properties_sql($arr)
	{
		extract($arr);
		$ret = array();

		// then read the properties from the db
		// find all the tables that the properties are in
		$tables = array();
		$tbl2prop = array();
		$objtblprops = array();
		$conn_prop_fetch = array();
		foreach(safe_array($properties) as $prop => $data)
		{
			if ($data["store"] === "no")
			{
				continue;
			}

			if (empty($data["table"]))
			{
				$data["table"] = "objects";
			}

			if ($data["store"] === "connect")
			{
				// resolve reltype and do find_connections
				$_co_reltype = $data["reltype"];
				$_co_reltype = $GLOBALS["relinfo"][$class_id][$_co_reltype]["value"];
				$conn_prop_fetch[$data["name"]] = $_co_reltype;
			}

			if ($data["table"] !== "objects")
			{
				$tables[$data["table"]] = $data["table"];
				if ($data["store"] !== "no")
				{
					$tbl2prop[$data["table"]][] = $data;
				}
			}
			else
			{
				$objtblprops[] = $data;
			}
		}

		$fields = array();
		// do a query for each table
		foreach($tables as $table)
		{
			$_got_fields = array();
			foreach($tbl2prop[$table] as $prop)
			{
				if ($prop['field'] === "meta" && $prop["table"] === "objects")
				{
					$prop['field'] = "metadata";
				}

				if ($prop["method"] === "serialize")
				{
					if (!array_key_exists($prop["field"], $_got_fields))
					{
						$fields[] = $table.".`".$prop["field"]."` AS `".$prop["field"]."`";
						$_got_fields[$prop["field"]] = true;
					}
				}
				else
				if (ifset($prop, 'type') === 'range') // range support by dragut
				{
					$fields[] = $table.".`".$prop["field"]."_from` AS `".$prop["name"]."_from`";
					$fields[] = $table.".`".$prop["field"]."_to` AS `".$prop["name"]."_to`";
				}
				else
				if ($prop["method"] === "bitmask")
				{
					$fields[] = " (".$table.".`".$prop["field"]."` & ".$prop["ch_value"].") AS `".$prop["name"]."`";
				}
				else
				if ($prop["store"] !== "connect")	// must not try to read store=connect fields at all, since they don't have to exist!
				{
					$fields[] = $table.".`".$prop["field"]."` AS `".$prop["name"]."`";
				}
			}
		}

		if ($full)
		{
			$q = "SELECT
				objects.oid as oid,
				objects.parent as parent,
				objects.name as name,
				objects.createdby as createdby,
				objects.class_id as class_id,
				objects.created as created,
				objects.modified as modified,
				objects.status as status,
				objects.hits as hits,
				objects.lang_id as lang_id,
				objects.comment as comment,
				objects.last as last,
				objects.modifiedby as modifiedby,
				objects.jrk as jrk,
				objects.visible as visible,
				objects.period as period,
				objects.alias as alias,
				objects.periodic as periodic,
				objects.site_id as site_id,
				objects.brother_of as brother_of,
				objects.metadata as metadata,
				objects.subclass as subclass,
				objects.flags as flags,
				objects.acldata as acldata";

			if (count($objtblprops))
			{
				foreach($objtblprops as $objtblprop)
				{
					if ($objtblprop["method"] === "bitmask")
					{
						$q .= ",\n(objects.`{$objtblprop["field"]}` & {$objtblprop["ch_value"]}) AS `{$objtblprop["name"]}`";
					}
				}
			}

			if (count($fields) > 0)
			{
				$joins = "";
				foreach($tables as $table)
				{
					$joins .= " LEFT JOIN {$table} ON objects.brother_of = {$table}.`{$tableinfo[$table]["index"]}` ";
				}
				$q .= "," . implode(",", $fields) . " FROM objects {$joins} WHERE objects.oid";
			}
			else
			{
				$q .= " FROM objects WHERE oid";
			}
		}
		elseif (count($fields) > 0)
		{
			$table = reset($tables);
			$from = " FROM {$table} ";
			$o_t = $table;
			while($table = each($tables))
			{
				$from .= " LEFT JOIN {$table} ON {$o_t}.`{$tableinfo[$o_t]["index"]}` = {$table}.`{$tableinfo[$table]["index"]}` ";
			}
			$q = "SELECT " . implode(",", $fields) . " {$from} WHERE `{$tableinfo[$table]["index"]}`";
		}

		if (!$full)
		{
			if (is_array($object_id))
			{
				$q .= " IN (" . implode(",", $object_id) . ")";
			}
			else
			{
				$q .= "={$object_id}";
			}
		}

		$q2 = null;
		if (count($conn_prop_fetch))
		{
			if (is_array($object_id))
			{
				$source = "source IN (".join(",", map("'%s'", $object_id)).")";
			}
			else
			{
				$source  = "source = '".$object_id."'";
			}
			$q2 = "
				SELECT
					source,
					target,
					reltype,
					objects.name as target_name
				FROM
					aliases
				LEFT JOIN objects ON objects.oid = aliases.target
				WHERE
					$source AND
					reltype IN (".join(",", map("'%s'", $conn_prop_fetch)).") AND
					objects.status != 0
			";
		}

		return array("q" => $q, "q2" => $q2, "conn_prop_fetch" => $conn_prop_fetch);
	}


	// creates new, empty object
	// params:
	//	properties - prop array from propreader
	//	objdata - object data from objtable
	//	tableinfo - tableinfo from prop reader
	// returns:
	//	new oid

	function create_new_object($arr)
	{
		// add default values to metadata as well
		foreach($arr["properties"] as $prop => $data)
		{
			if (empty($data["table"]))
			{
				continue;
			}

			if ($data["table"] === "objects" && $data["field"] === "meta" && !isset($arr["objdata"]["meta"][$data["name"]]) && !empty($data["default"]))
			{
				$arr["objdata"]["meta"][$data["name"]] = $data["default"];
			}
		}
		extract($arr);

		$metadata = aw_serialize(ifset($objdata, "meta"), SERIALIZE_NATIVE);
		$this->quote($metadata);
		$this->quote($objdata);
		// insert default new acl to object table here
		$acld_fld = $acld_val = "";
		$n_acl_data = null;
		if (aw_ini_get("acl.use_new_acl") && !empty($_SESSION["uid"]) && is_oid(aw_global_get("uid_oid")))
		{
			$uo = obj(aw_global_get("uid_oid"));
			$g_d = $uo->get_default_group();

			$n_acl_data = array(
				$g_d => acl_base::get_acl_value_n(acl_base::acl_get_default_acl_arr())
			);

			$acld_fld = ",acldata";
			$acld_val = aw_serialize($n_acl_data, SERIALIZE_NATIVE);
			$this->quote($acld_val);
			$acld_val = ",'".$acld_val."'";
		}

		// Initialize, to avoid PHP notices
		$objdata = array_merge(array(
			"parent" => NULL,
			"class_id" => NULL,
			"name" => NULL,
			"created" => NULL,
			"createdby" => NULL,
			"modified" => NULL,
			"modifiedby" => NULL,
			"status" => NULL,
			"site_id" => NULL,
			"hits" => NULL,
			"lang_id" => NULL,
			"comment" => NULL,
			"jrk" => NULL,
			"period" => NULL,
			"alias" => NULL,
			"periodic" => NULL,
			"subclass" => NULL,
			"flags" => NULL,
		), $objdata);

		// create oid
		$q = sprintf("
			INSERT INTO objects (
				parent,						class_id,						name,						createdby,
				created,					modified,						status,						site_id,
				hits,						lang_id,						comment,					modifiedby,
				jrk,						period,							alias,						periodic,
				metadata,						subclass,					flags
				$acld_fld
		) VALUES (
				%d,							%d,								'%s',						'%s',
				%d,							%d,								%d,							%d,
				%d,							%d,								'%s',						'%s',
				%d,							%d,								'%s',						%d,
				'%s',						%d,								%d
				{$acld_val}
		)",		$objdata["parent"],		$objdata["class_id"],				$objdata["name"],			$objdata["createdby"],
				$objdata["created"],	$objdata["modified"],				$objdata["status"],			$objdata["site_id"],
				$objdata["hits"],		$objdata["lang_id"],				$objdata["comment"],		$objdata["modifiedby"],
				$objdata["jrk"],		$objdata["period"],					$objdata["alias"],			$objdata["periodic"],
				$metadata,				$objdata["subclass"],				$objdata["flags"]
		);
		//echo "q = <pre>". htmlentities($q)."</pre> <br />";

		$this->db_query($q);
		$oid = $this->db_last_insert_id();

		if (!aw_ini_get("acl.use_new_acl_final"))
		{
			// create all access for the creator
			acl_base::create_obj_access($oid);
		}
		// set brother to self if not specified.
		if (empty($objdata["brother_of"]))
		{
			$this->db_query("UPDATE objects SET brother_of = oid WHERE oid = $oid");
		}

		// put into cache to avoid query for the same object's data in the can() a few lines down
		$tmp = $objdata;
		$tmp["acldata"] = $n_acl_data;
		$GLOBALS["__obj_sys_objd_memc"][$oid] = $tmp;
		$this->can("admin", $oid);

		// hits
		$this->db_query("INSERT INTO hits(oid,hits,cachehits) VALUES($oid, 0, 0 )");

		// now we need to create entries in all tables that are in properties as well.
		$tbls = array();
		foreach($properties as $prop => $data)
		{
			if (empty($data["table"]))
			{
				continue;
			}

			if ($data["table"] === "objects")
			{
				continue;
			}

			if ($data["store"] !== "no" && $data["store"] !== "connect")
			{
				$tbls[$data["table"]]["index"] = $tableinfo[$data["table"]]["index"];
				// check if the property has a value
				if (isset($objdata["properties"][$prop]))
				{
					// if the prop is in a serialized field, then respect that
					if ($data["method"] === "serialize")
					{
						// unpack field, add value, repack field
						$_field_val = aw_unserialize($tbls[$data["table"]]["defaults"][$data["field"]], false, true);
						$_field_val[$prop] = $objdata["properties"][$prop];
						$tbls[$data["table"]]["defaults"][$data["field"]] = aw_serialize($_field_val, SERIALIZE_NATIVE);
					}
					else
					{
						$tbls[$data["table"]]["defaults"][$data["field"]] = $objdata["properties"][$prop];
					}
				}
				else
				{
					if ($data["method"] !== "serialize")
					{
						$tbls[$data["table"]]["defaults"][$data["field"]] = isset($data["default"]) ? $data["default"] : null;
					}
					else
					{
						$_field_val = isset($tbls[$data["table"]]["defaults"][$data["field"]]) ? aw_unserialize($tbls[$data["table"]]["defaults"][$data["field"]], false, true) : "";
						$_field_val[$prop] = isset($data["default"]) ? $data["default"] : "";
						$tbls[$data["table"]]["defaults"][$data["field"]] = aw_serialize($_field_val, SERIALIZE_NATIVE);
					}
				}

				if (ifset($data, "datatype") === "int" && $tbls[$data["table"]]["defaults"][$data["field"]] == "")
				{
					$tbls[$data["table"]]["defaults"][$data["field"]] = "0";
				}
			}
		}

		foreach($tbls as $tbl => $dat)
		{
			$idx = $dat["index"];
			$fds = "";
			$vls = "";
			if (is_array($dat["defaults"]))
			{
				foreach($dat["defaults"] as $fd => $vl)
				{
					$this->quote($vl);
					if($vl !== NULL && $vl !== "")
					{
						$fds .= ",`".$fd."`";
						$vls .= ",'".$vl."'";
					}
				}
			}

			$q = "INSERT INTO {$tbl} ({$idx}{$fds}) VALUES('{$oid}'{$vls})";
			$this->db_query($q);
		}

		$this->create_new_object_cache_update(null);

		return $oid;
	}

	function create_new_object_cache_update($oid)
	{
		// we need to clear the html cache here, not in ds_cache, because ds_cache can be not loaded
		// even when html caching is turned on

		if (!object_loader::opt("no_cache"))
		{
			cache::file_clear_pt("html");
		}
	}

	// saves object properties, including all object table fields,
	// just stores the data, does not update or check it in any way,
	// except for db quoting of course
	// params:
	//	properties - prop array from propreader
	//	objdata - object data from objtable
	//	tableinfo - tableinfo from prop reader
	//	propvalues - property values
	function save_properties($arr)
	{
		extract($arr);
		$objdata = $arr["objdata"];
		if ($arr["create_new_version"] == 1)
		{
			return $this->save_properties_new_version($arr);
		}

		if (!empty($GLOBALS["object2version"][$objdata["oid"]]))
		{
			$objdata["version_id"] = $GLOBALS["object2version"][$objdata["oid"]];
			return $this->save_properties_new_version($arr);
		}

		$metadata = aw_serialize((isset($objdata["meta"]) ? $objdata["meta"] : ""), SERIALIZE_NATIVE);
		$this->quote($metadata);
		$this->quote($objdata);
		$objdata["metadata"] = $metadata;

		if ($objdata["brother_of"] == 0)
		{
			$objdata["brother_of"] = $objdata["oid"];
		}

		$ot_sets = array();
		if (!isset($arr["ot_modified"]))
		{
			$arr["ot_modified"] = $GLOBALS["object_loader"]->all_ot_flds;
		}

		foreach(safe_array($arr["ot_modified"]) as $_field => $one)
		{
			$ot_sets[] = " {$_field} = '{$objdata[$_field]}' ";
		}

		$ot_sets = join(" , ", $ot_sets);

		if ($ot_sets)
		{
			$ot_sets = " , {$ot_sets}";
		}

		$obj_q = "UPDATE objects SET
			mod_cnt = mod_cnt + 1
			{$ot_sets}
			WHERE oid = '{$objdata["oid"]}'
		";

		// now save all properties


		$data_qs = array();
		$used_tables = array("objects" => "objects");

		// divide all properties into tables
		$tbls = array();
		foreach($properties as $prop => $data)
		{
			if ($data["store"] !== "no" && $data["store"] !== "connect")
			{
				$tbls[$data["table"]][] = $data;
			}
		}

		// remove all props that are not supposed to be saved
		if (isset($arr["props_modified"]))
		{
			// get a list of all table fields that have modified props in them
			// and include all props that are modified that are written to those
			// cause if we do not do that, it breaks serialized fields with several props
			$mod_flds = array();
			foreach(safe_array($arr["props_modified"]) as $_pn => $_one)
			{
				if (!empty($properties[$_pn]["table"]) and !empty($properties[$_pn]["field"]))
				{
					$mod_flds[$properties[$_pn]["table"]][$properties[$_pn]["field"]] = 1;
				}
			}

			$tmp = array();
			foreach($tbls as $tbl => $tbld)
			{
				foreach($tbld as $idx => $prop)
				{
					if (!empty($arr["props_modified"][$prop["name"]]) || isset($mod_flds[$prop["table"]][$prop["field"]]))
					{
						$tmp[$tbl][$idx] = $prop;
					}
				}
			}
			$tbls = $tmp;
		}

		// now save all props to tables.
		foreach($tbls as $tbl => $tbld)
		{
			if (empty($tbl))
			{
				continue;
			}

			if ($tbl === "objects")
			{
				if ($objdata["oid"] == $objdata["brother_of"])
				{
					continue; // no double save. but if this is brother, then meta need to be saved
				}
				$tableinfo[$tbl]["index"] = "oid";
				$serfs["metadata"] = $objdata["meta"];
			}
			else
			{
				$serfs = array();
			}

			$seta = array();
			foreach($tbld as $prop)
			{
				// this check is here, so that we won't overwrite default values, that are saved in create_new_object
				if (isset($propvalues[$prop['name']]))
				{
					if ($prop["type"] === "datagrid")
					{
						continue;
					}

					if ($prop['method'] === "serialize")
					{
						if ($prop['field'] === "meta" && $prop["table"] === "objects")
						{
							$prop['field'] = "metadata";
						}
						// since serialized properites can be several for each field, gather them together first
						$serfs[$prop['field']][$prop['name']] = $propvalues[$prop['name']];
					}
					elseif ($prop['method'] === "bitmask")
					{
						$val = $propvalues[$prop["name"]];

						if (!isset($seta[$prop["field"]]))
						{
							// jost objects.flags support for now
							$seta[$prop["field"]] = isset($objdata["flags"]) ? $objdata["flags"] : null;
						}

						// make mask for the flag - mask value is the previous field value with the
						// current flag bit(s) set to zero. flag bit(s) come from prop[ch_value]
						$mask = $seta[$prop["field"]] & (~((int)$prop["ch_value"]));
						// add the value
						$mask |= $val;

						$seta[$prop["field"]] = $mask;
					}
					elseif ($prop['type'] === 'range') // range support by dragut
					{
						$seta[$prop['field'].'_from'] = (int)$propvalues[$prop['name']]['from'];
						$seta[$prop['field'].'_to'] = (int)$propvalues[$prop['name']]['to'];
					}
					else
					{
						$str = $propvalues[$prop["name"]];
						$this->quote($str);
						$seta[$prop["field"]] = $str;
					}

					if (isset($prop["datatype"]) && $prop["datatype"] === "int" && empty($seta[$prop["field"]]))
					{
						$seta[$prop["field"]] = "0";
					}
				}
			}

			foreach($serfs as $field => $dat)
			{
				$str = aw_serialize($dat, SERIALIZE_NATIVE);
				$this->quote($str);
				$seta[$field] = $str;
			}

			// actually, this is a bit more complicated here - if this is a brother
			// and the table is the objects table, then we must ONLY write the metadata field
			// to the original object. because if we write all, then ot fields will be the same for the brother and the original
			// always. and that's not good.
			if ($tbl === "objects" && $objdata["brother_of"] != $objdata["oid"])
			{
				$seta = array("metadata" => $seta["metadata"]);
			}
			$sets = join(",",map2("`%s` = '%s'",$seta,0,true));
			if ($sets != "")
			{
				$insert_q_fields = "`" . implode("`,`", (array_merge(array($tableinfo[$tbl]["index"]), array_keys($seta)))) . "`";
				$insert_q_values = "'" . implode("','", (array_merge(array($objdata["brother_of"]), array_values($seta)))) . "'";
				$q = "INSERT INTO {$tbl} ({$insert_q_fields}) VALUES ({$insert_q_values}) ON DUPLICATE KEY UPDATE {$sets}"; // insert new row into data table or update if exists
				// $q = "UPDATE {$tbl} SET {$sets} WHERE {$tableinfo[$tbl]["index"]} = '{$objdata["brother_of"]}'"; // reserve for performance upgrade when other means of row existence checking available

				$used_tables[$tbl] = $tbl;
				$data_qs[] = $q;
			}
		}

		// make datagrid inserts
		foreach($tbls as $tbl => $tbld)
		{
			if ($tbl == "")
			{
				continue;
			}

			foreach($tbld as $prop)
			{
				if ($prop["type"] === "datagrid")
				{
					$data_qs[] = "DELETE FROM ".$prop["table"]." WHERE ".$tableinfo[$prop["table"]]["index"]." = ".$objdata["oid"];
					// insert data back
					$data = $propvalues[$prop["name"]];
					if (is_array($data))
					{
						foreach($data as $idx => $data_row)
						{
							$tmp = array();
							foreach($prop["fields"] as $field_name)
							{
								$this->quote($data_row[$field_name]);
								$tmp[$field_name] = $data_row[$field_name];
							}

							if ($idx < 1)
							{
								$data_qs[] = "INSERT INTO ".$prop["table"]."(".$tableinfo[$prop["table"]]["index"].",".join(",", map('`%s%``', $prop["fields"])).") VALUES(".$objdata["oid"].",".join(",", map("'%s'", $tmp)).") ";
							}
							else
							{
								$data_qs[] = "INSERT INTO ".$prop["table"]."(".$prop["field"].",".$tableinfo[$prop["table"]]["index"].",".join(",", map('`%s%``', $prop["fields"])).") VALUES(".$idx.",".$objdata["oid"].",".join(",", map("'%s'", $tmp)).") ";
							}
						}
					}
				}
			}
		}

		// check exclusivity
		if (!empty($arr["exclusive_save"]))
		{
			// lock tables and check mod count
			$this->db_query("LOCK TABLES ".join(" , ", map(" %s WRITE ", $used_tables)));
			$db_mod_cnt = (int) $this->db_fetch_field("SELECT mod_cnt FROM objects WHERE oid = '".$objdata["oid"]."'", "mod_cnt");
			if ($db_mod_cnt !== $arr["current_mod_count"])
			{
				// unlock tables and except
				$this->db_query("UNLOCK TABLES");
				throw new awex_obj_modified_by_others(sprintf("Object '%s' state mismatch (theirs: %s, ours: %s)", $objdata["oid"], $db_mod_cnt, $arr["current_mod_count"]));
				return;
			}
			// not modified, go save
		}

		$this->db_query($obj_q);
		foreach($data_qs as $q)
		{
			$this->db_query($q);
		}

		if (!empty($arr["exclusive_save"]))
		{
			// un lock tables
			$this->db_query("UNLOCK TABLES");
		}

		if(isset($GLOBALS["__obj_sys_objd_memc"][$objdata["brother_of"]]))
		{
			unset($GLOBALS["__obj_sys_objd_memc"][$objdata["brother_of"]]);
		}

		if(isset($GLOBALS["__obj_sys_objd_memc"][$objdata["oid"]]))
		{
			unset($GLOBALS["__obj_sys_objd_memc"][$objdata["oid"]]);
		}

		unset($this->read_properties_data_cache[$objdata["oid"]]);
		unset($this->read_properties_data_cache[$objdata["brother_of"]]);
		$this->save_properties_cache_update($objdata["oid"]);
	}

	function save_properties_cache_update($oid)
	{
		if (!object_loader::opt("no_cache"))
		{
			cache::file_clear_pt("html");
		}
	}

	function read_connection($id)
	{
		return $this->db_fetch_row("
			SELECT
				".$this->connection_query_fetch()."
			FROM
				aliases a
				LEFT JOIN objects o_s ON o_s.oid = a.source
				LEFT JOIN objects o_t ON o_t.oid = a.target
			WHERE
				id = $id
		");
	}

	function save_connection($data)
	{
		$data = array_merge(array(
			"source" => null,
			"target" => null,
			"type" => null,
			"data" => null,
			"idx" => null,
			"cached" => null,
			"relobj_id" => null,
			"reltype" => null,
			"pri" => null
		), $data);

		if (empty($data["type"]))
		{
			if (isset($GLOBALS["objects"][$data["to"]]))
			{
				$data["type"] = $GLOBALS["objects"][$data["to"]]->class_id();
			}
			else
			{
				$data["type"] = $this->db_fetch_field("SELECT class_id FROM objects WHERE oid = '".$data["to"]."'", "class_id");
			}
		}

		if (empty($data["id"]))
		{
			// we don't need the index if the connection has a reltype, cause the index is only used for aliases
			if (empty($data["idx"]) && empty($data["reltype"]))
			{
				$q = "SELECT MAX(idx) as idx FROM aliases where source = '$data[from]' and type = '$data[type]'";
				$data["idx"] = $this->db_fetch_field($q, "idx")+1;
			}
			$q = sprintf("INSERT INTO aliases (
				source,						target,					type,					data,
				idx,						cached,					relobj_id,				reltype,
				pri
			) VALUES(
				%u,							%u,						%d,						'%s',
				%d,							%d,						%u,						%u,
				%u
			)",
				$data["from"],				$data["to"],			$data["type"],			$data["data"],
				$data["idx"],				$data["cached"],		$data["relobj_id"],		$data["reltype"],
				$data["pri"]
			);
			$this->db_query($q);
			$data['id'] = $this->db_last_insert_id();
		}
		else
		{
			$q = sprintf("UPDATE aliases SET
				source = %u,		target = %u,		type = %d,			data = '%s',	idx = %d,
				cached = %d,		relobj_id = %u,		reltype = %u,		pri = %u
			WHERE
				id = %u",
				$data["from"],		$data["to"],		$data["type"],		$data["data"],	$data["idx"],
				$data["cached"],	$data["relobj_id"], $data["reltype"],	$data["pri"],
				$data["id"]
			);
			$this->db_query($q);
		}
		$this->save_connection_cache_update(null);

		return $data['id'];
	}

	function save_connection_cache_update($oid)
	{
		if (!object_loader::opt("no_cache"))
		{
			cache::file_clear_pt("html");
		}
	}

	function delete_connection($id)
	{
		$this->db_query("DELETE FROM aliases WHERE id = '$id'");
		$this->delete_connection_cache_update($id);
	}

	function delete_connection_cache_update($oid)
	{
		if (!object_loader::opt("no_cache"))
		{
			cache::file_clear_pt("html");
		}
	}

	function connection_query_fetch()
	{
		$ret = "a.id as `id`,
				a.source as `from`,
				a.target as `to`,
				a.type as `type`,
				a.data as `data`,
				a.idx as `idx`,
				a.cached as `cached`,
				a.relobj_id as `relobj_id`,
				a.reltype as `reltype`,
				a.pri as pri,
				o_t.lang_id as `to.lang_id`,
				o_s.lang_id as `from.lang_id`,
				o_t.flags as `to.flags`,
				o_s.flags as `from.flags`,
				o_t.modified as `to.modified`,
				o_s.modified as `from.modified`,
				o_t.modifiedby as `to.modifiedby`,
				o_s.modifiedby as `from.modifiedby`,
				o_t.name as `to.name`,
				o_s.name as `from.name`,
				o_t.class_id as `to.class_id`,
				o_s.class_id as `from.class_id`,
				o_t.jrk as `to.jrk`,
				o_s.jrk as `from.jrk`,
				o_t.status as `to.status`,
				o_s.status as `from.status`,
				o_t.parent as `to.parent`,
				o_s.parent as `from.parent`,
				o_t.comment as `to.comment`,
				o_s.comment as `from.comment`,
				o_t.acldata as `to.acldata`,
				o_s.acldata as `from.acldata`
		";

		if ($GLOBALS["cfg"]["acl"]["use_new_acl"])
		{
			$ret .= ",o_t.acldata as `to.acldata`,o_s.acldata as `from.acldata`";
		}
		return $ret;
	}

	// arr - { [from], [to], [type], [class], [to.obj_table_field], [from.obj_table_field] }
	function find_connections($arr)
	{
		$sql = "
			SELECT
				".$this->connection_query_fetch()."
			FROM
				aliases a
				LEFT JOIN objects o_s ON o_s.oid = a.source
				LEFT JOIN objects o_t ON o_t.oid = a.target
			WHERE
				o_s.status != 0 AND
				o_t.status != 0
		";

		if (!empty($arr["from"]))
		{
			$awa = new aw_array($arr["from"]);
			$sql .= " AND source IN (".$awa->to_sql().") ";
		}

		if (!empty($arr["to"]))
		{
			$awa = new aw_array($arr["to"]);
			$sql .= " AND target IN (".$awa->to_sql().") ";
		}

		if (!empty($arr["type"]))
		{
			$awa = new aw_array($arr["type"]);
			$sql .= " AND reltype IN (".$awa->to_sql().") ";
		}

		if (!empty($arr["class"]))
		{
			$awa = new aw_array($arr["class"]);
			$sql .= " AND type IN (".$awa->to_sql().") ";
		}

		if (!empty($arr["relobj_id"]))
		{
			$awa = new aw_array($arr["relobj_id"]);
			$sql .= " AND relobj_id IN (".$awa->to_sql().") ";
		}

		if (!empty($arr["idx"]))
		{
			$awa = new aw_array($arr["idx"]);
			$sql .= " AND idx IN (".$awa->to_sql().") ";
		}

		foreach($arr as $k => $v)
		{
			if (substr($k, 0, 3) === "to.")
			{
				if (is_array($v))
				{
					$sql .= " AND o_t.".substr($k, 3)." IN (" . join(",",$v) . ") ";
				}
				else
				{
					if (strpos($v, "%") !== false)
					{
						$sql .= " AND o_t.".substr($k, 3)." LIKE '$v' ";
					}
					else
					{
						$sql .= " AND o_t.".substr($k, 3)." = '$v' ";
					}
				}
			}
			if (substr($k, 0, 5) === "from.")
			{
				if (is_array($v))
				{
					$sql .= " AND o_s.".substr($k, 5)." IN (" . join(",",$v) . ") ";
				}
				else
				{
					if (strpos($v, "%") !== false)
					{
						$sql .= " AND o_s.".substr($k, 5)." LIKE '$v' ";
					}
					else
					{
						$sql .= " AND o_s.".substr($k, 5)." = '$v' ";
					}
				}
			}
		}

	//	$sql .= " ORDER BY a.id ";

		$this->db_query($sql);
		$ret = array();
		while ($row = $this->db_next())
		{
			$row["from.acldata"] = aw_unserialize($row["from.acldata"], false, true);
			$row["to.acldata"] = aw_unserialize($row["to.acldata"], false, true);
			$ret[$row["id"]] = $row;
		}

		ksort($ret);
		return $ret;
	}

	// params:
	//	array of filter parameters
	// if class id is present, properties can also be filtered, otherwise only object table fields
	function search($params, $to_fetch = NULL)
	{
		$this->used_tables = array();

		$this->properties = array();
		$this->tableinfo = array();

		// load property defs and table defs
		$this->class_id = null;
		if (isset($params["class_id"]))
		{
			$this->_do_add_class_id($params["class_id"]);
			$this->class_id = $params["class_id"];
			if (is_array($this->class_id))
			{
				$this->class_id = reset($this->class_id);
			}
		}

		$this->stat = false;
		$this->sby = "";
		$this->limit = "";
		$this->has_lang_id = false;

		$this->meta_filter = array();
		$this->alias_joins = array();
		$this->done_ot_js = array();
		$this->joins = array();

		// this contains the full names of all the tables used in any part of the sql ( fetch, join, where) so that we ca use this to leave out unused tables from the resulting query.
		// this could of course be done during query construction, but it is much much harder to do it there, so we do it as a post-process step.
		$this->search_tables_used = array("objects" => 1);

		$this->has_data_table_filter = false;
		list($fetch_sql, $fetch_props, $fetch_metafields, $has_sql_func, $multi_fetch_fields) = $this->_get_search_fetch($to_fetch, $params);

		// set fetch sql as member, so that req_make_sql can rewrite
		// in it fetch columns that are not known yet from data fetch, that get search fetch puts in it
		$this->current_fetch_sql = &$fetch_sql;
		$where = $this->req_make_sql($params);

		if (!$this->stat)
		{
			$where .= ($where != "" ? " AND\n " : "")." objects.status > 0 ";
		}

		$joins = $this->_get_joins($params);

		// now, optimize out the joins that are not needed
		$joins = $this->_optimize_joins($joins, $this->search_tables_used);

		$ret = array();
		if ($where != "")
		{
			$acld = "";
			if (aw_ini_get("acl.use_new_acl"))
			{
				$acld = ", objects.acldata as acldata, objects.parent as parent";
			}

			$datafetch = true;
			if ($fetch_sql == "")
			{
				$fetch_sql = "
					objects.oid as oid,
					objects.jrk as jrk,
					objects.name as name,
					objects.parent as parent,
					objects.brother_of as brother_of,
					objects.status as status,
					objects.class_id as class_id
					{$acld}
				";
				$datafetch = false;
			}

			$gpb = "";
			if ($this->limit != "")
			{
				// this is here for a quite complicated, but unfortunately, quite necessary reason:
				// if you do a search that searches through several relations and several of them match
				// then you would get several rows for each object
				// and thus you would get less than the limit amount of objects
				// which, in the sql sense is quite falid, since joins are cross products on data sets
				// but in the gimme-a-list-of-objects-with-those-props is not
				// so we solve this by making sure we only get separate objects in the result set.
				// we do this by adding the group by clause here.
				// it slows things by quite a bit, but unfortunately, it is the only way to avoid this.
				$gpb = "GROUP BY objects.oid";
			}

			$q = "
				SELECT
					{$fetch_sql}
				FROM
					{$joins}
				WHERE
					{$where} {$gpb} {$this->sby} {$this->limit}";

			$acldata = array();
			$parentdata = array();
			$objdata = array();

			$this->db_query($q);
			$this->last_search_query_string = $q;

			if ($datafetch)
			{
				$fetch_first_el = reset($to_fetch);
				$single_element_result_key = (count($to_fetch) === 1 and !is_array($fetch_first_el)) ? $fetch_first_el : "";
				$ret2 = array();
				$local_memory_limit = 0.95 * aw_bytes_string_to_int(ini_get("memory_limit"));
				while($row = $this->db_next())
				{
					// send alert when a too large result set
					if (memory_get_usage() > $local_memory_limit)
					{
						error::raise(array(
							"id" => "ERR_RESULT_TOO_LARGE",
							"silent" => true,
							"fatal" => false,
							"msg" => "Result set is too large, memory limit near"
						));
					}

					if (!$has_sql_func && count($multi_fetch_fields) && isset($ret2[$row["oid"]]))
					{
						// add the multi field values as arrays
						foreach($multi_fetch_fields as $field)
						{
							if (!is_array($ret2[$row["oid"]][$field]))
							{
								$ret2[$row["oid"]][$field] = array($ret2[$row["oid"]][$field] => $ret2[$row["oid"]][$field]);
							}
							$ret2[$row["oid"]][$field][$row[$field]] = $row[$field];
						}
						continue;
					}

					// process metafields
					foreach($fetch_metafields as $f_mf => $f_keys)
					{
						$f_unser = aw_unserialize($row[$f_mf], false, true);
						foreach($f_keys as $f_key_name)
						{
							if (isset($f_unser[$f_key_name]))
							{
								$row[$f_key_name] = $f_unser[$f_key_name];
							}
							else
							{
								$row[$f_key_name] = null;
							}
						}
						unset($row[$f_mf]);
					}

					if ($has_sql_func)
					{
						$ret2[] = $row;
					}
					else
					{
						foreach($multi_fetch_fields as $field)
						{
							if (!is_array($row[$field]))
							{
								if (empty($row[$field]))
								{
									$row[$field] = array();
								}
								else
								{
									$row[$field] = array($row[$field] => $row[$field]);
								}
							}
						}

						if ($single_element_result_key)
						{
							$ret2[$row["oid"]] = $row[$single_element_result_key];
						}
						else
						{
							$ret2[$row["oid"]] = $row;
						}
					}

					if(isset($row["oid"]))
					{
						$ret[$row["oid"]] = $row["name"];

						if (isset($row["parent"]))
						{
							$parentdata[$row["oid"]] = $row["parent"];
						}

						$objdata[$row["oid"]] = array(
							"brother_of" => isset($row["brother_of"]) ? $row["brother_of"] : null,
							"status" => isset($row["status"]) ? $row["status"] : null,
							"class_id" => isset($row["class_id"]) ? $row["class_id"] : null,
							"jrk" => isset($row["jrk"]) ? $row["jrk"] : null
						);

						if ($GLOBALS["cfg"]["acl"]["use_new_acl"] && isset($row["acldata"]))
						{
							$row["acldata"] = safe_array(aw_unserialize($row["acldata"], false, true));
							$acldata[$row["oid"]] = $row;
						}

						$GLOBALS["__obj_sys_acl_memc"][$row["oid"]] = array(
							"status" => isset($row["status"]) ? $row["status"] : null,
							"brother_of" => isset($row["brother_of"]) ? $row["brother_of"] : null,
							"acldata" => isset($row["acldata"]) ? $row["acldata"] : null,
							"parent" => isset($row["parent"]) ? $row["parent"] : null
						);
					}
				}

				return array($ret, $this->meta_filter, $acldata, $parentdata, $objdata, $ret2, $has_sql_func);
			}
			else
			{
				while ($row = $this->db_next())
				{
					$ret[$row["oid"]] = $row["name"];
					$parentdata[$row["oid"]] = $row["parent"];
					$objdata[$row["oid"]] = array(
						"brother_of" => $row["brother_of"],
						"status" => $row["status"],
						"class_id" => $row["class_id"],
						"jrk" => $row["jrk"],
					);
					if ($GLOBALS["cfg"]["acl"]["use_new_acl"])
					{
						$row["acldata"] = safe_array(aw_unserialize($row["acldata"], false, true));
						$acldata[$row["oid"]] = $row;
					}
				}
			}
		}

		return array($ret, $this->meta_filter, $acldata, $parentdata, $objdata);
	}

	function delete_object($oid)
	{
		$this->db_query("UPDATE objects SET status = '".STAT_DELETED."', modified = ".time().",modifiedby = '".aw_global_get("uid")."' WHERE oid = '$oid'");
		//$this->db_query("DELETE FROM aliases WHERE target = '$oid'");
		//$this->db_query("DELETE FROM aliases WHERE source = '$oid'");
		$this->delete_object_cache_update($oid);
	}

	function delete_object_cache_update($oid)
	{
		if (!object_loader::opt("no_cache"))
		{
			cache::file_clear_pt_oid("acl", $oid);
			cache::file_clear_pt("html");
			cache::file_clear_pt("menu_area_cache");
		}
	}

	function delete_multiple_objects($oid_list)
	{
		$awa = new aw_array($oid_list);
		$this->db_query("UPDATE objects SET status = '".STAT_DELETED."', modified = ".time().",modifiedby = '".aw_global_get("uid")."' WHERE oid IN(".$awa->to_sql().")");
		if (!object_loader::opt("no_cache"))
		{
			cache::file_clear_pt("acl");
			cache::file_clear_pt("html");
			cache::file_clear_pt("menu_area_cache");
		}
	}

	function final_delete_object($oid)
	{
		$clid = $this->db_fetch_field("SELECT class_id FROM objects WHERE oid = '{$oid}'", "class_id");
		if (!$clid)
		{
			error::raise(array(
				"id" => "ERR_NO_OBJECT",
				"msg" => sprintf(t("ds_mysql::final_delete_object(%s): no suct object exists!"), $oid)
			));
		}

		// load props by clid
		$cl = aw_ini_get("classes");
		$file = $cl[$clid]["file"];
		if ($clid == 29)
		{
			$file = "doc";
		}

		list($properties, $tableinfo, $relinfo) = $GLOBALS["object_loader"]->load_properties(array(
			"file" => basename($file),
			"clid" => $clid
		));

		$tableinfo = safe_array($tableinfo);
		$tableinfo["objects"] = array(
			"index" => "oid"
		);
		foreach($tableinfo as $tbl => $inf)
		{
			$sql = "DELETE FROM {$tbl} WHERE {$inf["index"]} = '{$oid}' LIMIT 1";
			$this->db_query($sql);
		}

		// also, aliases
		$this->db_query("DELETE FROM aliases WHERE source = '{$oid}' OR target = '{$oid}'");
		// hits, acl
		$this->db_query("DELETE FROM hits WHERE oid = '{$oid}'");
		if (!aw_ini_get("acl.use_new_acl_final"))
		{
			$this->db_query("DELETE FROM acl WHERE oid = '{$oid}'");
		}

		if (!object_loader::opt("no_cache"))
		{
			cache::file_clear_pt("acl");
			cache::file_clear_pt("html");
			cache::file_clear_pt("menu_area_cache");
		}
	}

	function req_make_sql($params, $logic = "AND", $dbg = false)
	{
		$sql = array();
		$p_tmp = $params;
		foreach($params as $key => $val)
		{
			if ($val === NULL)
			{
				continue;
			}

			if ("sort_by" === (string)($key))
			{
				// add to list of used tables
				$bits = explode(",", $val);
				foreach($bits as $bit)
				{
					if(strpos($bit, ".") !== false)
					{
						list($bit_tbl, $bit_field) = explode(".", $bit);
						if ($bit_tbl != "")
						{
							$this->_add_s($bit_tbl);
						}
					}
				}
				$this->quote($val);
				$this->sby = " ORDER BY {$val} ";
				continue;
			}

			if ("limit" === (string)($key))
			{
				$this->quote($val);
				$this->limit = " LIMIT {$val} ";
				continue;
			}
			elseif ("join_strategy" === (string)($key))
			{
				continue;
			}
			elseif ("status" === (string)($key))
			{
				$this->stat = true;
			}
			elseif ("lang_id" === (string)($key))
			{
				$this->has_lang_id = true;
			}

			$tbl = "objects";
			$fld = $key;

			// check for dots in key. if there are any, then we gots some join thingie
			$is_done = false;
			if (strpos($key, ".") !== false)
			{
				$_okey = $key;
				list($tbl, $fld) = $this->_do_proc_complex_param(array(
					"key" => &$key,
					"val" => $val,
					"params" => $p_tmp
				));
				if ($tbl === "__rewrite_prop")
				{
					$key = $fld;
				}
				else
				{
					$is_done = true;
				}

				$this->_add_s($tbl);
				// replace unknown columns in fetch sql
				$this->current_fetch_sql = str_replace("%%REPLACE({$_okey})%%", "{$tbl}.`{$key}`", $this->current_fetch_sql);
			}

			if (!$is_done && isset($this->properties[$key]) && $this->properties[$key]["store"] !== "no")
			{
				$tbl = $this->properties[$key]["table"];
				$fld = $this->properties[$key]["field"];
				if ($fld === "meta")
				{
					if ($this->properties[$key]["store"] !== "connect")
					{
						$this->meta_filter[$key] = $val;
						continue;
					}
				}
				elseif ($this->properties[$key]["method"] === "serialize")
				{
					error::raise(array(
						"id" => "ERR_FIELD",
						"msg" => sprintf(t("filter cannot contain properties (%s) that are in serialized fields other than metadata!"), $key)
					));
				}
				$this->used_tables[$tbl] = $tbl;
				$this->_add_s($tbl);
			}

			if ($tbl !== "objects")
			{
				$this->has_data_table_filter = true;
			}
			$tf = "{$tbl}.`{$fld}`";

			if (isset($this->properties[$key]["store"]) && $this->properties[$key]["store"] === "connect")
			{
				// join aliases as many-many relation and filter by that
				if ($tbl === "objects")
				{
					$idx = "brother_of";
				}
				else
				{
					$idx = $this->tableinfo[$tbl]["index"];
				}

				$this->alias_joins[$key] = array(
					"name" => "aliases_{$key}",
					"on" => "{$tbl}.{$idx} = aliases_{$key}.source AND aliases_{$key}.reltype=".$GLOBALS["relinfo"][$this->class_id][$this->properties[$key]["reltype"]]["value"]
				);
				$this->_add_s("aliases_{$key}");
			}

			if (isset($this->properties[$key]["store"]) && $this->properties[$key]["store"] === "connect" && $fld === "meta")
			{
				// figure out the joined alias table name and search from that
				$tbl = "aliases_{$key}";
				$fld = "target";
				$tf = "{$tbl}.`{$fld}`";
				$this->_add_s($tbl);
			}

			if (is_array($val) && ((isset($this->properties[$key]["method"]) && $this->properties[$key]["method"] === "bitmask") || $key === "flags"))
			{
				$sql[] = "{$tf} & {$val["mask"]} = ".((int)$val["flags"]);
			}
			elseif (!is_array($val) && isset($this->properties[$key]) && ($this->properties[$key]["method"] === "bitmask") && $this->properties[$key]["ch_value"] > 0)
			{
				if (is_object($val))
				{
					switch(get_class($val))
					{
						case "obj_predicate_not":
							$sql[] = $tf." & ".((int)$this->properties[$key]["ch_value"])." != ".((int)$this->properties[$key]["ch_value"]);
							break;

						case "obj_predicate_compare":
							$v_data = $val->data;
							if ($val->data instanceof aw_array)
							{
								$v_data = $v_data->get();
							}

							$comparator = "";
							switch($val->comparator)
							{
								case obj_predicate_compare::LESS:
									$comparator = " < ";
									break;

								case obj_predicate_compare::GREATER:
									$comparator = " > ";
									break;

								case obj_predicate_compare::LESS_OR_EQ:
									$comparator = " <= ";
									break;

								case obj_predicate_compare::GREATER_OR_EQ:
									$comparator = " >= ";
									break;

								case obj_predicate_compare::BETWEEN:
									$comparator = " > {$v_data} AND {$tf} < ";
									$v_data = $val->data2;
									break;

								case obj_predicate_compare::BETWEEN_INCLUDING:
									$comparator = " >= {$v_data} AND {$tf} <= ";
									$v_data = $val->data2;
									break;

								case obj_predicate_compare::EQUAL:
									$comparator = " = ";
									$v_data = $val->data2;
									break;

								case obj_predicate_compare::NULL: //DEPRECATED
								case obj_predicate_compare::IS_NULL:
									$comparator = " IS NULL ";
									$v_data = "";
									break;

								case obj_predicate_compare::IS_EMPTY:
									$comparator = " IS NULL OR ";
									$v_data = "";
									break;

								case obj_predicate_compare::IN_TIMESPAN:
									break;

								default:
									error::raise(array(
										"id" => "ERR_OBJ_COMPARATOR",
										"msg" => sprintf(t("obj_predicate_compare's comparator operand must be either obj_predicate_compare::LESS,obj_predicate_compare::GREATER,obj_predicate_compare::LESS_OR_EQ,obj_predicate_compare::GREATER_OR_EQ,obj_predicate_compare::NULL,obj_predicate_compare::IN_TIMESPAN. the value supplied, was: %s!"), $val->comparator)
									));
							}

							if ($val->comparator == obj_predicate_compare::IN_TIMESPAN)
							{
								$tbl_fld1 = $this->_get_tablefield_from_prop($val->data[0]);
								$tbl_fld2 = $this->_get_tablefield_from_prop($val->data[1]);
								$sql[] = " (NOT ($tbl_fld1 >= '".$val->data2[1]."' OR $tbl_fld2 <= '".$val->data2[0]."')) ";
							}
							elseif ($val->comparator == obj_predicate_compare::NULL)
							{
								$sql[] = $tf." & ".((int)$this->properties[$key]["ch_value"])." IS NULL ";
							}
							elseif (is_array($v_data))
							{
								$tmp = array();
								foreach($v_data as $d_k)
								{
									$tmp[] = $tf." & ".((int)$d_k)." $comparator ".((int)$d_k)." ";
								}
								$sql[] = "(".join(" OR ", $tmp).")";
							}
							else
							{
								$sql[] = $tf." & ".((int)$v_data)." $comparator ".((int)$v_data)." ";
							}
							break;

						default:
							error::raise(array(
								"id" => "OBJ_BF_NOTSUPPORTED",
								"msg" => sprintf(t("complex compares of this type (%s) are not yet supported on bitfields (%s)!"), get_class($val), $key)
							));
							return;
					}
				}
				else
				{
					$sql[] = $tf." & ".((int)$this->properties[$key]["ch_value"])." = ".((int)$this->properties[$key]["ch_value"]);
				}
			}
			elseif (is_object($val))
			{
				$class_name = get_class($val);
				if ($class_name === "object_list_filter")
				{
					if (!empty($val->filter["non_filter_classes"]))
					{
						$this->_do_add_class_id($val->filter["non_filter_classes"], true);
					}
					if (isset($val->filter["logic"]))
					{
						$aa = $this->req_make_sql($val->filter["conditions"], $val->filter["logic"],true);
						if ($aa != "")
						{
							$sql[] = "(".$aa.")";
						}
					}
				}
				elseif ($class_name === "obj_predicate_not")
				{
					$v_data = $val->data;
					if (is_object($val->data) && get_class($val->data) === "aw_array")
					{
						$v_data = $v_data->get();
					}

					if (is_array($val->data))
					{
						$has_pct = false;
						$tmp_sql = array();
						foreach($v_data as $__val)
						{
							if (strpos($__val , "%") !== false)
							{
								$has_pct = true;
							}
							$tmp_sql[] = "{$tf} NOT LIKE '{$__val}' ";
						}
						if ($has_pct)
						{
							$sql[] = " ( ".join(" AND ", $tmp_sql)." ) ";
						}
						else
						{
							$sql[] = "{$tf} NOT IN (".join(",", map("'%s'", $v_data)).") ";
						}
					}
					else
					{
						$opn_app = "";
						if (!is_numeric($v_data) || $v_data != 0)
						{
							$opn_app = "OR $tf IS NULL";
						}

						if (strpos($v_data, "%") !== false)
						{
							$sql[] = " ({$tf} NOT LIKE '{$v_data}'  $opn_app ) ";
						}
						else
						{
							$sql[] = " ({$tf} != '{$v_data}'  $opn_app ) ";
						}
					}
				}
				elseif ($class_name === "obj_predicate_regex")
				{
					$v_data = $val->data;
					$sql[] = " ({$tf} REGEXP '{$v_data}'  ) ";
				}
				elseif ($class_name === "obj_predicate_compare")
				{
					$v_data = $val->data;
					if (is_object($val->data) && get_class($val->data) === "aw_array")
					{
						$v_data = $v_data->get();
					}

					$comparator = "";
					switch($val->comparator)
					{
						case obj_predicate_compare::LESS:
							$comparator = " < ";
							break;

						case obj_predicate_compare::GREATER:
							$comparator = " > ";
							break;

						case obj_predicate_compare::LESS_OR_EQ:
							$comparator = " <= ";
							break;

						case obj_predicate_compare::GREATER_OR_EQ:
							$comparator = " >= ";
							break;

						case obj_predicate_compare::BETWEEN:
							$comparator = " > ".$v_data." AND $tf < ";
							$v_data = $val->data2;
							break;

						case obj_predicate_compare::BETWEEN_INCLUDING:
							$comparator = " >= ".$v_data." AND $tf <= ";
							$v_data = $val->data2;
							break;

						case obj_predicate_compare::EQUAL:
							$comparator = " = ";
							$v_data = $val->data2;
							break;

						case obj_predicate_compare::NULL: //DEPRECATED
						case obj_predicate_compare::IS_NULL:
							$comparator = " IS NULL ";
							$v_data = "";
							break;

						case obj_predicate_compare::IS_EMPTY:
							$comparator = " IS NULL OR CAST({$tf} AS UNSIGNED) = 0";
							$val->type = "int";
							$v_data = "";
							break;

						case obj_predicate_compare::IN_TIMESPAN:
							break;

						default:
							error::raise(array(
								"id" => "ERR_OBJ_COMPARATOR",
								"msg" => sprintf(t("obj_predicate_compare's comparator operand must be either obj_predicate_compare::LESS,obj_predicate_compare::GREATER,obj_predicate_compare::LESS_OR_EQ,obj_predicate_compare::GREATER_OR_EQ,obj_predicate_compare::NULL,obj_predicate_compare::IN_TIMESPAN. the value supplied, was: %s!"), $val->comparator)
							));
					}

					if ($val->comparator == obj_predicate_compare::IN_TIMESPAN)
					{
						$tbl_fld1 = $this->_get_tablefield_from_prop($val->data[0]);
						$tbl_fld2 = $this->_get_tablefield_from_prop($val->data[1]);
						$sql[] = " (NOT ($tbl_fld1 >= '".$val->data2[1]."' OR $tbl_fld2 <= '".$val->data2[0]."')) ";
					}
					elseif ($val->comparator == obj_predicate_compare::NULL)
					{
						$sql[] = "{$tf} IS NULL ";
					}
					elseif (is_array($v_data))
					{
						$tmp = array();
						foreach($v_data as $d_k)
						{
							$tmp[] = "{$tf} {$comparator} '{$d_k}' ";
						}
						$sql[] = "(".join(" OR ", $tmp).")";
					}
					else
					{
						if($val->type === "int")
						{
							$ent = "";
						}
						else
						{
							$ent = "'";
						}
						$sql[] = " ({$tf} {$comparator} {$ent}{$v_data}{$ent}) ";
					}
				}
				elseif ($class_name === "obj_predicate_prop")
				{
					if ($val->prop === "id")
					{
						$tbl2 = "objects";
						$fld2 = "oid";
					}
					else
					{
						$tbl2 = $this->properties[$val->prop]["table"];
						$fld2 = $this->properties[$val->prop]["field"];
					}

					switch($val->compare)
					{
						case obj_predicate_compare::LESS:
							$compr = " < ";
							break;

						case obj_predicate_compare::GREATER:
							$compr = " > ";
							break;

						case obj_predicate_compare::LESS_OR_EQ:
							$compr = " <= ";
							break;

						case obj_predicate_compare::GREATER_OR_EQ:
							$compr = " >= ";
							break;

						case obj_predicate_compare::BETWEEN:
							error::raise(array(
								"id" => "ERR_WRONG_COMPARATOR",
								"msg" => t("obj_predicate_compare::BETWEEN does not make sense with obj_predicate_prop!")
							));
							break;

						default:
						case obj_predicate_compare::EQUAL:
							$compr = " = ";
							break;
					}
					$sql[] = $tf.$compr.$tbl2.".".$fld2." ";
				}
				elseif ($class_name === "obj_predicate_limit")
				{
					if (($tmp = $val->get_per_page()) > 0)
					{
						$this->limit = " LIMIT ".$val->get_from().",$tmp ";
					}
					else
					{
						$this->limit = " LIMIT ".$val->get_from()." ";
					}
				}
				elseif ($class_name === "obj_predicate_sort")
				{
					if (empty($this->sby))
					{
						$this->sby = " ORDER BY ";
					}
					$tmp = array();
					foreach($val->get_sorter_list() as $sl_item)
					{
						if (strpos($sl_item["prop"], ".") !== false)
						{
							// no support for prop.prop.prop yet, just class definer
							list($table, $field) = $this->_do_proc_complex_param(array("key" => $sl_item["prop"], "val" => null, "params" => $p_tmp));
							$pd = array("table" => $table, "field" => $field);
						}
						elseif (isset($GLOBALS["object_loader"]->all_ot_flds[$sl_item["prop"]]) || $sl_item["prop"] === "oid")
						{
							$pd = array("table" => "objects", "field" => $sl_item["prop"]);
						}
						else
						{
							$pd = $this->properties[$sl_item["prop"]];
						}

						if ($sl_item["predicate"])
						{
							if ("obj_predicate_compare" === get_class($sl_item["predicate"]))
							{
								$comparator = "";
								$fld = "{$pd["table"]}.`{$pd["field"]}`";
								$predicate = $sl_item["predicate"];
								switch($predicate->comparator)
								{
									case obj_predicate_compare::LESS:
										$comparator = "{$fld} < {$predicate->data}";
										break;

									case obj_predicate_compare::GREATER:
										$comparator = "{$fld} > {$predicate->data}";
										break;

									case obj_predicate_compare::LESS_OR_EQ:
										$comparator = "{$fld} <= {$predicate->data}";
										break;

									case obj_predicate_compare::GREATER_OR_EQ:
										$comparator = "{$fld} >= {$predicate->data}";
										break;

									case obj_predicate_compare::BETWEEN:
										$comparator = "{$fld} > {$predicate->data} AND {$fld} < {$predicate->data2}";
										break;

									case obj_predicate_compare::BETWEEN_INCLUDING:
										$comparator = "{$fld} >= {$predicate->data} AND {$fld} <= {$predicate->data2}";
										break;

									case obj_predicate_compare::EQUAL:
										$comparator = "{$fld} = {$predicate->data}";
										break;

									case obj_predicate_compare::NULL://DEPRECATED
									case obj_predicate_compare::IS_NULL:
										$comparator = "ISNULL({$fld})";
										break;

									case obj_predicate_compare::IS_EMPTY:
										$comparator = "ISNULL({$fld})";
										break;

									default:
										throw new awex_obj("Comparator not supported");
								}
								$tmp[] = "IF(({$comparator}), 0, 1), {$fld} ".($sl_item["direction"] === "DESC" ? "DESC" : "ASC")." ";
							}
						}
						else
						{
							$tmp[] = $pd["table"].".`".$pd["field"]."` ".($sl_item["direction"] === "DESC" ? "DESC" : "ASC")." ";
						}

						$this->_add_s($pd["table"]);
					}
					$this->sby .= (strlen($this->sby) === 10 ? "" : ", ") . join(", ", $tmp);
				}
			}
			elseif (is_array($val) or $val instanceof aw_array)
			{
				$store = isset($this->properties[$key]["store"]) ? $this->properties[$key]["store"] : "";
				if ($str = self::_parse_array_param_value($val, $key, $tf, $store))
				{
					$str = implode(" OR ", $str);
					$sql[] = " ( {$str} ) ";
				}
			}
			else
			{
				$this->quote($val);
				if (isset($this->properties[$key]["store"]) && $this->properties[$key]["store"] === "connect")
				{
					$sql[] = " aliases_{$key}.target = '{$val}' ";
				}
				elseif (($key === "modified" && strpos($val, "%") === false) || $key === "flags")
				{
					// pass all arguments .. &, >, < or whatever the user wants to
					$sql[] = "{$tf} {$val}";
				}
				elseif (strpos($val,"%") !== false)
				{
					$sql[] = "{$tf} LIKE '{$val}'";
				}
				else
				{
					$sql[] = "{$tf} = '{$val}'";
				}
			}
		}
		return join(" {$logic} ", $sql);
	}

	private function _parse_array_param_value($val, $key, $tf, $store)
	{
		if ($val instanceof aw_array)
		{
			$val = $val->get();
		}
		$str = array();
		foreach($val as $v)
		{
			if ($v === "")
			{
				continue;
			}

			if ($store === "connect")
			{
				$this->quote($v);
				$str[] = " aliases_{$key}.target = '{$v}' ";
			}
			elseif (is_array($v) or $v instanceof aw_array)
			{
				$str = array_merge($str, self::_parse_array_param_value($v, $key, $tf, $store));
			}
			elseif (strpos($v, "%") !== false)
			{
				$this->quote($v);
				$str[] = "{$tf} LIKE '{$v}'";
			}
			else
			{
				$this->quote($v);
				$str[] = "{$tf} = '{$v}'";
			}
		}
		return $str;
	}

	function _do_add_class_id($clids, $add_table = false)
	{
		if (!is_array($clids))
		{
			$clids = array($clids);
		}

		foreach($clids as $clid)
		{
			if (!is_class_id($clid))
			{
				continue;
			}
			if (!isset($GLOBALS["properties"][$clid]) || !isset($GLOBALS["tableinfo"][$clid]) || !isset($GLOBALS["relinfo"][$clid]))
			{
				list($GLOBALS["properties"][$clid], $GLOBALS["tableinfo"][$clid], $GLOBALS["relinfo"][$clid]) = object_loader::instance()->load_properties(array(
					"file" => $clid == CL_DOCUMENT ? "doc" : (aw_ini_isset("classes.{$clid}") ? basename(aw_ini_get("classes.{$clid}.file")) : ""),
					"clid" => $clid
				));
			}

			$this->properties += $GLOBALS["properties"][$clid];
			if (is_array($GLOBALS["tableinfo"][$clid]))
			{
				if ($add_table)
				{
					foreach($GLOBALS["tableinfo"][$clid] as $_tbl => $td)
					{
						$this->used_tables[$_tbl] = $_tbl;
					}
				}
				$this->tableinfo += $GLOBALS["tableinfo"][$clid];
			}
			if (isset($this->tableinfo["documents"]))
			{
				$this->used_tables["documents"] = "documents";
			}
		}
	}

	public function create_brother($arr)
	{
		extract($arr);

		$metadata = aw_serialize($objdata["meta"], SERIALIZE_NATIVE);
		$this->quote($metadata);
		$this->quote($objdata);

		$objdata["createdby"] = $objdata["modifiedby"] = aw_global_get("uid");
		$objdata["created"] = $objdata["modified"] = time();

		$objdata["lang_id"] = aw_global_get("lang_id");

		// fetch site id from the parent
		$od = $this->get_objdata($parent);
		$objdata["site_id"] = $od["site_id"];

		$acld_fld = $acld_val = "";
		if (aw_ini_get("acl.use_new_acl") && $_SESSION["uid"] != "" && is_oid(aw_global_get("uid_oid")))
		{
			$uo = obj(aw_global_get("uid_oid"));
			$g_d = $uo->get_default_group();
			$acld_fld = ",acldata";
			$acld_val = aw_serialize(array(
				$g_d => acl_base::get_acl_value_n(acl_base::acl_get_default_acl_arr())
			), SERIALIZE_NATIVE);
			$this->quote($acld_val);
			$acld_val = ",'".$acld_val."'";
		}

		// create oid
		$q = "
			INSERT INTO objects (
				parent,						class_id,						name,						createdby,
				created,					modified,						status,						site_id,
				hits,						lang_id,						comment,					modifiedby,
				jrk,						period,							alias,						periodic,
				metadata,					subclass,					flags,
				brother_of					$acld_fld
		) VALUES (
				'{$parent}',				'{$objdata["class_id"]}',		'{$objdata["name"]}',		'{$objdata["createdby"]}',
				'{$objdata["created"]}',	'{$objdata["modified"]}',		'{$objdata["status"]}',	'{$objdata["site_id"]}',
				'{$objdata["hits"]}',		'{$objdata["lang_id"]}',		'{$objdata["comment"]}',	'{$objdata["modifiedby"]}',
				'{$objdata["jrk"]}',		'{$objdata["period"]}',		'{$objdata["alias"]}',	'{$objdata["periodic"]}',
										'{$metadata}',				'{$objdata["subclass"]}',	'{$objdata["flags"]}',
				'{$objdata["oid"]}'		{$acld_val}
		)";
		$this->db_query($q);
		$oid = $this->db_last_insert_id();

		if (!aw_ini_get("acl.use_new_acl_final"))
		{
			// create all access for the creator
			acl_base::create_obj_access($oid);
		}

		// hits
		$this->db_query("INSERT INTO hits(oid,hits,cachehits) VALUES($oid, 0, 0 )");

		$this->create_brother_cache_update(null);

		return $oid;
	}

	function create_brother_cache_update($oid)
	{
		if (!object_loader::opt("no_cache"))
		{
			cache::file_clear_pt("html");
		}
	}

	// $key, $val
	private function _do_proc_complex_param($arr)
	{
		extract($arr);
		$filt = explode(".", $key);
		if (!defined($filt[0]))
		{
			$clid = $arr["params"]["class_id"];
			if (is_array($clid) and count($clid) > 1)
			{
				error::raise(array(
					"id" => "ERR_OL_PARAM_ERROR",
					"msg" => sprintf(t("You must specify class id in a complex filter parameter (%s) if searching from multiple classes!"), $key)
				));
			}
		}
		else
		{
			$clid = constant($filt[0]);
		}

		if (substr($filt[0], 0, 3) !== "CL_" && (is_array($params["class_id"]) || is_class_id($params["class_id"])))
		{
			if (is_array($params["class_id"]))
			{
				$m_clid = reset($params["class_id"]);
			}
			else
			{
				$m_clid = $params["class_id"];
			}
			$key = aw_ini_get("classes.{$m_clid}.def") . "." . $key;
			$filt = explode(".", $key);
			$clid = constant($filt[0]);
		}

		if (!is_class_id($clid))
		{
			if (!is_array($params["class_id"]))
			{
				error::raise_if(!is_class_id($params["class_id"]), array(
					"id" => "ERR_OBJ_NO_CLID",
					"msg" => sprintf(t("ds_mysql::do_proc_complex_param(%s, %s): if a complex join parameter is given without a class id as the first element, the class_id parameter must be set!"), $key, $val)
				));
				$clid = $params["class_id"];
			}
			else
			{
				$clid = reset($params["class_id"]);
			}
		}
		else
		{
			// if the first part is a class id and there are only two parts then it is not a join
			// then it is a specification on what class's property to search from
			// UNLESS the second part begins with RELTYPE
			if (count($filt) === 2)
			{
				if (substr($filt[1], 0, 7) !== "RELTYPE")
				{
					// so just return the table and field for that clas
					if (!isset($GLOBALS["properties"][$clid]))
					{
						$this->_do_add_class_id($clid);
					}

					if (isset($GLOBALS["properties"][$clid][$filt[1]]))
					{
						$prop = $GLOBALS["properties"][$clid][$filt[1]];
					}
					else
					{
						// see if it is an objtbl prop
						switch($filt[1])
						{
							case "id":
							case "oid":
								return array("objects", "oid");

							case "ord":
								return array("objects", "jrk");

							case "created":
							case "createdby":
							case "modified":
							case "modifiedby":
							case "parent":
							case "name":
							case "lang_id":
							case "comment":
							case "period":
							case "site_id":
								return array("objects", $filt[1]);
						}
					}

					if ($prop["store"] === "connect" || $prop["method"] === "serialize")	// need special handling, rewrite to undefined class filter
					{
						return array("__rewrite_prop", $filt[1]);
					}
					$this->used_tables[$prop["table"]] = $prop["table"];
					return array($prop["table"], $prop["field"]);
				}
			}
		}

		$this->foo = array();
		$this->join_data = array();
		$this->_req_do_pcp($filt, 1, $clid, $arr);

		// join all other tables from the starting class except the objects table
		$tmp = $GLOBALS["tableinfo"][$clid];

		unset($tmp["objects"]);
		foreach($tmp as $tbl => $tbldat)
		{
			// check uniqueness
			$str = " LEFT JOIN {$tbl} {$tbl}_{$clid} ON {$tbl}_{$clid}.{$tbldat["index"]} = {$tbldat["master_table"]}.{$tbldat["master_index"]} ";
			if (!in_array($str, $this->joins))
			{
				$this->_add_join($str);
				$this->_add_s($tbldat["master_table"]);
			}
		}

		$done_ot_js = array(); 	// reverting remembering this, because previously it was needed for double joins on same names, but now we filter the doubles and unneeded joins out later anyway
		// and this actually can cause some joins to go missing that are needed, cause it does not recurd the full parameter list
		// now make joins and for the final prop, query
		foreach($this->join_data as $pos => $join)
		{
			if ($join["via"] === "rel")
			{
				// from prev to alias from alias to obj
				if (empty($join["table"]))
				{
					$prev_t = "";
				}
				else
				{
					$prev_t = $join["table"]."_".$join["from_class"];
				}
				$prev_clid = $join["from_class"];

				$tmp_prev = isset($this->join_data[$pos-1]) ? $this->join_data[$pos-1] : array(
					"from_class" => "",
					"reltype" => "",
					"via" => ""
				);
				$cur_al_name = "aliases_".$tmp_prev["from_class"]."_".$tmp_prev["reltype"]."_".$join["to_class"]."_".$join["reltype"];
				$rel_from_field = "source";
				$rel_to_field = "target";
				if (ifset($join, "is_reverse") == 1)
				{
					$rel_from_field = "target";
					$rel_to_field = "source";
				}
				$str  = " LEFT JOIN aliases {$cur_al_name} ON {$cur_al_name}.{$rel_from_field} = ";
				if ($join["from_class"] == $clid)
				{
					$str .= " objects.oid ";
				}
				elseif ($tmp_prev["via"] === "rel")
				{
					$_tb_name = "objects__".$tmp_prev["from_class"]."_".$join["from_class"]."_".$tmp_prev["reltype"];
					$str .= " ".$_tb_name.".oid ";
					$this->_add_s($_tb_name);
				}
				else
				{
					$_tb_name = "objects_".$join["from_class"];
					$str .= " {$_tb_name}.oid ";
					$this->_add_s($_tb_name);
				}

				if ($join["reltype"])
				{
					$str .= " AND {$cur_al_name}.reltype = {$join["reltype"]}";
				}

				$this->_add_join($str);

				$tmp_cur_obj_name = "objects_{$tmp_prev["reltype"]}_{$join["from_class"]}_{$join["to_class"]}_{$join["reltype"]}";
				$this->last_object_table_alias = $tmp_cur_obj_name;

				$str  = " LEFT JOIN objects {$tmp_cur_obj_name}  ON {$cur_al_name}.{$rel_to_field} = ";
				$str .= " {$tmp_cur_obj_name}.oid ";
				$prev_clid = $join["to_class"];
				$this->_add_s($cur_al_name);

				$this->_add_join($str);

				if (isset($GLOBALS["tableinfo"][$join["to_class"]]) and is_array($GLOBALS["tableinfo"][$join["to_class"]]))
				{
					$new_t = $GLOBALS["tableinfo"][$join["to_class"]];
					$objt_name = $tmp_cur_obj_name;
					$new_t_keys = array_keys($new_t);
					$tbl = $tbl_r = reset($new_t_keys);
					if ($tbl != "")
					{
						$field = $new_t[$tbl]["index"];
						$tbl .= "_".$join["from_class"]."_".(isset($join["field"]) ? $join["field"] : "");
						if (!isset($done_ot_js[$tbl_r]))
						{
							$str = " LEFT JOIN {$tbl_r} {$tbl} ON {$tbl}.{$field} = {$objt_name}.brother_of";
							$this->_add_s($objt_name);
							$this->_add_join($str);
							$done_ot_js[$tbl_r] = 1;
							$prev_t = $tbl;
						}
					}

					// now, if the next join is via rel, we are gonna need the objects table here as well, so add that
					if (isset($this->join_data[$pos+1]["via"]) and $this->join_data[$pos+1]["via"] === "rel" && $tbl != "")
					{
						$o_field = "oid";
						$o_tbl = "objects_".$join["to_class"];
						if (!isset($done_ot_js[$o_tbl]))
						{
							$str = " LEFT JOIN objects {$o_tbl} ON {$o_tbl}.{$o_field} = {$tbl}.{$field}";
							$this->_add_s($tbl);
							$this->_add_join($str);
						}
					}
				}

				$prev_al_name = $cur_al_name;
				$ret = array(
					$cur_al_name,
					$rel_to_field
				);
			}
			else	// via prop
			{
				if (!$join["to_class"] && $this->join_data[$pos-1]["via"] === "rel")
				{
					$prev = $this->join_data[$pos-1];
					$prev_prev = ifset($this->join_data, $pos-2);

					$this->_do_add_class_id($join["from_class"]);
					// join from rel to prop
					$prev_t = $prev_al_name;

					$new_t = $GLOBALS["tableinfo"][$join["from_class"]];
					$do_other_join = false;
					$and_buster = "";
					if (!is_array($new_t) || !isset($GLOBALS["properties"][$join["from_class"]][$join["prop"]]) || $GLOBALS["properties"][$join["from_class"]][$join["prop"]]["table"] === "objects")
					{
						// class only has objects table, so join that
						$tbl = "objects_rel_".$prev["from_class"]."_".$prev["reltype"]."_".$join["from_class"]."_".$prev["reltype"]."_".$prev_prev["reltype"];
						$tbl_r = "objects";
						$field = "oid";

						// and also join any other tables as well just to be on the safe side.
						$do_other_join = is_array($new_t);
						$and_buster = " AND ".$tbl.".status > 0 ";
					}
					else
					{
						$new_t_keys = array_keys($new_t);
						$tbl = $tbl_r = reset($new_t_keys);
						$field = $new_t[$tbl]["index"];
					}

					if (ifset($prev, "is_reverse") == 1)
					{
						$tmp_fld = "source";
					}
					else
					{
						$tmp_fld = "target";
					}
					$str = " LEFT JOIN {$tbl_r} {$tbl} ON {$tbl}.{$field} = {$prev_t}.{$tmp_fld} {$and_buster} ";
					$this->_add_s($prev_t);
					$this->_add_join($str);
//					$this->joins[] = $str;
					$ret = array(
						$tbl,
						$join["field"]
					);

					break;
				}
				elseif (!$join["to_class"])
				{
					if ($pos == (count($this->join_data)-1))
					{
						$prev_t = $join["table"]."_".$prev_clid."_".$prev_filt;
					}

					if ($join["prop"] === "class_id")
					{
						$__fld = "class_id";
					}
					elseif ($join["prop"] === "parent")
					{
						$__fld = "parent";
					}
					elseif ($join["prop"] === "oid")
					{
						$__fld = "oid";
					}
					elseif ($join["prop"] === "brother_of")
					{
						$__fld = "brother_of";
					}
					elseif ($join["prop"] === "ord")
					{
						$__fld = $filt[count($filt)-1] = "jrk";
					}
					else
					{
						$__fld = $GLOBALS["properties"][$join["from_class"]][$join["prop"]]["field"];
					}

					$ret = array(
						$prev_t,
						$__fld,
					);
					continue;
				}
				// if the next stop is a property
				// then join all the tables in that class
				// first the objects table
				if (empty($prev_t))
				{
					$prev_t = $join["table"]."_".$join["from_class"];
				}
				$prev_filt = $join["field"];
				$prev_clid = $join["from_class"];

				$objt_name = "objects_".$join["from_class"]."_".$join["field"];
				if (!isset($done_ot_js[$objt_name]))
				{
					$this->_add_join(" LEFT JOIN objects {$objt_name} ON {$objt_name}.oid = {$prev_t}.{$join["field"]} ");
					$this->_add_s($prev_t);
					$done_ot_js[$objt_name] = 1;
				}

				$new_t = $GLOBALS["tableinfo"][$join["to_class"]];

				if (is_array($new_t) && count($new_t))
				{
					$new_t_keys = array_keys($new_t);
					$tbl = $tbl_r = reset($new_t_keys);
					if ($tbl)
					{
						$field = $new_t[$tbl]["index"];
						$tbl .= "_".$join["from_class"]."_".$join["field"];
						if (!isset($done_ot_js[$tbl_r]))
						{
							$str = " LEFT JOIN ".$tbl_r." $tbl ON ".$tbl.".".$field." = ".$objt_name.".brother_of";
							$this->_add_s($objt_name);
							$this->_add_join($str);
							$done_ot_js[$tbl_r] = 1;
							$prev_t = $tbl;
						}
					}

					// now, if the next join is via rel, we are gonna need the objects table here as well, so add that
					if ($this->join_data[$pos+1]["via"] === "rel")
					{
						$o_field = "oid";
						$o_tbl = "objects_".$join["to_class"];
						if (!isset($done_ot_js[$o_tbl]))
						{
							$str = " LEFT JOIN objects $o_tbl ON ".$o_tbl.".".$o_field." = ".$tbl.".".$field;
							$this->_add_s($tbl);
							$this->_add_join($str);
						}
					}
				}
			}
		}

		$this->done_ot_js = $done_ot_js;

		$arr["key"] = $filt[count($filt)-1];
		$this->joins = array_unique($this->joins);
		return $ret;
	}

	function _req_do_pcp($filt, $pos, $cur_clid, $arr)
	{
		$pp = $filt[$pos];

		// if the next param is RELTYPE_* then via relation
		// else, if it is property for cur class - via property
		// else - throw up

		if (substr($pp, 0, 7) === "RELTYPE")
		{
			$this->_do_add_class_id($cur_clid);

			// check if this is RELTYPE_FOO(CL_CLID) that means a reverse relation check
			if (preg_match("/RELTYPE_(.*)\((.*)\)/", $pp, $mt))
			{
				$nxt_clid = constant($mt[2]);
				if ($nxt_clid)
				{
					$this->_do_add_class_id($nxt_clid);
					$reltype_id = isset($GLOBALS["relinfo"][$nxt_clid]["RELTYPE_".$mt[1]]["value"]) ? $GLOBALS["relinfo"][$nxt_clid]["RELTYPE_".$mt[1]]["value"] : "";
					error::raise_if(strlen($reltype_id) === 0 && $pp != "RELTYPE", array(
						"id" => "ERR_OBJ_NO_RELATION",
						"msg" => sprintf(t("ds_mysql::_req_do_pcp(): no relation from class %s named %s"), $cur_clid, $pp)
					));

					// calc new class id
					$new_clid = $nxt_clid;

					$this->join_data[] = array(
						"via" => "rel",
						"reltype" => $reltype_id,
						"from_class" => $cur_clid,
						"to_class" => $nxt_clid,
						"is_reverse" => 1
					);
				}
			}
			else
			{
				$reltype_id = isset($GLOBALS["relinfo"][$cur_clid][$pp]["value"]) ? $GLOBALS["relinfo"][$cur_clid][$pp]["value"] : "";
				error::raise_if(strlen($reltype_id) === 0 && $pp !== "RELTYPE", array(
					"id" => "ERR_OBJ_NO_RELATION",
					"msg" => sprintf(t("ds_mysql::_req_do_pcp(): no relation from class %s named %s"), $cur_clid, $pp)
				));

				// calc new class id
				$new_clid = isset($GLOBALS["relinfo"][$cur_clid][$pp]["clid"][0]) ? $GLOBALS["relinfo"][$cur_clid][$pp]["clid"][0] : null;//XXX: kui rel dfn-s clid m22ramata -- seos yksk6ik mis tyypi objektiga -- siis siin pole clid.

				$this->join_data[] = array(
					"via" => "rel",
					"reltype" => $reltype_id,
					"from_class" => $cur_clid,
					"to_class" => $new_clid
				);
			}
		}
		else
		{
			if (!isset($GLOBALS["properties"][$cur_clid]))
			{
				list($GLOBALS["properties"][$cur_clid], $GLOBALS["tableinfo"][$cur_clid], $GLOBALS["relinfo"][$cur_clid]) = object_loader::instance()->load_properties(array(
					"file" => ($cur_clid == CL_DOCUMENT ? "doc" : basename(aw_ini_get("classes.{$cur_clid}.file"))),
					"clid" => $cur_clid
				));
			}

			$set_clid = false;
			if (($_pos = strpos($pp, "(")) !== false)
			{
				$set_clid = constant(substr($pp, $_pos+1, -1));
				$pp = substr($pp, 0, $_pos);
			}

			if ($pp === "id")
			{
				$cur_prop = array("name" => "id", "table" => "objects", "field" => "oid");
			}
			elseif ($pp === "oid")
			{
				$cur_prop = array("name" => "oid", "table" => "objects", "field" => "oid");
			}
			elseif ($pp === "brother_of")
			{
				$cur_prop = array("name" => "brother_of", "table" => "objects", "field" => "brother_of");
			}
			elseif ($pp === "class_id")
			{
				$cur_prop = array("name" => "id", "table" => "objects", "field" => "class_id");
			}
			elseif ($pp === "parent")
			{
				$cur_prop = array("name" => "parent", "table" => "objects", "field" => "parent");
			}
			elseif ($pp === "created")
			{
				$cur_prop = array("name" => "created", "table" => "objects", "field" => "created");
			}
			elseif ($pp === "createdby")
			{
				$cur_prop = array("name" => "createdby", "table" => "objects", "field" => "createdby");
			}
			elseif ($pp === "modified")
			{
				$cur_prop = array("name" => "modified", "table" => "objects", "field" => "modified");
			}
			elseif ($pp === "modifiedby")
			{
				$cur_prop = array("name" => "modifiedby", "table" => "objects", "field" => "modifiedby");
			}
			elseif ($pp === "ord")
			{
				$cur_prop = array("name" => "ord", "table" => "objects", "field" => "jrk");
			}
			else
			{
				$cur_prop = $GLOBALS["properties"][$cur_clid][$pp];
			}

			error::raise_if(!is_array($cur_prop), array(
				"id" => "ERR_OBJ_NO_PROP",
				"msg" => sprintf(t("ds_mysql::_req_do_pcp(): no property %s in class %s "), $pp, $cur_clid)
			));


			$table = $cur_prop["table"];
			$field = $cur_prop["field"];

			// if it is the last one, then it can be anything
			if ($pos < (count($filt) - 1))
			{
				error::raise_if($cur_prop["method"] === "serialize" && $cur_prop["store"] !== "connect", array(
					"id" => "ERR_OBJ_NO_META",
					"msg" => sprintf(t("ds_mysql::_req_do_pcp(): can not join classes on serialized fields (property %s in class %s)"), $pp, $cur_clid)
				));
				if ($set_clid)
				{
					$new_clid = $set_clid;
					error::raise_if(!$set_clid, array(
						"id" => "ERR_OBJ_W_TP",
						"msg" => sprintf(t("ds_mysql::_req_do_pcp(): incorrect prop type! (%s)"), $cur_prop["type"])
					));
				}
				else
				{
					switch ($cur_prop["type"])
					{
						case "objpicker":
							$new_clid = false;
							$prop_clid = $cur_prop["clid"];
							if ($prop_clid)
							{
								$new_clid = constant($prop_clid);
							}
							break;

						case "relpicker":
						case "hidden"://? v6ib ju
						case "relmanager":
						case "classificator":
						case "popup_search":
						case "crm_participant_search":
						case "releditor":
							$new_clid = false;

							$relt_s = $cur_prop["reltype"];
							$relt = $GLOBALS["relinfo"][$cur_clid][$relt_s]["value"];

							if (!$relt)
							{
								$new_clid = constant($cur_prop["clid"]);
							}

							error::raise_if(!$relt && !$new_clid, array(
								"id" => "ERR_OBJ_NO_REL",
								"msg" => sprintf(t("ds_mysql::_req_do_pcp(): no reltype %s in class %s , got reltype from relpicker property %s"), $relt_s, $cur_clid, $cur_prop["name"])
							));

							if (!$new_clid)
							{
								$new_clid = isset($GLOBALS["relinfo"][$cur_clid][$relt_s]["clid"][0]) ? $GLOBALS["relinfo"][$cur_clid][$relt_s]["clid"][0] : NULL;
							}
							break;

						default:
							$new_clid = $set_clid;
							error::raise_if(!$set_clid, array(
								"id" => "ERR_OBJ_W_TP",
								"msg" => sprintf(t("ds_mysql::_req_do_pcp(): incorrect prop type! (%s)"), $cur_prop["type"])
							));
					}
				}
			}

			if (ifset($cur_prop, "store") === "connect")
			{
				$this->_do_add_class_id($cur_clid);
				// rewrite to a reltype join
				$pp = $cur_prop["reltype"];
				$reltype_id = isset($GLOBALS["relinfo"][$cur_clid][$pp]["value"]) ? $GLOBALS["relinfo"][$cur_clid][$pp]["value"] : "";
				error::raise_if(strlen($reltype_id) === 0 && $pp !== "RELTYPE", array(
					"id" => "ERR_OBJ_NO_RELATION",
					"msg" => sprintf(t("ds_mysql::_req_do_pcp(): no relation from class %s named %s"), $cur_clid, $pp)
				));

				// calc new class id
				$new_clid = isset($GLOBALS["relinfo"][$cur_clid][$pp]["clid"][0]) ? $GLOBALS["relinfo"][$cur_clid][$pp]["clid"][0] : NULL;
				$this->join_data[] = array(
					"via" => "rel",
					"reltype" => $reltype_id,
					"from_class" => $cur_clid,
					"to_class" => $new_clid
				);
			}
			else
			{
				$jd = array(
					"via" => "prop",
					"reltype" => "",
					"prop" => $pp,
					"from_class" => $cur_clid,
					"to_class" => isset($new_clid) ? $new_clid : NULL,
					"table" => $table,
					"field" => $field
				);
				$this->join_data[] = $jd;
			}
		}

		if ($pos < (count($filt)-1))
		{
			$this->_req_do_pcp($filt, $pos+1, $new_clid, $arr);
		}
	}

	function _get_joins($params)
	{
		// make joins
		$js = array();
		foreach($this->used_tables as $tbl)
		{
			if ($tbl !== "objects" && $tbl != "")
			{
				$js[] = " LEFT JOIN {$tbl} ON {$tbl}.{$this->tableinfo[$tbl]["index"]} = objects.brother_of ";
			}
		}

		foreach($this->alias_joins as $aj)
		{
			$js[] = " LEFT JOIN aliases {$aj["name"]} ON {$aj["on"]} ";
		}
		return "objects ".join("", $js).join(" ", $this->joins);
	}

	public function fetch_list($to_fetch)
	{
		$this->used_tables = array();
		$this->properties = array();
		$this->tableinfo = array();

		// make list of uniq class_id's
		$clids = array();
		$cl2obj = array();
		foreach($to_fetch as $oid => $clid)
		{
			$clids[$clid] = $clid;
			$cl2obj[$clid][$oid] = $oid;
		}

		// read props
		$this->_do_add_class_id($clids);

		$ret = array();
		$ret2 = array();

		// do joins on the data objects for those
		$joins = array();
		foreach($clids as $clid)
		{
			// this can not be cached, because it holds in it object id's for the query. silly, really.
			$sql = $this->get_read_properties_sql(array(
				"properties" => $GLOBALS["properties"][$clid],
				"tableinfo" => $GLOBALS["tableinfo"][$clid],
				"class_id" => $clid,
				"object_id" => $cl2obj[$clid],
				"full" => true
			));

			aw_cache_set("storage::get_read_properties_sql",$clid,$sql);

			if (!empty($sql["q"]))
			{
				$sql["q"] .= " IN (".join(",", $cl2obj[$clid]).")";

				// query
				$this->db_query($sql["q"]);
				while ($row = $this->db_next())
				{
					foreach ($this->properties as $property_name => $property_data)
					{
						if (isset($property_data['type']) and $property_data['type'] === 'range')
						{
							$row[$property_name] = array(
								"from" => isset($row[$property_name."_from"]) ? $row[$property_name."_from"] : null,
								"to" => isset($row[$property_name."_to"]) ? $row[$property_name."_to"] : null,
							);
							unset($row[$property_name."_from"], $row[$property_name."_to"]);
						}
					}
					$this->read_properties_data_cache[$row["oid"]] = $row;
					$GLOBALS["read_properties_data_cache_conn"][$row["oid"]] = array();
					$ret[] = $row;
				}
			}

			if (!empty($sql["q2"]))
			{
				$this->db_query($sql["q2"]);
				while ($row = $this->db_next())
				{
					$GLOBALS["read_properties_data_cache_conn"][$row["source"]][$row["reltype"]][] = $row;
					$ret2[] = $row;
				}
			}
		}
		return $ret;
	}

	private function _process_search_fetch_prop($pn, $resn, &$filter, &$has_func, &$ret, &$serialized_fields, &$multi_fields, $clid, $p)
	{
		if (is_numeric($pn) && !is_object($resn))
		{
			$pn = $resn;
		}

		if (is_object($resn) && get_class($resn) === "obj_sql_func")
		{
			$has_func = true;
			$param = $resn->params;
			if (isset($p[$param]))
			{
				$this->_add_s($p[$param]["table"]);
				$param = $p[$param]["table"].".`".$p[$param]["field"]."`";
			}
			switch($resn->sql_func)
			{
				case obj_sql_func::UNIQUE:
					$ret[$pn] = " DISTINCT(".$param.") AS `".$resn->name."` ";
					break;

				case obj_sql_func::COUNT:
					$ret[$pn] = " COUNT(".$param.") AS `".$resn->name."` ";
					break;

				case obj_sql_func::MAX:
					$ret[$pn] = " MAX(".$param.") AS `".$resn->name."` ";
					break;

				case obj_sql_func::MIN:
					$ret[$pn] = " MIN(".$param.") AS `".$resn->name."` ";
					break;

				default:
					error::raise(array(
						"id" => "MSG_WRONG_FUNC",
						"msg" => sprintf(t("ds_mysql::_get_search_fetch() was called with incorrect sql func %s"), $resn->sql)
					));
			}
		}
		elseif (is_numeric($pn))
		{
			$pn = $resn;
		}
		elseif (substr($pn, 0, 5) === "meta.")
		{
			$serialized_fields["objects.metadata"][] = substr($pn, 5);
		}
		elseif (strpos($pn, ".") !== false)
		{
			// over-prop join fetch. we don't know the column name yet, so let it replace it in req_make_sql when we figure it out.
			if (!isset($filter[$pn]))
			{
				$filter[$pn] = new obj_predicate_anything();
				$ret[$pn] = "%%REPLACE($pn)%% AS `$resn`"; //aliases___1063_26.target AS $resn ";
			}
		}
		elseif (!isset($p[$pn]))
		{
			// assume obj table
			$ret[$pn] = " objects.{$pn} AS `{$resn}` ";
		}
		elseif ($p[$pn]["method"] === "serialize")
		{
			if ($p[$pn]["table"] === "objects" && $p[$pn]["field"] === "meta")
			{
				$serialized_fields["objects.metadata"][] = $pn;
			}
			else
			{
				$serialized_fields[$p[$pn]["table"].".`".$p[$pn]["field"]."`"][] = substr($pn, 5);
				$this->_add_s($p[$pn]["table"]);
			}
		}
		elseif ($p[$pn]["store"] === "connect")
		{
			// fetch value from aliases table
			if (!isset($filter[$pn]))
			{
				$filter[$pn] = new obj_predicate_anything();
				$ret[$pn] = " aliases_".$pn.".target AS $resn ";
				$this->_add_s("aliases_".$pn);
			}
			else
			{
				$tbl_name = "aliases_".$clid."_".$GLOBALS["relinfo"][$clid][$p[$pn]["reltype"]]["value"];
				$ret[$pn] = " ".$tbl_name.".target AS $resn ";
				$this->_add_s($tbl_name);
			}
			if (!empty($p[$pn]["multiple"]))
			{
				$multi_fields[$pn] = $pn;
			}
		}
		else
		{
			$ret[$pn] = " ".$p[$pn]["table"].".`".$p[$pn]["field"]."` AS `$resn` ";
			$this->_add_s($p[$pn]["table"]);
		}
	}

	function _get_search_fetch($to_fetch, &$filter)
	{
		if (!is_array($to_fetch))
		{
			return array(0 => "", 1 => array(), 2 => array(), 3 => false, 4 => array());
		}
		$has_func = false;
		$ret = array();
		$serialized_fields = array();
		$multi_fields = array();

		foreach($to_fetch as $clid => $props)
		{
			$p = ifset($GLOBALS, "properties", $clid);
			$this->_do_add_class_id($clid, true);

			if (is_array($props))
			{
				foreach($props as $pn => $resn)
				{
					$this->_process_search_fetch_prop($pn, $resn, $filter, $has_func, $ret, $serialized_fields, $multi_fields, $clid, $p);
				}
			}
			elseif (is_string($props) and strlen($props) > 0)
			{
				$this->_process_search_fetch_prop(0, $props, $filter, $has_func, $ret, $serialized_fields, $multi_fields, $clid, $p);
			}
		}

		$sf = array();
		foreach($serialized_fields as $fld => $stuff)
		{
			$fldn = str_replace(".", "_", $fld);
			$ret[] = $fld." AS ".$fldn." ";
			$sf[$fldn] = $stuff;
		}

		$acld = "";
		if ($GLOBALS["cfg"]["acl"]["use_new_acl"])
		{
			$acld = " objects.acldata as acldata, objects.parent as parent,";
		}

		if ($has_func)
		{
			$fetch_sql = "";
		}
		else
		{
			$fetch_sql = "
				objects.oid as oid,
				objects.name as name,
				objects.parent as parent,
				objects.brother_of as brother_of,
				objects.status as status,
				objects.class_id as class_id,
				$acld
			";
		}
		$res =  $fetch_sql . implode(",", $ret);
		return array($res, array_keys($ret), $sf, $has_func, $multi_fields);
	}

	function save_properties_new_version($arr)
	{
		extract($arr);

		$metadata = aw_serialize($objdata["meta"], SERIALIZE_NATIVE);
		$this->quote($metadata);
		$this->quote($objdata);
		$objdata["metadata"] = $metadata;

		if ($objdata["brother_of"] == 0)
		{
			$objdata["brother_of"] = $objdata["oid"];
		}

		if (!$arr["objdata"]["version_id"])
		{
			// insert new record & get id
			$version_id = gen_uniq_id();
			$this->db_query("INSERT INTO documents_versions (version_id, docid, vers_crea, vers_crea_by) values('$version_id', $objdata[oid], ".time().", '".aw_global_get("uid")."')");
		}
		else
		{
			$version_id = $arr["objdata"]["version_id"];
			$this->db_query("UPDATE documents_versions SET vers_crea = ".time().", vers_crea_by = '".aw_global_get("uid")."' WHERE docid = $objdata[oid] AND version_id = '$version_id'");
		}

		$ot_sets = array();
		$arr["ot_modified"] = $GLOBALS["object_loader"]->all_ot_flds;
		foreach(safe_array($arr["ot_modified"]) as $_field => $one)
		{
			$ot_sets[] = " o_".$_field." = '".$objdata[$_field]."' ";
		}

		$ot_sets = join(" , ", $ot_sets);

		$q = "UPDATE documents_versions SET
			$ot_sets
			WHERE version_id = '".$version_id."'
		";

		$this->db_query($q);

		// now save all properties


		// divide all properties into tables
		$tbls = array();
		foreach($properties as $prop => $data)
		{
			if ($data["store"] !== "no" && $data["store"] !== "connect")
			{
				$tbls[$data["table"]][] = $data;
			}
		}

		// now save all props to tables.
		foreach($tbls as $tbl => $tbld)
		{
			if ($tbl == "")
			{
				continue;
			}

			if ($tbl === "objects")
			{
				continue;
				$tableinfo[$tbl]["index"] = "oid";
				$serfs["metadata"] = $objdata["meta"];
			}
			else
			{
				$serfs = array();
			}

			$seta = array();
			foreach($tbld as $prop)
			{
				// this check is here, so that we won't overwrite default values, that are saved in create_new_object
				if (isset($propvalues[$prop['name']]))
				{
					if ($prop['method'] === "serialize")
					{
						if ($prop['field'] === "meta" && $prop["table"] === "objects")
						{
							$prop['field'] = "metadata";
						}
						// since serialized properites can be several for each field, gather them together first
						$serfs[$prop['field']][$prop['name']] = $propvalues[$prop['name']];
					}
					else
					if ($prop['method'] === "bitmask")
					{
						$val = $propvalues[$prop["name"]];

						if (!isset($seta[$prop["field"]]))
						{
							// jost objects.flags support for now
							$seta[$prop["field"]] = $objdata["flags"];
						}

						// make mask for the flag - mask value is the previous field value with the
						// current flag bit(s) set to zero. flag bit(s) come from prop[ch_value]
						$mask = $seta[$prop["field"]] & (~((int)$prop["ch_value"]));
						// add the value
						$mask |= $val;

						$seta[$prop["field"]] = $mask;;
					}
					else
					{
						$str = $propvalues[$prop["name"]];
						$this->quote($str);
						$seta[$prop["field"]] = $str;
					}

					if ($prop["datatype"] === "int" && $seta[$prop["field"]] == "")
					{
						$seta[$prop["field"]] = "0";
					}
				}
			}

			foreach($serfs as $field => $dat)
			{
				$str = aw_serialize($dat, SERIALIZE_NATIVE);
				$this->quote($str);
				$seta[$field] = $str;
			}
			$sets = join(",",map2("`%s` = '%s'",$seta,0,true));
			if ($sets != "")
			{
				$tbl .= "_versions";
				$q = "UPDATE $tbl SET $sets WHERE version_id = '".$version_id."'";
				$this->db_query($q);
			}
		}

		unset($GLOBALS["__obj_sys_objd_memc"][$objdata["brother_of"]]);
		unset($GLOBALS["__obj_sys_objd_memc"][$objdata["oid"]]);

		unset($this->read_properties_data_cache[$objdata["oid"]]);
		unset($this->read_properties_data_cache[$objdata["brother_of"]]);

		if (!object_loader::opt("no_cache"))
		{
			cache::file_clear_pt("html");
		}
	}

	function load_version_properties($arr)
	{
		extract($arr);
		$ret = array();

		// then read the properties from the db
		// find all the tables that the properties are in
		$tables = array();
		$tbl2prop = array();
		$objtblprops = array();
		foreach($properties as $prop => $data)
		{
			if ($data["store"] === "no")
			{
				continue;
			}

			if ($data["table"] == "")
			{
				$data["table"] = "objects";
			}

			if ($data["table"] !== "objects")
			{
				$tables[$data["table"]] = $data["table"];
				if ($data["store"] != "no")
				{
					$tbl2prop[$data["table"]][] = $data;
				}
			}
			else
			{
				$objtblprops[] = $data;
			}
		}

		// import object table properties in the props array
		foreach($objtblprops as $prop)
		{
			if ($prop["method"] === "serialize")
			{
				// metadata is unserialized in read_objprops
				$ret[$prop["name"]] = isset($objdata[$prop['field']]) && isset($objdata[$prop["field"]][$prop["name"]]) ? $objdata[$prop["field"]][$prop["name"]] : "";
			}
			else
			if ($prop["method"] === "bitmask")
			{
				$ret[$prop["name"]] = ((int)$objdata[$prop["field"]]) & ((int)$prop["ch_value"]);
			}
			else
			{
				$ret[$prop["name"]] = $objdata[$prop["field"]];
			}

			if (isset($prop["datatype"]) && $prop["datatype"] == "int" && $ret[$prop["name"]] == "")
			{
				$ret[$prop["name"]] = "0";
			}
		}

		// fix old broken databases where brother_of may be 0 for non-brother objects
		$object_id = ($objdata["brother_of"] ? $objdata["brother_of"] : $objdata["oid"]);

		$conn_prop_vals = array();
		$conn_prop_fetch = array();

		// do a query for each table
		foreach($tables as $table)
		{
			$fields = array();
			$_got_fields = array();
			foreach($tbl2prop[$table] as $prop)
			{
				if ($prop['field'] === "meta" && $prop["table"] === "objects")
				{
					$prop['field'] = "metadata";
				}

				if ($prop["method"] === "serialize")
				{
					if (!array_key_exists($prop["field"], $_got_fields))
					{
						$fields[] = $table."_versions.`".$prop["field"]."` AS `".$prop["field"]."`";
						$_got_fields[$prop["field"]] = true;
					}
				}
				else
				if ($prop["store"] === "connect")
				{
					$_co_reltype = $prop["reltype"];
					$_co_reltype = $GLOBALS["relinfo"][$objdata["class_id"]][$_co_reltype]["value"];

					if ($_co_reltype == "")
					{
						error::raise(array(
							"id" => "ERR_NO_RT",
							"msg" => sprintf(t("ds_mysql::read_properties(): no reltype for prop %s (%s)"), $prop["name"], $prop["reltype"])
						));
					}

					$conn_prop_fetch[$prop["name"]] = $_co_reltype;
				}
				else
				{
					$fields[] = $table."_versions.`".$prop["field"]."` AS `".$prop["name"]."`";
				}
			}

			if (count($fields) > 0)
			{
				$q = "SELECT ".join(",", $fields)." FROM ".$table."_versions WHERE `version_id` = '".$arr["objdata"]["load_version"]."'";

				$data = $this->db_fetch_row($q);
				if (is_array($data))
				{
					$ret += $data;
				}

				foreach($tbl2prop[$table] as $prop)
				{
					if ($prop["method"] === "serialize")
					{
						if ($prop['field'] === "meta" && $prop["table"] === "objects")
						{
							$prop['field'] = "metadata";
						}

						$unser = aw_unserialize($ret[$prop["field"]], false, true);
						$ret[$prop["name"]] = $unser[$prop["name"]];
					}

					if (isset($prop["datatype"]) && $prop["datatype"] === "int" && $ret[$prop["name"]] == "")
					{
						$ret[$prop["name"]] = "0";
					}
				}
			}
		}


		if (count($conn_prop_fetch))
		{
			$cpf_dat = array();
			if (isset($GLOBALS["read_properties_data_cache_conn"][$object_id]))
			{
				$cfp_dat = $GLOBALS["read_properties_data_cache_conn"][$object_id];
			}
			else
			{
				$q = "
					SELECT
						target,
						reltype
					FROM
						aliases
					LEFT JOIN objects ON objects.oid = aliases.target
					WHERE
						source = '".$object_id."' AND
						reltype IN (".join(",", map("'%s'", $conn_prop_fetch)).") AND
						objects.status != 0
				";
				$this->db_query($q);
				while ($row = $this->db_next())
				{
					$cfp_dat[] = $row;
				}
			}

			foreach($cfp_dat as $row)
			{
				$prop_name = array_search($row["reltype"], $conn_prop_fetch);
				if (!$prop_name)
				{
					error::raise(array(
						"id" => "ERR_NO_PROP",
						"msg" => sprintf(t("ds_mysql::read_properties(): no prop name for reltype %s in store=connect fetch! q = %s"), $row["reltype"], $q)
					));
				}

				$prop = $properties[$prop_name];
				if ($prop["multiple"] == 1)
				{
					$ret[$prop_name][$row["target"]] = $row["target"];
				}
				else
				{
					if (!isset($ret[$prop_name])) // just the first one
					{
						$ret[$prop_name] = $row["target"];
					}
				}
			}
		}
		return $ret;
	}

	function backup_current_version($arr)
	{
		$id = $arr["id"];
		// create a complete copy of the current object to the _versions table

		$table_names = array_keys($arr["tableinfo"]);
		$table_name = reset($table_names)."_versions";
		$table_dat = reset($arr["tableinfo"]);
		$properties = $arr["properties"];
		$tableinfo = $arr["tableinfo"];

		$version_id = gen_uniq_id();
		$this->db_query("INSERT INTO `$table_name` (version_id, $table_dat[index], vers_crea, vers_crea_by) values('$version_id', $id, ".time().", '".aw_global_get("uid")."')");

		$objdata = $this->get_objdata($id);
		$propvalues = $this->read_properties(array(
			"properties" => $properties,
			"tableinfo" => $tableinfo,
			"objdata" => $objdata,
		));
		$objdata["metadata"] = $this->db_fetch_field("SELECT metadata FROM objects WHERE oid = '$id'", "metadata");

		$ot_sets = array();
		$arr["ot_modified"] = $GLOBALS["object_loader"]->all_ot_flds;
		foreach(safe_array($arr["ot_modified"]) as $_field => $one)
		{
			$this->quote($objdata[$_field]);
			$ot_sets[] = " o_".$_field." = '".$objdata[$_field]."' ";
		}

		$ot_sets = join(" , ", $ot_sets);

		$q = "UPDATE `$table_name` SET
			$ot_sets
			WHERE version_id = '".$version_id."'
		";

		$this->db_query($q);

		// now save all properties


		// divide all properties into tables
		$tbls = array();
		foreach($properties as $prop => $data)
		{
			if ($data["store"] !== "no" && $data["store"] !== "connect")
			{
				$tbls[$data["table"]][] = $data;
			}
		}

		// now save all props to tables.
		foreach($tbls as $tbl => $tbld)
		{
			if ($tbl == "")
			{
				continue;
			}

			if ($tbl === "objects")
			{
				continue;
				$tableinfo[$tbl]["index"] = "oid";
				$serfs["metadata"] = $objdata["meta"];
			}
			else
			{
				$serfs = array();
			};
			$seta = array();
			foreach($tbld as $prop)
			{
				// this check is here, so that we won't overwrite default values, that are saved in create_new_object
				if (isset($propvalues[$prop['name']]))
				{
					if ($prop['method'] === "serialize")
					{
						if ($prop['field'] === "meta" && $prop["table"] === "objects")
						{
							$prop['field'] = "metadata";
						}
						// since serialized properites can be several for each field, gather them together first
						$serfs[$prop['field']][$prop['name']] = $propvalues[$prop['name']];
					}
					else
					if ($prop['method'] === "bitmask")
					{
						$val = $propvalues[$prop["name"]];

						if (!isset($seta[$prop["field"]]))
						{
							// jost objects.flags support for now
							$seta[$prop["field"]] = $objdata["flags"];
						}

						// make mask for the flag - mask value is the previous field value with the
						// current flag bit(s) set to zero. flag bit(s) come from prop[ch_value]
						$mask = $seta[$prop["field"]] & (~((int)$prop["ch_value"]));
						// add the value
						$mask |= $val;

						$seta[$prop["field"]] = $mask;;
					}
					else
					{
						$str = $propvalues[$prop["name"]];
						$this->quote($str);
						$seta[$prop["field"]] = $str;
					}

					if (isset($prop["datatype"]) and $prop["datatype"] === "int" and empty($seta[$prop["field"]]))
					{
						$seta[$prop["field"]] = "0";
					}
				}
			}

			foreach($serfs as $field => $dat)
			{
				$str = aw_serialize($dat, SERIALIZE_NATIVE);
				$this->quote($str);
				$seta[$field] = $str;
			}
			$sets = join(",",map2("`%s` = '%s'",$seta,0,true));
			if ($sets != "")
			{
				$tbl .= "_versions";
				$q = "UPDATE $tbl SET $sets WHERE version_id = '".$version_id."'";
				$this->db_query($q);
			}
		}
	}

	function originalize($oid)
	{
		$brof = $this->db_fetch_field("SELECT brother_of from objects where oid = '$oid'", "brother_of");
		$this->db_query("UPDATE objects SET brother_of = '$oid' WHERE brother_of = '$brof'");
		$this->db_query("UPDATE aliases SET source = '$oid' WHERE source = '$brof'");
		$this->db_query("UPDATE aliases SET target = '$oid' WHERE target = '$brof'");
		$this->originalize_cache_update($oid);
	}

	function originalize_cache_update($oid)
	{

	}

	/** returns table.field, for the given prop **/
	function _get_tablefield_from_prop($key, $val=null, $p_tmp=null)
	{
		$tbl = "objects";
		$fld = $key;

		// check for dots in key. if there are any, then we gots some join thingie
		if (strpos($key, ".") !== false)
		{
			list($tbl, $fld) = $this->_do_proc_complex_param(array(
				"key" => &$key,
				"val" => $val,
				"params" => $p_tmp
			));
		}
		elseif (isset($this->properties[$key]) && $this->properties[$key]["store"] !== "no")
		{
			$tbl = $this->properties[$key]["table"];
			$fld = $this->properties[$key]["field"];
			if ($fld == "meta")
			{
				if ($this->properties[$key]["store"] !== "connect")
				{
					$this->meta_filter[$key] = $val;
					continue;
				}
			}
			else
			if ($this->properties[$key]["method"] === "serialize")
			{
				error::raise(array(
					"id" => "ERR_FIELD",
					"msg" => sprintf(t("filter cannot contain properties (%s) that are in serialized fields other than metadata!"), $key)
				));
			}
			$this->used_tables[$tbl] = $tbl;
		}

		if ($tbl != "objects")
		{
			$this->has_data_table_filter = true;
		}
		return $tbl.".`".$fld."`";
	}

	function compile_oql_query($oql)
	{
		// parse into bits
		preg_match("/SELECT(.*)FROM(.*)WHERE(.*?)/imsU", $oql, $mt);

		// now turn it into sql
		$main_clid = constant(trim($mt[2]));
		error::raise_if(!is_class_id($main_clid), array(
			"id" => "ERR_NO_MAIN_CLID",
			"msg" => sprintf(t("object_complex_query::compile_oql_query(): FROM clause has an error, unrecognized clid %s"), $main_clid)
		));

		$this->properties = array();
		$this->tableinfo = array();
		$this->used_tables = array();
		$this->done_ot_js = array();
		$this->_do_add_class_id($main_clid);

		$fetch = $this->_parse_fetch($mt[1], $main_clid);
		list($joins, $where) = $this->_parse_where($mt[3], $main_clid);
		$from = $this->_parse_from($main_clid);

		$q =  "SELECT {$fetch} FROM {$from} {$joins} WHERE {$where}";
		return $q;
	}

	function execute_oql_query($sql)
	{
		$this->db_query($sql);
		$rv = array();
		while ($row = $this->db_next())
		{
			$rv[$row["oid"]] = $row;
		}
		return $rv;
	}

	function _parse_from($main_clid)
	{
		$str = "";
		foreach($GLOBALS["tableinfo"][$main_clid] as $tbl => $dat)
		{
			$str .= " LEFT JOIN $tbl ON $tbl.".$dat["index"]." = ".$dat["master_table"].".".$dat["master_index"]." ";
		}
		return " objects ".$str;
	}

	function _parse_where($str, $main_clid)
	{
		// we have to tokenize things here.
		$p = new parser();
		$p->p_init(trim($str));
		$new_str = " objects.status > 0 AND objects.class_id = {$main_clid} AND ";
		$props = $GLOBALS["properties"][$main_clid];
		while (!$p->p_eos())
		{
			$tok = $p->_p_get_token();
			if (substr($tok, 0, 2) === "CL")
			{
				list($t, $f) = $this->_do_proc_complex_param(array(
					"key" => &$tok,
					"val" => $val,
					"params" => $p_tmp,
				));
				// resolve prop
				$tf = "{$t}.`{$f}`";
				$new_str .= $tf;
			}
			elseif (isset($props[$tok]))
			{
				// also prop
				$tf = $props[$tok]["table"].".`".$props[$tok]["field"]."`";
				$new_str .= $tf;
			}
			elseif ($tok === "?")
			{
				$new_str .= "%s";
			}
			else
			{
				$new_str .= $tok;
			}
		}
		return array(join(" ", $this->joins), $new_str);
	}

	function _parse_fetch($str, $main_clid)
	{
		$props = $GLOBALS["properties"][$main_clid];
		$fetch = array();
		foreach(explode(",", trim($str)) as $prop_fetch)
		{
			if (preg_match("/(.*)\sAS\s(.*)/imsU", $prop_fetch, $pf))
			{
				$fetch[trim($pf[1])] = trim($pf[2]);
			}
			else
			{
				$fetch[trim($prop_fetch)] = trim($prop_fetch);
			}
		}
		$nf = array();
		foreach($fetch as $prop => $as)
		{
			if ($prop === "id")
			{
				$tf = "objects.oid";
			}
			else
			{
				if (!isset($props[$prop]))
				{
					error::raise(array(
						"id" => "ERR_NO_PROP",
						"msg" => sprintf(t("ds_mysql::_parse_fetch(): no property %s in class %s"), $prop, $main_clid)
					));
				}
				$tf = $props[$prop]["table"].".`".$props[$prop]["field"]."`";
			}
			$nf[$tf] = $as;
		}
		$str = array();
		foreach($nf as $tf => $as)
		{
			$str[] = " $tf AS `$as` ";
		}
		return "objects.oid AS oid, objects.parent AS parent, objects.acldata AS acldata, ".join(",", $str);
	}

	private function _add_s($tbl)
	{
		$this->search_tables_used[$tbl] = 1;
	}

	private function _add_join($str)
	{
		$this->joins[] = $str;
	}

	private function _optimize_joins($j, $used)
	{
		$j = trim(substr($j, strlen("objects")));
		$js = explode("\n", str_replace("LEFT JOIN", "\nLEFT JOIN", $j));
		$rs = "objects ";
		foreach($js as $join_line)
		{
			if (trim($join_line) == "")
			{
				continue;
			}
			$joined_table = null;
			if (preg_match("/LEFT JOIN (.*) (.*) ON (.*)\.(.*) = (.*)\.(\S*)/imsU", $join_line, $mt))
			{
				$joined_table = $mt[2];
			}
			else	// no rename table
			if (preg_match("/LEFT JOIN (.*) ON (.*)\.(.*) = (.*)\.(\S+?)/imsU", $join_line, $mt))
			{
				$joined_table = $mt[1];
			}

			if ($joined_table !== null)
			{
				if (isset($this->search_tables_used[trim($joined_table)]) || $joined_table === "documents")
				{
					$rs .= $join_line." ";
				}
			}
		}

		return $rs;
	}

	function property_is_multi_saveable($clid, $prop)
	{
		if (!is_class_id($clid))
		{
			return false;
		}
		// saved to a table and simple type
		list($data) = $this->_get_prop_data($clid, $prop);
		if (empty($data["store"]) && $data["table"] != "objects" && $data["field"] != "" && empty($data["reltype"]))
		{
			return true;
		}
		return false;
	}

	function save_property_multiple($class_id, $prop, $value, $oid_list)
	{
		if (!is_array($oid_list) || !count($oid_list))
		{
			return false;
		}
		list($pd, $tableinfo) = $this->_get_prop_data($class_id, $prop);

		$oid_list_awa = new aw_array($oid_list);
		$table = $pd["table"];
		$field = $pd["field"];
		$value = mysql_real_escape_string($value);

		$table_index = $tableinfo[$table]["index"];

		$sql = " UPDATE $table SET `$field` = '$value' WHERE $table_index IN (".$oid_list_awa->to_sql().") ";
		$this->db_query($sql);
		return true;
	}

	function _get_prop_data($clid, $prop)
	{
		$tmp = obj();
		$tmp->set_class_id($clid);
		$pl = $tmp->get_property_list();

		return array($pl[$prop], $tmp->get_tableinfo());
	}

	/** Returns last executed search query string for debugging purposes
		@attrib api=1 params=pos
		@comment
		@returns string
		@errors none
	**/
	public function last_search_query_string()
	{
		return $this->last_search_query_string;
	}
}
