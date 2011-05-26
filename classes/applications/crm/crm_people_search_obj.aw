<?php

class crm_people_search_obj extends _int_object
{
	const CLID = 1390;


	function get_search_props()
	{
		$meta = $this->meta("searh_props");
		return $meta;
	}

	function set_search_props($data)
	{
		$this->set_meta("searh_props" , $data);
	}

	function get_visible_props()
	{
		$data = $this->meta("searh_props");
		$ret = array();
		foreach($data as $prop => $stuff)
		{
			if($stuff["visible"])
			{
				$ret[$prop] = $stuff["jrk"];
			}
		}
		asort($ret);
		return array_keys($ret);
	}
}

?>
