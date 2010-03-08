<?php
/*
@classinfo  maintainer=kristo
*/
class date
{
	function date()
	{
		$this->month = array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");

	}

	function get_lc_date($timestamp, $format)
	{
		if ($timestamp==0)
		{
			$timestamp=time();
		}
		
		switch ($format)
		{
			case 1:
				$newdate=date("d.m.y", $timestamp);
				return $newdate;
			case 2:
			
				$newdate=date("d.m.Y", $timestamp);
				return $newdate;
				
			case 3:
				$newdate=date("d ", $timestamp).$this->month[date("m", $timestamp)-1].date(" y",$timestamp);
				return $newdate;
				
			case 4:
				$newdate=date("d ", $timestamp).$this->month[date("m", $timestamp)-1].date(" Y",$timestamp);
				return $newdate;
			
			case 5:
				$rv = date("j. ",$timestamp).$this->month[date("m",$timestamp)-1];
				break;
			
			case 6:
				$rv = date("j. ",$timestamp).$this->month[date("m",$timestamp)-1] . date(" Y",$timestamp);
				break;
			
			case 7:
				$newdate=date("H:i d.m.y", $timestamp);
				return $newdate;
				
		}
	}
	
	function get_lc_weekday($num, $short = false, $ucfirst = false)
	{
		// date("w") returns 0 for sunday, but for historical reasons 7 should also be sunday
		$names = array("dimanche","lundi","mardi","mercredi","jeudi","vendredi","samedi","dimanche");
		$name = ($ucfirst) ? ucfirst($names[$num]) : $names[$num];
		return $short ? substr($name,0,3) . "." : $name;
	}

	function get_lc_month($num)
	{
		return $this->month[$num-1];
	}
	
	
}
?>
