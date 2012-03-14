<?php
/*
@default table=objects
@default group=general
@default field=meta
@default method=serialize

@classinfo syslog_type=ST_ROOM_RESERVATION relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@property rooms type=relpicker multiple=1 reltype=RELTYPE_ROOM store=connect field=meta method=serialize
@caption Ruumid

@property prices type=select multiple=1
@caption N&auml;ita hindu valuutades

@property multiple_reservations type=checkbox
@caption Ruume saab samal ajal broneerida mitu

@property reservation_template type=select
@caption Broneeringu template

@property ver_mail_template type=select
@caption Kinnitusmaili template

@property revoke_url type=textbox
@caption Broneerimise katkestamise url

@property levels type=table no_caption=1 store=no
@caption Tasemed

@groupinfo trans caption=T&otilde;lgi
@default group=transl

	@property trans type=callback callback=callback_get_trans store=no
	@caption T&otilde;lgi

@groupinfo order_email caption="Tellimusmeil"
@default group=order_email

	@property order_mail_from type=textbox
	@caption Meili from aadress

	@property order_mail_from_name type=textbox
	@caption Meili from nimi

	@property order_mail_subj type=textbox
	@caption Meili subjekt

	@property order_mail_legend type=text
	@caption Meili sisu legend

	@property order_mail_to type=textbox
	@caption Kellele tellimuse kohta meil saata

	@property order_mail_groups type=select multiple=1
	@caption Kasutajagrupid, kelle poolt tehtud broneeringute kohta meil saadetakse


@reltype ROOM value=1 clid=CL_ROOM
@caption Ruum mida broneerida


*/

class room_reservation extends class_base
{
	function room_reservation()
	{
		$this->init(array(
			"tpldir" => "common/room",
			"clid" => CL_ROOM_RESERVATION
		));
		$this->bank_inst = get_instance(CL_BANK_PAYMENT);
/*		$this->banks = array(
			"hansapank" => "Hansapank",
			"seb" => "&Uuml;hispank",
			"sampopank" => "Sampopank",
			"credit_card" => "Krediitkaart",
			"hansapanklv" => "LV-Hansasabankas",
			"hansapanklt" => "LT-Hansasabankas",
		);
*/
		$this->obligatory = array("name","phone","email");

		$this->banks = $this->bank_inst->banks;//$this->banks + $this->bank_inst->banks;

		$this->obligatory = array("name","phone","email");

		$this->trans_props = array(
			"revoke_url"
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
				case "levels":
					$this->do_table($arr);
					break;

				case "order_mail_legend":
					$prop["value"] = t("sisu tuleb common/room/preview.tpl failist");
					break;

				case "reservation_template":
					$tm = get_instance("templatemgr");
					$prop["options"] = $tm->template_picker(array(
						"folder" => "common/room"
					));
					if(!sizeof($prop["options"]))
					{
						$prop["caption"] .= t("\n".$this->site_template_dir."");
		//				$prop["type"] = "text";
		//				$prop["value"] = t("Template fail peab asuma kataloogis :".$this->site_template_dir."");
					}
					break;

				case "ver_mail_template":
					$tm = get_instance("templatemgr");
					$prop["options"] = $tm->template_picker(array(
						"folder" => "common/room"
					));
					if(!sizeof($prop["options"]))
					{
						$prop["caption"] .= t("\n".$this->site_template_dir."");
		//				$prop["type"] = "text";
		//				$prop["value"] = t("Template fail peab asuma kataloogis :".$this->site_template_dir."");
					}
					break;

				case "order_mail_groups":
					$ol = new object_list(array(
						"class_id" => CL_GROUP,
						"type" => "0",
						"lang_id" => array(),
						"site_id" => array()
					));
					$prop["options"] = $ol->names();
					break;

				case "prices":
					$arr["obj_inst"]->prop("type");
					$curr_list = new object_list(array("class_id" => CL_CURRENCY, "lang_id" => array()));
					$prop["options"] = $curr_list->names();
					break;

		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- set_property --//
			case "levels":
				$this->submit_meta($arr);
				break;
			case "trans":
				$this->trans_save($arr, $this->trans_props);
				break;
			}
		return $retval;
	}

	private function submit_meta($arr = array())
	{
		$meta = $arr["request"]["meta"];
		//praagib v2lja tasemed, kus ei ole kas adekvaatset template faili v6i nime
		if(($arr["prop"]["name"] == "levels") && is_array($meta))
		{
			$temp_arr = array();
			foreach($meta as $metadata)
			{
				if((strlen($metadata["name"]) > 0) && strlen($metadata["template"]) > 4)
				{
					$temp_arr[] = $metadata;
				}
			}
			$meta = $temp_arr;
		}
		if (is_array($meta))
		{
			$so = new object($arr["obj_inst"]->id());
			$so->set_name($arr["obj_inst"]->name());
			$so->set_meta($arr["prop"]["name"], $meta);
		//	$so->save();
		};
	}

	private function do_table($arr)
	{
		$levels = $arr["obj_inst"]->meta("levels");
		$t = &$arr["prop"]["vcl_inst"];

		$t->define_field(array(
			"name" => "id",
			"caption" => t("Tase"),
//			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Etapi nimi"),
		));

		aw_global_set("output_charset", "utf-8");
		$lg = get_instance("languages");
		$langdata = $lg->get_list();
		foreach($langdata as $id => $lang)
		{
			if($arr["obj_inst"]->lang_id() != $id)
			{
				$t->define_field(array(
					"name" => $id,
					"lang_id" => $id,
					"caption" => t($lang),
				));
			}
		}

		$t->define_field(array(
			"name" => "template",
			"caption" => t("Template"),
		));


		$tm = get_instance("templatemgr");
		$options = $tm->template_picker(array(
			"folder" => "common/room"
			));

		$transyes = $arr["obj_inst"]->prop("transyes");
//		$langdata = array();
		$count = 1;
		foreach($levels as $level)
		{
			$data = array(
				"id" => $count,
			);

			$data["name"] = html::textbox(array(
				"name" => "meta[".$count."][name]",
				"size" => 30,
				"value" => $level["name"],
			));

			foreach($langdata as $lid => $lang)
			{
				 $data[$lid] = html::textbox(array(
					"name" => "meta[".$count."][tolge][".$lid."]",
					"size" => 15,
					"value" => $level["tolge"][$lid],
				));
			}

			$data["template"] = html::select(array(
				"name" => "meta[".$count."][template]",
				"options" => $options,
				"value" => $level["template"],
			));
			$t->define_data($data);
			$count++;

		}
		$new_data = array(
			"id" => $count,
		);

		 $new_data["name"] = html::textbox(array(
			"name" => "meta[".$count."][name]",
			"size" => 30,
			"value" => "",
		));

		foreach($langdata as $lid => $lang)
		{
			 $new_data[$lid] = html::textbox(array(
				"name" => "meta[".$count."][tolge][".$lid."]",
				"size" => 15,
				"value" => "",
			));
		}

		$new_data["template"] = html::select(array(
			"name" => "meta[".$count."][template]",
			"options" => $options,
			"value" => "",
		));

//		$new_data["template"] = html::textbox(array(
//			"name" => "meta[".$count."][template]",
//			"size" => 30,
//			"value" => "",
//		));
		$t->define_data($new_data);
		$t->set_sortable(false);
	}


	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	/**
		@attrib name=parse_alias is_public="1" caption="Change" nologin=1
	**/
	function parse_alias($arr)
	{
		aw_session_set("no_cache", 1);
		global $level;
		enter_function("room_reservation::parse_alias");
if(!$level)
{
	$level = $_GET["level"];
}
		//antud juhul peaks objektist v6tma info hoopis... peaks olema just tagasi pangamakselt tulnud
		if($_GET["preview"])
		{
			if($_GET["tpl"])
			{
				$tpl = $_GET["tpl"];
			}
			else
			{
				$tpl = "preview.tpl";
			}
			$this->read_template($tpl);
			lc_site_load("room_reservation", &$this);
			$this->vars($this->get_object_data($_GET["id"]));
			$this->vars(array("error" => ($_SESSION["reservation"]["error"]) ? $this->parse("error") : "",));
			return $this->parse();
		}

		$targ = obj($arr["alias"]["target"]);

		//ruumi valiku jama... toodete tellimise jne jne jaoks.... et ruume v6ib olla mitu, on see paras k2kk
		// k6igepealt vaatab, kui on 1 ruum, siis kui on mitu, v6tab sessioonist ruumi id, kui see on olemas
		// kui pole, siis esimese ruumidest
		//seda jama dubleerib toodete vaates... sest vahepeal v6ib kalendris teine ruum valitud olla
		if(is_array($targ->prop("rooms")) && sizeof($targ->prop("rooms")) == 1 && $this->can("view", reset($targ->prop("rooms"))))
		{
			$room = obj(reset($targ->prop("rooms")));
		}
		elseif(is_oid($_SESSION["room_reservation"]["room_id"]) && $this->can("view" , $_SESSION["room_reservation"]["room_id"]))
		{
			$room =  obj($_SESSION["room_reservation"]["room_id"]);
		}
		elseif(is_array($_SESSION["room_reservation"]["room_id"]) && $this->can("view" , reset($_SESSION["room_reservation"]["room_id"])))
		{
			$room =  obj(reset($_SESSION["room_reservation"]["room_id"]));
		}
		elseif(is_array($targ->prop("rooms")) && $this->can("view", reset($targ->prop("rooms"))))
		{
			$room = obj(reset($targ->prop("rooms")));
		}
		else
		{
			$room = $targ->get_first_obj_by_reltype(array("type" => "RELTYPE_ROOM"));
		}

		if(!is_object($room))
		{
			return "";
		}
		$levels = $targ->meta("levels");
		$this->vars($this->get_level_urls($levels));

		//teeb viimasel vaatel testi, et kas k6ik kohustulslikud on t2idetud
		$last_l = max(array_keys($levels));
		if($last_l - 1 == $level)
		{
			foreach($this->obligatory as $obl)
			if(!array_key_exists($obl , $_SESSION["room_reservation"][$room->id()]))
			{
				$_SESSION["room_reservation"][$room->id()][$obl] = "";
			}//keegi v6is tark olla ja m88da hiilida viimasele lehele, et kui m6ndasi muutujaid ei ole siis t6idab kohustulsikud v6ljad tyhjade v22rtustega enne kontrolli
			if($err = $this->check_fields($_SESSION["room_reservation"][$room->id()]))
			{
				$_SESSION["room_reservation"][$room->id()]["stay"] = 1;
				$_SESSION["room_reservation"][$room->id()]["errors"] = $err;
			}
		}

		if(!$_SESSION["room_reservation"][$room->id()]["stay"])
		{
			if(!isset($level))
			{
				$level=0;
			}
			else
			{
				$level++;
			}
		}
		else
		{
			unset($_SESSION["room_reservation"][$room->id()]["stay"]);
		}
		$tpl = $levels[$level]["template"];

		$this->read_template($tpl);
		lc_site_load("room_reservation", &$this);


		$data = array("joga" => "jogajoga");
		$data["revoke_url"] = $this->mk_my_orb("revoke_reservation", array("room" => $room->id() , "room_res" => $targ->id()));

		//seda vaja, et toodete tabelis vana crapi ka n2ha oleks,....et vahepeal v6ib ruum muutunud olla
		$this->set_cart_session($room);

		$this->vars($this->get_site_props(array(
			"room" => $room,
			"id" => $arr["alias"]["target"],
		)));
		$this->vars($data);

		$p = "";
		foreach ($_SESSION["room_reservation"][$room->id()]["products"] as $prod => $amount)
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

		$back_level = $level-2;
		if($back_level < 0)
		{
			$back_url = $_SERVER["PATH_INFO"];
		}
		else
		{
			$back_url = $_SERVER["PATH_INFO"]."?level=".$back_level;
			//$back_url = aw_global_get("section")."?level=".($back_level);
			$this_url = $_SERVER["PATH_INFO"]."?level=".($back_level+1);
		}
		$this->vars(array(
			"room_name" => $room->name(),
			"PROD" 	=> $p,
			"errors" => $_SESSION["room_reservation"][$room->id()]["errors"],
			"CONTINUE" => ($_SESSION["room_reservation"][$room->id()]["start"]) ? $this->parse("CONTINUE") : "",
			"continue_submit" => $_SESSION["room_reservation"][$room->id()]["start"] ? "changeform.submit();" : "alert('".t("Vali enne j&auml;tkamist sobiv aeg!")."');",
			//"continue_alt" => !$_SESSION["room_reservation"][$room->id()]["start"] ? "alert();" : "",//t("Vali enne j&auml;tkamist sobiv aeg!") : "",
			"reforb" => $this->mk_reforb("parse_alias",array(
				"section"	=> aw_global_get("section"),
				"level"		=> $level,
				"return_to"	=> post_ru(),
				"id"		=> $arr["alias"]["target"],
				"do"		=> $do,
				"parent"	=> $parent,
				"clid"		=> $clid,
				"default"	=> $default,
			)),
			"this_url" => $this_url,
			"path"	=> $_SERVER["PATH_INFO"],
			"back_url" => $back_url,

			"url" => aw_global_get("section")."?level=".$level,
			"submit" => $this->mk_my_orb('submit_data',array(
				'id' => $arr["alias"]["target"],
				'level' => $level,
				'url' => aw_global_get("section")."?level=".$level,
				"return_to"	=> post_ru(),
			), CL_ROOM_RESERVATION),
			"affirm_url" => $this->mk_my_orb("affirm_reservation", array(
				"section" => aw_global_get("section"),
				"level" => $level,
				"id" => $id,
				"room" => $room->id(),
				"res" => $targ->id(),
			)),
			"pay_url" => $this->mk_my_orb("pay_reservation", array(
//				"section" => aw_global_get("section"),
//				"level" => $level,
				"bron_id" => $_SESSION["room_reservation"][$room->id()]["bron_id"],
				"room" => $room->id(),
				"res" => $targ->id(),
			"error" => ($_SESSION["reservation"]["error"]) ? $this->parse("error") : "",
			)),
		));
		$_SESSION["room_reservation"][$room->id()]["errors"] = null;
		//property v22rtuse saatmine kujul "property_nimi"_value
		exit_function("room_reservation::parse_alias");
		return $this->parse();
	}

	private function set_cart_session($room)
	{
		unset($_SESSION["cart"]);
		$_SESSION["soc_err"] = $_SESSION["room_reservation"][$room->id()]["products"];
		foreach($_SESSION["room_reservation"][$room->id()]["products"] as $oid=> $val)
		{
			if($val)
			{
				$o = obj($oid);
				if($o->class_id() == CL_SHOP_PRODUCT_PACKAGING)
				{
					//$_SESSION["cart"]["items"][$oid]["items"] = $val;
					$ol = new object_list(array("lang_id" => array(), "class_id" => CL_SHOP_PRODUCT, "CL_SHOP_PRODUCT.RELTYPE_PACKAGING" => $oid));
					if(sizeof($ol->arr()))
					{
						$prod = reset($ol->arr());
						$_SESSION["cart"]["items"][$prod->id()][$oid]["items"] = $val;
					}

				}
				else
				{
					$_SESSION["cart"]["items"][$oid]["items"] = $val;
				}
				$_SESSION["soc_err"][$oid] = array("ordered_num_enter" => $val);
			}
		}
	}

	/** Returns the total price for the reservation
		@attrib api=1 params=name

			@param bron optional type=object
				The reservation to calculate price for

			@param room optional type=oid
				The room the reservation is in

			@param people optional type=int
				Number of people

			@param start optional type=int
				Timestamp for start of reservation

			@param end optional type=int
				Timestamp for end of reservation

			@param products optional type=array
				array { product_id => how_many } for all products in the reservation

		@comment
			et siis arvutab broneeringu hinna arvestades miinimumhinda, ja kui anda objekt ette, siis v6tab ylej22nud info sealt

		@returns
			The total price for the reservation for all defined currencies
				array { currency_id => price in that currency }
	**/
	function get_total_bron_price($arr)
	{
		extract($arr);
		$room_inst = get_instance(CL_ROOM);
		if(is_object($bron))
		{
			$room = $bron->prop("resource");
			$people = $bron->prop("people_count");
			$start = $bron->prop("start1");
			$end = $bron->prop("end");
			$products = $bron->meta("amount");
		}
		$room = obj($room);
		$min_prices = $room->meta("web_room_min_price");
		$sum = $room_inst->cal_room_price(array(
			"room" => $room->id(),
			"people" => $people,
			"start" => $start,
			"end" => $end,
			"products" => $products,
		));
		foreach($sum as $cur => $val)
		{
			if($min_prices[$cur] > 0 && $min_prices[$cur] > $val)
			{
				$sum[$cur] = $min_prices[$cur];
			}
		}
		return $sum;
	}

	function get_object_data($id)
	{
		$ret = array();
		if(!is_oid($id) || !$this->can("view" , $id))
		{
			return $ret;
		}
		$bron = obj($id);//arr($bron->meta("lang"));
		$room = obj($bron->prop("resource"));
		$ret["room_name"] = $room->name();
		$ret["time_str"] = $this->get_time_str(array(
			"start" => $bron->prop("start1"),
			"end" => $bron->prop("end"),
		));
		$ret["hours"] = ($bron->prop("end")-$bron->prop("start1"))/3600;
		$ret["people_value"] = $bron->prop("people_count");

		$room_inst = get_instance(CL_ROOM);
		$sum = $room_inst->cal_room_price(array(
			"room" => $bron->prop("resource"),
			"people" => $ret["people_value"],
			"start" => $bron->prop("start1"),
			"end" => $bron->prop("end"),
			"products" => -1,
		//	"products" => $bron->meta("amount"),
		));
		$data["sum"] = $data["sum_wb"] = $data["bargain"] = $data["menu_sum"] = $data["menu_sum_left"] = array();

		$prod_discount = $room_inst->get_prod_discount(array(
			"room" => $bron->prop("resource"),
			"start" => $bron->prop("start1"),
			"end" => $bron->prop("end"))
		);
		foreach($sum as $curr => $val)
		{
			$currency = obj($curr);
	//		$data["sum"][] =  $val." ".$currency->name();
			$data["bargain"][] = (0+$room_inst->bargain_value[$curr])." ".$currency->name();
			$data["sum_wb"][] = ((double) $val + (double)$room_inst->bargain_value[$curr]) ." ".$currency->name();
		}
		foreach ($bron->meta("amount") as $prod => $amount)
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

		$sum = $this->get_total_bron_price(array(
			"bron" => $bron,
		));



		$min_prod_prices = $room->meta("web_min_prod_prices");
		$min_prices = $room->meta("web_room_min_price");
		foreach($sum as $curr => $val)
		{
			$currency = obj($curr);
			$data["sum"][] =  $val." ".$currency->name();
		//	$min_sum = $min_prices[$curr] - $val;
			$min_sum = $min_prices[$curr] - $data["menu_sum"][$curr] + $data["menu_disc"][$curr];
			if($min_sum < 0)
			{
				$min_sum = 0;
			}
			$data["min_sum_left"][] = $min_sum." ".$currency->name();

			if($val || $val == 0)
			{
				$data["sum_pay"][] = $val." ".$currency->name();
			}

		}

		$ret["sum_pay"] = join("/" , $data["sum_pay"]);

		$ret["sum"] = join("/" , $data["sum"]);
		$ret["bargain"] = join("/" , $data["bargain"]);
		$ret["sum_wb"] = join("/" , $data["sum_wb"]);
		$ret["menu_sum"] = join("/" , $data["menu_sum"]);
		$ret["comment_value"] = $bron->prop("content");
		$ret["min_sum_left"] = join("/" , $data["min_sum_left"]);

		$ret["status"] = ($bron->prop("verified") ? t("Kinnitatud") : t("Kinnitamata"));
		$ret["bank_value"] = $bron->meta("bank_name");
		foreach ($bron->meta("amount") as $prod => $amount)
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

		if(is_oid($bron->prop("customer")))
		{
			$customer = obj($bron->prop("customer"));
			$ret["phone_value"] = $customer->prop("phone.name");
			$ret["email_value"] = $customer->prop("email.mail");;
		}
		$ret["name_value"] = $bron->prop_str("customer");
		$ret["PROD"] = $p;
		return $ret;
	}

	//returns url for each level
	private function get_level_urls($levels)
	{
		$data = array();
		foreach($levels as $key => $level)
		{

			if(!($key))
			{
				$data[$level["name"]."_url"] = aw_global_get("section");
			}
			else
			{
				$data[$level["name"]."_url"] = aw_global_get("section")."?level=".($key-1);
			}
		}

		$data["pay_url"] = "http://link.maksmisesse.aw";
		return $data;
	}

	private function get_site_props($arr)
	{
		extract($arr);
		$data = array();
		$people_opts = array();
		$x=1;
		while($x < ($room->prop("max_capacity") + 1))
		{
			$people_opts[$x] = $x;
			$x++;
		}
		$data["people"] = html::select(array(
			"options" => $people_opts,
			"value" => $_SESSION["room_reservation"][$room->id()]["people"],
			"name" => "people",
		));
		$data["comment"] = html::textarea(array(
			"value" => $_SESSION["room_reservation"][$room->id()]["comment"],
			"name" => "comment",
		));
		$data["name"] = html::textbox(array(
			"value" => $_SESSION["room_reservation"][$room->id()]["name"],
			"name" => "name",
		));
		$data["email"] = html::textbox(array(
			"value" => $_SESSION["room_reservation"][$room->id()]["email"],
			"name" => "email",
		));
		$data["phone"] = html::textbox(array(
			"value" => $_SESSION["room_reservation"][$room->id()]["phone"],
			"name" => "phone",
		));

		if($this->can("view" , $room->prop("location")))
		{
			$loc = obj($room->prop("location"));
			$bank_inst = get_instance(CL_BANK_PAYMENT);
			$bank_payment = $loc->prop("bank_payment");
		}
		if(is_oid($bank_payment))
		{

			if(!$_SESSION["room_reservation"][$room->id()]["bank"])
			{
				$need_to_choose_default_bank = 1;
			}
			$payment = obj($bank_payment);
			foreach($payment->meta("bank") as $key => $val)
			{
				if(!$val["sender_id"])
				{
					continue;
				}
				$checked=0;
				if($_SESSION["room_reservation"][$room->id()]["bank"] == $key || $need_to_choose_default_bank)
				{
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

		$data["products_link"] = html::popup(array(
			"url" 	=> $this->mk_my_orb("get_web_products_table", array(
					"id" => $id,
					"room" => $room->id(),
				)),
			"no_link" => 1,
			"width" => 770,
			"height" => 600,
			"scrollbars" => 1,
		));
		//"http://link.produktide_juurde.aw";
		$data["calendar_link"] = html::popup(array(
			"url" 	=> $this->mk_my_orb("get_web_calendar_table", array(
					"id" => $id,
					"room" => $room->id(),
				)),
			"no_link" => 1,
			"scrollbars" => 1,
			"width" => 770,
			"height" => 600,
		));
		$room_inst = get_instance(CL_ROOM);

		$sum = $room_inst->cal_room_price(array(
			"room" => $room->id(),
			"people" => $_SESSION["room_reservation"][$room->id()]["people"],
			"start" => $_SESSION["room_reservation"][$room->id()]["start"],
			"end" => $_SESSION["room_reservation"][$room->id()]["end"],
			"products" => -1,
		));
		//muidu annab massiivi k6ikide valuutade hindadega... et eks selgub, kuda seda hiljem tahetakse
		$room_res = obj($arr["id"]);

		$show_curr = $room_res->prop("prices");

		$data["sum"] = $data["sum_wb"] = $data["bargain"] = $data["menu_sum"]= $data["sum_pay"]= array();

		$prod_discount = $room_inst->get_prod_discount(array(
			"room" => $room->id(),
			"start" => $_SESSION["room_reservation"][$room->id()]["start"],
			"end" => $_SESSION["room_reservation"][$room->id()]["end"],
		));
// 		foreach ($_SESSION["room_reservation"][$room->id()]["products"] as $prod => $amount)
// 		{
// 			if($amount)
// 			{
// 				$product = obj($prod);
// 				$prices = $product->meta("cur_prices");
// 				foreach ($show_curr as $curr)
// 				{
// 					if($prices[$curr] || $prices[$curr] === 0)
// 					{
// 						$data["menu_sum"][$curr] = $data["menu_sum"][$curr] + $prices[$curr]*$amount;
// 						$data["menu_disc"][$curr]+=$prices[$curr]*$amount*$prod_discount*0.01;
// 					}
// 					else
// 					{
// 						$data["menu_sum"][$curr] = $data["menu_sum"][$curr]+$product->prop("price")*$amount;
// 						$data["menu_disc"][$curr]+=$product->prop("price")*$amount*$prod_discount*0.01;
// 					}
// 				}
// 			}
// 		}
//
		foreach ($show_curr as $curr)
		{
			$currency = obj($curr);
			if($sum[$curr] || $sum[$curr] == 0)
			{
				$data["menu_sum"][$curr] += $room_inst->cal_products_price(array(
					"products" => $_SESSION["room_reservation"][$room->id()]["products"],
					"currency" => $curr,
					"prod_discount" => $prod_discount,
					"room" => $room,
				));
				$data["menu_disc"][$curr]+= $room_inst->last_discount;
				$data["menu_sum"][$curr] += $room_inst->last_discount;//menyy hinda n2itab kokku vist

				$data["sum"][$curr] = $sum[$curr]." ".$currency->name();
				$data["bargain"][$curr] = ($data["menu_disc"][$curr]+$room_inst->bargain_value[$curr])." ".$currency->name();
				$data["sum_wb"][$curr] = ((double)$sum[$curr] +  (double)$room_inst->bargain_value[$curr])." ".$currency->name();
			}
		}

		$min_prod_prices = $room->meta("web_min_prod_prices");
		$min_prices = $room->meta("web_room_min_price");
		foreach ($show_curr as $curr)
		{
			if(!$data["menu_sum"][$curr])
			{
				$data["menu_sum"][$curr] = 0;
			}
			$currency = obj($curr);

			$min_sum = $min_prices[$curr] - $data["menu_sum"][$curr] + $data["menu_disc"][$curr];
			
			if($min_sum < 0)
			{
				$min_sum = 0;
			}
			$data["min_sum_left"][] = $min_sum." ".$currency->name();

			$data["menu_sum"][$curr] = $data["menu_sum"][$curr]." ".$currency->name();
			$data["menu_disc"][$curr] = $data["menu_disc"][$curr]." ".$currency->name();

		}

		$sum = $this->get_total_bron_price(array(
			"room" => $room->id(),
			"people" => $_SESSION["room_reservation"][$room->id()]["people"],
			"start" => $_SESSION["room_reservation"][$room->id()]["start"],
			"end" => $_SESSION["room_reservation"][$room->id()]["end"],
			"products" => $_SESSION["room_reservation"][$room->id()]["products"],
		));
		foreach ($show_curr as $curr)
		{
			$currency = obj($curr);
			if($sum[$curr] || $sum[$curr] == 0)
			{
				$data["sum_pay"][] = $sum[$curr]." ".$currency->name();
			}
		}

		$data["sum"] = join("/" , $data["sum"]);
		$data["sum_pay"] = join("/" , $data["sum_pay"]);
		$data["bargain"] = join("/" , $data["bargain"]);
		$data["sum_wb"] = join("/" , $data["sum_wb"]);
		$data["menu_sum"] = join("/" , $data["menu_sum"]);
		$data["menu_disc"] = join("/" , $data["menu_sum"]);
		$data["min_sum_left"] = join("/" , $data["min_sum_left"]);

		$data["time_from"] = "";
		$data["time_to"] = "";
		$data["time_day"] = "";
		$data["time"] = "";
		$data["hours"] = (int)(($_SESSION["room_reservation"][$room->id()]["end"]-$_SESSION["room_reservation"][$room->id()]["start"] + 60) / 3600);
		$data["minutes"] = (int)(($_SESSION["room_reservation"][$room->id()]["end"]-$_SESSION["room_reservation"][$room->id()]["start"]) / 60) - $data["hours"]*60;
		$data["time_str"] = $this->get_time_str(array(
			"start" => $_SESSION["room_reservation"][$room->id()]["start"],
			"end" => $_SESSION["room_reservation"][$room->id()]["end"]
		));
		if($_SESSION["room_reservation"][$room->id()]["start"])$data["date_start"] =  date("d.m.Y" , $_SESSION["room_reservation"][$room->id()]["start"]);
		$data["date"] = $this->get_date_str(array(
			"start" => $_SESSION["room_reservation"][$room->id()]["start"],
			"end" => $_SESSION["room_reservation"][$room->id()]["end"]
		));
		$data["time"] = $this->get_time_(array(
			"start" => $_SESSION["room_reservation"][$room->id()]["start"],
			"end" => $_SESSION["room_reservation"][$room->id()]["end"]
		));
		foreach($_SESSION["room_reservation"][$room->id()] as $key => $val)
		{
			$data[$key."_value"] = $val;
		}
		$data["bank_value"] = $this->get_bank_caption($_SESSION["room_reservation"][$room->id()]);
		return $data;
	}

	private function get_bank_caption($sess_data)
	{
		$ret = $this->banks[$sess_data["bank"]."_".$sess_data["bank_country"]];
		if(!$ret)
		{
			$ret = $this->banks[$sess_data["bank"]];
		}
		return $ret;
	}


	private function get_time_($arr)
	{
		if(!$arr["start"])
		{
			return "";
		}
		$res.= date("H:i" , $arr["start"]);
		$res.= " - ";
		$res.= date("H:i" , $arr["end"]);
		return $res;
	}

	private function get_date_str($arr)
	{//arr($arr);
		if(!$arr["start"]) return "";
		$room_inst = get_instance(CL_ROOM);
		extract($arr);
		$res = $res1 = $res2 = "";

		$res1.= date("d.m.Y" , $arr["start"]);
		$res2.= date("d.m.Y" , $arr["end"]);
		if($res2 == $res1)
		{
			$res = $res1;
		}
		else
		{
			$res = $res1." - ".$res2;
		}
		return $res;
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

	/** submit_data
		@attrib name=submit_data nologin="1"
		@param id required type=int
		@param return_to required type=string
		@param level optional type=int
	**/
	function submit_data($args = array())//tegeleb postitatud infoga
	{
		$bron_obj = obj($args["id"]);
		if( $_SESSION["room_reservation"]["room_id"])
		{
			$room =  $_SESSION["room_reservation"]["room_id"];
		}
		else
		{
			$room = $bron_obj->prop("rooms");
		}

		if(!is_array($room))
		{
			$room = array($room);
		}
		foreach($room as $r)
		{
			$this->submit_one_room($r);
		}
		extract($args);//arr($_GET["level"]);
		$url = parse_url($args["return_to"]);
		if(!$level && !($level==0))
		{
			return $args["return_to"];
		}
		else
		{
			return $url["scheme"]."://".$url["host"].$url["path"]."?level=".$level;
		}
	}

	/**
		@param room required type=oid
	**/
	private function submit_one_room($room)
	{
		foreach($_POST as $key=>$val)
		{
			$val = htmlspecialchars($val);
			$_SESSION["room_reservation"][$room][$key] = $val;
		}

		if($err = $this->check_fields($_POST))
		{
			$_SESSION["room_reservation"][$room]["stay"] = 1;
			$_SESSION["room_reservation"][$room]["errors"] = $err;
		}
	}

	private function check_fields($data)
	{
		$ret = "";
		if(array_key_exists("name" , $data) && !(strlen($data["name"]) > 1))
		{
			$ret.= t("Nimi on kohustuslik")."\n<br>";
		}
		if(array_key_exists("phone" , $data) && !(strlen($data["phone"]) > 1))
		{
			$ret.= t("Telefon on kohustuslik")."\n<br>";
		}
		if(array_key_exists("email" , $data) && !(strlen($data["email"]) > 5))
		{
			$ret.= t("E-Mail on kohustuslik")."\n<br>";
		}
		return $ret;
	}

	/**
		@attrib name=get_web_products_table api=1 params=name nologin=1
		@param room required type=int
	**/
	function get_web_products_table($arr)
	{//arr($_SESSION["room_reservation"]["room_id"]);
		extract($arr);
		if(is_oid($id) && $this->can("view", $id))
		{
			$targ = obj($targ);
			if(is_array($targ->prop("rooms")) && sizeof($targ->prop("rooms")) == 1)
			{
				$room = obj(reset($targ->prop("rooms")));
			}
			elseif(is_oid($_SESSION["room_reservation"]["room_id"]) && $this->can("view" , $_SESSION["room_reservation"]["room_id"]))
			{
				$room =  obj($_SESSION["room_reservation"]["room_id"]);
			}
			elseif(is_array($_SESSION["room_reservation"]["room_id"]))
			{
				$room = reset($_SESSION["room_reservation"]["room_id"]);
			}
		}
		if(!is_object($room) && is_oid($room) && $this->can("view", $room))
		{
			$room = obj($room);
		}
		else
		{
			return false;
		}
		
		$t = new vcl_table;
		$res_inst = get_instance(CL_RESERVATION);
		$html = $res_inst->_get_products_order_view(array(
			"prop" => array("vcl_inst" => &$t),
			"web" => 1,
			"room" => $room->id(),
		));
		//kui tuleb kuskilt kaugelt mystilisest templatest tellimise vaade, siis j22b asi nii nagu on... muidu teeb tabeli
		if(!$html)
		{
			$html = $t->draw();
		}

		$sf = new aw_template;
		$sf->db_init();
		$sf->tpl_init("automatweb");
		$sf->read_template("index.tpl");
			lc_site_load("room_reservation", &$sf);
		$action = $this->mk_my_orb("submit_web_products_table", array("room" => $room->id()));
		$sf->vars(array(
			"content" => "<form name='products_form' action=".$action." method=POST>".$html."<br></form>",
			"uid" => aw_global_get("uid"),
			"charset" => aw_global_get("charset")
		));

		$s = $sf->parse();
		die($s);
	}

	/**
		@attrib name=submit_web_products_table api=1 params=name nologin=1
		@param amount optional type=array
		@param room required type=oid
	**/
	function submit_web_products_table($arr)
	{
		//see siis variant kui info tuleb templatest jne
		if(is_array($arr["add_to_cart"]))
		{
			$_SESSION["room_reservation"][$arr["room"]]["products"] = $arr["add_to_cart"];
		}
		else
		{
			$_SESSION["room_reservation"][$arr["room"]]["products"] = $arr["amount"];
		}
		$ret.= '<script language="javascript">
			window.opener.document.getElementById("stay").value=1;
			window.opener.document.getElementById("changeform").submit();
			window.close();
		</script>';
		//return $ret;
		die($ret);
	}

	/**
		@attrib name=get_web_calendar_table api=1 params=name nologin=1
		@param room optional type=int
		@param id optional type=int
	**/
	function get_web_calendar_table($arr)
	{
		if(is_oid($arr["id"]))
		{
			$room_res = obj($arr["id"]);
			$rooms = $room_res->prop("rooms");
		}
		else
		{
			$rooms = array($arr["room"]);
		}

		$room_tmp = array();
		foreach($rooms as $key => $val)
		{
			$room_obj = obj($val);
			$room_tmp[$val] = $room_obj->ord();
		}
		asort($room_tmp,SORT_NUMERIC);
		$rooms = $room_tmp;

		$rooms = array_keys($rooms);
		$tables = "";
		

		$sf = new aw_template;
		$sf->db_init();

		if($room_res->prop("reservation_template"))
		{
			$sf->tpl_init("common/room");
			$tpl = $room_res->prop("reservation_template");
		}
		else
		{
			$sf->tpl_init("automatweb");
			$tpl = "index.tpl";
		}
		$sf->read_template($tpl);
		lc_site_load("room_reservation", &$sf);
		$action = $this->mk_my_orb("submit_web_calendar_table", array("room" => $arr["room"], "room_res" => $arr["id"]));
		$arr["obj_inst"] = obj($arr["room"]);
		$res_inst = get_instance(CL_ROOM);
		$select = $res_inst->_get_calendar_select($arr);
		$hidden = $res_inst->_get_hidden_fields($arr);
		if($_GET["start"])
		{
			$arr["request"]["start"] = $_GET["start"];
		}
		if($sf->is_template("CALENDAR"))
		{
			$c = "";
			foreach($rooms as $room)
			{
				$t = new vcl_table();
				$room_obj = obj($room);
				$res_inst->_get_calendar_tbl(array(
					"prop" => array("vcl_inst" => &$t),
					"room" => $room,
					"web" => 1,
				));
				$sf->vars(array(
					"calendar" => $t->draw(),
					"room_name" => $room_obj->name(),
				));
				$c.= $sf->parse("CALENDAR");
			}
			$sf->vars(array(
				"CALENDAR" => $c,
				"select" => $select,
				"hidden" => $hidden,
				"time_select" => $res_inst->_get_time_select($arr).$hidden,
				"length_select" => $res_inst->_get_length_select($arr),
				"submit_url" => $action,
			));
			if(!$arr["obj_inst"]->prop("use_product_times"))
			{
				$sf->vars(array("SELECT" => $sf->parse("SELECT")));
			}
		}
		else
		{
			foreach($rooms as $room)
			{
				$t = new vcl_table();
				$res_inst->_get_calendar_tbl(array(
					"prop" => array("vcl_inst" => &$t),
					"room" => $room,
					"web" => 1,
	//
				));
				$tables.= $t->draw();
			}
		}
		$sf->vars(array(
			"content" => "<form name='products_form' action=".$action." method=POST>".$select.$tables."<br>".html::submit(array("value" => t("Salvesta")))."</form>",
			"uid" => aw_global_get("uid"),
			"charset" => aw_global_get("charset")
		));

//		die($ret);
		die($sf->parse());

		die($t->draw());
	}

	/**
		@attrib name=submit_web_calendar_table api=1 params=name nologin=1
		@param bron optional type=array
		@param room required type=oid
		@param room_res optional type=oid
		@param room_reservation_length optional type=int
	**/
	function submit_web_calendar_table($arr)
	{
		$room_inst = get_instance(CL_ROOM);
		$room = $arr["room"];
		if(is_oid($arr["room_res"]) && $this->can("view" , $arr["room_res"]))
		{
			$rr = obj($arr["room_res"]);
			if($rr->prop("multiple_reservations"))
			{
				$multiple = 1;
				$room = array();
			}
		}
		foreach($arr["bron"] as $id => $bron)
		{
			if(array_sum($bron))//v]tab esimese kalendri kus oli miskit valitud
			{
				$times = $room_inst->_get_bron_time(array(
					"bron" => $bron,
					"id" => $id,
					"room_reservation_length" => $arr["room_reservation_length"],
				));
				$_SESSION["room_reservation"][$id]["start"] = $times["start"];
				$_SESSION["room_reservation"][$id]["end"] = $times["end"];
				if($multiple)
				{
					$room[] = $id;
				}
				else
				{
					$room = $id;//et siis nyyd juhul kui oli tegutsetud teise ruumiga... siis nyyd see k]ik muutud... paremuse poole kindlasti
				}
			}
		}//if(aw_global_get("uid") == "struktuur"){arr($_SESSION["room_reservation"]);arr($arr);die();}
		//tegelt teised ruumid 'ra nullida oleks vaja ... vist.... j2tame selle tuleviku tarkadele otsustada
		$_SESSION["room_reservation"]["room_id"] = $room;

		//teeb kohe algul broneeringu 2ra... igal kalenri submittimisel salvestab
		$bron_id = $_SESSION["room_reservation"][$room]["bron_id"];
		$_SESSION["room_reservation"][$room]["bron_id"] = $room_inst->make_reservation(array(
			"not_verified" => 1,//veebi poolelt et ei kinnitaks 2ra
			"id" => $room,
			"res_id" => $bron_id,
			"data" => $_SESSION["room_reservation"][$room],
			"tpl" => $tpl,
		));
		$ret.= '<script language="javascript">
			window.opener.document.getElementById("stay").value=1;
			window.opener.document.getElementById("changeform").submit();
			window.close();
		</script>';
		//return $ret;
		die($ret);
	}

	/** makes the reservation object ... then this stuff is ready for paying and stuff
		@attrib name=affirm_reservation api=1 params=name nologin=1
		@param bron_id optional type=array
			reservation id
		@param room required type=oid,array
			room id
		@param section optional type=string
			aw section
		@param level optional type=int
			web reservarion level
		@param res optional type=oid
			web room reservation object
	**/
	function affirm_reservation($arr)
	{
		if( $_SESSION["room_reservation"]["room_id"])
		{
			$arr["room"] =  $_SESSION["room_reservation"]["room_id"];
		}
		extract($arr);
		$room_inst = get_instance(CL_ROOM);
		if(!is_array($room))
		{
			$room = array($room);
		}
		if(is_oid($res_obj) && $this->can("view" , $res_obj))
		{
			$res_obj = obj($res_obj);
			$tpl = $res_obj->prop("ver_mail_template");
		}
		foreach($room as $r)
		{
			if(!$bron_id)
			{
				$bron_id = $_SESSION["room_reservation"][$room]["bron_id"];
			}
			$_SESSION["room_reservation"][$room]["bron_id"] = $room_inst->make_reservation(array(
				"not_verified" => 1,//veebi poolelt et ei kinnitaks 2ra
				"id" => $room,
				"res_id" => $bron_id,
				"data" => $_SESSION["room_reservation"][$room],
				"tpl" => $tpl,
			));
		}
		//return $section."?level=".$level;
	}

	/** makes the reservation object ... then this stuff is ready for paying and stuff
	@attrib name=pay_reservation api=1 params=name nologin=1
		@param bron_id optional type=array
			reservation id
		@param room required type=oid
			room id
		@param section optional type=string
			aw section
		@param level optional type=int
			web reservarion level
		@param res optional type=oid
			web room reservation object
	**/
	function pay_reservation($arr)
	{
		if($_SESSION["room_reservation"]["room_id"])
		{
			$arr["room"] =  $_SESSION["room_reservation"]["room_id"];
		}
		extract($arr);

		//et ei saaks mingi valemiga maksma minna seda mille eest on juba makstud
		if($this->can("view" , $bron_id))
		{
			$bron_object = obj($bron_id);
			if($bron_object->prop("paid"))
			{
				return $this->mk_my_orb("parse_alias", array("level" => 1, "preview" => 1, "id" => $bron_id));
			}
		}

// 		$lang = aw_global_get("lang_id");

		$bron_ids = array();
		$bron_names = array();
		$total_sum = 0;
		$room_inst = get_instance(CL_ROOM);
		if(!is_array($room))
		{
			$room = array($room);
		}
		if(is_oid($res) && $this->can("view" , $res))
		{
			$res_obj = obj($res);
			$tpl = $res_obj->prop("ver_mail_template");
		}

//j2rgnev 6nnetu tsykkel tuleks ringi teha, ... v2hemalt siis kui peaks veebist mitme ruumi broneerimine k2iku minema
		foreach($room as $r)
		{
			$r = obj($r);
			//miski imevalemiga on keegi oma reserveeringut yle kirjutama p22senud.... j2rgnev vast aitab selle vastu.... point tegelikult et ei oleks maksmisele mindud ja siis peale seda sama bronni id'ga make_reservation funktsiooni mindud... see tuleb v2listada
			if(!(is_array($_SESSION["room_reservation"][$r->id()]) && (sizeof($_SESSION["room_reservation"][$r->id()]))))
			{
				continue;
			}

			if(!$bron_id)
			{
				$bron_id = $_SESSION["room_reservation"][$r->id()]["bron_id"];
			}
			$_SESSION["room_reservation"][$r->id()]["bron_id"] = $room_inst->make_reservation(array(
				"id" => $r->id(),
				"res_id" => $bron_id,
				"not_verified" => 1,//veebi poolelt et ei kinnitaks 2ra
				"data" => $_SESSION["room_reservation"][$r->id()],
				"tpl" => $tpl,
			));
			$bron = obj($_SESSION["room_reservation"][$r->id()]["bron_id"]);
			$bron->set_meta("bank_name",$this->get_bank_caption($_SESSION["room_reservation"][$r->id()]));
			$bron->set_meta("room_reservation_id",$arr["res"]);
			$bron->save();
//			$bron->set_meta("lang" , $lang);
			$bron_ids[] = $bron->id();
			$bron_names[] = $bron->name();
			$sum = $this->get_total_bron_price(array(
				"bron" => $bron,
			));
			//2 lolli asja j2rjest
			foreach($sum as $curr => $val)
			{
				$c = obj($curr);
				if($c->name() == "EEK")
				{
					$sum = $val;
				}
			}
			$total_sum+= $sum;
			$bank = $_SESSION["room_reservation"][$r->id()]["bank"];
			$bank_country = $_SESSION["room_reservation"][$r->id()]["bank_country"];
			$lang = $_SESSION["room_reservation"][$r->id()]["lang"];
			$_SESSION["room_reservation"][$r->id()] = null;
		}

		//peab ikka k6ik igaks juhuks 2ra kustutama, nii on lollikindlam
		unset($_SESSION["room_reservation"]);

		if(!is_oid($r->prop("location")))
		{
			return;
		}

		$loc = obj($r->prop("location"));
		$bank_inst = get_instance(CL_BANK_PAYMENT);
		$bank_payment = $loc->prop("bank_payment");
		//if(aw_global_get("uid") == "struktuur"){arr($lang); die();}
		$_SESSION["bank_payment"]["url"] = null;

		if(is_oid($res))
		{
			$expl_res = " (".$res.")"; //et broneerimise objekt ka n2ha j22ks
		}
		//$_SESSION["bank_payment"]["url"] = $this->mk_my_orb("bank_return", array("id" => reset($bron_ids)));
		$ret = $bank_inst->do_payment(array(
			"bank_id" => $bank,
			"amount" => $total_sum,
			"reference_nr" => reset($bron_ids),
			"payment_id" => $bank_payment,
			"expl" => join(" ," , $bron_names).$expl_res,
			"cntr" => $bank_country,
			"lang" => $lang,
		));
	/*	if(aw_global_get("uid") == "struktuur"){arr($this->mk_my_orb("parse_alias", array("level" => 1, "preview" => 1, "id" => $arr["id"]))); die();}
	*/
		$this->mk_my_orb("parse_alias", array("level" => 1, "preview" => 1, "id" => $arr["id"]));

		//kuna siiani asi ei j6ua, siis makse kontrollis peaks vist sessiooni 2ra nullima... v6i ma ei tea
		return $ret;
		//return $section."?level=".$level;
	}

	/**
		@attrib name=bank_return nologin=1
		@param id required type=int acl=view
	**/
	function bank_return($arr)
	{
		$bank_inst = get_instance(CL_BANK_PAYMENT);
		unset($_SESSION["reservation"]["error"]);

		if(!(is_oid($arr["id"]) && $this->can("view" , $arr["id"])))
		{
			$bad_url = aw_ini_get("room_reservation.unsuccessful_bank_payment_url");
			return $bad_url;
		}
		$bron = obj($arr["id"]);
	//	if(aw_global_get("uid") == "strutkuur")arr($bron->meta("lang"));
		if($bron->meta("lang_id"))
		{
			$_SESSION["ct_lang_id"] = $bron->meta("lang_id");
			$_SESSION["ct_lang_lc"] = $bron->meta("lang_lc");
			$l = get_instance("languages");
			aw_global_set("ct_lang_lc", $_SESSION["ct_lang_lc"]);
			aw_global_set("ct_lang_id", $_SESSION["ct_lang_id"]);
		}
		$room = obj($bron->prop("resource"));
		$location = obj($room->prop("location"));
		$bank_payment = obj($location->prop("bank_payment"));
		if($bank_payment->prop("cancel_url"))
		{
			$bad_url = $bank_payment->prop("cancel_url");
		}
		$bad = 0;
// 		if($_SESSION["bank_return"]["data"]["action"] == "afb" && !$bank_inst->check_response())
// 		{
// 			$bad = 1;
// 		}
		if(!$bad)
		{
			if(!$this->make_verified($arr["id"]))
			{
				//print t("Broneeringut ei &otilde;nnestunud kinnitada");//miski suurem kala
				$bad = 1;
			}

		}
		if(!$bad)
		{
			if($bron->meta("tpl"))
			{
				$tpl = $bron->meta("tpl");
			}
			if(!$this->mail_sent_already)
			{
				$this->send_affirmation_mail($arr["id"],$tpl);
			}

			//no ilge idiootsus, kuid peab kindel olema et adminni lehele ei yrita minna
			$GLOBALS["cfg"]["in_admin"] = 0;
			$GLOBALS["cfg"]["__default"]["in_admin"] = 0;
//			header("Location:".$this->mk_my_orb("parse_alias", array("level" => 1, "preview" => 1, "id" => $arr["id"] , "tpl" => $tpl,)));
			header("Location:".
				str_replace("automatweb/orb.aw" , "" , $this->mk_my_orb(
					"parse_alias",
					array("level" => 1, "preview" => 1, "id" => $arr["id"] , "tpl" => $tpl,)
				))
			);
			die();
			return $this->mk_my_orb("parse_alias", array("level" => 1, "preview" => 1, "id" => $arr["id"] , "tpl" => $tpl,));
		}
		header("Location:".$bad_url);
		die();
		return $bad_url;
		//$this->mk_my_orb("show", array("id" => $order_id), "shop_order");
		//returni peaks miski ilusa saidi urli andma
	}

	private function get_other_addresses($o)
	{
		$set = $this->get_bron_room_settings($o);
		if(is_object($set) && $set->prop("order_mail_from"))
		{
			$groups = $set->prop("order_mail_groups");
			$gl = aw_global_get("gidlist_oid");
			if(sizeof(array_intersect($gl,$groups)))
			{
				return $set->prop("order_mail_to");
			}
		}
		return "";
	}

	private function _get_order_mail_sender_addr($o)
	{
		$set = $this->get_bron_room_settings($o);
		if(is_object($set) && $set->prop("order_mail_from"))
		{
			return $set->prop("order_mail_from");
		}
		return "automatweb@automatweb.com";
	}

	private function _get_order_mail_sender_name($o)
	{
		$set = $this->get_bron_room_settings($o);
		if(is_object($set) && $set->prop("order_mail_from_name"))
		{
			return $set->prop("order_mail_from_name");
		}
		return str_replace("http://", "", aw_ini_get("baseurl"));
	}

	private function _get_order_mail_subject($o)
	{
		$set = $this->get_bron_room_settings($o);
		if(is_object($set) && $set->prop("order_mail_subj"))
		{
			return $set->prop("order_mail_subj");
		}
		return sprintf(t("Broneering: %s"), $o->id());
	}

	private function get_bron_room_settings($o)
	{
		if(is_object($this->set))
		{
			return $this->set;
		}

		//j2rgnevalt maili saatmise v6imalus juhul kui on ruumi broneerimise objektist v2ljad t2idetud... et siis annaks nagu settingute objektiks hoopis broneerimise objekti, kuna propertyd on samad... kuid ei tea kui hea m6te see on... juhul kui mujal ka kasutama hakata neid settinguid kui maili saatmisel, siis tuleb teha 2 erinevat muutujat
		if(is_oid($o->meta("room_reservation_id")) && $this->can("view" , $o->meta("room_reservation_id")))
		{
			$res = obj($o->meta("room_reservation_id"));
			if($res->prop("order_mail_to"))
			{
				$this->set = $res;
				return $this->set;
			}
		}

		if ($this->can("view", $o->prop("resource")))
		{
			$res = obj($o->prop("resource"));
			$sets = $res->prop("settings");
			if (is_array($sets))
			{
				$sets = reset($sets);
			}
			if ($this->can("view", $sets))
			{
				$this->set = obj($sets);
			}
		}
		return $this->set;
	}

	/** Sends confirmation mail for a reservation
		@attrib api=1 params=pos

		@param id required type=oid
			The oid of the reservation

		@param tpl optional type=string
			The name of the template to use for formatting the email content

	**/
	function send_affirmation_mail($id,$tpl = null)
	{
		if(!is_oid($id))
		{
			return "";
		}
		$bron = obj($id);
		$_send_to = $bron->prop("customer.email.mail");
		if($others = $this->get_other_addresses($bron))
		{
			$_send_to.=",".$others;
		}
		$email_subj = $this->_get_order_mail_subject($bron);//sprintf(t("Broneering: %s"), $id);
		$mail_from_addr = $this->_get_order_mail_sender_addr($bron);//"automatweb@automatweb.com";
		$mail_from_name = $this->_get_order_mail_sender_name($bron);//str_replace("http://", "", aw_ini_get("baseurl"));
		if(!$tpl)
		{
			$tpl = "preview.tpl";
		}
		$this->read_site_template($tpl);
		lc_site_load("room_reservation", &$this);
		$this->vars($this->get_object_data($id));
		$this->vars(array("error" => ($_SESSION["reservation"]["error"]) ? $this->parse("error") : "",));
		$html =  $this->parse();
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
	}

	//see m2rgib ka makstuks, kui vaja seda funktsiooni ka muudmoodi kasutada, siis peaks miski muutuja juurde tekitama, mis maksmise asjadega ei tegeleks
	private function make_verified($id)
	{
		$bank_inst = get_instance(CL_BANK_PAYMENT);
		if(is_oid($id) && $this->can("view" , $id))
		{
			$room_inst = get_instance(CL_ROOM);
			$bron = obj($id);
			$bron->set_prop("verified" , 1);
			//broneeringusse maksest tuleva info, niipalju kui seda saab...siia seep2rast, et ei taha mitmes kohas acl disableda
			$bron->set_meta("payment_info" , $bank_inst->get_payment_info());
			// this was turned off in kalevspa
			//$bron->set_prop("paid" , 1);
			aw_disable_acl();
			if(!$bron->meta("mail_sent"))//topelt mailide v2ltimiseks
			{
				$bron->set_meta("mail_sent",1);
			}
			else
			{
				$this->mail_sent_already = 1;
			}
			$bron->save();
			aw_restore_acl();
			//juhuks kui m6ni enne maksmist magama j22nud, kuid siiski seda m6ne tunni p2rast teha kavatseb
			if(!$room_inst->check_if_available(array(
				"room" => $bron->prop("resource"),
				"start" => $bron->prop("start1"),
				"end" => $bron->prop("end"),
				"ignore_booking" => $bron->id(),
			)))
			{
				$_SESSION["reservation"]["error"].=  sprintf(t("%s - Sorry, but Your payment was made too late and another person has already booked this time!"), $bron->name())."<br>\n";
			}
			return 1;
		}
		else
		{
			return 0;
		}
	}

	/**
		@attrib name=revoke_reservation api=1 params=name nologin=1
		@param bron_id optional type=array
			reservation oid
		@param room required type=oid
			room oid
		@param room_res optional type=oid
			room reservation object id
		@param section optional
	**/
	function revoke_reservation($arr)
	{
		if($_SESSION["room_reservation"]["room_id"])
		{
			$arr["room"] =  $_SESSION["room_reservation"]["room_id"];
		}

		extract($arr);
		if(!$bron_id)
		{
			$bron_id = $_SESSION["room_reservation"][$arr["room"]]["bron_id"];
		}
		if(is_oid($bron_id))
		{
			$bron = obj($bron_id);
			$bron->delete();
		}
		$_SESSION["room_reservation"][$arr["room"]] = null;
		if(is_oid($room_res) || $this->can("view" , $room_res))
		{
			$ro = obj($room_res);
			if($ro->prop("revoke_url"))
			{
				$section = $ro->trans_get_val("revoke_url");
			}
		}
		return $section;
	}

	function callback_get_trans($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
	}

}
?>
