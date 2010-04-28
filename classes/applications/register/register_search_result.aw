<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/register/register_search_result.aw,v 1.3 2007/12/06 14:34:00 kristo Exp $
// register_search_result.aw - Registri otsingu tulemused 
/*

@classinfo syslog_type=ST_REGISTER_SEARCH_RESULT relationmgr=yes no_status=1 no_comment=1 maintainer=kristo

@default table=objects
@default group=general
@default field=meta 
@default method=serialize

@property search type=relpicker reltype=RELTYPE_SEARCH 
@caption Registri otsing

@groupinfo search caption="Otsing"
@default group=search

@property search_data type=callback callback=callback_get_search

@groupinfo search_res caption="Tulemused" submit=no
@default group=search_res

@property search_res type=text store=no no_caption=1

@reltype SEARCH value=1 clid=CL_REGISTER_SEARCH
@caption registri otsing

*/

class register_search_result extends class_base
{
	const AW_CLID = 486;

	function register_search_result()
	{
		$this->init(array(
			"tpldir" => "applications/register/register_search_result",
			"clid" => CL_REGISTER_SEARCH_RESULT
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "search_res":
				$prop["value"] = $this->show(array(
					"id" => $arr["obj_inst"]->id()
				));
				break;
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "search_data":
				$this->do_save_search_data($arr);
				break;
		}
		return $retval;
	}	

	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	function show($arr)
	{
		$ob = new object($arr["id"]);

		$request = array("rsf" => $ob->meta("rsf"));
		$request["search_butt"] = "1";
		if ($GLOBALS["ft_page"])
		{
			$request["ft_page"] = $GLOBALS["ft_page"];
		}

		$r = get_instance(CL_REGISTER_SEARCH);
		$ro = obj($ob->prop("search"));
		$props =  $r->get_sform_properties($ro, $request);
		
		classload("vcl/table");
		$t = new aw_table(array(
			"layout" => "generic"
		));
		$r->do_search_res_tbl(array(
			"prop" => array(
				"vcl_inst" => &$t
			),
			"obj_inst" => &$ro,
			"request" => $request,
		));
		if (count($t->data) < 1 && $request["search_butt"] != "" && $ro->prop("notfound_text") != "")
		{
			$table = nl2br(sprintf($ro->prop("notfound_text"), $request["rsf"][$r->fts_name]));
		}
		else
		{
			$table = $t->draw();
		}

		if ($ro->prop("show_date") && $request["search_butt"] != "")
		{
			$table .= "<br>".date("d.m.Y H:i:s");
		}
		
		return $table;
	}

	function callback_get_search($arr)
	{
		$r = get_instance(CL_REGISTER_SEARCH);
		return $r->get_sform_properties(obj($arr["obj_inst"]->prop("search")), array("rsf" => $arr["obj_inst"]->meta("rsf")));
	}

	function do_save_search_data($arr)
	{
		$arr["obj_inst"]->set_meta("rsf", $arr["request"]["rsf"]);
	}
}
?>
