<?php

interface application_interface
{
	// returns CL_CFGFORM object or NULL if no cfgform defined for $object in this application
	public function get_cfgform_for_object(object $object);
}

?>
