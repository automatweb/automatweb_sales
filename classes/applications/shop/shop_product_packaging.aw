<?php
/*
@classinfo relationmgr=yes prop_cb=1
@tableinfo aw_shop_packaging index=id master_table=objects master_index=brother_of
@extends applications/shop/shop_warehouse_item

@default table=objects

@default table=aw_shop_packaging

@groupinfo general_general parent=general caption=&Uuml;ldine
@default group=general_general

	@property name type=textbox table=objects
	@caption Nimi

	@property status_edit type=chooser table=objects field=status
	@caption Staatus

	@property comment type=textbox table=objects
	@caption Kommentaar

	@property jrk type=textbox size=5 table=objects field=jrk
	@caption J&auml;rjekord

	@property product type=hidden field=aw_product
	@caption Toode

	@property price type=textbox size=5 field=aw_price
	@caption Hind

	@property special_price type=textbox size=5 field=aw_special_price
	@caption Erihind

	@property price_object type=relpicker reltype=RELTYPE_PRICE
	@caption Hind (objekt)

	@property special_price_object type=relpicker reltype=RELTYPE_PRICE
	@caption Erihind (objekt)

	@property content_package_price_condition type=checkbox ch_value=1 disabled=1 table=aw_shop_packaging field=aw_content_package_price_condition
	@caption Sisupaketi hinnatingimuse pakendiobjekt

	@property price_cur type=table store=no
	@caption Hinnad valuutades

@groupinfo general_time_settings caption="Ajaseaded" parent=general
@default group=general_time_settings

	@layout gentms_main type=hbox

		@layout reservation type=hbox area_caption=Broneeritav&nbsp;aeg closeable=1 parent=gentms_main

			@property reservation_time type=textbox size=5 table=objects field=meta method=serialize parent=reservation
			@caption Broneeritav aeg

			@property reservation_time_unit type=select table=objects field=meta method=serialize parent=reservation no_caption=1

		@layout buffer type=hbox area_caption=Puhveraeg closeable=1 parent=gentms_main

			@property buffer_time_before type=textbox size=5 table=objects field=meta method=serialize parent=buffer
			@caption Puhveraeg enne

			@property buffer_time_after type=textbox size=5 table=objects field=meta method=serialize parent=buffer
			@caption Puhveraeg p&auml;rast

			@property buffer_time_unit type=select  table=objects field=meta method=serialize parent=buffer no_caption=1

@groupinfo amount_limits caption="Kogusepiirangud" parent=general
@default group=amount_limits

	@property amount_limits type=hidden store=no

	@property amount_limits_tb type=toolbar no_caption=1

	@property aml_inheritable type=checkbox ch_value=1 field=aml_inheritable table=aw_shop_packaging
	@caption P&auml;ritav

	@property inherit_aml_from type=relpicker reltype=RELTYPE_INHERIT_AML_FROM store=connect
	@caption P&auml;ri kogusepiirangud

	@property amount_limits_tbl type=table no_caption=1 store=no

@groupinfo info caption="Lisainfo"

	@groupinfo file caption="Failid" parent=info

		@property files type=releditor reltype=RELTYPE_FILE table=objects field=meta method=serialize mode=manager props=name,file,type,comment,file_url,newwindow group=file table_fields=name
		@caption Failid

	@groupinfo img caption=Pildid parent=info

		@property images type=releditor reltype=RELTYPE_IMAGE table=objects field=meta method=serialize mode=manager props=name,ord,status,file group=img
		@caption Pildid

	@groupinfo data caption="Andmed" parent=info
	@default group=data

		@property size type=textbox
		@caption Suurus

		@property user1 type=textbox field=user1 group=data
		@caption User-defined 1

		@property user2 type=textbox field=user2 group=data
		@caption User-defined 2

		@property user3 type=textbox field=user3 group=data
		@caption User-defined 3

		@property user4 type=textbox field=user4 group=data
		@caption User-defined 4

		@property user5 type=textbox field=user5 group=data
		@caption User-defined 5

		@property user5 type=textbox field=user5 group=data
		@caption User-defined 5

		@property user6 type=textbox field=user6 group=data
		@caption User-defined 6

		@property user7 type=textbox field=user7 group=data
		@caption User-defined 7

		@property user8 type=textbox field=user8 group=data
		@caption User-defined 8

		@property user9 type=textbox field=user9 group=data
		@caption User-defined 9

		@property user10 type=textbox field=user10 group=data
		@caption User-defined 10

		@property user11 type=textbox field=user11 group=data
		@caption User-defined 11

		@property user12 type=textbox field=user12 group=data
		@caption User-defined 12

		@property user13 type=textbox field=user13 group=data
		@caption User-defined 13

		@property user14 type=textbox field=user14 group=data
		@caption User-defined 14

		@property user15 type=textbox field=user15 group=data
		@caption User-defined 15

		@property userta1 type=textarea field=userta1 group=data
		@caption User-defined ta 1

		@property userta2 type=textarea field=userta2 group=data
		@caption User-defined ta 2

		@property userta3 type=textarea field=userta3 group=data
		@caption User-defined ta 3

		@property userta4 type=textarea field=userta4 group=data
		@caption User-defined ta 4

		@property userta5 type=textarea field=userta5 group=data
		@caption User-defined ta 5


		@property uservar1 type=classificator field=uservar1 group=data store=connect reltype=RELTYPE_USERVAR1
		@caption User-defined var 1

		@property uservar2 type=classificator field=uservar2 group=data store=connect reltype=RELTYPE_USERVAR2
		@caption User-defined var 2

		@property uservar3 type=classificator field=uservar3 group=data store=connect reltype=RELTYPE_USERVAR3
		@caption User-defined var 3

		@property uservar4 type=classificator field=uservar4 group=data store=connect reltype=RELTYPE_USERVAR4
		@caption User-defined var 4

		@property uservar5 type=classificator field=uservar5 group=data store=connect reltype=RELTYPE_USERVAR5
		@caption User-defined var 5

		@property userch1 type=checkbox ch_value=1  field=userch1 group=data datatype=int
		@caption User-defined checkbox 1

		@property userch2 type=checkbox ch_value=1  field=userch2 group=data datatype=int
		@caption User-defined checkbox 2

		@property userch3 type=checkbox ch_value=1  field=userch3 group=data datatype=int
		@caption User-defined checkbox 3

		@property userch4 type=checkbox ch_value=1  field=userch4 group=data datatype=int
		@caption User-defined checkbox 4

		@property userch5 type=checkbox ch_value=1  field=userch5 group=data datatype=int
		@caption User-defined checkbox 5

#      Inherited from shop_warehouse_item
@groupinfo purveyance

@groupinfo acl caption=&Otilde;igused
@default group=acl

	@property acl type=acl_manager store=no
	@caption &Otilde;igused

@groupinfo transl caption=T&otilde;lgi
@default group=transl

	@property transl type=callback callback=callback_get_transl store=no
	@caption T&otilde;lgi


@reltype IMAGE value=1 clid=CL_IMAGE
@caption pilt

@reltype FILE value=2 clid=CL_FILE
@caption fail

@reltype USERVAR1 value=3 clid=CL_META
@caption muutuja1

@reltype USERVAR2 value=4 clid=CL_META
@caption muutuja2

@reltype USERVAR3 value=5 clid=CL_META
@caption muutuja3

@reltype USERVAR4 value=6 clid=CL_META
@caption muutuja4

@reltype USERVAR5 value=7 clid=CL_META
@caption muutuja5

@reltype INHERIT_AML_FROM value=8 clid=CL_SHOP_PRODUCT,CL_SHOP_PRODUCT_PACKAGING
@caption P&auml;ri kogusepiirangud

@reltype PRICE value=9 clid=CL_SHOP_ITEM_PRICE
@caption Hind

#      Inherited from shop_warehouse_item
#reltype WAREHOUSE value=25 clid=CL_SHOP_WAREHOUSE
#caption Ladu

*/

class shop_product_packaging extends shop_warehouse_item
{
	function shop_product_packaging()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_product_packaging",
			"clid" => CL_SHOP_PRODUCT_PACKAGING
		));
		$this->trans_props = array(
			"name","comment","user1", "user2", "user3", "user4", "user5", "user6",
			"user7", "user8", "user9", "user10", "user11", "user12", "user13", "user14",
			"user15", "userta1", "userta2", "userta3", "userta4", "userta5"
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "inherit_aml_from":
				$ol = new object_list(array(
					"class_id" => array(CL_SHOP_PRODUCT, CL_SHOP_PRODUCT_PACKAGING),
					"aml_inheritable" => 1,
					"lang_id" => array(),
					"site_id" => array(),
				));
				$data["options"] = array("" => t("--vali--")) + $ol->names();
				break;

			case "amount_limits_tb":
				shop_product::_get_amount_limits_tb($arr);
				break;

			case "amount_limits_tbl":
				shop_product::_get_amount_limits_tbl($arr);
				break;

			case "price_cur":
				$this->_price_cur($arr);
				break;
			case "buffer_time_unit":
			case "reservation_time_unit":
				$prop["options"] = array(
					60 => t("Minutit"),
					3600 => t("Tundi"),
				);
				break;

		};
		return $retval;
	}

	function _get_amount_limits_tbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
		));
		$t->define_field(array(
			"name" => "group",
			"caption" => t("Kasutajagrupp"),
			"align" => "left",
		));
		$t->define_field(array(
			"name" => "min_amount",
			"caption" => t("Minimaalne kogus"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "max_amount",
			"caption" => t("Maksimaalne kogus"),
			"align" => "center",
		));
		$amount_limits = $this->get_amount_limits(array(
			"id" => $arr["obj_inst"]->id(),
		));
		$odl = new object_data_list(
			array(
				"class_id" => CL_GROUP,
				"parent" => array(),
				"lang_id" => array(),
				"site_id" => array(),
			),
			array(
				CL_GROUP => array("name"),
			)
		);
		$groups = $odl->arr();
		foreach($amount_limits as $g => $limits)
		{
			$t->define_data(array(
				"oid" => $g,
				"group" => $groups[$g]["name"],
				"min_amount" => html::textbox(array(
					"name" => "limits[".$g."][min]",
					"value" => $limits["min"],
					"size" => 6,
				)),
				"max_amount" => html::textbox(array(
					"name" => "limits[".$g."][max]",
					"value" => $limits["max"],
					"size" => 6,
				)),
			));
		}
		$t->sort_by(array(
			"field" => "group",
			"sorder" => "asc",
		));
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "content_package_price_condition":
				$retval = PROP_IGNORE;
				break;

			case "amount_limits":
				shop_product::_set_amount_limits($arr);
				break;

			case "amount_limits_tbl":
				if(empty($arr["request"]["amount_limits"]))
				{
					$arr["obj_inst"]->set_meta("amount_limits", $arr["request"]["limits"]);
				}
				break;

			case "price_cur":

				$arr["obj_inst"]->set_meta("cur_prices", $arr["request"]["cur_prices"]);
				break;

			case "transl":
				$this->trans_save($arr, $this->trans_props);
				break;
		}
		return $retval;
	}

	function parse_alias($arr = array())
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars_safe(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	/** returns the html for the product

		@comment

			uses the $layout object to draw the product packaging $prod
			from the layout reads the template and inserts correct vars
			optionally you can give the $quantity parameter
			$oc_obj must be the order center object via what the product is drawn

	**/
	function do_draw_product($arr)
	{
		extract($arr);
		$it = isset($arr["it"]) ? $arr["it"] : NULL;
		$pr_i = get_instance(CL_SHOP_PRODUCT);

		$pi = $prod;
		$prods = $pi->connections_to(array(
			"from.class_id" => CL_SHOP_PRODUCT,
		));
		if(!$prod = reset($prods))
		{
			//return NULL;
			$prod = obj();
		}
		else
		{
			$prod = $prod->from();
		}
		if(!$l_inst)
		{
			$l_inst = $layout->instance();
			$l_inst->read_template($layout->prop("template"));
		}

		$parent_fld = $pi;
		do
		{
			$parent_fld = obj($parent_fld->parent());
		}
		while($parent_fld->class_id() != CL_MENU && $parent_fld->parent());

		$rp_all_cur = "";
		foreach(safe_array($pi->meta("cur_prices")) as $cur_id => $cur_price)
		{
			if ($cur_price != "")
			{
				$cur_obj = obj($cur_id);
				$rp_all_cur .= " ".number_format($cur_price, 2)." ".$cur_obj->name();
			}
		}


		$soc = get_instance(CL_SHOP_ORDER_CART);
		$soc->get_cart($oc_obj);
		$inf = $soc->get_item_in_cart($pi->id());
		$ivs = array(
			"it" => $it,
			"bgcolor" => $bgcolor,
			"packaging_name" => $pi->trans_get_val("name"),
			"packaging_price" => $this->get_price($pi),
			"packaging_id" => $pi->id(),
			"packaging_quantity" => (int)($arr["quantity"]),
			"packaging_view_link" => obj_link($pi->id().":".$oc_obj->id()),
			"name" => $prod->trans_get_val("name"),
			"price" => $this->get_price($prod),
			"tot_price" => number_format(((int)($arr["quantity"]) * $this->get_calc_price($prod)), 2),
			"obj_price" => $this->get_price($pi),
			"obj_price_all_cur" => $rp_all_cur,
			"obj_tot_price" => number_format(((int)($arr["quantity"]) * $this->get_calc_price($pi)), 2),
			"read_price_total" => number_format(((int)($arr["quantity"]) * str_replace(",", "", ifset($inf, "data", "read_price"))), 2),
			"id" => $prod->id(),
			"trow_id" => "trow".$prod->id(),
			"err_class" => ($arr["is_err"] ? "class='selprod'" : ""),
			"quantity" => (int)($arr["quantity"]),
			"view_link" => obj_link($prod->id().":".$oc_obj->id()),
			"edit_link" => $this->mk_my_orb("change", array("id" => $prod->id()), $prod->class_id(), true),
			"obj_id" => $pi->id(),
			"obj_parent" => $parent_fld->id()
		);
		$l_inst->vars_safe($ivs);
		$proc_ivs = $ivs;

		// insert images
		$i = get_instance(CL_IMAGE);
		$cnt = 1;
		$imgc = array();
		if (is_oid($prod->id()))
		{
			$imgc = $prod->connections_from(array("type" => "RELTYPE_IMAGE"));
		}
		usort($imgc, create_function('$a,$b', 'return ($a->prop("to.jrk") == $b->prop("to.jrk") ? 0 : ($a->prop("to.jrk") > $b->prop("to.jrk") ? 1 : -1));'));
		foreach($imgc as $c)
		{
			$u = $i->get_url_by_id($c->prop("to"));
			$l_inst->vars_safe(array(
				"image".$cnt => image::make_img_tag($u, $c->prop("to.name")),
				"image".$cnt."_url" => $u
			));
			$cnt++;
		}
		$imgc = $pi->connections_from(array("type" => "RELTYPE_IMAGE"));
		usort($imgc, create_function('$a,$b', 'return ($a->prop("to.jrk") == $b->prop("to.jrk") ? 0 : ($a->prop("to.jrk") > $b->prop("to.jrk") ? 1 : -1));'));
		foreach($imgc as $c)
		{
			$u = $i->get_url_by_id($c->prop("to"));
			$l_inst->vars_safe(array(
				"packaging_image".$cnt => image::make_img_tag($u, $c->prop("to.name")),
				"packaging_image".$cnt."_url" => $u
			));
			$cnt++;
		}

		for($i = 1; $i < 21; $i++)
		{
			$tmp = $prod->prop("uservar".$i);
			if (is_oid($tmp) && $this->can("view", $tmp))
			{
				$tmp = obj($tmp);
				$tmp = $tmp->name();
			}
			else
			{
				$tmp = "";
			}
			$tmp2 = $pi->prop("uservar".$i);
			if ($tmp2)
			{
				$tmp2 = obj($tmp2);
				$tmp2 = $tmp2->name();
			}
			else
			{
				$tmp2 = "";
			}

			$ui = $prod->prop("user".$i);
			if ($i == 16 && aw_ini_get("site_id") == 139 && $prod->prop("userch5"))
			{
				$ui = $pi->prop("user3");
			}

			$ivar = array(
				"user".$i => $ui,
				"userta".$i => nl2br($prod->trans_get_val("userta".$i)),
				"uservar".$i => $tmp,
				"packaging_user".$i => $pi->prop("user".$i),
				"packaging_userta".$i => nl2br($pi->trans_get_val("userta".$i)),
				"packaging_uservar".$i => $tmp2
			);

			if ($i < 6)
			{
				$ivar["userch".$i] = $prod->prop("userch".$i);
			}

			$l_inst->vars_safe($ivar);
			$proc_ivs += $ivar;
		}
		$pr_i->_int_proc_ivs($proc_ivs, $l_inst);

		// order data
		$soc = get_instance(CL_SHOP_ORDER_CART);
		$awa = $soc->get_item_in_cart(array("iid" => $pi->id(), "it" => $it));
		//$awa = new aw_array($inf["data"]);
		foreach($awa as $datan => $datav)
		{
			if ($datan == "url")
			{
				$datav =str_replace("afto=1", "",$datav);
			}
			$vs = array(
				"order_data_".$datan => $datav
			);
			$l_inst->vars_safe($vs);
			$proc_ivs += $vs;
		}
		$pr_i->_int_proc_ivs($proc_ivs, $l_inst);

		if (!empty($awa["url"]))
		{
			$l_inst->vars_safe(Array(
				"URL_IN_DATA" => $l_inst->parse("URL_IN_DATA")
			));
		}
		else
		{
			$l_inst->vars_safe(Array(
				"NO_URL_IN_DATA" => $l_inst->parse("NO_URL_IN_DATA")
			));
		}

		$l_inst->vars_safe(array(
			"logged" => (aw_global_get("uid") == "" ? "" : $l_inst->parse("logged"))
		));

		return $l_inst->parse();
	}

	function get_price($o)
	{
		return number_format($o->prop("price"),2);
	}

	function get_calc_price($o)
	{
		return $o->prop("price");
	}

	function get_prod_calc_price($o)
	{
		foreach($o->connections_to(array(
			"from.class_id" => CL_SHOP_PRODUCT,
			"type" => 2 // RELTYPE_PACKAGING
		)) as $c)
		{
			$o = $c->from();
			return $o->prop("price");
		}
		return 0;
	}

	function request_execute($obj)
	{
		list($prod_id, $oc_id) = explode(":", aw_global_get("section"));
		$prod = obj($prod_id);

		// get layout from soc.
		$soc_o = obj($oc_id);
		$soc_i = $soc_o->instance();

		$layout = $soc_i->get_long_layout_for_prod(array(
			"soc" => $soc_o,
			"prod" => $prod
		));

		return $this->do_draw_product(array(
			"layout" => $layout,
			"prod" => $prod,
			"oc_obj" => $soc_o
		));
	}

	function get_must_order_num($o)
	{

		foreach($o->connections_to(array(
			"from.class_id" => CL_SHOP_PRODUCT,
		)) as $prod)
		{
			$prod = $prod->from();
			$prod_i = $prod->instance();
			return $prod_i->get_must_order_num($prod);
		}
		return null;
	}

	function _price_cur($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		if (!$t)
		{
			return;
		}

		$t->define_field(array(
			"name" => "pr",
			"caption" => t("Hind"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "cur",
			"caption" => t("Valuuta"),
			"align" => "center"
		));

		$ol = new object_list(array(
			"class_id" => CL_CURRENCY,
			"lang_id" => array(),
			"site_id" => array(),
			"sort_by" => "name asc",
		));
		$prs = $arr["obj_inst"]->meta("cur_prices");
		foreach($ol->arr() as $cur)
		{
			$t->define_data(array(
				"pr" => html::textbox(array(
					"name" => "cur_prices[".$cur->id()."]",
					"size" => 5,
					"value" => $prs[$cur->id()]
				)),
				"cur" => $cur->name()
			));
		}
		$t->set_sortable(false);
	}

	/** Returns the list of amount limits for the product.
		@attrib name=get_ammuot_limits api=1 params=name

		@param id required type=oid
			The oid of the product the limits are asked for.

		@param group optional type=array(oid),oid
			If no group is given, limits for all groups will be returned. Can be either oid or array of oid's.

		@returns The list of amount limits for the product.

	**/
	function get_amount_limits($arr)
	{
		$ret = array();
		$o = obj($arr["id"]);
		if($this->can("view", $o->inherit_aml_from) && $arr["id"] != $o->inherit_aml_from)
		{
			$arr["id"] = $o->inherit_aml_from;
			return $this->get_amount_limits($arr);
		}
		$amount_limits = $o->meta("amount_limits");
		if(!is_array($amount_limits))
		{
			$amount_limits = array();
		}

		if(is_oid($arr["group"]))
		{
			$arr["group"] = array($arr["group"]);
		}

		foreach($arr["group"] as $g)
		{
			if(array_key_exists($g, $amount_limits))
			{
				$ret[$g] = $amount_limits[$g];
			}
		}

		// If no group is given, limits for all groups will be returned.
		if(count($arr["group"]) == 0)
		{
			return $amount_limits;
		}
		return $ret;
	}

	/**
		@attrib name=delete_amounts
	**/
	function delete_amounts($arr)
	{
		foreach($arr["limits"] as $g => $limit)
		{
			if($arr["sel"][$g] == $g)
				unset($arr["limits"][$g]);
		}
		$o = obj($arr["id"]);
		$o->set_meta("amount_limits", $arr["limits"]);
		$o->save();

		return $arr["post_ru"];
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function callback_mod_tab($arr)
	{
		if ($arr["id"] == "transl" && aw_ini_get("user_interface.content_trans") != 1)
		{
			return false;
		}
		return true;
	}

	function callback_get_transl($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
	}

	function do_db_upgrade($t, $f, $query, $error)
	{
		if ($tbl == "aw_shop_packaging" && $field == "")
		{
			$this->db_query("create table aw_shop_packaging (id int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_special_price":
			case "aw_price":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "double"
				));
				break;

			case "size":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "varchar(255)"
				));
				break;

			case "aw_content_package_price_condition":
			case "aw_product":
			case "price_object":
			case "special_price_object":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				break;

			case "aml_inheritable":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				$ol = new object_list(array(
					"class_id" => CL_SHOP_PRODUCT_PACKAGING,
					"lang_id" => array(),
					"site_id" => array(),
					"parent" => array(),
					"status" => array(),
				));
				foreach($ol->arr() as $o)
				{
					$v = 0;
					$oid = $o->id();
					$this->db_query("
						INSERT INTO
							aw_shop_packaging (id, $f)
						VALUES
							('$oid', '$v')
						ON DUPLICATE KEY UPDATE
							$f = '$v'
					");
				}
				return true;
		}
		return false;
	}
}
