<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/scm/scm_group.aw,v 1.5 2007/12/06 14:34:06 kristo Exp $
// scm_group.aw - V&otilde;istlusklass 
/*

@classinfo syslog_type=ST_SCM_GROUP relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=tarvo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property abbreviation type=textbox
@caption L&uuml;hend

@layout split_age type=hbox width=200px
@caption Vanusevahemik
	@property age_from type=textbox size=5 parent=split_age
	@caption Alates

	@property age_to type=textbox size=5 parent=split_age
	@caption Kuni

@layout split_sex type=vbox
@caption Sugu
	@property male type=checkbox no_caption=1 ch_value=1 default=0 parent=split_sex
	@caption Mehed

	@property female type=checkbox no_caption=1 ch_value=1 default=0 parent=split_sex
	@caption Naised
*/

class scm_group extends class_base
{
	const AW_CLID = 1098;

	function scm_group()
	{
		$this->init(array(
			"tpldir" => "applications/scm/scm_group",
			"clid" => CL_SCM_GROUP
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

	function get_groups()
	{
		$list = new object_list(array(
			"class_id" => CL_SCM_GROUP,
		));
		return $list->arr();
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
