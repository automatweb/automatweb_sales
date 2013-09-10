<?php

class mini_gallery_obj extends _int_object
{
	const CLID = 318;
	
	const VIEW_MODE_SLIDESHOW = 0;
	const VIEW_MODE_THUMBNAILS = 1;
	
	public static function get_view_mode_names () {
		return array(
			self::VIEW_MODE_SLIDESHOW => t("Slaid&scaron;&otilde;u"),
			self::VIEW_MODE_THUMBNAILS => t("Minipildid"),
		);
	}
	
	public function awobj_get_view_mode () {
		$val = parent::prop("view_mode");
		return (int)$val;
	}
	
	/**	Returns the the object in JSON
		@attrib api=1
	**/
	public function json ($encode = true) {
		$folders_oids = $this->meta("folder");
		$folders_ol = $this->meta("folder") ? new object_list(array(
			"oid" => $folders_oids,
		)) : new object_list();
		$folders = array();
		foreach ($folders_ol->names() as $id => $name) {
			$folders[] = array("id" => $id, "name" => $name);
		}
		$images = array();
		foreach ($this->get_images()->arr() as $image) {
			$images[] = $image->json(false);
		}
		
		$data = array(
			"id" => $this->id(),
			"name" => $this->prop("name"),
			"comment" => $this->prop("comment"),
			"parent" => $this->prop("parent"),
			"folder" => $folders,
			"images" => $images,
			"view_mode" => $this->awobj_get_view_mode(),
		);

		$json = new json();
		return $encode ? $json->encode($data, aw_global_get("charset")) : $data;
	}
	
	public function get_images () {
		return new object_list(array(
			"class_id" => image_obj::CLID,
			"parent" => $this->prop("folder"),
			"sort_by" => "objects.jrk"
		));
	}
}