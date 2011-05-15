<?php

class shop_order_cart_obj extends _int_object
{
	/**
		@attrib name=price params=name

		@param product required type=int acl=view
			The OID of the product
		@param amount optional type=float default=1
			The amount of the product prices are asked for
		@param product_category optional type=array/int acl=view
			OIDs of product categories
		@param product_packet optional type=array/int acl=view
			OIDs of product packets
		@param customer_data optional type=int acl=view
			OID of customer_data object
		@param customer_category optional type=array/int acl=view
			OIDs of customer categories.
		@param location optional type=array/int acl=view
			OIDs of locations
		@param validate optional type=boolean default=true
			If set to false, delivery methods will not be validated, therefore all delivery methods will be returned

	**/
	public function delivery_methods($arr = array())
	{
		enter_function("shop_order_center_obj::delivery_mehtods");
		$ol = new object_list(array(
			"class_id" => CL_SHOP_DELIVERY_METHOD,
			"CL_SHOP_DELIVERY_METHOD.RELTYPE_DELIVERY_METHOD(CL_SHOP_ORDER_CART)" => $this->id(),
			"lang_id" => array(),
			"site_id" => array(),
			new obj_predicate_sort(array(
				"jrk" => "ASC"
			)),
		));
		// Validate
		if(!isset($arr["validate"]) || !empty($arr["validate"]))
		{
			if(is_object($o = $this->get_shop_order_center()))
			{
				$customer_data = $o->get_customer_data();
				if(is_object($customer_data))
				{
					$arr = array_merge(array(
						"customer_data" => $customer_data->id(),
						"customer_category" => array(),//$customer_data->get_customer_categories()->ids(),
						"location" => array(),//$customer_data->get_locations()->ids(),
					), $arr);
				}
			}
			foreach($ol->arr() as $o)
			{
				if(!$o->valid($arr))
				{
					$ol->remove($o->id());
				}
			}
		}
		exit_function("shop_order_center_obj::delivery_mehtods");
		return $ol;
	}

	public function get_shop_order_center()
	{
		$ol = new object_list(array(
			"class_id" => CL_SHOP_ORDER_CENTER,
			"cart" => $this->id(),
			"lang_id" => array(),
			"site_id" => array(),
		));
		return $ol->begin();
	}

	function prop($prop)
	{
		switch($prop["name"])
		{
			case "cart_type":
				$oc = $this->get_oc();
				return $oc->prop("cart_type");
		}
		return parent::prop($prop);
	}

	public function set_oc()
	{
		if(empty($this->oc))
		{
			$ol = new object_list(array(
				"class_id" => CL_SHOP_ORDER_CENTER,
				"lang_id" => array(),
				"cart" => $this->id(),
				"site_id" => array(),
			));
			$ids = $ol->ids();
			$this->oc = obj(reset($ids));
			if(!is_oid($this->oc->id()))
			{
				$ol = new object_list(array(
					"class_id" => CL_SHOP_ORDER_CENTER,
					"lang_id" => array(),
					"site_id" => array(),
				));
				$ids = $ol->ids();
				$this->oc = obj(reset($ids));
				if(!is_oid($this->oc->id()))
				{	
					error::raise(array(
						"id" => "ERR_NO_WAREHOOS",
						"msg" => sprintf(t("shop_order_cart_obj::creat_osell_order(): no order_center set for cart %s!"), $this->id())
					));
				}
			}
		}
	}

	/**	returns cart order center object
		@attrib api=1
	**/
	public function get_oc()
	{
		$this->set_oc();
		return $this->oc;
	}

	/**
		@attrib name=get_price api=1 params=pos
		@param product optional type
			The OID of the shop_order_center object the prices are asked for. If not given, no price list will be applied!
	**/
	public function get_prod_amount($product)
	{
		$cart = $this->get_cart();
		$items = safe_array($cart["items"]);
		foreach($items as $prod => $val)
		{
			if($prod = $product)
			{
				return $val[0]["items"];
			}
		}
		return 0;
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
	public function set_cart($cart)
	{
		if($this->prop("cart_type") == 1 && aw_global_get("uid") != "")
		{
			$user = obj(aw_global_get("uid_oid"));
			$user->set_meta("shop_cart", $cart);
			$user->save();
		}
		$_SESSION["cart"] = $cart;
	}

	/**
		@attrib api=1
	**/
	public function set_order_data($arr)
	{
		foreach($arr as $key => $val)
		{
			$_SESSION["cart"]["order_data"][$key] = $arr[$key];
			$_SESSION["cart"]["user_data"][$key] = $arr[$key];//ajutiselt, et vanemates funktsioonides need muutujad ka sisse saaks
		}
	}


	/** resets cart
		@attrib api=1
	**/
	public function reset_cart()
	{
		if(!empty($_SESSION["cart"]))
		{
			unset($_SESSION["cart"]);
		}
	}

	/**
		@attrib api=1
	**/
	public function get_order_data()
	{
		return empty($_SESSION["cart"]["order_data"]) ? array() : $_SESSION["cart"]["order_data"];
	}

	/** makes new warehouse sell order object
		@attrib api=1
	**/
	public function create_order()
	{
		$this->set_oc();
		if (!is_oid($this->oc->prop("warehouse")))
		{
			error::raise(array(
				"id" => "ERR_NO_WAREHOOS",
				"msg" => sprintf(t("shop_order_cart::do_creat_order_from_cart(): no warehouse set for ordering center %s!"), $this->oc->id())
			));
		}
		$warehouse = $this->oc->prop("warehouse");
		$cart = $this->get_cart();
		$order_data = $this->get_order_data();
//kui mingi imega suudetakse isikuandmeteta tellimus teha... suuname tagasi
		if(empty($order_data["email"]) && empty($order_data["fisrsname"]) && empty($order_data["lastname"]))
		{
			$return_url = str_replace("final_finish_order" , "orderer_data", $GLOBALS["_SERVER"]["HTTP_REFERER"]);
			   header( 'Location: '.$return_url);
			   die();
		}
		$o = new object();
		$o->set_name(t("M&uuml;&uuml;gitellimus")." ".date("d.m.Y H:i"));
		$o->set_parent($this->oc->id());
		$o->set_class_id(CL_SHOP_SELL_ORDER);
		$o->set_prop("warehouse" , $warehouse);
		$o->set_prop("date" , time());

		$person = $this->_get_person($order_data);
		$o->set_prop("purchaser" , $person->id());
		$o->set_prop("buyer_rep" , $person->id());

		$address = $this->_get_address($order_data);
		$o->set_prop("delivery_address" , $address->id());

		$o->set_prop("transp_type" , $order_data["delivery"]);
		$o->set_prop("shop_delivery_type" , $order_data["delivery"]);

		//kui valitud transpordiliik omab oma miskeid kontoreid v6i kohti kuhu viia, siis salvestab selle aadressi ka
		$ed_types = $this->oc->prop("extra_address_delivery_types");

		if(is_array($ed_types) && sizeof($ed_types) && in_array($order_data["delivery"], $ed_types))
		{
			$delivery = obj($order_data["delivery"]);
			$delivery_vars = $delivery->get_vars($order_data);
			$o->set_prop("smartpost_sell_place_name" , $delivery_vars["smartpost_sell_place_name"]);
		}

		$o->set_prop("payment_type" , $order_data["payment"]);
		$o->set_prop("currency" , $this->oc->get_currency());
		$o->set_prop("channel" , $this->prop("channel"));
		$o->set_meta("order_data" , $order_data);

		//seda keele v2rki peaks tegelt kontrollima, et kas niipalju l2bu on ikka vaja... asi selleks, et kirja saadaks vastavas keeles tellijale
		$lang = aw_global_get("lang_id");
		$l = get_instance("languages");
		$o->set_meta("lang" , $lang);
		$o->set_meta("lang_id" , $_SESSION["ct_lang_id"]);
		$o->set_meta("lang_lc" , $l->get_langid($_SESSION["ct_lang_id"]));

		$o->save();

		$awa = new aw_array($cart["items"]);
		$sum = 0;
		foreach($awa->get() as $iid => $quant)
		{
			$qu = new aw_array($quant);
			foreach($qu->get() as $key => $val)
			{
				if($val["cart"] && !$this->check_confirm_carts($val["cart"]) && $iid)
				{
					continue;
				}

				// If the quantity of an item is 0, then don't add it to order:
				if ($cart["items"][$iid][$key]["items"] == 0)
				{
					continue;
				}
				$product = obj($iid);
				$special_price = $product->get_shop_special_price($this->oc->id());
				if (!empty($special_price))
				{
					$price = $special_price;
				}
				else
				{
					$price = $product->get_shop_price($this->oc->id());
				}
				$sum += $cart["items"][$iid][$key]["items"] * $price;
				$o->add_row(array(
					"product" => $iid,
					"amount" => $cart["items"][$iid][$key]["items"],
					"price" => $price,
				));
			}
		}

		//j2relmaksude arv, juhul kui tegu on j2relmaksuga
		if($this->is_after_payment($order_data["payment"] , $sum))
		{
			$o->set_prop("deferred_payment_count" , $order_data["deferred_payment_count"]);
			$o->save();
		}

		return $o->id();
	}

	//tagastab summa ja maksetyybi objekti kohta, kas on j2relmaksuga tegu
	private function is_after_payment($payment, $sum)
	{
		if(!empty($payment))
		{
			$payment = obj($payment);
			$condition = $payment->valid_conditions(array(
				"sum" => $sum,
				"currency" => $this->oc->get_currency(),
				"product" => array(),
				"product_packaging" => array(),
			));
			if($this->can("view" , $condition))
			{
				$c = obj($condition);
				if($c->prop("prepayment_interest") != 100)
				{
					return true;
				}
			}
		}
		return false;
	}

	public function _get_person($data)
	{
		$person = "";

		//kui yldse selliseid andmeid pole mille j2rgi isikut idendifitseerida... v6tab kasutaja isiku
		if(empty($data["firstname"]) && empty($data["lastname"]) && empty($data["birthday"]) && empty($data["personalcode"]) && empty($data["customer_no"]))
		{
			$person = get_current_person();
		}
		//sellisel juhul otsib olemasolevate isikute hulgast, kui on andmeid mille j2rgi otsida
		/*	This is causing incorrect client data to be found if a customer mistypes his/her client code
		if(
			!empty($data["personalcode"]) || 
			!empty($data["customer_no"]) || 
			(!empty($data["birthday"]) && !empty($data["lastname"])))
		{
			$filter = array(
				"class_id" => CL_CRM_PERSON,
				"site_id" => array(),
				"lang_id" => array(),
			);
			if(!empty($data["personalcode"]))
			{
				$filter["personal_id"] = $data["personalcode"];
			}
			if(!empty($data["customer_no"]) && is_numeric($data["customer_no"]))
			{
				$filter["external_id"] = $data["customer_no"];
			}
			else
			{
				if(!empty($data["firstname"]))
				{
					$filter["firstname"] = $data["firstname"];
				}
				if(!empty($data["lastname"]))
				{
					$filter["lastname"] = $data["lastname"];
				}
				if(!empty($data["birthday"]))
				{
					if(is_array($data["birthday"]))
					{
						$filter["birthday"] = mktime(0,0,0,$data["birthday"]["month"],$data["birthday"]["day"],$data["birthday"]["year"]);
					}
				}
			}
			if(sizeof($filter) > 3)
			{
				$ol = new object_list($filter);

				if($ol->count())
				{
					$person = $ol->begin();
				}
			}
		}
		*/

		if(!is_object($person))
		{
			$person = new object();
			$person->set_class_id(CL_CRM_PERSON);
			$person->set_parent($this->oc->id());
			$person->set_name($data["firstname"]." ".$data["lastname"]);
			$person->set_prop("firstname" , $data["firstname"]);
			$person->set_prop("lastname" , $data["lastname"]);
			if(!empty($data["personalcode"]))
			{
				$person->set_prop("personal_id" , $data["personalcode"]);
			}

			if(!empty($data["customer_no"]))
			{
				$person->set_prop("external_id" , $data["customer_no"]);
			}
		}

		if(!empty($data["birthday"]))
		{
			if(is_array($data["birthday"]))
			{
				$person->set_prop("birthday" , mktime(0,0,0,$data["birthday"]["month"],$data["birthday"]["day"],$data["birthday"]["year"]));
			}
		}

		$person->save();

		if(!empty($data["email"]))
		{
			$person->set_email($data["email"]);
		}
		if(!empty($data["mobilephone"]))
		{
			$person->set_phone($data["mobilephone"]);
		}
		if(!empty($data["homephone"]))
		{
			$person->set_phone($data["homephone"], "home");
		}
		if(!empty($data["workphone"]))
		{
			$person->set_phone($data["workphone"], "work");
		}
		return $person;
	}

	public function _get_address($data)
	{
		$address = new object();
		$address->set_parent($this->oc->id());
		$address->set_name($data["address"]." ".$data["city"]);
		$address->set_class_id(CL_CRM_ADDRESS);
		$address->set_prop("aadress" ,$data["address"]);
		if(!empty($data["index"]))
		{
			$address->set_prop("postiindeks",$data["index"]);
		}
		$address->save();
		if(!empty($data["city"]))
		{
			$address->set_city($data["city"]);
		}
		return $address;
	}

	public function confirm_order()
	{
		$order = $this->create_order();
		$this->set_oc();
		$order_obj = obj($order);
		$order_obj->set_prop("order_status" , "5");
		$order_obj->save();

		if($this->can("view", $this->prop("finish_handler")))
		{
			$ctrl = get_instance(CL_FORM_CONTROLLER);
			$ctrl->eval_controller($this->prop("finish_handler"), &$order_obj, &$this);
		}

		$this->oc->send_confirm_mail($order);
		$this->reset_cart();
		return $order_obj;
	}

	public function get_pay_form()
	{
		$order = $this->create_order();
		$this->reset_cart();
		$oc = $this->get_oc();
		$order = obj($order);
		$order->set_prop("order_status" , "0");

		$bank_payment_inst = get_instance(CL_BANK_PAYMENT);
		$bank_payment = $oc->get_bank_payment_id();
		$expl = $order->id();

		if($oc->prop("show_prod_and_package"))
		{
			$expl = substr($expl." ".join(", " , $order->get_product_names()), 0, 69);
		}
		if(strlen($expl." (".$oc->id().")") < 70)
		{
			$expl.= " (".$oc->id().")"; //et tellimiskeskkonna objekt ka naha jaaks
		}
		return $bank_payment_inst->bank_forms(array(
			"id" => $bank_payment,
			"amount" => $order->get_sum(),
			"reference_nr" => $order->id(),
			"lang" => empty($order_data["bank_lang"]) ? "" : $order_data["bank_lang"],
			"expl" => $expl,
		));
	}

	public function remove_product($product)
	{
		$cart = $this->get_cart();
		if(isset($cart["items"][$product]))
		{
			$cart["items"][$product] = null;
		}
		$this->set_cart($cart);
	}


}

?>
