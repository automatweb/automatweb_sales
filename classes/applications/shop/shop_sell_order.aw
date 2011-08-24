<?php
/*
@classinfo syslog_type=ST_SHOP_SELL_ORDER relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_shop_sell_orders master_index=brother_of master_table=objects index=aw_oid

@default table=aw_shop_sell_orders
@default group=general

	@property number type=textbox field=aw_number
	@caption Number

	@property purchaser type=relpicker reltype=RELTYPE_PURCHASER field=aw_purchaser
	@caption Tellija

	@property job type=relpicker reltype=RELTYPE_JOB field=aw_job
	@caption T&ouml;&ouml;

	@property related_orders type=relpicker multiple=1 reltype=RELTYPE_PURCHASE_ORDER store=connect
	@caption Seotud ostutellimused			

	@property date type=date_select field=aw_date
	@caption Kuup&auml;ev

	@property deal_date type=date_select field=aw_deal_date
	@caption Tegelemise kuup&auml;ev

	@property planned_date type=date_select field=aw_planned_send_date
	@caption Planeeritud saatmise kuup&auml;ev

	@property buyer_rep type=relpicker reltype=RELTYPE_BUYER_REP field=aw_buyer_rep
	@caption Tellija esindaja

	@property our_rep type=relpicker reltype=RELTYPE_OUR_REP field=aw_our_rep
	@caption Meie esindaja

	@property trans_cost type=textbox field=aw_trans_cost
	@caption Transpordikulu

	@property customs_cost type=textbox field=aw_customs_cost datatype=int
	@caption Tollikulu

	@property transp_type type=relpicker field=aw_transp_type reltype=RELTYPE_TRANSFER_METHOD
	@caption L&auml;hetusviis

	@property shop_delivery_type type=relpicker field=aw_delivery_type reltype=RELTYPE_DELIVERY_METHOD
	@caption Poe kohaletoimetamise viis

	@property currency type=relpicker reltype=RELTYPE_CURRENCY automatic=1 field=aw_currency
	@caption Valuuta

	@property warehouse type=relpicker reltype=RELTYPE_WAREHOUSE automatic=1 field=aw_warehouse
	@caption Ladu

	@property delivery_address type=relpicker reltype=RELTYPE_ADDRESS field=aw_address
	@caption Kohaletoimetamise aadress


	@property smartpost_sell_place_name type=hidden field=aw_address_text
	@caption Kohaletoimetamise aadress tekstina (postkontorid jne)
	@comment M&otilde;nel kohaletoimetamise viisil on omal aadresside valik kuhu saadetakse kaup (nt. smartpost)



	@property order_status type=chooser default=0 field=aw_status default=0
	@caption Staatus

	@property channel type=relpicker field=aw_channel reltype=RELTYPE_CHANNEL store=connect
	@caption M&uuml;&uuml;gikanal

	@property taxed type=chooser field=aw_taxed
	@caption Maks

	@property payment_type type=select field=aw_payment_type
	@caption Maksetingimus

	@property deferred_payment_count type=hidden field=aw_deferred_payment_count
	@caption J&auml;relmaksude arv

	@property art_toolbar type=toolbar no_caption=1 store=no

	@property articles type=table store=no no_caption=1

	@property order_rows_to type=hidden store=no no_caption=1
	@caption Order rows to

@groupinfo preview caption="Eelvaade" submit=no
@default group=preview

	@property preview type=text no_caption=1 store=no

######

@reltype PURCHASER value=1 clid=CL_CRM_COMPANY,CL_CRM_PERSON
@caption Hankija

@reltype BUYER_REP value=2 clid=CL_CRM_PERSON
@caption Hankija esindaja

@reltype PURCHASE_ORDER value=3 clid=CL_SHOP_PURCHASE_ORDER
@caption Ostutellimus

@reltype OUR_REP value=4 clid=CL_CRM_PERSON
@caption Meie esindaja

@reltype CURRENCY value=5 clid=CL_CURRENCY
@caption Valuuta

@reltype WAREHOUSE value=6 clid=CL_SHOP_WAREHOUSE
@caption Ladu

@reltype TRANSFER_METHOD value=7 clid=CL_CRM_TRANSFER_METHOD
@caption L&auml;hetusviis

@reltype PRODUCT value=8 clid=CL_SHOP_PRODUCT
@caption Artikkel

@reltype ROW value=9 clid=CL_SHOP_ORDER_ROW
@caption Rida

@reltype JOB value=10 clid=CL_MRP_JOB
@caption T&ouml;&ouml;

@reltype ADDRESS value=11 clid=CL_CRM_ADDRESS
@caption Aadress

@reltype RELTYPE_CHANNEL value=12 clid=CL_WAREHOUSE_SELL_CHANNEL
@caption M&uuml;&uuml;gikanal

@reltype DELIVERY_METHOD value=13 clid=CL_SHOP_DELIVERY_METHOD
@caption Poe kohaletoimetamise viis
*/

class shop_sell_order extends class_base
{
	function shop_sell_order()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_sell_order",
			"clid" => CL_SHOP_SELL_ORDER
		));

		get_instance(CL_SHOP_PURCHASE_ORDER);

		$this->states = array(
			ORDER_STATUS_INPROGRESS => t("Koostamisel"),
			ORDER_STATUS_CONFIRMED => t("Kinnitatud"),
			ORDER_STATUS_CANCELLED => t("Katkestatud"),
			ORDER_STATUS_SENT => t("Saadetud"),
			ORDER_STATUS_CLOSED => t("T&auml;idetud"),
			ORDER_STATUS_WORKING => t("T&ouml;&ouml;tlemisel"),
		);

	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "payment_type":
				if($prop["value"])
				{
					$prop["options"] = array($prop["value"] => get_name($prop["value"]));
				}

				break;
		}

		return $retval;
	}

	public function _get_preview($arr)
	{
		if(!is_oid($arr["obj_inst"]->id()))
		{
			return PROP_IGNORE;
		}

		$arr["prop"]["value"] = $this->show(array(
			"id" => $arr["obj_inst"]->id(),
			//	Next line assumes the shop_order_center is the parent of the shop_sell_order. -kaarel 7.04.2010
			"template" => $arr["obj_inst"]->prop("parent.mail_template"),
		));
		return PROP_OK;
	}

	function callback_mod_reforb($arr)
	{
		return get_instance(CL_SHOP_PURCHASE_ORDER)->callback_mod_reforb($arr);
	}
	
	function callback_post_save($arr)
	{
		//echo'<pre>';var_dump($arr['request']['order_rows_to']);echo'</pre>';
		
		//echo'<pre>';var_dump($arr['request']['sel']);echo'</pre>';
		if (isset($arr['request']['order_rows_to']) and isset($arr['request']['sel'])) {
			
			$this->order_rows_to_bill($arr, $arr['request']['sel'], $arr['request']['order_rows_to']);
			
		}
		
		
		
		
	}
	
	function order_rows_to_bill($arr, $rows, $bill) {
		
		echo "alvar";
		
		/* @var $bill_obj crm_bill_obj */
		$bill_obj = obj($bill);
		
		foreach ($rows as $order_row_id) {
			
			/* @var $bill_row_obj crm_bill_row_object */
			$bill_row_obj = $bill_obj->add_row();
			
			
			
			/*
			$bill_row_obj->connect(array(
				'to' => $order_row_id,
				'type' => 'RELTYPE_SHOP_ORDER_ROW'
			));
			*/
			
			
			/* @var $order_row_obj shop_order_row */
			$order_row_obj = obj($order_row_id);
			
			
			//var_dump($order_row_obj);
			
			$cons = $order_row_obj->connections_from(array("type" => "RELTYPE_PRODUCT"));
			foreach ($cons as $con) {
				
				/* @var $con connection */
				
				//$bla = new c
				
				//echo'<pre>';var_dump($con);echo'</pre>';
				
				//@todo con on objekt, ära kasuta seda arrayna!
				
				
				//var_dump(get_class_methods(get_class($con)));
				//var_dump($con->to());
				
				
				//$prod_obj = obj();
				
				
				
				
				//$bill_row_obj->set_prop('prod', $con['to']);
			}
			
			
			
			/*
			$ol = new object_list(array(
					"class_id" => CL_SHOP_PRODUCT,
					"lang_id" => array(),
					"site_id" => array(),
				));
				
				var_dump($ol);
			*/
			
			//$this->name()." ".t("rida")
			//@todo save this :)
			
			
			
			$bill_row_obj->set_prop('price', 10);
			$bill_row_obj->set_prop('name', $order_row_obj->name());
			
			/*
			 * lisa ylejäänud asjad samuti
			 */
			
			
			$bill_row_obj->save();
			
			/*
			$this->connect(array(
				"to" => $br->id(),
				"type" => "RELTYPE_ROW"
			));
			*/
			
			/* @var $order_row_obj shop_order_row_obj */
			//$order_row_obj = obj($order_row_id);
			
			
			
		}
		
		
	}

	function _get_taxed($arr)
	{
		return PROP_IGNORE;
		$arr["prop"]["options"] = array(0 => "K&auml;ibemaksuta", 1 => "K&auml;ibemaksuga");
	}

	function _get_order_status($arr)
	{
		$arr["prop"]["options"] = $this->states;
		if(!$arr["prop"]["value"])
		{
			$arr["prop"]["value"] = ORDER_STATUS_INPROGRESS;
		}
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_shop_sell_orders(aw_oid int primary key, aw_number varchar(255), aw_purchaser int, related_purcahse_orders int, aw_date int, aw_planned_send_date int, aw_buyer_rep int, aw_our_rep int, aw_trans_cost double, aw_transp_type varchar(255), aw_currency int, aw_warehouse int, aw_taxed int)");
			return true;
		}
		switch($f)
		{
			case "aw_address_text":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "VARCHAR(127)"
				));
				break;
			case "aw_customs_cost":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "double"
				));
				return true;
				break;
			case "aw_deferred_payment_count":
			case "aw_job":
			case "aw_deal_date":
			case "aw_status":
			case "aw_delivery":
			case "aw_channel":
			case "aw_address":
			case "aw_payment_type":
			case "aw_delivery_type":
			case "aw_channel":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
				break;
		}
	}

	function _get_warehouse($arr)
	{
		if ($arr["request"]["warehouse"])
		{
			$arr["prop"]["value"] = $arr["request"]["warehouse"];
			$arr["prop"]["options"][$arr["request"]["warehouse"]] = obj($arr["request"]["warehouse"])->name();
		}
	}

	function _get_art_toolbar($arr)
	{
		return get_instance(CL_SHOP_PURCHASE_ORDER)->_get_art_toolbar($arr);
	}

	function _get_articles($arr)
	{
		return get_instance(CL_SHOP_PURCHASE_ORDER)->_get_articles($arr);
	}

	function _set_articles($arr)
	{
		return get_instance(CL_SHOP_PURCHASE_ORDER)->_set_articles($arr);
	}

	function _set_related_orders($arr)
	{
		return get_instance(CL_SHOP_PURCHASE_ORDER)->_set_related_orders($arr);
	}

	function request_execute($o)
	{
		return $this->show(array(
			"id" => $o->id(),
		));
	}

	/**
		@attrib name=show
		@param id required
		@param template optional
	**/
	public function show($arr)
	{
		$is_bonus_discount = 0;

		if(empty($arr["template"]))
		{
			$arr["template"] = "show.tpl";
		}
		
	//	echo '<pre>';var_dump($arr);echo '</pre>';
		$this->read_any_template($arr["template"]);
		lc_site_load("shop", $this);
		$data = array();
		$o = obj($arr["id"]);
		$meta = $o->meta("order_data");
		$this->vars($meta);
		if(aw_global_get("uid") == "struktuur.markop") arr($meta);
		foreach($o->get_property_list() as $pn => $pd)
		{
			$data[$pn] = $o->prop_str($pn);
		}
//arr($data);
		$t = new aw_table();
		$t->define_field(array(
			"name" => "prod",
			"caption" => t("Toode"),
			"align" => "left"
		));
		$t->define_field(array(
			"name" => "amount",
			"caption" => t("Kogus"),
			"align" => "right"
		));
		$t->define_field(array(
			"name" => "price",
			"caption" => t("Hind"),
			"align" => "right"
		));
		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa"),
			"align" => "right"
		));

		$sum = $cart_sum = 0;
		$different_products = 0;
		$rows = "";
		$prod_data_keys = array();
		foreach($o->connections_from(array("type" => "RELTYPE_ROW")) as $c)
		{
			$row = $c->to();
			$prod_data = array();
			if($this->can("view" , $row->prop("prod")))
			{
				$product = obj($row->prop("prod"));
				$prod_data = $product->get_data();
				$prod_data_keys = array_keys($prod_data);
			}
			else
			{
				if(sizeof($prod_data_keys))//selleks, et kui yhel real on toode kyljes, ja teisel pole, et siis teisel ei t2idaks eelmiste andmetega muutujaid
				{
					foreach($prod_data_keys as $key)
					{
						$prod_data[$key] = "";
					}
					$prod_data_keys = array();
				}
			

				$prod_data["code"] = $row->meta("product_code");
				$prod_data["size"] = $row->meta("product_size");
				$prod_data["color"] = $row->meta("product_color");
				$prod_data["name"] = $row->prop("prod_name");
				$prod_data["packet_name"] = $row->prop("prod_name");
			}
				if(aw_ini_get("site_id") == 484 && (substr(trim($prod_data["code"]) , 0 , 1) == "V" || substr(trim($prod_data["code"]) , 0 , 1) == "v"))//keyword
				{
					$is_bonus_discount = 1;
				}
			if(!$row->prop("price"))
			{
				$price = $prod_data["price"];
			}
			else
			{
				$price = $row->prop("price");
			}
			$c_sum = $row->amount * $price;
			$sum+= $c_sum;

			$row_data = array(
				"prod" => $row->prod_name,
				"amount" => $row->amount,
				"price" => number_format($row->price , 2),
				"price_with_zeros" => number_format($row->price , 2),
				"sum" => number_format($c_sum, 2)
			);
			$t->define_data($row_data);
			foreach($row->get_property_list() as $pn => $pd)
			{
				$row_data[$pn] = $row->prop_str($pn);
			}
			foreach(safe_array($row->meta()) as $pn => $pd)
			{
				$row_data[$pn] = $pd;
			}
			$different_products++;

			//$row_data - muutujad mis tulevad tellimuse reast, $prod_data - need muutujad, mis tulevad toote juurest
			$this->vars($prod_data);
			$this->vars($row_data);

			//mingi kamm on nende hindade formaadiga... erinevatest kohtadest tulevad ka teised
			//ylej22nud v6iks tulevikus eest 2ra kustutada, kuid kindlasti keegi kasutab kuskil neid hetkel,,, kuid l6pus kirjutab yle nyyd
			$this->vars(array(
				"unformated_price" => $price,
				"price" => number_format($price, 2, "." , ""),
			));

			foreach($prod_data as $key => $val)//v6ibolla peaks samamoobi l2bi laskma ka $row_data muutuja
			{
				if($this->is_template("HAS_".strtoupper($key)))
				{
					if($val)
					{
						$this->vars(array("HAS_".strtoupper($key) => $this->parse("HAS_".strtoupper($key))));
					}
					else
					{
						$this->vars(array("HAS_".strtoupper($key) => ""));
					}
				}
				
			}
			if($this->is_template("ROW_".$prod_data["id"]))
			{
				$this->vars(array("ROW_".$prod_data["id"] => $this->parse("ROW_".$prod_data["id"])));
			}
			else
			{
				$cart_sum+= $c_sum;
				$rows.= $this->parse("ROW");
			}

/*if(aw_global_get("uid") == "struktuur.markop")
		{
			arr($this->vars); die();
		}*/
			foreach($prod_data as $key => $var)
			{
				$this->vars(array(
					$key => "",
				));
			}



		}
/*
if(aw_ini_get("site_id") != 484)//true)//aw_global_get("uid") == "struktuur.markop")
{
	$sum = 0.9*$sum;
	$cart_sum = 0.9*$cart_sum;
}*/
		$data["cart_sum"] = number_format($cart_sum , 2);
		if($this->can("view" , $o->prop("payment_type")))
		{
			$payment = obj($o->prop("payment_type"));
			$data["payment_name"] = $payment->name();
			$data["payment"] = $payment->id();
			$condition = $payment->valid_conditions(array(
				"sum" => $sum,
				"currency" => $o->prop("currency"),
				"product" => array(),
				"product_packaging" => array(),
			));

			if($this->can("view" , $condition))
			{
				$condition_object = obj($condition);
				foreach($condition_object->properties() as $key => $prop)
				{
					$this->vars(array("condition_".$key => $prop));
				}
				
				$stuff = $condition_object->calculate_rent($sum , $meta["deferred_payment_count"]);

				if($stuff["sum_rent"] > 0)
				{
					$sum = $stuff["sum_rent"];
					$data["deferred_payment_price"] = $stuff["single_payment"];
				}
			}
		}
		
		if($this->can("view" , $o->prop("shop_delivery_type")))
		{

//-----------------------------


//--------------------------------
			$delivery = obj($o->prop("shop_delivery_type"));
			$t->define_data(array(
				"prod" => $delivery->name(),
				"sum" => $is_bonus_discount ? 0 : $delivery->get_curr_price($o->prop("currency")),
			));
			$data["delivery_sum"] = $is_bonus_discount ? 0 : $delivery->get_curr_price($o->prop("currency"));
			$data["delivery_name"] = $delivery->name();
			$data["delivery_price"] = $is_bonus_discount ? 0 : $delivery->get_curr_price($o->prop("currency"));

			if(!$is_bonus_discount)$sum+= $data["delivery_price"];//keyword

			//kohaletoimetamise info muutujad
//			$this->vars($delivery->get_vars($o->meta("order_data") + $o->properties()));
		}


		$data["ROW"] = $rows;
		$data["id"] = $o->id();
		$data["orderer"] = $o->prop("purchaser.name");
		$data["status"] = $o->prop("order_status") ? $this->states[$o->prop("order_status")] : "";
		$data["table"] = $t->draw();
		$data["sum"] = number_format($sum, 2);
		$data["date"] = date("d.m.Y" , $o->prop("date"));
		$data["different_products"] = $different_products;
		if($this->can("view" , $o->prop("delivery_address")))
		{
			$address = obj($o->prop("delivery_address"));
			$data["address_index"] = $address->prop("postiindeks");
			$data["address_city"] = $address->prop_str("linn");
			$data["address_address"] = $address->prop_str("aadress");
		}
		if($this->can("view" , $o->prop("purchaser")))
		{
			$orderer = obj($o->prop("purchaser"));
			$data["email"] = $orderer->get_mail();
			$data["mobile_phone"] = $orderer->get_phone(null,null,"mobile");
			$data["home_phone"] = $orderer->get_phone(null,null,"home");
			if($orderer->class_id() == CL_CRM_PERSON && $orderer->prop("birthday"))
			{
				$data["birthday"] = date("d.m.Y" , $orderer->prop("birthday"));
				$data["customer_no"] = $orderer->prop("external_id");
				$data["firstname"] = $orderer->prop("firstname");
				$data["lastname"] = $orderer->prop("lastname");
			}
			$data["customer_no"] = $orderer->prop("external_id");
		}
		$data["channel"] = $o->prop("channel");
		$customer_data_ids = shop_sell_order_obj::get_customer_relation_ids_for_purchasers(array($o->prop("purchaser")), obj($o->prop("warehouse.conf.owner")));
		$data["customer_data_id"] = reset($customer_data_ids);

		$this->vars($data);

//igale muutujale sub selle kohta kas ta on olemas
		foreach($this->vars as $key => $val)
		{
			if($val && $this->is_template("HAS_".strtoupper($key)))
			{
				$this->vars(array("HAS_".strtoupper($key) => $this->parse("HAS_".strtoupper($key))));
			}
		}
//m6nele muutuja v22rtusele spets sub kujul MUUTUJANIMI_MUUTUJAV22R
		
		$sub_vars = array("payment");
		foreach($sub_vars as $var)
		{
			if(isset($data[$var]) and $this->is_template(strtoupper($var)."_".$data[$var]))
			{
				$this->vars(array(strtoupper($var)."_".$data[$var] => $this->parse(strtoupper($var)."_".$data[$var])));
			}
		}
		
	//	arr(aw_global_get("uid"));

		return $this->parse();
	}

}





?>
