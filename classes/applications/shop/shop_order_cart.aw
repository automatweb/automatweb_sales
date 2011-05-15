<?php

// shop_order_cart.aw - Poe ostukorv
/*

@classinfo syslog_type=ST_SHOP_ORDER_CART relationmgr=yes prop_cb=1 no_status=1 maintainer=kristo

@default table=objects
@default group=general

	@property prod_layout type=relpicker reltype=RELTYPE_PROD_LAYOUT field=meta method=serialize
	@caption Kujundus, mida korvis kasutatakse

	@property email_subj type=textbox field=meta method=serialize
	@caption Tellimuse e-maili subjekt

	@property postal_price type=textbox field=meta method=serialize size=5
	@caption Vaikimisi postikulu

	@property subject_handler type=relpicker reltype=RELTYPE_CONTROLLER field=meta method=serialize
	@caption Subjekti kontroller

	@property update_handler type=relpicker reltype=RELTYPE_CONTROLLER field=meta method=serialize
	@caption Korvi uuendamise kontroller

	@property finish_handler type=relpicker reltype=RELTYPE_CONTROLLER field=meta method=serialize
	@caption Tellimise kontroller ($form_ref - ostukorvi instants , $entry - loodud tellimus)

	@property order_show_controller type=relpicker reltype=RELTYPE_CONTROLLER field=meta method=serialize
	@caption Tellimuse n&auml;itamise kontroller
   @property show_only_valid_delivery_methods type=checkbox field=meta method=serialize 	 
	         @caption N&auml;ita checkout'is ainult valiidseid k&auml;ttetoimetamise viise 	 
	  	 
	         @property show_only_valid_payment_types type=checkbox field=meta method=serialize 	 
	         @caption N&auml;ita checkout'is ainult valiidseid makseviise 	 
	 
@property product_template type=select field=meta method=serialize
@caption Ostukorvi &uuml;he toote vaate templeit

@property orderer_data_template type=select field=meta method=serialize
@caption Ostukorvi kasutaja andmete templeit

@property channel type=relpicker reltype=RELTYPE_CHANNEL store=connect
@caption M&uuml;&uuml;gikanal

@groupinfo delivery_methods caption="K&auml;ttetoimetamise viisid"
@default group=delivery_methods

	@property delivery_method_add type=hidden table=objects field=meta method=serialize
	@property delivery_method_tlb type=toolbar store=no no_caption=1
	@property delivery_method_tbl type=table store=no no_caption=1



### RELTYPES

@reltype PROD_LAYOUT value=1 clid=CL_SHOP_PRODUCT_LAYOUT
@caption toote kujundus

@reltype CONTROLLER value=2 clid=CL_FORM_CONTROLLER
@caption kontroller

@reltype DELIVERY_METHOD value=3 clid=CL_SHOP_DELIVERY_METHOD
@caption K&auml;ttetoimetamise viis

@reltype RELTYPE_CHANNEL value=12 clid=CL_WAREHOUSE_SELL_CHANNEL
@caption M&uuml;&uuml;gikanal

*/

class shop_order_cart extends class_base
{
	function shop_order_cart()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_order_cart",
			"clid" => CL_SHOP_ORDER_CART
		));

		$this->orderer_vars = array(
			"customer_no" => t("Kliendi number"),
			"birthday" => t("S&uuml;nnikuup&auml;ev"),
			"lastname" => t("Perekonnanimi"),
			"firstname" => t("Eesnimi"),
			"address" => t("Aadress"),
			"index" => t("Postiindeks"),
			"city" => t("Linn"),
			"email" => t("E-mail"), 
			"homephone" => t("Telefon kodus"),
			"workphone" => t("Telefon t&ouml;&ouml;l"),  
			"mobilephone" => t("Mobiil"),
			"work" => t("T&ouml;&ouml;koht"),
			"workexperience" => t("T&ouml;&ouml;staa"),
			"wage" => t("T&ouml;&ouml;tasu"),
			"profession" => t("Amet"),
			"personalcode" => t("Isikukood"),
		);

	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "user_data_template":
			case "product_template":
				$tm = get_instance("templatemgr");
				$prop["options"] = $tm->template_picker(array(
					"folder" => "applications/shop/shop_order_cart/"
				));
				break;
		};
		return $retval;
	}


	public function _get_delivery_method_tlb($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		if($this->can("add", $arr["obj_inst"]->id()))
		{
			$t->add_new_button(array(CL_SHOP_DELIVERY_METHOD), $arr["obj_inst"]->id(), 3);
		}
		$t->add_save_button();
		$t->add_search_button(array(
			"pn" => "delivery_method_add",
			"clid" => CL_SHOP_DELIVERY_METHOD
		));
		$t->add_delete_rels_button();
	}

	public function _get_delivery_method_add($arr)
	{
		$arr["prop"]["value"] = "";
	}

	public function _set_delivery_method_add($arr)
	{
		$o = obj(automatweb::$request->arg("id"));
		foreach(explode(",", $arr["prop"]["value"]) as $id)
		{
			$o->connect(array(
				"to" => $id,
				"type" => "RELTYPE_DELIVERY_METHOD",
			));
		}
	}

	public function _get_delivery_method_tbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->define_chooser();
		$t->define_field(array(
			"name" => "jrk",
			"caption" => t("Jrk"),
			"sortable" => true,
			"sorting_field" => "jrk_num",
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => true,
		));
		$t->define_field(array(
			"name" => "type",
			"caption" => t("T&uuml;&uuml;p"),
			"sortable" => true,
		));
		$t->define_field(array(
			"name" => "price",
			"caption" => t("Hind"),
			"sortable" => true,
		));

		$t->data_from_ol(new object_list(array(
			"class_id" => CL_SHOP_DELIVERY_METHOD,
			"CL_SHOP_DELIVERY_METHOD.RELTYPE_DELIVERY_METHOD(CL_SHOP_ORDER_CART)" => $arr["obj_inst"]->id(),
			"site_id" => array(),
			"lang_id" => array(),
		)), array("change_col" => "name"));

		$opts = shop_delivery_method::get_type_options();
		foreach($t->get_data() as $id => $row)
		{
			if(!empty($opts[$row["type"]]))
			{
				$row["type"] = $opts[$row["type"]];
			}
			$row["jrk_num"] = $row["jrk"];
			$row["jrk"] = html::textbox(array(
				"name" => "delivery_method_tbl[".$row["oid"]."][jrk]",
				"value" => $row["jrk"],
				"size" => 4,
			));
			$t->set_data($id, $row);
		}

		$t->set_default_sortby("jrk_num");
	}

	public function _set_delivery_method_tbl($arr)
	{
		foreach($arr["prop"]["value"] as $oid => $data)
		{
			$o = obj($oid);
			$o->set_ord($data["jrk"]);
			$o->save();
		}
	}

	public function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function get_cart($oc)
	{
		if($oc && $oc->prop("cart_type") == 1 && aw_global_get("uid") != "")
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

	function set_cart($arr)
	{
		extract($arr);
		if($oc->prop("cart_type") == 1 && aw_global_get("uid") != "")
		{
			$user = obj(aw_global_get("uid_oid"));
			$user->set_meta("shop_cart", $cart);
			$user->save();
		}
		$_SESSION["cart"] = $cart;
	}

	function parse_alias($arr = array())
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	/** shows the cart to user

		@attrib name=show_cart nologin="1"

		@param id optional type=int acl=view
		@param oc optional type=int
		@param cart optional type=int
		@param section optional
	**/
	function show($arr)
 	{
		extract($arr);
		if(empty($oc) && !empty($arr["cart"]))
		{
			$this->cart = obj($arr["cart"]);
			$oc = $this->cart->get_oc();
		}
		elseif(!empty($oc))
		{
			$oc = obj($oc);
		}

		// get cart to user from oc
		if (!empty($arr["id"]))
		{
			$this->cart = $cart_o = obj($arr["id"]);
		}
		else
		{
			$this->cart = $cart_o = obj($oc->prop("cart"));
		}
		if(empty($oc))
		{
			if(!is_object($oc = $this->cart->get_shop_order_center()))
			{
				$oc = obj();
			}
		}
		$this->vars(array("section" => aw_global_get("section")));
		if(!empty($arr["template"]))
		{
			$this->read_template($arr["template"]);
		}
		elseif($oc->prop("chart_show_template"))
		{
			$this->read_template($oc->prop("chart_show_template"));
		}
		else
		{
			$this->read_template("show.tpl");
		}
		lc_site_load("shop", &$this);

		$this->add_cart_vars();
		$this->add_product_vars();
		$cart_total = $this->cart_sum;
		$this->add_orderer_vars();
		if($oc->prop("show_delivery"))
		{
			$this->add_order_vars();
		}

/*		$soce = new aw_array(aw_global_get("soc_err"));
		$soce_arr = $soce->get();
		foreach($soce->get() as $prid => $errmsg)
		{
			if (!$errmsg["is_err"])
			{
				continue;
			}

			$this->vars(array(
				"msg" => $errmsg["msg"],
				"prod_name" => $errmsg["prod_name"],
				"prod_id" => $errmsg["prod_id"],
				"must_order_num" => $errmsg["must_order_num"],
				"ordered_num" => $errmsg["ordered_num"]
			));
			$err .= $this->parse("ERROR");
		}
		if(!empty($err))
		{
			$this->vars(array(
				"ERROR" => $err
			));
		}

		aw_session_del("soc_err");
*/
		if ($oc->prop("no_show_cart_contents"))
		{
			return $this->pre_finish_order($arr);
		}


//		error::raise_if(!$cart_o->prop("prod_layout"), array(
//			"id" => "ERR_NO_PROD_LAYOUT",
//			"msg" => sprintf(t("shop_order_cart::show(): no product layout set for cart (%s) "), $cart_o->id())
//		));
//		$layout = obj($cart_o->prop("prod_layout"));

		$total = $cart_total;
		$items = 0;
		$prod_total = 0;
//		$cart_total = 0;
		$str = "";
		$cart = $this->get_cart($oc);
		$awa = new aw_array($cart["items"]);
		$show_info_page = true;
//		$l_inst = $layout->instance();
//		$l_inst->read_template($layout->prop("template"));

/*

		 $product_str = "";

		if($this->is_template("CART"))
		{
			$carts = array();
			foreach($awa->get() as $iid => $quantx)
			{
				if(!is_oid($iid) || !$this->can("view", $iid))
				{
					continue;
				}	

				$quantx = new aw_array($quantx);
				
				foreach($quantx->get() as $x => $quant)
				{
					if($quant["items"] < 1)
					{
						continue;
					}
					$carts[$quant["cart"]][$iid][$x] = $quant;
				}
			}

			$cart_str = "";
			foreach($carts as $cart_name => $cart_arr)
			{
				$prod_str = "";
				foreach($cart_arr as $iid => $quantx)
				{
					$quantx = new aw_array($quantx);
					$i = obj($iid);
					$inst = $i->instance();
					$price = $inst->get_price($i);
						
					foreach($quantx->get() as $x => $quant)
					{

						$items ++;
						$product = obj($i);
						$vars = $product->get_data();
						$price = $product->get_shop_price($oc->id());
						$total_price = $quant["items"] * $price;

						$vars["amount"] = $quant["items"];
						$vars["price"] = number_format($price , 2);
						$vars["total_price"] = number_format($total_price , 2);
						$vars["total_price_without_thousand_separator"] = $total_price;
						$vars["remove_url"] = $this->mk_my_orb("remove_product" , array("cart" => $cart_o->id(), "product" => $iid));
						$this->vars($vars);
						$subs = array();
						foreach($vars as $key => $val)
						{
							if($this->is_template("HAS_".strtoupper($key)))
							{
								if($val)
								{
									$subs["HAS_".strtoupper($key)] = $this->parse("HAS_".strtoupper($key));
								}
								else
								{
									$subs["HAS_".strtoupper($key)] = "";
								}
							}
						}
						$this->vars($subs);
						$product_str.= $this->parse("PRODUCT");
						$show_info_page = false;
						$total += $total_price;
						$cart_total += $total_price;
						$str .= $this->parse("PROD");
					}
				}
				$this->vars(array(
					"PRODUCT" => $product_str,
					"PROD" =>  $prod_str,
					"cart_total" => $cart_total,
					"cart_name" => $cart_name,
					"cart_confirm_checkbox" => html::checkbox(array(
						"name" => "cart_confirm[".$cart_name."]",
						"value" => 1,
					)),
					"cart_confirm_chooser" => html::radiobutton(array(
						"name" => "cart_confirm",
						"value" => $cart_name,
					)),
				));
				$str.= $this->parse("CART");
			}
			$this->vars(array(
				"CART" =>  $str,
			));
			$str = " ";
		}
		else
		{
			foreach($awa->get() as $iid => $quantx)
			{
				if(!is_oid($iid) || !$this->can("view", $iid))
				{
					continue;
				}
				$quantx = new aw_array($quantx);
				$i = obj($iid);
				$inst = $i->instance();
				$price = $inst->get_price($i);
				
				foreach($quantx->get() as $x => $quant)
				{
					if($quant["items"] < 1)
					{
						continue;
					}
	
					$items ++;


					$product = obj($i);
					$vars = $product->get_data();
					$special_price = $product->get_shop_special_price($oc->id());
					if (!empty($special_price))
					{
						$price = $special_price;
					}
					else
					{
						$price = $product->get_shop_price($oc->id());
					}
					$total_price = $quant["items"] * $price;

					$vars["amount"] = $quant["items"];
					$vars["price"] = number_format($price , 2);
					$vars["total_price"] = number_format($total_price , 2);
					$vars["total_price_without_thousand_separator"] = $total_price;
					$vars["remove_url"] = $this->mk_my_orb("remove_product" , array("cart" => $cart_o->id(), "product" => $iid));
					$this->vars($vars);
						$subs = array();
						foreach($vars as $key => $val)
						{
							if($this->is_template("HAS_".strtoupper($key)))
							{
								if($val)
								{
									$subs["HAS_".strtoupper($key)] = $this->parse("HAS_".strtoupper($key));
								}
								else
								{
									$subs["HAS_".strtoupper($key)] = "";
								}
							}
						}
						$this->vars($subs);
					$product_str.= $this->parse("PRODUCT");

					$show_info_page = false;
					$total += $total_price;
					$cart_total += $total_price;
					$str .= $this->parse("PROD");
				}
			}
		}
		$this->cart_sum = $cart_total;
*/

		if ($str == "" && $this->is_template("NO_SHOW_EMPTY"))
		{
			return $this->parse("NO_SHOW_EMPTY");
		}

		$swh = get_instance(CL_SHOP_WAREHOUSE);
		$wh_o = obj($oc->prop("warehouse"));

		// fake user data
		if(!empty($cart["user_data"]))	$wh_o->set_meta("order_cur_ud", $cart["user_data"]);

		$els = $swh->callback_get_order_current_form(array(
			"obj_inst" => $wh_o
		));

		$els = $this->do_insert_user_data_errors($els);


		$htmlc = get_instance("cfg/htmlclient");
		$htmlc->start_output();
		foreach($els as $pn => $pd)
		{
			$htmlc->add_property($pd);
		}
		$htmlc->finish_output();

		$html = $htmlc->get_result(array(
			"raw_output" => 1
		));

		if (aw_global_get("uid") != "")
		{
			$us = get_instance(CL_USER);
			$objs = array(
				"user_data_user_" => obj($us->get_current_user()),
				"user_data_person_" => obj($us->get_current_person()),
				"user_data_org_" => obj($us->get_current_company()),
			);
			$vars = array();
			foreach($objs as $prefix => $obj)
			{
				$ops = $obj->properties();

				foreach($ops as $opk => $opv)
				{
					$vars[$prefix.$opk] = $opv;
				}
			}

			$vars["logged"] = $this->parse("logged");
			$this->vars($vars);
		}

		//$cart_total = $this->get_cart_value();
		$cart_discount = $cart_total * ($oc->prop("web_discount")/100);


		$postal_price = 0;


		if(!empty($this->total_sum) &&  $this->total_sum > 0)
		{
			$total = $this->total_sum;
		}

		if ($oc->prop("show_delivery") and $this->can("view", $oc->prop("delivery_show_controller")))
		{
			$ctrl = get_instance(CL_FORM_CONTROLLER);
			$arr["cart_val_w_disc"] = $cart_total - $cart_discount;
			$delivery_vars = $ctrl->eval_controller($oc->prop("delivery_show_controller"), $oc, $cart, $arr);
		}
		else
		{
			$delivery_vars = array();
		}

		if(!empty($cart["order_data"]["delivery"]) && $this->can("view" , $cart["order_data"]["delivery"]))
		{
			$delivery = obj( $cart["order_data"]["delivery"]);
			$postal_price = $delivery->get_curr_price($oc->prop("default_currency"));
			$total+= $delivery->get_curr_price($oc->prop("default_currency"));
		}
		elseif($this->delivery_sum)
		{
			$total+= $this->delivery_sum;
			$postal_price = $this->delivery_sum;

		}
		elseif ($cart_o->prop("postal_price") > 0)
		{
			$total += $cart_o->prop("postal_price");
			$postal_price =  $cart_o->prop("postal_price");
		}

		if(aw_global_get("uid") == "struktuur.markop")
		{
//$this->cart_sum =$this->cart_sum * 0.9;
		}



		$this->vars($delivery_vars + array(
			"cart_total" => number_format($this->cart_sum , 2, "." , ""),
			"unformated_cart_total" => $this->cart_sum,
			"cart_discount" => number_format($cart_discount, 2),
			"cart_val_w_disc" => number_format($cart_total - $cart_discount, 2),
			"user_data_form" => $html,
//			"PROD" => $str,
//			"PRODUCT" => $product_str,
			"basket_total_price" =>number_format($this->cart_sum,2),
			"total" => number_format($total, 2),
			"prod_total" => number_format($prod_total, 2),
			"postal_price" => number_format($postal_price,2),
			"reforb" => $this->mk_reforb("submit_add_cart", array(
				"oc" => $oc->id(),
				"cart" => $cart_o->id(),
				"update" => 1,
				"section" => aw_global_get("section"),
			)),
		));
/*
		if ($cart_o->prop("postal_price") > 0 or !empty($delivery_vars["postal_price"]))
		{
			$this->vars(array(
				"HAS_POSTAGE_FEE" => $this->parse("HAS_POSTAGE_FEE")
			));
		}
		if($show_info_page)
		{
			$this->vars(array(
				'info_page' => $this->parse('info_page'),
			));
		}
		else
		{
			$this->vars(array(
				'cart_page' => $this->parse('cart_page'),
			));
		}
*/
		if($this->product_count && $this->is_template("HAS_PRODUCTS"))
		{
			$this->vars(array(
				"HAS_PRODUCTS" => $this->parse("HAS_PRODUCTS"),
			));
		}
/*
		$ll = $lln = "";
		if (aw_global_get("uid") != "")
		{
			$ll = $this->parse("logged");
		}
		else
		{
			$lln = $this->parse("not_logged");
		}

		$this->vars(array(
			"logged" => $ll,
			"not_logged" => $lln,
		));
*/

		//pangamakse muutujad
		if($oc->prop("use_bank_payment"))
		{
			$this->add_bank_vars($oc, empty($cart["user_data"]) ? null : $cart["user_data"]);
		}

		$data["cart"] = $cart_o->id();
		
		//peale submiti edasi mineku url
		$data["go_to_after"] = aw_ini_get("baseurl")."/index.aw?action=".($oc->prop("show_delivery") ? "order_data" : "orderer_data")."&class=shop_order_cart&cart=".$cart_o->id()."&section=".aw_global_get("section");

		//kinnitamise url
		$data["confirm_url"] = aw_ini_get("baseurl")."/index.aw?action=confirm_order&class=shop_order_cart&cart=".$cart_o->id()."&section=".aw_global_get("section");

		$this->vars($data);
		//k6igi muutujate kohta sub ka selle jaoks, kui on olemas selline muutuja
		foreach($this->vars as $key => $val)
		{
			if($val && $this->is_template("HAS_".strtoupper($key)))
			{
				$this->vars(array("HAS_".strtoupper($key) => $this->parse("HAS_".strtoupper($key))));
			}
		}

		return $this->parse();
	}

	function add_bank_vars($oc, $uta)
	{
		$data = array();
		$bank_inst = get_instance(CL_BANK_PAYMENT);
		$bank_payment = $oc->prop("bank_payment");
		//et pank saaks alati valitud
		if(empty($soce_arr["bank"]))
		{
			$soce_arr["bank"] = empty($_SESSION["cart"]["user_data"]["user9"]) ?  "" : $_SESSION["cart"]["user_data"]["user9"];
			if($oc->prop("bank_id"))
			{
				$soce_arr["bank"] = $uta[$oc->prop("bank_id")];
			}
			if(!$soce_arr["bank"]) $need_to_choose_default_bank = 1;
		}
		if(is_oid($bank_payment))
		{
			$payment = obj($bank_payment);
			foreach($payment->meta("bank") as $key => $val)
			{
				if(!$val["sender_id"])
				{
					continue;
				}
				$checked=0;
				if($soce_arr["bank"] == $key || $need_to_choose_default_bank)
				{
					$data[$key."_checked"] = "checked";
					$checked = 1;
					$need_to_choose_default_bank = 0;
				}
				$data["bank_".$key] = html::radiobutton(array(
					"value" => $key,
					"checked" => $checked,
					"name" => "bank",
				));
			}
		}
		$this->vars($data);
	}

	function set_confirm_carts($cc)
	{
		$this->confirm_carts = $_SESSION["soc"]["confirm_carts"] = array();
		if(is_array($cc))
		{
			foreach($cc as $key => $val)
			{
				$this->confirm_carts[$key] = $_SESSION["soc"]["confirm_carts"][$key] = $key;
			}
		}
		else
		{
			$this->confirm_carts[$cc] = $_SESSION["soc"]["confirm_carts"][$cc] = $cc;
		}
	}

	function check_confirm_carts($cart)
	{
		if(!$_SESSION["soc"]["confirm_carts"])
		{
			return 1;
		}
		if(!$cart)
		{
			if(!is_array($_SESSION["soc"]["confirm_carts"]))
			{
				return 1;
			}
			foreach($_SESSION["soc"]["confirm_carts"] as $c)
			{
				if(!$c)
				{
					return 1;
				}
			}
		}
		else
		{
			foreach($_SESSION["soc"]["confirm_carts"] as $c)
			{
				if($c == $cart)
				{
					return 1;
				}
			}
		}
/*		if(!$this->confirm_carts)
		{
			return 1;
		}
		if(!$cart)
		{
			if(!is_array($this->confirm_carts))
			{
				return 1;
			}
			foreach($this->confirm_carts as $c)
			{
				if(!$c)
				{
					return 1;
				}
			}
		}
		else
		{
			foreach($this->confirm_carts as $c)
			{
				if($c == $cart)
				{
					return 1;
				}
			}
		}*/
		return 0;
	}

	/** order submit page, must add items to cart

		@attrib name=submit_add_cart nologin="1"

		@param oc required type=int acl=view
		@param add_to_cart optional
		@param is_update optional type=int
		@param order_data optional
		@param go_to_after optional
		@param section optional

	**/
	function submit_add_cart($arr)
	{
		extract($arr);

		//kui on mitu ostukorvi, siis hiljem kontrollib ykshaaval tooteid sealt
		if(!empty($cart_confirm))
		{
			$this->set_confirm_carts($cart_confirm);
		}

		$section = aw_global_get("section");
		$oc = obj($oc);
		$cart = $this->get_cart($oc);
		if(isset($arr["order_cond_ok"]))
		{
			aw_session_set("order.accept_cond", $arr["order_cond_ok"]);
		}
		// get cart to user from oc
		$cart_o = obj($oc->prop("cart"));

		// now get item layout from cart
		$layout = obj($cart_o->prop("prod_layout"));

		$order_ok = true;
		if (!empty($arr["add_to_cart_id"]))
		{
			$arr["add_to_cart"] = $add_to_cart = array(
				$arr["add_to_cart_id"] => $arr["quantity"]
			);
			$arr["order_data"] = $order_data = array(
				$arr["add_to_cart_id"] => $arr["order_data"]
			);
		}
		$awa = new aw_array($arr["add_to_cart"]);
		$si = __get_site_instance();
		$has_cb = method_exists($si, "check_submit_add_to_cart");
		foreach($awa->get() as $iid => $quantx)
		{
			if (!is_oid($iid) || !$this->can("view", $iid))
			{
				unset($arr["add_to_cart"]["items"][$iid]);
				continue;
			}
			$i_o = obj($iid);
			$i_i = $i_o->instance();
			$mon = $i_i->get_must_order_num($i_o);
			$uid = aw_global_get("uid");
			$group = strlen($uid) > 0 ? get_instance(CL_USER)->get_groups_for_user($uid)->ids() : get_instance(CL_GROUP)->get_non_logged_in_group();
			$am_limits = $i_i->get_amount_limits(array("id" => $iid, "group" => $group));
			// initialize $am_limit - markop
			// oh no you don't! - terryf. check :436
			// OK. Now, I do. Cuz otherwise it memorizes limits from previous products. -kaarel (fixed :437, :439 and :459)
			$am_limit = array();
			foreach($am_limits as $limits)
			{
				// Determine the lowest minimum for current user
				if(!isset($am_limit["min"]) || $am_limit["min"] > $limits["min"])
				{
					$am_limit["min"] = $limits["min"];
				}
				// Determine the highest maximum for current user
				if(!isset($am_limit["max"]) || $am_limit["max"] < $limits["max"])
				{
					$am_limit["max"] = $limits["max"];
				}
			}
			$quantx = new aw_array($quantx);
			foreach($quantx->get() as $x => $quant)
			{
				if (!empty($arr["update"]) && $arr["update"] == 1)
				{
					$cc = $quant;
				}
				else
				{
					$cc = (int)$cart["items"][$iid][$x]["items"] + $quant;
				}
				if(is_array($am_limit) && count($am_limit) > 0)
				{
					if(isset($am_limit["min"]) && $am_limit["min"] > $cc)
					{
						$soce = aw_global_get("soc_err");
						if (!is_array($soce))
						{
							$soce = array();
						}
						$soce[$iid] = array(
							"msg" => sprintf(t("%s minimaalne tellimiskogus on %s, hetkel korvis %s."), $i_o->trans_get_val("name"), $am_limit["min"], $cc),
							"prod_name" => $i_o->name(),
							"prod_id" => $i_o->id(),
							"amount_limit_min" => $am_limit["min"],
							"ordered_num" => $cc,
							"ordered_num_enter" => $quant,
							"is_err" => true
						);
						aw_session_set("soc_err", $soce);
						$order_ok = false;
					}
					else
					if(isset($am_limit["max"]) && $am_limit["max"] < $cc)
					{
						$soce = aw_global_get("soc_err");
						if (!is_array($soce))
						{
							$soce = array();
						}
						$soce[$iid] = array(
							"msg" => sprintf(t("%s maksimaalne tellimiskogus on %s, hetkel korvis %s."), $i_o->trans_get_val("name"), $am_limit["max"], $cc),
							"prod_name" => $i_o->name(),
							"prod_id" => $i_o->id(),
							"amount_limit_max" => $am_limit["max"],
							"ordered_num" => $cc,
							"ordered_num_enter" => $quant,
							"is_err" => true
						);
						aw_session_set("soc_err", $soce);
						$order_ok = false;
					}
				}
				if ($mon)
				{
					if (($cc % $mon) != 0)
					{
						$soce = aw_global_get("soc_err");
						if (!is_array($soce))
						{
							$soce = array();
						}
						$soce[$iid] = array(
							"msg" => sprintf(t("%s peab tellima %s kaupa, hetkel kokku %s!"), $i_o->name(),$mon, $cc),
							"prod_name" => $i_o->name(),
							"prod_id" => $i_o->id(),
							"must_order_num" => $mon,
							"ordered_num" => $cc,
							"ordered_num_enter" => $quant,
							"is_err" => true
						);
						aw_session_set("soc_err", $soce);
						$order_ok = false;
					}
				}
				if ($has_cb)
				{
					if (is_array($rv = $si->check_submit_add_to_cart($iid, $cc)))
					{
						 $soce = aw_global_get("soc_err");
						 if (!is_array($soce))
						 {
						 	 $soce = array();

						}
						$soce[$iid] = array(
							"msg" => $rv["msg"],
							"prod_name" => $i_o->name(),
							"prod_id" => $i_o->id(),
							"must_order_num" => $mon,
			   //             "ordered_num" => $cc,
				//            "ordered_num_enter" => $quant,
							"is_err" => true
						);
						aw_session_set("soc_err", $soce);
						$order_ok = false;

					}
				}
			}
		}

		// process delivery
		if ($oc->prop("show_delivery") and $this->can("view", $oc->prop("delivery_save_controller")))
		{
			$ctrl = get_instance(CL_FORM_CONTROLLER);
			$ctrl->eval_controller($oc->prop("delivery_save_controller"), $oc, &$cart);
		}

		if (($arr["from"] != "confirm" && $arr["from"] != "") || (is_array($_REQUEST["user_data"]) && count($_REQUEST["user_data"])))
		{
			$cart["user_data"] = $_REQUEST["user_data"];
		}

		if (isset($arr["payment_method"]))
		{
			$cart["payment_method"] = $arr["payment_method"];
		}
		if (isset($arr["num_payments"]))
		{
			$cart["payment"]["num_payments"] = $arr["num_payments"];
		}
		$this->set_cart(array(
			"oc" => $oc,
			"cart" => $cart,
		));

		// check cfgform controllers for user data
		$cfgf = $oc->prop("data_form");
		if ($cfgf && $arr["from"] != "confirm")
		{
			$is_valid = $this->validate_data(array(
				"cfgform_id" => $cfgf,
				"request" => $user_data
			));
			if (count($is_valid) > 0)
			{
				$order_ok = false;
				// save the errors in session
				aw_session_set("soc_err_ud", $is_valid);
			}
		}

		// i'm quite sure that you don't want to know, whatta hell is going on in here
		// neighter do i... -- ahz
		$awa = new aw_array($arr["add_to_cart"]);
		$order_data = safe_array($order_data);
		foreach($awa->get() as $iid => $quantx)
		{
			$cart["items"][$iid] = safe_array($cart["items"][$iid]);
			if (is_numeric($quantx))
			{
				$quantx = new aw_array(array($quantx));
			}
			else
			{
				$quantx = new aw_array($quantx);
			}

			foreach($quantx->get() as $x => $quant)
			{
				if ($arr["update"] == 1)
				{
					$cart["items"][$iid][$x] = safe_array($cart["items"][$iid][$x]);
					$cart["items"][$iid][$x]["items"] = $quant;
				}
				else
				{
					if($oc->prop("multi_items") == 1)
					{
						$x = 0;
						// get the highest id from items -- ahz
						if(count($cart["items"][$iid]) > 0)
						{
							foreach($cart["items"][$iid] as $key => $val)
							{
								if($key > $x)
								{
									$x = $key;
								}
							}
							$x++;
						}
						$cart["items"][$iid][$x]["items"] = $quant;
					}
					else
					{
						$cart["items"][$iid][$x]["items"] += $quant;
					}
				}
				if($arr["from"] != "confirm")
				{
					foreach(safe_array($order_data[$iid]) as $key => $val)
					{
						if((string)$key == "all_items" || (string)$key == "all_pkts")
						{
							continue;
						}
						if(is_array($val))
						{
							if($key == $x)
							{
								$tmp = $cart["items"][$iid][$x];
								$cart["items"][$iid][$x] = $val + $tmp;
							}
						}
						else
						{
							$cart["items"][$iid][$x][$key] = $val;
						}
					}
				}
			}
		}

		foreach(safe_array($to_remove) as $xid => $rm)
		{
			$rm = new aw_array($rm);
			foreach($rm->get() as $key => $val)
			{
				if($val == 1)
				{
					unset($cart["items"][$xid][$key]);
				}
			}
		}
		foreach(safe_array($cart["items"]) as $iid => $val)
		{
			if(count($val) <= 0)
			{
				unset($cart["items"][$iid]);
			}
		}
		//arr$(cart);
		$this->set_cart(array(
			"oc" => $oc,
			"cart" => $cart,
		));

		if (!$order_ok)
		{
			$awa = new aw_array($arr["add_to_cart"]);
			$soce = aw_global_get("soc_err");
			foreach($awa->get() as $iid => $quant)
			{
				if (isset($soce[$iid]))
				{
					continue;
				}
				$soce[$iid] = array(
					"is_err" => false,
					"ordered_num_enter" => $quant
				);
			}
			aw_session_set("soc_err", $soce);
			aw_session_set("no_cache", 1);

			if (!$arr["return_url"])
			{
				if ($arr["from"] == "pre")
				{
					header("Location: ".$this->mk_my_orb("pre_finish_order", array("oc" => $arr["oc"], "section" => $arr["section"])));
				}
				else
				{
					header("Location: ".$this->mk_my_orb("show_cart", array("oc" => $arr["oc"], "section" => $arr["section"])));
				}
			}
			else
			{
				header("Location: ".$arr["return_url"]);
			}
			die();
		}

		if ($arr["from"] == "pre" && !$arr["order_cond_ok"])
		{
			aw_session_set("order_cond_fail", 1);
			aw_session_set("no_cache", 1);
			if (!$arr["return_url"])
			{
				if ($arr["from"] == "pre")
				{
					header("Location: ".$this->mk_my_orb("pre_finish_order", array("oc" => $arr["oc"], "section" => $arr["section"])));
				}
				else
				{
					header("Location: ".$this->mk_my_orb("show_cart", array("oc" => $arr["oc"], "section" => $arr["section"])));
				}
			}
			else
			{
				header("Location: ".$arr["return_url"]);
			}
			die();
		}


		if($arr["from"] != "confirm")
		{
			if (is_array($order_data["all_items"]))
			{
				$awa_i = new aw_array($arr["add_to_cart"]);
				$awa_all = safe_array($order_data["all_items"]);
				foreach($awa_i->get() as $iid => $quantx)
				{
					$quantx = new aw_array($quantx);
					foreach($quantx->get() as $x => $quant)
					{
						if($quant > 0)
						{
							$cart["items"][$iid][$x] = safe_array($cart["items"][$iid][$x]);
							foreach($awa_all as $aa_k => $aa_v)
							{
								$cart["items"][$iid][$x][$aa_k] = $aa_v;
							}
						}
					}
				}
			}
			if (is_array($order_data["all_pkts"]))
			{
				foreach($order_data["all_pkts"] as $iid => $k_d)
				{
					if (!is_oid($iid) || !$this->can("view", $iid))
					{
						continue;
					}
					$tmp = obj($iid);
					foreach($tmp->connections_from(array("type" => "RELTYPE_PACKAGING")) as $c)
					{
						foreach($cart["items"][$c->prop("to")] as $key => $val)
						{
							foreach(safe_array($k_d) as $k_k => $k_v)
							{
								$cart["items"][$c->prop("to")][$key][$k_k] = $k_v;
							}
						}
					}
				}
			}
		}
		foreach(safe_array($to_remove) as $xid => $rm)
		{
			$rm = new aw_array($rm);
			foreach($rm->get() as $key => $val)
			{
				if($val == 1)
				{
					unset($cart["items"][$xid][$key]);
				}
			}
		}
		foreach(safe_array($cart["items"]) as $iid => $val)
		{
			if(count($val) <= 0)
			{
				unset($cart["items"][$iid]);
			}
		}
		//arr($cart);
		$this->set_cart(array(
			"oc" => $oc,
			"cart" => $cart,
		));
		//arr($cart);

		if (is_oid($cart_o->prop("update_handler")) && $this->can("view", $cart_o->prop("update_handler")))
		{
			$ctr = get_instance(CL_FORM_CONTROLLER);
			if (!$ctr->do_check($cart_o->prop("update_handler"), NULL, $cart_o, $oc))
			{
				if (!$arr["return_url"])
				{
					if ($arr["from"] == "pre")
					{
						header("Location: ".$this->mk_my_orb("pre_finish_order", array("oc" => $arr["oc"], "section" => $arr["section"])));
					}
					else
					{
						header("Location: ".$this->mk_my_orb("show_cart", array("oc" => $arr["oc"], "section" => $arr["section"])));
					}
				}
				else
				{
					header("Location: ".$arr["return_url"]);
				}
				// if this gets removed, then THINGS WILL GET FUCKED. the header() calls above will night sie workensh
				// so don't touch this.
				die();
			}
		}

		if (!empty($arr["clear_cart"]))
		{
			$this->clear_cart($oc);
		}

		if (!empty($arr["go_to_after"]))
		{
			return $arr["go_to_after"];
		}

		if (!empty($arr["pre_confirm_order"]))
		{
			// go to separate page with order non modifiable and user data form below
			return urldecode($this->mk_my_orb("pre_finish_order", array("oc" => $arr["oc"], "section" => $arr["section"])));
		}
		else
		if (!empty($arr["final_confirm_order"]))
		{
			// go to separate page with order non modifiable and user data form below
			return urldecode($this->mk_my_orb("final_finish_order", array("oc" => $arr["oc"], "section" => $arr["section"])));
		}
		else
		if (!empty($arr["update_final_finish"]))
		{
			return urldecode($this->mk_my_orb("final_finish_order", array("oc" => $arr["oc"], "section" => $arr["section"])));
		}
		else
		if (!empty($arr["confirm_order"]))
		{
			// do confirm order and show user
			// if cart is empty, redirect to front page
			$awa = new aw_array($cart["items"]);
			$empty = true;
			foreach($awa->get() as $val)
			{
				if (count($val) >= 1)
				{
					$empty = false;
					break;
				}
			}
			if($empty)
			{
				return aw_ini_get("baseurl");
			}
//			if(is_oid($cart_o->prop("finish_handler")) && $this->can("view", $cart_o->prop("finish_handler")))
//			{
//				$ctr = get_instance(CL_FORM_CONTROLLER);
//				$ctr->do_check($cart_o->prop("finish_handler"), NULL, $cart_o, $oc);
//			}
			aw_session_del("order.accept_cond");

		// There is no such function ?!? --dragut@28.08.2009
		//	$sell_order_id = $cart_o->create_sell_order();

			$ordid = $this->do_create_order_from_cart($arr["oc"], NULL, array(
				"payment" => $cart["payment"],
				"payment_type" => $cart["payment_method"]
			));

			$this->clear_some_carts($oc);
			return urldecode($this->mk_my_orb("show", array("id" => $ordid, "section" => $arr["section"]), "shop_order"));
		}
		else
		{
			return urldecode($this->mk_my_orb("show_cart", array("oc" => $arr["oc"], "section" => $arr["section"])));
		}
	}

	function do_create_order_from_cart($oc, $warehouse = NULL, $params = array())
	{
		$so = get_instance(CL_SHOP_ORDER);
		$oc = obj($oc);

		if ($warehouse === NULL)
		{
			if (!is_oid($oc->prop("warehouse")))
			{
				error::raise(array(
					"id" => "ERR_NO_WAREHOOS",
					"msg" => sprintf(t("shop_order_cart::do_creat_order_from_cart(): no warehouse set for cart %s!"), $oc->id())
				));
			}
			$warehouse = $oc->prop("warehouse");
		}

		// get cart from oc (order center)
		if ($oc->prop("cart"))
		{
			$order_cart = obj($oc->prop("cart"));
			// now, get postal_price from cart
			$params["postal_price"] = $order_cart->prop("postal_price");
		}
		$cart = obj($oc->prop("cart"));
		$params["cart"] = $cart;
		$params[""] = $cart;


		$this->update_user_data_from_order($oc, $warehouse, $params);

		$so->start_order(obj($warehouse), $oc);


		$cart = $this->get_cart($oc);
		$awa = new aw_array($cart["items"]);
		foreach($awa->get() as $iid => $quant)
		{
			$qu = new aw_array($quant);
			foreach($qu->get() as $key => $val)
			{
				if($val["cart"] && !$this->check_confirm_carts($val["cart"]))
				{
					continue;
				}
				$so->add_item(array("iid" => $iid, "item_data" => $cart["items"][$iid][$key], "it" => $key));
			}
		}

		//kui pank ise teeb paringu tagasi, siis votab miski muu keeele milles maili saata, et jargnev siis selle vastu
		if($oc->meta("lang_id"))
		{
			$params["lang_id"] = $oc->meta("lang_id");
			$params["lang_lc"] = $oc->meta("lang_lc");
		}
		$rval = $so->finish_order($params);
		//$this->clear_cart($oc);
		$this->clear_some_carts($oc);

		//uus teema on lao m&uuml;&uuml;gitellimus, mis peab ka toimima saama
		//vana v6ib 2ra kustutada, kui on kindel, et kuskil seda enam ei kasutata
		$o = new object();
		$o->set_name(t("M&uuml;&uuml;gitellimus")." ".date("d.m.Y H:i"));
		$o->set_parent($oc->id());
		$o->set_class_id(CL_SHOP_SELL_ORDER);
		$o->save();
		$awa = new aw_array($cart["items"]);
		foreach($awa->get() as $iid => $quant)
		{
			$qu = new aw_array($quant);
			foreach($qu->get() as $key => $val)
			{
				if($val["cart"] && !$this->check_confirm_carts($val["cart"]))
				{
					continue;
				}
				$o->add_row(array(
					"product" => $iid,
					"amount" => $cart["items"][$iid][$key],
				));
			}
		}		

		return $rval;
	}

	function add_item($arr)
	{
		extract($arr);
		$prod_data = safe_array($prod_data);
		$cart = $this->get_cart($oc);

		$multi = $oc->prop("multi_items");
		$cart["items"][$iid] = safe_array($cart["items"][$iid]);
		foreach($cart["items"][$iid] as $iid => $qx)
		{
			if($multi == 1)
			{
				$x = 0;
				foreach($cart["items"][$iid] as $high => $low)
				{
					if($high > $x)
					{
						$x = $high;
					}
				}
				$x++;
			}
			$tmp = array();
			$tmp["items"] = $cart["items"][$iid][$x]["items"] + $quant;
			$cart["items"][$iid][$x] = $tmp + $prod_data;
		}
		$this->set_cart(array(
			"oc" => $oc,
			"cart" => $cart,
		));
	}

	function set_item($arr)
	{
		extract($arr);
		$it = !$it ? 0 : $it;
		$cart = $this->get_cart($oc);
		if ($quant == 0)
		{
			unset($cart["items"][$iid][$it]);
		}
		else
		{
			$cart["items"][$iid][$it]["items"] = $quant;
		}
		$this->set_cart(array(
			"oc" => $oc,
			"cart" => $cart,
		));
	}

	function get_cart_value($prod = false)
	{
		$total = 0;
		$prod_total = 0;

		$awa = new aw_array(ifset($_SESSION, "cart", "items"));
		foreach($awa->get() as $iid => $quantx)
		{
			if(!is_oid($iid) || !$this->can("view", $iid))
			{
				continue;
			}
			$i = obj($iid);
			$inst = $i->instance();


//porno------------
//see miski loll systeem, et site.aw'st toodete hindade jms k2ki muutmise funktsioon k2iku lasta... juhul kui on mikski erandv2rk
			if (function_exists("__get_site_instance"))
			{
				$si =&__get_site_instance();
				if (is_object($si))
				{
					if (method_exists($si, "handle_product_display"))
					{
						$si->handle_product_display($i);
					}
				}
			}
//------------porno
			$price = $i->get_special_price();
//			$price = $inst->get_calc_price($i);
			$quantx = new aw_array($quantx);
			foreach($quantx->get() as $x => $quant)
			{
				$total += ($quant["items"] * $price);
				if ($prod)
				{
					if ($i->class_id() == CL_SHOP_PRODUCT_PACKAGING)
					{
						$prod_total += ($quant["items"] *  $inst->get_prod_calc_price($i));
					}
					else
					{
						$prod_total = $total;
					}
				}
			}
		}
		if ($prod)
		{
			return array($total, $prod_total);
		}
		else
		{
			return $total;
		}
	}

	function get_items_in_cart()
	{
		if (isset($_SESSION["cart"]["items"]))
		{
			$awa = new aw_array($_SESSION["cart"]["items"]);
		}
		else
		{
			$awa = new aw_array();
		}
		$ret = array();
		foreach($awa->get() as $iid => $q)
		{
			$q = new aw_array($q);
			foreach($q->get() as $v => $z)
			{
				if ($z > 0)
				{
					$ret[$iid][$v] = $z;
				}
			}
		}
		return $ret;
	}
		/* siin pannakse andmed loplikku tabelisse */
	function get_item_in_cart($arr)
	{
		$it = !$arr["it"] ? 0 : $arr["it"];
		return safe_array(ifset($_SESSION, "cart", "items", $arr["iid"], $it));
	}

	function clear_cart($oc)
	{
		$this->set_cart(array(
			"oc" => $oc,
			"cart" => array(),
		));
	}

	function clear_some_carts($oc)
	{
		$cart = $this->get_cart($oc);
		if(!$_SESSION["soc"]["confirm_carts"])
		{
			$cart = array();
		}
		else
		{
			foreach($cart["items"] as $iid => $quant)
			{
				foreach($quant as $key => $val)
				{
					if($val["cart"] && !$this->check_confirm_carts($val["cart"]))
					{
						continue;
					}
					unset($cart["items"][$iid][$key]);
					if(!sizeof($cart["items"][$iid]))
					{
						unset($cart["items"][$iid]);
					}
				}
			}
			if(!sizeof($cart["items"]))
			{
				$cart = array();
			}
			unset($_SESSION["soc"]["confirm_carts"]);
		}

		$this->set_cart(array(
			"oc" => $oc,
			"cart" => $cart,
		));

	}

	/**

		@attrib name=pre_finish_order nologin=1

		@param oc required
		@param section optional

	**/
	function pre_finish_order($arr)
	{
		extract($arr);
		$this->read_template("show_pre_finish.tpl");
		lc_site_load("shop", &$this);

		$soce = new aw_array(aw_global_get("soc_err"));
		$soce_arr = $soce->get();
		foreach($soce->get() as $prid => $errmsg)
		{
			if (!$errmsg["is_err"])
			{
				continue;
			}

			$this->vars(array(
				"msg" => $errmsg["msg"],
				"prod_name" => $errmsg["prod_name"],
				"prod_id" => $errmsg["prod_id"],
				"must_order_num" => $errmsg["must_order_num"],
				"ordered_num" => $errmsg["ordered_num"]
			));
			$err .= $this->parse("ERROR");
		}
		$this->vars(array(
			"ERROR" => $err,
			"order_cond_ok" => checked(aw_global_get("order.accept_cond"))
		));

		aw_session_del("soc_err");

		$oc = obj($oc);

		// get cart to user from oc
		if ($arr["id"])
		{
			$cart_o = obj($arr["id"]);
		}
		else
		{
			$cart_o = obj($oc->prop("cart"));
		}

		// now get item layout from cart
		error::raise_if(!$cart_o->prop("prod_layout"), array(
			"id" => "ERR_NO_PROD_LAYOUT",
			"msg" => sprintf(t("shop_order_cart::show(): no product layout set for cart (%s) "), $cart_o->id())
		));
		$layout = obj($cart_o->prop("prod_layout"));
		$layout->set_prop("template", "prod_pre_confirm.tpl");

		$l_inst = $layout->instance();
		$l_inst->read_template($layout->prop("template"));

		$total = 0;
		$prod_total = 0;

		$cart = $this->get_cart($oc);

		$awa = new aw_array($cart["items"]);
		foreach($awa->get() as $iid => $quantx)
		{
			if(!is_oid($iid) || !$this->can("view", $iid))
			{
				continue;
			}
			$quantx = new aw_array($quantx);
			$i = obj($iid);
			$inst = $i->instance();
			foreach($quantx->get() as $x => $quant)
			{
				if ($quant["items"] < 1)
				{
					continue;
				}
				$this->vars(array(
					"prod_html" => $inst->do_draw_product(array(
						"layout" => $layout,
						"prod" => $i,
						"it" => $x,
						"l_inst" => $l_inst,
						"quantity" => $quant["items"],
						"oc_obj" => $oc,
						"is_err" => ($soce_arr[$iid]["is_err"] ? "class=\"selprod\"" : "")
					))
				));
				$read_price_total += ($quant["items"] * str_replace(",","", $quant["read_price"]));
				$read_price_total_sum += (str_replace(",","", $quant["read_price"]));
				if (get_class($inst) == "shop_product_packaging")
				{
					$prod_total += ($quant["items"] * $inst->get_prod_calc_price($i));
				}
				else
				{
					$prod_total += ($quant["items"] * $inst->get_calc_price($i));
				}
				/*
				else
				{
					$prod_total = $total;
				}
				*/

				$str .= $this->parse("PROD");
			}
		}
		$swh = get_instance(CL_SHOP_WAREHOUSE);
		$wh_o = obj($oc->prop("warehouse"));

		// fake user data
		$wh_o->set_meta("order_cur_ud", $cart["user_data"]);

		$els = $swh->callback_get_order_current_form(array(
			"obj_inst" => $wh_o,
			"no_data" => (aw_global_get("uid") == "" ? true : false)
		));

		$do = false;
		if ($this->is_template("RENT"))
		{
			$cr = "";
			if ($prod_total > $oc->prop("rent_min_amt"))
			{
				$do = true;
				if ($oc->prop("rent_prop") != "" && $oc->prop("rent_prop_val") != "")
				{
					if ($els[$oc->prop("rent_prop")]["value"] != $oc->prop("rent_prop_val"))
					{

						$do = false;
					}
				}
			}
			else
			{
				$do = false;
			}

			$this->vars(array(
				"cod_selected" => checked($cart["payment_method"] == "cod" || !$do || $cart["payment_method"] == ""),
				"rent_selected" => checked($cart["payment_method"] == "rent"),
			));

			if ($do)
			{
				$this->vars(array(
					"can_rent" => $this->parse("can_rent")
				));
			}
			else
			{
				$this->vars(array(
					"no_can_rent" => $this->parse("no_can_rent")
				));
			}
			$this->vars(array(
				"RENT" => $this->parse("RENT")
			));
		}

		if ($els["userdate1"])
		{
			$els["userdate1"]["year_from"] = 1930;
			$els["userdate1"]["year_to"] = date("Y");
			$els["userdate1"]["no_default"] = true;
			$els["userdate1"]["value"] = -1;
		}

		if ($els["userdate2"])
		{
			$els["userdate2"]["year_from"] = date("Y");
			$els["userdate2"]["year_to"] = date("Y")+3;
		}

		// apply view controllers
		foreach($els as $el_pn => $el_inf)
		{
			foreach(safe_array($el_inf["view_controllers"]) as $v_ctr_id)
			{
				$vc = get_instance(CL_CFG_VIEW_CONTROLLER);
				$rv = $vc->check_property($els[$el_pn], $v_ctr_id, array());
				if ($rv == PROP_IGNORE)
				{
					unset($els[$el_pn]);
				}
			}
		}

		// if there are errors
		$els = $this->do_insert_user_data_errors($els);

		$rd = get_instance(CL_REGISTER_DATA);
		$els = $rd->parse_properties(array(
			"properties" => $els,
			"name_prefix" => ""
		));

		$htmlc = get_instance("cfg/htmlclient");
		$htmlc->start_output();
		foreach($els as $pn => $pd)
		{
			if ($pn == "user_data[uservar1]" && aw_ini_get("otto.import") && $prod_total > 1000)
			{
				$pd["onclick"] = "upd_rent(this)";
			}
			$htmlc->add_property($pd);
		}
		$htmlc->finish_output();

		$html = $htmlc->get_result(array(
			"raw_output" => 1
		));

		if (aw_global_get("order_cond_fail"))
		{
			$this->vars(array(
				"ACC_ERROR" => $this->parse("ACC_ERROR")
			));
			aw_session_del("order_cond_fail");
		}

		if (aw_global_get("uid") != "")
		{
			$us = get_instance(CL_USER);
			$objs = array(
				"user_data_user_" => obj($us->get_current_user()),
				"user_data_person_" => obj($us->get_current_person()),
				"user_data_org_" => obj($us->get_current_company()),
			);
			$vars = array();
			foreach($objs as $prefix => $obj)
			{
				$ops = $obj->properties();

				foreach($ops as $opk => $opv)
				{
					$vars[$prefix.$opk] = $opv;
				}
			}
			$vars["logged"] = $this->parse("logged");
			$this->vars($vars);
		}
		$this->vars(array(
			"user_data_form" => $html,
			"PROD" => $str,
			"total" => number_format($total, 2),
			"prod_total" => number_format($prod_total, 2),
			"read_price_total" => number_format($read_price_total, 2),
			"read_price_total_sum" => number_format($read_price_total_sum, 2),
			"reforb" => $this->mk_reforb("submit_add_cart", array("oc" => $arr["oc"], "update" => 1, "section" => $arr["section"], "from" => "pre")),
			"postal_price" => number_format($cart_o->prop("postal_price"))
		));

		if ($cart_o->prop("postal_price") > 0)
		{
			$this->vars(array(
				"HAS_POSTAGE_FEE" => $this->parse("HAS_POSTAGE_FEE")
			));
		}
		$ll = $lln = "";
		if (aw_global_get("uid") != "")
		{
			$ll = $this->parse("logged");
		}
		else
		{
			$lln = $this->parse("not_logged");
		}

		$this->vars(array(
			"logged" => $ll,
			"not_logged" => $lln
		));

		return $this->parse();
	}

	/**
		@attrib name=final_finish_order nologin=1
		@param oc optional
		@param cart optional
		@param section optional
		@param confirm_url optional
	**/
	function final_finish_order($arr)
	{
		if(!empty($arr["confirm_url"]))
		{
			header("Location: ".$arr["confirm_url"]);
			die();
		}
		$arr["template"] = "final_finish_order.tpl";
		return $this->show($arr);

		extract($arr);
		$oc = obj($oc);
		if($oc->prop("chart_final_template"))
		{
			$this->read_template($oc->prop("chart_final_template"));
		}
		else
		{
			$this->read_template("final_finish_order.tpl");
		}

		lc_site_load("shop", &$this);

		// get cart to user from oc
		if ($arr["id"])
		{
			$cart_o = obj($arr["id"]);
		}
		else
		{
			$cart_o = obj($oc->prop("cart"));
		}

		// now get item layout from cart
		error::raise_if(!$cart_o->prop("prod_layout"), array(
			"id" => "ERR_NO_PROD_LAYOUT",
			"msg" => sprintf(t("shop_order_cart::show(): no product layout set for cart (%s) "), $cart_o->id())
		));
		$layout = obj($cart_o->prop("prod_layout"));
		$layout->set_prop("template", "prod_pre_confirm.tpl");

		$total = 0;

		$cart = $this->get_cart($oc);

		$awa = new aw_array($cart["items"]);
		foreach($awa->get() as $iid => $quantx)
		{

			if (!is_oid($iid) or !$this->can("view", $iid))
			{
				continue;
			}
			$i = obj($iid);
			$inst = $i->instance();
			$price = $inst->get_price($i);
			foreach($quantx as $quant)
			{
				if($quant["items"] < 1)
				{
					continue;
				}
				if($quant["cart"] && !$this->check_confirm_carts($quant["cart"]))
				{
					continue;
				}
				$this->vars(array(
					"prod_html" => $inst->do_draw_product(array(
						"layout" => $layout,
						"prod" => $i,
						"quantity" => $quant["items"],
						"oc_obj" => $oc,
						"is_err" => ($soce_arr[$iid]["is_err"] ? "class=\"selprod\"" : "")
					))
				));
				$total += ($quant["items"] * $this->_format_calc_price($price));
				$str .= $this->parse("PROD");
			}
		}

		$swh = get_instance(CL_SHOP_WAREHOUSE);
		$wh_o = obj($oc->prop("warehouse"));

		// fake user data
		$wh_o->set_meta("order_cur_ud", $cart["user_data"]);
		$els = $swh->callback_get_order_current_form(array(
			"obj_inst" => $wh_o
		));

		foreach(safe_array($cart["user_data"]) as $k => $v)
		{
			if (($els[$k]["type"] == "chooser" || $els[$k]["type"] == "select") && $els[$k]["store"] == "connect")
			{
				if (is_array($v))
                                {
					$vs = array();
					foreach($v as $v_oid)
					{
						if ($this->can("view", $v_oid))
						{
							$tmp = obj($v_oid);
							$vs[] = $tmp->name();
						}
					}
					$v = join(", ", $vs);
				}
				else
				if ($this->can("view", $v))
				{
					$tmp = obj($v);
					$v = $tmp->name();
				}
				else
				{
					$v = "";
				}
			}
			$this->vars(array(
				"user_data_".$k => $v
			));
		}

		// if there are errors
		$els = $this->do_insert_user_data_errors($els);
		// since this view should be confirm view, then show the previously entered data.
		// if this nees to be different, we need some sort of config switch for it
		$ud = safe_array($cart["user_data"]);
		foreach($els as $pn => $pd)
		{
			$els[$pn]["value"] = $ud[$pn];
		}

		$prevd = $els["userdate1"]["value"];

		$rd = get_instance(CL_REGISTER_DATA);
		$els = $rd->parse_properties(array(
			"properties" => $els,
			"name_prefix" => ""
		));
		$els["user_data[userdate1]"]["value"] = $prevd;

		$htmlc = get_instance("cfg/htmlclient");
		$htmlc->start_output();

		foreach($els as $pn => $pd)
		{
			if ($pd["type"] == "date_select")
			{
				if ($pd["value"] == -1)
				{
					$pd["value"] = "---.---.---";
				}
				else
				{
					$pd["value"] = date("d.m.Y", $pd["value"]);
				}
			}
			else
			if ($pd["type"] == "chooser")
			{
				if (is_oid($pd["value"]) && $this->can("view", $pd["value"]))
				{
					$tmp = obj($pd["value"]);
					$pd["value"] = $tmp->name();
				}
				else
				{
					$pd["value"] = "";
				}
			}

			$pd["type"] = "text";
			$htmlc->add_property($pd);
		}
		$htmlc->finish_output();

		$html = $htmlc->get_result(array(
			"raw_output" => 1
		));

		if (false && aw_global_get("uid") != "")
		{
			$us = get_instance(CL_USER);
			$objs = array(
				"user_data_user_" => obj($us->get_current_user()),
				"user_data_person_" => obj($us->get_current_person()),
				"user_data_org_" => obj($us->get_current_company()),
			);
			$vars = array();
			foreach($objs as $prefix => $obj)
			{
				$ops = $obj->properties();

				foreach($ops as $opk => $opv)
				{
					$vars[$prefix.$opk] = $opv;
				}
			}
			$this->vars($vars);
		}

		$cart_total = $this->get_cart_value();
		$cart_discount = $cart_total * ($oc->prop("web_discount")/100);

		if ($cart_o->prop("postal_price") > 0)
		{
			$total += $cart_o->prop("postal_price");
		}

		if ($oc->prop("show_delivery") and $this->can("view", $oc->prop("delivery_show_controller")))
		{
			$ctrl = get_instance(CL_FORM_CONTROLLER);
			$arr["cart_val_w_disc"] = $cart_total - $cart_discount;
			$delivery_vars = $ctrl->eval_controller($oc->prop("delivery_show_controller"), $oc, &$cart, $arr);
		}
		else
		{
			$delivery_vars = array();
		}
		$this->vars($delivery_vars + array(
			"cart_total" => number_format($cart_total, 2),
			"cart_discount" => number_format($cart_discount, 2),
			"cart_val_w_disc" => number_format($cart_total - $cart_discount, 2),
			"user_data_form" => $html,
			"PROD" => $str,
			"total" => number_format($total, 2),
			"reforb" => $this->mk_reforb("submit_add_cart", array("oc" => $arr["oc"], "update" => 1, "section" => $arr["section"], "from" => "confirm")),
			"postal_price" => number_format($cart_o->prop("postal_price")),
			"clear_cart_url" => $this->mk_my_orb("clear_cart", array("oc" => $arr["oc"])),
			"pay_cart_url" => $this->mk_my_orb("pay_cart", array("oc" => $arr["oc"])),
		));

		if ($cart_o->prop("postal_price") > 0)
		{
			$this->vars(array(
				"HAS_POSTAGE_FEE" => $this->parse("HAS_POSTAGE_FEE")
			));
		}
		$ll = $lln = "";
		if (aw_global_get("uid") != "")
		{
			$ll = $this->parse("logged");
		}
		else
		{
			$lln = $this->parse("not_logged");
		}

		$this->vars(array(
			"logged" => $ll,
			"not_logged" => $lln
		));

		$can_confirm = true;
		if (($imp = aw_ini_get("otto.import")) && $cart["payment_method"] == "rent" && $this->is_template("HAS_RENT"))
		{
			$i = obj($imp);
			$cl_pgs = $this->make_keys(explode(",", $i->prop("jm_clothes")));
			$ls_pgs = $this->make_keys(explode(",", $i->prop("jm_lasting")));
			$ft_pgs = $this->make_keys(explode(",", $i->prop("jm_furniture")));
			$awa = new aw_array($cart["items"]);
			foreach($awa->get() as $iid => $quantx)
			{
				if (!is_oid($iid) || !$this->can("view", $iid))
				{
					continue;
				}
				$pr = obj($iid);
				if ($pr->class_id() == CL_SHOP_PRODUCT_PACKAGING)
				{
					$c = reset($pr->connections_to(array("from.class_id" => CL_SHOP_PRODUCT)));
					$pr = $c->from();
				}
				$i = obj($iid);
				$inst = $i->instance();
				$calc_price = $inst->get_prod_calc_price($i);
				$price = $inst->get_price($i);
				$parent = $pr->parent();
				foreach($quantx as $quant)
				{
					if($quant["items"] < 1)
					{
						continue;
					}
					if($quant["cart"] && !$this->check_confirm_carts($quant["cart"]))
					{
						continue;
					}
					$this->vars(array(
						"prod_html" => $inst->do_draw_product(array(
							"layout" => $layout,
							"prod" => $i,
							"quantity" => $quant["items"],
							"oc_obj" => $oc,
							"is_err" => ($soce_arr[$iid]["is_err"] ? "class=\"selprod\"" : "")
						))
					));

					if (get_class($inst) == "shop_product_packaging")
					{
						$pr_price = ($quant["items"] * $calc_price);
					}
					else
					{
						$pr_price = ($quant["items"] * $price);
					}

					if ( $cl_pgs[$parent] || (!$ft_pgs[$parent] && !$ls_pgs[$parent]))
					{
						$cl_total += $pr_price;
						$cl_str .= $this->parse("PROD");
					}
					else
					if ($ft_pgs[$parent])
					{
						$ft_total += $pr_price;
						$ft_str .= $this->parse("PROD");
					}
					else
					if ($ls_pgs[$parent])
					{
						$ls_total += $pr_price;
						$ls_str .= $this->parse("PROD");
					}
				}
			}

			$npc = max(2,$cart["payment"]["num_payments"]["clothes"]);
			$cl_payment = ($cl_total+($cl_total*($npc)*1.25/100))/($npc+1);
			$cl_tot_wr = ($cl_payment * ($npc+1));

			$ft_npc = max(2,$cart["payment"]["num_payments"]["furniture"]);
			$ft_first_payment = ($ft_total/5);
			$ft_payment = ($ft_total-$ft_first_payment+(($ft_total-$ft_first_payment)*$ft_npc*1.25/100))/($ft_npc+1);
			$ft_total_wr = $ft_payment * ($ft_npc+1) + $ft_first_payment;

			$ls_npc = max(2,$cart["payment"]["num_payments"]["last"]);
			$ls_payment = ($ls_total+($ls_total*($ls_npc)*1.25/100))/($ls_npc+1);
			$ls_total_wr = ($ls_payment * ($ls_npc+1));

			$this->vars(array(
				"PROD_RENT_CLOTHES" => $cl_str,
				"PROD_RENT_FURNITURE" => $ft_str,
				"PROD_RENT_LAST" => $ls_str,
				"total_clothes_price" => number_format($cl_total,2),
				"num_payments_clothes" => $this->picker($npc, array("2" => "2 kuud","3" => "3 kuud", "4" => "4 kuud", "5" => "5 kuud", "6" => "6 kuud")),
				"num_payments_clothes_show" => $npc+1,
				"payment_clothes" => number_format($cl_payment,2),
				"total_clothes_price_wr" => number_format($cl_tot_wr,2),
				"total_furniture_price" => number_format($ft_total,2),
				"first_payment_furniture" => number_format($ft_total/5,2),
				"num_payments_furniture" => $this->picker($ft_npc, array("2" => "2 kuud","3" => "3 kuud", "4" => "4 kuud", "5" => "5 kuud", "6" => "6 kuud","7" => "7 kuud", "8" => "8 kuud", "9" => "9 kuud", "10" => "10 kuud", "11" => "11 kuud", "12" => "12 kuud")),
				"num_payments_furniture_show" => $ft_npc+1,
				"payment_furniture" => number_format($ft_payment,2),
				"total_furniture_price_wr" => number_format($ft_total_wr,2),
				"total_last_price" => number_format($ls_total,2),
				"num_payments_last" => $this->picker($ls_npc, array("2" => "2 kuud","3" => "3 kuud", "4" => "4 kuud", "5" => "5 kuud", "6" => "6 kuud","7" => "7 kuud", "8" => "8 kuud", "9" => "9 kuud", "10" => "10 kuud", "11" => "11 kuud", "12" => "12 kuud")),
				"num_payments_last_show" => $ls_npc+1,
				"payment_last" => number_format($ls_payment,2),
				"total_last_price_wr" => number_format($ls_total_wr,2),
				"total_price_rent" => number_format($cl_tot_wr + $ft_total_wr + $ls_total_wr,2),
				"total_price_rent_w_pst" => number_format($cl_tot_wr + $ft_total_wr + $ls_total_wr + $cart_o->prop("postal_price"),2),
				"postal_price" => number_format($cart_o->prop("postal_price"))
			));

			if ($cart["payment_method"] == "rent" && ($cl_total + $ft_total + $ls_total) < $oc->prop("rent_min_amt"))
			{
				$this->read_template("cart_too_small_for_rent.tpl");
				$this->vars(array(
					"cancel_order" => $this->mk_my_orb("clear_cart", array("oc" => $oc->id()))
				));
				return $this->parse();
			}


			if ($cl_tot_wr + $ft_total_wr + $ls_total_wr > 8000)
			{
				$this->vars(array(
					"RENT_TOO_LARGE" => $this->parse("RENT_TOO_LARGE")
				));
			}
			if ($cl_payment + $ft_payment + $ls_payment < 100)
			{
				$this->vars(array(
					"RENT_TOO_SMALL" => $this->parse("RENT_TOO_SMALL")
				));
				$can_confirm = false;
			}
			if ($cl_tot_wr > 0)
			{
				$this->vars(array(
					"HAS_PROD_RENT_CLOTHES" => $this->parse("HAS_PROD_RENT_CLOTHES"),
				));
			}
			if ($ft_total_wr > 0)
			{
				$this->vars(array(
					"HAS_PROD_RENT_FURNITURE" => $this->parse("HAS_PROD_RENT_FURNITURE"),
				));
			}
			if ($ls_total_wr > 0)
			{
				$this->vars(array(
					"HAS_PROD_RENT_LAST" => $this->parse("HAS_PROD_RENT_LAST"),
				));
			}
			$this->vars(array(
				"HAS_RENT" => $this->parse("HAS_RENT")
			));
			$str = "";
		}
		else
		{
			$this->vars(array(
				"NO_RENT" => $this->parse("NO_RENT")
			));
		}

		if ($can_confirm)
		{
			$this->vars(Array(
				"CAN_CONFIRM" => $this->parse("CAN_CONFIRM")
			));
		}
		return $this->parse();
	}

	function do_insert_user_data_errors($props)
	{
		$errs = new aw_array(aw_global_get("soc_err_ud"));
		$errs = $errs->get();

		$ret = array();
		foreach($props as $pn => $pd)
		{
			if (isset($errs[$pn]))
			{
				$ret[$pn."_err"] = array(
					"name" => $pn."_err",
					"type" => "text",
					"store" => "no",
					"value" => "<font color=red>".$errs[$pn]["msg"]."</font>",
					"no_caption" => 1
				);
			}
			$ret[$pn] = $pd;
		}

		aw_session_del("soc_err_ud");
		return $ret;
	}

	/**

		@attrib name=clear_cart nologin=1

		@param oc required type=int acl=view
	**/
	function orb_clear_cart($arr)
	{
		$oc = obj($arr["oc"]);
		$this->clear_cart($oc);
		return aw_ini_get("baseurl");
	}

	/**
		@attrib name=pay_cart nologin=1
		@param oc required type=int acl=view
	**/
	function pay_cart($arr)
	{
		$oc = obj($arr["oc"]);
		//$this->clear_cart($oc);
		//return aw_ini_get("baseurl");
		$cart_total = $this->get_cart_value();
		$cart_discount = $cart_total * ($oc->prop("web_discount")/100);

		$cart_o = obj($oc->prop("cart"));
		if ($cart_o->prop("postal_price") > 0)
		{
			$cart_total += $cart_o->prop("postal_price");
		}

		$real_sum = $cart_total - $cart_discount;

//iin siis $real_sum on see, mis maksta tuleb. mis pank on valitud, on:

		$soc = get_instance(CL_SHOP_ORDER_CART);
		$cart = $soc->get_cart(obj($oc));

		// process delivery
		if ($oc->prop("show_delivery") and $this->can("view", $oc->prop("cart_value_controller")))
		{
			$ctrl = get_instance(CL_FORM_CONTROLLER);
			$ctrl->eval_controller($oc->prop("cart_value_controller"), $oc, &$cart, &$real_sum);
		}

		$user_data = $cart["user_data"];
		$bank = $user_data["user9"];
		if($oc->prop("bank_id"))
		{
			$bank = $user_data[$oc->prop("bank_id")];
		}
		$bank_lang=$user_data["user10"];
		if($oc->prop("bank_lang"))
		{
			$bank_lang = $user_data[$oc->prop("bank_lang")];
		}

		//if(aw_global_get(uid) == "struktuur"){arr($_SESSION);arr($user_data);arr($GLOBALS);die();}
		$bank_inst = get_instance(CL_BANK_PAYMENT);
		$bank_payment = $oc->prop("bank_payment");

		$order_id = shop_order_cart::do_create_order_from_cart($oc, NULL,array("no_mail" => 1));
		$bank_return = $this->mk_my_orb("bank_return", array("id" =>$order_id), "shop_order");
		$_SESSION["bank_payment"]["url"] = $bank_return;
		$order_obj = obj($order_id);

		$lang = aw_global_get("lang_id");
		$order_obj->set_meta("lang" , $lang);
		$l = get_instance("languages");
		$_SESSION["ct_lang_lc"] = $l->get_langid($_SESSION["ct_lang_id"]);
		$order_obj->set_meta("lang_id" , $_SESSION["ct_lang_id"]);
		$order_obj->set_meta("lang_lc" , $_SESSION["ct_lang_lc"]);
		aw_disable_acl();
		$order_obj->save();
		aw_restore_acl();
//		arr($cart);

		$expl = $order_id;
		if(is_object($oc) && $oc->prop("show_prod_and_package"))
		{
			$expl = substr($expl.$this->get_prod_expl($cart), 0, 69);
		}

		if(is_oid($arr["oc"]) && strlen($expl." (".$arr["oc"].")") < 70)
		{
			$expl.= " (".$arr["oc"].")"; //et tellimiskeskkonna objekt ka naha jaaks
		}

		$ret = $bank_inst->do_payment(array(
			"bank_id" => $bank,
			"amount" => $real_sum,
			"reference_nr" => $order_id,
			"payment_id" => $bank_payment,
			"expl" => $expl,
			"lang"=>$bank_lang,
		));
		return $ret;
	}

	function get_prod_expl($cart)
	{
		$str = "";
		foreach($cart["items"] as $id => $item)
		{
			foreach($item as $data)
			{
				if($data["items"])
				{
					if($this->can("view" , $id))
					{
						$io = obj($id);
						if($io->class_id() == CL_SHOP_PRODUCT_PACKAGING)
						{
							if($prod = reset($io->connections_to(array(
								"from.class_id" => CL_SHOP_PRODUCT,
							))))
							{
								$prod = $prod->from();
								$str.=" ".$prod->name()." - ";
							}
						}
						$str.=" ".$io->name();
					}
				}
			}
		}
		return $str;
	}

	/**
		@attrib name=bank_return nologin=1
		@param id required type=int acl=view
	**/
	function bank_return($arr)
	{
		$order = obj($arr["id"]);
		$order_id = shop_order_cart::do_create_order_from_cart($oc, NULL,array("no_mail" => 1));
		return $this->mk_my_orb("show", array("id" => $order_id), "shop_order");
	}

	function update_user_data_from_order($oc, $wh, $params)
	{
		if (aw_global_get("uid") == "")
		{
			return;
		}
		$cart = $this->get_cart($oc);

		$ud = is_array($_POST["user_data"]) ? $_POST["user_data"] : safe_array($cart["user_data"]);

		$ps_pmap = safe_array($oc->meta("ps_pmap"));
		$org_pmap = safe_array($oc->meta("org_pmap"));

		$u_i = get_instance(CL_USER);
		$cur_p_id = $u_i->get_current_person();
		$cur_p = obj();
		if (is_oid($cur_p_id) && $this->can("view", $cur_p_id))
		{
			$cur_p = obj($cur_p_id);
		}

		$cur_co_id = $u_i->get_current_company();
		$cur_co = obj();
		if (is_oid($cur_co_id) && $this->can("view", $cur_co_id))
		{
			$cur_co = obj($cur_co_id);
		}

		foreach($ud as $pn => $pv)
		{
			if ($key = array_search($pn, $ps_pmap))
			{
				$cur_p->set_prop($key, $pv);
				$p_m = true;
			}
			if ($key = array_search($pn, $org_pmap))
			{
				$cur_co->set_prop($key, $pv);
				$c_m = true;
			}
		}

		if ($p_m)
		{
			aw_disable_acl();
			$cur_p->save();
			aw_restore_acl();
		}

		if ($c_m)
		{
			aw_disable_acl();
			$cur_co->save();
			aw_restore_acl();
		}
	}

	/**
		@attrib name=add_prod_to_cart nologin="1"
		@param oc required type=int acl=view
		@param add_to_cart optional
	**/
	function add_prod_to_cart($arr)
	{
		extract($arr);

		$oc = obj($oc);
		$cart = $this->get_cart($oc);
		$awa = new aw_array($arr["add_to_cart"]);
		foreach($awa->get() as $iid => $quantx)
		{
			if (!is_oid($iid) || !$this->can("view", $iid))
			{
				continue;
			}
			$quantx = new aw_array($quantx);
			foreach($quantx->get() as $x => $quant)
			{
				$cart["items"][$iid][$x]["items"] = $quant;
			}
		}
		$this->set_cart(array(
			"oc" => $oc,
			"cart" => $cart,
		));
		die();
	}

	/**
		@attrib name=add_product nologin="1"
		@param oc required type=int acl=view
		@param product optional
		@param amount optional
		@param return_url optional
		@param section optional
	**/
	public function add_product($arr)
	{
		$order_center = obj($arr["oc"]);
		$cart = $order_center->get_cart();
		$cart_id = $order_center->prop("cart");
		$url = $this->mk_my_orb("show_product", array(
			"cart" => $cart_id,
			"section" => $arr["section"],
			"product" => $arr["product"],
		));
		$url = str_replace("automatweb/orb.aw" , "index.aw" , $url);
		$url = str_replace("orb.aw" , "index.aw" , $url);
		$this->submit_add_cart(array(
			"oc" => $arr["oc"],
			"add_to_cart" => array($arr["product"] => $arr["amount"])
		));
		die($url);
	}

	/**
		@attrib name=show_product nologin="1"
		@param cart required type=int acl=view
		@param product optional
		@param return_url optional
	**/
	public function show_product($arr)
	{
		$cart = obj($arr["cart"]);
		if($cart->prop("product_template"))
		{
			$template = $cart->prop("product_template");
		}
		else
		{
			$template = "show_product.tpl";
		}
		$this->read_template($template);
		lc_site_load("shop", &$this);
		
		$vars = array();
		if($this->can("view" , $arr["product"]))
		{
			$product_object = obj($arr["product"]);
			$vars = $product_object->get_data();

			$vars['special_price_visibility'] = '';
			$vars['PRODUCT_SPECIAL_PRICE'] = '';
			if (!empty($vars['special_price']))
			{
				$vars['special_price_visibility'] = '_specialPrice';
				$this->vars(array(
					'special_price' => number_format($vars['special_price'], 2)
				));
				$vars['PRODUCT_SPECIAL_PRICE'] = $this->parse('PRODUCT_SPECIAL_PRICE');
			}
		}

		if(!empty($arr["return_url"]))
		{
			$vars["return_url"] = $arr["return_url"];
		}
		$vars["amount"] = $cart->get_prod_amount($arr["product"]);
		$this->vars($vars);
		return $this->parse();
	}

	/**
		@attrib name=orderer_data nologin="1"
		@param cart required type=int acl=view
		@param next_view optional
		@param confirm_url optional
		@param section optional
	**/
	public function orderer_data($arr)
	{
		$this->cart = obj($arr["cart"]);
		$this->oc = $this->cart->get_oc();
		if($this->cart->prop("orderer_data_template"))
		{
			$template = $this->cart->prop("orderer_data_template");
		}
		else
		{
			$template = "orderer_data.tpl";
		}
		$this->read_template($template);
		lc_site_load("shop", &$this);

		$this->add_cart_vars();
		$this->add_product_vars();
		$this->add_orderer_vars();
		if($this->oc->prop("show_delivery"))
		{
			$this->add_order_vars();
		}

		$this->vars($arr);
		return $this->parse();
	}

	/**
		@attrib name=order_data nologin="1"
		@param cart required type=int acl=view
		@param next_view optional
		@param confirm_url optional
		@param section optional
	**/
	public function order_data($arr)
	{
		$this->cart = obj($arr["cart"]);
		$this->oc = $this->cart->get_oc();
		if($this->cart->prop("order_data_template"))
		{
			$template = $this->cart->prop("order_data_template");
		}
		else
		{
			$template = "order_data.tpl";
		}
		$this->read_template($template);
		lc_site_load("shop", &$this);

		$this->add_cart_vars();
		$this->add_product_vars();
		$this->add_orderer_vars();
		$this->add_order_vars();
		$this->vars($arr);
		return $this->parse();
	}

	public function add_orderer_vars()
	{
		//load_javascript('validationEngine.jquery.css');
		$data = $this->cart->get_order_data();
		$vars = array("ORDERER_DATA" => "");
		if(!isset($this->oc))
		{
			$this->oc = $this->cart->get_oc();
		}
		$orderer_vars = $this->oc->get_orderer_vars($this);
		$this->orderer_vars_meta = $this->oc->meta("orderer_vars");
		foreach($orderer_vars as $orderer_var => $caption)
		{
			$vars[$orderer_var."_value"] = empty($data[$orderer_var]) ? "" : $data[$orderer_var];
			$vars[$orderer_var."_caption"] = $caption;
			switch($orderer_var)
			{
				case "customer_no":
				case "lastname":
				case "firstname":
				case "address":
				case "index":
				case "city":
				case "email":
				case "homephone":
				case "workphone":
				case "mobilephone":
				case "work":
				case "workexperience":
				case "profession":
				case "personalcode":
					$vars[$orderer_var] = html::textbox(array(
						"name" => $orderer_var,
						"value" => $vars[$orderer_var."_value"],
						"class" => empty($this->orderer_vars_meta["req"][$orderer_var]) ? "" : "validate[required] text",
					));
					break;
				case "birthday":
					if(!empty($data[$orderer_var]))
					{
						$vars["birthday_day_value"] = empty($data[$orderer_var]["day"]) ? t("PP") : $data[$orderer_var]["day"];
						$vars["birthday_month_value"] = empty($data[$orderer_var]["month"]) ? t("KK") : $data[$orderer_var]["month"];
						$vars["birthday_year_value"] = empty($data[$orderer_var]["year"]) ? t("AAAA") : $data[$orderer_var]["year"];
						$vars["birthday_value"] = empty($data[$orderer_var]["day"]) ? "" : date("d.m.Y" , mktime(0,0,0,$data[$orderer_var]["month"],$data[$orderer_var]["day"],$data[$orderer_var]["year"]));
					}
					break;
			}
			if($this->is_template("ORDERER_DATA") || $this->is_template(strtoupper($orderer_var."_SUB")))
			{
				$this->vars(array(
					"caption" => $caption,
					"value" => empty($data[$orderer_var]) ? "" : $data[$orderer_var],
					"var_name" => $orderer_var,
					"REQUIRED" => empty($this->orderer_vars_meta["req"][$orderer_var]) ? "" : $this->parse("REQUIRED"),
					"class" => empty($this->orderer_vars_meta["req"][$orderer_var]) ? "" : "validate[required] text",
				));
				if($this->is_template(strtoupper($orderer_var."_SUB")))
				{
					$this->vars($vars);
					$vars["ORDERER_DATA"].= $this->parse(strtoupper($orderer_var."_SUB"));
				}
				else
				{
					$vars["ORDERER_DATA"].= $this->parse("ORDERER_DATA");
				}
			}
		}


		$this->vars($vars);
	}

	public function add_order_vars()
	{
		$data = $this->cart->get_order_data();
		$this->vars($data);
		$oc = $this->cart->get_oc();
		$payment = $delivery = "";
		$prods =  $this->cart->get_cart();

		$asd = $oc->get_payment_types(array(
			"sum" => $this->cart_sum,
			"currency" => $oc->get_currency(),
			"product" => array(),
			"product_packaging" => array(),
			"validate" => false,
		));

		$self_validate_payment_types = $this->cart->prop("show_only_valid_payment_types") ? false : true;
		$self_validate_delivery_methods = $this->cart->prop("show_only_valid_delivery_methods") ? false : true;
		$payment_types_params = array( 	 
			"sum" => $this->cart_sum,
			"currency" => $oc->get_currency(), 
			"product" => array(),
			"product_packaging" => array(),
			"validate" => !$self_validate_payment_types,
		); 	 
		$asd = $oc->get_payment_types($payment_types_params);

		$method_params = array(
			"product" =>  array_keys($prods["items"]),
			"product_packaging" => array_keys($prods["items"]),
			"validate" => !$self_validate_delivery_methods,
		);
		if(isset($this->categories) && is_array($this->categories))
		{
			$method_params["product_category"] = $this->categories ;
		}
		if(isset($this->products) && is_array($this->products))
		{
			$method_params["product_packaging"] = $this->products;
			$method_params["product"] = $this->products;
		}

		$delivery_methods_object_list = $this->cart->delivery_methods($method_params);

		foreach($asd->arr() as $a => $o)
		{
			$porn = 0;
			$this->vars(array(
				"payment_name" => $o->name(),
				"payment_id" => $a,
				"comment" => $o->comment(),
				"payment_checked" => (!empty($data["payment"]) && $data["payment"] == $a) || ($porn==0 && empty($data["payment"])) ? " checked='checked' " : " ",
			));

// 			if($self_validate_payment_types)
// 			{
// 				$payment.= $this->parse("PAYMENT".(is_oid($o->valid_conditions($payment_types_params)) ? "" : "_DISABLED")); 
// 			}
// 			else
// 			{
// 				$payment.= $this->parse("PAYMENT");
// 				$porn++;
// 			}

			$condition = $o->valid_conditions(array(
				"sum" => $this->cart_sum,
				"currency" => $oc->get_currency(),
				"product" => array(),
				"product_packaging" => array(),
			));
			if(is_oid($condition))
			{
				$payment .= $this->parse("PAYMENT");
				$porn++;
			}
			else
			{
				$payment .= $this->parse("PAYMENT_DISABLED");
			}

			$condition_object = obj($condition);
			foreach($condition_object->properties() as $key => $prop)
			{
				$this->vars(array("condition_".$key => $prop));
			}
//$stuff = "professional ganja smoker";print "<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>" .$stuff;
			$DEFERRED_PAYMENT = "";
			$step = $condition_object->prop("period_step") > 0 ? $condition_object->prop("period_step") : 1;
			$min = $condition_object->prop("period_min");
			$max =  $condition_object->prop("period_max") > 0 ? $condition_object->prop("period_max") : 1;
			while($min <= $max)
			{
				$this->vars(array(
					"deferred_payment_selected" => (!empty($data["deferred_payment_count"]) && $data["deferred_payment_count"] == $min) ? 'selected="selected"' : "",
					"value" => $min
				));
				$DEFERRED_PAYMENT.= $this->parse("DEFERRED_PAYMENT");
				$min = $min + $step;
			}
			$this->vars(array(
				"deferred_payment_selected" => empty($data["deferred_payment_count"]) ? 'selected="selected"' : "",
				"value" => ""
			));
			$DEFERRED_PAYMENT.= $this->parse("DEFERRED_PAYMENT");
		}
		$this->vars(array(
			"DEFERRED_PAYMENT" => $DEFERRED_PAYMENT,  
			"PAYMENT" => $payment,
			"payment_name" => empty($data["payment"]) ? " " : get_name($data["payment"]),
			"payment_value" => empty($data["payment"]) ? "" : $data["payment"],
		));

		$porno = 0;//lihstalt count
		foreach($delivery_methods_object_list->arr() as $id => $o)
		{
			$sel_delivery = (!empty($data["delivery"]) && $data["delivery"] == $id) || ($porno==0 && empty($data["delivery"]));
			$this->vars(array(
				"delivery_name" => $o->name(),
				"delivery_id" => $id,
				"delivery_checked" => $sel_delivery ? " checked='checked' " : " ",
				"delivery_price" => $o->get_shop_price($oc),
			));
			if($sel_delivery)
			{
				$this->delivery_sum = $o->get_shop_price($oc);
			}
			if($self_validate_delivery_methods)
			{
				//misasi see $delivery_methods_params on? (Marko)
				//	Hea kysimus. Veel parem kysimus on, mis see OLI?
				//	Sa kirjutasid mu muudatused millalgi yle ja neid taastadedes j2i see j2relikult taastamata.
				//	Ilmselt on see see sama, millega need k2ttesaamisviisid alguses kysitakse.
				//	Niiet prolly $delivery_methods_params = $method_params;		-kaarel 15.10.2009


//				$delivery.=$this->parse("DELIVERY".($o->valid($delivery_methods_params) ? "" : "_DISABLED"));
				$delivery .= $this->parse("DELIVERY".($o->valid($method_params) ? "" : "_DISABLED"));

//				if($o->valid($delivery_methods_params))
				if($o->valid($method_params))
				{
					$porno++;
				}
			}
			else 	 
			{ 	 
				$delivery.=$this->parse("DELIVERY"); 	
				$porno++;  
			}

		}

		$this->vars(array(
			"delivery_sum" => $this->delivery_sum,
		));	

		$sp = new object_list(array(
			"class_id" => CL_SMART_POST,
		));
		$smart_post = $sp->begin();
		$county = "";
		$smartpost_active = array();
		$n = 0;
		if($smart_post)
		{		
			foreach($smart_post->get_automates_by_city() as $name =>  $pask)
			{
				$n++;
				$city_selected = !empty($data["county"]) && $data["county"] == $n;
				$this->vars(array(
					"county_name" => $name,
					"county_id" => $n,
					"county_selected" => $city_selected ? 'selected="selected"' : "",
				));
				if($city_selected || $n == 1)
				{
					$smartpost_active = $pask;
				}
				$county.= $this->parse("COUNTY");
			}
		}
		$SMARTPOST_SELL_PLACE = "";
		foreach($smartpost_active as $id => $values)
		{
			$this->vars(array(
				"smartpost_name" => $values["NAME"],
				"smartpost_value" => $id,
				"smartpost_sell_place_selected" => !empty($data["smartpost_sell_place"]) &&  $data["smartpost_sell_place"] == $id ? 'selected="selected"' : "",
			));
			$SMARTPOST_SELL_PLACE.= $this->parse("SMARTPOST_SELL_PLACE");
		}

		$post_office_list = new object_list(array(
			"class_id" => CL_POST_OFFICE,
			new obj_predicate_sort(array(
				"county.ord" => "asc",
				"county.name" => "asc",
				"ord" => "asc",
				"name" => "asc",
			)),
		));
			
		$POST_OFFICE_SELL_PLACE = "";
		$previous_post_office_county = NULL;
		foreach($post_office_list->arr() as $post_office)
		{
			if($previous_post_office_county != $post_office->prop("county"))
			{
				$this->vars(array(
					"post_office_county" => $post_office->prop("county"),
					"post_office_county_name" => $post_office->prop("county.name")
				));
				$previous_post_office_county = $post_office->prop("county");
				$POST_OFFICE_SELL_PLACE .= $this->parse("POST_OFFICE_SELL_PLACE_NAME");
			}
			
			$this->vars(array(
				"post_office_value" => $post_office->id(),
				"post_office_name" => $post_office->prop("name"),
				"post_office_place_selected" => !empty($data["post_office_sell_place"]) &&  $data["post_office_sell_place"] == $post_office->id() ? 'selected="selected"' : "",
			));
			$previous_post_office_county = $post_office->prop("county");
			$POST_OFFICE_SELL_PLACE .= $this->parse("POST_OFFICE_SELL_PLACE");
		}

//---------- kui miskit suva porno checkboxi v6i radiobuttoni muutujat vaja kasutada, mis on templeidis, siis siit annaks v22rtus

		$checkbox_vars = array("client_status");
		foreach($checkbox_vars as $checkbox_var)
		{
			if(isset($data[$checkbox_var]))
			{
				$this->vars(array($checkbox_var."_".$data[$checkbox_var]."_checked"  => "checked=\"checked\""));
			}
			else
			{
				$this->vars(array($checkbox_var."_2_checked"  => "checked=\"checked\""));
			}
		}
		
		$this->vars(array(
			"DELIVERY" => $delivery,
			"delivery_name" => empty($data["delivery"]) ? " " : get_name($data["delivery"]),
			"delivery_value" => empty($data["delivery"]) ? "" : $data["delivery"],
			"COUNTY" => $county,
			"SMARTPOST_SELL_PLACE" => $SMARTPOST_SELL_PLACE,
			"POST_OFFICE_SELL_PLACE" => $POST_OFFICE_SELL_PLACE,
			"POST_OFFICE_SELL_PLACE_NAME" => "",
		));

		if(isset($data["delivery"]) && $this->can("view" , $data["delivery"]))
		{
			$delivery = obj($data["delivery"]);
			$this->vars($delivery->get_vars($data));
		}

		//j2relmaksu jaoks SUB


		if(!empty($data["payment"]) && $this->can("view" , $data["payment"]))
		{
			$payment = obj($data["payment"]);
			$condition = $payment->valid_conditions(array(
				"sum" => $this->cart_sum,
				"currency" => $oc->get_currency(),
				"product" => array(),
				"product_packaging" => array(),
			));
			if(is_oid($condition) && $this->can("view", $condition))
			{
				$c = obj($condition);
				$rent = $c->calculate_rent($this->cart_sum,$data["deferred_payment_count"]);

				if($rent["single_payment"])
				{
					$this->vars(array(
						"deferred_payment_price" => $rent["single_payment"],
					));
					$this->total_sum =  $rent["sum_rent"];
				}

				if($c->prop("prepayment_interest") != 100)
				{//print "<br><br><br><br><br>minge munni!!!!!, kopp on ees sest jamast!";
					$this->vars(array(
						"HAS_LEASE_PURCHASE" => $this->parse("HAS_LEASE_PURCHASE"),
					));
				}
			}
		}

	}

	private function add_product_vars()
	{
		$cart = $this->cart->get_cart();
		$vars = array();
		$product_str = "";
		$cart_total = 0;
		$items = 0;
		foreach($cart["items"] as $iid => $quantx)
		{
			if(!is_oid($iid) || !$this->can("view", $iid))
			{
				continue;
			}
			$product = obj($iid);
			foreach($quantx as $x => $quant)
			{
				if($quant["items"] < 1)
				{
					continue;
				}
				$items ++;
	
				$vars = $product->get_data();
				$special_price = $product->get_shop_special_price($this->oc->id());
				$vars['PRODUCT_SPECIAL_PRICE'] = '';
				$vars['special_price_visibility'] = '';
				if (!empty($special_price))
				{
					$sum = $quant["items"] * $special_price;
					$vars['special_price_visibility'] = '_specialPrice';
					$vars['special_price'] = $special_price;
					$this->vars(array(
						'special_price' => number_format($special_price, 2),
						'unformated_special_price' => $special_price
					));
					$vars['PRODUCT_SPECIAL_PRICE'] = $this->parse('PRODUCT_SPECIAL_PRICE');
				}
				else
				{
					$price = $product->get_shop_price($this->oc->id());
					$sum = $quant["items"] * $price;
					$vars['special_price_visibility'] = '';
					$vars['PRODUCT_SPECIAL_PRICE'] = '';
					$vars['special_price'] = $price;
				}
				
				$vars["amount"] = $quant["items"];

				$price = $product->get_shop_price($this->oc->id());
				$vars["price"] = number_format($price , 2 , "." , "");
				$vars["unformated_price"] = $price;
				$vars['unformated_special_price'] = $vars['special_price'];
				$vars['special_price'] = number_format($vars['special_price'] , 2, "." , "");
				$vars["unformated_total_price"] = $sum;
				$vars["total_price"] = number_format($sum , 2, "." , "");
				$vars["total_price_without_thousand_separator"] = $sum;//see yleliigne vast nyyd

				$vars["remove_url"] = $this->mk_my_orb("remove_product" , array("cart" => $this->cart->id(), "product" => $iid));
				$this->vars($vars);
//arr($vars);
				$subs = array();
				foreach($vars as $key => $val)
				{
					if($this->is_template("HAS_".strtoupper($key)))
					{
						if($val)
						{
							$subs["HAS_".strtoupper($key)] = $this->parse("HAS_".strtoupper($key));
						}
						else
						{
							$subs["HAS_".strtoupper($key)] = "";
						}
					}
				}
				$this->vars($subs);

				$product_str.= $this->parse("PRODUCT");
				$cart_total += $sum;
			}
		}
		$this->product_count = $items;
		$this->cart_sum = $cart_total;
		$this->vars(array(
			"PRODUCT" => $product_str,
			"unformated_cart_total" => $cart_total,
			"cart_total" => number_format($cart_total , 2, "." , ""),
		));
	}

	public function add_cart_vars()
	{
		$vars = array();
		$vars["cart"] = $this->cart->id();
		$this->oc= $this->cart->get_oc();
		$this->vars($vars);
	}

	/**
		@attrib name=submit_order_data nologin="1" all_args=1
		@param next_action optional type=string
		@param confirm_url optional type=string
		@param section optional type=string
			for some other confirm view 
	**/
	public function submit_order_data($arr)
	{
		$cart = obj($arr["cart"]);
		$cart -> set_order_data($arr);

		if($arr["next_action"])
		{
			$action = $arr["next_action"];
		}
		else
		{
			$action = "final_finish_order";
		}
		$return_data = array(
			"oc" => $arr["oc"],
			"cart" => $arr["cart"],
			"section" => $arr["section"],
		);
		if(!empty($arr["confirm_url"]))
		{
			$return_data["confirm_url"] = $arr["confirm_url"];
		}

//isikuandmeid jne tihti tahetakse yle ssh, siis t6en2oliselt tahetakse vastust ka selliselt, kud on need isikuandmed tihti n2ha
		$return = $this->mk_my_orb($action, $return_data);
		if(substr($_SERVER["SCRIPT_URI"],0,8) == "https://")
		{
			$return = str_replace("http://" ,  "https://" , $return);
		//	if(aw_global_get("uid") == "struktuur.markop"){arr($return);}
		}
//if(aw_global_get("uid") == "struktuur.markop"){arr($return);die();}
		return $return; 
	}

	/**
		@attrib name=remove_product nologin="1" params=name
		@param cart required type=oid
		@param product optional type=oid
	**/
	public function remove_product($arr)
	{
		ignore_user_abort(true);
		$cart = obj($arr["cart"]);
		$cart -> remove_product($arr["product"]);
		ignore_user_abort(false);
		die("1");
	}

	/**
		@attrib name=confirm_order nologin="1" params=name
		@param cart required type=oid
	**/
	public function confirm_order($arr)
	{
		$cart = obj($arr["cart"]);
		$cart-> set_order_data($arr);
		$order_data = $cart->get_order_data();
		$oc = $cart->get_oc();
		if($oc->prop("use_bank_payment"))
		{
			if($order_data["bank"])
			{
				return $this->pay_cart(array(
					"oc" => $oc->id(),
				));
			}
			else
			{
				return $cart->get_pay_form();
			}
		}
		$order = $cart->confirm_order();
		$url = aw_global_get("baseurl")."/".$order->id();
		if(substr($_SERVER["SCRIPT_URI"],0,8) == "https://")
		{
			$url = str_replace("http://" ,  "https://" , $url);
		}
		header("Location: ".$url);
		die();
	}


	private function _format_calc_price($p)
	{
		return (double)str_replace(",", "", $p);
	}
}
?>
