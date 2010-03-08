<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/scm/scm_result_type.aw,v 1.7 2007/12/06 14:34:06 kristo Exp $
// scm_result_type.aw - Paremusj&auml;rjestuse t&uuml;&uuml;p 
/*

@classinfo syslog_type=ST_SCM_RESULT_TYPE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=tarvo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property unit type=select
@caption &Uuml;hik

@property unit_type type=chooser multiple=1
@caption &Uuml;hikud

@property sort type=select
@caption Sorteerimine

*/

class scm_result_type extends class_base
{
	function scm_result_type()
	{
		$this->init(array(
			"tpldir" => "applications/scm/scm_result_type",
			"clid" => CL_SCM_RESULT_TYPE
		));
		$this->_set_data();
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- get_property --//
			case "unit":
				foreach($this->units as $name => $data)
				{
					$prop["options"][$name] = $data["caption"];
				}
				break;

			case "unit_type":
				if(!$arr["obj_inst"]->prop("unit"))
				{
					return PROP_IGNORE;
				}
				if(!$prop["value"])
				{
					if($arr["obj_inst"]->prop("unit") == "length")
					{
						$default = array(
							"m" => "m",
							"cm" => "cm",
						);
					}
					elseif($arr["obj_inst"]->prop("unit") == "time")
					{
						$default = array(
							"h" => "h",
							"m" => "m",
							"s" => "s",
						);
					}
					elseif($arr["obj_inst"]->prop("unit") == "points")
					{
						$default = array(
							"p" => "p",
						);
					}
					$arr["obj_inst"]->set_prop("unit_type", $default);
					$prop["value"] = $default;
				}
				$prop["options"] = $this->units[$arr["obj_inst"]->prop("unit")]["format"];
				break;
			case "sort":
				$prop["options"] = array(
					"asc" => t("Kasvav"),
					"desc" => t("Kahanev"),
				);
			break;
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- set_property --//
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}
	
	function get_result_types($arg = array())
	{
		if(strlen($arg["organizer"]))
		{
			$filt["parent"] = $arg["organizer"];
		}
		$filt["class_id"] = CL_SCM_RESULT_TYPE;
		$list = new object_list($filt);
		return $list->arr();
	}

	function add_result_type($arg = array())
	{
		$obj = obj();
		$obj->set_parent($arg["organizer"]);
		$obj->set_class_id(CL_SCM_RESULT_TYPE);
		$obj->set_name($arg["name"]);
		$obj->set_prop("unit", $arg["unit"]);
		$obj->set_prop("sort", $arg["sort"]);
		$oid = $obj->save_new();
		return $oid;
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

//-- methods --//

	/**
		@param data
		@param result_type
		@comment
			$data seee peab olema formaat:
			array(
				id,
				result,
			)
		@return
			array(
				$id,
				place,
			)
	**/
	function sort_results($arr)
	{
		$res_type = obj($arr["result_type"]);
		$sort = $res_type->prop("sort");
		($sort == "asc")?asort($arr["data"], SORT_NUMERIC):arsort($arr["data"], SORT_NUMERIC);
		$place = 1;
		foreach($arr["data"] as $id => $res)
		{
			$arr["places"][$id] = (int)$place;
			$place++;
		}
		return $arr["places"];
	}

	/**
		@param result_type required type=int
			result type object id
		@comment
			fetches the unit display format for current result type
	**/
	function get_format($arr = array())
	{
		$o = obj($arr["result_type"]);
		return $o->prop("unit_type");
	}

	/**
		@param source required type=string
			result, competition... etc
		@param oid required type=int
			oid of the $source object
		@param data required type=int
			formattable data
		@param reverse optional type=bool
		@comment
	**/
	function format_data($arr)
	{
		$reverse = ($arr["reverse"] != true && $arr["reverse"] != false)?"false":$arr["reverse"];
		switch($arr["source"])
		{
			case "result":
				$inst = get_instance(CL_SCM_RESULT);
				$comp = $inst->get_competition(array("result" => $arr["oid"]));
				$inst = get_instance(CL_SCM_COMPETITION);
				$event = $inst->get_event(array("competition" => $comp));
				$inst = get_instance(CL_SCM_EVENT);
				$result_type = $inst->get_result_type(array("event" => $event));
				$obj = obj($result_type);
				$unit = $obj->prop("unit");
				$fun = $this->units[$unit]["fun"];
			break;
			case "competition":
				$inst = get_instance(CL_SCM_COMPETITION);
				$event = $inst->get_event(array("competition" => $arr["oid"]));
				$inst = get_instance(CL_SCM_EVENT);
				$result_type = $inst->get_result_type(array("event" => $event));
				$obj = obj($result_type);
				$unit = $obj->prop("unit");
				$fun = $this->units[$unit]["fun"];
			break;
		}
		return $this->$fun($arr["data"], $reverse);
	}

	/**
		@param res_type 
	**/
	function get_utypes($arr)
	{
		$o = obj($arr);
		return $o->prop("unit_type");
	}

	function _set_data()
	{
		$this->units = array(
			"time" => array(
				"caption" => t("Aeg"),
				"format" => array(
					//"d" => t("p"),
					"h" => t("t"),
					"m" => t("m"),
					"s" => t("s"),
					"ms" => t("ms"),
				),
				"fun" => "_format_time",
			),
			"points" => array(
				"caption" => t("Punktid"),
				"format" => array(
					"p" => t("punkti"),
				),
				"fun" => "_format_points",
			),
			"length" => array(
				"caption" => t("Pikkus/Kaugus"),
				"format" => array(
					"km" => t("km"),
					"m" => t("m"),
					"cm" => t("cm"),
					"mm" => t("mm"),
				),
				"fun" => "_format_length",
			),
		);
	}
	
	function _format_time($arr, $reverse = false)
	{
		if($reverse)
		{
			$arr["ms"] = ($arr["ms"] < 0 || !strlen($arr["ms"]))?0:$arr["ms"];
			$arr["s"] = ($arr["ms"] < 0 || !strlen($arr["s"]))?0:$arr["s"];
			$arr["m"] = ($arr["m"] < 0 || !strlen($arr["m"]))?0:$arr["m"];
			$arr["t"] = ($arr["t"] < 0 || !strlen($arr["t"]))?0:$arr["t"];
			$arr["p"] = ($arr["p"] < 0 || !strlen($arr["p"]))?0:$arr["p"];

			if($arr["ms"] > 9)
			{
				$tmp = floor($arr["ms"] / 10);
				$arr["s"] += $tmp;
				$arr["ms"] = $arr["ms"] - ($tmp * 10);
			}
			if($arr["s"] > 59)
			{
				$tmp = floor($arr["s"] / 60);
				$arr["m"] += $tmp;
				$arr["s"] = $arr["s"] - ($tmp * 60);
			}
			if($arr["m"] > 59)
			{
				$tmp = floor($arr["m"] / 60);
				$arr["h"] += $tmp;
				$arr["m"] = $arr["m"] - ($tmp * 60);
			}
			if($arr["h"] > 23)
			{
				$tmp = floor($arr["h"] / 24);
				$arr["d"] += $tmp;
				$arr["h"] = $arr["h"] - ($tmp * 24);
			}
			$sum = ($arr["d"] * 24 * 60 * 60 * 10) + ($arr["h"] * 60 * 60 * 10) + ($arr["m"] * 60 * 10) + ($arr["s"] * 10) + $arr["ms"];
			return $sum;

		}
		else
		{
			$s = $arr / 10;
			$m = $s / 60;
			$h = $m / 60;
			$d = $h / 24;

			$ret["ms"] = $arr[strlen($arr) - 1];
			$ret["s"] = floor($s) - (floor($m) * 60);
			$ret["m"] = floor($m) - (floor($h) * 60);
			$ret["h"] = floor($h) - (floor($d) * 24);
			$ret["d"] = floor($d);

			return $ret;	
		}
	}

	function _format_points($arr, $reverse = false)
	{
		if($reverse)
		{
			return $arr["p"];
		}
		else
		{
			$ret["p"] = $arr;
			return $ret;
		}
	}

	function _format_length($arr, $reverse = false)
	{
		if($reverse)
		{
			$arr["mm"] = ($arr["mm"] < 0 || !strlen($arr["mm"]))?0:$arr["mm"];
			$arr["cm"] = ($arr["cm"] < 0 || !strlen($arr["cm"]))?"00":$arr["cm"];
			$arr["m"] = ($arr["m"] < 0 || !strlen($arr["m"]))?"000":$arr["m"];
			$arr["km"] = ($arr["km"] < 0)?0:$arr["km"];
			if($arr["mm"] > 9)
			{
				$tmp = floor($arr["mm"] / 10);
				$arr["cm"] += $tmp;
				$arr["mm"] = $arr["mm"] - ($tmp * 10);
			}
			if($arr["cm"] > 99)
			{
				$tmp = floor($arr["cm"] / 100);
				$arr["m"] += $tmp;
				$arr["cm"] = $arr["cm"] - ($tmp * 100);
			}
			if($arr["m"] > 999)
			{
				$tmp = floor($arr["m"] / 1000);
				$arr["km"] += $tmp;
				$arr["m"] = $arr["m"] - ($tmp * 1000);
			}

			$arr["cm"] = str_pad($arr["cm"], 2, "0", STR_PAD_LEFT);
			$arr["m"] = str_pad($arr["m"], 3, "0", STR_PAD_LEFT);
			$ret = $arr["km"].$arr["m"].$arr["cm"].$arr["mm"];
		}
		else
		{
			$l = strlen($arr);
			$ret["mm"] = trim($arr[$l-1], "0");
			$ret["cm"] = trim($arr[$l-3].$arr[$l-2], "0");
			$ret["m"] = trim($arr[$l-6].$arr[$l-5].$arr[$l-4], "0");
			$ret["km"] = (strlen($arr) > 6)?substr($arr, 0, strlen($arr) - 6):"";
		}
		return $ret;
	}
}
?>
