<?php

// stat.aw - generating statictis from the syslog
// klass, mille abil saab genereerida statistikat syslog tabelist

class db_stat extends aw_template
{
	// konstruktor
	function db_stat($params)
	{
		$this->init("syslog");
		$month = $params["month"];
		$this->day = $params["day"];
		$this->month = ($month) ? $month : 13;
		$this->uniqid = $params["uniqid"];
		$this->start = mktime(0,0,0,$this->month,1,2000);
		$this->dm = date("t",$this->start);
		$this->end = mktime(23,59,59,$this->month+1,0,2000);
		$this->filter_uid = $GLOBALS["filter_uid"];
		$this->syslog_site_id = (aw_global_get("syslog_site_id")) ? aw_global_get("syslog_site_id") : $this->cfg["site_id"];
		lc_load("definition");
	}

	// kuvab vormi, mille abil erinevaid perioode vorrelda
	function display_compare($arr = array())
	{
		extract($arr);
		if (!$color)
		{
			$color = array("#000000","#FF0000","#7F0000","#00FF00","#007F00","#0000FF","#00007F");
		};
		$this->read_adm_template("compare.tpl");
		$c = "";
		for ($i = 1; $i <= 5; $i++)
		{
			$this->vars(array(
				"cnt" => $i,
				"day"   => $day[$i],
				"color" => $color[$i]
			));
			$c .= $this->parse("line");
		};
		if ($showgraph)
		{
			$g = $this->parse("graph");
		}
		else
		{
			$g = "";
		};
		$this->vars(array(
			"line" => $c,
			"graph" => $g,
			"reforb" => $this->mk_reforb("compare", array("no_reforb" => 1, "showgraph" => 1))
		));
		return $this->parse();
	}

	// kuvab statistika mingi perioodi kohta
	function display_stat()
	{
		#$left = "kuvame statti siia";
		$left = $this->stat_by_hits(50);
		$left1 = $this->stat_by_ip(30);
		$right = $this->stat_by_oid(50);
		if ($this->types["auth"])
		{
			$right .= $this->stat_by_login(10);
		};
		if ($this->types["menuedit"])
		{
			$right .= $this->stat_by_menu(20);
		};
		$this->read_adm_template("stat.tpl");
		$this->vars(array(
			"left" => $left,
			"left1" => $left1,
			"from" => $this->start,
			"parts" => $this->parts,
			"right" => $right,
			"to"   => $this->end,
			"reforb" => $this->mk_reforb("show", array("no_reforb" => 1))
		));
		return $this->parse();
	}

	// seab paika ajavahemiku, mille seest järgneva päringu abil
	// andmeid küsitakse
	function set_timeframe($start,$end)
	{
		// kui midagi ette ei antud, voi ei vastanud see normidele,
		// siis võtame defaultiks tänase päeva
		if (!is_date($start) || !is_date($end) )
		{
			$start = date("d-m-Y");
			$end = date("d-m-Y");
		};

		list($start_day,$start_mon,$start_year) = explode("-",$start);
		list($end_day,$end_mon,$end_year) = explode("-",$end);

		$this->tf["start"]["day"] = $start_day;
		$this->tf["start"]["mon"] = $start_mon;
		$this->tf["start"]["year"] = $start_year;

		$this->tf["end"]["day"] = $end_day;
		$this->tf["end"]["mon"] = $end_mon;
		$this->tf["end"]["year"] = $end_year;

		$this->from = mktime(0,0,0,$start_mon,$start_day,$start_year);
		$this->to   = mktime(23,59,59,$end_mon,$end_day,$end_year);

		$this->start = $start;
		$this->end = $end;

		$this->timeframe = "tm >= $this->from AND tm <= $this->to";

		$days_from = round(($this->from / (60 * 60 * 24)) + 0.5);
		$days_to = round(($this->to / (60 * 60 * 24)) + 0.5);

		// mitu päeva vahemikus on?
		$this->days = $days_to - $days_from;

	}

	// loeb päringust koik andmed sisse ja leiab suurima väärtuse mingis väljas
	// ning selle välja summa
	function fetch_data($fld,$sess_id = "")
	{
		$top = 0;
		$total = 0;
		$this->data = array();
		while($row = $this->db_next())
		{
			$total += $row[$fld];
			$this->data[] = $row;
			if ($row[$fld] > $top)
			{
				$top = $row[$fld];
			};
		};
		// salvestame koik fetchitud andmed sessiooni sisse, kui
		// selleks soovi avaldati
		if ($sess_id)
		{
			$this->saved[$this->uniqid][$sess_id] = $this->data;
		};
		return array($top,$total);
	}

	function prep_query($syslog_types)
	{
		$this->read_adm_template("parts.tpl");
		$this->types = array();
		if (is_array($syslog_types))
		{
			$this->selector = "WHERE syslog.type IN (".join(",",map("'%s'",$syslog_types)).")";
			reset($syslog_types);
			while(list(,$v) = each($syslog_types))
			{
				$this->types[$v] = 1;
				$this->vars(array($v."_sel" => "CHECKED"));
			}
		}
		else
		{
			$this->selector = "";
		};
		$this->parts = $this->parse("selectors");
	}

	// limit - mitut näitame
	function _stat_by_hits($limit)
	{
		$this->read_adm_template("parts.tpl");

		if ($this->days <= 1)
		{
			// 1 päev, näitame väljavõtteid tundide kaupa
   			$cf = "hour(from_unixtime(tm))";
		}
		else
		{
			// rohkem, kui 1 päev, näitame päevade kaupa
			$cf = "date_format(from_unixtime(tm),'%m%d%y')";
		};

		if ($GLOBALS["filter_uid"] != "")
		{
			$fu = " AND uid = '".$GLOBALS["filter_uid"]."' ";
		}

		if (aw_ini_get("syslog.has_site_id") == 1 && $this->syslog_site_id != -1)
		{
			$ss = " AND syslog.site_id = " . $this->syslog_site_id;
		};

		$q = "SELECT count(*) AS hits,$cf AS tm1,tm
			FROM syslog
			WHERE ($this->timeframe AND (type IN ('pageview','cachehit'))) $fu ".$this->mk_bipstr()."
			$ss GROUP BY tm1";
		$this->db_query($q);

		$c = "";
		$cnt = 0;
		list($top,$total) = $this->fetch_data("hits","hits");
		return array($top,$total);
	}
	// vaatamiste arv hittide järgi
	// limit - mitut näitame
	function stat_by_hits($limit)
	{
		if ($this->tf["start"]["day"] == $this->tf["end"]["day"])
		{
			$dir .= sprintf($rootdir . "/support/syslog/%d/%02d/%02d",
				$this->tf["start"]["year"],$this->tf["start"]["mon"],
				$this->tf["start"]["day"]);
			$dir .= "/visits.xml";
		}
		else
		{
			for ($i = $this->tf["start"]["mon"]; $i <= $this->tf["end"]["mon"]; $i++)
			{
				$dir .= sprintf($rootdir . "/support/syslog/%d/%02d",
					$this->tf["start"]["year"],$i);
				$dir .= "/visits.xml\n";
			};
		};
		$dir .= "</pre>";
		#$res = $dir;
		list($top,$total) = $this->_stat_by_hits($limit);
		reset($this->data);
		while(list(,$row) = each($this->data))
		{
			$cnt++;
			$this->vars(array("style" => ($cnt % 2) ? "fgtext2" : "fgtext"));
			if ($this->days <= 1)
			{
				$period = sprintf("%02d:00 - %02d:59",$row["tm1"],$row["tm1"]);
				$hits = $row[hits];
				$width = round((200*$row["hits"])/$top + 1);
			}
			else
			{
				$period = sprintf("<a href='$PHP_SELF?display=stat&from=%s&to=%s'>%s</a>",date("d-m-Y",$row["					tm"]),date("d-m-Y",$row["tm"]),date("d-M-Y",$row["tm"]));
				$hits = $row["hits"];
				$width = round((200*$row["hits"])/$top + 1);
			};
			$this->vars(array(
				"period" => $period,
				"hits"   => $hits,
				"width"  => $width
			));
			$c .= $this->parse("hits_line");
		};

		$this->vars(array(
			"total" => $total,
			"hits_line" => $c,
			"uniqid" => $this->uniqid,
			"lefttitle" => ($days <= 1) ? LC_STAT_LOOKS_BY_HOURS : LC_STAT_LOOKS_BY_DAYS
		));

		$res = $this->parse("hits");
		#$res = $dir;
		return $res;
	}

	// ja siis on veel mingi idee, et voiks need moodulid ära kirjeldada XML-is naiteks?
	// vaatamiste arv IP-de järgi mingis ajavahemikus
	function stat_by_ip($limit)
	{
		$this->read_adm_template("parts.tpl");

		if ($GLOBALS["filter_uid"] != "")
		{
			$fu = " AND uid = '".$GLOBALS["filter_uid"]."' ";
		}

		if (aw_ini_get("syslog.has_site_id") == 1 && $this->syslog_site_id != -1)
    {
	    $ss = " AND syslog.site_id = " . $this->syslog_site_id;
    };

		$q = "SELECT COUNT(*) AS hits,ip
			FROM syslog
			WHERE ($this->timeframe AND (type = 'pageview' OR type = 'cachehit')) $fu ".$this->mk_bipstr()."
			$ss GROUP BY ip
			ORDER BY hits DESC
			LIMIT $limit";
		$this->db_query($q);
		$h = "";
		$cnt = 0;
		list($top,$total) = $this->fetch_data("hits");
		reset($this->data);
		while(list(,$row) = each($this->data))
		{
			$cnt++;
			$this->vars(array(
				"cnt" => $cnt,
				"ip"  => gethostbyaddr($row["ip"]),
				"hits" => $row["hits"],
				"style" => ($cnt % 2) ? "fgtext" : "fgtext2",
				"width" => round( (200*$row["hits"]) / $top) + 1
			));
			$h .= $this->parse("hosts_line");
		};

		// mitmelt erinevalt iplt k2idi
		$q = "SELECT COUNT(distinct(ip)) AS hits
			FROM syslog
			WHERE ($this->timeframe AND (type = 'pageview' OR type = 'cachehit')) $fu ".$this->mk_bipstr();
		$this->db_query($q);
		$res = $this->db_next();

		$this->vars(array(
			"total" => $total,
			"hosts_line" => $h,
			"num_ips" => $res["hits"]
		));
		return $this->parse("hosts");
	}
	function stat_by_login($limit)
	{
		$this->read_adm_template("parts.tpl");
		if ($GLOBALS["filter_uid"] != "")
		{
			$fu = " AND uid = '".$GLOBALS["filter_uid"]."' ";
		}
		$q = "SELECT COUNT(*) AS logins,uid
			FROM syslog
			WHERE ($this->timeframe AND type = 'auth') $fu ".$this->mk_bipstr()."
			GROUP BY uid
			ORDER BY logins DESC
			LIMIT $limit";
		$this->db_query($q);
		$l = "";
		$cnt = 0;
		list($top,$total) = $this->fetch_data("logins");
		reset($this->data);
		while(list(,$row) = each($this->data))
		{
			$cnt++;
			$this->vars(array(
				"cnt" => $cnt,
				"uid" => $row["uid"],
				"logins" => $row["logins"],
				"style" => ($cnt % 2) ? "fgtext" : "fgtext2",
				"width" => round( (200*$row["logins"]) / $top) + 1
			));
			$l .= $this->parse("login_line");
		};
		$this->vars(array(
			"total" => $total,
		  "login_line" => $l,
		  "title" => sprintf(t("Top %s logijat"), $limit)
		));
		return $this->parse("logins");
	}

	function stat_by_oid($limit)
	{
		$this->read_adm_template("parts.tpl");

		if ($GLOBALS["filter_uid"] != "")
		{
			$fu = " AND uid = '".$GLOBALS["filter_uid"]."' ";
		}

		if (aw_ini_get("syslog.has_site_id") == 1 && $this->syslog_site_id != -1)
		{
			$ss = " AND syslog.site_id = " . $this->syslog_site_id;
		};

		$q = "SELECT COUNT(*) AS hits,syslog.oid AS oid,objects.name AS oname
			FROM syslog
			LEFT JOIN objects ON (syslog.oid = objects.oid)
			WHERE ($this->timeframe AND (type = 'pageview' OR type = 'cachehit')) $fu ".$this->mk_bipstr()."
			$ss GROUP BY oid
			ORDER BY hits DESC
			LIMIT $limit";
		$this->db_query($q);
		$t = "";
		$cnt = 0;
		list($top,$total) = $this->fetch_data("hits");
		reset($this->data);
		while(list(,$row) = each($this->data))
		{
			$cnt++;
			$this->vars(array(
				"oid" => $row["oid"],
				"name" => $row["oname"],
				"cnt" => $cnt,
				"style" => ($cnt % 2) ? "fgtext" : "fgtext2",
				"width" => round( (200*$row["hits"]) / $top) + 1,
				"hits" => $row["hits"]
			));
			$t .= $this->parse("objects_line");
		};
		$this->vars(array(
			"total" => $total,
			"objects_line" => $t
		));
		return $this->parse("objects");
	}

	function stat_by_menu($limit)
	{
		$this->read_adm_template("parts.tpl");
		if ($GLOBALS["filter_uid"] != "")
		{
			$fu = " AND uid = '".$GLOBALS["filter_uid"]."' ";
		}
		$q = "SELECT COUNT(*) AS changes,syslog.oid AS oid,objects.name AS mname
			FROM syslog
			LEFT JOIN objects ON (syslog.oid = objects.oid)
			WHERE ($this->timeframe AND syslog.type = 'menuedit') $fu ".$this->mk_bipstr()."
			GROUP BY oid
			ORDER BY changes DESC
			LIMIT $limit";
		$this->db_query($q);
		$m = "";
		$cnt = 0;
		list($top,$total) = $this->fetch_data("changes");
		reset($this->data);
		while(list(,$row) = each($this->data))
		{
			$cnt++;
			$this->vars(array(
				"oid" => $row["oid"],
				"name" => $row["mname"],
				"cnt" => $cnt,
				"style" => ($cnt % 2) ? "fgtext" : "fgtext2",
				"width" => round( (200*$row["hits"]) / $top) + 1,
				"changes" => $row["changes"]
			));
			$m .= $this->parse("menus_line");
		};
		$this->vars(array(
			"total" => $total,
			"menus_line" => $m
		));
		return $this->parse("menus");
	}

	// autentimisi (sisselogimisi)
	function stat_by_auth($limit)
	{
		if ($GLOBALS["filter_uid"] != "")
		{
			$fu = " AND uid = '".$GLOBALS["filter_uid"]."' ";
		}
		$q = "SELECT COUNT(*) AS cnt,uid
			FROM syslog
			WHERE ($this->tf AND type = 'auth') $fu ".$this->mk_bipstr()."
			GROUP BY uid
			ORDER BY cnt DESC
			LIMIT $limit";
		$this->db_query($q);
	}

	// linkidele klikkimised
	function stat_by_links($limit)
	{
		if ($GLOBALS["filter_uid"] != "")
		{
			$fu = " AND uid = '".$GLOBALS["filter_uid"]."' ";
		}
		$q = "SELECT COUNT(*) AS cnt,oid
			FROM syslog
			WHERE ($this->tf AND type = 'link') $fu ".$this->mk_bipstr()."
			GROUP BY oid
			ORDER BY cnt DESC
			LIMIT $limit";
		$this->db_query($q);
	}

	function mk_bipstr()
	{
		// blokitud ipd tshekime ka v2lja
		$blocked_ips = utf_unserialize($this->get_cval("blockedip"));
		$bips = join(",",map("'%s'",$blocked_ips));
		if ($bips != "")
		{
			$bip = " AND ( ip NOT IN ($bips) ) ";
		}
		return $bip;
	}
};

class stat extends db_stat
{
	function stat($params = array())
	{
		$this->db_stat($params);
	}

	/**

		@attrib name=show params=name default="1"

		@param from optional
		@param to optional
		@param month optional
		@param day optional

		@returns


		@comment

	**/
	function show($arr)
	{
		extract($arr);

		global $types, $syslog_types,$gdata;
		session_register("syslog_types");
		session_register("syslog_params");
		if (is_array($types))
		{
			$syslog_types = $types;
		};

		$this->db_stat(array(
			"month" => $month,
			"day"   => $day,
			"uniqid"=> gen_uniq_id()
		));
		$this->set_timeframe($from,$to);
		$this->prep_query($syslog_types);
		$c = $this->display_stat();
		$gdata = array();
		while(list($k,$v) = each($this->saved[$this->uniqid]["hits"]))
		{
			$gdata[$this->uniqid][] = $v["hits"];
		};
		session_register("gdata");
		return $c;
	}

	/**

		@attrib name=graph params=name default="0"

		@param id optional

		@returns


		@comment

	**/
	function graph($arr)
	{
		extract($arr);
		global $gdata;
		$awg = get_instance("syslog/aw_graph");
		$awg->parse_xml_def("graph.xml");
		$awg->import_data($gdata[$id]);
		$awg->draw_graph();
		die();
	}

	/**

		@attrib name=compare params=name default="0"


		@returns


		@comment

	**/
	function compare($arr)
	{
		extract($arr);
		global $day, $color, $showgraph,$gdata,$gd;
		$this->db_stat(array("uniqid" => gen_uniq_id()));
		$c = $this->display_compare(array(
			"cnt" => 5,
			"day" => $day,
			"color" => $color,
			"showgraph" => $showgraph
		));
		$gdata = array();
		$gd = array();
		$i = 0;
		if (is_array($day))
		{
			while(list($k,$v) = each($day))
			{
				if (is_date($v))
				{
					$this->set_timeframe($v,$v);
					$this->_stat_by_hits(69);
					$gdata = $this->saved[$this->uniqid]["hits"];
					$i++;
					while(list(,$v) = each($gdata))
					{
						$gd[$i][] = $v["hits"];
					};
				};
			};
		};
		session_register("gd");
		session_register("color");
		return $c;
	}

	/**

		@attrib name=cgraph params=name default="0"


		@returns


		@comment

	**/
	function cgraph($arr)
	{
		extract($arr);
		global $gd,$color;
		$awg = get_instance("syslog/aw_graph");
		$awg->parse_xml_def("cgraph.xml");
		$awg->import_multi($gd);
		$awg->import_color($color);
		$awg->draw_graph();
		die();
	}
}
