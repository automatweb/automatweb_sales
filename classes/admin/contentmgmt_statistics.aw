<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/admin/contentmgmt_statistics.aw,v 1.5 2008/07/03 13:05:47 kristo Exp $
// contentmgmt_statistics.aw - Sisuhalduse statistika 
/*

@classinfo syslog_type=ST_CONTENTMGMT_STATISTICS relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@default table=objects
@default group=general

@property sel_folders type=relpicker reltype=RELTYPE_FOLDER multiple=1 field=meta method=serialize
@caption Vali kaustad

@property num_docs type=textbox size=5  field=meta method=serialize
@caption Mitu dokumenti nimekirjas


	@property mod_table type=table store=no no_caption=1 group=stats_lastmod,stats_unmod

	@property link_checker type=text store=no no_caption=1 group=link_checker

@default group=auto_link_checker

	@property link_check_bg type=checkbox ch_value=1 field=meta method=serialize
	@caption Kontrolli linke taustal

	@property link_check_mailto type=textbox field=meta method=serialize
	@caption Kellele saata kontrolli raport

	@property recur_edit type=releditor reltype=RELTYPE_RECURRENCE use_form=emb rel_id=first
	@caption Automaatse impordi seadistamine

@groupinfo stats caption="Statistika"

	@groupinfo stats_lastmod caption="Viimati muudetud" parent=stats
	@groupinfo stats_unmod caption="Ammu muudetud" parent=stats

@groupinfo link_checker caption="Linkide kontroll"
@groupinfo auto_link_checker caption="Linkide kontrolli automaatika"

@reltype FOLDER value=1 clid=CL_MENU
@caption Kaust

@reltype RECURRENCE value=2 clid=CL_RECURRENCE
@caption kordus
*/

class contentmgmt_statistics extends class_base
{
	const AW_CLID = 1332;

	function contentmgmt_statistics()
	{
		$this->init(array(
			"tpldir" => "admin/contentmgmt_statistics",
			"clid" => CL_CONTENTMGMT_STATISTICS
		));
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function _get_mod_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];

		$t->set_default_sortby("modified");
		if ($arr["request"]["group"] == "stats_unmod")
		{
			$ol = new object_list(array(	
				"class_id" => CL_DOCUMENT,
				"sort_by" => "objects.modified desc",
				"limit" => $arr["obj_inst"]->prop("num_docs"),
				"parent" => $this->_get_parent($arr["obj_inst"]),
				"lang_id" => array(),
				"site_id" => array()
			));
			$t->set_default_sorder("asc");
		}
		else
		{
			$ol = new object_list(array(	
				"class_id" => CL_DOCUMENT,
				"sort_by" => "objects.modified asc",
				"limit" => $arr["obj_inst"]->prop("num_docs"),
				"parent" => $this->_get_parent($arr["obj_inst"]),
				"lang_id" => array(),
				"site_id" => array()
			));
			$t->set_default_sorder("desc");
		}

		$t->table_from_ol($ol, array("name", "parent", "created", "createdby", "modified", "modifiedby"), CL_DOCUMENT);
	}

	private function _get_parent($o)
	{
		$rv = array();
		foreach(safe_array($o->prop("sel_folders")) as $fld)
		{
			$ot = new object_tree(array(
				"parent" => $fld,
				"class_id" => CL_MENU,
				"lang_id" => array(),
				"site_id" => array()
			));
			foreach($ot->ids() as $id)
			{
				$rv[] = $id;
			}
		}
		return $rv;
	}

	function _get_link_checker($arr, $die = true)
	{
		echo t("Kontrollin linke:<br>\n");
		flush();
		$ol = new object_list(array(
			"class_id" => CL_EXTLINK,
			"lang_id" => array(),
			"site_id" => array()
		));
		echo sprintf(t("Leidsin %s linki<br>\n"), $ol->count());
		foreach($ol->arr() as $o)
		{
			echo sprintf(t("Kontrollin linki %s ... "), html::obj_change_url($o));
			flush();
			if ($this->_check_link($o->prop("url")))
			{
				echo t("OK<br>\n");
			}
			else
			{
				echo t("<font color=red>VIGA!!</font><br>\n");
			}
			flush();
		}
		if ($die)
		{
			die(sprintf(t("Valmis. <a href='%s'>Tagasi</a>"), aw_url_change_var("group", "general")));
		}
		else
		{
			echo t("Valmis.");
		}
	}

	private function _check_link($url)
	{
		if (substr($url, 0, 3) != "htt")
		{
			$url = "http://".$url;
		}
		$data = parse_url($url);

		$host = !empty($data["host"]) ? $data["host"] : aw_ini_get("baseurl");
		$host = str_replace("http://", "", $host);
		$host = str_replace("https://", "", $host);

		$port = (!empty($data["port"]) ? $data["port"] : 80);

		$y_url = $data["path"].($data["query"] != "" ? "?".$data["query"] : "").($data["fragment"] != "" ? "#".$data["fragment"] : "");
		if ($y_url == "")
		{
			$y_url = "/";
		}

		$req  = "HEAD $y_url HTTP/1.0\r\n";
		$req .= "Host: ".$host.($port != 80 ? ":".$port : "")."\r\n";
		$req .= "User-agent: AW-http-fetch\r\n";
		$req .= "\r\n\r\n";

		$f = fsockopen($host, 80, $err, $errstr, 5);
		if (!$f)
		{
			return false;
		}

		fwrite($f, $req);
		$data = "";
		while ($s = fread($f, 4096))
		{
			$data .= $s;
		}
		preg_match("/HTTP\/\d\.\d (\d+)/ims", $data, $mt);
		if ($mt[1] == "" || $mt[1] == 404)
		{
			return false;
		}
		fclose($f);
		return true;
	}

	function callback_post_save($arr)
	{
		if (($re = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_RECURRENCE")))
		{
			get_instance("core/scheduler")->add(array(
				"event" => $this->mk_my_orb("invoke", array("id" => $arr["obj_inst"]->id())),
				"time" => $re->instance()->get_next_event(array("id" => $re->id()))
			));
		}
	}

	/**
		@attrib name=invoke nologin="1"
		@param id required type=int acl=view
	**/
	function invoke($arr)
	{
		echo "checking links <br>\n";
		flush();
		ob_start();
		$this->_get_link_checker(array(), false);
		$ct = ob_get_contents();
		ob_end_clean();
		
		$awm = get_instance("protocols/mail/aw_mail");
		$awm->create_message(array(
			"froma" => "info@".aw_ini_get("baseurl"),
			"subject" => t("Lingikontrolli tulemus"),
			"to" => obj($arr["id"])->link_check_mailto,
			"body" => strip_tags($ct),
		));
		$awm->htmlbodyattach(array(
			"data" => $ct,
		));
		$awm->gen_mail();

		if (($re = obj($arr["id"])->get_first_obj_by_reltype("RELTYPE_RECURRENCE")))
		{
			get_instance("core/scheduler")->add(array(
				"event" => $this->mk_my_orb("invoke", array("id" => $arr["id"])),
				"time" => $re->instance()->get_next_event(array("id" => $re->id()))
			));
		}

		die("all done");
	}
}
?>
