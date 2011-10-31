<?php

// shop_warehouse_selling_order.aw - Müügitellimus
/*

@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@tableinfo shop_warehouse_selling_order index=oid master_table=objects master_index=oid

@default table=objects
@default group=general

	@property date type=date_select table=shop_warehouse_selling_order
	@caption Kuup&auml;ev

	@property buyer type=relpicker reltype=RELTYPE_BUYER table=shop_warehouse_selling_order
	@caption Ostja

	@property order_nr type=textbox table=shop_warehouse_selling_order
	@caption Tellimuse nr.

	@property deadline type=date_select table=shop_warehouse_selling_order
	@caption T&auml;itmist&auml;htaeg

	@property create_selling_sheet type=checkbox ch_value=1 table=shop_warehouse_selling_order
	@caption Moodusta m&uuml;&uuml;gi-saateleht

	@property append_to_selling_sheet type=select table=shop_warehouse_selling_order
	@caption Lisa m&uuml;&uuml;gi-saatelehele

	@property confirm type=checkbox ch_value=1 table=shop_warehouse_selling_order
	@caption Kinnitatud

@groupinfo products caption="Tooted"
@default group=products

	@property products_table type=table no_caption=1
	@caption Toodete tabel

@reltype BUYER value=1 clid=CL_CRM_COMPANY
@caption Ostja

*/

class shop_warehouse_selling_order extends class_base
{
	function shop_warehouse_selling_order()
	{
		// change this to the folder under the templates folder, where this classes templates will be,
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "applications/shop/shop_warehouse_selling_order",
			"clid" => CL_SHOP_WAREHOUSE_SELLING_ORDER
		));
	}

	function parse_alias($arr = array())
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

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
		$t = $arr['prop']['vcl_inst'];
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
		if (empty($field) and "shop_warehouse_selling_order" === $table)
		{
			$this->db_query('CREATE TABLE `shop_warehouse_selling_order` (oid INT PRIMARY KEY NOT NULL)');
			return true;
		}

		switch ($field)
		{
			case 'order_nr':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'varchar(255)'
				));
				return true;
			case 'date':
			case 'buyer':
			case 'deadline':
			case 'create_selling_sheet':
			case 'append_to_selling_sheet':
			case 'confirm':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'int'
				));
				return true;
		}

		return false;
	}
}
