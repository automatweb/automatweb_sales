<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/clients/taket/taket_search.aw,v 1.6 2009/05/05 13:50:21 dragut Exp $
// taket_search.aw - Taketi Otsing 
/*

@classinfo syslog_type= relationmgr=yes maintainer=robert
//groupinfo blocked caption=Piirangud

@default table=objects
@default group=general

//property taket_block_conf type=relpicker group=blocked reltype=RELTYPE_TAKET_BLOCK_CONF multiple=1
//caption Piirangud

@property warehouse0 type=relpicker reltype=RELTYPE_WAREHOUSE0 field=meta method=serialize
@caption Kadaka tee ladu

@property warehouse1 type=relpicker reltype=RELTYPE_WAREHOUSE1 field=meta method=serialize
@caption Punane tn ladu

@property warehouse2 type=relpicker reltype=RELTYPE_WAREHOUSE2 field=meta method=serialize
@caption Tartu ladu

@property warehouse3 type=relpicker reltype=RELTYPE_WAREHOUSE3 field=meta method=serialize
@caption P&auml;rnu ladu

@property warehouse4 type=relpicker reltype=RELTYPE_WAREHOUSE4 field=meta method=serialize
@caption Paavli ladu

@property warehouse5 type=relpicker reltype=RELTYPE_WAREHOUSE5 field=meta method=serialize
@caption Viljandi ladu

@reltype RELTYPE_WAREHOUSE0 value=1 clid=CL_SHOP_WAREHOUSE
@caption Kadaka tee ladu

@reltype RELTYPE_WAREHOUSE1 value=2 clid=CL_SHOP_WAREHOUSE
@caption Punane tn ladu

@reltype RELTYPE_WAREHOUSE2 value=3 clid=CL_SHOP_WAREHOUSE
@caption Tartu ladu

@reltype RELTYPE_WAREHOUSE3 value=4 clid=CL_SHOP_WAREHOUSE
@caption P&auml;rnu ladu

@reltype RELTYPE_WAREHOUSE4 value=5 clid=CL_SHOP_WAREHOUSE
@caption Paavli ladu

@reltype RELTYPE_WAREHOUSE5 value=6 clid=CL_SHOP_WAREHOUSE
@caption Viljandi ladu
*/

class taket_search extends class_base implements main_subtemplate_handler
{
	function taket_search()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "taket/taket_search",
			"clid" => CL_TAKET_SEARCH
		));
		lc_site_load('taket_search', $this);
	}

	//////
	// class_base classes usually need those, uncomment them if you want to use them
	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	////
	// !this shows the object. not strictly necessary, but you'll probably need it, it is used by parse_alias
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");	
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	/**  
		
		@attrib name=parse_submit_info params=name default="0"
		
		@param tootekood optional
		@param asendustooted optional
		@param otsitunnus optional
		@param toote_nimetus optional
		@param laos optional
		@param kogus optional
		@param reforb optional
		@param start optional
		@param orderBy optional
		@param direction optional
		@param asukoht optional
		@param wvat optional
		@param osaline optional
		
		@returns
		
		
		@comment

	**/
	function parse_submit_info($arr)
	{
		enter_function("taket_search::parse_submit_info");
		enter_function("taket_search::parse_submit_info:1");

		// checking comma separated amount values
		if(isset($arr['kogus']))
		{
			$tmpArr = split(',',$arr['kogus']);
			$tmpArr2 = array();
			foreach($tmpArr as $value)
			{
				$tmpArr2[] = (int)$value <= 0 ? 1 : (int)$value;
			}
			$arr['kogus'] = implode(',', $tmpArr2);
			unset($tmpArr2);
			unset($tmpArr);
		}

		$this->read_template('search.tpl');

		enter_function("taket_search::compose_params");
		$param = $this->compose_params($arr);
		exit_function("taket_search::compose_params");

		enter_function("taket_search::get_products");
	//	$data = $this->get_products($param);
		$data = $this->get_products($arr);
		exit_function("taket_search::get_products");

		exit_function("taket_search::parse_submit_info:1");
		enter_function("taket_search::parse_submit_info:2");
		enter_function("taket_search::parse_submit_info:2:1");

		$product_ids = array_keys($data);

		if($product_ids)
		{
			$amounts = $this->get_amounts(array(
				'product_ids' => $product_ids
			));
		/*
			// lets leave the purveyances alone at the moment
			// I wonder anyway how the heck one will update those objects? Nobody will be setting them by hand per product ...
			$purveyances = $this->get_purveyances(array(
				'product_ids' => $product_ids
			));
		*/
		}

		exit_function("taket_search::parse_submit_info:2:1");
		enter_function("taket_search::parse_submit_info:2:2");
		
		$hidden["orderBy"] = $arr["orderBy"];
		if($arr['direction'] == 'desc')
		{
			$hidden['direction'] = 'asc';
			$hidden['direction_pg'] = 'desc';
		}
		else
		{
			$hidden['direction'] = 'desc';
			$hidden['direction_pg'] = 'asc';
		}

		$o_ol = new object_list(array(
			"class_id" => CL_TAKET_SEARCH,
			"site_id" => array(),
			"lang_id" => array(),
		));
		$obj_inst = $o_ol->begin();

		foreach($data as $value)
		{
			for($i = 0; $i < 6; $i++)
			{
				$whid = $obj_inst->prop("warehouse".$i);
				$amount = $amounts[$value['oid']][$whid]['amount'];
				if($value["amount"] <= $amount && $amount)
				{
					$in_stock[$value["oid"]][$i] = $this->parse('instockyes');
					$in_stock[$value["oid"]][$whid] = $this->parse('instockyes');
				}
				//this product is out of stock
				else
				{
					if($amount > 0)
					{
						$in_stock[$value["oid"]][$i] = $this->parse('instockpartially');
						$in_stock[$value["oid"]][$whid] = $this->parse('instockpartially');
					}
					else
					{
					//	$in_stock[$value["oid"]][$i] =  $this->_get_date_by_supplier_id($value["supplier_times"][$whid]);
					//	$in_stock[$value["oid"]][$whid] =  $this->_get_date_by_supplier_id($value["supplier_times"][$whid]);

						$in_stock[$value["oid"]][$i] =  '-';
					}
				}
			}
		}

		exit_function("taket_search::parse_submit_info:2:2");
		enter_function("taket_search::parse_submit_info:2:3");

		usort($data, array($this, "__sort_products"));

		$pre_pages = $data;
		$data = array();
		$start = ($arr["start"] ? $arr["start"] : 0);

		$page_prod_codes = array();
		for($i = $start; $i < $start + 40; $i++)
		{
			if($pre_pages[$i])
			{
				$data[] = $pre_pages[$i];
			}
			$page_prod_codes = $pre_pages[$i]["product_code"];
		}

		$arr['asendustooted'] = (int)$arr['asendustooted'];
		$arr['laos'] = (int)$arr['laos'];

		$arr['start'] = (int)$arr['start'];

		$this->vars($arr);

		//if the search was done as follows:
		//product_code & it's quantity, product_code & it's quantity etc
		//i have to display for a found product_code _it's_ quantity
		//so i have to some pattern matching here because i can't
		//extract the info from the query/results
		//build patterns:
		
		exit_function("taket_search::parse_submit_info:2:3");

		enter_function("taket_search::parse_submit_info:2:4");

		$page_prod_codes = array();
		foreach($data as $value)
		{
			$page_prod_codes[] = $value["product_code"];
		}

		// add all warehouse urls here
		$urls = array(
			0 => "http://88.196.208.74:8888/xmlrpc/index.php?db=1&".http_build_query(array("pc" => $page_prod_codes))
		//	1 => "http://84.50.96.150:8080/xmlrpc/index.php?db=1&".http_build_query(array("pc" => $page_prod_codes)),
		);

		$prs = $this->parallel_price_fetch($urls);
	//	$prices = $prs[0];
		
		$prices = array();
		foreach ($prs[0] as $price_data)
		{
			$prices[$price_data['product_code']] = $price_data;
		}

		exit_function("taket_search::parse_submit_info:2:4");
		exit_function("taket_search::parse_submit_info:2");

// this here is temporary as well - i think there should be better place to put this TAKET data into session! --dragut
taket_users_import::update_user_info(array('uid' => aw_global_get('uid')));

//automatweb::$instance->mode(automatweb::MODE_DBG);
		enter_function("taket_search::parse_submit_info:3");
		// tsykkel yle toodete, joonistatakse toodete tabel
		foreach($data as $value)
		{
			// lets add the prices data to value array ...
			$value += $prices[$value['product_code']];

			//have to determine the discount for this user/*
			$wx = 1;
			if(!$arr["wvat"])
			{
				$wvat = $_COOKIE["wvat"];
			}
			else
			{
				$wvat = $arr["wvat"];
			}
			if($wvat == 1)
			{
				$wx = 1.18;
			}
			$value['discount'] = (int)$prices[$value['product_code']]['discount_'.$_SESSION['TAKET']['ale']];
			if(!((int)$value['discount']))
			{
				$value['discount'] = 0;
			}
			$value['product_code2'] = urlencode($value['product_code']);
			if((int)$_SESSION['TAKET']['tukkuGrupp'] == 100)
			{
				$value['price'] = number_format(($value['tukkuprice']/$wx), 2, '.', '');
			}
			else
			{
				$value['price'] = number_format(($value['price']/$wx), 2, '.', '');
			}

			// special price (tarhoushinta)
			if($value["special_price"] <= 0)
			{
				$value["special_price"] = "-";
			}
			else
			{
				$value["special_price"] = number_format(($value["special_price"]/$wx), 2, '.', '');
			}
			$value['finalPrice'] = number_format($value['price'] * ((100 - $value['discount']) / 100), 2, '.', '');

			$old_trans_instock_no = $this->vars['trans_instock_no'];

			foreach($in_stock[$value["oid"]] as $id => $val)
			{
				if(is_numeric($val))
				{
					$this->vars(array(
						"trans_instock_no" => date("d/m/y", $val),
					));
				}
				elseif($val === false)
				{
					$this->vars(array(
						"trans_instock_no" => $old_trans_instock_no,
					));
				}
				else
				{
					$this->vars(array(
						"trans_instock_no" => $val,
					));
				}
				$in_stock[$value["oid"]][$id] = $this->parse('instockno');
			}
			$this->vars(array(
				"in_Stock3" => $in_stock[$value["oid"]][0],
				"in_Stock4" => $in_stock[$value["oid"]][1],
				"in_Stock5" => $in_stock[$value["oid"]][2],
				"in_Stock6" => $in_stock[$value["oid"]][3],
				"in_Stock7" => $in_stock[$value["oid"]][4],
				"in_Stock8" => $in_stock[$value["oid"]][5],
			));
			$value['search_code'] = str_replace(' ','&nbsp;', $value["search_term"]);
			$value['product_code'] = str_replace(' ','&nbsp;', $value["product_code"]);
			$value['product_name'] = str_replace(' ','&nbsp;', $value["name"]);
			$value['i'] = $i++;
			$this->vars($value);

			$value['quantity'] = ((int)$value['quantity']) ? (int)$value['quantity'] : '1';

			if($value['quantity'] <= $value['inStock'])
			{
				$this->vars(array(
					'quantityParsed' => $this->parse('canSetQuantity'),
					'karuParsed' => $this->parse('karu')
				));
			}
			else
			{
				$this->vars(array(
					'quantityParsed' => $this->parse('cannotSetQuantity'),
					'karuParsed' => $this->parse('karupole'),
				));
			}

			//kas on asendustoode v6i mitte
			if($value['replacement'] == 'K&uuml;situd')
			{
				$this->vars(array(
					'esimeneVeerg' => $this->parse('mainproduct')
				));
			}
			else
			{
				$this->vars(array(
					'esimeneVeerg' => $this->parse('asendustoodeblock')
				));
			}
			$content .= $this->parse('product');
			$count++;
		}
		exit_function("taket_search::parse_submit_info:3");
		enter_function("taket_search::parse_submit_info:4");
		$this->vars(array('productParsed' => $content));
		$data = '';
			
		//make column label bold if it was used to sort
		$tmpArr = array(
			'cssstaatus' => 'listTitle',
			'csstootekood' => 'listTitle',
			'cssnimetus' => 'listTitle',
			'cssotsitunnus' => 'listTitle',
			'csshind' => 'listTitle',
			'cssallahindlus' => 'listTitle',
			'csslopphind' => 'listTitle',
			'csslaos' => 'listTitle',
		);
		$tmpArr['css'.$hidden['orderBy']] = 'listTitleSort';
		$this->vars($tmpArr);
		classload('taket/taket_ebasket');
		$ebasket = new taket_ebasket();
		if(sizeof($_SESSION['TAKET']['ebasket_list']))
		{
			$tmp = '';
			foreach($_SESSION['TAKET']['ebasket_list'] as $key => $value)
			{
				if($value != $ebasket->current_ebasket_identificator)
				{
					$this->vars(array('ebasket_list_item_name' => $value));
					$tmp .= $this->parse('ebasket_list_item');
				}
			}
			$this->vars(array('ebasket_list_items' => $tmp));
			$this->vars(array('ebasket_list_value' => $this->parse('ebasket_list')));
		}
		

		//assign hidden values
		$this->vars($hidden);

		//generating page numbers
		$count2 = $count;
		$noSkipped = $arr["start"];
		$count = ceil($numOfRows/40);
		$content = '';
		for($i = 0; $i < $count; $i++)
		{
			$prev = $noSkipped ? ($noSkipped-40) : 0;
			$next = ($noSkipped == 40*4) ? (40*4) : ($noSkipped+40);
			$pageNumber = ($i*40) == $noSkipped ? '<b>'.($i+1).'</b>' : ($i+1);
			if($count == 0)
			{
				$next = 0;
			}
			$this->vars(array(
				'next' => $next,
				'prev' => $prev,
				'pageNumber' => $pageNumber,
				'start_pg' => $i*40,
			));
			$content .= $this->parse('pageNumbers');
		}
		$this->vars(array('pageNumbersParsed' => $content));
		if($count>1)
		{
			$this->vars(array('numbersPart' => $this->parse('numbersPart')));
		}
		
		//simple var assignments
		$this->vars(array(
			'otsisin' => $arr['tootekood'].' '.$arr['otsitunnus'],
			'tootekood' => $arr['tootekood'],
			'toote_nimetus' => $arr['toote_nimetus'],
			'results' => $numOfRows
		));

		exit_function("taket_search::parse_submit_info:4");
		exit_function("taket_search::parse_submit_info");
		return $this->parse();
	}

	function __sort_products($a, $b)
	{
		if($_GET["direction"] == "desc")
		{
			$c = $a;
			$a = $b;
			$b = $c;
		}
		switch($_GET["orderBy"])
		{
			case "tootekood":
				return strnatcasecmp($a["product_code"], $b["product_code"]);
			case "nimetus":
				return strcasecmp($a["product_name"], $b["product_name"]);
			case "otsitunnus":
				return strcasecmp($a["search_term"], $b["search_term"]);
			case "staatus":
				return strcasecmp($a["replacement"], $b["replacement"]);
			case "hind":
				return $a["price"] - $b["price"];
			case "allahindlus":
				return $a['kat_ale'.$_SESSION['TAKET']['ale']] - $b['kat_ale'.$_SESSION['TAKET']['ale']];
			case "lopphind":
				return (100 - ($a['kat_ale'.$_SESSION['TAKET']['ale']]/100)) * $a["price"] - (100 - ($b['kat_ale'.$_SESSION['TAKET']['ale']]/100)) * $b["price"];
		}
	}

	function compose_params($arr)
	{
		$param = array();

		$multiple_product_codes = false;
		if(strstr($arr['tootekood'], ',') || $arr['kogus'])
		{
		// see asi siin ei t88ta praegu korralikult, sellep2rast, et filtrisse pannakse short code v2lja parameetritekse tootekoodid, mis ei ole short code-iks tehtud
		// ilmselt tuleks siin rakendada ka ikkagi seda yhte short code controllerit mida importimisel kasutatakse.
			$multiple_product_codes = true;
			$products = split(',', $arr['tootekood']);
			$quantities = split(',', $arr['kogus']);
			foreach($products as $key => $value)
			{
				$products[$key] = trim($value);
				$quantities[$key] = ((int)$quantities[$key]) > 0 ? (int)$quantities[$key] : 1;
			}
		}

		$f_add = $arr["osaline"] ? "%" : "";
		if($arr["tootekood"])
		{
			$find = array("-", " ", "O", "(", ")");
			$replace = array("", "", "0", "", "");
			$tk = str_replace($find, $replace, $arr["tootekood"]);
		
			$param[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"search_term" => $multiple_product_codes ? $products : $f_add.$tk.$f_add,
					"short_code" => $multiple_product_codes ? $products : $f_add.$tk.$f_add,
					"code" => $multiple_product_codes ? $products : $f_add.$tk.$f_add,
				),
			));
		}

		if($arr["toote_nimetus"])
		{
			$param["name"] = "%".$arr["toote_nimetus"]."%";
		}

		// the amount thingie has to be updated during the full import as well
		if($arr["laos"])
		{
			$param["CL_SHOP_PRODUCT.RELTYPE_PRODUCT(CL_SHOP_WAREHOUSE_AMOUNT).amount"] = new obj_predicate_compare(OBJ_COMP_GREATER, 0);
		}

		$param["class_id"] = CL_SHOP_PRODUCT;
		$param["lang_id"] = array();
		$param["site_id"] = array();
		$param["limit"] = "0,200";

		return $param;
	}

	function get_products($param)
	{
		// lets try to make a sql query directly to aw_shop_products table
		$sql = "
			SELECT
				aw_oid as oid,
				code as product_code,
				search_term,
				user1 as replacement_product_code
			FROM
				aw_shop_products
			WHERE
				code like '%".$param['tootekood']."%' or 
				short_code like '%".$param['tootekood']."%' or 
				search_term like '%".$param['tootekood']."%' 
			LIMIT 0,200
		";
		$sql = "
			SELECT
				aw_oid as oid
			FROM
				aw_shop_products
			WHERE
				code like '%".$param['tootekood']."%' or 
				short_code like '%".$param['tootekood']."%' or 
				search_term like '%".$param['tootekood']."%' 
			LIMIT 0,200
		";

		enter_function("get_products::1");
		$this->db_query($sql);
		exit_function("get_products::1");
		enter_function("get_products::2");
		while ($r = $this->db_next())
		{
			$data[$r['oid']] = $r['oid'];
		}
		exit_function("get_products::2");
//		return $data;
//arr($data);
		enter_function("get_products::3");
		$param = array(
			'class_id' => CL_SHOP_PRODUCT,
			'oid' => $data,
			"lang_id" => array(),
			"site_id" => array()
		);
		// search with storage
		$fetch = array(
			CL_SHOP_PRODUCT => array(
				'code' => 'product_code',
				'short_code',
				'search_term',
				'user1' => 'replacement_product_code'
			)
		);
		$param["join_strategy"] = "data";
		$odl = new object_data_list($param, $fetch);

		$products = $odl->arr();
		$product_ids = array_keys($products);
		exit_function("get_products::3");

		enter_function("get_products::4");
		// get replacement products:
		$replacements = array();
		foreach ($products as $item)
		{
			if (!empty($item['replacement_product_code']))
			{
				$replacement_product_codes[$item['replacement_product_code']] = $item['replacement_product_code'];
			}
		}

		if (!empty($replacement_product_codes))
		{
			$repl_odl = new object_data_list(
				array(
					'code' => $replacement_product_codes,
					'site_id' => array(),
					'lang_id' => array(),
				),
				$fetch
			);
			$replacements = $repl_odl->arr();
		}
		exit_function("get_products::4");	
		return $products + $replacements;
/*
		$prods = array();

		// in this loop, the replacement product are collected
		// but i definitely need to solve it somehow more elegantly and optimaly
	
		foreach($ol->arr() as $oid => $o)
		{
			if(count($prods) == 200)
			{
				break;
			}
			$prods[$oid] = $oid;
			if($o->prop("user1"))
			{
				$rp_ol = new object_list(array(
					"class_id" => CL_SHOP_PRODUCT,
					"site_id" => array(),
					"lang_id" => array(),
					"user1" => $o->prop("user1"),
					"oid" => new obj_predicate_not($oid),
				));
				foreach($rp_ol->ids() as $rp_oid)
				{
					if(count($prods) == 200)
					{
						break;
					}
					$prods[$rp_oid] = $rp_oid;
				}
			}
		}
		
		$numOfRows = count($prods);

		$ol = new object_list();
		
		if(count($prods))
		{
			$ol = new object_list(array(
				"oid" => $prods,
			));
		}

		$data = array();
		foreach($ol->arr() as $oid => $o)
		{
			$value = array();
			$value["product_name"] = $o->name();
			$value["product_code"] = $code = $o->prop("code");
			$value["search_term"] = $o->prop("search_term");
			$value["product_id"] = $o->id();

			if(!isset($value["quantity"]))
			{
				$value["quantity"] = 1;
			}

			$data[$oid] = $value;
		}
		return $data;
*/
	}

	function get_amounts($arr)
	{
		$product_ids = $arr['product_ids'];

		// anyway, the storage here is slow as hell as usual, so lets try to solve this with plain sql:
		$sql = "
			SELECT
				amount,
				product,
				warehouse
			FROM
				aw_shop_warehouse_amount
			WHERE
				product IN (".implode(',', $product_ids).")
		";

		$this->db_query($sql);
		while ($row = $this->db_next())
		{
			$result[$row['product']][$row['warehouse']] = $row;
		}

		return $result;

		// for some reason, this object_data_list doesn't found any amount data, though, there are some!
		// mkai, this might be related to fact, that there should be connection from amount obj to product obj
		// but right now there isn't any - there is only product id saved to amount object's property --dragut
		$amt_ol = new object_data_list(
			array(
			"class_id" => CL_SHOP_WAREHOUSE_AMOUNT,
			"product" => $product_ids,
			"site_id" => array(),
			"lang_id" => array(),
			),
			array(
				CL_SHOP_WAREHOUSE_AMOUNT => array(
					'amount',
					'product',
					'warehouse'
				)
			)
		);
/*
		foreach($amt_ol->arr() as $o)
		{
			$data[$o["product"]]["amounts"][$o["warehouse"]] = $o["amount"];
		}
*/
		return $amt_ol->arr();
	}

	function get_purveyances($arr)
	{
		$product_ids = $arr['product_ids'];

		$org_ol = new object_data_list(array(
			"class_id" => CL_SHOP_PRODUCT_PURVEYANCE,
			"product" => $product_ids,
			"site_id" => array(),
			"lang_id" => array(),
		),
		array(
			CL_SHOP_PRODUCT_PURVEYANCE => array(
				"product" => "product", 
				"warehouse" => "warehouse",
				"date1" => "date1",
				"date2" => "date2",
				"days" => "days",
				"weekday" => "weekday"
			)
		));
	/*
		foreach($org_ol->arr() as $o)
		{
			$data[$o["product"]]["supplier_times"][$o["warehouse"]] = array(
				"date1" => $o["date1"],
				"date2" => $o["date2"],
				"days" => $o["days"],
				"day1" => $o["weekday"],
			);
		}
	*/
		return $org_ol->arr();
	}

	function on_get_subtemplate_content($arr)
	{
		$inst = $arr['inst'];
	
		//h6mm main.tpl'i subi TAKET_SEARCH peax vist ikkagi
		//n2itama antud klassi show.tpl'i	
		$this->read_template('show.tpl');
		//reforb
		$asukoht = !$_REQUEST["asukoht"] ? 0 : $_REQUEST["asukoht"];
		switch($asukoht)
		{
			case -1:
				$name = "lis_sel";
				break;
			
			case 1:
				$name = "lis_sel1";
				break;
			case 2:
				$name = "lis_sel2";
				break;
			case 3: 
				$name = "lis_sel3";
				break;
			case 4: 
				$name = "lis_sel4";
				break;
			case 5: 
				$name = "lis_sel5";
				break;
			default:
				$name = "lis_sel0";
				break;
		}
		$value = array();
		if(!$_REQUEST["wvat"])
		{
			$wvat = $_COOKIE["wvat"];
		}
		else
		{
			$wvat = $_REQUEST["wvat"];
		}
		if($wvat == 1)
		{
			$value["wvat_check"] = "checked";
		}
		else
		{
			$wvat = 0;
		}
		setcookie("wvat", $wvat, (3600*24*365*5));
		
		$this->vars(array(
			'reforb'=>$this->mk_reforb('parse_submit_info', array('no_reforb'=>true)),
			$name => "selected",
		) + $value);
		$inst->vars(array(
			'taket_search_content'=>$this->parse()
		));
		
		$inst->vars(array(
			'TAKET_SEARCH' => $inst->parse("TAKET_SEARCH")
		));	
	}
	////
	// supplier_id - Supplier id
	function _get_date_by_supplier_id($supplier_times)
	{
		// JC (supplier_id == 179) 
		// teisip2eva 6htust on ylej2rgmine esmasp2ev v6imalik
//		$supplier_times = $this->db_fetch_array("select * from taket_times where supplier_id='".$arr['supplier_id']."'");
		if (empty($supplier_times) || ($supplier_times['date1'] < 1 && $supplier_times['date2'] < 1 && !$supplier_times['day1'] && !$supplier_times['days']))
		{
			return false;
		}

		// i think, that supplier ids are unique, i don't assume that in times management
		// but here, if there are several, i'll take the first one
		if ($supplier_times['date1'] < 1 && $supplier_times['date2'] < 1)
		{
			// this is for strtotime, just to get the eng. day according to the number
			// i cant save the days like this in database, cause i need to do some 
			// comparison with day numbers
			$days = array(
				"0" => "Sun",
				"1" => "Mon",
				"2" => "Tue",
				"3" => "Wed",
				"4" => "Thu",
				"5" => "Fri",
				"6" => "Sat",
				"7" => "Sun"
			);

			// just for clearance:
			$delivery_day = $supplier_times['day1'];
//			$order_day = $supplier_time['day2'];

			$delivery_time = strtotime("this ".date("l")) + ($supplier_times['days'] * 24 * 3600);
			// in php4, if next "day" is same as today, then it returns today, not +1 week, changed in php5 --dragut
			if (date("w") == $delivery_day)
			{
				$next_delivery_day = strtotime("next ".$days[$delivery_day]) + (7 * 24 * 3600);
			}
			else
			{
				$next_delivery_day = strtotime("next ".$days[$delivery_day]);
			}

			if ($delivery_time <= $next_delivery_day)
			{

				$date = $next_delivery_day;
			}
			else
			{
				$date = strtotime("+1 week", $next_delivery_day);
			}

		}
		else
		{
			if ($supplier_times['date1'] >= (time() + $supplier_times['days'] * 86400))
			{
				$date = $supplier_times['date1'];
			} 
			else
			{
				$date = $supplier_times['date2'];
			}
		}

		return $date;
	}
	
	/**

		@attrib name=give_me_times 

	**/
	function give_me_times($arr)
	{
		$this->read_template("give_me_times.tpl");

		$days = array(
			"---" => "---",
			"1" => "Esmasp&auml;ev",
			"2" => "Teisip&auml;ev",
			"3" => "Kolmap&auml;ev",
			"4" => "Neljap&auml;ev",
			"5" => "Reede",
			"6" => "Laup&auml;ev",
			"7" => "P&uuml;hap&auml;ev",
		);
		$suppliers = "";
		$suppliers_info = $this->db_fetch_array("SELECT * from taket_times");
		if (empty($suppliers_info))
		{
			$suppliers_info = array();
		}
		foreach($suppliers_info as $supplier)
		{
			$this->vars(array(
				"supplier_id" => $supplier['supplier_id'],
				"day1" => html::select(array(
					"name" => "suppliers[".$supplier['supplier_id']."][day1]",
					"options" => $days,
					"selected" => $supplier['day1'],
				)),
				"days" => html::textbox(array(
					"name" => "suppliers[".$supplier['supplier_id']."][days]",
					"size" => 7,
					"value" => $supplier['days'],
				)),
				"day2" => html::select(array(
					"name" => "suppliers[".$supplier['supplier_id']."][day2]",
					"options" => $days,
					"selected" => $supplier['day2'],
				)),
				"date1" => html::date_select(array(
					"name" => "suppliers[".$supplier['supplier_id']."][date1]",
					"value" => $supplier['date1'],
				)),
				"date2" => html::date_select(array(
					"name" => "suppliers[".$supplier['supplier_id']."][date2]",
					"value" => $supplier['date2'],
				)),
				"delete" => html::checkbox(array(
					"name" => "suppliers[".$supplier['supplier_id']."][delete]",
					"value" => $supplier['id'],
				)),
				"style" => "default_row",
			));
			$suppliers .= $this->parse("SUPPLIER");
		}
		// the row to add a new supplier
		$this->vars(array(
			"supplier_id" => html::textbox(array(
				"name" => "suppliers[new][supplier_id]",
				"size" => 10,
			)),
			"day1" => html::select(array(
				"name" => "suppliers[new][day1]",
				"options" => $days,
			)),
			"days" => html::textbox(array(
				"name" => "suppliers[new][days]",
				"size" => 7,
			)),
			"day2" => html::select(array(
				"name" => "suppliers[new][day2]",
				"options" => $days,
			)),
			"date1" => html::date_select(array(
				"name" => "suppliers[new][date1]",
			)),
			"date2" => html::date_select(array(
				"name" => "suppliers[new][date2]",
			)),
			"delete" => "",
			"style" => "new_row",
		));
		$suppliers .= $this->parse("SUPPLIER");

		$this->vars(array(
			"suppliers" => $suppliers,
			"reforb" => $this->mk_reforb("save_give_me_times", array("no_reforb" => true)),
		));
		

		return $this->parse();
	}

	/**
		@attrib name=save_give_me_times
		@param suppliers optional
	**/
	function save_give_me_times($arr)
	{
		$old_suppliers = $this->db_fetch_array("select * from taket_times");
		if (empty($old_suppliers))
		{
			$old_suppliers = array();
		}
		foreach ($old_suppliers as $old_supplier)
		{
                        $date1 = mktime(0,0,0,$arr['suppliers'][$old_supplier['supplier_id']]['date1']['month'], $arr['suppliers'][$old_supplier['supplier_id']]['date1']['day'], $arr['suppliers'][$old_supplier['supplier_id']]['date1']['year']);
                        $date2 = mktime(0,0,0,$arr['suppliers'][$old_supplier['supplier_id']]['date2']['month'], $arr['suppliers'][$old_supplier['supplier_id']]['date2']['day'], $arr['suppliers'][$old_supplier['supplier_id']]['date2']['year']);
			if (isset($arr['suppliers'][$old_supplier['supplier_id']]['delete']))
			{
				$this->db_query("delete from taket_times where id=".$old_supplier['id']);
			}
			else
			{
				$this->db_query("update taket_times set 
					day1='".$arr['suppliers'][$old_supplier['supplier_id']]['day1']."',
					days='".$arr['suppliers'][$old_supplier['supplier_id']]['days']."',
					day2='".$arr['suppliers'][$old_supplier['supplier_id']]['day2']."',
					date1='".$date1."',
					date2='".$date2."' 
					where id=".$old_supplier['id']
				);
			}
			
		}

		if (!empty($arr['suppliers']['new']['supplier_id']))
		{
			$date1 = mktime(0,0,0,$arr['suppliers']['new']['date1']['month'], $arr['suppliers']['new']['date1']['day'], $arr['suppliers']['new']['date1']['year']);
			$date2 = mktime(0,0,0,$arr['suppliers']['new']['date2']['month'], $arr['suppliers']['new']['date2']['day'], $arr['suppliers']['new']['date2']['year']);
			$days = (empty($arr['suppliers']['new']['days'])) ? 0 : $arr['suppliers']['new']['days'];
			$this->db_query("insert into taket_times set 
				supplier_id='".$arr['suppliers']['new']['supplier_id']."',
				day1='".$arr['suppliers']['new']['day1']."',
				days=".$days.",
				day2='".$arr['suppliers']['new']['day2']."',
				date1=".$date1.",
				date2=".$date2
			);
		}
		return $this->mk_my_orb("give_me_times");
	}

	function parallel_price_fetch($d)
	{
		$mh = curl_multi_init();

		$ch = array();
		foreach($d as $nr => $url)
		{
			$ch[$nr] = curl_init();
			curl_setopt($ch[$nr], CURLOPT_URL, $url);
			curl_setopt($ch[$nr], CURLOPT_HEADER, 0);
			curl_setopt($ch[$nr], CURLOPT_RETURNTRANSFER, true);
			curl_multi_add_handle($mh,$ch[$nr]);
		}

		$running=null;
		//execute the handles
		do {
		    curl_multi_exec($mh,$running);
		} while ($running > 0);

		$rv = array();
		foreach($d as $nr => $url)
		{
			$rv[$nr] = unserialize(curl_multi_getcontent($ch[$nr]));
			curl_multi_remove_handle($mh, $ch[$nr]);
		}

		curl_multi_close($mh);

		return $rv;
	}
}
?>
