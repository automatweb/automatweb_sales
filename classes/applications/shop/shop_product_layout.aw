<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/shop/shop_product_layout.aw,v 1.3 2008/01/31 13:50:07 kristo Exp $
// shop_product_layout.aw - Lao toote kujundus 
/*

@classinfo syslog_type=ST_SHOP_PRODUCT_LAYOUT relationmgr=yes no_status=1 maintainer=kristo

@default table=objects
@default group=general

@property template type=select field=meta method=serialize
@caption Template

*/

class shop_product_layout extends class_base
{
	function shop_product_layout()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_product_layout",
			"clid" => CL_SHOP_PRODUCT_LAYOUT
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "template":
				$tm = get_instance("templatemgr");
				$prop["options"] = $tm->template_picker(array(
					"folder" => "applications/shop/shop_product_layout"
				));
				break;
		};
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
}
?>
