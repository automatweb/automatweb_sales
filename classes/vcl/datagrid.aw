<?php

namespace automatweb;

class datagrid extends aw_template
{
	function init_vcl_property($arr)
	{
		$t = new vcl_table;
		$this->_init_table_fields($t, $arr["prop"]);
		$this->_init_table_contents($t, $arr["prop"], $arr["obj_inst"]);
		$t->set_sortable(false);
		return array(
			$arr["prop"]["name"] => array(
				"name" => $arr["prop"]["name"],
				"type" => "text",
				"value" => $t->draw(),
				"no_caption" => 1
			)
		);
	}

	private function _init_table_contents($t, $p, $o)
	{
		$idx_field = $p["field"];
		foreach(safe_array($o->prop($p["name"])) as $row)
		{
			$d = array();
			foreach(safe_array($p["fields"]) as $idx => $field)
			{
				$d[$field] = html::textbox(array(
					"name" => "datagrid[".$p["name"]."][".$row[$idx_field]."][$field]",
					"value" => $row[$field]
				));
			}
			$t->define_data($d);
		}

		foreach(safe_array($p["fields"]) as $idx => $field)
		{
			$d[$field] = html::textbox(array(
				"name" => "datagrid[".$p["name"]."][-1][$field]",
				"value" => ""
			));
		}
		$t->define_data($d);
	}

	private function _init_table_fields($t, $p)
	{
		foreach(safe_array($p["fields"]) as $idx => $field)
		{
			$t->define_field(array(
				"name" => $field,
				"caption" => $p["captions"][$idx],
				"align" => "center"
			));
		}
	}

	function process_vcl_property($arr)
	{
		$d = $arr["request"]["datagrid"][$arr["prop"]["name"]];
		// remove all empty rows from the d
		foreach($d as $idx => $row)
		{
			$has = false;
			foreach($row as $k => $v)
			{
				if (trim($v) != "")
				{
					$has = true;
				}
			}
			if (!$has)
			{
				unset($d[$idx]);
			}
		}
		$arr["prop"]["value"] = $d;
	}
}
?>
