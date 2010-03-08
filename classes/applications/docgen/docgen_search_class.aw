<?php

class docgen_search_class extends core implements docgen_search_module
{
	function docgen_search_class()
	{
		$this->init();
	}

	function get_module_name()
	{
		return t("Class");
	}
	
	function do_search($arr)
	{
		$this->db_query("SELECT * FROM aw_da_classes WHERE class_name like '".$this->quote($arr["search"])."'");
		$ret = array();
		while ($row = $this->db_next())
		{
			$ret[] = html::href(array(
				"url" => $this->mk_my_orb("class_info", array(
					"file" => str_replace("/classes", "", $row["file"]),
					"disp" => $row["class_name"],
				), "docgen_viewer"),
				"target" => "list",
				"caption" => $row["class_name"]
			));
		}
		return $ret;
	}
}