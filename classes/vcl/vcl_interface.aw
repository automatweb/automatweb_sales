<?php

namespace automatweb;

interface vcl_interface
{
	public function init_vcl_property($arr);
	public function process_vcl_property(&$arr);
}

class awex_vcl extends aw_exception {}

?>
