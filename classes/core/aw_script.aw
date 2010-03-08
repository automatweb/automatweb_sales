<?php
// $Header: /home/cvs/automatweb_dev/classes/core/aw_script.aw,v 1.3 2008/01/31 13:52:49 kristo Exp $
// aw_script.aw - AW skript 
/*

@classinfo syslog_type=ST_AW_SCRIPT relationmgr=yes maintainer=kristo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property script type=chooser orient=vertical
@caption Vali skript

@property configure_script type=callback callback=callback_configure_script group=configure
@caption Seadistused

@property run_script type=checkbox group=configure store=no
@caption Käivita skript

@groupinfo configure caption="Seadistused"

// can be used for configuring scripts
@reltype CONFIG_OPTION value=1 clid=CL_MENU
@caption Seadistus

*/

class aw_script extends class_base
{
	function aw_script()
	{
		$this->init(array(
			"clid" => CL_AW_SCRIPT
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "script":
				$prop["options"] = array(0 => "--vali--");
				$files = $this->get_directory(array(
					"dir" => aw_ini_get("basedir") . "/scripts/create_scripts",
				));
				foreach($files as $file)
				{
					$prop["options"][basename($file,".aw")] = $file;
				};
				// XX: if the script changes, then reset script configuration data. or not?
				break;

		};
		return $retval;
	}

	function callback_configure_script($arr)
	{
		$scr = $arr["obj_inst"]->prop("script");

		$filter = array("form" => "configure");
		$cfgu = get_instance("cfg/cfgutils");

		$_all_props = $cfgu->load_properties(array(
			"file" => $scr,
			"filter" => $filter,
		));

		$oldconfig = $arr["obj_inst"]->meta("config");

		$res = array();
		foreach($_all_props as $key => $val)
		{
			$tmp = $val;
			$tmp["name"] = "config[" . $val["name"] . "]";
			if (isset($oldconfig[$val["name"]]))
			{
				$tmp["value"] = $oldconfig[$val["name"]];
			};
			$res[$key] = $tmp;
		};
		// well, bloody hell, this is almost going to be TOO easy

		return $res;

	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "configure_script":
				$arr["obj_inst"]->set_meta("config",$arr["request"]["config"]);
				break;

			case "run_script":
				if (1 == $arr["request"]["run_script"])
				{
					// do some variable initialization
					foreach($arr["request"]["config"] as $key => $val)
					{
						$$key = $val;
					};
					$sname = aw_ini_get("basedir") . "/scripts/create_scripts/" . $arr["obj_inst"]->prop("script") . ".aw";
					include($sname);
				};
				break;

		}
		return $retval;
	}	

	function run_script_run($arr)
	{
		$script_obj = new object($arr["id"]);
		if (is_array($arr["vars"]))
		{
			foreach($arr["vars"] as $name => $value)
			{
				$$name = $value;
			};

		};
		$config = $script_obj->meta("config");
		if (is_array($config))
		{
			foreach($config as $name => $value)
			{
				$$name = $value;
			};
		};
		$sname = aw_ini_get("basedir") . "/scripts/create_scripts/" . $script_obj->prop("script") . ".aw";
		if (file_exists($sname))
		{
			include($sname);
		}
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	////
	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	////
	// !this shows the object. not strictly necessary, but you'll probably need it, it is used by parse_alias
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}
}
?>
