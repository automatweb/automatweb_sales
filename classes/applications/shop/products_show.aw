<?php
/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_products_show master_index=brother_of master_table=objects index=aw_oid

@default table=aw_products_show
@default group=general

	@property packets type=relpicker multiple=1 store=connect reltype=RELTYPE_PACKET
	@caption Paketid
	@comment Paketid, mida kuvatakse

	@property categories type=relpicker multiple=1 store=connect reltype=RELTYPE_CATEGORY
	@caption Tootekategooriad
	@comment Tootekategooriad millesse toode peaks kuuluma, et teda kuvataks

	@property products type=relpicker multiple=1 store=connect reltype=RELTYPE_PRODUCTS
	@caption Tooted
	@comment Konkreetne toodete valik

	@property columns type=textbox field=aw_columns
	@caption Tulpasid

	@property template type=select
	@caption Toodete n&auml;itamise template

	@property product_template type=select
	@caption &Uuml;he toote n&auml;itamise templeit

	@property type type=select multiple=1 field=aw_type
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

@reltype PRODUCTS value=4 clid=CL_SHOP_PRODUCT,CL_SHOP_PRODUCT_PACKAGING
@caption Toode

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

	public function _get_categories($arr)
	{
		$arr["prop"]["value"] = $arr["obj_inst"]->prop("categories");
		return class_base::PROP_OK;
	}

	/** returns products showing template selection
		@attrib api=1
	**/
	public function templates()
	{
		$tm = new templatemgr();
		$ret = $tm->template_picker(array(
			"folder" => "applications/shop/products_show/"
		));
		return $ret;
	}

	/** returns product showing template selection
		@attrib api=1
	**/
	public function product_templates()
	{
		$tm = new templatemgr();
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
					$prop["caption"].= html::linebreak().t("templates/applications/shop/products_show/");
				}
				break;

			case "product_template":
				$tm = new templatemgr();
				$prop["options"] = array();
				foreach ($arr["obj_inst"]->prop("type") as $type)
				{
					switch($type)
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

					if(!empty($dir))
					{
						$prop["options"] += $tm->template_picker(array(
							"folder" => $dir
						));
					}
				}
				break;

			case "type":
				$prop["options"] = $this->types;
				break;
		}

		return $retval;
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

			case "aw_type":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "varchar(15)"
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
		return;
		// TODO: Should use Memcached! Not sure if AW has all the necessary developments, though...
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
		$cache_dir = aw_ini_get("cache.page_cache")."product_show";
		$master_cache = $cache_dir.$_SERVER["REQUEST_URI"].".tpl";
		aw_translations::load("products_show.tpl.show");

		if(file_exists($master_cache))
		{
			$cache = file($master_cache);
		}

		$ob = new object($arr["id"]);
		$product_oid = isset($arr["product"]) ? (int) $arr["product"] : 0;
		if(acl_base::can("view" , $product_oid))
		{
			$show_product = obj($product_oid);
			$instance = get_instance($show_product->class_id());
			$instance->template = $ob->prop("product_template");
			$ret = $instance->show(array(
				"id" => $product_oid,
				"oc" => (int)$arr["oc"],
			));
			$this->set_cache($ret);
			return $ret;
		}

		$oc = $ob->get_oc();

		$this->read_template($ob->get_template());
		$this->vars(array(
			"name" => $ob->trans_get_val("name"),
			"currency" => get_name($oc->get_currency()),
		));

		//	The products will be ordered by products_show_obj::get_web_items()
		$products = $ob->get_web_items();

		$GLOBALS["order_center"] = $oc->id();

		$ROW = "";
		$PARSED_SUBS = array(
			"PRODUCT" => "",
			"PACKET" => "",
			"PACKAGING" => "",
		);
		$HAS_SUBS = array();

		$max = 4;	//default, TODO: This should be configurable:
		$per_page = 16;	//default products per page

		$page = empty($arr["page"]) ? 0 : $arr["page"];
		if($oc->prop("per_page"))
		{
			$per_page = $oc->prop("per_page");
		}

		$products->slice($per_page * $page, $per_page);
		if ($products->count() > 0)
		{
			$product = $products->begin();

			$count_all = $count_row = 0;
			do
			{
				$count_all++;
				$count_row++;

				$SUB = $this->__warehouse_item_sub_name($product);
				$HAS_SUBS[] = $SUB;

				$data_params = array("image_url" => 1,
					"min_price" => 1,
					"product_id" => 1,
					"brand_name" => 1,
				//"special_prices" => 1
					"min_special_price" => 1
				);
				$product_data = $product->get_data($data_params);

				$this->__prepare_data($product_data, $ob, $oc, $product);

				$this->vars($product_data);

				if (isset($product_data["special_price"]) and strlen(trim($product_data["special_price"])) > 0)
				{
					$this->vars(array(
						"HAS_SPECIAL_PRICE" => $this->parse("HAS_SPECIAL_PRICE"),
						"NO_SPECIAL_PRICE" => "",
					));
				}
				else
				{
					$this->vars(array(
						"HAS_SPECIAL_PRICE" => "",
						"NO_SPECIAL_PRICE" => $this->parse("NO_SPECIAL_PRICE"),
					));
				}

				if ($product->is_a(shop_packet_obj::CLID) and  $this->is_template("PACKET.ROW"))
				{
					$PACKET_ROW = "";

					$packet_rows = $product->get_rows();
					if ($packet_rows->count() > 0)
					{
						$row_prefix = "row.";
						$item_prefix = "{$row_prefix}item.";
						$packet_row = $packet_rows->begin();
						do
						{
							$this->vars(array(
								"{$row_prefix}amount" => $packet_row->prop("amount"),
							));

							$item = obj($packet_row->prop("item"));
							$item_data = $item->get_data(array("prefix" => $item_prefix));
							$this->__prepare_data($item_data, $ob, $oc, $item, $item_prefix);

							$this->vars($item_data);

							$PACKET_ROW .= $this->parse("PACKET.ROW");
						} while ($packet_row = $packet_rows->next());
					}
					$this->vars_safe(array(
						"PACKET.ROW" => $PACKET_ROW,
					));
				}

				if ($product->is_a(shop_product_obj::CLID) and $this->is_template("PRODUCT.PACKAGING"))
				{
					$PACKAGING = "";
					$packaging_prefix = "packaging.";

					$packagings = $product->get_packagings();
					if ($packagings->count() > 0)
					{
						$packaging = $packagings->begin();
						do
						{
							$packaging_data = $packaging->get_data(array(
								"prefix" => $packaging_prefix,
							));

							$this->__prepare_data($packaging_data, $ob, $oc, $packaging, $packaging_prefix);

							$this->vars($packaging_data);

							if (isset($packaging_data["{$packaging_prefix}special_price"]) and strlen(trim($packaging_data["{$packaging_prefix}special_price"])) > 0)
							{
								$this->vars(array(
									"PACKAGING.HAS_SPECIAL_PRICE" => $this->parse("PACKAGING.HAS_SPECIAL_PRICE"),
									"PACKAGING.NO_SPECIAL_PRICE" => "",
								));
							}
							else
							{
								$this->vars(array(
									"PACKAGING.HAS_SPECIAL_PRICE" => "",
									"PACKAGING.NO_SPECIAL_PRICE" => $this->parse("PACKAGING.NO_SPECIAL_PRICE"),
								));
							}

							$PACKAGING .= $this->parse("PACKAGING");
						} while ($packaging = $packagings->next());
					}

					$this->vars(array(
						"PACKAGING" => $PACKAGING,
					));
				}

				if ($this->is_template("IMAGE"))
				{
					$this->vars(array(
						"IMAGE" => !empty($product_data["image_url"]) ? $this->parse("IMAGE") : ""
					));
				}

				if($count_row >= $max && $this->is_template("ROW"))//viimane tulp yksk6ik mis reas
				{
					$count_row = 0;
					if($this->is_template("{$SUB}_END"))
					{
						$PARSED_SUBS[$SUB] .= $this->parse("{$SUB}_END");
					}
					else
					{
						$PARSED_SUBS[$SUB] .= $this->parse($SUB);
					}
					$this->vars(array($SUB => $PARSED_SUBS[$SUB]));
					$ROW .= $this->parse("ROW");
					$PARSED_SUBS[$SUB] = "";
				}
				elseif($count_all >= $products->count() && $this->is_template("ROW"))//viimane rida
				{
					$PARSED_SUBS[$SUB] .= $this->parse($SUB);
					$this->vars(array($SUB => $PARSED_SUBS[$SUB]));
					$ROW .= $this->parse("ROW");
				}
				else
				{
					$PARSED_SUBS[$SUB] .= ($count_all === 1 and $this->is_template("{$SUB}_BEGIN")) ? $this->parse("{$SUB}_BEGIN") : $this->parse($SUB);
				}
			}
			while ($product = $products->next());
		}

		$SUB = $this->__warehouse_item_sub_name(new object());//TODO: $SUB oli siin defineerimata, kontrollida kas default "PRODUCT" on sobiv
		$this->vars_safe($PARSED_SUBS + array(
			"PRODUCT_BEGIN" => "",
			"PRODUCT_END" => "",
			"PACKET_BEGIN" => "",
			"PACKET_END" => "",
			"PACKAGING_BEGIN" => "",
			"PACKAGING_END" => "",
			"ROW" => $ROW
		));

		foreach ($HAS_SUBS as $HAS_SUB)
		{
			$this->vars_safe(array(
				"HAS_{$HAS_SUB}S" => $this->parse("HAS_{$HAS_SUB}S"),
			));
		}

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
		$cart_inst = new shop_order_cart();
 		$data["submit_url"] = $this->mk_my_orb("submit_add_cart", array(
			"oc" => $oc->id(),
			"id" => $oc->prop("cart"),
		), CL_SHOP_ORDER_CART, false, false, "&amp;");

		if(!substr_count("orb.aw" ,$data["submit_url"] ))
		{
			$data["submit_url"] = str_replace(aw_ini_get("baseurl"), aw_ini_get("baseurl")."orb.aw", $data["submit_url"]);

		}
		$data["oc"] = $oc->id();
		$data["cart"] = $oc->prop("cart");

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

	protected function __warehouse_item_sub_name($item)
	{
		switch($item->class_id())
		{
			case shop_packet_obj::CLID:
				$SUB = "PACKET";
				break;

			case shop_product_packaging_obj::CLID:
				$SUB = "PACKAGING";
				break;

			default:
				$SUB = "PRODUCT";
		}

		return $this->is_template($SUB) ? $SUB : "PRODUCT";
	}

	// FIXME: DOCUMENT THIS! Prolly should use aw_math_calc::string2float(). woz = WithOutZeros
	public function woz($number)
	{
		$number = str_replace("," , "" , $number);
		if(!($number%1 > 0))
		{
			return (int)$number;
		}

		return number_format($number , 2);
	}

	/**
		TODO: Throw out unnecessary stuff!
	**/
	protected function __prepare_data(&$data, $products_show, $oc, $o, $prefix = "")
	{
		// this one should be coming from the get_data() fn. probably, but i don't know at the moment how to make that object data list query to work
		// so i just use this one here:
		$min_special_price = (!empty($data["{$prefix}min_special_price"])) && $data["{$prefix}min_special_price"] != $data["{$prefix}min_price"] ? $data["{$prefix}min_special_price"] : 0;
		if ($min_special_price > 0)
		{
			$this->vars(array(
				"min_special_price" => $min_special_price,
				"min_special_price_without_zeroes" => $this->woz($min_special_price),
			));
		}
		$data["{$prefix}min_special_price_without_zeroes"] = $this->woz(isset($data["{$prefix}min_special_price"]) ? $data["{$prefix}min_special_price"] : 0);
		$data["{$prefix}min_price_without_zeroes"] = $this->woz($data["{$prefix}min_price"]);
		if ($this->is_template("checkbox"))
		{
			$data["{$prefix}checkbox"] = html::checkbox(array(
				"name" => "add_to_cart[".$data["{$prefix}product_id"]."]",
				"value" => 1
			));
		}

		$data["{$prefix}product_link"] = aw_global_get("baseurl").aw_global_get("section")."?product=".$data["{$prefix}id"]."&oc=".$oc->id();

		$category = $o->get_first_category_id();

		if (is_oid($category))
		{
			$data["{$prefix}menu"] = $products_show->get_category_menu($category);
			$data["{$prefix}menu_name"] = get_name($data["{$prefix}menu"]);
		}

		$data["{$prefix}price"] = $this->number_format($data["{$prefix}price"]);
		$data["{$prefix}special_price"] = $this->number_format($data["{$prefix}special_price"]);
		$data["{$prefix}min_price"] = $this->number_format($data["{$prefix}min_price"]);
	}

	/**
		TODO: This one should be configurable!
	**/
	protected function number_format($value)
	{
		$precision = 2;
		return is_numeric($value) ? number_format(aw_math_calc::string2float($value), $precision) : $value;
	}

}
