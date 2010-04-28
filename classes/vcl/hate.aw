<?php

namespace automatweb;

class hate
{
	var $colors = array("red", "green", "blue", "magenta");

	function generate($who = "", $hate = "Hate")
	{
		$hate = "";
		for ($i = 0; $i < rand(5,200); $i++)
		{
			$hate.="<font size='".rand(-2,7)."' color='".$this->colors[rand(1,count($this->colors))]."'>Hate ";
			if (rand(1,50) < 10 && $who != "")
			{
				$hate.= $who;
			}
			$hate.= " </font> ";
		}
		return $hate;
	}
}
?>
