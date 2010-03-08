<?php
// $Header: /home/cvs/automatweb_dev/classes/core/locale/lv/number.aw,v 1.3 2008/01/31 13:53:27 kristo Exp $
// et.aw - Estonian localization
/*
@classinfo  maintainer=kristo
*/
class number
{
	function get_lc_number($number)
	{
		$number = (int)$number;
		$singles = array("","üks","kaks","kolm","neli","viis","kuus","seitse","kaheksa","üheksa");
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
							$res .= "kümme";
						}
						else
						{
							$res .= $singles[end($pieces)] . "teist";
						};
					}
					else
					{
						$res .= $singles[reset($pieces)] . "kümmend";
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
};
?>
