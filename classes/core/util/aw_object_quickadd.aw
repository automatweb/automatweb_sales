<?php
// aw_object_quickadd.aw - Generates javascript array of all objects one can add for "aw object quickadd" javascript
/*
@classinfo  maintainer=hannes
*/
class aw_object_quickadd extends class_base
{

	/** outputs file

		@attrib name=get_objects

		@returns

		@comment
	**/
	function get_objects_as_js_array()
	{
		ob_start ("ob_gzhandler");
		header ("Content-type: text/javascript; charset: UTF-8");
		header("Expires: ".gmdate("D, d M Y H:i:s", time()+43200)." GMT");
		header("Cache-Control: max-age=315360000");
		echo $this->get_objects_json();
		die();
	}

	// gets objects to array
	function get_objects($tb, $i_parent)
	{
		$a_items = array();
		$atc = new add_tree_conf();

		// although fast enough allready .. caching makes it 3 times as fast
		$tree = cache::file_get("aw_object_quickadd_cache_".aw_global_get("uid"));
		$tree = unserialize($tree);

		if(!is_array($tree))
		{
			$tree = $atc->get_class_tree(array(
				"docforms" => 1,
				// those are for docs menu only
				"parent" => "--p--",
			));
			cache::file_set("aw_object_quickadd_cache_".aw_global_get("uid"), serialize($tree));
		}

		foreach($tree as $item_id => $item_collection)
		{
			foreach($item_collection as $el_id => $el_data)
			{
				$parnt = $item_id === "root" ? "new" : $item_id;

				if ($el_data["clid"])
				{
					$url = $this->mk_my_orb("new",array("parent" => "--p--"),$el_data["clid"]);
					$url = str_replace(aw_ini_get("baseurl")."/automatweb/orb.aw", "", $url);
					$name = $el_data["name"];
					$a_items[strtolower(substr($name,0,1))][] = array(
						"name" => $name,
						"url" => $url,
						"icon" =>  icons::get_icon_url($el_data["id"]),
						"id" => $el_data["id"],
						"class" => substr($el_data["def"],3)
					);
				}
				elseif ($el_data["link"])
				{
					$url = str_replace(aw_ini_get("baseurl")."/automatweb/orb.aw", "", $el_data["link"]);
					$name = $el_data["name"];
					$a_items[strtolower(substr($name,0,1))][] = array(
						"name" => $name,
						"url" => $url,
						"icon" =>  icons::get_icon_url($el_data["id"]),
						"id" => $el_data["id"],
						"class" => "DOCUMENT",
					);
				}
			}
		}
		return $a_items;
	}

	function get_objects_to_js_array($arr)
	{
		if (empty($arr["pack"]))
		{
			$line_prefix = "\n";
		}

		$a_items = $this->get_objects();
		$a_out = array();
		$a_out[] = 'var items = [';
		foreach ($a_items as $key => $value)
		{
			$index = 0;
			foreach ($a_items[$key] as $key2 => $value2)
			{
				$a_out[] = '{name: "'.html_entity_decode ($a_items[$key][$key2]["name"]).'",';
				$a_out[] = 'url_obj: "'.$a_items[$key][$key2]["url"].'",'.$line_prefix;
				$a_out[] = 'icon: "'.$a_items[$key][$key2]["icon"].'",'.$line_prefix;
				$a_out[] = '"class": "'.$a_items[$key][$key2]["class"].'"'.$line_prefix;
				//$s_out .= 'priority: 5,'.$line_prefix;
				$a_out[] = '},'.$line_prefix;
			}
		}
		$a_out[count($a_out)-1] = "}";
		$a_out[] = "]";
		return implode  ( "",  $a_out);
	}

	function get_objects_json($arr)
	{
		if (empty($arr["pack"]))
		{
			$line_prefix = "\n";
		}

		$a_items = $this->get_objects();

		$d = array();

		$a_out = array();
		$a_out[] = 'var items = [';
		foreach ($a_items as $key => $value)
		{
			$index = 0;
			foreach ($a_items[$key] as $key2 => $value2)
			{
				$tmp = array();
				$tmp["name"] = html_entity_decode ($a_items[$key][$key2]["name"]);
				$tmp["url_obj"] = $a_items[$key][$key2]["url"];
				$tmp["icon"] = $a_items[$key][$key2]["icon"];
				$tmp["class"] = $a_items[$key][$key2]["class"];
				$tmp["priority"] = 5;
				$d[] = $tmp;

				$a_out[] = '{name: "'.html_entity_decode ($a_items[$key][$key2]["name"]).'",';
				$a_out[] = 'url_obj: "'.$a_items[$key][$key2]["url"].'",'.$line_prefix;
				$a_out[] = 'icon: "'.$a_items[$key][$key2]["icon"].'",'.$line_prefix;
				$a_out[] = '"class": "'.$a_items[$key][$key2]["class"].'"'.$line_prefix;
				$a_out[] = '},'.$line_prefix;
			}
		}
		return json_encode($d);
		$a_out[count($a_out)-1] = "}";
		$a_out[] = "]";
		return implode  ( "",  $a_out);
	}
}
?>
