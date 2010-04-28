<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/shop/shop_warehouse_purchase_bill.aw,v 1.2 2008/01/31 13:50:07 kristo Exp $
// shop_warehouse_purchase_bill.aw - Ostu arve 
/*

@classinfo syslog_type=ST_SHOP_WAREHOUSE_PURCHASE_BILL relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@tableinfo shop_warehouse_purchase_bill index=oid master_table=objects master_index=oid

@default table=objects
@default group=general

@property confirmed type=checkbox ch_value=1 table=shop_warehouse_purchase_bill
@caption Kinnitatud

*/

class shop_warehouse_purchase_bill extends class_base
{
	const AW_CLID = 1038;

	function shop_warehouse_purchase_bill()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "applications/shop/shop_warehouse_purchase_bill",
			"clid" => CL_SHOP_WAREHOUSE_PURCHASE_BILL
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
			case 'confirmed':
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
