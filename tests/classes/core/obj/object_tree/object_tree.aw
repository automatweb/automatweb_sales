<?php

class object_tree_test extends UnitTestCase
{
	function object_tree_test($name)
	{
		 $this->UnitTestCase($name);
	}
/*
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

	function _get_temp_tree()
	{
		static $tree;
		if (!$tree)
		{
			aw_disable_acl();
			$o = $this->_get_temp_o();
			$o1 = $this->_get_temp_o();
			$o1->set_parent($o->id());
			$o1->save();
			$o2 = $this->_get_temp_o();
			$o2->set_parent($o1->id());
			$o2->save();
			$o3 = $this->_get_temp_o();
			$o3->set_parent($o2->id());
			$o3->save();
			$o4 = $this->_get_temp_o();
			$o4->set_parent($o1->id());
			$o4->set_class_id(CL_FILE);
			$o4->save();
			$tree = new object_tree(array(
				"parent" => $o->id(),
			));
			aw_restore_acl();
		}
		return $tree;
	}

	function _del_tree($tree)
	{
		$ol = $tree->to_list(array("add_root" => 1));
		foreach($ol->arr() as $o)
		{
			$o->delete(true);
		}
	}

	function test_construct_err()
	{
		__disable_err();
		$tree = new object_tree("foo");
		$this->assertTrue(__is_err());
	}

	function test_construct_err2()
	{
		__disable_err();
		$tree = new object_tree(array("something" => "value"));
		$this->assertTrue(__is_err());
	}

	function test_filter()
	{
	return;
		$tree = $this->_get_temp_tree();
		$tree->filter(array(
			"class_id" => CL_FILE
		), false);
		$ids = $tree->ids();
		$this->assertEqual(count($ids), 1);
		$this->_del_tree($tree);
	}

	function test_to_list()
	{
		$tree = $this->_get_temp_tree();
		$ol = $tree->to_list(array("add_root" => 1));
		$this->assertTrue($ol instanceof object_list);
		$this->assertEqual(count($ol->ids()), 4);
		$this->_del_tree($tree);
	}

	function test_foreach_o()
	{
		$tree = $this->_get_temp_tree();
		aw_disable_acl();
		$tree->foreach_o(array(
			"func" => "set_name",
			"params" => "foo",
			"save" => true,
		));
		aw_restore_acl();
		$ol = $tree->to_list();
		foreach($ol->arr() as $o)
		{
			$this->assertEqual("foo", $o->name());
		}
		$this->_del_tree($tree);
	}

	function _new_name(&$o, $name)
	{
		$o->set_name($name);
	}

	function test_foreach_cb()
	{
		$tree = $this->_get_temp_tree();
		aw_disable_acl();
		$tree->foreach_cb(array(
			"func" => array(&$this, "_new_name"),
			"param" => "foo",
			"save" => true,
		));
		aw_restore_acl();
		$ol = $tree->to_list();
		foreach($ol->arr() as $o)
		{
			$this->assertEqual("foo", $o->name());
		}
		$this->_del_tree($tree);
	}

	
	//this test will give a fatal error unless object_tree.aw is fixed
	function test_foreach_cb_err()
	{
		$tree = $this->_get_temp_tree();
		__disable_err();
		$tree->foreach_cb(array(
			"func" => "foo",
			"save" => true,
		));
		$this->assertTrue(__is_err());
		$this->_del_tree($tree);
	}

	function test_level()
	{
		aw_disable_acl();
		$o = $this->_get_temp_o();
		$o0 = $this->_get_temp_o();
		$o0->set_parent($o->id());
		$o0->save();
		$o1 = $this->_get_temp_o();
		$o1->set_parent($o0->id());
		$o1->save();
		$o2 = $this->_get_temp_o();
		$o2->set_parent($o0->id());
		$o2->save();
		aw_restore_acl();
		$tree = new object_tree(array(
			"parent" => $o->id(),
		));
		$count = 0;
		$parent = $o0->id();
		foreach($tree->level($parent) as $o)
		{
			$this->assertEqual($o->parent(), $parent);
			$count++;
		}
		$this->assertEqual($count, 2);
		$o->delete(true);
		$o0->delete(true);
		$o1->delete(true);
		$o2->delete(true);
	}

	function test_subtree()
	{
		$o = $this->_get_temp_o();
		$o1 = $this->_get_temp_o();
		aw_disable_acl();
		$o1->set_parent($o);
		$o1->save();
		$o2 = $this->_get_temp_o();
		$o2->set_parent($o1->id());
		$o2->save();
		$o3 = $this->_get_temp_o();
		$o3->set_parent($o2->id());
		$o3->save();
		aw_restore_acl();
		$tree = new object_tree(array(
			"parent" => $o->id(),
		));
		$tree2 = $tree->subtree($o2->id());
		$ol = $tree2->to_list();
		$this->assertEqual($ol->count(), 1);
		$ob = $ol->begin();
		$this->assertEqual($ob->id(), $o3->id());
		$o->delete(true);
		$o1->delete(true);
		$o2->delete(true);
		$o3->delete(true);
	}

	function test_add()
	{
		$o = $this->_get_temp_o();
		$o1 = $this->_get_temp_o();
		aw_disable_acl();
		$o1->set_parent($o->id());
		$o1->save();
		$o2 = $this->_get_temp_o();
		$o2->set_class_id(CL_FILE);
		$o2->set_parent($o->id());
		$o2->save();
		aw_restore_acl();
		$tree = new object_tree(array(
			"parent" => $o->id(),
			"class_id" => CL_MENU,
		));
		$num = $tree->add($o2);
		$this->assertEqual($num, 1);
		$ids = $tree->ids();
		$this->assertTrue(array_search($o2->id(), $ids));
		$o->delete(true);
		$o1->delete(true);
		$o2->delete(true);
	}

	function test_delete()
	{
		$tree = $this->_get_temp_tree();
		$num = $tree->delete();
		$this->assertEqual($num, 4);
		$ol = $tree->to_list();
		$this->assertEqual($ol->count(), 0);
	}

	function test_remove()
	{
		$o = $this->_get_temp_o();
		$o1 = $this->_get_temp_o();
		$o2 = $this->_get_temp_o();
		aw_disable_acl();
		$o1->set_parent($o->id());
		$o1->save();
		$o2->set_parent($o1->id());
		$o2->save();
		aw_restore_acl();
		$tree = new object_tree(array(
			"parent" => $o->id(),
		));
		$num = $tree->remove($o2);
		$this->assertEqual($num, 1);
		$ol = $tree->to_list();
		$this->assertEqual($ol->count(), 1);
		$o->delete(true);
		$o1->delete(true);
		$o2->delete(true);
	}

	function test_save()
	{
		$tree = $this->_get_temp_tree();
		aw_disable_acl();
		$tree->foreach_o(array(
			"func" => "set_name",
			"params" => array("foo"),
			"save" => false,
		));
		$tree->save();
		aw_restore_acl();
		$ol = $tree->to_list();
		foreach($ol->arr() as $o)
		{
			$this->assertEqual("foo", $o->name());
		}
		$this->_del_tree($tree);
	}

	function test_remove_all()
	{
		$o1 = $this->_get_temp_o();
		$o2 = $this->_get_temp_o();
		aw_disable_acl();
		$o2->set_parent($o1->id());
		$o2->save();
		aw_restore_acl();
		$tree = new object_tree(array(
			"parent" => $o1->id(),
		));
		$tree->remove_all();
		$ol = $tree->to_list();
		$this->assertEqual($ol->count(), 0);
		$o1->delete(true);
	}

	function test_ids()
	{
		$o = $this->_get_temp_o();
		$o1 = $this->_get_temp_o();
		$o2 = $this->_get_temp_o();
		aw_disable_acl();
		$o1->set_parent($o->id());
		$o1->save();
		$o2->set_parent($o1->id());
		$o2->save();
		aw_restore_acl();
		$tree = new object_tree(array(
			"parent" => $o->id(),
		));
		$ids = $tree->ids();
		$this->assertEqual(array_search($o1->id(), $ids), 0);
		$this->assertEqual(array_search($o2->id(), $ids), 1);
		$o->delete(true);
		$o1->delete(true);
		$o2->delete(true);
	}*/
}
?>
