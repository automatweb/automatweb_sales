<?php

// jsonclient - generates JSON output for cb


namespace automatweb;


class jsonclient extends htmlclient
{
	protected $properties = array();
	protected $data = "";
	protected $inapplicable_property_types = array(
		"tabpanel",
		"table",
		"toolbar"
	);

	public function __construct($arr =  array())
	{
		parent::htmlclient($arr);
	}

	function add_property($args = array())
	{
		if (empty($args["name"]) or empty($args["type"]) or isset($args["vcl_inst"]) or in_array($args["type"], $this->inapplicable_property_types))
		{
			return;
		}

		if (isset($args["post_append_text"]))
		{
			unset($args["post_append_text"]);
		}

		if (isset($args["value"]))
		{
			$args["value"] = str_replace("\"", "&quot;", $args["value"]);
			// $args["value"] = addslashes($args["value"]);
		}

		$this->properties[$args["name"]] = $args;
	}

	function finish_output($arr = array())
	{
		$this->data = json_encode($this->properties);
	}

	function get_result($arr = array())
	{
		return $this->data;
	}
}

?>
