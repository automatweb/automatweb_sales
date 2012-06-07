<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_crm_customer_view master_index=brother_of master_table=objects index=aw_oid

@default table=objects
@default method=serialize
@default field=meta
@default group=general

@property company type=relpicker reltype=RELTYPE_COMPANY
@caption Ettev&otilde;te



// Customers view
@default group=relorg

	@property my_customers_toolbar type=toolbar no_caption=1 store=no
	@caption Kliendivaate tegevused

	@layout my_cust_bot type=hbox width=20%:80%
		@layout tree_search_split type=vbox parent=my_cust_bot

			@property tree_search_split_dummy type=hidden no_caption=1 parent=tree_search_split

			@layout vvoc_customers_tree_left type=vbox parent=tree_search_split closeable=1 area_caption=Kliendivalik
				@property customer_listing_tree type=treeview no_caption=1 parent=vvoc_customers_tree_left
				@caption Kliendikategooriad hierarhiliselt

			@layout customers_tree_responsible type=vbox parent=tree_search_split closeable=1 area_caption=Osapooled
				@property customer_responsible_tree type=treeview no_caption=1 parent=customers_tree_responsible
				@caption Kliendikategooriad Vastutajate puu

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

		@property cts_status type=select parent=vbox_customers_left_top store=no captionside=top
		@caption Staatus

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


/// end of customers


@groupinfo relorg caption="Kliendid" focus=cs_n submit=no

@reltype COMPANY value=1 clid=CL_CRM_COMPANY
@caption Ettev&otilde;te


*/

class crm_customer_view extends class_base
{
	const REQVAR_CATEGORY = "cs_c"; // request parameter name for customer category

	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_customer_view",
			"clid" => crm_customer_view_obj::CLID
		));

		$this->search_props = array("cs_n","customer_search_reg","customer_search_address","customer_search_city","customer_search_county" , "cts_phone" , "cts_salesman" , "cts_calls" , "cts_lead_source" , "cts_address" , "cts_status", "cts_contact","cts_comment","cts_cat");

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
		if ("cts_status" === $data["name"])
		{ // set search status selection options
			$arr["prop"]["options"] = array("" => "") + crm_company_customer_data_obj::sales_state_names();
		}
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
			case "my_customers_toolbar":
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
				if(!$arr["obj_inst"]->prop("company"))
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
		$o = obj($arr["request"]["id"]);
		if($o->prop("company"))
		{
			return "relorg";
		}
		else
		{
			return "general";
		}
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
		if("relorg" === substr($this->use_group, 0, 6))
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
		aw_session::set("awcb_category_old_parent", (isset($arr["cs_c"]) ? $arr["cs_c"] : ""));
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
		aw_session::set("awcb_category_old_parent", (isset($arr["cs_c"]) ? $arr["cs_c"] : ""));
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
				$new_parent = empty($arr["cs_c"]) ? $arr[self::REQVAR_CATEGORY] : $arr["cs_c"];
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
		if (is_array($arr["cust_check"]) and count($arr["cust_check"]) and is_oid($arr[self::REQVAR_CATEGORY]))
		{
			$errors = array();
			try
			{
				$category = obj($arr[self::REQVAR_CATEGORY]);

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
		@attrib name=remove_cust_relations all_args=1
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

	function _get_my_customers_table(&$arr)
	{ // lists customers, filters by search parameters
		$company = obj($arr['obj_inst']->prop("company"));
		$customer_relations_list = array();


		foreach(array("seller" , "buyer") as $company_action)
		{
			$customer_relations_search = new crm_sales_contacts_search();
			$customer_relations_search->$company_action = $company;
/*		if ("relorg_s" === $this->use_group)
		{ // list sellers
			$customer_relations_search->buyer = $arr['obj_inst'];
		}
		elseif ("relorg_b" === $this->use_group)
		{ // list buyers
			$customer_relations_search->seller = $company;
		}
*/

		
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
		if (!empty($arr["request"]["cmgr"]))
		{
			$customer_relations_search->customer_manager = $arr["request"]["cmgr"];
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
			if (!empty($arr["request"]["cts_status"]))
			{
				$customer_relations_search->status = (int)$arr["request"]["cts_status"];
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

			if (!empty($arr["request"][crm_company::REQVAR_CATEGORY]))
			{
				try
				{
					$category = new object($arr["request"][crm_company::REQVAR_CATEGORY]);
				}
				catch (Exception $e)
				{
					//XXX: pole vist vaja. veatolerantne.
					// $this->show_error_text(t("Kategooria parameeter ei vasta n&otilde;uetele"));
				}

				if ($category->is_a(crm_category_obj::CLID))
				{
					$customer_relations_search->category = $category;
				}
				elseif ($category->is_a(crm_company_obj::CLID))
				{
					$customer_relations_search->category = null;
				}
				else
				{
				}
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
		$requested_category = isset($arr["request"][crm_company::REQVAR_CATEGORY]) ? $arr["request"][crm_company::REQVAR_CATEGORY] : null;

		$this->_org_table_header($tf);
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
							crm_company::REQVAR_CATEGORY => $role_entry->prop("role")
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
				if (($default_cfg or in_array("url", $visible_fields)) and ($this->can("view", $o->prop("url_id"))))
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
					$phones = $o->get_phones();
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
			if (($default_cfg or  in_array("fax", $visible_fields)) and object_loader::can("view", $o->prop("telefax_id")))
			{
				$fax = obj($o->prop("telefax_id"));
				$fax = $fax->name();
			}

			# client_manager
			if ($default_cfg or in_array("client_manager", $visible_fields))
			{
				$client_manager = html::obj_change_url($o->prop("client_manager"));
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

				foreach($idx_cro_by_customer[$o->id()] as $cro)
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
				$name = html::span(array(
					"nowrap" => true,
					"content" => icons::get_class_icon($o->class_id()) . html::space() . html::get_change_url($o->id(), array("return_url" => get_ru()), ($o->name() ? $o->name() : t("[Nimetu]")) . $vorm
				)));

				if ($o->is_a(crm_company_obj::CLID))
				{
					$_url = $this->mk_my_orb("get_cust_contact_table", array("id" => $o->id(), "return_url" => post_ru()));
					$name .= html::href(array(
						"url" => "javascript:void(0)",
						"id" => "tnr" . $o->id(),
						"caption" => t("(Kontaktid)"),
						"onclick" => "co_contact(" . $o->id() . ",\"{$_url}\");"
					));
				}
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

			$cro_oid = 0;
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

			$customer_rel_order = "";

			foreach($idx_cro_by_customer[$o->id()] as $cro)
			{
				$customer_rel_order.= html::href(array(
					"caption" => $cro->prop("buyer") == $org->id() ? "M" : "O",
					"url" => html::get_change_url($cro, array("return_url" => get_ru()))
				));
			}



			//TODO: define and get data only for fields configured to be shown in current crm settings.
			$tf->define_data(array(
				"id" => $cro_oid,
				"name" => $name,
				"cutcopied" => !empty($_SESSION["awcb_customer_selection_clipboard"][$cro_oid]) ? self::CUTCOPIED_COLOUR : "",
				"classif1" => $classif1,
				"customer_rel_creator" => method_exists($o, "get_cust_rel_creator_name") ? $o->get_cust_rel_creator_name() : "n/a",///!!!! teha korda
				"reg_nr" => $o->prop("reg_nr"),
				"address" => $o->class_id() == crm_company_obj::CLID ? $o->prop_str("contact") : $o->prop("RELTYPE_ADDRESS.name"),
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


	function _org_table_header($tf)
	{
		$tf->define_field(array(
			"name" => "pop",
			"caption" => t("&nbsp;")
		));

		$tf->define_field(array(
			"name" => "name",
			"caption" => t("Kliendi nimi"),
			"chgbgcolor" => "cutcopied",
			"sortable" => 1
		));

		$tf->define_field(array(
			"name" => "address",
			"chgbgcolor" => "cutcopied",
			"caption" => t("Aadress")
		));

		$tf->define_field(array(
			"name" => "email",
			"caption" => t("Kontakt"),
			"chgbgcolor" => "cutcopied",
			"align" => "center"
		));

		$tf->define_field(array(
			"name" => "url",
			"chgbgcolor" => "cutcopied",
			"caption" => t("WWW")
		));

		$tf->define_field(array(
			"name" => "phone",
			"chgbgcolor" => "cutcopied",
			"caption" => t('Telefon')
		));

		$tf->define_field(array(
			"name" => "fax",
			"chgbgcolor" => "cutcopied",
			"caption" => t('Faks')
		));

		$tf->define_field(array(
			"name" => "ceo",
			"chgbgcolor" => "cutcopied",
			"caption" => t("Juht")
		));

		$tf->define_field(array(
			"name" => "rollid",
			"chgbgcolor" => "cutcopied",
			"caption" => t("Rollid")
		));

		$tf->define_field(array(
			"name" => "client_manager",
			"chgbgcolor" => "cutcopied",
			"caption" => t("Kliendihaldur"),
			"sortable" => 1,
		));

		$tf->define_field(array(
			"name" => "customer_rel_creator",
			"chgbgcolor" => "cutcopied",
			"caption" => t("Kliendisuhte looja"),
			"sortable" => 1
		));

		$tf->define_field(array(
			"name" => "customer_rel_order",
			"chgbgcolor" => "cutcopied",
			"caption" => t("Suund"),
			"sortable" => 1
		));

		$tf->define_chooser(array(
			"field" => "id",
			"name" => "cust_check"
		));
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

}
