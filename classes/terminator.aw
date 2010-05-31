<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/terminator.aw,v 1.9 2009/05/07 13:18:42 kristo Exp $
// terminator.aw - The Terminator
/*

@classinfo syslog_type=ST_TERMINATOR relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kaarel

@default table=objects
@default group=general

*/

class terminator extends class_base
{
	const AW_CLID = 1370;

	function terminator()
	{
		$this->init(array(
			"tpldir" => "terminator",
			"clid" => CL_TERMINATOR
		));
	}

	function get_property($arr)
	{
		if(array_key_exists("KAAREL", $_GET) && $_GET["KAAREL"] == 1)
		{
			$this->KAAREL($arr);
			die;
		}
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- get_property --//
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- set_property --//
		}
		return $retval;
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}
	function inverse($x) {
		if (!$x) {
			throw new \Exception('Division by zero.');
		}
		else return 1/$x;
	}

	function KAAREL($arr)
	{
		try {
			echo $this->inverse(5) . "\n";
			echo $this->inverse(0) . "\n";
			echo $this->inverse(5) . "\n";
			echo $this->inverse(5) . "\n";
		} catch (\Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
	}
}
?>
