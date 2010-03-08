<?php

class cfgcontroller_obj extends _int_object
{
	/** Processes $data according to formula
	@attrib api=1 params=pos
	@param data type=array default=array()
	@returns void
	**/
	public function process(&$data = array())
	{
		eval($this->trans_get_val("formula"));
	}
}

?>
