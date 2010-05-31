<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/shop/shop_warehouse_selling_sheet.aw,v 1.2 2008/01/31 13:50:07 kristo Exp $
// shop_warehouse_selling_sheet.aw - Müügi-saateleht 
/*

@classinfo syslog_type=ST_SHOP_WAREHOUSE_SELLING_SHEET relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@tableinfo shop_warehouse_selling_sheet index=oid master_table=objects master_index=oid

@default table=objects
@default group=general

	@property date type=date_select table=shop_warehouse_selling_sheet
	@caption Kuup&auml;ev

	@property buyer type=relpicker reltype=RELTYPE_BUYER table=shop_warehouse_selling_sheet
	@caption Ostja

	@property selling_sheet_nr type=textbox table=shop_warehouse_selling_sheet
	@caption Saatelehe nr.

	@property confirmed type=checkbox ch_value=1 table=shop_warehouse_selling_sheet
	@caption Kinnitatud

	@property payment_deadline type=date_select table=shop_warehouse_selling_sheet
	@caption Tasumist&auml;htaeg

	@property payment_time type=date_select table=shop_warehouse_selling_sheet
	@caption Tasumiskuup&auml;ev

	@property payment_complete type=checkbox ch_value=1 table=shop_warehouse_selling_sheet
	@caption Tasutud

	@property create_bill type=checkbox ch_value=1 store=no
	@caption Moodusta m&uuml;&uuml;giarve

	@property append_to_bill type=select store=no
	@caption Lisa arvele

@reltype BUYER value=1 clid=CL_CRM_COMPANY
@caption Hankija

@reltype SHOP_PRODUCT value=2 clid=CL_SHOP_PRODUCT
@caption Lao toode

*/

class shop_warehouse_selling_sheet extends class_base
{
	const AW_CLID = 1039;

	function shop_warehouse_selling_sheet()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "applications/shop/shop_warehouse_selling_sheet",
			"clid" => CL_SHOP_WAREHOUSE_SELLING_SHEET
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

	function callback_mod_reforb(&$arr)
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

		$connections_to_products = $arr['obj_inst']->connections_from(array(
			"type" => "RELTYPE_SHOP_PRODUCT"
		));

		foreach($connections_to_products as $connection_to_products)
		{
			$t->define_data(array(
				"code" => "",
				"barcode" => "",
				"name" => "",
				"amount" => "",
				"unit" => "",
				"price" => "",
				"discount" => "",
				"sum" => "",
				"sale_price" => "",
			));
		}
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
			case 'selling_sheet_nr':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'varchar(255)'
				));
				return true;
			case 'date':
			case 'buyer':
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
