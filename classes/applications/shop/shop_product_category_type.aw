<?php
/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_shop_product_category_type master_index=brother_of master_table=objects index=aw_oid

@default group=general

@property jrk type=textbox table=objects
@caption Jrk
@comment Objekti j&auuml;rjekord

@default table=aw_shop_product_category_type

@reltype CATEGORY value=1 clid=CL_SHOP_PRODUCT_CATEGORY
@caption Tootekategooria, mille tooted omavad ka seda t&uuml;&uuml;pi kategooriaid

@reltype WAREHOUSE value=2 clid=CL_SHOP_WAREHOUSE
@caption Ladu

*/

class shop_product_category_type extends class_base
{
	function shop_product_category_type()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_product_category_type",
			"clid" => CL_SHOP_PRODUCT_CATEGORY_TYPE
		));
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
			$this->db_query("CREATE TABLE aw_shop_product_category_type(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => ""
				));
				return true;
		}
	}
}
