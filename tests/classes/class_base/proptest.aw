<?php
// change does a lot of work and I want to write tests for the resulting data structures,
// there is no point in doing the same work over and over again in setUp

// any ideas on how do it this in OO Style? TestDecorator?
// tests output of proptest class
class proptest_test extends UnitTestCase
{
	var $cb;
	function proptest_test()
	{
		$this->UnitTestCase("Classbase testid");
/*		$this->cb = get_instance("cfg/proptest");
		$this->cb->change(array(
			"id" => 1,
			"cbcli" => "debugclient",
		));*/
	}
/*
	function setUp()
	{
		$this->testclass = "proptest";
		$this->id = 1;
	}

	function test_reforb_classname()
	{
		// class should be the same that was requested
		$this->assertEqual($this->testclass,$this->cb->cli->formdata["class"]);
	}
	
	function test_reforb_action()
	{
		$this->assertEqual("submit",$this->cb->cli->formdata["action"]);
	}
	
	function test_reforb_group()
	{
		$this->assertEqual("general",$this->cb->cli->formdata["group"]);
	}
	
	function test_reforb_id()
	{
		$this->assertEqual($this->id,$this->cb->cli->formdata["id"]);
	}
	
	function test_tab_general()
	{
		$general = $this->cb->cli->tabs["general"];
		$this->assertIsA($general,"array","General tab does note exist");
		$this->assertEqual(true,$general["active"],"General tab is not active");
		$this->assertEqual(1,$general["level"],"General tab is not on first level");
		$this->assertTrue(empty($general["parent"]),"General tab has a parent, but shouldn't");
		$this->assertFalse(empty($general["caption"]),"General tab has no caption");
	}
	
	function test_tab_empty()
	{
		$gr = $this->cb->cli->tabs["empty_group"];
		$this->assertNull($this->cb->cli->tabs["empty_group"]);
	}
	
	function test_callback_mod_tab_hide()
	{
		$this->assertNull($this->cb->cli->tabs["hidden_by_mod_tab"],"hidden_my_mod_tab should not reach output client, because it was explicitly hidden in callback_mod_tab");
	}
	
	function test_list_aliases()
	{
		$this->assertIsA($this->cb->cli->tabs["list_aliases"],"array");
	}

	function test_focus_el()
	{
		$this->assertEqual("name",$this->cb->cli->focus_el);
	}
	
	function test_submit_method()
	{
		$this->assertEqual("POST",$this->cb->cli->formdata["method"]);
	}

	function test_prop_ignore()
	{
		$this->assertNull($this->cb->cli->proplist["get_property_prop_ignore"],
						"get_property_prop_ignore should not reach output client.");
	}
	
	function test_prop_error()
	{
		$err = $this->cb->cli->proplist["get_property_prop_error"];
		$this->assertIsA($err,"array");
		$this->assertEqual("text",$err["type"]);
		$this->assertNotNull($err["error"]);
	}

	function test_prop_calback()
	{
		$props = &$this->cb->cli->proplist;
		$this->assertIsA($props["cb[1]"],"array","One of the properties defined in callback is not there");
		$this->assertIsA($props["cb[2]"],"array","One of the properties defined in callback is not there");
		$this->assertNull($props["cb"],"Property with type=callback should have been replaced with properties generated in the correspondening method");

	}
	
	function test_callback_on_load()
	{
		$this->assertTrue($this->cb->inst->on_load_called);
		
	}
	
	function test_callback_pre_edit()
	{
		$this->assertTrue($this->cb->inst->pre_edit_called);
	}
	
	function test_callback_mod_reforb()
	{
		$this->assertTrue($this->cb->inst->mod_reforb_called);
		$this->assertEqual("works",$this->cb->cli->formdata["data"]["added_by_mod_reforb"]);
		
	}
*/
}

?>
