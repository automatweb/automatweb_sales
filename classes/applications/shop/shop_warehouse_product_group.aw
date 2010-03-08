<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/shop/shop_warehouse_product_group.aw,v 1.2 2008/01/31 13:50:07 kristo Exp $
// shop_warehouse_product_group.aw - Tootegrupp 
/*

@classinfo syslog_type=ST_SHOP_WAREHOUSE_PRODUCT_GROUP relationmgr=yes no_status=1 prop_cb=1 maintainer=kristo

@tableinfo shop_warehouse_product_group index=oid master_table=objects master_index=oid

@default table=objects
@default group=general

	@property code type=textbox table=shop_warehouse_product_group
	@caption Kood

	@property percent type=textbox table=shop_warehouse_product_group
	@caption Katte %

@groupinfo products caption="Tooted"
@default group=products

	@layout products_frame type=hbox width=20%:80%

		@layout search_params_frame type=vbox parent=products_frame

			@property search_name type=textbox store=no parent=search_params_frame
			@caption Nimetus

			@property search_code type=textbox store=no parent=search_params_frame
			@caption Kood

		@layout search_result_frame type=vbox parent=products_frame

			@property search_result_table type=table parent=search_result_frame
			@caption Otsingu tulemused

	@property products_table type=table
	@caption Grupis olevad tooted

@reltype PRODUCT value=1 clid=CL_SHOP_PRODUCT
@caption Toode

*/

class shop_warehouse_product_group extends class_base
{
	function shop_warehouse_product_group()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "applications/shop/shop_warehouse_product_group",
			"clid" => CL_SHOP_WAREHOUSE_PRODUCT_GROUP
		));
	}

	//////
	// class_base classes usually need those, uncomment them if you want to use them
	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- get_property --//
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- set_property --//

		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	////
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

	function _get_search_result_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->define_field(array(
			"name" => "code",
			"caption" => t("Tootekood"),
		));
		$t->define_field(array(
			"name" => "barcode",
			"caption" => t("Ribakood"),
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimetus"),
		));
		$t->define_field(array(
			"name" => "select",
			"caption" => t("Vali"),
		));
	}

	function _get_products_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->define_field(array(
			"name" => "code",
			"caption" => t("Tootekood"),
		));
		$t->define_field(array(
			"name" => "barcode",
			"caption" => t("Ribakood"),
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimetus"),
		));
		$t->define_field(array(
			"name" => "select",
			"caption" => t("Vali"),
			"align" => "center"
		));

		$connections_to_products = $arr['obj_inst']->connections_from(array(
			"type" => "RELTYPE_PRODUCT",
		));

		foreach ($connections_to_products as $connection_to_product)
		{
			$product_id = $connection_to_product->prop("to");
			$product_object = $connection_to_product->to();

			$t->define_data(array(
				"code" => $product_object->prop("code"),
				"barcode" => $product_object->prop("barcode"),
				"name" => $product_object->name(),
				"select" => html::checkbox(array(
					"name" => "selected_ids[$product_id]",
					"value" => $product_id
				)),
			));
		}
	}

	function callback_mod_retval($arr)
	{
		// otsingus saab seda kasutada
	}

	/**
		DB UPGRADE
	**/
	function do_db_upgrade($table, $field, $query, $error)
	{
		// this should be the way to detect, if table exist:
		if (empty($field))
		{
			$this->db_query('CREATE TABLE '.$table.' (oid INT PRIMARY KEY NOT NULL)');
			return true;
		}

		switch ($field)
		{
			case 'code':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'varchar(255)'
				));
				return true;
			case 'percent':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'double'
				));
				return true;
		}

		return false;
	}

}
?>
