<?php

class awlc_number_et implements awlc_number
{
	public static function get_lc_number($number)
	{
		$number = (int)$number;
		$singles = array("","&uuml;ks","kaks","kolm","neli","viis","kuus","seitse","kaheksa","&uuml;heksa");
		$jargud1 = array(" miljon "," tuhat "," ");
		$jargud2 = array(" miljonit "," tuhat "," ");
		$res = "";
		if (preg_match("/([0-9]{0,3}?)([0-9]{0,3}?)([0-9]{1,3}?)$/",$number,$m))
		{
			foreach(array_splice($m,1) as $jrk => $token)
			{
				if ((int)$token === 0)
				{
					continue;
				};

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
				};

				$res .= end($pieces) == 1 ? $jargud1[$jrk] : $jargud2[$jrk];
			};
		}
		else
		{
			return "ENOCLUE";
		}
		return $res;
	}

	public static function get_lc_money_text($number, $currency)
	{
		list($eek, $cent) = explode(".", number_format($number, 2, ".", ""));
		$res = $currency->get_string_for_sum(self::get_lc_number($eek), languages::LC_EST);
		if ($cent > 0)
		{
			$res .= " ja ". $currency->get_small_unit_string_for_sum(self::get_lc_number($cent), languages::LC_EST);
		}
		else
		{
			$res .= " ja 00 ". $currency->get_small_unit_string_for_sum("0", languages::LC_EST);
		}
		return $res;
	}
}
