<?php

class crm_customer_modal extends aw_modal {
	
	protected function get_header_template() {
		return new aw_php_template("crm_customer_modal", "header");
	}
	
	protected function get_content_template() {
		$template = new aw_php_template("crm_customer_modal", "content");
		
		// FIXME: skill_manager should be an actual object!
		$skill_manager = new person_skill_manager_obj();
		$skills_options = array();
		$this->__populate_skills_options($skill_manager, $skills_options);
		
		$template->add_vars(array(
			"corporate_form_options", crm_company_obj::get_company_forms()->names(),
			"email_type_options" => ml_member_obj::get_contact_type_names(),
			"phone_type_options" => crm_phone_obj::get_old_type_options(),
			"country_options" => array(24613 => "Eesti"),
			"gender_options" => crm_person_obj::gender_options(),
			"skills_options" => $skills_options,
		));
		return $template;
	}
	
	private function __populate_skills_options($skill_manager, &$options, $parent = null, $level = 0) {
		$skills = $parent !== null ? $skill_manager->get_all_skills($parent) : $skill_manager->get_root_skills();
		foreach ($skills->names() as $skill_id => $skill_name) {
			$options[$skill_id] = str_repeat("&nbsp; ", $level).$skill_name;
			$this->__populate_skills_options($skill_manager, $options, $skill_id, $level + 1);
		}
		
		return $options;
	}
	
	protected function get_footer_template() {
		return new aw_php_template("crm_customer_modal", "footer");
	}

	/**
		@attrib api=1
		@param company require type=oid
			OID of a CL_CRM_COMPANY object.
		@param customer require type=oid
			OID of a CL_CRM_PERSON or CL_CRM_COMPANY object.
		@returns JSON of the object.
	**/
	public function get_customer_data($arr)	{
		// FIXME: Add ACL check and error handling, and remove duplication of code (same code in crm_person::create_customer)!
		$company = obj($arr["company"], null, crm_company_obj::CLID);
		$customer = obj($arr["customer"], null);
		
		$data = $customer->json(false);
		
		// Include customer relation in JSON.
		$customer_relation = $company->get_customer_relation(isset($arr["type"]) ? $arr["type"] : crm_company_obj::CUSTOMER_TYPE_BUYER, $customer);
		if ($customer_relation !== null)
		{
			$data["customer_relation"] = $customer_relation->json(false);
		}
		
		$data["emails"] = array();
		foreach ($customer->get_email_addresses()->arr() as $email)
		{
			$data["emails"][] = $email->json(false);
		}
		
		$data["phones"] = array();
		foreach ($customer->get_phones()->arr() as $phone)
		{
			$data["phones"][] = $phone->json(false);
		}
		
		if ($customer->is_a(crm_company_obj::CLID)) {
			$data["employees"] = array();
			foreach($customer->get_employees()->arr() as $employee)
			{
				$data["employees"][] = $employee->json(false);
			}
		}
		
		$data["addresses"] = array();
		foreach($customer->get_addresses()->arr() as $address)
		{
			$data["addresses"][] = $address->json(false);
		}
		
		$encoder = new json();
		$json = $encoder->encode($data, aw_global_get("charset"));

		automatweb::$result->set_data($json);
		automatweb::$instance->http_exit();
	}
}