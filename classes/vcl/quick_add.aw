<?php

class quick_add extends class_base
{
	function quick_add()
	{
	}

	function init_vcl_property($arr)
	{
		// read props from the given class
		$prop = $arr["prop"];

		$tmp = obj();
		$tmp->set_class_id(@constant($prop["clid"]));
		$pl = $tmp->get_property_list();

		$ret = array();
		foreach($prop["props"] as $p)
		{
			$pn = $prop["name"]."[".$p."]";
			$ret[$pn] = $pl[$p];
			$ret[$pn]["parent"] = $prop["parent"];
			$ret[$pn]["name"] = $pn;
			$ret[$pn]["size"] = 18;
			$ret[$pn]["captionside"] = "top";
			$ret[$pn]["store"] = "class_base";
			$ret[$pn]["value"] = "";
		}
		$ret[$prop["name"]."[sbt]"] = array(
			"type" => "text",
			"name" => $prop["name"]."[sbt]",
			"parent" => $prop["parent"],
			"no_caption" => 1,
			"store" => "class_base",
			"value" => html::submit(array(
				"name" => $prop["name"]."[sbt]",
				"class" => "sbtbutton",
				"value" => t("Lisa")
			))." ".html::submit(array(
				"name" => $prop["name"]."[sbtm]",
				"value" => t("Lisa ja t&auml;ienda"),
				"class" => "sbtbutton"
			))
		);
		return $ret;
	}

	function process_vcl_property($arr)
	{
		// if any of the fields are filled, then do the add thingie
		$prop = $arr["prop"];
		$add = false;
		foreach($prop["props"] as $p)
		{
			if ($prop["value"][$p] != "")
			{
				$add = true;
			}
		}

		if ($add)
		{
			$clss = aw_ini_get("classes");
			$class = basename($clss[@constant($prop["clid"])]["file"]);
			$d = array(
				"class" => $class,
				"action" => "submit",
				"parent" => $arr["obj_inst"]->id(),
			);
			foreach($prop["props"] as $p)
			{
				$d[$p] = $prop["value"][$p];
			}
			$i = get_instance(@constant($prop["clid"]));
			$rv = $i->submit($d);

			if ($prop["value"]["sbtm"] != "")
			{
				header("Location: ".$rv);
				die();
			}

			header("Location: ".$arr["request"]["post_ru"]);
			die();
		}
	}
}
