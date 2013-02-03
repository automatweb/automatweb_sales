<?php

class join_site_obj extends _int_object
{
	const CLID = 287;
	
	const FIELD_TYPE_SELECT = "select";
	const FIELD_TYPE_RADIOS = "radios";
	const FIELD_TYPE_CHECKBOXES = "checkboxes";
	const FIELD_TYPE_ADDRESS = "address";

	private $form_fields_initialised = false;
	private $form_fields = array(
		crm_person_obj::CLID => array(
			"title" => array(
				"caption" => "Pöördumine",
				"type" => self::FIELD_TYPE_SELECT,
				"type_options" => array(self::FIELD_TYPE_SELECT, self::FIELD_TYPE_RADIOS),
				"options" => array(
					crm_person_obj::TITLE_MR => "Härra",
					crm_person_obj::TITLE_MRS => "Proua",
					crm_person_obj::TITLE_MISS => "Preili"
				),
			),
			"firstname" => array(
				"caption" => "Eesnimi",
			),
			"lastname" => array(
				"caption" => "Perekonnanimi",
			),
			"gender" => array(
				"caption" => "Sugu",
				"type" => self::FIELD_TYPE_SELECT,
				"type_options" => array(self::FIELD_TYPE_SELECT, self::FIELD_TYPE_RADIOS),
				"options" => array(
					crm_person_obj::GENDER_MALE => "Mees",
					crm_person_obj::GENDER_FEMALE => "Naine",
				),
			),
			"age" => array(
				"caption" => "Vanus",
			),
			"birth_date" => array(
				"caption" => "Sünniaeg",
			),
			"personal_id" => array(
				"caption" => "Isikukood",
			),
			"postal_address" => array(
				"caption" => "Postiaadress",
				"type" => self::FIELD_TYPE_ADDRESS,
				"address_fields" => array(
					"country" => array(
						"caption" => "Riik",
						"type" => "select",
						"options" => array("join_site_obj", "get_address_countries"),
					),
//					"location" => array("caption" => "Asukoht"),
					"street" => array("caption" => "Tänav/asum"),
					"house" => array("caption" => "Maja/number"),
					"apartment" => array("caption" => "Korter/tuba"),
					"postal_code" => array("caption" => "Postiindeks"),
					"po_box" => array("caption" => "Postkast"),
				),
			),
			"address" => array(
				"caption" => "Füüsiline aadress",
				"type" => self::FIELD_TYPE_ADDRESS,
				"address_fields" => array(
					"location" => "Asukoht",
					"street" => "Tänav/asum",
					"house" => "Maja/number",
					"apartment" => "Korter/tuba",
					"postal_code" => "Postiindeks",
					"po_box" => "Postkast"
				),
			),
			"default_phone" => array(
				"caption" => "Vaikimisi telefon",
			),
			"secondary_phone" => array(
				"caption" => "Sekundaarne telefon",
			),
			"web_address" => array(
				"caption" => "Veebiaadress",
			),
			"email" => array(
				"caption" => "E-mail",
			),
			"company" => array(
				"caption" => "Organisatsioon",
			),
			"profession" => array(
				"caption" => "Amet",
			),
		),
		crm_company_obj::CLID => array(
			"name" => array(
				"caption" => "Organisatsiooni nimi",
			),
			"reg_nr" => array(
				"caption" => "Registrikood",
			),
			"tax_nr" => array(
				"caption" => "KM kood",
			),
			"ettevotlusvorm" => array(
				"caption" => "Õiguslik vorm",
				"type" => self::FIELD_TYPE_SELECT,
				"type_options" => array(self::FIELD_TYPE_SELECT, self::FIELD_TYPE_RADIOS),
				"options" => array(),
			),
			"pohitegevus" => array(
				"caption" => "Tüüp ja alamtüüp",
			),
			"address" => array(
				"caption" => "Aadress",
				"type" => self::FIELD_TYPE_ADDRESS,
			),
			"phone" => array(
				"caption" => "Telefon",
			),
			"web_address" => array(
				"caption" => "Veebiaadress",
			),
			"email" => array(
				"caption" => "E-mail",
			),
		),
		crm_company_customer_data_obj::CLID => array(),
	);
	
	static function get_address_countries()
	{
		$ol = new object_list(array(
			"class_id" => country_obj::CLID,
		));
		return $ol->names();
	}
	
	function get_form_groups()
	{
		$groups = $this->meta("form_groups");
		uasort($groups, function($a, $b){ return $a["ord"] - $b["ord"]; });
		foreach ($groups as $i => $group)
		{
			uasort($groups[$i]["subgroups"], function($a, $b){ return $a["ord"] - $b["ord"]; });
		}
		return $groups;
	}
	
	function get_default_form_fields()
	{
		if (!$this->form_fields_initialised)
		{
			$this->__initialise_form_fields();
		}
		
		return $this->form_fields;
	}
	
	private function __initialise_form_fields()
	{
		if ($this->is_saved())
		{
			$personnel_management = new personnel_management();
			$this->form_fields[crm_company_obj::CLID]["ettevotlusvorm"]["options"] = $personnel_management->get_legal_forms();

			// FIXME: Abstract into a method.
			$customer_relations = new object_list(array(
				"class_id" => join_site_customer_relation_obj::CLID,
				"join_site" => $this->id(),
			));
		
			foreach($customer_relations->arr() as $customer_relation)
			{
				$organisation = $customer_relation->organisation();
				$organisation_id = $organisation->id();
				$organisation_name = $organisation->get_title();
				$this->form_fields[crm_company_customer_data_obj::CLID]["categories_{$organisation_id}"] = array(
					"caption" => "{$organisation_name} - Kliendigrupid",
					"type" => self::FIELD_TYPE_SELECT,
					"type_options" => array(self::FIELD_TYPE_SELECT, self::FIELD_TYPE_RADIOS, self::FIELD_TYPE_CHECKBOXES),
					"options" => array($customer_relation, "get_customer_groups"),
				);
//				$this->form_fields[crm_company_customer_data_obj::CLID]["contact_{$organisation_id}"] = array(
//					"caption" => "Kontaktisikud",
//				);
			}
			$this->form_fields_initialised = true;
		}
	}
	
	function get_form_fields($group_id, $subgroup_id)
	{
		$active_subgroup_fields = array();

		foreach($this->meta("form_fields") as $clid => $fields)
		{
			foreach ($fields as $field_id => $field)
			{
				if (!empty($field["active"]) && $field["group"] == $group_id && $field["subgroup"] == $subgroup_id)
				{
					$field["clid"] = $clid;
					$field["id"] = $field_id;
					$this->__apply_field_specific_arguments($field);
					$active_subgroup_fields["data[{$clid}][{$field_id}]"] = $field;
				}
			}
		}
		
		return $active_subgroup_fields;
	}
	
	private function __apply_field_specific_arguments(&$field)
	{
		$default_form_fields = $this->get_default_form_fields();
		if (isset($default_form_fields[$field["clid"]][$field["id"]]) && is_array($default_form_fields[$field["clid"]][$field["id"]]))
		{
			$field = $field + $default_form_fields[$field["clid"]][$field["id"]];
		}
	}
	
	function get_form_field_value($field)
	{
		if (!users::is_logged_in())
		{
			return null;
		}
		
		switch ($field["clid"])
		{
			case crm_person_obj::CLID:
				return $this->__get_form_field_value_person($field["id"]);
				
			case crm_company_obj::CLID:
				return $this->__get_form_field_value_organisation($field["id"]);
		
			default:
				return null;
		}
	}
	
	private function __get_form_field_value_organisation($field_id)
	{
		static $organisation;
		if (!isset($organisation))
		{
			$organisation = obj(user::get_current_company(), null, crm_company_obj::CLID);
		}
		
		switch($field_id)
		{
			case "web_address":
				return $organisation->prop("fake_url");

			case "phone":
				return $organisation->prop("fake_phone");

			case "email":
				return $organisation->prop("fake_email");

			default:
				return $organisation->is_property($field_id) ? $organisation->prop($field_id) : null;
		}
	}
	
	private function __get_form_field_value_person($field_id)
	{
		static $person;
		if (!isset($person))
		{
			$person = obj(user::get_current_person(), null, crm_person_obj::CLID);
		}
		
		switch($field_id)
		{
			case "postal_address":
			case "address":
//				$person->add_address($value);
				return null;

			case "default_phone":
				return $person->prop("fake_phone");
		
			case "email":
				return $person->prop("fake_email");
		
			default:
				return $person->is_property($field_id) ? $person->prop($field_id) : null;
		}
	}
	
	function save_form_data($data)
	{
		$person = $this->__save_person($data[crm_person_obj::CLID]);
		if (!empty($data[crm_company_obj::CLID]))
		{
			$organisation = $this->__save_organisation($data[crm_company_obj::CLID]);
		}
		if ($this->__create_new_objects())
		{
			$this->__create_work_relations($person, isset($organisation) ? array($organisation) : array());
		}

		$this->__save_customer_relations($person, isset($data[crm_company_customer_data_obj::CLID]) ? $data[crm_company_customer_data_obj::CLID] : null);
	}
	
	private function __create_new_objects()
	{
		return !users::is_logged_in();
	}
	
	private function __save_person($data)
	{
		if (!$this->__create_new_objects())
		{
			$person = obj(user::get_current_person(), null, crm_person_obj::CLID);
		}
		else
		{
			$person = obj(null, null, crm_person_obj::CLID);		
			$person->set_parent($this->id());
		}
		
		foreach($data as $key => $value)
		{
			$this->__set_person_property($person, $key, $value);
		}
		
		$person->save();
		
		return $person;
	}
	
	private function __set_person_property($person, $key, $value)
	{
		switch($key)
		{
			case "postal_address":
			case "address":
				$person->add_address($value);
				break;

			case "default_phone":
				$person->set_prop("fake_phone", $value);
				break;
		
			case "email":
				$person->set_prop("fake_email", $value);
				break;
		
			default:
				if ($person->is_property($key))
				{
					$person->set_prop($key, $value);
				}
		}
	}
	
	private function __save_organisation($data)
	{
		if (!$this->__create_new_objects() && is_oid(user::get_current_company()))
		{
			$organisation = obj(user::get_current_company(), null, crm_company_obj::CLID);
		}
		else
		{
			$organisation = obj(null, null, crm_company_obj::CLID);
			$organisation->set_parent($this->id());
		}
		
		foreach($data as $key => $value)
		{
			$this->__set_organisation_property($organisation, $key, $value);
		}
		
		$organisation->save();
		
		return $organisation;
	}
	
	private function __set_organisation_property($organisation, $key, $value)
	{
		switch($key)
		{
			case "web_address":
				$organisation->set_prop("fake_url", $value);
				break;
				
			case "phone":
				$organisation->set_prop("fake_phone", $value);
				break;
		
			case "email":
				$organisation->set_prop("fake_email", $value);
				break;
		
			default:
				if ($organisation->is_property($key))
				{
					$organisation->set_prop($key, $value);
				}
		}
	}
	
	private function __create_work_relations($person, $additionnal_organisations = array())
	{
		$organisations = $this->__get_organisations_for_work_relations() + $additionnal_organisations;
		foreach($organisations as $organisation)
		{
			$organisation->add_employee(null, $person);
		}
	}
	
	private function __get_organisations_for_work_relations()
	{
		$employment_rules = new object_data_list(
			array(
				"class_id" => join_site_employment_obj::CLID,
				"join_site" => $this->id(),
			),
			array(
				join_site_employment_obj::CLID => array("organisation"),
			)
		);
		
		$organisation_ids = $employment_rules->get_element_from_all("organisation");
		
		if (empty($organisation_ids))
		{
			return array();
		}
		
		$organisations = new object_list(array(
			"class_id" => crm_company_obj::CLID,
			"oid" => $organisation_ids,
		));
		
		return $organisations->arr();
	}
	
	private function __save_customer_relations($person, $data)
	{
		$customer_relation_rules = $this->__get_customer_relation_rules();
		foreach($customer_relation_rules as $customer_relation_rule)
		{
			if ((int)$customer_relation_rule->type === join_site_customer_relation_obj::TYPE_SELLER)
			{
				$buyer = $person;
				$seller = $customer_relation_rule->organisation();
			}
			else
			{
				$buyer = $customer_relation_rule->organisation();
				$seller = $person;
			}
			
			if (!is_object($seller) || !is_object($buyer) || !$buyer->is_saved() || !$seller->is_saved())
			{
				continue;
			}
			
			$customer_relation = $buyer->find_customer_relation($seller, true);
			$customer_relation->categories = isset($data["categories_".$customer_relation_rule->organisation]) ? $data["categories_".$customer_relation_rule->organisation] : $customer_relation_rule->customer_groups;
			$customer_relation->save();
		}
	}
	
	private function __get_customer_relation_rules()
	{
		$customer_relation_rules = new object_list(array(
			"class_id" => join_site_customer_relation_obj::CLID,
			"join_site" => $this->id(),
		));
		
		return $customer_relation_rules->arr();
	}
}
