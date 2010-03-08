<?php
/*
@classinfo syslog_type=ST_SHOP_PRICE_LIST_CUSTOMER_DISCOUNT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=robert
@tableinfo aw_shop_price_list_customer_discount master_index=brother_of master_table=objects index=aw_oid

@default table=aw_shop_price_list_customer_discount
@default group=general

@property pricelist type=relpicker reltype=RELTYPE_PRICELIST
@caption Hinnakiri

@property crm_category type=relpicker reltype=RELTYPE_CRM_CATEGORY
@caption Kliendigrupp

@property prod_category type=relpicker reltype=RELTYPE_PROD_CATEGORY
@caption Tootegrupp

@property discount type=textbox size=5 datatype=int
@caption Allahindluse protsent

@reltype PRICELIST value=1 clid=CL_SHOP_PRICE_LIST
@caption Hinnakiri

@reltype CRM_CATEGORY value=2 clid=CL_CRM_CATEGORY
@caption Kliendigrupp

@reltype PROD_CATEGORY value=3 clid=CL_SHOP_PRODUCT_CATEGORY
@caption Tootegrupp
*/

class shop_price_list_customer_discount extends class_base
{
	function shop_price_list_customer_discount()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_price_list_customer_discount",
			"clid" => CL_SHOP_PRICE_LIST_CUSTOMER_DISCOUNT
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
		}

		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
		}

		return $retval;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
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

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_shop_price_list_customer_discount(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "pricelist":
			case "crm_category":
			case "prod_category":
			case "discount":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}
}

?>
