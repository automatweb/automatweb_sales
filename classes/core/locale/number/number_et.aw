<?php

class awlc_number_et implements awlc_number
{
	public static function get_lc_number($number)
	{
		$number = (int) $number;
		$singles = array("","&uuml;ks","kaks","kolm","neli","viis","kuus","seitse","kaheksa","&uuml;heksa");
		$jargud1 = array(" miljon "," tuhat "," ");
		$jargud2 = array(" miljonit "," tuhat "," ");
		$res = "";
		if (0 === $number)
		{
			return "null";
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
					$res .= $singles[$pieces[0]] . "sada ";
					array_shift($pieces);
					$size--;
				};

				if ($size == 2 && $pieces[0] != 0)
				{
					if ($pieces[0] == 1)
					{
						if (end($pieces) == 0)
						{
							$res .= "k&uuml;mme";
						}
						else
						{
							$res .= $singles[end($pieces)] . "teist";
						};
					}
					else
					{
						$res .= $singles[reset($pieces)] . "k&uuml;mmend";
						$res .= " " . $singles[end($pieces)];
					};
				}
				else
				{
					$res .= $singles[end($pieces)];
				}

				$res .= end($pieces) == 1 ? $jargud1[$jrk] : $jargud2[$jrk];
			}
		}
		else
		{
			return "ENOCLUE";
		}
		return $res;
	}

	public static function get_lc_money_text($sum, $currency)
	{
		list($sum, $small_unit_sum) = explode(".", number_format($sum, 2, ".", ""));
		$res = str_replace($sum, self::get_lc_number($sum), $currency->get_string_for_sum($sum, languages::LC_EST));

		if ($small_unit_sum > 0)
		{
			$res .= " ja ". str_replace($small_unit_sum, self::get_lc_number($small_unit_sum), $currency->get_small_unit_string_for_sum($small_unit_sum, languages::LC_EST));
		}
		else
		{
			$res .= " ja ". str_replace("0", "00", $currency->get_small_unit_string_for_sum("0", languages::LC_EST));
		}

		return $res;
	}
}
