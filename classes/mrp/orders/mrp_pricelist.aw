<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_MRP_PRICELIST relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_mrp_pricelist master_index=brother_of master_table=objects index=aw_oid

@default table=aw_mrp_pricelist
@default group=general

	@property act_from type=date_select field=aw_act_from
	@caption Kehtib alates

	@property act_to type=date_select field=aw_act_to
	@caption Kehtib kuni

@default group=res_prices_amt

	@layout main_split type=hbox width=20%:80%

		@layout left_tree type=vbox closeable=1 area_caption=Resursside&nbsp;kaustad parent=main_split

			@property res_folder_tree type=treeview store=no no_caption=1 parent=left_tree 

		@property res_prices type=table no_caption=1 store=no parent=main_split

@default group=res_prices_hr

	@layout main_split_hr type=hbox width=20%:80%

		@layout left_tree_hr type=vbox closeable=1 area_caption=Resursside&nbsp;kaustad parent=main_split_hr

			@property res_folder_tree_hr type=treeview store=no no_caption=1 parent=left_tree_hr

		@property res_prices_hr type=table no_caption=1 store=no parent=main_split_hr

@groupinfo res_prices caption="Ressursside hinnad" 
	@groupinfo res_prices_amt parent=res_prices caption="T&uuml;kkide hinnad" 
	@groupinfo res_prices_hr parent=res_prices caption="Tundide hinnad" 
*/

class mrp_pricelist extends class_base
{
	const AW_CLID = 1521;

	function mrp_pricelist()
	{
		$this->init(array(
			"tpldir" => "mrp/orders/mrp_pricelist",
			"clid" => CL_MRP_PRICELIST
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

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
		$arr["res_fld"] = $_GET["res_fld"];
	}

	function callback_mod_retval($arr)
	{
		$arr['args']['res_fld'] = $arr['request']['res_fld'];
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
			$this->db_query("CREATE TABLE aw_mrp_pricelist(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_act_from":
			case "aw_act_to":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}

	private function _init_res_prices_table($t)
	{
		$t->define_field(array(
			"name" => "cnts",
			"caption" => t("Kogused"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "cnt_from",
			"caption" => t("Alates"),
			"align" => "center",
			"parent" => "cnts"
		));
		$t->define_field(array(
			"name" => "cnt_to",
			"caption" => t("Kuni"),
			"align" => "center",
			"parent" => "cnts"
		));

		$t->define_field(array(
			"name" => "prices",
			"caption" => t("Hinnad"),
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "config",
			"caption" => t("Seadistamine"),
			"align" => "center",
			"parent" => "prices",
		));
		$t->define_field(array(
			"name" => "item_price",
			"caption" => t("Tk hind"),
			"align" => "center",
			"parent" => "prices"
		));

		$t->set_rgroupby(array("res" => "res"));
	}

	private function _init_res_prices_table_hr($t)
	{
		$t->define_field(array(
			"name" => "cnts",
			"caption" => t("Kogused"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "cnt_from",
			"caption" => t("Alates"),
			"align" => "center",
			"parent" => "cnts"
		));
		$t->define_field(array(
			"name" => "cnt_to",
			"caption" => t("Kuni"),
			"align" => "center",
			"parent" => "cnts"
		));

		$t->define_field(array(
			"name" => "prices",
			"caption" => t("Hinnad"),
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "config",
			"caption" => t("P&uuml;sikulu"),
			"align" => "center",
			"parent" => "prices",
		));
		$t->define_field(array(
			"name" => "item_price",
			"caption" => t("Tunnihind"),
			"align" => "center",
			"parent" => "prices"
		));

		$t->set_rgroupby(array("res" => "res"));
	}

	function _get_res_prices($arr)
	{	
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_res_prices_table($t);

		if (!$arr["request"]["res_fld"])
		{
			$arr["request"]["res_fld"] = $this->_get_default_resource_parent($arr["obj_inst"]);
			if (!$arr["request"]["res_fld"])
			{
				return;
			}
		}

		foreach($arr["obj_inst"]->get_resource_list($arr["request"]["res_fld"]) as $res)
		{
			foreach($arr["obj_inst"]->get_ranges_for_resource($res) as $range)
			{
				$t->define_data(array(
					"item_price" => html::textbox(array("name" => "t[".$res->id."][".$range->id."][item_price]", "size" => 8, "value" => $range->item_price)),
					"config" => html::textbox(array("name" => "t[".$res->id."][".$range->id."][config_price]", "size" => 8, "value" => $range->config_price)),
					"cnt_to" => html::textbox(array("name" => "t[".$res->id."][".$range->id."][cnt_to]", "size" => 5, "value" => $range->cnt_to)),
					"cnt_from" => html::textbox(array("name" => "t[".$res->id."][".$range->id."][cnt_from]", "size" => 5, "value" => $range->cnt_from)),
					"res" => html::strong($res->name()),
					"sfld" => $res->id()."0"
				));
			}
			$t->define_data(array(
				"item_price" => html::textbox(array("name" => "t[".$res->id."][-1][item_price]", "size" => 8)),
				"config" => html::textbox(array("name" => "t[".$res->id."][-1][config_price]", "size" => 8)),
				"cnt_to" => html::textbox(array("name" => "t[".$res->id."][-1][cnt_to]", "size" => 5)),
				"cnt_from" => html::textbox(array("name" => "t[".$res->id."][-1][cnt_from]", "size" => 5)),
				"res" => html::strong($res->name()),
				"sfld" => $res->id()."1"
			));
		}

		$t->set_caption(t("Ressursside hinnad"));
		$t->set_default_sortby("sfld");
	}

	function _set_res_prices($arr)
	{
		if (!$arr["request"]["res_fld"])
		{
			$arr["request"]["res_fld"] = $this->_get_default_resource_parent($arr["obj_inst"]);
			if (!$arr["request"]["res_fld"])
			{
				return;
			}
		}

		foreach($arr["obj_inst"]->get_resource_list($arr["request"]["res_fld"]) as $res)
		{
			$arr["obj_inst"]->set_ranges_for_resource($res, $arr["request"]["t"][$res->id()]);
		}
	}

	function _get_default_resource_parent($o)
	{
		$conns = $o->connections_to(array("from.class_id" => CL_MRP_ORDER_CENTER));
		$c = reset($conns);
		if (!$c)
		{
			return null;
		}

		return $c->from()->mrp_workspace()->resources_folder;
	}

	function _get_res_folder_tree_hr($arr)
	{
		return $this->_get_res_folder_tree($arr);
	}

	function _get_res_folder_tree($arr)
	{
		$pt = $this->_get_default_resource_parent($arr["obj_inst"]);
		if (!$pt)
		{
			return array();
		}

		$arr["prop"]["vcl_inst"] = treeview::tree_from_objects(array(
			"tree_opts" => array(
				"type" => TREE_DHTML,
				"persist_state" => true,
				"tree_id" => "mplt",
			),
			"root_item" => obj($pt),
			"ot" => new object_tree(array(
				"parent" => $pt,
				"class_id" => array(CL_MENU),
				"lang_id" => array(),
				"site_id" => array()
			)),
			"var" => "res_fld",
			"icon" => icons::get_icon_url(CL_MENU)
		));

		if (!$arr["request"]["res_fld"] || $pt == $arr["request"]["res_fld"])
		{
			$arr["prop"]["vcl_inst"]->set_selected_item($pt);
		}
	}

	function _get_res_prices_hr($arr)
	{	
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_res_prices_table_hr($t);

		if (!$arr["request"]["res_fld"])
		{
			$arr["request"]["res_fld"] = $this->_get_default_resource_parent($arr["obj_inst"]);
			if (!$arr["request"]["res_fld"])
			{
				return;
			}
		}

		foreach($arr["obj_inst"]->get_resource_list($arr["request"]["res_fld"]) as $res)
		{
			foreach($arr["obj_inst"]->get_ranges_for_resource_hr($res) as $range)
			{
				$t->define_data(array(
					"item_price" => html::textbox(array("name" => "t[".$res->id."][".$range->id."][item_price]", "size" => 8, "value" => $range->item_price)),
					"config" => html::textbox(array("name" => "t[".$res->id."][".$range->id."][config_price]", "size" => 8, "value" => $range->config_price)),
					"cnt_to" => html::textbox(array("name" => "t[".$res->id."][".$range->id."][cnt_to]", "size" => 5, "value" => $range->cnt_to)),
					"cnt_from" => html::textbox(array("name" => "t[".$res->id."][".$range->id."][cnt_from]", "size" => 5, "value" => $range->cnt_from)),
					"res" => html::strong($res->name()),
					"sfld" => $res->id()."0"
				));
			}
			$t->define_data(array(
				"item_price" => html::textbox(array("name" => "t[".$res->id."][-1][item_price]", "size" => 8)),
				"config" => html::textbox(array("name" => "t[".$res->id."][-1][config_price]", "size" => 8)),
				"cnt_to" => html::textbox(array("name" => "t[".$res->id."][-1][cnt_to]", "size" => 5)),
				"cnt_from" => html::textbox(array("name" => "t[".$res->id."][-1][cnt_from]", "size" => 5)),
				"res" => html::strong($res->name()),
				"sfld" => $res->id()."1"
			));
		}

		$t->set_caption(t("Ressursside tunnihinnad"));
		$t->set_default_sortby("sfld");
	}

	function _set_res_prices_hr($arr)
	{
		if (!$arr["request"]["res_fld"])
		{
			$arr["request"]["res_fld"] = $this->_get_default_resource_parent($arr["obj_inst"]);
			if (!$arr["request"]["res_fld"])
			{
				return;
			}
		}

		foreach($arr["obj_inst"]->get_resource_list($arr["request"]["res_fld"]) as $res)
		{
			$arr["obj_inst"]->set_ranges_for_resource_hr($res, $arr["request"]["t"][$res->id()]);
		}
	}
}

?>
