<?php

// timing.aw - Ajaline aktiivsus
/*
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_ADD_FROM, CL_DOCUMENT, on_tconnect_from)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_ADD_FROM, CL_MENU, on_tconnect_from)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_ADD_TO, CL_DOCUMENT, on_tconnect_to)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_ADD_TO, CL_MENU, on_tconnect_to)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_DELETE_FROM, CL_DOCUMENT, on_tdisconnect_from)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_DELETE_FROM, CL_MENU, on_tdisconnect_from)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_DELETE_TO, CL_DOCUMENT, on_tdisconnect_to)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_DELETE_TO, CL_MENU, on_tdisconnect_to)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_SAVE, CL_TIMING, init_scheduler)

@classinfo syslog_type=ST_TIMING relationmgr=yes no_status=1 maintainer=dragut

@default table=objects
@default group=general

@property activate type=datetime_select year_from=2004 year_to=2010 field=meta method=serialize
@caption Aktiveerida

@property deactivate type=datetime_select year_from=2004 year_to=2010 field=meta method=serialize
@caption Deaktiveerida

@property apply_langs type=chooser multiple=1 field=meta method=serialize
@caption Kehtib keeltele

@property archive_time type=datetime_select year_from=2004 year_to=2010 field=meta method=serialize
@caption Arhiveerimise aeg

@property archive_folder type=select field=meta method=serialize
@caption Arhiivi kaust
@comment Kaust kuhu objekt liigutatakse

@property delete_object type=checkbox ch_value=1 field=meta method=serialize
@caption Kustuta objekt
@comment Objekt kustutatakse arhiveerimise asemel


@groupinfo objects caption="Seotud objektid" submit=no

@property objects_toolbar type=toolbar no_caption=1 group=objects
@caption Objektide toolbar

@property objects type=table no_caption=1 group=objects
@caption Seotud objektid

@reltype TIMING_OBJECT value=1 clid=CL_DOCUMENT,CL_MENU
@caption Seotud objekt

*/

class timing extends class_base
{
	function timing()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/timing",
			"clid" => CL_TIMING
		));
	}

	function on_tconnect_to($arr)
	{
		$con = &$arr["connection"];
		if($con->prop("from.class_id") == CL_TIMING)
		{
			$to = $con->to();
			$to->connect(array(
				"to" => $con->prop("from"),
				"reltype" => "RELTYPE_TIMING",
			));
		}
	}

	function on_tconnect_from($arr)
	{
		$con = &$arr["connection"];
		if($con->prop("to.class_id") == CL_TIMING)
		{
			$to = $con->to();
			$to->connect(array(
				"to" => $con->prop("from"),
				"reltype" => "RELTYPE_TIMING_OBJECT",
			));
		}
	}

	function on_tdisconnect_to($arr)
	{
		$con = &$arr["connection"];
		if($con->prop("from.class_id") == CL_TIMING)
		{
			$to = $con->to();
			$to->disconnect(array(
				"from" => $con->prop("from"),
				"reltype" => "RELTYPE_TIMING",
				"errors" => false,
			));
		}
	}

	function on_tdisconnect_from($arr)
	{
		$con = &$arr["connection"];
		if($con->prop("to.class_id") == CL_TIMING)
		{
			$to = $con->to();
			$to->disconnect(array(
				"from" => $con->prop("from"),
				"reltype" => "RELTYPE_TIMING_OBJECT",
				"errors" => false,
			));
		}
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "apply_langs":
				if (!aw_ini_get("user_interface.full_content_trans"))
				{
					return PROP_IGNORE;
				}
				$this->_apply_langs($arr);
				break;

			case "activate":
				if($arr["new"])
				{
					$prop["value"] = -1;
				}
				break;
			case "deactivate":
				if($arr["new"])
				{
					$prop["value"] = -1;
				}
				break;
			case "archive_time":
				if ($arr['new'])
				{
					$prop['value'] = -1;
				}
				break;
			case "archive_folder":
				$archive_folder_ids = aw_ini_get("timing.archive_folders");
				if (empty($archive_folder_ids))
				{
					$archive_folder_ids[1] = $arr['obj_inst']->parent();
				}
//				$prop['options'][0] = "---";
				foreach ($archive_folder_ids as $archive_folder_id)
				{
					if ($this->can("add", $archive_folder_id))
					{
						$archive_folder_obj = new object($archive_folder_id);
						$prop['options'][$archive_folder_id] = $archive_folder_obj->name();
					}
				}
				break;
			case "objects_toolbar":
				$this->objects_toolbar($arr);
				break;
			case "objects":
				$this->objects_table($arr);
				break;
		};
		return $retval;
	}

	function init_scheduler($arr)
	{
		$scheduler = get_instance("scheduler");
		$atrue = true;
		$datrue = true;
		$archive_true = true;

		// well, when i use this nifty messaging system, then i don't have any $request array
		// so i have to get those times somewhere else
		// maybe i'll get them from timing object properties
		$timing_obj = new object($arr['oid']);
		$act = $timing_obj->prop("activate");
		$deact = $timing_obj->prop("deactivate");
		$archive = $timing_obj->prop("archive_time");
		$delete = $timing_obj->prop("delete_object");

		if (empty($act) || $act == -1)
		{
			$atrue = false;
		}
		if (empty($deact) || $deact == -1)
		{
			$datrue = false;
		}
		if (empty($archive) || $archive == -1)
		{
			$archive_true = false;
		}
		if($delete == 1)
		{
			$event = $this->mk_my_orb("init_action", array(
				"subaction" => "delete",
				"id" => $arr['oid'],
			));

			// a possibility to fix this archive/delete conflict is just to make
			// delete action always happen one minute later
			$scheduler->remove(array("event" => $event));
			$scheduler->add(array(
				"time" => $archive + 60,
				"event" => $event,
				"uid" => aw_global_get("uid"),
				"auth_as_local_user" => true,
			));
			// if the object is going to be deleted, then i see no reason to
			// schedule those activation/deactivation/archive tasks
			$atrue = false;
			$datrue = false;
			$archive_true = false;
		}
		if($atrue)
		{
			$event = $this->mk_my_orb("init_action", array(
				"subaction" => "activate",
				"id" => $arr['oid'],
			));
			$scheduler->remove(array("event" => $event));
			$scheduler->add(array(
				"time" => $act,
				"event" =>  $event,
				"uid" => aw_global_get("uid"),
				"auth_as_local_user" => true,
			));
		}
		if($datrue)
		{
			$event = $this->mk_my_orb("init_action", array(
				"subaction" => "deactivate",
				"id" => $arr['oid'],
			));
			$scheduler->remove(array("event" => $event));
			$scheduler->add(array(
				"time" => $deact,
				"event" => $event,
				"uid" => aw_global_get("uid"),
				"auth_as_local_user" => true,
			));
		}

		if($archive_true)
		{
			$event = $this->mk_my_orb("init_action", array(
				"subaction" => "archive",
				"id" => $arr['oid'],
			));
			$scheduler->remove(array("event" => $event));
			$scheduler->add(array(
				"time" => $archive,
				"event" => $event,
				"uid" => aw_global_get("uid"),
				"auth_as_local_user" => true,
			));
		}

		//$time, $event, $uid = "", $password = "", $rep_id = 0, $event_id = "", $sessid ="")
	}

	function objects_toolbar($arr)
	{
		$tb = &$arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "delete",
			"tooltip" => t("Eemalda valitud objektid"),
			"action" => "disconnect",
			"confirm" => t("Oled kindel, et soovid valitud objektid ajastamiselt eemaldada?"),
			"img" => "delete.gif",
		));
	}

	function objects_table($arr)
	{
		$classes = aw_ini_get("classes");
		$t = &$arr["prop"]["vcl_inst"];
		$var = array(
			"id" => t("ID"),
			"name" => t("Nimi"),
			"type" => t("Tüüp"),
		);
		foreach($var as $key => $val)
		{
			$t->define_field(array(
				"name" => $key,
				"caption" => $val,
			));
		}
		$t->define_chooser(array(
			"field" => "id",
			"name" => "sel",
		));
		$objs = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_TIMING_OBJECT",
		));
		foreach($objs as $obj)
		{
			$t->define_data(array(
				"id" => $obj->prop("to"),
				"name" => html::get_change_url($obj->prop("to"), array(), $obj->prop("to.name")),
				"type" => $classes[$obj->prop("to.class_id")]["name"],
			));
		}
	}

	/**
		@attrib name=disconnect

		@param id required type=int acl=edit
		@param group optional
		@param sel required
	**/
	function disconnect($arr)
	{
		$obj_inst = obj($arr["id"]);
		foreach(safe_array($arr["sel"]) as $key => $value)
		{
			$obj = obj($value);
			$obj_inst->disconnect(array(
				"from" => $value,
				"reltype" => "RELTYPE_TIMING_OBJECT",
				"errors" => false,
			));
		}
		return html::get_change_url($arr["id"], array("group" => $arr["group"]));
	}

	/**
		@attrib name=init_action

		@param id required type=int acl=view
		@param subaction required
	**/
	function init_action($arr)
	{
		$obj_inst = obj($arr["id"]);
		$objs = $obj_inst->connections_from(array(
			"type" => "RELTYPE_TIMING_OBJECT",
		));
		foreach($objs as $obz)
		{
			$obj = $obz->to();
			$oar = array($obj->id() => $obj);
			$ol = new object_list(array(
				"brother_of" => $obj->id(),
				"lang_id" => array(),
				"site_id" => array()
			));
			foreach($ol->arr() as $o)
			{
				$oar[$o->id()] = $o;
			}


			foreach($oar as $obj)
			{
				switch ($arr["subaction"])
				{
					case "delete":
						$obj->delete();
						break;
					case "activate":
						$obj->set_status(STAT_ACTIVE);
						foreach(safe_array($obj_inst->prop("apply_langs")) as $lid )
						{
							$obj->set_meta("trans_".$lid."_status", 1);
						}
						$obj->save();
						break;
					case "deactivate":
						$obj->set_status(STAT_NOTACTIVE);
						foreach(safe_array($obj_inst->prop("apply_langs")) as $lid )
						{
							$obj->set_meta("trans_".$lid."_status", 0);
						}
						$obj->save();
						break;
					case "archive":
						$obj->set_parent($obj_inst->prop("archive_folder"));
						$obj->save();
						break;
//					$obj->set_status(($arr["subaction"] == "activate" ? STAT_ACTIVE : STAT_NOTACTIVE));
				}
//				$obj->save();
			}
		}
		return "done";
	}

	function init_vcl_property($arr)
	{
		$tmp = obj();
		$tmp->set_class_id($this->clid);
		$props = $tmp->get_property_list();
		unset($props["name"]);
		unset($props["comment"]);

		$o = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_TIMING");
		if ($o)
		{
			foreach($props as $pn => $pd)
			{
				$props[$pn]["value"] = $o->prop($pn);
			}
		}

		$a = array(
			"obj_inst" => $o ? $o : $arr["obj_inst"],
			"request" => $arr["request"],
			"prop" => &$props["archive_folder"]
		);
		$this->get_property($a);

		return $props;
	}

	function process_vcl_property($arr)
	{
		$o = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_TIMING");
		if (!$o)
		{
			$o = obj();
			$o->set_parent($arr["obj_inst"]->id());
			$o->set_class_id(CL_TIMING);
			$o->set_name(sprintf(t("%s ajaline aktiivsus"), $arr["obj_inst"]->name()));
			$crea = true;
		}

		foreach($o->get_property_list() as $pn => $pd)
		{
			if ($pd["type"] == "datetime_select")
			{
				$o->set_prop($pn, date_edit::get_timestamp($arr["request"][$pn]));
			}
			else
			{
				$o->set_prop($pn, $arr["request"][$pn]);
			}
		}
		$o->set_name(sprintf(t("%s ajaline aktiivsus"), $arr["obj_inst"]->name()));
		$o->save();

		if ($crea)
		{
			$arr["obj_inst"]->connect(array(
				"to" => $o->id(),
				"type" => "RELTYPE_TIMING"
			));
		}
	}

	function _apply_langs($arr)
	{
		$arr["prop"]["options"] = array();
		$l = get_instance("languages");
		foreach($l->get_list() as $k => $v)
		{
			$arr["prop"]["options"][$k] = $v;
		}
	}
}
?>
