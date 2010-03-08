<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/shop/shop_item_price.aw,v 1.4 2008/06/05 11:27:27 robert Exp $
// shop_item_price.aw - Toote Hind 
/*
@tableinfo aw_shop_item_prices index=aw_oid master_table=objects master_index=brother_of
@classinfo syslog_type=ST_SHOP_ITEM_PRICE relationmgr=yes maintainer=kristo

@default table=aw_shop_item_prices
@default group=general

@property product type=relpicker reltype=RELTYPE_PRODUCT
@caption Artikkel

@property price type=textbox datatype=int
@caption Hind

@property currency type=relpicker reltype=RELTYPE_CURRENCY
@caption Valuuta

@property price_list type=relpicker reltype=RELTYPE_PRICE_LIST
@caption Hinnakiri

@property valid_from type=date_select
@caption Kehtib alates

@property valid_to type=date_select
@caption Kehtib kuni

@property warehouse type=relpicker reltype=RELTYPE_WAREHOUSE
@caption Ladu

@reltype PRODUCT value=1 clid=CL_SHOP_PRODUCT
@caption Toode

@reltype CURRENCY value=2 clid=CL_CURRENCY
@caption Valuuta

@reltype PRICE_LIST value=3 clid=CL_SHOP_PRICE_LIST
@caption Hinnakiri

@reltype WAREHOUSE value=4 clid=CL_SHOP_WAREHOUSE
@caption Ladu
*/

class shop_item_price extends class_base
{
	function shop_item_price()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "applications/shop/shop_item_price",
			"clid" => CL_SHOP_ITEM_PRICE
		));
	}

	//////
	// class_base classes usually need those, uncomment them if you want to use them

	/*
	function get_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{

		};
		return $retval;
	}
	*/

	/*
	function set_property($arr = array())
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
                {

		}
		return $retval;
	}	
	*/

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

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_shop_item_prices(aw_oid int primary key)");
			return true;
		}
		$ret = false;
		switch($f)
		{
			case "valid_from":
			case "valid_to":
			case "product":
			case "currency":
			case "warehouse":
			case "price_list":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				$ret = true;
				break;
			case "price":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "float"
				));
				$ret = true;
				break;
		}

		switch($f)
		{
			case "product":
			case "currency":
			case "warehouse":
			case "price_list":
				$this->db_query("ALTER TABLE aw_shop_item_prices ADD INDEX(".$f.")");
		}
		return $ret;
	}
}
?>
