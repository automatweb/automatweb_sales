<?php
/*

EMIT_MESSAGE(MSG_USER_JOINED)

@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@default table=objects
@default field=meta
@default method=serialize

@groupinfo subgeneral caption=Andmed parent=general
@default group=subgeneral

		@property name type=textbox table=objects field=name method=none
		@caption Name

		@property autoactivate type=checkbox
		@caption Autoaktiveerimine
		@comment Uued kasutajad aktiveeritakse automaatselt
		
		@property languages type=chooser multiple=1 orient=vertical table=objects field=meta method=serialize
		@caption Keeled

	@groupinfo authentication caption=Autentimine parent=general
	@default group=authentication
		
		@property authentication_toolbar type=toolbar store=no no_caption=1

	@groupinfo extras caption=Lisategevused parent=general
	@default group=extras
		
		@property extras_toolbar type=toolbar store=no no_caption=1

@groupinfo form caption=Vorm
@default group=form

	@groupinfo form_fields caption=Omadused parent=form
	@default group=form_fields
	
		@property form_fields_table type=table store=no no_caption=1
		
	@groupinfo form_translations caption=Omaduste&nbsp;tõlked parent=form
	@default group=form_translations
	
		@property form_translations_table type=table store=no no_caption=1

	@groupinfo form_groups caption=Jaotus parent=form
	@default group=form_groups
	
		@property form_groups_table type=table store=no no_caption=1

@groupinfo relations caption=Suhted	

	@groupinfo customer_relations caption=Kliendisuhted parent=relations
	@default group=customer_relations
	
		@property customer_relations_toolbar type=toolbar store=no no_caption=1
	
		@property customer_relations_table type=table store=no no_caption=1
		
		@property customer_relations_new type=hidden store=no

	@groupinfo employments caption=Töösuhted parent=relations
	@default group=employments

		@property employments_toolbar type=toolbar store=no no_caption=1
		
		@property employments_table type=table store=no no_caption=1
		
		@property employments_new type=hidden store=no

@groupinfo emails caption=Kirjad
@default group=emails
	
	@property emails_toolbar type=toolbar store=no no_caption=1
	
	@property emails_table type=table store=no no_caption=1

@reltype MAIL_TEMPLATE clid=CL_MESSAGE_TEMPLATE value=1
@caption E-kirja šabloon	

*/

class join_site extends class_base
{
	private static $__form_field_type_captions = array(
		join_site_obj::FIELD_TYPE_SELECT => "Rippvalik",
		join_site_obj::FIELD_TYPE_RADIOS => "Raadionupud",
		join_site_obj::FIELD_TYPE_CHECKBOXES => "Valikruudud",
	);
				
	public function __construct()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/join/join_site",
			"clid" => join_site_obj::CLID
		));
	}
	
	public function _get_languages(&$arr)
	{
		$languages = array();
		
		foreach(aw_ini_get("languages.list") as $language_id => $language)
		{
			$languages[$language_id] = $language["name"];
		}
		
		$arr["prop"]["options"] = $languages;

		return PROP_OK;
	}
	
	public function _get_form_groups_table(&$arr)
	{
		$languages = $arr["obj_inst"]->get_languages();

		$t = $arr["prop"]["vcl_inst"];
		
		$t->set_default("align", "center");
		$table_fields = array(
			"ord" => t("Jrk"),
			"name" => sprintf(t("Nimi (%s)"), $languages[$arr["obj_inst"]->prop("lang_id")]["name"]),
			"comment" => sprintf(t("Kommentaar (%s)"), $languages[$arr["obj_inst"]->prop("lang_id")]["name"]),
		);
		foreach($languages as $language_id => $language)
		{
			if ($language_id == $arr["obj_inst"]->prop("lang_id"))
			{
				// Handled by default fields!
				continue;
			}
			$table_fields["name_{$language_id}"] = sprintf(t("Nimi (%s)"), $language["name"]);
			$table_fields["comment_{$language_id}"] = sprintf(t("Kommentaar (%s)"), $language["name"]);
		}
		$t->add_fields($table_fields);
		
		$new_i = 0;
		foreach($arr["obj_inst"]->get_form_groups() as $i => $form_group)
		{
			$data = array(
				"name" => html::textbox(array(
					"name" => "form_groups[{$i}][name]",
					"value" => $form_group["name"],
				)),
				"comment" => html::textbox(array(
					"name" => "form_groups[{$i}][comment]",
					"value" => $form_group["comment"],
				)),
				"ord" => html::textbox(array(
					"name" => "form_groups[{$i}][ord]",
					"value" => $form_group["ord"],
					"size" => 2
				)),
			);
			foreach($languages as $language_id => $language)
			{
				$data["name_{$language_id}"] = html::textbox(array(
					"name" => "form_groups[{$i}][translations][{$language_id}][name]",
					"value" => isset($form_group["translations"][$language_id]["name"]) ? $form_group["translations"][$language_id]["name"] : null,
				));
				$data["comment_{$language_id}"] = html::textbox(array(
					"name" => "form_groups[{$i}][translations][{$language_id}][comment]",
					"value" => isset($form_group["translations"][$language_id]["comment"]) ? $form_group["translations"][$language_id]["comment"] : null,
				));
			}
			$t->define_data($data);

			$new_j = 0;
			if (!empty($form_group["subgroups"]))
			{
				foreach($form_group["subgroups"] as $j => $form_subgroup)
				{
					$data = array(
						"name" => str_repeat("&nbsp;", 15).html::textbox(array(
							"name" => "form_groups[{$i}][subgroups][{$j}][name]",
							"value" => $form_subgroup["name"],
						)),
						"comment" => html::textbox(array(
							"name" => "form_groups[{$i}][subgroups][{$j}][comment]",
							"value" => $form_subgroup["comment"],
						)),
						"ord" => html::textbox(array(
							"name" => "form_groups[{$i}][subgroups][{$j}][ord]",
							"value" => $form_subgroup["ord"],
							"size" => 2
						)),
					);
					foreach($languages as $language_id => $language)
					{
						$data["name_{$language_id}"] = html::textbox(array(
							"name" => "form_groups[{$i}][subgroups][{$j}][translations][{$language_id}][name]",
							"value" => isset($form_subgroup["translations"][$language_id]["name"]) ? $form_subgroup["translations"][$language_id]["name"] : null,
						));
						$data["comment_{$language_id}"] = html::textbox(array(
							"name" => "form_groups[{$i}][subgroups][{$j}][translations][{$language_id}][comment]",
							"value" => isset($form_subgroup["translations"][$language_id]["comment"]) ? $form_subgroup["translations"][$language_id]["comment"] : null,
						));
					}
					$t->define_data($data);
					$new_j = max($new_j, $j + 1);
				}
			}
			$data = array(
				"name" => str_repeat("&nbsp;", 15).html::textbox(array(
					"name" => "form_groups[{$i}][subgroups][{$new_j}][name]",
				)),
				"comment" => html::textbox(array(
					"name" => "form_groups[{$i}][subgroups][{$new_j}][comment]",
				)),
				"ord" => html::textbox(array(
					"name" => "form_groups[{$i}][subgroups][{$new_j}][ord]",
					"size" => 2
				)),
			);
			foreach($languages as $language_id => $language)
			{
				$data["name_{$language_id}"] = html::textbox(array(
					"name" => "form_groups[{$i}][subgroups][{$new_j}][translations][{$language_id}][name]"
				));
				$data["comment_{$language_id}"] = html::textbox(array(
					"name" => "form_groups[{$i}][subgroups][{$new_j}][translations][{$language_id}][comment]"
				));
			}
			$t->define_data($data);
			$new_i = max($new_i, $i + 1);
		}
		
		$data = array(
			"name" => html::textbox(array(
				"name" => "form_groups[{$new_i}][name]",
			)),
			"comment" => html::textbox(array(
				"name" => "form_groups[{$new_i}][comment]",
			)),
			"ord" => html::textbox(array(
				"name" => "form_groups[{$new_i}][ord]",
				"size" => 2
			)),
		);
		foreach($languages as $language_id => $language)
		{
			$data["name_{$language_id}"] = html::textbox(array(
				"name" => "form_groups[{$new_i}][translations][{$language_id}][name]"
			));
			$data["comment_{$language_id}"] = html::textbox(array(
				"name" => "form_groups[{$new_i}][translations][{$language_id}][comment]"
			));
		}
		$t->define_data($data);
		
		return PROP_OK;
	}
	
	public function _set_form_groups_table(&$arr)
	{
		$form_groups = $arr["request"]["form_groups"];
		
		foreach ($form_groups as $i => $form_group)
		{
			if (empty($form_group["name"]))
			{
				unset($form_groups[$i]);
				continue;
			}
			foreach ($form_group["subgroups"] as $j => $form_subgroup)
			{
				if (empty($form_subgroup["name"]))
				{
					unset($form_groups[$i]["subgroups"][$j]);
				}
			}
		}
		
		$arr["obj_inst"]->set_meta("form_groups", $form_groups);
	
		return PROP_OK;
	}
	
	public function _get_form_fields_table(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		
		$t->set_default("align", "center");
		$t->add_fields(array(
			"active" => t("Aktiivne"),
			"ord" => t("Jrk"),
		));
		$t->set_default("align", "left");
		$t->add_fields(array(
			"field" => t("Andmeväli"),
		));
		$t->set_default("align", "center");
		$t->add_fields(array(
			"caption" => t("Pealkiri"),
			"comment" => t("Kommentaar"),
			"type" => t("Andmevälja tüüp"),
			"group" => t("Leht"),
			"subgroup" => t("Grupp"),
			"required" => t("Kohustuslik"),
		));
		
		$classes = array(
			crm_person_obj::CLID => "Isik",
			crm_company_obj::CLID => "Organisatsioon",
			crm_company_customer_data_obj::CLID => "Kliendisuhe",
		);
		
		$fields = $this->__get_form_fields($arr["obj_inst"]);
		
		$form_groups = array(-1 => null);
		$form_subgroups = array();
		foreach($arr["obj_inst"]->meta("form_groups") as $form_group_id => $form_group)
		{
			$form_groups[$form_group_id] = $form_group["name"];
			$form_subgroups[$form_group_id] = array(null => null);
			foreach($form_group["subgroups"] as $form_subgroup_id => $form_subgroup)
			{
				$form_subgroups[$form_group_id][$form_subgroup_id] = $form_subgroup["name"];
			}
		}
		
		foreach ($classes as $clid => $clcaption)
		{
			$t->define_data(array(
				"field" => html::bold($clcaption)
			));
			foreach ($fields[$clid] as $field_id => $field)
			{
				$type_options = array();
				if(!empty($field["type_options"]))
				{
					foreach($field["type_options"] as $type_option)
					{
						$type_options[$type_option] = self::$__form_field_type_captions[$type_option];
					}
				}
				$t->define_data(array(
					"ord" => html::textbox(array(
						"name" => "form_fields[{$clid}][{$field_id}][ord]",
						"value" => !empty($fields[$clid][$field_id]["ord"]) ? (int)$fields[$clid][$field_id]["ord"] : 0,
						"size" => 2,
					)),
					"active" => html::checkbox(array(
						"name" => "form_fields[{$clid}][{$field_id}][active]",
						"checked" => !empty($fields[$clid][$field_id]["active"])
					)),
					"field" => $field["original_caption"],
					"caption" => html::textbox(array(
						"name" => "form_fields[{$clid}][{$field_id}][caption]",
						"value" => !empty($fields[$clid][$field_id]["caption"]) ? $fields[$clid][$field_id]["caption"] : $field["caption"],
					)),
					"comment" => html::textbox(array(
						"name" => "form_fields[{$clid}][{$field_id}][comment]",
						"value" => !empty($fields[$clid][$field_id]["comment"]) ? $fields[$clid][$field_id]["comment"] : null,
					)),
					"type" => !empty($type_options) ? html::select(array(
						"name" => "form_fields[{$clid}][{$field_id}][type]",
						"options" => $type_options,
						"value" => !empty($fields[$clid][$field_id]["type"]) ? $fields[$clid][$field_id]["type"] : $field["type"],
					)) : "",
					"group" => html::select(array(
						"name" => "form_fields[{$clid}][{$field_id}][group]",
						"options" => $form_groups,
						"selected" => isset($fields[$clid][$field_id]["group"]) ? $fields[$clid][$field_id]["group"] : null,
					)),
					"subgroup" => html::select(array(
						"name" => "form_fields[{$clid}][{$field_id}][subgroup]",
						"options" => (isset($fields[$clid][$field_id]["group"]) && !empty($form_subgroups[$fields[$clid][$field_id]["group"]])) ? $form_subgroups[$fields[$clid][$field_id]["group"]] : null,
						"selected" => isset($fields[$clid][$field_id]["subgroup"]) ? $fields[$clid][$field_id]["subgroup"] : null,
					)),
					"required" => html::checkbox(array(
						"name" => "form_fields[{$clid}][{$field_id}][required]",
						"checked" => !empty($fields[$clid][$field_id]["required"])
					)),
				));
			}
		}
		
		return PROP_OK;
	}
	
	private function __get_form_fields($o)
	{
		$default_fields = $o->get_default_form_fields();
		foreach($default_fields as $clid => $cl_fields)
		{
			foreach($cl_fields as $field_id => $field)
			{
				$default_fields[$clid][$field_id]["original_caption"] = $field["caption"];
			}
		}
		
		$form_fields = $o->meta("form_fields");
		
		$fields = array_replace_recursive($default_fields, $form_fields);
		
		foreach(array_keys($fields) as $clid)
		{
			uasort($fields[$clid], function($a, $b){
				if (empty($a["active"]) && !empty($b["active"])) return 1;
				if (!empty($a["active"]) && empty($b["active"])) return -1;
				return $a["ord"] - $b["ord"];
			});
		}
			
		return $fields;
	}
	
	public function _set_form_fields_table(&$arr)
	{
		$arr["obj_inst"]->set_meta("form_fields", $arr["request"]["form_fields"]);
		
		return PROP_OK;
	}
	
	public function _get_form_translations_table(&$arr)
	{
		$languages = $arr["obj_inst"]->get_languages();

		$t = $arr["prop"]["vcl_inst"];
		
		$table_fields = array("field" => "Andmeväli");
		foreach($languages as $language_id => $language)
		{
			$table_fields["caption_{$language_id}"] = sprintf(t("Pealkiri (%s)"), $language["name"]);
			$table_fields["comment_{$language_id}"] = sprintf(t("Kommentaar (%s)"), $language["name"]);
		}
		$t->add_fields($table_fields);
		
		$fields = $this->__get_form_fields($arr["obj_inst"]);
		$translations = $arr["obj_inst"]->get_translations();
		
		$classes = array(
			crm_person_obj::CLID => "Isik",
			crm_company_obj::CLID => "Organisatsioon",
			crm_company_customer_data_obj::CLID => "Kliendisuhe",
		);
		foreach ($classes as $clid => $clcaption)
		{
			$t->define_data(array(
				"field" => html::bold($clcaption)
			));
			foreach ($fields[$clid] as $field_id => $field)
			{
				$data = array(
					"field" => $field["original_caption"],
				);
				foreach($languages as $language_id => $language)
				{
					$data["caption_{$language_id}"] = html::textbox(array(
						"name" => "form_translations[{$clid}][{$field_id}][{$language_id}][caption]",
						"value" => isset($translations[$clid][$field_id][$language_id]["caption"]) ? $translations[$clid][$field_id][$language_id]["caption"] : null,
					));
					$data["comment_{$language_id}"] = html::textbox(array(
						"name" => "form_translations[{$clid}][{$field_id}][{$language_id}][comment]",
						"value" => isset($translations[$clid][$field_id][$language_id]["comment"]) ? $translations[$clid][$field_id][$language_id]["comment"] : null,
					));
				}
				$t->define_data($data);
			}
		}
			
		return PROP_OK;
	}
	
	public function _set_form_translations_table(&$arr)
	{
		$arr["obj_inst"]->set_translations($arr["request"]["form_translations"]);
		
		return PROP_OK;
	}
	
	public function _get_customer_relations_toolbar(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		
		$t->add_search_button(array(
			'name' => 'add_customer_relation',
			'pn' => 'customer_relations_new',
			'clid' => crm_company_obj::CLID
		));
		$t->add_delete_button();
		
		return PROP_OK;
	}
	
	public function _get_customer_relations_table(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		
		$t->define_chooser();
		$t->add_fields(array(
			"organisation" => t("Organisatsioon"),
			"customer_groups" => t("Kliendigrupid"),
			"type" => t("Tüüp"),
		));
		
		$customer_relations = new object_list(array(
			"class_id" => join_site_customer_relation_obj::CLID,
			"join_site" => $arr["obj_inst"]->id
		));
		
		foreach($customer_relations->arr() as $customer_relation_id => $customer_relation)
		{
			$t->define_data(array(
				"oid" => $customer_relation_id,
				"organisation" => $customer_relation->prop("organisation.name"),
				"customer_groups" => html::select(array(
					"name" => "customer_relations[{$customer_relation_id}][customer_groups]",
					"options" => $customer_relation->organisation()->get_customer_categories()->names(),					
					"selected" => $customer_relation->customer_groups,
					"multiple" => true,
				)),
				"type" => html::select(array(
					"name" => "customer_relations[{$customer_relation_id}][type]",
					"options" => array("Ostja", "Müüja"),
					"selected" => $customer_relation->type
				))
			));
		}
		
		return PROP_OK;
	}
	
	public function _set_customer_relations_table(&$arr)
	{
		if (!empty($arr["request"]["customer_relations"]))
		{
			foreach ($arr["request"]["customer_relations"] as $customer_relation_id => $customer_relation_data)
			{
				$customer_relation = obj($customer_relation_id, null, join_site_customer_relation_obj::CLID);
				$customer_relation->set_prop("customer_groups", $customer_relation_data["customer_groups"]);
				$customer_relation->set_prop("type", $customer_relation_data["type"]);
				$customer_relation->save();
			}
		}
		
		return PROP_OK;
	}
	
	public function _get_employments_toolbar(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		
		$t->add_search_button(array(
			'name' => 'add_employment',
			'pn' => 'employments_new',
			'clid' => crm_company_obj::CLID
		));
		$t->add_delete_button();
		
		return PROP_OK;
	}
	
	public function _get_employments_table(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		
		$t->define_chooser();
		$t->add_fields(array(
			"organisation" => t("Organisatsioon"),
		));
		
		$employments = new object_list(array(
			"class_id" => join_site_employment_obj::CLID,
			"join_site" => $arr["obj_inst"]->id
		));
		
		foreach($employments->arr() as $employment_id => $employment)
		{
			$t->define_data(array(
				"oid" => $employment_id,
				"organisation" => $employment->prop("organisation.name"),
			));
		}
		
		return PROP_OK;
	}
	
	public function _get_emails_toolbar(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		
		$t->add_new_button(array(CL_MESSAGE_TEMPLATE), $arr["obj_inst"]->id, 1 /* RELTYPE_MAIL_TEMPLATE */);
		$t->add_delete_button();
		
		return PROP_OK;
	}
	
	public function _get_emails_table(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		
		$t->define_chooser();
		$t->add_fields(array(
			"name" => t("Nimi"),
			"type" => t("Millal saadetakse?"),
		));
		
		$emails = new object_list(array(
			"class_id" => CL_MESSAGE_TEMPLATE,
			"RELTYPE_MAIL_TEMPLATE(CL_JOIN_SITE).id" => $arr["obj_inst"]->id,
		));
		
		$email_types = $arr["obj_inst"]->meta("emails");
		foreach($emails as $email)
		{
			$t->define_data(array(
				"oid" => $email->id,
				"name" => html::obj_change_url($email),
				"type" => html::select(array(
					"name" => "emails[{$email->id}][type]",
					"options" => array("Liitumisel", "Andmete uuendamisel", "Parooli meeldetuletus"),
					"selected" => !empty($email_types[$email->id]["type"]) ? $email_types[$email->id]["type"] : null
				)),
			));
		}
		
		return PROP_OK;
	}
	
	public function _set_emails_table(&$arr)
	{	
		if(!empty($arr["request"]["emails"]))
		{
			$arr["obj_inst"]->set_meta("emails", $arr["request"]["emails"]);
		}
		
		return PROP_OK;
	}
	
	public function callback_post_save($arr)
	{
		if(!empty($arr["request"]["customer_relations_new"]))
		{
			$ol = new object_list(array(
				"class_id" => join_site_customer_relation_obj::CLID,
				"join_site" => $arr["obj_inst"]->id,
				"organisation" => $arr["request"]["customer_relations_new"],
			));
			
			if($ol->count() === 0)
			{
				$customer_relation = obj(null, null, join_site_customer_relation_obj::CLID);
				$customer_relation->set_parent($arr["obj_inst"]->id);
				$customer_relation->set_prop("join_site", $arr["obj_inst"]->id);
				$customer_relation->set_prop("organisation", $arr["request"]["customer_relations_new"]);
				$customer_relation->save();
			}
		}
		
		if(!empty($arr["request"]["employments_new"]))
		{
			$ol = new object_list(array(
				"class_id" => join_site_employment_obj::CLID,
				"join_site" => $arr["obj_inst"]->id,
				"organisation" => $arr["request"]["employments_new"],
			));
			
			if($ol->count() === 0)
			{
				$customer_relation = obj(null, null, join_site_employment_obj::CLID);
				$customer_relation->set_parent($arr["obj_inst"]->id);
				$customer_relation->set_prop("join_site", $arr["obj_inst"]->id);
				$customer_relation->set_prop("organisation", $arr["request"]["employments_new"]);
				$customer_relation->save();
			}
		}
	}
	
	public function show($arr)
	{
		$o = obj($arr["id"], null, join_site_obj::CLID);
		
		$this->read_template("join.tpl");
		
		$groups = $o->get_form_groups();
		
		$this->__parse_form_header($groups);
		
		$GROUP = "";
		$group_count = 0;
		foreach($groups as $group_id => $group)
		{
			$group_count++;
			$SUBGROUP = $this->__parse_form_subgroup($o, $group_id, null, null);
			foreach($group["subgroups"] as $subgroup_id => $subgroup)
			{
				$SUBGROUP .= $this->__parse_form_subgroup($o, $group_id, $subgroup_id, $subgroup);
			}
			$this->vars(array(
				"FORM.SUBMIT" => $group_count === count($groups) ? $this->parse("FORM.SUBMIT") : "",
				"FORM.SUBGROUP" => $SUBGROUP,
			));
			$GROUP .= $this->parse("FORM.GROUP");
		}
		$this->vars(array(
			"FORM.GROUP" => $GROUP,
			"FORM.REFORB" => $this->parse("FORM.REFORB"),
		));
		$this->vars(array(
			"HEADER" => $this->parse("HEADER"),
			"FORM" => $this->parse("FORM"),
		));
		
		return $this->parse();
	}
	
	private function __parse_form_subgroup($o, $group_id, $subgroup_id, $subgroup)
	{
		$fields = $o->get_form_fields($group_id, $subgroup_id);
		if (empty($fields))
		{
			return "";
		}
		$FIELD = "";
		foreach($fields as $field_id => $field)
		{
			$FIELD .= $this->__parse_form_field($o, $field_id, $field);
		}
		$this->vars(array(
			"subgroup.name" => $subgroup_id !== null ? $subgroup["name"] : null,
			"FORM.FIELD" => $FIELD,
		));
		$this->vars(array(
			"FORM.SUBGROUP.HAS_NAME" => $subgroup_id !== null && isset($subgroup["name"]) && strlen($subgroup["name"]) > 0 ? $this->parse("FORM.SUBGROUP.HAS_NAME") : "",
		));
		
		return $this->parse("FORM.SUBGROUP");
	}
	
	private function __parse_form_header($groups)
	{
		$GROUP = "";
		foreach($groups as $group_id => $group)
		{
			$this->vars(array(
				"group.name" => $group["name"],
				"group.comment" => $group["comment"],
			));
			$GROUP .= $this->parse("HEADER.GROUP");
		}
		$this->vars(array(
			"HEADER.GROUP" => $GROUP,
		));
	}
	
	private function __parse_form_field($o, $field_id, $field)
	{
		$field_value = $o->get_form_field_value($field);
		$this->vars(array(
			"field.id" => $field_id,
			"field.caption" => $field["caption"],
			"field.required" => !empty($field["required"]) ? trim($this->parse("FORM.FIELD.REQUIRED")) : "",
			"field.value" => $field_value,
		));
		if (isset($field["type"]) && $this->is_template("FORM.FIELD.ELEMENT.".strtoupper($field["type"])))
		{
			$field_callback = "__parse_form_field_{$field["type"]}";
			if (method_exists($this, $field_callback))
			{
				$this->$field_callback($field, $field_value);
			}
			$this->vars(array(
				"FORM.FIELD.ELEMENT" => $this->parse("FORM.FIELD.ELEMENT.".strtoupper($field["type"])),
			));
		}
		elseif ($this->is_template("FORM.FIELD.ELEMENT.DEFAULT"))
		{
			$this->vars(array(
				"FORM.FIELD.ELEMENT" => $this->parse("FORM.FIELD.ELEMENT.DEFAULT"),
			));
		}
		return $this->parse("FORM.FIELD");
	}
	
	private function __parse_form_field_select($field, $value)
	{
		$this->vars(array(
			"FORM.FIELD.ELEMENT.SELECT.OPTION" => $this->__parse_form_field_options($field, "SELECT", $value),
		));
	}
	
	private function __parse_form_field_radios($field, $value)
	{
		$this->vars(array(
			"FORM.FIELD.ELEMENT.RADIOS.OPTION" => $this->__parse_form_field_options($field, "RADIOS", $value),
		));
	}
	
	private function __parse_form_field_checkboxes($field, $value)
	{
		$this->vars(array(
			"FORM.FIELD.ELEMENT.CHECKBOXES.OPTION" => $this->__parse_form_field_options($field, "CHECKBOXES", $value),
		));
	}
	
	private function __parse_form_field_options($field, $field_type, $value)
	{
		$options = is_callable($field["options"]) ? call_user_func($field["options"]) : $field["options"];
		if ($options instanceof object_list)
		{
			$options = $options->names();
		}
		if (empty($options) || !is_array($options))
		{
			return null;
		}
		$OPTION = "";
		foreach($options as $option_value => $option_caption)
		{
			$this->vars(array(
				"field.element.option.value" => $option_value,
				"field.element.option.caption" => $option_caption,
				"field.element.option.selected" => in_array($option_value, (array)$value) ? $this->parse("FORM.FIELD.ELEMENT.{$field_type}.OPTION.SELECTED") : "",
			));
			$OPTION .= $this->parse("FORM.FIELD.ELEMENT.{$field_type}.OPTION");
		}
		return $OPTION;
	}
	
	private function __parse_form_field_address($field, $value)
	{
		$FIELD = "";
		foreach($field["address_fields"] as $address_field_id => $address_field)
		{
			$this->vars(array(
				"field.element.address.field.id" => $address_field_id,
				"field.element.address.field.caption" => $address_field["caption"],
			));
			if (!empty($address_field["type"]))
			{
				$address_field_callback = "__parse_form_field_address_{$address_field["type"]}";
				if (method_exists($this, $address_field_callback))
				{
					$FIELD = $this->$address_field_callback($address_field);
				}
			}
			else
			{
				$FIELD .= $this->parse("FORM.FIELD.ELEMENT.ADDRESS.FIELD.TEXT");
			}
		}
		$this->vars(array(
			"FORM.FIELD.ELEMENT.ADDRESS.FIELD" => $FIELD,
		));
	}
	
	private function __parse_form_field_address_select($address_field)
	{
		if(empty($address_field["options"]))
		{
			return "";
		}
		$options = is_callable($address_field["options"]) ? call_user_func($address_field["options"]) : $address_field["options"];
		$OPTION = "";
		foreach($options as $option_value => $option_caption)
		{
			$this->vars(array(
				"field.element.address.select.option.value" => $option_value,
				"field.element.address.select.option.caption" => $option_caption,
			));
			$OPTION .= $this->parse("FORM.FIELD.ELEMENT.ADDRESS.FIELD.SELECT.OPTION");
		}
		$this->vars(array(
			"FORM.FIELD.ELEMENT.ADDRESS.FIELD.SELECT.OPTION" => $OPTION,
		));
		return $this->parse("FORM.FIELD.ELEMENT.ADDRESS.FIELD.SELECT");
	}
	
	/**
		@attrib name=submit_join_form all_args=1 nologin=1
	**/
	public function submit_join_form($arr)
	{
		$o = obj($arr["id"], null, join_site_obj::CLID);
		$o->save_form_data($arr["data"]);
		
		var_dump($arr["data"]);
		
		die(".");
		
		header("Location: http://ehta.dev.automatweb.com/");
		exit;
	}
}
