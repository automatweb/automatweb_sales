<?php

namespace automatweb;

class class_relations_manager
{
	function class_relations_manager()
	{

	}

	/////////////////////////////////////////////////
	// public
	/////////////////////////////////////////////////

	function find_paths_from($from, $to)
	{
		$this->precompute_relation_graph();

		$ps = array();
		foreach($to as $clid)
		{
			if ($clid != $from)
			{
				$ps[] = $this->find_path($from, $clid);
			}
		}

		return $this->join_paths($ps);
	}

	function find_path($from, $to)
	{
		$clss = aw_ini_get("classes");

		// optimize to the best
		// go over the complete tree, find all possible solutions
		$this->fp_beenthere = array();
		$this->_tmp_path = array();
		$this->found_paths = array();
		$this->_req_find_path($from, $to);


		// go over all solutions, find the one with the smallest length
		$ret = array();
		$mc = 1000;
		foreach($this->found_paths as $path)
		{
			$cc = count($path);
			if ($cc < $mc)
			{
				$ret = $path;
				$mc = $cc;
			}
		}

		// now, add the relevant data like tables and properties to join via
		// to the result path
		return $this->final_process_path($ret);
	}

	function precompute_relation_graph()
	{
		// ok, here we just stuff all the possible relations between classes into a damn tree.

		// this->graph will contain clid => $dat
		// where $dat is array of related_clid => $rel_dat
		// $rel_dat says via what property the join can be done or if it has to be done via aliases table

		$this->graph = array();
		$clss = aw_ini_get("classes");
		foreach($clss as $clid => $cld)
		{
			$this->_do_comp_class_rel_graph($clid);
		}

		// now $this->graph contains all possible relations in aw!
		// just for fun, find all classes that are not related to anything :)
		//$this->dump_not_rel_classes();
	}

	function process_fields($fields)
	{
		$ret = array();
		foreach($fields as $class => $clf)
		{
			list($properties, $tableinfo, $relinfo) = $GLOBALS["object_loader"]->load_properties(array(
				"clid" => $class
			));
			foreach($clf as $pn)
			{
				$tbl = $properties[$pn]["table"];
				$fld = $properties[$pn]["field"];
				if ($tbl == "objects")
				{
					reset($tableinfo);
					list($_tbl, $_tbl_dat) = each($tableinfo);
					$tbl = "objects_".$_tbl;
				}
				$ret["fields"][$tbl][$fld] = $fld;
				$ret["map"][$class][$pn] = array("tbl" => $tbl, "fld" => $fld);
			}
		}

		return $ret;
	}

	////////////////////////////////////////////////////////
	// internal!
	////////////////////////////////////////////////////////

	function _req_find_path($from, $to)
	{
		if ($this->fp_beenthere[$from])
		{
			return;
		}
		$this->fp_beenthere[$from] = true;

		array_push($this->_tmp_path, $from);
		$tmp = new aw_array($this->graph[$from]);
		foreach($tmp->get() as $rel_to => $rel_dat)
		{
			if ($rel_to == $to)
			{
				// we got a match!, mark it down
				array_push($this->_tmp_path, $rel_to);
				$this->found_paths[] = $this->_tmp_path;
				array_pop($this->_tmp_path);
			}
		}

		// go deeper as well, just in case
		$tmp = new aw_array($this->graph[$from]);
		foreach($tmp->get() as $rel_to => $rel_dat)
		{
			$this->_req_find_path($rel_to, $to);
		}

		array_pop($this->_tmp_path);
	}

	/** joins several paths together

		@comment

			what this does, is take a bunch of join paths, checks if they contain
			any classes that are in several paths
			and if so, combines the paths together
	**/
	function join_paths($ps)
	{
		// for now, jsut return all and see if we actually need to do
		// this step. we do if we start getting duplicate table errors in sql
		return $ps;
	}

	function _do_comp_class_rel_graph($clid)
	{
		list($properties, $tableinfo, $relinfo) = $GLOBALS["object_loader"]->load_properties(array(
			"clid" => $clid
		));

		$awa = new aw_array($relinfo);
		foreach($awa->get() as $rt => $rd)
		{
			// $rt - relation type, $rd - relation data
			if (is_numeric($rt) || !is_array($rd))
			{
				continue;
			}

			$tmp = new aw_array($rd["clid"]);
			foreach($tmp->get() as $r_clid)
			{
				// now, find a property in $clid properties, that will serve as the join point
				// if it does not exist, mark the join as via aliases table
				$rel_prop = false;
				foreach($properties as $prop => $pd)
				{
					if ($pd["type"] == "relpicker" && $pd["reltype"] == $rt)
					{
						$rel_prop = $pd;
					}
				}

				$via_alias = false;

				if (!$rel_prop)
				{
					$via_alias = true;
				}

				list($n_properties, $n_tableinfo, $n_relinfo) = $GLOBALS["object_loader"]->load_properties(array(
					"clid" => $r_clid
				));

				$this->graph[$clid][$r_clid] = array(
					"via_prop" => $rel_prop,
					"via_alias" => $via_alias,
					"tableinfo" => $n_tableinfo
				);
			}
		}
	}

	function final_process_path($path)
	{
		$first = true;
		$res = array();
		foreach($path as $clid)
		{
			if (!$first)
			{
				$res[] = array(
					"clid" => $clid,
					"jd" => $this->graph[$prev][$clid]
				);
			}
			else
			{
				list($properties, $tableinfo, $relinfo) = $GLOBALS["object_loader"]->load_properties(array(
					"clid" => $clid
				));

				reset($tableinfo);
				list($tbl, $tbl_dat) = each($tableinfo);

				$res[] = array(
					"clid" => $clid,
					"jd" => array(
						"via_prop" => array(
							"table" => $tbl,
							"field" => $tbl_dat["index"]
						),
						"tableinfo" => $tableinfo
					)
				);
			}

			$first = false;
			$prev = $clid;
		}

		return $res;
	}

	/////////////////////////////////////////////////
	// debug and misc
	////////////////////////////////////////////////

	function dump_not_rel_classes()
	{
		$clss = aw_ini_get("classes");

		foreach($this->graph as $clid => $cl_rels)
		{
			foreach($cl_rels as $rel_clid => $rd)
			{
				unset($clss[$rel_clid]);
			}
		}

		foreach($clss as $clid => $cld)
		{
			echo "$clid  (".$cld["name"].") <br>";
		}

	}


}
?>
