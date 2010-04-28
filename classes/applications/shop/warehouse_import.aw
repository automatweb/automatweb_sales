<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_WAREHOUSE_IMPORT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=smeedia
@tableinfo aw_warehouse_import master_index=brother_of master_table=objects index=aw_oid

@default table=aw_warehouse_import
@default group=general_general

	@property name type=textbox table=objects field=name
	@caption Nimi

	@property data_source type=select table=objects field=meta method=serialize
	@caption Andmeallikas

	@property price_list type=select table=objects field=meta method=serialize
	@caption Hinnakiri

@default group=aw_warehouses 

	@property aw_warehouses_tb type=toolbar store=no no_caption=1
	@caption AW Laod toolbar

	@property aw_warehouses type=table store=no no_caption=1
	@caption AW Laod

@default group=import_warehouses 

	@property config_table type=table store=no no_caption=1
	@caption Seaded

@default group=import_timing

	@layout timing_prods_lay type=vbox area_caption=Toodete&nbsp;impordi&nbsp;ajastus closeable=1

		@property timing_prods type=releditor reltype=RELTYPE_PROD_REPEATER use_form=emb rel_id=first field=aw_timing_prods parent=timing_prods_lay
		@caption Toodete impordi ajastus

	@layout timing_prices_lay type=vbox area_caption=Hindade&nbsp;impordi&nbsp;ajastus closeable=1

		@property timing_prices type=releditor reltype=RELTYPE_PRICES_REPEATER use_form=emb rel_id=first field=aw_timing_prices parent=timing_prices_lay
		@caption Hindade impordi ajastus

	@layout timing_amounts_lay type=vbox area_caption=Koguste&nbsp;impordi&nbsp;ajastus closeable=1

		@property timing_amounts type=releditor reltype=RELTYPE_AMOUNTS_REPEATER use_form=emb rel_id=first field=aw_timing_amounts parent=timing_amounts_lay
		@caption Koguste impordi ajastus

	@layout timing_price_lists_lay type=vbox area_caption=Hinnakirjade&nbsp;impordi&nbsp;ajastus closeable=1

		@property timing_price_lists type=releditor reltype=RELTYPE_PRICE_LISTS_REPEATER use_form=emb rel_id=first field=aw_timing_price_lists parent=timing_price_lists_lay
		@caption Hinnakirjade impordi ajastus

	@layout timing_dnotes_lay type=vbox area_caption=Saatelehtede&nbsp;impordi&nbsp;ajastus closeable=1

		@property timing_dnotes type=releditor reltype=RELTYPE_DNOTES_REPEATER use_form=emb rel_id=first field=aw_timing_price_lists parent=timing_dnotes_lay
		@caption Saatelehtede impordi ajastus
 
       @layout timing_bills_lay type=vbox area_caption=Arvete&nbsp;impordi&nbsp;ajastus closeable=1

                @property timing_bills type=releditor reltype=RELTYPE_BILLS_REPEATER use_form=emb rel_id=first field=aw_timing_bills parent=timing_bills_lay
                @caption Arvete impordi ajastus

       @layout timing_orders_lay type=vbox area_caption=Tellimuste&nbsp;impordi&nbsp;ajastus closeable=1

                @property timing_orders type=releditor reltype=RELTYPE_ORDERS_REPEATER use_form=emb rel_id=first field=aw_timing_orders parent=timing_orders_lay
                @caption Tellimuste impordi ajastus

// lets leave the cleanup for later --dragut
default group=product_status

	layout stat_prods_lay type=vbox area_caption=Toodete&nbsp;impordi&nbsp;staatus closeable=1

		property product_status type=text store=no no_caption=1 parent=stat_prods_lay

@default group=product_status_dev

	@layout product_status_dev_frame type=hbox width=20%:80%
		
		@layout product_status_dev_imports_frame type=vbox area_caption=Impordid closeable=1 parent=product_status_dev_frame
			@property product_status_dev_imports type=treeview store=no no_caption=1 parent=product_status_dev_imports_frame

		@layout product_status_dev_left_frame type=vbox parent=product_status_dev_frame 

			@layout product_status_dev_info_frame type=vbox area_caption=Info closeable=1 parent=product_status_dev_left_frame
				@property product_status_dev_info type=text store=no no_caption=1 parent=product_status_dev_info_frame

			@layout product_status_dev_files_frame type=vbox area_caption=Impordi&nbsp;failid closeable=1 parent=product_status_dev_left_frame
				@property product_status_dev_files type=table store=no no_caption=1 parent=product_status_dev_files_frame

@default group=product_prices

	@layout stat_prices_lay type=vbox area_caption=Hindade&nbsp;impordi&nbsp;staatus closeable=1

		@property prices_status type=text store=no no_caption=1 parent=stat_prices_lay

@default group=product_amounts

	@layout stat_amounts_lay type=vbox area_caption=Koguste&nbsp;impordi&nbsp;staatus closeable=1

		@property amounts_status type=text store=no no_caption=1 parent=stat_amounts_lay

@default group=pricelists

	@layout stat_pricelists_lay type=vbox area_caption=Hinnakirjade&nbsp;impordi&nbsp;staatus closeable=1

		@property pricelists_status type=text store=no no_caption=1 parent=stat_pricelists_lay

@default group=delivery_notes

	@layout stat_dnotes_lay type=vbox area_caption=Saatelehtede&nbsp;impordi&nbsp;staatus closeable=1

		@property dnotes_status type=text store=no no_caption=1 parent=stat_dnotes_lay

@default group=bills

        @layout stat_bills_lay type=vbox area_caption=Arvete&nbsp;impordi&nbsp;staatus closeable=1

                @property bills_status type=text store=no no_caption=1 parent=stat_bills_lay

@default group=orders

        @layout stat_orders_lay type=vbox area_caption=Tellimuste&nbsp;impordi&nbsp;staatus closeable=1

                @property orders_status type=text store=no no_caption=1 parent=stat_orders_lay

	@groupinfo general_general parent=general caption="&Uuml;ldine"
	@groupinfo aw_warehouses parent=general caption="AW Laod"
	@groupinfo import_warehouses parent=general caption="Imporditavad laod"

@groupinfo import_status caption="Importide staatus"

	groupinfo product_status caption="Toodete import" parent=import_status submit=no
	@groupinfo product_status_dev caption="Toodete import" parent=import_status submit=no
	@groupinfo product_prices caption="Toodete hinnad" parent=import_status submit=no
	@groupinfo product_amounts caption="Toodete laoseisud" parent=import_status  submit=no
	@groupinfo pricelists caption="Hinnakirjad" parent=import_status submit=no
	@groupinfo customers caption="Kliendid" parent=import_status submit=no
	@groupinfo delivery_notes caption="Saatelehed" parent=import_status submit=no
	@groupinfo bills caption="Arved" parent=import_status submit=no
	@groupinfo orders caption="Tellimused" parent=import_status submit=no

@groupinfo import_timing caption="Importide ajastus"


@reltype WAREHOUSE value=10 clid=CL_SHOP_WAREHOUSE
@caption AW Ladu

@reltype PROD_REPEATER value=11 clid=CL_RECURRENCE
@caption Toodete kordaja

@reltype PRICES_REPEATER value=12 clid=CL_RECURRENCE
@caption Hindade kordaja

@reltype AMOUNTS_REPEATER value=13 clid=CL_RECURRENCE
@caption Koguste kordaja

@reltype PRICE_LISTS_REPEATER value=14 clid=CL_RECURRENCE
@caption Hinnakirjade kordaja

@reltype DNOTES_REPEATER value=15 clid=CL_RECURRENCE
@caption Saatelehtede kordaja

@reltype BILLS_REPEATER value=16 clid=CL_RECURRENCE
@caption Arvete kordaja

@reltype ORDERS_REPEATER value=17 clid=CL_RECURRENCE
@caption Tellimuste kordaja

*/

class warehouse_import extends class_base
{
	const AW_CLID = 1535;

	function warehouse_import()
	{
		$this->init(array(
			"tpldir" => "applications/shop/warehouse_import",
			"clid" => CL_WAREHOUSE_IMPORT
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
		}

		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
		}

		return $retval;
	}

	function _get_aw_warehouses($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->set_sortable(false);

		$t->define_field(array(
			'name' => 'warehouse',
			'caption' => t('Ladu')
		));

		$t->define_field(array(
			"name" => "import_matrix",
			"caption" => t("Vali kust imporditakse"),
			"align" => "center"
		));

		$t->define_field(array(
			'name' => 'products',
			'caption' => t('Tooted'),
			"parent" => "import_matrix",
		));
		$t->define_field(array(
			'name' => 'amounts',
			"parent" => "import_matrix",
			'caption' => t('Laoseis')
		));
		$t->define_field(array(
			'name' => 'prices',
			"parent" => "import_matrix",
			'caption' => t('Hinnad')
		));
		$t->define_field(array(
			'name' => 'price_list',
			"parent" => "import_matrix",
			'caption' => t('Hinnakiri')
		));
		$t->define_chooser(array(
			'field' => 'oid',
			'name' => 'sel'
		));

		// make picker options for ext wh
		$ext_wh = array("" => t("--vali--"));
		foreach($arr["obj_inst"]->list_external_warehouses(true) as $id => $data)
		{
			$ext_wh[$id] = $data["name"];
		}

		foreach ($arr['obj_inst']->list_aw_warehouses() as $wh_id => $wh_data)
		{
			$t->define_data(array(
				'warehouse' => $wh_data["name"],
				"oid" => $wh_id,
				"products" => html::select(array(
					"name" => "imp[$wh_id][products]",
					"options" => $ext_wh,
					"value" => $wh_data["imp_products"]
				)),
				"amounts" => html::select(array(
					"name" => "imp[$wh_id][amounts]",
					"options" => $ext_wh,
					"value" => $wh_data["imp_amounts"]
				)),
				"prices" => html::select(array(
					"name" => "imp[$wh_id][prices]",
					"options" => $ext_wh,
					"value" => $wh_data["imp_prices"]
				)),
				"price_list" => html::select(array(
					"name" => "imp[$wh_id][price_list]",
					"options" => $ext_wh,
					"value" => $wh_data["imp_price_list"]
				)),
			));
		}

		return PROP_OK;
	}

	function _set_aw_warehouses($arr)
	{
		$arr["obj_inst"]->set_import_matrix($arr["request"]["imp"]);
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
		$arr["swh"] = "0";
	}

	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_warehouse_import(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_timing_prods":
			case "aw_timing_prices":
			case "aw_timing_amounts":
			case "aw_timing_price_lists":
			case "aw_timing_bills":
			case "aw_timing_orders":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}

	function _get_data_source($arr)
	{
		$arr["prop"]["options"] = array("" => t("--vali--")) + $this->make_keys(class_index::get_classes_by_interface("warehouse_import_if"));
	}

	// I think it shouldn't be selected here ...
	function _get_price_list($arr)
	{
		$arr["prop"]["options"] = array("" => t("--vali--"));

		$ol = new object_list(array(
			'class_id' => CL_SHOP_PRICE_LIST
		));
		foreach ($ol->arr() as $oid => $o)
		{
			$arr['prop']['options'][$oid] = $o->name();
		}
	}

	function _get_aw_warehouses_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_search_button(array(
			"name" => "swh",
			"pn" => "swh",
			"clid" => array(CL_SHOP_WAREHOUSE)
		));
		$tb->add_delete_rels_button();
	}

	function callback_post_save($arr)
	{
		$ps = new popup_search();
		$ps->do_create_rels($arr["obj_inst"], $arr["request"]["swh"], "RELTYPE_WAREHOUSE");
	}

	private function _init_config_table($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "use",
			"caption" => t("Kasutusel"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "info",
			"caption" => t("Lisainfo"),
			"align" => "center"
		));
	}

	function _get_config_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->set_sortable(false);
		$this->_init_config_table($t);

		foreach($arr["obj_inst"]->list_external_warehouses() as $id => $data)
		{
			$t->define_data(array(
				"name" => $data["name"],
				"use" => html::checkbox(array(
					"name" => "use[$id]",
					"value" => 1,
					"checked" => $data["used"] == 1
				)),
				"info" => $data["info"]
			));
		}
	}

	function _set_config_table($arr)
	{
		$arr["obj_inst"]->set_used_external_warehouses($arr["request"]["use"]);
	}

	private function _init_timing_table($t)
	{
		$t->define_field(array(
			"name" => "type",
			"caption" => t("Impordi t&uuml;&uuml;p"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "timing",
			"caption" => t("Impordi ajakava"),
			"align" => "center"
		));
	}

	function _get_import_timing($arr)
	{	
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_import_timing($t);
	}

	function _get_product_status($arr)
	{
		$arr["prop"]["value"] = $this->_describe_import($arr["obj_inst"], "products", "RELTYPE_PROD_REPEATER", 1);
	}

	function _get_prices_status($arr)
	{
		$arr["prop"]["value"] = $this->_describe_import($arr["obj_inst"], "prices", "RELTYPE_PRICES_REPEATER");
	}

	function _get_amounts_status($arr)
	{
		foreach($arr["obj_inst"]->list_aw_warehouses() as $wh_id => $wh_data)
		{
			$arr["prop"]["value"] .= "<h1>".sprintf(t("Ladu %s"), obj($wh_id)->name())."</h1>";
			$arr["prop"]["value"] .= $this->_describe_import($arr["obj_inst"], "amounts", "RELTYPE_AMOUNTS_REPEATER", $wh_id);
		}
	}

	function _get_pricelists_status($arr)
	{
		$arr["prop"]["value"] = $this->_describe_import($arr["obj_inst"], "pricelists", "RELTYPE_PRICE_LISTS_REPEATER");
	}

	function _get_dnotes_status($arr)
	{
		$arr["prop"]["value"] = $this->_describe_import($arr["obj_inst"], "dnotes", "RELTYPE_DNOTES_REPEATER");
	}

        function _get_bills_status($arr)
        {
                $arr["prop"]["value"] = $this->_describe_import($arr["obj_inst"], "bills", "RELTYPE_BILLS_REPEATER");
        }

        function _get_orders_status($arr)
        {
                $arr["prop"]["value"] = $this->_describe_import($arr["obj_inst"], "orders", "RELTYPE_ORDERS_REPEATER");
        }

	private function _describe_import($o, $type, $rt, $wh_id = null)
	{
		$t = "";
		if (($pid = $o->import_is_running($type, $wh_id)))
		{
			$full_stat = $o->full_import_status($type, $wh_id);
			$t = html::strong(t("Import k&auml;ib!"));
			$t .= "<br/>".sprintf(t("Staatus: %s, protsess: %s, t&ouml;&ouml;deldud %s, kokku %s, algusaeg %s"), 
				self::name_for_status($full_stat[2]),
				$pid, 
				(int)$full_stat[4],
				(int)$full_stat[5],
				date("d.m.Y H:i:s", $full_stat[0])
			);

			if ($o->need_to_stop_now($type, $wh_id))
			{
				$t .= "<br/>".html::href(array(
					"url" => $this->mk_my_orb("reset_import", array("type" => $type, "wh_id" => $wh_id, "id" => $o->id(), "post_ru" => get_ru())),
					"caption" => t("Reset")
				));
			}
			else
			{
				$t .= "<br/>".html::href(array(
					"url" => $this->mk_my_orb("stop_import", array("type" => $type, "wh_id" => $wh_id, "id" => $o->id(), "post_ru" => get_ru())),
					"caption" => t("Peata kohe")
				));
			}
		}
		else
		{
			$rec = $o->get_first_obj_by_reltype($rt);
			if ($rec)
			{
				$ne = $rec->instance()->get_next_event(array("id" => $rec->id()));
				if ($ne > 10)
				{
					$t = sprintf(t("J&auml;rgmine import algab %s"), date("d.m.Y H:i", $ne));
				}
				else
				{
					$t = t("Impordi kordaja on l&otilde;ppenud!");
				}
			}
			else
			{
				$t = t("Impordile pole automaatset k&auml;ivitust m&auml;&auml;ratud!");
			}

			$t .= "<br/>".html::href(array(
				"url" => $this->mk_my_orb("do_".$type."_import", array("id" => $o->id(), "wh_id" => $wh_id, "post_ru" => get_ru())),
				"caption" => t("K&auml;ivita kohe")
			));
		}

		if (($prev = $o->get_import_log($type, $wh_id)))
		{
			$tb = new vcl_table();
			$tb->set_sortable(false);

			$tb->define_field(array(
				"caption" => t("Alustati"),
				"name" => "start",
				"align" => "center",
				"type" => "time",
				"format" => "d.m.Y H:i:s",
				"numeric" => 1
			));
			$tb->define_field(array(
				"caption" => t("L&otilde;petati"),
				"name" => "end",
				"align" => "center",
				"type" => "time",
				"format" => "d.m.Y H:i:s",
				"numeric" => 1
			));
			$tb->define_field(array(
				"caption" => t("Edukas"),
				"name" => "success",
				"align" => "center",
			));
			$tb->define_field(array(
				"caption" => t("Imporditud"),
				"name" => "prod_count",
				"align" => "center",
				"numeric" => 1
			));
			$tb->define_field(array(
				"caption" => t("Kokku"),
				"name" => "total",
				"align" => "center",
				"numeric" => 1
			));
			$tb->define_field(array(
				"caption" => t("L&otilde;petamise p&otilde;hjus"),
				"name" => "reason",
				"align" => "center",
			));
			$tb->define_field(array(
				"caption" => t("XML Failid"),
				"name" => "xmls",
				"align" => "center",
			));

			foreach($prev as $entry)
			{
				$xmls = array();
				$pt = aw_ini_get("site_basedir")."/files/warehouse_import/xml_".$type."_".$entry["full_status"][0].".xml";
				foreach(glob($pt) as $item)
				{
					$xmls[] = html::href(array(
						"url" => $this->mk_my_orb("view_xml", array("fn" => basename($item))),
						"caption" => basename($item)
					));
				}
				$tb->define_data(array(
					"start" => $entry["full_status"][0],
					"end" => $entry["finish_tm"],
					"success" => $entry["success"] ? t("Jah") : t("Ei"),
					"prod_count" => (isset($entry["full_status"][4])) ? $entry["full_status"][4] : 'n/a',
					"total" => (isset($entry["full_status"][5])) ? $entry["full_status"][5] : 'n/a',
					"reason" => $entry["reason"],
					"xmls" => join(", ", $xmls)
				));
			}

			$tb->set_caption(t("Eelneva 10 impordi info"));
			$t .= "<br/>".$tb->get_html();
		}

		return $t;
	}

	/**
		@attrib name=view_xml 
		@param fn required
	**/
	function view_xml($arr)
	{
/*		$f = fopen(aw_ini_get("cache.page_cache")."/../files/warehouse_import/products.csv", "r");
			
		$first = true;
		while (($items = fgetcsv($f, 0, "\t", "\"")) !== false)
		{
			if ($first)
			{
				$first = false;
				continue;
			}
			$this->db_query("UPDATE aw_shop_products SET user5 = '".trim($items[5])."' WHERE code = '".trim($items["0"])."'");
if (++$cnt > 10000)
{
			echo $items[0]." <br>\n";
			flush();
$cnt = 0;
}
		}
die("meh");*/
		$fn = aw_ini_get("site_basedir")."/files/warehouse_import/".basename(realpath($arr["fn"]));
		if (file_exists($fn))
		{
			echo '<pre>' . $this->xmlpp(file_get_contents($fn), true) . '</pre>';  
			die();
		}
		die();
	}

	function xmlpp($xml, $html_output=false) 
	{
		$xml_obj = new SimpleXMLElement($xml);
		$level = 4;
		$indent = 0; // current indentation level
		$pretty = array();

		// get an array containing each XML element
		$xml = explode("\n", preg_replace('/>\s*</', ">\n<", $xml_obj->asXML()));

		// shift off opening XML tag if present
		if (count($xml) && preg_match('/^<\?\s*xml/', $xml[0])) 
		{
			$pretty[] = array_shift($xml);
		}

		foreach ($xml as $el) 
		{
			if (preg_match('/^<([\w])+[^>\/]*>$/U', $el)) 
			{
				// opening tag, increase indent
				$pretty[] = str_repeat(' ', $indent) . $el;
				$indent += $level;
			} 
			else 
			{
				if (preg_match('/^<\/.+>$/', $el)) 
				{
					$indent -= $level;// closing tag, decrease indent
				}
				if ($indent < 0) 
				{
					$indent += $level;
				}
				$pretty[] = str_repeat(' ', $indent) . $el;
			}
		}	 
		$xml = implode("\n", $pretty);	 
		return ($html_output) ? htmlentities($xml) : $xml;
 	}

	/**
		@attrib name=reset_import
		@param id required 
		@param type required
		@param wh_id optional
		@param post_ru optional
	**/
	function reset_import($arr)
	{	
		$o = obj($arr["id"]);
		$o->reset_import($arr["type"], $arr["wh_id"]);
		return $arr["post_ru"];
	}

	/**
		@attrib name=stop_import
		@param type required
		@param wh_id optional
		@param id required
		@param post_ru optional
	**/
	function stop_import($arr)
	{
		$o = obj($arr["id"]);
		$o->stop_import($arr["type"], $arr["wh_id"]);
		return $arr["post_ru"];
	}

	function run_backgrounded($act, $id, $wh_id = null)
	{
		$url = $this->mk_my_orb("run_backgrounded", array("wh_id" => $wh_id, "act" => $act, "id" => $id));
		$url = str_replace("/automatweb", "", $url);
		$h = new http;
		exit($url);  // debug:
		$h->get($url);
	}

	/**
		@attrib name=run_backgrounded nologin="1"
		@param id required
		@param wh_id optional
		@param file optional
		@param act required
	**/
	function do_run_bg($arr)
	{
		session_write_close();
		while(ob_get_level()) { ob_end_clean(); }
/*
// If it is needed to debug the imports, then comment the following lines until 'flush()'
		// let the user continue with their business
		ignore_user_abort(1);
		header("Content-Type: image/gif");
		header("Content-Length: 43");
		header("Connection: close");
		echo base64_decode("R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==")."\n";
		flush();
*/
		aw_set_exec_time(AW_LONG_PROCESS);

		$act = $arr["act"];
		$this->$act($arr["id"], $arr["wh_id"], $arr['file']);
		die("all done!");
	}

	static function name_for_status($stat)
	{
		$lut = array(
			warehouse_import_if::STATE_PREPARING => t("Alustamine"),
			warehouse_import_if::STATE_FETCHING  => t("Andmete t&otilde;mbamine"),
			warehouse_import_if::STATE_PROCESSING => t("Andmete t&ouml;&ouml;tlemine"),
			warehouse_import_if::STATE_WRITING => t("Andmete salvestamine"),
			warehouse_import_if::STATE_FINISHING => t("L&otilde;petamine")
		);
		return $lut[$stat];
	}


//////////// actual imports


	/**
		@attrib name=do_prices_import
		@param id required type=int acl=view
		@param post_ru optional
	**/
	function do_prices_import($arr)
	{
		$this->run_backgrounded("real_prices_import", $arr["id"]);
		return $arr["post_ru"];
	}

	function real_prices_import($id)
	{
		$o = obj($id);
		$o->start_prices_import();
	}


	/**
		@attrib name=do_amounts_import
		@param id required type=int acl=view
		@param wh_id optional
		@param post_ru optional
	**/
	function do_amounts_import($arr)
	{
		// for all aw warehouses
		$this->run_backgrounded("real_amounts_import", $arr["id"], $arr["wh_id"]);
		return $arr["post_ru"];
	}

	function real_amounts_import($id, $wh_id)
	{
		$o = obj($id);
		$o->start_amounts_import($wh_id);
	}

	/**
		@attrib name=do_pricelists_import
		@param id required type=int acl=view
		@param post_ru optional
	**/
	function do_pricelists_import($arr)
	{
		$this->run_backgrounded("real_pricelists_import", $arr["id"]);
		return $arr["post_ru"];
	}

	function real_pricelists_import($id)
	{
		$o = obj($id);
		$o->update_price_list();
	}

	/**
		@attrib name=do_dnotes_import
		@param id required type=int acl=view
		@param post_ru optional
	**/
	function do_dnotes_import($arr)
	{
		$this->run_backgrounded("real_dnotes_import", $arr["id"]);
		return $arr["post_ru"];
	}

	function real_dnotes_import($id)
	{
		$o = obj($id);
		$o->update_dnotes();
	}

        /**
                @attrib name=do_bills_import
                @param id required type=int acl=view
                @param post_ru optional
        **/
        function do_bills_import($arr)
        {
                $this->run_backgrounded("real_bills_import", $arr["id"]);
                return $arr["post_ru"];
        }

        function real_bills_import($id)
        {
                $o = obj($id);
                $o->update_bills();
        }

        /**
                @attrib name=do_orders_import
                @param id required type=int acl=view
                @param post_ru optional
        **/
        function do_orders_import($arr)
        {
                $this->run_backgrounded("real_orders_import", $arr["id"]);
                return $arr["post_ru"];
        }

        function real_orders_import($id)
        {
                $o = obj($id);
                $o->update_orders();
        }

	function _get_product_status_tmp($arr)
	{
		//arr($arr['obj_inst']->meta('bg_run_log'));
		//arr($arr['obj_inst']->meta('bg_run_state'));
		$foo = new warehouse_products_import();
		$foo->bg_run_get_property_control($arr);
	//	$foo->bg_run_get_property_status($arr);
		return PROP_OK;
	}

	function _get_product_status_dev_imports($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->start_tree(array(
			"type" => TREE_DHTML,
			"has_root" => 0,
			"tree_id" => "product_status",
			"persist_state" => 1,
		));

		$import = new warehouse_products_import();
		$files = $import->get_chunk_files();

		foreach ($files as $file)
		{
			$parts = explode('_', basename($file));
			$times[$parts[1]] = $parts[1]; // timestamp part of the filename
		}
		rsort($times);
		foreach ($times as $ts)
		{
			$t->add_item(0, array(
				'id' => $ts,
				'name' => (automatweb::$request->arg('sel_ts') == $ts) ? html::strong(date('d.m.Y H:i:s', $ts)) : date('d.m.Y H:i:s', $ts),
				'iconurl' => icons::get_icon_url(CL_MENU),
				'url' => aw_url_change_var(array(
						'sel_ts' => $ts
					))
			));
			
		}

		return PROP_OK;
	}

	function _get_product_status_dev_info($arr)
	{
		// if there is no ongoing imports and none are selected from the left pane, then show the link to start a new import
		// i know about on going imports if i just parse the stat file:
		$import = new warehouse_products_import();

		$import_status = $import->get_import_status();
		if ($import_status == 'started')
		{
			$lines = array(
				t('Staatus: alustatud'),
				sprintf(t('Alguse aeg: %s'), date('d.m.Y H:i:s', $import->get_import_start_time()))
			);
		}
		else
		{
			$lines = array(
				html::href(array(
					'caption' => t('K&auml;ivita toodete import'),
					'url' => $this->mk_my_orb('bg_control', array('id' => $arr['obj_inst']->id(), 'do' => 'start'), 'warehouse_products_import'),
				))
			);

			// tmp OTTO specific stuff here, 
			$lines[] = '---------------';
			$ol = new object_list(array(
				'class_id' => CL_OTTO_IMPORT
			));
			$otto_import = $ol->begin();
			$lines[] = html::href(array(
				'caption' => t('Seadista mis faile imporditakse'),
				'url' => $this->mk_my_orb('change', array('id' => $otto_import->id(), 'group' => 'files', 'return_url' => get_ru()), CL_OTTO_IMPORT)
			)); 
		}

		$arr['prop']['value'] = implode("<br />\n", $lines);



		// if there is something selected from the left pane (sel_ts GET variable has a timestamp) then show info about this import
	}

	function _get_product_status_dev_files($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->define_field(array(
			'name' => 'count',
			'caption' => t('Nr'),
		));
		
		$t->define_field(array(
			'name' => 'filename',
			'caption' => t('Faili nimi'),
		));
		$t->define_field(array(
			'name' => 'status',
			'caption' => t('Staatus'),
			'align' => 'center'
		));


		$import = new warehouse_products_import();

		$file_ts = automatweb::$request->arg('sel_ts');
		$files = array();
		if (empty($file_ts))
		{
			if ($import->get_import_status() == 'started')
			{
				$files = $import->get_chunk_files($import->get_import_start_time());
			}
		}
		else
		{
			$files = $import->get_chunk_files($file_ts);
		}

		$sort_files = array();
		foreach ($files as $file)
		{
			$parts = explode('_', basename($file, '.xml'));
			$sort_files[$parts[2]] = $file;
		}
		ksort($sort_files, SORT_NUMERIC);
		$counter = 0;
		foreach ($sort_files as $file)
		{
			$t->define_data(array(
				'count' => ++$counter,
				'filename' => $file,
				'status' => 'staatus'
			));
		}
		return PROP_OK;
	}

	/**
		@attrib name=do_products_import
		@param id required type=int acl=view
	**/
	function do_products_import($arr)
	{
		$o = obj($arr["id"]);
		$o->start_prod_import($this->mk_my_orb("callback_xml_done", array("id" => $arr["id"])));
	}

	/**
		@attrib name=callback_xml_done
		@param id required type=int acl=view
		@param prod_xml required 
	**/
	function callback_xml_done($arr)
	{
		$o = obj($arr["id"]);
		// process xml peaks nyyd jagama selle faili v2iksemateks tykkideks ja salvestama need failid kuskile kausta 2ra
		$o->process_product_xml($arr["prod_xml"]);

	}

	/**
		@attrib name=process_product_chunk
		@param id required type=int acl=view
		@param file required 
	**/
	function process_product_chunk($id, $wh_id, $file)
	{
		$o = obj($id);
		$o->process_prods_chunk($file);

	}
}


interface warehouse_import_if
{
	const STATE_PREPARING = 1;
	const STATE_FETCHING = 2;
	const STATE_PROCESSING = 3;
	const STATE_WRITING = 4;
	const STATE_FINISHING = 5;

	public function get_warehouse_list();
	public function get_pricelist_xml();
	public function get_prices_xml();
	public function get_dnotes_xml();
	public function get_amounts_xml($wh_id = null);
	public function get_bills_xml($wh_id = null);
}
?>
