<?php

namespace automatweb;

class aw_session_track extends class_base
{
	function aw_session_track()
	{
		$this->init("core/aw_session_track");
	}

	/** shows list of site => user count

		@attrib name=list default=1

	**/
	function orb_list($arr)
	{
		classload("vcl/table");
		$tb = new aw_table(array(
			"layout" => "generic"
		));
		$this->_init_l_t($tb);

		$data = $this->get_track_data();

		//$this->mk_path(0, t("Saidid"));

		$c_ts = time();

		$s = array();
		$msgs = array();
		$redir = array();
		foreach($data["data"] as $sf => $sd)
		{
			$s[$sd["server"]["site"]]["cnt"] ++;
			if ($sd["aw"]["uid"] != "")
			{
				$s[$sd["server"]["site"]]["cnt_logged"] ++;
				$s[$sd["server"]["site"]]["uid_list"][] = $sd["aw"]["uid"];
			}

			if ($sd["aw"]["timestamp"] > ($c_ts - 60))
			{
				$s[$sd["server"]["site"]]["cnt_60_sec"] ++;
			}

			if ($sd["aw"]["timestamp"] > ($c_ts - 600))
			{
				$s[$sd["server"]["site"]]["cnt_600_sec"] ++;
			}

			if ($sd["aw"]["do_message"] != "")
			{
				$msgs[] = $sd;
			}

			if ($sd["aw"]["do_redir"] != "")
			{
				$redir[] = $sd;
			}
		}

		$tot = array();
		foreach($s as $url => $dat)
		{
			$ul = "";
			if (is_array($dat["uid_list"]))
			{
				$ul = join(",", $dat["uid_list"]);
			}
			$tb->define_data(array(
				"url" => html::href(array(
					"url" => "http://".$url,
					"caption" => $url
				)),
				"cnt" => html::href(array(
					"url" => $this->mk_my_orb("show_site", array("url" => $url)),
					"caption" => $dat["cnt"]
				)),
				"cnt_logged" => html::href(array(
					"url" => $this->mk_my_orb("show_site", array("url" => $url, "logged" => 1)),
					"caption" => $dat["cnt_logged"]
				)),
				"cnt_60_sec" => html::href(array(
					"url" => $this->mk_my_orb("show_site", array("url" => $url, "time" => 60)),
					"caption" => $dat["cnt_60_sec"]
				)),
				"cnt_600_sec" => html::href(array(
					"url" => $this->mk_my_orb("show_site", array("url" => $url, "time" => 600)),
					"caption" => $dat["cnt_600_sec"]
				)),
				"uid_list" => $ul
			));
			$tot["cnt"] += $dat["cnt"];
			$tot["cnt_logged"] += $dat["cnt_logged"];
			$tot["cnt_60_sec"] += $dat["cnt_60_sec"];
			$tot["cnt_600_sec"] += $dat["cnt_600_sec"];
		}

		$ret = sprintf(t("<b>KOKKU:</b><Br>Sessioone: %s<br>Tr&auml;kitud sessioone: %s<br>Logitud: %s<br>60 sek: %s<br>600 sek: %s<br>"), $data["sess_cnt"], $tot["cnt"], $tot["cnt_logged"], $tot["cnt_60_sec"], $tot["cnt_600_sec"]);

		$tb->set_default_sortby("cnt");
		$tb->set_default_sorder("desc");
		$tb->sort_by();
		return $ret.$tb->draw().$this->_do_unsent_tables($msgs, $redir);;
	}

	function _init_l_t(&$t)
	{
		$t->define_field(array(
			"name" => "url",
			"caption" => t("Sait"),
			"sortable" => 1,
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "cnt",
			"caption" => t("Mitu tr2kitud kasutajat"),
			"numeric" => 1,
			"align" => "center"
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "cnt_logged",
			"caption" => t("Mitu sisse loginud tr2kitud kasutajat"),
			"numeric" => 1,
			"align" => "center"
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "cnt_60_sec",
			"caption" => t("Viimati minuti jooksul"),
			"numeric" => 1,
			"align" => "center"
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "cnt_600_sec",
			"caption" => t("Viimati 10 minuti jooksul"),
			"numeric" => 1,
			"align" => "center"
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "uid_list",
			"caption" => t("Sisse loginud kasutajad")
		));
	}

	/**

		@attrib name=show_site

		@param url required
		@param logged optional type=int
		@param time optional type=int
	**/
	function show_site($arr)
	{
		classload("vcl/table");
		$t = new aw_table(array(
			"layout" => "generic"
		));
		$this->_init_show_site_t($t);

		/*$this->mk_path(0, html::href(array(
			"url" => $this->mk_my_orb("list"),
			"caption" => t("Saidid")
		))." / $arr[url] ");*/


		$data = $this->get_track_data(array(
			"server" => $arr["url"]
		));

		$msgs = array();
		$redir = array();
		foreach($data["data"] as $sess_file => $d)
		{
			if ($arr["logged"] == 1 && $d["aw"]["uid"] == "")
			{
				continue;
			}

			if (!empty($arr["time"]) && $d["aw"]["timestamp"] < (time() - $arr["time"]))
			{
				continue;
			}

			if ($d["aw"]["do_message"] != "")
			{
				$msgs[] = $d;
			}

			if ($d["aw"]["do_redir"] != "")
			{
				$redir[] = $d;
			}

			$fu = "http://".$d["server"]["site"].$d["server"]["ru"];
			$lo = "";
			if ($d["aw"]["uid"] != "")
			{
				$lo = html::href(array(
					"url" => $this->mk_my_orb("del_sess", array("sess" => $sess_file, "return_url" => get_ru())),
					"caption" => t("Logi v&auml;lja")
				));
			}

			list($reso) = inet::gethostbyaddr($d["server"]["ip"]);
			$t->define_data(array(
				"ip" => $reso,
				"referer" => html::href(array(
					"url" => $d["server"]["referer"],
					"caption" => $d["server"]["referer"]
				)),
				"url" => html::href(array(
					"url" => $fu,
					"caption" => $fu
				)),
				"uid" => $d["aw"]["uid"],
				"timestamp" => $d["aw"]["timestamp"],
				"msg" => html::href(array(
					"url" => "javascript:void(0)",
					"caption" => t("Saada teade"),
					"onClick" => "u = prompt(\"Teade\");window.location=\"".$this->mk_my_orb("do_msg", array(
							"sess" => $sess_file,
							"return_url" => get_ru()
					))."&msg=\"+u;"
				)),
				"redir" => html::href(array(
					"url" => "javascript:void(0)",
					"caption" => t("Suuna"),
					"onClick" => "u = prompt(\"".t("Aadress, kuhu suunata")."\");window.location=\"".$this->mk_my_orb("do_redir", array(
							"sess" => $sess_file,
							"return_url" => get_ru()
					))."&redir_to=\"+u;"
				)),
				"logout" => $lo
			));
		}

		$t->set_default_sortby("timestamp");
		$t->set_default_sorder("desc");
		$t->sort_by();

		return $t->draw().$this->_do_unsent_tables($msgs, $redir);
	}

	function _do_unsent_tables($msgs, $redirs)
	{
		$t2 = new aw_table(array(
			"layout" => "generic"
		));
		$t2->define_field(array(
			"name" => "ip",
			"caption" => t("Kasutaja ip"),
		));
		$t2->define_field(array(
			"name" => "who",
			"caption" => t("Kellele"),
		));
		$t2->define_field(array(
			"name" => "msg",
			"caption" => t("Teade"),
		));

		foreach($msgs as $d)
		{
			list($reso) = inet::gethostbyaddr($d["server"]["ip"]);
			$t2->define_data(array(
				"ip" => $reso,
				"who" => $d["server"]["site"]." / ".$d["aw"]["uid"],
				"msg" => $d["aw"]["do_message"]
			));
		}

		$t3 = new aw_table(array(
			"layout" => "generic"
		));
		$t3->define_field(array(
			"name" => "ip",
			"caption" => t("Kasutaja ip"),
		));
		$t3->define_field(array(
			"name" => "who",
			"caption" => t("Kellele"),
		));
		$t3->define_field(array(
			"name" => "url",
			"caption" => t("Kuhu suunata"),
		));

		foreach($redirs as $d)
		{
			list($reso) = inet::gethostbyaddr($d["server"]["ip"]);
			$t3->define_data(array(
				"ip" => $reso,
				"who" => $d["server"]["site"]." / ".$d["aw"]["uid"],
				"url" => $d["aw"]["do_redir"]
			));
		}

		return sprintf(t("<br>Saatmata teated: %s <br>Tegemata suunamised: %s"), $t2->draw(), $t3->draw());
	}

	function _init_show_site_t(&$t)
	{
		$t->define_field(array(
			"name" => "ip",
			"caption" => t("Kasutaja IP aadress"),
			"nowrap" => 1,
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "uid",
			"caption" => t("Kasutaja"),
			"nowrap" => 1,
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "timestamp",
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y H:i:s",
			"caption" => t("Millal viimati"),
			"nowrap" => 1,
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "msg",
			"caption" => t("Saada teade"),
			"nowrap" => 1
		));

		$t->define_field(array(
			"name" => "redir",
			"caption" => t("Suuna"),
			"nowrap" => 1
		));

		$t->define_field(array(
			"name" => "logout",
			"caption" => t("Logi v&auml;lja"),
			"nowrap" => 1
		));

		$t->define_field(array(
			"name" => "url",
			"caption" => t("Url, mida vaadatakse"),
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "referer",
			"caption" => t("Referer"),
			"sortable" => 1
		));
	}

	function get_track_data($arr = array())
	{
		$dh = opendir(aw_ini_get("server.tmpdir"));
		$cnt = 0;
		$cnt_suc = 0;

		$restrict_url = aw_ini_get("aw_session_track.restrict_url");

		$track_data = array();

		while (($file = readdir($dh)) !== false)
		{
			if (substr($file, 0, 4) == "sess")
			{
				$fc = @file_get_contents("/tmp/".$file);
				$ofc = $fc;
				if ($fc !== false)
				{
					if (strpos($fc, "aw_session_track") !== false)
					{
						$cnt_suc++;

						$tcnt = 0;
						while (strlen(trim($fc)) > 10)
						{
							// slurp varname - until |
							list($vn, $fc) = explode("|", $fc, 2);
							$vv = unserialize($fc);

							if ($vn == "aw_session_track")
							{
								if (!empty($arr["server"]) && $vv["server"]["site"] != $arr["server"])
								{
									continue;
								}
								if ($restrict_url != "" && $vv["server"]["site"] != $restrict_url)
								{
									continue;
								}
								$track_data[$file] = $vv;
							}

							$tmp = serialize($vv);
							$fc = substr($fc, strlen($tmp));
							$tcnt++;
							if ($tcnt > 1000)
							{
								die("tcnT! <br>orig str: ".$ofc." <br><br>cur: ".dbg::dump($fc)." <br>tmp = ".dbg::dump($tmp));
							}
						}
					}
				}
			}
			$cnt++;
		}
		closedir($dh);

		return array(
			"data" => $track_data,
			"sess_cnt" => $cnt,
			"track_cnt" => $cnt_suc
		);
	}

	/** deltes a session file - meaning, logs the user with that session out!

		@attrib name=del_sess

		@param sess required
		@param return_url required

	**/
	function del_sess($arr)
	{
		$fp = aw_ini_get("server.tmpdir")."/".$arr["sess"];
		unlink($fp);
		return aw_ini_get("baseurl").$arr["return_url"];
	}

	/** sets the redir_to in the session file so taht the next time the user views a page, he/she will get redirected to the requested page

		@attrib name=do_redir

		@param sess required
		@param return_url required
		@param redir_to required

	**/
	function do_redir($arr)
	{
		$fp = aw_ini_get("server.tmpdir")."/".$arr["sess"];
		// this will be prone to threading problems, bu fuck that!
		$fc = $this->_unser_sess_str($this->get_file(array("file" => $fp)));
		$fc["aw_session_track"]["aw"]["do_redir"] = $arr["redir_to"];
		$this->put_file(array(
			"file" => $fp,
			"content" => $this->_ser_sess_str($fc)
		));

		return aw_ini_get("baseurl").$arr["return_url"];
	}

	/** sets the do_message in the session file so taht the next time the user views a page, he/she will get a js popup with the message :)

		@attrib name=do_msg

		@param sess required
		@param return_url required
		@param msg required

	**/
	function do_msg($arr)
	{
		$fp = aw_ini_get("server.tmpdir")."/".$arr["sess"];
		// this will be prone to threading problems, bu fuck that!
		$fc = $this->_unser_sess_str($this->get_file(array("file" => $fp)));
		$fc["aw_session_track"]["aw"]["do_message"] = $arr["msg"];
		$this->put_file(array(
			"file" => $fp,
			"content" => $this->_ser_sess_str($fc)
		));

		return aw_ini_get("baseurl").$arr["return_url"];
	}

	function _unser_sess_str($fc)
	{
		$ret = array();
		$tcnt = 0;
		while (strlen(trim($fc)) > 10)
		{
			// slurp varname - until |
			list($vn, $fc) = explode("|", $fc, 2);
			$vv = unserialize($fc);

			$ret[$vn] = $vv;

			$tmp = serialize($vv);
			$fc = substr($fc, strlen($tmp));
			$tcnt++;
			if ($tcnt > 1000)
			{
				die("tcnT! <br>orig str: ".$ofc." <br><br>cur: ".dbg::dump($fc)." <br>tmp = ".dbg::dump($tmp));
			}
		}

		return $ret;
	}

	function _ser_sess_str($arr)
	{
		$ret = array();
		foreach($arr as $vn => $vv)
		{
			$ret[] = $vn."|".serialize($vv);
		}
		return join("", $ret);
	}
}
?>
