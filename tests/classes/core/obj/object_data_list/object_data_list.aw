<?php

class object_data_list_test extends UnitTestCase
{
	function object_data_list_test($name)
	{
		 $this->UnitTestCase($name);
	}

	function setUp()
	{
		$this->db = get_instance("class_base");
		aw_disable_acl();
		$this->tmp_objs = array();
	}

	function tearDown()
	{
		foreach($this->tmp_objs as $doomed_obj)
		{
			$doomed_obj->delete(true);
		}
		aw_restore_acl();
	}
	function test_construct_aliases()
	{

		$odl = new object_data_list(
			array(
				"class_id" => CL_MENU,
				"limit" => 1
			),
			array(
				CL_MENU => array("comment" => "foo"),		// I'm trying to confuse it. :P
			)
		);
		foreach($odl->list_data as $oid => $odata)
		{
			$row = $this->db->db_fetch_row("SELECT oid, comment FROM objects WHERE oid = ".$oid." LIMIT 1");
			break;
		}
		$this->assertTrue($odl->list_data[$row["oid"]]["foo"] == $row["comment"]);
	}
	
	function test_arr_aliases()
	{
		$row = $this->db->db_fetch_row("SELECT oid, class_id, comment FROM objects WHERE status > 0 LIMIT 1");
		$odl = new object_data_list(
			array(
				"class_id" => $row["class_id"],
				"oid" => $row["oid"],
				"lang_id" => array(),
			),
			array(
				$row["class_id"] => array("comment" => "foo"),		// I'm trying to confuse it. :P
			)
		);
		$odl_arr = $odl->arr();
		$this->assertTrue($row["comment"] == $odl_arr[$row["id"]]["foo"]);
	}

	function test_arr_no_aliases()
	{
		$row = $this->db->db_fetch_row("SELECT oid, class_id, name FROM objects WHERE status > 0 LIMIT 1");
		$odl = new object_data_list(
			array(
				"class_id" => $row["class_id"],
				"oid" => $row["oid"],
				"lang_id" => array(),
				"site_id" => array()
			),
			array(
				$row["class_id"] => array("name"),
			)
		);
		$odl_arr = $odl->arr();
		$this->assertTrue($row["name"] == $odl_arr[$row["oid"]]["name"]);

	}

	function test_construct_no_aliases()
	{
		$odl = new object_data_list(
			array(
				"class_id" => CL_MENU,
				"limit" => 1
			),
			array(
				CL_MENU => array("name"),
			)
		);
		foreach($odl->list_data as $oid => $odata)
		{
			$row = $this->db->db_fetch_row("SELECT oid, name FROM objects WHERE oid = ".$oid." LIMIT 1");
			break;
		}
		$this->assertTrue($odl->list_data[$row["oid"]]["name"] == $row["name"]);
	}

	function test_filter_props_props()
	{
		return;
		$o1 = obj();
		$o1->set_parent(aw_ini_get("site_rootmenu"));
		$o1->set_class_id(CL_CRM_PERSON);
		$o1->save();
		$this->tmp_objs[] = $o1;

		$o2 = obj();
		$o2->set_parent(aw_ini_get("site_rootmenu"));
		$o2->set_class_id(CL_LANGUAGE);
		$o2->name = "This_is_very_unique_name_Foo_Fighters";
		$o2->save();

		$o1->mlang = $o1->id();
		$o1->save();

		$this->tmp_objs[] = $o2;

		$odl = new object_data_list(
			array(
				"class_id" => CL_CRM_PERSON,
				"mlang.name" => "%his_is_very_unique_name_Foo_Fighter%",
			),
			array(
				CL_CRM_PERSON => array("oid"),
			)
		);
		$v = $odl->arr();
		$ok = count($v) > 0;
		foreach($v as $d)
		{
			$o = obj($d["oid"]);
			if(!preg_match("his_is_very_unique_name_Foo_Fighter", $o->prop("mlang.name")))
			{
				$ok = false;
				break;
			}
		}
		$this->assertTrue($ok);
	}

	function test_filter_props_n_reltypes()
	{
return;
		$o1 = $this->_get_temp_o(array("name" => "This_is_very_unique_name_Foo_Fighters"));
		$o2 = $this->_get_temp_o();

		$o1->connect(array(
			"to" => $o2->id(),
			"type" => "RELTYPE_SHOW_SUBFOLDERS_MENU",
		));

		$odl = new object_data_list(
			array(
				"class_id" => CL_MENU,
				"name" => "%his_is_very_unique_name_Foo_Fighter%",
				"RELTYPE_SHOW_SUBFOLDERS_MENU" => $o2->id(),
			),
			array(
				CL_MENU => array("oid"),
			)
		);
		$v = $odl->arr();
		$ok = count($v) > 0;
		foreach($v as $od)
		{
			$o = obj($od["oid"]);
			if(!preg_match("his_is_very_unique_name_Foo_Fighter", $o->name))
			{
				$ok = false;
				break;
			}
			$conn_ok = false;
			foreach($o->connections_from(array("type" => "RELTYPE_SHOW_SUBFOLDERS_MENU")) as $conn)
			{
				if($conn->prop("to") == $o2->id())
				{
					$conn_ok = true;
				}
			}
			$ok = $ok && $conn_ok;
		}
		$this->assertTrue($ok);
	}

	function test_filter_reltypes_props()
	{
return;
		$o1 = $this->_get_temp_o();
		$o2 = $this->_get_temp_o(array("name" => "This_is_very_unique_name_Foo_Fighters"));
		$o1->connect(array(
			"to" => $o2->id(),
			"type" => "RELTYPE_SHOW_SUBFOLDERS_MENU",
		));
		$odl = new object_data_list(
			array(
				"class_id" => CL_MENU,
				"RELTYPE_SHOW_SUBFOLDERS_MENU.name" => "%his_is_very_unique_name_Foo_Fighter%",
			),
			array(
				CL_MENU => array("oid"),
			)
		);
		$v = $odl->arr();
		$ok = count($v) > 0;
		foreach($v as $d)
		{
			$o = obj($d["oid"]);
			foreach($o->connections_from(array("type" => "RELTYPE_SHOW_SUBFOLDERS_MENU")) as $conn)
			{
				if(!preg_match("his_is_very_unique_name_Foo_Fighter", $conn->prop("to.name")))
				{
					$ok = false;
					break;
				}
			}
		}
		$this->assertTrue($ok);
	}

	function test_props_props_parent_name()
	{
		return;
		$o1 = $this->_get_temp_o(array("name" => "This_is_very_unique_name_Foo_Fighters"));
		$o2 = $this->_get_temp_o(array("parent" => $o1->id()));

		$odl = new object_data_list(
			array(
				"class_id" => CL_MENU,
				"oid" => $o1->id(),
				"limit" => 1,
			),
			array(
				CL_MENU => array("parent.name" => "parent"),
			)
		);
		$odl_el = reset($odl->arr());
		$this->assertTrue($odl_el["parent"] == $o1->name());
	}

	function test_props_props_foo_ord()
	{
return;
		$o1 = $this->_get_temp_o(array("ord" => 865));
		$o2 = $this->_get_temp_o(array("parent" => $o1->id()));

		$odl = new object_data_list(
			array(
				"class_id" => CL_MENU,
				"oid" => $o1->id(),
				"limit" => 1,
			),
			array(
				CL_MENU => array("parent.ord"),
			)
		);
		$odl_el = reset($odl->arr());
		$this->assertTrue($odl_el["parent.ord"] == $o1->ord());
	}

	function test_props_props_foo_foo()
	{
return;
		$o1 = $this->_get_temp_o(array("class_id" => CL_CRM_PERSON));
		$o1->personal_id = 37806292799;
		$o1->save();

		$o2 = $this->_get_temp_o(array("class_id" => CL_CRM_PERSON));
		$o2->client_manager = $o1->id();
		$o2->save();

		$odl = new object_data_list(
			array(
				"class_id" => CL_MENU,
				"oid" => $o1->id(),
				"limit" => 1,
			),
			array(
				CL_MENU => array("client_manager.personal_id" => "foo"),
			)
		);
		$odl_el = reset($odl->arr());
		$this->assertTrue($odl_el["foo"] == $o1->personal_id);
	}

	function test_filter_multiple_clids()
	{
return;
		$odl = new object_data_list(
			array(
				"class_id" => array(CL_MENU, CL_CRM_PERSON),
				"parent" => array(),
				"lang_id" => array(),
				"site_id" => array(),
				"status" => array(),
			),
			array()
		);
		$ids = "";
		foreach(array_keys($odl->arr()) as $id)
		{
			$ids .= (strlen($ids) > 0) ? ", ".$id : $id;
		}
		$row = $this->db->db_fetch_row("SELECT COUNT(oid) as oid_cnt FROM objects WHERE oid IN (".$ids.") AND class_id NOT IN ('".CL_MENU."', '".CL_CRM_PERSON."')");
		$this->assertFalse($row["oid_cnt"] > 0);
	}

	function test_props_multiple_clids_props_asked_for()
	{
return;
		$o1 = $this->_get_temp_o(array("class_id" => CL_CRM_PERSON));
		$o1->personal_id = 37806292799;
		$o1->save();

		$o2 = $this->_get_temp_o(array("class_id" => CL_MENU, "name" => "This_is_very_unique_name_Foo2"));

		$odl = new object_data_list(
			array(
				"class_id" => array(CL_MENU, CL_CRM_PERSON),
				"oid" => array($o1->id(), $o2->id()),
			),
			array(
				CL_MENU => array("name" => "mname"),
				CL_CRM_PERSON => array("personal_id" => "pid"),
			)
		);
		$props = array(
			CL_MENU => array("mname"),
			CL_CRM_PERSON => array("pid"),
		);
		$ok = count($odl->arr()) == 2;
		foreach($odl->arr() as $oid => $odata)
		{
			$o = obj($oid);
			foreach($odata as $k => $v)
			{
				if(!in_array($k, $props[$o->class_id]))
				{
					$ok = false;
					break;
				}
			}
		}
		$this->assertTrue($ok);
	}

	function test_props_multiple_clids_values()
	{
return;
		$o1 = $this->_get_temp_o(array("class_id" => CL_CRM_PERSON));
		$o1->personal_id = 37806292799;
		$o1->save();

		$o2 = $this->_get_temp_o(array("class_id" => CL_MENU, "name" => "This_is_very_unique_name_Foo2"));

		$odl = new object_data_list(
			array(
				"class_id" => array(CL_MENU, CL_CRM_PERSON),
				"oid" => array($o1->id(), $o2->id()),
			),
			array(
				CL_MENU => array("name" => "mname"),
				CL_CRM_PERSON => array("personal_id" => "pid"),
			)
		);
		$props = array(
			"mname" => "name",
			"pid" => "personal_id"
		);
		$ok = count($odl->arr()) == 2;
		foreach($odl->arr() as $oid => $odata)
		{
			$o = obj($oid);
			foreach($odata as $k => $v)
			{
				if($o->prop($props[$k]) != $v)
				{
					$ok = false;
					break;
				}
			}
		}
		$this->assertTrue($ok);
	}	

	function _get_temp_o($arr = array())
	{
		// create new object
		$o = obj();
		$o->set_parent(isset($arr["parent"]) ? $arr["parent"] : aw_ini_get("site_rootmenu"));
		$o->set_class_id(isset($arr["class_id"]) ? $arr["class_id"] : CL_MENU);
		$o->name = $arr["name"];
		$o->set_ord($arr["ord"]);
		$o->save();
		// Easier to kill 'em this way afterwards.
		$this->tmp_objs[] = $o;

		return $o;
	}
}

?>
