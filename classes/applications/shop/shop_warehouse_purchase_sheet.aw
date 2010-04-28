<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/shop/shop_warehouse_purchase_sheet.aw,v 1.2 2008/01/31 13:50:07 kristo Exp $
// shop_warehouse_purchase_sheet.aw - Ostu-saateleht 
/*

@classinfo syslog_type=ST_SHOP_WAREHOUSE_PURCHASE_SHEET relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@tableinfo shop_warehouse_purchase_sheet index=oid master_table=objects master_index=oid

@default table=objects
@default group=general

	@property date type=date_select table=shop_warehouse_purchase_sheet
	@caption Kuup&auml;ev

	@property supplier type=relpicker reltype=RELTYPE_SUPPLIER table=shop_warehouse_purchase_sheet
	@caption Hankija

	@property purchase_sheet_nr type=textbox table=shop_warehouse_purchase_sheet
	@caption Saatelehe nr.

	@property confirmed type=checkbox ch_value=1 table=shop_warehouse_purchase_sheet
	@caption Kinnitatud

	@property payment_deadline type=date_select table=shop_warehouse_purchase_sheet
	@caption Tasumist&auml;htaeg

	@property payment_time type=date_select table=shop_warehouse_purchase_sheet
	@caption Tasumiskuup&auml;ev

	@property payment_complete type=checkbox ch_value=1 table=shop_warehouse_purchase_sheet
	@caption Tasutud

	@property create_bill type=checkbox ch_value=1 store=no
	@caption Moodusta ostuarve

	@property append_to_bill type=select store=no
	@caption Lisa arvele

@groupinfo products caption="Tooted"
@default group=products

	@property products_table type=table no_caption=1
	@caption Toodete tabel

@groupinfo other_costs caption="Muud kulud"
@default group=other_costs

	@property other_costs_table type=table no_caption=1
	@caption Muude kulude tabel


@reltype SUPPLIER value=1 clid=CL_CRM_COMPANY
@caption Hankija

@reltype SHOP_PRODUCT value=2 clid=CL_SHOP_PRODUCT
@caption Lao toode
*/

class shop_warehouse_purchase_sheet extends class_base
{
	const AW_CLID = 1037;

	function shop_warehouse_purchase_sheet()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "applications/shop/shop_warehouse_purchase_sheet",
			"clid" => CL_SHOP_WAREHOUSE_PURCHASE_SHEET
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

	function _get_products_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->define_field(array(
			"name" => "code",
			"caption" => t("Kood"),
			"align" => "center", 
		));
		$t->define_field(array(
			"name" => "barcode",
			"caption" => t("Ribakood"),
			"align" => "center", 
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimetus"),
			"align" => "center", 
		));
		$t->define_field(array(
			"name" => "amount",
			"caption" => t("Kogus"),
			"align" => "center", 
		));
		$t->define_field(array(
			"name" => "unit",
			"caption" => t("&Uuml;hik"),
			"align" => "center", 
		));
		$t->define_field(array(
			"name" => "price",
			"caption" => t("Hind"),
			"align" => "center", 
		));
		$t->define_field(array(
			"name" => "discount",
			"caption" => t("Soodustus"),
			"align" => "center", 
		));
		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa"),
			"align" => "center", 
		));
		$t->define_field(array(
			"name" => "sale_price",
			"caption" => t("M&uuml;&uuml;gi hind"),
			"align" => "center", 
		));


		// new products:
		for ($i = 0; $i < 10; $i++)
		{
			$t->define_data(array(
				"code" => html::textbox(array(
					"name" => "new_products[$i][code]",
					"size" => 12
				)),
				"barcode" => html::textbox(array(
					"name" => "new_products[$i][barcode]",
					"size" => 12 
				)), 
				"name" => html::textbox(array(
					"name" => "new_products[$i][name]",
					"size" => 30
				)),
				"amount" => html::textbox(array(
					"name" => "new_products[$i][amount]",
					"size" => 5
				)),
				"unit" => html::textbox(array(
					"name" => "new_products[$i][unit]",
					"size" => 7
				)),
				"price" => html::textbox(array(
					"name" => "new_products[$i][price]",
					"size" => 10 
				)),
				"discount" => html::textbox(array(
					"name" => "new_products[$i][discount]",
					"size" => 7
				)),
				"sum" => html::textbox(array(
					"name" => "new_products[$i][sum]",
					"size" => 10
				)),
				"sale_price" => html::textbox(array(
					"name" => "new_products[$i][sale_price]",
					"size" => 10
				)) 
			));

		}

		$connections_to_products = $arr['obj_inst']->connections_from(array(
			"type" => "RELTYPE_SHOP_PRODUCT"
		));
		foreach($connections_to_products as $connection_to_product)
		{
			$product = $connection_to_product->to();
			
			$amount = $product->meta('amount');
			$discount = $product->meta('discount');
			$price = $product->prop('price'); // sale price

			$t->define_data(array(
				'code' => $product->prop('code'),
				'barcode' => $product->prop('barcode'),
				'name' => $product->name(),
				'amount' => $amount,
				'unit' => $product->prop('user1'),
				'price' => $product->prop('purchase_price'),
				'discount' => $discount."%",
				'sum' => $amount * $price,
				'sale_price' => $product->prop('price'),
			));
		}

		return PROP_OK;
	}

	function _set_products_table($arr)
	{
		$new_products_info = $arr['request']['new_products'];
		foreach( $new_products_info as $new_product_info)
		{
			if (!empty($new_product_info['code']))
			{
				$new_product = new object();
				$new_product->set_class_id(CL_SHOP_PRODUCT);
				$new_product->set_parent($arr['obj_inst']->id());
		
				$new_product->set_name($new_product_info['name']);
				$new_product->set_prop("code", $new_product_info['code']);
				$new_product->set_prop("barcode", $new_product_info['barcode']);
				$new_product->set_prop("user1", $new_product_info['unit']);
				$new_product->set_prop("price", $new_product_info['sale_price']);
				$new_product->set_prop("purchase_price", $new_product_info['price']);

				$new_product->set_meta('amount', $new_product_info['amount']);
				$new_product->set_meta('discount', $new_product_info['discount']);

				$new_product->save();

				$arr['obj_inst']->connect(array(
					'to' => $new_product,
					'type' => "RELTYPE_SHOP_PRODUCT",
				));
			}
		}
	}

	function _get_other_costs_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimetus"),
		));
		$t->define_field(array(
			"name" => "amount",
			"caption" => t("Kogus"),
		));
		$t->define_field(array(
			"name" => "unit",
			"caption" => t("&Uuml;hik"),
		));
		$t->define_field(array(
			"name" => "price",
			"caption" => t("Hind"),
		));
		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa"),
		));
		return PROP_OK;		

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
			case 'purchase_sheet_nr':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'varchar(255)'
				));
				return true;
			case 'date':
			case 'supplier':
			case 'confirmed':
			case 'payment_deadline':
			case 'payment_time':
			case 'payment_complete':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'int'
				));
				return true;
		}

		return false;
	}


}
?>
