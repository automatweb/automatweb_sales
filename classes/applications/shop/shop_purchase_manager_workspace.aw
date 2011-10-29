<?php
/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@default table=objects
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

	@property mrp_workspace type=relpicker reltype=RELTYPE_WORKSPACE store=connect
	@caption Ressursihalduskeskkond

	@property show_alt_units type=checkbox ch_value=1 field=meta method=serialize
	@caption Kuva alternatiiv&uuml;hikuid

@default group=storage_income

	@property storage_income_toolbar type=toolbar no_caption=1 store=no

	@layout storage_income_split type=hbox width=20%:80%

		@layout storage_income_left type=vbox parent=storage_income_split

			@layout storage_income_tree_lay type=vbox closeable=1 area_caption=Filtreeri parent=storage_income_left

				@property storage_income_tree type=treeview parent=storage_income_tree_lay store=no no_caption=1

				@property storage_income_prod_tree type=treeview parent=storage_income_tree_lay store=no no_caption=1

			@layout storage_income_left_search type=vbox parent=storage_income_left area_caption=Otsing closeable=1

				@property storage_income_s_acquiredby type=textbox store=no captionside=top size=30 parent=storage_income_left_search
				@caption Hankija

				@property storage_income_s_type type=chooser store=no captionside=top size=30 parent=storage_income_left_search
				@caption T&uuml;&uuml;p

				@property storage_income_s_number type=textbox store=no captionside=top size=30 parent=storage_income_left_search
				@caption Number

				@property storage_income_s_status  type=chooser store=no captionside=top size=30 parent=storage_income_left_search
				@caption Staatus

				@property storage_income_s_from type=date_select store=no captionside=top parent=storage_income_left_search
				@caption Alates

				@property storage_income_s_to type=date_select store=no captionside=top size=30 parent=storage_income_left_search
				@caption Kuni

				@property storage_income_s_article type=textbox store=no captionside=top size=30  parent=storage_income_left_search
				@caption Artikkel

				@property storage_income_s_articlecode type=textbox store=no captionside=top size=30  parent=storage_income_left_search
				@caption Artikli kood

				@property storage_income_s_art_cat type=select store=no captionside=top  parent=storage_income_left_search
				@caption Artikli kategooria

				@property storage_income_s_sbt type=submit store=no captionside=top  parent=storage_income_left_search value="Otsi"
				@caption Otsi


		@property storage_income type=table store=no no_caption=1  parent=storage_income_split
		@caption Sissetulekud


@default group=storage_export

	@property storage_export_toolbar type=toolbar no_caption=1 store=no

	@layout storage_export_split type=hbox width=20%:80%

		@layout storage_export_left type=vbox parent=storage_export_split

			@layout storage_export_tree_lay type=vbox closeable=1 area_caption=Filtreeri parent=storage_export_left

				@property storage_export_tree type=treeview parent=storage_export_tree_lay store=no no_caption=1

				@property storage_export_prod_tree type=treeview parent=storage_export_tree_lay store=no no_caption=1

			@layout storage_export_left_search type=vbox parent=storage_export_left area_caption=Otsing closeable=1

				@property storage_export_s_acquiredby type=textbox store=no captionside=top size=30 parent=storage_export_left_search
				@caption Hankija

				@property storage_export_s_type type=chooser store=no captionside=top size=30 parent=storage_export_left_search
				@caption T&uuml;&uuml;p

				@property storage_export_s_number type=textbox store=no captionside=top size=30 parent=storage_export_left_search
				@caption Number

				@property storage_export_s_status type=chooser store=no captionside=top size=30 parent=storage_export_left_search
				@caption Staatus

				@property storage_export_s_from type=date_select store=no captionside=top parent=storage_export_left_search
				@caption Alates

				@property storage_export_s_to type=date_select store=no captionside=top size=30 parent=storage_export_left_search
				@caption Kuni

				@property storage_export_s_article type=textbox store=no captionside=top size=30  parent=storage_export_left_search
				@caption Artikkel

				@property storage_export_s_articlecode type=textbox store=no captionside=top size=30  parent=storage_export_left_search
				@caption Artikli kood

				@property storage_export_s_art_cat type=select store=no captionside=top  parent=storage_export_left_search
				@caption Artikli kategooria

				@property storage_export_s_sbt type=submit store=no captionside=top  parent=storage_export_left_search value="Otsi"
				@caption Otsi


		@property storage_export type=table store=no no_caption=1  parent=storage_export_split
		@caption V&auml;ljaminekud


@default group=storage_movements

	@property storage_movements_toolbar type=toolbar no_caption=1 store=no

	@layout storage_movements_split type=hbox width=20%:80%

		@layout storage_movements_left type=vbox parent=storage_movements_split

			@layout storage_movements_tree_lay type=vbox closeable=1 area_caption=Filtreeri parent=storage_movements_left

				@property storage_movements_tree2 type=treeview parent=storage_movements_tree_lay store=no no_caption=1

				@property storage_movements_tree type=treeview parent=storage_movements_tree_lay store=no no_caption=1

			@layout storage_movements_left_search type=vbox parent=storage_movements_left area_caption=Otsing closeable=1

				@property storage_movements_s_warehouse type=select store=no captionside=top parent=storage_movements_left_search
				@caption Ladu

				@property storage_movements_s_direction type=chooser store=no captionside=top parent=storage_movements_left_search
				@caption Liikumise suund

				@property storage_movements_s_number type=textbox store=no captionside=top size=30 parent=storage_movements_left_search
				@caption Saatelehe number

				@property storage_movements_s_from type=date_select store=no captionside=top parent=storage_movements_left_search
				@caption Alates

				@property storage_movements_s_to type=date_select store=no captionside=top size=30 parent=storage_movements_left_search
				@caption Kuni

				@property storage_movements_s_article type=textbox store=no captionside=top size=30  parent=storage_movements_left_search
				@caption Artikkel

				@property storage_movements_s_articlecode type=textbox store=no captionside=top size=30  parent=storage_movements_left_search
				@caption Artiklikood

				@property storage_movements_s_art_cat type=select store=no captionside=top parent=storage_movements_left_search
				@caption Artikli kategooria

				@property storage_movements_s_sbt type=submit store=no captionside=top  parent=storage_movements_left_search value="Otsi"
				@caption Otsi


		@property storage_movements type=table store=no no_caption=1  parent=storage_movements_split
		@caption V&auml;ljaminekud


@default group=storage_writeoffs

	@property storage_writeoffs_toolbar type=toolbar no_caption=1 store=no

	@layout storage_writeoffs_split type=hbox width=20%:80%

		@layout storage_writeoffs_left type=vbox parent=storage_writeoffs_split

			@layout storage_writeoffs_tree_lay type=vbox closeable=1 area_caption=Filtreeri parent=storage_writeoffs_left

				@property storage_writeoffs_tree2 type=treeview parent=storage_writeoffs_tree_lay store=no no_caption=1

				@property storage_writeoffs_tree type=treeview parent=storage_writeoffs_tree_lay store=no no_caption=1

			@layout storage_writeoffs_left_search type=vbox parent=storage_writeoffs_left area_caption=Otsing closeable=1

				@property storage_writeoffs_s_warehouse type=select store=no captionside=top parent=storage_writeoffs_left_search
				@caption Ladu

				@property storage_writeoffs_s_number type=textbox store=no captionside=top size=30 parent=storage_writeoffs_left_search
				@caption Saatelehe number

				@property storage_writeoffs_s_from type=date_select store=no captionside=top parent=storage_writeoffs_left_search
				@caption Alates

				@property storage_writeoffs_s_to type=date_select store=no captionside=top size=30 parent=storage_writeoffs_left_search
				@caption Kuni

				@property storage_writeoffs_s_article type=textbox store=no captionside=top size=30  parent=storage_writeoffs_left_search
				@caption Artikkel

				@property storage_writeoffs_s_articlecode type=textbox store=no captionside=top size=30  parent=storage_writeoffs_left_search
				@caption Artiklikood

				@property storage_writeoffs_s_art_cat type=select store=no captionside=top parent=storage_writeoffs_left_search
				@caption Artikli kategooria

				@property storage_writeoffs_s_sbt type=submit store=no captionside=top  parent=storage_writeoffs_left_search value="Otsi"
				@caption Otsi


		@property storage_writeoffs type=table store=no no_caption=1  parent=storage_writeoffs_split
		@caption Mahakandmised

@default group=status_status

	@property storage_status_toolbar type=toolbar no_caption=1 store=no

	@layout storage_status_split type=hbox width=20%:80%

		@layout storage_status_left type=vbox parent=storage_status_split

			@layout storage_status_tree_lay type=vbox closeable=1 area_caption=Filtreeri parent=storage_status_left

				@property storage_status_tree type=treeview parent=storage_status_tree_lay store=no no_caption=1

				@property storage_status_tree2 type=treeview parent=storage_status_tree_lay store=no no_caption=1

			@layout storage_status_left_search type=vbox parent=storage_status_left area_caption=Otsing closeable=1

				@property storage_status_s_name type=textbox store=no captionside=top size=30 parent=storage_status_left_search
				@caption Nimi

				@property storage_status_s_code type=textbox store=no captionside=top size=30 parent=storage_status_left_search
				@caption Kood

				@property storage_status_s_barcode type=textbox store=no captionside=top size=30 parent=storage_status_left_search
				@caption Ribakood

				@property storage_status_s_art_cat type=select store=no captionside=top parent=storage_status_left_search
				@caption Kategooria

				@property storage_status_s_count type=chooser store=no captionside=top size=30 parent=storage_status_left_search
				@caption Laoseis

				@property storage_status_s_price type=textbox store=no captionside=top size=30  parent=storage_status_left_search
				@caption Hind

				@property storage_status_s_pricelist type=select store=no captionside=top  parent=storage_status_left_search
				@caption Hinnakiri

				@property storage_status_s_below_min type=checkbox ch_value=1 store=no captionside=top size=30  parent=storage_status_left_search no_caption=1
				@caption Alla miinimumi

				@property storage_status_s_show_pieces type=checkbox ch_value=1 store=no captionside=top size=30  parent=storage_status_left_search no_caption=1
				@caption Kuva t&uuml;kkidena

				@property storage_status_s_show_batches type=checkbox ch_value=1 store=no captionside=top size=30  parent=storage_status_left_search no_caption=1
				@caption Kuva partiidena

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

				@property storage_prognosis_tree2 type=treeview parent=storage_prognosis_tree_lay store=no no_caption=1

			@layout storage_prognosis_left_search type=vbox parent=storage_prognosis_left area_caption=Otsing closeable=1

				@property storage_prognosis_s_name type=textbox store=no captionside=top size=30 parent=storage_prognosis_left_search
				@caption Nimi

				@property storage_prognosis_s_code type=textbox store=no captionside=top size=30 parent=storage_prognosis_left_search
				@caption Kood

				@property storage_prognosis_s_barcode type=textbox store=no captionside=top size=30 parent=storage_prognosis_left_search
				@caption Ribakood

				@property storage_prognosis_s_art_cat type=select store=no captionside=top parent=storage_prognosis_left_search
				@caption Kategooria

				@property storage_prognosis_s_count type=chooser store=no captionside=top size=30 parent=storage_prognosis_left_search
				@caption Laoseis

				@property storage_prognosis_s_price type=textbox store=no captionside=top size=30  parent=storage_prognosis_left_search
				@caption Hind

				@property storage_prognosis_s_pricelist type=select store=no captionside=top  parent=storage_prognosis_left_search
				@caption Hinnakiri

				@property storage_prognosis_s_below_min type=checkbox ch_value=1 store=no captionside=top size=30  parent=storage_prognosis_left_search no_caption=1
				@caption Alla miinimumi

				@property storage_prognosis_s_date type=date_select ch_value=1 store=no captionside=top size=30  parent=storage_prognosis_left_search
				@caption Kuup&auml;ev

				@property storage_prognosis_s_sales_order_status type=chooser ch_value=1 store=no captionside=top size=30  parent=storage_prognosis_left_search
				@caption M&uuml;&uuml;gitellimuste staatus

				@property storage_prognosis_s_purchase_order_status type=chooser ch_value=1 store=no captionside=top size=30  parent=storage_prognosis_left_search
				@caption Ostutellimuste staatus

				@property storage_prognosis_s_show_pieces type=checkbox ch_value=1 store=no captionside=top size=30  parent=storage_prognosis_left_search no_caption=1
				@caption Kuva t&uuml;kkidena

				@property storage_prognosis_s_show_batches type=checkbox ch_value=1 store=no captionside=top size=30  parent=storage_prognosis_left_search no_caption=1
				@caption Kuva partiidena

				@property storage_prognosis_s_sbt type=submit store=no captionside=top  parent=storage_prognosis_left_search value="Otsi"
				@caption Otsi



		@property storage_prognosis type=table store=no no_caption=1  parent=storage_prognosis_split
		@caption Laoseis


@default group=status_inventories

	@property storage_inventories_toolbar type=toolbar no_caption=1 store=no

	@layout storage_inventories_split type=hbox width=20%:80%

		@layout storage_inventories_left type=vbox parent=storage_inventories_split

			@layout storage_inventories_tree_lay type=vbox closeable=1 area_caption=Filtreeri parent=storage_inventories_left

				@property storage_inventories_tree type=treeview parent=storage_inventories_tree_lay store=no no_caption=1

			@layout storage_inventories_left_search type=vbox parent=storage_inventories_left area_caption=Otsing closeable=1

				@property storage_inventories_s_name type=textbox store=no captionside=top size=30 parent=storage_inventories_left_search
				@caption Nimi

				@property storage_inventories_s_from type=date_select store=no captionside=top parent=storage_inventories_left_search
				@caption Alates

				@property storage_inventories_s_to type=date_select store=no captionside=top size=30 parent=storage_inventories_left_search
				@caption Kuni

				@property storage_inventories_s_sbt type=submit store=no captionside=top  parent=storage_inventories_left_search value="Otsi"
				@caption Otsi


		@property storage_inventories type=table store=no no_caption=1  parent=storage_inventories_split
		@caption Inventuurid

@default group=status_orders

	@property status_orders_toolbar type=toolbar no_caption=1 store=no

	@layout status_orders_split type=hbox width=20%:80%

		@layout status_orders_left type=vbox parent=status_orders_split

			@layout status_orders_time_tree_lay type=vbox closeable=1 area_caption=Vali&nbsp;ajavahemik parent=status_orders_left

				@property status_orders_time_tree type=treeview parent=status_orders_time_tree_lay store=no no_caption=1

			@layout status_orders_case_tree_lay type=vbox closeable=1 area_caption=Vali&nbsp;tellimus parent=status_orders_left

				@property status_orders_case_tree type=treeview parent=status_orders_case_tree_lay store=no no_caption=1

			@layout status_orders_prod_tree_lay type=vbox closeable=1 area_caption=Vali&nbsp;tootegrupp parent=status_orders_left

				@property status_orders_prod_tree type=treeview parent=status_orders_prod_tree_lay store=no no_caption=1

			@layout status_orders_res_tree_lay type=vbox closeable=1 area_caption=Vali&nbsp;ressurss parent=status_orders_left

				@property status_orders_res_tree type=treeview parent=status_orders_res_tree_lay store=no no_caption=1

			@layout status_orders_opt_lay type=vbox closeable=1 parent=status_orders_left area_caption=Lisavalikud

				@property status_orders_opt1 type=chooser store=no captionside=top parent=status_orders_opt_lay
				@caption Kuvatakse tooteid, mis...

			@layout status_orders_left_search type=vbox parent=status_orders_left area_caption=Otsing closeable=1

				@property status_orders_s_name type=textbox store=no captionside=top size=30 parent=status_orders_left_search
				@caption Toote nimi

				@property status_orders_s_code type=textbox store=no captionside=top size=30 parent=status_orders_left_search
				@caption Toote kood

				@property status_orders_s_art_cat type=select store=no captionside=top parent=status_orders_left_search
				@caption Toote kategooria

				@property status_orders_s_case_no type=textbox store=no captionside=top size=30 parent=status_orders_left_search
				@caption Tellimuse nr

				@property status_orders_s_start type=date_select ch_value=1 store=no captionside=top size=30  parent=status_orders_left_search
				@caption Ajavahemiku algus

				@property status_orders_s_end type=date_select ch_value=1 store=no captionside=top size=30  parent=status_orders_left_search
				@caption Ajavahemiku l&otilde;pp

				@property status_orders_s_sbt type=submit store=no captionside=top  parent=status_orders_left_search value="Otsi"
				@caption Otsi

		@property status_orders type=table store=no no_caption=1  parent=status_orders_split

@default group=purchase_orders

	@property purchase_orders_toolbar type=toolbar no_caption=1 store=no

	@layout purchase_orders_split type=hbox width=20%:80%

		@layout purchase_orders_left type=vbox parent=purchase_orders_split

			@layout purchase_orders_tree_lay type=vbox closeable=1 area_caption=Filtreeri parent=purchase_orders_left

				@property purchase_orders_tree type=treeview parent=purchase_orders_tree_lay store=no no_caption=1

			@layout purchase_orders_left_search type=vbox parent=purchase_orders_left area_caption=Otsing closeable=1

				@property purchase_orders_s_customer type=textbox store=no captionside=top size=30 parent=purchase_orders_left_search
				@caption Klient

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

				@property purchase_orders_s_art_cat type=select store=no captionside=top parent=purchase_orders_left_search
				@caption Artikli kategooria

				@property purchase_orders_s_purchaser type=textbox store=no captionside=top size=30 parent=purchase_orders_left_search
				@caption Hankija

				@property purchase_orders_s_job_name type=textbox store=no captionside=top size=30 parent=purchase_orders_left_search
				@caption T&ouml;&ouml; nimi

				@property purchase_orders_s_job_number type=textbox store=no captionside=top size=30 parent=purchase_orders_left_search
				@caption T&ouml;&ouml; number

				@property purchase_orders_s_sales_manager type=textbox store=no captionside=top size=30 parent=purchase_orders_left_search
				@caption M&uuml;&uuml;gijuht

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

				@property sell_orders_s_art_cat type=select store=no captionside=top parent=sell_orders_left_search
				@caption Artikli kategooria

				@property sell_orders_s_sbt type=submit store=no captionside=top  parent=sell_orders_left_search value="Otsi"
				@caption Otsi


		@property sell_orders type=table store=no no_caption=1  parent=sell_orders_split
		@caption M&uuml;&uuml;gitellimused





/// general subs
	@groupinfo general_general parent=general caption="&Uuml;ldine"
	@groupinfo general_settings parent=general caption="Seaded"

@groupinfo storage caption="Muutused"

	@groupinfo storage_income parent=storage caption="Sissetulekud"
	@groupinfo storage_export parent=storage caption="V&auml;ljaminekud"
	@groupinfo storage_movements parent=storage caption="Liikumised" submit=no
	@groupinfo storage_writeoffs parent=storage caption="Mahakandmised" submit=no

@groupinfo status caption="Laoseis"

	@groupinfo status_status caption="Laoseis" parent=status
	@groupinfo status_prognosis caption="Prognoos" parent=status
	@groupinfo status_inventories caption="Inventuurid" parent=status
	@groupinfo status_orders caption="Vajadused" parent=status

@groupinfo purchases caption="Tellimused"

	@groupinfo purchase_orders caption="Ostutellimused" parent=purchases
	@groupinfo sell_orders caption="M&uuml;&uuml;gitellimused" parent=purchases

@reltype WAREHOUSE value=1 clid=CL_SHOP_WAREHOUSE
@caption Ladu

@reltype WORKSPACE value=2 clid=CL_MRP_WORKSPACE
*/

class shop_purchase_manager_workspace extends class_base
{
	function shop_purchase_manager_workspace()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_purchase_manager_workspace",
			"clid" => CL_SHOP_PURCHASE_MANAGER_WORKSPACE
		));
	}

	function callback_mod_reforb(&$arr, $request)
	{
		$i = new shop_warehouse();
		return $i->callback_mod_reforb($arr, $request);
	}

	function get_property($arr)
	{
		$prop =& $arr["prop"];
		$arr["warehouses"] = $arr["obj_inst"]->prop("warehouses");
		if (substr($prop["name"], 0, strlen("storage_")) === "storage_" || substr($prop["name"], 0, strlen("purchase_")) === "purchase_" || substr($prop["name"], 0, strlen("sell_")) === "sell_" || substr($prop["name"], 0, strlen("status_")) === "status_")
		{
			$ret = $this->_delegate_warehouse($arr);
			if($ret)
			{
				return $ret;
			}
		}
		$retval = class_base::PROP_OK;
		return $retval;
	}

	public function _set_purchase_orders(&$arr)
	{
		$i = new shop_warehouse();
		return $i->_set_purchase_orders($arr);
	}

	private function _delegate_warehouse($arr)
	{
		$i = new shop_warehouse();
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
		$i = new shop_warehouse();
		return $i->callback_mod_retval($arr);
	}

	/**
	@attrib name=create_purchase_order
	**/
	function create_purchase_order($arr)
	{
		$i = new shop_warehouse();
		return $i->create_purchase_order($arr);
	}
}
