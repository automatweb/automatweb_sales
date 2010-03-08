<?php
define("TEST_PASS",0);
define("TEST_FAIL",1);
/*
@classinfo  maintainer=kristo
*/

class testrunner extends core
{
	function testrunner()
	{
		$this->init();
		$this->passcount = 0;
		$this->failcount = 0;
		$this->tests = array();
	}

	function setup()
	{
		//print "running setup!";


	}

	function teardown()
	{
		//print "running teardown!";

	}

	/**
		@attrib name=test default=1
		@param script required
		@param format optional
	**/
	function test($arr)
	{
		$script = $arr["script"];
		include_once($script . ".aw");

		$inst = new $script();

		$methods = get_class_methods($inst);

		$inst->setup();
		$key = 0;
		$method_inf = $res["classes"][$script]["functions"];
		foreach($methods as $method)
		{
			if (substr($method,0,5) == "test_")
			{
				$test_args = array();
				$this->testkey = $key;
				$this->tests[$key]["name"] = $method;
				$this->tests[$key]["description"] = $method_inf[$method]["doc_comment"]["short_comment"];
				$result = TEST_PASS;
				$res = $inst->$method($test_args);
				if ($res == TEST_FAIL)
				{
					$this->tests[$key]["result"] = "FAIL";
				}
				else
				{
					$this->tests[$key]["result"] = "PASS";
				};
				$key++;
			};
		};
		$inst->teardown();


		switch($arr["format"])
		{
			case "xml":
				header("Content-Type: text/xml");
				die($this->return_xml());

			default:
				return $this->return_html();
		};
	}

	function return_xml()
	{
		$arr = array("tests" => $this->tests);
		return aw_serialize($arr,SERIALIZE_XML,array(
			"ctag" => "",
			"child_id" => array("tests" => "test"),
		));
	}

	function return_html()
	{
		$awt = get_instance("aw_template");
		$awt->tpl_init("applications/unittest");
		$awt->read_template("test_results.tpl");
		$awt->sub_merge = 1;
		$testcount = $failcount = $passcount = 0;
		foreach($this->tests as $test)
		{
			$testcount++;
			if ($test["result"] == "PASS")
			{
				$passcount++;
			}
			else
			{	
				$failcount++;
			};
			$awt->vars($test);
			$awt->parse("TEST");
		};
		$awt->vars(array(
			"test_count" => $testcount . " - 100%",
			"passed" => $passcount . " - " . $passcount * 100 / $testcount . "%",
			"failed" => $failcount . " - " . $failcount * 100 / $testcount . "%",
		));
		return $awt->parse();
	}

	function start_test($arr)
	{


	}

	function fail_test($arr = array())
	{
		return TEST_FAIL;

	}

	function assert_test($expr,$msg)
	{
		if (!$expr)
		{
			return TEST_FAIL;
		};

	}

	function return_status()
	{


	}
}
?>
