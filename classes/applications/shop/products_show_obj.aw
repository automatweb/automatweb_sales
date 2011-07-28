<?php

class products_show_obj extends _int_object
{
	const CLID = 1576;

	public function awobj_get_type()
	{
		return explode(",", parent::prop("type"));
	}

	public function awobj_set_type($types)
	{
		return parent::set_prop("type", implode(",", $types));
	}

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
		$categories = $this->get_categories();

		if(!$categories->count() && (!is_array($this->prop("packets")) || !sizeof($this->prop("packets"))))
		{
			$categories = $this->all_lower_categories();
		}

		$ol = new object_list();
		if($categories->count())
		{
			$types = $this->awobj_get_type();

			if (in_array(shop_product_obj::CLID, $types))
			{
				foreach($categories->arr() as $c)
				{
					$products = $c->get_products();
					$ol->add($products);
				}
			}

			if (in_array(shop_product_packaging_obj::CLID, $types))
			{
				foreach($categories->arr() as $c)
				{
					$products = $c->get_products();
					$ol->add($products);
				}
			}

			if (in_array(shop_packet_obj::CLID, $types) or empty($types))
			{
				$filter = array(
					"class_id" => shop_packet_obj::CLID,
					"CL_SHOP_PACKET.RELTYPE_CATEGORY" => $categories->ids(),
					"status" => array(object::STAT_ACTIVE),
					"sort_by" => "objects.modified desc",
				);
				$ol->add(new object_list($filter));
			}
		}

		if(is_array($this->prop("packets")))
		{
			foreach($this->prop("packets") as $packet)
			{
				$ol->add($packet);
			}
		}

		$ol->sort_by_cb(array($this, "__sort_web_items"));

		return $ol;
	}

	public function __sort_web_items($a, $b)
	{
		if ($a->is_a(shop_packet_obj::CLID) and $b->is_a(shop_packet_obj::CLID))
		{
			return $a->ord() - $b->ord();
		}
		elseif ($a->is_a(shop_packet_obj::CLID))
		{
			return -1;
		}
		elseif ($b->is_a(shop_packet_obj::CLID))
		{
			return 1;
		}
		elseif ($a->is_a(shop_product_obj::CLID) and $b->is_a(shop_product_obj::CLID))
		{
			return $a->ord() - $b->ord();
		}
		elseif ($a->is_a(shop_product_obj::CLID))
		{
			return -1;
		}
		elseif ($b->is_a(shop_product_obj::CLID))
		{
			return 1;
		}
		return 0;
	}

	/** returns order center object
		@attrib api=1 params=pos
		@returns object
	**/
	public function get_oc()
	{
		if(is_oid($this->prop("oc")) && $GLOBALS["object_loader"]->cache->can("view" , $this->prop("oc")))
		{
			return obj($this->prop("oc"));
		}
aw_disable_acl();
		$ol = new object_list(array(
//			"limit" => 1,
			"class_id" => CL_SHOP_ORDER_CENTER, "lang_id" => array(), "site_id" => array()));
aw_restore_acl();
		$oc = $ol->begin();
		if(is_oid($oc))
		{
			return obj($oc);
		}
		return $oc;
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
		$ol = new object_list();
		$menu = $this->parent();
		$ot = new object_tree(array(
			"parent" => $menu,
			"class_id" => array(CL_MENU),

			"limit" => 100,//ajutine, ära kommiti
		));
		$menus = $ot->ids();
		if(sizeof($menus))
		{
			$categories = new object_list(array(
				"class_id" => CL_SHOP_PRODUCT_CATEGORY,
				"CL_SHOP_PRODUCT_CATEGORY.RELTYPE_CATEGORY(CL_PRODUCTS_SHOW).parent" => $menus,

			"limit" => 100,//ajutine, ära kommiti
			));
		}
		else
		{
			$categories = new object_list();
		}
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
			"limit" => 1,
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
