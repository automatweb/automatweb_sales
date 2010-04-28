<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_EXTERNAL_SYSTEM relationmgr=yes no_comment=1 prop_cb=1 maintainer=voldemar

@tableinfo aw_ext_systems index=aw_oid master_table=objects master_index=brother_of

@default table=aw_ext_systems
@default group=general

	@property ord type=textbox size=5 table=objects field=jrk
	@caption J&auml;rjekord

	@property apply_class type=select field=aw_apply_class
	@caption Klass, millele kehtib

	@property systems type=relpicker field=meta table=objects method=serialize multiple=1 reltype=RELTYPE_SYSTEM
	@caption Kuvatavad siduss&uuml;steemid

@default group=data

	@property data_table type=table no_caption=1 store=no

@groupinfo data caption="Andmed"

@reltype SYSTEM value=1 clid=CL_EXTERNAL_SYSTEM
@caption Siduss&uuml;steem

*/

class external_system extends class_base
{
	const AW_CLID = 1073;

	function external_system()
	{
		$this->init(array(
			"tpldir" => "common/external/external_system",
			"clid" => CL_EXTERNAL_SYSTEM
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "apply_class":
				$prop["options"] = get_class_picker();
				break;
		};
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

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_ext_systems (aw_oid int primary key, aw_apply_class int)");
			return true;
		}
	}

	private function _get_ext_sys_arr($arr)
	{
		$ss = $arr["obj_inst"]->prop("systems");
		if(count($ss))
		{
			$params["oid"] = $ss;
		}
		else
		{
			$params["oid"] = -1;
		}
		$params["class_id"] = CL_EXTERNAL_SYSTEM;
		$params["lang_id"] = array();
		$params["site_id"] = array();
		$ol = new object_list($params);
		$arr = $ol->arr();
		return $arr;
	}

	private function _init_data_table(&$t, $arr)
	{
		$t->define_field(array(
			"name" => "co",
			"caption" => t("Organisatsioon"),
			"align" => "center",
		));
		$exts = $this->_get_ext_sys_arr($arr);
		foreach($exts as $o)
		{
			$t->define_field(array(
				"name" => $o->id(),
				"caption" => $o->name(),
				"align" => "center",
			));
		}
		$t->define_pageselector(array(
			"type" => "text",
			"records_per_page" => 200,
			"position" => "both",
		));
	}

	function _get_data_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_data_table($t, $arr);

		$ext_sys_arr = $this->_get_ext_sys_arr($arr);

		$ol = new object_list(array(
			"class_id" => $arr["obj_inst"]->prop("apply_class"),
			"lang_id" => array(),
			"site_id" => array()
		));

		$extents = new object_list(array(
			"class_id" => CL_EXTERNAL_SYSTEM_ENTRY,
			"obj" => $ol->ids(),
			"lang_id" => array(),
			"site_id" => array()
		));
		$obj2data = array();
		
		foreach($extents->arr() as $ext_ent)
		{
			$obj2data[$ext_ent->prop("ext_sys_id")][$ext_ent->prop("obj")] = $ext_ent->prop("value");
		}

		foreach($ol->names() as $oid => $name)
		{
			$d = array(
				"co" => html::href(array(
					"caption" => $name,
					"url" => $this->mk_my_orb("change", array("id" => $oid, "action" => "change", "return_url" => get_ru()), $arr["obj_inst"]->prop("apply_class"))
				))
			);
			foreach($ext_sys_arr as $ext)
			{
				$d[$ext->id()] = html::textbox(array(
					"name" => "grid[".$ext->id()."][$oid]",
					"value" => $obj2data[$ext->id()][$oid]
				));
			}
			
			$t->define_data($d);
		}
		$t->set_sortable(false);
	}

	function _set_data_table($arr)
	{
		$ol = new object_list(array(
			"class_id" => $arr["obj_inst"]->prop("apply_class"),
			"lang_id" => array(),
			"site_id" => array()
		));

		$ext_sys = new object_list(array(
			"class_id" => CL_EXTERNAL_SYSTEM,
			"lang_id" => array(),
			"site_id" => array()
		));
		$ext_sys_arr = $ext_sys->arr();

		$extents = new object_list(array(
			"class_id" => CL_EXTERNAL_SYSTEM_ENTRY,
			"obj" => $ol->ids(),
			"lang_id" => array(),
			"site_id" => array()
		));
		$obj2data = array();
		
		foreach($extents->arr() as $ext_ent)
		{
			$obj2data[$ext_ent->prop("ext_sys_id")][$ext_ent->prop("obj")] = $ext_ent->id();
		}

		// now, loop over all the objects for all the systems and check the entries for them 
		foreach($ol->arr() as $obj)
		{
			foreach($ext_sys_arr as $ext_sys)
			{
				$entry = $obj2data[$ext_sys->id()][$obj->id()];
				if (!$this->can("view", $entry))
				{
					$entry = obj();
					$entry->set_class_id(CL_EXTERNAL_SYSTEM_ENTRY);
					$entry->set_parent($obj->id());
					$entry->set_name(sprintf(t("Siduss&uuml;steemi %s sisestus objektile %s"), $ext_sys->name(), $obj->name()));
					$entry->set_prop("ext_sys_id", $ext_sys->id());
					$entry->set_prop("obj", $obj->id());
				}
				else
				{
					$entry = obj($entry);
				}

				if (($val = $arr["request"]["grid"][$ext_sys->id()][$obj->id()]) != $entry->prop("value"))
				{
					$new = !is_oid($entry->id());
					if (!($val == "" && $new))
					{
						$entry->set_prop("value", $val);
						$entry->save();

						if ($new)
						{
							$cc = get_instance(CL_CRM_COMPANY);
							$crel = $cc->get_cust_rel($obj, true);
							$crel->connect(array(
								"to" => $entry->id(),
								"type" => "RELTYPE_EXT_SYS_ENTRY"
							));
						}
					}
				}
			}
		}
	}
}
?>