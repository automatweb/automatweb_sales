<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_SHOP_SALES_MANAGER_WORKSPACE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@default table=objects
@default group=general

@default group=general_general

	@property name type=textbox rel=1 trans=1
	@caption Nimi
	@comment Objekti nimi

	@property comment type=textbox
	@caption Kommentaar
	@comment Vabas vormis tekst objekti kohta

	@property status type=status trans=1 default=1
	@caption Aktiivne
	@comment Kas objekt on aktiivne


@default group=general_settings

	@property warehouses type=relpicker multiple=1 reltype=RELTYPE_WAREHOUSE store=connect
	@caption Laod

	@property show_alt_units type=checkbox ch_value=1 field=meta method=serialize
	@caption Kuva alternatiiv&uuml;hikuid

@default group=products

	@property products_toolbar type=toolbar no_caption=1 store=no

	@layout prod_split type=hbox width=20%:80%

		@layout prod_left type=vbox parent=prod_split

			@layout prod_tree_lay type=vbox closeable=1 area_caption=Toodete&nbsp;puu parent=prod_left
	
				@property prod_tree type=treeview parent=prod_tree_lay store=no no_caption=1

			@layout prod_left_search type=vbox parent=prod_left area_caption=Otsing closeable=1

				@property prod_s_name type=textbox store=no captionside=top size=30 parent=prod_left_search
				@caption Nimi 
				
				@property prod_s_code type=textbox store=no captionside=top size=30 parent=prod_left_search
				@caption Kood
				
				@property prod_s_barcode type=textbox store=no captionside=top size=30 parent=prod_left_search
				@caption Ribakood
				
				@property prod_s_cat type=textbox store=no captionside=top size=30 parent=prod_left_search
				@caption Kategooria
				
				@property prod_s_count type=select store=no captionside=top parent=prod_left_search
				@caption Laoseis

				@property prod_s_price_from type=textbox store=no captionside=top size=30 parent=prod_left_search
				@caption Hind alates
				
				@property prod_s_pricelist type=textbox store=no captionside=top size=30  parent=prod_left_search
				@caption Hinnakiri
				
				@property prod_s_sbt type=submit store=no captionside=top  parent=prod_left_search value="Otsi"
				@caption Otsi
				

		@property products_list type=table store=no no_caption=1  parent=prod_split
		@caption Toodete nimekiri 

@default group=packets

	@property packets_toolbar type=toolbar no_caption=1 group=packets store=no

	@layout packets_split type=hbox width=20%:80%

		@layout packets_left type=vbox parent=packets_split

			@layout packets_tree_lay type=vbox closeable=1 area_caption=Pakettide&nbsp;puu parent=packets_left
	
				@property packets_tree type=treeview parent=packets_tree_lay store=no no_caption=1

			@layout packets_left_search type=vbox parent=packets_left area_caption=Otsing closeable=1

				@property packets_s_name type=textbox store=no captionside=top size=30 parent=packets_left_search
				@caption Nimi 
				
				@property packets_s_code type=textbox store=no captionside=top size=30 parent=packets_left_search
				@caption Kood
				
				@property packets_s_barcode type=textbox store=no captionside=top size=30 parent=packets_left_search
				@caption Ribakood
				
				@property packets_s_cat type=textbox store=no captionside=top size=30 parent=packets_left_search
				@caption Kategooria
				
				@property packets_s_count type=select store=no captionside=top parent=packets_left_search
				@caption Laoseis

				@property packets_s_price_from type=textbox store=no captionside=top size=30 parent=packets_left_search
				@caption Hind alates
				
				@property packets_s_pricelist type=textbox store=no captionside=top size=30  parent=packets_left_search
				@caption Hinnakiri
				
				@property packets_s_sbt type=submit store=no captionside=top  parent=packets_left_search value="Otsi"
				@caption Otsi
				

		@property packets_list type=table store=no no_caption=1  parent=packets_split
		@caption Pakettide nimekiri 


@default group=status_status

	@property storage_status_toolbar type=toolbar no_caption=1 store=no

	@layout storage_status_split type=hbox width=20%:80%

		@layout storage_status_left type=vbox parent=storage_status_split

			@layout storage_status_tree_lay type=vbox closeable=1 area_caption=Filtreeri parent=storage_status_left
	
				@property storage_status_tree type=treeview parent=storage_status_tree_lay store=no no_caption=1

			@layout storage_status_left_search type=vbox parent=storage_status_left area_caption=Otsing closeable=1

				@property storage_status_s_name type=textbox store=no captionside=top size=30 parent=storage_status_left_search
				@caption Nimi
				
				@property storage_status_s_code type=textbox store=no captionside=top size=30 parent=storage_status_left_search
				@caption Kood
				
				@property storage_status_s_barcode type=textbox store=no captionside=top size=30 parent=storage_status_left_search
				@caption Ribakood
				
				@property storage_status_s_category type=date_select store=no captionside=top parent=storage_status_left_search
				@caption Kategooria

				@property storage_status_s_status type=date_select store=no captionside=top size=30 parent=storage_status_left_search
				@caption Laoseis
				
				@property storage_status_s_price type=textbox store=no captionside=top size=30  parent=storage_status_left_search
				@caption Hind
				
				@property storage_status_s_pricelist type=textbox store=no captionside=top size=30  parent=storage_status_left_search
				@caption Hinnakiri
				
				@property storage_status_s_below_min type=checkbox ch_value=1 store=no captionside=top size=30  parent=storage_status_left_search
				@caption Alla miinimumi
				
				@property storage_status_s_sbt type=submit store=no captionside=top  parent=storage_status_left_search value="Otsi"
				@caption Otsi
				

		@property storage_status type=table store=no no_caption=1  parent=storage_status_split
		@caption Laoseis


@default group=status_prognosis

	@property storage_prognosis_toolbar type=toolbar no_caption=1 store=no

	@layout storage_prognosis_split type=hbox width=20%:80%

		@layout storage_prognosis_left type=vbox parent=storage_prognosis_split

			@layout storage_prognosis_tree_lay type=vbox closeable=1 area_caption=Filtreeri parent=storage_prognosis_left
	
				@property storage_prognosis_tree type=treeview parent=storage_prognosis_tree_lay store=no no_caption=1

			@layout storage_prognosis_left_search type=vbox parent=storage_prognosis_left area_caption=Otsing closeable=1

				@property storage_prognosis_s_name type=textbox store=no captionside=top size=30 parent=storage_prognosis_left_search
				@caption Nimi
				
				@property storage_prognosis_s_code type=textbox store=no captionside=top size=30 parent=storage_prognosis_left_search
				@caption Kood
				
				@property storage_prognosis_s_barcode type=textbox store=no captionside=top size=30 parent=storage_prognosis_left_search
				@caption Ribakood
				
				@property storage_prognosis_s_category type=date_select store=no captionside=top parent=storage_prognosis_left_search
				@caption Kategooria

				@property storage_prognosis_s_status type=date_select store=no captionside=top size=30 parent=storage_prognosis_left_search
				@caption Laoseis
				
				@property storage_prognosis_s_price type=textbox store=no captionside=top size=30  parent=storage_prognosis_left_search
				@caption Hind
				
				@property storage_prognosis_s_pricelist type=textbox store=no captionside=top size=30  parent=storage_prognosis_left_search
				@caption Hinnakiri
				
				@property storage_prognosis_s_below_min type=checkbox ch_value=1 store=no captionside=top size=30  parent=storage_prognosis_left_search
				@caption Alla miinimumi
				
				@property storage_prognosis_s_date type=date_select ch_value=1 store=no captionside=top size=30  parent=storage_prognosis_left_search
				@caption Kuup&auml;ev
				
				@property storage_prognosis_s_sales_order_status type=chooser ch_value=1 store=no captionside=top size=30  parent=storage_prognosis_left_search
				@caption M&uuml;&uuml;gitellimuste staatus

				@property storage_prognosis_s_purchase_order_status type=chooser ch_value=1 store=no captionside=top size=30  parent=storage_prognosis_left_search
				@caption Ostutellimuste staatus
				
				@property storage_prognosis_s_sbt type=submit store=no captionside=top  parent=storage_prognosis_left_search value="Otsi"
				@caption Otsi
				

		@property storage_prognosis type=table store=no no_caption=1  parent=storage_prognosis_split
		@caption Laoseis


@default group=purchase_orders

	@property purchase_orders_toolbar type=toolbar no_caption=1 store=no

	@layout purchase_orders_split type=hbox width=20%:80%

		@layout purchase_orders_left type=vbox parent=purchase_orders_split

			@layout purchase_orders_tree_lay type=vbox closeable=1 area_caption=Filtreeri parent=purchase_orders_left
	
				@property purchase_orders_tree type=treeview parent=purchase_orders_tree_lay store=no no_caption=1

			@layout purchase_orders_left_search type=vbox parent=purchase_orders_left area_caption=Otsing closeable=1

				@property purchase_orders_s_purchaser type=textbox store=no captionside=top size=30 parent=purchase_orders_left_search
				@caption Hankija
				
				@property purchase_orders_s_number type=textbox store=no captionside=top size=30 parent=purchase_orders_left_search
				@caption Number

				@property purchase_orders_s_status type=chooser store=no captionside=top size=30 parent=purchase_orders_left_search
				@caption Staatus
				
				@property purchase_orders_s_from type=date_select store=no captionside=top parent=purchase_orders_left_search
				@caption Alates

				@property purchase_orders_s_to type=date_select store=no captionside=top size=30 parent=purchase_orders_left_search
				@caption Kuni
				
				@property purchase_orders_s_art type=textbox store=no captionside=top size=30 parent=purchase_orders_left_search
				@caption Artikkel

				@property purchase_orders_s_art_cat type=textbox store=no captionside=top size=30 parent=purchase_orders_left_search
				@caption Artikli kategooria

				@property purchase_orders_s_sbt type=submit store=no captionside=top  parent=purchase_orders_left_search value="Otsi"
				@caption Otsi
				

		@property purchase_orders type=table store=no no_caption=1  parent=purchase_orders_split
		@caption Ostutellimused


@default group=sell_orders

	@property sell_orders_toolbar type=toolbar no_caption=1 store=no

	@layout sell_orders_split type=hbox width=20%:80%

		@layout sell_orders_left type=vbox parent=sell_orders_split

			@layout sell_orders_tree_lay type=vbox closeable=1 area_caption=Filtreeri parent=sell_orders_left
	
				@property sell_orders_tree type=treeview parent=sell_orders_tree_lay store=no no_caption=1

			@layout sell_orders_left_search type=vbox parent=sell_orders_left area_caption=Otsing closeable=1

				@property sell_orders_s_buyer type=textbox store=no captionside=top size=30 parent=sell_orders_left_search
				@caption Ostja
				
				@property sell_orders_s_number type=textbox store=no captionside=top size=30 parent=sell_orders_left_search
				@caption Number

				@property sell_orders_s_status type=chooser store=no captionside=top size=30 parent=sell_orders_left_search
				@caption Staatus
				
				@property sell_orders_s_from type=date_select store=no captionside=top parent=sell_orders_left_search
				@caption Alates

				@property sell_orders_s_to type=date_select store=no captionside=top size=30 parent=sell_orders_left_search
				@caption Kuni
				
				@property sell_orders_s_art type=textbox store=no captionside=top size=30 parent=sell_orders_left_search
				@caption Artikkel

				@property sell_orders_s_art_cat type=textbox store=no captionside=top size=30 parent=sell_orders_left_search
				@caption Artikli kategooria

				@property sell_orders_s_purchaser type=textbox store=no captionside=top size=30 parent=sell_orders_left_search
				@caption Hankija

				@property sell_orders_s_job_name type=textbox store=no captionside=top size=30 parent=sell_orders_left_search
				@caption T&ouml;&ouml; nimi

				@property sell_orders_s_job_number type=textbox store=no captionside=top size=30 parent=sell_orders_left_search
				@caption T&ouml;&ouml; number

				@property sell_orders_s_sales_manager type=textbox store=no captionside=top size=30 parent=sell_orders_left_search
				@caption M&uuml;&uuml;gijuht

				@property sell_orders_s_sbt type=submit store=no captionside=top  parent=sell_orders_left_search value="Otsi"
				@caption Otsi
				

		@property sell_orders type=table store=no no_caption=1  parent=sell_orders_split
		@caption M&uuml;&uuml;gitellimused

@default group=offers

	@property offers_toolbar type=toolbar no_caption=1 store=no

	@layout offers_split type=hbox width=20%:80%

		@layout offers_left type=vbox parent=offers_split

			@layout offers_tree_lay type=vbox closeable=1 area_caption=Filtreeri parent=offers_left
	
				@property offers_tree type=treeview parent=offers_tree_lay store=no no_caption=1

			@layout offers_left_search type=vbox parent=offers_left area_caption=Otsing closeable=1

				@property offers_s_buyer type=textbox store=no captionside=top size=30 parent=offers_left_search
				@caption Tellija
				
				@property offers_s_number type=textbox store=no captionside=top size=30 parent=offers_left_search
				@caption Number

				@property offers_s_status type=chooser store=no captionside=top size=30 parent=offers_left_search
				@caption Staatus
				
				@property offers_s_from type=date_select store=no captionside=top parent=offers_left_search
				@caption Alates

				@property offers_s_to type=date_select store=no captionside=top size=30 parent=offers_left_search
				@caption Kuni
				
				@property offers_s_art type=textbox store=no captionside=top size=30 parent=offers_left_search
				@caption Artikkel

				@property offers_s_art_cat type=textbox store=no captionside=top size=30 parent=offers_left_search
				@caption Artikli kategooria

				@property offers_s_sbt type=submit store=no captionside=top  parent=offers_left_search value="Otsi"
				@caption Otsi
				

		@property offers type=table store=no no_caption=1  parent=offers_split
		@caption Pakkumised


@default group=sales_specialoffers

	@property sales_specialoffers_toolbar type=toolbar no_caption=1 store=no

	@layout sales_specialoffers_split type=hbox width=20%:80%

		@layout sales_specialoffers_left type=vbox parent=sales_specialoffers_split

			@layout sales_specialoffers_tree_lay type=vbox closeable=1 area_caption=Filtreeri parent=sales_specialoffers_left
	
				@property sales_specialoffers_tree type=treeview parent=sales_specialoffers_tree_lay store=no no_caption=1

			@layout sales_specialoffers_left_search type=vbox parent=sales_specialoffers_left area_caption=Otsing closeable=1

				@property sales_specialoffers_s_name type=textbox store=no captionside=top size=30 parent=sales_specialoffers_left_search
				@caption Nimetus

				@property sales_specialoffers_s_from type=date_select store=no captionside=top parent=sales_specialoffers_left_search
				@caption Kehtivus alates

				@property sales_specialoffers_s_to type=date_select store=no captionside=top size=30 parent=sales_specialoffers_left_search
				@caption Kehtivus kuni

				@property sales_specialoffers_s_art type=textbox store=no captionside=top size=30 parent=sales_specialoffers_left_search
				@caption Artikkel

				@property sales_specialoffers_s_art_cat type=textbox store=no captionside=top size=30 parent=sales_specialoffers_left_search
				@caption Artikli kategooria
				
				@property sales_specialoffers_s_customer type=textbox store=no captionside=top size=30 parent=sales_specialoffers_left_search
				@caption Klient
				
				@property sales_specialoffers_s_customer_group type=textbox store=no captionside=top size=30 parent=sales_specialoffers_left_search
				@caption Kliendigrupp
				
				@property sales_specialoffers_s_usergroup type=textbox store=no captionside=top size=30 parent=sales_specialoffers_left_search
				@caption Kasutajagrupp

				@property sales_specialoffers_s_warehouse type=textbox store=no captionside=top size=30 parent=sales_specialoffers_left_search
				@caption Ladu
				
				@property sales_specialoffers_s_order_center type=textbox store=no captionside=top size=30 parent=sales_specialoffers_left_search
				@caption Tellimiskeskkond
				
				@property sales_specialoffers_s_brand type=textbox store=no captionside=top size=30 parent=sales_specialoffers_left_search
				@caption Br&auml;nd
				
				@property sales_specialoffers_s_sbt type=submit store=no captionside=top  parent=sales_specialoffers_left_search value="Otsi"
				@caption Otsi
				

		@property specialoffers type=table store=no no_caption=1  parent=sales_specialoffers_split
		@caption Eripakkumised
	

@default group=sales_custcards

	@property sales_custcards_toolbar type=toolbar no_caption=1 store=no

	@layout sales_custcards_split type=hbox width=20%:80%

		@layout sales_custcards_left type=vbox parent=sales_custcards_split

			@layout sales_custcards_tree_lay type=vbox closeable=1 area_caption=Filtreeri parent=sales_custcards_left
	
				@property sales_custcards_tree type=treeview parent=sales_custcards_tree_lay store=no no_caption=1

			@layout sales_custcards_left_search type=vbox parent=sales_custcards_left area_caption=Otsing closeable=1

				@property sales_custcards_s_name type=textbox store=no captionside=top size=30 parent=sales_custcards_left_search
				@caption Customer

				@property sales_custcards_s_from type=date_select store=no captionside=top parent=sales_custcards_left_search
				@caption Kehtivuse algus alates

				@property sales_custcards_s_to type=date_select store=no captionside=top size=30 parent=sales_custcards_left_search
				@caption Kehtivuse algus kuni

				@property sales_custcards_s_efrom type=date_select store=no captionside=top parent=sales_custcards_left_search
				@caption Kehtivuse l&otilde;pp alates

				@property sales_custcards_s_eto type=date_select store=no captionside=top size=30 parent=sales_custcards_left_search
				@caption Kehtivuse l&otilde;pp kuni

				@property sales_custcards_s_number type=textbox store=no captionside=top size=30 parent=sales_custcards_left_search
				@caption Number

				@property sales_custcards_s_pfrom type=date_select store=no captionside=top parent=sales_custcards_left_search
				@caption Ostude ajavahemik alates

				@property sales_custcards_s_pto type=date_select store=no captionside=top size=30 parent=sales_custcards_left_search
				@caption Ostude ajavahemik kuni

				@property sales_custcards_s_art_cat type=textbox store=no captionside=top size=30 parent=sales_custcards_left_search
				@caption Artikli kategooria
				
				@property sales_custcards_s_sbt type=submit store=no captionside=top  parent=sales_custcards_left_search value="Otsi"
				@caption Otsi
				

		@property customer_cards type=table store=no no_caption=1  parent=sales_custcards_split
		@caption Kliendikaardid
	


/// general subs
	@groupinfo general_general parent=general caption="&Uuml;ldine"
	@groupinfo general_settings parent=general caption="Seaded"

@groupinfo articles caption="Artiklid"

	@groupinfo products caption="Artiklid" submit=no parent=articles
	@groupinfo packets caption="Paketid" submit=no parent=articles

@groupinfo status caption="Laoseis"

	@groupinfo status_status caption="Laoseis" parent=status
	@groupinfo status_prognosis caption="Prognoos" parent=status
	@groupinfo status_inventories caption="Inventuurid" parent=status

@groupinfo purchases caption="Tellimused"

	@groupinfo sell_orders caption="M&uuml;&uuml;gitellimused" parent=purchases
	@groupinfo purchase_orders caption="Ostutellimused" parent=purchases
	@groupinfo offers caption="Pakkumised" parent=purchases

@groupinfo sales caption="M&uuml;&uuml;giedendus"

	@groupinfo sales_specialoffers parent=sales caption="Eripakkumised"
	@groupinfo sales_custcards parent=sales caption="Kliendikaardid"

@reltype WAREHOUSE value=1 clid=CL_SHOP_WAREHOUSE
@caption Ladu

*/

class shop_sales_manager_workspace extends class_base
{
	const AW_CLID = 1440;

	function shop_sales_manager_workspace()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_sales_manager_workspace",
			"clid" => CL_SHOP_SALES_MANAGER_WORKSPACE
		));
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["ptf"] = $_GET["ptf"];
		$arr["pgtf"] = $_GET["pgtf"];
		$arr["post_ru"] = post_ru();
	}

	function get_property($arr)
	{
		$prop =& $arr["prop"];
		$arr["warehouses"] = $arr["obj_inst"]->prop("warehouses");
		if (substr($prop["name"], 0, strlen("storage_")) == "storage_" || substr($prop["name"], 0, strlen("purchase_")) == "purchase_" || substr($prop["name"], 0, strlen("sell_")) == "sell_" || substr($prop["name"], 0, strlen("status_")) == "status_")
		{
			$ret = $this->_delegate_warehouse($arr);
			if($ret)
			{
				return $ret;
			}
		}
		$retval = PROP_OK;

		switch($prop["name"])
		{
		}
		return $retval;
	}

	function set_property($arr)
	{
		$prop =& $arr["prop"];
		$retval = PROP_OK;
		$i = get_instance(CL_SHOP_WAREHOUSE);
		switch($prop["name"])
		{
			case "purchase_orders":
				return $i->_set_purchase_orders($arr);
				break;
		}
		return $retval;
	}

	private function _delegate_warehouse($arr)
	{
		$i = get_instance(CL_SHOP_WAREHOUSE);
		$fn = "_get_".$arr["prop"]["name"];
		$i->config = obj();
		if (method_exists($i, $fn))
		{
			return $i->$fn($arr);
		}
		elseif($ret = $i->process_search_param($arr))
		{
			return $ret;
		}
		else
		{
			return false;
		}
	}

	function callback_mod_retval($arr)
	{
		return get_instance(CL_SHOP_WAREHOUSE)->callback_mod_retval(&$arr);
	}

	function _get_offers_tree($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$disp = $arr["request"]["disp"];

		$t->add_item(0, array(
			"id" => "unc",
			"url" => aw_url_change_var("disp", "unc"),
			"name" => $disp == "unc" ? "<b>".t("Kinnitamata")."</b>" : t("Kinnitamata")
		));

		$t->add_item(0, array(
			"id" => "conf",
			"url" => aw_url_change_var("disp", "conf"),
			"name" => $disp == "conf" ? "<b>".t("Kinnitatud")."</b>" : t("Kinnitatud")
		));

		$t->add_item(0, array(
			"id" => "arc",
			"url" => aw_url_change_var("disp", "arc"),
			"name" => $disp == "arc" ? "<b>".t("Arhiveeritud")."</b>" : t("Arhiveeritud")
		));
	}

	function _get_offers(&$arr)
	{
		$this->_init_sell_orders_tbl($arr["prop"]["vcl_inst"]);
	}

	private function _init_sell_orders_tbl($t)
	{
		$t->define_field(array(
			"name" => "number",
			"caption" => t("Number"),
			"sortable" => 1,
		));

		$t->define_field(array(
			"name" => "seller",
			"caption" => t("Tellija"),
			"sortable" => 1,
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "created",
			"caption" => t("Kuup&auml;ev"),
			"align" => "center",
			"type" => "time",
			"format" => "d.m.Y H:i"
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "rels",
			"caption" => t("Seosed"),
			"align" => "center"
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "sum",
			"caption" => t("Summa"),
			"align" => "center"
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "status",
			"caption" => t("Staatus"),
			"align" => "center"
		));

		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	function _get_sales_specialoffers_tree($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$disp = $arr["request"]["disp"];

		$t->add_item(0, array(
			"id" => "unc",
			"url" => aw_url_change_var("disp", "unc"),
			"name" => $disp == "unc" ? "<b>".t("Kehtivad")."</b>" : t("Kehtivad")
		));

		$t->add_item(0, array(
			"id" => "arc",
			"url" => aw_url_change_var("disp", "arc"),
			"name" => $disp == "arc" ? "<b>".t("Arhiveeritud")."</b>" : t("Arhiveeritud")
		));
	}

	function _get_specialoffers(&$arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$this->_init_specialoffers_tbl($t);
		$ol = new object_list(array(
			"warehouses" => $arr["obj_inst"]->prop("warehouses"),
			"class_id" => CL_SHOP_SPECIAL_OFFER,
			"site_id" => array(),
			"lang_id" => array(),
		));
		foreach($ol->arr() as $o)
		{
			$t->define_field(array(
				"name" => html::obj_change_url($o, parse_obj_name($o->name())),
				"from" => $o->prop("valid_from_date"),
				"to" => $o->prop("valid_to_date"),
				"active" => $o->prop("valid")?t("Jah"):t("Ei"),
				"who" => "",
			));
		}
	}

	private function _init_specialoffers_tbl($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Name"),
			"sortable" => 1,
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "from",
			"caption" => t("Alates"),
			"align" => "center",
			"type" => "time",
			"format" => "d.m.Y H:i"
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "to",
			"caption" => t("Kuni"),
			"align" => "center",
			"type" => "time",
			"format" => "d.m.Y H:i"
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "active",
			"caption" => t("Kehtib?"),
			"align" => "center"
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "who",
			"caption" => t("Kellele"),
			"align" => "center"
		));

		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	function _get_sales_specialoffers_toolbar($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_new_button(array(CL_SHOP_SPECIAL_OFFER), $arr["obj_inst"]->id());
		$tb->add_save_button();
		$tb->add_delete_button();
	}

	function _get_sales_custcards_toolbar($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_new_button(array(CL_MENU));
		$tb->add_save_button();
		$tb->add_delete_button();
	}

	function _get_sales_custcards_tree($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$disp = $arr["request"]["disp"];

		$t->add_item(0, array(
			"id" => "unc",
			"url" => aw_url_change_var("disp", "unc"),
			"name" => $disp == "unc" ? "<b>".t("Kehtivad")."</b>" : t("Kehtivad")
		));

		$t->add_item(0, array(
			"id" => "arc",
			"url" => aw_url_change_var("disp", "arc"),
			"name" => $disp == "arc" ? "<b>".t("Arhiveeritud")."</b>" : t("Arhiveeritud")
		));
	}

	function _get_customer_cards(&$arr)
	{
		$this->_init_custcards_tbl($arr["prop"]["vcl_inst"]);
	}

	private function _init_custcards_tbl($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Name"),
			"sortable" => 1,
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "from",
			"caption" => t("Alates"),
			"align" => "center",
			"type" => "time",
			"format" => "d.m.Y H:i"
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "to",
			"caption" => t("Kuni"),
			"align" => "center",
			"type" => "time",
			"format" => "d.m.Y H:i"
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "active",
			"caption" => t("Kehtib?"),
			"align" => "center"
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "who",
			"caption" => t("Kellele"),
			"align" => "center"
		));

		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}
}


?>
