<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_order_management master_index=brother_of master_table=objects index=aw_oid
@extends common/management_base/management_base

@default table=aw_order_management
@default group=general

	@property mrp_workspace type=objpicker clid=CL_MRP_WORKSPACE field=aw_mrp_workspace
	@caption Ressursihalduskeskkond

	@property owner type=objpicker clid=CL_CRM_COMPANY field=aw_owner
	@caption Omanikorganisatsioon
	
@default table=objects field=meta method=serialize

	@property price_components_folder type=relpicker reltype=RELTYPE_FOLDER clid=CL_MENU
	@comment Kaust kuhu salvestatakse ning kust loetakse selle tellimuste halduse hinnakomponentide objekte
	@caption Hinnakomponentide kaust

	@property price_component_categories_folder type=relpicker reltype=RELTYPE_FOLDER clid=CL_MENU
	@comment Kaust kust loetakse selle tellimuste halduse hinnakomponendi kategooriate objekte
	@caption Hinnakomponendi kategooriate kaust
	
	@property email_templates_folder type=relpicker reltype=RELTYPE_FOLDER clid=CL_MENU
	@comment Kaust kust loetakse selle tellimuste halduse e-kirja &scaron;abloonide objekte
	@caption E-kirja &scaron;abloonide kaust
	
	@property default_email_from type=textbox
	@caption Vaikimisi e-kirja saatja aadress
	
	@property default_email_from_name type=textbox
	@caption Vaikimisi e-kirja saatja nimi

@groupinfo orders caption="Tellimused" submit_method=get submit=no save=no
@default group=orders

	@property orders_toolbar type=toolbar store=no no_caption=1

	@layout orders_split type=hbox width=26%74%

		@layout orders_left type=vbox parent=orders_split
		
			@layout orders_filter type=vbox parent=orders_left area_caption=Tellimuste&nbsp;filter
			
				@layout orders_filter_order_sources type=vbox_sub parent=orders_filter area_caption=M&uuml;&uuml;gikanal closeable=1
			
					@property orders_filter_order_sources type=yui-chooser multiple=true store=no no_caption=true parent=orders_filter_order_sources
			
				@layout orders_filter_order_state type=vbox_sub parent=orders_filter area_caption=M&uuml;&uuml;gi&nbsp;staatus closeable=1
				
					@property orders_filter_order_state type=yui-chooser multiple=true store=no no_caption=true parent=orders_filter_order_state
			
				@layout orders_filter_state type=vbox_sub parent=orders_filter area_caption=Tootmise&nbsp;staatus closeable=1
				
					@property orders_filter_state type=yui-chooser multiple=true store=no no_caption=true parent=orders_filter_state
			
				@layout orders_filter_time_period type=vbox_sub parent=orders_filter area_caption=Periood closeable=1
				
					@property orders_filter_time_period type=period_filter store=no parent=orders_filter_time_period
			
				@layout orders_filter_customer_category type=vbox_sub parent=orders_filter area_caption=Kliendikategooria closeable=1
				
					@property orders_filter_customer_category type=yui-chooser multiple=true store=no no_caption=true parent=orders_filter_customer_category
		
		@layout orders_right type=vbox parent=orders_split
					
			@layout orders_filter_search type=vbox parent=orders_right area_caption=Tellimuste&nbsp;otsing closeable=1
				
				@layout orders_filter_search_fields_1 type=hbox width=20%:20%:20%:20%:20% parent=orders_filter_search
		
					@property orders_filter_search_customer_name size=30 type=textbox store=no captionside=top parent=orders_filter_search_fields_1
					@caption Kliendi nimi
				
					@property orders_filter_search_name size=30 type=textbox store=no captionside=top parent=orders_filter_search_fields_1
					@caption Tellimuse nimi
				
					@property orders_filter_search_order_number size=15 type=textbox store=no captionside=top parent=orders_filter_search_fields_1
					@caption Tellimuse number
				
					@property orders_filter_search_date_from time=0 type=datepicker store=no captionside=top parent=orders_filter_search_fields_1
					@caption Esitatud alates
				
					@property orders_filter_search_date_to time=0 type=datepicker store=no captionside=top parent=orders_filter_search_fields_1
					@caption Esitatud kuni
				
				@layout orders_filter_search_buttons type=hbox width=90%:5%:5% parent=orders_filter_search
				
					@property orders_filter_search_dummy type=hidden store=no no_caption=1 parent=orders_filter_search_buttons
			
					@property orders_filter_search_submit type=button store=no no_caption=1 class=yui3-button parent=orders_filter_search_buttons
					@caption Otsi tellimusi
				
					@property orders_filter_search_reset type=button store=no no_caption=1 class=yui3-button parent=orders_filter_search_buttons
					@caption T&uuml;hista
	
				@property orders_table type=table store=no no_caption=1 parent=orders_right
	
@groupinfo configuration caption=Seaded

	@groupinfo configuration_filter parent=configuration caption=Filter
	@default group=configuration_filter
	
		@layout configuration_filter_split type=hbox width=50%:50%

			@layout configuration_filter_left type=vbox parent=configuration_filter_split
			
				@layout configuration_filter_order_sources type=vbox parent=configuration_filter_left area_caption=Tellimuste&nbsp;filtris&nbsp;kuvatavad&nbsp;kanalid closeable=1
			
					@property configuration_orders_sources_toolbar type=toolbar store=no no_caption=true parent=configuration_filter_order_sources
		
					@property configuration_orders_filter_order_sources type=table store=no no_caption=true parent=configuration_filter_order_sources
			
				@layout configuration_filter_states type=vbox parent=configuration_filter_left area_caption=Tellimuste&nbsp;filtris&nbsp;valitud&nbsp;staatused closeable=1
				
					@property configuration_orders_filter_order_state type=yui-chooser multiple=1 captionside=top parent=configuration_filter_states
					@caption M&uuml;&uuml;gi staatus
				
					@property configuration_orders_filter_state type=yui-chooser multiple=1 captionside=top parent=configuration_filter_states
					@caption Tootmise staatus
			
				@layout configuration_filter_time_period type=vbox_sub parent=configuration_filter_left area_caption=Tellimuste&nbsp;filtris&nbsp;valitud&nbsp;periood closeable=1
				
					@property configuration_orders_filter_time_period type=period_filter parent=configuration_filter_time_period
			
			@layout configuration_filter_right type=vbox parent=configuration_filter_split area_caption=Tellimuste&nbsp;filtris&nbsp;kuvatavad&nbsp;kliendikategooriad closeable=1
	
				@property configuration_orders_filter_customer_category type=table store=no no_caption=true parent=configuration_filter_right

	@groupinfo configuration_table parent=configuration caption=Tabel
	@default group=configuration_table
	
		@property configuration_orders_table type=table store=no no_caption=true
		
	@groupinfo configuration_price_components caption="Hinnakomponendid" parent=configuration
	@default group=configuration_price_components

		@property price_components_toolbar type=toolbar store=no no_caption=1
	
		@layout price_components_vsplitbox type=hbox width=25%:75%
	
			@layout price_components_left type=vbox parent=price_components_vsplitbox
	
				@layout price_component_categories_tree type=vbox parent=price_components_left area_caption=Hinnakomponentide&nbsp;kategooriad
	
					@property price_component_categories_tree type=treeview parent=price_component_categories_tree
	
				@layout price_components_settings type=vbox parent=price_components_left area_caption=Hinnakomponentide&nbsp;seaded
	
					@property hide_mandatory_price_components type=checkbox parent=price_components_settings no_caption=1
					@caption Peida kohustuslikud hinnakomponendid
	
			@layout price_components_right type=vbox parent=price_components_vsplitbox
	
				@property price_component_categories_table type=table store=no no_caption=1 parent=price_components_right
	
				@property price_components_table type=table store=no no_caption=1 parent=price_components_right
				
@reltype FOLDER value=1 clid=CL_MENU
@caption Kaust

*/

class order_management extends management_base
{
	const PRICE_COMPONENTS_ALL = "all";
	
	private static $not_available_string = "NA";
	private $application;
	private $price_components_list_view = self::PRICE_COMPONENTS_ALL;
	
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/order_management/order_management",
			"clid" => order_management_obj::CLID
		));
	}
	
	function callback_on_load($arr)
	{
		$this->application = obj($arr["request"]["id"], null, order_management_obj::CLID);
		
		if ("configuration_price_components" === $this->use_group)
		{
			$this->price_components_list_view = automatweb::$request->arg_isset("category") ? automatweb::$request->arg("category") : self::PRICE_COMPONENTS_ALL;
		}
	}
	
	function _get_mrp_workspace()
	{
		return PROP_IGNORE;
	}
	
	function _get_configuration_orders_filter_order_state(&$arr)
	{
		$prop = &$arr["prop"];
		$prop["options"] = mrp_case_obj::get_order_state_names();
		
		return PROP_OK;
	}
	
	function _get_configuration_orders_filter_state(&$arr)
	{
		$prop = &$arr["prop"];
		$prop["options"] = mrp_case_obj::get_state_names();
		
		return PROP_OK;
	}

	function _get_configuration_orders_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->set_caption(t("Tellimuste tabelis kuvatavad veerud"));

		$t->add_fields(array(
			"ord" => t("J&auml;rjekord"),
			"caption" => t("Veeru pealkiri"),
			"active" => t("Aktiivne"),
		));

		foreach ($arr["obj_inst"]->get_orders_table_fields() as $field)
		{
			$t->define_data(array(
				"ord" => html::textbox(array(
					"name" => "configuration_orders_table[{$field["name"]}][ord]",
					"value" => $field["ord"],
				)),
				"caption" => html::textbox(array(
					"name" => "configuration_orders_table[{$field["name"]}][caption]",
					"value" => $field["caption"],
				)).sprintf(t(" (%s)"), $field["original_caption"]),
				"active" => html::hidden(array(
					"name" => "configuration_orders_table[{$field["name"]}][active]",
					"value" => 0,
				)).html::checkbox(array(
					"name" => "configuration_orders_table[{$field["name"]}][active]",
					"checked" => !empty($field["active"]),
				))
			));
		}
	}
	
	function _get_configuration_orders_sources_toolbar($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		
		$t->add_new_button(array(order_source_obj::CLID), $arr["obj_inst"]->id());
		$t->add_delete_button();
		
		return PROP_OK;
	}
	
	function _get_configuration_orders_filter_order_sources($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		
		$t->define_chooser();
		$t->set_default("align", "center");
		$t->add_fields(array(
			"name" => "Kanal",
			"checked_by_default" => "Vaikimisi valitud",
		));
		
		$configuration = $arr["obj_inst"]->meta("configuration_orders_filter_order_sources");
		foreach ($arr["obj_inst"]->get_order_sources()->arr() as $source)
		{
			$t->define_data(array(
				"oid" => $source->id,
				"name" => html::obj_change_url($source),
				"checked_by_default" => html::checkbox(array(
					"name" => "configuration_orders_filter_order_sources[{$source->id}][checked_by_default]",
					"checked" => !empty($configuration[$source->id]["checked_by_default"])
				)),
			));
		}
		
		return PROP_OK;
	}
	
	function _set_configuration_orders_filter_order_sources($arr)
	{
		if(automatweb::$request->arg_isset("configuration_orders_filter_order_sources"))
		{
			$arr["obj_inst"]->set_meta("configuration_orders_filter_order_sources", automatweb::$request->arg("configuration_orders_filter_order_sources"));
		}
	}
	
	function _get_configuration_orders_filter_customer_category($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		
		$t->add_fields(array(
			"name" => "Kliendikategooria",
		));
		$t->set_default("align", "center");
		$t->add_fields(array(
			"use_in_filter" => "Kuva filtris",
			"use_subcategories_in_filter" => "Kuva alamkategooriaid filtris",
			"checked_by_default" => "Vaikimisi valitud",
		));
		
		$configuration = $arr["obj_inst"]->meta("configuration_orders_filter_customer_category");
		
		$categories = $arr["obj_inst"]->get_customer_categories_hierarchy();
		$this->__configuration_orders_filter_customer_category_insert_category($t, $configuration, $categories);
		
		return PROP_OK;
	}
	
	private function __configuration_orders_filter_customer_category_insert_category ($t, $configuration, $categories, $level = 0)
	{
		foreach($categories as $id => $subcategories)
		{
			$name = obj($id)->name();
			$t->define_data(array(
				"name" => str_repeat("&nbsp; &nbsp; ", $level).$name,
				"use_in_filter" => html::checkbox(array(
					"name" => "configuration_orders_filter_customer_category[{$id}][use_in_filter]",
					"checked" => !empty($configuration[$id]["use_in_filter"])
				)),
				"use_subcategories_in_filter" => html::checkbox(array(
					"name" => "configuration_orders_filter_customer_category[{$id}][use_subcategories_in_filter]",
					"checked" => !empty($configuration[$id]["use_subcategories_in_filter"])
				)),
				"checked_by_default" => html::checkbox(array(
					"name" => "configuration_orders_filter_customer_category[{$id}][checked_by_default]",
					"checked" => !empty($configuration[$id]["checked_by_default"])
				)),
			));
			$this->__configuration_orders_filter_customer_category_insert_category($t, $configuration, $subcategories, $level + 1);
		}
	}
	
	function _set_configuration_orders_filter_customer_category($arr)
	{
		if(automatweb::$request->arg_isset("configuration_orders_filter_customer_category"))
		{
			$arr["obj_inst"]->set_meta("configuration_orders_filter_customer_category", automatweb::$request->arg("configuration_orders_filter_customer_category"));
		}
	}
	
	function _get_orders_toolbar($arr)
	{
		$owner = $arr["obj_inst"]->owner();
	
		$t = $arr["prop"]["vcl_inst"];
		
		$t->add_new_button(array(mrp_case_obj::CLID), $arr["obj_inst"]->id(), null, array("mrp_workspace" => mrp_workspace_obj::get_hr_manager($owner)->id));
		
		return PROP_OK;
	}
	
	function _get_orders_filter_order_sources($arr)
	{
		$prop = &$arr["prop"];
		// TODO: Make these configurable!
		$prop["options"] = $arr["obj_inst"]->get_order_sources()->names();
		$prop["value"] = isset($arr["request"][$prop["name"]]) ? $arr["request"][$prop["name"]] : $arr["obj_inst"]->default_filter("orders_filter_order_sources");
		
		$this->set_filter_onchange_action($prop);

		return PROP_OK;
	}
	
	function _get_orders_filter_customer_category($arr)
	{
		$customer_groups = $arr["obj_inst"]->get_customer_categories_for_filter();
	
		$prop = &$arr["prop"];
		$prop["options"] = $customer_groups->names();
		$prop["value"] = isset($arr["request"][$prop["name"]]) ? $arr["request"][$prop["name"]] : $arr["obj_inst"]->default_filter("orders_filter_customer_category");
		
		$this->set_filter_onchange_action($prop);
		
		return PROP_OK;
	}
	
	function _get_orders_filter_state($arr)
	{
		$prop = &$arr["prop"];
		$prop["options"] = mrp_case_obj::get_state_names();
		$prop["value"] = isset($arr["request"][$prop["name"]]) ? $arr["request"][$prop["name"]] : $arr["obj_inst"]->default_filter("orders_filter_state");
		
		$this->set_filter_onchange_action($prop);
		
		return PROP_OK;
	}
	
	function _get_orders_filter_order_state($arr)
	{
		$prop = &$arr["prop"];
		$prop["options"] = mrp_case_obj::get_order_state_names();
		$prop["value"] = isset($arr["request"][$prop["name"]]) ? $arr["request"][$prop["name"]] : $arr["obj_inst"]->default_filter("orders_filter_order_state");
		
		$this->set_filter_onchange_action($prop);
		
		return PROP_OK;
	}
	
	function _get_configuration_orders_filter_time_period(&$arr)
	{
		$prop = &$arr["prop"];
		$prop["options"] = array(
			"current" => array(
				order_management_obj::FILTER_DATE_CURRENT_DAY => "P&auml;ev",
				order_management_obj::FILTER_DATE_CURRENT_WEEK => "N&auml;dal",
				order_management_obj::FILTER_DATE_CURRENT_MONTH => "Kuu",
				order_management_obj::FILTER_DATE_CURRENT_QUARTER => "Kvartal",
				order_management_obj::FILTER_DATE_CURRENT_YEAR => "Aasta",
			),
			"previous" => array(
				order_management_obj::FILTER_DATE_PREVIOUS_DAY => "P&auml;ev",
				order_management_obj::FILTER_DATE_PREVIOUS_WEEK => "N&auml;dal",
				order_management_obj::FILTER_DATE_PREVIOUS_MONTH => "Kuu",
				order_management_obj::FILTER_DATE_PREVIOUS_QUARTER => "Kvartal",
				order_management_obj::FILTER_DATE_PREVIOUS_YEAR => "Aasta",
			),
		);
		
		return PROP_OK;
	}
	
	function _get_orders_filter_time_period($arr)
	{
		$prop = &$arr["prop"];
		$prop["options"] = array(
			"current" => array(
				order_management_obj::FILTER_DATE_CURRENT_DAY => "P&auml;ev",
				order_management_obj::FILTER_DATE_CURRENT_WEEK => "N&auml;dal",
				order_management_obj::FILTER_DATE_CURRENT_MONTH => "Kuu",
				order_management_obj::FILTER_DATE_CURRENT_QUARTER => "Kvartal",
				order_management_obj::FILTER_DATE_CURRENT_YEAR => "Aasta",
			),
			"previous" => array(
				order_management_obj::FILTER_DATE_PREVIOUS_DAY => "P&auml;ev",
				order_management_obj::FILTER_DATE_PREVIOUS_WEEK => "N&auml;dal",
				order_management_obj::FILTER_DATE_PREVIOUS_MONTH => "Kuu",
				order_management_obj::FILTER_DATE_PREVIOUS_QUARTER => "Kvartal",
				order_management_obj::FILTER_DATE_PREVIOUS_YEAR => "Aasta",
			),
		);
		$prop["value"] = isset($arr["request"][$prop["name"]]) ? $arr["request"][$prop["name"]] : $arr["obj_inst"]->default_filter("orders_filter_time_period");
		$prop["onclick"] = "AW.UI.order_management.update_date_filter(this); AW.UI.order_management.refresh_orders();";
		
		return PROP_OK;
	}
	
	function _get_orders_filter_search_date_from(&$arr)
	{
		$prop = &$arr["prop"];
		$prop["value"] = isset($arr["request"][$prop["name"]]) ? $arr["request"][$prop["name"]] : $arr["obj_inst"]->default_filter("orders_filter_date_from");
		if (is_array($prop["value"])) $prop["value"] = datepicker::get_timestamp($prop["value"]);
		
		return PROP_OK;
	}
	
	function _get_orders_filter_search_date_to(&$arr)
	{
		$prop = &$arr["prop"];
		$prop["value"] = isset($arr["request"][$prop["name"]]) ? $arr["request"][$prop["name"]] : $arr["obj_inst"]->default_filter("orders_filter_date_to");
		if (is_array($prop["value"])) $prop["value"] = datepicker::get_timestamp($prop["value"]);
		
		return PROP_OK;
	}
	
	function _get_orders_filter_search_submit($arr)
	{
		$arr["prop"]["class"] = "yui3-button yui3-button-selected";
		$this->set_filter_onchange_action($arr["prop"]);
		return PROP_OK;
	}
	
	function _get_orders_filter_search_reset($arr)
	{
		$arr["prop"]["onclick"] = "AW.UI.order_management.reset_search(); AW.UI.order_management.refresh_orders();";
		return PROP_OK;
	}
	
	private function set_filter_onchange_action(&$prop)
	{
		$prop["onclick"] = "AW.UI.order_management.refresh_orders();";
	}
	
	function _get_orders_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		
		foreach ($arr["obj_inst"]->get_orders_table_fields() as $field)
		{
			if (!empty($field["active"]))
			{
				$t->define_field($field);
			}
		}
		
		// TODO: Refactor into a separate method!
		$filter = array();
		if (automatweb::$request->arg_isset("orders_filter_customer_category"))
		{
			$filter["customer_category"] = automatweb::$request->arg("orders_filter_customer_category");
		}
		if (automatweb::$request->arg_isset("orders_filter_search_customer_name"))
		{
			$filter["customer_name"] = automatweb::$request->arg("orders_filter_search_customer_name");
		}
		if (automatweb::$request->arg_isset("orders_filter_search_name"))
		{
			$filter["name"] = automatweb::$request->arg("orders_filter_search_name");
		}
		if (automatweb::$request->arg_isset("orders_filter_search_date_from"))
		{
			$filter["date_from"] = automatweb::$request->arg("orders_filter_search_date_from");
		}
		if (automatweb::$request->arg_isset("orders_filter_search_date_to"))
		{
			$filter["date_to"] = automatweb::$request->arg("orders_filter_search_date_to");
		}
		if (automatweb::$request->arg_isset("orders_filter_order_sources"))
		{
			$filter["order_sources"] = automatweb::$request->arg("orders_filter_order_sources");
		}
		if (automatweb::$request->arg_isset("orders_filter_state"))
		{
			$filter["state"] = automatweb::$request->arg("orders_filter_state");
		}
		if (automatweb::$request->arg_isset("orders_filter_order_state"))
		{
			$filter["order_state"] = automatweb::$request->arg("orders_filter_order_state");
		}
		
		$orders = $arr["obj_inst"]->get_orders($filter);
	
		// TODO: Pagination
		
		foreach ($orders->arr() as $order)
		{
			$customer = $order->customer();
			$customer_relation = $customer->is_saved() ? $customer->find_customer_relation($arr["obj_inst"]->owner()) : null;
			$order_inst = new mrp_case();
			$t->define_data(array(
				"name" => html::obj_change_url($order),
				"customer_name" => $customer->is_saved() ? html::obj_change_url($customer, ($customer->is_a(crm_company_obj::CLID) ? $customer->get_title() : $customer->name())) : self::$not_available_string,
				"customer_manager" => $customer_relation !== null && is_oid($customer_relation->client_manager) ? html::obj_change_url($customer_relation->client_manager()) : self::$not_available_string,
				"customer_relation" => $customer_relation !== null ? html::obj_change_url($customer_relation, $customer_relation->id()) : self::$not_available_string,
				"order_state" => mrp_case_obj::get_order_state_names($order->order_state),
				"state" => mrp_case_obj::get_state_names($order->state),
				"date" => date("d/m/Y H:i", $order->created)
			));
		}
		
		return PROP_OK;
	}
	public function _get_hide_mandatory_price_components(&$arr)
	{
		$arr["prop"]["label"] = t("Peida kohustuslikud hinnakomponendid");
		return PROP_OK;
	}

	protected function define_price_components_tbl_header(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->set_caption(t("Pakkumustes kasutatavad hinnakomponendid"));

		$t->define_chooser();
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => true
		));
		$t->define_field(array(
			"name" => "type",
			"caption" => t("T&uuml;&uuml;p"),
			"align" => "center",
			"callback" => array($this, "callback_price_components_table_type"),
			"callb_pass_row" => true,
			"sortable" => true
		));
		$t->define_field(array(
			"name" => "value",
			"caption" => t("Summa v&otilde;i protsent"),
			"align" => "center",
			"callback" => array($this, "callback_price_components_table_value"),
			"callb_pass_row" => true,
			"sortable" => true
		));
		$t->define_field(array(
			"name" => "category",
			"caption" => t("Kategooria"),
			"align" => "center",
			"callback" => array($this, "callback_price_components_table_category"),
			"callb_pass_row" => true,
			"sortable" => true
		));
	}

	public function _get_price_components_table(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$price_component_inst = new price_component();
		$price_component_types = $price_component_inst->type_options;
		$not_available_str = html::italic(t("M&auml;&auml;ramata"));

		$this->define_price_components_tbl_header($arr);

		$price_component_predicates = array();
		if(automatweb::$request->arg_isset("category"))
		{
			$category = automatweb::$request->arg("category");
			// $category is either 'undefined' or 'pc_{$category_oid}'
			$category = "undefined" === $category ? "undefined" : preg_replace("/[^0-9]/", "", $category);
			$price_component_predicates = array("category" => $category);
		}
		$price_components = $this->application->get_price_component_list($price_component_predicates);

		$categories = $this->application->get_price_component_category_list()->names();
		foreach($price_components->arr() as $price_component)
		{
			$t->define_data(array(
				"oid" => $price_component->id(),
				"name" => html::obj_change_url($price_component),
				"type" => $price_component->prop("type"),
				"value" => $price_component->prop_str("value"),
				"category" => $price_component->prop("category"),
			));
		}

		return PROP_OK;
	}

	public function callback_price_components_table_type($row)
	{
		static $types;
		
		if(!isset($types))
		{
			$price_component_inst = new price_component();
			$types = $price_component_inst->type_options;
		}

		return html::select(array(
			"name" => "price_components[{$row["oid"]}][type]",
			"options" => $types,
			"value" => $row["type"],
		));
	}

	public function callback_price_components_table_value($row)
	{
		return html::textbox(array(
			"name" => "price_components[{$row["oid"]}][value]",
			"size" => 10,
			"value" => $row["value"],
		));
	}

	public function callback_price_components_table_category($row)
	{
		static $options;
		
		if(!isset($options))
		{
			$options = array(t("--Vali--")) + $this->application->get_price_component_category_list()->names();
		}

		return html::select(array(
			"name" => "price_components[{$row["oid"]}][category]",
			"options" => $options,
			"value" => $row["category"],
		));
	}

	public function _set_price_components_table(&$arr)
	{
		$price_components = automatweb::$request->arg("price_components");
		if(!empty($price_components) && is_array($price_components))
		{
			foreach($price_components as $price_component_id => $price_component_data)
			{
				$price_component = new object($price_component_id, array(), price_component_obj::CLID);
				$price_component->set_prop("type", $price_component_data["type"]);
				$price_component->set_prop("value", $price_component_data["value"]);
				$price_component->set_prop("category", $price_component_data["category"]);
				$price_component->save();
			}
		}
	}

	protected function define_price_component_categories_tbl_header(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->set_caption(t("Hinnakomponentide kategooriad"));

		$t->define_chooser();
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => true
		));
		$t->define_field(array(
			"name" => "edit",
			"caption" => "",
			"align" => "center",
			"callback" => array($this, "callback_price_component_categories_table_edit"),
			"callb_pass_row" => true
		));
	}

	public function callback_price_component_categories_table_edit($row)
	{
		$menu = new popup_menu();
		$menu->begin_menu("edit_{$row["oid"]}");
		$menu->add_item(array(
				"text" => t("Muuda"),
				"link" => html::get_change_url($row["oid"]),
		));

		return $menu->get_menu();
	}

	public function _get_price_component_categories_tree(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		
		$url = automatweb::$request->get_uri();
		$url->unset_arg("category");

		$t->add_item(0, array(
			"id" => "all",
			"name" => t("K&otilde;ik hinnakomponendid"),
			"url" => $url->get(),
		));

		$categories = $this->application->get_price_component_category_list();
		foreach($categories->arr() as $category)
		{
			$key = "pc_{$category->id()}";
			$url->set_arg("category", $key);
			$t->add_item("all", array(
				"id" => $key,
				"name" => $category->name(),
				"url" => $url->get(),
			));
		}
		
		$key = "undefined";
		$url->set_arg("category", $key);
		$t->add_item("all", array(
			"id" => $key,
			"name" => html::italic(t("M&auml;&auml;ramata")),
			"url" => $url->get(),
		));

		$t->set_selected_item($this->price_components_list_view);

		return PROP_OK;
	}

	public function _get_price_component_categories_table(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$this->define_price_component_categories_tbl_header($arr);

		$categories = $this->application->get_price_component_category_list();
		$url = automatweb::$request->get_uri();
		foreach($categories->names() as $category_id => $category_name)
		{
			$url->set_arg("category", "pc_{$category_id}");
			$t->define_data(array(
				"oid" => $category_id,
				"name" => html::href(array(
					"caption" => $category_name,
					"url" => $url->get(),
				)),
			));
		}

		return PROP_OK;
	}

	public function _get_price_components_toolbar(&$arr)
	{
		$this_o = $arr["obj_inst"];
		$t = $arr["prop"]["vcl_inst"];
		$add_new_button = false;

		if ($this_o->is_saved() && is_oid($this_o->prop("price_components_folder")))
		{
			$t->add_menu_item(array(
				"parent" => "new",
				"text" => t("Hinnakomponent"),
				"link" => html::get_new_url(price_component_obj::CLID, $this_o->prop("price_components_folder"), array(
					"application" => $this_o->id(),
					"return_url" => get_ru(),
				)),
			));
			$add_new_button = true;
		}
		if ($this_o->is_saved() && is_oid($this_o->prop("price_component_categories_folder")))
		{
			$t->add_menu_item(array(
				"parent" => "new",
				"text" => t("Hinnakomponendi kategooria"),
				"link" => html::get_new_url(price_component_category_obj::CLID, $this_o->prop("price_component_categories_folder"), array(
					"application" => $this_o->id(),
					"return_url" => get_ru(),
				)),
			));
			$add_new_button = true;
		}

		if($add_new_button)
		{
			$t->add_menu_button(array(
				"name" => "new",
				"img" => "new.gif",
			));
		}
		$t->add_save_button();
		$t->add_delete_button();

		return PROP_OK;
	}

	function callback_pre_save($arr)
	{
		if (isset($arr["request"]["configuration_orders_table"]))
		{
			$arr["obj_inst"]->set_meta("configuration_orders_table", $arr["request"]["configuration_orders_table"]);
		}
	}
	
	function callback_generate_scripts()
	{
		load_javascript("reload_properties_layouts.js");
		load_javascript("applications/order_management/order_management.js");
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_order_management" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_order_management` (
					`aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
					PRIMARY KEY	(`aw_oid`)
				)");
				$r = true;
			}
			else
			{
				switch($field)
				{
					case "aw_owner":
					case "aw_mrp_workspace":
						$this->db_add_col($table, array(
							"name" => $field,
							"type" => "INT"
						));
						break;

				}
				$r = true;
			}
		}

		return $r;
	}
}
