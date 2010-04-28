<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/shop/shop_warehouse_purchase_order.aw,v 1.2 2008/01/31 13:50:07 kristo Exp $
// shop_warehouse_purchase_order.aw - Ostutellimus 
/*

@classinfo syslog_type=ST_SHOP_WAREHOUSE_PURCHASE_ORDER relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@tableinfo shop_warehouse_purchase_order index=oid master_table=objects master_index=oid

@default table=objects
@default group=general

	@property date type=date_select table=shop_warehouse_purchase_order
	@caption Kuup&auml;ev

	@property supplier type=relpicker reltype=RELTYPE_SUPPLIER table=shop_warehouse_purchase_order
	@caption Hankija

	@property order_nr type=textbox table=shop_warehouse_purchase_order
	@caption Tellimuse nr.

	@property deadline type=date_select table=shop_warehouse_purchase_order
	@caption T&auml;itmist&auml;htaeg

	@property create_purchase_sheet type=checkbox ch_value=1 table=shop_warehouse_purchase_order
	@caption Mooduste ostu-saateleht

	@property append_to_purchase_sheet type=select table=shop_warehouse_purchase_order
	@caption Lisa ostu-saatelehele

	@property confirm type=checkbox ch_value=1 table=shop_warehouse_purchase_order
	@caption Kinnitatud

@groupinfo products caption="Tooted"
@default group=products

	@property products_table type=table no_caption=1
	@caption Toodete tabel

@reltype SUPPLIER value=1 clid=CL_CRM_COMPANY
@caption Hankija
*/

class shop_warehouse_purchase_order extends class_base
{
	const AW_CLID = 1040;

	function shop_warehouse_purchase_order()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "applications/shop/shop_warehouse_purchase_order",
			"clid" => CL_SHOP_WAREHOUSE_PURCHASE_ORDER
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
			"name" => "amount",
			"caption" => t("Kogus"),
		));
		$t->define_field(array(
			"name" => "unit",
			"caption" => t("&Uuml;hik"),
		));

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
			case 'date':
			case 'supplier':
			case 'deadline':
			case 'create_purchase_sheet':
			case 'append_to_purchase_sheet':
			case 'confirm':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'int'
				));
				return true;
			case 'order_nr':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'varchar(255)'
				));
				return true;
		}

		return false;
	}

}
?>
