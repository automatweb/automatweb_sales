<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/scm/scm_contestant.aw,v 1.12 2009/01/16 11:37:34 kristo Exp $
// scm_contestant.aw - V&otilde;istleja 
/*

@classinfo syslog_type=ST_SCM_CONTESTANT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=tarvo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property contestant type=relpicker reltype=RELTYPE_CONTESTANT
@caption V&otilde;istleja

@property contestants_company type=text store=no editonly=1
@caption Firmast

@groupinfo register caption="Registreeri v&otilde;istlustele" submit=no
	@property reg_tbl type=table group=register no_caption=1
	@caption V&otilde;istluste tabel

	@property reg_button type=submit group=register
	@caption Registreeru

@groupinfo competitions caption="Minu v&otilde;istlused" submit=no
	
	@default group=competitions

	@property comp_tbl type=table no_caption=1
	@caption voistlused

	@property comp_caption type=text store=no
	@caption V&otilde;istlus

	@prop
	function callback_pre_save($arr)
	{
		$arr["obj_inst"]->set_name($arr["obj_inst"]->prop_str("organizer_person"));
	}erty sel_teams type=select store=no
	@caption Minu meeskonnad

	@property teams_submit type=submit
	@caption Salvesta

	@property unreg_button type=submit group=competitions
	@caption Eemalda registratsioon

@reltype CONTESTANT value=1 clid=CL_CRM_PERSON
@caption V&otilde;istleja

*/

class scm_contestant extends class_base
{
	const AW_CLID = 1092;

	function scm_contestant()
	{
		$this->init(array(
			"tpldir" => "applications/scm/scm_contestant",
			"clid" => CL_SCM_CONTESTANT
		));
		$this->contestant = false;
	}

	function set_contestant($cnt)
	{
		$this->contestant = $cnt?$cnt:$this->contestant;
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- get_property --//
			case "reg_tbl":
				$t = &$prop["vcl_inst"];
				$this->_gen_tbl(&$t);
				// add special table fields
				$t->define_chooser(array(
					"name" => "reg",
					"field" => "register",
				));


				// insert data
				$comp = get_instance(CL_SCM_COMPETITION);
				$org = get_instance(CL_SCM_ORGANIZER);
				$filt = array(
					"contestant" => $arr["obj_inst"]->id(),
					"unregistered" => true,
				);
				foreach($comp->get_competitions($filt) as $oid => $obj)
				{
					$org_oid  = obj($comp->get_organizer(array("competition" => $obj->id())));
					$org_company = obj($org->get_organizer_company(array("organizer" => $org_oid)));
					
					$l_obj = obj($obj->prop("location"));
					$e_obj = obj($obj->prop("scm_event"));
					$t_obj = obj($obj->prop("scm_tournament"));
					
					$l_url = $this->mk_my_orb("change", array(
						"class" => "location",
						"id" => $l_obj->id(),
						"return_url" => get_ru(),
					));
					$e_url = $this->mk_my_orb("change", array(
						"class" => "scm_event",
						"id" => $e_obj->id(),
						"return_url" => get_ru(),
					));
					$t_url = $this->mk_my_orb("change", array(
						"class" => "scm_tournament",
						"id" => $t_obj->id(),
						"return_url" => get_ru(),
					));
					$c_url = $this->mk_my_orb("change", array(
						"class" => "scm_competition",
						"id" => $obj->id(),
						"return_url" => get_ru(),
					));

					$link = html::href(array(
						"url" => "%s",
						"caption" => 
							"%s",
					));

					$event = obj($comp->get_event(array("competition" => $oid)));
					$team = $event->prop("type");
					$team_str = ($team == "single")?t("Ei"):t("Jah");

					$t->define_data(array(
						"competition" => sprintf($link, $c_url, $obj->name()),
						"date" => date("d / m / Y", $obj->prop("date")),
						"register" => $obj->id(),
						"location" => sprintf($link, $l_url, $l_obj->name()),
						"event" => sprintf($link, $e_url, $e_obj->name()),
						"tournament" => sprintf($link, $t_url, $t_obj->name()),
						"organizer" => $org_company->name(),
						"team" => $team_str,
					));
				}
			break;

			case "comp_tbl":
				$t = &$prop["vcl_inst"];
				$this->_gen_tbl(&$t);
				// add special table fields
				$t->define_chooser(array(
					"name" => "unreg",
					"field" => "unregister",
				));

				// insert data
				$filt = array(
					"contestant" => $arr["obj_inst"]->id(),
					"registered" => true,
				);
				$comp = get_instance(CL_SCM_COMPETITION);
				foreach($comp->get_competitions($filt) as $oid => $obj)
				{
					$event = obj($comp->get_event(array("competition" => $oid)));
					$team = $event->prop("type");
					if($team == "single")
					{
						$team_str = t("Ei");
					}
					else
					{
						$url = $this->mk_my_orb("change", array(
							"set_team" => $oid,
							"group" => $arr["request"]["group"],
							"id" => $arr["request"]["id"],
							"return_url" => $arr["request"]["return_url"],
						));
						$missing = "<font color=\"red\">".t("Meeskonnad m&auml;&auml;ramata")."</font>";
						// checks if there are any teams assigned
						$has_team = false;
						foreach($this->get_teams(array("contestant" => $arr["obj_inst"])) as $team)
						{
							$team_obj = obj($team);
							$sel_competitions = $team_obj->prop("competitions");
							if(in_array($oid, $sel_competitions))
							{
								$has_team = true;
								break;
							}
						}
						//
						$add = html::href(array(
							"url" => $url,
							"caption" => t("M&auml;&auml;ra meeskond"),
						));
						$team_str = t("Jah").($has_team?"":"<br/>".$missing)."<br/>".$add;
					}
					$t->define_data(array(
						"competition" => $obj->name(),
						"team" => $team_str,
						"unregister" => $oid,
					));
				}
			break;

			case "sel_teams":
				if(empty($arr["request"]["set_team"]))
				{
					return PROP_IGNORE;
				}
				$prop["name"] = "sel_teams[".$arr["request"]["set_team"]."]";
				$prop["options"][-1] = t("-Vali meeskond-");
				foreach($this->get_teams(array("contestant" => $arr["obj_inst"])) as $team)
				{
					$team_obj = obj($team);
					$sel_competitions = $team_obj->prop("competitions");
					if(in_array($arr["request"]["set_team"], $sel_competitions))
					{
						$prop["selected"][] = $team;
					}
					$prop["options"][$team] = $team_obj->name();
				}
			break;
			case "comp_caption":
				if(empty($arr["request"]["set_team"]))
				{
					return PROP_IGNORE;
				}
				$obj = obj($arr["request"]["set_team"]);
				$prop["value"] = $obj->name();
			break;
			case "teams_submit":
				if(empty($arr["request"]["set_team"]))
				{
					return PROP_IGNORE;
				}
			break;

			case "contestants_company":
				$o = obj($this->get_contestant_company(array("contestant" => $arr["obj_inst"]->id())));
				$prop["value"] = $o->name();
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
			case "reg_tbl":
				foreach($arr["request"]["reg"] as $oid)
				{
					$obj = obj($oid);
					$obj->connect(array(
						"to" => $arr["obj_inst"]->id(),
						"type" => "RELTYPE_CONTESTANT",
					));
				}
			break;

			case "sel_teams":
				if(!strlen($prop["value"]))
				{
					return PROP_IGNORE;
				}
				$all_teams = $this->get_teams(array("contestant" => $arr["obj_inst"]->id()));

				$competition = key($prop["value"]);
				$save_team = current($prop["value"]);
				unset($prop["value"][$competition][-1]);
				foreach($all_teams as $team)
				{
					$obj = obj($team);
					$comps = $obj->prop("competitions");
					if($team == $save_team && !in_array($competition, $comps))
					{
						$comps[] = $competition;
					}
					elseif($team != $save_team && in_array($competition, $comps))
					{
						foreach($comps as $k => $v)
						{
							if($v == $competition)
							{
								unset($comps[$k]);
							}
						}
					}
					$obj->set_prop("competitions", $comps);
					$obj->save();
				}
			break;

			case "comp_tbl":
				if(count($arr["request"]["unreg"]))
				{
					$conn = new connection();
					$conns = $conn->find(array(
						"to" => $arr["obj_inst"]->id(),
						"from" => $arr["request"]["unreg"],
						"type" => "6",
					));
					foreach($conns as $cid => $conn)
					{
						$cobj = new connection($cid);
						$cobj->delete();
					}
				}
			break;
		}
		return $retval;
	}	

	function callback_post_save($arr)
	{
		$arr["obj_inst"]->set_name($arr["obj_inst"]->prop_str("contestant"));
		$arr["obj_inst"]->save();
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
		@attrib name=gen_new_contestant_sheet params=name all_args=1
	**/
	function gen_new_contestant_sheet($arr)
	{
		$comp = obj($arr["parent"]);
		$gr = $comp->prop("scm_group");
		foreach($gr as $group)
		{
			$obj = obj($group);
			$groups[$group] = $obj->name();
		}
		$htmlc = get_instance("cfg/htmlclient");
		$htmlc->start_output();
		if($arr["team"])
		{
			$o = obj($arr["team"]);
			$htmlc->add_property(array(
				"name" => "team_name",
				"type" => "text",
				"store" => "no",
				"caption" => t("Meeskond"),
				"value" => $o->name(),
			));
			$htmlc->add_property(array(
				"name" => "team",
				"type" => "hidden",
				"store" => "no",
				"value" => $o->id(),
			));
		}
		$htmlc->add_property(array(
			"name" => "firstname",
			"type" => "textbox",
			"store" => "no",
			"caption" => t("Eesnimi"),
		));
		$htmlc->add_property(array(
			"name" => "lastname",
			"type" => "textbox",
			"store" => "no",
			"caption" => t("Perekonnanimi"),
		));
		$htmlc->add_property(array(
			"name" => "gender",
			"type" => "chooser",
			"store" => "no",
			"caption" => t("Sugu"),
			"options" => array(
				1 => t("Mees"),
				2 => t("Naine"),
			),
		));
		$htmlc->add_property(array(
			"name" => "birthday",
			"type" => "date_select",
			"default" => -1,
			"store" => "no",
			"caption" => t("S&uuml;nniaeg"),
			"year_from" => 1900,
			"year_to" => 2006,
		));
		if(!$arr["do_not_register"])
		{
			$htmlc->add_property(array(
				"name" => "group",
				"type" => "select",
				"multiple" => 1,
				"store" => "no",
				"options" => $groups,
				"caption" => t("V&otilde;istlusklass"),
			));
			$htmlc->add_property(array(
				"name" => "id",
				"type" => "textbox",
				"store" => "no",
				"caption" => t("S&auml;rgi number"),
			));
		}
		else
		{
			$htmlc->add_property(array(
				"name" => "do_not_register",
				"type" => "hidden",
				"store" => "no",
				"value" => true,
			));
		}
		$htmlc->add_property(array(
			"name" => "phone",
			"type" => "textbox",
			"store" => "no",
			"caption" => t("Telefon"),
		));
		$htmlc->add_property(array(
			"name" => "email",
			"type" => "textbox",
			"store" => "no",
			"caption" => t("E-Mail"),
		));
		$htmlc->add_property(array(
			"name" => "submit",
			"type" => "submit",
			"value" => "lisa",
			"action" => "do_process_contestant_sheet",
		));
		$htmlc->add_property(array(
			"name" => "submit_and_add",
			"type" => "submit",
			"value" => "lisa ja uus",
			"action" => "do_process_contestant_sheet_with_new",
		));
		$htmlc->add_property(array(
			"name" => "parent",
			"type" => "hidden",
			"value" => $arr["parent"],
		));
		$htmlc->finish_output(array(
			"data" => array(
				"orb_class" => "scm_contestant",
			),
		));
		return $htmlc->get_result();
	}
	
	/**
		@attrib name=do_process_contestant_sheet_with_new params=name all_args=1
	**/
	function do_process_contestant_sheet_with_new($arr)
	{
		$arr["add_more"] = true;
		$this->do_process_contestant_sheet($arr);
	}
	
	/**
		@attrib name=do_process_contestant_sheet params=name all_args=1
	**/
	function do_process_contestant_sheet($arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_CRM_PERSON,
			"CL_CRM_PERSON.firstname" => $arr["firstname"],
			"CL_CRM_PERSON.lastname" => $arr["lastname"],
			"CL_CRM_PERSON.gender" => $arr["gender"],
			"CL_CRM_PERSON.birthday" => sprintf("%04d-%02d-%02d", $arr["birthday"]["year"], $arr["birthday"]["month"], $arr["birthday"]["day"]),
		));
		
		// leian kas olemasoleva crm_personi või tekitan uue
		if($ol->count())
		{
			$o_person = $ol->begin();
		}
		else
		{
			$o_person = obj();
			$o_person->set_class_id(CL_CRM_PERSON);
			$o_person->set_name($arr["firstname"]." ".$arr["lastname"]);
			$o_person->set_prop("firstname", $arr["firstname"]);
			$o_person->set_prop("lastname", $arr["lastname"]);			
			$o_person->set_parent($arr["parent"]);
			$o_person->set_prop("birthday", sprintf("%04d-%02d-%02d", $arr["birthday"]["year"], $arr["birthday"]["month"], $arr["birthday"]["day"]));
			$o_person->set_prop("gender", $arr["gender"]);
			$o_person->save();

		}
		// kui email olemas, lisan crm_personile
		if(strlen($arr["email"]))
		{
			$e_obj = obj();
			$e_obj->set_class_id(CL_ML_MEMBER);
			$e_obj->set_parent($o_person->id());
			$e_obj->set_prop("mail", $arr["email"]);
			$e_obj->set_prop("mail", $arr["email"]);
			$e_id = $e_obj->save();
			$o_person->set_prop("email", $e_id);
			$o_person->connect(array(
				"to" => $e_id,
				"type" => "RELTYPE_EMAIL"
			));
			$o_person->save();
		}
		// kui telefon olemas, lisan crm_personile
		if(strlen($arr["phone"]))
		{
			$e_obj = obj();
			$e_obj->set_class_id(CL_CRM_PHONE);
			$e_obj->set_parent($o_person->id());
			$e_obj->set_prop("name", $arr["phone"]);
			$e_obj->set_prop("type", "mobile");
			$e_id = $e_obj->save();
			$o_person->set_prop("phone", $e_id);
			$o_person->connect(array(
				"to" => $e_id,
				"type" => "RELTYPE_PHONE"
			));
			$o_person->save();
		}

		$comp_inst = get_instance(CL_SCM_COMPETITION);


		// tekitan scm_contestant_objekti
		$o = obj();
		$o->set_name($o_person->name());
		$o->set_parent($arr["parent"]);
		$o->set_class_id(CL_SCM_CONTESTANT);
		$o->save();
		
		// ühendan scm_contestant'i crm_personiga
		$o->connect(array(
			"to" => $o_person->id(),
			"type" => "RELTYPE_CONTESTANT",
		));
		$o->set_prop("contestant", $o_person->id());
		$cnt_id = $o->save();

		if(!$arr["do_not_register"])
		{
			$competition = obj($arr["parent"]);
		
			// ühendan scm_competition'i kas võistleja või tiimiga
			if($arr["team"])
			{
				$comp_inst->add_contestant_to_team_and_register(array(
					"contestant" => $cnt_id,
					"competition" => $arr["parent"],
					"team" => $arr["team"],
					"contestant_id" => $arr["id"],
					"groups" => $arr["group"],
				));
			}
			else
			{
				$competition->connect(array(
					"type" => "RELTYPE_CONTESTANT",
					"to" => $cnt_id,
				));
				
				// otsin just tekitatud ühendust, et seal seadeid muuta
				// tiimi puhul tehti see seal vinges funktsioonis kõik ära..
				$c = new connection();
				$conns = $c->find(array(
					"to" => ($arr["team"])?$arr["team"]:$cnt_id,
					"from" => $arr["parent"],
				));
				$data = $comp_inst->get_rel_data(key($conns));

				$data["data"]["groups"] = $arr["group"];
				$data["data"]["id"] = $arr["id"];
				$comp_inst->save_rel_data($data);
			}
		}
		else
		{
			if($arr["team"])
			{
				// registreedida pole vaja, võistlja tuleb lihtsalt meeskonna liikmeks panna
				$o = obj($arr["team"]);
				$o->connect(array(
					"to" => $cnt_id,
					"type" => "RELTYPE_SCM_TEAM_MEMBER"
				));
			}
			else
			{
				// basically i need to do shitload of nothing here.. groovy
			}
		}
		
		if($arr["add_more"])
		{
			$url = $this->mk_my_orb("gen_new_contestant_sheet", array(
				"parent" => $arr["parent"],
				"team" => $arr["team"],
			), CL_SCM_CONTESTANT);
			header("Location: $url");
		}
		$exit = "<body onLoad=\"javascript:window.opener.location.reload(true);javascript:self.close();\"></body>";
		die($exit);
	}
	
	function _gen_tbl(&$t)
	{
		$t->define_field(array(
			"name" => "competition",
			"caption" => t("V&otilde;istlus"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "event",
			"caption" => t("Spordiala"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "team",
			"caption" => t("Meeskondlik"),
			"sortable" => 1,
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "location",
			"caption" => t("Toimumiskoht"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "date",
			"caption" => t("Toimumisaeg"),
			"sortable" => 1,
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "organizer",
			"caption" => t("Korraldaja"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "tournament",
			"caption" => t("V&otilde;istlussari"),
			"sortable" => 1,
		));

	}

	/**
		@param contestant required type=int
			csm_contestant object id
		@comment
			fetches company
		@returns
			crm_company object id
	**/
	function get_contestant_company($arr = array())
	{
		$o = obj($this->get_contestant_person($arr));
		return ($s = $o->company_id())?$s:false;
	}

	/**
		@param contestant required type=int
			csm_contestant object id
		@comment
			fetches person
		@returns
			crm_person object id
	**/
	function get_contestant_person($arr = array())
	{
		$obj = obj($arr["contestant"]);
		return ($s = $obj->prop("contestant"))?$s:false;
	}

	/**
		@attrib api=1
		@comment
			generates list of contestant objects
		@returns
			array of contestants.
			array(
				scm_contestant object_id
				scm_contestant object_inst
			)
	**/
	function get_contestants()
	{
		$list = new object_list(array(
			"class_id" => CL_SCM_CONTESTANT,
		));
		return $list->arr();
	}

	function get_teams($arr = array())
	{
		$obj = obj($arr["contestant"]);
		$teams = $obj->prop("teams");
		return $teams;
	}
}
?>
