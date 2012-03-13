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
@default group=relorg_s,relorg_b

	@property my_customers_toolbar type=toolbar no_caption=1 store=no
	@caption Kliendivaate tegevused

	@layout my_cust_bot type=hbox width=20%:80%
		@layout tree_search_split type=vbox parent=my_cust_bot

			@property tree_search_split_dummy type=hidden no_caption=1 parent=tree_search_split

			@layout vvoc_customers_tree_left type=vbox parent=tree_search_split closeable=1 area_caption=Kliendivalik
				@property customer_listing_tree type=treeview no_caption=1 parent=vvoc_customers_tree_left
				@caption Kliendikategooriad hierarhiliselt

			@layout vbox_customers_left type=vbox parent=tree_search_split closeable=1 area_caption=Otsing
				@layout vbox_customers_left_top type=vbox parent=vbox_customers_left

					@property cs_n type=textbox size=30 store=no parent=vbox_customers_left_top captionside=top
					@caption Nimi

					@property customer_search_reg type=textbox size=30 store=no parent=vbox_customers_left_top captionside=top
					@caption Reg nr.

					@property customer_search_address type=textbox size=30 store=no parent=vbox_customers_left_top captionside=top
					@caption Aadress

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
	// @groupinfo relorg_t caption="K&otilde;ik" parent=relorg submit=no submit=no
	@groupinfo relorg_b caption="Ostjad" focus=cs_n parent=relorg submit=no
	@groupinfo relorg_s caption="M&uuml;&uuml;jad" focus=cs_n parent=relorg submit=no


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

		$this->search_props = array("cs_n","customer_search_reg","customer_search_address","customer_search_city","customer_search_county");

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
			/// CUSTOMER tab
			case "my_projects":
			case "customer_rel_creator":
			case "customer_search_cust_mgr":
			case "customer_search_is_co":
			case "my_customers_toolbar":
			case "my_customers_table":
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

}
