<?php

class cfgutils_test extends UnitTestCase
{
	function cfgutils_test($name)
	{
		$this->UnitTestCase($name);
		$this->cfgu = get_instance("cfg/cfgutils");
		$this->props = $this->cfgu->load_properties(array("clid" => CL_PROPTEST));
	}

	function setUp()
	{
	}

	function test_properties_exist()
	{
		// if it's array, then properties have probably been loaded
		$this->assertIsA($this->props,"array");
		$this->assertTrue(sizeof($this->props) > 0);
	}

	function test_props_derived_from_classbase()
	{
		$this->assertIsA($this->props["name"],"array");
		$this->assertIsA($this->props["comment"],"array");
		$this->assertIsA($this->props["status"],"array");
		//arr($this->props);
	}

	function test_textbox1_exists()
	{
		$this->assertIsA($this->props["textbox1"],"array");
		$this->assertEqual("textbox",$this->props["textbox1"]["type"]);
	}
	
	function test_img1_releditor_use_form()
	{
		$this->assertIsA($this->props["img1"],"array");
		$this->assertEqual("releditor",$this->props["img1"]["type"]);
		$this->assertEqual("RELTYPE_IMAGE",$this->props["img1"]["reltype"]);
		$this->assertEqual("emb",$this->props["img1"]["use_form"]);
	}
	
	function test_img2_releditor_props()
	{
		$this->assertIsA($this->props["img2"],"array");
		$this->assertEqual("releditor",$this->props["img2"]["type"]);
		$this->assertEqual("RELTYPE_IMAGE",$this->props["img2"]["reltype"]);
		$this->assertEqual(2,sizeof($this->props["img2"]["props"]));
		$this->assertEqual("file",$this->props["img2"]["props"][0]);
		$this->assertEqual("comment",$this->props["img2"]["props"][1]);
	}

	function test_groupinfo_exists()
	{
		$groups = $this->cfgu->get_groupinfo();
		$this->assertIsA($groups,"array");
		$this->assertTrue(sizeof($groups) > 0);
	}
	
	function test_groupinfo_general_exists()
	{
		$groups = $this->cfgu->get_groupinfo();
		$this->assertIsA($groups["general"],"array");
	}
	
	function test_groupinfo_parent1_exists()
	{
		$groups = $this->cfgu->get_groupinfo();
		$this->assertIsA($groups["parentgroup1"],"array");
	}
	
	function test_groupinfo_parent1_quoted_caption()
	{
		$groups = $this->cfgu->get_groupinfo();
		$this->assertEqual("Parent Group 1",$groups["parentgroup1"]["caption"]);
	}
	
	function test_groupinfo_childgroup1()
	{
		$groups = $this->cfgu->get_groupinfo();
		$this->assertEqual($groups["childgroup1"]["parent"],"parentgroup1");
		$this->assertEqual($groups["childgroup1"]["submit"],"no");
	}
	
	function test_groupinfo_childgroup2()
	{
		$groups = $this->cfgu->get_groupinfo();
		$this->assertEqual($groups["childgroup2"]["parent"],"parentgroup1");
		$this->assertEqual($groups["childgroup2"]["submit_method"],"get");
	}
	
	function test_classinfo_exists()
	{
		$clinf = $this->cfgu->get_classinfo();
		$this->assertIsA($clinf,"array");
		$this->assertTrue(sizeof($clinf) > 0);
	}
	
	function test_relinfo_exists()
	{
		$relinfo = $this->cfgu->get_relinfo();
		$this->assertIsA($relinfo,"array");
		$this->assertTrue(sizeof($relinfo) > 0);
	}
	
	function test_reltype_menu()
	{
		$relinfo = $this->cfgu->get_relinfo();
		$menu = $relinfo["RELTYPE_MENU"];
		$this->assertIsA($menu,"array");
		$this->assertTrue(sizeof($menu) > 0);
		$this->assertEqual(1,$menu["value"]);
		$this->assertEqual("Link to menu",$menu["caption"]);
		$this->assertIsA($menu["clid"],"array");
		$this->assertEqual(1,sizeof($menu["clid"]));
		$this->assertEqual(CL_MENU,$menu["clid"][0]);
	}
	
	function test_reltype_multi()
	{
		$relinfo = $this->cfgu->get_relinfo();
		$menu = $relinfo["RELTYPE_MULTI"];
		$this->assertIsA($menu,"array");
		$this->assertTrue(sizeof($menu) > 0);
		$this->assertEqual(2,$menu["value"]);
		$this->assertEqual("Link with 2 clids",$menu["caption"]);
		$this->assertIsA($menu["clid"],"array");
		$this->assertEqual(2,sizeof($menu["clid"]));
		$this->assertEqual(CL_MENU,$menu["clid"][0]);
		$this->assertEqual(CL_IMAGE,$menu["clid"][1]);
	}
	
	function test_tableinfo_exists()
	{
		$tableinfo = $this->cfgu->get_tableinfo();
		$this->assertIsA($tableinfo,"array");
		$this->assertTrue(sizeof($tableinfo) > 0);
	}
	
	function test_tableinfo_proptest()
	{
		$tableinfo = $this->cfgu->get_tableinfo();
		$proptest = $tableinfo["proptest"];
		$this->assertIsA($proptest,"array");
		$this->assertEqual(3,sizeof($proptest));
		$this->assertEqual("aw_id",$proptest["index"]);
		$this->assertEqual("objects",$proptest["master_table"]);
		$this->assertEqual("brother_of",$proptest["master_index"]);
	}

}

?>
