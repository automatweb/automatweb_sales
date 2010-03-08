<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/orders/orders_form.aw,v 1.31 2009/08/22 20:32:04 markop Exp $
// $Header: /home/cvs/automatweb_dev/classes/applications/orders/orders_form.aw,v 1.31 2009/08/22 20:32:04 markop Exp $
// orders_form.aw - Tellimuse vorm 
/*

@classinfo syslog_type=ST_ORDERS_FORM relationmgr=yes maintainer=markop

@default table=objects
@default group=general
@default field=meta
@default method=serialize


@property postal_fee type=textbox
@caption Postikulu

@property orders_post_to type=textbox
@caption Mail kuhu tellimus saata

@property add_attach type=checkbox ch_value=1
@caption Lisa tellimus manusena

@property orders_to_mail type=checkbox ch_value=1
@caption Saada e-mail tellijale

@property orders_post_from type=relpicker reltype=RELTYPE_MAIL_ADDRESS
@caption Kliendile saatja (e-mail)

@property no_pdata_check type=checkbox ch_value=1
@caption Kasutajaandmed isikust

@property order_center type=relpicker reltype=RELTYPE_ORDER_CENTER store=connect
@caption Tellimiskeskkond



@property channel type=relpicker reltype=RELTYPE_CHANNEL store=connect
@caption M&uuml;&uuml;gikanal


@groupinfo mails caption=Meiliseaded 
@default group=mails

@property mail_subject type=textbox
@caption Maili subjekt

@property mail_from type=textbox
@caption Mail kellelt

@property mail_from_address type=textbox
@caption Mail kellelt aadress





@groupinfo config caption=Seaded 
@default group=config

@property orderform type=relpicker reltype=RELTYPE_ORDERFORM
@caption Tellimuse seadetevorm

@property itemform type=relpicker reltype=RELTYPE_ITEMFORM
@caption Tellimuse rea seadetevorm

@property num_rows type=textbox size=6
@caption Mitu rida saab korraga korvi lisada

@property ordemail type=relpicker reltype=RELTYPE_MAIL
@caption Mail tellijale

@property thankudoc type=relpicker reltype=RELTYPE_THANKU
@caption Dokument kuhu suunata peale esitamist

@property order_item_template type=select
@caption Tellimuse kujundusp&otilde;hi

@property orders_form_template type=select
@caption Tellija andmete kujundusp&otilde;hi

@property confirm_template type=select
@caption Kinnitusvaate templeit


@groupinfo ordering caption=Tellimine submit=no
@property ordering type=callback group=ordering no_caption=1 callback=do_order_form



@groupinfo payment caption=Makseviisid
@default group=payment

@property has_rent type=checkbox ch_value=1 
@caption Saab maksta j&auml;relmaksuga

@property rent_min_amt type=textbox size=6
@caption J&auml;relmasu min. summa

@property rent_min_amt_payment type=textbox size=6
@caption &Uuml;he makse miinimumsumma

@property rent_min_amt_payment_text type=textbox
@caption Miinimumsumma veateade

@property rent_max_amt_warn type=textbox size=6
@caption J&auml;relmaksu maksimaalne summa

@property rent_max_amt_warn_text type=textbox
@caption Maksimaalse summa &uuml;letamise hoiatus

@property rent_item_types type=table 
@caption Makseperioodid


@reltype ORDERFORM value=1 clid=CL_CFGFORM
@caption Tellimuse seadetevorm

@reltype ITEMFORM value=2 clid=CL_CFGFORM
@caption Tellimuse rea seadetevorm

@reltype THANKU value=3 clid=CL_DOCUMENT
@caption Dokument

@reltype ADDORDER value=4 clid=CL_ORDERS_ITEM
@caption Tellimuse lisa

@reltype MAIL value=5 clid=CL_MESSAGE
@caption Mail

@reltype MAIL_ADDRESS value=6 clid=CL_ML_MEMBER
@caption Maili aadress

@reltype ORDER_CENTER value=7 clid=CL_SHOP_ORDER_CENTER
@caption Tellimiskeskkond

@reltype RELTYPE_CHANNEL value=12 clid=CL_WAREHOUSE_SELL_CHANNEL
@caption M&uuml;&uuml;gikanal

*/

class orders_form extends class_base
{
	function orders_form()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"clid" => CL_ORDERS_FORM,
			"tpldir" =>  "applications/orders",
		));
		$this->prod_statuses = array(
			"" => "t&auml;psustamisel",
			NULL => "t&auml;psustamisel",
			0 => "puudub",
			1 => "laos",
			2 => "pikk tarnet&auml;htaeg"
		);
	}

	//////
	// class_base classes usually need those, uncomment them if you want to use them
	/*
	function do_order_form($arr)
	{
		$retval[] = array(
			"name" => "order_form_id",
			"type" => "hidden",
			"value" => $arr["obj_inst"]->id(),
		);
		$retval[] = array(
			"name" => "shop_cart",
			"type" => "text",
			"value" => $this->do_shop_cart($arr),
			"no_caption" => 1,
			
		);
		
		return $retval;
	}*/
	
	

	/**
		@attrib name=delete_from_order nologin=1
		@param id required type=int acl=delete
	**/
	function delete_from_order($arr)
	{
		if(is_oid($arr["id"]) && $this->can("delete", $arr["id"]) && $this->can("view", $arr["id"]))
		{
			$obj = &obj($arr["id"]);
			$obj->delete();
		}
		return aw_ini_get("baseurl")."/".$_SESSION["orders_section"];
	}
	
	function do_shop_cart($arr)
	{	
		aw_global_set("no_cache", 1);
		if(!$_SESSION["order_cart_id"])
		{
			$order = new object(array(
				"class_id" => CL_ORDERS_ORDER,
				"parent" => $arr["obj_inst"]->id(),
			));
			$order->save();
			$_SESSION["order_cart_id"] = $order->id();
			$_SESSION["order_form_id"] = $arr["obj_inst"]->id();
			if($conns = $arr["obj_inst"]->connections_from(array("type" => "RELTYPE_ADDORDER")))
			{
				foreach ($conns as $conn)
				{
					$order->connect(array(
						"to" => $conn->prop("to"),
						"reltype" => "RELTYPE_ADDORDER"
					));
				}
			}
		}
		else
		{
			$order = &obj($_SESSION["order_cart_id"]);
		}
		
		$orders_inst = get_instance(CL_ORDERS_ORDER);
		$order->save();
		if($arr["request"]["persondata"] == 1)
		{
			$subgroup = "orderinfo";
						
			$cfgform = $arr["obj_inst"]->prop("orderform");

		}
		else
		{
			$subgroup = "orderitems";
		}
		
		
		$retval = $orders_inst->change(array(
			"class" => "orders_order",
			"group" => $subgroup,
			"id" => $order->id(),
			"cb_part" => 1,
			"cfgform" => $cfgform,
		));
		
		return $retval;
	}
	
	public function get_order()
	{
		return empty($_SESSION["order"]) ? array() : $_SESSION["order"];
	}

	public function clear_order()
	{
		unset($_SESSION["order"]);
	}

	public function set_order($order)
	{
		foreach($order as $key =>  $o)
		{
			$set = 0;
			foreach($o as $val)
			{
				if($val)
				{
					$set = 1;
					break;
				}
			}
			if(!$set)
			{
				unset($order[$key]);
			}
		}

		$_SESSION["order"] = $order;
	}

	/**
		@attrib name=orderer_data nologin="1"
		@param id required type=int acl=view
		@param next_view optional
		@param cart required type=int acl=view
		@param section optional
	**/
	public function orderer_data($arr)
	{
		$cart_instance = get_instance(CL_SHOP_ORDER_CART);
		$cart_instance->cart = obj($arr["cart"]);
	
		if($this->can("view" , $arr["id"]))
		{
			$form= obj($arr["id"]);
			if($form->prop("orderer_data_template"))
			{
				$template = $form->prop("orderer_data_template");
			}
		}

		if(empty($template))
		{
			$template = "orderer_form_data.tpl";
		}
		$cart_instance->read_template($template);
		lc_site_load("shop_order_cart", &$cart_instance);

		$cart_instance->add_orderer_vars();
		$cart_instance->add_order_vars();
		$cart_instance->add_cart_vars();
		$cart_instance->vars($arr);
		$cart_instance->vars(array("id" => $arr["id"]));
		return $cart_instance->parse();
	}

	/**
		@attrib name=submit_order_data nologin="1" all_args=1
		@param next_action optional type=string
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
			$action = "confirm_view";
		}
		return $this->mk_my_orb($action, array(
			"oc" => $arr["oc"],
			"cart" => $arr["cart"],
			"section" => $arr["section"],
			"id" => $arr["id"],
		));
	}

	/**
		@attrib name=order_data nologin="1"
		@param id required type=int acl=view
		@param cart required type=int acl=view
		@param next_view optional
		@param section optional
	**/
	public function order_data($arr)
	{
		$cart_instance = get_instance(CL_SHOP_ORDER_CART);
		$cart_instance->cart = obj($arr["cart"]);
		$form = obj($arr["id"]);
		if($form->prop("order_data_template"))
		{
			$template = $form->prop("order_data_template");
		}
		else
		{
			$template = "order_form_data.tpl";
		}
		$cart_instance->read_template($template);
		lc_site_load("shop_order_cart", &$cart_instance);
		$cart_instance->cart->get_order_data();
		$cart_instance->add_orderer_vars();
		$cart_instance->add_order_vars();
		$cart_instance->add_cart_vars();
		$cart_instance->vars($arr);
		$cart_instance->vars(array("id" => $arr["id"]));
		return $cart_instance->parse();
	}
	function show($arr)
	{
	//tellimuse info
		$form_obj = obj($arr["id"]);
		$oc = obj($form_obj->prop("order_center"));
		$cart = obj($oc->prop("cart"));
		$c_data = $cart->get_order_data();
		$order_data = $this->get_order();
		$cart_instance = get_instance(CL_SHOP_ORDER_CART);
	//templeidi valik
		if(!empty($arr["template"]))
		{
			$this->read_template($arr["template"]);
		}
		elseif($form_obj->prop("orders_form_template"))
		{
			$this->read_template($form_obj->prop("orders_form_template"));
		}
		elseif(file_exists($this->site_template_dir."/orders_form.tpl"))
		{
			$this->read_site_template("orders_form.tpl");
		}
		else
		{
			$this->read_template("orders_form.tpl");
		}


		foreach($c_data as $key => $val)
		{
			$this->vars(array(
				$key."_value" => $val,
			));
		}

		$this->vars(array(
			"delivery_name" => empty($c_data["delivery"]) ? " " : get_name($c_data["delivery"]),
			"delivery_value" => empty($c_data["delivery"]) ? "" : $c_data["delivery"],
		));
		$this->vars(array(
			"payment_name" => empty($c_data["payment"]) ? " " : get_name($c_data["payment"]),
			"payment_value" => empty($c_data["payment"]) ? "" : $c_data["payment"],
		));
		//v6imalikud v2ljad mida templeidis kasutada
		$order_vars = array(
			"name" => t("Nimi"),
			"product_code" => t("Tootekood"),
			"product_color" => t("V&auml;rv"),
			"product_size" => t("Suurus"),
			"product_count" => t("Hulk"),
			"product_page" => t("Lehek&uuml;lg"),
			"product_image" => t("Pilt"),
			"product_price" => t("Hind"),
			"product_sum" => t("Summa")
		);
		//v2ljad mida toote leidmisel laost muuta ei saa
		$disable_vars = array("product_code" , "name" , "product_color", "product_page" , "product_image" , "product_sum");
		
		$shop_cart_table = "";
		$count = 0;
		foreach($order_data as $key => $data)
		{
			$vars = array();
			$product = null;
			$data["product_price"] = t("Hinnakirja<br>alusel");
			if(!$data["product_count"])
			{
				$order_data[$key]["product_count"] = $data["product_count"] = 1;
			}

			if($data["product_code"])//kui koodi j2rgi toode, siis annab ise igast andmeid ette
			{
				$product = $this->get_product_by_code($data["product_code"]);
				if($product)
				{
					$data["name"] = $product->get_packet_name();
					$order_data[$key]["product_color"] = $data["product_color"] = $product->get_color_name();
					if(empty($data["product_size"]))
					{
						$order_data[$key]["product_size"] = $data["product_size"] = reset($product->get_size_vals());
					}
					if($pr = $product->get_size_price($data["product_size"] , $form_obj->prop("order_center")))
					{
						$data["product_price"] = number_format($pr , 2);
					}

					$vars["image_popup"] = $product->get_image_popup();
					$vars["image_url"] = $product->get_product_big_image_url();
				}
			}

			foreach($order_vars as $order_var => $caption)
			{
				$vars[$order_var."_value"] = empty($data[$order_var]) ? "" : $data[$order_var];
				$vars[$order_var."_caption"] = $caption;
				$vars[$order_var] = html::textbox(array(
					"name" => "order_row[".$count."][".$order_var."]",
					"size" => 11,
					"value" => $vars[$order_var."_value"],
				));

			if($product && in_array($order_var,$disable_vars))
			{
				$vars[$order_var] = 
//html::textbox(array(
	//					"name" => "order_row[".$count."][".$order_var."]",
//						"size" => 11,
//						"value" => $vars[$order_var."_value"],
//						"disabled" => 1,
//					)).
					$vars[$order_var."_value"].
					html::hidden(array(
						"name" => "order_row[".$count."][".$order_var."]",
						"value" => $vars[$order_var."_value"],
					));
				}
			}
			if($product)
			{
				$vars["product_size"] = html::select(array(
					"name" => "order_row[".$count."][product_size]",
					"value" => $vars["product_size_value"],
					"options" => $product->get_size_vals()
				));
				if(!(sizeof($product->get_size_vals()) > 1))
				{
					$vars["product_size"] = "";
				}
				if(!empty($data["product_price"]) && $data["product_count"])
				{
					$vars["product_sum"] = number_format($data["product_price"] * $data["product_count"] , 2);
				}
			}
			else
			{
			//	$vars["product_size"] = "";
				$vars["image_popup"] = "";
				$vars["image_url"] = "";
				$vars["product_sum"] = "";
			}

			$vars["delete"] = html::href(array("url" => "javascript:void(0);" , "onClick" => '$.get("/automatweb/orb.aw?class=orders_form&action=delete_row&row='.$count.'", {
						}, function (html) {
							x=document.getElementById("order_row_'.$count.'");
							//alert(jQuery(x).css("border", "1px solid red"));
							jQuery(x).remove();
						}
					);',
					"caption" => t("Eemalda<br>toode"),
			));
			$vars["tr_id"] = "order_row_".$count;
			$this->vars($vars);
			$_vars = $vars;
			foreach($_vars as $var => $value)
			{
				if($value && $this->is_template("HAS_".strtoupper($var)))
				{
					$this->vars(array("HAS_".strtoupper($var) => $this->parse("HAS_".strtoupper($var))));
				}
				elseif($this->is_template("HAS_".strtoupper($var)))
				{
					$this->vars(array("HAS_".strtoupper($var) => " "));
				}
			}

			$shop_cart_table.= $this->parse("shop_cart_table");
			$count++;
		}
		
		if(empty($arr["confirm"]))
		{
			$x = 0;
			while($x < 1)
			{
				$vars = array();
				foreach($order_vars as $order_var => $caption)
				{
					$vars[$order_var."_caption"] = $caption;
					$vars[$order_var] = html::textbox(array(
						"name" => "order_row[".($count+$x)."][".$order_var."]",
						"size" => 11,
					));
				}
				$this->vars( $vars);
				$shop_cart_table.= html::div(array("id" => "order_row_".($count+$x) , "content" => $this->parse("shop_cart_new_table")));
				$x++;
			}
		}
			$confirm_url =$this->mk_my_orb("confirm_view", array("id" => $arr["id"], "section" => aw_global_get("section")));
		
//add delivery vars
		if($this->can("view" , $c_data["delivery"]))
		{
			$delivery_vars = array();
			$delivery = obj($c_data["delivery"]);
			$delivery_vars["delivery_name"] = $delivery->name();
			$delivery_vars["delivery_price"] = $delivery->get_curr_price($oc->prop("default_currency"));
			$this->vars($delivery_vars);
		}
		$this->vars(array(
			"shop_cart_table" => $shop_cart_table,
			"rows_count" => $count,
			"id" => $form_obj->id(),
			"reforb" => $this->mk_reforb("submit_order", array(
				"section" => aw_global_get("section"))),
			"forwardurl" => $this->mk_my_orb("order_data" , 
				array("confirm_url" => $confirm_url,
					"cart" => $form_obj->prop("order_center.cart"),
					"section" => aw_global_get("section"),
					"id" => $arr["id"],
				) , CL_ORDERS_FORM),
			"confirm_url" => $this->mk_my_orb("confirm", array("id" => $arr["id"], "section" => aw_global_get("section"))),
		));
		$this->vars(array("shop_table" => $this->parse("shop_table")));

		$this->set_order($order_data);//et igasugu default v22rtused ka 2ra salvestaks
		return $this->parse();
	}

	public function get_product_by_code($code)
	{
		$ol = new object_list(array(
			"class_id" => CL_SHOP_PRODUCT,
			"lang_id" => array(),
			"site_id" => array(),
			"code" => $code,
		));
		return $ol->begin();
	}

	/**
		@attrib name=delete_row nologin=1 all_args=1
	**/
	public function delete_row($arr)
	{
		$order = $this->get_order();
		unset($order[$arr["row"]]);
		$this->set_order($order);
		die(1);
	}

	/**
		@attrib name=confirm_view nologin=1 all_args=1
	**/
	public function confirm_view($arr)
	{
		if(is_oid($arr["id"]))
		{
			$obj = obj($arr["id"]);
			if($obj->prop("confirm_template"))
			{
				return $this->show(array("id" => $arr["id"], "template" => $obj->prop("confirm_template")));
			}
		}
		return $this->show(array("id" => $arr["id"], "template" => "confirm_template.tpl","confirm" => 1));
		die(t("tellimuse vormi kinnituse templeit valimata"));
	}

	/**
		@attrib name=confirm nologin=1 all_args=1
	**/
	public function confirm($arr)
	{
		$form = obj($arr["id"]);
		$this->oc = obj($form->prop("order_center"));
		$cart = obj($this->oc->prop("cart"));
		$cart->set_oc();
		if (!is_oid($this->oc->prop("warehouse")))
		{
			error::raise(array(
				"id" => "ERR_NO_WAREHOOS",
				"msg" => sprintf(t("shop_order_cart::do_creat_order_from_cart(): no warehouse set for ordering center %s!"), $this->oc->id())
			));
		}

		$warehouse = $this->oc->prop("warehouse");

		$order_data = $cart->get_order_data();

		$o = new object();
		$o->set_name(t("M&uuml;&uuml;gitellimus")." ".date("d.m.Y H:i"));
		$o->set_parent($this->oc->id());
		$o->set_class_id(CL_SHOP_SELL_ORDER);
		$o->set_prop("warehouse" , $warehouse);
		$o->set_prop("date" , time());
		

		$person = $cart->_get_person($order_data);

		$o->set_prop("purchaser" , $person->id());
		$o->set_prop("buyer_rep" , $person->id());
		$address = $cart->_get_address($order_data);
		$o->set_prop("delivery_address" , $address->id());
		$o->set_prop("transp_type" , $order_data["delivery"]);
		$o->set_prop("payment_type" , $order_data["payment"]);
		$o->set_prop("currency" , $this->oc->get_currency());
		$o->set_prop("channel" , $form->prop("channel"));
		$o->save();

		$rows = $this->get_order();

		foreach($rows as $row)
		{
			$product = null;
			if(!empty($row["product_code"]))
			{
				$prod = $this->get_product_by_code($row["product_code"]);
				if(is_object($prod))
				{
					$packaging = $prod->get_package_by_size($row["product_size"]);

					if(is_object($packaging))
					{
						$product = $packaging->id();
						$row["product_price"] = $packaging->get_shop_price($cart->id());
					}
				}
			}
			$id = $o->add_row(array(
				"product_name" => $row["name"],
				"product" => $product,
				"amount" => $row["product_count"],
				"price" => $row["product_price"],
				"code" => $row["product_code"],
			));
			$r = obj($id);
			foreach($row as $key => $val)
			{
				$r->set_meta($key , $val);
			}
			$r->save();
		}
		$this->clear_order();

		$form->send_confirm_mail($o->id());

		header("Location: /".$o->id());
		die();
		return $o->id();

	}

	/**
		@attrib name=submit_order nologin=1 all_args=1
	**/
	public function submit_order($arr)
	{
		$this->set_order($arr["order_row"]);
		$url = aw_global_get("baseurl")."/".$arr["section"];
		//$url = $this->mk_my_orb("show" , array("id" => $order) , CL_SHOP_SELL_ORDER);
		header("Location: ".$url);
		die();

	}

	function parse_alias($arr)
	{
		$object = obj($arr["alias"]["target"]);
		if($this->can("view" , $object->prop("order_center")))//ostukorviga versiooni jaoks... siis teeb hiljem lao myygitellimuse
		{
			return $this->show(array(
				"id" => $arr["alias"]["target"],
			));
		}
		$_SESSION["orders_section"] = $arr["alias"]["from.parent"];
		$_SESSION["order_form_id"] = $arr["alias"]["to"];
		$arr["id"] = $arr["alias"]["target"];
		$arr["group"] = "ordering";
		$arr["cb_part"] = 1;
		
		return $this->change($arr);
	}

	/**
		@attrib name=change nologin=1 all_args=1
	**/
	function change($arr)
	{
		//If admin side then dont use templates
		if(strstr($_SERVER['REQUEST_URI'], "/automatweb") && !strstr($_SERVER['REQUEST_URI'], "action=print_orders") && !($_POST["action"] == "print_orders"))
		{
			return parent::change($arr);
		}
		
		if(($_GET["group"] == "confirmpage") || ($_GET["group"] == "persondata"))
		{
			if(!$_SESSION["order_cart_id"] || !$_SESSION["order_form_id"])
			{
				return aw_ini_get("baseurl");
			}
		}
		if(!is_oid($_SESSION["order_cart_id"]) || !$this->can("view", $_SESSION["order_cart_id"]) || !$_SESSION["order_form_id"])
		{
			$order = new object();
			$order->set_class_id(CL_ORDERS_ORDER);
			$order->set_parent($arr["oid"] ? $arr["oid"] : $arr["id"]);
			$order->set_meta("orders_form" , $arr["id"]);
			//arr($arr);
			$order->save();

			$_SESSION["order_cart_id"] = $order->id();
			$_SESSION["order_form_id"] = $arr["alias"]["to"];
			
			if (!is_oid($_SESSION["order_form_id"]) || !$this->can("view", $_SESSION["order_form_id"]))
			{
				$ol = new object_list(array("class_id" => CL_ORDERS_FORM));
				$tmp = $ol->begin();
				$_SESSION["order_form_id"] = $tmp->id();
			}

			$form_obj = &obj($_SESSION["order_form_id"]);
			$order->set_meta("itemform" , $form_obj->prop("itemform"));
			$order->save();
			$_SESSION["order_item_form_id"] = $form_obj->prop("itemform");
			if($conns = $form_obj->connections_from(array("type" => "RELTYPE_ADDORDER")))
			{
				foreach ($conns as $conn)
				{
					$order->connect(array(
						"to" => $conn->prop("to"),
						"reltype" => 1
					));
				}
			}
		}
		else
		{
			$form_obj = obj($_SESSION["order_form_id"]);
			$order = obj($_SESSION["order_cart_id"]);
		}
		if($form_obj->prop("orders_form_template"))
		{
			$this->read_template($form_obj->prop("orders_form_template"));
		}
		elseif(file_exists($this->site_template_dir."/orders_form.tpl"))
		{
			$this->read_site_template("orders_form.tpl");
		}
		else
		{
			$this->read_template("orders_form.tpl");
		}
		$this->submerge = 1;
		
		if($_GET["group"] == "persondata")
		{
			$this->vars(array(
				"add_persondata" => $this->get_persondata_form($arr),
				"shop_table" => $this->get_cart_table(),
			));
		}
		elseif ($_GET["group"] == "confirmpage" || $arr["show_order"] == 1)
		{
			$vars = array("order" => $order);
			if($form_obj->prop("no_pdata_check") == 1)
			{
				$vars["no_pdata_check"] = 1;
			}
			$vars = $vars + $arr;
			$this->vars(array(
				"show_confirm" => $this->get_confirm_persondata($vars),
				"shop_table" => ($_SESSION["orders_form"]["payment"]["type"] == "rent" ? $this->get_rent_table() : $this->get_cart_table()),
			));
		}
		else
		{
			$this->vars(array(
				"forwardurl" => aw_url_change_var(array("group" => ($form_obj->prop("no_pdata_check") == 1 ? "confirmpage" : "persondata"))),
			));
			$forward = $this->parse("forward_link");
			$this->vars(array(
				"add_items" => $this->get_additems_form($arr),
			));
			
			$conns = $order->connections_from(array(
				"type" => "RELTYPE_ORDER"
			));
		
			if($conns)
			{
				$this->vars(array(
					"forward_link" => $forward,
					"shop_table" => $this->get_cart_table(),
				));
			}
		}
		$this->vars(array(
			"logged" => (aw_global_get("uid") == "" ? "" : $this->parse("logged")),
		));
		return $this->parse();
	}
	

	function get_confirm_persondata($arr)
	{
		extract($arr);
		$this->read_template("orders_confirm_persondata.tpl");

		if($no_pdata_check == 1)
		{
			if(aw_global_get("uid") != "")
			{
				$user = obj(aw_global_get("uid_oid"));
				$person = $user->get_first_obj_by_reltype("RELTYPE_PERSON");
			}
		}
		else
		{
			$person = current($order->connections_from(array(
				"type" => "RELTYPE_PERSON",
			)));
			if ($person)
			{
				$person = $person->to();
			}
		}
		
		if(!$person)
		{
			if(file_exists($this->site_template_dir."/orders_form.tpl"))
			{
				$this->read_site_template("orders_form.tpl");
			}
			else
			{
				$this->read_template("orders_form.tpl");
			}
			return;
		}
		
		if($person->prop("email"))
		{
			$mail_o = &obj($person->prop("email"));
			$email = $mail_o->prop("mail");
		}
		
		if($person->prop("phone"))
		{
			$phone_obj = &obj($person->prop("phone"));
			$phonenr = $phone_obj->name();
		}

		$birthday_parts = explode('-', $person->prop("birthday"));
		$birthday_timestamp = mktime(0, 0, 0, $birthday_parts[1], $birthday_parts[2], $birthday_parts[0]);

		$this->vars(array(
			"person_name" => $person->name(),
			"firstname" => $person->prop("firstname"),
			"lastname" => $person->prop("lastname"),
			"personal_id" => $person->prop("personal_id"),
			"person_email" => $email,//$mail->prop("mail"),
			"person_phone" => $phonenr, //$phone->name(),
			"sendurl" => $this->mk_my_orb("send_order", array(), CL_ORDERS_ORDER), 
			"client_nr" => $order->prop("udef_textbox1"),
			"udef_textbox2" => $order->prop("udef_textbox2"),
			"udef_textbox3" => $order->prop("udef_textbox3"),
			"udef_textbox4" => $order->prop("udef_textbox4"),
			"udef_textbox5" => $order->prop("udef_textbox5"),
			"udef_textbox6" => $order->prop("udef_textbox6"),
			"udef_textbox7" => $order->prop("udef_textbox7"),
			"person_contact" => $person->prop("comment"),
			"birthday" => get_lc_date($birthday_timestamp, 1),
			"payment_type" => ($_SESSION["orders_form"]["payment"]["type"] == "cod" ? "Lunamaks" : "J&auml;relmaks"),
		));
		$add_props = array();
		if(aw_global_get("uid") != "")
		{
			$user = obj(aw_global_get("uid_oid"));
			if($user->is_connected_to(array(
				"type" => "RELTYPE_PERSON",
				"to" => $person->id(),
			)))
			{
				foreach($user->properties() as $name => $val)
				{
					$add_props["user_data_$name"] = $val;
				}
			}
		}
//		if($company = reset($person->connections_from(array("type" => "RELTYPE_WORK"))))
//		{
//			$com = $company->to();
//
		if($com = $person->company())
		{
			foreach($com->properties() as $name => $val)
			{
				if($name == "email_id" && is_oid($val) && $this->can("view", $val))
				{
					$ob = obj($val);
					$add_props["org_data_email_value"] = $ob->prop("mail");
				}
				elseif($name == "phone_id" && is_oid($val) && $this->can("view", $val))
				{
					$ob = obj($val);
					$add_props["org_data_phone_value"] = $ob->name();
				}
				elseif($name == "contact" && is_oid($val) && $this->can("view", $val))
				{
					$ob = obj($val);
					$add_props["org_data_address_value"] = $ob->name();
				}
				$add_props["org_data_$name"] = $val;
			}
		}
		$this->vars($add_props);

		if ($_SESSION["orders_form"]["payment"]["type"] == "rent")
		{
			$o = obj($_GET["id"]);
			$this->get_rent_table();
			if ($this->_totalsum > $o->prop("rent_max_amt_warn"))
			{
				$this->vars(array(
					"too_large_err" => $o->prop("rent_max_amt_warn_text")
				));
				$this->vars(array(
					"TOO_LARGE" => $this->parse("TOO_LARGE")
				));
			}
		}
		if($arr["show_order"] != 1)
		{
			$this->vars(array(
				"CONF_BLOCK" => $this->parse("CONF_BLOCK"),
				"SUBMIT_BLOCK" => $this->parse("SUBMIT_BLOCK"),
			));
		}

		$retval = $this->parse();
	//	$this->read_template("orders_form.tpl");
//if(aw_global_get("uid") == "struktuur")arr($arr);

		if(is_oid($arr["id"])&& $this->can("view" , $arr["id"]))
		{
			$o = obj($arr["id"]);
                	if($o->prop("orders_form_template"))
                	{
                        	$this->read_template($o->prop("orders_form_template"));
                	}
			elseif(file_exists($this->site_template_dir."/orders_form.tpl"))
			{
				$this->read_site_template("orders_form.tpl");
			}
                	else
                	{
                	        $this->read_template("orders_form.tpl");
       		         }
		}
		elseif(is_oid($_SESSION["order_form_id"])&& $this->can("view" , $_SESSION["order_form_id"]))
		{
			$o = obj($_SESSION["order_form_id"]);
                	if($o->prop("orders_form_template"))
                	{
                        	$this->read_template($o->prop("orders_form_template"));
                	}
			elseif(file_exists($this->site_template_dir."/orders_form.tpl"))
			{
				$this->read_site_template("orders_form.tpl");
			}
                	else
                	{
                	        $this->read_template("orders_form.tpl");
       		         }
		}
		else
		{
			if(file_exists($this->site_template_dir."/orders_form.tpl"))
			{
				$this->read_site_template("orders_form.tpl");
			}
			else
			{
				$this->read_template("orders_form.tpl");
			}
		}

		return $retval;
	}
	
	function get_persondata_form($arr)	
	{
		$this->read_template("orders_persondata.tpl");
		
		$this->vars(array(
			"id" => $_SESSION["order_cart_id"],
			"udef_checkbox1_error" => $_SESSION["udef_checkbox1_error"]
		));
		unset($_SESSION["udef_checkbox1_error"]);
		if($errors = aw_global_get("cb_values"))
		{
			foreach ($errors as $key => $value)
			{
				$tmp = array(
					$key."_error" => $value["error"]
				);
				$this->vars($tmp);
				unset($tmp);
			}
		}
		if($_SESSION["person_form_values"])
		{
			
			$this->vars(array(
				"selected_day".$_SESSION['person_form_values']['person_birthday']['day'] => "SELECTED",
				"selected_month".$_SESSION['person_form_values']['person_birthday']['day'] => "SELECTED"
			)); 
			foreach ($_SESSION["person_form_values"] as $key => $value)
			{
				$tmp = array(
					$key."_value" => $value
				);
				$this->vars($tmp);
				unset($tmp);
			}	
		}
		
		$yoptions[-1] = "--";
		for($i=1930; $i<date("Y"); $i++)
		{
			$yoptions[$i] = $i;
		}
		
		$year_select = html::select(array(
			"name" => 'person_birthday[year]',
			"options" => $yoptions,
			"value" => $_SESSION['person_form_values']['person_birthday']['year'],
		));
		
		if($_SESSION['person_form_values']['udef_textbox6'] == "esmakordselt")
		{	
			$udef_check2 = true;
		}
		else
		{
			$udef_check1 = true;
		}
		//temporary ? heh, n2ha on
		//XXX: temporary hack
		if($_SESSION["LC"]=="fi")
		{
			$pysiklient = "Kanta-asiakas";	
			$esmakordselt = "Ensimm&auml;inen OTTO-tilaukseni";
		}
		else
		{
			$pysiklient = "p&uuml;siklient";
			$esmakordselt = "esmakordselt";
		}
		
		$cv = aw_global_get("cb_values");
		$this->vars(array(
			"year_select" => $year_select,
			"customer_type1" => html::radiobutton(array(
				"name" => "udef_textbox6",
				"value" => $pysiklient,
				"checked" => $udef_check1,
				"onclick" => "check_rent()"
			)),
			"udef_checkbox1" => html::checkbox(array(
				"name" => "udef_checkbox1",
				"value" => 1,
				"checked" => $_SESSION['person_form_values']['udef_checkbox1'],
			)),
			"customer_type2" => html::radiobutton(array(
				"name" => "udef_textbox6",
				"value" => $esmakordselt,
				"checked" => $udef_check2,
				"onclick" => "check_rent()"
			)),
			"udef_checkbox1_error" => $cv["udef_checkbox1"]["error"]
		));

		$o = obj($arr["id"]);
		if ($o->prop("has_rent"))
		{
			$cr = false;
			if ($_SESSION["orders_form"]["payment"]["type"] == "rent" || $_SESSION['person_form_values']['udef_textbox6'] != "esmakordselt")
			{
				$cr = true;
			}

			if ($this->get_cart_sum() < $o->prop("rent_min_amt"))
			{
				$cr = false;
			}

			$this->vars(array(
				"cod_selected" => checked($_SESSION["orders_form"]["payment"]["type"] != "rent"),
				"rent_selected" => checked($_SESSION["orders_form"]["payment"]["type"] == "rent")
			));

			if ($cr)
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
		
		unset($_SESSION["cb_values"]);
		$retval = $this->parse();
		if($o->prop("orders_form_template"))
		{
			$this->read_template($o->prop("orders_form_template"));
		}
		elseif(file_exists($this->site_template_dir."/orders_form.tpl"))
		{
			$this->read_site_template("orders_form.tpl");
		}
		else
		{
			$this->read_template("orders_form.tpl");
		}
		return $retval;
	}
	
	function get_additems_form($arr)
	{
		$rval = "";
		$obj_inst = obj($arr["id"]);
		if($obj_inst->prop("order_item_template"))
		{
			$this->read_template($obj_inst->prop("order_item_template"));
		}
		else
		{
			$this->read_template("orders_order_item.tpl");
		}
		$num_rows = ((int)$obj_inst->prop("num_rows")) >= 1 ? (int)$obj_inst->prop("num_rows") : 1;
		$add_change_caption = "Lisa tellimusse";
		
		if($_GET["editid"])
		{
			$num_rows = 1;
			$obj = &obj($_GET["editid"]);
			$_SESSION["order_eoid"] = $_GET["editid"];
			
			$values[0]["name"] = $obj->prop("name");
			$values[0]["comment"] = $obj->prop("comment");
			$values[0]["product_size"] = $obj->prop("product_size");
			$values[0]["product_color"] = $obj->prop("product_color");
			$values[0]["product_code"] = $obj->prop("product_code");
			$values[0]["product_count"] = $obj->prop("product_count");
			$values[0]["product_price"] = $obj->prop("product_price");
			$values[0]["product_page"] = $obj->prop("product_page");
			$values[0]["product_image"] = $obj->prop("product_image");
			$values[0]["product_duedate"] = $obj->prop("product_duedate");
			$values[0]["product_unit"] = $obj->prop("product_unit");
			$values[0]["product_bill"] = $obj->prop("product_bill");

			$values[0]["udef_textbox1"] = $obj->prop("udef_textbox1");
			$values[0]["udef_textbox2"] = $obj->prop("udef_textbox2");
			$values[0]["udef_textbox3"] = $obj->prop("udef_textbox3");
			$values[0]["udef_textbox4"] = $obj->prop("udef_textbox4");
			$values[0]["udef_textbox5"] = $obj->prop("udef_textbox5");
			$values[0]["udef_textbox6"] = $obj->prop("udef_textbox6");
			$values[0]["udef_textbox7"] = $obj->prop("udef_textbox7");

			$add_change_caption = "Salvesta muudatused";
		}
		else 
		{
			$values = safe_array($_SESSION["order_form_values"]);
		}
		$_tmp = $values;
		
		$errors = $_SESSION["order_form_errors"]["items"];
		$_ertmp = $errors;
		$this->vars(array(
			"add_change_caption" => $add_change_caption,
			"id" => $_SESSION["order_cart_id"],
		));
		reset($values);
		for($i = 0; $i < $num_rows; $i++)
		{
			if(list($key, $values) = @each($values))
			{
				$errors = $_ertmp[$key];
			}
			else
			{
				$values = array();
				$errors = array();
			}
			$this->vars(array(
				"num" => $i,
				"product_code_error" => $errors["product_code"]["msg"],
				"product_code_value" => $values["product_code"],

				"product_unit_error" => $errors["product_unit"]["msg"],
				"product_unit_value" => $values["product_unit"],
				
				"product_name_error" => $errors["name"]["msg"],
				"product_name_value" => $values["name"],
				
				"product_size_error" => $errors["product_size"]["msg"],
				"product_size_value" => $values["product_size"],
				
				"product_color_error" => $errors["product_color"]["msg"],
				"product_color_value" => $values["product_color"],
				
				"product_count_error" => $errors["product_count"]["msg"],
				"product_count_value" => $values["product_count"],
				
				"product_price_error" => $errors["product_price"]["msg"],
				"product_price_value" => $values["product_price"],
				
				"product_page_error" => $errors["product_page"]["msg"],
				"product_page_value" => $values["product_page"],
				
				"product_image_error" => $errors["product_image"]["msg"],
				"product_image_value" => $values["product_image"],


				"udef_textbox1_error" => $errors["udef_textbox1"]["msg"],
				"udef_textbox1_value" => $values["udef_textbox1"],
				"udef_textbox2_error" => $errors["udef_textbox2"]["msg"],
				"udef_textbox2_value" => $values["udef_textbox2"],
				"udef_textbox3_error" => $errors["udef_textbox3"]["msg"],
				"udef_textbox3_value" => $values["udef_textbox3"],
				"udef_textbox4_error" => $errors["udef_textbox4"]["msg"],
				"udef_textbox4_value" => $values["udef_textbox4"],
				"udef_textbox5_error" => $errors["udef_textbox5"]["msg"],
				"udef_textbox5_value" => $values["udef_textbox5"],
				"udef_textbox6_error" => $errors["udef_textbox6"]["msg"],
				"udef_textbox6_value" => $values["udef_textbox6"],
				"udef_textbox7_error" => $errors["udef_textbox7"]["msg"],
				"udef_textbox7_value" => $values["udef_textbox7"],

				"product_duedate_error" => $errors["product_duedate"]["msg"],
				"product_duedate_value" => $values["product_duedate"],

				"product_bill_error" => $errors["product_bill"]["msg"],
				"product_bill_value" => $values["product_bill"],
				"comment_error" => $errors["comment"]["msg"],
				"comment_value" => $values["comment"],
			));
			
			unset($_SESSION["order_form_errors"]["items"][$key]);
			unset($_SESSION["order_form_values"][$key]);
			$rval .= $this->parse("ELEMENT"); 
		}
		
		$this->vars(array(
			"ELEMENT" => $rval,
		));
		//$this->submerge = 1;
		$retval = $this->parse();
		if($obj_inst->prop("orders_form_template"))
		{
			$this->read_template($obj_inst->prop("orders_form_template"));
		}
		elseif(file_exists($this->site_template_dir."/orders_form.tpl"))
		{
			$this->read_site_template("orders_form.tpl");
		}
		else
		{
			$this->read_template("orders_form.tpl");
		}
		
		$this->submerge = 1;
		
		return $retval;
	}

	function get_cart_items()
	{
		if (!is_oid($_SESSION["order_cart_id"]))
		{
			return aw_ini_get("baseurl");
		}
		$order = &obj($_SESSION["order_cart_id"]);
		$form = &obj($_SESSION["order_form_id"]);
		$conns = $order->connections_from(array(
			"type" => "RELTYPE_ORDER"
		));

		return new object_list($conns);
	}	

	function get_cart_sum()
	{
		$totalsum = 0;

		$order = &obj($_SESSION["order_cart_id"]);
		$form = &obj($_SESSION["order_form_id"]);
		$conns = $order->connections_from(array(
			"type" => "RELTYPE_ORDER"
		));

		$ol = new object_list($conns);
		foreach ($ol->arr() as $item)
		{
			$totalsum = $totalsum + $item->prop("product_count") * str_replace(",", ".", $item->prop("product_price"));
		}

		return $totalsum;
	}

	function get_rent_table()
	{
		$o = obj($_GET["id"]);
		$inf = $o->meta("rent_data");

		// get items in cart
		$items = $this->get_cart_items();

		$cats = array();
		foreach($items->arr() as $item)
		{
			$cats[(int)$_SESSION["orders_form"]["payment"]["itypes"][$item->id()]][$item->id()] = $item;
		}

		$states = $this->get_states();

		// display cats
		$item_cat = "";
		$totalsum = 0;
		foreach($cats as $cat => $items)
		{
			$item_in_cat = "";
			$tot_price = 0;
			foreach($items as $item)
			{
				$this->_insert_item_inf($item, $states);

				$tot_price += $item->prop("product_count") * str_replace(",", ".", $item->prop("product_price"));
				$item_in_cat .= $this->parse("ITEM_IN_CAT");
			}

			$dat = $inf[(int)$_SESSION["orders_form"]["payment"]["itypes"][$item->id()]];

			$prepayment = (($tot_price / 100.0) * (float)$inf[$cat]["prepayment"]);
			$num_payments = max($_SESSION["orders_form"]["payment"]["lengths"][$item->id()], $dat["min_mons"]);

			$cp = $tot_price - $prepayment;

			$percent = $inf[$cat]["interest"];

			$payment = ($cp+($cp*$num_payments*(1+($percent/100))/100))/($num_payments+1);

			$rent_price = $payment * ($num_payments+1) + $prepayment;

			$totalsum += $rent_price;

			$this->vars(array(
				"catalog_price" => number_format($tot_price, 2),
				"prepayment_price" => number_format($prepayment,2),
				"prepayment" => (int)$inf[$cat]["prepayment"],
				"num_payments" => $num_payments+1,
				"rent_payment" => number_format($payment,2),
				"total_rent_price" => number_format($rent_price,2)
			));

			$this->vars(array(
				"cat_name" => $inf[$cat]["type"],
				"ITEM_IN_CAT" => $item_in_cat,
				"HAS_PREPAYMENT" => ($inf[$cat]["prepayment"] > 0 ? $this->parse("HAS_PREPAYMENT") : "")
			));

			$item_cat .= $this->parse("ITEM_CAT");
		}
			
		$form = &obj($_SESSION["order_form_id"]);
		$this->vars(array(
			"ITEM_CAT" => $item_cat,
			"totalsum" => number_format($totalsum + $form->prop("postal_fee"), 2),
			"postal_fee" => $form->prop("postal_fee"),
			"print_url" => aw_url_change_var("print", 1)
		));
		$this->_totalsum = $totalsum + $form->prop("postal_fee");

		$retval = $this->parse("shop_table_rent");
		return $retval;
	}

	function get_cart_table()
	{	
		$order = &obj($_SESSION["order_cart_id"]);
		$form = &obj($_SESSION["order_form_id"]);
		if(!$conns = $order->connections_from(array(
			"type" => "RELTYPE_ORDER"
		)))
		{
			return;
		}

		$states_f = @file(aw_ini_get("site_basedir")."/public/laoseis.txt");
		$states = array();
		foreach(safe_array($states_f) as $s_l)
		{
			list($s_c, $s_v) = explode(";", $s_l);
			$states[$s_c] = $s_v;
		}
		
		$states = $this->get_states();

		$ol = new object_list($conns);
		$this->submerge = 1;
		foreach ($ol->arr() as $item)
		{
			//kui kuskilt porno kohast tahetakse ainult saatmata asju n2ha
			if($_GET["unsent"] && !$item->prop("product_count_undone"))
			{
				continue;
			}
			$_state = $states[$item->prop("product_code")];
			$this->vars(array(
				"name" => $item->name(),
				"editurl" => aw_url_change_var(array(
					"editid" => $item->id(), "group" => "")),
				"delete_href" => html::href(array(
					"url" => $this->mk_my_orb("delete_from_order",array(
						"id" => $item->id(),
					), CL_ORDERS_FORM),
					"caption" => t("Kustuta"))),
				"delete_url" => $this->mk_my_orb("delete_from_order",array(
						"id" => $item->id(),
					), CL_ORDERS_FORM),	
				"product_unit" => $item->prop("product_unit"),
				"product_code" => $item->prop("product_code"),
				"product_color" => $item->prop("product_color"),
				"product_size" => $item->prop("product_size"),
				"product_count" => !$_GET["unsent"] ? $item->prop("product_count"):$item->prop("product_count_undone"),
				"product_price" => $item->prop("product_price"),
				"product_image" => $item->prop("product_image"),
				"product_page" => $item->prop("product_page"),
				"product_duedate" => $item->prop("product_duedate"),
				"product_bill" => $item->prop("product_bill"),
				"comment" => $item->prop("comment"),
				"product_sum" => $item->prop("product_count") * str_replace(",", ".", $item->prop("product_price")),
				"product_status" => "",/*$this->prod_statuses[$_state]*/
				"udef_textbox7" => $item->prop("udef_textbox7"),
				"udef_textbox6" => $item->prop("udef_textbox6"),
				"udef_textbox5" => $item->prop("udef_textbox5"),
				"udef_textbox4" => $item->prop("udef_textbox4"),
				"udef_textbox3" => $item->prop("udef_textbox3"),
				"udef_textbox2" => $item->prop("udef_textbox2"),
				"udef_textbox1" => $item->prop("udef_textbox1"),
			));
			if(!$_SESSION["show_order"])
			{
				$this->vars(array("CHANGE_BLOCK" => $this->parse("CHANGE_BLOCK")));
			}
			$retval.= $this->parse("shop_cart_table");
			$totalsum = $totalsum + $item->prop("product_count") * str_replace(",", ".", $item->prop("product_price"));
		}
		
		$totalsum = $totalsum + $form->prop("postal_fee");
		
		$this->vars(array(
			"shop_cart_table" => $retval,
			"totalsum" => $totalsum,
			"postal_fee" => $form->prop("postal_fee"),
		));
		$retval = $this->parse("shop_table");
		return $retval;
	}

	function get_property($arr)
	{
		$prop =& $arr["prop"];
		switch($prop["name"])
		{
			case "rent_item_types":
				$this->_do_rent_item_types($arr);
				break;
			case "order_item_template":
				$tm = get_instance("templatemgr");
				$prop["options"] = $tm->template_picker(array(
					"folder" => "applications/orders"
				));
				if(!sizeof($prop["options"]))
				{
					$prop["caption"] .= t("\n".$this->site_template_dir."");
				}
				break;
			case "orders_form_template":
				$tm = get_instance("templatemgr");
				$prop["options"] = $tm->template_picker(array(
					"folder" => "applications/orders"
				));
				if(!sizeof($prop["options"]))
				{
					$prop["caption"] .= t("\n".$this->site_template_dir."");
				}
				break;
		}
		return PROP_OK;
	}

	function _init_rent_item_types_t(&$t)
	{
		$t->define_field(array(
			"name" => "type",
			"caption" => t("Kauba t&uuml;&uuml;p"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "min_mons", 
			"caption" => t("Min. Kuud"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "max_mons",
			"caption" => t("Max. Kuud"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "prepayment",
			"caption" => t("Esmase sissemakse %"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "interest",
			"caption" => t("Intressi %"),
			"align" => "center"
		));
	}

	function _do_rent_item_types($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_rent_item_types_t($t);

		$rent_data = safe_array($arr["obj_inst"]->meta("rent_data"));
		$rent_data[""] = array();

		foreach($rent_data as $dat)
		{
			++$idx;
			$t->define_data(array(
				"type" => html::textbox(array(
					"name" => "dat[$idx][type]",
					"value" => $dat["type"]
				)),
				"min_mons" => html::textbox(array(
					"name" => "dat[$idx][min_mons]",
					"value" => $dat["min_mons"],
					"size" => 5,
				)),
				"max_mons" => html::textbox(array(
					"name" => "dat[$idx][max_mons]",
					"value" => $dat["max_mons"],
					"size" => 5,
				)),
				"prepayment" => html::textbox(array(
					"name" => "dat[$idx][prepayment]",
					"value" => $dat["prepayment"],
					"size" => 5,
				)),
				"interest" => html::textbox(array(
					"name" => "dat[$idx][interest]",
					"value" => $dat["interest"],
					"size" => 5,
				)),
			));
		}
		$t->set_sortable(false);
	}

	function set_property($arr)
	{
		$prop =& $arr["prop"];
		switch($prop["name"])
		{
			case "rent_item_types":
				$inf = array();
				foreach(safe_array($arr["request"]["dat"]) as $idx => $dat)
				{
					if ($dat["type"] != "" && $dat["min_mons"] && $dat["max_mons"])
					{
						$inf[] = $dat;
					}
				}
				$arr["obj_inst"]->set_meta("rent_data", $inf);
				break;
		}
		return PROP_OK;
	}

	function _insert_item_inf($item, $states = NULL)
	{
		$name = $item->name();
		if (false && isset($states[$item->prop("product_code")]))
		{
			$str = $this->prod_statuses[$states[$item->prop("product_code")]];
			$name = "<a href='javascript:void(0)' alt='$str' title='$str'>$name</a>";
		}
		$this->vars(array(
			"udef_textbox1" => $item->prop("udef_textbox1"),
			"udef_textbox2" => $item->prop("udef_textbox2"),
			"udef_textbox3" => $item->prop("udef_textbox3"),
			"udef_textbox4" => $item->prop("udef_textbox4"),
			"udef_textbox5" => $item->prop("udef_textbox5"),
			"udef_textbox6" => $item->prop("udef_textbox6"),
			"udef_textbox7" => $item->prop("udef_textbox7"),

			"product_code" => $item->prop("product_code"),
			"product_color" => $item->prop("product_color"),
			"product_size" => $item->prop("product_size"),
			"product_count" => $item->prop("product_count"),
			"product_price" => $item->prop("product_price"),
			"product_image" => $item->prop("product_image"),
			"product_page" => $item->prop("product_page"),
			"product_sum" => $item->prop("product_count") * str_replace(",", ".", $item->prop("product_price")),
			"name" => $name,
		));
	}

	function get_states()
	{
		$states_f = @file(aw_ini_get("site_basedir")."/public/laoseis.txt");
		$states = array();
		foreach(safe_array($states_f) as $s_l)
		{
			if (trim($s_l) != "")
			{
				list($s_c, $s_v) = explode(";", $s_l);
				$states[$s_c] = trim($s_v);
			}
		}

		return $states;
	}
}
?>
