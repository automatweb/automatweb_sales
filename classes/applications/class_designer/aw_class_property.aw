<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_AW_CLASS_PROPERTY relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_aw_class_property master_index=brother_of master_table=objects index=aw_oid

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property c_class type=text 
@caption Klass

@property p_caption type=text 
@caption Caption

@property p_name type=text 
@caption Nimi

@property p_type type=text 
@caption T&uuml;&uuml;p

@property p_store type=text 
@caption Salvestatav

@property p_table type=text 
@caption Tabel

@property p_field type=text 
@caption Tulp

@property p_method type=text 
@caption Salvestamise meetod

@property p_reltype type=text 
@caption Seoset&uuml;&uuml;p

@property p_no_caption type=text 
@caption &Auml;ra n&auml;ita captionit

@property p_group type=text 
@caption Kaart

@property p_parent type=text 
@caption Layout

@property p_default type=text 
@caption Vaikimisi v&auml;&auml;rtus

@property customer type=relpicker reltype=RELTYPE_CUSTOMER store=connect
@caption Klient

@property project type=relpicker reltype=RELTYPE_PROJECT store=connect
@caption Projekt

@default group=groups

	@property toolbar type=toolbar store=no no_caption=1
	@property table type=table store=no no_caption=1

@groupinfo groups caption="Grupid"

@reltype PROJECT value=1 clid=CL_PROJECT
@caption Projekt

@reltype CUSTOMER value=2 clid=CL_CRM_COMPANY
@caption Klient


*/

class aw_class_property extends class_base
{
	const AW_CLID = 1506;

	function aw_class_property()
	{
		$this->init(array(
			"tpldir" => "applications/class_designer/aw_class_property",
			"clid" => CL_AW_CLASS_PROPERTY
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
			$this->db_query("CREATE TABLE aw_aw_class_property(aw_oid int primary key)");
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

	function _get_toolbar($arr)
	{	
		$arr["prop"]["vcl_inst"]->add_delete_rels_button_rel_id();
	}

	function _get_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "group",
			"caption" => t("Grupp"),
		));
		$t->define_chooser(array(
			"id" => "id",
			"name" => "sel"
		));

		$t->set_caption(t("Omaduste grupid, kuhu see omadus kuulub"));
		foreach($arr["obj_inst"]->connections_to(array("from.class_id" => CL_SM_PROP_STATS_GROUP)) as $c)
		{
			$t->define_data(array(
				"id" => $c->id(),
				"group" => $c->prop("from.name")
			));
		}
	}
}

?>
