<?php
/*
@classinfo  maintainer=kristo
*/
class send_link extends aw_template
{
	function send_link()
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
			"caption" => t("Saada link nähtav"),
			"checked" => !empty($args["value"]),
			"name" => "plugins[" . get_class($this) . "]",
		));
	}
}
?>
