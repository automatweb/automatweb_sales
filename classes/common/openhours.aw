<?php
/*
@classinfo syslog_type=ST_OPENHOURS relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@default table=objects
@default group=general

@property openhours type=text form=+emb field=meta method=serialize
@caption Avamisajad
@comment Rea kustutamiseks vali molemale paevale "Valimata"

@property apply_group type=relpicker field=meta method=serialize reltype=RELTYPE_GROUP multiple=1 store=connect 
@caption Kehtib gruppidele

@property date_from type=date_select field=meta method=serialize default=-1
@caption Kehtib alates

@property date_to type=date_select field=meta method=serialize default=-1
@caption Kehtib kuni

@reltype GROUP value=1 clid=CL_GROUP
@caption Kehtib kasutajagrupile

*/

class openhours extends class_base
{
	function openhours()
	{
		$this->init(array(
			"tpldir" => "common",
			"clid" => CL_OPENHOURS
		));
	}

	function get_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "apply_group":
				$ol = new object_list(array(
					"class_id" => CL_GROUP,
					"lang_id" => array(),
					"site_id" => array(),
					"type" => group_obj::TYPE_REGULAR
				));
				$data["options"] = array("" => t("--vali--")) + $ol->names();
				break;

			case 'openhours':
				$prefix = $data['name'];
				if (isset($arr['name_prefix']))
				{
					$prefix = $arr['name_prefix'].'['.$prefix.']';
				}
				$value = $data['value'];
				$data['value']	= "";
				
				if (is_array($value))
				{
					for ($i = 0; array_key_exists($i, $value); $i++)
					{
						$out .= $this->_make_openhours_row($value[$i], $prefix.'['.$i.']');
						$out .= "<br />";
					}
				}
				
				$out .= "<br />".t("Lisa").":&nbsp;&nbsp;&nbsp;".$this->_make_openhours_row(null, $prefix.'['.($i+1).']');
				$data['value'] = $out;
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
			case 'openhours':
				$lastvalue = $arr['obj_inst']->meta('openhours');
				$store = array ();
				$j = 0; // last stored
				$vars = ifset($arr['request'],'openhours');
				foreach ($vars as $i => $values)
				#for ($i = 0; array_key_exists('row'.$i.'time1', $arr['request']); $i++)
				{
					$t = $this->_verified_openhours($values);
					if (is_array($t))
					{
						$store[$j++] = $t;
					}
					else if (!is_null($t))
					{
						$store[$j++] = $lastvalue[$i]; // Keep last value on error
						$data["error"] .= t("Rida")." $j: ".$t;
						$retval = PROP_ERROR;
					}

				}
				if ($retval == PROP_OK)
				{
					// Sort array. We dont want to sort upon error, for that would make error message have an error
					// This is gonna be nice
					$cmp = create_function('$a,$b', 'return $a["day1"] == $b["day1"] ? 
											$a["day2"] == $b["day2"] ?
												$a["h1"] == $b["h1"] ?
													$a["m1"] == $b["m1"] ?
														$a["h2"] == $b["h2"] ? 
															$a["m2"] > $b["m2"] :
														$a["h2"] > $b["h2"] :
													$a["m1"] > $b["m1"] :
												$a["h1"] > $b["h1"] :
											$a["day2"] > $b["day2"] :
										$a["day1"] > $b["day1"];');

					// well.. toldya!
					// indeed you did.  - terryf
					usort($store, $cmp);
				}
				$arr['obj_inst']->set_meta('openhours', $store);
				$data['value'] = "";//$store;
			break;
		}
		return $retval;
	}	

	////
	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	////
	// !this shows the object. not strictly necessary, but you'll probably need it, it is used by parse_alias
	// 'id' - oid
	// 'style' - default | short
	function show($arr)
	{
		$this->vars(array('ONEDAY' => '', 'TWODAYS' => '', 'TIMES' => '', 'TIMES_24H' => '', 'ALLBUTFIRST' => '', 'ALLBUTFIRST_SHORT' => '', 'LINE' => ''));
		$this->sub_merge = 1;
		$days_short = array (
			0 => " ",
			1 => t("E"), 
			2 => t("T"), 
			3 => t("K"), 
			4 => t("N"), 
			5 => t("R"), 
			6 => t("L"), 
			7 => t("P"), 
		);
		$ob = new object($arr["id"]);
		$this->read_template("openhours.tpl");

		$style = ifset($arr, 'style') == 'short' ? 'short' : 'default';
		
		$values = $ob->meta('openhours');
		$first = true;
		foreach ($values as $i => $val)
		{
			if (!$first) 
			{
				if ($style == 'short')
				{
					$this->parse('ALLBUTFIRST_SHORT');
				}
				else
				{
					$this->parse('ALLBUTFIRST');
				}
			}
			$first = false;
		
			$show['h1'] = $val['h1'];//sprintf("%02d", $val['h1']);
			$show['h2'] = $val['h2'];//sprintf("%02d", $val['h2']);
			$show['m1'] = sprintf("%02d", $val['m1']);
			$show['m2'] = sprintf("%02d", $val['m2']);
			$show['day1'] = $days_short[$val['day1']];
			$show['day2'] = $days_short[$val['day2']];
			
			$this->vars($show);
			if ($val['day2'] == 0 || $val['day1'] == $val['day2'])
			{
				$this->parse('ONEDAY');
			}
			else
			{
				$this->parse('TWODAYS');
			}
			if ($val['h1'] == $val['h2'] && $val['m1'] == $val['m2']) // If times are equal, it's open all day
			{
				$this->parse('TIMES_24H');
			}
			else
			{
				$this->parse('TIMES');
			}
			$this->parse('LINE');
			$this->vars(array('ONEDAY' => '', 'TWODAYS' => '', 'TIMES' => '', 'TIMES_24H' => '', 'ALLBUTFIRST' => '', 'ALLBUTFIRST_SHORT' => ''));
		}
		return $this->parse();
	}

//-- methods --//
	/*
		Format opening time output form row.
		$arr is array with structure:
					array (
						"0"	=> array(
							'day1'	=> int,
							'day2'	=> int,
							'h1'	=> int,
							'm1'	=> int,
							'h2'	=> int,
							'm2'	=> int,
						),
						"1" => array(...),
						...	
					)		
					
		$name is prefixed
		Returns string
	*/
	private function _make_openhours_row($arr, $name)
	{
		$ret = "";
		$minute_step = 15;
		$wdays = array(
			0	=> t("-- Valimata --"),
			1	=> t("Esmasp&auml;ev"),
			2	=> t("Teisip&auml;ev"),
			3	=> t("Kolmap&auml;ev"),
			4	=> t("Neljap&auml;ev"),
			5	=> t("Reede"),
			6	=> t("Laup&auml;ev"),
			7	=> t("P&uuml;hap&auml;ev"),
		);
		 
		$day1 = isset($arr['day1']) && is_numeric($arr['day1']) && between($arr['day1'], 0, 7) ? $arr['day1'] : 0;
		$day2 = isset($arr['day2']) && is_numeric($arr['day2']) && between($arr['day2'], 0, 7) ? $arr['day2'] : 0;
		$h1 = isset($arr['h1']) && is_numeric($arr['h1']) && between($arr['h1'], 0, 23) ? $arr['h1'] : 0;
		$h2 = isset($arr['h2']) && is_numeric($arr['h2']) && between($arr['h2'], 0, 23) ? $arr['h2'] : 0;
		$m1 = isset($arr['m1']) && is_numeric($arr['m1']) && between($arr['m1'], 0, 59) ? $arr['m1'] : 0;
		$m2 = isset($arr['m2']) && is_numeric($arr['m2']) && between($arr['m2'], 0, 59) ? $arr['m2'] : 0;
		
		$ret .= html::select(array(
			'name'	=> $name.'[day1]',
			'selected'	=> $day1,
			'options'	=> $wdays,
		));
		$ret .= " - " . html::select(array(
			'name'	=> $name.'[day2]',
			'selected'	=> $day2,
			'options'	=> $wdays,
		));
		$ret .= "&nbsp;&nbsp;&nbsp;" . html::time_select(array(
			'name'	=> $name.'[time1]',
			'minute_step'	=> $minute_step,
			'value'	=> array ('hour' => $h1, 'minute' => $m1)
		));
		$ret .= " - " . html::time_select(array(
			'name'	=> $name.'[time2]',
			'minute_step'	=> $minute_step,
			'value'	=> array ('hour' => $h2, 'minute' => $m2)
		));
		
		return $ret;
	}
	
	// Formats submitted data to storable structure
	// returns error message if times not valid
	private function _verified_openhours($arr)
	{
		$day1 = isset($arr[$name.'day1']) && is_numeric($arr[$name.'day1']) && between($arr[$name.'day1'], 0, 7) ? $arr[$name.'day1'] : null;
		$day2 = isset($arr[$name.'day2']) && is_numeric($arr[$name.'day2']) && between($arr[$name.'day2'], 0, 7) ? $arr[$name.'day2'] : null;
		$h1 = $h2 = $m1 = $m2 = null;
		if(isset($arr[$name.'time1']))
		{
			$arr2 = $arr[$name.'time1'];
			$h1 = isset($arr2['hour']) && is_numeric($arr2['hour']) && between($arr2['hour'], 0, 23) ? $arr2['hour'] : null;
			$m1 = isset($arr2['minute']) && is_numeric($arr2['minute']) && between($arr2['minute'], 0, 59) ? $arr2['minute'] : null;
		}
		if(isset($arr[$name.'time2']))
		{
			$arr2 = $arr[$name.'time2'];
			$h2 = isset($arr2['hour']) && is_numeric($arr2['hour']) && between($arr2['hour'], 0, 59) ? $arr2['hour'] : null;
			$m2 = isset($arr2['minute']) && is_numeric($arr2['minute']) && between($arr2['minute'], 0, 59) ? $arr2['minute'] : null;
		}
		
		if (is_null($day1))
		{
			return t("Vigane perioodi alguse p&auml;ev");
		}
		if (is_null($day2))
		{
			return t("Vigane perioodi l&otilde;pu p&auml;ev");
		}
		if (is_null($h1) || is_null($m1))
		{
			return t("Vigane perioodi alguse aeg");
		}
		if (is_null($h2) || is_null($m2))
		{
			return t("Vigane perioodi l&otilde;pu aeg");
		}
		if (!$day1 && !$day2)
		{
			return null; // row will be deleted
		}
		
		$t1 = $h1*60 + $m1;
		$t2 = $h2*60 + $m2;

		if ($day1 == 0 && $day2 > 0)
		{
			list ($day1, $day2) = array($day2, $day1);
		}
		
		//miks see hea on?
		if ($day1 == $day2)
		{
			$day2 = 0;
		}
		
		// phew
		
		return array(
			'day1'	=> $day1,
			'day2'	=> $day2,
			'h1'	=> $h1,
			'm1'	=> $m1,
			'h2'	=> $h2,
			'm2'	=> $m2,
		);
		
	}

	/** returns array(start_time, end_time) for the given date 
		@attrib api=1
		@param oh required type=object
			The openhours object to check

		@param tm required type=int
			Date to check
	**/
	function get_times_for_date($oh, $tm)
	{
		$wd = date("w", $tm);
		if($wd == 0)
		{
			$wd = 7;
		}
		$m = $oh->meta("openhours");
		if($wd == 0)
		{
			$wd = 7;
		}
		foreach($m as $row)
		{
			if($row["h2"] == 0 && $row["m2"] == 0)
			{
				$row["h2"] = 24;
			}
			if (($row["day1"] <= $wd && $row["day2"] >= $wd) || ($row["day1"] == $wd && !$row["day2"]))
			{//arr($row); arr($wd);
				return array($row["h1"]*3600+$row["m1"]*60, $row["h2"]*3600+$row["m2"]*60);
			}
		}
		return array(0, 0);
	}
	
	/** returns array(hour, minute) when opens
		@attrib api=1
		@param o required type=object
			The openhours object
	**/
	function get_opening_time($o)
	{
		$meta = $o->meta("openhours");
		$h = 24;
		$m = 60;
		foreach($meta as $row)
		{
			if(100*$row["h1"]+$row["m1"] < 100*$h+$m)
			{
				$h = $row["h1"];
				$m = $row["m1"];
			}
		}
		return array("hour" => $h, "minute" => $m);
	}

	/** returns int, timestamp for the given date midday 
		@attrib api=1
		@param o required type=object
			The openhours object to check

		@param tm required type=int
			Date to check
	**/
	function get_midday($o,$tm)
	{
		$wd = date("w", $tm);
		if($wd == 0)
		{
			$wd = 7;
		}
		$m = $o->meta("openhours");
		foreach($m as $row)
		{
			if (($row["day1"] <= $wd && $row["day2"] >= $wd) || ($row["day1"] == $wd && !$row["day2"]))
			{
				return mktime(($row["h1"] + $row["h2"])/2,($row["m1"] + $row["m2"])/2 ,0,date("n" , $tm),date("j" , $tm),date("Y" , $tm));
			}
		}
		return mktime(date("h" , $tm),(int)date("i" , $tm) ,0,date("n" , $tm),date("j" , $tm),date("Y" , $tm));
	}
	
	/** Returns array of rooms that use the given openhours object
		@attrib api=1 params=pos

		@param o required type=cl_openhours
			the openhours object to check 

		@returns
			array { room_id, ... } for all rooms that use the given openhours object
	**/
	function get_rooms_for_oh($o)
	{
		$rv = array();
		$gl = aw_global_get("gidlist_oid");
		foreach($o->connections_to(array("type" => 44)) as $c)
		{
			$rv[] = $c->from();

		}
		return count($rv) ? $rv : null;
	}
	
}
	
?>
