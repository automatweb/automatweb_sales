<?php

namespace automatweb;
/*

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_ADD_FROM, CL_GROUP_MEMBERSHIP, on_connect_from_group_membership)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_ADD_TO, CL_GROUP_MEMBERSHIP, on_connect_to_group_membership)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_DELETE_FROM, CL_GROUP_MEMBERSHIP, on_disconnect_from_group_membership)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_DELETE_TO, CL_GROUP_MEMBERSHIP, on_disconnect_to_group_membership)

@classinfo syslog_type=ST_GROUP_MEMBERSHIP relationmgr=yes no_comment=1 prop_cb=1 maintainer=instrumental
@tableinfo aw_group_membership master_index=brother_of master_table=objects index=aw_oid

@default table=aw_group_membership
@default group=general

@property gms_user type=relpicker reltype=RELTYPE_USER store=connect
@caption Kasutaja

@property gms_group type=relpicker reltype=RELTYPE_GROUP store=connect
@caption Grupp

@property date_start type=datetime_select add_empty=no
@caption Alguskuup&auml;ev

@property date_end type=datetime_select add_empty=no
@caption L&otilde;ppkuup&auml;ev

@property membership_forever type=checkbox ch_value=1
@caption Ajaliselt piiramata

@reltype USER value=1 clid=CL_USER
@caption Kasutaja

@reltype GROUP value=2 clid=CL_GROUP
@caption Grupp

*/

class group_membership extends class_base
{
	const AW_CLID = 1481;

	function group_membership()
	{
		$this->init(array(
			"tpldir" => "core/users/group_membership",
			"clid" => CL_GROUP_MEMBERSHIP
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "date_end":
				if($arr["new"])
				{
					$prop["value"] = time() + 30*24*3600;
				}
			case "date_start":
				$prop["year_from"] = min(date("Y"), date("Y", $prop["value"]));
				$prop["year_to"] = max(date("Y") + 10, date("Y", $prop["value"]));
				if($arr["obj_inst"]->membership_forever)
				{
					return PROP_IGNORE;
				}
				break;

			case "membership_forever":
				$prop["onclick"] = 'if(this.checked){$("#date_start").parent().parent().parent().hide();$("#date_end").parent().parent().parent().hide();}else{$("#date_start").parent().parent().parent().show();$("#date_end").parent().parent().parent().show();}';
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
			case "date_start":
			case "date_end":
				if($arr["request"]["membership_forever"])
				{
					return PROP_IGNORE;
				}
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

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_group_membership(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "membership_forever":
			case "date_start":
			case "date_end":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}

	function on_connect_from_group_membership($arr)
	{
		return get_instance("group_membership_obj")->on_connect_from_group_membership($arr);
	}

	function on_connect_to_group_membership($arr)
	{
		return get_instance("group_membership_obj")->on_connect_to_group_membership($arr);
	}

	function on_disconnect_to_group_membership($arr)
	{
		return get_instance("group_membership_obj")->on_disconnect_to_group_membership($arr);
	}

	function on_disconnect_from_group_membership($arr)
	{
		return get_instance("group_membership_obj")->on_disconnect_from_group_membership($arr);
	}

	/**
	@attrib name=brother_destroyer api=1 params=name

	@param id required type=oid acl=view
		The OID of the CL_GROUP_MEMBERSHIP object.

	**/
	public function brother_destroyer($arr)
	{
		return get_instance("group_membership_obj")->brother_destroyer($arr);
	}

	/**
	@attrib name=brother_creator api=1 params=name

	@param id required type=oid acl=view
		The OID of the CL_GROUP_MEMBERSHIP object.

	**/
	public function brother_creator($arr)
	{
		return get_instance("group_membership_obj")->brother_creator($arr);
	}

	/**
	@attrib name=gms_valid api=1
	@return TRUE if the CL_GROUP_MEMBERSHIP object is valid, otherwise return FALSE.
	**/
	public function gms_valid()
	{
		return get_instance("group_membership_obj")->gms_valid();
	}
}

?>
