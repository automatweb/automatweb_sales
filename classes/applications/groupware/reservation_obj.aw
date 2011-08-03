<?php
//metas vrtused
//final_saved_sum - valuutades lplik summa mis sai makstud ka tenoliselt... ja kui see olemas siis rohkem ei arvutata
//special_sum - mratud kindel summa kigis valuutades... le kirjutamiseks objekti juurest miskitel spetsjuhtudel

//maintainer=markop
class reservation_obj extends _int_object
{
	const CLID = 1165;

	function delete($full_delete = false)
	{
		$ol = $this->get_other_brons();
		$ol->delete();
		parent::delete($full_delete);
	}

	public function save($check_state = false)
	{
		$rv = parent::save($check_state);

		$this->set_sum();

		return $rv;
	}

	function set_prop($pn, $pv)
	{
		//teatud propide puhul uuendab alambroneeringute andmeid ka
		if($this->meta("has_other_brons"))
		{
			$inst = get_instance(CL_RESERVATION);
			if(in_array($pn , $inst->get_from_parent_props))
			{
				$other_brons = $this->get_other_brons();
				foreach($other_brons->arr() as $ob)
				{
					$ob->set_prop($pn, $pv);
					$ob->save();
				}
			}
		}

		switch($pn)
		{
			case "start1":
				if($pv && $this->prop("verified"))
				{
					$this->set_meta("redecleared" , 1);
				}
				break;
			case "customer":
				if(is_oid($pv))
				{
					$person = obj($pv);
					$parent = $this->get_room_setting("customer_menu");
					if($parent && $parent != $person->parent())
					{
						$person->set_parent($parent);
						$person->save();
					}
				}
				break;
			case "verified":
				if($pv && !$this->prop("verified"))
				{
					$this->verified = 1;
					if($this->get_room_setting("send_verify_mail"))
					{
						$this->send_affirmation_mail();
					}
				}
				break;
			case "type":
				if($this->prop("type") && !$pv)//ei saa tyypi 2ra kaotada
				{
					return;
				}
				break;
			case "seller":
				$pn = "inbetweener";
				break;

		}
		$retval = parent::set_prop($pn, $pv);

		switch($pn)
		{
			case "start1":
			case "end":
			case "resource":
			case "customer":
				$this->set_correct_name();
		}
		return $retval;
	}

	private function set_sum()
	{
		$room_instance = get_instance(CL_ROOM);
		$room_sum = $room_instance->cal_room_price(array(
			"room" => $this->prop("resource"),
			"start" => $this->prop("start1"),
			"end" => $this->prop("end"),
			"people" => $this->prop("people_count"),
			"products" => $this->meta("amount"),
			"bron" => $this,
		));
		if(is_array($this->get_special_sum()))
		{
			$sum = $spec_sum;
		}
		elseif(is_array($this->meta("special_sum")))
		{
			$sum = $this->meta("special_sum");
		}
		else
		{
			$sum = $room_sum;
		}
		//$this->set_saved_sum($room_sum,"room");//mingi aeg peaks eraldi ruumi ja toodete hinna ka salvestama kui vajadus tekkib
		$this->set_saved_sum($sum,"sum");
	}

	/** returns reservation price
		@attrib api=1
		@return array
			array(currency => sum, ...)
	 **/
	public function get_sum()
	{
		enter_function("sbo::_get_sum");
		if($this->is_lower_bron())//kui on miski alambronn , siis on suht hindamatu
		{
			exit_function("sbo::_get_sum");
			return array();
		}

		if(is_array($sum = $this->get_saved_sum()))
		{exit_function("sbo::_get_sum");
			return $sum;
		}

		//see meta versioon tuleb tagantpoolt 2ra koristada kohe kui kindel on, et kuskile pole seni seda salvestatud
		if($spec_sum = $this->get_special_sum())
		{exit_function("sbo::_get_sum");
			return $spec_sum;
		}


		$sum = $this->meta("final_saved_sum");
		//kui on salvestatud summa ja mneski valuutas omab vrtust, ning see on salvestatud ndal peale aja lbi saamist, siis lheb salvestatud variant loosi ja ei hakka uuesti le arvutama
		if(is_array($sum) && (!$this->prop("end") || ($this->prop("end") + 3600*24*7) < $this->meta("sum_saved_time")))
		{
			exit_function("sbo::_get_sum");
			return $sum;
		}
//		arr($sum); arr($this->prop("end"));arr($this->meta("sum_saved_time"));

		$special_sum = $this->meta("special_sum");
		if(is_array($special_sum) && array_sum($special_sum))
		{
			$sum = $special_sum;
		}
		else
		{
			$room_instance = get_instance(CL_ROOM);
			$sum = $room_instance->cal_room_price(array(
				"room" => $this->prop("resource"),
				"start" => $this->prop("start1"),
				"end" => $this->prop("end"),
				"people" => $this->prop("people_count"),
//				"products" => $this->meta("amount"),
				"bron" => $this,
			));
			$products = $this->get_product_amount();
			$params = array();
			$params["time"] = $this->created();
			$params["from"] = $this->prop("start1");
			$params["uid"] = $this->createdby();
			foreach($products as $product => $amount)
			{
				if(!$GLOBALS["object_loader"]->cache->can("view", $product))
				{
					continue;
				}
				$po = obj($product);
				foreach($po->get_price($params) as $curr => $prodsum)
				{
					if(!isset($sum[$curr]))
					{
						$sum[$curr] = $prodsum;
					}
					else
					{
						$sum[$curr]+= $prodsum;
					}
				}
			}
		}

		$this->set_meta("final_saved_sum" , $sum);
		$this->set_meta("sum_saved_time" , time());
		$this->save();
		exit_function("sbo::_get_sum");
		return $sum;
	}

	private function get_saved_sum($type = "sum")
	{
		$ret = array();
		$prices = new object_list(array(
			"class_id" => CL_PRICE,
			"object" => $this->id(),
			"lang_id" => array(),
			"site_id" => array(),
			"price_prop" => $type,
		));
		foreach($prices->arr() as $price)
		{
			if($price->prop("sum") && $price->prop("currency"))
			{
				$ret[$price->prop("currency")] = $price->prop("sum");
			}
		}
		if(!sizeof($ret))
		{
			return null;
		}
		return $ret;
	}

	private function set_saved_sum($price_array , $type = "sum")
	{
		$prices = new object_list(array(
			"class_id" => CL_PRICE,
			"object" => $this->id(),
			"lang_id" => array(),
			"site_id" => array(),
			"price_prop" => $type,
		));
		foreach($prices->arr() as $price)
		{
			if(isset($price_array[$price->prop("currency")]))
			{
				$price->set_prop("sum" , $price_array[$price->prop("currency")]);
				$price->save();
				unset($price_array[$price->prop("currency")]);
			}
		}
		foreach($price_array  as $curr => $sum)
		{
			if($sum)
			{
				$this->add_price_object($curr , $sum, $type);
			}
		}
		return 1;
	}

	/** returns reservation special price
		@attrib api=1
		@returns array/null
			array(currency id => sum , ...), or null if no special prices set
	**/
	public function get_special_sum()
	{
		$ret = array();
		$prices = new object_list(array(
			"class_id" => CL_PRICE,
			"object" => $this->id(),
			"lang_id" => array(),
			"site_id" => array(),
			"price_prop" => "special",
		));
		foreach($prices->arr() as $price)
		{
			if($price->prop("sum") && $price->prop("currency"))
			{
				$ret[$price->prop("currency")] = $price->prop("sum");
			}
		}
		if(!sizeof($ret) || !$this->id())
		{
			return null;
		}
		return $ret;
	}

        /** returns reservation rfp object
                @attrib api=1
        **/
	public function get_rfp()
	{
		 $rfps = new object_list(array(
                       "class_id" => CL_RFP,
                        "lang_id" => array(),
                        "site_id" => array(),
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_RFP.RELTYPE_RESERVATION" => $this->id(),
					"CL_RFP.RELTYPE_CATERING_RESERVATION" => $this->id(),
					new object_list_filter(array(
						"logic" => "AND",
						"conditions" => array(
							"CL_RFP.RELTYPE_RESERVATION" => $this->parent(),
						)
					)),
				),
			)),
                ));
		if(sizeof($rfps->ids()))
		{
			return reset($rfps->arr());
		}
		return null;
	}

	/** sets reservation special price
		@attrib api=1 params=pos
		@param price_array type=array
			array(currency object id => sum , ...)
		@returns 1 if successful
	**/
	public function set_special_sum($price_array)
	{
		$prices = new object_list(array(
			"class_id" => CL_PRICE,
			"object" => $this->id(),
			"lang_id" => array(),
			"site_id" => array(),
			"price_prop" => "special",
		));
		foreach($prices->arr() as $price)
		{
			if(isset($price_array[$price->prop("currency")]))
			{
				$price->set_prop("sum" , $price_array[$price->prop("currency")]);
				$price->save();
				unset($price_array[$price->prop("currency")]);
			}
		}
		foreach($price_array  as $curr => $sum)
		{
			if($sum)
			{
				$this->add_price_object($curr , $sum, "special");
			}
		}

		return 1;
	}

	private function add_price_object($curr , $sum, $prop = "")
	{
		$o = new object();
		$o->set_parent($this->id());
		$o->set_class_id(CL_PRICE);
		$o->set_name($this->name()." ".$prop." ".t("hind"));
		$o->set_prop("price_prop" , $prop);
		$o->set_prop("object" , $this->id());
		$o->set_prop("type" , $this->class_id());
		$o->set_prop("sum", $sum);
		$o->set_prop("currency", $curr);
		$o->save();
	}

	/** returns reservation price in currency
		@attrib api=1
		@param curr type=int/string
			currency id or
		@returns double
			reservation price
	**/
	function get_sum_in_curr($curr)
	{
		if(!is_oid($curr))
		{
			$ol = new object_list(array(
				"site_id" => array(),
				"lang_id" => array(),
				"class_id" => CL_CURRENCY,
				"name" => $curr,
			));
			$curr = reset($ol->ids());
		}
		if(!is_oid($curr))
		{
			return "";
		}
		$sum = $this->get_sum();
		return $sum[$curr];
	}

	/** Returns resouces data
		@attrib api=1
	 **/
	function get_resources_data()
	{
		$inst = $this->instance();
		return $inst->get_resources_data($this->id());
	}

	/** Sets resources data
		@attrib api=1 params=pos
		@param data required type=array
			Resources data in array (structure is same as the get_resources_data() returns)
	 **/
	function set_resources_data($data = array())
	{
		$inst = $this->instance();
		return $inst->set_resources_data(array(
			"reservation" => $this->id(),
			"resources_info" => $data,
		));
	}

	/** Returns resources special prices
		@attrib api=1
	 **/
	function get_resources_price()
	{
		$inst = $this->instance();
		return $inst->get_resources_price($this->id());
	}

	/** Returns resources special discount
		@attrib api=1
	 **/
	function get_resources_discount()
	{
		$inst = $this->instance();
		return $inst->get_resources_discount($this->id());
	}

	/** returns resources sum
		@attrib api=1 params=pos
		@param special_discounts_off bool optional default=false

		@returns
			returns reservations resources sum in different currencies
	 **/
	function get_resources_sum($special_discounts_off = false)
	{
		$info = $this->get_resources_data();
		$price = $this->get_resources_price();
		$discount = $this->get_resources_discount();
		foreach($this->get_currencies_in_use() as $oid => $obj)
		{
			// check if special price is set
			if(strlen($price[$oid]))
			{
				$sum[$oid] = $price[$oid];
			}
			else // no special price, calc resources prices
			{

				foreach($info as $resource => $r_data) // loop over resources
				{
					if(strlen($r_data["prices"][$oid])) // if price is set
					{
						$count_total = $r_data["prices"][$oid] * $r_data["count"]; // amount * price
						$sum[$oid] += (strlen($r_data["discount"]) && $r_data["discount"] != 0)?$count_total * ((100 - $r_data["discount"]) / 100):$count_total; // discount and sum up
					}
				}
			}

			if(strlen($discount) && $discount != 0 && !$special_discounts_off) // calc special discount for all
			{
				$sum[$oid] *= ((100 - $discount) / 100);
			}
		}
		return $sum;
	}

	/** Returns currencies in use
		@attrib api=1
		@returns
			array(
				cur_oid => cur_obj
			)
		@comment
			Actually what this does is just return all system currencies right now, and all the places even don't use this in reservation obj(but they should).
	 **/
	function get_currencies_in_use()
	{
		$ol = new object_list(array(
			"site_id" => array(),
			"lang_id" => array(),
			"class_id" => CL_CURRENCY,
		));
		return $ol->arr();
	}

	/** Returns room currencies
		@attrib api=1
		@returns
			array(
				cur_oid => cur_name
			)
	 **/
	function get_room_currencies()
	{
		$currency = $this->prop("resource")?$this->prop("resource.currency"):$this->get_currencies_in_use();
		$ol = new object_list();
		$ol->add($currency);
		return $ol->names();
	}

	/** adds new project to reservation
		@attrib api=1
		@returns oid
			project id
	**/
	function set_new_project($name)
	{
		if(!strlen($name))
		{
			return;
		}
		$parent = $this->get_room_setting("projects_menu");
		if(!$parent)
		{
			$parent = $this->id();
		}
		if(!$parent)
		{
			$parent = $this->parent();
		}
		$project = new object();
		$project->set_parent($parent);
		$project->set_class_id(CL_PROJECT);
		$project->set_name($name);
		$project->save();
		$this->set_prop("project" , $project->id());
		$this->save();
		return $project->id();
	}

	/** Returns this reservation room setting
		@attrib api=1 params=pos
		@param setting required type=string
			room setting
		@return
			room setting value , or 0
	**/
	function get_room_setting($setting)
	{
		if(!is_object($this->room))
		{
			if(!$this->prop("resource"))
			{
				return null;
			}
			$this->room = obj($this->prop("resource"));
		}
		return $this->room->get_setting($setting);
	}

	/** Sends confirmation mail for a reservation
		@attrib api=1 params=pos
		@param tpl optional type=string
			The name of the template to use for formatting the email content
		@return boolean
			1 if mail sent, 0 if not
	**/
	function send_affirmation_mail($tpl = null)
	{
		if($this->meta("mail_sent"))
		{
			return 0;
		}
		$res_inst = get_instance(CL_ROOM_RESERVATION);
		$_send_to = $this->prop("customer.email.mail");

		$email_subj = $this->get_room_setting("verify_mail_subj");
		$mail_from_addr = $this->get_room_setting("verify_mail_from");
		$mail_from_name = $this->get_room_setting("verify_mail_from_name");
		if(!$tpl)
		{
			$tpl = "preview.tpl";
		}
		$res_inst->read_site_template($tpl);
		lc_site_load("room_reservation", $res_inst);
		$res_inst->vars($this->get_bron_data());
		$html =  $res_inst->parse();

		$awm = get_instance("protocols/mail/aw_mail");
		$awm->create_message(array(
			"froma" => $mail_from_addr,
			"fromn" => $mail_from_name,
			"subject" => $email_subj,
			"to" => $_send_to,
			"body" => strip_tags(str_replace("<br>", "\n",$html)),
		));
		$awm->htmlbodyattach(array(
			"data" => $html
		));
		$awm->gen_mail();
		$this->set_meta("mail_sent" , 1);
		$this->save();
		return 1;
	}

	/** Returns object data for printing or sending mail ...
		@attrib api=1 params=pos
		@param tpl optional type=string
			The name of the template to use for formatting the email content
		@return boolean
			1 if mail sent, 0 if not
	**/
	function get_bron_data()
	{
		$ret = array();
		$room = obj($this->prop("resource"));
		$ret["room_name"] = $room->name();
		$ret["time_str"] = $this->get_time_str(array(
			"start" => $this->prop("start1"),
			"end" => $this->prop("end"),
		));
		$ret["hours"] = ($this->prop("end")-$this->prop("start1"))/3600;
		$ret["people_value"] = $this->prop("people_count");

		$room_inst = get_instance(CL_ROOM);
		$sum = $room_inst->cal_room_price(array(
			"room" => $this->prop("resource"),
			"people" => $ret["people_value"],
			"start" => $this->prop("start1"),
			"end" => $this->prop("end"),
			"products" => -1,
		//	"products" => $bron->meta("amount"),
		));
		$data["sum"] = $data["sum_wb"] = $data["bargain"] = $data["menu_sum"] = $data["menu_sum_left"] = array();

		$prod_discount = $room_inst->get_prod_discount(array(
			"room" => $this->prop("resource"),
			"start" => $this->prop("start1"),
			"end" => $this->prop("end"))
		);
		foreach($sum as $curr => $val)
		{
			$currency = obj($curr);
	//		$data["sum"][] =  $val." ".$currency->name();
			$data["bargain"][] = (0+$room_inst->bargain_value[$curr])." ".$currency->name();
			$data["sum_wb"][] = ((double) $val + (double)$room_inst->bargain_value[$curr]) ." ".$currency->name();
		}
		foreach ($this->meta("amount") as $prod => $amount)
		{
			if($amount)
			{
				$product = obj($prod);
				$prices = $product->meta("cur_prices");
				foreach ($sum as $curr=> $val)
				{
					if($prices[$curr] || $prices[$curr] === 0)
					{
						$data["menu_sum"][$curr] = $data["menu_sum"][$curr] + $prices[$curr]*$amount*(100-$prod_discount)*0.01;
					}
					else
					{
						$data["menu_sum"][$curr] = $data["menu_sum"][$curr]+$product->prop("price")*$amount*(100-$prod_discount)*0.01;
					}
				}
			}
		}
		foreach ($sum as $curr=> $val)
		{
			$currency = obj($curr);
			if(!$data["menu_sum"][$curr])
			{
				$data["menu_sum"][$curr] = 0;
			}
			$data["menu_sum"][$curr] = $data["menu_sum"][$curr]." ".$currency->name();
		}

		$sum = $this->get_sum();

		foreach($sum as $curr => $val)
		{
			$currency = obj($curr);
			$data["sum"][] =  $val." ".$currency->name();
			$min_prices = $room->meta("web_room_min_price");
			$min_sum = $min_prices[$curr] - $val;
			if($min_sum < 0)
			{
				$min_sum = 0;
			}
			$data["min_sum_left"][] = $min_sum." ".$currency->name();
		}
		$ret["sum"] = join("/" , $data["sum"]);
		$ret["bargain"] = join("/" , $data["bargain"]);
		$ret["sum_wb"] = join("/" , $data["sum_wb"]);
		$ret["menu_sum"] = join("/" , $data["menu_sum"]);
		$ret["comment_value"] = $this->prop("content");
		$ret["min_sum_left"] = join("/" , $data["min_sum_left"]);

		$ret["status"] = (($this->prop("verified") || $this->verified) ? t("Kinnitatud") : t("Kinnitamata"));
		$ret["bank_value"] = $this->meta("bank_name");
		foreach ($this->meta("amount") as $prod => $amount)
		{
			if($amount)
			{
				$product = obj($prod);

				$this->vars(array(
					"prod_name" => $product->name(), "prod_amount" => $amount  , "prod_value"=> $product->prop("price") ,
				));
				$p.= $this->parse("PROD");
			}
		}

		if(is_oid($this->prop("customer")))
		{
			$customer = obj($this->prop("customer"));
			$ret["phone_value"] = $customer->prop("phone.name");
			$ret["email_value"] = $customer->prop("email.mail");;
		}
		$ret["name_value"] = $this->prop_str("customer");
		$ret["PROD"] = $p;
		return $ret;
	}

	private function get_time_str($arr)
	{
		$room_inst = get_instance(CL_ROOM);
		extract($arr);
		$res = "";
		$res.= $room_inst->weekdays[(int)date("w" , $arr["start"])];
		$res.= ", ";
		$res.= date("d.m.Y" , $arr["start"]);
		$res.= ", ";
		$res.= date("H:i" , $arr["start"]);
		$res.= " - ";
		$res.= date("H:i" , $arr["end"]);
		return $res;
	}

	/** Sets correct reservation name
	 **/
	public function set_correct_name()
	{
		$res_inst = get_instance(CL_RESERVATION);
		$this->set_name($res_inst->get_correct_name($this));
		$this->save();
	}

	/** Returns amount of products reserved for this reservation
		@attrib api=1
		@returns
			array of product id's as key's and amount as value
	 **/
	public function get_product_amount()
	{
		return $this->meta("amount");
	}

	/** Sets the amount of products reserved for this reservation.
		@attrib api=1 params=pos
		@param amount required type=array
			Array of product id's as key's and amount as value
		@param merge optional type=bool default=true
			If set to true, new data is merged with old (doesn't overwrite)
	 **/
	public function set_product_amount($amount, $merge = true)
	{
		if($merge)
		{
			$old = $this->get_product_amount();
			foreach($amount as $prod => $c) // damn, can't use array_merge here, this messes up the numeric array key's
			{
				$old[$prod] = $c;
			}
			$amount = $old;
		}
		$this->set_meta("amount", $amount);
	}

	/** Makes reservations for other rooms
		@attrib api=1 params=pos
		@param slaves required type=array
			Array of other rooms ids
	 **/
	public function make_slave_brons($slaves)
	{

		$exist2 = $this->get_other_bron_rooms();


		$exist = array();
		foreach($exist2 as $key2 => $val2)
		{
			$exist[$val2] = $key2;
		}

		//ei lase lisada kinnistele aegadele
		foreach($slaves as $asd => $key)
		{
			$room = obj($key);
			if(!$room->is_available(array(
				"start" => $this->prop("start1"),
				"end" => $this->prop("end"),
				"ignore_booking" => $exist[$key] ? $exist[$key] : null,
			)))
			{
				unset($slaves[$asd]);
			}
		}

		//vaatab 2kki m6ned juba olemas, ja mis yleliigne, kustutab 2ra
		foreach($slaves as $asd => $key)
		{
			if(array_key_exists($key,$exist))
			{
				unset($slaves[$asd]);
				unset($exist[$key]);
			}
		}
		foreach($exist as $ex_key => $ex)//neid pole vaja enam, kustutav 2ra
		{
			$e = obj($ex);
			$e->delete();
		}

		$inst = get_instance(CL_RESERVATION);
		foreach($slaves as $slave)
		{
			$b = new object();
			$b->set_parent($this->id());
			$b->set_name($this->name());
			$b->set_class_id(CL_RESERVATION);
			$b->set_prop("resource" , $slave);
			foreach($inst->get_from_parent_props as $prop)
			{
				$b->set_prop($prop , $this->prop($prop));
			}
			$b->save();
			$this->set_meta("has_other_brons" , 1);
			$this->save();
		}
		return 1;
	}

	/** Returns other reservations connected to this
		@attrib api=1 params=pos
		@return object list
	 **/
	public function get_other_brons()
	{
		$ol = new object_list(array(
			"class_id" => CL_RESERVATION,
			"lang_id" => array(),
			"site_id" => array(),
			"parent" => $this->id(),
		));
		return $ol;
	}

	/** Returns other reservation rooms connected to this
		@attrib api=1 params=pos
		@return array
	 **/
	public function get_other_bron_rooms()
	{
		$ol = new object_list(array(
			"class_id" => CL_RESERVATION,
			"lang_id" => array(),
			"site_id" => array(),
			"parent" => $this->id(),
		));
		$ret = array();
		foreach($ol->arr() as $o)
		{
			$ret[$o->id()] = $o->prop("resource");
		}
		return $ret;
	}

	/** checks if bron has a parent bron
		@attrib api=1
		@return boolean
			if has parent reservation, returns parent id, else 0
	 **/
	public function is_lower_bron()
	{
		if(!isset($this->is_lower_bron))
		{
			if($this->prop("parent.class_id") == CL_RESERVATION)
			{
				$this->is_lower_bron = $this->parent();
			}
			else
			{
				$this->is_lower_bron = 0;
			}
		}
		return $this->is_lower_bron;
	}

	/** checks if you can use bron time for other brons
		@attrib api=1
		@return boolean
	 **/
	public function is_dead()
	{
		if($this->prop("verified"))
		{
			return 0;
		}
		if(time() < $this->prop("deadline"))
		{
			return 0;
		}
		return 1;
	}

	/** returns all customer names
		@attrib api=1
		@return sting
	 **/
	public function get_customer_name()
	{
		$cus = array();
		foreach($this->connections_from(array("type" => "RELTYPE_CUSTOMER")) as $c)
		{
			$cus[] = $c->prop("to.name")." ";
		}
		$cus = join(", ", $cus);
		return $cus;
	}

	/**
		@attrib api=1
		@return boolean
	 **/
	public function get_products()
	{
		if(!$this->products)
		{
			$this->products = new object_list();
			$products = $this->meta("amount");
			foreach($products as $prod => $val)
			{
				if($val)
				{
					$this->products->add($prod);
				}
			}
		}
	}

	/** returns product codes
		@attrib api=1
		@return array
	 **/
	public function get_product_codes()
	{
		$ret = array();
		$this->get_products();
		foreach($this->products->arr() as $prod)
		{
			$ret[] = $prod->prop("code");
		}
		return $ret;
	}

	/** returns product image tags
		@attrib api=1 params=pos
		@param settings required type=object
			room settings object
		@return array()
	 **/
	public function get_product_images(&$settings)
	{
		$ret = array();
		$this->get_products();
		foreach($this->products->arr() as $product)
		{
			if ($product->class_id() == CL_SHOP_PRODUCT_PACKAGING)
			{
				$_conns = $product->connections_to(array("from.class_id" => CL_SHOP_PRODUCT));
				if (count($_conns))
				{
					$_con = reset($_conns);
					$product = $_con->from();
				}
			}
			$cons = $product->connections_from(array(
				"type" => "RELTYPE_IMAGE",
				"to.jrk" => $settings->prop("cal_show_prod_img_ord")
			));
			if (count($cons))
			{
				$con = reset($cons);
				if ($con)
				{
					$ii = get_instance(CL_IMAGE);
					$ret[] = $ii->make_img_tag_wl($con->prop("to"));
				}
			}
		}
		return $ret;
	}

	/** returns formated bron property for room calendar
		@attrib api=1 params=pos
		@param prop required type=string
			property name
		@param settings required type=object
			room settings object
		@return string
	 **/
	public function get_room_calendar_prop($prop , &$settings)
	{
		$value = "";
		switch($prop)
		{
			case "customer":
				$value = $this->get_customer_name();
				break;
			case "products_text":
				$this->get_products();
				$value = join(" ," , $this->products->names());
				break;
			case "product_code":
				$codes = $this->get_product_codes();
				$value = join(" ," , $codes);
				break;
			case "product_image":
				$image = $this->get_product_images($settings);
				$value = join(" ," , $image);
				break;
			case "cp_phone":
				$value = $this->prop("customer.phone.name");
				break;
			default:
				if($this->is_property($prop))
				{
					$value = $this->prop($prop);
				}
		}
		return $value;
	}

	function payment_marked()
	{
		return $o->prop("paid");
	}

}
?>
