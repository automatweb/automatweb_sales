<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_REMOTE_OBJECT_INCLUDE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_remote_object_include master_index=brother_of master_table=objects index=aw_oid

@default table=aw_remote_object_include
@default group=general

	@property site type=select field=aw_site
	@caption Sait

	@property object_type type=select field=aw_object_type
	@caption Objektit&uuml;&uuml;p


@default group=search

	@property s_name type=textbox store=no
	@caption Nimi

	@property s_oid type=textbox store=no size=6
	@caption OID

	@property search type=submit
	@caption Otsi

	@property result_table type=table store=no no_caption=1
	@caption Otsingu tulemused

	@property sel_s_res type=submit
	@caption Vali

	@property sel_object type=text field=aw_sel_object
	@caption Valitud objekt

@default group=preview

	@property preview type=text store=no no_caption=1

@groupinfo search caption="Otsi" submit_method=get
@groupinfo preview caption="Eelvaade" submit=no


*/

class remote_object_include extends class_base
{
	const AW_CLID = 1466;

	function remote_object_include()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/remote_object_include",
			"clid" => CL_REMOTE_OBJECT_INCLUDE
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
		$o = obj($arr["id"]);
		if (is_oid($o->sel_object))
		{
			return $this->do_orb_method_call(array(
				"server" => get_instance("install/site_list")->get_url_for_site($o->site),
				"method" => "xmlrpc",
				"action" => "show",
				"params" => array("id" => $o->sel_object),
				"class" => "objects"
			));
		}
		return "";
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_remote_object_include(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_site":
			case "aw_object_type":
			case "aw_sel_object":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}

	function _get_s_name($arr)
	{	
		$arr["prop"]["value"] = $arr["request"]["s_name"];
	}

	function _get_s_oid($arr)
	{	
		$arr["prop"]["value"] = $arr["request"]["s_oid"];
	}

	function _get_result_table($arr)
	{
		if (!($arr["request"]["s_name"] != "" || $arr["request"]["s_oid"] != ""))
		{
			return PROP_IGNORE;
		}

		$filter = array(
			"class_id" => $arr["obj_inst"]->object_type
		);
		if ((int)$arr["request"]["s_oid"] > 0)
		{
			$filter["oid"] = (int)$arr["request"]["s_oid"];
		}	
		if (trim($arr["request"]["s_name"]) != "")
		{
			$filter["name"] = "%".trim($arr["request"]["s_name"])."%";
		}
		$rv = $this->do_orb_method_call(array(
			"server" => get_instance("install/site_list")->get_url_for_site($arr["obj_inst"]->site),
			"method" => "xmlrpc",
			"action" => "storage_query",
			"params" => $filter,
			"class" => "objects"
		));
		
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_result_table($t);
		foreach($rv as $oid => $item)
		{
			$item["oid"] = $oid;
			$item["sel"] = html::radiobutton(array(
				"name" => "sel",
				"value" => $oid
			));
			$t->define_data($item);
		}
	}

	private function _init_result_table($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Name"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "created",
			"caption" => t("Loodud"),
			"align" => "center",
			"type" => "time",
			"format" => "d.m.Y H:i"
		));

		$t->define_field(array(
			"name" => "createdby",
			"caption" => t("Looja"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "modified",
			"caption" => t("Muudetud"),
			"align" => "center",
			"type" => "time",
			"format" => "d.m.Y H:i"
		));

		$t->define_field(array(
			"name" => "modifiedby",
			"caption" => t("Muutja"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "path_str",
			"caption" => t("Asukoht"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "sel",
			"caption" => t("Vali"),
			"align" => "center"
		));
		$t->set_caption(t("Otsingu tulemused"));
	}

	function _get_site($arr)
	{
		$arr["prop"]["options"] = array_map(create_function('$a','return $a["url"];'), get_instance("install/site_list")->get_local_list());
		asort($arr["prop"]["options"]);
	}

	function _get_object_type($arr)
	{
		$arr["prop"]["options"] = array_map(create_function('$a','return $a["name"];'), array_filter(aw_ini_get("classes"), create_function('$a','return $a["alias"] != "";')));
		asort($arr["prop"]["options"]);
	}

	function _get_sel_object($arr)
	{
		if ($arr["request"]["sel"])
		{
			$arr["prop"]["value"] = $arr["request"]["sel"];
			if ($arr["obj_inst"]->sel_object != $arr["request"]["sel"])
			{
				$arr["obj_inst"]->sel_object = $arr["request"]["sel"];
				$arr["obj_inst"]->save();
			}
		}
		else
		{
			$arr["prop"]["value"] = $arr["obj_inst"]->prop($arr["prop"]["name"]);
		}
	}

	function _get_preview($arr)
	{
		$arr["prop"]["value"] = $this->show(array("id" => $arr["obj_inst"]->id));
	}
}

?>
