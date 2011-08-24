<?php

// shop_order_center.aw - Tellimiskeskkond
/*

@tableinfo aw_shop_order_center index=aw_id master_table=objects master_index=brother_of

@classinfo syslog_type=ST_SHOP_ORDER_CENTER relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@default group=general_
@default table=aw_shop_order_center

@property warehouse type=relpicker reltype=RELTYPE_WAREHOUSE field=aw_warehouse_id
@caption Ladu

@property root_menu type=relpicker reltype=RELTYPE_MENU store=connect multiple=1
@caption Veebis kuvamise juurkaustad

@property cart type=relpicker reltype=RELTYPE_CART field=aw_cart_id
@caption Ostukorv

@property default_currency type=relpicker reltype=RELTYPE_DEFAULT_CURRENCY field=aw_default_currency
@caption Vaikimisi valuuta

@default table=objects
@default field=meta
@default method=serialize

@default group=mail_settings_orderer

	@property mail_to_client type=checkbox ch_value=1
	@caption Saada tellijale e-mail

	@property mail_recievers type=relpicker reltype=RELTYPE_MAIL_RECIEVERS store=connect multiple=1
	@caption Kinnitusmeili saajad

	@property mail_from_addr type=textbox
	@caption Meili From aadress

	@property mail_from_name type=textbox
	@caption Meili From nimi

#	@property mail_cust_content type=textarea rows=10 cols=80
#	@caption Meili sisu (kui t&uuml;hi, siis templatest)
#
	@property mail_template type=select
	@caption E-maili template

@default group=mail_settings_seller

#	@property mails_sep_by_el type=checkbox ch_value=1
#	@caption Saada eraldi meilid vastavalt klassifikaatorile

#	@property send_attach type=checkbox ch_value=1
#	@caption Lisa meili manusega tellimus

@default group=payment1

	@property web_discount type=textbox size=5 user=1
	@caption Veebis allahindlus (%)

	@property data_form_discount type=select user=1
	@caption Allahindluse element andmete vormis

	@property rent_prop type=select user=1
	@caption Elemendi

	@property rent_prop_val type=textbox user=1
	@caption v&auml;&auml;rtus j&auml;relmaksuks

	## SEE ON VANA ASI, LIHTSALT 2RA KAOTADA EI TOHI!
	@property rent_min_amt type=textbox user=1
	@caption J&auml;relmaksu miinumumsumma

@default group=payment_types

	@property payment_types_tlb type=toolbar store=no no_caption=1

	@property payment_types_tbl type=table store=no no_caption=1


@default group=delivery

	@property extra_address_delivery_types type=relpicker multiple=1 store=connect reltype=RELTYPE_EXTRA_ADDRESS_DELIVERY_TYPES
	@caption Lisaadressi omavad kohaletoimetamise viisid
	@comment Kohaletoimetamise viisid, millel on omal miski aadresside valik kuhu saadetakse kaup (nt. smartpost)

	@property show_delivery type=checkbox ch_value=1
	@caption Kuva kohaletoimetamise valikut
	@comment Suunab ostukorvi vaatest otse isikuandmete vaatesse, kui valitud, ning ei n&auml;ita kohaletoimetamise muutujaid mujal vaadetes



	@property delivery_show_controller type=relpicker reltype=RELTYPE_DELIVERY_SHOW_CONTROLLER
	@caption N&auml;itamise kontroller
	@comment Vaja t88le kruvida

	@property delivery_save_controller type=relpicker reltype=RELTYPE_DELIVERY_SAVE_CONTROLLER
	@caption Salvestamise kontroller
	@comment Vaja t88le kruvida

	@property delivery_exec_controller type=relpicker reltype=RELTYPE_DELIVERY_EXEC_CONTROLLER
	@caption Teostamiselesaatmise kontroller
	@comment Vaja t88le kruvida

	@property cart_value_controller type=relpicker reltype=RELTYPE_CART_VALUE_CONTROLLER
	@caption Korvi hinna kontroller
	@comment Vaja t88le kruvida


@default group=appearance
	@property appearance_toolbar type=toolbar no_caption=1 store=no
	@layout appearance_c width=30%:70% type=hbox
		@layout appearance_l type=vbox closeable=1 parent=appearance_c area_caption=Kaustade&nbsp;puu
			@property appearance_tree type=treeview store=no no_caption=1 parent=appearance_l
		@layout appearance_r type=vbox closeable=1 parent=appearance_c area_caption=Kaustade&nbsp;all&nbsp;toodete&nbsp;kuvamise&nbsp;seaded
			@property count_active_products type=text store=no no_caption=1 parent=appearance_r
			@caption M&uuml;&uuml;gil olevaid tooteid kokku

			@property appearance_list type=table store=no no_caption=1 parent=appearance_r
			@caption N&auml;itamise kaustade seaded

@default group=appear_settings

	@property per_page type=textbox table=aw_shop_order_center field=aw_per_page method=null
	@caption Tooteid lehek&uuml;ljel

	@property product_type type=select multiple=1
	@caption N&auml;idatavad klassi t&uuml;&uuml;bid

	@property dont_sell_not_available_products type=checkbox ch_value=1
	@caption Auml;ra m&uuml;&uuml; tooteid mis pole saadaval
	@comment Toodete mitte n&auml;itamiseks, mis on tarne infoga mis laos defineeritud kui v&auml;lja m&uuml;&uuml;dud

	@property only_active_items type=checkbox ch_value=1
	@caption Ainult aktiivsed tooted
	@comment Deaktiivseid pakette/tooteid/pakendeid ei kuvata ning ka ostukorvi neid lisada ei saa

	@property inactive_item_tpl type=textbox
	@caption Deaktiivse toote kuvamise templeit

@property childtitle1 type=text store=no subtitle=1
@caption Vanad-&uuml;le-vaadata-kas-toimivad-ja-kas-vaja

	@property use_controller type=checkbox ch_value=1
	@caption N&auml;itamiseks kasuta kontrollerit

	@property use_cart_controller type=checkbox ch_value=1
	@caption Ostukorvi n&auml;itamiseks kasuta kontrollerit

	@property no_show_cart_contents type=checkbox ch_value=1
	@caption &Auml;ra n&auml;ita korvi kinnitusvaadet

	@property controller type=relpicker reltype=RELTYPE_CONTROLLER
	@caption Vaikimisi n&auml;itamise kontroller

	@property order_show_controller type=relpicker reltype=RELTYPE_CONTROLLER
	@caption Tellimuse n&auml;itamise kontroller

	@property sortbl type=table store=no
	@caption Toodete sorteerimine

	@property grouping type=select
	@caption Toodete grupeerimine

	@property disp_cart_in_web type=checkbox ch_value=1
	@caption Kuva korvi toodete all

	@property no_change_button type=checkbox ch_value=1
	@caption &Auml;ra kuva tellimiskeskkonnas toote k&otilde;rvale "Muuda" nuppu

	@property prods_are_folders type=checkbox ch_value=1
	@caption Veebis tooted on kataloogid


@default group=appear_ctr

	@property controller_tbl type=callback callback=callback_get_controller_tbl store=no
	@caption Kontrollerid kataloogidele

@default group=appear_layout

	@property layoutbl type=table store=no no_caption=1
	@caption Toodete layout

@default group=psfieldmap
	@property person_properties type=table store=no
	@caption Tellimuse andmete vormis asuvad isikuandmed


@default group=data_settings

	@property data_form type=relpicker reltype=RELTYPE_ORDER_FORM
	@caption Tellija andmete vorm

	@property data_form_person type=select
	@caption Isiku nime element andmete vormis

	@property data_form_company type=select
	@caption Organisatsiooni nime element andmete vormis

	@property psfieldmap type=table store=no
	@caption Vali millised elemendid tellimuse andmete vormis vastavad isukuandmetele

@default group=orgfieldmap

	@property orgfieldmap type=table store=no
	@caption Vali millised elemendid tellimuse andmete vormis vastavad firma andmetele

@default group=payment_settings

	@property use_bank_payment type=checkbox ch_value=1
	@caption Kasuta pangamakset
	@comment Kui see valitud, siis kinnitades ei kinnita tellimust enne &auml;ra kui on makstud. Kui varem on templeidis olemas pankade valik, siis suunab otse maksma, kui pole, siis tuleb peale kinnitusvaadet pangamaksete vormide vaade

	@property bank_payment type=releditor reltype=RELTYPE_BANK_PAYMENT store=connect props=cancel_url,bank direct_links=1 rel_id=first use_form=emb
	@caption Pangamakse objekt

#	@property bank_id type=select
#	@caption Panga muutuja

#	@property orderer_mail type=select
#	@caption Tellija mailiaadressi muutuja

#	@property bank_lang type=select
#	@caption Panga keele muutuja

@default group=filter_settings

	@property use_filtering type=checkbox ch_value=1
	@caption Filtreeri tooteid

	@property filter_fields_class type=table store=no no_caption=1
	@caption Filtrid integratsiooniklassist

	@property filter_fields_props type=table store=no no_caption=1
	@caption Filtrid toote omadustest

@default group=filter_select

	@property filter_settings_tb type=toolbar store=no no_caption=1
	@property filter_settings type=table store=no no_caption=1

@default group=filter_set_folders

	@property filter_sel_for_folders type=table store=no no_caption=1

@default group=settings

	@property cart_type type=chooser
	@caption Ostukorvi t&uuml;&uuml;p

	@property multi_items type=checkbox ch_value=1
	@caption Ostukorvis v&otilde;ib olla mitu sama ID-ga toodet

	@property show_unconfirmed type=checkbox ch_value=1
	@caption N&auml;ita tellijale tellimuste nimekirjas ainult kinnitamata tellimusi

#see vaid vanas tellimuses kasutuses
#	@property pdf_template type=textbox
#	@caption PDF Template faili nimi

	@property show_prod_and_package type=checkbox ch_value=1
	@caption N&auml;ita selgituses toodet/paketti

	@property chart_show_template type=select
	@caption Ostukorvi vaade template

	@property chart_final_template type=select
	@caption Ostukorvi l&ouml;ppvaate template

	@property integration_class type=select
	@caption Integratsiooni klass

@default group=bonus

	@property bonus_toolbar type=toolbar store=no no_caption=1
	@property bonus_table type=table store=no no_caption=1


@groupinfo general_ parent=general caption="&Uuml;ldine"
@groupinfo settings caption="Seaded" parent=general

@groupinfo appear caption="N&auml;itamine"
	@groupinfo appearance parent=appear caption="N&auml;itamine"
	@groupinfo appear_settings parent=appear caption="Seaded"
	@groupinfo appear_ctr parent=appear caption="Kontrollerid"
	@groupinfo appear_layout parent=appear caption="Layoudid"

@groupinfo payment caption="Maksmine"
	@groupinfo payment_types caption="J&auml;relmaks" parent=payment
	@groupinfo payment_settings caption="Pangamakse seaded" parent=payment
	@groupinfo payment1 caption="Seaded" parent=payment

@groupinfo delivery caption="Kohaletoimetamine"


@groupinfo mail_settings caption="Meiliseaded"
	@groupinfo mail_settings_orderer caption="Tellijale" parent=mail_settings
	@groupinfo mail_settings_seller caption="Pakkujale" parent=mail_settings


@groupinfo data caption="Andmed"
	@groupinfo psfieldmap caption="Isikuandmete kaart" parent=data
	@groupinfo data_settings caption="Seaded" parent=data
	@groupinfo orgfieldmap caption="Firma andmete kaart" parent=data

@groupinfo filter caption="Filtreerimine"
	@groupinfo filter_settings caption="Seaded" parent=filter
	@groupinfo filter_select caption="Koosta filter" parent=filter submit=no
	@groupinfo filter_set_folders caption="Vali kehtivad filtrid" parent=filter

@groupinfo bonus caption="Boonuskoodid"

##################### RELTYPES

@reltype WAREHOUSE value=1 clid=CL_SHOP_WAREHOUSE
@caption ladu

@reltype TABLE_LAYOUT value=2 clid=CL_SHOP_PRODUCT_TABLE_LAYOUT
@caption toodete tabeli kujundus

@reltype ITEM_LAYOUT value=3 clid=CL_SHOP_PRODUCT_LAYOUT
@caption toote kujundus

@reltype CART value=4 clid=CL_SHOP_ORDER_CART
@caption ostukorv

@reltype ORDER_FORM value=5 clid=CL_CFGFORM
@caption vorm tellija andmete jaoks

@reltype CONTROLLER value=6 clid=CL_FORM_CONTROLLER
@caption n&auml;itamise kontroller

@reltype DELIVERY_SHOW_CONTROLLER value=14 clid=CL_FORM_CONTROLLER
@caption kohaletoimetamise n&auml;itamise kontroller

@reltype DELIVERY_SAVE_CONTROLLER value=15 clid=CL_FORM_CONTROLLER
@caption kohaletoimetamise salvestamise kontroller

@reltype DELIVERY_EXEC_CONTROLLER value=16 clid=CL_FORM_CONTROLLER
@caption kohaletoimetamise teostamiselesaatmise kontroller

@reltype CART_VALUE_CONTROLLER value=17 clid=CL_FORM_CONTROLLER
@caption Korvi hinna kontroller

@reltype ORDER_NAME_CTR value=7 clid=CL_FORM_CONTROLLER
@caption tellimuse nime kontroller

@reltype BANK_PAYMENT value=11 clid=CL_BANK_PAYMENT
@caption Pangalingi objekt

@reltype FILTER value=12 clid=CL_SHOP_ORDER_CENTER_FILTER_ENTRY
@caption Toodete filtri sisestus

@reltype RELTYPE_MENU value=18 clid=CL_MENU
@caption Kaust

@reltype DEFAULT_CURRENCY value=20 clid=CL_CURRENCY
@caption Vaikimisi valuuta

@reltype EXTRA_ADDRESS_DELIVERY_TYPES value=21 clid=CL_SHOP_DELIVERY_METHOD
@postitusaadresse omavad kohaletoimetamise meetodid

@reltype MAIL_RECIEVERS value=22 clid=CL_CRM_PERSON,CL_ML_MEMBER
@caption Kinnitusmaili saajad

@reltype NOT_AVAILABLE_PURVEIANCE value=23 clid=CL_SHOP_PRODUCT_PURVEYANCE
@caption Tarneinfo mis t&auml;hendab, et toode ei ole saadaval

@reltype BONUS_CODE_PRODUCT value=24 clid=CL_SHOP_PRODUCT,CL_SHOP_PRODUCT_PACKAGING,CL_SHOP_PACKET
@caption Boonuskoodi toode

*/

class shop_order_center extends class_base
{
	protected $_oinst;
	protected $tblayouts;
	protected $tblayout_items;
	protected $itemlayouts;
	protected $itemlayouts_long;
	protected $itemlayouts_long_2;
	protected $itemlayout_items;
	protected $folder_obj;
	protected $web_discount;
	protected $last_menu;
	protected $xi;
	protected $__is;
	protected $filter_sel;
	protected $selected_filters;
	protected $imgbase;
	protected $shop;

	function shop_order_center()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_order_center",
			"clid" => CL_SHOP_ORDER_CENTER
		));
	}

	function callback_mod_tab($arr)
	{
		if (ifset($arr, "group") === "delivery_cfg" and !$arr["obj_inst"]->prop("show_delivery"))
		{
			return false;
		}
	}


	function callback_mod_layout(&$arr)
	{
		if($arr["name"] == "appearance_r")
		{
			if(isset($arr["request"]["menu"]) && $this->can("view" , $arr["request"]["menu"]))
			{
				$arr["area_caption"] = sprintf(t("Kausta %s all asuvate kaustade toodete n&auml;itamise seaded"), get_name($arr["request"]["menu"]));
			}
		}
		return true;
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "product_type":
				$products_show = new products_show();
				$prop["options"] = $products_show->types;
				break;
			case "bank_id":
			case "orderer_mail":
			case "bank_lang":
				$cx = new cfgutils();
				$props = $cx->load_class_properties(array(
					"clid" => CL_REGISTER_DATA,
				));
				foreach($props as $p => $dsadsad)
				{
					$prop["options"][$p] = $p;
				}
				break;
			case "cart_type":
				$prop["options"] = array(
					0 => t("Sessionip&otilde;hine"),
					1 => t("Kasutajap&otilde;hine"),
				);
				break;

			case "chart_show_template":
			case "chart_final_template":

				$tm = new templatemgr();
				$prop["options"] = $tm->template_picker(array(
					"folder" => "applications/shop/shop_order_cart/"
				));
				break;
			case "mail_template":
				$tm = new templatemgr();
				$prop["options"] = $tm->template_picker(array(
					"folder" => "applications/shop/shop_sell_order/"
				));
				break;

			case "rent_prop":
				$df = $arr["obj_inst"]->prop("data_form");
				$opts = array();
				if (is_oid($df) && $this->can("view", $df))
				{
					$cu = get_instance(CL_CFGFORM);
					$ps = $cu->get_props_from_cfgform(array(
						"id" => $df
					));
					foreach($ps as $pn => $pd)
					{
						$opts[$pn] = $pd["caption"];
					}
				}
				$prop["options"] = $opts;
				break;

			case "layoutbl":
				if ($arr["obj_inst"]->prop("use_controller"))
				{
//					return PROP_IGNORE;
				}
				$this->do_layoutbl($arr);
				break;

			case "sortbl":
				$this->do_sortbl($arr);
				break;
			case "grouping":
				$prop["options"] = array("" => "" , "parent" => t("Kaust"));
				break;

			case "controller":
				if (!$arr["obj_inst"]->prop("use_controller"))
				{
					return PROP_IGNORE;
				}
				break;

			case "controller_tbl":
				if (!$arr["obj_inst"]->prop("use_controller"))
				{
					return PROP_IGNORE;
				}
				break;

			case "data_form_person":
			case "data_form_company":
			case "data_form_discount":
			case "mail_to_seller_in_el":
				if (!$arr["obj_inst"]->prop("data_form"))
				{
					return PROP_IGNORE;
				}
				$opts = array("" => "");
				$props = $this->get_properties_from_data_form($arr["obj_inst"]);
				foreach($props as $pn => $pd)
				{
					$opts[$pn] = empty($pd["caption"]) ? $pd["name"] : $pd["caption"];
				}
				$prop["options"] = $opts;
				break;

			case "psfieldmap":
				return $this->do_psfieldmap($arr);
				break;

			case "orgfieldmap":
				return $this->do_orgfieldmap($arr);
				break;

			case "mail_group_by":
				$cu = get_instance("cfg/cfgutils");
				$ps = $cu->load_properties(array("clid" => CL_SHOP_PRODUCT));
				$v = array("" => "");
				foreach($ps as $pn => $pd)
				{
					$v[$pn] = isset($pd["caption"]) ? $pd["caption"] : $pd["name"];
				}
				$prop["options"] = $v;
				break;
			case "count_active_products":
				$prop["value"] = sprintf(t("M&uuml;&uuml;gil tooteid kokku : %s") , $arr["obj_inst"]->get_active_products_count());
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
			case "layoutbl":
				$this->do_save_layoutbl($arr);
				break;

			case "sortbl":
				$this->do_save_sortbl($arr);
				break;
			case "psfieldmap":
				$arr["obj_inst"]->set_meta("ps_pmap", $arr["request"]["pmap"]);
				break;

			case "orgfieldmap":
				$arr["obj_inst"]->set_meta("org_pmap", $arr["request"]["pmap"]);
				break;

			case "controller_tbl":
				$this->save_ctr_t($arr);
				break;
			case "use_bank_payment":
				$this->_set_bank_payment($arr);
				break;
		}
		return $retval;
	}

	function do_save_layoutbl(&$arr)
	{
		$arr["obj_inst"]->set_meta("itemlayouts", $arr["request"]["itemlayout"]);
		$arr["obj_inst"]->set_meta("itemlayouts_long", $arr["request"]["itemlayout_long"]);
		$arr["obj_inst"]->set_meta("itemlayouts_long_2", $arr["request"]["itemlayout_long_2"]);
		$arr["obj_inst"]->set_meta("tblayouts", $arr["request"]["tblayout"]);
	}

	function _set_bank_payment($arr)
	{
		$bank_payment_instance = get_instance(CL_BANK_PAYMENT);
		$bank_payment_instance->submit_meta(array(
			"request" => $arr["request"],
			"obj_inst" => obj($arr["request"]["bank_payment"]["id"]),
			"prop" => array("name" => "bank"),
		));
	}

	function _get_bonus_toolbar($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->add_save_button();
	}

	protected function _define_bonus_table_header($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->define_field(array(
			"name" => "code",
			"caption" => t("Boonuskood"),
			"callback" => array($this, "_callback_bonus_table_code"),
			"callb_pass_row" => true,
		));
		$t->define_field(array(
			"name" => "products",
			"caption" => t("Tooted, mis selle koodi sisestamise korral kliendi tellimusse lisatakse"),
			"callback" => array($this, "_callback_bonus_table_products"),
			"callb_pass_row" => true,
		));
	}

	function _callback_bonus_table_code($row)
	{
		return html::textbox(array(
			"name" => "bonus_codes[{$row["products"]["i"]}][code]",
			"value" => $row["code"],
		));
	}

	function _callback_bonus_table_products($row)
	{
		$relpicker = new relpicker();
		return $relpicker->create_relpicker(array(
			"name" => "bonus_codes[{$row["products"]["i"]}][products]",
			"reltype" => "RELTYPE_BONUS_CODE_PRODUCT",
			"oid" => $row["products"]["id"],
			"property" => "bonus_codes[{$row["products"]["i"]}][products]",
			"multiple" => true,
			"value" => $row["products"]["product_oids"],
			"options" => $row["products"]["product_names"],
			"no_edit" => true,
			"do_search" => true,
		));
	}

	function _get_bonus_table($arr)
	{
		if(!is_oid($arr["obj_inst"]->id()))
		{
			return PROP_IGNORE;
		}

		$this->_define_bonus_table_header($arr);

		$t = $arr["prop"]["vcl_inst"];

		$bonuscodes = $arr["obj_inst"]->get_bonus_codes();
		$i = 0;
		foreach($bonuscodes as $bonuscode => $products)
		{
			$product_oids = $product_names = array();
			foreach($products as $product)
			{
				$product_oids[$product] = $product;
				$product_names[$product] = obj($product)->name();
			}

			$t->define_data(array(
				"code" => $bonuscode,
				//	The following is a workaround, cuz apparently I can't define data for which there hasn't been a field defined.
				"products" => array(
					"id" => $arr["obj_inst"]->id(),
					"i" => $i++,
					"product_oids" => $product_oids,
					"product_names" => $product_names
				)
			));
		}

		$t->define_data(array(
			"code" => "",
			"products" =>  array(
				"id" => $arr["obj_inst"]->id(),
				"i" => $i++,
				"products" => array(),
			),
		));
	}

	function _set_bonus_table($arr)
	{
		$bonus_codes = array();
		foreach($arr["request"]["bonus_codes"] as $data)
		{
			$code = trim($data["code"]);
			if(strlen($code) > 0)
			{
				$bonus_codes[$code] = $data["products"];
			}
		}

		$arr["obj_inst"]->set_bonus_codes($bonus_codes);
	}

	function _get_person_properties($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "name",
			"caption" => t("V&auml;lja nimi")
		));
		$t->define_field(array(
			"name" => "show",
			"caption" => t("N&auml;ita"),
		));
		$t->define_field(array(
			"name" => "req",
			"caption" => t("Kohustuslik"),
		));
		$t->define_field(array(
			"name" => "jrk",
			"caption" => t("Jrk"),
		));

		$cart = get_instance(CL_SHOP_ORDER_CART);
		$orderer_vars = $arr["obj_inst"]->meta("orderer_vars");

		foreach($cart->orderer_vars as $var => $caption)
		{
			$t->define_data(array(
				"name" => $caption,
				"show" => html::checkbox(array(
					"name" => "orderer_vars[show][".$var."]",
					"checked" => empty($orderer_vars["show"][$var]) ? "" : 1,
				)),
				"req" => html::checkbox(array(
					"name" => "orderer_vars[req][".$var."]",
					"checked" => empty($orderer_vars["req"][$var]) ? "" : 1,
				)),
				"jrk" => html::textbox(array(
					"name" => "orderer_vars[jrk][".$var."]",
					"value" => empty($orderer_vars["jrk"][$var]) ? "" : $orderer_vars["jrk"][$var],
				)),
			));
		}
	}

	function _set_person_properties($arr)
	{
		$arr["obj_inst"]->set_meta("orderer_vars" , $arr["request"]["orderer_vars"]);
	}

	function do_save_sortbl(&$arr)
	{
		$awa = new aw_array($arr["request"]["itemsorts"]);
		$res = array();
		foreach($awa->get() as $idx => $dat)
		{
			if ($dat["element"])
			{
				$res[] = $dat;
			}
		}

		$arr["obj_inst"]->set_meta("itemsorts", $res);
	}

	function _init_layoutbl($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Kataloog")
		));

		$t->define_field(array(
			"name" => "tbl_layout",
			"caption" => t("Tabeli kujundus"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "item_layout",
			"caption" => t("Paketi kujundus"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "item_layout_long",
			"caption" => t("Paketi vaate kujundus"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "item_layout_long_2",
			"caption" => t("Paketi teise vaate kujundus"),
			"align" => "center"
		));

		$t->set_default_sortby("name");
	}

	function do_layoutbl(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_layoutbl($t);

		$wh = get_instance(CL_SHOP_WAREHOUSE);

		$o = $arr["obj_inst"];
		$this->_get_folder_ot_from_o($o);

		$this->_oinst = &$o;

		$this->tblayouts = $o->meta("tblayouts");
		$this->tblayout_items = array("0" => t("--vali--"));
		foreach($o->connections_from(array("type" => "RELTYPE_TABLE_LAYOUT")) as $c)
		{
			$this->tblayout_items[$c->prop("to")] = $c->prop("to.name");
		}

		$this->itemlayouts = $o->meta("itemlayouts");
		$this->itemlayouts_long = $o->meta("itemlayouts_long");
		$this->itemlayouts_long_2 = $o->meta("itemlayouts_long_2");

		$this->itemlayout_items = array("0" => "--vali--");
		foreach($o->connections_from(array("type" => "RELTYPE_ITEM_LAYOUT")) as $c)
		{
			$this->itemlayout_items[$c->prop("to")] = $c->prop("to.name");
		}

		if (!$o->prop("warehouse"))
		{
			return new object_list();
		}
		$wh = obj($o->prop("warehouse"));

		if (!$wh->prop("conf"))
		{
			return new object_list();
		}
		$conf = obj($wh->prop("conf"));
		$o = obj($conf->prop("pkt_fld"));
		$this->layoutbl_ot_cb($o, $t);

		$ot = new object_tree(array(
			"class_id" => CL_MENU,
			"parent" => $o->id(),
		));

		$ot->foreach_cb(array(
			"func" => array($this, "layoutbl_ot_cb"),
			"param" => $t,
			"save" => false
		));

		$t->sort_by();
	}


	function layoutbl_ot_cb(&$o, &$t)
	{
		$t->define_data(array(
			"name" => $o->path_str(),
			"tbl_layout" => html::select(array(
				"name" => "tblayout[".$o->id()."]",
				"options" => $this->tblayout_items,
				"selected" => $this->tblayouts[$o->id()]
			)),
			"item_layout" => html::select(array(
				"name" => "itemlayout[".$o->id()."]",
				"options" => $this->itemlayout_items,
				"selected" => $this->itemlayouts[$o->id()]
			)),
			"item_layout_long" => html::select(array(
				"name" => "itemlayout_long[".$o->id()."]",
				"options" => $this->itemlayout_items,
				"selected" => $this->itemlayouts_long[$o->id()]
			)),
			"item_layout_long_2" => html::select(array(
				"name" => "itemlayout_long_2[".$o->id()."]",
				"options" => $this->itemlayout_items,
				"selected" => $this->itemlayouts_long_2[$o->id()]
			)),
		));
	}

	function _init_sortbl(&$t)
	{
		$t->define_field(array(
			"name" => "sby",
			"caption" => t("Sorditav v&auml;li"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "sby_ord",
			"caption" => t("Kasvav / kahanev"),
			"align" => "center"
		));
	}

	function do_sortbl(&$arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_sortbl($t);

		$elements = array("" => "");
		list($GLOBALS["properties"][CL_SHOP_PRODUCT], $GLOBALS["tableinfo"][CL_SHOP_PRODUCT], $GLOBALS["relinfo"][CL_SHOP_PRODUCT]) = $GLOBALS["object_loader"]->load_properties(array(
			"clid" => CL_SHOP_PRODUCT
		));
		foreach($GLOBALS["properties"][CL_SHOP_PRODUCT] as $pn => $pd)
		{
			$elements[$pn] = isset($pd["caption"]) ? $pd["caption"] : $pd["name"];
		}
		$elements["jrk"] = t("J&auml;rjekord");


		$maxi = 0;
		$is = new aw_array($arr["obj_inst"]->meta("itemsorts"));
		foreach($is->get() as $idx => $sd)
		{
			$t->define_data(array(
				"sby" => html::select(array(
					"options" => $elements,
					"selected" => $sd["element"],
					"name" => "itemsorts[$idx][element]"
				)),
				"sby_ord" => html::select(array(
					"options" => array("asc" => "Kasvav", "desc" => "Kahanev"),
					"selected" => $sd["ord"],
					"name" => "itemsorts[$idx][ord]"
				))
			));
			$maxi = max($maxi, $idx);
		}
		$maxi++;

		$t->define_data(array(
			"sby" => html::select(array(
				"options" => $elements,
				"selected" => "",
				"name" => "itemsorts[$maxi][element]"
			)),
			"sby_ord" => html::select(array(
				"options" => array("asc" => "Kasvav", "desc" => "Kahanev"),
				"selected" => "",
				"name" => "itemsorts[$maxi][ord]"
			))
		));

		$t->set_sortable(false);
	}

	function parse_alias($arr = array())
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	function show($arr)
	{
		return $this->my_orders(array());
	}

	function get_folders_as_object_list($o, $level, $parent)
	{
		$this->folder_obj = $o;

		if (!$o->prop("warehouse"))
		{
			return new object_list();
		}
		$wh = obj($o->prop("warehouse"));

		if (!$wh->prop("conf"))
		{
			return new object_list();
		}
		$conf = obj($wh->prop("conf"));

		if (!$conf->prop("pkt_fld"))
		{
			return new object_list();
		}

		if ($level > 0 && $parent)
		{
			$ol = new object_list(array(
				"parent" => $parent->id(),
				"class_id" => $conf->prop("prod_tree_clids"),
				"sort_by" => "objects.jrk,objects.created",
				"status" => STAT_ACTIVE
			));
			if (!$ol->count() && $o->prop("prods_are_folders"))
			{
				// list prods for this folder instead of folders
				$ol = new object_list(array(
					"parent" => $parent->id(),
					"class_id" => CL_SHOP_PRODUCT,
					"sort_by" => "objects.jrk,objects.created",
					"status" => STAT_ACTIVE
				));
			}
		}
		else
		{
			$ol = new object_list(array(
				"parent" => $conf->prop("pkt_fld"),
				"class_id" => $conf->prop("prod_tree_clids"),
				"sort_by" => "objects.jrk,objects.created",
				"status" => STAT_ACTIVE
			));
		}

		return $ol;
	}

	function make_menu_link($o, $ref = NULL)
	{
		if ($o->prop("link") != "")
		{
			return $o->prop("link");
		}

		$sect = $o->id();
		if (aw_ini_get("user_interface.full_content_trans"))
		{
			$sect = aw_global_get("ct_lang_lc")."/".$sect;
		}
		if ($ref === NULL)
		{
			$link =  $this->mk_my_orb("show_items", array("id" => $this->folder_obj->id(), "section" => $sect));
		}
		else
		{
			$link =  $this->mk_my_orb("show_items", array("id" => $ref->id(), "section" => $sect));
		}
		return urldecode($link);
	}

	/** shows shop items

		@attrib name=show_items nologin="1"

		@param id required type=int acl=view
		@param f optional
		@param show_prod optional
		@param section required

	**/
	function show_items($arr)
	{
		enter_function("shop_order_center::show_items");
		extract($arr);
		$soc = obj($arr["id"]);
		if ($soc->prop("use_controller"))
		{
			$ctr = $soc->prop("controller");

			// see if this folder has a special controller
			$vals = safe_array($soc->meta("fld_controllers"));
			$so = obj(aw_global_get("section"));
			enter_function("shop_order_center::show_items::path");
			$path = $so->path();

			foreach($path as $po)
			{
				$po_id = $po->id();
				if (!empty($vals[$po_id]))
				{
					$ctr = $vals[$po_id];
				}
			}
			exit_function("shop_order_center::show_items::path");

			if (is_oid($ctr) && $this->can("view", $ctr))
			{
				enter_function("shop_order_center::show_items::controller");
				$fc = get_instance(CL_FORM_CONTROLLER);
				$html = $fc->eval_controller($ctr, array(
					"soc" => $soc
				));
				exit_function("shop_order_center::show_items::controller");
				exit_function("shop_order_center::show_items");
				return $html;
			}
		}

		$wh_id = $soc->prop("warehouse");

		$wh = get_instance(CL_SHOP_WAREHOUSE);

		// also show docs
		$ss = get_instance("contentmgmt/site_show");
		$tmp = array();
		$ss->_init_path_vars($tmp);
		$html = $ss->show_documents($tmp);

		$so = obj(aw_global_get("section"));
		if ($so->class_id() == CL_SHOP_PRODUCT)
		{
			$pl = array($so);
		}
		else
		if ($soc->prop("prods_are_folders"))
		{
			$pl = array();
		}
		else
		{
			$pl = $wh->get_packet_list(array(
				"id" => $wh_id,
				"parent" => aw_global_get("section"),
				"only_active" => $soc->prop("only_active_items")
			));
		}
		if (!empty($arr["show_prod"]) && $this->can("view", $arr["show_prod"]))
		{
			$pl = array(obj($arr["show_prod"]));
		}

		if (isset($arr["f"]) && is_array($arr["f"]))
		{
			$this->do_filter_packet_list($pl, $arr["f"], $soc);
		}

		$this->do_sort_packet_list($pl, $soc->meta("itemsorts"));

		$section = aw_global_get("section");
		// get the template for products for this folder
		$layout = $this->get_prod_layout_for_folder($soc, $section);

		// get the table layout for this folder
		$t_layout = $this->get_prod_table_layout_for_folder($soc, $section);

		$html .= $this->do_draw_prods_with_layout(array(
			"t_layout" => $t_layout,
			"layout" => $layout,
			"pl" =>  $pl,
			"soc" => $soc
		));

		if ($soc->prop("disp_cart_in_web"))
		{
			$cart = get_instance(CL_SHOP_ORDER_CART);
			$html .= $cart->pre_finish_order(array(
				"oc" => $soc->id(),
				"section" => aw_global_get("section")
			));
		}

		exit_function("shop_order_center::show_items");
		return $html;
	}

	function get_prod_layout_for_folder($soc, $section)
	{
		if(!$section)
		{
			return false;
		}
		$il = $soc->meta("itemlayouts");
		$_p = obj($section);
		foreach(array_reverse($_p->path()) as $p)
		{
			if (!empty($il[$p->id()]))
			{
				return obj($il[$p->id()]);
			}
		}
		return false;
	}

	function get_prod_table_layout_for_folder($soc, $section)
	{
		$il = $soc->meta("tblayouts");
		if(!$section)
		{
			return false;
		}
		$_p = obj($section);
		foreach(array_reverse($_p->path()) as $p)
		{
			if (!empty($il[$p->id()]))
			{
				return obj($il[$p->id()]);
			}
		}
		return false;
	}

	/** returns the html for the products given

		@comment

			params:
				$t_layout - table layout to use
				$layout - product layout to use
				$pl - array of product object instances
				$total_count - number of total products
				$pl_on_page - list ofprods on the current page - if set, $pl is ignored
	**/
	function do_draw_prods_with_layout($arr)
	{
		extract($arr);
		$soce = aw_global_get("soc_err");
//arr($_SESSION["soc_err"]);
		error::raise_if(!is_object($t_layout), array(
			"id" => "ERR_NO_LAYOUT",
			"msg" => "do_draw_prods_with_layout(): layout not set!"
		));
		$tl_inst = $t_layout->instance();
		$tl_inst->start_table($t_layout, $soc);
		if(!empty($this->web_discount))
		{
			$tl_inst->web_discount = $this->web_discount;
		}
		$xi = 0;
		$l_inst = $layout->instance();
		$l_inst->read_template($layout->prop("template"));

		lc_site_load("shop_order_center", &$this);
		$last_menu = "";
		if (isset($arr["pl_on_page"]))
		{
			$tl_inst->cnt = $tl_inst->per_page * (int)$_GET["sptlp"];
			$this->_init_draw_prod();
			foreach($arr["pl_on_page"] as $o)
			{
				$this->_draw_one_prod($o, $tl_inst, $layout, $soc, $l_inst, $soce, ifset($arr, "prod_link_cb"));
			}
			$tl_inst->cnt = $arr["total_count"];
		}
		else
		{
			foreach($pl as $o)
			{
				$tl_inst->cnt++;
				if ($tl_inst->is_on_cur_page())
				{
					$this->_draw_one_prod($o, $tl_inst, $layout, $soc, $l_inst, $soce, ifset($arr, "prod_link_cb"));
					$tl_inst->cnt--;
				}
				$this->last_menu =  $o->parent();
			}
			$tl_inst->cnt = count($pl);
		}
		return $tl_inst->finish_table();
	}

	private function _init_draw_prod()
	{
		$this->xi = 0;
		$this->last_menu = "";
	}

	private function _draw_one_prod($o, $tl_inst, $layout, $soc, $l_inst, $soce, $prod_link_cb)
	{
		$i = $o->instance();
		$oid = $o->id();
		$tl_inst->add_product($i->do_draw_product(array(
			"bgcolor" => isset($this->xi) && $this->xi % 2 ? "cartbgcolor1" : "cartbgcolor2",
			"prod" => $o,
			"layout" => $layout,
			"oc_obj" => $soc,
			"l_inst" => $l_inst,
			"quantity" => $soce[$oid]["ordered_num_enter"],
			"is_err" => $soce[$oid]["is_err"],
			"prod_link_cb" => $prod_link_cb,
			"last_product_menu" => isset($this->last_menu) ? $this->last_menu : NULL,
			"soce" => $soce,
		)));
		$this->xi++;
		$this->last_menu =  $o->parent();
	}

	/** returns the long layout object for the product, based on the view given in the url

		@comment
			$soc - order center object
			$prod - product object
	**/
	function get_long_layout_for_prod($arr)
	{
		extract($arr);
		if ($GLOBALS["view"] == 2)
		{
			$il = $soc->meta("itemlayouts_long_2");
		}
		else
		{
			$il = $soc->meta("itemlayouts_long");
		}
		foreach(array_reverse($prod->path()) as $p)
		{
			if ($il[$p->id()])
			{
				return obj($il[$p->id()]);
			}
		}
		return false;
	}

	/** shows the user a list of his/her previous orders

		@attrib name=my_orders is_public=1 caption="Minu tellimused"

	**/
	function my_orders($arr)
	{
		extract($arr);

		// get current person and get the orders from that
		$u = get_instance(CL_USER);
		$p = obj($u->get_current_person());
		$this->read_template("orders.tpl");
		lc_site_load("shop_order_center", &$this);
		if($ord = $p->get_first_obj_by_reltype("RELTYPE_ORDER"))
		{
			$center = $ord->get_first_obj_by_reltype("RELTYPE_ORDER_CENTER");
			$unconfed = $center->prop("show_unconfirmed");
		}
		foreach($p->connections_from(array("type" => "RELTYPE_ORDER")) as $c)
		{
			$ord = $c->to();
			$ord_item_data = safe_array($ord->meta('ord_item_data'));
			$read_price_total = 0;
			foreach($ord_item_data as $_prod_id => $inf)
			{
				foreach($inf as $num => $dat)
				{
					$read_price_total += str_replace(",", "", $dat["read_price"]);
				}
			}

			if($unconfed == 1 && $confirmed = $ord->prop("confirmed") == 1)
			{
				continue;
			}
			$this->vars_safe(array(
				"name" => $ord->name(),
				"tm" => $ord->created(),
				"sum" => number_format($ord->prop("sum"), 2),
				"order_data_read_price_total" => number_format($read_price_total,2),
				"view_link" => obj_link($ord->id()),
				"id" => $ord->id()
			));
			$l .= $this->parse("LINE");
		}

		$ord_ids = array();
		foreach($p->connections_to(array("from.class_id" => CL_ORDERS_ORDER)) as $c)
		{
			$ord = $c->from();
			if ($ord->prop("order_confirmed") == 1)
			{
				continue;
			}
			$ord_ids[] = $ord->id();
		}

		if (!count($ord_ids))
		{
			$ool = new object_list();
		}
		else
		{
			$ool = new object_list(array(
				"class_id" => CL_ORDERS_ORDER,
				"oid" => $ord_ids,
				"sort_by" => "objects.created"
			));
		}

		lc_site_load("shop_order_center", &$this);

		foreach($ool->arr() as $ord)
		{
			$this->vars_safe(array(
				"name" => $ord->name(),
				"tm" => $ord->created(),
				"sum" => number_format($ord->prop("sum"), 2),
				"view_link" => obj_link($ord->id()),
				"id" => $ord->id()
			));
			$l .= $this->parse("LINE2");
		}


		$this->vars_safe(array(
			"LINE" => $l,
			"reforb" => $this->mk_reforb("submit_my_orders")
		));

		return $this->parse();
	}

	/**

		@attrib name=submit_my_orders

	**/
	function submit_my_orders($arr)
	{
		extract($arr);
		$ord_i = get_instance(CL_SHOP_ORDER);
		$warehouse = 0;
		$items = array();
		if (is_array($sel) && count($sel) > 0 && !empty($makenew))
		{
			// create new order based on the selected orders
			$first = true;
			foreach($sel as $ordid)
			{
				$ord = obj($ordid);
				if ($first)
				{
					// get order center
					$oc = $ord->prop("oc");
				}

				// get all items from order
				foreach($ord_i->get_items_from_order($ord) as $i_id => $quant)
				{
					$items[$i_id] += $quant;
				}
				$first = false;
			}

			// must not create a real order, just stuff the items in the session
			$soc = get_instance(CL_SHOP_ORDER_CART);
			$soc->start_order();
			foreach($items as $iid => $q)
			{
				$soc->add_item($iid, $q);
			}
			return $this->mk_my_orb("show_cart" , array("oc" => $oc), CL_SHOP_ORDER_CART);
		}

		return $this->mk_my_orb("my_orders");
	}

	function do_sort_packet_list(&$pl, $itemsorts,$groups=null)
	{
		if (!is_array($itemsorts))
		{
			return;
		}
		$this->__is = $itemsorts;
		if($groups=="parent")
		{
			$items = array();
			$result = array();
			$menu = null;
			foreach($pl as $key => $item)
			{
				if($item->parent() != $menu)
				{
					if(sizeof($items))
					{
						usort($items, array(&$this, "__is_sorter"));
						$result = array_merge($result ,$items);
						$items = array($key => $item);
						$menu = $item->parent();
						continue;
					}
				}
				$menu = $item->parent();
				$items[$key] = $item;
			}
			if(sizeof($items))
			{
				usort($items, array(&$this, "__is_sorter"));
				$result = array_merge($result,$items);
			}
			$pl = $result;
			return ;
		}
		usort($pl, array(&$this, "__is_sorter"));
	}

	function __is_sorter($a, $b)
	{
		$comp_a = NULL;
		$comp_b = NULL;
		// find the first non-matching element
		foreach($this->__is as $isd)
		{
			if ($isd["element"] == "jrk")
			{
				$comp_a = $a->ord();
				$comp_b = $b->ord();
			}
			else
			{
				$comp_a = $a->prop($isd["element"]);
				$comp_b = $b->prop($isd["element"]);
			}

			$ord = $isd["ord"];
			if ($comp_a != $comp_b)
			{
				break;
			}
		}
		// sort by that element
		if ($comp_a  == $comp_b)
		{
			return 0;
		}

		if ($ord == "asc")
		{
			return $comp_a > $comp_b ? 1 : -1;
		}
		else
		{
			return $comp_a > $comp_b ? -1 : 1;
		}
	}

	function get_properties_from_data_form($oc, $cud = array())
	{
		$ret = array();

		// get data form from that
		if (!$oc->prop("data_form"))
		{
			return $ret;
		}

		// get props from conf form
		if (!$this->can("view", $oc->prop("data_form")))
		{
			return $ret;
		}
		$cff = obj($oc->prop("data_form"));
		$class_id = $cff->prop("ctype");
		if (!$class_id)
		{
			return $ret;
		}
		$class_i = get_instance($class_id == CL_DOCUMENT ? "doc" : $class_id);

		$cf_ps = $class_i->load_from_storage(array(
			"id" => $cff->id()
		));

		$v_ctrs = safe_array($cff->meta("view_controllers"));

		// get all props
		$cfgx = get_instance("cfg/cfgutils");
		$all_ps = $cfgx->load_properties(array(
			"clid" => $class_id,
		));
		$tmp_obj = obj();
		$tmp_obj->set_class_id($class_id);

		$class_i->cfgform_id = $cff->id();
		$all_ps = $class_i->parse_properties(array(
			"properties" => &$all_ps,
			"obj_inst" => $tmp_obj
		));

		$ps_pmap = safe_array($oc->meta("ps_pmap"));
		$org_pmap = safe_array($oc->meta("org_pmap"));

		$u_i = get_instance(CL_USER);
		$cur_p_id = $u_i->get_current_person();
		$cur_p = obj();
		if (is_oid($cur_p_id) && $this->can("view", $cur_p_id))
		{
			$cur_p = obj($cur_p_id);
		}

		$cur_co_id = $u_i->get_current_company();
		$cur_co = obj();
		if (is_oid($cur_co_id) && $this->can("view", $cur_co_id))
		{
			$cur_co = obj($cur_co_id);
		}

		$cart = get_instance(CL_SHOP_ORDER_CART)->get_cart($oc);

		$override_params = array("rows" , "cols" , "caption");
		// rewrite names as user_data[prop]
		foreach($cf_ps as $pn => $pd)
		{
			if ($pn == "is_translated" || $pn == "needs_translation")
			{
				continue;
			}
			$ret[$pn] = $all_ps[$pn];
			foreach($override_params as $override_param)
			{
				if(!empty($pd[$override_param]))
				{
					$ret[$pn][$override_param] = $pd[$override_param];
				}
			}

			$ret[$pn]["name"] = "user_data[$pn]";

			if (aw_global_get("uid") != "")
			{
				if (($fld = array_search($pn, $ps_pmap)))
				{
					$cud[$pn] = $cur_p->prop($fld);
				}

				if (($fld = array_search($pn, $org_pmap)))
				{
					$cud[$pn] = $cur_co->prop($fld);
				}
			}

			if (!empty($cart["user_data"][$pn]))
			{
				$ret[$pn]["value"] = $cart["user_data"][$pn];
			}
			else
			if ($ret[$pn]["type"] == "date_select")
			{
				$ret[$pn]["value"] = date_edit::get_timestamp($cud[$pn]);
			}
			else
			{
				$ret[$pn]["value"] = $cud[$pn];
			}

			$ret[$pn]["view_controllers"] = $v_ctrs[$pn];
		}

		return $ret;
	}

	function _init_fieldm_t(&$t, $props)
	{
		$t->define_field(array(
			"name" => "desc",
			"caption" => t("&nbsp")
		));

		// now, for each property in the selected form do a column
		foreach($props as $pn => $pd)
		{
			$t->define_field(array(
				"name" => "f_".$pn,
				"caption" => $pd["caption"],
				"align" => "center"
			));
		}

		$t->define_field(array(
			"name" => "empty",
			"caption" => t("Vali t&uuml;hjaks"),
			"align" => "center"
		));
	}

	function do_psfieldmap($arr)
	{
		// get props from cfgform
		$dat = $this->get_props_from_cfgform($arr);
		if ($dat == PROP_ERROR)
		{
			return $dat;
		}

		$t =&$arr["prop"]["vcl_inst"];
		$this->_init_fieldm_t($t, $dat);

		// now, insert rows for the person object
		$cu = get_instance("cfg/cfgutils");
		$props = $cu->load_properties(array(
			"clid" => CL_CRM_PERSON
		));
		uasort($props, create_function('$a,$b', 'return strcasecmp($a["caption"], $b["caption"]);'));

		$pmap = $arr["obj_inst"]->meta("ps_pmap");

		foreach($props as $pn => $pd)
		{
			$row = array(
				"desc" => $pd["caption"]." [$pn]",
				"empty" => html::radiobutton(array(
					"name" => "pmap[$pn]",
					"value" => "",
				))
			);

			foreach($dat as $dpn => $dpd)
			{
				$row["f_".$dpn] = html::radiobutton(array(
					"name" => "pmap[$pn]",
					"value" => $dpn,
					"checked" => checked($pmap[$pn] == $dpn)
				));
			}

			$t->define_data($row);
		}

		$t->set_sortable(false);
		return PROP_OK;
	}

	function do_orgfieldmap($arr)
	{
		// get props from cfgform
		$dat = $this->get_props_from_cfgform($arr);
		if ($dat == PROP_ERROR)
		{
			return $dat;
		}

		$t =&$arr["prop"]["vcl_inst"];
		$this->_init_fieldm_t($t, $dat);

		// now, insert rows for the person object
		$cu = get_instance("cfg/cfgutils");
		$props = $cu->load_properties(array(
			"clid" => CL_CRM_COMPANY
		));
		uasort($props, create_function('$a,$b', 'return strcasecmp($a["caption"], $b["caption"]);'));

		$pmap = $arr["obj_inst"]->meta("org_pmap");

		foreach($props as $pn => $pd)
		{
			$row = array(
				"desc" => $pd["caption"],
				"empty" => html::radiobutton(array(
					"name" => "pmap[$pn]",
					"value" => "",
				))
			);

			foreach($dat as $dpn => $dpd)
			{
				$row["f_".$dpn] = html::radiobutton(array(
					"name" => "pmap[$pn]",
					"value" => $dpn,
					"checked" => checked($pmap[$pn] == $dpn)
				));
			}

			$t->define_data($row);
		}

		$t->set_sortable(false);
		return PROP_OK;
	}

	function get_props_from_cfgform($arr)
	{
		if (!is_oid($arr["obj_inst"]->prop("data_form")) || !$this->can("view", $arr["obj_inst"]->prop("data_form")))
		{
			$arr["prop"]["error"] = t("Tellija andmete vorm valimata!");
			return PROP_ERROR;
		}

		$cff = get_instance(CL_CFGFORM);
		$ret =  $cff->get_props_from_cfgform(array(
			"id" => $arr["obj_inst"]->prop("data_form")
		));
		return $ret;
	}

	function get_property_map($oc_id, $type)
	{
		$o = obj($oc_id);
		switch($type)
		{
			case "person":
				return array_flip(safe_array($o->meta("ps_pmap")));
				break;

			case "org":
				return array_flip(safe_array($o->meta("org_pmap")));
				break;

			default:
				error::raise(array(
					"id" => "ERR_WRONG_MAP",
					"msg" => sprintf(t("shop_order_center::get_property_map(%s, %s): the options for type are 'person' and 'org'"), $oc_id, $type)
				));
		}
	}

	function get_discount_from_order_data($oc_id, $data)
	{
		$oc = obj($oc_id);
		if (!$oc->prop("data_form_discount"))
		{
			return 0;
		}
		return $data[$oc->prop("data_form_discount")];
	}

	function callback_get_controller_tbl($arr)
	{
		if (!$arr["obj_inst"]->prop("use_controller"))
		{
			return array();
		}
		$ret = array();
		$ot = $this->_get_folder_ot_from_o($arr["obj_inst"]);
		$ol = $ot->to_list();

		$opts = new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_CONTROLLER")));
		$opts = array("" => t("--vali--")) + $opts->names();
		$vals = $arr["obj_inst"]->meta("fld_controllers");
		foreach($ol->arr() as $o)
		{
			$nm = "fld_".$o->id();
			$ret[$nm] = array(
				"name" => $nm,
				"type" => "select",
				"options" => $opts,
				"value" => $vals[$o->id()],
				"caption" => $o->path_str()
			);
		}
		return $ret;
	}

	public function _get_payment_types_tlb($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->add_new_button(array(CL_SHOP_PAYMENT_TYPE), $arr["obj_inst"]->id());
		$t->add_delete_button();
		$t->add_save_button();
	}

	public function _get_payment_types_tbl($arr)
	{
		$ol = $arr["obj_inst"]->get_payment_types();

		$t = $arr["prop"]["vcl_inst"];
		$t->table_from_ol(
			$ol,
			array("name"),
			CL_SHOP_PAYMENT_TYPE
		);
	}

	function _get_folder_ot_from_o($o)
	{
		if (!$o->prop("warehouse"))
		{
			return;
		}
		$wh = obj($o->prop("warehouse"));

		if (!$wh->prop("conf"))
		{
			return;
		}
		$conf = obj($wh->prop("conf"));

		if (!$conf->prop("pkt_fld"))
		{
			return;
		}

		$ot = new object_tree(array(
			"parent" => $conf->prop("pkt_fld"),
			"class_id" => CL_MENU,
		));
		return $ot;
	}

	function save_ctr_t($arr)
	{
		$vals = array();
		foreach(safe_array($arr["request"]) as $k => $v)
		{
			if (substr($k, 0, 4) == 'fld_')
			{
				$vals[substr($k, 4)] = $v;
			}
		}
		$arr["obj_inst"]->set_meta("fld_controllers", $vals);
	}

	function _get_integration_class($arr)
	{
		$arr["prop"]["options"] = array("" => t("--vali--"));
		$clss = aw_ini_get("classes");
		foreach(class_index::get_classes_by_interface("shop_order_center_integrator") as $class_name)
		{
			$clid = clid_for_name($class_name);
			$arr["prop"]["options"][$clid] = $clss[$clid]["name"];
		}

		foreach($clss as $clid => $clinf)
		{
			if (isset($clinf["site_class"]) && $clinf["site_class"] == 1)
			{
				// check if site class implements interface
				$anal = get_instance("aw_code_analyzer");
				$data = $anal->analyze_file(aw_ini_get("site_basedir")."/classes/".$clinf["file"].".".aw_ini_get("ext"), true);
				foreach($data["classes"] as $class_name => $class_data)
				{
					if (in_array("shop_order_center_integrator", $class_data["implements"]))
					{
						$arr["prop"]["options"][$clid] = $clinf["name"];
					}
				}
			}
		}
	}

	function _get_filter_fields_class($arr)
	{
		if (!is_class_id($ic = $arr["obj_inst"]->prop("integration_class")))
		{
			return PROP_IGNORE;
		}
		$t = $arr["prop"]["vcl_inst"];

		$class_filter_fields = $arr["obj_inst"]->meta("class_filter_fields");

		$this->_init_filter_fields_table($t);
		$clss = aw_ini_get("classes");
		foreach(get_instance($clss[$ic]["file"])->get_filterable_fields() as $field_name => $field_caption)
		{
			$t->define_data(array(
				"field" => $field_caption,
				"select" => html::checkbox(array(
					"name" => "class_filter_select[$field_name]",
					"value" => 1,
					"checked" => $class_filter_fields[$field_name] == 1
				))
			));
		}
		$t->set_caption(t("Filtrid integratiooniklassist"));
	}

	function _set_filter_fields_class($arr)
	{
		$arr["obj_inst"]->set_meta("class_filter_fields", $arr["request"]["class_filter_select"]);
	}

	function _get_filter_fields_props($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$prop_filter_fields = $arr["obj_inst"]->meta("prop_filter_fields");

		$this->_init_filter_fields_table($t);
		foreach(obj()->set_class_id(CL_SHOP_PRODUCT)->get_property_list() as $field_name => $field_data)
		{
			$t->define_data(array(
				"field" => ifset($field_data, "caption")." [$field_name]",
				"select" => html::checkbox(array(
					"name" => "prop_filter_select[$field_name]",
					"value" => 1,
					"checked" => $prop_filter_fields[$field_name] == 1
				))
			));
		}
		$t->set_caption(t("Filtrid toote omadustest"));
	}

	function _set_filter_fields_props($arr)
	{
		$arr["obj_inst"]->set_meta("prop_filter_fields", $arr["request"]["prop_filter_select"]);
	}

	private function _init_filter_fields_table($t)
	{
		$t->define_field(array(
			"name" => "field",
			"caption" => t("V&auml;li"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "select",
			"caption" => t("Vali"),
			"align" => "center"
		));
	}

	function _get_filter_settings($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->table_from_ol(
			new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_FILTER"))),
			array("name"),
			CL_SHOP_ORDER_CENTER_FILTER_ENTRY
		);
	}

	function _get_filter_settings_tb($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->add_new_button(array(CL_SHOP_ORDER_CENTER_FILTER_ENTRY), $arr["obj_inst"]->id(),12, array("set_oc" => $arr["obj_inst"]->id()));
		$t->add_delete_button();
	}

	private function _init_filter_sel_table($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Kataloog")
		));

		$t->define_field(array(
			"name" => "sel_filter",
			"caption" => t("Kehtiv filter"),
			"align" => "center"
		));
	}

	function _set_filter_sel_for_folders($arr)
	{
		$arr["obj_inst"]->filter_set_active_by_folder($arr["request"]["sel_filter"]);
	}

	function _get_filter_sel_for_folders($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_filter_sel_table($t);

		$wh = get_instance(CL_SHOP_WAREHOUSE);

		$o = $arr["obj_inst"];
		$this->_get_folder_ot_from_o($o);

		$ol = new object_list($o->connections_from(array("type" => "RELTYPE_FILTER")));
		$this->filter_sel = array("" => t("--vali--")) + $ol->names();
		$this->selected_filters = $o->meta("filter_by_folder");

		$this->_oinst = &$o;

		if (!$o->prop("warehouse"))
		{
			return new object_list();
		}
		$wh = obj($o->prop("warehouse"));

		if (!$wh->prop("conf"))
		{
			return new object_list();
		}
		$conf = obj($wh->prop("conf"));
		$o = obj($conf->prop("pkt_fld"));
		$this->filter_table_ot_cb($o, $t);

		$ot = new object_tree(array(
			"class_id" => CL_MENU,
			"parent" => $o->id(),
		));

		$ot->foreach_cb(array(
			"func" => array(&$this, "filter_table_ot_cb"),
			"param" => &$t,
			"save" => false
		));

		$t->sort_by();
	}

	function filter_table_ot_cb(&$o, &$t)
	{
		$t->define_data(array(
			"name" => $o->path_str(),
			"sel_filter" => html::select(array(
				"name" => "sel_filter[".$o->id()."]",
				"options" => $this->filter_sel,
				"selected" => $this->selected_filters[$o->id()]
			)),
		));
		$t->set_default_sortby("name");
	}

	function do_filter_packet_list(&$pl, $f, $soc)
	{
//die(dbg::dump($f));
		$filter_ic = array();
		$filter_prod = array();
		foreach($f as $filter_name => $filter_value)
		{
			if (!is_array($filter_value) || !count($filter_value))
			{
				continue;
			}

			list($type, $name) = explode("::", $filter_name);
			if ($type == "ic")
			{
				$filter_ic[$name] = $filter_value;
			}
			else
			{
				$filter_prod[$name] = $filter_value;
			}
		}
		if (count($filter_ic) && count($pl))
		{
			$inst = $soc->get_integration_class_instance();
			$inst->apply_filter_to_product_list($pl, $filter_ic);
		}
		if (count($filter_prod) && count($pl))
		{
			$this->apply_filter_to_product_list($pl, $filter_prod);
		}
	}

	function apply_filter_to_product_list(&$pl, $filter_prod)
	{
		enter_function("shop_product::apply_filter_to_product_list");
		$filt = array(
			"oid" => $pl,
			"lang_id" => array(),
			"site_id" => array(),
			"class_id" => CL_SHOP_PRODUCT
		);
		foreach($filter_prod as $prop => $vals)
		{
			$filt[$prop] = array_keys($vals);
		}
		$ol = new object_list($filt);
		$pl = $this->make_keys($ol->ids());
		exit_function("shop_product::apply_filter_to_product_list");
	}
//------------------ siit piitist peaks ok olema... enne seda on l2bu
	function do_db_upgrade($t, $f)
	{
		switch($f)
		{
			case "aw_default_currency":
			case "aw_root_menu":
			case "aw_per_page":
			case "not_available_purveyance":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
		return false;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function _get_appearance_tree($arr)
	{
		$roots = $arr["obj_inst"]->prop("root_menu");
		$tv =& $arr["prop"]["vcl_inst"];
		$var = "menu";
		$tv->set_selected_item(isset($arr["request"][$var]) ? $arr["request"][$var] : reset($roots));

		if(empty($cls))
		{
			$cls = "";
		}
		$gbf = $this->mk_my_orb("add_appearance_leaf",array(
			"tree_type" => "storage",
			"cls" => $cls,
			"parent" => " ",
		), CL_SHOP_ORDER_CENTER);

		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"tree_id" => "appearance_tree",
			"persist_state" => 1,
			"get_branch_func" => $gbf,
			"has_root" => true,
			"root_name" => sizeof($roots) ? "" : "<font color=red>" . t("Toodete kuvamise juurkaust valimata") . "</font>",
			"root_url" => "javascript:;",
		));

		foreach($roots as $root)
		{
			$root_object = obj($root);

			$tv->add_item(0,array(
				"name" => $root_object->name(),//t("K&otilde;ik tooted"),
				"id" => $root,
				"reload" => array(
					"props" => array("appearance_list"),
					"params" => array($var => $root)
				)
			));

			$menus = new object_list(array(
				"class_id" => CL_MENU,
				"parent" => $root,
				"sort_by" => "jrk asc, name asc",
			));

			foreach($menus->names() as $id => $name)
			{
				$tv->add_item($root, array(
					"id" => $id,
					"name" => $name,
					"iconurl" => icons::get_icon_url(CL_MENU),
					"reload" => array(
						"props" => array("appearance_list"),
						"params" => array($var => $id)
					)
				));
				$groups = new object_list(array(
					"class_id" => array(CL_MENU),
					"parent" => $id,
					"limit" => 1,
				));
				if($groups->count())
				{
					$tv->add_item($id, array(
						"id" => $id."_".$id,
						"name" => $id."_".$id,
// 						"iconurl" => icons::get_icon_url(CL_MENU),
					));
				}
//			$this->add_appearance_leaf($tv , $id);
			}
		}//arr(count($tv->get_item_ids()));die();
	}

	/**
		@attrib name=add_appearance_leaf all_args=1
	**/
	function add_appearance_leaf($arr)
	{
		parse_str($_SERVER['QUERY_STRING'], $arr);
		$tv = get_instance("vcl/treeview");
		$parent = trim($arr["parent"]);
		$tv->start_tree(array (
			"type" => treeview::TYPE_DHTML,
			"branch" => 1,
			"tree_id" => "appearance_tree_".$arr["parent"],
			"persist_state" => 1,
		));
		$tv ->set_rootnode($parent);
		$groups = new object_list(array(
			"class_id" => array(CL_MENU),
			"parent" => $parent,
			"sort_by" => "jrk asc",
		));

		foreach($groups->names() as $id => $name)
		{
			$tv->add_item($parent, array(
				"id" => $id,
				"name" => $name,
				"iconurl" => icons::get_icon_url(CL_MENU),
				"reload" => array(
					"props" => array("appearance_list"),
					"params" => array("menu" => $id)
				)
			));

			$groups = new object_list(array(
				"class_id" => array(CL_MENU),
				"parent" => $id,
				"limit" => 1,
				"sort_by" => "jrk asc, name asc",
			));

			if($groups->count())
			{
				$tv->add_item($id, array(
					"id" => $id."_".$id,
					"name" => $id."_".$id,
// 					"iconurl" => icons::get_icon_url(CL_MENU),
				));
			}
		}
//		$tv->set_selected_item(trim(automatweb::$request->arg("menu")));

		die($tv->finalize_tree());

	}

	function _get_appearance_list($arr)
	{
		if (empty($imgbase))
		{
			$imgbase = "/automatweb/images/icons";
		};

		$this->imgbase = $this->cfg["baseurl"] . $imgbase;

		$t = $arr["prop"]["vcl_inst"];

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Dokument"),
			"align" => "left",
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"name" => "cat",
			"caption" => t("Kategooriad"),
			"align" => "left",
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"name" => "cat_search",
			"align" => "left",
			"parent" => "cat",
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"name" => "categories",
			"align" => "right",
			"parent" => "cat",
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"name" => "types",
			"caption" => t("T&uuml;&uuml;bid"),
			"align" => "left",
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"name" => "template",
			"caption" => t("Templeit"),
			"align" => "left",
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"name" => "product_template",
			"caption" => t("&Uuml;he toote templeit"),
			"align" => "left",
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"name" => "open",
			"caption" => t("Ava"),
			"align" => "left",
			"chgbgcolor" => "color",
		));

		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
			"chgbgcolor" => "color",
		));

		$show_inst = get_instance(CL_PRODUCTS_SHOW);
		$ps = get_instance("vcl/popup_search");
		$ps->set_class_id(array(CL_SHOP_PRODUCT_CATEGORY));
		$ps->set_reload_layout("appearance_r");
		$ps->set_property("categories");
		$t->set_sortable(false);
		if(!empty($arr["request"]["menu"]))
		{
			$ol = new object_list(array(
				"class_id" => CL_MENU,
				"parent" => $arr["request"]["menu"],
				"sort_by" => "jrk asc, name asc",
			));
			foreach($ol->arr() as $id => $menu)
			{
				$data = array(
					"name" => $menu->name(),
					"oid" => $id
				);
				$data["color"] = $menu->prop("status") == 2 ? "#99FF99" : "silver";
				$o = $arr["obj_inst"]->get_product_show_obj($id);
				if(is_object($o))
				{
					if($o->prop("type"))
					{
						$data["types"] = $show_inst->types[$o->prop("type")];
					}
					if($o->prop("template"))
					{
						$data["template"] = $o->prop("template");
					}
					if($o->prop("product_template"))
					{
						$data["product_template"] = $o->prop("product_template");
					}
					$cats = array();
					foreach($o->get_categories()->arr() as $cat)
					{
						$cats[] = html::obj_change_url($cat) . " " . html::href(array(
							"url" => "javascript:;",
							"onclick" => 'remove_category("'.$o->id().'" , "'.$cat->id().'");',
							"caption" => html::img(array("url" => $this->imgbase."/delete.gif")),
						));
					}
					if($document = $o->get_document())
					{
						$data["name"] = html::href(array(
							"url" =>  $this->mk_my_orb("change", array("id" => $document), CL_DOCUMENT),
							"caption" => $menu->name(),
						));
					}

					$data["categories"] = join(",\n<br>" , $cats);
					$ps->set_id($o->id());
					$data["cat_search"] = $ps->get_search_button();
				}

/*				$data["cat_search"] = html::href(array(
					"url" => "javascript:;",
					"onclick" => 'win = window.open("'.$this->mk_my_orb("search_categories", array("is_popup" => 1), CL_SHOP_ORDER_CENTER).'&menu='.$id.'" ,"categoty_search","width=720,height=600,statusbar=yes, scrollbars=yes");',
					"caption" => html::img(array("url" => $this->imgbase."/search.gif")),
				));*/
				$data["open"] = html::href(array(
					"url" => $this->mk_my_orb("redir", array("parent" => $id), CL_ADMIN_IF),
					"caption" => t("Ava")
				));
				$t->define_data($data);
			}
		}
	}

	public function callback_generate_scripts($arr)
	{
		$js = "";
		if(in_array(automatweb::$request->arg("group"), array("appear", "appearance")))
		{
			$js.= "
				function set_sel_prop(property , value)
				{
					result = $('input[name^=sel]');
					$.get('/automatweb/orb.aw?class=shop_order_center&id=".$arr["obj_inst"]->id()."&action=ajax_set_product_show_property&' + property + '=' + value + '&' + result.serialize(), {
						}, function (html) {
							reload_property('appearance_list');
						}
					);
				}
			";
			$js.= "
				function remove_category(show_object,category)
				{
					$.get('/automatweb/orb.aw?class=shop_order_center&action=ajax_remove_category&category=' + category + '&show_object=' + show_object, {
						}, function (html) {
							reload_property('appearance_list');
						}
					);
				}
			";

			$js.= "
				function make_all_not_active()
				{
					$.get('/automatweb/orb.aw?class=shop_order_center&action=make_all_not_active&id=".$arr["obj_inst"]->id()."', {
						}, function (html) {
							reload_property('appearance_list');
						}
					);
				}
			";
			$js.= "
				function make_new_struct()
				{
					var ansa = confirm('" . t("Kataloogistruktuuri ehitamine l6hub seni toiminud toodete n2itamise seaded. Oled kindel et luua uus struktuur?") . "');
					if (ansa)
					{
						alert('kui sa nyyd OK vajutad siis k6ik on kadunud....ja peab ametit vahetama...');
						$.get('/automatweb/orb.aw?class=shop_order_center&action=make_new_struct&id=".$arr["obj_inst"]->id()."', {
							}, function (html) {
								reload_property('appearance_list');
							}
						);
					}
					else
					{
						alert('vuss!!!');
					}
				}
			";
		}
		return $js;
	}

	function _get_appearance_toolbar($arr)
	{
		$tb = &$arr["prop"]["vcl_inst"];

		$roots = $arr["obj_inst"]->prop("root_menu");

 		$tb->add_js_new_button(array(
 			"parent_var" => "menu",
			"parent" => is_array($roots) ? reset($roots) : "",
 			"clid" => CL_MENU,
 			"refresh" => array("appearance_list"),
	//		"refresh_layout" => array("appearance_c"),
 			"promts" => array("name" => t("Sisesta uue kausta nimi")),
 		));

		$tb->add_delete_button();

		$tb->add_menu_button(array(
			"name" => "active",
//			"img" => "delete.gif",
			"text" => t("Aktiivsus"),
			"tooltip" => t("Tee kaustu aktiivseteks ja mitteaktiivseteks"),
		));

		$tb->add_menu_item(array(
			"parent" => "active",
			"text" => t("Aktiivseks"),
			"link" => "javascript:set_sel_prop('active' , '2');",
		));

		$tb->add_menu_item(array(
			"parent" => "active",
			"text" => t("Mitteaktiivseks"),
			"link" => "javascript:set_sel_prop('active' , '1');",
		));

		$tb->add_menu_item(array(
			"parent" => "active",
			"text" => t("K&otilde;ik t&uuml;hjad kaustad mitteaktiivseteks"),
			"link" => "javascript:make_all_not_active();",
		));

		$tb->add_menu_button(array(
			"name" => "type",
			"text" => t("T&uuml;&uuml;p"),
			"tooltip" => t("Objektit&uuml;&uuml;p mida tootena kuvatakse"),
		));

		$prod_show = get_instance(CL_PRODUCTS_SHOW);
		foreach($prod_show->types as $key => $name)
		{
			$tb->add_menu_item(array(
				"parent" => "type",
				"text" => $name,
				"link" => "javascript:set_sel_prop('type' , '".$key."');",
			));
		}

		$tb->add_menu_button(array(
			"name" => "template",
			"text" => t("Templeit"),
			"tooltip" => t("M&auml;&auml;ra valitud kaustadele templeit"),
		));

		foreach($prod_show->templates() as $key => $name)
		{
			$tb->add_menu_item(array(
				"parent" => "template",
				"text" => $name,
				"link" => "javascript:set_sel_prop('template' , '".$name."');",
			));
		}


		$tb->add_menu_button(array(
			"name" => "product_template",
			"text" => t("Toote templeit"),
			"tooltip" => t("M&auml;&auml;ra valitud kaustadele &uuml;he toote n&auml;itamiseks templeit"),
		));

		foreach($prod_show->product_templates() as $key => $name)
		{
			$tb->add_menu_item(array(
				"parent" => "product_template",
				"text" => $name,
				"link" => "javascript:set_sel_prop('product_template' , '".$name."');",
			));
		}

		$new_struct_html_url = $this->mk_my_orb("get_make_new_struct_prompt", array("id" => $arr["obj_inst"]->id()));
		$new_struct_submit_url = $this->mk_my_orb("make_new_struct", array("id" => $arr["obj_inst"]->id()));

		$onclick = <<<SCRIPT
$.please_wait_window.show();
$.ajax({
	url: '{$new_struct_html_url}',
	success: function(html){
		$.please_wait_window.hide();
		$.prompt(html, {
			callback: function(v,m){
				if(v){
					$.please_wait_window.show();
					products_shows = {};
					m.find('input[name^=products_show_]:checked').each(function(){
						console.log(this.id);
						products_shows[this.id.substr(14)] = true;
					});
					purveyors_shows = {};
					m.find('input[name^=purveyors_show_]:checked').each(function(){
						console.log(this.id);
						purveyors_shows[this.id.substr(15)] = true;
					});
					products_tpls = {};
					m.find('select[name^=products_tpl_]').each(function(){
						products_tpls[this.id.substr(13)] = $(this).val();
					});
					purveyors_tpls = {};
					m.find('select[name^=purveyors_tpl_]').each(function(){
						purveyors_tpls[this.id.substr(14)] = $(this).val();
					});
					$.ajax({
						url: '{$new_struct_submit_url}',
						data: {
							products_show: products_shows,
							purveyors_show: purveyors_shows,
							products_tpl: products_tpls,
							purveyors_tpl: purveyors_tpls,
							delete_menus_without_category: m.find('#delete_menus_without_category').prop('checked'),
							delete_menus_with_deleted_category: m.find('#delete_menus_with_deleted_category').prop('checked')
						},
						success: function(){
							$.please_wait_window.hide();
							$.prompt('Uus struktuur edukalt loodud!', { buttons: { 'OK': true } });
						}
					});
				}
			},
			buttons: { 'Loo uus struktuur': true, 'Katkesta': false }
		});
	}
});
SCRIPT;

		$tb->add_button(array(
			"name" => "new_struct",
			"text" => t("Loo uus struktuur"),
			"tooltip" => t("Loo uus struktuur"),
			"url" => "javascript:void(0)",
			"onclick" => $onclick,
		));


	}


	/** searches and connects bill row to task row
		@attrib name=search_categories
		@param category optional
			category oid/category type oid
		@param name optional type=string
			category name
		@param menu optional type=int
			menu oid
		@param result optional type=int/array
			result category id

	**//*
	function search_categories($arr)
	{
		$content = "";
		if(is_oid($arr["result"]) || (is_array($arr["result"]) && sizeof($arr["result"])))
		{
			if(is_oid($arr["menu"]))
			{
				$o = $this->get_product_show_obj($arr["menu"], true);
				$o->add_category($arr["result"]);
			}

			die("<script language='javascript'>
				window.opener.reload_property('appearance_list');
				window.close();
			</script>");
		}


		$htmlc = get_instance("cfg/htmlclient");
		$htmlc->start_output();

		$htmlc->add_property(array(
			"name" => "name",
			"type" => "textbox",
			"value" => $arr["name"],
			"caption" => t("Kategooria nimi"),
			"autocomplete_class_id" => array(CL_SHOP_PRODUCT_CATEGORY),
		));
		$htmlc->add_property(array(
			"name" => "oid",
			"type" => "textbox",
			"value" => $arr["oid"],
			"caption" => t("ID"),
		));
		$htmlc->add_property(array(
			"name" => "submit",
			"type" => "submit",
			"value" => t("Otsi"),
			"caption" => t("Otsi")
		));
		$data = array(
//			"oid" => $arr["oid"],
			"menu" => $arr["menu"],
//			"name" => $arr["name"],
			"orb_class" => $_GET["class"]?$_GET["class"]:$_POST["class"],
			"reforb" => 0,
		);

		classload("vcl/table");
		$t = new vcl_table(array(
			"layout" => "generic",
		));
		$t->define_field(array(
			"name" => "choose",
			"caption" => "",
		));
		$t->define_chooser(array(
			"name" => "result",
			"field" => "oid",
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));

		$t->define_field(array(
			"name" => "code",
			"caption" => t("Kood"),
		));

		$filter = array(
			"class_id" => CL_SHOP_PRODUCT_CATEGORY,
			"lang_id" => array(),
-			"site_id" => array(),
		);

		if($arr["name"])
		{
			$filter["name"] = $arr["name"]."%";
		}

		if(sizeof($filter) < 3)
		{
			$ol = new object_list();
		}
		else
		{
			$ol = new object_list($filter);
		}
		foreach($ol->arr() as $o)
		{
			$t->define_data(array(
				"oid" => $o->id(),
				"name" => $o->name(),
				"code" => $o->prop("code"),
				"choose" => html::href(array(
					"caption" => t("Vali see"),
					"url" => $this->mk_my_orb("search_categories",
						array(
							"result" => $o->id(),
							"menu" => $arr["menu"],
						), "shop_order_center"
					),
				)),
			));
		}

		$htmlc->add_property(array(
			"name" => "table",
			"type" => "text",
			"value" => $t->draw(),
			"no_caption" => 1,
		));


		$htmlc->add_property(array(
			"name" => "submit2",
			"type" => "submit",
			"value" => t("Salvesta"),
			"caption" => t("Salvesta")
		));

		$htmlc->finish_output(array(
			"action" => "search_categories",
			"method" => "POST",
			"data" => $data
		));

		$content.= $htmlc->get_result();

		return $content;
	}
*/
	/**
		@attrib name=ajax_remove_category all_args=1
	**/
	public function ajax_remove_category($arr)
	{
		if($this->can("view" , $arr["show_object"]))
		{
			$o = obj($arr["show_object"]);
			$o->remove_category($arr["category"]);
		}
		die();
	}

	/**
		@attrib name=get_make_new_struct_prompt params=name
		@param id required type=int
	**/
	public function get_make_new_struct_prompt($arr)
	{
		$o = obj($arr["id"], array(), shop_order_center_obj::CLID);
		$categories_depth = 3; // TODO: is_oid($o->prop("warehouse")) ? $o->warehouse()->get_categories_depth() : 0;

		$tm = new templatemgr();
		$products_tpl_options = $tm->template_picker(array(
			"empty" => false,
			"folder" => "applications/shop/products_show/"
		));
		$purveyors_tpl_options = $tm->template_picker(array(
			"empty" => false,
			"folder" => "applications/shop/shop_purveyors_webview/"
		));

		$tpl_selectors = "";
		$default_products_show_values = $o->meta("make_new_struct.products_show");
		$default_purveyors_show_values = $o->meta("make_new_struct.purveyors_show");
		$default_products_tpl_values = $o->meta("make_new_struct.products_tpl");
		$default_purveyors_tpl_values = $o->meta("make_new_struct.purveyors_tpl");
		for ($i = 0; $i < $categories_depth; $i++)
		{
			$tpl_selectors .= sprintf(t("%u. tase\n"), $i + 1)
			.html::linebreak()
			.html::checkbox(array(
				"name" => "products_show_{$i}",
				"checked" => !empty($default_products_show_values[$i]),
			))
			.t("Kuva tooteid kujundusega: ")
			.html::select(array(
				"name" => "products_tpl_{$i}",
				"options" => $products_tpl_options,
				"value" => isset($default_products_tpl_values[$i]) ? $default_products_tpl_values[$i] : "show.tpl",
			))
			.html::linebreak()
			.html::checkbox(array(
				"name" => "purveyors_show_{$i}",
				"checked" => !empty($default_purveyors_show_values[$i]),
			))
			.t("Kuva tarnijaid kujudusega: ")
			.html::select(array(
				"name" => "purveyors_tpl_{$i}",
				"options" => $purveyors_tpl_options,
				"value" => isset($default_purveyors_tpl_values[$i]) ? $default_purveyors_tpl_values[$i] : "show.tpl",
			))
			.html::linebreak();
		}
		$tpl_selectors .= html::linebreak();

		$delete_menus_without_category = html::checkbox(array(
			"name" => "delete_menus_without_category",
			"label" => t("Kustuta kaustad, mis ei ole loodud 'Loo uus struktuur' nupu abil"),
			"checked" => $o->meta("make_new_struct.delete_menus_without_category"),
		)).html::linebreak();
		$delete_menus_with_deleted_category = html::checkbox(array(
			"name" => "delete_menus_with_deleted_category",
			"label" => t("Kustuta kaustad, mille kategooria on kustutatud"),
			"checked" => $o->meta("make_new_struct.delete_menus_with_deleted_category"),
		)).html::linebreak();

		$html = <<<HTML
{$tpl_selectors}{$delete_menus_without_category}{$delete_menus_with_deleted_category}
HTML;
		die($html);
	}

	/**
		@attrib name=make_new_struct params=name
		@param id required type=int
			shop id
		@param products_show optional type=array
		@param purveyors_show optional type=array
		@param products_tpl optional type=array
		@param purveyors_tpl optional type=array
		@param delete_menus_without_category optional type=boolean
		@param delete_menus_with_deleted_category optional type=boolean
	**/
	public function make_new_struct($arr)
	{
		var_dump($arr);

		$this->shop = obj($arr["id"]);
		$this->shop->set_meta("make_new_struct.products_show", isset($arr["products_show"]) ? $arr["products_show"] : null);
		$this->shop->set_meta("make_new_struct.purveyors_show", isset($arr["purveyors_show"]) ? $arr["purveyors_show"] : null);
		$this->shop->set_meta("make_new_struct.products_tpl", isset($arr["products_tpl"]) ? $arr["products_tpl"] : null);
		$this->shop->set_meta("make_new_struct.purveyors_tpl", isset($arr["purveyors_tpl"]) ? $arr["purveyors_tpl"] : null);
		$this->shop->set_meta("make_new_struct.delete_menus_without_category", !empty($arr["delete_menus_without_category"]) and $arr["delete_menus_without_category"] !== "false");
		$this->shop->set_meta("make_new_struct.delete_menus_with_deleted_category", !empty($arr["delete_menus_with_deleted_category"]) and $arr["delete_menus_with_deleted_category"] !== "false");
		$this->shop->save();

		$this->shop->make_new_struct();

		die("SUCCESS");
	}

	/**
		@attrib name=ajax_set_product_show_property all_args=1
	**/
	public function ajax_set_product_show_property($arr)
	{
		$shop = obj($arr["id"]);
		foreach($arr["sel"] as $id)
		{
			if($this->can("view" , $id))
			{
				$o = obj($id);arr($o->class_id());
				switch($o->class_id())
				{
					case CL_MENU:
						foreach($arr as $key => $val)
						{
							switch($key)
							{
								case "active":
									$o->set_prop("status" , $val);
									$o->save();
									if($val == 2)
									{
										$ol = new object_list(array(
											"class_id" => CL_DOCUMENT,
											"parent" => $o->id(),
										));
										foreach($ol->arr() as $doc)
										{
											$doc->set_prop("status" , 2);
											$doc->save();
										}
									}

									break;
								case "type":
								case "template":
								case "product_template":
									$show = $shop->get_product_show_obj($o->id() , true);
									$show->set_prop($key ,$val);
									$show->save();
									break;
								default:
									break;
							}
						}
						break;
					case CL_SHOP_SELL_ORDER:
						foreach($arr as $key => $val)
						{
							switch($key)
							{
								case "status":
									$o->set_prop("order_status" , $val);
									$o->save();
									break;
								default:
									break;
							}
						}
						break;
				}
			}
		}
		die();
	}

	/**
		@attrib name=make_all_not_active all_args=1
	**/
	public function make_all_not_active($arr)
	{
		$obj = obj($arr["id"]);
		$obj->make_all_empty_menus_not_active();
		die(1);
	}

}

/** If you want to create a class that can be used in shop order center to filter products and other things, then implement this interface **/
interface shop_order_center_integrator
{
	/** Returns a list of fields that can be used for filtering
		@attrib api=1

		@returns
			array { filter_field => filter field caption, ... }
	**/
	public function get_filterable_fields();

	/** Returns a list of all values for the given filter
		@attrib api=1 params=pos

		@param filter_name required type=string
			The name of the filter field to return values for

		@returns
			array { filter_value => filter_value_caption, ... }
	**/
	public function get_all_filter_values($filter_name);

	/** Applies the given filter to the product list
		@attrib api=1 params=pos

		@param pl required type=array
			Array of produxts to filter { index => product_obj, ... }

		@param filter_prod type=array
			The filter array { filter_name => array { filter_value => 1 }, ... }
	**/
	public function apply_filter_to_product_list(&$pl, $filter_prod);
}
