<?php

class _int_obj_ds_mssql extends _int_obj_ds_base
{
	function _int_obj_ds_mssql()
	{
		$this->init();
		$this->numeric_datatypes = array("relmanager", "relpicker", "date_select","classificator", "datetime_select");
	}

	// returns oid for alias
	// parameters:
	//	alias - required
	//	site_id - optional
	//	parent - optional
	function get_oid_by_alias($arr)
	{
		extract($arr);
		if (isset($arr["parent"]))
		{
			$parent = " AND parent = '".$parent."'";
		}
		if (isset($arr["site_id"]))
		{
			$site_id = " AND site_id = '".$site_id."'";
		}

		$q = sprintf("
			SELECT
				%s
			FROM
				objects WITH (nolock)
			WHERE
				alias = '%s' AND
				status != 0 %s %s
		", OID, $alias, $site_id,$parent);

		return $this->db_fetch_row($q);
	}

	function get_objdata($oid, $param = array())
	{
		$ret = $this->db_fetch_row("SELECT * FROM objects WITH (nolock) WHERE oid = $oid AND status != 0");
		if ($ret === false)
		{
			if ($param["no_errors"])
			{
				return NULL;
			}
			else
			{
				error::raise(array(
					"id" => ERR_NO_OBJ,
					"msg" => sprintf(t("object::load(%s): no such object!"), $oid)
				));
			}
		}

		$ret["meta"] = aw_unserialize($ret["metadata"]);
		unset($ret["metadata"]);

		if ($ret["brother_of"] == 0)
		{
			$ret["brother_of"] = $ret["oid"];
		}

		$tmp = array();
		foreach($ret as $k => $v)
		{
			if (!is_array($v))
			{
				$v = trim($v);
			}
			$tmp[$k] = $v;
		}
		return $tmp;
	}


	// parameters:
	//	properties - property array
	//	tableinfo - tableinfo from propreader
	//	objdata - result of this::get_objdata
	function read_properties($arr)
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
			if ($data["store"] == "no")
			{
				continue;
			}

			if ($data["table"] == "")
			{
				$data["table"] = "objects";
			}

			if ($data["table"] != "objects")
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
			if ($prop["method"] == "serialize")
			{
				// metadata is unserialized in read_objprops
				$ret[$prop["name"]] = $objdata[$prop["field"]][$prop["name"]];
			}
			else
			if ($prop["method"] == "bitmask")
			{
				$ret[$prop["name"]] = ((int)$objdata[$prop["field"]]) & ((int)$prop["ch_value"]);
			}
			else
			{
				$ret[$prop["name"]] = $objdata[$prop["field"]];
			}

			if ($prop["datatype"] == "int" && $ret[$prop["name"]] == "")
			{
				$ret[$prop["name"]] = "0";
			}
		}

		// fix old broken databases where brother_of may be 0 for non-brother objects
		$object_id = ($objdata["brother_of"] ? $objdata["brother_of"] : $objdata["oid"]);

		$conn_prop_vals = array();

		// do a query for each table
		foreach($tables as $table)
		{
			$fields = array();
			$_got_fields = array();
			foreach($tbl2prop[$table] as $prop)
			{
				if ($prop['field'] == "meta" && $prop["table"] == "objects")
				{
					$prop['field'] = "metadata";
				}

				if ($prop["method"] == "serialize")
				{
					if (!$_got_fields[$prop["field"]])
					{
						$fields[] = "[".$table."].[".$prop["field"]."] AS [".$prop["field"]."]";
						$_got_fields[$prop["field"]] = true;
					}
				}
				else
				if ($prop["store"] == "connect")
				{
					// resolve reltype and do find_connections
					$values = array();
					$_co_reltype = $prop["reltype"];
					$_co_reltype = $GLOBALS["relinfo"][$objdata["class_id"]][$_co_reltype]["value"];
					if ($_co_reltype)
					{
						$this->db_query("
							SELECT
								target
							FROM
								aliases WITH (nolock)
								LEFT JOIN objects WITH (nolock) ON objects.oid = aliases.target
							WHERE
								source = '".$object_id."' AND
								reltype = '$_co_reltype' AND
								objects.status != 0
						");
						while ($row = $this->db_next())
						{
							if ($prop["multiple"] == 1)
							{
								$values[$row["target"]] = $row["target"];
							}
							else
							{
								$values = $row["target"];
								break;
							}
						}
					}
					$conn_prop_vals[$prop["name"]] = $values;
					//echo "resolved reltype to ".dbg::dump($_co_reltype)." <br>";
				}
				else
				{
					$fields[] = "[".$table."].[".$prop["field"]."] AS [".$prop["name"]."]";
				}
			}

			if (count($fields) > 0)
			{
				$q = "SELECT ".join(",", $fields)." FROM $table WITH (nolock) WHERE ".$tableinfo[$table]["index"]." = '".$object_id."'";
				if (aw_global_get("uid") == "kix")
				{
					//echo "q = $q <br />";
				}
				$data = $this->db_fetch_row($q);
				if (is_array($data))
				{
					$ret += $data;
				}

				foreach($tbl2prop[$table] as $prop)
				{
					if ($prop["method"] == "serialize")
					{
						if ($prop['field'] == "meta" && $prop["table"] == "objects")
						{
							$prop['field'] = "metadata";
						}

						//echo "unser for prop ".dbg::dump($prop)." <br>";
						$unser = aw_unserialize($ret[$prop["field"]]);
						//echo "unser = ".dbg::dump($unser)." <br>";
						$ret[$prop["name"]] = $unser[$prop["name"]];
					}

					if ($prop["datatype"] == "int" && $ret[$prop["name"]] == "")
					{
						$ret[$prop["name"]] = "0";
					}
				}
			}
		}

		foreach($conn_prop_vals as $prop => $val)
		{
			$ret[$prop] = $val;
			//echo "set $prop => ".dbg::dump($val)." <br>";
		}

		$tmp = array();
		foreach($ret as $k => $v)
		{
			if (!is_array($v))
			{
				$v = trim($v);
			}
			$tmp[$k] = $v;
		}
		return $tmp;
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


			if ($data["table"] == "objects" && $data["field"] == "meta" && !isset($arr["objdata"]["meta"][$data["name"]]) && !empty($data["default"]))
			{
				$arr["objdata"]["meta"][$data["name"]] = $data["default"];
			}
		}

		extract($arr);

		$metadata = aw_serialize($objdata["meta"]);
		$this->quote($metadata);
		$this->quote(&$objdata);

		// create oid
		$oid = $this->db_fetch_field("SELECT max(oid) as oid FROM objects with(tablock)", "oid")+1;

		// set brother to self if not specified.
		if (!$objdata["brother_of"])
		{
			$objdata["brother_of"] = $oid;
		}

		$q = "
			INSERT INTO objects (
				parent,						class_id,						name,						createdby,
				created,					modified,						status,						site_id,
				hits,						lang_id,						comment,					modifiedby,
				jrk,						period,							alias,						periodic,
				metadata,						subclass,					flags,
				oid,						brother_of
		) VALUES (
				'".$objdata["parent"]."',	'".$objdata["class_id"]."',		'".$objdata["name"]."',		'".$objdata["createdby"]."',
				'".$objdata["created"]."',	'".$objdata["modified"]."',		'".$objdata["status"]."',	'".$objdata["site_id"]."',
				'".$objdata["hits"]."',		'".$objdata["lang_id"]."',		'".$objdata["comment"]."',	'".$objdata["modifiedby"]."',
				'".$objdata["jrk"]."',		'".$objdata["period"]."',		'".$objdata["alias"]."',	'".$objdata["periodic"]."',
				'1',						'".$metadata."',				'".$objdata["subclass"]."',	'".$objdata["flags"]."',
				$oid,						$objdata[brother_of]
		)";
		//echo "q = <pre>". htmlentities($q)."</pre> <br />";

		$this->db_query($q);
		//$oid = $this->db_last_insert_id();


		// create all access for the creator
		$this->create_obj_access($oid);


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

			if ($data["table"] == "objects")
			{
				continue;
			}

			if ($data["store"] != "no" && $data["store"] != "connect")
			{
				$tbls[$data["table"]]["index"] = $tableinfo[$data["table"]]["index"];
				// check if the property has a value
				if (isset($objdata["properties"][$prop]))
				{
					$tbls[$data["table"]]["defaults"][$data["field"]] = $objdata["properties"][$prop];
				}
				else
				{
					$tbls[$data["table"]]["defaults"][$data["field"]] = $data["default"];
				}

				if (($data["datatype"] == "int" || in_array($data["type"], $this->numeric_datatypes)) && $tbls[$data["table"]]["defaults"][$data["field"]] == "")
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
					$fds .=",[".$fd."]";
					$vls .=",'".$vl."'";
				}
			}

			$q = "INSERT INTO $tbl (".$idx.$fds.") VALUES('".$oid."'".$vls.")";
			$this->db_query($q);
		}

		return $oid;
	}

	// saves object prtoperties, including all object table fields,
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

		$metadata = aw_serialize($objdata["meta"]);
		$this->quote(&$metadata);
		$this->quote(&$objdata);

		if ($objdata["brother_of"] == 0)
		{
			$objdata["brother_of"] = $objdata["oid"];
		}

		// first, save all object table fields.
		$q = "UPDATE objects SET
			parent = '".$objdata["parent"]."',
			name = '".$objdata["name"]."',
			class_id = '".$objdata["class_id"]."',
			modified = '".$objdata["modified"]."',
			status = '".$objdata["status"]."',
			lang_id = '".$objdata["lang_id"]."',
			comment = '".$objdata["comment"]."',
			modifiedby = '".$objdata["modifiedby"]."',
			jrk = '".$objdata["jrk"]."',
			period = '".$objdata["period"]."',
			alias = '".$objdata["alias"]."',
			periodic = '".$objdata["periodic"]."',
			site_id = '".$objdata["site_id"]."',
			metadata = '".$metadata."',
			subclass = '".$objdata["subclass"]."',
			flags = '".$objdata["flags"]."',
			brother_of = '".$objdata["brother_of"]."'

			WHERE oid = '".$objdata["oid"]."'
		";

//		echo "q = <pre>". htmlentities($q)."</pre> <br />";
		$this->db_query($q);

		// now save all properties


		// divide all properties into tables
		$tbls = array();
		foreach($properties as $prop => $data)
		{
			if ($data["store"] != "no" && $data["store"] != "connect")
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

			if ($tbl == "objects")
			{
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
					if ($prop['method'] == "serialize")
					{
						if ($prop['field'] == "meta" && $prop["table"] == "objects")
						{
							$prop['field'] = "metadata";
						}
						// since serialized properites can be several for each field, gather them together first
						$serfs[$prop['field']][$prop['name']] = $propvalues[$prop['name']];
					}
					else
					if ($prop['method'] == "bitmask")
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
						$this->quote(&$str);
						$seta[$prop["field"]] = $str;
					}

					if (($prop["datatype"] == "int" || in_array($prop["type"], $this->numeric_datatypes)) && $seta[$prop["field"]] == "")
					{
						$seta[$prop["field"]] = "0";
					}
				}
			}

			foreach($serfs as $field => $dat)
			{
				$str = aw_serialize($dat);
				$this->quote($str);
				$seta[$field] = $str;
			}
			$sets = join(",",map2("[%s] = '%s'",$seta,0,true));
			if ($sets != "")
			{
				$q = "UPDATE $tbl SET $sets WHERE ".$tableinfo[$tbl]["index"]." = '".$objdata["brother_of"]."'";
			//	echo "q = <pre>". htmlentities($q)."</pre> <br />";
				$this->db_query($q);
			}
		}
	}

	function read_connection($id)
	{
		return $this->db_fetch_row("
			SELECT
				".$this->connection_query_fetch()."
			FROM
				aliases a WITH (nolock)
				LEFT JOIN objects o_s WITH (nolock) ON o_s.oid = a.source
				LEFT JOIN objects o_t WITH (nolock) ON o_t.oid = a.target
			WHERE
				id = $id
		");
	}

	function save_connection($data)
	{
		if (!$data["type"])
		{
			$data["type"] = $this->db_fetch_field("SELECT class_id FROM objects WITH (nolock) WHERE oid = '".$data["to"]."'", "class_id");
		}

		if ($data["id"])
		{
			$q = "UPDATE aliases SET
				source = '$data[from]',
				target = '$data[to]',
				type = '$data[type]',
				data = '$data[data]',
				idx = '$data[idx]',
				cached = '$data[cached]',
				relobj_id = '$data[relobj_id]',
				reltype = '$data[reltype]',
				pri = '".((int)$data[pri])."'
			WHERE id = '$data[id]'";
			$this->db_query($q);
		}
		else
		{
			if (!$data["idx"])
			{
				$q = "SELECT MAX(idx) as idx FROM aliases where source = '$data[from]' and type = '$data[type]'";
				$data["idx"] = $this->db_fetch_field($q, "idx")+1;
			}

			$data['id'] = $this->db_fetch_field("select max(id) as id from aliases", "id")+1;

			$q = "INSERT INTO aliases (
				source,						target,					type,					data,
				idx,						cached,					relobj_id,				reltype,
				pri,						id
			) VALUES(
				'$data[from]',				'$data[to]',			'$data[type]',			'$data[data]',
				'$data[idx]',				'".((int)$data["cached"])."',		'$data[relobj_id]',		'".((int)$data["reltype"])."',
				'".((int)$data["pri"])."',	 '$data[id]'
			)";
			$this->db_query($q);
			//$data['id'] = $this->db_last_insert_id();
		}

		return $data['id'];
	}

	function delete_connection($id)
	{
		$this->db_query("DELETE FROM aliases WHERE id = '$id'");
	}

	function connection_query_fetch()
	{
		return "a.id as [id],
				a.source as [from],
				a.target as [to],
				a.type as [type],
				a.data as [data],
				a.idx as [idx],
				a.cached as [cached],
				a.relobj_id as [relobj_id],
				a.reltype as [reltype],
				a.pri as pri,
				o_t.lang_id as [to.lang_id],
				o_s.lang_id as [from.lang_id],
				o_t.flags as [to.flags],
				o_s.flags as [from.flags],
				o_t.modified as [to.modified],
				o_s.modified as [from.modified],
				o_t.modifiedby as [to.modifiedby],
				o_s.modifiedby as [from.modifiedby],
				o_t.name as [to.name],
				o_s.name as [from.name],
				o_t.class_id as [to.class_id],
				o_s.class_id as [from.class_id],
				o_t.status as [to.status],
				o_s.status as [from.status]
		";
	}

	// arr - { [from], [to], [type], [class], [to.obj_table_field], [from.obj_table_field] }
	function find_connections($arr)
	{
		$sql = "
			SELECT
				".$this->connection_query_fetch()."
			FROM
				aliases a WITH (nolock)
				LEFT JOIN objects o_s WITH (nolock) ON o_s.oid = a.source
				LEFT JOIN objects o_t WITH (nolock) ON o_t.oid = a.target
			WHERE
				o_s.status != 0 AND
				o_t.status != 0
		";

		if ($arr["from"])
		{
			$awa = new aw_array($arr["from"]);
			$sql .= " AND source IN (".$awa->to_sql().") ";
		}

		if ($arr["to"])
		{
			$awa = new aw_array($arr["to"]);
			$sql .= " AND target IN (".$awa->to_sql().") ";
		}

		if ($arr["type"])
		{
			$awa = new aw_array($arr["type"]);
			$sql .= " AND reltype IN (".$awa->to_sql().") ";
		}

		if ($arr["class"])
		{
			$awa = new aw_array($arr["class"]);
			$sql .= " AND type IN (".$awa->to_sql().") ";
		}

		if ($arr["relobj_id"])
		{
			$awa = new aw_array($arr["relobj_id"]);
			$sql .= " AND relobj_id IN (".$awa->to_sql().") ";
		}

		if ($arr["idx"])
		{
			$awa = new aw_array($arr["idx"]);
			$sql .= " AND idx IN (".$awa->to_sql().") ";
		}

		foreach($arr as $k => $v)
		{
			if (substr($k, 0, 3) == "to.")
			{
				if (is_array($v))
				{
					$sql .= " AND o_t.".substr($k, 3)." IN (" . join(",",$v) . ") ";
				}
				else
				{
					$sql .= " AND o_t.".substr($k, 3)." = '$v' ";
				};
			}
			if (substr($k, 0, 5) == "from.")
			{
				$sql .= " AND o_s.".substr($k, 5)." = '$v' ";
			}
		}

		$sql .= " ORDER BY a.id ";

		$this->db_query($sql);
		$ret = array();
		while ($row = $this->db_next())
		{
			$ret[$row["id"]] = $row;
		}

		return $ret;
	}

	// params:
	//	array of filter parameters
	// if class id is present, properties can also be filtered, otherwise only object table fields
	function search($params)
	{
		$this->used_tables = array();

		$this->properties = array();
		$this->tableinfo = array();

		// load property defs and table defs
		if (isset($params["class_id"]))
		{
			$this->_do_add_class_id($params["class_id"]);
		}

		$this->stat = false;
		$this->sby = "";
		$this->limit = "";
		$this->has_lang_id = false;

		$this->meta_filter = array();
		$this->alias_joins = array();

		$this->joins = array();
		$this->limit_skip = 0;

		$where = $this->req_make_sql($params);

		if (!$this->stat)
		{
			$where .= ($where != "" ? " AND " : "")." objects.status != 0 ";
		}

		if (!isset($params["site_id"]))
		{
			$where .= ($where != "" ? " AND " : "")." objects.site_id = '".aw_ini_get("site_id")."' ";
		}

		if (!$this->has_lang_id)
		{
			$where .= ($where != "" ? " AND " : "")." objects.lang_id = '".aw_global_get("lang_id")."' ";
		}

		// make joins
		$js = array();
		foreach($this->used_tables as $tbl)
		{
			if ($tbl != "objects")
			{
				$js[] = " LEFT JOIN $tbl WITH (nolock) ON $tbl.".$this->tableinfo[$tbl]["index"]." = objects.brother_of ";
			}
		}
		foreach($this->alias_joins as $aj)
		{
			$js[] = " LEFT JOIN aliases $aj[name] WITH (nolock) ON $aj[on] ";
		}
		$joins = join("", $js).join(" ", $this->joins);

		$ret = array();
		if ($where != "")
		{
			if ($GLOBALS["cfg"]["acl"]["use_new_acl"])
			{
				$acld = ", objects.acldata as acldata, objects.parent as parent";
			}
			$q = "SELECT ".$this->limit." objects.oid as oid,objects.name as name,objects.parent as parent  $acld FROM objects WITH (nolock) $joins WHERE $where ".$this->sby;

			$acldata = array();
			$parentdata = array();

			$this->db_query($q);
			while ($row = $this->db_next())
			{
				if ($this->limit_skip > 0)
				{
					$this->limit_skip--;
					continue;
				}
				$ret[$row["oid"]] = $row["name"];
				$parentdata[$row["oid"]] = $row["parent"];
				if ($GLOBALS["cfg"]["acl"]["use_new_acl"])
				{
					$row["acldata"] = safe_array(aw_unserialize($row["acldata"]));
					$acldata[$row["oid"]] = $row;
				}
			}
		}

		// try this - get list of special objects fiest time around
		// then if we later do a search-by-parent, let the class handle it
		/*if (!is_array($GLOBALS["obj_fs_globals"]["container_objs"]))
		{
			$GLOBALS["obj_fs_globals"]["container_objs"] = array();
			list($data) = $this->search(array("class_id" => CL_OTV_DS_POSTIPOISS));
			foreach($data as $d_oid)
			{
				$GLOBALS["obj_fs_globals"]["container_objs"][$d_oid] = obj($d_oid);
			}
		}

		if (!empty($params["parent"]) && !is_array($params["parent"]))
		{
		echo dbg::dump($GLOBALS["obj_fs_globals"]);
			foreach($GLOBALS["obj_fs_globals"]["container_objs"] as $c_oid => $c_obj)
			{
				if ($c_obj->id() == $params["parent"])
				{
					$inst = $c_obj->instance();
					$tmp = $inst->get_objects($c_obj);
					foreach($tmp as $t_id => $t_dat)
					{
						$full_oid = $c_oid.":".$t_id;
						$ret[$full_oid] = $full_oid;
					}
				}
			}
		}*/
		return array($ret, $this->meta_filter, $acldata, $parentdata);
	}

	function delete_object($oid)
	{
		$this->db_query("UPDATE objects SET status = '".STAT_DELETED."', modified = ".time().",modifiedby = '".aw_global_get("uid")."' WHERE oid = '$oid'");
		$this->db_query("DELETE FROM aliases WHERE target = '$oid'");
		$this->db_query("DELETE FROM aliases WHERE source = '$oid'");
	}

	function req_make_sql($params, $logic = "AND")
	{
		$sql = array();
		$p_tmp = $params;
		foreach($params as $key => $val)
		{
			if ($val === NULL)
			{
				continue;
			}

			if ("sort_by" == (string)($key))
			{
				$this->sby = " ORDER BY $val ";
				continue;
			}

			if ("limit" == (string)($key))
			{
				if (strpos($val, ",") !== false)
				{
					// range, like mysql says it - 5,10 means 6-15 - skip 5, return 10
					// so for mysql we select TOP skip + return and then skip the first ones manually
					list($this->limit_skip, $limit_return) = explode(",", trim($val));
				}
				else
				{
					// just limit, convert to TOP
					$this->limit_skip = 0;
					$limit_return = (int)trim($val);
				}
				$this->limit = " TOP $limit_return ";
				continue;
			}

			if ("status" == (string)($key))
			{
				$this->stat = true;
			}

			if ("lang_id" == (string)($key))
			{
				$this->has_lang_id = true;
			}

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
			else
			if (isset($this->properties[$key]) && $this->properties[$key]["store"] != "no")
			{
				$tbl = $this->properties[$key]["table"];
				$fld = $this->properties[$key]["field"];
				if ($fld == "meta")
				{
					$this->meta_filter[$key] = $val;
					continue;
				}
				else
				if ($this->properties[$key]["method"] == "serialize")
				{
					error::raise(array(
						"id" => ERR_FIELD,
						"msg" => sprintf(t("filter cannot contain properties (%s) that are in serialized fields other than metadata!"), $key)
					));
				}
				$this->used_tables[$tbl] = $tbl;
			}

			$tf = "[".$tbl."].[".$fld."]";


			if ($this->properties[$key]["store"] == "connect")
			{
				// join aliases as many-many relation and filter by that
				$this->alias_joins[$key] = array(
					"name" => "aliases_".$key,
					"on" => $tbl.".".$this->tableinfo[$this->properties[$key]["table"]]["index"]." = "."aliases_".$key.".source"
				);
			}

			if (($this->properties[$key]["method"] == "bitmask" || $key == "flags") && is_array($val))
			{
				$sql[] = $tf." & ".$val["mask"]." = ".$val["flags"];
			}
			else
			if (is_object($val))
			{
				$class_name = get_class($val);
				if ($class_name == "object_list_filter")
				{
					if (!empty($val->filter["non_filter_classes"]))
					{
						$this->_do_add_class_id($val->filter["non_filter_classes"], true);
					}
					if (isset($val->filter["logic"]))
					{
						$sql[] = "(".$this->req_make_sql($val->filter["conditions"], $val->filter["logic"]).")";
					}
				}
				else
				if ($class_name == "obj_predicate_not")
				{
					$v_data = $val->data;
					if (is_object($val->data) && get_class($val->data) == "aw_array")
					{
						$v_data = $v_data->get();
					}

					if (is_array($val->data))
					{
						$sql[] = $tf." NOT IN (".join(",", $v_data).") ";
					}
					else
					{
						$sql[] = $tf." != ".$v_data." ";
					}
				}
				else
				if ($class_name == "obj_predicate_compare")
				{
					$v_data = $val->data;
					if (is_object($val->data) && get_class($val->data) == "aw_array")
					{
						$v_data = $v_data->get();
					}

					$comparator = "";
					switch($val->comparator)
					{
						case OBJ_COMP_LESS:
							$comparator = " < ";
							break;

						case OBJ_COMP_GREATER:
							$comparator = " > ";
							break;

						case OBJ_COMP_LESS_OR_EQ:
							$comparator = " <= ";
							break;

						case OBJ_COMP_GREATER_OR_EQ:
							$comparator = " >= ";
							break;

						case OBJ_COMP_BETWEEN:
							$comparator = " > ".$v_data." AND $tf < ";
							$v_data = $val->data2;
							break;

						default:
							error::raise(array(
								"id" => ERR_OBJ_COMPARATOR,
								"msg" => sprintf(t("obj_predicate_compare's comparator operand must be either OBJ_COMP_LESS,OBJ_COMP_GREATER,OBJ_COMP_LESS_OR_EQ,OBJ_COMP_GREATER_OR_EQ. the value supplied, was: %s!"), $val->comparator)
							));
					}

					if (is_array($v_data))
					{
						$tmp = array();
						foreach($v_data as $d_k)
						{
							$tmp[] = $tf." $comparator $d_k ";
						}
						$sql[] = "(".join(" OR ", $tmp).")";
					}
					else
					{
						$sql[] = $tf." $comparator ".$v_data." ";
					}
				}
			}
			else
			if (is_array($val) || (is_object($val) && get_class($val) == "aw_array"))
			{
				if (is_object($val))
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
					$this->quote(&$v);
					if ($this->properties[$key]["store"] == "connect")
					{
						$str[] = " aliases_".$key.".target = '$v' ";
					}
					else
					if (strpos($v, "%") !== false)
					{
						$str[] = $tf." LIKE '".$v."'";
					}
					else
					{
						$str[] = $tf." = '".$v."'";
					}
				}
				$str = join(" OR ", $str);
				if ($str != "")
				{
					$sql[] = " ( $str ) ";
				}
			}
			else
			{
				$this->quote(&$val);
				if ($this->properties[$key]["store"] == "connect")
				{
					$sql[] = " aliases_".$key.".target = '$val' ";
				}
				else
				if ($key == "modified" || $key == "flags")
				{
					// pass all arguments .. &, >, < or whatever the user wants to
					$sql[] = $tf." ".$val;
				}
				else
				if (strpos($val,"%") !== false)
				{
					$sql[] = $tf." LIKE '".$val."'";
				}
				else
				{
					$sql[] = $tf." = '".$val."'";
				}
			}
		}
		return join(" ".$logic." ", $sql);
	}

	function _do_add_class_id($clids, $add_table = false)
	{
		if (!is_array($clids))
		{
			$clids = array($clids);
		}

		foreach($clids as $clid)
		{
			if (!isset($GLOBALS["properties"][$clid]) || !isset($GLOBALS["tableinfo"][$clid]) || !isset($GLOBALS["relinfo"][$clid]))
			{
				list($GLOBALS["properties"][$clid], $GLOBALS["tableinfo"][$clid], $GLOBALS["relinfo"][$clid]) = $GLOBALS["object_loader"]->load_properties(array(
					"file" => ($clid == CL_DOCUMENT ? "doc" : $classes[$clid]["file"]),
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

	function create_brother($arr)
	{
		extract($arr);

		$metadata = aw_serialize($objdata["meta"]);
		$this->quote($metadata);
		$this->quote(&$objdata);

		$objdata["createdby"] = aw_global_get("uid");
		$objdata["created"] = time();

		$objdata["modifiedby"] = aw_global_get("uid");
		$objdata["modified"] = time();

		$objdata["lang_id"] = aw_global_get("lang_id");

		$oid = $this->db_fetch_field("SELECT max(oid) as oid FROM objects", "oid")+1;

		// create oid
		$q = "
			INSERT INTO objects (
				parent,						class_id,						name,						createdby,
				created,					modified,						status,						site_id,
				hits,						lang_id,						comment,					modifiedby,
				jrk,						period,							alias,						periodic,
				metadata,						subclass,					flags,
				brother_of, oid
		) VALUES (
				'".$parent."',				'".$objdata["class_id"]."',		'".$objdata["name"]."',		'".$objdata["createdby"]."',
				'".$objdata["created"]."',	'".$objdata["modified"]."',		'".$objdata["status"]."',	'".$objdata["site_id"]."',
				'".$objdata["hits"]."',		'".$objdata["lang_id"]."',		'".$objdata["comment"]."',	'".$objdata["modifiedby"]."',
				'".$objdata["jrk"]."',		'".aw_global_get("act_per_id")."',		'".$objdata["alias"]."',	'".$objdata["periodic"]."',
				'1',						'".$metadata."',				'".$objdata["subclass"]."',	'".$objdata["flags"]."',
				'".$objdata["oid"]."', $oid
		)";
		//echo "q = <pre>". htmlentities($q)."</pre> <br />";
		$this->db_query($q);
		//$oid = $this->db_last_insert_id();

		// create all access for the creator
		$this->create_obj_access($oid);

		// hits
		$this->db_query("INSERT INTO hits(oid,hits,cachehits) VALUES($oid, 0, 0 )");

		return $oid;
	}

	// $key, $val
	function _do_proc_complex_param($arr)
	{
		extract($arr);

		$filt = explode(".", $key);
		$clid = constant($filt[0]);

		if (!is_class_id($clid))
		{
			if (!is_array($params["class_id"]))
			{
				error::raise_if(!is_class_id($params["class_id"]), array(
					"id" => ERR_OBJ_NO_CLID,
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
			if (count($filt) == 2)
			{
				// so just return the table and field for that class
				$prop = $GLOBALS["properties"][$clid][$filt[1]];
				$this->used_tables[$prop["table"]] = $prop["table"];
				return array($prop["table"], $prop["field"]);
			}
		}

		$this->foo = array();
		$this->_req_do_pcp($filt, 1, $clid, $arr);

		$this->joins = array();

		// join all other tables from the starting class except the objects table
		$tmp = $GLOBALS["tableinfo"][$clid];
		unset($tmp["objects"]);
		foreach($tmp as $tbl => $tbldat)
		{
			$this->joins[] = " LEFT JOIN $tbl ".$tbl."_".$clid." ON ".$tbl."_".$clid.".".$tbldat["index"]." = ".$tbldat["master_table"].".".$tbldat["master_index"]." ";
		}

		// now make joins and for the final prop, query
		foreach($this->join_data as $pos => $join)
		{
			if ($join["via"] == "rel")
			{
				// from prev to alias from alias to obj
				$prev_t = $join["table"]."_".$join["from_class"];
				$prev_clid = $join["from_class"];

				$str  = " LEFT JOIN aliases aliases_".$join["from_class"]." ON aliases_".$join["from_class"].".source = ";
				if ($join["from_class"] == $clid)
				{
					$str .= " objects.oid ";
				}
				else
				{
					$str .= " objects_".$join["from_class"].".oid ";
				}
				$this->joins[] = $str;

				$str  = " LEFT JOIN objects objects_".$join["to_class"]." ON aliases_".$join["from_class"].".target = ";
				$str .= " objects_".$join["to_class"].".oid ";
				$prev_clid = $join["to_class"];
				$this->joins[] = $str;
			}
			else	// via prop
			{
				if (!$join["to_class"])
				{
					$prev_t = $join["table"]."_".$prev_clid;
					$ret = array(
						$prev_t,
						$GLOBALS["properties"][$join["from_class"]][$join["prop"]]["field"],
					);
					continue;
				}

				// if the next stop is a property
				// then join all the tables in that class
				// first the objects table

				$prev_t = $join["table"]."_".$join["from_class"];
				$prev_clid = $join["from_class"];
				$this->joins[] = " LEFT JOIN objects objects_".$join["from_class"]." ON objects_".$join["from_class"].".oid = $prev_t.".$join["field"]." ";
			}
		}

		$arr["key"] = $filt[count($filt)-1];

		return $ret;
	}

	function _req_do_pcp($filt, $pos, $cur_clid, $arr)
	{
		$pp = $filt[$pos];

		// if the next param is RELTYPE_* then via relation
		// else, if it is property for cur class - via property
		// else - throw up

		if (substr($pp, 0, 8) == "RELTYPE_")
		{
			$reltype_id = $GLOBALS["relinfo"][$cur_clid][$pp]["value"];
			error::raise_if(!$reltype_id, array(
				"id" => ERR_OBJ_NO_RELATION,
				"msg" => sprintf(t("ds_mysql::_req_do_pcp(): no relation from class %s named %s"), $cur_clid, $pp)
			));

			// calc new class id
			$new_clid = $GLOBALS["relinfo"][$cur_clid][$pp]["clid"][0];

			$this->join_data[] = array(
				"via" => "rel",
				"reltype" => $reltype_id,
				"from_class" => $cur_clid,
				"to_class" => $new_clid
			);
		}
		else
		{
			if (!isset($GLOBALS["properties"][$cur_clid]))
			{
				$classes = aw_ini_get("classes");
				list($GLOBALS["properties"][$cur_clid], $GLOBALS["tableinfo"][$cur_clid], $GLOBALS["relinfo"][$cur_clid]) = $GLOBALS["object_loader"]->load_properties(array(
					"file" => ($cur_clid == CL_DOCUMENT ? "doc" : basename($classes[$cur_clid]["file"])),
					"clid" => $cur_clid
				));
			}

			error::raise_if(!is_array($GLOBALS["properties"][$cur_clid][$pp]), array(
				"id" => ERR_OBJ_NO_PROP,
				"msg" => sprintf(t("ds_mysql::_req_do_pcp(): no property %s in class %s "), $pp, $cur_clid)
			));

			$cur_prop = $GLOBALS["properties"][$cur_clid][$pp];

			$table = $cur_prop["table"];
			$field = $cur_prop["field"];

			// if it is the last one, then it can be anything
			if ($pos < (count($filt) - 1))
			{
				error::raise_if($cur_prop["type"] != "relpicker", array(
					"id" => ERR_OBJ_NO_RP,
					"msg" => t("ds_mysql::_req_do_pcp(): currently join properties can only be of type relpicker - can't figure out the class id of the object-to-join otherwise")
				));

				error::raise_if($cur_prop["method"] == "serialize", array(
					"id" => ERR_OBJ_NO_META,
					"msg" => sprintf(t("ds_mysql::_req_do_pcp(): can not join classes on serialized fields (property %s in class %s)"), $pp, $cur_clid)
				));

				switch ($cur_prop["type"])
				{
					case "relpicker":
						$relt_s = $cur_prop["reltype"];
						$relt = $GLOBALS["relinfo"][$cur_clid][$relt_s]["value"];

						error::raise_if(!$relt, array(
							"id" => ERR_OBJ_NO_REL,
							"msg" => sprintf(t("ds_mysql::_req_do_pcp(): no reltype %s in class %s , got reltype from relpicker property %s"), $relt_s, $cur_clid, $cur_prop["name"])
						));

						$new_clid = $GLOBALS["relinfo"][$cur_clid][$relt_s]["clid"][0];
						break;

					default:
						error::raise(array(
							"id" => ERR_OBJ_W_TP,
							"msg" => sprintf(t("ds_mysql::_req_do_pcp(): incorrect prop type! (%s)"), $cur_prop["type"])
						));
				}
			}

			$this->join_data[] = array(
				"via" => "prop",
				"prop" => $pp,
				"from_class" => $cur_clid,
				"to_class" => $new_clid,
				"table" => $table,
				"field" => $field
			);
		}

		if ($pos < (count($filt)-1))
		{
			$this->_req_do_pcp($filt, $pos+1, $new_clid, $arr);
		}
	}
}
