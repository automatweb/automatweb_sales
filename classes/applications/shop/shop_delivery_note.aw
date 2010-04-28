<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_SHOP_DELIVERY_NOTE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=robert
@tableinfo aw_shop_delivery_note master_index=brother_of master_table=objects index=aw_oid

@default table=aw_shop_delivery_note
@groupinfo general caption=&Uuml;ldine
@default group=general

	@property number type=textbox
	@caption Number

	@property enter_date type=date_select
	@caption Sisestamise kuup&auml;ev

	@property delivery_date type=date_select
	@caption Kohaletoimetamise kuup&auml;ev

	@property from_warehouse type=relpicker reltype=RELTYPE_FROM_WAREHOUSE
	@caption Laost
	
	@property to_warehouse type=relpicker reltype=RELTYPE_TO_WAREHOUSE
	@caption Lattu

	@property customer type=relpicker reltype=RELTYPE_CUST
	@caption Klient

	@property impl type=relpicker reltype=RELTYPE_IMPL
	@caption Tarnija

	@property transport type=textbox
	@caption Transpordikulu

	@property customs type=textbox
	@caption Tollikulu

	@property currency type=relpicker reltype=RELTYPE_CURRENCY
	@caption Valuuta

	@property writeoff type=checkbox ch_value=1
	@caption Mahakandmine

	@property state type=select field=aw_state
	@caption Staatus

	@property approved type=hidden
	@caption Kinnitatud

	@property articles_tb store=no no_caption=1 type=toolbar

	@property articles_tbl store=no no_caption=1 type=table

@groupinfo bills caption=Arved
@default group=bills

	@property bills_tb store=no no_caption=1 type=toolbar

	@property bills_tbl store=no no_caption=1 type=table

@groupinfo articles caption=Artiklid
@default group=articles

@reltype FROM_WAREHOUSE value=1 clid=CL_SHOP_WAREHOUSE
@caption Algladu

@reltype TO_WAREHOUSE value=2 clid=CL_SHOP_WAREHOUSE
@caption Sihtladu

@reltype CURRENCY value=3 clid=CL_CURRENCY
@caption Valuuta

@reltype BILL value=4 clid=CL_CRM_BILL
@caption Arve

@reltype ROW value=5 clid=CL_SHOP_DELIVERY_NOTE_ROW
@caption Rida

@reltype CUST value=6 clid=CL_CRM_COMPANY,CL_CRM_PERSON
@caption Klient

@reltype IMPL value=7 clid=CL_CRM_COMPANY,CL_CRM_PERSON
@caption Teostaja

@reltype PRODUCT value=8 clid=CL_SHOP_PRODUCT
@caption Artikkel
*/

class shop_delivery_note extends class_base
{
	const AW_CLID = 1459;

	function shop_delivery_note()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_delivery_note",
			"clid" => CL_SHOP_DELIVERY_NOTE
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "approved":
				if($arr["new"])
				{
					return PROP_IGNORE;
				}
				break;

			case "writeoff":
				if($v = $arr["request"][$prop["name"]])
				{
					$prop["value"] = $v;
				}
		}

		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "approved":
				if($prop["value"] && !($arr["obj_inst"]->prop("approved")))
				{
					$this->callback_post_save($arr);
					$ret = $arr["obj_inst"]->create_movement($arr);
					if(!$ret)
					{
						$err = aw_global_get("dn_err");
						$prop["error"] = implode("<br />", $err);
						$retval = PROP_FATAL_ERROR;
						aw_session_del("dn_err");
					}
				}
				break;
		}

		return $retval;
	}

	function callback_post_save($arr)
	{
		$add = $arr["request"]["add"];
		if(is_array($add))
		{
			foreach($add as $row)
			{
				$prodid = null;
				if($id = $row["name"])
				{
					if(!$this->can("view", $id))
					{
						$ol = new object_list(array(
							"class_id" => CL_SHOP_PRODUCT,
							"site_id" => array(),
							"lang_id" => array(),
							"name" => $id,
						));
						$o = $ol->begin();
						if($o)
						{
							$prodid = $o->id();
						}
					}
					else
					{
						$prodid = $id;
					}
				}
				if($id = $row["code"])
				{
					if(!$this->can("view", $id))
					{
						$ol = new object_list(array(
							"class_id" => CL_SHOP_PRODUCT,
							"site_id" => array(),
							"lang_id" => array(),
							"code" => $id,
						));
						$o = $ol->begin();
						if($o)
						{
							$prodid = $o->id();
						}
					}
					else
					{
						$prodid = $id;
					}
				}
				if($prodid)
				{
					$po = obj($prodid);
					$o = obj();
					$o->set_class_id(CL_SHOP_DELIVERY_NOTE_ROW);
					$o->set_name(sprintf(t("%s rida"), $arr["obj_inst"]->name()));
					$o->set_parent($arr["obj_inst"]->id());
					$o->set_prop("product", $prodid);
					$o->set_prop("warehouse", $row["warehouse"]);
					$o->set_prop("price", $row["price"]);
					if($u = $row["unit"])
					{
						$units = $po->instance()->get_units($po);
						if(array_search($u, $units) !== false)
						{
							$o->set_prop("unit", $u);
						}
						else
						{
							aw_session_set("dn_err", sprintf(t("Toote %s &uuml;hikuks ei saa m&auml;&auml;rata %s"), $po->name(), obj($u)->name()));
						}
					}
					if(($n = $row["serial_no"]) && $po->prop("serial_number_based"))
					{
						$o->set_prop("serial_no", $n);
					}
					if(($n = $row["set_no"]) && $po->prop("order_based"))
					{
						$o->set_prop("set_no", $n);
					}
					$o->set_prop("amount", $row["amount"]);
					$o->save();
					$arr["obj_inst"]->connect(array(
						"type" => "RELTYPE_ROW",
						"to" => $o->id(),
					));
					$arr["obj_inst"]->connect(array(
						"type" => "RELTYPE_PRODUCT",
						"to" => $prodid,
					));
				}
			}
		}
		foreach($arr["request"]["rows"] as $rowid => $data)
		{
			$ro = obj($rowid);
			$ro->set_prop("serial_no", $data["serial_no"]);
			$ro->set_prop("set_no", $data["set_no"]);
			$ro->set_prop("warehouse", $data["warehouse"]);
			$ro->set_prop("unit", $data["unit"]);
			$ro->set_prop("amount", $data["amount"]);
			$ro->set_prop("price", $data["price"]);
			$ro->save();
		}
	}


	function _get_articles_tb($arr)
	{
		if($arr["new"])
		{
			return ;
		}
		$tb = &$arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"img" => "delete.gif",
			"tooltip" => t("Eemalda valitud artiklid"),
			"name" => "delete_article_rels",
			"action" => "del_article_rels",
		));

		$tb->add_menu_button(array(
			"name" => "bills",
			"text" => t("Arve"),
			"tooltip" => t("Kanna valitud read arvele"),
		));
		$tb->add_menu_item(array(
			"parent" => "bills",
			"text" => t("Uus arve"),
			"link" => "javascript:void(0)",
			"onClick" => "cf = document.forms.changeform; cf.elements.action.value = 'create_bill'; cf.submit();",
		));
	}

	function _init_articles_tbl($t)
	{
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
		));
		$t->define_field(array(
			"caption" => t("Nimetus"),
			"align"=> "center",
			"name" => "name",
		));
		$t->define_field(array(
			"caption" => t("Artiklikood"),
			"align"=> "center",
			"name" => "code",
		));
		$t->define_field(array(
			"caption" => t("Seerianumber"),
			"align"=> "center",
			"name" => "serial_no",
		));
		$t->define_field(array(
			"caption" => t("Partiinumber"),
			"align"=> "center",
			"name" => "set_no",
		));
		$t->define_field(array(
			"caption" => t("Ladu"),
			"align"=> "center",
			"name" => "warehouse",
		));
		$t->define_field(array(
			"caption" => t("Hind"),
			"align"=> "center",
			"name" => "price",
		));
		$t->define_field(array(
			"caption" => t("Kogus"),
			"align"=> "center",
			"name" => "amount",
		));
		$t->define_field(array(
			"caption" => t("&Uuml;hik"),
			"align"=> "center",
			"name" => "unit",
		));
		$t->define_field(array(
			"caption" => t("Summa"),
			"align"=> "center",
			"name" => "sum",
		));
		$t->define_field(array(
			"caption" => t("Omahinna summa"),
			"align"=> "center",
			"name" => "ourprice_sum",
		));
		$t->define_field(array(
			"caption" => t("Ostuhind"),
			"align"=> "center",
			"name" => "purchase_price",
		));
		$t->define_field(array(
			"caption" => t("P&otilde;hihind"),
			"align"=> "center",
			"name" => "base_price",
		));
		$t->define_field(array(
			"caption" => t("P&otilde;hihind KM-ga"),
			"align"=> "center",
			"name" => "base_price_tax",
		));
		$t->define_field(array(
			"caption" => t("KM kood"),
			"align"=> "center",
			"name" => "km_code",
		));
		$t->define_field(array(
			"caption" => t("Ribakood"),
			"align"=> "center",
			"name" => "barcode",
		));
		$t->define_field(array(
			"caption" => t("Kirjeldus"),
			"align"=> "center",
			"name" => "description",
		));
	}

	function _get_articles_tbl(&$arr)
	{
		if($err = aw_global_get("dn_err"))
		{
			$arr["prop"]["error"] = $err;
			aw_session_del("dn_err");
		}
		$t = &$arr["prop"]["vcl_inst"];
		$this->_init_articles_tbl(&$t);
		if(!$arr["new"])
		{
			$data = $arr["obj_inst"]->meta("articles");
			$conn = $arr["obj_inst"]->connections_from(array(
				"type" => "RELTYPE_ROW",
			));
			if($to = $arr["obj_inst"]->prop("to_warehouse"))
			{
				$warehouse = $to;
			}
			if($this->can("view",$warehouse))
			{
				$who = obj($warehouse);
				$calc_type = $who->prop("status_calc_type");
			}
			$pi = get_instance(CL_SHOP_PRODUCT);
			foreach($conn as $c)
			{
				$row = $c->to();
				$amounts[$row->id()] = ($a = $row->prop("amount"))?$a:$arr["obj_inst"]->get_wh_amount($row, $arr["obj_inst"]);
				$totalprices[$row->id()] = $row->prop("price")*$amounts[$row->id()];
			}
			$total = 0;
			foreach($totalprices as $id=>$price)
			{
				$total += $price;
			}
			$other_prices = $arr["obj_inst"]->prop("customs") + $arr["obj_inst"]->prop("transport");
		}
		$warehouse_list = $arr["obj_inst"]->_get_warehouse_chooser();
		$pi = get_instance(CL_SHOP_PRODUCT);
		$ui = get_instance(CL_UNIT);
		$unitnames = $ui->get_unit_list(true);
		$data = array(
			"warehouse" => $warehouse,
			"amounts" => $amounts,
			"pi" => $pi,
			"total" => $total,
			"other_prices" => $other_prices,
			"calc_type" => $calc_type,
			"warehouse_list" => $warehouse_list,
			"unit_list" => $unitnames,
			"totalprices" => $totalprices,
			"other_prices" => $other_prices,
			"total" => $total,
		);
		if($arr["request"]["singles"] && $arr["new"])
		{
			$data["singles"] = array();
			foreach($arr["request"]["singles"] as $oid)
			{
				$data["singles"][] = $oid;
			}
			$t->set_default_sortby("rid");
			$count += count($arr["request"]["singles"]);
			$data["amts"] = $arr["request"]["amounts"];
		}
		for($i = 0; $i < 10+$count; $i++)
		{
			$t->define_data($this->get_art_row($i, $data, $arr["obj_inst"]));
		}
		if(!$arr["new"])
		{
			foreach($conn as $c)
			{
				$row = $c->to();
				$rd = $this->get_art_row($row, $data, $arr["obj_inst"]);
				if($rd)
				{
					$t->define_data($rd);
				}
			}
		}
		$t->set_numeric_field("rid");
		$t->set_rgroupby(array("add"=>"add"));
	}

	function get_art_row($row = null, $arr = array(), $obj)
	{
		unset($vals);
		extract($arr);
		if(gettype($row) == "object")
		{
			$id = $row->prop("product");
			if(!$this->can("view", $id))
			{
				return false;
			}
			$prod = obj($id);
			$bprice = $pi->calc_price($prod);
			$amount = $row->prop("amount");
			$price = $row->prop("price");
			if($other_prices > 0 && $amount > 0)
			{
				$ourprice_sum = (round((($price*$amount/$total) * $other_prices)/$amount + $price, 2))*$amount;
			}
			else
			{
				$ourprice_sum = (($p = $price)?$p:0)*$amount;
			}
			$prod_unit_list = $pi->get_units($prod);
			$vals["unit_list"] = array();
			foreach($prod_unit_list as $i=>$unit)
			{
				if($this->can("view", $unit))
				{
					$uo = obj($unit);
					$vals["unit_list"][$unit] = $uo->prop("unit_code") ? $uo->prop("unit_code") : $uo->name();
				}
			}
			$data = array(
				"oid" => $row->id(),
				"code" => $prod->prop("code"),
				"name" => html::obj_change_url($prod, $prod->name()),
				"barcode" => $prod->prop("barcode"),
				"description" => $prod->comment(),
				"serial_no" => $prod->prop("serial_number_based")?html::textbox(array(
					"name" => "rows[".$row->id()."][serial_no]",
					"size" => 5,
					"value" => $row->prop("serial_no"),
					"autocomplete_source" => $this->mk_my_orb("articles_serial_no_autocomplete_source"),
					"autocomplete_params" => array("rows[".$row->id()."][prodid2]"),
				)).html::hidden(array(
					"name" => "rows[".$row->id()."][prodid2]",
					"value" => $id,
				)):'',
				"set_no" => $prod->prop("order_based")?html::textbox(array(
					"name" => "rows[".$row->id()."][set_no]",
					"size" => 5,
					"value" => $row->prop("set_no"),
					"autocomplete_source" => $this->mk_my_orb("articles_set_no_autocomplete_source"),
					"autocomplete_params" => array("rows[".$row->id()."][prodid]"),
				)).html::hidden(array(
					"name" => "rows[".$row->id()."][prodid]",
					"value" => $id,
				)):'',
				"purchase_price" => ($calc_type == 2) ? $pi->get_fifo_price($prod, $obj->prop("from_warehouse")) : $pi->get_last_purchase_price($prod),
				"base_price" => $bprice,
				"base_price_tax" => round($bprice*1.18, 2),
				"ourprice_sum" => $ourprice_sum,
				"sum" => $totalprices[$row->id()],
				"add" => t("<strong>Saatelehe read</strong>"),
			);
			$name = "rows[".$row->id()."]";
			$vals["wh"] = ($wh = $row->prop("warehouse")) ? $wh : $warehouse;
			if(!$price)
			{
				$mv_ol = new object_list(array(
					"class_id" => CL_SHOP_WAREHOUSE_MOVEMENT,
					"site_id" => array(),
					"lang_id" => array(),
					"product" => $id,
					"limit" => 1,
					"sort_by" => "created desc",
				));
				$mv_o = $mv_ol->begin();
				if($mv_o)
				{
					$price = $mv_o->prop("price");
				}
			}
			$vals["price"] = $price;
			$vals["unit"] = $row->prop("unit");
			$vals["amount"] = $amounts[$row->id()];
		}
		else
		{
			$name = "add[".$row."]";
			if($oid = $singles[$row])
			{
				$so = obj($oid);
				if($so->class_id() == CL_SHOP_PRODUCT)
				{
					$prodid = $oid;
					$po = $so;
				}
				else
				{
					$prodid = $so->prop("product");
					$st = $so->prop("type");
					$po = obj($prodid);
					$sc = $so->prop("code");
					if($st == 2)
					{
						$serial_no = $sc;
					}
					else
					{
						$set_no = $sc;
					}
				}
				$n = $po->prop("name");
				$c = $po->prop("code");
				$units = $pi->get_units($po);
				foreach($units as $i=>$unit)
				{
					if($this->can("view", $unit))
					{
						$uo = obj($unit);
						$vals["unit_list"][$unit] = $uo->name();
					}
				}
				$vals["unit"] = $units[0];
				$vals["amount"] = $arr["amts"][$oid];
			}
			$url = $this->mk_my_orb("do_search", array(
				"pn" => $name."[name]",
				"clid" => array(
					CL_SHOP_PRODUCT
				),
				"tbl_props" => array("oid", "name", "code", "parent"),
				"multiple" => 0,
				"no_submit" => 1,
			), "shop_product_popup_search");
			$url = "javascript:aw_popup_scroll('".$url."','".t("Otsi")."',600,500)";
			$s = html::href(array(
				"caption" => html::img(array(
					"url" => "images/icons/search.gif",
					"border" => 0
				)),
				"url" => $url
			));
			$data = array(
				"name" => "<div style=\"width: 130px;\">".html::textbox(array(
						"name" => $name."[name]",
						"size" => 10,
					)).$s."</div>",
				"add" => t("<strong>Lisa uus</strong>"),
				"serial_no" => html::textbox(array(
					"name" => $name."[serial_no]",
					"size" => 5,
					"value" => $serial_no,
				)),
				"set_no" => html::textbox(array(
					"name" => $name."[set_no]",
					"size" => 5,
					"value" => $set_no,
				)),
				"rid" => $row,
			);
		}
		$data["warehouse"] = html::select(array(
			"name" => $name."[warehouse]",
			"value" => $vals["wh"]?$vals["wh"]:$warehouse,
			"options" => $warehouse_list,
		));
		$data["price"] = html::textbox(array(
			"name" => $name."[price]",
			"size" => 4,
			"value" => $vals["price"],
		));
		$data["unit"] = html::select(array(
			"name" => $name."[unit]",
			"value" => $vals["unit"],
			"options" => $vals["unit_list"]?$vals["unit_list"]:$unit_list,
		));
		$data["amount"] = html::textbox(array(
			"name" => $name."[amount]",
			"size" => 3,
			"value" => $vals["amount"],
		));
		return $data;
	}

	function _get_bills_tb($arr)
	{
		$tb = &$arr["prop"]["vcl_inst"];
		$tb->add_new_button(array(CL_CRM_BILL), $arr["obj_inst"]->id(), 4);
		$tb->add_search_button(array(
			"pn" => "add_bill",
			"clid" => CL_CRM_BILL,
			"multiple" => 1,
		));
		$tb->add_delete_rels_button();
	}

	function _get_bills_tbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel",
		));
		$t->define_field(array(
			"name" => "number",
			"caption" => t("Number"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "date",
			"caption" => t("Kuup&auml;ev"),
			"align" => "center",
		));
		$conn = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_BILL",
		));
		foreach($conn as $c)
		{
			$bill = $c->to();
			$t->define_data(array(
				"number" => html::obj_change_url($c->to(), ($no = $bill->prop("bill_no"))?$no:t("(Puudub)")),
				"date" => date('d.m.Y', $bill->prop("bill_date")),
				"oid" => $bill->id(),
			));
		}
	}

	function _set_bills_tb($arr)
	{
		if($add = $arr["request"]["add_bill"])
		{
			$tmp = explode(",", $add);
			foreach($tmp as $bill)
			{
				$arr["obj_inst"]->connect(array(
					"to" => $bill,
					"type" => "RELTYPE_BILL",
				));
			}
		}
	}

	

	/**
	@attrib name=del_article_rels all_args=1
	**/
	function del_article_rels($arr)
	{
		$o = obj($arr["id"]);
		foreach($arr["sel"] as $oid)
		{
			$ro = obj($oid);
			$prod = $ro->prop("product");
			$ro->delete();
		}
		$del = true;
		foreach($o->connections_from(array("type" => "RELTYPE_ROW")) as $c)
		{
			$ro = $c->to();
			$chp = $ro->prop("product");
			if($chp == $prod)
			{
				$del = false;
			}
		}
		if($del)
		{
			$o->disconnect(array(
				"from" => $prod,
				"type" => "RELTYPE_PRODUCT",
			));
		}
		return $arr["post_ru"];
	}

	/**
	@attrib name=articles_set_no_autocomplete_source all_args=1
	**/
	function articles_set_no_autocomplete_source($arr)
	{
		$ac = get_instance("vcl/autocomplete");
		$arr = $ac->get_ac_params($arr);
		$prodid = substr($_SERVER['QUERY_STRING'], strpos($_SERVER['QUERY_STRING'], "]=")+2);
		$ol = new object_list(array(
			"class_id" => CL_SHOP_PRODUCT_SINGLE,
			"product" => $prodid,
			"lang_id" => array(),
			"site_id" => array(),
			"limit" => 1000,
			"type" => 1,
		));
		$res = array();
		foreach($ol->arr() as $o)
		{
			$res[$o->id()] = $o->prop("code");
		}
		return $ac->finish_ac($res);
	}

	/**
	@attrib name=articles_serial_no_autocomplete_source all_args=1
	**/
	function articles_serial_no_autocomplete_source($arr)
	{
		$ac = get_instance("vcl/autocomplete");
		$arr = $ac->get_ac_params($arr);
		$prodid = substr($_SERVER['QUERY_STRING'], strpos($_SERVER['QUERY_STRING'], "]=")+2);
		$ol = new object_list(array(
			"class_id" => CL_SHOP_PRODUCT_SINGLE,
			"product" => $prodid,
			"lang_id" => array(),
			"site_id" => array(),
			"limit" => 1000,
			"type" => 2,
		));
		$res = array();
		$pi = get_instance(CL_SHOP_PRODUCT);
		foreach($ol->arr() as $oid => $o)
		{
			$amt = $pi->get_amount(array(
				"single" => $oid,
			));
			$cont = true;
			if($amt)
			{
				foreach($amt->arr() as $ao)
				{
					if($ao->prop("amount") != 0)
					{
						$cont = false;
					}
				}
			}
			if($cont)
			{
				continue;
			}
			$res[$o->id()] = $o->prop("code");
		}
		return $ac->finish_ac($res);
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
		$arr["add_bill"] = 0;
	}

	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		$this->vars($ob->properties());
		$this->vars(array("currency" => $ob->prop("currency.name")));
		$this->vars(array("customer" => $ob->prop("curstomer.name")));
		return $this->parse();
	}

	function request_execute($obj)
	{
		$arx = array();
		$arx["id"] = $obj->id();
		return $this->show($arx);
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_shop_delivery_note(aw_oid int primary key)");
			return true;
		}
		switch($f)
		{
			case "number":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "varchar(255)"
				));
				return true;
				break;
			case "transport":
			case "customs":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "double"
				));
				return true;
				break;

			case "delivery_date":
			case "enter_date":
			case "from_warehouse":
			case "to_warehouse":
			case "currency":
			case "approved":
			case "customer":
			case "impl":
			case "writeoff":
			case "aw_state":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int",
				));
				return true;
				break;
		}
		return false;
	}

	/**
	@attrib name=create_movement all_args=1
	**/
	function create_movement($arr)
	{
		$o = obj($arr["id"]);
		if(!$o->prop("approved"))
		{
			$ret = $o->create_movement($arr);
		}
		if(!$ret)
		{
			$err = aw_global_get("dn_err");
			aw_session_del("dn_err");
			die($err);
		}
		else
		{
			$o->set_prop("approved", 1);
			$o->save();
		}
		die();
	}

	function _get_state($arr)
	{
		$arr["prop"]["options"] = array(
			0 => t("Koostamisel"),
			1 => t("L&otilde;petatud"),
			2 => t("Koostatud"),
			3 => t("Katkestatud")
		);
	}

	/**
	@attrib name=create_bill all_args=1
	**/
	function create_bill($arr)
	{
		$o = obj($arr["id"]);
		$o->create_bill($arr);
		return $arr["post_ru"];
	}
}

?>
