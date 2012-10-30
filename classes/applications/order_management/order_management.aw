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

@groupinfo orders caption="Tellimused" submit_method=get submit=no save=no
@default group=orders

	@property orders_toolbar type=toolbar store=no no_caption=1

	@layout orders_split type=hbox width=25%75%

		@layout orders_left type=vbox parent=orders_split
		
			@layout orders_filter type=vbox parent=orders_left area_caption=Tellimuste&nbsp;filter
			
				@layout orders_filter_sales_channel type=vbox_sub parent=orders_filter area_caption=M&uuml;&uuml;gikanal closeable=1
			
					@property orders_filter_sales_channel type=yui-chooser multiple=true store=no no_caption=true parent=orders_filter_sales_channel
			
				@layout orders_filter_time_period type=vbox_sub parent=orders_filter area_caption=Periood closeable=1
				
					@property orders_filter_time_period type=yui-chooser orient=vertical store=no no_caption=true parent=orders_filter_time_period
			
				@layout orders_filter_customer_category type=vbox_sub parent=orders_filter area_caption=Kliendikategooria closeable=1
				
					@property orders_filter_customer_category type=yui-chooser multiple=true store=no no_caption=true parent=orders_filter_customer_category
					
				@layout orders_filter_search type=vbox_sub parent=orders_filter area_caption=Tellimuste&nbsp;otsing closeable=1
				
					@layout orders_filter_search_fields type=vbox parent=orders_filter_search
				
						@property orders_filter_search_customer_name type=textbox store=no captionside=top parent=orders_filter_search_fields
						@caption Kliendi nimi
					
						@property orders_filter_search_name type=textbox store=no captionside=top parent=orders_filter_search_fields
						@caption Tellimuse nimi
					
						@property orders_filter_search_order_number type=textbox store=no captionside=top parent=orders_filter_search_fields
						@caption Tellimuse number
					
						@property orders_filter_search_date_from type=datepicker store=no captionside=top parent=orders_filter_search_fields
						@caption Tellimus esitatud alates
					
						@property orders_filter_search_date_to type=datepicker store=no captionside=top parent=orders_filter_search_fields
						@caption Tellimus esitatud kuni
					
					@layout orders_filter_search_buttons type=hbox width=50%:50% parent=orders_filter_search
				
						@property orders_filter_search_submit type=button store=no no_caption=1 class=yui3-button-selected parent=orders_filter_search_buttons
						@caption Otsi tellimusi
					
						@property orders_filter_search_reset type=button store=no no_caption=1 class=yui3-button parent=orders_filter_search_buttons
						@caption T&uuml;hista
		
		@layout orders_right type=vbox parent=orders_split
	
			@property orders_table type=table store=no no_caption=1 parent=orders_right
	
@groupinfo configuration caption=Seaded

	@groupinfo configuration_filter parent=configuration caption=Filter
	@default group=configuration_filter
	
		@property configuration_orders_filter_customer_category type=table store=no no_caption=true

	@groupinfo configuration_table parent=configuration caption=Tabel
	@default group=configuration_table
	
		@property configuration_orders_table type=table store=no no_caption=true

*/

class order_management extends management_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/order_management/order_management",
			"clid" => order_management_obj::CLID
		));
	}
	
	function _get_mrp_workspace()
	{
		return PROP_IGNORE;
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
	
	function _get_configuration_orders_filter_customer_category($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		
		$t->set_caption(t("Tellimuste filtris kuvatavad kliendikategooriad"));
		
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
	
	function _get_orders_filter_sales_channel($arr)
	{
		$prop = &$arr["prop"];
		// TODO: Make these configurable!
		$prop["options"] = array(
			"Veeb",
			"E-post",
			"Telefon",
			"Otsem&uuml;&uuml;k"
		);
		$prop["value"] = isset($arr["request"][$prop["name"]]) ? $arr["request"][$prop["name"]] : $prop["options"];
		
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
	
	function _get_orders_filter_time_period($arr)
	{
		$prop = &$arr["prop"];
		// TODO: Make these configurable!
		$prop["options"] = array(
			1 => "T&auml;na",
			2 => "Eile",
			3 => "K&auml;esolev n&auml;dal",
			4 => "M&ouml;&ouml;dunud n&auml;dal",
			5 => "K&auml;esolev kuu",
			6 => "M&ouml;&ouml;dunud kuu",
			7 => "K&auml;esolev aasta",
			8 => "M&ouml;&ouml;dunud aasta",
		);
		$prop["value"] = isset($arr["request"][$prop["name"]]) ? $arr["request"][$prop["name"]] : null;

		$prop["onclick"] = "AW.UI.order_management.update_date_filter(); AW.UI.order_management.refresh_orders();";
		
		return PROP_OK;
	}
	
	function _get_orders_filter_search_submit($arr)
	{
		$this->set_filter_onchange_action($arr["prop"]);
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
		
		$orders = $arr["obj_inst"]->get_orders($filter);
	
		// TODO: Pagination
		
		foreach ($orders->arr() as $order)
		{
			$customer_relation = $order->customer()->find_customer_relation($arr["obj_inst"]->owner());
			$t->define_data(array(
				"name" => html::obj_change_url($order),
				"customer_name" => html::obj_change_url($order->customer(), $order->customer()->get_title()),
				"customer_relation" => html::obj_change_url($customer_relation, $customer_relation->id()),
				"date" => date("d/m/Y H:i", $order->created)
			));
		}
		
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
