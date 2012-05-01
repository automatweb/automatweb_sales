<?php

// otto_import.aw - Otto toodete import
/*

@classinfo relationmgr=yes no_status=1 no_comment=1 prop_cb=1

@default table=objects
@default group=general
@default field=meta
@default method=serialize

	@property base_url type=textbox
	@caption Toodete csv failide baasaadress

	@property prod_folder type=relpicker reltype=RELTYPE_FOLDER
	@caption Toodete kataloog

	@property shop_product_config_form type=relpicker reltype=RELTYPE_SHOP_PRODUCT_CONFIG_FORM
	@caption Lao toote seadete vorm

	@property images_folder type=textbox
	@caption Serveri kaust kuhu pildid salvestatakse

	@property files_to_import type=text store=no
	@caption Imporditavad failid

	@property do_i type=checkbox ch_value=1
	@caption Teosta import

	@property do_pict_i type=checkbox ch_value=1
	@caption Teosta piltide import

	@property update_params_table type=table
	@caption Toodete uuendamine

	@property last_import_log type=text store=no
	@caption Viimase impordi logi

	@property products_count type=text store=no
	@caption Toodete arv


@groupinfo imported_products caption="Imporditud tooted"

	@property import_log_list type=table no_caption=1 group=imported_products
	@caption Impordi logide list

	@property import_log_item type=table no_caption=1 group=imported_products
	@caption Impordi logi vaatamine

@groupinfo products_manager caption="Toodete haldus"

	@layout hbox_products_manager_toolbar type=hbox group=products_manager

		@property products_manager_toolbar type=toolbar store=no no_caption=1 group=products_manager parent=hbox_products_manager_toolbar
		@caption Toodete halduse t&ouml;&ouml;riistariba

	@layout hbox_products_manager type=hbox width=20%:80% group=products_manager

		@layout vbox_products_manager_search type=vbox closeable=1 area_caption=Toodete&nbsp;otsing group=products_manager parent=hbox_products_manager

			@property products_manager_prod_id type=textbox store=no size=20 captionside=top group=products_manager parent=vbox_products_manager_search
			@caption Toote OID

			@property products_manager_pcode type=textbox store=no size=20 captionside=top group=products_manager parent=vbox_products_manager_search
			@caption Tootekood

			@property products_manager_prod_page type=textbox store=no size=20 captionside=top group=products_manager parent=vbox_products_manager_search
			@caption Leht

			@property products_manager_prod_image type=textbox store=no size=20 captionside=top group=products_manager parent=vbox_products_manager_search
			@caption Pilt

			@property products_search type=hidden store=no no_caption=1 group=products_manager parent=vbox_products_manager_search
			@caption products_search

			@property products_manager_search_submit type=submit no_caption=1 group=products_manager parent=vbox_products_manager_search
			@caption Otsi

		@layout vbox_products_manager type=vbox group=products_manager parent=hbox_products_manager

			@property products_manager_search_results_table type=table no_caption=1 group=products_manager parent=vbox_products_manager
			@caption Toodete otsingu tulemused

			@property products_manager_item_table type=table no_caption=1 group=products_manager parent=vbox_products_manager
			@caption Tooted

			@property products_manager_change_submit type=submit no_caption=1 group=products_manager parent=vbox_products_manager
			@caption Salvesta

@groupinfo files caption="Failid"

	@groupinfo files_import caption="Imporditavad failid" parent=files

		@layout hbox_files_import type=hbox width=20%:80% group=files_import

			@layout vbox_files_import_filenames type=vbox closeable=1 area_caption=Toodete&nbsp;otsing group=files_import parent=hbox_files_import

				@property fnames type=textarea rows=15 cols=30 group=files_import parent=vbox_files_import_filenames
				@caption Failinimed


			@layout vbox_files_import_sites type=vbox group=files_import parent=hbox_files_import

				@property first_site_to_search_images type=select field=meta method=serialize group=files_import parent=vbox_files_import_sites
				@caption Esimene leht kust pilte otsitakse

				@property files_import_sites_order type=table field=meta method=serialize group=files_import parent=vbox_files_import_sites captionside=top
				@caption Saitide j&auml;rjekord

	@groupinfo files_order caption="Failide j&auml;rjekord" parent=files

		@property files_order type=table group=files_order
		@caption Failide n&auml;itamise j&auml;rjekord

	@groupinfo file_suffix caption="Failide suffiksid" parent=files

		@property file_suffix type=table group=file_suffix
		@caption Failide suffiksid

@groupinfo discount_products caption="Soodustooted"

	@groupinfo discount_products_general caption="&Uuml;ldine" parent=discount_products

		@property discount_products_file type=textbox size=60 group=discount_products_general field=meta method=serialize
		@caption Soodustoodete faili asukoht

		@property discount_products_parents type=textbox size=60 group=discount_products_general field=meta method=serialize
		@caption Kausta id, kus all soodustooted asuvad

		@property discount_products_count type=text store=no group=discount_products_general
		@caption Ridu tabelis

		@property import_discount_products type=text store=no group=discount_products_general
		@caption &nbsp;

		@property clear_discount_products type=text store=no group=discount_products_general
		@caption &nbsp;

		@property discount_title type=textbox group=discount_products_general field=meta method=serialize
		@caption Hooajalise allahindluse nimetus

		@property discount_season_parent type=textbox group=discount_products_general field=meta method=serialize
		@caption Hooajalise allahindluse toodete asukoht

		@property discount_date_from type=date_select group=discount_products_general field=meta method=serialize
		@caption Alates

		@property discount_date_to type=date_select group=discount_products_general field=meta method=serialize
		@caption Kuni

		@property discount_season_sum type=textbox group=discount_products_general field=meta method=serialize
		@caption Summa, alates millest soodust kehtib

		@property discount_season_amount type=textbox group=discount_products_general field=meta method=serialize
		@caption Kui palju allahindlust tehake

	@groupinfo discount_products_list_without_pictures caption="Soodustooted (ilma piltideta)" parent=discount_products

		@property discount_products_list_without_pictures type=table no_caption=1 group=discount_products_list_without_pictures

	@groupinfo discount_products_list_with_pictures caption="Soodustooted (piltidega)" parent=discount_products

		@property discount_products_list_with_pictures type=table no_caption=1 group=discount_products_list_with_pictures

@groupinfo foldersa caption="Kataloogid / Kategooriad"

	@groupinfo categories caption="Kategooriad" parent=foldersa

		@property categories type=table store=no group=categories no_caption=1
		@caption Kategooriad

	@groupinfo firm_pictures_group caption="Firmapildid" parent=foldersa

		@property firm_pictures_toolbar type=toolbar group=firm_pictures_group no_caption=1

		@property firm_pictures type=table group=firm_pictures_group no_caption=1
		@caption Firmapildid

	@groupinfo bubble_pictures_group caption="Mullipildid" parent=foldersa

		@property bubble_pictures_toolbar type=toolbar group=bubble_pictures_group no_caption=1

		@property bubble_pictures type=table group=bubble_pictures_group no_caption=1
		@caption Mullipildid


	@groupinfo category_settings caption="Kategooriate seaded" parent=foldersa

		@property sideways_pages type=textarea rows=4 cols=80 table=objects field=meta method=serialize group=category_settings
		@comment Ilmselt hetkel ei t&ouml;&ouml;ta!
		@caption Landscape vaatega lehed

	groupinfo folders caption="Kataloogid (deprecated)" parent=foldersa

		property folders type=table store=no group=folders no_caption=1

		property inf_pages type=textarea rows=3 cols=40 group=folders field=meta method=serialize table=objects
		caption L&otilde;pmatus vaatega lehed

	groupinfo folderspri caption="Kataloogide m&auml;&auml;rangud (deprecated)" parent=foldersa

		property foldpri type=textarea rows=20 cols=20 table=objects field=meta method=serialize group=folderspri
		caption T&auml;htede prioriteedid

	groupinfo foldersnames caption="Kaustade nimed (deprecated)" parent=foldersa

		property foldernames type=table store=no group=foldersnames
		caption Kaustade nimed impordi jaoks

@groupinfo containers caption="Konteinerid"

	@property containers_toolbar type=toolbar group=containers no_caption=1
	@caption Konteinerite t&oouml;&ouml;riisariba

	@property containers_list type=table group=containers no_caption=1
	@caption Konteinerite nimekiri

	@property container_info type=table group=containers
	@caption Konteineri info

	@property container_rows type=table group=containers
	@caption Konteineri read

@groupinfo views caption="Vaated"

	@property force_7_view type=textbox table=objects field=meta method=serialize group=views
	@caption 7 pildiga lehed

	@property force_inf_view type=textbox table=objects field=meta method=serialize group=views
	@caption L&otilde;pmatute pildiga lehehed

	@property force_10_view type=textbox table=objects field=meta method=serialize group=views
	@caption 10 pildiga lehed

	@property force_8_view type=textbox table=objects field=meta method=serialize group=views
	@caption 8 pildiga lehed

	@property force_no_side_view type=textbox table=objects field=meta method=serialize group=views
	@caption Ilma detailvaate lisapiltideta lehed

	property force_7_view_for_trends type=textbox table=objects field=meta method=serialize group=views
	caption 7 pildiga trendide lehed
	comment Ainult BonPrix. lk koodide asemel kaustade id-d, mille all 7st vaadet n&auml;idata

@groupinfo jm caption="J&auml;relmaks"

	@property jm_clothes type=textarea rows=5 cols=50 table=objects field=meta method=serialize group=jm
	@caption R&otilde;ivad

	@property jm_lasting type=textarea rows=5 cols=50 table=objects field=meta method=serialize group=jm
	@caption Kestvuskaubad

	@property jm_furniture type=textarea rows=5 cols=50 table=objects field=meta method=serialize group=jm
	@caption M&ouml;&ouml;bel

@groupinfo delete caption="Kustutamine"

	@property del_prods type=textarea rows=10 cols=50 store=no group=delete
	@caption Kustuta tooted koodidega (komaga eraldatud)

	@property del_prods_by_filename type=textbox field=meta method=serialize group=delete
	@caption Kustuta tooted vastavalt failikoodile

	@property del_prods_by_filename_info type=text store=no group=delete
	@caption Info

@groupinfo products_xml caption="[dev] Toodete XML"
@default group=products_xml

	@property csv_files_location type=textbox field=meta method=serialize
	@caption CSV failid

	@property xml_file_link type=text
	@caption Genereeritud XML fail

	@property csv_files_list type=table no_caption=1

@groupinfo availability caption="Laoseisud"
@default group=availability

	@property availability_ftp_host type=textbox table=objects field=meta method=serialize
	@caption FTP aadress
	@comment FTP serveri aadress

	@property availability_ftp_user type=textbox table=objects field=meta method=serialize
	@caption FTP kasutaja
	@comment Kasutajanimi, millega FTP serverisse logitakse

	@property availability_ftp_password type=password table=objects field=meta method=serialize
	@caption FTP parool
	@comment Parool FTP kasutajale

	@property availability_ftp_file_location type=textbox table=objects field=meta method=serialize size=70
	@caption Faili asukoht

	@property availability_import_link type=text store=no
	@caption Laoseisu import

@reltype FOLDER value=1 clid=CL_MENU
@caption kataloog

@reltype SHOP_PRODUCT_CONFIG_FORM value=2 clid=CL_CFGFORM
@caption Lao toote seadete vorm

*/

define('BIG_PICTURE', 1);
define('SMALL_PICTURE', 2);

class otto_import extends class_base implements warehouse_import_if
{
	var $not_found_products = array();

	function otto_import()
	{
		$this->init(array(
			"tpldir" => "applications/shop/otto/otto_import",
			"clid" => CL_OTTO_IMPORT
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case 'files_to_import':
				$prop['value'] = nl2br($arr['obj_inst']->prop('fnames'));
				break;
			case "last_import_log":
				if (!empty($_GET['dragut'])){
					$this->clean_up_otto_prod_img_table();
					die();
				}
				if (file_exists(aw_ini_get("site_basedir")."/files/import_last_log.txt"))
				{
					$prop["value"] = join("<br>\n", @file(aw_ini_get("site_basedir")."/files/import_last_log.txt"));
				}
				break;
			case "products_count":
				$ol = new object_list(array(
					'class_id' => CL_SHOP_PRODUCT,
				));
				$prop['value'] = $ol->count();
				break;
			case "folders":
				$this->do_folders_tbl($arr);
				break;

			case "view_img":
				$prop["value"] = "<a href='javascript:void(0)' onClick='viewimg()'>Vaata pilti</a>";
				$prop["value"] .= "<script language=\"javascript\">\n";
				$prop["value"] .= "function viewimg() { var url;\n
					url = \"http://image01.otto.de/pool/OttoDe/de_DE/images/formata/\"+document.changeform.orig_img.value+\".jpg\";
					window.open(url,\"popupx\", \"width=400,height=600\");
				}\n";
				$prop["value"] .= "</script>\n";
				break;

			case "first_site_to_search_images":
				// this one for Bonprix only:
				if (aw_ini_get("site_id") == 276 || aw_ini_get("site_id") == 277)
				{
					$prop['options'] = array(
						"bp_pl" => "Poola Bonprix",
						"bp_de" => "Saksa Bonprix"
					);
					$retval = PROP_OK;
				}
				else
				{
					$retval = PROP_IGNORE;
				}
				break;

			case "import_discount_products":
				$prop['value'] = html::href(array(
					"caption" => t("Impordi soodustooted"),
					"url" => $this->mk_my_orb("import_discount_products", array(
						"id" => $arr['obj_inst']->id(),
					)),
				));
				break;

			case "clear_discount_products":
				$prop['value'] = html::href(array(
					"caption" => t("T&uuml;hjenda soodustoodete tabel ( aktiivse keele alt: ".aw_global_get('lang_id')." )"),
					"url" => $this->mk_my_orb("clear_discount_products", array(
						"id" => $arr['obj_inst']->id(),
						"lang_id" => aw_global_get('lang_id')
					)),
				));

				$prop['value'] .= ' ### ';
				$prop['value'] .= html::href(array(
					"caption" => t("T&uuml;hjenda soodustoodete tabel ( olenemata keelest! )"),
					"url" => $this->mk_my_orb("clear_discount_products", array(
						"id" => $arr['obj_inst']->id(),
					)),
				));
				break;

			case "discount_products_count":
				$all_products_count = $this->db_fetch_field("select count(*) as count from bp_discount_products", "count");
				$products_count = $this->db_fetch_field("select count(*) as count from bp_discount_products where lang_id=".aw_global_get('lang_id'), "count");
				$prop['value'] = 'Aktiivse keele all ('.aw_global_get('lang_id').'): <strong>'.$products_count.'</strong>';
				$prop['value'] .= '<br />';
				$prop['value'] .= 'K&otilde;ik kokku (olenemata keelest): <strong>'.$all_products_count.'</strong>';
				break;

			case "foldernames":
				$this->_foldernames($arr);
				break;
			case "del_prods_by_filename_info":
				$prop['value'] = t("Failikood moodustub failinimest j&auml;rgmiselt: faili nimi: EST.TT010, selle faili kood: T010. Toodete otsimisel arvestatakse aktiivse keele ja saidi id-ga.<br /> ");
				$prop['value'] .= t("Failikoodist v&otilde;ib kirjutada ka ainult alguse v&otilde;i l&otilde;pu, puuduvat osa t&auml;histab sel juhul '%' m&auml;rk <br />");
				$prop['value'] .= t("N&auml;iteks k&otilde;ik 'G'-ga algavad t&auml;hised oleks: 'G%'. K&otilde;ik 'H0' algusega oleks 'H0%'. '%' m&auml;rgi v&otilde;ib ka &auml;ra j&auml;tta, sel juhul otsitakse t&auml;pselt selle j&auml;rgi, mis tekstikastis on. ")."<br />";

				$prop['value'] .= t("Peale salvestamist kuvatakse teile tekstikastis olevale stringile vastavad lehed, mille alusel hakatakse tooteid kustutama. Muutes stringi tekstikastis ja uuesti salvastades, saate veenduda, et &otilde;igete t&auml;histe j&rgi hakatakse tooteid otsima.")."<br />";
				$prop['value'] .= t("Selleks, et tooted kustutataks, m&auml;rkige &auml;ra l&otilde;ppu tekkiv m&auml;rkeruut ja salvestage. Peale seda kustutakse k&auml;ik tooted ja sellega seonduvad hinnad/suurused, mille juures on vastavalt tekstikasti sisestatud otsingustringile vastav t&auml;his (lehe nimetus).")."<br />";
				$prop['value'] .= "<strong>".t("Tooted ja nendega seonduv info kustutatakse s&uuml;steemist l&otilde;plikult!!!")."</strong><br />";


				$tmp = $arr['obj_inst']->prop('del_prods_by_filename');
				$lang_id = $arr['obj_inst']->lang_id();
				if ( !empty( $tmp ) )
				{
					$prop['value'] .= t('Kustutan j&auml;rgnevate t&auml;histega failides olevad tooted: ').'<br />';

					$this->db_query("
						select
							distinct(user18) as pg
						from
							aw_shop_products
							left join objects on objects.brother_of = aw_shop_products.aw_oid
						where
							aw_shop_products.user18 like '$tmp' AND
							objects.lang_id = $lang_id AND
							objects.status > 0

					");
					$show_confirm = false;
					while ($row = $this->db_next())
					{
						$show_confirm = true;
						$prop['value'] .= '- '.$row['pg'].' <br />';
					}
					if ($show_confirm)
					{
						$prop['value'] .= '<span style="color: red;">'.t('Kustuta?').'</span>';
						$prop['value'] .= html::checkbox(array(
							'name' => 'confirm_del_prods_by_filename',
							'value' => 1
						));
					}
				}
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
			case "folders":
				$this->db_query("DELETE FROM otto_imp_t_p2p WHERE lang_id = ".aw_global_get("lang_id"));
				foreach(safe_array($arr["request"]["dat"]) as $cnt => $row)
				{
					foreach(explode(",", $row["pgs"]) as $pg)
					{
						if ($pg && $row["awfld"])
						{
							$this->db_query ("INSERT INTO otto_imp_t_p2p (pg,fld,lang_id)
								VALUES('$pg','$row[awfld]','".aw_global_get("lang_id")."')
							");
						}
					}
				}
				break;

			case "to_img":
				if ($prop["value"] != "" /*&& $arr["request"]["orig_img"] != ""*/)
				{
					// do replace
					$toims = explode(",", $prop["value"]);
					$q = "
						UPDATE
							otto_prod_img
						SET
							show_imnr = '".$arr["request"]["orig_img"]."'
						WHERE
							imnr IN (".join(",", map("'%s'", $toims)).")
					";
					$this->db_query($q);
				}
				break;

			case "del_prods":
				if ($prop["value"] != "")
				{
					$product_codes = explode(",", $prop["value"]);
					foreach ($product_codes as $key => $product_code)
					{
						$product_codes[$key] = str_replace(" ", "", $product_code);
					}
					$this->_do_del_prods($product_codes);
				}
				break;

			case "del_prods_by_filename":

				if ($arr['request']['confirm_del_prods_by_filename'] == 1 && !empty($arr['request']['del_prods_by_filename']))
				{
					$this->_do_del_prods(array(), $arr['request']['del_prods_by_filename']);
				}
				break;

			case "foldernames":
				$dat = $arr["request"]["dat"];
				$inf = array();
				foreach(safe_array($dat) as $cnt => $entry)
				{
					if (trim($entry["cat"]) != "" && trim($entry["fld"]) != "")
					{
						foreach(explode(",", $entry["fld"]) as $r_fld)
						{
							$inf[] = $r_fld."=".$entry["cat"];
						}
					}
				}
				$val = join(",", $inf);
				$arr["obj_inst"]->set_meta("foldernames", $val);
				break;
		}
		return $retval;
	}
/*
	function callback_on_load($arr)
	{

	}
*/
	function callback_mod_tab($arr)
	{
		if ($arr['id'] == 'discount_products')
		{
			// lets show the tab only in bonprix
			if (aw_ini_get("site_id") != 276 && aw_ini_get("site_id") != 277)
			{
				return false;
			}
		}
	}

	function callback_mod_layout($arr)
	{
		if ($arr['name'] == 'vbox_products_manager_search' && isset($arr['request']['products_manager_prod_id']) && $this->can('view', $arr['request']['products_manager_prod_id']))
		{
			return false;
		}
		return true;
	}

	function callback_mod_reforb($arr)
	{
		if ( isset($_GET['container_id']) && $_GET['container_id'] && ($arr['group'] == 'containers') )
		{
			$arr['container_id'] = (int)$_GET['container_id'];
		}
	}

	function callback_mod_retval($arr)
	{
		if ( isset($arr['request']['container_id']) )
		{
			$arr['args']['container_id'] = $arr['request']['container_id'];
		}

		////
		// products manager
		if (!empty($arr['request']['products_search']))
		{
			$arr['args']['products_manager_pcode'] = $arr['request']['products_manager_pcode'];
			$arr['args']['products_manager_prod_page'] = $arr['request']['products_manager_prod_page'];
			$arr['args']['products_manager_prod_image'] = $arr['request']['products_manager_prod_image'];
			$arr['args']['products_search'] = $arr['request']['products_search'];
		}

		$product_id = (isset($arr['request']['product_manager_prod_id'])) ? (int)$arr['request']['products_manager_prod_id'] : '';
		if ( $this->can('view', $product_id) )
		{
			$product_obj = new object($product_id);
			if ($product_obj->class_id() == CL_SHOP_PRODUCT)
			{
				$arr['args']['products_manager_prod_id'] = $product_id;
			}
		}

		////
		// category data filter
		if ( isset($arr['request']['data_filter']['aw_folder_id']) )
		{
			$arr['args']['filter_aw_folder_id'] = $arr['request']['data_filter']['aw_folder_id'];
		}
		if ( isset($arr['request']['data_filter']['category']) )
		{
			$arr['args']['filter_category'] = $arr['request']['data_filter']['category'];
		}

		////
		// bubble filter
		if ( isset($arr['request']['bubble_filter']['category']) )
		{
			$arr['args']['bubble_filter']['category'] = $arr['request']['bubble_filter']['category'];
		}
		if ( isset($arr['request']['bubble_filter']['image_url']) )
		{
			$arr['args']['bubble_filter']['image_url'] = $arr['request']['bubble_filter']['image_url'];
		}
		if ( isset($arr['request']['bubble_filter']['title']) )
		{
			$arr['args']['bubble_filter']['title'] = $arr['request']['bubble_filter']['title'];
		}

		////
		// firm filter
		if ( isset($arr['request']['firm_filter']['category']) )
		{
			$arr['args']['firm_filter']['category'] = $arr['request']['firm_filter']['category'];
		}
		if ( isset($arr['request']['firm_filter']['image_url']) )
		{
			$arr['args']['firm_filter']['image_url'] = $arr['request']['firm_filter']['image_url'];
		}
		if ( isset($arr['request']['firm_filter']['title']) )
		{
			$arr['args']['firm_filter']['title'] = $arr['request']['firm_filter']['title'];
		}

	}

	function callback_pre_save($arr)
	{
		if($arr['obj_inst']->prop('do_fixes'))
		{
			$arr['obj_inst']->set_prop('do_fixes',0);

			$this->do_post_import_fixes($arr['obj_inst']);
		}

		if ($arr["obj_inst"]->prop("do_i"))
		{
			echo "START IMPORT<br>";
			if ($arr["obj_inst"]->prop("do_pict_i"))
			{
				echo "[ Tee piltide import ]<br>\n";
			}
			$arr["obj_inst"]->set_prop("do_i", 0);
			$arr["obj_inst"]->set_prop("do_pict_i", 0);
			$this->do_prod_import(array(
				'otto_import' => $arr["obj_inst"],
				'doing_pict_i' => $arr['request']['do_pict_i'],
				'update_product_images' => $arr['request']['update_product_images'],
				'force_update_product_images' => $arr['request']['force_update_product_images'],
				'update_product_categories' => $arr['request']['update_product_categories'],
				'force_update_product_categories' => $arr['request']['force_update_product_categories'],
				'update_connected_products' => $arr['request']['update_connected_products'],
				'force_update_connected_products' => $arr['request']['force_update_connected_products']
			));
		}

	}

	function callback_post_save($arr)
	{

	}

	function _init_fn_t(&$t)
	{
		$t->define_field(array(
			"name" => "cat_name",
			"caption" => t("Kategooria nimi"),
		));

		$t->define_field(array(
			"name" => "fld_name",
			"caption" => t("AW Kataloogi ID"),
		));
	}
	function _foldernames($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_fn_t($t);

		$val = $arr["obj_inst"]->meta("foldernames");
		$inf = explode(",", $val);
		$dat = array();
		foreach($inf as $pair)
		{
			list($k, $v) = explode("=", $pair);
			$dat[trim($k)] = trim($v);
		}

		$cnt = 1;
		foreach($dat as $aw_fld => $name)
		{
			$t->define_data(array(
				"cat_name" => html::textbox(array(
					"name" => "dat[$cnt][cat]",
					"value" => $name
				)),
				"fld_name" => html::textbox(array(
					"name" => "dat[$cnt][fld]",
					"value" => $aw_fld
				)),
			));
			$cnt++;
		}

		for($i = 0; $i<10; $i++)
		{
			$t->define_data(array(
				"cat_name" => html::textbox(array(
					"name" => "dat[$cnt][cat]",
					"value" => ""
				)),
				"fld_name" => html::textbox(array(
					"name" => "dat[$cnt][fld]",
					"value" => ""
				)),
			));
			$cnt++;
		}
		$t->set_sortable(false);
	}

	function _get_update_params_table($arr)
	{

		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);
		$t->define_field(array(
			'name' => 'caption',
		));
		$t->define_field(array(
			'name' => 'update',
			'caption' => t('Uuenda'),
			'align' => 'center'
		));
		$t->define_field(array(
			'name' => 'force_update',
			'caption' => t('Uuenda alati'),
			'align' => 'center'
		));
		$t->define_field(array(
			'name' => 'desc',
			'caption' => t('Kirjeldus')
		));

		$t->set_header( t('* Kui on valitud "Uuenda", siis arvestatakse toote juures olevat m&auml;rget, et selle toote andmeid ei tohi uundada. See m&auml;rge tekib tavaliselt siis, kui tootele on k&auml;sitsi lisatud pilte/kategooriaid vms. Kui valida "Uuenda alati", siis uuendatakse vastavaid toote andmeid igal juhul, hoolimata sellest m&auml;rkest.') );

		$t->define_data(array(
			'caption' => t('uuenda toote pilte'),
			'update' => html::checkbox(array(
				'name' => 'update_product_images',
				'value' => 1
			)),
			'force_update' => html::checkbox(array(
				'name' => 'force_update_product_images',
				'value' => 1
			)),
			'desc' => t('Uuendatakse toote juures olevaid pilte. Kui toode on juba olemas, ja seal on pilte, siis need pildid j&auml;etakse samuti alles. ')
		));
		$t->define_data(array(
			'caption' => t('uuenda toote kategooriaid'),
			'update' => html::checkbox(array(
				'name' => 'update_product_categories',
				'value' => 1
			)),
			'force_update' => html::checkbox(array(
				'name' => 'force_update_product_categories',
				'value' => 1
			)),
			'desc' => t('Uuendatakse toote kategooriaid ja seda, milliste sektsioonide/kataloogide all tooteid n&auml;idatakse. Kui on vaja, et tooted ilmuksid m&otilde;ne uue kausta alla v&otilde;i et kuskilt kausta alt &auml;ra liiguksid, siis tuleb see valik &auml;ra m&auml;rkida.')
		));
		$t->define_data(array(
			'caption' => t('uuenda seotud tooteid'),
			'update' => html::checkbox(array(
				'name' => 'update_connected_products',
				'value' => 1
			)),
			'force_update' => html::checkbox(array(
				'name' => 'force_update_connected_products',
				'value' => 1
			)),
			'desc' => t('Uuendatakse &uuml;henduspiltide j&auml;rgi toodete n&auml;htavust erinevate kataloogide all. Ehk uuendatakse toote juures valikut, kas toode on toodete nimekirjas n&auml;htav v&otilde;i mitte.')
		));
	}

	function _get_import_log_list($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);
		$t->define_field(array(
			'name' => 'products_count',
			'caption' => t('Toodete arv'),
			'width' => '10%',
			'align' => 'center'
		));
		$t->define_field(array(
			'name' => 'import_time',
			'caption' => t('Impordi aeg'),
		));
		$t->define_field(array(
			'name' => 'csv_files',
			'caption' => t('csv failid')
		));
		$t->define_field(array(
			'name' => 'view',
			'caption' => t('Vaata'),
		));
		$sql = 'select count(*) as count,csv_files,import_time from otto_import_log group by import_time order by import_time desc';
		if (aw_global_get('lang_id') == 7 || aw_global_get('lang_id') == 6)
		{
			$sql = 'select count(*) as count,csv_files,import_time from otto_import_log where csv_files like \'LET%\'group by import_time order by import_time desc';
		}
		if (aw_global_get('lang_id') == 1)
		{
			$sql = 'select count(*) as count,csv_files,import_time from otto_import_log where csv_files like \'EST%\'group by import_time order by import_time desc';
		}
		$this->db_query($sql);
		while ($row = $this->db_next())
		{
			$t->define_data(array(
				'products_count' => $row['count'],
				'import_time' => date('d.m.Y H:m:s', $row['import_time']),
				'csv_files' => $row['csv_files'],
				'view' => html::href(array(
					'caption' => t('Vaata'),
					'url' => aw_url_change_var('import_time',  $row['import_time'])

				)),


			));
		}
	}


	function _get_import_log_item($arr)
	{
		if (empty($_GET['import_time']))
		{
			return PROP_IGNORE;
		}

		$t = &$arr['prop']['vcl_inst'];
		$t->define_field(array(
			'name' => 'oid',
			'caption' => t('OID')
		));
		$t->define_field(array(
			'name' => 'data',
			'caption' => t('Andmed')
		));
		$t->define_field(array(
			'name' => 'images',
			'caption' => t('Pildid'),
		));

		$this->db_query('select * from otto_import_log where import_time = '.(int)$_GET['import_time']);

		while ($row = $this->db_next())
		{
			$prod_id = $row['product_id'];

			$prod_obj = new object($prod_id);

			$data_str = '<strong>'.$prod_obj->name().'</strong><br />';
			$data_str .= $prod_obj->prop('userta2');
			$data_str .= '<br />Min hind: '.$prod_obj->prop('user14').'.- | Max hind: '.$prod_obj->prop('user15').'.-';
			$data_str .= '<br />Kategooriad: '.$prod_obj->prop('user11');
			$data_str .= '<br />Leht: '.$prod_obj->prop('user18');
			$data_str .= '<br />Tootekoodid: '.$prod_obj->prop('user6');
			$data_str .= '<br />Toode on nimekirjas n&auml;htav: '.(($prod_obj->prop('userch4') == 1) ? 'Jah' : 'Ei');
			$data_str .= '<br />'.html::href(array(
				'caption' => t('Muuda toodet'),
				'url' => $this->mk_my_orb('change', array(
					'id' => $arr['obj_inst']->id(),
					'group' => 'products_manager',
					'products_manager_prod_id' => $prod_obj->id(),
					'return_url' => post_ru()
				), CL_OTTO_IMPORT),
			));

			$images = explode(',', $prod_obj->prop('user3'));

			$images_str = '';
			foreach ($images as $image)
			{
				$images_str .= html::img(array(
					'url' => aw_ini_get('baseurl').'/vv_product_images/'.$image{0}.'/'.$image{1}.'/'.$image.'_2.jpg',
					'width' => '100',
					'alt' => $image,
					'title' => $image,
				)).html::checkbox(array(
					'name' => 'images['.$prod_id.']['.$image.']',
					'value' => 1,
					'caption' => t('Kustuta?')
				)).'||';
			}


			$t->define_data(array(
				'oid' => html::href(array(
					'url' => $this->mk_my_orb('change', array(
						'id' => $prod_id,
						'return_url' => post_ru(),
					), CL_SHOP_PRODUCT),
					'caption' => $prod_id
				)),
				'data' => $data_str,
				'images' => $images_str
			));
		}

		return PROP_OK;
	}

	function _set_import_log_item($arr)
	{
		foreach (safe_array($arr['request']['images']) as $prod_id => $images)
		{
			$prod = new object($prod_id);
			$existing_images = explode(',', $prod->prop('user3'));
			foreach ($images as $image => $nr)
			{
				$key = array_search($image, $existing_images);
				if ($key !== false)
				{
					unset($existing_images[$key]);
				}
			}
			$prod->set_prop('user3', implode(',', $existing_images));
			$prod->save();
		}
	}

	function _get_products_manager_toolbar($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		if ($this->can('view', $arr['request']['products_manager_prod_id']))
		{
			$t->add_button(array(
				'name' => 'search',
				'tooltip' => t('Otsing'),
				'img' => 'search.gif',
				'url' => $this->mk_my_orb('change', array(
					'id' => $arr['obj_inst']->id(),
					'group' => 'products_manager'
				), CL_OTTO_IMPORT),
			));

		}
		return PROP_OK;
	}

	function _get_products_manager_prod_id($arr)
	{
		// don't put '0' into textbox if no prod_id is set
		if (!empty($arr['request']['products_manager_prod_id']))
		{
			$arr['prop']['value'] = $arr['request']['products_manager_prod_id'];
		}
		return PROP_OK;
	}

	function _get_products_manager_pcode($arr)
	{
		$arr['prop']['value'] = $arr['request']['products_manager_pcode'];
		return PROP_OK;
	}

	function _get_products_manager_prod_page($arr)
	{
		$arr['prop']['value'] = strtoupper($arr['request']['products_manager_prod_page']);
		return PROP_OK;
	}

	function _get_products_manager_prod_image($arr)
	{
		$arr['prop']['value'] = $arr['request']['products_manager_prod_image'];
		return PROP_OK;
	}

	function _get_products_manager_search_results_table($arr)
	{
		if ($this->can('view', $arr['request']['products_manager_prod_id']))
		{
			return PROP_IGNORE;
		}

		$t = &$arr['prop']['vcl_inst'];

		$t->set_sortable(false);
		$t->define_field(array(
			'name' => 'oid',
			'caption' => t('Toote OID'),
		));
		$t->define_field(array(
			'name' => 'name',
			'caption' => t('Nimetus'),
		));
		$t->define_field(array(
			'name' => 'pcodes',
			'caption' => t('Toote koodid'),
		));
		$t->define_field(array(
			'name' => 'video',
			'caption' => t('Video'),
		));
		$t->define_field(array(
			'name' => 'change',
			'caption' => t('Muuda'),
		));

		if ($arr['request']['products_search'])
		{
			$filter = array('class_id' => CL_SHOP_PRODUCT);
			if (!empty($arr['request']['products_manager_prod_id']))
			{
				$filter['oid'] = $arr['request']['products_manager_prod_id'];
			}
			if (!empty($arr['request']['products_manager_pcode']))
			{
				$filter['user6'] = '%'.$arr['request']['products_manager_pcode'].'%';
			}
			if (!empty($arr['request']['products_manager_prod_page']))
			{
				$filter['user18'] = '%'.$arr['request']['products_manager_prod_page'].'%';
			}
			if (!empty($arr['request']['products_manager_prod_image']))
			{
				$filter['user3'] = '%'.$arr['request']['products_manager_prod_image'].'%';
			}

			$ol = new object_list();
			if (count($filter) > 1)
			{
				$ol = new object_list($filter);
			}

			foreach ($ol->arr() as $oid => $o)
			{
				$t->define_data(array(
					'oid' => $oid,
					'name' => $o->name(),
					'pcodes' => $o->prop('user6'),
					'video' => $o->prop('user8'),
					'change' => html::href(array(
						'caption' => t('Muuda'),
						'url' => $this->mk_my_orb('change', array(
							'id' => $arr['obj_inst']->id(),
							'products_manager_prod_id' => $oid,
							'group' => 'products_manager'
						), CL_OTTO_IMPORT)
					)),
				));
			}
			// xxx lets comment it out until all otto sites will be running on new code
			// $t->set_caption(sprintf(t('Leiti %s toodet'), $ol->count()));
		}

		return PROP_OK;

	}

	function _get_products_manager_item_table($arr)
	{
		$prod_id = $arr['request']['products_manager_prod_id'];

		if (!$this->can('view', $prod_id))
		{
			return PROP_IGNORE;
		}

		$prod_obj = new object($prod_id);
		if ($prod_obj->class_id() != CL_SHOP_PRODUCT)
		{
			return PROP_IGNORE;
		}

		if ( $this->can('view', $prod_id) )
		{

			$t = &$arr['prop']['vcl_inst'];
			// xxx lets comment it out until all otto sites will be running on new code
			// $t->set_caption($prod_obj->name());
			$t->set_sortable(false);
			$t->define_field(array(
				'name' => 'caption',
				'caption' => '',
			));
			$t->define_field(array(
				'name' => 'data',
				'caption' => t('Andmed'),
			));

			$prod_id_hidden_element = html::hidden(array(
				'name' => 'products_manager_prod_id',
				'value' => $prod_id
			));

			$prod_link = html::href(array(
				'caption' => $prod_id,
				'url' => $this->mk_my_orb('change', array(
					'id' => $prod_id,
					'return_url' => get_ru()
				), CL_SHOP_PRODUCT)
			));

			$t->define_data(array(
				'caption' => t('Toode'),
				'data' => $prod_link . $prod_id_hidden_element . ' - <strong>'.$prod_obj->name().'</strong>'
			));

			$t->define_data(array(
				'caption' => t('Toote kirjeldus'),
				'data' => $prod_obj->prop('userta2')
			));

			// categories:
			$t->define_data(array(
				'caption' => t('Kategooriad'),
				'data' => html::textbox(array(
					'name' => 'categories',
					'value' => $prod_obj->prop('user11'),
					'size' => 100
				)),
			));

			// product page:
			$t->define_data(array(
				'caption' => t('Leht'),
				'data' => html::textbox(array(
					'name' => 'product_page',
					'value' => $prod_obj->prop('user18'),
					'size' => 10
				)). ' J&auml;rjekord: '.$prod_obj->ord(),
			));

			// product codes:
			$t->define_data(array(
				'caption' => t('Toote koodid'),
				'data' => html::textbox(array(
					'name' => 'product_codes',
					'value' => $prod_obj->prop('user6'),
					'size' => 100
				)),
			));

			$t->define_data(array(
				'caption' => t('V&auml;rvid'),
				'data' => $prod_obj->prop('user7')
			));

			$t->define_data(array(
				'data' => html::checkbox(array(
					'name' => 'show_in_products_list',
					'label' => t('N&auml;idatakse toodete nimekirjas'),
					'value' => 1,
					'checked' => ($prod_obj->prop('userch4') == 1) ? true : false
				)).' '.html::checkbox(array(
					'name' => 'update_allowed',
					'label' => t('Toote andmeid ei uuendata impordi k&auml;igus'),
					'value' => 1,
					'checked' => ($prod_obj->prop('userch2') == 1) ? true : false
				)),
			));

			// other products:
			$other_prods_ids = $prod_obj->prop('user4');
			$other_prods_links = array();
			if (!empty($other_prods_ids))
			{
				foreach (explode(',', $other_prods_ids) as $other_prod_id)
				{
					if ($this->can('view', $other_prod_id))
					{
						$other_prod = new object($other_prod_id);
						$other_prods_links[] = html::href(array(
							'caption' => $other_prod->name().' ('.$other_prod_id.')',
							'url' => aw_url_change_var('products_manager_prod_id', $other_prod_id)
						));
					}
				}
			}
			$t->define_data(array(
				'caption' => t('Teised tooted'),
				'data' => html::textbox(array(
					'name' => 'other_products',
					'value' => $prod_obj->prop('user4'),
					'size' => 40
				)).implode(' | ', $other_prods_links),
			));

			$t->define_data(array(
				'caption' => t('&Uuml;hendav pilt'),
				'data' => $prod_obj->prop('user2')
			));

			$pics_str = '';
			$pics = explode(',', $prod_obj->prop('user3'));

			foreach ($pics as $pic)
			{
				$pics_str .= '<table style="display:inline;">';
				$pics_str .= '<tr><td style="border: 1px solid blue">';
				$pics_str .= html::img(array(
					'url' => aw_ini_get('baseurl').'/vv_product_images/'.$pic{0}.'/'.$pic{1}.'/'.$pic.'_2.jpg?a='.rand(),
				));
				$pics_str .= '</td></tr>';
				$pic_del_check_box = html::checkbox(array(
					'name' => 'pic_del['.$pic.']',
					'value' => 1,
					'caption' => t('Kustuta'),
				));
				$first_pic_radiobutton = html::radiobutton(array(
					'name' => 'first_pic',
					'value' => $pic,
					'caption' => t('Esimeseks pildiks')
				));
				$pics_str .= '<tr><td style="border:1px solid green; text-align:center"><strong>'.$pic.'</strong></td></tr>';
				$pics_str .= '<tr><td style="border:1px solid green">'.$first_pic_radiobutton.'</td></tr>';
				$pics_str .= '<tr><td style="border:1px solid green">'.$pic_del_check_box.'</td></tr>';
				$pics_str .= '</table>';
			}
			$t->define_data(array(
				'caption' => t('Pildid'),
				'data' => $pics_str
			));


			if ($_SESSION['otto_import_prod_manager_image_exists'])
			{

				// lets check, what products have this picture:
				$ol = new object_list(array(
					'class_id' => CL_SHOP_PRODUCT,
					'user3' => '%'.$_SESSION['otto_import_prod_manager_image_exists']['filename'].'%'
				));
				$prods_for_pic = array();
				foreach ($ol->arr() as $prod_oid => $prod)
				{
					$caption = $prod->name();
					if ((int)$_GET['products_manager_prod_id'] == $prod_oid)
					{
						$caption = "<strong>".$prod->name()."</strong>";
					}
					$prods_for_pic[$prod_oid] = html::href(array(
						'url' => aw_url_change_var('products_manager_prod_id', $prod_oid),
						'caption' => $caption
					));
				}
				$new_image_str = "Pilt on juba olemas j&auml;rgmiste toodete juures - ".implode(', ', $prods_for_pic)."<br /> ";
				$new_image_str .= html::hidden(array(
					'name' => 'tmp_filename',
					'value' => $_SESSION['otto_import_prod_manager_image_exists']['tmp_filename']
				));
				$new_image_str .= "Uus pildi nimi: " . html::textbox(array(
					'name' => 'new_picture_name',
					'value' => $_SESSION['otto_import_prod_manager_image_exists']['filename'],
					'size' => 20
				));
				$new_image_str .= html::checkbox(array(
					'name' => 'overwrite',
					'value' => 1,
					'label' => t('Kirjuta &uuml;le')
				));

				unset($_SESSION['otto_import_prod_manager_image_exists']);
			}
			else
			{
				$new_image_str = html::fileupload(array(
					'name' => 'new_picture'
				));
			}
			$t->define_data(array(
				'caption' => t('Uus pilt'),
				'data' => $new_image_str
			));


			// pakendid:
			/*
			foreach($prod_obj->connections_from(array("type" => "RELTYPE_PACKAGING", "sort_by" => "to.user6")) as $c)
			{
				$p = $c->to();
				$t->define_data(array(
					'caption' => $p->name(),
					'data' => $p->prop('user6').' / '.$p->prop('user7').' / '.$p->prop('user8').' / '.$p->prop('user5').' / '.$p->prop('price'),
				));
			}
			*/
		}
		return PROP_OK;
	}

	function _set_products_manager_item_table($arr)
	{

		if (!isset($arr['request']['products_manager_change_submit']) || !$this->can('view', $arr['request']['products_manager_prod_id']))
		{
			return PROP_IGNORE;
		}

		$prod_id = $arr['request']['products_manager_prod_id'];
		$prod_obj = new object($arr['request']['products_manager_prod_id']);

		if ($prod_obj->class_id() != CL_SHOP_PRODUCT)
		{
			return PROP_IGNORE;
		}

		// to prevent saving the same product object multiple times, i set a boolean variable to keep track
		// if i need to save the object or not
		$save = false;

		// check if categories have been changed:
		if ($prod_obj->prop('user11') != $arr['request']['categories'])
		{
			$categories = array();
			foreach (explode(',', $arr['request']['categories']) as $cat)
			{
				if (!empty($cat))
				{
					$categories[] = $cat;
				}
			}


			// delete this products data from products to sections look-up table:
			$this->db_query("delete from otto_prod_to_section_lut where product=".$prod_id);

			// get new sections list for the categories which are set for this product:
			$sections = $this->db_fetch_array("
				select
					aw_folder
				from
					otto_imp_t_aw_to_cat
				where
					category in (".implode(',', map("'%s'", $categories)).") and
					lang_id = ".aw_global_get('lang_id')."
				group by
					aw_folder
			");

			// add the new sections  info to products to section look-up table:
			foreach ($sections as $section)
			{
				$this->db_query('insert into otto_prod_to_section_lut set
					product='.$prod_id.',
					section='.$section['aw_folder'].',
					lang_id='.aw_global_get('lang_id').'
				');
			}

			// save new categories list to product object as well:
			$prod_obj->set_prop('user11', implode(',', $categories));
			// this products categories and pictures will not be updated during import:
			$prod_obj->set_prop('userch2', 1);

			$save = true;
		}

		// product page:
		if ($prod_obj->prop('user18') != $arr['request']['product_page'])
		{
			$prod_obj->set_prop('user18', $arr['request']['product_page']);
			$save = true;
		}

		// product codes
		if ($prod_obj->prop('user6') != $arr['request']['product_codes'])
		{
			$prod_obj->set_prop('user6', $arr['request']['product_codes']);
			$save = true;
		}

		// show this product in products list:
		if ($prod_obj->prop('userch4') != $arr['request']['show_in_products_list'])
		{
			$prod_obj->set_prop('userch4', (int)$arr['request']['show_in_products_list']);
			$save = true;
		}

		// If products import is allowed to change the data
		if ($prod_obj->prop('userch2') != $arr['request']['update_allowed'])
		{
			$prod_obj->set_prop('userch2', (int)$arr['request']['update_allowed']);
			$save = true;
		}

		// other products
		if ($prod_obj->prop('user4') != $arr['request']['other_products'])
		{
			$other_products = array_merge(explode(',', $prod_obj->prop('user4')), explode(',', $arr['request']['other_products']));
			$update_products = array();
			foreach ($other_products as $other_product)
			{
				$other_product = (int)$other_product;
				if ($this->can('view', $other_product))
				{
					$update_products[$other_product] = new object($other_product);

				}
			}
			foreach ($update_products as $other_product)
			{
				$other_product->set_prop('user4', $arr['request']['other_products']);
				$other_product->set_prop('userch2', 1);
				$other_product->save();
			}
		}

		////
		// pics
		$pics = explode(',', $prod_obj->prop('user3'));
		$pics_mod = false;

		// set first picture
		if (!empty($arr['request']['first_pic']))
		{
			array_unshift($pics, $arr['request']['first_pic']);
			$pics = array_unique($pics);
			$pics_mod = true;
		}

		// delete picture
		if (!empty($arr['request']['pic_del']))
		{
			$pics_to_del = array_keys($arr['request']['pic_del']);
			$pics = array_diff($pics, $pics_to_del);
			$pics_mod = true;
		}

		// new picture
		$filename = "";
		$image_source = "";
		$folder = $arr['obj_inst']->prop('images_folder');
		if ($_FILES['new_picture']['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['new_picture']['tmp_name']))
		{
			$filename = basename($_FILES['new_picture']['name'], '.jpg');

			$small_picture = $folder.'/'.$filename{0}.'/'.$filename{1}.'/'.$filename.'_'.SMALL_PICTURE.'.jpg';

			// peaks kontrollima seda, et kas fail on juba olemas ...
			// ma arvan, et piisab v2ikse pildi olemasolu kontrollimisest
			if (!file_exists($small_picture) || $arr['request']['overwrite'] == 1)
			{
				$image_source = $_FILES['new_picture']['tmp_name'];
			}
			else
			{
				$_SESSION['otto_import_prod_manager_image_exists'] = array(
					'tmp_filename' => '/tmp/' . basename($_FILES['new_picture']['tmp_name']),
					'filename' => $filename
				);
				move_uploaded_file($_FILES['new_picture']['tmp_name'], "/tmp/" . basename($_FILES['new_picture']['tmp_name']));
			}
		}

		// so, if there should be any renaming or overwriting done, then lets do it ...
		if (!empty($arr['request']['new_picture_name'])){
			$filename = basename($arr['request']['new_picture_name'], '.jpg');
			$small_picture = $folder.'/'.$filename{0}.'/'.$filename{1}.'/'.$filename.'_'.SMALL_PICTURE.'.jpg';
			if (!file_exists($small_picture) || $arr['request']['overwrite'] == 1){
			//	$image_source = $_SESSION['otto_import_prod_manager_image_exists']['tmp_filename'];
				$image_source = $arr['request']['tmp_filename'];
			//	unset($_SESSION['otto_import_prod_manager_image_exists']);
			}
			else
			{
				$_SESSION['otto_import_prod_manager_image_exists']['filename'] = $filename;
				$_SESSION['otto_import_prod_manager_image_exists']['tmp_filename'] = $arr['request']['tmp_filename'];
			}
		}

		// so, do we need to add the picture now ... ?
		if (!empty($filename) && !empty($image_source))
		{
			$this->get_image(array(
				'source' => $image_source,
				'otto_import' => $arr['obj_inst'],
				'format' => BIG_PICTURE,
				'filename' => $filename,
				'overwrite' => true
			));

			$image_converter = get_instance('core/converters/image_convert');
			$image_converter->load_from_file($folder.'/'.$filename{0}.'/'.$filename{1}.'/'.$filename.'_'.BIG_PICTURE.'.jpg');

			$max_image_width = 168;
			$max_image_height = 240;

			list($image_width, $image_height) = $image_converter->size();

			$new_image_width = $max_image_width;
			$new_image_height = ($max_image_width * $image_height) / $image_width;

			if ($new_image_height > $max_image_height)
			{
				$new_image_width = ($max_image_height * $image_width) / $image_height;
				$new_image_height = ($new_image_width * $image_height) / $image_width;
			}

			$image_converter->resize_simple($new_image_width, $new_image_height);



			$small_picture = $folder.'/'.$filename{0}.'/'.$filename{1}.'/'.$filename.'_'.SMALL_PICTURE.'.jpg';
			$image_converter->save($small_picture, 2);

			array_push($pics, $filename);
			$pics_mod = true;
		}

		if ($pics_mod)
		{
			foreach ($pics as $k => $v)
			{
				if (empty($v))
				{
					unset($pics[$k]);
				}
			}
			$prod_obj->set_prop('user3', implode(',', array_unique($pics)));
			$prod_obj->set_prop('userch2', 1);
			$save = true;
		}

		// if i need to save the product object, then lets do it once in the end:
		if ($save)
		{
			$prod_obj->save();
		}
	}

	function _get_products_manager_change_submit($arr)
	{
		if ($this->can('view', $arr['request']['products_manager_prod_id']))
		{
			return PROP_OK;
		}
		return PROP_IGNORE;
	}

	function _get_files_import_sites_order($arr)
	{
		$table = &$arr['prop']['vcl_inst'];
		$table->set_sortable(false);

		$table->define_field(array(
			'name' => 'order',
			'caption' => t('Jrk'),
			'align' => 'center',
			'width' => '10%'
		));
		$table->define_field(array(
			'name' => 'name',
			'caption' => t('Nimi'),
		));
		$sites = $arr['obj_inst']->meta("files_import_sites_order");

		if (empty($sites))
		{
			$sites = array(
				"heine" => 1,
				"otto" => 2,
				"schwab" => 3,
				"albamoda" => 4,
				"baur" => 5
			);
		}

		foreach ($sites as $key => $value)
		{
			$table->define_data(array(
				'order' => html::textbox(array(
					'name' => "sites_order[".trim($key)."]",
					'value' => $value,
					'size' => 3
				)),
				'name' => $key
			));
		}
		return PROP_OK;
	}

	function _set_files_import_sites_order($arr)
	{
		asort($arr['request']['sites_order']);
		$arr['obj_inst']->set_meta("files_import_sites_order", $arr['request']['sites_order']);
		return PROP_OK;
	}

	function _get_discount_products_list_without_pictures($arr)
	{
		$table = &$arr['prop']['vcl_inst'];
		$table->set_sortable(false);

		$table->define_field(array(
			'name' => 'id',
			'caption' => t('OID'),
			'align' => 'center',
			'width' => '10%'
		));
		$table->define_field(array(
			'name' => 'product',
			'caption' => t('Toode'),
		));
		$table->define_field(array(
			'name' => 'product_codes',
			'caption' => t('Toote koodid'),
		));
		$table->define_field(array(
			'name' => 'product_page',
			'caption' => t('Fail/leht'),
		));
		$sql = "
			select
				bp_discount_products.name as disc_prod_name,
				bp_discount_products.product_code as disc_prod_code,
				bp_discount_products.prod_oid as prod_oid,
				aw_shop_products.aw_oid as aw_oid,
				aw_shop_products.user3 as images,
				aw_shop_products.user6 as codes,
				aw_shop_products.user18 as page,
				objects.name as prod_name
			from
				bp_discount_products
				left join aw_shop_products on (bp_discount_products.prod_oid = aw_shop_products.aw_oid)
				left join objects on (bp_discount_products.prod_oid = objects.oid)
			where
				bp_discount_products.lang_id = ".aw_global_get('lang_id')." and
				aw_shop_products.aw_oid is not null and
				aw_shop_products.user3 = \"\"
		";

		$prods = $this->db_fetch_array($sql);

		foreach ($prods as $prod)
		{
			$table->define_data(array(
				'id' => $prod['prod_oid'],
				'product' => html::href(array(
					'caption' => $prod['prod_name'].' ('.$prod['disc_prod_name'].')',
					'url' => $this->mk_my_orb('change', array(
						'id' => $arr['obj_inst']->id(),
						'group' => 'products_manager',
						'products_manager_prod_id' => $prod['prod_oid'],
						'return_url' => post_ru()
					), CL_OTTO_IMPORT),
				)),
				'product_codes' => $prod['codes'],
				'product_page' => $prod['page']
			));
		}


		return PROP_OK;
	}

	function _get_discount_products_list_with_pictures($arr)
	{
		$table = &$arr['prop']['vcl_inst'];
		$table->set_sortable(false);

		$table->define_field(array(
			'name' => 'id',
			'caption' => t('OID'),
			'align' => 'center',
			'width' => '10%'
		));
		$table->define_field(array(
			'name' => 'product',
			'caption' => t('Toode'),
		));
		$table->define_field(array(
			'name' => 'product_codes',
			'caption' => t('Toote koodid'),
		));
		$table->define_field(array(
			'name' => 'product_page',
			'caption' => t('Fail/leht'),
		));
		$sql = "
			select
				bp_discount_products.name as disc_prod_name,
				bp_discount_products.product_code as disc_prod_code,
				bp_discount_products.prod_oid as prod_oid,
				aw_shop_products.aw_oid as aw_oid,
				aw_shop_products.user3 as images,
				aw_shop_products.user6 as codes,
				aw_shop_products.user18 as page,
				objects.name as prod_name
			from
				bp_discount_products
				left join aw_shop_products on (bp_discount_products.prod_oid = aw_shop_products.aw_oid)
				left join objects on (bp_discount_products.prod_oid = objects.oid)
			where
				bp_discount_products.lang_id = ".aw_global_get('lang_id')." and
				aw_shop_products.aw_oid is not null and
				aw_shop_products.user3 != \"\"
		";

		$prods = $this->db_fetch_array($sql);

		foreach ($prods as $prod)
		{
			$table->define_data(array(
				'id' => $prod['prod_oid'],
				'product' => html::href(array(
					'caption' => $prod['prod_name'].' ('.$prod['disc_prod_name'].')',
					'url' => $this->mk_my_orb('change', array(
						'id' => $arr['obj_inst']->id(),
						'group' => 'products_manager',
						'products_manager_prod_id' => $prod['prod_oid'],
						'return_url' => post_ru()
					), CL_OTTO_IMPORT),
				)),
				'product_codes' => str_replace(',', ', ', $prod['codes']),
				'product_page' => $prod['page']
			));
		}
		return PROP_OK;
	}

	function _get_containers_toolbar($arr)
	{
		$containers = $arr['obj_inst']->meta('containers');
		$new_key = max( array_keys( $containers ) ) + 1;

		$toolbar = &$arr['prop']['vcl_inst'];
		$toolbar->add_button(array(
			"name" => "new",
			"tooltip" => t('Uus konteiner'),
			"url" => $this->mk_my_orb('change', array(
				'id' => $arr['obj_inst']->id(),
				'group' => 'containers',
				'container_id' => $new_key
			)),
			"img" => "new.gif",
		));
		$toolbar->add_separator();

		$toolbar->add_button(array(
			"name" => "list",
			"tooltip" => t('Konteinerite nimekiri'),
			"url" => $this->mk_my_orb('change', array(
				'id' => $arr['obj_inst']->id(),
				'group' => 'containers',
			)),
			"img" => "iother_promo_box.gif",
		));

		return PROP_OK;
	}

	function _get_containers_list($arr)
	{
		if ( isset($arr['request']['container_id']) )
		{
			return PROP_IGNORE;
		}

		$table = &$arr['prop']['vcl_inst'];
		$table->set_sortable(false);

		$table->define_field(array(
			'name' => 'id',
			'caption' => t('ID'),
			'align' => 'center',
			'width' => '10%'
		));
		$table->define_field(array(
			'name' => 'order',
			'caption' => t('J&auml;rjekord'),
			'align' => 'center',
			'width' => '10%'
		));
		$table->define_field(array(
			'name' => 'name',
			'caption' => t('Nimi'),
			'align' => 'center'
		));
		$table->define_field(array(
			'name' => 'delete',
			'caption' => t('Kustuta'),
			'width' => '10%',
			'align' => 'center'
		));

		$saved_containers = $arr['obj_inst']->meta('containers');
		foreach (safe_array($saved_containers) as $container_key => $container)
		{
			$table->define_data(array(
				'id' => $container_key." ",
				'order' => html::textbox(array(
					'name' => 'container_order['.$container_key.']',
					'value' => $container['order'],
					'size' => 5
				)),
				'name' => html::href(array(
					'url' => $this->mk_my_orb('change', array(
						'id' => $arr['obj_inst']->id(),
						'group' => 'containers',
						'container_id' => $container_key
					)),
					'caption' => $container['name']
				)),
				'delete' => html::checkbox(array(
					'name' => 'delete_container['.$container_key.']',
					'value' => 1
				)),
			));

		}
	}

	function _set_containers_list($arr)
	{
		if (!empty($arr['request']['container_id']))
		{
			return PROP_OK;
		}
		$containers = $arr['obj_inst']->meta('containers');

		$delete_containers = safe_array($arr['request']['delete_container']);
		$containers_order = safe_array($arr['request']['container_order']);
		foreach ( safe_array($containers) as $id => $container)
		{
			if (array_key_exists($id, $delete_containers))
			{
				unset($containers[$id]);
			}
			else
			{
				$containers[$id]['order'] = $containers_order[$id];
			}

		}
		$arr['obj_inst']->set_meta('containers', $containers);
		return PROP_OK;
	}

	function _get_container_info($arr)
	{
		if (!isset($arr['request']['container_id']))
		{
			return PROP_IGNORE;
		}
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->define_field(array(
			'name' => 'name',
			'caption' => t('Nimi/J&auml;rjekord')
		));
		$t->define_field(array(
			'name' => 'categories',
			'caption' => t('Kategooriad')
		));
		$t->define_field(array(
			'name' => 'all_categories',
			'caption' => t('N&auml;ita k&otilde;igis kategooriates'),
			'align' => 'center'
		));

		$container_id = (int)$arr['request']['container_id'];
		if (empty($container_id)) {}

		$saved_containers = $arr['obj_inst']->meta('containers');
		$name = $saved_containers[$container_id]['name'];
		$order = $saved_containers[$container_id]['order'];
		$categories = $saved_containers[$container_id]['categories'];
		$all_categories = $saved_containers[$container_id]['all_categories'];

		$t->define_data(array(
			'name' => t('Nimi: ').html::textbox(array(
				'name' => 'container[name]',
				'value' => $name,
				'size' => 20
			)).
			'<br />'
			.t('J&auml;rjekord: ').html::textbox(array(
				'name' => 'container[order]',
				'value' => $order,
				'size' => 20
			)).
			html::hidden(array(
				'name' => 'container[id]',
				'value' => $container_id
			)),
			'categories' => html::textarea(array(
				'name' => 'container[categories]',
				'value' => implode(',', $categories)
			)),
			'all_categories' => html::checkbox(array(
				'name' => 'container[all_categories]',
				'value' => 1,
				'checked' => ($all_categories) ? true : false
			))
		));

		return PROP_OK;
	}

	function _set_container_info($arr)
	{
		if ( !isset($arr['request']['container']) )
		{
			return PROP_OK;
		}

		$saved_containers = $arr['obj_inst']->meta('containers');
		$containers_lut = $arr['obj_inst']->meta('containers_lut');
		$data = $arr['request']['container'];

		$container = $saved_containers[$data['id']];
		$container['name'] = $data['name'];
		$container['categories'] = explode(',', $data['categories']);
		$container['all_categories'] = $data['all_categories'];
		$container['order'] = $data['order'];

		// clean up the container data from the lut
		foreach ( $containers_lut['by_cat'] as $key => $value )
		{
			if ( array_key_exists($data['id'], $value) || !empty($container['all_categories']) )
			{
				unset($containers_lut['by_cat'][$key][$data['id']]);
				if ( empty($containers_lut['by_cat'][$key]) )
				{
					unset( $containers_lut['by_cat'][$key] );
				}
			}
		}
		unset($containers_lut['all_cat'][$data['id']]);

		// put back the container into the lut where needed
		if ( empty($container['all_categories']) )
		{
			foreach ( $container['categories'] as $cat )
			{
				$containers_lut['by_cat'][$cat][$data['id']] = $data['id'];
			}
		}
		else
		{
			$containers_lut['all_cat'][$data['id']] = $data['id'];
		}

		$valid_rows = array();
		foreach (safe_array($data['rows']) as $row)
		{
			if ( !isset($row['delete']) )
			{
				foreach ($row as $row_value)
				{
					if ( !empty($row_value) )
					{
						$valid_rows[] = $row;
						break;
					}
				}
			}
		}
		$container['rows'] = $valid_rows;

		$saved_containers[$data['id']] = $container;

		$arr['obj_inst']->set_meta('containers', $saved_containers);
		$arr['obj_inst']->set_meta('containers_lut', $containers_lut);
		return PROP_OK;
	}

	function _get_container_rows($arr)
	{
		$id = $arr['request']['container_id'];
		if (!isset($id))
		{
			return PROP_IGNORE;
		}

		$t  = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->define_field(array(
			'name' => 'img_url',
			'caption' => t('Pildi URL')
		));
		$t->define_field(array(
			'name' => 'link_text',
			'caption' => t('Lingi tekst')
		));
		$t->define_field(array(
			'name' => 'link_url',
			'caption' => t('Lingi URL')
		));
		$t->define_field(array(
			'name' => 'no_line_breaks',
			'caption' => t('Ilma reavahetusteta'),
			'align' => 'center'
		));
		$t->define_field(array(
			'name' => 'delete',
			'caption' => t('Kustuta'),
			'align' => 'center'
		));

		$saved_containers = $arr['obj_inst']->meta('containers');
		$container = $saved_containers[$id];

		$counter = 1;
		foreach (safe_array($container['rows']) as $row_key => $row_value)
		{
			$t->define_data(array(
				'img_url' => html::textbox(array(
					'name' => 'container[rows]['.$counter.'][img_url]',
					'value' => $row_value['img_url']
				)),
				'link_text' => html::textbox(array(
					'name' => 'container[rows]['.$counter.'][link_text]',
					'value' => $row_value['link_text']
				)),
				'link_url' => html::textbox(array(
					'name' => 'container[rows]['.$counter.'][link_url]',
					'value' => $row_value['link_url']
				)),
				'no_line_breaks' => html::checkbox(array(
					'name' => 'container[rows]['.$counter.'][no_line_breaks]',
					'value' => 1,
					'checked' => ($row_value['no_line_breaks']) ? true : false
				)),
				'delete' => html::checkbox(array(
					'name' => 'container[rows]['.$counter.'][delete]',
					'value' => 1,
				)),

			));
			$counter++;
		}

		$t->define_data(array(
			'img_url' => html::textbox(array(
				'name' => 'container[rows]['.$counter.'][img_url]',
			)),
			'link_text' => html::textbox(array(
				'name' => 'container[rows]['.$counter.'][link_text]',
			)),
			'link_url' => html::textbox(array(
				'name' => 'container[rows]['.$counter.'][link_url]',
			)),
			'no_line_breaks' => html::checkbox(array(
				'name' => 'container[rows]['.$counter.'][no_line_breaks]',
				'value' => 1
			)),
			'delete' => ''
		));

		return PROP_OK;
	}

	function _get_files_order($args)
	{
		$t = &$args['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->define_field(array(
			'name' => 'file',
			'caption' => t('Fail'),
			'chgbgcolor' => 'line_color'
		));
		$t->define_field(array(
			'name' => 'order',
			'caption' => t('J&auml;rjekord'),
			'chgbgcolor' => 'line_color'
		));

		$count = 0;
		$saved_data = $args['obj_inst']->meta('files_order');
		foreach (safe_array($saved_data) as $file => $order)
		{
			$t->define_data(array(
				'file' => html::textbox(array(
					'name' => 'files_order['.$count.'][file]',
					'value' => $file,
					'size' => '10'
				)),
				'order' => html::textbox(array(
					'name' => 'files_order['.$count.'][order]',
					'value' => $order
				)),
			));
			$count++;
		}

		for ($j = 0; $j < 30; $j++)
		{
			$t->define_data(array(
				'file' => html::textbox(array(
					'name' => 'files_order['.$count.'][file]',
					'size' => '10'
				)),
				'order' => html::textbox(array(
					'name' => 'files_order['.$count.'][order]'
				)),
				'line_color' => 'lightblue'
			));
			$count++;
		}
		return PROP_OK;
	}

	function _set_files_order($args)
	{
		$valid_data = array();
		foreach (safe_array($args['request']['files_order']) as $data)
		{
			if (!empty($data['file']))
			{
				$valid_data[$data['file']] = $data['order'];
			}
		}
		$args['obj_inst']->set_meta('files_order', $valid_data);
		// i think that to avoid the scannig for orders from otto_prod_img table
		// i should keep them in meta too ... maybe it isn't necessary, anyway, this is
		// the place where i should update otto_prod_img table and set the order

		foreach ($valid_data as $file => $order)
		{

			if (strlen($file) > 4)
			{
				// if file name is longer than 4 characters, then it is probably full file name, so lets make it shorter:
				list(, $cur_pg) = explode(".", $file);
				$cur_pg = substr($cur_pg,1);
				if ((string)((int)$cur_pg{0}) === (string)$cur_pg{0})
				{
					$cur_pg = (int)$cur_pg;
				}
				$cur_pg = trim($cur_pg);
			}
			else if (strlen($file) < 4)
			{
				// if the file name is shorter than 4 characters, then it is probably a part of the needed file code/page code
				$cur_pg = trim($file).'%';
			}
			else
			{
				// in other cases it is probably the exact needed file code/page code
				$cur_pg = $file;
			}


			// get all products which are imported from this file:
			$prods = new object_list(array(
				'class_id' => CL_SHOP_PRODUCT,
				'status' => array(STAT_ACTIVE, STAT_NOTACTIVE),
				'user18' => $cur_pg
			));
			foreach ($prods->arr() as $prod)
			{
				if ($prod->ord() != $order)
				{
					$prod->set_ord($order);
					$prod->save();
				}
			}

		// i don't recall that it is in use anywhere, so I comment it out at first and let's see what happens --dragut (14.11.2007)
		//	$this->db_query("UPDATE otto_prod_img set file_order='".(int)$order."' WHERE p_pg='$cur_pg'");

		}
		return PROP_OK;
	}

	function _get_file_suffix($args)
	{
		$t = &$args['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->define_field(array(
			'name' => 'file',
			'caption' => t('Faili t&auml;ht'),
			'chgbgcolor' => 'line_color'
		));
		$t->define_field(array(
			'name' => 'suffix',
			'caption' => t('Suffiks'),
			'chgbgcolor' => 'line_color'
		));

		$count = 0;
		$saved_data = $args['obj_inst']->meta('file_suffix');

		foreach (safe_array($saved_data) as $file => $suffix)
		{
			$t->define_data(array(
				'file' => html::textbox(array(
					'name' => 'file_suffix['.$count.'][file]',
					'value' => $file,
					'size' => '10'
				)),
				'suffix' => html::textbox(array(
					'name' => 'file_suffix['.$count.'][suffix]',
					'value' => $suffix
				)),
			));
			$count++;
		}

		for ($i = 0; $i < 20; $i++)
		{
			$t->define_data(array(
				'file' => html::textbox(array(
					'name' => 'file_suffix['.$count.'][file]',
					'size' => '10'
				)),
				'suffix' => html::textbox(array(
					'name' => 'file_suffix['.$count.'][suffix]'
				)),
				'line_color' => 'lightblue'
			));
			$count++;
		}
		return PROP_OK;
	}

	function _set_file_suffix($args)
	{
		$valid_data = array();
		foreach (safe_array($args['request']['file_suffix']) as $data)
		{
			if (!empty($data['file']) && !empty($data['suffix']))
			{
				$valid_data[$data['file']] = $data['suffix'];
			}
		}
		$args['obj_inst']->set_meta('file_suffix', $valid_data);
		return PROP_OK;
	}


	function _get_categories($args)
	{
		$t = &$args['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->define_field(array(
			'name' => 'jrk',
			'caption' => t('Jrk'),
			'align' => 'center',
			'chgbgcolor' => 'line_color'
		));
		$t->define_field(array(
			'name' => 'aw_folder_id',
			'caption' => t('AW Kataloogi ID'),
			'align' => 'center',
			'chgbgcolor' => 'line_color'
		));
		$t->define_field(array(
			'name' => 'categories',
			'caption' => t('Kategooriad'),
			'chgbgcolor' => 'line_color'
		));
		$t->define_field(array(
			'name' => 'filter_button',
			'caption' => t('Filtreeri'),
			'chgbgcolor' => 'line_color'
		));

		$count = 1;
		$data = array();
		$aw_folder_ids = array();
		if (!empty($args['request']['filter_aw_folder_id']))
		{
			$aw_folder_ids[] = $args['request']['filter_aw_folder_id'];
		}
		if (!empty($args['request']['filter_category']))
		{
			$this->db_query("
				select
					aw_folder
				from
					otto_imp_t_aw_to_cat
				where
					lang_id = ".aw_global_get('lang_id')." and
					category like '%".$args['request']['filter_category']."%'
			");
			while ($row = $this->db_next())
			{
				$aw_folder_ids[] = $row['aw_folder'];
			}
		}

		if (!empty($aw_folder_ids))
		{
			$sql_params = " and aw_folder in (".implode(',', $aw_folder_ids).")";
		}

		$this->db_query("SELECT * FROM otto_imp_t_aw_to_cat WHERE lang_id=".aw_global_get('lang_id'). $sql_params);
		while ($row = $this->db_next())
		{
			if (!in_array($row['category'], $data[$row['aw_folder']]))
			{
				$data[$row['aw_folder']][] = $row['category'];
			}
		}

		$t->define_data(array(
			'jrk' => t('Filter'),
			'aw_folder_id' => html::textbox(array(
				'name' => 'data_filter[aw_folder_id]',
				'value' => (!empty($args['request']['filter_aw_folder_id'])) ? $args['request']['filter_aw_folder_id'] : '',
				'size' => '10'
			)),
			'categories' => html::textbox(array(
				'name' => 'data_filter[category]',
				'value' => (!empty($args['request']['filter_category'])) ? $args['request']['filter_category'] : '',
				'size' => '80',
			)),
			'filter_button' => html::submit(array(
				'name' => 'filter_categories',
				'value' => t('Filtreeri')
			)),
			'line_color' => 'green'

		));

		foreach ($data as $aw_folder => $categories)
		{

			$t->define_data(array(
				'jrk' => $count,
				'aw_folder_id' => html::textbox(array(
					'name' => 'data['.$aw_folder.'][aw_folder_id]',
					'value' => $aw_folder,
					'size' => '10'
				)),
				'categories' => html::textbox(array(
					'name' => 'data['.$aw_folder.'][categories]',
					'value' => implode(',', $categories),
					'size' => '80',
				)),
				'line_color' => ($this->can('view', $aw_folder)) ? '' : '#FFFF8F'
			));
			$count++;

		}

		for ($i = 0; $i<10; $i++)
		{
			$t->define_data(array(
				'aw_folder_id' => html::textbox(array(
					'name' => 'new_data['.$count.'][aw_folder_id]',
					'value' => '',
					'size' => '10'
				)),
				'categories' => html::textbox(array(
					'name' => 'new_data['.$count.'][categories]',
					'value' => '',
					'size' => '80'
				)),
				'line_color' => 'lightblue'
			));
			$count++;
		}

		return PROP_OK;
	}

	function _set_categories($args)
	{
		if (!array_key_exists('filter_categories', $args['request']))
		{

			$aw_folder_ids = array_keys($args['request']['data']);

			if (!empty($aw_folder_ids))
			{
				$categories_by_section = array();
				$this->db_query('select * from otto_imp_t_aw_to_cat where lang_id = '.aw_global_get('lang_id').' and aw_folder in ('.implode(',', $aw_folder_ids).')');
				while ($row = $this->db_next())
				{
					$categories_by_section[$row['aw_folder']][] = $row['category'];
				}

				$this->db_query('delete from otto_imp_t_aw_to_cat where lang_id = '.aw_global_get('lang_id').' and aw_folder in ('.implode(',', $aw_folder_ids).')');
				foreach ($args['request']['data'] as $data)
				{
					$categories = explode(',', $data['categories']);
					$old_categories = $categories_by_section[$data['aw_folder_id']];
					// nyyd oleks vaja selline trikk teha, et kui kategooriate paigutus on muutunud, siis peaks
					// ka vastava kategooria tooted uute sektsioonide alla m22rama, v6i siis 2ra v6tta

					$added_categories = array_diff($categories, $old_categories);
					$deleted_categories = array_diff($old_categories, $categories);

					$tmp_added_categories = array();
					foreach ($added_categories as $key => $value)
					{
						if (!empty($value))
						{
							$tmp_added_categories[] = $value;
						}
					}
					$added_categories = $tmp_added_categories;

					if (!empty($added_categories))
					{
						// mul on vaja k6iki nende kategooriatega tooteid

						$prod_ol = new object_list(array(
							'class_id' => CL_SHOP_PRODUCT,
							'user11' => $added_categories
						));
						$prod_ol_ids = $prod_ol->ids();
						if (!empty($prod_ol_ids))
						{
							// v6tame need tooted selle sektsiooni alt mis praegust aktiivne on
							$this->db_query("
								select
									*
								from
									otto_prod_to_section_lut
								where
									section = ".$data['aw_folder_id']." and
									product in (".implode(',', $prod_ol_ids).") and
									lang_id = ".aw_global_get('lang_id')."
								");
							$tmp_prods = array();
							while ($row = $this->db_next())
							{
								$tmp_prods[$row['product']] = $row['section'];
							}
						}

						foreach ($prod_ol_ids as $prod_id)
						{
							if (!isset($tmp_prods[$prod_id]))
							{
								echo ">>> ".$prod_id." lisatakse sektsiooni ".$data['aw_folder_id']." alla <br /> \n";
								$this->db_query("
									insert into
										otto_prod_to_section_lut
									set
										product = ".$prod_id.",
										section = ".$data['aw_folder_id'].",
										lang_id = ".aw_global_get('lang_id')."
								");
							}
							else
							{
								echo "### ".$prod_id." n2idatakse juba sektsiooni ".$data['aw_folder_id']." all (ei tee midagi) <br />\n";
							}
						}

					}

					$tmp_deleted_categories = array();
					foreach ($deleted_categories as $key => $value)
					{
						if (!empty($value))
						{
							$tmp_deleted_categories[] = $value;
						}
					}
					$deleted_categories = $tmp_deleted_categories;

					if (!empty($deleted_categories))
					{
						echo "deleted categories <br /> \n";
						arr($deleted_categories);
						$prod_ol = new object_list(array(
							'class_id' => CL_SHOP_PRODUCT,
							'user11' => $deleted_categories
						));

						$prod_ol_ids = $prod_ol->ids();
						if (!empty($prod_ol_ids))
						{
							$this->db_query("
								select
									aw_oid, user11
								from
									aw_shop_products
								where
									aw_oid in (".implode(',', $prod_ol_ids).")
							");
							$prod_cats = array();
							while ($row = $this->db_next())
							{
								$prod_cats[$row['aw_oid']] = explode(',', $row['user11']);
							}

							foreach ($prod_ol_ids as $prod_id)
							{
								$tmp_arr_intersect = array_intersect($prod_cats[$prod_id], $categories);
								arr($tmp_arr_intersect);
								if (empty($tmp_arr_intersect))
								{
									echo "--- remove ".$prod_id." from section ".$data['aw_folder_id']." <br /> \n";
									$this->db_query("
										delete from
											otto_prod_to_section_lut
										where
											product = ".$prod_id." and
											section = ".$data['aw_folder_id']." and
											lang_id = ".aw_global_get('lang_id')."
									");
								}
							}
						}
					}
					$categories = array_unique($categories);
					foreach ($categories as $category)
					{
						if (!empty($category) && !empty($data['aw_folder_id']))
						{
							$this->db_query("INSERT INTO otto_imp_t_aw_to_cat set
								category = '$category',
								aw_folder = ".$data['aw_folder_id'].",
								lang_id = ".aw_global_get('lang_id')."
							");
						}
					}
				}
			}

			foreach ($args['request']['new_data'] as $data)
			{
				if (!empty($data['aw_folder_id']))
				{
					echo "--- Adding new section data: <br />\n";
					$categories = explode(',', $data['categories']);
					$categories = array_unique($categories);

					$prod_ol = new object_list(array(
						'class_id' => CL_SHOP_PRODUCT,
						'user11' => $categories
					));
					$prod_ol_ids = $prod_ol->ids();

					foreach ($prod_ol_ids as $prod_id)
					{
						echo ">>> ".$prod_id." lisatakse sektsiooni ".$data['aw_folder_id']." alla <br /> \n";
						$this->db_query("
							insert into
								otto_prod_to_section_lut
							set
								product = ".$prod_id.",
								section = ".$data['aw_folder_id'].",
								lang_id = ".aw_global_get('lang_id')."
						");
					}

					foreach ($categories as $category)
					{
						if (!empty($category) && !empty($data['aw_folder_id']))
						{
							$this->db_query("INSERT INTO otto_imp_t_aw_to_cat set
								category = '$category',
								aw_folder = ".$data['aw_folder_id'].",
								lang_id = ".aw_global_get('lang_id')."
							");
						}
					}
				}
			}
		}

		return PROP_OK;
	}

	function _get_bubble_pictures_toolbar($arr)
	{
		$t = &$arr['prop']['vcl_inst'];

		$t->add_button(array(
			'name' => 'delete',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta'),
			'action' => '_delete_bubble_picture',
			'confirm' => t('Oled kindel et soovid valitud read kustutada?')
		));

		return PROP_OK;

	}

	function _get_bubble_pictures($args)
	{
		$t = &$args['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->define_field(array(
			'name' => 'select',
			'caption' => t('Vali'),
			'width' => '5%',
			'chgbgcolor' => 'line_color',
			'align' => 'center'
		));
		$t->define_field(array(
			'name' => 'category',
			'caption' => t('Kategooria'),
			'chgbgcolor' => 'line_color'
		));
		$t->define_field(array(
			'name' => 'title',
			'caption' => t('Pealkiri'),
			'chgbgcolor' => 'line_color'
		));
		$t->define_field(array(
			'name' => 'image_url',
			'caption' => t('Pildi aadress'),
			'chgbgcolor' => 'line_color'
		));
		$t->define_field(array(
			'name' => 'filter_button',
			'caption' => t('Filtreeri'),
			'chgbgcolor' => 'line_color'
		));

		$saved_data = $args['obj_inst']->meta('bubble_pictures');

		$filter_bubble_category = $args['request']['bubble_filter']['category'];
		$filter_bubble_image_url = $args['request']['bubble_filter']['image_url'];
		$filter_bubble_title = $args['request']['bubble_filter']['title'];

		$t->define_data(array(
			'category' => html::textbox(array(
				'name' => 'bubble_filter[category]',
				'value' => $filter_bubble_category
			)),
			'title' => html::textbox(array(
				'name' => 'bubble_filter[title]',
				'value' => $filter_bubble_title
			)),
			'image_url' => html::textbox(array(
				'name' => 'bubble_filter[image_url]',
				'value' => $filter_bubble_image_url
			)),
			'filter_button' => html::submit(array(
				'name' => 'filter_bubble_images',
				'value' => t('Filtreeri')
			)),
			'line_color' => 'green'
		));

		foreach ( $saved_data as $category => $data )
		{
			$add_line = false;
			if (!empty($filter_bubble_category) || !empty($filter_bubble_image_url) || !empty($filter_bubble_title))
			{
				if (strpos($category, $filter_bubble_category) !== false)
				{
					$add_line = true;
				}
				if (strpos($data['image_url'], $filter_bubble_image_url) !== false)
				{
					$add_line = true;
				}
				if (strpos($data['title'], $filter_bubble_title) !== false)
				{
					$add_line = true;
				}

			}
			else
			{
				$add_line = true;
			}

			if ($add_line)
			{

				$t->define_data(array(
					'select' => html::checkbox(array(
						'name' => 'bubble_select['.$category.']',
						'value' => $category
					)),
					'category' => html::textbox(array(
						'name' => 'bubble_data['.$category.'][category]',
						'value' => $category
					)),
					'title' => html::textbox(array(
						'name' => 'bubble_data['.$category.'][title]',
						'value' => $data['title']
					)),
					'image_url' => html::textbox(array(
						'name' => 'bubble_data['.$category.'][image_url]',
						'value' => $data['image_url']
					)),
				));

			}
		}

		for ($i = 0; $i < 5; $i++ )
		{
			$t->define_data(array(
				'category' => html::textbox(array(
					'name' => 'new_bubble_data['.$i.'][category]'
				)),
				'title' => html::textbox(array(
					'name' => 'new_bubble_data['.$i.'][title]',
				)),
				'image_url' => html::textbox(array(
					'name' => 'new_bubble_data['.$i.'][image_url]'
				)),
				'line_color' => 'lightblue'
			));
		}
		return PROP_OK;
	}

	function _set_bubble_pictures($args)
	{
		if (!array_key_exists('filter_bubble_images', $args['request']))
		{
			$valid_data = $args['obj_inst']->meta('bubble_pictures');
			foreach (safe_array($args['request']['bubble_data']) as $category => $data)
			{
				if (!empty($data['category']))
				{
					if ($category != $data['category'])
					{
						unset($valid_data[$category]);
						$category = $data['category'];
					}

					$valid_data[$category] = array(
						'title' => $data['title'],
						'image_url' => $data['image_url']
					);
				}
			}
			foreach (safe_array($args['request']['new_bubble_data']) as $key => $data)
			{
				if (!empty($data['category']))
				{
					$valid_data[$data['category']] = array(
						'title' => $data['title'],
						'image_url' => $data['image_url']
					);
				}
			}

			$args['obj_inst']->set_meta('bubble_pictures', $valid_data);
		}
		return PROP_OK;

	}

        /**
                @attrib name=_delete_bubble_picture
        **/
	function _delete_bubble_picture($arr)
	{
		if ($this->can('view', $arr['id']))
		{
			$o = new object($arr['id']);
			$data = $o->meta('bubble_pictures');
			foreach ($arr['bubble_select'] as $cat)
			{
				unset($data[$cat]);
			}
			$o->set_meta('bubble_pictures', $data);
			$o->save();
		}

		return $this->mk_my_orb("change", array(
			'id' => $arr['id'],
			'group' => $arr['group']
		), CL_OTTO_IMPORT);
	}

	function _get_firm_pictures_toolbar($arr)
	{
		$t = &$arr['prop']['vcl_inst'];

		$t->add_button(array(
			'name' => 'delete',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta'),
			'action' => '_delete_firm_picture',
			'confirm' => t('Oled kindel et soovid valitud read kustutada?')
		));

		return PROP_OK;

	}

	function _get_firm_pictures($args)
	{
		$t = &$args['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->define_field(array(
			'name' => 'select',
			'caption' => t('Vali'),
			'width' => '5%',
			'chgbgcolor' => 'line_color',
			'align' => 'center'
		));
		$t->define_field(array(
			'name' => 'category',
			'caption' => t('Kategooria'),
			'chgbgcolor' => 'line_color'
		));
		$t->define_field(array(
			'name' => 'title',
			'caption' => t('Pealkiri'),
			'chgbgcolor' => 'line_color'
		));
		$t->define_field(array(
			'name' => 'image_url',
			'caption' => t('Pildi aadress'),
			'chgbgcolor' => 'line_color'
		));
		$t->define_field(array(
			'name' => 'filter_button',
			'caption' => t('Filtreeri'),
			'chgbgcolor' => 'line_color'
		));

		$saved_data = $args['obj_inst']->meta('firm_pictures');

		$filter_firm_category = $args['request']['firm_filter']['category'];
		$filter_firm_image_url = $args['request']['firm_filter']['image_url'];
		$filter_firm_title = $args['request']['firm_filter']['title'];

		$t->define_data(array(
			'category' => html::textbox(array(
				'name' => 'firm_filter[category]',
				'value' => $filter_firm_category
			)),
			'title' => html::textbox(array(
				'name' => 'firm_filter[title]',
				'value' => $filter_firm_title
			)),
			'image_url' => html::textbox(array(
				'name' => 'firm_filter[image_url]',
				'value' => $filter_firm_image_url
			)),
			'filter_button' => html::submit(array(
				'name' => 'filter_firm_images',
				'value' => t('Filtreeri')
			)),
			'line_color' => 'green'
		));

		foreach ($saved_data as $category => $data)
		{
			$add_line = false;
			if (!empty($filter_firm_category) || !empty($filter_firm_image_url) || !empty($filter_firm_title))
			{
				if (strpos($category, $filter_firm_category) !== false)
				{
					$add_line = true;
				}
				if (strpos($data['image_url'], $filter_firm_image_url) !== false)
				{
					$add_line = true;
				}
				if (strpos($data['title'], $filter_firm_title) !== false)
				{
					$add_line = true;
				}

			}
			else
			{
				$add_line = true;
			}
			if ($add_line)
			{
				$t->define_data(array(
					'select' => html::checkbox(array(
						'name' => 'firm_select['.$category.']',
						'value' => $category
					)),
					'category' => html::textbox(array(
						'name' => 'firm_data['.$category.'][category]',
						'value' => $category
					)),
					'title' => html::textbox(array(
						'name' => 'firm_data['.$category.'][title]',
						'value' => $data['title']
					)),
					'image_url' => html::textbox(array(
						'name' => 'firm_data['.$category.'][image_url]',
						'value' => $data['image_url']
					)),
				));
			}
		}

		for ($i = 0; $i < 5; $i++ )
		{
			$t->define_data(array(
				'category' => html::textbox(array(
					'name' => 'new_firm_data['.$i.'][category]'
				)),
				'title' => html::textbox(array(
					'name' => 'new_firm_data['.$i.'][title]'
				)),
				'image_url' => html::textbox(array(
					'name' => 'new_firm_data['.$i.'][image_url]'
				)),
				'line_color' => 'lightblue'
			));
		}
		return PROP_OK;
	}

	function _set_firm_pictures($args)
	{
		if (!array_key_exists('filter_firm_images', $args['request']))
		{
			$valid_data = $args['obj_inst']->meta('firm_pictures');
			foreach (safe_array($args['request']['firm_data']) as $category => $data)
			{
				if (!empty($data['category']))
				{
					if ($category != $data['category'])
					{
						unset($valid_data[$category]);
						$category = $data['category'];
					}

					$valid_data[$category] = array(
						'title' => $data['title'],
						'image_url' => $data['image_url']
					);
				}
			}
			foreach (safe_array($args['request']['new_firm_data']) as $key => $data)
			{
				if (!empty($data['category']))
				{
					$valid_data[$data['category']] = array(
						'title' => $data['title'],
						'image_url' => $data['image_url']
					);
				}
			}
			$args['obj_inst']->set_meta('firm_pictures', $valid_data);
		}
		return PROP_OK;
	}

        /**
                @attrib name=_delete_firm_picture
        **/
	function _delete_firm_picture($arr)
	{
		if ($this->can('view', $arr['id']))
		{
			$o = new object($arr['id']);
			$data = $o->meta('firm_pictures');
			foreach ($arr['firm_select'] as $cat)
			{
				unset($data[$cat]);
			}
			$o->set_meta('firm_pictures', $data);
			$o->save();
		}

		return $this->mk_my_orb("change", array(
			'id' => $arr['id'],
			'group' => $arr['group']
		), CL_OTTO_IMPORT);
	}

	function get_product_codes($o)
	{
		$data = array();
		if (!is_object($o))
		{
			return array();
		}

		foreach(explode("\n", $o->prop("fnames")) as $fname)
		{
			if (trim($fname) == "" )
			{
				continue;
			}

			$fld_url = $o->prop("base_url")."/".trim($fname)."-2.xls";
			if (!$this->is_csv($fld_url))
			{
				continue;
			}
			echo "from url ".$fld_url." read: <br><br />\n";
			list(, $cur_pg) = explode(".", $fname);
			$cur_pg = substr($cur_pg,1);
			if ((string)((int)$cur_pg{0}) === (string)$cur_pg{0})
			{
				$cur_pg = (int)$cur_pg;
			}
			$cur_pg = trim($cur_pg);

			$first = true;
			$num =0;

			// fucking mackintosh
			if (count(file($fld_url)) == 1)
			{
				$lines = $this->mk_file($fld_url, "\t");
				if (count($lines) > 1)
				{
					$tmpf = tempnam("/tmp", "aw-ott-imp-5");
					$fp = fopen($tmpf,"w");
					fwrite($fp, join("\n", $lines));
					fclose($fp);
					$fld_url = $tmpf;
				}
			}

			$fp = fopen($fld_url, "r");
			while ($row = fgetcsv($fp,1000,"\t"))
			{
				if ($first)
				{
					$first = false;
					continue;
				}
				$row = $this->char_replacement($row);
				$row[4] = str_replace(".","", $row[4]);
				$row[4] = substr(str_replace(" ","", $row[4]), 0, 8);
				$data[] = $row[4];
			}
		}
		return $data;
	}

	/**

		@attrib name=pictimp

		@comment Otto pictures import

	**/
	function pictimp($arr,$fix_missing = false)
	{
		$GLOBALS["no_cache_clear"] = 1;
		$this->added_images = array();
		aw_set_exec_time(AW_LONG_PROCESS);
		$import_obj = $arr;

		echo "-----------[ start of picture import ]------------------<br>";
		flush();

		$data = $this->get_product_codes($arr);
		if ($fix_missing)
		{
			$this->db_query("select c.code from otto_imp_t_codes c left join otto_prod_img p on p.pcode = c.code where (p.imnr is null or p.imnr = '')");
			$data = array();
			while ($row = $this->db_next())
			{
				$data[] = $row["code"];
			}
			$skip_to = "";
			echo "fixing not found codes:".join(", ",$data)." <br><br>";
		}

		$total = count($data);
		$cur_cnt = -1;
		$start_time = time();

		foreach ($data as $pcode)
		{

			$pcode = str_replace(" ", "", $pcode);

			if ($pcode == "")
			{
				continue;
			}
			echo "\n<br /><strong>[ process pcode $pcode ]</strong><br>\n";
			flush();

			// BONPRIX:
			if (aw_ini_get("site_id") == 276 || aw_ini_get("site_id") == 277)
			{
				$this->bonprix_picture_import(array(
					'pcode' => $pcode,
					'import_obj' => $import_obj,
					'start_time' => $start_time
				));
			}
			else
			{
				$this->otto_picture_import(array(
					'pcode' => $pcode,
					'import_obj' => $import_obj,
					'start_time' => $start_time
				));
			}

			$stat = fopen(aw_ini_get("site_basedir")."files/status.txt","w");

			fwrite($stat, $pcode);
			fclose($stat);

			$cur_cnt++;
			$time_cur_cnt++;
		}

		echo "-----------[ end of picture import function ]------------------<br>";
	}

	function bonprix_picture_import($arr)
	{
		$pcode = $arr['pcode'];
		$params = array(
			'import_obj' => $arr['import_obj'],
			'pcode' => $arr['pcode'],
			'start_time' => $arr['start_time']
		);
		// so, here should i check which will be the first site to check for pictures
		$first_site = $arr['import_obj']->prop("first_site_to_search_images");
		switch ($first_site)
		{
			case "bp_de":
				// if set so, search images from German Bonprix first
				if ($this->bonprix_picture_import_de($params) === false)
				{
					if ($this->bonprix_picture_import_pl($params) === false)
					{
						$this->not_found_products[$params['pcode']] = $params['pcode'];
						echo "Toodet ei leitud! <br>";
					}
				}
				break;
			default:
				// by default we search images from Polish Bonprix first
				if ($this->bonprix_picture_import_pl($params) === false)
				{
					if ($this->bonprix_picture_import_de($params) === false)
					{
						$this->not_found_products[$params['pcode']] = $params['pcode'];
						echo "Toodet ei leitud!<br>";
					}
				}
		}

	}

	////
	// Picture import from Polish Bonprix (www.bonprix.pl)
	// Parameters:
	// 	pcode - product code which will be searched
	// return:
	// 	(boolean) true if product is found
	// 	(boolean) false if not found
	function bonprix_picture_import_pl($arr)
	{
		$pcode = $arr['pcode'];
		$start_time = $arr['start_time'];
		$import_obj = $arr['import_obj'];

		/*
			Poola bp saidist ei ole vaja pilte enam otsima minna, vaid need on juba olemas
			ja seosed piltide ja toodete vahel on defineeritud seosetabelis mille saab ftp-st.
		*/
		$f = file('/www/bp.ee.struktuur.ee/public/vv_bp_pl_img/linkage.txt');
		$f = array_unique($f);
		foreach ($f as $line)
		{
			$items = explode(';', $line);
			if ($items[0] == $pcode)
			{
				echo $pcode ." - ". $line."<br>\n";
				$mask = $items[2];
				$filename = basename($items[1], '.jpg');
				for ( $i = 0; $i < strlen($mask); $i++ )
				{
					if ($mask{$i} == 1)
					{
						$image_ok = $this->get_image(array(
							'source' => 'http://www.bonprix.ee/vv_bp_pl_img/'.$i.'/'.$filename.'_160.jpg',
							'format' => 2,
							'otto_import' => $import_obj,
							'filename' => $filename.'_var'.$i,
							'debug' => true
						));
						if ($image_ok)
						{
							// download the big version of the image too:
							$this->get_image(array(
								'source' => 'http://www.bonprix.ee/vv_bp_pl_img/'.$i.'/'.$filename.'_600.jpg',
								'format' => 1,
								'otto_import' => $import_obj,
								'filename' => $filename.'_var'.$i,
								'debug' => true
							));
						}
						$imnr = $this->db_fetch_field("SELECT pcode FROM otto_prod_img WHERE imnr = '".$ilename."_var".$i."' AND nr = '$i' AND pcode = '$pcode'", "pcode");
						echo "---- Otsin baasist pilti [".$filename."_var".$i."] numbriga [$i] ja tootekoodiga [$pcode] <br>";
						if (!$imnr)
						{
							echo "------ image not found, insert new image $im <br>\n";
							flush();

							$q = ("
								INSERT INTO
									otto_prod_img(pcode, nr,imnr, server_id, mod_time)
									values('$pcode','$i','".$filename."_var".$i."', 7, $start_time)
								");
								//echo "q = $q <br>";
								$this->db_query($q);
								$this->added_images[] = $filename."_var".$i;
						}
						else
						{
							echo "------ found image, update mod_time to $start_time (".date("d.m.Y H:m:s", $start_time).")<br>\n";
							$this->db_query("UPDATE otto_prod_img SET mod_time=$start_time WHERE imnr = '$im' AND nr = '$num' AND pcode = '$pcode'");
						}
					}
				}

			}
		}
		return true;

	}
	////
	// Picture import from German Bonprix (www.bonprix.de)
	// Parameters:
	// 	pcode - product code which will be searched
	// Return:
	//	(boolean) true - product is found
	//	(boolean) false - product is not found
	function bonprix_picture_import_de($arr)
	{
		$pcode = $arr['pcode'];
		$start_time = $arr['start_time'];
		$import_obj = $arr['import_obj'];
		$url = "http://www.bonprix-shop.de/bp/search.htm?id=188035177146052928-0&nv=0%7C0%7C1&sc=0&pAnfrage=".$pcode;
		$html = $this->file_get_contents($url);

		if (strpos($html, "Leider konnten wir") === false)
		{
			echo "[ BONPRIX SAKSA ]<br>";
			echo "-- Leidsin toote <strong>[ $pcode ]</strong> [<a href=\"$url\">url</a>]<br />";

			$patterns = array(
				"/http:\/\/image01\.otto\.de\/bonprixbilder\/shopposiklein\/7er\/gross\/var(\d+)\/(.*).jpg/imsU",
				"/\/\/image01\.otto\.de\/bonprixbilder\/shopposiklein\/7er\/gross\/var(\d+)\/(.*).jpg/imsU",
				"/\/\/image01\.otto\.de\/bonprixbilder\/varianten\/artikel_ansicht\/var(\d+)\/(.*).jpg/imsU",
			);

			// lets make the search:
			foreach ($patterns as $pattern)
			{
				if (preg_match($pattern, $html, $mt))
				{
					$first_im = $mt[2]."_var".$mt[1];
					$first_im_name = $mt[2];
					$first_im_var = $mt[1];
					break;
				}
			}
			echo "---- Kontrollin baasist pilti [ $first_im ] <br>\n";
			flush();
				$image_ok = $this->get_image(array(
					'source' => 'http://image01.otto.de/bonprixbilder/shopposiklein/7er/gross/var'.$first_im_var.'/'.$first_im_name.'.jpg',
					'format' => 2,
					'otto_import' => $import_obj,
					'filename' => $first_im_name.'_var'.$first_im_var,
					'debug' => true
				));
				if ($image_ok)
				{
					// download the big version of the image too:
					$this->get_image(array(
						'source' => 'http://image01.otto.de/bonprixbilder/shopposiklein/7er/gross/var'.$first_im_var.'/'.$first_im_name.'.jpg',
						'format' => 1,
						'otto_import' => $import_obj,
						'filename' => $first_im_name.'_var'.$first_im_var,
						'debug' => true
					));
				}
			$imnr = $this->db_fetch_field("SELECT pcode FROM otto_prod_img WHERE imnr = '$first_im' AND nr = '1' AND pcode = '$pcode'", "pcode");
			echo "---- Sellele pildile vastab tootekood [ $imnr ]<br>\n";
			flush();
			if (!$imnr && $first_im)
			{
				echo "";
				echo "------ insert new first image [ $first_im ]<br>\n";
				flush();

				$nr = $first_im{strlen($first_im)-1};
				$q = ("
					INSERT INTO
						otto_prod_img(pcode, nr,imnr, server_id, mod_time)
						values('$pcode','$nr','$first_im', 6, $start_time)
				");
				//echo "q = $q <br>";
				$this->db_query($q);
				$this->added_images[] = $first_im;
			}
			else
			{
				echo "------ found first image, update mod_time $start_time (".date("d.m.Y H:m:s", $start_time).")<br>\n";
				$this->db_query("UPDATE otto_prod_img SET mod_time=$start_time WHERE imnr = '$first_im' AND nr = '1' AND pcode = '$pcode'");
			}

			// get other images
			list($r_i) = explode("_", $first_im);
			echo "---- Otsin teisi pilte: <br>";
			if (!preg_match_all("/http:\/\/image01\.otto\.de\/bonprixbilder\/shopposiklein\/7er\/klein\/(.*)\/".$r_i.".jpg/imsU", $html, $mt, PREG_PATTERN_ORDER))
			{
				preg_match_all("/\/\/image01\.otto\.de\/bonprixbilder\/shopposiklein\/7er\/klein\/(.*)\/".$r_i.".jpg/imsU", $html, $mt, PREG_PATTERN_ORDER);
			}
			$otherim = $mt[1];
			foreach($otherim as $nr)
			{
				$im = $r_i."_".$nr;
			//	$var = $nr;
				$nr = $nr{strlen($nr)-1};
				echo "---- Kontrollin baasist pilti [ $im ] <br>\n";
				flush();
				$image_ok = $this->get_image(array(
					'source' => 'http://image01.otto.de/bonprixbilder/shopposiklein/7er/gross/var'.$nr.'/'.$r_i.'.jpg',
					'format' => 2,
					'otto_import' => $import_obj,
					'filename' => $im,
					'debug' => true
				));
				if ($image_ok)
				{
					// download the big version of the image too:
					$this->get_image(array(
						'source' => 'http://image01.otto.de/bonprixbilder/shopposiklein/7er/gross/var'.$nr.'/'.$r_i.'.jpg',
						'format' => 1,
						'otto_import' => $import_obj,
						'filename' => $im,
						'debug' => true
					));
				}

				$imnr = $this->db_fetch_field("SELECT pcode FROM otto_prod_img WHERE imnr = '$im' AND nr = '$nr' AND pcode = '$pcode'", "pcode");
				echo "---- Sellele pildile vastab tootekood [ $imnr ]<br>\n";
				flush();
				if (!$imnr)
				{
					echo "------ insert new image [ $im ]<br>\n";
					flush();
					$q = ("
						INSERT INTO
							otto_prod_img(pcode, nr,imnr, server_id, mod_time)
							values('$pcode','$nr','$im', 6, $start_time)
					");
					//echo "q = $q <br>";
					$this->db_query($q);
					$this->added_images[] = $im;
				}
				else
				{
					echo "------ found image, update mod_time $start_time (".date("d.m.Y H:m:s", $start_time).")<br>\n";

					$this->db_query("UPDATE otto_prod_img SET mod_time=$start_time WHERE imnr = '$im' AND nr = '$nr' AND pcode = '$pcode'");
				}
			}
		}
		else
		{
			return false;
		}

		return true;

	}

	function do_prod_import($arr)
	{

		$o = $arr['otto_import'];

		$GLOBALS["no_cache_clear"] = 1;

		// unset the products list which was imported last time:
		unset($_SESSION['otto_import_product_data']);

		if ($arr['doing_pict_i'])
		{
			$this->pictimp($o);
		}

		$this->import_product_objects(array(
			'otto_import' => $o,
		//	'force_full_update' => $arr['force_full_update'],
			'update_product_images' => $arr['update_product_images'],
			'force_update_product_images' => $arr['force_update_product_images'],
			'update_product_categories' => $arr['update_product_categories'],
			'force_update_product_categories' => $arr['force_update_product_categories'],
			'update_connected_products' => $arr['update_connected_products'],
			'force_update_connected_products' => $arr['force_update_connected_products']
		));

		// flush cache
		$this->cache_files = array();
		$fld = aw_ini_get("site_basedir")."/prod_cache";
		$this->_get_cache_files($fld);
		foreach($this->cache_files as $file)
		{
			$fp = $fld."/".$file{0}."/".$file{1}."/".$file;
			unlink($fp);
		}
	}

	function _get_cache_files($fld)
	{
		if ($dir = @opendir($fld))
		{
			while (($file = readdir($dir)) !== false)
			{
				if (!($file == "." || $file == ".."))
				{
					if (is_dir($fld."/".$file))
					{
						$this->_get_cache_files($fld."/".$file);
					}
					else
					{
						$this->cache_files[] = $file;
					};
				};
			};
		}
	}

	// takes otto_import obj. instance as parameter
	function import_product_objects($arr)
	{
		$import_time = time();

		$o = $arr['otto_import'];

		aw_set_exec_time(AW_LONG_PROCESS);
		$otto_import_lang_id = $o->lang_id();
		$not_found_products_by_page = array();

		echo "START UPDATE CSV DB<br>";

		echo "<b>[!!]</b> start reading data from csv files <b>[!!]</b><br>\n";

		$this->import_data_from_csv($o); // reading data from csv file into temporary db tables

		echo "<br><b>[!!]</b>  end reading data from the csv files <b>[!!]</b><br><br>\n";


		// This should clean up the otto_prod_to_code_lut table from those product object ids which are deleted from the system:
		$this->clean_up_products_to_code_lut();

		// This should clean up the otto_prod_to_section_lut table from those product objects ids which are deleted from the system:
		$this->clean_up_products_to_section_lut();


		// the new structure is like this:
		// product - the row from the first file, contains name, desc, pictures info
		// 	packaging - for every row in the third file, contains info about price, size, and colors
		//			and product codes with comma separated list

		// all products (previosuly packages) - first file content
		$products = $this->db_fetch_array("select * from otto_imp_t_prod where lang_id = ".aw_global_get('lang_id'));

		foreach ($products as $product)
		{
			$product_data = array();

			echo "<b>product: ".$product['title']."</b><br>\n";

			// get all product codes and colors:
			$product_codes = array();
			$colors = array();
			$q = "select * from otto_imp_t_codes where pg='".$product['pg']."' and nr='".$product['nr']."' and lang_id = ".aw_global_get('lang_id');
			$product_codes_data = $this->db_fetch_array($q);
			foreach ($product_codes_data as $value)
			{
				$product_codes[$value['code']] = $value['code'];
				$colors[] = $value['color'];

				// add some information into otto_prod_img table (refactor: see tuleks liigutada piltide impordi juurde imo):
				$this->db_query("UPDATE otto_prod_img SET p_pg='".$product['pg']."', p_nr='".$product['nr']."' WHERE pcode='".$value['code']."'");
			}

			echo "product codes: ".implode(',', $product_codes)."<br>\n";
			echo "colors: ".implode(',', $colors)."<br />\n";
			flush();

			if (empty($product_codes)){
				echo "<strong>[ FATAL ERROR ]</strong> No product codes found for this product so skipping it.  Check if the CSV files are containing proper data.<br />\n";
				flush();
				continue;
			}

			// using product codes lets search for existing product objects:
			$existing_products = $this->db_fetch_array("
				select
					objects.status as status,
					otto_prod_to_code_lut.product_code as product_code,
					otto_prod_to_code_lut.product_id as product_id
				from
					otto_prod_to_code_lut
					left join objects on (objects.oid = otto_prod_to_code_lut.product_id)
				where
					product_code in (".implode(",", map("'%s'", $product_codes)).") and
					objects.status > 0 and
					objects.lang_id = ".aw_global_get('lang_id')."
			");
			$product_object_ids = array();
			foreach ($existing_products as $existing_product)
			{
				$product_object_ids[] = $existing_product['product_id'];
			}


			// lets delete the rows from the otto_prod_to_code_lut, which are related to that product by product codes:
			if (!empty($product_object_ids))
			{
				// clean up the otto_prod_to_code_lut database table, which connects product codes to product object id-s
				$this->db_query("delete from otto_prod_to_code_lut where product_id in (".implode(',', $product_object_ids).")");

				$product_object_id = reset($product_object_ids);
				$product_obj = new object($product_object_id);
				echo "found existing product <br>\n";
			}
			else
			{
				$product_obj = obj();
				$product_obj->set_class_id(CL_SHOP_PRODUCT);
				$product_obj->set_parent($o->prop("prod_folder"));

				$shop_product_cfgform_id = $o->prop('shop_product_config_form');
				if (!empty($shop_product_cfgform_id))
				{
					$product_obj->set_meta("cfgform_id", $shop_product_cfgform_id);
				}
				$product_obj->set_meta("object_type", 1040);
				$product_obj->set_prop("item_type", 593);

				$product_obj->save();
				echo "created new product <br>\n";
			}

			$product_obj->set_name($product["title"]);
			$product_obj->set_prop("userta2", $product["c"]);
			$product_obj->set_prop("user18", $product["pg"]);
			$product_obj->set_prop("user19", $product["nr"]);

			// user6 - product codes, comma separated list
			$product_obj->set_prop('user6', implode(',', $product_codes));

			// user7 - colors, comma separated list
			$product_obj->set_prop('user7', implode(',', $colors));

			$product_obj->save();

			// allow to update images
			$update_product_images = false;
			if ( ( $product_obj->prop('userch2') != 1 && $arr['update_product_images'] ) || $arr['force_update_product_images'] )
			{
				$update_product_images = true;
			}

			// allow to update categories
			$update_product_categories = false;
			if ( ( $product_obj->prop('userch2') != 1 && $arr['update_product_categories'] ) || $arr['force_update_product_categories'] )
			{
				$update_product_categories = true;
			}

			// allow to update connected products
			$update_connected_products = false;
			if ( ( $product_obj->prop('userch2') != 1 && $arr['update_connected_products'] ) || $arr['force_update_connected_products'] )
			{
				$update_connected_products = true;
			}
/*
			if ($arr['force_full_update'])
			{
				$update_product_images = true;
				$update_product_categories = true;
				$update_connected_products = true;
			}
*/
			////
			// otto_prod_to_code_lut tabelisse tootekood <-> toote objekti id seosed:
			foreach ($product_codes as $product_code)
			{
				$this->db_query("
					insert into
						otto_prod_to_code_lut
					set
						product_code = '".$product_code."',
						product_id = ".$product_obj->id().",
						color = '".$code_data['color']."'
				");
			}

			////
			// CATEGORIES
			////
			// products belong to several categories
			// every category is shown under specified section
			// to skip the categories level while showing the products, I have a section<->product lookup table
			if ($update_product_categories)
			{
				$categories = array($product['pg']);
				foreach (explode(',', $product['extrafld']) as $extrafld)
				{
					$categories[] = $extrafld;
				}
				$this->db_query("delete from otto_prod_to_section_lut where product=".$product_obj->id());
				$sections = $this->db_fetch_array("
					select
						aw_folder
					from
						otto_imp_t_aw_to_cat
					where
						category in (".implode(',', map("'%s'", $categories)).") and
						lang_id = ".aw_global_get('lang_id')."
					group by
						aw_folder
				");

				$product_sections = array();
				foreach ($sections as $section)
				{
					$this->db_query('insert into otto_prod_to_section_lut set
						product='.$product_obj->id().',
						section='.$section['aw_folder'].',
						lang_id='.aw_global_get('lang_id').'
					');
					$product_sections[$section['aw_folder']] = $section['aw_folder'];
				}
				// save additional categories to product
				$product_obj->set_prop("user11", $product["extrafld"]);
			}
			else
			{
				// if the categories will not be updated, then i need to get the sections for this product anyway:
				$sections = $this->db_fetch_array("
					select
						section
					from
						otto_prod_to_section_lut
					where
						product = ".$product_obj->id()." and
						lang_id = ".aw_global_get('lang_id')."
				");

				$product_sections = array();
				foreach ($sections as $section)
				{
					$product_sections[$section['aw_folder']] = $section['aw_folder'];
				}
			}

			////
			// IMAGES
			////
			$images = $this->db_fetch_array("
				select
					*
				from
					otto_prod_img
				where
					pcode in (".implode(',', map("'%s'", $product_codes)).") and
					p_nr = '".$product['nr']."'
			");


			$images_arr = array();
			// lets get the images from current product as well and merge them with new ones
			foreach (safe_array(explode(',', $product_obj->prop('user3'))) as $value)
			{
				if (!empty($value))
				{
					$images_arr[$value] = $value;
				}
			}
			$connection_image = '';
			foreach (safe_array($images) as $value)
			{
				if (!empty($value['imnr']))
				{
					$images_arr[$value['imnr']] = $value['imnr'];
				}

				// siin v6ib olla nyyd see probleem, et vahel, m6nel tootel ei ole seda yhenduspilti
				// k6ikide tootekoodide kohta. ma arvan, et see on otto poolne black tegelt
				// nii et niikui leian selle yhenduspildi, siis aitab kyll:
				if (!empty($value['conn_img']) && empty($connection_image))
				{
					$connection_image = $value['conn_img'];
				}
			}
			echo "selle toote koodidele leiti j&auml;rgmised pildid: ".implode(',', $images_arr)."<br />\n";
			if ($update_product_images)
			{
				echo "Saving images to product: ".implode(',', $images_arr)."<br />\n";
				$product_obj->set_prop('user3', implode(',', $images_arr));
			}
			$product_obj->set_prop('user8', $images[0]['video']);

			////
			// CONNECTED PRODUCTS
			////
			if ($update_connected_products)
			{
				////
				// scanning which products should be visible via connection images and categories
				////
				// I need to find other products which have the same
				// connection image and which should have been shown under products list
				if (!empty($connection_image))
				{
					echo "&uuml;hendav pilt: [".$connection_image."]<br />\n";
					$products_ol = new object_list(array(
						'class_id' => CL_SHOP_PRODUCT,
						'user2' => $connection_image,
						'status' => array(STAT_ACTIVE, STAT_NOTACTIVE),
						'oid' => new obj_predicate_not($product_obj->id())
					));

					$product_obj->set_prop('user2', $connection_image);
					if ($products_ol->count())
					{
						echo "found more products with this connection image <br />\n";

						$products_ol_ids = $products_ol->ids() + array($product_obj->id());
						$product_obj->set_prop('user4', implode(',', $product_ol_ids));
						$product_obj->set_prop('userch4', 1);
						echo "product oids with this connection image: ".implode(',', $products_ol_ids)."<br />\n";

						$visible_product = false;
						foreach ($products_ol->arr() as $products_ol_item_id => $products_ol_item)
						{
							// nyyd on asi nii, et siin ma panen k6ik objektid listis mitte n2htavaks
							// n2htavaks objektiks selles pundis saab alati just 2sja imporditud toode

							// aga samal ajal ma peaks tsekkima ka sektsioone, et kui sektsioonid on erinevad
							// siis peaks ka toote n2htavaks panema.

							// ysnaga ma pean tegema query otto_prod_to_section_lut-i selle objekti id kohta
							$this->db_query("select section from otto_prod_to_section_lut where product = ".$products_ol_item_id);
							$product_ol_item_sections = array();
							while ($row = $this->db_next())
							{
								$product_ol_item_sections[$row['section']] = $row['section'];
							}

							if (count($product_sections) > count($product_ol_item_sections))
							{
								$array_diff_res = array_diff($product_sections, $product_ol_item_sections);
							}
							else
							{
								$array_diff_res = array_diff($product_ol_item_sections, $product_sections);
							}
							if (!empty($array_diff_res))
							{
								echo "Seda toodet n&auml;idatakse erinevate sektsioonide all, nii, et m2rgin ka selle toote listis n2htavaks <br />\n";
								$products_ol_item->set_prop('userch4', 1);
							}
							else
							{
								$products_ol_item->set_prop('userch4', '');
							}

							$products_ol_item->set_prop('user4', implode(',', $products_ol_ids));
							$products_ol_item->save();
						}

					}
					else
					{
						echo "teisi selle yhenduspildiga tooteid ei leidnud, m&auml;rgin selle toote listis n&auml;idatavaks: [".$product_obj->id()."]<br />\n";
						$product_obj->set_prop('userch4', 1);
					}
				}
				else
				{
					echo "&uuml;hendav pilt puudub, m&auml;rgin selle toote listis n&auml;idatavaks [".$product_obj->id()."]<br />\n";
					$product_obj->set_prop('userch4', 1);
				}
			}
			else
			{
				echo "ei uuendatud teisi tooteid, toodete nimekirjas n&auml;htavust ega &uuml;henduspilte <br />\n";
			}


			$product_obj->save();


			////
			// prices/sizes
			////
			// get list of attached packaging objects
			$pkgs = array();
			$pak_sl = array();
			foreach($product_obj->connections_from(array("type" => "RELTYPE_PACKAGING")) as $c)
			{
				$t = $c->to();
				$pkgs[$t->prop('user6')][$t->prop("user8")][$t->prop("price")][$t->prop("user5")] = $t->id();
				$pak_sl[] = $t->id();
			}
			$found = array();

			$lowest = 10000000;

			// now, for each price, create packaging objects
			echo "---- [Iga hinna jaoks tekita pakendi objekt (packaging)]<br>\n";

			$this->db_query("
				select
					otto_imp_t_prices.pg as prices_pg,
					otto_imp_t_prices.nr as prices_nr,
					otto_imp_t_prices.type as prices_type,
					otto_imp_t_prices.size as size,
					otto_imp_t_prices.unit as unit,
					otto_imp_t_prices.price as price,
					otto_imp_t_prices.s_type as prices_s_type,

					otto_imp_t_codes.pg as codes_pg,
					otto_imp_t_codes.nr as codes_nr,
					otto_imp_t_codes.size as codes_size,
					otto_imp_t_codes.color as color,
					otto_imp_t_codes.code as code,
					otto_imp_t_codes.s_type as codes_s_type,
					otto_imp_t_codes.full_code as full_code
				from
					otto_imp_t_prices left join otto_imp_t_codes on (
						otto_imp_t_prices.nr = otto_imp_t_codes.nr and
						otto_imp_t_prices.s_type = otto_imp_t_codes.s_type and
						otto_imp_t_prices.pg = otto_imp_t_codes.pg
					)
				where
					otto_imp_t_prices.pg = '".$product['pg']."' and
					otto_imp_t_codes.pg = '".$product['pg']."' and
					otto_imp_t_prices.nr = '".$product['nr']."' and
					otto_imp_t_codes.nr = '".$product['nr']."' and
					otto_imp_t_prices.lang_id = ".aw_global_get('lang_id')." and
					otto_imp_t_codes.lang_id = ".aw_global_get('lang_id')."
				order by prices_nr,code,price
			");

			$rows = array();
			while ($row = $this->db_next())
			{
			//	echo "XXX (csv tabelitest) ".$row['prices_nr']." - ".$row['code']." - ".$row['color']." ".$row['price']." - ".$row['size']." - ".$row['prices_s_type']."<br>\n";
				$rows[] = $row;
			}

			$sizes = false;
			$min_price = 0;
			$max_price = 0;
			foreach($rows as $row)
			{
				// gotta split the sizes and do one packaging for each
				$s_tmpc = explode(",", $row["size"]);
				$s_tmp = array();
				foreach($s_tmpc as $tmpcc)
				{
					// because the bloody csv files don't contain 100 106, that would mean 100,102,104,106, but they contain 100106
					// so try to be intelligent and split those
					if ($tmpcc > 100000)
					{
						$s_from = $tmpcc{0}.$tmpcc{1}.$tmpcc{2};
						$s_to = $tmpcc{3}.$tmpcc{4}.$tmpcc{5};
						for ($pup = $s_from; $pup <= $s_to; $pup+=2)
						{
							$s_tmp[] = $pup;
						}
					}
					else
					if ($tmpcc > 10000)
					{
						$s_from = $tmpcc{0}.$tmpcc{1};
						$s_to = $tmpcc{2}.$tmpcc{3}.$tmpcc{4};
						for ($pup = $s_from; $pup <= $s_to; $pup+=2)
						{
							$s_tmp[] = $pup;
						}
					}
					else
					{
						$s_tmp[] = $tmpcc;
					}
				}

				foreach($s_tmp as $tmpcc)
				{
					$sizes = true;
					$row["size"] = $tmpcc;
					if (is_oid($pkgs[$row['code']][$row["codes_s_type"]][$row["price"]][$row["size"]]))
					{
						$pk = obj($pkgs[$row['code']][$row["codes_s_type"]][$row["price"]][$row["size"]]);
//						echo "------ for prod ".$product_obj->name()." got (".$pk->id().") packaging ".$row["price"]." for type :: prices/codes: ".$row["prices_s_type"]."/".$row["codes_s_type"]."<br>";
						echo "------ [OLEMAS] Pakend: ";
						echo " Tootekood: ".$row['code']." / ";
						echo " V&auml;rv: ".$row['color']." / ";
						echo " s. liik: ".$row['prices_s_type']." / ";
						echo " Suurus: ".$row['size']." / ";
						echo " Hind: ".$row['price']." / <br />\n";
					}
					else
					{
					//	echo "------ for prod ".$product_obj->name()." got NEW packaging ".$row["price"]." for type :: prices/codes: ".$row["prices_s_type"]."/".$row["codes_s_type"]."<br>";
						echo "------ [UUS] Pakend: ";
						echo " Tootekood: ".$row['code']." / ";
						echo " V&auml;rv: ".$row['color']." / ";
						echo " s. liik: ".$row['prices_s_type']." / ";
						echo " Suurus: ".$row['size']." / ";
						echo " Hind: ".$row['price']." / <br />\n";

						$pk = obj();
						$pk->set_class_id(CL_SHOP_PRODUCT_PACKAGING);
						$pk->set_parent($product_obj->id());
						$pk->save();

						$product_obj->connect(array(
							"to" => $pk->id(),
							"reltype" => 2 // RELTYPE_PACKAGING
						));
					}

					// i need to know min and max prices of the product:
					if ($max_price < $row['price'])
					{
						$max_price = $row['price'];
					}
					if ($min_price == 0 || $min_price > $row['price'])
					{
						$min_price = $row['price'];
					}

					$pk->set_parent($product_obj->id());
					$pk->set_prop("price", $row["price"]);
					$pk->set_prop("user5", $row["size"]);
					$pk->set_prop("user6", $row["code"]);
					$pk->set_prop("user7", $row["color"]);
					$pk->set_prop("user8", $row["codes_s_type"]);
					$pk->set_name($product_obj->name());
					$pk->save();

					$lowest = min($lowest, $row["price"]);

					$used[$pk->id()] = true;
					$first = false;
				}
			}
			$product_obj->set_prop('user14', $min_price);
			$product_obj->set_prop('user15', $max_price);
			$product_obj->save();
			foreach($pak_sl as $pak_sl_id)
			{
				if (!$used[$pak_sl_id] && $this->can('view', $pak_sl_id))
				{
					$product_obj->disconnect(array(
						"from" => $pak_sl_id
					));
					echo "disconnect from $pak_sl_id <br>";
				}
			}




			////
			// lets put the imported product id into the session, so i can show it after the import
			////
			$_SESSION['otto_import_product_data'][$product_obj->id()] = $product_obj->id();

			// lets fill the otto_import_log table:
			$this->db_query('
				INSERT INTO
					otto_import_log
				SET
					import_time = '.$import_time.',
					product_id = '.$product_obj->id().',
					csv_files = \''.str_replace( "\n", ",", trim($o->prop('fnames')) ).'\'
			');


		}

		echo "[!!] hear hear. prods done. Imporditi $items_done toodet [!!] <br>\n";
		$sql = 'select import_time from otto_import_log group by import_time order by import_time desc limit 20';
		if (aw_global_get('lang_id') == 7 || aw_global_get('lang_id') == 6)
		{
			$sql = 'select import_time from otto_import_log where csv_files like \'LET%\' group by import_time order by import_time desc limit 20';
		}
		if (aw_global_get('lang_id') == 1)
		{
			$sql = 'select import_time from otto_import_log where csv_files like \'EST%\' group by import_time order by import_time desc limit 20';
		}
		$this->db_query($sql);
		$latest_import_times = array();
		while ($row = $this->db_next())
		{
			$latest_import_times[] = $row['import_time'];
		}

		$sql = 'delete from otto_import_log where import_time not in('.implode(',', $latest_import_times).')';
		if (aw_global_get('lang_id') == 7 || aw_global_get('lang_id') == 6)
		{
			$sql = 'delete from otto_import_log where csv_files like \'LET%\' and import_time not in('.implode(',', $latest_import_times).')';
		}
		if (aw_global_get('lang_id') == 1)
		{
			$sql = 'delete from otto_import_log where csv_files like \'EST%\' and import_time not in('.implode(',', $latest_import_times).')';
		}
		$this->db_query($sql);

		////////////////
		// clear cache
		////////////////
		$cache = get_instance("cache");
 		$cache->file_clear_pt("menu_area_cache");
		$cache->file_clear_pt("storage_search");
		$cache->file_clear_pt("storage_object_data");
		$cache->file_clear_pt("html");
		$cache->file_clear_pt("acl");

		$fld = aw_ini_get("site_basedir")."/prod_cache";
		$cache->_get_cache_files($fld);
		echo 'about to delete '.count($cache->cache_files2).' files<br />';

		foreach(safe_array($cache->cache_files2) as $file)
		{
			unlink($file);
		}

		echo "all done! <br />\n";
		return;
	//	die(t("all done! <br>"));
	}

	function import_data_from_csv($o)
	{
		$lang_id = aw_global_get('lang_id');
		$this->db_query("DELETE FROM otto_imp_t_prod WHERE lang_id=".$lang_id);
		$this->db_query("DELETE FROM otto_imp_t_codes WHERE lang_id=".$lang_id);
		$this->db_query("DELETE FROM otto_imp_t_prices WHERE lang_id=".$lang_id);

		$import_time = time();

	//	$fext = 'xls';
		$fext = 'txt';

		foreach(explode("\n", $o->prop("fnames")) as $fname)
		{
			if (trim($fname) == "")
			{
				continue;
			}
			$fld_url = $o->prop("base_url")."/".trim($fname)."-1.".$fext;
			if (!$this->is_csv($fld_url))
			{
				echo "<span style=\"color:red\">[ ERROR ] faili $fld_url ei suudetud lugeda/parsida </span><br />\n";
				continue;
			}
			echo "[ reading from the first file ]<br>\n";
			echo "from url ".$fld_url." read: <br>";
			flush();
			list(, $cur_pg) = explode(".", $fname);
			$cur_pg = substr($cur_pg,1);

			if ((string)((int)$cur_pg{0}) === (string)$cur_pg{0})
			{
				$cur_pg = (int)$cur_pg;
			}
			$cur_pg = trim($cur_pg);
			$first = true;
			$num = 0;

			// fucking mackintosh
			if (count(file($fld_url)) == 1)
			{
				$lines = $this->mk_file($fld_url, "\t");
				if (count($lines) > 1)
				{
					$tmpf = tempnam("/tmp", "aw-ott-imp");
					$fp = fopen($tmpf,"w");
					fwrite($fp, join("\n", $lines));
					fclose($fp);
					$fld_url = $tmpf;
				}
			}

			$fp = fopen($fld_url, "r");
			while ($row = fgetcsv($fp,1000,"\t"))
			{
				if ($first)
				{
					$first = false;
					continue;
				}
				if (count($row) < 2)
				{
					continue;
				}

				if (trim($row[2]) == "" && trim($row[1]) == "" && trim($row[3]) == "")
				{
					continue;
				}

				$this->quote(&$row);
				$row = $this->char_replacement($row);
				$row[2] = $this->conv($row[2]);

				// Lets remove the first underscore --dragut@13.08.2009
				if ($row[2]{0} == '_')
				{
					$row[2] = substr($row[2], 1);
				}

				// Lets remove the first underscore --dragut@13.08.2009
				$extrafld = trim($row[3]);
				if ($extrafld{0} == '_')
				{
					$extrafld = substr($extrafld, 1);
				}
				$desc = $this->conv(trim($row[4]." ".$row[5]." ".$row[6]." ".$row[7]." ".$row[8]." ".$row[9]." ".$row[10]." ".$row[11]." ".$row[12]." ".$row[13]." ".$row[14]." ".$row[15]." ".$row[16]." ".$row[17]." ".$row[18]." ".$row[19]." ".$row[20]." ".$row[21]." ".$row[22]." ".$row[23]." ".$row[24]." ".$row[25]." ".$row[26]." ".$row[27]." ".$row[28]." ".$row[29]." ".$row[30]." ".$row[31]." ".$row[32]." ".$row[33]." ".$row[34]." ".$row[35]." ".$row[36]." ".$row[37]." ".$row[38]." ".$row[39]." ".$row[40]." ".$row[41]." ".$row[42]));

				// remove the underscore before description as well
				if ($desc{0} == '_'){
					$desc = substr($desc, 1);
				}

				$this->db_query("
					INSERT INTO otto_imp_t_prod(pg,nr,title,c,extrafld, lang_id)
					VALUES('$cur_pg','$row[1]','$row[2]','$desc','$extrafld', ".aw_global_get('lang_id').")
				");

				if ($row[2] == "")
				{
					echo "ERROR ON LINE $num title ".$row[2]." <br>";
					flush();
					$log[] = "VIGA real $num failis $fld_url nimi: ".$row[2];
				}
				$num++;

				echo "-- Lisasin toote numbriga [".$row[1]."], leht: [".$cur_pg."], extrafld/kategooria: [".$extrafld."],  nimi: [".$row[2]."]<br>\n";
				flush();

			}

			if ($tmpf)
			{
				@unlink($tmpf);
			}

			echo "[ ...got $num titles from file $fld_url] <br><br>";
			flush();
			$log[] = "lugesin failist $fld_url $num toodet";
		}

		foreach(explode("\n", $o->prop("fnames")) as $fname)
		{
			if (trim($fname) == "")
			{
				continue;
			}
			$fld_url = $o->prop("base_url")."/".trim($fname)."-2.".$fext;
			if (!$this->is_csv($fld_url))
			{
				echo "<span style=\"color:red\">[ ERROR ] faili $fld_url ei suudetud lugeda/parsida </span><br />\n";
				continue;
			}
			echo "[ reading from the second file ]<br>\n";
			echo "from url ".$fld_url." read: <br>\n";
			flush();
			list(, $cur_pg) = explode(".", $fname);
			$cur_pg = substr($cur_pg,1);
			if ((string)((int)$cur_pg{0}) === (string)$cur_pg{0})
			{
				$cur_pg = (int)$cur_pg;
			}
			$cur_pg = trim($cur_pg);

			$first = true;
			$num =0;

			if (count(file($fld_url)) == 1)
			{
				$lines = $this->mk_file($fld_url, "\t");
				if (count($lines) > 1)
				{
					$tmpf = tempnam("/tmp", "aw-ott-imp");
					$fp = fopen($tmpf,"w");
					fwrite($fp, join("\n", $lines));
					fclose($fp);
					$fld_url = $tmpf;
				}
			}

			$fp = fopen($fld_url, "r");
			while ($row = fgetcsv($fp,1000,"\t"))
			{
				if ($first)
				{
					$first = false;
					continue;
				}
				if (count($row) < 2)
				{
					continue;
				}

				if ($row[2] == "" && $row[1] == "" && $row[3] == "")
				{
					continue;
				}

				$this->quote(&$row);
				$row = $this->char_replacement($row);

				// some weird underscores were introduced in the beginning of the field --dragut@13.08.2009
				if ($row[2]{0} == '_')
				{
					$row[2] = substr($row[2], 1);
				}
				$row[3] = str_replace('_', '', $row[3]);
				$row[4] = str_replace('_', '', $row[4]);

				$full_code = str_replace(".","", $row[4]);
				$full_code = str_replace(" ","", $full_code);

				$row[4] = substr(str_replace(".","", str_replace(" ","", $row[4])), 0, 8);
				$color = $row[3];
				if ($row[2] != "")
				{
					$color .= " (".$row[2].")";
				}

				$set_f_img = trim($row[5]);

				$this->db_query("
					INSERT INTO otto_imp_t_codes(pg,nr,s_type,color,code, full_code, set_f_img, lang_id)
					VALUES('$cur_pg','$row[1]','$row[2]','$color','$row[4]','$full_code', '$set_f_img', ".aw_global_get('lang_id').")
				");
				$num++;
				if (!$row[4])
				{
					echo "ERROR ON LINE $num code ".$row[4]." <br>";
					flush();
					$log[] = "VIGA real $num failis $fld_url kood: $row[4]";
				}

				// collect data for those product codes where no picture were found
				if (array_search($row[4], $this->not_found_products) !== false)
				{
					$not_found_products_by_page[$cur_pg][$row[4]] = $row;
					$prod_title = $this->db_fetch_field("select title from otto_imp_t_prod where pg='".$cur_pg."' and nr='".$row[1]."' and lang_id=".aw_global_get('lang_id'), "title");
					$not_found_products_by_page[$cur_pg][$row[4]][] = $prod_title;
					echo "X";

				}

				echo "-- Lisasin koodi numbriga $row[1], leht: [$cur_pg], tyyp: [$row[2]], v2rv: [$color], kood: [$row[4]], t2iskood: [$full_code], set_f_img: [$set_f_img]<br>\n";

			}

			if ($tmpf)
			{
				@unlink($tmpf);
			}

			echo "[... got $num codes from file $fld_url] <br><br>\n";
			$log[] = "lugesin failist $fld_url $num koodi";
			flush();
		}

		foreach(explode("\n", $o->prop("fnames")) as $fname)
		{
			if (trim($fname) == "")
			{
				continue;
			}

			$fld_url = $o->prop("base_url")."/".trim($fname)."-3.".$fext;
			if (!$this->is_csv($fld_url))
			{
				echo "<span style=\"color:red\">[ ERROR ] faili $fld_url ei suudetud lugeda/parsida </span><br />\n";
				continue;
			}
			echo "[ reading from the third file ]<br>\n";
			echo "from url ".$fld_url." read: <br>";
			flush();
			list(, $cur_pg) = explode(".", $fname);
			$cur_pg = substr($cur_pg,1);
			if ((string)((int)$cur_pg{0}) === (string)$cur_pg{0})
			{
				$cur_pg = (int)$cur_pg;
			}
			$cur_pg = trim($cur_pg);

			$first = true;

			if (count(file($fld_url)) == 1)
			{
				$lines = $this->mk_file($fld_url, "\t");
				if (count($lines) > 1)
				{
					$tmpf = tempnam("/tmp", "aw-ott-imp-3");
					$fp = fopen($tmpf,"w");
					fwrite($fp, join("\n", $lines));
					fclose($fp);
					$fld_url = $tmpf;
				}
			}

			$num = 0;
			$fp = fopen($fld_url, "r");
			while ($row = fgetcsv($fp,1000,"\t"))
			{
				if ($first)
				{
					$first = false;
					continue;
				}
				if (count($row) < 2)
				{
					continue;
				}

				if ($row[2] == "" && $row[1] == "" && $row[3] == "")
				{
					continue;
				}
				if ($row[2]{0} == '_')
				{
					$row[2] = substr($row[2], 1);
				}
				$orow = $row;
				if (count($row) == 5)
				{
					$row[5] = $row[4];
					$row[4] = "";
				}
				$row = $this->char_replacement($row);
				$this->quote(&$row);

				$row[3] = str_replace('_', '', $row[3]);

				$orig = $row[5];
			//	$row[5] = (double)trim(str_replace(",",".", str_replace("-", "",str_replace(chr(160), "", $row[5]))));
				$searches = array(',', '-', '_', chr(160), chr(208));
				$replaces = array('.', '', '', '', '');
				$row[5] = (double)trim(str_replace($searches, $replaces, $row[5]));
				if ($row[4] == "")
				{
					$row[4] = "tk";
				}
				$this->db_query("
					INSERT INTO otto_imp_t_prices(pg,nr,s_type,size,unit,price, lang_id)
					VALUES('$cur_pg','$row[1]','$row[2]','$row[3]','$row[4]','$row[5]', ".aw_global_get('lang_id').")
				");


				if (!$row[5])
				{
					echo "ERROR ON LINE $num price = $row[5] (orig = $orig)<br>".dbg::dump($orow);
					flush();
					$log[] = "VIGA real $num hind = $row[5]";

					for ($i = 0; $i < strlen($orig); $i++)
					{
						echo "at pos ".$i." char = ".ord($orig{$i})." v = ".$orig{$i}." <br>";
					}
				}
				$num++;

				echo "-- Lisasin hinna numbriga [$row[1]], leht: [$cur_pg], tyyp: [$row[2]], suurus: [$row[3]], yhik: [$row[4]], hind: [$row[5]]<br>\n";
				flush();
			}

			if ($tmpf)
			{
				@unlink($tmpf);
			}
			echo "[... got $num prices from file $fld_url ] <br>\n";
			$log[] = "lugesin failist $fld_url $num hinda";
			flush();
		}

	}

	function clean_up_products_to_code_lut()
	{
		$products = $this->db_fetch_array("
			SELECT
				otto_prod_to_code_lut.product_id as product_id
			FROM
				otto_prod_to_code_lut
				LEFT JOIN objects ON (objects.oid = otto_prod_to_code_lut.product_id)
			WHERE
				objects.status = 0 OR
				objects.status IS NULL
		");

		if (!empty($products))
		{
			$product_ids = array();
			foreach ($products as $value)
			{
				$product_ids[] = $value['product_id'];
			}
			$this->db_query("delete from otto_prod_to_code_lut where product_id in (".implode(',', $product_ids).")");
		}

		return true;
	}

	function clean_up_products_to_section_lut()
	{
		$products = $this->db_fetch_array("
			SELECT
				otto_prod_to_section_lut.product as product_id
			FROM
				otto_prod_to_section_lut
				LEFT JOIN objects ON (objects.oid = otto_prod_to_section_lut.product)
			WHERE
				objects.status = 0 OR
				objects.status IS NULL
		");

		if (!empty($products))
		{
			$product_ids = array();
			foreach ($products as $value)
			{
				$product_ids[] = $value['product_id'];
			}
			$this->db_query("delete from otto_prod_to_section_lut where product in (".implode(',', $product_ids).")");
		}

		return true;
	}

	function clean_up_otto_prod_img_table()
	{
		$this->db_query("select * from otto_prod_img");
		$delete_images = array();
		while ($row = $this->db_next())
		{

			$imnr = $row['imnr'];
			$url = aw_ini_get('baseurl').'/vv_product_images/'.$imnr{0}.'/'.$imnr{1}.'/'.$imnr.'_2.jpg';
			$file = aw_ini_get('site_basedir').'/product_images/'.$imnr{0}.'/'.$imnr{1}.'/'.$imnr.'_2.jpg';
		//	if (getimagesize($url) !== false)
		//	if (is_readable($file))
			if (filesize($file) > 0)
			{
				echo "[".$imnr."] ".$file." [ok]<br />\n";
				flush();
			}
			else
			{
				echo "[".$imnr."] ".$file." [fail]<br />\n";
				$delete_images[$imnr] = $imnr;
				flush();
			}
		}
		arr($delete_images);
		arr(count($delete_images));

		if (!empty($delete_images))
		{
			$this->db_query("delete from otto_prod_img where imnr in (".implode(',', map("'%s'", $delete_images)).")");
		}
		echo "all done <br />\n";
		return true;
	}

	function conv($str)
	{
		$str = str_replace(chr(207), (string)(ord(126).ord(94)), $str);
		return $str;
	}

	function char_replacement($str)
	{
		/* l2ti t2hed
		,
		,chr(226)
		,chr(238)
		,chr(239)
		,chr(231)


		Andmete allikaks oli:
		Impordifail: http://terryf.struktuur.ee/str/otto/import/data/LAT.T004-11.txt
		Tekst saidil (skrolli alla): http://otto-latvia.struktuur.ee/134393
		kooditabel: http://www.science.co.il/Language/Character-Code.asp?s=1257
		*/
		if (aw_global_get("lang_id") == 6)
		{

			$needle = array(
			chr(207), //254
			chr(240), //251
			chr(165), //238
			chr(236), //234
			chr(191), //242
			//chr(199), //226
			chr(148), //199
			chr(239), //231
			chr(134), //239
			chr(174), //236
			chr(149), //231
			chr(192), //242
			chr(228), //240
			chr(180), //238
			chr(250), //237
			chr(137), //200
			chr(208), //45
			chr(130), //226
			chr(153), //237
			chr(179), //34
			chr(129), //194
			chr(210), //34
			chr(211), //34
			chr(178), //34
			chr(175), //236
			chr(183), //208
			chr(177), //206
			chr(185), //207
			chr(225), //208
			chr(186), //239
			chr(158), //236
			chr(202),
			chr(200), // "
			chr(199),  // "
			chr(161), // &deg;
			chr(181), // 205
			chr(227), //34
			chr(234), //&#382;
			chr(139), //&#269;
			);






			$haystack = array(
			chr(254),
			chr(251),
			chr(238),
			chr(234),
			chr(242),
			//chr(226),
			chr(199),
			chr(231),
			chr(239),
			chr(236),
			chr(231),
			chr(242),
			chr(240),
			chr(238),
			chr(237),
			chr(200),
			chr(45),
			chr(226),
			chr(237),
			chr(34),
			chr(194),
			chr(34),
			chr(34),
			chr(34),
			chr(236),
			chr(208),
			chr(206),
			chr(207),
			chr(208),
			chr(239),
			chr(234),
			"",
			"&quot;",
			"&quot;",
			"&deg;",
			chr(205),
			chr(34),
			"&#382;",
			"&#269;",
			);
		}
		elseif (aw_global_get("lang_id") == 7)
		{

			$needle = array(
			//chr(207), //254
			chr(240), //251
			chr(165), //238
			chr(236), //234
			chr(191), //242
			//chr(199), //226
			chr(148), //199
			chr(239), //231
			chr(134), //239
			chr(174), //236
			chr(149), //231
			chr(192), //242
			chr(228), //240
			chr(180), //238
			chr(250), //237
			chr(137), //200
			chr(208), //45
			chr(130), //226
			chr(153), //237
			chr(179), //34
			chr(129), //194
			chr(210), //34
			chr(211), //34
			chr(178), //34
			chr(175), //236
			chr(183), //208
			chr(177), //206
			chr(185), //207
			chr(225), //208
			chr(186), //239
			//chr(158), //236
			chr(202),
			//chr(200), // "
			//chr(199),  // "
			chr(161), // &deg;
			chr(181), // 205
			chr(227), //34
			chr(234), //&#382;
			chr(139), //&#269;
			);






			$haystack = array(
			//chr(254),
			chr(251),
			chr(238),
			chr(234),
			chr(242),
			//chr(226),
			chr(199),
			chr(231),
			chr(239),
			chr(236),
			chr(231),
			chr(242),
			chr(240),
			chr(238),
			chr(237),
			chr(200),
			chr(45),
			chr(226),
			chr(237),
			chr(34),
			chr(194),
			chr(34),
			chr(34),
			chr(34),
			chr(236),
			chr(208),
			chr(206),
			chr(207),
			chr(208),
			chr(239),
			//chr(234),
			"",
			//"&quot;",
			//"&quot;",
			"&deg;",
			chr(205),
			chr(34),
			"&#382;",
			"&#269;",
			);
		}
		else
		{
		$needle = array(
			chr(158),	// &#381;
			chr(213),	// ylakoma;
			chr(235),	// zhee;
			chr(159),	// &uuml;
			chr(134), 	// &Uuml;
			chr(154),	// &ouml;
			chr(228), // shaa
			chr(138),	// &auml;
			chr(205),	// &Otilde;
			chr(155), 	// &otilde;
			chr(199),
			chr(200),
			chr(210),
			chr(211),
			chr(175),
			chr(236), //&#382;
			chr(227), //34
			chr(225), //&#352;
			chr(149), //z

		);
		$haystack = array(
			"&#381;",
			chr(180),	// ylakoma;
			chr(158),// zhee;
			chr(252),// &uuml;
			chr(220),	// &Uuml;
			chr(246),// &ouml;
			chr(154), // shaa-enne oli 185
			chr(228),// &auml;
			chr(213),// &Otilde;
			chr(245),	// &otilde;
			chr(34),
			chr(34),
			chr(34),
			chr(34),
			chr(216),
			"&#382;",
			chr(34),
			"&#352;",
			"z",
		);
		}

		// xxx debug by dragut
		if (false)
		{
			if (is_array($str))
			{
				$xxx = $str[2];
				for ($i = 0; $i < strlen($xxx); $i++)
				{
					arr('---'.$xxx{$i}.' ---- '.ord($xxx{$i}));
				}
			}
		}

		if(is_array($str))
		{
			foreach($str as $key=>$value)
			{
				$str[$key]= str_replace($needle,$haystack,$value);
			}
		}
		else
		{
			$str = str_replace($needle,$haystack,$str);
		}

		// xxx debug by dragut
		if (false)
		{
			if (is_array($str))
			{
				$xxx = $str[2];
				for ($i = 0; $i < strlen($xxx); $i++)
				{
					arr('xxx'.$xxx{$i}.' ---- '.ord($xxx{$i}));
				}
			}
		}
		return $str;
	}

	/**

		@attrib name=submit_add_cart nologin=1

	**/
	function submit_add_cart($arr)
	{
		$afv = 1;

		if (!$arr["testjs"])
		{
			$afv = 2;
		}

		if (strpos($arr["return_url"], "?") === false)
		{
			$retval = aw_ini_get("baseurl").str_replace("afto=1", "", $arr["return_url"])."?afto=".$afv;
		}
		else
		{
			$retval = aw_ini_get("baseurl").str_replace("afto=1", "", $arr["return_url"])."&afto=".$afv;
		}
/*
		if (!$arr["testjs"])
		{
			//return $retval;
		}
*/

		// For bugs #250598, #263491
		//	Cancelled by bug #291652 -kaarel 16.12.2008
		//	And brought up again by bug #346805. Just a bit modified. -kaarel 16.06.2009
		// Discounts by site_id => sections
		$dc_siteid_sec = array(
			"276" => array(
				139072,		// Soodustooted /
				/*
				139076,		// Soodustooted / Naistele / Pluusid, s2rgid, topid
				139077,		// Soodustooted / Naistele / Jakid, kampsunid, pulloverid
				139078,		// Soodustooted / Naistele / Joped, mantlid
				139080,		// Soodustooted / Naistele / Pyksid
				139074,		// Soodustooted / Naistele / Seelikud, kleidid
				139075,		// Soodustooted / Naistele / Pesu, 88pesu, rannamood
				139082,		// Soodustooted / Naistele / Jalan6ud
				139085,		// Soodustooted / Meestele / Pluusid, s2rgid
				139089,		// Soodustooted / Meestele / Jakid, kampsunid
				139091,		// Soodustooted / Meestele / Pyksid
				139084,		// Soodustooted / Meestele / Pesu, 88pesu
				139094,		// Soodustooted / Lastele / R6ivad
				139095,		// Soodustooted / Lastele / Jalan6ud
				139097,		// Soodustooted / Aksessuaarid
				939720,		// Soodustooted / Kodusisustus / Voodipesu
				1158663,	// Soodustooted / Kodusisustus / K2ter2tikud
				139110,		// Soodustooted / Kodusisustus / Vannitoamatid
				689148,		// Soodustooted / Kodusisustus / Sulle abiks
				*/
			),
			"277" => array(
				186099
			),
		);
		$discount = 0;
		foreach($dc_siteid_sec as $site_id => $sections)
		{
			if(aw_ini_get("site_id") == $site_id)
			{
				foreach($sections as $section)
				{
					$ot = new object_tree(array(
						"class_id" => CL_MENU,
						"parent" => $section,
						"site_id" => aw_ini_get("site_id")
					));
					$ids = $ot->ids();
					if(!in_array(aw_global_get("section"), $ids) && time() < mktime(0, 0, 0, 8, 1, 2009))
					{
						$discount = 25;
					}
				}
			}
		}
		if($discount > 0)
		{
			$item_obj = obj($arr["add_to_cart"]);
			$arr["new_price"] = (1 - $discount / 100) * $item_obj->prop("price");
		}

		// rewrite some vars that are hard to rewire in js and forward to shop order cart
		$vars = $arr;
		if ($arr["spid"])
		{
			$vars["order_data"] = array();
			$vars["order_data"][$arr["add_to_cart".$arr["spid"]]]["prod_id"] = $arr["prod_id"];
			$vars["order_data"][$arr["add_to_cart".$arr["spid"]]]["color"] = ($arr["order_data_color".$arr["spid"]] != "" ? $arr["order_data_color".$arr["spid"]] : "---");
			$vars["order_data"][$arr["add_to_cart".$arr["spid"]]]["size"] = $arr["size_name".$arr["spid"]];
			$vars["order_data"][$arr["add_to_cart".$arr["spid"]]]["url"] = $retval;
			$vars["order_data"][$arr["add_to_cart".$arr["spid"]]]["discount"] = $discount;

			$vars["add_to_cart"] = array();
			$vars["add_to_cart"][$arr["add_to_cart".$arr["spid"]]] = $arr["add_to_cart_count".$arr["spid"]];
		}
		else
		{
			$vars["order_data"] = array();
			$vars["order_data"][$arr["add_to_cart"]]["prod_id"] = $arr["prod_id"];
			$vars["order_data"][$arr["add_to_cart"]]["color"] = ($arr["order_data_color"] != "" ? $arr["order_data_color"] : "---");
			$vars["order_data"][$arr["add_to_cart"]]["size"] = $arr["size_name"];
			$vars["order_data"][$arr["add_to_cart"]]["new_price"] = $arr["new_price"];
			$vars["order_data"][$arr["add_to_cart"]]["url"] = $retval;
			$vars["order_data"][$arr["add_to_cart".$arr["spid"]]]["discount"] = $discount;

			$vars["add_to_cart"] = array();
			$vars["add_to_cart"][$arr["add_to_cart"]] = $arr["add_to_cart_count"];
		}
		$i = get_instance(CL_SHOP_ORDER_CART);
		$i->submit_add_cart($vars);

		return $retval;
	}

	/**

	@attrib name=pictfix

	**/
	function pictfix($arr)
	{
	//	$this->pictimp(array(), true);
		$this->pictimp($arr['import_obj'], true);
	}

	// ??? --dragut
	function do_post_import_fixes($obj)
	{
		$query = 'select aw_oid, tauser2 from aw_shop_products where '.
						' tauser2 like "%'.chr(206).'%" or tauser2 like "%'.chr(207).'%" or tauser2 like"%'.chr(137).'%"';
		//echo $query,"<br><br>";
		$this->db_query($query);

		//echo $this->num_rows(),"<br>";
		while($arr = $this->db_next())
		{
			//echo $arr['user17'],"    ";
			$arr['tauser2'] = $this->char_replacement($arr['tauser2']);
			//echo $arr['user17'],"    ";
			$query = 'update aw_shop_products set tauser2="'.$arr['tauser2'].'" where aw_oid='.$arr['aw_oid'].' limit 1';
			echo $query,"<br>";
			$this->save_handle();
			$this->db_query($query);
			$this->restore_handle();
			//echo $query,"<br>";
		}
	}

	/** if no random other images show for some products, call this

		@attrib name=fix_image_codes

	**/
	function fix_image_codes()
	{
		return;
		echo "fixing image pages <br>\n";
		flush();
		$this->db_query("SELECT * FROM otto_prod_img WHERE p_pg IS NULL or p_nr IS NULL ");
		while ($row = $this->db_next())
		{
			if ($row["pcode"] == "hall" || substr($row["pcode"], 0, 3) == "bee")
			{
				continue;
			}
			echo "pcode = $row[pcode] <br>\n";
			flush();
			$this->save_handle();
			// find the correct ones from the prod by code
			$ol = new object_list(array(
				"class_id" => CL_SHOP_PRODUCT,
				"user20" => $row["pcode"]
			));
			if ($ol->count() > 0)
			{
				$o = $ol->begin();
				$pg = $o->prop("user18");
				$nr = $o->prop("user19");
				$this->db_query("UPDATE otto_prod_img SET p_pg = '$pg', p_nr = '$nr' WHERE pcode = '$row[pcode]' AND imnr = '$row[imnr]' AND nr = '$row[nr]'");
				echo "fixed code $row[pcode] <br>\n";
				flush();
			}
			$this->restore_handle();
		}
		echo ("all done! ");
	}

	/**

		@attrib name=fix_prices

	**/
	function fix_prices()
	{
		$ol = new object_list(array("class_id" => CL_SHOP_PRODUCT_PACKAGING, "price" => 0));
		foreach($ol->arr() as $o)
		{
			$c = reset($o->connections_to(array("type" => 2, "from.class_id" => CL_SHOP_PRODUCT)));
			if (!$c)
			{
				echo ("unconnected packaging ".$o->id()."!!!");
				continue;
			}
			$p = $c->from();
			$pg = $p->prop("user18");
			$nr = $p->prop("user19");
			$size = $o->prop("user5");

			$this->db_query("SELECT * FROM otto_imp_t_prices WHERE pg = '$pg' AND nr = '$nr'");
			while ($row = $this->db_next())
			{
				// find the correct size
				$sizes = $this->make_keys($this->_proc_size($row["size"]));
				if (isset($sizes[$size]))
				{
					echo "found price $row[price] for packet ".$o->name()."! <br>";
					$o->set_prop("price", $row["price"]);
					$o->save();
				}
			}
		}
		echo "all done! <br>";
	}

	function _proc_size($size)
	{
		$s_tmpc = explode(",", $size);
		$s_tmp = array();
		foreach($s_tmpc as $tmpcc)
		{
			// because the bloody csv files don't containt 100 106, that would mean 100,102,104,106, but they contain 100106
			// so try to be intelligent and split those
			if ($tmpcc > 100000)
			{
				$s_from = $tmpcc{0}.$tmpcc{1}.$tmpcc{2};
				$s_to = $tmpcc{3}.$tmpcc{4}.$tmpcc{5};
				for ($pup = $s_from; $pup <= $s_to; $pup+=2)
				{
					$s_tmp[] = $pup;
				}
			}
			else
			if ($tmpcc > 10000)
			{
				$s_from = $tmpcc{0}.$tmpcc{1};
				$s_to = $tmpcc{2}.$tmpcc{3}.$tmpcc{4};
				for ($pup = $s_from; $pup <= $s_to; $pup+=2)
				{
					$s_tmp[] = $pup;
				}
			}
			else
			{
				$s_tmp[] = $tmpcc;
			}
		}

		return $s_tmp;
	}

	function mk_file($file,$separator)
	{
		$filestr = file_get_contents($file);

		$len = strlen($filestr);
		$linearr = array();
		$in_cell = false;
		$line = '';
		for ($pos=0; $pos < $len; $pos++)
		{
			if ($filestr[$pos] == "\"")
			{
				if ($in_cell == false)
				{
					// pole celli sees ja jutum2rk. j2relikult algab quoted cell
					$in_cell = true;
					$line.=$filestr[$pos];
				}
				else
				if ($in_cell == true && ($filestr[$pos+1] == $separator || $filestr[$pos+1] == "\n" || $filestr[$pos+1] == "\r"))
				{
					// celli sees ja jutum2rk ja j2rgmine on kas semikas v6i reavahetus, j2relikult cell l6peb
					$in_cell = false;
					$line.=$filestr[$pos];
				}
				else
				{
					// dubleeritud jutum2rk
					$line.=$filestr[$pos];
				}
			}
			else
			if ($filestr[$pos] == $separator && $in_cell == false)
			{
				// semikas t2histab celli l6ppu aint siis, kui ta pole jutum2rkide vahel
				$in_cell = false;
				$line.=$filestr[$pos];
			}
			else
			if (($filestr[$pos] == "\n" || $filestr[$pos] == "\r") && $in_cell == false)
			{
				// kui on reavahetus ja me pole quotetud celli sees, siis algab j2rgmine rida

				// clearime j2rgneva l2bu ka 2ra
				if ($filestr[$pos+1] == "\n" || $filestr[$pos+1] == "\r")
					$pos++;
				$linearr[] = $line;
				$line = "";
			}
			else
			{
				$line .= $filestr[$pos];
			}
		}

		if (trim($line) != "")
		{
			$linearr[] = $line;
		}
		return $linearr;
	}

	function is_csv($url)
	{
		$fc = file_get_contents($url);
		if (strpos($fc, "onLoad") !== false || strpos($fc, "javascript") !== false)
		{
			return false;
		}
		return true;
	}

	function _init_folders_tbl(&$t)
	{
		$t->define_field(array(
			"name" => "pgs",
			"caption" => t("Lehed komaga eraldatult"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "awfld",
			"caption" => t("AW Kataloogi ID"),
			"align" => "center"
		));
	}

	function do_folders_tbl($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_folders_tbl($t);

		$data = $this->_get_fld_dat();

		$cnt = 1;
		foreach($data as $fld => $row)
		{
			if (!$fld)
			{
				continue;
			}
			$t->define_data(array(
				"pgs" => html::textbox(array(
					"name" => "dat[$cnt][pgs]",
					"value" => join(",", $row),
					"size" => "80"
				)),
				"awfld" => html::textbox(array(
					"name" => "dat[$cnt][awfld]",
					"value" => $fld,
					"size" => "10"
				)),
			));
			$cnt++;
		}
		$t->define_data(array(
			"pgs" => html::textbox(array(
				"name" => "dat[$cnt][pgs]",
				"value" => "",
				"size" => "80"
			)),
			"awfld" => html::textbox(array(
				"name" => "dat[$cnt][awfld]",
				"value" => "",
				"size" => "10"
			)),
		));

		$t->set_sortable(false);
	}

	function _get_fld_dat()
	{
		$ret = array();
		$this->db_query("SELECT * FROM otto_imp_t_p2p WHERE lang_id = ".aw_global_get("lang_id"));
		while ($row = $this->db_next())
		{
			$ret[$row["fld"]][] = $row["pg"];
		}
		return $ret;
	}

	function otto_picture_import($arr)
	{
		$sites = $arr['import_obj']->meta("files_import_sites_order");
		$picture_found = false;
		foreach ($sites as $site => $order)
		{
			switch ($site)
			{
				case "otto":
				//	$picture_found = $this->read_img_from_otto($arr);
					$picture_found = $this->get_images_from_otto($arr);
					break;
				case "heine":
					$picture_found = $this->read_img_from_heine($arr);
					break;
				case "schwab":
					$picture_found = $this->read_img_from_schwab($arr);
					break;
				case "albamoda":
				// lets comment this thing out for now cause it gave some db error
				// and searching from albamoda doesn't work at the moment anyway --dragut@19.08.2009
				//	$picture_found = $this->read_img_from_albamoda($arr);
					break;
				case "baur":
					$picture_found = $this->read_img_from_baur($arr);
					break;
			}
			if ($picture_found !== false)
			{
				break;
			}
		}
		// TODO have to refactor this place here
		return $picture_found;
	}

	function read_img_from_otto($arr)
	{
		$return_images = array();

		$pcode = $arr['pcode'];
		$import_obj = $arr['import_obj'];
		$start_time = $arr['start_time'];

		$url = "http://www.otto.de/is-bin/INTERSHOP.enfinity/WFS/Otto-OttoDe-Site/de_DE/-/EUR/OV_ViewFHSearch-Search;sid=JV7cfTuwQAxofX1y7nscFVe673M6xo8CrLL_UKN1wStaXWmvgBB3ETZoVkw_5Q==?ls=0&commit=true&fh_search=$pcode&fh_search_initial=$pcode&stype=N";

		echo "[ OTTO ] Loading <a href=\"$url\">page</a> content ... ";
		flush();

		$html = $this->file_get_contents($url);
	//	arr(htmlentities($html));
		echo "[ OK ]<br />\n";
		flush();

		// image is http://image01.otto.de:80/pool/OttoDe/de_DE/images/formatb/[number].jpg

		if (strpos($html,"Leider konnten wir") !== false)
		{
			echo "[ OTTO ] Can't find an product for <b>$pcode</b> from otto.de, return false<br>\n";
			return false;
		}
		else
		{
			$o_html = $html;


			// just for not, this kind of search happens when one searches for a product code
			// it seems, that if the search is made by string (for example 'bikini'), then there won't be such
			// javascript transition page and it is shown just a list of products, so in that case it should be handled
			// differently. But maybe there won't be such a case and therefore let it be for now.
			preg_match_all("/function goon\(\) \{(.*)\}/imsU", $html, $mt, PREG_PATTERN_ORDER);
			$js_code = $mt[1][0];

			$pattern = "/\" \+ encodeURIComponent\(\"(.*)\"\)/U";

			preg_match_all($pattern, $js_code, $m);
			foreach ($m[0] as $k => $v)
			{
			        $js_code = str_replace($m[0][$k], urlencode($m[1][$k]).'"', $js_code);
			}
			$pattern = "/\"(.*)\"/U";
			preg_match_all($pattern, $js_code, $m);

			$urld[] = implode('', $m[1]);
/*
			$urld = array();
			foreach($mt[1] as $url)
			{
				$urld[$url] = $url;
			}
*/

			foreach($urld as $url)
			{
				echo "[ OTTO ] Searching pictures from <a href=\"$url\">url</a> <br />\n";
				$html = $this->file_get_contents($url);

				if (!preg_match_all("/<img id=\"mainimage\" src=\"(.*)\.jpg\"/imsU", $html, $mt, PREG_PATTERN_ORDER))
				{
					echo "[ OTTO ] If we can't find image from otto.de product view, then return false <br />\n";
/*
	XXX
	This is weird here actually - it breaked the loop previously, and now it should return false ... but
	let's consider the situation, where multiple products where found and the loop actually could look
	through multiple products, then maybe from some next product it does find the images?
	But with previous functionality and and current one it just breaks the loop all together when it doesn't
	find images ... I'll check back here later --dragut 19.02.2008
*/
					return false;
				}

				$f_imnr = NULL;


				// we need that connecting picture:
				$connection_image = '';
				$pattern = "/<img width=.* title=.* src=\"http:\/\/image01\.otto\.de:80\/pool\/ov_formatg\/(.*)\.jpg\"/imsU";
				if (preg_match($pattern, $html, $matches ))
				{
					$connection_image = $matches[1];
					if (!empty($connection_image))
					{
						// NEW for new import
						$return_images[] = 'http://image01.otto.de:80/pool/formata/'.$connection_image.'.jpg';

						echo "[ OTTO ] Siduv pilt ";
						$image_ok = $this->get_image(array(
							'source' => 'http://image01.otto.de:80/pool/formatb/'.$connection_image.'.jpg',
							'format' => SMALL_PICTURE,
							'otto_import' => $import_obj,
							'debug' => true
						));
						if ($image_ok)
						{

							$image_ok = $this->get_image(array(
								'source' => 'http://image01.otto.de:80/pool/formata/'.$connection_image.'.jpg',
								'format' => BIG_PICTURE,
								'otto_import' => $import_obj,
								'debug' => true
							));
						}

						if ($image_ok)
						{
							echo "<a href=\"http://image01.otto.de:80/pool/formatb/".$connection_image.".jpg\">small image</a> / ";
							echo "<a href=\"http://image01.otto.de:80/pool/formata/".$connection_image.".jpg\">big image</a><br />\n";
						}
						else
						{
							echo "Couldn't save the connection image.<br />\n";
						}
					}
				}

				foreach($mt[1] as $idx => $img)
				{
					if (strpos($img, 'leer.gif') !== false )
					{
						echo "[ OTTO ] tundub, et sellele variandile pilti ei ole <br>\n";
						continue;
					}
					$imnr = basename($img, ".jpg");

					if (file_get_contents(str_replace($imnr, $imnr.'.jpg', $img)) === false)
					{
						echo "[ OTTO ] selle variandi pilti ei &otilde;nnestu k&auml;tte saada<br />\n";
						continue;
					}

					echo "[ OTTO ] ".$imnr."<br>\n";
					echo "[ OTTO ] image from product $pcode : ($t_imnr)<br />";
					$q = "SELECT pcode FROM otto_prod_img WHERE imnr = '$imnr' AND nr = '".$mt[2][$idx]."' AND pcode = '$pcode'";
					$t_imnr = $this->db_fetch_field($q, "pcode");

					if (!$f_imnr)
					{
						$f_imnr = $t_imnr.".jpg";
					}

					if (!$t_imnr)
					{
						echo "[ OTTO ] insert new image $imnr <br />\n";
						flush();

						$q = ("
							INSERT INTO
								otto_prod_img(pcode, nr,imnr, server_id, conn_img)
								values('$pcode','".$mt[2][$idx]."','$imnr', 1, '$connection_image')
						");
						$this->db_query($q);
						$this->added_images[] = $mt[2][$idx];
					}
					else
					{
						$this->db_query("
							update
								otto_prod_img
							set
								conn_img = '".$connection_image."'
							where
								imnr = '".$imnr."' and
								nr = '".$mt[2][$idx]."' and
								pcode = '".$pcode."'
						");
						echo "[ OTTO ] image $imnr for product $pcode is already in db<br />\n";
						flush();
					}

					$image_ok = $this->get_image(array(
						'source' => 'http://image01.otto.de:80/pool/formatb/'.$imnr.'.jpg',
						'format' => SMALL_PICTURE,
						'otto_import' => $import_obj,
						'debug' => true
					));

					// NEW for new import
					$return_images[] = 'http://image01.otto.de:80/pool/formata/'.$imnr.'.jpg';

					// download the big version of the image too:
					$this->get_image(array(
						'source' => 'http://image01.otto.de:80/pool/formata/'.$imnr.'.jpg',
						'format' => BIG_PICTURE,
						'otto_import' => $import_obj,
						'debug' => true
					));
					if ($image_ok)
					{
						// download the big version of the image too:
						$this->get_image(array(
							'source' => 'http://image01.otto.de:80/pool/formata/'.$imnr.'.jpg',
							'format' => BIG_PICTURE,
							'otto_import' => $import_obj,
							'debug' => true
						));
					}
					else
					{
						/////
						// some pictures are coming from different URL-s, so if we get 0 sized image ($image_ok === false)
						// lets try some other places to get the image:
						$image_ok = $this->get_image(array(
							'source' => 'http://image01.otto.de/pool/ov_formatd/'.$imnr.'.jpg',
							'format' => SMALL_PICTURE,
							'otto_import' => $import_obj,
							'debug' => true
						));

						if ($image_ok)
						{
							// download the big version of the image too:
							$this->get_image(array(
								'source' => 'http://image01.otto.de:80/pool/formata/'.$imnr.'.jpg',
								'format' => BIG_PICTURE,
								'otto_import' => $import_obj,
								'debug' => true
							));
						}
					}
				}

				// check for rundumanshiftph (flash)
				if (strpos($html, "rundum_ansicht") !== false)
				{
					echo "[ OTTO ] video ";

					$pattern = "/'".preg_quote("http://www.otto.de/is-bin/INTERSHOP.enfinity/WFS/Otto-OttoDe-Site/de_DE/-/EUR/OV_DisplayProductInformation-SuperZoom3D;", "/").".*'/imsU";
					preg_match_all($pattern, $html, $mt, PREG_PATTERN_ORDER);
					$popup_url = str_replace("'", "", $mt[0][0].$f_imnr);
					echo " - from <a href=\"".$popup_url."\">url</a>";

					// get the rundum image number from the popup :(
					$r_html = file_get_contents($popup_url);

					// save rundum
					// get rundum imnr from html
					preg_match_all("/writeFlashCode_superzoom3d\('(.*)'\);/imsU", $r_html, $mt, PREG_PATTERN_ORDER);

					$flash_file_urls = $mt[1];
					foreach ($flash_file_urls as $flash_file_url)
					{
						$flash_file_name = basename($flash_file_url);

						$flash_file_url .= '.swf';


						// NEW for new import
						$return_images[] = $flash_file_url;

						$video_download_result = $this->get_video(array(
							'source' => $flash_file_url,
							'otto_import' => $import_obj,
							'overwrite' => true
						));
						if ($video_download_result !== false)
						{
							echo " | <a href='".$flash_file_url."'>".$flash_file_name."</a>";
							$this->db_query("update otto_prod_img set video = '".addslashes(strip_tags($flash_file_name))."' where pcode = '".$pcode."'");
						}
						else
						{
							echo " | failed to get videofile!";
						}
					}
					echo "<br /> \n";
				}
			}
		}
	//	return true;
		return $return_images;
	}

	function get_images_from_otto($arr)
	{
		$return_images = array();

		$full_pcode = $arr['pcode'];
		$pcode = substr($arr['pcode'], 0, 6);
		$import_obj = $arr['import_obj'];
		$start_time = $arr['start_time'];
		echo "[ OTTO ] Searching images for product code <strong>".$pcode." </strong>(code length: ".strlen($pcode)." / full code length: ".strlen($full_pcode).")<br />\n";
		$url = "http://www.otto.de/is-bin/INTERSHOP.enfinity/WFS/Otto-OttoDe-Site/de_DE/-/EUR/OV_ViewFHSearch-Search;sid=JV7cfTuwQAxofX1y7nscFVe673M6xo8CrLL_UKN1wStaXWmvgBB3ETZoVkw_5Q==?ls=0&commit=true&fh_search=$pcode&fh_search_initial=$pcode&stype=N";

		echo "[ OTTO ] Loading <a href=\"$url\">page</a> content ... ";
		flush();

		$html = $this->file_get_contents($url);

		echo "[ OK ]<br />\n";
		flush();

		// image is http://image01.otto.de:80/pool/OttoDe/de_DE/images/formatb/[number].jpg

		if (strpos($html,"Leider konnten wir") !== false)
		{
			echo "[ OTTO ] Can't find an product for <b>$pcode</b> from otto.de, return false<br>\n";
			return false;
		}
		else
		{
			$o_html = $html;

			// just for not, this kind of search happens when one searches for a product code
			// it seems, that if the search is made by string (for example 'bikini'), then there won't be such
			// javascript transition page and it is shown just a list of products, so in that case it should be handled
			// differently. But maybe there won't be such a case and therefore let it be for now.
			preg_match_all("/function goon\(\) \{(.*)\}/imsU", $html, $mt, PREG_PATTERN_ORDER);
			$js_code = $mt[1][0];

			$pattern = "/\" \+ encodeURIComponent\(\"(.*)\"\)/U";

			preg_match_all($pattern, $js_code, $m);
			foreach ($m[0] as $k => $v)
			{
			        $js_code = str_replace($m[0][$k], urlencode($m[1][$k]).'"', $js_code);
			}
			$pattern = "/\"(.*)\"/U";
			preg_match_all($pattern, $js_code, $m);

			$urld[] = implode('', $m[1]);

			foreach($urld as $url)
			{
				echo "[ OTTO ] Searching pictures from <a href=\"$url\">url</a> <br />\n";
				$html = $this->file_get_contents($url);

				if (!preg_match_all("/<img id=\"mainimage\" src=\"(.*)\.jpg\"/imsU", $html, $mt, PREG_PATTERN_ORDER))
				{
					echo "[ OTTO ] If we can't find image from otto.de product view, then return false <br />\n";
					return false;
				}

				// we need that connecting picture:
				$connection_image = '';
				$pattern = "/<img width=.* title=.* src=\"http:\/\/image01\.otto\.de:80\/pool\/ov_formatg\/(.*)\.jpg\"/imsU";
				if (preg_match($pattern, $html, $matches ))
				{
					$connection_image = $matches[1];
					if (!empty($connection_image))
					{
						$return_images[] = 'http://image01.otto.de:80/pool/formata/'.$connection_image.'.jpg';
					}
				}

				foreach($mt[1] as $idx => $img)
				{
					if (strpos($img, 'leer.gif') !== false )
					{
						echo "[ OTTO ] tundub, et sellele variandile pilti ei ole <br>\n";
						continue;
					}
					$imnr = basename($img, ".jpg");

					if (file_get_contents(str_replace($imnr, $imnr.'.jpg', $img)) === false)
					{
						echo "[ OTTO ] selle variandi pilti ei &otilde;nnestu k&auml;tte saada<br />\n";
						continue;
					}

					echo "[ OTTO ] Image name: <strong>".$imnr."</strong><br>\n";

					// NEW for new import
					if (file_get_contents('http://image01.otto.de:80/pool/formata/'.$imnr.'.jpg') === false)
					{
						$return_images[] = "http://image02.otto.de/pool/ov_formatg/".$imnr.".jpg";
					}
					else
					{
						$return_images[] = 'http://image01.otto.de:80/pool/formata/'.$imnr.'.jpg';
					}
				}

				// check for rundumanshiftph (flash)
				if (strpos($html, "rundum_ansicht") !== false)
				{
					echo "[ OTTO ] video ";

					$pattern = "/'".preg_quote("http://www.otto.de/is-bin/INTERSHOP.enfinity/WFS/Otto-OttoDe-Site/de_DE/-/EUR/OV_DisplayProductInformation-SuperZoom3D;", "/").".*'/imsU";
					preg_match_all($pattern, $html, $mt, PREG_PATTERN_ORDER);
					$popup_url = str_replace("'", "", $mt[0][0].$f_imnr);
					echo " - from <a href=\"".$popup_url."\">url</a>";

					// get the rundum image number from the popup :(
					$r_html = file_get_contents($popup_url);

					// save rundum
					// get rundum imnr from html
					preg_match_all("/writeFlashCode_superzoom3d\('(.*)'\);/imsU", $r_html, $mt, PREG_PATTERN_ORDER);

					$flash_file_urls = $mt[1];
					foreach ($flash_file_urls as $flash_file_url)
					{
						$flash_file_name = basename($flash_file_url);

						$flash_file_url .= '.swf';

						// NEW for new import
						$return_images[] = $flash_file_url;
					}
					echo "<br /> \n";
				}
			}
		}

		return $return_images;
	}
	function read_img_from_baur($arr)
	{
		$pcode = str_replace(" ", "", $arr['pcode']);
		$import_obj = $arr['import_obj'];

		$url = "http://suche.baur.de/servlet/weikatec.search.SearchServletMmx?ls=0&source=&resultsPerPage=99&searchandbrowse=&category2=&query=".$pcode."&category=";

		echo "[ BAUR ] Loading <a href=\"$url\">page</a> content ";
		$fc = $this->file_get_contents($url);
		echo " [ok]<br />\n";
//		if (strpos($fc, "leider keine Artikel gefunden") !== false)
		if ( (strpos($fc, "search/topcontent/noresult_slogan.gif") !== false) || (strpos($fc, "Entschuldigung,<br>diese Seite konnte nicht gefunden werden.") !== false) || true) // xxx disable baur import for now
		{
			echo "[ BAUR ] Can't find a product for <b>$pcode</b> from baur.de, so return false<br>\n";
			return false;

		}

		preg_match_all("/redirectIt\( \"(.*)\" \)/ims", $fc, $mt, PREG_PATTERN_ORDER);

		$pcs = array_unique($mt[1]);
		foreach($pcs as $n_pc)
		{
			$url2 = "http://www.baur.de/is-bin/INTERSHOP.enfinity/WFS/BaurDe/de_DE/-/EUR/BV_DisplayProductInformation-ProductRef;sid=vawch68xzhk1fe62PgtM0m08zJ5byxprRr3IpZL-?ls=0&ProductRef=".$n_pc."&SearchBack=true&SearchDetail=true";
			$fc = $this->file_get_contents($url2);

			preg_match_all("/http\:\/\/image01(.*)jpg/imsU", $fc, $mt, PREG_PATTERN_ORDER);
			$pics = array_unique($mt[0]);
			$fp = basename($pics[0], ".jpg");

			preg_match("/OpenPopUpZoom\('\d*','\d*','(.*)'\)/imsU", $fc, $mt);
			$popurl = $mt[1];

			$fc_p = $this->file_get_contents($popurl);

			preg_match("/<frame name=\"_popcont\" src=\"(.*)\"/imsU", $fc_p, $mt);
			$contenturl = $mt[1];

			$fc_c = $this->file_get_contents($contenturl);

			preg_match_all("/http\:\/\/image01(.*)jpg/imsU", $fc_c, $mt, PREG_PATTERN_ORDER);
			$pics = array_unique($mt[0]);

			$pa = array($fp => $fp);
			foreach($pics as $pic)
			{
				$tmp = basename($pic, ".jpg");
				$pa[$tmp] = $tmp;
			}

			// now pa contains all images for this one.

			$cnt = 1;
			// insert images in db
			foreach($pa as $pn)
			{
				$image_ok = $this->get_image(array(
					'source' => 'http://image01.otto.de/pool/BaurDe/de_DE/images/formatb/'.$pn.'.jpg',
					'format' => 2,
					'otto_import' => $import_obj,
					'debug' => true
				));
				if ($image_ok)
				{
					// download the big version of the image too:
					$this->get_image(array(
						'source' => 'http://image01.otto.de/pool/BaurDe/de_DE/images/formatb/'.$pn.'.jpg',
						'format' => 1,
						'otto_import' => $import_obj,
						'debug' => true
					));
				}

				// check if the image combo already exists
				$imnr = $this->db_fetch_field("SELECT pcode FROM otto_prod_img WHERE imnr = '$pn' AND nr = '$cnt' AND pcode = '$pcode'", "pcode");
				if (!$imnr)
				{
					echo "[ BAUR ] insert new image $pn <br>\n";
					flush();
					$q = ("
						INSERT INTO
							otto_prod_img(pcode, nr,imnr, server_id)
							values('$pcode','$cnt','$pn', 2)
					");
					//echo "q = $q <br>";
					$this->db_query($q);
					$this->added_images[] = $pn;
				}
				else
				{
					echo "[ BAUR ] existing image $pn <br>\n";
				}
				$cnt++;
			}
		}
	}

	/**

		@attrib name=swt

		@param pcode required

	**/
	function swt($arr)
	{
		return $this->pictimp(false,false);
	}

	function read_img_from_schwab($arr)
	{
		echo "[ SCHWAB ] - product search url is changed <br />\n";
		return false;
		$pcode = $arr['pcode'];
		$import_obj = $arr['import_obj'];

		$url = "http://suche.schwab.de/Schwab/Search.ff?query=".$pcode;
		echo "[ SCHWAB ] Loading <a href=\"$url\">page</a> content ";
		$fc = $this->file_get_contents($url);
		echo "[ok]<br />\n";
		if (strpos($fc, "Keine passenden Ergebnisse f".chr(252)."r:") !== false)
		{
			echo "[ SCHWAB ] can't find a product for <b>$pcode</b> from schwab.de, so returning false<br>\n";
			return false;
		}

		// match prod urls
	//	preg_match_all("/ProductRef=(.*)&/imsU", $fc, $mt, PREG_PATTERN_ORDER);
	//	$pcs = array_unique($mt[1]);
	//	echo "[schwab] got pcs as ".dbg::dump($pcs)."\n";

	// I assume that this is not exactly needed, cause I have the product code already
	//	preg_match("/query: '(.*)'/", $fc, $mt);
	//	$pcode = $mt[1];

	// It doesn't have the the support for multiple products in search result right now.
	// Because:
	//	a) I assume, that product code is products identifier, so one code == one product
	//	b) I don't have a test case at the moment where there are multiple products/pictures in search result
	//		and I don't know how it looks like in html source
		preg_match("/articleId: '(.*)'/", $fc, $mt);
		$articleId = $mt[1];

		$product_url = "http://www.schwab.de/is-bin/INTERSHOP.enfinity/WFS/Schwab-SchwabDe-Site/de_DE/-/EUR/SV_DisplayProductInformation-SearchDetail?ls=0&query=".$pcode."&ArticleNo=".$articleId;

		// ok, lets keep this loop for now, maybe in real life examples there will be multiple products/pictures in search results:
		$pcs = array(
			0 => $product_url
		);

		foreach($pcs as $prod_url)
		{
			echo "[ SCHWAB ] product <a href=\"$prod_url\">url</a>: <br />\n";
			$fc2 = $this->file_get_contents($prod_url);

			// get first image
			preg_match("/http:\/\/image01\.otto\.de:80\/pool\/formatb\/(\d+).jpg/imsU", $fc2, $mt);
			$first_im = $mt[1];

			$image_ok = $this->get_image(array(
				'source' => 'http://image01.otto.de/pool/formatb/'.$first_im.'.jpg',
				'format' => 2,
				'otto_import' => $import_obj,
				'debug' => true
			));
			if ($image_ok)
			{
				// download the big version of the image too:
				$this->get_image(array(
					'source' => 'http://image01.otto.de/pool/formata/'.$first_im.'.jpg',
					'format' => 1,
					'otto_import' => $import_obj,
					'debug' => true
				));
			}

			$imnr = $this->db_fetch_field("SELECT pcode FROM otto_prod_img WHERE imnr = '$first_im' AND nr = '1' AND pcode = '$pcode'", "pcode");
			if (!$imnr)
			{
				echo "[ SCHWAB ] insert new image $first_im <br>\n";
				flush();
				$q = ("
					INSERT INTO
						otto_prod_img(pcode, nr,imnr, server_id)
						values('$pcode','1','$first_im', 3)
				");
				//echo "q = $q <br>";
				$this->db_query($q);
				$this->added_images[] = $first_im;
			}

// apparently there seems to be no other images ...
// again, if in real life there actually are, then lets keep the possibility for now:
/*
			// get other images
			preg_match_all("/jump_img\('(\d+)'\)/imsU", $fc2, $mt, PREG_PATTERN_ORDER);
			$otherim = $mt[1];

			foreach($otherim as $nr)
			{
				$o_url = $prod_url."&bild_nr=".$nr;
				$fc3 = $this->file_get_contents($o_url);

				preg_match("/http:\/\/image01\.otto\.de:80\/pool\/formatb\/(\d+).jpg/imsU", $fc3, $mt);
				$im = $mt[1];

				$imnr = $this->db_fetch_field("SELECT pcode FROM otto_prod_img WHERE imnr = '$im' AND nr = '$nr' AND pcode = '$pcode'", "pcode");
				if (!$imnr)
				{
					echo "[ SCHWAB ] insert new image $im <br>\n";
					flush();
					$q = ("
						INSERT INTO
							otto_prod_img(pcode, nr,imnr, server_id)
							values('$pcode','$nr','$im', 3)
					");
					$this->db_query($q);
					$this->added_images[] = $im;
				}
			}
*/
		}
		return true;
	}

	function read_img_from_albamoda($arr)
	{
return false;
		$pcode = $arr['pcode'];
		$import_obj = $arr['import_obj'];

		$url = "http://suche.albamoda.de/servlet/SearchServlet?clientId=AlbaModa-AlbaModaDe-Site&query=".$pcode."&resultsPerPage=120&category=&color=&manufacturer=&minPrice=&maxPrice=&prodDetailUrl=http%3A//www.albamoda.de/is-bin/INTERSHOP.enfinity/WFS/AlbaModa-AlbaModaDe-Site/de_DE/-/EUR/AM_ViewProduct-ProductRef%3Bsid%3DYxKYQ1BufUk5QxZUapu1Y0vC4a2r_3Im9-K6C8SemFURf8RYYg66C8SeC-oUEg%3D%3D%3Fls%3D%26ProductRef%3D%253CSKU%253E%2540AlbaModa-AlbaModaDe%26SearchBack%3D-1%26SearchDetail%3Dtrue";

		echo "[ ALBAMODA ] Loading <a href=\"$url\">page</a> content ";
		$fc = $this->file_get_contents($url);
		echo "[ok]<br />\n";


		if (strpos($fc, "Es wurden leider keine Artikel gefunden.") !== false)
		{
			echo "[ ALBAMODA ] can't find a product for code <b>$pcode</b> from albamoda.de, so return false<br>\n";
			return false;
		}

		// if we found only one product, then the user is redirected directly to the products page
		// if multiple products were found, then I need collect all the urls to products
		if (strpos($fc, "<!-- redirect_proddetail.vm -->") !== false)
		{
			// one product, lets get the url where the user is redirected to
			preg_match("/var url = \"(.*)\"/imsU", $fc, $mt);
			$url = $mt[1];
			echo "[ ALBAMODA ] One product found: <a href=\"$url\">page</a> <br />\n";
			$prod_urls = array($mt[1]);
		}
		else
		{
			// multiple products, lets collect all the product urls from the search results
			$pattern = "/<a href=\"(http\:\/\/.*)\">/i";
			preg_match_all($pattern, $fc, $mt, PREG_PATTERN_ORDER);
			$prod_urls = array_unique($mt[1]);
			echo "[ ALBAMODA ] Found ".count($prod_urls)." products: ";
			$prod_count = 0;
			foreach ($prod_urls as $prod_url)
			{
				$prod_count++;
				echo " <a href=\"".$prod_url."\">[ ".$prod_count." ]</a> ";
			}
			echo "<br />\n";
		}


		foreach($prod_urls as $url)
		{
			$fc = $this->file_get_contents($url);
			echo "<br />\n";

			echo "[ ALBAMODA ] Loading product <a href=\"$url\">page</a> <br />\n";

			// apparently some products are broken in albamoda as well, so lets chek if we got a product from this url at the first place:
			if (strpos($fc, "Liebe Kundin, lieber Kunde.") !== false)
			{
				echo "[ ALBAMODA ] Product page seems to be unavailable <a href=\"".$url."\">[ url ]</a><br />\n";
				continue;
			}

			$pattern = "/SRC=\"http\:\/\/image01\.otto\.de\:80\/pool\/albamoda_formatK\/(.*)\.jpg\"/U";
			preg_match_all($pattern, $fc, $mt, PREG_PATTERN_ORDER);

			$image_name = $mt[1][0];

			echo "[ ALBAMODA ] Image files: ";

			$small_image_src = 'http://image01.otto.de:80/pool/albamoda_formatL/'.$image_name.'.jpg';
			$small_image_ok = $this->get_image(array(
				'source' => $small_image_src,
				'format' => SMALL_PICTURE,
				'otto_import' => $import_obj,
				'debug' => true
			));

			if ($small_image_ok)
			{
				echo "<a href=\"".$small_image_src."\">[ Small image ]</a> ";
			}
			else
			{
				echo "<a href=\"".$small_image_src."\">[ Getting small image failed ]</a> ";
			}

			// download the big version of the image too:
			$big_image_src = 'http://image01.otto.de:80/pool/albamoda_formatK/'.$image_name.'.jpg';
			$big_image_ok = $this->get_image(array(
				'source' => $big_image_src,
				'format' => BIG_PICTURE,
				'otto_import' => $import_obj,
				'debug' => true
			));

			if ($big_image_ok)
			{
				echo "<a href=\"".$big_image_src."\">[ Big image ]</a> ";
			}
			else
			{
				echo "<a href=\"".$big_image_src."\">[ Getting big image failed ]</a> ";
			}

			echo " <br />\n";

			$imnr = $this->db_fetch_field("SELECT pcode FROM otto_prod_img WHERE imnr = '$image_name' AND nr = '1' AND pcode = '$pcode'", "pcode");
			if (!$imnr)
			{
				echo "[ ALBAMODA ] Database: insert new image $image_name <br>\n";
				flush();
				$q = ("
					INSERT INTO
						otto_prod_img(pcode, nr,imnr, server_id)
						values('$pcode','1','$image_name', 4)
				");
				$this->db_query($q);
			// seems it is not used, so lets start to remove this to get removed unused code -- dragut@10.03.2008
			//	$this->added_images[] = $image_name;
			}
			else
			{
				echo "[ ALBAMODA ] Database: Image [".$image_name."] is already in database <br />\n";
			}

			////
			// Albamoda does have videos as well, so lets improt them too:

			$pattern = "/so\.addVariable\(\"flv_file\", \"(.*\.flv)\"\);/";
			preg_match($pattern, $fc, $mt);
			$file_url = $mt[1];
			if (!empty($file_url))
			{
				$parts = explode('/', $file_url);
				$filename = $parts[count($parts) - 2];

				$video_download_result = $this->get_video(array(
					'source' => $file_url,
					'filename' => $filename.'.flv',
					'otto_import' => $import_obj
				));
				if ($video_download_result !== false)
				{
					echo "[ ALBAMODA ] Video: <a href=\"".$file_url."\">".$filename."</a><br />\n";
					$this->db_query("update otto_prod_img set video = '".addslashes(strip_tags($filename)).".flv' where pcode = '".$pcode."'");
				}
			}
		}
		return true;
	}

	function read_img_from_heine($arr)
	{
		// This heine import works somewhat weird way, will look at it later
		return false;


		$pcode = $arr['pcode'];
		$import_obj = $arr['import_obj'];
		$product_page_urls = array();

		// no spaces in product code ! --dragut
		$pcode = str_replace(" ", "", $pcode);

	//	$url = "http://search.heine.de/Heine/Search.ff?query=".$pcode;
		$url = "http://www.heine.de/is-bin/INTERSHOP.enfinity/WFS/Heine-HeineDe-Site/de_DE/-/EUR/ViewProductSearch-Search;sid=FPzpHV5oa6GkHRTr4Hn03ws4Wg19UWgvT-9O9HYx0YFE1ZEVxF5O9HYxlOi83Q==?query=$pcode&host=www.heine.de#lmPromo=la,1,hk,sh_home,fl,sh_home_header_suchen";
		echo "[ HEINE ] Loading <a href=\"$url\">page</a> content ... ";
		flush();

		$fc = $this->file_get_contents($url);
		echo "[ok]<br />\n";
		flush();

		if (preg_match("/top\.location\.href\=\"(.*)\";/imsU", $fc, $mt))
		{
			$url = $mt[1];
			echo "[ HEINE ] getting redirect <a href='$url'>url</a> to product page ... ";
			flush();

		//	$fc = $this->file_get_contents($url);
			$product_page_urls[] = $url;
			echo "[ok]<br />\n";
			flush();
		}
		else
		{
			// v6imalik, et leiti mitu toodet, seega on meil neid k6iki vaja, et sealt pildid kokku otsida ...
			$pattern = "/\"".preg_quote("http://www.heine.de/is-bin/INTERSHOP.enfinity/WFS/Heine-HeineDe-Site/de_DE/-/EUR/SH_ViewProduct-ArticleNo", "/").".*\"/imsU";
			preg_match_all($pattern, $fc, $mt, PREG_PATTERN_ORDER);
			foreach ($mt[0] as $url)
			{
				$url = str_replace("\"", "", $url);
				$url = str_replace(" ", "%20", $url);
				if ( array_search($url, $product_page_urls) === false )
				{
					echo "[ HEINE ] getting product page <a href='$url'>url</a> ... ";
					$product_page_urls[] = $url;
					echo "[ok]<br />\n";
					flush();
				}
			}
		}

		$found_image = false;
		foreach ($product_page_urls as $url)
		{
			$fc = $this->file_get_contents($url);

			if (strpos($fc, "Sie sind auf der Suche nach etwas Besonderem?") !== false)
			{
				echo "[ HEINE ] Can't find product for code $pcode from page <a href=\"$url\">$url</a><br />\n";
				flush();
				continue;
			}


			// connection image ... xxx to fix!
			$connection_image = '';
			if (preg_match("/ImageBundle = (\d+).jpg/", $fc, $mt))
			{
				$connection_image = $mt[1];
				echo "[ HEINE ] salvestan seose pildi $connection_image ";
				echo "[ <a href=\"http://image01.otto.de/pool/format_hv_ds_b/".$connection_image.".jpg\">v&auml;ike</a> ";
				echo "| <a href=\"http://image01.otto.de/pool/format_hv_ds_a/".$connection_image.".jpg\">suur</a> ]<br />\n";
				$image_ok = $this->get_image(array(
					'source' => 'http://image01.otto.de/pool/format_hv_ds_b/'.$connection_image.'.jpg',
					'format' => 2,
					'otto_import' => $import_obj,
					'debug' => true
				));
				if ($image_ok)
				{
					// download the big version of the image too:
					$this->get_image(array(
						'source' => 'http://image01.otto.de/pool/format_hv_ds_a/'.$connection_image.'.jpg',
						'format' => 1,
						'otto_import' => $import_obj,
						'debug' => true
					));
				}
			}


			$patterns = array(
				"/bild\[bildZahl\+\+\]\=\"(\d+).jpg\";/imsU",
			);

			foreach ($patterns as $pattern)
			{
				if (preg_match($pattern, $fc, $mt))
				{
					break;
				}
			}

			if (empty($mt))
			{
				continue;
			}

			$first_im = $mt[1];
			echo "[ HEINE ] salvestan pildi $first_im [ <a href=\"http://image01.otto.de/pool/format_hv_ds_b/".$first_im.".jpg\">v&auml;ike</a> | <a href=\"http://image01.otto.de/pool/format_hv_ds_a/".$first_im.".jpg\">suur</a> ]<br />\n";
			$image_ok = $this->get_image(array(
				'source' => 'http://image01.otto.de/pool/format_hv_ds_b/'.$first_im.'.jpg',
				'format' => 2,
				'otto_import' => $import_obj,
				'debug' => true
			));
			if ($image_ok)
			{
				// download the big version of the image too:
				$this->get_image(array(
					'source' => 'http://image01.otto.de/pool/format_hv_ds_a/'.$first_im.'.jpg',
					'format' => 1,
					'otto_import' => $import_obj,
					'debug' => true
				));
			}

			$imnr = $this->db_fetch_field("SELECT pcode FROM otto_prod_img WHERE imnr = '$first_im' AND nr = '1' AND pcode = '$pcode'", "pcode");
			if (!$imnr)
			{
				echo "[ HEINE ] lisan uue pildi \"".$first_im."\" tootele \"".$pcode."\" piltide andmebaasi <br />\n";
				flush();

				$q = ("
					INSERT INTO
						otto_prod_img(pcode, nr,imnr, server_id, conn_img)
						values('$pcode','1','$first_im', 5, '$connection_image')
				");

				$this->db_query($q);
				$this->added_images[] = $first_im;
			}
			else
			{
				$this->db_query("
					update
						otto_prod_img
					set
						conn_img = '".$connection_image."'
					where
						imnr = '".$first_im."' and
						pcode = '".$pcode."'
				");
				echo "[ HEINE ] pilt \"". $first_im ."\" tootele \"". $pcode ."\" on piltide andmebaasis juba olemas <br />\n";
			}
			$found_image = true;
		}

		// if images has been found, it should return true, false othervise
		return $found_image;

	}

	function file_get_contents($url)
	{
		for($i = 0; $i < 3; $i++)
		{
			$fc = @file_get_contents($url);
			if ($fc != "")
			{
				return $fc;
			}
		}
		echo "SITE $url seems to be <font color=red>DOWN</font> <br>\n";
		flush();
		return "";
	}

	function _do_del_prods($prods, $page_pattern = "")
	{
		aw_set_exec_time(AW_LONG_PROCESS);

		$sql_params = "
			objects.site_id = ".aw_ini_get('site_id')." AND
			objects.lang_id = ".aw_global_get('lang_id')."
		";

		$otto_prod_to_code_lut = "";
		if (!empty($page_pattern))
		{
			$sql_params .= " AND aw_shop_products.user18 LIKE '$page_pattern'";

			$otto_prod_to_code_lut = "objects.oid as product_id,";
			$sql_from = "objects
				left join aw_shop_products on objects.brother_of = aw_shop_products.aw_oid";
		}

		if (!empty($prods))
		{
			$product_codes_str = implode(',', map("'%s'", $prods ));
			$sql_params .= " AND otto_prod_to_code_lut.product_code in ($product_codes_str) ";

			$otto_prod_to_code_lut = "otto_prod_to_code_lut.product_code as product_code,
				otto_prod_to_code_lut.product_id as product_id,";
			$sql_from = "otto_prod_to_code_lut
				left join objects on objects.brother_of = otto_prod_to_code_lut.product_id
				left join aw_shop_products on objects.brother_of = aw_shop_products.aw_oid";
		}

		$sql = "
			select
				$otto_prod_to_code_lut
				objects.name as product_name,
				aw_shop_products.user4 as connected_products,
				aw_shop_products.user18 as page
			from
				$sql_from
			where
				$sql_params

		";

		$this->db_query($sql);
		echo "Leidsin tooted: <br />\n";
		flush();
		$found_any_products = false;
		$product_ids = array();
		while ($row = $this->db_next())
		{
			$found_any_products = true;
			$product_ids[$row['product_id']] = $row['product_id'];
			echo $row['product_id']." (".$row['page'].") -- ".$row['product_name']."<br />\n";
			flush();
		}

		// so maybe the product obj. is not present in otto_prod_to_code_lut, lets find it by aw_shop_products.user6 value then (comma separated product codes)
		foreach ($prods as $prod)
		{
			$this->db_query("
				SELECT
					aw_shop_products.aw_oid as aw_oid,
					aw_shop_products.user6 as user6
				FROM
					aw_shop_products
					LEFT JOIN objects ON objects.oid = aw_shop_products.aw_oid
				WHERE
					aw_shop_products.user6 like '%".$prod."%' AND
					objects.site_id = ".aw_ini_get('site_id')." AND
					objects.lang_id = ".aw_global_get('lang_id')."
			");
			while ($row = $this->db_next())
			{
				$found_any_products = true;
				$product_ids[$row['aw_oid']] = $row['aw_oid'];
			}
		}

		if ($found_any_products === false)
		{
			echo "Tooteid ei leitud:<br>\n";
			arr($prods);
			arr($page_pattern);
			return;
		}

		$product_ids_str = implode(',', $product_ids);
		$this->db_query("
			select
				target
			from
				aliases
			where
				source in($product_ids_str)
		");

		while ($row = $this->db_next())
		{
			$packaging_ids[$row['target']] = $row['target'];
		}
		$packaging_ids_str = implode(',', $packaging_ids);

		/**
			DELETING
				-- products (colors)
				-- packagings (prices/sizes)
		**/
		if (!empty($product_ids_str))
		{
			$this->db_query("delete from objects where oid in ($product_ids_str)");
			$this->db_query("delete from aw_shop_products where aw_oid in ($product_ids_str)");
			$this->db_query("delete from aliases where source in ($product_ids_str)");
			$this->db_query("delete from otto_prod_to_code_lut where product_id in ($product_ids_str)");
			echo "Kustutasin <strong>".count($product_ids)."</strong> toodet (v&auml;rvid)<br />\n";
		}
		if (!empty($packaging_ids_str))
		{
			$this->db_query("delete from objects where oid in ($packaging_ids_str)");
			$this->db_query("delete from aw_shop_packaging where id in ($packaging_ids_str)");
			$this->db_query("delete from aliases where source in ($packaging_ids_str)");
			echo "Kustutasin <strong>".count($packaging_ids)."</strong> pakendit (suurused/hinnad)<br />\n";
		}

		flush();

		$other_prods = array();
		foreach ($product_ids as $id)
		{
			$other_prods[] = " user4 like '%".$id."%' ";
		}
		$other_prods_data = $this->db_fetch_array("select * from aw_shop_products where ".implode(" or ", $other_prods)." ");
		arr($other_prods_data);
		foreach ($other_prods_data as $data)
		{
			$additional_prods = explode(',', $data['user4']);
			$valid_prods = array();
			foreach ($additional_prods as $prod)
			{
				if (!empty($prod) && array_search($prod, $product_ids) === false)
				{
					$valid_prods[] = $prod;
				}
			}
			$sql = 'update aw_shop_products set user4 = '.implode(',', $valid_prods).' where aw_oid='.$data['aw_oid'];
			$this->db_query($sql);
		}
		$cache = get_instance("cache");
		$cache->full_flush();

		echo "valmis! <br />";
	}

	function _get_id_by_code($code, $s_type = NULL)
	{
		if ($s_type != "")
		{
			$ad_sql = " AND user17 LIKE '%($s_type)%' ";
		}
		$id = $this->db_fetch_field("SELECT aw_oid FROM aw_shop_products LEFT JOIN objects ON objects.oid = aw_shop_products.aw_oid  WHERE user20 = '$code' $ad_sql AND objects.status > 0 AND objects.lang_id = ".aw_global_get("lang_id"), "aw_oid");
		return $id;
	}

	function _get_ids_by_code($code)
	{
		$ret = array();
		$this->db_query("SELECT aw_oid FROM aw_shop_products LEFT JOIN objects ON objects.oid = aw_shop_products.aw_oid  WHERE user20 = '$code' AND objects.status > 0 AND objects.lang_id = ".aw_global_get("lang_id"));
		while ($row = $this->db_next())
		{
			$ret[] = obj($row["aw_oid"]);
		}
		return $ret;
	}
	/**
		@attrib name=import_discount_products
		@param id required type=int
	**/
	function import_discount_products($args)
	{
		$object_id = $args['id'];
		$object = new object($object_id);

		$file_url = $object->prop("discount_products_file");
		if (!empty($file_url))
		{
			$rows = file($file_url);

			// fucking mackintosh
			if (count($rows) == 1)
			{
				$lines = $this->mk_file($file_url, "\t");
				if (count($lines) > 1)
				{
					$tmpf = tempnam("/tmp", "aw-ott-imp-5");
					$fp = fopen($tmpf,"w");
					fwrite($fp, join("\n", $lines));
					fclose($fp);
					$file_url = $tmpf;
				}
			}

			$rows = file($file_url);




			if ($rows !== false)
			{
				// unset the firs row:
				unset($rows[0]);
				// first of all, empty the table
				$this->db_query("delete from bp_discount_products where lang_id=".aw_global_get('lang_id'));
				echo "importing ".count($rows)." products<br>";
				$prods_data = array();
				foreach($rows as $row)
				{
					$fields = explode("\t", $row);

					// fields 5 & 6 contain price-s, and they should not contain
					// any spaces or commas or double quotas:
					$fields[5] = str_replace(" ", "", $fields[5]);
					$fields[5] = str_replace(",", "", $fields[5]);
					$fields[5] = str_replace('"', "", $fields[5]);


					$fields[6] = str_replace(" ", "", $fields[6]);
					$fields[6] = str_replace(",", "", $fields[6]);
					$fields[6] = str_replace('"', "", $fields[6]);

					// ok, i'm sick of this thing, they always bounce this
					// thing to me when there is new price bigger than old price
					// so what the hell, lets solve this problem then!
					// if new price is bigger than old price, then switch them:
					$fields[5] = (int)$fields[5];
					$fields[6] = (int)$fields[6];
					if ($fields[5] < $fields[6])
					{
						$old_price = $fields[6];
						$new_price = $fields[5];
					}
					else
					{
						$old_price = $fields[5];
						$new_price = $fields[6];
					}

					$prod_oid = $this->db_fetch_field("
						select
							aw_oid
						from
							aw_shop_products
							left join objects on (aw_oid = oid)
						where
							user6 like '%".trim($fields[1])."%' and
							lang_id = ".aw_global_get('lang_id').";
					", "aw_oid");
					if (empty($prod_oid)){
						echo "## tootekoodile ".trim($fields[1])." ei leitud toote objekti<br />\n";
						$prod_oid = 0;
					}

					$sql = "insert into bp_discount_products set ";
					$sql .= "prom='".trim($fields[0])."',";
					$sql .= "product_code='".trim($fields[1])."',";
					$sql .= "name='".trim($fields[2])."',";
					$sql .= "size='".trim($fields[3])."',";
					$sql .= "amount=".(int)$fields[4].",";
					$sql .= "old_price=".$old_price.",";
					$sql .= "new_price=".$new_price.",";
					$sql .= "category='".trim($fields[7])."',";
					$sql .= "prod_oid=".$prod_oid.",";
					$sql .= "lang_id=".aw_global_get('lang_id')." ;";

					$this->db_query($sql);

					if ((int)$fields[4] > 0)
					{
						$prods_data[$fields[1]] = array(
							'new_price' => (int)$fields[6],
							'amount' => (int)$fields[4]
						);
					}

				}
				$discount_products_parents = $object->prop('discount_products_parents');

				$visible_discount_products = array();
				$this->db_query("select product_id from otto_prod_to_code_lut where product_code in (".implode(',', map("'%s'", array_keys($prods_data))).")");
				while ($row = $this->db_next())
				{
					$visible_discount_products[$row['product_id']] = $row['product_id'];
				}

				$this->db_query("select product from otto_prod_to_section_lut where section in (".$discount_products_parents.") and lang_id = ".aw_global_get('lang_id')."");
				while ($row = $this->db_next())
				{
					if ($this->can('view', $row['product']))
					{
						$prod_obj = new object($row['product']);

					/*
						// I don't remember anymore what was the purpose for the following logic
						// but I think it might break things ...
						// So I set the userch3 flag to zero for all imported discount products
						// --dragut@25.03.2009
						if (isset($visible_discount_products[$row['product']]))
						{
							if ($prod_obj->prop('userch3') == 1)
							{
								$prod_obj->set_prop('userch3', 0);
								$prod_obj->save();
							}
						}
						else
						{
							if ($prod_obj->prop('userch3') != 1)
							{
								$prod_obj->set_prop('userch3', 1);
								$prod_obj->save();
							}
						}
					*/
						$prod_obj->set_prop('userch3', 0);
						$prod_obj->save();
					}
				}
			//	echo ".::[ import complete ]::.<br>";
			}
			else
			{
				echo "<span style=\"color:red;\">Faili ei &otilde;nnestunud lugeda!</span><br>";
			}

		}
		echo "Done <br />\n";
		return $this->mk_my_orb("change", array(
			"id" => $object_id,
			"group" => "discount_products",
		));
	}

	/**
		@attrib name=clear_discount_products
		@param id optional type=int
		@param lang_id optional type=int
	**/
	function clear_discount_products($args)
	{
		$sql = "delete from bp_discount_products";
		if (!empty($args['lang_id']))
		{
			$sql .= ' where lang_id='.aw_global_get('lang_id');
		}
		$this->db_query($sql);

		return $this->mk_my_orb("change", array(
			"id" => $args['id'],
			"group" => "discount_products",
		));
	}

	//
	// source - image url
	// format - images format (1 - for big image, 2 for thumbnail)
	//- target_folder - server folder where to download images
	// filename - the new filename, if empty, then original filename is used provided by image parameter
	// debug - if set to true, then print out what is happening during download process (boolean)
	// otto_import - otto_import object instance (aw object)
	// overwrite - boolean, indictes if the image file should be overwritten or not
	function get_image($arr)
	{
		$debug = $arr['debug'];
		$overwrite = ( $arr['overwrite'] === true ) ? true : false;

		if (empty($arr['filename']))
		{
			$filename = basename($arr['source'], '.jpg');
		}
		else
		{
			$filename = basename($arr['filename'], '.jpg');
		}

		$folder = $this->get_file_location(array(
			'filename' => $filename,
			'otto_import' => $arr['otto_import']
		));
		if ($folder)
		{
			// new image file
			$new_file = $folder.'/'.$filename.'_'.$arr['format'].'.jpg';
			if ($overwrite || !file_exists($new_file) || filesize($new_file) == 0)
			{
				$this->copy_file(array(
					'source' => $arr['source'],
					'target' => $new_file
				));
			}
			if (filesize($new_file) > 0)
			{
				return true;
			}
		}
		return false;
	}

	////
	// source - url or filesystem path where to get the video (string)
	// otto_import - otto_import object's instance (aw object)
	function get_video($arr)
	{
		if (!empty($arr['filename']))
		{
			$filename = $arr['filename'];
		}
		else
		{
			$filename = basename($arr['source']);
		}

		if (empty($filename) || empty($arr['otto_import']))
		{
			return false;
		}

		$folder = $this->get_file_location(array(
			'filename' => $filename,
			'otto_import' => $arr['otto_import']
		));

		$new_file = $folder.'/'.$filename;

		if (!file_exists($new_file) || $arr['overwrite'] === true)
		{
			$result = $this->copy_file(array(
				'source' => $arr['source'],
				'target' => $new_file
			));
		}
		return $result;
	}

	////
	// filename - (string) filename to ask location for
	// create - (boolean) if the location doesn't exist, then create it, othervise the function returns false
	// otto_import (aw object) otto import object instance
	function get_file_location($arr)
	{
		$filename = $arr['filename'];

		$folder = $arr['otto_import']->prop('images_folder').'/'.$filename{0};

		if (!is_dir($folder))
		{
			mkdir($folder);
		}
		$folder .= '/'.$filename{1};
		if (!is_dir($folder))
		{
			mkdir($folder);
		}
		if (!is_writable($folder))
		{
			return false;
		}
		return $folder;
	}

	////
	// source - url or path where to get the media file (string)
	// target - full path in filesystem where to save the file (string)
	function copy_file($arr)
	{
		$f = fopen($arr['source'], 'rb');
		if ($f)
		{
			while (!feof($f))
			{
				$content .= fread($f, 1024);
			}
			fclose($f);

			$f = fopen($arr['target'], 'wb');
			if ($f)
			{
				fwrite($f, $content);
				fclose($f);
			}

			return true;
		}

		return false;
	}

	function get_file_name($imnr)
	{
		return $imnr{0}.'/'.$imnr{1}.'/'.$imnr;
	}

	function _get_availability_import_link($arr)
	{
		$arr['prop']['value'] = html::href(array(
			'caption' => t('Impordi'),
			'url' => $this->mk_my_orb('do_products_amounts_import', array('id' => $arr['obj_inst']->id()))
		));
	}

	/**
		@attrib name=do_products_amounts_import
		@param id required type=int
	**/
	function do_products_amounts_import($arr)
	{
		ini_set("memory_limit", "2048M");
		aw_set_exec_time(AW_LONG_PROCESS);

		if ($this->can('view', $arr['id']))
		{
			$o = new object($arr['id']);
		}
		else
		{
			exit('OTTO import object is not accessible');
		}


		// Get the file from FTP:
		$ftp = new ftp();
		$connection = $ftp->connect(array(
			'host' => $o->prop('availability_ftp_host'),
			'user' => $o->prop('availability_ftp_user'),
			'pass' => $o->prop('availability_ftp_password')
		));

		$file_location = $o->prop('availability_ftp_file_location');
		echo "Get file from ftp ... ";
		flush();
		$file_content = $ftp->get_file($file_location);
		$local_file = aw_ini_get('site_basedir').'/files/otto_import_availability.zip';
		$file_size = file_put_contents($local_file, $file_content);
		echo "( ".number_format(($file_size / 1024 / 1024), 2)." )";
		echo "[done]<br />\n";
		flush();
		echo "Unpacking the file ...";
		flush();
		shell_exec("unzip -o $local_file");
		echo "[done]<br />\n";
		flush();

		// Start import:
		$lines = file(aw_ini_get('site_basedir').'/files/ASTAEXP.TXT');

		$prods = new object_list(array(
			'class_id' => CL_SHOP_PRODUCT
		));
		foreach ($prods->arr() as $prod_id => $prod)
		{
			$codes[substr($prod->prop('code'), 0, 6)] = $prod;
		}

		// tarnija seostamiseks
		$comps = new object_list(array(
			'class_id' => CL_CRM_COMPANY,
			'name' => '%Saksa%',
			new obj_predicate_limit(1),
		));
		$comp = $comps->count() > 0 ? $comps->begin() : false;

		$whs = new object_list(array(
			'class_id' => CL_SHOP_WAREHOUSE,
			'name' => '%Eesti%',
			new obj_predicate_limit(1),
		));
		$wh = $whs->count() > 0 ? $whs->begin() : false;

		foreach ($lines as $line)
		{
			$fields = explode(';', $line);
			if (array_key_exists($fields[0], $codes))
			{
				// lets get sizes from the product
				$prod = $codes[$fields[0]];
				echo "product: ".$prod->name()." (".$prod->id().")<br />\n";
				$packagings = $prod->get_packagings(array());
				foreach ($packagings->arr() as $packaging)
				{
					//	do_products_amounts_import_handle_size() handles different formes of sizes a'la S(127), 41/2(37), 56
					$handled_code = $this->do_products_amounts_import_handle_size($packaging->prop('size'));
					echo "-- Going to compare '".$handled_code."' with '".((int)($fields[1]))."' (".$fields[1].") - packaging id: ".$packaging->id()."<br />\n";
					if ($handled_code === ((int)$fields[1]))
					{
						echo "----".$packaging->prop('size')." -- ".$fields[1]." - ".((int)$fields[1])."/ ".$fields[2]."<br />\n";

						$purvs = new object_list(array(
							"class_id" => CL_SHOP_PRODUCT_PURVEYANCE,
							"packaging" => $packaging->id(),
							"lang_id" => array(),
							"site_id" => array(),
						));
						if ($purvs->count() > 0)
						{
							$purv = $purvs->begin();
							echo "-------- Existing purveyance oid: ".$purv->id()." <br />\n";
						}
						else
						{
							echo "-------- NEW purv: ";
							$purv = new object();
							$purv->set_class_id(CL_SHOP_PRODUCT_PURVEYANCE);
							$purv->set_parent($packaging->id());
							$purv->save();
							$purv->connect(array(
								'to' => $packaging->id(),
								'type' => 'RELTYPE_PACKAGING'
							));
							$purv->set_prop('packaging', $packaging->id());

							// tarnija seostamine
							if ($comp !== false)
							{
								echo "------------ connect company ".$comp->name()."<br />\n";
								$purv->connect(array(
									'to' => $comp->id(),
									'type' => 'RELTYPE_COMPANY'
								));
								$purv->set_prop('company', $comp->id());
							}

							// seostab lao ka 2ra:
							if ($wh !== false)
							{
								echo "------------ connect warehouse ".$wh->name()."<br />\n";
								$purv->connect(array(
									'to' => $wh->id(),
									'type' => 'RELTYPE_WAREHOUSE'
								));
								$purv->set_prop('warehouse', $wh->id());
							}
							$purv->save();
						}
						//	It's better to handle the status as integer. The caption will be in template
						$purv->set_name($fields[2]);
						switch ($fields[2])
						{
							case 1:
								$purv->set_comment(t('Tarneaeg 3-4 n&auml;dalat'));
								break;
							case 2:
								$purv->set_comment(t('Tarneaeg pikem kui 4 n&auml;dalat'));
								break;
							case 3:
								$purv->set_comment(t('V&auml;ljam&uuml;&uuml;dud'));
								break;
						}
						$purv->set_prop('code', $fields[2]);
						$purv->save();
					//	echo "-------- Updated purveyance for packaging ".$packaging->id().", set comment = ".$purv->comment().", set name = ".$purv->name()."<br />\n";
					}
				}
				$packagings = null;

			}
			$fields = null;
		}
		exit('done');
	}

	function do_products_amounts_import_handle_size($size)
	{
		/*
			American sizes must be decoded
			8XL = 924
			7XL = 923
			6XL = 922
			5XL = 921
			4XL = 910
			3XL = XXXL = 909
			XXL = 908
			XL = 907
			L = 906
			M = 905
			ML = 955
			S = 904
			XS = 903
			XXS = 902
			3XS = XXXS = 901
		*/
		$expressions = array(
			'/^3XS\([0-9]+\)$/' => 901,
			'/^XXXS\([0-9]+\)$/' => 901,
			'/^XXS\([0-9]\)$/' => 902,
			'/^XS\([0-9]+\)$/' => 903,
			'/^S\([0-9]+\)$/' => 904,
			'/^ML\([0-9]+\)$/' => 955,
			'/^M\([0-9]+\)$/' => 905,
			'/^L\([0-9]+\)$/' => 906,
			'/^XL\([0-9]+\)$/' => 907,
			'/^XXL\([0-9]+\)$/' => 908,
			'/^XXXL\([0-9]+\)$/' => 909,
			'/^3XL\([0-9]+\)$/' => 909,
			'/^4XL\([0-9]+\)$/' => 910,
			'/^5XL\([0-9]+\)$/' => 921,
			'/^6XL\([0-9]+\)$/' => 922,
			'/^7XL\([0-9]+\)$/' => 923,
			'/^8XL\([0-9]+\)$/' => 924,
		);
		foreach($expressions as $expression => $value)
		{
			if(preg_match($expression, $size))
			{
				return $value;
			}
		}
		/*
			double sizes 40/42 use the starting 40 only
		*/
		if(preg_match('/^[0-9]+[\/]{1}[0-9]+$/', $size, $m))
		{
	//	arr($m);
			return (int)substr($size, 0, strpos($size, "/"));
		}
		/*
			ring or shoe sizes 19,5 or 8,5 use 195 or 85 (without decimals)

			28.08.2009 -kaarel : The shitty part is that we have show sizes like this: 41/2(37)
			Tested cases:
			41/2(37)	->	45
			51/2(38		->	55
			6(39)		->	60
			61/2(40)	->	65
			7(40		->	70
			71/2(4		->	75
		*/
		if(preg_match('/^[0-9]+(\/[0-9]+)?\([0-9]+/', $size))
		{
			return ((int)substr($size, 0, min(strpos($size, "/") -1, strpos($size, "("))))*10 + (strpos($size, "/") ? 5 : 0);
		}
		return (int)aw_math_calc::string2float($size);
		// !!! FOLLOWING CASES ARE NOT YET HANDLED !!!
		/*
			if quantity is a length so order in cm, max 999 = 10m,		// I can't understand this one! -kaarel 28.08.2009
		*/
	}

	public function _get_xml_file_link($arr)
	{
		$url = $this->mk_my_orb('get_products_xml', array('id' => $arr['obj_inst']->id()));
		$arr['prop']['value'] = html::href(array(
			'url' => $url,
			'caption' => $url
		));
	}

	public function _get_csv_files_list($arr)
	{
		/*
			I probably need to build here somekind of interface where it is possible to choose which files will be imported
		*/
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->define_field(array(
			'name' => 'csv_file',
			'caption' => t('CSV fail')
		));
		$t->define_field(array(
			'name' => 'xml_file',
			'caption' => t('XML fail')
		));
		$folder = $arr['obj_inst']->prop('csv_files_location');

		$files = glob($folder.'/*.xls');

		$page_files = array();
		foreach ($files as $file)
		{
			$filename = basename($file, '.xls');

			$parts = explode('-', $filename);
			if (count($parts) == 2)
			{
				$page = $parts[0];
				$page_nr = $parts[1];
				$page_files[$page][$page_nr] = $filename;
			}
		}

		foreach ($page_files as $page => $files)
		{
			$t->define_data(array(
				'csv_file' => $page .'(' . implode(',', array_keys($files)) . ')'
			));
		}
	}

        /**
                @attrib name=get_products_xml
        **/
	public function get_products_xml()
	{
		$xml_file_path = aw_ini_get('site_basedir').'/files/warehouse_import/products.xml';
		// This is for warehouse import to get the XML file which the warehouse import will be able to import

		// TODO: I need a better way to have otto import object id here
		$otto_import_ol = new object_list(array(
			'class_id' => CL_OTTO_IMPORT
		));
		$o = $otto_import_ol->begin();

		$this->import_data_from_csv($o);

/*
// üks variant kuidas see toodete xml välja võiks näha
<products>
	<product>
		<name />
		<desc />
		<categories>
			<category />
			<category />
			<category />
		</categories>
		<colors>
			<color>
				<code />
				<color_name />
				<sizes>
					<size>
						<size_name />
						<price />
					</size>
				</sizes>
			</color>
		</colors>
	</product>
</products>

teine variant oleks teha xml selline, et vastaks aw objektidele (<packet><product></packagin>) jne.
Esimese puhul oleks ülesehitus vast loogilisem, aga siis peaks kuidagi konfitavaks tegema selle, et
milliste parent tagide järgi packette/tooteid/pakeneid tekitatakse (või kas üldse tehakse)

<warehouse_data>
	<packet>
		<page />
		<nr />
		<name />
		<description />
		<categories>
			<category />
		</categories>
		<products>
			<page />
			<nr />
			<type />
			<color />
			<code />
			<product>
				<packagings>
					<page />
					<nr />
					<type />
					<size />
					<price />
				</packagings>
			</product>
		</product>
	</packet>
</warehouse_data>

Võtn hetkel kasutusele selle teise variandi

*/
		$oxml = new XMLWriter();
	//	$oxml->openMemory();
		$oxml->openURI($xml_file_path);
		$oxml->startDocument();
		$oxml->startElement('warehouse_data');

		$prods = $this->db_fetch_array("select * from otto_imp_t_prod where lang_id = ".aw_global_get('lang_id')." order by pg,nr");
		foreach ($prods as $prod)
		{
			$prod = $this->convert_utf($prod);


			$oxml->startElement('packet');

			$oxml->writeElement('page', $prod['pg']);
			$oxml->writeElement('nr', $prod['nr']);

			$oxml->startElement('name');
			$oxml->writeCData($prod['title']);
			$oxml->endElement();

			$oxml->startElement('categories');
			foreach (explode(',', $prod['extrafld']) as $extrafld)
			{
				$oxml->writeElement('category', $extrafld);
			}
			$oxml->endElement();

			$oxml->startElement('description');
			$oxml->writeCData($prod['c']);
			$oxml->endElement();

			echo "- ".$prod['pg'].' -- '.$prod['nr'].' -- '.$prod['title']."<br />\n";
			$codes = $this->db_fetch_array("select * from otto_imp_t_codes where lang_id = ". aw_global_get("lang_id")." and pg = '". $prod["pg"]."' and nr = ".$prod["nr"]." order by pg,nr,s_type" );
			$oxml->startElement('products');
			foreach (safe_array($codes) as $code)
			{

				$code = $this->convert_utf($code);

				$oxml->startElement('product');

				$oxml->writeElement('page', $code['pg']);

				$oxml->writeElement('nr', $code['nr']);

				$oxml->startElement('type');
				$oxml->writeCData($code['s_type']);
				$oxml->endElement();

				$oxml->startElement('color');
				$oxml->writeCData($code['color']);
				$oxml->endElement();

				$oxml->startElement('code');
				$oxml->writeCData($code['code']);
				$oxml->endElement();

				// here i have product code and now i should perform images search
				$imgs = $this->otto_picture_import(array(
					'pcode' => $code['code'],
					'import_obj' => $o,
					'start_time' => time(),
				));
				if (empty($imgs))
				{
					echo "[NO IMAGES FOUND FOR THIS PRODUCT]<br />\n";
				}
				$oxml->startElement('images');
				foreach (safe_array($imgs) as $img)
				{
					$oxml->startElement('image');
					$oxml->writeCData($img);
					$oxml->endElement();
				}
				$oxml->endElement();

				echo "---- ".$code['pg']." -- ".$code['nr']." -- ".$code['s_type']." -- ".$code['code']." -- ".$code['color']."<br />\n";
				$sizes = $this->db_fetch_array("select * from otto_imp_t_prices where lang_id = ".aw_global_get("lang_id")." and pg = '".$code['pg']."' and nr = ".$code['nr']." and s_type = '".$code['s_type']."' order by pg,nr,s_type");

				$oxml->startElement('packagings');

				foreach ($sizes as $size)
				{

					$size = $this->convert_utf($size);

					echo "-------- ".$size['pg']." -- ". $size['nr'] ." -- ".$size['s_type']." -- ".$size['price'].".- -- ".$size['size']." -- ".$size['unit']."<br />\n";
					$tmp = explode(',', $size['size']);
					foreach ($tmp as $s)
					{
						$oxml->startElement('packaging');

						$oxml->writeElement('page', $size['pg']);

						$oxml->writeElement('nr', $size['nr']);

						$oxml->startElement('type');
						$oxml->writeCData($size['s_type']);
						$oxml->endElement();

						$oxml->writeElement('price', $size['price']);

						$oxml->startElement('size');
						$oxml->writeCData($s);
						$oxml->endElement();

						$oxml->endElement();

						echo "------------ ".$s."<br />\n";
					}
				}

				$oxml->endElement(); // packagings

				$oxml->endElement(); // product tag
			}
			$oxml->endElement(); // products tag

			$oxml->endElement(); // packet tag
		}

		$oxml->endElement(); // warehouse_data tag

		return $xml_file_path;
	}

	function convert_utf($arr)
	{
		foreach ($arr as $k => $v)
		{
			$arr[$k] = utf8_encode($v);
		}
		return $arr;
	}

	function parse_csv_file($file)
	{

	}

	// for warehouse interface:
	public function get_warehouse_list()
	{
		return array(
			1 => array(
				'name' => t('OTTO Eesti ladu'),
				'info' => t('draiver')
			)
		);
	}

	public function get_pricelist_xml(){}
	public function get_prices_xml(){}
	public function get_dnotes_xml(){}
	public function get_amounts_xml($wh_id = null){}
	public function get_bills_xml($wh_id = null){}
}
