<?php

class objpicker extends core implements vcl_interface, orb_public_interface
{
	private $req;

	/** Sets orb request to be processed by this object
		@attrib api=1 params=pos
		@param request type=aw_request
		@returns void
	**/
	public function set_request(aw_request $request)
	{
		$this->req = $request;
	}

	/**
		@attrib params=name api=1

		@param name type=string
			String to indetify the object picker. Chars: A-z, 0-9, _ (first character not numeric)

		@param object type=object
			The object the picker picks objects for

		@param clid type=array
			Class id-s of objects to be picked from. Default is empty array, meaning any class object can be picked. If not specified, options must be defined or mode 'autocomplete'

		@param mode type=string default=text
			Values: "text", "select"

		@param disabled type=boolean default=FALSE

		@param search_enabled type=boolean default=FALSE

		@param view type=boolean default=FALSE

		@param size type=string default=""
			Textbox size

		@param value type=int

		@param options_callback type=string default=""

		@returns string
			The HTML of the object picker.

		@errors
			Throws awex_vcl_objpicker_arg if provided options callback specification is invalid.
	**/
	public static function create($args)
	{
		$name = $args["name"];
		$autocomplete_textbox_name = preg_replace("/[^A-Za-z0-9_]/", "_", $name)."__autocompleteTextbox";
		$mode = (isset($args["mode"]) and "select" === $args["mode"]) ? "select" : "text";

		if ("text" === $mode)
		{
			if ($args["object"]->is_property($name) and object_loader::can("", $args["object"]->prop($name)))
			{
				$o = new object($args["object"]->prop($name));
				$value = $o->prop_xml("name");
				$data_element = html::hidden(array("name" => $name, "value" => $o->id()));
			}
			elseif (isset($args["value"]) and is_oid($args["value"]))
			{
				$o = new object($args["value"]);
				$value = $o->prop_xml("name");
				$data_element = html::hidden(array("name" => $name, "value" => $o->id()));
			}
			else
			{
				$value = "";
				$data_element = html::hidden(array("name" => $name, "value" => ""));
			}

			if (empty($args["view"]) and empty($args["disabled"]))
			{
				$size = isset($args["size"]) ? $args["size"] : "";
				$input_element = html::textbox(array("name" => $autocomplete_textbox_name, "value" => $value, "size" => $size));

				load_javascript("bsnAutosuggest.js");

				if (!empty($args["options_callback"]))
				{
					preg_match("/([a-z0-9_]+)::([a-z0-9_]+)(\((([a-z0-9_]+),?)+\))?/i", $args["options_callback"], $matches);

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
					$clids = isset($args["clid"]) ? (is_array($args["clid"]) ? implode(",", $args["clid"]) : $args["clid"]) : "";

					if (empty($clids))
					{
						throw new awex_vcl_objpicker_arg("Required parameter 'clid' missing.");
					}

					$class = "objpicker";
					$method = "get_options";
					$params = array("clids" => $clids, "id" => $args["object"]->id());
				}

				//	TODO: There really should be a way to call this statically!
				$inst = new objpicker();
				$name_options_url = $inst->mk_my_orb($method, $params, $class);
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
var nameAS = new AutoSuggest('{$autocomplete_textbox_name}', options1);
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

			$html = $visible_element . $data_element;
		}
		elseif ("select" === $mode)
		{
			$value = isset($args["value"]) ? $args["value"] : $args["object"]->prop($name);

			if (empty($args["view"]) and empty($args["disabled"]))
			{
				$clids = (array)$args["clid"];
				foreach($clids as $i => $clid)
				{
					if (is_class_id($clid))
					{
						continue;
					}
					elseif(defined($clid))
					{
						$clids[$i] = constant($clid);
					}
					else
					{
						unset($clids[$i]);
					}
				}
				$list = new object_list(array(
					"class_id" => $clids
				));

				$element = html::select(array(
					"name" => $name,
					"options" => array(t("--vali--")) + $list->names(),
					"value" => $value
				));
			}
			else
			{
				$element = $value;
			}
			$html = $element;
		}
		return $html;
	}

	public function init_vcl_property($args)
	{
		$prop = $args["property"];
		$prop["value"] = self::create($prop + array(
			"object" => $args["obj_inst"],
			"view" => !empty($args["view"]),
			"clid" => !empty($args["clid"]) ? $args["clid"] : 0
		));

		return array($prop["name"] => $prop);
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
			if (is_class_id($clid))
			{
				$clids[$key] = $clid;
			}
			elseif (defined($clid))
			{
				$clids[$key] = constant($clid);
			}
			else
			{
				$classes_valid = false;
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
					$results[] = array("id" => $o->id(), "value" => $value, "info" => $info);
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
