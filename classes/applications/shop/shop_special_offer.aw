<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/shop/shop_special_offer.aw,v 1.17 2008/07/22 06:58:51 tarvo Exp $
// shop_special_offer.aw - Poe eripakkumine 
/*

@classinfo syslog_type=ST_SHOP_SPECIAL_OFFER relationmgr=yes no_caption=1 no_status=1 maintainer=kristo

@default table=objects
@default group=general

@default field=meta
@default method=serialize

@property general_tb type=toolbar no_caption=1

@layout main_split type=hbox

@layout left_top type=vbox closeable=1 area_caption=&Uuml;ldseaded parent=main_split
	
	@property name type=textbox parent=left_top
	@caption Nimi

	@property comment type=textbox parent=left_top
	@caption Kommentaar

	@property valid_from_date type=date_select parent=left_top
	@caption Kehtib alates

	@property valid_until_date type=date_select parent=left_top
	@property Kehtib kuni

	@property weekdays type=checkbox multiple=1 parent=left_top
	@caption N&auml;dalap&auml;evad

	@property valid_from_time type=time_select parent=left_top
	@caption Kehtib alates kellaajast

	@property valid_until_time type=time_select parent=left_top
	@caption Kehtib kuni kellaajani

	@property transaction_start_date type=date_select parent=left_top
	@caption Tehing toimunud alates

	@property transaction_end_date type=date_select parent=left_top
	@caption Tehing toimunud kuni

	@property repeats type=checkbox parent=left_top
	@caption Kordub

	@property valid type=checkbox default=1 ch_value=1 parent=left_top
	@caption Kehtib

	@property discount_precentage type=textbox parent=left_top
	@caption Soodus-%

	@property priority type=textbox parent=left_top
	@caption Prioriteet

	@property valid_everywhere type=checkbox parent=left_top
	@caption Kehtib igal pool


@layout right_top type=vbox closeable=1 area_caption=S&uuml;steemsed&nbsp;seaded parent=main_split

	@property warehouses type=relpicker multiple=1 reltype=RELTYPE_WAREHOUSES parent=right_top
	@caption Laod

	@property order_centers type=relpicker multiple=1 reltype=RELTYPE_ORDER_CENTERS parent=right_top
	@caption Tellimiskeskkonnad

	@property brands type=relpicker multiple=1 reltype=RELTYPE_BRANDS parent=right_top
	@caption Br&auml;ndid

	@property user_groups type=relpicker multiple=1 reltype=RELTYPE_USER_GROUPS parent=right_top
	@caption Kasuajagrupid

	@property organizations_persons type=relpicker multiple=1 reltype=RELTYPE_ORGS_PERSONS parent=right_top
	@caption Organisatsioonid ja isikud

	@property client_groups type=relpicker multiple=1 reltype=RELTYPE_CLIENT_GROUPS parent=right_top
	@caption Kliendigrupid


@layout mid_box type=vbox closeable=1 area_caption=Skeemivalik

	@property discount_scheme type=select parent=mid_box
	@caption Skeem

	@property discount_scheme_tbl type=table store=no no_caption=1 parent=mid_box



@layout down_box type=vbox closeable=1 area_caption=Artiklikategooriate&nbsp;soodustused

	
	@property product_groups_tbl type=table store=no no_caption=1 parent=down_box




@groupinfo vis caption="N&auml;itamine"
@default group=vis

	@property template type=relpicker reltype=RELTYPE_ITEM_LAYOUT field=meta method=serialize automatic=1
	@caption Kujundusmall

	@property use_controller type=relpicker reltype=RELTYPE_CONTROLLER field=meta method=serialize 
	@caption Kasuta toodete n&auml;itamiseks kontrollerit



@groupinfo prods caption="Tooted"
@default group=prods

	@property products_tb type=toolbar store=no no_caption=1
	
	@property products_table type=table store=no no_caption=1




@reltype PRODUCT value=1 clid=CL_SHOP_PRODUCT,CL_SHOP_PACKET
@caption toode

@reltype ITEM_LAYOUT value=2 clid=CL_SHOP_PRODUCT_LAYOUT
@caption toote kujundusmall

@reltype CONTROLLER value=3 clid=CL_FORM_CONTROLLER
@caption kontroller

@reltype WAREHOUSES value=5 clid=CL_SHOP_WAREHOUSE
@caption Laod

@reltype ORDER_CENTERS value=6 clid=CL_SHOP_ORDER_CENTER
@caption Tellimiskeskkonnad

@reltype BRANDS value=7 clid=CL_SHOP_BRAND
@caption Br&auml;ndid

@reltype USER_GROUPS value=8 clid=CL_USER_GROUP
@caption Kasuajagrupid

@reltype ORGS_PERSONS value=9 clid=CL_CRM_COMPANY,CL_CRM_PERSON
@caption Organisatsioonid ja isikud

@reltype CLIENT_GROUPS value=10 clid=
@caption Kliendigrupid
*/


define('DISCOUNT_SCHEME_1', 1);
define('DISCOUNT_SCHEME_2', 2);
define('DISCOUNT_SCHEME_3', 3);
define('DISCOUNT_SCHEME_4', 4);

class shop_special_offer extends class_base
{
	function shop_special_offer()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_special_offer",
			"clid" => CL_SHOP_SPECIAL_OFFER
		));

		$this->discount_schemes = array(
			DISCOUNT_SCHEME_1 => t("Osta x, saad y"),
			DISCOUNT_SCHEME_2 => t("Soodustus ostusumma pealt"),
			DISCOUNT_SCHEME_3 => t("Soodustus ostes teatud toodet teatud koguses"),
			DISCOUNT_SCHEME_4 => t("Soodustus ostes teatud tootesarja/brandi/kategooria toodet teatud koguses"),
		);
		$this->discount_scheme_tbl_info = array(
			DISCOUNT_SCHEME_1 => array(
				"buy_product" => array(
					"type" => "textbox",
				       	"caption" => t("Ostad toote"),
					"obj_check" => true,
				),
				"get_product" => array(
					"type" => "textbox",
				       	"caption" => t("Saad toote"),
					"obj_check" => true,
				),
			),
			DISCOUNT_SCHEME_2 => array(
				"buy_price" => array(
					"type" => "textbox",
				       	"caption" => t("Ostad summa eest"),
					"isset_check" => true,
				),
				"get_discount" => array(
					"type" => "textbox",
					"caption" => t("Saad soodust"),
					"isset_check" => true,
				),
				"get_discount_type" => array(
					"type" => "select",
					"caption" => t("Allahindluse t&uuml;&uuml;p"),
					"options" => array(
						1 => t("%"),
						2 => t("kr"),
					),
				),
			),
			DISCOUNT_SCHEME_3 => array(
				"buy_product" => array(
					"type" => "textbox",
					"autocomplete_source_method" => "_get_ac_products",
					"option_is_tuple" => true,
					"autocomplete_params" => array(
						"buy_product"
					),
					"size" => 20,
					"caption" => t("Ostad toote"),
					"obj_check" => true,
				),
				"buy_unit" => array(
					"type" => "textbox",
					"autocomplete_source_method" => "_get_ac_product_units",
					"option_is_tuple" => true,
					"autocomplete_params" => array(
						"buy_product",
						"buy_unit",
					),
					"size" => 10,
					"caption" => t("Ostetav &uuml;hik"),
					"obj_check" => true,
				),
				"buy_amount" => array(
					"type" => "textbox",
					"size" => 10,
				       	"caption" => t("Ostetav kogus"),
				),
				"get_product" => array(
					"type" => "textbox",
					"autocomplete_source_method" => "_get_ac_products",
					"option_is_tuple" => true,
					"autocomplete_params" => array(
						"get_product"
					),
					"size" => 20,
					"caption" => t("Soodustoode"),
					"obj_check" => true,
				),
				"get_unit" => array(
					"type" => "textbox",
					"autocomplete_source_method" => "_get_ac_product_units",
					"option_is_tuple" => true,
					"autocomplete_params" => array(
						"get_product",
						"get_unit",
					),
					"size" => 10,
					"caption" => t("Soodus&uuml;hik"),
					"obj_check" => true,
				),
				"get_amount" => array(
					"type" => "textbox",
					"size" => 10,
					"caption" => t("Sooduskogus"),
				),

			),
			DISCOUNT_SCHEME_4 => array(
				"buy_cat" => array(
					"type" => "textbox",
					"autocomplete_source_method" => "_get_ac_cats",
					"option_is_tuple" => true,
					"autocomplete_params" => array(
						"buy_cat"
					),
					"size" => 20,
					"caption" => t("Ostetav kategooria/brand/..."),
					"obj_check" => true,
				),
				"buy_product" => array(
					"type" => "textbox",
					"autocomplete_source_method" => "_get_ac_products",
					"option_is_tuple" => true,
					"autocomplete_params" => array(
						"buy_cat",
						"buy_product",
					),
					"size" => 20,
					"caption" => t("Ostetav toode"),
					"obj_check" => true,
				),
				"buy_amount" => array(
					"type" => "textbox",
					"size" => 5,
					"caption" => t("Ostetav kogus"),
				),
				"get_cat" => array(
					"type" => "textbox",
					"autocomplete_source_method" => "_get_ac_cats",
					"option_is_tuple" => true,
					"autocomplete_params" => array(
						"get_cat"
					),
					"size" => 20,
					"caption" => t("Sooduskategooria, -brand..."),
					"obj_check" => true,
				),
				"get_product" => array(
					"type" => "textbox",
					"autocomplete_source_method" => "_get_ac_products",
					"option_is_tuple" => true,
					"autocomplete_params" => array(
						"get_cat",
						"get_product",
					),
					"size" => 20,
					"caption" => t("Soodustoode"),
					"obj_check" => true,
				),
				"get_amount" => array(
					"type" => "textbox",
					"size" => 5,
					"caption" => t("Sooduskogus"),
				),

			),
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "products_table":
				$this->_get_products_table($arr);
				break;
			case "discount_scheme_tbl":
				$this->_get_discount_scheme_tbl($arr);
				break;
			case "discount_scheme":
				$prop["options"] = $this->discount_schemes;
				break;
			case "products_tb":
				$this->_get_products_tb($arr);
				break;
			case "product_groups_tbl":
				$this->_get_product_groups_tbl($arr);
				break;
			case "general_tb":
				$arr["prop"]["vcl_inst"]->add_save_button();
				$arr["prop"]["vcl_inst"]->add_delete_rels_button();
				$arr["prop"]["vcl_inst"]->add_cdata(html::hidden(array(
					"name" => "rurl",
					"value" => post_ru(),
				)));
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
			case "products_table":
				$this->_add_new_product_to_request($arr);
				$this->_reset_product_rels($arr["obj_inst"], $arr["request"]["prodat"]);
				$arr["obj_inst"]->set_meta("prodat", $arr["request"]["prodat"]);
				break;
			case "product_groups_tbl":
				$this->_add_new_product_group_to_request($arr);
				$arr["obj_inst"]->set_meta("pro_gr_dat", $arr["request"]["pro_gr_dat"]);
				break;
			case "discount_scheme_tbl":
				break;
			case "discount_scheme":
				if($arr["obj_inst"]->prop("discount_scheme") != $arr["request"]["discount_scheme"])
				{
					$arr["obj_inst"]->set_meta("discount_scheme_data", array());
				}
				else
				{
					$this->_add_new_discount_scheme_row_to_request($arr);
					$arr["obj_inst"]->set_meta("discount_scheme_data", $arr["request"]["discount_scheme_data"]);
				}
				break;
		}
		return $retval;
	}	

	function _init_discount_scheme_tbl(&$arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$ds = $arr["obj_inst"]->prop("discount_scheme");
		$t->define_chooser(array(
			"name" => "discount_scheme_sel",
			"field" => "discount_scheme_id",
		));
		foreach($this->discount_scheme_tbl_info[$ds] as $elem => $data)
		{
			$t->define_field(array(
				"name" => $elem,
				"caption" => $data["caption"],
			));
		}
	}

	function _gen_discount_scheme_tbl(&$arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$ds = $arr["obj_inst"]->prop("discount_scheme");
		$ds_data = array_reverse($arr["obj_inst"]->meta("discount_scheme_data"));
		foreach($ds_data as $row_id => $row)
		{
			$altered_row = $this->_gen_discount_scheme_tbl_fields($ds, $row, false, $row_id);
			$altered_row["discount_scheme_id"] = ++$row_id;
			$t->define_data($altered_row);
		}
	}

	function _gen_discount_scheme_tbl_field_elem($type, $name, $data, $value)
	{
		switch($type)
		{
			case "textbox":
				$params = $data + array(
					"name" => $name,
					"value" => $value,
				);
				if($params["option_is_tuple"])
				{
					$params["selected"] = array(
						$value => $data["content"],
					);
					unset($params["value"]);
					unset($params["content"]);
				}
				$value = html::textbox($params);
				break;
			case "select":
				$value = html::select(array(
					"name" => $name,
					"options" => $data["options"],
					"selected" => $value,
				));
				break;
		}
		return $value;
	}

	function _add_new_discount_scheme_row_to_request(&$arr)
	{
		$ds = $arr["obj_inst"]->prop("discount_scheme");
		$errorstring = false;
		foreach($arr["request"]["new_discount_scheme"] as $k => $v)
		{
			if($this->discount_scheme_tbl_info[$ds][$k]["obj_check"] && !$this->can("view", $v))
			{
				$errorstring = sprintf(t("Palun valida v&auml;lja '%s' korrektne objekt."), $this->discount_scheme_tbl_info[$ds][$k]["caption"]);
			}
			if($this->discount_scheme_tbl_info[$ds][$k]["isset_check"] && !strlen($v))
			{
				$errorstring = sprintf(t("Palun t&auml;ita v&auml;i: '%s'"), $this->discount_scheme_tbl_info[$ds][$k]["caption"]);
			}
		}
		if(!$errorstring)
		{
			if(!is_array($arr["request"]["discount_scheme_data"]))
			{
				$arr["request"]["discount_scheme_data"] = array();
			}
			array_push($arr["request"]["discount_scheme_data"], $arr["request"]["new_discount_scheme"]);
		}
		else
		{
			//somesort of errormanagment
		}
	}

	function _gen_discount_scheme_tbl_fields($scheme, $val, $new = false, $rowid = false)
	{
		$inf = $this->discount_scheme_tbl_info[$scheme];
		foreach($val as $name => $value)
		{
			$form_name = $new?"new_discount_scheme[".$name."]":"discount_scheme_data[".$rowid."][".$name."]";
			foreach($inf[$name]["autocomplete_params"] as $k => $param)
			{
				$inf[$name]["autocomplete_params"][$k] = $new?"new_discount_scheme[".$param."]":"discount_scheme_data[".$param."]";
			}
			// i'm not gonna do the $value this->can(view) check here because this is time-consuming and every element with option_is_tuple should validate as an object !!!
			if($inf[$name]["option_is_tuple"])
			{
				$o = obj($value);
				$inf[$name]["content"] = $o->name();
			}
			$row[$name] = $this->_gen_discount_scheme_tbl_field_elem($inf[$name]["type"], $form_name, $inf[$name], $value);
		}
		return $row;
	}

	function _get_discount_scheme_tbl(&$arr)
	{
		$this->_init_discount_scheme_tbl($arr);
		$ds = $arr["obj_inst"]->prop("discount_scheme");
		$ds_data = $arr["obj_inst"]->meta("discount_scheme_data");
		$empty_row = array_combine(array_keys($this->discount_scheme_tbl_info[$ds]), array_fill(0,count($this->discount_scheme_tbl_info[$ds]), ""));
		$empty_row = $this->_gen_discount_scheme_tbl_fields($ds, $empty_row, true);
		$arr["prop"]["vcl_inst"]->define_data($empty_row);

		$this->_gen_discount_scheme_tbl($arr);
	}

	function _init_products_table(&$arr)
	{
		$t =& $arr["prop"]["vcl_inst"];

		$t->define_chooser(array(
			"name" => "products_sel",
			"field" => "products_id",
		));
		$t->define_field(array(
			"name" => "product",
			"caption" => t("Nimi")
		));

		foreach($this->_get_currency() as $cur)
		{
			$cur_obj = obj($cur);
			$t->define_field(array(
				"name" => "currency_".$cur,
				"caption" => $cur_obj->name(),
				"align" => "center"
			));
		}

		$t->define_field(array(
			"name" => "discount",
			"caption" => t("Soodus %"),
			"align" => "center"
		));
	}

	function _gen_products_table(&$arr)
	{
		$prodat = array_reverse($arr["obj_inst"]->meta("prodat"));
		foreach($prodat as $k => $dat)
		{
			$oid = $dat["product"];
			$dat["product"] = html::obj_change_url(obj($dat["product"]));
			$dat["product"] .=  html::hidden(array(
				"name" => "prodat[".$k."][product]",
				"value" => $oid,
			));
			$dat["discount"] = html::textbox(array(
				"name" => "prodat[".$k."][discount]",
				"value" => $dat["discount"],
				"size" => 10,
			));
			foreach($dat["currency"] as $ck => $v)
			{
				$dat["currency_".$ck] = html::textbox(array(
					"name" => "prodat[".$k."][currency][".$ck."]",
				       	"value"	=> $v,
					"size" => 10,
				));
			}

			$dat["products_id"] = $k+1;
			$arr["prop"]["vcl_inst"]->define_data($dat);
		}
	}

	function _add_new_product_to_request(&$arr)
	{
		if(strlen($arr["request"]["new_product"]["product"]))
		{
			$whs = $arr["obj_inst"]->prop("warehouses");
			$fnd = false;
			foreach($whs as $wh)
			{
				if($this->can("view", $wh))
				{
					$fnd = true;
					break;
				}
			}
			if(!$fnd)
			{
				return false;
			}
			$wh = obj($wh);
			$fld = $wh->prop("conf.prod_fld");
			if($this->can("view", $arr["request"]["new_product"]["product"]))
			{
				$prod = obj($arr["request"]["new_product"]["product"]);
			}
			else
			{
				$prod = obj();
				$prod->set_name($arr["request"]["new_product"]["product"]);
				$prod->set_class_id(CL_SHOP_PRODUCT);
				$prod->set_parent($fld);
				$prod->save();
			}
			$arr["request"]["new_product"]["product"] = $prod->id();

			if(!is_array($arr["request"]["prodat"]))
			{
				$arr["request"]["prodat"] = array();
			}
			$prc = $this->_get_product_prices($arr["request"]["new_product"]["product"]);
			
			foreach($arr["request"]["new_product"]["currency"] as $oid => $val)
			{
				if(empty($val))
				{
					$arr["request"]["new_product"]["currency"][$oid] = $prc[$oid];
				}
			}
			array_push($arr["request"]["prodat"], $arr["request"]["new_product"]);
		}
	}

	function _get_products_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_products_table($arr);

		// empty line
		$empty_line = array(
			"product" => html::textbox(array(
				"name" => "new_product[product]",
				"size" => 30,
				"autocomplete_source_method" => "_get_ac_products",
				"autocomplete_params" => array(
					"new_product[product]",
				),
				"option_is_tuple" => true,
			)),
			"discount" => html::textbox(array(
				"name" => "new_product[discount]",
				"size" => 10,
			)),
		);
		// for remove_rels action
		$empty_line["product"] .= html::hidden(array(
			"name" => "rurl",
			"value" => post_ru(),
		));
		foreach($this->_get_currency() as $cur)
		{
			$c_obj = obj($cur);
			$empty_line["currency_".$cur] = html::textbox(array(
				"name" => "new_product[currency][".$cur."]",
				"size" => 10,
			));
		}
		$t->define_data($empty_line);
		
		// fill table 
		$this->_gen_products_table($arr);
	}



	function _get_products_tb(&$arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_save_button();
		$tb->add_delete_rels_button();
	}

	function _init_product_groups_tbl(&$arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$t->define_chooser(array(
			"name" => "product_group_sel",
			"field" => "product_group_id",
		));
		$t->define_field(array(
			"name" => "product_group",
			"caption" => t("Artikligrupp"),
		));
		$t->define_field(array(
			"name" => "discount",
			"caption" => t("Soodustus"),
		));
	}

	function _add_new_product_group_to_request(&$arr)
	{
		if($this->can("view", $arr["request"]["new_product_group"]["product_group"]))
		{
			if(!is_array($arr["request"]["pro_gr_dat"]))
			{
				$arr["request"]["pro_gr_dat"] = array();
			}
			array_push($arr["request"]["pro_gr_dat"], $arr["request"]["new_product_group"]);
		}
	}

	function _get_product_groups_tbl(&$arr)
	{
		$this->_init_product_groups_tbl($arr);
		$t =& $arr["prop"]["vcl_inst"];
		$empty_row = array(
			"product_group" => html::textbox(array(
				"name" => "new_product_group[product_group]",
				"size" => 30,
				"autocomplete_source_method" => "_get_ac_product_groups",
				"autocomplete_params" => array(
					"new_product_group[product_group]"
				),
				"option_is_tuple" => true,
			)),
			"discount" => html::textbox(array(
				"name" => "new_product_group[discount]",
				"size" => 10,
			)),
		);
		$t->define_data($empty_row);

		$data = array_reverse($arr["obj_inst"]->meta("pro_gr_dat"));
		foreach($data as $k => $v)
		{
			$_t = $v["product_group"];
			$v["product_group"] = html::obj_change_url($v["product_group"]);
			$v["product_group"] .= html::hidden(array(
				"name" => "pro_gr_dat[".$k."][product_group]",
				"value" => $_t,
			));
			$v["discount"] = html::textbox(array(
				"name" => "pro_gr_dat[".$k."][discount]",
				"value" => $v["discount"],
			));
			$v["product_group_id"] = ++$k;
			$t->define_data($v);
		}


	}

	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	////
	// !shows the special offer
	function show($arr)
	{
		$ob = new object($arr["id"]);

		error::raise_if(!$ob->prop("template"), array(
			"id" => "ERR_NO_LAYOUT",
			"msg" => t("shop_special_offer::show(): no layout set for product display in special offer!")
		));

		$layout = obj($ob->prop("template"));
		$prodat = $ob->meta("prodat");
		foreach($prodat as $dat)
		{
			$n_prodat[$dat["product"]] = $dat;
		}
		$prodat = $n_prodat;

		$html = "";
		$plut = array();
		foreach($ob->connections_from(array("type" => "RELTYPE_PRODUCT")) as $c)
		{
			$plut[$c->prop("to")] = $prodat[$c->prop("to")]["product"];
		}

		asort($plut);

		if ($ob->prop("use_controller"))
		{
			$param = array(
				"layout" => $layout,
				"prod" => $prod,
				"prodat" => $prodat,
				"plut" => $plut,
			);

			$param["special_offer"] = $ob;
			$fg = get_instance(CL_FORM_CONTROLLER);
			$html = $fg->eval_controller($ob->prop("use_controller"), $param);
		}
		else
		{
			$l_inst = $layout->instance();
			$l_inst->read_template($layout->prop("template"));
			foreach($plut as $pid => $tmp)
			{
				$prod = obj($pid);
				$prod_i = $prod->instance();

				$param = array(
					"layout" => $layout,
					"prod" => $prod,
					"prodat" => $prodat, // deprecated?
					"plut" => $plut, // what for?
					"l_inst" => $l_inst,
					"shop_special_offer_price" => $prodat[$pid]["currency"],
					"shop_special_offer_discount" => $prodat[$pid]["currency"],
				);

				$html .= $prod_i->do_draw_product($param);
			}
		}

		return $html;
	}


	/**
		@attrib name=_get_ac_product_groups params=name all_args=1
	**/
	function _get_ac_product_groups($args)
	{
		classload("protocols/data/json");
		$json = new json();
		$error = false;
		$errorstring = "";
		$opts = array();
		$return = array(
			"error" => &$error,
			"errorstring" => &$errorstring,
			"options" => &$opts,
		);
		if($this->can("view", $args["id"]))
		{
			$o = obj($args["id"]);
			$whs = safe_array($o->prop("warehouses"));
			$flds = array();
			foreach($whs as $wh)
			{
				if($this->can("view", $wh))
				{
					$wh_o = obj($wh);
					$flds[] = $wh_o->prop("conf.prod_type_fld");
				}
			}
			// fucking sick stuff
			$ol = new object_list();
			foreach($flds as $fld)
			{
				$ot = new object_tree(array(
					"class_id" => array(
						CL_SHOP_PRODUCT_CATEGORY,
						CL_MENU,
					),
					"parent" => $fld,
				));
				$ol2 = $ot->to_list();
				$ol->add($ol2);
			}
			foreach($ol->arr() as $k => $obj)
			{
				if($obj->class_id() == CL_SHOP_PRODUCT_CATEGORY)
				{
					$opts[$k] = $obj->name();
				}
			}
		}
		else
		{
			$error = true;
			$errorstring = t("Teadmata viga!");
		}
		return die($json->encode($return));

	}

	/**
		@attrib name=_get_ac_cats params=name all_args=1
	**/
	function _get_ac_cats($args)
	{
		classload("protocols/data/json");
		$json = new json();
		$error = false;
		$units = array();

		$o = obj($args["id"]);
		$bds = $o->prop("brands");
		foreach(array($bds) as $brand)
		{
			$b_obj = obj($brand);
			$brands[$brand] = $b_obj->name();
		}

		$whs = $o->prop("warehouses");
		foreach($whs as $wh)
		{
			if($this->can("view", $wh))
			{
				$wh = obj($wh);
				$fldrs[] = $wh->prop("conf.prod_type_fld");
			}
		}
		$ol = new object_list(array(
			"class_id" => CL_SHOP_PRODUCT_CATEGORY,
			"parent" => $fldrs,
		));
		foreach($ol->arr() as $oid => $obj)
		{
			$cats[$oid] = $obj->name();
		}
		//$opts = array_merge($brands, $cats);
		$opts = $brands + $cats;
		$return = array(
			"error" => $error,
			"errorstring" => $errorstring,
			"options" => $opts,
		);
		return die($json->encode($return));
	}

	/**
		@attrib name=_get_ac_products params=name all_args=1
	 **/
	function _get_ac_products($args)
	{
		classload("protocols/data/json");
		$json = new json();
		$error = false;
		$errorstring = "";
		$opts = array();
		$return = array(
			"error" => $error,
			"errorstring" => $errorstring,
		);
		if($this->can("view", $args["id"]))
		{
			$o = obj($args["id"]);
			$whs = safe_array($o->prop("warehouses"));
			$flds = array();
			foreach($whs as $wh)
			{
				if($this->can("view", $wh))
				{
					$wh_o = obj($wh);
					$flds[] = $wh_o->prop("conf.prod_fld");
				}
			}
			// fucking sick stuff
			$ol = new object_list();
			foreach($flds as $fld)
			{
				$ot = new object_tree(array(
					"class_id" => array(
						CL_SHOP_PRODUCT,
						CL_MENU,
					),
					"parent" => $fld,
				));
				$ol2 = $ot->to_list();
				$ol->add($ol2);
			}
			foreach($ol->arr() as $k => $obj)
			{
				if($obj->class_id() == CL_SHOP_PRODUCT)
				{
					$opts[$k] = $obj->name();
				}
			}
		}
		else
		{
			$error = true;
			$errorstring = t("Teadmata viga!");
		}
		$return["options"] = $opts;
		return die($json->encode($return));
	}

	/**
		@attrib name=_get_ac_product_units params=name all_args=1
	**/
	function _get_ac_product_units($args)
	{
		classload("protocols/data/json");
		$json = new json();
		$error = false;
		$units = array();
		preg_match("/(.*)\[(.*)\].*/", $args["requester"], $matches, PREG_OFFSET_CAPTURE);
		$elem_name = $matches[1][0];
		$requester = $matches[2][0];


		$product = (substr($requester, 0, 4) == "buy_")?$args[$elem_name]["buy_product"]:$args[$elem_name]["get_product"];
		if($this->can("view", $product))
		{
			$prod_obj = obj($product);
			$prod = get_instance(CL_SHOP_PRODUCT);
			$prod_units = $prod->get_units($prod_obj);
			$ui = get_instance(CL_UNIT);
			$unitnames = $ui->get_unit_list(true);
			foreach($prod_units as $unit_id)
			{
				$units[$unit_id] = $unitnames[$unit_id];
			}
		}
		else
		{
			$error = true;
			$errorstring = t("Valige toode");
		}
		$return = array(
			"error" => $error,
			"errorstring" => $errorstring,
			"options" => $units,
		);
		return die($json->encode($return));
	}


	private function _get_currency()
	{
		$ol = new object_list(array(
			"class_id" => CL_CURRENCY,
			"site_id" => array(),
			"lang_id" => array(),
		));
		return $ol->ids();
	}

	private function _get_product_prices($prod)
	{
		$prod = obj($prod);
		return $prod->meta("cur_prices");
	}

	private function _reset_product_rels(&$obj_inst, $data = false)
	{
		// we also have to connect dha prouct to special offer
		// this don't make duplicate connections!!
		foreach($obj_inst->connections_from(array("type" => "RELTYPE_PRODUCT")) as $conn)
		{
			$conn->delete();
		}
		$data = $data?$data:$obj_inst->meta("prodat");
		foreach($data as $dat)
		{
			$obj_inst->connect(array(
				"type" => "RELTYPE_PRODUCT",
				"to" => $dat["product"],
			));
		}
	}

	/**
		@attrib name=delete_rels params=name all_args=1
	**/
	function delete_rels($arr)
	{
		if($this->can("view", $arr["id"]))
		{
			$o = obj($arr["id"]);
			$array = array(
				"products_sel" => "prodat",
				"product_group_sel" => "pro_gr_dat",
				"discount_scheme_sel" => "discount_scheme_data",
			);
			foreach($array as $sel => $meta)
			{
				if(is_array($arr[$sel]))
				{
					$d = $o->meta($meta);
					foreach(safe_array($arr[$sel]) as $selected)
					{
						unset($d[--$selected]);
					}
					$o->set_meta($meta, $d);
					if($sel == "products_sel")
					{
						$this->_reset_product_rels(&$o, false);
					}
					$o->save();
				}
			}
		}
		return $arr["rurl"];
	}
}
?>
