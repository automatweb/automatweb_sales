<?php

class shop_product_search_obj extends _int_object
{
	function get_order_center()
	{
		return new object($this->prop('oc'));
	}

	public function get_search_results()
	{
		enter_function("shop_product_search_obj::get_search_results");
		$no_products = array();
		$args = array(
			'class_id' => CL_SHOP_PACKET,
			"lang_id" => array(),
			"site_id" => array(),
			"status" => ($this->prop('find_only_active')) ? STAT_ACTIVE : array(STAT_ACTIVE, STAT_NOTACTIVE),
			'CL_SHOP_PACKET.RELTYPE_PRODUCT.id' =>	new obj_predicate_compare(OBJ_COMP_GREATER, 0),
		);
		if(strlen(automatweb::$request->arg('search_term')) > 0)
		{
			$args[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					'name' => '%'.str_replace(".", "" , automatweb::$request->arg('search_term')).'%',
					'CL_SHOP_PACKET.RELTYPE_PRODUCT.code' => '%'.str_replace(".", "" , automatweb::$request->arg('search_term')).'%',
					'CL_SHOP_PACKET.RELTYPE_PRODUCT.short_code' => '%'.str_replace(".", "" , automatweb::$request->arg('search_term')).'%'
				),
			));
		}
		if(is_oid(automatweb::$request->arg("search_category")))
		{
			$search_category_tree = new object_tree(array(
				"class_id" => array(CL_MENU, CL_DOCUMENT),
				"lang_id" => array(),
				"site_id" => array(),
				"parent" => automatweb::$request->arg("search_category"),
			));

			$search_category_ids = $search_category_tree->ids();
			if(count($search_category_ids) > 0)
			{
//				$args["CL_SHOP_PACKET.RELTYPE_CATEGORY.RELTYPE_CATEGORY(CL_PRODUCTS_SHOW).RELTYPE_ALIAS(CL_DOCUMENT)"] = $search_category_ids;
				$ol = new object_list(array(
					"class_id" => CL_SHOP_PRODUCT_CATEGORY,
					"CL_SHOP_PRODUCT_CATEGORY.RELTYPE_CATEGORY(CL_PRODUCTS_SHOW).RELTYPE_ALIAS(CL_DOCUMENT)" => $search_category_ids,
					"lang_id" => array(),
					"site_id" => array(),
				));
				if($ol->count() > 0)
				{
					$args["CL_SHOP_PACKET.RELTYPE_CATEGORY"] = $ol->ids();
				}
				else
				{
					$no_products = true;
				}
			}
			else
			{
				$no_products = true;
			}
		}
		if(is_oid($this->prop("products_object_list_filter_controller")))
		{
			eval($this->prop("products_object_list_filter_controller.formula"));
		}
		$products = $no_products ? new object_list() : new object_list($args);
		exit_function("shop_product_search_obj::get_search_results");
		return $products;
	}

	
}
