<?php
// klassile antakse ette "unix timestamp", ta konverdib
// selle ajayhikuteks, ning tagastab nende muutmiseks
// sobivad vormielemendid
/*
@classinfo  maintainer=kristo
*/
class date_edit
{
	// vormielementide nimed saavad olema kujul
	// $varname[month] $varname[day] jne.

	// kui aega ette ei anta, siis kuvame selleks kuup2eva
	// 88p2ev hiljem dokumendi avamisest. See on ysna suvaline muidugi

	function date_edit($varname = "", $timestamp = "+24h")
	{
		$this->init($varname, $timestamp);
		// default to all shown
		$this->configure(array(
			"year" => "",
			"month" => "",
			"day" => "",
			"hour" => "",
			"minute" => "",
		));
	}

	// well, you can set these but they aren't used <-- taiu
	function set($field, $value)
	{
		$this->$field = $value;
	}

	function init($varname, $timestamp)
	{
		$this->varname = $varname;
		if ($timestamp == "+24h")
		{
			$timestamp = time() + (60 * 60 * 24);
		};
		$this->timestamp = $timestamp;
		$this->step = 5;
		$this->classid = "";
	}

	////
	// !Sets the layout of the date editor
	// default is to show a select element
	// set_layout(array("year" => "textbox")) makes it a textbox instead

	// sets the layout flag but this isn't used in anywhere.. therefore isn't  api function <-- taiu
	function set_layout($args = array())
	{
		$this->layout = $args;
	}

	/**
		@attrib params=name api=1
		@param classid optional type=int
		@comment
	**/
	function configure($fields)
	{
		// millised v2ljad ja millises j2rjekorras kuvame
		// ja mida me nende captioniteks n2itame
		//    month = Kuu
		//
		if (!is_array($fields))
		{
			return false;
		}

		if (isset($fields["classid"]))
		{
			$this->classid = $fields["classid"];
			unset($fields["classid"]);
		}

		$this->fields = $fields;
	}

	/**
		@attrib params=pos api=1

		@param varname
			Sets the varname for the date form that is posted.
			varname[year],varname[month],varname[day],varname[hour],varname[minute]

		@param timestamp optional type=int
			Sets the time to be selected(unix timestamp). Default is current time +24h

		@param range1 optional type=int
			Sets the start year(default is 2003)

		@param range2 optional type=int
			Sets the end year(default is 2010)

		@param add_empty optional type=bool
			If set tu true, adds an '---' item  and selects it(adds for everythind.. selects only for year)
			Default is false. If this is set range1 and range2 must be manually set.

		@param buttons type=bool
		@param no_buttons_in_admin type=bool

		@param month_as_numbers type=bool default=false
			if set to true, monthnames are replaced with numbers

		@comment
		Generates the date edit html code accorndig to options

		@returns
		The form elements html code to be printed on page
	**/
	function gen_edit_form($varname, $timestamp, $range1 = 2003, $range2 = 2010, $add_empty = false, $buttons = false, $no_buttons_in_admin = false, $month_as_numbers = false)
	{
		if (is_array ($varname))
		{
			$textsize = isset ($varname["textsize"]) ? ' style="font-size: ' . (string) $varname["textsize"] . ';"' : "";
			$disabled = isset ($varname["disabled"]) ? ' disabled="disabled"' : "";
			$varname = $varname["name"];
		}
		else
		{
			$disabled = "";
			$textsize = "";
		};

		if ($timestamp == "+24h")
		{
			$timestamp = time() + (60 * 60 * 24);
		}

		if ($timestamp == "+48h")
		{
			$timestamp = time() + (2 * 60 * 60 * 24);
		}

		$this->varname = $varname;
		$this->timestamp = $timestamp;
		$clid = "";
		if ($this->classid != "")
		{
			$clid=" class=\"{$this->classid}\"";
		};
		// support for ISO-8601 date format
		list($year,$month,$day) = sscanf($this->timestamp,"%4d-%2d-%2d");
		if ($this->timestamp == -1 or !is_numeric($this->timestamp))
		{
			$year = $month = $day = $hour = $minute = -1;
		}
		elseif ($year && $month && $day)
		{

		}
		else
		{
			list($year,$month,$day,$hour,$minute) = explode(" ", date("Y n j H i", $this->timestamp));
		}

		$retval = "";
		foreach( $this->fields as $k => $v)
		{
			switch($k)
			{
				case "year":
					$retval .= "<select name=\"{$this->varname}[year]\"{$disabled}{$textsize}{$clid}>\n";
					if ($add_empty)
					{
						$retval.= "<option value='---'>---</option>\n";
					}
// sorting principle:
// range1: 1950 range2: 2000	1950, 1951, 1952 ... 2000
// range1: 2008 range2: 2020	2008, 2009, 2010 ... 2020

// range1: 2000 range2: 1950    1950, 1951, 1952 ... 2000
// range1: 2020 range2: 2008    2008, 2009, 2010 ... 2020
					$min_range = min($range1, $range2);
					$max_range = max($range1, $range2);
					// if future
					if ($max_range >= date("Y"))
					{
						$range2 = $min_range;
						$range1 = $max_range;
					}
					else
					{
						$range1 = $min_range;
						$range2 = $max_range;
					}

					if ($range1 > $range2)
					{
						for ($i = $range1; $i >= $range2; $i--)
						{
							$retval .= sprintf("<option value='%s' %s>%s</option>\n",$i,selected($i == $year),$i);
						};
					}
					else
					{
						for ($i = $range1; $i < $range2; $i++)
						{
							$retval .= sprintf("<option value='%s' %s>%s</option>\n",$i,selected($i == $year),$i);
						};
					}
					$retval .= "</select>\n";
					break;

				case "year_textbox":
					if ($year == -1)
					{
						$year = "";
					}
					$retval .= "<input type='text' name='{$this->varname}[year]' size='4' maxlength='4' value='{$year}'{$disabled}{$textsize}{$clid} \>\n";
					break;

				case "month":
					$retval .= "<select name='{$this->varname}[month]'{$disabled}{$textsize}{$clid}>\n";
					if ($add_empty)
					{
						$retval.= "<option value='---'>---</option>\n";
					}
					$mnames = array(
						"1" => t("Jaanuar"),
						"2" => t("Veebruar"),
						"3" => t("M&auml;rts"),
						"4" => t("Aprill"),
						"5" => t("Mai"),
						"6" => t("Juuni"),
						"7" => t("Juuli"),
						"8" => t("August"),
						"9" => t("September"),
						"10" => t("Oktoober"),
						"11" => t("November"),
						"12" => t("Detsember")
					);
					// wtf is this mon_for thingie?
					if(isset($this->mon_for))
					{
						$mnames = array();
						$tmp = range(1, 12);
						foreach($tmp as $val)
						{
							$mnames[$val < 10 ? "0".$val : $val] = $val < 10 ? "0".$val : $val;
						}
					}
					foreach($mnames as $mk => $mv)
					{
						$retval .= sprintf("<option value='%s' %s>%s</option>\n",$mk,selected($mk == $month && $this->timestamp != -1),$month_as_numbers?$mk:$mv);
					};
					$retval .= "</select>\n";
					break;

				case "month_textbox":
					if ($month == -1)
					{
						$month = "";
					}
					$retval .= "<input type='text' name='{$this->varname}[month]' size='2' maxlength='2' value='$month'{$disabled}{$textsize}{$clid} \>\n";
					break;

				case "day":
					$retval .= "<select name='{$this->varname}[day]'{$disabled}{$textsize}{$clid}>\n";
					if ($add_empty)
					{
						$retval.= "<option value='---'>---</option>\n";
					}
					for ($i = 1; $i <= 31; $i++)
					{
						$retval .= sprintf("<option value='%s' %s>%s</option>\n",$i,selected($i == $day && $this->timestamp != -1),$i);
					};
					$retval .= "</select>\n";
					break;

				case "day_textbox":
					if ($day == -1)
					{
						$day = "";
					}
					$retval .= "<input type='text' name='{$this->varname}[day]' size='2' maxlength='2' value='$day'{$disabled}{$textsize}{$clid} \>\n";
					break;

				case "hour":
					// if hour is followed by minute, then start a nowrap span, so that minute and hour entry boxes wrap together
					$tmp = array_keys($this->fields);
					$hour_idx = array_search("hour", $tmp);
					$prev_hr_span = false;
					if ($tmp[$hour_idx+1] == "minute")
					{
						$retval .= "<span style='white-space: nowrap'>";
						$prev_hr_span = true;
					}
					$retval .= "<select name='{$this->varname}[hour]'{$disabled}{$textsize}{$clid}>\n";
					if ($add_empty)
					{
						$retval.= "<option value='---'>---</option>\n";
					}
					for ($i = 0; $i <= 23; $i++)
					{
						$retval .= sprintf("<option value='%s' %s>%02d</option>\n",$i,selected($i == $hour && $this->timestamp != -1),$i);
					};
					$retval .= "</select> :\n";
					break;

				case "hour_textbox":
					$retval .= "<input type='text' name='{$this->varname}[hour]' size='2' maxlength='2' value='$hour' $disabled $textsize \>\n";
					break;

				case "minute":
					$retval .= "<select name='{$this->varname}[minute]'{$disabled}{$textsize}{$clid}>\n";
					if ($add_empty)
					{
						$retval.= "<option value='---'>---</option>\n";
					}
					$step = (isset($this->minute_step) && $this->minute_step > 0) ? $this->minute_step : 1;
					for ($i = 0; $i <= 59; $i = $i + $step)
					{
						$retval .= sprintf("<option value='%s' %s>%02d</option>\n",$i,selected($i <= $minute && $i +  $step > $minute && $this->timestamp != -1),$i);
					};
					$retval .= "</select>";
					if ($prev_hr_span)
					{
						$retval .= "</span>";
					}
					$retval .= "\n";
					break;

				case "minute_textbox":
					$retval .= "<input type='text' name='{$this->varname}[minute]' size='2' maxlength='2' value='$minute'{$disabled}{$textsize}{$clid} \>\n";
					break;
			}; // end switch
		}; // end while

		if ((false === $no_buttons_in_admin and is_admin()) || $buttons === true)
		{
			// make those date button images configurable
			$date_choose_img_url = aw_ini_get('date_edit.date_choose_img_url');
			if (empty($date_choose_img_url))
			{
				$date_choose_img_url = '/automatweb/images/icons/class_126.gif';
			}
			$date_clear_img_url = aw_ini_get('date_edit.date_clear_img_url');
			if (empty($date_clear_img_url))
			{
				$date_clear_img_url = '/automatweb/images/icons/delete.gif';
			}

			$retval .=
				"<span style='white-space: nowrap;'>
					<a href='javascript:void(0)' onclick='aw_date_edit_show_cal(\"{$this->varname}\");' id='{$this->varname}' name='{$this->varname}'>";
			$retval .= "<img id='{$this->varname}_ico"."' src='".aw_ini_get('baseurl').$date_choose_img_url."' border='0' alt=''/>
					</a> ";
			$retval .= "<a href='javascript:void(0)' onclick='aw_date_edit_clear(\"{$this->varname}\");'>
					<img src='".aw_ini_get('baseurl').$date_clear_img_url."' border=0  alt=''/>
					</a>
				</span>";
		}

		return $retval;
	} // end gen_edit_form

	/**
		@attrib params=name api=1
		@param year required type=int
			sets the year
		@param month required type=int
		sets the month
		@param day required type=int
		sets the day
		@param hour required type=int
		sets the hour
		@param minute required type=int
		sets the minute
		@param second required type=int
		sets the second

		@param prop_def optional type=array
			The property definition for the property to convert - useful if some fields should be missing

		@comment
		Generates unix timestamp according to given values
		@returns
		Returns Unix timestamp
	**/
	public static function get_timestamp($var, $prop_def = null)
	{
		// Initialize
		$var = array_merge(array(
			"hour" => 0,
			"minute" => 0,
			"second" => 0,
			"month" => 0,
			"day" => 0,
			"year" => 0,
		), safe_array($var));
		// if the prop def gives format, then eoms fields might be omitted, fill them in with defaults
		if ($prop_def !== null && is_array($prop_def["format"]))
		{
			if (!$var["day"] && !in_array("day", $prop_def["format"]))
			{
				$var["day"] = 1;
			}
			if (!$var["month"] && !in_array("month", $prop_def["format"]))
			{
				$var["month"] = 1;
			}
			if (!$var["year"] && !in_array("year", $prop_def["format"]))
			{
				$var["year"] = date("Y");
			}
		}

		if ($var['month'] == '---' || $var['day'] == '---' || $var['year'] == '---')
		{
			return -1;
		}
		if ($var['month'] == 0 || $var['day'] == 0 || $var['year'] == 0)
		{
			return -1;
		}
		if (!is_array($var))
		{
			return -1;
		}
		$tmp =  mktime($var["hour"], $var["minute"], $var["second"], $var["month"], $var["day"], $var["year"]);
		return $tmp;
	}

	/**
		@attrib params=name api=1
		@param year required type=int
		sets the year
		@param month required type=int
		sets the month
		@param day required type=int
		sets the day
		@comment
		Generates unix timestamp according to given values
		@returns
		Returns Unix timestamp
	**/
	function get_day_end_timestamp($var)
	{
		if ($var['month'] == '---' || $var['day'] == '---' || $var['year'] == '---')
		{
			return -1;
		}
		if ($var['month'] == 0 || $var['day'] == 0 || $var['year'] == 0)
		{
			return -1;
		}
		if (!is_array($var))
		{
			return -1;
		}
		$tmp =  mktime(23, 59, 59, $var["month"], $var["day"], $var["year"]);
		return $tmp;
	}
}; // end class
