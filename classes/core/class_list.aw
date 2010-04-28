<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/core/class_list.aw,v 1.8 2008/01/31 13:52:49 kristo Exp $
// class_list.aw - Klasside nimekiri 
/*

@classinfo syslog_type=ST_CLASS_LIST relationmgr=yes maintainer=kristo

@default table=objects
@default group=general

*/

class class_list extends class_base
{
	const AW_CLID = 331;

	function class_list()
	{
		$this->init(array(
			"tpldir" => "core/class_list",
			"clid" => CL_CLASS_LIST
		));
	}


	/** registers new class

		@attrib name=register_new_class_id nologin="1"

		@param data required

		@comment data can contain:
			id optional
			def required
			name required
			file required
			can_add optional 
			parents optional
			alias optional
			alias_class optional
			old_alias optional
			is_remoted optional 
			subtpl_handler optional 
	**/
	function register_new_class_id($arr)
	{
		$arr = $arr["data"];
		if ($arr["id"])
		{
			$new_id = $arr["id"];
		}
		else
		{
			$new_id = $this->db_fetch_field("SELECT max(id) as id FROM aw_class_list", "id")+1;
		}
		$this->db_query("INSERT INTO aw_class_list(id) VALUES($new_id)");
		$this->update_class_def(array(
			"id" => $new_id
		) + $arr);

		return $new_id;
	}

	/** changes class parameters

		@attrib name=update_class_def

		@param data required 

		@comment data can contain:
			id required
			def required
			name required
			file required
			can_add optional 
			parents optional
			alias optional
			alias_class optional
			old_alias optional
			is_remoted optional 
			subtpl_handler optional 
		
	**/
	function update_class_def($arr)
	{
		$this->db_query("
			UPDATE 
				aw_class_list 
			SET
				def = '$arr[def]',
				name = '$arr[name]',
				file = '$arr[file]',
				can_add = '$arr[can_add]',
				parents = '$arr[parents]',
				alias = '$arr[alias]',
				alias_class = '$arr[alias_class]',
				old_alias = '$arr[old_alias]',
				is_remoted = '$arr[is_remoted]',
				subtpl_handler = '$arr[subtpl_handler]'
			WHERE
				id = '$arr[id]'
		");

		return $id;
	}

	/** shows list of classes

		@attrib name=get_list 

	**/
	function get_list($arr)
	{
		$t = get_instance("vcl/table");
		$t = new aw_table();
		$t->set_layout("generic");
		$t->define_field(array(
			"name" => "id",
			"caption" => t("id"),
			"sortable" => 1,
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "def",
			"caption" =>t("def"),
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "name",
			"caption" =>t("name"),
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "file",
			"caption" =>t("file"),
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "can_add",
			"caption" =>t("can_add"),
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "parents",
			"caption" =>t("parents"),
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "alias",
			"caption" =>t("alias"),
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "alias_class",
			"caption" =>t("alias_class"),
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "old_alias",
			"caption" =>t("old_alias"),
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "is_remoted",
			"caption" =>t("is_remoted"),
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "subtpl_handler",
			"caption" => t("subtpl_handler"),
			"sortable" => 1
		));

		$this->db_query("SELECT * FROM aw_class_list");
		while ($row = $this->db_next())
		{
			$t->define_data($row);
		}		
		
		$t->set_default_sortby("id");
		$t->sort_by();

		return $t->draw();
	}
}
?>
