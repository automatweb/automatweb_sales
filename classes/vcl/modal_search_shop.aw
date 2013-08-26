<?php
/*

@classinfo no_save=1

@layout search_filter type=horizontal width=3:3:3:3

	@property search_source type=select parent=search_filter
	@caption E-pood

	@property search_oid type=textbox parent=search_filter
	@caption Objekti ID

	@property search_name type=textbox parent=search_filter
	@caption Objekti nimi

	@property search type=button parent=search_filter

@property search_results type=table
@caption Leitud objektid

*/

class modal_search_shop extends modal_search {
	
	protected $source_class = shop_order_center_obj::CLID;
	
	protected function get_title() {
		return t("ARTIKLIOTSING");
	}
	
	protected function _get_search_source(&$property) {
		$ol = new object_list(array(
			"class_id" => shop_order_center_obj::CLID,
		));
		$property["options"] = $ol->names();
		$property["data"] = array("bind" => "value: source");
	}
	
	protected function get_children($source_id, $parents, $level) {
		if (!object_loader::can("", $source_id)) {
			return new object_list();
		}
		$source = obj($source_id, null, $this->source_class);
		if (!object_loader::can("", $source->warehouse)) {
			return new object_list();
		}
		$warehouse = obj($source->warehouse, null, shop_warehouse_obj::CLID);
		$parents = (array)$parents;
		foreach ($parents as $id => $parent) {
			if (!object_loader::can("", $parent)) {
				unset($parents[$id]);
			}
		}
		$ol = count($parents) > 0 ? $warehouse->get_categories($parents) : $warehouse->get_root_categories();
		
		$oids = automatweb::$request->arg_isset("oid") ? array_map('trim', explode(",", automatweb::$request->arg("oid"))) : array();
		$names = automatweb::$request->arg_isset("name") ? explode(",", automatweb::$request->arg("name")) : array();
		
		return array(
			$level => $ol,
			2 => $this->get_items($source_id, $parents, $oids, $names),
		);
	}
	
	protected function get_items($source_id, $parents, $oids, $names) {
		if (!object_loader::can("", $source_id)) {
			return new object_list();
		}
		$source = obj($source_id, null, $this->source_class);
		if (!object_loader::can("", $source->warehouse)) {
			return new object_list();
		}
		$warehouse = obj($source->warehouse, null, shop_warehouse_obj::CLID);
		
		if (empty($parents) and empty($oids) and empty($names)) {
			return new object_list();
		}
		
		return $warehouse->search_products(array(
			"oid" => $oids,
			"category" => $parents,
			"name" => $names,
			"cat_condition" => "or",
			"recursive" => true,
		));
	}
}