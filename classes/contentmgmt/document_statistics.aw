<?php
// $Header: /home/cvs/automatweb_dev/classes/contentmgmt/document_statistics.aw,v 1.21 2008/01/31 13:52:14 kristo Exp $
// document_statistics.aw - Dokumentide vaatamise statistika 
/*

@classinfo syslog_type=ST_DOCUMENT_STATISTICS relationmgr=yes no_status=1 maintainer=kristo

@default table=objects
@default group=general

@property timespan type=select field=meta method=serialize
@caption Ajavahemik

@property count type=textbox field=meta method=serialize
@caption Mitu esimest

@property stats type=table store=no
@caption TOP

@groupinfo folders caption="Kataloogid ja perioodid"
@default group=folders

@property folders type=table store=no 
@caption Kataloogid

@property periods type=table store=no 
@caption Perioodid

@property period_type type=select field=meta method=serialize
@caption Milliseid perioode kasutada

@groupinfo mail caption="E-mail"

@property mail_to type=textbox field=meta method=serialize group=mail
@caption Meiliaadressid

@property mail_info type=text store=no group=mail
@caption Legend

@property mail_subj type=textbox field=meta method=serialize group=mail
@caption Kirja teema

@property mail_content type=textarea rows=25 cols=70 field=meta method=serialize group=mail
@caption Kirja sisu

@groupinfo styles caption="Stiilid"

@property text_style type=relpicker reltype=RELTYPE_CSS field=meta method=serialize group=styles
@caption Dokumendi pealkirja stiil

@property comment_style type=relpicker reltype=RELTYPE_CSS field=meta method=serialize group=styles
@caption Vaatamiste arvu stiil

@reltype SHOW_FOLDER value=1 clid=CL_MENU
@caption kataloog

@reltype SHOW_PERIOD value=2 clid=CL_PERIOD
@caption periood

@reltype CSS value=3 clid=CL_CSS
@caption css stiil

*/

class document_statistics extends class_base
{
	function document_statistics()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/document_statistics",
			"clid" => CL_DOCUMENT_STATISTICS
		));
	}

	function get_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "mail_info":
				$data["value"] = t("#sisu# asendatakse statistika tabeliga. ");
				break;

			case "timespan":
				$data["options"] = array(
					"day" => t("P&auml;ev"),
					"week" => t("N&auml;dal"),
					"mon" => t("Kuu")
				);
				break;

			case "period_type":
				$data["options"] = array(
					"rel" => t("Seostatud perioodid"),
					"all" => t("K&otilde;ik perioodid"),
					"not" => t("Mitteperioodilised"),
					"act" => t("Aktiivne periood")
				);
				break;

			case "stats":
				if ($arr["new"])
				{
					return PROP_IGNORE;
				}
				$st = $this->get_stat_arr($arr["obj_inst"]);
				
				$data["vcl_inst"]->define_field(array(
					"name" => "docid",
					"caption" => t("Dokument")
				));

				$data["vcl_inst"]->define_field(array(
					"name" => "hits",
					"caption" => t("Vaatamisi"),
					"align" => "center",
					"type" => "int",
					"numeric" => 1,
					"sortable" => 1
				));

				foreach($st as $did => $hc)
				{
					$o = obj($did);
					$a = array(
						"docid" => $o->name(),
						"hits" => $hc
					);
					$data["vcl_inst"]->define_data($a);
				}

				$data["vcl_inst"]->set_default_sortby("hits");
				$data["vcl_inst"]->set_default_sorder("desc");
				$data["vcl_inst"]->sort_by();
				break;

			case "folders":
				$this->do_folders_table($arr);
				break;

			case "periods":
				$this->do_periods_table($arr);
				break;
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "folders":
				$arr["obj_inst"]->set_meta("subs", $arr["request"]["subs"]);
				break;
		}
		return $retval;
	}	

	function _init_folders_table(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Kataloog")
		));

		$t->define_field(array(
			"name" => "subs",
			"caption" => t("K.A. Alamkataloogid"),
			"align" => "center"
		));
	}

	function do_folders_table(&$arr)
	{
		$t =&$arr["prop"]["vcl_inst"];
		$this->_init_folders_table($t);

		$subs = $arr["obj_inst"]->meta("subs");

		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_SHOW_FOLDER")) as $c)
		{
			$o = $c->to();
			$t->define_data(array(
				"name" => $o->path_str(),
				"subs" => html::checkbox(array(
					"name" => "subs[".$o->id()."]",
					"value" => 1,
					"checked" => ($subs[$o->id()] == 1)
				))
			));
		}
	}

	function _init_periods_table(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Periood")
		));
	}

	function do_periods_table(&$arr)
	{
		$t =&$arr["prop"]["vcl_inst"];
		$this->_init_periods_table($t);

		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_SHOW_PERIOD")) as $c)
		{
			$o = $c->to();
			$t->define_data(array(
				"name" => $o->path_str(),
			));
		}
	}

	function parse_alias($arr)
	{
		// don't show immediately, mark as to be shown
		$tmp =  "[document_statistics".$arr["alias"]["target"]."]";
		$ob = obj($arr["alias"]["target"]);
		if ($ob->prop("text_style"))
		{
			active_page_data::add_site_css_style($ob->prop("text_style"));
		}
		if ($ob->prop("comment_style"))
		{
			active_page_data::add_site_css_style($ob->prop("comment_style"));
		}
		return $tmp;
	}

	function show($arr)
	{
		$ob = new object($arr["id"]);
		global $awt;
		$awt->start("stat-arr");
		$st = $this->get_stat_arr($ob, $arr["yesterday"]);
		$awt->stop("stat-arr");
				

		$this->read_template("show.tpl");
		$this->vars(array(
			"doc_style" => "st".$ob->prop("text_style"),
			"hits_style" => "st".$ob->prop("comment_style")
		));

		$l = "";

		foreach($st as $did => $hc)
		{
			if ($did == aw_ini_get("frontpage"))
			{
				continue;
			};
			$o = obj($did);
			$this->vars(array(
				"doc_name" => trim(strip_tags($o->name())),
				"docid" => $did,
				"hits" => $hc
			));

			$l .= $this->parse("LINE");
		}

		$this->vars(array(
			"LINE" => $l
		));
		$tmp =  $this->parse();
		return $tmp;
	}

	/** adds a hit to the document hit list to the document $docid
	**/
	function add_hit($docid)
	{
		$fld = $this->cfg["site_basedir"]."/files/docstats";
		if (!is_dir($fld))
		{
			mkdir($fld, 0777);
			@chmod($fld, 0777);
		}

		// daily stat
		$fname = date("Y-m-d").".txt";
		$this->_upd_stat_file($fld . "/" . $fname,$docid);
	
		// monthly stat
		$fname = date("Y-m").".txt";
		$this->_upd_stat_file($fld . "/" . $fname,$docid);
	}

	function _upd_stat_file($fname,$docid)
	{
		$old = $this->get_file(array(
			"file" => $fname,
		));

		$nf = "";

		if ($old !== false)
		{
			$lines = explode("\n",$old);
			foreach($lines as $line)
			{
				list($did, $hc) = explode(",", $line);
				if ($line == "")
				{
					continue;
				};
				if ($did == $docid)
				{
					$line = $docid.",".($hc+1);
					$found = true;
				}
				$nf .= trim($line)."\n";
			}
		};

		if (!$old || !$found)
		{
			$nf .= $docid . ",1\n";
		};

		// write file
		$this->put_file(array(
			"file" => $fname,
			"content" => $nf,
		));
	}

	/** returns an array of statistics for document views

		@comment

			returns an array of document id => hit count 
			taking into account the display statistics from the object passed as a parameter
	**/
	function get_stat_arr($obj, $yesterday = false)
	{
		$timespan = $obj->prop("timespan");
		$count = $obj->prop("count");

		if ($yesterday)
		{
			$rtm = mktime(23, 59, 59, date("m"), date("d")-1, date("Y"));
		}
		else
		{
			$rtm = time();
		}

		classload("core/date/date_calc");
		global $awt;
		if ($timespan == "week")
		{
			$fc = array();
			$tm = get_week_start();
			while ($tm < $rtm)
			{
				$fp = $this->cfg["site_basedir"]."/files/docstats/".date("Y-m-d", $tm).".txt";
				$tmp = explode("\n", $this->get_file(array("file" => $fp)));
				if (is_array($tmp))
				{
					$fc = array_merge($fc, $tmp);
				}
				$tm += 24*3600;
			}
		}
		else
		if ($timespan == "mon")
		{
			$fc = array();
			$tm = get_month_start();
			$awt->start("docstat-get-file");
			$fp = $this->cfg["site_basedir"]."/files/docstats/".date("Y-m").".txt";
			if (file_exists($fp))
			{
				$tmp = file($fp);
				$fc = array_merge($fc,$tmp);
			};
			/*
			while ($tm < $rtm)
			{
				$awt->count("docstat-get-file");
				$fp = $this->cfg["site_basedir"]."/files/docstats/".date("Y-m-d", $tm).".txt";
				if (file_exists($fp))
				{
					$tmp = file($fp);
					if (is_array($tmp))
					{
						$fc = array_merge($fc, $tmp);
					}
				}
				$tm += 24*3600;
			}
			*/
			$awt->stop("docstat-get-file");
		}
		else
		{
			// day
			$fp = $this->cfg["site_basedir"]."/files/docstats/".date("Y-m-d", $rtm).".txt";
			$fc = explode("\n", $this->get_file(array("file" => $fp)));
		}
		$awt->start("calc2");

		// now, get list of documents 
		$c_dids = $this->_get_document_list($obj);

		$awt->stop("calc2");

		$ds_arr = array();

		foreach($fc as $line)
		{
			if ($line == "")
			{
				continue;
			}
			list($did, $hc) = explode(",", $line);
			if ($c_dids[$did])
			{
				$ds_arr[$did] += $hc;
			}
		}
		
		arsort($ds_arr);
		$ret = array();
		$i = 0;
		foreach($ds_arr as $did => $hc)
		{
			if ($i > $count)
			{
				return $ret;
			}
		
			$ret[$did] = $hc;
			$i++;
		}
		return $ret;
	}

	/** returns a list of document id's that this object can show stats for
	**/
	function _get_document_list($o)
	{
		$menus = array();

		$subs = $o->meta("subs");
		foreach($o->connections_from(array("type" => "RELTYPE_SHOW_FOLDER")) as $c)
		{	
			$menus[$c->prop("to")] = $c->prop("to");

			if ($subs[$c->prop("to")] == 1)
			{
				$tmp = $this->get_menu_list(false, false, $c->prop("to"), -1, false);
				foreach($tmp as $_id => $_pt)
				{
					$menus[$_id] = $_id;
				}
			}
		}

		switch($o->prop("period_type"))
		{
			case "all": /* all periods */
				$pl = new object_list(array(
					"class_id" => CL_PERIOD
				));
				$pc = $pl->ids();
				break;

			case "not": /* not periodic */
				$pc = "0";
				break;

			case "act": /* active period only */
				$pc = aw_global_get("act_per_id");
				break;

			case "rel": /* connected poeriods */
			default:
				$pc = array();
				foreach($o->connections_from(array("type" => "RELTYPE_SHOW_PERIOD")) as $c)
				{
					$pc[] = $c->prop("to");
				}
				break;
		}

		// now get docs
		$ret = array();
		$ol = new object_list(array(
			"class_id" => array(CL_DOCUMENT, CL_DOCUMENT_BROTHER, CL_PERIODIC_SECTION),
			"parent" => $menus,
			"period" => $pc
		));

		return $this->make_keys($ol->ids());
	}

	function callback_post_save($arr)
	{
		// register in scheduler
		$sched = get_instance("scheduler");
		$sched->add(array(
			"event" => $this->mk_my_orb("do_send_mail", array("id" => $arr["obj_inst"]->id())),
			"time" => mktime(8,0,0, date("m"), date("d")+1, date("Y"))
		));
	}

	/** sends e-mail woth statistics. called via scheduler 8:00 AM every day
		
		@attrib name=do_send_mail nologin="1"

		@param id required type=int acl=view

	**/
	function do_send_mail($arr)
	{
		$o = obj($arr["id"]);

		// add back to scheduler
		$sched = get_instance("scheduler");
		$sched->add(array(
			"event" => $this->mk_my_orb("do_send_mail", array("id" => $o->id())),
			"time" => mktime(8,0,0, date("m"), date("d")+1, date("Y"))
		));

		if ($o->prop("mail_to") != "")
		{
			$body = nl2br($o->prop("mail_content"));
			$body = str_replace("#sisu#", $this->show(array("id" => $o->id(), "yesterday" => true)), $body);

			foreach(explode(",", $o->prop("mail_to")) as $mail_to)
			{
				$awm = get_instance("protocols/mail/aw_mail");
				$awm->create_message(array(
					"froma" => "automatweb@am.ee",
					"fromn" => "AutomatWeb",
					"subject" => $o->prop("mail_subj"),
					"to" => $mail_to,
					"body" => strip_tags(str_replace("<br>", "\n", $body))
				));
				$awm->htmlbodyattach(array(
					"data" => $body
				));
				$awm->gen_mail();
			}
		}
	}

	function get_all_doc_stats()
	{
		// gather stats from the last 3 months
		for($i = 0; $i < 3; $i++)
		{
			$fn = aw_ini_get("site_basedir")."/files/docstats/".date("Y-m", mktime(4,4,4, date("m")-$i, date("d"), date("Y"))).".txt";
			$fc = explode("\n", $this->get_file(array("file" => $fn)));
			foreach($fc as $line)
			{
				if ($line == "")
				{
					continue;
				}
				list($did, $hc) = explode(",", $line);
				$ds_arr[$did] += $hc;
			}
		}
		return $ds_arr;
	}
}
?>
