<?php

class sm_site_group_obj extends _int_object
{
	/** Returns a list of site entry objects in the group
		@attrib api=1
	**/
	public function get_sites_in_group()
	{
		$r = array();
		foreach($this->connections_from(array("type" => "RELTYPE_SITE")) as $c)
		{
			$r[] = $c->to();
		}
		return $r;
	}
}

?>
