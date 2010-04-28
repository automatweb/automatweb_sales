<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_CONTENT_ITEM relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=instrumental
@tableinfo aw_content_item master_index=brother_of master_table=objects index=aw_oid

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_ADD_FROM, CL_CONTENT_ITEM, on_object_connect)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_DELETE_FROM, CL_CONTENT_ITEM, on_object_disconnect)

@default table=aw_content_item
@default group=general

	@property content_package type=relpicker reltype=RELTYPE_CONTENT_PACKAGE multiple=1 store=connect
	@caption Sisupakett

	@property price type=textbox size=4
	@caption Hind

	@property acls type=chooser multiple=1 store=no
	@caption &Otilde;igused

	@property acl_change type=checkbox ch_value=1
	@caption Muutmine

	@property acl_add type=checkbox ch_value=1
	@caption Lisamine

	@property acl_admin type=checkbox ch_value=1
	@caption ACLi muutmine

	@property acl_delete type=checkbox ch_value=1
	@caption Kustutamine

	@property acl_view type=checkbox ch_value=1
	@caption Vaatamine

	@property objects type=relpicker reltype=RELTYPE_CONTENT_OBJECT multiple=1 store=connect
	@caption Sisuobjektid

# @groupinfo content_objects caption=Sisuobjektid
# @default group=content_objects

#	@property content_objects_tlb type=toolbar no_caption=1 store=no

#	@property content_objects_tbl type=table no_caption=1 store=no

@reltype CONTENT_PACKAGE value=1 clid=CL_CONTENT_PACKAGE
@caption Sisupakett

@reltype CONTENT_OBJECT value=2
@caption Sisuobjekt

*/

class content_item extends class_base
{
	const AW_CLID = 1478;

	function content_item()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/content_package/content_item",
			"clid" => CL_CONTENT_ITEM
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "content_package":
				if(isset($_GET["contpack"]) && !empty($_GET["contpack"]) && (!is_array($_GET["contpack"]) || count($_GET["contpack"]) > 0))
				{
					$ol = new object_list(array(
						"class_id" => CL_CONTENT_PACKAGE,
						"oid" => $_GET["contpack"],
						"lang_id" => array(),
					));
					$prop["options"] = $ol->names();
					$prop["value"] = $ol->ids();
				}
				break;

			case "acl_change":
			case "acl_add":
			case "acl_admin":
			case "acl_delete":
			case "acl_view":
				return PROP_IGNORE;
				break;

			case "acls":
				$prop["options"] = array(
					"acl_change" => t("Muutmine"),
					"acl_add" => t("Lisamine"),
					"acl_admin" => t("ACLi muutmine"),
					"acl_delete" => t("Kustutamine"),
					"acl_view" => t("Vaatamine"),
				);
				$prop["value"]["acl_change"] = $arr["obj_inst"]->prop("acl_change");
				$prop["value"]["acl_add"] = $arr["obj_inst"]->prop("acl_add");
				$prop["value"]["acl_admin"] = $arr["obj_inst"]->prop("acl_admin");
				$prop["value"]["acl_delete"] = $arr["obj_inst"]->prop("acl_delete");
				$prop["value"]["acl_view"] = $arr["obj_inst"]->prop("acl_view");
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
			case "acl_change":
			case "acl_add":
			case "acl_admin":
			case "acl_delete":
			case "acl_view":
				return PROP_IGNORE;
				break;

			case "acls":
				$arr["obj_inst"]->set_prop("acl_change", isset($prop["value"]["acl_change"]) ? 1 : 0);
				$arr["obj_inst"]->set_prop("acl_add", isset($prop["value"]["acl_add"]) ? 1 : 0);
				$arr["obj_inst"]->set_prop("acl_admin", isset($prop["value"]["acl_admin"]) ? 1 : 0);
				$arr["obj_inst"]->set_prop("acl_delete", isset($prop["value"]["acl_delete"]) ? 1 : 0);
				$arr["obj_inst"]->set_prop("acl_view", isset($prop["value"]["acl_view"]) ? 1 : 0);
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

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_content_item(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "acl_change":
			case "acl_add":
			case "acl_admin":
			case "acl_delete":
			case "acl_view":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;

			case "price":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "double"
				));
				return true;
		}

		return false;
	}

	function on_object_connect($arr)
	{
		$conn = $arr['connection'];
		if($conn->prop("reltype") == 2 || $conn->prop("reltype") == 1)	// RELTYPE_CONTENT_OBJECT or RELTYPE_CONTENT_PACKAGE
		{
			$contitem = $conn->from();
			get_instance(CL_CONTENT_PACKAGE)->update_acl_for_usergroup(array("id" => $contitem->content_package));
		}
	}

	function on_object_disconnect($arr)
	{
		$conn = $arr['connection'];
		if($conn->prop("reltype") == 2)	// RELTYPE_CONTENT_OBJECT
		{
			$contitem = $conn->from();
			get_instance(CL_CONTENT_PACKAGE)->remove_acl_for_objects(array("id" => $contitem->content_package, "oid" => $conn->prop("to")));
			get_instance(CL_CONTENT_PACKAGE)->update_acl_for_usergroup(array("id" => $contitem->content_package));
		}
		elseif($conn->prop("reltype") == 1) // RELTYPE_CONTENT_PACKAGE
		{
			$contitem = $conn->from();
			get_instance(CL_CONTENT_PACKAGE)->remove_acl_for_objects(array("id" => $contitem->content_package, "oid" => $contitem->object));
			get_instance(CL_CONTENT_PACKAGE)->update_acl_for_usergroup(array("id" => $contitem->content_package));
		}
	}
}

?>
