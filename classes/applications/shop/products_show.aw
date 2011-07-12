<?php
/*
@classinfo syslog_type=ST_PRODUCTS_SHOW relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=smeedia
@tableinfo aw_products_show master_index=brother_of master_table=objects index=aw_oid

@default table=aw_products_show
@default group=general

	@property packets type=relpicker multiple=1 store=connect reltype=RELTYPE_PACKET
	@caption Paketid
	@comment Paketid, mida kuvatakse

	@property categories type=relpicker multiple=1 store=connect reltype=RELTYPE_CATEGORY 
	@caption Tootekategooriad
	@comment Tootekategooriad millesse toode peaks kuuluma, et teda kuvataks

	@property columns type=textbox field=aw_columns
	@caption Tulpasid

	@property template type=select
	@caption Toodete n&auml;itamise template

	@property product_template type=select
	@caption &Uuml;he toote n&auml;itamise templeit

	@property type type=select
	@caption N&auml;idatavad klassi t&uuml;&uuml;bid

	@property oc type=relpicker reltype=RELTYPE_OC
	@caption Tellimiskeskkond
	@comment veebipood, mille tooteid see n&auml;itamise objekt n&auml;itab


### RELTYPES

@reltype CATEGORY value=1 clid=CL_SHOP_PRODUCT_CATEGORY
@caption Tootekategooria

@reltype PACKET value=2 clid=CL_SHOP_PACKET
@caption Pakett

@reltype OC value=3 clid=CL_SHOP_ORDER_CENTER
@caption Tellimiskeskkond


*/

class products_show extends class_base
{
	function products_show()
	{
		$this->init(array(
			"tpldir" => "applications/shop/products_show",
			"clid" => CL_PRODUCTS_SHOW
		));
		$this->types = array(
			CL_SHOP_PRODUCT => t("Toode"),
			CL_SHOP_PRODUCT_PACKAGING => t("Pakend"),
			CL_SHOP_PACKET => t("Pakett"),
		);
	}

	/** returns products showing template selection
		@attrib api=1
	**/
	public function templates()
	{
		$tm = get_instance("templatemgr");
		$ret = $tm->template_picker(array(
					"folder" => "applications/shop/products_show/"
				));;
		return $ret;
	}

	/** returns product showing template selection
		@attrib api=1
	**/
	public function product_templates()
	{
		$tm = get_instance("templatemgr");
		$ret = array();

		$dir = "applications/shop/shop_packet";
		$ret = $ret + $tm->template_picker(array(
			"folder" => $dir
		));

		$dir = "applications/shop/shop_product";
		$ret = $ret + $tm->template_picker(array(
			"folder" => $dir
		));
		$dir = "applications/shop/shop_product_packaging";

		$ret = $ret + $tm->template_picker(array(
			"folder" => $dir
		));
		return $ret;
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "template":
				$prop["options"] = $this->templates();
				if(sizeof($prop["options"]) < 2)
				{
					$prop["caption"].= "<br>".t("templates/applications/shop/products_show/");
				}
				break;

			case "product_template":
				$tm = get_instance("templatemgr");
				switch($arr["obj_inst"]->prop("type"))
				{
					case CL_SHOP_PACKET:
						$dir = "applications/shop/shop_packet";
						break;

					case CL_SHOP_PRODUCT:
						$dir = "applications/shop/shop_product";
						break;

					case CL_SHOP_PRODUCT_PACKAGING:
						$dir = "applications/shop/shop_product_packaging";
						break;
				}
				if($dir)
				{
					$prop["options"] = $tm->template_picker(array(
						"folder" => $dir
					));
					if(sizeof($prop["options"]) < 2)
					{
						$prop["caption"].= "<br>".t("templates/").$dir;
					}
				}
				break;

			case "type":
				$prop["options"] = $this->types;
				break;
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

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_products_show(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "product_template":
			case "template":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "varchar(63)"
				));
				return true;
			
			case "aw_columns":
			case "type":
			case "oc":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}

	function parse_alias($arr = array())
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	function set_cache($html)
	{
		$cache_dir = aw_ini_get("cache.page_cache")."/product_show";
		$master_cache = $cache_dir.$_SERVER["REQUEST_URI"].".tpl";

			if(!file_exists($cache_dir))
			{
				mkdir($cache_dir);
			}
			$fh = fopen($master_cache, 'w');
			fwrite($fh, $html);
			fclose($fh);
	}

	function show($arr)
	{
		$cache_dir = aw_ini_get("cache.page_cache")."/product_show";
		$master_cache = $cache_dir.$_SERVER["REQUEST_URI"].".tpl";

		if(file_exists($master_cache))
		{
			$cache = file($master_cache);
			return join ("" , $cache);
		}

		$ob = new object($arr["id"]);
		if(!empty($_GET["product"]) && $this->can("view" , $_GET["product"]))
		{
			$show_product = obj($_GET["product"]);
			$instance = get_instance($show_product->class_id());
			$instance->template = $ob->prop("product_template");
			$ret =  $instance->show(array(
				"id" => (int)$_GET["product"],
				"oc" => (int)$_GET["oc"],
			));
			$this->set_cache($ret);
			return $ret;
		}
		$oc = $ob->get_oc();
		$this->read_template($ob->get_template());
		$this->vars(array(
			"name" => $ob->prop("name"),
			"currency" => get_name($oc->get_currency()),
		));

		lc_site_load("shop", &$this);
		$products = $ob->get_web_items();

		$GLOBALS["order_center"] = $oc->id();
		
		$prod = "";//templeiti muutuja PRODUCT v22rtuseks
		$rows = "";
		
		$max = 4;//default
		$per_page = 16;//default products per page
		$page = empty($_GET["page"]) ? 0 : $_GET["page"];
		if($oc->prop("per_page"))
		{
			$per_page = $oc->prop("per_page");
		}

		$count = $count_all = 0;
		//teeb sorteerimise enne ära
//		$products->sort_by(array(
//			"prop" => "changed",
//			"order" => "desc"
//		));
		foreach($products->ids() as $product_id)
		{
			$count_all++;
			if($count_all <= ($per_page * $page))
			{
				continue;
			}
			$product = obj($product_id);
			$count++;
			$data_params = array("image_url" => 1,
				"min_price" => 1,
				"product_id" => 1,
				"brand_name" => 1,
			//"special_prices" => 1
				"min_special_price" => 1
			);
			$product_data = $product->get_data($data_params);

			// this one should be coming from the get_data() fn. probably, but i don't know at the moment how to make that object data list query to work
			// so i just use this one here:
			$min_special_price = (!empty($product_data['min_special_price'])) && $product_data['min_special_price'] != $product_data['min_price'] ? $product_data['min_special_price'] : 0;
			$product_data['PRODUCT_SPECIAL_PRICE'] = '';
			$product_data['special_price_visibility'] = '';
			if ($min_special_price > 0)
			{
				$product_data['special_price_visibility'] = '_specialPrice';
				$this->vars(array(
					'min_special_price' => $min_special_price,
					'min_special_price_without_zeroes' => $this->woz($min_special_price),
				));
				$product_data['PRODUCT_SPECIAL_PRICE'] = $this->parse('PRODUCT_SPECIAL_PRICE');
			}
			$product_data['min_special_price_without_zeroes'] = $this->woz($product_data['min_special_price']);
			$product_data['min_price_without_zeroes'] = $this->woz($product_data['min_price']); 
			$product_data["checkbox"] = html::checkbox(array(
				"name" => "add_to_cart[".$product_data["product_id"]."]",
				"value" => 1,
			));

			$product_data["product_link"] = aw_global_get("baseurl")."/".aw_global_get("section")."?product=".$product_data["id"]."&oc=".$oc->id();

			$category = $product->get_first_caregory_id();

			$product_data["menu"] = $ob->get_category_menu($category);
			$product_data["menu_name"] = get_name($product_data["menu"]);
			$this->vars($product_data);

			if($count >= $max && $this->is_template("ROW"))//viimane tulp yksk6ik mis reas
			{
				$count = 0;
				if($this->is_template("PRODUCT_END"))
				{
					$prod .= $this->parse("PRODUCT_END");
				}
				else
				{
					$prod .= $this->parse("PRODUCT");
				}
				$this->vars(array("PRODUCT" => $prod));
				$rows .= $this->parse("ROW");
				$prod = "";
			}
			elseif($count_all >= $products->count() && $this->is_template("ROW"))//viimane rida
			{
				$prod .= $this->parse("PRODUCT");
				$this->vars(array("PRODUCT" => $prod));
				$rows .= $this->parse("ROW");
			}
			else
			{
				$prod .= ($count_all === 1 and $this->is_template("PRODUCT_BEGIN")) ? $this->parse("PRODUCT_BEGIN") : $this->parse("PRODUCT");
			}

			if($count_all >= $per_page * ($page + 1))
			{
				break;
			}
		}
		$this->vars(array(
			"PRODUCT_BEGIN" => "",
			"PRODUCT_END" => "",
			"PRODUCT" => $prod,
			"ROW" => $rows
		));

		$pages = $products->count() / $per_page;
		$pages = (int)$pages;
		if($products->count() % $per_page) $pages++;
		if($pages > 1)
		{
			if($page > 0)
			{
				$this->vars(array("pager_url" => aw_url_change_var("page", $page - 1)));
				$this->vars(array("PAGE_PREV" => $this->parse("PAGE_PREV")));
			}
			if($page < ($pages-1))
			{
				$this->vars(array("pager_url" => aw_url_change_var("page", $page + 1)));
				$this->vars(array("PAGE_NEXT" => $this->parse("PAGE_NEXT")));
			}

			$page_str = "";
			
			$x = max(array(0,$page - 2));
			$y = 0;
			if($x+$y > 1)
			{
				$this->vars(array("pager_url" => aw_url_change_var("page", 0)));
				$page_str.= $this->parse("PAGE_SEP");
			}
			while($y < 5)
			{
				if($x+$y >= $pages)
				{
					break;
				}
				$this->vars(array("pager_url" => aw_url_change_var("page", ($x+$y))));
				$this->vars(array("pager_nr" => ($x + $y + 1)));

				
				if($x+$y == $page)
				{
					$page_str.= $this->parse("PAGE_SEL");
				}
				else
				{
					$page_str.= $this->parse("PAGE");
				}
				$y++;
			}

			if($x+$y < $pages)
			{
				$this->vars(array("pager_url" => aw_url_change_var("page", ($pages-1))));
				$page_str.= $this->parse("PAGE_SEP");
			}

			$this->vars(array(
				"PAGE" => $page_str,
				"PAGE_SEL" => " ",
			));
			$this->vars(array("PAGER" => $this->parse("PAGER")));
		}

		$data = array();
		$cart_inst = get_instance(CL_SHOP_ORDER_CART);
 		$data["submit_url"] = $this->mk_my_orb("submit_add_cart", array(
			"oc" => $oc->id(),
			"id" => $oc->prop("cart"),
		),CL_SHOP_ORDER_CART,false,false,"&amp;");

		if(!substr_count("orb.aw" ,$data["submit_url"] ))
		{
			$data["submit_url"] = str_replace(aw_ini_get("baseurl")."/" ,aw_ini_get("baseurl")."/orb.aw" , $data["submit_url"]);

		}
		$data["oc"] = $oc->id();

		$data["submit"] = html::submit(array(
			"value" => t("Lisa tooted korvi"),
		));
		$data["section"] = aw_global_get("section");
		$this->vars($data);
		$result = $this->parse();

//cachemine
		$this->set_cache($result);
//-------
		return $result;
	}

	// FIXME: DOCUMENT THIS! Prolly should use aw_math_calc::string2float().
	public function woz($number)
	{
		$number = str_replace("," , "" , $number);
		if(!($number%1 > 0))
		{
			return (int)$number;
		}

		return number_format($number , 2);
	}

}
