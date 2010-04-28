<?php

namespace automatweb;


class shop_product_category_type_obj extends _int_object
{
	const AW_CLID = 1575;

	/** return categories
		@attrib api=1
		@returns
			object list
	**/
	public function get_categories()
	{
/*		$ol= new object_list();
		$conn = $this->connections_from(array(
			"type" => "RELTYPE_CATEGORY",
		));
		foreach($conn as $c)
		{
			$ol->add($c->prop("to"));
		}
		$ol->sort_by(array(
			"prop" => "name",
			"order" => "desc"
		));
		$ol->sort_by(array(
			"prop" => "ord",
			"order" => "asc"
		));
*/
		$prms = array(
			"class_id" => CL_SHOP_PRODUCT_CATEGORY,
			"lang_id" => array(),
			"site_id" => array(),
			"CL_SHOP_PRODUCT_CATEGORY.RELTYPE_CATEGORY(CL_SHOP_PRODUCT_CATEGORY_TYPE)" =>$this->id(),
			"sort_by" => "jrk asc, name asc",
		);
		return new object_list($prms);


		return $ol;
	}

	/** adds category
		@attrib api=1
	**/
	public function add_category($id)
	{
		$this->connect(array(
			"to" => $id,
			"reltype" => "RELTYPE_CATEGORY",
		));
	}

}

?>
