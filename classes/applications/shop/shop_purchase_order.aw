<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_SHOP_PURCHASE_ORDER relationmgr=yes no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_shop_purcahse_orders master_index=brother_of master_table=objects index=aw_oid

@default table=aw_shop_purcahse_orders
@default group=general

@property gen_tb type=toolbar no_caption=1 store=no

@property name type=textbox table=objects field=name
@caption Nimi

@property number type=textbox field=aw_number
@caption Number

@property comment type=textarea rows=5 cols=40 table=objects field=comment
@caption Kommentaar

@property purchaser type=relpicker reltype=RELTYPE_PURCHASER field=aw_purchaser
@caption Tarnija

@property job type=relpicker reltype=RELTYPE_JOB field=aw_job
@caption T&ouml;&ouml;

@property related_orders type=relpicker multiple=1 reltype=RELTYPE_SELL_ORDER store=connect
@caption Seotud m&uuml;&uuml;gitellimused			

@property date type=date_select field=aw_date
@caption Kuup&auml;ev

@property deal_date type=date_select field=aw_deal_date
@caption Tegelemise kuup&auml;ev

@property planned_date type=date_select field=aw_planned_arrival_date
@caption Planeeritud saabumise kuup&auml;ev

@property purchaser_rep type=relpicker reltype=RELTYPE_PURCHASER_REP field=aw_purchaser_rep
@caption Tarnija esindaja

@property our_rep type=relpicker reltype=RELTYPE_OUR_REP field=aw_our_rep
@caption Meie esindaja

@property trans_cost type=textbox field=aw_trans_cost datatype=int
@caption Transpordikulu

@property customs_cost type=textbox field=aw_customs_cost datatype=int
@caption Tollikulu

@property transp_type type=relpicker field=aw_transp_type reltype=RELTYPE_TRANSFER_METHOD
@caption L&auml;hetusviis

@property currency type=relpicker reltype=RELTYPE_CURRENCY automatic=1 field=aw_currency
@caption Valuuta

@property warehouse type=relpicker reltype=RELTYPE_WAREHOUSE automatic=1 field=aw_warehouse
@caption Ladu

@property order_status type=chooser default=0 field=aw_status
@caption Staatus

@property taxed type=chooser field=aw_taxed
@caption Maks

@property art_toolbar type=toolbar no_caption=1 store=no

@property articles type=table store=no no_caption=1

@reltype PURCHASER value=1 clid=CL_CRM_COMPANY
@caption Hankija

@reltype PURCHASER_REP value=2 clid=CL_CRM_PERSON
@caption Hankija esindaja

@reltype SELL_ORDER value=3 clid=CL_SHOP_SELL_ORDER
@caption M&uuml;&uuml;gitellimus

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

@reltype JOB value=10 clid=CL_MRP_CASE
@caption T&ouml;&ouml;

@reltype COMMENT value=11 clid=CL_COMMENT
@caption Kommentaar
*/

define("ORDER_STATUS_CANCELLED", -1);
define("ORDER_STATUS_INPROGRESS", 1);
define("ORDER_STATUS_SENT", 2);
define("ORDER_STATUS_CONFIRMED", 3);
define("ORDER_STATUS_CLOSED", 4);
define("ORDER_STATUS_WORKING", 5);

class shop_purchase_order extends class_base
{
	const AW_CLID = 1430;

	function shop_purchase_order()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_purchase_order",
			"clid" => CL_SHOP_PURCHASE_ORDER
		));

		$this->states = array(
			ORDER_STATUS_CANCELLED => t("Katkestatud"),
			ORDER_STATUS_INPROGRESS => t("Koostamisel"),
			ORDER_STATUS_SENT => t("Saadetud"),
			ORDER_STATUS_CONFIRMED => t("Kinnitatud"),
			ORDER_STATUS_CLOSED => t("T&auml;idetud"),
			ORDER_STATUS_WORKING => t("T&ouml;&ouml;tlemisel"),
		);
	}

	function _get_order_status($arr)
	{
		$arr["prop"]["options"] = $this->states;
		if(!$arr["prop"]["value"])
		{
			$arr["prop"]["value"] = ORDER_STATUS_INPROGRESS;
		}
	}

	function callback_mod_reforb($arr, $request)
	{
		$arr["post_ru"] = post_ru();
		$arr["add_rows"] = $request["add_rows"];
		if($request["action"] == "new")
		{
			return;
		}
		$arr["add_art"] = 0;
		if($arr["id"])
		{
			$conn = obj($arr["id"])->connections_from(array(
				"type" => "RELTYPE_ROW",
			));
			foreach($conn as $c)
			{
				$o = $c->to();
				$arr["rows"][$o->id()]["tax_rate"] = $o->prop("tax_rate");
			}
		}
	}

	function callback_post_save($arr)
	{
		if(($add = $arr["request"]["add_rows"]) && $arr["request"]["group"] != "articles")
		{
			$this->_add_extra_rows($add, $arr["obj_inst"]);
		}
	}

	function _add_extra_rows($add, $obj)
	{
		$rows = explode(";", $add);
		foreach($rows as $row)
		{
			$data = explode(",", $row);
			$o = obj();
			$o->set_class_id(CL_SHOP_ORDER_ROW);
			$o->set_parent($obj->id());
			$o->set_prop("prod", $data[0]);
			$o->set_prop("unit", $data[2]);
			$o->set_prop("amount", $data[1]);
			$o->save();
			$obj->connect(array(
				"to" => $o,
				"type" => "RELTYPE_ROW",
			));
		}
	}

	function _get_gen_tb($arr)
	{
		$tb = &$arr["prop"]["vcl_inst"];
		if($this->can("view", $arr["obj_inst"]->prop("warehouse")))
		{
			$cfgid = obj($arr["obj_inst"]->prop("warehouse"))->prop("conf");
			if($this->can("view", $cfgid))
			{
				$cfg = obj($cfgid);
			}
		}
		if(!$cfg)
		{
			$ol = new object_list(array(
				"class_id" => CL_SHOP_WAREHOUSE_CONFIG,
				"lang_id" => array(),
			));
			$cfg = $ol->begin();
		}
		$ml_type = $cfg->prop("purchase_order_mail");
		$cfgi = get_instance(CL_SHOP_WAREHOUSE_CONFIG);
		if($ml_type == SEND_AW_MAIL)
		{
			$mail_url = get_instance(CL_MESSAGE)->mk_my_orb("new", array("parent" => $arr["obj_inst"]->id(), "return_url" => get_ru()));
		}
		else
		{
			try
			{
				get_instance(CL_CFG_VIEW_CONTROLLER)->check_property(&$mail_url, $cfg->prop("purchase_order_mail_ctrl"), $arr);
			}
			catch(\Exception $e)
			{
			}
		}
		$tb->add_button(array(
			"img" => "mail_send.gif",
			"url" => $mail_url,
			"name" => "send_mail",
		));
	}

	function _get_taxed($arr)
	{
		return PROP_IGNORE;
		$arr["prop"]["options"] = array(0 => "K&auml;ibemaksuta", 1 => "K&auml;ibemaksuga");
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_shop_purcahse_orders(aw_oid int primary key, aw_number varchar(255), aw_purchaser int, related_sales_orders int, aw_date int, aw_planned_arrival_date int, aw_purchaser_rep int, aw_our_rep int, aw_trans_cost double, aw_transp_type varchar(255), aw_currency int, aw_warehouse int, aw_taxed int)");
			return true;
		}
		switch($f)
		{
			case "aw_customs_cost":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "double"
				));
				return true;
				break;
			case "aw_job":
			case "aw_deal_date":
			case "aw_status":
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

	function _get_art_toolbar(&$arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_search_button(array(
			"pn" => "add_art",
			"multiple" => 1,
			"clid" => CL_SHOP_PRODUCT,
		));
		$tb->add_save_button();
		$tb->add_delete_button();
	}

	function _set_articles(&$arr)
	{
		$tmp = $arr["request"]["add_art"];
		if($tmp)
		{
			$arts = explode(",", $tmp);
			foreach($arts as $art)
			{
				$this->add_art_row($art, $arr);
			}
		}
		$rows = $arr["request"]["rows"];
		if(is_array($rows))
		{
			foreach($rows as $id => $row)
			{
				$ro = null;
				if(isset($row["prodname"]))
				{
					$n = $row["prodname"];
					$c = $row["prodcode"];
					$prodid = null;
					if($this->can("view", $n))
					{
						$prodid = $n;
					}
					elseif($this->can("view", $c))
					{
						$prodid = $c;
					}
					if($prodid)
					{
						$ro = $this->add_art_row($prodid, $arr);
					}
					else
					{
						continue;
					}
				}
				else
				{
					$ro = obj($id);
				}
				foreach($row as $var => $val)
				{
					if($ro->is_property($var))
					{
						$ro->set_prop($var, $val);
					}
				}
				$ro->save();
			}
		}
	}

	private function add_art_row($art, $arr)
	{
		$o = obj();
		$o->set_class_id(CL_SHOP_ORDER_ROW);
		$o->set_parent($arr["obj_inst"]->id());
		$o->set_name(sprintf(t("%s rida"), $arr["obj_inst"]->name()));
		$o->set_prop("prod", $art);
		$o->save();
		$arr["obj_inst"]->connect(array(
			"to" => $o->id(),
			"type" => "RELTYPE_ROW",
		));
		$arr["obj_inst"]->connect(array(
			"to" => $art,
			"type" => "RELTYPE_PRODUCT",
		));
		return $o;
	}

	function _get_articles(&$arr)
	{
		if($arr["new"])
		{
			return PROP_IGNORE;
		}
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_articles_tbl($t);
		$conn = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_ROW",
		));
		$units = get_instance(CL_UNIT)->get_unit_list(true);
		$data["units"] = $units;
		for($i = 0; $i < 10; $i++)
		{
			$t->define_data($this->get_art_row_data($data, $i));
		}
		if($ar = $arr["request"]["add_rows"])
		{
			$rows = explode(";", $ar);
			foreach($rows as $row)
			{
				$data = explode(",", $row);
				$t->define_data($this->get_art_row_data(array(
					"product" => $data[0],
					"unit" => $data[2],
					"amount" => $data[1],
					"units" => $units,
				), $i++));
			}
		}
		foreach($conn as $c)
		{
			$o = $c->to();
			$url = $this->mk_my_orb("do_search", array(
				"pn" => "rows[".$o->id()."][tax_rate]",
				"clid" => array(
					CL_CRM_TAX_RATE
				),
				"multiple" => 0,
			), "popup_search");
			$url = "javascript:aw_popup_scroll('".$url."','".t("Otsi")."',550,500)";
			$tax = html::href(array(
				"caption" => html::img(array(
					"url" => "images/icons/search.gif",
					"border" => 0
				)),
				"url" => $url
			))." ".$o->prop("tax_rate.name");
			$sum = $o->prop("amount") * $o->prop("price");
			$taxsum = $sum + number_format($sum * $o->prop("tax_rate.tax_amt") / 100, 2);
			$data["o"] = $o;
			$data["tax"] = $tax;
			$data["taxsum"] = $taxsum;
			$data["sum"] = $sum;
			$t->define_data($this->get_art_row_data($data, $o->id()));
		}
		$t->set_rgroupby(array("add"=>"add"));
		$t->set_default_sortby("add");
		$t->set_default_sorder("asc");
	}

	private function get_art_row_data($data, $id)
	{
		extract($data);
		if($o)
		{
			$data["oid"] = $o->id();
			$data["name"] = $this->can("view", $o->prop("prod"))?html::obj_change_url(obj($o->prop("prod")), parse_obj_name($o->prop("prod.name"))): ($o->prod_name != "" ? $o->prod_name : '');
			$data["code"] = $o->prop("prod.code");
		}
		else
		{
			if($product && $this->can("view", $product))
			{
				$po = obj($product);
				$data["add"] = t("Lisa read");
				$code_val = array($product => $po->prop("code"));
				$name_val = array($product => $po->name());
			}
			else
			{
				$data["add"] = t("Lisa uus");
			}
			$url = $this->mk_my_orb("do_search", array(
				"pn" => "rows[".$id."][prodname]",
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
			$data["name"] = html::textbox(array(
				"name" => "rows[".$id."][prodname]",
				"size" => 10,
				"value" => $name_val,
			)).$s;
			$data["add_num"] = $id;
		}
		$data["amount"] = html::textbox(array(
			"name" => "rows[".$id."][amount]",
			"value" => $o?$o->prop("amount"):($amount ? $amount : ''),
			"size" => 3,
		));
		$data["required"] = html::textbox(array(
			"name" => "rows[".$id."][required]",
			"value" => $o?$o->prop("required"):'',
			"size" => 3,
		));
		$data["unit"] = html::select(array(
			"name" => "rows[".$id."][unit]",
			"options" => $units,
			"value" => $o?$o->prop("unit"):($unit ? $unit : ''),
		));
		$data["unit_price"] = html::textbox(array(
			"name" => "rows[".$id."][price]",
			"value" => $o?$o->prop("price"):'',
			"size" => 3,
		));
		$data["sum"] = $sum;
		$data["taxsum"] = $taxsum;
		$data["tax_rate"] = $tax;
		$data["comment"] = html::textbox(array(
			"name" => "rows[".$id."][comment]",
			"value" => $o?$o->prop("comment"):'',
			"size" => 10,
		));
		$data["purchaser_art_code"] = html::textbox(array(
			"name" => "rows[".$id."][other_code]",
			"value" => $o?$o->prop("other_code"):'',
			"size" => 5,
		));
		$data["gotten_amt"] = html::textbox(array(
			"name" => "rows[".$id."][real_amount]",
			"value" => $o?$o->prop("real_amount"):'',
			"size" => 3,
		));
		return $data;
	}

	private function _init_articles_tbl($t)
	{
		$t->define_field(array(
			"caption" => t("Artikkel"),
			"align" => "center",
			"name" => "name",
		));
		$t->define_field(array(
			"caption" => t("Kood"),
			"align" => "center",
			"name" => "code",
		));
		$t->define_field(array(
			"caption" => t("Vajadus"),
			"align" => "center",
			"name" => "required",
		));
		$t->define_field(array(
			"caption" => t("Tellitud kogus"),
			"align" => "center",
			"name" => "amount",
		));
		$t->define_field(array(
			"caption" => t("Saadud kogus"),
			"align" => "center",
			"name" => "gotten_amt",
		));
		$t->define_field(array(
			"caption" => t("&Uuml;hik"),
			"align" => "center",
			"name" => "unit",
		));
		$t->define_field(array(
			"caption" => t("&Uuml;hiku hind"),
			"align" => "center",
			"name" => "unit_price",
		));
		$t->define_field(array(
			"caption" => t("Hankija artiklikood"),
			"align" => "center",
			"name" => "purchaser_art_code",
		));
		$t->define_field(array(
			"caption" => t("Maksum&auml;&auml;r"),
			"align" => "center",
			"name" => "tax_rate",
		));
		$t->define_field(array(
			"caption" => t("Kommentaar"),
			"align" => "center",
			"name" => "comment",
		));
		$t->define_field(array(
			"caption" => t("Summa"),
			"align" => "center",
			"name" => "sum",
		));
		$t->define_field(array(
			"caption" => t("Summa KMga"),
			"align" => "center",
			"name" => "taxsum",
		));
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel",
		));
	}

	function _set_related_orders($arr)
	{
		if($arr["prop"]["value"] != $arr["obj_inst"]->prop($arr["prop"]["name"]))
		{
			foreach($arr["prop"]["value"] as $oid)
			{
				$o = obj($oid);
				$o_val = $o->prop($arr["prop"]["name"]);
				$o_val[$arr["obj_inst"]->id()] = $arr["obj_inst"]->id();
				$o->set_prop($arr["prop"]["name"], $o_val);
				$o->save();
			}
		}
		return PROP_OK;
	}
}

?>
