<?php

class object_test extends UnitTestCase
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

	function test_construct_empty()
	{
		$o = new object();
		$this->assertTrue(!is_oid($o->id()));
	}

	function test_construct_id()
	{
		// get random object
		$o = new object($this->obj_id);
		$this->assertEqual($o->id(),$this->obj_id);
	}

	function test_construct_alias()
	{
		// get random object
		$this->db->db_query("SELECT oid,name,alias FROM objects WHERE status > 0 and alias != '' and site_id=".aw_ini_get("site_id"));
		while ($row = $this->db->db_next())
		{
			if ($this->db->can("view", $row["oid"]))
			{
				$o = new object($row["alias"]);
				$this->assertEqual($row["oid"],$o->id());
				return;
			}
		}
		$this->assertTrue(false);
	}

	function test_construct_obj()
	{
		// get random object
		$o = new object($this->obj_id);
		$o2 = new object($o);
		$this->assertEqual($o->id(),$o2->id());
	}

	function test_construct_fail_del()
	{
		// get random object
		$this->db->db_query("SELECT oid,name FROM objects WHERE status = 0");
		while ($row = $this->db->db_next())
		{
			__disable_err();
			$o = new object($row["oid"]);
			$this->assertTrue(__is_err());
			return;
		}
	}

	function test_save()
	{
		$o = obj($this->obj_id);
		$nc = $o->comment()+1;
		$o->set_comment($nc);
		aw_disable_acl();
		$o->save();
		aw_restore_acl();

		$nv = $this->db->db_fetch_field("SELECT comment FROM objects WHERE oid = ".$this->obj_id, "comment");
		$this->assertEqual($nc, $nv);
	}

	function test_save_fail()
	{
		$o = obj();
		__disable_err();
		$o->save();
		$this->assertTrue(__is_err());
	}

	function test_save_new()
	{
		$o = obj($this->obj_id);
		$nm = $o->name();
		aw_disable_acl();
		$nid = $o->save_new();
		$no = obj($nid);
		$this->assertEqual($nm, $no->name());
		$no->delete(true);
		aw_restore_acl();
	}

	function test_implicit_save()
	{
		$o = $this->_get_temp_o();
		$oldn = $o->name();
		$o->set_implicit_save(true);
		$this->assertTrue($o->get_implicit_save());
		aw_disable_acl();
		$o->set_name($oldn+1);
		$this->assertEqual($oldn+1, $this->db->db_fetch_field("SELECT name FROM objects WHERE oid = ".$o->id(), "name"));
		$o->set_implicit_save(false);
		$this->assertFalse($o->get_implicit_save());
		$o->set_name($oldn);
		$this->assertFalse($oldn == $this->db->db_fetch_field("SELECT name FROM objects WHERE oid = ".$o->id(), "name"));
		$o->save();
		$this->assertEqual($oldn, $this->db->db_fetch_field("SELECT name FROM objects WHERE oid = ".$o->id(), "name"));
		aw_restore_acl();
	}

	function test_arr()
	{
		$o = obj($this->obj_id);
		$ar = $o->arr();
		$this->assertEqual($ar["name"], $o->name());
	}

	function _get_temp_o()
	{
		aw_disable_acl();
		// create new object
		$o = obj();
		$o->set_parent(aw_ini_get("site_rootmenu"));
		$o->set_class_id(CL_MENU);
		$o->save();
		aw_restore_acl();

		return $o;
	}

	function test_delete()
	{
		$o = $this->_get_temp_o();
		$id = $o->id();
		aw_disable_acl();
		$ret = $o->delete();
		aw_restore_acl();
		$this->assertEqual($id, $ret);

		// verify that we do not find it in ol
		$ol = new object_list(array("oid" => $id));
		$this->assertEqual(0, $ol->count());

		// and that status = 0
		$this->assertEqual(0, $this->db->db_fetch_field("SELECT status FROM objects WHERE oid = $id", "status"));
	}

	function test_delete_final()
	{
		$o = $this->_get_temp_o();
		$id = $o->id();
		aw_disable_acl();
		$ret = $o->delete(true);
		aw_restore_acl();
		$this->assertEqual($id, $ret);

		// verify that we do not find it in ol
		$ol = new object_list(array("oid" => $id));
		$this->assertEqual(0, $ol->count());

		// and that no such line exists
		$this->assertEqual(0, $this->db->db_fetch_field("SELECT count(*) as cnt FROM objects WHERE oid = $id", "cnt"));
		$this->assertEqual(0, $this->db->db_fetch_field("SELECT count(*) as cnt FROM menu WHERE id = $id", "cnt"));
	}

	function test_connect_id()
	{
		$o = $this->_get_temp_o();
	
		aw_disable_acl();
		$o->connect(array(
			"to" => aw_ini_get("site_rootmenu")
		));

		$this->assertEqual(aw_ini_get("site_rootmenu"), $this->db->db_fetch_field("SELECT target FROM aliases WHERE source = ".$o->id(), "target"));
		$o->delete(true);
		aw_restore_acl();
	}

	function test_connect_obj()
	{
		$o = $this->_get_temp_o();
		$rm = obj(aw_ini_get("site_rootmenu"));
	
		aw_disable_acl();
		$o->connect(array(
			"to" => $rm
		));

		$this->assertEqual($rm->id(), $this->db->db_fetch_field("SELECT target FROM aliases WHERE source = ".$o->id(), "target"));
		$o->delete(true);
		aw_restore_acl();
	}

	function test_connect_list()
	{
		$o = $this->_get_temp_o();
		$ol = new object_list(array("oid" => array_keys(aw_ini_get("menuedit.menu_defs"))));
	
		aw_disable_acl();
		$o->connect(array(
			"to" => $ol
		));

		$this->assertEqual($ol->count(), $this->db->db_fetch_field("SELECT count(*) as cnt FROM aliases WHERE source = ".$o->id(), "cnt"));
		foreach($ol->ids() as $id)
		{
			$this->assertEqual($id, $this->db->db_fetch_field("SELECT target FROM aliases WHERE target = $id and source = ".$o->id(), "target"));
		}
		$o->delete(true);
		aw_restore_acl();
	}

	function test_connect_type()
	{
		$o = $this->_get_temp_o();
		$rm = obj(aw_ini_get("site_rootmenu"));
	
		aw_disable_acl();
		$o->connect(array(
			"to" => $rm,
			"type" => "RELTYPE_SEEALSO"
		));

		$this->assertEqual(5, $this->db->db_fetch_field("SELECT reltype FROM aliases WHERE source = ".$o->id(), "reltype"));
		$o->delete(true);
		aw_restore_acl();
	}

	function test_disconnect()
	{
		$o = $this->_get_temp_o();
		$rm = obj(aw_ini_get("site_rootmenu"));
	
		aw_disable_acl();
		$o->connect(array(
			"to" => $rm,
			"type" => "RELTYPE_SEEALSO"
		));

		$o->disconnect(array(
			"from" => $rm
		));

		$this->assertEqual(0, $this->db->db_fetch_field("SELECT count(*) as cnt FROM aliases WHERE source = ".$o->id(), "cnt"));
		$o->delete(true);
		aw_restore_acl();
	}

	function test_connections_from()
	{
		$o = $this->_get_temp_o();
		$rm = obj(aw_ini_get("site_rootmenu"));
	
		aw_disable_acl();
		$o->connect(array(
			"to" => $rm,
			"type" => "RELTYPE_SEEALSO"
		));

		$cf = $o->connections_from();
		$this->assertEqual(1, count($cf));
		if (count($cf))
		{
			$r = reset($cf);
			$this->assertEqual($r->prop("to"), $rm->id());
		}
		$o->delete(true);
		aw_restore_acl();
	}

	function test_connections_from_filter_type()
	{
		$o = $this->_get_temp_o();
		$rm = obj(aw_ini_get("site_rootmenu"));
	
		aw_disable_acl();
		$o->connect(array(
			"to" => $rm,
			"type" => "RELTYPE_SEEALSO"
		));

		$cf = $o->connections_from(array(
			"type" => "RELTYPE_SEEALSO"
		));
		$this->assertEqual(1, count($cf));
		if (count($cf))
		{
			$r = reset($cf);
			$this->assertEqual($r->prop("to"), $rm->id());
		}
		$o->delete(true);
		aw_restore_acl();
	}

	function test_connections_from_filter_class()
	{
		$o = $this->_get_temp_o();
		$rm = obj(aw_ini_get("site_rootmenu"));
	
		aw_disable_acl();
		$o->connect(array(
			"to" => $rm,
			"type" => "RELTYPE_SEEALSO"
		));

		$cf = $o->connections_from(array(
			"class" => CL_MENU
		));
		$this->assertEqual(1, count($cf));
		if (count($cf))
		{
			$r = reset($cf);
			$this->assertEqual($r->prop("to"), $rm->id());
		}
		$o->delete(true);
		aw_restore_acl();
	}

	function test_connections_from_filter_to()
	{
		$o = $this->_get_temp_o();
		$rm = obj(aw_ini_get("site_rootmenu"));
	
		aw_disable_acl();
		$o->connect(array(
			"to" => $rm,
			"type" => "RELTYPE_SEEALSO"
		));

		$cf = $o->connections_from(array(
			"to" => $rm
		));
		$this->assertEqual(1, count($cf));
		if (count($cf))
		{
			$r = reset($cf);
			$this->assertEqual($r->prop("to"), $rm->id());
		}
		$o->delete(true);
		aw_restore_acl();
	}

	function test_connections_from_filter_idx()
	{
		$o = $this->_get_temp_o();
		$rm = obj(aw_ini_get("site_rootmenu"));
		aw_disable_acl();
		$o->connect(array(
			"to" => $rm,
//			"type" => "RELTYPE_SEEALSO"	only no-reltype-connections get indexes
		));

		$cf = $o->connections_from(array(
			"idx" => 1
		));
		$this->assertEqual(1, count($cf));

		if (count($cf))
		{
			$r = reset($cf);
			$this->assertEqual($r->prop("to"), $rm->id());
		}
		$o->delete(true);
		aw_restore_acl();
	}

	function test_connections_from_filter_to_field()
	{
		$o = $this->_get_temp_o();
		$rm = obj(aw_ini_get("site_rootmenu"));
	
		aw_disable_acl();
		$o->connect(array(
			"to" => $rm,
			"type" => "RELTYPE_SEEALSO"
		));

		$cf = $o->connections_from(array(
			"to.name" => $rm->name()
		));
		$this->assertEqual(1, count($cf));
		if (count($cf))
		{
			$r = reset($cf);
			$this->assertEqual($r->prop("to"), $rm->id());
		}
		$o->delete(true);
		aw_restore_acl();
	}

	function test_connections_from_filter_complex()
	{
		$o = $this->_get_temp_o();
		$rm = obj(aw_ini_get("site_rootmenu"));
	
		aw_disable_acl();
		$o->connect(array(
			"to" => $rm,
			"type" => "RELTYPE_SEEALSO"
		));

		$cf = $o->connections_from(array(
			"type" => "RELTYPE_SEEALSO",
			"class" => CL_MENU,
			"to" => $rm,
//			"idx" => 1, connections with reltype do not get indexes
			"to.name" => $rm->name(),
			"to.created" => $rm->created()
		));
		$this->assertEqual(1, count($cf));
		if (count($cf))
		{
			$r = reset($cf);
			$this->assertEqual($r->prop("to"), $rm->id());
		}
		$o->delete(true);
		aw_restore_acl();
	}

	function test_connections_from_err()
	{
		$o = obj();
		__disable_err();
		$o->connections_from();
		$this->assertTrue(__is_err());
	}

	function test_connections_to()
	{
		$o = $this->_get_temp_o();
		$rm = obj(aw_ini_get("site_rootmenu"));
	
		aw_disable_acl();
		$rm->connect(array(
			"to" => $o,
			"type" => "RELTYPE_SEEALSO"
		));

		$cf = $o->connections_to();
		$this->assertEqual(1, count($cf));
		if (count($cf))
		{
			$r = reset($cf);
			$this->assertEqual($r->prop("to"), $o->id());
		}
		$rm->disconnect(array("from" => $o));
		$o->delete(true);
		aw_restore_acl();
	}

	function test_connections_to_filter_type()
	{
		$o = $this->_get_temp_o();
		$rm = obj(aw_ini_get("site_rootmenu"));
	
		aw_disable_acl();
		$rm->connect(array(
			"to" => $o,
			"type" => "RELTYPE_SEEALSO"
		));

		$cf = $o->connections_to(array(
			"type" => "RELTYPE_SEEALSO",
			"from.class_id" => CL_MENU
		));
		$this->assertEqual(1, count($cf));
		if (count($cf))
		{
			$r = reset($cf);
			$this->assertEqual($r->prop("to"), $o->id());
		}
		$rm->disconnect(array("from" => $o));
		$o->delete(true);
		aw_restore_acl();
	}

	function test_connections_to_filter_class()
	{
		$o = $this->_get_temp_o();
		$rm = obj(aw_ini_get("site_rootmenu"));
	
		aw_disable_acl();
		$rm->connect(array(
			"to" => $o,
			"type" => "RELTYPE_SEEALSO"
		));

		$cf = $o->connections_to(array(
			"class" => CL_MENU
		));
		$this->assertEqual(1, count($cf));
		if (count($cf))
		{
			$r = reset($cf);
			$this->assertEqual($r->prop("to"), $o->id());
		}
		$rm->disconnect(array("from" => $o));
		$o->delete(true);
		aw_restore_acl();
	}

	function test_connections_to_filter_from()
	{
		$o = $this->_get_temp_o();
		$rm = obj(aw_ini_get("site_rootmenu"));
	
		aw_disable_acl();
		$rm->connect(array(
			"to" => $o,
			"type" => "RELTYPE_SEEALSO"
		));

		$cf = $o->connections_to(array(
			"from" => $rm
		));
		$this->assertEqual(1, count($cf));
		if (count($cf))
		{
			$r = reset($cf);
			$this->assertEqual($r->prop("to"), $o->id());
		}
		$rm->disconnect(array("from" => $o));
		$o->delete(true);
		aw_restore_acl();
	}

	function test_connections_to_filter_idx()
	{
		$o = $this->_get_temp_o();
		$rm = obj(aw_ini_get("site_rootmenu"));
	
		aw_disable_acl();
		$rm->connect(array(
			"to" => $o,
//			"type" => "RELTYPE_SEEALSO"	with reltype do not get indexes
		));

		$cf = $o->connections_to(array(
			"idx" => 1
		));
		$this->assertEqual(1, count($cf));
		if (count($cf))
		{
			$r = reset($cf);
			$this->assertEqual($r->prop("to"), $o->id());
		}
		$rm->disconnect(array("from" => $o));
		$o->delete(true);
		aw_restore_acl();
	}

	function test_connections_to_filter_from_field()
	{
		$o = $this->_get_temp_o();
		$rm = obj(aw_ini_get("site_rootmenu"));
	
		aw_disable_acl();
		$rm->connect(array(
			"to" => $o,
			"type" => "RELTYPE_SEEALSO"
		));

		$cf = $o->connections_to(array(
			"from.name" => $rm->name()
		));
		$this->assertEqual(1, count($cf));
		if (count($cf))
		{
			$r = reset($cf);
			$this->assertEqual($r->prop("to"), $o->id());
		}
		$o->delete(true);
		aw_restore_acl();
	}

	function test_connections_to_filter_complex()
	{
		$o = $this->_get_temp_o();
		$rm = obj(aw_ini_get("site_rootmenu"));
	
		aw_disable_acl();
		$rm->connect(array(
			"to" => $o,
			"type" => "RELTYPE_SEEALSO"
		));

		$cf = $o->connections_to(array(
			"type" => "RELTYPE_SEEALSO",
			"class" => CL_MENU,
			"from" => $rm,
//			"idx" => 1,	no index for with reltype
			"from.name" => $rm->name(),
			"from.created" => $rm->created(),
			"from.class_id" => CL_MENU
		));
		$this->assertEqual(1, count($cf));
		if (count($cf))
		{
			$r = reset($cf);
			$this->assertEqual($r->prop("from"), $rm->id());
		}
		$o->delete(true);
		aw_restore_acl();
	}

	function test_connections_to_err()
	{
		$o = obj();
		__disable_err();
		$o->connections_to();
		$this->assertTrue(__is_err());
	}

	function test_get_first_conn_by_reltype()
	{
		$o = $this->_get_temp_o();
		$rm = obj(aw_ini_get("site_rootmenu"));
	
		aw_disable_acl();
		$o->connect(array(
			"to" => $rm,
			"type" => "RELTYPE_SEEALSO"
		));

		$cf = $o->get_first_conn_by_reltype("RELTYPE_SEEALSO");
		$this->assertEqual("connection", get_class($cf));
		$this->assertEqual($cf->prop("to"), $rm->id());
		$o->delete(true);
		aw_restore_acl();
	}

	function test_get_first_conn_by_reltype_false()
	{
		$o = $this->_get_temp_o();
		$rm = obj(aw_ini_get("site_rootmenu"));
	
		aw_disable_acl();

		$cf = $o->get_first_conn_by_reltype();
		$this->assertFalse($cf);
		$o->delete(true);
		aw_restore_acl();
	}

	function test_get_first_obj_by_reltype()
	{
		$o = $this->_get_temp_o();
		$rm = obj(aw_ini_get("site_rootmenu"));
	
		aw_disable_acl();
		$o->connect(array(
			"to" => $rm,
			"type" => "RELTYPE_SEEALSO"
		));

		$cf = $o->get_first_obj_by_reltype("RELTYPE_SEEALSO");
		$this->assertEqual("object", get_class($cf));
		$this->assertEqual($cf->id(), $rm->id());
		$o->delete(true);
		aw_restore_acl();
	}

	function test_get_first_obj_by_reltype_false()
	{
		$o = $this->_get_temp_o();
		$rm = obj(aw_ini_get("site_rootmenu"));
	
		aw_disable_acl();

		$cf = $o->get_first_obj_by_reltype();
		$this->assertFalse($cf);
		$o->delete(true);
		aw_restore_acl();
	}

	function test_get_first_conn_by_reltype_err()
	{
		__disable_err();
		$o = obj();
		$cf = $o->get_first_obj_by_reltype();
		$this->assertTrue(__is_err());
	}

	function test_get_first_obj_by_reltype_err()
	{
		__disable_err();
		$o = obj();
		$cf = $o->get_first_obj_by_reltype();
		$this->assertTrue(__is_err());
	}

	function test_path_err_no_obj()
	{
		__disable_err();
		$o = obj();
		$o->path();
		$this->assertTrue(__is_err());
	}

	function test_path_err_static_no_id()
	{
		__disable_err();
		object::path();
		$this->assertTrue(__is_err());
	}

	function test_path_err_param()
	{
		__disable_err();
		$rm = obj(aw_ini_get("site_rootmenu"));
		$rm->path(56);
		$this->assertTrue(__is_err());
	}

	function test_path()
	{
		$o = obj(aw_ini_get("site_rootmenu"));
		$pt = $o->path();
		$this->assertEqual(count($pt), 1);
		$this->assertEqual($pt[0]->id(), aw_ini_get("site_rootmenu"));
	}

	function test_path_str_err_obj()
	{
		__disable_err();
		$o = obj();
		$o->path_str();
		$this->assertTrue(__is_err());
	}

	function test_path_str_err_param()
	{
		__disable_err();
		$o = obj(aw_ini_get("site_rootmenu"));
		$o->path_str(56);
		$this->assertTrue(__is_err());
	}

	function test_path_str()
	{
		$o = obj(aw_ini_get("site_rootmenu"));
		$str = $o->path_str();
		$this->assertEqual($str, $o->name());
	}

	function test_path_str_err_static()
	{
		__disable_err();
		object::path_str();
		$this->assertTrue(__is_err());
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
		$o1->delete(true);
		$o2->delete(true);
		$o3->delete(true);
		
		$this->assertTrue(__is_err());
	}
}
?>
