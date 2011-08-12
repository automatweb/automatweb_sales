<?php
// crm_bill_payment.aw - Laekumine
/*

@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@default table=objects

@tableinfo aw_crm_bill_payment index=aw_oid master_index=brother_of master_table=objects

@default group=general

@default table=aw_crm_bill_payment

@property date type=date_select table=aw_crm_bill_payment field=aw_date
@caption Kuup&auml;ev

@property payment_type type=chooser field=aw_payment_type
@caption Tasumisviis

@property sum type=textbox field=aw_sum
@caption Summa

@property currency type=relpicker reltype=RELTYPE_CURRENCY field=aw_currency
@caption Valuuta

@property customer type=relpicker reltype=RELTYPE_CUSTOMER field=aw_customer
@caption Klient

@property currency_rate type=textbox field=currency_rate field=aw_currency_rate
@caption Valuutakurss

@property bills type=table store=no
@caption Arved

@reltype CURRENCY value=1 clid=CL_CURRENCY
@caption valuuta

@reltype CUSTOMER value=2 clid=CL_CRM_COMPANY,CL_CRM_PERSON
@caption Kleint

- Kuup2ev
- Tasumisviis (Ylekandega, sularahas)
- Valuuta
- Valuutakurss
Arvete nimekiri, mis selle laekumisega on seotud


*/

class crm_bill_payment extends class_base
{
	function crm_bill_payment()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_bill_payment",
			"clid" => CL_CRM_BILL_PAYMENT
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "bills":
				$sum = 0;
				if($arr["obj_inst"]->id())
				{
					$prop["value"] = $this->_get_bills_table($arr);
					$url = $this->mk_my_orb("do_search", array(
						"pn" => "add_bill",
						"clid" => CL_CRM_BILL,
					), "popup_search", false, true);

					$prop["value"].= "<br>".html::href(array(
						"url" => "javascript:aw_popup_scroll(\"$url\",\"Otsing\",550,500)",
						"caption" => "<img src='".aw_ini_get("baseurl")."/automatweb/images/icons/search.gif' border=0>",
						"title" => t("Lisa Arve"),
					))."<br>";
				}

				break;
			case "payment_type":
				$prop["options"] = array(0 => t("&Uuml;lekandega"), 1 => t("Sularahas"));
				break;
			case "sum":
				$prop["type"] = "text";
				if(!$arr["new"])
				{
					$prop["value"] = number_format($arr["obj_inst"]->prop("sum") , 2);//number_format($arr["obj_inst"]->get_connected_bills_sum() , 2);
				}
				break;
		}

		return $retval;
	}

	function _get_bills_table($arr)
	{
		$t = new vcl_table;
		$t->define_field(array(
			"name" => "nr",
			"caption" => t("Arve nr"),
		));
		$t->define_field(array(
			"name" => "customer",
			"caption" => t("Klient"),
		));
		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa"),
		));
		$t->define_field(array(
			"name" => "curr",
			"caption" => t("Valuuta"),
		));

		$ol = new object_list(array(
			"class_id" => CL_CRM_BILL,
			"lang_id" => array(),
			"CL_CRM_BILL.RELTYPE_PAYMENT.id" => $arr["obj_inst"]->id(),
		));

		$bi = get_instance(CL_CRM_BILL);

		foreach($ol -> arr() as $o)
		{
		//	$this_sum = $bi->get_bill_sum($o);
		//	$free_sum = $arr["obj_inst"]->get_free_sum($o->id());
		//	$sum = $sum + $free_sum;
			$bill_sum = $arr["obj_inst"]->get_bill_sum($o->id());
			if(!$bill_sum)
			{
				$bill_sum = $bi->get_bill_needs_payment(array("bill" => $o->id()));//get_bill_recieved_money($o , $arr["obj_inst"]->id());//arr($bill_sum); arr($free_sum);
			}
			$t->define_data(array(
				"nr" => html::obj_change_url($o->id(),$o->prop("bill_no")),
				"sum" => html::textbox(array("name" => "bills[".$o->id()."][sum]", "value" => $bill_sum , "size" => 8)),
				"customer" => $o->prop("customer.name"),
				"curr" => $arr["obj_inst"]->get_currency_name(),
			));
//			$prop["value"] .= t("Arve nr:").html::obj_change_url($o->id(),$o->prop("bill_no")).", ".$o->prop("customer.name").",  ".
//			html::textbox(array("name" => "bills[".$o->id()."][sum]", "value" => $bill_sum , "size" => 8))
//			." ".$arr["obj_inst"]->get_currency_name()."<br>\n";

		}
		$t->define_data(array(
			"nr" => html::textbox(array("name" => "bills[new][no]" , "size" => 8)),
			"sum" => html::textbox(array("name" => "bills[new][sum]", "value" => 0 , "size" => 8)),
			"curr" => $arr["obj_inst"]->get_currency_name(),
		));

//		$prop["value"] .= t("Arve nr:").
//			html::textbox(array("name" => "bills[new][no]" , "size" => 8))." ".t("Summa").": ".
//			html::textbox(array("name" => "bills[new][sum]", "value" => 0 , "size" => 8))
//			." ".$arr["obj_inst"]->get_currency_name()."<br>\n";
		return $t->draw();
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "bills":
			{
				$bills = $arr["request"]["bills"];
				foreach($bills as $bill => $data)
				{
					if($bill == "new")
					{
						$bill_id = crm_bill_obj::get_bill_id(array(
							"no" => $data["no"],
						));
						if($bill_id)
						{
							$params = array(
								"o" => $bill_id,
								"sum" => $data["sum"]
							);
							$arr["obj_inst"]->add_bill($params);
						}
					}
					elseif($this->can("view" , $bill))
					{
						$arr["obj_inst"]->set_bill_sum(array(
							"bill" => $bill,
							"sum" => $data["sum"],
						));
					}

				}

//				$free_sum = $arr["obj_inst"]->get_free_sum();
				$pa = array();

				if(is_oid($arr["request"]["add_bill"]))
				{
					$pa[] = $arr["request"]["add_bill"];
				}
				else
				{
					$pa = explode(",",$arr["request"]["add_bill"]);
				}
				$error = array();
				foreach($pa as $p)
				{
					if(!is_oid($p))
					{
						continue;
					}
//					if($free_sum > 0)
//					{
						$err = $arr["obj_inst"]->add_bill(array("o" => $p));
						if($err)
						{
							$error[] = $err;
						}
//					}
//					else
//					{
//						$error[] = t("Ei j&auml;tku raha arvele id'ga ").$p;
//					}
				}
				if(sizeof($error))
				{
					$prop["error"] = join("<br>\n" , $error);
				}
				break;
			}
		}
		return $retval;
	}

	function _get_bill_payments_tb($arr)
	{
		$_SESSION["create_bill_ru"] = get_ru();
		$tb = $arr["prop"]["vcl_inst"];

		$tb->add_button(array(
			'name' => 'new',
			'img' => 'new.gif',
			'tooltip' => t('Lisa uus laekumine'),
			'url' => html::get_new_url(CL_CRM_BILL_PAYMENT, $arr["obj_inst"]->id(), array("return_url" => get_ru()))
		));
		$tb->add_button(array(
			'name' => 'del',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta valitud laekumised'),
			"confirm" => t("Oled kindel et soovid valitud laekumised kustutada?"),
			'action' => 'submit_delete_docs',
		));
// 		$tb->add_button(array(
// 			"name" => "search_bill",
// 			"img" => "search.gif",
// 			"tooltip" => t("Otsi"),
// 	//		"action" => "search_bill"
// 			"url" => "javascript:aw_popup_scroll('".$this->mk_my_orb("search_bill", array("openprintdialog" => 1,))."','Otsing',550,500)",
// 		));
	}

	function _init_bills_list_t($t, $r)
	{
		$t->define_field(array(
			"name" => "date",
			"caption" => t("Kuup&auml;ev"),
			"type" => "time",
			"format" => "d.m.Y",
			"numeric" => 1,
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "bills_list",
			"caption" => t("Arved"),
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "cust",
			"caption" => t("Klient"),
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "type",
			"caption" => t("Tasumisviis"),
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa"),
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "currency",
			"caption" => t("Valuuta"),
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "currency_rate",
			"caption" => t("Valuutakurss"),
			"sortable" => 1
		));
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));
	}

	function _get_bill_payments_cust(&$arr)
	{
		$arr["prop"]["value"] = $arr["request"]["bill_payments_cust"];
	}

	function _get_bill_payments_client_mgr(&$arr)
	{
		$v = $arr["request"]["bill_payments_client_mgr"];
		$arr["prop"]["value"] = html::textbox(array(
			"name" => "bill_payments_client_mgr",
			"value" => $v,
			"size" => 25
		))."<a href='javascript:void(0)' onClick='document.changeform.bill_s_client_mgr.value=\"\"' title=\"$tt\" alt=\"$tt\"><img title=\"$tt\" alt=\"$tt\" src='".aw_ini_get("baseurl")."/automatweb/images/icons/delete.gif' border=0></a>";
	}

	function _get_bill_payments_from(&$arr)
	{
		if(!$arr["request"]["bill_payments_from"])
		{
			$arr["request"]["bill_payments_from"] = (time() - 30*3600*24);
		}
		$arr["prop"]["value"] = $arr["request"]["bill_payments_from"];
	}

	function _get_bill_payments_to(&$arr)
	{
		$arr["prop"]["value"] = $arr["request"]["bill_payments_to"];
	}

	function _get_bill_payments_bill_no(&$arr)
	{
		$arr["prop"]["value"] = $arr["request"]["bill_payments_bill_no"];
	}

	function _get_bill_payments_bill_to(&$arr)
	{
		$arr["prop"]["value"] = $arr["request"]["bill_payments_bill_to"];
	}

	function _get_bill_payments_table($arr)
	{
		$filter = array(
			"class_id" => array(CL_CRM_BILL_PAYMENT)
		);
		$srch = $arr["request"];
		if($srch["bill_payments_search"])
		{
			if($srch["bill_payments_cust"] || $srch["bill_payments_bill_no"] || $srch["bill_payments_bill_to"] || $srch["bill_payments_client_mgr"])
			{
				$bfilter = array(
					"class_id" => CL_CRM_BILL,
				);
				if($srch["bill_payments_cust"])
				{
					$bfilter[] = new object_list_filter(array(
						"logic" => "OR",
						"conditions" => array(
							"CL_CRM_BILL.customer(CL_CRM_COMPANY).name" => "%".$srch["bill_payments_cust"]."%",
							"CL_CRM_BILL.customer(CL_CRM_PERSON).name" => "%".$srch["bill_payments_cust"]."%",
							"CL_CRM_BILL.customer_name" => "%".$srch["bill_payments_cust"]."%",
						)
					));
				}

				if($srch["bill_payments_client_mgr"])
				{

					$relist = new object_list(array(
						"class_id" => CL_CRM_COMPANY_ROLE_ENTRY,
						"CL_CRM_COMPANY_ROLE_ENTRY.person.name" => map("%%%s%%", explode(",", $srch["bill_payments_client_mgr"]))
					));

					$rs = array();
					foreach($relist->arr() as $o)
					{
						$rs = $o->prop("client");
					}

					$ft = new object_list_filter(array(
						"logic" => "OR",
						"conditions" => array(
							"CL_CRM_BILL.customer.client_manager.name" => map("%%%s%%", explode(",", $srch["bill_payments_client_mgr"])),
							"oid" => $rs,
						)
					));
					$bfilter[] = $ft;
				}
				if($srch["bill_payments_bill_no"] && $srch["bill_payments_bill_to"])
				{
					$bfilter["bill_no"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $arr["request"]["bill_payments_bill_no"] , $arr["request"]["bill_payments_bill_to"], "int");
				}
				else
				if($srch["bill_payments_bill_to"])
				{
					$bfilter["bill_no"] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $arr["request"]["bill_payments_bill_to"], "","int");
				}
				elseif($srch["bill_payments_bill_no"])
				{
					$bfilter["bill_no"] = $srch["bill_payments_bill_no"];
				}

				$bills = new object_list($bfilter);
				$ids = array();

				foreach($bills->arr() as $bill)
				{
					$conns_tmp = $bill->connections_from(array(
						"type" => "RELTYPE_PAYMENT",
					));
					foreach($conns_tmp as $conn)
					{
						$ids[$conn->prop('to')] = $conn->prop('to');
					}
				}
				if(sizeof($ids))
				{
					 $filter["oid"] = $ids;
				}
				else
				{
					$filter["oid"] = 1;
				}
			}

			$to = 9999999999;
			$from = 1;
			if($srch["bill_payments_from"])
			{
				$from = date_edit::get_timestamp($srch["bill_payments_from"]);
			}
			if($srch["bill_payments_to"])
			{
				$to = date_edit::get_day_end_timestamp($srch["bill_payments_to"]);
			}
			if($srch["bill_payments_from"] || $srch["bill_payments_to"])
			{
				$filter["date"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $from , $to);
			}
		}
		else
		{
			$filter["date"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, (time() - 30*3600*24));
		}
		$ol = new object_list($filter);

		$t = $arr["prop"]["vcl_inst"];
		$this->_init_bills_list_t($t, $arr["request"]);

		$t->set_caption(t("Laekumised"));

		$sum = 0;
		$inst = new crm_company_stats_impl();
		$curr = new currency();
		foreach($ol->arr() as $o)
		{
			$cust_name = $bill = $bills_list = "";
			$sum = $sum + $inst->convert_to_company_currency(array(
				"o" => $o,
				"sum" => $o->prop("sum"),
			));
			$bills = new object_list(array(
				"class_id" => CL_CRM_BILL,
				"lang_id" => array(),
				"CL_CRM_BILL.RELTYPE_PAYMENT.id" => $o->id(),
			));
			$bills_array = array();
			$bill = obj(reset($bills->ids()));
			if(is_oid($bill->id()))
			{
				$cust_name = html::get_change_url($bill->prop("customer"), array("return_url" => get_ru()),$bill->prop("customer.name")?$bill->prop("customer.name"):t("nimetu"));
			}
			foreach($bills->arr() as $b)
			{
				$bills_array[]= html::get_change_url($b->id(), array("return_url" => get_ru()),$b->name()?$b->name():t("nimetu"));

			}
			$bills_list = join(" ," ,$bills_array);

			$t->define_data(array(
				"name" => html::get_change_url($o->id(), array("return_url" => get_ru()),$o->name()?$o->name():t("nimetu")),
				"date" => $o->prop("date"),
				"oid" => $o->id(),
				"type" => $o->prop("type") ? t("Sularahas") : t("&Uuml;lekandega"),
				"currency" => $o->prop("currency.name"),
				"currency_rate" => $o->prop("currency_rate"),
				"sum" => $o->prop("sum"),//$o->get_connected_bills_sum(),//$o->prop("sum"),
				"cust" => $cust_name,
				"bills_list" => $bills_list,
			));
			$sum+= $cursum;
		}

		$t->set_default_sorder("desc");
		$t->set_default_sortby("date");
		$t->sort_by();
		$t->set_sortable(false);

//vajalik vaid siis kui mingi summa teema ka ikka tuleb, mis oleks loogiline
		$t->define_data(array(
			"sum" => "<b>".number_format($sum, 2)."</b>",
			"bill_no" => t("<b>Summa</b>"),
			"currency" => $curr->get_default_currency_name(),
		));
	}

	function callback_mod_reforb($arr)
	{
		$arr["add_bill"] = "";
		$arr["post_ru"] = post_ru();
	}

	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "" && $t === "aw_crm_bill_payment")
		{
			$this->db_query("CREATE TABLE aw_crm_bill_payment(aw_oid int primary key,
				aw_date int,
				aw_payment_type int,
				aw_sum double,
				aw_currency int,
				aw_customer int,
				aw_currency_rate double,

			)");
			return true;
		}
		switch($f)
		{
			case "aw_customer":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
		return false;
	}


}
