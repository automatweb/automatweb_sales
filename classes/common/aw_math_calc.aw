<?php

/*
@classinfo  maintainer=voldemar
*/


/*  TEST CASES
echo aw_math_calc::string2float("$35,234.43")."\n";
echo "35234.43 - 1\n\n";

echo aw_math_calc::string2float("35,234.43")."\n";
echo "35234.43 - 2\n\n";

echo aw_math_calc::string2float("35,2,3,4.43")."\n";
echo "35234.43 - 2b\n\n";

echo aw_math_calc::string2float("35234.43")."\n";
echo "35234.43 - 3\n\n";

echo aw_math_calc::string2float("35234,43")."\n";
echo "35234.43 - 4\n\n";

echo aw_math_calc::string2float("35 234.43")."\n";
echo "35234.43 - 5\n\n";

echo aw_math_calc::string2float("35.234,43")."\n";
echo "35234.43 - 6\n\n";

echo aw_math_calc::string2float("35.23.4,43")."\n";
echo "35234.43 - 6b\n\n";

echo aw_math_calc::string2float("35,234,43")."\n";
echo "35.234 - 7\n\n";

echo aw_math_calc::string2float("35.234.43")."\n";
echo "35.234 - 8\n\n";

echo aw_math_calc::string2float("35.234, 43.4")."\n";
echo "35.234 - 9\n\n";

echo aw_math_calc::string2float("35asdf234,43asdf")."\n";
echo "35234.43 - 10\n\n";

echo aw_math_calc::string2float("35asdf234.43asdf")."\n";
echo "35234.43 - 11\n\n";

echo aw_math_calc::string2float("adf35234.43asdf")."\n";
echo "35234.43 - 12\n\n";

echo aw_math_calc::string2float("adf35234,43asdf")."\n";
echo "35234.43 - 13\n\n";

echo aw_math_calc::string2float("adf35234,43afcc.ad")."\n";
echo "35234.43 - 14\n\n";

echo aw_math_calc::string2float("a,df35234,43afcc.ad")."\n";
echo "35234.43 - 15\n\n";

echo aw_math_calc::string2float("a,d,f35234,43af,,,cc.ad")."\n";
echo "35234.43 - 16\n\n";

echo aw_math_calc::string2float("a,d,f35234.43af,,,cc.ad")."\n";
echo "35234.43 - 17\n\n";

echo aw_math_calc::string2float("ac,df35234.43afcc.a,d")."\n";
echo "35234.43 - 18\n\n";

echo aw_math_calc::string2float("35234")."\n";
echo "35234 - 19\n\n";

echo aw_math_calc::string2float(".45")."\n";
echo "0.45 - 20\n\n";

echo aw_math_calc::string2float(",45")."\n";
echo "0.45 - 21\n\n";

echo aw_math_calc::string2float("'',45")."\n";
echo "0.45 - 22\n\n";

echo aw_math_calc::string2float(".45'")."\n";
echo "0.45 - 23\n\n";

echo aw_math_calc::string2float("45,'")."\n";
echo "45 - 1\n\n";

echo aw_math_calc::string2float("45.'")."\n";
echo "45 - 1\n\n";
 */


class aw_math_calc
{
	const DECIMAL_SEPARATORS = ".,";

	/** Converts any numeric string float representantion to real float value as best as it can
	@attrib api=1 params=pos
	@param value required type=string
		Numeric string with a decimal separator either full stop or comma
	@comment
	@returns float
	**/
	public static function string2float($value)
	{
		$a = str_split($value);

		// throw away irrelevant chars and count separators
		$comma_count = 0;
		$dot_count = 0;
		$last_sep_pos_c = 0;
		$last_sep_pos_d = 0;
		$chr_cnt = 0;
		foreach ($a as $key => $chr)
		{
			if (!is_numeric($chr) and "," !== $chr and "." !== $chr and !("-" === $chr and 0 === $chr_cnt))
			{
				unset($a[$key]);
			}
			else
			{
				if ("," === $chr)
				{
					++$comma_count;
					$last_sep_pos_c = $key;
				}
				elseif ("." === $chr)
				{
					++$dot_count;
					$last_sep_pos_d = $key;
				}
				$chr_cnt++;
			}
		}

		// determine decimal separator and convert/filter chars
		if (0 === $dot_count or $comma_count and $comma_count < $dot_count)
		{ // separator is comma
			foreach ($a as $key => $chr)
			{
				if ("." === $chr)
				{
					unset($a[$key]);
				}
				elseif ("," === $chr)
				{
					$a[$key] = ".";
				}
			}
		}
		elseif (0 === $comma_count or $dot_count and $comma_count > $dot_count)
		{ // separator is dot
			foreach ($a as $key => $chr)
			{
				if ("," === $chr)
				{
					unset($a[$key]);
				}
			}
		}
		elseif ($comma_count === $dot_count)
		{ // the one nearer to end is consiered to be the separator
			if ($last_sep_pos_c > $last_sep_pos_d)
			{
				// separator is comma
				foreach ($a as $key => $chr)
				{
					if ("." === $chr)
					{
						unset($a[$key]);
					}
					elseif ("," === $chr)
					{
						$a[$key] = ".";
					}
				}
			}
			else
			{
				// separator is dot
				foreach ($a as $key => $chr)
				{
					if ("," === $chr)
					{
						unset($a[$key]);
					}
				}
			}
		}

		$value = (float) implode("", $a);
		return $value;
	}
}

?>
