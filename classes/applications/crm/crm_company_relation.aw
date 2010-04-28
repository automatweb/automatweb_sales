<?php

namespace automatweb;
// crm_company_relation.aw - Organisatoorne kuuluvus
/*

@classinfo syslog_type=ST_CRM_COMPANY_RELATION relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo kliendibaas_organisatoorne_kuuluvus master_index=oid master_table=objects index=oid

@default table=objects
@default group=general

@property org type=relpicker reltype=RELTYPE_COMPANY store=connect mode=autocomplete option_is_tuple=1
@caption Organisatsioon

#@property start type=date_select year_from=1950 save_format=iso8601 field=rel_start table=kliendibaas_organisatoorne_kuuluvus
@property start type=select field=rel_start table=kliendibaas_organisatoorne_kuuluvus
@caption Algus

#@property end type=date_select year_from=1950 save_format=iso8601 field=rel_end table=kliendibaas_organisatoorne_kuuluvus
@property end type=select field=rel_end table=kliendibaas_organisatoorne_kuuluvus
@caption L&otilde;pp

@property add_info type=textarea field=comment
@caption Lisainfo

@reltype COMPANY value=1 clid=CL_CRM_COMPANY
@caption Organisatsioon

*/

class crm_company_relation extends class_base
{
	const AW_CLID = 1405;

	function crm_company_relation()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_company_relation",
			"clid" => CL_CRM_COMPANY_RELATION
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "start":
			case "end":
				$prop["options"][""] = t("--vali--");
				for($i = date("Y"); $i >= 1950; $i--)
				{
					$prop["options"][$i."-01-01"] = $i;
				}
				break;

			case "org":
				$prop["option_is_tuple"] = true;
				if(is_oid($prop["value"]) && $this->can("view", $prop["value"]))
				{
					$prop["content"] = obj($prop["value"])->name();
				}
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
			case "org":
				if(!is_oid($prop["value"]) && strlen($prop["value"]) > 0)
				{
					$ol = new object_list(array(
						"class_id" => CL_CRM_COMPANY,
						"lang_id" => array(),
						"site_id" => array(),
					));
					$rev_nms = array_flip($ol->names());
					if(array_key_exists($prop["value"], $rev_nms))
					{
						$arr["obj_inst"]->set_prop("org", $rev_nms[$prop["value"]]);
					}
					else
					{
						$new_p = new object;
						$new_p->set_class_id(CL_CRM_COMPANY);
						$new_p->set_parent($arr["obj_inst"]->parent());
						$new_p->set_name($prop["value"]);
						$new_p->save();
						$arr["obj_inst"]->set_prop("org", $new_p->id());
					}
					$retval = PROP_IGNORE;
				}
				else
				{
					$arr["obj_inst"]->set_prop($prop["name"], $prop["value"]);
					$retval = PROP_IGNORE;
				}
				break;
		}

		return $retval;
	}

	function do_db_upgrade($tbl, $field, $q, $err)
	{
		if ($tbl == "kliendibaas_organisatoorne_kuuluvus" && $field == "")
		{
			$this->db_query("create table kliendibaas_organisatoorne_kuuluvus (oid int primary key)");
			return true;
		}

		switch($field)
		{
			case "rel_end":
			case "rel_start":
				$this->db_add_col($tbl, array(
					"name" => $field,
					"type" => "date"
				));
				return true;
		}

		return false;
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
		@attrib name=org_ac all_args=1
	**/
	function org_ac($arr)
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
			"class_id" => CL_CRM_COMPANY,
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
