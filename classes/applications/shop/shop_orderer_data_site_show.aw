<?php
/*
@classinfo syslog_type=ST_SHOP_ORDERER_DATA_SITE_SHOW relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_shop_orderer_data_site_show master_index=brother_of master_table=objects index=aw_oid

@default table=aw_shop_orderer_data_site_show
@default group=general

@property template type=select
@caption Template




@groupinfo display_properties caption="Omadused"
@default group=display_properties

	@property display_properties_table type=table store=no
	@caption N&auml;idatavate omaduste tabel

*/

class shop_orderer_data_site_show extends class_base
{
	function shop_orderer_data_site_show()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_orderer_data_site_show",
			"clid" => CL_SHOP_ORDERER_DATA_SITE_SHOW
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

	function _get_template($arr)
	{
		$tm = new templatemgr();
		$arr["prop"]["options"] = $tm->template_picker(array(
			"folder" => "applications/shop/shop_orderer_data_site_show",//$this->site_template_dir
		));
		if(!(sizeof($arr["prop"]["options"]) > 1))
		{
			$arr["prop"]["caption"] .= "\n(".$this->site_template_dir.")";
		}
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
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	/**
		@attrib name=parse_alias is_public="1" caption="Change" nologin=1
	**/
	function parse_alias($arr = array())
	{
		$show_params = array(
			"id" => $arr["alias"]["target"],
		);
		$target = obj($arr["alias"]["target"]);
		if($target->prop("template"))
		{
			$show_params["template"] = $target->prop("template");
		}
		return $this->show($show_params);
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_shop_orderer_data_site_show(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "template":
			case "state":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "VARCHAR(64)"
				));
				return true;
		}
	}
}
