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
	
	protected function get_children($source_id, $parents, $level) {
		if (!object_loader::can("", $source_id)) {
			return new object_list();
		}
		$source = obj($source_id, null, $this->source_class);
		$parents = (array)$parents;
		$selected_customers = array();
		foreach ($parents as $id => $parent) {
			if (!object_loader::can("", $parent)) {
				unset($parents[$id]);
			}
		}
		$ol = new object_list();
		if ($level < 2) {
			// Customer categories
			if (count($parents)) {
				$ol = new object_list();
				foreach ($parents as $parent) {
					$ol->add($source->get_customer_categories(obj($parent, null, crm_category_obj::CLID)));
				}
			} else {
				$ol = $source->get_customer_categories($source);
			}
		} elseif ($level > 2) {
			if ($level == 3) {
				$selected_customers = $parents;
			} elseif (count($parents)) {
				$odl = new object_data_list(
					array(
						"class_id" => crm_section_obj::CLID,
						"oid" => $parents,
					), array(
						crm_section_obj::CLID => array("organization")
					)
				);
				$selected_customers = $odl->get_element_from_all("organization");
			}
			
			if (count($selected_customers)) {
				$ol = new object_list();
				foreach ($selected_customers as $i => $selected_customer) {
					if (!object_loader::can("", $selected_customer)) {
						unset($selected_customers[$i]);
					}
					$source = obj($selected_customer, null, crm_company_obj::CLID);
					if ($level > 3 && count($parents)) {
						foreach ($parents as $parent) {
							$ol->add($source->get_sections(obj($parent, null, crm_section_obj::CLID)));
						}
					} elseif ($level == 3) {
						$ol->add($source->get_sections($source));
					}
				}
			}
		}
		
		$oids = automatweb::$request->arg_isset("oid") ? array_map('trim', explode(",", automatweb::$request->arg("oid"))) : array();
		$names = automatweb::$request->arg_isset("name") ? explode(",", automatweb::$request->arg("name")) : array();
		
		$levels = array($level => $ol);
		
		if ($level < 3) {
			$levels[2] = $this->get_customers($source_id, $parents);
		}
		
		if (count($selected_customers)) {
			$employees = new object_list();
			foreach ($selected_customers as $selected_customer) {
				$source = obj($selected_customer, null, crm_company_obj::CLID);
				$employees->add($this->get_employees($source, $level > 3 ? $parents : array(), $oids, $names));
			}
			$levels[5] = $employees;
		}
		
		return $levels;
	}
	
	protected function get_customers($source_id, $parents, $oids = array(), $names = array()) {
		if (!object_loader::can("", $source_id)) {
			return new object_list();
		}
		$source = obj($source_id, null, $this->source_class);
		
		if (empty($parents) and empty($oids) and empty($names)) {
			return new object_list();
		}
		
		$customer_ids = array();
		foreach ($parents as $parent) {
			foreach ($source->get_customer_ids(obj($parent, null, crm_category_obj::CLID), true) as $customer_id) {
				$customer_ids[] = $customer_id["buyer"];
			}
		}
		$customer_ids = !empty($oids) ? array_intersect($customer_ids, $oids) : $customer_ids;
		
		foreach ($names as $i => $name) {
			$names[$i] = "%{$name}%";
		}
		
		return count($customer_ids) > 0 ? new object_list(array(
			"class_id" => crm_company_obj::CLID,
			"oid" => $customer_ids,
			"name" => $names,
		)) : new object_list();
	}
	
	protected function get_employees($source, $parents, $oids, $names) {		
		if (empty($parents) and empty($oids) and empty($names)) {
			return $source->get_employees();
		}
		
		$employee_ids = array();
		foreach ($parents as $parent) {
			$employee_ids = array_merge($employee_ids, $source->get_employees("active", null, obj($parent, null, crm_section_obj::CLID))->ids());
		}
		$employee_ids = !empty($oids) ? array_intersect($employee_ids, $oids) : $employee_ids;
		
		foreach ($names as $i => $name) {
			$names[$i] = "%{$name}%";
		}
		
		return count($employee_ids) > 0 ? new object_list(array(
			"class_id" => crm_person_obj::CLID,
			"oid" => $employee_ids,
			"name" => $names,
		)) : new object_list();
	}
}