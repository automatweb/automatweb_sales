<?php

class docgen_search_use_func extends core implements docgen_search_module, docgen_index_module
{
	function docgen_search_use_func()
	{
		$this->init();
	}

	function get_module_name()
	{
		return t("Method usage");
	}

	function do_search($arr)
	{
		$s = $arr["search"];
		$this->quote($s);
		if (strpos($s, "::") !== false)
		{
			list($class, $method) = explode("::", $s);
			$q = "SELECT * FROM aw_da_callers WHERE callee_class = '$class' AND callee_func = '$method'";
		}
		else
		{
			$q = "SELECT * FROM aw_da_callers WHERE callee_func = '$s'";
		}

		$ret = array();
		$this->db_query($q);
		while ($row = $this->db_next())
		{
			$tmp = aw_ini_get("classes");
			foreach($tmp as $tclass)
			{
				if (basename($tclass["file"]) == $row["caller_class"])
				{
					$cl_file = "/".$tclass["file"].".aw";
				}
			}

			$caller_class_file = empty($row["caller_class"]) ? t("n/a") : $this->_r(class_index::get_file_by_name($row["caller_class"]));
			$callee_class_file = empty($row["callee_class"]) ? t("n/a") : $this->_r(class_index::get_file_by_name($row["callee_class"]));

			$ret[] = sprintf(
					t("%s::%s on line %s called %s::%s"),
					html::href(array(
						"url" => $this->mk_my_orb("class_info", array(
							"file" => $caller_class_file,
							"disp" => $row["caller_class"]
						), "docgen_viewer"),
						"target" => "list",
						"caption" => $row["caller_class"]
					)),
					html::href(array(
						"url" => $this->mk_my_orb("class_info", array(
							"file" => $caller_class_file,
							"disp" => $row["caller_class"],
						), "docgen_viewer")."#fn.".$row["caller_func"],
						"target" => "list",
						"caption" => $row["caller_func"]
					)),
					html::href(array(
						"url" => $this->mk_my_orb("view_source", array(
							"file" => $caller_class_file,
							"v_class" => $row["caller_class"],
							"func" => $row["caller_func"]
						), "docgen_viewer")."#line.".$row["caller_line"],
						"target" => "list",
						"caption" => $row["caller_line"]
					)),
					html::href(array(
						"url" => $this->mk_my_orb("class_info", array(
							"file" => $callee_class_file,
							"disp" => $row["callee_class"]
						), "docgen_viewer"),
						"target" => "list",
						"caption" => $row["callee_class"]
					)),
					html::href(array(
						"url" => $this->mk_my_orb("class_info", array(
							"file" => $callee_class_file,
							"disp" => $row["callee_class"],
						), "docgen_viewer")."#fn.".$row["callee_func"],
						"target" => "list",
						"caption" => $row["callee_func"]
					))
			);
		}
		return $ret;
	}

	private function _r($f)
	{
		return str_replace(aw_ini_get("classdir"), "", $f);
	}

	function handle_index_start()
	{
		$this->db_query("DELETE FROM aw_da_callers");
	}

	function handle_index_file($rel_file, $data)
	{
	}

	function handle_index_class($rel_file, $class, $c_data)
	{
	}

	function handle_index_method($rel_file, $class, $fname, $fdata)
	{
		// caller data
		if (isset($fdata["local_calls"]))
		{
			foreach(safe_array($fdata["local_calls"]) as $calld)
			{
				$calld["class"] = isset($calld["class"]) ? basename($calld["class"]) : "";
				$class = basename($class);
				$this->db_query("
					INSERT INTO
						aw_da_callers(
							caller_class,			caller_func,			caller_line,
							callee_class,			callee_func
						)
						values(
							'$class',				'$fname',				'".$calld["line"]."',
							'$class',				'".$calld["func"]."'
						)
				");
			}
		}

		if (isset($fdata["foreign_calls"]))
		{
			foreach(safe_array($fdata["foreign_calls"]) as $calld)
			{
				$calld["class"] = isset($calld["class"]) ? basename($calld["class"]) : "";
				$class = basename($class);
				$this->db_query("
					INSERT INTO
						aw_da_callers(
							caller_class,			caller_func,			caller_line,
							callee_class,			callee_func
						)
						values(
							'$class',				'$fname',				'".$calld["line"]."',
							'".$calld["class"]."',				'".$calld["func"]."'
						)
				");
			}
		}
	}
}
