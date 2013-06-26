<?php
/*

@layout search_filter type=horizontal width=3:3:3:3

	@property search_source type=select parent=search_filter
	@caption Organisatsioon

	@property search_oid type=textbox parent=search_filter
	@caption Kliendi ID

	@property search_name type=textbox parent=search_filter
	@caption Kliendi nimi

	@property search type=button parent=search_filter

@property search_results type=table

*/

class modal_search_customer_employee extends modal_search {
	
	protected $source_class = crm_company_obj::CLID;
	
	protected function get_title() {
		return t("KLIENDIOTSING");
	}
	
	protected function _get_search_source(&$property) {
		$person = obj(user::get_current_person(), null, crm_person_obj::CLID);
		$property["options"] = $person->get_companies()->names();
		$property["data"] = array("bind" => "value: source");
	}
	
	protected function get_children($source_id, $parent) {
		if (!object_loader::can("", $source_id)) {
			return new object_list();
		}
		$source = obj($source_id, null, $this->source_class);
		$parents = (array)$parent;
		foreach ($parents as $id => $parent) {
			if (!object_loader::can("", $parent)) {
				unset($parents[$id]);
			}
		}
		if (count($parents)) {
			$ol = new object_list();
			foreach ($parents as $parent) {
				$ol->add($source->get_customer_categories(obj($parent, null, crm_category_obj::CLID)));
			}
		} else {
			$ol = $source->get_customer_categories($source);
		}
		
		return $ol;
	}
	
	protected function get_items($source_id, $parents, $oids, $names) {
		if (!object_loader::can("", $source_id)) {
			return new object_list();
		}
		$source = obj($source_id, null, $this->source_class);
		
		if (empty($parents) and empty($oids) and empty($names)) {
			return new object_list();
		}
		
		$customer_ids = array();
		foreach ($parents as $parent) {
			$customer_ids = array_merge($customer_ids, $source->get_customer_ids(obj($parent, null, crm_category_obj::CLID)));
		}
		$customer_ids = !empty($oids) ? array_intersect($customer_ids, $oids) : $customer_ids;
		
		foreach ($names as $i => $name) {
			$names[$i] = "%{$name}%";
		}
		
		return count($customer_ids) > 0 ? new object_list(array(
			"class_id" => array(crm_person_obj::CLID, crm_company_obj::CLID),
			"oid" => $customer_ids,
			"name" => $names,
		)) : new object_list();
	}
}