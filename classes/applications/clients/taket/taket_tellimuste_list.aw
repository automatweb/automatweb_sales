<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/clients/taket/taket_tellimuste_list.aw,v 1.1 2008/10/01 14:17:40 markop Exp $
// taket_tellimuste_list.aw - Taket tellimuste nimekiri 
/*

@classinfo syslog_type= relationmgr=yes

@default table=objects
@default group=general

*/

class taket_tellimuste_list extends class_base
{
	function taket_tellimuste_list()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "taket/taket_tellimuste_list",
			"clid" => CL_TAKET_TELLIMUSTE_LIST
		));
		lc_site_load('taket_tellimuste_list',&$this);
	}

	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	/** this shows the object. not strictly necessary, but you'll probably need it, it is used by parse_alias 
		
		@attrib name=show params=name default="0"
		
		@param sort optional
		@param dir optional
		
		@returns
		
		
		@comment

	**/
	function show($arr)
	{
		$ob = new object($arr["id"]);

		//tavajuuser saab j2rgmise listingu
		$this->read_template("show.tpl");
		$this->sub_merge=1;
		
		//testing xmlrpc
		include('IXR_Library.inc.php');
		$hosts = aw_ini_get('taket.xmlrpchost');
		$path = aw_ini_get("taket.xmlrpcpath");
		$port = aw_ini_get("taket.xmlrpcport");
		$client = new IXR_Client($hosts[0], $path[0], $port[0]);
		//listime siia tellimused

		//"sort by" logic
		$sortBy = 'timestmp';
		$dirs= 'desc';
		$options = array('timestmp','order_id','price','contact','transport','status', "location");
		$css = array(
					'timestmpcss' => 'listTitle',
					'order_idcss' => 'listTitle',
					'pricecss' => 'listTitle',
					'contactcss' => 'listTitle',
					'transportcss' => 'listTitle',
					'statuscss' => 'listTitle',
					'commentcss' => 'listTitle',
				);
		$dir = array(
					'timestmpdir' => 'asc',
					'order_iddir' => 'asc',
					'pricedir' => 'asc',
					'contactdir' => 'asc',
					'transportdir' => 'asc',
					"locationdir" => "asc",
					'statusdir' => 'asc',
					'commentdir' => 'asc',
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
		else
		{
			$css['timestmpcss']='listTitlesort';
		}	
		$this->vars($css);
		$this->vars($dir);

		$ol = new object_list(array(
					'parent' => aw_ini_get('taket_order.order_parent_id'),
					'class_id' => CL_TAKET_ORDER,
					'user_id' => users::get_oid_for_uid(aw_global_get('uid')),
					'lang_id' => array(),
					'sort_by' => 'taket_orders.'.$sortBy.' '.$dirs,
					'modified' => new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, strtotime("-1 month"))
				));
/*
		$ol->sort_by(array(
			'prop'=>'transport',
			'order'=>'desc'
		));
*/
		for($o = $ol->begin();!$ol->end();$o=$ol->next())
		{
			$nol = new object_list(array(
				"class_id" => CL_TAKET_ORDER_ITEM,
				"limit" => 1,
				"order_id" => $o->id(),
			));
			if($ox = $nol->begin())
			{
				$ebasket = $ox->prop("ebasket_name");
			}
			$this->vars(array(
				'id' => $o->id(),
				'timestmp' => date('d.m.Y H:i:s', $o->prop('timestmp')),
				//'timestmp' => $o->prop('timestmp'),
				"ebasket" => $ebasket,
				'price' => number_format($o->prop('price'),2,'.',''),
				'comments' => $o->prop('comments'),
				'transport' => $o->prop('transport'),
				'contact' => $o->prop('contact'),
				'status' => $o->prop('status'),
				"location" => $o->prop("location"),
			));
			$this->parse('tellimus');
		}
		return $this->parse();
	}

	/**  
		
		@attrib name=show_order params=name default="0"
		
		@param order_id required
		@param sort optional
		@param dir optional
		
		@returns
		
		
		@comment

	**/
	function show_order($arr)
	{
		$this->read_template('show_order.tpl');
		$this->sub_merge=1;

		$o = new object($arr['order_id']);
		//if not right type and not right userid then show empty template
		if(!($o->class_id()==CL_TAKET_ORDER
				&& $o->prop('user_id')==users::get_oid_for_uid(aw_global_get('uid'))))
				return $this->parse();
		
		//sort by logic
		$sortBy = 'id';
		$dirs= 'asc';
		$options = array('product_code','product_name','price','discount','finalprice','quantity');
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
		else
		{
			$css['product_codecss']='listTitlesort';
		}

		if($arr['sort']!='finalprice')
		{
			$sortBy='taket_order_items.'.$sortBy.' '.$dirs;
		}
		else
		{
			$sortBy='(taket_order_items.price*(1-(discount/100))) '.$dirs;
		}
		
		$this->vars($css);
		$this->vars($dir);
		$this->vars(array('orderId'=>$arr['order_id']));

		$ol = new object_list(array(
			'class_id' => CL_TAKET_ORDER_ITEM,
			'parent' => aw_ini_get('taket_order.order_item_parent_id'),
			'order_id' => $o->id(),
			'lang_id' => array(),
			'sort_by'=> $sortBy
		));
		for($obj=$ol->begin();!$ol->end();$obj=$ol->next())
		{
			$finalPrice = 0;
			$tarjoushinta = (int)$obj->meta('tarjoushinta');
			if ($tarjoushinta > 0)
			{
				$finalPrice = number_format($obj->prop('price'), 2, '.', '');
			}
			else
			{
				$finalPrice = number_format(($obj->prop('price')*(1-$obj->prop('discount')/100)),2,'.','');
			}
			$this->vars(array(
				'order_id' => $obj->prop('order_id'),
				'product_code' => $obj->prop('product_code'),
				'quantity' => $obj->prop('quantity'),
				'price' => number_format($obj->prop('price'),2,'.',''),
			//	'finalprice' => number_format(($obj->prop('price')*(1-$obj->prop('discount')/100)),2,'.',''),
				'finalprice' => $finalPrice,
				'tarjoushinta' => number_format($obj->meta('tarjoushinta'), 2, '.', ''),
				'discount' => $obj->prop('discount'),
				'product_name' => $obj->prop('product_name')
			));

			$this->parse('toode');
		}

		$this->vars(array(
			'priceWithoutTax' => number_format($o->prop('price')/1.18,2,'.',''),
			'tax' => number_format($o->prop('price')/1.18*0.18,2,'.',''),
			'priceGrandTotal' => number_format($o->prop('price'),2,'.',''),
			'status' => $o->prop('status'),
			'orderid' => $o->id()
		));
		
		return $this->parse();
	}

	function unix_timestamp($timestmp)
	{
		$year = substr($timestmp, 0, 4);
		$month = substr($timestmp, 4, 2);
		$day = substr($timestmp, 6, 2);
		$hour = substr($timestmp, 8, 2);
		$min = substr($timestmp, 10, 2);
		$sec = substr($timestmp, 12,2);
		//if u want to u can check it yourself :)
		//echo $year.' '.$month.' '.$day.' '.$hour.' '.$min.' '.$sec.'--'.$timestmp.'<br>';
		return mktime($hour, $min, $sec, $month, $day, $year, 0);
		
	}
}
?>
