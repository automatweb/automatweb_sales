<?php

class shop_order_center_obj extends _int_object
{
	const CLID = 314;


	public function save($exclusive = false, $previous_state = null)
	{
		$do_new_shop_stuff = is_oid($this->id()) ? 0 : 1;
		$r =  parent::save($exclusive, $previous_state);

		if($do_new_shop_stuff)
		{
			$this->do_new_shop_stuff();

		}
		return $r;
	}

	private function do_new_shop_stuff()
	{
		//ostukorvi on alati vaja
		$cart = new object();
		$cart->set_class_id(CL_SHOP_ORDER_CART);
		$cart->set_name($this->name() . " " . t("ostukorv"));
		$cart->set_parent($this->id());
		$cart->save();
		$this->set_prop("cart" , $cart->id());

		//pangamakse objekt ka suht populaarne
		$bp = new object();
		$bp->set_class_id(CL_BANK_PAYMENT);
		$bp->set_name($this->name() . " " . t("pangamakse"));
		$bp->set_parent($this->id());
		$bp->save();
		$this->set_prop("bank_payment" , $bp->id());

		//maili v6iks ka kohe saata
		$this->set_prop("mail_to_client" , 1);

		$warehouses = new object_list(array(
			"class_id" => CL_SHOP_WAREHOUSE,
			"site_id" => array(),
			"lang_id" => array(),
		));
		$warehouse = $warehouses->begin();
		if(!is_object($warehouse))
		{
			$warehouse = new object();
			$warehouse->set_class_id(CL_SHOP_WAREHOUSE);
			$warehouse->set_name($this->name() . " " . t("ladu"));
			$warehouse->set_parent($this->parent());
			$warehouse->save();
		}
		$this->set_prop("product_type" , CL_SHOP_PRODUCT);
		$this->set_prop("warehouse" , $warehouse->id());
		$this->save();
	}

	/**
		@attrib api=1

		@param sum required type=float
			The total sum of products/packagings
		@param currency required type=int
			The OID of currency
		@param product optional type=array/int acl=view
			OIDs of products
		@param product_packaging optional type=array/int acl=view
			OIDs of products
		@param product_category optional type=array/int acl=view
			OIDs of product categories
		@param customer_data optional type=int acl=view
			OID of customer_data object
		@param customer_category optional type=array/int acl=view
			OIDs of customer categories.
		@param location optional type=array/int acl=view
			OIDs of locations

		@returns OID of shop_payment_type object or NULL if none found
	**/
	public function get_rent_conditions($arr)
	{
		$customer_data = $this->get_customer_data();
		if(is_object($customer_data))
		{
			$arr = array_merge(array(
				"customer_data" => $customer_data->id(),
				"customer_category" => array(),//$customer_data->get_customer_categories()->ids(),
				"location" => array(),//$customer_data->get_locations()->ids(),
			), $arr);
		}
		return is_oid($id = $this->prop("shop_payment_type")) ? obj($id)->valid_conditions($arr) : NULL;
	}

	/**
		@attrib api=1

		@param sum optional type=float
			The total sum of products/packagings
		@param currency optional type=int
			The OID of currency
		@param product optional type=array/int acl=view
			OIDs of products
		@param product_packaging optional type=array/int acl=view
			OIDs of products
		@param product_category optional type=array/int acl=view
			OIDs of product categories
		@param customer_data optional type=int acl=view
			OID of customer_data object
		@param customer_category optional type=array/int acl=view
			OIDs of customer categories.
		@param location optional type=array/int acl=view
			OIDs of locations
		@param validate optional type=boolean default=true

		@returns object_list of valid shop_payment_type objects
	**/
	public function get_payment_types($arr = array())
	{
		$ol = new object_list(array(
			"class_id" => CL_SHOP_PAYMENT_TYPE,
			"shop" => $this->id(),
			"lang_id" => array(),
			"site_id" => array(),
		));
		if((!isset($arr["validate"]) || !empty($arr["validate"])) && !empty($arr["sum"]))
		{
			foreach($ol->arr() as $o)
			{
				if(!is_oid($o->valid_conditions($arr)))
				{
					$ol->remove($o->id());
				}
			}
		}
		return $ol;
	}

	function filter_get_fields()
	{
		$class_filter_fields = $this->meta("class_filter_fields");
		$prop_filter_fields = $this->meta("prop_filter_fields");

		$rv = array();
		$ic_inst = $this->get_integration_class_instance();
		$ic_fields = $is_inst ? $ic_inst->get_filterable_fields() : array();
		foreach(safe_array($class_filter_fields) as $field_name => $one)
		{
			if ($one == 1)
			{
				$rv["ic::".$field_name] = $ic_fields[$field_name];
			}
		}

		$prod_props = obj()->set_class_id(CL_SHOP_PRODUCT)->get_property_list();
		foreach(safe_array($prop_filter_fields) as $field_name => $one)
		{
			if ($one == 1)
			{
				$rv["prod::".$field_name] = $prod_props[$field_name]["caption"];
			}
		}
		return $rv;
	}

	function filter_get_all_values($filter_name)
	{
		list($type, $field_name) = explode("::",$filter_name);

		if ($type === "ic")
		{
			$ic_inst = $this->get_integration_class_instance();
			return $ic_inst ? $ic_inst->get_all_filter_values($field_name) : array();
		}
		elseif ($type === "prod")
		{
			$rv = array();
			$odl = new object_data_list(
				array(
					"class_id" => CL_SHOP_PRODUCT,
					"price" => new obj_predicate_not(-1)//see ainult selleks, et toodete tabeli sisse loeks
				),
				array(
					CL_SHOP_PRODUCT => array(new obj_sql_func(OBJ_SQL_UNIQUE, "value", $field_name))
				)
			);
			foreach($odl->arr() as $od)
			{
				$rv[$od["value"]] = $od["value"];
			}
			return $rv;
		}

		return array();
	}

	function get_integration_class_instance()
	{
		if (!is_class_id($ic = $this->prop("integration_class")))
		{
			return null;
		}

		return get_instance(aw_ini_get("classes.{$ic}.file"));
	}

	function filter_set_active_by_folder($data)
	{
		$this->set_meta("filter_by_folder", $data);
	}

	function filter_get_active_by_folder($folder_id)
	{
		$fbf = safe_array($this->meta("filter_by_folder"));
		if (is_oid($fbf[$folder_id]) && $GLOBALS["object_loader"]->cache->can("view", $fbf[$folder_id]))
		{
			return $fbf[$folder_id];
		}
		foreach(obj($folder_id)->path(array("full_path" => 1)) as $path_item)
		{
			if (is_oid($fbf[$path_item->id()]) && $GLOBALS["object_loader"]->cache->can("view", $fbf[$path_item->id()]))
			{
				return $fbf[$path_item->id()];
			}
		}
		return null;
	}

	/**
		@attrib api=1
	**/
	public function get_cart()
	{
		if($this->prop("cart_type") == 1 && aw_global_get("uid") != "")
		{
			$user = obj(aw_global_get("uid_oid"));
			// well, it would be wise to syncronize the session aswell...
			$_SESSION["cart"] = $user->meta("shop_cart");
			return $user->meta("shop_cart");
		}
		else
		{
			return ifset($_SESSION, "cart");
		}
	}

	/**
		@attrib api=1
	**/
	public function set_cart($arr)
	{
		if($this->prop("cart_type") == 1 && aw_global_get("uid") != "")
		{
			$user = obj(aw_global_get("uid_oid"));
			$user->set_meta("shop_cart", $arr["cart"]);
			$user->save();
		}
		$_SESSION["cart"] = $arr["cart"];

	}


	/**
		@comment
	**/
	public function get_customer_data()
	{
		if(!is_oid($this->prop("warehouse.conf.owner")))
		{
			return FALSE;
		}
		else
		{
			$ol = new object_list(array(
				"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
				"buyer" => array(user::get_current_company(), user::get_current_person()),
				"seller" => $this->prop("warehouse.conf.owner"),
				"lang_id" => array(),
				"site_id" => array(),
				new obj_predicate_limit(1),
			));
			return $ol->count() > 0 ? $ol->begin() : FALSE;
		}
	}

	public function get_product_show_obj($menu, $make_new = false)
	{
		$o = "";
		$docs = new object_list(array(
			"class_id" => CL_DOCUMENT,
			"parent" => $menu,
			"lang_id" => array(),
		));
		$doc = $docs->count() ? $docs->begin() : "";
		if(!is_object($doc) && $make_new)
		{
			$doc = new object();
			$doc->set_class_id(CL_DOCUMENT);
			$doc->set_parent($menu);
			$doc->set_name($menu);
			$doc->set_status(2);
			$doc->save();
		}
		if(is_object($doc))
		{
			foreach($doc->connections_from(array("to.class_id" => CL_PRODUCTS_SHOW)) as $c)
			{
				$o = $c->to();
				break;
			}
			if(!is_object($o) && $make_new)
			{
				$o = new object();
				$o->set_class_id(CL_PRODUCTS_SHOW);
				$o->set_parent($menu);
				$o->set_name($menu." ".t("toodete n&auml;itamine"));
				$o->set_prop("oc" , $this->id());
				$o->save();
				$doc->set_prop("content" , $doc->prop("content")."#show_products1#");
				$doc->save();
				$doc->connect(array(
					"type" => "RELTYPE_ALIAS",
					"to" => $o->id(),
				));
			}
		}
		return $o;
	}

	public function send_confirm_mail($order , $mail_data = array())
	{
		$order_inst = get_instance(CL_SHOP_SELL_ORDER);

		$html_params = array(
			"id" => $order,
		);
		if(!empty($mail_data["template"]))
		{
			$html_params["template"] = $mail_data["template"];
		}
		elseif($this->prop("mail_template"))
		{
			$html_params["template"] = $this->prop("mail_template");
		}
		$html = $order_inst->show($html_params);
		$order_object = obj($order);
		$warehouse = $this->prop("warehouse");
		$wo = obj($warehouse);

		$email_subj = t("Tellimus laost");
		$mail_from_addr = "automatweb@automatweb.com";
		$mail_from_name = str_replace("http://", "", aw_ini_get("baseurl"));
		if ($GLOBALS["object_loader"]->cache->can("view", $this->prop("cart")))
		{
			$cart_o = obj($this->prop("cart"));
			if ($cart_o->prop("email_subj") != "")
			{
				$email_subj = $cart_o->prop("email_subj");
			}
			if($GLOBALS["object_loader"]->cache->can("view", $cart_o->prop("subject_handler")))
			{
				$ctr = get_instance(CL_FORM_CONTROLLER);
				$email_subj = $ctr->eval_controller_ref($cart_o->prop("subject_handler"), NULL, $cart_o, $order);
			}
		}

		if(!empty($mail_data["from_address"]))
		{
			$mail_from_addr = $mail_data["from_address"];
		}
		elseif ($this->prop("mail_from_addr"))
		{
			$mail_from_addr = $this->prop("mail_from_addr");
		}

		if(!empty($mail_data["from_name"]))
		{
			$mail_from_name = $mail_data["from_name"];
		}
		elseif ($this->prop("mail_from_name"))
		{
			$mail_from_name = $this->prop("mail_from_name");
		}

		if(!empty($mail_data["subject"]))
		{
			$email_subj= $mail_data["subject"];
		}


		$order_mails = $wo->get_order_mails() + $this->get_order_mails();
		if($this->prop("mail_to_client"))
		{
			$order_mails[$order_object->get_orderer_mail()] = $order_object->get_orderer_mail();
		}
		if (count($order_mails) > 0)
		{
			$awm = get_instance("protocols/mail/aw_mail");
			foreach($order_mails as $mail)
			{
				$awm->clean();
				$awm->create_message(array(
					"froma" => $mail_from_addr,
					"fromn" => $mail_from_name,
					"subject" => $email_subj,
					"to" => $mail,
					"body" => t("see on html kiri"),
				));
				$awm->htmlbodyattach(array(
					"data" => $html,
				));
				$awm->gen_mail();
			}
		}
	}

	/**
		@attrib api=1
	**/
	public function get_currency()
	{
		if(is_oid($this->prop("default_currency")))
		{
			return $this->prop("default_currency");
		}
		$ol = new object_list(array("class_id" => CL_CURRENCY));
		foreach($ol->ids() as $id)
		{
			return $id;
		}
		return null;
	}

	private function get_all_product_show_menus()
	{
		$menus = array();
		$roots = $this->prop("root_menu");
		foreach($roots as $root_menu)
		{
			$ot = new object_tree(array(
				"class_id" => CL_MENU,
				"parent" => $root_menu,
			));
			$menus = $menus + $ot->ids();
		}
		return $menus;
	}

	/** makes all menus used to show products inactive if there is no products
		@attrib api=1
	**/
	public function make_all_empty_menus_not_active()
	{
		$menus = $this->get_all_product_show_menus();
		$inactive_menus = array();
		$active_menus = array();
		arr("kokku men&uuml;&uuml;sid : ".sizeof($menus));flush();
		foreach($menus as $key => $menu_id)
		{
			arr("kontrollib men&uuml;&uuml;d idga : ".$menu_id);flush();
			if(in_array($menu_id , $inactive_menus) || in_array($menu_id , $active_menus))
			{
				continue;
			}
			$show_object = $this->get_product_show_obj($menu_id);
			if(is_object($show_object))
			{
				$items = $show_object->get_web_items();
				if($items->count())
				{
					$active_menus[$menu_id] = $menu_id;
					continue;
				}
			}
			if(in_array($menu_id , $active_menus))
			{
				continue;
			}

			$ot = new object_tree(array(
				"class_id" => CL_MENU,
				"parent" => $menu_id,
			));
			foreach($ot->ids() as $id)
			{
				if(in_array($id , $active_menus))
				{
					$active_menus[$menu_id] = $menu_id;
					break;
				}
				$show_object = $this->get_product_show_obj($id);
				if(is_object($show_object))
				{
					$items = $show_object->get_web_items();
					if($items->count())
					{
						$active_menus[$menu_id] = $menu_id;
						$active_menus[$id] = $id;
						break;
					}
				}
			}
			if(!in_array($menu_id , $active_menus))
			{
				$inactive_menus[$menu_id] = $menu_id;
				foreach($ot->ids() as $id)
				{
					$inactive_menus[$id] = $id;
				}
			}
		}
		arr("kokku mitteaktiivseid men&uuml;&uuml;sid : ".sizeof($inactive_menus));
		print "<br>";
		print "teeb mitteaktiivseks:<br>";
		if(sizeof($inactive_menus))
		{
			$ol = new object_list();
			$ol->add($inactive_menus);
			foreach($ol->arr() as $inactive)
			{
				print "ID: ".$inactive->id()." , Nimi: ".$inactive->name()."<br>";
				$inactive->set_prop("status" , 1);
//				$inactive->save();
			}
		}
	}


	/**
		@attrib name=make_new_struct api=1
	**/
	public function make_new_struct($arr)
	{
		$warehouse = obj($this->prop("warehouse"));
		$root_menus = $this->prop("root_menu");
		$root = reset($root_menus);
		$cats = $warehouse->get_root_categories();

		if($cats->count())
		{
			$this->_make_new_struct_leaf($cats , $root);
		}
	}

	private function _make_new_struct_leaf($cats , $root)
	{
		$ol = new object_list(array(
			"class_id" => CL_MENU,
			"site_id" => array(),
			"lang_id" => array(),
			"parent" => $root));

		foreach($cats->arr() as $cat)
		{
			$menu_found = 0;
			foreach($ol->arr() as $menu)
			{
				if($menu->name() == $cat->name())
				{
					$menu_found = 1;
					break;
				}
			}
			if(!$menu_found)
			{
				$menu = new object();
				$menu->set_class_id(CL_MENU);
				$menu->set_parent($root);
				$menu->set_name($cat->name());
				$menu->save();
			}
			$menu->set_prop("status" , 2);
			$menu->save();
			$o = $this->get_product_show_obj($menu->id(), true);
			$o->add_category($cat->get_all_categories());
			$o->set_prop("type" , $this->prop("product_type"));
			$o->save();

			$categories = $cat->get_categories();
			if($categories->count())
			{
				$this->_make_new_struct_leaf($categories , $menu->id());
			}
		}
	}

	private function  __orderer_vars_sorter($a, $b)
	{
		if ($this->orderer_vars_meta["jrk"][$a] == $this->orderer_vars_meta["jrk"][$b])
		{
			return 0;
		}
		return ($this->orderer_vars_meta["jrk"][$a] < $this->orderer_vars_meta["jrk"][$b]) ? -1 : 1;
	}

	public function get_orderer_vars(&$cart_instance)
	{
		$orderer_vars = $cart_instance->orderer_vars;
		$this->orderer_vars_meta = $this->meta("orderer_vars");
		uksort($orderer_vars, array(&$this, "__orderer_vars_sorter"));
		return $orderer_vars;
	}

	public function get_bank_payment_id()
	{
		$bp = $this->get_first_obj_by_reltype("RELTYPE_BANK_PAYMENT");
		if(!is_object($bp))
		{
			if($this->prop("use_bank_payment"))
			{
				$bp = new object();
				$bp->set_class_id(CL_BANK_PAYMENT);
				$bp->set_name($this->name() . " " . t("pangamakse"));
				$bp->set_parent($this->id());
				$bp->save();
				$this->set_prop("bank_payment" , $bp->id());
				$this->save();
			}
			else return null;
		}
		return $bp->id();
	}

	private function get_order_mails()
	{
		$ret = array();
		foreach($this->connections_from(array(
			"type" => "RELTYPE_MAIL_RECIEVERS",
	//		"sort_by_num" => "to.jrk"
		)) as $c)
		{
			$o = $c->to();
			switch($o->class_id())
			{
				case CL_CRM_PERSON:
					$ret[$o->prop("mail")] = $o->get_mail();
					break;
				case CL_ML_MEMBER:
					$ret[$o->prop("mail")] = $o->prop("mail");
					break;
			}
			break;
		}


		return $ret;
	}

	public function get_active_products_count()
	{//CL_SHOP_PRODUCT_PACKAGE.RELTYPE_CATEGORY.
		$GLOBALS["SLOW_DUKE"] = 1;

		$t = new object_data_list(
			array(
				"class_id" => CL_SHOP_PACKET,
				"site_id" => array(),
				"lang_id" => array(),
				"status" => 2,
//				"CL_SHOP_PACKET.RELTYPE_CATEGORY.RELTYPE_CATEGORY(CL_PRODUCTS_SHOW)" => new obj_predicate_compare(OBJ_COMP_GREATER, 0),
			),
			array(
				CL_SHOP_PACKET =>  array(new obj_sql_func(OBJ_SQL_COUNT,"cnt" , "*"))
			)
		);

		$cnt = $t->get_element_from_all("cnt");
		return reset($cnt);

	}

	public function get_bonus_codes()
	{
		$bonus_codes = array();

		$data = $this->meta("bonus_codes");
		if(is_array($data))
		{
			foreach($data as $code => $products)
			{
				$bonus_codes[$code] = $products;
			}
		}

		return $bonus_codes;
	}

	public function set_bonus_codes($bonus_codes)
	{
		$this->set_meta("bonus_codes", $bonus_codes);
	}
}
