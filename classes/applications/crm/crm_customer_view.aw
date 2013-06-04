<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_crm_customer_view master_index=brother_of master_table=objects index=aw_oid

@default table=objects method=serialize field=meta

@default group=general

	@property company type=relpicker reltype=RELTYPE_COMPANY
	@caption Ettev&otilde;te

@groupinfo relorg caption="Kliendid"

	@groupinfo customers caption="Kliendid" focus=cs_n submit=no parent=relorg
	@default group=customers
	
		@property customer_category type=hidden store=no
	
		@property my_customers_toolbar type=toolbar no_caption=1 store=no
		@caption Kliendivaate tegevused
	
		@layout my_cust_bot type=hbox width=20%:80%
			@layout tree_search_split type=vbox parent=my_cust_bot
					
				@layout customers_filter_customer_category type=vbox parent=tree_search_split closeable=1 area_caption=Kliendikategooria
	
					@property customers_filter_customer_category type=yui-chooser indented=true multiple=true store=no no_caption=true parent=customers_filter_customer_category
					
				@layout customers_filter_customer_manager type=vbox parent=tree_search_split closeable=1 area_caption=Osapooled
	
					@property customers_filter_customer_manager type=yui-chooser indented=true multiple=true store=no captionside=top parent=customers_filter_customer_manager
					@caption Kliendihaldur
	
				@layout customers_filter_state type=vbox parent=tree_search_split area_caption=Staatus closeable=1
	
					@property customers_filter_state type=yui-chooser multiple=true store=no no_caption=true parent=customers_filter_state
	
				@layout customers_tree_areas type=vbox parent=tree_search_split closeable=1 area_caption=Piirkonnad
					@property customer_areas_tree type=treeview no_caption=1 parent=customers_tree_areas
					@caption Kliendikategooriad Piirkondade puu
	
				@layout vbox_customers_left type=vbox parent=tree_search_split closeable=1 area_caption=Otsing
					@layout vbox_customers_left_top type=vbox parent=vbox_customers_left
	
						@property cs_n type=textbox size=30 store=no parent=vbox_customers_left_top captionside=top
						@caption Nimi
	
						@property customer_search_reg type=textbox size=30 store=no parent=vbox_customers_left_top captionside=top
						@caption Reg nr.
	
						@property customer_search_address type=textbox size=30 store=no parent=vbox_customers_left_top captionside=top
						@caption Aadress
	
			@property cts_comment type=textbox parent=vbox_customers_left_top store=no size=30 captionside=top
			@caption Kommentaar
	
			@property cts_phone type=textbox parent=vbox_customers_left_top store=no size=30 captionside=top
			@caption Telefon
	
			@property cts_lead_source type=textbox parent=vbox_customers_left_top store=no size=30 captionside=top
			@caption Soovitaja
	
			@property cts_contact type=textbox parent=vbox_customers_left_top store=no size=30 captionside=top
			@caption Kontaktisik
	
			@property cts_salesman type=select parent=vbox_customers_left_top store=no captionside=top
			@caption M&uuml;&uuml;giesindaja
	
			@property cts_cat type=objpicker parent=vbox_customers_left_top store=no options_callback=crm_customer_view::get_category_options captionside=top clid=CL_CRM_CATEGORY size=30
			@caption Kliendigrupp
	
			@property cts_calls type=textbox parent=vbox_customers_left_top store=no captionside=top size=30
			@comment Positiivne t&auml;isarv. V&otilde;imalik kasutada v&otilde;rdlusoperaatoreid suurem kui ( &gt; ), v&auml;iksem kui ( &lt; ) ning '='. Kui operaatorit pole numbri ees, arvatakse vaikimisi operaatoriks v&otilde;rdus ( = )
			@caption Tehtud k&otilde;nesid
	
			@layout vbox_customers_left_search_btn type=hbox parent=vbox_customers_left

				@property cs_sbt type=submit size=15 store=no parent=vbox_customers_left_search_btn no_caption=1
				@caption Otsi
	
			@layout list_container type=vbox parent=my_cust_bot
				@layout category_list_container type=vbox parent=list_container closeable=1 area_caption="Kategooriad" no_padding=1 default_state=closed
					@property customer_categories_table type=table store=no no_caption=1 parent=category_list_container
					@caption Kliendikategooriad
	
				@layout customer_list_container type=vbox parent=list_container area_caption="Kliendid" closeable=1 no_padding=1
					@property my_customers_table type=table store=no no_caption=1 parent=customer_list_container
					@caption Kliendid

	@groupinfo categories caption="Kliendikategooriad" focus=cs_n submit=no parent=relorg
	@default group=categories
	
		@property categories_toolbar type=toolbar no_caption=1 store=no
		
		@layout categories_split type=hbox width=20%:80%
		
			@layout categories_tree type=vbox parent=categories_split closeable=1 area_caption=Kliendikategooria
			
				@property categories_tree type=treeview parent=categories_tree store=no no_caption=1
			
			@property categories_table type=table parent=categories_split store=no no_caption=1

@groupinfo configuration caption=Seaded

	@groupinfo configuration_filter parent=configuration caption=Filter
	@default group=configuration_filter
	
		@layout configuration_filter_split type=hbox width=50%:50%

			@layout configuration_filter_left type=vbox parent=configuration_filter_split
			
				@layout configuration_filter_states type=vbox parent=configuration_filter_left area_caption=Filtris&nbsp;valitud&nbsp;staatused closeable=1
				
					@property configuration_customers_filter_state type=yui-chooser multiple=1 captionside=top parent=configuration_filter_states
					@caption Staatus
			
				@layout configuration_customers_filter_customer_manager type=vbox parent=configuration_filter_left area_caption=Filtris&nbsp;valitud&nbsp;osapooled closeable=1
				
					@property configuration_customers_filter_customer_manager type=yui-chooser multiple=1 captionside=top parent=configuration_customers_filter_customer_manager
					@caption Kliendihaldur
			
			@layout configuration_filter_right type=vbox parent=configuration_filter_split area_caption=Filtris&nbsp;kuvatavad&nbsp;kliendikategooriad closeable=1
	
				@property configuration_customers_filter_customer_category type=table store=no no_caption=true parent=configuration_filter_right

	@groupinfo configuration_table parent=configuration caption=Tabel
	@default group=configuration_table
	
		@property configuration_customers_table type=table store=no no_caption=true

@reltype COMPANY value=1 clid=CL_CRM_COMPANY
@caption Ettev&otilde;te


*/

class crm_customer_view extends class_base
{
	const CUTCOPIED_COLOUR = "silver";
	const REQVAR_CATEGORY = "customers_filter_customer_category"; // request parameter name for customer category
	const CUSTOMER_CATEGORY = "customer_category";
	
	private $filter_customer_category = null;
	private $filter_state = null;
	private $filter_customer_manager = null;
	
	public function callback_on_load($arr)
	{
		if ("customers" === $this->use_group)
		{
			$filters = array("state", "customer_category", "customer_manager");
			foreach ($filters as $filter)
			{
				$filter = "filter_{$filter}";
				if (automatweb::$request->arg_isset("customers_{$filter}"))
				{
					$filter_value = (array)automatweb::$request->arg("customers_{$filter}");
				}
				elseif (isset($arr["request"]["id"]) and object_loader::can("", $arr["request"]["id"]))
				{
					$view = obj($arr["request"]["id"], null, crm_customer_view_obj::CLID);
					$filter_value = safe_array($view->default_filter("customers_{$filter}"));
				}
				foreach($filter_value as $key => $value)
				{
					if ((int)$value === 0)
					{
						unset($filter_value[$key]);
					}
					else
					{
						$filter_value[$key] = (int)$value;
					}
				}
				$this->$filter = $filter_value;
			}
		}
	}

	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_customer_view",
			"clid" => crm_customer_view_obj::CLID
		));

		$this->search_props = array("cs_n","customer_search_reg","customer_search_address","customer_search_city","customer_search_county" , "cts_phone" , "cts_salesman" , "cts_calls" , "cts_lead_source" , "cts_address" , "cts_status", "cts_contact","cts_comment","cts_cat");
	}

	public function _get_configuration_customers_table(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->set_caption(t("Klientide tabelis kuvatavad veerud"));

		$t->add_fields(array(
			"ord" => t("J&auml;rjekord"),
			"caption" => t("Veeru pealkiri"),
			"active" => t("Aktiivne"),
		));

		foreach ($arr["obj_inst"]->get_customers_table_fields() as $field)
		{
			$t->define_data(array(
				"ord" => html::textbox(array(
					"name" => "configuration_customers_table[{$field["name"]}][ord]",
					"value" => $field["ord"],
				)),
				"caption" => html::textbox(array(
					"name" => "configuration_customers_table[{$field["name"]}][caption]",
					"value" => $field["caption"],
				)).sprintf(t(" (%s)"), $field["original_caption"]),
				"active" => html::hidden(array(
					"name" => "configuration_customers_table[{$field["name"]}][active]",
					"value" => 0,
				)).html::checkbox(array(
					"name" => "configuration_customers_table[{$field["name"]}][active]",
					"checked" => !empty($field["active"]),
				))
			));
		}

		return class_base::PROP_OK;
	}

	public function _set_configuration_customers_table($arr)
	{
		if (isset($arr["request"]["configuration_customers_table"]))
		{
			$arr["obj_inst"]->set_meta("configuration_customers_table", $arr["request"]["configuration_customers_table"]);
		}
		
		return class_base::PROP_OK;
	}
	
	function _get_configuration_customers_filter_customer_category($arr)
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
		
		$configuration = $arr["obj_inst"]->meta("configuration_customers_filter_customer_category");
		
		$categories = $arr["obj_inst"]->get_customer_categories_hierarchy();
		$this->__configuration_customers_filter_customer_category_insert_category($t, $configuration, $categories);
		
		return PROP_OK;
	}
	
	private function __configuration_customers_filter_customer_category_insert_category ($t, $configuration, $categories, $level = 0, $parent = 0)
	{
		foreach($categories as $id => $subcategories)
		{
			$name = obj($id)->name();
			$t->define_data(array(
				"name" => str_repeat("&nbsp; &nbsp; ", $level).(strlen(trim($name)) > 0 ? $name : html::italic(t("(nimetu)"))),
				"use_in_filter" => html::checkbox(array(
					"name" => "configuration_customers_filter_customer_category[{$id}][use_in_filter]",
					"checked" => !empty($configuration[$id]["use_in_filter"])
				)),
				"use_subcategories_in_filter" => html::hidden(array(
					"name" => "configuration_customers_filter_customer_category[{$id}][parent]",
					"value" => $parent,
				)).html::checkbox(array(
					"name" => "configuration_customers_filter_customer_category[{$id}][use_subcategories_in_filter]",
					"checked" => !empty($configuration[$id]["use_subcategories_in_filter"])
				)),
				"checked_by_default" => html::checkbox(array(
					"name" => "configuration_customers_filter_customer_category[{$id}][checked_by_default]",
					"checked" => !empty($configuration[$id]["checked_by_default"])
				)),
			));
			$this->__configuration_customers_filter_customer_category_insert_category($t, $configuration, $subcategories, $level + 1, $id);
		}
	}
	
	function _set_configuration_customers_filter_customer_category($arr)
	{
		if(automatweb::$request->arg_isset("configuration_customers_filter_customer_category"))
		{
			$arr["obj_inst"]->set_meta("configuration_customers_filter_customer_category", automatweb::$request->arg("configuration_customers_filter_customer_category"));
		}
	}
	
	function _get_configuration_customers_filter_state(&$arr)
	{
		$prop = &$arr["prop"];
		$prop["options"] = crm_company_customer_data_obj::sales_state_names();
		
		return PROP_OK;
	}
	
	function _get_configuration_customers_filter_customer_manager(&$arr)
	{
		$prop = &$arr["prop"];
		$prop["options"] = $arr["obj_inst"]->get_customer_managers()->names();
		
		return self::PROP_OK;
	}
	
	public function _get_customers_filter_customer_category(&$arr)
	{
		$customer_groups = $arr["obj_inst"]->get_customer_categories_for_filter()->names();
	
		$prop = &$arr["prop"];
		$prop["options"] = $this->__create_customer_category_filter_options($arr["obj_inst"]->get_customer_categories_hierarchy(), $customer_groups);
		$prop["value"] = isset($arr["request"][$prop["name"]]) ? $arr["request"][$prop["name"]] : $arr["obj_inst"]->default_filter("customers_filter_customer_category");

		$this->__set_filter_onchange_action($prop);

		return PROP_OK;
	}
	
	private function __create_customer_category_filter_options($hierarchy, $names)
	{
		$options = array();
		foreach($hierarchy as $id => $subhierarchy)
		{
			if (isset($names[$id]))
			{
				$options[$id] = array(
					"caption" => $names[$id],
					"suboptions" => $this->__create_customer_category_filter_options($subhierarchy, $names)
				);
			}
			else
			{
				$options += $this->__create_customer_category_filter_options($subhierarchy, $names);
			}
		}
		
		return $options;
	}
	
	function _get_customers_filter_state($arr)
	{
		$prop = &$arr["prop"];
		$prop["options"] = crm_company_customer_data_obj::sales_state_names();
		$prop["value"] = isset($arr["request"][$prop["name"]]) ? $arr["request"][$prop["name"]] : $arr["obj_inst"]->default_filter("customers_filter_state");
		
		$this->__set_filter_onchange_action($prop);
		
		return PROP_OK;
	}
	
	function _get_customers_filter_customer_manager($arr)
	{
		$prop = &$arr["prop"];
		$prop["options"] = $arr["obj_inst"]->get_customer_managers()->names();
		$prop["value"] = isset($arr["request"][$prop["name"]]) ? $arr["request"][$prop["name"]] : $arr["obj_inst"]->default_filter("customers_filter_customer_manager");
		
		$this->__set_filter_onchange_action($prop);
		
		return PROP_OK;
	}
	
	private function __set_filter_onchange_action(&$prop)
	{
		$prop["onclick"] = "AW.UI.crm_customer_view.refresh_customers();";
	}

	/** Outputs autocomplete options matching category name search string $typed_text in bsnAutosuggest format json
		@attrib name=get_category_options
		@param typed_text optional type=string
	**/
	public static function get_category_options($args)
	{
		$choices = array("results" => array());
		$typed_text = $args["typed_text"];
	//	$this_o = new object($args["id"]);
		$limit = 20;
		$list = new object_list(array(
			"class_id" => CL_CRM_CATEGORY,
	//		"organization" => $this_o->prop("company"),
			"name" => "{$typed_text}%",
			new obj_predicate_limit($limit)
		));

		if ($list->count() > 0)
		{
			$results = array();
			$o = $list->begin();
			do
			{
				$value = $o->prop_xml("name");
				$info = "";
				$results[] = array("id" => $o->id(), "value" => iconv("iso-8859-4", "UTF-8", $value), "info" => $info);//FIXME charsets
			}
			while ($o = $list->next());
			$choices["results"] = $results;
		}

		ob_start("ob_gzhandler");
		header("Content-Type: application/json");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Pragma: no-cache"); // HTTP/1.0
		exit(json_encode($choices));
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_crm_customer_view" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_crm_customer_view` (
				  `aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`aw_oid`)
				)");
				$r = true;
			}
			elseif ("" === $field)
			{
				$this->db_add_col("aw_crm_customer_view", array(
					"name" => "",
					"type" => ""
				));
				$r = true;
			}
		}

		return $r;
	}

	private function get_salesman_search_prop(&$arr)
	{
		$r = PROP_IGNORE;
		$arr["prop"]["value"] = isset($arr["request"][$arr["prop"]["name"]]) ? $arr["request"][$arr["prop"]["name"]] : "";

		try
		{
			$oid = new aw_oid($arr["obj_inst"]->prop("role_profession_salesman"));
			$profession = obj($oid, array(), CL_CRM_PROFESSION);
			$this->set_employees_options($arr, $profession, false);
			$r = PROP_OK;
		}
		catch (Exception $e)
		{
		}

		try
		{
			$oid = new aw_oid($arr["obj_inst"]->prop("role_profession_sales_manager"));
			$profession = obj($oid, array(), CL_CRM_PROFESSION);
			$this->set_employees_options($arr, $profession, false);
			$r = PROP_OK;
		}
		catch (Exception $e)
		{
		}

		return $r;
	}
	function _get_cts_salesman(&$arr)
	{
		return $this->get_salesman_search_prop($arr);
	}



	function get_property(&$arr)
	{
		$retval = PROP_OK;



		$data = &$arr['prop'];
		$arr["use_group"] = $this->use_group;
		if(in_array($data["name"] , $this->search_props))
		{
			if(isset($arr["request"][$data["name"]]))
			{
				$data["value"] = $arr["request"][$data["name"]];
			}
			else
			{
				$data["value"] = "";
			}
		}

		switch($data['name'])
		{
			/// CUSTOMER tab$arr
			case "my_projects":
			case "customer_rel_creator":
			case "customer_search_cust_mgr":
			case "customer_search_is_co":
//			case "my_customers_toolbar":
//			case "my_customers_table":
			case "customer_categories_table":
			case "offers_listing_toolbar":
			case "offers_listing_tree":
			case "offers_listing_table":
			case "offers_current_org_id":
			case "projects_listing_tree":
			case "projects_listing_table":
			case "project_tree":
			case "org_proj_tb":
			case "org_proj_arh_tb":
			case "report_list":
			case "all_proj_search_part":
			case "proj_search_part":
			case "customer_toolbar":
			case "customer_listing_tree":
				if(!acl_base::can("view" ,$arr["obj_inst"]->prop("company")))
				{
					return PROP_IGNORE;
				}
				static $cust_impl;
				if (!$cust_impl)
				{
					$cust_impl = new crm_company_cust_impl();
					$cust_impl->layoutinfo = &$this->layoutinfo;
					$cust_impl->use_group = $this->use_group;
				}
				$params = $arr;
				$params["obj_inst"] = obj($arr["obj_inst"]->prop("company"));
				$fn = "_get_".$data["name"];
				return $cust_impl->$fn($params);
		}
		return $retval;
	}

	function callback_get_default_group($arr)
	{
		$default_grp = "general";
		if (!empty($arr["request"]["id"]) and acl_base::can("", $arr["request"]["id"]))
		{
			$o = obj($arr["request"]["id"]);
			if($o->prop("company"))
			{
				$default_grp = "customers";
			}
		}
		return $default_grp;
	}

	function callback_mod_retval(&$arr)
	{
		foreach($this->search_props as $prop)
		{
			if(!empty($arr['request'][$prop]))
			{
				$arr['args'][$prop] = $arr['request'][$prop];
			}
			else
			{
				$arr['args'][$prop] = "";
			}
		}
	}

	function callback_mod_reforb(&$arr, $request)
	{
		if("customers" === substr($this->use_group, 0, 6))
		{
			if (isset($request[self::REQVAR_CATEGORY])) $arr[self::REQVAR_CATEGORY] = $request[self::REQVAR_CATEGORY];
			$arr["sbt_data_add_seller"] = 0;
			$arr["sbt_data_add_buyer"] = 0;
		}
	}

	function submit($args = array())
	{
		if(!empty($args["sbt_data_add_buyer"]) or !empty($args["sbt_data_add_seller"]))
		{
			// FIX-REQVAR_CATEGORY
			$args["s"] = isset($args[self::REQVAR_CATEGORY]) ? $args[self::REQVAR_CATEGORY] : "";
			$args["return_url"] = isset($args["post_ru"]) ? $args["post_ru"] : "";

			if (!empty($args["sbt_data_add_buyer"]))
			{
				$args["o"] = $args["sbt_data_add_buyer"];
				$args["t"] = crm_company_obj::CUSTOMER_TYPE_BUYER;
			}
			elseif (!empty($args["sbt_data_add_seller"]))
			{
				$args["o"] = $args["sbt_data_add_seller"];
				$args["t"] = crm_company_obj::CUSTOMER_TYPE_SELLER;
			}
			$manager = obj($args["id"]);
			$args["id"] = $manager->prop("company");

			$r = $this->add_customer($args);
		}
		else
		{ // normal submit
			$r = parent::submit($args);
		}

		return $r;
	}
	
	public function _get_categories_toolbar(&$arr)
	{
		if (!object_loader::can("", $arr["obj_inst"]->prop("company")))
		{
			return self::PROP_IGNORE;
		}
		
		$t = $arr["prop"]["vcl_inst"];
		
		$t->add_button(array(
			"name" => "new",
			"icon" => "add",
			"tooltip" => t("Lisa kliendikategooria"),
			"url" => $this->mk_my_orb("add_customer_category",array(
				"id" => $arr["obj_inst"]->prop("company"),
				"save_autoreturn" => "1",
				"c" => automatweb::$request->arg_isset(self::CUSTOMER_CATEGORY) ? automatweb::$request->arg(self::CUSTOMER_CATEGORY) : 0,
				"return_url" => get_ru()
			), "crm_company")
		));
		
		$t->add_delete_button();
		
		return self::PROP_OK;
	}
	
	public function _get_categories_tree(&$arr)
	{
		if (!object_loader::can("", $arr["obj_inst"]->company))
		{
			return self::PROP_IGNORE;
		}
		
		$t = $arr["prop"]["vcl_inst"];

		$company = obj($arr["obj_inst"]->company, null, crm_company_obj::CLID);

		$core = new core();
		$url = new aw_uri($core->mk_my_orb($arr["request"]["action"], array(
			"group" => $arr["request"]["group"],
			"id" => $arr["request"]["id"],
			"return_url" => ifset($arr,"request", "return_url"),
		), $arr["request"]["class"]));

		$url->set_arg(self::CUSTOMER_CATEGORY, $company->id());
		$t->add_item(0, array(
			"id" => $company->id(),
			"name" => sprintf(t("%s kliendigrupid"), $company->get_title()),
			"url" => $url->get(),
			"iconurl" => icons::get_std_icon_url("folder"),
		));

		$categories = $company->get_customer_categories();
		foreach ($categories->arr() as $category)
		{
			$category_id = $category->id();
			$parent = $category->prop("parent_category") ? (int) $category->prop("parent_category") : $company->id();
			$url->set_arg(self::CUSTOMER_CATEGORY, $category_id);
			$t->add_item ($parent, array (
				"name" => $category->name(),
				"iconurl" => icons::get_std_icon_url("folder"),
				"id" => $category_id,
				"parent" => $parent,
				"url" => $url->get()
			));
		}

		$t->set_selected_item(automatweb::$request->arg_isset(self::CUSTOMER_CATEGORY) ? automatweb::$request->arg(self::CUSTOMER_CATEGORY) : $company->id());
		
		return self::PROP_OK;
	}
	
	public function _get_categories_table(&$arr)
	{
		if (!object_loader::can("", $arr["obj_inst"]->company))
		{
			return self::PROP_IGNORE;
		}

		$company = obj($arr["obj_inst"]->company, null, crm_company_obj::CLID);
		
		$t = $arr["prop"]["vcl_inst"];
		
		$t->define_chooser();
		$t->add_fields(array(
			"name" => t("Nimi"),
		));
		
		$categories = $company->get_customer_categories(automatweb::$request->arg_isset(self::CUSTOMER_CATEGORY) ? obj(automatweb::$request->arg(self::CUSTOMER_CATEGORY)) : $company);
		
		foreach ($categories->arr() as $category)
		{
			$t->define_data(array(
				"oid" => $category->id(),
				"name" => html::obj_change_url($category)
			));
		}
		
		return self::PROP_OK;
	}

	/** Adds customer. c or o must be defined.
		@attrib name=add_customer
		@param id required type=oid
			Company oid where customer added
		@param t required type=int
			Relation type (seller or buyer). One of crm_company_obj::CUSTOMER_TYPE_... constant values
		@param c optional type=clid
			Customer class id (person or organization) to create
		@param o optional type=oid
			Customer object to be added (person or organization)
		@param s optional type=oid
			Customer category
		@param return_url required type=string
	**/
	function add_customer($arr)
	{
		$r = $arr["return_url"];
		$type = (int) $arr["t"];

		// load company where customer is added
		try
		{
			$this_o = obj($arr["id"], array(), CL_CRM_COMPANY);
		}
		catch (Exception $e)
		{
			$this->show_error_text(sprintf(t("Viga p&auml;ringus! Lubamatu organisatsiooni id '%s'"), $arr["id"]));
			return $r;
		}

		if (crm_company_obj::CUSTOMER_TYPE_BUYER !== $type and crm_company_obj::CUSTOMER_TYPE_SELLER !== $type)
		{
			$this->show_error_text(t("Loodava kliendisuhte t&uuml;&uuml;p m&auml;&auml;ramata"));
			return $r;
		}

		if (!empty($arr["o"]))
		{
			$customer = new object($arr["o"]);
			if (!$customer->is_saved() or !$customer->is_a(CL_CRM_COMPANY) and !$customer->is_a(crm_person_obj::CLID))
			{
				$this->show_error_text(sprintf(t("Antud klient (id '%s') ei ole lisatav"), $customer->id()));
				return $r;
			}
		}
		elseif (!empty($arr["c"]))
		{
			$customer = obj(null, array(), $arr["c"]);
			$customer->set_parent($this_o->id());
			if (!$customer->is_a(CL_CRM_COMPANY) and !$customer->is_a(crm_person_obj::CLID))
			{
				$this->show_error_text(sprintf(t("Antud objekt ('%s') pole lisatav kliendina"), $customer->class_id()));
				return $r;
			}
			$customer->save();
			$params = array();
			$params["return_url"] = $arr["return_url"];
			$params["save_autoreturn"] = "1";

			$r = html::get_change_url($customer, $params);
		}

		try
		{
			if (!($customer_relation = $this_o->get_customer_relation($type, $customer)))
			{
				$customer_relation = $this_o->create_customer_relation($type, $customer);
			}

			// set category if specified
			if (!empty($arr["s"]))
			{
				try
				{
					$category = obj($arr["s"], array(), CL_CRM_CATEGORY);
					$customer_relation->add_category($category);
				}
				catch (Exception $e)
				{
					$this->show_error_text(sprintf(t("Kategooria m&auml;&auml;ramine eba&otilde;nnestus! Lubamatu objekti id '%s'"), $args["s"]));
					return $r;
				}
			}
		}
		catch (Exception $e)
		{
			trigger_error("Caught exception " . get_class($e) . " while trying to add customer ".$customer->id().". Thrown in '" . $e->getFile() . "' on line " . $e->getLine() . ": '" . $e->getMessage() . "' <br> Backtrace:<br>" . dbg::process_backtrace($e->getTrace(), -1, true), E_USER_WARNING);
			$this->show_error_text(t("Kliendi lisamine eba&otilde;nnestus."));
		}

		return $r;
	}

	/**
		@attrib name=customer_view_cut
		@param cust_check optional type=array
		@param cat_check optional type=array
		@param cs_c required type=string
		@param post_ru required type=string
	**/
	function customer_view_cut($arr)
	{
		$check = array();

		if (isset($arr["cust_check"]))
		{
			$check += $arr["cust_check"];
		}

		if (isset($arr["cat_check"]))
		{
			$check += $arr["cat_check"];
		}

		aw_session::set("awcb_clipboard_action", "cut");
		aw_session::set("awcb_customer_selection_clipboard", $check);
		// FIX-REQVAR_CATEGORY
		aw_session::set("awcb_category_old_parent", (isset($arr[self::REQVAR_CATEGORY]) ? $arr[self::REQVAR_CATEGORY] : ""));
		return $arr["post_ru"];
	}

	/**
		@attrib name=customer_view_copy
		@param cust_check optional type=array
		@param cat_check optional type=array
		@param cs_c required type=string
		@param post_ru required type=string
	**/
	function customer_view_copy($arr)
	{
		$check = array();

		if (isset($arr["cust_check"]))
		{
			$check = array_merge($check, $arr["cust_check"]);
		}

		if (isset($arr["cat_check"]))
		{
			$check = array_merge($check, $arr["cat_check"]);
		}

		aw_session::set("awcb_clipboard_action", "copy");
		aw_session::set("awcb_customer_selection_clipboard", $check);
		// FIX-REQVAR_CATEGORY
		aw_session::set("awcb_category_old_parent", (isset($arr[self::REQVAR_CATEGORY]) ? $arr[self::REQVAR_CATEGORY] : ""));
		return $arr["post_ru"];
	}

	/**
		@attrib name=customer_view_paste
		@param id required type=oid acl=view
			This object id
		@param cs_c required type=string
		@param post_ru required type=string
	**/
	function customer_view_paste($arr)
	{
		$errors = array();
		$action = aw_session::get("awcb_clipboard_action");
		$manager = obj($arr["id"]);
		$this_object = obj($manager->prop("company"), array(), crm_company_obj::CLID);

		if (aw_session::get("awcb_customer_selection_clipboard"))
		{
			try
			{
				// get old parent
				if (is_oid(aw_session::get("awcb_category_old_parent")))
				{
					$old_parent = aw_session::get("awcb_category_old_parent");
					try
					{
						$old_parent = obj($old_parent, array(), crm_category_obj::CLID);
					}
					catch (awex_obj_class $e)
					{
						$old_parent = null;
					}
				}
				else
				{
					$old_parent = null;
				}

				// find new parent
				// FIX-REQVAR_CATEGORY
				$new_parent = !empty($arr[self::REQVAR_CATEGORY]) ? $arr[self::REQVAR_CATEGORY] : null;
				if (is_oid($new_parent))
				{
					try
					{
						$new_parent = obj($new_parent, array(), crm_category_obj::CLID);
						$new_parent_oid = $new_parent->id();
					}
					catch (awex_obj_class $e)
					{
						$new_parent = null;
						$new_parent_oid = 0;
					}
				}
				else
				{
					$this->show_error_text(t("Kategooria valimata"));
					return $arr["post_ru"];
				}

				// perform action on objects
				$selected_objects = aw_session::get("awcb_customer_selection_clipboard");
				foreach ($selected_objects as $oid)
				{
					if ($oid)
					{
						try
						{
							$o = new object($oid);
							if ($o->is_a(crm_company_customer_data_obj::CLID)) // process cut/copied customer objects
							{ // move or copy customer to new category. if new category not given, just remove from old.
								if ("cut" === $action  and $old_parent)
								{ // cut action requested -- remove old category
									$o->remove_category($old_parent);
								}

								if ($new_parent)
								{ // if new parent given, add that category to customer's categories
									$o->add_category($new_parent);
								}
							}
							elseif ($o->is_a(crm_category_obj::CLID)) // process cut/copied categories
							{ // replace category parent category
								if (
									"copy" === $action and
									$o->prop("parent_category") != $new_parent // avoid paste (making a copy with same name) to where copied category originally is
								)
								{ // add a new category by same name
									$category_copy = $this_object->add_customer_category($new_parent, (int) $o->prop("category_type"));
									$category_copy->set_name($o->name());
									$o = $category_copy;
								}

								$o->set_prop("parent_category", $new_parent_oid);
								$o->save();
							}
							else
							{
								$errors[] = "({$oid})";
							}
						}
						catch (Exception $e)
						{
							trigger_error("Caught exception " . get_class($e) . ". Thrown in '" . $e->getFile() . "' on line " . $e->getLine() . ": '" . $e->getMessage() . "' <br /> Backtrace:<br />" . dbg::process_backtrace($e->getTrace(), -1, true), E_USER_WARNING);
							$errors[] = $oid;
						}
					}
				}
			}
			catch (Exception $e)
			{
				$this->show_error_text(t("Kleepimiskoht defineerimata"));
				trigger_error("Caught exception " . get_class($e) . ". Thrown in '" . $e->getFile() . "' on line " . $e->getLine() . ": '" . $e->getMessage() . "' <br /> Backtrace:<br />" . dbg::process_backtrace($e->getTrace(), -1, true), E_USER_WARNING);
			}
		}

		aw_session::del("awcb_clipboard_action");
		aw_session::del("awcb_customer_selection_clipboard");
		aw_session::del("awcb_category_old_parent");

		if (count($errors))
		{
			$this->show_error_text(sprintf(t("Viga kleebitava(te) objekti(de) lugemisel [%s]"), implode(", ", $errors)));
		}

		return $arr["post_ru"];
	}

	/**
		@attrib name=remove_from_category all_args=1
	**/
	function remove_from_category($arr)
	{
		if (is_array($arr["cust_check"]) and count($arr["cust_check"]) and is_oid($arr["customer_category"]))
		{
			$errors = array();
			try
			{
				$category = obj($arr["customer_category"]);

				foreach($arr['cust_check'] as $customer_relation_oid)
				{
					try
					{
						$customer_relation_o = obj($customer_relation_oid);
						$customer_relation_o->remove_category($category);
					}
					catch (Exception $e)
					{
						$errors[] = $customer_relation_o->name();
					}
				}
			}
			catch (Exception $e)
			{
				$this->show_error_text(t("Viga kategooria lugemisel"));
			}

			if ($errors)
			{
				$this->show_error_text(sprintf(t("Kliente %s kategooriast eemaldada ei &otilde;nnestunud."), ("'" . implode("', '", $errors) . "'")));
			}
		}
		return $arr["post_ru"];
	}

	/**
		@attrib name=delete_selected_objects
	**/
	function delete_selected_objects($arr)
	{
		$selected_objects = $errors = array();
		if (!empty($arr["select"]))
		{
			$selected_objects += $arr["select"] ;
		}

		if (!empty($arr["cust_check"]))
		{
			$selected_objects += $arr["cust_check"] ;
		}

		if (!empty($arr["cat_check"]))
		{
			$selected_objects += $arr["cat_check"] ;
		}

		foreach ($selected_objects as $delete_obj_id)
		{
			if (object_loader::can("delete", $delete_obj_id))
			{
				$deleted_obj = obj($delete_obj_id);
				$deleted_obj->delete();
			}
			else
			{
				$errors[] = $delete_obj_id;
			}
		}

		if (count($errors))
		{
			$this->show_error_text(sprintf(t("Objekte %s ei saanud kustutada."), implode(", ", $errors)));
		}

		// return url
		$r = empty($arr["post_ru"]) ? $this->mk_my_orb("change", array(
			"id" => $arr["id"],
			"group" => $arr["group"],
	//		"org_id" => isset($arr["offers_current_org_id"]) ? $arr["offers_current_org_id"] : 0
			),
			$arr["class"]
		) : $arr["post_ru"];
		return $r;
	}

	/**
		@attrib name=remove_buy_relations all_args=1
	**/
	function remove_buy_relations($arr)
	{
		if (is_array($arr["cust_check"]) and count($arr["cust_check"]))
		{
			try
			{
				$manager = obj($arr["id"]);
				$this_o = obj($manager->prop("company"), array(), crm_company_obj::CLID);
			}
			catch (Exception $e)
			{
				$this->show_error_text(t("Organisatsiooniobjekt polnud loetav."));
				return $arr["post_ru"];
			}

			$errors = array();
			foreach($arr["cust_check"] as $customer_relation_oid)
			{
				try
				{
					$customer_relation_o = obj($customer_relation_oid);
					$this_o->delete_customer($customer_relation_o);
				}
				catch (Exception $e)
				{
					$errors[] = $customer_relation_o->name();
				}
			}

			if ($errors)
			{
				$this->show_error_text(sprintf(t("Kliendisuhteid %s l&otilde;petada ei &otilde;nnestunud."), ("'" . implode("', '", $errors) . "'")));
			}
		}

		return $arr["post_ru"];
	}

	/**
		@attrib name=remove_sell_relations all_args=1
	**/
	function remove_cust_relations($arr)
	{
		if (is_array($arr["cust_check"]) and count($arr["cust_check"]))
		{
			try
			{
				$manager = obj($arr["id"]);
				$this_o = obj($manager->prop("company"), array(), crm_company_obj::CLID);
			}
			catch (Exception $e)
			{
				$this->show_error_text(t("Organisatsiooniobjekt polnud loetav."));
				return $arr["post_ru"];
			}

			$errors = array();
			foreach($arr["cust_check"] as $customer_relation_oid)
			{
				try
				{
					$customer_relation_o = obj($customer_relation_oid);
					$this_o->delete_customer($customer_relation_o);
				}
				catch (Exception $e)
				{
					$errors[] = $customer_relation_o->name();
				}
			}

			if ($errors)
			{
				$this->show_error_text(sprintf(t("Kliendisuhteid %s l&otilde;petada ei &otilde;nnestunud."), ("'" . implode("', '", $errors) . "'")));
			}
		}

		return $arr["post_ru"];
	}

	function _get_my_customers_toolbar($arr)
	{
		if(!acl_base::can("view" ,$arr["obj_inst"]->prop("company"))) return;

		$tb = $arr["prop"]["vcl_inst"];
		$tb->cache_items = false;
		
		$categories = array();
		if ($this->filter_customer_category !== null)
		{
			if (!empty($this->filter_customer_category))
			{
				$ol = new object_list(array(
					"class_id" => crm_category_obj::CLID,
					"oid" => $this->filter_customer_category,
				));
				$categories = $ol->names();
			}
		}
		if (empty($categories))
		{
			$categories = array(0 => html::italic(t("Ilma kliendikategooriata")));
		}

		$tb->add_menu_button(array(
			"name"=>"add_item",
			"icon" => "add",
			"tooltip"=> t("Uus")
		));

		$tb->add_menu_button(array(
			"name" => "search_item",
			"icon" => "magnifier",
			"tooltip" => t("Otsi")
		));

		$tb->add_sub_menu(array(
			'parent'=> "add_item",
			"name" => "add_buyer",
			'text' => t("Ostja"),
		));
		$tb->add_sub_menu(array(
			'parent'=> "add_buyer",
			"name" => "add_buyer_company",
			'text' => t("Organisatsioon"),
		));
		foreach ($categories as $category_id => $category_name)
		{
			$tb->add_menu_item(array(
				"parent"=> "add_buyer_company",
				"text" => sprintf(t("Kategooriasse '%s'"), $category_name),
				"link" => "javascript:void(0)",
				"onclick" => "AW.UI.crm_customer_view.open_customer_modal('company', " . crm_company_obj::CUSTOMER_TYPE_BUYER . ", {$category_id})"
			));
		}
		$tb->add_sub_menu(array(
			'parent'=> "add_buyer",
			"name" => "add_buyer_person",
			'text' => t("Eraisik"),
		));
		foreach ($categories as $category_id => $category_name)
		{
			$tb->add_menu_item(array(
				"parent"=> "add_buyer_person",
				"text" => sprintf(t("Kategooriasse '%s'"), $category_name),
				"link" => "javascript:void(0)",
				"onclick" => "AW.UI.crm_customer_view.open_customer_modal('person', " . crm_company_obj::CUSTOMER_TYPE_BUYER . ", {$category_id})"
			));
		}

		// search and add customer from existing persons/organizations in database
		$url = $this->mk_my_orb("do_search", array(
			"clid" => array(crm_company_obj::CLID, CL_CRM_PERSON),
			"pn" => "sbt_data_add_buyer"
		), "popup_search");
		$tb->add_menu_item(array(
			"parent" => "search_item",
			"text" => t("Lisa ostja olemasolevate isikute/organisatsioonide hulgast"),
			"link" => "#",
			"url" => "#",
			"onClick" => html::popup(array(
				"url" => $url,
				"resizable" => true,
				"scrollbars" => "auto",
				"height" => 500,
				"width" => 700,
				"no_link" => true,
				"quote" => "'"
			))
		));

		$tb->add_sub_menu(array(
			'parent'=> "add_item",
			"name" => "add_seller",
			'text' => t("Müüja"),
		));
		$tb->add_sub_menu(array(
			'parent'=> "add_seller",
			"name" => "add_seller_company",
			'text' => t("Organisatsioon"),
		));
		foreach ($categories as $category_id => $category_name)
		{
			$tb->add_menu_item(array(
				"parent"=> "add_seller_company",
				"text" => sprintf(t("Kategooriasse '%s'"), $category_name),
				"link" => "javascript:void(0)",
				"onclick" => "AW.UI.crm_customer_view.open_customer_modal('company', " . crm_company_obj::CUSTOMER_TYPE_SELLER . ", {$category_id})"
			));
		}
		$tb->add_sub_menu(array(
			'parent'=> "add_seller",
			"name" => "add_seller_person",
			'text' => t("Eraisik"),
		));
		foreach ($categories as $category_id => $category_name)
		{
			$tb->add_menu_item(array(
				"parent"=> "add_seller_person",
				"text" => sprintf(t("Kategooriasse '%s'"), $category_name),
				"link" => "javascript:void(0)",
				"onclick" => "AW.UI.crm_customer_view.open_customer_modal('person', " . crm_company_obj::CUSTOMER_TYPE_SELLER . ", {$category_id})"
			));
		}

		//  search and add customer from existing persons/organizations in database
		$url = $this->mk_my_orb("do_search", array(
			"clid" => array(crm_company_obj::CLID, CL_CRM_PERSON),
			"pn" => "sbt_data_add_seller"
		), "popup_search");
		$tb->add_menu_item(array(
			"parent" => "search_item",
			"text" => t("Lisa m&uuml;&uuml;ja olemasolevate isikute/organisatsioonide hulgast"),
			"link" => "#",
			"url" => "#",
			"onClick" => html::popup(array(
				"url" => $url,
				"resizable" => true,
				"scrollbars" => "auto",
				"height" => 500,
				"width" => 700,
				"no_link" => true,
				"quote" => "'"
			))
		));

		$tb->add_separator();

		// cut, copy, paste
		$tb->add_button(array(
			"name" => "cut",
			"tooltip" => t("L&otilde;ika"),
			"action" => "customer_view_cut",
			"icon" => "cut"
		));

		$tb->add_button(array(
			"name"=>"copy",
			"tooltip"=> t("Kopeeri"),
			"action" => "customer_view_copy",
			"icon" => "copy"
		));

		if (aw_global_get("awcb_customer_selection_clipboard"))
		{
			$tb->add_button(array(
				"name"=>"paste",
				"tooltip"=> t("Kleebi"),
				"action" => "customer_view_paste",
				"icon" => "paste"
			));
		}


		// customers delete button
		$tb->add_menu_button(array(
			"name"=>"delete_customers",
			"tooltip"=> t("Eemalda valitud kliendid"),
			"icon" => "link_delete"
		));

		if($this->filter_customer_category !== null)
		{
			$tb->add_sub_menu(array(
				'parent'=> "delete_customers",
				"name" => "remove_from_category",
				'text' => t("Eemalda kategooriast"),
			));
			foreach ($categories as $category_id => $category_name)
			{
				$tb->add_menu_item(array(
					"parent"=> "remove_from_category",
					"text" => $category_name,
					"onclick" => "$('input[type=hidden][name=customer_category]').val({$category_id});",
					"action" => "remove_from_category"
				));
			}
		}

		$tb->add_menu_item(array(
			"parent"=> "delete_customers",
			"text" => t("L&otilde;peta kliendisuhe"),
			"action" => "remove_cust_relations"
		));

		$tb->add_menu_item(array(
			"parent"=> "delete_customers",
			"text" => t("Kustuta klient t&auml;ielikult"),
			"action" => "delete_selected_objects"
		));
	}

	function _get_my_customers_table(&$arr)
	{ // lists customers, filters by search parameters
		if(!acl_base::can("view" ,$arr["obj_inst"]->prop("company"))) return;
		$company = obj($arr['obj_inst']->prop("company"));
		$customer_relations_list = array();


		foreach(array("seller" , "buyer") as $company_action)
		{
			$customer_relations_search = new crm_sales_contacts_search();
			$customer_relations_search->$company_action = $company;

			if (!empty($arr["request"]["cs_n"]))
			{
				$customer_relations_search->name = "%{$arr["request"]["cs_n"]}%";
			}
			elseif (!empty($arr["request"]["filt_p"]))
			{
				$customer_relations_search->name = "{$arr["request"]["filt_p"]}%";
			}
			else
			{
				$customer_relations_search->name = "%%";
			}

			if (!empty($arr["request"]["customer_search_reg"]))
			{
				$customer_relations_search->reg_nr = "{$arr["request"]["customer_search_reg"]}%";
			}

			if (!empty($arr["request"]["customer_search_address"]))
			{
				$customer_relations_search->address = "%{$arr["request"]["customer_search_address"]}%";
			}

			if (!empty($arr["request"]["customer_search_city"]))
			{
				$customer_relations_search->city = "%{$arr["request"]["customer_search_city"]}%";
			}

			if (!empty($arr["request"]["customer_search_county"]))
			{
				$customer_relations_search->county = "%{$arr["request"]["customer_search_county"]}%";
			}

			if (!empty($arr["request"]["cts_comment"]))
			{
				$customer_relations_search->comment = "%{$arr["request"]["cts_comment"]}%";
			}

			if (!empty($arr["request"]["area"]))
			{
				$customer_relations_search->area = $arr["request"]["area"];
			}

			if (!empty($arr["request"]["pmgr"]))
			{
				$customer_relations_search->manager = $arr["request"]["pmgr"];
			}
			if (!empty($this->filter_customer_manager))
			{
				$customer_relations_search->customer_manager = $this->filter_customer_manager;
			}

			if (!empty($arr["request"]["cts_phone"]))
			{
				$customer_relations_search->phone = $arr["request"]["cts_phone"];
			}
			if (!empty($arr["request"]["cts_lead_source"]))
			{
				$customer_relations_search->lead_source = "{$arr["request"]["cts_lead_source"]}%";
			}
			if (!empty($arr["request"]["cts_contact"]))
			{
				$customer_relations_search->contact_name = "{$arr["request"]["cts_contact"]}%";
			}
			if (!empty($this->filter_state))
			{
				$customer_relations_search->status = $this->filter_state;
			}

			if (!empty($arr["request"]["cts_cat"]))
			{
				$customer_relations_search->category = obj($arr["request"]["cts_cat"]);
			}

			if (!empty($arr["request"]["cts_calls"]))
			{
				$calls_constraint = null;
				$tmp = preg_split("/\s*/", $arr["request"]["cts_calls"], 2, PREG_SPLIT_NO_EMPTY);
				if (1 === count($tmp) and is_numeric($tmp[0]))
				{
					$calls_constraint = new obj_predicate_compare(obj_predicate_compare::EQUAL, (int) $tmp[0]);
				}
				elseif (2 === count($tmp) and is_numeric($tmp[1]))
				{
					$calls_compare_value = (int) $tmp[1];
					if ("<" === $tmp[0] and $calls_compare_value)
					{
						$calls_constraint = new obj_predicate_compare(obj_predicate_compare::LESS, $calls_compare_value);
					}
					elseif (">" === $tmp[0])
					{
						$calls_constraint = new obj_predicate_compare(obj_predicate_compare::GREATER, $calls_compare_value);
					}
					elseif ("=" === $tmp[0])
					{
						$calls_constraint = new obj_predicate_compare(obj_predicate_compare::EQUAL, (int) $tmp[1]);
					}
					else
					{
						class_base::show_error_text(t("Viga k&otilde;nede arvu v&otilde;rdlusoperaatoris"));
					}
				}
				else
				{
					class_base::show_error_text(t("Viga k&otilde;nede arvu parameetris"));
				}

				$customer_relations_search->calls = $calls_constraint;
				$params_defined = true;
			}


			$customer_relations_search->set_sort_order("name-asc");

			if ($this->filter_customer_category !== null)
			{
				$categories = array();
				foreach($this->filter_customer_category as $category_id)
				{
					if (object_loader::can("", $category_id))
					{
						$category = obj($category_id);

						if ($category->is_a(crm_category_obj::CLID))
						{
							$categories[] = $category;
						}
						elseif ($category->is_a(crm_company_obj::CLID))
						{
							$categories[] = null;
						}
					}
				}
				$customer_relations_search->category = $categories;
			}

			$customer_relations_one_list = $customer_relations_search->get_customer_relation_oids(new obj_predicate_limit(crm_settings::LIST_LENGTH_DEFAULT));
			$customer_relations_list = $customer_relations_list + $customer_relations_one_list;

		}
		$this->_finish_org_tbl($arr, $customer_relations_list);

		// print table only
		//TODO: probably needs some style
		if (!empty($arr["request"]["customer_search_print_view"]))
		{
			$sf = new aw_template();
			$sf->db_init();
			$sf->tpl_init("automatweb");
			$sf->read_template("index.tpl");
			$sf->vars(array(
				"content"	=> $arr["prop"]["vcl_inst"]->draw(),
				"uid" => aw_global_get("uid"),
				"charset" => aw_global_get("charset")
			));
			exit($sf->parse());
		}
	}


	function _finish_org_tbl($arr, $customer_relations_list)
	{

		$mail_inst = get_instance(CL_ML_MEMBER);
/*		if ("relorg_s" === $this->use_group)
		{ // list sellers
			$customer_relation_type_prop = "seller";
		}
		elseif ("relorg_b" === $this->use_group)
		{ // list buyers
		*/
			$customer_relation_type_prop = "buyer";
//		}

		$tf = $arr["prop"]["vcl_inst"];
		$manager = obj($arr["request"]["id"]);
		$org = obj($manager->prop("company"));


		$format = t("%s kliendid");
		$requested_category = isset($arr["request"][self::REQVAR_CATEGORY]) ? $arr["request"][self::REQVAR_CATEGORY] : null;

		$this->_org_table_header($arr);
		$default_cfg = true;

		$cl_crm_settings = new crm_settings();
		$cl_crm_company = new crm_company();
		if ($o = $cl_crm_settings->get_current_settings())
		{
			$usecase = $cl_crm_company->get_current_usecase($arr);//$arr["obj_inst"] peab olemas olema.
			$cl_crm_settings->apply_table_cfg($o, $usecase, $arr["prop"]["name"], $tf);
			$visible_fields = $cl_crm_settings->get_visible_fields($o, $usecase, $arr["prop"]["name"]);

			if (!empty($visible_fields))
			{
				$default_cfg = false;
			}
		}

		//TODO: teha et ei peaks lugema neid eraldi arraysse vms.
		//tmp. get and index customers by cro-s
		$customer_list = array();
		$buyers = array();
		$sellers = array();
		$idx_cro_by_customer = array();
		foreach ($customer_relations_list as $cro_oid => $cro_data)
		{
			$cro_o = new object($cro_oid);

			if($cro_o->prop("buyer") == $org->id())
			{
				$customer_oid = $cro_o->prop("seller");
			}

			if($cro_o->prop("seller") == $org->id())
			{
				$customer_oid = $cro_o->prop("buyer");
			}
			$customer_list[]  = $customer_oid;

			$idx_cro_by_customer[$customer_oid][] = $cro_o;
		}
		//end tmp

		# some helper data for roles
		if ($default_cfg or in_array("rollid", $visible_fields))
		{
			$rc_by_co = array();
			$role_entry_list = new object_list(array(
				"class_id" => CL_CRM_COMPANY_ROLE_ENTRY,
				"company" => $arr["request"]["id"],
				"client" => $customer_list,
				"project" => new obj_predicate_compare(OBJ_COMP_LESS, 1)
			));
			foreach($role_entry_list->arr() as $role_entry)
			{
				$rc_by_co[$role_entry->prop("client")][$role_entry->prop("person")][] = html::get_change_url(
						$arr["request"]["id"],
						array(
							"group" => "contacts2",
							"unit" => $role_entry->prop("unit"),
						),
						parse_obj_name($role_entry->prop_str("unit"))
					)
					."/".
					html::get_change_url(
						$arr["request"]["id"],
						array(
							"group" => "contacts2",
							self::REQVAR_CATEGORY => $role_entry->prop("role")
						),
						parse_obj_name($role_entry->prop_str("role"))
					);
			}
		}

		# table contents
		$perpage = 100;
		$page_nr = isset($arr["request"]["ft_page"]) ? (int) $arr["request"]["ft_page"] : 0;
		$org_count = count($customer_list);
		if($perpage > $org_count)
		{
			$page_nr = 0;
		}

		foreach($customer_list as $customer_oid)
		{
			try
			{
				$o = obj($customer_oid);
			}
			catch (Exception $e)
			{
				continue;
			}

			// get customer relation object
			$cro_oid = 0;
			$cro_obj = null;
			if(sizeof($idx_cro_by_customer[$o->id()]) == 1)
			{
				$cro_obj = reset($idx_cro_by_customer[$o->id()]);
				$cro_oid = $cro_obj->id();
			}
			else
			{
				foreach($idx_cro_by_customer[$o->id()] as $cro_obj)
				{
					if($cro_obj->prop("seller") == $org->id())
					{
						$cro_oid = $cro_obj->id();
					}
				}
				if(!$cro_oid)
				{
					$cro_obj = reset($idx_cro_by_customer[$o->id()]);
					$cro_oid = $cro_obj->id();
				}
			}

			// aga &uuml;lej&auml;&auml;nud on k&otilde;ik seosed!
			$name = $client_manager = $pm = $vorm = $tegevus = $contact = $juht = $juht_id = $phone = $fax = $url = $mail = $ceo = "";

			# rollid
			if ($default_cfg or in_array("rollid", $visible_fields))
			{
				$roles = $this->_get_role_html(array(
					"from_org" => $arr["request"]["id"],
					"to_org" => $o->id(),
					"rc_by_co" => $rc_by_co
				));
			}

			if ($o->is_a(crm_company_obj::CLID))
			{
				try
				{
					$tmp = obj($o->prop("ettevotlusvorm"), array(), CL_CRM_CORPFORM);
					$vorm = html::space() . $tmp->prop("shortname");
				}
				catch (awex_obj $e)
				{
				}

				# ceo
				if ($default_cfg or in_array("ceo", $visible_fields))
				{
					$ceo = html::obj_change_url($o->prop("firmajuht"));
				}

				# email
				if ($default_cfg or in_array("email", $visible_fields))
				{
					try
					{
						$mail_obj = new object($o->prop("email_id"));
						$mail = $mail_obj->prop("mail");
						$mail = empty($mail) ? "" : html::href(array(
							"url" => "mailto:" . $mail,
							"caption" => $mail
						));
					}
					catch (awex_obj $e)
					{
					}
				}

				# url
				if (($default_cfg or in_array("url", $visible_fields)) and ($this->can("", $o->prop("url_id"))))
				{
					$url_o = obj($o->prop("url_id"));
					$url_str = $url_o->name();
					if ($url_str)
					{
						if (strpos($url_str, "http:") === false && substr($url_str, 0, 3) === "www")
						{
							$url_str = "http://{$url_str}";
						}

						$url = html::href(array(
							"url" => $url_str,
							"caption" => $url_str,
							"target" => "_blank"
						));
					}
				}

				# phone
				if (($default_cfg or in_array("phone", $visible_fields)))
				{
					$phones = $o->get_phones()->names();
					$count = count($phones);
					$i = 0;
					foreach ($phones as $phone_nr)
					{
						$phone .= html::span(array("content" => $phone_nr . (++$i === $count ? "" : ", "), "nowrap" => true));
					}
				}
			}
			elseif ($o->is_a(crm_person_obj::CLID))
			{
				// e-mail address
				try
				{
					$mail_obj = obj($o->prop("email"), array(), CL_ML_MEMBER);
					$mail = html::href(array(
						"url" => "mailto:" . $mail_obj->prop("mail"),
						"caption" => $mail_obj->prop("mail"),
					));
				}
				catch (awex_obj $e)
				{
				}

				// web address
				try
				{
					$urlo = obj($o->prop("url"), array(), CL_EXTLINK);
					$ru = $urlo->prop_str("url");
					if (substr($ru, 0, 4) != "http")
					{
						$ru = "http://".$ru;
					}
					$url = html::href(array(
						"url" => $ru,
						"caption" => $urlo->prop_str("url"),
					));
				}
				catch (awex_obj $e)
				{
				}

				// phone
				try
				{
					$tmp = obj($o->prop("phone"), array(), CL_CRM_PHONE);
					$phone = $tmp->name();
				}
				catch (awex_obj $e)
				{
				}
			}

			# fax
			if (($default_cfg or  in_array("fax", $visible_fields)) and $o->is_a(crm_company_obj::CLID) and acl_base::can("", $o->prop("telefax_id")))
			{
				$fax = obj($o->prop("telefax_id"));
				$fax = $fax->name();
			}

			# client_manager
			if ($default_cfg or in_array("client_manager", $visible_fields))
			{
				$client_manager = html::obj_change_url($cro_obj->prop("client_manager"));
			}

			# pop
			if ($default_cfg or in_array("pop", $visible_fields))
			{
				$pm = new popup_menu();
				$pm->begin_menu("org".$o->id());
				$pm->add_item(array(
					"text" => t("Vaata klienti"),
					"link" => $this->mk_my_orb("change", array("id" => $o->id(), "return_url" => get_ru(), "group" => "quick_view"), $o->class_id())
				));
				$pm->add_item(array(
					"text" => t("Muuda klienti"),
					"link" => html::get_change_url($o->id(), array("return_url" => get_ru()))
				));

				foreach ($idx_cro_by_customer[$o->id()] as $cro)
				{
					$pm->add_item(array(
						"text" => t("Muuda kliendisuhet"),
						"link" => html::get_change_url($cro, array("return_url" => get_ru()))
					));
				}

				if (!empty($requested_category))
				{
					$pm->add_item(array(
						"text" => t("Eemalda kliendigrupist"),
						"link" => $this->mk_my_orb("remove_from_cust_grp", array(
							"id" => $o->id(),
							"cgrp" => $requested_category,
							"post_ru" => get_ru()
						))
					));
				}
				$pm = $pm->get_menu();
			}

			# name
			if ($default_cfg or in_array("name", $visible_fields))
			{
				if ($cro_obj->prop("short_name"))
				{
					$short_name = $cro_obj->prop("short_name");
				}
				elseif ($o->is_a(CL_CRM_COMPANY) and $o->prop("short_name"))
				{
					$short_name = $o->prop("short_name");
				}
				elseif ($o->is_a(CL_CRM_PERSON))
				{
					$short_name = $o->prop("lastname");
				}
				else
				{
					$short_name = "";
				}

				$name = html::span(array(
					"nowrap" => true,
					"content" =>
						icons::get_class_icon($o->class_id()) .
						html::space() .
						html::href(array(
							"url" => "javascript:void(0)",
							"onclick" => "AW.UI.crm_customer_view.open_customer_modal(\"" . ($o->is_a(crm_person_obj::CLID) ? "person" : "company") . "\", " . ($cro_obj !== null && $cro_obj->seller == $o->id ? crm_company_obj::CUSTOMER_TYPE_SELLER : crm_company_obj::CUSTOMER_TYPE_BUYER) . ", [" . implode(",", $this->filter_customer_category) . "], " . $o->id() . ")",
							"caption" => ($o->name() ? $o->name() : t("[Nimetu]")) . $vorm
						)) .
						html::linebreak() .
						$short_name
				));

	/*			if ($o->is_a(crm_company_obj::CLID))
				{
					$_url = $this->mk_my_orb("get_cust_contact_table", array("id" => $o->id(), "return_url" => post_ru()));
					$name .= html::href(array(
						"url" => "javascript:void(0)",
						"id" => "tnr" . $o->id(),
						"caption" => t("(Kontaktid)"),
						"onclick" => "co_contact(" . $o->id() . ",\"{$_url}\");"
					));
				}*/
			}

			$c = $o->connections_from(array(
				"type" => "RELTYPE_METAMGR"
			));

			if (count($c))
			{
				$classif1 = array();
				foreach ($c as $c_o)
				{
					$classif1[] = $c_o->prop("to.name");
				}
				$classif1 = implode(", ", $classif1);
			}
			else
			{
				$classif1 = t("N/A");
			}

			$customer_rel_order_a = array();
			foreach($idx_cro_by_customer[$o->id()] as $cro)
			{
				if($cro->prop("buyer") == $org->id())
				{
					$customer_rel_order_a[]= html::href(array(
						"caption" => sprintf(t("%s ostab %s"), $this->__format_party_name($cro, "buyer", 0, true), $this->__format_party_name($cro, "seller", 2)),
						"url" => html::get_change_url($cro, array("return_url" => get_ru()))
					));
				}
				else
				{
					$customer_rel_order_a[]= html::href(array(
						"caption" => sprintf(t("%s müüb %s"), $this->__format_party_name($cro, "seller", 0, true), $this->__format_party_name($cro, "buyer", 1)),
						"url" => html::get_change_url($cro, array("return_url" => get_ru()))
					));

				}

	/*			$customer_rel_order_a[]= html::href(array(
					"caption" => $cro->prop("buyer") == $org->id() ? "M&uuml;&uuml;me" : "Ostame",
					"url" => html::get_change_url($cro, array("return_url" => get_ru()))
				));*/
			}
			$customer_rel_order = join(html::linebreak() , $customer_rel_order_a);

			$bp = array();

			if($cro_obj->is_property("buyer.firmajuht") and $cro_obj->prop("buyer.firmajuht")) $bp[] = $this->get_person_data( $cro_obj->prop("buyer.firmajuht"), $cro_obj->prop("buyer"), t("&Uuml;ldjuht"));

			if($cro_obj->prop("buyer_contract_creator"))$bp[] =$this->get_person_data( $cro_obj->prop("buyer_contract_creator"), $cro_obj->prop("buyer"), t("Hankijasuhte looja"));


			$bill_person_ol = new object_list($cro_obj->connections_from(array("reltype" => "RELTYPE_BILL_PERSON")));
			if($bill_person_ol->count())
			{
				$person = $bill_person_ol->begin();
				do
				{
					if($cro_obj->prop("buyer_contract_creator"))$bp[] =$this->get_person_data( $person->id(), $cro_obj->prop("buyer"), t("Arve saaja"));
				}
				while ($person = $bill_person_ol->next());
			}

			$sp = array();
			if($cro_obj->prop("seller.class_id") == crm_company_obj::CLID && $cro_obj->prop("seller.firmajuht")) $sp[] = $this->get_person_data( $cro_obj->prop("seller.firmajuht"), $cro_obj->prop("seller"), t("&Uuml;ldjuht"));
			if($cro_obj->prop("cust_contract_creator")) $sp[] = $this->get_person_data( $cro_obj->prop("cust_contract_creator"), $cro_obj->prop("seller"), t("Kliendisuhte looja"));
			if($cro_obj->prop("client_manager")) $sp[] = $this->get_person_data( $cro_obj->prop("client_manager"), $cro_obj->prop("seller"), t("Kliendihaldur"));
			if($cro_obj->prop("salesman")) $sp[] = $this->get_person_data( $cro_obj->prop("salesman"), $cro_obj->prop("seller"), t("M&uuml;&uuml;giesindaja"));

			if ($o->class_id() == crm_company_obj::CLID and acl_base::can("view" ,$o->prop("contact")))
			{
				$address_object = obj($o->prop("contact"));
			}
			elseif ($o->class_id() == crm_person_obj::CLID and acl_base::can("view" ,$o->prop("address")))
			{
				$address_object = obj($o->prop("address"));
			}
			else
			{
				$address_object = null;
			}

			if ($address_object and $address_object->is_a(address_object::CLID))
			{
				$city = $address_object->prop("parent.name");
				$county = $address_object->prop("parent.parent.name");
				$address_row = $address_object->prop("street")." ".$address_object->prop("house");
				$address_row.= html::linebreak().$address_object->prop("postal_code")." ".$city;
				$address_row.= html::linebreak().$county." ".$address_object->prop("parent.parent.parent.name");
			}
			else
			{
				$address_row = "";
			}

			if($url)
			{
				$address_row.= html::linebreak().$url;
			}

//-------- mailid

			$conns = $o->connections_from(array(
				"type" => "RELTYPE_EMAIL",
			));
			foreach($conns as $conn)
			{
				$obj = $conn->to();
				$obj->conn_id = $conn->id();
				$address_row.= html::linebreak().($obj->prop("contact_type") ? $mail_inst->types[$obj->prop("contact_type")].": " : "").$obj->name();
			}

//-------- telefonid

			$conns = $o->connections_from(array(
				"type" => "RELTYPE_PHONE",
			));

			$ptypes = crm_phone_obj::get_old_type_options();

			foreach($conns as $conn)
			{
				$obj = $conn->to();
				$address_row.= html::linebreak().($obj->prop("type") ? $ptypes[$obj->prop("type")] . ": " : "").$obj->name();
			}


//faksid
		$tp = "RELTYPE_TELEFAX";
		if ($o->class_id() == CL_CRM_PERSON)
		{
			$tp = "RELTYPE_FAX";
		}

		$conns = $o->connections_from(array(
			"type" => $tp,
		));

		foreach($conns as $conn)
		{
			$obj = $conn->to();
			$address_row.= html::linebreak().t("faks").": ".$obj->name();
		}
/*
Viadukti 42
11313, Tallinn
Harjumaa, Eesti
http://www.espak.ee
üldkontakt: info@espak.ee
tööl: 6512 301
tööl: 6512 333
faks: 6556 235
*/

			$tf->define_data(array(
				"seller_people" => join(html::linebreak() , $sp),
				"buyer_people" => join(html::linebreak() , $bp),
				"id" => $cro_oid,
				"name" => $name,
				"cutcopied" => !empty($_SESSION["awcb_customer_selection_clipboard"][$cro_oid]) ? self::CUTCOPIED_COLOUR : "",
				"classif1" => $classif1,
				"customer_rel_creator" => method_exists($o, "get_cust_rel_creator_name") ? $o->get_cust_rel_creator_name() : "n/a",///!!!! teha korda
				"reg_nr" => $o->is_a(crm_company_obj::CLID) ? $o->prop("reg_nr") : "",
				"address" => $address_row ,
				"ceo" => $ceo,
				"phone" => $phone,
				"fax" => $fax,
				"url" => $url,
				"email" => $mail,
				'rollid' => $o->class_id() == CL_CRM_CATEGORY ? "" : $roles,
				'client_manager' => $client_manager,
				"pop" => $o->class_id() == CL_CRM_CATEGORY ? "" : $pm,
				"customer_rel_order" => $customer_rel_order,
			));
		}

		if ($requested_category == $arr["obj_inst"]->id())
		{
			$this->layoutinfo["customer_list_container"]["area_caption"] =  sprintf(t("'%s' kategoriseerimata kliendid"), $arr["obj_inst"]->name());
		}
		elseif (!$requested_category)
		{ //TODO: nimi tabelile kui mitte kategooriate list
		}
		else
		{
			try
			{
				$requested_category = obj($requested_category, array(), crm_category_obj::CLID);
				$this->layoutinfo["customer_list_container"]["area_caption"] = sprintf(t("'%s' kliendid kategoorias '%s'"), $arr["obj_inst"]->name(), $requested_category->name());
			}
			catch (Exception $e)
			{
				$this->show_error_text("Vigane kategooria id '{$requested_category}'");
			}
		}

		// make pageselector.
		$tf->define_pageselector(array(
			"type"=>"lb",
			"records_per_page"=>100,
			"d_row_cnt" => $org_count,
		));
	}
	
	private function __format_party_name($cro, $party = "seller", $case = 0, $ucfirst = false)
	{
		if ($cro->prop("{$party}.class_id") == crm_company_obj::CLID)
		{
			if (object_loader::can("", $cro->prop("{$party}.ettevotlusvorm")))
			{
				$corpform = $cro->prop("{$party}.ettevotlusvorm.shortname") ? $cro->prop("{$party}.ettevotlusvorm.shortname") : $cro->prop("{$party}.ettevotlusvorm.name");
				switch ($case)
				{
					case 1:
						$corpform = t("%s-le", $corpform);
						break;
	
					case 2:
						$corpform = t("%s-lt", $corpform);
						break;
				}
			}
			else
			{
				switch ($case)
				{
					case 1:
						$corpform = t("organisatsioonile");
						break;
	
					case 2:
						$corpform = t("organisatsioonilt");
						break;
	
					default:
						$corpform = t("organisatsioon");
				}
			}
		}
		else
		{
			switch ($case)
			{
				case 1:
					$corpform = t("isikule");
					break;

				case 2:
					$corpform = t("isikult");
					break;

				default:
					$corpform = t("isik");
			}
		}
		
		$formatted_name = $corpform." ".$cro->prop("{$party}.name");
		return $ucfirst ? ucfirst($formatted_name) : $formatted_name;
	}

	private function get_person_data($id , $co,$role)
	{
		$ret = array();
		if(acl_base::can("view" ,$id))
		{
			$person = obj($id);
			if($person->class_id() == CL_USER)
			{
				$person = obj($person->get_person_for_user());
			}

			$company = obj($co);
			$professions = $company->is_a(crm_company_obj::CLID) ? $person->get_profession_names($company) : array();

			$ret[]= $person->name()." (".$role.(sizeof($professions) ? "," : "")." ".join(", " , $professions).")";

			$phone = $person->get_phone($co);
			if($phone) $ret[]= $phone;
			$email = $person->get_mail($co);
			if($email) $ret[]= $email;
		}
		return join(", ", $ret);
	}


	function _org_table_header($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		
		$t->define_chooser(array(
			"field" => "id",
			"name" => "cust_check"
		));
		
		foreach ($arr["obj_inst"]->get_customers_table_fields() as $field)
		{
			if (!empty($field["active"]))
			{
				$t->define_field($field);
			}
		}
	}

	function _get_role_html($arr)
	{
		extract($arr);
		$role_url = $this->mk_my_orb("change", array(
			"from_org" => $from_org,
			"to_org" => $to_org,
			"to_project" => isset($to_project) ?  $to_project :  null
		), "crm_role_manager");

		$roles = array();

		$iter = isset($rc_by_co[$to_org]) ? safe_array($rc_by_co[$to_org]) :  array();
		if (!empty($to_project))
		{
			$iter = isset($rc_by_co[$to_org][$to_project]) ? safe_array($rc_by_co[$to_org][$to_project]) : array();
		}

		foreach($iter as $r_p_id => $r_p_data)
		{
			try
			{
				$r_p_o = obj($r_p_id);
				$roles[] = html::get_change_url($r_p_o->id(), array(), parse_obj_name($r_p_o->name())).": ".join(",", $r_p_data);
			}
			catch (awex_obj $e)
			{
			}
		}
		$roles = join(html::linebreak(), $roles);

		$roles .= ($roles != "" ? html::linebreak() : "" ).html::popup(array(
			"url" => $role_url,
			'caption' => t('Rollid'),
			"width" => 800,
			"height" => 600,
			"scrollbars" => "auto"
		));
		return $roles;
	}

	function _get_customer_responsible_tree($arr)
	{
		$tree_inst = $arr["prop"]["vcl_inst"];
		$reset_tree_params_url = aw_url_change_var("cmgr", null, aw_url_change_var("pmgr", null));
		$tree_inst->add_item(0, array(
			"id" => "people_who_are_responsible_for_that_s__t",
			"name" => t("Vastutajad"),
			"url" => $reset_tree_params_url
		));

		$tree_inst->add_item("people_who_are_responsible_for_that_s__t", array(
			"id" => "cmgr",
			"name" => t("Kliendihaldurid"),
			"url" => $reset_tree_params_url
		));
		$tree_inst->add_item("people_who_are_responsible_for_that_s__t", array(
			"id" => "pmgr",
			"name" => t("Projektijuhid"),
			"url" => $reset_tree_params_url
		));

		$ol = new object_data_list(array(
				"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
				"seller" => $arr["obj_inst"]->prop("company")
			),
			array(
				CL_CRM_COMPANY_CUSTOMER_DATA => array(new obj_sql_func(OBJ_SQL_UNIQUE, "client_manager", "client_manager"))
			)
		);
		$tmp = $ol->arr();
		$ids = array(-1);
		foreach($tmp as $item)
		{
			if ($this->can("view", $item["client_manager"]))
			{
				$ids[] = $item["client_manager"];
			}
		}
		$ol = new object_list(array(
			"oid" => $ids
		));
		$nms = $ol->names();
		foreach($nms as $id => $nm)
		{
			$tree_inst->add_item("cmgr", array(
				"id" => "cmgr_".$id,
				"name" => parse_obj_name($nm),
				"url" => aw_url_change_var("cmgr", $id,aw_url_change_var("pmgr", null)),
				"iconurl" => icons::get_icon_url(CL_CRM_PERSON)
			));
		}

		if (!empty($arr["request"]["cmgr"]))
		{
			$tree_inst->set_selected_item("cmgr_".$arr["request"]["cmgr"]);
		}
/* projektijuhid*/
		$ol = new object_data_list(array(
				"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
				"seller" => $arr["obj_inst"]->prop("company")
			),
			array(
				CL_CRM_COMPANY_CUSTOMER_DATA => array(new obj_sql_func(OBJ_SQL_UNIQUE, "salesman", "salesman"))
			)
		);

		$tmp = $ol->arr();
		$ids = array(-1);
		foreach($tmp as $item)
		{
			if ($this->can("view", $item["salesman"]))
			{
				$ids[] = $item["salesman"];
			}
		}
		$ol = new object_list(array(
			"oid" => $ids
		));
		$nms = $ol->names();
		foreach($nms as $id => $nm)
		{
			$tree_inst->add_item("pmgr", array(
				"id" => "pmgr_".$id,
				"name" => parse_obj_name($nm),
				"url" => aw_url_change_var("pmgr", $id, aw_url_change_var("cmgr", null)),
				"iconurl" => icons::get_icon_url(CL_CRM_PERSON)
			));
		}
		if (!empty($arr["request"]["pmgr"]))
		{
			$tree_inst->set_selected_item("pmgr_".$arr["request"]["pmgr"]);
		}
	}

	function _get_customer_areas_tree($arr)
	{
		return self::PROP_IGNORE;
		
		$tree_inst = $arr["prop"]["vcl_inst"];

		$tree_inst->add_item(0, array(
			"id" => "areas",
			"name" => t("Piirkonnad"),
			"url" => aw_url_change_var("area", null)
		));

		$ol = new object_list(array(
			"class_id" => CL_CRM_AREA
		));

		foreach($ol->arr() as $o)
		{
			$tree_inst->add_item("areas", array(
				"id" => $o->id(),
				"name" => $o->name(),
				"url" => aw_url_change_var("area", $o->id())
			));
		}

		if (!empty($arr["request"]["area"]))
		{
			$tree_inst->set_selected_item($arr["request"]["area"]);
		}
	}
	
	function callback_generate_scripts($arr)
	{
		if ("customers" === $this->use_group) {
			active_page_data::load_stylesheet("js/bootstrap/css/bootstrap.datepicker.css");
			active_page_data::load_stylesheet("js/bootstrap/css/bootstrap.min.css");
			active_page_data::load_javascript("bootstrap/js/bootstrap.min.js");
			active_page_data::load_javascript("bootstrap/js/bootstrap.datepicker.js");
			active_page_data::load_javascript("knockout/knockout-2.2.0.js");
			active_page_data::load_javascript("knockout/ko.datepick.js");
			active_page_data::add_javascript("var initialize = setInterval(function(){if(typeof AW !== 'undefined'){ clearInterval(initialize); AW.UI.crm_customer_view.initialize_modals(" . $arr["obj_inst"]->prop("company") . "); }}, 100);", "bottom");
		}

		active_page_data::load_javascript("reload_properties_layouts.js");
		active_page_data::load_javascript("applications/crm/crm_customer_view.js");
	}
}
