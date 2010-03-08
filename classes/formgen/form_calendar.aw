<?php
// $Header: /home/cvs/automatweb_dev/classes/formgen/form_calendar.aw,v 1.33 2008/01/31 13:54:33 kristo Exp $
// form_calendar.aw - manages formgen controlled calendars
/*
@classinfo  maintainer=kristo
*/
classload("formgen/form_base");
class form_calendar extends form_base
{
	function form_calendar($args = array())
	{
		$this->form_base();
	}

	////
	//! Processes frames
	function _process_frame($val,$key)
	{
		// meid huvitab ainult vektori see osa, mis asub enne meid huvitava
		// perioodi lıppu, s.t. me peame kokku liitma kıik v‰‰rtused, mis
		// enne perioodi lıppu toimuvad
		// ma pean teadma, mis seisus on vastav vektor enne mind huvitava
		// ajalıigu algust, this code will take care of it

		// this will be invoked only at the first pass
		if (not($this->current_block))
		{
			list($this->bid,$this->current_block) = each($this->blocks);
			$this->state = 0;
		};

		// advance the other pointer, repeat and rinse until we find the first block,
		// which actually matches the first key of the vector (try to sync the array pointers)
		while ( ($key > $this->current_block["start"]) && ($this->bid) && ($this->bid < sizeof($this->blocks)) )
		{
			
			$this->blocks[$this->bid]["cnt"] = $this->state;
			list($this->bid,$this->current_block) = each($this->blocks);
		};
		
		$this->state += $val;

		if ($key <= $this->current_block["end"])
		{
			$this->blocks[$this->bid]["cnt"] = $this->state;

		}
		else
		{
			// still, I have to set the state up to the end of the blocks
			if (isset($this->bid))
			{
				$this->blocks[$this->bid]["cnt"] = $this->state;
			};
		};
			
		// advance the other pointer too			
		list($this->bid,$this->current_block) = each($this->blocks);
	}

	////
	// !Fetches all form entries from a date range
	// id(int) - form id
	// start(int) - start of the range
	// end(int) - end of the range
	function _get_entries_in_range($args = array())
	{
		extract($args);
		$this->load($id);
		$ctls = $this->get_form_elements(array("id" => $id));
		foreach($ctls as $key => $val)
		{
			if ( ($val["type"] == "date") && ($val["subtype"] == "from") )
			{
				$el_start = $val["id"];
			}

			if ( ($val["type"] == "date") && ($val["subtype"] == "to") )
			{
				$el_end = $val["id"];
			}
			
			if ( ($val["type"] == "textbox") && ($val["subtype"] == "count") )
			{
				$count_el = $val["id"];
			}

		};

		$this->ev_entry_start_el = $el_start;
		$this->ev_entry_end_el = $el_end;

		$ft_name = sprintf("form_%s_entries",$id);

		// this means that we can ignore one entry when doing our calculations
		$ignore = (int)$ignore;

		// this query could be faster if the date elements in the database
		// would be integers

		// I should ignore events which
		// 1) end before my time slot starts ($ev[end] < $start)
		// 2) start after my time slot ends ($ev[start] > $end)
		// 3) are $ignored
		$chains = $this->get_chains_for_form($id);
		list($cid,) = each($chains);

		$q = sprintf("SELECT *,objects.name AS name FROM $ft_name
				LEFT JOIN objects ON ($ft_name.id = objects.oid)
				LEFT JOIN form_entries ON ($ft_name.id = form_entries.id)
				WHERE form_entries.cal_id = '$cal_id' AND objects.status = 2 AND objects.oid != $ignore AND (el_%s >= %d) AND (el_%s <= %d)",
				$el_end,$start,$el_start,$end);
		if ($GLOBALS["fg_dbg"])
                {
                        echo ("sql = $q <br />");
                }

		$this->db_query($q);
		$events = array();
		$this->raw_events = array();
		$this->raw_headers = array();

		while($row = $this->db_next())
		{
			$e_start = $row["el_" . $el_start];
			$e_end = $row["el_" . $el_end];
			$e_count = (int)$row["el_" . $count_el];

			$dkey = date("dmY",$e_start);

			$event = array(
				"start" => $e_start,
				"end" => $e_end,
				"title" => $row["name"],
				"link" => $this->mk_my_orb("show",array("id" => $cid,"entry_id" => $row["oid"]),"form_chain"),
			);

			$events[$dkey][] = $event;	

			for ($xi = $e_start; $xi < $e_end; $xi = $xi + (60*60*24))
			{
				$dkey = date("dmY",$xi);
				$this->raw_events[$dkey][] = $row;
				$this->event_counts[$dkey] += $e_count;
			}
		
			// update the timeline vector as well	
			// --------------
			// set 'em to zero, if we encounter them the first time
			if (not($this->vector[$e_start]))
			{
				$this->vector[$e_start] = 0;
			};
			
			if (not($this->vector[$e_end]))
			{
				$this->vector[$e_end] = 0;
			};

			// and now increment/decrement the value
			$this->vector[$e_start] += $e_count;
			$this->vector[$e_end] -= $e_count;
		}

		return $events;
	}
	
	////
	// !Fetches all form entries from a date range
	// id(int) - form id
	// start(int) - start of the range
	// end(int) - end of the range
	function _get_entries_in_range2($args = array())
	{
		extract($args);

		$ft_name = sprintf("form_%s_entries",$eform);

		$q = "SELECT * FROM calendar2event
			LEFT JOIN objects ON calendar2event.entry_id = objects.oid
			WHERE cal_id = '$cal_id' AND relation = '$ctrl' AND start >= '$start' AND end <= '$end' AND objects.status != 0";
		if ($GLOBALS["fg_dbg"])
                {
                        echo ("sql = $q <br />");
                }
		$this->db_query($q);
		$events = array();
		$this->raw_events = array();
		$this->raw_headers = array();

		while($row = $this->db_next())
		{
			$this->save_handle();
			$q = "SELECT * FROM form_" . $row["form_id"] . "_entries WHERE id = '$row[entry_id]'";
			$this->db_query($q);
			$row2 = $this->db_next();
			$this->restore_handle();
			if (is_array($row2))
			{
				$row = $row + $row2;
			};
			$e_start = $row["start"];
			$e_end = $row["end"];
			if (!$e_end)
			{
				$e_end = $e_start + 86399;
			};
			$e_count = (int)$row["items"];

			$dkey = date("dmY",$e_start);
			$ekey = date("dmY",$e_end);

			$event = array(
				"start" => $e_start,
				"end" => $e_end,
				"title" => $row["name"],
				"link" => $this->mk_my_orb("show",array("id" => $cid,"entry_id" => $row["oid"]),"form_chain"),
			);

			$events[$dkey][] = $event;	

			for ($xi = $e_start; $xi < $e_end; $xi = $xi + (60*60*24))
			{
				$dkey = date("dmY",$xi);
				$this->raw_events[$dkey][] = $row;
				$this->event_counts[$dkey] += $e_count;
			}
		
			// update the timeline vector as well	
			// --------------
			// set 'em to zero, if we encounter them the first time
			if (not($this->vector[$e_start]))
			{
				$this->vector[$e_start] = 0;
			};
			
			if (not($this->vector[$e_end]))
			{
				$this->vector[$e_end] = 0;
			};

			// and now increment/decrement the value
			$this->vector[$e_start] += $e_count;
			$this->vector[$e_end] -= $e_count;
		}

		return $events;
	}

	////
	// !Processes one controller form entry
	function _ctrl_process_entry($args = array())
	{
		extract($args);

		$this->load_entry($entry_id);

		// maybe this function should return the building blocks instead?
		// available date ranges and max items in each?
		$ct_start = $this->entry[$this->start_el];
		$ct_end = $this->entry[$this->end_el];
		$ct_max = $this->entry[$this->max_el];
		$ct_tslice = aw_unserialize($this->entry[$this->tslice_el]);
		$ct_cnt = ($ct_tslice["count"] > 0) ? $ct_tslice["count"] : 1;
		$ct_tslice2 = aw_unserialize($this->entry[$this->tslice_el2]);
		// this one can be zero as well
		$ct_pregap = $ct_tslice2["count"];

		$shift = ($ct_tslice["type"] == "day") ? 3600 * 24 : 3600;

		$blocks = array();

		// XXX 3600
		$timeshift = $ct_pregap * 3600;
		for ($i = ($start + $timeshift); $i <= $end; $i=$i+($shift * $ct_cnt)+$timeshift)
		{
			// if it is in range, then ..
			if ( ($i >= $ct_start) && ($i <= $ct_end) )
			{
				$blocks[] = array(
					"start" => $i,
					"end" => $i+($shift * $ct_cnt) - 1,
					"max" => $ct_max,
				);
			};
		}

		return $blocks;
        }

	function new_event($args = array())
	{
		extract($args);
		$q = "INSERT INTO calendar2event (entry_id,cal_id,start,end,items)
			VALUES ('$entry_id','$cal_id','$start','$end','$items')";
		$this->db_query($q);
	}

	function upd_event($args = array())
	{
		extract($args);
		$q = "DELETE FROM calendar2event WHERE entry_id = '$entry_id'";
		$this->db_query($q);

		$this->new_event($args);

	}

	////
	// !Registers a new calendar (invoked from form_chain submit)
	// cal_id - int
	// form_id - int id of event entry form
	// vform_id - int id of the period definition form inside the chain
	function new_calendar($args = array())
	{
		extract($args);
		$q = "INSERT INTO calendar2object (cal_id,form_id,vform_id)
			VALUES ('$cal_id','$form_id','$vform_id')";
		$this->db_query($q);
	}
	
	////
	// !Updates an existing calendar (invoked from form_chain submit)
	// cal_id - int
	// form_id - int id of event entry form
	// vform_id - int id of the period definition form inside the chain
	function upd_calendar($args = array())
	{
		extract($args);
		// reap the old entry
		$q = "DELETE FROM calendar2object WHERE cal_id = '$cal_id'";
		$this->db_query($q);

		// only re-add if the user still wishes to have a calendar
		// for that chain
		if ($active)
		{
			$this->new_calendar($args);
		}

		// event entry form needs to know to which calendar 
		// it will store it's entries
		$fb = get_instance("formgen/form_base");
		$fb->load($form_id);
		$fb->meta["calendar_chain"] = $cal_id;
		$fb->save();
	}

	////
	// !Retrieves calender record
	// cal_id - int
	function get_calendar($args = array())
	{
		extract($args);
		$row = false;
		if ($cal_id)
		{
			$q = "SELECT * FROM calendar2object WHERE cal_id = '$cal_id'";
			$this->db_query($q);
			$row = $this->db_next();
		}
		return $row;
	}

		
	function _process_blocks($args = array())
	{
		//$shift = ($ct_tslice["type"] == "day") ? 3600 * 24 : 3600;
		extract($args);
		$shift = 3600 * 24;
		$blocks = array();

		// XXX 3600
		$timeshift = $pregap * 3600;

		if ($period_cnt < 1)
		{
			$period_cnt = 1;
		};


		//for ($i = ($start + $timeshift); $i <= $end; $i=$i+($shift * $timedef)+$timeshift)
		for ($i = ($start); $i <= $end; $i=$i+($shift * $period_cnt))
		{
			// if it is in range, then ..
			if ( ($i >= $this->start) && ($i <= $this->end) )
			{
				$blocks[] = array(
					"start" => $i,
					"end" => $i+($shift) - 1,
					"max" => $max_items,
				);
			};
		}
		return $blocks;
	}
	
	function check_vacancies($args = array())
	{
		extract($args);	
		$id = (int)$id;

		if (is_array($entry_id))
		{
			$r_entry_id = join(",",$entry_id);
		}
		else
		{
			$r_entry_id = $entry_id;
		};
		
		$q = "SELECT SUM(max_items) AS max FROM calendar2timedef
			 WHERE oid = '$contr' 
				AND relation IN ($r_entry_id) AND start <= '$end' AND end >= '$start'";
		if ($GLOBALS["fg_dbg"]) 
		{
			echo ("sql = $q <br />");
		}

		$this->db_query($q);
		$row2 = $this->db_next();
		$max = (int)$row2["max"];
		//print "loading window for $entry_id - it has $max max slots<br />";
		// and now, for each calendar figure out how many
		// free spots does it have in the requested period.
		// for this, I'll have to query the calendar2event table

		// kui eventi_lopp >= otsingu_algus OR eventi_algus <= otsingu_lopp
		// siis langeb see event meid huvitava ajavahemiku sisse ja ma tean
		// tema broneeritud ruumide summaga arvestama
		$q = "SELECT SUM(items) AS sum FROM calendar2event
			LEFT JOIN objects ON (calendar2event.entry_id = objects.oid)
			WHERE cal_id = '$cal_id' AND form_id = '$id'
				AND relation = '$entry_id' 
				AND end >= '$start' AND start <= '$end'";
		if ($GLOBALS["fg_dbg"]) 
		{
			echo ("sql = $q <br />");
		}
		/*
		print $q;
		print "<br />";
		*/
		$this->db_query($q);
		$row2 = $this->db_next();
		$sum = (int)$row2["sum"];
		$vac = $max - $sum - $req_items;
		if ($GLOBALS["fg_dbg"]) 
		{
			print "id = $r_entry_id, max avail = $max, reserved = $sum, vac = $vac, requested = $req_items<br />\n";
			print "$sum ruumi on broneeritud<br />\n";
			print "$vac j‰‰ks j‰rgi<br />\n";
		}
		return $vac;
	}
	
	function get_events($args = array())
	{
		$id = (int)$id;
		extract($args);	
		$found = false;
		$blocks = array();
		$events = array();
		$this->raw_events = array();
		$this->raw_headers = array();

		$this->load($eform);
		//$q = "SELECT * FROM calendar2timedef LEFT JOIN objects ON (calendar2timedef.entry_id = objects.oid) WHERE calendar2timedef.oid = '$eform' AND start <= '$start' AND end >= '$end' AND relation = '$ctrl' AND status = 2";
		$q = "SELECT * FROM calendar2timedef LEFT JOIN objects ON (calendar2timedef.entry_id = objects.oid) WHERE calendar2timedef.oid = '$eform' AND start <= '$end' AND end >= '$start' AND relation = '$ctrl' AND status = 2";
		if ($GLOBALS["fg_dbg"])
                {
                        echo ("sql = $q <br />");
                }
		$this->db_query($q);
		$this->start = $start;
		$this->end = $end;
		while($row = $this->db_next())
		{
			$found = true;
			$new_blocks = $this->_process_blocks($row);
			$blocks = $blocks + $new_blocks;
		}

		$this->vector = array();

		if ($eform)
		{
			//$q = "SELECT * FROM calendar2timedef LEFT JOIN objects ON (calendar2timedef.entry_id = objects.oid) WHERE calendar2timedef.oid = '$eid' AND start <= '$start' AND end >= '$end' AND status = 2";

			// get events in range and build an incremental vector of all events. for
			// example, if we have 2 events, one from 10:00-13:00 and other from
			// 11:00-14:00, then the vector will look akin to (keys are timestamps)
			// [10:00] -> +1
			// [11:00] -> +1
			// [13:00] -> -1
			// [14:00] -> -1

			// --------
	
			$events = $this->_get_entries_in_range2(array(
							"start" => $start,
							"end" => $end,
							"cal_id" => $eid,
							"ctrl" => $ctrl,
			));

			// now we have all the ranges (if any) and events (if any)
			// and are going to build the events for calendar
			ksort($this->vector);
			$this->blocks = $blocks;
			reset($this->blocks);
		
			if ( is_array($this->blocks) && (sizeof($this->blocks) > 0) )
			{
				array_walk($this->vector,array(&$this,"_process_frame"));
			}

			// If I want to find vacancies, then I have to check whether each block has
			// the required amount of blocks available:

			//if ($check == "vacancies")
			//{
				$this->has_vacancies = true;
			//}
			
			// do not show usual entries		
			//$events = array();

			// no blocks, just bail out
			if (sizeof($this->blocks) == 0)
			{
				$this->has_vacancies = false;
			}

			// done. now we can build the special entries for the calenar


			foreach($this->blocks as $bid => $block)
			{
				$dkey = date("dmY",$block["start"]);
				$cnt = (int)$this->event_counts[$dkey];
				//$vac = $block["max"] - $block["cnt"];
				$vac = $block["max"] - $cnt;

				if (!$this->cfg["hide_availability_indicator"])
				{
					$title = "<small>free <b>$vac of $block[max]</b></small>";
				};

				if ($vac == $block["max"])
				{
					$color = "#AAFFAA";
				}
				elseif ($vac == 0)
				{
					//$title = "fully booked ($block[max])!";
					$color = "#FF6633";
				}
				// shouldn't happen, maybe should even be configurable?
				elseif ($vac < 0)
				{
					$title = sprintf("<b>OVERBOOKED BY %d!</b>",abs($vac));
					$color = "#FF0000";
				}
				else
				{
					$color = "#FFFFAA";
				};
			
				$this->raw_headers[$dkey] = $title;
		
				$dummy = array(
					"title" => $title,
					"start" => $block["start"],
					"end" => $block["end"],
					"color" => $color,
					"target" => "_new",
				);

				if ($vac != 0)
				{
					$events[$dkey][] = $dummy;
				}
			};
	
		}
		return $events;
		#return $found;

	}

	////
	// !Returns the number of vacancies
	// start (array) - period start
	// end (timestamp) - period end
	// cal_id (int) - calendar id
	// entry_id (int) - entry id
	// contr (int) - calendar controller id
	// req_items (int) - how many items do we want?
	// rel(int) - selector for calendar
	function get_vac_by_contr($args = array())
	{
		extract($args);
		if (!$rel2)
		{
			return -1;
		};
		load_vcl('date_edit');
		$_start = date_edit::get_timestamp($args["start"]);
		list($_d,$_m,$_y) = explode("-",date("d-m-Y",$_start));
		if ($end > 0)
		{
			$_end = $end;
		}
		else
		{
			$_end = mktime(23,59,59,$_m,$_d,$_y);
		};

		$q = "SELECT SUM(max_items) AS max FROM calendar2timedef
			 WHERE oid = '$contr' AND relation IN ($rel) AND txtid = '$txtid'
				AND start <= '$_end' AND end >= '$_start'";
		#$GLOBALS["fg_dbg"] = 1;
		if ($GLOBALS["fg_dbg"])
                {
                        echo ("sql = $q <br />");
                }


		$this->db_query($q);
		$row2 = $this->db_next();
		$max = (int)$row2["max"];

	

		/*print $q;
		print "<br />";
		*/
		// and now, for each calendar figure out how many
		// free spots does it have in the requested period.
		// for this, I'll have to query the calendar2event table
		
		// kui eventi_lopp >= otsingu_algus OR eventi_algus <= otsingu_lopp
		// siis langeb see event meid huvitava ajavahemiku sisse ja ma tean
		// tema broneeritud ruumide summaga arvestama

		if (isset($entry_id))
		{
			$q = "SELECT SUM(items) AS sum FROM calendar2event
				LEFT JOIN objects ON (calendar2event.entry_id = objects.oid)
				WHERE oid != '$entry_id' AND relation = '$rel' AND txtid = '$txtid' AND
					cal_id = '$cal_id' AND form_id = '$id' AND end >= '$_start' AND
					start <= '$_end' AND status != 0";
		}
		else
		{
			$q = "SELECT SUM(items) AS sum FROM calendar2event
				WHERE relation = '$rel' AND txtid = '$txtid' AND
					cal_id = '$cal_id' AND form_id = '$id' AND end >= '$_start' AND
					start <= '$_end'";
		};
		if ($GLOBALS["fg_dbg"])
                {
                        echo ("sql = $q <br />");
                }
		$this->db_query($q);
		$row2 = $this->db_next();
		$sum = (int)$row2["sum"];
		//print "max = $max, sum = $sum, req = $req_items<br />\n";
		$vac = $max - $sum - $req_items;
		$x = &$args;
		$x["max"] = $max;
		return $vac;
	}


	function init_cal_controller($args = array())
        {
		extract($args);
		$q = "SELECT oid,class_id,cal_id,el_relation FROM calendar2forms LEFT JOIN objects ON (calendar2forms.cal_id = objects.oid) WHERE form_id = '$id'";
		$this->db_query($q);
		$row = $this->db_next();
		if ($row["class_id"] == CL_FORM_CHAIN)
		{
			$fch = get_instance(CL_FORM_CHAIN);
			$fch->load_chain($row["cal_id"]);
			$cal_controller = (int)$fch->chain["cal_controller"];
		}
		elseif ($row["class_id"] == CL_FORM)
		{
			$cal_controller = $row["oid"];
		};
		$this->el_relation = $row["el_relation"];
		$this->cal_controller = $cal_controller;
		$this->cal_id = $row["cal_id"];
			
        }

	////
        // !Returns a list of vacancies
        // start (array) - period start
        // end (timestamp) - period end
        // cal_id (int) - calendar id
        // entry_id (int) - entry id
        // contr (int) - calendar controller id
        // req_items (int) - how many items do we want?
        // rel(int) - selector for calendar
        function get_vac_list($args = array())
        {
                extract($args);
                                                                                                                            
                $_start = ($start) ? $start : 0;
                $_end = ($end) ? $end : 0;
                                                                                                                            
                $contr = $this->cal_controller;
		$rel = (int)$this->relation;
                $cal_id = (int)$this->cal_id;
                                                                                                                            
                $q = "SELECT txtid,max_items FROM calendar2timedef
                         WHERE oid = '$contr' AND relation IN ($rel)
                                AND start <= '$_end' AND end >= '$_start'";

                $this->db_query($q);
                $max_items = array();
                while($row = $this->db_next())
                {
                        $max_items[$row["txtid"]] = $row["max_items"];
                };
                $q = "SELECT txtid,items FROM calendar2event
                        LEFT JOIN objects ON (calendar2event.entry_id = objects.oid)
                        WHERE relation = '$rel' AND cal_id = '$cal_id' AND form_id = '$id'
                                AND end >= '$_start' AND start <= '$_end'";

		$this->db_query($q);
                                                                                                                            
                while($row = $this->db_next())
                {
			$max_items[$row["txtid"]] -= $row["items"];
                };

		$tmp = array();
		foreach($max_items as $key => $val)
		{
			if ($val > -1)
			{
				$tmp[$key] = $val;
			};
		};

		return join(",",map2("%s=%d",$tmp));
                                                                                                                            
        }

	function get_rel_el($args = array())
	{
		extract($args);
		$q = "SELECT * FROM calendar2forms WHERE cal_id = '$id'";
		$this->db_query($q);
		$c2f = $this->db_next();
		// now c2f["form_id"] has the id of event entry form
		// and c2f["el_relation"] has the id of the element in that form, which
		// interests us. A relation element. Now we figure from which form
		// that element originates
		$q = "SELECT * FROM form_relations WHERE el_to = $c2f[el_relation]";
		$this->db_query($q);
		$f_r = $this->db_next();

		// $f_r[form_from] is it.
		// $f_r[el_from] is the element id which contains the information we want
		/*
		print "form: $f_r[form_from] el: $f_r[el_from]<br />";
		*/

		// and now the final step - figure out, which
		// load the current chain entry

		$q = "SELECT * FROM form_chain_entries WHERE id = '$chain_entry_id'";
		$this->db_query($q);
		$fce = $this->db_next();
		$_eids = aw_unserialize($fce["ids"]);

		/*
		print "<pre>";
		print_r($_eids);
		print "</pre>";
		*/

		$_eid = $_eids[$f_r["form_from"]];
		return $_eid;

	}

	function check_calendar($args = array())
	{
		static $used_slots = 0;
		extract($args);
		$this->controller_errors = array();
		$q = "SELECT * FROM calendar2forms
				LEFT JOIN objects ON (calendar2forms.cal_id = objects.oid)
				WHERE form_id = '$id'";
		if ($GLOBALS["fg_dbg"])
                {
                        echo ("sql = $q <br />");
                }
		$this->db_query($q);
		$has_vacancies = true;
		$has_errors = false;
		$this->fatal = true;
		$fch = get_instance(CL_FORM_CHAIN);
		/*
		print "<pre>";
		print_r($args["post_vars"]);
		print "</pre>";
		*/
		while($row = $this->db_next())
		{
			// get the vacancy controller for the chain.
			$this->save_handle();
			// now I need to fetch all the records for this from form the
			// calendar2form_relations table
			$q = "SELECT * FROM calendar2form_relations WHERE calendar2forms_id = '$row[id]'";
			$this->db_query($q);
			$rels = array();
			while($relrow = $this->db_next())
			{
				if ($relrow["el_count"])
				{
					$req_items = $args["post_vars"][$relrow["el_count"]];
					if ($req_items == 0)
					{
						// default to 1. is this good?
						$req_items = 1;
					};
					$relrow["req_items"] = $req_items;
					$relrow["el_allow_exceed"] = $row["el_allow_exceed"];
					$lb_sel = $args["post_vars"][$relrow["el_relation"]];
					/*
					print "<pre>";
					print_r($relrow);
					print_r($lb_sel);
					print_r($args["els"][$relrow["el_relation"]]);
					print "</pre>";
					*/

					//if (preg_match("/^element_\d*_lbopt_(\d*)$/",$lb_sel,$m))
					if (!empty($args["els"][$relrow["el_relation"]]["value"]))
					{
						//$relrow["txtid"] = $args["els"][$relrow["el_relation"]]["lb_items"][$m[1]];
						$relrow["txtid"] = $args["els"][$relrow["el_relation"]]["value"];
						$rels[] = $relrow;
					};
				};
			};

			if ($row["class_id"] == CL_FORM_CHAIN)
			{
				$fch->load_chain($row["cal_id"]);
				$cal_controller = (int)$fch->chain["cal_controller"];
			}
			elseif ($row["class_id"] == CL_FORM)
			{
				$cal_controller = $row["oid"];
			};

			$__rel = $args["post_vars"][$row["el_relation"]];
			preg_match("/lbopt_(\d+?)$/",$__rel,$m);
			$_rel = (int)$m[1];


			if ($row["count"] > 0)
			{
				$_req_items = $row["count"];
			}
			else
			{
				$_req_items = (int)$args["post_vars"][$row["el_cnt"]];
				if ($_req_items == 0)
				{
					$_req_items = 1;
				};
			};

			// now I need to figure out the selected value of the relation
			// element
			$q = "SELECT * FROM form_relations WHERE el_to = $row[el_relation]";
			$this->db_query($q);
			$frel = $this->db_next();
			if (!$frel)
			{
				$this->raise_error(ERR_FG_CAL_NORELEL, "No relation found in relation table for calendar relation element $row[el_relation] (check that the relation element in the calendar is set correctly)", true);
			}
			$q = sprintf("SELECT id,ev_%s AS name FROM form_%d_entries WHERE id = '%d'",
					$frel["el_from"],$frel["form_from"],$_rel);
			$this->db_query($q);
			$rowx = $this->db_next();
			if ($chain_entry_id && $row["class_id"] == CL_FORM_CHAIN)
			{
				$rel2 = (int)$chain_entry_id;
			}
			else
			{
				$rel2 = $rowx["id"];
			};
			foreach($rels as $relval)
			{
				$max = 0;
				$used_slots = $used_slots + $relval["req_items"];
				$vac = $this->get_vac_by_contr(array(
						"start" => $args["post_vars"][$row["el_start"]],
						"contr" => $cal_controller,
						"cal_id" => $row["cal_id"],
						"entry_id" => $args["entry_id"],
						//"req_items" => $relval["req_items"],
						"req_items" => $used_slots,
						"txtid" => $relval["txtid"],
						"rel" => $_rel,
						"rel2" => $rel2,
						"id" => $id,
						"max" => &$max,
				));

				//print "vac = $vac<br />";


				//print "<br />max = $max, req_items = $relval[req_items], us = $used_slots, vac = $vac, txtid = $relval[txtid]<br />";

// vana veateade: $this->controller_errors[$err_el][] = "Calendar '$row[name]/$rowx[name]' does not have this many vacancies in the requested period.";

				$this->vac = $vac;

				if ($vac < 0)
				{
					if (empty($relval["el_allow_exceed"]))
					{
						$has_errors = true;
						$this->fatal = true;
						$msg = "On request only!";
					}
					else
					{
						$this->fatal = false;
						$msg = "On request!";
					}
					$this->msg = $msg;
					// where do we put the error message?
					$err_el = ($relval["el_count"]) ? $relval["el_count"] : $row["el_start"];
					$this->controller_errors[$err_el][] = $msg;



					
				};
			};
			$this->restore_handle();

		}; # while
		return $has_errors;
	} # check_calendar

	function get_controller_errors()
	{
		return $this->controller_errors;
	}

	function make_event_relations($args = array())
	{
		extract($args);
		load_vcl('date_edit');
		$this->del_event_relations($eid);
		// cycle over all the forms that this event entry form
		// has been assigned to and write new relations for those
		$q = "SELECT * FROM calendar2forms
				LEFT JOIN objects ON (calendar2forms.cal_id = objects.oid)
				WHERE form_id = '$id'";
		$this->db_query($q);
		while($row = $this->db_next())
		{
			// and I will again have check against all the records in
			// calendar2form_relations
			$this->save_handle();
			// now I need to fetch all the records for this from form the
			// calendar2form_relations table
			$q = "SELECT * FROM calendar2form_relations WHERE calendar2forms_id = '$row[id]'";
			$this->db_query($q);
			$rels = array();
			while($relrow = $this->db_next())
			{
				$relrow["el_use_chain_entry_id"] = $row["el_use_chain_entry_id"];
				if (isset($relrow["el_count"]))
				{
					$count = $args["post_vars"][$relrow["el_count"]];
					if ($count == 0)
					{
						// default to 1. is this good?
						$count = 1;
					};
					$relrow["count"] = $count;
					$lb_sel = $args["post_vars"][$relrow["el_relation"]];
					if (preg_match("/^element_\d*_lbopt_(\d*)$/",$lb_sel,$m))	 
					{	 
						$relrow["txtid"] = $args["els"][$relrow["el_relation"]]["lb_items"][$m[1]];	 
						if ($relrow["el_use_chain_entry_id"])
						{
							$relrow["el_relation"] = $m[1];
						};
						$rels[] = $relrow;	     
					 };
				};
			};

			$_start = (int)date_edit::get_timestamp($args["post_vars"][$row["el_start"]]);

			if ($row["end"] > 0)
			{
				$_end = $_start + $row["end"];
			}
			else
			{
				$_end = (int)date_edit::get_timestamp($args["post_vars"][$row["el_end"]]);
			};

			foreach($rels as $reval)
			{
				$_cnt = $reval["count"];
				$txtid = $reval["txtid"];
				if ($chain_entry_id && ($row["class_id"] == CL_FORM_CHAIN))
				{
					if ($reval["el_use_chain_entry_id"] && $args["post_vars"]["chain_entry_id"])
					{
						$_rel = $args["post_vars"]["chain_entry_id"];
					}
					else
					{
						$__rel = $args["post_vars"][$row["el_relation"]];
						// gah, this sucks so much
						preg_match("/lbopt_(\d+?)$/",$__rel,$m);
						$_rel = (int)$m[1];
			
					};

					$q = "INSERT INTO calendar2event (cal_id,entry_id,start,end,items,relation,form_id,txtid)
						VALUES ('$row[cal_id]','$eid','$_start','$_end','$_cnt','$_rel','$id','$txtid')";
					$this->db_query($q);
					/*
					if (aw_global_get("uid") == "erkihotel")
					{
						print "ıige = ";
						print $q;
						print "<br />\n";
						flush();
					};
					*/
				};
					
				//$_rel = $reval["el_relation"];

				$__rel = $args["post_vars"][$row["el_relation"]];
				// gah, this sucks so much
				preg_match("/lbopt_(\d+?)$/",$__rel,$m);
				$_rel = (int)$m[1];

				$q = "INSERT INTO calendar2event (cal_id,entry_id,start,end,items,relation,form_id,txtid)
					VALUES ('$row[cal_id]','$eid','$_start','$_end','$_cnt','$_rel','$id','$txtid')";
				$this->db_query($q);
				/*
					if (aw_global_get("uid") == "erkihotel")
					{
						print "imelik = ";
						print $q;
						print "<br />\n";
						flush();
					};
					*/
			};
			$this->restore_handle();

		};
	}

	function del_event_relations($eid)
	{
		// reap the old relations, we are creating new ones anyway
		$q = "DELETE FROM calendar2event WHERE entry_id = '$eid'";
		$this->db_query($q);
	}

	function fg_define_calendar($args = array())
	{
		extract($args);
		// generates a HTML form for defining a calendar.
		// it consists of the following elements:
		// 1 - el_event_start
		// 2 - el_event_end
		// 3 - el_event_count
		// 4 - el_event_period
		// 5 - el_event_release
		// all of those elements have the type select
		
		// additionally, I want to display one element of type 3 for each 
		// textbox/count element in the form
		$this->read_template("define_calendar.tpl");
		$count_els = new aw_array($els_count);
		$count_lines = "";
		$picks = array("0" => "--vali--");
		$textboxes = array("0" => "--vali--");
		foreach($all_els as $key => $val)
		{
			if ( ($val["type"] == "textbox") || ($val["type"] == "textarea") || ($val["type"] == "listbox") )
			{
				if ($val["subtype"] != "count")
				{
					$picks[$key] = $val["name"];
				};
			};

			if (($val["type"] == "textbox") && ($val["subtype"] != "count"))
			{
				$textboxes[$key] = $val["name"];
			};
		};

		foreach($count_els->get() as $key => $val)
		{
			if ($key)
			{
				$this->vars(array(
					"el" => $val,
					"el_id" => $key,
					"els" => $this->picker($arr["amount_el"][$key],$picks),
				));
				$count_lines .= $this->parse("count_lines");
			};
		};
		$period_type = ($arr["period_type"]) ? $arr["period_type"] : 1;
		$release_type = ($arr["release_type"]) ? $arr["release_type"] : 1;
		$units = array("0" => "minut","1" => "tund","2" => "p‰ev");
		$this->vars(array(
			"els_start" => $this->picker($arr["el_event_start"],$els_start),
			"els_end" => $this->picker($arr["el_event_end"],$els_end),
			#"els_count" => $this->picker($arr["el_event_count"],$els_count),
			"count_lines" => $count_lines,
			"els_period" => $this->picker($arr["el_event_period"],$els_period),
			"els_release" => $this->picker($arr["el_event_release"],$els_release),
			"textboxes" => $this->picker($arr["release_textbox"],$textboxes),
			"per_type_1_check" => checked($period_type == 1),
			"per_type_2_check" => checked($period_type == 2),
			"rel_type_1_check" => checked($release_type == 1),
			"rel_type_2_check" => checked($release_type == 2),
			"per_unit_type" => $this->picker($arr["per_unit_type"],$units),
			"release_unit_type" => $this->picker($arr["release_unit_type"],$units),
			"per_amount" => $arr["per_amount"],
			"reforb"        => $this->mk_reforb("submit_calendar", array("id" => $id),"form"),
		));
		$retval = $this->parse();
		return $retval;
	}

	function fg_update_cal_conf($args = array())
	{
		// I need to pass post_vars
		// and $this->arr
		$post_vars = &$args["post_vars"];
		$arr = &$args["arr"];

		// this is where we have to check the types
		$_start = date_edit::get_timestamp($post_vars[$arr["el_event_start"]]);
		// XXX: adding 86399 is just plain stupid
		$_end = date_edit::get_timestamp($post_vars[$arr["el_event_end"]]) + 86399;
		// now we need to cycle over all the $this->arr["el_amount"] elements and update
		// the bloody records
		
		$_types = array(
			"minute" => "0",
			"hour" => "1",
			"day" => "2",
		);
		
		$cal_id = (int)$args["cal_id"];
		$cal_relation = (int)$args["cal_relation"];

		$amount_els = new aw_array($arr["amount_el"]);
		
		$_oid = $args["id"];
		$_eid = $args["entry_id"];

		$f_e_id = $args["post_vars"]["form_entry_id"];

		$q = "DELETE FROM calendar2timedef WHERE oid = '$_oid' AND relation = '$cal_relation' AND entry_id = '$f_e_id'";
		$this->db_query($q);

		$period_type = ($arr["period_type"]) ? $arr["period_type"] : 1;
		$release_type = ($arr["release_type"]) ? $arr["release_type"] : 1;
	

		$lb_id_data = array();

		// it's fcking easy, since that stuff is shown as text, it does
		// _not_ come from the form and therefore this stuff will not work

		// soo .. if that stuff is _not_ in the post_vars, then I need
		// figure out it out from the existing entry
		foreach($amount_els->get() as $el_with_value => $count_el_id)
		{
			$lb_id_data[$count_el_id] = isset($post_vars[$count_el_id]) ? $post_vars[$count_el_id] : $args["els"][$count_el_id]["value"];
		}

		foreach($amount_els->get() as $el_with_value => $count_el_id)
		{
		
			// no such thing anymore, I need to cycle over all the count elements
			// $_cnt = $post_vars[$arr["el_event_count"]];
			// calculate the correct data for period based on the period setting
			if ($period_type == 1)
			{
				$_period = $post_vars[$arr["el_event_period"] . "_count"];
				$_pertype = $_types[$post_vars[$arr["el_event_period"] . "_type"]];
			}
			elseif ($period_type == 2)
			{
				$_period = (int)$arr["per_amount"];
				$_pertype = (int)$arr["per_unit_type"];
			}

			if ($release_type == 1)
			{
				$_release = $post_vars[$arr["el_event_release"] . "_count"];
				$_reltype = $_types[$post_vars[$arr["el_event_release"] . "_type"]];
			}
			elseif ($release_type == 2)
			{
				$_release = $post_vars[$arr["release_textbox"]];
				$_reltype = $arr["release_unit_type"];
			};

			//$_cnt = (int)$post_vars[$el_with_value];
			$_cnt = (int)$args["els"][$el_with_value]["value"];

			if ($_cnt > 0)
			{
				$txtid = $lb_id_data[$count_el_id];
				$q = "INSERT INTO calendar2timedef (oid,relation,cal_id,start,end,
					max_items,period,period_cnt, release,release_cnt,entry_id,txtid)
					VALUES ('$_oid','$cal_relation','$cal_id','$_start','$_end','$_cnt',
					'$_pertype','$_period','$_reltype', '$_release','$_eid','$txtid')";
				$this->db_query($q);
			};
		};
	}

	function edit_calendar_relation($args = array())
	{
		extract($args);
		$this->read_template("calendar_relation.tpl");

		$subrelations = array();
		$countrelations = array();

		// how the phuck do i load multiple relations then?
		if ($id)
		{
			$item = $this->get_record("calendar2forms","id",$id);
			$q = "SELECT * FROM calendar2form_relations WHERE calendar2forms_id = '$id'";
			$this->db_query($q);
			while($row = $this->db_next())
			{
				if ($row["el_count"])
				{
					$subrelations[$row["el_count"]] = $row;
				}
				else
				{
					$countrelations[] = $row;
				};
			};
		}
		else
		{
			$item = array();
		};
		
		$els_start = $els_listboxes = $els_relation = $els_end = $tables = $target_objects = array("0" => " -- Vali --");
		$els_count = array();

		$ol = new object_list(array(
			"class_id" => array(CL_FORM,CL_FORM_CHAIN),
			"status" => STAT_ACTIVE,
			"flags" => array(
				"mask" => OBJ_HAS_CALENDAR,
				"flags" => OBJ_HAS_CALENDAR
			)
		));
		$target_objects = $ol->names();

		foreach($els as $key => $val)
		{
			if ( ($val["type"] == "date") && ($val["subtype"] == "from") )
			{
				$els_start[$key] = $val["name"];
			};

			if ( ($val["type"] == "date") && ($val["subtype"] == "to") )
			{
				$els_end[$key] = $val["name"];
			};

			if ( ($val["type"] == "textbox") && ($val["subtype"] == "count") )
			{
				$els_count[$key] = $val["name"];
			};

			if ( ($val["type"] == "listbox") && ($val["subtype"] == "relation") )
			{
				$els_relation[$key] = $val["name"];
			};

			if ($val["type"] == "listbox")
			{
				$els_listboxes[$key] = "element: " . $val["name"];
			};
		};

		$ft = get_instance(CL_FORM_TABLE);
		$tables = $tables + $ft->get_form_tables_for_form($form_id);

		if ($item["end"] > 0)
                {
                        // Do we deal with days?
                        if ($item["end"] >= 86400)
                        {
                                $end_mp = 86400;
                        }
                        // hours perhaps?
                        elseif ($item["end"] >= 3600)
                        {
                                $end_mp = 3600;
                        }
                        // probably minutes then
                        else
                        {
                                $end_mp = 60;
                        };
                        $end = (int)($item["end"] / $end_mp);
                }
                else
                {
                        $end_mp = 60;
                        $end = 0;
                };

                $l = "";
                foreach($els_count as $key => $val)
                {
                        $this->vars(array(
                                "count_el_name" => $val,
                                "count_el_id" => $key,
                                "cnt_els" => $this->picker($subrelations[$key]["el_relation"],$els_listboxes),
                        ));
                        $l .= $this->parse("count_line");
                }

		$this->vars(array(
                        "target_objects" => $this->picker($item["cal_id"],$target_objects),
                        "objects_disabled" => disabled(sizeof($target_objects) == 1),
                        "start_els" => $this->picker($item["el_start"],$els_start),
                        "start_disabled" => disabled(sizeof($els_start) == 1),
                        //"cnt_els" => $this->picker($item["el_cnt"],$els_count),
                        //"count_disabled" => disabled(sizeof($els_count) == 1),
                        "end_els" => $this->picker($item["el_end"],$els_end),
                        "end_disabled" => disabled(sizeof($els_end) == 1),
                        "relation_els" => $this->picker($item["el_relation"],$els_relation),
                        "relation_disabled" => disabled(sizeof($els_relation) == 1),
                        "ev_tables" => $this->picker($item["ev_table"],$tables),
                        "tables_disabled" => disabled(sizeof($tables) == 1),
                        //"cnt_type_el" => checked($item["count"] == 0),
                        //"cnt_type_cnt" => checked($item["count"] > 0),
			"el_use_chain_entry_id" => checked($item["el_use_chain_entry_id"]),
			"el_allow_exceed" => checked($item["el_allow_exceed"]),
                        "end_type_el" => checked($item["end"] == 0),
                        "end_type_shift" => checked($item["end"] > 0),
                        "end_mp" => $this->picker($end_mp,array(60 => "minut(it)",3600 => "tund(i)",86400 => "p‰ev(a)")),
                        "end" => $end,
                        //"count" => (int)$item["count"],
                        "count_line" => $l,
                        "amount_number" => $countrelations[0]["count"],
                        "cnt_els2" => $this->picker($countrelations[0]["el_relation"],$els_listboxes),
                        "reforb" => $this->mk_reforb("submit_cal_rel",array("form_id" => $form_id,"id" => $id),"form"),
                ));
		return $this->parse();

	}

	function submit_calendar_relation($args = array())
	{
		extract($args);
		$count = ($cnt_type == 2) ? $count : 0;
                $end = ($end_type == 2) ? (int)$end * (int)$end_mp : 0;
                if ($id)
                {
			$upd_fields = array();
			$upd_fields["cal_id"] = "'$cal_id'";
			$upd_fields["el_start"] = "'$el_start'";
			$upd_fields["el_cnt"] = "'$el_cnt'";
			$upd_fields["el_cnt"] = "'$el_cnt'";
			$upd_fields["ev_table"] = "'$ev_table'";
			$upd_fields["el_relation"] = "'$el_relation'";
			$upd_fields["el_end"] = "'$el_end'";
			$upd_fields["count"] = "'$count'";
			$upd_fields["end"] = "'$end'";
			if ($el_use_chain_entry_id)
			{
				$upd_fields["el_use_chain_entry_id"] = $el_use_chain_entry_id;
			};
			if ($el_allow_exceed)
			{
				$upd_fields["el_allow_exceed"] = $el_allow_exceed;
			};
                        $q = sprintf("UPDATE calendar2forms SET %s WHERE id = '$id'",join(",",map2("%s=%s",$upd_fields)));
                }
                else
                {
                        $q = "INSERT INTO calendar2forms (cal_id,form_id,el_start,el_cnt,ev_table,el_relation,el_end, count, end)
                                VALUES ('$cal_id','$form_id','$el_start','$el_cnt','$ev_table','$el_relation','$el_end','$count','$end')";
                        $id = $this->db_last_insert_id();
                };
                $this->db_query($q);
                $amount_elements = new aw_array($amount_el);
                $q = "DELETE FROM calendar2form_relations WHERE calendar2forms_id = '$id'";
                $this->db_query($q);
                foreach($amount_elements->get() as $key => $val)
                {
                        $q = "INSERT INTO calendar2form_relations (calendar2forms_id,el_count,el_relation) VALUES ('$id','$key','$val')";
                        $this->db_query($q);
                }

		$amount_elements2 = new aw_array($amount_el2);
                foreach($amount_elements2->get() as $key => $val)
                {
                        $cnt = $amount_number[$key];
                        $q = "INSERT INTO calendar2form_relations (calendar2forms_id,count,el_relation) VALUES ('$id','$cnt','$val')";
                        $this->db_query($q);
                }
	}
};
?>
