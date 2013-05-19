<?php

class aw_modal implements orb_public_interface {
	
	protected $request;
	
	public function set_request(aw_request $request) {
		$this->request = $request;
	}
	
	public function parse() {
		$template = new aw_php_template("aw_modal", "default");
		
		if (is_callable(array($this, "get_header_template"))) {
			$template->bind($this->get_header_template(), "header");
		}

		if (is_callable(array($this, "get_content_template"))) {
			$template->bind($this->get_content_template(), "content");
		}

		if (is_callable(array($this, "get_footer_template"))) {
			$template->bind($this->get_footer_template(), "footer");
		}
		
		automatweb::$result->set_data($template->render());
		automatweb::$instance->http_exit();
	}
	
	/**
		@attrib api=1
		@param oid required type=oid
			OID
		@returns JSON of the object.
	**/
	public function get_data($arr) {
		$object = obj($arr["oid"]);
		
		$json = $object->json();
		
		automatweb::$result->set_data($json);
		automatweb::$instance->http_exit();
	}
	
	/**
		@attrib api=1
		@param class_id required type=clid
		@param parent required type=oid
		@param data required type=array
		@param removed optional type=array
		@returns JSON of the object.
	**/
	public function save($arr) {
		if (!empty($arr["data"]["id"]) and object_loader::can("", $arr["data"]["id"])) {
			$object = obj($arr["data"]["id"], null, (int)$arr["class_id"]);
			unset($arr["data"]["id"]);
		} elseif (is_class_id($arr["class_id"])) {
			$object = obj(null, null, $arr["class_id"]);
			$object->set_parent($arr["parent"]);
			unset($arr["parent"]);
		}
		
		if (isset($arr["data"]) && is_array($arr["data"])) {	
			foreach ($arr["data"] as $key => $value) {
				switch ($key) {
					case "ord":
						$object->set_ord($value);
						break;
					
					case "name":
						$object->set_name($value);
						break;
					
					case "status":
						$object->set_status($value);
						break;
					
					default:		
						if ($object->is_property($key)) {
							$object->set_prop($key, $value);
						}
				}
			}
		}
		
		$object->save();
		
		$json = $object->json();
		
		automatweb::$result->set_data($json);
		automatweb::$instance->http_exit();
	}
}