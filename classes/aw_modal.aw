<?php

class aw_modal implements orb_public_interface {

	const LOAD_PRELOAD = "preload";
	const LOAD_ON_DEMAND = "on_demand";
	const LOAD_ON_DEMAND_ONCE = "on_demand_once";
	const LOAD_BACKGROUND = "background";
	
	protected $request;
	
	protected $template;
	protected $header_template;
	protected $content_template;
	protected $footer_template;
	
	protected $properties;
	protected $groupinfo;
	
	public function set_request(aw_request $request) {
		$this->request = $request;
	}
	
	public function parse() {
		$this->template = new aw_php_template("aw_modal", "default");
		
		$this->preprocess_property_definitions();
		
		$this->parse_header();
		$this->parse_content();
		$this->parse_footer();
		
		automatweb::$result->set_data($this->template->render());
		automatweb::$instance->http_exit();
	}
	
	private function preprocess_property_definitions() {
		$cfgu = new cfgutils();
		$this->properties = $cfgu->load_class_properties(array(
			"file" => get_class($this)
		));
		$this->groupinfo = $cfgu->get_groupinfo();
		
		foreach ($this->groupinfo as $group_id => $group_details) {
			$this->groupinfo[$group_id]["id"] = $group_id;
			$this->groupinfo[$group_id]["subgroups"] = array();
			$this->groupinfo[$group_id]["properties"] = array();
			
			$group_callback = "_group_{$group_id}";
			if (is_callable(array($this, $group_callback))) {
				$this->$group_callback($this->groupinfo[$group_id]);
			}
		}
		
		foreach ($this->properties as $property_id => $property_details) {
			if (!isset($property_details["group"]) or !isset($this->groupinfo[$property_details["group"]])) {
				continue;
			}
			
			$property_details["id"] = $property_id;
			$property_callback = "_get_{$property_id}";
			if (is_callable(array($this, $property_callback))) {
				$this->$property_callback($property_details);
			}
			
			if ($property_details["type"] === "toolbar") {
				$this->groupinfo[$property_details["group"]]["toolbar"] = $property_details;
			} else {
				$this->groupinfo[$property_details["group"]]["properties"][$property_id] = $property_details;
			}
		}
		
		foreach ($this->groupinfo as $group_id => $group_details) {
			if (isset($group_details["parent"]) and isset($this->groupinfo[$group_details["parent"]])) {
				$this->groupinfo[$group_details["parent"]]["subgroups"][$group_id] = $group_details;
				unset($this->groupinfo[$group_id]);
			}
		}
	}
	
	private function parse_header() {
		$this->header_template = new aw_php_template("aw_modal", "default-header");
		$this->header_template->add_vars(array(
			"title" => $this->get_title(),
		));
		
		$this->template->bind($this->header_template, "header");
	}
	
	private function parse_content() {
		$this->content_template = new aw_php_template("aw_modal", "default-content");
		$this->content_template->add_vars(array(
			"groups" => $this->groupinfo,
		));
		if (is_callable(array($this, "get_popups_template"))) {
			$this->content_template->bind($this->get_popups_template(), "popups");
		}
		
		$this->template->bind($this->content_template, "content");
	}
	
	private function parse_footer() {
		$this->footer_template = new aw_php_template("aw_modal", "default-footer");
		$this->footer_template->add_vars(array(
			"save_method" => $this->get_save_method(),
			"groups" => $this->groupinfo,
		));
		
		$this->template->bind($this->footer_template, "footer");
	}
	
	public static function parse_group($group) {
		$load = isset($group["load"]) ? $group["load"] : self::LOAD_PRELOAD;
		
		switch ($load) {
			case self::LOAD_ON_DEMAND:
				return self::parse_on_demand_loaded_group($group);

			case self::LOAD_PRELOAD:
			default:
				return self::parse_preloaded_group($group);
		}
	}
	
	private static function parse_preloaded_group($group) {
		return self::parse_properties($group["properties"]);
	}
	
	private static function parse_on_demand_loaded_group($group) {
		$template = new aw_php_template("aw_modal", "default-on-demand-group");
		
		$template->add_vars(array(
			"group" => $group,
		));
		
		return $template->render();
	}
	
	private static function parse_properties($properties) {
		$template = new aw_php_template("aw_modal", "default-properties");
		
		$template->add_vars(array(
			"properties" => $properties,
		));
		
		return $template->render();
	}
	
	public static function parse_table($table) {
		$template = new aw_php_template("aw_modal", "default-table");
		
		$template->add_vars(array(
			"table" => $table,
		));
		
		return $template->render();
	}
	
	public static function implode_data_fields($data) {
		$output = "";
		if (is_array($data)) {
			foreach ($data as $data_key => $data_value) {
				$output .= " data-{$data_key}=\"{$data_value}\"";
			}
		}
		return $output;
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
						$this->set_property($object, $key, $value);
				}
			}
		}
		
		$object->save();
		
		$json = $object->json();
		
		automatweb::$result->set_data($json);
		automatweb::$instance->http_exit();
	}
	
	protected function set_property($object, $key, $value) {
		$callback = "_set_{$key}";
		if (is_callable(array($this, $callback))) {
			$this->$callback($object, $value);
		} elseif ($object->is_property($key)) {
			if (is_array($value) and isset($value["id"])) {
				$value = $value["id"];
			}
			$object->set_prop($key, $value);
		}
	}
}