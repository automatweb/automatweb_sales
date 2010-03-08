<?php
classload("site_base");

class site extends site_base
{
	function site()
	{
		$this->init("");
	}

	////
	// !well, this is obviously for drawing the frontpage - iow, it will only get called 
	// on pageviews to the front page
	//
	// it must return the content of the front page
	function on_frontpage() 
	{
		return $this->do_fp_menus_return("");
	}

	////
	// !this will get called on every pageview and must return an array of
	// template_name => template_value pairs, that will be imported in the menu
	// drawing template
	function on_page() 
	{
		$blocks = array();
		return $blocks;
	}

	////
	// !this will get called one per pageview
	// it may return an array of subtemplate_name => function_name pairs
	// that will get executed if the subtemplates exist in main.tpl and
	// their output will replace the subtemplates
	function get_sub_callbacks() 
	{
		return array();
	}

}

?>
