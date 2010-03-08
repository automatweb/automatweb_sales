<?php

class mk_orb_test extends UnitTestCase
{
	var $c;
	var $classname;
	var $baseurl;
	function mk_orb_test($name)
	{
		 $this->UnitTestCase("mk_my_orb test");
		// XXX: Would like to test core directly, but this->cfg gets initialized in
		// aw_template->tpl_init() and mk_my_orb needs baseurl from this-cfg
		classload("aw_template");
		$this->c = new aw_template();
		$this->c->init();
		$this->classname = get_class($this->c);
		$this->baseurl = aw_ini_get("baseurl");
	}

	function setUp()
	{
		// XXX: Would like to test core directly, but this->cfg gets initialized in
		// aw_template->tpl_init() and mk_my_orb needs baseurl from this-cfg
		/*
		classload("aw_template");
		$this->c = new aw_template();
		$this->c->init();
		$this->classname = get_class($this->c);
		$this->baseurl = aw_ini_get("baseurl");
		*/
	}

	function test_action()
	{
		$url = $this->c->mk_my_orb("change",array("id" => 3));
		$this->assertPattern("/action=change/",$url);
	}
	
	function test_empty_clid()
	{
		$url = $this->c->mk_my_orb("change",array("id" => 3));
		$this->assertPattern("/class=" . $this->classname . "/",$url);
	}
	
	function test_textual_clid()
	{
		$url = $this->c->mk_my_orb("change",array("id" => 3),"menu");
		$this->assertPattern("/class=menu/",$url);
	}
	
	function test_directory_stripped()
	{
		// orb does not allow slashes in class names for security reasons and mk_my_orb
		// always creates "flat" links (without the directory described in classes.ini)
		$url = $this->c->mk_my_orb("change",array("id" => 3),CL_FILE);
		$this->assertPattern("/class=file/",$url);
	}
	
	function test_numeric_clid()
	{
		$url = $this->c->mk_my_orb("change",array("id" => 3),CL_MENU);
		$this->assertPattern("/class=menu/",$url);
	}
	
	function test_arg_single()
	{
		$url = $this->c->mk_my_orb("change",array("id" => 3));
		$shouldbe = $this->baseurl . "/?class=" . $this->classname . "&action=change&id=3";
		$this->assertEqual($shouldbe,$url);
	}
	
	function test_empty_arg()
	{
		// mk_my_orb should skip empty arguments
		$url = $this->c->mk_my_orb("change",array("id" => ""));
		$shouldbe = $this->baseurl . "/?class=" . $this->classname . "&action=change";
		$this->assertEqual($shouldbe,$url);
	}
	
	function test_arr_arg()
	{
		// mk_my_orb should skip empty arguments
		$args = array("first" => 1, "second" => 2);
		$url = $this->c->mk_my_orb("change",array($args));
		$shouldbe = $this->baseurl . "/?class=" . $this->classname . "&action=change&first=1&second=2";
		$this->assertEqual($shouldbe,$url);
	}
	
	function test_no_args()
	{
		$url = $this->c->mk_my_orb("test");
		$shouldbe = $this->baseurl . "/?class=" . $this->classname . "&action=test";
		$this->assertEqual($shouldbe,$url);
	}
	
	function test_force_admin()
	{
		$url = $this->c->mk_my_orb("test",array(),"",true);
		$shouldbe = $this->baseurl . "/automatweb/orb.aw?class=" . $this->classname . "&action=test";
		$this->assertEqual($shouldbe,$url);
	}
	
	function test_use_orb()
	{
		$url = $this->c->mk_my_orb("test",array(),"",false,true);
		$shouldbe = $this->baseurl . "/orb.aw?class=" . $this->classname . "&action=test";
		$this->assertEqual($shouldbe,$url);
	}
	
	function test_defaults()
	{
		// default values for force_admin and use_orb
		$url = $this->c->mk_my_orb("test",array());
		$shouldbe = $this->baseurl . "/?class=" . $this->classname . "&action=test";
		$this->assertEqual($shouldbe,$url);
	}
	
	function test_return_url()
	{
		// default values for force_admin and use_orb
		$url = $this->c->mk_my_orb("test",array("return_url" => "blah"));
		$this->assertPattern("/return_url=blah/",$url);
	}

}

?>
