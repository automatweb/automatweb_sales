<?php

namespace automatweb;


class products_show_obj extends _int_object
{
	const AW_CLID = 1576;

	/** returns template name
		@attrib api=1
		@returns string
			template file name
	**/
	public function get_template()
	{
		if($this->prop("template"))
		{
			return $this->prop("template");
		}
		return "show.tpl";
	}

	/** returns products
		@attrib api=1
		@returns object list
	**/
	public function get_products()
	{
		$ol = new object_list();
		foreach($this->prop("categories") as $category)
		{
			$c = obj($category);
			$ol->add($c->get_products());
		}
		return $ol;
	}

	/** returns all items for showing
		@attrib api=1
		@returns object list
			product , packaging or packet objects
	**/
	public function get_web_items()
	{
enter_function("products_show::get_web_items");

		$categories = $this->get_categories();

		if(!$categories->count() && (!is_array($this->prop("packets")) || !sizeof($this->prop("packets"))))
		{
			$categories = $this->all_lower_categories();
		}

		$ol = new object_list();
		if($categories->count())
		{
			switch($this->prop("type"))
			{
				case CL_SHOP_PRODUCT://toode
					foreach($categories->arr() as $c)
					{
						$products = $c->get_products();
						$ol->add($products);
					}
					break;
				case CL_SHOP_PRODUCT_PACKAGINGS://pakend
					foreach($categories->arr() as $c)
					{
						$products = $c->get_packagings();
						$ol->add($products);
					}
					break;
				case CL_SHOP_PACKER:
				default:
					$filter = array(
						"class_id" => CL_SHOP_PACKET,
						"lang_id" => array(),
						"site_id" => array(),
						"CL_SHOP_PACKET.RELTYPE_CATEGORY" => $categories->ids(),
					);
					$ol = new object_list($filter);
					break;
			}
		}

		if(is_array($this->prop("packets")))
		{
			foreach($this->prop("packets") as $packet)
			{
				$ol->add($packet);
			}
		}
exit_function("products_show::get_web_items");
		return $ol;
	}

	/** returns order center object
		@attrib api=1 params=pos
		@returns object
	**/
	public function get_oc()
	{
		$ol = new object_list(array("class_id" => CL_SHOP_ORDER_CENTER, "lang_id" => array(), "site_id" => array()));
		return $ol->begin();
	}

	/** returns categories
		@attrib api=1
		@returns object list
	**/
	public function get_categories()
	{
		$ol = new object_list();	
		if(is_array($this->prop("categories")))
		{
			foreach($this->prop("categories") as $category)
			{
				$ol->add($category);
			}
		}
		return $ol;
	}

	private function all_lower_categories()
	{
enter_function("pll_lower_categories::get_web_items1");
		$ol = new object_list();
		$menu = $this->parent();
		$ot = new object_tree(array(
			"parent" => $menu,
			"class_id" => array(CL_MENU),
		));
		$menus = $ot->ids();
		if(sizeof($menus))
		{
			$categories = new object_list(array(
				"class_id" => CL_SHOP_PRODUCT_CATEGORY,
				"CL_SHOP_PRODUCT_CATEGORY.RELTYPE_CATEGORY(CL_PRODUCTS_SHOW).parent" => $menus
			));
		}
		else
		{
			$categories = new object_list();
		}
exit_function("pll_lower_categories::get_web_items1");
		return $categories;
	}

	/** adds category
		@attrib api=1 params=pos
		@param cat required oid
		@returns true
	**/
	public function add_category($cat)
	{
		if(is_oid($cat))
		{
			$cat = array($cat);
		}
		foreach($cat as $category)
		{
			$this->connect(array(
				"to" => $category,
				"reltype" => "RELTYPE_CATEGORY",
			));
		}
		return true;
	}

	/** removes category
		@attrib api=1 params=pos
		@param cat required oid
	**/
	public function remove_category($cat)
	{
		$this->disconnect(array(
			"from" => $cat,
		));
	}

	/** returns document id where product show object is connected to
		@attrib api=1
		@returns oid
			document object id
	**/
	public function get_document()
	{
		foreach($this->connections_to(array("from.class_id" => CL_DOCUMENT)) as $c)
		{
			return $c->prop("from");
		}
		return null;
	}

	/** returns category site menu id
		@attrib api=1 params=pos
		@param cat required oid
		@returns oid
			menu object id
	**/
	public function get_category_menu($cat)
	{
		$category = obj($cat);
		$ol = new object_list(array(
			"class_id" => CL_PRODUCTS_SHOW,
			"CL_PRODUCTS_SHOW.RELTYPE_CATEGORY" => $cat,
		));
		$ol = $ol->arr();
		$o = reset($ol);
		if(is_object($o))
		{
			$document = $o->get_document();
			if(is_oid($document))
			{
				$doc = obj($document);
				return $doc->parent();
			}
		}

		return null;
	}
}

?>
