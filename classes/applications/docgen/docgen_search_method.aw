<?php

class docgen_search_method extends core implements docgen_search_module
{
	function docgen_search_method()
	{
		$this->init();
	}

	function get_module_name()
	{
		return t("Method");
	}
	
	function do_search($arr)
	{
		$this->db_query("SELECT * FROM aw_da_funcs WHERE func like '".$this->quote($arr["search"])."'");
		$ret = array();
		while ($row = $this->db_next())
		{
			$ret[] = sprintf(
				t("method %s defined in class %s"),
				html::href(array(
					"url" => $this->mk_my_orb("class_info", array(
						"file" => str_replace("/classes", "", $row["file"]),
						"disp" => $row["class"],
					), "docgen_viewer")."#fn.".$row["func"],
					"target" => "list",
					"caption" => $row["func"]
				)),
				html::href(array(
					"url" => $this->mk_my_orb("class_info", array(
						"file" => str_replace("/classes", "", $row["file"]),
						"disp" => $row["class"],
					), "docgen_viewer"),
					"target" => "list",
					"caption" => $row["class"]
				))
			);
		}
		return $ret;
	}
}