<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/questionary/answerer.aw,v 1.4 2007/12/06 14:33:53 kristo Exp $
// answerer.aw - Vastaja 
/*

@classinfo syslog_type=ST_ANSWERER relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=tarvo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property gender type=text
@caption Sugu

@property age type=text
@caption Vanus

@property area type=text
@caption Tegevusala

@property school type=text
@caption &Otilde;ppimine/T&ouml;&ouml;tamine k&otilde;rgkoolis

@property intrests type=text
@caption Huvivaldkond

@property visit_recur type=text
@caption Rahvusraamatukogu k&uuml;lastan

@property usage type=text
@caption Raamatukogu teenuseid kasutan

@property questionary_comment type=textarea cols=50 rows=6
@caption comment

@property questionary type=relpicker
@caption K&uuml;simustik

@reltype ANSWER value=1 clid=CL_QUESTIONARY_RESULT
@caption Vastus

@reltype QUESTIONARY value=2 clid=CL_QUESTIONARY
@caption K&uuml;simustik
*/

class answerer extends class_base
{
	const AW_CLID = 1169;

	function answerer()
	{
		$this->init(array(
			"tpldir" => "applications/questionary/answerer",
			"clid" => CL_ANSWERER
		));
	}

	function get_property($arr)
	{
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

	function callback_mod_reforb($arr)
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

//-- methods --//
}
?>
