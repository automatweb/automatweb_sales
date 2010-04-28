<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/shop/shop_product_type.aw,v 1.8 2008/01/31 13:50:07 kristo Exp $
// shop_product_type.aw - Toote t&uuml;&uuml;p 
/*

@classinfo syslog_type=ST_SHOP_PRODUCT_TYPE relationmgr=yes no_status=1 maintainer=kristo

@default table=objects
@default group=general

@property sp_cfgform type=relpicker reltype=RELTYPE_SP_CFGFORM field=meta method=serialize
@caption Vorm, millega toodet sisestatakse

@property sp_object_type type=relpicker reltype=RELTYPE_SP_OBJECT_TYPE field=meta method=serialize
@caption Objekti t&uuml;&uuml;p, millega toodet sisestatakse

@property packaging_cfgform type=relpicker reltype=RELTYPE_SP_CFGFORM field=meta method=serialize
@caption Vorm, millega toote pakendit sisestatakse

@property image_folder type=relpicker reltype=RELTYPE_FOLDER field=meta method=serialize
@caption Toodete piltide kataloog

@property file_folder type=relpicker reltype=RELTYPE_FOLDER field=meta method=serialize
@caption Toodete failide kataloog

@property default_product_folder type=select field=meta method=serialize
@caption Vaikimisi toodete kataloog

@reltype SP_CFGFORM value=1 clid=CL_CFGFORM
@caption seadete vorm

@reltype SP_OBJECT_TYPE value=3 clid=CL_OBJECT_TYPE
@caption objektit&uuml;&uuml;p

@reltype FOLDER value=2 clid=CL_MENU
@caption kataloog

*/

class shop_product_type extends class_base
{
	const AW_CLID = 300;

	function shop_product_type()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_product_type",
			"clid" => CL_SHOP_PRODUCT_TYPE
		));
	}

	function get_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "default_product_folder":
				if($arr["new"])
				{
					$options = $this->_get_sub_folders(obj($arr["request"]["parent"]));
				}
				else
				{
					$options = $this->_get_sub_folders(obj($arr["obj_inst"]->parent()));
				}
				foreach($options as $opt)
				{
					$data["options"][$opt->id()] = $opt->name(); 
				}
				break;
		};
		return $retval;
	}

	function _get_sub_folders($obj)
	{
		$parents = array();
		$ol = new object_list(array(
			"lang_id" => array(),
			"parent" => $obj->id(),
			"class_id" => CL_MENU,
		));
		$parents[] = $obj;
		foreach($ol->arr() as $folder)
		{
			$parents = array_merge($parents,$this->_get_sub_folders($folder));
		}
		return $parents;
	}

	function set_property($arr = array())
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{

		}
		return $retval;
	}	
}
?>
