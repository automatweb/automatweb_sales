<?php
/*
@classinfo syslog_type=ST_MRP_RESOURCE_SETTING relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_mrp_resource_setting master_index=brother_of master_table=objects index=aw_oid

@default table=aw_mrp_resource_setting
@default group=general


	@layout applies_resources_lay type=vbox closeable=1 area_caption=Kehtib&nbsp;ressurssidele

		@property applies_resources_tb type=toolbar store=no no_caption=1 parent=applies_resources_lay
		@caption Kehtib resurssidele toolbar

		@property applies_resources type=table store=no no_caption=1 parent=applies_resources_lay
		@caption Kehtib resurssidele


@reltype APPLIES_RESOURCE value=1 clid=CL_MRP_RESOURCE
@caption Kehtib ressursile
*/

class mrp_resource_setting extends class_base
{
	function mrp_resource_setting()
	{
		$this->init(array(
			"tpldir" => "mrp/mrp_resource_setting",
			"clid" => CL_MRP_RESOURCE_SETTING
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
			$this->db_query("CREATE TABLE aw_mrp_resource_setting(aw_oid int primary key)");
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
		$tb->add_delete_rels_button();
	}

	function _get_applies_resources($arr)
	{
		if (!is_oid($arr["obj_inst"]->id()))
		{
			return PROP_IGNORE;
		}
		$t = $arr["prop"]["vcl_inst"];
		$t->table_from_ol(
			new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_APPLIES_RESOURCE"))),
			array("name", "created", "createdby_person"),
			CL_MRP_RESOURCE
		);
	}

	function callback_post_save($arr)
	{
		$ps = get_instance("vcl/popup_search");
		$ps->do_create_rels($arr["obj_inst"], $arr["request"]["search_res"], "RELTYPE_APPLIES_RESOURCE");
	}
}

?>
