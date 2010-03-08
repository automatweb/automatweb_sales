<?php
/*
@classinfo  maintainer=kristo
*/
class add_link extends aw_template
{
	// that is just a generic add link plugin for documents
	function add_link()
	{
		$this->init("");
	}

	function get_property($value)
	{
		return array(
			"caption" => t("Lisa link nähtav"),
			"type" => "checkbox",
			"value" => $value,
			"ch_value" => 1,
			"name" => "plugins[" . get_class($this) . "]",
		);
	}


	function show($args = array())
	{
		return !empty($args["value"]) ? $args["tpl"] : "";
	}

	////
	// !that thingie is needed until the class_base based document class is not
	// yet ready to replace the old static one.
	function get_static_property($args = array())
	{
		return html::checkbox(array(
			"caption" => t("Lisa link nähtav"),
			"checked" => !empty($args["value"]),
			"name" => "plugins[" . get_class($this) . "]",
		));
	}
}
?>
