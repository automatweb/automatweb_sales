<?php
// poll.aw - Generic poll handling class
// latest version - all answer data is in metadata "answers"[lang_id][answer_id] array, poll_answers is just to count clicks.

/*

@classinfo trans=1 relationmgr=yes

@groupinfo clicks caption=Klikke

	@groupinfo clicks_stats caption=Statistika parent=clicks
	@groupinfo clicks_detail caption=Vastajad parent=clicks

@groupinfo translate caption=T&otilde;lgi
@groupinfo activity caption=Aktiivsus
@groupinfo data caption=Andmed parent=general
@groupinfo settings caption=Seaded parent=general


@property clicks type=text store=no group=clicks_detail
@caption Klikid

@property clicks_stats type=text store=no group=clicks_stats
@caption Statistika

@default group=general

@default group=data

@property name type=textbox rel=1 trans=1 table=objects
@caption Nimi

@property comment type=textbox table=objects
@caption Kommentaar

@property in_archive type=checkbox ch_value=1 field=meta method=serialize table=objects
@caption Arhiivis

@property question type=textbox store=no
@caption K&uuml;simus

@property answers type=callback callback=callback_get_answers store=no editonly=1
@caption Vastused

@property activity type=table group=activity no_caption=1
@caption Aktiivsus

@property translate type=callback group=translate callback=callback_get_translate store=no
@caption T&otilde;lgi

@default group=settings

@property tpl_question type=select field=meta method=serialize table=objects
@caption Kujundusp&otilde;hi kuvamiseks

@property tpl_results type=select field=meta method=serialize table=objects
@caption Kujundusp&otilde;hi tulemustele

*/

class poll extends class_base implements main_subtemplate_handler
{
	function poll()
	{
		$this->init(array(
			"tpldir" => "poll",
			"clid" => CL_POLL
		));
		lc_site_load("poll",$this);
	}

	function get_answers($id)
	{
		$o = obj($id);
		$ans = $o->meta("answers");

		$ret = empty($ans[$o->lang_id()]) ? array() : $ans[$o->lang_id()];

		$data = array();
		$this->db_query("SELECT * FROM poll_answers WHERE poll_id = '$id' ORDER BY id");
		while ($row = $this->db_next())
		{
			$data[$row["id"]] = $row;
		}

		$awa = empty($ans[aw_global_get("lang_id")]) ? new aw_array() : new aw_array($ans[aw_global_get("lang_id")]);
		foreach($awa->get() as $aid => $aval)
		{
			$data[$aid]["answer"] = $aval;
			$ret[$aid] = $data[$aid];
		}

		if (!is_array($ret))
		{
			return array();
		}
		return $ret;
	}

	function get_active_poll()
	{
		$apid = $this->get_cval("active_poll_id_".aw_ini_get("site_id"));
		if (!$apid)
		{
			// try the old way
			$apid = $this->db_fetch_field("SELECT oid FROM objects WHERE class_id = ".CL_POLL." AND status = 2 AND parent =".aw_ini_get("site_id"),"oid");
			if (!$apid)
			{
				// try the oldest way
				$apid = $this->db_fetch_field("SELECT oid FROM objects WHERE class_id = ".CL_POLL." AND status = 2 ","oid");
			}
		}
		if (!$this->can("view", $apid))
		{
			return false;
		}
		return obj($apid);
	}

	////
	// !Generates HTML for the user
	function gen_user_html($id = false)
	{

		$lid = aw_global_get("lang_id");
		$section = aw_global_get("section");
		$def = 0;
		if ( !$this->can("view", $section) )
		{
			return "";
		}

		if ($id)
		{
			if (!$this->can("view", $id))
			{
				return "";
			}
			$ap = obj($id);
			$tplsrc = (strlen($ap->meta('tpl_question'))>1)?$ap->meta('tpl_question'):'poll_embed.tpl';
			$this->read_any_template($tplsrc);
		}
		else
		{
			if (!($ap = $this->get_active_poll()))
			{
				return "";
			}
			$def = true;
			$this->read_any_template("poll.tpl");
		}
		if (!empty($_GET["c_set_answer_id"]) || !empty($GLOBALS["answer_id"]) && !$GLOBALS["class"] && $GLOBALS["poll_id"] == $id)
		{
			return $this->show($GLOBALS["poll_id"]);
		}


		if (is_array($ap->meta("name")))
		{
			$namear = $ap->meta("name");
		}
		else
		{
			$namear = $ap->meta("names");
		}

		$poll_id = $ap->id();

		$this->vars(array(
			"poll_id" => $poll_id,
			"section" => $section,
			"question" => ($namear[$lid] == "" ? $ap->name() : $namear[$lid]),
			"set_lang_id" => $lid
		));

		$so = obj($section);
		$pt = $so->path();
		$md = aw_ini_get("menuedit.menu_defs");
		foreach($md as $id => $nm)
		{
			foreach($pt as $idx => $o)
			{
				if ($o->id() == $id && $pt[$idx+1])
				{
					foreach(explode(",", $nm) as $nmm)
					{
						$this->vars(array(
							"sel_menu_".$nmm."_L1_id" => $pt[$idx+1]->id()
						));
					}
					break;
				}
			}
		}

		$ans = $this->get_answers($poll_id);
		if(empty($GLOBALS["poll_disp_count"]))
		{
			$GLOBALS["poll_disp_count"] = 0;
		}
		$GLOBALS["poll_disp_count"]++;

		reset($ans);
		$as = "";
		while (list($k,$v) = each($ans))
		{
			$o_l = $this->mk_my_orb("show", array("poll_id" => $poll_id, "c_set_answer_id" => $k, "section" => $section));
			if ($def)
			{
				$au = "javascript:window.location.href='" . $this->mk_my_orb("show", array("poll_id" => $poll_id, "c_set_answer_id" => $k, "section" => $section)) . "'";
			}
			else
			{
				$au = "javascript:window.location.href='/?section=".$section."&poll_id=".$poll_id."&c_set_answer_id=".$k."&section=".$section . "'";
			}
			if (is_admin())
			{
				$au = aw_url_change_var(array("answer_id" => $k, "poll_id" => $poll_id));
			}
			if (isset($v["answer"]))
			{
				$this->dequote($v);
			}

			$this->vars(array(
				"answer_id" => $k,
				"answer_id_uniq" => $GLOBALS["poll_disp_count"].$k,
				"answer" => is_array($v) ? $v["answer"] : $v,
				"click_answer" => str_replace("&", "&amp;", $au),
				"click_answer_js" => $o_l,
				"clicks" => $v["clicks"],
			));

			if ($v["answer"] != "")
			{
				$as.=$this->parse("ANSWER");
			}
		}
		if ($def)
		{
			$au = $this->mk_my_orb("show", array("poll_id" => $poll_id));
		}
		else
		{
			$au = "/?section=".$section."&poll_id=".$poll_id;
		}

		$this->vars(array(
			"ANSWER" => $as,
			"show_url" => str_replace("&", "&amp;", $au),
		));
		$str =  $this->parse();
		return $str;
	}

	function add_click($aid)
	{
		$polls_clicked = $_COOKIE["polls_clicked"];
		$poa = unserialize($polls_clicked);

		// block google
		if (strpos($_SERVER["HTTP_USER_AGENT"], "Google") !== false)
		{
			return;
		}
		if (strpos($_SERVER["HTTP_USER_AGENT"], "spider") !== false)
		{
			return;
		}

		$this->quote($aid);
		$poll_id = $this->db_fetch_field("SELECT poll_id FROM poll_answers WHERE id = '$aid'", "poll_id");

		if ($poa[$poll_id] != 1)
		{
			$REMOTE_ADDR = aw_global_get("REMOTE_ADDR");
			$ip = aw_global_get("HTTP_X_FORWARDED_FOR");
			if (!inet::is_ip($ip))
			{
				$ip = $REMOTE_ADDR;
			}
			$this->db_query("UPDATE poll_answers SET clicks=clicks+1 WHERE id = '$aid'");
			$this->db_query("INSERT INTO poll_clicks(uid, ip, date, poll_id, answer_id) VALUES('".aw_global_get("uid")."','$ip',".time().",'$poll_id','$aid')");
		}

		$poa[$poll_id] = 1;
		setcookie("polls_clicked", serialize($poa),time()+24*3600*1000,"/");
	}

	/**

		@attrib name=show params=name nologin="1" default="0"

		@param poll_id required type=int
		@param answer_id optional type=int

		@returns


		@comment

	**/
	function show($id)
	{
                if ($_GET["c_set_answer_id"])
                {
                        // try to set the answer id via a cookie
                        $_COOKIE["poll_set_answer_id"] = $_GET["c_set_answer_id"];
                        setcookie("poll_set_answer_id", $_GET["c_set_answer_id"],time()+24*3600*1000,"/");
                        ///$url = aw_url_change_var("c_set_answer_id", null);
			$url = $this->mk_my_orb("show", array("poll_id" => $_GET["poll_id"], "section" => aw_global_get("section")));
                        die("<script language=javascript>window.location.href='$url';</script>");
                }

		if (is_array($id))
		{
			// orb call
			extract($id);
			$id = $poll_id;
			$def = true;
		}
		if (!$this->can("view", $id))
		{
			return "";
		}

		global $answer_id, $aid;
		if ($aid && !$answer_id)
		{
			$answer_id = $aid;
		}

                if ($_COOKIE["poll_set_answer_id"])
                {
                        $answer_id = $_COOKIE["poll_set_answer_id"];
                }

		if ($answer_id && ($GLOBALS["poll_id"] == $id || $_GET["poll_id"] == $id))
		{
			$this->add_click($answer_id);
		}

		$poll = obj($id);

		$tplsrc = (strlen($poll->meta('tpl_results'))>1)?$poll->meta('tpl_results'):'show.tpl';
		$this->read_template($tplsrc);

		$lang_id = aw_global_get("lang_id");
		$this->vars(array(
			"set_lang_id" => $lang_id
		));

		$answers = $this->get_answers($id);

		$total = 0;
		reset($answers);
		while(list($k,$v) = each($answers))
		{
			$total += $v["clicks"];
		}

		reset($answers);
		while(list($k,$v) = each($answers))
		{
			$percent = $total ? (($v["clicks"] / $total) * 100) : 0;
			$width = sprintf("%2.0f", $percent);
			$percent = sprintf("%2.1f", $percent);
			if ($lang_id == 1)
			{
				$percent = str_replace(".",",",$percent);
			}
			$mp = $this->cfg["result_width_mp"];
			$this->vars(array(
				"answer" => is_array($v) ? $v["answer"] : $v,
				"percent" => $percent,
				"width" => (int)$width*$mp,
				"clicks" => $v["clicks"],
			));
			$as.=$this->parse("ANSWER");
		}

		$this->vars(array("total_answers" => $total));

		$t = get_instance(CL_FORUM);

		// pollide arhiiv
		$filt = array(
			"class_id" => CL_POLL,
			"lang_id" => array(),
			"sort_by" => "objects.jrk ASC,objects.created DESC"
		);
		if (aw_ini_get("poll.archive_from_all_sites"))
		{
			$filt["site_id"] = array();
		}
		$ol = new object_list($filt);
		foreach($ol->arr() as $o)
		{
			$o_id = $o->id();
			if ($id != $o_id)
			{
				if ($o->meta('in_archive') == 1)
				{
					$ns = $o->meta("name");
					$_name  = $o->name();
					if (isset($ns[aw_global_get("lang_id")]))
					{
						$_name = $ns[aw_global_get("lang_id")];
					}
					$this->vars(array(
						"question" => $_name,
						"poll_id" => $o_id,
						"num_comments" => $t->get_num_comments($o_id),
						"link" => $this->mk_my_orb("show", array("poll_id" => $o_id))
					));
					$p.=$this->parse("QUESTION");
				}
			}
		}

		if (is_array($poll->meta("name")))
		{
			$namear = $poll->meta("name");
		}
		else
		{
			$namear = $poll->meta("names");
		}

		$this->dequote($namear);

		$na = $namear[aw_global_get("lang_id")];

		$this->vars(array(
			"ANSWER" => $as,
			"question" => ($na == "" ? $poll->name() : $na),
			"date" => $this->time2date($poll->modified(),2),
			"addcomment" => $t->add_comment(array("board" => $id)),
			"num_comments" => $t->get_num_comments($id),
			"poll_id" => $id,
			"QUESTION" => $p
		));

		if ($def)
		{
			$this->vars(array("HAS_ARCHIVE" => $this->parse("HAS_ARCHIVE")));
		}
		return $this->parse();
	}

	/**

		@attrib name=show_archive params=name nologin="1" default="0"

	**/
	function show_archive($id)
	{
		$this->read_template("show_archive.tpl");

		$lang_id = aw_global_get("lang_id");
		$this->vars(array(
			"set_lang_id" => $lang_id
		));

		// pollide arhiiv
		$ol = new object_list(array(
			"class_id" => CL_POLL,
			"lang_id" => array()
		));
		$section = aw_global_get("section");
		foreach($ol->arr() as $o)
		{
			if (1 == $o->prop('in_archive'))
			{
				$ns = $o->meta("name");
				$_name  = $o->name();

				if (isset($ns[$lang_id]))
				{
					$_name = $ns[$lang_id];
				}

				$this->vars(array(
					"question" => $_name,
					"poll_id" => $o_id,
					"link" => $this->mk_my_orb("show", array("poll_id" => $o->id(), "section" => $section))
				));
				$p.=$this->parse("QUESTION");
			}
		}

		$this->vars(array(
			"QUESTION" => $p
		));

		return $this->parse();
	}

	function parse_alias($args = array())
	{
		extract($args);
		if (!empty($f["target"]) && $alias["target"] == $f["target"])
		{
			return $this->show($f["target"]);
		}
		else
		{
			return $this->gen_user_html($alias["target"]);
		}
	}

	function clicks($arr)
	{
		extract($arr);
		$this->t = new aw_table(array("prefix" => "images"));
		$this->t->parse_xml_def($this->cfg["basedir"]."/xml/generic_table.xml");
		$this->t->define_field(array(
			"name" => "uid",
			"caption" => t("UID"),
			"talign" => "center",
			"align" => "center",
			"sortable" => 1,
		));
		$this->t->define_field(array(
			"name" => "ip",
			"caption" => t("IP"),
			"talign" => "center",
			"align" => "center",
			"sortable" => 1,
		));
		$this->t->define_field(array(
			"name" => "date",
			"caption" => t("Kuup&auml;ev"),
			"talign" => "center",
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
			"type" => "time",
			"format" => "d.m.y / H:i"
		));
		$this->t->define_field(array(
			"name" => "answer",
			"caption" => t("Vastus"),
			"talign" => "center",
			"align" => "center",
			"sortable" => 1,
		));

		$id = $arr["obj_inst"]->id();
		$ansa = $this->get_answers($id);

		$this->db_query("SELECT * FROM poll_clicks WHERE poll_id = '$id' AND answer_id != 0");
		while ($row = $this->db_next())
		{
			$row["answer"] = $ansa[$row["answer_id"]]["answer"];
			list($row["ip"],) = @inet::gethostbyaddr($row["ip"]);
			$this->t->define_data($row);
		}

		$this->t->set_default_sortby("date");
		$this->t->sort_by();
		return $this->t->draw();
	}

	function clicks_stats($arr)
	{
		extract($arr);
		$this->t = new aw_table(array("prefix" => "images"));
		$this->t->parse_xml_def($this->cfg["basedir"]."/xml/generic_table.xml");
		$this->t->define_field(array(
			"name" => "answer",
			"caption" => t("Vastus"),
			"talign" => "center",
			"align" => "center",
			"sortable" => 1,
		));
		$this->t->define_field(array(
			"name" => "clicks",
			"caption" => t("Klikke"),
			"talign" => "center",
			"align" => "center",
			"sortable" => 1,
			"type" => "int"
		));

		$this->t->define_field(array(
			"name" => "percent",
			"caption" => t("Protsent"),
			"talign" => "center",
			"align" => "center",
			"sortable" => 1,
			"type" => "int"
		));

		$this->t->define_field(array(
			"name" => "img",
			"caption" => t(""),
			"talign" => "center",
			"align" => "left",
			"sortable" => 0,
			"width" => "200"
		));

		$id = $arr["obj_inst"]->id();
		$ansa = $this->get_answers($id);

		$dat = array();
		$t_clicks = 0;
		$this->db_query("SELECT count(*) as cnt, answer_id FROM poll_clicks WHERE poll_id = '$id' AND answer_id != 0 group by answer_id");
		while ($row = $this->db_next())
		{
			$dat[$row["answer_id"]] = $row;
			$t_clicks += $row["cnt"];
		}

		foreach($ansa as $ansa_id => $adat)
		{
			if($t_clicks)
			{
				$pct = (100.0*$dat[$ansa_id]["cnt"]) / $t_clicks;
			}
			else
			{
				$pct = 0;
			}

			$pr = floor(($pct)+0.5);

			$img = html::img(array(
				'url' => $this->cfg['baseurl'].'/automatweb/images/bar.gif',
				'height' => 5,
				'width' => ($pr == 0 ? '1' : $pr.'%')
			));

			$this->t->define_data(array(
				"answer" => $adat["answer"],
				"clicks" => (int)$dat[$ansa_id]["cnt"],
				"percent" => number_format($pct, 2)." %",
				"img" => $img
			));
		}

		$this->t->sort_by();
		return $this->t->draw();
	}

	function on_get_subtemplate_content($arr)
	{
		$arr["inst"]->vars(array(
			"POLL" => $this->gen_user_html()
		));
	}

	function callback_get_answers($arr)
	{
		if (!is_oid($arr["obj_inst"]->id()))
		{
			return;
		}
		$ansa = $arr["obj_inst"]->meta("answers");

		$ret = array();

		$last_id = 0;
		$idx = 0;

		$ans = new aw_array($ansa[aw_global_get("lang_id")]);

		foreach($ans->get() as $a_id => $a)
		{
			$idx++;
			$ret["answers[".$a_id."]"] = array(
				"type" => "textbox",
				"name" => "answers[".$a_id."]",
				"caption" => sprintf(t("Vastus nr %s"), $idx),
				"value" => $a
			);

			$last_id = max($a_id, $last_id);
		}
		$last_id ++;
		$idx++;

		$ret["answers[".$last_id."]"] = array(
			"type" => "textbox",
			"store" => "class_base",
			"name" => "answers[".$last_id."]",
			"caption" => sprintf(t("Vastus nr %s"), $idx),
			"value" => ""
		);

		return $ret;
	}

	function get_property($arr)
	{
		$prop =& $arr["prop"];

		if ($prop["name"] == "question")
		{
			$qs = $arr["obj_inst"]->meta("name");
			$prop["value"] = $qs[aw_global_get("lang_id")];
		}
		else
		if ($prop["name"] == "clicks")
		{
			$prop["value"] = $this->clicks($arr);
		}
		else
		if ($prop["name"] == "clicks_stats")
		{
			$prop["value"] = $this->clicks_stats($arr);
		}
		else
		if ($prop["name"] == "activity")
		{
			$this->mk_activity_table($arr);
		}
		else
		if ($prop["name"] == "tpl_question" || $prop["name"] == "tpl_results")
		{
			$dir_default = $this->site_template_dir;
			$options_default = $this->get_directory(array("dir" => $dir_default));
			if(count($options_default))
			{
				$options = $options_default;
			}
			else
			{
				$pos = strpos($dir_default, 'site/templates/');
				$dir = substr($dir_default, 0, $pos).'automatweb_dev/templates/'.substr($dir_default, $pos+15);
				$options = $this->get_directory(array("dir" => $dir));
			}
			$options['.'] = '';
			ksort($options);
			$prop["options"] = $options;
		}

		return PROP_OK;
	}

	function set_property($arr)
	{
		$prop =& $arr["prop"];

		if ($prop["name"] == "question")
		{
			$qs = $arr["obj_inst"]->meta("name");
			$qs[aw_global_get("lang_id")] = $prop["value"];
			$arr["obj_inst"]->set_meta("name", $qs);
		}
		else
		if ($prop["name"] == "answers")
		{
			$ans = new aw_array($arr["request"]["answers"]);

			$answers = $arr["obj_inst"]->meta("answers");

			$tawa = new aw_array($answers[aw_global_get("lang_id")]);
			$rans = $ans->get();
			foreach($tawa->get() as $id => $val)
			{
				if (!isset($rans[$id]))
				{
					$this->db_query("DELETE FROM poll_answers WHERE id = '$id'");
				}
			}

			$tmpa = array();
			$lang_id = aw_global_get("lang_id");
			$o_id = $arr["obj_inst"]->id();
			foreach($ans->get() as $id => $val)
			{
				if ($val != "")
				{
					if (!isset($answers[$lang_id][$id]))
					{
						$tval = $val;
						$this->quote($tval);
						// manually find index
						$id = $this->db_fetch_field("SELECT MAX(id) as id FROM poll_answers", "id")+1;
						$this->db_query("INSERT INTO poll_answers(id, answer,poll_id) values($id,'".$tval."','".$o_id."')");
					}
					$tmpa[$id] = $val;
				}
			}

			$answers[$lang_id] = $tmpa;

			$arr["obj_inst"]->set_meta("answers", $answers);
		}
		else
		if ($prop["name"] == "activity")
		{
			$cfg = get_instance("config");
			$cfg->set_simple_config("active_poll_id_".aw_ini_get("site_id"), $arr["request"]["activeperiod"]);
		}
		else
		if ($prop["name"] == "translate")
		{
			$answers = array();
			$ans = new aw_array($arr["request"]["answers"]);
			foreach($ans->get() as $lid => $ldat)
			{
				$lans = new aw_array($ldat);
				foreach($lans->get() as $aid => $aval)
				{
					if ($aval != "")
					{
						$answers[$lid][$aid] = $aval;
					}
				}
			}

			$arr["obj_inst"]->set_meta("answers", $answers);
			$arr["obj_inst"]->set_meta("name", $arr["request"]["question"]);
		}


		return PROP_OK;
	}

	function mk_activity_table($arr)
	{
		// this is supposed to return a list of all active polls
		// to let the user choose the active one
		$table = &$arr["prop"]["vcl_inst"];
		$table->parse_xml_def("poll/list");

		$active = $this->get_active_poll();
		if (is_object($active))
		{
			$active = $active->id();
		}

		$pl = new object_list(array(
			"class_id" => CL_POLL,
			"site_id" => array(),
			"sort_by" => "objects.oid desc"
		));
		foreach($pl->arr() as $o)
		{
			$actcheck = checked($o->id() == $active);
			$act_html = "<input type='radio' name='activeperiod' $actcheck value='".$o->id()."'>";
			$row = $o->arr();
			$row["active"] = $act_html;
			$table->define_data($row);
		};
		$table->set_default_sortby("oid");
		$table->set_default_sorder("desc");
	}

	function callback_get_translate($arr)
	{
		$ansa = $arr["obj_inst"]->meta("answers");
		$names = $arr["obj_inst"]->meta("name");

		$ret = array();
		$lgs = languages::get_list();
		$lang_id = aw_global_get("lang_id");
		foreach($lgs as $lid => $lname)
		{
			$idx = 0;
			$adat = new aw_array($ansa[$lang_id]);

			$ret["splitter_".$lid] = array(
				"type" => "text",
				"store" => "class_base",
				"name" => "splitter_".$lid,
				"caption" => t(""),
				"no_caption" => 1,
				"value" => "<b>".$lname."</b>"
			);

			$ret["question[$lid]"] = array(
				"type" => "textbox",
				"store" => "class_base",
				"name" => "question[$lid]",
				"caption" => t("K&uuml;simus"),
				"value" => $names[$lid]
			);

			foreach($adat->get() as $a_id => $a)
			{
				$idx++;
				$ret["answers[$lid][".$a_id."]"] = array(
					"type" => "textbox",
					"store" => "class_base",
					"name" => "answers[$lid][".$a_id."]",
					"caption" => sprintf(t("Vastus nr %s "), $idx),
					"value" => $ansa[$lid][$a_id]
				);
			}
		}

		return $ret;
	}
}
