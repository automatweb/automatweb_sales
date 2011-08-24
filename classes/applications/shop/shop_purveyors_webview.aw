<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_shop_purveyors_webview master_index=brother_of master_table=objects index=aw_oid

@default table=aw_shop_purveyors_webview
@default group=general

	@property template type=select field=aw_template
	@caption Tarnijate kuvamise kujundus

	@property categories type=relpicker multiple=1 store=connect reltype=RELTYPE_CATEGORY
	@caption Tootekategooriad
	@comment Tootekategooriad millesse toode peaks kuuluma, et teda kuvataks

	@property oc type=objpicker clid=CL_SHOP_ORDER_CENTER field=aw_oc
	@caption Tellimiskeskkond
	@comment Veebipood, mille tooteid see n&auml;itamise objekt n&auml;itab

@reltype CATEGORY value=1 clid=CL_SHOP_PRODUCT_CATEGORY
@caption Kuvatav tootekategooria

*/

class shop_purveyors_webview extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_purveyors_webview",
			"clid" => shop_purveyors_webview_obj::CLID
		));
	}

	public function _get_categories($arr)
	{
		$arr["prop"]["value"] = $arr["obj_inst"]->prop("categories");
		return PROP_OK;
	}

	public function show($arr)
	{
		$webview = obj($arr["id"], array(), shop_purveyors_webview_obj::CLID);

		$this->read_template($webview->prop("template"));

		return $this->parse();
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_shop_purveyors_webview" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_shop_purveyors_webview` (
				  `aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`aw_oid`)
				)");
				$r = true;
			}
			elseif ("aw_oc" === $field)
			{
				$this->db_add_col("aw_shop_purveyors_webview", array(
					"name" => $field,
					"type" => "int"
				));
				$r = true;
			}
			elseif ("aw_template" === $field)
			{
				$this->db_add_col("aw_shop_purveyors_webview", array(
					"name" => $field,
					"type" => "varchar(63)"
				));
				$r = true;
			}
		}

		return $r;
	}
}
