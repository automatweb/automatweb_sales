<?php

class connection_test extends UnitTestCase
{
	function connection_test($name)
	{
		 $this->UnitTestCase($name);
	}

	function setUp()
	{
		$this->db = get_instance("class_base");

		aw_disable_acl();
		$this->o_from = $this->_get_temp_o();
		$this->o_to = $this->_get_temp_o();		
	}

	function tearDown()
	{
		$this->o_from->delete(true);
		$this->o_to->delete(true);
		aw_restore_acl();
	}

	function test_construct()
	{
		$c = new connection();
		$this->assertTrue(!$c->id());
	}

	function test_construct_id()
	{
		$row = $this->db->db_fetch_row("SELECT id,source,target FROM aliases LIMIT 1", "id");
		$c = new connection($row["id"]);
		$this->assertTrue($c->prop("from") == $row["source"]);
		$this->assertTrue($c->prop("to") == $row["target"]);
	}

	function test_construct_arr_from_to()
	{
		$c = new connection(array(
			'from' => $this->o_from->id(),
			'to' => $this->o_to->id(),
		));
		$this->assertTrue($c->prop('from') == $this->o_from->id());
		$this->assertTrue($c->prop('to') == $this->o_to->id());
		$this->assertTrue($c->prop('reltype') == 0);
		// also we should check that it didn't write anything to db
		$id = $this->db->db_fetch_field("SELECT id FROM aliases WHERE source = ".$this->o_from->id()." AND target = ".$this->o_to->id()." AND reltype = 0", "id");
		$this->assertTrue($id == NULL);
	}

	function test_construct_arr_from_to_reltype()
	{
		$c = new connection(array(
			'from' => $this->o_from->id(),
			'to' => $this->o_to->id(),
			'reltype' => 669
		));
		$this->assertTrue($c->prop('from') == $this->o_from->id());
		$this->assertTrue($c->prop("to") == $this->o_to->id());
		$this->assertTrue($c->prop("reltype") == 669);
		// also we should check that it didn't write anything to db
		$id = $this->db->db_fetch_field("SELECT id FROM aliases WHERE source = ".$this->o_from->id()." AND target = ".$this->o_to->id()." AND reltype = 669", "id");
		$this->assertTrue($id == NULL);
	}

	function test_construct_arr_from_to_reltype_data()
	{
		$c = new connection(array(
			'from' => $this->o_from->id(),
			'to' => $this->o_to->id(),
			'reltype' => 669,
			'data' => 'this string must be saved with connection'
		));
		$this->assertTrue($c->prop('from') == $this->o_from->id());
		$this->assertTrue($c->prop("to") == $this->o_to->id());
		$this->assertTrue($c->prop("reltype") == 669);
		$this->assertTrue($c->prop('data') === 'this string must be saved with connection');
		// also we should check that it didn't write anything to db
		$id = $this->db->db_fetch_field("SELECT id FROM aliases WHERE source = ".$this->o_from->id()." AND target = ".$this->o_to->id()." AND reltype = 669", "id");
		$this->assertTrue($id == NULL);
	}

	function test_construct_arr_from_to_reltype_data_idx()
	{
		$c = new connection(array(
			'from' => $this->o_from->id(),
			'to' => $this->o_to->id(),
			'reltype' => 669,
			'data' => 'this string must be saved with connection',
			'idx' => 777
		));
		$this->assertTrue($c->prop('from') == $this->o_from->id());
		$this->assertTrue($c->prop('to') == $this->o_to->id());
		$this->assertTrue($c->prop('reltype') == 669);
		$this->assertTrue($c->prop('data') === 'this string must be saved with connection');
		$this->assertTrue($c->prop('idx') == 777);
		// also we should check that it didn't write anything to db
		$id = $this->db->db_fetch_field("SELECT id FROM aliases WHERE source = ".$this->o_from->id()." AND target = ".$this->o_to->id()." AND reltype = 669", "id");
		$this->assertTrue($id == NULL);
	}

	function test_construct_arr_save()
	{
		$c = new connection(array(
			'from' => $this->o_from->id(),
			'to' => $this->o_to->id(),
			'reltype' => 669,
			'data' => 'this string must be saved with connection',
			'idx' => 777
		));
		$c->save();
		$this->assertTrue($c->prop('from') == $this->o_from->id());
		$this->assertTrue($c->prop('to') == $this->o_to->id());
		$this->assertTrue($c->prop('reltype') == 669);
		$this->assertTrue($c->prop('data') === 'this string must be saved with connection');
		$this->assertTrue($c->prop('idx') == 777);

		// also we should check that it wrote the connection to database
		$id = $this->db->db_fetch_field("SELECT id FROM aliases WHERE source = ".$this->o_from->id()." AND target = ".$this->o_to->id()." AND reltype = 669", "id");
		$this->assertTrue($id > 0);

		$data_field = $this->db->db_fetch_field("SELECT data FROM aliases WHERE source = ".$this->o_from->id()." AND target = ".$this->o_to->id()." AND reltype = 669", "data");
		$this->assertTrue($data_field === 'this string must be saved with connection');

		$idx_field = $this->db->db_fetch_field("SELECT idx FROM aliases WHERE source = ".$this->o_from->id()." AND target = ".$this->o_to->id()." AND reltype = 669", "idx");
		$this->assertTrue((int)$idx_field === 777);
	}

	function test_construct_err()
	{
		__disable_err();
		$c = new connection(new object());
		$this->assertTrue(__is_err());
		
		__disable_err();
		$c = new connection("mingi id");
		$this->assertTrue(__is_err());
	}

	function test_load_id()
	{
		$row = $this->db->db_fetch_row("SELECT id,source,target FROM aliases LIMIT 1", "id");
		$c = new connection();
		$c->load($row["id"]);
		$this->assertTrue($c->prop("from") == $row["source"]);
		$this->assertTrue($c->prop("to") == $row["target"]);
	}

	function test_load_arr()
	{
		$c = new connection();
		$c->load(array(
			'from' => $this->o_from->id(),
			'to' => $this->o_to->id(),
			'reltype' => 669,
			'data' => 'this string must be saved with connection',
			'idx' => 777
		));
		$this->assertTrue($c->prop('from') == $this->o_from->id());
		$this->assertTrue($c->prop('to') == $this->o_to->id());
		$this->assertTrue($c->prop('reltype') == 669);
		$this->assertTrue($c->prop('data') === 'this string must be saved with connection');
		$this->assertTrue($c->prop('idx') == 777);
		// also we should check that it didn't write anything to db
		$id = $this->db->db_fetch_field("SELECT id FROM aliases WHERE source = ".$this->o_from->id()." AND target = ".$this->o_to->id()." AND reltype = 669", "id");
		$this->assertTrue($id == NULL);
	}

	function test_load_err()
	{
		__disable_err();
		$c = new connection();
		$c->load(new object());
		$this->assertTrue(__is_err());

		__disable_err();
		$c = new connection();
		$c->load("mingi id");
		$this->assertTrue(__is_err());
	}

	function test_find_err()
	{
		__disable_err();
		$c = new connection();
		$c->find();
		$this->assertTrue(__is_err());
	}

	function test_find_from()
	{
		// get some conns and check those
		$row = $this->db->db_fetch_row("SELECT id,source FROM aliases");
		
		$c = new connection();
		$res = $c->find(array(
			"from" => $row["source"]
		));
		$first = reset($res);
		$this->assertTrue($first["from"] == $row["source"]);
	}

	function test_find_to()
	{
		// get some conns and check those
		$row = $this->db->db_fetch_row("SELECT id,target FROM aliases");
		
		$c = new connection();
		$res = $c->find(array(
			"to" => $row["target"]
		));
		$first = reset($res);
		$this->assertTrue($first["to"] == $row["target"]);
	}

	function test_find_type()
	{
		// get some conns and check those
		$row = $this->db->db_fetch_row("SELECT id,target,reltype FROM aliases");
		
		$c = new connection();
		$res = $c->find(array(
			"to" => $row["target"]
		));
		$first = reset($res);
		$this->assertTrue($first["reltype"] == $row["reltype"]);
	}

	function test_find_to_obj()
	{
		// get some conns and check those
		$row = $this->db->db_fetch_row("SELECT id,target,o.name as name FROM aliases left join objects o on o.oid = aliases.target");
		
		$c = new connection();
		$res = $c->find(array(
			"to" => $row["target"]
		));
		$first = reset($res);
		$this->assertTrue($first["to.name"] == $row["name"]);
	}

	function test_connection_change_err()
	{
		$row = $this->db->db_fetch_row("SELECT id,source,target FROM aliases LIMIT 1", "id");
		$c = new connection();
		$c->load($row["id"]);

		__disable_err();
		$c->change(1);
		$this->assertTrue(__is_err());
	}

	function test_connection_change()
	{
		$row = $this->db->db_fetch_row("SELECT id,source,target,data FROM aliases LIMIT 1", "id");
		$c = new connection();
		$c->load($row["id"]);
		$c->change(array(
			"data" => "8"
		));
		$row = $this->db->db_fetch_row("SELECT id,source,target,data FROM aliases LIMIT 1", "id");
		$this->assertTrue($row["data"] == 8);
	}

	function test_connection_delete()
	{
		// create
		$c = new connection(array(
			"from" => $this->o_from->id(),
			"to" => $this->o_to->id(),
			"reltype" => 669
		));
		$c->save();
		$this->assertTrue($c->prop("from") == $this->o_from->id());
		$this->assertTrue($c->prop("to") == $this->o_to->id());
		$this->assertTrue($c->prop("reltype") == 669);
		// also we should check that it wrote the conn
		$id = $this->db->db_fetch_field("SELECT id FROM aliases WHERE source = ".$this->o_from->id()." AND target = ".$this->o_to->id()." AND reltype = 669", "id");
		$this->assertTrue($id > 0);
		// now delete the thing
		$res = $c->delete();
		$this->assertTrue($id == $res);
		$id2 = $this->db->db_fetch_field("SELECT id FROM aliases WHERE source = ".$this->o_from->id()." AND target = ".$this->o_to->id()." AND reltype = 669", "id");
		$this->assertTrue($id2 < 1);
	}

	function test_connection_delete_err()
	{
		$c = new connection();
		__disable_err();
		$c->delete();
		$this->assertTrue(__is_err());
	}

	function test_connection_id()
	{
		$row = $this->db->db_fetch_row("SELECT id,source,target FROM aliases LIMIT 1", "id");
		$c = new connection();
		$c->load($row["id"]);
		$this->assertTrue($row["id"] == $c->id());
	}

	function test_connection_to()
	{
		$row = $this->db->db_fetch_row("SELECT id,source,target FROM aliases LIMIT 1", "id");
		$c = new connection();
		$c->load($row["id"]);
		$to = $c->to();
		$this->assertTrue($row["target"] == $to->id());
	}

	function test_connection_to_err()
	{
		$c = new connection();
		__disable_err();
		$to = $c->to();
		$this->assertFalse(__is_err());
	}

	function test_connection_from()
	{
		$row = $this->db->db_fetch_row("SELECT id,source,target FROM aliases LIMIT 1", "id");
		$c = new connection();
		$c->load($row["id"]);
		$from = $c->from();
		$this->assertTrue($row["source"] == $from->id());
	}

	function test_connection_from_err()
	{
		$c = new connection();
		__disable_err();
		$from = $c->from();
		$this->assertFalse(__is_err());
	}

	function test_prop_reltype()
	{
		$row = $this->db->db_fetch_row("SELECT id, reltype FROM aliases LIMIT 1");
		$c = new connection();
		$c->load($row["id"]);
		$this->assertTrue($row["reltype"] == $c->prop("reltype"));
	}

	function test_prop_err()
	{
		$c = new connection();
		__disable_err();
		$to = $c->prop("to");
		$this->assertFalse(__is_err());
	}

	function test_prop_from_name()
	{
		$row = $this->db->db_fetch_row("SELECT a.id, f.name FROM aliases a, objects f WHERE a.source = f.oid LIMIT 1");
		$c = new connection();
		$c->load($row["id"]);
		$this->assertTrue($row["name"] == $c->prop("from.name"));
	}

	function test_prop_to_name()
	{
		$row = $this->db->db_fetch_row("SELECT a.id, t.class_id FROM aliases a, objects t WHERE a.target = t.oid LIMIT 1");
		$c = new connection();
		$c->load($row["id"]);
		$this->assertTrue($row["class_id"] == $c->prop("to.class_id"));
	}

	function test_alias_to_link_true()
	{
		$c = new connection(array(
			"from" => $this->o_from->id(),
			"to" => $this->o_to->id(),
			"reltype" => 669,
		));
		$c->save();
		$c->alias_to_link(true);
		$row = $this->db->db_fetch_row("SELECT a.target, f.metadata FROM aliases a, objects f WHERE a.source = f.oid AND a.id = ".$c->id()." LIMIT 1");
		$meta = aw_unserialize($row["metadata"]);
		$this->assertTrue($meta["aliaslinks"][$row["target"]] == 1);
	}

	function test_alias_to_link_false()
	{
		$c = new connection(array(
			"from" => $this->o_from->id(),
			"to" => $this->o_to->id(),
			"reltype" => 669,
		));
		$c->save();
		$c->alias_to_link(false);
		$row = $this->db->db_fetch_row("SELECT a.target, f.metadata FROM aliases a, objects f WHERE a.source = f.oid AND a.id = ".$c->id()." LIMIT 1");
		$meta = aw_unserialize($row["metadata"]);
		$this->assertFalse(isset($meta["aliaslinks"][$row["target"]]));
	}

	function _get_temp_o()
	{
	//	aw_disable_acl();
		// create new object
		$o = obj();
		$o->set_parent(aw_ini_get("site_rootmenu"));
		$o->set_class_id(CL_MENU);
		$o->save();
	//	aw_restore_acl();

		return $o;
	}
}

?>
