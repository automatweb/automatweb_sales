<?php

class planner_model extends core
{
	public $recur_info;

	function planner_model()
	{
		$this->init();
	}

	function get_event_sources($id)
	{
		$obj = new object($id);
		$sources = array();
		$sources = $this->make_keys($this->get_event_folders(array("id" => $id)));
		if ($obj->prop("my_projects") == 1)
		{
			$project = aw_global_get("project");
			// this is wrong, I need to figure out the users this calendar belongs to
			$owners = $obj->connections_from(array(
				"type" => "RELTYPE_CALENDAR_OWNERSHIP",
			));
			// ignore projects, if there are no users connected to this calendar
			if (sizeof($owners) == 0)
			{
				$pr = aw_global_get("project");
				if(is_oid($pr))
				{
					$sources = array($pr => $pr);
				}
			}
			else
			{
				$user_ids = array();
				foreach($owners as $owner)
				{
					$user_ids[] = $owner->prop("to");
				}
				$prj = get_instance(CL_PROJECT);
				$tmp = $prj->get_event_folders(array(
					"user_ids" => $user_ids,
					"project_id" => aw_global_get("project"),
					"type" => "my_projects",
				));
				if (!is_array($tmp))
				{
					$tmp = array($tmp => $tmp);
				}
				if (aw_global_get("project"))
				{
					$sources = $tmp;
				}
				else
				{
					$sources = $sources + $tmp;
				}
			}
		}
		return $sources;
	}

	/** returns a list of folders for a specified calendar, this is more efficient that returning a list of events

	**/
	function get_event_folders($arr)
	{
		// if given names argument, then return a list of id => name pairs,
		// otherwise just id-s - suitable for feeding to object_list
		$cal_obj = new object($arr["id"]);
		$folders = array();

		if (!is_oid($arr["id"]))
		{
			return $folders;
		}
		$evt_folder = $cal_obj->prop("event_folder");
		if (is_oid($evt_folder))
		{
			$folders[$evt_folder] = $cal_obj->name();
		};

		// get others as well
		$folderlist = $cal_obj->connections_from(array(
			"type" => "RELTYPE_EVENT_SOURCE",
		));

		foreach($folderlist as $conn)
		{
			$_tmp = $conn->to();
			$clid = $_tmp->class_id();
			if ($clid == CL_PLANNER)
			{
				$evt_folder = $_tmp->prop("event_folder");
				if (is_oid($evt_folder))
				{
					$folders[$evt_folder] = $_tmp->name();
				}
			}

			if ($clid == CL_PROJECT)
			{
				$folders[$_tmp->id()] = $_tmp->name();
			}
		}
		return isset($arr["names"]) ? $folders : array_keys($folders);
	}

	// this is called from calendar "properties"
	function _init_event_source($args = array())
	{
		extract($args);

		$di = date_calc::get_date_range(array(
			"date" => isset($date) ? $date : date("d-m-Y"),
			"type" => $type,
		));
		
		if (aw_template::bootstrap()) {
			$di["start"] = $args["start"] = mktime(0, 0, 0, 1, 1, 2010);
			$di["end"] = $args["end"] = mktime(0, 0, 0, 8, 1, 2013);
		}

		$start = $di["start"];
		$end = $di["end"];


		$obj = new object($id);
		$this->id = $id;
		$events = $this->get_event_list($args);

		if (count($events))
		{
			// load participant list
			$ol = new object_list(array(
				"class_id" => CL_CRM_MEETING,
				"oid" => array_keys($events)
			));
			if ($ol->count())
			{
				$meetings = $ol->ids();
				$c = new connection();
				$conns = $c->find(array(
					"from.class_id" => CL_CRM_PERSON,
					"to" => $meetings,
					"type" => "RELTYPE_PERSON_MEETING"
				));
				$participants = array();
				foreach($conns as $con)
				{
					$participants[$con["to"]][$con["from"]] = array(
						"id" => $con["from"],
						"name" => $con["from.name"],
						"class_id" => $con["from.class_id"],
					);
				}

				$conns = $c->find(array(
					"from.class_id" => CL_CRM_MEETING,
					"from" => $meetings,
					"type" => "RELTYPE_CUSTOMER"
				));
				$customers = array();
				foreach($conns as $con)
				{
					$customers[$con["from"]][$con["to"]] = array(
						"id" => $con["to"],
						"name" => $con["to.name"],
						"class_id" => $con["to.class_id"],
					);
				}
			}
		}

		$reflist = array();
		$rv = array();
		// that eidlist thingie is no good! I might have events out of my range which I still need to include
		// I need folders! Folders! I'm telling you! Folders! Those I can probably include in my query!
		foreach($events as $event)
		{
			// fuck me. plenty of places expect different data from me .. until I'm
			// sure that nothing breaks, I can't remove this
			if (!$this->can("view", $event["id"]))
			{
				continue;
			}
			$of = new object($event["id"]);
			$row = $event + $of->properties();
			$row["parts"] = isset($participants[$event["id"]]) ? $participants[$event["id"]] : null;
			$row["custs"] = isset($customers[$event["id"]]) ? $customers[$event["id"]] : null;

			$rec = array();
			$gx = date("dmY",$event["start"]);
			$row["link"] = $this->get_event_edit_link(array(
				"cal_id" => $this->id,
				"event_id" => $event["id"],
			));

			$eo = $of;
			if ($row["status"] == 0)
			{
				continue;
			}

			if (!isset($row["oid"]) or $row["brother_of"] != $row["oid"])
			{
				$real_obj = $of->get_original();
				$eo = $real_obj;
				$row["name"] = $real_obj->name();
				$row["comment"] = $real_obj->comment();
				$row["status"] = $real_obj->status();
				$row["flags"] = $real_obj->flags();
			}

			if ($of->class_id() == CL_BUG)
			{
				if ($of->prop("bug_status") < 3)
				{
					$row["event_icon_url"] = aw_ini_get("baseurl")."/automatweb/images/icons/bug_open.gif";
				}
				else
				if ($of->prop("bug_status") == 5)
				{
					$row["event_icon_url"] = aw_ini_get("baseurl")."/automatweb/images/icons/bug_closed.gif";
				}
				else
				{
					$row["event_icon_url"] = icons::get_icon_url($eo);
				}
			}
			else
			{
				$row["event_icon_url"] = icons::get_icon_url($eo);
			}

			if ($of->class_id() == CL_CRM_PERSON)
			{
				$row["name"] = sprintf(t("%s s&uuml;nnip&auml;ev!"), $of->name());
			}

			$rv[$gx][$row["brother_of"]] = $row;
			if ($args["flatlist"])
			{
				$reflist[] = &$rv[$gx][$row["brother_of"]];
			}
		}

		return isset($args["flatlist"]) ? $reflist : $rv;
	}

	////
	// !Returns an array of event id, start and end times in requested range
	// required arguments
	// id - calendar object
	function get_event_list($arr)
	{
		$obj = new object($arr["id"]);
		$event_ids = array();
		$folders = $this->get_event_folders(array("id" => $obj->id()));

		if(!isset($arr["start"]) && isset($arr["range"]["start"]))
		{
			$arr["start"] = $arr["range"]["start"];
		}
		if(!isset($arr["end"]) && isset($arr["range"]["end"]))
		{
			$arr["end"] = $arr["range"]["end"];
		}
		if (empty($arr["start"]))
		{
			$di = date_calc::get_date_range(array(
				"date" => isset($arr["date"]) ? $arr["date"] : date("d-m-Y"),
				"type" => $arr["type"],
				"fullweeks" => 1,
			));

			$_start = $di["start"];
			$_end = $di["end"];
		}
		else
		{
			$_start = $arr["start"];
			$_end = $arr["end"];
		};
		// also include events from any projects that are connected to this calender
		// if the user wants so

		// "my_projects" is misleading, what it actually does is that it includes
		// events from projects that the owner of the current calendar participiates in
		if ($obj->prop("my_projects") == 1)
		{
			$project = aw_global_get("project");
			// this is wrong, I need to figure out the users this calendar belongs to
			$owners = $obj->connections_from(array(
				"type" => "RELTYPE_CALENDAR_OWNERSHIP",
			));

			// ignore projects, if there are no users connected to this calendar
			if (sizeof($owners) == 0)
			{
				$pr = aw_global_get("project");
				if(is_oid($pr))
				{
					$folders = array($pr);
				}
			}
			else
			{
				$user_ids = array();

				foreach($owners as $owner)
				{
					$user_ids[] = $owner->prop("to");
				};

				$prj = new project();
				$tmp = $prj->get_event_folders(array(
					"user_ids" => $user_ids,
					"project_id" => aw_global_get("project"),
					"type" => "my_projects",
				));

				if (!is_array($tmp))
				{
					$tmp = array($tmp);
				}

				if (aw_global_get("project"))
				{
					$folders = $tmp;
				}
				else
				{
					$folders = $folders + $tmp;
				}
			}
		}


		$rv = array();
		$eidstr = $parstr = "";

		if (sizeof($folders) == 0)
		{
			return array();
		};

		$parprefix = " AND ";
		$parstr = "objects.parent IN (" . join(",",$folders) . ")";

		// that is the basic query
		// I need to add different things to it
		$old_query = 1;
		if ($old_query)
		{
			$q = "SELECT ".$this->db_fn("objects.oid")." AS id,".$this->db_fn("objects.brother_of").",".$this->db_fn("objects.name").",".$this->db_fn("planner.start").",".$this->db_fn("planner.end")."
				FROM planner
				LEFT JOIN objects ON (".$this->db_fn("planner.id")." = ".$this->db_fn("objects.brother_of").")
				INNER JOIN objects o2 ON (".$this->db_fn("o2.oid")." = ".$this->db_fn("objects.brother_of").")
				WHERE NOT (".$this->db_fn("planner.start")." >= '${_end}' OR
				".$this->db_fn("planner.end")." <= '${_start}') AND ". $this->db_fn("objects.status")." AND ". $this->db_fn("o2.status").">0 ";

		/*	$q = "SELECT ".$this->db_fn("objects.oid")." AS id,".$this->db_fn("objects.brother_of").",".$this->db_fn("objects.name").",".$this->db_fn("planner.start").",".$this->db_fn("planner.end")."
				FROM planner
				LEFT JOIN objects ON (".$this->db_fn("planner.id")." = ".$this->db_fn("objects.brother_of").")
				WHERE ".$this->db_fn("planner.start")." >= '${_start}' AND
				(".$this->db_fn("planner.start")." <= '${_end}' OR ".$this->db_fn("planner.end")." IS NULL) AND ". $this->db_fn("objects.status")." ";*/
		}
		else
		{
			$q = "SELECT ".$this->db_fn("objects.oid")." AS id,".$this->db_fn("objects.brother_of").",".$this->db_fn("objects.name").",".$this->db_fn("planner.start").",".$this->db_fn("planner.end")."
			FROM planner
			LEFT JOIN objects ON (".$this->db_fn("planner.id")." = ".$this->db_fn("objects.brother_of").")
			WHERE (".$this->db_fn("planner.end")." >= '${_start}' OR ".$this->db_fn("planner.end")." IS NULL OR ".$this->db_fn("planner.end")." = 0) AND
			(".$this->db_fn("planner.start")." <= '${_end}' ) AND
			".$this->db_fn("objects.status")." ";
		};

		// see on 1 case.

		// and I have a second one too

		if(isset($arr["status"]) and is_array($arr["status"]))
		{
			$q .= "IN (".implode(",", $arr["status"]).")";
		}
		else
		{
		 	$q .= "!= 0";
		}

		// lyhidalt. planneri tabelis peaks kirjas olema. No, but it can't be there
		// I need to connect that god damn recurrence table into this fucking place.

		// if events from a project were requested, then include events
		// from that projects only - id's are in event_ids array()

		//if ($project)
		//{
		//	$q .= $eidstr;
		//}
		// include events from all folders and all projects
		//else
		//{
			if ($parstr)
			{
				$q .= $parprefix . "(" . $parstr . $eidstr . ")";
			};
		//}

		if(!empty($arr["group_by"]))
		{
			$q .= "GROUP BY ".$this->db_fn("planner.start");
		}

		// now, I need another clue string .. perhaps even in that big fat ass query?
		$this->db_query($q);
		while($row = $this->db_next())
		{
			$rv[$row["brother_of"]] = array(
				"id" => $row["brother_of"],
				"start" => $row["start"],
				"end" => $row["end"],
			);
		}

		$fldstr = join(",",$folders);

		if (aw_ini_get("calendar.recurrence_enabled") == 1)
		{
			// now collect recurrence data
			$q = "SELECT planner.id,planner.start,planner.end,recurrence.recur_start,recurrence.recur_end FROM planner,aliases,recurrence,objects
				WHERE planner.id = objects.brother_of AND objects.parent IN ($fldstr) AND recurrence.recur_end >= ${_start} AND recurrence.recur_start <= ${_end}
					AND planner.id = aliases.source AND objects.status != 0 AND aliases.target = recurrence.recur_id
					AND aliases.type = " . CL_RECURRENCE;
			//print $q;
			$this->db_query($q);
			while($row = $this->db_next())
			{
				// now, I have to include that information in my result set as well, otherwise
				// the events outside my current scope will not show up even it they recur
				// in the range I'm viewing at the moment
				$evt_id = $row["id"];
				if (empty($rv[$evt_id]))
				{
					$rv[$evt_id] = array(
						"id" => $evt_id,
						"start" => $row["start"],
						"end" => $row["end"],
					);
				};
				$this->recur_info[$row["id"]][] = $row["recur_start"];
			};
		};

		// get b-days
		if ($obj->prop("show_bdays") == 1)
		{
			$s_m = date("m", $_start);
			$e_m = date("m", $_end);
			$q = "
				SELECT
					objects.name as name,
					objects.oid as oid,
					kliendibaas_isik.birthday as bd
				FROM
					objects  LEFT JOIN kliendibaas_isik ON kliendibaas_isik.oid = objects.brother_of
				WHERE
					objects.class_id = '145' AND
					objects.status > 0  AND
					kliendibaas_isik.birthday != '' AND kliendibaas_isik.birthday != 0 AND kliendibaas_isik.birthday is not null
			";
			$this->db_query($q);
			while ($row = $this->db_next())
			{
				//$m = date("m", $row["bd"]);
				list($y, $m, $d) = explode("-", $row["bd"]."--");
				if (($s_m > $e_m ? ($m >= $s_m || $m <= $e_m) : ($m >= $s_m && $m <= $e_m)))
				{
					$ds = $obj->prop("day_start");
					$bs = mktime($ds["hour"], $ds["minute"], 0, $m, $d, date("Y"));
					$rv[$row["oid"]] = array(
						"id" => $row["oid"],
						"start" => $bs,
						"end" => $bs+3600,
					);
				}
			}
		}

		// list bugs as well
		$owner = $obj->get_first_obj_by_reltype("RELTYPE_CALENDAR_OWNERSHIP");
		if ($owner)
		{
			$u = $owner->instance();
			$owner = $u->get_person_for_user($owner);
			$ol = new object_list(array(
				"class_id" => CL_BUG,
				"who" => $owner,
				"deadline" => new obj_predicate_compare(OBJ_COMP_BETWEEN, $_start, $_end),
				"lang_id" => array(),
				"site_id" => array()
			));
			foreach($ol->arr() as $bug)
			{
				$rv[$bug->id()] = array(
					"id" => $bug->id(),
					"start" => $bug->prop("deadline") - ($bug->prop("num_hrs_guess") * 3600),
					"end" => $bug->prop("deadline"),
				);
			}
		}
		return $rv;
	}

	// !Returns a link for editing an event
	// cal_id - calendar id
	// event_id - id of an event
	function get_event_edit_link($arr)
	{
		$evo = obj($arr["event_id"]);
		if (true || $evo->class_id() == CL_CRM_PERSON || $evo->class_id() == CL_BROTHER_DOCUMENT || $evo->class_id() == CL_DOCUMENT || $evo->class_id() == CL_BUG) // birthday
		{
			return html::get_change_url($evo->id(), array("return_url" => get_ru()));
		}

		return $this->mk_my_orb("change",array(
			"id" => $arr["cal_id"],
			"group" => "add_event",
			"event_id" => $arr["event_id"],
			"return_url" => $arr["return_url"],
		), "planner");

	}
}
