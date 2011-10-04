<?php

// crm_settings.aw - Kliendibaasi seaded
/*

@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@default table=objects


@default group=general
	@property s_cfgform type=relpicker reltype=RELTYPE_CFGFORM table=objects field=meta method=serialize
	@caption Kliendi seadete vorm

	@property s_p_cfgform type=relpicker reltype=RELTYPE_CFGFORM table=objects field=meta method=serialize
	@caption Eraisikust kliendi seadete vorm

	@property work_cfgform type=relpicker reltype=RELTYPE_CFGFORM table=objects field=meta method=serialize
	@caption Minu t&ouml;&ouml;koha seadete vorm

	@property coworker_cfgform type=relpicker reltype=RELTYPE_CFGFORM table=objects field=meta method=serialize
	@caption T&ouml;&ouml;kaaslaste seadete vorm

	@property customer_employer_cfgform type=relpicker reltype=RELTYPE_CFGFORM table=objects field=meta method=serialize
	@caption Kliendi t&ouml;&ouml;taja seadete vorm

	@property controller_proj type=relpicker reltype=RELTYPE_CTR table=objects field=meta method=serialize
	@caption Projekti kontroller

	@property controller_proj type=relpicker reltype=RELTYPE_CTR table=objects field=meta method=serialize
	@caption Projekti kontroller

	@property controller_task type=relpicker reltype=RELTYPE_CTR table=objects field=meta method=serialize
	@caption Toimetuse kontroller

         @property default_tasks_view type=select table=objects field=meta
  	         @caption Tegevused vaikimisi vaade

  	         @property group_task_view type=checkbox ch_value=1 table=objects field=meta
  	         @caption Grupeeri tegevused vaade

  	         @property view_task_rows_open type=checkbox ch_value=1 table=objects field=meta
  	         @caption Vaikimisi avatakse toimetuse nimel klikkides kohe read

	         @property default_task_rows_bills_filter type=select table=objects field=meta
  	         @caption Toimetuse ridades valitud Arve tulba vaikimisi filter

	@property task_rows_controller type=relpicker table=objects field=meta reltype=RELTYPE_CTR
	@caption Toimetuse ridade kontroller

	@property task_save_controller type=relpicker table=objects field=meta reltype=RELTYPE_CTR
	@caption Toimetuse salvestamise kontroller

	@property controller_meeting type=relpicker reltype=RELTYPE_CTR table=objects field=meta method=serialize
	@caption Kohtumise kontroller

	@property default_tasks_view type=select table=objects field=meta
	@caption Tegevused vaikimisi vaade

  	@property group_task_view type=checkbox ch_value=1 table=objects field=meta
  	@caption Grupeeri tegevused vaade

	@property view_task_rows_open type=checkbox ch_value=1 table=objects field=meta
	@caption Vaikimisi avatakse toimetuse nimel klikkides kohe read

	@property send_mail_feature type=checkbox ch_value=1 table=objects field=meta
	@caption V&otilde;imalus saata CRMis maile

	@property show_files_and_docs_in_tree type=checkbox ch_value=1 table=objects field=meta
	@caption N&auml;ita puus faile ja dokumente

	@property default_task_rows_bills_filter type=select table=objects field=meta
	@caption Toimetuse ridades valitud Arve tulba vaikimisi filter

	@property task_rows_controller type=relpicker table=objects field=meta reltype=RELTYPE_CTR
  	@caption Toimetuse ridade kontroller

  	@property task_save_controller type=relpicker table=objects field=meta reltype=RELTYPE_CTR
	@caption Toimetuse salvestamise kontroller

  	@property org_link_menu type=relpicker table=objects field=meta reltype=RELTYPE_MENU
	@caption Organisatsioonide linkide kataloog

  	@property insurance_link_menu type=relpicker table=objects field=meta reltype=RELTYPE_MENU
	@caption Kindlustuse t&uuml;&uuml;pide kataloog

	@property comment_menu type=relpicker reltype=RELTYPE_MENU table=objects field=meta method=serialize
	@caption Kommentaaride kaust

	@property default_my_company_tab type=select table=objects field=meta
  	@caption Minu organisatsiooni avanev tab

	@property default_client_company_tab type=select table=objects field=meta
  	@caption Kliendi organisatsiooni avanev tab



- Organisatsiooni Vaade V6tmes6nad, kus saab m2rkida systeemis olevaid v6tmes6nu, mis on systeemis olemas ja mida 6igused lubavad n2ha (ja ei salvesta yle neid v6tmes6nu mida kasutaja "ei n2e")


- T88tajad vaatesse
V6imalus m22rata, kes on volitatud isikud ja volituse alus. T88taja nime j2rele on v6imalik panna m2rkeruut tulpa &#8220;Volitatud&#8221;. Selle m2rkimisel avaneb uus aken, kus kysitakse volituse alust (Objektityyp Volitus). Volitus kehtib kolmese seosena (Meie firma, klientfirma, volitatav isik).

- Kontaktandmetesse seos: Keel
Vaikimisi eesti keel. Keelele peab saama m22rata, milline on systeemi default. Vaikimisi v22rtus Arve-saatelehel


@default group=tables
	@property tables_toolbar type=toolbar store=no no_caption=1

	@layout vsplitbox type=hbox no_caption=1

	@property tables_treemenu type=treeview store=no parent=vsplitbox no_caption=1
	@caption Tabelite valik

	@property table_cfg type=table store=no parent=vsplitbox
	@caption Tabeli konfiguratsioon


@default group=whom
	@property users type=relpicker multiple=1 store=connect reltype=RELTYPE_USER field=meta method=serialize
	@caption Kasutajad

	@property persons type=relpicker multiple=1 store=connect reltype=RELTYPE_PERSON field=meta method=serialize
	@caption Isikud

	@property cos type=relpicker multiple=1 store=connect reltype=RELTYPE_COMPANY field=meta method=serialize
	@caption Organisatsioonid

	@property sects type=relpicker multiple=1 store=connect reltype=RELTYPE_SECTION field=meta method=serialize
	@caption Osakonnad

	@property profs type=relpicker multiple=1 store=connect reltype=RELTYPE_PROFESSION field=meta method=serialize
	@caption Ametinimetused

	@property everyone type=checkbox ch_value=1 table=objects field=flags
	@caption K&otilde;ik


@default group=img
	@property person_img_settings type=relpicker reltype=RELTYPE_GALLERY_CONF field=meta method=serialize
	@caption Isiku piltide seaded


@default group=status_limits
	@property status_limits type=table
	@caption Staatuste piirangud


@default group=bill
	@layout bill_main type=hbox width=50%:50%
		@layout bills_mail_settings type=vbox parent=bill_main area_caption=Arvete&nbsp;e-kirjade&nbsp;vaikimisi&nbsp;seaded
			@property billable_only_by_mrg type=checkbox ch_value=1 table=objects field=meta parent=bills_mail_settings
			@caption Saata arve saab m&auml;&auml;rata toimetusele vaid kliendihaldur

			@property bill_mail_to type=textbox field=meta method=serialize parent=bills_mail_settings
			@caption Saaja(d)

			@property bill_mail_from type=textbox field=meta method=serialize parent=bills_mail_settings
			@caption Saatja aadress

			@property bill_mail_from_name type=textbox field=meta method=serialize parent=bills_mail_settings
			@caption Saatja nimi

			@property bill_mail_legend type=text store=no parent=bills_mail_settings
			@comment E-kirja sisus ja pealkirjas kasutatavad muutujad. Asendatakse saatmisel vastavate tegelike v&auml;&auml;rtustega
			@caption Kasutatavad muutujad

			@property bill_mail_subj type=textbox field=meta method=serialize parent=bills_mail_settings captionside=top
			@caption Pealkiri

			@property bill_mail_ct type=textarea rows=20 cols=50 field=meta method=serialize parent=bills_mail_settings captionside=top
			@caption Sisu

		@layout bills_other_settings type=vbox parent=bill_main area_caption=Muud&nbsp;seaded
			@property bill_default_due_days type=textbox size=3 field=meta method=serialize datatype=int default=14 parent=bills_other_settings
			@comment P&auml;eva. Vaikev&auml;&auml;rtus 14
			@caption Vaikimisi makset&auml;htaeg

			@property bill_default_overdue_interest type=textbox size=3 table=objects field=meta method=serialize default=0.15 parent=bills_other_settings
			@comment Vaikev&auml;&auml;rtus 0.15
			@caption Vaikimisi viivis (%)

			@property bill_hide_pwr type=checkbox ch_value=1 table=objects field=meta method=serialize parent=bills_other_settings
			@caption Peita eelvaade ridadega

			@property bill_hide_cr type=checkbox ch_value=1 table=objects field=meta method=serialize parent=bills_other_settings
			@caption Peita koonda read nupp

			@property bill_no_agreement_price type=hidden table=objects field=meta method=serialize

			@property bill_def_prod type=relpicker reltype=RELTYPE_PROD table=objects field=meta method=serialize parent=bills_other_settings
			@caption Vaikimisi toode arve ridadel

			@property bill_default_unit type=select table=objects field=meta method=serialize parent=bills_other_settings
			@caption Vaikimisi &Uuml;hik

			@property bill_default_tax type=relpicker multiple=1 store=connect reltype=RELTYPE_TAX_RATE parent=bills_other_settings
			@caption Vaikimisi k&auml;ibemaks


@groupinfo tables caption="Tabelid"
@groupinfo whom caption="Kellele kehtib"
@groupinfo img caption="Pildid"
@groupinfo status_limits caption="Staatuste piirangud"
@groupinfo bill caption="Arve seaded"


@reltype USER value=1 clid=CL_USER
@caption Kasutaja

@reltype CFGFORM value=2 clid=CL_CFGFORM
@caption Seadete vorm

@reltype PERSON value=3 clid=CL_CRM_PERSON
@caption Isik

@reltype COMPANY value=4 clid=CL_CRM_COMPANY
@caption Organisatsioon

@reltype SECTION value=5 clid=CL_CRM_SECTION
@caption Osakond

@reltype PROFESSION value=6 clid=CL_CRM_PROFESSION
@caption Ametinimetus

@reltype PROD value=7 clid=CL_SHOP_PRODUCT
@caption Toode

@reltype GALLERY_CONF value=8 clid=CL_GALLERY_CONF
@caption Galerii seaded

@reltype CTR value=9 clid=CL_FORM_CONTROLLER
@caption Kontroller

@reltype MENU value=10 clid=CL_MENU
@caption Kataloog

@reltype TAX_RATE value=11 clid=CL_CRM_TAX_RATE
@caption Maksum&auml;&auml;r

*/

class crm_settings extends class_base
{
	const LIST_LENGTH_DEFAULT = 25;

	// var ${"_properties" . CL_CRM_COMPANY} = array();
	// var ${"_properties" . CL_CRM_PERSON} = array();
	var $crmcfg_defined_tables = array();
	var $crmcfg_class_index = array();

	private $cl_cfgu;

	function crm_settings()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_settings",
			"clid" => CL_CRM_SETTINGS
		));

		$this->crmcfg_defined_tables = array(
			CL_CRM_COMPANY => array(
				// "human_resources",
				// "contacts_search_results",
				// "prof_search_results",
				// "personal_offers_table",
				// "personal_candidates_table",
				"my_customers_table" => array(
					"fields_class" => "applications/crm/crm_company_cust_impl",
					"fields_method" => "_org_table_header"
				),
				"customer_t" => array(
					"fields_class" => "applications/crm/crm_company_cust_impl",
					"fields_method" => "_org_table_header"
				),
				// "projects_listing_table",
				// "my_projects",
				"impl_projects" => array(
					"fields_class" => "applications/crm/crm_company_cust_impl",
					"fields_method" => "get_impl_projects_header"
				),
				// "report_list",
				"docs_tbl" => array(
					"fields_class" => "applications/crm/crm_company_docs_impl",
					"fields_method" => "_init_docs_tbl"
				),
				// "dn_res",
				// "documents_lmod",
				// "bill_proj_list",
				// "bill_task_list",
				// "bills_list",
				// "my_tasks",
				// "stats_s_res",
				// "stats_list",
				// "qv_t",
			),
			CL_CRM_PERSON => array(),
		);

		$this->crmcfg_class_index = array(
			"work" => CL_CRM_COMPANY,
			"s" => CL_CRM_COMPANY,
			"s_p" => CL_CRM_PERSON,
			"coworker" => CL_CRM_PERSON,
			"customer_employer" => CL_CRM_PERSON
		);

		$this->bills_filter_options = array(
			"" => "",
			0 => t("Jah"),
			1 => t("Ei"),
			2 => t("Arvel"),
			3 => t("Arveta"),
		);
	}

	function get_property(&$arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "default_tasks_view":
				$prop["options"] = array(
					0 => t("Tabelvaade"),
					1 => t("Kalendervaade"),
				);
				break;

			case "status_limits":
				$this->_get_status_limits_table($arr);
				break;
			case "default_task_rows_bills_filter":
				$prop["options"] = $this->bills_filter_options;
				break;
			case "default_my_company_tab":
			case "default_client_company_tab":
				$prop["options"] = $this->get_company_tabs();
				break;
			case "bill_mail_legend":
				$prop["value"] = nl2br(crm_bill_obj::get_mail_parse_legend());
				break;
			case "bill_default_unit":
				$filter = array(
					"class_id" => CL_UNIT
				);
				$prop["options"] = array("" => "");
				$t = new object_data_list(
					$filter,
					array(
						CL_UNIT => array(
							new obj_sql_func(OBJ_SQL_UNIQUE, "name", "objects.name"),
						)
					)
				);
				$names = $t->get_element_from_all("name");
				foreach($names as $name)
				{
					$ol = new object_list(array(
						"class_id" => CL_UNIT,
						"name" => $name
					));

					if ($ol->count())
					{
						$o = $ol->begin();
						$prop["options"][$o->id()] = $o->name();
					}
				}
				break;
		}
		return $retval;
	}

	function get_company_tabs()
	{
		$o = new object();
		$o->set_class_id(CL_CRM_COMPANY);
		$ret = array();
		foreach($o->get_group_list() as $key => $val)
		{
			$ret[$key] = $val["caption"];
		}
		return $ret;
	}

	function _get_status_limits_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Staatus"),
			"align" => "center",
		));

		$limit_types = array(
			"no_sell_wo_cash" => "Sularahata m&uuml;&uuml;k keelatud",
			"no_sell" => "M&uuml;&uuml;k keelatud",
			"no_offers" => "Pakkumised keelatud",
			"no_mission" => "L&auml;hetamine keelatud",
		);
		foreach($limit_types as $key => $val)
		{
			$t->define_field(array(
				"name" => $key,
				"caption" => $val,
				"align" => "center",
			));
		}

		$t->define_field(array(
			"name" => "ignore_groups",
			"caption" => t("Grupid kellele piirang ei m&otilde;ju"),
			"align" => "center",
		));

		$ol = new object_list(array(
			"class_id" => CL_CRM_COMPANY_STATUS
		));
		$gl = new object_list(array(
			"class_id" => CL_GROUP
		));
		$groups = $gl->names();

		$limits = $arr["obj_inst"]->meta("limits");
		foreach($ol->arr() as $o)
		{
			$data = array(
				"name" => $o->name(),
			);
			foreach($limit_types as $key => $val)
			{
				$data[$key] = html::checkbox(array(
					"name" => "limits[".$o->id()."][".$key."]",
					"value" => 1,
					"checked" => $limits[$o->id()][$key],
				));
			}
			$data["ignore_groups"] = html::select(array(
				"name" => "limits[".$o->id()."][ignore_groups]",
				"options" => $groups,
				"multiple" => 1,
				"value" => $limits[$o->id()]["ignore_groups"],
				"size" => 5,
//				"checked" => $limits[$o->id()][$key],
			));//"Grupp 1, Grupp2";
			$t->define_data($data);
		}
/*TODO: - Kliendibaasi seadetesse vaade: Staatuste piirangud
See v6imaldab vastavusse viia mitmesuguseid piiranguid mingi kliendi staatusega (crm_company_status).
Nt staatus Halb maksja seotakse piiranguga: Sularahata myyk keelatud. Selliseid piiranguid v6ib olla veel (myyk keelatud, pakkumised keelatud, l2hetamine keelatud) ja lisaks saab m22rata kasutajagruppe, kellel on 6igus sellest piirangust yle minna, ehk defineerida erandid.
*/
	}

	function check_limits($arr)
	{
		extract($arr);
		if(!$this->can("view" , $settings))
		{
			return null;
		}
		$settings = obj($settings);
		$limits = $settings->meta("limits");
		$obj_limits = $limits[$id];
		if(!is_array($obj_limits) || !($obj_limits["limit"]))
		{
			return null;
		}

		$ignore_groups = $obj_limits["ignore_groups"];
		//nyyd tarvis veel kontroll, kas kasutaja kuskil antud grupis
		$gl = aw_global_get("gidlist_pri_oid");
		asort($gl);
		$gl = array_keys($gl);
		foreach($ignore_groups as $group)
		{
			if(in_array($group , $gl))
			{
				return null;
			}
		}
		return true;
	}

	function _get_tables_toolbar($arr = array ())
	{
		$this_object = $arr["obj_inst"];
		$toolbar = $arr["prop"]["toolbar"];

		### save button
		$toolbar->add_button(array(
			"name" => "save",
			"img" => "save.gif",
			"tooltip" => t("Salvesta tabeli seaded"),
			"action" => "submit",
		));

		### load defaults button
		list($usecase, $cfg_table_name) = explode("-", $arr["request"]["tables_treemenu_item"], 2);
		$class_id = $this->crmcfg_class_index[$usecase];

		if (is_array($this->crmcfg_defined_tables[$class_id][$cfg_table_name]))
		{
			if (is_array($this_object->meta($usecase . "-" . $cfg_table_name)))
			{
				$load_defaults_url = $this->mk_my_orb("delete_table_cfg", array(
					"id" => $this_object->id (),
					"table" => $usecase . "-" . $cfg_table_name,
					"return_url" => get_ru(),
				));

				$toolbar->add_button(array(
					"name" => "defaults",
					"img" => "delete.gif",
					"tooltip" => t("Taasta algseaded"),
					"confirm" => t("Muudetud seaded kaovad kui algseaded taastada, kas j&auml;tkata?"),
					"url" => $load_defaults_url,
				));
			}
		}
	}

	function _get_tables_treemenu($arr)
	{
		$tree = $arr["prop"]["vcl_inst"];

		$tree->add_item(0, array(
			"id" => "work",
			"name" => t("Minu t&ouml;&ouml;koht"),
			"url" => "",
		));
		$this->_add_tables($tree, CL_CRM_COMPANY, "work");

		$tree->add_item(0, array(
			"id" => "s",
			"name" => t("Klient"),
			"url" => "",
		));
		$this->_add_tables($tree, CL_CRM_COMPANY, "s");

		$tree->add_item(0, array(
			"id" => "s_p",
			"name" => t("Eraisikust klient"),
			"url" => "",
		));
		$this->_add_tables($tree, CL_CRM_PERSON, "s_p");

		$tree->add_item(0, array(
			"id" => "coworker",
			"name" => t("T&ouml;&ouml;kaaslane"),
			"url" => "",
		));
		$this->_add_tables($tree, CL_CRM_PERSON, "coworker");

		$tree->add_item(0, array(
			"id" => "customer_employer",
			"name" => t("Kliendi t&ouml;&ouml;taja"),
			"url" => "",
		));
		$this->_add_tables($tree, CL_CRM_PERSON, "customer_employer");

		$tree->set_selected_item($arr["request"]["tables_treemenu_item"]);
	}

	function _add_tables($tree, $cl_id, $parent)
	{
		$properties_var = "_properties" . $cl_id;

		if (!is_object ($this->cl_cfgu))
		{
			$this->cl_cfgu = new cfgutils();
		}

		if (!isset($this->$properties_var))
		{
			$this->$properties_var = $this->cl_cfgu->load_properties(array ("clid" => $cl_id));
		}

		foreach ($this->$properties_var as $name => $property)
		{
			if (("table" == $property["type"]) and array_key_exists($name, $this->crmcfg_defined_tables[$cl_id]))
			{
				$id = $parent . "-" . $name;
				$tree->add_item($parent, array(
					"id" => $id,
					"name" => $name,
					"url" => aw_url_change_var("tables_treemenu_item", $id),
				));
			}
		}
	}

	function _get_table_cfg($arr)
	{
		list($usecase, $cfg_table_name) = explode("-", $arr["request"]["tables_treemenu_item"], 2);
		$class_id = $this->crmcfg_class_index[$usecase];

		if (!is_array($this->crmcfg_defined_tables[$class_id][$cfg_table_name]))
		{
			return PROP_IGNORE;
		}


		$table = $arr["prop"]["vcl_inst"];
		$table->define_field(array(
			"name" => "name",
			"caption" => t("Veeru nimi"),
		));
		$table->define_field(array(
			"name" => "order",
			"caption" => t("J&auml;rjekord"),
		));
		$table->define_field(array(
			"name" => "visible",
			"caption" => t("Kasutusel"),
		));
		$table->define_field(array(
			"name" => "caption",
			"caption" => t("Pealkiri"),
		));
		$table->set_numeric_field ("order_nr");
		$table->set_default_sortby ("order_nr");
		$table->set_default_sorder ("asc");

		# get default fields
		$i = get_instance($this->crmcfg_defined_tables[$class_id][$cfg_table_name]["fields_class"]);
		$function = $this->crmcfg_defined_tables[$class_id][$cfg_table_name]["fields_method"];

		$default_cfg = new vcl_table(array(
			"layout" => "generic"
		));
		$i->$function($default_cfg);

		# get saved cfg
		$this_object = $arr["obj_inst"];
		$table_cfg = $this_object->meta($usecase . "-" . $cfg_table_name);
		$cfg_not_defined = is_array($table_cfg) ? false : true;

		# ...
		foreach ($default_cfg->rowdefs as $key => $rd)
		{
			$table->define_data(array(
				"name" => $rd["name"],
				"order_nr" => $cfg_not_defined ? $key : $table_cfg[$rd["name"]]["definicion"]["order"],
				"order" => html::textbox(array(
					"name" => "tablecfg-order[" . $rd["name"] . "]",
					"size" => "3",
					"textsize" => "11px",
					"value" => $table_cfg[$rd["name"]]["definicion"]["order"],
					)
				) . html::hidden(array("name" => "tablecfgkey[" . $rd["name"] . "]", "value" => $rd["name"])),
				"visible" => html::checkbox (array(
					"name" => "tablecfg-visible[" . $rd["name"] . "]",
					"checked" => $cfg_not_defined ? 1 : $table_cfg[$rd["name"]]["visible"],
				)),
				"caption" => html::textbox(array(
					"name" => "tablecfg-caption[" . $rd["name"] . "]",
					"size" => "30",
					"textsize" => "11px",
					"value" => $cfg_not_defined ? $rd["caption"] : $table_cfg[$rd["name"]]["definicion"]["caption"],
					)
				),
			));
		}

		return PROP_OK;
	}

	function apply_table_cfg($this_object, $usecase, $cfg_table_name, $table)
	{
		if (!is_object($this_object))
		{
			if ($this->can("view", $this_object))
			{
				$this_object = new object($this_object);
			}
			else
			{
				return false;
			}
		}

		$class_id = $this->crmcfg_class_index[$usecase];

		if (!is_array($this->crmcfg_defined_tables[$class_id][$cfg_table_name]))
		{
			return false;
		}

		$table_cfg = $this_object->meta($usecase . "-" . $cfg_table_name);

		if (!is_array($table_cfg))
		{
			return false;
		}

		# apply cfg
		foreach ($table_cfg as $field_name => $field_cfg)
		{
			if ($field_cfg["visible"])
			{
				$table->update_field($field_cfg["definicion"]);
			}
			else
			{
				$table->remove_field($field_name);
			}
		}

		return true;
	}

	// returns array of field names configured in this settings object to be used in specified usecase, false on error.
	function get_visible_fields($this_object, $usecase, $cfg_table_name)
	{
		if (!is_object($this_object))
		{
			if ($this->can("view", $this_object))
			{
				$this_object = new object($this_object);
			}
			else
			{
				return false;
			}
		}

		$class_id = $this->crmcfg_class_index[$usecase];

		if (!is_array($this->crmcfg_defined_tables[$class_id][$cfg_table_name]))
		{
			return false;
		}

		$table_cfg = $this_object->meta($usecase . "-" . $cfg_table_name);

		if (!is_array($table_cfg))
		{
			return false;
		}

		$fields = array();

		foreach ($table_cfg as $field_name => $field_cfg)
		{
			if ($field_cfg["visible"])
			{
				$fields[] = $field_name;
			}
		}

		return $fields;
	}

	function _set_table_cfg($arr)
	{
		list($usecase, $cfg_table_name) = explode("-", $arr["request"]["tables_treemenu_item"], 2);
		$class_id = $this->crmcfg_class_index[$usecase];

		if (!is_array($this->crmcfg_defined_tables[$class_id][$cfg_table_name]))
		{
			return PROP_IGNORE;
		}

		$this_object = $arr["obj_inst"];
		$table_cfg = array();

		foreach (safe_array ($arr["request"]["tablecfgkey"]) as $field_name)
		{
			$table_cfg[$field_name]["definicion"]["name"] = $field_name;
			$table_cfg[$field_name]["definicion"]["caption"] = trim($arr["request"]["tablecfg-caption"][$field_name]);

			if (strlen(trim($arr["request"]["tablecfg-order"][$field_name])))
			{
				$table_cfg[$field_name]["definicion"]["order"] = (int) $arr["request"]["tablecfg-order"][$field_name];
			}

			$table_cfg[$field_name]["visible"] = (int) $arr["request"]["tablecfg-visible"][$field_name];
		}

		$this_object->set_meta($usecase . "-" . $cfg_table_name, $table_cfg);
		return PROP_OK;
	}

	function _set_status_limits($arr)
	{
		$arr["obj_inst"]->set_meta("limits" , $arr["request"]["limits"]);
	}

	function callback_mod_retval(&$arr)
	{
		if (isset($arr["request"]["tables_treemenu_item"]))
		{
			$arr["args"]["tables_treemenu_item"] = $arr["request"]["tables_treemenu_item"];
		}
	}

	function callback_mod_reforb(&$arr, $request)
	{
		$arr["post_ru"] = post_ru();

		if (isset($request["tables_treemenu_item"]))
		{
			$arr["tables_treemenu_item"] = $request["tables_treemenu_item"];
		}
	}

	function get_project_controller($settings)
	{
		if ($settings == null)
		{
			return false;
		}
		return $settings->prop("controller_proj");
	}

	function get_task_controller($settings)
	{
		if ($settings == null)
		{
			return false;
		}
		return $settings->prop("controller_task");
	}

	function get_meeting_controller($settings)
	{
		if ($settings == null)
		{
			return false;
		}
		return $settings->prop("controller_meeting");
	}

	// DEPRECATED. use crm_settings_obj::get_active_instance() instead
	function get_current_settings() { return crm_settings_obj::get_active_instance(); }

/*{ PUBLIC METHODS */

/**
    @attrib name=delete_table_cfg
	@param id required type=int
	@param table required
	@param return_url optional
**/
	function delete_table_cfg ($arr)
	{
		if ($this->can("view", $arr["id"]))
		{
			list($usecase, $cfg_table_name) = explode("-", $arr["table"], 2);
			$class_id = $this->crmcfg_class_index[$usecase];

			if (is_array($this->crmcfg_defined_tables[$class_id][$cfg_table_name]))
			{
				$this_object = new object($arr["id"]);
				$this_object->set_meta($arr["table"], NULL);
				$this_object->save();
			}
		}

		return $arr["return_url"];
	}

/*} END PUBLIC METHODS */
}
