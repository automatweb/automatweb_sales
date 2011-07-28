<?php
/*
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_ADD_FROM, CL_SHOP_PRODUCT, on_add_alias)

@classinfo syslog_type=ST_SHOP_PRODUCT relationmgr=yes prop_cb=1
@extends applications/shop/shop_warehouse_item

@tableinfo aw_shop_products index=aw_oid master_table=objects master_index=brother_of
@tableinfo aw_account_balances master_index=oid master_table=objects index=aw_oid

@default table=objects

@default group=general_general

	@layout gen_split type=hbox

		@layout gen_left type=vbox parent=gen_split

		@layout gen_gen type=vbox area_caption=&Uuml;ldine closeable=1 parent=gen_left

			@property name type=textbox parent=gen_gen
			@caption Nimi
			@comment Objekti nimi

			@property status_edit type=chooser parent=gen_gen table=objects field=status
			@caption Staatus

			@property comment type=textbox parent=gen_gen
			@caption Kommentaar
			@comment Vabas vormis tekst objekti kohta

			@property status type=status default=1 parent=gen_gen
			@caption Aktiivne
			@comment Kas objekt on aktiivne

			@property jrk type=textbox size=5 table=objects field=jrk parent=gen_gen
			@caption J&auml;rjekord
			@comment Objekti j&auml;rjekord

			@property description type=textarea cols=40 rows=5 table=aw_shop_products parent=gen_gen
			@caption Kirjeldus
			@comment Toote kirjeldus

			@property content_package type=checkbox ch_value=1 parent=gen_gen disabled=1 table=aw_shop_products
			@caption Sisupaketi tooteobjekt

			@property short_name type=textbox table=aw_shop_products field=aw_short_name parent=gen_gen
			@caption L&uuml;hinimetus

			@property item_type type=relpicker reltype=RELTYPE_ITEM_TYPE table=aw_shop_products field=aw_type_id parent=gen_gen
			@caption Toote t&uuml;&uuml;p

		@layout gen_prices type=vbox area_caption=Hinnad closeable=1 parent=gen_left

			@layout gen_prices_prop type=hbox parent=gen_prices
			
				@layout gen_prices_prop_left type=vbox parent=gen_prices_prop
	
					@property price_settings type=relpicker automatic=1 table=objects field=meta method=serialize reltype=RELTYPE_PRICE_SETTINGS parent=gen_prices_prop_left
					@caption Hinnakujundus
		
					@property tax_rate type=relpicker automatic=1 table=objects field=meta method=serialize reltype=RELTYPE_TAX_RATE parent=gen_prices_prop_left
					@caption Maksum&auml;&auml;r
		
				@layout gen_prices_prop_right type=vbox parent=gen_prices_prop
	
					@property price type=textbox table=aw_shop_products field=price size=10 parent=gen_prices_prop_right
					@caption Hind
					@comment M&uuml;&uuml;gi hind

					@property special_price type=textbox table=aw_shop_products field=aw_special_price size=10 parent=gen_prices_prop_right
					@caption Erihind
		
					@property purchase_price type=textbox table=aw_shop_products field=purchase_price size=10 parent=gen_prices_prop_right
					@caption Ostu hind

			@layout gen_prices_tbl type=vbox parent=gen_prices

				@property prev_sales_price_tbl type=table store=no parent=gen_prices_tbl no_caption=1
	
				@property prev_purchase_price_tbl type=table store=no no_caption=1 parent=gen_prices_tbl
	
				@property price_cur type=table store=no parent=gen_prices_tbl no_caption=1

		@layout gen_right type=vbox parent=gen_split

		@layout gen_data type=vbox area_caption=Andmed closeable=1 parent=gen_right

			@property code type=textbox table=aw_shop_products field=code parent=gen_data
			@caption Kood

			@property short_code type=textbox table=aw_shop_products field=short_code parent=gen_data
			@caption L&uuml;hikood

			@property barcode type=textbox table=aw_shop_products field=barcode parent=gen_data
			@caption Ribakood

			@property type_code type=textbox table=aw_shop_products field=type_code parent=gen_data
			@caption T&uuml;&uuml;bi kood

			@property brand type=relpicker reltype=RELTYPE_BRAND store=connect  parent=gen_data multiple=1
			@caption Brand

			@property brand_series type=text store=no parent=gen_data
			@caption Brandisarjad

		@layout gen_wh type=vbox area_caption=Lao&nbsp;andmed closeable=1 parent=gen_right

			@layout gen_wh_split type=hbox parent=gen_wh

			@layout gen_wh_left type=vbox parent=gen_wh_split

			@property min_order_amt type=textbox table=aw_shop_products field=aw_min_order_amt parent=gen_wh_left size=5 captionside=top
			@caption Minimaalne tellimiskogus

			@property max_order_amt type=textbox table=aw_shop_products field=aw_max_order_amt parent=gen_wh_left size=5 captionside=top
			@caption Maksimaalne tellimiskogus

			@property must_order_num type=textbox table=aw_shop_products field=must_order_num size=5 parent=gen_wh_left captionside=top
			@caption Mitu peab korraga tellima

			@layout gen_wh_right type=vbox parent=gen_wh_split

			@property min_wh_order_amt type=textbox table=aw_shop_products field=aw_min_wh_order_amt parent=gen_wh_right size=5 captionside=top
			@caption Minimaalne sisseostu kogus

			@property serial_number_based type=chooser table=aw_shop_products field=aw_serial_number_based parent=gen_wh_right captionside=top
			@caption Seerianumbrip&otilde;hine arvestus

			@property order_based type=chooser table=aw_shop_products field=aw_order_based parent=gen_wh_right captionside=top
			@caption Partiip&otilde;hine arvestus

			@property units_tbl type=table store=no no_caption=1 parent=gen_wh

	@property balance type=hidden table=aw_account_balances field=aw_balance

	@property item_count type=hidden table=aw_shop_products field=aw_count
	@caption Mitu laos

@default group=general_categories

	@property categories_toolbar type=toolbar store=no no_caption=1
	@property categories_table type=table store=no no_caption=1
	@property categories type=relpicker reltype=RELTYPE_CATEGORY multiple=1 store=connect


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


@default group=general_limits

	@property amount_limits type=hidden store=no

	@property amount_limits_tb type=toolbar no_caption=1

	@property aml_inheritable type=checkbox ch_value=1 field=aml_inheritable table=aw_shop_products
	@caption P&auml;ritav

	@property inherit_aml_from type=relpicker reltype=RELTYPE_INHERIT_AML_FROM store=connect no_edit=1
	@caption P&auml;ri kogusepiirangud

	@property amount_limits_tbl type=table no_caption=1 store=no

	@property wh_minimum type=textbox datatype=int table=aw_shop_products
	@caption Miinimumkogus laos

	@property wh_maximum type=textbox datatype=int table=aw_shop_products
	@caption Maksimumkogus laos

	@property amount_limits_re type=releditor reltype=RELTYPE_AMOUNT_LIMIT mode=manager props=name,start,end,time,length,recur_type,recur_interval table_fields=name,start,end,time,length,recur_type,recur_interval
	@caption Kogusepiirangud


@default group=general_match_prod

	@property match_prod type=releditor reltype=RELTYPE_MATCH_PROD mode=manager props=jrk,name table_fields=name,jrk table_edit_fields=jrk field=meta method=serialize table=objects clone_link=1 filt_edit_fields=1 direct_links=1
	@caption Kokkusobivad tooted

@default group=general_replacement_prods

	@property replacement_prods_tb type=toolbar no_caption=1
	@caption Asendustoodete t&ouml;&ouml;riistariba

	@property replacement_prods type=table no_caption=1
	@caption Asendustooted

@default group=packaging

	@property packaging type=releditor reltype=RELTYPE_PACKAGING props=jrk,name,size,price,user1,user2,user3,user4,user5,userta1,userta2,userta3,userta4,userta5,uservar1,uservar2,uservar3,uservar4,uservar5 group=packaging mode=manager field=meta method=serialize table=objects table_edit_fields=jrk table_fields=name,jrk clone_link=1 filt_edit_fields=1 direct_links=1
	@caption Pakendid

@default group=singles

	@property singles_toolbar type=toolbar no_caption=1 store=no
	@property singles_table type=table store=no no_caption=1



@default group=data

@property color type=relpicker reltype=RELTYPE_COLOR field=color table=aw_shop_products multiple=1
@caption V&auml;rvus

@property height type=textbox field=height table=aw_shop_products
@caption K&otilde;rgus

@property width type=textbox field=width table=aw_shop_products
@caption Laius

@property depth type=textbox field=depth table=aw_shop_products
@caption S&uuml;gavus

@property wideness type=textbox field=wideness table=aw_shop_products
@caption Paksus

@property density type=textbox field=density table=aw_shop_products
@caption Tihedus

@property weight type=textbox field=weight table=aw_shop_products
@caption Kaal

@property gramweight type=textbox field=gramweight table=aw_shop_products
@caption Grammkaal

@property raster type=textbox field=raster table=aw_shop_products
@caption Rasteritihedus

@property bulk type=textbox field=bulk table=aw_shop_products
@caption Mahulisus

@property guarantee type=textbox field=guarantee table=aw_shop_products
@caption Garantii kuudes

@property userch1 type=checkbox ch_value=1 table=aw_shop_products field=userch1 group=data datatype=int
@caption User-defined checkbox 1

@property userch2 type=checkbox ch_value=1 table=aw_shop_products field=userch2 group=data datatype=int
@caption User-defined checkbox 2

@property userch3 type=checkbox ch_value=1 table=aw_shop_products field=userch3 group=data datatype=int
@caption User-defined checkbox 3

@property userch4 type=checkbox ch_value=1 table=aw_shop_products field=userch4 group=data datatype=int
@caption User-defined checkbox 4

@property userch5 type=checkbox ch_value=1 table=aw_shop_products field=userch5 group=data datatype=int
@caption User-defined checkbox 5

@property userch6 type=checkbox ch_value=1 table=aw_shop_products field=userch6 group=data datatype=int
@caption User-defined checkbox 6

@property userch7 type=checkbox ch_value=1 table=aw_shop_products field=userch7 group=data datatype=int
@caption User-defined checkbox 7

@property userch8 type=checkbox ch_value=1 table=aw_shop_products field=userch8 group=data datatype=int
@caption User-defined checkbox 8

@property userch9 type=checkbox ch_value=1 table=aw_shop_products field=userch9 group=data datatype=int
@caption User-defined checkbox 9

@property userch10 type=checkbox ch_value=1 table=aw_shop_products field=userch10 group=data datatype=int
@caption User-defined checkbox 10

@property user1 type=textbox table=aw_shop_products field=user1 group=data
@caption User-defined 1

@property user2 type=textbox table=aw_shop_products field=user2 group=data
@caption User-defined 2

@property user3 type=textbox table=aw_shop_products field=user3 group=data
@caption User-defined 3

@property user4 type=textbox table=aw_shop_products field=user4 group=data
@caption User-defined 4

@property user5 type=textbox table=aw_shop_products field=user5 group=data
@caption User-defined 5

@property user6 type=textbox table=aw_shop_products field=user6 group=data
@caption User-defined 6

@property user7 type=textbox table=aw_shop_products field=user7 group=data
@caption User-defined 7

@property user8 type=textbox table=aw_shop_products field=user8 group=data
@caption User-defined 8

@property user9 type=textbox table=aw_shop_products field=user9 group=data
@caption User-defined 9

@property user10 type=textbox table=aw_shop_products field=user10 group=data
@caption User-defined 10

@property user11 type=textbox table=aw_shop_products field=user11 group=data
@caption User-defined 11

@property user12 type=textbox table=aw_shop_products field=user12 group=data
@caption User-defined 12

@property user13 type=textbox table=aw_shop_products field=user13 group=data
@caption User-defined 13

@property user14 type=textbox table=aw_shop_products field=user14 group=data
@caption User-defined 14

@property user15 type=textbox table=aw_shop_products field=user15 group=data
@caption User-defined 15

@property user16 type=textbox table=aw_shop_products field=user16 group=data
@caption User-defined 16

@property user17 type=textbox table=aw_shop_products field=user17 group=data
@caption User-defined 17

@property user18 type=textbox table=aw_shop_products field=user18 group=data
@caption User-defined 18

@property user19 type=textbox table=aw_shop_products field=user19 group=data
@caption User-defined 19

@property user20 type=textbox table=aw_shop_products field=user20 group=data
@caption User-defined 20


@property userta1 type=textarea table=aw_shop_products field=tauser1 group=data
@caption User-defined ta 1

@property userta2 type=textarea table=aw_shop_products field=tauser2 group=data
@caption User-defined ta 2

@property userta3 type=textarea table=aw_shop_products field=tauser3 group=data
@caption User-defined ta 3

@property userta4 type=textarea table=aw_shop_products field=tauser4 group=data
@caption User-defined ta 4

@property userta5 type=textarea table=aw_shop_products field=tauser5 group=data
@caption User-defined ta 5

@property userta6 type=textarea table=aw_shop_products field=tauser6 group=data
@caption User-defined ta 6

@property userta7 type=textarea table=aw_shop_products field=tauser7 group=data
@caption User-defined ta 7

@property userta8 type=textarea table=aw_shop_products field=tauser8 group=data
@caption User-defined ta 8

@property userta9 type=textarea table=aw_shop_products field=tauser9 group=data
@caption User-defined ta 9

@property userta10 type=textarea table=aw_shop_products field=tauser10 group=data
@caption User-defined ta 10

@property uservar1 type=classificator table=aw_shop_products field=varuser1 group=data
@caption User-defined var 1

@property uservar2 type=classificator table=aw_shop_products field=varuser2 group=data
@caption User-defined var 2

@property uservar3 type=classificator table=aw_shop_products field=varuser3 group=data
@caption User-defined var 3

@property uservar4 type=classificator table=aw_shop_products field=varuser4 group=data
@caption User-defined var 4

@property uservar5 type=classificator table=aw_shop_products field=varuser5 group=data
@caption User-defined var 5

@property uservar6 type=classificator table=aw_shop_products field=varuser6 group=data
@caption User-defined var 6

@property uservar7 type=classificator table=aw_shop_products field=varuser7 group=data
@caption User-defined var 7

@property uservar8 type=classificator table=aw_shop_products field=varuser8 group=data
@caption User-defined var 8

@property uservar9 type=classificator table=aw_shop_products field=varuser9 group=data
@caption User-defined var 9

@property uservar10 type=classificator table=aw_shop_products field=varuser10 group=data
@caption User-defined var 10


@property uservarm1 type=classificator table=aw_shop_products field=varuserm1 group=data store=connect reltype=RELTYPE_VARUSERM1
@caption User-defined var (multiple) 1

@property uservarm2 type=classificator table=aw_shop_products field=varuserm2 group=data store=connect reltype=RELTYPE_VARUSERM2
@caption User-defined var (multiple) 2

@property uservarm3 type=classificator table=aw_shop_products field=varuserm3 group=data store=connect reltype=RELTYPE_VARUSERM3
@caption User-defined var (multiple) 3

@property uservarm4 type=classificator table=aw_shop_products field=varuserm4 group=data store=connect reltype=RELTYPE_VARUSERM4
@caption User-defined var (multiple) 4

@property uservarm5 type=classificator table=aw_shop_products field=varuserm5 group=data store=connect reltype=RELTYPE_VARUSERM5
@caption User-defined var (multiple) 5

@property uservarm6 type=classificator table=aw_shop_products field=varuserm6 group=data store=connect reltype=RELTYPE_VARUSERM6
@caption User-defined var (multiple) 6

@property uservarm7 type=classificator table=aw_shop_products field=varuserm7 group=data store=connect reltype=RELTYPE_VARUSERM7
@caption User-defined var (multiple) 7

@property uservarm8 type=classificator table=aw_shop_products field=varuserm8 group=data store=connect reltype=RELTYPE_VARUSERM8
@caption User-defined var (multiple) 8

@property uservarm9 type=classificator table=aw_shop_products field=varuserm9 group=data store=connect reltype=RELTYPE_VARUSERM9
@caption User-defined var (multiple) 9

@property uservarm10 type=classificator table=aw_shop_products field=varuserm10 group=data store=connect reltype=RELTYPE_VARUSERM10
@caption User-defined var (multiple) 10

@property search_term type=hidden table=aw_shop_products field=search_term

@default group=img

	@property images type=releditor reltype=RELTYPE_IMAGE field=meta method=serialize mode=manager props=name,ord,status,file,file2,new_w,new_h,new_w_big,new_h_big,comment,transl group=img table_fields=name,ord table_edit_fields=ord
	@caption Pildid

@default group=fls

	@property files type=releditor reltype=RELTYPE_FILE field=meta method=serialize mode=manager props=name,file,type,comment,file_url,newwindow group=fls table_fields=name
	@caption Failid

@default group=doc

	@property docs type=releditor reltype=RELTYPE_DOC field=meta method=serialize mode=manager props=title,lead,content group=doc table_fields=title
	@caption Dokumendid

@default group=lnk

	@property lnk type=releditor reltype=RELTYPE_LNK field=meta method=serialize mode=manager props=name,url,newwindow group=lnk table_fields=name,url,newwindow
	@caption Lingid

@default group=purveyors

	@property companies_tb type=toolbar no_caption=1 store=no
	@property companies_tbl type=table no_caption=1 store=no

@default group=materials

	@property materials_tb type=toolbar no_caption=1 store=no
	@property materials_tbl type=table no_caption=1 store=no

@default group=brands

	@property brands_tb type=toolbar no_caption=1 store=no
	@property brands_tbl type=table no_caption=1 store=no

@default group=transl

	@property transl type=callback callback=callback_get_transl store=no
	@caption T&otilde;lgi



// general subs
	@groupinfo general_general caption="&Uuml;ldine" parent=general
	@groupinfo general_categories caption="Kategooriad" parent=general
	@groupinfo general_time_settings caption="Ajaseaded" parent=general
	@groupinfo general_limits caption="Kogusepiirangud" parent=general
	@groupinfo general_match_prod caption="Kokkusobivad tooted" parent=general
	@groupinfo general_replacement_prods caption="Asendustooted" parent=general

@groupinfo packaging caption="Pakendid"
@groupinfo singles caption="&Uuml;ksiktooted"
@groupinfo moreinfo caption="Lisainfo"
	@groupinfo img caption="Pildid" parent=moreinfo
	@groupinfo fls caption="Failid" parent=moreinfo
	@groupinfo doc caption="Dokumendid" parent=moreinfo
	@groupinfo lnk caption="Lingid" parent=moreinfo
	@groupinfo data caption="Andmed" parent=moreinfo
	@groupinfo purveyors caption="Tarnijad" parent=moreinfo
	@groupinfo materials caption="Materjalid" parent=moreinfo
	@groupinfo brands caption="Br&auml;ndid" parent=moreinfo

#	Inherited from shop_warehouse_item
@groupinfo purveyance

@groupinfo transl caption=T&otilde;lgi

@reltype IMAGE value=1 clid=CL_IMAGE
@caption pilt

@reltype PACKAGING value=2 clid=CL_SHOP_PRODUCT_PACKAGING
@caption pakend

@reltype VARUSERM1 value=3 clid=CL_META
@caption kasutajadefineeritud muutuja 1

@reltype VARUSERM2 value=4 clid=CL_META
@caption kasutajadefineeritud muutuja 2

@reltype VARUSERM3 value=5 clid=CL_META
@caption kasutajadefineeritud muutuja 3

@reltype VARUSERM4 value=6 clid=CL_META
@caption kasutajadefineeritud muutuja 4

@reltype VARUSERM5 value=7 clid=CL_META
@caption kasutajadefineeritud muutuja 5

@reltype FILE value=8 clid=CL_FILE
@caption fail

@reltype DOC value=9 clid=CL_DOCUMENT
@caption dokument

@reltype LNK value=10 clid=CL_EXTLINK
@caption link

@reltype KW value=11 clid=CL_KEYWORD
@caption v&otilde;tmes&otilde;na

@reltype VARUSERM6 value=12 clid=CL_META
@caption kasutajadefineeritud muutuja 6

@reltype VARUSERM7 value=13 clid=CL_META
@caption kasutajadefineeritud muutuja 7

@reltype VARUSERM8 value=14 clid=CL_META
@caption kasutajadefineeritud muutuja 8

@reltype PRICE_SETTINGS value=15 clid=CL_SHOP_PRICE_MODIFIER,CL_SHOP_PRICE_MODIFIER_WINDOW
@caption hinnakujundus

@reltype VARUSERM9 value=16 clid=CL_META
@caption kasutajadefineeritud muutuja 9

@reltype VARUSERM10 value=17 clid=CL_META
@caption kasutajadefineeritud muutuja 10

@reltype TAX_RATE value=18 clid=CL_CRM_TAX_RATE
@caption Maksum&auml;&auml;r

@reltype MATCH_PROD value=19 clid=CL_SHOP_PRODUCT,CL_SHOP_PRODUCT_PACKAGING
@caption Kokkusobiv toode

@reltype BRAND value=20 clid=CL_SHOP_BRAND
@caption Brand

@reltype ITEM_TYPE value=21 clid=CL_SHOP_PRODUCT_TYPE
@caption Toote t&uuml;&uuml;p

@reltype REPLACEMENT_PROD value=22 clid=CL_SHOP_PRODUCT
@caption Asendustoode

@reltype CATEGORY value=23 clid=CL_SHOP_PRODUCT_CATEGORY
@caption Kategooria

@reltype PRICE value=24 clid=CL_SHOP_ITEM_PRICE
@caption Hind

#	Inherited from shop_warehouse_item
#reltype WAREHOUSE value=25 clid=CL_SHOP_WAREHOUSE
#caption Ladu

@reltype COLOR value=26 clid=CL_SHOP_COLOUR
@caption V&auml;rvus

@reltype PURVEYOR value=27 clid=CL_CRM_COMPANY
@caption Tarnija

@reltype MATERIAL value=28 clid=CL_SHOP_MATERIAL
@caption Materjal

@reltype INHERIT_AML_FROM value=29 clid=CL_SHOP_PRODUCT,CL_SHOP_PRODUCT_PACKAGING
@caption P&auml;ri kogusepiirangud

@reltype UNIT_FORMULA value=30 clid=CL_SHOP_UNIT_FORMULA
@caption &Uuml;hikute valem

@reltype AMOUNT_LIMIT value=31 clid=CL_SHOP_AMOUNT_LIMIT
@caption Kogusepiirang
*/

class shop_product extends shop_warehouse_item
{
	private $cfgforms = array();

	function shop_product()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_product",
			"clid" => CL_SHOP_PRODUCT
		));

		$this->trans_props = array(
			"name","comment", "userta1"
		);
	}

	function callback_on_load($arr)
	{
		if(is_oid($arr["request"]["id"]) && $this->can("view", $arr["request"]["id"]))
		{
			$obj = obj($arr["request"]["id"]);
			$conns = $obj->connections_to(array(
				"from.class_id" => CL_SHOP_WAREHOUSE,
				"type" => 2 //RELTYPE_SHOP_PRODUCT
			));
			if($ware = reset($conns))
			{
				$warehouse = $ware->from();
				$conf = $warehouse->prop("conf");
				if(is_oid($conf) && $this->can("view", $conf))
				{
					$conf = obj($conf);;
					$fld = $conf->prop("prod_conf_folder");
					if(is_oid($fld) && $this->can("view", $fld))
					{
						$cfgforms = new object_list(array(
							"parent" => $fld,
							"class_id" => CL_CFGFORM,
						));
						foreach($cfgforms->arr() as $form)
						{
							// this is probably the fastest way :(
							switch($form->subclass())
							{
								case CL_IMAGE:
									$var = "RELTYPE_IMAGE";
									break;
								case CL_EXTLINK:
									$var = "RELTYPE_LNK";
									break;
								case CL_DOCUMENT:
									$var = "RELTYPE_DOC";
									break;
								case CL_FILE:
									$var = "RELTYPE_FILE";
									break;
							}
							$this->cfgforms[$var] = $form->id();
						}
					}
				}
			}
		}
	}

	function get_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "content_package":
				unset($data["disabled"]);
				break;

			case "inherit_aml_from":
				$ol = new object_list(array(
					"class_id" => array(CL_SHOP_PRODUCT, CL_SHOP_PRODUCT_PACKAGING),
					"aml_inheritable" => 1,
					"lang_id" => array(),
					"site_id" => array(),
				));
				$data["options"] = array("" => t("--vali--")) + $ol->names();
				break;

			case "price_cur":
				$this->_price_cur($arr);
			break;
			case "buffer_time_unit":
			case "reservation_time_unit":
				$data["options"] = array(
					60 => t("Minutit"),
					3600 => t("Tundi"),
				);
				break;
			case "packaging":
				// get item type and cfgform from that
				if (is_oid($arr["obj_inst"]->prop("item_type")) && $this->can("view", $arr["obj_inst"]->prop("item_type")))
				{
					$ityp = obj($arr["obj_inst"]->prop("item_type"));
					$data["cfgform"] = $ityp->prop("packaging_cfgform");
					$data["direct_links"] = 1;
				}
				$data["caption"] = sprintf("Pakendite valik tootele \"%s\"", $arr["obj_inst"]->name());
				break;

			case "images":
			case "docs":
				// get item type and cfgform from that
				$data["direct_links"] = 1;
			case "lnk":
			case "files":
				$data["cfgform_id"] = $this->cfgforms[$data["reltype"]];
				break;

			case "item_type":
				if ($arr["new"])
				{
					$data["value"] = $arr["request"]["item_type"];
				}
				if (isset($data["value"]) and !isset($data["options"][$data["value"]]) and $this->can("view", $data["value"]))
				{
					$data["options"][$data["value"]] = obj($data["value"])->name;
				}
				break;
			case "price_cur":
				$this->_price_cur($arr);
				break;
			case "serial_number_based":
			case "order_based":
				$data["options"] = array(
					1 => t("Jah"),
					0 => t("Ei"),
				);
				if(empty($data["value"]))
				{
					$data["value"] = 0;
				}
				break;
		};
		return $retval;
	}

	function _get_categories($arr)
	{
		return PROP_IGNORE;
	}

	function _set_categories($arr)
	{
		return PROP_IGNORE;
	}

	function _get_units_tbl($arr)
	{
		if($arr["request"]["action"] == "new")
		{
			return PROP_IGNORE;
		}
		$t = $arr["prop"]["vcl_inst"];
		$t->set_caption(t("&Uuml;hikud"));

		$count = 0;

		if($wh = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_WAREHOUSE"))
		{
			$conf = $wh->prop("conf");
			if($this->can("view", $conf))
			{
				$confo = obj($conf);
				$count += $confo->prop("alternative_unit_levels");
			}
		}
		$units = $this->get_units($arr["obj_inst"]);
		$ui = get_instance(CL_UNIT);
		$unitnames = $ui->get_unit_list(true);
		for($i = 0; $i<=$count; $i++)
		{
			$t->define_field(array(
				"name" => "unit_".$i,
				"caption" => $i? sprintf(t("Alternatiiv&uuml;hik %s"), $i) : t("P&otilde;hi&uuml;hik"),
				"align" => "center",
			));
			$data["unit_".$i] = html::select(array(
				"options" => $unitnames,
				"value" => ifset($units, $i),
				"name" => "set_units[".$i."]",
			));
		}
		$t->define_data($data);
	}

	function _set_units_tbl($arr)
	{
		$arr["obj_inst"]->set_meta("units", $arr["request"]["set_units"]);
		$arr["obj_inst"]->save();
	}

	function _get_companies_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_search_button(array(
			"pn" => "add_purveyor",
			"clid" => CL_CRM_COMPANY,
			"multiple" => 1,
		));
		$tb->add_delete_rels_button();
	}

	function _get_companies_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
		));
		$t->define_field(array(
			"name" => "name",
			"align" => "center",
			"caption" => t("Nimi"),
		));
		$t->define_field(array(
			"name" => "manufacturer",
			"align" => "center",
			"caption" => t("Tootja"),
		));
		$mco = $arr["obj_inst"]->meta("manufacturer");
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_PURVEYOR")) as $c)
		{
			$co = $c->to();
			$t->define_data(array(
				"oid" => $co->id(),
				"name" => html::obj_change_url($co),
				"manufacturer" => html::checkbox(array(
					"name" => "manufacturer[".$co->id()."]",
					"value" => 1,
					"checked" => ($mco[$co->id()])?1:0,
				)),
			));
		}
	}

	function _set_companies_tbl($arr)
	{
		if($co = $arr["request"]["add_purveyor"])
		{
			$tmp = explode(",", $co);
			foreach($tmp as $co)
			{
				$arr["obj_inst"]->connect(array(
					"to" => $co,
					"type" => "RELTYPE_PURVEYOR",
				));
			}
		}
		$arr["obj_inst"]->set_meta("manufacturer", $arr["request"]["manufacturer"]);
		$arr["obj_inst"]->save();
	}

	function _get_materials_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_new_button(array(CL_SHOP_MATERIAL), $arr["obj_inst"]->id(), 28);
		$tb->add_search_button(array(
			"pn" => "add_material",
			"clid" => CL_SHOP_MATERIAL,
			"multiple" => 1,
		));
		$tb->add_delete_rels_button();
	}

	function _get_materials_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
		));
		$t->define_field(array(
			"name" => "name",
			"align" => "center",
			"caption" => t("Nimi"),
		));
		$t->define_field(array(
			"name" => "desc",
			"align" => "center",
			"caption" => t("Kirjeldus"),
		));
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_MATERIAL")) as $c)
		{
			$o = $c->to();
			$t->define_data(array(
				"oid" => $o->id(),
				"name" => html::obj_change_url($o),
				"desc" => $o->prop("desc"),
			));
		}
	}

	function _set_materials_tbl($arr)
	{
		if($mats = $arr["request"]["add_material"])
		{
			$tmp = explode(",", $mats);
			foreach($tmp as $mat)
			{
				$arr["obj_inst"]->connect(array(
					"to" => $mat,
					"type" => "RELTYPE_MATERIAL",
				));
			}
		}
	}

	function _get_brands_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_search_button(array(
			"pn" => "add_brand",
			"clid" => CL_SHOP_BRAND,
			"multiple" => 1,
		));
		$tb->add_delete_rels_button();
	}

	function _get_brands_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
		));
		$t->define_field(array(
			"name" => "name",
			"align" => "center",
			"caption" => t("Nimi"),
		));
		$t->define_field(array(
			"name" => "series",
			"align" => "center",
			"caption" => t("Sarjad"),
		));
		$bs = $arr["obj_inst"]->meta("brand_series");
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_BRAND")) as $c)
		{
			$brand = $c->to();
			$res = "";
			foreach($brand->connections_from(array("type" => "RELTYPE_BRAND_SERIES")) as $c)
			{
				$series = $c->to();
				if(!$set[$series->id()])
				{
					$set[$series->id()] = 1;
					$res .= html::checkbox(array(
						"name" => "brand_series[".$brand->id() ."][".$series->id()."]",
						"value" => 1,
						"checked" => $bs[$series->id()]?1:0,
					))." ".$series->name()."<br>";
				}
			}
			$t->define_data(array(
				"oid" => $brand->id(),
				"name" => html::obj_change_url($brand),
				"series" => $res,
			));
		}
	}

	function _set_brands_tbl($arr)
	{
		if($bs = $arr["request"]["add_brand"])
		{
			$tmp = explode(",", $bs);
			foreach($tmp as $b)
			{
				$arr["obj_inst"]->connect(array(
					"to" => $b,
					"type" => "RELTYPE_BRAND",
				));
			}
		}
		$brand_series = array();
		$brands = array();
		foreach($arr["request"]["brand_series"] as $brandid => $series)
		{
			$brands[$brandid] = $brandid;
			foreach($series as $sid=>$serie)
			{
				$brand_series[$sid] = 1;
			}
		}
		$arr["obj_inst"]->set_meta("brand_series", $brand_series);
		$arr["obj_inst"]->save();
	}

	function _get_brand_series($arr)
	{
		$prop = &$arr["prop"];
		if(!($bids = $arr["obj_inst"]->prop("brand")))
		{
			return PROP_IGNORE;
		}
		foreach($bids as $bid)
		{
			$brands[] = obj($bid);
		}
		$res = "";
		$sdata = $arr["obj_inst"]->meta("brand_series");
		$set = array();
		foreach($brands as $brand)
		{
			foreach($brand->connections_from(array("type" => "RELTYPE_BRAND_SERIES")) as $c)
			{
				$series = $c->to();
				if(!$set[$series->id()])
				{
					$set[$series->id()] = 1;
					$res .= html::checkbox(array(
						"name" => "brand_series[".$series->id()."]",
						"value" => 1,
						"checked" => $sdata[$series->id()]?1:0,
					))." ".$series->name()."<br>";
				}
			}
		}
		$prop["value"] = $res;
	}

	function _set_brand_series($arr)
	{
		$arr["obj_inst"]->set_meta("brand_series", ifset($arr["request"], "brand_series"));
		$arr["obj_inst"]->save();
	}

	function _get_prev_sales_price_tbl($arr)
	{
		if($arr["request"]["action"] == "new")
		{
			return PROP_IGNORE;
		}
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "price",
			"caption" => t("Hind"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "currency",
			"caption" => t("Valuuta"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "valid_from",
			"caption" => t("Alates"),
			"align" => "center",
			"type" => "time",
			"format" => "d.m.Y",
		));
		$t->define_field(array(
			"name" => "valid_to",
			"caption" => t("Kuni"),
			"align" => "center",
			"type" => "time",
			"format" => "d.m.Y",
		));
		$t->set_caption(t("M&uuml;&uuml;gihinnad"));
		$currencies = new object_list(array(
			"class_id" => CL_CURRENCY,
			"site_id" => array(),
			"lang_id" => array(),
		));
		$t->define_data(array(
			"price" => html::textbox(array(
				"name" => "add_price[price]",
				"size" => 4,
			)),
			"currency" => html::select(array(
				"name" => "add_price[currency]",
				"options" => $currencies->names(),
			)),
		));
		$pr_ol = new object_list(array(
			"class_id" => CL_SHOP_ITEM_PRICE,
			"product" => $arr["obj_inst"]->id(),
			"sort_by" => "valid_to DESC",
			"limit" => 5,
			"site_id" => array(),
			"lang_id" => array(),
		));
		foreach($pr_ol->arr() as $o)
		{
			$url = html::get_change_url($o->id(), array("return_url" => get_ru()));
			$t->define_data(array(
				"price" => html::href(array(
					"caption" => ($p = $o->prop("price"))?$p:$p." ",
					"url" => $url,
				)),
				"currency" => $o->prop("currency.name"),
				"valid_from" => $o->prop("valid_from"),
				"valid_to" => $o->prop("valid_to"),
			));
		}
	}

	function _set_prev_sales_price_tbl($arr)
	{
		$add = $arr["request"]["add_price"];
		if(is_array($add))
		{
			foreach($add as $var => $val)
			{
				if($val && $add["price"])
				{
					$props[$var] = $val;
				}
			}
		}
		if(isset($props) and is_array($props))
		{
			$ol = new object_list(array(
				"class_id" => CL_SHOP_ITEM_PRICE,
				"site_id" => array(),
				"lang_id" => array(),
				"product" => $arr["obj_inst"]->id(),
				"valid_to" => new obj_predicate_compare(OBJ_COMP_GREATER, time()),
				"currency" => $props["currency"],
			));
			foreach($ol->arr() as $o)
			{
				$o->set_prop("valid_to", time());
				$o->save();
			}
			$o = obj();
			$o->set_class_id(CL_SHOP_ITEM_PRICE);
			$o->set_parent($arr["obj_inst"]->id());
			$o->set_name(sprintf(t("%s hind"), $arr["obj_inst"]->name()));
			foreach($props as $prop => $val)
			{
				$o->set_prop($prop, $val);
			}
			$o->set_prop("product", $arr["obj_inst"]->id());
			$o->set_prop("valid_from", time());
			$o->set_prop("valid_to", mktime(0,0,0,0,0,2037));
			$o->save();
			$arr["obj_inst"]->connect(array(
				"to" => $o->id(),
				"type" => "RELTYPE_PRICE",
			));
		}
	}

	function _get_prev_purchase_price_tbl($arr)
	{
		if($arr["request"]["action"] == "new")
		{
			return PROP_IGNORE;
		}
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "price",
			"caption" => t("Hind"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "currency",
			"caption" => t("Valuuta"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "date",
			"caption" => t("Kuup&auml;ev"),
			"align" => "center",
			"type" => "time",
			"format" => "d.m.Y",
		));
		$t->set_caption(t("Ostuhinnad"));
		$ol = new object_list(array(
			"class_id" => CL_SHOP_WAREHOUSE_MOVEMENT,
			"product" => $arr["obj_inst"]->id(),
			"from_wh" => 0,
			"to_wh" => new obj_predicate_compare(OBJ_COMP_GREATER, 0),
			"limit" => 5,
			"sort_by" => "objects.created DESC",
			"site_id" => array(),
			"lang_id" => array(),
		));
		$chk = array();
		foreach($ol->arr() as $o)
		{
			$date = $o->created();
			$price = $o->prop("price");
			if(isset($chk[$date][$price]))
			{
				continue;
			}
			$chk[$date][$price] = 1;
			$t->define_data(array(
				"price" => $price,
				"currency" => $o->prop("currency.name"),
				"date" => $date,
			));
		}
	}

	function _get_amount_limits_tb($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->add_search_button(array(
			"pn" => "amount_limits",
			"multiple" => "1",
			"clid" => CL_GROUP,
		));
		$t->add_button(array(
			"name" => "delete_ammout",
			"tooltip" => t("Kustuta"),
			"img" => "delete.gif",
			"action" => "delete_amounts",
			"confirm" => t("Kas olete kindel, et soovite kustutada kogusepiirangud valitud gruppidele?"),
		));
		$t->add_save_button();
	}

	function _get_amount_limits_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
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

	function _price_cur($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
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
		$t->set_caption(t("Hinnad valuutades"));
		$t->set_sortable(false);
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
	}

	function _get_replacement_prods_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];

		// 22 is RELTYPE_REPLACEMENT_PROD relation type
		$tb->add_new_button(array(CL_SHOP_PRODUCT), $arr['obj_inst']->id(), 22);

		// search button
		$tb->add_search_button(array(
			'name' => 'add_replacement_product',
			'tooltip' => t('Otsi asendustoode'),
			'pn' => 'add_replacement_product',
			'clid' => CL_SHOP_PRODUCT
		));

		// delete button
		$tb->add_button(array(
			"name" => "delete_replacement_prods_rels",
			"img" => "delete.gif",
			"action" => "delete_replacement_prods_rels",
			"tooltip" => t("Kustuta asendustoodete seos"),
			"confirm" => t("Oled sa kindel, et tahad asendustoote seose kustutada?"),
		));

	}
	
	function _set_replacement_prods_tb($arr)
	{
		if($add = $arr["request"]["add_replacement_product"])
		{
			$tmp = explode(",", $add);
			foreach($tmp as $oid)
			{
				if(!$arr["obj_inst"]->is_connected_to(array("to" => $oid)))
				{
					$arr["obj_inst"]->connect(array(
						"type" => "RELTYPE_REPLACEMENT_PROD",
						"to" => $oid,
					));
				}
			}
		}
	}

	/**
		@attrib name=delete_replacement_prods_rels params=name 

		@param sel required type=array
			The array of select object ids, which relations will be deleted
		
	**/
	function delete_replacement_prods_rels($arr)
	{
		$prod = new object($arr['id']);
		foreach (safe_array($arr['sel']) as $oid)
		{
			if ($prod->is_connected_to(array('to' => $oid)))
			{
				$prod->disconnect(array('from' => $oid));
			}

			// lets remove the type code from the replacement product
			$o = new object($oid);
			$o->set_prop('type_code', '');
			$o->save();
		}
		return $arr['post_ru'];
	}

	function _get_replacement_prods($arr)
	{
		$t = $arr['prop']['vcl_inst'];
		$t->set_sortable(false);
		$t->set_caption(sprintf(t("Toote <strong>%s (%s)</strong> asendustooted"), $arr['obj_inst']->name(), $arr['obj_inst']->prop('code')));

		$t->define_chooser(array(
			'name' => 'sel',
			'field' => 'oid',
			'chgbgcolor' => 'highlight'
		));
		$t->define_field(array(
			'name' => 'name',
			'caption' => t('Nimi'),
			'chgbgcolor' => 'highlight'
		));
		$t->define_field(array(
			'name' => 'code',
			'caption' => t('Tootekood'),
			'chgbgcolor' => 'highlight'
		));
		$t->define_field(array(
			'name' => 'type_code',
			'caption' => t('Toote t&uuml;&uuml;bi kood (Peatoode)'),
			'chgbgcolor' => 'highlight'
		));
		$t->define_field(array(
			'name' => 'connection_type',
			'caption' => t('Seose t&uuml;&uuml;p'),
			'chgbgcolor' => 'highlight'
		));

		$prods = $arr['obj_inst']->get_replacement_products();
		foreach ($prods as $oid => $o)
		{	
			$connection_type = array();
			$highlight = '';
			if ($arr['obj_inst']->prop('type_code') != '' && $arr['obj_inst']->prop('type_code') == $o->prop('type_code'))
			{
				$connection_type[] = t('T&uuml;&uuml;bi kood');
				if ($o->prop('code') == $o->prop('type_code'))
				{
					$highlight = 'orange';
				}
			}

			if ($arr['obj_inst']->is_connected_to(array('to' => $oid)))
			{
				$connection_type[] = t('Seos');
			}

			$t->define_data(array(
				'oid' => $oid,
				'name' => html::href(array(
					'caption' => $o->name(),
					'url' => $this->mk_my_orb('change', array('id' => $oid, 'return_url' => get_ru()), CL_SHOP_PRODUCT)
					)),
				'code' => $o->prop('code'),
				'type_code' => $o->prop('type_code'),
				'connection_type' => implode(' / ', $connection_type),
				'highlight' => $highlight
			));
		}
		return PROP_OK;
	}

	function set_property($arr = array())
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "content_package":

			//	$retval = PROP_IGNORE;
				break;

			case "amount_limits":
				$this->_set_amount_limits($arr);
				break;

			case "amount_limits_tbl":
				if(empty($arr["request"]["amount_limits"]))
					$arr["obj_inst"]->set_meta("amount_limits", $arr["request"]["limits"]);
				break;

			case "transl":
				$this->trans_save($arr, $this->trans_props);
				break;

			case "price_cur":
				$arr["obj_inst"]->set_meta("cur_prices", $arr["request"]["cur_prices"]);
				break;

		}
		return $retval;
	}

	function _set_amount_limits($arr)
	{
		$gs = explode(",", $arr["prop"]["value"]);
		$limits = $arr["obj_inst"]->meta("amount_limits");
		foreach($gs as $g)
		{
			if(!is_array($limits[$g]) && is_oid($g))
			{
				$limits[$g]["min"] = 0;
				$limits[$g]["max"] = 0;
			}
		}
		$arr["obj_inst"]->set_meta("amount_limits", $limits);
	}

	function get_price($o)
	{
		return number_format(str_replace(",","",$o->prop("price")),2);
	}

	function get_calc_price($o)
	{
		return str_replace(",","",$o->prop("price"));
	}

	/** returns the html for the product

		@comment

			uses the $layout object to draw the product $prod
			from the layout reads the template and inserts correct vars
			optionally you can give the $quantity parameter
			$oc_obj must be the order center object via what the product is drawn

	**/
	function do_draw_product($arr)
	{
		extract($arr);
		enter_function("shop_product::do_draw_product");
		$tmp = $this->do_final_draw_product($arr);
		exit_function("shop_product::do_draw_product");
		return $tmp;
	}

	function show_change_button(&$arr)
	{
		$conns = $arr["oc_obj"]->connections_from(array(
			"type" => "RELTYPE_WAREHOUSE"
		));

		$persons = array();
		foreach($conns as $warehouse)
		{
			$warehouse = $warehouse->to();
			$conns2 = $warehouse->connections_from(array(
				'type' => 'RELTYPE_CONFIG'
			));

			foreach($conns2 as $config)
			{
				$config = $config->to();
				$conns3 = $config->connections_from(array(
					"type" => "RELTYPE_MANAGER_CO",
				));
				foreach($conns3 as $company)
				{
					$company = $company->to();
					$pol = $company->get_workers();
					$persons = $pol->ids();
				}
			}
		}
		$person = get_current_person()->id();
		if(in_array($person, $persons))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function do_final_draw_product($arr)
	{
		extract($arr);
		$it = isset($arr["it"]) ? $arr["it"] : NULL;
		enter_function("shop_product::bgin");

		$si = __get_site_instance();
		if (method_exists($si, "handle_product_display"))
		{
			$si->handle_product_display($prod);
		}

		if (!empty($prod_link_cb))
		{
			$ol = $prod_link_cb(array(
				"prod" => $prod,
				"oc" => $oc_obj
			));
		}
		else
		if ($oc_obj)
		{
			$ol = obj_link($prod->id().":".$oc_obj->id());
			$oc_id = $oc_obj->id();
		}
		else
		{
			$ol = obj_link($prod->id());
			$oc_id = false;
		}

		//change button should be visible to only
		//the users' persons objects that are
		//workers of the companies that are
		//the maintainers of the current warehouse
		//configuration - weeeeeeeeeeeeeeeeeeeee

		//how the hell should i get the current warehouse conf?
		//let's get the warehouse of the $arr['oc_obj']
		if($arr["oc_obj"])
		{
			if($arr["oc_obj"]->prop("no_change_button") != 1)
			{
				$show_change_button = $this->show_change_button($arr);
			}
		}
		if (!is_object($layout))
		{
			error::raise(array(
				"id" => "ERR_NO_LAYOUT",
				"msg" => t("shop_product::draw(): no layout set!")
			));
		}

		$so = obj(aw_global_get("section"));
		if ($so->class_id() != CL_MENU)
		{
			$so = obj($so->parent());
		}

		if(!$l_inst)
		{
			$l_inst = $layout->instance();
			$l_inst->read_any_template($layout->prop("template"));
		}
		lc_site_load("shop_order_center", $l_inst);
		$rp = (!empty($arr["price"]) ? number_format($arr["price"], 2) : $this->get_price($prod));
		$rp_all_cur = "";
		foreach(safe_array($prod->meta("cur_prices")) as $cur_id => $cur_price)
		{
			$cur_obj = obj($cur_id);
			$rp_all_cur .= " ".$cur_obj->name()." ".number_format($cur_price, 2);
		}

		$sect = $prod->id();
		if (aw_ini_get("user_interface.full_content_trans"))
		{
			$sect = aw_global_get("ct_lang_lc")."/".$sect;
		}
		$soc = self::_get_soc_instance();
		$soc->get_cart($oc_obj);
		$inf = $soc->get_item_in_cart($prod->id());
		$parent = obj($prod->parent());
		$ivs = array(
			"bgcolor" => $bgcolor,
			"name" => $prod->trans_get_val("name"),
			"price" => $rp,
			"obj_price" => str_replace("," , "" , $rp),
			"obj_price_all_cur" => $rp_all_cur,
			"id" => $prod->id(),
			"it" => $it,
			"trow_id" => "trow".$prod->id(),
			"err_class" => ($arr["is_err"] ? "class=\"selprod\"" : ""),
			"quantity" => (int)($arr["quantity"]),
			"view_link" => $ol,
			"packaging_name" => "",
			"packaging_price" => "",
			"packaging_id" => "",
			"packaging_quantity" => "",
			"packaging_view_link" => "",
			"tot_price" => number_format(((int)($arr["quantity"]) * $this->get_calc_price($prod)), 2),
			"obj_tot_price" => number_format(((int)($arr["quantity"]) * $this->get_calc_price($prod)), 2),
			"read_price_total" => number_format(((int)($arr["quantity"]) * (int)ifset($inf, "data", "read_price")), 2),
			"reforb" =>  $this->mk_reforb("submit_add_cart", array("section" => $prod->parent(), "oc" => $oc_id, "return_url" => aw_global_get("REQUEST_URI")), "shop_order_cart"),
			"edit_link" => $this->mk_my_orb("change", array("id" => $prod->id()), $prod->class_id(), true),
			"obj_id" => $prod->id(),
			"obj_parent" => $prod->parent(),
			"obj_parent_name" => $parent->name(),
			"price_comment" => (!empty($arr["price_comment"]) ? $arr["price_comment"] : $rp),
			"sel_menu_text" => $so->trans_get_val("name"),
			"oc_id" => $oc_id,
			"show_packages_link" => $this->mk_my_orb("show_items", array("id" => $oc_id, "section" => $sect), CL_SHOP_ORDER_CENTER),
		);

		//like this method isn't cray enough..
		foreach(safe_array(ifset($arr, "shop_special_offer_price")) as $cur => $price)
		{
			$cur = obj($cur);
			$sso["shop_special_offer_".$cur->name()."_price"] = $price;
		}
		$sso["shop_special_offer_discount"] = ifset($arr, "shop_spcial_offer_discount");
		$ivs += is_array($sso) ? $sso : array();


		/*
		if(strpos(aw_global_get("HTTP_HOST"), "tiitveod") !== false)
		{
			arr($arr);
			arr(dbg::process_backtrace(debug_backtrace()));
		}
		*/
		$l_inst->vars_safe(array(
			"printlink" => aw_global_get("REQUEST_URI")."&print=1"
		));

		$l_inst->vars_safe($ivs);

		if($arr["last_product_menu"] != $prod->parent())
		{
			$l_inst->vars_safe(array("MENU" => $l_inst->parse("MENU")));
		}
		else
		{
			$l_inst->vars_safe(array("MENU" => ""));
		}

		if(!empty($show_change_button))
		{
			$l_inst->vars_safe(array('show_change' => $l_inst->parse('show_change')));
		}
		exit_function("shop_product::bgin");

		enter_function("shop_product::pre");

		$proc_ivs = $ivs;

		// insert images
		if(is_object($oc_obj))
		{
			$only_prods = $oc_obj->prop("only_prods");
		}
		for($i = 0; $i < 10; $i++)
		{
			$l_inst->vars_safe(array(
				"image".$i => "",
				"image".$i."_url" => "",
				"image".$i."_big" => "",
				"HAS_IMAGE".$i => "",
				"NO_IMAGE".$i => ""
			));
		}
		if($only_prods != 1)
		{
			$i = self::_get_i_instance();
			$cnt = 1;
			$imgc = $prod->connections_from(array("type" => "RELTYPE_IMAGE"));
			usort($imgc, create_function('$a,$b', 'return $a->prop("to.jrk") - $b->prop("to.jrk");'));
			foreach($imgc as $c)
			{
				$u = $i->get_url_by_id($c->prop("to"));
				$ub = $i->get_big_url_by_id($c->prop("to"));

				$pid = $c->prop("to");
				$image_obj = $c->to();
				$l_inst->vars_safe(array(
					"image".$cnt => image::make_img_tag_wl($image_obj->id()),
					"image_br".$cnt => "<br><br>".image::make_img_tag($u, $c->prop("to.name")),
					"image".$cnt."_comment" => "<br>".$image_obj->prop('comment'),
					//"name" => $prod->name(),
					"image".$cnt."_url" => $u,
					"image".$cnt."_onclick" => image::get_on_click_js($c->prop("to")),
					"packaging_image".$cnt => "",
					"packaging_image".$cnt."_url" => "",
					"image".$cnt."_big" => $ub
				));

				if ($image_obj->prop("file2") != "")
				{
					$l_inst->vars_safe(array(
						"IMAGE".$cnt."_HAS_BIG" => $l_inst->parse("IMAGE".$cnt."_HAS_BIG")
					));
				}
				$l_inst->vars_safe(array(
					"HAS_IMAGE".$cnt => $l_inst->parse("HAS_IMAGE".$cnt)
				));
				$cnt++;
			}
		}
		exit_function("shop_product::pre");

		enter_function("shop_product::final");

		$cnt = 1;
		if($only_prods != 1)
		{
			$fi = self::_get_fi_instance();
			foreach($prod->connections_from(array("type" => "RELTYPE_FILE")) as $c)
			{
				$f_url = $fi->get_url($c->prop("to"), $c->prop("to.name"));
				$l_inst->vars_safe(array(
					"prod_file_".$cnt."_url" => $f_url,
					"prod_file_".$cnt."_name" => $c->prop("to.name"),
					"prod_file_".$cnt => html::href(array(
						"url" => $f_url,
						"caption" => $c->prop("to.name")
					))
				));

				$l_inst->vars_safe(array(
					"HAS_FILE_".$cnt => $l_inst->parse("HAS_FILE_".$cnt)
				));
				$cnt++;
			}
		}

		$ivar = array();

		static $prod2props;
		if (!is_array($prod2props))
		{
			$prod2props = array();
		}
		foreach($prod->properties() as $pkey => $pprop)
		{
			if (!$l_inst->template_has_var_full($pkey) && strpos($pkey, "userch") === false)
			{
				continue;
			}
			if(strpos($pkey, "uservar") !== false)
			{
				if(is_oid($pprop) && $this->can("view", $pprop))
				{
					$obj = obj($pprop);
					$ivar[$pkey] = $obj->name();
				}
			}
			elseif(strpos($pkey, "userta") !== false)
			{
				if (!isset($prod2props[$prod->meta("cfgform_id")]))
				{
					$cfg = get_instance(CL_CFGFORM);
					$prod2props[$prod->meta("cfgform_id")] = $cfg->get_props_from_cfgform(array(
						"id" => $prod->meta("cfgform_id")
					));
				}
				$props = $prod2props[$prod->meta("cfgform_id")];
				if($props["userta1"]["richtext"])
				{
					$ivar[$pkey] = $prod->trans_get_val($pkey);
				}
				else
				{
					$ivar[$pkey] = nl2br($prod->trans_get_val($pkey));
				}

			}
			else
			{
				//see siis selleks, et kui fck editor on peal, siis pole kyll hea, et ise veel miskeid reavahetusi juurde teha
				if (!isset($prod2props[$prod->meta("cfgform_id")]))
				{
					$cfg = get_instance(CL_CFGFORM);
					if(is_oid($prod->meta("cfgform_id")))
					{
						$prod2props[$prod->meta("cfgform_id")] = $cfg->get_props_from_cfgform(array(
							"id" => $prod->meta("cfgform_id")
						));
					}
				}
				$props = ifset($prod2props, $prod->meta("cfgform_id"));
				if(!empty($props["userta1"]["richtext"]))
				{
					$ivar[$pkey] = $prod->trans_get_val($pkey);
				}
				else
				{
					$ivar[$pkey] = nl2br($prod->trans_get_val($pkey));
				}
			}
		}

		if($only_prods != 1)
		{
			for($iz = 0; $iz < 6; $iz++)
			{
				if ($l_inst->is_template("USERVARM".$iz."_ITEM"))
				{
					$item_str = "";
					foreach($prod->connections_from(array("type" => "RELTYPE_VARUSERM".$iz, "sort_by" => "to.jrk")) as $c)
					{
						$l_inst->vars_safe(array(
							"value" => $c->prop("to.name")
						));
						$item_str .= $l_inst->parse("USERVARM".$iz."_ITEM");
					}
					$l_inst->vars_safe(array(
						"USERVARM".$iz."_ITEM" => $item_str
					));
					if ($item_str != "")
					{
						$l_inst->vars_safe(array(
							"USERVARM".$iz."_HAS_ITEMS" => $l_inst->parse("USERVARM".$iz."_HAS_ITEMS")
						));
					}
				}
			}
		}
		$l_inst->vars_safe($ivar);
		$proc_ivs += $ivar;

		// order data
		$soc = self::_get_soc_instance();
		$awa = $soc->get_item_in_cart(array(
			"iid" => $prod->id(),
			"it" => $it,
		));
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

		$this->_int_proc_ivs($proc_ivs, $l_inst);

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

		$conns = $prod->connections_from(array(
			"type" => array("RELTYPE_FILE", "RELTYPE_DOC", "RELTYPE_LNK"),
			"sort_by" => "objects.jrk"
		));

		$df = "";
		$t2sub = array(
			8 => array("FILE", "HAS_FILES"),
			9 => array("DOC", "HAS_DOCS"),
			10 => array("LINK", "HAS_LINKS")
		);
		$tpls = array();
		foreach($conns as $c)
		{
			if (aw_global_get("uid") == "" && $c->prop("to.status") == STAT_NOTACTIVE)
			{
				// thsi was done for bbraun and is very silly, cause it is really confusing
				//continue;
			}
			$l_inst->vars_safe(array(
				"link" => obj_link($c->prop("to")),
				"text" => $c->prop("to.name"),
				"comment" => $c->prop("to.comment")
			));

			$tpl = $t2sub[$c->prop("reltype")][0];
			$tpls[$c->prop("reltype")] .= $l_inst->parse($tpl);
		}
		foreach($tpls as $rt => $str)
		{
			$l_inst->vars_safe(array(
				$t2sub[$rt][0] => $str,
				$t2sub[$rt][1] => ($str != "" ? $l_inst->parse($t2sub[$rt][1]) : "")
			));
		}

		// if has packagings, draw other products packagings
		if ($l_inst->is_template("PACKAGING1") || ($has_loop = $l_inst->is_template("PACKAGING_LOOP")))
		{
			$cnt = 1;
			foreach($prod->connections_from(array("type" => "RELTYPE_PACKAGING")) as $c)
			{
				$pkids[$c->prop("to")] = $c->prop("to");
			}
			$args = array(
				"oid" => $pkids,
			);
			if(!count($pkids))
			{
				$args = array(
					"oid" => -1,
				);
			}
			$ol = new object_list($args);
			$ol->sort_by(array(
				"order" => "asc",
				"prop" => "jrk"
			));
			foreach($ol->arr() as $pko)
			{
				if ($oc_obj->prop("only_active_items") && $pko->status() != STAT_ACTIVE)
				{
					continue;
				}
				$pk_tpl = "PACKAGING".$cnt;
				if ($has_loop)
				{
					$pk_tpl = "PACKAGING_LOOP";
				}

				$pk_all_cur = "";
				foreach(safe_array($pko->meta("cur_prices")) as $cur_id => $cur_price)
				{
					$cur_obj = obj($cur_id);
					$pk_all_cur .= " ".$cur_obj->name()." ".number_format($cur_price, 2);
				}
				$pk_items = reset($_SESSION["cart"]["items"][$pko->id()]);

				$l_inst->vars_safe(array(
					"pk_id" => $pko->id(),
					"pk_user1" => $pko->trans_get_val("user1"),
					"pk_user3" => $pko->trans_get_val("user3"),
					"pk_name" => $pko->trans_get_val("name"),
					"pk_price" => $pko->prop("price"),
					"pk_price_all_cur" => $pk_all_cur,
					"pk_quantity" => $_SESSION["cart"]["items"][$prod->id()][$pko->id()]["items"],
					"pk_quant" => ($cq = $_SESSION["cart"]["items"][$pko->id()][0]["items"]) ? $cq : ((($seq = $soce[$pko->id()]["ordered_num_enter"]) && !$soce[$pko->id()]["is_err"]) ? $seq : ""),
					"pk_quantit" => $pk_items["items"],
					"checked" => "",
				));

				//see on selleks, et kui on porno systeem mis nagu tegeleks produktidega, aga siis saad valida neist hoopis yhe pakendi, siis hiljem n2eks mis on selektitud
				if($pk_items["items"])
				{
					$l_inst->vars(array(
						"selected_packaging" => $pko->id(),
						"selected_count" => $pk_items["items"],
						"checked" => "checked",
					));
				}
				// if has images
				if ($l_inst->template_has_var("pk_image1", "MAIN.".$pk_tpl))
				{
					$cnt = 1;
					foreach($pko->connections_from(array("type" => "RELTYPE_IMAGE")) as $c_i)
					{
						$l_inst->vars_safe(array(
							"pk_image".$cnt => $i->make_img_tag_wl($c_i->prop("to"), "", $l_inst->parse("PK_IMAGE_HAS_BIG_ALT_TEXT")),
							"pk_image".$cnt."_big" => $i->get_big_url_by_id($c_i->prop("to"))
						));
						$cnt++;
					}
				}

				if ($has_loop)
				{
					$pl_tpl_str .= $l_inst->parse($pk_tpl);
				}
				else
				{
					$l_inst->vars_safe(array(
						$pk_tpl => $l_inst->parse($pk_tpl)
					));
				}
				$cnt++;
			}

			if ($has_loop)
			{
				$l_inst->vars_safe(array("PACKAGING_LOOP" => $pl_tpl_str));
			}
		}

		if ($l_inst->is_template("RELATED_PROD_PACKAGING_LOOP"))
		{
			$pl_tpl_str = "";
			$cnt = 1;
			foreach($prod->connections_from(array("type" => "RELTYPE_MATCH_PROD")) as $c)
			{
				$r_prod = $c->to();
				$r_pks = array();
				if ($r_prod->class_id() == CL_SHOP_PRODUCT)
				{
					foreach($r_prod->connections_from(array("type" => "RELTYPE_PACKAGING")) as $c)
					{
						$r_pks[] = $c->to();
					}
				}
				else
				{
					// find prod this connects to
					$conns = $r_prod->connections_to(array("from.class_id" => CL_SHOP_PRODUCT));
					$c = reset($conns);
					$r_pks[] = $r_prod;
					$r_prod = $c->from();
				}


				foreach($r_pks as $pko)
				{
					if ($oc_obj->prop("only_active_items") && $pko->status() != STAT_ACTIVE)
					{
						continue;
					}
					$pk_tpl = "RELATED_PROD_PACKAGING_LOOP";

					$pk_all_cur = "";
					foreach(safe_array($pko->meta("cur_prices")) as $cur_id => $cur_price)
					{
						$cur_obj = obj($cur_id);
						$pk_all_cur .= " ".$cur_obj->name()." ".number_format($cur_price, 2);
					}
					$l_inst->vars_safe(array(
						"pk_id" => $pko->id(),
						"pk_user1" => $pko->trans_get_val("user1"),
						"pk_user3" => $pko->trans_get_val("user3"),
						"pk_name" => $pko->trans_get_val("name"),
						"pk_price" => $pko->trans_get_val("price"),
						"pk_price_all_cur" => $pk_all_cur,
						"pk_quantity" => $_SESSION["cart"]["items"][$r_prod->id()][$pko->id()]["items"],
						"pk_prod_id" => $r_prod->id(),
						"pk_prod_parent" => $r_prod->parent(),
					));
					// if has images
					if ($l_inst->template_has_var("pk_image1", "MAIN.".$pk_tpl))
					{
						$cnt = 1;
						foreach($pko->connections_from(array("type" => "RELTYPE_IMAGE")) as $c_i)
						{
							$l_inst->vars_safe(array(
								"pk_image".$cnt => $i->make_img_tag_wl($c_i->prop("to"), "", $l_inst->parse("PK_IMAGE_HAS_BIG_ALT_TEXT"))
							));
							$cnt++;
						}
					}

					$pl_tpl_str .= $l_inst->parse($pk_tpl);
					$cnt++;
				}
			}

			$l_inst->vars_safe(array("RELATED_PROD_PACKAGING_LOOP" => $pl_tpl_str));

			if ($pl_tpl_str != "")
			{
				$l_inst->vars_safe(array(
					"HAS_RELATED_PRODS" => $l_inst->parse("HAS_RELATED_PRODS")
				));
			}
			else
			{
				$l_inst->vars_safe(array(
					"HAS_RELATED_PRODS" => ""
				));
			}
		}

		if ($l_inst->is_template("RELATED_PROD_LOOP"))
		{
			$pl_tpl_str = "";
			$cnt = 1;
			foreach($prod->connections_from(array("type" => "RELTYPE_MATCH_PROD")) as $c)
			{
				$r_prod = $c->to();
				if ($r_prod->class_id() != CL_SHOP_PRODUCT)
				{
					// find prod this connects to
					$conns = $r_prod->connections_to(array("from.class_id" => CL_SHOP_PRODUCT));
					$c = reset($conns);
					$r_prod = $c->from();
				}


				$pk_tpl = "HAS_RELATED_PRODS.RELATED_PROD_LOOP";

				$pk_all_cur = "";
				foreach(safe_array($r_prod->meta("cur_prices")) as $cur_id => $cur_price)
				{
					$cur_obj = obj($cur_id);
					$pk_all_cur .= " ".$cur_obj->name()." ".number_format($cur_price, 2);
				}

				$l_inst->vars_safe(array(
					"rp_user1" => $r_prod->trans_get_val("user1"),
					"rp_user3" => $r_prod->trans_get_val("user3"),
					"rp_user6" => $r_prod->trans_get_val("user6"),
					"rp_name" => $r_prod->trans_get_val("name"),
					"rp_price" => $r_prod->trans_get_val("price"),
					"rp_price_all_cur" => $pk_all_cur,
					"rp_quantity" => $_SESSION["cart"]["items"][$r_prod->id()][$r_prod->id()]["items"],
					"rp_prod_id" => $r_prod->id(),
					"rp_prod_parent" => $r_prod->parent(),
				));
				// if has images
				if ($l_inst->template_has_var("rp_image1", "MAIN.".$pk_tpl))
				{
					$cnt = 1;
					foreach($r_prod->connections_from(array("type" => "RELTYPE_IMAGE")) as $c_i)
					{
						$l_inst->vars_safe(array(
							"rp_image".$cnt => $i->make_img_tag_wl($c_i->prop("to"), "", $l_inst->parse("RP_IMAGE_HAS_BIG_ALT_TEXT"))
						));
						$cnt++;
					}
				}
				$pl_tpl_str .= $l_inst->parse($pk_tpl);
				$cnt++;
			}

			$l_inst->vars_safe(array("RELATED_PROD_LOOP" => $pl_tpl_str));

			if ($pl_tpl_str != "")
			{
				$l_inst->vars_safe(array(
					"HAS_RELATED_PRODS" => $l_inst->parse("HAS_RELATED_PRODS")
				));
			}
		}


		$l_inst->vars_safe(array(
			"logged" => (aw_global_get("uid") == "" ? "" : $l_inst->parse("logged"))
		));



		exit_function("shop_product::final");
		return eval_buffer($l_inst->parse());
	}

	function request_execute($obj)
	{
		list($prod_id, $oc_id) = explode(":", aw_global_get("raw_section"));
		if (!is_oid($oc_id))
		{
			return;
		}
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

	function _int_proc_ivs($ivs, $l_inst)
	{
		foreach($ivs as $ivar => $ival)
		{
			$ne_iv = "";
			$ne_iv_e = "";
			$nen = "NOT_EMPTY_".$ivar;
			$nen_e = "EMPTY_".$ivar;
			if ($l_inst->is_template($nen) || $l_inst->is_template($nen_e))
			{
				if (((double)$ival == $ival && $ival > 0) || ((double)$ival != $ival && $ival != ""))
				{
					$ne_iv = $l_inst->parse($nen);
				}
				else
				{
					$ne_iv_e = $l_inst->parse($nen_e);
				}
				$l_inst->vars_safe(array(
					$nen => $ne_iv,
					$nen_e => $ne_iv_e
				));
			}
		}
	}

	function get_contained_products($o)
	{
		$conn = $o->connections_from(array(
			"type" => "RELTYPE_PACKAGING"
		));
		if (count($conn) > 0)
		{
			$ret = array();
			foreach($conn as $c)
			{
				$ret[] = $c->to();
			}
			return $ret;
		}
		else
		{
			return array($o);
		}
	}

	function get_must_order_num($o)
	{
		return $o->prop("must_order_num");
	}

	function on_add_alias($arr)
	{
		if ($arr["connection"]->prop("to.class_id") == CL_IMAGE)
		{
			// based on the product type, move images to correct place.
			$prod = $arr["connection"]->from();
			$img = $arr["connection"]->to();

			if ($prod->prop("item_type"))
			{
				$type = obj($prod->prop("item_type"));
				if ($type->prop("image_folder") && $img->class_id() == CL_IMAGE)
				{
					$img->set_parent($type->prop("image_folder"));
					$img->save();
				}
				else
				if ($type->prop("file_folder") && $img->class_id() == CL_FILE)
				{
					$img->set_parent($type->prop("file_folder"));
					$img->save();
				}
			}
		}
		elseif($arr["connection"]->prop("to.class_id") == CL_SHOP_PRODUCT_PACKAGING && $arr["connection"]->prop("reltype") == 2)	// RELTYPE_PACKAGING
		{
			$packaging_obj = $arr["connection"]->to();
			$packaging_obj->set_prop("product", $arr["connection"]->prop("from"));
			$packaging_obj->save();
		}
	}

	/**

		@attrib name=show_prod nologin="1"

		@param id required type=int acl=view
		@param template optional type=string

	**/
	function show_prod($arr)
	{
		$tpl = obj();
		$tpl->set_class_id(CL_SHOP_PRODUCT_LAYOUT);
		$tpl->set_prop("template", $arr["template"]?$arr["template"]:"prod_single.tpl");
		return $this->do_draw_product(array(
			"prod" => obj($arr["id"]),
			"layout" => $tpl,
		));
	}

	function do_db_upgrade($t, $f, $query, $error)
	{
		if ("aw_account_balances" === $t)
		{
			$i = new crm_category();
			return $i->do_db_upgrade($t, $f);
		}
		switch($f)
		{
			case "content_package":
			case "aml_inheritable":
			case "userch5":
			case "userch6":
			case "userch7":
			case "userch8":
			case "userch9":
			case "userch10":
			case "aw_serial_number_based":
			case "aw_order_based":
			case "color":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;

			case "description":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "text"
				));
				return true;

			case "purchase_price":
			case "aw_min_order_amt":
			case "aw_max_order_amt":
			case "wh_minimum":
			case "wh_maximum":
			case "aw_min_wh_order_amt":
			case "aw_special_price":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "double"
				));
				return true;

			case "code":
			case "barcode":
			case "type_code":
			case "short_code":
			case "aw_short_name":
			case "weight":
			case "height":
			case "width":
			case "depth":
			case "wideness":
			case "density":
			case "gramweight":
			case "raster":
			case "bulk":
			case "guarantee":
			case "search_term":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "varchar(100)"
				));
				return true;
		}
	}

	/** return the reservation length in seconds
		@attrib api=1
		@param prod required type=object
		@returns time in seconds for reservation
	**/
	function get_reservation_length($prod)
	{
		return $prod->prop("reservation_time") * $prod->prop("reservation_time_unit");
	}

	/** return the pre buffer length in seconds
		@attrib api=1
		@param prod required type=object
		@returns time in seconds for pre-buffer
	**/
	function get_pre_buffer($prod)
	{
		return $prod->prop("buffer_time_before") * $prod->prop("buffer_time_unit");
	}

	/** return the post buffer length in seconds
		@attrib api=1
		@param prod required type=object
		@returns time in seconds for post-buffer
	**/
	function get_post_buffer($prod)
	{
		return $prod->prop("buffer_time_after") * $prod->prop("buffer_time_unit");
	}

	function callback_mod_tab($arr)
	{
		if ($arr["id"] == "transl" && aw_ini_get("user_interface.content_trans") != 1)
		{
			return false;
		}
		if($arr["id"] == "singles" && !$arr["obj_inst"]->prop("serial_number_based"))
		{
			return false;
		}
		return true;
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
		$o = obj($arr["id"]);
		if($this->can("view", $o->inherit_aml_from))
		{
			$arr["id"] = $o->inherit_aml_from;
			return $this->get_amount_limits($arr);
		}
		$amount_limits = $o->meta("amount_limits");
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
		$arr["add_purveyor"] = 0;
		$arr["add_material"] = 0;
		$arr["add_brand"] = 0;
		$arr["add_cat"] = 0;
		$arr["add_replacement_product"] = 0;
		$arr["post_ru"] = post_ru();
		if($_GET["action"] == "new")
		{
			$arr["new_cat"] = $_GET["category"];
			$arr["warehouse"] = $_GET["warehouse"];
		}
	}

	function callback_get_transl($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
	}

	function _get_categories_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_categories_table($t);
		$conn = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_CATEGORY",
		));
		foreach($conn as $c)
		{
			$o = $c->to();
			$t->define_data(array(
				"oid" => $o->id(),
				"name" => $o->name(),
				"location" => $o->prop("parent.name"),
			));
		}
	}

	function _get_categories_toolbar($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_search_button(array(
			"pn" => "add_cat",
			"clid" => CL_SHOP_PRODUCT_CATEGORY,
			"multiple" => 1,
		));
		$tb->add_delete_rels_button();
	}

	function _set_categories_toolbar($arr)
	{
		if($add = $arr["request"]["add_cat"])
		{
			$tmp = explode(",", $add);
			foreach($tmp as $oid)
			{
				if(!$arr["obj_inst"]->is_connected_to(array("to" => $oid)))
				{
					$arr["obj_inst"]->connect(array(
						"type" => "RELTYPE_CATEGORY",
						"to" => $oid,
					));
				}
			}
		}
	}

	function _get_singles_toolbar($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_new_button(array(CL_SHOP_PRODUCT_SINGLE), $arr["obj_inst"]->id(), null, array("product" => $arr["obj_inst"]->id()));
		$tb->add_save_button();
		$tb->add_delete_button();
		$tb->add_button(array(
			"name" => "create_dn",
			"action" => "create_new_dn",
			"img" => "class_326.gif",
			"tooltip" => t("Loo saateleht valitud ridadega"),
		));
	}

	/**
	@attrib name=create_new_dn
	**/
	function create_new_dn($arr)
	{
		if(count($arr["sel"]))
		{
			$url = html::get_new_url(CL_SHOP_DELIVERY_NOTE, $arr["id"], array("singles" => $arr["sel"], "return_url" => $arr["post_ru"]));
			return $url;
		}
		else
		{
			return $arr["post_ru"];
		}
	}

	function _init_singles_table($t, $arr)
	{
		$t->define_field(array(
			"name" => "code",
			"caption" => t("Kood"),
			"align" => "center"
		));
		$units = $this->get_units($arr["obj_inst"]);
		foreach($units as $i=>$unit)
		{
			if($this->can("view", $unit))
			{
				$uo = obj($unit);
				$t->define_field(array(
					"name" => "amount_".$i,
					"caption" => sprintf(t("Kogus %s"), $i+1),
					"align" => "center",
				));
			}
		}
		$t->define_field(array(
			"name" => "type",
			"caption" => t("T&uuml;&uuml;p"),
			"align" => "center",
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	function _get_singles_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_singles_table($t, $arr);
		$ol = new object_list(array(
			"lang_id" => array(),
			"site_id" => array(),
			"class_id" => CL_SHOP_PRODUCT_SINGLE,
			"product" => $arr["obj_inst"]->id(),
		));
		$s_types = get_instance(CL_SHOP_PRODUCT_SINGLE)->get_types();
		//$si = get_instance(CL_SHOP_PRODUCT_SINGLE);
		$units = $this->get_units($arr["obj_inst"]);
		$wh = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_WAREHOUSE");
		foreach($ol->arr() as $o)
		{
			$data = array();
			foreach($units as $i=>$unit)
			{
				$uo = obj($unit);
				$tmp = $this->get_amount(array(
					"product" => $arr["obj_inst"]->id(),
					"single" => $o->id(),
					"unit" => $unit,
					"warehouse" => $wh?$wh->id():null,
				));
				$amt = $tmp->begin();
				$quant = 0;
				if($amt)
				{
					$quant = $amt->prop("amount");
				}
				$data["amount_".$i] = $quant." ".$uo->prop("unit_code");
			}
			$data["type"] = $s_types[$o->prop("type")];
			$data["code"] = html::obj_change_url($o, ($c=$o->prop("code"))?$c:t("(Puudub)"));
			$data["oid"] = $o->id();
			$t->define_data($data);
		}
	}

	function _init_categories_table($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "location",
			"caption" => t("Asukoht"),
			"align" => "center"
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	function callback_post_save($arr)
	{
		if($arr["new"] && $wh = $arr["request"]["warehouse"])
		{
			if($this->can("view", $wh))
			{
				$arr["obj_inst"]->connect(array(
					"to" => $wh,
					"type" => "RELTYPE_WAREHOUSE",
				));
			}
		}
		if($arr["new"] && $cat = $arr["request"]["new_cat"])
		{
			if($this->can("view", $cat))
			{
				$arr["obj_inst"]->set_meta("def_cat", $cat);
				$arr["obj_inst"]->save();
				$arr["obj_inst"]->connect(array(
					"to" => $cat,
					"type" => "RELTYPE_CATEGORY",
				));
			}
		}
	}

	/** return the last purchase price
		@attrib api=1
		@param prod required type=object
		@returns price, integer. if the product's warehouse doesn't have a default currency, or a price is not found, then returns false.
	**/
	function get_last_purchase_price($o)
	{
		$wh = $o->get_first_obj_by_reltype("RELTYPE_WAREHOUSE");
		if(!$wh)
		{
			return false;
		}
		$cur = $wh->prop("conf.def_currency");
		if(!$cur)
		{
			return false;
		}
		$ol = new object_list(array(
			"class_id" => CL_SHOP_WAREHOUSE_MOVEMENT,
			"from_wh" => 0,
			"to_wh" => new obj_predicate_compare(OBJ_COMP_GREATER, 0),
			"site_id" => array(),
			"lang_id" => array(),
			"sort_by" => "objects.created DESC",
			"product" => $o->id(),
		));
		$mo = $ol->begin();
		if(!$mo)
		{
			return false;
		}
		return $mo->prop("base_price");
	}

	/** get product's fifo price
		@attrib api=1
		@param prod required type=object
		@param warehouse required type=int
		@returns price.
	**/
	function get_fifo_price($o, $wh)
	{
		$fifos = $o->meta("fifo_movement");
		$p = obj($fifos[$wh])->prop("base_price");
		return $p;
	}

	/** get product's warehouse saldo, calculated by fifo prices and amounts
		@attrib api=1
		@param prod required type=object
		@param warehouse required type=int
		@returns sum.
	**/
	function get_saldo($o, $wh)
	{
		$saldos = $o->meta("saldo");
		$s = $saldos[$wh];
		return $s;
	}

	/** calculate product's fifo price. should be called each time its amounts are changed.
		@attrib api=1
		@param prod required type=object
		@returns nothing
	**/
	function calc_fifo_price($o)
	{
		$ol = new object_list(array(
			"class_id" => CL_SHOP_WAREHOUSE_MOVEMENT,
			"from_wh" => 0,
			"to_wh" => new obj_predicate_compare(OBJ_COMP_GREATER, 0),
			"site_id" => array(),
			"lang_id" => array(),
			"sort_by" => "objects.created DESC",
			"product" => $o->id(),
		));
		$units = $this->get_units($o);
		$amts = $this->get_amount(array(
			"prod" => $o->id(),
			"unit" => $units[0],
		));
		if(!$amts)
		{
			return;
		}
		foreach($amts->arr() as $amt)
		{
			$wh = $amt->prop("warehouse");
			$whs[$wh] = $wh;
			$totals[$wh] += $amt->prop("amount");
		}
		$saldo = array();
		foreach($whs as $wh)
		{
			foreach($ol->arr() as $mo)
			{
				if($mo->prop("to_wh") == $wh)
				{
					$amt = $mo->prop("amount");
					$price = $mo->prop("base_price");
					if($amt < $totals[$wh])
					{
						$saldo[$wh] += $amt*$price;
						$totals[$wh] -= $amt;
					}
					else
					{
						$saldo[$wh] += $totals[$wh]*$price;
						$fifo[$wh] = $mo->id();
						break;
					}
				}
			}
		}
		$o->set_meta("saldo", $saldo);
		$o->set_meta("fifo_movement", $fifo);
		$o->save();
	}

	/** get product's sales price
		@attrib api=1

		@param prod required type=object
		@param currency optional type=int
		@param pricelist optional type=int,bool

		@param pricelist_params optional type=array

		@returns price, integer.
		
		@comment
			if currency is not set, uses warehouse's default currency.

			if pricelist is set to false, then no pricelist is used
			if pricelist is set to oid, then that pricelist is used
			if pricelist is set to true:
				you can use param pricelist_params, which may include: 
				group, crm_category, org, person, warehouse, prod_category
				to find and apply a pricelist according to those filters

		@example
			$prod->instance()->calc_price($prod, null, true, array(
				"crm_category" => 12345,
			));
				
	**/
	function calc_price($o, $cur = null, $pricelist = false, $pricelist_params = array())
	{
		if(!$cur)
		{
			$prod = $o;
			if(is_array($o))
			{
				$prod = obj(reset($o));
			}
			$wh = $prod->get_first_obj_by_reltype("RELTYPE_WAREHOUSE");
			if(!$wh)
			{
			//	return false;
			}
			//$cur = $wh->prop("conf.def_currency");
		}
		if(!$cur)
		{
		//	return false;
		}
		$ol = new object_list(array(
			"class_id" => CL_SHOP_ITEM_PRICE,
		//	"valid_to" => new obj_predicate_compare(OBJ_COMP_GREATER, time()),
		//	"valid_from" => new obj_predicate_compare(OBJ_COMP_LESS, time()),
			"site_id" => array(),
			"lang_id" => array(),
		//	"currency" => $cur,
			"sort_by" => "objects.created DESC",
			"product" => is_array($o) ? $o : $o->id(),
		));
		if(!$ol->count())
		{
			return false;
		}
		foreach($ol->arr() as $po)
		{
			$price = $po->prop("price");
			if($this->can("view",  $pricelist))
			{
				$plid = $pricelist;
			}
			/*		Price list doesn't work that way any more! -kaarel 29.07.2009
			if($pricelist)
			{
				$disc = $prod->get_discount($plid, $pricelist_params);
			}
			if($disc)
			{
				$price = $price - ($disc/100*$price);
			}
			*/
			if(is_array($o))
			{
				$res[$po->prop("product")] = $price;
			}
			else
			{
				$res = $price;
				break;
			}
		}
		return $res;
	}

	/** get product's units from either productgroup or product itself
		@attrib api=1
		@param prod required type=object
		@returns array of units, array(0=>oid, 1=>oid, etc).
		@comment some of the results may be undefined, beware of that. result[0] is the base unit
	**/
	function get_units($o)
	{
		return $o->get_units()->ids();
	}

	/** get product's warehouse amounts
		@attrib api=1
		@param prod optional type=int
		@param single optional type=int
		@param unit optional type=int
		@param warehouse optional type=int
		@param singlecode optional type=int
		@returns object list of shop_warehouse_amount objects
	**/
	function get_amount($arr)
	{
		if(isset($arr["prod"]))
		{
			$params["product"] = $arr["prod"];
		}
		if(isset($arr["single"]))
		{
			$params["single"] = $arr["single"];
		}
		elseif($this->can("view", $arr["prod"]))
		{
			$po = obj($arr["prod"]);
			if(!$po->prop("serial_number_based") && !$po->prop("order_based"))
			{
				$params["single"] = null;
			}
		}
		if(count($params))
		{
			if($arr["unit"])
			{
				$params["unit"] = $arr["unit"];
			}
			if($arr["warehouse"])
			{
				$params["warehouse"] = $arr["warehouse"];
			}
			if($arr["singlecode"])
			{
				$params["CL_SHOP_WAREHOUSE_AMOUNT.single.code"] = $arr["singlecode"];
			}
			$params["class_id"] = CL_SHOP_WAREHOUSE_AMOUNT;
			$params["lang_id"] = array();
			$params["site_id"] = array();
			$ol = new object_list($params);
			return $ol;
		}
		return false;
	}

	private static function _get_soc_instance()
	{
		static $soc_instance;
		if (!$soc_instance)
		{
			$soc_instance = get_instance(CL_SHOP_ORDER_CART);
		}
		return $soc_instance;
	}

	private static function _get_fi_instance()
	{
		static $fi_instance;
		if (!$fi_instance)
		{
			$fi_instance = get_instance(CL_FILE);
		}
		return $fi_instance;
	}

	private static function _get_i_instance()
	{
		static $i_instance;
		if (!$i_instance)
		{
			$i_instance = get_instance(CL_IMAGE);
		}
		return $i_instance;
	}



	private function get_template($ob, $oc)
	{
		if ($ob->status() != object::STAT_ACTIVE and $oc->prop("only_active_items"))
		{
			return $oc->prop("inactive_item_tpl");
		}
		elseif($this->template)
		{
			return $this->template;
		}
		else
		{
			return "show.tpl";
		}
	}

	function show($arr)
	{
		error::raise_if(!$this->can("view" , $arr["oc"], array(
			"id" => ERR_NO_OC,
			"msg" => t("shop_packet::show(): no order center object selected!")
		)));

		$ob = new object($arr["id"]);
		$oc = obj($arr["oc"]);

		$this->read_template($this->get_template($ob, $oc));
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		lc_site_load("shop", $this);

		$data = $ob->get_data();

		$cart_inst = get_instance(CL_SHOP_ORDER_CART);

		$data["oc"] = $arr["oc"];
		$data["submit"] = html::submit(array(
			"value" => t("Lisa tooted korvi"),
		));
		$data["submit_url"] = $this->mk_my_orb("submit_add_cart", array(
			"oc" => $oc->id(),
			"id" => $oc->prop("cart"),
		),CL_SHOP_ORDER_CART,false,false,"&amp;");

		$data["section"] = aw_global_get("section");
		$this->vars($data);
		return $this->parse();
	}


}
