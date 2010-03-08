<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/scm/scm_event.aw,v 1.9 2007/12/06 14:34:06 kristo Exp $
// scm_event.aw - Spordiala 
/*

@classinfo syslog_type=ST_SCM_EVENT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=tarvo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property type type=select
@caption T&uuml;&uuml;p

@property team_result_calc type=select
@caption V&otilde;istkonna tulemuse arvutus

@property result_type type=relpicker reltype=RELTYPE_RESULT_TYPE editonly=1
@caption Paremusj&auml;rjestuse t&uuml;&uuml;p

@reltype RESULT_TYPE value=1 clid=CL_SCM_RESULT_TYPE
@caption Paremusj&auml;rjestuse t&uuml;&uuml;p
*/

class scm_event extends class_base
{
	function scm_event()
	{
		$this->init(array(
			"tpldir" => "applications/scm/scm_event",
			"clid" => CL_SCM_EVENT
		));
		$this->_set_data();
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- get_property --//
			case "type":
				$prop["options"] = array(
					"single" => t("Individuaalne"),
					"multi" => t("Individuaal-v&otilde;istkondlik"),
					"multi_coll" => t("V&otilde;istkondlik-kollektiivne"),
				);
			break;

			case "team_result_calc":
				if($arr["obj_inst"]->prop("type") == "single" || $arr["obj_inst"]->prop("type") == "multi_coll")
				{
					return PROP_IGNORE;
				}
				foreach($this->get_alg() as $alg => $caption)
				{
					$prop["options"][$alg] = $caption;
				}

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
			//-- set_property --//
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}


	/**
		@attrib api=1 params=name
		@param organizer optional type=oid
			if set, only this organizers events will be returned
		@comment
			fetches events
		@returns 
			fetched events or false if none found
	**/
	function get_events($arr = array())
	{
		if(strlen($arr["organizer"]))
		{
			$filter["parent"] = $arr["organizer"];
		}
		$filter["class_id"] = CL_SCM_EVENT;
		$list = new object_list($filter);
		return $list->arr();
	}

	function add_event($arr = array())
	{
		$obj = obj();
		$obj->set_parent($arg["parent"]);
		$obj->set_class_id(CL_SCM_EVENT);
		$obj->set_name($arg["name"]);
		$obj->set_prop("type", $arg["type"]);
		$obj->set_prop("result_type", $arg["result_type"]);
		$oid = $obj->save_new();
		return $oid;
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
	/* next two functions are deprecated.. ai tink */
	function get_result_type($arr = array())
	{
		 $obj = obj($arr["event"]);
		 return $obj->prop("result_type");
	}
	
	function get_team_result_calc_fun($arr = array())
	{
		$o = obj($arr["event"]);
		return $o->prop("team_result_calc");
	}

	function _set_data()
	{
		$this->alg = array(
			"_scm_max" => t("Parim tulemus"),
			"_scm_min" => t("Kehvim tulemus"),
			"_scm_avg" => t("Tulemuste keskmine"),
			"_scm_sum" => t("Tulemuste summa"),
		);
	}

	// algorithms to calculate team result when event type is set to multi
	function _scm_max($arr)
	{
		return max($arr);
	}

	function _scm_min($arr)
	{
		return min($arr);
	}
	

	function _scm_avg($arr)
	{
		return (array_sum($arr) / count($arr));
	}

	function _scm_sum($arr)
	{
		return array_sum($arr);
	}

	/**
		@attrib params=pos
		@param algorithm optional type=string
			options:
				max,min,avg,sum
	**/
	function get_alg($arr = "")
	{
		return (strlen($arr)?$this->alg["_".$arr]:$this->alg);
	}
}
?>
