<?php

namespace automatweb;
// crm_family_relation.aw - Sugulusside
/*

@classinfo syslog_type=ST_CRM_FAMILY_RELATION relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo kliendibaas_sugulusside index=oid master_table=objects master_index=oid

@default table=kliendibaas_sugulusside
@default group=general

@property person type=relpicker reltype=RELTYPE_PERSON store=connect
@caption Isik

@property relation_type type=select
@caption Sugulussideme t&uuml;&uuml;p

@property start type=date_select field=fr_start
@caption Algus

@property end type=date_select field=fr_end
@caption L&otilde;pp

@reltype PERSON value=1 clid=CL_CRM_PERSON
@caption Isik

*/

class crm_family_relation extends class_base
{
	const AW_CLID = 1410;

	function crm_family_relation()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_family_relation",
			"clid" => CL_CRM_FAMILY_RELATION
		));
		$this->relation_type_options = array(
			0 => t("Abikaasa"),
			1 => t("Laps"),
			2 => t("Vanem"),
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "person":
				if(!$prop["value"])
				{
					$prop["post_append_text"] = "";
					$prop["type"] = "textbox";
					$prop["autocomplete_source"] = $this->mk_my_orb("person_ac");
					$prop["autocomplete_params"] = array();
				}
				break;

			case "relation_type":
				$prop["options"] = $this->relation_type_options;
				break;
		}

		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "person":
				if(!is_oid($prop["value"]) && strlen($prop["value"]) > 0)
				{
					$ol = new object_list(array(
						"class_id" => CL_CRM_PERSON,
						"lang_id" => array(),
						"site_id" => array(),
					));
					$rev_nms = array_flip($ol->names());
					if(array_key_exists($prop["value"], $rev_nms))
					{
						$arr["obj_inst"]->set_prop("person", $rev_nms[$prop["value"]]);
					}
					else
					{
						$new_p = new object;
						$new_p->set_class_id(CL_CRM_PERSON);
						$new_p->set_parent($arr["obj_inst"]->parent());
						$new_p->set_name($prop["value"]);
						$new_p->save();
						$arr["obj_inst"]->set_prop("person", $new_p->id());
					}
					return PROP_IGNORE;
				}
				break;
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
		if ($tbl == "kliendibaas_sugulusside" && $field == "")
		{
			$this->db_query("create table kliendibaas_sugulusside (oid int primary key)");
			return true;
		}

		switch($field)
		{
			case "relation_type":
				$this->db_add_col($tbl, array(
					"name" => $field,
					"type" => "int"
				));
				return true;
			
			case "fr_start":
			case "fr_end":
				$this->db_add_col($tbl, array(
					"name" => $field,
					"type" => "varchar(20)"
				));
				return true;
		}
		return false;
	}
	
	/**
		@attrib name=person_ac all_args=1
	**/
	function person_ac($arr)
	{
		header ("Content-Type: text/html; charset=" . aw_global_get("charset"));
		$cl_json = get_instance("protocols/data/json");

		$errorstring = "";
		$error = false;
		$autocomplete_options = array();

		$option_data = array(
			"error" => &$error,// recommended
			"errorstring" => &$errorstring,// optional
			"options" => &$autocomplete_options,// required
			"limited" => false,// whether option count limiting applied or not. applicable only for real time autocomplete.
		);
		
		$ol = new object_list(array(
			"class_id" => CL_CRM_PERSON,
			"lang_id" => array(),
			"site_id" => array(),
			"limit" => 500,
		));
		$autocomplete_options = $ol->names();
		foreach($autocomplete_options as $k => $v)
		{
			$autocomplete_options[$k] = iconv(aw_global_get("charset"), "UTF-8", parse_obj_name($v));
		}

		$autocomplete_options = array_unique($autocomplete_options);
		header("Content-type: text/html; charset=utf-8");
		exit ($cl_json->encode($option_data));
	}
}

?>
