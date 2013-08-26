<?php
/*

@classinfo no_save=1

@layout search_filter type=horizontal width=3:3:3:3

	@property search_source type=select parent=search_filter
	@caption Organisatsioon

	@property search_oid type=textbox parent=search_filter
	@caption T&ouml;&ouml;taja ID

	@property search_name type=textbox parent=search_filter
	@caption T&ouml;&ouml;taja nimi

	@property search type=button parent=search_filter

@property search_results type=table

*/

class modal_search_employee extends modal_search {
	
	protected $source_class = crm_company_obj::CLID;
	
	protected function get_title() {
		return t("T&Ouml;&Ouml;TAJATE OTSING");
	}
	
	protected function _get_search_source(&$property) {
		$person = obj(user::get_current_person(), null, crm_person_obj::CLID);
		$property["options"] = $person->get_companies()->names();
		$property["data"] = array("bind" => "value: source");
	}
	
	protected function get_children($source_id, $parents, $level) {
		if (!object_loader::can("", $source_id)) {
			return new object_list();
		}
		$source = obj($source_id, null, $this->source_class);
		$parents = (array)$parents;
		foreach ($parents as $id => $parent) {
			if (!object_loader::can("", $parent)) {
				unset($parents[$id]);
			}
		}
		if (count($parents)) {
			$ol = new object_list();
			foreach ($parents as $parent) {
				$ol->add($source->get_sections(obj($parent, null, crm_section_obj::CLID)));
			}
		} else {
			$ol = $source->get_sections($source);
		}
		
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
		
		if (empty($parents) and empty($oids) and empty($names)) {
			return new object_list();
		}
		
		$employee_ids = array();
		foreach ($parents as $parent) {
			$employee_ids = array_merge($employee_ids, $source->get_employees("active", null, obj($parent, null, crm_section_obj::CLID))->ids());
		}
		$employee_ids = !empty($oids) ? array_intersect($employee_ids, $oids) : $employee_ids;
		
		foreach ($names as $i => $name) {
			$names[$i] = "%{$name}%";
		}
		
		$ol = count($employee_ids) > 0 ? new object_list(array(
			"class_id" => crm_person_obj::CLID,
			"oid" => $employee_ids,
			"name" => $names,
		)) : new object_list();
		
		$items = array();
		foreach ($ol->arr() as $item) {
			$items[] = array(
				"id" => $item->id,
				"name" => $item->name,
				"phone" => $item->get_phone_number(),
				"email" => $item->get_mail(),
				"organisation" => array(
					"id" => $source->id,
					"name" => $source->name,
				),
			);
		}
		
		return $items;
	}
}