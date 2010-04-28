<?php

namespace automatweb;

class awlc_number_es implements awlc_number
{
	public static function get_lc_number($number)
	{
		$number = (int)$number;
		$singles = array("","uno","dos","tre","cuatro","cinco","seis","siete","ocho","nueve");
		$jargud1 = array(" millón "," mil "," ");
		$jargud2 = array(" millones "," mil "," ");

		$special = array(
			"10" => "diez",
			"11" => "once",
			"12" => "doce",
			"13" => "trece",
			"14" => "catorce",
			"15" => "quince",
			"16" => "dieciséis",
			"17" => "diecisiete",
			"18" => "dieciocho",
			"19" => "diecinueve",
			"20" => "veinte",
			"30" => "treinta",
			"40" => "cuarenta",
			"50" => "cincuenta",
			"60" => "sesenta",
			"70" => "setenta",
			"80" => "ochenta",
			"90" => "noventa",
		);

		$special_tens = array(
			"2" => "veinti",
			"3" => "treinta y ",
			"4" => "cuarenta y ",
			"5" => "cincuenta y ",
			"6" => "sesenta y ",
			"7" => "setenta y ",
			"8" => "ochenta y ",
			"9" => "noventa y ",
		);

		$hundreds = array(
			"1" => "ciento ",
			"2" => "doscientos ",
			"3" => "trescientos ",
			"4" => "cuatrocientos ",
			"5" => "quinientos ",
			"6" => "seiscientos ",
			"7" => "setecientos ",
			"8" => "ochocientos ",
			"9" => "novecientos ",
		);

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
					if ($token == 100)
					{
						$res .= "cien";
					}
					else
					{
						$res .= $hundreds[reset($pieces)];
					};
					array_shift($pieces);
					$size--;
				};

				if ($size == 2)
				{
					if ($pieces[0] != 0)
					{
						$newtoken = $pieces[0] . $pieces[1];
						if (isset($special[$newtoken]))
						{
							$res .= $special[$newtoken];
						}
						else
						{
							$res .= $special_tens[reset($pieces)];
							$res .= $singles[end($pieces)];
						};
					}
					else
					{
						$res .= $singles[end($pieces)];
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
		return $number;
	}
}
?>
