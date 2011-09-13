<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/questionary/questionary.aw,v 1.15 2007/12/06 14:33:53 kristo Exp $
// questionary.aw - K&uuml;simustik 
/*

@classinfo syslog_type=ST_QUESTIONARY relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=tarvo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

	@property answer_count type=textbox
	@caption Valikvastuste arv
	
	@property thank_you_doc type=relpicker reltype=RELTYPE_DOC
	@caption T&auml;nudokument

@groupinfo groups caption=Grupid
@default group=groups
	@property gr_tb type=toolbar no_caption=1
	@property groups type=table no_caption=1

@groupinfo results caption=Vastatud submit=no
@default group=results
	@property results type=text
	@caption Vastuseid
	
	@property get_results type=text
	@caption Ekspordi vastused

@reltype GROUP value=1 clid=CL_QUESTION_GROUP
@caption K&uml;simustegrupp

@reltype DOC value=2 clid=CL_DOCUMENT
@caption T&auml;nudokument

*/

class questionary extends class_base
{
	function questionary()
	{
		$this->init(array(
			"tpldir" => "questionary",
			"clid" => CL_QUESTIONARY
		));
		$this->init_data();
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- get_property --//
			case "gr_tb":
				$tb = &$prop["vcl_inst"];
				$tb->add_button(array(
					"name" => "action",
					"img" => "new.gif",
					"tooltip" => t("Uus grupp"),
					"url" => $this->mk_my_orb("new", array(
						"alias_to" => $arr["obj_inst"]->id(),
						"reltype" => 1,
						"parent" => $arr["obj_inst"]->id(),
						"return_url" => get_ru(),
					), CL_QUESTION_GROUP), 
				));
			break;
			case "groups":
				$t = &$prop["vcl_inst"];
				$t->define_field(array(
					"name" => "name",
					"caption" => t("Nimi"),
				));
				foreach($this->get_groups($arr["obj_inst"]->id()) as $oid => $obj)
				{
					$url = $this->mk_my_orb("change", array(
						"id" => $oid,
						"return_url" => get_ru(),
					), CL_QUESTION_GROUP);
					$t->define_data(array(
						"name" => html::href(array(
							"caption" => $obj->name(),
							"url" => $url,
						)),
					));
				}
			break;
			case "results":
				$prop["value"] = count($this->get_results($arr["obj_inst"]->id()));

				break;
			case "get_results":
				$prop["value"] = html::href(array(
					"caption" => t("Ekspordi tulemused"),
					"url" => $this->mk_my_orb("change", array(
						"id" => $arr["obj_inst"]->id(),
						"return_url" => get_ru(),
						"group" => $arr["request"]["group"],
						"export" => 1,
					), CL_QUESTIONARY),
				));
				if($arr["request"]["export"])
				{
					$res = $this->get_results($arr["obj_inst"]->id());
					$this->gen_csv_output($res, $arr["obj_inst"]->id());
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

	function callback_mod_reforb($arr, $request)
	{
		$arr["post_ru"] = post_ru();
	}

	function parse_alias($arr)
	{
		$arr["id"] = $arr["alias"]["to"];
		return $this->show($arr);
	}

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
	function show($arr)
	{
		$t = $GLOBALS["_GET"];
		$_unans = $t["questionary_unanswered"]?aw_unserialize(aw_global_get("questionary_unanswered")):array();
		$_answers = $t["questionary_unanswered"]?aw_unserialize(aw_global_get("questionary_answers")):array();

		$questionary_id = $arr["id"];
		if($t["questionary_submitted"])
		{
			$o = obj($questionary_id);
			$docid = $o->prop("thank_you_doc");
			if(!$docid)
			{
				$this->read_template("thank_you.tpl");
				return $this->parse();
			}
			header("Location:".aw_ini_get("baseurl")."/".$docid);
		}

		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");

		$gr = $this->get_groups($arr["id"]);
		$gr_inst = get_instance(CL_QUESTION_GROUP);
		$size = 10;

		foreach($gr as $oid => $obj)
		{
			$no_answer = !$obj->prop("no_answer");
			unset($header, $rows);
			// table header
			$questions = $gr_inst->get_questions($oid);
			foreach($questions as $o)
			{
				$this->vars(array(
					"question_name" => $o->name(),
				));
				$header .= $this->parse("QUESTION");
			}
			if(!$no_answer)
			{
				$this->vars(array(
					"question_name" => t("Ei oska &ouml;elda"),
				));
				$header .= $this->parse("QUESTION");
			}
			$this->vars(array(
				"corner_caption" => t("topic\question"),
				"QUESTION" => $header,
			));
			$header = $this->parse("HEADER");

			$topics = $gr_inst->get_topics($oid);
			foreach($topics as $o)
			{
				unset($answer);
				$arr["topic"] = $o->id();
				$arr["group"] = $oid;
				foreach($questions as $q_o)
				{
					$arr["question"] = $q_o->id();
					$this->vars(array(
						"answer_element" => $this->_get_answer_element($arr, $_answers),
					));
					$answer .= $this->parse("ANSWER");
					//$topic_questions[] = $q_o->id();
				}
				if(!$no_answer)
				{
					$checkbox = html::checkbox(array(
							"name" => "no_answer[".$oid."][".$arr["topic"]."]",
							"checked" => $_answers["no_answer"][$oid][$o->id()],
							"title" => "Group ".$arr["group"]."_".$arr["topic"]." Master",
					));
					$this->vars(array(
						"answer_element" => "<span title=\"Group".$arr["group"]."_".$arr["topic"]."f7_Master\">".$checkbox."</span>",
					));
					$answer .= $this->parse("ANSWER");
				}
				//unset($topic_questions);
				$this->vars(array(
					"topic_name" => $o->name(),
					"ANSWER" => $answer,
					"row_color" => $_unans[$oid][$o->id()]?"#FFDFE0":"",
				));
				$rows .= $this->parse("TOPIC");
			}

			$this->vars(array(
				"span" => ($no_answer)?(count($questions) + 1):(count($questions) + 2),
				"name" => $obj->name(),
				"HEADER" => $header,
				"TOPIC" => $rows,
			));
			$groups .= $this->parse("GROUP");
		}

		// SICK FUCK PART
		// area

		foreach($this->pers["area"] as $k => $el)
		{
			$this->vars(array(
				"caption" => $el,
				"value" => $k,
				"checked" => ($_answers["pers"]["area_radio"] == $k)?"CHECKED":"",
				"prev_value" => $_answers["pers"]["area_text"][$k],
			));
			$areas .= $this->parse("PERS_AREA");
		}
		// schools
		foreach($this->pers["school"] as $k => $el)
		{
			$this->vars(array(
				"caption" => $el,
				"value" => $k,
				"checked" => ($_answers["pers"]["school_radio"] == $k)?"CHECKED":"",
				"prev_value" => $_answers["pers"]["school_text"][$k],
			));
			$schs .= $this->parse("PERS_SCHOOL");
		}

		// intrests
		foreach($this->pers["intrests"] as $k => $el)
		{
			$this->vars(array(
				"caption" => $el,
				"value" => $k,
				"checked" => ($_answers["pers"]["intrest_check"][$k])?"CHECKED":"",
				"prev_value" => $_answers["pers"]["intrest_text"][$k],
			));
			$area2 .= $this->parse("S_AREA");
		}

		// visites to library
		foreach($this->pers["visits"] as $k => $el)
		{
			$this->vars(array(
				"caption" => $el,
				"value" => $k,
				"checked" => ($_answers["pers"]["visits"] == $k)?"CHECKED":"",
			));
			$visits .= $this->parse("VISITS");
		}

		// usage

		foreach($this->pers["usage"] as $k => $el)
		{
			$this->vars(array(
				"caption" => $el,
				"value" => $k,
				"checked" => ($_answers["pers"]["usage"] == $k)?"CHECKED":"",
			));
			$usage .= $this->parse("USAGE");
		}
		
		$this->vars(array(
			"PERS_AREA" => $areas,
			"PERS_SCHOOL" => $schs,
			"S_AREA" => $area2,
			"VISITS" => $visits,
			"USAGE" => $usage,
			"gender_".$_answers["pers"]["gender"] => "CHECKED",
			"age_".$_answers["pers"]["age"] => "CHECKED",
		));
		$formdata = $this->parse("PERS_DATA");
	

		// END OF SICK FUCK PART
		$this->vars(array(
			"GROUP" => $groups,
			"name" => $ob->prop("name"),
			"PERS_DATA" => $formdata,
			"reforb" => $this->mk_reforb("add_result", array(
				"questionary" => $questionary_id,
				"return_url" => post_ru(),
			)),
			"submit_caption" => t("Vasta"),
			"ANSWERS_MISSING" => ($t["questionary_unanswered"])?$this->parse("ANSWERS_MISSING"):"",
			"pers_comment" => $_answers["pers"]["comment"],

		));
		return $this->parse();
	}
	
//-- methods --//

	function _get_answer_element($arr, $_answers)
	{
		$o = obj($arr["id"]);
		$ans = $GLOBALS["_GET"]["answer"];
		//arr($ans);
		//arr($arr["group"]."/".$arr["topic"]."/".$arr["question"]);
		$a_count = $o->prop("answer_count");
		for($i=1; $i <= $a_count; $i++)
		{
			$sel = ($ans[$arr["group"]][$arr["topic"]][$arr["question"]] == $i)?true:false;
			$radio = html::radiobutton(array(
				"name" => "answer[".$arr["group"]."][".$arr["topic"]."][".$arr["question"]."]",
				"value" => $i,
				"checked" => ($_answers["answer"][$arr["group"]][$arr["topic"]][$arr["question"]] == $i)?true:false,
			));
			$this->vars(array(
				"nr" => $i,
				"html_element" => "<span title=\"Group".$arr["group"]."_".$arr["topic"]."\">".$radio."</span>",
			));
			$elements .= $this->parse("INPUT");
		}
		$this->vars(array(
			"INPUT" => $elements,
		));
		return $this->parse("A_ELEMENT");
	}

	/**
	**/
	function get_groups($oid)
	{
		$c = new connection();
		$conns = $c->find(array(
			"from" => $oid,
			"from.class_id" => CL_QUESTIONARY,
			"to.class_id" => CL_QUESTION_GROUP,
			"type" => "RELTYPE_GROUP",
			"sort" => "to.jrk"
		));
		foreach($conns as $cdata)
		{
			$ret[$cdata["to"]] = obj($cdata["to"]);
		}
		uasort($ret, create_function('$a,$b', 'return $a->prop("jrk") - $b->prop("jrk");'));
		return $ret;
	}


	function check_answers($arr)
	{
		$gr = $this->get_groups($arr["questionary"]);
		$gr_inst = get_instance(CL_QUESTION_GROUP);
		foreach($gr as $oid => $obj)
		{
			$topics = $gr_inst->get_topics($oid);
			$questions = $gr_inst->get_questions($oid);
			foreach($topics as $topic_id => $topic_obj)
			{
				if(!$arr["no_answer"][$oid][$topic_id])
				{
					// no_answer wasn't checked.. lets check now if all questions were answered then
					foreach($questions as $question_id => $question_obj)
					{
						if(!in_array($question_id, array_keys($arr["answer"][$oid][$topic_id])))
						{
							$wrong[$oid][$topic_id] = 1;
						}
					}
				}
			}
		}
		if(count($wrong))
		{
			aw_session_set("questionary_answers", aw_serialize($arr, SERIALIZE_NATIVE));
			aw_session_set("questionary_unanswered", aw_serialize($wrong, SERIALIZE_NATIVE));
			return false;
		}
		else
		{
			aw_session_set("questionary_answers", "");
			aw_session_set("questionary_unanswered", "");
		}

		return true;
	}

	/**
		@attrib params=name name=add_result all_args=1 nologin=1
	**/
	function add_result($arr)
	{
		if(!$this->check_answers($arr))
		{
			$url = parse_url($arr["return_url"]);
			return $url["scheme"]."://".$url["host"].$url["path"]."?questionary_unanswered=1";
		}

		$o = obj();
		$o->set_class_id(CL_ANSWERER);
		$o->set_parent($arr["questionary"]);
		$o->set_name("Küsimustikule vastaja");
		$o->save();
		$o->set_prop("questionary", $arr["questionary"]);
		$o->set_prop("gender", $this->pers["gender"][$arr["pers"]["gender"]]);
		$o->set_prop("age", $this->pers["age"][$arr["pers"]["age"]]);
		$o->set_prop("questionary_comment", $arr["pers"]["comment"]);
		if(!($a = $arr["pers"]["area_radio"]))
		{
			foreach($arr["pers"]["area_text"] as $k => $v)
			{
				if(strlen($v))
				{
					$area = $this->pers["area"][$k].", ".$v;
					break;
				}
			}
		}
		elseif($a == count($this->pers["area"]))
		{
			$area = "muu,´".$arr["pers"]["area_text"][$a];
		}
		else
		{
			$area = $this->pers["area"][$a].", Tallinnast";
		}
		$o->set_prop("area", $area);
		# SHCOOL
		if(!($a = $arr["pers"]["school_radio"]))
		{
			foreach($arr["pers"]["school_text"] as $k => $v)
			{
				if(strlen($v))
				{
					$school = $this->pers["school"][$k].", ".$v;
					break;
				}
			}
		}
		elseif($a == count($this->pers["area"]))
		{
			$school = "muu, ".$arr["pers"]["school_text"][$a];
		}
		else
		{
			$school = $this->pers["school"][$a].", ".$arr["pers"]["school_text"][$a];
		}
		$o->set_prop("school", $school);

		# INTRESTS
		foreach($arr["pers"]["intrest_check"] as $nr => $pointless)
		{
			$intrests[] = $this->pers["intrests"][$nr].(strlen(($tmp = $arr["pers"]["intrest_text"][$nr]))?"(".$tmp.")":"");
		}

		$o->set_prop("intrests", join(", ", $intrests));

		# VISITS etc...
		$o->set_prop("visit_recur", $this->pers["visits"][$arr["pers"]["visits"]]);
		$o->set_prop("usage", $this->pers["usage"][$arr["pers"]["usage"]]);
		$o->save();

		$ans_inst = get_instance(CL_QUESTIONARY_RESULT);
		foreach($arr["answer"] as $group_id => $topics)
		{
			foreach($topics as $topic_id => $questions)
			{
				if(!$arr["no_answer"][$group_id][$topic_id])
				{
					foreach($questions as $question_id => $answer)
					{
						/*
						$anses[] = array(
							"group" => $group_id,
							"topic" => $topic_id,
							"question" => $question_id,
							"answer" => $answer,
						);
						*/
						$ans_inst->add_answer(array(
							"questionary" => $arr["questionary"],
							"question" => $question_id,
							"group" => $group_id,
							"topic" => $topic_id,
							"answer" => $answer,
							"answerer" => $o->id(),
						));
					}
				}
			}
		}
		/*
		$o->set_prop("answers", $anses);
		$o->save();
		*/
		$url = parse_url($arr["return_url"]);
		return $url["scheme"]."://".$url["host"].$url["path"]."?questionary_submitted=1";
	}
	/**
		@attrib params=pos api=1
		@param id required type=oid
	**/
	function get_results($id)
	{
		if(!is_oid($id))
		{
			return false;
		}
		$ol = new object_list(array(
			"class_id" => CL_ANSWERER,
			"questionary" => $id,
		));

		$ol2 = $ol;
		foreach($ol2->arr() as $oid => $obj)
		{
			$ans = $obj->prop("answer");
			foreach($ans as $ans)
			{
				$rets[$oid][] = array(
					"question" => $ans["question"],
					"topic" => $ans["question_topic"],
					"group" => $ans["question_group"],
					"answer" => $ans["answer"],
				);
			}
		}

		foreach($ol->arr() as $oid => $obj)
		{
			$conns = $obj->connections_from(array(
				"type" => "RELTYPE_ANSWER",
				"to.class_id" => CL_QUESTIONARY_RESULT,
			));
			foreach($conns as $data)
			{
				$result = $data->to();
				$ret[$oid][$result->id()] = array(
					"question" => $result->prop("question"),
					"topic" => $result->prop("question_topic"),
					"group" => $result->prop("question_group"),
					"answer" => $result->prop("answer"),
				);
			}

		}
		/*
		$fun .= '$g_a = call_user_func(array(obj($a["group"]), "ord"));';
		$fun .= '$t_a = call_user_func(array(obj($a["topic"]), "ord"));';
		$fun .= '$q_a = call_user_func(array(obj($a["question"]), "ord"));';
		$fun .= '$g_b = call_user_func(array(obj($b["group"]), "ord"));';
		$fun .= '$t_b = call_user_func(array(obj($b["topic"]), "ord"));';
		$fun .= '$q_b = call_user_func(array(obj($b["question"]), "ord"));';
		$fun .= 'if($g_a < $g_b) { return -1; }';
		$fun .= 'elseif($g_a > $g_b) { return 1; }';
		$fun .= 'elseif($t_a < $t_b) { return -1; }';
		$fun .= 'elseif($t_a > $t_b) { return 1; }';
		$fun .= 'elseif($q_a < $t_b) { return -1; }';
		$fun .= 'elseif($q_a > $q_b) { return 1; }';
		$fun .= 'else return 0;';
		// same thing in one line
		//$fun_line = '$g_a = call_user_func(array(obj($a["group"]), "ord")); $t_a = call_user_func(array(obj($a["topic"]), "ord")); $q_a = call_user_func(array(obj($a["question"]), "ord")); $g_b = call_user_func(array(obj($b["group"]), "ord")); $t_b = call_user_func(array(obj($b["topic"]), "ord")); $q_b = call_user_func(array(obj($b["question"]), "ord")); if($g_a < $g_b) { return -1; } elseif($g_a > $g_b) { return 1; } elseif($t_a < $t_b) { return -1; } elseif($t_a > $t_b) { return 1; } elseif($q_a < $t_b) { return -1; } elseif($q_a > $q_b) { return 1; } else return 0;';

		//foreach($ret as $answerer => $results){ uasort($results, create_function('$a, $b', '$g_a = call_user_func(array(obj($a["group"]), "ord")); $t_a = call_user_func(array(obj($a["topic"]), "ord")); $q_a = call_user_func(array(obj($a["question"]), "ord")); $g_b = call_user_func(array(obj($b["group"]), "ord")); $t_b = call_user_func(array(obj($b["topic"]), "ord")); $q_b = call_user_func(array(obj($b["question"]), "ord")); if($g_a < $g_b) { return -1; } elseif($g_a > $g_b) { return 1; } elseif($t_a < $t_b) { return -1; } elseif($t_a > $t_b) { return 1; } elseif($q_a < $t_b) { return -1; } elseif($q_a > $q_b) { return 1; } else return 0;')); $nret[$answerer] = $results; }
		foreach($ret as $answerer => $results)
		{
			uasort($results, create_function('$a, $b', $fun));
			$nret[$answerer] = $results;
		}
		*/
		return $ret;
	}
/*
	function cmp($a, $b)
	{
		echo "<br/>".$a;
		return $b-$a;
	}
*/

	function group_ord($g, $quest)
	{
		$gr = $this->get_groups($quest);
		$k = array_keys($gr);
		return (array_search($g, $k) + 1);
	}

	function topic_ord($g, $t, $quest)
	{
		$g_inst = get_instance(CL_QUESTION_GROUP);
		$ehh = $g_inst->get_topics($g); 
		$k = array_keys($ehh);
		return (array_search($t, $k) + 1);
	}

	function question_ord($g, $q, $quest)
	{
		$g_inst = get_instance(CL_QUESTION_GROUP);
		$ehh = $g_inst->get_questions($g);
		$k = array_keys($ehh);
		return (array_search($q, $k) + 1);

	}

	/**
		@attrib name=gen_csv_output params=name all_args=1
	**/
	function gen_csv_output($results, $questionary)
	{
		$first = true;
		
		$t = new vcl_table();
		foreach($results as $result => $answers)
		{
			unset($res, $tres);
			$answerer = obj($result);

			if($first)
			{
				/* old style
				$struct[] = "Sugu";
				$struct[] = "Vanus";
				$struct[] = "Tegevusala";
				$struct[] = "Õppimine/töötamine kõrgkoolis";
				$struct[] = "Huvivaldkond";
				$struct[] = "Rahvusraamatukogu külastan";
				$struct[] = "Raamatukogu teenuseid kasutan";
				$struct[] = "Kommentaar";
				*/
				
				// this is new.. through vcl table tryout
				$t->define_field(array(
					"name" => "date",
					"caption" => t("Aeg"),
				));
				$t->define_field(array(
					"name" => "id",
					"caption" => t("ID"),
				));
				$t->define_field(array(
					"name" => "gender",
					"caption" => t("Sugu"),
				));
				$t->define_field(array(
					"name" => "age",
					"caption" => t("Vanus"),
				));
				$t->define_field(array(
					"name" => "area",
					"caption" => t("Tegevusala"),
				));
				$t->define_field(array(
					"name" => "school",
					"caption" => t("Ã•imine/&oÃµpetamine koolis"),
				));
				$t->define_field(array(
					"name" => "intrests",
					"caption" => t("Huvivaldkond"),
				));
				$t->define_field(array(
					"name" => "visits",
					"caption" => t("Rahvusraamatukogu kÃ¼lastan"),
				));
				$t->define_field(array(
					"name" => "usage",
					"caption" => t("Rahvusraamatukogu teenuseid kasutan"),
				));
				$t->define_field(array(
					"name" => "comment",
					"caption" => t("Kommentaar"),
				));
			}
			/*
				old crap.. i leave it here just in case ..
			$res[$result][] = $answerer->prop("gender");
			$res[$result][] = $answerer->prop("age");
			$res[$result][] = html_entity_decode($answerer->prop("area"));
			$res[$result][] = html_entity_decode($answerer->prop("school"));
			$res[$result][] = html_entity_decode($answerer->prop("intrests"));
			$res[$result][] = html_entity_decode($answerer->prop("visits"));
			$res[$result][] = html_entity_decode($answerer->prop("usage"));
			$res[$result][] = html_entity_decode($answerer->prop("questionary_comment"));
			*/
			// new vcl table approach
			$tres["date"] = date("d.m.y", $answerer->created());
			$tres["id"] = $answerer->id();
			$tres["gender"] = $answerer->prop("gender");
			$tres["age"] = $answerer->prop("age");
			$tres["area"] = html_entity_decode($answerer->prop("area"));
			$tres["school"] = html_entity_decode($answerer->prop("school"));
			$tres["intrests"] = html_entity_decode($answerer->prop("intrests"));
			$tres["visits"] = html_entity_decode($answerer->prop("visit_recur"));
			$tres["usage"] = html_entity_decode($answerer->prop("usage"));
			$tres["comment"] = html_entity_decode($answerer->prop("questionary_comment"));
			foreach($answers as $ans_id => $data)
			{
				$g_no = $this->group_ord($data["group"], $questionary);
				$t_no = $this->topic_ord($data["group"], $data["topic"], $questionary);
				$q_no = $this->question_ord($data["group"], $data["question"], $questionary);
				$tmp = $g_no."_".$t_no."_".$q_no;
				if(!$t->field_exists($tmp))
				{
					$endkey = $t->define_field(array(
						"name" => $tmp,
						"caption" => $tmp,
					));
					if(!$startkey)
					{
						$startkey = $endkey;
					}
				}

				$res[$result][$tmp] = $data["answer"];
				$tres[$tmp] = $data["answer"];
				$struct[$tmp] = $tmp;
			}
			$t->define_data($tres);
			$first = false;
		}
		// sorts table answer fields by name
		$t->sort_fields($startkey, $endkey);

		header('Content-type: application/octet-stream');
		header('Content-disposition: root_access; filename="csv_output.csv"');
		print $t->get_csv_file();
		die();

		
		/* old style

		$file[] = $struct;
		foreach($res as $key => $row)
		{
			unset($newrow);
			foreach($struct as $skey => $srow)
			{
				$newrow[$skey] = $row[$skey];
			}
			$file[] = $newrow;
		}

		foreach($file as $row_nr => $row)
		{
			$row_str = join(";",$row);
			$tot_str .= $row_str."\n";
		}
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="vastused.csv"');
		die($tot_str);
		*/
	}

	function init_data()
	{
		$this->pers["gender"] = array(
			1 => "Mees",
			2 => "Naine",
		);
		$this->pers["age"] = array(
			1 => "18 või noorem",
			2 => "19-29",
			3 => "30-39",
			4 => "40-49",
			5 => "50-59",
			6 => "60 või vanem",
		);
		$this->pers["area"] = array(
			1 => "riigiteenistuja",
			2 => "teadlane, &otilde;ppej&otilde;ud",
			3 => "loomeinimene",
			4 => "spetsialist, juhtiv t&ouml;&ouml;taja",
			5 => "doktorant",
			6 => "magistrant",
			7 => "bakalaurus&otilde;ppe &uuml;li&otilde;pilane",
			8 => "&otilde;pilane",
			9 => "muu (t&auml;psustage)",
		);
		$this->pers["school"] = array(
			1 => "Tallinna &Uuml;likool",
			2 => "Tallinna Tehnika&uuml;likool",
			3 => "Eesti Muusikaakadeemia",
			4 => "Eesti Kunstiakadeemia",
			5 => "Tartu &Uuml;likool",
			6 => "Eesti Maa&uuml;likool",
			7 => "Muu (milline)",
		);
		$this->pers["intrests"] = array(
			1 => "Humanitaarteadused",
			2 => "Sotsiaalteadused",
			3 => "Loodus ja t&auml;ppisteadused",
			4 => "Tehnikateadused",
			5 => "Meditsiiin",
			6 => "P&otilde;llumajandus, aiandus, metsandus",
			7 => "Muu"
		);
		$this->pers["visits"] = array(
			1 => "Iga p&auml;ev",
			2 => "M&otilde;ne korra n&auml;dalas",
			3 => "M&otilde;ne korra kuus",
			4 => "M&otilde;ne korra aastas",
		);
		$this->pers["usage"] = array(
			1 => "Ainult E-raamatukogu RR-i kodulehel",
			2 => "Peamiselt E-raamatukogu RR-i kodulehel",
			3 => "Ainult raamatukoguhoones",
			4 => "Peamiselt raamatukoguhoones",
			5 => "Kasutan k&otilde;iki v&otilde;imalusi",
		);
	}
}
?>
