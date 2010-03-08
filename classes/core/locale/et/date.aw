<?php
/*
@classinfo  maintainer=kristo
*/
class date
{
	function date()
	{
		$this->month = array("jaanuar", "veebruar", "m&auml;rts", "aprill", "mai", "juuni", "juuli", "august", "september", "oktoober", "november", "detsember");
	}

	function get_lc_date($timestamp, $format)
	{
		if (empty($timestamp))
		{
			$timestamp = time();
		}

		$rv = "";

		if (PHP_OS == "WINNT" && $timestamp < 0)
		{
			return "n/a";
		}
		
		switch ($format)
		{
			case 1:
				$rv = date("j.m.y", $timestamp);
				break;

			case 2:
				$rv = date("j.m.Y", $timestamp);
				break;
				
			case 3:
				$rv = date("j. ", $timestamp).$this->month[date("m", $timestamp)-1].date(" y",$timestamp);
				break;
				
			case 4:
				$rv = date("j. ", $timestamp).$this->month[date("m", $timestamp)-1].date(" Y",$timestamp);
				break;

			case 5:
				$rv = date("j. ",$timestamp).$this->month[date("m",$timestamp)-1];
				break;
			
			case 6:
				$rv = date("j. ",$timestamp).$this->month[date("m",$timestamp)-1] . date(" Y",$timestamp);
				break;
			case 7:
				$rv = date("H:i j.m.Y", $timestamp);
				break;
				


		}

		return $rv;
	}

	function get_lc_weekday($num, $short = false, $ucfirst = false)
	{
		// date("w") returns 0 for sunday, but for historical reasons should also work with 7
		$names = array("p&uuml;hap&auml;ev","esmasp&auml;ev","teisip&auml;ev","kolmap&auml;ev","neljap&auml;ev","reede","laup&auml;ev","p&uuml;hap&auml;ev");
		$name = ($ucfirst) ? ucfirst($names[$num]) : $names[$num];
		return $short ? substr($name,0,1) : $name;
	}
	function estoo()
	{

	}
	
	function get_lc_month($num)
	{
		return $this->month[$num-1];
	}
	
	
}
?>
