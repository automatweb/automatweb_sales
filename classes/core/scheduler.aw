<?php

/**
		The scheduler can be used to schedule events to happen at certain times.
		An event is an orb function call that will get called at the time specified.
		You can specify a certain time or an aw repeater when the event will take place.

		The fields in the event array are:
		time - the time that the event is scheduled for - unix timestamp
		event - the full url of the page that will be requested
		uid - the username as who the event will be executed
		password - the password for the account in uid
		rep_id - the id of the repeater that generated this event

		The fields in the log entry array are:
		time - the time that the event was actually executed
		event - the event array for the event that took place
		response - the text that was returned from the HTTP request including headers
**/
class scheduler extends aw_template
{
	function scheduler()
	{
		$this->init("scheduler");
		$this->file = get_instance(CL_FILE);
	}

	/** adds the specified event to the scheduler
		@attrib api=1 params=name

		@param event required type=string
			the url that will get called at the right time

		@param time optional type=int
			the time when the event will be executed - if this is omitted, then rep_id must be specified

		@param rep_id optional type=oid
			the id of the aw repeater for controlling when the event will take place

		@param uid optional type=string
			if specified, the event will get excecuted as that user

		@param sessid optional type=string
			 if specified, the event will get called with the session id given - can be used to restart events with the same uid as the current user

		@param password optional type=string
			the password of the user uid

		@param auth_as_local_user type=bool
			if set to true, the current logged on user is given a temporary password and that is written to the scheduler xml file, so that the scheduled action will be run as the current user
	**/
	function add($arr)
	{
		extract($arr);
		if (!$time && !$rep_id)
		{
			$this->raise_error(ERR_SCHED_NOTIMEREP, "No time or repeater id specified in adding an event", true);
		}

		if (!$event)
		{
			// no url? fuck off.
			return false;
		};

		if ($uid == "")
		{
			$event = str_replace("automatweb/", "", $event);
		}

		if ($time)
		{
			$event_id = md5($event);
			$this->evnt_add($time, $event, $uid, $password, 0, $event_id, $sessid, $arr["auth_as_local_user"]);
		}

		if ($rep_id)
		{
			$now = time();
			// XXX: convert to storage as soon as possible
			$ltime = 0;
			$q = "SELECT * FROM recurrence WHERE recur_id = '${rep_id}' AND recur_start >= '${now}' ORDER BY recur_start LIMIT 20";
			$this->db_query($q);
			while($row = $this->db_next())
			{
				$this->evnt_add($row["recur_start"],$event."&ts=".$row["recur_start"],$uid,$password,$rep_id,$event_id);
				$ltime = $row["recur_start"];
				break;
			};
			// and the clever bit here - schedule an event right after the last repeater to reschedule
			// events for that repeater
			if ($ltime)
			{
				$this->evnt_add(
					$ltime+3000,
					str_replace("automatweb/", "", $this->mk_my_orb("update_repeaters", array("id" => $rep_id))),
					$uid,
					$password
				);
			}
		}
	}

	/** updates the scheduled events that use repeater $id

		@attrib name=update_repeaters params=name default="0" nologin="1"

		@param id required

		@returns


		@comment

	**/
	function update_repeaters($arr)
	{
		extract($arr);
		$this->open_session();

		$newdat = array();

		// delete the events for that repeater
		foreach($this->repdata as $evnt)
		{
			if ($evnt["rep_id"] != $id)
			{
				$newdat[] = $evnt;
			}
		}
		$this->repdata = $newdat;

		$o = new object($id);
		$now = time();
		$clid = $o->class_id();

		$cs = $o->connections_to(array("from.class_id" => CL_SCHEDULER));
		$c = reset($cs);
		// recur_id refers to a single CL_RECURRENCE object, conventional connections method is not used
		// because it would add too much overhead, too much redundant data. There can literally be
		// thousands of records in the recurrence table for a single recurrence object and every little
		// bit of speed helps in those cases
		//print "recur_id = $id<br>";
		$q = "SELECT * FROM recurrence WHERE recur_id = '$id' AND recur_start >= '${now}' ORDER BY recur_start LIMIT 20";
		$this->db_query($q);
		while($row = $this->db_next())
		{
			// get events for scheduler obj
			// some classes link to CL_RECURRENCE directly, those will need a separate code path
			if ($c)
			{
				$o = obj($c->prop("from"));
				// read properties into tmp array, this way accessing login_uid for object
				// without such property should not cause a fatal error
				//foreach($o->connections_from(array("type" => 1)) as $c)
				// see 1 annab target objekti .. aga k6ik need probleemid ju tulenevad
				// sellest, et sihtobjektil v6ib olla olla seos hoopid scheduleriga
				foreach($o->connections_from(array("type" => "RELTYPE_TARGET_OBJ")) as $c)
				{
					$event = str_replace("automatweb/", "", $this->mk_my_orb("invoke",array("id" => $c->prop("to")),$c->prop('to.class_id')));
					$re = $event."&ts=".$row["recur_start"];
					$this->evnt_add(
						$row["recur_start"],
						$re,
						$o->prop("login_uid"),
						$o->prop("login_password"),
						$id,
						md5($re)
					);
					$ltime = $row["recur_start"];
					//echo "added event $re at $row[recur_start] <br>";
				}
			}
			else
			{
				foreach($o->connections_to(array("to.class_id" => CL_RECURRENCE)) as $c)
				{
					// see asi siin peab invoke uuesti scheduleri sisse pysti panema?
					$event = str_replace("automatweb/", "", $this->mk_my_orb("invoke",array("id" => $c->prop("from")),$c->prop("from.class_id")));
						$re = $event."&ts=".$row["recur_start"];
						$this->evnt_add(
							$row["recur_start"],
							$re,
							$uid,
							$password,
							$id,
							md5($re)
						);
						$ltime = $row["recur_start"];
						//echo "added event $re at $row[recur_start] <br>";
				};


			};
			break;
		};
		// and the clever bit here - schedule an event right after the last repeater to reschedule
		// events for that repeater
		if ($ltime)
		{
			$this->evnt_add(
				$ltime+3000,
				str_replace("automatweb/", "", $this->mk_my_orb("update_repeaters", array("id" => $id))),
				$uid,
				$password
			);
		}
		$this->close_session(true);
	}

	function evnt_add($time, $event, $uid = "", $password = "", $rep_id = 0, $event_id = "", $sessid ="", $auth_as_local_user = false)
	{
		$this->open_session();
		$found = false;
		if (substr($event,0,4) != "http")
		{
			$event = aw_ini_get("baseurl") . $event;
		};
		// try and remove all existing scheduling information for this event
		if (is_array($this->repdata))
		{
			// modifying an array while looping over it can lead to unexpected results
			$tmp = $this->repdata;
			foreach($tmp as $key => $evnt)
			{
				if ($evnt["event"] == $event && $evnt["uid"] == $uid)
				{
					unset($this->repdata[$key]);
				}
			}
		}

		if (empty($event_id))
		{
			// that should be enough to make sure that 2 requests to one url
			// do not overlap
			$event_id = md5($event);
		};

		// (re)add the event to the queue
		$repdata = array(
			"time" => $time,
			"event" => $event,
			"event_id" => $event_id,
			"uid" => $uid,
			"password" => $password,
			"rep_id" => $rep_id,
			"sessid" => $sessid
		);

		if ($auth_as_local_user)
		{
			$uid = aw_global_get("uid");
			$hash = gen_uniq_id();
			$hash_time = $time + 24 * 3600 * 2;

			// see if we have the user hashes table
			$this->_check_hash_tbl();

			$this->db_query("INSERT INTO user_hashes (uid,hash, hash_time) values('$uid','$hash','$hash_time')");
			$repdata["uid"] = $uid;
			$repdata["auth_hash"] = $hash;
		}
		$this->repdata[] = $repdata;

		$this->close_session(true);
	}

	function open_session()
	{
		$this->session_fp = fopen($this->cfg["sched_file"], "a+");

		//kui juhtub ime ja samalajal keegi tegutseb selle failiga, siis on kahetsusv22rne kui lihtsalt die tuleb... asjad v6ivad katki minna nii... see on paha... v2ga paha
		$reading_start = time();
		while(!$this->session_fp && ($reading_start + 15 > time()))
		{
			sleep(1);
			$this->session_fp = fopen($this->cfg["sched_file"], "a+");
		}

		if (!$this->session_fp)
		{
			printf("cannot open %s for writing, please check permission",$this->cfg["sched_file"]);
			die();
		};
		flock($this->session_fp,LOCK_EX);

		fseek($this->session_fp,0,SEEK_SET);
		clearstatcache();
		$fc = fread($this->session_fp, filesize($this->cfg["sched_file"]));
		$this->repdata = aw_unserialize($fc);
		if (!is_array($this->repdata))
		{
			$this->repdata = array();
		}

		// also remove events that only have time set, but no url
		$nrd = array();
		foreach($this->repdata as $idx => $evnt)
		{
			if ($evnt["event"] != "")
			{
				$nrd[] = $evnt;
			}
		}
		$this->repdata = $nrd;
	}

	function close_session($write = false)
	{
		if ($write)
		{
			ftruncate($this->session_fp,0);
			fseek($this->session_fp,0,SEEK_SET);
			fwrite($this->session_fp, aw_serialize($this->repdata,SERIALIZE_XML));
			fflush($this->session_fp);
		}

		flock($this->session_fp,LOCK_UN);
		fclose($this->session_fp);
		$this->session_fp = false;
	}

	/** this is where the event processing will take place

		@attrib name=do_events params=name nologin="1" default="1"


		@returns


		@comment

	**/
	function do_events($arr)
	{
		extract($arr);
		aw_set_exec_time(AW_LONG_PROCESS);

		// read in all events
		$this->open_session();
		$this->close_session(true);

		// now do all events for which the time has expired
		$cp = $this->repdata;

		$now = time();

		foreach($cp as $evnt)
		{
//w:		echo "now = $now , time  = $evnt[time] , event = $evnt[url] \n";
			if (isset($evnt["time"]) && ($now > $evnt["time"]))
			{
				echo "exec event $evnt[event] <br />";
				$this->do_and_log_event($evnt);
			}

		}
	}

	function do_and_log_event($evnt)
	{
		ob_start();
		$file = $this->cfg["error_file"];
		touch($file);
		$fl = fopen($file, "wb");
		if ($evnt["event"] == "")
		{
			echo "no event specified, exiting<br />\n";
			fwrite($fl, ob_get_contents());
			fclose($fl);
			ob_end_flush();
			return;
		}

		// ok, here check if this event is already being processed
		$lockfilename = $this->cfg["lock_file"] . "." . $evnt["event_id"];
		if (file_exists($lockfilename) && (filemtime($lockfilename) > (time()-300)))
		//if (file_exists($lockfilename))
		{
			$pid = $this->get_file(array(
				"file" => $lockfilename,
			));
			if ($pid == getmypid())
			{
				// they are so just bail out
				echo "bailing for lock file ",$lockfilename,"<br />\n";
				fwrite($fl, ob_get_contents());
				fclose($fl);
				ob_end_flush();
				return;
			}
			else
			{
				echo "shouldn't but bailing for lock file ",$lockfilename,"<br />\n";
				fwrite($fl, ob_get_contents());
				fclose($fl);
				ob_end_flush();
				return;
			};
		}

		$this->put_file(array(
			"file" => $lockfilename,
			"content" => getmypid(),
		));

		touch($lockfilename);

		$evnt["event"];
		$ev_url = str_replace("/automatweb","",$evnt["event"]);

		preg_match("/^http:\/\/(.*)\//U",$ev_url, $mt);
		$url = $mt[1];

		echo "url = $url <br />";
		$awt = get_instance("protocols/file/http");
		$awt->handshake(array(
			"host" => $url,
			"sessid" => $evnt["sessid"]
		));

		if ($evnt["uid"] && $evnt["password"])
		{
			// we must log in
			$awt->login(array(
				"host" => $url,
				"uid" => $evnt["uid"],
				"password" => $evnt["password"]
			));
		}
		else
		if ($evnt["uid"] && $evnt["auth_hash"])
		{
			$awt->login_hash(array(
				"host" => $url,
				"uid" => $evnt["uid"],
				"hash" => $evnt["auth_hash"],
			));
		}

		echo "do send req $url ",substr($ev_url,strlen("http://")+strlen($url))," <br />";
		$req = $awt->do_send_request(array("host" => $url, "req" => substr($ev_url,strlen("http://")+strlen($url))));
		print $req;
		echo "unlinking ",$lockfilename," <br />";
		unlink($lockfilename);

		if ($evnt["uid"] && $evnt["password"])
		{
			// be nice and logout
			$awt->logout(array("host" => $url));
		}

		// consider the event done, so log it and remove
		$this->log_event($evnt, $req);

		$this->remove($evnt);

		// update repeater
		if ($evnt["rep_id"])
		{
			$this->update_repeaters(array("id" => $evnt["rep_id"]));
		}
		fwrite($fl, ob_get_contents());
		fclose($fl);
		ob_end_flush();
	}

	/** removes events from the queue that match the mask - the mask can contain any fields in the event description and only the ones that are specified are used in matching
		@attrib api=1
	**/
	function remove($evnt)
	{
		$this->open_session();
		$newdat = array();
		foreach($this->repdata as $e)
		{
			if (!$this->match($evnt, $e))
			{
				$newdat[] = $e;
			}
		}

		$this->repdata = $newdat;
		$this->close_session(true);
	}

	/** searches the current schedule for events matching the mask and returns their info

		@attrib api=1

	**/
	function find($evnt)
	{
		$this->open_session();
		$newdat = array();
		foreach($this->repdata as $e)
		{
			if ($this->match($evnt, $e))
			{
				$newdat[] = $e;
			}
		}

		$this->close_session();
		return $newdat;
	}

	function match($mask, $event)
	{
		$match = true;
		foreach($mask as $k => $v)
		{
			if ($event[$k] != $v)
			{
				$match = false;
			}
		}
		return $match;
	}

	function open_log_session()
	{
		$this->log = array();
		return;

		$this->log_fp = @fopen($this->cfg["log_file"], "a+");
		if (!$this->log_fp)
		{
			return false;
		};
		flock($this->log_fp,LOCK_EX);

		fseek($this->log_fp,0,SEEK_SET);
		clearstatcache();
		$fc = fread($this->log_fp, filesize($this->cfg["log_file"]));
		$this->log = aw_unserialize($fc);
		if (!is_array($this->log))
		{
			$this->log = array();
		}
	}

	function close_log_session($write = false)
	{
		$this->log = array();
		return;
		if ($this->log_fp && $write)
		{
			ftruncate($this->log_fp,0);
			fseek($this->log_fp,0,SEEK_SET);

			fwrite($this->log_fp, aw_serialize($this->log,SERIALIZE_XML));
			fflush($this->log_fp);
		}

		if ($this->log_fp)
		{
			flock($this->log_fp,LOCK_UN);
			fclose($this->log_fp);
		};
	}

	function log_event($event, $pg)
	{
		$this->open_log_session();
		$this->log[] = array("time" => time(), "event" => $event, "response" => $pg);
		$this->close_log_session(true);
	}

	/** returns the log entries for the events that match mask
		@attrib api=1
	**/
	function get_log_for_events($mask)
	{
		// read log
		$this->open_log_session();
		$this->close_log_session();

		$ret = array();
		foreach($this->log as $ldat)
		{
			if ($this->match($mask,$ldat["event"]))
			{
				$ret[] = $ldat;
			}
		}

		return $ret;
	}

	/** removes entries from the event log that are for events that match the mask
		@attrib api=1
	**/
	function remove_log_events($mask)
	{
		$this->open_log_session();
		$ret = array();
		foreach($this->log as $ldat)
		{
			if (!$this->match($mask,$ldat["event"]))
			{
				$ret[] = $ldat;
			}
		}
		$this->log = $ret;
		$this->close_log_session(true);
	}

	/** ui for scheduler

		@attrib name=show params=name default="0"

		@param sortby optional
		@param sort_order optional

		@returns


		@comment

	**/
	function show($arr)
	{
		extract($arr);
		load_vcl("table");
		$t = new aw_table(array("prefix" => "schedshow"));
		$t->parse_xml_def($this->cfg["basedir"]."/xml/scheduler/show.xml");
		$this->read_template("list.tpl");
		$this->open_session();
		$this->close_session();

		print "<pre>";
		print_r($this->repdata);
		print "</pre>";

		foreach($this->repdata as $evnt)
		{
			$t->define_data(array(
				"time" => $evnt["time"],
				"event" => $evnt["event"],
			));
		}

		$t->sort_by(array(
                        "field" => ($sortby) ? $sortby : "time",
                        "sorder" => ($sort_order) ? $sort_order : "asc",
                ));

		$this->vars(array(
			"table" => $t->draw(),
			"log_url" => $this->mk_my_orb("show_log")
		));
		return $this->parse();
	}

	/** shows log entries

		@attrib name=show_log params=name default="0"


		@returns


		@comment

	**/
	function show_log($arr)
	{
		extract($arr);
		$this->read_template("log.tpl");
		$this->open_log_session();
		$this->close_log_session();

		foreach($this->log as $lid => $lit)
		{
			$this->vars(array(
				"time" => $this->time2date($lit["time"], 2),
				"event" => $lit["event"]["event"],
				"view" => $this->mk_my_orb("show_log_entry", array("id" => $lid))
			));
			$l.=$this->parse("LINE");
		}
		$this->vars(array(
			"LINE" => $l,
			"sched_url" => $this->mk_my_orb("show")
		));
		return $this->parse();
	}

	/**

		@attrib name=show_log_entry params=name default="0"

		@param id required

		@returns


		@comment

	**/
	function show_log_entry($arr)
	{
		extract($arr);
		$this->read_template("log_entry.tpl");
		$this->open_log_session();
		$this->close_log_session();

		$this->vars(array(
			"time" => $this->time2date($this->log[$id]["time"],2),
			"event" => $this->log[$id]["event"]["event"],
			"response" => htmlentities($this->log[$id]["response"]),
			"sched_url" => $this->mk_my_orb("show"),
			"log_url" => $this->mk_my_orb("show_log"),
		));

		return $this->parse();
	}


	// ah yes. Each time I'm saving the schedulering object, I need to create a new
	// record in the scheduler table. And that pretty much is it too

	//$this->add(array("event" => $link,"rep_id" => $id, "uid" => $obj["meta"]["login_uid"],"password" => $obj["meta"]["login_password"]));

	function _check_hash_tbl()
	{
		if (!$this->db_table_exists("user_hashes"))
		{
			$this->db_query("CREATE TABLE user_hashes(uid varchar(100),hash char(32), hash_time int)");
		}
	}

	/** Shows a list of all scheduled items
		@attrib name=list_entries
	**/
	function list_entries()
	{
		// nii, nyyd ma pean kuidagi html-i tegema? how?
		$htmlc = get_instance("cfg/htmlclient");


		$t = new vcl_table();

		$t->define_field(array(
			"name" => "time",
			"caption" => t("Aeg"),
		));

		$t->define_field(array(
			"name" => "url",
			"caption" => t("URL"),
		));

		$t->define_field(array(
			"name" => "rep_id",
			"caption" => t("Kordus"),
		));

		$this->open_session();
		$newdat = array();
		foreach($this->repdata as $e)
		{
			arr($e);
		}

		$this->close_session();

		$htmlc->add_property(array(
			"type" => "table",
			"vcl_inst" => &$t,
		));

		$htmlc->finish_output();

		return $htmlc->get_result();


	}

	/**
		@attrib name=static_sched nologin="1"
	**/
	function static_sched($arr)
	{
		// basically, what this thing should do, is:
		// read the static scheduler definition file and if the time for something is about to come, then
		// generate the correct url and add it to the real scheduler
		$p = xml_parser_create();
		xml_parse_into_struct($p, file_get_contents(aw_ini_get("basedir")."/xml/static_scheduler.xml"), $vals, $index);
		xml_parser_free($p);
		foreach($vals as $val)
		{
			if ($val["tag"] == "REPEAT" && $val["type"] == "complete")
			{
				if (strpos($val["attributes"]["TIME"], "+") !== false)
				{
					list($tm, $add) = explode("+", $val["attributes"]["TIME"]);

					list($hr, $min) = explode(":", $tm);
					if (substr($add, 0, 4) == "rand")
					{
						if (preg_match("/rand\((\d)\)/", $add, $mt))
						{
							$t_mins = $hr * 60 + $min;
							$t_mins += rand(1, $mt[1]*60);
							$hr = floor($t_mins / 60);
							$min = $t_mins % 60;
						}
					}
				}
				else
				{
					list($hr, $min) = explode(":", $val["attributes"]["TIME"]);
				}
				if ($val["attributes"]["TYPE"] == "daily" && date("H") == ($hr-1))
				{
					// add to scheduler
					$this->add(array(
						"event" => str_replace("automatweb/", "", $this->mk_my_orb($val["attributes"]["ACTION"], array(), $val["attributes"]["CLASS"])),
						"time" => mktime($hr,$min,0, date("m"), date("d")+ ($hr < date("H") ? 1 : 0), date("Y"))
					));
				}
				else
				if ($val["attributes"]["TYPE"] == "monthly" && date("H") == ($hr-1) && date("d") == $val["attributes"]["DAY"])
				{
					// add to scheduler
					$this->add(array(
						"event" => str_replace("automatweb/", "", $this->mk_my_orb($val["attributes"]["ACTION"], array(), $val["attributes"]["CLASS"])),
						"time" => mktime($hr,$min,0, date("m"), date("d")+ ($hr < date("H") ? 1 : 0), date("Y"))
					));
				}
			}
		}
	}

}
