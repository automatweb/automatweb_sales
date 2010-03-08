<?php
/*
@classinfo syslog_type=ST_MRP_ORDER_COVER relationmgr=yes no_comment=1 prop_cb=1 maintainer=kristo
@tableinfo aw_mrp_order_cover master_index=brother_of master_table=objects index=aw_oid

@default table=aw_mrp_order_cover
@default group=general

	@property cover_type type=select field=aw_cover_type
	@caption Katte t&uuml;&uuml;p

	@property cover_amt type=textbox size=10 field=aw_cover_amt
	@caption Katte summa v&otilde;i protsent

@default group=applies

		@property belongs_group type=relpicker reltype=RELTYPE_APPLIES_GROUP field=aw_group
		@caption Kuulub gruppi

	@layout applies_all_lay type=vbox closeable=1 area_caption=Kehtib&nbsp;k&otilde;ikidele

		@property applies_all type=checkbox ch_value=1 default=1 field=aw_applies_all 
		@caption Kehtib kogusummale

	@layout applies_resources_lay type=vbox closeable=1 area_caption=Kehtib&nbsp;ressurssidele

		@property applies_resources_tb type=toolbar store=no no_caption=1 parent=applies_resources_lay
		@caption Kehtib resurssidele toolbar

		@property applies_resources type=table store=no no_caption=1 parent=applies_resources_lay
		@caption Kehtib resurssidele

	@layout applies_materials_lay type=vbox closeable=1 area_caption=Kehtib&nbsp;materjalidele

		@property applies_materials_tb type=toolbar store=no no_caption=1 parent=applies_materials_lay
		@caption Kehtib materjalidele toolbar

		@property applies_materials type=table store=no no_caption=1 parent=applies_materials_lay
		@caption Kehtib materjalidele

@groupinfo applies caption="Kehtimine"

@reltype APPLIES_RESOURCE value=1 clid=CL_MRP_RESOURCE
@caption Kehtib ressursile

@reltype APPLIES_PROD value=2 clid=CL_SHOP_PRODUCT
@caption Kehtib tootele

@reltype APPLIES_GROUP value=3 clid=CL_MRP_ORDER_COVER_GROUP
@caption Asub grupis

*/

class mrp_order_cover extends class_base
{
	function mrp_order_cover()
	{
		$this->init(array(
			"tpldir" => "mrp/orders/mrp_order_cover",
			"clid" => CL_MRP_ORDER_COVER
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
		$arr["search_res"] = "0";
		$arr["search_mat"] = "0";
		if (isset($_GET["apply"]))
		{
			$arr["apply"] = $_GET["apply"];
		}
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
			$this->db_query("CREATE TABLE aw_mrp_order_cover(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_cover_amt":
			case "aw_cover_tot_price_pct":
			case "aw_cover_amt_piece":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "double"
				));
				return true;

			case "aw_applies_all":
			case "aw_cover_type":
			case "aw_group":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}

	function _get_applies_resources_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_search_button(array(
			"pn" => "search_res",
			"clid" => array(CL_MRP_RESOURCE),
		));
		$tb->add_delete_rels_button();
	}

	function _get_applies_materials_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_search_button(array(
			"pn" => "search_mat",
			"clid" => array(CL_SHOP_PRODUCT),
		));
		$tb->add_delete_rels_button();
	}

	function _get_applies_materials($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->table_from_ol(
			new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_APPLIES_PROD"))),
			array("name", "created", "createdby_person"),
			CL_SHOP_PRODUCT
		);
	}

	function _get_applies_resources($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->table_from_ol(
			new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_APPLIES_RESOURCE"))),
			array("name", "created", "createdby_person"),
			CL_MRP_RESOURCE
		);
	}

	function callback_pre_save($arr)
	{	
		if (!empty($arr["request"]["apply"]))
		{
			if (is_oid($arr["request"]["apply"]))
			{
				$arr["obj_inst"]->set_prop("applies_all", 0);
			}
		}
	}

	function callback_post_save($arr)
	{
		$ps = get_instance("vcl/popup_search");
		$ps->do_create_rels($arr["obj_inst"], $arr["request"]["search_res"], "RELTYPE_APPLIES_RESOURCE");
		$ps->do_create_rels($arr["obj_inst"], $arr["request"]["search_mat"], "RELTYPE_APPLIES_PROD");

		if (!empty($arr["request"]["apply"]))
		{
			if (is_oid($arr["request"]["apply"]))
			{
				$o = obj($arr["request"]["apply"]);
				if ($o->class_id() == CL_MRP_RESOURCE)
				{
					$arr["obj_inst"]->connect(array("to" => $o->id(), 
						"type" => "RELTYPE_APPLIES_RESOURCE"));
				}
				else
				if ($o->class_id() == CL_SHOP_PRODUCT)
				{
					$arr["obj_inst"]->connect(array("to" => $o->id(),
						"type" => "RELTYPE_APPLIES_PROD"));
				}
			}
		}
	}

	function _get_cover_type($arr)
	{
		$arr["prop"]["options"] = $arr["obj_inst"]->get_cover_types();
	}
}

?>
