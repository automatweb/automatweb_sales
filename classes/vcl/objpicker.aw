<?php

/*
@classinfo maintainer=voldemar
*/

class objpicker extends core implements vcl_interface
{
	/**
		@attrib params=name api=1

		@param name required type=string
			String to indetify the object picker. Chars: A-z, 0-9, _ (first character not numeric)

		@param object required type=object
			The object the picker picks objects for

		@param clid required type=int
			Class id-s of objects to be picked from. Default is empty array, meaning any class object can be picked. If not specified, options must be defined or mode 'autocomplete'

		@param mode optional type=string default=text
			Values: "text", "select"

		@param can_add optional type=bool default=false
			Whether user can add new objects (by add button in select mode or by object name in text mode)

		@param can_edit optional type=bool default=false
			Whether user can edit selected object in select mode (not applicable in text mode)

		@param can_delete optional type=bool default=false
			Whether user can delete selected object in select mode (not applicable in text mode)

		@returns string
			The HTML of the object picker.
	**/
	public static function create($args)
	{
	}

	public function init_vcl_property($args)
	{
		$prop = $args["property"];
		$name = $prop["name"];
		$mode = (isset($prop["mode"]) and "select" === $prop["mode"]) ? "select" : "text";

		if ("text" === $mode)
		{
			if (is_oid($args["obj_inst"]->prop($name)))
			{
				$o = new object($args["obj_inst"]->prop($name));
				$value = $o->prop_xml("name");
				$data_element = html::hidden(array("name" => $name, "value" => $o->id()));
			}
			elseif (isset($prop["value"]) and is_oid($prop["value"]))
			{
				$o = new object($prop["value"]);
				$value = $o->prop_xml("name");
				$data_element = html::hidden(array("name" => $name, "value" => $o->id()));
			}
			else
			{
				$value = "";
				$data_element = html::hidden(array("name" => $name, "value" => ""));
			}

			if (empty($args["view"]) and empty($prop["disabled"]))
			{
				$size = isset($args["size"]) ? $args["size"] : "";
				$input_element = html::textbox(array("name" => "{$name}__autocompleteTextbox", "value" => $value, "size" => $size));
				$clids = is_array($prop["clid"]) ? implode(",", $prop["clid"]) : $prop["clid"];

				load_javascript("bsnAutosuggest.js");

				if (!empty($prop["options_callback"]))
				{
					preg_match("/([a-z0-9_]+)::([a-z0-9_]+)(\((([a-z0-9_]+),?)+\))?/i", $prop["options_callback"], $matches);

					if (empty($matches[1]) or empty($matches[2]))
					{
						throw new awex_vcl_objpicker_arg("Invalid options callback specification");
					}

					$class = $matches[1];
					$method = $matches[2];

					if (!empty($matches[3]))
					{
						$params = explode(",", substr($matches[3], 1, -1));
					}
					else
					{
						$params = array();
					}
				}
				else
				{
					$class = "objpicker";
					$method = "get_options";
				}

				$name_options_url = $this->mk_my_orb($method, array("clids" => $clids, "id" => $args["id"]), $class);
				$autocomplete_js = <<<SCRIPT
<script type="text/javascript">
// OBJPICKER {$name} ELEMENT AUTOCOMPLETE
(function(){
var optionsUrl = "{$name_options_url}&";
var options1 = {
	script: optionsUrl,
	varname: "typed_text",
	minchars: 2,
	timeout: 10000,
	delay: 200,
	json: true,
	shownoresults: false,
	callback: function(obj){ $("input[name='{$name}']").attr("value", obj.id) }
};
var nameAS = new AutoSuggest('{$name}__autocompleteTextbox', options1);
})()
// END AUTOCOMPLETE
</script>
SCRIPT;

				$visible_element = $input_element . $autocomplete_js;
			}
			else
			{
				$visible_element = $value;
			}

			$prop["value"] = $visible_element . $data_element;
		}
		elseif ("select" === $mode)
		{
			if (is_oid($args["obj_inst"]->prop($name)))
			{
				$o = new object($args["obj_inst"]->prop($name));
				$value = $o->prop_xml("name");
			}
			elseif (isset($prop["value"]) and is_oid($prop["value"]))
			{
				$o = new object($prop["value"]);
				$value = $o->prop_xml("name");
			}
			else
			{
				$value = "";
			}

			if (empty($args["view"]) and empty($prop["disabled"]))
			{
				$clids = is_array($prop["clid"]) ? implode(",", $prop["clid"]) : $prop["clid"];
				$list = new object_list(array(
					"class_id" => $clids,
					"site_id" => array(),
					"lang_id" => array()
				));

				$element = html::select(array(
					"name" => $name,
					"options" => $list->names(),
					"value" => $value
				));
			}
			else
			{
				$element = $value;
			}

			$prop["value"] = $element;
		}

		return array($name => $prop);
	}

	public function process_vcl_property(&$args)
	{
		$name = $args["prop"]["name"];
		// $args["obj_inst"]->set_prop($name, $args["prop"]["value"]);
	}

	/** Outputs autocomplete options matching object name search string $typed_text in bsnAutosuggest format json
		@attrib name=get_options
		@param clids required type=string
		@param typed_text optional type=string
	**/
	public static function get_options($args)
	{
		$choices = array("results" => array());

		$clids = explode(",", $args["clids"]);
		$classes_valid = true;
		foreach ($clids as $key => $clid)
		{
			if (!defined($clid))
			{
				$classes_valid = false;
			}
			else
			{
				$clids[$key] = constant($clid);
			}
		}

		if ($classes_valid)
		{
			$typed_text = $args["typed_text"];
			$limit = 20;
			$list = new object_list(array(
				"class_id" => $clids,
				"name" => "{$typed_text}%",
				new obj_predicate_limit($limit)
			));

			if ($list->count() > 0)
			{
				$results = array();
				$o = $list->begin();
				do
				{
					$value = $o->prop_xml("name");
					$info = "";
					$results[] = array("id" => $o->id(), "value" => iconv("iso-8859-4", "UTF-8", $value), "info" => $info);
				}
				while ($o = $list->next());
				$choices["results"] = $results;
			}
		}

		ob_start("ob_gzhandler");
		header("Content-Type: application/json");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Pragma: no-cache"); // HTTP/1.0
		// header ("Content-type: text/javascript; charset: UTF-8");
		// header("Expires: ".gmdate("D, d M Y H:i:s", time()+43200)." GMT");
		exit(json_encode($choices));
	}
}

/** Generic objpicker error **/
class awex_vcl_objpicker extends awex_vcl {}

/** Argument type error indicator **/
class awex_vcl_objpicker_arg extends awex_vcl {}


?>
