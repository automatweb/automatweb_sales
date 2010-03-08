<?php
/*
@classinfo syslog_type=ST_SHOP_PRODUCT_SINGLE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=robert
@tableinfo aw_shop_product_single master_index=brother_of master_table=objects index=aw_oid

@default table=aw_shop_product_single
@default group=general

@property product type=relpicker reltype=RELTYPE_PRODUCT
@caption Artikkel

@property code type=textbox
@caption Kood

@property type type=chooser
@caption T&uuml;&uuml;p

@reltype PRODUCT clid=CL_SHOP_PRODUCT value=1
@caption Artikkel
*/

class shop_product_single extends class_base
{
	function shop_product_single()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_product_single",
			"clid" => CL_SHOP_PRODUCT_SINGLE
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "type":
				$prop["options"] = $this->get_types();
				if(!$prop["value"])
				{
					$prop["value"] = 2;
				}
				break;

			case "name":
				$retval = PROP_IGNORE;
				break;

			case "product":
				if($arr["request"]["action"] == "new" && $pid = $arr["request"]["product"])
				{
					$po = obj($pid);
					$prop["options"] = array($pid=>$po->name());
				}
				break;
		}

		return $retval;
	}

	function get_types()
	{
		$types = array(
			2 => t("&Uuml;ksiktoode"),
			1 => t("Partii"),
		);
		return $types;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "name":
				if($pid = $arr["request"]["product"])
				{
					$po = obj($pid);
					$types = $this->get_types();
					$prop["value"] = sprintf("%s %s", $po->name(), ($c = $arr["request"]["code"]) ? "(".$c.")" : $types[$arr["request"]["type"]]);
				}
				break;
		}
		return $retval;
	}

	function callback_mod_reforb($arr)
	{
		if($_GET["action"] == "new")
		{
			$arr["def_prod"] = $_GET["product"];
		}
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
			$this->db_query("CREATE TABLE aw_shop_product_single(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "product":
			case "type":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
			case "code":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "varchar(100)"
				));
				return true;
		}
	}
}

?>
