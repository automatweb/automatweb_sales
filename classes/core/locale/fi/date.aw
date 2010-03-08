<?php
/*
@classinfo  maintainer=kristo
*/
class date
{
	function date()
	{
		$this->month = array("tammikuu", "helmikuu", "maaliskuu", "huhtikuu", "toukokuu", "kesäkuu", "heinäkuu", "elokuu", "syyskuu", "lokakuu", "marraskuu", "joulukuu");
	}

	function get_lc_date($timestamp, $format)
	{
		if ($timestamp == 0)
		{
			$timestamp = time();
		}

		$mname = $this->month[date("m", $timestamp)-1] . "ta";
		
		switch ($format)
		{
			case 1:
				$rv = date("d.m.y", $timestamp);
				break;
				
			case 2:
				$rv = date("d.m.Y", $timestamp);
				break;
				
			case 3:
				$rv = date("d. ", $timestamp).$mname.date(" y", $timestamp);
				break;
				
			case 4:
				$rv = date("d. ", $timestamp).$mname.date(" Y", $timestamp);
				break;
				
			case 5:
				$rv = date("d. ", $timestamp).$mname;
				break;
				
			case 7:
				$rv = date("H:i j.m.Y", $timestamp);
				break;
		}
		return $rv;
	}

	function get_lc_weekday($num, $short = false, $ucfirst = false)
	{
		$names = array("sunnuntai", "maanantai", "tiistai", "keskiviikko", "torstai", "perjantai", "lauantai", "sunnuntai");
		$name = ($ucfirst) ? ucfirst($names[$num]) : $names[$num];
		return $short ? substr($name, 0, 2) : $name;
	}
	
	function get_lc_month($num)
	{
		return $this->month[$num-1];
	}
}
?>
