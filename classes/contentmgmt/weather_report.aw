<?php

namespace automatweb;

// weather_report.aw - Ilmateade
/*

@classinfo syslog_type=ST_WEATHER_REPORT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=robert

@default table=objects
@default group=general

@property report_type type=select field=meta method=serialize
@caption Allikas

@property feed_url type=textbox field=meta method=serialize
@caption Feedi URL

@property cache_time type=textbox field=meta method=serialize
@caption Cache uuendusaeg (h)

@property pic_url type=textbox field=meta method=serialize
@caption Piltide url

@property pic_list type=textarea cols=60 rows=4 field=meta method=serialize
@caption Pildid (nt Light rain=rain.gif)
*/

class weather_report extends class_base
{
	const AW_CLID = 1364;

	function weather_report()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/weather_report",
			"clid" => CL_WEATHER_REPORT
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "report_type":
				$prop["options"] = array(
					1 => "Weather underground"
				);
				break;

			case "feed_url":
			case "cache_time":
			case "pic_list":
				$retval = PROP_IGNORE;
				if($arr["request"]["action"] != "new" || $arr["obj_inst"]->prop("report_type")== 1)
				{
					$retval = PROP_OK;
				}
				break;
		}
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

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function fetch_data($ob)
	{
		$fname = $ob->prop("feed_url");
		$data = @file_get_contents($fname);
		if($data)
		{
			$x = xml_parser_create();
			xml_parse_into_struct($x, $data, $vals, $index);
			xml_parser_free($x);
			$i = $index["DESCRIPTION"][1];
			$data = $vals[$i-7]["value"]."|".$vals[$i]["value"];
			return $data;
		}
		else
		{
			return false;
		}
	}
	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
	function show($arr)
	{
		$ob = new object($arr["id"]);
		if($ob->prop("report_type")==1)
		{
			$this->read_template("show.tpl");
			$c = get_instance("cache");
			$time = ((int)$ob->prop("cache_time"))*60*60;
			if (!($ct = $c->file_get_ts("weather_".$ob->id(), time() - $time)))
			{
				$ct = $this->fetch_data($ob);
				if($ct)
				$c->file_set("weather_".$ob->id(), $ct);
			}
			if($ct)
			{
				$props = array();
				$data = explode('|',$ct);
				foreach($data as $i=>$fielddata)
				{
					if($i==0)
					{
						$this->vars(array(
							"title" => $fielddata
						));
						continue;
					}
					$field = explode(':',$fielddata);
					$prop = str_replace(" ","",trim($field[0]));
					unset($field[0]);
					$val = trim(implode(':', $field));
					if($prop == "Conditions")
					{
						$pic_url = $ob->prop("pic_url");
						if(empty($pic_url))
						{
							$pic_url = "http://icons-pe.wxug.com/graphics/conds/";
						}
						$src = strtolower(str_replace(" ","",$val));
						$imgsrc = $pic_url.$src.".GIF";
						if(!@fopen($imgsrc, "r"))
						{
							$piclist = $ob->prop("pic_list");
							$piclist = explode(chr(13).chr(10),$piclist);
							$pics = array();
							foreach($piclist as $pici)
							{
								$pic = explode("=", $pici);
								$pics[str_replace(" ", "", trim(strtolower($pic[0])))] = $pic[1];
							}
							$imgsrc = $pic_url . $pics[str_replace(" ", "", trim(strtolower($val)))];
							if(!@fopen($imgsrc,"r"))
							$imgsrc = $pic_url . "unknown.gif";
						}
						$this->vars(array(
							"imgsrc" => $imgsrc
						));
					}
					elseif($prop == "Temperature")
					{
						preg_match_all("/^(\d+).+?(\d+).*?/", $val, $vals, PREG_PATTERN_ORDER);
						$fields['fahr'] = $vals['1']['0'];
						$fields['cels'] = $vals['2']['0'];
					}

					$fields[$prop] = $val;
				}
				foreach($fields as $prop=>$val)
				{
					$this->vars(array(
						$prop => $val
					));
				}
			}
		}

		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

//-- methods --//
}
?>
