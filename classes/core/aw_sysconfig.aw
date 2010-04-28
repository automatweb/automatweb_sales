<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/core/aw_sysconfig.aw,v 1.4 2008/01/31 13:52:49 kristo Exp $
// aw_sysconfig.aw - AW Sysconfig 
/*

@classinfo syslog_type=ST_AW_SYSCONFIG relationmgr=yes maintainer=kristo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property matchmaker type=callback callback=callback_matchmaker group=configure no_caption=1
@caption Seadistamise tabel

@groupinfo configure caption="Seadistamine"

@reltype SCRIPT value=1 clid=CL_AW_SCRIPT
@caption Skript

HANDLE_MESSAGE_WITH_PARAM(MSG_USER_CREATE, CL_USER, on_interesting_gossip)

*/

class aw_sysconfig extends class_base
{
	const AW_CLID = 479;

	function aw_sysconfig()
	{
		$this->init(array(
			"clid" => CL_AW_SYSCONFIG
		));
	}

	//////
	// class_base classes usually need those, uncomment them if you want to use them

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
		};
		return $retval;
	}

	function callback_matchmaker($arr)
	{
		// TODO: gather a list of acceptable messages from the top of the class
		$msgs = array("MSG_USER_CREATE");
		$res = array();
		$conns = $arr["obj_inst"]->connections_from(array(
			//"type" => "RELTYPE_CONFIG_OPTION",
			"type" => "RELTYPE_SCRIPT",
		));

		foreach($msgs as $msg)
		{
			$res[] = array(
				"name" => "message",
				"type" => "text",
				"caption" => t("Teade"),
				"value" => $msg,
			);
			// now add a list of all attached scripts
			$opts = array();

			foreach($conns as $conn)
			{
				$opts[$conn->prop("to")] = $conn->prop("to.name");
			};

			$scripts = $arr["obj_inst"]->meta("scripts");


			$res[] = array(
				"name" => "scripts[$msg]",
				"type" => "chooser",
				"caption" => t("Vali skriptid"),
				"value" => $scripts[$msg],
				"multiple" => 1,
				"options" => $opts,
			);
		};
		return $res;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "matchmaker":
				$arr["obj_inst"]->set_meta("scripts",$arr["request"]["scripts"]);
				break;

		}
		return $retval;
	}	

	function on_interesting_gossip($arr)
	{
		// $arr contains all the arguments passed by the message, I need to do something
		// with them. It all boils down to "how do I pass the user object" to the script?

		/*
		print "sysconfig received the following<br>";
		print "<pre>";
		var_dump($arr);
		print "</pre>";

		print "and the following script objects are connected<br>";
		*/
		// there is a bug here, I can't connect sysconfig thingie to a folder, so I have to create an objectlist

		$ol = new object_list(array(
			"class_id" => CL_AW_SYSCONFIG,
			"status" => STAT_ACTIVE,
		));

		for ($cfg_obj = $ol->begin(); !$ol->end(); $cfg_obj = $ol->next())
		{
			//print "sconf name = " . $cfg_obj->name() . "<br>";
			$this->run_attached_scripts(array(
				"id" => $cfg_obj->id(),
				"vars" => $arr,
			));
		};

		//print "so I'm just going to invoke the script objects and that should be it";
	}

	function run_attached_scripts($arr)
	{
		// this is THE meat	
		$sconf_obj = new object($arr["id"]);
		$conns = $sconf_obj->connections_from(array(
			"type" => "RELTYPE_SCRIPT",
		));

		$t = get_instance(CL_AW_SCRIPT);

		foreach($conns as $conn)
		{
			$script_obj = $conn->to();
			$t->run_script_run(array(
				"id" => $conn->prop("to"),
				"vars" => $arr["vars"],
			));
		};
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
