<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/scm/scm_tournament.aw,v 1.9 2007/12/06 14:34:07 kristo Exp $
// scm_tournament.aw - V&otilde;istlussari
/*

@classinfo syslog_type=ST_SCM_TOURNAMENT relationmgr=yes no_comment=1 no_status=1 prop_cb=1  maintainer=tarvo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@groupinfo competitions caption="V&otilde;istlused"

	@default group=competitions

	@property comp_toolbar no_caption=1 type=toolbar
	@caption T&ouml;&ouml;riistariba

	@property comp_table no_caption=1 type=table
	@caption V&ouml;istlused

	@property search_res type=hidden name=search_result store=no no_caption=1
	@caption Otsingutulemuste hoidja

	@property rem_competitions type=submit
	@caption Eemalda v&otilde;istlus(ed) v&otilde;istlussarjast


*/

class scm_tournament extends class_base
{
	function scm_tournament()
	{
		$this->init(array(
			"tpldir" => "applications/scm/scm_tournament",
			"clid" => CL_SCM_TOURNAMENT
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- get_property --//
			case "comp_toolbar":
				$url = $this->mk_my_orb("gen_new_competition",array(
					"parent" => $arr["obj_inst"]->parent(),
					"id" => $arr["obj_inst"]->id(),
					"return_url" => get_ru(),
				));
				$prop["vcl_inst"]->add_button(array(
					"name" => "add_competition",
					"tooltip" => t("Lisa uus v&otilde;istlus"),
					"img" => "new.gif",
					"url" => $url,
				));
				$popup_search = get_instance("vcl/popup_search");
				$search_butt = $popup_search->get_popup_search_link(array(
					"pn" => "search_result",
					"clid" => CL_SCM_COMPETITION,
				));
				$prop["vcl_inst"]->add_cdata($search_butt);
			break;
			case "comp_table":
				$t = &$prop["vcl_inst"];
				$this->_gen_competitions_table(&$t);
				
				$link = html::href(array(
					"url" => "%s",
					"caption" =>
						"%s",
				));
				$filt = array(
					"tournament" => $arr["obj_inst"]->id(),
				);

				$inst = get_instance(CL_SCM_COMPETITION);

				foreach($this->get_competitions($filt) as $oid)
				{
					$obj = obj($oid);
					$l_obj = obj(($s = ($inst->get_location(array("competition" => $oid))))?$s:false);
					$l_url = $this->mk_my_orb("change", array(
						"class" => "location",
						"id" => $s,
						"return_url" => get_ru(),
					));

					$e_obj = obj(($s = ($inst->get_event(array("competition" => $oid))))?$s:false);
					$e_url = $this->mk_my_orb("change", array(
						"class" => "scm_event",
						"id" => $s,
						"return_url" => get_ru(),
					));

					$date = $inst->get_date(array("competition" => $oid));
					$c_url = $this->mk_my_orb("change", array(
						"class" => "scm_competition",
						"id" => $oid,
						"return_url" => get_ru(),
					));
					$prop["vcl_inst"]->define_data(array(
						"name" => sprintf($link, $c_url, $obj->name()),
						"location" => ($l_obj)?sprintf($link, $l_url, $l_obj->name()):t("M&auml;&auml;ramata"),
						"event" => ($e_obj)?sprintf($link, $e_url, $e_obj->name()):t("M&auml;&auml;ramata"),
						"start_time" => $date,
						"rem_competition" => $oid,
					));
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
			case "comp_table":
				if(strlen(($res = $arr["request"]["search_result"])))
				{
					foreach(split(",", $res) as $competition)
					{
						$obj = obj($competition);
						$tournaments = $obj->prop(($propname = "scm_tournament"));
						$tournaments[] = $arr["obj_inst"]->id();
						$obj->set_prop($propname, $tournaments);
						$obj->save();
					}
				}
				if(count($rem = $arr["request"]["rem_comp"]))
				{
					$id = $arr["obj_inst"]->id();
					foreach($rem as $competition)
					{
						$o = obj($competition);
						foreach(($tournaments = $o->prop(($propname = "scm_tournament"))) as $k => $tournament)
						{
							if($tournament == $id)
							{
								unset($tournaments[$k]);
							}
						}
						$o->set_prop($propname, $tournaments);
						$o->save();
					}
				}
			break;
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function _gen_competitions_table($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("V&otilde;istluse nimi"),
			"sortable" => true,
		));
		$t->define_field(array(
			"name" => "location",
			"caption" => t("Asukoht"),
			"sortable" => true,
		));
		$t->define_field(array(
			"name" => "event",
			"caption" => t("Spordiala"),
			"sortable" => true,
		));
		$t->define_field(array(
			"name" => "start_time",
			"caption" => t("V&otilde;istluse aeg"),
			"align" => "center",
			"sortable" => true,
			"callback" => array(&$this, "__date_sort_callback"),
		));
		$t->define_chooser(array(
			"name" => "rem_comp",
			"field" => "rem_competition",
		));
		$t->set_default_sortby("name");
		return $t;
	}
	
	function get_tournaments()
	{
		$list = new object_list(array(
			"class_id" => CL_SCM_TOURNAMENT,
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
	
	function __date_sort_callback($key, $str, $row)
	{
		return date("d/m/Y", $key);
	}

	/**
	**/
	function get_competitions($arr = array())
	{
		$list = new object_list(array(
			"class_id" => CL_SCM_COMPETITION,
			"CL_SCM_COMPETITION.RELTYPE_TOURNAMENT" => $arr["tournament"],
		));
		foreach($list->arr() as $oid => $obj)
		{
			if(in_array($arr["tournament"], $obj->prop("scm_tournament")))
			{
				$ret[] = $oid;
			}
		}
		return $ret;
	}
	
	/**
		@attrib name=gen_new_competition params=name
		@param parent required
		@param id required
		@param return_url required
	**/
	function _gen_new_competition($arr)
	{
		$obj = obj();
		$obj->set_parent($arr["parent"]);
		$obj->set_class_id(CL_SCM_COMPETITION);
		$obj->set_prop("scm_tournament", array($arr["id"]));

		$new_id = $obj->save_new();
		$url = $this->mk_my_orb("change", array(
			"id" => $new_id,
			"class" => "scm_competition",
			"return_url" => $arr["return_url"],
		));
		return $url;
	}
}
?>
