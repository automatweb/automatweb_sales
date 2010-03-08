<?php
/*
@classinfo  maintainer=kristo
*/
class print_preview extends aw_template
{
	function print_preview()
	{
		$this->init("");
	}

	function get_property()
	{
		print "getting property!";
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
			"caption" => t("Prindi nähtav"),
			"checked" => !empty($args["value"]),
			"name" => "plugins[" . get_class($this) . "]",
		));
	}
}
?>
