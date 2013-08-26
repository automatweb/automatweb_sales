<?php

class doc_obj extends _int_object implements price_component_interface, crm_offer_row_interface
{
	const CLID = 7;
	
	const STATUS_PUBLISHED = 0;
	const STATUS_DRAFT = 1;
	const STATUS_ARCHIVED = 2;
	
	const PARTICIPATION_TYPE_CONTRIBUTOR = 1;
	const PARTICIPATION_TYPE_COLLABORATOR = 2;
	
	const PARTICIPATION_PERMISSION_VIEW = 1;
	const PARTICIPATION_PERMISSION_EDIT = 2;

	public static function get_document_status_names()
	{
		return array(
			self::STATUS_DRAFT => t("Koostamisel"),
			self::STATUS_PUBLISHED => t("Avaldatud"),
			self::STATUS_ARCHIVED => t("Arhiveeritud"),
		);
	}
	
	public static function get_participation_type_names()
	{
		return array(
			self::PARTICIPATION_TYPE_CONTRIBUTOR => t("Kontribuutor"),
			self::PARTICIPATION_TYPE_COLLABORATOR => t("Kollaboraator"),
		);
	}
	
	public static function get_participation_permission_names()
	{
		return array(
			self::PARTICIPATION_PERMISSION_VIEW => t("Vaadata"),
			self::PARTICIPATION_PERMISSION_EDIT => t("Muuta"),
		);
	}

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
			"document_status" => (int)$this->prop("document_status"),
			"title" => $this->prop("title"),
			"lead" => $this->prop("lead"),
			"content" => $this->prop("content"),
			"show_title" => (bool)$this->prop("show_title"),
			"showlead" => (bool)$this->prop("showlead"),
			"show_modified" => (bool)$this->prop("show_modified"),
			"esilehel" => (bool)$this->prop("esilehel"),
			"title_clickable" => (bool)$this->prop("title_clickable"),
			"editors" => $this->meta("editors"),
			"authors" => $this->meta("authors"),
			"participants" => $this->meta("participants"),
		);

		$json = new json();
		return $encode ? $json->encode($data, aw_global_get("charset")) : $data;
	}
}
