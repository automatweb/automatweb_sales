<?php
if ($argc < 2)
{
	exit("Usage:\n\tphp ${argv[0]} /path/to/site/aw.ini [folder folder folder to run tests in]\n\n");
}

$nl = "<br>\n";
if(!$autotest)
{
	chdir("..");
	$nl = "\n";
}

$site_dir = str_replace("/aw.ini", "", $argv[1]);
$aw_dir = getcwd();//"/www/dev/autotest/automatweb_dev";
$basedir =$aw_dir;// realpath("..")."/automatweb_dev";
$cache_file = $site_dir . "/pagecache/ini.cache";
$cfg_files = array($site_dir . "/aw.ini");

automatweb::start();
automatweb::$instance->bc();
automatweb::$instance->load_config_files($cfg_files, $cache_file);
$request = aw_request::autoload();
automatweb::$instance->set_request($request);
automatweb::$instance->exec();
automatweb::$result->send();
automatweb::shutdown();

chdir("classes");

if($do_test || !$autotest)
{
	if($autotest) //automaatse testi puhul peaks inimesi sp2mmima hakkama
	{
		manage_failed_data($_GET);
	}

	require_once('simpletest/unit_tester.php');
	require_once('simpletest/reporter.php');

	$awt = new aw_timer;

	//ob_start();

	if ($argc < 3)
	{
		$argc = 3;
		$argv[2] = "classes";
	}
	for($i = 2; $i < $argc; $i++)
	{
		$path = $aw_dir."/tests/".$argv[$i];//"/www/dev/autotest/automatweb_dev/tests/".$argv[$i];

		echo "running tests in ".$argv[$i]."... \n\n<br><br>";

		if (is_file($path))
		{
			$files[] = $path;
		}
		else
		{
			// get files from folder
			$p = get_instance("core/aw_code_analyzer/parser");
			$files = array();
			$p->_get_class_list($files, $path);
		}
		$suite = &new GroupTest("All tests");

		foreach($files as $filename)
		{
		//	if(substr_count($filename, "init.aw") > 0) continue;
			//$suite = &new GroupTest(basename($filename,".aw")."_test");
			$suite->addTestFile($filename);
			//$suite->run(new TextReporter());
			//$suite->run(new TextReporter());
		}
		$suite->run(new TextReporter());
	}
//	$log["data"] = str_replace("\n" , "" , ob_get_contents());
	echo "\n<br><br>";
}
elseif($_GET["test"])
{
	$log_array = _get_log($site_dir);
	foreach($log_array as $log)
	{
		if(is_array(unserialize($log)))
		{
			$val = unserialize($log);
			if($val["time"] == $_GET["test"])
			{
				$autotest_content.= "Tested : ".date("d.m.Y H:i" , $val["time"]);
				$autotest_content.= "<br>result : <br>";

					classload("vcl/table");
				//	get_instance("vcl/table");
				$t = new vcl_table(array(
					"layout" => "generic",
				));
				$t->define_field(array(
					"name" => "case",
					"caption" => t("Test name"),
				));
				$t->define_field(array(
					"name" => "result",
					"caption" => t(""),
				));
				foreach($val["stuff"]["case"] as $key => $val)
				{
				  $t->define_data(array("case" => $key , "result" => $val));
				}
				$autotest_content.= $t->draw();
				$autotest_content.= $stuff["conc"]."<br>";
//				print $val["data"];
			}
		}
	}
	$autotest_content.= html::href(array("caption" => "<<<<<<<<<<<<<" , "url" => $GLOBALS["_SERVER"]["SCRIPT_URI"]))."<br>";
}
else
{
	$autotest_content.=  "Show ";
	$asd = 2;
	$asds = array();
	while($asd < 2000001)
	{
		$asds[] = html::href(array("caption" => $asd , "url" => "?show=".$asd));
		$asd *= 2;
	}
	$autotest_content.= join (", " , $asds);
	$autotest_content.=  " results" ;

	$t = new vcl_table(array(
		"layout" => "generic",
	));
	$t->define_field(array(
		"name" => "time",
		"caption" => t("Time"),
	));

	$t->define_field(array(
		"name" => "file",
		"caption" => t("Files"),
	));

	$t->define_field(array(
		"name" => "email",
		"caption" => t("e-mail"),
	));

	$t->define_field(array(
		"name" => "run",
		"caption" => t("Cases run"),
	));
	$t->define_field(array(
		"name" => "pass",
		"caption" => t("Passes"),
	));
	$t->define_field(array(
		"name" => "fail",
		"caption" => t("Failures"),
	));
	$t->define_field(array(
		"name" => "exc",
		"caption" => t("Exceptions"),
	));

	$log_array = _get_log($site_dir);
	$log_data = array();
	$done = array();
	$count = sizeof($log_array);
	$show = 5;
	if($_GET["show"])
	{
		$show = $_GET["show"];
	}

	foreach($log_array as $log)
	{
		if(is_array(unserialize($log)) && $show >= $count-1)
		{
			$val = unserialize($log);
			$color = "white";
			if($val["fail"]) $color = "red";
			else $color = "green";
			$t->define_data(array(
			        "run" => "<font color=".$color.">".$val["tested"]."</br>",
				"email" => "<font color=".$color.">".$val["email"]."</br>",
				"file" => "<font color=".$color.">".$val["file"]."</br>",
				"pass" => "<font color=".$color.">".$val["passed"]."</br>",
				"fail" => "<font color=".$color.">".$val["fail"]."</br>",
				"exc" => "<font color=".$color.">".$val["exc"]."</br>",
				"time" => "<a href='?test=".$val["time"]."'><font color=".$color.">".date("d.m.Y H:i" , $val["time"])."</br></a>",
			));
		}
		$show++;
	}

	$autotest_content.= $t->draw().'<br>';

	$myFile = "/www/dev/autotest/test_list.txt";
	if(!filesize($myFile))
	{
		$autotest_content.= "\n<br>test_list.txt file empty\n<br>";
	}
	else
	{
		$fh = fopen($myFile, 'r');
		$theData = fread($fh, filesize($myFile));
		fclose($fh);
		$log_array = explode("\n" , $theData);
		if(is_array($log_array) && sizeof($log_array))
		{

			$t = new vcl_table(array(
				"layout" => "generic",
			));
			$t->set_header("Waiting....");
			$t->define_field(array(
				"name" => "time",
				"caption" => t("Time"),
			));

			$t->define_field(array(
				"name" => "file",
				"caption" => t("Files"),
			));

			$t->define_field(array(
				"name" => "email",
				"caption" => t("e-mail"),
			));

			$files = array();
			foreach($log_array as $id => $log)
			{
				if(is_array(unserialize($log)))
				{
					$val = unserialize($log);
					$color = "black";
					$t->define_data(array(
						"email" => "<font color=".$color.">".$val["email"]."</br>",
						"file" => "<font color=".$color.">".$val["file"]."</br>",
						"time" => "<font color=".$color.">".date("d.m.Y H:i" , $val["time"])."</br>",
					));
				}
			}
		}
		$autotest_content.= $t->draw();
	}
}

function __disable_err()
{
	aw_global_set("__from_raise_error", 1);
}

function __is_err()
{
	aw_global_set("__from_raise_error", 0);
	if ($GLOBALS["aw_is_error"] == 1)
	{
		$GLOBALS["aw_is_error"] = 0;
		return true;
	}
	return false;
}
