<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/crm/crm_conference_value_days.aw,v 1.10 2008/05/12 07:35:15 kristo Exp $
// crm_conference_value_days.aw - Konverentsi kalendrivaade 
/*

@classinfo syslog_type=ST_CRM_CONFERENCE_VALUE_DAYS relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@default table=objects
@default group=general
@default field=meta
@default method=serialize

#GENERAL
	@property hotel_code type=textbox
	@caption Hotelli kood
	
	@property months type=select
	@caption Mitu kuud
	
	@property show_codes type=checkbox ch_value=1
	@caption N&auml;ita koode
	
	@property template type=select
	@caption Kalendrivaate templeit
	
*/

class crm_conference_value_days extends class_base
{
	const AW_CLID = 1210;

	function crm_conference_value_days()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_conference_value_days",
			"clid" => CL_CRM_CONFERENCE_VALUE_DAYS
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "template":
				$tm = get_instance("templatemgr");
				$prop["options"] = $tm->template_picker(array(
					"folder" => "applications/crm/crm_conference_value_days"
				));
				unset($prop["options"][""]);
				if(!sizeof($prop["options"]))
				{
					$prop["caption"] .= t("\n".$this->site_template_dir."");
				}
				break;
			case "months":
				$prop["options"] = array("",1,2,3,4,5,6,7,8,9,10,11,12);
			//-- get_property --//
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

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////


	/**
		@attrib name=parse_alias is_public="1" caption="Change" nologin=1
	**/
	function parse_alias($arr)
	{
		enter_function("value_days::parse_alias");

//		$data = array("joga" => "jogajoga");
//		$this->vars($data);
		//property v�rtuse saatmine kujul "property_nimi"_value
		$html = "";
		$months = 3;
		
		if(is_oid($arr["alias"]["to"]))
		{
			$calendar_object = obj($arr["alias"]["to"]);
		}
		else
		{
			$calendar_object = obj($arr["id"]);
		}
		if($calendar_object->prop("months"))
		{
			$months = $calendar_object->prop("months");
		}
		
		$calendar_month = $_GET["calendar_month"];
		
		$tpl = $calendar_object->prop("template");
		$this->read_template($tpl);
		lc_site_load("conference_calendar", &$this);
		$comment = $GLOBALS["lc_conference_calendar"]["LC_COMMENT"];
	
		$n = 0;
		$n = $n + $calendar_month;
                include_once(aw_ini_get("site_basedir")."/code/soapclient.aw");
                $revalBookingServiceURL = "https://195.250.171.36/RevalORSService/RRCServices.asmx";
                $revalBookingServiceNamespace = "http://revalhotels.com/ORS/webservices/";
	        $urlData = parse_url($revalBookingServiceURL);
                $soapclient = new C_SoapClient($revalBookingServiceURL);
                $soapclient->namespace = $revalBookingServiceNamespace;
                $soapclient->debug = 0;
                $soapclient->ns_end = "/";
                $parameters = array();
			
			
		$q_start = mktime(0, 0, 0, date("n",(time() + $n*30*24*3600)), 1, date("Y",(time() + $n*30*24*3600)));
		$q_end = $q_start + 32*24*3600*$months;
			
		$parameters["Resort"] = $calendar_object->prop("hotel_code");
         	$parameters["FirstDate"] = date("Y",$q_start).'-'.date("m",$q_start).'-01T00:00:00.0000000+03:00';
		$parameters["LastDate"] =  date("Y",$q_end).'-'.date("m",$q_end).'-01T00:00:00.0000000+03:00';
         	$return = $soapclient->call("GetConferenceDayTypes" , $parameters);
         	$codes = array();
		$bg_colors = array();
		$colors = array("H" => "red" , "M" => "yellow" , "L" => "lime");
		$comments = array("H" => t("red") , "M" => t("yellow") , "L" => t("lime"));
		foreach($return["GetConferenceDayTypesResult"]["ConferenceDayTypeClass"] as $data)
		{
			$codes[$data["Resort"]] = $data["Resort"];
			if($data["Resort"] == $calendar_object->prop("hotel_code"))
			{
				$bg_colors[substr($data["DayTypeDate"], 0, 10)] = $data["ConferenceDayType"];
			}
		}

		while($n < $months+$calendar_month)
		{
			$month_start = mktime(0, 0, 0, date("n",(time() + $n*30*24*3600)), 1, date("Y",(time() + $n*30*24*3600)));
			$month_end = mktime(0, 0, 0, date("n",(time() + ($n)*30*24*3600))+1, 1, date("Y",(time() + $n*30*24*3600)));

			$day_of_the_week = date("w",($month_start));
			if($day_of_the_week == 0) $day_of_the_week = 7;
			
			// well, i changed these get_lc's from aw_locale:: class to static, because these different languages fucked the locale classload totally up. if anybody wants to fix it, be my guest.. 
			$html.='<table class="type4">
				<tr class="subheading">	
					<th colspan="7">'.aw_locale::get_lc_month(date("m",(time() + $n*30*24*3600)))." ".date("Y",(time() + $n*30*24*3600)).'</th>
				</tr>
				<tr>
					<th>'.strtoupper(substr(aw_locale::get_lc_weekday(1,true), 0, 1)).'</th>
					<th>'.strtoupper(substr(aw_locale::get_lc_weekday(2,true), 0, 1)).'</th>
					<th>'.strtoupper(substr(aw_locale::get_lc_weekday(3,true), 0, 1)).'</th>
					<th>'.strtoupper(substr(aw_locale::get_lc_weekday(4,true), 0, 1)).'</th>
					<th>'.strtoupper(substr(aw_locale::get_lc_weekday(5,true), 0, 1)).'</th>
					<th>'.strtoupper(substr(aw_locale::get_lc_weekday(6,true), 0, 1)).'</th>
					<th>'.strtoupper(substr(aw_locale::get_lc_weekday(7,true), 0, 1)).'</th>
				</tr>';
			$day_start = $month_start - 3600*24*($day_of_the_week - 1);
			$w = 0;
			while($w < 6)
			{
				$d = 0;
				$html.='<tr>';
				while($d < 7)
				{
					$html.='<td class="disabled" bgcolor="'.$colors[$bg_colors[date("Y-m-d" ,$day_start)]].'">';

					if($day_start >= $month_end  || $day_start < $month_start)
					{
						$html.='<font color="black">';
					}
					elseif($day_start < time())
					{
				//$html.='<a href="#" title="'.$comment[$bg_colors[date("Y-m-d" 
//,$day_start)]].'"><font color="grey">';
$html.='<font color="black">';
					}
					else
					{
						//$html.='<a href="#" title="'.$comment[$bg_colors[date("Y-m-d" 
//,$day_start)]].'"><font color="black">';
$html.='<font color="black">';
					}
					$html.=date("d",$day_start);
					$html.='</font">';
	//				$html.='</a></td>';
  $html.='</td>';
					$d++;
					$day_start = $day_start + 3600*24;
				}
				$html.='</tr>';
				$w++;
				if($w == 5 && date("d",$day_start) < 10) $w++;
			}
			$html.='</table>';
			$n++;
		}
		
		$next_url = aw_url_change_var("calendar_month" , $calendar_month + $months);
		$prev_url = aw_url_change_var("calendar_month" , $calendar_month - $months);
		
		$next_link = html::href(array("caption" => (t("Next")." ".$months." ".t("months")." >>") , "url" => $next_url));
		$prev_link = html::href(array("caption" => ("<< ".t("Previous")." ".$months." ".t("months")) , "url" => $prev_url));
		if($calendar_object->prop("show_codes"))
		{
			$html.= "<br>Hotellide koodid: <br>";
			$html.= join(", " , $codes);
		}
		
	//	return $html;
		
		$this->vars(array(
			"CALENDAR" => $html,
			"last_link" => $prev_link,
			"next_link" => $next_link,
			"last_url" => $prev_url,
			"next_url" => $next_url,
		));
		exit_function("value_days::parse_alias");
		return $this->parse();
	}

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
	function show($arr)
	{
		return $this->parse_alias($arr);
		
		
		
		
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $this->parse_alias($arr),
		));
		
		return $this->parse();
	}

	function request_execute ($this_object)
	{
		return $this->show (array (
			"id" => $this_object->id(),
		));
	}
//-- methods --//
}
?>
