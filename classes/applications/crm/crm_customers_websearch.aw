<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_crm_customers_websearch master_index=brother_of master_table=objects index=aw_oid

@default table=objects field=meta method=serialize

@default group=general

	@property owner type=objpicker clid=CL_CRM_COMPANY,CL_CRM_PERSON
	@caption Kliendisuhte omanik
	
	@property type type=chooser multiple=1
	@caption Otsitakse
	
	@property root_category type=select
	@caption Juurkliendikategooria
	@comment Selle kliendikategooria alamkategooriaid saab otsingus valida. Kui juurkliendikategooria on valimata, kuvatakse otsingus k&otilde;iki kliendikategooriaid.
	
	@property skill_manager type=relpicker reltype=RELTYPE_SKILL_MANAGER
	@caption P&auml;devuste haldur
	
	@property searchable_skills type=select
	@caption P&auml;devused
	@comment Otsingus kuvatavad p&auml;devused (kuvatakse alamp&auml;devusi)
	
	@property skill_search_type type=chooser orient=vertical
	@caption P&auml;devuste otsingu t&uuml;&uuml;p
	
	@property action_document type=relpicker reltype=RELTYPE_DOCUMENT
	@caption Otsingute kuvamise dokument
	
	@property search_by_employee_name type=checkbox
	@caption Otsi t&ouml;&ouml;taja nimest
	
@reltype DOCUMENT clid=CL_DOCUMENT value=1
@caption Dokument

@reltype SKILL_MANAGER clid=CL_PERSON_SKILL_MANAGER value=2
@caption P&auml;devuste haldur

*/

class crm_customers_websearch extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_customers_websearch",
			"clid" => crm_customers_websearch_obj::CLID
		));
	}
	
	function _get_type(&$arr)
	{
		$arr["prop"]["options"] = array(
			crm_customers_websearch_obj::TYPE_SELLER => t("Müüjaid"),
			crm_customers_websearch_obj::TYPE_BUYER => t("Ostjaid"),
		);
		
		return class_base::PROP_OK;
	}
	
	function _get_root_category(&$arr)
	{
		if (!object_loader::can("", $arr["obj_inst"]->owner))
		{
			$arr["prop"]["type"] = "text";
			$arr["prop"]["value"] = t("Palun vali esmalt kliendisuhte omanik");
		}
		else
		{
			$owner = obj($arr["obj_inst"]->owner);
			$arr["prop"]["options"] = $owner->is_a(crm_company_obj::CLID) ? array(0 => t("--vali--")) + $owner->get_customer_categories()->names() : array();
		}
		
		return class_base::PROP_OK;
	}
	
	function _get_searchable_skills(&$arr)
	{
		if (!object_loader::can("", $arr["obj_inst"]->skill_manager))
		{
			$arr["prop"]["type"] = "text";
			$arr["prop"]["value"] = t("Palun vali esmalt pädevuste haldur");
		}
		else
		{
			$arr["prop"]["options"] = array(null => t("--vali--"));

			$skill_manager = obj($arr["obj_inst"]->skill_manager, null, person_skill_manager_obj::CLID);
			foreach($skill_manager->get_root_skills()->names() as $root_skill_id => $root_skill_name)
			{
				$this->__insert_skill_to_options($skill_manager, $arr["prop"]["options"], $root_skill_id, $root_skill_name);
			}
		}
		
		return class_base::PROP_OK;
	}
	
	private function __insert_skill_to_options($skill_manager, &$options, $skill_id, $skill_name, $nested_lvl = 0)
	{
		$options[$skill_id] = sprintf("%s%s", str_repeat("-- ", $nested_lvl), $skill_name);
		foreach($skill_manager->get_all_skills($skill_id)->names() as $skill_id => $skill_name)
		{
			$this->__insert_skill_to_options($skill_manager, $options, $skill_id, $skill_name, $nested_lvl + 1);
		}
	}
	
	public function _get_skill_search_type(&$arr)
	{
		if (!object_loader::can("", $arr["obj_inst"]->skill_manager))
		{
			return class_base::PROP_IGNORE;
		}
		else
		{
			$arr["prop"]["options"] = array(
				crm_customers_websearch_obj::SEARCH_TYPE_OR => t("Leitakse kliendid, kel on vähemalt üks valitud pädevus"),
				crm_customers_websearch_obj::SEARCH_TYPE_AND => t("Leitakse kliendid, kel on kõik valitud pädevused"),
			);
		}
		
		return class_base::PROP_OK;
	}

	/**
		@attrib name=show params=name
		@param id required type=int
		@param charset optional type=string
	**/
	public function show($arr)
	{
		$websearch = obj($arr["id"], array(), crm_customers_websearch_obj::CLID);

		$this->read_template("show.tpl");
		
		$this->vars(array(
			"form.action" => object_loader::can("", $websearch->action_document) ? doc_display::get_doc_link(obj($websearch->action_document, null, doc_obj::CLID)) : null,
			"name.value" => automatweb::$request->arg_isset("name") ? automatweb::$request->arg("name") : null,
		));
		
		$CUSTOMER_CATEGORY = "";
		foreach ($websearch->get_customer_categories()->names() as $id => $name)
		{
			$this->vars(array(
				"customer_category.id" => $id,
				"customer_category.name" => $name,
				"customer_category.checked" => automatweb::$request->arg_isset("category") && in_array($id, (array)automatweb::$request->arg("category")) ? "checked" : null,
			));
			$CUSTOMER_CATEGORY .= $this->parse("CUSTOMER_CATEGORY");
		}
		
		$SKILL = "";
		if (object_loader::can("", $websearch->skill_manager) && object_loader::can("", $websearch->searchable_skills))
		{
			$skill_manager = obj($websearch->skill_manager, null, person_skill_manager_obj::CLID);
			foreach($skill_manager->get_all_skills($websearch->searchable_skills)->names() as $skill_id => $skill_name)
			{
				$this->vars(array(
					"skill.id" => $skill_id,
					"skill.name" => $skill_name,
					"skill.checked" => automatweb::$request->arg_isset("skill") && in_array($skill_id, (array)automatweb::$request->arg("skill")) ? "checked" : null,
				));
				$SKILL .= $this->parse("SKILL");
			}
		}
		
		$this->vars_safe(array(
			"CUSTOMER_CATEGORY" => $CUSTOMER_CATEGORY,
			"SKILL" => $SKILL
		));

		return $this->parse();
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_crm_customers_websearch" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_crm_customers_websearch` (
				  `aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`aw_oid`)
				)");
				$r = true;
			}
			elseif ("" === $field)
			{
				$this->db_add_col("aw_crm_customers_websearch", array(
					"name" => $field,
					"type" => ""
				));
				$r = true;
			}
		}

		return $r;
	}
}
