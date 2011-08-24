<?php
/*
HANDLE_MESSAGE_WITH_PARAM(MSG_POPUP_SEARCH_CHANGE,CL_SHOP_WAREHOUSE, on_popup_search_change)

@tableinfo aw_shop_warehouses index=aw_oid master_table=objects master_index=brother_of
@classinfo relationmgr=yes prop_cb=1

@default table=objects

@default group=general_sub

	@property name type=textbox rel=1 trans=1
	@caption Nimi
	@comment Objekti nimi

	@property short_name type=textbox field=meta method=serialize
	@caption L&uuml;hend

	@property comment type=textbox
	@caption Kommentaar
	@comment Vabas vormis tekst objekti kohta

	@property status type=status trans=1 default=1
	@caption Aktiivne
	@comment Kas objekt on aktiivne

	@property no_new_config type=checkbox ch_value=1
	@caption &Auml;ra loo seadeteobjekti


@default group=general_settings

	@property conf type=relpicker reltype=RELTYPE_CONFIG table=aw_shop_warehouses field=aw_config
	@caption Seaded

	@property order_center type=relpicker reltype=RELTYPE_ORDER_CENTER table=aw_shop_warehouses field=aw_order_center
	@caption Tellimiskeskkond tellimuste jaoks

	@property category_entry_form type=relpicker reltype=RELTYPE_CAT_ENTRY_FORM table=objects field=meta method=serialize
	@caption Kategooria lisamise vorm

	@property status_calc_type type=chooser table=aw_shop_warehouses field=aw_status_calc_type
	@caption Laoseisu arvestus

	@property sold_purveyance_code type=textbox field=meta method=serialize
	@caption V&auml;ljam&uuml;&uuml;dud kohaletoimetamise kood
	@comment Kohaletoimetamise tingimuse kood, mille puhul toode on v&auml;lja m&uuml;&uuml;dud

@default group=productgroups

	@property productgroups_toolbar type=toolbar no_caption=1 store=no

	@layout productgroups_c width=30%:70% type=hbox

		@layout productgroups_l type=vbox closeable=1 area_caption=Tootegrupid parent=productgroups_c

			@property productgroups_tree type=treeview store=no no_caption=1 parent=productgroups_l

		@layout productgroups_r type=vbox parent=productgroups_c

	@property productgroups_list type=table store=no no_caption=1 parent=productgroups_r
	@caption Tootegruppide nimekiri


@default group=product_management
	@property product_management_toolbar type=toolbar no_caption=1 store=no

	@layout product_managementsplit type=hbox width=20%:80%

		@layout product_managementleft type=vbox parent=product_managementsplit

			@layout product_managementtree_layout type=vbox closeable=1 area_caption=Artiklikategooriad parent=product_managementleft
				@property product_management_tree type=text parent=product_managementtree_layout store=no no_caption=1

			@layout product_management_tree_layout2 type=vbox closeable=1 area_caption=Artiklikategooriate&nbsp;t&uuml;&uuml;bid parent=product_managementleft
				@property product_management_category_tree type=text parent=product_management_tree_layout2 store=no no_caption=1

			@layout product_managementleft_search type=vbox parent=product_managementleft area_caption=Otsing closeable=1

				@layout product_managements_top_box type=vbox parent=product_managementleft_search

					@property product_managements_name type=textbox store=no captionside=top size=20 parent=product_managements_top_box
					@caption Nimi

					@property product_managements_code type=textbox store=no captionside=top size=20 parent=product_managements_top_box
					@caption Kood

					@property product_managements_barcode type=textbox store=no captionside=top size=20 parent=product_managements_top_box
					@caption Ribakood

					@property product_managements_count type=chooser store=no captionside=top parent=product_managements_top_box size=30
					@caption Laoseis

				@layout product_managements_price_box type=hbox parent=product_managementleft_search

					@property product_managements_price_from type=textbox store=no captionside=top size=8 parent=product_managements_price_box
					@caption Hind alates

					@property product_managements_price_to type=textbox store=no captionside=top size=8 parent=product_managements_price_box
					@caption Hind kuni

				@property product_managements_show_pieces type=checkbox ch_value=1 store=no captionside=top size=30  parent=product_managementleft_search no_caption=1 label=Kuva&nbsp;t&uuml;kkidena
				@caption Kuva t&uuml;kkidena

				@property product_managements_show_batches type=checkbox ch_value=1 store=no captionside=top size=30  parent=product_managementleft_search no_caption=1 label=Kuva&nbsp;partiidena
				@caption Kuva partiidena


				@property product_managements_sbt type=button store=no captionside=top  parent=product_managementleft_search
				@caption Otsi

		@layout product_managementright type=vbox parent=product_managementsplit

			@property category_list type=table store=no no_caption=1 parent=product_managementright
			@caption Kategooriate nimekiri

			@property packets_list type=table store=no no_caption=1 parent=product_managementright
			@caption Pakettide nimekiri

			@property product_management_list type=table store=no no_caption=1  parent=product_managementright
			@caption Toodete nimekiri

@default group=products

	@property products_toolbar type=toolbar no_caption=1 store=no

	@layout prod_split type=hbox width=20%:80%

		@layout prod_left type=vbox parent=prod_split

			@layout prod_tree_lay type=vbox closeable=1 area_caption=Toodete&nbsp;puu parent=prod_left

				@property prod_tree type=treeview parent=prod_tree_lay store=no no_caption=1

				@property prod_cat_tree type=treeview group=products parent=prod_tree_lay store=no no_caption=1

			@layout prod_left_search type=vbox parent=prod_left area_caption=Otsing closeable=1

				@layout prod_s_top_box type=vbox parent=prod_left_search

					@property prod_s_name type=textbox store=no captionside=top size=20 parent=prod_s_top_box
					@caption Nimi

					@property prod_s_code type=textbox store=no captionside=top size=20 parent=prod_s_top_box
					@caption Kood

					@property prod_s_barcode type=textbox store=no captionside=top size=20 parent=prod_s_top_box
					@caption Ribakood

					@property prod_s_cat type=select store=no captionside=top parent=prod_s_top_box
					@caption Kategooria

					@property prod_s_count type=chooser store=no captionside=top parent=prod_s_top_box size=30
					@caption Laoseis

				@layout prod_s_price_box type=hbox parent=prod_left_search

					@property prod_s_price_from type=textbox store=no captionside=top size=8 parent=prod_s_price_box
					@caption Hind alates

					@property prod_s_price_to type=textbox store=no captionside=top size=8 parent=prod_s_price_box
					@caption Hind kuni

				@property prod_s_pricelist type=select store=no captionside=top  parent=prod_left_search
				@caption Hinnakiri

				@property prod_s_show_pieces type=checkbox ch_value=1 store=no captionside=top size=30  parent=prod_left_search no_caption=1
				@caption Kuva t&uuml;kkidena

				@property prod_s_show_batches type=checkbox ch_value=1 store=no captionside=top size=30  parent=prod_left_search no_caption=1
				@caption Kuva partiidena

				@property prod_s_sbt type=button store=no captionside=top  parent=prod_left_search
				@caption Otsi


		@property products_list type=table store=no no_caption=1  parent=prod_split
		@caption Toodete nimekiri


#@default group=category
#	@property category_tb type=toolbar store=no no_caption=1
#	@layout category_split type=hbox width=20%:80%
#		@layout category_left type=vbox parent=category_split
#			@layout category_treel type=vbox closeable=1 area_caption=Kategooriad parent=category_left
#				@property category_tree type=treeview store=no no_caption=1 parent=category_treel
#			@layout category_type_treel type=vbox closeable=1 area_caption=T&uuml;&uuml;bid parent=category_left
#				@property category_type_tree type=treeview store=no no_caption=1 parent=category_type_treel
#		@layout category_listl type=vbox parent=category_split
#			@property category_list type=table store=no no_caption=1 parent=category_listl


@default group=packets

	@property packets_toolbar type=toolbar no_caption=1 group=packets store=no

	@layout packets_split type=hbox width=20%:80%

		@layout packets_left type=vbox parent=packets_split

			@layout packets_tree_lay type=vbox closeable=1 area_caption=Pakettide&nbsp;puu parent=packets_left
#
				@property packets_tree type=treeview parent=packets_tree_lay store=no no_caption=1
#
#			@layout packets_tree_lay2 type=vbox closeable=1 area_caption=Kategooriate&nbsp;t&uuml;&uuml;bid parent=packets_left
#
#				@property packets_cat_tree type=text parent=packets_tree_lay2 store=no no_caption=1


			@layout packets_left_search type=vbox parent=packets_left area_caption=Otsing closeable=1

				@property packets_s_name type=textbox store=no captionside=top size=30 parent=packets_left_search
				@caption Nimi

				@property packets_s_code type=textbox store=no captionside=top size=30 parent=packets_left_search
				@caption Kood

				@property packets_s_barcode type=textbox store=no captionside=top size=30 parent=packets_left_search
				@caption Ribakood

				@property packets_s_cat type=textbox store=no captionside=top parent=packets_left_search size=30
				@caption Kategooria

				@property packets_s_count type=select store=no captionside=top parent=packets_left_search
				@caption Laoseis

				@property packets_s_price_from type=textbox store=no captionside=top size=30 parent=packets_left_search
				@caption Hind alates

				@property packets_s_pricelist type=select store=no captionside=top parent=packets_left_search
				@caption Hinnakiri

				@property packets_s_created_from type=date_select store=no captionside=top parent=packets_left_search
				@caption Loodud alates

				@property packets_s_created_to type=date_select store=no captionside=top parent=packets_left_search
				@caption Loodud kuni

				@property packets_s_active type=select store=no captionside=top parent=packets_left_search
				@caption Aktiivsus

				@property packets_s_sbt type=submit store=no captionside=top  parent=packets_left_search value="Otsi"
				@caption Otsi

		@layout packets_right type=vbox parent=packets_split

			@layout packets_list_lay type=vbox closeable=1 parent=packets_right closeable=1 area_caption="Paketid"

				@property packets_list_old type=table store=no no_caption=1 parent=packets_list_lay
				@caption Pakettide nimekiri

@default group=brand

	@layout brand_toolbar type=vbox
		@property brand_toolbar type=toolbar no_caption=1 store=no parent=brand_toolbar
		@caption Kaubam&auml;rkide toolbar
	@layout brand_list type=vbox closeable=1 area_caption=Kaubam&auml;rkide&nbsp;nimekiri
		@property brand_list type=table store=no no_caption=1 parent=brand_list
		@caption Kaubam&auml;rkide nimekiri



@default group=channels

	@layout channel_toolbar type=vbox
		@property channel_toolbar type=toolbar no_caption=1 store=no parent=channel_toolbar
		@caption M&uuml;&uuml;gikanalite toolbar
	@layout channel_list type=vbox closeable=1 area_caption=M&uuml;&uuml;gikanalite&nbsp;nimekiri
		@property channel_list type=table store=no no_caption=1 channel=brand_list
		@caption M&uuml;&uuml;gikanalite nimekiri


@default group=arrivals

	@property arrivals_tb type=toolbar no_caption=1

	@layout arrival_prod_split type=hbox width=20%:80%

		@layout arrival_prod_left type=vbox parent=arrival_prod_split

			@layout arrival_prod_tree_lay type=vbox closeable=1 area_caption=Toodete&nbsp;puu parent=arrival_prod_left

				@property arrival_prod_tree type=treeview parent=arrival_prod_tree_lay store=no no_caption=1

				@property arrival_prod_cat_tree type=treeview parent=arrival_prod_tree_lay store=no no_caption=1

			@layout arrival_prod_left_search type=vbox parent=arrival_prod_left area_caption=Otsing closeable=1

				@layout arrival_prod_s_top_box type=vbox parent=arrival_prod_left_search

					@property arrival_prod_s_name type=textbox store=no captionside=top size=20 parent=arrival_prod_s_top_box
					@caption Nimi

					@property arrival_prod_s_code type=textbox store=no captionside=top size=20 parent=arrival_prod_s_top_box
					@caption Kood

					@property arrival_prod_s_barcode type=textbox store=no captionside=top size=20 parent=arrival_prod_s_top_box
					@caption Ribakood

					@property arrival_prod_s_cat type=select store=no captionside=top parent=arrival_prod_s_top_box
					@caption Kategooria

				@property arrival_prod_s_sbt type=submit store=no captionside=top  parent=prod_left_search value="Otsi"
				@caption Otsi


		@property arrival_products_list type=table store=no no_caption=1  parent=arrival_prod_split
		@caption Toodete nimekiri

@default group=arrivals_by_company

	@property arrivals_bc_info type=text
	@caption Info

	@property arrivals_bc_table type=table no_caption=1

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


#@default group=status_prognosis

	#@property storage_prognosis_toolbar type=toolbar no_caption=1 store=no

	#@layout storage_prognosis_split type=hbox width=20%:80%

	#	@layout storage_prognosis_left type=vbox parent=storage_prognosis_split

	#		@layout storage_prognosis_tree_lay type=vbox closeable=1 area_caption=Filtreeri parent=storage_prognosis_left

	#			@property storage_prognosis_tree type=treeview parent=storage_prognosis_tree_lay store=no no_caption=1

	#			@property storage_prognosis_tree2 type=treeview parent=storage_prognosis_tree_lay store=no no_caption=1

	#		@layout storage_prognosis_left_search type=vbox parent=storage_prognosis_left area_caption=Otsing closeable=1

	#			@property storage_prognosis_s_name type=textbox store=no captionside=top size=30 parent=storage_prognosis_left_search
	#			@caption Nimi

	#			@property storage_prognosis_s_code type=textbox store=no captionside=top size=30 parent=storage_prognosis_left_search
	#			@caption Kood

	#			@property storage_prognosis_s_barcode type=textbox store=no captionside=top size=30 parent=storage_prognosis_left_search
	#			@caption Ribakood

	#			@property storage_prognosis_s_art_cat type=select store=no captionside=top parent=storage_prognosis_left_search
	#			@caption Kategooria

	#			@property storage_prognosis_s_count type=chooser store=no captionside=top size=30 parent=storage_prognosis_left_search
	#			@caption Laoseis

	#			@property storage_prognosis_s_price type=textbox store=no captionside=top size=30  parent=storage_prognosis_left_search
	#			@caption Hind

	#			@property storage_prognosis_s_pricelist type=select store=no captionside=top  parent=storage_prognosis_left_search
	3			@caption Hinnakiri

	#			@property storage_prognosis_s_below_min type=checkbox ch_value=1 store=no captionside=top size=30  parent=storage_prognosis_left_search no_caption=1
	#			@caption Alla miinimumi

	#			@property storage_prognosis_s_show_pieces type=checkbox ch_value=1 store=no captionside=top size=30  parent=storage_prognosis_left_search no_caption=1
	#			@caption Kuva t&uuml;kkidena

	#			@property storage_prognosis_s_show_batches type=checkbox ch_value=1 store=no captionside=top size=30  parent=storage_prognosis_left_search no_caption=1
	#			@caption Kuva partiidena

	#			@property storage_prognosis_s_date type=date_select ch_value=1 store=no captionside=top size=30  parent=storage_prognosis_left_search
	#			@caption Kuup&auml;ev

	#			@property storage_prognosis_s_sales_order_status type=chooser ch_value=1 store=no captionside=top size=30  parent=storage_prognosis_left_search
	#			@caption M&uuml;&uuml;gitellimuste staatus

	#			@property storage_prognosis_s_purchase_order_status type=chooser ch_value=1 store=no captionside=top size=30  parent=storage_prognosis_left_search
	#			@caption Ostutellimuste staatus

	#			@property storage_prognosis_s_sbt type=submit store=no captionside=top  parent=storage_prognosis_left_search value="Otsi"
	#			@caption Otsi


	#	@property storage_prognosis type=table store=no no_caption=1  parent=storage_prognosis_split
	#	@caption Laoseis


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

				@property status_orders_time_tree type=treeview parent=status_orders_time_tree_lay store=no no_caption=1 group=status_orders

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

				@property status_orders_s_start type=date_select store=no captionside=top size=30  parent=status_orders_left_search
				@caption Ajavahemiku algus

				@property status_orders_s_end type=date_select store=no captionside=top size=30  parent=status_orders_left_search
				@caption Ajavahemiku l&otilde;pp

				@property status_orders_s_sbt type=submit store=no captionside=top  parent=status_orders_left_search value="Otsi"
				@caption Otsi

		@property status_orders type=table store=no no_caption=1  parent=status_orders_split

@default group=purchase_orders

	@property purchase_orders_toolbar type=toolbar no_caption=1 store=no

	@layout purchase_orders_split type=hbox width=20%:80%

		@layout purchase_orders_left type=vbox parent=purchase_orders_split

			@layout purchase_orders_tree_lay type=vbox closeable=1 area_caption=Filtreeri&nbsp;staatuse&nbsp;j&auml;rgi parent=purchase_orders_left

				@property purchase_orders_tree type=treeview parent=purchase_orders_tree_lay store=no no_caption=1

			@layout purchase_orders_time_lay type=vbox closeable=1 area_caption=Filtreeri&nbsp;ajavahemiku&nbsp;j&auml;rgi parent=purchase_orders_left

				@property purchase_orders_time_tree type=treeview no_caption=1 parent=purchase_orders_time_lay

			@layout purchase_orders_cust_lay type=vbox closeable=1 area_caption=Filtreeri&nbsp;kliendigrupi&nbsp;j&auml;rgi parent=purchase_orders_left

				@property purchase_orders_cust_tree type=treeview no_caption=1 parent=purchase_orders_cust_lay

			@layout purchase_orders_left_search type=vbox parent=purchase_orders_left area_caption=Otsing closeable=1

				@property purchase_orders_s_purchaser type=textbox store=no captionside=top size=30 parent=purchase_orders_left_search
				@caption Tarnija

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

				@property purchase_orders_s_sbt type=submit store=no captionside=top  parent=purchase_orders_left_search value="Otsi"
				@caption Otsi


		@property purchase_orders type=table store=no no_caption=1  parent=purchase_orders_split
		@caption Ostutellimused

@default group=purchase_notes

	@property purchase_notes_toolbar type=toolbar no_caption=1

	@layout purchase_notes_split type=hbox width=20%:80%

		@layout purchase_notes_left type=vbox parent=purchase_notes_split

			@layout purchase_notes_status type=vbox closeable=1 area_caption=Filtreeri&nbsp;staatuse&nbsp;j&auml;rgi parent=purchase_notes_left

				@property purchase_notes_status_tree type=treeview no_caption=1 parent=purchase_notes_status

			@layout purchase_notes_time type=vbox closeable=1 area_caption=Filtreeri&nbsp;perioodi&nbsp;j&auml;rgi parent=purchase_notes_left

				@property purchase_notes_time_tree type=treeview no_caption=1 parent=purchase_notes_time

			@layout purchase_notes_prod type=vbox closeable=1 area_caption=Filtreeri&nbsp;tootegrupi&nbsp;j&auml;rgi parent=purchase_notes_left

				@property purchase_notes_prod_tree type=treeview no_caption=1 parent=purchase_notes_prod

			@layout purchase_notes_cust type=vbox closeable=1 area_caption=Filtreeri&nbsp;kliendigrupi&nbsp;j&auml;rgi parent=purchase_notes_left

				@property purchase_notes_cust_tree type=treeview no_caption=1 parent=purchase_notes_cust

			@layout purchase_notes_left_search type=vbox closeable=1 area_caption=Filtreeri&nbsp;otsinguga parent=purchase_notes_left

				@property purchase_notes_s_acquiredby type=textbox store=no captionside=top size=30 parent=purchase_notes_left_search
				@caption Tarnija

				@property purchase_notes_s_number type=textbox store=no captionside=top size=30 parent=purchase_notes_left_search
				@caption Number

				@property purchase_notes_s_status type=chooser store=no captionside=top size=30 parent=purchase_notes_left_search
				@caption Staatus

				@property purchase_notes_s_from type=date_select store=no captionside=top parent=purchase_notes_left_search
				@caption Alates

				@property purchase_notes_s_to type=date_select store=no captionside=top size=30 parent=purchase_notes_left_search
				@caption Kuni

				@property purchase_notes_s_article type=textbox store=no captionside=top size=30  parent=purchase_notes_left_search
				@caption Artikkel

				@property purchase_notes_s_articlecode type=textbox store=no captionside=top size=30  parent=purchase_notes_left_search
				@caption Artikli kood

				@property purchase_notes_s_art_cat type=select store=no captionside=top  parent=purchase_notes_left_search
				@caption Artikli kategooria

				@property purchase_notes_s_sbt type=submit store=no captionside=top  parent=purchase_notes_left_search value="Otsi"
				@caption Otsi

		@layout purchase_notes_right type=vbox parent=purchase_notes_split

			@property purchase_notes type=table no_caption=1 parent=purchase_notes_right

@default group=purchase_bills

	@property purchase_bills_toolbar type=toolbar no_caption=1

	@layout purchase_bills_split type=hbox width=20%:80%

		@layout purchase_bills_left type=vbox parent=purchase_bills_split

			@layout purchase_bills_status type=vbox closeable=1 area_caption=Filtreeri&nbsp;staatuse&nbsp;j&auml;rgi parent=purchase_bills_left

				@property purchase_bills_status_tree type=treeview no_caption=1 parent=purchase_bills_status

			@layout purchase_bills_time type=vbox closeable=1 area_caption=Filtreeri&nbsp;perioodi&nbsp;j&auml;rgi parent=purchase_bills_left

				@property purchase_bills_time_tree type=treeview no_caption=1 parent=purchase_bills_time

			@layout purchase_bills_prod type=vbox closeable=1 area_caption=Filtreeri&nbsp;tootegrupi&nbsp;j&auml;rgi parent=purchase_bills_left

				@property purchase_bills_prod_tree type=treeview no_caption=1 parent=purchase_bills_prod

			@layout purchase_bills_cust type=vbox closeable=1 area_caption=Filtreeri&nbsp;kliendigrupi&nbsp;j&auml;rgi parent=purchase_bills_left

				@property purchase_bills_cust_tree type=treeview no_caption=1 parent=purchase_bills_cust

			@layout purchase_bills_left_search type=vbox closeable=1 area_caption=Filtreeri&nbsp;otsinguga parent=purchase_bills_left

				@property purchase_bills_s_acquiredby type=textbox store=no captionside=top size=30 parent=purchase_bills_left_search
				@caption Tarnija

				@property purchase_bills_s_number type=textbox store=no captionside=top size=30 parent=purchase_bills_left_search
				@caption Number

				@property purchase_bills_s_status type=chooser store=no captionside=top size=30 parent=purchase_bills_left_search
				@caption Staatus

				@property purchase_bills_s_from type=date_select store=no captionside=top parent=purchase_bills_left_search
				@caption Alates

				@property purchase_bills_s_to type=date_select store=no captionside=top size=30 parent=purchase_bills_left_search
				@caption Kuni

				@property purchase_bills_s_article type=textbox store=no captionside=top size=30  parent=purchase_bills_left_search
				@caption Artikkel

				@property purchase_bills_s_articlecode type=textbox store=no captionside=top size=30  parent=purchase_bills_left_search
				@caption Artikli kood

				@property purchase_bills_s_art_cat type=select store=no captionside=top  parent=purchase_bills_left_search
				@caption Artikli kategooria

				@property purchase_bills_s_sbt type=submit store=no captionside=top  parent=purchase_bills_left_search value="Otsi"
				@caption Otsi

		@layout purchase_bills_right type=vbox parent=purchase_bills_split

			@property purchase_bills type=table no_caption=1 parent=purchase_bills_right


@default group=sell_orders

	@property sell_orders_toolbar type=toolbar no_caption=1 store=no

	@layout sell_orders_split type=hbox width=25%:75%

		@layout sell_orders_left type=vbox parent=sell_orders_split

			@layout sell_orders_channel_lay type=vbox closeable=1 area_caption=Filtreeri&nbsp;m&uuml;&uuml;gikanali&nbsp;kaupa parent=sell_orders_left

				@property sell_orders_channel_tree type=treeview parent=sell_orders_channel_lay store=no no_caption=1

			@layout sell_orders_tree_lay type=vbox closeable=1 area_caption=Filtreeri&nbsp;staatuste&nbsp;kaupa parent=sell_orders_left

				@property sell_orders_tree type=treeview parent=sell_orders_tree_lay store=no no_caption=1

			@layout sell_orders_time_lay type=vbox closeable=1 area_caption=Filtreeri&nbsp;tellimuse&nbsp;esitamise&nbsp;aja&nbsp;j&auml;rgi parent=sell_orders_left

				@property sell_orders_time_tree type=treeview no_caption=1 parent=sell_orders_time_lay

			@layout sell_orders_cust_lay type=vbox closeable=1 area_caption=Filtreeri&nbsp;kliendigrupi&nbsp;j&auml;rgi parent=sell_orders_left

				@property sell_orders_cust_tree type=treeview no_caption=1 parent=sell_orders_cust_lay

			@layout sell_orders_left_search type=vbox parent=sell_orders_left area_caption=Tellimuste&nbsp;otsing closeable=1

				@property sell_orders_s_buyer type=textbox store=no captionside=top size=30 parent=sell_orders_left_search
				@caption Kliendi nimi

				@property sell_orders_s_number type=textbox store=no captionside=top size=30 parent=sell_orders_left_search
				@caption Tellimuse number

				@property sell_orders_s_purchaser_id type=textbox store=no captionside=top size=30 parent=sell_orders_left_search
				@caption AW kliendikood

				@property sell_orders_s_purchaser_other_id type=textbox store=no captionside=top size=30 parent=sell_orders_left_search
				@caption Naabri kliendikood

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




@default group=campaigns

	@property campaigns_toolbar type=toolbar no_caption=1 store=no
	@layout campaigns_split type=hbox width=20%:80%
		@layout campaigns_left type=vbox parent=campaigns_split

			@layout campaigns_time_lay type=vbox closeable=1 area_caption=Ajavahemik parent=campaigns_left
				@property campaigns_time_tree type=treeview no_caption=1 parent=campaigns_time_lay

			@layout campaigns_cust_lay type=vbox closeable=1 area_caption=Kasutajagrupid parent=campaigns_left
				@property campaigns_groups_tree type=treeview no_caption=1 parent=campaigns_cust_lay

			@layout campaigns_product_lay type=vbox closeable=1 area_caption=Tooted parent=campaigns_left
				@property campaigns_product_tree type=treeview no_caption=1 parent=campaigns_product_lay
		@layout campaigns_right type=vbox parent=campaigns_split
			@property campaigns type=table store=no no_caption=1  parent=campaigns_right
			@caption Kampaaniad


@default group=sales_notes

	@property sales_notes_toolbar type=toolbar no_caption=1

	@layout sales_notes_split type=hbox width=20%:80%

		@layout sales_notes_left type=vbox parent=sales_notes_split

			@layout sales_notes_status type=vbox closeable=1 area_caption=Filtreeri&nbsp;staatuse&nbsp;j&auml;rgi parent=sales_notes_left

				@property sales_notes_status_tree type=treeview no_caption=1 parent=sales_notes_status

			@layout sales_notes_time type=vbox closeable=1 area_caption=Filtreeri&nbsp;perioodi&nbsp;j&auml;rgi parent=sales_notes_left

				@property sales_notes_time_tree type=treeview no_caption=1 parent=sales_notes_time

			@layout sales_notes_prod type=vbox closeable=1 area_caption=Filtreeri&nbsp;tootegrupi&nbsp;j&auml;rgi parent=sales_notes_left

				@property sales_notes_prod_tree type=treeview no_caption=1 parent=sales_notes_prod

			@layout sales_notes_cust type=vbox closeable=1 area_caption=Filtreeri&nbsp;kliendigrupi&nbsp;j&auml;rgi parent=sales_notes_left

				@property sales_notes_cust_tree type=treeview no_caption=1 parent=sales_notes_cust

			@layout sales_notes_left_search type=vbox closeable=1 area_caption=Filtreeri&nbsp;otsinguga parent=sales_notes_left

				@property sales_notes_s_acquiredby type=textbox store=no captionside=top size=30 parent=sales_notes_left_search
				@caption Tarnija

				@property sales_notes_s_number type=textbox store=no captionside=top size=30 parent=sales_notes_left_search
				@caption Number

				@property sales_notes_s_status type=chooser store=no captionside=top size=30 parent=sales_notes_left_search
				@caption Staatus

				@property sales_notes_s_from type=date_select store=no captionside=top parent=sales_notes_left_search
				@caption Alates

				@property sales_notes_s_to type=date_select store=no captionside=top size=30 parent=sales_notes_left_search
				@caption Kuni

				@property sales_notes_s_article type=textbox store=no captionside=top size=30  parent=sales_notes_left_search
				@caption Artikkel

				@property sales_notes_s_articlecode type=textbox store=no captionside=top size=30  parent=sales_notes_left_search
				@caption Artikli kood

				@property sales_notes_s_art_cat type=select store=no captionside=top  parent=sales_notes_left_search
				@caption Artikli kategooria

				@property sales_notes_s_sbt type=submit store=no captionside=top  parent=sales_notes_left_search value="Otsi"
				@caption Otsi

		@layout sales_notes_right type=vbox parent=sales_notes_split

			@property sales_notes type=table no_caption=1 parent=sales_notes_right

@default group=sales_bills

	@property sales_bills_toolbar type=toolbar no_caption=1

	@layout sales_bills_split type=hbox width=20%:80%

		@layout sales_bills_left type=vbox parent=sales_bills_split

			@layout sales_bills_status type=vbox closeable=1 area_caption=Filtreeri&nbsp;staatuse&nbsp;j&auml;rgi parent=sales_bills_left

				@property sales_bills_status_tree type=treeview no_caption=1 parent=sales_bills_status

			@layout sales_bills_time type=vbox closeable=1 area_caption=Filtreeri&nbsp;perioodi&nbsp;j&auml;rgi parent=sales_bills_left

				@property sales_bills_time_tree type=treeview no_caption=1 parent=sales_bills_time

			@layout sales_bills_prod type=vbox closeable=1 area_caption=Filtreeri&nbsp;tootegrupi&nbsp;j&auml;rgi parent=sales_bills_left

				@property sales_bills_prod_tree type=treeview no_caption=1 parent=sales_bills_prod

			@layout sales_bills_cust type=vbox closeable=1 area_caption=Filtreeri&nbsp;kliendigrupi&nbsp;j&auml;rgi parent=sales_bills_left

				@property sales_bills_cust_tree type=treeview no_caption=1 parent=sales_bills_cust

			@layout sales_bills_left_search type=vbox closeable=1 area_caption=Filtreeri&nbsp;otsinguga parent=sales_bills_left

				@property sales_bills_s_acquiredby type=textbox store=no captionside=top size=30 parent=sales_bills_left_search
				@caption Tarnija

				@property sales_bills_s_number type=textbox store=no captionside=top size=30 parent=sales_bills_left_search
				@caption Number

				@property sales_bills_s_status type=chooser store=no captionside=top size=30 parent=sales_bills_left_search
				@caption Staatus

				@property sales_bills_s_from type=date_select store=no captionside=top parent=sales_bills_left_search
				@caption Alates

				@property sales_bills_s_to type=date_select store=no captionside=top size=30 parent=sales_bills_left_search
				@caption Kuni

				@property sales_bills_s_article type=textbox store=no captionside=top size=30  parent=sales_bills_left_search
				@caption Artikkel

				@property sales_bills_s_articlecode type=textbox store=no captionside=top size=30  parent=sales_bills_left_search
				@caption Artikli kood

				@property sales_bills_s_art_cat type=select store=no captionside=top  parent=sales_bills_left_search
				@caption Artikli kategooria

				@property sales_bills_s_sbt type=submit store=no captionside=top  parent=sales_bills_left_search value="Otsi"
				@caption Otsi

		@layout sales_bills_right type=vbox parent=sales_bills_split

			@property sales_bills type=table no_caption=1 parent=sales_bills_right

	@property clients_toolbar type=toolbar no_caption=1 group=purchase_clients,sales_clients

	@layout clients_split type=hbox width=20%:80% group=purchase_clients,sales_clients

		@layout clients_left type=vbox group=purchase_clients,sales_clients parent=clients_split

			@layout clients_groups_lay type=vbox area_caption=Kliendigrupp group=purchase_clients,sales_clients parent=clients_left

				@property clients_groups_tree type=treeview no_caption=1  group=purchase_clients,sales_clients parent=clients_groups_lay

			@layout clients_alphabet_lay type=vbox area_caption=Nimi group=purchase_clients,sales_clients parent=clients_left

				@property clients_alphabet_tree type=treeview no_caption=1  group=purchase_clients,sales_clients parent=clients_alphabet_lay

			@layout clients_status_lay type=vbox area_caption=Staatus group=purchase_clients,sales_clients parent=clients_left

				@property clients_status_tree type=treeview no_caption=1 group=purchase_clients,sales_clients parent=clients_status_lay

			@layout clients_start_lay type=vbox area_caption=Kliendisuhte&nbsp;algus group=purchase_clients,sales_clients parent=clients_left

				@property clients_time_tree type=treeview no_caption=1 group=purchase_clients,sales_clients parent=clients_start_lay

		@property clients_tbl type=table no_caption=1 group=purchase_clients,sales_clients parent=clients_split

@default group=shop_orders

	@property shop_orders_toolbar type=toolbar no_caption=1

	@layout shop_orders_split type=hbox width=20%:80%

		@layout shop_orders_left type=vbox parent=shop_orders_split

			@layout shop_orders_tree type=vbox parent=shop_orders_left area_caption=Poe&nbsp;tellimuste&nbsp;staatused closeable=1

				@property shop_orders_tree type=treeview no_caption=1 parent=shop_orders_tree

			@layout shop_orders_search type=vbox closeable=1 area_caption=Poe&nbsp;tellimuste&nbsp;otsing parent=shop_orders_left

				@property shop_orders_s_uname type=textbox parent=shop_orders_search store=no captionside=top size=20
				@caption Tellija kasutajanimi

				@property shop_orders_s_pname type=textbox parent=shop_orders_search store=no captionside=top size=20
				@caption Tellija isikunimi

				@property shop_orders_s_oname type=textbox parent=shop_orders_search store=no captionside=top size=20
				@caption Organisatsiooni nimi

				@property shop_orders_s_oid type=textbox size=8 parent=shop_orders_search store=no captionside=top size=20
				@caption Tellimuse ID

				@property shop_orders_s_prod type=textbox parent=shop_orders_search store=no captionside=top size=20
				@caption Toote nimi

				@property shop_orders_s_from type=date_select parent=shop_orders_search store=no captionside=top
				@caption Tellimuse ajavahemik (alates)

				@property shop_orders_s_to type=date_select parent=shop_orders_search store=no captionside=top
				@caption Tellimuse ajavahemik (kuni)

				@property osearch_submit type=submit parent=shop_orders_search
				@caption Otsi


		@layout shop_orders_right type=vbox parent=shop_orders_split

			@property shop_orders_table type=table no_caption=1 parent=shop_orders_right

@default group=order_undone

	@property order_undone_tb type=toolbar no_caption=1
	@property order_undone type=table no_caption=1

@default group=order_orderer_cos

	@layout hbox_oc type=hbox

		@property order_orderer_cos_tree type=text store=no parent=hbox_oc no_caption=1
		@property order_orderer_cos type=table store=no parent=hbox_oc no_caption=1

// search tab

@default group=search_search
	@property search_tb type=toolbar store=no no_caption=1
	@caption Otsingu toolbar

	@property search_form type=callback callback=callback_get_search_form submit_method=get store=no
	@caption Otsinguvorm

	@property search_res type=table store=no no_caption=1
	@caption Otsingu tulemused

	@property search_cur_ord_text type=text store=no no_caption=1
	@caption Hetke tellimus text

	@property search_cur_ord type=table store=no no_caption=1
	@caption Hetke tellimus tabel


@property order_current_toolbar type=toolbar no_caption=1 group=order_current store=no
@property order_current_table type=table store=no group=order_current no_caption=1

@property order_current_org type=popup_search field=meta method=serialize group=order_current clid=CL_CRM_COMPANY
@caption Tellija organisatsioon

@property order_current_person type=popup_search field=meta method=serialize group=order_current clid=CL_CRM_PERSON
@caption Tellija isik

@property order_current_form type=callback callback=callback_get_order_current_form store=no group=order_current
@caption Tellimuse info vorm


@default group=stats_general

	@property stats_toolbar type=toolbar no_caption=1 store=no

	@layout stats_split type=hbox width=20%:80%

		@layout stats_left type=vbox parent=stats_split

#			@layout stats_tree_lay type=vbox closeable=1 area_caption=Filtreeri parent=stats_left
#				@property stats_tree type=treeview parent=stats_tree_lay store=no no_caption=1

			@layout stats_time_lay type=vbox closeable=1 area_caption=Ajavahemik parent=stats_left
				@property stats_time_tree type=treeview no_caption=1 parent=stats_time_lay

			@layout stats_prod_lay type=vbox closeable=1 area_caption=Toode parent=stats_left
				@property stats_prod_tree type=treeview no_caption=1 parent=stats_prod_lay
				@property stats_cat_tree type=treeview parent=stats_prod_lay store=no no_caption=1

		@layout stats_right type=vbox parent=stats_split

			@property stats_table type=table store=no no_caption=1 parent=stats_right
				@caption Statistika tabel

@default group=stats_balance

	@property stats_balance_toolbar type=toolbar no_caption=1 store=no

	@layout stats_balance_split type=hbox width=20%:80%

		@layout stats_balance_left type=vbox parent=stats_balance_split

			@layout stats_balance_time_lay type=vbox closeable=1 area_caption=Ajavahemik parent=stats_balance_left
				@property stats_balance_time_tree type=treeview no_caption=1 parent=stats_balance_time_lay

			@layout stats_balance_prod_lay type=vbox closeable=1 area_caption=Toode parent=stats_balance_left
				@property stats_balance_prod_tree type=treeview no_caption=1 parent=stats_balance_prod_lay
				@property stats_balance_cat_tree type=treeview parent=stats_balance_prod_lay store=no no_caption=1

		@layout stats_balance_right type=vbox parent=stats_balance_split

			@property stats_balance_table type=table store=no no_caption=1 parent=stats_balance_right
				@caption Statistika tabel


@default group=stats_day

	@property stats_day_toolbar type=toolbar no_caption=1 store=no

	@layout stats_day_split type=hbox width=20%:80%

		@layout stats_day_left type=vbox parent=stats_day_split

			@layout stats_day_tree_lay type=vbox closeable=1 area_caption=Filtreeri parent=stats_day_left
				@property stats_day_tree type=treeview parent=stats_day_tree_lay store=no no_caption=1

			@layout stats_day_time_lay type=vbox closeable=1 area_caption=Ajavahemik parent=stats_day_left
				@property stats_day_time_tree type=treeview no_caption=1 parent=stats_day_time_lay

			@layout stats_day_prod_lay type=vbox closeable=1 area_caption=Toode parent=stats_day_left
				@property stats_day_prod_tree type=treeview no_caption=1 parent=stats_day_prod_lay
				@property stats_day_cat_tree type=treeview parent=stats_day_prod_lay store=no no_caption=1

		@layout stats_day_right type=vbox parent=stats_day_split

			@property stats_day_table type=table store=no no_caption=1 parent=stats_day_right
				@caption Statistika tabel

@default group=stats_inventory_repair

	@property stats_inventory_repair_toolbar type=toolbar no_caption=1 store=no

	@layout stats_inventory_repair_split type=hbox width=20%:80%

		@layout stats_inventory_repair_left type=vbox parent=stats_inventory_repair_split

			@layout stats_inventory_repair_time_lay type=vbox closeable=1 area_caption=Ajavahemik parent=stats_inventory_repair_left
				@property stats_inventory_repair_time_tree type=treeview no_caption=1 parent=stats_inventory_repair_time_lay

			@layout stats_inventory_repair_prod_lay type=vbox closeable=1 area_caption=Toode parent=stats_inventory_repair_left
				@property stats_inventory_repair_prod_tree type=treeview no_caption=1 parent=stats_inventory_repair_prod_lay
				@property stats_inventory_repair_cat_tree type=treeview parent=stats_inventory_repair_prod_lay store=no no_caption=1

		@layout stats_inventory_repair_right type=vbox parent=stats_inventory_repair_split

			@property stats_inventory_rapair_table type=table store=no no_caption=1 parent=stats_inventory_repair_right
				@caption Statistika tabel


// general subs
	@groupinfo general_sub parent=general caption="&Uuml;ldine"
	@groupinfo general_settings parent=general caption="Seaded"
	@groupinfo productgroups caption="Tootegrupid" submit=no parent=general
	@groupinfo search_search caption="A Otsing" parent=general submit_method=get
	@groupinfo order_current parent=general caption="A Pakkumine"

@groupinfo articles caption="Artiklid"

	@groupinfo product_management caption="Artiklid" submit=no parent=articles
	@groupinfo category caption="Tootekategooriad" submit=no parent=articles
	@groupinfo packets caption="Paketid" submit=no parent=articles submit_method=get
	@groupinfo brand caption="Kaubam&auml;rk" submit=no parent=articles
	@groupinfo products caption="A Artiklid" submit=no parent=articles

@groupinfo status caption="Laoseis"

	@groupinfo status_status caption="Laoseis" parent=status
#	@groupinfo status_prognosis caption="Prognoos" parent=status
	@groupinfo storage_movements parent=status caption="Liikumised" submit=no
	@groupinfo storage_writeoffs parent=status caption="Mahakandmised" submit=no
	@groupinfo status_inventories caption="Inventuurid" parent=status
	@groupinfo status_orders caption="Vajadused" parent=status

@groupinfo purchases caption="Ost"
	@groupinfo purchase_orders caption="Ostutellimused" parent=purchases
	@groupinfo purchase_notes caption="Ostusaatelehed" parent=purchases
	@groupinfo purchase_bills caption="Ostuarved" parent=purchases
	@groupinfo purchase_clients caption="Ostukliendid" parent=purchases
	@groupinfo arrivals caption="Tarneajad" submit=no parent=purchases
	@groupinfo arrivals_by_company caption="Firmade tarneajad" parent=purchases

@groupinfo sales caption="M&uuml;&uuml;k"

	@groupinfo sell_orders caption="M&uuml;&uuml;gitellimused" parent=sales
	@groupinfo sales_notes caption="M&uuml;&uuml;gisaatelehed" parent=sales
	@groupinfo sales_bills caption="M&uuml;&uuml;giarved" parent=sales
	@groupinfo sales_clients caption="M&uuml;&uuml;gikliendid" parent=sales
	@groupinfo shop_orders caption="Poe tellimused" parent=sales
	@groupinfo order_undone parent=sales caption="T&auml;itmata poe tellimused"
	@groupinfo channels parent=sales caption="M&uuml;&uuml;gikanalid"
	@groupinfo order_orderer_cos parent=sales caption="A Tellijad"
	@groupinfo campaigns parent=sales caption="Kampaaniad" submit=no

@groupinfo stats caption="Aruanded"

	@groupinfo stats_general caption="&Uuml;ldine aruanne" parent=stats
	@groupinfo stats_balance caption="Saldo aruanne" parent=stats
	@groupinfo stats_day caption="P&auml;evade aruanne" parent=stats
	@groupinfo stats_inventory_repair caption="Inventuuri parandused" parent=stats



////////// reltypes
@reltype CONFIG value=1 clid=CL_SHOP_WAREHOUSE_CONFIG
@caption Konfiguratsioon

@reltype PRODUCT value=2 clid=CL_SHOP_PRODUCT
@caption Toode

@reltype PACKET value=2 clid=CL_SHOP_PACKET
@caption Pakett

@reltype STORAGE_INCOME value=3 clid=CL_SHOP_WAREHOUSE_RECEPTION
@caption Lao sissetulek

@reltype STORAGE_EXPORT value=4 clid=CL_SHOP_WAREHOUSE_EXPORT
@caption Lao v&auml;jaminek

@reltype ORDER value=5 clid=CL_SHOP_ORDER
@caption Tellimus

@reltype ORDER_CENTER value=6 clid=CL_SHOP_ORDER_CENTER
@caption Tellimiskeskkond

@reltype EMAIL value=7 clid=CL_ML_MEMBER
@caption Saada tellimused

@reltype CFGMANAGER value=8 clid=CL_CFGMANAGER
@caption Seadete haldur

@reltype CAT_ENTRY_FORM value=9 clid=CL_CFGFORM
@caption Kategooria lisamise vorm

@reltype PURCHASE_ORDER value=10 clid=CL_SHOP_PURCHASE_ORDER
@caption Ostutellimus

@reltype SELL_ORDER value=11 clid=CL_SHOP_SELL_ORDER
@caption M&uuml;&uuml;gitellimus

@reltype INVENTORY value=12 clid=CL_SHOP_WAREHOUSE_INVENTORY
@caption Inventuur

*/
define("QUANT_UNDEFINED", 0);
define("QUANT_NEGATIVE", 1);
define("QUANT_ZERO", 2);
define("QUANT_POSITIVE", 3);

define("STORAGE_FILTER_BILLS", 1);
define("STORAGE_FILTER_DNOTES", 2);
define("STORAGE_FILTER_ALL", 3);

define("STORAGE_FILTER_CONFIRMED", 1);
define("STORAGE_FILTER_UNCONFIRMED", 2);
define("STORAGE_FILTER_CONFIRMATION_ALL", 10);

define("STORAGE_FILTER_INCOME", 1);
define("STORAGE_FILTER_EXPORT", 2);

class shop_warehouse extends class_base
{
	function shop_warehouse()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_warehouse",
			"clid" => shop_warehouse_obj::CLID
		));
	}

	function callback_on_load($arr)
	{
		if(isset($arr["request"]["id"]) and $this->can("view", $arr["request"]["id"]))
		{
			$obj = obj($arr["request"]["id"]);
			if($cfgmanager = $obj->get_first_conn_by_reltype("RELTYPE_CFGMANAGER"))
			{
				$this->cfgmanager = $cfgmanager->prop("to");
			}
		}
	}

/*	function callback_mod_layout(&$arr)
	{
		if($arr["name"] === "product_managementtree_lay2")
		{
			$types = $arr["obj_inst"]->get_product_category_types();
			if(!$types->count())
			{
				return false;
			}
		}

		return true;
	}*/

	function _get_stats_time_tree($arr)
	{
		$tv =& $arr["prop"]["vcl_inst"];
		$var = "timespan";
		$tv->set_selected_item(isset($arr["request"][$var]) ? $arr["request"][$var] : "period_week");

		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"persist_state" => true,
			"tree_id" => "proj_bills_time_tree",
		));

		$tv->add_item(0,array(
			"name" => t("K&otilde;ik ajavahemikud"),
			"id" => "all_time",
			"url" => aw_url_change_var($var, "all_time"),
		));

		$branches = array(
			"period_last_week" => t("Eelmine n&auml;dal"),
			"period_week" => t("K&auml;esolev n&auml;dal"),
			"period_last_last" => t("&Uuml;leelmine kuu"),
			"period_last" => t("Eelmine kuu"),
			"period_current" => t("K&auml;esolev kuu"),
			"period_next" => t("J&auml;rgmine kuu"),
			"period_lastyear" => t("Eelmine aasta"),
			"period_year" => t("K&auml;esolev aasta"),
		);

		foreach($branches as $id => $caption)
		{
			$tv->add_item("all_time", array(
				"id" => $id,
				"name" => $caption,
				"url" => aw_url_change_var(array(
					$var => $id,
				)),
			));
		}
	}

	function _get_clients_time_tree($arr)
	{
		$tv =& $arr["prop"]["vcl_inst"];
		$var = "timespan";
		$tv->set_selected_item(isset($arr["request"][$var]) ? $arr["request"][$var] : "period_week");

		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"persist_state" => true,
			"tree_id" => "proj_bills_time_tree",
		));

		$tv->add_item(0,array(
			"name" => t("K&otilde;ik ajavahemikud"),
			"id" => "all_time",
			"url" => aw_url_change_var($var, "all_time"),
		));

		$branches = array(
			"period_last_week" => t("Eelmine n&auml;dal"),
			"period_week" => t("K&auml;esolev n&auml;dal"),
			"period_last_last" => t("&Uuml;leelmine kuu"),
			"period_last" => t("Eelmine kuu"),
			"period_current" => t("K&auml;esolev kuu"),
//			"period_next" => t("J&auml;rgmine kuu"),
			"period_lastyear" => t("Eelmine aasta"),
			"period_year" => t("K&auml;esolev aasta"),
		);

		foreach($branches as $id => $caption)
		{
			$tv->add_item("all_time", array(
				"id" => $id,
				"name" => $caption,
				"url" => aw_url_change_var(array(
					$var => $id,
				)),
			));
		}
	}

	function _get_product_managements_count($arr)
	{
		$arr["prop"]["options"] = array(t("Laoseis ebaoluline") , t("Laos olemas"));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		if($arr["request"]["action"] != "new" and $arr["prop"]["name"] === "no_new_config")
		{
			return PROP_IGNORE;
		}
		if (!$arr["new"] and !$this->_init_view($arr))
		{
			return PROP_OK;
		}
		switch($prop["name"])
		{
			case "packets_s_name":
			case "packets_s_code":
			case "packets_s_barcode":
			case "packets_s_cat":
			case "packets_s_count":
			case "packets_s_price_from":
			case "packets_s_pricelist":
				if(automatweb::$request->arg_isset($prop["name"]))
				{
					$prop["value"] = automatweb::$request->arg($prop["name"]);
				}
				break;
			case "packets_s_created_from":
			case "packets_s_created_to":
				if(automatweb::$request->arg_isset($prop["name"]))
				{
					$prop["value"] = automatweb::$request->arg($prop["name"]);
				}
				$prop["format"] = array("day_textbox", "month_textbox", "year_textbox");
				if(empty($prop["value"]))
				{
					$prop["value"] = -1;
				}
				break;
			case "packets_s_active":
				if(automatweb::$request->arg_isset($prop["name"]))
				{
					$prop["value"] = automatweb::$request->arg($prop["name"]);
				}
				$prop["options"] = array(t("K&otilde;ik"), t("Mitteaktiivsed") , t("Aktiivsed"));
				break;
			case "packets_cat_tree":
			case "product_managementcat_tree":
				$prop["value"] = $this->_get_managementcat_tree($arr);
				break;
			case "stats_inventory_repair_time_tree":
			case "stats_balance_time_tree":
			case "stats_day_time_tree":
			case "stats_time_tree":
				return $this->_get_stats_time_tree($arr);
				break;
			case "purchase_notes_s_from":
			case "purchase_bills_s_from":
			case "sales_notes_s_from":
			case "sales_bills_s_from":
			case "purchase_orders_s_from":
			case "sell_orders_s_from":
				return $this->_get_status_orders_s_start($arr);
				break;
			case "purchase_notes_s_to":
			case "purchase_bills_s_to":
			case "sales_notes_s_to":
			case "sales_bills_s_to":
			case "purchase_orders_s_to":
			case "sell_orders_s_to":
				return $this->_get_status_orders_s_end($arr);
				break;
			case "sales_bills_s_status":
			case "purchase_bills_s_status":
				$prop["options"] = get_instance(CL_CRM_BILL)->states + array("" => t("K&otilde;ik"));
				$prop["value"] = $arr["request"][$prop["name"]] ? $arr["request"][$prop["name"]] : "";
				$prop["orient"] = "vertical";
				return $retval;
				break;
			case "purchase_notes_s_acquiredby":
			case "purchase_bills_s_acquiredby":
			case "sales_notes_s_acquiredby":
			case "sales_bills_s_acquiredby":
			case "purchase_orders_s_purchaser":
			case "sell_orders_s_buyer":
				if(!empty($arr["request"][$prop["name"]]))
				{
					$prop["value"] = $arr["request"][$prop["name"]];
				}
				elseif(!empty($arr["request"]["filt_cust"]))
				{
					$v = $arr["request"]["filt_cust"];
					if($this->can("view", $v))
					{
						$co = obj($v);
					}
					if($co and $co->is_a(crm_company_obj::CLID))
					{
						$prop["value"] = $co->name();
					}
				}
				return $retval;
				break;

		}
		if($ret = $this->process_search_param($arr))
		{
			return $ret;
		}
		switch($prop["name"])
		{
			case "shop_orders_s_uname":
			case "shop_orders_s_pname":
			case "shop_orders_s_oname":
			case "shop_orders_s_oid":
			case "shop_orders_s_prod":
				$prop["value"] = $arr["request"][$prop["name"]];
				break;
			case "shop_orders_s_from":
			case "shop_orders_s_to":
				$prop["format"] = array("day_textbox", "month_textbox", "year_textbox");
				$prop["value"] = $arr["request"][$prop["name"]] ?  date_edit::get_timestamp($arr["request"][$prop["name"]]) : time();
				break;

			case "products_toolbar":
				$this->mk_prod_toolbar($arr);
				break;

			case "productgroups_toolbar":
				$this->mk_prodg_toolbar($arr);
				break;

			case "productgroups_tree":
				$this->mk_prodg_tree($arr);
				break;

			case "productgroups_list":
				$this->do_prodg_list($arr);
				break;

			case "packets_toolbar":
				$this->mk_pkt_toolbar($arr);
				break;

			case "storage_list":
				$this->do_storage_list_tbl($arr);
				break;
			case "stats_day_prod_tree":
			case "stats_balance_prod_tree":
			case "stats_prod_tree":
			case "arrival_prod_tree":
			case "stats_inventory_repair_prod_tree":
			case "prod_tree":
				$retval = $this->get_prod_tree($arr);
				break;
			case "stats_day_cat_tree":
			case "stats_balance_cat_tree":
			case "stats_cat_tree":
			case "arrival_prod_cat_tree":
			case "stats_inventory_repair_cat_tree":
			case "prod_cat_tree":
				$retval = $this->mk_prodg_tree($arr);
				break;

			case "products_list":
				$this->get_products_list($arr);
				break;

			case "order_undone":
				$this->do_order_undone_tbl($arr);
				break;
			case "order_undone_tb":
				$this->do_order_undone_tb($arr);
				break;

			case "order_orderer_cos":
				$this->do_order_orderer_cos_tbl($arr);
				break;

			case "order_orderer_cos_tree":
				$this->do_order_orderer_cos_tree($arr);
				break;

			case "order_current_toolbar":
				$this->do_order_cur_tb($arr);
				break;

			case "order_current_table":
			case "search_cur_ord":
				$this->save_ord_cur_tbl($arr);
				$this->do_order_cur_table($arr);
				break;

			case "search_res":
				$this->do_search_res_tbl($arr);
				break;

			case "search_tb":
				$this->do_search_tb($arr);
				break;

			case "search_cur_ord_text":
				$prop["value"] = t("<br><br>Hetkel pakkumises olevad tooted:");
				break;
			case "packets_s_sbt":
				/*
					The HTML returned is too big and causes JS to crash!
				*/
				//	$prop['onclick'] = "search_packets();";
				break;
		};
		return $retval;
	}

	function process_search_param(&$arr)
	{
		$prop = &$arr["prop"];
		if($tmp = $this->is_search_param($prop["name"]))
		{
			switch($tmp["var"])
			{
				case "cat":
				case "art_cat":
					$prop["options"] = $this->get_cat_picker($arr);
					if (!empty($arr["request"][$prop["name"]]))
					{
						$prop["value"] = $arr["request"][$prop["name"]];
					}
					elseif($tf = automatweb::$request->arg("pgtf"))
					{
						$prop["value"] = $tf;
					}
					break;

				case "count":
					if($this->no_count)
					{
						return PROP_IGNORE;
					}
					$prop["options"] = array(
						QUANT_UNDEFINED => t("K&otilde;ik"),
						QUANT_NEGATIVE => t("< 0"),
						QUANT_ZERO => t("= 0"),
						QUANT_POSITIVE => t("> 0"),
					);
					if (empty($arr["request"][$prop["name"]]))
					{
						$prop["value"] = 0;
					}
					else
					{
						$prop["value"] = $arr["request"][$prop["name"]];
					}
					break;

				case "pricelist":
					$prop["options"] = $this->get_pricelist_picker();
					if (isset($arr["request"][$prop["name"]]))
					{
						$prop["value"] = $arr["request"][$prop["name"]];
					}
					elseif($this->def_price_list)
					{
						$prop["value"] = $this->def_price_list;
					}
					break;

				case "to":
				case "from":
					$prop["value"] = isset($arr["request"][$prop["name"]]) ? date_edit::get_timestamp($arr["request"][$prop["name"]]) : 0;
					$prop["format"] = array("day_textbox", "month_textbox", "year_textbox");
					break;

				case "warehouse":
					$prop["options"] = array(t("--vali--"));
					$ol = new object_list(array(
						"class_id" => CL_SHOP_WAREHOUSE,
					));
					$prop["options"] += $ol->names();
					$prop["value"] = !empty($arr["request"][$prop["name"]]) ? $arr["request"][$prop["name"]] : $arr["obj_inst"]->id();
					break;

				case "sales_order_status":
				case "purchase_order_status":
				case "status":
					if(in_array($prop["name"], array("purchase_orders_s_status", "sell_orders_s_status")))
					{
						$prop["options"] = array(STORAGE_FILTER_CONFIRMATION_ALL => t("K&otilde;ik")) + get_instance(CL_SHOP_PURCHASE_ORDER)->states;
					}
					else
					{
						$prop["options"] = array(
							STORAGE_FILTER_CONFIRMATION_ALL => t("K&otilde;ik"),
							STORAGE_FILTER_CONFIRMED => t("Kinnitatud"),
							STORAGE_FILTER_UNCONFIRMED => t("Kinnitamata"),
						);
					}
					$prop["value"] =  empty($arr["request"][$prop["name"]]) ? STORAGE_FILTER_CONFIRMATION_ALL : $arr["request"][$prop["name"]];
					break;

				case "direction":
					$prop["options"] = array(
						STORAGE_FILTER_ALL => t("K&otilde;ik"),
						STORAGE_FILTER_INCOME => t("Sissetulekud"),
						STORAGE_FILTER_EXPORT => t("V&auml;ljaminekud"),
					);
					$prop["value"] = !empty($arr["request"][$prop["name"]]) ? $arr["request"][$prop["name"]] : STORAGE_FILTER_ALL;
					break;

				case "type":
					$prop["options"] = array(
						STORAGE_FILTER_ALL => t("K&otilde;ik"),
						STORAGE_FILTER_BILLS => t("Arved"),
						STORAGE_FILTER_DNOTES => t("Saatelehed"),
					);
					$prop["value"] = !empty($arr["request"][$prop["name"]]) ? $arr["request"][$prop["name"]] : STORAGE_FILTER_ALL;
					break;

				case "date":
					if(!$arr["request"][$prop["name"]])
					{
						$prop["value"] = -1;
					}
					else
					{
						$prop["value"] = $arr["request"][$prop["name"]];
					}
					break;

				default:
					$prop["value"] = automatweb::$request->arg($prop["name"]);
			}
			return PROP_OK;
		}
		return false;
	}

	function set_property($arr = array())
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "category_list":
				foreach($_POST["ord"] as $cat => $val)
				{
					if($this->can("view" , $cat))
					{
						$category = obj($cat);
						$category->set_ord($val);
						$category->save();
					}
				}
				break;
			case "storage_income":
				$this->save_storage_inc_tbl($arr);
				break;

			case "storage_export":
				$this->save_storage_exp_tbl($arr);
				break;

			case "products_list":
				$this->do_update_prod($arr);
				break;

			case "order_current_table":
			case "search_cur_ord":
				$this->save_ord_cur_tbl($arr, true);
				break;

			case "order_current_org":
				if ($arr["obj_inst"]->prop("order_current_org") != $arr["request"]["order_current_org"])
				{
					$this->upd_ud = true;
				}
				break;

			case "order_current_person":
				if ($arr["obj_inst"]->prop("order_current_person") != $arr["request"]["order_current_person"])
				{
					$this->upd_ud = true;
				}
				break;
		}
		return $retval;
	}

	function save_ord_cur_tbl($arr, $is_post = false)
	{
		$oc = obj($arr["obj_inst"]->prop("order_center"));
		$soc = get_instance(CL_SHOP_ORDER_CART);
		if (isset($arr["request"]["quant"]))
		{
			$awa = new aw_array($arr["request"]["quant"]);
			foreach($awa->get() as $iid => $quantx)
			{
				$quantx = new aw_array($quantx);
				foreach($quantx->get() as $x => $quant)
				{
					$soc->set_item(array(
						"iid" => $iid,
						"quant" => $quant,
						"oc" => $oc,
						"it" => $x,
					));
				}
			}
		}

		if ($is_post)
		{
			// also, if we got a discount element, save that as well
			$soc = get_instance(CL_SHOP_ORDER_CENTER);

			$arr["obj_inst"]->set_meta(
				"order_cur_discount",
				$soc->get_discount_from_order_data($arr["obj_inst"]->prop("order_center"), $arr["request"]["user_data"])
			);

			$arr["obj_inst"]->set_meta("order_cur_pages", $arr["request"]["pgnr"]);
		}
	}

	function _get_prod_s_sbt($arr){
		$arr['prop']['onclick'] = "update_products_table();";
		return PROP_OK;
	}

	function _get_stats_inventory_rapair_table($arr)
	{
		$srch = $arr["request"];

		$t = $arr["prop"]["vcl_inst"];
		$t->set_sortable(false);
		$t->define_field(array(
			"name" => "product",
			"caption" => t("Artikkel"),
			"sortable" => 1,
		));
		$prod_fields = array(
			"prod_name" => t("Nimetus"),
			"weight" => t("kaal (gr/m2)"),
			"width" => t("Laius (mm)"),
		);
		foreach($prod_fields as $key => $val)
		{
			$t->define_field(array(
				"name" => $key,
				"caption" => $val,
				"sortable" => 1,
				"parent" => "product"
			));
		}
		$fields = array(
//			"prod" => t("Artikkel"),
			"begin_count" => t("Perioodi algsaldo (kg)"),
			"amount" => t("Kogus (kg)"),
			"len" => t("Kogus (jm)"),
			"price" => t("hind (EEK/t)"),
			"balance" => t("Hetke saldo (EEK)"),
			"repair" => t("Parandus"),
		);
		foreach($fields as $key => $val)
		{
			$t->define_field(array(
				"name" => $key,
				"caption" => $val,
				"sortable" => 1,
			));
		}


		if(empty($srch["stats_type"]))
		{
			$srch["stats_type"] = "all";
		}
		if(empty($arr["request"]["timespan"]))
		{
			$arr["request"]["timespan"] = "period_week";
		}
		$filter = $this->get_range($arr["request"]["timespan"]);
		$filter["category"] = isset($arr["request"]["pgtf"]) ? $arr["request"]["pgtf"] : 0;
		while($filter["from"] < $filter["to"])
		{
			if($filter["from"] > time())
			{
				break;
			}
			$parent = date("mY" , $filter["from"]);
			$t->define_field(array(
				"name" => $parent,
				"caption" => date("m.Y" , $filter["from"]),
				"sortable" => 1,

			));
			$filter["from"]+= 31*3600*25;
		}

		$ol = $arr["obj_inst"] -> get_inventories($filter);
		$products = $arr["obj_inst"] -> get_products($filter);
		$ol = $arr["obj_inst"] -> get_movements($filter);

		$notes = $arr["obj_inst"] -> get_delivery_note_rows($filter);
		$movements = array();
		foreach($ol->arr() as $note)
		{
			$movements[$note->prop("product")]+=$note->prop("amount");
		}

		foreach($products->arr() as $o)
		{
			$amount = $arr["obj_inst"]->get_amount(array(
				"prod" => $o->id(),
			));
			$count = $amount;

			$t->define_data(array(
				"prod_name" => $o->name(),
//				"weight" => t("kaal (gr/m2)"),
//				"width" => t("Laius (mm)"),
//				"begin_count" => t("Perioodi algsaldo"),
//				"amount" => t("Kogus kg"),
//				"len" => t("Kogus jm."),
//				"price" => t("hind EEK/t"),
				"balance" => $count,
				"repair" => $movements[$o->id()],
				"width" => $o->prop("width"),
				"weight" => $o->prop("gramweight"),
			));
		}
	}

	function _get_stats_balance_table($arr)
	{
		$srch = $arr["request"];
		if(empty($srch["stats_type"]))
		{
			$srch["stats_type"] = "all";
		}
		if(empty($arr["request"]["timespan"]))
		{
			$arr["request"]["timespan"] = "period_week";
		}
		$filter = $this->get_range($arr["request"]["timespan"]);
		$filter["category"] = isset($arr["request"]["pgtf"]) ? $arr["request"]["pgtf"] : null;

		$ol = $arr["obj_inst"] -> get_movements($filter);

		$movements_in = array();
		$movements_out = array();
		$movements_after = array();

		foreach($ol->arr() as $note)
		{
			if($note->prop("from_wh") == $arr["obj_inst"]->id())
			{
				$movements_out[$note->prop("product")][date("mY" , $note->prop("date"))]+=$note->prop("amount");
			}
			if($note->prop("to_wh") == $arr["obj_inst"]->id())
			{
				$movements_in[$note->prop("product")][date("mY" , $note->prop("date"))]+=$note->prop("amount");
			}
		}

		$filter["after_time"] = 1;
		$after_ol = $arr["obj_inst"] -> get_movements($filter);
		foreach($after_ol->arr() as $note2)
		{
			$movements_after[$note2->prop("product")]+= $note2->prop("amount");
		}

		$products = $arr["obj_inst"] -> get_products($filter);

		$t = $arr["prop"]["vcl_inst"];
		$t->set_sortable(false);
		$t->set_caption(t("Lao saldo"));
		$fields = array(
			"begin_count" => t("Perioodi algsaldo (kg)"),
			"amount" => t("Kogus (kg)"),
			"len" => t("Kogus jm."),
			"price" => t("hind EEK/t"),
			"balance" => t("Hetke saldo"),
		);
		$t->define_field(array(
			"name" => "product",
			"caption" => t("Artikkel"),
			"sortable" => 1,
		));
		$prod_fields = array(
			"prod_name" => t("Nimetus"),
			"weight" => t("kaal (gr/m2)"),
			"width" => t("Laius (mm)"),
		);
		foreach($prod_fields as $key => $val)
		{
			$t->define_field(array(
				"name" => $key,
				"caption" => $val,
				"sortable" => 1,
				"parent" => "product"
			));
		}
		foreach($fields as $key => $val)
		{
			$t->define_field(array(
				"name" => $key,
				"caption" => $val,
				"sortable" => 1,
			));
		}

		$month_fields = array(
			"in" => t("Sisse"),
			"tell" => t("Tell.nr."),
			"out" => t("V&auml;lja"),
			"tell_out" => t("Tell.nr."),
			"residual" => t("J&auml;&auml;k")
		);


		while($filter["from"] < $filter["to"])
		{
			if($filter["from"] > time())
			{
				break;
			}
			$parent = date("mY" , $filter["from"]);
			$t->define_field(array(
				"name" => $parent,
				"caption" => date("m Y" , $filter["from"]),
				"sortable" => 1,

			));
			foreach($month_fields as $key => $val)
			{
				$t->define_field(array(
					"name" => $parent.$key,
					"caption" => $val,
					"sortable" => 1,
					"parent" => $parent,
				));
			}

			$filter["from"]+= 31*3600*25;
		}

		foreach($products->arr() as $product)
		{
			$balance = $arr["obj_inst"]->get_amount(array(
				"prod" => $product->id(),
			));
			$count = $balance - $movements_after[$product->id()];
			$begin = $count;
			if(isset($movements_in[$product->id()]))
			{
				$begin-= array_sum($movements_in[$product->id()]);
			}

			if(isset($movements_out[$product->id()]))
			{
				$begin+= array_sum($movements_out[$product->id()]);
			}
			$data = array(
				"prod_name" => $product->name(),
				"width" => $product->prop("width"),
				"weight" => $product->prop("gramweight"),
				"balance" => $balance,
				"begin_count" => $begin,
			);

			foreach($movements_in[$product->id()] as $date => $sum)
			{
				$data[$date."in"] = $sum;
			}

			foreach($movements_out[$product->id()] as $date => $sum)
			{
				$data[$date."out"] = $sum;
			}

			$t->define_data($data);
		}
	}


	function _get_stats_day_table($arr)
	{
		$srch = $arr["request"];

		if(empty($srch["stats_type"]))
		{
			$srch["stats_type"] = "all";
		}

		if(empty($arr["request"]["timespan"]))
		{
			$arr["request"]["timespan"] = "period_week";
		}
		$filter = $this->get_range($arr["request"]["timespan"]);

		$t = $arr["prop"]["vcl_inst"];
		$t->set_sortable(false);
		$t->define_field(array(
			"name" => "product",
			"caption" => t("Artikkel"),
			"sortable" => 1,
		));
		$prod_fields = array(
			"prod_name" => t("Nimetus"),
			"weight" => t("kaal (gr/m2)"),
			"width" => t("Laius (mm)"),
		);
		foreach($prod_fields as $key => $val)
		{
			$t->define_field(array(
				"name" => $key,
				"caption" => $val,
				"sortable" => 1,
				"parent" => "product"
			));
		}
		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa"),
			"sortable" => 1,
			"chgbgcolor" => "bgcolor",
		));
		$start = $filter["from"];
		while($start < $filter["to"])
		{
			$t->define_field(array(
				"name" => $start,
				"caption" => date("d.m.Y" , $start),
				"sortable" => 1,
			));

			$start+= 86400;
		}
		$filter["category"] = isset($arr["request"]["pgtf"]) ? $arr["request"]["pgtf"] : null;
		$filter["action_type"] = $srch["stats_type"];
		$ol = $arr["obj_inst"] -> get_movements($filter);//		arr($ol);
		$data = array();
		$all = array();
		foreach($ol->arr() as $o)
		{
			$ti = $o->prop("date");
			$date = mktime(0,0,0,date("m" , $ti) , date("d" , $ti), date("Y" , $ti));
			if($o->prop("from_wh") == $arr["obj_inst"]->id())
			{
				$data[$o->prop("product")][$date]-= $o->prop("amount");
				$all[$o->prop("product")]-=$o->prop("amount");
			}
			if($o->prop("to_wh") == $arr["obj_inst"]->id())
			{
				$data[$o->prop("product")][$date]+= $o->prop("amount");
				$all[$o->prop("product")]+=$o->prop("amount");
			}
		}


		$products = $arr["obj_inst"] -> get_products($filter);
		foreach($products->arr() as $o)
		{
			$name = $o->name();
			$id = $o->id();
			$prod_data = array(
				"prod_name" => $name,
			);
			if(is_array($data[$id]))
			{
				$prod_data = $prod_data + $data[$id];
			}
			if(isset($all[$id]))
			{
				$prod_data["sum"] = $all[$id];
			}
			else
			{
				$prod_data["sum"] = 0;
			}
			$prod_data["weight"] = $o->prop("gramweight");
			$prod_data["width"] = $o->prop("width");
			$prod_data["bgcolor"] = "grey";
			$t->define_data($prod_data);
		}
		$t->set_caption(t("Toodete liikumine laos p&auml;evade l&otilde;ikes"));
	}

	function _get_stats_table($arr)
	{
		$srch = $arr["request"];

		if(empty($srch["stats_type"]))
		{
			$srch["stats_type"] = "all";
		}
		if(empty($arr["request"]["timespan"]))
		{
			$arr["request"]["timespan"] = "period_week";
		}
		$filter = $this->get_range($arr["request"]["timespan"]);
		$filter["category"] = isset($arr["request"]["pgtf"]) ? $arr["request"]["pgtf"] : null;


		$t = $arr["prop"]["vcl_inst"];
		$t->set_sortable(false);

		$fields = array(
//			"id" => t("Tellimus"),
//			"prod" => t("Artikkel"),
			"begin_count" => t("Perioodi algsaldo"),
			"income" => t("Perioodi sissetulek"),
			"outcome" => t("Perioodi v&auml;ljaminek"),
			"inventory_repair" => t("Inventuuri parandus"),
			"final_count" => t("Perioodi l&otilde;ppsaldo"),
//			"final_count_check" => t("Perioodi l&otilde;ppsaldo kontroll"),
		);

		$t->define_field(array(
			"name" => "product",
			"caption" => t("Artikkel"),
			"sortable" => 1,
		));
		$prod_fields = array(
			"prod_name" => t("Nimetus"),
			"weight" => t("kaal (gr/m2)"),
			"width" => t("Laius (mm)"),
		);
		foreach($prod_fields as $key => $val)
		{
			$t->define_field(array(
				"name" => $key,
				"caption" => $val,
				"sortable" => 1,
				"parent" => "product"
			));
		}

		foreach($fields as $key => $val)
		{
			$t->define_field(array(
				"name" => $key,
				"caption" => $val,
				"sortable" => 1,
			));
		}



		$ol = $arr["obj_inst"] -> get_movements($filter);

		$movements_in = array();
		$movements_out = array();
		$movements_after = array();

		foreach($ol->arr() as $note)
		{
			if($note->prop("from_wh") == $arr["obj_inst"]->id())
			{
				$movements_out[$note->prop("product")]+=$note->prop("amount");
			}
			if($note->prop("to_wh") == $arr["obj_inst"]->id())
			{
				$movements_in[$note->prop("product")]+=$note->prop("amount");
			}
		}

		$filter["after_time"] = 1;
		$after_ol = $arr["obj_inst"] -> get_movements($filter);
		foreach($after_ol->arr() as $note2)
		{
			$movements_after[$note2->prop("product")]+= $note2->prop("amount");
		}

		$ol = $products = $arr["obj_inst"] -> get_products($filter);
//		$ol = $arr["obj_inst"] -> get_packagings();

		foreach($ol->arr() as $o)
		{
			$count = $arr["obj_inst"]->get_amount(array(
				"prod" => $o->id(),
			));
			$count = $count - $movements_after[$o->id()];

			$t->define_data(array(
				"begin_count" => $count - $movements_in[$o->id()] + $movements_out[$o->id()],
				"final_count" => $count,
				"income" => $movements_in[$o->id()],
				"outcome" => $movements_out[$o->id()],
//				"prod_name" => get_name($o->prop("product")),
//				"id" => html::get_change_url($o->prop("delivery_note"), $vars, $o->prop("delivery_note.name")),
				"prod_name" => $o->name(),
				"weight" => $o->prop("gramweight"),
				"width" => $o->prop("width"),
			));
		}
		$t->set_caption(t("Materjalide hulga muutus laos"));
	}

	function _get_osearch_table($arr)
	{
		$srch = $arr["request"];
		$fields = array(
			"id" => t("Tellimus"),
			"prodname" => t("Toode"),
			"uname" => t("Kasutaja"),
			"pname" => t("Isik"),
			"oname" => t("Organisatsioon"),
			"odate" => t("Telliti"),
		);
		$t = $arr["prop"]["vcl_inst"];
		$t->set_sortable(false);
		foreach($fields as $key => $val)
		{
			$t->define_field(array(
				"name" => $key,
				"caption" => $val,
				"sortable" => 1,
			));
		}
		if($srch["osearch_uname"])
		{
			$z .= " AND user.name LIKE '%".$srch["osearch_uname"]."%'";
		}
		if($srch["osearch_pname"])
		{
			$z .= " AND isik.name LIKE '%".$srch["osearch_pname"]."%'";
		}
		if($srch["osearch_oname"])
		{
			$z .= " AND com.name LIKE '%".$srch["osearch_pname"]."%'";
		}
		if($srch["osearch_oid"])
		{
			$z .= " AND objects.oid = '".$srch["osearch_oid"]."'";
		}
		if(is_array($srch["osearch_odates"]))
		{
			$d = $srch["osearch_odates"];
			$d_ts = mktime(0, 0, 0, $d["month"], $d["day"], $d["year"]);
			if ($d_ts > 100)
			{
				$z .= " AND objects.created > ".$d_ts;
			}
		}
		if(is_array($srch["osearch_odatee"]))
		{
			$d = $srch["osearch_odatee"];
			$d_ts = mktime(23, 59, 59, $d["month"], $d["day"], $d["year"]);
			if ($d_ts > 30000)
			{
				$z .= " AND objects.created < ".$d_ts;
			}
		}
		if($srch["osearch_from"] == 1)
		{
			$z .= " AND so.confirmed = 1";
		}
		elseif($srch["osearch_from"] == 2)
		{
			$z .= " AND so.confirmed = 0";
		}

		$lim = " LIMIT 200 ";
		if ($srch["osearch_prodname"] != "")
		{
			$lim = "";
		}
		$q = "
			SELECT
				objects.oid AS id,
				isik.name AS pname,
				isik.oid AS pname_id,
				user.name AS uname,
				user.oid AS uname_id,
				com.name AS oname,
				com.oid AS oname_id,
				objects.created AS odate,
				so.confirmed as confirmed
			FROM
				objects
				LEFT JOIN aw_shop_orders so ON (so.aw_oid = objects.oid)
				LEFT JOIN objects isik ON (so.aw_orderer_person = isik.oid)
				LEFT JOIN objects com ON (so.aw_orderer_company = com.oid)
				LEFT JOIN aliases isik2user ON (isik2user.target = isik.oid AND isik2user.reltype = 2)
				LEFT JOIN users ON (isik2user.source = users.oid)
				LEFT JOIN objects user ON (users.oid = user.oid)
			WHERE
				objects.status > 0 AND
				isik.status > 0 AND
				com.status > 0 AND
				user.status > 0 AND
				objects.parent = ".$this->order_fld."
				$z
				GROUP BY objects.created DESC
				$lim
		";
		$this->db_query($q);
		$vars = array("return_url" => get_ru());
		//$t->table_header = t("<center>Leiti ".$this->num_rows()." kirjet</center>");
		$mt = 0;
		while($w = $this->db_next())
		{
			$this->save_handle();
			if($srch["osearch_prodname"])
			{
				$z2 = " AND objects.name LIKE '%".$srch["osearch_prodname"]."%'";
			}
			$q2 = "
			SELECT objects.name AS name, objects.oid AS id
			FROM objects
				LEFT JOIN aw_shop_products prod ON (objects.oid = prod.aw_oid)
				LEFT JOIN aliases order2prod ON (order2prod.target = objects.oid AND order2prod.reltype = 1)
			WHERE
				objects.oid > 0 AND
				order2prod.source = '".$w["id"]."'
				$z2
			";
			$this->db_query($q2);

			if($this->num_rows() == 0)
			{
				$this->restore_handle();
				continue;
			}
			$e = array();
			while($w2 = $this->db_next())
			{
				$e[$w2["id"]] = html::get_change_url($w2["id"], $vars, $w2["name"]);
			}
			$this->restore_handle();
			$t->define_data(array(
				"id" => html::get_change_url($w["id"], $vars, $w["id"]),
				"pname" => html::get_change_url($w["pname_id"], $vars, $w["pname"]),
				"uname" => html::get_change_url($w["uname_id"], $vars, $w["uname"]),
				"oname" => html::get_change_url($w["oname_id"], $vars, $w["oname"]),
				"prodname" => implode(", ", $e),
				"odate" => date("d-m-Y", $w["odate"]),
			));
			$mt++;
		}
		$t->set_caption(t("<center>Otsingule vastab ".$mt." tellimust</center>"));

	}

	function do_order_cur_tb($data)
	{
		$tb =& $data["prop"]["toolbar"];

		/*$tb->add_button(array(
			"name" => "save",
			"img" => "save.gif",
			"tooltip" => t("Salvesta"),
			"url" => "javascript:document.changeform.submit()"
		));*/

		$url = $this->mk_my_orb("gen_order", array("id" => $data["obj_inst"]->id(), "html" => 1));
		$url = "window.open('$url','offer','width=700,height=600,toolbar=0,location=0,menubar=1,scrollbars=1')";
		$tb->add_button(array(
			"name" => "confirm",
			"img" => "pdf_upload.gif",
			"tooltip" => t("Genereeri HTML pakkumine"),
			"onClick" => $url,
			"url" => "#"
		));

		$tb->add_button(array(
			"name" => "mail",
			"img" => "save.gif",
			"tooltip" => t("Saada meilile"),
			"action" => "send_cur_order"
		));

		$tb->add_button(array(
			"name" => "clear",
			"img" => "new.gif",
			"tooltip" => t("Uus pakkumine"),
			"action" => "clear_order"
		));
	}

	private function _init_order_cur_table($t)
	{
		if ($_GET["group"] === "order_current")
		{
			$t->define_field(array(
				"name" => "page",
				"caption" => t("Lehek&uuml;lg"),
				"align" => "center"
			));
		}

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));

		$t->define_field(array(
			"name" => "quantity",
			"caption" => t("Kogus"),
			"align" => "center"
		));
	}

	function do_order_cur_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_order_cur_table($t);

		$pgnr = $arr["obj_inst"]->meta("order_cur_pages");

		// stick the order in the table
		$soc = get_instance(CL_SHOP_ORDER_CART);
		$soc->get_cart(obj($arr["obj_inst"]->prop("order_center")));
		foreach($soc->get_items_in_cart() as $iid => $quant)
		{
			$item = obj($iid);
			$t->define_data(array(
				"page" => html::textbox(array(
					"name" => "pgnr[$iid]",
					"value" => $pgnr[$iid],
					"size" => 5
				)),
				"name" => html::href(array(
					"caption" => $item->name(),
					"url" => $this->mk_my_orb("change", array("id" => $iid), $item->class_id())
				)),
				"quantity" => html::textbox(array(
					"name" => "quant[$iid]",
					"value" => is_array($quant) ? $quant[0]["items"] : $quant,
					"size" => 5
				))
			));
		}

		$t->set_default_sortby("page");
		$t->sort_by();
	}


	private function _init_undone_tbl($t,$cl)
	{
		$t->define_field(array(
			"name" => "code",
			"caption" => t("Kood"),
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"name" => "product",
			"caption" => t("Toode"),
			"chgbgcolor" => "color",
		));
		$t->define_field(array(
			"name" => "unit",
			"caption" => t("M&otilde;&otilde;t&uuml;hik"),
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"name" => "packaging",
			"caption" => t("Pakend"),
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"name" => "amount",
			"caption" => t("Tellitav Kogus"),
			"align" => "center",
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"name" => "date",
			"caption" => t("Soovitav tarne t&auml;itmine"),
			"align" => "center",
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"name" => "order_date",
			"caption" => t("Tellimuse kuup&auml;ev"),
			"align" => "center",
			"chgbgcolor" => "color",
		));
/*
		$t->define_field(array(
			"name" => "bill",
			"caption" => t("Tellimuse kuup&auml;ev"),
			"align" => "center",
			"chgbgcolor" => "color",
		));
*/
		if(!$cl)$t->define_field(array(
			"name" => "client",
			"caption" => t("Klient"),
			"align" => "center",
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"name" => "order",
			"caption" => t("Tellimuse nr."),
			"align" => "center",
			"chgbgcolor" => "color",
		));
	}

	function __br_sort($a, $b)
	{
		if(!($this->can("view" , $a) and $this->can("view" , $b))) return 1;
		$p1 = obj($a);
		$p2 = obj($b);
		if($p1->name() > $p2->name()) return 1;
		return -1;
	}

	function _get_shop_orders_toolbar($arr)
	{
		$tb =& $arr["prop"]["toolbar"];

		$tb->add_menu_button(array(
			"name" => "create_order",
			"tooltip" => t("Uus tellimus")
		));

		$tb->add_menu_item(array(
			"parent" => "create_order",
			"text" => t("Tellimus"),
			"link" => $this->mk_my_orb("new", array(
				"parent" => isset($this->order_fld) ? $this->order_fld : null,
				"alias_to" => $arr["obj_inst"]->id(),
				"reltype" => 5, //RELTYPE_ORDER,
				"return_url" => get_ru()
			), CL_SHOP_ORDER)
		));
		$tb->add_button(array(
			"name" => "print",
			"tooltip" => t("Prindi tellimused"),
			"img" => "print.gif",
			"url" => "javascript:document.changeform.target='_blank';javascript:submit_changeform('print_orders')",
//			"url" => $this->mk_my_orb("print_orders", array(
//				"id" => $arr["obj_inst"]->id(),
//				"return_url" => get_ru()
//			), CL_ORDERS_MANAGER)
		));

		$tb->add_button(array(
			"name" => "confirm",
			"img" => "save.gif",
			"tooltip" => t("Kinnita tellimused"),
			"action" => "confirm_orders",
			"confirm" => t("Oled kindel, et soovid valitud tellimused kinnitada?"),
		));
	}

	function _get_shop_orders_tree($arr)
	{
		$s = automatweb::$request->arg("shop_orders_s_status");
		$t = $arr["prop"]["vcl_inst"];
		$t->start_tree(array(
			"type" => TREE_DHTML,
			"tree_id" => "shop_orders_tree",
		));
		$ol1 = new object_list(array(
			"class_id" => CL_SHOP_ORDER,
			"confirmed" => 1,
		));
		$ol2 = new object_list(array(
			"class_id" => CL_SHOP_ORDER,
			"confirmed" => new obj_predicate_not(1),
		));
		$t->add_item(0, array(
			"name" => sprintf("%s (%s)", ($s == 1) ? "<strong>" .t("Kinnitatud")."</strong>" : t("Kinnitatud"), $ol1->count()),
			"url" => aw_url_change_var("shop_orders_s_status", 1),
			"id" => "confirmed",
		));
		$t->add_item(0, array(
			"name" => sprintf("%s (%s)", ($s == -1) ? "<strong>" .t("Kinnitamata")."</strong>" : t("Kinnitamata"), $ol2->count()),
			"url" => aw_url_change_var("shop_orders_s_status", -1),
			"id" => "unconfirmed",
		));
		$t->add_item(0, array(
			"name" => sprintf("%s (%s)", ($s === "") ? "<strong>" .t("K&otilde;ik")."</strong>" : t("K&otilde;ik"), $ol1->count() + $ol2->count()),
			"url" => aw_url_change_var("shop_orders_s_status", null),
			"id" => "all",
		));
	}

	private function _init_shop_orders_table($t)
	{
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Tellimus"),
			"align" => "center",
			"sortable" => true,
		));
		$t->define_field(array(
			"name" => "prod",
			"caption" => t("Tooted"),
			"align" => "center",
			"sortable" => true,
		));
		$t->define_field(array(
			"name" => "uname",
			"caption" => t("Kasutaja"),
			"align" => "center",
			"sortable" => true,
		));
		$t->define_field(array(
			"name" => "pname",
			"caption" => t("Isik"),
			"align" => "center",
			"sortable" => true,
		));
		$t->define_field(array(
			"name" => "oname",
			"caption" => t("Organisatsioon"),
			"align" => "center",
			"sortable" => true,
		));
		$t->define_field(array(
			"name" => "status",
			"caption" => t("Staatus"),
			"align" => "center",
			"sortable" => true,
		));
		$t->define_field(array(
			"name" => "date",
			"caption" => t("Telliti"),
			"align" => "center",
			"type" => "time",
			"format" => "d.m.Y",
			"sortable" => true,
		));

		$t->set_caption(t("Poe tellimuste otsingu tulemused"));
	}

	function _get_shop_orders_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_shop_orders_table($t);
		$ol = $this->_get_shop_orders_ol($arr["request"]);
		foreach($ol->arr() as $oid => $o)
		{
			$p = $o->prop("orderer_person");
			if($this->can("view", $p))
			{
				$po = obj($p);
				$uo = crm_person::has_user($po);
			}
			$c = $o->prop("orderer_company");
			if($this->can("view", $c))
			{
				$co = obj($c);
			}
			$t->define_data(array(
				"name" => html::obj_change_url($o, $o->name() ? $o->name() : t("(nimetu)"))." (".$oid.")",
				"oid" => $oid,
				"pname" => $po ? html::obj_change_url($po) : null,
				"uname" => $uo ? html::obj_change_url($uo) : null,
				"oname" => $co ? html::obj_change_url($co) : null,
				"status" => $o->prop("confirmed") ? t("Kinnitatud") : t("Kinnitamata"),
				"date" => $o->created(),
			));
		}
	}

	function _get_shop_orders_ol($arr)
	{
		$s = "shop_orders_s_";
		if(!empty($arr[$s."status"]))
		{
			$params["confirmed"] = ($arr[$s."status"] > 0) ? 1 : new obj_predicate_not(1);
		}
		if(!empty($arr[$s."uname"]))
		{
			$params["orderer_person.RELTYPE_PERSON(CL_USER).name"] = "%".$arr[$s."uname"]."%";
		}
		if(!empty($arr[$s."pname"]))
		{
			$params["orderer_person.name"] = "%".$arr[$s."pname"]."%";
		}
		if(!empty($arr[$s."oname"]))
		{
			$params["orderer_company.name"] = "%".$arr[$s."oname"]."%";
		}
		if(!empty($arr[$s."oid"]))
		{
			$params["oid"] = $arr[$s."oid"];
		}
		if(!empty($arr[$s."prod"]))
		{
			$params["RELTYPE_PRODUCT.name"] = "%".$arr[$s."prod"]."%";
		}
		$f = isset($arr[$s."from"]) ? date_edit::get_timestamp($arr[$s."from"]) : 0;
		$t = isset($arr[$s."to"]) ? date_edit::get_timestamp($arr[$s."to"]) : 0;
		if($f > 0 and $t > 0)
		{
			$params["created"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, mktime(0,0,0,date('m', $f),date('d', $f),date('Y', $f)), mktime(23,59,59,date('m', $t),date('d', $t),date('Y', $t)));
		}
		elseif($f > 0)
		{
			$params["created"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, mktime(0,0,0,date('m',$f),date('d',$f),date('Y',$f)));
		}
		elseif($t > 0)
		{
			$params["created"] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, mktime(23,59,59,date('m', $t),date('d', $t),date('Y', $t)));
		}
		elseif(!isset($arr[$s."from"]))
		{
			$params["created"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, mktime(0,0,0,date('m'),date('d'),date('Y')), mktime(23,59,59,date('m'),date('d'),date('Y')));
		}
		$params["class_id"] = CL_SHOP_ORDER;

		$ol = new object_list($params);
		return $ol;
	}

	/**
		@attrib name=unsent_table
		@param client optional type=id acl=view
	**/
	function unsent_table($arr)
	{
		classload("vcl/table");
		$arr["prop"]["vcl_inst"] = new aw_table(array(
			"layout" => "generic"
		));
		$arr["cl"] = 1;
		$this->do_order_undone_tbl($arr);
		return $arr["prop"]["vcl_inst"]->draw();
	}


	/**
		@attrib name=undone_xls
		@param undone_xls optional type=id acl=view
	**/
	function undone_xls($arr)
	{
		classload("vcl/table");
		$arr["prop"]["vcl_inst"] = new aw_table(array(
			"layout" => "generic"
		));
		$arr["xls"] = 1;
		$this->do_order_undone_tbl($arr);
		header("Content-type: application/csv");
		header("Content-disposition: inline; filename=undone.csv;");
		die($arr["prop"]["vcl_inst"]->get_csv_file());
	}

	function do_order_undone_tb(&$arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];

		$tb->add_button(array(
			"name" => "xls",
			"img" => "ftype_xls.gif",
			"tooltip" => t("Exceli-tabeli vormis"),
			"url" => $this->mk_my_orb("undone_xls", array(
				"id" => $arr["obj_inst"]->id(),
				"return_url" => get_ru()
			), CL_SHOP_WAREHOUSE)
		));
		if ($arr["request"]["group"] != "order_undone")
		{
			$tb->add_button(array(
				"name" => "confirm",
				"img" => "save.gif",
				"tooltip" => t("Kinnita tellimused"),
				"action" => "confirm_orders",
				"confirm" => t("Oled kindel, et soovid valitud tellimused kinnitada?"),
			));
		}
	}

	function do_order_undone_tbl(&$arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$cl = $arr["cl"];
		$xls = $arr["xls"];
		$this->_init_undone_tbl($t,$cl);

		// list orders from order folder
		$filter = array(
			"class_id" => CL_SHOP_ORDER,
//			"confirmed" => 0
		);
		if($arr["client"])
		{
			$filter["orderer_company"] = $arr["client"];
		}
		if($cl)
		{
			$filter["createdby"] = aw_global_get("uid");
		}
		$ol = new object_list($filter);

		$undone_products = array();
		$ord_data = array();
		foreach($ol->arr() as $o)
		{
			foreach($o->meta("ord_item_data") as $id => $items)
			{
				foreach($items as $item)
				{
					if($item["unsent"])
					{
						$ord_data[$o->id()][$id] = $item;
						$undone_products[$id][$o->id()] = $item["unsent"];
						break;
					}
				}
			}
		}
		$upkeys = array_keys($undone_products);
		usort($upkeys, array($this, "__br_sort"));
		foreach($upkeys as $product)
		{
			$order = $undone_products[$product];
			if(!$this->can("view" , $product)) continue;
			$product_obj = obj($product);
			$unit = "";
			if($this->can("view", $product_obj->prop("uservar1")))
			{
				$cls_obj = obj($product_obj->prop("uservar1"));
				$unit = $cls_obj->name();
			}
			if(!$xls) $t->define_data(array(
				"product" => $cl?$product_obj->name():html::get_change_url($product, array("return_url" => get_ru()) , $product_obj->name()),
				"code" => $product_obj->prop("user2"),
				"unit" => $unit,
				"packaging" => $product_obj->prop("user1"),
			));

			$prod_count = 0;

			foreach($order as $key => $amount)
			{
				if(!$this->can("view" , $key)) continue;
				$order = obj($key);
				$client = "";
				if($this->can("view" , $order->prop("orderer_company")))
				{
					$client_o = obj($order->prop("orderer_company"));
					$client = html::get_change_url($order->prop("orderer_company"), array("return_url" => get_ru()) , $client_o->name());
				}


				$t->define_data(array(
					"product" => (!$xls)?"":($cl?$product_obj->name():html::get_change_url($product, array("return_url" => get_ru()) , $product_obj->name())),
					"code" => (!$xls)?"":($product_obj->prop("user2") ? $product_obj->prop("user2") : " "),
					"unit" => (!$xls)?"":($unit ? $unit : " "),
					"packaging" => (!$xls)?"":($product_obj->prop("user1") ? $product_obj->prop("user1") : " "),
					"order" => $cl?html::href(array("url" => $key, "caption" => $key)):html::get_change_url($key, array("return_url" => get_ru() , "group" => "items") , $key),
					"client" => $client,
					"amount" => $amount,
					"color" => $order->prop("confirmed")?"":"#CCFFCC",

		//			"packaging" => $ord_data[$order->id()][$product]["user1"],
					"date" => $ord_data[$order->id()][$product]["duedate"],
					"bill" => $ord_data[$order->id()][$product]["bill"],
					"order_date" => date("d.m.Y" , $order->created()),
				));
				$prod_count+=$amount;
			}


			if(!$xls) $t->define_data(array(
				"product" => t("Kokku:"),
				"amount" => "<b>".$prod_count."</b>",

			));
		}

		$t->set_sortable(false);

//		$t->set_default_sortby("modified");
//		$t->set_default_sorder("DESC");
//		$t->sort_by();
	}

	function do_update_prod($arr)
	{
		foreach($arr["request"]["set_ord"] as $oid => $ord)
		{
			if($arr["request"]["old_ord"][$oid] != $ord)
			{
				$o = obj($oid);
				$o->set_ord($ord);
				$o->save();
			}
		}
	}

	function _get_arrivals_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_save_button();
	}

	function _get_arrival_products_list($arr)
	{
		if (!isset($this->config) or !is_object($this->config))
		{
			$this->show_error_text(t("VIGA: konfiguratsioon on valimata!"));
			return PROP_ERROR;
		}

		$t = $arr["prop"]["vcl_inst"];
		$t->set_sortable(false);

		$t->define_field(array(
			"name" => "count",
			"caption" => t("Nr"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "prod",
			"caption" => t("Artikkel"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "company",
			"caption" => t("Organisatsioon"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "weekday",
			"caption" => t("P&auml;ev"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "days",
			"caption" => t("Tarneaeg p&auml;evades"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "date1",
			"caption" => t("Kuup&auml;ev 1"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "date2",
			"caption" => t("Kuup&auml;ev 2"),
			"align" => "center",
		));
		$c_ol = new object_list(array(
			"class_id" => crm_company_obj::CLID,
			"parent" => $this->config->prop("arrival_company_folder"),
			"sort_by" => "name asc",
		));
		$companies = array(0 => t("--vali--")) + $c_ol->names();
		$arr["warehouses"] = array($arr["obj_inst"]->id());
		$res = $this->get_products_list_ol($arr);
		/*
		// eee - what is this for here? --dragut@01.07.2009
		$ol = new object_list(array(
			"class_id" => CL_SHOP_PRODUCT_PURVEYANCE,
			"site_id" => array(),
			"lang_id" => array(),
			"warehouse" => $arr["obj_inst"]->id(),
			"product" => $res["ol"]->ids(),
		));
		$ol->arr();
		*/
		for($i = 1; $i <=7; $i++)
		{
			$weekdays[$i] = locale::get_lc_weekday($i, true, true);
		}
		$counter = 1;
		foreach($res["ol"]->arr() as $prodid => $prod)
		{
			$ol = new object_list(array(
				"class_id" => CL_SHOP_PRODUCT_PURVEYANCE,
				"warehouse" => $arr["obj_inst"]->id(),
				"object" => $prodid,
			));
			$o = $ol->begin();
			$t->define_data(array(
				"count" => $counter++,
				"prod" => html::obj_change_url($prod),
				"company" => html::select(array(
					"options" => $companies,
					"name" => "arrivals[".$prodid."][org]",
					"value" => $o ? $o->prop("company") : 0,
				)),
				"weekday" => html::select(array(
					"options" => $weekdays,
					"name" => "arrivals[".$prodid."][weekday]",
					"value" => $o ? $o->prop("weekday") : 0,
				)),
				"days" => html::textbox(array(
					"size" => 3,
					"name" => "arrivals[".$prodid."][days]",
					"value" => $o ? $o->prop("days") : 0,
				)),
				"date1" => html::date_select(array(
					"name" => "arrivals[".$prodid."][date1]",
					"value" => $o ? (($d = $o->prop("date1"))? $d : -1) : -1,
					"format" => array("day_textbox", "month_textbox", "year_textbox"),
				)),
				"date2" => html::date_select(array(
					"name" => "arrivals[".$prodid."][date2]",
					"value" => $o ? (($d = $o->prop("date2"))? $d : -1) : - 1,
					"format" => array("day_textbox", "month_textbox", "year_textbox"),
				)),
			));
		}
	}

	function _set_arrival_products_list($arr)
	{
		foreach($arr["request"]["arrivals"] as $oid => $data)
		{
			$ol = new object_list(array(
				"class_id" => CL_SHOP_PRODUCT_PURVEYANCE,
				"warehouse" => $arr["obj_inst"]->id(),
				"object" => $oid,
			));
			$o = $ol->begin();
			if(!$o)
			{
				$o = obj();
				$o->set_class_id(CL_SHOP_PRODUCT_PURVEYANCE);
				$o->set_parent($oid);
				$o->set_name(sprintf(t("%s tarnetingimus"), obj($oid)->name()));
				$o->set_prop("object", $oid);
				$o->set_prop("warehouse", $arr["obj_inst"]->id());
			}
			$o->set_prop("company", $data["org"]);
			$o->set_prop("weekday", $data["weekday"]);
			$o->set_prop("days", $data["days"]);
			$o->set_prop("date1", date_edit::get_timestamp($data["date1"]));
			$o->set_prop("date2", date_edit::get_timestamp($data["date2"]));
			$o->save();
		}
	}

	function _get_arrivals_bc_info($arr)
	{
		$arr["prop"]["value"] = t("Selle vaate salvestamine kirjutab &uuml;le k&otilde;ik vastavate organisatsioonide tarnetingimused");
		$arr["prop"]["value"] .= t("<br />");
		$arr["prop"]["value"] .= t("Selles vaates olevaid ettev&otilde;ttedi n&auml;idatakse &quot;Tarnefirmade kaust&quot; kausta alt ja seda saab m&auml;&auml;rata lao seadete objektist");
	}

	function _get_arrivals_bc_table($arr)
	{
		if (!isset($this->config) or !is_object($this->config))
		{
			$this->show_error_text(t("VIGA: konfiguratsioon on valimata!"));
			return PROP_ERROR;
		}

		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "company",
			"caption" => t("Organisatsioon"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "weekday",
			"caption" => t("Tarnep&auml;ev"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "days",
			"caption" => t("Tarneaeg p&auml;evades"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "date1",
			"caption" => t("Kuup&auml;ev 1"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "date2",
			"caption" => t("Kuup&auml;ev 2"),
			"align" => "center",
		));
		$c_ol = new object_list(array(
			"class_id" => crm_company_obj::CLID,
			"parent" => $this->config->prop("arrival_company_folder"),
			"sort_by" => "name asc",
		));
		for($i = 1; $i <=7; $i++)
		{
			$weekdays[$i] = locale::get_lc_weekday($i, true, true);
		}
		foreach($c_ol->arr() as $oid => $o)
		{
			$t->define_data(array(
				"company" => html::obj_change_url($o),
				"weekday" => html::select(array(
					"options" => $weekdays,
					"name" => "arrivals[".$oid."][weekday]",
					"value" => $o->meta("purveyance_weekday"),
				)),
				"days" => html::textbox(array(
					"size" => 3,
					"name" => "arrivals[".$oid."][days]",
					"value" => $o->meta("purveyance_days"),
				)),
				"date1" => html::date_select(array(
					"name" => "arrivals[".$oid."][date1]",
					"value" => ($d = $o->meta("purveyance_date1"))? $d : -1,
					"format" => array("day_textbox", "month_textbox", "year_textbox"),
				)),
				"date2" => html::date_select(array(
					"name" => "arrivals[".$oid."][date2]",
					"value" => ($d = $o->meta("purveyance_date2"))? $d : -1,
					"format" => array("day_textbox", "month_textbox", "year_textbox"),
				)),
			));
		}
	}

	function _set_arrivals_bc_table($arr)
	{
		foreach($arr["request"]["arrivals"] as $oid => $data)
		{
			$co = obj($oid);
			$ol = new object_list(array(
				"class_id" => CL_SHOP_PRODUCT_PURVEYANCE,
				"company" => $oid,
				"warehouse" => $arr["obj_inst"]->id(),
			));

			$date1 = mktime(0, 0, 0, (int)$data["date1"]["month"], (int)$data["date1"]["day"], (int)$data["date1"]["year"]);
			$date2 = mktime(0, 0, 0, (int)$data["date2"]["month"], (int)$data["date2"]["day"], (int)$data["date2"]["year"]);

			$ol->set_prop("weekday", $data["weekday"]);
			$ol->set_prop("days", $data["days"]);
			$ol->set_prop("date1", $date1);
			$ol->set_prop("date2", $date2);
			$ol->save();
			$ol->remove_all();
			$co->set_meta("purveyance_weekday", $data["weekday"]);
			$co->set_meta("purveyance_days", $data["days"]);
			$co->set_meta("purveyance_date1", $date1);
			$co->set_meta("purveyance_date2", $date2);
			$co->save();
		}
	}

	function mk_prod_toolbar(&$data)
	{
		$tb = $data["prop"]["toolbar"];

		$tb->add_menu_button(array(
			"name" => "new",
			"tooltip" => t("Uus")
		));

		$tb->add_delete_button();

		$tb->add_save_button();

		if(automatweb::$request->arg("ptf") and !automatweb::$request->arg("pgtf"))
		{
			if(!$this->prod_tree_root)
			{
				$this->prod_tree_root = $arr["request"]["ptf"];
			}
			$clids = $this->get_warehouse_configs($data, "prod_tree_clids");

			$tb->add_menu_item(array(
				"parent" => "new",
				"text" => t("Artikkel"),
				"link" => $this->mk_my_orb("new", array(
					"parent" => $this->prod_tree_root,
					"return_url" => get_ru(),
					"warehouse" => $data["obj_inst"]->id(),
				), CL_SHOP_PRODUCT)
			));

			if($this->can("view", automatweb::$request->arg("ptf")))
			{
				$ptf_o = obj(automatweb::$request->arg("ptf"));

				$tb->add_menu_item(array(
					"parent" => "new",
					"text" => t("Pakend"),
					"link" => $this->mk_my_orb("new", array(
						"parent" => $ptf_o->id(),
						"return_url" => get_ru(),
						"alias_to" => $ptf_o->id(),
						"reltype" => 2,
					), CL_SHOP_PRODUCT_PACKAGING)
				));
			}

			$clid_captions = get_class_picker(array("field" => "name"));
			foreach($clids as $clid)
			{
				$tb->add_menu_item(array(
					"parent" => "new",
					"text" => $clid_captions[$clid],
					"link" => $this->mk_my_orb("new", array(
						"parent" => $this->prod_tree_root,
						"return_url" => get_ru(),
					), $clid)
				));
			}

			$tb->add_button(array(
				"name" => "copy",
				"img" => "copy.gif",
				"tooltip" => t("Kopeeri"),
				"action" => "copy_products"
			));

			$tb->add_button(array(
				"name" => "cut",
				"img" => "cut.gif",
				"tooltip" => t("L&otilde;ika"),
				"action" => "cut_products"
			));

			$tb->add_button(array(
				"name" => "paste",
				"img" => "paste.gif",
				"tooltip" => t("Kleebi"),
				"url" => $this->mk_my_orb("paste_products", array(
					"parent" => $this->prod_tree_root,
					"return_url" => get_ru(),
				))
				//"action" => "paste_products"
			));
		}
		elseif(!automatweb::$request->arg("ptf") and automatweb::$request->arg("pgtf"))
		{
			$tb->add_menu_item(array(
				"parent" => "new",
				"text" => t("Artiklikategooria"),
				"link" => $this->mk_my_orb("new", array(
					"parent" => $data["request"]["pgtf"],
					"return_url" => get_ru(),
				), CL_SHOP_PRODUCT_CATEGORY)
			));
			$gid = $data["request"]["pgtf"];
			if($this->can("view", $gid))
			{
				$go = obj($gid);
				if($go->class_id() == CL_SHOP_PRODUCT_CATEGORY)
				{
					$fid = $go->meta("def_fld");
					if($this->can("view", $fid))
					{
						$tb->add_menu_item(array(
							"parent" => "new",
							"text" => t("Artikkel"),
							"link" => $this->mk_my_orb("new", array(
								"parent" => $fid,
								"category" => $gid,
								"return_url" => get_ru(),
								"warehouse" => $data["obj_inst"]->id(),
							), CL_SHOP_PRODUCT)
						));
						if(!$this->prod_tree_root)
						{
							$this->prod_tree_root = $fid;
						}
					}
				}
			}
		}
		if($this->can("view", $this->prod_type_fld) and $this->prod_tree_root)
		{
			$this->_req_add_itypes($tb, $this->prod_type_fld, $data);
		}

		// Add the index update button here for now
		$tb->add_button(array(
			'name' => 'update_index',
			'img' => 'refresh.gif',
			'action' => 'update_products_index',
			'tooltip' => t('Uuenda toodete otsingu indeksit'),
		));
	}

	function mk_prodg_toolbar(&$prop)
	{
		$tb = $prop["prop"]["toolbar"];

		$tb->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Tootegrupp"),
			"url" => $this->mk_my_orb("new", array(
				"parent" => !empty($prop["request"]["pgtf"]) ? $prop["request"]["pgtf"] : $this->prod_type_fld,
				"return_url" => get_ru(),
				"cfgform" => $this->prod_type_cfgform,
			), CL_SHOP_PRODUCT_CATEGORY),
		));

		$tb->add_delete_button();
	}

	function _get_storage_income_prod_tree($arr)
	{
		return $this->mk_storage_prodg_tree($arr);
	}

	function _get_storage_export_prod_tree($arr)
	{
		return $this->mk_storage_prodg_tree($arr);
	}

	function mk_storage_prodg_tree($arr)
	{
		$pt = $this->get_warehouse_configs($arr, "prod_type_fld");
		$ol = new object_list(array(
			"parent" => $pt,
			"class_id" => CL_SHOP_PRODUCT_CATEGORY,
		));
		$tree = $arr["prop"]["vcl_inst"];
		$group = $this->get_search_group($arr);
		$cls = $arr["request"][$group."_s_type"];
		if(empty($cls))
		{
			$cls = STORAGE_FILTER_ALL;
		}
		$gbf = $this->mk_my_orb("get_prodg_tree_level",array(
			"set_retu" => get_ru(),
			"tree_type" => "storage",
			"cls" => $cls,
			"group" => $group,
			"pgtf" => automatweb::$request->arg("pgtf"),
			"parent" => " ",
		), CL_SHOP_WAREHOUSE);
		$tree->start_tree(array(
			"has_root" => true,
			"root_name" => t("Artiklid"),
			"root_url" => aw_url_change_var(array("pgtf"=> null)),
			"root_icon" => icons::get_icon_url(CL_MENU),
			"type" => TREE_DHTML,
			"tree_id" => "storage_prodg_tree",
			"persist_state" => 1,
			"get_branch_func" => $gbf,
		));
		foreach($ol->arr() as $o)
		{
			$url = aw_url_change_var(array("pgtf" => $o->id(), $group."_s_type" => $cls, $group."_s_art_cat" => $o->id(), $group."_s_article" => null));
			$this->insert_prodg_tree_item($tree, $o, $url, null);
		}
		$tree->set_selected_item(automatweb::$request->arg("pgtf"));
	}

	function mk_prodg_tree(&$arr)
	{
		$chk = $this->get_warehouse_configs($arr, "no_prodg_tree");
		if(count($chk) and $arr["request"]["group"] != "productgroups")
		{
			return PROP_IGNORE;
		}

		if($arr["obj_inst"]->class_id() == CL_SHOP_WAREHOUSE)
		{
			$pt = isset($this->prod_type_fld) ? $this->prod_type_fld : null;
			$root_name = obj($pt)->name();
		}
		else
		{
			$pt = $this->get_warehouse_configs($arr, "prod_type_fld");
			$root_name = t("Artiklikategooriad");
		}

		if(empty($pt))
		{
			return PROP_IGNORE;
		}

		$group = $this->get_search_group($arr);
		$ol = new object_list(array(
			"parent" => $pt,
			"class_id" => CL_SHOP_PRODUCT_CATEGORY,
		));
		$params = array(
			"set_retu" => get_ru(),
			"group" => $group,
			"pgtf" => automatweb::$request->arg("pgtf"),
			"id" => $arr["obj_inst"]->id(),
		);
		if(!empty($arr["request"]["filt_time"]))
		{
			$params["filt_time"] = $arr["request"]["filt_time"];
		}
		if(!empty($arr["request"]["filt_cust"]))
		{
			$params["filt_cust"] = $arr["request"]["filt_cust"];
		}
		foreach($arr["request"] as $var => $val)
		{
			if($this->is_search_param($var))
			{
				$params[$var] = $val;
			}
		}
		$params["parent"] = " ";
		$gbf = $this->mk_my_orb("get_prodg_tree_level",$params, CL_SHOP_WAREHOUSE);

		$tree = $arr["prop"]["vcl_inst"];
		$tree->start_tree(array(
			"has_root" => true,
			"root_name" => $root_name,
			"root_url" => aw_url_change_var(array("pgtf"=> $this->prod_type_fld, "ptf" => null)),
			"root_icon" => icons::get_icon_url(CL_MENU),
			"type" => TREE_DHTML,
			"tree_id" => "prodg_tree",
			"persist_state" => 1,
			"get_branch_func" => $gbf,
		));
		foreach($ol->arr() as $o)
		{
			$url = aw_url_change_var(array("pgtf" => $o->id(), $group."_s_art_cat" => $o->id(), "ptf" => null));
			$this->insert_prodg_tree_item($tree, $o, $url, null, $arr["request"]);
		}
		$g = $this->get_search_group($arr);
		$arr["request"]["pgtf"] = null;
		$arr["request"][$g."_s_art_cat"] = null;
		switch($g)
		{
			case "arrival_prod":
			case "prod":
			case "storage_status":
				$ol = $this->get_products_list_ol($arr);
				$count = $ol["ol"]->count();
				break;
			case "storage_movements":
			case "storage_writeoffs":
				if (isset($filt["ptf"]))
				{
					unset($filt["ptf"]);
				}
				$ol = $this->_get_movements_ol($arr);
				$count = $ol->count();
				break;
			case "purchase_notes":
			case "purchase_bills":
			case "sales_notes":
			case "sales_bills":
				$arr["warehouses"] = array($arr["obj_inst"]->id());
				$ol = $this->_get_storage_ol($arr);
				$count = $ol->count();
				break;
		}
		$tree->add_item(0, array(
			"url" => aw_url_change_var(array("pgtf" => null, $group."_s_art_cat" => null, "ptf" => null)),
			"id" => "all",
			"name" => t("K&otilde;ik").(isset($count) ? " (".$count.")" : ""),
			"iconurl" => icons::get_icon_url(CL_MENU),
		));
		$v = automatweb::$request->arg("pgtf");
		$tree->set_selected_item($v ? $v : "all");
	}

	/**
		@attrib name=get_prodg_tree_level all_args=1
	**/
	function get_prodg_tree_level($arr)
	{
		parse_str($_SERVER['QUERY_STRING'], $arr);
		$this->config = obj(obj($arr["id"])->prop("conf"));
		$tree = get_instance("vcl/treeview");
		$arr["parent"] = trim($arr["parent"]);
		$tree->start_tree(array (
			"type" => TREE_DHTML,
			"branch" => 1,
			"tree_id" => "prodg_tree",
			"persist_state" => 1,
		));
		if($arr["tree_type"] === "storage")
		{
			$clids = array(CL_SHOP_PRODUCT_CATEGORY, CL_SHOP_PRODUCT);
		}
		$ol = new object_list(array(
			"parent" => $arr["parent"],
			"class_id" => CL_SHOP_PRODUCT_CATEGORY,
		));

		if($arr["tree_type"] === "storage")
		{
			$po = obj($arr["parent"]);
			$conn = $po->connections_to(array(
				"from.class_id" => CL_SHOP_PRODUCT,
				"type" => "RELTYPE_CATEGORY",
			));
			foreach($conn as $c)
			{
				$ol->add($c->from());
			}
		}

		foreach($ol->arr() as $o)
		{
			if($arr["tree_type"] === "storage")
			{
				if($o->class_id() != CL_SHOP_PRODUCT)
				{
					$params["pgtf"] = $o->id();
					$params[$arr["group"]."_s_art_cat"] = $o->id();
					$params[$arr["group"]."_s_article"] = null;
				}
				else
				{
					$params[$arr["group"]."_s_article"] = $o->name();
					$params[$arr["group"]."_s_art_cat"] = $po->id();
				}
				$params[$arr["group"]."_s_type"] = $arr["cls"]."";
				$url = aw_url_change_var($params, false, $arr["set_retu"]);
			}
			else
			{
				$url = aw_url_change_var(array("pgtf" => $o->id(), $arr["group"]."_s_art_cat" => $o->id(), "ptf" => null), false,  $arr["set_retu"]);
			}
			$this->insert_prodg_tree_item($tree, $o, $url, $arr["tree_type"], $arr);
		}

		$tree->set_selected_item(trim(automatweb::$request->arg("pgtf")));
		die($tree->finalize_tree());
	}

	private function insert_prodg_tree_item($tree, $o, $url, $type = null, $filt)
	{
		$g = $this->get_search_group(array(
			"request" => $filt,
		));
		switch($g)
		{
			case "arrival_prod":
			case "prod":
			case "storage_status":
				$filt["pgtf"] = $o->id();
				$filt[$g."_s_art_cat"] = $o->id();
				$ol = $this->get_products_list_ol(array(
					"request" => $filt,
				));
				$count = $ol["ol"]->count();
				break;
			case "storage_movements":
			case "storage_writeoffs":
				unset($filt["ptf"]);
				$filt["pgtf"] = $o->id();
				$filt[$g."_s_art_cat"] = $o->id();
				$ol = $this->_get_movements_ol(array(
					"request" => $filt,
					"obj_inst" => obj($filt["id"]),
				));
				$count = $ol->count();
				break;
			case "purchase_notes":
			case "purchase_bills":
			case "sales_notes":
			case "sales_bills":
				$filt["pgtf"] = $o->id();
				$filt[$g."_s_art_cat"] = $o->id();
				$ol = $this->_get_storage_ol(array(
					"request" => $filt,
					"warehouses" => array($filt["id"]),
					"obj_inst" => obj($filt["id"]),
				));
				$count = $ol->count();
				break;
		}
		$clid = $o->class_id();
		$tree->add_item(0, array(
			"url" => $url,
			"name" => sprintf("%s %s", $o->name(), isset($count) ? "(".$count.")" : ""),
			"id" => $o->id(),
			"iconurl" => icons::get_icon_url(($clid == CL_SHOP_PRODUCT)?$clid:CL_MENU),
		));
		$check_ol = new object_list(array(
			"parent" => $o->id(),
			"class_id" => CL_SHOP_PRODUCT_CATEGORY,
		));
		if($type === "storage" and  $clid == CL_SHOP_PRODUCT_CATEGORY)
		{
			$conn = $o->connections_to(array(
				"from.class_id" => CL_SHOP_PRODUCT,
				"type" => "RELTYPE_CATEGORY",
			));
			if(count($conn))
			{
				$subitems = 1;
			}
		}

		if($check_ol->count() || !empty($subitems))
		{
			$tree->add_item($o->id(), array(
				"name" => "tmp",
				"id" => $c->prop("to")."_tmp",
			));
		}
	}

	function do_prodg_list($arr)
	{
		$this->_init_do_prodg_list($arr);
		$t = $arr["prop"]["vcl_inst"];
		$ol = new object_list(array(
			"parent" => ($p = $arr["request"]["pgtf"])?$p:$this->prod_type_fld,
			"class_id" => CL_SHOP_PRODUCT_CATEGORY,
		));
		foreach($ol->arr() as $obj)
		{
			$t->define_data(array(
				"id" => $obj->id(),
				"name" => $obj->name(),
				"change" => html::get_change_url($obj->id(), array(
					"group" => "form",
					"return_url" => get_ru(),
				), t("Muuda")),
			));
		}
	}

	private function _init_do_prodg_list($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));
		$t->define_field(array(
			"name" => "change",
			"caption" => t("Muuda"),
		));
		$t->define_chooser(array(
			"field" => "id",
			"name" => "sel",
		));
	}

	function _req_add_itypes($tb, $parent, &$data)
	{
		$ol = new object_list(array(
			"parent" => $parent,
			"class_id" => array(CL_MENU, CL_SHOP_PRODUCT_TYPE),
		));
		$tbparent = ($parent == $this->prod_type_fld)?"new":"new".$parent;
		for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
		{
			if ($o->class_id() != CL_MENU)
			{
				$tb->add_menu_item(array(
					"parent" => $tbparent,
					"text" => $o->name(),
					"link" => $this->mk_my_orb("new", array(
						"item_type" => $o->id(),
						"parent" => $this->prod_tree_root,
						"alias_to" => $data["obj_inst"]->id(),
						"reltype" => 2, //RELTYPE_PRODUCT,
						"return_url" => get_ru(),
						"cfgform" => $o->prop("sp_cfgform"),
						"object_type" => $o->prop("sp_object_type")
					), CL_SHOP_PRODUCT)
				));
			}
			else
			{
				$tb->add_sub_menu(array(
					"parent" => $tbparent,
					"name" => "new".$o->id(),
					"text" => $o->name()
				));
				$this->_req_add_itypes($tb, $o->id(), $data);
			}
		}
	}

	function _get_stats_day_tree($arr)
	{
		$tree = $arr["prop"]["vcl_inst"];
		$tree->start_tree(array(
			"type" => TREE_DHTML,
			"tree_id" => "prod_tree",
		));
		$items = array(
			"all" => t("K&otilde;ik"),
			"purchases" => t("Ostud"),
			"whole_out" => t("Terved v&auml;lja"),
			"back_to_warehouse" => t("Tagastus lattu"),
			"half_out" => t("Poolikud v&auml;lja"),
		);

		foreach($items as $name => $caption)
		{
			$url = aw_url_change_var(array("stats_type"=> $name));
			$var = "stats_type";
			$tree->set_selected_item(isset($arr["request"][$var]) ? $arr["request"][$var] : "all");

			$tree->add_item(0, array(
				"url" => $url,
				"name" => $caption,
				"id" => $name,
			));
		}
	}



	function get_prod_tree($arr)
	{
		$chk = $this->get_warehouse_configs($arr, "no_prod_tree");
		if(count($chk))
		{
			return PROP_IGNORE;
		}

		$clids = array(menu_obj::CLID);
		if($arr["obj_inst"]->is_a(shop_warehouse_obj::CLID))
		{
			$pt = isset($this->prod_fld) ? $this->prod_fld : null;
			$root_name = obj($pt)->name();

			$clids = $this->get_warehouse_configs($arr, "prod_tree_clids");
		}
		else
		{
			$pt = $this->get_warehouse_configs($arr, "prod_fld");
			$root_name = t("Artiklid");
		}

		if(empty($pt))
		{
			return PROP_IGNORE;
		}
		$ol = new object_list(array(
			"parent" => $pt,
			"class_id" => $clids,
			"sort_by" => "objects.jrk"
		));
		$tree = $arr["prop"]["vcl_inst"];
		$params["set_retu"] = get_ru();
		foreach($arr["request"] as $var => $val)
		{
			if($this->is_search_param($var))
			{
				$params[$var] = $val;
			}
		}
		$params["id"] = $arr["obj_inst"]->id();
		$params["ptf"] = automatweb::$request->arg("ptf");
		$params["group"] = $arr["request"]["group"];
		$params["clids"] = $clids;
		$params["parent"] = " ";
		$gbf = $this->mk_my_orb("get_prod_tree_level", $params, CL_SHOP_WAREHOUSE);
		$tree->start_tree(array(
			"has_root" => true,
			"root_name" => trim(automatweb::$request->arg("ptf")) == $pt ? "<strong>".$root_name."</strong>" : $root_name,
			"root_url" => aw_url_change_var(array("ptf"=> isset($this->prod_fld) ? $this->prod_fld : null, "pgtf"=>null)),
			"root_icon" => icons::get_icon_url(CL_MENU),
			"type" => TREE_DHTML,
			"tree_id" => "prod_tree",
			"get_branch_func" => $gbf,
		));
		$g = $this->get_search_group($arr);
		foreach($ol->arr() as $o)
		{
			$url = aw_url_change_var(array("ptf" => $o->id(), "pgtf" => null, $g."_s_art_cat" => null));
			$this->insert_prod_tree_item($tree, $o, $url, $arr["request"], $clids);
		}
		$tree->set_selected_item(trim(automatweb::$request->arg("ptf")));
	}

	/**
		@attrib name=get_prod_tree_level all_args=1
	**/
	function get_prod_tree_level($arr)
	{
		$clids = CL_MENU;
		if(isset($arr["clids"]) and (is_class_id($arr["clids"]) || is_array($arr["clids"]) and count($arr["clids"]) > 0))
		{
			$clids = $arr["clids"];
		}

		$tree = get_instance("vcl/treeview");
		$tree->start_tree(array (
			"type" => TREE_DHTML,
			"branch" => 1,
			"tree_id" => "prod_tree",
		));
		$arr["parent"] = trim($arr["parent"]);
		$ol = new object_list(array(
			"parent" => $arr["parent"],
			"class_id" => $clids,
			"sort_by" => "objects.jrk"
		));
		$g = $this->get_search_group(array(
			"request" => $arr,
		));
		foreach($ol->arr() as $o)
		{
			$url = aw_url_change_var(array("ptf" => $o->id(), "pgtf" => null, $g."_s_art_cat" => null), false, $arr["set_retu"]);
			$this->insert_prod_tree_item($tree, $o, $url, $arr, $clids);
		}
		$tree->set_selected_item(trim(automatweb::$request->arg("ptf")));
		die($tree->finalize_tree());
	}

	private function insert_prod_tree_item($tree, $o, $url, $filt, $clids = CL_MENU)
	{
		$g = $this->get_search_group(array(
			"request" => $filt,
		));
		switch($g)
		{
			case "prod":
			case "arrival_prod":
			case "storage_status":
				$filt["ptf"] = $o->id();
				unset($filt[$g."_s_art_cat"]);
				unset($filt["pgtf"]);
				if($o->is_a(CL_SHOP_PRODUCT))
				{
					$ol = $o->get_packagings();
					$count = $ol->count();
				}
				else
				{
					$ol = $this->get_products_list_ol(array(
						"request" => $filt,
					));
					$count = $ol["ol"]->count();
				}
				break;

			case "storage_movements":
			case "storage_writeoffs":
				$filt["ptf"] = $o->id();
				unset($filt[$g."_s_art_cat"]);
				unset($filt["pgtf"]);
				$ol = $this->_get_movements_ol(array(
					"request" => $filt,
					"obj_inst" => obj($filt["id"]),
				));
				$count = $ol->count();
				break;

			default:
				$count = 0;
		}
		$tree->add_item(0, array(
			"url" => $url,
			"name" => sprintf("%s (%u)", $o->name(), $count),
			"id" => $o->id(),
		));
		$check_ol = new object_list(array(
			"parent" => $o->id(),
			"class_id" => $clids,
		));
		if($check_ol->count())
		{
			$tree->add_item($o->id(), array(
				"name" => "tmp",
				"id" => $o->id()."_tmp",
			));
		}
	}

	private function calc_prognosis_amounts(&$res, $arr)
	{
		$group = $this->get_search_group($arr);
		$d = $arr["request"][$group."_s_date"];
		$ds = date_edit::get_timestamp($d) + 60 * 60 * 24 -1;
		if($ds > 0)
		{
			$comp = new obj_predicate_compare(OBJ_COMP_BETWEEN, mktime(0,0,1, date('m', time()), date('d', time()), date('Y', time())), $ds);
			$oparams[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_SHOP_PURCHASE_ORDER.planned_date" => $comp,
					"CL_SHOP_SELL_ORDER.planned_date" => $comp,
				),
			));
		}

		$oparams["class_id"] = array(CL_SHOP_SELL_ORDER, CL_SHOP_PURCHASE_ORDER);

		$ps = $arr["request"][$group."_s_purchase_order_status"];
		$ss = $arr["request"][$group."_s_sales_order_status"];
		foreach($oparams["class_id"] as $clid)
		{
			if($clid == CL_SHOP_SELL_ORDER)
			{
				$p = $ss;
				$f = "CL_SHOP_SELL_ORDER";
			}
			else
			{
				$p = $ps;
				$f = "CL_SHOP_PURCHASE_ORDER";
			}
			if($p == STORAGE_FILTER_CONFIRMATION_ALL)
			{
				$conditions["class_id"][] = $clid;
			}
			else
			{
				$conditions["order_status"] = $p;
			}
		}

		$oparams[] = new object_list_filter(array(
			"logic" => "OR",
			"conditions" => $conditions,
		));

		$oparams[] = new object_list_filter(array(
			"logic" => "OR",
			"conditions" => array(
				"CL_SHOP_PURCHASE_ORDER.warehouse" => $arr["warehouses"],
				"CL_SHOP_SELL_ORDER.warehouse" => $arr["warehouses"],
			),
		));
		$orders = new object_list($oparams);
		$ids = $orders->ids();
		if(!count($ids))
		{
			return;
		}
		$c = new connection();
		$conn = $c->find(array(
			"from.oid" => $ids,
			"type" => 9,
			"from.class_id" => $oparams["class_id"],
		));
		foreach($conn as $c)
		{
			$row = obj($c["to"]);
			$order = obj($c["from"]);
			$mod = ($order->class_id() == CL_SHOP_SELL_ORDER)?(-1):1;
			$add_amts[$order->prop("warehouse")][$row->prop("prod")][$row->prop("unit")] += $mod * $row->prop("amount");
		}
		$ufi = obj();
		$ufi->set_class_id(CL_SHOP_UNIT_FORMULA);
		foreach($res["amounts"] as $oid => $whdata)
		{
			$o = obj($oid);
			if($o->class_id() != CL_SHOP_PRODUCT)
			{
				continue;
			}
			foreach($whdata as  $whid => $unitdata)
			{
				$unit = $res["units"][$oid][0];
				$add = $add_amts[$whid][$oid];
				if(count($add))
				{
					foreach($add as $u => $add_amt)
					{
						if($unit == $u)
						{
							$res["amounts"][$oid][$whid][$unit] += $add_amt;
						}
						else
						{
							$fo = $ufi->get_formula(array(
								"from_unit" => $u,
								"to_unit" => $unit,
								"product" => $o,
							));
							if($fo)
							{
								$amt = $ufi->calc_amount(array(
									"amount" => $add_amt,
									"prod" => $o,
									"obj" => $fo,
								));
								$res["amounts"][$oid][$whid][$unit] +=  $amt;
							}
						}
					}
				}
			}
		}
	}

	private function decide_search_method($arr)
	{
		if ($this->can_use_products_index() === false)
		{
			return 'regular';
		}

		$fields = array(
		//	'prod_s_name' => 'prod_s_name',
		//	'prod_s_code' => 'prod_s_code',
			'prod_s_barcode' => 'prod_s_barcode',
			'prod_s_cat' => 'prod_s_cat',
			'prod_s_count' => 'prod_s_count',
			'prod_s_price_from' => 'prod_s_price_from',
			'prod_s_price_to' => 'prod_s_price_to',
		//	'prod_s_pricelist' => 'prod_s_pricelist'
		);
		foreach ($fields as $field)
		{
			$value = automatweb::$request->arg($field);
			if (!empty($value))
			{
				return 'regular';
			}
		}

		return 'index';
	}

	private function get_products_list_ol($arr)
	{
		$oids = array();
		$params = array();
		if($name = automatweb::$request->arg("prod_s_name"))
		{
			$params["name"] = "%".$name."%";
		}
		if($code = automatweb::$request->arg("prod_s_code"))
		{
			if($this->config and $cid = $this->config->prop("short_code_ctrl"))
			{
				$short_code = get_instance(CL_CFGCONTROLLER)->check_property($cid, null, $code, null, null, null);
				$params[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						"code" => "%".$code."%",
						"short_code" => "%".$short_code."%",
					),
				));
			}
			else
			{
				$params["code"] = "%".$code."%";
			}
		}
		if($barcode = automatweb::$request->arg("prod_s_barcode"))
		{
			$params["barcode"] = "%".$barcode."%";
		}

		$group = $this->get_search_group($arr);
		if(($from = automatweb::$request->arg($group."_s_price_from")) || ( automatweb::$request->arg($group."_s_price_to")))
		{
			$to = $arr["request"][$group."_s_price_to"];
			$cparams = array(
				"class_id" => CL_SHOP_ITEM_PRICE,
				"currency" => $this->def_currency,
				"valid_from" => new obj_predicate_compare(OBJ_COMP_LESS, time()),
				"valid_to" => new obj_predicate_compare(OBJ_COMP_GREATER, time()),
			);
			if($from and $to)
			{
				$cparams["price"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $from, $to);
			}
			elseif($from)
			{
				$cparams["price"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $from);
			}
			elseif($to)
			{
				$cparams["price"] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $to);
			}
			$cparams["warehouse"] = $arr["obj_inst"]->id();
			$ol = new object_list($cparams);
			$oids = array();
			foreach($ol->arr() as $o)
			{
				$prod = $o->prop("product");
				$oids[$prod] = $prod;
			}
			$params["oid"] = isset($params["oid"])?array_intersect($params["oid"], $oids):$oids;
		}
		// get items arr
		if(!($cat = automatweb::$request->arg($group."_s_art_cat")) and !($cat = automatweb::$request->arg($group."_s_cat")))
		{
			$cat = automatweb::$request->arg("pgtf");
		}
		if ($this->can("view", $cat))
		{
			$cato = obj($cat);
			$conn = $cato->connections_to(array(
				"type" => "RELTYPE_CATEGORY",
				"from.class_id" => CL_SHOP_PRODUCT,
			));
			$oids = array();
			foreach($conn as $c)
			{
				$oids[$c->prop("from")] = $c->prop("from");
			}
			$params["class_id"] = CL_SHOP_PRODUCT;
			$params["oid"] = isset($params["oid"])?array_intersect($params["oid"], $oids):$oids;
		}
		elseif($this->can("view", automatweb::$request->arg("ptf")))
		{
			$params["parent"] = $arr["request"]["ptf"];
			$params["class_id"] = array(CL_MENU, CL_SHOP_PRODUCT);
		}
		if(count($params) || automatweb::$request->arg("just_saved"))
		{
			$show = 1;
		}
		$params["limit"] = "0,100";
		if(empty($show))
		{
			foreach($arr["request"] as $var => $val)
			{
				if(strpos($var, $group."_s_") !== false)
				{
					$show = 1;
				}
			}
		}
		if(!empty($show))
		{
			if(empty($params["class_id"]))
			{
				$params["class_id"] = CL_SHOP_PRODUCT;
			}
			if(isset($params["oid"]) and !count($params["oid"]))
			{
				$params["oid"] = array(-1);
			}
			$ot = new object_list($params);
		}
		else
		{
			$ot = new object_list();
		}
		$p = automatweb::$request->arg($group."_s_show_pieces");
		$s = automatweb::$request->arg($group."_s_show_batches");
		if(($p || $s) and $ot->count())
		{
			$sparams["class_id"] = CL_SHOP_PRODUCT_SINGLE;
			$sparams["product"] = $ot->ids();
			$sparams["type"] = array();
			if($p)
			{
				$sparams["type"][] = 2;
			}
			if($s)
			{
				$sparams["type"][] = 1;
			}
			$ot->add(new object_list($sparams));
		}
		$pi = get_instance(CL_SHOP_PRODUCT);

	// I'll comment this replacement prods thingie out here for now
		$replacement_products = $this->find_replacement_products($ot);
		$ot->add($replacement_products);

		foreach($ot->arr() as $o)
		{
			$remove = $below_min = $chk_min = $prodtotal = 0;
			if($o->class_id() == CL_SHOP_PRODUCT)
			{
				$prodid = $o->id();
				$single = null;
				if(empty($res["units"][$prodid]))
				{
					$res["units"][$prodid] = $pi->get_units(obj($prodid));
				}
			}
			elseif($o->class_id() == CL_SHOP_PRODUCT_SINGLE)
			{
				$prodid = $o->prop("product");
				$single = $o->id();
			}
			foreach(safe_array(ifset($arr, "warehouses")) as $wh)
			{
				foreach($res["units"][$prodid] as $unit)
				{
					if(!$unit)
					{
						continue;
					}
					$amt = $pi->get_amount(array(
						"prod" => $prodid,
						"warehouse" => $wh,
						"single" => $single,
						"unit" => $unit,
					));
					$res["amounts"][$o->id()][$wh][$unit] = 0;
					if($amt)
					{
						foreach($amt->arr() as $a)
						{
							$res["amounts"][$o->id()][$wh][$unit] += $a->prop("amount");
						}
					}
				}
				if(automatweb::$request->arg($group."_s_below_min") and $o->class_id() == CL_SHOP_PRODUCT)
				{
					$chk_min = 1;
					$a = $res["amounts"][$o->id()][$wh][$res["units"][$prodid][0]];
					$min = $o->prop("wh_minimum");
					if($a < $min)
					{
						$below_min = 1;
					}
				}
				$prodtotal += isset($res["units"][$prodid][0]) and isset($res["amounts"][$o->id()][$wh][$res["units"][$prodid][0]]) ? $res["amounts"][$o->id()][$wh][$res["units"][$prodid][0]] : 0;
			}
			$a = $prodtotal;
			if(($q = automatweb::$request->arg($group."_s_count")) and $o->class_id() == CL_SHOP_PRODUCT)
			{
				if(($q == QUANT_NEGATIVE and $a >= 0) || ($q == QUANT_ZERO and $a != 0) || ($q == QUANT_POSITIVE and $a <= 0))
				{
					$remove = 1;
				}
			}
			if($chk_min and !$below_min)
			{
				$remove = 1;
			}
			if($remove)
			{
				$this->ol_remove_prod($ot, $o);
			}
			$res["amounts"][$o->id()]["total"] = $prodtotal;

		}

		if($group === "storage_prognosis")
		{
			$this->calc_prognosis_amounts($res, $arr);
		}
		$res["ol"] = $ot;
		return $res;
	}

	public function get_products_list_from_index($arr, $request = null)
	{
		$params = array();
		if (!$request)
		{
			$request = automatweb::$request;
		}
		if($code = $request->arg("prod_s_code"))
		{
			if($this->config and $cid = $this->config->prop("short_code_ctrl"))
			{
				$short_code = get_instance(CL_CFGCONTROLLER)->check_property($cid, null, $code, null, null, null);
				$params[] = "(
					code LIKE '%".$code."%' OR
					short_code LIKE '%".$short_code."%' OR
					search_term LIKE '%".$code."%' OR
					search_term LIKE '%".$short_code."%'
				)";
			}
			else
			{
				$params[] = "code like '%".$code."%'";
			}
		}

		if($name = $request->arg("prod_s_name"))
		{
			$params["name"] = "name like '%".$name."%'";
		}

		$ol = new object_list();
		if (!empty($params))
		{
			$res = $this->db_fetch_array("select oid from aw_shop_products_index where ".implode(' AND ', $params)."");
			$oids = array();
			foreach ($res as $r)
			{
				$oids[] = $r['oid'];
			}

			if (!empty($oids))
			{
				$ol = new object_list(array(
					'class_id' => CL_SHOP_PRODUCT,
					'oid' => $oids
				));
			}
		}

		return array(
			'ol' => $ol,
		);

	}

	private function find_replacement_products($prods)
	{
		if ($prods->count() <= 0)
		{
			return new object_list();
		}
		$replacements_codes = array();

		foreach ($prods->arr() as $prod_id => $prod)
		{
			if($replacement_code = $prod->prop('type_code'))
			{
				$replacements_codes[$prod_id] = $replacement_code;
			}
		}

		if(count($replacements_codes) > 0)
		{
			$ol = new object_list(array(
				'class_id' => CL_SHOP_PRODUCT,
				'user1' => $replacements_codes
			));
		}
		else
		{
			$ol = new object_list();
		}

		return $ol;
	}

	private function ol_remove_prod($ol, $ro)
	{
		foreach($ol->arr() as $o)
		{
			if($o->class_id() == CL_SHOP_PRODUCT_SINGLE and $o->prop("product") == $ro->id())
			{
				$ol->remove($o);
			}
		}
		$ol->remove($ro);
	}

	protected function _init_prod_package_list_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->define_chooser();
		$t->define_field(array(
			"name" => "jrk",
			"caption" => t("J&auml;rjekord"),
			"align" => "center",
			"width" => 60,
			"sortable" => true,
			"sorting_field" => "jrk_num",
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "left",
			"sortable" => true,
		));
		$t->define_field(array(
			"name" => "price",
			"caption" => t("Hind"),
			"align" => "center",
			"width" => 60,
			"sortable" => true,
			"sorting_field" => "price_num",
		));
		if($this->get_warehouse_configs($arr, "show_purveyance"))
		{
			$t->define_field(array(
				"name" => "purveyance",
				"caption" => t("Tarnijad"),
				"align" => "left",
			));
		}

		$t->set_numeric_field(array("jrk_num", "price_num"));
	}

	protected function get_products_packages_list($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_prod_package_list_tbl($arr);

		$ptf_o = obj(automatweb::$request->arg("ptf"));
		$t->set_caption(sprintf(t("Toote '%s' pakendid"), parse_obj_name($ptf_o->name())));

		$data = $ptf_o->get_packagings(array("odl" => true))->arr();

		$show_purveyance = $this->get_warehouse_configs($arr, "show_purveyance");
		if($show_purveyance)
		{
			$purveyance_odl = new object_data_list(
				array(
					"class_id" => CL_SHOP_PRODUCT_PURVEYANCE,
					"object" => array_merge(array(-1), array_keys($data)),
					"company" => new obj_predicate_compare(OBJ_COMP_GREATER, 0),
				),
				array(
					CL_SHOP_PRODUCT_PURVEYANCE => array("company.name", "company", "object"),
				)
			);
			$purveyance_urls = array();
			foreach($purveyance_odl->arr() as $pdata)
			{
				$purveyance_urls[$pdata["object"]][] = html::href(array(
					"caption" => parse_obj_name($pdata["company.name"]),
					"url" => $this->mk_my_orb("change", array("id" => $pdata["company"], "return_url" => get_ru()), crm_company_obj::CLID),
				));
			}
		}

		foreach($data as $oid => $odata)
		{
			$row = array(
				"oid" => $oid,
				"name" => html::href(array(
					"url" => $this->mk_my_orb("change", array("id" => $oid, "return_url" => get_ru()), CL_SHOP_PRODUCT_PACKAGING),
					"caption" => parse_obj_name($odata["name"]),
				)),
				"price" => html::textbox(array(
					"name" => "products_list[$oid][price]",
					"value" => $odata["price"],
					"size" => 3,
				)),
				"jrk" => html::textbox(array(
					"name" => "products_list[$oid][jrk]",
					"value" => $odata["jrk"],
					"size" => 3,
				)),
				"price_num" => $odata["price"],
				"jrk_num" => $odata["price"],
			);

			if($show_purveyance and isset($purveyance_urls[$oid]))
			{
				$row["purveyance"] = implode(", ", $purveyance_urls[$oid]);
			}

			$t->define_data($row);
		}
	}
	/**
		@attrib name=ajax_update_products_table all_args=1
		@param id required type=int
	**/
	function ajax_update_products_table($arr)
	{
		$this->config = obj(obj($arr["id"])->prop("conf"));

		classload('vcl/table');
		$t = new vcl_table();

		$arr['prop'] = array('vcl_inst' => $t);
		$arr['obj_inst'] = new object($arr['id']);

		$this->get_products_list($arr);

		print iconv(aw_global_get("charset"), "UTF-8", $t->get_html());

		exit();
	}

	function get_products_list(&$arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$group = $this->get_search_group($arr);
		$show_purveyance = $this->get_warehouse_configs($arr, "show_purveyance");

		if ($this->can("view", automatweb::$request->arg("pgtf")))
		{
			$tb->set_caption(sprintf(t("Artiklid kategoorias %s"), obj($arr["request"]["pgtf"])->path_str(array("start_at" => $this->config->prop("prod_type_fld")))));
		}
		elseif($this->can("view", automatweb::$request->arg("ptf")))
		{
			$ptf_o = obj(automatweb::$request->arg("ptf"));
			if($ptf_o->is_a(CL_SHOP_PRODUCT))
			{
				return $this->get_products_packages_list($arr);
			}
			else
			{
				$tb->set_caption(sprintf(t("Artiklid kaustas %s"), $ptf_o->path_str(array("start_at" => $this->config->prop("prod_fld")))));
			}
		}
		if($arr["obj_inst"]->class_id() == CL_SHOP_WAREHOUSE)
		{
			$arr["warehouses"] = array($arr["obj_inst"]->id());
		}
		$this->_init_prod_list_list_tbl($tb, $arr);

		// lets try to implement here the separate search method thingie
		// the idea is, that decide_search_method somehow decides if it can search
		// index table or has to make the complex search --dragut
		$search_method = $this->decide_search_method($arr);

		switch ($search_method)
		{
			case 'index':
				$res = $this->get_products_list_from_index($arr);
				break;
			case 'regular':
				$res = $this->get_products_list_ol($arr);
				break;
		}

		classload("core/icons");
		$pi = get_instance(CL_SHOP_PRODUCT);
		//$ol = $ot->to_list();
		$ol = $res["ol"]->arr();

		if($show_purveyance)
		{
			$purveyance_odl = new object_data_list(
				array(
					"class_id" => CL_SHOP_PRODUCT_PURVEYANCE,
					"object" => array_merge(array(-1), array_keys($ol)),
					"company" => new obj_predicate_compare(OBJ_COMP_GREATER, 0),
				),
				array(
					CL_SHOP_PRODUCT_PURVEYANCE => array("company.name", "company", "object"),
				)
			);
			$purveyance_urls = array();
			foreach($purveyance_odl->arr() as $pdata)
			{
				$purveyance_urls[$pdata["product"]][] = html::href(array(
					"caption" => parse_obj_name($pdata["company.name"]),
					"url" => $this->mk_my_orb("change", array("id" => $pdata["company"], "return_url" => get_ru()), crm_company_obj::CLID),
				));
			}
		}

		foreach($ol as $o)
		{
			if ($o->class_id() == CL_MENU)
			{
				$tp = t("Kaust");
			}
			else
			if (is_oid($o->prop("item_type")))
			{
				$tp = obj($o->prop("item_type"));
				$tp = $tp->name();
			}
			else
			{
				$tp = "";
			}

			$get = "";
			if ($o->prop("item_count") > 0)
			{
				$get = html::href(array(
					"url" => $this->mk_my_orb("create_export", array(
						"id" => $arr["obj_inst"]->id(),
						"product" => $o->id()
					)),
					"caption" => t("V&otilde;ta laost")
				));
			}

			$put = "";
			if ($o->class_id() != CL_MENU)
			{
				$put = html::href(array(
					"url" => $this->mk_my_orb("create_reception", array(
						"id" => $arr["obj_inst"]->id(),
						"product" => $o->id()
					)),
					"caption" => t("Vii lattu")
				));
			}

			$name = $o->path_str(array("to" => $this->prod_fld));
			if ($o->class_id() == CL_MENU)
			{
				$name = html::href(array(
					"url" => aw_url_change_var("ptf", $o->id()),
					"caption" => $name
				));
			}
			elseif($o->class_id() == CL_SHOP_PRODUCT)
			{
				$prodid = $o->id();
			}
			elseif($o->class_id() == CL_SHOP_PRODUCT_SINGLE)
			{
				$prodid = $o->prop("product");
			}
			$data = array(
				"icon" => html::img(array("url" => icons::get_icon_url($o->class_id(), $o->name()))),
				"oid" => $o->id(),
				"name" => html::obj_change_url($o, parse_obj_name($o->name())), //$name,
				"cnt" => $o->prop("item_count"),
				"item_type" => $tp,
				"change" => html::href(array(
					"url" => $this->mk_my_orb("change", array(
						"id" => $o->id(),
						"return_url" => get_ru()
					), $o->class_id()),
					"caption" => t("Muuda")
				)),
				"get" => $get,
				"put" => $put,
				"code" => $o->prop("code"),
				"last_purchase_price" => $pi->get_last_purchase_price($o),
				"price_fifo" => $pi->get_fifo_price($o, $arr["obj_inst"]->id()),
				"del" => html::checkbox(array(
					"name" => "sel[]",
					"value" => $o->id()
				)),
				"is_menu" => ($o->class_id() == CL_MENU ? 0 : 1),
				"ord" => html::textbox(array(
					"name" => "set_ord[".$o->id()."]",
					"value" => $o->ord(),
					"size" => 5
				)).html::hidden(array(
					"name" => "old_ord[".$o->id()."]",
					"value" => $o->ord()
				)),
				"hidden_ord" => $o->ord(),
				"prodid" => $prodid,
				"prodname" => obj($prodid)->name(),
				"clid" => $o->class_id(),
				"type" => ($o->class_id() == CL_SHOP_PRODUCT)?"":t("&Uuml;ksiktooted"),
			);
			if($this->def_price_list || automatweb::$request->arg($group."_s_pricelist"))
			{
				$data["sales_price"] = $pi->calc_price($o, $this->get_warehouse_configs($arr, "def_currency"), $arr["request"][$group."_s_pricelist"]);
			}
			foreach($arr["warehouses"] as $wh)
			{
				foreach(safe_array($res["units"][$prodid]) as $i=>$unit)
				{
					if(!$unit)
					{
						continue;
					}
					$uo = obj($unit);
					$data["amount_".$wh."_".$i] = $res["amounts"][$o->id()][$wh][$unit]." ".$uo->prop("unit_code");
				}
				$data["saldo_".$wh] = $pi->get_saldo($o, $wh);
			}
			$conn = $o->connections_from(array(
				"type" => "RELTYPE_CATEGORY",
			));
			$cats = array();
			foreach($conn as $c)
			{
				$cats[] = $c->prop("to.name");
			}
			$data["cat"] = implode(',', $cats);
			if($show_purveyance and isset($purveyance_urls[$o->id()]))
			{
				$data["purveyance"] = implode(", ", $purveyance_urls[$o->id()]);
			}
			$tb->define_data($data);
		}
		$tb->set_numeric_field("hidden_ord");
		$tb->set_default_sortby("name");
		$tb->set_default_sorder("asc");
		$tb->sort_by();
	}

	private function _init_prod_list_list_tbl($t, $arr)
	{
		$group = $this->get_search_group($arr);
		$t->define_field(array(
			"name" => "icon",
			"caption" => t("&nbsp;"),
			"sortable" => 0,
		));

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "code",
			"caption" => t("Kood"),
			"align" => "center"
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "last_purchase_price",
			"caption" => t("Ostuhind"),
			"align" => "center"
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "price_fifo",
			"caption" => t("FIFO"),
			"align" => "center"
		));
		if((!isset($arr["request"][$group."_s_pricelist"]) and !empty($this->def_price_list)) || automatweb::$request->arg($group."_s_pricelist"))
		{
			$t->define_field(array(
				"sortable" => 1,
				"name" => "sales_price",
				"caption" => t("M&uuml;&uuml;gihind"),
				"align" => "center"
			));
		}
		if(empty($this->no_count))
		{
			$levels = 0;
			$chk = 1;
			if($group === "storage_prognosis")
			{
				$chk = false;
			}
			elseif($arr["obj_inst"]->class_id() != CL_SHOP_WAREHOUSE)
			{
				$chk = $arr["obj_inst"]->prop("show_alt_units");
			}
			if(count($this->get_warehouse_configs($arr, "has_alternative_units")) and $chk)
			{
				$levels += (int)$this->get_warehouse_configs($arr, "alternative_unit_levels");
			}
			if(isset($arr["warehouses"]))//kust see yldse peaks tulema?
			{
				foreach($arr["warehouses"] as $wh)
				{
					if(!$this->can("view", $wh))
					{
						continue;
					}
					for($i = 0; $i <= $levels; $i++)
					{
						if(count($arr["warehouses"]) == 1)
						{
							$cp = $i?sprintf(t("Kogus %s"),$i+1):t("Kogus");
						}
						else
						{
							$who = obj($wh);
							$cp = $i?sprintf(t("%s kogus %s"), $who->name(), $i+1):sprintf(t("%s kogus"), $who->name());
						}
						$t->define_field(array(
							"sortable" => 1,
							"name" => "amount_".$wh."_".$i,
							"caption" => $cp,
							"align" => "center"
						));
					}
					if(count($arr["warehouses"]) == 1)
					{
						$cp = t("Saldo");
					}
					else
					{
						$who = obj($wh);
						$cp = sprintf(t("%s saldo"), $who->name());
					}
					$t->define_field(array(
						"sortable" => 1,
						"name" => "saldo_".$wh,
						"caption" => $cp,
						"align" => "center",
					));
				}
			}
		}

		if($arr["obj_inst"]->prop("order_center"))
		{
			$t->define_field(array(
				"name" => "ord",
				"sortable" => 1,
				"caption" => t("J&auml;rjekord"),
				"align" => "center",
				"sorting_field" => "hidden_ord",
			));
		}

		if(!automatweb::$request->arg("pgtf") and !empty($this->prod_type_fld) and !automatweb::$request->arg($group."_s_cat"))
		{
			$t->define_field(array(
				"name" => "cat",
				"caption" => t("Kategooria"),
				"sortable" => 1,
				"align" => "center",
			));
		}

		$ol = new object_list(array(
			"class_id" => CL_SHOP_PRODUCT_TYPE,
		));
		if($ol->count()>0)
		{
			$t->define_field(array(
				"sortable" => 1,
				"name" => "item_type",
				"caption" => t("T&uuml;&uuml;p"),
				"align" => "center"
			));
		}

		if($this->get_warehouse_configs($arr, "show_purveyance"))
		{
			$t->define_field(array(
				"name" => "purveyance",
				"caption" => t("Tarnijad"),
				"align" => "left",
			));
		}

/*		$conf = obj($arr["obj_inst"]->prop("conf"));
		if (!$conf->prop("no_count"))
		{
			$t->define_field(array(
				"sortable" => 1,
				"name" => "cnt",
				"caption" => t("Kogus laos"),
				"align" => "center",
				"type" => "int"
			));

			$t->define_field(array(
				"name" => "get",
				"caption" => t("V&otilde;ta laost"),
				"align" => "center"
			));

			$t->define_field(array(
				"name" => "put",
				"caption" => t("Vii lattu"),
				"align" => "center"
			));
		}

		$t->define_field(array(
			"name" => "change",
			"caption" => t("Muuda"),
			"align" => "center"
		));*/

		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	private function _init_view(&$arr)
	{
		if (!$arr["obj_inst"]->prop("conf"))
		{
			if($arr["prop"]["type"] === "table" || $arr["prop"]["type"] === "toolbar")
			{
				$arr["prop"]["value"] =  null;
				$this->show_error_text(t("VIGA: konfiguratsioon on valimata!"));
			}
			return false;
		}
		$this->config = obj($arr["obj_inst"]->prop("conf"));
		//"prod_type_cfgform",
		$checks = array("prod_fld", "pkt_fld", "reception_fld", "export_fld", "prod_type_fld", "order_fld", "buyers_fld", "def_currency");

		$err["prod_fld"] =  t("VIGA: konfiguratsioonist on toodete kataloog valimata!");
		$err["pkt_fld"] =  t("VIGA: konfiguratsioonist on pakettide kataloog valimata!");
		$err["reception_fld"] =  t("VIGA: konfiguratsioonist on sissetulekute kataloog valimata!");
		$err["export_fld"] =  t("VIGA: konfiguratsioonist on v&auml;jaminekute kataloog valimata!");
		$err["prod_type_fld"] =  t("VIGA: konfiguratsioonist on toodete t&uuml;&uuml;pide kataloog valimata!");
		$err["order_fld"] =  t("VIGA: konfiguratsioonist on tellimuste kataloog valimata!");
		$err["buyers_fld"] =  t("VIGA: konfiguratsioonist on tellijate kataloog valimata!");
		$err["def_currency"] =  t("VIGA: konfiguratsioonist on vaikimisi valuuta valimata!");
		foreach($checks as $check)
		{
			if(!$this->config->prop($check))
			{
				if($arr["prop"]["type"] === "table" || $arr["prop"]["type"] === "toolbar")
				{
					$arr["prop"]["value"] = $err[$check];
				}
				return false;
			}
		}

		$this->prod_fld = $this->config->prop("prod_fld");
		$this->prod_tree_root = isset($arr["request"]["ptf"]) ? $arr["request"]["ptf"] : (isset($arr["request"]["pgtf"])? null : $this->config->prop("prod_fld"));

		$this->pkt_fld = $this->config->prop("pkt_fld");
		$this->pkt_tree_root = isset($_GET["tree_filter"]) ? $_GET["tree_filter"] : $this->config->prop("pkt_fld");

		$this->prod_type_cfgform = $this->config->prop("prod_type_cfgform");
		$this->reception_fld = $this->config->prop("reception_fld");
		$this->export_fld = $this->config->prop("export_fld");
		$this->prod_type_fld = $this->config->prop("prod_type_fld");
		$this->order_fld = $this->config->prop("order_fld");
		$this->buyers_fld = $this->config->prop("buyers_fld");
		$this->prod_conf_folder = $this->config->prop("prod_conf_folder");
		$this->def_price_list = $this->config->prop("def_price_list");
		$this->def_currency = $this->config->prop("def_currency");
		$this->no_count = $this->config->prop("no_count");
		return true;
	}

	function mk_pkt_toolbar(&$data)
	{
		$tb = $data["prop"]["toolbar"];

		$tb->add_menu_button(array(
			"name" => "create_pkt",
			"tooltip" => t("Uus")
		));

		$tb->add_menu_item(array(
			"parent" => "create_pkt",
			"text" => t("Pakett"),
			"link" => $this->mk_my_orb("new", array(
				"parent" => $this->pkt_tree_root,
				"alias_to" => $data["obj_inst"]->id(),
				"reltype" => 2, //RELTYPE_PACKET,
				"return_url" => get_ru()
			), CL_SHOP_PACKET)
		));
/*
		$tb->add_button(array(
			"name" => "del",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta valitud"),
			"url" => "javascript:document.changeform.submit()"
		));
*/
$tb->add_delete_button();

		$tb->add_button(array(
			"name" => "save",
			"img" => "save.gif",
			"tooltip" => t("Lisa korvi"),
			"action" => "add_to_cart"
		));

		$tb->add_button(array(
			"name" => "copy",
			"img" => "copy.gif",
			"tooltip" => t("Kopeeri"),
			"action" => "copy_products"
		));

		$tb->add_button(array(
			"name" => "cut",
			"img" => "cut.gif",
			"tooltip" => t("L&otilde;ika"),
			"action" => "cut_products"
		));

		$tb->add_button(array(
			"name" => "paste",
			"img" => "paste.gif",
			"tooltip" => t("Kleebi"),
			"url" => $this->mk_my_orb("paste_products", array(
				"parent" => $this->prod_tree_root,
				"return_url" => get_ru(),
			))
			//"action" => "paste_products"
		));

		$tb->add_menu_button(array(
			"name" => "active",
//			"img" => "delete.gif",
			"text" => t("Aktiivsus"),
			"tooltip" => t("Tee pakette aktiivseteks ja mitteaktiivseteks"),
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
	}

	function _get_packets_tree($arr)
	{
		return $this->_get_product_management_category_tree($arr);
/*
		$ot = new object_tree(array(
			"parent" => $this->config->prop("pkt_fld"),
			"class_id" => CL_MENU,
			"status" => array(STAT_ACTIVE, STAT_NOTACTIVE),
			"sort_by" => "objects.jrk"
		));

		$arr["prop"]["vcl_inst"] = treeview::tree_from_objects(array(
			"tree_opts" => array(
				"type" => TREE_DHTML,
				"tree_id" => "pkts",
				"persist_state" => true,
			),
			"root_item" => obj($this->config->prop("pkt_fld")),
			"ot" => $ot,
			"var" => "tree_filter"
		));*/
	}

	function _get_packets_list_old(&$arr)
	{
		return $this->_get_packets_list($arr);
	}

	function _get_packets_list(&$arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$request = $arr["request"];
		$this->_init_pkt_list_list_tbl($tb, $arr["obj_inst"]);
		$tree_filter = array(
//			"parent" => isset($this->pkt_tree_root) ? $this->pkt_tree_root : 1,
//			"class_id" => array(CL_MENU,CL_SHOP_PACKET),
			"class_id" => array(CL_SHOP_PACKET),
			"status" => array(STAT_ACTIVE, STAT_NOTACTIVE),
			"limit" => 300,
			"sort_by" => "name asc",
		);




//-----------paketi loomise aja j2rgi filtreerimine------------- toimib kui v2hemalt aasta on valitud
		$search_from = false;
		$search_to = false;
		if(!empty($arr["request"]['packets_s_created_from']['year']))
		{
			$search_from = true;
			$search_from_time = mktime(0,0,0,!empty($request['packets_s_created_from']['month']) ? $request['packets_s_created_from']['month'] : 1,!empty($request['packets_s_created']['day']) ? $request['packets_s_created_from']['day'] : 1,$request['packets_s_created_from']['year']);

		}
		if(!empty($arr["request"]['packets_s_created_to']['year']))
		{
			$search_to = true;
			$search_to_time = mktime(0,0,0,
				empty($request['packets_s_created_to']['month']) ? 1 : (empty($request['packets_s_created_to']['day']) ? $request['packets_s_created_to']['month'] + 1 : $request['packets_s_created_to']['month']),
				!empty($request['packets_s_created_to']['day']) ? $request['packets_s_created_to']['day'] : 1,
				empty($request['packets_s_created_to']['month']) ? $request['packets_s_created_to']['year']+1 :  $request['packets_s_created_to']['year']
			) - 1;
		}

		if($search_from and $search_to)
		{
			$tree_filter["created"]  = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $search_from_time, $search_to_time);
		}
		elseif($search_from)
		{
			$tree_filter["created"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $search_from_time);
		}
		elseif($search_to)
		{
			$tree_filter["created"] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $search_to_time);
		}

//-------------------------------

		if(!empty($arr["request"]["product_managements_name"]))
		{
			$tree_filter["name"] = "%".$arr["request"]["product_managements_name"]."%";
		}
		if(!empty($arr["request"]["packets_s_active"]) and $arr["request"]["packets_s_active"] > 0)
		{
			$tree_filter["status"] = $arr["request"]["packets_s_active"];
		}
		if(!empty($arr["request"]["packets_s_cat"]))
		{
			$tree_filter["CL_SHOP_PACKET.RELTYPE_CATEGORY.name"] = "%".$arr["request"]["packets_s_cat"]."%";
		}

		if(!empty($arr["request"]["cat"]))
		{
			$filter = array(
				"class_id" => CL_SHOP_PACKET,
			);
			$cats = $this->get_categories_from_search($arr["request"]);

			$filter["CL_SHOP_PACKET.RELTYPE_CATEGORY"] = $cats;
			if(is_array($cats) and !sizeof($cats))
			{
				$tree_filter["oid"] = 1;
			}
			else
			{
				$packets = new object_list($filter);
				if($packets->count())
				{
					$tree_filter["oid"] = $packets->ids();
				}
				else
				{
					$tree_filter["oid"] = 1;
				}
			}
		}
		if(isset($cats) and sizeof($cats) and $this->can("view" , reset($cats)))
		{
			$cat_obj = obj(reset($cats));
			$tb->set_caption(sprintf(t("Kategooriates: %s"), $cat_obj->name()));
		}



		// get items

/*		$ot = new object_tree($tree_filter);
		$ol = $ot->to_list();*/

		$ol = new object_list($tree_filter);

//		$products = array();
//		$products = $arr["obj_inst"]->get_packet_products($ol->ids());
//		foreach(get_packet_products() as $data)
//		{
//			$products[] = $this->change_link($id , $name);
//			$products[] = html::obj_change_url($product, $product->name());
//		}


		for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
		{
			if ($o->class_id() == CL_MENU)
			{
				continue;
			}

			$get = "";
//milleks see vajalik on? - Marko
			if ($o->prop("item_count") > 0)
			{
				$get = html::href(array(
					"url" => $this->mk_my_orb("create_export", array(
						"id" => $arr["obj_inst"]->id(),
						"product" => $o->id()
					)),
					"caption" => t("V&otilde;ta laost")
				));
			}

			$products = array();
			foreach($o->get_products()->names() as $id => $name)
			{
				$products[] = $this->change_link($id , $name);
//				$products[] = html::obj_change_url($product, $product->name());
			}

			$tb->define_data(array(
				"name" => html::obj_change_url($o, $o->path_str(array("to" => $this->pkt_fld))),
				"cnt" => $o->prop("item_count"),
				"change" => html::href(array(
					"url" => $this->mk_my_orb("change", array(
						"id" => $o->id(),
						"return_url" => get_ru()
					), CL_SHOP_PACKET),
					"caption" => t("Muuda")
				)),
				"get" => $get,
				"oid" => $o->id(),
				"put" => html::href(array(
					"url" => $this->mk_my_orb("create_reception", array(
						"id" => $arr["obj_inst"]->id(),
						"product" => $o->id()
					)),
					"caption" => t("Vii lattu")
				)),
				"products" => join(",<br>" , $products),
				"categories" => join(", " , $o->get_categories()->names()),
				"color" => $o->status() == 2 ? "#99FF99" : "#E1E1E1",
			));
		}
		$tb->set_caption(t("Pakettide nimekiri"));
	}

	private function _init_pkt_list_list_tbl($t, $o)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "code",
			"caption" => t("Kood"),
			"align" => "center",
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "products",
			"caption" => t("Tooted"),
			"align" => "left",
			"chgbgcolor" => "color",
		));
/*
		$t->define_field(array(
			"sortable" => 1,
			"name" => "last_purchase_price",
			"caption" => t("Ostuhind"),
			"align" => "center"
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "price_fifo",
			"caption" => t("FIFO"),
			"align" => "center"
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "sales_price",
			"caption" => t("M&uuml;&uuml;gihind"),
			"align" => "center"
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "amount1",
			"caption" => t("Kogus"),
			"align" => "center"
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "unit1",
			"caption" => t("&Uuml;hik"),
			"align" => "center"
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "amount2",
			"caption" => t("Kogus 2"),
			"align" => "center"
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "unit2",
			"caption" => t("&Uuml;hik 2"),
			"align" => "center"
		));
*/
/*
		$t->define_field(array(
			"name" => "ord",
			"caption" => t("J&auml;rjekord"),
			"align" => "center"
		));*/
/*
		$t->define_field(array(
			"sortable" => 1,
			"name" => "item_type",
			"caption" => t("T&uuml;&uuml;p"),
			"align" => "center"
		));
*/

		$conf = obj($o->prop("conf"));
		if (!$conf->prop("no_count"))
		{
			$t->define_field(array(
				"sortable" => 1,
				"name" => "cnt",
				"caption" => t("Kogus laos"),
				"align" => "center",
				"type" => "int",
				"chgbgcolor" => "color",
			));

			$t->define_field(array(
				"name" => "get",
				"caption" => t("V&otilde;ta laost"),
				"align" => "center",
				"chgbgcolor" => "color",
			));

			$t->define_field(array(
				"name" => "put",
				"caption" => t("Vii lattu"),
				"align" => "center",
				"chgbgcolor" => "color",
			));
		}
/*
		$t->define_field(array(
			"name" => "change",
			"caption" => t("Muuda"),
			"align" => "center"
		));
*/

		$t->define_field(array(
			"name" => "categories",
			"caption" => t("Paketiga seotud tootekategooriad"),
			"align" => "center",
			"chgbgcolor" => "color",
		));

		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
			"chgbgcolor" => "color",
		));
	}

	function do_storage_list_tbl(&$arr)
	{
		$this->_init_storage_list_tbl($arr["prop"]["vcl_inst"]);

		$tr = $this->get_packet_folder_list(array("id" => $arr["obj_inst"]->id()));
		$items = $this->get_packet_list(array(
			"id" => $arr["obj_inst"]->id(),
			"parent" => $tr[1]->ids()
		));
		foreach($items as $i)
		{
			if ($i->class_id() == CL_SHOP_PACKET)
			{
				$type = t("Pakett");
				$name = $i->path_str(array("to" => $this->config->prop("pkt_fld")));
			}
			else
			{
				$type = "";
				if (is_oid($i->prop("item_type")))
				{
					$type_o = obj($i->prop("item_type"));
					$type = $type_o->name();
				}
				$name = $i->path_str(array("to" => $this->config->prop("prod_fld")));
			}

			$get = "";
			if ($i->prop("item_count") > 0)
			{
				$get = html::href(array(
					"url" => $this->mk_my_orb("create_export", array(
						"id" => $arr["obj_inst"]->id(),
						"product" => $i->id()
					)),
					"caption" => t("V&otilde;ta laost")
				));
			}

			$arr["prop"]["vcl_inst"]->define_data(array(
				"name" => $name,
				"type" => $type,
				"count" => $i->prop("item_count"),
				"get" => $get,
				"put" => html::href(array(
					"url" => $this->mk_my_orb("create_reception", array(
						"id" => $arr["obj_inst"]->id(),
						"product" => $i->id()
					)),
					"caption" => t("Vii lattu")
				))
			));
		}

		$arr["prop"]["vcl_inst"]->sort_by();
	}

	private function _init_storage_list_tbl($t)
	{
		$t->define_field(array(
			"sortable" => 1,
			"name" => "name",
			"caption" => t("Nimi")
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "type",
			"caption" => t("T&uuml;&uuml;p"),
			"align" => "center"
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "count",
			"caption" => t("Laoseis"),
			"type" => "int",
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "get",
			"caption" => t("V&otilde;ta laost"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "put",
			"caption" => t("Vii lattu"),
			"align" => "center"
		));

	}

	private function _get_storage_ol($arr)
	{
		$group = $this->get_search_group($arr);
		if(isset($arr["request"][$group."_s_type"]) and $arr["request"][$group."_s_type"] == STORAGE_FILTER_BILLS || strpos($group, "bills"))
		{
			$bills = 1;
		}
		elseif(isset($arr["request"][$group."_s_type"]) and $arr["request"][$group."_s_type"] == STORAGE_FILTER_DNOTES || strpos($group, "notes"))
		{
			$dnotes = 1;
		}
		elseif(isset($arr["request"][$group."_s_type"]))
		{
			$bills = 1;
			$dnotes = 1;
		}
		$f = $t = 0;
		if(!empty($arr["request"][$group."_s_from"]))
		{
			$f_ = $arr["request"][$group."_s_from"];
			$f = mktime(0, 0, 0, $f_["month"], $f_["day"], $f_["year"]);
		}
		if(!empty($arr["request"][$group."_s_to"]))
		{
			$t_ = $arr["request"][$group."_s_to"];
			$t = mktime(23, 59, 59, $t_["month"], $t_["day"], $t_["year"]);
		}
		if($t > 1 and $f > 1 and empty($arr["request"]["filt_time"]))
		{
			$timefilter = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $f, $t);
		}
		elseif($f>1 and !$arr["request"]["filt_time"])
		{
			$timefilter = new obj_predicate_compare(OBJ_COMP_GREATER, $f);
		}
		elseif($t>1 and !$arr["request"]["filt_time"])
		{
			$timefilter = new obj_predicate_compare(OBJ_COMP_LESS, $t);
		}
		elseif((strpos($group, "sales") !== false || strpos($group, "purchase") !== false) and (!isset($arr["request"]["filt_time"]) or $arr["request"]["filt_time"] != "all"))
		{
			unset($arr["start"]);
			unset($arr["end"]);
			$v = $this->_get_status_orders_time_filt($arr);
			$timefilter = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $v["filt_start"], $v["filt_end"]);
		}
		$prod_ol = $this->get_art_filter_ol($arr);
		if(!empty($dnotes))
		{
			if($arr["request"][$group."_s_status"] == STORAGE_FILTER_CONFIRMED)
			{
				$aparams["approved"] = 1;
			}
			elseif($arr["request"][$group."_s_status"] == STORAGE_FILTER_UNCONFIRMED)
			{
				$aparams["approved"] = new obj_predicate_not(1);
			}
			if($prod_ol)
			{
				$params["oid"] = array();
				foreach($prod_ol->arr() as $prod)
				{
					$conn = $prod->connections_to(array(
						"from.class_id" => CL_SHOP_DELIVERY_NOTE,
						"type" => "RELTYPE_PRODUCT",
					));
					foreach($conn as $c)
					{
						$params["oid"][] = $c->prop("from");
					}
				}
				if(!count($params["oid"]))
				{
					$params["oid"] = array(-1);
				}
			}
			if($no = $arr["request"][$group."_s_number"])
			{
				$params["number"] = "%".$no."%";
			}
			if($purchaser = $arr["request"][$group."_s_purchaser_id"])
			{
				$params["purchaser"] = "%".$purchaser."%";
			}
			if($purchaser_other = $arr["request"][$group."_s_purchaser_other_id"])
			{
				$params[] = new object_list_filter(array(
					"logic" => "OR",
					array(
						"purchaser(CL_CRM_PERSON).external" => "%".$purchaser_other."%",
						"purchaser(CL_CRM_PERSON).external" => "%".$purchaser_other."%",
					)
				));
			}
			if($arr["request"]["group"] === "storage_income" || $arr["request"]["group"] === "storage" || strpos($group, "purchase") !== false)
			{
				$prop = "to_warehouse";
				$sprop = "impl";
			}
			elseif($arr["request"]["group"] === "storage_export" || strpos($group, "sales") !== false)
			{
				$prop = "from_warehouse";
				$sprop = "customer";
			}
			if($arr["request"][$group."_s_acquiredby"])
			{
				$co = $arr["request"][$group."_s_acquiredby"];
			}
			elseif($this->can("view", $arr["request"]["filt_cust"]))
			{
				$co = obj($arr["request"]["filt_cust"]);
				if($co->class_id() == CL_CRM_CATEGORY)
				{
					$params["CL_SHOP_DELIVERY_NOTE.".$sprop.".RELTYPE_CUSTOMER(CL_CRM_CATEGORY)"] = $co->id();
					unset($co);
				}
				else
				{
					$co = $co->name();
				}
			}
			if($co)
			{
				$params["CL_SHOP_DELIVERY_NOTE.".$sprop.".name"] = "%".$co."%";
			}
			$params[$prop] = $arr["warehouses"];
			if($timefilter)
			{
				$params["delivery_date"] = $timefilter;
			}
			$params["class_id"] = CL_SHOP_DELIVERY_NOTE;
			$params = array_merge($params, $aparams);
			$ol = new object_list($params);
		}
		if(!empty($bills))
		{
			unset($params);
			if($arr["request"][$group."_s_status"])
			{
				$aparams["state"] = $arr["request"][$group."_s_status"];
			}
			$chk = obj($arr["obj_inst"]->prop("conf"));
			$cos = $this->get_warehouse_configs($arr, "manager_cos");
			if(count($cos) and is_array($cos))
			{
				if($prod_ol)
				{
					$params["oid"] = array();
					foreach($prod_ol->arr() as $prod)
					{
						$conn = $prod->connections_to(array(
							"from.class_id" => CL_CRM_BILL_ROW,
							"type" => "RELTYPE_PROD",
						));
						foreach($conn as $c)
						{
							$row = $c->from();
							$bconn = $row->connections_to(array(
								"from.class_id" => CL_CRM_BILL,
								"type" => "RELTYPE_ROW"
							));
							foreach($bconn as $bc)
							{
								$params["oid"][] = $bc->prop("from");
							}
						}
					}
					if(!count($params["oid"]))
					{
						$params["oid"] = array(-1);
					}
				}
				if($no = $arr["request"][$group."_s_number"])
				{
					$params["bill_no"] = "%".$no."%";
				}
				if($arr["request"]["group"] === "storage_income" || $arr["request"]["group"] === "storage" || strpos($group, "purchase") !== false)
				{
					$prop = "customer";
					$sprop = "impl";
				}
				elseif($arr["request"]["group"] === "storage_export" || strpos($group, "sales") !== false)
				{
					$prop = "impl";
					$sprop = "customer";
				}
				$params[$prop] = $cos;
				if($arr["request"][$group."_s_acquiredby"])
				{
					$co = $arr["request"][$group."_s_acquiredby"];
				}
				elseif($this->can("view", $arr["request"]["filt_cust"]))
				{
					$co = obj($arr["request"]["filt_cust"]);
					if($co->class_id() == CL_CRM_CATEGORY)
					{
						$params["CL_CRM_BILL.".$sprop.".RELTYPE_CUSTOMER(CL_CRM_CATEGORY)"] = $co->id();
						unset($co);
					}
					else
					{
						$co = $co->name();
					}
				}
				if($co)
				{
					$params["CL_CRM_BILL.".$sprop.".name"] = "%".$co."%";
				}
				if($timefilter)
				{
					$params["bill_date"] = $timefilter;
				}
				$params["class_id"] = CL_CRM_BILL;
				$params = array_merge($params, $aparams);
				$b_ol = new object_list($params);
				if($b_ol->count())
				{
					if($ol)
					{
						$ol->add($b_ol);
					}
					else
					{
						$ol = $b_ol;
					}
				}
			}
		}
		if(empty($ol))
		{
			$ol = new object_list();
		}
		$ol->sort_by(array(
			"prop" => "created",
			"order" => "desc",
		));
		return $ol;
	}

	function get_warehouse_configs($arr, $prop = null)
	{
		$cfgs = array();
		if(isset($arr["warehouses"]) and !is_array($arr["warehouses"]) and !empty($this->config))
		{
			$cfgs[] = $this->config;
		}
		else
		{
			foreach(safe_array(ifset($arr, "warehouses")) as $wh)
			{
				if($this->can("view", $wh))
				{
					$who = obj($wh);
					$cfgid = $who->prop("conf");
					if($this->can("view", $cfgid))
					{
						$cfgs[] = obj($cfgid);
					}
				}
			}
		}
		if($prop)
		{
			switch($prop)
			{
				case "alternative_unit_levels":
					$high = 0;
					foreach($cfgs as $cfg)
					{
						$val = $cfg->prop($prop);
						if($val > $high)
						{
							$high = $val;
						}
					}
					$vals = $high;
					break;

				default:
					$vals = array();
					if($arr["obj_inst"]->class_id() == CL_SHOP_WAREHOUSE)
					{
						if(isset($this->$prop) and $val = $this->$prop)
						{
							$vals[$val] = $val;
						}
						elseif(isset($this->config) && is_object($this->config) and $val = $this->config->prop($prop))
						{
							if(is_array($val))
							{
								$vals = $vals + $val;
							}
							else
							{
								$vals[$val] = $val;
							}
						}
					}
					else
					{
						foreach($cfgs as $cfg)
						{
							$val = $cfg->prop($prop);
							if(is_array($val))
							{
								foreach($val as $v)
								{
									$vals[$v] = $v;
								}
							}
							elseif(!empty($val))
							{
								$vals[$val] = $val;
							}
						}
					}
					break;
			}
			return $vals;
		}
		return $cfgs;
	}

	function _get_storage_income(&$arr)
	{
		$this->_init_storage_income_tbl($arr);
		if(!isset($arr["warehouses"]))
		{
			$arr["warehouses"] = array($arr["obj_inst"]->id());
		}
		$ol = $this->_get_storage_ol($arr);
		foreach($ol->arr() as $o)
		{
			$t = 0;
			if($o->class_id() == CL_CRM_BILL)
			{
				$t = 1;
			}
			$conn = $o->connections_from(array(
				"type" => $t?"RELTYPE_DELIVERY_NOTE":"RELTYPE_BILL",
			));
			$rels = array();
			foreach($conn as $c)
			{
				$to = $c->to();
				$cnum = $to->prop($t ? "number" : "bill_no");
				$rels[] = html::obj_change_url($c->to(), $cnum ? $cnum : t("(Puudub)"));
			}
			$sum = 0;
			if($t)
			{
				$agreement_prices = $o->meta("agreement_price");
				if(is_array($agreement_price) and $agreement_prices[0]["price"] and strlen($agreement_prices[0]["name"]) > 0)
				{
					$sum = 0;
					foreach($agreement_prices as $agreement_price)
					{
						$sum+= $agreement_price["sum"];
					}
				}
			}
			else
			{
				$conn = $o->connections_from(array(
					"type" => "RELTYPE_ROW",
				));
				foreach($conn as $c)
				{
					$row = $c->to();
					$id = $row->prop("product");
					$sum += $row->prop("price")*$row->prop("amount");
				}
				$sum += $o->prop("customs") + $o->prop("transport");
			}
			$sum = number_format($sum, 2);
			$relations = implode(", ", $rels);
			$num = $o->prop($t?"bill_no":"number");
			$data = array(
				"oid" => $o->id(),
				"number" => html::obj_change_url($o, $num?$num:t("(Puudub)")),
				"type" => $t?t("Arve"):t("Saateleht"),
				"acquirer" => $o->prop(($arr["request"]["group"] === "storage_export" ? "customer" : "impl").".name"),
				"created" => $o->prop($t?"bill_date":"delivery_date"),
				"relations" => $relations,
				"sum" => $sum,
				"status" => $o->prop("approved")?t("Kinnitatud"):t("Kinnitamata"),
			);
			$arr["prop"]["vcl_inst"]->define_data($data);
		}

		$arr["prop"]["vcl_inst"]->sort_by();
	}

	private function _init_storage_income_tbl(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"sortable" => 1,
			"name" => "number",
			"caption" => t("Number"),
			"align" => "center",
		));
		if(empty($arr["request"][$arr["request"]["group"]."_s_type"]))
		{
			$t->define_field(array(
				"name" => "type",
				"caption" => t("T&uuml;&uuml;p"),
				"align" => "center"
			));
		}
		if($arr["request"]["group"] === "storage_export")
		{
			$t->define_field(array(
				"sortable" => 1,
				"name" => "acquirer",
				"caption" => t("Ostja"),
				"align" => "center"
			));
		}
		else
		{
			$t->define_field(array(
				"sortable" => 1,
				"name" => "acquirer",
				"caption" => t("Tarnija"),
				"align" => "center"
			));
		}

		$t->define_field(array(
			"sortable" => 1,
			"name" => "created",
			"caption" => t("Kuup&auml;ev"),
			"align" => "center",
			"type" => "time",
			"format" => "d.m.Y"
		));

		$t->define_field(array(
			"name" => "relations",
			"caption" => t("Seosed"),
			"align" => "center",
			"width" => 100,
		));

		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa"),
			"align" => "center",
			"sortable" => 1
		));

		if(empty($arr["request"][$arr["request"]["group"]."_s_status"]))
		{
			$t->define_field(array(
				"name" => "status",
				"caption" => t("Staatus"),
				"align" => "center",
				"sortable" => 1
			));
		}

		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));

		$t->define_pageselector(array(
			"type" => "text",
			"records_per_page" => 100,
		));
	}

	function save_storage_inc_tbl(&$arr)
	{

	}

	function _get_storage_income_toolbar(&$data)
	{
		$tb =& $data["prop"]["toolbar"];

		$tb->add_menu_button(array(
			"name" => "create_new",
			"tooltip" => t("Uus")
		));

		if(!$data["warehouses"] and $data["obj_inst"]->class_id() == CL_SHOP_WAREHOUSE)
		{
			$whs = array($data["obj_inst"]);
		}
		else
		{
			foreach($data["warehouses"] as $wh)
			{
				if($this->can("view", $wh))
				{
					$whs[$wh] = obj($wh);
				}
			}
		}
		$npt = "create_new";
		foreach($whs as $whid)
		{
			$who = obj($whid);
			$pt = $who->prop("conf.".(($data["prop"]["name"] === "storage_export_toolbar") ? "export_fld" : "reception_fld"));
			if(!$pt)
			{
				continue;
			}
			if(count($whs) > 1)
			{
				$tb->add_sub_menu(array(
					"name" => "wh_".$whid,
					"text" => $who->name(),
					"parent" => "create_new",
				));
				$npt = "wh_".$whid;
			}
			$tb->add_menu_item(array(
				"parent" => $npt,
				"text" => t("Saateleht"),
				"link" => $this->mk_my_orb("new", array(
					"parent" => $pt,
					"return_url" => get_ru()
				), CL_SHOP_DELIVERY_NOTE)
			));

			$tb->add_menu_item(array(
				"parent" => $npt,
				"text" => t("Arve"),
				"link" => $this->mk_my_orb("new", array(
					"parent" => $pt,
					"return_url" => get_ru(),
				), CL_CRM_BILL),
			));
		}

		$tb->add_delete_button();
		$tb->add_save_button();
	}


	function _get_storage_export(&$arr)
	{
		$this->_get_storage_income($arr);
	}

	function save_storage_exp_tbl(&$arr)
	{
		$re = get_instance(CL_SHOP_WAREHOUSE_EXPORT);

		$awa = new aw_array($arr["request"]["confirm"]);
		foreach($awa->get() as $inc => $one)
		{
			if ($one == 1)
			{
				// confirm export
				$re->do_confirm(obj($inc));
			}
		}
	}

	function _get_storage_export_toolbar(&$data)
	{
		$this->_get_storage_income_toolbar($data);
	}

	/** creates a new export object and attach a product to it, then redirect user to count entry

		@attrib name=create_export

		@param id required type=int acl=view
		@param product required type=int acl=view

	**/
	function create_export($arr)
	{
		extract($arr);
		$o = obj($id);
		$tmp = array(
			"obj_inst" => $o
		);
		$this->_init_view($tmp);

		$p = obj($product);

		// create export object
		$e = obj();
		$e->set_parent($this->export_fld);
		$e->set_class_id(CL_SHOP_WAREHOUSE_EXPORT);
		$e->set_name(sprintf(t("Lao v&auml;ljaminek: %s"), $p->name()));
		$e->save();

		$e->connect(array(
			"to" => $p->id(),
			"reltype" => "RELTYPE_PRODUCT",
		));

		// also connect the export to warehouse
		$o->connect(array(
			"to" => $e,
			"reltype" => "RELTYPE_STORAGE_EXPORT",
		));

		return $this->mk_my_orb("change", array(
			"id" => $e->id(),
			"group" => "export",
			"return_url" => $this->mk_my_orb("change", array(
				"id" => $o->id(),
				"group" => "storage_export"
			))
		), CL_SHOP_WAREHOUSE_EXPORT);
	}

	/** creates a new reception object and attach a product to it, then redirect user to count entry

		@attrib name=create_reception

		@param id required type=int acl=view
		@param product required type=int acl=view

	**/
	function create_reception($arr)
	{
		extract($arr);
		$o = obj($id);
		$tmp = array(
			"obj_inst" => $o
		);
		$this->_init_view($tmp);

		$p = obj($product);

		// create export object
		$e = obj();
		$e->set_parent($this->reception_fld);
		$e->set_class_id(CL_SHOP_WAREHOUSE_RECEPTION);
		$e->set_name(sprintf(t("Lao sissetulek: %s"), $p->name()));
		$e->save();

		$e->connect(array(
			"to" => $p->id(),
			"reltype" => "RELTYPE_PRODUCT",
		));

		// also connect the reception to warehouse
		$o->connect(array(
			"to" => $e,
			"reltype" => "RELTYPE_STORAGE_INCOME",
		));

		return $this->mk_my_orb("change", array(
			"id" => $e->id(),
			"group" => "income",
			"return_url" => $this->mk_my_orb("change", array(
				"id" => $o->id(),
				"group" => "storage_income"
			))
		), CL_SHOP_WAREHOUSE_RECEPTION);
	}

	private function _init_order_orderer_cos_tbl($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));

		$t->define_field(array(
			"name" => "price",
			"caption" => t("Hind"),
		));
		$t->define_field(array(
			"name" => "who",
			"caption" => t("Kes"),
		));
		$t->define_field(array(
			"name" => "when",
			"caption" => t("Millal"),
		));
		$t->define_field(array(
			"name" => "view",
			"caption" => t("Vaata"),
		));
	}

	function do_order_orderer_cos_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_order_orderer_cos_tbl($t);

		// get orders by orderer
		if ($arr["request"]["tree_worker"])
		{
			$ol = new object_list(array(
				"class_id" => CL_SHOP_ORDER,
				"orderer_person" => $arr["request"]["tree_worker"]
			));
		}
		else
		if ($arr["request"]["tree_company"])
		{
			// get workers for co
			$co = obj($arr["request"]["tree_company"]);
			$ids = array();
			$con = new connection();
			foreach($con->find(array("from.class_id" => crm_person_obj::CLID, "to" => $co->id())) as $c)
			{
				$ids[] = $c["from"];
			}
			if (!count($ids))
			{
				$ol = new object_list();
			}
			else
			{
				$ol = new object_list(array(
					"class_id" => CL_SHOP_ORDER,
					"orderer_person" => $ids
				));
			}
		}
		else
		if ($arr["request"]["tree_code"])
		{
			// get workers for co
			$categories = new object_list(array(
				"parent" => $this->buyers_fld,
				"class_id" => CL_CRM_SECTOR,
				"kood" => $arr["request"]["tree_code"]."%"
			));
			$ids = array();
			for($cat = $categories->begin(); !$categories->end(); $cat = $categories->next())
			{
				foreach($cat->connections_to(array("from.class_id" => crm_company_obj::CLID)) as $c)
				{
					$co = $c->from();
					$workers = $co->get_workers();
					$ids+= $workers->ids();
//					foreach($co->connections_from(array("type" => "RELTYPE_WORKER")) as $c)
//					{
//						$ids[] = $c->prop("to");
//					}
				}
			}

			if (count($ids) < 1)
			{
				$ol = new object_list();
			}
			else
			{
				$ol = new object_list(array(
					"class_id" => CL_SHOP_ORDER,
					"orderer_person" => $ids
				));
			}
		}
		else
		{
			$ol = new object_list();
		}

		$oinst = get_instance(CL_SHOP_ORDER);
		for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
		{
			$t->define_data(array(
				"name" => $o->name(),
				"price" => $o->prop("sum"),
				"who" => $oinst->get_orderer($o),
				"when" => $o->modified(),
				"view" => html::href(array(
					"url" => $this->mk_my_orb("change", array("id" => $o->id()), $o->class_id()),
					"caption" => t("Vaata")
				))
			));
		}
	}

	function do_order_orderer_cos_tree(&$arr)
	{
		// get categories
		$categories = new object_list(array(
			"parent" => $this->buyers_fld,
			"class_id" => CL_CRM_SECTOR,
		));

		$all_cos = new object_list(array(
			"parent" => $this->buyers_fld,
			"class_id" => crm_company_obj::CLID
		));
		$this->all_cos_ids = $all_cos->names();

		$tv = $this->get_vcl_tree_from_cat_list($categories);

		// now, add all remaining cos as top level items
		foreach($this->all_cos_ids as $co_id => $con)
		{
			$tv->add_item(0, array(
				"name" => $con,
				"id" => "nocode_co".$co_id,
				"url" => aw_url_change_var("tree_code", NULL, aw_url_change_var("tree_company", $co_id, aw_url_change_var("tree_worker", NULL)))
			));

			$co = obj($co_id);
			// now all people for that company

			foreach($co->get_workers()->arr() as $c)
//			foreach($co->connections_from(array("type" => "RELTYPE_WORKER")) as $c)
			{
				$tv->add_item("nocode_co".$co->id(), array(
					"name" => $c->name(),
					"id" => "nocode_wk".$c->id(),
					"url" => aw_url_change_var("tree_code", NULL, aw_url_change_var("tree_company", NULL, aw_url_change_var("tree_worker", $c->id())))
				));
			}
		}


		$arr["prop"]["value"] = $tv->finalize_tree();
	}

	function get_vcl_tree_from_cat_list($categories)
	{
		// now, gotst to make tree out of them.
		// algorithm is: sort by length, add the shortest to first level, then start adding by legth
		// prop: kood
		$ta = array();
		$ids = array();
		for($o = $categories->begin(); !$categories->end(); $o = $categories->next())
		{
			$ta[$o->prop("kood")] = $o;
			$ids[] = $o->id();
		}
		uksort($ta, array($this, "__ta_sb_cb"));

		// get all companies with these categories
		$cos = new object_list(array(
			"class_id" => crm_company_obj::CLID,
			"pohitegevus" => $ids
		));
		$this->cos_by_code = array();
		for($o = $cos->begin(); !$cos->end(); $o = $cos->next())
		{
			// get all type rels
			foreach($o->connections_from(array("to.class_id" => CL_CRM_SECTOR)) as $c)
			{
				$s = $c->to();
				$this->cos_by_code[$s->prop("kood")][] = $o;
			}
		}

		// now, start adding things to the tree.
		$tv = get_instance("vcl/treeview");
		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"tree_id" => "shwhordcos",
			"persist_state" => true
		));

		$this->_req_filter_and_add($tv, $ta, "", 0);

		return $tv;
	}

	function _req_filter_and_add($tv, $ta, $filter_code, $parent)
	{
		$nta = array();

		$fclen = strlen($filter_code);
		$minl = 1000;
		$cpta = $ta;

		foreach($cpta as $code => $code_o)
		{
			if (substr($code, 0, $fclen) == $filter_code and $code != $filter_code)
			{
				$nta[$code] = $code_o;
				if (strlen($code) < $minl)
				{
					$minl = strlen($code);
				}
			}
		}

		if (count($nta) < 1)
		{
			// we reached the end of the tree, add cos now
			$this->_do_add_cos_by_code($tv, $filter_code);
			return;
		}

		uksort($nta, array($this, "__ta_sb_cb"));

		reset($nta);
		list($code, $code_o) = each($nta);
		while (strlen($code) == $minl)
		{
			$tv->add_item($parent, array(
				"name" => $code_o->name(),
				"id" => $code,
				"url" => aw_url_change_var("tree_code", $code, aw_url_change_var("tree_company", NULL, aw_url_change_var("tree_worker", NULL)))
			));

			// now find the children for this.
			// how to do this? simple, filter the list by the start of this code and sort and insert smallest length,
			// lather, rinse, repeat
			$this->_req_filter_and_add($tv, $nta, $code, $code);

			list($code, $code_o) = each($nta);
		}
	}

	function _do_add_cos_by_code($tv, $code)
	{
		if (!is_array($this->cos_by_code[$code]))
		{
			return;
		}
		foreach($this->cos_by_code[$code] as $co)
		{
			$tv->add_item($code, array(
				"name" => $co->name(),
				"id" => $code."co".$co->id(),
				"url" => aw_url_change_var("tree_code", NULL, aw_url_change_var("tree_company", $co->id(), aw_url_change_var("tree_worker", NULL)))
			));
			unset($this->all_cos_ids[$co->id()]);

			// now all people for that company
			foreach($co->get_workers()->arr() as $c)
//			foreach($co->connections_from(array("type" => "RELTYPE_WORKER")) as $c)
			{
				$tv->add_item($code."co".$co->id(), array(
					"name" => $c->name(),
					"id" => $code."wk".$c->id(),
					"url" => aw_url_change_var("tree_code", NULL, aw_url_change_var("tree_company", NULL, aw_url_change_var("tree_worker", $c->id())))
				));
			}
		}
	}

	function __ta_sb_cb($a, $b)
	{
		return ($a == $b ? 0 : ((strlen($a) < strlen($b)) ? -1 : 1));
	}

	function callback_pre_edit($arr)
	{
		if (!$arr["obj_inst"]->prop("order_current_org") and
			is_oid($arr["obj_inst"]->prop("order_current_person")) and
			$this->can("view", $arr["obj_inst"]->prop("order_current_person"))
		)
		{
			// get the org from the person
			$pers = obj($arr["obj_inst"]->prop("order_current_person"));
			$conn = reset($pers->connections_from(array(
				"type" => "RELTYPE_WORK"
			)));
			if ($conn)
			{
				$arr["obj_inst"]->set_prop("order_current_org", $conn->prop("to"));
				$tmp = $arr["obj_inst"]->meta("popup_search[order_current_org]");
				$tmp[$conn->prop("to")] = $conn->prop("to");
				$arr["obj_inst"]->set_meta("popup_search[order_current_org]", $tmp);
				$arr["obj_inst"]->save();
			}
		}
	}

	function callback_pre_save($arr)
	{
		if ($arr["request"]["group"] === "order_current")
		{
			$arr["obj_inst"]->set_meta("order_cur_ud", $arr["request"]["user_data"]);
		}

		if (!empty($this->upd_ud))
		{
			$this->do_update_user_data(array(
				"oid" => $arr["obj_inst"]->id()
			));
		}
	}

	function callback_get_order_current_form($arr)
	{
		$ret = array();

		$o = $arr["obj_inst"];
		$cud = $o->meta("order_cur_ud");

		// get order center
		if (!$o->prop("order_center"))
		{
			return $ret;
		}
		$oc = obj($o->prop("order_center"));
		$oc_i = $oc->instance();

		$props = $oc_i->get_properties_from_data_form($oc, $cud);

		if (!empty($arr["no_data"]))
		{
			return $props;
		}

		if (($pp = $oc->prop("data_form_person")) and is_oid($o->prop("order_current_person")))
		{
			$po = obj($o->prop("order_current_person"));
			$props[$pp]["value"] = $po->name();
			$props[$pp]["type"] = "hidden";
			$props[$pp."_show"] = $props[$pp];
			$props[$pp."_show"]["type"] = "text";
		}

		if (($pp = $oc->prop("data_form_company")) and $o->prop("order_current_org"))
		{
			$po = obj($o->prop("order_current_org"));
			$props[$pp]["value"] = $po->name();
			$props[$pp]["type"] = "hidden";
			$props[$pp."_show"] = $props[$pp];
			$props[$pp."_show"]["type"] = "text";
		}

		return $props;
	}

	function do_search_res_tbl($arr)
	{
		if (!$arr["obj_inst"]->prop("conf"))
		{
			return;
		}
		$conf = obj($arr["obj_inst"]->prop("conf"));
		if (!$conf->prop("search_form"))
		{
			return;
		}
		$sf = obj($conf->prop("search_form"));
		$sf_i = $sf->instance();

		$sf_i->get_search_result_table(array(
			"ob" => $sf,
			"t" => $arr["prop"]["vcl_inst"],
			"request" => $arr["request"]
		));

		// add select column
		$arr["prop"]["vcl_inst"]->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	/** finishes the order

		@attrib name=gen_order

		@param id required type=int acl=view
		@param user_data optional
		@param html optional
	**/
	function gen_order($arr)
	{
		$ordid = $this->make_cur_order_id($arr);

		return $this->mk_my_orb("gen_pdf", array(
			"id" => $ordid,
			"html" => $arr["html"],
		), CL_SHOP_ORDER);
	}

	function make_cur_order_id($arr)
	{
		$o = obj($arr["id"]);
		$oc = $o->prop("order_center");
		error::raise_if(!$oc, array(
			"id" => ERR_NO_OC,
			"msg" => t("shop_warehouse::gen_order(): no order center object selected!")
		));

		$soc = get_instance(CL_SHOP_ORDER_CART);
		if (!aw_global_get("wh_order_cur_order_id"))
		{
			$ordid = $soc->do_create_order_from_cart($oc, $arr["id"], array(
				"pers_id" => $o->prop("order_current_person"),
				"com_id" => $o->prop("order_current_org"),
				"user_data" => $o->meta("order_cur_ud"),
				"discount" => $o->meta("order_cur_discount"),
				"prod_paging" => $o->meta("order_cur_pages"),
				"no_send_mail" => 1
			));
			aw_session_set("wh_order_cur_order_id", $ordid);
		}
		return aw_global_get("wh_order_cur_order_id");
	}

	function callback_get_search_form($arr)
	{
		if (!$arr["obj_inst"]->prop("conf"))
		{
			return;
		}
		$conf = obj($arr["obj_inst"]->prop("conf"));
		if (!$conf->prop("search_form"))
		{
			return;
		}
		$sf = obj($conf->prop("search_form"));
		$sf_i = $sf->instance();
		return $sf_i->get_callback_properties($sf);
	}

	function do_search_tb($arr)
	{
		$tb =& $arr["prop"]["toolbar"];

		$tb->add_button(array(
			"name" => "add_to_order",
			"img" => "import.gif",
			"tooltip" => t("Lisa pakkumisse"),
			"action" => "add_to_cart"
		));

		$tb->add_button(array(
			"name" => "go_to_order",
			"img" => "save.gif",
			"tooltip" => t("Moodusta pakkumine"),
			"url" => $this->mk_my_orb("change", array("id" => $arr["obj_inst"]->id(), "group" => "order_current"))
		));
	}

	/** message handler for the MSG_POPUP_SEARCH_CHANGE message so we can update
		the person/company listboxes when one changes
	**/
	function on_popup_search_change($arr)
	{
		if ($arr["prop"] === "order_current_org")
		{
			$this->do_update_persons_from_org($arr);
		}
		else
		{
			$this->do_update_orgs_from_person($arr);
		}

		$this->do_update_user_data(array(
			"oid" => $arr["oid"]
		));
	}

	function do_update_user_data($arr)
	{
		// also update the data form data, based on the property maps from the order center
		// first org
		if(!is_oid($arr["oid"]) || !$this->can("view", $arr["oid"]))
		{
			return;
		}
		$o = obj($arr["oid"]);

		$oc = get_instance(CL_SHOP_ORDER_CENTER);
		$personmap = $oc->get_property_map($o->prop("order_center"), "person");
		$orgmap = $oc->get_property_map($o->prop("order_center"), "org");

		$cud = $o->meta("order_cur_ud");

		// get selected person object
		if (($ps = $o->prop("order_current_person")))
		{
			$person = obj($ps);
			$ps_props = $person->get_property_list();

			foreach($personmap as $data_f_prop => $person_o_prop)
			{
				if ($ps_props[$person_o_prop]["type"] === "relmanager")
				{
					$tmp = $person->prop($person_o_prop);
					if (is_oid($tmp))
					{
						$tmp = obj($tmp);
						$cud[$data_f_prop] = $tmp->name();
					}
				}
				else
				{
					$cud[$data_f_prop] = $person->prop($person_o_prop);
				}
			}
		}

		if (($org = $o->prop("order_current_org")))
		{
			$org = obj($org);
			$org_props = $org->get_property_list();

			foreach($orgmap as $data_f_prop => $org_o_prop)
			{
				if ($org_props[$org_o_prop]["type"] === "relmanager")
				{
					$tmp = $org->prop($org_o_prop);
					if (is_oid($tmp))
					{
						$tmp = obj($tmp);
						$cud[$data_f_prop] = $tmp->name();
					}
				}
				else
				{
					$cud[$data_f_prop] = $org->prop($org_o_prop);
				}
			}
		}

		$o->set_meta("order_cur_ud", $cud);
		$o->save();
	}

	function do_update_persons_from_org($arr)
	{
		$o = obj($arr["oid"]);
		$cur_co = $o->prop($arr["prop"]);
		if (!is_oid($cur_co))
		{
			return;
		}

		$workers = array();

		$co = new crm_company();
		$co->get_all_workers_for_company(obj($cur_co), $workers, true);

		$pop = get_instance("vcl/popup_search");
		$pop->set_options(array(
			"obj" => $o,
			"prop" => "order_current_person",
			"opts" => $workers
		));
	}

	function do_update_orgs_from_person($arr)
	{
		$o = obj($arr["oid"]);
		$cur_person = $o->prop($arr["prop"]);

		if (!is_oid($cur_person))
		{
			return;
		}

		$p = obj($cur_person);

		$pop = get_instance("vcl/popup_search");
		$pop->set_options(array(
			"obj" => $o,
			"prop" => "order_current_org",
			"opts" => $p->get_all_org_ids(),
		));
	}

	///////////////////////////////////////////////
	// warehouse public interface functions      //
	///////////////////////////////////////////////

	/** returns an object_tree of warehouse folders

		@attrib param=name api=1

		@param id required

	**/
	function get_packet_folder_list($arr)
	{
		$o = obj($arr["id"]);
		$config = obj($o->prop("conf"));
		$ot = new object_tree(array(
			"parent" => $config->prop("pkt_fld"),
			"class_id" => CL_MENU,
			"status" => array(STAT_ACTIVE, STAT_NOTACTIVE),
			"sort_by" => "objects.jrk"
		));
		return array(obj($config->prop("pkt_fld")), $ot);
	}

	/** Returns a list of packets/products in the warehouse $id, optionally under folder $parent

		@attrib param=name api=1

		@param id required type=int
			Warehouse object id
		@param parent optional type=var
			Parent folder id or array of parent folders
		@param only_active optional type=bool
			To get only active packets/products
		@param no_subitems optional type=bool
			If true, sub-products are not requested

		@returns Array of packet/product objects

	**/
	function get_packet_list($arr)
	{
		$wh = obj($arr["id"]);
		$conf = obj($wh->prop("conf"));

		$status = array(STAT_ACTIVE, STAT_NOTACTIVE);
		if (!empty($arr["only_active"]))
		{
			$status = STAT_ACTIVE;
		}

		$ret = array();

		if($conf->prop("no_packets") != 1 and !(isset($arr['parent']) and  is_array($arr['parent'])))
		{
			$po = obj((!empty($arr["parent"]) ? $arr["parent"] : $conf->prop("pkt_fld")));
			if ($po->is_brother())
			{
				$po = $po->get_original();
			}

			$ol = new object_list(array(
				"parent" => $po->id(),
				"class_id" => CL_SHOP_PACKET,
				"status" => $status
			));
			$ret = $ol->arr();
		}

		if (is_array($arr['parent']))
		{
			$parent = $arr['parent'];
		}
		else
		{
			$po = obj((!empty($arr["parent"]) ? $arr["parent"] : $conf->prop("prod_fld")));
			if ($po->is_brother())
			{
				$po = $po->get_original();
			}
			$parent = $po->id();
		}

		$ol = new object_list(array(
			"parent" => $parent,
			"class_id" => CL_SHOP_PRODUCT,
			"status" => $status
		));
		$ret = array_merge($ret, $ol->arr());
		if(!$conf->prop("sell_prods") and empty($arr["no_subitems"]))
		{
			// now, let the classes add sub-items to the list
			$tmp = array();
			foreach($ret as $o)
			{
				$inst = $o->instance();
				foreach($inst->get_contained_products($o) as $co)
				{
					$tmp[] = $co;
				}
			}
			$ret = $tmp;
		}
		return $ret;
	}

	/** Gives the folder oid where the orders are saved

		@attrib name=get_order_folder params=pos api=1

		@param id required type=object acl=view
			Warehouse object
	**/
	function get_order_folder($w)
	{
		error::raise_if(!$w->prop("conf"), array(
			"id" => ERR_FATAL,
			"msg" => sprintf(t("shop_warehouse::get_order_folder(%s): the warehouse has not configuration object set!"), $w->id())
		));

		$conf = obj($w->prop("conf"));
		$tmp = $conf->prop("order_fld");

		error::raise_if(empty($tmp), array(
			"id" => ERR_FATAL,
			"msg" => sprintf(t("shop_warehouse::get_order_folder(%s): the warehouse configuration has no order folder set!"), $w->id())
		));

		return $tmp;
	}

	/** Returns the products folder id

		@attrib name=get_products_folder params=pos api=1

		@param id required type=object acl=view
			Warehouse object
	**/
	function get_products_folder($w)
	{
		error::raise_if(!$w->prop("conf"), array(
			"id" => ERR_FATAL,
			"msg" => sprintf(t("shop_warehouse::get_products_folder(%s): the warehouse has not configuration object set!"), $w)
		));

		$conf = obj($w->prop("conf"));
		$tmp = $conf->prop("prod_fld");

		error::raise_if(empty($tmp), array(
			"id" => ERR_FATAL,
			"msg" => sprintf(t("shop_warehouse::get_products_folder(%s): the warehouse configuration has no products folder set!"), $w)
		));

		return $tmp;
	}
	/** adds the selected items to the basket

		@attrib name=add_to_cart api=1

		@param id required type=int acl=view
		@param sel optional
		@param group optional
	**/
	function add_to_cart($arr)
	{
		$adc = array();
		foreach(safe_array($arr["sel"]) as $_id)
		{
			$adc[$_id] = 1;
		}
		$warehouse = obj($arr["id"]);
		$soc = get_instance(CL_SHOP_ORDER_CART);
		$soc->submit_add_cart(array(
			"oc" => $warehouse->prop("order_center"),
			"add_to_cart" => $adc
		));

		$this->do_save_prod_ord($arr);

		return $this->mk_my_orb("change", array(
			"id" => $arr["id"],
			"tree_filter" => $arr["tree_filter"],
			"group" => $arr["group"]
		));
	}

	/** cuts the selected items
		@attrib name=cut_products params=name all_args=1
	**/
	function cut_products($arr)
	{
		$_SESSION["shop_warehouse"]["copy_products"] = null;
		$_SESSION["shop_warehouse"]["cut_products"] = $arr["sel"];
		return $arr["post_ru"];
	}

	/** copys the selected items
		@attrib name=copy_products params=name all_args=1
	**/
	function copy_products($arr)
	{
		$_SESSION["shop_warehouse"]["cut_products"] = null;
		$_SESSION["shop_warehouse"]["copy_products"] = $arr["sel"];
		return $arr["post_ru"];
	}

	/** pastes items to menu
		@attrib name=paste_products params=name all_args=1
	**/
	function paste_products($arr)
	{
		$cats = $this->get_categories_from_search($arr);

		if(sizeof($cats))
		{
			foreach(safe_array(ifset($_SESSION, "shop_warehouse", "cut_products")) as $id)
			{
				$o = obj($id);
				$o->remove_categories();
				foreach($cats as $cat)
				{
					$o->add_category($cat);
				}
			}
			foreach(safe_array(ifset($_SESSION, "shop_warehouse", "copy_products")) as $id)
			{
				$o = obj($id);
				foreach($cats as $cat)
				{
					$o->add_category($cat);
				}
			}
		}

		if(is_oid($arr["parent"]) and $this->can("add" , $arr["parent"]))
		{
			foreach(safe_array(ifset($_SESSION, "shop_warehouse", "cut_products")) as $id)
			{
				$o = obj($id);
				if($o->is_a(CL_SHOP_PRODUCT_PACKAGING))
				{
					$conns = $o->connections_to(array(
						"from.class_id" => CL_SHOP_PRODUCT,
						"type" => "RELTYPE_PACKAGING",
					));
					foreach($conns as $conn)
					{
						$conn->delete();
					}
					obj($arr["parent"])->connect(array(
						"to" => $o->id(),
						"type" => "RELTYPE_PACKAGING",
					));
				}
				else
				{
					$o->set_parent($arr["parent"]);
					$o->save();
				}
			}
			foreach(safe_array(ifset($_SESSION, "shop_warehouse", "copy_products")) as $id)
			{
				$o = obj($id);
				$new_o = new object();
				$new_o->set_class_id($o->class_id());
				foreach($o->get_property_list() as $prop => $val)
				{
					$new_o->set_prop($prop , $o->prop($prop));
				}
				$new_o->set_name($o->name());
				$new_o->set_parent($arr["parent"]);
				$new_o->save();

				if($o->is_a(CL_SHOP_PRODUCT_PACKAGING))
				{
					obj($arr["parent"])->connect(array(
						"to" => $new_o->id(),
						"type" => "RELTYPE_PACKAGING",
					));
				}
			}
		}
		$_SESSION["shop_warehouse"]["copy_products"] = null;
		$_SESSION["shop_warehouse"]["cut_products"] = null;
		return $arr["return_url"];
	}

	/** checks if the company $id is a manager company for  warehouse $wh

	**/
	function is_manager_co($wh, $id)
	{
		if (!$wh->prop("conf"))
		{
			return false;
		}
		$conf = obj($wh->prop("conf"));
		$awa = new aw_array($conf->prop("manager_cos"));
		$mc = $awa->get();

		$mc = $this->make_keys($mc);
		if ($mc[$id])
		{
			return true;
		}
		return false;
	}

	/** sends the current order to the orderer's e-mail

		@attrib name=send_cur_order api=1

	**/
	function sent_cur_order($arr)
	{
		$ordid = $this->make_cur_order_id($arr);

		$ordo = obj($ordid);

		// get e-mail address from order
		$o = obj($arr["id"]);
		$oc = obj($o->prop("order_center"));
		$mail_to_el = $oc->prop("mail_to_el");
		$ud = $o->meta("order_cur_ud");
		$to = str_replace("&gt;", "", str_replace("&lt;", "", $ud[$mail_to_el]));
		if ($to === "")
		{
			return;
		}

		$so = get_instance(CL_SHOP_ORDER);
		$html = $so->gen_pdf(array(
			"id" => $ordid,
			"html" => 1,
			"return" => 1
		));

		$us = get_instance(CL_USER);
		$cur_person = obj($us->get_current_person());

		$froma = "automatweb@automatweb.com";
		if (is_oid($cur_person->prop("email")))
		{
			$tmp = obj($cur_person->prop("email"));
			$froma = $tmp->prop("mail");
		}

		$fromn = $cur_person->prop("name");

		$awm = get_instance("protocols/mail/aw_mail");
		$awm->create_message(array(
			"froma" => $froma,
			"fromn" => $fromn,
			"subject" => sprintf(t("Tellimus laost %s"), $o->name()),
			"to" => $to,
			"body" => strip_tags(str_replace("<br>", "\n",$html)),
		));
		$awm->htmlbodyattach(array(
			"data" => $html
		));
		$awm->gen_mail();

		return $this->mk_my_orb("change", array("id" => $arr["id"], "group" => "order_current"));
	}

	/** clears the current order

		@attrib name=clear_order api=1

	**/
	function clear_order($arr)
	{
		$o = obj($arr["id"]);
		$oc = obj($o->prop("order_center"));
		$soc = get_instance(CL_SHOP_ORDER_CART);
		$soc->clear_cart($oc);

		$o->set_prop("order_current_person", "");
		$o->set_prop("order_current_org", "");
		$o->set_meta("order_cur_ud", "");
		$o->set_meta("order_cur_discount", "");
		$o->set_meta("order_cur_pages", "");
		$o->save();

		aw_session_del("wh_order_cur_order_id");

		return $this->mk_my_orb("change", array("id" => $arr["id"], "group" => "search_search"));
	}

	function do_save_prod_ord($arr)
	{
		foreach(safe_array($arr["old_ord"]) as $oid => $o_ord)
		{
			if ($arr["set_ord"][$oid] != $o_ord)
			{
				$o = obj($oid);
				$o->set_ord($arr["set_ord"][$oid]);
				$o->save();
			}
		}
	}

	function callback_mod_tab($arr)
	{
		if(($arr["id"] === "status" || $arr["id"] === "storage") and $arr["obj_inst"]->prop("conf.no_count") == 1)
		{
			return false;
		}
		if ("sales" === $arr["id"] || "sell_orders" === $arr["id"])
		{
			$arr["link"] = aw_url_change_var("filt_time", "today", $arr["link"]);
		}
		return true;
	}

	function callback_mod_reforb($arr, $request)
	{
		$args_to_transfer = array("ptf", "pgtf");
		foreach($args_to_transfer as $arg_to_transfer)
		{
			if(isset($request[$arg_to_transfer]))
			{
				$arr[$arg_to_transfer] = $request[$arg_to_transfer];
			}
		}

		$arr["post_ru"] = post_ru();

		if(isset($request["group"]))
		{
			switch($request["group"])
			{
				case "status_orders":
					$arr["add_rows_order"] = 0;
					if (isset($request["filt_case"]))
					{
						$arr["filt_case"] = $request["filt_case"];
					}
					if (isset($request["filt_res"]))
					{
						$arr["filt_res"] = $request["filt_res"];
					}
					break;

				case "shop_orders":
					if(isset($request["shop_orders_s_status"]))
					{
						$arr["shop_orders_s_status"] = $request["shop_orders_s_status"];
					}
					break;
			}
		}
	}

	function callback_mod_retval($arr)
	{
		if(isset($arr["request"]["ptf"]))
		{
			$arr["args"]["ptf"] = $arr["request"]["ptf"];
		}
		if(isset($arr["request"]["timespan"]))
		{
			$arr["args"]["timespan"] = $arr["request"]["timespan"];
		}
		if(isset($arr["request"]["pgtf"]))
		{
			$arr["args"]["pgtf"] = $arr["request"]["pgtf"];
		}
		$group = $this->get_search_group($arr);
		$g = $arr["request"]["group"];
		if(!$arr["request"][$group."_s_cat"])
		{
			$arr["args"]["pgtf"] = null;
		}
		else
		{
			$arr["args"]["pgtf"] = $arr["request"][$group."_s_cat"];
		}
		$vars = obj($arr["request"]["id"])->get_property_list();
		foreach($vars as $var => $c)
		{
			if($this->is_search_param($var) and array_key_exists($var, $arr["request"]))
			{
				if(strpos($var, "from") || strpos($var, "to") || strpos($var, "start") || strpos($var, "end") and is_array($arr["request"][$var]))
				{
					foreach($arr["request"][$var] as $vr => $vl)
					{
						if(!$vl)
						{
							$arr["request"][$var][$vr] = "-";
						}
					}
				}
				$arr["args"][$var] = $arr["request"][$var];
			}
		}
		$status_orders_vars = array("status_orders_opt1", "filt_time", "filt_case", "filt_res");
		foreach($status_orders_vars as $var)
		{
			if($arr["request"][$var])
			{
 				$arr["args"][$var] = $arr["request"][$var];
			}
		}
		if($g === "shop_orders")
		{
			$vars = array("oname", "uname", "pname", "prod", "oid", "status");
			foreach($vars as $var)
			{
				$v = "shop_orders_s_".$var;
				$arr["args"][$v] = $arr["request"][$v];
			}
			foreach(array("to", "from") as $var)
			{
				$v = "shop_orders_s_".$var;
				foreach($arr["request"][$v] as $vr => $vl)
				{
					if(!$vl)
					{
						 $arr["request"][$v][$vr] = "-";
					}
				}
				$arr["args"][$v] = $arr["request"][$v] ;
			}
		}
		if(in_array($g, array("purchase_notes", "sales_notes", "purchase_bills", "sales_bills")))
		{
			foreach(array("acquiredby", "number", "status", "article", "articlecode", "art_cat") as $var)
			{
				$arr["args"][$g."_s_".$var] = $arr["request"][$g."_s_".$var];
			}
			foreach(array("to", "from") as $var)
			{
				foreach($arr["request"][$g."_s_".$var] as $vr => $vl)
				{
					if(!$vl)
					{
						 $arr["request"][$g."_s_".$var][$vr] = "-";
					}
				}
				$arr["args"][$g."_s_".$var] = $arr["request"][$g."_s_".$var] ;
			}
		}
	}

	public function callback_generate_scripts($arr)
	{
		$js = "";
		if(!empty($arr['request']['group']))
		{
			switch($this->use_group)
			{
				case "packets":

					$vars = array('packets_s_active' , 'packets_s_name', 'packets_s_code', 'packets_s_barcode' , 'packets_s_cat', 'packets_s_count' , 'packets_s_price_from' , 'packets_s_pricelist',
						'packets_s_created_from__day' , 'packets_s_created_from__month','packets_s_created_from__year',
						'packets_s_created_to__day' , 'packets_s_created_to__month','packets_s_created_to__year'
					);
					$props_to_update = array();
					foreach($vars as $var)
					{
						$props_to_update[] = $var.": $('[id=".$var."]').val()";
					}

					$js.= "
						function search_packets()
						{
							reload_layout(['packets_list_lay'] , {".join($props_to_update , ",")."});
						}
					";

					$js.= "
						function set_sel_prop(property , value)
						{
							result = $('input[name^=sel]');
							$.please_wait_window.show();
							$.get('/automatweb/orb.aw?class=shop_warehouse&id=".$arr["obj_inst"]->id()."&action=ajax_set_property&' + property + '=' + value + '&' + result.serialize(), {}, function(){
								$.please_wait_window.hide();
								window.location.reload();
							});
						}
					";

					$js .= $this->__callback_generate_scripts_for_product_management_category_tree($arr);

					break;
				case "sales":
				case "sell_orders":
					$js.= "
						function set_sel_prop(property , value)
						{
							result = $('input[name^=sel]');
							$.get('/automatweb/orb.aw?class=shop_order_center&action=ajax_set_product_show_property&' + property + '=' + value + '&' + result.serialize(), {
								}, function (html) {
									reload_property('sell_orders');
								}
							);
						}
					";
				case "product_management":
				case "articles":

					$vars = array('prod_s_name', 'prod_s_code', 'prod_s_barcode');
					$ajax_vars = array();
					foreach ($vars as $var)
					{
						$ajax_vars[] = $var.": document.getElementsByName('".$var."')[0].value\n";
					}
					$js.=  "
						function update_products_table(){

							var result=[];
							result = $('input[name^=prod_s]');

							button=document.getElementsByName('prod_s_sbt')[0];
							button.disabled = true;
							$.post('/automatweb/orb.aw?class=shop_warehouse&action=ajax_update_products_table&id=".$arr['obj_inst']->id()."&'+result.serialize(),{
								id: ".$arr["obj_inst"]->id()."
								, ".join(", " , $ajax_vars)."},function(html){
									x=document.getElementsByName('products_list');
									x[0].innerHTML = html;
									button.disabled = false;
							});
						}
					";
					$types = $arr["obj_inst"]->get_product_category_types();

					$js.= "
						function add_product()
						{
							var cat = get_property_data['cat'];
							var my_string = prompt('".t("Sisesta toote nimi")."');
							$.get('/automatweb/orb.aw', {'class': 'shop_warehouse', 'action': 'create_new_product',
								'id': '".$arr["obj_inst"]->id()."' , 'name': my_string,";
								foreach($types->names() as $id => $cat)
								{
									$js.= " 'cat_".$id."': get_property_data['cat_".$id."'],
									";
								}
									$js.= " 'cat': cat}, function (html) {
									reload_property('product_management_list');
								}
							);
						}
						function add_packet()
						{
							var cat = get_property_data['cat'];
							var my_string = prompt('".t("Sisesta paketi nimi")."');
							$.get('/automatweb/orb.aw', {'class': 'shop_warehouse', 'action': 'create_new_packet',
								'id': '".$arr["obj_inst"]->id()."' , 'name': my_string,";
								foreach($types->names() as $id => $cat)
								{
									$js.= " 'cat_".$id."': get_property_data['cat_".$id."'],
									";
								}
									$js.= " 'cat': cat}, function (html) {
									reload_property('packets_list');
								}
							);
						}
					";
					$js.= "
						function copy_products()
						{
							result = $('input[name^=sel]');
							$.get('/automatweb/orb.aw?class=shop_warehouse&action=copy_products&id=".$arr["obj_inst"]->id()."&' + result.serialize(), {
								}, function (html) {
									reload_property('product_management_toolbar');
									reload_property('product_management_list');
								}
							);
						}
					";
					$js.= "
						function cut_products()
						{
							result = $('input[name^=sel]');
							$.get('/automatweb/orb.aw?class=shop_warehouse&action=cut_products&id=".$arr["obj_inst"]->id()."&' + result.serialize(), {
								}, function (html) {
									reload_property('product_management_toolbar');
									reload_property('product_management_list');
								}
							);
						}
					";
					$js.= "
						function paste_products()
						{
							var cat = get_property_data['cat'];
							$.get('/automatweb/orb.aw', {'class': 'shop_warehouse', 'action': 'paste_products',
								'id': '".$arr["obj_inst"]->id()."' ,";
								foreach($types->names() as $id => $cat)
								{
									$js.= " 'cat_".$id."': get_property_data['cat_".$id."'],
									";
								}
									$js.= " 'cat': cat}, function (html) {
									reload_property('product_management_toolbar');
									reload_property('product_management_list');
								}
							);

						}

				function add_cat()
				{
					var cat = get_property_data['cat'];
					var my_string = prompt('".t("Sisesta kategooria nimi")."');
					$.get('/automatweb/orb.aw', {'class': 'shop_warehouse', 'action': 'create_new_category',
						'id': '".$arr["obj_inst"]->id()."', 'name': my_string, 'cat': cat}, function (html) {
							reload_property('category_list');
							$('#product_management_tree').jstree('refresh');
						}
					);
				}

				function add_cat_type()
				{
					var my_string = prompt('".t("Sisesta kategooria liigi nimi")."');
					$.get('/automatweb/orb.aw', {'class': 'shop_warehouse', 'action': 'create_new_category_type',
						'id': '".$arr["obj_inst"]->id()."', 'name': my_string}, function (html) {
							reload_layout(['product_managementtree_lay2']);
							$('#product_management_category_tree').jstree('refresh');
						}
					);
				}
				function add_type(type)
				{
					result = $('input[name^=sel]');
					$.get('/automatweb/orb.aw?class=shop_warehouse&action=add_type_to_categories&id=".$arr["obj_inst"]->id()."&type=' + type + '& ' + result.serialize(), {
							}, function (html) {
								reload_property('category_list');
							}
						);
				}
				function rem_type_from_cat(cat , type)
				{
					$.get('/automatweb/orb.aw', {'class': 'shop_warehouse', 'action': 'rem_type_from_category',
						'id': '".$arr["obj_inst"]->id()."',  'cat': cat,  'type': type}, function (html) {
							reload_property('category_list');
						}
					);
				}
				function save_categories()
				{
					result = $('input[name^=ord]');
					$.get('/automatweb/orb.aw?class=shop_warehouse&action=save_categories&id=".$arr["obj_inst"]->id()."& ' + result.serialize(), {
							}, function (html) {
								reload_property('category_list');
							}
						);
				}


				";
				
					load_javascript("jquery/plugins/jsTree/jquery.jstree.js");

					$ajax_url = $this->mk_my_orb("get_product_management_tree_nodes", array("id" => $arr["obj_inst"]->id()));

					$js .= <<<SCRIPT
$('#product_management_tree').jstree({
	'json_data' : {
		'ajax': {
			'type': 'GET',
			'url': '{$ajax_url}',
			'async': true,
			'data': function(n) {
				return { 'node': n.attr ? n.attr('id') : -1 }; 
			}
		}
	},
	'themes': { 'theme': 'default', 'url': '/automatweb/js/jquery/plugins/jsTree/themes/default/style.css' },
	'checkbox': { 'override_ui': true },
	'plugins' : ['json_data','themes','ui']
})
.bind("select_node.jstree", function (event, data) {
	reload_layout(["product_managementright"], {"cat": data.rslt.obj.attr("id")});
});
SCRIPT;

					$js .= $this->__callback_generate_scripts_for_product_management_category_tree($arr);

					break;
			}

		}
		return $js;
	}

	protected function __callback_generate_scripts_for_product_management_category_tree($arr)
	{
		load_javascript("jquery/plugins/jsTree/jquery.jstree.js");
		$ajax_url = $this->mk_my_orb("get_product_management_category_tree_nodes", array("id" => $arr["obj_inst"]->id()));

		return <<<SCRIPT
$('#product_management_category_tree').jstree({
	'json_data' : {
		'ajax': {
			'type': 'GET',
			'url': '{$ajax_url}',
			'async': true,
			'data': function(n) {
				return { 'node': n.attr ? n.attr('id') : -1 }; 
			}
		}
	},
	'themes': { 'theme': 'default', 'url': '/automatweb/js/jquery/plugins/jsTree/themes/default/style.css' },
	'checkbox': { 'override_ui': true },
	'plugins' : ['json_data','themes','ui']
})
.bind("select_node.jstree", function (event, data) {
	reload_layout(["product_managementright", "packets_right"], {"cat": data.rslt.obj.attr("id")});
});
SCRIPT;
	}


	/** returns a list of config forms that can be used to enter products

		@comment
			takes warehouse oid as parameter

	**/
	function get_prod_add_config_forms($arr)
	{
		$wh = obj($arr["warehouse"]);
		$conf_id = $wh->prop("conf");
		$ret = array();
		if(is_oid($conf_id) and $this->can("view", $conf_id))
		{
			$conf = obj($conf_id);
			$this->_req_get_prod_add_config_forms($conf->prop("prod_type_fld"), $ret, "sp_cfgform");
		}
		return $ret;
	}

	/** returns a list of config forms that can be used to enter product packagings

		@comment
			takes warehouse oid as parameter

	**/
	function get_prod_packaging_add_config_forms($arr)
	{
		$wh = obj($arr["warehouse"]);
		$conf_id = $wh->prop("conf");
		$ret = array();
		if(is_oid($conf_id) and $this->can("view", $conf_id))
		{
			$conf = obj($conf_id);
			$this->_req_get_prod_add_config_forms($conf->prop("prod_type_fld"), $ret, "packaging_cfgform");
		}
		return $ret;
	}

	function _req_get_prod_add_config_forms($parent, &$ret, $prop)
	{
		$ol = new object_list(array(
			"parent" => $parent,
			"class_id" => array(CL_MENU, CL_SHOP_PRODUCT_TYPE),
		));
		foreach($ol->arr() as $o)
		{
			if ($o->class_id() != CL_MENU)
			{
				if (is_oid($cf_id = $o->prop($prop)) and $this->can("view", $cf_id))
				{
					$ret[$cf_id] = $cf_id;
				}
			}
			else
			{
				$this->_req_get_prod_add_config_forms($o->id(), $ret, $prop);
			}
		}
	}

	/**

		@attrib name=confirm_orders all_args=1

	**/
	function confirm_orders($arr)
	{
		if (is_array($arr["sel"]) and count($arr["sel"]))
		{
			$re = get_instance(CL_SHOP_ORDER);
			foreach($arr["sel"] as $id => $one)
			{
				$re->do_confirm(obj($id));
			}
		}
		return $this->mk_my_orb("change", array("id" => $arr["id"], "group" => $arr["group"]));
	}

	/**

		@attrib name=print_orders all_args=1

	**/
	function print_orders($arr)
	{
		if($_GET["y"])
		{
			set_time_limit(1800);
			$ol = new object_list(array(
				"class_id" => CL_SHOP_SELL_ORDER,
				"payment_type" => 668535,
				"created" => new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, mktime(0,0,0,1,25,2010), mktime(0,0,0,1,27,2011)),
			));
			print $ol->count();

			foreach($ol-> arr() as $o)
			{$order_data = $o->meta("order_data");
				print $o->id()." - " . $o->prop("purchaser.name"). " - " .  $order_data["personalcode"]."<br>";


			}

die();

			$arr["sel"] = $ol->ids();
		}




		$res = "";
//		fopen("http://games.swirve.com/utopia/login.htm");
//		die();
		$oo = get_instance(CL_SHOP_SELL_ORDER);
		if (is_array($arr["sel"]) and count($arr["sel"]))
		{
			foreach($arr["sel"] as $id)
			{;

				if($this->can("view", $id))
				{
					$vars = array(
						"id" => $id,
					);
					$res.='<DIV style="page-break-after:always">';

					if(file_exists($oo->site_template_dir."/print_order.tpl"))
					{
						$vars["template"] = "print_order.tpl";
//						$vars["unsent"] = $_GET["unsent"];
					}
//					arr($oo->show($vars));
					$res .= $oo->show($vars);
				//	$res .= $oo->request_execute(obj($id));
					$res.='</DIV>';
				}

/*				$link =  $this->mk_my_orb("print_orders", array("print_id" => $id));
				$res.= '<script name= javascript>window.open("'.$link.'","", "toolbar=no, directories=no, status=no, location=no, resizable=yes, scrollbars=yes, menubar=no, height=800, width=720")</script>';
				//"<script language='javascript'>setTimeout('window.close()',10000);window.print();if (navigator.userAgent.toLowerCase().indexOf('msie') == -1) {window.close(); }</script>";
*/			}
		//		$res.= "<script name= javascript>setTimeout('window.close()',10000);window.print();</script>";
		}
//		elseif($this->can("view", $arr["print_id"]))
//		{
//
//			$res .= $oo->request_execute(obj($arr["print_id"]));
//			$res .= "
//				<script language='javascript'>
//					setTimeout('window.close()',5000);
//					window.print();
//				//	if (navigator.userAgent.toLowerCase().indexOf('msie') == -1) {window.close(); }
//				</script>
//			";
//		}
		else
		{
			$res .= t("Pole midagi printida");
		}

//		$res .= "<script language='javascript'>setTimeout('window.close()',10000);window.print();if (navigator.userAgent.toLowerCase().indexOf('msie') == -1) {window.close(); }</script>";
//return $res;
		if (file_exists($this->site_template_dir."/print_orders.tpl"))
		{
			$this->read_any_template("print_orders.tpl");
			$this->vars_safe(array(
				"content" => $res,
			));
			$res = $this->parse();
		}
		die($res);
	}

	function _get_category_entry_form($arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_CFGFORM,
			"subclass" => CL_MENU
		));
		$arr["prop"]["options"] = array("" => t("--vali--")) + $ol->names();
	}

	function _get_status_calc_type($arr)
	{
		$arr["prop"]["options"] = $arr["obj_inst"]->get_status_calc_options();
	}

	function do_db_upgrade($t, $f)
	{
		switch($f)
		{
			case "aw_order_center":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				$ol = new object_list(array(
					"class_id" => CL_SHOP_WAREHOUSE,
				));
				foreach($ol->arr() as $o)
				{
					$this->db_query(sprintf("UPDATE aw_shop_warehouses SET aw_order_center = '%u' WHERE aw_oid = '%u'", $o->meta("order_center"), $o->id()));
				}
				return true;

			case "aw_status_calc_type":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}

	function _get_storage_export_tree($arr)
	{
		return $this->_get_storage_income_tree($arr);
	}

	function _get_storage_income_tree($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->start_tree(array(
			"type" => TREE_DHTML,
			"tree_id" => "storage_tree",
			"persist_state" => true,
			"has_root" => false,
			"get_branch_func" => null,
		));
		$disp = $arr["request"]["disp"];
		$group = $this->get_search_group($arr);
		$t->add_item(0, array(
			"id" => "sl",
			"url" => aw_url_change_var(array($group."_s_status" => STORAGE_FILTER_CONFIRMATION_ALL, $group."_s_type" => 2)),
			"name" => t("Saatelehed")
		));

			$t->add_item("sl", array(
				"id" => "sl_unc",
				"url" => aw_url_change_var(array($group."_s_status" => STORAGE_FILTER_UNCONFIRMED, $group."_s_type" => 2)),
				"name" => t("Kinnitamata")
			));

			$t->add_item("sl", array(
				"id" => "sl_conf",
				"url" => aw_url_change_var(array($group."_s_status" => STORAGE_FILTER_CONFIRMED ,$group."_s_type" => 2)),
				"name" => t("Kinnitatud")
			));

		$t->add_item(0, array(
			"id" => "bl",
			"url" => aw_url_change_var(array($group."_s_status" => STORAGE_FILTER_CONFIRMATION_ALL, $group."_s_type" => 1)),
			"name" => t("Arved")
		));

			$t->add_item("bl", array(
				"id" => "bl_unc",
				"url" => aw_url_change_var(array($group."_s_status" => STORAGE_FILTER_UNCONFIRMED, $group."_s_type" => 1)),
				"name" => t("Kinnitamata")
			));

			$t->add_item("bl", array(
				"id" => "bl_conf",
				"url" => aw_url_change_var(array($group."_s_status" => STORAGE_FILTER_CONFIRMED, $group."_s_type" => 1)),
				"name" => t("Kinnitatud")
			));
	}

	function _get_storage_movements_toolbar(&$data)
	{
		$tb = $data["prop"]["vcl_inst"];

		$tb->add_menu_button(array(
			"name" => "create",
			"tooltip" => t("Uus")
		));

		if(empty($data["warehouses"]) and $data["obj_inst"]->is_a(shop_warehouse_obj::CLID))
		{
			$whs = array($data["obj_inst"]);
		}
		else
		{
			foreach($data["warehouses"] as $wh)
			{
				$whs[$wh] = obj($wh);
			}
		}
		$vars = array("_income", "_export");
		if($data["prop"]["name"] === "storage_writeoffs_toolbar")
		{
			$vars = array("");
		}
		else
		{
			$tb->add_sub_menu(array(
				"name" => "create_export",
				"text" => t("V&auml;ljaminek"),
				"parent" => "create",
			));
			$tb->add_sub_menu(array(
				"name" => "create_income",
				"text" => t("Sissetulek"),
				"parent" => "create",
			));
		}
		foreach($vars as $var)
		{
			foreach($whs as $whid)
			{
				$who = obj($whid);
				$pt = $who->prop("conf.".(($var === "_export") ? "export_fld" : "reception_fld"));
				$npt = "create".$var;
				if(count($whs) > 1)
				{
					$tb->add_sub_menu(array(
						"name" => "wh_".$var.$whid,
						"text" => $who->name(),
						"parent" => "create".$var,
					));
					$npt = "wh_".$var.$whid;
				}
				$tb->add_menu_item(array(
					"parent" => $npt,
					"text" => t("Saateleht"),
					"link" => $this->mk_my_orb("new", array(
						"parent" => $pt,
						"return_url" => get_ru()
					), CL_SHOP_DELIVERY_NOTE)
				));
			}
		}

		$tb->add_save_button();
		$tb->add_delete_button();
	}

	function _get_storage_movements_tree($arr)
	{
		return $this->mk_prodg_tree($arr);
	}

	function _get_storage_movements_tree2($arr)
	{
		return $this->get_prod_tree($arr);
	}

	private function _init_storage_movements_tbl(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->define_field(array(
			"name" => "product",
			"caption" => t("Artikkel"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "from_wh",
			"caption" => t("Laost"),
			"align" => "center"
		));

		$group = $this->get_search_group($arr);

		if($group != "storage_writeoffs")
		{
			$t->define_field(array(
				"name" => "to_wh",
				"caption" => t("Lattu"),
				"align" => "center"
			));
		}

		$t->define_field(array(
			"name" => "created",
			"caption" => t("Kuup&auml;ev"),
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "amount",
			"caption" => t("Kogus"),
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "dn",
			"caption" => t("Saateleht"),
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "bill",
			"caption" => t("Arve"),
			"align" => "center",
		));

		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));

		$t->set_rgroupby(array("prod" => "prod"));
		$t->set_default_sortby("total");
		$t->set_default_sorder("asc");
	}

	private function _get_movements_ol($arr)
	{
		$group = $this->get_search_group($arr);
		if($group === "storage_writeoffs")
		{
			$wh_prop = "from_wh";
			$params["CL_SHOP_WAREHOUSE_MOVEMENT.delivery_note.writeoff"] = 1;
		}
		else
		{
			if(!empty($arr["request"][$group."_s_type"]))
			{
				if(STORAGE_FILTER_INCOME == $arr["request"][$group."_s_type"])
				{
					$wh_prop = "to_wh";
				}
				elseif(STORAGE_FILTER_EXPORT == $arr["request"][$group."_s_type"])
				{
					$wh_prop = "from_wh";
				}
			}
			$params["CL_SHOP_WAREHOUSE_MOVEMENT.delivery_note.writeoff"] = new obj_predicate_not(1);
		}
		if(!empty($arr["request"][$group."_s_warehouse"]))
		{
			$wh = $arr["request"][$group."_s_warehouse"];
		}
		else
		{
			if($arr["obj_inst"]->class_id() == CL_SHOP_WAREHOUSE)
			{
				$wh = $arr["obj_inst"]->id();
			}
			elseif(count($arr["warehouses"]))
			{
				$wh = $arr["warehouses"];
			}
		}
		if(!empty($wh))
		{
			if(!empty($wh_prop))
			{
				$params[$wh_prop] = $wh;
			}
			else
			{
				$params[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						"from_wh" => $wh,
						"to_wh" => $wh,
					),
				));
			}
		}
		if(empty($arr["request"][$group."_s_from"]))
		{
			$arr["request"][$group."_s_from"] = mktime(0, 0, 0, date('m'), 1, date('Y'));
		}
		if(empty($arr["request"][$group."_s_to"]))
		{
			$arr["request"][$group."_s_to"] = mktime(0, 0, 0, date('m')+1, 0, date('Y'));
		}
		if(!empty($arr["request"][$group."_s_from"]) || !empty($arr["request"][$group."_s_to"]))
		{
			$to = is_numeric($arr["request"][$group."_s_to"]) ? $arr["request"][$group."_s_to"] : date_edit::get_timestamp($arr["request"][$group."_s_to"]);
			$from = is_numeric($arr["request"][$group."_s_from"]) ? $arr["request"][$group."_s_from"] : date_edit::get_timestamp($arr["request"][$group."_s_from"]);
			if($from > 0 and $to > 0)
			{
				$to += 24 * 60 * 60 -1;
				$params["created"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $from, $to);
			}
			elseif($from > 0)
			{
				$params["created"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $from);
			}
			elseif($to > 0)
			{
				$to += 24 * 60 * 60 -1;
				$params["created"] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $to);
			}
		}
		if(!empty($arr["request"][$group."_s_article"]))
		{
			$params["CL_SHOP_WAREHOUSE_MOVEMENT.product.name"] = "%{$arr["request"][$group."_s_article"]}%";
		}
		if(!empty($arr["request"][$group."_s_articlecode"]))
		{
			$params["CL_SHOP_WAREHOUSE_MOVEMENT.product.code"] = "%{$arr["request"][$group."_s_articlecode"]}%";
		}
		if(!empty($arr["request"][$group."_s_number"]))
		{
			$params["CL_SHOP_WAREHOUSE_MOVEMENT.delivery_note.number"] = "%{$arr["request"][$group."_s_number"]}%";
		}
		$params["product"] = $this->get_art_cat_filter(isset($arr["request"][$group."_s_art_cat"]) ? $arr["request"][$group."_s_art_cat"] : NULL);
		$ol = new object_list();
		if(!empty($arr["request"]["ptf"]))
		{
			$prod_ol = new object_list(array(
				"parent" => $arr["request"]["ptf"],
				"class_id" => CL_SHOP_PRODUCT,
			));
			if($prod_ol->count() > 0)
			{
				$params["CL_SHOP_WAREHOUSE_MOVEMENT.product"] = $prod_ol->ids();
			}
			else
			{
				$params["CL_SHOP_WAREHOUSE_MOVEMENT.product"] = array(-1);
			}
		}
		if(count($params))
		{
			$params["class_id"] = CL_SHOP_WAREHOUSE_MOVEMENT;
			$ol->add(new object_list($params));
		}
		return $ol;
	}

	function _get_storage_movements_s_from($arr)
	{
		if (empty($arr["request"][$arr["prop"]["name"]]))
		{
			$arr["prop"]["value"] = mktime(0, 0, 0, date('m'), 1, date('Y'));
		}
		else
		{
			$arr["prop"]["value"] = date_edit::get_timestamp($arr["request"][$arr["prop"]["name"]]);
		}
		$arr["prop"]["format"] = array("day_textbox", "month_textbox", "year_textbox");
	}

	function _get_storage_movements_s_to($arr)
	{
		if (empty($arr["request"][$arr["prop"]["name"]]))
		{
			$arr["prop"]["value"] = mktime(0, 0, 0, date('m')+1, 0, date('Y'));
		}
		else
		{
			$arr["prop"]["value"] = date_edit::get_timestamp($arr["request"][$arr["prop"]["name"]]);
		}
		$arr["prop"]["format"] = array("day_textbox", "month_textbox", "year_textbox");
	}

	function _get_storage_movements(&$arr)
	{
		$this->_init_storage_movements_tbl($arr);

		$ol = $this->_get_movements_ol($arr);

		$t = $arr["prop"]["vcl_inst"];

		$total = array();
		foreach($ol->arr() as $o)
		{
			$objs = array("product", "from_wh", "to_wh");
			$data["oid"] = $o->id();
			foreach($objs as $obj)
			{
				if($this->can("view", ($id = $o->prop($obj))))
				{
					${$obj} = obj($id);
					$data[$obj] = html::obj_change_url(${$obj}, parse_obj_name(${$obj}->name()));
				}
			}
			$data["prod"] = obj($o->prop("product"))->name();
			$data["created"] = date('d.m.Y, H:i', $o->created());
			$data["amount"] = $o->prop("amount")." ".$o->prop("unit.unit_code");
			$total[$o->prop("product")][$o->prop("unit")] += $o->prop("amount");
			if($this->can("view", ($id = $o->prop("delivery_note"))))
			{
				$dno = obj($id);
				$cnum = $dno->prop("number");
				$data["dn"] = html::obj_change_url($dno, $cnum ? $cnum : t("(Puudub)"));
				$conn = $dno->connections_to(array(
					"type" => "RELTYPE_DELIVERY_NOTE",
					"from.class_id" => CL_CRM_BILL,
				));
				$bills = array();
				foreach($conn as $c)
				{
					$no = $c->from()->prop("bill_no");
					$bills[] = html::obj_change_url($c->from(), $no ? $no : t("(Puudub)"));
				}
				$data["bill"] = implode(", ", $bills);
			}
			$t->define_data($data);
		}

		foreach($total as $prod => $data)
		{
			foreach($data as $unit => $total)
			{
				$t->define_data(array(
					"prod" => obj($prod)->name(),
					"product" => sprintf(t("Kokku (%s):"), obj($unit)->prop("unit_code")),
					"amount" => $total." ".obj($unit)->prop("unit_code"),
					"total" => 1,
				));
			}
		}
	}

	function _get_storage_writeoffs_toolbar(&$arr)
	{
		return $this->_get_storage_movements_toolbar($arr);
	}

	function _get_storage_writeoffs_tree($arr)
	{
		return $this->mk_prodg_tree($arr);
	}

	function _get_storage_writeoffs_tree2($arr)
	{
		return $this->get_prod_tree($arr);
	}

	function _get_storage_writeoffs(&$arr)
	{
		return $this->_get_storage_movements($arr);
	}

	function _get_storage_status_toolbar($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "refresh",
			"tooltip" => t("Uuenda"),
			"url" => "javascript:window.location.reload()",
			"img" => "refresh.gif",
		));
	}

	function _get_storage_status_tree(&$arr)
	{
		return $this->get_prod_tree($arr);
	}

	function _get_storage_status_tree2(&$arr)
	{
		return $this->mk_prodg_tree($arr);
	}

	private function _init_storage_status_tbl($t)
	{
		$t->define_field(array(
			"name" => "icon",
			"caption" => t("&nbsp;"),
			"sortable" => 0,
		));

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "code",
			"caption" => t("Kood"),
			"align" => "center"
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "last_purchase_price",
			"caption" => t("Ostuhind"),
			"align" => "center"
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "price_fifo",
			"caption" => t("FIFO"),
			"align" => "center"
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "sales_price",
			"caption" => t("M&uuml;&uuml;gihind"),
			"align" => "center"
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "amount1",
			"caption" => t("Kogus"),
			"align" => "center"
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "unit1",
			"caption" => t("&Uuml;hik"),
			"align" => "center"
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "amount2",
			"caption" => t("Kogus 2"),
			"align" => "center"
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "unit2",
			"caption" => t("&Uuml;hik 2"),
			"align" => "center"
		));


		$t->define_field(array(
			"sortable" => 1,
			"name" => "item_type",
			"caption" => t("T&uuml;&uuml;p"),
			"align" => "center"
		));

		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	function _get_storage_status(&$arr)
	{
		$this->get_products_list($arr);
	}

	function _get_storage_prognosis_toolbar($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "refresh",
			"tooltip" => t("Uuenda"),
			"url" => "javascript:window.location.reload()",
			"img" => "refresh.gif",
		));
	}

	function _get_storage_prognosis_tree(&$arr)
	{
		return $this->get_prod_tree($arr);
	}

	function _get_storage_prognosis_tree2(&$arr)
	{
		return $this->mk_prodg_tree($arr);
	}


	function _get_storage_prognosis(&$arr)
	{
		$this->get_products_list($arr);

	}

	function _get_storage_inventories_toolbar($data)
	{
		$tb = $data["prop"]["vcl_inst"];
		$tb->add_menu_button(array(
			"name" => "create_new",
			"tooltip" => t("Uus")
		));
		if(empty($data["warehouses"]) and $data["obj_inst"]->is_a(shop_warehouse_obj::CLID))
		{
			$whs = array($data["obj_inst"]);
		}
		else
		{
			foreach($data["warehouses"] as $wh)
			{
				$whs[$wh] = obj($wh);
			}
		}
		$npt = "create_new";
		foreach($whs as $wh)
		{
			$whid = $wh->id();
			$pt = $wh->prop("conf.".(($data["prop"]["name"] === "storage_export_toolbar") ? "export_fld" : "reception_fld"));
			if(count($whs) > 1)
			{
				$tb->add_sub_menu(array(
					"name" => "wh_".$whid,
					"text" => $wh->name(),
					"parent" => "create_new",
				));
				$npt = "wh_".$whid;
			}
			$tb->add_menu_item(array(
				"parent" => $npt,
				"text" => t("Uus inventuur"),
				"link" => $this->mk_my_orb("new", array(
					"parent" => $whid,
					"return_url" => get_ru(),
					"warehouse" => $whid
				), CL_SHOP_WAREHOUSE_INVENTORY)
			));
		}

		$tb->add_save_button();
		$tb->add_delete_button();
	}

	function _get_storage_inventories_tree($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$group = $this->get_search_group($arr);
		$var = $group."_s_status";
		$disp = isset($arr["request"][$var]) ? $arr["request"][$var] : null;

		$arr["request"][$var] = STORAGE_FILTER_UNCONFIRMED;
		$count1 = $this->_get_inventories_ol($arr)->count();
		$arr["request"][$var] = STORAGE_FILTER_CONFIRMED;
		$count2 = $this->_get_inventories_ol($arr)->count();

		$t->add_item(0, array(
			"id" => "unc",
			"url" => aw_url_change_var($var, STORAGE_FILTER_UNCONFIRMED),
			"name" => sprintf("%s (%s)", $disp == STORAGE_FILTER_UNCONFIRMED ? "<b>".t("Kinnitamata")."</b>" : t("Kinnitamata"), $count1),
		));

		$t->add_item(0, array(
			"id" => "conf",
			"url" => aw_url_change_var($var, STORAGE_FILTER_CONFIRMED),
			"name" => sprintf("%s (%s)", $disp == STORAGE_FILTER_CONFIRMED ? "<b>".t("Kinnitatud")."</b>" : t("Kinnitatud"), $count2),
		));
	}

	function _get_inventories_ol($arr)
	{
		if(empty($arr["warehouses"]) and $arr["obj_inst"]->is_a(shop_warehouse_obj::CLID))
		{
			$arr["warehouses"] = array($arr["obj_inst"]->id());
		}
		$params = array(
			"class_id" => CL_SHOP_WAREHOUSE_INVENTORY,
			"warehouse" => $arr["warehouses"],
		);
		$group = $this->get_search_group($arr);
		if(!empty($arr["request"][$group."_s_name"]))
		{
			$params["name"] = "%{$arr["request"][$group."_s_name"]}%";
		}
		if(!empty($arr["request"][$group."_s_status"]))
		{
			if(STORAGE_FILTER_CONFIRMED == $arr["request"][$group."_s_status"])
			{
				$params["confirmed"] = 1;
			}
			elseif(STORAGE_FILTER_UNCONFIRMED == $arr["request"][$group."_s_status"])
			{
				$params["confirmed"] = new obj_predicate_not(1);
			}
		}
		$from = isset($arr["request"][$group."_s_from"]) ? date_edit::get_timestamp($arr["request"][$group."_s_from"]) : 0;
		$to = isset($arr["request"][$group."_s_to"]) ? date_edit::get_timestamp($arr["request"][$group."_s_to"]) : 0;
		if($from > 0 and $to > 0)
		{
			$to += 24 * 60 * 60 -1;
			$params["date"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $from, $to);
		}
		elseif($from > 0)
		{
			$params["date"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $from);
		}
		elseif($to > 0)
		{
			$to += 24 * 60 * 60 -1;
			$params["date"] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $to);
		}
		$ol = new object_list($params);
		return $ol;
	}

	function _get_storage_inventories(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_storage_inventories_tbl($t);
		$ol = $this->_get_inventories_ol($arr);
		foreach($ol->arr() as $o)
		{
			$t->define_data(array(
				"name" => html::obj_change_url($o, parse_obj_name($o->name())),
				"created" => $o->created(),
				"sum" => 0,
				"status" => $o->prop("confirmed")?t("Kinnitatud"):t("Kinnitamata"),
				"oid" => $o->id(),
			));
		}
	}

	private function _init_storage_inventories_tbl($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
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

	function _get_purchase_orders_toolbar(&$data)
	{
		$tb = $data["prop"]["vcl_inst"];
		$tb->add_menu_button(array(
			"name" => "create_new",
			"tooltip" => t("Uus")
		));

		if(empty($data["warehouses"]) and $data["obj_inst"]->class_id() == CL_SHOP_WAREHOUSE)
		{
			$whs = array($data["obj_inst"]);
		}
		else
		{
			foreach($data["warehouses"] as $wh)
			{
				if($this->can("view", $wh))
				{
					$whs[$wh] = obj($wh);
				}
			}
		}
		$clid = ($this->get_search_group($data)=="purchase_orders")?CL_SHOP_PURCHASE_ORDER:CL_SHOP_SELL_ORDER;
		$npt = "create_new";
		foreach($whs as $wh)
		{
			$whid = $wh->id();
			if(count($whs) > 1)
			{
				$tb->add_sub_menu(array(
					"name" => "wh_".$whid,
					"text" => $wh->name(),
					"parent" => "create_new",
				));
				$npt = "wh_".$whid;
			}
			$pt = $wh->prop("conf.order_fld");
			$tb->add_menu_item(array(
				"parent" => $npt,
				"text" => t("Tellimus"),
				"link" => $this->mk_my_orb("new", array(
					"parent" => $pt,
					"return_url" => get_ru(),
					"warehouse" => $whid,
				), $clid)
			));
		}

		$tb->add_save_button();
		$tb->add_delete_button();
		$tb->add_button(array(
			"name" => "print",
			"tooltip" => t("Prindi tellimused"),
			"img" => "print.gif",
			"url" => "javascript:document.changeform.target='_blank';javascript:submit_changeform('print_orders', false, true)",
			"onclick_disable" => false,
		));

		// CSV
		$tb->add_menu_button(array(
			"name" => "csv",
			"tooltip" => t("Ekspordi CSV"),
			"text" => "CSV",
		));

		$tb->add_menu_item(array(
			"parent" => "csv",
			"text" => t("Hetkel kuvatavad tellimused"),
			"link" => aw_url_change_var("action", "csv_export"),
		));

		$tb->add_menu_item(array(
			"parent" => "csv",
			"text" => t("Valitud tellimused"),
			"action" => "csv_export",
			"onclick_disable" => false,
		));

		$branches = array(
			"yesterday" => t("Eilsed tellimused"),
			"today" => t("T&auml;nased tellimused"),
			"lastweek" => t("Eelmise n&auml;dala tellimused"),
			"thisweek" => t("K&auml;esoleva n&auml;dala tellimused"),
			"lastmonth" => t("Eelmise kuu"),
			"thismonth" => t("K&auml;esoleva kuu tellimused"),
		);

		foreach($branches as $id => $caption)
		{
			$tb->add_menu_item(array(
				"parent" => "csv",
				"text" => $caption,
				"link" => aw_url_change_var(array(
					"action" => "csv_export",
					"filt_time" => $id,
				)),
			));
		}

		$tb->add_menu_button(array(
			"name" => "type",
			"text" => t("Muuda staatust"),
			"tooltip" => t("Objektit&uuml;&uuml;p mida tootena kuvatakse"),
		));
		load_javascript("reload_properties_layouts.js");
		$order = get_instance(CL_SHOP_SELL_ORDER);
		foreach($order->states as $key => $name)
		{
			$tb->add_menu_item(array(
				"parent" => "type",
				"text" => $name,
				"link" => "javascript:set_sel_prop('status' , '".$key."');",
			));
		}
	}

	function _get_sell_orders_channel_tree($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$channels = $arr["obj_inst"]->get_channels();
		$t->set_selected_item(empty($arr["request"]["channel"]) ? "channel_all" : "channel_".$arr["request"]["channel"]);
		$odl = $this->_get_orders_odl($arr);

		foreach($channels->names() as $channel => $name)
		{
			$cnt = 0;
			foreach($odl->arr() as $odata)
			{
				if($odata["channel"] == $channel)
				{
					$cnt++;
				}
			}
			$t->add_item(0, array(
				"id" => "channel_".$channel,
				"url" => aw_url_change_var("channel", $channel),
				"name" =>  sprintf("%s (%s)",  $name, $cnt),
			));
		}
		$t->add_item(0, array(
			"id" => "channel_all",
			"url" => aw_url_change_var("channel", "all"),
			"name" => sprintf("%s (%s)",  t("K&otilde;ik m&uuml;&uuml;gikanalid"), $odl->count()),
		));
	}

	function _get_purchase_orders_tree($arr)
	{
		$oi = get_instance(CL_SHOP_PURCHASE_ORDER);
		$t = $arr["prop"]["vcl_inst"];

		$group = $this->get_search_group($arr);
		$var = $group."_s_status";
		$disp = isset($arr["request"][$var]) ? $arr["request"][$var] : "";
		$t->set_selected_item($disp ? "state_".$disp :"state_5" );
		if($disp == 10)
		{
			$t->set_selected_item("state_all");
		}

		$odl = $this->_get_orders_odl($arr);

		foreach($oi->states as $id => $state)
		{
			$cnt = 0;
			foreach($odl->arr() as $odata)
			{
				if($odata["order_status"] == $id)
				{
					$cnt++;
				}
			}
			$t->add_item(0, array(
				"id" => "state_".$id,
				"url" => aw_url_change_var($var, $id),
				"name" => sprintf("%s (%s)", $state, $cnt),
			));
		}
		$t->add_item(0, array(
			"id" => "state_all",
			"url" => aw_url_change_var($var, STORAGE_FILTER_CONFIRMATION_ALL),
			"name" => sprintf("%s (%s)", t("K&otilde;ik"), $odl->count()),
		));
	}

	function _get_orders_odl($arr, $additional_properties = array())
	{
		$hash = md5(serialize($arr["request"])) . md5(serialize($additional_properties));

		static $odl_by_hash;
		if(!empty($odl_by_hash[$hash]))
		{
			return $odl_by_hash[$hash];
		}

		if(empty($arr["request"]["sell_orders_s_status"]))
		{
			$arr["request"]["sell_orders_s_status"] = 5;
		}
		$odl_by_hash[$hash] = $odl = new object_data_list();
		$group = $this->get_search_group($arr);

		$co_prop = "purchaser";
		if($group === "purchase_orders")
		{
			$co_filt = "purchaser";
			$class_id = CL_SHOP_PURCHASE_ORDER;
		}
		elseif($group === "sell_orders")
		{
			$co_filt = "buyer";
			$class_id = CL_SHOP_SELL_ORDER;
		}
		else
		{
			return $odl;
		}
		$params = array();
		if(!empty($arr["request"]["sel"]))
		{
			$params["oid"] = $arr["request"]["sel"];
		}
		if(isset($arr["request"]["channel"]) and is_oid($arr["request"]["channel"]))
		{
			$params["channel.id"] = $arr["request"]["channel"];
		}
		if(!empty($arr["request"][$group."_s_number"]) and $n = $arr["request"][$group."_s_number"])
		{
			$params["oid"] = "%".$n."%";
		}
		if(!empty($arr["request"][$group."_s_purchaser_id"]))
		{
			$purchaser_ids_odl = new object_data_list(
				array(
					"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
					"oid" => "%{$arr["request"][$group."_s_purchaser_id"]}%",
					"buyer" => new obj_predicate_compare(OBJ_COMP_GREATER, 0, NULL, "int"),
				),
				array(
					CL_CRM_COMPANY_CUSTOMER_DATA => array("buyer"),
				)
			);
			$params["purchaser"] = array_merge(array(-1), $purchaser_ids_odl->get_element_from_all("buyer"));
		}
		if(!empty($arr["request"][$group."_s_purchaser_other_id"]))
		{
			/*
			$params[] = new object_list_filter(array(
				"logic" => "OR",
				array(
					"purchaser(CL_CRM_PERSON).external_id" => "%".$purchaser_other."%",
					"purchaser(CL_CRM_COMPANY).external_id" => "%".$purchaser_other."%",
				)
			));
			*/
			$params["purchaser(CL_CRM_PERSON).external_id"] = "%{$arr["request"][$group."_s_purchaser_other_id"]}%";
		}
		if(!empty($arr["request"][$group."_s_".$co_filt]) and $co = $arr["request"][$group."_s_".$co_filt])
		{
			$co = $arr["request"][$group."_s_".$co_filt];
		}
		elseif(!empty($arr["request"]["filt_cust"]) and $this->can("view", $arr["request"]["filt_cust"]))
		{
			$co = obj($arr["request"]["filt_cust"]);
			if($co->class_id() == CL_CRM_CATEGORY)
			{
				// $params[$co_prop.".RELTYPE_CUSTOMER(CL_CRM_CATEGORY)"] = $co->id();
				//FIXME: RELTYPE_CUSTOMER reltype doesn't exist
				unset($co);
			}
			else
			{
				$co = $co->name();
			}
		}
		if(!empty($co))
		{
			$params[$co_prop.".name"] = "%".$co."%";
		}
		if(!empty($arr["request"][$group."_s_sales_manager"]) and $sm = $arr["request"][$group."_s_sales_manager"])
		{
			$params["job.RELTYPE_MRP_MANAGER.name"] = "%".$sm."%";
		}
		if(!empty($arr["request"][$group."_s_job_name"]) and $jn = $arr["request"][$group."_s_job_name"])
		{
			$params["job.comment"] = "%".$jn."%";
		}
		if(!empty($arr["request"][$group."_s_job_number"]) and $jno = $arr["request"][$group."_s_job_number"])
		{
			$params["job.name"] = "%".$jno."%";
		}
		if(!empty($arr["request"][$group."_s_status"]) and $s = $arr["request"][$group."_s_status"])
		{
			if($s != STORAGE_FILTER_CONFIRMATION_ALL)
			{
				$params["order_status"] = $s;
			}
			else
			{
				$params["order_status"] = new obj_predicate_anything();
			}
		}
		$t = isset($arr["request"][$group."_s_to"]) ? date_edit::get_timestamp($arr["request"][$group."_s_to"]) : 0;
		$f = isset($arr["request"][$group."_s_from"]) ? date_edit::get_timestamp($arr["request"][$group."_s_from"]) : 0;
		if($t > 1 and $f > 1 and empty($arr["request"]["filt_time"]))
		{
			$t += 24 * 60 * 60 - 1;
			$params["date"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $f, $t);
		}
		elseif($f>1 and empty($arr["request"]["filt_time"]))
		{
			$params["date"] = new obj_predicate_compare(OBJ_COMP_GREATER_EQ, $f);
		}
		elseif($t>1 and empty($arr["request"]["filt_time"]))
		{
			$t += 24 * 60 * 60 -1;
			$params["date"] = new obj_predicate_compare(OBJ_COMP_LESS_EQ, $t);
		}
		elseif(!empty($arr["request"]["filt_time"]) and $arr["request"]["filt_time"] != "all")
		{
			unset($arr["start"]);
			unset($arr["end"]);
			$v = $this->_get_status_orders_time_filt($arr);
			$params["date"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $v["filt_start"], $v["filt_end"]);
		}
		$prods = $this->get_art_filter_ol($arr);
		if($prods)
		{
			$c = new connection();
			$cs = $c->find(array(
				"type" => "RELTYPE_PRODUCT",
				"from.class_id" => $class_id,
				"to.class_id" => CL_SHOP_PRODUCT,
				"to.oid" => $prods->ids() + array(-1),
			));
			foreach($cs as $conn)
			{
				$params["oid"][] = $conn["from"];
			}
			if(!count($params["oid"]))
			{
				$params["oid"] = array(-1);
			}
		}
		if(count($params) or !empty($arr["request"]["just_saved"]) or isset($arr["request"]["filt_time"]) and $arr["request"]["filt_time"] === "all")
		{
			if(empty($arr["warehouses"]) and $arr["obj_inst"]->class_id() == CL_SHOP_WAREHOUSE)
			{
				$wh = $arr["obj_inst"]->id();
			}
			else
			{
				$wh = $arr["warehouses"];
			}
			//$params["warehouse"] = $wh;
			$params["class_id"] = $class_id;

			$odl_by_hash[$hash] = $odl = new object_data_list(
				$params,
				array(
					CL_SHOP_SELL_ORDER => array_merge(array("related_orders", "number", "order_status", "deal_date", "job", "purchaser", "purchaser(CL_CRM_PERSON).name" => "purchaser.name", "purchaser.class_id", "channel", "channel.name", "date", "currency", "order_status", "order_status"), $additional_properties)
				)
			);
		}
		return $odl;
	}

	function _get_orders_ol($arr)
	{
		$hash = md5(serialize($arr));

		static $ol_by_hash;
		if(!empty($ol_by_hash[$hash]))
		{
			return $ol_by_hash[$hash];
		}

		if(empty($arr["request"]["sell_orders_s_status"]))
		{
			$arr["request"]["sell_orders_s_status"] = 5;
		}
		$ol_by_hash[$hash] = $ol = new object_list();
		$group = $this->get_search_group($arr);

		$co_prop = "purchaser";
		if($group === "purchase_orders")
		{
			$co_filt = "purchaser";
			$class_id = CL_SHOP_PURCHASE_ORDER;
		}
		elseif($group === "sell_orders")
		{
			$co_filt = "buyer";
			$class_id = CL_SHOP_SELL_ORDER;
		}
		else
		{
			return $ol;
		}

		$params = array();
		if(!empty($arr["request"]["sel"]))
		{
			$params["oid"] = $arr["request"]["sel"];
		}
		if(isset($arr["request"]["channel"]) and is_oid($arr["request"]["channel"]))
		{
			$params["channel"] = $arr["request"]["channel"];
		}
		if(!empty($arr["request"][$group."_s_number"]) and $n = $arr["request"][$group."_s_number"])
		{
			$params["number"] = "%".$n."%";
		}
		if($purchaser = $arr["request"][$group."_s_purchaser_id"])
		{
			$purchaser_ids_odl = new object_data_list(
				array(
					"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
					"oid" => "%".$purchaser."%",
					"buyer" => new obj_predicate_compare(OBJ_COMP_GREATER, 0, NULL, "int"),
				),
				array(
					CL_CRM_COMPANY_CUSTOMER_DATA => array("buyer"),
				)
			);
			$params["purchaser"] = array_merge(array(-1), $purchaser_ids_odl->get_element_from_all("buyer"));
		}
		if($purchaser_other = $arr["request"][$group."_s_purchaser_other_id"])
		{
			/*
			$params[] = new object_list_filter(array(
				"logic" => "OR",
				array(
					"purchaser(CL_CRM_PERSON).external_id" => "%".$purchaser_other."%",
					"purchaser(CL_CRM_COMPANY).external_id" => "%".$purchaser_other."%",
				)
			));
			*/
			$params["purchaser(CL_CRM_PERSON).external_id"] = "%".$purchaser_other."%";
		}
		if(!empty($arr["request"][$group."_s_".$co_filt]) and $co = $arr["request"][$group."_s_".$co_filt])
		{
			$co = $arr["request"][$group."_s_".$co_filt];
		}
		elseif(!empty($arr["request"]["filt_cust"]) and $this->can("view", $arr["request"]["filt_cust"]))
		{
			$co = obj($arr["request"]["filt_cust"]);
			if($co->class_id() == CL_CRM_CATEGORY)
			{
				$params[$co_prop.".RELTYPE_CUSTOMER(CL_CRM_CATEGORY)"] = $co->id();
				unset($co);
			}
			else
			{
				$co = $co->name();
			}
		}
		if(!empty($co))
		{
			$params[$co_prop.".name"] = "%".$co."%";
		}
		if(!empty($arr["request"][$group."_s_sales_manager"]) and $sm = $arr["request"][$group."_s_sales_manager"])
		{
			$params["job.RELTYPE_MRP_MANAGER.name"] = "%".$sm."%";
		}
		if(!empty($arr["request"][$group."_s_job_name"]) and $jn = $arr["request"][$group."_s_job_name"])
		{
			$params["job.comment"] = "%".$jn."%";
		}
		if(!empty($arr["request"][$group."_s_job_number"]) and $jno = $arr["request"][$group."_s_job_number"])
		{
			$params["job.name"] = "%".$jno."%";
		}
		if(!empty($arr["request"][$group."_s_status"]) and $s = $arr["request"][$group."_s_status"])
		{
			if($s != STORAGE_FILTER_CONFIRMATION_ALL)
			{
				$params["order_status"] = $s;
			}
			else
			{
				$params["order_status"] = new obj_predicate_anything();
			}
		}
		$t = isset($arr["request"][$group."_s_to"]) ? date_edit::get_timestamp($arr["request"][$group."_s_to"]) : 0;
		$f = isset($arr["request"][$group."_s_from"]) ? date_edit::get_timestamp($arr["request"][$group."_s_from"]) : 0;
		if($t > 1 and $f > 1 and empty($arr["request"]["filt_time"]))
		{
			$t += 24 * 60 * 60 - 1;
			$params["date"] = new obj_predicate_compare(OBJ_COMP_BETWEEN, $f, $t);
		}
		elseif($f>1 and !$arr["request"]["filt_time"])
		{
			$params["date"] = new obj_predicate_compare(OBJ_COMP_GREATER, $f);
		}
		elseif($t>1 and !$arr["request"]["filt_time"])
		{
			$t += 24 * 60 * 60 -1;
			$params["date"] = new obj_predicate_compare(OBJ_COMP_LESS, $t);
		}
		elseif(empty($arr["request"]["filt_time"]) || $arr["request"]["filt_time"] != "all")
		{
			unset($arr["start"]);
			unset($arr["end"]);
			$v = $this->_get_status_orders_time_filt($arr);
			$params["date"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $v["filt_start"], $v["filt_end"]);
		}
		$prods = $this->get_art_filter_ol($arr);
		if($prods)
		{
			$c = new connection();
			$cs = $c->find(array(
				"type" => "RELTYPE_PRODUCT",
				"from.class_id" => $class_id,
				"to.class_id" => CL_SHOP_PRODUCT,
				"to.oid" => $prods->ids() + array(-1),
			));
			foreach($cs as $conn)
			{
				$params["oid"][] = $conn["from"];
			}
			if(!count($params["oid"]))
			{
				$params["oid"] = array(-1);
			}
		}
		if(count($params) || $arr["request"]["just_saved"] || $arr["request"]["filt_time"] === "all")
		{
			if(empty($arr["warehouses"]) and $arr["obj_inst"]->class_id() == CL_SHOP_WAREHOUSE)
			{
				$wh = $arr["obj_inst"]->id();
			}
			else
			{
				$wh = $arr["warehouses"];
			}
			//$params["warehouse"] = $wh;
			$params["class_id"] = $class_id;

			$ol->add(new object_list($params));
		}
		return $ol;
	}

	function _get_purchase_orders(&$arr)
	{
		$shop_sell_order_instance = new shop_sell_order();
		$user_instance = new user();
		$t = $arr["prop"]["vcl_inst"];
		$group = $this->get_search_group($arr);
		if(($arr["obj_inst"]->class_id() == CL_SHOP_PURCHASE_MANAGER_WORKSPACE and $group === "purchase_orders") || ($arr["obj_inst"]->class_id() == CL_SHOP_SALES_MANAGER_WORKSPACE and $group === "sell_orders"))
		{
			$arr["extra"] = 1;
		}
		if($group === "sell_orders" and $ws = $arr["obj_inst"]->prop("mrp_workspace"))
		{
			$schedule = new mrp_schedule();
			$schedule->create(array(
				"mrp_workspace" => obj($ws)->id(),
				"mrp_force_replan" => 1,
			));
			$arr["obj_inst"]->update_orders();
		}
		$this->_init_purchase_orders_tbl($t, $arr);
		$odl = $this->_get_orders_odl($arr);
		$count = 0;
		$total_sum = 0;
		$customer_relation_ids = array();
/*		if($this->can("view", $arr["obj_inst"]->prop("conf.owner")))
		{
			$owner = obj($arr["obj_inst"]->prop("conf.owner"));
			$customer_relation_ids = shop_sell_order_obj::get_customer_relation_ids_for_purchasers($odl->get_element_from_all("purchaser"), $owner, true);
		}*/
		$sums_for_orders = shop_sell_order_obj::get_sums_by_ids($odl->ids());
//		$external_ids = crm_person_obj::get_external_ids_for_person_ids($odl->ids());
		$purchaser_data = shop_sell_order_obj::get_purchaser_data_by_ids($odl->get_element_from_all("purchaser"))->arr();

		foreach($odl->arr() as $oid => $odata)
		{
			$count++;
			$other_rels = $odata["related_orders"];
			$rel_arr = array();
			// TO OPTIMIZE!
			if(count($other_rels))
			{
				$other_ol = new object_list(array(
					"oid" => $other_rels,
				));
				foreach($other_ol->arr() as $so)
				{
					$cnum = $so->prop("number");
					$rel_arr[] = html::obj_change_url($so, $cnum ? $cnum : t("(Puudub)"));
				}
			}

			$cnum = $odata["number"];
			$cid = $odata["job"];
			if($this->can("view", $cid))
			{
				$co = obj($cid);
				$case = html::obj_change_url($co, parse_obj_name($co->name())).", ".$co->comment();
			}
			else
			{
				$case = "";
			}
			$dd = $odata["deal_date"];
			$dealnow = 0;
			if($dd <= time() and $odata["order_status"] < ORDER_STATUS_SENT and !empty($arr["extra"]))
			{
				$dealnow = 1;
			}
			$add_row = null;
			$sum = 0;

			if(!empty($sums_for_orders[$oid]))
			{
				$sum += $sums_for_orders[$oid];
			}

			$total_sum+=$sum;
			if(($group === "purchase_orders" and $arr["obj_inst"]->class_id() == CL_SHOP_PURCHASE_MANAGER_WORKSPACE) || ($group === "sell_orders" and $arr["obj_inst"]->class_id() == CL_SHOP_SALES_MANAGER_WORKSPACE))
			{
				$add_row .= html::strong(t("Kommentaarid:"))."<br />";
				// TO OPTIMIZE!
				$com_conn = connection::find(array(
					"from.class_id" => CL_SHOP_SELL_PRODUCT,
					"from" => $oid,
					"type" => "RELTYPE_COMMENT",
				));
				$comments = array();
				foreach($com_conn as $cc)
				{
					$com = obj($cc["to"]);
					$uo = $user_instance->get_obj_for_uid($com->createdby());
					$p = $user_instance->get_person_for_user($uo);
					$name = obj($p)->name();
					$val = $name." @ ".date("d.m.Y H:i", $com->created())." - ".$com->prop("commtext");
					$comments[$com->id()] = $val;
				}
				if(is_array($comments) and count($comments))
				{
					$add_row .= implode("<br />", $comments)."<br />";
				}
				$add_row .= html::textbox(array(
					"name" => "orders[".$oid."][add_comment]",
					"size" => 40,
				))."<br />";

				if(count($conn))
				{
					$at = new vcl_table();
					$at->define_field(array(
						"name" => "name",
						"align" => "center",
					));
					$at->define_field(array(
						"name" => "code",
						"align" => "center",
					));
					$at->define_field(array(
						"name" => "type",
						"align" => "center",
					));
					$at->define_field(array(
						"name" => "comment",
						"align" => "center",
					));
					$at->define_field(array(
						"name" => "required",
						"align" => "center",
					));
					$at->define_field(array(
						"name" => "amount",
						"align" => "center",
					));
					$at->define_field(array(
						"name" => "real_amount",
						"align" => "center",
					));
					foreach($conn as $c)
					{
						$row = $c->to();
						$prodid = $row->prop("prod");
						if($this->can("view", $prodid))
						{
							$prod = obj($prodid);
							$data = array(
								"name" => $prod->name(),
								"code" => $prod->prop("code"),
								"amount" => $row->prop("amount"),
								"real_amount" => $row->prop("real_amount"),
								"type" => $prod->prop("item_type.name"),
								"comment" => $row->comment(),
								"required" => $row->prop("required"),
							);
						}
						$at->set_default_sortby("sb");
						$at->set_default_sorder("desc");
						$at->define_data($data);
//						$sum += $row->prop("amount") * $row->prop("price");
					}
					$at->define_data(array(
						"name" => html::strong(t("Nimi")),
						"code" => html::strong(t("Kood")),
						"amount" => html::strong(t("Kogus")),
						"real_amount" => html::strong(t("Saadud kogus")),
						"required" => html::strong(t("Vajadus")),
						"type" => html::strong(t("T&uuml;&uuml;p")),
						"comment" => html::strong(t("Kommentaar")),
						"sb" => 1,
					));
					$at->set_titlebar_display(false);
					$add_row .= html::strong(t("Artiklid:"))."<br />".$at->get_html(true);
				}
				//$add_row .= "<br />";
			}

			$cust_code = isset($customer_relation_ids[$odata["purchaser"]]) ? html::get_change_url($customer_relation_ids[$odata["purchaser"]], array(), $customer_relation_ids[$odata["purchaser"]], NULL, CL_CRM_COMPANY_CUSTOMER_DATA) : "";
			$t->define_data(array(
				"nr" => $count,
				"number" => html::get_change_url($oid, array(), $cnum ? $cnum : t("(Puudub)"), NULL, CL_SHOP_SELL_ORDER),
				"purchaser" => html::get_change_url($odata["purchaser"], array(), $odata["purchaser.name"], NULL, $odata["purchaser.class_id"]),
				"channel" => $odata["channel.name"],
				"purchaser_id" => $cust_code,	//$odata["purchaser"],
				"purchaser_other_id" => $purchaser_data[$odata["purchaser"]]["external_id"],
				"date" => $odata["date"],
				"rels" => implode(", ", $rel_arr),
				"sum" => $sum." ".get_name($odata["currency"]),
				"status" => $shop_sell_order_instance->states[$odata["order_status"]],
				"oid" => $oid,
				"start_date" => html::textbox(array(
					"name" => "orders[".$oid."][deal_date][day]",
					"value" => date('d', $dd),
					"style" => "width: 18px;",
				)).html::textbox(array(
					"name" => "orders[".$oid."][deal_date][month]",
					"value" => date('m', $dd),
					"style" => "width: 18px;",
				)).html::textbox(array(
					"name" => "orders[".$oid."][deal_date][year]",
					"value" => date('Y', $dd),
					"style" => "width: 40px;",
				)),
				"id" => $oid,
				"case" => $case,
				"color" => ($dealnow) ? "#FF4444" : "",
				"now" => $dealnow,
				"add_row" => $add_row ? array($add_row, array("style" => "background-color: #BBBBBB; height: 12px;")) :"",
			));
		}
		//$t->set_lower_titlebar_display(true);
		$time_capt = "";
		if(isset($arr["request"]["filt_time"]))
		{
			switch($arr["request"]["filt_time"])
			{
				case "today":
					$time_capt = t("t&auml;na");
					break;
				case "tomorrow":
					$time_capt = t("homme");
					break;
				case "yesterday":
					$time_capt = t("eile");
					break;
				case "lastweek":
					$time_capt = t("eelmine n&auml;dal");
					break;
				case "thisweek":
					$time_capt = t("k&auml;esolev n&auml;dal");
					break;
				case "nextweek":
					$time_capt = t("j&auml;rgmine n&auml;dal");
					break;
				case "lastmonth":
					$time_capt = t("eelmine kuu");
					break;
				case "thismonth":
					$time_capt = t("k&auml;esolev kuu");
					break;
				case "next":
					$time_capt = t("j&auml;rgmine kuu");
					break;
				case "":
					$time_capt = t("t&auml;na");
					break;
				default:
					$time_capt = t("k&otilde;ik perioodid");
			}
			if(is_numeric($arr["request"]["filt_time"]))
			{
				$time_capt = date("d.m.Y" , $arr["request"]["filt_time"]);
			}
		}

		if(isset($odata["currency"]))
		{
			$t->define_data(array(
				"purchaser" => t("Kokku"),
				"sum" => $total_sum." ".get_name($odata["currency"]),
			));
		}


		$sell_capt = t("Ostutellimused");
		if($arr["request"]["group"] === "sell_orders" || $arr["request"]["group"] === "sales")
		{
			$sell_capt = t("M&uuml;&uuml;gitellimused");
		}
		$t->set_caption(sprintf(t("%s: %s "), $sell_capt , $time_capt));
		$t->set_sortable(false);
//		$t->sort_by(array(
//			"field" => "now",
//			"sorder" => "asc",
//		));
	}

	function _set_purchase_orders(&$arr)
	{
		$tmp = $arr["request"]["orders"];
		if($tmp)
		{
			foreach($tmp as $oid => $data)
			{
				$o = obj($oid);
				$save = false;
				foreach($data as $prop => $val)
				{
					if($o->is_property($prop) and $val != $o->prop($prop))
					{
						$o->set_prop($prop, is_array($val) ? date_edit::get_timestamp($val) : $val);
						$save = true;
					}
					elseif($prop === "add_comment" and $val)
					{
						$co = obj();
						$co->set_class_id(CL_COMMENT);
						$co->set_name(sprintf(t("%s kommentaar"), $o->name()));
						$co->set_parent($o->id());
						$co->set_prop("commtext", $val);
						$co->save();
						$o->connect(array(
							"type" => "RELTYPE_COMMENT",
							"to" => $co->id(),
						));
					}
				}
				if($save)
				{
					$o->save();
				}
			}
		}
	}

	private function _init_purchase_orders_tbl($t, $arr)
	{
		$t->define_field(array(
			"name" => "nr",
			"caption" => t("NR"),
			"sortable" => 1,
			"align" => "center",
			"chgbgcolor" => "color",
			"colspan" => "colspan",
		));
		$t->define_field(array(
			"name" => "channel",
			"caption" => t("M&uuml;&uuml;gikanal"),
			"sortable" => 1,
			"align" => "center",
			"chgbgcolor" => "color",
			"colspan" => "colspan",
		));


		$t->define_field(array(
			"sortable" => 1,
			"name" => "status",
			"caption" => t("Staatus"),
			"align" => "center",
			"chgbgcolor" => "color",
		));


		$t->define_field(array(
			"name" => "ids",
			"caption" => t("Tellimuse number"),
			"sortable" => 1,
			"align" => "center",
			"chgbgcolor" => "color",
			"colspan" => "colspan",
		));

		$t->define_field(array(
			"name" => "oid",

			"caption" => t("AW ID"),
			"sortable" => 1,
			"align" => "center",
			"chgbgcolor" => "color",
			"colspan" => "colspan",
			"parent" => "ids"
		));

		$t->define_field(array(
			"name" => "number",
			"caption" => t("Naabri ID"),
			"sortable" => 1,
			"align" => "center",
			"chgbgcolor" => "color",
			"colspan" => "colspan",
			"parent" => "ids"
		));

		$t->define_field(array(
			"name" => "orderer",
			"caption" => t("Tellija"),
			"align" => "center",
			"chgbgcolor" => "color",
		));
		$t->define_field(array(
			"name" => "purchaser",
			"caption" => t("Isik"),
			"sortable" => 1,
			"align" => "center",
			"chgbgcolor" => "color",
			"parent" => "orderer",
		));
/*		$t->define_field(array(
			"name" => "purchaser_id",
			"caption" => html::href(array("url" => "#" , "title" => t("AutomatWebi poolne kliendikood") , "caption" => t("AW KK"))),
			"sortable" => 1,
			"align" => "center",
			"chgbgcolor" => "color",
			"parent" => "orderer",
		));
 */
		$t->define_field(array(
			"name" => "purchaser_other_id",
			"caption" => html::href(array("url" => "#" , "title" => t("Naabers&uuml;steemi kliendikood") , "caption" => t("Naabri KK"))),
			"sortable" => 1,
			"align" => "center",
			"chgbgcolor" => "color",
			"parent" => "orderer",
		));



		$t->define_field(array(
			"sortable" => 1,
			"name" => "date",
			"caption" => t("Kuup&auml;ev"),
			"align" => "center",
			"type" => "time",
			"format" => "d.m.Y H:i",
			"chgbgcolor" => "color",
		));


		$t->define_field(array(
			"sortable" => 1,
			"name" => "sum",
			"caption" => t("Summa"),
			"align" => "center",
			"chgbgcolor" => "color",
		));


		if(!empty($arr["extra"]))
		{
			$t->define_field(array(
				"sortable" => 1,
				"name" => "start_date",
				"caption" => t("Algus"),
				"align" => "center",
				"chgbgcolor" => "color",
			));

			$t->define_field(array(
				"sortable" => 1,
				"name" => "case",
				"caption" => t("T&ouml;&ouml;"),
				"align" => "center",
				"chgbgcolor" => "color",
			));
		}
		$t->define_field(array(
			"sortable" => 1,
			"name" => "rels",
			"caption" => t("Seosed"),
			"align" => "center",
			"chgbgcolor" => "color",
		));

		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
			"chgbgcolor" => "color",
		));
	}

	function _get_sell_orders_toolbar(&$arr)
	{
		return $this->_get_purchase_orders_toolbar($arr);
	}

	function _get_sell_orders_tree($arr)
	{
		$this->_get_purchase_orders_tree($arr);
	}


	function _get_sell_orders(&$arr)
	{
		return $this->_get_purchase_orders($arr);
	}

	function _get_campaigns($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
			"align" => "left",
			"chgbgcolor" => "color",
			"colspan" => "colspan",
		));

		$t->define_field(array(
			"name" => "discount",
			"caption" => t("Allahindluse %"),
			"sortable" => 1,
			"align" => "center",
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "date",
			"caption" => t("Kestus"),
			"align" => "center",
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "active",
			"caption" => t("Kehtib"),
			"align" => "center",
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "product",
			"caption" => t("Toode/kategooria"),
			"align" => "center",
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "groups",
			"caption" => t("Kasutajaruppidele"),
			"align" => "center",
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "change",
			"caption" => t("Muuda"),
			"align" => "center",
			"chgbgcolor" => "color",
		));

		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
			"chgbgcolor" => "color",
		));

		$f = $this->get_range(isset($arr["request"]["timespan"]) ? $arr["request"]["timespan"] : null);
		if(isset($arr["request"]["user_group"]) and is_oid($arr["request"]["user_group"]))
		{
			$f["group"] = $arr["request"]["user_group"];
			$t->set_caption(sprintf(t("Valitud ajavahemikul grupile \"%s\" m&otilde;juvad kampaaniad"), get_name($f["group"])));
		}
		else
		{
			$t->set_caption("Valitud ajavahemikul toimuvad kampaaniad");
		}

		if(isset($arr["request"]["product"]) and is_oid($arr["request"]["product"]))
		{
			$f["object"] = $arr["request"]["user_group"];
		}

		$discounts = discount_obj::get_discounts($f);
		foreach($discounts as $id => $data)
		{
			$change = ($arr["request"]["change_discount_id"] == $id) ? 1 : 0;
			$def = array(
				"name" => $data["name"],
				"discount" => $data["discount"],
				"active" => $data["active"] ? t("Aktiivne") : t("Mitteaktiivne"),
				"oid" => $id,
			);
			if($data["from"] || $data["to"])
			{
				$def["date"] = "";
				if($data["from"] > 0)
				{
					$def["date"].= date("d.m.Y" , $data["from"]);
				}
				if($data["from"] > 0 and $data["to"] > 0)
				{
					$def["date"].= " - ";
				}
				if($data["to"] > 0)
				{
					$def["date"].= date("d.m.Y" , $data["to"]);
				}
			}
			$def["groups"] = is_array($group_names) ? join(", " , $group_names) : "";
			if(is_oid($data["object"]))
			{
				$def["product"] = get_name($data["object"]);
			}

			if($change)
			{
				$def["name"] = html::textbox(array("name" => "row[".$id."][name]" , "value" => $data["name"]));
				$def["discount"] = html::textbox(array("name" => "row[".$id."][discount]" , "value" => $data["discount"] , "size" => 2));
				$def["active"] = html::checkbox(array("name" => "row[".$id."][active]" , "value" => 1, "checked" => $data["active"]));
				$def["date"] = html::date_select(array("month_as_numbers" => 1,"name" => "row[".$id."][from]" , "value" => $data["from"])) . " -
					" . html::date_select(array("month_as_numbers" => 1,"name" => "row[".$id."][to]" , "value" => $data["to"]));
				$groups = new object_list(array(
					"class_id" => CL_GROUP,
				));
				$def["groups"] = html::select(array("name" => "row[".$id."][group]"  , "value" => array_keys($group_names) , "multiple" => 1, "size" => 4 , "options" => $groups->names()));
				$products = new object_list(array(
					"class_id" => array(CL_SHOP_PRODUCT , CL_SHOP_PRODUCT_PACKAGING, CL_SHOP_PRODUCT_CATEGORY)
				));

				$def["product"] = html::select(array("name" => "row[".$id."][product]", "value" => $data["object"], "options" => array("" => t("-- Vali --")) + $products->names()));

			}
			$group_names = get_name($data["apply_groups"]);
			if(!$change)
			{
				$def["change"] = html::button(array(
					"name" => "change_row",
					"value" => t("Muuda"),
					"onclick" => "javascript:reload_layout('campaigns_right', {change_discount_id: '".$id."'});"
				));
			}
			else
			{
				$def["change"] = html::button(array(
					"name" => "change_row",
					"value" => t("Salvesta"),
					"onclick" => "
					$.post('/automatweb/orb.aw?class=shop_warehouse&action=post_campaign_row', {
						discount: document.getElementsByName('row[".$id."][discount]')[0].value
						, id: ".$id."
						, name: document.getElementsByName('row[".$id."][name]')[0].value
						, active: document.getElementsByName('row[".$id."][active]')[0].value
						, from: document.getElementsByName('row[".$id."][from]')[0].value
						, to: document.getElementsByName('row[".$id."][to]')[0].value
						//, group: document.getElementsByName('row[".$id."][group]')[0].value
						, product: document.getElementsByName('row[".$id."][product]')[0].value
						},function(data){reload_layout('campaigns_right',{change_discount_id: ''});
					});
				",
				));
			}
			$t->define_data($def);
		}
	}

	function _get_campaigns_toolbar($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_delete_button();
	}


	function _get_campaigns_product_tree($arr)
	{
		$tv =& $arr["prop"]["vcl_inst"];
		$var = "product";
		$tv->set_selected_item(isset($arr["request"][$var]) ? $arr["request"][$var] : "all_products");

		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"persist_state" => true,
			"tree_id" => "discounts_product_tree",
		));

		$tv->add_item(0,array(
			"name" => t("K&otilde;ik tooted"),
			"id" => "all_products",
			"reload" => array(
				"props" => array("campaigns"),
				"params" => array($var => "all")
			)
		));

		$groups = new object_list(array(
			"class_id" => CL_SHOP_PRODUCT_CATEGORY,
//			"type" => new obj_predicate_not(1)
		));

		foreach($groups->arr() as $id => $o)
		{
			if($o->prop("parent.class_id") == CL_SHOP_PRODUCT_CATEGORY)
			{
				continue;
			}
			$tv->add_item("all_products", array(
				"id" => $id,
				"name" => $o->name(),
				"iconurl" => icons::get_icon_url(CL_MENU),
				"reload" => array(
					"props" => array("campaigns"),
					"params" => array($var => $id)
				)
			));
			$this->add_discount_product_leaf($tv , $id);
		}
	}

	function add_discount_product_leaf($tv , $parent)
	{
		$groups = new object_list(array(
			"class_id" => array(CL_SHOP_PRODUCT_CATEGORY),
//			"type" => new obj_predicate_not(1),
			"parent" => $parent
		));
		foreach($groups->arr() as $id => $o)
		{
			$tv->add_item($parent, array(
				"id" => $id,
				"name" => $o->name(),
				"iconurl" => icons::get_icon_url(CL_MENU),
				"reload" => array(
					"props" => array("campaigns"),
					"params" => array("product" => $id)
				)
			));
			$this->add_discount_product_leaf($tv , $id);
		}

		$groups = new object_list(array(
			"class_id" => array(CL_SHOP_PRODUCT),
//			"type" => new obj_predicate_not(1),
			"CL_SHOP_PRODUCT.RELTYPE_CATEGORY" => $parent
		));
		foreach($groups->arr() as $id => $o)
		{
			if($o->class_id() == CL_SHOP_PRODUCT)
			{
				$tv->add_item($parent, array(
					"id" => $id,
					"name" => $o->name(),
					"iconurl" => icons::get_icon_url(CL_SHOP_PRODUCT),
					"reload" => array(
						"props" => array("campaigns"),
						"params" => array("product" => $id)
					)
				));
			}
		}
	}

	function _get_campaigns_groups_tree($arr)
	{
		$tv =& $arr["prop"]["vcl_inst"];
		$var = "user_group";
		$tv->set_selected_item(isset($arr["request"][$var]) ? $arr["request"][$var] : "all");

		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"persist_state" => true,
			"tree_id" => "proj_bills_time_tree",
		));

		$tv->add_item(0,array(
			"name" => t("K&otilde;ik grupid"),
			"id" => "all",
			"reload" => array(
				"props" => array("campaigns"),
				"params" => array($var => "all")
			)
//			"url" => aw_url_change_var($var, "all"),
		));

		$groups = new object_list(array("class_id" => CL_GROUP , "type" => new obj_predicate_not(1)));

		foreach($groups->arr() as $id => $o)
		{
			if($o->prop("parent.class_id") == CL_GROUP)
			{
				continue;
			}
			$tv->add_item("all", array(
				"id" => $id,
				"name" => $o->name(),
				"iconurl" => icons::get_icon_url(CL_GROUP),
				"reload" => array(
					"props" => array("campaigns"),
					"params" => array($var => $id)
				)
			));
			$this->add_group_leaf($tv , $id);
		}
	}

	function add_group_leaf($tv , $parent)
	{
		$groups = new object_list(array("class_id" => CL_GROUP , "type" => new obj_predicate_not(1) , "parent" => $parent));
		foreach($groups->arr() as $id => $o)
		{
			$tv->add_item($parent, array(
				"id" => $id,
				"name" => $o->name(),
				"iconurl" => icons::get_icon_url(CL_GROUP),
				"reload" => array(
					"props" => array("campaigns"),
					"params" => array("user_group" => $id)
				)
			));
			$this->add_group_leaf($tv , $id);
		}
	}

	function _get_campaigns_time_tree($arr)
	{
		$tv =& $arr["prop"]["vcl_inst"];
		$var = "timespan";
		$tv->set_selected_item(isset($arr["request"][$var]) ? $arr["request"][$var] : "period_week");

		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"persist_state" => true,
			"tree_id" => "proj_bills_time_tree",
		));

		$tv->add_item(0,array(
			"name" => t("K&otilde;ik ajavahemikud"),
			"id" => "all_time",
				"reload" => array(
					"props" => array("campaigns"),
					"params" => array("timespan" => "all_time")
				)
		));

		$branches = array(
			"period_last_week" => t("Eelmine n&auml;dal"),
			"period_week" => t("K&auml;esolev n&auml;dal"),
			"period_last_last" => t("&Uuml;leelmine kuu"),
			"period_last" => t("Eelmine kuu"),
			"period_current" => t("K&auml;esolev kuu"),
			"period_next" => t("J&auml;rgmine kuu"),
			"period_lastyear" => t("Eelmine aasta"),
			"period_year" => t("K&auml;esolev aasta"),
		);

		foreach($branches as $id => $caption)
		{
			$tv->add_item("all_time", array(
				"id" => $id,
				"name" => $caption,
				"reload" => array(
					"props" => array("campaigns"),
					"params" => array("timespan" => $id)
				)
			));
		}
	}


	function get_cat_picker($arr)
	{
		$ol = new object_list(array(
			"parent" => $this->get_warehouse_configs($arr, "prod_type_fld"),
			"class_id" => CL_SHOP_PRODUCT_CATEGORY,
		));
		$this->search_folders = array(0 => t("--vali--"));
		foreach($ol->arr() as $o)
		{
			$flevel = 0;
			$this->get_cat_picker_recur($o, $flevel);
		}
		return $this->search_folders;
	}

	private function get_cat_picker_recur($o, $flevel)
	{
		$slashes = "";
		for($i=0;$i<$flevel;$i++)
		{
			$slashes .= "--";
		}
		$nflevel = $flevel+1;
		$this->search_folders[$o->id()] = $slashes.$o->name();

		$ol = new object_list(array(
			"parent" => $o->id(),
			"class_id" => CL_SHOP_PRODUCT_CATEGORY,
		));
		foreach($ol->arr() as $o)
		{
			$this->get_cat_picker_recur($o, $nflevel);
		}
	}

	function get_pricelist_picker()
	{
		$ol = new object_list(array(
			"class_id" => CL_SHOP_PRICE_LIST,
		));
		return array(0 => t("--vali--")) + $ol->names();
	}

	protected function init_csv_export($t)
	{
		$t->add_fields(array(
			"order.oid" => t("Meie tellimuse id"),
			"order.number" => t("Naabri tellimuse id"),
		));
		$t->define_field(array(
			"name" => "order.date",
			"caption" => t("Kuup&auml;ev"),
			"type" => "time",
			"format" => "d.m.Y H:i",
		));
		$t->add_fields(array(
			"site_baseurl" => t("Domeen"),
			"order.channel.name" => t("M&uuml;&uuml;gikanal"),
			"order.payment_type" => t("Makseviis"),
			"order.deferred_payment_count" => t("J&auml;relmaksu osamaksete arv"),
			"order.shop_delivery_type" => t("K&auml;ttetoimetamise viis"),
			"order.smartpost_sell_place_name" => t("Asukoht"),
			"order.order_data.address" => t("Aadress"),
			"order.order_data.index" => t("Postiindeks"),
			"order.order_data.city" => t("Linn"),

			"order.purchaser" => t("Meie kliendikood"),
			"order.purchaser.external_id" => t("Naabri kliendikood"),
			"order.purchaser.name" => t("Nimi"),
			"order.purchaser.firstname" => t("Eesnimi"),
			"order.purchaser.lastname" => t("Perekonnanimi"),
			"order.purchaser.personal_id" => t("Isikukood"),
			"order.purchaser.birthday" => t("S&uuml;nnip&auml;ev"),
			"order.order_data.email" => t("E-post"),
			"order.order_data.mobilephone" => t("Mobiiltelefon"),
			"order.order_data.homephone" => t("Lauatelefon"),
			"order.order_data.work" => t("T&ouml;&ouml;koht"),
			"order.order_data.workexperience" => t("T&ouml;&ouml;staa&#0158;"),
			"order.order_data.wage" => t("T&ouml;&ouml;tasu"),
			"order.order_data.profession" => t("Amet"),
			"order.order_status" => t("Tellimuse staatus"),

			/*
			code" => $row->meta("product_code") => t(),
			"size" => $row->meta("product_size") => t(),
			"color" => $row->meta("product_color") => t(),
			"name" => $row->prop("prod_name") => t(),
			*//*
			"product.name" => t("Toote nimi"),
			"packaging.name" => t("Pakendi nimi"),
			"packaging.size" => t("Suurus"),
			"product.code" => t("Kood"),
			"packet.description" => t("Kirjeldus"),
			"packaging.price_object.name" => t("Hind"),
			"packaging.special_price_object.name" => t("Soodushind"),
			"price" => t("Tellimise hind"),
			"product.color" => t("V&auml;rvus"),
//			"" => t("Br&auml;nd"),
			*/

			"product.name" => t("Toote nimi"),
			"product.size" => t("Suurus"),
			"product.code" => t("Tootenumber"),
			"amount" => t("Kogus"),
			"price" => t("Hind"),
			"order.currency" => t("Valuuta"),
		));
	}

	/**
		@attrib name=csv_export all_args=1
		@param id required type=int acl=view
		@param sel optional type=array
		@param filt_time optional type=array
	**/
	public function csv_export($arr)
	{
		$o = obj($arr["id"]);
		$order_instance = get_instance(CL_SHOP_PURCHASE_ORDER);

		$odl = $this->_get_orders_odl(
			array(
				"obj_inst" => $o,
				"request" => $arr
			),
			array("payment_type", "deferred_payment_count", "order_status", "smartpost_sell_place_name", "delivery_address.name", "shop_delivery_type.name", /* Now the worst part! -> */ "metadata")
		);
		$payment_types_ol = count($payment_types_ids = $odl->get_element_from_all("payment_type")) ? new object_list(array(
			"class_id" => CL_SHOP_PAYMENT_TYPE,
			"oid" => $payment_types_ids,
		)) : new object_list();
		$payment_types = $payment_types_ol->names();
		$orders_data = $odl->arr();
		$orders_rows = shop_sell_order_obj::get_rows_by_ids(array_keys($orders_data));
		$purchaser_data = shop_sell_order_obj::get_purchaser_data_by_ids($odl->get_element_from_all("purchaser"))->arr();

		$data = array();
		foreach($orders_rows as $order_oid => $order_rows)
		{
			foreach($order_rows as $order_row)
			{
				$data_row = $order_row;
				//	Following row should read it's content by site_id
				$data_row["site_baseurl"] = aw_ini_get("baseurl");
				$data_row["prod_name"] = str_replace(array("\n", "\r\n", "\n\r"), " ", $data_row["prod_name"]);

				foreach($orders_data[$order_oid] as $order_data_key => $order_data_value)
				{
					if($order_data_key === "metadata")
					{
						$order_data_value = aw_unserialize($order_data_value);
						foreach($order_data_value["order_data"] as $meta_order_data_key => $meta_order_data_value)
						{
							$data_row["order.order_data.".$meta_order_data_key] = $meta_order_data_value;
						}
					}
					else
					{
						$data_row["order.".$order_data_key] = $order_data_value;
					}
				}
				foreach($purchaser_data[$orders_data[$order_oid]["purchaser"]] as $purchaser_data_key => $purchaser_data_value)
				{
					$data_row["order.purchaser.".$purchaser_data_key] = $purchaser_data_value;
				}

				$data[] = $data_row;
			}
		}

		$t = new vcl_table();
		$this->init_csv_export($t);
		foreach($data as $odata)
		{
			$product_name = isset($odata["meta"]["name"]) ? $odata["meta"]["name"] : (strlen(trim($odata["prod(CL_SHOP_PRODUCT_PACKAGING).product(CL_SHOP_PRODUCT).name"])) > 0 ? trim($odata["prod(CL_SHOP_PRODUCT_PACKAGING).product(CL_SHOP_PRODUCT).name"]) : trim($odata["prod(CL_SHOP_PRODUCT).name"]));

			$product_size = isset($odata["meta"]["product_size"]) ? $odata["meta"]["product_size"] : $odata["prod(CL_SHOP_PRODUCT_PACKAGING).size"];

			$product_code = isset($odata["meta"]["product_code"]) ? $odata["meta"]["product_code"] : trim($odata["prod(CL_SHOP_PRODUCT_PACKAGING).product(CL_SHOP_PRODUCT).code"]);

			$table_data = array(
				"order.oid" => $odata["order.oid"],
				"order.number" => $odata["order.number"],
				"order.date" => $odata["order.date"],
				"site_baseurl" => $odata["site_baseurl"],
				"order.channel.name" => $odata["order.channel.name"],
				"order.payment_type" => $payment_types[$odata["order.payment_type"]],
				"order.deferred_payment_count" => $odata["order.deferred_payment_count"],
				"order.shop_delivery_type" => $odata["order.shop_delivery_type.name"],
				"order.delivery_address" => $odata["order.delivery_address.name"],
				"order.smartpost_sell_place_name" => $odata["order.smartpost_sell_place_name"],

				"order.purchaser" => $odata["order.purchaser"],
				"order.purchaser.external_id" => $odata["order.purchaser.external_id"],
				"order.purchaser.name" => $odata["order.purchaser.name"],
				"order.purchaser.firstname" => $odata["order.purchaser.firstname"],
				"order.purchaser.lastname" => $odata["order.purchaser.lastname"],
				"order.purchaser.personal_id" => $odata["order.purchaser.personal_id"],
				"order.purchaser.birthday" => !empty($odata["order.purchaser.birthday"]) ? date("d-m-Y", $odata["order.purchaser.birthday"]) : "",
				"order.order_status" => $order_instance->states[$odata["order.order_status"]],

				"order.order_data.work" => $odata["order.order_data.work"],
				"order.order_data.workexperience" => $odata["order.order_data.workexperience"],
				"order.order_data.wage" => $odata["order.order_data.wage"],
				"order.order_data.profession" => $odata["order.order_data.profession"],
				"order.order_data.address" => $odata["order.order_data.address"],
				"order.order_data.index" => $odata["order.order_data.index"],
				"order.order_data.city" => $odata["order.order_data.city"],
				"order.order_data.email" => $odata["order.order_data.email"],
				"order.order_data.mobilephone" => $odata["order.order_data.mobilephone"],
				"order.order_data.homephone" => $odata["order.order_data.homephone"],

				"prod_name" => $odata["prod_name"],
				"product.name" => $product_name,
				"product.size" => $product_size,
				"product.code" => $product_code,
				"amount" => $odata["amount"],
				"price" => $odata["price"],
				"order.currency" => obj($odata["order.currency"])->name(),
			);
			$t->define_data($table_data);
		}
		/*
		$count = 0;
		$total_sum = 0;
		$customer_relation_ids = array();
		if($this->can("view", $arr["obj_inst"]->prop("conf.owner")))
		{
			$owner = obj($arr["obj_inst"]->prop("conf.owner"));
			$customer_relation_ids = shop_sell_order_obj::get_customer_relation_ids_for_purchasers($odl->get_element_from_all("purchaser"), $owner, true);
		}
		$sums_for_orders = shop_sell_order_obj::get_sums_by_ids($odl->ids());
		$external_ids = crm_person_obj::get_external_ids_for_person_ids($odl->ids());
		*/

		$group = $this->get_search_group(array("request" => $arr));
		$timestamp = $this->_get_status_orders_time_filt(array("request" => $arr));
		$filename = sprintf("orders - %s - %s - %s.csv",
			aw_ini_get("baseurl"),
			date("d.m.Y", isset($arr["filt_time"]) ? $timestamp["filt_start"] : date_edit::get_timestamp($arr[$group."_s_from"])),
			date("d.m.Y", isset($arr["filt_time"]) ? $timestamp["filt_end"] : date_edit::get_timestamp($arr[$group."_s_to"]))
		);

		header("Content-type: application/csv");
		header("Content-disposition: inline; filename=\"{$filename}\";");

		$encoding = "ISO-8859-1";
		if(strlen(trim($o->prop("conf.csv_file_encoding"))) > 0)
		{
			$encoding = $o->prop("conf.csv_file_encoding");
		}

		//	Excel won't display data in UTF-8 correctly. At least not by default. Hence iconv(); -kaarel 7.04.2010
		die(iconv(aw_global_get("charset"), $encoding."//IGNORE", aw_html_entity_decode($t->get_csv_file())));
	}

	/**
	@attrib name=get_status_orders_time_tree_level all_args=1
	**/
	function get_status_orders_time_tree_level($arr)
	{
		$t = get_instance("vcl/treeview");
		$parent = trim($arr["parent"]);
		$tmp = explode("_", $parent);
		$t->start_tree(array (
			"type" => TREE_DHTML,
			"branch" => 1,
			"tree_id" => "prodg_tree_s",
			"persist_state" => 1,
		));
		if(isset($arr["end"]))
		{
			$end = $arr["end"];
			$start = $arr["start"];
		}
		else
		{
			$end = $this->_get_status_orders_time_tree_end(obj($arr["id"]));
		}
		if($end)
		{
			switch($tmp[0])
			{
				case "year":
					if($start and date('Y', $start) == $tmp[1])
					{
						$start = date('m', mktime(0,0,0, date('m', $start),1,$tmp[1]));
					}
					elseif(date('Y') == $tmp[1] and !$start)
					{
						$start = date('m', mktime(0,0,0,date('m'),1,$tmp[1]));
					}
					else
					{
						$start = 1;
					}
					if(date('Y', $end) == $tmp[1])
					{
						$end = date('m', mktime(0,0,0,date('m', $end),1,$tmp[1]));
					}
					else
					{
						$end = 12;
					}
					for($i = $start; $i <= $end; $i++)
					{
						$t->add_item(0, array(
							"name" => sprintf("%s %s", locale::get_lc_month($i), $tmp[1]),
							"id" => "month_".$i."_".$tmp[1],
							"iconurl" => icons::get_icon_url(CL_MENU),
							"url" => "#",
						));
						$t->add_item("month_".$i."_".$tmp[1], array());
					}
					break;
				case "month":
					if($start and date('m.Y', $start) == $tmp[1].".".$tmp[2])
					{
						$start = date('d', mktime(0,0,1,date('m', $start), date('d', $start),$tmp[2]));
					}
					elseif(date('m.Y') == $tmp[1].".".$tmp[2] and !$start)
					{
						$start = date('d', mktime(0,0,1,date('m'),date('d'),$tmp[2]));
					}
					else
					{
						$start = 1;
					}
					if(date('m.Y', $end) == $tmp[1].".".$tmp[2])
					{
						$end = date('d', mktime(0,0,1,date('m', $end),date('d', $end),$tmp[2]));
					}
					else
					{
						$end = date('d', mktime(0,0,1,$tmp[1]+1,0,$tmp[2]));
					}
					$g = $this->get_search_group(array(
						"request" => $arr,
					));
					for($i = $start; $i <= $end; $i++)
					{
						switch($g)
						{
							case "purchase_orders":
							case "sell_orders":
								$arr["filt_time"] = mktime(0,0,0,$tmp[1],$i,$tmp[2]);
								$ol = $this->_get_orders_ol(array(
									"request" => $arr,
									"obj_inst" => obj($arr["id"]),
								));
								$count = $ol->count();
								break;
							case "purchase_notes":
							case "purchase_bills":
							case "sales_notes":
							case "sales_bills":
								$arr["filt_time"] = mktime(0,0,0,$tmp[1],$i,$tmp[2]);
								$ol = $this->_get_storage_ol(array(
									"request" => $arr,
									"warehouses" => array($arr["id"]),
									"obj_inst" => obj($arr["id"]),
								));
								$count = $ol->count();
								break;
						}
						$t->add_item(0, array(
							"name" => sprintf("%s %s %s %s", $i, locale::get_lc_month($tmp[1]), $tmp[2], isset($count) ? "(".$count.")" : ""),
							"id" => "day_".$i."_".$tmp[1]."_".$tmp[2],
							"iconurl" => icons::get_icon_url(CL_MENU),
							"url" => aw_url_change_var(array("filt_time" => mktime(0,0,0,$tmp[1],$i,$tmp[2])), false, $arr["set_retu"]),
						));
					}
					break;
			}
		}
		$f = automatweb::$request->arg("filt_time");
		if(is_numeric($f))
		{
			$t->set_selected_item("day_".((int)date('d', $f))."_".((int)date('m', $f))."_".date('Y', $f));
		}
		die($t->finalize_tree());
	}

	function _get_status_orders_time_tree(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$params = array(
			"set_retu" => get_ru(),
			"filt_time" => automatweb::$request->arg("filt_time"),
			"id" => $arr["obj_inst"]->id(),
			"group" => $arr["request"]["group"],
		);
		$g = $this->get_search_group($arr);
		foreach($arr["request"] as $var => $val)
		{
			if($this->is_search_param($var))
			{
				$params[$var] = $val;
			}
		}

		if(!empty($arr["request"]["filt_cust"]))
		{
			$params["filt_cust"] = $arr["request"]["filt_cust"];
		}
		if(!empty($arr["request"]["pgtf"]))
		{
			$params["pgtf"] = $arr["request"]["pgtf"];
		}
		if(!empty($arr["start"]))
		{
			$params["start"] = $arr["start"];
			$params["end"] = $arr["end"];
		}
		$params["parent"] = " ";
		$gbf = $this->mk_my_orb("get_status_orders_time_tree_level", $params, CL_SHOP_WAREHOUSE);

		$t->start_tree(array(
			"has_root" => true,
			"root_name" => t("Ajavahemikud"),
			"root_url" => aw_url_change_var(array("time"=> null)),
			"root_icon" => icons::get_icon_url(CL_MENU),
			"type" => TREE_DHTML,
			"tree_id" => "status_orders_time_tree",
			"persist_state" => 1,
			"get_branch_func" => $gbf,
		));
	//v6tsin "all" maha siit et see jama kiiremini liiguks
		foreach(array("", "yesterday" , "today" , "tomorrow" , "lastweek" , "thisweek" , "nextweek", "lastmonth" , "thismonth", "nextmonth", "all") as $id => $val)
		{
			switch($g)
			{
				case "purchase_orders":
				case "sell_orders":
					$arr["request"]["filt_time"] = $val;
//					$ol = $this->_get_orders_ol($arr);
//					${"count".$id} = $ol->count();
//					${"count".$id} = " ";//$ol->count();
					${"count".$id} = $this->get_order_cnt($val);
					break;
				case "purchase_notes":
				case "purchase_bills":
				case "sales_notes":
				case "sales_bills":
					$arr["request"]["filt_time"] = $val;
					$arr["warehouses"] = array($arr["obj_inst"]->id());
					$ol = $this->_get_storage_ol($arr);
					${"count".$id} = $ol->count();
					break;
			}
		}


		if(!empty($count1))$t->add_item(0, array(
			"name" => sprintf("%s %s", t("Eile"), isset($count1) ? "(".$count1.")" : ""),
			"id" => "yesterday",
			"iconurl" => icons::get_icon_url(CL_MENU),
			"url" => aw_url_change_var("filt_time", "yesterday"),
		));

		if(!empty($count2))$t->add_item(0, array(
			"name" => sprintf("%s %s", t("T&auml;na"), isset($count2) ? "(".$count2.")" : ""),
			"id" => "today",
			"iconurl" => icons::get_icon_url(CL_MENU),
			"url" => aw_url_change_var("filt_time", "today"),
		));

		if(!in_array($g, array("sell_orders")))
		{
			if(!empty($count3))$t->add_item(0, array(
				"name" => sprintf("%s %s", t("Homme"), isset($count3) ? "(".$count3.")" : ""),
				"id" => "tomorrow",
				"iconurl" => icons::get_icon_url(CL_MENU),
				"url" => aw_url_change_var("filt_time", "tomorrow"),
			));
		}

		if(!empty($count4))$t->add_item(0, array(
			"name" => sprintf("%s %s", t("Eelmine n&auml;dal"), isset($count4) ? "(".$count4.")" : ""),
			"id" => "lastweek",
			"iconurl" => icons::get_icon_url(CL_MENU),
			"url" => aw_url_change_var("filt_time", "lastweek"),
		));

		if(!empty($count5))$t->add_item(0, array(
			"name" => sprintf("%s %s", t("K&auml;esolev n&auml;dal"), isset($count5) ? "(".$count5.")" : ""),
			"id" => "thisweek",
			"iconurl" => icons::get_icon_url(CL_MENU),
			"url" => aw_url_change_var("filt_time", "thisweek"),
		));

		if(!in_array($g, array("sell_orders")))
		{
			if(!empty($count6))$t->add_item(0, array(
				"name" => sprintf("%s %s", t("J&auml;rgmine n&auml;dal"), isset($count6) ? "(".$count6.")" : ""),
				"id" => "nextweek",
				"iconurl" => icons::get_icon_url(CL_MENU),
				"url" => aw_url_change_var("filt_time", "nextweek"),
			));
		}
		if(!empty($count7))$t->add_item(0, array(
			"name" => sprintf("%s %s", t("Eelmine kuu"), isset($count7) ? "(".$count7.")" : ""),
			"id" => "lastmonth",
			"iconurl" => icons::get_icon_url(CL_MENU),
			"url" => aw_url_change_var("filt_time", "lastmonth"),
		));
		if(!empty($count8))$t->add_item(0, array(
			"name" => sprintf("%s %s", t("K&auml;esolev kuu"), isset($count8) ? "(".$count8.")" : ""),
			"id" => "thismonth",
			"iconurl" => icons::get_icon_url(CL_MENU),
			"url" => aw_url_change_var("filt_time", "thismonth"),
		));

		if(!in_array($g, array("sell_orders")))
		{
			if(!empty($count9))$t->add_item(0, array(
				"name" => sprintf("%s %s", t("J&auml;rgmine kuu"), isset($count9) ? "(".$count9.")" : ""),
				"id" => "nextmonth",
				"iconurl" => icons::get_icon_url(CL_MENU),
				"url" => aw_url_change_var("filt_time", "nextmonth"),
			));
		}

		if(isset($arr["end"]))
		{
			$end = $arr["end"];
			$start = $arr["start"];
		}
		else
		{
			$end = $this->_get_status_orders_time_tree_end($arr["obj_inst"]);
		}
		if(!empty($end))
		{
			if(empty($start))
			{
				$start = time();
			}
			for($i = date('Y', $start); $i <= date('Y', $end); $i++)
			{
				$t->add_item(0, array(
					"name" => $i,
					"id" => "year_".$i,
					"url" => "#",
					"iconurl" => icons::get_icon_url(CL_MENU),
				));
				$t->add_item("year_".$i, array(
					"name" => "tmp",
					"id" => "year_".$i."_tmp",
				));
			}
		}

		if(!empty($arr["all"]))
		{
			$t->add_item(0, array(
				"name" => sprintf("%s %s", t("K&otilde;ik"), isset($count10) ? "(".$count10.")" : ""),
				"id" => "all",
				"iconurl" => icons::get_icon_url(CL_MENU),
				"url" => aw_url_change_var(array("filt_time" => "all", $g."_s_from" => "-", $g."_s_to" => "-")),
			));
		}

		if(empty($arr["request"]["status_orders_s_start"]))
		{
			$t->set_selected_item(($f = automatweb::$request->arg("filt_time")) ? $f : "today");
		}
	}

	protected function get_order_cnt($val)
	{
		$timestamp = $this->_get_status_orders_time_filt(array(
			"request" => array(
				"filt_time" => $val,
			)
		));
		$odl = new object_data_list(
			array(
				"class_id" => CL_SHOP_SELL_ORDER,
				"date" => new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $timestamp["filt_start"], $timestamp["filt_end"])
			),
			array(
				CL_SHOP_SELL_ORDER => array(new obj_sql_func(OBJ_SQL_COUNT, "count" , "*"))
			)
		);
		$cnt = $odl->arr();
		return $cnt[0]["count"];
	}

	private function _get_status_orders_time_tree_end($o)
	{
		$days = $start = 0;
		$ol = new object_list(array(
			"class_id" => CL_MRP_JOB,
			"RELTYPE_JOB(CL_MATERIAL_EXPENSE).class_id" => CL_MATERIAL_EXPENSE,
			"RELTYPE_MRP_PROJECT.workspace" => $o->prop("mrp_workspace"),
			"starttime" => new obj_predicate_compare(OBJ_COMP_GREATER, 0),
			"sort_by" => "mrp_schedule.starttime desc",
			"limit" => "0,1",
		));
		$o = $ol->begin();
		if($o)
		{
			$start = $o->prop("starttime");
			/*
			$ol2 = new object_list(array(
				"class_id" => CL_SHOP_PRODUCT_PURVEYANCE,
				"RELTYPE_PRODUCT.RELTYPE_PRODUCT(CL_MATERIAL_EXPENSE).RELTYPE_JOB" => $o->id(),
				"days" => new obj_predicate_compare(OBJ_COMP_GREATER, 0),
				"sort_by" => "days desc",
				"limit" => "0,1",
			));
			$o2 = $ol2->begin();*/
			$days = 100;
		}
		return $start + 24 * 60 * 60 * $days;
	}

	function _get_status_orders_case_tree($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->start_tree(array(
			"has_root" => false,
			"type" => TREE_DHTML,
			"tree_id" => "status_orders_case_tree",
			"persist_state" => 1,
		));
		$job_ol = $this->_get_jobs_for_time($arr);
		$ol = new object_list(array(
			"class_id" => CL_MRP_CASE,
			"RELTYPE_MRP_PROJECT(CL_MRP_JOB).oid" => $job_ol->count() ? $job_ol->ids() : -1,
		));
		foreach($ol->arr() as $oid => $o)
		{
			$t->add_item(0, array(
				"name" => $o->name(),
				"url" => aw_url_change_var("filt_case", $oid),
				"iconurl" => icons::get_icon_url(CL_MRP_CASE),
				"id" => "case".$oid,
			));
		}
		$t->add_item(0, array(
			"name" => t("K&otilde;ik"),
			"url" => aw_url_change_var("filt_case", ""),
			"iconurl" => icons::get_icon_url(CL_MRP_CASE),
			"id" => "case_all",
		));
		if(empty($arr["request"]["status_orders_s_case_no"]))
		{
			$t->set_selected_item(($f = automatweb::$request->arg("filt_case")) ? "case".$f : "case_all");
		}
	}

	function _get_status_orders_prod_tree($arr)
	{
		$this->mk_prodg_tree($arr);
		$arr["prop"]["vcl_inst"]->add_item(0, array(
			"name" => t("K&otilde;ik"),
			"id" => "prod_all",
			"url" => aw_url_change_var(array("pgtf" => "", "status_orders_s_art_cat" => null)),
			"iconurl" => icons::get_icon_url(CL_MENU),
		));
		$arr["prop"]["vcl_inst"]->set_selected_item(($f = automatweb::$request->arg("pgtf")) ? "case".$f : "prod_all");
	}

	function _get_status_orders_opt1($arr)
	{
		$arr["prop"]["options"] = array(
			"bron" => t("On broneeritud antud ajavahemikuks")."<br />",
			"order" => t("Tuleb tellida antud ajavahemikus")."<br /><br />",
		);
		$arr["prop"]["value"] = !empty($arr["request"][$arr["prop"]["name"]]) ? $arr["request"][$arr["prop"]["name"]] : "bron";
	}

	function _get_status_orders_s_start(&$arr)
	{
		$times = $this->_get_status_orders_time_filt($arr);
		if(empty($arr["request"][$arr["prop"]["name"]]))
		{
			$arr["prop"]["value"] = $times["filt_start"];
		}
		else
		{
			$arr["prop"]["value"] = date_edit::get_timestamp($arr["request"][$arr["prop"]["name"]]);
		}
		$arr["prop"]["format"] = array("day_textbox", "month_textbox", "year_textbox");
	}

	function _get_status_orders_s_end(&$arr)
	{
		$times = $this->_get_status_orders_time_filt($arr);
		if(empty($arr["request"][$arr["prop"]["name"]]))
		{
			$arr["prop"]["value"] = $times["filt_end"];
		}
		else
		{
			$arr["prop"]["value"] = date_edit::get_timestamp($arr["request"][$arr["prop"]["name"]]);
		}
		$arr["prop"]["format"] = array("day_textbox", "month_textbox", "year_textbox");
	}

	function _get_status_orders_s_case_no($arr)
	{
		if(isset($arr["request"]["filt_case"]) && is_oid($arr["request"]["filt_case"]))
		{
			$arr["prop"]["value"] = obj($arr["request"]["filt_case"])->prop("name");
		}
	}

	function _get_status_orders_res_tree($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		if($ws = $arr["obj_inst"]->prop("mrp_workspace"))
		{
			$t->start_tree(array(
				"type" => TREE_DHTML,
				"tree_id" => "status_orders_res_tree",
				"persist_state" => 1,
			));
			$pt = obj($ws)->prop ("resources_folder");
 			$t->add_item(0, array(
				"id" => "res".$pt,
				"name" => obj($pt)->name(),
				"iconurl" => icons::get_icon_url(CL_MENU),
				"url" => "#",
			));
			$ol = new object_list(array(
				"parent" => $pt,
				"class_id" => array(CL_MENU, CL_MRP_RESOURCE),
				"sort_by" => "objects.jrk",
			));
			foreach($ol->arr() as $oid => $o)
			{
				$t->add_item("res".$pt, array(
					"id" => "res".$oid,
					"name" => $o->name(),
					"iconurl" => icons::get_icon_url($o->class_id()),
					"url" => aw_url_change_var("filt_res", ($o->class_id() == CL_MRP_RESOURCE) ? $oid : null),
				));
				if($o->class_id() == CL_MENU)
				{
					$this->_insert_res_tree_level($t, $oid);
				}
			}
			$t->add_item(0, array(
				"id" => "filt_all",
				"name" => t("K&otilde;ik"),
				"url" => aw_url_change_var("filt_res", null),
				"iconurl" => icons::get_icon_url(CL_MRP_RESOURCE),
			));
			$t->set_selected_item(($f = automatweb::$request->arg("filt_res")) ? "res".$f : "filt_all");
		}
		else
		{
			return PROP_IGNORE;
		}
	}

	private function _insert_res_tree_level($t, $pt)
	{
		$ol = new object_list(array(
			"parent" => $pt,
			"class_id" => array(CL_MENU, CL_MRP_RESOURCE),
			"sort_by" => "objects.jrk",
		));
		foreach($ol->arr() as $oid => $o)
		{
			$t->add_item("res".$pt, array(
				"id" => $oid,
				"name" => $o->name(),
				"iconurl" => icons::get_icon_url($o->class_id()),
				"url" => aw_url_change_var("res_filt", ($o->class_id() == CL_MRP_RESOURCE) ? $oid : ""),
			));
			if($o->class_id() == CL_MENU)
			{
				$this->_insert_res_tree_level($t, $oid);
			}
		}
	}

	private function _get_status_orders_time_filt($arr)
	{
		if(!empty($arr["start"]) and !empty($arr["end"]))
		{
			return array(
				"filt_start" => date_edit::get_timestamp($arr["start"]),
				"filt_end" => date_edit::get_timestamp($arr["end"]),
			);
		}
		$time = empty($arr["request"]["filt_time"]) ? "" : $arr["request"]["filt_time"];
		switch($time)
		{
			case "yesterday":
				$filt_start = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));
				$filt_end = mktime(0, 0, -1, date('m'), date('d'), date('Y'));
				break;
			case "today":
				$filt_start = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
				$filt_end = mktime(0, 0, -1, date('m'), date('d')+1, date('Y'));
				break;
			case "tomorrow":
				$filt_start = mktime(0, 0, 0, date('m'), date('d')+1, date('Y'));
				$filt_end = mktime(0, 0, -1, date('m'), date('d')+2, date('Y'));
				break;
			case "lastweek":
				$filt_start = mktime(0, 0, 0, date('m'), date('d') - 6 - date('N'), date('Y'));
				$filt_end = mktime(0, 0, -1, date('m'), date('d') - date('N') + 1, date('Y'));
				break;
			case "thisweek":
				$filt_start = mktime(0, 0, 0, date('m'), date('d') - date('N') + 1, date('Y'));
				$filt_end = mktime(0, 0, -1, date('m'), date('d') + 8 - date('N'), date('Y'));
				break;
			case "nextweek":
				$filt_start = mktime(0, 0, 0, date('m'), date('d') + 8 - date('N'), date('Y'));
				$filt_end = mktime(0, 0, -1, date('m'), date('d') + 15 - date('N'), date('Y'));
				break;
			case "lastmonth":
				$filt_start = mktime(0,0,0,date('m')-1,1,date('Y'));
				$filt_end = mktime(0,0,-1,date('m'),1,date('Y'));
				break;
			case "thismonth":
				$filt_start = mktime(0,0,0,date('m'),1,date('Y'));
				$filt_end = mktime(0,0,-1,date('m')+1,1,date('Y'));
				break;
			case "nextmonth":
				$filt_start = mktime(0,0,0,date('m')+1,1,date('Y'));
				$filt_end = mktime(0,0,-1,date('m')+2,1,date('Y'));
				break;
			default:
				if(is_numeric($time))
				{
					$filt_start = mktime(0,0,0,date('m', $time),date('d', $time),date('Y', $time));
					$filt_end = mktime(23,59,59,date('m', $time),date('d', $time),date('Y', $time));
				}
				else
				{
					$filt_start = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
					$filt_end = mktime(0, 0, -1, date('m'), date('d')+1, date('Y'));
				}
		}
		return array(
			"filt_start" => $filt_start,
			"filt_end" => $filt_end,
		);
	}

	private function _get_jobs_for_time($arr, $filters = false)
	{
		$time = isset($arr["request"]["filt_time"]) ? $arr["request"]["filt_time"] : 0;
		$min = 0;
		$max = 0;
		if(isset($arr["request"]["status_orders_opt1"]) and $arr["request"]["status_orders_opt1"] === "order")
		{
			$ol2 = new object_list(array(
				"class_id" => CL_SHOP_PRODUCT_PURVEYANCE,
				"days" => new obj_predicate_not(null),
				"sort_by" => "days desc",
				"limit" => "0,1",
			));
			if($ol2->count())
			{
				$max = $ol2->begin()->prop("days");
			}
		}

		$arr["start"] = isset($arr["request"]["status_orders_s_start"]) ? $arr["request"]["status_orders_s_start"] : 0;
		$arr["end"] = isset($arr["request"]["status_orders_s_end"]) ? $arr["request"]["status_orders_s_end"] : 0;
		extract($this->_get_status_orders_time_filt($arr));
		$filt = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $filt_start - $max * 24 * 60 * 60, $filt_end);
		$params = array(
			"class_id" => CL_MRP_JOB,
			"starttime" => $filt,
			"RELTYPE_MRP_PROJECT.workspace" => $arr["obj_inst"]->prop("mrp_workspace"),
			"RELTYPE_JOB(CL_MATERIAL_EXPENSE).class_id" => CL_MATERIAL_EXPENSE,
		);

		if($filters)
		{
			if(!empty($arr["request"]["status_orders_s_case_no"]))
			{
				$params["RELTYPE_MRP_PROJECT.name"] = "%{$arr["request"]["status_orders_s_case_no"]}%";
			}
			elseif(!empty($arr["request"]["filt_case"]))
			{
				$params["RELTYPE_MRP_PROJECT.oid"] = $arr["request"]["filt_case"];
			}
			if(!empty($arr["request"]["res_filt"]))
			{
				$params["RELTYPE_MRP_RESOURCE.oid"] = $arr["request"]["res_filt"];
				if(obj($arr["request"]["res_filt"])->is_a(menu_obj::CLID))
				{
					$ot = new object_tree(array(
						"class_id" => array(menu_obj::CLID, mrp_resource_obj::CLID),
						"parent" => $res,
					));
					$params["RELTYPE_MRP_RESOURCE.oid"] = $ot->ids();
				}
			}
		}

		if(isset($arr["request"]["status_orders_opt1"]) and $arr["request"]["status_orders_opt1"] === "order")
		{
			$ol = new object_list($params);
			$remove_ids = array();
			foreach($ol->arr() as $oid =>$o)
			{
				$is_in_range = false;
				$conn = $o->connections_to(array(
					"from.class_id" => CL_MATERIAL_EXPENSE,
				));
				$prods = array();
				foreach($conn as $c)
				{
					$prods[] = $c->from()->prop("product");
				}
				$odl = new object_data_list(array(
					"class_id" => CL_SHOP_PRODUCT_PURVEYANCE,
					"object" => $prods,
				),
				array(
					CL_SHOP_PRODUCT_PURVEYANCE => array("days" => "days", "object" => "object"),
				));
				$checked_prods = array();
				foreach($odl->arr() as $od)
				{
					$d = $o->prop("starttime") + $od["days"] * 24 * 60 * 60;
					$checked_prods[] = $od["object"];
					if($d >= $filt_start and $d <= $filt_end)
					{
						$is_in_range = true;
						break;
					}
				}
				foreach($prods as $prod)
				{
					if(!in_array($prod, $checked_prods))
					{
						$d = $o->prop("starttime");
						if($d >= $filt_start and $d <= $filt_end)
						{
							$is_in_range = true;
							break;
						}
					}
				}
				if(!$is_in_range)
				{
					$remove_ids[] = $oid;
				}
			}
			$ol->remove($remove_ids);
			return $ol;
		}
		$ol = new object_list($params);
		return $ol;
	}

	function _get_status_orders_toolbar($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];

		$tb->add_menu_button(array(
			"img" => "nool1.gif",
			"name" => "move_to_order",
			"tooltip" => t("Lisa ostutellimuse reaks"),
		));

		$tb->add_sub_menu(array(
			"parent" => "move_to_order",
			"name" => "move_to_order_w1",
			"text" => t("See n&auml;dal loodud"),
		));

		$tb->add_sub_menu(array(
			"parent" => "move_to_order",
			"name" => "move_to_order_w2",
			"text" => t("Eelmine n&auml;dal loodud"),
		));

		$tb->add_sub_menu(array(
			"parent" => "move_to_order",
			"name" => "move_to_order_w3",
			"text" => t("2 n&auml;dalat tagasi loodud"),
		));

		$tb->add_sub_menu(array(
			"parent" => "move_to_order",
			"name" => "move_to_order_w4",
			"text" => t("3 n&auml;dalat tagasi loodud"),
		));

		get_instance(CL_SHOP_PURCHASE_ORDER);
		$ol = new object_list(array(
			"class_id" => CL_SHOP_PURCHASE_ORDER,
			"order_status" => ORDER_STATUS_INPROGRESS,
			"created" => new obj_predicate_compare(OBJ_COMP_GREATER, mktime(0, 0, 0, date('m'), date('d') - 28 + date('N'), date('Y'))),
		));

		$nw = date('W');

		foreach($ol->arr() as $oid => $o)
		{
			$date = $o->created();
			$tb->add_menu_item(array(
				"parent" => "move_to_order_w".($nw - date('W', $date) + 1),
				"text" => sprintf("%s (%s)", date('d.m.Y', $date), ($p = $o->prop("purchaser")) ? obj($p)->name() : t("Tundmatu tarnija")),
				"url" => "javascript: var cf = document.forms.changeform; cf.add_rows_order.value = '".$oid."'; cf.action.value = 'create_purchase_order'; cf.submit();",
			));
		}

		$tb->add_button(array(
			"img" => "copy.gif",
			"action" => "create_purchase_order",
			"name" => "create_purchase_order",
			"tooltip" => t("Loo ostutellimus"),
		));

		$tb->add_save_button();
	}

	private function _init_status_orders(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
			"chgbgcolor" => "bgcolor",
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"chgbgcolor" => "bgcolor",
		));

		if($arr["levels"])
		{
			$t->define_field(array(
				"name" => "required",
				"caption" => t("Vajadused"),
			));

			$t->define_field(array(
				"name" => "ordered",
				"caption" => t("Tellitud"),
			));
		}

		for($i = 0; $i <= $arr["levels"]; $i++)
		{
			$t->define_field(array(
				"name" => "required_".$i,
				"caption" => $arr["levels"] ? sprintf("&Uuml;hik %s", $i + 1) : t("Vajadus"),
				"align" => "center",
				"chgbgcolor" => "bgcolor",
				"parent" => $arr["levels"] ? "required" : null,
			));
		}

		for($i = 0; $i <= $arr["levels"]; $i++)
		{
			$t->define_field(array(
				"name" => "ordered_".$i,
				"caption" => $arr["levels"] ? sprintf("&Uuml;hik %s", $i + 1) : t("Tellitud"),
				"align" => "center",
				"chgbgcolor" => "bgcolor",
				"parent" => $arr["levels"] ? "ordered" : null,
			));
		}

		foreach($arr["warehouses"] as $wh)
		{
			if($arr["levels"])
			{
				$t->define_field(array(
					"name" => "amount_".$wh,
					"caption" => t("Kogused"),
					"parent" => "wh_".$wh,
				));
				$t->define_field(array(
					"name" => "diff_".$wh,
					"caption" => t("Vahed"),
					"parent" => "wh_".$wh,
				));
			}

			if(!$this->can("view", $wh))
			{
				continue;
			}
			$t->define_field(array(
				"name" => "wh_".$wh,
				"caption" => obj($wh)->name(),
			));
			for($i = 0; $i <= $arr["levels"]; $i++)
			{
				$cp = $arr["levels"] ? sprintf("&Uuml;hik %s", $i + 1) : t("Kogus");
				$t->define_field(array(
					"name" => "amount_".$wh."_".$i,
					"caption" => $cp,
					"align" => "center",
					"chgbgcolor" => "bgcolor",
					"parent" => $arr["levels"] ? "amount_".$wh : "wh_".$wh,
				));
			}
			for($i = 0; $i <= $arr["levels"]; $i++)
			{
				$cp = $arr["levels"] ? sprintf("&Uuml;hik %s", $i + 1) : t("Vahe");
				$t->define_field(array(
					"name" => "diff_".$wh."_".$i,
					"caption" => $cp,
					"align" => "center",
					"chgbgcolor" => "bgcolor",
					"parent" => $arr["levels"] ? "diff_".$wh : "wh_".$wh,
				));
			}
		}

		$t->define_field(array(
			"name" => "companies",
			"caption" => t("Tarnijad"),
			"align" => "center",
			"chgbgcolor" => "bgcolor",
		));
		$t->set_default_sortby(array("product" => "product", "type" => "type"));
		$t->set_caption(t("Kogusevajaduste tabel"));
	}

	function _get_status_orders($arr)
	{
		if($ws = $arr["obj_inst"]->prop("mrp_workspace"))
		{
			$schedule = new mrp_schedule();
			$schedule->create(array(
				"mrp_workspace" => obj($ws)->id(),
				"mrp_force_replan" => 1,
			));
			$arr["obj_inst"]->update_orders();
		}

		$t = $arr["prop"]["vcl_inst"];

		if($arr["obj_inst"]->class_id() == CL_SHOP_WAREHOUSE)
		{
			$arr["warehouses"] = array($arr["obj_inst"]->id());
		}

		$levels = 0;

		if(count($this->get_warehouse_configs($arr, "has_alternative_units")) and ($arr["obj_inst"]->class_id() == CL_SHOP_WAREHOUSE || $arr["obj_inst"]->prop("show_alt_units")))
		{
			$levels += (int)$this->get_warehouse_configs($arr, "alternative_unit_levels");
		}
		$arr["levels"] = $levels;

		$this->_init_status_orders($arr);

		$job_ol = $this->_get_jobs_for_time($arr, true);
		$params = array(
			"class_id" => CL_SHOP_PRODUCT,
		);

		$n = isset($arr["request"]["status_orders_s_name"]) ? $arr["request"]["status_orders_s_name"] : false;
		$c = isset($arr["request"]["status_orders_s_code"]) ? $arr["request"]["status_orders_s_code"] : false;
		if($n || $c)
		{
			if($n)
			{
				$params["name"] = "%".$n."%";
			}
			if($c)
			{
				$params["code"] = "%".$c."%";
			}
		}
		else
		{
			$params["RELTYPE_PRODUCT(CL_MATERIAL_EXPENSE).RELTYPE_JOB"] = $job_ol->count() ? $job_ol->ids() : -1;
		}

		if(!empty($arr["request"]["status_orders_s_art_cat"]) and !empty($arr["request"]["pgtf"]))
		{
			$params["RELTYPE_CATEGORY.oid"] = !empty($arr["request"]["status_orders_s_art_cat"]) ? $arr["request"]["status_orders_s_art_cat"] : $arr["request"]["pgtf"];
		}
		$prod_ol = new object_list($params);

		$pi = get_instance(CL_SHOP_PRODUCT);
		$wso = obj();
		$wso->set_class_id(CL_SHOP_PURCHASE_MANAGER_WORKSPACE);
		$ufi = obj();
		$ufi->set_class_id(CL_SHOP_UNIT_FORMULA);
		get_instance(CL_SHOP_PURCHASE_ORDER);

		foreach($prod_ol->arr() as $oid => $o)
		{
			$data = array(
				"oid" => $oid,
				"name" => html::obj_change_url($o).(($c = $o->prop("code")) ? " (".$c.")" : ""),
				"product" => $oid,
			);

			$units = $pi->get_units($o);

			if(!$arr["request"]["filt_time"])
			{
				$arr["start"] = $arr["request"]["status_orders_s_start"];
				$arr["end"] = $arr["request"]["status_orders_s_end"];
			}
			$times = $this->_get_status_orders_time_filt($arr);

			$add_time = 0;
			if($arr["request"]["status_orders_opt1"] === "order")
			{
				$conn = $o->connections_to(array(
					"from.class_id" => CL_SHOP_PRODUCT_PURVEYANCE,
				));
				if(count($conn))
				{
					$c = reset($conn);
					$add_time = $c->from()->prop("days") * 24 * 60 * 60;
				}
			}

			$brons = $wso->get_order_rows(array(
				"date" => array($times["filt_start"] - $add_time, $times["filt_end"] - $add_time),
				"job" => $job_ol->count() ? $job_ol->ids() : -1,
				"product" => $oid,
				"order_type" => "CL_SHOP_SELL_ORDER",
			));

			if(!$brons->count())
			{
				continue;
			}

			$orders = $wso->get_order_rows(array(
				"date" => 0,
				"product" => $oid,
				"order_type" => "CL_SHOP_PURCHASE_ORDER",
				"order_status" => ORDER_STATUS_CONFIRMED,
			));
			$order = 0;

			foreach(array(array("brons", "required"), array("orders", "ordered")) as $var)
			{
				foreach($$var[0]->arr() as $row)
				{
					$req_amt = "";
					for($i = 0; $i <= $levels; $i++)
					{
						if(!$units[$i])
						{
							continue;
						}
						if($row->prop("unit") == $units[$i])
						{
							$req_amt[$i] = $row->prop("amount");
						}
						if(isset($req_amt))
						{
							$data[$var[1]."_".$i] += $req_amt[$i];
						}
						else
						{
							unset($fo);
							$unit = $row->prop("unit");
							$fo = $ufi->get_formula(array(
								"from_unit" => $unit,
								"to_unit" => $units[$i],
								"product" => $o,
							));
							if($fo)
							{
								$data[$var[1]."_".$i] += $req_amt[$i] = round($ufi->calc_amount(array(
									"amount" => $row->prop("amount"),
									"prod" => $o,
									"obj" => $fo,
								)), 3);
							}
						}
					}
					if($var[0] === "brons")
					{
						$conn = $row->connections_to(array(
							"from.class_id" => CL_SHOP_SELL_ORDER,
						));
						foreach($conn as $c)
						{
							$order = $c->from();
						}
						$job = obj($order->prop("job"));
						$job_data = array(
							"name" => html::obj_change_url($job)."<br /><span style=\"font-size:10px;\">(".date('d.m.Y H:i', $job->prop("starttime")).")</span>",
							"oid" => $row->id(),
							"product" => $oid,
							"type" => "job",
							"bgcolor" => "#f0f0f0",
						);
						for($i = 0; $i <= $levels; $i++)
						{
							$job_data["required_".$i] = sprintf("%s %s", $req_amt[$i], obj($units[$i])->prop("unit_code"));
						}
						$t->define_data($job_data);
					}
				}
			}
			for($i = 0; $i <= $levels; $i++)
			{
				if(!$units[$i])
				{
					continue;
				}
				if(!$data["required_".$i])
				{
					$data["required_".$i] = 0;
				}
				if(!$data["ordered_".$i])
				{
					$data["ordered_".$i] = 0;
				}
				unset($req_amt);
				unset($row);
				foreach($arr["warehouses"] as $wh)
				{
					if(!$this->can("view", $wh))
					{
						continue;
					}
					$amt = $pi->get_amount(array(
						"unit" => $units[$i],
						"prod" => $oid,
						"warehouse" => $wh,
					));
					$amount = 0;
					foreach($amt->arr() as $ao)
					{
						$amount += $ao->prop("amount");
					}
					$data["amount_".$wh."_".$i] = sprintf("%s %s", $amount, obj($units[$i])->prop("unit_code"));
					$data["diff_".$wh."_".$i] = sprintf("%s %s", $amount - $data["required_".$i] + $data["ordered_".$i], obj($units[$i])->prop("unit_code"));
				}
				$data["required_".$i] = sprintf("%s %s", $data["required_".$i], obj($units[$i])->prop("unit_code"));
				$data["ordered_".$i] = sprintf("%s %s", $data["ordered_".$i], obj($units[$i])->prop("unit_code"));
			}
			$c_ol = new object_list(array(
				"class_id" => CL_SHOP_PRODUCT_PURVEYANCE,
				"object" => $oid,
			));
			$cos = array();
			foreach($c_ol->arr() as $o)
			{
				$co = $o->prop("company");
				if($this->can("view", $co))
				{
					$cos[$co] = html::obj_change_url(obj($co));
				}
			}
			$data["companies"] = implode(", ", $cos);
			$t->define_data($data);
		}
	}

	/**
	@attrib name=create_purchase_order
	**/
	function create_purchase_order($arr)
	{
		$params = array();
		$item_params = array();
		$wso = obj();
		$wso->set_class_id(CL_SHOP_PURCHASE_MANAGER_WORKSPACE);
		foreach($arr["sel"] as $id)
		{
			$o = obj($id);
			if($o->class_id() == CL_SHOP_PRODUCT)
			{
				$units = $o->instance()->get_units($o);
				$brons = $wso->get_order_rows(array(
					"date" => ($d = $arr["request"]["status_orders_s_date"]) ? date_edit::get_timestamp($d) : 0,
					"product" => $id,
					"order_type" => "CL_SHOP_SELL_ORDER",
				));
				$amt = 0;
				foreach($brons->arr() as $b)
				{
					if($b->prop("unit") == $units[0])
					{
						$amt += $b->prop("amount");
					}
					else
					{
						$fo = $ufi->get_formula(array(
							"from_unit" => $b->prop("unit"),
							"to_unit" => $units[0],
							"product" => $o,
						));
						if($fo)
						{
							$amt += round($ufi->calc_amount(array(
								"amount" => $b->prop("amount"),
								"prod" => $o,
								"obj" => $fo,
							)), 3);
						}
					}
				}
				$item_params[] = $id.",".$amt.",".$units[0];
			}
			elseif($o->class_id() == CL_SHOP_ORDER_ROW)
			{
				$item_params[] = $o->prop("prod").",".$o->prop("amount").",".$o->prop("unit");
			}
		}
		$params["add_rows"] = implode(";", $item_params);
		if($oid = $arr["add_rows_order"])
		{
			$params["group"] = "articles";
			$params["id"] = $oid;
			return $this->mk_my_orb("change", $params, CL_SHOP_PURCHASE_ORDER);
		}
		else
		{
			$params["parent"] = $arr["id"];
			return $this->mk_my_orb("new", $params, CL_SHOP_PURCHASE_ORDER);
		}
	}

	private function get_search_param_groups()
	{
		return array("prod", "storage_income", "storage_export", "storage_status", "storage_movements", "storage_writeoffs", "storage_prognosis", "storage_inventories", "purchase_orders", "sell_orders", "arrival_prod", "status_orders","purchase_notes","purchase_bills","sales_notes","sales_bills");
	}

	private function get_search_group($arr)
	{
		switch($arr["request"]["group"])
		{
			case "articles":
			case "products":
				$group = "prod";
				break;
			case "storage":
				$group = "storage_income";
				break;
			case "status":
			case "status_status":
				$group = "storage_status";
				break;
			case "status_prognosis":
				$group = "storage_prognosis";
				break;
			case "status_inventories":
				$group = "storage_inventories";
				break;
			case "purchases":
				$group = "purchase_orders";
				break;
			case "arrivals":
				$group = "arrival_prod";
				break;
			case "purchases":
				$group = "purchase_orders";
				break;
			case "sales":
				$group = "sell_orders";
				break;
			default:
				$group = $arr["request"]["group"];
				break;
		}
		return $group;
	}

	private function is_search_param($var)
	{
		$chk = null;
		if(($pos = strpos($var, "_s_")) and strpos($var, "_sbt") === false)
		{
			$chk = substr($var, 0, $pos);
		}
		if(in_array($chk, $this->get_search_param_groups()))
		{
			return array(
				"grp" => $chk,
				"var" => substr($var, $pos+3),
			);
		}
		return false;
	}

	private function get_art_filter_ol($arr)
	{
		$group = $this->get_search_group($arr);
		$params = array();
		if(!empty($arr["request"][$group."_s_article"]) and $p = $arr["request"][$group."_s_article"])
		{
			$name = $p;
		}
		elseif(!empty($arr["request"][$group."_s_art"]) and $p = $arr["request"][$group."_s_art"])
		{
			$name = $p;
		}
		if(!empty($name) and $name)
		{
			$params["name"] = "%".$name."%";
		}
		if(!empty($arr["request"][$group."_s_barcode"]) and $b = $arr["request"][$group."_s_barcode"])
		{
			$params["barcode"] = "%".$b."%";
		}
		if(!empty($arr["request"][$group."_s_articlecode"]) and $c = $arr["request"][$group."_s_articlecode"])
		{
			$params["code"] = "%".$c."%";
		}
		if(!empty($arr["request"][$group."_s_art_cat"]) and $pgid = $arr["request"][$group."_s_art_cat"])
		{
			$oids = $this->get_art_cat_filter($pgid);
			if(count($oids))
			{
				$params["oid"] = $oids;
			}
		}
		if(count($params))
		{
			$params["class_id"] = CL_SHOP_PRODUCT;
			$ol = new object_list($params);
			return $ol;
		}
		else
		{
			return false;
		}
	}

	function get_art_cat_filter($pgid)
	{
		$res = array();
		if($pgid)
		{
			$cats = new object_list(array(
				"class_id" => CL_SHOP_PRODUCT_CATEGORY,
				"oid" => $pgid,
			));
			if($cats->count())
			{
				$c = new connection();
				$conn = $c->find(array(
					"to" => $cats->ids(),
					"from.class_id" => CL_SHOP_PRODUCT,
					"reltype" => "RELTYPE_CATEGORY",
				));
				foreach($conn as $c)
				{
					$res[] = $c["from"];
				}
			}
			if(!count($res))
			{
				$res = array(-1);
			}
		}
		return $res;
	}

	function callback_post_save($arr)
	{
		if(!empty($arr["new"]) and empty($arr["request"]["no_new_config"]))
		{
			$pt = $arr["obj_inst"]->id();
			$o = obj();
			$o->set_class_id(CL_SHOP_WAREHOUSE_CONFIG);
			$o->set_parent($pt);
			$o->set_name(sprintf(t("%s seaded"), $arr["obj_inst"]->name()));
			$this->create_config_folder($o, $pt, "prod_fld", t("Toodete kataloog"));
			$this->create_config_folder($o, $pt, "prod_cat_fld", t("Tootekategooriate kataloog"));
			$this->create_config_folder($o, $pt, "pkt_fld", t("Pakettide kataloog"));
			$this->create_config_folder($o, $pt, "reception_fld", t("Sissetulekute kataloog"));
			$this->create_config_folder($o, $pt, "export_fld", t("V&auml;ljaminekute kataloog"));
			$this->create_config_folder($o, $pt, "prod_type_fld", t("Tootet&uuml;&uuml;pide kataloog"));
			$this->create_config_folder($o, $pt, "prod_conf_folder", t("Seadetevormide kataloog"));
			$this->create_config_folder($o, $pt, "order_fld", t("Tellimuste kataloog"));
			$this->create_config_folder($o, $pt, "buyers_fld", t("Tellijate kataloog"));
			$o->save();
			$arr["obj_inst"]->set_prop("conf", $o->id());
			$arr["obj_inst"]->save();
		}
	}

	function create_config_folder($conf, $pt, $fld, $name)
	{
		$o = obj();
		$o->set_class_id(CL_MENU);
		$o->set_parent($pt);
		$o->set_name($name);
		$o->save();
		$conf->set_prop($fld, $o->id());
	}

	function _get_purchase_notes_time_tree(&$arr)
	{
		if(empty($arr["warehouses"]))
		{
			$arr["warehouses"] = array($arr["obj_inst"]->id());
		}
		$ol = new object_list(array(
			"class_id" => CL_SHOP_DELIVERY_NOTE,
			($arr["prop"]["name"] === "purchase_notes_time_tree" ? "to_warehouse" : "from_warehouse") => $arr["warehouses"],
			"sort_by" => "delivery_date asc",
			"limit" => "0,1",
		));
		$arr["start"] = $arr["end"] = 0;
		$start_o = $ol->begin();
		if($start_o)
		{
			$arr["start"] = $start_o->prop("delivery_date");
			$ol = new object_list(array(
				"class_id" => CL_SHOP_DELIVERY_NOTE,
				($arr["prop"]["name"] === "purchase_notes_time_tree" ? "to_warehouse" : "from_warehouse") => $arr["warehouses"],
				"sort_by" => "delivery_date desc",
				"limit" => "0,1",
			));
			$end_o = $ol->begin();
			$arr["end"] = $end_o->prop("delivery_date");
		}
//		$arr["all"] = true;
		return $this->_get_status_orders_time_tree($arr);
	}

	function _get_purchase_bills_time_tree($arr)
	{
		if(empty($arr["warehouses"]))
		{
			$arr["warehouses"] = array($arr["obj_inst"]->id());
		}
		$cos = $this->get_warehouse_configs($arr, "manager_cos");
		$ol = new object_list(array(
			"class_id" => CL_CRM_BILL,
			($arr["prop"]["name"] === "purchase_bills_time_tree" ? "customer" : "impl") => $cos,
			"sort_by" => "aw_date asc",
			"limit" => "0,1",
		));
		$start_o = $ol->begin();
		$arr["start"] = $arr["end"] = 0;
		if($start_o)
		{
			$arr["start"] = $start_o->prop("delivery_date");
			$ol = new object_list(array(
				"class_id" => CL_CRM_BILL,
				($arr["prop"]["name"] === "purchase_bills_time_tree" ? "customer" : "impl") => $cos,
				"sort_by" => "aw_date desc",
				"limit" => "0,1",
			));
			$end_o = $ol->begin();
			$arr["end"] = $end_o->prop("bill_date");
		}
		$arr["all"] = true;
		return $this->_get_status_orders_time_tree($arr);
	}

	function _get_sales_notes_time_tree($arr)
	{
		return $this->_get_purchase_notes_time_tree($arr);
	}

	function _get_sales_bills_time_tree($arr)
	{
		return $this->_get_purchase_bills_time_tree($arr);
	}

	function _get_purchase_notes_prod_tree($arr)
	{
		return $this->mk_prodg_tree($arr);
	}

	function _get_purchase_bills_prod_tree($arr)
	{
		return $this->mk_prodg_tree($arr);
	}

	function _get_sales_notes_prod_tree($arr)
	{
		return $this->mk_prodg_tree($arr);
	}

	function _get_sales_bills_prod_tree($arr)
	{
		return $this->mk_prodg_tree($arr);
	}

	function _get_purchase_notes_cust_tree($arr)
	{
		return $this->_get_purchase_cust_groups_tree($arr);
	}

	function _get_purchase_bills_cust_tree($arr)
	{
		return $this->_get_purchase_cust_groups_tree($arr);
	}

	function _get_sales_notes_cust_tree($arr)
	{
		return $this->_get_purchase_cust_groups_tree($arr);
	}

	function _get_sales_bills_cust_tree($arr)
	{
		return $this->_get_purchase_cust_groups_tree($arr);
	}

	function _get_purchase_cust_groups_tree(&$arr)
	{
		$arr["show_subs"] = true;
		return $this->_get_clients_groups_tree($arr);
	}

	function _get_purchase_notes_status_tree(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->start_tree(array(
			"type" => TREE_DHTML,
			"tree_id" => "notes_status_tree",
			"persist_state" => true,
			"has_root" => false,
			"get_branch_func" => null,
		));
		$group = $this->get_search_group($arr);

		foreach(array(STORAGE_FILTER_UNCONFIRMED, STORAGE_FILTER_CONFIRMED, null) as $id => $var)
		{
			$arr["warehouses"] = array($arr["obj_inst"]->id());
			$arr["request"][$group."_s_status"] = $var;
			$ol = $this->_get_storage_ol($arr);
			${"count".$id} = $ol->count();
		}

		$t->add_item(0, array(
			"id" => "sl_unc",
			"url" => aw_url_change_var($group."_s_status", STORAGE_FILTER_UNCONFIRMED),
			"name" => sprintf("%s (%s)", t("Kinnitamata"), $count0),
		));
		$t->add_item(0, array(
			"id" => "sl_conf",
			"url" => aw_url_change_var($group."_s_status", STORAGE_FILTER_CONFIRMED),
			"name" => sprintf("%s (%s)", t("Kinnitatud"), $count1),
		));
		$t->add_item(0, array(
			"id" => "sl_all",
			"url" => aw_url_change_var($group."_s_status", null),
			"name" => sprintf("%s (%s)", t("K&otilde;ik"), $count2),
		));

		$v = automatweb::$request->arg($group."_s_status");
		$t->set_selected_item(($v == STORAGE_FILTER_UNCONFIRMED) ? "sl_unc" : ($v ? "sl_conf" : "sl_all"));
	}

	function _get_purchase_bills_status_tree(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->start_tree(array(
			"type" => TREE_DHTML,
			"tree_id" => "bills_status_tree",
		));
		$group = $this->get_search_group($arr);

		$bi = get_instance(CL_CRM_BILL);

		foreach($bi->states as $id => $state)
		{
			$arg = $arr;
			$arg["request"][$group."_s_status"] = $id;
			$ol = $this->_get_storage_ol($arg);
			$count = $ol->count();
			$t->add_item(0, array(
				"id" => "st_".$id,
				"url" => aw_url_change_var($group."_s_status", $id),
				"name" => sprintf("%s (%s)", $state, $count),
			));
		}

		$arg["request"][$group."_s_status"] = null;
		$ol = $this->_get_storage_ol($arg);
		$count = $ol->count();

		$t->add_item(0, array(
			"id" => "all",
			"url" => aw_url_change_var($group."_s_status", null),
			"name" => sprintf("%s (%s)", t("K&otilde;ik"), $count),
		));

		$v = automatweb::$request->arg($group."_s_status");

		$t->set_selected_item($v === null ? "all" : "st_".$v);
	}

	function _get_sales_notes_status_tree($arr)
	{
		return $this->_get_purchase_notes_status_tree($arr);
	}

	function _get_sales_bills_status_tree($arr)
	{
		return $this->_get_purchase_bills_status_tree($arr);
	}

	function _get_purchase_notes($arr)
	{
		$this->_get_storage_income($arr);
		$arr["prop"]["vcl_inst"]->set_caption("Ostusaatelehed");
	}

	function _get_purchase_bills($arr)
	{
		$this->_get_storage_income($arr);
		$arr["prop"]["vcl_inst"]->set_caption("Ostuarved");
	}

	function _get_sales_notes($arr)
	{
		$this->_get_storage_export($arr);
		$arr["prop"]["vcl_inst"]->set_caption("M&uuml;&uuml;gisaatelehed");
	}

	function _get_sales_bills($arr)
	{
		$this->_get_storage_export($arr);
		$arr["prop"]["vcl_inst"]->set_caption("M&uuml;&uuml;giarved");
	}

	function _get_purchase_notes_toolbar(&$arr)
	{
		$tb =& $arr["prop"]["toolbar"];

		$tb->add_menu_button(array(
			"name" => "create_new",
			"tooltip" => t("Uus")
		));

		if(empty($arr["warehouses"]) and $arr["obj_inst"]->class_id() == CL_SHOP_WAREHOUSE)
		{
			$whs = array($arr["obj_inst"]);
		}
		else
		{
			foreach($arr["warehouses"] as $wh)
			{
				if($this->can("view", $wh))
				{
					$whs[$wh] = obj($wh);
				}
			}
		}
		$npt = "create_new";
		foreach($whs as $whid)
		{
			$who = obj($whid);
			$pt = $who->prop("conf.".(($arr["prop"]["name"] === "storage_export_toolbar" || strpos("sales", $arr["request"]["group"]) !== false) ? "export_fld" : "reception_fld"));
			if(!$pt)
			{
				continue;
			}
			if(count($whs) > 1)
			{
				$tb->add_sub_menu(array(
					"name" => "wh_".$whid,
					"text" => $who->name(),
					"parent" => "create_new",
				));
				$npt = "wh_".$whid;
			}
			if(strpos($arr["request"]["group"], "notes") !== false)
			{
				$tb->add_menu_item(array(
					"parent" => $npt,
					"text" => t("Saateleht"),
					"link" => $this->mk_my_orb("new", array(
						"parent" => $pt,
						"return_url" => get_ru()
					), CL_SHOP_DELIVERY_NOTE)
				));
			}
			else
			{
				$tb->add_menu_item(array(
					"parent" => $npt,
					"text" => t("Arve"),
					"link" => $this->mk_my_orb("new", array(
						"parent" => $pt,
						"return_url" => get_ru(),
					), CL_CRM_BILL),
				));
			}
		}

		$tb->add_delete_button();
		$tb->add_save_button();
	}

	function _get_purchase_bills_toolbar($arr)
	{
		return $this->_get_purchase_notes_toolbar($arr);
	}

	function _get_sales_notes_toolbar($arr)
	{
		return $this->_get_purchase_notes_toolbar($arr);
	}

	function _get_sales_bills_toolbar($arr)
	{
		return $this->_get_purchase_notes_toolbar($arr);
	}

	function _get_clients_status_tree($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->set_selected_item(empty($arr["request"]["client_state"]) ? "" : "state_".$arr["request"]["client_state"]);
		$cr_inst = get_instance("applications/crm/crm_company_customer_data_obj");
		$t->start_tree(array(
			"has_root" => true,
			"root_name" => empty($arr["request"]["client_state"]) ?  "<b>".t("K&otilde;ik staatused")."</b>" : t("K&otilde;ik staatused"),
			"root_url" => aw_url_change_var(array("client_state" => null)),
			"root_icon" => icons::get_icon_url(CL_MENU),
			"type" => TREE_DHTML,
			"tree_id" => "client_state_tree",
			"persist_state" => 1,
		));

		foreach($cr_inst->sales_state_names() as $key => $name)
		{
			$t->add_item(0, array(
				"id" => "state_".$key,
				"name" => $name,
				"url" => aw_url_change_var("client_state", $key),
			));
		}
	}

	function _get_clients_groups_tree($arr)
	{
		if (!isset($this->config) or !is_object($this->config))
		{
			$this->show_error_text(t("VIGA: konfiguratsioon on valimata!"));
			return PROP_ERROR;
		}

		$owner = $this->config->prop("owner");
		if(!$this->can("view", $owner))
		{
			return;
		}
		$params = array(
			"set_retu" => get_ru(),
			"id" => $arr["obj_inst"]->id(),
			"group" => $arr["request"]["group"],
		);
		if(isset($arr["request"]["filt_cust"])) 	$params["filt_cust"] = 	$arr["request"]["filt_cust"];
		if(isset($arr["show_subs"])) 			$params["show_subs"] = 	$arr["show_subs"];
		if(isset($arr["request"]["filt_time"]))		$params["filt_time"] = 	$arr["request"]["filt_time"];
		if(isset($arr["request"]["pgtf"]))		$params["pgtf"] = 	$arr["request"]["pgtf"];

		foreach($arr["request"] as $var => $val)
		{
			if($this->is_search_param($var))
			{
				$params[$var] = $val;
			}
		}
		$params["parent"] = " ";
		$gbf = $this->mk_my_orb("get_clients_groups_tree_level", $params, CL_SHOP_WAREHOUSE);
		$t = $arr["prop"]["vcl_inst"];
		$g = $this->get_search_group($arr);
		$t->start_tree(array(
			"has_root" => true,
			"root_name" => t("Kliendigrupid"),
			"root_url" => aw_url_change_var(array("filt_cust" => null)),
			"root_icon" => icons::get_icon_url(CL_MENU),
			"type" => TREE_DHTML,
			"tree_id" => "client_groups_tree",
			"persist_state" => 1,
			"get_branch_func" => $gbf,
		));
		$conn = obj($owner)->connections_from(array(
			"type" => "RELTYPE_CATEGORY",
			"sort_by" => "to.name"
		));
		foreach($conn as $c)
		{
			switch($g)
			{
				case "purchase_orders":
				case "sell_orders":
					$arr["request"]["filt_cust"] = $c->prop("to");
					$ol = $this->_get_orders_ol($arr);
					$count = $ol->count();
					break;
				case "purchase_notes":
				case "purchase_bills":
				case "sales_notes":
				case "sales_bills":
					$arr["request"]["filt_cust"] = $c->prop("to");
					$arr["warehouses"] = array($arr["obj_inst"]->id());
					$ol = $this->_get_storage_ol($arr);
					$count = $ol->count();
					break;
				case "sales_clients":
				case "purchase_clients":
					$arr["request"]["filt_cust"] = $c->prop("to");
					$ol = $this->_get_clients_ol($arr);
					$count = $ol->count();
					break;
			}
			$t->add_item(0, array(
				"id" => $c->prop("to"),
				"name" => sprintf("%s %s", $c->prop("to.name"), isset($count) ? "(".$count.")" : ""),
				"url" => aw_url_change_var("filt_cust", $c->prop("to")),
			));
			$conn = $c->to()->connections_from(array(
				"type" => "RELTYPE_CATEGORY"
			));
			if(count($conn))
			{
				$t->add_item($c->prop("to"), array(
					"name" => "tmp",
					"id" => $c->prop("to")."_tmp",
				));
			}
			if(!empty($arr["show_subs"]))
			{
				$conn = $c->to()->connections_from(array(
					"type" => "RELTYPE_CUSTOMER",
				));
				if(count($conn))
				{
					$t->add_item($c->prop("to"), array(
						"name" => "tmp",
						"id" => $c->prop("to")."_tmp",
					));
				}
			}
		}
		$arr["request"]["filt_cust"] = null;
		switch($g)
		{
			case "purchase_orders":
			case "sell_orders":
				$ol = $this->_get_orders_ol($arr);
				$count = $ol->count();
				break;
			case "purchase_notes":
			case "purchase_bills":
			case "sales_notes":
			case "sales_bills":
				$arr["warehouses"] = array($arr["obj_inst"]->id());
				$ol = $this->_get_storage_ol($arr);
				$count = $ol->count();
				break;
			case "sales_clients":
			case "purchase_clients":
				$ol = $this->_get_clients_ol($arr);
				$count = $ol->count();
				break;
		}
		$t->add_item(0, array(
			"id" => "all",
			"name" => sprintf("%s (%s)", t("K&otilde;ik"), $count),
			"url" => aw_url_change_var("filt_cust", null),
		));
		$f = automatweb::$request->arg("filt_cust");
		$t->set_selected_item($f ? $f : "all");
	}

	/**
		@attrib name=post_campaign_row all_args=1
	**/
	function post_campaign_row($arr)
	{
		$discount = obj($arr["id"]);
		$discount->set_prop("discount" , $arr["discount"]);
		$discount->set_prop("object" , $arr["product"]);
		$discount->set_prop("active" , $arr["active"] == 1 ? 1 : 0);
		$discount->save();

		//kuup2evad ka, siis vouks toimida

		die();
	}

	/**
	@attrib name=get_clients_groups_tree_level all_args=1
	**/
	function _get_clients_groups_tree_level($arr)
	{
		$this->config = obj(obj($arr["id"])->prop("conf"));
		$t = get_instance("vcl/treeview");
		$t->start_tree(array(
			"type" => TREE_DHTML,
			"branch" => 1,
			"tree_id" => "client_groups_tree",
		));
		$pt = $arr["parent"];
		$conn = obj($pt)->connections_from(array(
			"type" => "RELTYPE_CATEGORY"
		));
		$g = $this->get_search_group(array(
			"request" => $arr,
		));
		foreach($conn as $c)
		{
			switch($g)
			{
				case "purchase_orders":
				case "sell_orders":
					$arr["filt_cust"] = $c->prop("to");
					$ol = $this->_get_orders_ol(array(
						"request" => $arr,
						"obj_inst" => obj($arr["id"]),
					));
					$count = $ol->count();
					break;
				case "purchase_notes":
				case "purchase_bills":
				case "sales_notes":
				case "sales_bills":
					$arr["filt_cust"] = $c->prop("to");
					$ol = $this->_get_storage_ol(array(
						"request" => $arr,
						"warehouses" => array($arr["id"]),
						"obj_inst" => obj($arr["id"]),
					));
					$count = $ol->count();
					break;
				case "sales_clients":
				case "purchase_clients":
					$arr["filt_cust"] = $c->prop("to");
					$ol = $this->_get_clients_ol(array(
						"request" => $arr,
					));
					$count = $ol->count();
					break;
			}
			$t->add_item(0, array(
				"id" => $c->prop("to"),
				"name" => sprintf("%s %s", $c->prop("to.name"), isset($count) ? "(".$count.")" : ""),
				"url" => aw_url_change_var(array("filt_cust" => $c->prop("to")), false, $arr["set_retu"]),
			));
			$conn = $c->to()->connections_from(array(
				"type" => "RELTYPE_CATEGORY"
			));
			if(count($conn))
			{
				$t->add_item($c->prop("to"), array(
					"name" => "tmp",
					"id" => $c->prop("to")."_tmp",
				));
			}
			if($arr["show_subs"])
			{
				$conn = $c->to()->connections_from(array(
					"type" => "RELTYPE_CUSTOMER",
				));
				if(count($conn))
				{
					$t->add_item($c->prop("to"), array(
						"name" => "tmp",
						"id" => $c->prop("to")."_tmp",
					));
				}
			}
		}
		if($arr["show_subs"])
		{
			$conn = obj($pt)->connections_from(array(
				"type" => "RELTYPE_CUSTOMER",
			));
			if(count($conn))
			{
				foreach($conn as $c)
				{
					switch($g)
					{
						case "purchase_orders":
						case "sell_orders":
							$arr["filt_cust"] = $c->prop("to");
							$ol = $this->_get_orders_ol(array(
								"request" => $arr,
								"obj_inst" => obj($arr["id"]),
							));
							$count = $ol->count();
							break;
						case "purchase_notes":
						case "purchase_bills":
						case "sales_notes":
						case "sales_bills":
							$arr["filt_cust"] = $c->prop("to");
							$ol = $this->_get_storage_ol(array(
								"request" => $arr,
								"warehouses" => array($arr["id"]),
								"obj_inst" => obj($arr["id"]),
							));
							$count = $ol->count();
							break;
						case "sales_clients":
						case "purchase_clients":
							$arr["filt_cust"] = $c->prop("to");
							$ol = $this->_get_clients_ol(array(
								"request" => $arr,
							));
							$count = $ol->count();
							break;
					}
					$t->add_item(0, array(
						"id" => $c->prop("to"),
						"name" => sprintf("%s %s", $c->prop("to.name"),  isset($count) ? "(".$count.")" : ""),
						"url" => aw_url_change_var(array("filt_cust" => $c->prop("to")), false, $arr["set_retu"]),
						"iconurl" => icons::get_icon_url($c->prop("to.class_id")),
					));
				}
			}
		}
		$f = automatweb::$request->arg("filt_cust");
		$t->set_selected_item($f ? $f : "all");
		die($t->finalize_tree());
	}

	function _get_clients_alphabet_tree($arr)
	{
		if (!isset($this->config) or !is_object($this->config))
		{
			$this->show_error_text(t("VIGA: konfiguratsioon on valimata!"));
			return PROP_ERROR;
		}

		$owner = $this->config->prop("owner");
		if(!$this->can("view", $owner))
		{
			$this->show_error_text(t("Lao omanik valimata"));
			return;
		}
		$t = $arr["prop"]["vcl_inst"];
		$t->start_tree(array(
			"type" => TREE_DHTML,
			"tree_id" => "clients_alphabet_tree",
		));
		$t->add_item(0, array(
			"id" => "root",
			"name" => t("T&auml;hestik"),
			"url" => "#",
		));
		$g = $arr["request"]["group"];
		unset($arr["request"]["filt_cust_name"]);
		$ol = $this->_get_clients_ol($arr);
		$letters = array();
		$total = 0;
		foreach($ol->names() as $oid => $name)
		{
			if(!isset($letters[strtolower(substr($name,0,1))]))
			{
				$letters[strtolower(substr($name,0,1))] = 0;
			}
			$letters[strtolower(substr($name,0,1))]++;
			$total++;
		}
		ksort($letters);
		foreach($letters as $l => $c)
		{
			$t->add_item("root", array(
				"id" => $l,
				"name" => sprintf("%s (%s)", strtoupper($l), $c),
				"url" => aw_url_change_var("filt_cust_name", $l),
			));
		}
		$t->add_item(0, array(
			"id" => "all",
			"name" => sprintf("%s (%s)", t("K&otilde;ik"), $total),
			"url" => aw_url_change_var("filt_cust_name", null),
		));
		$f = automatweb::$request->arg("filt_cust_name");
		$t->set_selected_item($f ? $f : "all");
	}

	function _get_clients_toolbar($arr)
	{
		if (!isset($this->config) or !is_object($this->config))
		{
			$this->show_error_text(t("VIGA: konfiguratsioon on valimata!"));
			return PROP_ERROR;
		}

		$owner = $this->config->prop("owner");
		if(!$this->can("view", $owner))
		{
			return;
		}

		$t = $arr["prop"]["vcl_inst"];
		$t->add_menu_button(array(
			'name'=>'add_item',
			'tooltip'=> t('Uus')
		));

		$lp = array(
			'parent' => $arr['obj_inst']->id(),
			'return_url' => get_ru(),
		);
		if(isset($arr["request"]["filt_cust"]) and $this->can("view", $arr["request"]["filt_cust"]) and obj($arr["request"]["filt_cust"])->class_id() == CL_CRM_CATEGORY)
		{
			$lp['alias_to'] = $cat;
			$lp['reltype'] = 3; // crm_company.CUSTOMER,
		}
		if ($arr["request"]["group"] === "sales_clients")
		{
			$lp["set_as_is_cust"] = 1;
		}
		elseif ($arr["request"]["group"] === "purchase_clients")
		{
			$lp["set_as_is_buyer"] = 1;
		}
		$t->add_menu_item(array(
			'parent'=> "add_item",
			'text' => t("Organisatsioon"),
			'link' => $this->mk_my_orb('new',$lp,
				'crm_company'
			)
		));

		$alias_to = $parent = $owner;
		$rt = 30;

		if(isset($arr["request"]["filt_cust"]) and $this->can("view", $arr["request"]["filt_cust"]) and obj($arr["request"]["filt_cust"])->class_id() == CL_CRM_CATEGORY)
		{
			$alias_to = $arr["request"]["filt_cust"];
			$parent = $arr["request"]["filt_cust"];
			$rt = 2;
		}

		$t->add_menu_item(array(
			'parent'=>'add_item',
			'text' => t('Kategooria'),
			'link' => $this->mk_my_orb('new',array(
					'parent' => $parent,
					'alias_to' => $alias_to,
					'reltype' => $rt, //RELTYPE_CATEGORY
					'return_url' => get_ru()
				),
				'crm_category'
			)
		));
	}

	function _get_clients_ol($arr)
	{
		$g = $arr["request"]["group"];
		$owner = $this->config->prop("owner");
		if(!$this->can("view", $owner))
		{
			return new object_list();
		}
		$cust_rel_params = array(
			"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
			"CL_CRM_COMPANY_CUSTOMER_DATA.RELTYPE_".(strpos($g, "sales") !== false ? "SELLER" : "BUYER").".oid" => $owner,
			"limit" => 200,
			"CL_CRM_COMPANY_CUSTOMER_DATA.RELTYPE_".(strpos($g, "sales") !== false ? "BUYER" : "SELLER").".name" => empty($arr["request"]["filt_cust_name"]) ? array() : $arr["request"]["filt_cust_name"]."%",
		);
		if(!empty($arr["request"]["filt_state"]))	$cust_rel_params["sales_state"] = $arr["request"]["filt_state"];
		if(!empty($arr["request"]["timespan"]))		$cust_rel_params["cust_contract_date"] = $this->get_time_ol_filter($arr["request"]["timespan"]);

		$t = new object_data_list(
			$cust_rel_params,
			array(
				CL_CRM_COMPANY_CUSTOMER_DATA => array("seller", "buyer"),
			)
		);
		if(strpos($g, "sales") !== false)
		{
			$buyers = $t->get_element_from_all("buyer");
		}
		else
		{
			$buyers = $t->get_element_from_all("seller");
		}

		$ol = new object_list();

		if(sizeof($buyers))
		{
			$ol->add($buyers);
		}

/*
			"class_id" => array(crm_company_obj::CLID, crm_person_obj::CLID),
			"CL_CRM_COMPANY.RELTYPE_".(strpos($g, "sales") !== false ? "BUYER" : "SELLER")."(CL_CRM_COMPANY_CUSTOMER_DATA).RELTYPE_".(strpos($g, "sales") !== false ? "SELLER" : "BUYER").".oid" => $owner,
			"site_id" => array(),
			"class_id" => array(),
			"name" => empty($arr["request"]["filt_cust_name"]) ? array() : $arr["request"]["filt_cust_name"]."%",
		//	"limit" => "0,200",
		);
		$params = array(
			"class_id" => array(crm_company_obj::CLID, crm_person_obj::CLID),
			"CL_CRM_COMPANY.RELTYPE_".(strpos($g, "sales") !== false ? "BUYER" : "SELLER")."(CL_CRM_COMPANY_CUSTOMER_DATA).RELTYPE_".(strpos($g, "sales") !== false ? "SELLER" : "BUYER").".oid" => $owner,
			"site_id" => array(),
			"class_id" => array(),
			"name" => empty($arr["request"]["filt_cust_name"]) ? array() : $arr["request"]["filt_cust_name"]."%",
		);
		if(!empty($arr["request"]["filt_cust"]))	$params["CL_CRM_COMPANY.RELTYPE_CUSTOMER(CL_CRM_CATEGORY).oid"] = $arr["request"]["filt_cust"];

		$ol = new object_list($params);*/
		return $ol;
	}

	private function get_range($timespan)
	{
		switch($timespan)
		{
			case "period_last_week":
				$from = mktime(0,0,0, 1, 1+((date("W") - 2) * 7) - date("N"), date("Y"));
				$to = mktime(0,0,0, 1, 8+((date("W") - 2) * 7 ) - date("N"), date("Y"));
				break;
			case "period_week":
				$from = mktime(0,0,0, 1, 1+((date("W")- 1) * 7) - date("N"), date("Y"));
				$to = mktime(0,0,0, 1, 8+((date("W")- 1) * 7), date("Y"));
				break;
			case "period_last_last":
				$from = mktime(0,0,0, date("m")-2, 1, date("Y"));
				$to = mktime(0,0,0, date("m")-1, 1, date("Y"));
				break;
			case "period_last":
				$from = mktime(0,0,0, date("m")-1, 1, date("Y"));
				$to = mktime(0,0,0, date("m"), 1, date("Y"));
				break;
			case "period_current":
				$from = mktime(0,0,0, date("m"), 1, date("Y"));
				$to = mktime(0,0,0, date("m")+1, 1, date("Y"));
				break;
			case "period_next":
				$from = mktime(0,0,0, date("m")+1, 1, date("Y"));
				$to = mktime(0,0,0, date("m")+2, 1, date("Y"));
				break;
			case "period_year":
				$from = mktime(0,0,0, 1, 1, date("Y"));
				$to = mktime(0,0,0, 1, 1, date("Y")+1);
				break;
			case "period_lastyear":
				$from = mktime(0,0,0, 1, 1, date("Y")-1);
				$to = mktime(0,0,0,1 , 1, date("Y"));
				break;
			default :
				return array();
				break;
		}
		return array(
			"from" => $from,
			"to" => $to,
		);
	}

	//annad timespani v22rtuse ja saad ol filtrile sobiliku compare objekti
	private function get_time_ol_filter($timespan)
	{
		$range = $this->get_range($timespan);
		extract($range);

		$filt = array();
		if(!empty($from) and !empty($to))
		{
			$filt = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $from, $to);
		}
		else
		{
			if(!empty($from))
			{
				$filt = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $from);
			}
			if(!empty($to))
			{
				$filt = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $to);
			}
		}

		return $filt;
	}


	function _get_clients_tbl($arr)
	{
		if (!isset($this->config) or !is_object($this->config))
		{
			$this->show_error_text(t("VIGA: konfiguratsioon on valimata!"));
			return PROP_ERROR;
		}

		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "address",
			"caption" => t("Aadress"),
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "email",
			"caption" => t("Kontakt"),
			"align" => "center",
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "phone",
			"caption" => t('Telefon'),
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "fax",
			"caption" => t('Faks'),
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "client_manager",
			"caption" => t("Kliendihaldur"),
			"sortable" => 1,
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "customer_rel_creator",
			"caption" => t("Kliendisuhte looja"),
			"sortable" => 1,
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "rel",
			"caption" => t("Kliendisuhe"),
			"sortable" => 1,
			"align" => "center",
			"width" => 160
		));

		$t->set_caption("Kliendid");

		$g = $arr["request"]["group"];
		$owner = $this->config->prop("owner");
		if(!$this->can("view", $owner))
		{
			return;
		}
		$ownerobject = obj($owner);

		if(empty($arr["request"]["timespan"]))
		{
			$arr["request"]["timespan"] = "period_week";
		}

		$ol = $this->_get_clients_ol($arr);
		foreach($ol->arr() as $oid => $o)
		{
			if(!$o->is_a(crm_person_obj::CLID) and !$o->is_a(crm_company_obj::CLID))
			{
				continue;
			}
			if ($this->can("view", $o->prop("phone_id")))
			{
				$phone = obj($o->prop("phone_id"));
				$phone = $phone->name();
			}

			if ($this->can("view", $o->prop("telefax_id")))
			{
				$fax = obj($o->prop("telefax_id"));
				$fax = $fax->name();
			}

			$rel = $mail = $fax = $phone =  "";
			if ($this->can("view", $o->prop("email_id")))
			{
				$mail_obj = new object($o->prop("email_id"));
				$mail = $mail_obj->prop("mail");
				$mail = empty($mail) ? "" : html::href(array(
					"url" => "mailto:" . $mail,
					"caption" => $mail
				));
			}

//			$conn = $o->connections_to(array(
//				"type" => "RELTYPE_".(strpos($g, "sales") !== false ? "SELLER" : "BUYER"),
//				"from.class_id" => CL_CRM_COMPANY_CUSTOMER_DATA
//			));
//			$c = reset($conn);arr();
//			if($c)
//			{
//				$rel = html::obj_change_url($c->from(), ($n = $c->from()->name()) ? $n : t("(nimetu)"));
//			}

			if(strpos($g, "sales") !== false)
			{
				$relation = $o->find_customer_relation($ownerobject , true);
			}
			else
			{
				$relation = $ownerobject->find_customer_relation($o , true);
			}

			if(is_object($relation))
			{
				$rel = html::obj_change_url($relation->id(),  $relation->id());
			}

			$t->define_data(array(
				"rel" => $rel,
				"name" => html::obj_change_url($o),
				"address" => $o->prop("RELTYPE_ADDRESS.name"),
				"email" => $mail,
				"phone" => $phone,
				"fax" => $fax,
				"client_manager" => html::obj_change_url($o->prop("client_manager")),
				"customer_rel_creator" => $o->get_cust_rel_creator_name(),
			));
		}
	}

	function _get_sell_orders_time_tree(&$arr)
	{
		$ol = new object_list(array(
			"class_id" => $arr["prop"]["name"] === "sell_orders_time_tree" ? CL_SHOP_SELL_ORDER : CL_SHOP_PURCHASE_ORDER,
			"sort_by" => "aw_date asc",
			"limit" => "0,1",
			"date" => new obj_predicate_not(null),
		));
		$arr["start"] = $arr["end"]  = 0;
		$start_o = $ol->begin();
		if($start_o)
		{
			$arr["start"] = $start_o->prop("date");
			$ol = new object_list(array(
				"class_id" => $arr["prop"]["name"] === "sell_orders_time_tree" ? CL_SHOP_SELL_ORDER : CL_SHOP_PURCHASE_ORDER,
				"sort_by" => "aw_date desc",
				"limit" => "0,1",
				"date" => new obj_predicate_not(null),
			));
			$end_o = $ol->begin();
			$arr["end"] = $end_o->prop("date");
		}
		$arr["all"] = true;
		return $this->_get_status_orders_time_tree($arr);
	}

	function _get_sell_orders_cust_tree($arr)
	{
		return $this->_get_purchase_cust_groups_tree($arr);
	}

	function _get_purchase_orders_time_tree($arr)
	{
		return $this->_get_sell_orders_time_tree($arr);
	}

	function _get_purchase_orders_cust_tree($arr)
	{
		return $this->_get_purchase_cust_groups_tree($arr);
	}

	/**
		@attrib name=update_products_index
	**/
	function update_products_index($arr)
	{
		$cid = 0;
		$obj_inst = new object($arr['id']);
		if ($this->can('view', $obj_inst->prop('conf')))
		{
			$config = new object($obj_inst->prop('conf'));
			$cid = $config->prop('short_code_ctrl');
		}

		$res = $this->db_fetch_array('
			SELECT
				name,
				code,
				short_code,
				search_term,
				aw_oid
			FROM
				aw_shop_products left join objects on oid = aw_oid
		');
		foreach ($res as $r)
		{
			// convert the search term into short format with short code controller
			if ($cid)
			{
				$short_search_term = get_instance(CL_CFGCONTROLLER)->check_property($cid, null, $r['search_term'], null, null, null);
			//	$short_search_term = $r['search_term'];
			}

			$this->db_query("
				REPLACE
					INTO aw_shop_products_index
				SET
					code = '".addslashes($r['code'])."',
					oid = '".$r['aw_oid']."',
					short_code = '".addslashes($r['short_code'])."',
					search_term = '".addslashes($short_search_term)."',
					name = '".addslashes($r['name'])."',
					updated = true

			");
		}

		// now, we need to delete all those lines from the table which weren't updated
		$this->db_query("DELETE FROM aw_shop_products_index WHERE updated = false");

		// For next import, lets reset the updated bit
		$this->db_query("UPDATE aw_shop_products_index SET updated = false");

		return $arr['post_ru'];
	}

       /** Returns true, if it is possible to make a prducts search using the index table

       **/
       function can_use_products_index()
       {
               if ($this->db_get_table('aw_shop_products_index') !== false)
               {
                       $count = $this->db_fetch_field("SELECT count(*) AS count FROM aw_shop_products_index", "count");
                       if ($count > 0)
                       {
                               return true;
                       }
               }
               return false;
       }

	function _get_category_list($arr)
	{
		$per_page = 10;
		$selected_page = automatweb::$request->arg('ft_page');

		$t =& $arr["prop"]["vcl_inst"];

		$t->define_pageselector(array(
			'type' => 'lb',
			'records_per_page' => $per_page
		));

		$t->define_field(array(
			"name" => "ord",
			"caption" => t("Jrk"),
			"align" => "left",
		));

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "left",
		));

		$t->define_field(array(
			"name" => "types",
			"caption" => t("Seotud tootekategooriate t&uuml;&uuml;bid"),
			"align" => "left",
		));

 		$t->define_chooser(array(
			"name" => "sel",
			"field" => "id",
		));

		$caption = sprintf(t('Lao "%s" tootekategooriad'), $arr["obj_inst"] -> name());
		if(!isset($arr["request"]["cat"]))
		{
			$arr["request"]["cat"] = $arr["obj_inst"]->get_conf("prod_cat_fld");
		}

		$ol = new object_list();
		if($this->can("view" , $arr["request"]["cat"]))
		{
			$object = obj($arr["request"]["cat"]);
			switch($object->class_id())
			{
				case CL_SHOP_PRODUCT_CATEGORY_TYPE:
					$caption = sprintf(t('T&uuml;&uuml;bi %s tootekategooriad'), $object->name());
					$ol= $object->get_categories();
					break;
				case CL_SHOP_PRODUCT_CATEGORY:
					$caption = sprintf(t('Tootekategooria %s alamkategooriad'), $object->name());
					$ol= $object->get_categories();
					break;
				case CL_MENU:
					$ol= new object_list(array(
						"parent" => $object->id(),
						"class_id" => CL_SHOP_PRODUCT_CATEGORY,
						"sort_by" => "jrk asc, name asc",
					));
					$caption = sprintf(t('Lao "%s" tootekategooriad'), $arr["obj_inst"] -> name());
					break;
				default:
					$ol= new object_list();
					$caption = sprintf(t('Lao "%s" tootekategooriad'), $arr["obj_inst"] -> name());
			}
		}
		elseif($arr["request"]["cat"] === "all")
		{
			$ol= new object_list(array(
				"class_id" => CL_SHOP_PRODUCT_CATEGORY,
				"sort_by" => "jrk asc, name asc",
			));
		}
		foreach($ol->arr() as $o)
		{
			$types = $o->get_gategory_types()->names();
			foreach($types as $id => $name)
			{
				$types[$id] .= " ".html::href(array(
					"url" => "javascript:rem_type_from_cat('".$o->id()."' , '".$id."');",
					"caption" => html::img(array(
						"url" => icons::get_std_icon_url("delete"),
					)),
				));
			}
			$t->define_data(array(
				"name" => html::obj_change_url($o,null,array("return_url" => $this->mk_my_orb("change" , array("class" => "shop_warehouse","id" => $arr["obj_inst"]->id() , "group" => "category" )))),
				"id" => $o->id(),
				"types" => join(", " , $types),
				"ord" => html::textbox(array("name" => "ord[".$o->id()."]" , "value" => $o->ord() , "size" => 3)),
			));
		}
		$t->set_sortable(false);
		$t->set_caption($caption);
	}

	function _get_category_tree($arr)
	{
		$tv = $arr["prop"]["vcl_inst"];
		$var = "cat";

		$prod_folder = $this->config->prop("prod_cat_fld");

		$tv->set_selected_item(isset($arr["request"][$var]) ? $arr["request"][$var] : $prod_folder);

		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"persist_state" => true,
			"tree_id" => "product_cat_tree",
		));

		$tv->add_item(0,array(
			"name" => get_name($prod_folder),
			"id" => $prod_folder,
			"reload" => array(
				"props" => array("category_list"),
			        "params" => array("cat" => $prod_folder)
			)
		));//print "folder:" ; arr($prod_folder);

		$cats = new object_list(array(
			"class_id" => CL_SHOP_PRODUCT_CATEGORY,
			"parent" => $prod_folder,
			"sort_by" => "jrk asc, name asc",
		));

		foreach($cats->arr() as $id => $cat)
		{
			$tv->add_item($prod_folder,array(
				"name" => $cat->name(),
				"id" => $id."",
				"reload" => array(
					"props" => array("category_list"),
				        "params" => array("cat" => $id)
				)
			));
			$this->add_cat_leaf($tv , $id);
		}

		$types = $arr["obj_inst"]->get_product_category_types();

		foreach($types->arr() as $id => $cat)
		{
			$tv->add_item(0,array(
				"name" => $cat->name(),
				"id" => $id."",
				"reload" => array(
					"props" => array("category_list"),
				        "params" => array("cat" => $id)
				)
			));

			$this->add_cat_leaf($tv , $id);
		}

		$tv->add_item(0,array(
			"name" => t("K&otilde;ik kategooriad"),
			"id" => "all",
			"reload" => array(
				"props" => array("category_list"),
			        "params" => array("cat" => "all")
			)
		));//print "folder:" ; arr($prod_folder);


	}

	function add_cat_leaf($tv , $parent)
	{
		if(!is_oid($parent))
		{
			return;
		}
		$o = obj($parent);
		$cats = $o->get_categories();

		foreach($cats->arr() as $id => $o)
		{
			$name = $o->name();
			$tv->add_item($parent,array(
				"name" => $name,
				"id" => $id."",
				"reload" => array(
					"props" => array("category_list"),
				        "params" => array("cat" => $id)
				)
			));
			$this->add_cat_leaf($tv , $id);
		}
	}

	function _get_category_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_menu_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Uus"),
		));

		$tb->add_menu_item(array(
			"parent" => "new",
			"text" => t("Tootekategooria"),
			"link" => "javascript:add_cat();"
		));

		$tb->add_menu_item(array(
			"parent" => "new",
			"text" => t("Tootekategooria t&uuml;&uuml;p"),
			"link" => "javascript:add_cat_type();"
		));

		$tb->add_menu_button(array(
			"name" => "change",
			"img" => "edit.gif",
			"tooltip" => t("Muuda"),
		));

		$types = $arr["obj_inst"]->get_product_category_types();

		foreach($types->arr() as $id => $cat)
		{
			$tb->add_menu_item(array(
				"parent" => "change",
				"text" => $cat->name(),
				"link" => html::get_change_url($id,array("return_url" => get_ru())),
			));
		}

		$tb->add_button(array(
			"name" => "save",
			"img" => "save.gif",
			"text" => t("Salvesta kategooriad"),
			"url" => "javascript:save_categories();",
//			"url" => "javascript:javascript:submit_changeform();",
		));

		$tb->add_button(array(
			"name" => "search",
			"img" => "search.gif",
			"text" => t("Otsi kategooriaid"),
			"url" => "javascript:;",
			"onClick" => "win = window.open('".$this->mk_my_orb("search_categories", array("is_popup" => 1), CL_SHOP_WAREHOUSE)."&category=' + get_property_data['cat'] ,'categoty_search','width=720,height=600,statusbar=yes, scrollbars=yes');",
		));

		$tb->add_menu_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta"),
		));

		$tb->add_menu_item(array(
			"parent" => "delete",
			"text" => t("Valitud kategooriad"),
			"link" => "javascript:submit_changeform('delete_objects');",
		));

		foreach($types->arr() as $id => $cat)
		{
			$tb->add_menu_item(array(
				"parent" => "delete",
				"text" => $cat->name(),
				"link" => $this->mk_my_orb("delete_objects", array("sel" => array($id => $id),
//					"id" => $o->id(),
					"post_ru" => get_ru()
				)),
			));
		}

		$tb->add_menu_button(array(
			"name" => "add_type",
//			"img" => "delete.gif",
			"text" => t("Lisa kategooriale kategooria t&uuml;&uuml;p"),
			"tooltip" => t("Lisa kategooriale kategooria t&uuml;&uuml;p"),
		));

		foreach($types->names() as $id => $name)
		{
			$tb->add_menu_item(array(
				"parent" => "add_type",
				"text" => $name,
				"link" => "javascript:add_type('".$id."');",
			));
		}
	}

	function _get_product_management_toolbar($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$types = $arr["obj_inst"]->get_product_category_types();

		$tb->add_menu_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Uus"),
		));

		$tb->add_menu_item(array(
			"parent" => "new",
			"text" => t("Pakett"),
			"link" => "javascript:add_packet();"
		));

		$tb->add_menu_item(array(
			"parent" => "new",
			"text" => t("Toode"),
			"link" => "javascript:add_product();"
		));

		$tb->add_menu_item(array(
			"parent" => "new",
			"text" => t("Pakend"),
			"link" => "javascript:add_packaging();"
		));

		$tb->add_menu_item(array(
			"parent" => "new",
			"text" => t("Tootekategooria"),
			"link" => "javascript:add_cat();"
		));

		$tb->add_menu_item(array(
			"parent" => "new",
			"text" => t("Tootekategooria t&uuml;&uuml;p"),
			"link" => "javascript:add_cat_type();"
		));

		$tb->add_button(array(
			"name" => "copy",
			"img" => "copy.gif",
			"tooltip" => t("Kopeeri tooted teise kategooriasse"),
			"url" => "javascript:copy_products();"
		));

		$tb->add_button(array(
			"name" => "cut",
			"img" => "cut.gif",
			"tooltip" => t("T&otilde;sta tooted teise kategooriasse"),
			"url" => "javascript:cut_products();"
		));

		if(isset($_SESSION["shop_warehouse"]) and ($_SESSION["shop_warehouse"]["cut_products"] || $_SESSION["shop_warehouse"]["copy_products"]))
		{
			$tb->add_button(array(
				"name" => "paste",
				"img" => "paste.gif",
				"tooltip" => t("Paigalda kopeeritud/l&otilde;igatud tooted valitud kategooriatesse"),
				"url" => "javascript:paste_products();"
			));
		}

		$tb->add_menu_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta"),
		));

		$tb->add_menu_item(array(
			"parent" => "delete",
			"text" => t("Valitud objektid"),
			"link" => "javascript:submit_changeform('delete_objects');",
		));

		foreach($types->arr() as $id => $cat)
		{
			$tb->add_menu_item(array(
				"parent" => "delete",
				"text" => $cat->name(),
				"link" => $this->mk_my_orb("delete_objects", array("sel" => array($id => $id),
//					"id" => $o->id(),
					"post_ru" => get_ru()
				)),
			));
		}

		$tb->add_menu_button(array(
			"name" => "change",
			"img" => "edit.gif",
			"tooltip" => t("Muuda"),
		));


		foreach($types->arr() as $id => $cat)
		{
			$tb->add_menu_item(array(
				"parent" => "change",
				"text" => $cat->name(),
				"link" => html::get_change_url($id,array("return_url" => get_ru())),
			));
		}

		$tb->add_button(array(
			"name" => "save",
			"img" => "save.gif",
			"text" => t("Salvesta kategooriad"),
			"url" => "javascript:save_categories();",
//			"url" => "javascript:javascript:submit_changeform();",
		));

		$tb->add_button(array(
			"name" => "search",
			"img" => "search.gif",
			"text" => t("Otsi kategooriaid valitud t&uuml;&uuml;bi/kategooria/kausta alla"),
			"url" => "javascript:;",
			"onClick" => "win = window.open('".$this->mk_my_orb("search_categories", array("is_popup" => 1), CL_SHOP_WAREHOUSE)."&category=' + get_property_data['cat'] ,'categoty_search','width=720,height=600,statusbar=yes, scrollbars=yes');",
		));

		if($types->count())
		{
			$tb->add_menu_button(array(
				"name" => "add_type",
	//			"img" => "delete.gif",
				"text" => t("Lisa kategooriale kategooria t&uuml;&uuml;p"),
				"tooltip" => t("Lisa kategooriale t&uuml;&uuml;p, millist t&uuml;&uuml;pi kategooriaid v&otilde;ib antud tootel veel olla"),
			));

			foreach($types->names() as $id => $name)
			{
				$tb->add_menu_item(array(
					"parent" => "add_type",
					"text" => $name,
					"link" => "javascript:add_type('".$id."');",
				));
			}
		}

		$tb->add_button(array(
			"name" => "activate",
			"action" => "activate_warehouse_items",
			"text" => t("Aktiveeri"),
			"tooltip" => t("Aktiveeri"),
		));
		$tb->add_button(array(
			"name" => "deactivate",
			"action" => "deactivate_warehouse_items",
			"text" => t("Deaktiveeri"),
			"tooltip" => t("Deaktiveeri"),
		));

		load_javascript("reload_properties_layouts.js");
	}

	function _get_brand_toolbar($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_js_new_button(array(
			"parent" => $arr["obj_inst"]->id(),
			"clid" => CL_SHOP_BRAND,
			"refresh" => array("brand_list"),
			"promts" => array("name" => t("Sisesta uue objekti nimi")),
		));
		$tb->add_delete_button();
	}

	function _get_channel_toolbar($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_js_new_button(array(
			"parent" => $arr["obj_inst"]->id(),
			"clid" => CL_WAREHOUSE_SELL_CHANNEL,
			"refresh" => array("channel_list"),
			"promts" => array("name" => t("Sisesta uue m&uuml;&uuml;gikanali nimi")),
		));
		$tb->add_delete_button();
	}

	function _get_channel_list($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];

		$tb->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
		));

		$tb->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
		$channels = $arr["obj_inst"]->get_channels();
		foreach($channels->arr() as $channel)
		{
			$tb->define_data(array(
				"name" =>html::obj_change_url($channel, parse_obj_name($channel->name())),
				"oid" => $channel->id(),
			));
		}

	}



	function _get_brand_list($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];

		$tb->define_field(array(
			"name" => "logo",
			"caption" => t("Logo"),
			"sortable" => 1,
		));

		$tb->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
		));

		$tb->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
		$brands = $arr["obj_inst"]->get_brands();
		foreach($brands->arr() as $brand)
		{
			$tb->define_data(array(
				"name" =>html::obj_change_url($brand, parse_obj_name($brand->name())),
				"oid" => $brand->id(),
				"logo" => $brand->get_logo_html(),
			));
		}

	}

	function _get_product_management_tree(&$arr)
	{
		$arr["prop"]["value"] = html::div(array(
			"id" => "product_management_tree",
		));

		return PROP_OK;
	}

	/**
		@attrib name=get_product_management_tree_nodes params=pos
		@param id required
			The OID of the crm_db object
		@param node optional default=-1
			The id of the parent node for which the children will be returned.
	**/
	public function get_product_management_tree_nodes($arr)
	{
		if (!isset($this->config))
		{
			$o = obj($arr["id"], array(), shop_warehouse_obj::CLID);
			$this->config = $this->config = obj($o->prop("conf"), array(), shop_warehouse_config_obj::CLID);
		}

		if (isset($arr["node"]) and $arr["node"] > 0)
		{
			$parent_category = obj($arr["node"], array(), shop_product_category_obj::CLID);
			$categories = $parent_category->get_categories();
		}
		else
		{
			$categories = new object_list(array(
				"class_id" => shop_product_category_obj::CLID,
				"parent" => $this->config->prop("prod_cat_fld"),
			));
		}

		$data = array();
		foreach($categories->names() as $oid => $name)
		{
			$data[] = array(
				"data" => iconv(aw_global_get("charset"), "utf-8", strlen($name) > 30 ? substr($name, 0, 30)."..." : $name),
				"attr" => array("id" => $oid),
				"state" => "closed"
			);
		}
		die(json_encode($data));
	}

	public function _get_product_management_category_tree($arr)
	{
		$arr["prop"]["value"] = html::div(array(
			"id" => "product_management_category_tree",
		));

		return PROP_OK;
	}

	/**
		@attrib name=get_product_management_category_tree_nodes params=pos
		@param id required
			The OID of the crm_db object
		@param node optional default=-1
			The id of the parent node for which the children will be returned.
	**/
	public function get_product_management_category_tree_nodes($arr)
	{
		$o = obj($arr["id"], array(), shop_warehouse_obj::CLID);

		if (isset($arr["node"]) and $arr["node"] > 0)
		{
			$parent_category_type = obj($arr["node"], array(), shop_product_category_type_obj::CLID);
			$category_types = $parent_category_type->get_categories();
		}
		else
		{
			$category_types = $o->get_product_category_types();
		}

		$data = array();
		foreach($category_types->names() as $oid => $name)
		{
			$data[] = array(
				"data" => iconv(aw_global_get("charset"), "utf-8", strlen($name) > 30 ? substr($name, 0, 30)."..." : $name),
				"attr" => array("id" => $oid),
				"state" => "closed"
			);
		}
		die(json_encode($data));
	}

	function remove_this_shit()
	{

		if (!isset($this->config) or !is_object($this->config))
		{
			$this->show_error_text(t("VIGA: konfiguratsioon on valimata!"));
			return PROP_ERROR;
		}

		$ret = "";
		$types = $arr["obj_inst"]->get_product_category_types();
//		if(!$types->count())
//		{
//			return PROP_IGNORE;
//		}

//------ esimene puu on selleks, et k6ik saaks kohe kuskilt oksa alt n2htavale
		$var = "cat";
		$prod_folder = $this->config->prop("prod_cat_fld");

		$tv = new treeview();
		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"persist_state" => true,
			"tree_id" => "product_management_cat_tree",
		));
		$tv->set_selected_item(isset($arr["request"][$var]) ? $arr["request"][$var] : $prod_folder);

		$tv->add_item(0,array(
			"name" => t("Tootet&uuml;&uuml;bid"),
			"id" => $prod_folder,
			"reload" => array(
				"layouts" => array("product_managementright","packets_right"),
			        "params" => array("cat" => null)
			)
		));

		$cats = new object_list(array(
			"class_id" => CL_SHOP_PRODUCT_CATEGORY,
			"parent" => $prod_folder,
			"sort_by" => "name asc",
			"status" => array(object::STAT_NOTACTIVE, object::STAT_ACTIVE),
		));

		foreach($types->arr() as $id => $cat)
		{
			$t = new treeview();
			$t->start_tree(array(
				"type" => TREE_DHTML,
				"persist_state" => true,
				"tree_id" => "product_management_cat_types_tree".$id,
			));


			$var = "cat_".$id;
			$t->set_selected_item(isset($arr["request"][$var]) ? $arr["request"][$var] : "");
			$t->add_item(0,array(
				"name" => $cat->name(),
				"id" => $id."",
				"reload" => array(
					"props" => array("product_management_list", "packets_list"),
				        "params" => array("cat_".$id => $id)
				)
			));

			$this->add_cat_type_leaf($t , $id);
			$ret .= html::div(array(
				"content" => $t->get_html(),
				"border" => "1px solid gray",
				"background" => "white",
				"margin" => "5px",
			));
	//		$ret .= "<div style='border: 1px solid gray; background-color: white;margin:5px;'>".$t->get_html()."</div>";
		}

		$arr["prop"]["value"] = $ret;
		return PROP_OK;
	}


	function _get_managementcat_tree($arr)
	{
		$types = $arr["obj_inst"]->get_product_category_types();
		$val = "";
		foreach($types->arr() as $id => $cat)
		{
			$t = new treeview();
			$t->start_tree(array(
				"type" => TREE_DHTML,
				"persist_state" => true,
				"tree_id" => "product_management_cat_types_tree".$id,
			));


			$var = "cat_".$id;
			$t->set_selected_item(isset($arr["request"][$var]) ? $arr["request"][$var] : "");
			$t->add_item(0,array(
				"name" => $cat->name(),
				"id" => $id."",
				"reload" => array(
					"props" => array("product_management_list", "packets_list"),
				        "params" => array("cat_".$id => $id)
				)
			));
//
			$this->add_cat_type_leaf($t , $id);
			$val.= $t->get_html();
		}
		return $val;
	}

	function add_cat_type_leaf($t , $parent)
	{
		if(!is_oid($parent))
		{
			return;
		}

		$o = obj($parent);
		$cats = $o->get_categories();

		foreach($cats->names() as $id => $name)
		{
			$t->add_item($parent,array(
				"name" => $name,
				"id" => $id."",
				"iconurl" => icons::get_icon_url(CL_SHOP_PRODUCT),
				"reload" => array(
					"props" => array("product_management_list", "packets_list"),
				        "params" => array("cat_".$parent => $id)
				)
			));

			$this->add_cat_type_leaf($t , $id);
		}
	}


	function _get_product_managements_sbt($arr)
	{
		$arr['prop']['onclick'] = "reload_layout(
			['product_managementright'],
			{product_managements_name: $('[id=product_managements_name]').val(),product_managements_code: $('[id=product_managements_code]').val(), product_managements_barcode: $('[id=product_managements_barcode]').val(), product_managements_count: $('[id=product_managements_count]').val(), product_managements_price_from: $('[id=product_managements_price_from]').val(), product_managements_price_to: $('[id=product_managements_price_to]').val(), });";
		return PROP_OK;
	}


	function _get_product_management_list($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		
		$tb->set_caption(t("Toodete nimekiri"));

		$tb->define_pageselector(array(
			'type' => 'lb',
			'records_per_page' => 10,
		));

		$tb->set_default("chgbgcolor", "color");

		$tb->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
		));

		$tb->define_field(array(
			"sortable" => 1,
			"name" => "code",
			"caption" => t("Kood"),
			"align" => "center"
		));

		$tb->define_field(array(
			"sortable" => 1,
			"name" => "last_purchase_price",
			"caption" => t("Ostuhind"),
			"align" => "center"
		));

		$tb->define_field(array(
			"sortable" => 1,
			"name" => "price_fifo",
			"caption" => t("FIFO"),
			"align" => "center"
		));
		if(!isset($group) || ((!isset($arr["request"][$group."_s_pricelist"]) and $this->def_price_list) || automatweb::$request->arg($group."_s_pricelist")))
		{
			$tb->define_field(array(
				"sortable" => 1,
				"name" => "sales_price",
				"caption" => t("M&uuml;&uuml;gihind"),
				"align" => "center"
			));
		}

		if($arr["obj_inst"]->prop("order_center"))
		{
			$tb->define_field(array(
				"name" => "ord",
				"sortable" => 1,
				"caption" => t("J&auml;rjekord"),
				"align" => "center",
				"sorting_field" => "hidden_ord",
			));
		}

		if(empty($group) || (!automatweb::$request->arg("pgtf") and automatweb::$request->arg("pgtf") != $this->prod_type_fld and !automatweb::$request->arg($group."_s_cat")))
		{
			$tb->define_field(array(
				"name" => "cat",
				"caption" => t("Kategooria"),
				"sortable" => 1,
				"align" => "center",
			));
		}
		$tb->define_field(array(
			"name" => "packagings",
			"caption" => t("Pakendid"),
			"sortable" => 1,
			"align" => "center",
		));
		$tb->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
			"chgbgcolor" => "color",
		));

		$filter = array();
		if(!empty($arr["request"]["cat"]))
		{
			$cats = $this->get_categories_from_search($arr["request"]);
			$filter["category"] = $cats;
			if(is_array($filter["category"]) and !sizeof($filter["category"]))
			{
				$filter["category"] = array(1);
			}
		}
		elseif(sizeof($arr["request"]) <= 6 )//ei tule otsingust
		{
			$filter["parent"] = array($arr["obj_inst"]->id());
			if($arr["obj_inst"]->get_conf("prod_fld"))
			{
				$filter["parent"][] = $arr["obj_inst"]->get_conf("prod_fld");
			}
//			$filter["category"] = new obj_predicate_anything();
		}

		$params = $arr["request"];
		if(isset($params["product_managements_name"]) and strlen($params["product_managements_name"]))
		{
			$filter["name"] = $params["product_managements_name"];
		}
		if(isset($params["product_managements_code"]) and strlen($params["product_managements_code"]))
		{
			$filter["code"] = $params["product_managements_code"];
		}
		if(isset($params["product_managements_barcode"]) and strlen($params["product_managements_barcode"]))
		{
			$filter["barcode"] = $params["product_managements_barcode"];
		}
		if(isset($params["product_managements_price_from"]) and $params["product_managements_price_from"] > 0)
		{
			$filter["price_from"] = $params["product_managements_price_from"];
		}
		if(isset($params["product_managements_price_to"]) and $params["product_managements_price_to"] > 0)
		{
			$filter["price_to"] = $params["product_managements_price_to"];
		}

		$ol = $arr["obj_inst"]->search_products($filter);

		foreach($ol->arr() as $o)
		{
			$packagings = array();
			foreach($o->get_packagings()->arr() as $packaging)
			{
				$packagings[]=  html::obj_change_url($packaging, parse_obj_name($packaging->name()));
			}
			$cats = array();
			foreach($o->get_categories($o->id()) as $cat)
			{
				if($this->can("view" , $cat))
				{
					$cat = obj( $cat);
					$cats[]=  html::obj_change_url($cat, parse_obj_name($cat->name()));
				}
			}
			$data = array(
				"oid" => $o->id(),
				"name" => html::obj_change_url($o, parse_obj_name($o->name())), //$name,
				"cnt" => $o->prop("item_count"),
	//			"item_type" => $tp,
				"change" => html::href(array(
					"url" => $this->mk_my_orb("change", array(
						"id" => $o->id(),
						"return_url" => get_ru()
					), $o->class_id()),
					"caption" => t("Muuda")
				)),
				"code" => $o->prop("code"),
				"del" => html::checkbox(array(
					"name" => "sel[]",
					"value" => $o->id()
				)),
				"is_menu" => ($o->class_id() == CL_MENU ? 0 : 1),
				"ord" => html::textbox(array(
					"name" => "set_ord[".$o->id()."]",
					"value" => $o->ord(),
					"size" => 5
				)).html::hidden(array(
					"name" => "old_ord[".$o->id()."]",
					"value" => $o->ord()
				)),
				"hidden_ord" => $o->ord(),
				"clid" => $o->class_id(),
				"type" => ($o->class_id() == CL_SHOP_PRODUCT)?"":t("&Uuml;ksiktooted"),
				"packagings" => join(", " , $packagings),
				"cat" => join(", " , $cats),
				"color" => $o->status() == 2 ? "#99FF99" : "#E1E1E1",
			);
			$tb->define_data($data);
		}

		if(isset($filter["category"]) and is_array($filter["category"]) and sizeof($filter["category"]))
		{
			$cat = reset($filter["category"]);
			$tb->set_caption(sprintf(t("Tootekategooria %s tooted") , get_name($cat)));
		}

		$tb->set_numeric_field("hidden_ord");
		$tb->set_default_sortby("name");
		$tb->set_default_sorder("asc");
		$tb->sort_by();
	}

	/**
		@attrib name=create_new_category all_args=1
	**/
	public function create_new_category($arr)
	{
		$warehouse = obj($arr["id"]);
		if(!is_oid($arr["cat"]))
		{
			$arr["cat"] = $warehouse->get_conf("prod_cat_fld");
		}
		$o = new object();
		$o->set_parent($arr["cat"]);
		$o->set_name(iconv("UTF-8",aw_global_get("charset"),$arr["name"]));arr($arr["name"]);
		$o->set_class_id(CL_SHOP_PRODUCT_CATEGORY);
		$o->save();
		if($this->can("view" , $arr["cat"]))
		{
			$category = obj($arr["cat"]);
			if($category->class_id() == CL_SHOP_PRODUCT_CATEGORY)
			{
				$o->set_category($category->id());
			}
			if($category->class_id() == CL_SHOP_PRODUCT_CATEGORY_TYPE)
			{
				$o->set_category_type($category->id());
			}
		}
		$o->save();
		die($o->id());
	}

	/**
		@attrib name=create_new_category_type all_args=1
	**/
	public function create_new_category_type($arr)
	{
		$warehouse = obj($arr["id"]);arr($arr);arr($warehouse->get_conf("prod_cat_fld"));
		$o = new object();
		$o->set_parent($warehouse->get_conf("prod_cat_fld"));
		$o->set_name(iconv("UTF-8",aw_global_get("charset"),$arr["name"]));
		$o->set_class_id(CL_SHOP_PRODUCT_CATEGORY_TYPE);
		$o->save();arr($o);
		die($o->id());
	}

	private function get_categories_from_search($arr)
	{
		$cats = array();
		if(!empty($arr["cat"]) and is_oid($arr["cat"]))
		{
			$cats[] = $arr["cat"];
		}
		foreach($arr as $key => $val)
		{
			if(substr_count($key , "cat_"))
			{
				$type_id = substr($key , 4);
				if($key != $val)
				{
					$cats[] = $val;
				}
			}
		}
		return $cats;
	}

	/**
		@attrib name=create_new_product all_args=1
	**/
	public function create_new_product($arr)
	{
		$arr["category"] =  $this->get_categories_from_search($arr);
		$object = obj($arr["id"]);
		$arr["name"] = iconv("UTF-8",aw_global_get("charset"),$arr["name"]);
		$id = $object->new_product($arr);
		die($id);
	}

	/**
		@attrib name=create_new_packet all_args=1
	**/
	public function create_new_packet($arr)
	{
		$arr["category"] =  $this->get_categories_from_search($arr);
		$object = obj($arr["id"]);
		$arr["name"] = iconv("UTF-8",aw_global_get("charset"),$arr["name"]);
		$id = $object->new_packet($arr);
		die($id);
	}

	/**
		@attrib name=add_type_to_categories all_args=1
	**/
	public function add_type_to_categories($arr)
	{
		if(is_array($arr["sel"]) and $arr["type"])
		{
			foreach($arr["sel"] as $cat)
			{
				$c = obj($cat);
				$c->add_type($arr["type"]);
			}
		}
		die("1");
	}

	/**
		@attrib name=rem_type_from_category all_args=1
	**/
	public function rem_type_from_category($arr)
	{
		if(is_oid($arr["cat"]) and $arr["type"])
		{
			$c = obj($arr["cat"]);
			$c->remove_type($arr["type"]);
		}
		die("1");
	}

	/**
		@attrib name=save_categories all_args=1
	**/
	public function save_categories($arr)
	{
		if(is_array($arr["ord"]))
		{
			$this->set_order($arr["ord"]);
		}
		die("1");
	}

	/** searches and connects bill row to task row
		@attrib name=search_categories
		@param category optional
			category oid/category type oid
		@param name optional type=string
			category name
		@param oid optional type=int
			category oid
		@param result optional type=int/array
			result category id
	**/
	function search_categories($arr)
	{
		$content = "";
		if(is_oid($arr["result"]) || (is_array($arr["result"]) and sizeof($arr["result"])))
		{
			if(is_oid($arr["result"]))
			{
				$arr["result"] = array($arr["result"] => $arr["result"]);
			}
			if(is_oid($arr["category"]))
			{
				$category = obj($arr["category"]);
				if($category->class_id() == CL_SHOP_PRODUCT_CATEGORY)
				{
					foreach($arr["result"] as $tr)
					{
						$o = obj($tr);
						$o->set_category($category->id());
					}
				}
				if($category->class_id() == CL_SHOP_PRODUCT_CATEGORY_TYPE)
				{
					foreach($arr["result"] as $tr)
					{
						$category->add_category($tr);
					}
				}
			}

			die("<script language='javascript'>
				window.opener.reload_property('category_list');
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
			"category" => $arr["category"],
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

		$filter = array(
			"class_id" => CL_SHOP_PRODUCT_CATEGORY,
		);

		if($arr["name"])
		{
			$filter["name"] = $arr["name"]."%";
		}

		if($arr["oid"])
		{
			$filter["oid"] = $arr["oid"];
		}

		if(sizeof($filter) < 4)
		{
			$filter["limit"] = 10;
		}
		$ol = new object_list($filter);
		foreach($ol->arr() as $o)
		{
			$t->define_data(array(
				"oid" => $o->id(),
				"name" => $o->name(),
				"choose" => html::href(array(
					"caption" => t("Vali see"),
					"url" => $this->mk_my_orb("search_categories",
						array(
							"result" => $o->id(),
							"category" => $arr["category"],
						), "shop_warehouse"
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

	/**
		@attrib name=activate_warehouse_items params=pos
		@param sel required type=array
		@param post_ru required type=string
	**/
	public function activate_warehouse_items($arr)
	{
		if (!empty($arr["sel"]))
		{
			$ol = new object_list(array(
				"oid" => $arr["sel"],
				"status" => new obj_predicate_not(object::STAT_ACTIVE),
			));
			if ($ol->count() > 0)
			{
				$o = $ol->begin();
				do
				{
					$o->set_status(object::STAT_ACTIVE);
					$o->save();
				} while ($o = $ol->next());
			}
		}

		return $arr["post_ru"];
	}

	/**
		@attrib name=deactivate_warehouse_items params=pos
		@param sel required type=array
		@param post_ru required type=string
	**/
	public function deactivate_warehouse_items($arr)
	{
		if (!empty($arr["sel"]))
		{
			$ol = new object_list(array(
				"oid" => $arr["sel"],
				"status" => new obj_predicate_not(object::STAT_NOTACTIVE),
			));
			if ($ol->count() > 0)
			{
				$o = $ol->begin();
				do
				{
					$o->set_status(object::STAT_NOTACTIVE);
					$o->save();
				} while ($o = $ol->next());
			}
		}

		return $arr["post_ru"];
	}
	

	/**
		@attrib name=ajax_set_property all_args=1
	**/
	public function ajax_set_property($arr)
	{
		$shop = obj($arr["id"]);
		foreach($arr["sel"] as $id)
		{
			if($this->can("view" , $id))
			{
				$o = obj($id);
				switch($o->class_id())
				{
					case CL_SHOP_PACKET:
						foreach($arr as $key => $val)
						{
							switch($key)
							{
								case "active":
									$o->set_prop("status" , $val);
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
}
