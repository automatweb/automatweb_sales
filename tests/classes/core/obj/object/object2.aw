<?php

class object_test2 extends UnitTestCase
{
	function object_test($name)
	{
		 $this->UnitTestCase($name);
	}

	function setUp()
	{
		$this->db = get_instance("class_base");

		$this->db->db_query("SELECT oid,name FROM objects WHERE status > 0 and parent > 0 and class_id > 0");
		while ($row = $this->db->db_next())
		{
			if ($this->db->can("view", $row["oid"]))
			{
				$this->obj_id = $row["oid"];
				return;
			}
		}
	}

	function _get_temp_o()
	{
		aw_disable_acl();__disable_err();
		// create new object
		$o = obj();
		$o->set_parent(aw_ini_get("site_rootmenu"));
		$o->set_class_id(CL_MENU);
		$o->save();
		aw_restore_acl();

		return $o;
	}

	function test_path_str_err_cycle()
	{
		__disable_err();
		aw_disable_acl();
		$o1 = $this->_get_temp_o();
		$o2 = $this->_get_temp_o();
		$o2->set_parent($o1->id());
		$o3 = $this->_get_temp_o();
		$o3->set_parent($o2->id());
		$o1->set_parent($o3->id());
		$o1->save();
		$o2->save();
		$o3->save();
	
		$o3->path_str();

		$o1->delete(true);
		$o2->delete(true);
		$o3->delete(true);
		aw_restore_acl();
		$this->assertTrue(__is_err());
	}

	function test_path_str_2()
	{
		aw_disable_acl();
		$o = obj(aw_ini_get("site_rootmenu"));

		$o1 = $this->_get_temp_o();
		$o1->set_name("o1");
		$o1->save();

		$o2 = $this->_get_temp_o();
		$o2->set_parent($o1->id());
		$o2->set_name("o2");
		$o2->save();

		$o3 = $this->_get_temp_o();
		$o3->set_parent($o2->id());
		$o3->set_name("o3");

		$str = $o3->path_str();

		$this->assertEqual($str, $o->name()." / o1 / o2 / o3");

		$o1->delete(true);
		$o2->delete(true);
		$o3->delete(true);
	}

	function test_path_str_max_len()
	{
		aw_disable_acl();
		$o = obj(aw_ini_get("site_rootmenu"));

		$o1 = $this->_get_temp_o();
		$o1->set_name("o1");
		$o1->save();
		$o2 = $this->_get_temp_o();
		$o2->set_parent($o1->id());
		$o2->set_name("o2");
		$o2->save();

		$o3 = $this->_get_temp_o();
		$o3->set_parent($o2->id());
		$o3->set_name("o3");
		$o3->save();


		$str = $o3->path_str(array(
			"max_len" => 2
		));
		$this->assertEqual($str, "o2 / o3");

		$o1->delete(true);
		$o2->delete(true);
		$o3->delete(true);
	}

	function test_path_str_start_at()
	{
		aw_disable_acl();
		$o = obj(aw_ini_get("site_rootmenu"));

		$o1 = $this->_get_temp_o();
		$o1->set_name("o1");
		$o1->save();

		$o2 = $this->_get_temp_o();
		$o2->set_parent($o1->id());
		$o2->set_name("o2");
		$o2->save();

		$o3 = $this->_get_temp_o();
		$o3->set_parent($o2->id());
		$o3->set_name("o3");

		$str = $o3->path_str(array(
			"start_at" => $o2->id()
		));

		$this->assertEqual($str, "o2 / o3");

		$o1->delete(true);
		$o2->delete(true);
		$o3->delete(true);
	}

	function test_path_str_path_only()
	{
		aw_disable_acl();
		$o = obj(aw_ini_get("site_rootmenu"));

		$o1 = $this->_get_temp_o();
		$o1->set_name("o1");
		$o1->save();

		$o2 = $this->_get_temp_o();
		$o2->set_parent($o1->id());
		$o2->set_name("o2");
		$o2->save();

		$o3 = $this->_get_temp_o();
		$o3->set_parent($o2->id());
		$o3->set_name("o3");

		$str = $o3->path_str(array(
			"path_only" => true
		));

		$this->assertEqual($str, $o->name()." / o1 / o2");

		$o1->delete(true);
		$o2->delete(true);
		$o3->delete(true);
	}

	function test_is_property_err_param()
	{
		__disable_err();
		$o = obj(aw_ini_get("site_rootmenu"));
		$o->is_property(5);
		$this->assertTrue(__is_err());
	}

	function test_is_property_err_clid()
	{
		__disable_err();
		$o = obj();
		$o->is_property("foo");
		$this->assertTrue(__is_err());
	}

	function test_is_property_existing()
	{
		$o = obj(aw_ini_get("site_rootmenu"));
		$this->assertTrue($o->is_property("target"));
	}

	function test_is_property_new()
	{
		$o = obj();
		$o->set_class_id(CL_MENU);
		$this->assertTrue($o->is_property("target"));
	}

	function test_can_new()
	{
		$o = obj($this->obj_id);
		$this->assertTrue($o->can("view"));
	}

	function test_parent()
	{
		$p = aw_ini_get("site_rootmenu");
		$o1 = $this->_get_temp_o();
		$o1->save();
		$this->assertEqual($p, $o1->parent());
		$o1->delete(true);
	}

	function test_set_parent()
	{
		$o1 = $this->_get_temp_o();
		$o1->set_class_id(CL_MENU);
		$site_id = 666;
		$o1->set_site_id($site_id);
		$o1->save();
		$p = $o1->id();
		$o2 = $this->_get_temp_o();
		$o2->set_parent($p);
		aw_disable_acl();
		$o2->save();
		aw_restore_acl();
		$this->assertEqual($site_id, $o2->site_id());
		$id = $o2->id();
		$this->assertEqual($p, $this->db->db_fetch_field("SELECT parent FROM objects WHERE oid = $id", "parent"));
		$o1->delete(true);
		$o2->delete(true);
	}

	function test_set_parent_err1()
	{
		$o = $this->_get_temp_o();
		__disable_err();
		$o->set_parent("abc".mt_rand());
		$this->assertTrue(__is_err());
		$o->delete(true);
	}

	function test_set_parent_err2()
	{
		$o = $this->_get_temp_o();
		__disable_err();
		$o->set_parent();
		$this->assertTrue(__is_err());
		$o->delete(true);
	}

	function test_name()
	{
		$o = $this->_get_temp_o();
		$name = $o->name() + 1;
		$o->set_name($name);
		$this->assertEqual($name, $o->name());
		$o->delete(true);
	}

	function test_set_name()
	{
		$o = $this->_get_temp_o();
		$name = $o->name() + 1;
		$o->set_name($name);
		aw_disable_acl();
		$o->save();
		aw_restore_acl();
		$id = $o->id();
		$this->assertEqual($name, $this->db->db_fetch_field("SELECT name FROM objects WHERE oid = $id", "name"));
		$o->delete(true);
	}

	function test_class_id()
	{
		$o = $this->_get_temp_o();
		$clid = CL_MENU;
		$o->set_class_id($clid);
		$this->assertEqual($clid, $o->class_id());
		$o->delete(true);
	}

	function test_set_class_id()
	{
		$o = $this->_get_temp_o();
		$id = $o->id();
		$clid = CL_MENU;
		$o->set_class_id($clid);
		aw_disable_acl();
		$o->save();
		aw_restore_acl();
		$this->assertEqual($clid, $this->db->db_fetch_field("SELECT class_id FROM objects WHERE oid = $id", "class_id"));
		$o->delete(true);
	}

	function test_set_class_id_err()
	{
		$o = $this->_get_temp_o();
		__disable_err();
		$o->set_class_id("asd".mt_rand());
		$this->assertTrue(__is_err());
		$o->delete(true);
	}

	function test_status()
	{
		$o = $this->_get_temp_o();
		$o->save();
		$this->assertEqual(1, $o->status());
		$o->delete(true);
	}

	function test_set_status()
	{
		$o = $this->_get_temp_o();
		$o->set_status(2);
		aw_disable_acl();
		$o->save();
		aw_restore_acl();
		$id = $o->id();
		$this->assertEqual(2, $this->db->db_fetch_field("SELECT status FROM objects WHERE oid = $id", "status"));
		$o->delete(true);
	}

	function test_set_status_err()
	{
		$o = $this->_get_temp_o();
		__disable_err();
		$o->set_status(mt_rand());
		$this->assertTrue(__is_err());
		$o->delete(true);
	}

	function test_lang()
	{
		$o = $this->_get_temp_o();
		$lang = "et";
		$o->set_lang($lang);
		$this->assertEqual($lang, $o->lang());
		$o->delete(true);
	}

	function test_lang_id()
	{
		$o = $this->_get_temp_o();
		$lang_id = 1;
		$o->set_lang_id($lang_id);
		$this->assertEqual($lang_id, $o->lang_id());
		$o->delete(true);
	}

	function test_set_lang_id()
	{
		$o = $this->_get_temp_o();
		$id = $o->id();
		$lang_id = 1;
		$o->set_lang_id($lang_id);
		aw_disable_acl();
		$o->save();
		aw_restore_acl();
		$this->assertEqual($lang_id, $this->db->db_fetch_field("SELECT lang_id FROM objects WHERE oid = $id", "lang_id"));
		$o->delete(true);
	}

	function test_set_lang_id_err()
	{
		$o = $this->_get_temp_o();
		__disable_err();
		$o->set_lang_id("abc");
		$this->assertTrue(__is_err());
		$o->delete(true);
	}

	function test_comment()
	{
		$o = $this->_get_temp_o();
		$comment = 1;
		$o->set_comment($comment);
		$this->assertEqual($comment, $o->comment());
		$o->delete(true);
	}

	function test_set_comment()
	{
		$o = $this->_get_temp_o();
		$comment = 1;
		$o->set_comment($comment);
		aw_disable_acl();
		$o->save();
		aw_restore_acl();
		$id = $o->id();
		$this->assertEqual($comment, $this->db->db_fetch_field("SELECT comment FROM objects WHERE oid = $id", "comment"));
		$o->delete(true);
	}

	function test_ord()
	{
		$o = $this->_get_temp_o();
		$ord = 1;
		$o->set_ord($ord);
		$this->assertEqual($ord, $o->ord());
		$o->delete(true);
	}

	function test_set_ord()
	{
		$o = $this->_get_temp_o();
		$ord = 1;
		$o->set_ord($ord);
		aw_disable_acl();
		$o->save();
		aw_restore_acl();
		$id = $o->id();
		$this->assertEqual($ord, $this->db->db_fetch_field("SELECT jrk FROM objects WHERE oid = $id", "jrk"));
		$o->delete(true);
	}

	function test_alias()
	{
		$o = $this->_get_temp_o();
		$alias = "alias";
		$o->set_alias($alias);
		$this->assertEqual($alias, $o->alias());
		$o->delete(true);
	}

	function test_set_alias()
	{
		$o = $this->_get_temp_o();
		$alias = "alias";
		$o->set_alias($alias);
		aw_disable_acl();
		$o->save();
		aw_restore_acl();
		$id = $o->id();
		$this->assertEqual($alias, $this->db->db_fetch_field("SELECT alias FROM objects WHERE oid = $id", "alias"));
		$o->delete(true);
	}

	function test_createdby()
	{
		$uid = $this->db->db_fetch_field("SELECT uid FROM users LIMIT 0,1", "uid");
		aw_switch_user(array("uid"=>$uid));
		$o = $this->_get_temp_o();
		$id = $o->id();
		$this->assertEqual($uid, $this->db->db_fetch_field("SELECT createdby FROM objects WHERE oid= $id", "createdby"));
		$o->delete(true);
	}

	function test_modifiedby()
	{
		$o = $this->_get_temp_o();
		$uid = $this->db->db_fetch_field("SELECT uid FROM users LIMIT 0,1", "uid");
		aw_switch_user(array("uid"=>$uid));
		$o->set_name(1);
		aw_disable_acl();
		$o->save();
		aw_restore_acl();
		$id = $o->id();
		$this->assertEqual($uid, $this->db->db_fetch_field("SELECT modifiedby FROM objects WHERE oid= $id", "modifiedby"));
		$o->delete(true);
	}

	function test_created()
	{
		$time = time();
		$o = $this->_get_temp_o();
		$id = $o->id();
		$this->assertTrue((abs($time - $this->db->db_fetch_field("SELECT created FROM objects WHERE oid= $id", "created"))<2));
		$o->delete(true);
	}

	function test_modified()
	{
		$o = $this->_get_temp_o();
		$o->set_name(1);
		$time = time();
		aw_disable_acl();
		$o->save();
		aw_restore_acl();
		$id = $o->id();
		$this->assertEqual($time, $this->db->db_fetch_field("SELECT modified FROM objects WHERE oid= $id", "modified"));
		$o->delete(true);
	}

	function test_period()
	{
		$o = $this->_get_temp_o();
		$period = 1;
		$o->set_period($period);
		$this->assertEqual($period, $o->period());
		$o->delete(true);
	}

	function test_set_period()
	{
		$o = $this->_get_temp_o();
		$period = 1;
		$o->set_period($period);
		aw_disable_acl();
		$o->save();
		aw_restore_acl();
		$id = $o->id();
		$this->assertEqual($period, $this->db->db_fetch_field("SELECT period FROM objects WHERE oid = $id", "period"));
		$o->delete(true);
	}

	function test_set_periodic()
	{
		$o = $this->_get_temp_o();
		$periodic = 1;
		$o->set_periodic($periodic);
		aw_disable_acl();
		$o->save();
		aw_restore_acl();
		$id = $o->id();
		$this->assertEqual($periodic, $this->db->db_fetch_field("SELECT periodic FROM objects WHERE oid = $id", "periodic"));
		$o->delete(true);
	}

	function test_site_id()
	{
		$o = $this->_get_temp_o();
		$site_id = 1;
		$o->set_site_id($site_id);
		$this->assertEqual($site_id, $o->site_id());
		$o->delete(true);
	}

	function test_set_site_id()
	{
		$o = $this->_get_temp_o();
		$site_id = 1;
		$o->set_site_id($site_id);
		aw_disable_acl();
		$o->save();
		aw_restore_acl();
		$id = $o->id();
		$this->assertEqual($site_id, $this->db->db_fetch_field("SELECT site_id FROM objects WHERE oid = $id", "site_id"));
		$o->delete(true);
	}

	function test_get_original()
	{
		$o1 = $this->_get_temp_o();
		$o2 = $o1->get_original();
		$this->assertEqual($o1->id(), $o2->id());
		$parent = $o1->parent();
		$o3 = obj($o1->create_brother($parent));
		$o4 = $o2->get_original();
		$this->assertEqual($o1->id(), $o4->id());
		$o1->delete(true);
		$o2->delete(true);
	}

	function test_create_brother()
	{
		$o1 = $this->_get_temp_o();
		__disable_err();
		$tmp = $o1->create_brother();
		$this->assertTrue(__is_err());
		$o2 = obj($o1->create_brother($o1->parent()));
		$this->assertEqual($o1->id(), $o2->id());
		$o3 = obj($o1->create_brother($o1->parent()));
		$this->assertEqual($o2->id(), $o3->id());
		$o1->delete(true);
	}

	function test_subclass()
	{
		$o = $this->_get_temp_o();
		$subclass = 1;
		$o->set_subclass($subclass);
		$this->assertEqual($subclass, $o->subclass());
		$o->delete(true);
	}

	function test_set_subclass()
	{
		$o = $this->_get_temp_o();
		$subclass = 1;
		$o->set_subclass($subclass);
		aw_disable_acl();
		$o->save();
		aw_restore_acl();
		$id = $o->id();
		$this->assertEqual($subclass, $this->db->db_fetch_field("SELECT subclass FROM objects WHERE oid = $id", "subclass"));
		$o->delete(true);
	}

	function test_flags()
	{
		$o = $this->_get_temp_o();
		define("FL_FOO", 1<<10);
		$fl |= FL_FOO;
		$o->set_flags($fl);
		$this->assertTrue(($o->flags() & FL_FOO) == FL_FOO);
		$o->delete(true);
	}

	function test_set_flags()
	{
		$o = $this->_get_temp_o();
		define("FL_FOO", 1<<10);
		$fl |= FL_FOO;
		$o->set_flags($fl);
		aw_disable_acl();
		$o->save();
		aw_restore_acl();
		$id = $o->id();
		$fl = $this->db->db_fetch_field("SELECT flags FROM objects WHERE oid = $id", "flags");
		$this->assertTrue(($fl & FL_FOO) == FL_FOO);
		$o->delete(true);
	}
	
	function test_flag()
	{
		$o = $this->_get_temp_o();
		define("FL_FOO", 1<<10);
		$o->set_flag(FL_FOO, true);
		$this->assertTrue($o->flag(FL_FOO));
		$o->delete(true);
	}

	function test_set_flag()
	{
		$o = $this->_get_temp_o();
		define("FL_FOO", 1<<10);
		$o->set_flag(FL_FOO, true);
		aw_disable_acl();
		$o->save();
		aw_restore_acl();
		$id = $o->id();
		$fl = $this->db->db_fetch_field("SELECT flags FROM objects WHERE oid = $id", "flags");
		$this->assertTrue($fl & FL_FOO);
		$o->delete(true);
	}

	function test_meta()
	{
		$o = $this->_get_temp_o();
		$o->set_meta("var", 1);
		$this->assertEqual(1, $o->meta("var"));
		$o->delete(true);
	}
	
	function test_set_meta()
	{
		$o = $this->_get_temp_o();
		$o->set_meta("var", 1);
		aw_disable_acl();
		$o->save();
		aw_restore_acl();
		$id = $o->id();
		$metadata = $this->db->db_fetch_field("SELECT metadata FROM objects WHERE oid = $id", "metadata");
		eval($metadata);
		$this->assertEqual(1, $arr["var"]);
		$o->delete(true);
	}

	function test_prop()
	{
		$o = $this->_get_temp_o();
		$name = 1;
		$o->set_prop("name", $name);
		$this->assertEqual($name, $o->prop("name"));
		$o->delete(true);
	}

	function test_set_prop()
	{
		$o = $this->_get_temp_o();
		$name = 1;
		$o->set_prop("name", $name);
		aw_disable_acl();
		$o->save();
		aw_restore_acl();
		$id = $o->id();
		$this->assertEqual($name, $this->db->db_fetch_field("SELECT name FROM objects WHERE oid = $id", "name"));
		$o->delete(true);
	}

	function test_prop_str()
	{
		$o1 = $this->_get_temp_o();
		$o1->set_class_id(CL_MENU);
		$o2 = $this->_get_temp_o();
		$o2->set_class_id(CL_MENU);
		$name = "abc";
		$o2->set_name($name);
		aw_disable_acl();
		$o2->save();
		$o1->set_prop("default_image_folder", $o2->id());
		$o1->save();
		aw_restore_acl();
		$this->assertEqual($name,$o1->prop_str("default_image_folder"));
		$o1->delete(true);
		$o2->delete(true);
	}

	function test_get_property_list()
	{
		$o = $this->_get_temp_o();
		$o->set_class_id(CL_MENU);
		$props = $o->get_property_list();
		$this->assertEqual("relpicker", $props["default_image_folder"]["type"]);
		$o->delete(true);
	}

	function test_get_group_list()
	{
		$o = $this->_get_temp_o();
		$o->set_class_id(CL_MENU);
		$groups = $o->get_group_list();
		$this->assertEqual("general", $groups["advanced_settings"]["parent"]);
		$o->delete(true);
	}

	function test_get_relinfo()
	{
		$o = $this->_get_temp_o();
		$o->set_class_id(CL_MENU);
		$rels = $o->get_relinfo();
		$this->assertEqual(178, $rels[4]["clid"][0]);
		$o->delete(true);
	}
	
	function test_get_tableinfo()
	{
		$o = $this->_get_temp_o();
		$o->set_class_id(CL_MENU);
		$tables = $o->get_tableinfo();
		$this->assertEqual("id", $tables["menu"]["index"]);
		$o->delete(true);
	}

	function test_get_classinfo()
	{
		$o = $this->_get_temp_o();
		$o->set_class_id(CL_MENU);
		$class = $o->get_classinfo();
		$this->assertEqual("menu", $class["objtable"]);
		$o->delete(true);
	}
	
	function test_properties()
	{
		$o = obj();
		$o->set_class_id(CL_MENU);
		$o->set_name(1);
		$props = $o->properties();
		$this->assertEqual(1, $props["name"]);
		$o->delete(true);
	}

	function test_brother_of()
	{
		$o1 = $this->_get_temp_o();
		__disable_err();
		$tmp = $o1->create_brother();
		$this->assertTrue(__is_err());
		$o2 = obj($o1->create_brother($o1->parent()));
		$tmp = $o2->brother_of();
		$this->assertEqual($tmp, $o1->id());
		$o1->delete(true);
	}

	function test_instance()
	{
		$o = $this->_get_temp_o();
		$o->set_class_id(CL_MENU);
		$i = $o->instance();
		$this->assertEqual(get_class($i), "menu");
		$o->delete(true);
	}

	function test_instance_err()
	{
		__disable_err();
		$o = obj();
		$i = $o->instance();
		$this->assertTrue(__is_err());
	}

	function test_is_connected_to()
	{
		$o = $this->_get_temp_o();
		$rm = obj(aw_ini_get("site_rootmenu"));
	
		aw_disable_acl();
		$o->connect(array(
			"to" => $rm,
			"type" => "RELTYPE_SEEALSO"
		));

		$this->assertTrue($o->is_connected_to(array(
			"to" => $rm
		)));
		$o->delete(true);
		aw_restore_acl();
	}

	function test_acl()
	{
		$o = $this->_get_temp_o();
		$acl = array("can_view" => 1, "can_edit" => 1, "can_delete" => 1, "can_add" => 0, "can_admin" => 0, "can_subs" => 0);
		$groupid = $this->db->db_fetch_field("SELECT oid FROM groups LIMIT 0,1", "oid");
		$group = obj($groupid);
		aw_disable_acl();
		$o->acl_set($group, $acl);
		$o->save();
		$o2 = obj($o->id());
		aw_restore_acl();
		$set_data = array(
			$group->id() => $acl,
		);
		$acldata = $o2->acl_get();
		$this->assertEqual($set_data, $acldata);
		$o->delete(true);
	}

	function test_acl_del()
	{
		$o = $this->_get_temp_o();
		$acl = array("can_view" => 1, "can_edit" => 0, "can_delete" => 1);
		$groupid = $this->db->db_fetch_field("SELECT oid FROM groups LIMIT 0,1", "oid");
		$group = obj($groupid);
		$o->acl_set($group, $acl);
		aw_disable_acl();
		$o->save();
		aw_restore_acl();
		$o->acl_del($groupid);
		$this->assertEqual($o->acl_get(), array());
		$o->delete(true);
	}

	function test_xml()
	{
		$o1 = $this->_get_temp_o();
		$o1->set_name("foo");
		aw_disable_acl();
		$o1->save();
		aw_restore_acl();
		aw_global_set("charset", "iso-8859-1");
		$xml = $o1->get_xml();
		$o2 = $o1->from_xml($xml, $o1->id());
		$this->assertEqual($o1->name(), trim($o2->name()));
		$o1->delete(true);
		$o2->delete(true);
	}
}
?>
