<?php
// en.aw - english localization

class awlc_number_en implements awlc_number
{
	public static function get_lc_number($number)
	{
		$number = (int) $number;
		$singles = array("","one","two","three","four","five","six","seven","eight","nine");
		$jargud1 = array(" million "," thousand "," ");

		// check if there is a dot in the number and if so, separate the last part

		$special = array(
			"00" => "",
			"10" => "ten",
			"11" => "eleven",
			"12" => "twelve",
			"13" => "thirteen",
			"14" => "fourteen",
			"15" => "fifteen",
			"16" => "sixteen",
			"17" => "seventeen",
			"18" => "eighteen",
			"19" => "nineteen",
			"20" => "twenty",
			"30" => "thirty",
			"40" => "forty",
			"50" => "fifty",
			"60" => "sixty",
			"70" => "seventy",
			"80" => "eighty",
			"90" => "ninety"
		);

		$res = "";
		if (0 === $number)
		{
			return "zero";
		}
		elseif (preg_match("/([0-9]{0,3}?)([0-9]{0,3}?)([0-9]{1,3}?)$/",$number,$m))
		{
			foreach(array_splice($m,1) as $jrk => $token)
			{
				if ((int)$token === 0)
				{
					continue;
				}

				$pieces = explode(":", wordwrap((int)$token, 1, ":", 1));
				$size = count($pieces);

				// hundreds first and get rid of them too
				if ($size == 3)
				{
					$res .= $singles[reset($pieces)] . " hundred ";
					array_shift($pieces);
					$size--;
				}

				if ($size == 2)
				{
					$newtoken = $pieces[0] . $pieces[1];
					if (isset($special[$newtoken]))
					{
						$res .= $special[$newtoken];
					}
					else
					{
						$res .= $special[$pieces[0]."0"] . " ";
						$res .= $singles[end($pieces)];
					}
				}
				else
				{
					$res .= $singles[end($pieces)];
				}

				//$res .= end($pieces) == 1 ? $jargud1[$jrk] : $jargud2[$jrk];
				$res .= $jargud1[$jrk];
			}
		}
		else
		{
			return "ENOCLUE";
		}
		return $res;
	}

	function get_lc_sum($number)
	{
		$lastpart = substr($number,strpos($number,".")+1);
		$number = str_replace(",","",$number);
		$res = self::get_lc_number($number);

		$currency1 = aw_global_get("currency1");
		$currency2 = aw_global_get("currency2");

		if (!empty($lastpart))
		{
			$res .= " $currency1 and " . $lastpart . " " .$currency2;
		}

		return $res;
	}

	public static function get_lc_money_text($sum, $currency)
	{
		list($sum, $small_unit_sum) = explode(".", number_format($sum, 2, ".", ""));

		$res = str_replace($sum, self::get_lc_number($sum), $currency->get_string_for_sum($sum, languages::LC_ENG));

		if ($small_unit_sum > 0)
		{
			$res .= " and ". str_replace($small_unit_sum, self::get_lc_number($small_unit_sum), $currency->get_small_unit_string_for_sum($small_unit_sum, languages::LC_ENG));
		}
		else
		{
			$res .= " and ". str_replace("0", "00", $currency->get_small_unit_string_for_sum("0", languages::LC_ENG));
		}

		return $res;
	}
}
