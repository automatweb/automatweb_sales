<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/scm/scm_competition.aw,v 1.20 2007/12/06 14:34:06 kristo Exp $
// scm_competition.aw - V&otilde;istlus 
/*

@classinfo syslog_type=ST_SCM_COMPETITION relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=tarvo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property relation_data type=hidden

@groupinfo sub_general caption="&Uuml;ldine" parent=general
	@default group=sub_general

	@property name type=textbox maxlength=255
	@caption Nimi

	@property scm_event type=relpicker reltype=RELTYPE_EVENT editonly=1
	@caption Spordiala

	@property location type=relpicker reltype=RELTYPE_LOCATION editonly=1
	@caption Asukoht

	@property date_from type=text
	@caption Algus

	@property date_to type=date_select
	@caption L&otilde;pp

	@property scm_tournament type=relpicker reltype=RELTYPE_TOURNAMENT editonly=1 multiple=1
	@caption V&otilde;istlussari

	@property scm_group type=relpicker reltype=RELTYPE_GROUP multiple=1 editonly=1
	@caption V&otilde;istlusklassid

@groupinfo general_settings caption="M&auml;&auml;rangud" parent=general
	@default group=general_settings

	@property scm_score_calc type=relpicker reltype=RELTYPE_SCORE_CALC editonly=1
	@caption Punktis&uuml;steem

	@property scm_group_box type=textarea cols=50 rows=6
	@caption Gruppide lisainfo

	@property scm_group_consider type=textbox size=4
	@caption Igast grupist arvesse

	@property archive type=checkbox ch_value=1
	@caption Arhiveeritud

	@property register type=select
	@caption Registreerumine

	@property group_select_type type=chooser default=year
	@caption V&otilde;istlusklassi valik

	@property team_result_calc type=select editonly=1
	@caption V&otilde;istkonna tulemuse arvutus

	@property result_type type=relpicker reltype=RELTYPE_RESULT_TYPE editonly=1
	@caption Paremusj&auml;rjestuse t&uuml;&uuml;p

	@property unique_nr type=checkbox ch_value=1 default=1
	@caption Unikaalne rinnanumber


@groupinfo group_comments caption="V&otilde;istlusklasside kommentaarid" parent=general
	@default group=group_comments
	@property scm_groups_comment type=table editonly=1 no_caption=1

@groupinfo map_gr caption="Kaart" submit=no
	@property map type=text group=map_gr
	@caption Asukohakaart

@groupinfo photo_gr caption="Foto" submit=no
	@property photo type=text group=photo_gr
	@caption Pilt kohast

@groupinfo contestants caption="Osalejad" submit=no
	@groupinfo manage_groups caption="V&otilde;istkonnad" parent=contestants
		@default group=manage_groups

		@property search_res_team type=hidden name=search_result_teams store=no

		@property teams_tb type=toolbar no_caption=1

		@property teams type=table no_caption=1
		@caption Meeskondade nimekiri

	@groupinfo manage_contestants caption="V&otilde;istlejad" parent=contestants
		@default group=manage_contestants

		@property contestants_tb type=toolbar no_caption=1

		@property search_res type=hidden name=search_result no_caption=1 store=no

		@property contestants type=table no_caption=1
		@caption V&otilde;istlejate nimekiri

@groupinfo results caption="Tulemused" submit=no
	@groupinfo view_results parent=results caption="Tulemused" submit=no
		@property results_tbl type=table group=view_results no_caption=1
		@caption Tulemuste tabel

	@groupinfo add_results parent=results caption="Sisesta tulemused"
		@property add_results_tb type=toolbar group=add_results no_caption=1

		@property add_results_tbl type=table group=add_results no_caption=1
		@caption Tulemuste lisamine

@reltype EVENT value=1 clid=CL_SCM_EVENT
@caption Spordiala

@reltype LOCATION value=2 clid=CL_LOCATION
@caption Asukoht

@reltype TOURNAMENT value=3 clid=CL_SCM_TOURNAMENT
@caption V&otilde;istlussari

@reltype SCORE_CALC value=4 clid=CL_SCM_SCORE_CALC
@caption Punktis&uuml;steem

@reltype GROUP value=5 clid=CL_SCM_GROUP
@caption V&otilde;istlusgrupp

@reltype CONTESTANT value=6 clid=CL_SCM_CONTESTANT,CL_SCM_TEAM
@caption V&otilde;istleja

@reltype RESULT_TYPE value=7 clid=CL_SCM_RESULT_TYPE
@caption Paremusj&auml;rjestuse t&uuml;&uuml;p

*/

class scm_competition extends class_base
{
	function scm_competition()
	{
		$this->init(array(
			"tpldir" => "applications/scm",
			"clid" => CL_SCM_COMPETITION
		));

		$this->register_types = array(
			"0" => t("Avalik"),
			"1" => t("Piiratud"),
			"2" => t("Registreerumine l&otilde;ppenud"),
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- get_property --//
			case "map":
				$prop["value"] = $this->_get_image($arr);
			break;
			case "photo":
				$prop["value"] = $this->_get_image($arr);
			break;

			case "date_from":
				$ts = get_instance("vcl/date_edit");
				$name = array(
					"name" => "datetime_form",
					"size" => 11,
				);
				$ts->configure(array(
					"day" => "day",
					"month" => "month",
					"year" => "year",
					"hour" => "hour",
					"minute" => "minute",
				));
				$form = $ts->gen_edit_form($name, $arr["obj_inst"]->prop("date_from"));
				$prop["value"] = $form;
			break;

			case "scm_groups_comment":
				$t = &$prop["vcl_inst"];
				$t->define_field(array(
					"name" => "group",
					"caption" => t("V&otilde;istlusklass"),
				));
				$t->define_field(array(
					"name" => "comment",
					"caption" => t("Kommentaar"),
				));
				$comments = $this->get_groups_comments($arr["obj_inst"]->id());
				foreach($comments as $group => $comment)
				{
					$textbox = html::textarea(array(
						"name" => "groups_comment[".$group."]",
						"value" => $comment,
					));
					$group = obj($group);
					$t->define_data(array(
						"group" => $group->name(),
						"comment" => $textbox,
					));
				}
			break;

			case "teams_tb":
				$et = ($evt = ($s = $arr["obj_inst"]->prop("scm_event"))?obj($s):false)?call_user_method("prop", $evt, "type"):false;
				if($et == "single")
				{
					return PROP_IGNORE;
				}
				$tb = &$prop["vcl_inst"];
				$tb->add_button(array(
					"name" => "add_team",
					"img" => "new.gif",
					"tooltip" => t("Lisa uus meeskond"),
					"url" => $this->mk_my_orb("prep_new_team", array(
						"id" => $arr["obj_inst"]->id(),
						"return_url" => get_ru(),
					)),
				));

				$popup_search = get_instance("vcl/popup_search");
				$search_butt = $popup_search->get_popup_search_link(array(
					"pn" => "search_result_teams",
					"clid" => CL_SCM_TEAM,
				));

				$tb->add_cdata($search_butt);
				$tb->add_button(array(
					"name" => "remove_team",
					"tooltip" => t("Eemalda meeskonnad"),
					"img" => "delete.gif",
					"action" => "unregister_team",
				));

				$this->_gen_groups_change_toolbar_addon(array(
					"tb" => &$tb,
					"competition" => $arr["obj_inst"]->id(),
				));
			break;

			case "teams":
				$evt = $this->get_event(array(
					"competition" => $arr["obj_inst"]->id(),
				));
				$e = $evt?obj($evt):false;
				if($e && $e->prop("type") == "single")
				{
					$prop["value"] = t("Individuaalne v&otilde;istlus");
					return $retval;
				}
				$t = &$prop["vcl_inst"];

				$team_inst = get_instance(CL_SCM_TEAM);
				$teams = $this->get_teams(array(
					"competition" => $arr["obj_inst"]->id(),
				));
				foreach($teams as $tid => $obj)
				{
					$extra_data = $this->get_extra_data(array(
						"competition" => $arr["obj_inst"]->id(),
						"team" => $tid,
					));
					$t_cid[$tid] = $extra_data["conn"]->id();
					$groups = array_flip($extra_data["data"]["groups"]);			
					foreach($groups as $gid => $null)
					{
						$n_groups[$tid][$gid] = call_user_method("prop", obj($gid), "abbreviation");
						$grps[$gid] = $n_groups[$tid][$gid];
					}
				}
				$this->_gen_teams_tbl(&$t, $grps);

				foreach($teams as $tid => $obj)
				{
					$js = $this->_get_table_js(array(
						"nr" => $tid,
						"url" => $this->mk_my_orb("get_contestants_row_table", array(
							"class" => "scm_team",
							"team" => $tid,
							"competition" => $arr["obj_inst"]->id(),
							"return_url" => get_ru(),
						)), 
					));
					$memb = $team_inst->get_team_members(array(
						"team" => $tid,
						"competition" => $arr["obj_inst"]->id(),
					));
					$company = $this->_get_multi_company_name($memb);
					$url = $this->mk_my_orb("change", array(
						"class" => "scm_team",
						"id" => $tid,
						"return_url" => get_ru(),
					));
					$team_name = html::href(array(
						"caption" => $obj->name(),
						"url" => $url,
					));
					$t->define_data(array(
						"team" => $team_name." ".$js,
						"selected" => $tid.".".$t_cid[$tid],
						"company" => $company,
						"groups" => $n_groups[$tid],
					));

				}

			break;
			case "contestants_tb":
				$tb = &$prop["vcl_inst"];
				$et = ($evt = $arr["obj_inst"]->prop("scm_event"))?call_user_method("prop", obj($evt), "type"):false;
				if($et == "single")
				{
					$url = $this->mk_my_orb("gen_new_contestant_sheet",array(
						"parent" => $arr["obj_inst"]->id(),
					),CL_SCM_CONTESTANT);
					$tb->add_button(array(
						"name" => "add_contestant",
						"tooltip" => t("Lisa uus v&otilde;istleja"),
						"img" => "new.gif",
						"url" => "javascript:aw_popup_scroll('".$url."', 'title', 500,500);",
						/*
						"url" => $this->mk_my_orb("new", array(
							"class" => "scm_contestant",
							"parent" => $this->get_organizer(array("competitions" => ($id = $arr["obj_inst"]->id()))),
							"alias_to" => $id,
							"reltype" => 6,
							"return_url" => get_ru(),
						)),
						*/
					));
				}
				elseif($et == "multi" || $et == "multi_coll")
				{
					$tb->add_menu_button(array(
						"name" => "new",
						"img" => "new.gif",
						"tooltip" => t("Lisa uus v&otilde;istkonnaliige"),
					));
					$team_inst = get_instance(CL_SCM_TEAM);
					$teams = $team_inst->get_teams();
					foreach($teams as $oid => $obj)
					{
						$teams_n[$oid] = $obj->name();
					}
					asort($teams_n);
					foreach($teams_n as $oid => $name)
					{
						$url = $this->mk_my_orb("gen_new_contestant_sheet",array(
							"parent" => $arr["obj_inst"]->id(),
							"team" => $oid,
						),CL_SCM_CONTESTANT);

						$tb->add_menu_item(array(
							"parent" => "new",
							"name" => "team.".$oid,
							"text" => $name,
							"url" => "javascript:aw_popup_scroll('".$url."', 'title', 500,500);",
							/*
							"url" => $this->mk_my_orb("add_contestant_to_team_and_register", array(
								"team" => $oid,
								"competition" => $arr["obj_inst"]->id(),
								"return_url" => get_ru(),
							)),
							*/
						));
					}
				}
				
				$evt = $this->get_event(array("competition" => $arr["obj_inst"]->id()));
				$e = $evt?obj($evt):false;
				$clid = ($e && $e->prop("type") == "single")?CL_SCM_CONTESTANT:CL_SCM_TEAM;
				if($clid == CL_SCM_CONTESTANT)
				{
					$popup_search = get_instance("vcl/popup_search");
					$search_butt = $popup_search->get_popup_search_link(array(
						"pn" => "search_result",
						"clid" => $clid,
					));

					$tb->add_cdata($search_butt);
				}
				$tb->add_button(array(
					"name" => "save_state",
					"tooltip" => t("Salvesta"),
					"img" => "save.gif",
					"url" => "#",
					"onClick" => "javascript:submit_changeform()",
				));
				$tb->add_button(array(
					"name" => "delete",
					"tooltip" => t("Eemalda v&otilde;istluselt"),
					"img" => "delete.gif",
					"action" => "unregister_cnt",
				));

				$this->_gen_groups_change_toolbar_addon(array(
					"tb" => &$tb,
					"competition" => $arr["obj_inst"]->id(),
				));
			break;
			case "contestants":
				$t = &$prop["vcl_inst"];
				$cont = get_instance(CL_SCM_CONTESTANT);
				$contestants = $this->get_contestants(array(
					"competition" => $arr["obj_inst"]->id(),
					"ret_inst" => true,
				));
				foreach($contestants as $oid => $data)
				{
					$t_oid = $data["data"]["team"];
					if($t_oid)
					{
						$filters["teams"][$t_oid] = call_user_method("name", obj($t_oid));
					}
					foreach($data["data"]["groups"] as $g_oid)
					{
						$filters["groups"][$g_oid] = call_user_method("prop", obj($g_oid), "abbreviation");
					}
				}
				$evt = $this->get_event(array("competition" => $arr["obj_inst"]->id()));
				$e = $evt?obj($evt):false;

				$team = ($e && $e->prop("type") == "single")?false:true;
				// gen table structure
				$this->_gen_cont_tbl(&$t, $filters, $team);
				// get event type (single, multi, multi_coll) 
				$event = $this->get_event(array(
					"competition" => $arr["obj_inst"]->id(),
				));
				$event_type = ($event)?call_user_method("prop", obj($event), "type"):false;


				foreach($contestants as $oid => $data)
				{
					// siin peaks v&auml;lja raalima kas on olemas seoses v&otilde;istlusklassi info, kui pole sis selle seosesse kirjutama
					$person = obj($cont->get_contestant_person(array("contestant" => $oid)));

					// gender
					if(!($sex = $person->prop("gender")))
					{
						$sex = ($pid)?(!($pid[0]&1)?1:2):false;
					}
					// date of birth
					if(($s = $person->prop("birthday")))
					{
						list($y, $m, $d) = explode("-", $s);
						$dob = mktime(0,0,0, $m, $d, $y);
					}
					elseif(($pid = $person->prop("personal_id")))
					{
						$a = array(
							1 => 18,
							2 => 18,
							3 => 19,
							4 => 19,
							5 => 20,
							6 => 20,
						);
						$dob = mktime(0,0,0, substr($pid, 3, 2), substr($pid, 5, 2),$a[$pid[0]].substr($pid, 1, 2));
					}
					else
					{
						$dob = false;
					}
					
					// relation check 
					// whell... this basically does following:
					// checks if connection between competition and contestant has data that holds group info
					// if not, tries to figure out itself in which groups the contestant should be.. and puts them into these,
					// if needed, user can change the groups manually later...
					if(count($data["data"]["groups"]))
					{
						$groups = $data["data"]["groups"];
					}
					else
					{
						// siin peaks ta n&uuml;&uuml;d kudagi gruppidesse jaotama
						$cmp_groups = $this->get_groups(array(
							"competition" => $arr["obj_inst"]->id(),
						));
						unset($groups);
						foreach($cmp_groups as $group)
						{
							$o = obj($group);
							$ch = true;
							if(($s = $arr["obj_inst"]->prop("group_select_type")) == "year" && $dob)
							{
								$ch = (((date("Y", $dob) >= $o->prop("age_from")) || !$o->prop("age_from")) && ((date("Y", $dob) <= $o->prop("age_to")) || !$o->prop("age_to")))?$ch:false;
							}
							elseif($s == "age" || $dob)
							{
								$age = $this->get_age($dob);
								$ch = ((($age >= $o->prop("age_from") || !$o->prop("age_from"))) && ($age <= $o->prop("age_to") || !$o->prop("age_to")))?$ch:false;
							}
							$ch = (($o->prop("female") && $sex == 2) || ($o->prop("male") && $sex == 1))?$ch:false;
							if($ch)
							{
								$groups[] = $group;
							}
						}
						$data["data"]["groups"] = $groups;
						$data["connection"]->change(array(
							"data" => aw_serialize($data["data"], SERIALIZE_NATIVE),
						));
					}
					
					// group names
					unset($ngroups);
					foreach($groups as $group)
					{
						$ngroups[] = call_user_method("prop", obj($group), "abbreviation");
					}

					// finds out if he/she belongs to any team...
					if(strlen($data["data"]["team"]))
					{
						$team = obj($data["data"]["team"]);
					}

					// contacts
					if(($ph = $person->prop("phone")) || $person->prop("email"))
					{
						$ph = $ph?obj($ph):false;
						$em = ($em = $person->prop("email"))?obj($em):false;
						$contact = $ph?$ph->name()." (".$ph->prop("type").")":t("Telefoninumber puudub");
						$contact .= ",<br/>";
						$contact .= $em?$em->prop("mail"):t("E-mailiaadress puudub");
					}
					else
					{
						$contact = t("Andmed puuduvad");
					}
					$company = obj($cont->get_contestant_company(array(
						"contestant" => $oid
					)));
					$id = ($event_type == "multi" || $event_type == "multi_coll")?$data["id"]:$data["data"]["id"];

					// links
					if($event_type != "single")
					{
						$team_name = html::href(array(
							"caption" => $team->name(),
							"url" => $this->mk_my_orb("change", array(
								"class" => "scm_team",
								"id" => $team->id(),
								"return_url" => get_ru(),
							)),
						));
					}
					else
					{
						$team_name = t("-");
					}
					$t->define_data(array(
						"name" => html::href(array(
							"caption" => $person->prop("lastname").", ".$person->prop("firstname"),
							"url" => $this->mk_my_orb("change", array(
								"class" => "scm_contestant",
								"id" => $oid,
								"return_url" => get_ru(),
							)),
						)),
						"company" => $company->name(),
						"sex" => (($s = $sex) == 1)?t("Mees"):(($s == 2)?t("Naine"):t("Sugu m&auml;&auml;ramata")),
						"birthday" => $dob,
						"team" => $team_name,
						"groups" => $ngroups,
						"id" => $id.".".$oid.".".$data["connection"]->id(),
						"contact" => $contact,
						"sel_contestants" => $oid.".".$data["connection"]->id(),
					));
				}
				
			break;
			case "results_tbl":
				$t = &$prop["vcl_inst"];
				$competition = $arr["obj_inst"]->id();
				$event_oid = $this->get_event(array(
					"competition" => $competition,
				));
				$event = obj($event);

				$res_inst = get_instance(CL_SCM_RESULT);
				$et = ($event_oid)?(call_user_method("prop", obj($event_oid), "type")):false;
				if($et == "multi" || $et == "single")
				{
					$results = $res_inst->get_results(array(
						"competition" => $competition,
						"type" => "contestant",
					));
				}
				else
				{
					$results = $res_inst->get_results(array(
						"competition" => $competition,
						"type" => "team",
					));
				}
				$results = $this->convert_results($results, $competition);

				$res_type = $this->get_result_type(array(
					"competition" => $competition,
				));
				$type_inst = get_instance(CL_SCM_RESULT_TYPE);
				$format = $type_inst->get_format(array(
					"result_type" => $res_type
				));
				// finding out all groups used in competition(for table filter)
				foreach($results as $k => $data)
				{
					$groups = array_merge($groups, $data["groups"]);
					unset($tmp);
					foreach($data["groups"] as $group)
					{
						$o = obj($group);
						$tmp[$group] = $o->prop("abbreviation");
					}
					$results[$k]["groups"] = $tmp;
					$merged = array_merge($merged, $tmp);
				}
				
				$this->_gen_res_tbl(&$t, $et, array_unique($merged));
				
				krsort($results, SORT_NUMERIC);
				foreach($results as $data)
				{
					$data["result"] = $this->_gen_format_caption(array(
						"result" => $data["result_arr"],
						"format" => $format,
					));
					$t->define_data($data);
				}
			break;
			case "add_results_tbl":

				$archive = ($arr["obj_inst"]->prop("archive"));
				$t = &$prop["vcl_inst"];
				$event = $this->get_event(array("competition" => $arr["obj_inst"]->id()));
				$o = obj($event);
				$event_type = $o->prop("type");
				$this->_gen_add_res_tbl(&$t, $event_type);
				$conts = $this->get_contestants(array(
					"competition" => $arr["obj_inst"]->id()
				));
				$res_type = $this->get_result_type(array(
					"competition" => $arr["obj_inst"]->id(),
				));
				if(!$res_type)
				{
					return PROP_IGNORE;
				}

				$type_inst = get_instance(CL_SCM_RESULT_TYPE);
				$format = $type_inst->get_format(array(
					"result_type" => $res_type
				));
				$res_inst = get_instance(CL_SCM_RESULT);

				// result field

				if($event_type == "multi_coll")
				{
					$results = $res_inst->get_results(array(
						"type" => "team",
						"competition" => $arr["obj_inst"]->id(),
					));
					$team_inst = get_instance(CL_SCM_TEAM);
					$cont_inst = get_instance(CL_SCM_CONTESTANT);
					foreach($results as $result)
					{
						$memb = $team_inst->get_team_members(array(
							"team" => $result["team"],
							"competition" => $arr["obj_inst"]->id(),
						));
						unset($team_companys);
						foreach($memb as $oid => $obj)
						{
							$team_companys[$cont_inst->get_contestant_company(array("contestant" => $oid))] = 1;
						}
						
						$company = (count($team_companys) > 1)?t("Segav&otilde;istkond"):call_user_method("name", obj(key($team_companys)));
						$format_nice = $this->_gen_format_nice(array(
							"format" => $format,
							"result" => $result,
							"team" => $result["team"],
							"competition" => $result["competition"],
						));
						$team_obj = obj($result["team"]);
						$team_name = $team_obj->name();
						$data[] = array(
							"team_name" => html::href(array(
								"caption" => $team_name,
								"url" => $this->mk_my_orb("change", array(
									"class" => "scm_team",
									"id" => $result["team"],
									"return_url" => get_ru(),
								)),
							)), 
							"company" => $company,
							"result" => $format_nice,
						);
					}
				}
				elseif($event_type == "multi")
				{
					$results = $res_inst->get_results(array(
						"competition" => $arr["obj_inst"]->id(),
						"type" => "contestant",
					));

					if(count($results))
					{
						foreach($results as  $result)
						{
							$format_nice = $this->_gen_format_nice(array(
								"format" => $format,
								"result" => $result,
								"contestant" => $result["contestant"],
								"competition" => $result["competition"],
								"disable" => $archive,
							));

							$cont = get_instance(CL_SCM_CONTESTANT);
							$person = obj($cont->get_contestant_person(array("contestant" => $result["contestant"])));
							$company = obj($cont->get_contestant_company(array("contestant" => $result["contestant"])));
							$data[] = array(
								"contestant" => $person->prop("lastname").", ".$person->prop("firstname"),
								"team_name" => $result["team"],
								"company" => $company->name(),
								"result" => $format_nice,
								"id" => $result["id"],
							);
						}
					}

				}
				elseif($event_type == "single")
				{
					$results = $res_inst->get_results(array(
						"type" => "contestant",
						"competition" => $arr["obj_inst"]->id(),
					));
					foreach($results as  $result)
					{
						$format_nice = $this->_gen_format_nice(array(
							"format" => $format,
							"result" => $result,
							"contestant" => $result["contestant"],
							"competition" => $result["competition"],
						));

						$cont = get_instance(CL_SCM_CONTESTANT);
						$person = obj($cont->get_contestant_person(array("contestant" => $result["contestant"])));
						$company = obj($cont->get_contestant_company(array("contestant" => $result["contestant"])));
						$p_name = $person->prop("lastname").", ".$person->prop("firstname");
						$data[] = array(
							"contestant" => html::href(array(
								"caption" => $p_name,
								"url" => $this->mk_my_orb("change", array(
									"class" => "scm_contestant",
									"id" => $person->id(),
									"return_url" => get_ru(),
								)),
							)),
							"company" => $company->name(),
							"result" => $format_nice,
							"id" => $result["id"],
						);
					}
				}
				foreach($data as $row)
				{
					$t->define_data($row);
				}
			break;
			case "add_results_tb":
				$tb = &$prop["vcl_inst"];
				$tb->add_menu_button(array(
					"name" => "print",
					"img" => "print.gif",
				));
				$tb->add_sub_menu(array(
					"parent" => "print",
					"name" => "html",
					"text" => "HTML",
				));
				$tb->add_sub_menu(array(
					"parent" => "print",
					"name" => "pdf",
					"text" => "PDF",
				));
				 $groups = $this->get_groups(array("competition" => $arr["obj_inst"]->id()));
				 foreach($groups as $grid)
				 {
				 	$o = obj($grid);
					$tb->add_menu_item(array(
						"parent" => "html",
						"text" => ($name = $o->name())." (".($abr = call_user_method("prop", $o, "abbreviation")).")",
						"url" => $this->mk_my_orb("gen_protocol_html", array(
							"competition" => $arr["obj_inst"]->id(),
							"group" => $grid,
						)),
					));
					$tb->add_menu_item(array(
						"parent" => "pdf",
						"text" => $name." (".$abr.")",
						"url" => $this->mk_my_orb("gen_protocol_pdf", array(
							"competition" => $arr["obj_inst"]->id(),
							"group" => $grid,
						)),
					));
				 }
			break;

			case "register":
				$prop["options"] = $this->register_types;
			break;

			case "group_select_type":
				$prop["options"] = array(
					"year" => t("S&uuml;nniaasta alusel"),
					"age" => t("Vanuse alusel"),
				);
			break;

			case "team_result_calc":
				$o = obj($arr["obj_inst"]->id());
				$event = $o->prop("scm_event");
				if(($type = call_user_method("prop", obj($event), "type")) == "single" || $type == "multi_coll")
				{
					return PROP_IGNORE;
				}
				else
				{
					$inst = get_instance(CL_SCM_EVENT);
					$event = obj($event);
					$prop["options"] = $inst->get_alg();
					$prop["selected"] = $arr["obj_inst"]->prop("team_result_calc");
				}
			break;
			// to override original name prop
			case "name":
				$prop["value"] = $arr["obj_inst"]->name();
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
			case "add_results_tbl":
				$res_type = get_instance(CL_SCM_RESULT_TYPE);
				$res = get_instance(CL_SCM_RESULT);
				foreach($arr["request"]["res_contestant"] as $result => $data)
				{
					$obj = obj($result);
					$rev = $res_type->format_data(array(
						"source" => "result",
						"oid" => $result,
						"data" => $data,
						"reverse" => true,
					));
					$obj->set_prop("result", $rev);
					$obj->save();
				}
				foreach($arr["request"]["res_new_contestant"] as $competition => $tmp)
				{
					foreach($tmp as $contestant => $data)
					{
						$rev = $res_type->format_data(array(
							"source" => "competition",
							"oid" => $competition,
							"data" => $data,
							"reverse" => true,
						));
						$id = $res->add_result(array(
							"competition" => $competition,
							"contestant" => $contestant,
							"result" => $rev,
						));
					}
				}
				foreach($arr["request"]["res_team"] as $competition => $data)
				{
					foreach($data as $team => $result_data)
					{
						//siin on n&uuml;&uuml;d olemas result object.. ja team
						// &uuml;hes&otilde;naga ma saan team'i liikmed k&auml;tte.. ja neil on selle v&otilde;istlusega vb juba tulemus kellegil olemas, ma pean v&auml;lja raalima kas on ja kui on siis apdeitima.. kui pole sis uue tegema. IMEB!!
						// a seda ka et v&auml;hemalt &uuml;hel liikmel on olemas result objek.. mudu ta siia &uuml;ldse ei j&otilde;uaks, vist!!
						$inst = get_instance(CL_SCM_TEAM);
						$members = $inst->get_team_members(array(
							"team" => $team,
							"competition" => $competition,
						));
						foreach($members as $oid => $obj)
						{
							$list = new object_list(array(
								"class_id" => CL_SCM_RESULT,
								"CL_SCM_RESULT.RELTYPE_CONTESTANT" => $oid,
								"CL_SCM_RESULT.RELTYPE_COMPETITION" => $competition,
							));
							if($list->count())
							{
								// ehk siin on siis see koht kus on v&auml;lja raalitud et result on olemas ja tulemus updeiditakse
								$obj = $list->begin();;
								$rev = $res_type->format_data(array(
									"source" => "result",
									"oid" => $obj->id(),
									"data" => $result_data,
									"reverse" => true,
								));
								$id = current($list->ids());
								$obj = obj($id);
								$obj->set_prop("result", $rev);
								$obj->set_prop("team", $team);
								$obj->save();
							}
							else
							{
								// tuleb teha uus(teoorias ei tohiks asi siia &uuml;ldse j&otilde;uda, sest result objekt peaks juba olemas olema!!)
								$rev = $res_type->format_data(array(
									"source" => "competition",
									"oid" => $competition,
									"data" => $result_data,
									"reverse" => true,
								));
								$id = $res->add_result(array(
									"competition" => $competition,
									"contestant" => $oid,
									"team" => $team,
									"result" => $rev,
								));
							}
						}

					}
				}
				foreach($arr["request"]["res_new_team"] as $competition => $data)
				{
					foreach($data as $team => $result)
					{
						$inst = get_instance(CL_SCM_TEAM);
						foreach($inst->get_team_members(array("team" => $team)) as $oid => $obj)
						{
							$rev = $res_type->format_data(array(
								"source" => "competition",
								"oid" => $competition,
								"data" => $result,
								"reverse" => true,
							));
							$id = $res->add_result(array(
								"competition" => $competition,
								"contestant" => $oid,
								"team" => $team,
								"result" => $rev,
							));
						}
					}
				}
			break;
			case "name":
				$arr["obj_inst"]->set_name($prop["value"]);
			break;
		}
		return $retval;
	}	
	
	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function callback_pre_save($arr)
	{
		// getting event_type 
		$et = ($evt = $arr["obj_inst"]->prop("scm_event"))?call_user_method("prop", obj($evt), "type"):false;

		// setting team_result_calc if it isn't set
		if(($et == "multi" || $et == "multi_coll") && !$arr["obj_inst"]->prop("team_result_calc"))
		{
			$prop_val = call_user_method("prop", obj($evt), "team_result_calc");
			$arr["obj_inst"]->set_prop("team_result_calc", $prop_val);
			$arr["obj_inst"]->save();
		}

		// setting result_type if it isn't set
		if(($et == "multi" || $et == "multi_coll") && !$arr["obj_inst"]->prop("result_type"))
		{
			$prop_val = call_user_method("prop", obj($evt), "result_type");
			$arr["obj_inst"]->set_prop("result_type", $prop_val);
			$arr["obj_inst"]->save();
		}

		// checking and setting dha start and end times of dha compeititon
		if(($from = $arr["request"]["datetime_form"]) && ($date_to = $arr["request"]["date_to"]))
		{
			$start = mktime($from["hour"], $from["minute"], 0, $from["month"], $from["day"], $from["year"]);
			if($start > ($end = mktime(23, 59, 59, $date_to["month"], $date_to["day"], $date_to["year"])))
			{
				$end = mktime(23, 59, 59, $from["month"], $from["day"], $from["year"]);
			}
			$arr["obj_inst"]->set_prop("date_from", $start);
			$arr["obj_inst"]->set_prop("date_to", $end);
		}

		// new contestants added through search popup
		if($arr["request"]["search_result"])
		{
			$this->_add_contestant_from_search(&$arr);
		}

		// here we save new contestant id's
		$this->_update_contestant_ids(&$arr, $et);

		// registering new teams to competition 
		if($arr["request"]["search_result_teams"])
		{
			$this->_register_teams(&$arr);
		}

		// save groups data
		if(count($arr["request"]["groups_comment"]))
		{
			foreach($arr["request"]["groups_comment"] as $group => $comment)
			{
				$c = new connection();
				$conns = $c->find(array(
					"from" => $arr["obj_inst"]->id(),
					"to" => $group,
					"type" => "5",
				));
				foreach($conns as $cid => $data)
				{
					$c = new connection($cid);
					$c->change(array(
						"data" => $comment,
					));
				}
			}
		}
	}



	function _add_contestant_from_search($arr)
	{
		$res = split(",", $arr["request"]["search_result"]);
		// loops over every selected contestant and connects them to competition
		foreach($res as $contestant)
		{
			$data = array(
				"contestant" => $contestant,
				"competition" => $arr["obj_inst"]->id(),
			);
			$arr["obj_inst"]->connect(array(
				"to" => $contestant,
				"type" => "RELTYPE_CONTESTANT",
				"extra" => aw_serialize($data, SERIALIZE_NATIVE),
			));
		}
	}

	function _update_contestant_ids($arr, $et = "single")
	{
		// check for unique id's if needed
		$ids = $arr["request"]["contestant_ids"];
		$unique = $arr["obj_inst"]->prop("unique_nr");
		if($unique)
		{
			foreach($ids as $relid => $real_ids)
			{
				if($et == "single")
				{
					$to_check[] = reset($real_ids);
				}
				else
				{
					$to_check = array_merge($to_check, $real_ids);
				}
			}
			if(count(array_unique($to_check)) < count($to_check))
			{
				return false;
			}
		}
		foreach($ids as $relid => $sub_ids)
		{
			$c = new connection($relid);
			$data = aw_unserialize($c->prop("data"));
			if($et == "single")
			{
				$data["id"] = reset($sub_ids);
			}
			else
			{
				foreach($sub_ids as $c_oid => $c_id)
				{
					$data["members"][$c_oid] = $c_id;
				}
			}
			$c->change(array(
				"data" => aw_serialize($data, SERIALIZE_NATIVE),
			));
		}

	}

	function _register_teams($arr)
	{
		$spl = split(",", $arr["request"]["search_result_teams"]);
		foreach($spl as $team)
		{
			unset($data);
			$t_inst = get_instance(CL_SCM_TEAM);
			$t_inst->register_team(array(
				"team" => $team,
				"competition" => $arr["id"],
			));
		}
	}

	/**
		@comment
			okey actually this function isn't needed any more here, and it's not finished completely.. but i'll leave it here.. might be useful someday
			I used it to generate a fake relpicker. i made a type=select property and appended this crap to it
			like that:
			$prop["post_append_text"] => $this->_gen_fake_relpicker(array(
				"class" => class_file_name(scm_competition),
				"class_id" = clid (CL_SCM_COMPETITION),
				"id" => current_obj_id,
				"form_name" =>  name of the form element that catches the popup search result,
			));

			i almost needed it when i wantet to use releditor on a relpicker type prop(doesnt work by default)
	**/
	function _gen_fake_relpicker($arr)
	{
		$space = "&nbsp;";
		$new = html::href(array(
			"caption" => html::img(array(
					"url" => "/automatweb/images/icons/new.gif",
					"border" => 0,
				)),
			"url" => "#",
		));
		$edit = html::href(array(
			"caption" => html::img(array(
					"url" => "/automatweb/images/icons/edit.gif",
					"border" => 0,
				)),
			"url" => $this->mk_my_orb("change", array(
				"class" => $arr["class"],
				"id" => $arr["id"],
				"return_url" => get_ru(),
			)),
		));
		$popup_search = get_instance("vcl/popup_search");
		$search = $popup_search->get_popup_search_link(array(
			"pn" => $arr["form_name"],
			"clid" => $arr["class_id"],
		));
		$hidd = html::hidden(array(
			"name" => $arr["form_name"],
		));
		return $hidd.$search.$space.$edit.$space.$new;
	}

	function get_rel_data($rel)
	{
		return array(
			"data" => aw_unserialize(call_user_method("prop", ($conn = new connection($rel)), "data")),
			"conn" => $conn,
		);
	}

	function save_rel_data($arr)
	{
		$arr["conn"]->change(array(
			"data" => aw_serialize($arr["data"], SERIALIZE_NATIVE),
		));
	}

	function _get_table_js($arr)
	{
		$nr = $arr["nr"];
		$url = $arr["url"];
		$ret = " (<a id='tnr$nr' href='javascript:void(0)' onClick='
			if ((trel = document.getElementById(\"trows$nr\")))
			{
				if (trel.style.display == \"none\")
				{
					if (navigator.userAgent.toLowerCase().indexOf(\"msie\")>=0)
					{
						trel.style.display= \"block\";
					}
					else
					{
						trel.style.display= \"table-row\";
					}
				}
				else
				{
					trel.style.display=\"none\";
				}
				return false;
			}
			el=document.getElementById(\"tnr$nr\");
			td = el.parentNode;
			tr = td.parentNode;

			tbl = tr;
			while(tbl.tagName.toLowerCase() != \"table\")
			{
				tbl = tbl.parentNode;
			}
			p_row = tbl.insertRow(tr.rowIndex+1);
			p_row.className=\"awmenuedittablerow\";
			p_row.id=\"trows$nr\";
			n_td = p_row.insertCell(-1);
			n_td.className=\"awmenuedittabletext\";
			n_td.colspan=\"4\";
			n_td.innerHTML=aw_get_url_contents(\"$url\");
			n_td.colSpan=9;
		'>".t("Liikmed")."</a>) ";
		return $ret;
	}

	/**
		@comment
			converts raw results from #scm_result.get_results to nice-looking data to be put directly to $t->define_data();
	**/
	function convert_results($arr, $competition)
	{
		$res = $arr;
		$et = ($event_id = call_user_method("prop", obj($competition), "scm_event"))?(call_user_method("prop", obj($event_id), "type")):false;
		$team_inst = get_instance(CL_SCM_TEAM);
		$res_type_inst = get_instance(CL_SCM_RESULT_TYPE);
		switch($et)
		{
			case "single":
				$cnt_inst = get_instance(CL_SCM_CONTESTANT);
				foreach($res as $result)
				{
					$to_calc[(int)$result["contestant"]] = $result["raw_result"];
				}
				$pos_and_point = $this->_get_places_and_points(array(
					"data" => $to_calc,
					"competition" => $competition,
				));
				foreach($res as $result)
				{
					$pers = obj($cnt_inst->get_contestant_person(array(
						"contestant" => $result["contestant"],
					)));
					$cont_url = $this->mk_my_orb("change", array(
						"class" => "scm_contestant",
						"id" => $result["contestant"],
						"return_url" => get_ru(),
					));
					$ret[$pos_and_point[$result["contestant"]]["place"]] = array(
						"contestant" => html::href(array(
							"caption" => $pers->prop("lastname").", ".$pers->prop("firstname"),
							"url" => $cont_url,
						)),
						"company" => call_user_method("name", obj($cnt_inst->get_contestant_company(array(
						"contestant" => $result["contestant"],
						)))),
						"points" => $pos_and_point[$result["contestant"]]["points"],
						"place" => $pos_and_point[$result["contestant"]]["place"],
						"result" => $result["raw_result"],
						"result_arr" => $result["result"],
						"id" => strlen($result["id"])?$result["id"]:t("-"),
						"groups" => $result["groups"],

					);
				}
			break;

			case "multi":
				if(count($res))
				{
					foreach($res as $result)
					{
						$team_data[$result["team_oid"]][] = $result["raw_result"];
						$final_res[$result["team_oid"]] = array(
							"team" => $result["team_oid"],
							"groups" => $result["groups"],
						);
					}
					$event = get_instance(CL_SCM_EVENT);
					$calc_fun = $this->get_team_result_calc_fun(array(
						"competition" => $competition,
					));
					foreach($team_data as $team_oid => $results)
					{
						$team_tot_raw[$team_oid] = call_user_method($calc_fun, $event, $results);
						$team_tot_arr[$team_oid] = $res_type_inst->format_data(array(
							"data" => $team_tot_raw[$team_oid],
							"source" => "competition",
							"oid" => $competition,
						));
					}
					$pos_and_point = $this->_get_places_and_points(array(
						"data" => $team_tot_raw,
						"competition" => $competition,
					));
					foreach($final_res as $data)
					{
						$team = &$data["team"];
						$groups = &$data["groups"];
						$memb = $team_inst->get_team_members(array(
							"team" => $team,
							"competition" => $arr["competition"],
						));
						$company = $this->_get_multi_company_name($memb);	

						$obj = obj($team);
						$ret[$pos_and_point[$team]["place"]] = array(
							"team_name" => html::href(array(
								"caption" => $obj->name(),
								"url" => $this->mk_my_orb("change", array(
									"class" => "scm_team",
									"id" => $team,
									"return_url" => get_ru(),
								)),
							)),
							"points" => $pos_and_point[$team]["points"],
							"place" => $pos_and_point[$team]["place"],
							"result" => $team_tot_raw[$team],
							"result_arr" => $team_tot_arr[$team],
							"company" => $company,
							"groups" => $groups,
						);
					}
				}
			break;

			case "multi_coll":
				foreach($res as $result)
				{
					$to_calc[$result["team"]] = $result["raw_result"];
				}
				$pos_and_point = $this->_get_places_and_points(array(
					"data" => $to_calc,
					"competition" => $competition,
				));
				foreach($res as $result)
				{

					$memb = $team_inst->get_team_members(array(
						"team" => $result["team_oid"],
						"competition" => $competition,
					));
					$company = $this->_get_multi_company_name($memb);

					$team = obj($result["team"]);
					$ret[$pos_and_point[$result["team"]]["place"]] = array(
						"team_name" => html::href(array(
							"caption" => $team->name(),
							"url" => $this->mk_my_orb("change", array(
								"class" => "scm_team",
								"id" => $result["team"],
								"return_url" => get_ru(),
							)),
						)),
						"points" => $pos_and_point[$result["team"]]["points"],
						"place" => $pos_and_point[$result["team"]]["place"],
						"result" => $result["raw_result"],
						"result_arr" => $result["result"],
						"company" => $company,
						"groups" => $result["groups"],
					);
				}

			break;
		}

		return $ret;
	}

	function _get_places_and_points($arr = array())
	{
		$score_calc = get_instance(CL_SCM_SCORE_CALC);
		$comp = obj($arr["competition"]);
		$res = $score_calc->calc_results(array(
			"data" => $arr["data"],
			"score_calc" => $comp->prop("scm_score_calc"),
			"competition" => $arr["competition"],
		));
		return $res;
	}
	function _get_image($arr)
	{
		$loc_id = $arr["obj_inst"]->prop("location");
		
		if(!$loc_id)
		{
			return t("V&otilde;istluse toimumise asukoht m&auml;&auml;ramata");
		}
		$loc = obj($loc_id);
		$img_inst = get_instance(CL_IMAGE);
		$img_id = $loc->prop($arr["prop"]["name"]);
		if(!$img_id)
		{
			return t("Pilt on m&auml;&auml;ramata");
		}
		$img_inf = $img_inst->get_image_by_id($img_id);
		return html::img(array(
			"url" => $img_inf["url"],
		));
	}

	function _gen_cont_tbl($t, $filters = array(), $team = true)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("V&otilde;istleja"),
			"sortable" => true,
		));
		$t->define_field(array(
			"name" => "company",
			"caption" => t("Firma"),
			"sortable" => true,
		));
		$t->define_field(array(
			"name" => "sex",
			"caption" => t("Sugu"),
			"sortable" => true,
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "birthday",
			"caption" => t("S&uuml;nniaeg"),
			"sortable" => true,
			"callback" => array(&$this, "__dob_format"),
			"align" => "center",
		));
		if($team)
		{
			$t->define_field(array(
				"name" => "team",
				"caption" => t("Meeskond"),
				"sortable" => true,
				"align" => "center",
				"filter" => $filters["teams"],
				"filter_compare" => array(&$this, "__team_filter"),
			));
		}
		$t->define_field(array(
			"name" => "groups",
			"caption" => t("V&otilde;istlusklassid"),
			"filter" => $filters["groups"],
			"filter_compare" => array(&$this, "__group_filter"),
			"callback" => array(&$this, "__group_format"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "id",
			"caption" => t("S&auml;rgi number"),
			"callback" => array(&$this, "__id_textbox"),
			"sortable" => true,
			"numeric" => true,
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "contact",
			"caption" => t("Kontaktandmed"),
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "sel_contestants",
		));

	}
	
	function __team_filter($key, $str, $foo)
	{
		return ($foo["team"] == $str)?true:false;
	}

	function __group_format($key)
	{
		return (is_array($key) && count($key))?join(", ", $key):t("V&otilde;istlusklass m&auml;&auml;ramata");
	}

	function __group_filter($key, $str, $foo)
	{
		return in_array($str, $foo["groups"]);
	}

	function __dob_format($str)
	{
		return date("d / m / Y", $str)." (".$this->get_age($str)."a)";
	}

	function __id_textbox($str)
	{
		$str = split("[.]", $str);
		$html = html::textbox(array(
			"name" => "contestant_ids[".$str[2]."][".$str[1]."]",
			"value" => $str[0],
			"size" => 4,
		));
		return $html;
	}

	function _gen_res_tbl($t, $type = "single", $groups = array())
	{
		if($type == "single")
		{
			$t->define_field(array(
				"name" => "contestant",
				"caption" => t("V&otilde;istleja"),
				"sortable" => true,
			));
			$t->define_field(array(
				"name" => "id",
				"caption" => t("V&otilde;istleja nr"),
				"sortable" => true,
				"align" => "center",
			));
		}
		if($type == "multi" || $type == "multi_coll")
		{
			$t->define_field(array(
				"name" => "team_name",
				"caption" => t("Meeskond"),
				"sortable" => true,
			));
		}

		$t->define_field(array(
			"name" => "company",
			"caption" => t("Firma"),
			"sortable" => true,
		));
		$t->define_field(array(
			"name" => "result",
			"caption" => t("Tulemus"),
		));
		$t->define_field(array(
			"name" => "groups",
			"caption" => t("V&otilde;istlusklassid"),
			"align" => "center",
			"filter" => $groups, 
			"callback" => array(&$this, "__group_format"),
			"filter_compare" => array(&$this, "__group_filter"),
		));

		$t->define_field(array(
			"name" => "place",
			"caption" => t("Koht"),
			"sortable" => true,
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "points",
			"caption" => t("Punkte"),
			"align" => "center",
		));
		$t->set_numeric_field("place");
		$t->set_default_sortby("place");

	}

	function _gen_add_res_tbl($t, $event_type = "single")
	{
		if($event_type == "single" || $event_type == "multi")
		{
			$t->define_field(array(
				"name" => "contestant",
				"caption" => t("V&otilde;istleja"),
				"sortable" => 1,
			));
			$t->define_field(array(
				"name" => "id",
				"caption" => t("V&otilde;istleja nr"),
				"sortable" => true,
				"align" => "center",
			));
		}
		if($event_type == "multi" || $event_type == "multi_coll")
		{
			$t->define_field(array(
				"name" => "team_name",
				"caption" => t("V&otilde;istkond"),
				"sortable" => 1,
			));
		}
		$t->define_field(array(
			"name" => "company",
			"caption" => t("Firma"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "result",
			"caption" => t("Tulemus"),
		));

	}

	function _gen_teams_tbl($t, $groups)
	{
		$t->define_field(array(
			"name" => "team",
			"caption" => t("Meeskond"),
			"sortable" => true,
		));
		$t->define_field(array(
			"name" => "company",
			"caption" => t("Firma"),
			"sortable" => true,
		));
		$t->define_field(array(
			"name" => "groups",
			"caption" => t("V&otilde;istlusklassid"),
			"filter" => $groups,
			"filter_compare" => array(&$this, "__group_filter"),
			"callback" => array(&$this, "__group_format"),
		));
		$t->define_chooser(array(
			"name" => "selector",
			"field" => "selected",
		));
	}
	
	function _comp_list_callback(&$o, $list)
	{
		$list->remove($o->id());
	}

	
	/**
		@attrib params=name
		@param competition required type=oid
		@param team optional type=oid
		@param contestant optional type=oid
		@comment
			fetches extra data from connection.
			at least of the optional parameters(team,contestant) must be set.
		@returns
			false if parameters are wrong or if any connections isn't found.
			else array in exact same format as #get_rel_data ,
			so you can save the same array with #save_rel_data
	**/
	function get_extra_data($arr)
	{
		if(!$arr["competition"] || (!$arr["team"] && !$arr["contestant"]))
		{
			return false;
		}
		$to = strlen($arr["team"])?$arr["team"]:$arr["contestant"];
		$c = new connection();
		$conns = $c->find(array(
			"from" => $arr["competition"],
			"to" => $to,
			"type" => 6,
		));
		if($conns && count($conns))
		{
			return $this->get_rel_data(key($conns));
		}
		else
		{
			return false;
		}
	}
	
	function get_team_result_calc_fun($arr)
	{
		return call_user_method("prop", obj($arr["competition"]), "team_result_calc");
	}

	function get_result_type($arr)
	{
		return call_user_method("prop", obj($arr["competition"]), "result_type");
	}


	/**
		@attrib params=name api=1
		@param registered optional type=bool
			if set to true, only these competitions will be returned where $contestant has signed in
		@param unregistered optional type=bool
			if set to true, only these competitions will be returned where $contestant hasn't signed in.
		@param contestant optional type=oid
			this is for using with $registered and $unregistered, sets the contestant who's competitions will be returned.
		@param organizer optional type=oid
			returns only the competitions created by given organizer
		@param state type=string
			3 options:
			+ archive
			+ current
			+ all(default)
		@comment
			generates list of competitions.
		@returns
			array of competitions:
			array(
				scm_competition oid,
				scm_competition object_inst,
			)
			
	**/
	function get_competitions($arr = array())
	{
		$state = strlen($arr["state"])?$arr["state"]:0;
		$ol_filt["class_id"] = CL_SCM_COMPETITION;
		if($arr["state"] == "archive")
		{
			$ol_filt["archive"] = 1;
		}
		elseif($arr["state"] == "current")
		{
			$ol_filt["archive"] = 0;
		}
		$ol_filt["CL_SCM_COMPETITION.RELTYPE_CONTESTANT"] = ($arr["contestant"])?$arr["contestant"]:NULL;
		$list = new object_list($ol_filt);
		/*
		if(strlen($arr["contestant"]) && ($arr["unregistered"] || $arr["registered"]))
		{
			foreach($list->arr() as $oid => $obj)
			{
				$conns = $obj->connections_from(array(
					"type" => "RELTYPE_CONTESTANT",
				));
				unset($persons);
				foreach($conns as $con)
				{
					if($con->conn["to"] == $arr["contestant"] && $arr["unregistered"])
					{
						$list->remove($oid);
						break;
					}
					$persons[] = $con->conn["to"];
				}
				if($arr["registered"] && !in_array($arr["contestant"], $persons))
				{
					$list->remove($oid);
				}
			}
		}
		*/
		if(strlen($arr["organizer"]))
		{
			$obj = obj($arr["organizer"]);
			$conns = $obj->connections_from(array(
				"type" => "RELTYPE_COMPETITION",
				"class" => CL_SCM_COMPETITION,
			));
			foreach($conns as $data)
			{
				$o = $data->to();
				$orgs[] = $o->id();;
			}
			foreach($list->arr() as $oid => $obj)
			{
				if(!in_array($oid, $orgs))
				{
					$list->remove($oid);
				}
			}
		}
		return $list->arr();
	}

	/**
		@attrib params=pos
		@param competition required type=oid
		@comment
			fetches groups and their comments for given competition
	**/
	function get_groups_comments($arr)
	{
		$obj = obj($arr);
		$gr = $obj->prop("scm_group");
		$c = new connection();
		$conns = $c->find(array(
			"from" => $arr,
			"to.class_id"  => CL_SCM_GROUP,
		));
		foreach($conns as $cid => $data)
		{
			if(in_array($data["to"], $gr))
			{
				$ret[$data["to"]] = $data["data"];
			}
		}
		return $ret;
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
		@param competition required type=int
			the competition object id
		@comment
			fetches the organizer objects name
		@returns
			scm_organizer oid for given competition
	**/
	function get_organizer($arr = array())
	{
		$conn = new connection();
		$conns = $conn->find(array(
			"from.class_id" => CL_SCM_ORGANIZER,
			"to" => $arr["competition"],
		));
		if(!count($conns))
		{
			return false; // no organizer connected to competition
		}
		$org = current($conns);
		return $org["from"];
	}

	/**
		@attrib params=name api=1
		@param competition required type=int
			the competitions object id
		@param ret_inst optional type=bool
			returns connection instance
		@comment
			fetches contestants for given competition
		@returns
			returns array of scm_contestant object id's who are registred to this competition
			array(
				object_id => object_name
			)
	**/
	function get_contestants($arr = array())
	{
		if(!$arr["competition"])
		{
			return false;
		}
		$event = ($evt = call_user_method("prop", obj($arr["competition"]), "scm_event"))? obj($evt):false;
		$t = $event?$event->prop("type"):false;
		if($t && ($t == "single"))
		{
			$conn = new connection();
			$conns = $conn->find(array(
				"from" => $arr["competition"],
				"to.class_id" => CL_SCM_CONTESTANT,
				"type" => 6,
			));
			foreach($conns as $id => $data)
			{
				$res[$data["to"]] = array(
					"obj" => $arr["ret_inst"]?obj($data["to"]):NULL,
					"data" => aw_unserialize($data["data"]),
					"connection" => new connection($id),
				);
			}
		}
		elseif($t && ($t == "multi" || $t == "multi_coll"))
		{
			// so, i have to find teams that are registred, and team members registred to this competition
			$conn = new connection();
			$conns = $conn->find(array(
				"from" => $arr["competition"],
				"to.class_id" => CL_SCM_TEAM,
				"type" => 6,
			));
			foreach($conns as $id => $data)
			{
				$extra_data = aw_unserialize($data["data"]);
				foreach($extra_data["members"] as $member => $members_id)
				{
					$res[$member] = array(
						"obj" => $arr["ret_inst"]?obj($member):NULL,
						"data" => aw_unserialize($data["data"]),
						"connection" => new connection($id),
						"team" => $data["to"],
						"id" => $members_id,
					);
					$res[$member]["data"]["team"] = $data["to"];
				}
				// here should be data concerning registered members, their id's.. teams group .. etc
				// array(
				// groups = array(
				//	CL_SCM_GROUP oid
				// )
				// members = array(
				//	CL_SCM_CONTESTANT oid => shirt_id,
				// )
				// anything else?
			}
		}
		else
		{
			return false;
		}
		return $res;
	}

	/**
		@attrib params=name api=1
		@param competition required type=int
			competitions object id
		@comment
			gets event for given competition
		@returns
			event object id
	**/
	function get_event($arr = array())
	{
		$o = obj($arr["competition"]);
		return ($s = $o->prop("scm_event"))?$s:false;
	}
	
	function _gen_groups_change_toolbar_addon($arr)
	{
		$tb = &$arr["tb"];
		$competition = $arr["competition"];
		$options = $this->_gen_class_list(array("competition" => $competition));
		$options[0] = t("-Vali v&otilde;istlusklass-");
		asort($options);

		$select = html::select(array(
			"name" => "tb_select_option",
			"options" => $options,
		));
		$select2 = html::select(array(
			"name" => "tb_select_action",
			"options" => array(
				1 => ($t = $arr["caption"]["assign"])?$t:t("M&auml;&auml;ra v&otilde;istlejad klassi"),
				2 => ($t = $arr["caption"]["unassign"])?$t:t("Eemalda v&otilde;istlejad klassist"),
			),
		));
		$tb->add_cdata($select2, true);
		$tb->add_cdata($select, true);
		$tb->add_button(array(
			"name" => "save2",
			"img" => "prog_20.gif",
			"side" => true,
			"action" => "change_grp",
		));
	}

	function _gen_format_caption($arr)
	{
		$result = $arr["result"];
		foreach($arr["format"] as $name => $caption)
		{
			$ret[] = $result[$name]?$result[$name]." ".$caption:NULL;
		}

		return join(" ", $ret);
	}

	/**
	**/
	function _gen_format_nice($arr)
	{
		$result = $arr["result"];
		$type = (strlen($arr["team"])?"team":"contestant");
		// formaadi v&auml;ljad k&auml;ikase l&auml;bi ja tekitatakse input'id
		if($type == "team")
		{
			$for_team = "[".$arr["team"]."]";
		}
		else
		{
			$for_cont = "[".$arr["contestant"]."]";
		}
		foreach($arr["format"] as $name => $caption)
		{
			if(strlen($result["result_oid"]))
			{
				$textbox["name"] = "res_".$type."[".(($type == "team")?$arr["competition"]:$result["result_oid"])."]".$for_team."[".$name."]";
			}
			else
			{
				$textbox["name"] = "res_new_".$type."[".$arr["competition"]."]".$for_team.$for_cont."[".$name."]";
			}
			$textbox["size"] = "4";
			$textbox["value"] = $result["result"][$name];
			$res .= html::textbox($textbox);
			$res .= $caption."&nbsp;&nbsp;";
		}
		return $res;
	}
	

	/**
	**/
	function get_location($arr = array())
	{
		$obj = obj($arr["competition"]);
		return ($s = ($obj->prop("location")))?$s:false;
	}

	function get_date($arr = array())
	{
		$obj = obj($arr["competition"]);
		return $obj->prop("date");
	}

	/**
	**/
	function get_groups($arr)
	{
		$obj = obj($arr["competition"]);
		return ($s = $obj->prop("scm_group"))?$s:false;
	}

	function get_age($from, $to = "")
	{
		$birthday_rec = getdate($from);
		$now_rec = getdate($to?$to:time());
		$age = $now_rec["year"] - $birthday_rec["year"];
		$age = ($now_rec["mon"] < $birthday_rec["mon"])?$age--:(($now_rec["mday"] <= $birthday_rec["mday"])?$age--:$age);
		return $age;
	}

	function _gen_class_list($arr)
	{
		$o = obj($arr["competition"]);
		foreach($o->prop("scm_group") as $group)
		{
			$o = obj($group);
			$ret[$group] = $o->prop("abbreviation");
		}
		return $ret;
	}

	function get_teams($arr)
	{
		$c = new connection();
		$conns = $c->find(array(
			"from" => $arr["competition"],
			"to.class_id" => CL_SCM_TEAM,
			"type" => 6,
		));
		foreach($conns as $cid => $data)
		{
			$ret[$data["to"]] = obj($data["to"]);
		}
		return $ret;
	}

	/**
		@param members required type=array
			array(
				CL_SCM_CONTESTANT oid => CL_SCM_CONTESTANT object
			)
		@comment
			finds out in which company contestants work
		@returns
			if all work in one company, returns its name.
			if members are in different company's, returns 'segav&otilde;istkond'.
			if argument has 0 members, according text is returned
	**/
	function _get_multi_company_name($memb)
	{
		$cnt_inst = get_instance(CL_SCM_CONTESTANT);
		if(!count($memb))
		{
			return t("&Uuml;htegi liiget pole registreerunud");
		}
		foreach($memb as $oid => $obj)
		{
			$team_companys[$cnt_inst->get_contestant_company(array("contestant" => $oid))] = 1;
		}
		$company = (count($team_companys) > 1)?t("Segav&otilde;istkond"):call_user_method("name", obj(key($team_companys)));
		return $company;
	}

	/**
		@attrib name=change_grp params=name all_args=1 default=0
	**/
	function change_grp($arr)
	{
		$arr["sel"] = strlen($arr["selector"])?$arr["selector"]:$arr["sel"];
		$group = $arr["tb_select_option"];
		if(!$group || !count($arr["sel"]))
		{
			return $arr["post_ru"];
		}
		$action = $arr["tb_select_action"];
		$sel = $arr["sel"];
		$et = ($evt = call_user_method("prop", obj($arr["id"]), "scm_event"))?(call_user_method("prop", obj($evt), "type")):false;
		// for individual competitions
		if($et && $et == "single")
		{
			foreach($sel as $oid_and_id)
			{
				list($oid, $cid) = split("[.]", $oid_and_id);

				$data = $this->get_rel_data($cid);
				if($action  == 1)
				{
					$data["data"]["groups"][] = $group;
					$data["data"]["groups"] = array_unique($data["data"]["groups"]);
				}
				else
				{
					$flp = array_flip($data["data"]["groups"]);
					unset($flp[$group]);
					$data["data"]["groups"] = array_flip($flp);
				}
				$this->save_rel_data($data);
			}
		}
		elseif($et && ($et == "multi" || $et == "multi_coll"))
		{
			// have to figure out teams.. 
			foreach($sel as $oid_and_id)
			{
				list($oid, $cid) = split("[.]", $oid_and_id);
				$obj = obj($oid);
				// this figures out from where the change came from(from teams tab or from contestants tab)
				if($obj->class_id() == CL_SCM_TEAM)
				{
					$teams[] = $oid;
				}
				else
				{
					$data = $this->get_rel_data($cid);
					$teams[] = $data["data"]["team"];
				}
			}
			$teams = array_unique($teams);
			$conn = new connection();
			foreach($teams as $team)
			{
				$conns = $conn->find(array(
					"from" => $arr["id"],
					"to" => $team,
					"type" => 6,
				));
				$data = $this->get_rel_data(key($conns));
				if($action == 1)
				{
					$data["data"]["groups"][] = $group;
					$data["data"]["groups"] = array_unique($data["data"]["groups"]);
				}
				else
				{
					$flp = array_flip($data["data"]["groups"]);
					unset($flp[$group]);
					$data["data"]["groups"] = array_flip($flp);
				}
				$this->save_rel_data($data);
			}
		}
		return $arr["post_ru"];
	}

	/**
		@attrib name=unregister_cnt params=name all_args=1 default=0
	**/
	function unregister_cnt($arr)
	{
		// get the event type
		$et = ($evt = call_user_method("prop", obj($arr["id"]), "scm_event"))?(call_user_method("prop", obj($evt), "type")):false;
		if($et == "single")
		{
			$obj = obj($arr["id"]);
			foreach($arr["sel"] as $id)
			{
				list($oid, $cid) = split("[.]", $id);
				$obj->disconnect(array(
					"from" => $oid,
				));
			}
		}
		elseif($et == "multi" || $et == "multi_coll")
		{
			foreach($arr["sel"] as $id)
			{
				list($oid, $cid) = split("[.]", $id);
				$data = $this->get_rel_data($cid);
				foreach($data["data"]["members"] as $member => $id)
				{
					if($member == $oid)
					{
						unset($data["data"]["members"][$member]);
					}
				}
				$this->save_rel_data($data);
			}
		}
		return $arr["post_ru"];
	}

	/**
		@attrib name=unregister_team all_args=1 params=name
	**/
	function unregister_team($arr)
	{
		$obj = obj($arr["id"]);
		foreach($arr["selector"] as $id)
		{
			list($gid, $cid) = split("[.]", $id);
			$obj->disconnect(array(
				"from" => $gid,
			));
		}
		return $arr["post_ru"];
	}

	/**
		@attrib name=prep_new_team all_args=1 params=name
	**/
	function prep_new_team($arr)
	{
		$obj = obj();
		$obj->set_class_id(CL_SCM_TEAM);
		$org = $this->get_organizer(array(
			"competition" => $arr["id"],
		));
		$obj->set_parent($org);
		$id = $obj->save_new();
		$t_inst = get_instance(CL_SCM_TEAM);
		$t_inst->register_team(array(
			"competition" => $arr["id"],
			"team" => $id,
		));

		$url = $this->mk_my_orb("change", array(
			"class" => "scm_team",
			"id" => $id,
			"return_url" => $arr["return_url"],
		));
		return $url;
	}

	/**
		@attrib name=add_contestant_to_team_and_register all_args=1 params=name
	**/
	function add_contestant_to_team_and_register($arr)
	{
		// making new contestant
		$org = $this->get_organizer(array(
			"competition" => $arr["competition"],
		));
		if($arr["contestant"])
		{
			$obj = obj($arr["contestant"]);
		}
		$obj->set_parent($org);
		$obj->set_class_id(CL_SCM_CONTESTANT);
		$cnt_id = $obj->save();
		
		// connecting contestant to team
		$t_obj = obj($arr["team"]);
		$t_obj->connect(array(
			"to" => $cnt_id,
			"type" => 2,
		));

		// checking if team is already registered, if not, registers team.
		$c = new connection();
		$conns = $c->find(array(
			"from" => $arr["competition"],
			"to" => $arr["team"],
			"type" => 6,
		));
		if(count($conns))
		{
			$data = $this->get_rel_data(key($conns));
			$data["data"]["members"][$cnt_id] = $arr["contestant_id"];
			$data["data"]["groups"] = array_merge($data["data"]["groups"], $arr["groups"]);
			$this->save_rel_data($data);
		}
		else
		{
			// register this team to competition
			$team_inst = get_instance(CL_SCM_TEAM);
			$team_inst->register_team(array(
				"team" => $arr["team"],
				"groups" => $arr["groups"],
				"competition" => $arr["competition"],
				"members" => array(
					$cnt_id => $arr["contestant_id"],
				),
			));
		}

		$url = $this->mk_my_orb("change", array(
			"class" => "scm_contestant",
			"id" => $cnt_id,
			"return_url" => $arr["return_url"],
		));
		return $url;
	}



	function gen_protocol($arg)
	{
		$this->read_template("html_protocol.tpl");
		$event_oid = $this->get_event(array(
			"competition" => $arg["competition"],
		));
		$res_inst = get_instance(CL_SCM_RESULT);
		$et = ($event_oid)?(call_user_method("prop", obj($event_oid), "type")):false;
		if($et == "multi" || $et == "single")
		{
			$results = $res_inst->get_results(array(
				"competition" => $arg["competition"],
				"type" => "contestant",
			));
		}
		else
		{
			$results = $res_inst->get_results(array(
				"competition" => $arg["competition"],
				"type" => "team",
			));
		}
		$parse_type = ($et == "single")?"contestant":"team";
		$results = $this->convert_results($results, $arg["competition"]);

		$res_type = $this->get_result_type(array(
			"competition" => $arg["competition"],
		));
		$type_inst = get_instance(CL_SCM_RESULT_TYPE);
		$format = $type_inst->get_format(array(
			"result_type" => $res_type
		));
		ksort($results);
		foreach($results as $place => $data)
		{
			if(!in_array($arg["group"], $data["groups"]))
			{
				continue;
			}
			unset($ind, $team);
			if($et == "single")
			{
				$this->vars(array(
					"first_name" => $data["contestant"],
				));
				$ind = $this->parse("INDIVIDUAL");
			}
			else
			{
				$this->vars(array(
					"team" => $data["team_name"],
				));
				$team = $this->parse("TEAM");
			}

			$data["result"] = $this->_gen_format_caption(array(
				"result" => $data["result_arr"],
				"format" => $format,
			));
			$this->vars(array(
				"INDIVIDUAL" => $ind,
				"TEAM" => $team,
				"type" => ($et == "single")?t("V&otilde;istleja"):t("V&otilde;istkond"),
				"contestant" => $cnt_caption,
				"result" => $data["result"],
				"place" => $place,
				"points" => $data["points"],
			));
			$res_rows .= $this->parse("ROW");
		}
		$c_obj = obj($arg["competition"]);
		$g_obj = obj($arg["group"]);
		$cmp_inst = get_instance(CL_SCM_COMPETITION);
		$org = obj($cmp_inst->get_organizer(array(
			"competition" => $arg["competition"],
		)));
		$tourn = $c_obj->prop("scm_tournament");
		foreach($tourn as $tid)
		{
			$o = $tid?obj($tid):false;
			if($o)
			{
				$tourns[] = $o->name();
			}
		}
		$location = $c_obj->prop_str("location");
		$start_time = $c_obj->prop("date_from");
		$end_time = $c_obj->prop("date_to");
		$event = ($evt_oid = $cmp_inst->get_event(array("competition" => $arg["competition"])))?obj($evt_oid):false;
		if(($name = $c_obj->name()))
		{
			$this->vars(array(
				"competition_caption" => t("&Uuml;ritus"),
				"competition_name" => $name,
			));
			$competition = $this->parse("COMPETITION"); 
		}
		if(count($tourns))
		{
			$this->vars(array(
				"tournament_caption" => t("V&otilde;istlussar(i/jad)"),
				"tournament_name" => join(", ", $tourns),		
			));
			$tournament = $this->parse("TOURNAMENT");
		}
		if($org)
		{
			$this->vars(array(
				"organizer_caption" => t("Organisaator"),
				"organizer_name" => $org->name(),
			));
			$organizer = $this->parse("ORGANIZER");
		}
		if($location)
		{
			$this->vars(array(
				"location_caption" => t("Asukoht"),
				"location_name" => $location,
			));
			$location  = $this->parse("LOCATION");
		}
		if($event)
		{
			$this->vars(array(
				"event_caption" => t("Spordiala"),
				"event_name" => $event->name(),
			));
			$event  = $this->parse("EVENT");
		}
		if(strlen($res_rows))
		{
			$this->vars(array(
				"ROW" => $res_rows,
			));
			$has_results = $this->parse("HAS_RESULTS");
		}
		else
		{
			$hasnt_results = $this->parse("HASNT_RESULTS");
		}
		$this->vars(array(
			"COMPETITION" => $competition,
			"TOURNAMENT" => $tournament,
			"ORGANIZER" => $organizer,
			"LOCATION" => $location,
			"time_caption" => t("Toimumisaeg"),
			"time_name" => date("H:i d/m/Y", $start_time)." - ".date("H:i d/m/Y", $end_time),
			"EVENT" => $event,
			"group_caption" => t("V&otilde;istlusklass"),
			"group_name" => $g_obj->name(),
			"HAS_RESULTS" => $has_results,
			"HASNT_RESULTS" => $hasnt_results,
		));
		return $this->parse();
	}

	/**
		@attrib name=gen_protocol_html params=name
		@param competition required type=int
		@param group required type=int
	**/
	function gen_protocol_html($arg)
	{
		die($this->gen_protocol($arg));
	}

	/**
		@attrib params=name name=gen_protocol_pdf
		@param competition required type=int
		@param group required type=int
	**/
	function gen_protocol_pdf($arr)
	{
		$h2p = get_instance("core/converters/html2pdf");
		$html = "<html><head><title>tiitel</title><body>bodyyy</body></html>";
		die($h2p->gen_pdf(array(
			"source" => $this->gen_protocol($arr),
			"filename" => "protokoll",
			//"source" => $this->mk_my_orb("gen_protocol", $arr),
		)));
	}
}
?>
