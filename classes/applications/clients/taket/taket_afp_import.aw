<?php
/*
@classinfo syslog_type=ST_TAKET_AFP_IMPORT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=robert
@tableinfo aw_taket_afp_import master_index=brother_of master_table=objects index=aw_oid

@default group=general
@default table=objects

@property main_tb type=toolbar no_caption=1

@property name type=textbox
@caption Nimi

@property local_products_file type=textbox field=meta method=serialize
@caption Kohalik toodete fail

@property warehouses_table type=table
@caption Laod


@reltype WAREHOUSE value=1 clid=CL_SHOP_WAREHOUSE
@caption Ladu

@reltype CODE_CONTROLLER value=2 clid=CL_CFGCONTROLLER
@caption L&uuml;hikese koodi kontroller

@reltype ORG_FLD value=3 clid=CL_MENU
@caption Organisatsioonide kaust

@reltype PROD_FLD value=4 clid=CL_MENU
@caption Toodete kaust
*/

class taket_afp_import extends class_base implements warehouse_import_if
{
	function taket_afp_import()
	{
		$this->init(array(
			"tpldir" => "applications/clients/taket/taket_afp_import",
			"clid" => CL_TAKET_AFP_IMPORT
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

	function _get_main_tb($arr)
	{
		$tb = &$arr["prop"]["vcl_inst"];
		
		$tb->add_button(array(
			"name" => "import_button",
			"action" => "import_data",
			"img" => "import.gif",
			"tooltip" => t("Impordi tooteandmed"),
		));
	}

	function _get_warehouses_table($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->set_sortable(false);

		$t->define_field(array(
			'name' => 'warehouse',
			'caption' => t('Ladu')
		));

		$t->define_field(array(
			'name' => 'address',
			'caption' => t('Aadress')
		));

		$warehouses = $arr['obj_inst']->get_warehouses();

		foreach ($warehouses as $wh_oid)
		{
			$wh = new object($wh_oid);

			$t->define_data(array(
				'warehouse' => $wh->name(),
				'address' => $wh->comment()
			));
		}

	}

	/**
	@attrib name=import_data all_args=1
	**/
	function import_data($arr)
	{
		if($this->can("view", $arr["id"]))
		{
			obj($arr["id"])->do_import();
		}
		return $arr["post_ru"];
	}

	/**
		@attrib name=import_amounts all_args=1
	**/
	function import_amounts($arr)
	{
		if($this->can("view", $arr["id"]))
		{
			obj($arr["id"])->import_amounts($arr);
		}
		return $arr["post_ru"];
	}

	/**
		@attrib name=import_prices all_args=1
	**/
	function import_prices($arr)
	{
		if($this->can("view", $arr["id"]))
		{
			obj($arr["id"])->import_prices($arr);
		}
		return $arr["post_ru"];
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function get_warehouse_list()
	{
		return array(
			1 => array(
				"name" => "Taketi Kadaka ladu",
				"info" => "http://88.196.208.74:8888/xmlrpc"
			),
			2 => array(
				"name" => "Taketi Punane tn (Lasnamäe) ladu",
				"info" => "http://217.159.218.130:8888/xmlrpc"
			),
			3 => array(
				"name" => "Taketi Paavli ladu",
				"info" => "http://87.119.170.162:8888"
			),
			4 => array(
				"name" => "Taketi Tartu ladu",
				"info" => "http://84.50.96.150:8080/xmlrpc"
			),
			5 => array(
				"name" => "Taketi Pärnu ladu",
				"info" => "http://90.190.6.38:8080"
			),
			6 => array(
				"name" => "Taketi Viljandi ladu",
				"info" => "http://194.126.111.58:8888"
			)
		);
	}

	function fetch_product_xml($import_object, $xml_done_callback_url, $wh_id, $return_file_name)
	{
		$whl = $this->get_warehouse_list();
		$whd = $whl[$wh_id];

		$url = new aw_uri($whd["info"].'/index.php');
		$url->set_arg('create_products_file', 1);

		$this->print_line("Creating products file ... ", false);
		$result = file_get_contents($url->get());
		$this->print_line('['.$result.']');
		

		$adr = new aw_uri($whd["info"]."/prods.csv");
		$dest_fld = aw_ini_get('site_basedir').'/files/warehouse_import/products.csv';

		$wget_command = 'wget -O '.$dest_fld.' "'.$adr->get().'"';

		$this->print_line("Download products file ... ", false);
		shell_exec($wget_command);
	
		$this->print_line("[done]");

		$this->print_line("Generate prods XML file ... ", false);
		$xml_file_name = $this->generate_products_xml();
		$this->print_line("[done]");

		if ($return_file_name)
		{
			return $xml_file_name;
		}

		$xml_done_callback_url .= "&prod_xml=".$xml_file_name;
		die("now go here ".html::href(array("url" => $xml_done_callback_url)));
	}


	function generate_products_xml()
	{
		// TODO should make it configurable
		$path = aw_ini_get('site_basedir').'/files/warehouse_import/products.csv';
		$lines = file($path);

	
		$keys = explode("\t", trim($lines[0]));
		unset($lines[0]);

		foreach ($lines as $line)
		{
			$items = explode("\t", $line);

			foreach ($items as $k => $v)
			{
				$items[$k] = trim(urldecode($v));
			}

			$prod = array_combine($keys, $items);
			$prods[$prod['product_code']] = $prod;

			$suppliers[$prod['supplier_id']] = $prod['supplier_name'];
		}

		$xml = new SimpleXMLElement("<?xml version='1.0'?><products></products>");
		
		foreach ($prods as $code => $data)
		{
			if (++$counter >= 1000)
			{
			//	break;
			}
			$product = $xml->addChild('product');
			foreach ($data as $key => $value)
			{
				$product->addChild($key, utf8_encode(str_replace("&", "&amp;", htmlentities($value))));
			}
		}
		$prod_file = aw_ini_get('site_basedir').'/files/warehouse_import/products.xml';
		$xml->asXML($prod_file);

		return $prod_file;
	}

	private function print_line($str, $break = true)
	{
		echo $str;
		if ($break === true)
		{
			echo "<br />\n";
		}
		file_put_contents('/tmp/taket_afp_import.log', $str."\n", FILE_APPEND);
		flush();
	}

	public function get_pricelist_xml()
	{
		$wl = $this->get_warehouse_list();
//		$whs = $this->get_warehouses();
//		$wh = new object(reset($whs));
		$wh = reset($wl);
		$url = new aw_uri($wh['info'].'/index.php?get_discount_rules=1');
		$data = unserialize(file_get_contents($url->get()));

		$xml = new SimpleXMLElement("<customer_discounts />");

		foreach ($data as $value)
		{
			$product_cat = $xml->addChild('product_category');
			foreach ($value as $k => $v)
			{
				switch( $k )
				{
					case 'KAT_KOODI':
						$product_cat->addChild('name', utf8_encode($v));
						break;

					case 'KAT_ALARAJA':
						$product_cat->addChild('lower_limit', utf8_encode($v));
						break;

					case 'KAT_YLARAJA':
						$product_cat->addChild('upper_limit', utf8_encode($v));
						break;

					default:
						$client_cat = $product_cat->addChild('client_category');
						$client_cat->addChild('name', str_replace('KAT_ALE', '', $k));
						$client_cat->addChild('value', $v);
				}
			}
		}
		return $xml->asXML();
	}

	function get_amounts_xml($wh_id = null)
	{
		$wl = $this->get_warehouse_list();
		$url = $wl[$wh_id]["info"];

		$turl = new aw_uri($url.'/index.php');
		$turl->set_arg('create_amounts_file', 1);
		get_instance("http")->get($turl->get());


		$amounts_data = file($url."/amounts.csv");
		$total = count($amounts_data);
			
		$xml = new SimpleXMLElement("<amounts />");	

		foreach ($amounts_data as $line)
		{
			$items = explode("\t", $line);	
			$p = $xml->addChild("product");
			$p->addChild("product_code", trim($items[0]));
			$p->addChild("amount", trim($items[1]));
		}
		return $xml->asXML();
	}

	function get_prices_xml()
	{
		aw_set_exec_time(AW_LONG_PROCESS);

		$wl = $this->get_warehouse_list();

		$url = new aw_uri($wl[1]["info"].'/index.php');
		$url->set_arg('create_products_file', 1);

		$this->print_line("Creating products file ... ", false);
	//	$result = file_get_contents($url->get());


		$adr = new aw_uri($wl[1]["info"]."/prods.csv");
		$dest_fld = aw_ini_get('site_basedir').'/files/products.csv';

		$wget_command = 'wget -O '.$dest_fld.' "'.$adr->get().'"';

		$this->print_line("Download products file ... ", false);
//		shell_exec($wget_command);
	
		$this->print_line("[done]");


		$lines = file($dest_fld);
		unset($lines[0]);

		$xml = new SimpleXMLElement("<amounts />");	

		$result = array();
		foreach ($lines as $line)
		{
			$fields = explode("\t", $line);
			$p = $xml->addChild("product");
			$p->addChild("product_code", trim($fields[0]));
			$p->addChild("price", trim($fields[6]));
			$p->addChild("special_price", trim($fields[7]));
		}

		return $xml->asXML();
	}

	function get_dnotes_xml($wh_id = null)
	{
		$wl = $this->get_warehouse_list();
		$url = $wl[4]["info"];

		// fetch rows data
		$turl = new aw_uri($url.'/index.php');
		$turl->set_arg('get_saatelehed_read', 1);

		$f = fopen(aw_ini_get("cache.page_cache")."/taket_temp_read.csv", "w");
		fwrite($f, get_instance("http")->get($turl->get()));
		fclose($f);

		$f = fopen(aw_ini_get("cache.page_cache")."/taket_temp_read.csv", "r");
		$first = true;
		$rows = array();
		while (($items = fgetcsv($f, 0, "\t", "\"")) !== false)
		{
			if ($first)
			{
				$first = false;
				continue;
			}
			$rows[trim($items[0])][] = $items;
		}
		fclose($f);


		$turl = new aw_uri($url.'/index.php');
		$turl->set_arg('get_saatelehed', 1);

		$f = fopen(aw_ini_get("cache.page_cache")."/taket_temp.csv", "w");
		fwrite($f, get_instance("http")->get($turl->get()));
		fclose($f);

		$f = fopen(aw_ini_get("cache.page_cache")."/taket_temp.csv", "r");
			
		$xml = new SimpleXMLElement("<dnotes />");	

		$first = true;
		while (($items = fgetcsv($f, 0, "\t", "\"")) !== false)
		{
//			$items = explode("\t", $line);	
			if ($first)
			{
				$first = false;
//echo (dbg::dump($items));
				continue;
			}
//echo(dbg::dump($items));

			list($y, $m, $d) = explode("-", trim($items[5]));
			$deld = mktime(0, 0, 0, $m, $d, $y);

			list($y, $m, $d) = explode("-", trim($items[4]));
			$eld = mktime(0, 0, 0, $m, $d, $y);

			$p = $xml->addChild("dnote");
			$p->addChild("number", trim($items[0]));
			$p->addChild("customer_ext_id", trim($items[1]));
			$p->addChild("delivery_date", $deld);
			$p->addChild("enter_date", $eld);

// VALMIS, LASKUTETTU

			if (trim($items[3]) == "VALMIS")
			{
				$p->addChild("state", 2);
			}
			else
			if (trim($items[3]) == "LASKUTETTU")
			{
				$p->addChild("state", 1);
			}
	

			$p->addChild("from_warehouse", 6411);
			$p->addChild("impl", 131);
			$p->addChild("currency", currency::find_by_symbol(trim($items[18]))->id());

			foreach(safe_array($rows[trim($items[0])]) as $row)
			{
				$xr = $p->addChild("rows");
				$xr->addChild("prod_code", trim($row[7]));
				$xr->addChild("price", trim($row[9]));
				$xr->addChild("warehouse", 6411);
				$xr->addChild("amount", trim($row[10]));
			}
				
		}
		return $xml->asXML();
	}

	public function get_bills_xml($wh_id = null)
	{
		$wl = $this->get_warehouse_list();
		$url = $wl[6]["info"];

		// fetch rows data
		$turl = new aw_uri($url.'/index.php');
		$turl->set_arg('get_bill_rows', 1);

/*		$f = fopen(aw_ini_get("cache.page_cache")."/taket_temp_bread.csv", "w");
		fwrite($f, get_instance("http")->get($turl->get()));
		fclose($f);*/

		$f = fopen(aw_ini_get("cache.page_cache")."/taket_temp_bread.csv", "r");
		$first = true;
		$rows = array();
		while (($items = fgetcsv($f, 0, "\t", "\"")) !== false)
		{
			if ($first)
			{
				$first = false;
				continue;
			}
			$rows[trim($items[0])][] = $items;
		}
		fclose($f);


		$turl = new aw_uri($url.'/index.php');
		$turl->set_arg('get_bills', 1);

	/*	$f = fopen(aw_ini_get("cache.page_cache")."/taket_btemp.csv", "w");
		fwrite($f, get_instance("http")->get($turl->get()));
		fclose($f);*/

		$f = fopen(aw_ini_get("cache.page_cache")."/taket_btemp.csv", "r");
			
		$xml = new SimpleXMLElement("<bills />");	

		$first = true;
		while (($items = fgetcsv($f, 0, "\t", "\"")) !== false)
		{
			if ($first)
			{
				$first = false;
				continue;
			}

			list($y, $m, $d) = explode("-", trim($items[15]));
			$deld = mktime(0, 0, 0, $m, $d, $y);

			$p = $xml->addChild("bill");
			
			$p->addChild("name", trim($items[0]));
			$p->addChild("bill_no", trim($items[0]));
			$p->addChild("impl", 131);
			$p->addChild("bill_date", $deld);
			$p->addChild("bill_due_date_days", trim($items[21]));
			$p->addChild("sum", trim($items[11]));
			$p->addChild("currency", currency::find_by_symbol(trim($items[19]))->id());
			if (trim($items[11]) > 0)
			{
				$p->addChild("disc", (trim($items[12])*100.0) / trim($items[11]));
			}
			$p->addChild("approved", 1);
			$p->addChild("customer_ext_id", trim($items[3]));
			$p->addChild("warehouse", 6414);

			foreach(safe_array($rows[trim($items[0])]) as $row)
			{
				$xr = $p->addChild("rows");
				$xr->addChild("name", trim($row[1]));
				$xr->addChild("amt", trim($row[12]));
				$xr->addChild("prod", trim($row[9]));
				$xr->addChild("price", trim($row[11]));
				$xr->addChild("desc", trim($row[10]));
			}
				
		}
		return $xml->asXML();
	}


	public function get_orders_xml($wh_id = null)
	{
		$wl = $this->get_warehouse_list();
		$url = $wl[6]["info"];

		// fetch rows data
		$turl = new aw_uri($url.'/index.php');
		$turl->set_arg('get_tellimused_read', 1);

		$f = fopen(aw_ini_get("cache.page_cache")."/taket_temp_oread.csv", "w");
		fwrite($f, get_instance("http")->get($turl->get()));
		fclose($f);

		$f = fopen(aw_ini_get("cache.page_cache")."/taket_temp_oread.csv", "r");
		$first = true;
		$rows = array();
		while (($items = fgetcsv($f, 0, "\t", "\"")) !== false)
		{
			if ($first)
			{
				$first = false;
				continue;
			}
			$rows[trim($items[0])][] = $items;
		}
		fclose($f);


		$turl = new aw_uri($url.'/index.php');
		$turl->set_arg('get_tellimused', 1);

		$f = fopen(aw_ini_get("cache.page_cache")."/taket_otemp.csv", "w");
		fwrite($f, get_instance("http")->get($turl->get()));
		fclose($f);


		$f = fopen(aw_ini_get("cache.page_cache")."/taket_otemp.csv", "r");
			
		$xml = new SimpleXMLElement("<orders />");	

		$first = true;
		while (($items = fgetcsv($f, 0, "\t", "\"")) !== false)
		{
			if ($first)
			{
				$first = false;
				continue;
			}
			list($y, $m, $d) = explode("-", trim($items[4]));
			$deld = mktime(0, 0, 0, $m, $d, $y);

			$p = $xml->addChild("order");
			
			$p->addChild("name", trim($items[0]));
			$p->addChild("number", trim($items[0]));
			$p->addChild("date", $deld);
			$p->addChild("deal_date", $deld);
			$p->addChild("currency", currency::find_by_symbol(trim($items[18]))->id());
			$p->addChild("warehouse", 6414);
			$p->addChild("order_status", trim($items[3]) == "HYVÄKSYTTY");
			$p->addChild("purchaser_ext_id", trim($items[1]));

			foreach(safe_array($rows[trim($items[0])]) as $row)
			{
				$xr = $p->addChild("rows");
				$xr->addChild("name", trim($row[1]));
				$xr->addChild("prod", trim($row[5]));
				$xr->addChild("warehouse", 6414);
				$xr->addChild("date", $deld);
				$xr->addChild("price", trim($row[7]));
				$xr->addChild("amount", trim($row[8]));
				$xr->addChild("prod_name", trim($row[6]));
			}
				
		}
		return $xml->asXML();
	}
}

?>
