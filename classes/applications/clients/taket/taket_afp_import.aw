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

@property import_link type=text store=no
@caption Import

@property warehouse type=relpicker reltype=RELTYPE_WAREHOUSE field=meta method=serialize
@caption Ladu (ilmselt ei peaks siin olema)

@property prod_fld type=relpicker reltype=RELTYPE_PROD_FLD field=meta method=serialize
@caption Toodete kaust (ilmselt ei peaks siin olema)

@property org_fld type=relpicker reltype=RELTYPE_ORG_FLD field=meta method=serialize
@caption Organisatsioonide kaust (ilmselt ei peaks siin olema)

@property amount type=textbox field=meta method=serialize default=5000
@caption Mitu rida korraga importida (ilmselt ei peaks siin olema)

@property code_ctrl type=relpicker reltype=RELTYPE_CODE_CONTROLLER field=meta method=serialize
@caption L&uuml;hikese koodi kontroller


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

	function _get_import_link($arr)
	{
		$links[] = html::href(array(
			'caption' => t('K&auml;ivita import'),
			'url' => $this->mk_my_orb('import_data', array(
				'id' => $arr['obj_inst']->id(),
				'return_url' => get_ru()
			))
		));
		$links[] = html::href(array(
			'caption' => t('Laoseisu import'),
			'url' => $this->mk_my_orb('import_amounts', array(
				'id' => $arr['obj_inst']->id(),
				'return_url' => get_ru()
			))
		));
		$links[] = html::href(array(
			'caption' => t('Hindade import'),
			'url' => $this->mk_my_orb('import_prices', array(
				'id' => $arr['obj_inst']->id(),
				'return_url' => get_ru()
			))
		));
	
		$arr['prop']['value'] = implode(' | ', $links);
	}

	/**
	@attrib name=import_data all_args=1
	**/
	function import_data($arr)
	{
		if($this->can("view", $arr["id"]))
		{
			obj($arr["id"])->get_data($arr);
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

	/**
		@attrib name=get_users_data all_args=1
	**/
	function make_master_import_happy($arr)
	{
		$data = array();
		if($this->can("view", $arr["slave_obj_id"]))
		{
			$data = obj($arr["slave_obj_id"])->get_users_data($arr);
		}
		return $data;
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

	function fetch_product_xml($import_object, $xml_done_callback_url, $wh_id)
	{
		$whl = $this->get_warehouse_list();
		$whd = $whl[$wh_id];

		$url = new aw_uri($whd["info"].'/index.php');
		$url->set_arg('create_products_file', 1);

		$this->print_line("Creating products file ... ", false);
		$result = file_get_contents($url->get());
		$this->print_line($result);
		

		$adr = new aw_uri($whd["info"]."/prods.csv");
		$dest_fld = aw_ini_get('site_basedir').'/files/products.csv';

		$wget_command = 'wget -O '.$dest_fld.' "'.$adr->get().'"';

		$this->print_line("Download products file ... ", false);
		shell_exec($wget_command);
	
		$this->print_line("[done]");

		$xml_done_callback_url .= "&prod_xml=".$this->generate_products_xml();
		die("no go here ".html::href(array("url" => $xml_done_callback_url)));
	}


	function generate_products_xml()
	{
		// TODO should make it configurable
		$path = aw_ini_get('site_basedir').'/files/products.csv';
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
			$product = $xml->addChild('product');
			foreach ($data as $key => $value)
			{
				$product->addChild($key, utf8_encode(str_replace("&", "&amp;", htmlentities($value))));
			}
		}
		$prod_file = aw_ini_get('site_basedir').'/files/products.xml';
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
		$result = file_get_contents($url->get());


		$adr = new aw_uri($wl[1]["info"]."/prods.csv");
		$dest_fld = aw_ini_get('site_basedir').'/files/products.csv';

		$wget_command = 'wget -O '.$dest_fld.' "'.$adr->get().'"';

		$this->print_line("Download products file ... ", false);
		shell_exec($wget_command);
	
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
}

?>
