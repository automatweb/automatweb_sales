<?php
/*

@classinfo no_save=1

@property search_name type=textbox
@caption Objekti nimi

@property search_oid type=textbox
@caption Objekti ID

@property search type=button

@property search_results type=table
@caption Leitud objektid

*/

class modal_search extends aw_modal {
	
	protected $source_class = null;
	
	protected function get_title() {
		return t("OTSING");
	}
	
	protected function get_save_method() {
		return "(function(){})";
	}
	
	protected function _get_search_name(&$property) {
		$property["data"] = array("bind" => "value: name");
	}
	
	protected function _get_search_oid(&$property) {
		$property["data"] = array("bind" => "value: oid");
	}
	
	protected function _get_search(&$property) {
		$property["button"] = array(
			"caption" => t("Filtreeri"),
		);
		$property["data"] = array("bind" => "click: \$root.loadLevel");
	}
	
	protected function _get_search_results(&$property) {
		// FIXME: Make a separate class for knockout-tb table!
		$property["table"] = array(
			"hack" => true,
			"id" => "search_results",
			"caption" => t("Leitud objektid"),
			"fields" => array("chooser", "oid", "name"),
			"header" => array(
				"fields" => array(
					"chooser" => t("Vali"),
					"oid" => t("ID"),
					"name" => t("Nimi"),
				)
			),
			"content" => array(
				"data" => array("bind" => "foreach: results"),
				"fields" => array(
					"chooser" => html::radiobutton(array("name" => "search-selected", "data" => array("bind" => "value: id, checked: \$root.selected"))),
					"oid" => array("data" => array("bind" => "text: id")),
					"name" => array("data" => array("bind" => "text: name")),
				),
			),
		);
	}
	
	public function search() {
		$source = automatweb::$request->arg_isset("source") ? automatweb::$request->arg("source") : null;
		$parents = automatweb::$request->arg_isset("parent") ? (array)automatweb::$request->arg("parent") : null;
		$oids = automatweb::$request->arg_isset("oid") ? array_map('trim', explode(",", automatweb::$request->arg("oid"))) : array();
		$names = automatweb::$request->arg_isset("name") ? explode(",", automatweb::$request->arg("name")) : array();
		
		$items = $this->get_items($source, $parents, $oids, $names);
		
		$results = array();
		foreach ($items->names() as $id => $name) {
			$results[] = array("id" => $id, "name" => $name);
		}
		
		$json_encoder = new json();
		$json = $json_encoder->encode($results, aw_global_get("charset"));
	
		automatweb::$result->set_data($json);
		automatweb::$instance->http_exit();
	}
	
	protected function get_items($source_id, $parents, $oids, $names) {
		$filter = array(new obj_predicate_limit(1000));
		if (!empty($names)) {
			foreach ($names as $i => $name) {
				if (strlen(trim($name)) > 0) {
					$names[$i] = "%{$name}%";
				} else {
					unset($names[$i]);
				}
			}
			$filter["name"] = $names;
		}
		if (!empty($oids)) {
			$filter["oid"] = $oids;
		}
		
		if (!empty($parents)) {
			$filter["parent"] = array();
			while (!empty($parents)) {
				$filter["parent"] += $parents;
				$ol = new object_list(array(
					"class_id" => CL_MENU,
					"parent" => $parents,
				));
				$parents = $ol->ids();
			}
		}
		
		if (count($filter) > 1) {
			$filter["class_id"] = automatweb::$request->arg_isset("clid") ? automatweb::$request->arg("clid") : null;
			$ol = new object_list($filter);
		} else {
			$ol = new object_list();
		}
		
		return $ol;
	}
	
	public function children() {
		$source = automatweb::$request->arg_isset("source") ? automatweb::$request->arg("source") : null;
		$parent = automatweb::$request->arg_isset("parent") ? automatweb::$request->arg("parent") : null;
		$level = automatweb::$request->arg_isset("level") ? automatweb::$request->arg("level") : null;
		
		$ols = $this->get_children($source, $parent, $level);
		
		$results = array();
		foreach ($ols as $level => $ol) {
			$items = array();
			
			if ($ol instanceof object_list) {
				foreach ($ol->names() as $id => $name) {
					$items[] = array("id" => $id, "name" => $name, "level" => $level);
				}
			} elseif ($ol instanceof object_data_list) {
				foreach ($ol->arr() as $data) {
					$data["level"] = $level;
					$items[] = $data;
				}
			} elseif (is_array($ol)) {
				foreach ($ol as $data) {
					$data["level"] = $level;
					$items[] = $data;
				}
			} else {
				// TODO: Throw ERROR!
			}
			
			$results[] = array(
				"level" => $level,
				"items" => $items
			);
		}
		
		$json_encoder = new json();
		$json = $json_encoder->encode($results, aw_global_get("charset"));
	
		automatweb::$result->set_data($json);
		automatweb::$instance->http_exit();
	}
	
	protected function get_children($source, $parent, $level) {
		$filter = array();
		if (is_oid($parent) || is_array($parent) && count($parent) > 0) {
			$filter["parent"] = $parent;
		}
		if (!empty($filter)) {
			$filter["class_id"] = CL_MENU;
			$filter[] = new obj_predicate_limit(100);
			$ol = new object_list($filter);
		} else {
			$ol = new object_list();
		}
		
		$oids = automatweb::$request->arg_isset("oid") ? array_map('trim', explode(",", automatweb::$request->arg("oid"))) : array();
		$names = automatweb::$request->arg_isset("name") ? explode(",", automatweb::$request->arg("name")) : array();
		
		return array(
			$level  => $ol,
			2 => $this->get_items($source, (array)$parent, $oids, $names),
		);
	}
	
	protected function get_right_footer_buttons() {
		return array(
			html::href(array(
				"url" => "javascript:void(0)",
				"data" => array("bind" => "click: onReady", "dismiss" => "modal"),
				"class" => "btn btn-primary",
				"caption" => t("Valmis"),
			))
		);
	}
}