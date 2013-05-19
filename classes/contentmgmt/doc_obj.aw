<?php

class doc_obj extends _int_object implements price_component_interface, crm_offer_row_interface
{
	const CLID = 7;

	//	Written solely for testing purposes!
	public function get_units()
	{
		$ol = new object_list(array(
			"class_id" => CL_UNIT,
			"status" => object::STAT_ACTIVE,
		));
		return $ol;
	}

	public function is_visible_to()
	{
		//dokumentide mitte n2itamine yleliigsetest riikidest tulevatele p2ringutele
		if(is_oid($this->id()) && strlen($this->prop("show_to_country")) > 1)
		{
			$aproved_countries = explode("," , $this->prop("show_to_country"));
			if(!in_array(detect_country() , $aproved_countries))
			{
				return false;
			}
		}
		return true;
	}

	/**	Returns the the object in JSON
		@attrib api=1
	**/
	public function json($encode = true)
	{
		$data = array(
			"id" => $this->id(),
			"name" => $this->prop("name"),
			"comment" => $this->prop("comment"),
			"status" => $this->prop("status"),
			"title" => $this->prop("title"),
			"lead" => $this->prop("lead"),
			"content" => $this->prop("content"),
			"show_title" => (bool)$this->prop("show_title"),
			"showlead" => (bool)$this->prop("showlead"),
			"show_modified" => (bool)$this->prop("show_modified"),
			"esilehel" => (bool)$this->prop("esilehel"),
			"title_clickable" => (bool)$this->prop("title_clickable"),
		);

		$json = new json();
		return $encode ? $json->encode($data, aw_global_get("charset")) : $data;
	}
}
