<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_MRP_ORDER_PRINT_WEB_INTERFACE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_mrp_order_print_web_interface master_index=brother_of master_table=objects index=aw_oid

@default table=aw_mrp_order_print_web_interface
@default group=general

@property oc type=relpicker automatic=1 reltype=RELTYPE_OC field=aw_oc
@caption Tellimiskeskkond

@property disp_cfgform type=relpicker automatic=1 reltype=RELTYPE_CFGFORM field=aw_disp_cfgform
@caption Tellimuse seadete vorm

@property main_paper_folder type=relpicker reltype=RELTYPE_FOLDER field=aw_main_paper_folder
@caption Paberite valiku kaust

@property cover_paper_folder type=relpicker reltype=RELTYPE_FOLDER field=aw_cover_paper_folder
@caption Kaane paberite valiku kaust


@reltype OC value=1 clid=CL_MRP_ORDER_CENTER
@caption Tellimiskeskkond

@reltype CFGFORM value=2 clid=CL_CFGFORM
@caption Seadete vorm

@reltype FOLDER value=3 clid=CL_MENU
@caption Kaust
*/

class mrp_order_print_web_interface extends class_base
{
	const AW_CLID = 1528;

	function mrp_order_print_web_interface()
	{
		$this->init(array(
			"tpldir" => "mrp/orders/mrp_order_print_web_interface",
			"clid" => CL_MRP_ORDER_PRINT_WEB_INTERFACE
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
		// just get the attached cfgform and use that one
		$ob = new object($arr["id"]);
		
		return get_instance(CL_CFGFORM)->get_class_cfgview(array(
			"id" => $ob->disp_cfgform,
			"display_mode" => "cfg_embed",
			"submit_vars" => array(
				"web_interface_id" => $arr["id"],
				"pseh" => aw_register_ps_event_handler(
					"mrp_order_print_web_interface",
					"handle_web_add",
					array(	
						"id" => $arr["id"]
					),
					CL_MRP_ORDER_PRINT
				)
			)
		));
	}

	function handle_web_add($obj_inst, $params)
	{
		// connect to order center
		$obj_inst->workspace = obj($params["id"])->oc;
		$obj_inst->set_meta("cfgform_id", null);
		$obj_inst->save();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_mrp_order_print_web_interface(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_oc":
			case "aw_disp_cfgform":
			case "aw_main_paper_folder":
			case "aw_cover_paper_folder":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}
}

?>
