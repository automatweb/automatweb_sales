<?php
/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_mrp_order_print_format master_index=brother_of master_table=objects index=aw_oid

@default table=aw_mrp_order_print_format
@default group=general

	@property width type=textbox size=5 field=aw_width
	@caption Laius

	@property height type=textbox size=5 field=aw_height
	@caption K&otilde;rgus

	@property per_sqm type=textbox size=5 field=aw_per_sqm
	@caption Ruutmeetrile mahub

	@layout applies_resources_lay type=vbox closeable=1 area_caption=Kehtib&nbsp;ressurssidele

		@property applies_resources_tb type=toolbar store=no no_caption=1 parent=applies_resources_lay
		@caption Kehtib resurssidele toolbar

		@property applies_resources type=table store=no no_caption=1 parent=applies_resources_lay
		@caption Kehtib resurssidele

*/

class mrp_order_print_format extends class_base
{
	function mrp_order_print_format()
	{
		$this->init(array(
			"tpldir" => "mrp/orders/mrp_order_print_format",
			"clid" => CL_MRP_ORDER_PRINT_FORMAT
		));
	}

	function callback_mod_reforb(&$arr, $request)
	{
		$arr["search_res"] = "0";
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
			$this->db_query("CREATE TABLE aw_mrp_order_print_format(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_width":
			case "aw_height":
			case "aw_per_sqm":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}

	function _get_applies_resources_tb($arr)
	{
		if (!is_oid($arr["obj_inst"]->id()))
		{
			return PROP_IGNORE;
		}
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_search_button(array(
			"pn" => "search_res",
			"clid" => array(CL_MRP_RESOURCE),
		));
		$tb->add_delete_button();
	}

	function _get_applies_resources($arr)
	{
		if (!is_oid($arr["obj_inst"]->id()))
		{
			return PROP_IGNORE;
		}
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Ressurss"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "created",
			"caption" => t("Loodud"),
			"align" => "center",
			"type" => "time",
			"format" => "d.m.Y H:i:s",
			"numeric" => 1,
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "createdby_person",
			"caption" => t("Looja"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));

		$ol = new object_list(array(
			"class_id" => CL_MRP_RESOURCE_FORMAT_APPLIES,
			"format" => $arr["obj_inst"]->id()
		));
		$u = get_instance(CL_USER);
		foreach($ol->arr() as $o)
		{
			$t->define_data(array(
				"name" => $o->resource()->name(),
				"created" => $o->created(),
				"createdby_person" => $u->get_person_for_uid($o->createdby())->name(),
				"oid" => $o->id()
			));
		}
	}

	function callback_post_save($arr)
	{
		$val = isset($arr["request"]["search_res"]) ? $arr["request"]["search_res"] : "";
		if ($val)
		{
			$ol = new object_list(array(
				"class_id" => CL_MRP_RESOURCE_FORMAT_APPLIES,
				"format" => $arr["obj_inst"]->id()
			));
			$ex = array();
			foreach($ol->arr() as $o)
			{
				$ex[$o->resource] = $o->id();
			}

			foreach(explode(",", $val) as $item)
			{
				if ($this->can("view", $item) && !isset($ex[$item]))
				{
					$t = obj();
					$t->set_parent($item);
					$t->set_class_id(CL_MRP_RESOURCE_FORMAT_APPLIES);
					$t->set_name(sprintf(t("Formaadi %s kehtivus ressursile %s"), $arr["obj_inst"]->name(), obj($item)->name()));
					$t->set_prop("format", $arr["obj_inst"]->id());
					$t->set_prop("resource", $item);
					$t->save();
				}
			}
		}
	}
}
