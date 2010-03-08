<?php

class oql_test extends UnitTestCase
{
	function oql_test($name)
	{
		 $this->UnitTestCase($name);
	}

/*	function setUp()
	{
		$this->db = get_instance("class_base");
		aw_disable_acl();
		$this->tmp_objs = array();
	}

	function tearDown()
	{
		foreach($this->tmp_objs as $doomed_obj);
		{
			$doomed_obj->delete(true);
		}
		aw_restore_acl();
	}

	function test_execute_query_clid_without_where_clause()
	{
		return;
		$rv = oql::compile_query("
		SELECT
			name
		FROM
			CL_MENU
		");
		$rv2 = oql::execute_query($rv);
		$ok = true;
		foreach($rv2 as $oid => $data)
		{
			if($this->get_class_id($oid) != CL_MENU)
			{
				$ok = false;
				break;
			}				
		}
		$this->assertTrue($ok);
	}

	// I'm using "WHERE true" cause the darn thing won't work without WHERE clause.
	function test_execute_query_clid_with_where_clause()
	{
		$rv = oql::compile_query("
		SELECT
			name
		FROM
			CL_MENU
		WHERE
			true
		");
		$rv2 = oql::execute_query($rv);
		$ok = true;
		foreach($rv2 as $oid => $data)
		{
			if($this->get_class_id($oid) != CL_MENU)
			{
				$ok = false;
				break;
			}				
		}
		$this->assertTrue($ok);
	}

	// I'm using "WHERE true" cause the darn thing won't work without WHERE clause.
	// Check if it returns all the properties asked for in the SELECT [...] clause.
	function test_execute_query_atleast_properties_asked_for()
	{
		// Could I use LIMIT 1???
		$rv = oql::compile_query("
		SELECT
			name, firstname, lastname, balance
		FROM
			CL_CRM_PERSON
		WHERE
			true
		");
		$aks = array("name", "firstname", "lastname", "balance");
		$rv2 = oql::execute_query($rv);
		$ok = true;
		foreach($rv2 as $id => $data)
		{
			foreach($aks as $ak)
			{
				if(!array_key_exists($ak, $data))
				{
					$ok = false;
					break;
				}
			}
		}
		$this->assertTrue($ok);
	}

	// I'm using "WHERE true" cause the darn thing won't work without WHERE clause.
	// Check if it returns any properties NOT asked for in the SELECT [...] clause.
	function test_execute_query_only_properties_asked_for()
	{
		// Could I use LIMIT 1???
		$rv = oql::compile_query("
		SELECT
			name
		FROM
			CL_CRM_PERSON
		WHERE
			true
		");
		$rv2 = oql::execute_query($rv);
		$ok = true;
		foreach($rv2 as $id => $data)
		{
			foreach($data as $dk => $dv)
			{
				if($dk != "name")
				{
					$ok = false;
					break;
				}
			}
		}
		$this->assertTrue($ok);
	}

	// I'm using "WHERE true" cause the darn thing won't work without WHERE clause.
	// Test if "LIMIT n" works.
	function test_execute_query_limit_n()
	{
		$rv = oql::compile_query("
		SELECT
			name
		FROM
			CL_MENU
		WHERE
			true
		LIMIT 1
		");
		$rv2 = oql::execute_query($rv);
		assertTrue(count($rv2) == 1);
	}

	function test_execute_query_with_arguements()
	{
		$row = $this->db->db_fetch_row("SELECT oid, name, status FROM objects WHERE class_id = '".CL_MENU."' LIMIT 1");
		$rv = oql::compile_query("
		SELECT
			name, status
		FROM
			CL_MENU
		WHERE
			name = '%s' AND status = '%s'
		");
		$rv2 = oql::execute_query($rv, array($row["name"], $row["status"]));
		assertTrue($rv2[$row["oid"]]["name"] == $row["name"] && $rv2[$row["oid"]]["status"] == $row["status"]);
	}

	function test_execute_query_same_query_different_params()
	{
		$row = $this->db->db_fetch_row("SELECT oid, name, status FROM objects WHERE class_id = '".CL_MENU."' LIMIT 1");
		$rv = oql::compile_query("
		SELECT
			name, status
		FROM
			CL_MENU
		WHERE
			name = '%s' AND status = '%s'
		");
		$rv2 = oql::execute_query($rv, array($row["name"], $row["status"]));
		$ok = ($rv2[$row["oid"]]["name"] == $row["name"] && $rv2[$row["oid"]]["status"] == $row["status"]);
		$row2 = $this->db->db_fetch_row("SELECT oid, name, status FROM objects WHERE class_id = '".CL_MENU."' AND oid != ".$row["oid"]." LIMIT 1");
		$rv3 = oql::execute_query($rv, array($row2["name"], $row2["status"]));
		$ok = ($ok && $rv3[$row2["oid"]]["name"] == $row2["name"] && $rv3[$row2["oid"]]["status"] == $row2["status"]);
		assertTrue($ok);
	}

	function test_execute_query_select_props_prop()
	{
		$o1 = $this->_get_temp_o();
		$o2 = $this->_get_temp_o();
		$o3 = $this->_get_temp_o();

		$o1->set_prop("name", "This_is_very_unique_name_Foo1");
		$o1->save();

		$o2->set_prop("name", "This_is_very_unique_name_Foo2");
		$o2->set_prop("submenus_from_menu", $o1->id());
		$o2->save();

		$o3->set_prop("name", "This_is_very_unique_name_Foo3");
		$o3->set_prop("images_from_menu", $o2->id());
		$o3->save();

		$rv = oql::compile_query("
		SELECT
			images_from_menu.submenus_from_menu.name
		FROM
			CL_MENU
		WHERE
			name = '%s'
		");
		$r2 = oql::execute_query($rv, array($o3->name()));

		$this->assertTrue($rv[$o3->id()]["images_from_menu.submenus_from_menu.name"] == $o1->name());
	}

	function test_execute_query_select_all()
	{
		$q = oql::compile_query("
		SELECT
			*
		FROM
			CL_MENU
		LIMIT 1
		");
		$v = oql::execute_query($q, array());
		$ok = count($v) > 0;
		// Only checks some of the properties. But I selected 'em randomly. ;-) Some from objects table and some from menu table.
		$ps = array("oid, class_id", "parent", "brother_of", "type", "link", "name", "comment", "alias");
		foreach($v as $data)
		{
			foreach($ps as $p)
			{
				if(!array_key_exists($p, $data))
				{
					$ok = false;
					break;
				}
			}
		}
		$this->assertTrue($ok);
	}

	function test_execute_query_select_oid()
	{
		$row = $this->db->db_fetch_row("SELECT name, oid FROM objects WHERE class_id = '".CL_MENU."' ORDER BY RAND() LIMIT 1");
		$ol = new object_list(array(
			"class_id" => CL_MENU,
			"parent" => array(),
			"lang_id" => array(),
			"site_id" => array(),
			"status" => array(),
			"name" => $row["name"],
		));
		$rv = oql::compile_query("
		SELECT
			oid
		FROM
			CL_MENU
		WHERE
			name = '%s'
		");
		$v = oql::execute_query($rv, array($row["name"]));
		$this->assertTrue($v[$row["oid"]]["oid"] == $row["oid"]);
	}

	function test_execute_query_select_status()
	{
		$row = $this->db->db_fetch_row("SELECT oid, status FROM objects WHERE class_id = '".CL_MENU."' ORDER BY RAND() LIMIT 1");
		$rv = oql::compile_query("
		SELECT
			status
		FROM
			CL_MENU
		WHERE
			oid = '%u'
		");
		$v = oql::execute_query($rv, array($row["oid"]));
		$this->assertTrue($v[$row["oid"]]["status"] == $row["status"]);
	}

	function test_execute_query_where_props_prop()
	{
		$o1 = $this->_get_temp_o(array("class_id" => CL_CRM_PERSON));
		$o2 = $this->_get_temp_o(array("class_id" => CL_CRM_PERSON));
		$o3 = $this->_get_temp_o(array("class_id" => CL_LANGUAGE, "name" => "This_is_very_unique_name_Foo1"));

		$o1->client_manager = $o2->id();
		$o2->mlang = $o3->id();

		$rv = oql::compile_query("
		SELECT
			name
		FROM
			CL_CRM_PERSON
		WHERE
			client_manager.mlang.name = '%s'
		");
		$r2 = oql::execute_query($rv, array($o3->name()));
		$ok = count($rv2) > 0;
		foreach($rv2 as $id => $data)
		{
			$o = obj($id);
			$cm = obj($o->client_manager);
			if($cm->prop("mlang.name") != $o3->name())
			{
				$ok = false;
			}
		}
		$this->assertTrue($ok);
	}

	function test_execute_query_where_reltype()
	{
		$o1 = $this->_get_temp_o();
		$o2 = $this->_get_temp_o();

		$o1->set_prop("name", "This_is_very_unique_name_Foo1");
		$o1->save();

		$o2->set_prop("name", "This_is_very_unique_name_Foo2");
		$o2->save();

		$o1->connect(array(
			"to" => $o2->id(),
			"type" => "RELTYPE_SHOW_SUBFOLDERS_MENU",
		));

		$rv = oql::compile_query("
		SELECT
			name
		FROM
			CL_MENU
		WHERE
			RELTYPE_SHOW_SUBFOLDERS_MENU = '%s'
		");
		$rv2 = oql::execute_query($rv, array($o2->id()));
		$this->assertTrue($rv2[$o1->id()]["name"] == $o1->name());
	}

	function test_execute_query_where_reltypes_props()
	{
		$o1 = $this->_get_temp_o(array("class_id" => CL_CRM_PERSON));
		$o2 = $this->_get_temp_o(array("class_id" => CL_CRM_PERSON));
		$o3 = $this->_get_temp_o(array("class_id" => CL_LANGUAGE, "name" => "This_is_very_unique_name_Foo1"));

		$o1->connect(array(
			"to" => $o2->id(),
			"type" => "RELTYPE_CLIENT_MANAGER",
		));
		$o2->mlang = $o3->id();

		$rv = oql::compile_query("
		SELECT
			name
		FROM
			CL_CRM_PERSON
		WHERE
			RELTYPE_CLIENT_MANAGER.mlang.name = '%s'
		");
		$r2 = oql::execute_query($rv, array($o3->name()));
		$ok = count($rv2) > 0;
		foreach($rv2 as $id => $data)
		{
			$o = obj($id);
			foreach($o->connections_from(array("type" => "RELTYPE_CLIENT_MANAGER")) as $conn)
			{
				$cm = $conn->to();
				if($cm->prop("mlang.name") != $o3->name())
				{
					$ok = false;
				}
			}
		}
		$this->assertTrue($ok);
	}

	function test_execute_query_where_like()
	{
		$o1 = $this->_get_temp_o();
		$o2 = $this->_get_temp_o();
		$o3 = $this->_get_temp_o();
		$o1->set_name("aaNAMEaa");
		$o2->set_name("NAME");
		$o3->set_name("NAMEaa");
		$q = oql::compile_query("
		SELECT 
			name
		FROM
			CL_MENU
		WHERE 
			name LIKE '%s'
		");
		$v = oql::execute_query($q, array("NAME"));
		$ok = true;
		foreach($v as $k => $d)
		{
			$o = obj($k);
			if(strpos("NAME", $o->name()) === false)
			{
				$ok = false;
				break;
			}
		}
		$this->assertTrue($ok);
		$this->assertTrue(array_key_exists($o1->id(), $v) && array_key_exists($o2->id(), $v) && array_key_exists($o3->id(), $v));
	}
	
	function test_execute_query_where_calculating()
	{
		$o = $this->_get_temp_o();

		$q = oql::compile_query("
			SELECT
				oid
			FROM
				CL_MENU
			WHERE
				oid * 69 = '%u'
		");
		$id = $o->id() * 69;
		$v = oql::execute_query($q, array($id));
		$this->assertTrue(count($v) == 1 && array_key_exists($o->id(), $v));
	}

	function test_execute_query_where_greater_than()
	{
		$row = $this->db->db_fetch_row("SELECT created FROM objects WHERE class_id = '".CL_MENU."' ORDER BY created DESC LIMIT 5, 1");
		$q = oql::compile_query("
		SELECT
			oid
		FROM
			CL_MENU
		WHERE
			created > '%u'
		");
		$v = oql::execute_query($q, array($row["created"]));
		$ok = true;
		foreach($v as $k => $d)
		{
			$o = obj($k);
			if($o->created <= $row["created"])
			{
				$ok = false;
				break;
			}
		}
		$this->assertTrue($ok);
	}

	function test_execute_query_where_equal_to_or_greater_than()
	{
		$row = $this->db->db_fetch_row("SELECT created FROM objects WHERE class_id = '".CL_MENU."' ORDER BY created DESC LIMIT 5, 1");
		$q = oql::compile_query("
		SELECT
			oid
		FROM
			CL_MENU
		WHERE
			created >= '%u'
		");
		$v = oql::execute_query($q, array($row["created"]));
		$ok = true;
		foreach($v as $k => $d)
		{
			$o = obj($k);
			if($o->created < $row["created"])
			{
				$ok = false;
				break;
			}
		}
		$this->assertTrue($ok);
	}

	function test_execute_query_where_less_than()
	{
		$row = $this->db->db_fetch_row("SELECT created FROM objects WHERE class_id = '".CL_MENU."' ORDER BY created ASC LIMIT 5, 1");
		$q = oql::compile_query("
		SELECT
			oid
		FROM
			CL_MENU
		WHERE
			created < '%u'
		");
		$v = oql::execute_query($q, array($row["created"]));
		$ok = true;
		foreach($v as $k => $d)
		{
			$o = obj($k);
			if($o->created >= $row["created"])
			{
				$ok = false;
				break;
			}
		}
		$this->assertTrue($ok);
	}

	function test_execute_query_where_equal_to_or_less_than()
	{
		$row = $this->db->db_fetch_row("SELECT created FROM objects WHERE class_id = '".CL_MENU."' ORDER BY created ASC LIMIT 5, 1");
		$q = oql::compile_query("
		SELECT
			oid
		FROM
			CL_MENU
		WHERE
			created <= '%u'
		");
		$v = oql::execute_query($q, array($row["created"]));
		$ok = true;
		foreach($v as $k => $d)
		{
			$o = obj($k);
			if($o->created > $row["created"])
			{
				$ok = false;
				break;
			}
		}
		$this->assertTrue($ok);
	}

	function test_execute_query_where_this_and_that()
	{
		$o1 = $this->_get_temp_o();
		$o1->set_name("Ahvi P2rdik");
		$o1->save();
		$o2 = $this->_get_temp_o();
		$o2->set_name("Ahvi P2rdik");
		$o2->save();

		$q = oql::compile_query("
		SELECT
			name
		FROM
			CL_MENU
		WHERE
			oid = '%u' OR name = '%s'
		");
		$v = oql::execute_query($q, array($o1->id(), "Ahvi P2rdik"));
		$this->assertTrue(count($v) == 1 && array_key_exists($o1->id()));
	}

	function test_execute_query_where_this_or_that()
	{
		$o1 = $this->_get_temp_o();
		$o2 = $this->_get_temp_o();

		$q = oql::compile_query("
		SELECT
			name
		FROM
			CL_MENU
		WHERE
			oid = '%u' OR oid = '%u'
		");
		$v = oql::execute_query($q, array($o1->id(), $o2->id()));
		$v0 = $v;
		unset($v0[$o1->id()]);
		unset($v0[$o2->id()]);
		$this->assertTrue(count($v) == 2 && count($v0) == 0);
	}

	function test_execute_query_where_parent()
	{
		$o1 = $this->_get_temp_o();
		$o2 = $this->_get_temp_o();
		$o2->set_parent($o1->id());
		$o2->save();

		$q = oql::compile_query("
		SELECT
			name
		FROM
			CL_MENU
		WHERE
			parent = '%u'
		");
		$v = oql::execute_query($q, array($o1->id()));
		// Query works correctly if it returns only what I asked for.
		$ok = true;
		foreach($v as $k => $d)
		{
			$o = obj($k);
			if($o->parent() != $o1->id())
			{
				$ok = false;
				break;
			}
		}
		$this->assertTrue($ok);
	}

	function test_execute_query_where_oid()
	{
		$row = $this->db->db_fetch_row("SELECT oid FROM objects WHERE class_id = '".CL_MENU."' ORDER BY RAND() LIMIT 1");
		$q = oql::compile_query("
		SELECT
			name
		FROM
			CL_MENU
		WHERE
			oid = '%u'
		");
		$v = oql::execute_query($q, array($row["oid"]));
		// Query works correctly if it returns only what I asked for.
		$ok = true;
		foreach($v as $k => $d)
		{
			if($k != $row["oid"])
			{
				$ok = false;
				break;
			}
		}
		$this->assertTrue($ok);
	}

	function test_execute_query_where_status()
	{
		$row = $this->db->db_fetch_row("SELECT status FROM objects WHERE class_id = '".CL_MENU."' ORDER BY RAND() LIMIT 1");
		$q = oql::compile_query("
		SELECT
			oid
		FROM
			CL_MENU
		WHERE
			status = '%s'
		");
		$v = oql::execute_query($q, array($row["status"]));
		// Query works correctly if it returns only what I asked for.
		$ok = true;
		foreach($v as $k => $d)
		{
			$o = obj($k);
			if($o->status() != $row["status"])
			{
				$ok = false;
				break;
			}
		}
		$this->assertTrue($ok);
	}

	function test_execute_query_where_brother_of()
	{
		$o1 = $this->_get_temp_o();
		$o2 = obj($o1->create_brother());

		$q = oql::compile_query("
		SELECT
			oid
		FROM
			CL_MENU
		WHERE
			brother_of = '%u'
		");
		$v = oql::execute_query($q, array($o1->id()));
		// Query works correctly if it returns only what I asked for.
		$ok = true;
		foreach($v as $k => $d)
		{
			$o = obj($k);
			if($o->brother_of() != $o1->id())
			{
				$ok = false;
				break;
			}
		}
		$this->assertTrue($ok);
	}

	function test_execute_query_where_class_id()
	{
		// To make it 100% sure there's atleast one objects matching the where condition.
		$o = $this->_get_temp_o();
		$q = oql::compile_query("
		SELECT
			name
		FROM
			CL_MENU, CL_CRM_PERSON
		WHERE
			class_id = %u
		");
		$v = oql::execute_query($q, array(CL_MENU));
		// There has to be atleast one object matching the search conditions.
		$ok = count($v) > 0;
		foreach($v as $id => $data)
		{
			$o = obj($id);
			if($o->class_id != CL_MENU)
			{
				$ok = false;
				break;
			}
		}
		$this->assertTrue($ok);
	}

	function test_execute_query_where_in_with_static_array_size()
	{
		$o1 = $this->_get_temp_o(array("name" => "This_is_very_unique_name_Foo1"));
		$o2 = $this->_get_temp_o(array("name" => "This_is_very_unique_name_Foo2"));
		$o3 = $this->_get_temp_o(array("name" => "This_is_very_unique_name_Foo3"));
		$o4 = $this->_get_temp_o(array("name" => "This_is_very_unique_name_Foo4"));

		$q = oql::compile_query("
		SELECT
			name
		FROM
			CL_MENU
		WHERE
			name IN (%s, %s, %s)
		");
		$nms = array("This_is_very_unique_name_Foo1", "This_is_very_unique_name_Foo2", "This_is_very_unique_name_Foo3");
		$v = oql::execute_query($q, $nms);
		$ok = count($v) > 0;
		foreach($v as $d)
		{
			if(!in_array($d["name"], $nms))
			{
				$ok = false;
				break;
			}
		}
		$this->assertTrue($ok);
	}

	function test_execute_query_where_with_brackets()
	{
		// Just to make sure there's atleast one object matching the search conditions.
		$o1 = $this->_get_temp_o();
		$o1->target = 1;
		$o1->clickable = 1;
		$o1->save();

		$rv = oql::compile_query("
		SELECT
			name
		FROM
			CL_MENU
		WHERE
			(target + clickable) * (%u + %u) >= %u
		");
		$v = oql::execute_query($rv, array(75, 6, 2*(75+6)));
		// There has to be atleast one object matching the search conditions.
		$ok = count($v) > 0;
		foreach($v as $id => $data)
		{
			$o = obj($id);
			if($o->target + $o->clickable != 2)
			{
				$ok = false;
				break;
			}
		}
		$this->assertTrue($ok);
	}

	function test_execute_query_from_multiple_classes_only_classes_asked_for()
	{
		$o1 = $this->_get_temp_o();
		$o1->name = "This_is_very_unique_name_Foo_Fighters";
		$o1->save();

		$o2 = obj();
		$o2->set_parent($o1->parent);
		$o2->set_class_id(CL_CRM_PERSON);
		$o2->name = "This_is_very_unique_name_Foo_Fighters";
		$o2->save();
		// Easier to kill it this way afterwards.
		$this->tmp_objs[] = $o2;

		$o3 = obj();
		$o3->set_parent($o1->parent);
		$o3->set_class_id(CL_CRM_COMPANY);
		$o3->name = "This_is_very_unique_name_Foo_Fighters";
		$o3->save();
		// Easier to kill it this way afterwards.
		$this->tmp_objs[] = $o3;

		$q = oql::compile_query("
		SELECT
			name
		FROM
			CL_MENU, CL_CRM_PERSON
		WHERE
			name = %s
		");
		$v = oql::execute_query($q, array("This_is_very_unique_name_Foo_Fighters"));
		$ok = count($v) >= 2;
		foreach($v as $id => $data)
		{
			$o = obj($id);
			if(!in_array($o->class_id, array(CL_MENU, CL_CRM_PERSON)))
			{
				$ok = false;
				break;
			}
		}
		$this->assertTrue($ok);
	}

	function test_execute_query_from_multiple_classes_atleast_classes_asked_for()
	{
		$o1 = $this->_get_temp_o();
		$o1->name = "This_is_very_unique_name_Foo_Fighters";
		$o1->save();

		$o2 = obj();
		$o2->set_parent($o1->parent);
		$o2->set_class_id(CL_CRM_PERSON);
		$o2->name = "This_is_very_unique_name_Foo_Fighters";
		$o2->save();
		// Easier to kill it this way afterwards.
		$this->tmp_objs[] = $o2;

		$q = oql::compile_query("
		SELECT
			name
		FROM
			CL_MENU, CL_CRM_PERSON
		WHERE
			name = %s
		");
		$v = oql::execute_query($q, array("This_is_very_unique_name_Foo_Fighters"));
		$this->assertTrue(array_key_exists($o1->id(), $v) && array_key_exists($o2->id(), $v));
	}

	function test_execute_query_order_by_desc()
	{
		$q = oql::compile_query("
		SELECT
			oid
		FROM
			CL_MENU
		ORDER BY
			oid DESC
		");
		$v = oql::execute_query($q, array());
		$prev = -1;
		$ok = count($v) >= 2;
		foreach($v as $d)
		{
			if($d["oid"] > $prev && $prev != -1)
			{
				$ok = false;
			}
			$prev = $d["oid"];
			$c++;
		}
		$this->assertTrue($ok);
	}

	function test_execute_query_order_by_asc()
	{
		$q = oql::compile_query("
		SELECT
			oid
		FROM
			CL_MENU
		ORDER BY
			oid ASC
		");
		$v = oql::execute_query($q, array());
		$prev = -1;
		$ok = count($v) >= 2;
		foreach($v as $d)
		{
			if($d["oid"] < $prev)
			{
				$ok = false;
			}
			$prev = $d["oid"];
			$c++;
		}
		$this->assertTrue($ok);
	}

	function get_class_id($id)
	{
		$row = $this->db->db_fetch_row("SELECT class_id FROM objects WHERE oid = ".$id." LIMIT 1");
		return $row["class_id"];
	}

	function _get_temp_o($arr = array())
	{
		// create new object
		$o = obj();
		$o->set_parent(isset($arr["parent"]) ? $arr["parent"] : aw_ini_get("site_rootmenu"));
		$o->set_class_id(isset($arr["class_id"]) ? $arr["class_id"] : CL_MENU);
		$o->name = $arr["name"];
		$o->save();
		// Easier to kill 'em this way afterwards.
		$this->tmp_objs[] = $o;

		return $o;
	}*/
}

?>
