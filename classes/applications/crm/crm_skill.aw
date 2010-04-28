<?php

namespace automatweb;
// crm_skill.aw - Skill
/*

@classinfo syslog_type=ST_CRM_SKILL relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@tableinfo kliendibaas_oskus index=oid master_table=objects master_index=oid

@default table=objects
@default group=general

@property subheading type=checkbox ch_value=1 field=meta method=serialize
@caption Vahepealkiri
@comment Kui oskus on vahepealkiri, ei saa teda siduda isikuga ega m&auml;&auml;rata tema taset.

@property lvl type=checkbox ch_value=1 field=meta method=serialize
@caption Saab m&auml;&auml;rata taset

@property lvl_meta type=relpicker reltype=RELTYPE_LEVELS field=meta method=serialize
@caption Tasemed

@property other type=textbox field=other_capt table=kliendibaas_oskus
@caption Muu oskus caption

@property other_jrk type=textbox field=other_jrk table=kliendibaas_oskus
@caption Muu oskus jrk

@reltype LEVELS value=1 clid=CL_META
@caption Tasemed

*/

class crm_skill extends class_base
{
	const AW_CLID = 1400;

	function crm_skill()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_skill",
			"clid" => CL_CRM_SKILL
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
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	function do_db_upgrade($tbl, $field, $q, $err)
	{
		if ($tbl == "kliendibaas_oskus" && $field == "")
		{
			$this->db_query("create table kliendibaas_oskus (oid int primary key)");
			return true;
		}

		switch($field)
		{
			case "other_capt":
				$this->db_add_col($tbl, array(
					"name" => $field,
					"type" => "text"
				));
				$ol = new object_list(array(
					"class_id" => CL_CRM_SKILL,
					"parent" => array(),
					"site_id" => array(),
					"lang_id" => array(),
					"status" => array(),
				));
				foreach($ol->arr() as $o)
				{
					$value = $o->meta($field);
					$oid = $o->id();
					$this->db_query("
						INSERT INTO
							kliendibaas_oskus (oid, $field)
						VALUES
							('$oid', '$value')
						ON DUPLICATE KEY UPDATE
							$field = '$value'
					");
				}
				return true;

			case "other_jrk":
				$this->db_add_col($tbl, array(
					"name" => $field,
					"type" => "int"
				));
				$ol = new object_list(array(
					"class_id" => CL_CRM_SKILL,
					"parent" => array(),
					"site_id" => array(),
					"lang_id" => array(),
					"status" => array(),
				));
				foreach($ol->arr() as $o)
				{
					$value = $o->meta($field);
					$oid = $o->id();
					$this->db_query("
						INSERT INTO
							kliendibaas_oskus (oid, $field)
						VALUES
							('$oid', '$value')
						ON DUPLICATE KEY UPDATE
							$field = '$value'
					");
				}
				return true;
		}

		return false;
	}
}

?>
