<?php

namespace automatweb;
// taket_ebasket.aw - Ostukorv
/*

@classinfo relationmgr=yes

@default table=objects
@default group=general

*/

//
class taket_ebasket extends class_base
{
	const AW_CLID = 241;

	//defined in aw ini
	var $ebasket_parent_id; //location of the baskets
	var $ebasket_item_parent_id; //location of the basket items
	var $order_item_parent_id;

	var $current_ebasket_identificator = 'thisnevercomes';

	function taket_ebasket()
	{
		$this->ebasket_parent_id = aw_ini_get('taket_ebasket.ebasket_parent_id');
		$this->ebasket_item_parent_id = aw_ini_get('taket_ebasket.ebasket_item_parent_id');
		$this->order_item_parent_id = aw_ini_get('taket_order.order_item_parent_id');
		// change this to the folder under the templates folder, where this classes templates will be,
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "taket/taket_ebasket",
			"clid" => CL_TAKET_EBASKET
		));
		lc_site_load('taket_ebasket',&$this);
	}

	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}


	/**
		@attrib name=show params=name default="0"
		@param id optional
		@param product_code optional
		@param product_name optional
		@param price optional
		@param discount optional
		@param finalprice optional
		@param quantity optional
		@param check_all_stocks optional
	**/
	function show($arr)
	{

		// extended logging means, that almost every step during ebasket saving process, is logged into files/logs directory
		// log files naming scheme is default (log-YYYY-MM-DD.log)
		$taket_extended_log = aw_ini_get('taket_extended_log');

/*
if ($_GET['dd'] && aw_global_get('uid') != ''){
	$sql = 'select * from objects where class_id=245 and status=0';
	$this->db_query($sql);
	$ids = array();
	while ($row = $this->db_next()){
		$ids[$row['oid']] = $row['oid'];
	}
	if ($_GET['fatal'] && !empty($ids)){
		$this->db_query('delete from objects where oid in ('.implode(',', $ids).')');
		$this->db_query('delete from taket_ebasket_item where id in ('.implode(',', $ids).')');
		$this->db_query('delete from aliases where source in ('.implode(',', $ids).') or target in ('.implode(',', $ids).')');
	}
	if (empty($ids)){
		echo "tyhi<br>";
	}else{
		arr($ids);
	}

exit('done');

}
*/

		require(aw_ini_get("basedir")."addons/ixr/IXR_Library.inc.php");
		//let it be
		$ob = new object($arr["id"]);
		//needed template & settings
		$this->read_template("show.tpl");
		$this->sub_merge = 0;

		//current user
		$user_id = users::get_oid_for_uid(aw_global_get('uid'));

		//another thing written by the script
		$this->vars(array(
			"name" => $ob->prop("name"),
		));

		//ebasket
		$start_time = $this->microtime_float();
		$ebasket = $this->get_users_active_ebasket($user_id);
		$end_time = $this->microtime_float();

		if ($taket_extended_log)
		{
			$this->site_log($_SERVER['REMOTE_ADDR'].'['.aw_global_get('uid').'][taket_ebasket::show][1] get_users_active_ebasket ('.$user_id.') = '.(float)($end_time - $start_time));
		}

		//sort by logic
//		$sortBy = 'product_code'; // default sort by product_code
		$sortBy = 'id';
		$dirs= 'asc';
		$options = array(
			'product_code',
			'product_name',
			'price',
			'discount',
			'finalprice',
			'quantity'
		);

		$css = array(
			'product_codecss' => 'listTitle',
			'product_namecss' => 'listTitle',
			'pricecss' => 'listTitle',
			'discountcss' => 'listTitle',
			'finalpricecss' => 'listTitle',
			'quantitycss' => 'listTitle'
		);
		$dir = array(
			'product_codedir' => 'asc',
			'product_namedir' => 'asc',
			'pricedir' => 'asc',
			'discountdir' => 'asc',
			'finalpricedir' => 'asc',
			'quantitydir' => 'asc'
		);

		if(in_array($arr['sort'],$options))
		{
			$sortBy=$arr['sort'];
			$dirs=($arr['dir']=='asc')?'desc':'asc';
			$css[$sortBy.'css']='listTitlesort';
			$dir[$sortBy.'dir']=($arr['dir']=='asc')?'desc':'asc';
			if($arr['sort']=='order_id')
			{
				$sortBy='id';
				$dirs = ($arr['dir']=='asc')?'desc':'asc';
			}
		}
// if default sort by column is product_code, then add proper css style
//		else
//		{
//			$css['product_codecss']='listTitlesort';
//		}

		if($arr['sort']!='finalprice')
		{
			$sortBy='taket_ebasket_item.'.$sortBy.' '.$dirs;
		}
		else
		{
			$sortBy='(taket_ebasket_item.price*(1-(discount/100))) '.$dirs;
		}

		$this->vars($css);
		$this->vars($dir);
		$this->vars(array(
			'sort'=>$arr['sort'],
			'dir'=>$arr['dir']
		));
		//end of sort_by logic
		//load all the items
		$start_time = $this->microtime_float();
		$ol = new object_list(array(
			'parent'=>$this->ebasket_item_parent_id,
			'class_id' => CL_TAKET_EBASKET_ITEM,
			'ebasket_id' => $ebasket->id(),
			'lang_id' => array(),
			'sort_by' => $sortBy
		));
		$end_time = $this->microtime_float();

		if ($taket_extended_log)
		{
			$this->site_log($_SERVER['REMOTE_ADDR'].'['.aw_global_get('uid').'][taket_ebasket::show][2] [storage] get all ebasket items = '.(float)($end_time - $start_time));
		}

		if ($_GET['clear_basket'])
		{
			aw_disable_acl();
			foreach ($ol->arr() as $o)
			{
				$o->delete(true);
			}
			aw_restore_acl();
			exit(1);
		}

		$i=0;
		$content='';

		//have to gather all the product_codes so i won't
		//have to do product_code number of xml-rpc queries
		//opening http connections cost, 1 is cool, 10 sux
		$productCodes=array();
		$productCodesByHost = array();
		$grouped_basket_items = array();
		$start_time = $this->microtime_float();
		for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
		{
			if(!$o->prop('ebasket_id'))
			{
				continue;
			}
			$ebasket_name = $o->prop('ebasket_name');
			if(!strlen($ebasket_name))
			{
				$ebasket_name = $this->current_ebasket_identificator;
			}
			if(!array_key_exists($ebasket_name,$grouped_basket_items))
			{
				$grouped_basket_items[$ebasket_name] = array();
			}

			$grouped_basket_items[$ebasket_name][$o->prop('product_code')] = $o->prop('product_code');
			$productCodes[] = $o->prop('product_code');

			// kui check_all_stocks parameeter on m2rgitud, siis kontrollitakse
			// k6ikide toodete saadavust k6igist ladudest:
			if ($arr['check_all_stocks'])
			{
				$productCodesByHost[-1][] = $o->prop('product_code');
			}
			else
			{
				$productCodesByHost[(int)$o->meta('asukoht')][] = $o->prop('product_code');
			}
		}
		$end_time = $this->microtime_float();

		if ($taket_extended_log)
		{
			$this->site_log($_SERVER['REMOTE_ADDR'].'['.aw_global_get('uid').'][taket_ebasket::show][3] [storage] gathering all the ebasket items product codes = '.(float)($end_time - $start_time));
		}

		$hosts = aw_ini_get('taket.xmlrpchost');
		$path = aw_ini_get("taket.xmlrpcpath");
		$port = aw_ini_get("taket.xmlrpcport");
if (!$_GET['dragut']){
		$data = array();

		if ($taket_extended_log)
		{
			$this->site_log($_SERVER['REMOTE_ADDR'].'['.aw_global_get('uid').'][taket_ebasket::show][4] [XML-RPC] getting stock info for items');
		}

		foreach($hosts as $key => $host)
		{

			// if there is no products in ebasket, which are searched from $key host (location) and there are
			// no products in ebasket, which are searched from all hosts (locations), then there is no point to
			// make a XML-RPC call --dragut
			if ( empty($productCodesByHost[$key]) && empty($productCodesByHost[-1]) )
			{
				continue;
			}

			$client = new IXR_Client($host, $path[$key], $port[$key]);
//$client->debug = 1;
			$query_start_time = $this->microtime_float();
	 	//	$client->query('server.getProductInfoArr', $productCodes);

			// lets merge those product codes, which are searched from $key host and which are searched from all
			// hosts (locations) --dragut
	 		$client->query('server.getProductInfoArr', array_merge(safe_array($productCodesByHost[$key]), safe_array($productCodesByHost[-1])));
			$query_end_time = $this->microtime_float();

			$getresponse_start_time = $this->microtime_float();
			$tdata = $client->getResponse();
			$getresponse_end_time = $this->microtime_float();
			if ($taket_extended_log)
			{
				$this->site_log($_SERVER['REMOTE_ADDR'].'['.aw_global_get('uid').'][taket_ebasket::show][4] [XML-RPC] host: '.$key.' ('.$host.') query/getResponse = '.(float)($query_end_time - $query_start_time).'/'.(float)($getresponse_end_time - $getresponse_start_time));
			}

			if(!is_array($tdata))
			{
				$tdata = array();
			}
			foreach($tdata as $tkey => $tdat)
			{
				$no_add = false;
				foreach($data as $datkey => $val)
				{
					if($val["product_code"] == $tdat["product_code"])
					{
						$no_add = true;
						$data[$datkey]["inStock".$key] = (int)$tdat["inStock"];
						break;
					}
				}
				if(!$no_add)
				{
					$tdat["inStock".$key] = (int)$tdat["inStock"];
					$data[$tkey] = $tdat;
				}
			}
		}
}
		//unset the key that should always be first
		$should_be_added = false;
		if(array_key_exists($this->current_ebasket_identificator,$grouped_basket_items))
		{
			unset($grouped_basket_items[$this->current_ebasket_identificator]);
			$should_be_added = true;
		}
		//let's sort $grouped_basket_items by key
		ksort($grouped_basket_items);
		if($should_be_added)
		{
			$grouped_basket_items = array_reverse($grouped_basket_items);
			$grouped_basket_items[$this->current_ebasket_identificator] = array();
			$grouped_basket_items = array_reverse($grouped_basket_items);
		}
if (!$_GET['dragut']){
		//divide and conquer
		foreach($data as $key=>$value)
		{
			//the fetched data will be organized into the format of the
			//grouped_basket_item
			foreach($grouped_basket_items as $key2=>$value2)
			{

				if(array_key_exists($key,$value2))
				{
					$grouped_basket_items[$key2][$key] = $value;
				}
			}
		}
}
		//have to query the different transportation types from AFP
		//simple :)
		$data2 = array();
		$client = new IXR_Client($hosts[0], $path[0], $port[0]);
		$query_start_time = $this->microtime_float();
		$client->query('server.getTransportTypes',array());
		$query_end_time = $this->microtime_float();
		$getresponse_start_time = $this->microtime_float();
		$data2 = $client->getResponse();
		$getresponse_end_time = $this->microtime_float();

		if ($taket_extended_log)
		{
			$this->site_log($_SERVER['REMOTE_ADDR'].'['.aw_global_get('uid').'][taket_ebasket::show][5] [XML-RPC] getting transportation types query/getResponse = '.(float)($query_end_time - $query_start_time).'/'.(float)($getresponse_end_time - $getresponse_start_time));
		}

		if(!is_array($data2))
		{
			$data2 = array();
		}
		$transportation_html='';
		foreach($data2 as $value)
		{
			if($value['transport_id']==$_SESSION['TAKET']['transport_type'])
			{
				$value['tselected']='selected';
			}
			else
			{
				$value['tselected']='';
			}
			$this->vars($value);
			$transportation_html.=$this->parse('transport');
		}

		$tmpFlag=1;
		$ebasket_html = '';
		$unique_form_suffix = 1;
		foreach($grouped_basket_items as $key=>$value)
		{
			$priceWithoutTax=0;
			$tax=0;
			$priceGrandTotal=0;
			$this->vars(array(
				'ebasket_name_normal'=>($key==$this->current_ebasket_identificator)?"":$key,
				'ebasket_name_hidden'=>urlencode($key),
				'ebasket_name'=>'unique'.($unique_form_suffix++)
			));
			for($o=$ol->begin();!$ol->end();$o=$ol->next())
			{
				//ebaskets item
				if($o->prop('ebasket_name')!=$key && $key!=$this->current_ebasket_identificator)
				{
					continue;
				}
				if(!$o->prop('ebasket_id'))
				{
					continue;
				}

				$tmp_product_code = $o->prop("product_code");
if (!$_GET['dragut'])
{
				// stock #1
				if (isset($data[$tmp_product_code]['inStock0']))
				{
					if($data[$tmp_product_code]['inStock0'] >= $o->prop('quantity'))
					{
						// enough products in stock
						$this->vars(array('instock_parsed0'=>$this->parse('instockyes')));
					}
					else
					{
						if ($data[$tmp_product_code]['inStock0'] > 0)
						{
							// not enough products in stock
							$this->vars(array('instock_parsed0' => $this->parse('instockpartially')));

						}
						else
						{
							// no products in stock
							$this->vars(array('instock_parsed0' => $this->parse('instockno')));
						}
					}
				}
				else
				{
					$this->vars(array('instock_parsed0' => '---'));
				}

				// stock #2
				if (isset($data[$tmp_product_code]['inStock1']))
				{
					if($data[$tmp_product_code]['inStock1'] >= $o->prop('quantity'))
					{
						// enough products in stock
						$this->vars(array('instock_parsed1' => $this->parse('instockyes')));
					}
					else
					{
						if ($data[$tmp_product_code]['inStock1'] > 0)
						{
							// not enough products in stock
							$this->vars(array('instock_parsed1' => $this->parse('instockpartially')));

						}
						else
						{
							// no products in stock
							$this->vars(array('instock_parsed1' => $this->parse('instockno')));
						}
					}
				}
				else
				{
					$this->vars(array('instock_parsed1' => '---'));
				}

				// stock #3
				if (isset($data[$tmp_product_code]['inStock2']))
				{
					if($data[$tmp_product_code]['inStock2'] >= $o->prop('quantity'))
					{
						$this->vars(array('instock_parsed2' => $this->parse('instockyes')));
					}
					else
					{
						if ($data[$tmp_product_code]['inStock2'] > 0)
						{
							// not enough products in stock
							$this->vars(array('instock_parsed2' => $this->parse('instockpartially')));
						}
						else
						{
							// no products in stock
							$this->vars(array('instock_parsed2' => $this->parse('instockno')));
						}
					}
				}
				else
				{
					$this->vars(array('instock_parsed2' => '---'));
				}

				// stock #4
				if (isset($data[$tmp_product_code]['inStock3']))
				{
					if($data[$tmp_product_code]['inStock3'] >= $o->prop('quantity'))
					{
						$this->vars(array(
							'instock_parsed3' => $this->parse('instockyes')
						));
					}
					else
					{
						if ($data[$tmp_product_code]['inStock3'] > 0)
						{
							// not enough products in stock
							$this->vars(array(
								'instock_parsed3' => $this->parse('instockpartially')
							));

						}
						else
						{
							// no products in stock
							$this->vars(array(
								'instock_parsed3' => $this->parse('instockno')
							));
						}
					}
				}
				else
				{
					$this->vars(array('instock_parsed3' => '---'));
				}
				// stock #5
				if (isset($data[$tmp_product_code]['inStock4']))
				{
					if($data[$tmp_product_code]['inStock4'] >= $o->prop('quantity'))
					{
						$this->vars(array(
							'instock_parsed4' => $this->parse('instockyes')
						));
					}
					else
					{
						if ($data[$tmp_product_code]['inStock4'] > 0)
						{
							// not enough products in stock
							$this->vars(array(
								'instock_parsed4' => $this->parse('instockpartially')
							));

						}
						else
						{
							// no products in stock
							$this->vars(array(
								'instock_parsed4' => $this->parse('instockno')
							));
						}
					}
				}
				else
				{
					$this->vars(array('instock_parsed4' => '---'));
				}
				// stock #6
				if (isset($data[$tmp_product_code]['inStock5']))
				{
					if($data[$tmp_product_code]['inStock5'] >= $o->prop('quantity'))
					{
						$this->vars(array(
							'instock_parsed5' => $this->parse('instockyes')
						));
					}
					else
					{
						if ($data[$tmp_product_code]['inStock5'] > 0)
						{
							// not enough products in stock
							$this->vars(array(
								'instock_parsed5' => $this->parse('instockpartially')
							));

						}
						else
						{
							// no products in stock
							$this->vars(array(
								'instock_parsed5' => $this->parse('instockno')
							));
						}
					}
				}
				else
				{
					$this->vars(array('instock_parsed5' => '---'));
				}
}
else
{
	$instock_not_available = $this->parse('instocknotavailable');
	$this->vars(array(
		'instock_parsed0' => $instock_not_available,
		'instock_parsed1' => $instock_not_available,
		'instock_parsed2' => $instock_not_available,
		'instock_parsed3' => $instock_not_available,
		'instock_parsed4' => $instock_not_available,
		'instock_parsed5' => $instock_not_available,
	));
}
				$tarjoushinta = $data[$o->prop('product_code')]["tarjoushinta"];
				if($tarjoushinta <= 0)
				{
					$tarjoushinta = "-";
					$price = $o->prop('quantity') * ( $o->prop('price') * ( 1 - $o->prop('discount') / 100 ) );
				}
				else
				{
					$price = $tarjoushinta * $o->prop('quantity');

					// i need to remember the tarjoushinta somehow ... so lets save it into
					// taket_ebasket_item-s meta info ... perhaps it is useful --dragut
					$o->set_meta('tarjoushinta', $tarjoushinta);
					$o->save();
					// ---
					$tarjoushinta = number_format($tarjoushinta, 2, '.', '');
				}
				$this->vars(array(
						'product_code' => $o->prop('product_code'),
						'product_name' => $o->prop('product_name'),
						'price' => number_format($o->prop('price'),2,'.',''),
						'discount' => $o->prop('discount'),
						'tarjoushinta' => $tarjoushinta,
						'finalprice' => number_format(((1-$o->prop('discount')/100)*$o->prop('price')),2,'.',''),
						'quantity' => $o->prop('quantity'),
						//'inStock' => $msg,
						'i' => $i++,
						'tmpFlag' => $tmpFlag
				));

//				$priceWithoutTax += $o->prop('quantity') * ( $o->prop('price') * ( 1 - $o->prop('discount') / 100 ) );
				$priceWithoutTax += $price;

				$content.=$this->parse('toode');
			}

			$this->vars(array('toodeParsed'=>$content));
			$content='';
			//assign the variables calculated in the iteration
			$this->vars(array(
					'priceWithoutTax' => number_format($priceWithoutTax/1.18,2,'.',''),
					'tax'	=> number_format(round($priceWithoutTax/1.18*0.18,2),2,'.',''),
					'priceGrandTotal'	=> number_format($priceWithoutTax,2,'.','')
					));

			$this->vars(array(
				'reforb' => $this->mk_reforb('save_ebasket',
														array('no_reforb'=>true))
				));
			//save button was just pressed
			//it's okay to show the order form
			//if($arr['saved'])
			$this->vars(array(
				'reforb2' => $this->mk_reforb('send_order',
													array('no_reforb'=>true)),
				'eesperenimi' => $_SESSION['TAKET']['eesperenimi'],
				'kontakttelefon' => $_SESSION['TAKET']['gsm'],
				'info' => $_SESSION['TAKET']['info'],
				'transportParsed' => $transportation_html
				));

			if($arr['inputErr'] && $arr['inputErr']!=2)
			{
				$this->vars(array('inputErrParsed'=>$this->parse('inputErr')));
			}
			else if($arr['inputErr']==2)
			{
				$this->vars(array('inputErrParsed'=>$this->parse('inputErr2')));
			}
			$this->vars(array('vormistaParsed'=>$this->parse('vormista')));
			$ebasket_html.=$this->parse('ebasket');
		}
		$this->vars(array('ebaskets'=>$ebasket_html));
		return $this->parse();
	}

	/**

		@attrib name=send_order params=name default="0"

		@param transport optional
		@param kontakttelefon optional
		@param eesperenimi optional
		@param transport_name optional
		@param info optional
		@param ebasket_name required
		@param location optional
		@param transport_name optional

		@returns
		@comment
	**/
	function send_order($arr)
	{
		aw_disable_acl();

		$arr['ebasket_name'] = urldecode($arr['ebasket_name']);
		//if all the fields weren't filled
		if(!($arr['kontakttelefon'] && $arr['eesperenimi'] && $arr['transport']))
		{
			return $this->mk_my_orb('show',array('inputErr'=>1,'saved'=>1),'taket_ebasket');
		}
		//else continue


		//send the order to the AFP
		require(aw_ini_get("basedir")."addons/ixr/IXR_Library.inc.php");

		$hosts = aw_ini_get('taket.xmlrpchost');
		$path = aw_ini_get("taket.xmlrpcpath");
		$port = aw_ini_get("taket.xmlrpcport");
		$location_names = aw_ini_get("taket.location_name");
		// i need this location key thingie here, cause i need to send emaild later on
		// and this way i can get the correct emails from aw.ini
/*
                switch($arr['location'])
                {
                        case "Kadaka tee":
			//	$client = new IXR_Client($hosts[0], $path[0], $port[0]);
				$location_key = 0;
                                break;
                        case "Punane tn":
			//	$client = new IXR_Client($hosts[1], $path[1], $port[1]);
				$location_key = 1;
                                break;
			case "Tartu":
			//	$client = new IXR_Client($hosts[2], $path[2], $port[2]);
				$location_key = 2;
				break;
                        default:
			//	$client = new IXR_Client($hosts[0], $path[0], $port[0]);
				$location_key = 0;
		}
*/
/*
		if (aw_global_get("uid") == "110")
		{
			arr($hosts[$location_key]);
			arr($path[$location_key]);
			arr($port[$location_key]);
		}
*/
		$location_key = (int)$arr['location'];
		$client = new IXR_Client($hosts[$location_key], $path[$location_key], $port[$location_key]);
		//let's gather the info to be sent
		$userinfo = array(); //i'll know about it more tomorrow
		//basket info
		$user_id = users::get_oid_for_uid(aw_global_get('uid'));
		$ebasket = $this->get_users_active_ebasket($user_id);

		$start_time = $this->microtime_float();
		$ol = new object_list(array(
			'parent' => $this->ebasket_item_parent_id,
			'class_id' => CL_TAKET_EBASKET_ITEM,
			'lang_id' => array(),
			'ebasket_id' => $ebasket->id(),
			'ebasket_name' => $arr['ebasket_name'],
		));
		$end_time = $this->microtime_float();

		if ($taket_extended_log)
		{
			$this->site_log($_SERVER['REMOTE_ADDR'].'['.aw_global_get('uid').'][taket_ebasket::send_order][1] [storage] get all ebasket items = '.(float)($end_time - $start_time));
		}

		$rows = array();
		$orderPrice=0;
		$orderPriceD=0;
		$start_time = $this->microtime_float();
		for($o=$ol->begin();!$ol->end();$o=$ol->next())
		{
			if($o->prop('ebasket_id')==$ebasket->id())
			{
				$price = "";
				$tarjoushinta = (int)$o->meta('tarjoushinta');
				if ( $tarjoushinta > 0 )
				{
					$price = $tarjoushinta;
					$orderPriceD += $price * $o->prop('quantity');
				}
				else
				{
					$price = $o->prop('price');
					$orderPriceD += ( $price * ( 1 - $o->prop('discount') / 100 ) ) * $o->prop('quantity');

				}

				$row = array(
					'product_code' => $o->prop('product_code'),
					'quantity' => $o->prop('quantity'),
				//	'price' => $o->prop('price'),
					'price' => $price,
					'product_name' => $o->prop('product_name'),
					'discount'	=> $o->prop('discount'),
					'supplier_id' => $o->prop('supplier_id'),
				);
				$rows[] = $row;
			//	$orderPriceD+=($o->prop('price')*(1-$o->prop('discount')/100))*$o->prop('quantity');
			//	$orderPriceD += ( $price * ( 1 - $o->prop('discount') / 100 ) ) * $o->prop('quantity');

			//	$orderPrice+=$o->prop('price')*$o->prop('quantity');
				$orderPrice += $price * $o->prop('quantity');

			}
		}
		$end_time = $this->microtime_float();

		if ($taket_extended_log)
		{
			$this->site_log($_SERVER['REMOTE_ADDR'].'['.aw_global_get('uid').'][taket_ebasket::send_order][2] [storage] get info from items  = '.(float)($end_time - $start_time));
		}

		if(!sizeof($rows))
		{
			return $this->mk_my_orb('show',array('inputErr'=>2,'saved'=>1),'taket_ebasket');
		}

		//store the order locally
		//save the order locally for later viewing

		$start_time = $this->microtime_float();
		$obj = new object();
		$obj->set_class_id(CL_TAKET_ORDER);
		$obj->set_parent(aw_ini_get('taket_order.order_parent_id'));
		$obj->set_prop('price', $orderPriceD);
		$obj->set_prop('comments',$arr['info']);
		$obj->set_prop('transport',$arr['transport_name']);
		$obj->set_prop('timestmp',time());
		$obj->set_prop('status', 'Edastatud');
		$obj->set_prop('contact', $arr['eesperenimi']);
		$obj->set_prop('user_id', users::get_oid_for_uid(aw_global_get('uid')));
		$obj->set_prop("location", html_entity_decode($location_names[$location_key]));
		$obj->save();
		$orderId=$obj->id();
		$end_time = $this->microtime_float();

		if ($taket_extended_log)
		{
			$this->site_log($_SERVER['REMOTE_ADDR'].'['.aw_global_get('uid').'][taket_ebasket::send_order][3] [storage] save order locally = '.(float)($end_time - $start_time));
		}

		//info that will go to the AFP order system
		$toBeSent = array();
		$toBeSent['data']=$rows;
		$toBeSent['user']=aw_global_get('uid');
		$toBeSent['tukkuGrupp']=$_SESSION['TAKET']['tukkuGrupp'];
		$toBeSent['price']=$orderPrice;
		$toBeSent['order_id']=$orderId;
		$toBeSent['transport']=$arr['transport'];
		$toBeSent['transport_name']=$arr['transport_name'];
		$toBeSent['user_info']=$arr['info'];

		$start_time = $this->microtime_float();
		if (!$client->query('server.sendOrder', $toBeSent))
		{
			$error_msg = " [Sending order failed: error_code: ".$client->getErrorCode()." error_message: ".$client->getErrorMessage()." ]";
		}
		$end_time = $this->microtime_float();

		if ($taket_extended_log)
		{
			$this->site_log($_SERVER['REMOTE_ADDR'].'['.aw_global_get('uid').'][taket_ebasket::send_order][4] [XML-RPC] (query) sending order to AFP server = '.(float)($end_time - $start_time));
		}

                $log_file_name = aw_ini_get("site_basedir")."/files/logs/taket_ebasket-log-".date("d-m-Y").".log";
                if ($log_file = fopen($log_file_name, "a"))
                {
                        $log_file_content = date("d-m-Y H:i:s");
                        $log_file_content .= " | ".$orderId;
			$log_file_content .= " | ".html_entity_decode($location_names[$location_key]);
                        $log_file_content .= " | ".$error_msg."\n";
                        flock($log_file, LOCK_EX);
                        fwrite($log_file, $log_file_content);
                        flock($log_file, LOCK_UN);
                        fclose($log_file);
                }

		$start_time = $this->microtime_float();
		$data = $client->getResponse();
		$end_time = $this->microtime_float();

		if ($taket_extended_log)
		{
			$this->site_log($_SERVER['REMOTE_ADDR'].'['.aw_global_get('uid').'][taket_ebasket::send_order][5] [XML-RPC] (getResponse) sending order to AFP server = '.(float)($end_time - $start_time).' vajalikkus kysitav');
		}
		//let's remember the setting for this SESSION
		$_SESSION['TAKET']['info'] = $arr['info'];
		$_SESSION['TAKET']['eesperenimi'] = $arr['eesperenimi'];
		$_SESSION['TAKET']['gsm'] = $arr['kontakttelefon'];
		$_SESSION['TAKET']['transport_type'] = $arr['transport'];


		//save every item of the order for later viewing, FUN
		$start_time = $this->microtime_float();
		for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
		{
			if($o->prop('ebasket_id') == $ebasket->id())
			{
				$price = "";
				$tarjoushinta = (int)$o->meta('tarjoushinta');
				if ( $tarjoushinta > 0 )
				{
					$price = $tarjoushinta;
				}
				else
				{
					$price = $o->prop('price');
				}

				$obj = new object();
				$obj->set_class_id(CL_TAKET_ORDER_ITEM);
				$obj->set_parent(aw_ini_get('taket_order.order_item_parent_id'));
				$obj->set_prop('order_id',$orderId);
				$obj->set_prop('product_code',$o->prop('product_code'));
				$obj->set_prop('quantity',$o->prop('quantity'));
			//	$obj->set_prop('price',$o->prop('price'));
				$obj->set_prop('price', $price);
				$obj->set_meta('tarjoushinta', $tarjoushinta);
				$obj->set_prop('discount',$o->prop('discount'));
				$obj->set_prop('product_name',$o->prop('product_name'));
				$obj->set_prop('ebasket_name',$arr['ebasket_name']);
				$obj->save();
			}
		}
		$end_time = $this->microtime_float();

		if ($taket_extended_log)
		{
			$this->site_log($_SERVER['REMOTE_ADDR'].'['.aw_global_get('uid').'][taket_ebasket::send_order][6] [storage] saving all ebasket items for later viewing (CL_TAKET_ORDER_ITEM) = '.(float)($end_time - $start_time));
		}

		////
		//send email

		$start_time = $this->microtime_float();
		classload('taket/taket_tellimuste_list');
		$emailContent = taket_tellimuste_list::show_order(array('order_id' => $orderId));
		$end_time = $this->microtime_float();

		if ($taket_extended_log)
		{
			$this->site_log($_SERVER['REMOTE_ADDR'].'['.aw_global_get('uid').'][taket_ebasket::send_order][7] taket_tellimuste_list::show_order (get email content) = '.(float)($end_time - $start_time));
		}

		$this->read_template('shell.tpl');
		$this->vars(array(
			'content'=> $emailContent
		));
		$arr['user_id'] = aw_global_get('uid');
		$this->vars($arr);

		$mail_to = aw_ini_get('taket.email_address');
		$emailContent = $this->parse();
	/*
		$headers = "MIME-Version: 1.0\r\n";
		$headers.= "Content-type: text/html; charset=iso-8859-1\r\n";
		$headers.= "From: Taketi Tellimiskeskus <tellimine@taket.ee>\r\n";

		mail($mail_to[$location_key], 'Tellimus Taketi Tellimiskeskusest',$emailContent, $headers);
		$xtra = aw_ini_get('taket.extraemails');
		if(strlen($xtra[$_SESSION['uid']]))
		{
			mail($xtra[$_SESSION['uid']],'Tellimus Taketi Tellimiskeskusest',$emailContent, $headers);
		}
	*/

		$start_time = $this->microtime_float();
		$awm = get_instance("protocols/mail/aw_mail");
		$awm->create_message(array(
			"froma" => "tellimine@taket.ee",
			"fromn" => "Taketi Tellimiskeskus",
			"subject" => "Tellimus Taketi Tellimiskeskusest",
			"to" => $mail_to[$location_key],
			"body" => "tegemist on html kirjaga",
		));
		$awm->htmlbodyattach(array(
			"data" => $emailContent,
		));
		$awm->gen_mail();
		if(strlen($xtra[$_SESSION['uid']]))
		{
			$awm->create_message(array(
				"froma" => "tellimine@taket.ee",
				"fromn" => "Taketi Tellimiskeskus",
				"subject" => "Tellimus Taketi Tellimiskeskusest",
				"to" => $xtra[$_SESSION['uid']],
				"body" => "tegemist on html kirjaga",
			));
			$awm->htmlbodyattach(array(
				"data" => $emailContent,
			));
			$awm->gen_mail();
		}
		$end_time = $this->microtime_float();

		if ($taket_extended_log)
		{
			$this->site_log($_SERVER['REMOTE_ADDR'].'['.aw_global_get('uid').'][taket_ebasket::send_order] creating mail message and sending it = '.(float)($end_time - $start_time));
		}

		$emailContent='';
		//delete the ebasket
		$this->delete_users_ebasket($user_id, $arr['ebasket_name']);
		unset($_SESSION['TAKET']['ebasket_list'][$arr['ebasket_name']]);

//		return $this->mk_my_orb('show',array(),'taket_tellimuste_list');

		aw_restore_acl();

		return aw_ini_get("baseurl")."/41326";
	}

	function add_item($arr, $return=true)
	{
		$site_log_line = '[taket_ebasket::add_item][1]';
		//getting product info
		require_once(aw_ini_get("basedir")."addons/ixr/IXR_Library.inc.php");
		//let's get current users's id
		$user_id=users::get_oid_for_uid(aw_global_get('uid'));

		//xml-rpc call was made earlier?
		if(!$return)
		{
			$data = $arr['data'];
		}
		//have to make the call
		else
		{
			$hosts = aw_ini_get('taket.xmlrpchost');
			$path = aw_ini_get("taket.xmlrpcpath");
			$port = aw_ini_get("taket.xmlrpcport");
			$client = new IXR_Client($hosts[0], $path[0], $port[0]);
			$client->query('server.getProductInfo', $arr['product_code']);
			$data = $client->getResponse();
		}

		//let's get all the items
		$start_time = $this->microtime_float();
		$ebasket = $this->get_users_active_ebasket($user_id);
		$ol_params =array(
			'parent' => $this->ebasket_item_parent_id,
			'class_id' => CL_TAKET_EBASKET_ITEM,
			'lang_id' => array(),
			'ebasket_id' => $ebasket->id()
		);
		if(isset($arr['ebasket_name']))
		{
			$ol_paramas['ebasket_name'] = $arr['ebasket_name'];
		}
		if (!$arr['ol'])
		{
			$ol = new object_list($ol_params);
		}
		else
		{
			$ol = $arr['ol'];
		}
		$end_time = $this->microtime_float();
		$site_log_line .= ' make object_list = '.(float)($end_time - $start_time);
		$tmpFound = false;
		//users ebasket
		$arr['product_code'] = urldecode($arr['product_code']);
		//just in case
		$arr['quantity'] = (int)$arr['quantity'];

		if(!$arr['quantity'])
		{
			$arr['quantity'] = 1;
		}
		$start_time = $this->microtime_float();
		foreach ($ol->arr() as $o)
		{
			//if the object is the needed item and the product code matches
			//ebasket_id==$ebasket->()
			if($o->prop('ebasket_id') == $ebasket->id() &&
				$o->prop('product_code') == $arr['product_code'] &&
				$o->prop('ebasket_name') == $arr['ebasket_name'])
			{
				$o->set_prop("quantity", ( $o->prop("quantity") + $arr['quantity']) );
				$o->save();
				$tmpFound=true;
				break;
			}
		}
		$end_time = $this->microtime_float();
		$site_log_line .= ' | loop through ol & update quantity = '.(float)($end_time - $start_time);

		//such a product doesn't exist yet, lets add it
		$start_time = $this->microtime_float();
		if(!$tmpFound)
		{
			$o = new object();
			$o->set_class_id(CL_TAKET_EBASKET_ITEM);
			$o->set_parent($this->ebasket_item_parent_id);
			if((int)$_SESSION['TAKET']['tukkuGrupp']==100)
			{
				$o->set_prop('price',$data['tukkuprice']);
			}
			else
			{
				$o->set_prop('price',$data['price']);
			}
			$o->set_prop('product_name', $data['product_name']);
			$o->set_prop('discount', (int)$data['kat_ale'.$_SESSION['TAKET']['ale']]);
			$o->set_prop('product_code',$arr['product_code']);
			$o->set_prop('inStock', $data['inStock']);
			$o->set_prop('supplier_id',$data['supplier_id']);
			$o->set_prop('ebasket_id',$ebasket->id());
			$o->set_prop('ebasket_name',$arr['ebasket_name']);
			$o->set_prop("quantity", (int)$arr['quantity']);
			$o->set_meta('asukoht', $arr['asukoht']);
			$o->save();
		}
		$end_time = $this->microtime_float();
		$site_log_line .= ' | ebasket item did not exist, creating it = '.(float)($end_time - $start_time);

		if ($taket_extended_log)
		{
			$this->site_log($_SERVER['REMOTE_ADDR'].'['.aw_global_get('uid').']'.$site_log_line);
		}
		if($return)
		{
			return $this->mk_my_orb("show", array(), "taket_ebasket");
		}
	}

	/**
		@attrib name=add_items params=name default="0"

		@param productId optional
		@param quantity optional
		@param valitud optional
		@param ebasket_name optional
		@param ebasket_name_list optional
		@param asukoht optional
	**/
	function add_items($arr)
	{
		aw_disable_acl();
		//adds many items to the basket at a time, uses the add_item function
		//not to just copy the logic
		//getting product info
		require(aw_ini_get("basedir")."addons/ixr/IXR_Library.inc.php");
		//did user just add an ebasket?
		$arr['ebasket_name'] = trim($arr['ebasket_name']);
		$ebasket_name = $this->current_ebasket_identificator;
		if(strlen($arr['ebasket_name']))
		{
			//just in case :)
			if($arr['ebasket_name'] == $this->current_ebasket_identificator)
			{
				$arr['ebasket_name'].='1';
			}
			else
			{
				$ebasket_name = $arr['ebasket_name'];
			}

			if(!isset($_SESSION['TAKET']['ebasket_list']))
			{
				$_SESSION['TAKET']['ebasket_list'] = array();
			}
			if(!array_key_exists($ebasket_name,$_SESSION['TAKET']['ebasket_list']))
			{
				$_SESSION['TAKET']['ebasket_list'][$ebasket_name] = $ebasket_name;
			}
		}
		else if(strlen($arr['ebasket_name_list']))
		{
			$ebasket_name = $arr['ebasket_name_list'];
		}

		//let's get current users's id
		$hosts = aw_ini_get('taket.xmlrpchost');
		$path = aw_ini_get("taket.xmlrpcpath");
		$port = aw_ini_get("taket.xmlrpcport");

		$user_id=users::get_oid_for_uid(aw_global_get('uid'));

		$client = new IXR_Client($hosts[0], $path[0], $port[0]);

		//make array of selected products
		$product_codes = array();
		if(!is_array($arr['valitud']))
		{
			$arr['valitud'] = array();
		}
		foreach($arr['valitud'] as $key => $value)
		{
			$product_codes[] = $arr['productId'][$key];
		}

		//prefetching the data with one xml-rpc call
		//without this add_item would do everytime a separate call, it COSTS
		$query_start_time = $this->microtime_float();
		$client->query('server.getProductInfoArr', $product_codes);
		$query_end_time = $this->microtime_float();

		$getresponse_start_time = $this->microtime_float();
		$data = $client->getResponse();
		$getresponse_end_time = $this->microtime_float();

		if ($taket_extended_log)
		{
			$this->site_log($_SERVER['REMOTE_ADDR'].'['.aw_global_get('uid').'][taket_ebasket::send_items][1] [XML-RPC] getting selected products info (query/getResponse) = '.(float)($query_end_time - $query_start_time).'/'.(float)($getresponse_end_time - $getresponse_start_time));
		}
		//add the item
		$start_time = $this->microtime_float();
		foreach($arr['valitud'] as $key=>$value)
		{
			$start_time2 = $this->microtime_float();

			$this->add_item(array(
				'quantity'=>$arr['quantity'][$key],
				'product_code'=>$arr['productId'][$key],
				'data'=>$data[$arr['productId'][$key]],
				'ebasket_name' => $ebasket_name,
				'asukoht' => $arr['asukoht']
			) ,false);

			$end_time2 = $this->microtime_float();

		//	arr('--- '.($end_time2 - $start_time2));
		}
		$end_time = $this->microtime_float();

		if ($taket_extended_log)
		{
			$this->site_log($_SERVER['REMOTE_ADDR'].'['.aw_global_get('uid').'][taket_ebasket::add_items][2] adding items total = '.(float)($end_time - $start_time));
		}
		aw_restore_acl();

		return $this->mk_my_orb("show", array(), "taket_ebasket");
	}

	//saves the changes after the user has pushed the
	//check-out button
	/**

		@attrib name=save_ebasket params=name default="0"
		@param ebasket_name required
		@param productId optional
		@param quantity optional
		@param seesperenimi optional
		@param skontakttelefon optional
		@param stransport optional
		@param sort optional
		@param dir optional
		@param sinfo optional
		@param check_all_stocks optional

		@returns


		@comment

	**/
	function save_ebasket($arr){

		//let's get id of the current user
		$arr['ebasket_name'] = urldecode($arr['ebasket_name']);
		$user_id=users::get_oid_for_uid(aw_global_get('uid'));
		//let's get all the ebasket_items

		$start_time = $this->microtime_float();
		$ol = new object_list(array(
			'parent' => $this->ebasket_item_parent_id,
			'lang_id' => array(),
		));
		$end_time = $this->microtime_float();

		if ($taket_extended_log)
		{
			$this->site_log($_SERVER['REMOTE_ADDR'].'['.aw_global_get('uid').'][taket_ebasket::save_ebasket][1] [storage] getting ebasket items = '.(float)($end_time - $start_time));
		}

		//let's get the ebasket
		$ebasket = $this->get_users_active_ebasket($user_id);
		$tmpFlag=true;
		//change the default session values
		$_SESSION['TAKET']['info'] = $arr['sinfo'];
		$_SESSION['TAKET']['eesperenimi'] = $arr['seesperenimi'];
		$_SESSION['TAKET']['gsm'] = $arr['skontakttelefon'];
		$_SESSION['TAKET']['transport_type'] = $arr['stransport'];

		//$client->query('server.getProductInfo',$o->prop('product_code'));
		$start_time = $this->microtime_float();
		for($o=$ol->begin();!$ol->end();$o=$ol->next())
		{
			//and is from current group
			if(
					!(
					$arr['ebasket_name']==$this->current_ebasket_identificator
						||
					$o->prop('ebasket_name')==$arr['ebasket_name']
					)
			)
				continue;
			//if the object belongs to the $ebasket
			if($o->prop('ebasket_id')==$ebasket->id())
			{
				//find the "new" quantity for this product_id
				foreach($arr['productId'] as $key => $value)
				{
					//found
					if($value==$o->prop('product_code'))
					{
						//let's change the quantity
						//if quantity less than 0 or ==0 then the line will be deleted
						if( (int)$arr['quantity'][$key] <= 0 )
						{
							aw_disable_acl();
							$o->delete();
							aw_restore_acl();

						}
						//let's update the obj property
						else
						{
							//if AFP has more items then the change is allowed, only then
							//if($data[$o->prop('product_code')]['inStock']>=$arr['quantity'][$key])
							//{
								$o->set_prop('quantity',(int)$arr['quantity'][$key]);
								$o->save();
							//}
						}
					}
				}
			}
		}
		$end_time = $this->microtime_float();

		if ($taket_extended_log)
		{
			$this->site_log($_SERVER['REMOTE_ADDR'].'['.aw_global_get('uid').'][taket_ebasket::save_ebasket][2] [storage] update ebasket items info = '.(float)($end_time - $start_time));
		}

		if($tmpFlag)
		{
			$tmp=array('action'=>'show','saved'=>1);
		}
		else
		{
			$tmp=array('action'=>'show');
		}
		$tmp['sort'] = $arr['sort'];
		$tmp['dir'] = $arr['dir'];
		$tmp['check_all_stocks'] = $arr['check_all_stocks'];
		return $this->mk_my_orb("show",$tmp,"taket_ebasket");
	}

	//gonna need this in many places
	//fetches the current users ebasket, if it
	//doesn't exist, it creates one
	function get_users_active_ebasket($user_id, $create = true)
	{
		//get all the ebasket objects
		if (empty($user_id))
		{
			$user_id = users::get_oid_for_uid(aw_global_get('uid'));
		}

		$ol = new object_list(array(
			'parent' => $this->ebasket_parent_id,
			'lang_id' => array(),
			'user_id' => $user_id,
			'class_id' => CL_TAKET_EBASKET_INST
		));

		// if there is some ebaskets for this user, it returns the first one
		if ($ol->count() > 0)
		{
			$o = $ol->begin();
			return $o;
		}

		if($create)
		{
			$ebasket = new object();
			$ebasket->set_class_id(CL_TAKET_EBASKET_INST);
			$ebasket->set_parent($this->ebasket_parent_id);
			$ebasket->set_prop('user_id',$user_id);
			$ebasket->save();
		}

		return $ebasket;
	}
/*
	//gonna need this in many places
	//fetches the current users ebasket, if it
	//doesn't exist, it creates one
	function get_users_active_ebasket($user_id, $create=true)
	{
		//get all the ebasket objects
		$ol = new object_list(array(
				'parent' => $this->ebasket_parent_id,
				'lang_id' => array()
				));
		$ebasket = null;
		$user_id = users::get_oid_for_uid(aw_global_get('uid'));
		for($o=$ol->begin();!$ol->end();$o=$ol->next())
		{
			//object found, let's return it
			if($o->prop('user_id')==$user_id)
			{
				return $o;
			}
		}
		if($ebasket==null && $create)
		{
			$ebasket = new object();
			$ebasket->set_class_id(CL_TAKET_EBASKET_INST);
			$ebasket->set_parent($this->ebasket_parent_id);
			$ebasket->set_prop('user_id',$user_id);
			$ebasket->save();
		}
		return $ebasket;
	}
*/

	//l2heb sisselogimisel k2ivitusele
	//terrifile pean saatma emaili
	function delete_users_ebasket($user_id,$ebasket_name=false)
	{
		$ebasket = $this->get_users_active_ebasket($user_id, false);
		if($ebasket==null)
		{
			return;
		}

		$ol = new object_list(array(
			'parent' => $this->ebasket_item_parent_id,
			'lang_id' => array()
			));

		for($o=$ol->begin();!$ol->end();$o=$ol->next())
		{
			if($o->prop('ebasket_id')==$ebasket->id())
			{
				if($ebasket_name)
				{
					if($o->prop('ebasket_name')==$ebasket_name)
					{
						$o->delete();
					}
				}
				else
				{
					$o->delete();
				}
			}
		}
		if($ebasket_name)
		{
			//some kind of a check, if basket is empty
			//i guess at least
		}
		else
		{
			$ebasket->delete();
		}
	}

	function msg_delete_users_ebasket($arr)
	{
		// this should not be called if the site is not taket. I'm not sure whether
		// this check is the correct way so feel free to fix it.
		if (empty($this->ebasket_parent_id))
		{
			return false;
		};
		taket_ebasket::delete_users_ebasket(users::get_oid_for_uid($arr['uid']));
	}

	/**
		@attrib name=products_from_csv
	**/
	function products_from_csv($arr)
	{

		$upload_field_name = "csv_file";
		if (empty($_FILES) || !empty($_FILES[$upload_field_name]['error']))
		{
			$this->read_template("products_from_csv.tpl");
			$this->vars(array(
				"upload_field_name" => $upload_field_name,
				"reforb" => $this->mk_reforb("products_from_csv", array("no_reforb" => true)),
			));

                	return $this->parse();
		}
		else
		{
			// set execution time to 600 seconds
			set_time_limit(600);

			$tmp_file_name = $_FILES[$upload_field_name]['tmp_name'];
			$file_content = file($tmp_file_name);
			$separator = "";
			foreach( $file_content as $line )
			{
				if (empty($separator))
				{
					$separator = ",";
					if (strpos($line, $separator) === false)
					{
						$separator = ";";
					}
				}

				if (strpos($line, $separator) === false)
				{
					continue;
				}
				$product_data = explode($separator, $line);
				$params['productId'][] = $product_data[0];
				$params['quantity'][] = $product_data[1];
				$params['valitud'][] = 1;
			}
			$params['ebasket_name'] = "";
			$params['user'] = 1;
			$params['asukoht'] = 1;
			return $this->add_items($params);

		}
	}

	function microtime_float(){
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}


}
?>
