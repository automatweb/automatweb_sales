<?php

namespace automatweb;

// id_config.aw - ID-Kaardi konfiguratsioon
/*

@classinfo syslog_type=ST_ID_CONFIG relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@default table=objects
@default group=general

@property id_ugroup type=relpicker multiple=1 reltype=RELTYPE_ID_USER_GROUP field=meta method=serialize
@caption Kasutajagrupid

@property use_safelist type=checkbox ch_value=1 default=0 field=meta method=serialize
@caption Kasuta kontrollnimekirja

@groupinfo safelist caption=Kontrollnimekiri

/	@property safelist_tb type=toolbar group=safelist no_caption=1
/	@caption Kontrollnimekirja haldus

	@property safelist_tbl type=table group=safelist no_caption=1
	@caption Kontrollnimekiri

@groupinfo activity caption=Aktiivsus

	@property activity type=table group=activity no_caption=1
	@caption Aktiivsus

@property safelist type=hidden no_caption=1 field=meta method=serialize
@property search_helper type=hidden store=no no_caption=1 name=search_result_persons

@reltype ID_USER_GROUP value=1 clid=CL_GROUP
@caption Kasutajagrupp
*/

define("DEFAULT_ID_CONFIG_PARENT", 2);
define("DEFAULT_ID_LOGIN_PRIORITY", 10);

class id_config extends class_base
{
	const AW_CLID = 1193;

	function id_config()
	{
		$this->init(array(
			"tpldir" => "core/users/id_config",
			"clid" => CL_ID_CONFIG
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- get_property --//
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- set_property --//
		}
		return $retval;
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function callback_pre_save($arr)
	{
		// add new entries
		$this->add_into_safelist($arr["id"], $arr["request"]["entries"]);

		// add new searched persons
		//arr($arr["request"]);
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

//-- methods --//

	function _get_activity($arr)
	{
		// this is supposed to return a list of all active polls
		// to let the user choose the active one
		$table = &$arr["prop"]["vcl_inst"];
		$table->parse_xml_def("activity_list");

		$pl = new object_list(array(
			"class_id" => CL_ID_CONFIG
		));
		for($o = $pl->begin(); !$pl->end(); $o = $pl->next())
		{
			$actcheck = checked($o->flag(OBJ_FLAG_IS_SELECTED));
			$act_html = "<input type='radio' name='active' $actcheck value='".$o->id()."'>";
			$row = $o->arr();
			$row["active"] = $act_html;
			$table->define_data($row);
		};
	}

	function _set_activity($arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_ID_CONFIG,
		));
		for ($o = $ol->begin(); !$ol->end(); $o = $ol->next())
		{
			if ($o->flag(OBJ_FLAG_IS_SELECTED) && $o->id() != $arr["request"]["active"])
			{
				$o->set_flag(OBJ_FLAG_IS_SELECTED, false);
				$o->save();
			}
			else
			if ($o->id() == $arr["request"]["active"] && !$o->flag(OBJ_FLAG_IS_SELECTED))
			{
				$o->set_flag(OBJ_FLAG_IS_SELECTED, true);
				$o->save();
			}
		}
	}

	function _get_safelist_tbl($arr)
	{
		//$this->set_safelist($arr["obj_inst"]->id(), array());
		$t = &$arr["prop"]["vcl_inst"];
		$this->_init_safelist_tbl(&$t);
		$list = array_values($this->get_safelist($arr["obj_inst"]->id()));
		for($i = count($list); $i < (count($list)+3); $i++)
		{
			$t->define_data(array(
				"id" => ($i+1),
				"pid" => html::textbox(array(
					"name" => "entries[".$i."][pid]",
					"size" => 13,
				)),
				"name" => t("-"),
				"comment" => html::textbox(array(
					"name" => "entries[".$i."][comment]",
					"size" => 35,
				)),
			));
		}
		classload("core/icons");
		foreach($list as $k => $el)
		{
			$name = t("-");
			$t->define_data(array(
				"id" => ($k+1),
				"pid" => $el["pid"],
				"name" => $name,
				"comment" => $el["comment"],
				"rem" => html::href(array(
					"url" => $this->mk_my_orb("rem_pid", array(
						"pid" => $el["pid"],
						"oid" => $arr["obj_inst"]->id(),
						"return_url" => get_ru(),
					)),
					"caption" => html::img(array(
						"border" => 0,
						"url" => aw_ini_get("baseurl")."/automatweb/images/icons/delete.gif",
					)),
				)),
			));
		}

	}

	/**
		@attrib params=name all_args=1 name=rem_pid
	**/
	function rem_pid($arr)
	{
		$sl = $this->get_safelist($arr["oid"]);
		unset($sl[$arr["pid"]]);
		$this->set_safelist($arr["oid"], $sl);
		return $arr["return_url"];
	}

	function _init_safelist_tbl($t)
	{
		$t->define_field(array(
			"name" => "id",
			"caption" => t("ID"),
			"sortable" => 1,
			"numeric" => true,
		));
		$t->define_field(array(
			"name" => "pid",
			"caption" => t("Isikukood"),
		));
		//$t->define_field(array(
		//	"name" => "name",
		//	"caption" => t("Nimi"),
		//));
		$t->define_field(array(
			"name" => "comment",
			"caption" => t("Selgitus"),
		));
		$t->define_field(array(
			"name" => "rem",
			"caption" => t("Eemalda"),
			"align" => "center",
			"width" => "20",
		));
		$t->set_default_sortby("id");
		$t->set_default_sorder("asc");
	}

	function _get_safelist_tb($arr)
	{
		$tb = &$arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "add_person",
			"tooltip" => t("Lisa isik"),
			"img" => "new.gif",
			"url" => "#",
		));
		$popup_search = new popup_search();
		$search_butt = $popup_search->get_popup_search_link(array(
			"pn" => "search_result_persons",
			"clid" => CL_CRM_PERSON,
		));
		$tb->add_cdata($search_butt);
	}

	/**
		@attrib api=1 params=pos
		@param oid required type=oid
			id_config object's oid
	**/
	function get_safelist($oid)
	{
		if(!$this->can("view", $oid))
		{
			return false;
		}
		$o = obj($oid);
		return aw_unserialize($o->prop("safelist"));
	}

	/**
		@attrib api=1 params=pos
		@param oid required type=oid
			id_config object's oid
		@param list required type=array
			safelist to be set
	**/
	function set_safelist($oid, $list)
	{
		if(!$this->can("view", $oid))
		{
			return false;
		}
		$o = obj($oid);
		$o->set_prop("safelist", aw_serialize($list, SERIALIZE_NATIVE));
		$o->save();
		return true;
	}

	/**
		@attrib api=1 params=pos
		@param oid required type=oid
			id_config object's oid
		@param list required type=array
			safelist entriees to be added
	**/
	function add_into_safelist($oid, $list)
	{
		if(!$this->can("view", $oid))
		{
			return false;
		}
		$olist = $this->get_safelist($oid);
		foreach($list as $el)
		{
			if($el["pid"])
			{
				$olist[$el["pid"]] = $el;
			}
		}
		$this->set_safelist($oid, $olist);
		return true;
	}

	/**
		@attrib api=1
		@comment
			Finds out the active id-config object. If not present, creates one.
		@returns
			ID-config object.

	**/
	function get_active()
	{
		$pl = new object_list(array(
			"class_id" => CL_ID_CONFIG,
		));
		for($o = $pl->begin(); !$pl->end(); $o = $pl->next())
		{
			if($o->flag(OBJ_FLAG_IS_SELECTED))
			{
				break;
			}
		};
		if(!$pl->count())
		{
			$gr = get_instance(CL_GROUP);
			$o = new object();
			$o->set_class_id(CL_ID_CONFIG);
			$o->set_name(t("ID-kaardi Konfiguratsioon"));
			$o->set_parent(DEFAULT_ID_CONFIG_PARENT);
			$o->save();
			$new_group = new object();
			$new_group->set_class_id(CL_GROUP);
			$new_group->set_name("ID-Kaardi kasutajad");
			$new_group->set_parent(aw_ini_get("users.root_folder"));
			$new_group->save();
			$new_group->set_prop("priority", DEFAULT_ID_LOGIN_PRIORITY);
			$new_group->save();
			$o->connect(array(
				"to" => $new_group->id(),
				"type" => "RELTYPE_ID_USER_GROUP",
			));
			$o->set_prop("id_ugroup", array(
				0 => $new_group->id(),
			));
			$o->save();
		}
		if(!$o->flag(OBJ_FLAG_IS_SELECTED))
		{
			$o->set_flag(OBJ_FLAG_IS_SELECTED, true);
			$o->save();
		}

		$gr = $o->prop("id_ugroup");
		$is = false;
		foreach($gr as $group)
		{
			if(is_oid($group) && $group != 0)
			{
				$is = true;
				break;
			}
		}
		if(!$is)
		{
			$new_group = new object();
			$new_group->set_class_id(CL_GROUP);
			$new_group->set_name("ID-Kaardi kasutajad");
			$new_group->set_parent(aw_ini_get("users.root_folder"));
			$new_group->save();
			$new_group->set_prop("priority", DEFAULT_ID_LOGIN_PRIORITY);
			$new_group->save();
			$o->connect(array(
				"to" => $new_group->id(),
				"type" => "RELTYPE_ID_USER_GROUP",
			));
			$o->set_prop("id_ugroup", array(
				0 => $new_group->id(),
			));
			$o->save();
		}

		return $o;
	}

	/**
		@attrib api=1
		@comment
			checks wheater to use safelist during id-login or not.
		@returns
			boolean true or false depending on the need..
	**/
	function use_safelist()
	{

		$o = $this->get_active();
		if($o->prop("use_safelist"))
		{
			return true;
		}
		return false;
	}

	function get_ugroups()
	{
		$a = $this->get_active();
		$ugr = $a->prop("id_ugroup");
		return $ugr;

	}
}
?>
