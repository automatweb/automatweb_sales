<?php
// $Header: /home/cvs/automatweb_dev/classes/contentmgmt/forum/forum.aw,v 1.25 2008/09/30 22:22:10 dragut Exp $
// forum.aw - forums/messageboards
/*
@classinfo  maintainer=dragut
        // stuff that goes into the objects table
        @default table=objects
	@default group=general
	@default field=meta
	@default method=serialize

	@property topicsonpage type=select
	@caption Teemasid lehel

        @property comments type=checkbox ch_value=1
        @caption Kommenteeritav

	@property onpage type=select
	@caption Kommentaare lehel

	@property rated type=checkbox ch_value=1
        @caption Hinnatav

	@property template type=select
	@caption Template

	@property preview type=text store=no editonly=1
	@caption Eelvaade

	@property export type=text store=no editonly=1
	@caption Ekspordi XML

	@property rates callback=callback_get_rates group=rates
	@caption Hinded

	@property addresslist type=text store=no group=rates
	@caption E-posti aadressid

	@property language callback=callback_get_languages group=languages
	@caption Keel

	@groupinfo rates caption=Hinded
	@groupinfo languages caption=Keel

*/

class forum extends class_base
{
	function forum($args = array())
	{
		extract($args);
		$this->embedded = false;

		$this->init(array(
			"tpldir" => "msgboard",
			"clid" => CL_FORUM,
		));
		// $this->sub_merge = 1;
		// to keep track of how many topics we have already drawn
		$this->topic_count = 0;

		if ($this->embedded)
		{
			global $section;
			// remember the section id to keep the layout
			if ($section)
			{
				$this->section = $section;
			}
		};

		if ($section)
		{
			$this->section = $section;
		};

		$this->lc_load("msgboard","lc_msgboard");
		lc_site_load("msgboard",&$this);
	}

	////
	// !Should be called for all functions that display a forum
	function init_forum_display($args = array())
	{
		$forum_obj = new object($args["id"]);
		if ($forum_obj->prop("language") != "")
		{
			$this->lc_load("msgboard","lc_msgboard",$forum_obj->prop("language"));

		};
	}

	function callback_get_rates($args = array())
	{
		$ratelist = new aw_array($args["prop"]["value"]);
		$nodes = array();
		$idx = 0;
		foreach($ratelist->get() as $key => $val)
		{
			$idx++;
			$nodes[] = $this->_add_rate_line($idx,$key,$val);
		};
		$idx++;
		$nodes[] = $this->_add_rate_line($idx,-1,array());
		return $nodes;
	}

	function _add_rate_line($idx,$key,$val)
	{
		$tmp = array();
		$tmp["items"][] = array(
			"name" => "rates[$idx][ord]",
			"type" => "textbox",
			"size" => 2,
			"maxlength" => 2,
			"value" => $val["ord"],
		);
		$tmp["items"][] = array(
			"name" => "rates[$idx][name]",
			"type" => "textbox",
			"size" => 30,
			"value" => $val["name"],
		);
		$tmp["items"][] = array(
			"name" => "rates[$idx][rate]",
			"type" => "textbox",
			"size" => 4,
			"maxlength" => 4,
			"value" => $val["rate"],
		);
		// show the checkbox only for existing items
		if ($key > -1)
		{
			$tmp["items"][] = array(
				"type" => "checkbox",
				"value" => 1,
				"name" => "delete[$idx]",
			);
		};
		return $tmp;
	}

	function set_property($args = array())
	{
		$data = &$args["prop"];
		$request = &$args["request"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "rates":
				$rates = new aw_array($args["request"]["rates"]);
				$delete = $args["request"]["delete"];
				$rate_array = array();
				foreach($rates->get() as $key => $val)
				{
					if (!$delete[$key] && $val["name"])
					{
						$rate_array[$key] = $val;
					};
				};
				uasort($rate_array, create_function('$a,$b','if ($a["ord"] > $b["ord"]) { return 1; } if ($a["ord"] < $b["ord"]) { return -1; } return 0;'));

				$data["value"] = $rate_array;
		};
		return $retval;
	}

	/**

		@attrib name=notify_list params=name default="0"

		@param id required type=int
		@param sortby optional

		@returns


		@comment

	**/
	function notify_list($args = array())
	{
		extract($args);
		$this->read_template("notify_list.tpl");
		$this->mk_path(0,"Muuda foorumit");

		load_vcl("table");
		$t = new aw_table(array(
			"prefix" => "nforum",
			"tbgcolor" => "#C3D0DC",
		));

		$t->parse_xml_def($this->cfg["basedir"]."/xml/generic_table.xml");
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"talign" => "center",
			"align" => "center",
			"nowrap" => "1",
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "address",
			"caption" => t("Aadress"),
			"talign" => "center",
			"align" => "center",
			"nowrap" => "1",
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "check",
			"caption" => t("Vali"),
			"talign" => "center",
			"align" => "center",
			"nowrap" => "1",
			//"sortable" => 1,
		));

		$tmp = obj($id);
		$nflist = $tmp->meta("notifylist");

		if (is_array($nflist))
		{
			foreach($nflist as $key => $val)
			{
				$t->define_data(array(
					"name" => $val["name"],
					"address" => $val["address"],
					"check" => "<input type='checkbox' name='chk[$key]' value='1'>",
				));
			}
		};

		$t->sort_by();

		$this->vars(array(
			"table" => $t->draw(),
			"change_link" => $this->mk_my_orb("change",array("id" => $id)),
			"rates_link" => $this->mk_my_orb("change_rates",array("id" => $id)),
			"reforb" => $this->mk_reforb("submit_notify_list",array("id" => $id)),
		));
		return $this->parse();
	}

	/**

		@attrib name=submit_notify_list params=name default="0"


		@returns


		@comment

	**/
	function submit_notify_list($args = array())
	{
		extract($args);
		$tmp = obj($id);
		$nflist = $tmp->meta("notifylist");
		if (is_array($chk))
		{
			foreach($chk as $key => $val)
			{
				unset($nflist[$key]);
			};
		};

		if ($newaddress && $newname)
		{
			$nflist[] = array(
				"name" => $newname,
				"address" => $newaddress,
			);
		};

		$tmp->set_meta("notifylist",$nflist);
		return $this->mk_my_orb("notify_list",array("id" => $id));
	}

	function callback_get_toolbar($args = array())
	{
		$id = $args["id"];
		if ($id)
		{
			$notify_url = $this->mk_my_orb("notify_list",array("id" => $id));
			$notify_link = "<a href='$notify_url' class='fgtitle'>E-posti aadressid</a>";
			$toolbar = &$args["toolbar"];
			$toolbar->add_cdata($notify_link);
		};
	}

	////
	// !Displays tabs
	function tabs($args = array(),$active = "")
	{
		// et mitte koiki seniseid saite katki teha
		if (not($this->cfg["tabs"]))
		{
			return "";
		};


		$id = $this->forum_id;
		$board = $this->board;
		$from = $this->from;
		// oh god, I hate this
		if (strpos(aw_global_get("REQUEST_URI"),"automatweb"))
		{
			array_push($args,"configure");
		};

		$tabs = array(
			"newtopic" => $this->mk_my_orb("add_topic",array("id" => $id,"_alias" => "forum","section" => $this->section)),
			"configure" => $this->mk_my_orb("change",array("id" => $id,"_alias" => "forum","section" => $this->section)),
			"addcomment" => $this->mk_my_orb("addcomment",array("board" => $board,"_alias" => "forum","section" => $this->section)),
			"forum_link" => $this->mk_my_orb("topics",array("id" => $id,"_alias" => "forum", "section" => $this->section)),
			"archive" => $this->mk_my_orb("topics",array("id" => $id,"_alias" => "forum", "section" => $this->section,"archive" => 1)),
			"props_link" => $this->mk_my_orb("change",array("id" => $id)),
			"mark_all_read" => $this->mk_my_orb("mark_all_read",array("id" => $id,"_alias" => "forum", "section" => $this->section)),
			"search" => $this->mk_my_orb("search",array("id" => $id,"_alias" => "forum","section" => $this->section,)),
			"search_link" => $this->mk_my_orb("search",array("id" => $id,"_alias" => "forum","section" => $this->section,)),
			"flatcomments" => $this->mk_my_orb("show",array("board" => $board,"_alias" => "forum","section" => $this->section)),
			"threadedcomments" => $this->mk_my_orb("show_threaded",array("board" => $board,"_alias" => "forum","section" => $this->section)),
			"threadedsubjects" => $this->mk_my_orb("show_threaded",array("board" => $board,"_alias" => "forum","section" => $this->section,"no_comments" => 1)),
			"no_response" => $this->mk_my_orb("no_response",array("board" => $board,"_alias" => "forum","section" => $this->section)),
			"details" => $this->mk_my_orb("topics_detail",array("id" => $id, "_alias" => "forum","section" => $this->section, "from" => $from)),
			"flat" => $this->mk_my_orb("topics",array("id" => $id, "_alias" => "forum","section" => $this->section, "from" => $from)),
		);

		$captions = array(
			"newtopic" => $this->vars["LC_MSGBOARD_NEWTOPIC"],
			"addcomment" => $this->vars["LC_MSGBOARD_ADDCOMMENT"],
			"flat" => $this->vars["LC_MSGBOARD_FLATLIST"],
			"configure" => $this->vars["LC_MSGBOARD_CONFIGURE"],
			"archive" => $this->vars["LC_MSGBOARD_ARCHIVE"],
			"threadedsubjects" => $this->vars["LC_MSGBOARD_TITLES_ONLY"],
			"mark_all_read" => $this->vars["LC_MSGBOARD_MARK_ALL_READ"],
			"search" => $this->vars["LC_MSGBOARD_SEARCH"],
			"no_response" => $this->vars["LC_MSGBOARD_NO_RESPONSE"],
			"details" => $this->vars["LC_MSGBOARD_FORUM"],
			"flatcomments" => $this->vars["LC_MSGBOARD_SORTBY_DATE"],
			"threadedcomments" => $this->vars["LC_MSGBOARD_SORTBY_THREAD"],
		);

		$hide_tabs = array();
		if ($this->cfg["hide_tabs"])
		{
			$hide_tabs = explode(",",$this->cfg["hide_tabs"]);
		};

		$retval .= "";
		$this->read_template("tabs.tpl");
		$hide_tabs_if_forum_is_doc = array("flat","addcomment","search","details");
		foreach($args as $key => $val)
		{
			if ( (in_array($val,$hide_tabs_if_forum_is_doc)) && ($board == $this->section))
			{
				continue;
			};
			if ( in_array($val,$hide_tabs) )
			{
				continue;
			};

			if ( ($val == "newtopic") && $this->cfg["newtopic_logged_only"] == 1 && aw_global_get("uid") == "" )
			{
				// suck
			}
			else
			{
				if ($captions[$val])
				{
					$this->vars(array(
						"link" => $tabs[$val],
						"caption" => $captions[$val],
					));
					$tpl = ($active == $val) ? "active_tab" : "tab";
					$retval .= $this->parse($tpl);
				};
			};

		}
		$this->vars(array(
			"tab" => $retval,
		));
		return $this->parse();

	}

	////
	// !Generates links for forum templates. This has to be in one central place
	// to make it easier to alter the way links are shown
	// TODO: we conver those links into tabs .. which would then use the TAB subtemplate
	// in the template to display links. That would really be much more dynamic
	function mk_links($args = array())
	{
		extract($args);
		classload("document");
		$alias = ($this->embedded) ? "forum" : "forum";

		if ($id)
		{
			$this->vars(array(
				"newtopic_link" => $this->mk_my_orb("add_topic",array("id" => $id,"_alias" => $alias,"section" => $this->section)),
				"forum_link" => $this->mk_my_orb("topics",array("id" => $id,"_alias" => $alias, "section" => $this->section)),
				"props_link" => $this->mk_my_orb("configure",array("id" => $id)),
				"mark_all_read" => $this->mk_my_orb("mark_all_read",array("id" => $id,"_alias" => $alias, "section" => $this->section)),
				"search_forum_link" => $this->mk_my_orb("search",array("id" => $id,"_alias" => $alias,"section" => $this->section,)),
				"search_link" => $this->mk_my_orb("search",array("id" => $id,"_alias" => $alias,"section" => $this->section,)),
				"topic_detail_link" => $this->mk_my_orb("topics_detail",array("id" => $id, "_alias" => $alias,"section" => $this->section, "from" => $from)),
				"topic_flat_link" => $this->mk_my_orb("topics",array("id" => $id, "_alias" => $alias,"section" => $this->section, "from" => $from)),
				"flat_link" => $this->mk_my_orb("topics",array("id" => $id, "_alias" => $alias,"section" => $this->section, "from" => $from)),
			));
		}

		if ($board)
		{
			$b_obj = new object($board);
			if ($b_obj->class_id() == CL_PERIODIC_SECTION)
			{
				$topic_link = document::get_link($board);
			};
			$this->vars(array(
				"topic_link" => $topic_link,
				"threaded_link" => $this->mk_my_orb("show_threaded", array("board" => $board,"__alias" => $alias,"section" => $this->section)),
				"threaded_topic_link" => $this->mk_my_orb("show_threaded", array("board" => $board,"_alias" => $alias,"section" => $this->section)),
				"change_topic" => $this->mk_my_orb("change_topic", array("board" => $board,"_alias" => $alias,"section" => $this->section)),
				"flat_link" => $this->mk_my_orb("show",array("board" => $board,"_alias" => $alias,"section" => $this->section)),
				"search_link" => $this->mk_my_orb("search",array("board" => $board,"_alias" => $alias,"section" => $this->section)),
				"topic_detail_link" => $this->mk_my_orb("topics_detail",array("id" => $id, "from" => $from,"_alias" => $alias,"section" => $this->section)),
				"forum_link" => $this->mk_my_orb("topics",array("id" => $parent,"_alias" => $alias, "section" => $this->section)),
			));
		};
	}

	/** Kuvab uue topicu lisamise vormi

		@attrib name=add_topic params=name nologin="1" default="0"

		@param id required type=int
		@param section optional

		@returns


		@comment

	**/
	function add_topic($args = array())
	{
		// this first setting should really be configurable on per-forum basis
		if ( $this->cfg["newtopic_logged_only"] && aw_global_get("uid") == "" )
		{
			$c = get_instance("config");
			$la = get_instance("languages");
			$ld = $la->fetch(aw_global_get("lang_id"));
			$doc = $c->get_simple_config("orb_err_mustlogin_".$ld["acceptlang"]);
			if (!$doc)
			{
				$doc = $c->get_simple_config("orb_err_mustlogin");
			}

			if ($doc != "")
			{
				header("Location: $doc");
				die();
			}
			else
			{
				$this->raise_error(ERR_FORUM_LOGIN,E_ORB_LOGIN_REQUIRED,$fatal,$silent);
			}
		};
		extract($args);
		$this->init_forum_display(array("id" => $id));
		if ($section)
		{
			$this->section = $section;
		};
		$object = new object($id);
		// kui kaasa antakse section argument, siis peaks kontrollima
		// kas see ikka kuulub selle foorumi juurde
		$text = $this->mk_orb("configure",array("id" => $id));
		$this->mk_path($object->parent(),"<a href='$text'>" . $object->name() . "</a> / Lisa teema");
		$this->forum_id = $id;
		$tabs = $this->tabs(array("flat","details","newtopic","mark_all_read","archive","search"),"new
topic");
		$this->read_template("add_topic.tpl");
		$this->mk_links(array(
			"id" => $id,
		));
		$this->vars(array(
			"TABS" => $tabs,
			"reforb" => $this->mk_reforb("submit_topic",array("id" => $id,"section" => $section)),
		));
		return $this->parse();
	}

	/** Lisab uue topicu

		@attrib name=submit_topic params=name nologin="1" default="0"


		@returns


		@comment

	**/
	function submit_topic($args = array())
	{
		$this->quote($args);
		extract($args);

		if ($this->can('view', $id))
		{
			$parent_obj = new object($id);
			if ($parent_obj->class_id() ==CL_FORUM)
			{

				aw_disable_acl();
				$o = obj();
				$o->set_parent($id);
				$o->set_name($topic);
				$o->set_comment(($text) ? $text : $comment);
				$o->set_class_id(CL_MSGBOARD_TOPIC);
				$o->set_status(STAT_ACTIVE);
				$o->set_meta("author_email", $email);
				$tid = $o->save();
				aw_restore_acl();
			}
		}

		if ($section)
		{
			$retval = $this->cfg["baseurl"] . "/?section=$section";
		}
		else
		{
			$retval = $this->mk_my_orb("topics",array("id" => $id,"section" => $section));
		}
		return $retval;
	}

	/** Shows a flat list of messages

		@attrib name=show params=name nologin="1" default="0"

		@param board required type=int
		@param section optional

		@returns


		@comment

	**/
	function show($args = array())
	{
		extract($args);
		error::view_check($board);
		$board_obj = new object($board);
		$forum_obj = new object($board_obj->parent());
		$this->init_forum_display(array("id" => $forum_obj->id()));
		global $HTTP_COOKIE_VARS;
		$aw_mb_last = unserialize($HTTP_COOKIE_VARS["aw_mb_last"]);
		$aw_mb_last[$board_obj->parent()] = time();
		$meta = $forum_obj->meta();
		$board_meta = $board_obj->meta();
		setcookie("aw_mb_last",serialize($aw_mb_last),time()+24*3600*1000);
		$flink = sprintf("<a href='%s'>%s</a>",$this->mk_my_orb("configure",array("id" => $forum_obj->id())),$forum_obj->name());
		$this->mk_path($forum_obj->parent(),$flink . " / " . $board_obj->name());
		$this->_query_comments(array("board" => $board));
		$this->comm_count = 0;
		$this->section = $section;
		$this->board = $board;
		if (not($id))
		{
			$id = $forum_obj->id();
		};
		$this->forum_id = $id;

		$content = "";


		$tabs = $this->tabs(array("flat","addcomment","flatcomments","threadedcomments","threadedsubjects","no_response","search","details"),"flatcomments");
		if ($addcomment)
		{
			$tabs = $this->tabs(array("flat","addcomment","flatcomments","threadedcomments","threadedsubjects","no_response","search","details"),"addcomment");

		}
		elseif ($no_response)
		{
			$tabs = $this->tabs(array("flat","addcomment","flatcomments","threadedcomments","threadedsubjects","no_response","search","details"),"no_response");
		};
		$rated = "";
		if ($meta["rated"])
		{
			$rated = $this->_draw_ratings($meta["rates"],$board_meta);
		};
		$this->read_template("messages.tpl");


		if ($no_response)
		{
			while($row = $this->db_next())
			{
				$this->_comments[$row["parent"]][] = $row;
				$this->comm_count++;
			};
			$this->level = 1;
			$this->count_replies(0);
			if (is_array($this->_comments))
			{
			foreach($this->_comments[0] as $key => $val)
			{
				if ($this->reply_counts[$val["id"]] == 0)
				{
					$content .= $this->display_comment($val);
				};
			};
			};
		}
		elseif (not($addcomment))
		{
			while($row = $this->db_next())
			{
				$this->comm_count++;
				$content .= $this->display_comment($row);
			};
		};


		// miskit splitter tyypi funktsiooni on vaja, mis soltuvalt sellest kas tegu on adminni
		// voi dokumendi sees oleva asjaga valjastaks sobiva lingi
		if (not($id))
		{
			$id = $forum_obj->id();
		};

		$author = $this->get_author($board);
		if (empty($author))
		{
			$author = ($board_obj["last"]) ? $board_obj["last"] : $board_obj["createdby"];
		};

		$comment = stripslashes($board_obj->comment());
		$comment = str_replace("'","",$comment);
		$this->vars(array(
			"topic" => $board_obj["name"],
			"from" => $author,
			"email" => $board_obj["meta"]["author_email"],
			"created" => $this->time2date($board_obj["created"],2),
			"topic" => $board_obj->name(),
			"from" => ($board_obj->last() != "") ? $board_obj->last() : $board_obj->createdby(),
			"email" => $board_obj->meta("author_email"),
			"created" => $this->time2date($board_obj->created(),2),
			"rated" => $rated,
			"rate" => sprintf("%0.2f",$board_obj->prop("rate")),
			"text" => nl2br(create_links($comment)),
			"reforb" => $this->mk_reforb("submit_messages",array("board" => $board,"section" => $this->section,"act" => "show")),
		));

		$this->mk_links(array(
			"board" => $board,
			"id" => $id
		));

		$voteblock = "";

		if ($forum_obj->meta("rated") != "")
		{
			// lets calculate vote count for this forum
			if ($board_obj->meta("voters") == 0)
			{
				$rate = 0;
			}
			else
			{
				$rate = $board_obj->meta("votesum") / $board_obj->meta("voters");
			};

			$this->vars(array(
				"vote_reforb" => $this->mk_reforb("submit_vote",array("board" => $board)),
				"rate" => sprintf("%0.2f",$rate),
			));

			global $forum_votes;
			if ($forum_votes[$board])
			{
				$voteblock = $this->parse("ALREADY_VOTED");
			}
			else
			{
				$voteblock = $this->parse("VOTE_FOR_TOPIC");
			};
		};

		$this->vars(array(
			"CHANGE_TOPIC" => (acl_base::prog_acl("view", PRG_MENUEDIT) ? $this->parse("CHANGE_TOPIC") : "")
		));


		$this->vars(array(
			"TABS" => $tabs,
			"message" => $content,
			"VOTE_FOR_TOPIC" => $voteblock,
			"TOPIC" => $this->parse("TOPIC"),
		));
		if (acl_base::prog_acl("view", PRG_MENUEDIT))
		{
			$actions = $this->parse("actions");
		}

		if ($this->comm_count > 0)
		{
			$this->vars(array(
				"actions" => $actions,
			));
		};

		$this->vars(array(
			"forum_link" => $this->mk_my_orb("topics",array("id" => $board_obj->parent())),
		));
		$retval = $this->parse();
		$retval .= $this->add_comment(array("board" => $board,"parent" => $parent,"section" => $section,"act" => "show"));

		return $retval;

	}

	/** Submits a vote to a topic

		@attrib name=submit_vote params=name nologin="1" default="0"


		@returns


		@comment

	**/
	function submit_vote($args = array())
	{
		extract($args);
		//global $forum_votes;
		if (not($forum_votes[$args["board"]]))
		{
			$forum_votes[$args["board"]] = rand();
			session_register("forum_votes");
			$board_obj = new object($board);
			$voters = $board_obj->meta("voters") + 1;
			$votesum = $board_obj->meta("votesum") + $vote;
			$board_obj->set_meta("voters",$voters);
			$board_obj->set_meta("votesum",$votesum);
		}

		return $this->mk_my_orb("show",array("board" => $board));
	}

	/** Shows a threaded list of messages

		@attrib name=show_threaded params=name nologin="1" default="0"

		@param board required type=int
		@param section optional
		@param cid optional type=int
		@param from optional type=int
		@param no_comments optional

		@returns


		@comment

	**/
	function show_threaded($args = array())
	{
		$this->level = 0;
		$this->comments = array();
		$this->content = "";
		$tabs = "";
		extract($args);
		error::view_check($board);
		$board_obj = new object($board);
		$board_meta = $board_obj->meta();

		$forum_obj = new object($board_obj->parent());
		$this->init_forum_display(array("id" => $forum_obj->id()));
		$meta = $forum_obj->meta();
		// this is weird, I don't need that if the template is shown inside the site
		$flink = sprintf("<a href='%s'>%s</a>",$this->mk_my_orb("configure",array("id" => $forum_obj->id())),$forum_obj->name());
		$this->mk_path($forum_obj->parent(),$flink . " / " . $board_obj->name());

		$this->forum_id = $forum_obj->id();
		$this->board = $board;
		$this->section = isset($section) ? $section : "";


		$rated = "";
		if (!empty($meta["rated"]))
		{
			$rated = $this->_draw_ratings($meta["rates"],$board_meta);
		};

		if (!empty($no_comments))
		{
			$tabs = $this->tabs(array("flat","addcomment","flatcomments","threadedcomments","threadedsubjects","no_response","search","details"),"threadedsubjects");
			$tpl = "subjects_threaded.tpl";
		}
		else
		{
			$tabs = $this->tabs(array("flat","addcomment","flatcomments","threadedcomments","threadedsubjects","no_response","search"),"threadedcomments");
			$tpl = "messages_threaded.tpl";
		};
		$tpl = ($args["template"]) ? $args["template"] : $tpl;

		$this->read_template($tpl);

		$content = "";
		$this->comm_count = 0;
		$this->reply_counts = array();
		$this->_query_comments(array("board" => $board));

		$level1comments = 0;
		while($row = $this->db_next())
		{
			$this->comm_count++;
			$this->_comments[$row["parent"]][] = $row;
			if ($row["parent"] == 0)
			{
				$level1comments++;
			};
		};
		$this->from = isset($args["from"]) ? $args["from"] : 0;
		$this->level1comments = $level1comments;
		$this->level1comments_done = 0;
		$this->commentsonpage = is_numeric($meta["onpage"]) ? $meta["onpage"] : 25;

		list($this->from,$this->to) = $this->_draw_pager(array(
			"pgaction" => "show_threaded",
			"total" => $level1comments,
			"onpage" => $this->commentsonpage,
			"active" => $this->from,
		));

		if ($no_response)
		{
			$this->count_replies(0);
		}
		else
		{
			$start_from = isset($cid) ? $cid : 0;
			global $HTTP_COOKIE_VARS;
			$this->aw_mb_read = unserialize($HTTP_COOKIE_VARS["aw_mb_read"]);
			if ($cid)
			{
				$q = "SELECT * FROM comments WHERE id = '$cid'";
				$this->db_query($q);
				$crow = $this->db_next();
				$this->mark_comments = 1;
				$this->content .= $this->display_comment($crow);
			};
			if (empty($no_comments))
			{
				// if we are showing the threaded version with comments,
				// then we oughta mark them read as well, souldn't we
				$this->mark_comments = true;
			}
			$this->rec_comments($start_from);
		};

		$this->mk_links(array(
			"board" => $board,
			"id" => $forum_obj->id(),
		));


		$author = $this->get_author($board);
		if (empty($author))
		{
			$author = ($board_obj->prop("last")) ? $board_obj->prop("last") : $board_obj->createdby();
		};


		$this->vars(array(
			"TABS" => $tabs,
			"message" => $this->content,
			"reforb" => $this->mk_reforb("submit_messages",array("board" => $board,"section" => $this->section,"act" => "show_threaded")),
			"topic" => $board_obj->name(),
			"from" => $author,
			"email" => $board_obj->meta("author_email"),
			"created" => $this->time2date($board_obj->created() ,2),
			"topic" => $board_obj->name(),
			"from" => ($board_obj->last() != "") ? $board_obj->last() : $board_obj->createdby(),
			"email" => $board_obj->meta("author_email"),
			"created" => $this->time2date($board_obj->created(),2),
			"rated" => $rated,
			"board" => $board,
			"rate" => sprintf("%0.2f",$board_obj->meta("rate")),
			"text" => nl2br(create_links($board_obj->comment())),
		));

		if (acl_base::prog_acl("view", PRG_MENUEDIT))
		{
			$actions = $this->parse("actions");
		}

		if ($this->comm_count > 0)
		{
			$this->vars(array(
				"actions" => $actions,
			));
		};
		$this->vars(array(
			"TOPIC" => $this->parse("TOPIC"),
			"forum_link" => $this->mk_my_orb("topics",array("id" => $board_obj->parent())),
		));
		if ($cid)
		{
			$add_params = array("parent" => $cid,"subj" => $crow["subj"]);
		};
		if (!$no_comments)
		{
			if (!headers_sent())
			{
				setcookie("aw_mb_read",serialize($this->aw_mb_read),time()+24*3600*1000,"/");
			}
		}

		$aw_mb_last = unserialize(stripslashes($HTTP_COOKIE_VARS["aw_mb_last"]));
		$aw_mb_last[$board] = time();
		if (!headers_sent())
		{
			setcookie("aw_mb_last",serialize($aw_mb_last),time()+24*3600*1000,"/");
		}

		$retval = $this->parse();

		if (!$args["no_add_comment"])
		{
			$retval .= $this->add_comment(array_merge(array("board" => $board,"parent" => $parent,"section" => $this->section,"act" => "show_threaded","no_comments" => $no_comments),(array)$add_params));
		};

		return $retval;
	}
	/** Submits a message list

		@attrib name=submit_messages params=name default="0"


		@returns


		@comment

	**/
	function submit_messages($args = array())
	{
		extract($args);
		if (is_array($check))
		{
			// unfortunately, it's not so simple.
			// we gotta delete the comments below the to-be-deleted ones as well, so that
			// we can do the comment count quickly later
			foreach($check as $delid)
			{
				$this->req_del_comments($delid);
			}
//			$to_delete = join(",",$check);
//			$this->db_query("DELETE FROM comments WHERE id IN ($to_delete)");
		};
		return $this->mk_my_orb($act,array("board" => $board,"_alias" => "forum", "section" => $this->section));
	}

	function req_del_comments($parent)
	{
		if (!$parent)
		{
			return;
		}

		$this->save_handle();
		$this->db_query("SELECT id FROM comments WHERE parent = '$parent'");
		while ($row = $this->db_next())
		{
			$this->req_del_comments($row["id"]);
		}

		$this->db_query("DELETE FROM comments WHERE id = $parent");
		$this->restore_handle();
	}

	function _draw_ratings($args = array(),$board_meta = array())
	{
		$this->read_template("rate.tpl");
		$c = "";
		if (is_array($args))
		{
			foreach($args as $key => $val)
			{
				$this->vars(array(
					"value" => $key,
					"name" => $val["name"],
				));
				$c .= $this->parse("rate");
			}
		};

		if ($board_meta["voters"] > 0)
		{
			$ratings = sprintf("%0.02f",$board_meta["votesum"] / $board_meta["voters"]);
		}
		else
		{
			$ratings = "0.00";
		};

		$this->vars(array(
			"reforb" => $this->mk_reforb("submit_vote",array("board" => $this->board)),
			"rating" => $ratings,
			"rate" => $c,
		));

		return $this->parse();
	}



	////
	// !creates an indented list of comments
	function rec_comments($level)
	{
		if (not(is_array($this->_comments[$level])))
		{
			return;
		}
		$icons = "";

		$commcount = sizeof($this->_comments[$level]);

		$icon_prefix = "";

		if ($this->level > 0)
		{
			for ($i = 0; $i < ($this->level - 1); $i++)
			{
				$icons .= "<img src='".$this->cfg["baseurl"]."/img/forum/vert.gif'>";
			};

			$icon_prefix = "<img src='".$this->cfg["baseurl"]."/img/forum/vert.gif'>";
		}

		$cc = 0;
		foreach($this->_comments[$level] as $key => $val)
		{
			$cc++;
			$val["spacer"] = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;",$this->level);
			$val["level"] = 20 * $this->level;
			$replies = sizeof($this->_comments[$val["id"]]);
			if ($cc == $commcount)
			{
				$icon_sufix = ($replies == 0) ? "last" : "minus-last";
			}
			else
			{
				$icon_sufix = ($replies == 0) ? "node" : "minus";
			};
			$val["icons"] = $icons . $icon_prefix . "<img src='".$this->cfg["baseurl"]."/img/forum/$icon_sufix.gif'>";
			if ($this->level == 0)
			{
//arr($this->level1comments_done);
				if ($this->level1comments_done >= $this->from)
				{
					$this->content .= $this->display_comment($val);
				};
				$this->level1comments_done++;
			}
			else
			{
				$this->content .= $this->display_comment($val);
			};
			if ($this->level == 0)
			{
			//	if ($this->level1comments_done == ($this->to) + 1) // xxx why was that so ? --dragut
				if ($this->level1comments_done == $this->to)
				{
					return;
				};
			};
			$this->level++;
			$this->rec_comments($val["id"]);
			$this->level--;
		}
	}

	////
	// !counts replies under each message
	function count_replies($level)
	{
		static $use_level = 0;
		if (not(is_array($this->_comments[$level])))
		{
			return;
		}

		foreach($this->_comments[$level] as $key => $val)
		{
			if ($this->level == 1)
			{
				$use_level = $val["id"];
			};
			if ($val["response"])
			{
				$this->reply_counts[$use_level]++;
			};
			$this->level++;
			$this->count_replies($val["id"]);
			$this->level--;
		};
	}


	/** displays a reply form

		@attrib name=reply params=name nologin="1" default="0"

		@param parent required type=int
		@param section optional

		@returns


		@comment

	**/
	function reply($args = array())
	{
		extract($args);
		$q = "SELECT * FROM comments WHERE id = '$parent'";
		$this->db_query($q);
		$row = $this->db_next();
		$board_obj = new object($row["board_id"]);
		$forum_obj = new object($board_obj->parent());
		$flink = sprintf("<a href='%s'>%s</a>",$this->mk_my_orb("configure",array("id" => $forum_obj->id())),$forum_obj->name());
		$this->mk_links(array("board" => $board_obj->id(),"id" => $board_obj->parent()));
		$this->board = $board_obj->id();
		$this->mk_path($forum_obj->parent(),$flink . " / " . $board_obj->name());
		$this->section = $section;
		$tabs = $this->tabs(array("addcomment","threadedcomments","threadedsubjects","no_response","search","details"),"addcomment");
		 if ($row)
		{
			$this->read_template("messages.tpl");
			$this->vars(array(
				"topic" => $board_obj->name(),
				"from" => ($board_obj->last() != "") ? $board_obj->last() : $board_obj->createdby(),
				"created" => $this->time2date($board_obj->created(),2),
				"rate" => sprintf("%0.2f",$board_obj->meta("rate")),
				"text" => nl2br(create_links($board_obj->comment())),
			));
			$content = $this->display_comment($row);
		}
		$this->vars(array(
			"message" => $content,
			"TABS" => $tabs,
			"TOPIC" => $this->parse("TOPIC"),
		));
		$act = $this->cfg["reply_return"] != "" ? $this->cfg["reply_return"] : "show";
		return $this->parse() . $this->add_comment(array("parent" => $parent,"section" => $section,"act" => $act,"subj" => $row["subj"]));
	}

	////
	// !Displays a single comment
	// requires a loaded template with a subtemplate "message"
	function display_comment($args = array())
	{
		if ($args["response"])
		{
			$color = "#D4D4D4";
		}
		else
		{
			$color = "#ececec";
		};

		$new = "";
		if ($this->cfg["track_users"])
		{
			$uid = aw_global_get("uid");
			$board_id = $args["board_id"];
			$id = $args["id"];

			$this->save_handle();
			$uid = aw_global_get("uid");
			$q = "SELECT count(*) AS cnt FROM forum_track WHERE comm_id = '$id' AND uid = '$uid'";
			$this->db_query($q);
			$row = $this->db_next();


			if ($row["cnt"] == 0)
			{
				$new = $this->parse("NEW_MSGS");
			}
			else
			{
				$new = $this->parse("READ_MSGS");
			};
			$this->restore_handle();
		} else if (not($this->aw_mb_read[$args["id"]]))
		{
			$new = $this->parse("NEW_MSG");
		};

		$alias = ($this->embedded) ? "forum" : "";
		$this->dequote(&$args["comment"]);
		$this->dequote(&$args["subj"]);
		$this->dequote(&$args["name"]);
		$this->vars(array(
			"SHOW_COMMENT" => "",
			"spacer" => $args["spacer"],
			"level" => $args["level"],
			"from" => $args["name"],
			"email" => $args["email"],
			"icons" => $args["icons"],
			"parent" => $args["parent"],
			"subj" => ($args["subj"]) ? $args["subj"] : "(nimetu)",
			"id" => $args["id"],
			"new" => $new,
			"time" => $this->time2date($args["time"],2),
			"color" => $color,
			"comment" => nl2br(create_links($args["comment"])),
			"del_msg" => $this->mk_my_orb("del_msg", array("board" => $args["board_id"], "comment" => $args["id"],"section" => $this->section)),
			"reply_link" => $this->mk_my_orb("reply",array("parent" => $args["id"],"section" => $this->section,"_alias" => $alias,"section" => $this->section)),
			"open_link" => $this->mk_my_orb("topics_detail",array("id" => $this->forum_id,"cid" => $args["id"],"from" => $this->from,"section" => $this->section)),
			"open_link2" => $this->mk_my_orb("show_threaded",array("board" => $args["board_id"],"cid" => $args["id"],"from" => $this->from,"section" => $this->section)),
			"topic_link" => $this->mk_my_orb("show",array("board" => $args["board_id"],"section" => $this->section,"_alias" => $alias)),
		));

		if ($this->mark_comments)
		{
			$this->aw_mb_read[$args["id"]] = 1;
		};

		if ($this->cfg["track_users"])
		{
			$this->save_handle();
			// remember that this comment has already been shown
			$q = "REPLACE INTO forum_track (uid,thread_id,comm_id)
				VALUES ('$uid','$board_id','$id')";
			$this->db_query($q);
			$this->restore_handle();
		};

		if ($this->is_template("SHOW_COMMENT") && ($this->cid == $args["id"]))
		{
			$this->vars(array("SHOW_COMMENT" => $this->parse("SHOW_COMMENT")));
		};

		if ( (acl_base::prog_acl("view", PRG_MENUEDIT)) || ($this->members[aw_global_get("uid")]))
		{
			$del = $this->parse("KUSTUTA");
			$repl = $this->parse("REPLY");
		}
		$this->vars(array(
			"KUSTUTA" => $del,
			"REPLY" => $repl,
		));

		$retval = $this->parse("message");
		return $retval;
	}

	////
	// !Displays the form to add comments
	function add_comment($args = array())
	{
		extract($args);
		$this->read_template("add.tpl");
		global $HTTP_COOKIE_VARS;
		$aw_mb_name = $HTTP_COOKIE_VARS["aw_mb_name"];
		$aw_mb_mail = $HTTP_COOKIE_VARS["aw_mb_mail"];
		$aw_mb_url = $HTTP_COOKIE_VARS["aw_mb_url"];
		if ($subj)
		{
			$reply = $this->parse("reply");
		}
		else
		{
			$reply = "";
		};
		$this->section = $section;
		$cnt = $this->db_fetch_field("SELECT count(*) AS cnt
			FROM comments WHERE board_id = '$board'","cnt");
		$this->mk_links(array("board" => $board));

		if ($subj && not(preg_match("/Re:/i",$args["subj"])))
		{
			$subj = "Re: " . $subj;
		};

		$error_str = '';
		if (isset($_SESSION['aw_mb_error']) && is_array($_SESSION['aw_mb_error']))
		{
/*
				'name' =>
				'comment' => (strlen($comment) > 1) ? '' :
				'image_verification' => ($image_verification_result === true) ? '' : D
*/
			if ($_SESSION['aw_mb_error']['error']['name'] == 1)
			{
				$error_str .= t('Nime v&auml;li peab olema t&auml;idetud')."<br />";
			}
			if ($_SESSION['aw_mb_error']['error']['comment'] == 1)
			{
				$error_str .= t('Kommentaari v&auml;li peab olema t&auml;idetud')."<br />";
			}
			if ($_SESSION['aw_mb_error']['error']['image_verification'] == 1)
			{
				$error_str .= t('Sisestatud kontrollkood on vale');
			}
			$aw_mb_name = $_SESSION['aw_mb_error']['values']['name'];
			$aw_mb_mail = $_SESSION['aw_mb_error']['values']['email'];
			$args['comment'] = $_SESSION['aw_mb_error']['values']['comment'];
			$subj = $_SESSION['aw_mb_error']['values']['subj'];
			$this->vars(array(
				'error_msg' => $error_str
			));
		}

		$image_verification_oid = aw_ini_get('document.image_verification');
		$image_verification_str = '';
		if ( $this->can( 'view', $image_verification_oid ) )
		{
			aw_session_set('no_cache', 1);
			$image_verification_obj = new object($image_verification_oid);
			$this->vars(array(
				'image_verification_url' => aw_ini_get("baseurl")."/".$image_verification_oid."?rand=".rand(1, 99999),
				'image_verification_width' => $image_verification_obj->prop('width'),
				'image_verification_height' => $image_verification_obj->prop('height')
			));
			$image_verification_str = $this->parse('IMAGE_VERIFICATION');
		}
		$this->vars(array(
			"cnt" => $cnt,
			"num_comments" => $cnt,
			"name" => $aw_mb_name,
			"mail" => $aw_mb_mail,
			"url" => $aw_mb_url,
			"comment" => $args["comment"],
			"comm_link" => $this->mk_my_orb("show_threaded",array("board" => $board,"section" => aw_global_get("section")),"forum"),
			"subj" => $subj,
			"reply" => $reply,
			"IMAGE_VERIFICATION" => $image_verification_str,
			"ERROR" => $this->parse('ERROR'),
			"reforb" => $this->mk_reforb("submit_comment",array("board" => $board,"parent" => $parent,"section" => $section,"act" => $act,"no_comments" => $args["no_comments"])),
		));
		return $this->parse();
	}

	function get_num_comments($board)
	{
		$cnt = $this->db_fetch_field("SELECT count(*) AS cnt
		FROM comments WHERE board_id = '$board'","cnt");
		return $cnt;
	}

	/** Submits comment to a topic

		@attrib name=submit_comment params=name nologin="1" default="0"


		@returns


		@comment

	**/
	function submit_comment($args = array())
	{

		$this->quote($args);
		extract($args);
		if (!$name)
		{
			$name = $from;
		};
		$url = trim($url);
		if (strlen($url) > 0 && (strpos($url, "http://") !== 0 && strpos($url, "https://") !== 0) )
		{
			$url = "http://".$url;
		}

		$forum_obj = new object($board);

		$_mx = aw_ini_get("forum.comments_mailto");
		$mx = explode(",", $_mx);

		$tmp = obj($forum_obj->parent());
		$nflist = $tmp->meta("notifylist");

		if ($parent)
		{
			$q = "SELECT * FROM comments WHERE id = '$parent'";
			$this->db_query($q);
			$row = $this->db_next();
			$board = $row["board_id"];
		};

		$image_verification_result = true;
		$image_verification_oid = aw_ini_get('document.image_verification');
		if ( !empty( $image_verification_oid ) )
		{
			$image_verification_inst = get_instance( CL_IMAGE_VERIFICATION );
			$image_verification_result =  $image_verification_inst->validate($args['ver_code']); // returns true or false
		}

		unset($_SESSION['aw_mb_error']);

		if ( (strlen($name) > 0) && (strlen($comment) > 1) && ($image_verification_result === true) )
		{
			if (is_array($mx))
			{
				foreach($mx as $key => $val)
				{
					send_mail($val,
						"Uus sissekanne teemal: " . $forum_obj->name(),
						"Nimi: $name\nE-post: $email\nTeema: $subj\nKommentaar:\n$comment\n\nVastamiseks kliki siia: ".aw_ini_get("baseurl")."/?class=forum&action=show_threaded&board=$board",
						"From: $name <$email>");
				}
			};

			$name = strip_tags($name);
			$email = strip_tags($email);
			$comment = strip_tags($comment);
			$subj = strip_tags($subj);
			$parent = (int)$parent;
			$site_id = $this->cfg["site_id"];
			$ip = aw_global_get("REMOTE_ADDR");
			$t = time();
			if ($remember_me)
			{
				setcookie("aw_mb_name",$name,time()+24*3600*1000);
				setcookie("aw_mb_mail",$email,time()+24*3600*1000);
				setcookie("aw_mb_url",$url,time()+24*3600*1000);
			}
			// yeah, legacy code sucks, but we support it anyway
			if (not($name))
			{
				$name = $from;
			};
			$this->quote(&$name);
			$this->quote(&$email);
			$this->quote(&$comment);
			$this->quote(&$subj);
			$this->quote(&$response);
			if ($response)
			{
				$q = "INSERT INTO comments (parent, board_id, name, email, comment, subj,
						time, site_id, ip, response, lang_id)
					VALUES ('$parent','$board','$name','$email','$comment','$subj',
						$t,'$site_id', '$ip', '$response', '".aw_global_get("ct_lang_id")."')";
			}
			else
			{
				$q = "INSERT INTO comments (parent, board_id, name, email, url, comment, subj,
						time, site_id, ip, lang_id)
				VALUES ('$parent','$board','$name','$email','$url','$comment','$subj',
						$t,'$site_id', '$ip', '".aw_global_get("ct_lang_id")."')";
			};

			$this->db_query($q);

			// need to flush cache here
			$c = get_instance("cache");
			$c->file_clear_pt("html");
		}
		else
		{
			$_SESSION['aw_mb_error'] = array(
				'error' => array(
					'name' => (strlen($name) > 0) ? 0 : 1,
					'comment' => (strlen($comment) > 1) ? 0 : 1,
					'image_verification' => ($image_verification_result === true) ? 0 : 1,
				),
				'values' => $args
			);
		}

		if (not($act))
		{
			$act = "show_threaded";
		};

		$alias = false;
		if ($section)
		{
			$so = new object($section);
			if ($so->class_id() == CL_DOCUMENT || $so->class_id() == CL_PERIODIC_SECTION)
			{
				$alias = true;
			}
		}

		if ($alias && aw_ini_get("user_interface.full_content_trans") && aw_ini_get("user_interface.content_trans"))
		{
			$di = get_instance("doc_display");
			$doc = obj($section);
			$retval = $di->get_doc_link($doc, $_COOKIE["ct_lang_lc"]);
		}
		else if ($alias)
		{
			$retval =$this->mk_my_orb($act,array("board" => $board,"section" => $section,"_alias" => "forum","no_comments" => $args["no_comments"]));
		}
		else
		{
			$retval =$this->mk_my_orb($act,array("board" => $board,"section" => $section,"no_comments" => $args["no_comments"]));
		};

		return $retval;

	}

	/** Exports forum contents as XML
		@attrib name=export_xml
		@param id required tye=int

	**/
	function export_xml($arr)
	{
		$ol = new object_list(array(
			"parent" => $arr["id"],
			"class_id" => CL_MSGBOARD_TOPIC,
			"sort_by" => "objects.created desc",
		));
		$forum_obj = new object($arr["id"]);
		$struct = array();
		$struct["forum"] = array(
			"name" => $forum_obj->name(),
			"topics_on_page" => $forum_obj->prop("topicsonpage"),
			"comments_on_page" => $forum_obj->prop("onpage"),
			"comment" => $forum_obj->comment(),
		);
		for ($o = $ol->begin(); !$ol->end(); $o = $ol->next())
		{
			$topic_id = $o->id();
			$last = $o->last();
			$author = $last ? $last : $o->createdby();
			if (is_object($author))
			{
				$author = $author->name();
			};
			$struct["topics"][$topic_id] = array(
				"subject" => $o->name(),
				"comment" => $o->prop("comment"),
				"time" => $o->created(),
				"author" => $author,
				"email" => $o->meta("author_email"),
			);
			$this->_query_comments(array("board" => $o->id()));
			while($row = $this->db_next())
			{
				$commdata = array(
					"topic_id" => $row["board_id"],
					"ip" => $row["ip"],
					"time" => $row["time"],
					"name" => $row["name"],
					"email" => $row["email"],
					"comment" => $row["comment"],
					"subject" => $row["subj"],
				);
				$struct["comments"][$topic_id][$row["id"]] = $commdata;
			};
			// fer each topic, show teh contents too


		};
		header("Content-type: text/xml");
		print aw_serialize($struct,SERIALIZE_XML);
		die();
		/*
		print_r($ol);
		print "exporting da thing!<br>";
		arr($arr);
		*/
	}

	/** Shows a list of topics for a forum

		@attrib name=topics params=name nologin="1" default="0"

		@param id required type=int
		@param from optional type=int
		@param section optional
		@param archive optional

		@returns


		@comment
		id(int) - forum id

	**/
	function topics($args = array())
	{
		extract($args);
		$o = new object($id);
		$this->init_forum_display(array("id" => $id));
		$this->section = $section;

		$this->forum_id = $id;
		$this->from = $from;
		$this->archive = $archive;
		if ($archive)
		{
			$act_tab = "archive";
		}
		else
		{
			$act_tab = "flat";
		};

		$tabs = $this->tabs(array("flat","details","newtopic","mark_all_read","archive","search"),$act_tab);

		$this->topicsonpage = ($o->prop("topicsonpage") != "") ? $o->prop("topicsonpage") : 5;

		$this->mk_path($o->parent(), "Foorum");
		$this->read_template("list_topics.tpl");

		global $HTTP_COOKIE_VARS;
		$aw_mb_last = unserialize(stripslashes($HTTP_COOKIE_VARS["aw_mb_last"]));
		$this->last_read = $aw_mb_last;
		$this->now = time();

		$this->use_orb_for_links = 1;
		$content = $this->_draw_all_topics(array(
			"id" => $id,
		));

		// 8kk, this is overkill
		// $this->db_query("SELECT COUNT(id) as cnt ,board_id, MAX(time) as mtime FROM comments GROUP BY board_id");
		// pealkirjad, vastuseid, postitas, alustatud, hiliseim vastus

		$this->mk_links(array(
			"id" => $id,
			"board" => $board,
			"from" => $from,
		));

		$this->vars(array(
			"reforb" => $this->mk_reforb("submit_topics",array("id" => $id,"section" => $this->section)),
			"TO_ARCHIVE" => ($archive == 1 ? $this->parse("FROM_ARCHIVE") : $this->parse("TO_ARCHIVE")),
			"FROM_ARCHIVE" => ""
		));


		$this->vars(array(
			"actions" => (acl_base::prog_acl("view",PRG_MENUEDIT) ? $this->parse("actions") : ""),
			"TABS" => $tabs,
			"TOPIC" => $content,
			"TOPIC_EVEN" => $content,
		));
		return $this->parse();
	}

	/** Displays a detailed list of topics

		@attrib name=topics_detail params=name nologin="1" default="0"

		@param id required type=int
		@param from optional type=int
		@param cid optional type=int
		@param section optional

		@returns


		@comment

	**/
	function topics_detail($args = array())
	{
		extract($args);
		$o = new object($id);
		$this->init_forum_display(array("id" => $id));

		$this->forum_id = $id;
		$this->from = $from;
		$this->board = $id;
		$this->section = $section;
		$this->topicsonpage = ($o->prop("topicsonpage") != "") ? $o->prop("topicsonpage") : 5;
		$tabs = $this->tabs(array("flat","details","newtopic","mark_all_read","archive","search"),"details");
		$this->read_template("list_topics_detail.tpl");

		global $HTTP_COOKIE_VARS;
		$aw_mb_last = unserialize(stripslashes($HTTP_COOKIE_VARS["aw_mb_last"]));
		$this->last_read = $aw_mb_last;

		$this->cid = $args["cid"];
		$content = $this->_draw_all_topics(array(
			"id" => $id,
			"details" => 1,
		));

		$this->mk_links(array(
			"id" => $id,
			"board" => $id,
			"from" => $from,
		));

		$this->vars(array(
			"TOPIC" => $content,
			"TOPIC_EVEN" => $content,
			"TABS" => $tabs,
		));

		return $this->parse();
	}

	/**

		@attrib name=submit_topics params=name nologin="1" default="0"


		@returns


		@comment

	**/
	function submit_topics($args = array())
	{
		extract($args);
		if (is_array($check))
		{
			$stat = 1;
			if ($act == "delete")
			{
				$stat = 0;
			}
			if ($act == "activate")
			{
				$stat = 2;
			}
			if ($act == "archive")
			{
				$stat = 1;
			}
			$to_delete = join(",",$check);
			$q = "UPDATE objects SET status = $stat WHERE oid IN ($to_delete)";
			$this->db_query($q);
		};
		return $this->mk_my_orb("topics",array("id" => $id,"section" => $section,"_alias" => "forum","section" => $section));
	}


	/** Shows the search form

		@attrib name=search params=name nologin="1" default="0"

		@param id optional type=int
		@param board optional type=int
		@param section optional

		@returns


		@comment

	**/
	function search($args = array())
	{
		extract($args);
		if (not($id) && not($board))
		{
		  // neither is defined .. what the hell do you want anyway?
			return;
		};
		$this->init_forum_display(array("id" => $id));
		$this->section = $section;
		$this->forum_id = $id;
		$tabs = $this->tabs(array("flat","details","newtopic","mark_all_read","archive","search"),"search");
		$this->read_template("search.tpl");
		$board_obj = new object($board);
		$flink = $this->mk_my_orb("configure",array("id" => $board));
		$this->mk_path($board_obj->parent(), "<a href='$flink'> " . $board_obj->name() . "</a> / Otsi");
		$this->mk_links(array(
			"id" => $id,
			"board" => $board,
		));
		$this->vars(array(
			"reforb" => $this->mk_reforb("submit_search",array("id" => $id, "board" => $board,"no_reforb" => 1,"section" => $this->section)),
			"TABS" => $tabs,
		));
		return $this->parse();
	}

	/** Performs the actual search

		@attrib name=submit_search params=name nologin="1" all_args="1" default="0"


		@returns


		@comment

	**/
	function submit_search($args = array())
	{
		extract($args);
		$this->section = $section;
		$this->forum_id = $id;
		$tabs = $this->tabs(array("flat","details","newtopic","mark_all_read","archive","search"),"search");
		$this->read_template("search_results.tpl");
		if (not($board))
		{
			$board = $id;
		};
		$board_obj = new object($board);
		$forum_obj = new object($board_obj->parent());
		$c = "";

		// koigepealt tuleb koostada topicute nimekiri mingi foorumi all
		$blist[] = 0;
		if ($board_obj->class() == CL_MSGBOARD_TOPIC)
		{
			$blist[] = $board_obj->id();
			$this->mk_links(array(
				"board" => $board,
				"parent" => $board_obj->parent(),
			));
			$this->forum_id = $forum_obj->id();
		}
		else
		{
			$status = ($in_archive) ? "" : " AND status = 2";
			$q = "SELECT * FROM objects WHERE parent = '$board' $status AND class_id = " . CL_MSGBOARD_TOPIC;
			$this->db_query($q);
			$this->forum_id = $board_obj->parent();
			$this->mk_links(array(
				"id" => $board,
			));
			while ($row = $this->db_next())
			{
				$blist[] = $row["oid"];
			};

			// also search topics
			if ($this->is_template("TOPIC_EVEN") && $this->is_template("TOPIC_ODD"))
			{
				$matlist = array();
				$matches = array();
				$q = "SELECT * FROM objects WHERE parent = '$board' $status AND class_id = ".CL_MSGBOARD_TOPIC." AND
							createdby LIKE '%$from%' AND name LIKE '%$email%' AND comment LIKE '%$comment%'";
				$this->db_query($q);
				while ($row = $this->db_next())
				{
					$matlist[] = $row["oid"];
					$matches[] = $row;
				}
				$this->comments = $this->_get_comment_counts($matlist);

				foreach($matches as $row)
				{
					$row["meta"] = aw_unserialize($row["metadata"]);
					$c .= $this->_draw_topic(array_merge($row,array("section" => $this->section)));
				}
			}
		};
		$bjlist = join(",",$blist);
		// valjad: from,email,subj,comment
		// baasis: name,email,subj,comment
		$q = "SELECT * FROM comments WHERE
			name LIKE '%$from%' AND
			email LIKE '%$email%' AND
			subj LIKE '%$subj%' AND
			comment LIKE '%$comment%' AND
			board_id IN ($bjlist)
			ORDER BY time DESC";
		$this->db_query($q);
		$cnt = 0;
		while($row = $this->db_next())
		{
			$cnt++;
			#$this->vars(array(
			#	"from" => $row["name"],
			#	"subj" => $row["subj"],
			#	"email" => $row["email"],
			#	"time" => $this->time2date($row["time"]),
			#	"comment" => $row["comment"],
			#));
			#$c .= $this->parse("message");
			$c .= $this->display_comment($row);
		};
		$this->vars(array(
			"count" => $cnt,
			"message" => $c,
			"TABS" => $tabs,
			"TOPIC_EVEN" => "",
			"TOPIC_ODD" => ""
		));
		return $this->parse();

	}

	/** Marks all the boards in the current forum as read

		@attrib name=mark_all_read params=name nologin="1" default="0"

		@param id required type=int
		@param section optional

		@returns


		@comment

	**/
	function mark_all_read($args = array())
	{
		extract($args);
		global $HTTP_COOKIE_VARS;
		$aw_mb_last = unserialize(stripslashes($HTTP_COOKIE_VARS["aw_mb_last"]));
		$aw_mb_last[$id] = time();
		setcookie("aw_mb_last",serialize($aw_mb_last),time()+24*3600*1000);
		return $this->mk_my_orb("topics",array("id" => $id,"section" => $section,"_alias" => "forum"));
	}

	////
	// !Handles the forum alias inside the document
	function parse_alias($args = array())
	{
		extract($args);
		// we are inside the document, so we switch to embedded mode
		$this->embedded = true;
		$l = $alias;
		$target = $l["target"];
		$tobj = new object($target);
		$parent = $tobj->last();
		$id = $target;
		$section = $oid;

		$vars = $GLOBALS["HTTP_GET_VARS"];
		if (is_array($vars))
		{
			if ($vars["alias"])
			{
				$orb = get_instance("core/orb/orb",array(
					"class" => $vars["alias"],
					"action"=> $vars["action"],
					"vars" => array_merge($vars,array("section" => aw_global_get("section"))),
				));
				$content = $orb->get_data();
				if (substr($content,0,5) == "http:")
				{
					header("Location: $content");
					exit;
				};
			}
			else
			{
				$this->section = $oid;
				// if there are no arguments, then show forum in topicview
				$content = $this->topics(array("id" => $id,"section" => $oid));
			};
		};
		return $content;
	}

	////
	// !Calculates amounts of comments on all given message boards
	function _get_comment_counts($boards = array())
	{
		if (not(is_array($boards)))
		{
			return false;
		};

		// check the length too, otherwise we get a nasty mysql error
		if (sizeof($boards) == 0)
		{
			return false;
		};
		$comments = array();
		$q = sprintf("SELECT board_id,count(*) AS cnt FROM comments
				WHERE board_id IN (%s)
				GROUP BY board_id",join(",",$boards));
		$this->db_query($q);
		while($row = $this->db_next())
		{
			$comments[$row["board_id"]] = $row["cnt"];
		};
		return $comments;
	}

	////
	// !Draws a list of all topics under a forum
	function _draw_all_topics($args = array())
	{
		extract($args);
		// alright, this doesn't need to be changed
		$ol = new object_list(array(
			"parent" => $id,
			"class_id" => CL_MSGBOARD_TOPIC,
			"sort_by" => "objects.created desc",
			"status" => $this->archive ? STAT_NOTACTIVE : STAT_ACTIVE
		));
		$content = "";
		$blist = $ol->ids();

		$this->comments = $this->_get_comment_counts($blist);
		list($from,$to) = $this->_draw_pager(array(
					"total" => $ol->count(),
					"onpage" => $this->topicsonpage,
					"active" => $this->from,
					"details" => $args["details"],
		));
		$cnt = 0;
		for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
		{
			if ( ($cnt >= $from) && ($cnt <= $to) )
			{
				if ($args["details"])
				{
					// this is a tad ineffective, because we query
					// for each topic, instead of getting the comments
					// as a batch
					$this->_query_comments(array("board" => $o->id()));
					// put the comments into tree
					while($row = $this->db_next())
					{
						$this->_comments[$row["parent"]][] = $row;
					};
					$this->rec_comments(0);
					$this->vars(array("message" => $this->content));
					$this->content = "";
					$this->_comments = array();
				}
				$content .= $this->_draw_topic(array_merge($o->fetch(),array("section" => $oid)));
			}
			$cnt++;
		}
		return $content;
	}

	////
	// !Draws a single topic
	// requires a loaded template with either TOPIC_EVEN AND TOPIC_ODD subtemplates
	// or just a TOPIC template
	function _draw_topic($args = array())
	{
		$this->topic_count++;

		$alias = ($this->embedded) ? "forum" : "";

		$this->use_orb_for_links = 1;
		#if ($this->use_orb_for_links)
		#{
			$topic_link = $this->mk_my_orb("show",array("board" => $args["oid"],"section" => $this->section,"_alias" => $alias));
			$threaded_topic_link = $this->mk_my_orb("show_threaded",array("board" => $args["oid"],"section" => $this->section,"_alias" => $alias));
			$threaded_topic_link2 = $this->mk_my_orb("show_threaded",array("board" => $args["oid"],"section" => $this->section,"_alias" => $alias,"no_comments" => 1));
		#}
		#else
		#{
		#	$topic_link = $this->mk_url(array("board" => $args["oid"],"section" => $args["section"]));
		#};

		// to check against=
		$check_against = ($args["modified"] > $args["created"]) ? $args["modified"] : $args["created"];
		$mark = ($check_against > $this->last_read[$args["oid"]]) ? $this->parse("NEW_MSGS") : "";

		if ($this->cfg["track_users"])
		{
			$this->save_handle();
			$uid = aw_global_get("uid");
			$q = "SELECT count(*) AS cnt FROM forum_track WHERE thread_id = '$args[oid]' AND uid = '$uid'";
			$this->db_query($q);
			$row = $this->db_next();

			$read_msgs = $row["cnt"];


			$total_msgs = (int)$this->comments[$args["oid"]];

			if ($read_msgs < $total_msgs)
			{
				$mark = $this->parse("NEW_MSGS");
			}
			else
			{
				$mark = "";
			}
		};

		$this->vars(array(
			"del_topic" => $this->mk_my_orb("delete_topic", array("board" => $args["oid"],"forum_id" => $args["parent"])),
			"change_topic" => $this->mk_my_orb("change_topic", array("board" => $args["oid"],"forum_id" => $args["parent"])),
			"id" => $args["oid"],
		));

		$meta = aw_unserialize($args["metadata"]);
		if ($meta["voters"] == 0)
		{
			$rate = 0;
		}
		else
		{
			$rate = $meta["votesum"] / $meta["voters"];
		};

		$this->vars(array(
			"topic" => ($args["name"]) ? $args["name"] : "(nimetu)",
			"created" => $this->time2date($args["created"],2),
			"created_date" => $this->time2date($args["created"],8),
			"from" => $args["createdby"],
			"email" => $args["meta"]["author_email"],
			"text" => $args["comment"],
			"createdby" => ($args["last"]) ? $args["last"] : $args["createdby"],
			"last" => $this->time2date($args["modified"],11),
			"lastmessage" => $this->time2date($args["modified"],11),
			"comments" => (int)$this->comments[$args["oid"]],
			"cnt" => (int)$this->comments[$args["oid"]],
			"topic_link" => $topic_link,
			"threaded_topic_link" => $threaded_topic_link,
			"threaded_topic_link2" => $threaded_topic_link2,
			"NEW_MSGS" => $mark,
			"rate" => (floor(($rate*10)+0.5)/10),
			"DELETE" => (acl_base::prog_acl("view",PRG_MENUEDIT) ? $this->parse("DELETE") : ""),
			"DEL_TOPIC" => (acl_base::prog_acl("view",PRG_MENUEDIT) ? $this->parse("DELETE") : "")
		));
		$even = ($this->topic_count % 2);
		if ($this->is_template("TOPIC_EVEN"))
		{
			// if TOPIC_EVEN template exitsts then we assume that TOPIC_ODD also exists
			// actually we should check for it
			$tpl_to_parse = ($even) ? "TOPIC_EVEN" : "TOPIC_ODD";
		}
		else
		{
			$tpl_to_parse = "TOPIC";
		};
		$retval = $this->parse($tpl_to_parse);
		$this->vars(array(
			"DELETE" => "",
			"NEW_MSGS" => "",
		));
		return $retval;
	}

	////
	// !Performs a query to get comments matching a certain criteria
	function _query_comments($args = array())
	{
		$limit = '';
		if (!empty($args['limit'])){
			$limit = 'LIMIT '.$args['limit'];
		}
		$order = 'asc';
		if (!empty($args['order']))
		{
			$order = $args['order'];
		}
		if (isset($args["board"]))
		{
			$q = "SELECT * FROM comments WHERE board_id = '$args[board]' ORDER BY time $order ".$limit;
			$this->db_query($q);
		}
	}

	function _draw_comment_pager($args = array())
	{
		extract($args);
		if (!$onpage)
		{
			$onpage = 5;
		};
		$num_pages = (int)(($total / $onpage) + 1);

		// no pager, if we have less entries than will fit on one page
		if ($total < ($onpage - 1))
		{
			return array(0,$total);
		};

		for ($i = 1; $i <= $num_pages; $i++)
		{
			$page_start = ($i - 1)  * $onpage;
			$page_end = $page_start + $onpage - 1;
			if ( ($active >= $page_start) and ($active < $page_end) )
			{
				$act_start = $page_start;
				$act_end = $page_end;
				$tpl = "SEL_PAGE";
			}
			else
			{
				$tpl = "PAGE";
			};
			$pg_action = ($args["details"]) ? "topics_detail" : "topics";
			$this->vars(array(
				"pagelink" => $this->mk_my_orb($pg_action,array("id" => $this->forum_id,"from" => $page_start,"section" => $this->section,  "archive" => $this->archive)),
				"linktext" => $i,
			));
			$content .= $this->parse($tpl);
		};
		$this->vars(array(
			"PAGE" => $content,
		));
		$this->vars(array(
			"PAGES" => $this->parse("PAGES"),
		));

		return(array($act_start,$act_end));
	}

	////
	// !Draws a page
	// requires a loaded template and PAGES, PAGE and SEL_PAGE subtemplates to be defined
	// total(int) - how many items do we have?
	// onpage(int) - how many items on a page?
	// active(int) - what item are we showing at the moment?
	function _draw_pager($args = array())
	{
		extract($args);
		$content = "";
		if (!$onpage)
		{
			$onpage = 5;
		};
		$num_pages = ceil($total / $onpage);
		// no pager, if we have less entries than will fit on one page
		if ($total < ($onpage - 1))
		{
			return array(0,$total);
		};

		for ($i = 1; $i <= $num_pages; $i++)
		{
			$page_start = ($i - 1)  * $onpage;
			$page_end = $page_start + $onpage - 1;
			if ( ($active >= $page_start) and ($active < $page_end) )
			{
				$act_start = $page_start;
				$act_end = $page_end;
				$tpl = "SEL_PAGE";
			}
			else
			{
				$tpl = "PAGE";
			};
			$pglink = "#";
			if ($args["pgaction"] == "show_threaded")
			{
				$pglink = $this->mk_my_orb("show_threaded",array("board" => $this->board,"from" => $page_start,"section" => $this->section));
			}
			else
			if ($args["details"] == "topics_detail")
			{
				$pglink = $this->mk_my_orb("topics_detail",array("id" => $this->forum_id,"from" => $page_start,"section" => $this->section,"archive" => $this->archive));
			}
			else
			if ($args["details"] == "topics")
			{
				$pglink = $this->mk_my_orb("topics",array("id" => $this->forum_id,"from" => $page_start,"section" => $this->section,"archive" => $this->archive));
			}
			else
			{
				$pglink = $this->mk_my_orb("topics",array("id" => $this->forum_id,"from" => $page_start,"section" => $this->section,"archive" => $this->archive,"_alias" => "forum"));

			};
			$this->vars(array(
				"pagelink" => $pglink,
				"linktext" => $i,
			));
			$content .= $this->parse($tpl);
		};
		$this->vars(array(
			"PAGE" => $content,
		));
		$this->vars(array(
			"PAGES" => $this->parse("PAGES"),
		));

		return(array($act_start,$act_end));
	}

	///
	// !deletes a topic from the board. What about the comments though?
	/**

		@attrib name=delete_topic params=name default="0"

		@param forum_id required type=int
		@param board required type=int

		@returns


		@comment

	**/
	function del_topic($arr)
	{
		extract($arr);
		aw_disable_acl();
		$tmp = obj($board);
		$tmp->delete();
		$pobj = obj($forum_id);
		aw_restore_acl();
		if ($pobj->class_id() == CL_DOCUMENT)
		{
			$retval = $this->mk_link(array("section" => $pobj->id()));
		}
		else
		{
			$retval = $this->mk_my_orb("topics", array("id" => $forum_id));
		};
		return $retval;

	}

	/** Allows to change the topic

		@attrib name=change_topic params=name default="0"

		@param board required type=int

		@returns


		@comment

	**/
	function change_topic($arr)
	{
		extract($arr);
		$this->read_template("add_topic.tpl");

		$top = new object($board);

		$this->vars(array(
			"name" => $top->name(),
			"comment" => $top->comment(),
			"from" => $top->last(),
			"email" => $top->prop("author_email"),
			"reforb" => $this->mk_reforb("save_topic", array("board" => $board))
		));
		return $this->parse();
	}


	/** Submits the changed topic

		@attrib name=save_topic params=name default="0"


		@returns


		@comment

	**/
	function save_topic($arr)
	{
		extract($arr);

		aw_disable_acl();
		$o = obj($board);
		$o->set_meta("author_email", $email);
		$o->set_name($topic);
		$o->set_comment($comment);
		$o->save();
		aw_restore_acl();

		$pobj = new object($forum_id);
		if ($pobj->class_id() == CL_DOCUMENT)
		{
			$retval = $this->mk_link(array("section" => $pobj->id()));
		}
		else
		{
			$retval = $this->mk_my_orb("show", array("board" => $board));
		};
		return $retval;
	}

	/** Deletes a message from a board

		@attrib name=del_msg params=name default="0"

		@param comment required type=int
		@param board required type=int
		@param section optional

		@returns


		@comment

	**/
	function del_msg($arr)
	{
		extract($arr);

		$this->db_query("DELETE FROM comments WHERE id = $comment");
		if ($section)
		{
			$retval = $this->mk_my_orb("show",array("_alias" => "forum","board" => $board,"section" => $section));
		}
		else
		{
			$retval = $this->mk_my_orb("show", array("board" => $board));
		};

		return $retval;
	}

	function get_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "template":
				$data["options"] = aw_ini_get("menuedit.template_sets");
				break;

			case "onpage":
				$data["options"] = array(5 => 5,10 => 10,15 => 15,20 => 20,25 => 25,30 => 30);
				break;

			case "topicsonpage":
				$data["options"] = array(5 => 5,10 => 10,15 => 15,20 => 20,25 => 25,30 => 30);
				break;

			case "preview":
				$data["value"] = html::href(array(
					"url" => $this->mk_my_orb("topics",array("id" => $arr["obj_inst"]->id())),
					"caption" => t("N&auml;ita"),
				));

				break;

			case "export":
				$data["value"] = html::href(array(
					"url" => $this->mk_my_orb("export_xml",array("id" => $arr["obj_inst"]->id())),
					"caption" => t("Ekspordi XML"),
				));
				break;

			case "addresslist":
				$data["value"] = html::href(array(
					"caption" => t("E-posti aadressid"),
					"url" => $this->mk_my_orb("notify_list",array("id" => $arr["obj_inst"]->id())),
				));
				break;


		};
		return $retval;
	}

	function callback_get_languages($args = array())
	{
		$xlist = array();
		$xlist[0] = "default";
		$xlist["et"] = "Eesti (et)";
		$xlist["en"] = "Inglise (en)";
		foreach($xlist as $key => $val)
		{
			$retval[] = array(
				"name" => $args["prop"]["name"],
				"type" => "radiobutton",
				"caption" => $val,
				"value" => $args["prop"]["value"],
				"rb_value" => $key,
			);

		};
		return $retval;
	}

	function get_author($board_obj)
	{
		$b_obj = new object($board_obj);
		$class_id = $b_obj->class_id();
		if ($class_id == CL_PERIODIC_SECTION || $class_id == CL_DOCUMENT)
		{
			$docid = $b_obj->brother_of();
			$q = "SELECT author FROM documents WHERE docid = '$docid'";
			$this->db_query($q);
			$row = $this->db_next();
			$author = $row["author"];
		};
		return $author;
	}
}
?>
