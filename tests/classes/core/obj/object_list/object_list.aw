<?php

class object_list_test extends UnitTestCase
{/*
	private $oltst_test_data = array(); // test object id-s
	private $oltst_read_tests_performed = 0; // See 'oltst_num_of_read_tests'
	private $oltst_num_of_read_tests = 0; // Needed to perform setUp and cleanup only once for read tests. Automatically set by counting methods with prefix 'test_read_'
	private $oltst_write_tests_performed = 0;
	private $oltst_num_of_write_tests = 0;

	public function __construct($name)
	{
		$this->UnitTestCase($name);

		// count methods starting with 'test_read'
		$class_methods = get_class_methods($this);
		foreach ($class_methods as $name)
		{
			if (0 === strpos($name, "test_read_"))
			{
				++$this->oltst_num_of_read_tests;
			}
			elseif (0 === strpos($name, "test_write_"))
			{
				++$this->oltst_num_of_write_tests;
			}
		}
	}

	public function setUp()
	{
		if (0 === $this->oltst_read_tests_performed and 0 === $this->oltst_write_tests_performed)
		{
			// create test objects
			// 3 classes, 2-deep connections, 10? objs,
			$osi = get_instance("install/object_script_interpreter");
			aw_disable_acl();
			$rv = $osi->exec_file(array(
				"file" => aw_ini_get("basedir") . "/tests/classes/core/obj/object_list/test_data_set1.ojs",
				"vars" => array("parent" => aw_ini_get("site_rootmenu"))
			));
			$this->oltst_test_data = $rv["created_objs"];
		}
	}

	public function tearDown()
	{
		if ($this->oltst_num_of_read_tests === $this->oltst_read_tests_performed and $this->oltst_num_of_write_tests === $this->oltst_write_tests_performed)
		{
			// full delete test objects
			foreach ($this->oltst_test_data as $oid)
			{
				$o = new object($oid);
				$o->delete(true);
			}

			$this->oltst_test_data = array();
		}
	}

	public function test_read_parent()
	{
		++$this->oltst_read_tests_performed;
		$ol = new object_list(array(
			"parent" => $this->oltst_test_data[1]
		));
		$ol = $ol->names();
		$this->assertIdentical($ol[$this->oltst_test_data[2]], "Testobject 2");
		$this->assertIdentical($ol[$this->oltst_test_data[3]], "Testobject 3");
		$this->assertIdentical($ol[$this->oltst_test_data[4]], "Testobject 4");
	}

	public function test_read_oids()
	{
		++$this->oltst_read_tests_performed;
		$oids = array($this->oltst_test_data[0], $this->oltst_test_data[1], $this->oltst_test_data[2]);
		$ol = new object_list(array(
			"oid" => $oids
		));
		$ol = $ol->list_names();
		$ol = array_keys($ol);
		$this->assertIdentical($ol, $oids);
	}

	public function test_read_count()
	{
		++$this->oltst_read_tests_performed;
		$oids = array($this->oltst_test_data[0], $this->oltst_test_data[1], $this->oltst_test_data[2]);
		$ol = new object_list(array(
			"oid" => $oids
		));
		$count = $ol->count();
		$this->assertIdentical($count, 3);

		// empty list count
		$ol = new object_list();
		$count = $ol->count();
		$this->assertIdentical($count, 0);
	}

	public function test_read_add()
	{
		++$this->oltst_read_tests_performed;
		$oids1 = array($this->oltst_test_data[0], $this->oltst_test_data[1]);
		$oids2 = array($this->oltst_test_data[0], $this->oltst_test_data[1], $this->oltst_test_data[2]);
		$ol = new object_list(array(
			"oid" => $oids1
		));
		$ol->add(new object($this->oltst_test_data[2]));
		$ids = array_values($ol->ids());
		$this->assertIdentical($ids, $oids2);
		$this->assertIdentical($ol->count(), 3);
	}

	public function test_read_remove()
	{
		++$this->oltst_read_tests_performed;
		$oids1 = array($this->oltst_test_data[0], $this->oltst_test_data[1], $this->oltst_test_data[2]);
		$oids2 = array($this->oltst_test_data[0], $this->oltst_test_data[2]);
		$ol = new object_list(array(
			"oid" => $oids1
		));
		$ol->remove(new object($this->oltst_test_data[1]));
		$ids = array_values($ol->ids());
		$this->assertIdentical($ids, $oids2);
		$this->assertIdentical($ol->count(), 2);
	}

	public function test_read_remove_all()
	{
		++$this->oltst_read_tests_performed;
		$oids1 = array($this->oltst_test_data[0], $this->oltst_test_data[1], $this->oltst_test_data[2]);
		$ol = new object_list(array(
			"oid" => $oids1
		));
		$ol->remove_all();
		$this->assertIdentical($ol->count(), 0);
	}

	public function test_read_addremove()
	{
		++$this->oltst_read_tests_performed;
		$oids1 = array($this->oltst_test_data[0], $this->oltst_test_data[1], $this->oltst_test_data[2]);
		$ol = new object_list(array(
			"oid" => $oids1
		));

		// remove one
		$ol->remove(new object($this->oltst_test_data[1]));
		$ids = array_values($ol->ids());
		$this->assertIdentical($ids, array($this->oltst_test_data[0], $this->oltst_test_data[2]));
		$this->assertIdentical($ol->count(), 2);

		// add one
		$ol->add(new object($this->oltst_test_data[4]));
		$ids = array_values($ol->ids());
		$this->assertIdentical($ids, array($this->oltst_test_data[0], $this->oltst_test_data[2], $this->oltst_test_data[4]));
		$this->assertIdentical($ol->count(), 3);

		// remove all
		$ol->remove_all();
		$this->assertIdentical($ol->count(), 0);

		// add three
		$ol->add(new object($this->oltst_test_data[1]));
		$ol->add(new object($this->oltst_test_data[2]));
		$ol->add(new object($this->oltst_test_data[3]));
		$ids = array_values($ol->ids());
		$this->assertIdentical($ids, array($this->oltst_test_data[1], $this->oltst_test_data[2], $this->oltst_test_data[3]));
		$this->assertIdentical($ol->count(), 3);

		// remove two
		$ol->remove(new object($this->oltst_test_data[1]));
		$ol->remove(new object($this->oltst_test_data[3]));
		$ids = array_values($ol->ids());
		$this->assertIdentical($ids, array($this->oltst_test_data[2]));
		$this->assertIdentical($ol->count(), 1);
	}

	public function test_read_get_at()
	{
		++$this->oltst_read_tests_performed;
		$oids = array($this->oltst_test_data[0], $this->oltst_test_data[1], $this->oltst_test_data[2]);
		$ol = new object_list(array(
			"oid" => $oids
		));
		$o = $ol->get_at($this->oltst_test_data[1]);
		$ids = array_values($ol->ids());
		$this->assertIdentical($ids, $oids);
		$this->assertIdentical($o->name(), "Testobject 1");
	}

	public function test_read_arr()
	{
		++$this->oltst_read_tests_performed;
		$oids = array($this->oltst_test_data[0], $this->oltst_test_data[1], $this->oltst_test_data[2]);
		$ol = new object_list(array(
			"oid" => $oids
		));
		$ol = $ol->arr();
		$ol_k = array_keys($ol);
		$this->assertIdentical($ol_k, $oids);
		$this->assertIdentical($ol[$this->oltst_test_data[1]]->name(), "Testobject 1");
	}

	public function test_read_names()
	{
		++$this->oltst_read_tests_performed;
		$oids = array($this->oltst_test_data[0], $this->oltst_test_data[1], $this->oltst_test_data[3]);
		$names = array("Testobject 0", "Testobject 1", "Testobject 3");
		$ol = new object_list(array(
			"oid" => $oids
		));
		$ol = $ol->names();
		$ol_k = array_keys($ol);
		$this->assertIdentical($ol_k, $oids);
		$this->assertIdentical(array_values($ol), $names);
	}

	public function test_sort_by()
	{
		++$this->oltst_read_tests_performed;
		$oids = array($this->oltst_test_data[2], $this->oltst_test_data[1], $this->oltst_test_data[0]);
		$ol = new object_list(array(
			"oid" => $oids
		));
		$ol->sort_by(array(
			"prop" => "ord",
			"order" => "desc"
		));
		$correct_names = array("Testobject 1", "Testobject 2", "Testobject 0");
		$ol_names = array_values($ol->names());
		$this->assertIdentical($ol_names, $correct_names);
	}

	public function test_sort_by_multiple()
	{
		++$this->oltst_read_tests_performed;
		$oids = array($this->oltst_test_data[2], $this->oltst_test_data[1], $this->oltst_test_data[3]);
		$ol = new object_list(array(
			"oid" => $oids
		));
		$ol->sort_by(array(
			"prop" => array("ord", "name"),
			"order" => array("asc", "desc")
		));
		$correct_names = array("Testobject 1", "Testobject 3", "Testobject 2");
		$ol_names = array_values($ol->names());
		$this->assertIdentical($ol_names, $correct_names);
	}

	public function test_sort_by_cb()
	{
		++$this->oltst_read_tests_performed;
		$oids = array($this->oltst_test_data[2], $this->oltst_test_data[1], $this->oltst_test_data[0]);
		$ol = new object_list(array(
			"oid" => $oids
		));
		$ol->sort_by_cb(array($this, "tmp_sorter"));
		$correct_names = array("Testobject 1", "Testobject 2", "Testobject 0");
		$ol_names = array_values($ol->names());
		$this->assertIdentical($ol_names, $correct_names);
	}

	public function test_read_begin()
	{
		++$this->oltst_read_tests_performed;
		$oids = array($this->oltst_test_data[2], $this->oltst_test_data[1], $this->oltst_test_data[0]);
		$ol = new object_list(array(
			"oid" => $oids
		));
		$o = $ol->begin();
		$ids = $ol->ids();
		$this->assertIdentical($o->id(), reset($ids));
		$this->assertIdentical($ol->iter_index, 0);
	}

	public function test_read_end()
	{
		++$this->oltst_read_tests_performed;
		$oids = array($this->oltst_test_data[2], $this->oltst_test_data[1], $this->oltst_test_data[0]);
		$ol = new object_list(array(
			"oid" => $oids
		));
		$ol->begin();
		$ol->next();
		$this->assertTrue($ol->end());
		$this->assertIdentical($ol->iter_index, 2);
	}

	public function test_read_next()
	{
		++$this->oltst_read_tests_performed;
		$oids = array($this->oltst_test_data[2], $this->oltst_test_data[1], $this->oltst_test_data[0]);
		$ol = new object_list(array(
			"oid" => $oids
		));
		$ol->begin();
		$o = $ol->next();
		$ids = $ol->ids();
		reset($ids);
		$this->assertIdentical($o->id(), next($ids));
		$this->assertIdentical($ol->iter_index, 2);
	}

	public function test_read_foreach_o()
	{
		++$this->oltst_read_tests_performed;
		$oids = array($this->oltst_test_data[2], $this->oltst_test_data[1], $this->oltst_test_data[0]);
		$ol = new object_list(array(
			"oid" => $oids
		));
		$ol->foreach_o(array(
			"func" => "set_name",
			"params" => "test",
			"save" => false
		));
		$correct_names = array("test", "test", "test");
		$ol_names = array_values($ol->names());
		$this->assertIdentical($ol_names, $correct_names);
	}

	public function test_read_foreach_cb()
	{
		++$this->oltst_read_tests_performed;
		$oids = array($this->oltst_test_data[2], $this->oltst_test_data[1], $this->oltst_test_data[0]);
		$ol = new object_list(array(
			"oid" => $oids
		));
		$ol->foreach_cb(array(
			"func" => "tmp_foreach_cb",
			"param" => "test",
			"save" => false
		));
		$correct_names = array("test", "test", "test");
		$ol_names = array_values($ol->names());
		$this->assertIdentical($ol_names, $correct_names);
	}

	public function test_read_filter_prop()
	{
		++$this->oltst_read_tests_performed;
		$ol = new object_list(array(
			"class_id" => CL_DOCUMENT,
			"title" => "82djnslsamz.b[;dcwvkw# ksadfkoefefe28fa;gj92E@$aDdDAFakeufa"
		));
		$o = $ol->begin();
		$this->assertIdentical($this->oltst_test_data[4], $o->id());
	}

	public function test_read_filter_prop_substr()
	{
		++$this->oltst_read_tests_performed;
		$oids = array($this->oltst_test_data[4], $this->oltst_test_data[7], $this->oltst_test_data[8]);
		$ol = new object_list(array(
			"oid" => $oids,
			"subtitle" => "testsubtitle%"
		));
		$ids = array_values($ol->ids());
		$this->assertIdentical($ol->count(), 2);
		$this->assertTrue(in_array($this->oltst_test_data[8], $ids));
		$this->assertTrue(in_array($this->oltst_test_data[7], $ids));
	}

	public function test_read_filter_prop_not()
	{
		++$this->oltst_read_tests_performed;
		$oids = array($this->oltst_test_data[4], $this->oltst_test_data[7], $this->oltst_test_data[8]);
		$ol = new object_list(array(
			"oid" => $oids,
			"subtitle" => new obj_predicate_not ("testsubtitle2")
		));
		$ids = array_values($ol->ids());
		$this->assertIdentical($ol->count(), 2);
		$this->assertTrue(in_array($this->oltst_test_data[4], $ids));
		$this->assertTrue(in_array($this->oltst_test_data[7], $ids));
	}

	public function test_read_filter_prop_compare_null()
	{
		++$this->oltst_read_tests_performed;
		$oids = array($this->oltst_test_data[2], $this->oltst_test_data[3], $this->oltst_test_data[0]);
		$ol = new object_list(array(
			"oid" => $oids,
			"comment" => obj_predicate_compare(OBJ_COMP_NULL)
		));
		$o = $ol->begin();
		$this->assertIdentical($ol->count(), 1);
		$this->assertIdentical($this->oltst_test_data[3], $o->id());
	}

	public function test_read_filter_prop_compare_greater()
	{
		++$this->oltst_read_tests_performed;
		$oids = array($this->oltst_test_data[2], $this->oltst_test_data[3], $this->oltst_test_data[0]);
		$ol = new object_list(array(
			"oid" => $oids,
			"ord" => new obj_predicate_compare (OBJ_COMP_GREATER, 2)
		));
		$o = $ol->begin();
		$this->assertIdentical($ol->count(), 1);
		$this->assertIdentical($this->oltst_test_data[3], $o->id());
	}

	public function test_read_filter_prop_compare_less()
	{
		++$this->oltst_read_tests_performed;
		$oids = array($this->oltst_test_data[1], $this->oltst_test_data[2], $this->oltst_test_data[3], $this->oltst_test_data[0]);
		$ol = new object_list(array(
			"oid" => $oids,
			"ord" => new obj_predicate_compare (OBJ_COMP_LESS, 2)
		));
		$o = $ol->begin();
		$this->assertIdentical($ol->count(), 1);
		$this->assertIdentical($this->oltst_test_data[1], $o->id());
	}

	public function test_read_filter_prop_compare_greater_eq()
	{
		++$this->oltst_read_tests_performed;
		$oids = array($this->oltst_test_data[2], $this->oltst_test_data[0], $this->oltst_test_data[1]);
		$ol = new object_list(array(
			"oid" => $oids,
			"ord" => new obj_predicate_compare (OBJ_COMP_GREATER_OR_EQ, 2)
		));
		$ids = array_values($ol->ids());
		$this->assertIdentical($ol->count(), 2);
		$this->assertTrue(in_array($this->oltst_test_data[0], $ids));
		$this->assertTrue(in_array($this->oltst_test_data[2], $ids));
	}

	public function test_read_filter_prop_compare_less_eq()
	{
		++$this->oltst_read_tests_performed;
		$oids = array($this->oltst_test_data[1], $this->oltst_test_data[2], $this->oltst_test_data[3], $this->oltst_test_data[0]);
		$ol = new object_list(array(
			"oid" => $oids,
			"ord" => new obj_predicate_compare (OBJ_COMP_LESS_OR_EQ, 2)
		));
		$ids = array_values($ol->ids());
		$this->assertIdentical($ol->count(), 3);
		$this->assertTrue(in_array($this->oltst_test_data[1], $ids));
		$this->assertTrue(in_array($this->oltst_test_data[2], $ids));
		$this->assertTrue(in_array($this->oltst_test_data[3], $ids));
	}

	public function test_read_filter_prop_compare_between()
	{
		++$this->oltst_read_tests_performed;
		$oids = array($this->oltst_test_data[1], $this->oltst_test_data[2], $this->oltst_test_data[3], $this->oltst_test_data[0]);
		$ol = new object_list(array(
			"oid" => $oids,
			"ord" => new obj_predicate_compare (OBJ_COMP_BETWEEN, 1, 3)
		));
		$ids = array_values($ol->ids());
		$this->assertIdentical($ol->count(), 2);
		$this->assertTrue(in_array($this->oltst_test_data[2], $ids));
		$this->assertTrue(in_array($this->oltst_test_data[3], $ids));
	}

	public function test_read_filter_prop_compare_between_inc()
	{
		++$this->oltst_read_tests_performed;
		$oids = array($this->oltst_test_data[1], $this->oltst_test_data[2], $this->oltst_test_data[3], $this->oltst_test_data[0]);
		$ol = new object_list(array(
			"oid" => $oids,
			"ord" => new obj_predicate_compare (OBJ_COMP_BETWEEN_INCLUDING, 2, 3)
		));
		$ids = array_values($ol->ids());
		$this->assertIdentical($ol->count(), 3);
		$this->assertTrue(in_array($this->oltst_test_data[0], $ids));
		$this->assertTrue(in_array($this->oltst_test_data[2], $ids));
		$this->assertTrue(in_array($this->oltst_test_data[3], $ids));
	}

	public function test_read_filter_prop_compare_span()
	{//!!! pooleli
		++$this->oltst_read_tests_performed;
		$oids = array($this->oltst_test_data[1], $this->oltst_test_data[2], $this->oltst_test_data[3], $this->oltst_test_data[0]);//////!!!
		$ol = new object_list(array(
			"oid" => $oids,
			"ord" => new obj_predicate_compare (OBJ_COMP_IN_TIMESPAN, array("start", "end"), array(2, 3))
		));
		$ids = array_values($ol->ids());
		$this->assertIdentical($ol->count(), 3);
		$this->assertTrue(in_array($this->oltst_test_data[0], $ids));
		$this->assertTrue(in_array($this->oltst_test_data[2], $ids));
		$this->assertTrue(in_array($this->oltst_test_data[3], $ids));
	}

	public function test_read_filter_clid_array_param()
	{
		++$this->oltst_read_tests_performed;
		$oids = array($this->oltst_test_data[2], $this->oltst_test_data[4], $this->oltst_test_data[5]);
		$ol = new object_list(array(
			"oid" => $oids,
			"class_id" => array(CL_MENU, CL_IMAGE)
		));
		$ids = array_values($ol->ids());
		$this->assertIdentical($ol->count(), 2);
		$this->assertTrue(in_array($this->oltst_test_data[2], $ids));
		$this->assertTrue(in_array($this->oltst_test_data[5], $ids));
	}

	public function test_read_filter_prop_array_param()
	{
		++$this->oltst_read_tests_performed;
		$ol = new object_list(array(
			"parent" => array($this->oltst_test_data[2], $this->oltst_test_data[3]),
			"class_id" => CL_MENU,
			"subtitle" => array("testsubtitle", "testsubtitle2")
		));
		$ids = array_values($ol->ids());
		$this->assertIdentical($ol->count(), 3);
		$this->assertTrue(in_array($this->oltst_test_data[6], $ids));
		$this->assertTrue(in_array($this->oltst_test_data[7], $ids));
		$this->assertTrue(in_array($this->oltst_test_data[8], $ids));
	}

	public function test_read_filter_ol_from_connlist()
	{
		++$this->oltst_read_tests_performed;
		$o = new object($this->oltst_test_data[2]);
		$ol = new object_list ($o->connections_from(array(
			"type" => 5
		)));
		$ids = array_values($ol->ids());
		$this->assertTrue(in_array($this->oltst_test_data[1], $ids));
		$this->assertTrue(in_array($this->oltst_test_data[3], $ids));
		$this->assertIdentical($ol->count(), 2);
		$this->assertIdentical(count($ids), 2);
	}

	public function test_read_filter_conn_prop()
	{
		++$this->oltst_read_tests_performed;
		$oids = array($this->oltst_test_data[1], $this->oltst_test_data[2], $this->oltst_test_data[3]);
		$ol = new object_list(array(
			"oid" => $oids,
			"class_id" => CL_MENU,
			"CL_MENU.RELTYPE_IMAGE.name" => "Testobject 5"
		));
		$ids = array_values($ol->ids());
		$this->assertIdentical($ol->count(), 1);
		$this->assertTrue(in_array($this->oltst_test_data[1], $ids));
	}

	public function test_write_objfield()
	{
		++$this->oltst_write_tests_performed;
		$oids = array($this->oltst_test_data[10], $this->oltst_test_data[11]);
		$ol = new object_list(array(
			"oid" => $oids
		));
		$ol->set_name("newnamewritten");
		$ol->save();
		$ol = new object_list(array(
			"oid" => $oids
		));
		foreach ($ol->arr() as $o)
		{
			$this->assertIdentical($o->name(), "newnamewritten");
		}
	}

	public function test_write_prop()
	{
		++$this->oltst_write_tests_performed;
		$oids = array($this->oltst_test_data[10], $this->oltst_test_data[11]);
		$ol = new object_list(array(
			"oid" => $oids
		));
		$ol->set_prop("link", "newpropvalue");
		$ol->save();
		$ol = new object_list(array(
			"oid" => $oids
		));
		foreach ($ol->arr() as $o)
		{
			$this->assertIdentical($o->prop("link"), "newpropvalue");
		}
	}

	/* helper methods used by tests */
	public function tmp_sorter($a, $b)
	{
		return $a->ord() === $b->ord() ? 0 :  ($a->ord() < $b->ord() ? -1 : 1);
	}

	public function tmp_foreach_cb(&$o, $param)
	{
		$o->set_name($param);
	}
	/* END helper methods used by tests */
}

?>
