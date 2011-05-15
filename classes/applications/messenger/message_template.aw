<?php

// message_template.aw - Kirja template
/*

@classinfo syslog_type=ST_MESSAGE_TEMPLATE relationmgr=yes prop_cb=1

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@classinfo no_comment=1

@property subject type=textbox
@caption Pealkiri

@property is_html type=checkbox ch_value=1
@caption HTML

@property legend type=text
@caption Legend

@property content type=textarea cols=150 rows=40
@caption Sisu


*/

class message_template extends class_base
{
	function message_template()
	{
		$this->init(array(
			"tpldir" => "messenger/message_template",
			"clid" => CL_MESSAGE_TEMPLATE
		));
	}

	function _get_legend(&$arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		$prop["value"] = "NB! Sisu pealkirja stiili nimi peab olema alati <font color=red>doc-title</font> ja alampealkirja <font color=red>doc-titleSub</font>";
		return $retval;
	}

	function parse_alias($arr = array())
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

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
