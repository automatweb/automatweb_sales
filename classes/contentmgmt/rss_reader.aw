<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_RSS_READER relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_rss_reader master_index=brother_of master_table=objects index=aw_oid

@default table=aw_rss_reader
@default group=general

	@property rss_url type=textbox field=aw_rss_url
	@caption RSS url

	@property update_interval type=textbox field=aw_update_interval
	@caption Uuendamise intervall minutites

	@property max_display_items type=textbox field=aw_max_display_items
	@caption Kuvatavate kirjete arv

	@property desc_max_len type=textbox field=aw_desc_max_len size=5
	@caption Kirjelduse maksimaalne pikkus t&auml;htedes

@default group=preview

	@property preview type=text store=no no_caption=1

@groupinfo preview caption="Eelvaade"

*/

class rss_reader extends class_base
{
	const AW_CLID = 1531;

	function rss_reader()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/rss_reader",
			"clid" => CL_RSS_READER
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
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");

		$s = "";
		foreach($ob->get_rss_items($_GET["show_all"] == $ob->id()) as $item)
		{
			$this->vars(array(
				"title" => $item["title"],
				"link" => $item["link"],
				"description" => $this->_format_desc($item["description"], $ob),
				"guid" => $item["guid"],
				"pubDate" => $item["pubDate"]
			));
			$s .= $this->parse("ITEM");
		}

		$updated = $ob->get_updated_time();

		$this->vars(array(
			"ITEM" => $s,
			"oid" => $ob->id(),
			"name" => $ob->name(),
			"rss_url" => $ob->prop("rss_url"),
			"last_update" => $updated ? date("d/m/Y H:i:s" , $updated) : "",
		));
		return $this->parse();
	}

	private function _format_desc($str, $o)
	{
		if ($o->desc_max_len > 0)
		{
			$str = substr($str, 0, $o->desc_max_len)."...";
		}
		return nl2br($str);
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_rss_reader(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_rss_url":
			case "aw_update_interval":
			case "aw_max_display_items":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "varchar(255)"
				));
				return true;

			case "aw_desc_max_len":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}

	function _get_preview($arr)
	{
		$arr["prop"]["value"] = $this->show(array("id" => $arr["obj_inst"]->id()));
	}
}

?>
