<?php

class mini_gallery_obj extends _int_object
{
	const CLID = 318;
	
	/**	Returns the the object in JSON
		@attrib api=1
	**/
	public function json($encode = true)
	{
		$data = array(
			"id" => $this->id(),
			"name" => $this->prop("name"),
			"parent" => $this->prop("parent"),
			"folders" => $this->meta("folders"),
		);

		$json = new json();
		return $encode ? $json->encode($data, aw_global_get("charset")) : $data;
	}
}