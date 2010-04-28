<?php

namespace automatweb;

// complex queries that return object lists or data fittable to vcl table

class object_complex_query extends core
{
	function object_complex_query()
	{
		// init or something
		$this->init();
	}


	/** compiles a search by result set, figures out all the joins and crap by itself, or at least tries to

		@comment

			$arr is array of results, like this:
				array(
					"start_from" => CL_DOCUMENT,
					"fields" =>
						array(
							CL_DOCUMENT => array("title", "content"),
							CL_FILE => array("name")
						)
				)

			returns a compiled query id, that you can feed to the execute_query function
	**/
	function compile_search_by_result_set($arr)
	{
		// start from start from, find relations, join tables, create query template. badabimm, badaboom, and that's it
		error::raise_if(!isset($arr["fields"][$arr["start_from"]]), array(
			"id" => ERR_PARAM,
			"msg" => t("object_complex_query::compile_search_by_result_set(): start_from class has no fields in the fields array!")
		));

		$tbls = array();

		$classes = aw_ini_get("classes");

		$clrm = get_instance("core/obj/class_relations_manager");
		$paths = $clrm->find_paths_from($arr["start_from"], array_keys($arr["fields"]));
		$fields = $clrm->process_fields($arr["fields"]);

		// ok, now we got the paths from one class to the next
		// now we need to get into actual sql territory
		// try to put it together in this class and then later move as little as possible
		// of the db specific stuff to the db driver class

		$qd = $this->_do_compile_search_from_paths($paths, $fields);
		return $this->_store_q($qd);
	}


	/** compiles search by parameter list - parameters specify search parameters and joins

		@comment

			$arr - array of filter parameters and how to join objects, like this:

				array(
					"where" => array(
						"start_from" => CL_DOCUMENT,
						"fields" =>
							array(
								CL_DOCUMENT => array("title", "content"),
								CL_CRM_ADDRESS => array("name"),
								CL_CRM_CITY => array("name"),
								CL_IMAGE => array("name")
							)
					),
					"joins" => array(
						array(
							array(
								"clid" => CL_CRM_ADDRESS,
								"via_alias" => true
							),
							array(
								"clid" => CL_CRM_CITY,
								"via_prop" => "linn"
							)
						),
						array(
							array(
								"clid" => CL_IMAGE,
								"via_alias" => true
							)
						)
					)
				)
	**/
	function compile_search_by_param($arr)
	{
		// start from start from, create query template. badabimm, badaboom, and that's it
		error::raise_if(!isset($arr["where"]["fields"][$arr["where"]["start_from"]]), array(
			"id" => ERR_PARAM,
			"msg" => t("object_complex_query::compile_search_by_param(): start_from class has no fields in the fields array!")
		));

		$tbls = array();

		$classes = aw_ini_get("classes");

		$clrm = get_instance("core/obj/class_relations_manager");
		$fields = $clrm->process_fields($arr["where"]["fields"]);

		$paths = $this->rewrite_paths($arr["where"]["start_from"], $arr["joins"]);

		// ok, now we got the paths from one class to the next
		// now we need to get into actual sql territory
		// try to put it together in this class and then later move as little as possible
		// of the db specific stuff to the db driver class

		$qd = $this->_do_compile_search_from_paths($paths, $fields);
		return $this->_store_q($qd);
	}

	/** executes a compiled search and returns an object_list of result objects

		@comment

			$qid - identifier returned by compile_search_by_result_set
			$params - array of values for search parameters, example:
				array(
					CL_DOCUMENT => array(
						"name" => "%foo%"
					),
					CL_IMAGE => array(
						"name" => array(
							"a", "b"
						)
					)
				)

			returns an object_list instance populated by result sets
	**/
	function exec_search_ol($qid, $values, $dbg = false)
	{
		$sql = $this->_get_exec_q($qid, $values);
		if ($dbg)
		{
			echo "$quid sql = $sql <br>";
		}
		$ol = new object_list();
		$this->db_query($sql);
		while ($row = $this->db_next())
		{
			$ol->add($row["oid"]);
		}
		return $ol;
	}

	/** executes a compiled search and inserts the results into the given vcl table

		@comment

			$qid - identifier returned by compile_search_by_result_set
			$params - array of values for search parameters, example:
				array(
					CL_DOCUMENT => array(
						"name" => "%foo%"
					),
					CL_IMAGE => array(
						"name" => array(
							"a", "b"
						)
					)
				)


			returns the number of results
	**/
	function exec_search_vcl($qid, $values, &$tbl)
	{
	}

	/** compiles and executes a search with auto-joined tables (search by result columns)

		@comment

			$fields - array of searchable fields, format is the same as passed to compile_search_by_result
			$values - array of values to search for, format is the same as passed to exec_search_ol

			returns an object_list with the result objects
	**/
	function search_by_results_ol($fields, $values)
	{
		$qid = $this->compile_search_by_result($fields);
		return $this->exec_search_ol($qid, $values);
	}

	/** compiles and executes a search with auto-joined tables (search by result columns)

		@comment

			$fields - array of searchable fields, format is the same as passed to compile_search_by_result
			$values - array of values to search for, format is the same as passed to exec_search_ol
			$tbl - vcl table instance to insert results to

			inserts the results into a vcl table. table column names must be class_name.property, for example document.title
	**/
	function search_by_results_vcl($fields, $values, &$tbl)
	{
		$qid = $this->compile_search_by_result($fields);
		return $this->exec_search_vcl($qid, $values, $tbl);
	}


	///////////////////////////////////////////////////
	// private!
	//////////////////////////////////////////////////

	function _do_compile_search_from_paths($paths, $fields)
	{
		list($joins, $first_tbl_idx) = $this->_do_compile_search_joins($paths);
		$where = $this->_do_compile_where($fields);
		return array(
			"joins" => $joins,
			"where" => $where["sql"],
			"map" => $where["map"],
			"first_tbl_idx" => $first_tbl_idx
		);
	}

	function _do_compile_search_joins($paths)
	{
		$sql = "";
		$first_tbl_idx = "";
		foreach($paths as $path)
		{
			foreach($path as $jd)
			{
				reset($jd["jd"]["tableinfo"]);
				list($new_tbl, $tbl_dat) = each($jd["jd"]["tableinfo"]);
				$new_fld = $tbl_dat["index"];

				if ($sql == "")
				{
					$sql = "$new_tbl LEFT JOIN objects AS objects_".$new_tbl." ON objects_".$new_tbl.".oid = ".$new_tbl.".".$new_fld." ";
					$first_tbl_idx = $new_tbl.".".$new_fld;
				}
				else
				{
					if ($jd["via_alias"])
					{
						$sql .= " LEFT JOIN aliases ON $jd[clid].idx = $prev[clid].idx LEFT JOIN realtable ";
					}
					else
					{
						// via property

						$prev_tbl = $jd["jd"]["via_prop"]["table"];
						$prev_fld = $jd["jd"]["via_prop"]["field"];

						$sql .= "
							LEFT JOIN $new_tbl ON ".$new_tbl.".".$new_fld." = $prev_tbl.$prev_fld
							LEFT JOIN objects AS objects_$new_tbl ON objects_".$new_tbl.".oid = ".$new_tbl.".".$new_fld."
						";
					}
				}
			}
		}
		return array($sql,$first_tbl_idx);
	}

	function _do_compile_where($fields)
	{
		$sql = array();
		foreach($fields["fields"] as $tbl => $tf)
		{
			foreach($tf as $fld)
			{
				$sql[] = " {VAR:".$tbl.".".$fld."}";
			}
		}
		return array("sql" => join(" AND ", $sql), "map" => $fields["map"]);
	}

	function _store_q($param)
	{
		static $q_store;
		if (is_array($param))
		{
			$id = gen_uniq_id();
			$q_store[$id] = $param;
			return $id;
		}
		else
		{
			return $q_store[$param];
		}
	}

	function _get_exec_q($qid, $values)
	{
		$qd = $this->_store_q($qid);
		// replace vars in where
		$where = $qd["where"];
		foreach($values as $clid => $clvs)
		{
			foreach($clvs as $key => $value)
			{
				$mapt = $qd["map"][$clid][$key];
				$varn = "{VAR:".$mapt["tbl"].".".$mapt["fld"]."}";
				$where = str_replace($varn, $this->_get_sql_where_clause($mapt["tbl"], $mapt["fld"], $value), $where);
			}
		}
		$sql = "SELECT $qd[first_tbl_idx] AS oid FROM $qd[joins] WHERE $where";
		return $sql;
	}

	function _get_sql_where_clause($tbl, $fld, $value)
	{
		return $tbl.".".$fld." LIKE '$value'";
	}

	function rewrite_paths($start_from, $joins)
	{
		$ret = array();
		foreach($joins as $path)
		{
			// the first component is implied to be class $start_from
			list($n_properties, $n_tableinfo, $n_relinfo) = $GLOBALS["object_loader"]->load_properties(array(
				"clid" => $start_from
			));
			reset($n_tableinfo);
			list($tbl, $tbl_dat) = each($n_tableinfo);

			$prev = $start_from;
			$tmp = array();

			$tmp[] = array(
				"clid" => $start_from,
				"jd" => array(
					"via_prop" => array(
						"table" => $tbl,
						"field" => $tbl_dat["index"]
					),
					"tableinfo" => $n_tableinfo
				)
			);
			foreach($path as $component)
			{
				list($n_properties, $n_tableinfo, $n_relinfo) = $GLOBALS["object_loader"]->load_properties(array(
					"clid" => $prev
				));

				list($properties, $tableinfo, $relinfo) = $GLOBALS["object_loader"]->load_properties(array(
					"clid" => $component["clid"]
				));

				$tmp[] = array(
					"clid" => $component["clid"],
					"jd" => array(
						"via_alias" => $component["via_alias"],
						"via_prop" => $n_properties[$component["via_prop"]],
						"tableinfo" => $tableinfo
					)
				);
				$prev = $component["clid"];
			}

			$ret[] = $tmp;
		}
		return $ret;
	}
}
?>
