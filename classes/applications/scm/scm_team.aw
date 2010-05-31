<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/scm/scm_team.aw,v 1.11 2007/12/06 14:34:06 kristo Exp $
// scm_team.aw - Meeskond 
/*

@classinfo syslog_type=ST_SCM_TEAM relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=tarvo

@default table=objects
@default group=general
@default field=meta
@default method=serialize


@groupinfo members caption="Liikmed"
	@default group=members
	
	@property members_tb type=toolbar no_caption=1
	@caption Liikemete halduse t&ouml;&ouml;riistariba

	@property members_tbl type=table no_caption=1
	@caption Liikmete tabel

	@property search_res type=hidden name=search_result store=no no_caption=1
	@caption Otsingutulemuste hoidja

	@property members_unreg type=submit
	@caption Eemalda v&otilde;istkonnast

@groupinfo competitions caption="V&otilde;istlused" submit=no
	@groupinfo registration caption="Registreerumine" parent=competitions
		@default group=registration
		@property registration_tb type=toolbar no_caption=1

		@layout split_reg type=hbox width=15%:85% group=registration
			@property members_tree type=treeview no_caption=1 parent=split_reg
			@property members_list type=table no_caption=1 parent=split_reg

	@groupinfo registered caption="V&otilde;istlused" parent=competitions submit=no
		@property registered_tbl type=table no_caption=1 group=registered
	
	
@reltype SCM_TEAM_MEMBER value=2 clid=CL_SCM_CONTESTANT
@caption V&otilde;istkonna liige
*/

class scm_team extends class_base
{
	const AW_CLID = 1115;

	function scm_team()
	{
		$this->init(array(
			"tpldir" => "applications/scm//scm_team",
			"clid" => CL_SCM_TEAM
		));
		$this->team = false;
	}
	
	function set_team($team)
	{
		$this->team = $team?$team:$this->team;
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- get_property --//
			case "members_tb":
				$tb = &$prop["vcl_inst"];

				/*
				$url = $this->mk_my_orb("new",array(
					"parent" => $arr["obj_inst"]->id(),
					"alias_to" => $arr["obj_inst"]->id(),
					"class" => "scm_contestant",
					"reltype" => 2,
					"id" => $arr["obj_inst"]->id(),
					"return_url" => get_ru(),
				));
				*/

				$url = $this->mk_my_orb("gen_new_contestant_sheet",array(
					"parent" => $arr["obj_inst"]->id(),
					"team" => $arr["obj_inst"]->id(),
					"do_not_register" => true,
				),CL_SCM_CONTESTANT);
				
				$tb->add_button(array(
					"name" => "add_member",
					"tooltip" => t("Lisa liige"),
					"img" => "new.gif",
					"url" => "javascript:aw_popup_scroll('".$url."', 'title', 500,400);",
				));
				$popup_search = new popup_search();
				$search_butt = $popup_search->get_popup_search_link(array(
					"pn" => "search_result",
					"clid" => CL_SCM_CONTESTANT,
				));
				$prop["vcl_inst"]->add_cdata($search_butt);
			break;
			
			case "members_tbl":
				$t = &$prop["vcl_inst"];
				$this->_gen_members_tbl(&$t);
				foreach($this->get_team_members(array("team" => $arr["obj_inst"]->id())) as $oid => $obj)
				{
					$inst = get_instance(CL_SCM_CONTESTANT);
					$pers = obj($inst->get_contestant_person(array("contestant" => $oid)));
					$url = $this->mk_my_orb("change", array(
						"id" => $obj->id(),
						"return_url" => get_ru(),
					), CL_SCM_CONTESTANT);
					$t->define_data(array(
						"name" => html::href(array(
							"url" => $url,
							"caption" => $obj->name(),
						)),
						"sex" => (($s = $pers->prop("gender")) == 1)?t("Mees"):(($s == 2)?t("Naine"):t("M&auml;&auml;ramata")),
						"company" => ($s = $inst->get_contestant_company(array("contestant" => $oid)))?call_user_method("name", obj($s)):t("M&auml;&auml;ramata"),
						"rem_contestant" => $oid,
						"birthday" => (($s = $pers->prop("birthday")) != -1)?$s:t("M&auml;&auml;ramata"),
					));
				}
			break;

			case "registration_tb":
				$tb = &$prop["vcl_inst"];
				$tb->add_button(array(
					"name" => "bah",
					"img" => "new.gif",
				));
				if($competition = $arr["request"]["competition"])
				{
					$inst = get_instance(CL_SCM_COMPETITION);
					$inst->_gen_groups_change_toolbar_addon(array(
						"tb" => &$tb,
						"competition" => $competition,
						"caption" => array(
							"assign" => t("M&auml;&auml;ra v&otilde;istkonnale klass"),
							"unassign" => t("Eemalda v&otilde;istkond klassist"),
						),
					));
				}
			break;

			case "members_tree":
				$t = get_instance("vcl/treeview");
				classload("core/icons");
				$t->start_tree(array(
					"type" => TREE_DHTML,
					"has_root" => 1,
					"root_name" => t("Organisaatorid"),
					"root_url" => "#",
					"root_icon" => icons::get_icon_url(CL_CRM_PERSON),
					"tree_id" => "reg_tree",
					"persist_state" => 1,
					"get_branch_func" => $this->mk_my_orb("gen_tree_branch", array(
						"self_id" => $arr["obj_inst"]->id(),
						"group" => $arr["request"]["group"],
						"parent" => " ",
					)),
				));
				$this->_gen_reg_tree(&$t);
				$prop["type"] = "text";
				$prop["value"] = $t->finalize_tree();
			break;
			case "members_list":
				if(!($competition = $arr["request"]["competition"]))
				{
					$prop["value"] = t("Palun vali v&otilde;istlus");
					return PROP_OK;
				}
				$t = &$prop["vcl_inst"];
				$comp = obj($competition);
				$inst = get_instance(CL_SCM_COMPETITION);
				$grps = $inst->get_groups(array(
					"competition" => $competition
				));
				foreach($grps as $gr)
				{
					$o = obj($gr);
					$gr_options[$gr] = $o->name();
				}
				$this->_gen_members_reg_tbl(&$t, $gr_options);
				$header = sprintf(t("V&otilde;istluse '%s' haldamine"), $comp->name());
				$t->define_header($header);

				$cont_inst = get_instance(CL_SCM_CONTESTANT);
				$contestants = array_keys($inst->get_contestants(array(
					"competition" => $competition,
				)));
				$members = $this->get_team_members(array(
					"team" => $arr["obj_inst"]->id()
				));
				$extra_data = $inst->get_extra_data(array(
					"competition" => $competition,
					"team" => $arr["obj_inst"]->id(),
				));
				foreach($extra_data["data"]["groups"] as $gr)
				{
					$groups[$gr] = call_user_method("prop", obj($gr), "abbreviation");
				}
				$cmp_hidden = html::hidden(array(
					"name" => "competition",
					"value" => $competition,
				));
				foreach($members as $oid => $obj)
				{
					$reg = (in_array($oid, $contestants))?true:false;
					$pers = obj($cont_inst->get_contestant_person(array(
						"contestant" => $oid,
					)));
					$url = $this->mk_my_orb("change",array(
						"class" => "scm_contestant",
						"id" => $oid,
						"return_url" => get_ru(),
					));
					$link = html::href(array(
						"url" => $url,
						"caption" => $pers->prop("lastname").", ".$pers->prop("firstname"),
					));
					$chbox = html::checkbox(array(
						"name" => "reg[".$oid."]",
						"checked" => $reg,
					));

					$p_obj = obj($cont_inst->get_contestant_person(array("contestant" => $oid)));
					$t->define_data(array(
						"contestant" => $link,
						"registered" => $chbox.$cmp_hidden,
						"status" => $inst->register_types[$comp->prop("register")],
						"groups" => count($groups)?join(", ", $groups):t("M&auml;&auml;ramata"),
						"sex" => (($s = $p_obj->prop("gender")) == 1)?t("Mees"):(($s == 2)?t("Naine"):t("Sugu m&auml;&auml;ramata")),
						"birthday" => (($s = $p_obj->prop("birthday")) == -1)?t("M&auml;&auml;ramata"):$s,
					));
				}
			break;

			case "registered_tbl":
				$t = &$prop["vcl_inst"];
				$t->define_field(array(
					"name" => "competition",
					"caption" => t("V&otilde;istlus"),
					"sortable" => true,
				));
				$t->define_field(array(
					"name" => "status",
					"sortable" => true,
					"caption" => t("V&otilde;istluse staatus"),
				));
				$t->define_field(array(
					"name" => "time",
					"caption" => t("Toimumisaeg"),
					"sortable" => true,
					"callback" => array(&$this, "__format_time"),
				));
				$t->define_field(array(
					"name" => "organizer",
					"caption" => t("Korraldaja"),
				));
				$t->set_default_sortby("competition");

				$inst = get_instance(CL_SCM_COMPETITION);	
				$comps = $this->get_competitions(array(
					"team" => $arr["obj_inst"]->id(),
				));
				
				$nr = 0;
				foreach($comps as $oid => $obj)
				{
					$nr++;
					// shitload of ajax crap.. i just hate it!!
					$namp = "";
					$url = $this->mk_my_orb("get_contestants_row_table", array(
						"competition" => $oid,
						"team" => $arr["obj_inst"]->id(),
						"return_url" => get_ru(),
					));
					$namp = " (<a id='tnr$nr' href='javascript:void(0)' onClick='
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

					$o = ($s = $inst->get_organizer(array("competition" => $oid)))?obj($s):false;
					// status
					$status = $inst->register_types[$obj->prop("register")];
					//time
					$start = $obj->prop("date_from");
					$end = $obj->prop("date_to");

					$t->define_data(array(
						"competition" => $obj->name()." ".$namp,
						"status" => $status,
						"organizer" => $o->name(),
						"time" => $start.".".$end,
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
			case "members_tbl":
				$request = $arr["request"];

				// otsingust tulevate liikmete sidumine meeskonna liikmeks
				$sr = (strlen($request["search_result"]))?split(",", $request["search_result"]):NULL;
				$list = array_keys($this->get_team_members($arr["obj_inst"]->id()));
				foreach($sr as $contestant)
				{
					if(!in_array($contestant, $list))
					{
						$arr["obj_inst"]->connect(array(
							"type" => "RELTYPE_SCM_TEAM_MEMBER",
							"to" => $contestant,
						));
					}
				}
			break;

			case "members_unreg":
				$rem = count($r = $arr["request"]["rem"])?$r:false;
				foreach($rem as $contestant)
				{
					$arr["obj_inst"]->disconnect(array(
						"from" => $contestant,
					));
				}
			break;
		}
		return $retval;
	}	

	function callback_pre_save($arr)
	{
		$list = $arr["request"]["reg"];
		$competition = $arr["request"]["competition"];
		$team = $arr["obj_inst"]->id();
		//(un)registering teams and their members
		$reg = $this->_is_registered(array(
			"competition" => $competition,
			"team" => $team,
		));
		if(!$list)
		{
			if($reg)
			{
				$reg["conn"]->delete();
			}
		}
		else
		{
			$inst = get_instance(CL_SCM_COMPETITION);

			if(!$reg)
			{
				foreach(array_keys($list) as $contestant)
				{
					$members[$contestant] = obj($contestant);
				}
				$this->register_team(array(
					"team" => $team,
					"competition" => $competition,
					"members" => $members,
				));
			}
			else
			{
				// updates registration info 
				$ed = $inst->get_extra_data(array(
					"team" => $team,
					"competition" => $competition,
				));
				foreach(array_keys($list) as $contestant)
				{
					$ed["data"]["members"][$contestant] = ($prev = $ed["data"]["members"][$contestant])?$prev:"";
				}
				foreach($ed["data"]["members"] as $memb => $id)
				{
					if(!in_array($memb, array_keys($list)))
					{
						unset($ed["data"]["members"][$memb]);
					}
				}
				$inst->save_rel_data($ed);
			}
		}
	}

	function __format_time($str)
	{
		$spl = split("[.]", $str);
		return date("d.m.Y h:i", $spl[0])." - ".date("d.m.Y", $spl[1]);
	}

	/**
		@attrib params=name
		@param competition
		@param team
		@comment
			cheks if the $team is registered to competition
		@returns
			boolean false if isn't registered, data from #scm_competition.get_extra_data otherwise 
	**/
	function _is_registered($arr)
	{
		$inst = get_instance(CL_SCM_COMPETITION);
		$res = $inst->get_extra_data(array(
			"competition" => $arr["competition"],
			"team" => $arr["team"],
		));
		return $res;
	}

	function callback_mod_retval($arr)
	{
		$url = parse_url($arr["request"]["post_ru"]);
		parse_str($url["query"]);
		$arr["args"]["competition"] = $competition;
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
	**/
	function get_teams($arr = array())
	{
		$filt["class_id"] = CL_SCM_TEAM;
		$list = new object_list($filt);
		return $list->arr();
	}

	/**
		@param competition
		@param contestant
		@comment
			theoretically should find the team according to contestant and competition
	**/
	function get_team($arr = array())
	{
		$inst = get_instance(CL_SCM_CONTESTANT);
		$comp_inst = get_instance(CL_SCM_COMPETITION);
		$comp_teams = $comp_inst->get_teams_for_competition(array("competition" => $arr["competition"]));
		//arr($inst->get_teams(array("contestant" => $arr["contestant"])));
		foreach($inst->get_teams(array("contestant" => $arr["contestant"])) as $team)
		{
			if(in_array($team, $comp_teams))
			{
				return $team;
			}
		}
	}


	
	/**
		@param team required type=oid
		@comment
			fetches all competitions where given $team has registered
		@returns
			array of competitions
			array(
				CL_SCM_COMPETITION oid => CL_SCM_COMPETITION obj
			)
	**/
	function get_competitions($arr = array())
	{
		$list = new object_list(array(
			"class_id" => CL_SCM_COMPETITION,
			"CL_SCM_COMPETITION.RELTYPE_CONTESTANT" => $arr["team"],
		));
		return $list->arr();
	}

	/**
		@param competition opitional type=oid
		@param team required type=oid
		@comment
			returns all team members if $competition isn't set, or members who have registered to given competition
		@returns
			array of asked members
			array(
				CL_SCM_CONTESTANT oid => CL_SCM_CONTESTANT obj
			)
	**/
	function get_team_members($arr = array())
	{
		$arr["team"]?$this->set_team($arr["team"]):"";
		if($arr["competition"])
		{
			$inst = get_instance(CL_SCM_COMPETITION);
			$cont = $inst->get_contestants($arr);
			foreach($cont as $id => $data)
			{
				if($this->team == $data["data"]["team"])
				{
					$ret[$id] = obj($id);
				}
			}
		}
		else
		{
			$c =  new connection();
			$conns = $c->find(array(
				"from" => $this->team,
				"to.class_id" => CL_SCM_CONTESTANT,
			));
			foreach($conns as $data)
			{
				$ret[$data["to"]] = obj($data["to"]);
			}
		}
		return $ret;
	}
	
	/**
		@param members required type=array
		@param arr required type=array
			
		@comment
			filters out team members
	**/
	function _filter_team_members($members, $arr)
	{
		if($arr["isnt_in_team"])
		{
			$team = obj($arr["team"]);

			foreach($members as $oid => $obj)
			{
				unset($conns);
				$conns = $team->connections_from(array(
					"to" => $oid,
					"type" => 2,
				));
				if(!count($conns))
				{
					unset($members[$oid]);
				}
			}
		}

		if($arr["non_registered"])
		{
			$c = new connection();
			$conns = $c->find(array(
				"from" => $arr["competition"],
				"to.class_id" => CL_SCM_TEAM,
				"type" => 6
			));
			foreach($conns as $cid => $data)
			{
				$cd = aw_unserialize($data["data"]);
				$already_reg_memb = array_keys($cd["members"]);
				foreach(array_keys($members) as $member)
				{
					if(in_array($member, $already_reg_memb))
					{
						unset($members[$member]);
					}
				}
			}
		}
		if($arr["rem_obj"])
		{
			foreach($members as $oid => $obj)
			{
				$members[$oid] = NULL;
			}
		}
	}

	/**
		@param team required type=oid
			the team to register
		@param competition required type=oid
			the competition to register to 
		@param members optional type=array
			if this is set, only these members will be registered, by default all team members are.
			array(
				contestant oid => contestant obj
			)
		@comment
			registers team into competition(doing all the nessecary checks before ofcourse)
	**/
	function register_team($arr)
	{
		if(!$arr["competition"] || !$arr["team"])
		{
			return false;
		}

		if(is_array($arr["members"]) && count($arr["members"]))
		{
			$memb = $arr["members"];
		}
		else
		{
			$memb = $this->get_team_members(array(
				"team" => $arr["team"],
			));
		}

		$this->_filter_team_members(&$memb, array(
			"non_registered" => true,
			"isnt_in_team" => true,
			"team" => $arr["team"],
			"competition" => $arr["competition"],
			"rem_obj" => true,
		));
		$data = array(
			"members" => $memb,
			"groups" => array(),
		);
		$c = new connection(array(
			"from" => $arr["competition"],
			"to" => $arr["team"],
			"reltype" => 6,
			"data" => aw_serialize($data, SERIALIZE_NATIVE),
		));
		$c->save();
		return $c->id();
	}

	// not api

	function _gen_reg_tree($t)
	{
		$inst = get_instance(CL_SCM_ORGANIZER);
		classload("core/icons");
		foreach($inst->get_organizers(array("only_with_competitions" => true)) as $oid => $obj)
		{
			$t->add_item(0, array(
				"id" => "org_".$oid,
				"name" => $obj->name(),
				"url" => "#",
				"iconurl" => icons::get_icon_url(CL_CRM_PERSON),
			));
			$t->add_item("org_".$oid, array(
				"id" => "tmp",
				"name" => $obj->name(),
			));

		}
	}

	/**
		@attrib name=gen_tree_branch all_args=1
	**/
	function gen_tree_branch($arr)
	{
		$self_id = $arr["self_id"];
		$group = $arr["group"];
		$parent = trim($arr["parent"]);
		$split = split("[.]", $parent);
		$parent = $split[0];
		if(substr($parent, 0, 3) == "org")
		{
			$inst = get_instance(CL_SCM_COMPETITION);
			$comps = $inst->get_competitions(array("organizer" => ($organizer = substr($parent, 4))));
			// loop over every competition for this organizer
			foreach($comps as $oid => $obj)
			{
				$evt = ($s = $inst->get_event(array("competition" => $oid)))?$s:false;
				//  what the hell is this here for?:S it makes no sense at all .. 
				if(in_array($evt, $evts))
				{
					continue;
				}
				// competitions with no event specified are added into special tree branch
				if($evt)
				{
					$evts[] = $evt;
					$evt_obj = obj($evt);
					$et = $evt_obj->prop("type");
					// doesn't add individual competitions here, these are added into special branch
					if($et != "single")
					{
						$tree[] = array(
							"id" => "evt_".$evt.".org_".$organizer,
							"name" => $evt_obj->name(),
						);
					}
					else
					{
						$single_et[] = $oid;
					}
				}
				else
				{
					$no_evts[] = $oid;
				}
			}
			// for competitions where the event hasn't specified yet.. oh god i hate this treeview thingie, "someone" should write it to ajax!!
			// adds special branch for competitions with no event specified
			// hahaa!!! .. when there's no event specified, theres no event type also specified.. and therefore you can't know that this
			// competition isn't individual.. so.. i did this shit for nothing, as usual..fuck
			// you can comment it back in if you wish so..
			/*
			if(count($no_evts))
			{
				$tree[] = array(
					"id" => "evt_no.org_".$organizer,
					"name" => t("Spordiala m&auml;&auml;ramata"),
				);
			}
			*/
			// adds special branch for competitions with individual event type
			// okey.. til further notice.. individual competitions are ignored in team class
			/*
			if(count($single_et))
			{
				$tree[] = array(
					"id" => "evt_single.org_".$organizer,
					"name" => t("Individuaalsed v&otilde;istlused"),
				);
			}
			*/
		}
		elseif(substr($parent, 0, 3) == "evt")
		{
			$inst = get_instance(CL_SCM_COMPETITION);
			$comps = $inst->get_competitions(array("organizer" => substr($split[1], 4)));
			foreach($comps as $oid => $obj)
			{
				if(($evt_oid = ($s = $inst->get_event(array("competition" => $oid)))?$s:"no") != substr($parent, 4))
				{
					continue;
				}
				$tree[] = array(
					"id" => "cmp_".$oid,
					"name" => $obj->name(),
					"url" => $this->mk_my_orb("change", array(
						"id" => $self_id,
						"competition" => $oid,
						"group" => $group,
					)),
				);
			}
		}

		classload("core/icons");
		$t = get_instance("vcl/treeview");
		$t->start_tree(array(
			"type" => TREE_DHTML,
			"branch" => 1,
			"tree_id" => "reg_tree",
		));
		foreach($tree as $data)
		{
			$t->add_item(0, array(
				"id" => $data["id"],
				"name" => $data["name"],
				"url" => ($data["url"])?$data["url"]:"#",
			));
			if(substr($parent, 0, 3) != "evt")
			{
				$t->add_item($data["id"], array(
					"id" => PI,
				));
			}
		}

		die($t->finalize_tree());
	}
	
	function _gen_members_tbl($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "sex",
			"caption" => t("Sugu"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "birthday",
			"caption" => t("S&uuml;nniaeg (vanus)"),
			"align" => "center",
			"callback" => array(&$this, "_birthday_format"),
			"sortable" => true,
		));
		$t->define_field(array(
			"name" => "company",
			"caption" => t("Firma"),
			"sortable" => 1,
		));
		$t->define_chooser(array(
			"name" => "rem",
			"field" => "rem_contestant",
		));
		$t->set_default_sortby("name");

	}

	function _gen_members_reg_tbl($t, $gr_options)
	{
		$t->define_field(array(
			"name" => "contestant",
			"caption" => t("Liige"),
			"sortable" => true,
		));
		$t->define_field(array(
			"name" => "registered",
			"caption" => t("Registreerunud"),
			"sortable" => true,
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "status",
			"caption" => t("V&otilde;istluse staatus"),
			"sortable" => true,
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "sex",
			"caption" => t("sugu"),
			"sortable" => true,
			"align" => "center",
			"filter" => array(
				"1" => t("Mees"),
				"2" => t("Naine"),
			),
			"filter_compare" => array(&$this, "__sex_filter"),
		));
		$t->define_field(array(
			"name" => "birthday",
			"caption" => t("S&uuml;nniaeg (vanus)"),
			"sortable" => true,
			"align" => "center",
			"callback" => array(&$this, "_birthday_format"),
		));
		$t->define_field(array(
			"name" => "groups",
			"caption" => t("V&otilde;istlusklassid"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "birthday",
			"caption" => t("S&uuml;nniaeg"),
			"sortable" => true,
			"align" => "center",
			"callback" => array(&$this, "__birthday_format"),
		));
		$t->set_default_sortby("contestant");
	}

	function __sex_filter($key, $str, $row)
	{
		return in_array($str, $row);
	}

	function __birthday_format($key, $str, $row)
	{
		if(is_numeric($key))
		{
			return date("d / m / Y", $key);
		}
		return $key;
	}
	
	/**
		@param competition
		@param contestant
		@param ret_inst optional type=bool
			if set, returning array has an reference to located connection:
			array(
				"ret_inst" => &$connectiom,
			)
		@comment
			finds right registration connection and returns the extra data attached to it
	**/
	function _get_extra_data($arr)
	{
		$c = new connection();
		$conns = $c->find(array(
			"from" => $arr["competition"],
			"to" => $arr["contestant"],
			"reltype" => "RELTYPE_CONTESTANT",
		));
		$id = key($conns);
		$c = new connection($id);
		if($arr["ret_inst"])
		{
			$arr["ret_inst"] = $c;
		}
		$extra_data = aw_unserialize($c->prop("data"));
		return $extra_data;
	}
	
	/**
		@attrib name=change_grp all_args=1 params=name
		@comment
			changes team's group
	**/
	function change_grp($arr)
	{
		if(!($group = $arr["tb_select_option"]))
		{
			return $arr["post_ru"];
		}
		$team = $arr["id"];
		$competition = $arr["competition"];
		$inst = get_instance(CL_SCM_COMPETITION);
		$ed = $inst->get_extra_data(array(
			"competition" => $competition,
			"team" => $team,
		));
		$grps = &$ed["data"]["groups"];
		if(($action = $arr["tb_select_action"]) == 1)
		{
			$grps[] = $group;
			$grps = array_unique($grps);
		}
		elseif($action == 2)
		{
			$grps = array_flip($grps);
			unset($grps[$group]);
			$grps = array_flip($grps);
		}
		$inst->save_rel_data($ed);

		return $arr["post_ru"];
	}

	function _birthday_format($str)
	{
		$inst = get_instance(CL_SCM_COMPETITION);
		$age = $inst->get_age($str);
		return date("d / m / Y", $str)." (".$age.")";
	}

	/**
		@attrib params=name name=get_contestants_row_table all_args=1
	**/
	function get_contestants_row_table($arr)
	{
		classload("vcl/table");
		$t = new vcl_table();
		$t->define_field(array(
			"name" => "contestant",
			"caption" => t("V&otilde;istleja"),
		));
		$t->define_field(array(
			"name" => "company",
			"caption" => t("Firma"),
		));
		$t->define_field(array(
			"name" => "sex",
			"caption" => t("Sugu"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "birthday",
			"caption" => t("S&uuml;nniaeg (vanus)"),
			"callback" => array(&$this, "_birthday_format"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "groups",
			"caption" => t("V&otilde;istlusklassid"),
			"callback" => array(&$this, "_groups_format"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "id",
			"caption" => t("S&auml;rgi nr"),
			"align" => "center",
		));
		$t->set_default_sortby("contestant");



		$memb = $this->get_team_members(array(
			"team" => $arr["team"],
			"competition" => $arr["competition"],
		));
		$cnt = get_instance(CL_SCM_CONTESTANT);
		$cmp = get_instance(CL_SCM_COMPETITION);
		foreach($memb as $oid => $obj)
		{
			$ed = $cmp->get_extra_data(array(
				"competition" => $arr["competition"],
				"team" => $arr["team"],
			));
			foreach($ed["data"]["groups"] as $group)
			{
				$o = obj($group);
				$gr[$group] = $o->name()." (".call_user_method("prop", $o, "abbreviation").")";
			}

			$pers = obj($cnt->get_contestant_person(array(
				"contestant" => $oid,
			)));
			$contestant = html::href(array(
				"caption" => $pers->prop("lastname").", ".$pers->prop("firstname"),
				"url" => $this->mk_my_orb("change", array(
					"id" => $oid,
					"class" => "scm_contestant",
					"return_url" => $arr["return_url"],
				)),
			));
			$sex = ($pers->prop("gender") == 1)?t("Mees"):t("Naine");
			$birthday = ($tmp = $pers->prop("birthday"))?$tmp:t("S&uuml;nniaeg m&auml;&auml;ramata");
			$company = obj($cnt->get_contestant_company(array(
				"contestant" => $oid,
			)));
			$hid = html::hidden(array(
				"name" => "tere",
				"value" => "value",
			));
			$t->define_data(array(
				"contestant" => $contestant,
				"sex" => $sex.$hid,
				"birthday" => $birthday,
				"company" => ($tmp = $company->name())?$tmp:t("M&auml;&auml;ramata"),
				"groups" => count($gr)?join(", ", $gr):t("M&auml;&auml;ramata"),
				"id" => !($id = $ed["data"]["members"][$oid])?t("M&auml;&auml;ramata"):$id,
			));
		}
		return iconv(aw_global_get("charset"),"UTF-8",$t->draw());
	}

}
?>
