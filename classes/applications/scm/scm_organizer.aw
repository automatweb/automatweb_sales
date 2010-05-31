<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/scm/scm_organizer.aw,v 1.11 2008/11/06 18:52:08 markop Exp $
// scm_organizer.aw - Spordiv&otilde;istluste korraldaja 
/*

@classinfo syslog_type=ST_SCM_ORGANIZER relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=tarvo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property organizer_person type=relpicker reltype=RELTYPE_ORGANIZER
@caption Organiseeria

@property organizer_company type=text store=no editonly=1
@caption Firmast

@groupinfo competitions caption="V&otilde;istlused" submit=no
	@groupinfo current_competitions caption="Praegused v&otilde;istlused" submit=no parent=competitions
		@default group=current_competitions
		
		@property competitions_tb type=toolbar no_caption=1
		@caption voistluste tuulbar

		@property competitions_tbl type=table no_caption=1 group=current_competitions,competitions_archive
		@caption Praeguste v&otilde;istluste nimekiri

		@property remove_competitions type=submit group=current_competitions,competitions_archive
		@caption Kustuta v&otilde;istlused
	
	@groupinfo competitions_archive caption="V&otilde;istluste arhiiv" submit=no parent=competitions


@groupinfo events caption="Spordialad" submit=no
	@default group=events

	@property events_tb type=toolbar no_caption=1
	@caption alade tuulbar

	@property events_tbl type=table no_caption=1
	@caption Spordialade nimistu

@groupinfo locations caption="Asukohad" submit=no
	@default group=locations

	@property location_tb type=toolbar no_caption=1
	@caption Asukohtade t&ouml;&ouml;riistariba

	@property location_tbl type=table no_caption=1
	@caption Asukohtade tabel

@groupinfo tournaments caption="V&otilde;istlussarjad" submit=no
	@default group=tournaments

	@property tournaments_tb type=toolbar no_caption=1
	@caption V&otilde;istlussarjade t&ouml;&ouml;riistariba

	@property tournaments_tbl type=table no_caption=1
	@caption V&otilde;istlussarjade tabel

@groupinfo score_calcs caption="Punktis&uuml;steemid" submit=no
	@default group=score_calcs
	
	@property score_calc_tb type=toolbar no_caption=1
	@caption Punktis&uuml;steemide t&ouml;&ouml;riistariba

	@property score_calc_tbl type=table no_caption=1
	@caption Punktis&uuml;steemide tabel

@groupinfo groups caption="V&otilde;istlusklassid" submit=no
	@default group=groups

	@property groups_tb type=toolbar no_caption=1
	@caption V&otilde;istlusklasside t&ouml;&ouml;riistariba

	@property groups_tbl type=table no_caption=1
	@caption V&otilde;istlusklasside tabel

@groupinfo result_types caption="Paremusj&auml;rjestuse t&uuml;&uuml;bid" submit=no
	@default group=result_types

	@property result_type_tb type=toolbar no_caption=1
	@caption Paremusj&auml;rjestus t&uuml;&uuml;pide t&ouml;&ouml;riistariba

	@property result_type_tbl type=table no_caption=1
	@caption Paremusj&auml;rjestus t&uuml;&uuml;pide tabel

@reltype COMPETITION value=1 clid=CL_SCM_COMPETITION
@caption V&otilde;istlus

@reltype ORGANIZER value=2 clid=CL_CRM_PERSON
@caption Organisaator
*/

class scm_organizer extends class_base
{
	const AW_CLID = 1091;

	function scm_organizer()
	{
		$this->init(array(
			"tpldir" => "applications/scm/scm_organizer",
			"clid" => CL_SCM_ORGANIZER
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- get_property --//
			// events
			case "events_tbl":
				$this->_gen_events_tbl(&$prop["vcl_inst"]);
				$inst = get_instance(CL_SCM_EVENT);
				if(!count($list = $inst->get_events()))
				{
					return PROP_IGNORE;
				}

				foreach($list as $oid => $obj)
				{
					$prop["vcl_inst"]->define_data(array(
						"name" => $obj->name(),
						"type" => $obj->prop("type"),
						"result_type" => $obj->prop("result_type")
					));
				}
			break;
			case "events_tb":
				$tb = &$prop["vcl_inst"];

				$tb->add_button(array(
					"name" => "new_event",
					"tooltip" => t("Lisa uus spordiala"),
					"img" => "new.gif",
					"url" => $this->mk_my_orb("new",array(
						"class" => "scm_event",
						"parent" => $arr["obj_inst"]->parent(),
					)),
				));
			break;
			// res types
			case "result_type_tb":
				$tb = &$prop["vcl_inst"];

				$tb->add_button(array(
					"name" => "new_result_type",
					"tooltip" => t("Lisa uus paremusj&auml;rjestus t&uuml;&uuml;p"),
					"img" => "new.gif",
					"url" => $this->mk_my_orb("new",array(
						"class" => "scm_result_type",
						"parent" => $arr["obj_inst"]->parent(),
					)),
				));
			
			break;
			case "result_type_tbl":
				$t = &$prop["vcl_inst"];
				$this->_gen_res_type_tbl(&$t);
				$inst = get_instance(CL_SCM_RESULT_TYPE);
				if(!count($list = $inst->get_result_types()))
				{
					return PROP_IGNORE;
				}
				foreach($list as $oid => $obj)
				{
					$t->define_data(array(
						"name" => $obj->name(),
						"sort" => $obj->prop("sort"),
						"unit" => $obj->prop("unit"),
					));
				}

			break;
			case "result_type_h":
				$prop["value"] = CL_SCM_RESULT_TYPE;
			break;
			// locations
			case "location_tbl":
				$t = &$prop["vcl_inst"];
				$this->_gen_loc_tbl(&$t);
				$inst = get_instance(CL_LOCATION);
				if(!count($list = $inst->get_locations()))
				{
					return PROP_IGNORE;
				}

				foreach($list as $oid => $obj)
				{
					$t->define_data(array(
						"name" => $obj->name(),
						"address" => $obj->prop("address"),
						"map" => "link",
						"photo" => "link"
					));
				}
			break;
			case "location_tb":
				$tb = &$prop["vcl_inst"];
				$tb->add_button(array(
					"name" => "new_location",
					"tooltip" => t("Uus asukoht"),
					"img" => "new.gif",
					"url" => $this->mk_my_orb("new", array(
						"class" => "location",
						"parent" => $arr["obj_inst"]->parent(),
					)),
				));
			break;
			// competitions
			case "competitions_tbl":
				$t = &$prop["vcl_inst"];
				$this->_gen_comp_tbl(&$t);
				$inst = get_instance(CL_SCM_COMPETITION);
				$filt = array(
					"state" => ($arr["request"]["group"] == "competitions_archive")?"archive":"current",
					"organizer" => $arr["obj_inst"]->id(),
				);
				if(!count($list = $inst->get_competitions($filt)))
				{
					$prop["value"] = "<font color=\"#FF0000\">".t("Ei ole &uuml;htegi vastavat v&otilde;istlust")."</font>";
				}

				foreach($list as $oid => $obj)
				{
					$e_obj = ($s = $obj->prop("scm_event"))?obj($s):false;
					$l_obj = ($s = $obj->prop("location"))?obj($s):false;
					$t_obj = ($s = $obj->prop("scm_tournament"))?obj($s):false;
					$competition_url = $this->mk_my_orb("change" ,array(
						"class" => "scm_competition",
						"id" => $obj->id(),
						"return_url" => get_ru(),
					));
					if($e_obj)
					{
						$event_url = $this->mk_my_orb("change",array(
							"class" => "scm_event",
							"id" => $e_obj->id(),
							"return_url" => get_ru(),
						));
					}
					if($l_obj)
					{
						$location_url = $this->mk_my_orb("change",array(
							"class" => "location",
							"id" => $l_obj->id(),
							"return_url" => get_ru(),
						));
					}
					if($t_obj)
					{
						$tournament_url = $this->mk_my_orb("change", array(
							"class" => "scm_tournament",
							"id" => $t_obj->id(),
							"return_url" => get_ru(),
						));
					}
					$link = html::href(array(
						"caption" => 
							"%s",
						"url" => "%s",
					));
					$t->define_data(array(
						"name" => sprintf($link, $competition_url, $obj->name()),
						"location" => ($l_obj)?sprintf($link, $location_url, $l_obj->prop("name")):"<font color=\"#FF0000\">".t("Asukoht valimata")."</font>",
						"event" => ($e_obj)?sprintf($link, $event_url, $e_obj->prop("name")):"<font color=\"#FF0000\">".t("Spordiala valimata")."</font>",
						"date" => date("d / m / Y",$obj->prop("date_from")),
						"tournament" => ($t_obj)?sprintf($link, $tournament_url, $t_obj->prop("name")):t("Ei ole v&otilde;istlussarja osav&otilde;istlus"),
						"remove_comp" => $oid,
					));
				}
			break;

			case "competitions_tb":
				$tb = &$prop["vcl_inst"];

				$tb->add_button(array(
					"name" => "new_competition",
					"tooltip" => t("Lisa uus voistlus"),
					"img" => "new.gif",
					"url" => $this->mk_my_orb("new",array(
						"class" => "scm_competition",
						"parent" => $arr["obj_inst"]->id(),
						"reltype" => 1, // like whotto fokk?
						"alias_to" => $arr["obj_inst"]->id(),
						"return_url" => get_ru(),
					)),
				));
			break;

			// tournaments 
			case "tournaments_tb":
				$tb = &$prop["vcl_inst"];
				$tb->add_button(array(
					"name" => "new_tournament",
					"tooltip" => t("Lisa uus v&otilde;istlussari"),
					"img" => "new.gif",
					"url" => $this->mk_my_orb("new", array(
						"class" => "scm_tournament",
						"parent" => $arr["obj_inst"]->id(),
						"return_url" => get_ru(),
					)),
				));
			break;
			case "tournaments_tbl":
				$t = &$prop["vcl_inst"];
				$this->_gen_trn_tbl(&$t);
				$inst = get_instance(CL_SCM_TOURNAMENT);
				if(!count(($list = $inst->get_tournaments())))
				{
					return PROP_IGNORE;
				}
				foreach($list as $oid => $obj)
				{
					$t->define_data(array(
						"name" => $obj->name(),
						"competitions" => "xxx",
					));
				}
			break;

			// score calcs
			case "score_calc_tb":
				$tb = &$prop["vcl_inst"];
				$tb->add_button(array(
					"name" => "new_score_calc",
					"tooltip" => t("Lisa uus punktis&uuml;steem"),
					"img" => "new.gif",
					"url" => $this->mk_my_orb("new", array(
						"class" => "scm_score_calc",
						"parent" => $arr["obj_inst"]->id(),
						"return_url" => get_ru(),
					)),
				));

			break;

			case "score_calc_tbl":
				$t = &$prop["vcl_inst"];
				$this->_gen_scorecalc_tbl(&$t);
				$sc = get_instance(CL_SCM_SCORE_CALC);
				foreach($sc->algorithm_list() as $alg)
				{
					$t->define_data(array(
						"name" => $alg,
					));
				}
			break;
			// groups
			case "groups_tb":
				$tb = &$prop["vcl_inst"];
				$tb->add_button(array(
					"name" => "new_group",
					"tooltip" => t("Lisa uus v&otilde;istlusklass"),
					"img" => "new.gif",
					"url" => $this->mk_my_orb("new", array(
						"class" => "scm_group",
						"parent" => $arr["obj_inst"]->id(),
						"return_url" => get_ru(),
					)),
				));

			break;
			case "groups_tbl":
				$t = &$prop["vcl_inst"];
				$this->_gen_groups_tbl(&$t);
				$inst = get_instance(CL_SCM_GROUP);
				if(!count(($list = $inst->get_groups())))
				{
					return PROP_IGNORE;
				}
				foreach($list as $oid => $obj)
				{
					$t->define_data(array(
						"name" => $obj->name(),
						"short" => "short",
					));
				}

			break;


			// general
			case "organizer_company":
				$company = obj($this->get_organizer_company($arr = array("organizer" => $arr["obj_inst"]->id())));
				$prop["value"] = $company->name();
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
			case "result_type_unit":
				return PROP_IGNORE;
			break;
			case "event_type":
				return PROP_IGNORE;
			break;
			case "new_grp":
				return PROP_IGNORE;
			break;

			case "competitions_tbl":
				$rem = $arr["request"]["rem_comp"];
				if(count($rem))
				{
					foreach($rem as $comp)
					{
						$o = obj($comp);
						$o->delete(true);
					}
				}
			break;
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

//-- methods --//

	/**
		@attrib params=name api=1
		@param oganizer required type=oid
			scm_organizer object id.
		@comment
			fetches crm_company oid where the organizer person works.
		@returns
			oid of the crm_company.
	**/
	function get_organizer_company($arr)
	{
		$o = obj($this->get_organizer_person($arr));
		return ($s = $o->company_id())?$s:false;
	}

	/**
		@attrib params=name api=1
		@param organizer required type=oid
			scm_organizer object id.
		@comment
			fetches crm_person oid connected to given organizer
		@returns
			crm_person's oid.
	**/
	function get_organizer_person($arr)
	{
		$obj = obj($arr["organizer"]);
		return ($o = $obj->prop("organizer_person"))?$o:false;
	}

	/**
	**/
	function get_competitions($arr)
	{
		
	}

	/**
		@param only_with_competitions optional type=bool
			returns organizers who have at least 1 competition
		@comment
			generates list of all organizers.
		@returns
			array of all the organizers.
			array(
				scm_organizer oid,
				scm_organizer object_inst,
			)
	**/
	function get_organizers($arr)
	{
		$filt["class_id"] = CL_SCM_ORGANIZER;
		$list = new object_list($filt);
		if(!$arr["only_with_competitions"])
		{
			return $list->arr();
		}
		foreach($list->arr() as $oid => $obj)
		{
			$conns = $obj->connections_from(array(
				"type" => "RELTYPE_COMPETITION",
			));
			if(!count($conns))
			{
				$list->remove($oid);
			}
		}
		return $list->arr();
	}
	

	function _exclude_new($arr)
	{
		return (!$arr["request"]["add_new"])?true:false;
	}

	function _gen_loc_img_list($arr)
	{
		$conns = new connection();
		$conns = $conns->find(array(
			"from.class_id" => CL_LOCATION,
			"to.class_id" => CL_IMAGE,
			"from.parent" => $arr["obj_inst"]->id(),
		));
		foreach($conns as $conn)
		{
			$obj = obj($conn["to"]);
			$prop["options"][$conn["to"]] = $obj->name();
		}
	}

	function _gen_events_tbl($t)
	{
		$t->define_field(array(
			"caption" => t("Nimetus"),
			"name" => "name",
		));
		$t->define_field(array(
			"caption" => t("T&uuml;&uuml;p"),
			"name" => "type"
		));
		$t->define_field(array(
			"caption" => t("Paremusj&auml;rjestuse t&uuml;&uuml;p"),
			"name" => "result_type"
		));
	}

	function _gen_comp_tbl($t)
	{
		$t->define_field(array(
			"caption" => t("&Uuml;rituse nimi"),
			"name" => "name",
			"sortable" => 1,
		));
		$t->define_field(array(
			"caption" => t("Spordiala"),
			"name" => "event",
			"sortable" => 1,
		));
		$t->define_field(array(
			"caption" => t("Asukoht"),
			"name" => "location",
			"sortable" => 1,
		));
		$t->define_field(array(
			"caption" => t("Kuup&auml;ev"),
			"name" => "date",
			"sortable" => 1,
		));
		$t->define_field(array(
			"caption" => t("V&otilde;istlussari"),
			"name" => "tournament",
			"sortable" => 1,
		));
		$t->define_chooser(array(
			"name" => "rem_comp",
			"field" => "remove_comp",
		));

		$t->set_default_sortby("name");

	}

	function _gen_res_type_tbl($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));
		$t->define_field(array(
			"name" => "unit",
			"caption" => t("M&ouml;&ouml;detav &Uuml;hik"),
		));
		$t->define_field(array(
			"name" => "sort",
			"caption" => t("Sorteeritakse"),
		));

	}

	function _gen_loc_tbl($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimetus"),
		));
		$t->define_field(array(
			"name" => "address",
			"caption" => t("Aadress"),
		));
		$t->define_field(array(
			"name" => "map",
			"caption" => t("Kaart"),
		));
		$t->define_field(array(
			"name" => "photo",
			"caption" => t("Foto"),
		));

	}

	function _gen_trn_tbl($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("V&otilde;istlussarja nimi"),
		));	
		$t->define_field(array(
			"name" => "competitions",
			"caption" => t("V&otilde;istlusi"),
		));

	}

	function _gen_scorecalc_tbl($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Algoritm"),
		));

	}

	function _gen_groups_tbl($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("V&otilde;istlusklassi nimi"),
		));
		$t->define_field(array(
			"name" => "short",
			"caption" => t("L&uuml;hend"),
		));
	}

}
?>
